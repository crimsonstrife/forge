<?php

namespace App\Settings;

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
     * Creates a new instance of DiscordSettings with default values.
     *
     * @return DiscordSettings The newly created instance.
     */
    public static function makeDefault(): self
    {
        return new self([
            'enabled' => false,
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
     * Set the client ID for Discord OAuth.
     *
     * @param string $client_id The client ID to set.
     * @return void
     */
    public function setClientId(string $client_id): void
    {
        $this->client_id = $client_id;
    }

    /**
     * Set the client secret for Discord OAuth.
     * Should be kept secret, encrypted
     *
     * @param string $client_secret The client secret to set.
     * @return void
     */
    public function setClientSecret(string $client_secret): void
    {
        $this->attributes['client_secret'] = encrypt($client_secret);
    }

    /**
     * Set the bot token for Discord.
     * Should be kept secret, encrypted
     *
     * @param string $bot_token The bot token to set.
     * @return void
     */
    public function setBotToken(string $bot_token): void
    {
        $this->attributes['bot_token'] = encrypt($bot_token);
    }

    /**
     * Set the guild ID for Discord.
     *
     * @param string $guild_id The guild ID to set.
     * @return void
     */
    public function setGuildId(string $guild_id): void
    {
        $this->guild_id = $guild_id;
    }

    /**
     * Set the redirect URI for Discord OAuth.
     *
     * @param string $redirect_uri The redirect URI to set.
     * @return void
     */
    public function setRedirectUri(string $redirect_uri): void
    {
        $this->redirect_uri = $redirect_uri;
    }

    /**
     * Set the role mappings for Discord.
     *
     * @param array $role_mappings The role mappings to set.
     * @return void
     */
    public function setRoleMappings(array $role_mappings): void
    {
        $this->role_mappings = $role_mappings;
    }

    /**
     * Set the channel mappings for Discord.
     *
     * @param array $channel_mappings The channel mappings to set.
     * @return void
     */
    public function setChannelMappings(array $channel_mappings): void
    {
        $this->channel_mappings = $channel_mappings;
    }

    /**
     * Retrieves the client ID for Discord OAuth.
     *
     * @return string The client ID.
     */
    public function getClientId(): string
    {
        return $this->client_id;
    }

    /**
     * Retrieves the client secret for Discord OAuth.
     *
     * @return string The client secret.
     */
    public function getClientSecret(): string
    {
        // Decrypt the client secret
        return decrypt($this->client_secret);
    }

    /**
     * Retrieves the bot token for Discord.
     *
     * @return string The bot token.
     */
    public function getBotToken(): string
    {
        // Decrypt the bot token
        return decrypt($this->bot_token);
    }

    /**
     * Retrieves the guild ID for Discord.
     *
     * @return string The guild ID.
     */
    public function getGuildId(): string
    {
        return $this->guild_id;
    }

    /**
     * Retrieves the redirect URI for Discord OAuth.
     *
     * @return string The redirect URI.
     */
    public function getRedirectUri(): string
    {
        return $this->redirect_uri;
    }

    /**
     * Retrieves the role mappings for Discord.
     *
     * @return array The role mappings.
     */
    public function getRoleMappings(): array
    {
        return $this->role_mappings;
    }

    /**
     * Retrieves the channel mappings for Discord.
     *
     * @return array The channel mappings.
     */
    public function getChannelMappings(): array
    {
        return $this->channel_mappings;
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
