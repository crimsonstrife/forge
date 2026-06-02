<?php

namespace App\Integrations\Sentry\Support;

/**
 * Tracks whether the current request/job stack is mutating Forge models in
 * response to a Sentry webhook. The Sentry observer consults this to avoid
 * re-emitting outbound calls (Sentry → Forge → Sentry loop).
 */
final class SentrySyncContext
{
    private static int $depth = 0;

    public static function push(): void
    {
        self::$depth++;
    }

    public static function pop(): void
    {
        if (self::$depth > 0) {
            self::$depth--;
        }
    }

    public static function isActive(): bool
    {
        return self::$depth > 0;
    }

    /**
     * Run a callback inside the suppression scope.
     *
     * @template T
     *
     * @param  callable():T  $callback
     * @return T
     */
    public static function run(callable $callback): mixed
    {
        self::push();
        try {
            return $callback();
        } finally {
            self::pop();
        }
    }
}
