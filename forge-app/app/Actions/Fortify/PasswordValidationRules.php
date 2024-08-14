<?php

namespace App\Actions\Fortify;

use Illuminate\Validation\Rules\Password;

/**
 * Trait PasswordValidationRules
 *
 * This trait provides reusable password validation rules for the Fortify package.
 * It contains methods for defining password validation rules.
 */
trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>
     */
    protected function passwordRules(): array
    {
        return ['required', 'string', Password::default(), 'confirmed'];
    }
}
