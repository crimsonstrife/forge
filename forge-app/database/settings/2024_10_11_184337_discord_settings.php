<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

/**
 * Migration for Discord settings.
 *
 * This migration class is responsible for handling the database changes
 * related to Discord settings.
 *
 * @return void
 * @extends SettingsMigration
 */
return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('discord.enabled', false);
        $this->migrator->add('discord.client_id', '');
        $this->migrator->addEncrypted('discord.client_secret', '');
        $this->migrator->addEncrypted('discord.bot_token', '');
        $this->migrator->add('discord.guild_id', '');
        $this->migrator->add('discord.redirect_uri', '');
        $this->migrator->add('discord.role_mappings', []);
        $this->migrator->add('discord.channel_mappings', []);
    }

    public function down(): void
    {
        $this->migrator->delete('discord.enabled');
        $this->migrator->delete('discord.client_id');
        $this->migrator->delete('discord.client_secret');
        $this->migrator->delete('discord.bot_token');
        $this->migrator->delete('discord.guild_id');
        $this->migrator->delete('discord.redirect_uri');
        $this->migrator->delete('discord.role_mappings');
        $this->migrator->delete('discord.channel_mappings');
    }
};
