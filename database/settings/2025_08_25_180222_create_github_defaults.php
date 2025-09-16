<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('github.enabled', false);
        $this->migrator->add('github.app_name', 'Forge GitHub App');
        $this->migrator->add('github.client_id', '');
        $this->migrator->addEncrypted('github.client_secret', null);
        $this->migrator->addEncrypted('github.webhook_secret', null);
        $this->migrator->addEncrypted('github.personal_access_token', null);
        $this->migrator->add('github.api_base', 'https://api.github.com');
        $this->migrator->add('github.web_base', 'https://github.com');
    }

    public function down(): void
    {
        $this->migrator->delete('github.enabled');
        $this->migrator->delete('github.app_name');
        $this->migrator->delete('github.client_id');
        $this->migrator->delete('github.client_secret');
        $this->migrator->delete('github.webhook_secret');
        $this->migrator->delete('github.personal_access_token');
        $this->migrator->delete('github.api_base');
        $this->migrator->delete('github.web_base');
    }
};
