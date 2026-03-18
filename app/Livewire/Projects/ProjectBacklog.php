<?php

namespace App\Livewire\Projects;

use App\Enums\SprintState;
use App\Models\Issue;
use App\Models\IssueStatus;
use App\Models\Project;
use App\Models\Sprint;
use App\Models\User;
use App\Services\Projects\BacklogPlanningService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Throwable;

final class ProjectBacklog extends Component
{
    public Project $project;

    public string $search = '';

    public string $assigneeId = '';

    public string $statusId = '';

    public string $focusSprintId = '';

    public bool $onlyUnsized = false;

    public bool $onlyUnassigned = false;

    public string $bulkSprintId = '';

    /** @var array<int, string> */
    public array $selectedIssueIds = [];

    /** @var array<int, array<string, mixed>> */
    public array $backlog = [];

    /** @var array<int, array<string, mixed>> */
    public array $sprints = [];

    /** @var array<int, array{id:string,name:string,state:string}> */
    public array $sprintOptions = [];

    /** @var array<int, array{id:string,name:string}> */
    public array $assigneeOptions = [];

    /** @var array<int, array{id:string,name:string,color:string}> */
    public array $statusOptions = [];

    /** @var array{backlog_count:int,visible_backlog_count:int,unsized_count:int,unassigned_count:int,planning_sprint_count:int} */
    public array $summary = [
        'backlog_count' => 0,
        'visible_backlog_count' => 0,
        'unsized_count' => 0,
        'unassigned_count' => 0,
        'planning_sprint_count' => 0,
    ];

    public bool $canRank = true;

    public string $planningUnit = 'story_points';

    public string $planningUnitLabel = 'Story points';

    public string $planningUnitShort = 'pts';

    public ?int $projectVelocityTarget = null;

    public bool $showCreateSprint = false;

    /** @var array{name:string,goal:string,start_date:string,end_date:string,start_now:bool,capacity:string} */
    public array $newSprint = [
        'name' => '',
        'goal' => '',
        'start_date' => '',
        'end_date' => '',
        'start_now' => false,
        'capacity' => '',
    ];

    public function mount(Project $project): void
    {
        $this->authorize('view', $project);

        $this->project = $project;
        $this->setPlanningUnit();
        $this->loadData();
    }

    public function updated(string $property): void
    {
        if (! in_array($property, [
            'search',
            'assigneeId',
            'statusId',
            'focusSprintId',
            'onlyUnsized',
            'onlyUnassigned',
        ], true)) {
            return;
        }

        $this->selectedIssueIds = [];
        $this->loadData();
    }

    public function selectVisibleBacklog(): void
    {
        $this->selectedIssueIds = array_values(array_map(
            static fn (array $issue): string => (string) $issue['id'],
            $this->backlog
        ));
    }

    public function clearSelection(): void
    {
        $this->selectedIssueIds = [];
    }

    public function openCreateSprint(): void
    {
        $this->authorize('create', [Sprint::class, $this->project]);

        $this->resetValidation();
        $this->newSprint = [
            'name' => '',
            'goal' => '',
            'start_date' => '',
            'end_date' => '',
            'start_now' => false,
            'capacity' => $this->projectVelocityTarget !== null ? (string) $this->projectVelocityTarget : '',
        ];
        $this->showCreateSprint = true;
    }

    public function createSprint(): void
    {
        $this->authorize('create', [Sprint::class, $this->project]);

        if (($this->newSprint['capacity'] ?? null) === '') {
            $this->newSprint['capacity'] = null;
        }

        $data = $this->validate([
            'newSprint.name' => ['required', 'string', 'max:255'],
            'newSprint.goal' => ['nullable', 'string', 'max:2000'],
            'newSprint.start_date' => ['nullable', 'date'],
            'newSprint.end_date' => ['nullable', 'date', 'after_or_equal:newSprint.start_date'],
            'newSprint.start_now' => ['boolean'],
            'newSprint.capacity' => ['nullable', 'integer', 'min:0'],
        ])['newSprint'];

        $sortOrder = ((int) $this->project->sprints()->max('sort_order')) + 1;

        $sprint = new Sprint([
            'project_id' => $this->project->id,
            'name' => $data['name'],
            'goal' => $data['goal'] ?: null,
            'start_date' => $data['start_date'] ?: null,
            'end_date' => $data['end_date'] ?: null,
            'capacity' => $data['capacity'] !== '' ? (int) $data['capacity'] : null,
            'state' => SprintState::Planned,
            'sort_order' => $sortOrder,
        ]);

        $sprint->save();

        $this->showCreateSprint = false;

        if (! empty($data['start_now'])) {
            $this->startSprint((string) $sprint->id);

            return;
        }

        $this->loadData();
        $this->dispatch('notify', type: 'success', message: 'Sprint created.');
    }

    /**
     * @throws Throwable
     */
    public function startSprint(string $sprintId): void
    {
        /** @var Sprint $sprint */
        $sprint = $this->project->sprints()->findOrFail($sprintId);
        $this->authorize('start', $sprint);

        DB::transaction(function () use ($sprint): void {
            Sprint::query()
                ->where('project_id', $this->project->id)
                ->where('state', SprintState::Active->value)
                ->whereKeyNot($sprint->id)
                ->update([
                    'state' => SprintState::Closed->value,
                    'end_date' => now()->toDateString(),
                ]);

            $sprint->update([
                'state' => SprintState::Active,
                'start_date' => $sprint->start_date ?: now()->toDateString(),
            ]);
        });

        $this->showCreateSprint = false;
        $this->loadData();
        $this->dispatch('notify', type: 'success', message: 'Sprint started.');
    }

    public function updateSprintCapacity(string $sprintId, mixed $value): void
    {
        /** @var Sprint $sprint */
        $sprint = $this->project->sprints()->findOrFail($sprintId);
        $this->authorize('update', $sprint);

        $payload = [
            'capacity' => $value === '' || $value === null ? null : $value,
        ];

        validator($payload, [
            'capacity' => ['nullable', 'integer', 'min:0'],
        ])->validate();

        $sprint->update(['capacity' => $payload['capacity']]);

        $this->loadData();
        $this->dispatch('notify', type: 'success', message: 'Sprint capacity updated.');
    }

    public function moveSelectedToSprint(): void
    {
        if (! auth()->user()?->can('issues.update')) {
            $this->dispatch('notify', type: 'error', message: 'No permission.');

            return;
        }

        if ($this->bulkSprintId === '') {
            $this->dispatch('notify', type: 'error', message: 'Choose a sprint first.');

            return;
        }

        $moved = $this->planning()->moveIssues(
            $this->project,
            $this->selectedIssueIds,
            $this->bulkSprintId
        );

        $this->selectedIssueIds = [];
        $this->loadData();
        $this->dispatch('notify', type: 'success', message: $moved === 1 ? '1 issue moved.' : "{$moved} issues moved.");
    }

    public function moveIssueToSprint(string $issueId, string $sprintId): void
    {
        if (! auth()->user()?->can('issues.update')) {
            $this->dispatch('notify', type: 'error', message: 'No permission.');

            return;
        }

        if ($sprintId === '') {
            return;
        }

        $this->planning()->moveIssues($this->project, [$issueId], $sprintId);

        $this->selectedIssueIds = array_values(array_diff($this->selectedIssueIds, [$issueId]));
        $this->loadData();
        $this->dispatch('notify', type: 'success', message: 'Issue moved to sprint.');
    }

    public function moveIssueToBacklog(string $issueId): void
    {
        if (! auth()->user()?->can('issues.update')) {
            $this->dispatch('notify', type: 'error', message: 'No permission.');

            return;
        }

        $this->planning()->moveIssues($this->project, [$issueId], null);

        $this->loadData();
        $this->dispatch('notify', type: 'success', message: 'Issue moved to backlog.');
    }

    #[On('backlog:reorder')]
    public function reorderFromEvent(string $lane, array $orderedIssueIds, ?string $sprintId = null): void
    {
        if (! $this->canRank) {
            return;
        }

        if (! auth()->user()?->can('issues.update')) {
            return;
        }

        if ($lane === 'backlog') {
            $this->planning()->reorderLane($this->project, null, $orderedIssueIds);
        } elseif ($lane === 'sprint' && $sprintId) {
            $this->planning()->reorderLane($this->project, $sprintId, $orderedIssueIds);
        }

        $this->loadData();
    }

    public function render(): View
    {
        return view('livewire.projects.project-backlog');
    }

    private function loadData(): void
    {
        $this->canRank = $this->search === ''
            && $this->assigneeId === ''
            && $this->statusId === ''
            && ! $this->onlyUnsized
            && ! $this->onlyUnassigned;

        $this->loadStatusOptions();
        $this->loadAssigneeOptions();

        $allPlanningSprints = $this->planningSprintsQuery()->get([
            'id',
            'name',
            'state',
            'goal',
            'start_date',
            'end_date',
            'capacity',
            'sort_order',
        ]);

        $this->sprintOptions = $allPlanningSprints
            ->map(fn (Sprint $sprint): array => [
                'id' => (string) $sprint->id,
                'name' => $sprint->name,
                'state' => (string) ($sprint->state?->value ?? $sprint->state),
            ])
            ->values()
            ->all();

        if ($this->focusSprintId !== '' && ! $allPlanningSprints->contains('id', $this->focusSprintId)) {
            $this->focusSprintId = '';
        }

        if ($this->bulkSprintId !== '' && ! $allPlanningSprints->contains('id', $this->bulkSprintId)) {
            $this->bulkSprintId = '';
        }

        $displayedSprints = $allPlanningSprints
            ->when(
                $this->focusSprintId !== '',
                fn (Collection $collection) => $collection->where('id', $this->focusSprintId)->values()
            );

        $this->backlog = $this->filteredIssuesQuery()
            ->whereNull('sprint_id')
            ->orderedForPlanning()
            ->get()
            ->map(fn (Issue $issue): array => $this->mapIssue($issue))
            ->all();

        $this->summary = $this->buildSummary($allPlanningSprints->count(), count($this->backlog));

        $displayedSprintIds = $displayedSprints
            ->pluck('id')
            ->map(fn ($id): string => (string) $id)
            ->all();

        $issuesBySprint = collect();

        if ($displayedSprintIds !== []) {
            $issuesBySprint = $this->filteredIssuesQuery()
                ->whereIn('sprint_id', $displayedSprintIds)
                ->orderedForPlanning()
                ->get()
                ->groupBy(fn (Issue $issue): string => (string) $issue->sprint_id);
        }

        $totalsBySprint = Issue::query()
            ->where('project_id', $this->project->id)
            ->whereIn('sprint_id', $displayedSprintIds)
            ->select('sprint_id')
            ->selectRaw('COUNT(*) as issue_count')
            ->selectRaw('COALESCE(SUM(story_points), 0) as story_points_total')
            ->selectRaw('COALESCE(SUM(estimate_minutes), 0) as estimate_minutes_total')
            ->groupBy('sprint_id')
            ->get()
            ->keyBy(fn (Issue $issue): string => (string) $issue->sprint_id);

        $this->sprints = $displayedSprints
            ->map(function (Sprint $sprint) use ($issuesBySprint, $totalsBySprint): array {
                $laneIssues = $issuesBySprint->get((string) $sprint->id, collect())
                    ->map(fn (Issue $issue): array => $this->mapIssue($issue))
                    ->values()
                    ->all();

                $totals = $totalsBySprint->get((string) $sprint->id);
                $committed = $this->planningUnit === 'estimate_minutes'
                    ? (int) ($totals?->estimate_minutes_total ?? 0)
                    : (int) ($totals?->story_points_total ?? 0);

                $capacity = $sprint->capacity ?? $this->projectVelocityTarget;
                $remaining = $capacity !== null ? $capacity - $committed : null;

                return [
                    'id' => (string) $sprint->id,
                    'name' => $sprint->name,
                    'state' => (string) ($sprint->state?->value ?? $sprint->state),
                    'goal' => $sprint->goal,
                    'start_date' => $sprint->start_date?->toDateString(),
                    'end_date' => $sprint->end_date?->toDateString(),
                    'capacity' => $capacity,
                    'capacity_input' => $sprint->capacity !== null ? (string) $sprint->capacity : '',
                    'capacity_source' => $sprint->capacity !== null ? 'Sprint' : ($this->projectVelocityTarget !== null ? 'Project default' : 'Unset'),
                    'committed' => $committed,
                    'committed_display' => $this->formatPlanningValue($committed),
                    'capacity_display' => $capacity !== null ? $this->formatPlanningValue($capacity) : null,
                    'remaining' => $remaining,
                    'remaining_display' => $remaining !== null ? $this->formatPlanningValue(abs($remaining)) : null,
                    'is_over_capacity' => $remaining !== null && $remaining < 0,
                    'issue_count' => (int) ($totals?->issue_count ?? 0),
                    'visible_issue_count' => count($laneIssues),
                    'issues' => $laneIssues,
                ];
            })
            ->values()
            ->all();
    }

    private function setPlanningUnit(): void
    {
        $estimateUnit = (string) $this->project->setting('issues.estimate_unit', 'story_points');

        if ($estimateUnit === 'story_points') {
            $this->planningUnit = 'story_points';
            $this->planningUnitLabel = 'Story points';
            $this->planningUnitShort = 'pts';
        } else {
            $this->planningUnit = 'estimate_minutes';
            $this->planningUnitLabel = 'Estimated time';
            $this->planningUnitShort = 'min';
        }

        $target = $this->project->setting('sprints.velocity_target');
        $this->projectVelocityTarget = is_numeric($target) ? (int) $target : null;
    }

    private function loadStatusOptions(): void
    {
        $statuses = $this->project->issueStatuses()
            ->select('issue_statuses.id', 'issue_statuses.name', 'issue_statuses.color')
            ->orderBy('project_issue_statuses.order')
            ->get();

        if ($statuses->isEmpty()) {
            $statuses = IssueStatus::query()
                ->orderBy('name')
                ->get(['id', 'name', 'color']);
        }

        $this->statusOptions = $statuses
            ->map(fn (IssueStatus $status): array => [
                'id' => (string) $status->id,
                'name' => $status->name,
                'color' => $status->color,
            ])
            ->values()
            ->all();
    }

    private function loadAssigneeOptions(): void
    {
        $assigneeIds = Issue::query()
            ->where('project_id', $this->project->id)
            ->whereNotNull('assignee_id')
            ->distinct()
            ->pluck('assignee_id');

        $this->assigneeOptions = User::query()
            ->whereIn('id', $assigneeIds)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (User $user): array => [
                'id' => (string) $user->id,
                'name' => $user->name,
            ])
            ->values()
            ->all();
    }

    private function filteredIssuesQuery(): Builder
    {
        $query = Issue::query()
            ->where('project_id', $this->project->id)
            ->select([
                'id',
                'project_id',
                'sprint_id',
                'key',
                'number',
                'summary',
                'assignee_id',
                'issue_status_id',
                'issue_priority_id',
                'issue_type_id',
                'story_points',
                'estimate_minutes',
                'planning_order',
                'created_at',
            ])
            ->with([
                'assignee:id,name,profile_photo_path',
                'status:id,name,color',
                'priority:id,name,color',
                'type:id,name,tier',
            ]);

        $term = trim($this->search);

        if ($term !== '') {
            $numberSearch = null;
            $keySearch = null;

            if (preg_match('/^#?(\d+)$/', $term, $matches) === 1) {
                $numberSearch = (int) $matches[1];
            }

            if (preg_match('/^[A-Z]+-\d+$/i', $term) === 1) {
                $keySearch = strtoupper($term);
            }

            $query->where(function (Builder $builder) use ($term, $numberSearch, $keySearch): void {
                $builder
                    ->where('summary', 'like', '%'.$term.'%')
                    ->orWhere('description', 'like', '%'.$term.'%');

                if ($numberSearch !== null) {
                    $builder->orWhere('number', $numberSearch);
                }

                if ($keySearch !== null) {
                    $builder->orWhere('key', $keySearch);
                }
            });
        }

        if ($this->assigneeId !== '') {
            $query->where('assignee_id', $this->assigneeId);
        }

        if ($this->statusId !== '') {
            $query->where('issue_status_id', $this->statusId);
        }

        if ($this->onlyUnassigned) {
            $query->whereNull('assignee_id');
        }

        if ($this->onlyUnsized) {
            $this->applyUnsizedFilter($query);
        }

        return $query;
    }

    private function buildSummary(int $planningSprintCount, int $visibleBacklogCount): array
    {
        $baseBacklogQuery = Issue::query()
            ->where('project_id', $this->project->id)
            ->whereNull('sprint_id');

        $unsizedQuery = clone $baseBacklogQuery;
        $this->applyUnsizedFilter($unsizedQuery);

        return [
            'backlog_count' => (clone $baseBacklogQuery)->count(),
            'visible_backlog_count' => $visibleBacklogCount,
            'unsized_count' => $unsizedQuery->count(),
            'unassigned_count' => (clone $baseBacklogQuery)->whereNull('assignee_id')->count(),
            'planning_sprint_count' => $planningSprintCount,
        ];
    }

    private function applyUnsizedFilter(Builder $query): Builder
    {
        $column = $this->planningUnit;

        return $query->where(function (Builder $builder) use ($column): void {
            $builder
                ->whereNull($column)
                ->orWhere($column, '<=', 0);
        });
    }

    private function mapIssue(Issue $issue): array
    {
        $type = $issue->type;
        $priority = $issue->priority;
        $status = $issue->status;
        $assignee = $issue->assignee;
        $metricValue = $this->planningUnit === 'estimate_minutes'
            ? $issue->estimate_minutes
            : $issue->story_points;

        return [
            'id' => (string) $issue->id,
            'key' => (string) $issue->key,
            'summary' => (string) ($issue->summary ?? 'Untitled issue'),
            'issue_url' => route('issues.show', ['project' => $this->project, 'issue' => $issue->key]),
            'type_color' => $type?->badgeColor() ?? '#607D8B',
            'type_icon' => $type?->iconName() ?? 'filter_none',
            'status_name' => $status?->name ?? 'Unknown',
            'status_color' => $status?->color ?? '#78909C',
            'priority_name' => $priority?->name,
            'priority_color' => $priority?->color ?? '#6c757d',
            'assignee_name' => $assignee?->name,
            'assignee_photo_url' => $assignee?->profile_photo_url,
            'metric_value' => $metricValue,
            'metric_display' => $this->formatPlanningValue($metricValue),
            'is_unsized' => $metricValue === null || $metricValue <= 0,
        ];
    }

    private function formatPlanningValue(?int $value): string
    {
        if ($value === null || $value <= 0) {
            return 'No estimate';
        }

        if ($this->planningUnit === 'story_points') {
            return $value.' pts';
        }

        $hours = intdiv($value, 60);
        $minutes = $value % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}m";
        }

        if ($hours > 0) {
            return "{$hours}h";
        }

        return "{$minutes}m";
    }

    private function planningSprintsQuery(): HasMany
    {
        return $this->project->sprints()
            ->whereIn('state', [
                SprintState::Active->value,
                SprintState::Planned->value,
            ])
            ->orderByRaw(
                'CASE WHEN state = ? THEN 0 WHEN state = ? THEN 1 ELSE 2 END',
                [SprintState::Active->value, SprintState::Planned->value]
            )
            ->orderBy('sort_order')
            ->orderBy('start_date')
            ->orderBy('created_at');
    }

    private function planning(): BacklogPlanningService
    {
        return app(BacklogPlanningService::class);
    }
}
