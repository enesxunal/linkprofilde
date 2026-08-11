<?php

namespace App\Rules;

use App\Support\EmbedUrl as EmbedUrlParser;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EmbedUrl implements ValidationRule
{
    public function __construct(private ?string $icon = null)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) || EmbedUrlParser::canonicalize($value, $this->icon) === null) {
            $fail('The :attribute must be a supported YouTube, Vimeo, Spotify, SoundCloud, or TikTok URL.');
        }
    }
}
