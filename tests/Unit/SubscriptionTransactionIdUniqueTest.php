<?php

namespace Tests\Unit;

use App\Support\SubscriptionTransactionIdUnique;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Tests\TestCase;

class SubscriptionTransactionIdUniqueTest extends TestCase
{
    private const SANDBOX_TABLE = 'tmp_subscription_txid_unique_test';

    protected function setUp(): void
    {
        parent::setUp();
        $this->recreateSandboxTable();
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists(self::SANDBOX_TABLE);
        parent::tearDown();
    }

    public function test_unique_index_is_added_when_there_are_no_duplicates(): void
    {
        DB::table(self::SANDBOX_TABLE)->insert([
            ['transaction_id' => 'tx-alpha'],
            ['transaction_id' => 'tx-beta'],
        ]);

        SubscriptionTransactionIdUnique::addUniqueIndex(self::SANDBOX_TABLE);

        $this->assertTrue(SubscriptionTransactionIdUnique::indexExists(self::SANDBOX_TABLE));
        $this->assertSame(2, DB::table(self::SANDBOX_TABLE)->count());
    }

    public function test_precheck_rejects_duplicate_non_empty_ids_without_changing_data(): void
    {
        $duplicateValue = 'dup-txid-fixture';

        DB::table(self::SANDBOX_TABLE)->insert([
            ['transaction_id' => $duplicateValue],
            ['transaction_id' => $duplicateValue],
            ['transaction_id' => 'tx-other'],
        ]);

        $before = $this->sandboxSnapshot();

        try {
            SubscriptionTransactionIdUnique::addUniqueIndex(self::SANDBOX_TABLE);
            $this->fail('Expected RuntimeException when duplicate transaction_id groups exist.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('duplicate group', strtolower($e->getMessage()));
            $this->assertStringNotContainsString($duplicateValue, $e->getMessage());
            $this->assertStringNotContainsString('tx-other', $e->getMessage());
        }

        $this->assertSame($before, $this->sandboxSnapshot());
        $this->assertFalse(SubscriptionTransactionIdUnique::indexExists(self::SANDBOX_TABLE));
    }

    public function test_unique_index_rejects_a_second_insert_with_the_same_transaction_id(): void
    {
        DB::table(self::SANDBOX_TABLE)->insert([
            ['transaction_id' => 'tx-once'],
        ]);

        SubscriptionTransactionIdUnique::addUniqueIndex(self::SANDBOX_TABLE);

        $this->expectException(QueryException::class);

        DB::table(self::SANDBOX_TABLE)->insert([
            ['transaction_id' => 'tx-once'],
        ]);
    }

    public function test_distinct_transaction_ids_can_be_inserted_after_unique_index(): void
    {
        SubscriptionTransactionIdUnique::addUniqueIndex(self::SANDBOX_TABLE);

        DB::table(self::SANDBOX_TABLE)->insert([
            ['transaction_id' => 'tx-one'],
            ['transaction_id' => 'tx-two'],
        ]);

        $this->assertSame(2, DB::table(self::SANDBOX_TABLE)->count());
        $this->assertSame(
            2,
            DB::table(self::SANDBOX_TABLE)->distinct()->count('transaction_id')
        );
    }

    private function recreateSandboxTable(): void
    {
        Schema::dropIfExists(self::SANDBOX_TABLE);
        Schema::create(self::SANDBOX_TABLE, function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id');
        });
    }

    private function sandboxSnapshot(): array
    {
        return DB::table(self::SANDBOX_TABLE)->orderBy('id')->get()->map(function ($row) {
            return [
                'id' => (int) $row->id,
                'transaction_id' => (string) $row->transaction_id,
            ];
        })->all();
    }
}
