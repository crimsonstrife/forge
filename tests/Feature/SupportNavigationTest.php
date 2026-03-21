<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class SupportNavigationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
    }

    public function test_authenticated_users_see_support_portal_links_in_navigation(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Support Portal')
            ->assertSee('My Tickets')
            ->assertSee('Submit Ticket')
            ->assertDontSee('Support Triage');
    }

    public function test_staff_users_see_support_triage_alongside_portal_links(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        app(PermissionRegistrar::class)->setPermissionsTeamId(null);

        $role = Role::query()->create([
            'name' => 'Support Navigation Admin',
            'guard_name' => 'web',
            'team_id' => null,
        ]);
        $role->givePermissionTo('is-super-admin');
        $user->assignRole($role);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Support Portal')
            ->assertSee('My Tickets')
            ->assertSee('Submit Ticket')
            ->assertSee('Support Triage');
    }
}
