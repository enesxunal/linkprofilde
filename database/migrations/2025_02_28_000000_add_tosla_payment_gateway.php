<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('payment_gateways')->where('name', 'tosla')->exists();
        if (!$exists) {
            DB::table('payment_gateways')->insert([
                'active' => false,
                'name' => 'tosla',
                'key' => '',
                'secret' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('payment_gateways')->where('name', 'tosla')->delete();
    }
};
