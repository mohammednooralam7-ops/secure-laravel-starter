<?php
namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrongPassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $v = (string) $value;
        if (strlen($v) < 12
            || ! preg_match('/[a-z]/', $v) || ! preg_match('/[A-Z]/', $v)
            || ! preg_match('/\d/', $v)   || ! preg_match('/[^A-Za-z0-9]/', $v)) {
            $fail('The :attribute must be at least 12 characters and include upper, lower, number and symbol.');
        }
    }
}
