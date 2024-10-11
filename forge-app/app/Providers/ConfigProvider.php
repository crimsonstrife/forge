<?php

namespace App\Providers;

use App\Settings\CrucibleSettings;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use App\Models\DiscordConfig;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

/**
 * Class ConfigProvider
 *
 * This class is responsible for registering and bootstrapping services, as well as loading Crucible settings.
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
        if (Schema::hasTable('crucible_settings')) {
            $this->loadCrucibleSettings();
        }

        // Only load the Discord settings if the database is available
        if (Schema::hasTable('discord_config')) {
            $this->loadDiscordSettings();
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
        $discordConfig = DiscordConfig::first();  // Only fetch the first configuration

        if ($discordConfig) {
            Config::set('discord', array_merge(Config::get('discord'), [
                'client_id' => $discordConfig->client_id ?? Config::get('discord.client_id'),
                'client_secret' => $discordConfig->client_secret ?? Config::get('discord.client_secret'),
                'bot_token' => $discordConfig->bot_token ?? Config::get('discord.bot_token'),
                'guild_id' => $discordConfig->guild_id ?? Config::get('discord.guild_id'),
                'redirect_uri' => $discordConfig->redirect_uri ?? Config::get('discord.redirect_uri'),
                'role_mappings' => $discordConfig->role_mappings ?? Config::get('discord.role_mappings'),
                'channel_mappings' => $discordConfig->channel_mappings ?? Config::get('discord.channel_mappings'),
            ]));
        }
    }
}
