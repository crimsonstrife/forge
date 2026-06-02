<?php

namespace App\Integrations\Sentry\Webhooks;

use App\Integrations\Sentry\Services\SentryInstallationService;
use App\Integrations\Sentry\Services\SentryIssueSyncService;
use App\Integrations\Sentry\Support\SentryHeaders;
use Illuminate\Support\Facades\Log;

final class SentryEventRouter
{
    public function __construct(
        private readonly SentryIssueSyncService $issues,
        private readonly SentryInstallationService $installations,
    ) {
    }

    /**
     * @param  array<string,mixed>  $payload
     * @param  array<string,mixed>  $headers
     */
    public function dispatch(string $resource, array $payload, array $headers): void
    {
        match ($resource) {
            'installation' => $this->installations->handle($payload),
            'event_alert' => $this->issues->upsertFromAlert($payload),
            'issue' => $this->issues->syncStatus($payload),
            default => $this->logUnhandled($resource, $headers),
        };
    }

    /**
     * @param  array<string,mixed>  $headers
     */
    private function logUnhandled(string $resource, array $headers): void
    {
        Log::info('Sentry webhook resource not handled', [
            'resource' => $resource,
            'request_id' => SentryHeaders::value($headers, 'Request-ID'),
        ]);
    }
}
