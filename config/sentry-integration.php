<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Sentry Internal Integration
    |--------------------------------------------------------------------------
    |
    | Runtime configuration for the Sentry internal integration. Secrets and
    | per-tenant settings live in App\Settings\SentrySettings (DB-backed,
    | encrypted). Only invariants live here.
    |
    */

    'api_base' => env('SENTRY_INTEGRATION_API_BASE', 'https://sentry.io/api/0'),

    'queue' => env('SENTRY_INTEGRATION_QUEUE', null),

    'system_user_email' => env('SENTRY_INTEGRATION_SYSTEM_USER_EMAIL', 'forge-system+sentry@forge.local'),

    'system_user_name' => env('SENTRY_INTEGRATION_SYSTEM_USER_NAME', 'Sentry Integration'),

    /*
     * Sentry webhooks must be acknowledged within 1 second. The signature
     * validator + ProcessSentryWebhookJob are designed to push processing
     * onto this queue connection and respond immediately.
     */
    'webhook_request_timeout_seconds' => 1,
];
