<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidUSPhoneNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        // Remove formatting characters
        $cleaned = preg_replace('/[\s\-\(\)]+/', '', $value);

        // Check if it starts with +1
        if (!str_starts_with($cleaned, '+1')) {
            $fail('The :attribute must start with +1.');
            return;
        }

        // Get the number part after +1
        $number = substr($cleaned, 2);

        if (!preg_match('/^\d{10}$/', $number)) {
            $fail('The :attribute must be a valid 10-digit US number.');
        }
    }

    // public function passes($attribute, $value)
    // {
    //     // Remove allowed formatting characters
    //     $cleaned = preg_replace('/[\s\-\(\)]+/', '', $value);

    //     // Check for +1 at the start
    //     if (!str_starts_with($cleaned, '+1')) {
    //         return false;
    //     }

    //     // Extract the part after +1
    //     $numberPart = substr($cleaned, 2);

    //     // Check if it contains exactly 10 digits and only digits
    //     return preg_match('/^\d{10}$/', $numberPart);
    // }

    // public function message()
    // {
    //     return 'The :attribute must be a valid US phone number starting with +1 and followed by 10 digits.';
    // }
}
