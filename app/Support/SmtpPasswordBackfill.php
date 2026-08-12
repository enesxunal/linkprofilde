<?php

namespace App\Support;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class SmtpPasswordBackfill
{
    public static function encryptStoredValue(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        try {
            Crypt::decryptString($value);

            return $value;
        } catch (DecryptException $e) {
            return Crypt::encryptString($value);
        }
    }

    public static function encryptExistingRows(): int
    {
        if (!DB::getSchemaBuilder()->hasTable('smtp_settings')) {
            return 0;
        }

        $updated = 0;
        $rows = DB::table('smtp_settings')->select('id', 'password')->get();

        foreach ($rows as $row) {
            $current = $row->password;
            if (!is_string($current) || $current === '') {
                continue;
            }

            $next = self::encryptStoredValue($current);
            if ($next === null || hash_equals($current, $next)) {
                continue;
            }

            DB::table('smtp_settings')->where('id', $row->id)->update([
                'password' => $next,
            ]);
            $updated++;
        }

        return $updated;
    }
}
