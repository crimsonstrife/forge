<?php

namespace App\Livewire\Projects;

use App\Models\Issue;
use App\Models\IssueStatus;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Throwable;

final class KanbanBoard extends Component
{
    public Project $project;

    /** @var array<int, array{id:string,name:string,color:string,is_done:bool}> */
    public array $columns = [];

    /**
     * @var array<int, array<int, array{
     *   id:string,
     *   key:string,
     *   summary:string,
     *   description?:string,
     *   tier?:string,
     *   type_name?:string,
     *   type_color?:string,
     *   type_icon?:string,
     *   children_total?:int,
     *   children_done?:int,
     *   progress?:int|null
     * }>>
     */
    public array $lists = [];

    public string $viewMode = 'kanban';

    public function mount(Project $project): void
    {
        $this->authorize('view', $project);
        $this->project = $project;

        $statuses = $project->issueStatuses()
            ->select(['issue_statuses.id', 'issue_statuses.name', 'issue_statuses.color', 'issue_statuses.is_done'])
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

        $issues = Issue::query()
            ->where('project_id', $project->id)
            ->when(
                $issueHasOrder,
                fn ($q) => $q->select(['id', 'key', 'summary', 'description', 'issue_status_id', 'issue_type_id', 'order', 'number'])
                    ->orderBy('issues.order')->orderBy('issues.number'),
                fn ($q) => $q->select(['id', 'key', 'summary', 'description', 'issue_status_id', 'issue_type_id', 'number'])
                    ->orderBy('issues.number')
            )
            ->with(['type:id,name,tier', 'status:id,is_done'])
            ->withCount([
                'children as children_total',
                'children as children_done' => fn ($q) => $q->whereHas('status', fn ($s) => $s->where('is_done', true)),
            ])
            ->get();

        foreach ($statuses as $status) {
            $this->lists[$status->id] = $issues
                ->where('issue_status_id', $status->id)
                ->map(function (Issue $i): array {
                    $type = $i->type;
                    $tier = $type?->tier?->value ?? 'other';

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
                        'id'             => (string) $i->id,      // UUID as string
                        'key'            => (string) $i->key,     // include key for routing
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

        $this->dispatch('kanban:init');
    }

    /**
     * Reorder (and optionally re-parent) issues inside a status column.
     *
     * @param  int                 $toStatusId
     * @param  array<int,string>   $orderedIssueIds   UUIDs as strings
     * @throws Throwable
     */
    public function reorder(int $toStatusId, array $orderedIssueIds): void
    {
        $this->authorize('update', $this->project);

        $issueHasOrder = Schema::hasColumn('issues', 'order');

        DB::transaction(function () use ($toStatusId, $orderedIssueIds, $issueHasOrder): void {
            foreach ($orderedIssueIds as $index => $issueId) {
                $payload = ['issue_status_id' => $toStatusId];

                if ($issueHasOrder) {
                    $payload['order'] = $index + 1;
                }

                Issue::query()
                    ->whereKey($issueId) // UUID string
                    ->where('project_id', $this->project->id)
                    ->update($payload);
            }
        });

        $this->mount($this->project);
        $this->dispatch('kanban:init');
    }

    /** Open issue by KEY (not ID/UUID) */
    public function openIssue(string $issueKey): void
    {
        // If your Issue model uses getRouteKeyName()='key', you can pass the model.
        // Here we explicitly pass the key param.
        $this->redirectRoute('issues.show', [
            'project' => $this->project->id,
            'issue'   => $issueKey,
        ]);
    }

    public function createIssue(int $statusId): void
    {
        $this->redirectRoute('issues.create', [
            'project' => $this->project->id,
            'status'  => $statusId,
        ]);
    }

    /**
     * Inline update for simple fields.
     *
     * @param  string $issueId   UUID
     * @param  string $field     'summary' | 'description'
     * @param  string|null $value
     */
    public function updateIssueField(string $issueId, string $field, ?string $value): void
    {
        $this->authorize('update', $this->project);

        if (! in_array($field, ['summary', 'description'], true)) {
            throw ValidationException::withMessages(['field' => 'Field not allowed.']);
        }

        // Basic validation
        $rules = [
            'summary'     => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:10000'],
        ];

        $data = [$field => $value];
        validator($data, [$field => $rules[$field]])->validate();

        Issue::query()
            ->whereKey($issueId)
            ->where('project_id', $this->project->id)
            ->update($data);

        // Optimistic UI: refresh lists (cheap) and re-init Sortable
        $this->mount($this->project);
        $this->dispatch('kanban:init');
    }

    public function render(): View
    {
        return view('livewire.projects.kanban-board');
    }
}
