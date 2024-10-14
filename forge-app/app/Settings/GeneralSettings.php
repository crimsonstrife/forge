<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

/**
 * Class GeneralSettings
 *
 * This class represents the general settings for the application.
 * It extends the Settings class and defines various properties related to the general settings.
 */
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


    /**
     * Returns the group name for the general settings.
     *
     * @return string The group name.
     */
    public static function group(): string
    {
        return 'general';
    }

    /**
     * Provides the default values for the general settings.
     *
     * @return array The default values.
     */
    public static function defaults(): array
    {
        return [
            'site_name' => 'Forge',
            'site_active' => true,
            'enable_registration' => true,
            'site_description' => null,
            'site_logo' => null,
            'site_favicon' => null,
            'default_role' => 'user',
            'enable_login_form' => true,
            'enable_registration_form' => true,
        ];
    }
}
