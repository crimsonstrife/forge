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

    /**
     * Creates a new instance of ModuleSettings with default values.
     *
     * @return ModuleSettings The newly created instance.
     */
    public static function makeDefault(): self
    {
        return new self([
            'crucible_enabled' => false,
            'jira_enabled' => false,
            'gitea_enabled' => false,
            'slack_enabled' => false,
            'discord_enabled' => false,
        ]);
    }

    /**
     * Check if the Crucible module is enabled.
     *
     * @return bool Returns true if the Crucible module is enabled, false otherwise.
     */
    public function isCrucibleEnabled(): bool
    {
        return $this->crucible_enabled;
    }

    /**
     * Set the enabled status of the Crucible module.
     *
     * @param bool $enabled The enabled status to set.
     * @return void
     */
    public function setCrucibleEnabled(bool $enabled): void
    {
        $this->crucible_enabled = $enabled;
    }

    /**
     * Check if the Jira module is enabled.
     *
     * @return bool Returns true if the Jira module is enabled, false otherwise.
     */
    public function isJiraEnabled(): bool
    {
        return $this->jira_enabled;
    }

    /**
     * Set the enabled status of the Jira module.
     *
     * @param bool $enabled The enabled status to set.
     * @return void
     */
    public function setJiraEnabled(bool $enabled): void
    {
        $this->jira_enabled = $enabled;
    }

    /**
     * Check if the Gitea module is enabled.
     *
     * @return bool Returns true if the Gitea module is enabled, false otherwise.
     */
    public function isGiteaEnabled(): bool
    {
        return $this->gitea_enabled;
    }

    /**
     * Set the enabled status of the Gitea module.
     *
     * @param bool $enabled The enabled status to set.
     * @return void
     */
    public function setGiteaEnabled(bool $enabled): void
    {
        $this->gitea_enabled = $enabled;
    }

    /**
     * Check if the Slack module is enabled.
     *
     * @return bool Returns true if the Slack module is enabled, false otherwise.
     */
    public function isSlackEnabled(): bool
    {
        return $this->slack_enabled;
    }

    /**
     * Set the enabled status of the Slack module.
     *
     * @param bool $enabled The enabled status to set.
     * @return void
     */
    public function setSlackEnabled(bool $enabled): void
    {
        $this->slack_enabled = $enabled;
    }

    /**
     * Check if the Discord module is enabled.
     *
     * @return bool Returns true if the Discord module is enabled, false otherwise.
     */
    public function isDiscordEnabled(): bool
    {
        return $this->discord_enabled;
    }

    /**
     * Set the enabled status of the Discord module.
     *
     * @param bool $enabled The enabled status to set.
     * @return void
     */
    public function setDiscordEnabled(bool $enabled): void
    {
        $this->discord_enabled = $enabled;
    }

    /**
     * Check if a module is enabled.
     *
     * @param string $module The module name.
     * @return bool Returns true if the module is enabled, false otherwise.
     */
    public function isModuleEnabled(string $module): bool
    {
        switch ($module) {
            case 'crucible':
                return $this->isCrucibleEnabled();
            case 'jira':
                return $this->isJiraEnabled();
            case 'gitea':
                return $this->isGiteaEnabled();
            case 'slack':
                return $this->isSlackEnabled();
            case 'discord':
                return $this->isDiscordEnabled();
            default:
                return false;
        }
    }
}
