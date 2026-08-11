<?php

namespace App\Support;

class EmbedUrl
{
    public const ICONS = ['YouTube', 'Vimeo', 'Spotify', 'SoundCloud', 'TikTok'];

    private const YOUTUBE_HOSTS = [
        'youtube.com',
        'www.youtube.com',
        'm.youtube.com',
        'music.youtube.com',
        'youtu.be',
        'www.youtu.be',
        'youtube-nocookie.com',
        'www.youtube-nocookie.com',
    ];

    private const VIMEO_HOSTS = [
        'vimeo.com',
        'www.vimeo.com',
        'player.vimeo.com',
    ];

    private const SPOTIFY_HOSTS = [
        'open.spotify.com',
    ];

    private const SOUNDCLOUD_HOSTS = [
        'soundcloud.com',
        'www.soundcloud.com',
        'on.soundcloud.com',
        'w.soundcloud.com',
    ];

    private const TIKTOK_HOSTS = [
        'tiktok.com',
        'www.tiktok.com',
        'm.tiktok.com',
        'vm.tiktok.com',
        'vt.tiktok.com',
    ];

    /**
     * Returns a canonical embed/cite URL, or null if the input is not a supported provider URL.
     */
    public static function canonicalize(?string $url, ?string $icon = null): ?string
    {
        $parsed = self::parseHttp($url);
        if ($parsed === null) {
            return null;
        }

        $host = $parsed['host'];
        $provider = self::providerFromIcon($icon) ?? self::providerFromHost($host);
        if ($provider === null) {
            return null;
        }

        if (!self::hostAllowed($host, $provider)) {
            return null;
        }

        return match ($provider) {
            'youtube' => self::youtube($parsed),
            'vimeo' => self::vimeo($parsed),
            'spotify' => self::spotify($parsed),
            'soundcloud' => self::soundcloud($parsed),
            'tiktok' => self::tiktok($parsed),
            default => null,
        };
    }

    public static function isTikTok(?string $icon, ?string $url = null): bool
    {
        if (self::providerFromIcon($icon) === 'tiktok') {
            return true;
        }
        if ($url === null) {
            return false;
        }
        $parsed = self::parseHttp($url);
        return $parsed !== null && self::hostAllowed($parsed['host'], 'tiktok');
    }

    public static function providerFromIcon(?string $icon): ?string
    {
        return match ($icon) {
            'YouTube' => 'youtube',
            'Vimeo' => 'vimeo',
            'Spotify' => 'spotify',
            'SoundCloud' => 'soundcloud',
            'TikTok' => 'tiktok',
            default => null,
        };
    }

    /**
     * @return array{scheme:string,host:string,path:string,query:string,url:string}|null
     */
    private static function parseHttp(?string $url): ?array
    {
        $canonical = SafeUrl::canonicalize($url, ['http', 'https']);
        if ($canonical === null) {
            return null;
        }

        $parts = parse_url($canonical);
        if (!is_array($parts) || empty($parts['host'])) {
            return null;
        }

        if (isset($parts['port']) || !empty($parts['user']) || isset($parts['pass'])) {
            return null;
        }

        $host = strtolower(rtrim((string) $parts['host'], '.'));

        return [
            'scheme' => 'https',
            'host' => $host,
            'path' => $parts['path'] ?? '',
            'query' => $parts['query'] ?? '',
            'url' => $canonical,
        ];
    }

    private static function providerFromHost(string $host): ?string
    {
        if (in_array($host, self::YOUTUBE_HOSTS, true)) {
            return 'youtube';
        }
        if (in_array($host, self::VIMEO_HOSTS, true)) {
            return 'vimeo';
        }
        if (in_array($host, self::SPOTIFY_HOSTS, true)) {
            return 'spotify';
        }
        if (in_array($host, self::SOUNDCLOUD_HOSTS, true)) {
            return 'soundcloud';
        }
        if (in_array($host, self::TIKTOK_HOSTS, true)) {
            return 'tiktok';
        }

        return null;
    }

    private static function hostAllowed(string $host, string $provider): bool
    {
        $map = [
            'youtube' => self::YOUTUBE_HOSTS,
            'vimeo' => self::VIMEO_HOSTS,
            'spotify' => self::SPOTIFY_HOSTS,
            'soundcloud' => self::SOUNDCLOUD_HOSTS,
            'tiktok' => self::TIKTOK_HOSTS,
        ];

        return isset($map[$provider]) && in_array($host, $map[$provider], true);
    }

    /**
     * @param  array{host:string,path:string,query:string}  $parsed
     */
    private static function youtube(array $parsed): ?string
    {
        $id = null;
        $path = trim($parsed['path'], '/');
        $segments = $path === '' ? [] : explode('/', $path);

        if (in_array($parsed['host'], ['youtu.be', 'www.youtu.be'], true)) {
            $id = $segments[0] ?? null;
        } else {
            parse_str($parsed['query'], $query);
            if (!empty($query['v']) && is_string($query['v'])) {
                $id = $query['v'];
            } elseif (isset($segments[0], $segments[1]) && in_array($segments[0], ['embed', 'shorts', 'live', 'v'], true)) {
                $id = $segments[1];
            }
        }

        $id = is_string($id) ? explode('?', $id)[0] : null;
        if ($id === null || !preg_match('/^[A-Za-z0-9_-]{11}$/', $id)) {
            return null;
        }

        $nocookie = in_array($parsed['host'], ['youtube-nocookie.com', 'www.youtube-nocookie.com'], true);
        $embedHost = $nocookie ? 'www.youtube-nocookie.com' : 'www.youtube.com';

        return 'https://' . $embedHost . '/embed/' . $id;
    }

    /**
     * @param  array{host:string,path:string}  $parsed
     */
    private static function vimeo(array $parsed): ?string
    {
        $segments = array_values(array_filter(explode('/', trim($parsed['path'], '/')), fn ($s) => $s !== ''));
        $id = null;
        if ($parsed['host'] === 'player.vimeo.com' && ($segments[0] ?? '') === 'video') {
            $id = $segments[1] ?? null;
        } else {
            $id = $segments[0] ?? null;
        }

        if ($id === null || !preg_match('/^\d{6,12}$/', (string) $id)) {
            return null;
        }

        return 'https://player.vimeo.com/video/' . $id;
    }

    /**
     * @param  array{path:string}  $parsed
     */
    private static function spotify(array $parsed): ?string
    {
        $segments = array_values(array_filter(explode('/', trim($parsed['path'], '/')), fn ($s) => $s !== ''));
        if (($segments[0] ?? '') === 'embed') {
            array_shift($segments);
        }

        $type = $segments[0] ?? null;
        $id = $segments[1] ?? null;
        $id = is_string($id) ? explode('?', $id)[0] : null;

        if (!in_array($type, ['track', 'album', 'playlist', 'episode', 'show', 'artist'], true)) {
            return null;
        }
        if ($id === null || !preg_match('/^[A-Za-z0-9]{10,32}$/', $id)) {
            return null;
        }

        return 'https://open.spotify.com/embed/' . $type . '/' . $id;
    }

    /**
     * @param  array{host:string,path:string,query:string,url:string}  $parsed
     */
    private static function soundcloud(array $parsed): ?string
    {
        $trackUrl = $parsed['url'];

        if ($parsed['host'] === 'w.soundcloud.com') {
            parse_str($parsed['query'], $query);
            $inner = $query['url'] ?? null;
            if (!is_string($inner) || $inner === '') {
                return null;
            }
            $innerParsed = self::parseHttp($inner);
            if ($innerParsed === null || !self::hostAllowed($innerParsed['host'], 'soundcloud') || $innerParsed['host'] === 'w.soundcloud.com') {
                return null;
            }
            $trackUrl = $innerParsed['url'];
        } else {
            $https = SafeUrl::canonicalize($trackUrl, ['https']);
            $trackUrl = $https ?? ('https://' . $parsed['host'] . ($parsed['path'] !== '' ? $parsed['path'] : ''));
        }

        return 'https://w.soundcloud.com/player/?url=' . rawurlencode($trackUrl)
            . '&color=%23ff5500&auto_play=false&hide_related=false&show_comments=true&show_user=true&show_reposts=false&show_teaser=true&visual=true';
    }

    /**
     * @param  array{host:string,path:string,url:string}  $parsed
     */
    private static function tiktok(array $parsed): ?string
    {
        $path = $parsed['path'];
        if ($path === '' || $path === '/') {
            return null;
        }

        $https = SafeUrl::canonicalize($parsed['url'], ['https']);
        if ($https === null) {
            $https = 'https://' . $parsed['host'] . $path;
        }

        return $https;
    }
}
