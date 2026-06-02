<?php

namespace Tests\Unit\Integrations\Sentry;

use App\Integrations\Sentry\Webhooks\SentrySignatureValidator;
use App\Settings\SentrySettings;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Mockery;
use PHPUnit\Framework\TestCase;
use Spatie\WebhookClient\WebhookConfig;

class SentrySignatureValidatorTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_returns_true_for_valid_hmac_sha256_signature(): void
    {
        $secret = 'shh-its-a-secret';
        $body = '{"action":"created","installation":{"uuid":"abc"}}';
        $sig = hash_hmac('sha256', $body, $secret);

        $request = Request::create('/api/webhooks/sentry', 'POST', [], [], [], [], $body);
        $request->headers->set('Sentry-Hook-Signature', $sig);

        $config = $this->makeConfig();
        $this->bindSettings(enabled: true, secret: $secret);

        $this->assertTrue((new SentrySignatureValidator)->isValid($request, $config));
    }

    public function test_returns_false_when_body_is_tampered(): void
    {
        $secret = 'shh';
        $body = '{"action":"created"}';
        $sig = hash_hmac('sha256', $body, $secret);

        $request = Request::create('/api/webhooks/sentry', 'POST', [], [], [], [], $body.'tampered');
        $request->headers->set('Sentry-Hook-Signature', $sig);

        $config = $this->makeConfig();
        $this->bindSettings(enabled: true, secret: $secret);

        $this->assertFalse((new SentrySignatureValidator)->isValid($request, $config));
    }

    public function test_returns_false_when_integration_disabled(): void
    {
        $secret = 'shh';
        $body = '{}';
        $sig = hash_hmac('sha256', $body, $secret);

        $request = Request::create('/api/webhooks/sentry', 'POST', [], [], [], [], $body);
        $request->headers->set('Sentry-Hook-Signature', $sig);

        $config = $this->makeConfig();
        $this->bindSettings(enabled: false, secret: $secret);

        $this->assertFalse((new SentrySignatureValidator)->isValid($request, $config));
    }

    public function test_returns_false_when_secret_empty(): void
    {
        $body = '{}';
        $request = Request::create('/api/webhooks/sentry', 'POST', [], [], [], [], $body);
        $request->headers->set('Sentry-Hook-Signature', 'anything');

        $config = $this->makeConfig();
        $this->bindSettings(enabled: true, secret: '');

        $this->assertFalse((new SentrySignatureValidator)->isValid($request, $config));
    }

    public function test_returns_false_when_header_missing(): void
    {
        $request = Request::create('/api/webhooks/sentry', 'POST', [], [], [], [], '{}');

        $config = $this->makeConfig();
        $this->bindSettings(enabled: true, secret: 'shh');

        $this->assertFalse((new SentrySignatureValidator)->isValid($request, $config));
    }

    private function makeConfig(): WebhookConfig
    {
        $config = Mockery::mock(WebhookConfig::class);
        $config->signatureHeaderName = 'Sentry-Hook-Signature';

        return $config;
    }

    private function bindSettings(bool $enabled, string $secret): void
    {
        $settings = new \stdClass;
        $settings->enabled = $enabled;
        $settings->client_secret = $secret;

        $container = Container::getInstance();
        $container->instance(SentrySettings::class, $settings);
    }
}
