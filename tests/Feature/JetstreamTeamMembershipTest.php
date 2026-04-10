<?php

namespace Tests\Feature;

use App\Actions\Jetstream\AddTeamMember;
use App\Livewire\Teams\TeamMemberManager;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Jetstream\Jetstream;
use Livewire\Livewire;
use Tests\TestCase;

class JetstreamTeamMembershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_adding_a_team_member_generates_a_team_user_uuid(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $team = Team::factory()->create([
            'user_id' => $owner->getKey(),
            'personal_team' => false,
        ]);

        app(AddTeamMember::class)->add($owner, $team, $member->email, 'collaborator');

        $membershipId = DB::table('team_user')
            ->where('team_id', $team->getKey())
            ->where('user_id', $member->getKey())
            ->value('id');

        $this->assertTrue(Str::isUuid($membershipId));
    }

    public function test_team_member_manager_renders_members_with_missing_roles(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $team = Team::factory()->create([
            'user_id' => $owner->getKey(),
            'personal_team' => false,
        ]);

        $team->users()->attach($member, ['role' => null]);

        Livewire::actingAs($owner)
            ->test(TeamMemberManager::class, ['team' => $team])
            ->assertSee('Assign role');
    }

    public function test_manage_role_defaults_to_the_first_configured_role_when_membership_role_is_missing(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $team = Team::factory()->create([
            'user_id' => $owner->getKey(),
            'personal_team' => false,
        ]);

        $team->users()->attach($member, ['role' => null]);

        Livewire::actingAs($owner)
            ->test(TeamMemberManager::class, ['team' => $team])
            ->call('manageRole', $member->getKey())
            ->assertSet('currentlyManagingRole', true)
            ->assertSet('currentRole', collect(Jetstream::$roles)->keys()->first());
    }
}
