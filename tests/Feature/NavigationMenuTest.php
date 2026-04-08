<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationMenuTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
    }

    public function test_dashboard_renders_when_user_has_teams_but_no_current_team_selected(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'current_team_id' => null,
        ]);

        $team = Team::factory()->create([
            'user_id' => $user->getKey(),
            'personal_team' => false,
            'name' => 'Platform Team',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('No team selected')
            ->assertSee($team->name);
    }
}
