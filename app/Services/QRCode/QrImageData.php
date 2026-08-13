<?php

namespace App\Services\QRCode;

use Illuminate\Validation\ValidationException;

class QrImageData
{
    public const MAX_LENGTH = 1500000;

    public const PNG_SIGNATURE = "\x89PNG\r\n\x1a\n";

    public const JPEG_SIGNATURE = "\xFF\xD8\xFF";

    /**
     * @throws ValidationException
     */
    public static function assertValid(string $imgData): string
    {
        if (strlen($imgData) > self::MAX_LENGTH) {
            throw ValidationException::withMessages([
                'qr_code' => 'QR görseli çok büyük.',
            ]);
        }

        if (! preg_match('#^data:image/(png|jpeg|jpg);base64,([A-Za-z0-9+/]+=*)$#', $imgData, $matches)) {
            throw ValidationException::withMessages([
                'qr_code' => 'QR görseli yalnızca PNG veya JPEG data URI olabilir.',
            ]);
        }

        $type = strtolower($matches[1]);
        $payload = $matches[2];

        $bytes = base64_decode($payload, true);
        if ($bytes === false || $bytes === '') {
            throw ValidationException::withMessages([
                'qr_code' => 'QR görseli geçersiz.',
            ]);
        }

        if ($type === 'png') {
            if (! str_starts_with($bytes, self::PNG_SIGNATURE)) {
                throw ValidationException::withMessages([
                    'qr_code' => 'QR görseli geçerli bir PNG olmalıdır.',
                ]);
            }
        } else {
            if (! str_starts_with($bytes, self::JPEG_SIGNATURE)) {
                throw ValidationException::withMessages([
                    'qr_code' => 'QR görseli geçerli bir JPEG olmalıdır.',
                ]);
            }
        }

        return $imgData;
    }
}
