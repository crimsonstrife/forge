<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'discord' => [
        'client_id' => env('DISCORD_CLIENT_ID'),
        'client_secret' => env('DISCORD_CLIENT_SECRET'),
        'bot_token' => env('DISCORD_BOT_TOKEN'),
        'guild_id' => env('DISCORD_GUILD_ID'),
        'redirect' => env('DISCORD_REDIRECT_URI'),
    ],

    /*
    |--------------------------------------------------------------------------
    | First Party Services
    |--------------------------------------------------------------------------
    |
    | This section is for services that are developed by the Helical Games team.
    | These services are used to interact with our internal systems.
    | This includes services like Crucible, Codex, and more.
    |
    */
    // Crucible Service Configuration - Used to interact with Crucible Repositories
    'crucible' => [
        'enabled' => env('CRUCIBLE_CONNECTION_ENABLED') ?? false,
        'base_url' => env('CRUCIBLE_BASE_URL') ?? '',
        'api_token' => env('CRUCIBLE_API_TOKEN') ?? '',
    ],
];
