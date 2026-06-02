<?php

namespace App\Integrations\Sentry\Webhooks;

use App\Integrations\Sentry\Services\SentryInstallationService;
use App\Integrations\Sentry\Services\SentryIssueSyncService;
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
     * @param  array<string,array<int,string>>  $headers
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
     * @param  array<string,array<int,string>>  $headers
     */
    private function logUnhandled(string $resource, array $headers): void
    {
        $requestId = $headers['request-id'][0] ?? null;
        Log::info('Sentry webhook resource not handled', [
            'resource' => $resource,
            'request_id' => $requestId,
        ]);
    }
}
