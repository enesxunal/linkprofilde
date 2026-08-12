<?php

namespace App\Services\QRCode;

use App\Jobs\EnrichAnalyticsEventLocationJob;
use App\Models\AnalyticsEvent;
use App\Models\QRCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class QrScanRecorder
{
    /**
     * Soft cap to reduce analytics spam without blocking redirects.
     * Key is a hash of IP + QR id (IP is not stored in analytics_events).
     */
    private const EVENT_PER_MINUTE = 120;

    /**
     * Best-effort scan insert. Returns null when soft-throttled.
     * Callers must not let exceptions from this method block redirects
     * (QrRedirectController wraps the call in try/catch).
     */
    public function record(QRCode $qrCode, Request $request, QrDestinationResult $destination): ?AnalyticsEvent
    {
        $throttleKey = 'qr-scan-event:'.hash(
            'sha256',
            ($request->ip() ?? 'unknown').'|'.$qrCode->id
        );

        if (RateLimiter::tooManyAttempts($throttleKey, self::EVENT_PER_MINUTE)) {
            return null;
        }

        RateLimiter::hit($throttleKey, 60);

        $metadata = [
            'destination_type' => $destination->destinationType ?? $qrCode->destination_type,
        ];

        $linkId = $destination->destinationLinkId ?? $qrCode->destination_link_id;
        if ($linkId) {
            $metadata['destination_link_id'] = (int) $linkId;
        }

        $event = AnalyticsEvent::query()->create([
            'owner_id' => $qrCode->user_id,
            'event_type' => AnalyticsEvent::TYPE_QR_SCAN,
            'subject_type' => AnalyticsEvent::SUBJECT_QR_CODE,
            'subject_id' => $qrCode->id,
            'source_type' => AnalyticsEvent::SOURCE_QR,
            'source_id' => $qrCode->id,
            'visitor_key' => null,
            'country_code' => null,
            'city' => null,
            'referrer_host' => $this->referrerHost($request),
            'device' => $this->safeVisitorValue(fn () => $request->visitor()->device()),
            'browser' => $this->safeVisitorValue(fn () => $request->visitor()->browser()),
            'os' => $this->safeVisitorValue(fn () => $request->visitor()->platform()),
            'language' => $this->primaryLanguage($request),
            'metadata' => $metadata,
            'occurred_at' => now(),
            'created_at' => now(),
        ]);

        $ip = $request->ip();
        if (is_string($ip) && $ip !== '') {
            EnrichAnalyticsEventLocationJob::dispatch($event->id, $ip)->afterResponse();
        }

        return $event;
    }

    private function referrerHost(Request $request): ?string
    {
        $referer = $request->headers->get('referer');
        if (! is_string($referer) || trim($referer) === '') {
            return null;
        }

        $host = parse_url($referer, PHP_URL_HOST);

        return is_string($host) && $host !== '' ? strtolower($host) : null;
    }

    private function primaryLanguage(Request $request): ?string
    {
        try {
            $languages = $request->visitor()->languages();
            if (is_array($languages) && isset($languages[0]) && is_string($languages[0])) {
                return substr($languages[0], 0, 50);
            }
        } catch (\Throwable) {
            // fall through
        }

        $header = $request->headers->get('accept-language');
        if (! is_string($header) || $header === '') {
            return null;
        }

        $primary = strtok($header, ',;');

        return is_string($primary) && $primary !== '' ? substr(trim($primary), 0, 50) : null;
    }

    private function safeVisitorValue(callable $callback): ?string
    {
        try {
            $value = $callback();

            return is_string($value) && $value !== '' ? substr($value, 0, 100) : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
