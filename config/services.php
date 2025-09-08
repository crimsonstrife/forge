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

    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => env('GITHUB_REDIRECT_URI'),
    ],

    'gitlab' => [
        'client_id' => env('GITLAB_CLIENT_ID'),
        'client_secret' => env('GITLAB_CLIENT_SECRET'),
        'redirect' => env('GITLAB_REDIRECT_URI'),
        'base_url' => env('GITLAB_BASE_URL', 'https://gitlab.com'),
    ],

    'gitea' => [
        'client_id' => env('GITEA_CLIENT_ID'),
        'client_secret' => env('GITEA_CLIENT_SECRET'),
        'redirect' => env('GITEA_REDIRECT_URI'),
        'base_url' => env('GITEA_BASE_URL', 'https://gitea.example.com'),
    ],

    'discord' => [
        'client_id' => env('DISCORD_CLIENT_ID'),
        'client_secret' => env('DISCORD_CLIENT_SECRET'),
        'redirect' => env('DISCORD_REDIRECT_URI'),
        'allow_gif_avatars' => (bool)env('DISCORD_AVATAR_GIF', true),
        'avatar_default_extension' => env('DISCORD_EXTENSION_DEFAULT', 'png'), // only pick from jpg, png, webp
    ],

    'todoist' => [
        'client_id' => env('TODOIST_CLIENT_ID'),
        'client_secret' => env('TODOIST_CLIENT_SECRET'),
        'redirect' => env('TODOIST_REDIRECT_URI')
    ],

    'atlassian' => [
        'client_id' => env('ATLASSIAN_CLIENT_ID'),
        'client_secret' => env('ATLASSIAN_CLIENT_SECRET'),
        'redirect' => env('ATLASSIAN_REDIRECT_URI')
    ],

    'socialite_providers' => explode(',', env('SOCIALITE_PROVIDERS', 'github,gitlab,gitea,discord,todoist,atlassian,slack')),
];
