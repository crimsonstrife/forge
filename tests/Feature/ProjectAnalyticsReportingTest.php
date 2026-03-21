<?php

namespace Tests\Feature;

use App\Jobs\BuildProjectDailyReportsJob;
use App\Jobs\BuildSprintDailyReportsJob;
use App\Jobs\ComputeIssueMetricsJob;
use App\Models\Issue;
use App\Models\IssueMetric;
use App\Models\IssuePriority;
use App\Models\IssueStatus;
use App\Models\IssueType;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Sprint;
use App\Models\Team;
use App\Models\User;
use Database\Seeders\IssueEnumsSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\ReportPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ProjectAnalyticsReportingTest extends TestCase
{
    use RefreshDatabase;

    public function test_issue_metrics_track_creation_start_and_done_transitions_for_reporting(): void
    {
        $this->seed(IssueEnumsSeeder::class);

        $reporter = User::factory()->create();
        $project = Project::factory()->for($reporter, 'lead')->create([
            'name' => 'Analytics Platform',
            'key' => 'ANLY',
        ]);
        [$todo, $inProgress, $done] = $this->attachWorkflow($project);

        $issue = $this->createIssue(
            project: $project,
            reporter: $reporter,
            status: $todo,
            createdAt: Carbon::parse('2026-03-01 09:00:00'),
            summary: 'Instrument reporting'
        );

        $this->transitionIssue($issue, $inProgress, Carbon::parse('2026-03-02 10:00:00'));
        $this->transitionIssue($issue, $done, Carbon::parse('2026-03-04 18:00:00'));

        $metric = IssueMetric::query()->findOrFail($issue->id);

        $this->assertSame($issue->id, $metric->issue_id);
        $this->assertSame('2026-03-02 10:00:00', $metric->first_started_at?->format('Y-m-d H:i:s'));
        $this->assertSame('2026-03-04 18:00:00', $metric->first_done_at?->format('Y-m-d H:i:s'));
        $this->assertSame(4_860, $metric->lead_time_min);
        $this->assertSame(3_360, $metric->cycle_time_min);

        $this->assertDatabaseHas('issue_status_events', [
            'issue_id' => $issue->id,
            'to_status_id' => $todo->id,
            'changed_at' => '2026-03-01 09:00:00',
        ]);
    }

    public function test_project_daily_reports_use_start_and_done_history_for_summary_and_cfd(): void
    {
        $this->seed(IssueEnumsSeeder::class);

        $reporter = User::factory()->create();
        $project = Project::factory()->for($reporter, 'lead')->create([
            'name' => 'Ops Dashboard',
            'key' => 'OPS',
        ]);
        [$todo, $inProgress, $done] = $this->attachWorkflow($project);

        $issueA = $this->createIssue(
            project: $project,
            reporter: $reporter,
            status: $todo,
            createdAt: Carbon::parse('2026-03-01 09:00:00'),
            summary: 'Close a shipped change'
        );
        $issueB = $this->createIssue(
            project: $project,
            reporter: $reporter,
            status: $todo,
            createdAt: Carbon::parse('2026-03-01 12:00:00'),
            summary: 'Still open'
        );
        $issueC = $this->createIssue(
            project: $project,
            reporter: $reporter,
            status: $todo,
            createdAt: Carbon::parse('2026-03-01 13:00:00'),
            summary: 'Active work'
        );

        $this->transitionIssue($issueA, $inProgress, Carbon::parse('2026-03-02 10:00:00'));
        $this->transitionIssue($issueC, $inProgress, Carbon::parse('2026-03-03 11:00:00'));
        $this->transitionIssue($issueA, $done, Carbon::parse('2026-03-04 18:00:00'));

        (new BuildProjectDailyReportsJob($project->id, Carbon::parse('2026-03-03 00:00:00')))->handle();

        $this->assertDatabaseHas('report_project_daily_summaries', [
            'project_id' => $project->id,
            'report_date' => '2026-03-03',
            'open_count' => 1,
            'wip_count' => 2,
            'done_count' => 0,
            'throughput_count' => 0,
        ]);

        $this->assertSame(1, $this->cfdCount($project->id, '2026-03-03', $todo->id));
        $this->assertSame(2, $this->cfdCount($project->id, '2026-03-03', $inProgress->id));

        (new BuildProjectDailyReportsJob($project->id, Carbon::parse('2026-03-04 00:00:00')))->handle();

        $this->assertDatabaseHas('report_project_daily_summaries', [
            'project_id' => $project->id,
            'report_date' => '2026-03-04',
            'open_count' => 1,
            'wip_count' => 1,
            'done_count' => 1,
            'throughput_count' => 1,
        ]);

        $this->assertSame(1, $this->cfdCount($project->id, '2026-03-04', $todo->id));
        $this->assertSame(1, $this->cfdCount($project->id, '2026-03-04', $inProgress->id));
        $this->assertSame(1, $this->cfdCount($project->id, '2026-03-04', $done->id));
    }

    public function test_sprint_daily_reports_use_sprint_date_fields_and_remaining_work(): void
    {
        $this->seed(IssueEnumsSeeder::class);

        $reporter = User::factory()->create();
        $project = Project::factory()->for($reporter, 'lead')->create([
            'name' => 'Sprint Analytics',
            'key' => 'SPRT',
        ]);
        [$todo, $inProgress, $done] = $this->attachWorkflow($project);

        $sprint = Sprint::query()->create([
            'project_id' => $project->id,
            'name' => 'Sprint 14',
            'goal' => 'Ship charts',
            'start_date' => '2026-03-01',
            'end_date' => '2026-03-14',
            'capacity' => 13,
        ]);

        $issueA = $this->createIssue(
            project: $project,
            reporter: $reporter,
            status: $todo,
            createdAt: Carbon::parse('2026-03-01 09:00:00'),
            sprint: $sprint,
            storyPoints: 5,
            summary: 'Done in sprint'
        );
        $issueB = $this->createIssue(
            project: $project,
            reporter: $reporter,
            status: $todo,
            createdAt: Carbon::parse('2026-03-02 10:00:00'),
            sprint: $sprint,
            storyPoints: 8,
            summary: 'Still open'
        );
        $issueC = $this->createIssue(
            project: $project,
            reporter: $reporter,
            status: $todo,
            createdAt: Carbon::parse('2026-03-02 12:00:00'),
            sprint: $sprint,
            storyPoints: 3,
            summary: 'Done after sprint'
        );

        $this->transitionIssue($issueA, $inProgress, Carbon::parse('2026-03-03 09:00:00'));
        $this->transitionIssue($issueA, $done, Carbon::parse('2026-03-04 18:00:00'));
        $this->transitionIssue($issueC, $inProgress, Carbon::parse('2026-03-05 10:00:00'));
        $this->transitionIssue($issueC, $done, Carbon::parse('2026-03-16 10:00:00'));

        (new BuildSprintDailyReportsJob($project->id, $sprint->id, Carbon::parse('2026-03-04 00:00:00')))->handle();

        $this->assertDatabaseHas('report_sprint_daily_summaries', [
            'project_id' => $project->id,
            'sprint_id' => $sprint->id,
            'report_date' => '2026-03-04',
            'remaining_points' => 11,
            'remaining_issues' => 2,
        ]);

        (new BuildSprintDailyReportsJob($project->id, $sprint->id, Carbon::parse('2026-02-28 00:00:00')))->handle();

        $this->assertDatabaseMissing('report_sprint_daily_summaries', [
            'project_id' => $project->id,
            'sprint_id' => $sprint->id,
            'report_date' => '2026-02-28',
        ]);
    }

    public function test_project_analytics_page_surfaces_operational_sections_for_report_viewers(): void
    {
        $this->seed(IssueEnumsSeeder::class);
        $this->seed(PermissionSeeder::class);
        $this->seed(ReportPermissionsSeeder::class);

        $user = User::factory()->create();
        $team = Team::factory()->create([
            'user_id' => $user->id,
            'personal_team' => true,
        ]);
        $user->forceFill(['current_team_id' => $team->id])->save();

        Project::factory()->for($user, 'lead')->create([
            'name' => 'Reporting Surface',
            'key' => 'SURF',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);
        $user->givePermissionTo('filament.access');
        $user->givePermissionTo('view.reports');

        $this->actingAs($user)
            ->get('/admin/project-analytics')
            ->assertOk()
            ->assertSee('Overview')
            ->assertSee('Delivery')
            ->assertSee('Burnup')
            ->assertSee('Velocity by Sprint')
            ->assertSee('Operational Risk')
            ->assertSee('Overdue Work')
            ->assertSee('Team Load');
    }

    /**
     * @return array{0: IssueStatus, 1: IssueStatus, 2: IssueStatus}
     */
    private function attachWorkflow(Project $project): array
    {
        /** @var IssueStatus $todo */
        $todo = IssueStatus::query()->where('key', 'TODO')->sole();
        /** @var IssueStatus $inProgress */
        $inProgress = IssueStatus::query()->where('key', 'INPROGRESS')->sole();
        /** @var IssueStatus $done */
        $done = IssueStatus::query()->where('key', 'DONE')->sole();

        foreach ([$todo, $inProgress, $done] as $index => $status) {
            DB::table('project_issue_statuses')->insert([
                'id' => (string) Str::uuid(),
                'project_id' => $project->id,
                'issue_status_id' => $status->id,
                'order' => ($index + 1) * 10,
                'is_initial' => $status->id === $todo->id,
                'is_default_done' => $status->id === $done->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return [$todo, $inProgress, $done];
    }

    private function createIssue(
        Project $project,
        User $reporter,
        IssueStatus $status,
        Carbon $createdAt,
        string $summary,
        ?Sprint $sprint = null,
        ?int $storyPoints = 3,
    ): Issue {
        $typeId = (int) IssueType::query()->where('key', 'TASK')->value('id');
        $priorityId = (int) IssuePriority::query()->where('key', 'MEDIUM')->value('id');

        $this->travelTo($createdAt);

        $issue = Issue::query()->create([
            'project_id' => $project->id,
            'sprint_id' => $sprint?->id,
            'issue_type_id' => $typeId,
            'issue_status_id' => $status->id,
            'issue_priority_id' => $priorityId,
            'reporter_id' => $reporter->id,
            'assignee_id' => $reporter->id,
            'story_points' => $storyPoints,
            'summary' => $summary,
        ]);

        (new ComputeIssueMetricsJob($issue->id))->handle();
        $this->travelBack();

        return $issue->refresh();
    }

    private function transitionIssue(Issue $issue, IssueStatus $status, Carbon $at): void
    {
        $this->travelTo($at);
        $issue->update(['issue_status_id' => $status->id]);
        (new ComputeIssueMetricsJob($issue->id))->handle();
        $this->travelBack();
        $issue->refresh();
    }

    private function cfdCount(string $projectId, string $reportDate, int $statusId): int
    {
        return (int) DB::table('report_cfd_snapshots')
            ->where('project_id', $projectId)
            ->where('report_date', $reportDate)
            ->where('issue_status_id', $statusId)
            ->value('count');
    }
}
