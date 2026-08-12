<?php

namespace App\Jobs;

use App\Models\AnalyticsEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Stevebauman\Location\Facades\Location;

class EnrichAnalyticsEventLocationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Enrich analytics event geo fields after the HTTP response is sent.
     * IP is only kept in the job payload — never written to analytics_events.
     */
    public function __construct(
        public int $eventId,
        public string $ip
    ) {}

    public function handle(): void
    {
        try {
            $location = Location::get($this->ip);
            if (! $location) {
                return;
            }

            $country = null;
            if (! empty($location->countryCode) && is_string($location->countryCode)) {
                $country = substr($location->countryCode, 0, 10);
            }

            $city = null;
            if (! empty($location->cityName) && is_string($location->cityName)) {
                $city = substr($location->cityName, 0, 150);
            }

            if ($country === null && $city === null) {
                return;
            }

            AnalyticsEvent::query()->where('id', $this->eventId)->update([
                'country_code' => $country,
                'city' => $city,
            ]);
        } catch (\Throwable $th) {
            report($th);
        }
    }
}
