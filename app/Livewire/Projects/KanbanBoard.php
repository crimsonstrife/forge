<?php

namespace App\Livewire\Projects;

use App\Models\Issue;
use App\Models\IssueStatus;
use App\Models\Project;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\On;
use Livewire\Component;

final class KanbanBoard extends Component
{
    public Project $project;

    /** @var array<int, array{id:int,name:string,color:string,is_done:bool}> */
    public array $columns = [];

    /** @var array<string,int> map status_id => index */
    public array $columnIndex = [];

    /** @var array<int, array<int, array{id:string,summary:string,key?:string,type?:string,assignee?:string}>> */
    public array $lists = [];

    public bool $hasOrderColumn = false;

    public function mount(Project $project): void
    {
        $this->authorize('view', $project);

        $this->project = $project;

        // Build columns from project scheme in display order
        $statuses = $project->issueStatuses()
            ->orderBy('project_issue_statuses.order')
            ->get(['issue_statuses.id','issue_statuses.name','issue_statuses.color','issue_statuses.is_done']);

        $this->columns = $statuses->map(fn ($s) => [
            'id'      => (int) $s->id,
            'name'    => (string) $s->name,
            'color'   => (string) $s->color,
            'is_done' => (bool) $s->is_done,
        ])->values()->all();

        $this->columnIndex = collect($this->columns)->pluck('id')->flip()->all();

        // Optional per-column ordering support
        $this->hasOrderColumn = Schema::hasColumn('issues', 'order_in_status');

        $this->hydrateLists();
    }

    private function hydrateLists(): void
    {
        $q = Issue::query()
            ->where('project_id', $this->project->id)
            ->whereIn('issue_status_id', array_column($this->columns, 'id'))
            ->with(['type:id,name', 'assignee:id,name'])
            ->select(['id','key','summary','issue_status_id','issue_type_id','assignee_id','updated_at']);

        if ($this->hasOrderColumn) {
            $q->orderBy('order_in_status');
        } else {
            $q->latest('updated_at');
        }

        $issues = $q->get();

        $grouped = [];
        foreach ($this->columns as $col) {
            $grouped[$col['id']] = [];
        }

        foreach ($issues as $i) {
            $grouped[(int) $i->issue_status_id][] = [
                'id'       => (string) $i->id,
                'key'      => (string) ($i->key ?? ''),
                'summary'  => (string) $i->summary,
                'type'     => (string) ($i->type?->name ?? ''),
                'assignee' => (string) ($i->assignee?->name ?? ''),
            ];
        }

        $this->lists = $grouped;
    }

    #[On('kanban:issue-dropped')]
    public function onIssueDropped(string $issueId, int $toStatusId, int $newIndex, ?int $fromStatusId = null): void
    {
        $this->authorize('update', $this->project);

        $issue = Issue::query()
            ->where('project_id', $this->project->id)
            ->findOrFail($issueId);

        // Permission check: transition allowed?
        if (! auth()->user()->can('issues.transition')) {
            $this->dispatch('notify', type: 'error', message: 'You do not have permission to transition issues.');
            return;
        }
        if ($fromStatusId && ! $this->project->canTransition((string)$fromStatusId, (string)$toStatusId, (string)$issue->issue_type_id)) {
            $this->dispatch('notify', type: 'error', message: 'Transition not allowed by project workflow.');
            return;
        }

        $issue->issue_status_id = $toStatusId;

        if ($this->hasOrderColumn) {
            // Reindex target column (simple approach)
            $target = $this->lists[$toStatusId] ?? [];
            $ids = array_column($target, 'id');
            // Insert this issue id at newIndex position
            array_splice($ids, max(0, $newIndex), 0, [$issue->id]);
            // Persist new order
            foreach ($ids as $pos => $id) {
                Issue::whereKey($id)->update([
                    'issue_status_id' => $toStatusId,
                    'order_in_status' => $pos,
                ]);
            }
        }

        $issue->save();
        $this->hydrateLists();
        $this->dispatch('notify', type: 'success', message: 'Issue updated.');
    }

    public function render()
    {
        return view('livewire.projects.kanban-board');
    }
}
