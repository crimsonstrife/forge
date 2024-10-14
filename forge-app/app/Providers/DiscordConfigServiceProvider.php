<?php

namespace App\Providers;

use App\Settings\DiscordSettings;
use App\Settings\ModuleSettings;
use Illuminate\Support\ServiceProvider;

class DiscordConfigServiceProvider extends ServiceProvider
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
        // Check if the database is available and the settings table exists
        if (app()->runningInConsole() || !app()->bound('db') || !app()->make('db')->connection()->getSchemaBuilder()->hasTable('settings')) {
            // Check if the Discord module is enabled from the ModuleSettings
            try {
                $isEnabled = app(ModuleSettings::class)->isModuleEnabled('discord');
            } catch (\Exception $e) {
                // If the ModuleSettings class is not found, the Discord module is disabled
                $isEnabled = false;
            }

            // If the Discord module is enabled, load the DiscordSettings
            if ($isEnabled) {
                $this->load();
            }
        }
    }

    /**
     * Indicates if loading of the provider is deferred.
     */
    public function isDeferred(): bool
    {
        return false;
    }

    /**
     * Load the DiscordSettings.
     */
    private function load(): void
    {
        // Ensure DiscordSettings uses either the database or .env file
        $discordSettings = app(DiscordSettings::class);

        // Merge the settings globally
        config()->set('discord', array_merge(config('discord'), [
            'enabled' => $discordSettings->enabled,
            'client_id' => $discordSettings->client_id,
            'client_secret' => $discordSettings->client_secret,
            'bot_token' => $discordSettings->bot_token,
            'guild_id' => $discordSettings->guild_id,
            'redirect_uri' => $discordSettings->redirect_uri,
            'role_mappings' => $discordSettings->role_mappings,
            'channel_mappings' => $discordSettings->channel_mappings,
        ]));
    }
}
