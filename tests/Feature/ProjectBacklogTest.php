<?php

namespace Tests\Feature;

use App\Models\Issue;
use App\Models\IssuePriority;
use App\Models\IssueStatus;
use App\Models\IssueType;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Sprint;
use App\Models\Team;
use App\Models\User;
use App\Services\Projects\BacklogPlanningService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ProjectBacklogTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_moves_selected_backlog_items_into_a_sprint(): void
    {
        [$user, $project, $sprint] = $this->projectContext();

        $first = $this->makeIssue($project, $user, 'Plan APIs');
        $second = $this->makeIssue($project, $user, 'Ship release notes');

        app(BacklogPlanningService::class)->moveIssues(
            $project,
            [(string) $first->id, (string) $second->id],
            (string) $sprint->id
        );

        $this->assertDatabaseHas('issues', [
            'id' => $first->id,
            'sprint_id' => $sprint->id,
            'planning_order' => 1,
        ]);

        $this->assertDatabaseHas('issues', [
            'id' => $second->id,
            'sprint_id' => $sprint->id,
            'planning_order' => 2,
        ]);
    }

    public function test_it_reorders_backlog_rankings(): void
    {
        [$user, $project] = $this->projectContext();

        $first = $this->makeIssue($project, $user, 'Backlog one');
        $second = $this->makeIssue($project, $user, 'Backlog two');
        $third = $this->makeIssue($project, $user, 'Backlog three');

        DB::table('issues')->where('id', $first->id)->update(['planning_order' => 1]);
        DB::table('issues')->where('id', $second->id)->update(['planning_order' => 2]);
        DB::table('issues')->where('id', $third->id)->update(['planning_order' => 3]);

        app(BacklogPlanningService::class)->reorderLane(
            $project,
            null,
            [(string) $third->id, (string) $first->id, (string) $second->id]
        );

        $this->assertDatabaseHas('issues', [
            'id' => $third->id,
            'planning_order' => 1,
        ]);

        $this->assertDatabaseHas('issues', [
            'id' => $first->id,
            'planning_order' => 2,
        ]);

        $this->assertDatabaseHas('issues', [
            'id' => $second->id,
            'planning_order' => 3,
        ]);
    }

    public function test_it_updates_sprint_capacity(): void
    {
        [$user, $project, $sprint] = $this->projectContext();

        $sprint->update(['capacity' => 21]);

        $this->assertDatabaseHas('sprints', [
            'id' => $sprint->id,
            'capacity' => 21,
        ]);
    }

    /**
     * @return array{0:User,1:Project,2:Sprint}
     */
    private function projectContext(): array
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $user = User::factory()->create();
        $team = Team::factory()->create([
            'user_id' => $user->id,
            'personal_team' => true,
        ]);
        $user->forceFill(['current_team_id' => $team->id])->save();
        $project = Project::factory()->create([
            'lead_id' => $user->id,
        ]);

        foreach ([
            'is-admin',
            'is-super-admin',
            'projects.view',
            'projects.manage',
            'issues.update',
            'issues.manage',
            'sprints.manage',
        ] as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);
        $user->givePermissionTo(['projects.view', 'issues.update', 'sprints.manage']);

        $status = IssueStatus::query()->create([
            'name' => 'To Do',
            'key' => 'TODO',
            'color' => '#2563eb',
            'order' => 1,
            'is_done' => false,
        ]);

        DB::table('project_issue_statuses')->insert([
            'id' => (string) str()->uuid(),
            'project_id' => $project->id,
            'issue_status_id' => $status->id,
            'order' => 1,
            'is_initial' => true,
            'is_default_done' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $sprint = Sprint::query()->create([
            'project_id' => $project->id,
            'name' => 'Sprint 24',
            'state' => 'planned',
            'sort_order' => 1,
        ]);

        return [$user, $project, $sprint];
    }

    private function makeIssue(Project $project, User $user, string $summary): Issue
    {
        $type = IssueType::query()->firstOrCreate(
            ['key' => 'TASK'],
            [
                'name' => 'Task',
                'icon' => 'check_box',
                'is_default' => true,
                'is_hierarchical' => false,
                'tier' => 'task',
            ]
        );

        $status = IssueStatus::query()->first();

        $priority = IssuePriority::query()->firstOrCreate(
            ['key' => 'MEDIUM'],
            [
                'name' => 'Medium',
                'order' => 1,
                'weight' => 1,
                'color' => '#6b7280',
                'icon' => 'flag',
            ]
        );

        return Issue::query()->create([
            'project_id' => $project->id,
            'issue_type_id' => $type->id,
            'issue_status_id' => $status->id,
            'issue_priority_id' => $priority->id,
            'reporter_id' => $user->id,
            'summary' => $summary,
            'story_points' => 3,
        ]);
    }
}
