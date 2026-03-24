<?php

/**
 * Codex integration configuration.
 *
 * These values can be overridden by environment variables or managed dynamically
 * via the CodexSettings (Spatie Settings) stored in the database.
 * The CodexSettings take precedence over env vars in application code.
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Codex Integration Enabled
    |--------------------------------------------------------------------------
    |
    | Set to true to enable the Codex wiki/docs integration. When disabled,
    | no Codex UI elements will appear in Forge and all API calls to Codex
    | will be skipped.
    |
    */
    'enabled' => env('CODEX_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Codex Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL of the connected Codex installation.
    | Example: https://docs.example.com
    |
    */
    'url' => env('CODEX_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Codex App Token
    |--------------------------------------------------------------------------
    |
    | A system-level app token for machine-to-machine API calls (workspace
    | listing, page search). Not tied to any user account.
    |
    | Generate one on the Codex side with:
    |   php artisan app-token:create "Forge"
    |
    | Then set the printed value as CODEX_APP_TOKEN here.
    |
    */
    'app_token' => env('CODEX_APP_TOKEN', ''),

];
