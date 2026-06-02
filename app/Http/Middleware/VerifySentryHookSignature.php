<?php

namespace App\Http\Middleware;

use App\Integrations\Sentry\Support\SentryRequestSignature;
use App\Settings\SentrySettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Used by the alert-rule options endpoints, which Sentry signs the same way
 * as webhooks (HMAC-SHA256 of the raw body using the integration's client
 * secret). The Spatie webhook-client config handles signing for /webhooks/sentry;
 * this middleware applies the same check to GET option lookups.
 */
final class VerifySentryHookSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $settings = app(SentrySettings::class);

        if (! $settings->enabled) {
            return response('Sentry integration disabled', 403);
        }

        if (filled($settings->client_secret ?? null) === false) {
            return response('Sentry integration missing client secret', 403);
        }

        $provided = (string) $request->header(SentryRequestSignature::HEADER, '');
        if ($provided === '') {
            return response('Missing '.SentryRequestSignature::HEADER, 401);
        }

        if (! SentryRequestSignature::verify($request, $provided)) {
            return response('Invalid '.SentryRequestSignature::HEADER, 401);
        }

        return $next($request);
    }
}
