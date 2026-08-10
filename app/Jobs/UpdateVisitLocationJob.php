<?php

namespace App\Jobs;

use App\Models\ShetabitVisit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Stevebauman\Location\Facades\Location;

class UpdateVisitLocationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Ziyaret kaydının konum bilgisini arka planda günceller.
     * Sayfa yanıtının Location::get() gecikmesinden etkilenmemesi için kuyrukta çalışır.
     */
    public function __construct(
        public int $visitId,
        public string $ip
    ) {}

    public function handle(): void
    {
        try {
            $location = Location::get($this->ip);
            ShetabitVisit::where('id', $this->visitId)->update([
                'ip' => json_encode($location),
            ]);
        } catch (\Throwable $th) {
            report($th);
        }
    }
}
