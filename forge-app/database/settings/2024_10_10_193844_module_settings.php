<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    /**
     * Run the "up" method of the migration.
     *
     * This method adds module settings to the database.
     * It sets the Crucible, Jira, Gitea, Slack, Discord and other modules to be disabled by default.
     */
    public function up(): void
    {
        $this->migrator->add('modules.crucible_enabled', false);
        $this->migrator->add('modules.jira_enabled', false);
        $this->migrator->add('modules.gitea_enabled', false);
        $this->migrator->add('modules.slack_enabled', false);
        $this->migrator->add('modules.discord_enabled', false);
    }

    /**
     * Delete general settings from the database.
     *
     * This method is used to delete the following general settings from the database:
     * - modules.crucible_enabled
     * - modules.jira_enabled
     * - modules.gitea_enabled
     * - modules.slack_enabled
     * - modules.discord_enabled
     * - modules.other_module_enabled (add more as needed)
     */
    public function down(): void
    {
        $this->migrator->delete('modules.crucible_enabled');
        $this->migrator->delete('modules.jira_enabled');
        $this->migrator->delete('modules.gitea_enabled');
        $this->migrator->delete('modules.slack_enabled');
        $this->migrator->delete('modules.discord_enabled');
    }
};
