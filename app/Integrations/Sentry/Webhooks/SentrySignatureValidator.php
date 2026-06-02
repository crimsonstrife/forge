<?php

namespace App\Integrations\Sentry\Webhooks;

use App\Integrations\Sentry\Support\SentryRequestSignature;
use Illuminate\Http\Request;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;
use Spatie\WebhookClient\WebhookConfig;

final class SentrySignatureValidator implements SignatureValidator
{
    public function isValid(Request $request, WebhookConfig $config): bool
    {
        $provided = (string) $request->header($config->signatureHeaderName, '');

        return SentryRequestSignature::verify($request, $provided);
    }
}
