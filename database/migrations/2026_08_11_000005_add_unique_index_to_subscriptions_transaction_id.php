<?php

use App\Support\SubscriptionTransactionIdUnique;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('subscriptions')) {
            return;
        }

        if (!Schema::hasColumn('subscriptions', 'transaction_id')) {
            return;
        }

        SubscriptionTransactionIdUnique::addUniqueIndex();
    }

    public function down(): void
    {
        if (!Schema::hasTable('subscriptions')) {
            return;
        }

        SubscriptionTransactionIdUnique::dropUniqueIndex();
    }
};
