<?php

namespace Tests\Feature;

use App\Models\Issue;
use App\Models\IssuePriority;
use App\Models\IssueStatus;
use App\Models\IssueType;
use App\Models\Milestone;
use App\Models\Permission;
use App\Models\Project;
use App\Models\SavedIssueView;
use App\Models\Sprint;
use App\Models\Team;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class IssueExplorerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
    }

    public function test_it_filters_issues_with_query_language_and_structured_filters(): void
    {
        [$owner, $teammate, $team, $project, $otherProject] = $this->context();
        [$todo, $inProgress, $bug, $task, $high, $low] = $this->issueLookups();

        $milestone = Milestone::factory()->create([
            'project_id' => $project->id,
            'name' => 'Release 2.1',
        ]);

        $sprint = Sprint::query()->create([
            'project_id' => $project->id,
            'name' => 'Sprint 24',
            'state' => 'active',
            'sort_order' => 1,
        ]);

        $matching = $this->makeIssue(
            project: $project,
            reporter: $teammate,
            assignee: $owner,
            status: $inProgress,
            type: $bug,
            priority: $high,
            summary: 'Search indexing breaks for release search',
            milestone: $milestone,
            sprint: $sprint,
            dueAt: '2026-03-20 12:00:00',
            isNext: true,
        );
        $this->attachTag($matching, 'ops');

        $ticket = $this->makeTicket($project, 'SD-104', 'Search indexing support request');
        $ticket->issues()->attach($matching->id);

        $nonMatching = $this->makeIssue(
            project: $project,
            reporter: $owner,
            assignee: $teammate,
            status: $todo,
            type: $task,
            priority: $low,
            summary: 'Write release copy',
            milestone: null,
            sprint: null,
            dueAt: '2026-04-05 12:00:00',
            isNext: false,
        );
        $this->attachTag($nonMatching, 'marketing');

        $otherProjectIssue = $this->makeIssue(
            project: $otherProject,
            reporter: $owner,
            assignee: $owner,
            status: $inProgress,
            type: $bug,
            priority: $high,
            summary: 'Ops incident cleanup',
            milestone: null,
            sprint: null,
            dueAt: '2026-03-19 12:00:00',
            isNext: true,
        );
        $this->attachTag($otherProjectIssue, 'ops');

        DB::table('issues')->where('id', $matching->id)->update(['updated_at' => '2026-03-18 09:15:00']);
        DB::table('issues')->where('id', $nonMatching->id)->update(['updated_at' => '2026-03-10 09:15:00']);
        DB::table('issues')->where('id', $otherProjectIssue->id)->update(['updated_at' => '2026-03-18 09:15:00']);

        $query = http_build_query([
            'query' => 'project:ALPHA status:"In Progress" assignee:me ticket:SD-104 tag:ops is:next due<=2026-03-21',
            'reporter' => $teammate->id,
            'milestone' => $milestone->id,
            'sprint' => $sprint->id,
            'updated_from' => '2026-03-15',
            'updated_to' => '2026-03-19',
        ]);

        $this->actingAs($owner)
            ->get('/issues?'.$query)
            ->assertOk()
            ->assertSee('Issue Explorer')
            ->assertSee($matching->key)
            ->assertSee('Search indexing breaks for release search')
            ->assertDontSee($nonMatching->key)
            ->assertDontSee('Write release copy')
            ->assertDontSee($otherProjectIssue->key)
            ->assertDontSee('Ops incident cleanup');
    }

    public function test_it_saves_and_shares_views_with_the_current_team(): void
    {
        [$owner, $teammate, $team, $project] = $this->context();
        [$todo, $inProgress, $bug, $task, $high] = $this->issueLookups();

        $focusIssue = $this->makeIssue(
            project: $project,
            reporter: $owner,
            assignee: $owner,
            status: $inProgress,
            type: $bug,
            priority: $high,
            summary: 'Release blocker',
            milestone: null,
            sprint: null,
            dueAt: '2026-03-19 12:00:00',
            isNext: true,
        );
        $this->attachTag($focusIssue, 'release');

        $otherIssue = $this->makeIssue(
            project: $project,
            reporter: $owner,
            assignee: $teammate,
            status: $todo,
            type: $task,
            priority: $high,
            summary: 'General cleanup',
            milestone: null,
            sprint: null,
            dueAt: '2026-03-25 12:00:00',
            isNext: false,
        );

        $state = [
            'query' => 'project:ALPHA tag:release is:next',
            'sort' => 'due_asc',
            'next' => true,
            'filters' => [
                'project' => (string) $project->id,
                'assignee' => '',
                'reporter' => '',
                'status' => '',
                'type' => '',
                'priority' => '',
                'milestone' => '',
                'sprint' => '',
                'tag' => '',
                'ticket' => '',
                'due_from' => '',
                'due_to' => '',
                'updated_from' => '',
                'updated_to' => '',
            ],
        ];

        $this->actingAs($owner)
            ->post('/issues/views', [
                'name' => 'Release focus',
                'is_shared' => '1',
                'state' => json_encode($state),
            ])
            ->assertRedirect(url('/issues?view=release-focus'));

        $savedView = SavedIssueView::query()->where('slug', 'release-focus')->firstOrFail();

        $this->assertTrue($savedView->is_shared);
        $this->assertSame($team->id, $savedView->team_id);

        $this->actingAs($teammate)
            ->get('/issues?view='.$savedView->slug)
            ->assertOk()
            ->assertSee('Release focus')
            ->assertSee('Shared by '.$owner->name)
            ->assertSee($focusIssue->key)
            ->assertSee('Release blocker')
            ->assertDontSee($otherIssue->key)
            ->assertDontSee('General cleanup');
    }

    /**
     * @return array{0:User,1:User,2:Team,3:Project,4:Project}
     */
    private function context(): array
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $owner = User::factory()->create();
        $teammate = User::factory()->create();

        $team = Team::factory()->create([
            'user_id' => $owner->id,
            'personal_team' => true,
        ]);

        $owner->forceFill(['current_team_id' => $team->id])->save();
        $teammate->forceFill(['current_team_id' => $team->id])->save();

        DB::table('team_user')->insert([
            'id' => (string) Str::uuid(),
            'team_id' => $team->id,
            'user_id' => $teammate->id,
            'role' => 'member',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach (['issues.view', 'issues.update', 'issues.manage', 'projects.view', 'projects.manage', 'is-super-admin'] as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);
        $owner->givePermissionTo('issues.view');
        $teammate->givePermissionTo('issues.view');

        $project = Project::factory()->create([
            'lead_id' => $owner->id,
            'key' => 'ALPHA',
            'name' => 'Alpha',
        ]);

        $otherProject = Project::factory()->create([
            'lead_id' => $owner->id,
            'key' => 'OMEGA',
            'name' => 'Omega',
        ]);

        $this->attachUserToProject($project, $owner);
        $this->attachUserToProject($project, $teammate);
        $this->attachUserToProject($otherProject, $owner);
        $this->attachUserToProject($otherProject, $teammate);

        return [$owner, $teammate, $team, $project, $otherProject];
    }

    /**
     * @return array{0:IssueStatus,1:IssueStatus,2:IssueType,3:IssueType,4:IssuePriority,5:IssuePriority}
     */
    private function issueLookups(): array
    {
        $todo = IssueStatus::query()->firstOrCreate(
            ['key' => 'TODO'],
            [
                'name' => 'To Do',
                'color' => '#2563eb',
                'order' => 1,
                'is_done' => false,
            ]
        );

        $inProgress = IssueStatus::query()->firstOrCreate(
            ['key' => 'INPROGRESS'],
            [
                'name' => 'In Progress',
                'color' => '#f59e0b',
                'order' => 2,
                'is_done' => false,
            ]
        );

        $bug = IssueType::query()->firstOrCreate(
            ['key' => 'BUG'],
            [
                'name' => 'Bug',
                'icon' => 'bug_report',
                'is_default' => false,
                'is_hierarchical' => false,
                'tier' => 'task',
            ]
        );

        $task = IssueType::query()->firstOrCreate(
            ['key' => 'TASK'],
            [
                'name' => 'Task',
                'icon' => 'check_box',
                'is_default' => true,
                'is_hierarchical' => false,
                'tier' => 'task',
            ]
        );

        $high = IssuePriority::query()->firstOrCreate(
            ['key' => 'HIGH'],
            [
                'name' => 'High',
                'order' => 1,
                'weight' => 90,
                'color' => '#ef4444',
                'icon' => 'priority_high',
            ]
        );

        $low = IssuePriority::query()->firstOrCreate(
            ['key' => 'LOW'],
            [
                'name' => 'Low',
                'order' => 2,
                'weight' => 10,
                'color' => '#6b7280',
                'icon' => 'low_priority',
            ]
        );

        return [$todo, $inProgress, $bug, $task, $high, $low];
    }

    private function attachUserToProject(Project $project, User $user): void
    {
        DB::table('project_user')->insert([
            'id' => (string) Str::uuid(),
            'project_id' => $project->id,
            'user_id' => $user->id,
            'role' => 'member',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function makeIssue(
        Project $project,
        User $reporter,
        ?User $assignee,
        IssueStatus $status,
        IssueType $type,
        IssuePriority $priority,
        string $summary,
        ?Milestone $milestone,
        ?Sprint $sprint,
        string $dueAt,
        bool $isNext,
    ): Issue {
        $issue = Issue::query()->create([
            'project_id' => $project->id,
            'reporter_id' => $reporter->id,
            'assignee_id' => $assignee?->id,
            'issue_status_id' => $status->id,
            'issue_type_id' => $type->id,
            'issue_priority_id' => $priority->id,
            'summary' => $summary,
            'description' => $summary.' details',
            'milestone_id' => $milestone?->id,
            'sprint_id' => $sprint?->id,
            'due_at' => $dueAt,
        ]);

        $issue->forceFill(['is_next' => $isNext])->save();

        return $issue->fresh();
    }

    private function makeTicket(Project $project, string $key, string $subject): Ticket
    {
        return Ticket::query()->create([
            'key' => $key,
            'project_id' => $project->id,
            'organization_id' => $project->organization_id,
            'submitter_name' => 'Support User',
            'submitter_email' => 'support@example.test',
            'email_hash' => hash('sha256', 'support@example.test'),
            'subject' => $subject,
            'body' => $subject.' body',
            'status_id' => TicketStatus::query()->where('name', 'Open')->value('id'),
            'priority_id' => TicketPriority::query()->where('name', 'High')->value('id'),
            'type_id' => TicketType::query()->where('name', 'Bug')->value('id'),
        ]);
    }

    private function attachTag(Issue $issue, string $name): void
    {
        $tagId = (string) Str::uuid();

        DB::table('tags')->insert([
            'id' => $tagId,
            'name' => json_encode(['en' => $name], JSON_THROW_ON_ERROR),
            'slug' => json_encode(['en' => Str::slug($name)], JSON_THROW_ON_ERROR),
            'type' => null,
            'order_column' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('taggables')->insert([
            'tag_id' => $tagId,
            'taggable_id' => $issue->id,
            'taggable_type' => Issue::class,
        ]);
    }
}
