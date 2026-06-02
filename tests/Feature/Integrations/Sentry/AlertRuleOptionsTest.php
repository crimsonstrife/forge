<?php

namespace Tests\Feature\Integrations\Sentry;

use App\Models\Project;
use App\Settings\SentrySettings;
use Database\Seeders\IssueEnumsSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlertRuleOptionsTest extends TestCase
{
    use RefreshDatabase;

    private string $clientSecret = 'sentry-shh-secret';

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
        $this->seed(IssueEnumsSeeder::class);

        $s = app(SentrySettings::class);
        $s->enabled = true;
        $s->org_slug = 'acme';
        $s->client_id = 'cid';
        $s->client_secret = $this->clientSecret;
        $s->auth_token = 'tok';
        $s->api_base = 'https://sentry.io/api/0';
        $s->save();
    }

    public function test_projects_endpoint_returns_choices_when_signed(): void
    {
        Project::factory()->count(2)->create();

        $sig = hash_hmac('sha256', '', $this->clientSecret);
        $response = $this->call('GET', '/api/sentry/options/projects', [], [], [], [
            'HTTP_SENTRY_HOOK_SIGNATURE' => $sig,
        ]);

        $response->assertOk();
        $json = $response->json();
        $this->assertArrayHasKey('choices', $json);
        $this->assertCount(2, $json['choices']);
        $this->assertCount(2, $json['choices'][0], 'each choice should be [id, label]');
    }

    public function test_issue_types_endpoint_returns_seeded_types(): void
    {
        $sig = hash_hmac('sha256', '', $this->clientSecret);
        $response = $this->call('GET', '/api/sentry/options/issue-types', [], [], [], [
            'HTTP_SENTRY_HOOK_SIGNATURE' => $sig,
        ]);

        $response->assertOk();
        $labels = array_column($response->json('choices'), 1);
        $this->assertContains('Bug', $labels);
        $this->assertContains('Task', $labels);
    }

    public function test_priorities_endpoint_returns_seeded_priorities(): void
    {
        $sig = hash_hmac('sha256', '', $this->clientSecret);
        $response = $this->call('GET', '/api/sentry/options/priorities', [], [], [], [
            'HTTP_SENTRY_HOOK_SIGNATURE' => $sig,
        ]);

        $response->assertOk();
        $labels = array_column($response->json('choices'), 1);
        $this->assertContains('High', $labels);
        $this->assertContains('Low', $labels);
    }

    public function test_endpoint_rejects_request_with_bad_signature(): void
    {
        $response = $this->call('GET', '/api/sentry/options/projects', [], [], [], [
            'HTTP_SENTRY_HOOK_SIGNATURE' => 'bogus',
        ]);

        $this->assertSame(401, $response->getStatusCode());
    }

    public function test_endpoint_rejects_when_integration_disabled(): void
    {
        $s = app(SentrySettings::class);
        $s->enabled = false;
        $s->save();

        $sig = hash_hmac('sha256', '', $this->clientSecret);
        $response = $this->call('GET', '/api/sentry/options/projects', [], [], [], [
            'HTTP_SENTRY_HOOK_SIGNATURE' => $sig,
        ]);

        $this->assertSame(403, $response->getStatusCode());
    }
}
