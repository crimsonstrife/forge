<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $site_name;
    public bool $site_active;
    public bool $enable_registration;
    public string|null $site_description;
    public string|null $site_logo;
    public string|null $site_favicon;
    public string|null $default_role;
    public bool $enable_login_form;
    public bool $enable_registration_form;


    public static function group(): string
    {
        return 'general';
    }
}
