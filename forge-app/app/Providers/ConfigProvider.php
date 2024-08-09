<?php

namespace App\Providers;

use App\Settings\CrucibleSettings;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class ConfigProvider extends ServiceProvider
{
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
        //
    }

    /**
     * Load Crucible settings
     * @return void
     */
    public function loadCrucibleSettings(): void
    {
        $theConfig = null;

        try {
            $theConfig = app(CrucibleSettings::class);

            if ($theConfig->isEnabled()) {
                // Load the Crucible settings
                $this->loadCrucibleSettings();
            }
        } catch (\Exception $e) {
            // Log the error message if the Crucible settings are not loaded successfully
            Log::error('Crucible settings are not loaded successfully', ['error' => $e->getMessage()]);

            return;
        }

        config()->set(
            'crucible',
            [
                'enabled' => $theConfig->isEnabled(),
                'base_url' => $theConfig->getBaseUrl(),
                'api_token' => $theConfig->api_token,
            ],
        );
    }
}
