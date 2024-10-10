<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

/**
 * Class ModuleSettings
 *
 * This class represents the module settings for the application.
 * It extends the Settings class and defines various properties related to optional modules, such as Crucible, Jira, and Gitea and enables or disables them.
 * Those modules have their own settings classes, such as CrucibleSettings, JiraSettings, and GiteaSettings respectively to store their specific settings.
 */
class ModuleSettings extends Settings
{
    /**
     * Define the module settings properties. Each property represents a module and indicates whether it is enabled or not.
     * Add new properties for additional modules as needed.
     * @var bool
     */
    public bool $crucible_enabled;
    public bool $jira_enabled;
    public bool $gitea_enabled;
    public bool $slack_enabled;
    public bool $discord_enabled;

    /**
     * Returns the group name for the module settings.
     *
     * @return string The group name.
     */
    public static function group(): string
    {
        return 'modules';
    }
}
