<?php

namespace App\Support;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
use RuntimeException;

class SubscriptionTransactionIdUnique
{
    public const TABLE = 'subscriptions';

    public const INDEX_NAME = 'subscriptions_transaction_id_unique';

    public static function assertNoDuplicateTransactionIds(?string $table = null): void
    {
        $table = self::qualifiedTable($table);

        $duplicateGroups = DB::table($table)
            ->selectRaw('COUNT(*) as row_count')
            ->whereNotNull('transaction_id')
            ->where('transaction_id', '!=', '')
            ->groupBy('transaction_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($duplicateGroups->isEmpty()) {
            return;
        }

        $groupCount = $duplicateGroups->count();
        $rowCount = (int) $duplicateGroups->sum('row_count');

        throw new RuntimeException(
            "Cannot add unique index on subscriptions.transaction_id: {$groupCount} duplicate group(s) covering {$rowCount} row(s) were found. Resolve them manually before running this migration. This migration does not modify data."
        );
    }

    public static function addUniqueIndex(?string $table = null): void
    {
        $table = self::qualifiedTable($table);

        self::assertNoDuplicateTransactionIds($table);

        if (self::indexExists($table)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) {
            $blueprint->unique('transaction_id', self::INDEX_NAME);
        });
    }

    public static function dropUniqueIndex(?string $table = null): void
    {
        $table = self::qualifiedTable($table);

        if (!self::indexExists($table)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) {
            $blueprint->dropUnique(self::INDEX_NAME);
        });
    }

    public static function indexExists(?string $table = null): bool
    {
        $table = self::qualifiedTable($table);
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            $rows = DB::select(
                'SHOW INDEX FROM `'.$table.'` WHERE Key_name = ?',
                [self::INDEX_NAME]
            );

            return $rows !== [];
        }

        if ($driver === 'sqlite') {
            $rows = DB::select(
                'SELECT name FROM sqlite_master WHERE type = ? AND tbl_name = ? AND name = ?',
                ['index', $table, self::INDEX_NAME]
            );

            return $rows !== [];
        }

        return false;
    }

    private static function qualifiedTable(?string $table): string
    {
        $table = $table ?: self::TABLE;

        if (!preg_match('/^[A-Za-z0-9_]+$/', $table)) {
            throw new InvalidArgumentException('Invalid table name.');
        }

        return $table;
    }
}
