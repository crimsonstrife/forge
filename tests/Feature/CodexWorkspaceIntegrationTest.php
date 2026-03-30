<?php

namespace Tests\Feature;

use App\Livewire\Projects\ConnectCodexWorkspace;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use App\Settings\CodexSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class CodexWorkspaceIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['is-super-admin', 'projects.manage'] as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_workspace_lookup_uses_the_database_backed_codex_token(): void
    {
        [$user, $project] = $this->projectManagerContext();

        config()->set('codex.enabled', true);
        config()->set('codex.url', 'https://legacy-codex.test');
        config()->set('codex.app_token', 'legacy-token');

        $settings = app(CodexSettings::class);
        $settings->enabled = true;
        $settings->url = 'https://codex.test';
        $settings->token = 'settings-token';
        $settings->save();

        $this->actingAs($user);

        Http::fake(function (HttpRequest $request) use ($user) {
            $query = [];
            parse_str(parse_url($request->url(), PHP_URL_QUERY) ?? '', $query);

            $this->assertSame('Bearer settings-token', $request->header('Authorization')[0] ?? null);
            $this->assertStringStartsWith('https://codex.test/api/v1/workspaces', $request->url());
            $this->assertSame((string) $user->id, $query['for_forge_user_id'] ?? null);

            return Http::response([
                'data' => [
                    [
                        'id' => 'workspace-1',
                        'name' => 'Platform Docs',
                        'slug' => 'platform-docs',
                    ],
                ],
            ]);
        });

        $component = app(ConnectCodexWorkspace::class);
        $component->mount($project);
        $component->search = 'platform';
        $component->loadWorkspaces();

        $this->assertNull($component->error);
        $this->assertSame('workspace-1', data_get($component->workspaces, '0.id'));
        $this->assertSame('platform-docs', data_get($component->workspaces, '0.slug'));
    }

    public function test_workspace_lookup_logs_failed_codex_responses(): void
    {
        [$user, $project] = $this->projectManagerContext();

        $settings = app(CodexSettings::class);
        $settings->enabled = true;
        $settings->url = 'https://codex.test';
        $settings->token = 'settings-token';
        $settings->save();

        Log::spy();
        $this->actingAs($user);

        Http::fake([
            'https://codex.test/api/v1/workspaces*' => Http::response([
                'message' => 'Unauthenticated.',
            ], 401),
        ]);

        $component = app(ConnectCodexWorkspace::class);
        $component->mount($project);
        $component->loadWorkspaces();

        $this->assertSame('Failed to load workspaces from Codex (HTTP 401).', $component->error);
        $this->assertSame([], $component->workspaces);

        Log::shouldHaveReceived('warning')
            ->once()
            ->withArgs(function (string $message, array $context) use ($project, $user): bool {
                return $message === 'Codex workspace lookup failed'
                    && ($context['status'] ?? null) === 401
                    && ($context['project_id'] ?? null) === (string) $project->id
                    && ($context['forge_user_id'] ?? null) === (string) $user->id
                    && str_contains((string) ($context['response_body'] ?? ''), 'Unauthenticated');
            });
    }

    /**
     * @return array{0:User,1:Project}
     */
    private function projectManagerContext(): array
    {
        $user = User::factory()->withPersonalTeam()->create();
        $team = $user->ownedTeams()->firstOrFail();

        $user->forceFill(['current_team_id' => $team->id])->save();

        app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);
        $user->givePermissionTo('projects.manage');

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

        return [$user, $project];
    }
}
