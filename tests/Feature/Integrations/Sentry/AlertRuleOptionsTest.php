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

    private string $installationUuid = '9a89a822-0b62-4b62-9b99-905b9d742dd1';

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
        $this->seed(IssueEnumsSeeder::class);

        $s = app(SentrySettings::class);
        $s->enabled = true;
        $s->org_slug = 'acme';
        $s->client_id = 'cid';
        $s->client_secret = 'cs';
        $s->auth_token = 'tok';
        $s->api_base = 'https://sentry.io/api/0';
        $s->installation_uuid = $this->installationUuid;
        $s->save();
    }

    public function test_projects_endpoint_returns_choices_when_installation_id_matches(): void
    {
        Project::factory()->count(2)->create();

        $response = $this->get('/api/sentry/options/projects?installationId='.$this->installationUuid);

        $response->assertOk();
        $json = $response->json();
        $this->assertArrayHasKey('choices', $json);
        $this->assertCount(2, $json['choices']);
        $this->assertCount(2, $json['choices'][0], 'each choice should be [id, label]');
    }

    public function test_issue_types_endpoint_returns_seeded_types(): void
    {
        $response = $this->get('/api/sentry/options/issue-types?installationId='.$this->installationUuid);

        $response->assertOk();
        $labels = array_column($response->json('choices'), 1);
        $this->assertContains('Bug', $labels);
        $this->assertContains('Task', $labels);
    }

    public function test_priorities_endpoint_returns_seeded_priorities(): void
    {
        $response = $this->get('/api/sentry/options/priorities?installationId='.$this->installationUuid);

        $response->assertOk();
        $labels = array_column($response->json('choices'), 1);
        $this->assertContains('High', $labels);
        $this->assertContains('Low', $labels);
    }

    public function test_endpoint_rejects_request_with_wrong_installation_id(): void
    {
        $response = $this->get('/api/sentry/options/projects?installationId=not-the-right-uuid');
        $this->assertSame(401, $response->getStatusCode());
    }

    public function test_endpoint_rejects_request_with_no_installation_id(): void
    {
        $response = $this->get('/api/sentry/options/projects');
        $this->assertSame(401, $response->getStatusCode());
    }

    public function test_endpoint_rejects_when_integration_disabled(): void
    {
        $s = app(SentrySettings::class);
        $s->enabled = false;
        $s->save();

        $response = $this->get('/api/sentry/options/projects?installationId='.$this->installationUuid);
        $this->assertSame(403, $response->getStatusCode());
    }

    public function test_endpoint_rejects_when_installation_not_yet_recorded(): void
    {
        $s = app(SentrySettings::class);
        $s->installation_uuid = null;
        $s->save();

        $response = $this->get('/api/sentry/options/projects?installationId='.$this->installationUuid);
        $this->assertSame(403, $response->getStatusCode());
    }
}
