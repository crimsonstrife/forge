<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('crucible.enabled', false);
        $this->migrator->add('crucible.base_url', '');
        $this->migrator->addEncrypted('crucible.api_token', '');
    }

    public function down(): void
    {
        $this->migrator->delete('crucible.enabled');
        $this->migrator->delete('crucible.base_url');
        $this->migrator->delete('crucible.api_token');
    }
};
