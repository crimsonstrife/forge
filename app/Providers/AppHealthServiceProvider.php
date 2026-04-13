<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppHealthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Deprecated in favor of HealthServiceProvider. Keeping this class as a
        // no-op avoids duplicate health checks if a stale provider manifest or
        // cached bootstrap file still attempts to load it during deployment.
    }
}
