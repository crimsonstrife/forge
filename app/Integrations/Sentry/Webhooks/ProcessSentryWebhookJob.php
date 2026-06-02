<?php

namespace App\Integrations\Sentry\Webhooks;

use App\Integrations\Sentry\Support\SentryHeaders;
use App\Integrations\Sentry\Support\SentrySyncContext;
use App\Models\WebhookDelivery;
use Illuminate\Support\Facades\Log;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;
use Throwable;

final class ProcessSentryWebhookJob extends ProcessWebhookJob
{
    public function handle(SentryEventRouter $router): void
    {
        $headers = (array) ($this->webhookCall->headers ?? []);
        $resource = SentryHeaders::value($headers, 'Sentry-Hook-Resource');
        $requestId = SentryHeaders::value($headers, 'Request-ID');
        $signature = SentryHeaders::value($headers, 'Sentry-Hook-Signature');

        $delivery = WebhookDelivery::query()->create([
            'provider' => 'sentry',
            'event_type' => $resource,
            'signature' => $signature,
            'headers' => $headers,
            'payload' => json_encode($this->webhookCall->payload ?? [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ]);

        try {
            SentrySyncContext::run(function () use ($router, $resource, $headers): void {
                $router->dispatch(
                    resource: (string) $resource,
                    payload: (array) ($this->webhookCall->payload ?? []),
                    headers: $headers,
                );
            });

            $delivery->update(['http_status' => 204]);
        } catch (Throwable $e) {
            $delivery->update([
                'http_status' => 500,
                'processing_error' => mb_strimwidth($e->getMessage(), 0, 8000),
            ]);
            Log::error('Sentry webhook processing failed', [
                'request_id' => $requestId,
                'resource' => $resource,
                'webhook_id' => $this->webhookCall->id,
                'exception' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
