<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE = 'shetabit_visits';

    private const INDEX_LINK_CREATED = 'shetabit_visits_link_id_created_at_index';

    private const INDEX_CREATED = 'shetabit_visits_created_at_index';

    public function up(): void
    {
        if (! Schema::hasTable(self::TABLE)) {
            return;
        }

        Schema::table(self::TABLE, function (Blueprint $table) {
            if (! $this->indexExists(self::INDEX_LINK_CREATED)) {
                $table->index(['link_id', 'created_at'], self::INDEX_LINK_CREATED);
            }

            if (! $this->indexExists(self::INDEX_CREATED)) {
                $table->index(['created_at'], self::INDEX_CREATED);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable(self::TABLE)) {
            return;
        }

        Schema::table(self::TABLE, function (Blueprint $table) {
            if ($this->indexExists(self::INDEX_LINK_CREATED)) {
                $table->dropIndex(self::INDEX_LINK_CREATED);
            }

            if ($this->indexExists(self::INDEX_CREATED)) {
                $table->dropIndex(self::INDEX_CREATED);
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
