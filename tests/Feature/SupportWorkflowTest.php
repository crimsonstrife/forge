<?php

namespace Tests\Feature;

use App\Livewire\Staff\Support\Triage;
use App\Livewire\Support\NewTicket;
use App\Models\Issue;
use App\Models\IssuePriority;
use App\Models\IssueStatus;
use App\Models\IssueType;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Role;
use App\Models\ServiceProduct;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketType;
use App\Models\User;
use App\Services\Support\TicketWorkflowService;
use Carbon\CarbonImmutable;
use Database\Seeders\IssueEnumsSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class SupportWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
        $this->seed(IssueEnumsSeeder::class);
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow(null);

        parent::tearDown();
    }

    public function test_new_ticket_submission_applies_slas_and_auto_creates_a_linked_issue(): void
    {
        $now = CarbonImmutable::parse('2026-03-20 10:00:00');
        CarbonImmutable::setTestNow($now);

        $organization = Organization::factory()->create();
        $lead = User::factory()->create();
        $project = Project::factory()->create([
            'organization_id' => $organization->getKey(),
            'lead_id' => $lead->getKey(),
        ]);
        $this->configureProjectIssueDefaults($project);

        $bugType = TicketType::query()->where('name', 'Bug')->sole();

        $product = ServiceProduct::query()->create([
            'organization_id' => $organization->getKey(),
            'key' => 'PORTAL',
            'name' => 'Customer Portal',
            'default_project_id' => $project->getKey(),
            'first_response_target_minutes' => 60,
            'resolve_target_minutes' => 1440,
            'auto_create_issue_for_ticket_type_id' => $bugType->getKey(),
            'auto_create_issue_project_id' => $project->getKey(),
        ]);

        Livewire::test(NewTicket::class)
            ->set('productId', $product->getKey())
            ->set('typeId', $bugType->getKey())
            ->set('name', 'Jamie Customer')
            ->set('email', 'jamie@example.com')
            ->set('subject', 'Portal crashes on submit')
            ->set('body', 'Open the support form, choose a product, click submit, and the page freezes.')
            ->call('submit');

        $ticket = Ticket::query()->with('issues')->sole();
        $issue = Issue::query()->sole();
        $workflow = app(TicketWorkflowService::class);

        $this->assertSame((string) $product->getKey(), (string) $ticket->service_product_id);
        $this->assertSame($bugType->getKey(), $ticket->type_id);
        $this->assertSame($project->getKey(), $ticket->project_id);
        $this->assertSame($now->addHour()->toDateTimeString(), $ticket->first_response_due_at?->toDateTimeString());
        $this->assertSame($now->addDay()->toDateTimeString(), $ticket->resolve_due_at?->toDateTimeString());
        $this->assertSame($now->toDateTimeString(), $ticket->last_customer_reply_at?->toDateTimeString());
        $this->assertSame($project->getKey(), $issue->project_id);
        $this->assertTrue($ticket->issues->contains($issue));
        $this->assertDatabaseHas('ticket_issue_links', [
            'ticket_id' => $ticket->getKey(),
            'issue_id' => $issue->getKey(),
        ]);

        $this->assertNull($workflow->maybeAutoCreateIssue($ticket->fresh(['product.defaultProject', 'project'])));
        $this->assertDatabaseCount('issues', 1);
    }

    public function test_ticket_workflow_tracks_first_response_next_response_and_resolution_clocks(): void
    {
        $start = CarbonImmutable::parse('2026-03-20 09:00:00');
        CarbonImmutable::setTestNow($start);

        $organization = Organization::factory()->create();
        $project = Project::factory()->create([
            'organization_id' => $organization->getKey(),
            'lead_id' => User::factory()->create()->getKey(),
        ]);
        $product = ServiceProduct::query()->create([
            'organization_id' => $organization->getKey(),
            'key' => 'OPS',
            'name' => 'Ops Console',
            'default_project_id' => $project->getKey(),
            'first_response_target_minutes' => 30,
            'next_response_target_minutes' => 180,
            'resolve_target_minutes' => 1440,
        ]);

        $ticket = Ticket::query()->create([
            'organization_id' => $organization->getKey(),
            'service_product_id' => $product->getKey(),
            'project_id' => $project->getKey(),
            'submitter_name' => 'Morgan Customer',
            'submitter_email' => 'morgan@example.com',
            'email_hash' => hash('sha256', 'morgan@example.com'),
            'subject' => 'Need help with triage',
            'body' => 'Please investigate the queue delay on the support board.',
            'status_id' => TicketStatus::query()->where('name', 'New')->value('id'),
            'priority_id' => TicketPriority::query()->where('name', 'Medium')->value('id'),
            'type_id' => TicketType::query()->where('name', 'Question')->value('id'),
            'access_token' => (string) str()->ulid(),
            'via' => 'public',
        ]);

        $workflow = app(TicketWorkflowService::class);

        $workflow->initialize($ticket);
        $ticket->refresh();

        $this->assertSame($start->addMinutes(30)->toDateTimeString(), $ticket->first_response_due_at?->toDateTimeString());
        $this->assertSame($start->addDay()->toDateTimeString(), $ticket->resolve_due_at?->toDateTimeString());

        CarbonImmutable::setTestNow($start->addMinutes(20));
        $workflow->recordStaffReply($ticket);
        $ticket->refresh();

        $this->assertSame($start->addMinutes(20)->toDateTimeString(), $ticket->first_responded_at?->toDateTimeString());
        $this->assertNull($ticket->next_response_due_at);

        CarbonImmutable::setTestNow($start->addHours(2));
        $workflow->recordCustomerReply($ticket);
        $ticket->refresh();

        $this->assertSame($start->addHours(2)->toDateTimeString(), $ticket->last_customer_reply_at?->toDateTimeString());
        $this->assertSame($start->addHours(5)->toDateTimeString(), $ticket->next_response_due_at?->toDateTimeString());

        $ticket->update([
            'status_id' => TicketStatus::query()->where('name', 'Resolved')->value('id'),
        ]);
        $ticket->unsetRelation('status');
        $ticket->load('status');

        CarbonImmutable::setTestNow($start->addHours(3));
        $workflow->syncResolutionState($ticket);
        $ticket->refresh();

        $this->assertSame($start->addHours(3)->toDateTimeString(), $ticket->resolved_at?->toDateTimeString());
        $this->assertNull($ticket->next_response_due_at);
        $this->assertTrue($ticket->isResolved());
    }

    public function test_customer_reply_uses_full_product_sla_settings_when_product_relation_is_partially_loaded(): void
    {
        $start = CarbonImmutable::parse('2026-03-20 09:00:00');
        CarbonImmutable::setTestNow($start);

        $organization = Organization::factory()->create();
        $project = Project::factory()->create([
            'organization_id' => $organization->getKey(),
            'lead_id' => User::factory()->create()->getKey(),
        ]);
        $product = ServiceProduct::query()->create([
            'organization_id' => $organization->getKey(),
            'key' => 'PORTAL',
            'name' => 'Portal',
            'default_project_id' => $project->getKey(),
            'next_response_target_minutes' => 180,
        ]);

        $ticket = Ticket::query()->create([
            'organization_id' => $organization->getKey(),
            'service_product_id' => $product->getKey(),
            'project_id' => $project->getKey(),
            'submitter_name' => 'Morgan Customer',
            'submitter_email' => 'morgan@example.com',
            'email_hash' => hash('sha256', 'morgan@example.com'),
            'subject' => 'Need help with triage',
            'body' => 'Please investigate the queue delay on the support board.',
            'status_id' => TicketStatus::query()->where('name', 'New')->value('id'),
            'priority_id' => TicketPriority::query()->where('name', 'Medium')->value('id'),
            'type_id' => TicketType::query()->where('name', 'Question')->value('id'),
            'first_responded_at' => $start->addMinutes(20),
            'access_token' => (string) str()->ulid(),
            'via' => 'public',
        ]);

        $ticket = Ticket::query()
            ->with('product:id,name')
            ->findOrFail($ticket->getKey());

        $this->assertArrayNotHasKey('next_response_target_minutes', $ticket->product->getAttributes());

        CarbonImmutable::setTestNow($start->addHours(2));
        app(TicketWorkflowService::class)->recordCustomerReply($ticket);
        $ticket->refresh();

        $this->assertSame($start->addHours(2)->toDateTimeString(), $ticket->last_customer_reply_at?->toDateTimeString());
        $this->assertSame($start->addHours(5)->toDateTimeString(), $ticket->next_response_due_at?->toDateTimeString());
    }

    public function test_staff_triage_shows_breached_sla_badges(): void
    {
        $start = CarbonImmutable::parse('2026-03-20 08:00:00');
        CarbonImmutable::setTestNow($start);

        $organization = Organization::factory()->create();
        $project = Project::factory()->create([
            'organization_id' => $organization->getKey(),
            'lead_id' => User::factory()->create()->getKey(),
        ]);
        $product = ServiceProduct::query()->create([
            'organization_id' => $organization->getKey(),
            'key' => 'HELP',
            'name' => 'Help Center',
            'default_project_id' => $project->getKey(),
            'first_response_target_minutes' => 60,
        ]);

        $ticket = Ticket::query()->create([
            'organization_id' => $organization->getKey(),
            'service_product_id' => $product->getKey(),
            'project_id' => $project->getKey(),
            'submitter_name' => 'Casey Customer',
            'submitter_email' => 'casey@example.com',
            'email_hash' => hash('sha256', 'casey@example.com'),
            'subject' => 'Overdue response',
            'body' => 'This ticket should show a breached first response timer.',
            'status_id' => TicketStatus::query()->where('name', 'New')->value('id'),
            'priority_id' => TicketPriority::query()->where('name', 'High')->value('id'),
            'type_id' => TicketType::query()->where('name', 'Question')->value('id'),
            'access_token' => (string) str()->ulid(),
            'via' => 'public',
        ]);

        app(TicketWorkflowService::class)->initialize($ticket);

        $staff = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        app(PermissionRegistrar::class)->setPermissionsTeamId(null);

        $role = Role::query()->create([
            'name' => 'Support Super Admin',
            'guard_name' => 'web',
            'team_id' => null,
        ]);
        $role->givePermissionTo('is-super-admin');
        $staff->assignRole($role);

        CarbonImmutable::setTestNow($start->addHours(2));

        Livewire::actingAs($staff)
            ->test(Triage::class)
            ->assertSee($ticket->key)
            ->assertSee('First response')
            ->assertSee('Overdue');
    }

    private function configureProjectIssueDefaults(Project $project): void
    {
        $taskType = IssueType::query()->where('name', 'Task')->sole();
        $bugType = IssueType::query()->where('name', 'Bug')->sole();
        $todoStatus = IssueStatus::query()->where('name', 'To Do')->sole();
        $doneStatus = IssueStatus::query()->where('name', 'Done')->sole();
        $mediumPriority = IssuePriority::query()->where('name', 'Medium')->sole();
        $highPriority = IssuePriority::query()->where('name', 'High')->sole();

        DB::table('project_issue_types')->insert([
            [
                'id' => (string) Str::uuid(),
                'project_id' => $project->getKey(),
                'issue_type_id' => $taskType->getKey(),
                'order' => 10,
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'project_id' => $project->getKey(),
                'issue_type_id' => $bugType->getKey(),
                'order' => 20,
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('project_issue_statuses')->insert([
            [
                'id' => (string) Str::uuid(),
                'project_id' => $project->getKey(),
                'issue_status_id' => $todoStatus->getKey(),
                'order' => 10,
                'is_initial' => true,
                'is_default_done' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'project_id' => $project->getKey(),
                'issue_status_id' => $doneStatus->getKey(),
                'order' => 90,
                'is_initial' => false,
                'is_default_done' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('project_issue_priorities')->insert([
            [
                'id' => (string) Str::uuid(),
                'project_id' => $project->getKey(),
                'issue_priority_id' => $mediumPriority->getKey(),
                'order' => 10,
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'project_id' => $project->getKey(),
                'issue_priority_id' => $highPriority->getKey(),
                'order' => 20,
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
