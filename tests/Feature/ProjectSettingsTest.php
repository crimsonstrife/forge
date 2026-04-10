<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProjectSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_normalizes_legacy_scalar_settings_when_reading_and_updating(): void
    {
        $project = Project::factory()->create();

        DB::table('projects')
            ->where('id', $project->id)
            ->update(['settings' => json_encode('estimate_minutes')]);

        $project = $project->fresh();

        $this->assertSame('estimate_minutes', $project->setting('issues.estimate_unit'));

        $project->updateSettings([
            'sprints' => [
                'velocity_target' => 21,
            ],
        ]);
        $project->save();
        $project->refresh();

        $this->assertSame('estimate_minutes', $project->setting('issues.estimate_unit'));
        $this->assertSame(21, $project->setting('sprints.velocity_target'));
    }
}
