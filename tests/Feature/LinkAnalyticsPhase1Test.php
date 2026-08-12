<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\ShetabitVisit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LinkAnalyticsPhase1Test extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function userWithRole(string $role = 'PREMIUM'): User
    {
        Role::findOrCreate($role);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole($role);

        return $user;
    }

    private function makeLink(User $user, string $type = 'biolink', ?string $urlName = null): Link
    {
        $link = new Link();
        $link->user_id = $user->id;
        $link->link_name = $type === 'shortlink' ? 'Short Demo' : 'Bio Demo';
        $link->link_type = $type;
        $link->url_name = $urlName ?? ($type.'-'.uniqid());
        $link->external_url = $type === 'shortlink' ? 'https://example.com' : null;
        $link->save();

        return $link->fresh();
    }

    private function insertVisit(Link $link, array $overrides = []): void
    {
        $now = Carbon::now();

        DB::table('shetabit_visits')->insert(array_merge([
            'link_id' => $link->id,
            'method' => 'GET',
            'request' => json_encode(['secret' => 'should-not-leak']),
            'url' => 'https://example.test/'.$link->url_name,
            'referer' => null,
            'languages' => json_encode(['tr-TR', 'tr', 'en']),
            'useragent' => 'PHPUnit',
            'headers' => json_encode(['Cookie' => 'secret']),
            'device' => 'Desktop',
            'platform' => 'macOS',
            'browser' => 'Chrome',
            'ip' => json_encode([
                'ip' => '8.8.8.8',
                'countryName' => 'United States',
                'countryCode' => 'US',
                'cityName' => 'Mountain View',
            ]),
            'created_at' => $now,
            'updated_at' => $now,
        ], $overrides));
    }

    public function test_owner_can_view_bio_link_analytics(): void
    {
        $owner = $this->userWithRole();
        $link = $this->makeLink($owner, 'biolink');
        $this->insertVisit($link);

        $this->actingAs($owner)
            ->get('/link/analytics/'.$link->id)
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('LinkAnalytics')
                ->where('analytics.link.id', $link->id)
                ->where('analytics.link.link_type', 'biolink')
                ->where('analytics.link.type_label', 'Bio Link')
                ->where('analytics.range.key', '30d')
                ->where('analytics.overview.selected_period_total', 1)
                ->missing('analytics.0')
                ->missing('analytics.request')
                ->missing('analytics.headers')
            );
    }

    public function test_owner_can_view_short_link_analytics(): void
    {
        $owner = $this->userWithRole();
        $link = $this->makeLink($owner, 'shortlink');
        $this->insertVisit($link);

        $this->actingAs($owner)
            ->get('/link/analytics/'.$link->id)
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('LinkAnalytics')
                ->where('analytics.link.link_type', 'shortlink')
                ->where('analytics.link.type_label', 'Kısa Link')
                ->where('analytics.overview.total_views', 1)
            );
    }

    public function test_other_user_cannot_view_link_analytics(): void
    {
        $owner = $this->userWithRole();
        $other = $this->userWithRole();
        $link = $this->makeLink($owner, 'biolink');

        $this->actingAs($other)
            ->get('/link/analytics/'.$link->id)
            ->assertNotFound();
    }

    public function test_bio_analytics_is_not_404_for_owner(): void
    {
        $owner = $this->userWithRole();
        $link = $this->makeLink($owner, 'biolink');

        $this->actingAs($owner)
            ->get('/link/analytics/'.$link->id)
            ->assertOk();
    }

    public function test_default_range_is_30d(): void
    {
        $owner = $this->userWithRole();
        $link = $this->makeLink($owner);

        $this->actingAs($owner)
            ->get('/link/analytics/'.$link->id)
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('analytics.range.key', '30d')
            );
    }

    public function test_7d_range_counts_only_recent_visits(): void
    {
        $owner = $this->userWithRole();
        $link = $this->makeLink($owner);

        $this->insertVisit($link, [
            'created_at' => Carbon::now()->subDays(2),
            'updated_at' => Carbon::now()->subDays(2),
        ]);
        $this->insertVisit($link, [
            'created_at' => Carbon::now()->subDays(20),
            'updated_at' => Carbon::now()->subDays(20),
            'device' => 'Mobile',
        ]);

        $this->actingAs($owner)
            ->get('/link/analytics/'.$link->id.'?range=7d')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('analytics.range.key', '7d')
                ->where('analytics.overview.selected_period_total', 1)
                ->where('analytics.overview.total_views', 2)
            );
    }

    public function test_custom_range_validation_rejects_invalid_order(): void
    {
        $owner = $this->userWithRole();
        $link = $this->makeLink($owner);

        $this->actingAs($owner)
            ->get('/link/analytics/'.$link->id.'?range=custom&from=2026-08-10&to=2026-08-01')
            ->assertSessionHasErrors('from');
    }

    public function test_custom_range_validation_rejects_missing_dates(): void
    {
        $owner = $this->userWithRole();
        $link = $this->makeLink($owner);

        $this->actingAs($owner)
            ->get('/link/analytics/'.$link->id.'?range=custom')
            ->assertSessionHasErrors('from');
    }

    public function test_total_views_and_direct_referrer_and_malformed_geo(): void
    {
        $owner = $this->userWithRole();
        $link = $this->makeLink($owner);

        $this->insertVisit($link, [
            'referer' => null,
            'ip' => 'not-json',
        ]);
        $this->insertVisit($link, [
            'referer' => 'https://google.com/search?q=test',
            'ip' => json_encode(['countryName' => 'Turkey', 'countryCode' => 'TR']),
        ]);
        $this->insertVisit($link, [
            'referer' => '',
            'ip' => null,
            'device' => null,
            'browser' => null,
            'platform' => null,
            'languages' => null,
        ]);

        $response = $this->actingAs($owner)
            ->get('/link/analytics/'.$link->id.'?range=30d')
            ->assertOk();

        $response->assertInertia(fn (Assert $page) => $page
            ->component('LinkAnalytics')
            ->where('analytics.overview.selected_period_total', 3)
            ->where('analytics.overview.total_views', 3)
            ->has('analytics.referrers')
            ->has('analytics.countries')
            ->has('analytics.devices')
            ->missing('analytics.unique_visitors')
            ->missing('analytics.ctr')
        );

        $analytics = $response->viewData('page')['props']['analytics'];
        $referrerLabels = collect($analytics['referrers'])->pluck('label')->all();
        $countryLabels = collect($analytics['countries'])->pluck('label')->all();

        $this->assertContains('Doğrudan', $referrerLabels);
        $this->assertContains('google.com', $referrerLabels);
        $this->assertContains('Bilinmiyor', $countryLabels);
        $this->assertContains('Turkey', $countryLabels);
        $this->assertArrayNotHasKey('request', $analytics);
        $this->assertArrayNotHasKey('headers', $analytics);
        $this->assertArrayNotHasKey('useragent', $analytics);
    }

    public function test_response_does_not_include_raw_visit_rows(): void
    {
        $owner = $this->userWithRole();
        $link = $this->makeLink($owner);
        $this->insertVisit($link);

        $response = $this->actingAs($owner)
            ->get('/link/analytics/'.$link->id)
            ->assertOk();

        $page = $response->viewData('page');
        $analytics = $page['props']['analytics'] ?? [];

        $this->assertIsArray($analytics);
        $this->assertArrayHasKey('overview', $analytics);
        $this->assertArrayHasKey('timeseries', $analytics);
        $this->assertArrayNotHasKey('0', $analytics);
        $this->assertArrayNotHasKey('request', $analytics);
        $this->assertArrayNotHasKey('headers', $analytics);
        $this->assertArrayNotHasKey('useragent', $analytics);

        $encoded = json_encode($analytics);
        $this->assertStringNotContainsString('should-not-leak', $encoded);
        $this->assertStringNotContainsString('"Cookie"', $encoded);
    }

    public function test_dashboard_counts_only_owned_link_visits(): void
    {
        $owner = $this->userWithRole();
        $other = $this->userWithRole();

        $owned = $this->makeLink($owner);
        $foreign = $this->makeLink($other);

        $this->insertVisit($owned);
        $this->insertVisit($owned);
        $this->insertVisit($foreign);

        // Old buggy shape: visitor_id pointing at owner must NOT inflate dashboard.
        DB::table('shetabit_visits')->insert([
            'link_id' => $foreign->id,
            'method' => 'GET',
            'url' => 'https://example.test/x',
            'device' => 'Desktop',
            'platform' => 'Windows',
            'browser' => 'Firefox',
            'ip' => '1.1.1.1',
            'visitor_id' => $owner->id,
            'visitor_type' => User::class,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($owner)
            ->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard')
                ->where('analytics', 2)
                ->where('links', 1)
            );
    }

    public function test_other_users_visits_do_not_appear_on_dashboard(): void
    {
        $owner = $this->userWithRole();
        $other = $this->userWithRole();
        $foreign = $this->makeLink($other);
        $this->insertVisit($foreign);
        $this->insertVisit($foreign);

        $this->actingAs($owner)
            ->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('analytics', 0)
            );
    }

    public function test_analytics_indexes_exist_or_migration_is_present(): void
    {
        $migrationPath = database_path('migrations/2026_08_13_000001_add_analytics_indexes_to_shetabit_visits_table.php');
        $this->assertFileExists($migrationPath);

        if (! Schema::hasTable('shetabit_visits')) {
            $this->markTestSkipped('shetabit_visits table missing');
        }

        // Prefer asserting after migrate; if indexes already applied, check information_schema.
        $database = Schema::getConnection()->getDatabaseName();
        $indexes = collect(DB::select(
            'SELECT index_name FROM information_schema.statistics WHERE table_schema = ? AND table_name = ?',
            [$database, 'shetabit_visits']
        ))->pluck('index_name')->map(fn ($n) => strtolower((string) $n))->all();

        // Migration may not have been run yet in this environment; file presence is required.
        // When indexes exist, names must match.
        if (in_array('shetabit_visits_link_id_created_at_index', $indexes, true)
            || in_array('shetabit_visits_created_at_index', $indexes, true)) {
            $this->assertContains('shetabit_visits_link_id_created_at_index', $indexes);
            $this->assertContains('shetabit_visits_created_at_index', $indexes);
        } else {
            $this->assertTrue(true);
        }
    }

    public function test_super_admin_can_view_any_link_analytics(): void
    {
        $admin = $this->userWithRole('SUPER-ADMIN');
        $owner = $this->userWithRole();
        $link = $this->makeLink($owner, 'biolink');
        $this->insertVisit($link);

        $this->actingAs($admin)
            ->get('/link/analytics/'.$link->id)
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('analytics.link.id', $link->id)
            );
    }
}
