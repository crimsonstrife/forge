<?php

namespace Tests\Feature;

use App\Livewire\Dashboard\Overview;
use App\Models\DashboardPreference;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Database\Seeders\IssueEnumsSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class DashboardWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
        $this->seed(IssueEnumsSeeder::class);
        app(PermissionRegistrar::class)->setPermissionsTeamId(null);
    }

    public function test_support_staff_default_to_the_support_queue_workspace(): void
    {
        $user = User::factory()->withPersonalTeam()->create([
            'email_verified_at' => now(),
        ]);
        $team = $user->ownedTeams()->firstOrFail();

        $user->forceFill(['current_team_id' => $team->id])->save();
        app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);
        $user->givePermissionTo('tickets.view');

        $this->actingAs($user);

        Livewire::test(Overview::class)
            ->assertSet('activeWorkspace', 'support_queue')
            ->assertSet('landingWorkspace', 'support_queue');
    }

    public function test_team_owners_default_to_team_delivery_when_they_have_visible_projects(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $team = Team::factory()->create([
            'user_id' => $user->id,
            'personal_team' => false,
            'name' => 'Platform Ops',
        ]);

        $user->forceFill(['current_team_id' => $team->id])->save();

        Project::factory()->for($user, 'lead')->create([
            'name' => 'Release Command Center',
        ]);

        $this->actingAs($user);

        Livewire::test(Overview::class)
            ->assertSet('activeWorkspace', 'team_delivery')
            ->assertSet('landingWorkspace', 'team_delivery');
    }

    public function test_workspace_selection_and_widget_layout_preferences_are_persisted(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);

        Livewire::test(Overview::class)
            ->assertSet('activeWorkspace', 'solo_today')
            ->call('toggleWidget', 'due_soon')
            ->call('moveWidgetUp', 'due_soon')
            ->call('makeWorkspaceDefault', 'solo_today');

        $preference = DashboardPreference::query()->where('user_id', $user->id)->sole();

        $this->assertSame('solo_today', $preference->active_workspace);
        $this->assertSame('solo_today', $preference->landing_workspace);
        $this->assertContains('due_soon', $preference->hidden_widgets['solo_today']);
        $this->assertSame('due_soon', $preference->widget_order['solo_today'][1]);
    }
}
