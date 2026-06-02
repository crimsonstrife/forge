<?php

namespace App\Integrations\Sentry\Support;

/**
 * Header bag helpers for Sentry webhook payloads.
 *
 * Spatie's WebhookCall lowercases header keys when storing (Symfony Request
 * headers are lowercased on `->all()`). We normalize defensively at read time
 * so changes to webhook-client's `store_headers` casing can't silently null
 * our values.
 */
final class SentryHeaders
{
    /**
     * @param  array<string,mixed>  $headers
     * @return array<string,array<int,string>>
     */
    public static function normalize(array $headers): array
    {
        $out = [];
        foreach ($headers as $name => $value) {
            $key = strtolower((string) $name);
            $out[$key] = is_array($value) ? array_values($value) : [(string) $value];
        }

        return $out;
    }

    /**
     * @param  array<string,mixed>  $headers
     */
    public static function value(array $headers, string $name): ?string
    {
        $key = strtolower($name);

        if (isset($headers[$key])) {
            return self::firstValue($headers[$key]);
        }

        foreach ($headers as $headerName => $headerValue) {
            if (strtolower((string) $headerName) === $key) {
                return self::firstValue($headerValue);
            }
        }

        return null;
    }

    private static function firstValue(mixed $value): ?string
    {
        if (is_array($value)) {
            return isset($value[0]) ? (string) $value[0] : null;
        }

        return is_string($value) ? $value : null;
    }
}
