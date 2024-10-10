<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

/**
 * GiteaSettings class represents the settings for the Gitea feature.
 * This controls the ability for Forge to interact with a Gitea server instance.
 */
class GiteaSettings extends Settings
{
    public bool $enabled;
    public string $base_url;
    public string $api_token;

    /**
     * Returns the group name for Gitea settings.
     *
     * @return string The group name for Gitea settings.
     */
    public static function group(): string
    {
        return 'gitea';
    }
}
