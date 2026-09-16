<?php

namespace App\Http\Controllers;

use App\Models\AnalyticsEvent;
use App\Models\Link;
use App\Models\Project;
use App\Models\QRCode;
use App\Services\Analytics\LinkAnalyticsService;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(
        private LinkAnalyticsService $analytics
    ) {}

    public function index()
    {
        try {
            $user = auth()->user();
            $isSuperAdmin = $user->hasRole('SUPER-ADMIN');
            $primary_profile = null;

            if ($isSuperAdmin) {
                $links = Link::query()->count();
                $qrcodes = QRCode::query()->count();
                $projects = Project::query()->count();
                [$linkIds, $visitsQuery] = $this->analytics->dashboardVisitsQuery($user);
                $analytics = (clone $visitsQuery)->count();
                $visitors = $this->analytics->monthlyCounts($linkIds);
                $page_view = $this->analytics->lastSevenDaysCounts($linkIds);
            } else {
                $links = Link::query()->where('user_id', $user->id)->count();
                $qrcodes = QRCode::query()->where('user_id', $user->id)->count();
                $projects = Project::query()->where('user_id', $user->id)->count();
                [$linkIds, $visitsQuery] = $this->analytics->dashboardVisitsQuery($user);
                $analytics = (clone $visitsQuery)->count();
                $visitors = $this->analytics->monthlyCounts($linkIds);
                $page_view = $this->analytics->lastSevenDaysCounts($linkIds);
                $primary_profile = Link::query()
                    ->where('user_id', $user->id)
                    ->where('link_type', 'biolink')
                    ->orderBy('created_at')
                    ->first(['id', 'link_name', 'url_name', 'thumbnail', 'short_bio']);
            }

            $eventQuery = AnalyticsEvent::query();
            if (! $isSuperAdmin) {
                $eventQuery->where('owner_id', $user->id);
            }

            $last30Start = now()->subDays(29)->startOfDay();
            $event30 = (clone $eventQuery)->where('occurred_at', '>=', $last30Start);

            $event_metrics = [
                'interactions_30d' => (clone $event30)->whereIn('event_type', [
                    'link_click',
                    'social_click',
                    'vcard_download',
                    'share',
                    AnalyticsEvent::TYPE_QR_SCAN,
                ])->count(),
                'link_clicks_30d' => (clone $event30)->where('event_type', 'link_click')->count(),
                'social_clicks_30d' => (clone $event30)->where('event_type', 'social_click')->count(),
                'vcard_downloads_30d' => (clone $event30)->where('event_type', 'vcard_download')->count(),
                'shares_30d' => (clone $event30)->where('event_type', 'share')->count(),
                'qr_scans_30d' => (clone $event30)->where('event_type', AnalyticsEvent::TYPE_QR_SCAN)->count(),
            ];

            return Inertia::render(
                'Dashboard',
                compact(
                    'qrcodes',
                    'links',
                    'analytics',
                    'projects',
                    'visitors',
                    'page_view',
                    'primary_profile',
                    'event_metrics'
                )
            );
        } catch (\Throwable $th) {
            return back()->with('error', \App\Helpers\AppHelper::publicExceptionMessage($th));
        }
    }
}
