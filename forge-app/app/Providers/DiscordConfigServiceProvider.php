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
        // Check if the Discord module is enabled from the ModuleSettings
        $isEnabled = app(ModuleSettings::class)->discord_enabled;

        // If the Discord module is enabled, load the DiscordSettings
        if ($isEnabled) {
            $this->load();
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
