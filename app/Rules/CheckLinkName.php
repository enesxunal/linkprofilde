<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CheckLinkName implements ValidationRule
{
    private const RESERVED = [
        'admin', 'app', 'assets', 'billing', 'blog', 'build', 'currentplan', 'dashboard',
        'forgotpassword', 'login', 'logout', 'password', 'projects', 'q', 'qrcodes',
        'register', 'resetpassword', 'robots', 'settings', 'shortlinks', 'sitemap',
        'storage', 'tosla', 'verifyemail', 'version', 'llms', 'homesection', 'biolinks',
    ];

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = (string) $value;

        if (preg_match('/^[A-Za-z0-9]+$/', $value) !== 1) {
            $fail('Profil adresi yalnızca harf ve rakamlardan oluşabilir; boşluk veya özel karakter kullanılamaz.');
            return;
        }

        $normalized = strtolower($value);
        if (in_array($normalized, self::RESERVED, true)) {
            $fail('Bu profil adresi sistem tarafından ayrılmıştır. Lütfen farklı bir adres seçin.');
        }
    }
}
