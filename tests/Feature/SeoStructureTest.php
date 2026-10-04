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

    public function test_public_profile_seo_is_present_in_initial_html(): void
    {
        $user = User::factory()->create();

        $link = new Link();
        $link->user_id = $user->id;
        $link->link_name = 'SEO Test Profile';
        $link->link_type = 'biolink';
        $link->url_name = 'seo-test-profile';
        $link->short_bio = 'SEO test profili için yeterli uzunlukta açıklama metni.';
        $link->save();

        $this->get('/' . $link->url_name)
            ->assertOk()
            ->assertSee('<title inertia>SEO Test Profile | Bağlantılar ve İletişim | LinkProfilde</title>', false)
            ->assertSee('<meta name="description" content="SEO test profili için yeterli uzunlukta açıklama metni.">', false)
            ->assertSee('<link rel="canonical" href="' . url('/' . $link->url_name) . '">', false)
            ->assertSee('ProfilePage', false);
    }

    public function test_product_seo_landing_pages_have_unique_canonical_and_schema(): void
    {
        $pages = [
            'bio-link' => 'Tüm bağlantılarını tek bir bio linkte topla',
            'dijital-kartvizit' => 'Güncellenebilir ve paylaşılabilir dijital kartvizit oluştur',
            'qr-kod' => 'Paylaşılabilir ve yönetilebilir QR kodlar oluştur',
            'kisa-link' => 'Uzun bağlantılarını kısa, sade ve ölçülebilir hale getir',
        ];

        foreach ($pages as $slug => $h1) {
            $this->get('/' . $slug)
                ->assertOk()
                ->assertSee($h1)
                ->assertSee('<link rel="canonical" href="' . url('/' . $slug) . '">', false)
                ->assertSee('FAQPage')
                ->assertSee('SoftwareApplication');
        }
    }

    public function test_product_seo_landing_pages_are_in_sitemap(): void
    {
        $response = $this->get('/sitemap.xml')->assertOk();

        foreach (['bio-link', 'dijital-kartvizit', 'qr-kod', 'kisa-link'] as $slug) {
            $response->assertSee(url('/' . $slug), false);
        }
    }
}
