<?php

namespace App\Rules;

use App\Support\SafeUrl as SafeUrlParser;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SafeUrl implements ValidationRule
{
    /**
     * @param  list<string>  $schemes
     */
    public function __construct(private array $schemes = ['http', 'https'])
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) || SafeUrlParser::canonicalize($value, $this->schemes) === null) {
            $fail('The :attribute must be a valid http or https URL.');
        }
    }
}
