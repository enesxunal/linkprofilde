<?php

namespace Tests\Unit;

use App\Models\Subscription;
use App\Models\User;
use App\Support\SubscriptionTransactionIdUnique;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SubscriptionTransactionIdConstraintTest extends TestCase
{
    use DatabaseTransactions;

    public function test_subscriptions_table_rejects_duplicate_transaction_id_after_unique_index(): void
    {
        $this->assertTrue(
            SubscriptionTransactionIdUnique::indexExists(),
            'subscriptions_transaction_id_unique must exist on subscriptions.'
        );

        $user = User::factory()->create();
        $payload = [
            'user_id' => $user->id,
            'method' => 'tosla',
            'billing' => 'monthly',
            'transaction_id' => 'real-table-unique-once',
            'total_price' => '10.00',
            'currency' => 'TRY',
        ];

        Subscription::create($payload);

        $this->expectException(QueryException::class);

        Subscription::create($payload);
    }

    public function test_subscriptions_table_allows_distinct_transaction_ids(): void
    {
        $this->assertTrue(
            SubscriptionTransactionIdUnique::indexExists(),
            'subscriptions_transaction_id_unique must exist on subscriptions.'
        );

        $user = User::factory()->create();

        Subscription::create([
            'user_id' => $user->id,
            'method' => 'tosla',
            'billing' => 'monthly',
            'transaction_id' => 'real-table-unique-a',
            'total_price' => '10.00',
            'currency' => 'TRY',
        ]);

        Subscription::create([
            'user_id' => $user->id,
            'method' => 'tosla',
            'billing' => 'yearly',
            'transaction_id' => 'real-table-unique-b',
            'total_price' => '20.00',
            'currency' => 'TRY',
        ]);

        $this->assertTrue(Subscription::where('transaction_id', 'real-table-unique-a')->exists());
        $this->assertTrue(Subscription::where('transaction_id', 'real-table-unique-b')->exists());
    }
}
