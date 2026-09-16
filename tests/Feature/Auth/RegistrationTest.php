<?php

namespace Tests\Feature\Auth;

use App\Models\PricingPlan;
use App\Models\Theme;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    private function seedRegistrationDependencies(): void
    {
        Role::findOrCreate('BASIC');

        PricingPlan::firstOrCreate(
            ['name' => 'BASIC'],
            [
                'description' => 'Ücretsiz plan',
                'status' => 'active',
                'monthly_price' => 0,
                'yearly_price' => 0,
                'currency' => 'TRY',
                'biolinks' => 1,
                'biolink_blocks' => 10,
                'shortlinks' => 5,
                'projects' => 1,
                'qrcodes' => 1,
                'themes' => 1,
                'custom_theme' => 0,
                'support' => 0,
            ]
        );

        Theme::firstOrCreate(
            ['name' => 'Default'],
            [
                'background' => '#ffffff',
                'text_color' => '#101828',
                'button_style' => 'rounded',
                'font_family' => 'Inter',
                'theme_demo' => 'favicon.ico',
                'bg_image' => null,
                'type' => 'Free',
            ]
        );
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $this->seedRegistrationDependencies();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'url_name' => 'testuserprofile',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
        $this->assertDatabaseHas('links', [
            'url_name' => 'testuserprofile',
            'link_type' => 'biolink',
        ]);
    }
}
