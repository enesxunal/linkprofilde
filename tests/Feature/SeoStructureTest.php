<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoStructureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_auth_pages_expose_noindex_and_are_not_blocked_in_robots(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex,nofollow">', false);

        $robots = file_get_contents(public_path('robots.txt'));

        $this->assertStringNotContainsString('Disallow: /login', $robots);
        $this->assertStringNotContainsString('Disallow: /register', $robots);
        $this->assertStringNotContainsString('Disallow: /forgot-password', $robots);
        $this->assertStringNotContainsString('Disallow: /verify-email', $robots);
    }

    public function test_blog_pagination_uses_self_referencing_canonical(): void
    {
        $this->get('/blog?page=2')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="' . url('/blog') . '?page=2">', false);
    }

    public function test_sitemap_excludes_empty_biolinks_and_includes_meaningful_profiles(): void
    {
        $user = User::factory()->create();

        $empty = new Link();
        $empty->user_id = $user->id;
        $empty->link_name = 'Empty Profile';
        $empty->link_type = 'biolink';
        $empty->url_name = 'empty-profile-seo-test';
        $empty->save();

        $meaningful = new Link();
        $meaningful->user_id = $user->id;
        $meaningful->link_name = 'Meaningful Profile';
        $meaningful->link_type = 'biolink';
        $meaningful->url_name = 'meaningful-profile-seo-test';
        $meaningful->short_bio = 'Bu profil arama motorlari icin yeterli aciklama icerir.';
        $meaningful->save();

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertDontSee(url('/' . $empty->url_name), false);
        $response->assertSee(url('/' . $meaningful->url_name), false);
    }

    public function test_empty_public_profile_sends_x_robots_noindex_header(): void
    {
        $user = User::factory()->create();

        $link = new Link();
        $link->user_id = $user->id;
        $link->link_name = 'Empty Profile';
        $link->link_type = 'biolink';
        $link->url_name = 'empty-profile-header-test';
        $link->save();

        $this->get('/' . $link->url_name)
            ->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, follow');
    }
}
