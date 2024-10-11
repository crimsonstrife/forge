<?php

return [
    /**
     * Discord OAuth2 Configurationß
     */
    'client_id' => env('DISCORD_CLIENT_ID', ''),
    'client_secret' => env('DISCORD_CLIENT_SECRET', ''),
    'bot_token' => env('DISCORD_BOT_TOKEN', ''),
    'guild_id' => env('DISCORD_GUILD_ID', ''),  // Add the guild (server) ID to limit it to one server
    'redirect_uri' => env('DISCORD_REDIRECT_URI', ''),
    'role_mappings' => [], // Default role mappings, which can be overridden in the UI
    'channel_mappings' => [], // Default channel mappings, which can be overridden in the UI
];
