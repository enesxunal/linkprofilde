<?php

use App\Support\ToslaApiPassBackfill;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('payment_gateways')) {
            return;
        }

        if (!Schema::hasColumn('payment_gateways', 'api_pass')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE payment_gateways MODIFY api_pass TEXT NULL');
        }

        ToslaApiPassBackfill::encryptExistingRows();
    }

    public function down(): void
    {
        // Encrypted values do not fit VARCHAR(191). Leave TEXT in place.
    }
};
