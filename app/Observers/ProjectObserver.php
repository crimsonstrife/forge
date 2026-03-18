<?php

namespace App\Observers;

use App\Models\Project;
use App\Services\Projects\ProjectSchemeSeeder;
use App\Support\Keys\ProjectKeyGenerator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProjectObserver
{
    public function creating(Project $project): void
    {
        // Auto-generate if blank
        if (! filled($project->key) && filled($project->name)) {
            $project->key = app(ProjectKeyGenerator::class)->uniqueForName($project->name, 3);
        }

        // Normalize
        $project->key = Str::upper(preg_replace('/[^A-Za-z0-9]/', '', (string) $project->key));
    }

    public function updating(Project $project): void
    {
        if ($project->isDirty('key')) {
            throw ValidationException::withMessages([
                'key' => 'The project key is immutable.',
            ]);
        }
    }

    public function created(Project $project): void
    {
        app(ProjectSchemeSeeder::class)->seed($project);
    }
}
