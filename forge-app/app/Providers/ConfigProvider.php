<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use App\Models\DiscordConfig;
//use App\Models\CrucibleConfig;
use App\Settings\DiscordSettings;
use App\Settings\CrucibleSettings;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

/**
 * Class ConfigProvider
 *
 * This class is responsible for registering and bootstrapping services, as well as loading module settings.
 */
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
        /* // Only load the Crucible settings if the database is available
        if (Schema::hasTable('settings')) {
            // Check if the Crucible settings are enabled
            if (config('services.crucible.enabled') === true) {
                $this->loadCrucibleSettings();
            } else {
                Log::info('Crucible settings are not enabled');
            }
        } else {
            Log::warning('Crucible settings are not loaded because the database is not available');
        }

        // Only load the Discord settings if the database is available
        if (Schema::hasTable('settings')) {
            // Check if the Discord settings are enabled
            if (config('services.discord.enabled') === true) {
                $this->loadDiscordSettings();
            } else {
                Log::info('Discord settings are not enabled');
            }
        } else {
            Log::warning('Discord settings are not loaded because the database is not available');
        } */
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

    /**
     * Load Discord settings
     * @return void
     */
    public function loadDiscordSettings(): void
    {
        $theConfig = null;

        try {
            $theConfig = app(DiscordSettings::class);

            if ($theConfig->isEnabled()) {
                // Load the Discord settings
                $this->loadDiscordSettings();
            }
        } catch (\Exception $e) {
            // Log the error message if the Discord settings are not loaded successfully
            Log::error('Discord settings are not loaded successfully', ['error' => $e->getMessage()]);

            return;
        }

        config()->set(
            'discord',
            [
                'enabled' => $theConfig->isEnabled(),
                'client_id' => $theConfig->getClientId(),
                'client_secret' => $theConfig->getClientSecret(),
                'bot_token' => $theConfig->getBotToken(),
                'guild_id' => $theConfig->getGuildId(),
            ],
        );
    }
}
