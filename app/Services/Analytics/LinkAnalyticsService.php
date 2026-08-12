<?php

namespace App\Services\Analytics;

use App\Models\Language;
use App\Models\Link;
use App\Models\ShetabitVisit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class LinkAnalyticsService
{
    public const DEFAULT_RANGE = '30d';

    public const MAX_CUSTOM_DAYS = 365;

    public const BREAKDOWN_LIMIT = 15;

    /**
     * Resolve a validated date range from the request query string.
     *
     * @return array{key: string, from: Carbon, to: Carbon, label: string}
     */
    public function resolveDateRange(Request $request): array
    {
        $tz = config('app.timezone') ?: 'UTC';
        $range = $request->query('range', self::DEFAULT_RANGE);

        if (! is_string($range) || $range === '') {
            $range = self::DEFAULT_RANGE;
        }

        $now = Carbon::now($tz);

        return match ($range) {
            'today' => [
                'key' => 'today',
                'from' => $now->copy()->startOfDay(),
                'to' => $now->copy()->endOfDay(),
                'label' => 'Bugün',
            ],
            '7d' => [
                'key' => '7d',
                'from' => $now->copy()->subDays(6)->startOfDay(),
                'to' => $now->copy()->endOfDay(),
                'label' => 'Son 7 gün',
            ],
            '30d' => [
                'key' => '30d',
                'from' => $now->copy()->subDays(29)->startOfDay(),
                'to' => $now->copy()->endOfDay(),
                'label' => 'Son 30 gün',
            ],
            '90d' => [
                'key' => '90d',
                'from' => $now->copy()->subDays(89)->startOfDay(),
                'to' => $now->copy()->endOfDay(),
                'label' => 'Son 90 gün',
            ],
            'custom' => $this->resolveCustomRange($request, $tz),
            default => throw ValidationException::withMessages([
                'range' => 'Geçersiz tarih aralığı. today, 7d, 30d, 90d veya custom kullanın.',
            ]),
        };
    }

    /**
     * Find a link the authenticated user may view analytics for.
     */
    public function findAuthorizedLink(User $user, int $linkId): Link
    {
        $query = Link::query()->where('id', $linkId);

        if (! $user->hasRole('SUPER-ADMIN')) {
            $query->where('user_id', $user->id);
        }

        return $query->firstOrFail();
    }

    /**
     * Build aggregated analytics payload for a single link (no raw visit rows).
     *
     * @param  array{key: string, from: Carbon, to: Carbon, label: string}  $range
     * @return array<string, mixed>
     */
    public function buildForLink(Link $link, array $range): array
    {
        $from = $range['from'];
        $to = $range['to'];

        $base = ShetabitVisit::query()
            ->where('link_id', $link->id)
            ->whereBetween('created_at', [$from, $to]);

        $selectedPeriodTotal = (clone $base)->count();
        $totalViews = ShetabitVisit::query()->where('link_id', $link->id)->count();

        $tz = config('app.timezone') ?: 'UTC';
        $todayStart = Carbon::now($tz)->startOfDay();
        $todayEnd = Carbon::now($tz)->endOfDay();
        $today = ShetabitVisit::query()
            ->where('link_id', $link->id)
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->count();

        $previous = $this->previousPeriodBounds($from, $to);
        $previousTotal = ShetabitVisit::query()
            ->where('link_id', $link->id)
            ->whereBetween('created_at', [$previous['from'], $previous['to']])
            ->count();

        $periodChangePercent = null;
        if ($previousTotal > 0) {
            $periodChangePercent = round((($selectedPeriodTotal - $previousTotal) / $previousTotal) * 100, 1);
        } elseif ($selectedPeriodTotal > 0) {
            $periodChangePercent = 100.0;
        } else {
            $periodChangePercent = 0.0;
        }

        return [
            'link' => [
                'id' => $link->id,
                'name' => $link->link_name ?: $link->url_name,
                'url_name' => $link->url_name,
                'link_type' => $link->link_type,
                'type_label' => $link->link_type === 'shortlink' ? 'Kısa Link' : 'Bio Link',
            ],
            'range' => [
                'key' => $range['key'],
                'label' => $range['label'],
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ],
            'overview' => [
                'total_views' => $totalViews,
                'today' => $today,
                'selected_period_total' => $selectedPeriodTotal,
                'previous_period_total' => $previousTotal,
                'period_change_percent' => $periodChangePercent,
            ],
            'timeseries' => $this->timeseries($link->id, $from, $to),
            'countries' => $this->aggregateCountries($link->id, $from, $to, $selectedPeriodTotal),
            'devices' => $this->groupColumn($link->id, $from, $to, 'device', $selectedPeriodTotal),
            'browsers' => $this->groupColumn($link->id, $from, $to, 'browser', $selectedPeriodTotal),
            'operating_systems' => $this->groupColumn($link->id, $from, $to, 'platform', $selectedPeriodTotal),
            'languages' => $this->aggregateLanguages($link->id, $from, $to, $selectedPeriodTotal),
            'referrers' => $this->aggregateReferrers($link->id, $from, $to, $selectedPeriodTotal),
        ];
    }

    /**
     * Dashboard-scoped visit query for a user (ownership by link_id).
     *
     * @return array{0: Collection<int, int>|null, 1: \Illuminate\Database\Eloquent\Builder}
     */
    public function dashboardVisitsQuery(User $user): array
    {
        if ($user->hasRole('SUPER-ADMIN')) {
            return [null, ShetabitVisit::query()];
        }

        $linkIds = Link::query()
            ->where('user_id', $user->id)
            ->pluck('id');

        $query = ShetabitVisit::query()->whereIn('link_id', $linkIds);

        return [$linkIds, $query];
    }

    /**
     * Monthly counts for the current year (index 0 = January).
     *
     * @param  Collection<int, int>|null  $linkIds  null = all links (super-admin)
     * @return list<int>
     */
    public function monthlyCounts(?Collection $linkIds): array
    {
        $tz = config('app.timezone') ?: 'UTC';
        $year = Carbon::now($tz)->year;

        $query = ShetabitVisit::query()
            ->whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as month_num, COUNT(*) as total')
            ->groupBy('month_num');

        if ($linkIds !== null) {
            if ($linkIds->isEmpty()) {
                return array_fill(0, 12, 0);
            }
            $query->whereIn('link_id', $linkIds);
        }

        $rows = $query->pluck('total', 'month_num');

        $counts = [];
        for ($month = 1; $month <= 12; $month++) {
            $counts[] = (int) ($rows[$month] ?? 0);
        }

        return $counts;
    }

    /**
     * Last 7 calendar days counts ending today.
     *
     * @param  Collection<int, int>|null  $linkIds
     * @return list<int>
     */
    public function lastSevenDaysCounts(?Collection $linkIds): array
    {
        $tz = config('app.timezone') ?: 'UTC';
        $from = Carbon::now($tz)->subDays(6)->startOfDay();
        $to = Carbon::now($tz)->endOfDay();

        $query = ShetabitVisit::query()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as day_date, COUNT(*) as total')
            ->groupBy('day_date')
            ->orderBy('day_date');

        if ($linkIds !== null) {
            if ($linkIds->isEmpty()) {
                return array_fill(0, 7, 0);
            }
            $query->whereIn('link_id', $linkIds);
        }

        $rows = $query->pluck('total', 'day_date');

        $counts = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::now($tz)->subDays($i)->toDateString();
            $counts[] = (int) ($rows[$day] ?? 0);
        }

        return $counts;
    }

    /**
     * @return array{key: string, from: Carbon, to: Carbon, label: string}
     */
    private function resolveCustomRange(Request $request, string $tz): array
    {
        $fromRaw = $request->query('from');
        $toRaw = $request->query('to');

        if (! is_string($fromRaw) || ! is_string($toRaw) || $fromRaw === '' || $toRaw === '') {
            throw ValidationException::withMessages([
                'from' => 'Özel aralık için from ve to zorunludur (YYYY-MM-DD).',
            ]);
        }

        try {
            $from = Carbon::createFromFormat('Y-m-d', $fromRaw, $tz)->startOfDay();
            $to = Carbon::createFromFormat('Y-m-d', $toRaw, $tz)->endOfDay();
        } catch (\Throwable) {
            throw ValidationException::withMessages([
                'from' => 'Geçersiz tarih formatı. YYYY-MM-DD kullanın.',
            ]);
        }

        if ($from->gt($to)) {
            throw ValidationException::withMessages([
                'from' => 'Başlangıç tarihi bitiş tarihinden sonra olamaz.',
            ]);
        }

        if ($from->diffInDays($to) > self::MAX_CUSTOM_DAYS) {
            throw ValidationException::withMessages([
                'from' => 'Özel aralık en fazla '.self::MAX_CUSTOM_DAYS.' gün olabilir.',
            ]);
        }

        return [
            'key' => 'custom',
            'from' => $from,
            'to' => $to,
            'label' => $from->toDateString().' – '.$to->toDateString(),
        ];
    }

    /**
     * @return array{from: Carbon, to: Carbon}
     */
    private function previousPeriodBounds(Carbon $from, Carbon $to): array
    {
        $seconds = max(1, $to->getTimestamp() - $from->getTimestamp());
        $previousTo = $from->copy()->subSecond();
        $previousFrom = $previousTo->copy()->subSeconds($seconds);

        return [
            'from' => $previousFrom,
            'to' => $previousTo,
        ];
    }

    /**
     * @return list<array{date: string, count: int}>
     */
    private function timeseries(int $linkId, Carbon $from, Carbon $to): array
    {
        $rows = ShetabitVisit::query()
            ->where('link_id', $linkId)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as day_date, COUNT(*) as total')
            ->groupBy('day_date')
            ->orderBy('day_date')
            ->pluck('total', 'day_date');

        $series = [];
        $cursor = $from->copy()->startOfDay();
        $end = $to->copy()->startOfDay();

        while ($cursor->lte($end)) {
            $key = $cursor->toDateString();
            $series[] = [
                'date' => $key,
                'count' => (int) ($rows[$key] ?? 0),
            ];
            $cursor->addDay();
        }

        return $series;
    }

    /**
     * @return list<array{label: string, count: int, percent: float}>
     */
    private function groupColumn(int $linkId, Carbon $from, Carbon $to, string $column, int $periodTotal): array
    {
        $allowed = ['device', 'browser', 'platform'];
        if (! in_array($column, $allowed, true)) {
            return [];
        }

        $rows = ShetabitVisit::query()
            ->where('link_id', $linkId)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw("COALESCE(NULLIF(TRIM(`{$column}`), ''), ?) as label, COUNT(*) as total", ['Bilinmiyor'])
            ->groupBy('label')
            ->orderByDesc('total')
            ->limit(self::BREAKDOWN_LIMIT)
            ->get();

        return $rows->map(function ($row) use ($periodTotal) {
            $count = (int) $row->total;

            return [
                'label' => (string) $row->label,
                'count' => $count,
                'percent' => $periodTotal > 0 ? round(($count * 100) / $periodTotal, 1) : 0.0,
            ];
        })->all();
    }

    /**
     * Country from legacy `ip` column (plain IP or geo JSON). Only `ip` is loaded.
     *
     * @return list<array{label: string, code: string|null, count: int, percent: float}>
     */
    private function aggregateCountries(int $linkId, Carbon $from, Carbon $to, int $periodTotal): array
    {
        $ips = ShetabitVisit::query()
            ->where('link_id', $linkId)
            ->whereBetween('created_at', [$from, $to])
            ->pluck('ip');

        $counts = [];
        $codes = [];

        foreach ($ips as $raw) {
            [$label, $code] = $this->parseCountry($raw);
            $counts[$label] = ($counts[$label] ?? 0) + 1;
            if ($code && ! isset($codes[$label])) {
                $codes[$label] = $code;
            }
        }

        arsort($counts);

        $result = [];
        $i = 0;
        foreach ($counts as $label => $count) {
            if ($i >= self::BREAKDOWN_LIMIT) {
                break;
            }
            $result[] = [
                'label' => $label,
                'code' => $codes[$label] ?? null,
                'count' => $count,
                'percent' => $periodTotal > 0 ? round(($count * 100) / $periodTotal, 1) : 0.0,
            ];
            $i++;
        }

        return $result;
    }

    /**
     * @return array{0: string, 1: string|null}
     */
    private function parseCountry(mixed $raw): array
    {
        if ($raw === null || $raw === '') {
            return ['Bilinmiyor', null];
        }

        if (! is_string($raw)) {
            return ['Bilinmiyor', null];
        }

        $trimmed = trim($raw);

        if ($trimmed === '' || $trimmed === 'null' || $trimmed === 'false') {
            return ['Bilinmiyor', null];
        }

        if ($trimmed[0] === '{' || $trimmed[0] === '[') {
            try {
                $decoded = json_decode($trimmed, true, 512, JSON_THROW_ON_ERROR);
            } catch (\Throwable) {
                return ['Bilinmiyor', null];
            }

            if (! is_array($decoded)) {
                return ['Bilinmiyor', null];
            }

            $name = $decoded['countryName'] ?? $decoded['country'] ?? null;
            $code = $decoded['countryCode'] ?? null;

            if (is_string($name) && trim($name) !== '' && strtolower($name) !== 'null') {
                return [trim($name), is_string($code) ? $code : null];
            }

            return ['Bilinmiyor', null];
        }

        // Plain IP (pre-job) — no country yet
        return ['Bilinmiyor', null];
    }

    /**
     * @return list<array{label: string, count: int, percent: float}>
     */
    private function aggregateLanguages(int $linkId, Carbon $from, Carbon $to, int $periodTotal): array
    {
        $rows = ShetabitVisit::query()
            ->where('link_id', $linkId)
            ->whereBetween('created_at', [$from, $to])
            ->pluck('languages');

        $nameByCode = Language::query()->pluck('name', 'code');

        $counts = [];
        foreach ($rows as $raw) {
            $label = $this->parseLanguageLabel($raw, $nameByCode);
            $counts[$label] = ($counts[$label] ?? 0) + 1;
        }

        arsort($counts);

        $result = [];
        $i = 0;
        foreach ($counts as $label => $count) {
            if ($i >= self::BREAKDOWN_LIMIT) {
                break;
            }
            $result[] = [
                'label' => $label,
                'count' => $count,
                'percent' => $periodTotal > 0 ? round(($count * 100) / $periodTotal, 1) : 0.0,
            ];
            $i++;
        }

        return $result;
    }

    private function parseLanguageLabel(mixed $raw, Collection $nameByCode): string
    {
        if ($raw === null || $raw === '') {
            return 'Bilinmiyor';
        }

        $decoded = $raw;
        if (is_string($raw)) {
            try {
                $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
            } catch (\Throwable) {
                return 'Bilinmiyor';
            }
        }

        if (! is_array($decoded) || $decoded === []) {
            return 'Bilinmiyor';
        }

        // Legacy UI used index [1]; fall back to [0]
        $code = null;
        if (isset($decoded[1]) && is_string($decoded[1]) && $decoded[1] !== '') {
            $code = strtolower($decoded[1]);
        } elseif (isset($decoded[0]) && is_string($decoded[0]) && $decoded[0] !== '') {
            $code = strtolower($decoded[0]);
        }

        if ($code === null) {
            return 'Bilinmiyor';
        }

        $short = explode('-', $code)[0];

        return $nameByCode[$code]
            ?? $nameByCode[$short]
            ?? strtoupper($short);
    }

    /**
     * @return list<array{label: string, count: int, percent: float}>
     */
    private function aggregateReferrers(int $linkId, Carbon $from, Carbon $to, int $periodTotal): array
    {
        $referers = ShetabitVisit::query()
            ->where('link_id', $linkId)
            ->whereBetween('created_at', [$from, $to])
            ->pluck('referer');

        $counts = [];
        foreach ($referers as $raw) {
            $label = $this->normalizeReferrerHost($raw);
            $counts[$label] = ($counts[$label] ?? 0) + 1;
        }

        arsort($counts);

        $result = [];
        $i = 0;
        foreach ($counts as $label => $count) {
            if ($i >= self::BREAKDOWN_LIMIT) {
                break;
            }
            $result[] = [
                'label' => $label,
                'count' => $count,
                'percent' => $periodTotal > 0 ? round(($count * 100) / $periodTotal, 1) : 0.0,
            ];
            $i++;
        }

        return $result;
    }

    private function normalizeReferrerHost(mixed $raw): string
    {
        if ($raw === null) {
            return 'Doğrudan';
        }

        if (! is_string($raw)) {
            return 'Doğrudan';
        }

        $trimmed = trim($raw);
        if ($trimmed === '') {
            return 'Doğrudan';
        }

        $host = parse_url($trimmed, PHP_URL_HOST);
        if (is_string($host) && $host !== '') {
            return strtolower($host);
        }

        // Relative or malformed — treat as direct-ish unknown host label
        if (strlen($trimmed) > 80) {
            return 'Bilinmiyor';
        }

        return $trimmed;
    }
}
