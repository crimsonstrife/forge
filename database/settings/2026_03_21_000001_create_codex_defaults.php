<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('codex.enabled', false);
        $this->migrator->add('codex.url', '');
        $this->migrator->addEncrypted('codex.token', null);
    }

    public function down(): void
    {
        $this->migrator->delete('codex.enabled');
        $this->migrator->delete('codex.url');
        $this->migrator->delete('codex.token');
    }
};
