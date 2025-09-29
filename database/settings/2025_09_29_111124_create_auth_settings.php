<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('auth.allowRegistration', true);
    }

    public function down(): void
    {
        $this->migrator->delete('auth.allowRegistration');
    }
};
