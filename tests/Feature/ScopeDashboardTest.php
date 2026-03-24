<?php

namespace Tests\Feature;

use App\Livewire\Organizations\Form as OrganizationForm;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Auth\Access\Response as AccessResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Laravel\Jetstream\Jetstream;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ScopeDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_organizations_only_show_when_the_user_has_visible_shared_work(): void
    {
        [$owner, $teammate, $team] = $this->teamContext();

        $visibleOrganization = Organization::factory()->create([
            'name' => 'Visible Organization',
        ]);

        $hiddenOrganization = Organization::factory()->create([
            'name' => 'Hidden Organization',
        ]);

        $visibleProject = Project::factory()->create([
            'name' => 'Shared Platform',
            'organization_id' => $visibleOrganization->id,
            'lead_id' => $owner->id,
        ]);

        $hiddenProject = Project::factory()
            ->for(User::factory(), 'lead')
            ->create([
                'name' => 'Private Audit',
                'organization_id' => $hiddenOrganization->id,
            ]);

        $this->attachTeamToProject($team, $visibleProject);
        $this->attachUserToProject($visibleProject, $owner);
        $this->attachUserToProject($hiddenProject, $owner);

        $this->actingAs($teammate)
            ->get('/organizations')
            ->assertOk()
            ->assertSee('Visible Organization')
            ->assertDontSee('Hidden Organization');

        $this->actingAs($teammate)
            ->get('/organizations/'.$visibleOrganization->slug)
            ->assertOk()
            ->assertSee('Organization dashboard')
            ->assertSee('Shared Platform')
            ->assertDontSee('Private Audit');

        $this->actingAs($teammate)
            ->get('/organizations/'.$hiddenOrganization->slug)
            ->assertForbidden();
    }

    public function test_team_dashboard_shows_shared_projects_and_members_for_team_members(): void
    {
        [$owner, $teammate, $team] = $this->teamContext();
        $otherTeam = Team::factory()->create([
            'user_id' => User::factory(),
            'personal_team' => false,
            'name' => 'Other Team',
        ]);

        $organization = Organization::factory()->create([
            'name' => 'Forge Ops',
        ]);

        $sharedProject = Project::factory()->create([
            'name' => 'Release Command Center',
            'organization_id' => $organization->id,
            'lead_id' => $owner->id,
        ]);

        $otherProject = Project::factory()
            ->for(User::factory(), 'lead')
            ->create([
                'name' => 'Other Team Work',
                'organization_id' => $organization->id,
            ]);

        $this->attachTeamToProject($team, $sharedProject);
        $this->attachTeamToProject($otherTeam, $otherProject);
        $this->attachUserToProject($sharedProject, $owner);

        $this->actingAs($teammate)
            ->get('/teams/'.$team->id.'/dashboard')
            ->assertOk()
            ->assertSee('Team dashboard')
            ->assertSee('Release Command Center')
            ->assertSee('Forge Ops')
            ->assertSee($teammate->email)
            ->assertDontSee('Other Team Work');
    }

    public function test_collaborator_role_is_registered_for_team_memberships(): void
    {
        $role = Jetstream::findRole('collaborator');

        $this->assertNotNull($role);
        $this->assertSame('Collaborator', $role->name);
        $this->assertSame(['read', 'create', 'update'], $role->permissions);
    }

    public function test_organization_routes_generate_from_the_bound_model(): void
    {
        $organization = Organization::factory()->create([
            'name' => 'Test Org',
        ]);

        $this->assertSame(
            url('/organizations/'.$organization->slug),
            route('organizations.show', ['organization' => $organization])
        );

        $this->assertSame(
            url('/organizations/'.$organization->slug.'/edit'),
            route('organizations.edit', ['organization' => $organization])
        );
    }

    public function test_organization_form_treats_unsaved_models_as_create_mode(): void
    {
        Gate::shouldReceive('authorize')
            ->once()
            ->with('create', Organization::class)
            ->andReturn(AccessResponse::allow());

        $component = app(OrganizationForm::class);
        $component->mount(new Organization);

        $this->assertFalse($component->isEditing);
        $this->assertNull($component->organization);
        $this->assertSame('', $component->name);
    }

    public function test_project_page_only_links_teams_the_viewer_can_open(): void
    {
        $this->ensurePermissionsExist();

        $lead = User::factory()->create();
        $viewer = User::factory()->create();
        $viewerTeam = Team::factory()->create([
            'user_id' => $viewer->id,
            'personal_team' => true,
        ]);
        $restrictedTeam = Team::factory()->create([
            'user_id' => $lead->id,
            'personal_team' => false,
            'name' => 'Restricted Team',
        ]);

        $viewer->forceFill(['current_team_id' => $viewerTeam->id])->save();
        app(PermissionRegistrar::class)->setPermissionsTeamId($viewerTeam->id);
        $viewer->givePermissionTo('projects.view');

        $project = Project::factory()
            ->for($lead, 'lead')
            ->create([
                'name' => 'Shared Platform',
            ]);

        $this->attachTeamToProject($restrictedTeam, $project);
        $this->attachUserToProject($project, $viewer);

        $teamDashboardUrl = route('teams.dashboard', ['team' => $restrictedTeam]);

        $this->actingAs($viewer)
            ->get(route('projects.show', ['project' => $project]))
            ->assertOk()
            ->assertSee('Restricted Team')
            ->assertDontSee('href="'.$teamDashboardUrl.'"', false);
    }

    /**
     * @return array{0:User,1:User,2:Team}
     */
    private function teamContext(): array
    {
        $this->ensurePermissionsExist();

        $owner = User::factory()->create();
        $teammate = User::factory()->create();

        $team = Team::factory()->create([
            'user_id' => $owner->id,
            'personal_team' => false,
            'name' => 'Platform Team',
        ]);

        DB::table('team_user')->insert([
            'id' => (string) Str::uuid(),
            'team_id' => $team->id,
            'user_id' => $teammate->id,
            'role' => 'collaborator',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $owner->forceFill(['current_team_id' => $team->id])->save();
        $teammate->forceFill(['current_team_id' => $team->id])->save();

        return [$owner, $teammate, $team];
    }

    private function ensurePermissionsExist(): void
    {
        $this->seed(PermissionSeeder::class);
    }

    private function attachTeamToProject(Team $team, Project $project): void
    {
        DB::table('project_team')->insert([
            'id' => (string) Str::uuid(),
            'project_id' => $project->id,
            'team_id' => $team->id,
            'role' => 'Contributor',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function attachUserToProject(Project $project, User $user): void
    {
        DB::table('project_user')->insert([
            'id' => (string) Str::uuid(),
            'project_id' => $project->id,
            'user_id' => $user->id,
            'role' => 'Owner',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
