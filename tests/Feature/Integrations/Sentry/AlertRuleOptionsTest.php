<?php

namespace Tests\Feature\Integrations\Sentry;

use App\Models\IssuePriority;
use App\Models\IssueType;
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

    public function test_endpoint_captures_first_installation_id_when_not_yet_recorded(): void
    {
        $s = app(SentrySettings::class);
        $s->installation_uuid = null;
        $s->save();

        $response = $this->get('/api/sentry/options/projects?installationId='.$this->installationUuid);

        $response->assertOk();
        $this->assertSame($this->installationUuid, app(SentrySettings::class)->refresh()->installation_uuid);
    }

    public function test_alert_rule_settings_endpoint_accepts_valid_configuration(): void
    {
        $project = Project::factory()->create();
        $type = IssueType::query()->where('key', 'BUG')->firstOrFail();
        $priority = IssuePriority::query()->where('key', 'HIGH')->firstOrFail();

        $response = $this->postJson('/api/sentry/alert-rule?installationId='.$this->installationUuid, [
            'forge_project_id' => (string) $project->id,
            'forge_issue_type_id' => (string) $type->id,
            'forge_priority_id' => (string) $priority->id,
        ]);

        $response->assertNoContent();
    }

    public function test_alert_rule_settings_endpoint_accepts_settings_array_payload(): void
    {
        $project = Project::factory()->create();
        $type = IssueType::query()->where('key', 'BUG')->firstOrFail();
        $priority = IssuePriority::query()->where('key', 'HIGH')->firstOrFail();

        $response = $this->postJson('/api/sentry/alert-rule?installationId='.$this->installationUuid, [
            'settings' => [
                ['name' => 'forge_project_id', 'value' => (string) $project->id],
                ['name' => 'forge_issue_type_id', 'value' => (string) $type->id],
                ['name' => 'forge_priority_id', 'value' => (string) $priority->id],
            ],
        ]);

        $response->assertNoContent();
    }

    public function test_alert_rule_settings_endpoint_returns_sentry_error_shape_for_invalid_configuration(): void
    {
        $type = IssueType::query()->where('key', 'BUG')->firstOrFail();
        $priority = IssuePriority::query()->where('key', 'HIGH')->firstOrFail();

        $response = $this->postJson('/api/sentry/alert-rule?installationId='.$this->installationUuid, [
            'forge_project_id' => 'missing-project',
            'forge_issue_type_id' => (string) $type->id,
            'forge_priority_id' => (string) $priority->id,
        ]);

        $response
            ->assertStatus(400)
            ->assertJson(['message' => 'Choose a valid Forge project.']);
    }
}
