<?php

namespace App\Providers;

use App\Settings\PersonalizationSettings;
use Illuminate\Support\ServiceProvider;
use Laravel\Pennant\Feature;

class SoloModeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Feature::define('solo-mode', static function (): bool {
            /** @var PersonalizationSettings $settings */
            $settings = app(PersonalizationSettings::class);

            return $settings->solo_mode_default === true;
        });
    }
}
