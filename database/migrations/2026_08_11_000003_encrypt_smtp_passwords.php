<?php

use App\Support\SmtpPasswordBackfill;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('smtp_settings')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE smtp_settings MODIFY password TEXT NOT NULL');
        }

        SmtpPasswordBackfill::encryptExistingRows();
    }

    public function down(): void
    {
        // Encrypted values do not fit VARCHAR(191). Leave TEXT in place.
    }
};
