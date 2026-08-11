<?php

namespace Tests\Unit;

use App\Support\BioItemLink;
use App\Support\EmbedUrl;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class EmbedUrlTest extends TestCase
{
    public function test_youtube_watch_url_is_canonicalized(): void
    {
        $this->assertSame(
            'https://www.youtube.com/embed/dQw4w9wgXcQ',
            EmbedUrl::canonicalize('https://www.youtube.com/watch?v=dQw4w9wgXcQ', 'YouTube')
        );
    }

    public function test_youtube_share_and_embed_urls(): void
    {
        $this->assertSame(
            'https://www.youtube.com/embed/dQw4w9wgXcQ',
            EmbedUrl::canonicalize('https://youtu.be/dQw4w9wgXcQ', 'YouTube')
        );
        $this->assertSame(
            'https://www.youtube-nocookie.com/embed/dQw4w9wgXcQ',
            EmbedUrl::canonicalize('https://www.youtube-nocookie.com/embed/dQw4w9wgXcQ', 'YouTube')
        );
    }

    public function test_vimeo_and_spotify_canonicalize(): void
    {
        $this->assertSame(
            'https://player.vimeo.com/video/123456789',
            EmbedUrl::canonicalize('https://vimeo.com/123456789', 'Vimeo')
        );
        $this->assertSame(
            'https://open.spotify.com/embed/track/4uLU6hMCjMI75M1A2tKUQC',
            EmbedUrl::canonicalize('https://open.spotify.com/track/4uLU6hMCjMI75M1A2tKUQC', 'Spotify')
        );
    }

    public function test_soundcloud_becomes_player_url(): void
    {
        $canonical = EmbedUrl::canonicalize('https://soundcloud.com/artist/track', 'SoundCloud');
        $this->assertNotNull($canonical);
        $this->assertStringStartsWith('https://w.soundcloud.com/player/?url=', $canonical);
        $this->assertStringContainsString(rawurlencode('https://soundcloud.com/artist/track'), $canonical);
    }

    public function test_tiktok_keeps_https_tiktok_host(): void
    {
        $url = 'https://www.tiktok.com/@user/video/1234567890123456789';
        $this->assertSame($url, EmbedUrl::canonicalize($url, 'TikTok'));
    }

    public function test_evil_hosts_fail(): void
    {
        $this->assertNull(EmbedUrl::canonicalize('https://evil.example', 'YouTube'));
        $this->assertNull(EmbedUrl::canonicalize('https://youtube.com.evil.example/watch?v=dQw4w9wgXcQ', 'YouTube'));
        $this->assertNull(EmbedUrl::canonicalize('https://evil-youtube.com/embed/dQw4w9wgXcQ', 'YouTube'));
        $this->assertNull(EmbedUrl::canonicalize('data:text/html,alert(1)', 'YouTube'));
        $this->assertNull(EmbedUrl::canonicalize('javascript:alert(1)', 'YouTube'));
    }

    public function test_bio_item_link_rejects_unsafe_and_accepts_https(): void
    {
        $this->assertSame(
            'https://example.com',
            BioItemLink::normalize('https://example.com', 'Link', 'Link')
        );

        $this->expectException(ValidationException::class);
        BioItemLink::normalize('javascript:alert(1)', 'Link', 'Link');
    }

    public function test_bio_item_embed_rejects_evil_domain(): void
    {
        $this->expectException(ValidationException::class);
        BioItemLink::normalize('https://evil.example', 'Embed', 'YouTube');
    }

    public function test_image_link_may_be_empty(): void
    {
        $this->assertNull(BioItemLink::normalize(null, 'Image', 'Image'));
        $this->assertNull(BioItemLink::normalize('null', 'Image', 'Image'));
        $this->assertSame(
            'https://example.com',
            BioItemLink::normalize('https://example.com', 'Image', 'Image')
        );
    }
}
