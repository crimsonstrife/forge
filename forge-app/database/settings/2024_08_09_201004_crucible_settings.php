<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

/**
 * Migration class for Crucible settings.
 *
 * This class extends the SettingsMigration class and is used to handle
 * the migration of settings related to Crucible.
 *
 * @return void
 */
return new class () extends SettingsMigration {
    /**
     * Run the "up" method of the migration.
     *
     * This method is responsible for adding the necessary settings for the Crucible feature.
     * It sets the "crucible.enabled" setting to false, the "crucible.base_url" setting to an empty string,
     * and the "crucible.api_token" setting to an empty encrypted value.
     *
     * @return void
     */
    public function up(): void
    {
        $this->migrator->add('crucible.enabled', false);
        $this->migrator->add('crucible.base_url', '');
        $this->migrator->addEncrypted('crucible.api_token', '');
    }

    /**
     * Delete the crucible settings from the database.
     *
     * This method is responsible for deleting the following crucible settings from the database:
     * - crucible.enabled
     * - crucible.base_url
     * - crucible.api_token
     *
     * @return void
     */
    public function down(): void
    {
        $this->migrator->delete('crucible.enabled');
        $this->migrator->delete('crucible.base_url');
        $this->migrator->delete('crucible.api_token');
    }
};
