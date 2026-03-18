<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserTourState;
use App\Support\Onboarding\TourRegistry;
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
            ->assertSee('data-start-tour="main-app"', false);
    }
}
