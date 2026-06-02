<?php

namespace Tests\Feature;

use App\Models\FeedbackBoard;
use App\Models\FeedbackPost;
use App\Models\Project;
use App\Models\ServiceProduct;
use App\Models\Team;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ProjectFeedbackPostsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(PermissionSeeder::class);
    }

    public function test_project_users_with_view_permission_can_read_connected_feedback_posts(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create([
            'user_id' => $user->getKey(),
            'personal_team' => true,
        ]);

        $user->forceFill(['current_team_id' => $team->getKey()])->save();
        app(PermissionRegistrar::class)->setPermissionsTeamId($team->getKey());
        $user->givePermissionTo('projects.view');

        $project = Project::factory()->create([
            'lead_id' => $user->getKey(),
            'name' => 'Feedback Project',
        ]);
        $product = ServiceProduct::factory()->create(['name' => 'Demo Product']);
        $product->projects()->attach($project->getKey());

        $board = FeedbackBoard::factory()->create([
            'service_product_id' => $product->getKey(),
            'name' => 'Player Ideas',
        ]);

        FeedbackPost::factory()->create([
            'board_id' => $board->getKey(),
            'status_id' => $board->defaultStatus()?->getKey(),
            'title' => 'Add a better map view',
            'body_html' => '<p>Map detail matters.</p>',
        ]);

        $this->actingAs($user)
            ->get(route('projects.feedback', ['project' => $project]))
            ->assertOk()
            ->assertSee('Player Ideas')
            ->assertSee('Add a better map view');
    }

    public function test_project_feedback_route_is_registered_in_the_route_collection(): void
    {
        $route = Route::getRoutes()->getByName('projects.feedback');

        $this->assertNotNull($route);
        $this->assertSame('projects/{project}/feedback', $route->uri());
    }

    public function test_project_feedback_posts_respect_project_view_permission(): void
    {
        $project = Project::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('projects.feedback', ['project' => $project]))
            ->assertForbidden();
    }
}
