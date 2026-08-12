<?php

namespace App\Support;

use Illuminate\Validation\ValidationException;

class BioItemLink
{
    public static function normalize(?string $itemLink, ?string $itemType, ?string $itemIcon): ?string
    {
        $itemLink = self::emptyToNull($itemLink);
        $icon = (string) $itemIcon;
        $type = (string) $itemType;

        if (in_array($icon, ['Heading', 'Paragraph'], true)) {
            return null;
        }

        $isEmbed = $type === 'Embed' || in_array($icon, EmbedUrl::ICONS, true);
        if ($isEmbed) {
            if ($itemLink === null) {
                throw ValidationException::withMessages([
                    'item_link' => 'Embed bağlantısı zorunludur.',
                ]);
            }
            $canonical = EmbedUrl::canonicalize($itemLink, $icon !== '' ? $icon : null);
            if ($canonical === null) {
                throw ValidationException::withMessages([
                    'item_link' => 'Desteklenmeyen veya geçersiz video bağlantısı.',
                ]);
            }

            return $canonical;
        }

        if ($icon === 'Link') {
            if ($itemLink === null) {
                throw ValidationException::withMessages([
                    'item_link' => 'Link adresi zorunludur.',
                ]);
            }
            $canonical = SafeUrl::canonicalize($itemLink);
            if ($canonical === null) {
                throw ValidationException::withMessages([
                    'item_link' => 'Geçersiz bağlantı adresi.',
                ]);
            }

            return $canonical;
        }

        if ($itemLink === null) {
            return null;
        }

        $canonical = SafeUrl::canonicalize($itemLink);
        if ($canonical === null) {
            throw ValidationException::withMessages([
                'item_link' => 'Geçersiz bağlantı adresi.',
            ]);
        }

        return $canonical;
    }

    private static function emptyToNull(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = trim($value);
        if ($value === '' || strcasecmp($value, 'null') === 0) {
            return null;
        }

        return $value;
    }
}
