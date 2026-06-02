<?php

namespace App\Integrations\Sentry\Webhooks;

use App\Integrations\Sentry\Support\SentrySyncContext;
use App\Models\WebhookDelivery;
use Illuminate\Support\Facades\Log;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;
use Throwable;

final class ProcessSentryWebhookJob extends ProcessWebhookJob
{
    public function handle(SentryEventRouter $router): void
    {
        $resource = $this->headerValue('sentry-hook-resource');
        $requestId = $this->headerValue('request-id');

        $delivery = WebhookDelivery::query()->create([
            'provider' => 'sentry',
            'event_type' => $resource,
            'signature' => $this->headerValue('sentry-hook-signature'),
            'headers' => $this->webhookCall->headers ?? [],
            'payload' => json_encode($this->webhookCall->payload ?? [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ]);

        try {
            SentrySyncContext::run(function () use ($router, $resource): void {
                $router->dispatch(
                    resource: (string) $resource,
                    payload: (array) ($this->webhookCall->payload ?? []),
                    headers: (array) ($this->webhookCall->headers ?? []),
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

    private function headerValue(string $name): ?string
    {
        $headers = (array) ($this->webhookCall->headers ?? []);
        $values = $headers[$name] ?? null;
        if (is_array($values)) {
            return (string) ($values[0] ?? '');
        }

        return is_string($values) ? $values : null;
    }
}
