<?php

namespace Tests\Unit;

use App\Http\Controllers\Gateways\ToslaController;
use App\Models\PaymentGateway;
use App\Support\ToslaApiPassBackfill;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use ReflectionMethod;
use Tests\TestCase;

class ToslaApiPassEncryptionTest extends TestCase
{
    use DatabaseTransactions;

    private function seedTosla(array $overrides = []): PaymentGateway
    {
        $gateway = PaymentGateway::where('name', 'tosla')->first() ?? new PaymentGateway();
        $gateway->name = 'tosla';
        $gateway->active = $overrides['active'] ?? true;
        $gateway->key = $overrides['key'] ?? ($overrides['client_id'] ?? 'client-id');
        $gateway->secret = null;
        $gateway->client_id = $overrides['client_id'] ?? 'client-id';
        $gateway->api_user = $overrides['api_user'] ?? 'api-user';
        $gateway->api_pass = $overrides['api_pass'] ?? 'plain-secret';
        $gateway->save();

        return $gateway->fresh();
    }

    private function rawApiPass(int $id): ?string
    {
        return DB::table('payment_gateways')->where('id', $id)->value('api_pass');
    }

    private function invokePrivate(object $instance, string $method, array $args = []): mixed
    {
        $reflection = new ReflectionMethod($instance, $method);
        $reflection->setAccessible(true);

        return $reflection->invoke($instance, ...$args);
    }

    public function test_legacy_plaintext_is_encrypted_by_backfill(): void
    {
        $gateway = $this->seedTosla(['api_pass' => 'temp-value']);
        DB::table('payment_gateways')->where('id', $gateway->id)->update([
            'api_pass' => 'legacy-plain',
        ]);

        $updated = ToslaApiPassBackfill::encryptExistingRows();
        $raw = $this->rawApiPass($gateway->id);

        $this->assertGreaterThanOrEqual(1, $updated);
        $this->assertNotSame('legacy-plain', $raw);
        $this->assertSame('legacy-plain', Crypt::decryptString($raw));
        $this->assertSame('legacy-plain', PaymentGateway::find($gateway->id)->api_pass);
    }

    public function test_eloquent_read_returns_decrypted_plaintext(): void
    {
        $plain = 'plain-secret-b';
        $gateway = $this->seedTosla(['api_pass' => $plain]);

        $this->assertSame($plain, $gateway->api_pass);
        $this->assertSame($plain, PaymentGateway::find($gateway->id)->api_pass);
    }

    public function test_serialization_hides_api_pass(): void
    {
        $gateway = $this->seedTosla(['api_pass' => 'plain-secret-c']);

        $array = $gateway->toArray();
        $json = $gateway->toJson();

        $this->assertArrayNotHasKey('api_pass', $array);
        $this->assertStringNotContainsString('plain-secret-c', $json);
        $this->assertStringNotContainsString('"api_pass"', $json);
    }

    public function test_backfill_is_idempotent_and_does_not_double_encrypt(): void
    {
        $gateway = $this->seedTosla(['api_pass' => 'temp-value']);
        DB::table('payment_gateways')->where('id', $gateway->id)->update([
            'api_pass' => 'once-plain',
        ]);

        ToslaApiPassBackfill::encryptExistingRows();
        $rawAfterFirst = $this->rawApiPass($gateway->id);

        $updatedSecond = ToslaApiPassBackfill::encryptExistingRows();
        $rawAfterSecond = $this->rawApiPass($gateway->id);

        $this->assertSame(0, $updatedSecond);
        $this->assertSame($rawAfterFirst, $rawAfterSecond);
        $this->assertSame('once-plain', Crypt::decryptString($rawAfterSecond));
        $this->assertSame('once-plain', PaymentGateway::find($gateway->id)->api_pass);
    }

    public function test_tosla_credentials_uses_decrypted_api_pass(): void
    {
        $this->seedTosla([
            'active' => true,
            'client_id' => 'client-id',
            'api_user' => 'api-user',
            'api_pass' => 'credential-secret',
        ]);

        $controller = app(ToslaController::class);
        $credentials = $this->invokePrivate($controller, 'toslaCredentials');

        $this->assertSame('credential-secret', $credentials['api_pass']);
        $this->assertSame('client-id', $credentials['client_id']);
        $this->assertSame('api-user', $credentials['api_user']);
    }

    public function test_request_hash_matches_plaintext_secret_when_db_is_encrypted(): void
    {
        $plain = 'hash-secret';
        $gateway = $this->seedTosla([
            'client_id' => 'cid',
            'api_user' => 'auser',
            'api_pass' => $plain,
        ]);

        $raw = $this->rawApiPass($gateway->id);
        $this->assertNotSame($plain, $raw);

        $controller = app(ToslaController::class);
        $credentials = $this->invokePrivate($controller, 'toslaCredentials');
        $auth = $this->invokePrivate($controller, 'makeRequestAuth', [$credentials]);

        $expected = base64_encode(hash(
            'sha512',
            $plain . 'cid' . 'auser' . $auth['rnd'] . $auth['timeSpan'],
            true
        ));

        $this->assertSame($expected, $auth['hash']);
        $this->assertSame($plain, $credentials['api_pass']);
    }
}
