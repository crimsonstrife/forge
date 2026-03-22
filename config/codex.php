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
    | Codex API Token
    |--------------------------------------------------------------------------
    |
    | A Sanctum Personal Access Token generated in Codex for machine-to-machine
    | API calls (workspace listing, page search). Generate this token in Codex's
    | profile → API Tokens section and set it here.
    |
    */
    'token' => env('CODEX_TOKEN', ''),

];
