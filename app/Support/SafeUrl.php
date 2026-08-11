<?php

namespace App\Support;

class SafeUrl
{
    private const BLOCKED_SCHEMES = [
        'javascript',
        'data',
        'blob',
        'vbscript',
        'file',
        'about',
    ];

    /**
     * Canonical http/https URL, or null if unsafe/malformed.
     *
     * @param  list<string>  $schemes
     */
    public static function canonicalize(?string $url, array $schemes = ['http', 'https']): ?string
    {
        if ($url === null) {
            return null;
        }

        $url = trim($url);
        if ($url === '' || strcasecmp($url, 'null') === 0) {
            return null;
        }

        if (preg_match('/[\x00-\x1F\x7F]/', $url) || preg_match('/\s/u', $url)) {
            return null;
        }

        $decoded = self::decode($url);
        if ($decoded === null) {
            return null;
        }

        if (str_starts_with($decoded, '//') || str_starts_with($decoded, '\\\\')) {
            return null;
        }

        if (!preg_match('/^([a-z][a-z0-9+.-]*):/i', $decoded, $match)) {
            return null;
        }

        $scheme = strtolower($match[1]);
        $allowed = [];
        foreach ($schemes as $item) {
            $allowed[strtolower($item)] = true;
        }

        if (in_array($scheme, self::BLOCKED_SCHEMES, true) || !isset($allowed[$scheme])) {
            return null;
        }

        $parts = parse_url($decoded);
        if (!is_array($parts) || empty($parts['scheme'])) {
            return null;
        }

        if (strtolower((string) $parts['scheme']) !== $scheme) {
            return null;
        }

        if ($scheme === 'mailto') {
            return self::canonicalizeMailto($parts);
        }

        if ($scheme === 'tel') {
            return self::canonicalizeTel($decoded);
        }

        if (!empty($parts['user']) || isset($parts['pass'])) {
            return null;
        }

        $host = $parts['host'] ?? '';
        if ($host === '' || !self::isSafeHost($host)) {
            return null;
        }

        if (isset($parts['port'])) {
            $port = (int) $parts['port'];
            if ($port < 1 || $port > 65535) {
                return null;
            }
        }

        $rebuild = $scheme . '://' . strtolower($host);
        if (isset($parts['port'])) {
            $rebuild .= ':' . (int) $parts['port'];
        }
        $rebuild .= $parts['path'] ?? '';
        if (isset($parts['query'])) {
            $rebuild .= '?' . $parts['query'];
        }
        if (isset($parts['fragment'])) {
            $rebuild .= '#' . $parts['fragment'];
        }

        if (filter_var($rebuild, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        return $rebuild;
    }

    /**
     * Render-safe href for home/footer/custom-page links.
     * Empty string means "no href". Null means invalid (caller should not render).
     */
    public static function href(?string $url): ?string
    {
        if ($url === null) {
            return '';
        }

        $url = trim($url);
        if ($url === '' || strcasecmp($url, 'null') === 0) {
            return '';
        }

        if (preg_match('/^#[A-Za-z0-9_-]*$/', $url)) {
            return $url;
        }

        return self::canonicalize($url, ['http', 'https', 'mailto', 'tel']);
    }

    public static function isSafe(?string $url, array $schemes = ['http', 'https']): bool
    {
        return self::canonicalize($url, $schemes) !== null;
    }

    private static function decode(string $url): ?string
    {
        $current = $url;
        for ($i = 0; $i < 5; $i++) {
            $next = rawurldecode($current);
            $next = html_entity_decode($next, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if ($next === $current) {
                break;
            }
            $current = $next;
        }

        if (preg_match('/[\x00-\x1F\x7F]/', $current) || preg_match('/\s/u', $current)) {
            return null;
        }

        return $current;
    }

    private static function isSafeHost(string $host): bool
    {
        $host = rtrim($host, '.');
        if ($host === '' || str_contains($host, '..') || str_contains($host, '/') || str_contains($host, '\\')) {
            return false;
        }

        $ascii = $host;
        if (function_exists('idn_to_ascii')) {
            $converted = idn_to_ascii($host, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
            if ($converted !== false) {
                $ascii = $converted;
            }
        }

        $ascii = strtolower($ascii);

        if ($ascii === 'localhost') {
            return true;
        }

        if (filter_var($ascii, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
            return true;
        }

        return (bool) preg_match('/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,}$/', $ascii);
    }

    private static function canonicalizeMailto(array $parts): ?string
    {
        $email = $parts['path'] ?? '';
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }
        if (isset($parts['query']) || isset($parts['fragment'])) {
            return null;
        }

        return 'mailto:' . $email;
    }

    private static function canonicalizeTel(string $url): ?string
    {
        $number = substr($url, 4);
        $digits = preg_replace('/[^\d+]/', '', $number) ?? '';
        if (!preg_match('/^\+?\d{7,20}$/', $digits)) {
            return null;
        }

        return 'tel:' . $digits;
    }
}
