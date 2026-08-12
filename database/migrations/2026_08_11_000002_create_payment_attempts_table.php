<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('order_id', 20)->unique();
            $table->foreignId('user_id');
            $table->foreignId('pricing_plan_id');
            $table->string('billing_type');
            $table->unsignedBigInteger('expected_amount');
            $table->unsignedInteger('currency');
            $table->string('three_d_session_id')->nullable();
            $table->string('provider_transaction_id')->nullable()->unique();
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_attempts');
    }
};
