<?php

namespace Tests\Feature;

use App\Models\Milestone;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectMilestoneSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_milestone_show_returns_404_when_milestone_does_not_belong_to_project(): void
    {
        $user = User::factory()->create();
        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();
        $milestone = Milestone::factory()->create(['project_id' => $project1->id]);

        $this->actingAs($user)
            ->get(route('projects.milestones.show', [$project2, $milestone]))
            ->assertNotFound();
    }

    public function test_milestone_edit_returns_404_when_milestone_does_not_belong_to_project(): void
    {
        $user = User::factory()->create();
        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();
        $milestone = Milestone::factory()->create(['project_id' => $project1->id]);

        $this->actingAs($user)
            ->get(route('projects.milestones.edit', [$project2, $milestone]))
            ->assertNotFound();
    }

    public function test_milestone_update_returns_404_when_milestone_does_not_belong_to_project(): void
    {
        $user = User::factory()->create();
        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();
        $milestone = Milestone::factory()->create(['project_id' => $project1->id]);

        $this->actingAs($user)
            ->put(route('projects.milestones.update', [$project2, $milestone]), [
                'name' => 'Updated Name',
                'type' => 'milestone',
                'state' => 'planned',
            ])
            ->assertNotFound();
    }

    public function test_milestone_destroy_returns_404_when_milestone_does_not_belong_to_project(): void
    {
        $user = User::factory()->create();
        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();
        $milestone = Milestone::factory()->create(['project_id' => $project1->id]);

        $this->actingAs($user)
            ->delete(route('projects.milestones.destroy', [$project2, $milestone]))
            ->assertNotFound();
    }

    public function test_milestone_show_succeeds_when_milestone_belongs_to_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $milestone = Milestone::factory()->create(['project_id' => $project->id]);

        $this->actingAs($user)
            ->get(route('projects.milestones.show', [$project, $milestone]))
            ->assertOk();
    }
}
