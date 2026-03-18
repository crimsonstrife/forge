<?php

namespace Tests\Feature;

use App\Models\Issue;
use App\Models\Project;
use App\Models\Sprint;
use App\Models\User;
use App\Models\UserTourState;
use App\Support\Onboarding\TourRegistry;
use Database\Seeders\IssueEnumsSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnboardingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
        $this->seed(IssueEnumsSeeder::class);
    }

    public function test_dashboard_creates_a_pending_main_app_tour_state_and_renders_the_prompt(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Welcome to Forge')
            ->assertSee('data-start-tour="main-app"', false);

        $this->assertDatabaseHas('user_tour_states', [
            'user_id' => $user->id,
            'tour' => TourRegistry::MAIN_APP,
            'status' => UserTourState::STATUS_PENDING,
        ]);
    }

    public function test_user_can_start_and_complete_the_main_app_tour(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->postJson(route('onboarding.tours.start', ['tour' => TourRegistry::MAIN_APP]))
            ->assertOk()
            ->assertJsonPath('state.status', UserTourState::STATUS_ACTIVE)
            ->assertJsonPath('state.lastStep', 0);

        $this->actingAs($user)
            ->patchJson(route('onboarding.tours.update', ['tour' => TourRegistry::MAIN_APP]), [
                'action' => 'complete',
            ])
            ->assertOk()
            ->assertJsonPath('state.status', UserTourState::STATUS_COMPLETED);

        $this->assertDatabaseHas('user_tour_states', [
            'user_id' => $user->id,
            'tour' => TourRegistry::MAIN_APP,
            'status' => UserTourState::STATUS_COMPLETED,
        ]);
    }

    public function test_getting_started_page_is_available_to_verified_users(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('getting-started'))
            ->assertOk()
            ->assertSee('Getting Started')
            ->assertSee('data-start-tour="main-app"', false)
            ->assertSee('data-start-tour="project-detail"', false)
            ->assertSee('data-start-tour="issue-detail"', false)
            ->assertSee('private Forge Sandbox');

        $this->assertDatabaseCount('projects', 0);
    }

    public function test_starting_the_project_detail_tour_provisions_a_private_sandbox_project(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('onboarding.tours.start', ['tour' => TourRegistry::PROJECT_DETAIL]))
            ->assertOk()
            ->assertJsonPath('tour.name', TourRegistry::PROJECT_DETAIL)
            ->assertJsonPath('state.status', UserTourState::STATUS_ACTIVE);

        $project = Project::query()->sole();
        $issue = Issue::query()->findOrFail(data_get($project->settings, 'onboarding.primary_issue_id'));

        $response->assertJsonPath(
            'tour.steps.0.url',
            route('projects.show', ['project' => $project])
        );

        $tourStepUrls = collect($response->json('tour.steps'))->pluck('url');

        $this->assertTrue($tourStepUrls->contains(route('projects.backlog', ['project' => $project])));

        $this->assertTrue(data_get($project->settings, 'onboarding.sandbox'));
        $this->assertDatabaseHas('project_user', [
            'project_id' => $project->getKey(),
            'user_id' => $user->getKey(),
            'role' => 'Owner',
        ]);
        $this->assertGreaterThan(0, $project->issueStatuses()->count());
        $this->assertGreaterThan(0, $project->sprints()->count());
        $this->assertSame($project->getKey(), $issue->project_id);
        $this->assertSame($user->getKey(), $project->lead_id);
    }

    public function test_issue_detail_tour_reuses_the_existing_sandbox_project(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->postJson(route('onboarding.tours.start', ['tour' => TourRegistry::PROJECT_DETAIL]))
            ->assertOk();

        $project = Project::query()->sole();
        $issue = Issue::query()->findOrFail(data_get($project->settings, 'onboarding.primary_issue_id'));

        $this->actingAs($user)
            ->postJson(route('onboarding.tours.start', ['tour' => TourRegistry::ISSUE_DETAIL]))
            ->assertOk()
            ->assertJsonPath('tour.name', TourRegistry::ISSUE_DETAIL)
            ->assertJsonPath(
                'tour.steps.0.url',
                route('issues.show', ['project' => $project, 'issue' => $issue])
            );

        $this->assertDatabaseCount('projects', 1);
        $this->assertSame(4, Issue::query()->count());
    }
}
