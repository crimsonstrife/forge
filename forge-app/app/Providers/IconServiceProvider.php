<?php

namespace App\Providers;

use BladeUI\Icons\Factory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Models\Icon;

class IconServiceProvider extends ServiceProvider
{
    // Built-in icon sets that are available in the application by default, pulled from the configuration file for BladeUI Icons
    private ?array $builtInSets = null;

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Ensure the BladeUI Icons Factory is available
        $iconFactory = app(Factory::class);

        // Load the built-in icon sets from the configuration file
        $this->builtInSets = config('blade-icons.sets');
    }
}
