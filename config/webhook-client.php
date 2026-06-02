<?php

return [
    'configs' => [
        [
            'name' => 'sentry',
            'signing_secret' => null,
            'signature_header_name' => 'Sentry-Hook-Signature',
            'signature_validator' => \App\Integrations\Sentry\Webhooks\SentrySignatureValidator::class,
            'webhook_profile' => \Spatie\WebhookClient\WebhookProfile\ProcessEverythingWebhookProfile::class,
            'webhook_response' => \Spatie\WebhookClient\WebhookResponse\DefaultRespondsTo::class,
            'webhook_model' => \Spatie\WebhookClient\Models\WebhookCall::class,
            'store_headers' => [
                'Sentry-Hook-Resource',
                'Sentry-Hook-Timestamp',
                'Sentry-Hook-Signature',
                'Request-ID',
                'Content-Type',
            ],
            'process_webhook_job' => \App\Integrations\Sentry\Webhooks\ProcessSentryWebhookJob::class,
        ],
    ],

    /*
     * The integer amount of days after which models should be deleted.
     */
    'delete_after_days' => 30,

    'add_unique_token_to_route_name' => false,
];
