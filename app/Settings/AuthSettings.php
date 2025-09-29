<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

/**
 * @phpstan-type AuthSettingsShape array{allowRegistration: bool}
 */
class AuthSettings extends Settings
{
    public bool $allowRegistration = false;

    public static function group(): string
    {
        return 'auth';
    }
}
