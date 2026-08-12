<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE = 'analytics_events';

    public function up(): void
    {
        if (Schema::hasTable(self::TABLE)) {
            return;
        }

        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id');
            $table->string('event_type', 50);
            $table->string('subject_type', 100)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('source_type', 50)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('visitor_key', 64)->nullable();
            $table->string('country_code', 10)->nullable();
            $table->string('city', 150)->nullable();
            $table->string('referrer_host', 255)->nullable();
            $table->string('device', 100)->nullable();
            $table->string('browser', 100)->nullable();
            $table->string('os', 100)->nullable();
            $table->string('language', 50)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamp('created_at')->nullable();

            $table->index(['owner_id', 'event_type', 'occurred_at'], 'analytics_events_owner_type_occurred_index');
            $table->index(['subject_type', 'subject_id', 'occurred_at'], 'analytics_events_subject_occurred_index');
            $table->index(['event_type', 'occurred_at'], 'analytics_events_type_occurred_index');
            $table->index(['source_type', 'source_id', 'occurred_at'], 'analytics_events_source_occurred_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE);
    }
};
