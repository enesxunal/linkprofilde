<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\SmtpSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SmtpSettingsTest extends TestCase
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

    private function seedSmtp(string $password = 'stored-secret'): SmtpSetting
    {
        $smtp = new SmtpSetting();
        $smtp->host = 'smtp.example.com';
        $smtp->port = '587';
        $smtp->username = 'smtp-user';
        $smtp->password = $password;
        $smtp->encryption = 'tls';
        $smtp->sender_email = 'noreply@example.com';
        $smtp->sender_name = 'Example';
        $smtp->save();

        return $smtp->fresh();
    }

    public function test_admin_page_props_do_not_include_password(): void
    {
        DB::table('smtp_settings')->delete();
        $this->seedSmtp('should-not-leak');
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get('/admin/app-settings')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/AppSettings')
                ->has('smtp')
                ->missing('smtp.password')
                ->where('smtp.host', 'smtp.example.com')
                ->where('smtp.username', 'smtp-user')
            );
    }

    public function test_blank_password_update_preserves_existing_secret(): void
    {
        DB::table('smtp_settings')->delete();
        $smtp = $this->seedSmtp('keep-original');
        $rawBefore = DB::table('smtp_settings')->where('id', $smtp->id)->value('password');
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patch('/admin/app-settings/smtp/update', [
                'host' => 'smtp.updated.example',
                'port' => '587',
                'username' => 'smtp-user',
                'password' => '',
                'encryption' => 'tls',
                'from_address' => 'noreply@example.com',
                'from_name' => 'Example',
                'admin_email' => 'admin@example.com',
            ])
            ->assertRedirect();

        $fresh = $smtp->fresh();
        $rawAfter = DB::table('smtp_settings')->where('id', $fresh->id)->value('password');

        $this->assertSame($rawBefore, $rawAfter);
        $this->assertSame('keep-original', $fresh->password);
        $this->assertSame('smtp.updated.example', $fresh->host);
        $this->assertNotSame('keep-original', $rawAfter);
    }

    public function test_new_password_update_replaces_secret(): void
    {
        DB::table('smtp_settings')->delete();
        $smtp = $this->seedSmtp('old-secret');
        $rawBefore = DB::table('smtp_settings')->where('id', $smtp->id)->value('password');
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patch('/admin/app-settings/smtp/update', [
                'host' => 'smtp.example.com',
                'port' => '587',
                'username' => 'smtp-user',
                'password' => 'brand-new-secret',
                'encryption' => 'tls',
                'from_address' => 'noreply@example.com',
                'from_name' => 'Example',
                'admin_email' => 'admin@example.com',
            ])
            ->assertRedirect();

        $fresh = $smtp->fresh();
        $rawAfter = DB::table('smtp_settings')->where('id', $fresh->id)->value('password');

        $this->assertSame('brand-new-secret', $fresh->password);
        $this->assertNotSame($rawBefore, $rawAfter);
        $this->assertNotSame('brand-new-secret', $rawAfter);
        $this->assertStringNotContainsString('brand-new-secret', (string) $rawAfter);
    }

    public function test_new_row_requires_password(): void
    {
        DB::table('smtp_settings')->delete();
        $admin = $this->admin();

        $this->actingAs($admin)
            ->from('/admin/app-settings')
            ->patch('/admin/app-settings/smtp/update', [
                'host' => 'smtp.new.example',
                'port' => '587',
                'username' => 'new-user',
                'password' => '',
                'encryption' => 'tls',
                'from_address' => 'new@example.com',
                'from_name' => 'New',
                'admin_email' => 'admin@example.com',
            ])
            ->assertRedirect('/admin/app-settings')
            ->assertSessionHasErrors('password');

        $this->assertSame(0, SmtpSetting::count());
    }
}
