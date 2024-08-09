<?php

namespace App\Settings;

use App\Settings\Settings;

class GeneralSettings extends Settings
{
    public string $site_name;

    public bool $site_active;

    public static function group(): string
    {
        return 'general';
    }
}
