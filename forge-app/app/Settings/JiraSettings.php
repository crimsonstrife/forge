<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class JiraSettings extends Settings
{
    public static function group(): string
    {
        return 'default';
    }
}
