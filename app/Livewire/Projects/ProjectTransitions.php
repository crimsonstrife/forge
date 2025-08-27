<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

final class ProjectTransitions extends Component
{
    use AuthorizesRequests;

    public Project $project;

    /** @var array<string,bool> like ["fromId:toId" => true] */
    public array $matrix = [];

    public function mount(Project $project): void
    {
        $this->authorize('update', $project);
        $this->project = $project->load('issueStatuses');

        $existing = $project->statusTransitions()
            ->get(['from_status_id','to_status_id'])
            ->map(fn ($t) => $t->from_status_id.':'.$t->to_status_id)
            ->all();

        foreach ($project->issueStatuses as $sFrom) {
            foreach ($project->issueStatuses as $sTo) {
                if ($sFrom->id === $sTo->id) {
                    continue;
                }
                $key = (string)$sFrom->id . ':' . (string)$sTo->id;
                $this->matrix[$key] = in_array($key, $existing, true);
            }
        }
    }

    public function save(): void
    {
        $this->authorize('update', $this->project);

        $keep = [];
        foreach ($this->matrix as $key => $on) {
            if (! $on) {
                continue;
            }
            [$from, $to] = explode(':', $key, 2);
            $keep[] = ['from_status_id' => $from, 'to_status_id' => $to];
        }

        // Sync by delete-then-insert set difference (global transitions only)
        $this->project->statusTransitions()->delete();
        if (! empty($keep)) {
            $rows = array_map(fn ($r) => [
                'project_id' => $this->project->id,
                'from_status_id' => $r['from_status_id'],
                'to_status_id' => $r['to_status_id'],
                'is_global' => true,
                'issue_type_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ], $keep);

            \DB::table('project_status_transitions')->insert($rows);
        }

        session()->flash('flash.banner', 'Transitions saved.');
        session()->flash('flash.bannerStyle', 'success');
    }

    public function render(): mixed
    {
        return view('livewire.projects.project-transitions', [
            'statuses' => $this->project->issueStatuses()->orderBy('project_issue_statuses.order')->get(),
        ]);
    }
}
