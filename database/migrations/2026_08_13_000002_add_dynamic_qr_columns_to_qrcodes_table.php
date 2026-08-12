<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE = 'qrcodes';

    private const INDEX_USER_DYNAMIC = 'qrcodes_user_id_is_dynamic_index';

    private const INDEX_DESTINATION_LINK = 'qrcodes_destination_link_id_index';

    public function up(): void
    {
        if (! Schema::hasTable(self::TABLE)) {
            return;
        }

        Schema::table(self::TABLE, function (Blueprint $table) {
            if (! Schema::hasColumn(self::TABLE, 'public_code')) {
                $table->string('public_code', 12)->nullable()->unique()->after('name');
            }

            if (! Schema::hasColumn(self::TABLE, 'is_dynamic')) {
                $table->boolean('is_dynamic')->default(false)->after('public_code');
            }

            if (! Schema::hasColumn(self::TABLE, 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_dynamic');
            }

            if (! Schema::hasColumn(self::TABLE, 'destination_type')) {
                $table->string('destination_type')->nullable()->after('is_active');
            }

            if (! Schema::hasColumn(self::TABLE, 'destination_url')) {
                $table->text('destination_url')->nullable()->after('destination_type');
            }

            if (! Schema::hasColumn(self::TABLE, 'destination_link_id')) {
                $table->unsignedBigInteger('destination_link_id')->nullable()->after('destination_url');
            }

            if (! Schema::hasColumn(self::TABLE, 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Existing production rows stay legacy/static.
        if (Schema::hasColumn(self::TABLE, 'is_dynamic')) {
            DB::table(self::TABLE)->whereNull('is_dynamic')->update(['is_dynamic' => false]);
            DB::table(self::TABLE)->whereNull('is_active')->update(['is_active' => true]);
        }

        Schema::table(self::TABLE, function (Blueprint $table) {
            if (Schema::hasColumn(self::TABLE, 'is_dynamic') && ! $this->indexExists(self::INDEX_USER_DYNAMIC)) {
                $table->index(['user_id', 'is_dynamic'], self::INDEX_USER_DYNAMIC);
            }

            if (Schema::hasColumn(self::TABLE, 'destination_link_id') && ! $this->indexExists(self::INDEX_DESTINATION_LINK)) {
                $table->index(['destination_link_id'], self::INDEX_DESTINATION_LINK);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable(self::TABLE)) {
            return;
        }

        Schema::table(self::TABLE, function (Blueprint $table) {
            if ($this->indexExists(self::INDEX_USER_DYNAMIC)) {
                $table->dropIndex(self::INDEX_USER_DYNAMIC);
            }

            if ($this->indexExists(self::INDEX_DESTINATION_LINK)) {
                $table->dropIndex(self::INDEX_DESTINATION_LINK);
            }
        });

        Schema::table(self::TABLE, function (Blueprint $table) {
            $columns = [];

            foreach ([
                'public_code',
                'is_dynamic',
                'is_active',
                'destination_type',
                'destination_url',
                'destination_link_id',
                'deleted_at',
            ] as $column) {
                if (Schema::hasColumn(self::TABLE, $column)) {
                    $columns[] = $column;
                }
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }

    private function indexExists(string $indexName): bool
    {
        $database = Schema::getConnection()->getDatabaseName();

        $row = DB::selectOne(
            'SELECT 1 AS present
             FROM information_schema.statistics
             WHERE table_schema = ?
               AND table_name = ?
               AND index_name = ?
             LIMIT 1',
            [$database, self::TABLE, $indexName]
        );

        return $row !== null;
    }
};
