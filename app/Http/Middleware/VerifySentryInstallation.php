<?php

namespace App\Http\Middleware;

use App\Settings\SentrySettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Auth for Sentry UI-component option lookups (e.g. /api/sentry/options/*).
 *
 * Sentry signs webhook deliveries (POSTs with a body) using HMAC-SHA256, but
 * UI component select-option requests are GETs with no body. Instead, Sentry
 * appends an `installationId` query parameter that identifies the installed
 * integration. We validate that against the installation UUID Sentry sent us
 * via the 'installation.created' webhook.
 */
final class VerifySentryInstallation
{
    public function handle(Request $request, Closure $next): Response
    {
        $settings = app(SentrySettings::class);

        if (! $settings->enabled) {
            return response('Sentry integration disabled', 403);
        }

        $expected = (string) ($settings->installation_uuid ?? '');
        if ($expected === '') {
            return response('Sentry installation not yet recorded', 403);
        }

        $provided = (string) $request->query('installationId', '');
        if ($provided === '') {
            return response('Missing installationId', 401);
        }

        if (! hash_equals($expected, $provided)) {
            return response('Unknown installationId', 401);
        }

        return $next($request);
    }
}
