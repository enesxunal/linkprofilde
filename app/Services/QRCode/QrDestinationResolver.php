<?php

namespace App\Services\QRCode;

use App\Models\Link;
use App\Models\QRCode;
use App\Support\SafeUrl;
use Illuminate\Validation\ValidationException;

class QrDestinationResolver
{
    /**
     * Validate destination fields before assignment (create/edit).
     *
     * @throws ValidationException
     */
    public function assertAssignable(
        QRCode $qrCode,
        string $destinationType,
        ?string $destinationUrl = null,
        ?int $destinationLinkId = null
    ): void {
        $type = strtolower(trim($destinationType));

        if (! in_array($type, [
            QRCode::DESTINATION_EXTERNAL,
            QRCode::DESTINATION_BIOLINK,
            QRCode::DESTINATION_SHORTLINK,
        ], true)) {
            throw ValidationException::withMessages([
                'destination_type' => 'Geçersiz QR hedef tipi.',
            ]);
        }

        if ($type === QRCode::DESTINATION_EXTERNAL) {
            $canonical = SafeUrl::canonicalize($destinationUrl);
            if ($canonical === null) {
                throw ValidationException::withMessages([
                    'destination_url' => 'Hedef URL güvenli bir http/https adresi olmalıdır.',
                ]);
            }

            if ($this->pointsToLocalQrRedirect($canonical)) {
                throw ValidationException::withMessages([
                    'destination_url' => 'QR hedefi başka bir dynamic QR adresine yönlendirilemez.',
                ]);
            }

            if ($qrCode->public_code && $this->isSelfQrUrl($canonical, $qrCode->public_code)) {
                throw ValidationException::withMessages([
                    'destination_url' => 'QR hedefi kendisine yönlendirilemez.',
                ]);
            }

            return;
        }

        if (! $destinationLinkId) {
            throw ValidationException::withMessages([
                'destination_link_id' => 'Hedef link gerekli.',
            ]);
        }

        $link = Link::query()->where('id', $destinationLinkId)->first();
        if (! $link) {
            throw ValidationException::withMessages([
                'destination_link_id' => 'Hedef link bulunamadı.',
            ]);
        }

        if ((int) $link->user_id !== (int) $qrCode->user_id) {
            throw ValidationException::withMessages([
                'destination_link_id' => 'Bu linke erişim yetkiniz yok.',
            ]);
        }

        $expectedType = $type === QRCode::DESTINATION_BIOLINK ? 'biolink' : 'shortlink';
        if ($link->link_type !== $expectedType) {
            throw ValidationException::withMessages([
                'destination_link_id' => 'Link tipi hedef tipiyle uyuşmuyor.',
            ]);
        }
    }

    /**
     * Resolve a redirect URL for an active dynamic QR.
     */
    public function resolve(QRCode $qrCode): QrDestinationResult
    {
        if (! $qrCode->is_dynamic) {
            return QrDestinationResult::unavailable('not_dynamic');
        }

        $type = strtolower((string) $qrCode->destination_type);

        if ($type === QRCode::DESTINATION_EXTERNAL) {
            $canonical = SafeUrl::canonicalize($qrCode->destination_url);
            if ($canonical === null) {
                return QrDestinationResult::unavailable('unsafe_or_missing_url');
            }

            if ($this->pointsToLocalQrRedirect($canonical)) {
                return QrDestinationResult::unavailable('qr_redirect_loop');
            }

            if ($qrCode->public_code && $this->isSelfQrUrl($canonical, $qrCode->public_code)) {
                return QrDestinationResult::unavailable('self_redirect');
            }

            return QrDestinationResult::ok($canonical, QRCode::DESTINATION_EXTERNAL);
        }

        if (in_array($type, [QRCode::DESTINATION_BIOLINK, QRCode::DESTINATION_SHORTLINK], true)) {
            $link = Link::query()->where('id', $qrCode->destination_link_id)->first();
            if (! $link) {
                return QrDestinationResult::unavailable('missing_link');
            }

            if ((int) $link->user_id !== (int) $qrCode->user_id) {
                return QrDestinationResult::unavailable('ownership_mismatch');
            }

            $expectedType = $type === QRCode::DESTINATION_BIOLINK ? 'biolink' : 'shortlink';
            if ($link->link_type !== $expectedType) {
                return QrDestinationResult::unavailable('link_type_mismatch');
            }

            if (! is_string($link->url_name) || trim($link->url_name) === '') {
                return QrDestinationResult::unavailable('missing_url_name');
            }

            // Point at the public link route so Phase 1 Shetabit tracking still runs.
            return QrDestinationResult::ok(
                url('/'.$link->url_name),
                $type,
                (int) $link->id
            );
        }

        return QrDestinationResult::unavailable('missing_destination');
    }

    public function pointsToLocalQrRedirect(string $url): bool
    {
        $parts = parse_url($url);
        if (! is_array($parts) || empty($parts['host'])) {
            return false;
        }

        $path = $parts['path'] ?? '';
        if (! preg_match('#^/q/([A-Za-z0-9]{12})/?$#i', $path)) {
            return false;
        }

        return $this->isAppHost((string) $parts['host']);
    }

    public function isSelfQrUrl(string $url, string $publicCode): bool
    {
        $parts = parse_url($url);
        if (! is_array($parts) || empty($parts['host'])) {
            return false;
        }

        $path = $parts['path'] ?? '';
        if (! preg_match('#^/q/([A-Za-z0-9]{12})/?$#i', $path, $matches)) {
            return false;
        }

        if (! hash_equals($publicCode, $matches[1])) {
            return false;
        }

        return $this->isAppHost((string) $parts['host']);
    }

    private function isAppHost(string $host): bool
    {
        $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);
        if (! is_string($appHost) || $appHost === '') {
            return false;
        }

        return strcasecmp(rtrim($host, '.'), rtrim($appHost, '.')) === 0;
    }
}
