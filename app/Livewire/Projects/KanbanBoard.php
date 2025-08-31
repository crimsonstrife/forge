<?php

namespace App\Livewire\Projects;

use App\Models\Issue;
use App\Models\IssueStatus;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Throwable;

final class KanbanBoard extends Component
{
    public Project $project;

    /** @var array<int, array{id:int,name:string,color:string,is_done:bool}> */
    public array $columns = [];

    /** @var array<int, array<int, array{id:int,summary:string,description?:string}>> */
    public array $lists = [];

    public string $viewMode = 'kanban';

    public function mount(Project $project): void
    {
        $this->authorize('view', $project);

        $this->project = $project;

        $statuses = $project->issueStatuses()
            ->select([
                'issue_statuses.id',
                'issue_statuses.name',
                'issue_statuses.color',
                'issue_statuses.is_done',
            ])
            ->when(
                Schema::hasColumn('project_issue_statuses', 'order'),
                fn ($q) => $q->orderBy('project_issue_statuses.order'),
                fn ($q) => $q->orderBy('issue_statuses.order')
            )
            ->get();

        $this->columns = $statuses->map(fn (IssueStatus $s) => [
            'id' => $s->id,
            'name' => $s->name,
            'color' => $s->color ?: '#78909C',
            'is_done' => (bool) $s->is_done,
        ])->values()->all();

        $this->lists = [];

        $issueHasOrder = Schema::hasColumn('issues', 'order');

        $issues = Issue::query()
            ->where('project_id', $project->id)
            ->when(
                $issueHasOrder,
                fn ($q) => $q->select(['id','summary','description','issue_status_id','order','number'])
                    ->orderBy('issues.order')
                    ->orderBy('issues.number'),
                fn ($q) => $q->select(['id','summary','description','issue_status_id','number'])
                    ->orderBy('issues.number')
            )
            ->get();

        foreach ($statuses as $status) {
            $this->lists[$status->id] = $issues
                ->where('issue_status_id', $status->id) // <- ensure correct key
                ->map(fn (Issue $i) => [
                    'id' => $i->id,
                    'summary' => $i->summary,
                    'description' => $i->description,
                ])->values()->all();
        }
    }

    /**
     * Reorder (and optionally re-parent) issues inside a status column.
     *
     * @param int $toStatusId
     * @param array<int> $orderedIssueIds
     * @throws Throwable
     */
    public function reorder(int $toStatusId, array $orderedIssueIds): void
    {
        $this->authorize('update', $this->project);

        DB::transaction(function () use ($toStatusId, $orderedIssueIds): void {
            foreach ($orderedIssueIds as $index => $issueId) {
                Issue::query()
                    ->whereKey($issueId)
                    ->where('project_id', $this->project->id)
                    ->update([
                        'status_id' => $toStatusId,
                        'order' => $index + 1,
                    ]);
            }
        });

        // Refresh local lists for the two statuses affected is minimal,
        // but simplest is to re-mount the board snapshot:
        $this->mount($this->project);
    }

    public function openIssue(int $issueId): void
    {
        $this->redirectRoute('issues.show', ['issue' => $issueId]);
    }

    public function createIssue(int $statusId): void
    {
        $this->redirectRoute('issues.create', ['project' => $this->project->id, 'status' => $statusId]);
    }

    public function render()
    {
        return view('livewire.projects.kanban-board');
    }
}
