<?php

namespace Tests\Unit\Integrations\Sentry;

use App\Integrations\Sentry\Support\SentryHeaders;
use PHPUnit\Framework\TestCase;

class SentryHeadersTest extends TestCase
{
    public function test_value_is_case_insensitive_against_lowercase_storage(): void
    {
        $stored = [
            'sentry-hook-resource' => ['issue'],
            'request-id' => ['req-1'],
        ];

        $this->assertSame('issue', SentryHeaders::value($stored, 'Sentry-Hook-Resource'));
        $this->assertSame('req-1', SentryHeaders::value($stored, 'Request-ID'));
        $this->assertSame('issue', SentryHeaders::value($stored, 'sentry-hook-resource'));
    }

    public function test_value_handles_preserved_case_storage(): void
    {
        $stored = [
            'Sentry-Hook-Resource' => ['installation'],
            'Request-ID' => 'req-2',
        ];

        $this->assertSame('installation', SentryHeaders::value($stored, 'sentry-hook-resource'));
        $this->assertSame('req-2', SentryHeaders::value($stored, 'request-id'));
    }

    public function test_value_returns_null_when_missing(): void
    {
        $this->assertNull(SentryHeaders::value([], 'Sentry-Hook-Resource'));
    }

    public function test_normalize_lowercases_keys_and_wraps_scalar_values(): void
    {
        $out = SentryHeaders::normalize([
            'Sentry-Hook-Resource' => 'issue',
            'Request-ID' => ['req-3'],
        ]);

        $this->assertSame(['issue'], $out['sentry-hook-resource']);
        $this->assertSame(['req-3'], $out['request-id']);
    }
}
