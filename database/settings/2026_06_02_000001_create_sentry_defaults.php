<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('sentry.enabled', false);
        $this->migrator->add('sentry.org_slug', '');
        $this->migrator->add('sentry.client_id', '');
        $this->migrator->addEncrypted('sentry.client_secret', null);
        $this->migrator->addEncrypted('sentry.auth_token', null);
        $this->migrator->add('sentry.api_base', 'https://sentry.io/api/0');
        $this->migrator->add('sentry.default_project_id', null);
        $this->migrator->add('sentry.default_issue_type_id', null);
        $this->migrator->add('sentry.default_priority_id', null);
        $this->migrator->add('sentry.installation_uuid', null);
    }

    public function down(): void
    {
        $this->migrator->delete('sentry.enabled');
        $this->migrator->delete('sentry.org_slug');
        $this->migrator->delete('sentry.client_id');
        $this->migrator->delete('sentry.client_secret');
        $this->migrator->delete('sentry.auth_token');
        $this->migrator->delete('sentry.api_base');
        $this->migrator->delete('sentry.default_project_id');
        $this->migrator->delete('sentry.default_issue_type_id');
        $this->migrator->delete('sentry.default_priority_id');
        $this->migrator->delete('sentry.installation_uuid');
    }
};
