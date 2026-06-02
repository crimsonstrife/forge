<?php

namespace App\Integrations\Sentry\Webhooks;

use App\Settings\SentrySettings;
use Illuminate\Http\Request;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;
use Spatie\WebhookClient\WebhookConfig;

final class SentrySignatureValidator implements SignatureValidator
{
    public function isValid(Request $request, WebhookConfig $config): bool
    {
        $settings = app(SentrySettings::class);

        if (! $settings->enabled) {
            return false;
        }

        $secret = (string) ($settings->client_secret ?? '');
        if ($secret === '') {
            return false;
        }

        $provided = (string) $request->header($config->signatureHeaderName, '');
        if ($provided === '') {
            return false;
        }

        $expected = hash_hmac('sha256', $request->getContent(), $secret);

        return hash_equals($expected, $provided);
    }
}
