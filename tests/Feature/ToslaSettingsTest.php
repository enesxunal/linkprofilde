<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\PaymentGateway;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ToslaSettingsTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    private function admin(): User
    {
        Role::findOrCreate('SUPER-ADMIN');

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole('SUPER-ADMIN');

        return $user;
    }

    private function seedTosla(string $apiPass = 'stored-secret'): PaymentGateway
    {
        $gateway = PaymentGateway::where('name', 'tosla')->first() ?? new PaymentGateway();
        $gateway->name = 'tosla';
        $gateway->active = true;
        $gateway->key = 'client-id';
        $gateway->secret = null;
        $gateway->client_id = 'client-id';
        $gateway->api_user = 'api-user';
        $gateway->api_pass = $apiPass;
        $gateway->save();

        return $gateway->fresh();
    }

    public function test_admin_page_props_do_not_include_api_pass(): void
    {
        $this->seedTosla('should-not-leak');
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get('/admin/payments-setup')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/PaymentSetup')
                ->has('tosla')
                ->missing('tosla.api_pass')
                ->where('tosla.client_id', 'client-id')
                ->where('tosla.api_user', 'api-user')
            );
    }

    public function test_blank_api_pass_update_preserves_encrypted_value(): void
    {
        $gateway = $this->seedTosla('keep-original');
        $rawBefore = DB::table('payment_gateways')->where('id', $gateway->id)->value('api_pass');
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patch('/admin/payments-setup/tosla', [
                'allow_tosla' => 1,
                'tosla_client_id' => 'client-updated',
                'tosla_api_user' => 'api-user',
                'tosla_api_pass' => '',
            ])
            ->assertRedirect();

        $fresh = $gateway->fresh();
        $rawAfter = DB::table('payment_gateways')->where('id', $fresh->id)->value('api_pass');

        $this->assertSame($rawBefore, $rawAfter);
        $this->assertSame('keep-original', $fresh->api_pass);
        $this->assertSame('client-updated', $fresh->client_id);
        $this->assertNotSame('keep-original', $rawAfter);
    }

    public function test_new_api_pass_update_replaces_secret(): void
    {
        $gateway = $this->seedTosla('old-secret');
        $rawBefore = DB::table('payment_gateways')->where('id', $gateway->id)->value('api_pass');
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patch('/admin/payments-setup/tosla', [
                'allow_tosla' => 1,
                'tosla_client_id' => 'client-id',
                'tosla_api_user' => 'api-user',
                'tosla_api_pass' => 'brand-new-secret',
            ])
            ->assertRedirect();

        $fresh = $gateway->fresh();
        $rawAfter = DB::table('payment_gateways')->where('id', $fresh->id)->value('api_pass');

        $this->assertSame('brand-new-secret', $fresh->api_pass);
        $this->assertNotSame($rawBefore, $rawAfter);
        $this->assertNotSame('brand-new-secret', $rawAfter);
        $this->assertStringNotContainsString('brand-new-secret', (string) $rawAfter);
    }

    public function test_new_row_requires_api_pass(): void
    {
        DB::table('payment_gateways')->where('name', 'tosla')->delete();
        $admin = $this->admin();

        $this->actingAs($admin)
            ->from('/admin/payments-setup')
            ->patch('/admin/payments-setup/tosla', [
                'allow_tosla' => 1,
                'tosla_client_id' => 'client-id',
                'tosla_api_user' => 'api-user',
                'tosla_api_pass' => '',
            ])
            ->assertRedirect('/admin/payments-setup')
            ->assertSessionHasErrors('tosla_api_pass');

        $this->assertSame(0, PaymentGateway::where('name', 'tosla')->count());
    }

    public function test_validation_error_does_not_flash_api_pass(): void
    {
        $this->seedTosla('stored-secret');
        $admin = $this->admin();

        $response = $this->actingAs($admin)
            ->from('/admin/payments-setup')
            ->patch('/admin/payments-setup/tosla', [
                'allow_tosla' => 1,
                'tosla_client_id' => '',
                'tosla_api_user' => 'api-user',
                'tosla_api_pass' => 'should-not-flash',
            ]);

        $response->assertRedirect('/admin/payments-setup');
        $response->assertSessionHasErrors('tosla_client_id');
        $this->assertArrayNotHasKey('tosla_api_pass', session()->get('_old_input', []));
        $this->assertStringNotContainsString('should-not-flash', json_encode(session()->all()));
    }
}
