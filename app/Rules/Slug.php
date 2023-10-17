<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Slug implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if($this->hasUnderscores($value))
        {
            $fail(trans('validation.no_underscores'));
        }

        if($this->startsWithDashes($value))
        {
            $fail(trans('validation.no_starting_dashes'));
        }

        if($this->endsWithDashes($value))
        {
            $fail(trans('validation.no_ending_dashes'));
        }

    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function hasUnderscores(mixed $value): bool
    {
        return str_contains($value, '_');
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function startsWithDashes(mixed $value): bool
    {
        return str_starts_with($value, '-');
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function endsWithDashes(mixed $value): bool
    {
        return str_ends_with($value, '-');
    }
}
