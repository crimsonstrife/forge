<?php

namespace App\Http\Middleware;

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

        $secret = (string) ($settings->client_secret ?? '');
        if ($secret === '') {
            return response('Sentry integration missing client secret', 403);
        }

        $provided = (string) $request->header('Sentry-Hook-Signature', '');
        if ($provided === '') {
            return response('Missing Sentry-Hook-Signature', 401);
        }

        $expected = hash_hmac('sha256', $request->getContent(), $secret);
        if (! hash_equals($expected, $provided)) {
            return response('Invalid Sentry-Hook-Signature', 401);
        }

        return $next($request);
    }
}
