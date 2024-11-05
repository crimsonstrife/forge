<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;


/**
 * Migration class for general settings.
 *
 * This class extends the SettingsMigration class and is used to handle
 * the migration of general settings in the database.
 *
 * @return void
 */
return new class () extends SettingsMigration {
    /**
     * Run the "up" method of the migration.
     *
     * This method adds general settings to the database.
     * It sets the site name to "Forge", site active to true,
     * site description to "Forge is a Laravel application that facilitates Project Management.",
     * site logo to "logo.png", and site favicon to "favicon.ico".
     */
    public function up(): void
    {
        $this->migrator->add('general.site_name', 'Forge');
        $this->migrator->add('general.site_active', true);
        $this->migrator->add('general.enable_registration', false);
        $this->migrator->add('general.site_description', 'Forge is a Laravel application that facilitates Project Management.');
        $this->migrator->add('general.site_logo', 'logo.png');
        $this->migrator->add('general.site_favicon', 'favicon.ico');
        $this->migrator->add('general.default_role', 'user');
        $this->migrator->add('general.enable_login_form', true);
        $this->migrator->add('general.enable_registration_form', false);
    }

    /**
     * Delete general settings from the database.
     *
     * This method is used to delete the following general settings from the database:
     * - general.site_name
     * - general.site_active
     * - general.site_description
     * - general.site_logo
     * - general.site_favicon
     *
     * @return void
     */
    public function down(): void
    {
        $this->migrator->delete('general.site_name');
        $this->migrator->delete('general.site_active');
        $this->migrator->delete('general.enable_registration');
        $this->migrator->delete('general.site_description');
        $this->migrator->delete('general.site_logo');
        $this->migrator->delete('general.site_favicon');
        $this->migrator->delete('general.default_role');
        $this->migrator->delete('general.enable_login_form');
        $this->migrator->delete('general.enable_registration_form');
    }
};
