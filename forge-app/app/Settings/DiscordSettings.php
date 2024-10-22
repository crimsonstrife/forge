<?php

namespace App\Settings;

use App\Models\DiscordConfig;
use Spatie\LaravelSettings\Settings;

/**
 * DiscordSettings class represents the settings for the Discord Connectivity feature.
 *
 * @property bool $enabled Indicates whether the Discord feature is enabled or not.
 */
class DiscordSettings extends Settings
{
    public bool $enabled;
    public string $client_id;
    public string $client_secret;
    public string $bot_token;
    public string $guild_id;
    public string $redirect_uri;
    public array $role_mappings;
    public array $channel_mappings;

    /**
     * Returns the group name for Discord settings.
     *
     * @return string The group name for Discord settings.
     */
    public static function group(): string
    {
        return 'discord';
    }

    /**
     * Define the default values using the defaults() method.
     */
    public static function defaults(): array
    {
        // Get the DiscordConfig from the database
        $discordConfig = DiscordConfig::first();

        return [
            'enabled' => false,
            'client_id' => $discordConfig->client_id ?? env('DISCORD_CLIENT_ID', ''),
            'client_secret' => $discordConfig->client_secret ?? env('DISCORD_CLIENT_SECRET', ''),
            'bot_token' => $discordConfig->bot_token ?? env('DISCORD_BOT_TOKEN', ''),
            'guild_id' => $discordConfig->guild_id ?? env('DISCORD_GUILD_ID', ''),
            'redirect_uri' => $discordConfig->redirect_uri ?? env('DISCORD_REDIRECT_URI', ''),
            'role_mappings' => $discordConfig->role_mappings ?? [],
            'channel_mappings' => $discordConfig->channel_mappings ?? [],
        ];
    }

    /**
     * Creates a new instance of DiscordSettings with default values.
     *
     * @return DiscordSettings The newly created instance.
     */
    public static function makeDefault(): self
    {
        return new self([
            'enabled' => false,
            'client_id' => '',
            'client_secret' => '',
            'bot_token' => '',
            'guild_id' => '',
            'redirect_uri' => '',
            'role_mappings' => [],
            'channel_mappings' => [],
        ]);
    }

    /**
     * Check if the Discord settings are enabled.
     *
     * @return bool Returns true if the Discord settings are enabled, false otherwise.
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Set the enabled status of the Discord settings.
     *
     * @param bool $enabled The enabled status to set.
     * @return void
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * Returns the client secret for Discord.
     *
     * @return string The client secret for Discord.
     */
    public function getClientSecret(): string
    {
        return $this->client_secret;
    }

    /**
     * Returns the bot token for Discord.
     *
     * @return string The bot token for Discord.
     */
    public function getBotToken(): string
    {
        return $this->bot_token;
    }

    /**
     * Returns the guild ID for Discord.
     *
     * @return string The guild ID for Discord.
     */
    public function getGuildId(): string
    {
        return $this->guild_id;
    }

    /**
     * Returns the client ID for Discord.
     *
     * @return string The client ID for Discord.
     */
    public function getClientId(): string
    {
        return $this->client_id;
    }

    /**
     * Returns an array of encrypted settings.
     *
     * @return array The array of encrypted settings.
     */
    public static function encrypted(): array
    {
        return [
            'client_secret',
            'bot_token',
        ];
    }
}
