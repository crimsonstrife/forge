<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;


/**
 * Migration class for Gitea settings.
 *
 * This class extends the SettingsMigration class and is used to handle
 * the migration of settings related to Gitea.
 *
 * @return void
 */
return new class () extends SettingsMigration {
    /**
     * Run the "up" method of the migration.
     *
     * This method adds module settings to the database.
     * It sets the Gitea module to be disabled by default.
     */
    public function up(): void
    {
        $this->migrator->add('gitea.enabled', false);
        $this->migrator->add('gitea.base_url', '');
        $this->migrator->add('gitea.api_token', '');
    }

    /**
     * Delete general settings from the database.
     *
     * This method is used to delete the following general settings from the database:
     * - gitea.enabled
     * - gitea.base_url
     * - gitea.api_token
     */
    public function down(): void
    {
        $this->migrator->delete('gitea.enabled');
        $this->migrator->delete('gitea.base_url');
        $this->migrator->delete('gitea.api_token');
    }
};
