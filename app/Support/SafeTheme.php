<?php

namespace App\Support;

class SafeTheme
{
    public const FONTS = [
        'Inter, sans-serif',
        'MintGrotesk, sans-serif',
        'DM Sans, sans-serif',
        'Bebas Neue, cursive',
        'Poppins, sans-serif',
        'Quicksand, sans-serif',
    ];

    public const RADIUS = ['8px', '12px', '30px'];

    public const BUTTON_TYPES = [
        'rounded',
        'radius',
        'rectangle',
        'rounded-trans',
        'radius-trans',
        'rectangle-trans',
    ];

    public const DEFAULT_BG = '#30425A';
    public const DEFAULT_TEXT = '#ffffff';
    public const DEFAULT_BTN_BG = '#ffffff';
    public const DEFAULT_BTN_TEXT = '#1d2939';
    public const DEFAULT_FONT = 'Inter, sans-serif';
    public const DEFAULT_RADIUS = '30px';

    public static function hex(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = trim($value);
        if (!preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $value)) {
            return null;
        }

        return $value;
    }

    public static function font(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return in_array($value, self::FONTS, true) ? $value : null;
    }

    public static function radius(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return in_array($value, self::RADIUS, true) ? $value : null;
    }

    public static function buttonType(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return in_array($value, self::BUTTON_TYPES, true) ? $value : null;
    }

    public static function colorBackground(string $hex): string
    {
        return 'background-color: ' . $hex;
    }

    public static function imageBackground(string $uploadPath): string
    {
        return "background-image: url('/" . $uploadPath . "')";
    }

    public static function isUploadPath(?string $path): bool
    {
        if ($path === null) {
            return false;
        }
        $path = str_replace('\\', '/', trim($path));

        return (bool) preg_match('/^upload\/[A-Za-z0-9._-]+\.(jpg|jpeg|png)$/', $path)
            && !str_contains($path, '..');
    }
}
