<?php

namespace App\Http\Middleware;

use App\Settings\SentrySettings;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Auth for Sentry UI-component option lookups (e.g. /api/sentry/options/*).
 *
 * Sentry signs webhook deliveries (POSTs with a body) using HMAC-SHA256, but
 * UI component select-option requests are GETs with no body. Instead, Sentry
 * appends an `installationId` query parameter that identifies the installed
 * integration. We validate that against the recorded installation UUID, or
 * capture the first one Sentry sends when the webhook job has not run yet.
 */
final class VerifySentryInstallation
{
    public function handle(Request $request, Closure $next): Response
    {
        $settings = app(SentrySettings::class);

        if (! $settings->enabled) {
            return response('Sentry integration disabled', 403);
        }

        $provided = (string) $request->query('installationId', '');
        if ($provided === '') {
            return response('Missing installationId', 401);
        }

        $expected = (string) ($settings->installation_uuid ?? '');
        if ($expected === '') {
            $settings->installation_uuid = $provided;
            $settings->save();

            Log::info('Sentry installation UUID captured from options request', [
                'installation_uuid_hash' => hash('sha256', $provided),
            ]);

            return $next($request);
        }

        if (! hash_equals($expected, $provided)) {
            return response('Unknown installationId', 401);
        }

        return $next($request);
    }
}
