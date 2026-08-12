<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminUsersSearchTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        Role::findOrCreate('SUPER-ADMIN');

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole('SUPER-ADMIN');

        return $user;
    }

    public function test_search_returns_matching_user_id_not_list_index(): void
    {
        $admin = $this->admin();

        $ahmet = User::factory()->create([
            'name' => 'Ahmet',
            'email' => 'ahmet-search-fixture@example.com',
        ]);
        $mehmet = User::factory()->create([
            'name' => 'Mehmet',
            'email' => 'mehmet-search-fixture@example.com',
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/users/search?value=Mehmet&page=1&per_page=10');

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id')->all();

        $this->assertContains($mehmet->id, $ids);
        $this->assertNotContains($ahmet->id, $ids);
        $this->assertNotContains(0, $ids);
        $this->assertSame($mehmet->id, $response->json('data.0.id'));
    }

    public function test_search_pagination_links_keep_value_and_per_page(): void
    {
        $admin = $this->admin();

        User::factory()->create([
            'name' => 'Ahmet',
            'email' => 'ahmet-page-fixture@example.com',
        ]);

        for ($i = 0; $i < 15; $i++) {
            User::factory()->create([
                'name' => 'Mehmet '.$i,
                'email' => "mehmet-page-fixture-{$i}@example.com",
            ]);
        }

        $response = $this->actingAs($admin)
            ->get('/admin/users/search?value=Mehmet&page=1&per_page=10');

        $response->assertOk();
        $next = (string) $response->json('next_page_url');
        $query = [];
        parse_str(parse_url($next, PHP_URL_QUERY) ?: '', $query);

        $this->assertSame('Mehmet', $query['value'] ?? null);
        $this->assertSame('2', (string) ($query['page'] ?? ''));
        $this->assertSame('10', (string) ($query['per_page'] ?? ''));

        $page2 = $this->actingAs($admin)
            ->get('/admin/users/search?value=Mehmet&page=2&per_page=10');

        $page2->assertOk();
        $names = collect($page2->json('data'))->pluck('name')->implode(' ');
        $this->assertStringContainsString('Mehmet', $names);
        $this->assertStringNotContainsString('Ahmet', $names);
    }
}
