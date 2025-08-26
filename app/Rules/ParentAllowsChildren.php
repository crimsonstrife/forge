<?php

namespace App\Rules;

use App\Models\Issue;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ParentAllowsChildren implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value) { return; }
        $parent = Issue::query()->with('type')->find($value);
        if (! $parent) { $fail('Parent issue not found.'); return; }
        if (! $parent->type || ! $parent->type->is_hierarchical) {
            $fail('Selected parent issue cannot contain children.');
        }
    }
}
