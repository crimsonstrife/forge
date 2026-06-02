<?php

namespace App\Integrations\Sentry\Support;

use App\Settings\SentrySettings;
use Illuminate\Http\Request;

/**
 * HMAC-SHA256 verification for Sentry-signed requests (webhook deliveries and
 * alert-rule option lookups). Shared by SentrySignatureValidator (Spatie
 * webhook profile) and the VerifySentryHookSignature middleware so the two
 * paths cannot drift.
 */
final class SentryRequestSignature
{
    public const HEADER = 'Sentry-Hook-Signature';

    public static function verify(Request $request, ?string $providedHeader = null): bool
    {
        $settings = app(SentrySettings::class);

        if (! $settings->enabled) {
            return false;
        }

        $secret = (string) ($settings->client_secret ?? '');
        if ($secret === '') {
            return false;
        }

        $provided = $providedHeader !== null
            ? $providedHeader
            : (string) $request->header(self::HEADER, '');
        if ($provided === '') {
            return false;
        }

        $expected = hash_hmac('sha256', $request->getContent(), $secret);

        return hash_equals($expected, $provided);
    }
}
