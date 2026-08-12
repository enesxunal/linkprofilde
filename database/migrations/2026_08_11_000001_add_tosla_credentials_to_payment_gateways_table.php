<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            $table->string('client_id')->nullable()->after('secret');
            $table->string('api_user')->nullable()->after('client_id');
            $table->string('api_pass')->nullable()->after('api_user');
        });
    }

    public function down(): void
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            $table->dropColumn(['client_id', 'api_user', 'api_pass']);
        });
    }
};
