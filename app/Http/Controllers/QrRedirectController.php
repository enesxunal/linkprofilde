<?php

namespace App\Http\Controllers;

use App\Models\QRCode;
use App\Services\QRCode\QrDestinationResolver;
use App\Services\QRCode\QrScanRecorder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class QrRedirectController extends Controller
{
    public function __construct(
        private QrDestinationResolver $destinations,
        private QrScanRecorder $scans,
    ) {}

    public function __invoke(Request $request, string $publicCode)
    {
        $qrCode = QRCode::withTrashed()
            ->where('public_code', $publicCode)
            ->where('is_dynamic', true)
            ->first();

        if (! $qrCode) {
            return $this->unavailable('Bağlantı kullanılamıyor.', 404);
        }

        if ($qrCode->trashed() || ! $qrCode->is_active) {
            return $this->unavailable('Bu QR kod artık aktif değil.', 410);
        }

        $destination = $this->destinations->resolve($qrCode);
        if (! $destination->ok || ! $destination->url) {
            return $this->unavailable('Bağlantı kullanılamıyor.', 410);
        }

        try {
            $this->scans->record($qrCode, $request, $destination);
        } catch (Throwable $e) {
            report($e);
        }

        return redirect()
            ->away($destination->url, 302)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache');
    }

    private function unavailable(string $message, int $status): Response
    {
        return response($message, $status, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }
}
