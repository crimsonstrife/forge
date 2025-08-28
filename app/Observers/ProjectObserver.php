<?php

namespace App\Observers;

use App\Models\IssuePriority;
use App\Models\IssueStatus;
use App\Models\IssueType;
use App\Models\Project;
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
        $project->issueTypes()->sync(
            IssueType::query()->orderBy('name')->pluck('id')
                ->mapWithKeys(fn($id, $i)=>[(string)$id => ['order'=>$i, 'is_default'=>$i===0]])->all()
        );

        $statuses = IssueStatus::query()->orderBy('order')->get();
        $project->issueStatuses()->sync(
            $statuses->pluck('id')->mapWithKeys(
                fn($id, $i)=>[(string)$id => [
                    'order'=>$i,
                    'is_initial'=>$i===0 && ! $statuses[$i]->is_done,
                    'is_default_done'=>$statuses[$i]->is_done,
                ]])->all()
        );

        $project->issuePriorities()->sync(
            IssuePriority::query()->orderBy('order')->pluck('id')
                ->mapWithKeys(fn($id, $i)=>[(string)$id => ['order'=>$i, 'is_default'=>$i===2]])->all() // default Medium
        );
    }
}
