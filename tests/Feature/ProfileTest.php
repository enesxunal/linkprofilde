<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        Role::findOrCreate('SUPER-ADMIN');

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole('SUPER-ADMIN');

        return $user;
    }

    public function test_settings_page_is_displayed(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->get('/settings')
            ->assertOk();
    }

    public function test_profile_name_and_phone_can_be_updated(): void
    {
        $user = $this->makeUser();

        $response = $this
            ->actingAs($user)
            ->post('/settings/profile', [
                'name' => 'Test User Updated',
                'phone' => '+90 555 111 2233',
            ]);

        $response->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertSame('Test User Updated', $user->name);
        $this->assertSame('+90 555 111 2233', $user->phone);
    }

    public function test_profile_update_does_not_change_email_verification_status(): void
    {
        $user = $this->makeUser();
        $verifiedAt = $user->email_verified_at;

        $this->actingAs($user)
            ->post('/settings/profile', [
                'name' => 'Same Email User',
                'phone' => '',
            ])
            ->assertSessionHasNoErrors();

        $this->assertTrue($user->fresh()->email_verified_at->equalTo($verifiedAt));
    }

    public function test_profile_name_is_required(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->from('/settings')
            ->post('/settings/profile', [
                'name' => '',
                'phone' => '',
            ])
            ->assertSessionHasErrors('name')
            ->assertRedirect('/settings');
    }

    public function test_profile_phone_length_is_validated(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->from('/settings')
            ->post('/settings/profile', [
                'name' => 'Test User',
                'phone' => str_repeat('1', 21),
            ])
            ->assertSessionHasErrors('phone')
            ->assertRedirect('/settings');
    }
}
