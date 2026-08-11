<?php

namespace App\Support;

use HTMLPurifier;
use HTMLPurifier_Config;

class PageHtml
{
    public const ALLOWED = 'p[class],br,strong,em,u,s,h1[class],h2[class],h3[class],h4[class],h5[class],h6[class],ul[class],ol[class],li[class],blockquote[class],code[class],pre[class],span[class],div[class],table,thead,tbody,tr,td,th,a[href|title|target|rel]';

    public static function sanitize(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $purified = self::purifier()->purify($html);

        return self::normalizeBlankTargets($purified);
    }

    public static function config(): HTMLPurifier_Config
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Core.Encoding', 'UTF-8');
        $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
        $config->set('HTML.Allowed', self::ALLOWED);
        $config->set('HTML.ForbiddenElements', [
            'script', 'iframe', 'object', 'embed', 'form', 'input', 'button',
            'style', 'svg', 'math', 'link', 'meta', 'base', 'applet', 'video',
            'audio', 'source', 'track', 'canvas',
        ]);
        $config->set('URI.AllowedSchemes', [
            'http' => true,
            'https' => true,
            'mailto' => true,
        ]);
        $config->set('URI.DisableExternalResources', true);
        $config->set('Attr.AllowedFrameTargets', ['_blank', '_self']);
        $config->set('Attr.AllowedRel', ['noopener' => true, 'noreferrer' => true, 'nofollow' => true]);
        $config->set('Attr.EnableID', false);
        $config->set('CSS.AllowedProperties', []);
        $config->set('AutoFormat.RemoveEmpty.RemoveNbsp', false);
        $config->set('Cache.SerializerPath', self::cachePath());
        $config->set('Cache.SerializerPermissions', 0755);

        return $config;
    }

    private static function purifier(): HTMLPurifier
    {
        return new HTMLPurifier(self::config());
    }

    private static function cachePath(): string
    {
        $path = storage_path('app/htmlpurifier');
        if (!is_dir($path)) {
            @mkdir($path, 0755, true);
        }

        return is_dir($path) ? $path : sys_get_temp_dir();
    }

    private static function normalizeBlankTargets(string $html): string
    {
        if ($html === '' || !str_contains(strtolower($html), 'target')) {
            return $html;
        }

        return preg_replace_callback('/<a\b([^>]*)>/i', function (array $match) {
            $attrs = $match[1];
            if (!preg_match('/\btarget\s*=\s*(["\']?)_blank\1/i', $attrs)) {
                return $match[0];
            }

            if (preg_match('/\brel\s*=\s*(["\'])(.*?)\1/i', $attrs, $relMatch)) {
                $parts = preg_split('/\s+/', strtolower($relMatch[2])) ?: [];
                $parts = array_values(array_unique(array_filter(array_merge($parts, ['noopener', 'noreferrer']))));
                $attrs = preg_replace('/\brel\s*=\s*(["\']).*?\1/i', 'rel="' . implode(' ', $parts) . '"', $attrs);
            } else {
                $attrs .= ' rel="noopener noreferrer"';
            }

            return '<a' . $attrs . '>';
        }, $html) ?? $html;
    }
}
