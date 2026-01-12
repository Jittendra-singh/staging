<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrictEmail implements ValidationRule
{
    /**
    * Run the validation rule.
    *
    * @param  string  $attribute
    * @param  mixed   $value
    * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
    */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = trim($value);

        // 1. Must not be empty
        if (empty($value)) {
            $fail('The :attribute field is required.');
            return;
        }

        // 2. No spaces before, within, or after
        if (preg_match('/\s/', $value)) {
            $fail('The :attribute must not contain spaces.');
            return;
        }

        // 3. Must be a valid email format
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $fail('The :attribute must be a valid email address.');
            return;
        }

        // 4. No consecutive dots
        if (strpos($value, '..') !== false) {
            $fail('The :attribute must not contain consecutive dots.');
            return;
        }

        // 5. Only ASCII characters (no emojis or non-Latin characters)
        if (!mb_check_encoding($value, 'ASCII')) {
            $fail('The :attribute must not contain emojis or non-Latin characters.');
            return;
        }

        // 6. Must contain @ and valid domain with extension
        $parts = explode('@', $value);
        if (count($parts) !== 2 || empty($parts[0]) || empty($parts[1])) {
            $fail('The :attribute must contain a valid "@" and domain.');
            return;
        }

        $domain = $parts[1];
        if (!preg_match('/^[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,}$/', $domain)) {
            $fail('The :attribute must contain a valid domain extension (like .com, .org).');
        }
    }
}
