<?php

namespace App\Livewire\Projects;

use App\Models\Issue;
use App\Models\IssueStatus;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Throwable;

final class KanbanBoard extends Component
{
    public Project $project;

    /** @var array<int, array{id:int,name:string,color:string,is_done:bool}> */
    public array $columns = [];

    /**
     * @var array<int, array<int, array{
     *   id:int,
     *   summary:string,
     *   description?:string,
     *   tier?:string,
     *   type_name?:string,
     *   type_color?:string,
     *   type_icon?:string,
     *   children_total?:int,
     *   children_done?:int,
     *   progress?:int
     * }>>
     */
    public array $lists = [];

    public string $viewMode = 'kanban';

    public function mount(Project $project): void
    {
        $this->authorize('view', $project);
        $this->project = $project;

        // Columns (statuses)
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
            'id'      => $s->id,
            'name'    => $s->name,
            'color'   => $s->color ?: '#78909C',
            'is_done' => (bool) $s->is_done,
        ])->values()->all();

        $this->lists = [];

        $issueHasOrder = Schema::hasColumn('issues', 'order');

        // Pull all issues for the board, including type (for tier chip) and roll-up counts.
        $issues = Issue::query()
            ->where('project_id', $project->id)
            ->when(
                $issueHasOrder,
                fn ($q) => $q->select([
                    'id',
                    'summary',
                    'description',
                    'issue_status_id',
                    'issue_type_id',
                    'order',
                    'number',
                ])
                    ->orderBy('issues.order')
                    ->orderBy('issues.number'),
                fn ($q) => $q->select([
                    'id',
                    'summary',
                    'description',
                    'issue_status_id',
                    'issue_type_id',
                    'number',
                ])
                    ->orderBy('issues.number')
            )
            ->with([
                'type:id,name,tier',
                'status:id,is_done',
            ])
            ->withCount([
                'children as children_total',
                'children as children_done' => fn ($q) =>
                $q->whereHas('status', fn ($s) => $s->where('is_done', true)),
            ])
            ->get();

        // Map issues into each status list with presentational fields.
        foreach ($statuses as $status) {
            $this->lists[$status->id] = $issues
                ->where('issue_status_id', $status->id)
                ->map(function (Issue $i): array {
                    $type = $i->type; // may be null
                    $tier = $type?->tier?->value ?? 'other';

                    // Prefer the model helpers if present; otherwise fall back to a static palette.
                    $typeColor = $type?->badgeColor() ?? match ($tier) {
                        'epic'    => '#7e57c2',
                        'story'   => '#1e88e5',
                        'task'    => '#9e9e9e',
                        'subtask' => '#78909C',
                        default   => '#607D8B',
                    };

                    $typeIcon = $type?->iconName() ?? match ($tier) {
                        'epic'    => 'all_inclusive',
                        'story'   => 'menu_book',
                        'task'    => 'check_box',
                        'subtask' => 'subdirectory_arrow_right',
                        default   => 'filter_none',
                    };

                    $progress = $i->children_total > 0
                        ? (int) floor(($i->children_done / max(1, $i->children_total)) * 100)
                        : null;

                    return [
                        'id'             => (int) $i->id,
                        'summary'        => $i->summary,
                        'description'    => $i->description,
                        'tier'           => $tier,
                        'type_name'      => $type?->name,
                        'type_color'     => $typeColor,
                        'type_icon'      => $typeIcon,
                        'children_total' => (int) $i->children_total,
                        'children_done'  => (int) $i->children_done,
                        'progress'       => $progress,
                    ];
                })
                ->values()
                ->all();
        }
    }

    /**
     * Reorder (and optionally re-parent) issues inside a status column.
     *
     * @param  int         $toStatusId
     * @param  array<int>  $orderedIssueIds
     * @throws Throwable
     */
    public function reorder(int $toStatusId, array $orderedIssueIds): void
    {
        $this->authorize('update', $this->project);

        $issueHasOrder = Schema::hasColumn('issues', 'order');

        DB::transaction(function () use ($toStatusId, $orderedIssueIds, $issueHasOrder): void {
            foreach ($orderedIssueIds as $index => $issueId) {
                $payload = [
                    'issue_status_id' => $toStatusId,
                ];

                if ($issueHasOrder) {
                    $payload['order'] = $index + 1;
                }

                Issue::query()
                    ->whereKey($issueId)
                    ->where('project_id', $this->project->id)
                    ->update($payload);
            }
        });

        // Simple refresh
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

    public function render(): View
    {
        return view('livewire.projects.kanban-board');
    }
}
