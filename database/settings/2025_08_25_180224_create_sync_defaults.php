<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('sync.allow_outbound_issue_updates', false);
        $this->migrator->add('sync.auto_transition_on_pr_merge', true);
        $this->migrator->add('sync.link_keyword_fix', 'Fixes');
        $this->migrator->add('sync.link_keyword_close', 'Closes');
    }

    public function down(): void
    {
        $this->migrator->delete('sync.allow_outbound_issue_updates');
        $this->migrator->delete('sync.auto_transition_on_pr_merge');
        $this->migrator->delete('sync.link_keyword_fix');
        $this->migrator->delete('sync.link_keyword_close');
    }
};
