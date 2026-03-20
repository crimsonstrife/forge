<?php

namespace Tests\Feature;

use App\Enums\MilestoneState;
use App\Enums\MilestoneType;
use App\Models\Issue;
use App\Models\IssueLink;
use App\Models\IssueLinkType;
use App\Models\IssuePriority;
use App\Models\IssueStatus;
use App\Models\IssueType;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Database\Seeders\IssueLinkTypeSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ProjectRoadmapTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_roadmap_surfaces_release_readiness_and_dependency_risk(): void
    {
        [$user, $project, $release, $epic] = $this->roadmapContext();

        $this->actingAs($user)
            ->get(route('projects.roadmap', ['project' => $project]))
            ->assertOk()
            ->assertSee('Roadmap')
            ->assertSee('Release / milestone')
            ->assertSee('Spring Release')
            ->assertSee('v2.4.0')
            ->assertSee('Dependency pressure')
            ->assertSee('Blocked by')
            ->assertSee('Unscheduled')
            ->assertSee('Burnup / burndown')
            ->assertSee('Release readiness')
            ->assertSee('High risk');
    }

    public function test_project_roadmap_can_group_by_parent_issue(): void
    {
        [$user, $project, $release, $epic] = $this->roadmapContext();

        $this->actingAs($user)
            ->get(route('projects.roadmap', ['project' => $project, 'view' => 'parent']))
            ->assertOk()
            ->assertSee('Epic parent')
            ->assertSee($epic->summary)
            ->assertSee('Standalone work')
            ->assertSee('Scope readiness');
    }

    /**
     * @return array{0:User,1:Project,2:Milestone,3:Issue}
     */
    private function roadmapContext(): array
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(PermissionSeeder::class);
        $this->seed(IssueLinkTypeSeeder::class);

        $user = User::factory()->create();
        $team = Team::factory()->create([
            'user_id' => $user->id,
            'personal_team' => true,
        ]);

        $user->forceFill(['current_team_id' => $team->id])->save();

        app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);
        $user->givePermissionTo('projects.view');

        $project = Project::factory()->create([
            'key' => 'RLSE',
            'lead_id' => $user->id,
            'name' => 'Release Command',
        ]);

        DB::table('project_user')->insert([
            'id' => (string) Str::uuid(),
            'project_id' => $project->id,
            'user_id' => $user->id,
            'role' => 'Owner',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $todo = IssueStatus::query()->create([
            'name' => 'To Do',
            'key' => 'TODO',
            'color' => '#2563eb',
            'order' => 1,
            'is_done' => false,
        ]);

        $done = IssueStatus::query()->create([
            'name' => 'Done',
            'key' => 'DONE',
            'color' => '#16a34a',
            'order' => 2,
            'is_done' => true,
        ]);

        foreach ([$todo, $done] as $index => $status) {
            DB::table('project_issue_statuses')->insert([
                'id' => (string) Str::uuid(),
                'project_id' => $project->id,
                'issue_status_id' => $status->id,
                'order' => $index + 1,
                'is_initial' => $status->id === $todo->id,
                'is_default_done' => $status->id === $done->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $epicType = IssueType::query()->firstOrCreate(
            ['key' => 'EPIC'],
            [
                'name' => 'Epic',
                'icon' => 'all_inclusive',
                'is_default' => false,
                'is_hierarchical' => true,
                'tier' => 'epic',
            ]
        );

        $taskType = IssueType::query()->firstOrCreate(
            ['key' => 'TASK'],
            [
                'name' => 'Task',
                'icon' => 'check_box',
                'is_default' => true,
                'is_hierarchical' => false,
                'tier' => 'task',
            ]
        );

        $priority = IssuePriority::query()->firstOrCreate(
            ['key' => 'MEDIUM'],
            [
                'name' => 'Medium',
                'order' => 1,
                'weight' => 1,
                'color' => '#64748b',
                'icon' => 'flag',
            ]
        );

        $release = $project->milestones()->create([
            'type' => MilestoneType::Release,
            'state' => MilestoneState::Planned,
            'name' => 'Spring Release',
            'version' => 'v2.4.0',
            'starts_at' => now()->subDays(10),
            'due_at' => now()->addDays(7),
        ]);

        $epic = Issue::query()->create([
            'project_id' => $project->id,
            'issue_type_id' => $epicType->id,
            'issue_status_id' => $todo->id,
            'issue_priority_id' => $priority->id,
            'reporter_id' => $user->id,
            'summary' => 'Prepare launch train',
            'milestone_id' => $release->id,
            'story_points' => 8,
        ]);

        $blockedIssue = Issue::query()->create([
            'project_id' => $project->id,
            'issue_type_id' => $taskType->id,
            'issue_status_id' => $todo->id,
            'issue_priority_id' => $priority->id,
            'reporter_id' => $user->id,
            'parent_id' => $epic->id,
            'summary' => 'Harden deploy checklist',
            'milestone_id' => $release->id,
            'starts_at' => now()->subDays(5),
            'due_at' => now()->subDay(),
            'story_points' => 5,
        ]);

        Issue::query()->create([
            'project_id' => $project->id,
            'issue_type_id' => $taskType->id,
            'issue_status_id' => $done->id,
            'issue_priority_id' => $priority->id,
            'reporter_id' => $user->id,
            'parent_id' => $epic->id,
            'summary' => 'Ship release notes',
            'milestone_id' => $release->id,
            'starts_at' => now()->subDays(8),
            'due_at' => now()->subDays(2),
            'story_points' => 3,
        ]);

        $blocker = Issue::query()->create([
            'project_id' => $project->id,
            'issue_type_id' => $taskType->id,
            'issue_status_id' => $todo->id,
            'issue_priority_id' => $priority->id,
            'reporter_id' => $user->id,
            'summary' => 'Finalize API contract',
            'starts_at' => now()->subDays(4),
            'due_at' => now()->addDays(2),
            'story_points' => 2,
        ]);

        $blocksType = IssueLinkType::query()->where('key', 'blocks')->firstOrFail();

        IssueLink::query()->create([
            'issue_link_type_id' => $blocksType->id,
            'from_issue_id' => $blocker->id,
            'to_issue_id' => $blockedIssue->id,
            'created_by_id' => $user->id,
        ]);

        return [$user, $project, $release, $epic];
    }
}
