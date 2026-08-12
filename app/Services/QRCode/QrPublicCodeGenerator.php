<?php

namespace App\Services\QRCode;

use App\Models\QRCode;
use Illuminate\Database\QueryException;
use RuntimeException;

class QrPublicCodeGenerator
{
    public const LENGTH = 12;

    private const ALPHABET = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    private const MAX_ATTEMPTS = 16;

    public function generate(): string
    {
        $alphabetLength = strlen(self::ALPHABET);
        $code = '';

        for ($i = 0; $i < self::LENGTH; $i++) {
            $code .= self::ALPHABET[random_int(0, $alphabetLength - 1)];
        }

        // Avoid a pure-numeric token that could be confused with a DB id.
        if (ctype_digit($code)) {
            $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            $pos = random_int(0, self::LENGTH - 1);
            $code[$pos] = $letters[random_int(0, strlen($letters) - 1)];
        }

        return $code;
    }

    /**
     * Optimistic free-code probe. Prefer assign() for race-safe persistence.
     */
    public function generateUnique(): string
    {
        for ($attempt = 0; $attempt < self::MAX_ATTEMPTS; $attempt++) {
            $code = $this->generate();

            $exists = QRCode::withTrashed()
                ->where('public_code', $code)
                ->exists();

            if (! $exists) {
                return $code;
            }
        }

        throw new RuntimeException('Unable to generate a unique QR public_code.');
    }

    /**
     * Assign a public_code once and persist it.
     * DB unique constraint is the source of truth; retries only on public_code collisions.
     * Never overwrites an existing code.
     */
    public function assign(QRCode $qrCode): string
    {
        if ($qrCode->public_code !== null && $qrCode->public_code !== '') {
            return $qrCode->public_code;
        }

        for ($attempt = 0; $attempt < self::MAX_ATTEMPTS; $attempt++) {
            $code = $this->generate();
            $qrCode->public_code = $code;

            try {
                $qrCode->save();

                return $code;
            } catch (QueryException $e) {
                $qrCode->public_code = null;

                if (! $this->isPublicCodeUniqueViolation($e)) {
                    throw $e;
                }
            }
        }

        throw new RuntimeException('Unable to assign a unique QR public_code.');
    }

    public function isPublicCodeUniqueViolation(QueryException $e): bool
    {
        $sqlState = $e->errorInfo[0] ?? null;
        if ($sqlState !== '23000') {
            return false;
        }

        $message = $e->getMessage();

        return str_contains($message, 'qrcodes_public_code_unique')
            || str_contains($message, 'public_code');
    }
}
