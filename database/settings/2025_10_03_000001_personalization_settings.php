<?php

use App\Settings\PersonalizationSettings;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('personalization.solo_mode_default', true);
        $this->migrator->add('personalization.streamer_mode', false);
        $this->migrator->add('personalization.in_progress_status_id', null);
    }

    public function down(): void
    {
        $this->migrator->delete('personalization.solo_mode_default');
        $this->migrator->delete('personalization.streamer_mode');
        $this->migrator->delete('personalization.in_progress_status_id');
    }
};
