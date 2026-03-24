<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Crucible Integration Enabled
    |--------------------------------------------------------------------------
    |
    | Enables linking Forge projects to Crucible repositories.
    |
    */
    'enabled' => env('CRUCIBLE_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Crucible Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL of the connected Crucible installation.
    | Example: https://repos.example.com
    |
    */
    'url' => env('CRUCIBLE_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Crucible App Token
    |--------------------------------------------------------------------------
    |
    | A Sanctum token issued by Crucible with access to list and inspect
    | repositories for linking.
    |
    */
    'app_token' => env('CRUCIBLE_APP_TOKEN', ''),

];
