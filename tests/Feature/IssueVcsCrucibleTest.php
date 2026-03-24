<?php

namespace Tests\Feature;

use App\Models\Issue;
use App\Models\IssuePriority;
use App\Models\IssueStatus;
use App\Models\IssueType;
use App\Models\Permission;
use App\Models\Project;
use App\Models\ProjectRepository;
use App\Models\Repository;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class IssueVcsCrucibleTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_searches_crucible_pull_requests_with_the_current_forge_user_scope(): void
    {
        [$user, , $issue, $repository] = $this->issueContext(['projects.view']);

        config()->set('crucible.enabled', true);
        config()->set('crucible.url', 'https://crucible.test');
        config()->set('crucible.app_token', 'crucible-app-token');

        Http::fake(function (HttpRequest $request) use ($user) {
            $query = [];
            parse_str(parse_url($request->url(), PHP_URL_QUERY) ?? '', $query);

            $this->assertSame('Bearer crucible-app-token', $request->header('Authorization')[0] ?? null);
            $this->assertSame((string) $user->id, $query['for_forge_user_id'] ?? null);
            $this->assertSame('FORGE-7', $query['q'] ?? null);

            return Http::response([
                'data' => [
                    [
                        'number' => 17,
                        'title' => '[FORGE-7] Wire up integration',
                        'state' => 'open',
                        'head' => ['ref' => 'feature/forge-7'],
                        'base' => ['ref' => 'main'],
                        'web_url' => 'https://crucible.test/acme/platform/pull-requests/17',
                    ],
                ],
            ]);
        });

        $response = $this->actingAs($user)->getJson(route('issues.vcs.pulls.search', ['issue' => $issue]) . '?' . http_build_query([
            'repository_id' => $repository->id,
            'q' => 'FORGE-7',
        ]));

        $response
            ->assertOk()
            ->assertJsonPath('0.number', 17)
            ->assertJsonPath('0.title', '[FORGE-7] Wire up integration')
            ->assertJsonPath('0.head', 'feature/forge-7')
            ->assertJsonPath('0.base', 'main')
            ->assertJsonPath('0.url', 'https://crucible.test/acme/platform/pull-requests/17');
    }

    public function test_it_links_crucible_pull_requests_without_a_stored_project_token(): void
    {
        [$user, , $issue, $repository] = $this->issueContext(['projects.view', 'projects.manage']);

        $response = $this->actingAs($user)->postJson(route('issues.vcs.link.pr', ['issue' => $issue]), [
            'repository_id' => $repository->id,
            'number' => 17,
            'title' => '[FORGE-7] Wire up integration',
            'state' => 'open',
            'url' => 'https://crucible.test/acme/platform/pull-requests/17',
            'payload' => [
                'number' => 17,
                'title' => '[FORGE-7] Wire up integration',
            ],
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('number', 17)
            ->assertJsonPath('type', 'pull_request');

        $this->assertDatabaseHas('issue_vcs_links', [
            'issue_id' => $issue->id,
            'repository_id' => $repository->id,
            'type' => 'pull_request',
            'number' => 17,
        ]);
    }

    /**
     * @param  array<int, string>  $permissions
     * @return array{0:User,1:Project,2:Issue,3:Repository}
     */
    private function issueContext(array $permissions): array
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $user = User::factory()->create();
        $team = Team::factory()->create([
            'user_id' => $user->id,
            'personal_team' => true,
        ]);

        $user->forceFill(['current_team_id' => $team->id])->save();

        foreach (array_unique(array_merge(['is-admin', 'is-super-admin'], $permissions)) as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);
        $user->givePermissionTo(array_values(array_unique(array_merge($permissions, ['is-super-admin']))));
        app(PermissionRegistrar::class)->setPermissionsTeamId(null);

        $superAdminRole = Role::query()->firstOrCreate([
            'name' => 'SuperAdmin',
            'guard_name' => 'web',
        ]);

        $superAdminRole->givePermissionTo('is-super-admin');
        $user->assignRole($superAdminRole);

        app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);

        $project = Project::factory()->create([
            'lead_id' => $user->id,
        ]);

        $status = IssueStatus::query()->create([
            'name' => 'To Do',
            'key' => 'TODO',
            'color' => '#2563eb',
            'order' => 1,
            'is_done' => false,
        ]);

        $type = IssueType::query()->create([
            'key' => 'TASK',
            'name' => 'Task',
            'icon' => 'check_box',
            'is_default' => true,
            'is_hierarchical' => false,
            'tier' => 'task',
        ]);

        $priority = IssuePriority::query()->create([
            'key' => 'MEDIUM',
            'name' => 'Medium',
            'order' => 1,
            'weight' => 1,
            'color' => '#6b7280',
            'icon' => 'flag',
        ]);

        $issue = Issue::query()->create([
            'project_id' => $project->id,
            'issue_type_id' => $type->id,
            'issue_status_id' => $status->id,
            'issue_priority_id' => $priority->id,
            'reporter_id' => $user->id,
            'summary' => 'Wire up integration',
        ]);

        $repository = Repository::query()->create([
            'provider' => 'crucible',
            'host' => 'crucible.test',
            'owner' => 'acme',
            'name' => 'platform',
            'external_id' => 'repo-123',
            'default_branch' => 'main',
            'meta' => [
                'web_url' => 'https://crucible.test/acme/platform',
            ],
        ]);

        ProjectRepository::query()->create([
            'project_id' => $project->id,
            'repository_id' => $repository->id,
            'integrator_user_id' => $user->id,
            'token' => null,
            'token_type' => null,
        ]);

        return [$user, $project, $issue, $repository];
    }
}
