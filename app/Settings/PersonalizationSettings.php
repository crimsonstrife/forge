<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

/**
 * @property bool $solo_mode_default
 * @property bool $streamer_mode
 * @property ?int $in_progress_status_id
 */
final class PersonalizationSettings extends Settings
{
    public bool $solo_mode_default = true;
    public bool $streamer_mode = false;
    public ?int $in_progress_status_id = null;

    public static function group(): string
    {
        return 'personalization';
    }
}
