<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemProjectControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_scopes_system_project_results_by_for_forge_user_id(): void
    {
        $this->seedProjectVisibilityPermissions();

        $user = User::factory()->create();
        $project = Project::factory()->create([
            'lead_id' => $user->id,
        ]);

        $response = $this->withoutMiddleware()->getJson(route('api.v1.system.projects.index', [
            'for_forge_user_id' => $user->id,
        ]));

        $response
            ->assertOk()
            ->assertJsonPath('data.0.id', (string) $project->id)
            ->assertJsonPath('data.0.url', route('projects.show', ['project' => $project]));
    }

    public function test_it_hides_system_project_show_when_the_project_is_not_visible_to_that_user(): void
    {
        $this->seedProjectVisibilityPermissions();

        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $project = Project::factory()->create([
            'lead_id' => $owner->id,
        ]);

        $response = $this->withoutMiddleware()->getJson(route('api.v1.system.projects.show', [
            'project' => $project,
            'for_forge_user_id' => $otherUser->id,
        ]));

        $response->assertNotFound();
    }

    private function seedProjectVisibilityPermissions(): void
    {
        foreach (['is-admin', 'is-super-admin'] as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }
    }
}
