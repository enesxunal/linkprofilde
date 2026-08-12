<?php

namespace App\Support;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class ToslaApiPassBackfill
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
        if (!DB::getSchemaBuilder()->hasTable('payment_gateways')) {
            return 0;
        }

        if (!DB::getSchemaBuilder()->hasColumn('payment_gateways', 'api_pass')) {
            return 0;
        }

        $updated = 0;
        $rows = DB::table('payment_gateways')->select('id', 'api_pass')->get();

        foreach ($rows as $row) {
            $current = $row->api_pass;
            if (!is_string($current) || $current === '') {
                continue;
            }

            $next = self::encryptStoredValue($current);
            if ($next === null || hash_equals($current, $next)) {
                continue;
            }

            DB::table('payment_gateways')->where('id', $row->id)->update([
                'api_pass' => $next,
            ]);
            $updated++;
        }

        return $updated;
    }
}
