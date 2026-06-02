<?php

namespace Tests\Feature\Integrations\Sentry;

use App\Models\Issue;
use App\Models\IssueExternalRef;
use App\Models\IssuePriority;
use App\Models\IssueType;
use App\Models\Project;
use App\Models\WebhookDelivery;
use App\Settings\SentrySettings;
use Database\Seeders\IssueEnumsSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\SentryIntegrationUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InboundWebhookTest extends TestCase
{
    use RefreshDatabase;

    private string $clientSecret = 'sentry-shh-secret';

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
        $this->seed(IssueEnumsSeeder::class);
        $this->seed(SentryIntegrationUserSeeder::class);

        $settings = app(SentrySettings::class);
        $settings->enabled = true;
        $settings->org_slug = 'acme';
        $settings->client_id = 'cid';
        $settings->client_secret = $this->clientSecret;
        $settings->auth_token = 'tok';
        $settings->api_base = 'https://sentry.io/api/0';
        $settings->save();
    }

    public function test_signed_event_alert_creates_forge_issue_and_external_ref(): void
    {
        $project = Project::factory()->create();
        $type = IssueType::query()->where('key', 'BUG')->first();
        $prio = IssuePriority::query()->where('key', 'HIGH')->first();

        $s = app(SentrySettings::class);
        $s->default_project_id = (string) $project->id;
        $s->default_issue_type_id = $type->id;
        $s->default_priority_id = $prio->id;
        $s->save();

        $payload = [
            'action' => 'triggered',
            'data' => [
                'issue' => [
                    'id' => '900001',
                    'title' => 'TypeError in checkout',
                    'culprit' => 'App\\Checkout::pay',
                    'level' => 'error',
                    'web_url' => 'https://sentry.io/issues/900001/',
                ],
            ],
            'installation' => ['uuid' => 'uuid-1'],
            'actor' => ['type' => 'application', 'id' => 'sentry', 'name' => 'Sentry'],
        ];
        $body = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $sig = hash_hmac('sha256', $body, $this->clientSecret);

        $response = $this->call(
            'POST',
            '/api/webhooks/sentry',
            [], [], [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_SENTRY_HOOK_RESOURCE' => 'event_alert',
                'HTTP_SENTRY_HOOK_SIGNATURE' => $sig,
                'HTTP_SENTRY_HOOK_TIMESTAMP' => (string) now()->getTimestamp(),
                'HTTP_REQUEST_ID' => 'req-1',
            ],
            $body,
        );

        $this->assertLessThan(400, $response->getStatusCode(), 'expected 2xx, got: '.$response->getStatusCode().' body='.$response->getContent());

        $this->assertDatabaseHas('webhook_deliveries', [
            'provider' => 'sentry',
            'event_type' => 'event_alert',
        ]);
        $delivery = WebhookDelivery::query()->where('provider', 'sentry')->first();
        $this->assertSame(204, (int) $delivery->http_status);
        $this->assertNull($delivery->processing_error);

        $ref = IssueExternalRef::query()
            ->where('provider', 'sentry')
            ->where('external_issue_id', '900001')
            ->first();

        $this->assertNotNull($ref);
        $this->assertSame('https://sentry.io/issues/900001/', $ref->url);

        $issue = Issue::query()->find($ref->issue_id);
        $this->assertNotNull($issue);
        $this->assertSame((string) $project->id, (string) $issue->project_id);
        $this->assertStringContainsString('TypeError in checkout', (string) $issue->summary);
    }

    public function test_invalid_signature_is_rejected_and_no_issue_created(): void
    {
        $project = Project::factory()->create();
        $s = app(SentrySettings::class);
        $s->default_project_id = (string) $project->id;
        $s->save();

        $payload = [
            'action' => 'triggered',
            'data' => ['issue' => ['id' => '900002', 'title' => 't']],
        ];
        $body = json_encode($payload);

        $response = $this->call(
            'POST',
            '/api/webhooks/sentry',
            [], [], [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_SENTRY_HOOK_RESOURCE' => 'event_alert',
                'HTTP_SENTRY_HOOK_SIGNATURE' => 'wrong-signature-here',
            ],
            $body,
        );

        $this->assertGreaterThanOrEqual(400, $response->getStatusCode(), 'expected 4xx/5xx for bad sig');
        $this->assertDatabaseMissing('issue_external_refs', [
            'provider' => 'sentry',
            'external_issue_id' => '900002',
        ]);
    }

    public function test_installation_created_captures_uuid_on_settings(): void
    {
        $payload = [
            'action' => 'created',
            'installation' => ['uuid' => 'install-uuid-xyz'],
            'data' => [],
        ];
        $body = json_encode($payload);
        $sig = hash_hmac('sha256', $body, $this->clientSecret);

        $response = $this->call(
            'POST',
            '/api/webhooks/sentry',
            [], [], [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_SENTRY_HOOK_RESOURCE' => 'installation',
                'HTTP_SENTRY_HOOK_SIGNATURE' => $sig,
            ],
            $body,
        );

        $this->assertLessThan(400, $response->getStatusCode());
        $this->assertSame('install-uuid-xyz', app(SentrySettings::class)->refresh()->installation_uuid);
    }
}
