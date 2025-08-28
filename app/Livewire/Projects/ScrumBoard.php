<?php

namespace App\Livewire\Projects;

use App\Models\Issue;
use App\Models\Project;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;

final class ScrumBoard extends Component
{
    public Project $project;

    /** @var array<int, array> */
    public array $sprintColumns = [];   // Active sprint statuses

    /** @var array<int, array> */
    public array $sprintLists = [];     // status_id => issues[]

    /** @var array<int, array> */
    public array $backlog = [];         // issues[]

    public ?int $currentSprintId = null;

    public function mount(Project $project): void
    {
        $this->authorize('view', $project);
        $this->project = $project;

        // TODO: Resolve actual current sprint if you have a Sprint model.
        $this->currentSprintId = null;

        $this->loadData();
    }

    private function loadData(): void
    {
        // Backlog = issues not in active sprint (or simply earliest statuses)
        $this->backlog = Issue::query()
            ->where('project_id', $this->project->id)
            ->when(
                $this->currentSprintId,
                fn ($q, $sid) => $q->whereNull('sprint_id'),
                fn ($q) => $q->whereNull('sprint_id')
            ) // same for now
            ->latest()
            ->limit(100)
            ->get(['id','key','summary','issue_status_id'])
            ->map(fn ($i) => ['id' => (string)$i->id,'key' => $i->key,'summary' => $i->summary])
            ->all();

        // Sprint columns = project statuses in order (same as Kanban)
        $statuses = $this->project->issueStatuses()->orderBy('project_issue_statuses.order')
            ->get(['issue_statuses.id','issue_statuses.name']);

        $this->sprintColumns = $statuses->map(fn ($s) => ['id' => (int)$s->id,'name' => $s->name])->values()->all();

        $lists = [];
        foreach ($this->sprintColumns as $col) {
            $lists[$col['id']] = [];
        }

        // Sprint issues
        $sprintIssues = Issue::query()
            ->where('project_id', $this->project->id)
            ->when($this->currentSprintId, fn ($q, $sid) => $q->where('sprint_id', $sid), fn ($q) => $q->whereNotNull('sprint_id'))
            ->latest()
            ->get(['id','key','summary','issue_status_id']);

        foreach ($sprintIssues as $i) {
            if (isset($lists[$i->status_id])) {
                $lists[$i->status_id][] = ['id' => (string)$i->id,'key' => $i->key,'summary' => $i->summary];
            }
        }
        $this->sprintLists = $lists;
    }

    #[On('scrum:move-to-sprint')]
    public function moveToSprint(string $issueId): void
    {
        $this->authorize('update', $this->project);
        $issue = Issue::where('project_id', $this->project->id)->findOrFail($issueId);
        if (! auth()->user()->can('issues.update')) {
            $this->dispatch('notify', type:'error', message:'No permission.');
            return;
        }
        // If you have a sprint id, set it; else just mark “in sprint” by setting a non-null marker if you choose.
        $issue->sprint_id = $this->currentSprintId; // may be null; adapt if you introduce sprints.
        $issue->save();
        $this->loadData();
    }

    #[On('scrum:move-to-backlog')]
    public function moveToBacklog(string $issueId): void
    {
        $this->authorize('update', $this->project);
        $issue = Issue::where('project_id', $this->project->id)->findOrFail($issueId);
        if (! auth()->user()->can('issues.update')) {
            $this->dispatch('notify', type:'error', message:'No permission.');
            return;
        }
        $issue->sprint_id = null;
        $issue->save();
        $this->loadData();
    }

    #[On('scrum:status-changed')]
    public function statusChanged(string $issueId, int $toStatusId): void
    {
        $this->authorize('update', $this->project);
        $issue = Issue::where('project_id', $this->project->id)->findOrFail($issueId);
        if (! auth()->user()->can('issues.transition')) {
            $this->dispatch('notify', type:'error', message:'No permission.');
            return;
        }
        $issue->status_id = $toStatusId;
        $issue->save();
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.projects.scrum-board');
    }
}
