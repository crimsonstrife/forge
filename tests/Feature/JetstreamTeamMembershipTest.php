<?php

namespace Tests\Feature;

use App\Actions\Jetstream\AddTeamMember;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
}
