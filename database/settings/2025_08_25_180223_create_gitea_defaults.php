<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('gitea.enabled', false);
        $this->migrator->add('gitea.base_url', 'https://gitea.example.com');
        $this->migrator->add('gitea.app_name', 'Forge Gitea App');
        $this->migrator->add('gitea.client_id', null);
        $this->migrator->addEncrypted('gitea.client_secret', null);
        $this->migrator->addEncrypted('gitea.webhook_secret', null);
        $this->migrator->addEncrypted('gitea.personal_access_token', null);
    }

    public function down(): void
    {
        $this->migrator->delete('gitea.enabled');
        $this->migrator->delete('gitea.app_name');
        $this->migrator->delete('gitea.client_id');
        $this->migrator->delete('gitea.client_secret');
        $this->migrator->delete('gitea.webhook_secret');
        $this->migrator->delete('gitea.personal_access_token');
        $this->migrator->delete('gitea.base_url');
    }
};
