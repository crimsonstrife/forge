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
    | An application-level token issued by Crucible for Forge machine-to-
    | machine calls. Forge uses it to list repositories and inspect branch /
    | pull request data while Crucible scopes the results to a Forge user.
    |
    */
    'app_token' => env('CRUCIBLE_APP_TOKEN', ''),

];
