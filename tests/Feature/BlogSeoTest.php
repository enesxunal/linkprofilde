<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogSeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_blog_index_lists_published_guides(): void
    {
        $response = $this->get('/blog');

        $response->assertOk();
        $response->assertSee('Bio link, QR kod, kısa link ve dijital kartvizit', false);
        $response->assertSee('Instagram Bio Link Nasıl Oluşturulur?', false);
    }

    public function test_blog_post_has_canonical_and_article_schema(): void
    {
        $post = BlogPost::published()->firstOrFail();
        $response = $this->get('/blog/' . $post->slug);

        $response->assertOk();
        $response->assertSee('<link rel="canonical" href="' . url('/blog/' . $post->slug) . '">', false);
        $response->assertSee('BlogPosting', false);
        $response->assertSee($post->title, false);
    }

    public function test_sitemap_contains_blog_and_published_posts(): void
    {
        $post = BlogPost::published()->firstOrFail();
        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee(url('/blog'), false);
        $response->assertSee(url('/blog/' . $post->slug), false);
    }

    public function test_llms_txt_explains_product_and_links_guides(): void
    {
        $response = $this->get('/llms.txt');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $response->assertSee('LinkProfilde; bio link, dijital profil, kısa link, QR kod', false);
        $response->assertSee(url('/blog'), false);
    }
}
