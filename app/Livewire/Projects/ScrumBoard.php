<?php

namespace App\Livewire\Projects;

use App\Enums\SprintState;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Sprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;

final class ScrumBoard extends Component
{
    public Project $project;

    /** @var array<int, array{id:int,name:string}> */
    public array $sprintColumns = [];

    /** @var array<int, array<int, array{id:string,key:string,summary:string}>> */
    public array $sprintLists = [];

    /** @var array<int, array{id:string,key:string,summary:string}> */
    public array $backlog = [];

    public bool $showCreateSprint = false;
    public bool $showEndSprint = false;
    public bool $showSprintsModal = false;

    public ?string $currentSprintId = null;

    /** @var array{name:string,goal?:string,start_date?:string,end_date?:string,start_now?:bool} */
    public array $newSprint = [
        'name' => '',
        'goal' => '',
        'start_date' => '',
        'end_date' => '',
        'start_now' => false,
    ];

    public bool $rolloverIncomplete = true;

    /** @var array<int, array{id:string,name:string,state:string,start_date?:string,end_date?:string}> */
    public array $sprints = [];

    public function mount(Project $project): void
    {
        $this->authorize('view', $project);
        $this->project = $project;

        // Pick the active sprint if one exists.
        $this->currentSprintId = $this->project->sprints()
            ->where('state', 'Active')
            ->orderByDesc('start_date')
            ->value('id');

        $this->loadData();
    }

    private function loadData(): void
    {
        // Backlog = issues not assigned to any sprint
        $this->backlog = Issue::query()
            ->where('project_id', $this->project->id)
            ->whereNull('sprint_id')
            ->latest()
            ->limit(100)
            ->get(['id', 'key', 'summary', 'issue_status_id'])
            ->map(fn ($i) => [
                'id'      => (string) $i->id,
                'key'     => $i->key,
                'summary' => $i->summary,
            ])->all();

        // Sprint columns = project statuses in defined order
        $statuses = $this->project->issueStatuses()
            ->orderBy('project_issue_statuses.order')
            ->get(['issue_statuses.id', 'issue_statuses.name']);

        $this->sprintColumns = $statuses
            ->map(fn ($s) => ['id' => (int) $s->id, 'name' => $s->name])
            ->values()
            ->all();

        $lists = [];
        foreach ($this->sprintColumns as $col) {
            $lists[$col['id']] = [];
        }

        // Sprint issues = only current sprint (or none if no active sprint)
        $sprintIssues = collect();
        if ($this->currentSprintId !== null) {
            $sprintIssues = Issue::query()
                ->where('project_id', $this->project->id)
                ->where('sprint_id', $this->currentSprintId)
                ->latest()
                ->get(['id', 'key', 'summary', 'issue_status_id']);
        }

        foreach ($sprintIssues as $i) {
            $statusId = (int) $i->issue_status_id;
            if (isset($lists[$statusId])) {
                $lists[$statusId][] = [
                    'id'      => (string) $i->id,
                    'key'     => $i->key,
                    'summary' => $i->summary,
                ];
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
            $this->dispatch('notify', type: 'error', message: 'No permission.');
            return;
        }

        if ($this->currentSprintId === null) {
            $this->dispatch('notify', type: 'error', message: 'No active sprint.');
            return;
        }

        $issue->sprint_id = $this->currentSprintId;
        $issue->save();

        $this->loadData();
    }

    #[On('scrum:move-to-backlog')]
    public function moveToBacklog(string $issueId): void
    {
        $this->authorize('update', $this->project);
        $issue = Issue::where('project_id', $this->project->id)->findOrFail($issueId);

        if (! auth()->user()->can('issues.update')) {
            $this->dispatch('notify', type: 'error', message: 'No permission.');
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
            $this->dispatch('notify', type: 'error', message: 'No permission.');
            return;
        }

        $issue->issue_status_id = $toStatusId;
        $issue->save();

        $this->loadData();
    }

    /** Load sprints list for the “View sprints” modal */
    private function loadSprints(): void
    {
        $this->sprints = $this->project->sprints()
            ->orderByRaw("FIELD(state, ?, ?, ?) ASC", [
                SprintState::Active, SprintState::Planned, SprintState::Closed,
            ])
            ->orderByDesc('start_date')
            ->get(['id','name','state','start_date','end_date'])
            ->map(fn ($s) => [
                'id'         => (string) $s->id,
                'name'       => $s->name,
                'state'      => $s->state,
                'start_date' => $s->start_date?->toDateString(),
                'end_date'   => $s->end_date?->toDateString(),
            ])->all();
    }

    public function openCreateSprint(): void
    {
        $this->authorize('create', [Sprint::class, $this->project]);
        $this->resetValidation();
        $this->newSprint = ['name' => '','goal' => '','start_date' => '','end_date' => '','start_now' => false];
        $this->showCreateSprint = true;
    }

    public function createSprint(): void
    {
        $this->authorize('create', [Sprint::class, $this->project]);

        $data = $this->validate([
            'newSprint.name'       => ['required','string','max:255'],
            'newSprint.goal'       => ['nullable','string','max:2000'],
            'newSprint.start_date' => ['nullable','date'],
            'newSprint.end_date'   => ['nullable','date','after_or_equal:newSprint.start_date'],
            'newSprint.start_now'  => ['boolean'],
        ])['newSprint'];

        $sprint = new Sprint([
            'project_id' => $this->project->id,
            'name'       => $data['name'],
            'goal'       => $data['goal'] ?? null,
            'start_date' => $data['start_date'] ?: null,
            'end_date'   => $data['end_date'] ?: null,
            'state'      => SprintState::Planned,
            'sort_order' => 0,
        ]);

        $sprint->save();

        if (! empty($data['start_now'])) {
            $this->startSprint((string) $sprint->id);
        }

        $this->showCreateSprint = false;
        $this->loadSprints();
        $this->loadData();
        $this->dispatch('notify', type: 'success', message: 'Sprint created.');
    }

    public function openEndSprint(): void
    {
        if (! $this->currentSprintId) {
            $this->dispatch('notify', type: 'error', message: 'No active sprint.');
            return;
        }
        /** @var Sprint $sprint */
        $sprint = Sprint::findOrFail($this->currentSprintId);
        $this->authorize('close', $sprint);

        $this->rolloverIncomplete = true;
        $this->showEndSprint = true;
    }

    /**
     * @throws \Throwable
     */
    public function endCurrentSprint(): void
    {
        if (! $this->currentSprintId) {
            $this->dispatch('notify', type: 'error', message: 'No active sprint.');
            return;
        }

        /** @var Sprint $sprint */
        $sprint = Sprint::findOrFail($this->currentSprintId);
        $this->authorize('close', $sprint);

        DB::transaction(function () use ($sprint) {
            // Move incomplete issues to backlog if requested
            if ($this->rolloverIncomplete) {
                $doneStatusIds = $this->project->issueStatuses()->where('is_done', true)
                    ->pluck('issue_statuses.id');

                Issue::query()
                    ->where('project_id', $this->project->id)
                    ->where('sprint_id', $sprint->id)
                    ->whereNotIn('issue_status_id', $doneStatusIds)
                    ->update(['sprint_id' => null]);
            }

            $sprint->state = SprintState::Closed;
            $sprint->end_date = $sprint->end_date ?: now()->toDateString();
            $sprint->save();

            $this->currentSprintId = null;
        });

        $this->showEndSprint = false;
        $this->loadSprints();
        $this->loadData();
        $this->dispatch('notify', type: 'success', message: 'Sprint ended.');
    }

    public function openSprints(): void
    {
        $this->authorize('viewAny', [Sprint::class, $this->project]);
        $this->loadSprints();
        $this->showSprintsModal = true;
    }

    /** Start a planned sprint and ensure it’s the only active one. */
    public function startSprint(string $sprintId): void
    {
        /** @var Sprint $sprint */
        $sprint = Sprint::forProject($this->project->id)->findOrFail($sprintId);
        $this->authorize('start', $sprint);

        DB::transaction(function () use ($sprint) {
            // Close any existing active sprint for this project
            Sprint::forProject($this->project->id)->active()->update([
                'state'    => SprintState::Closed,
                'end_date' => now()->toDateString(),
            ]);

            $sprint->update([
                'state'      => SprintState::Active,
                'start_date' => $sprint->start_date ?: now()->toDateString(),
            ]);

            $this->currentSprintId = (string) $sprint->id;
        });

        $this->showCreateSprint = false;
        $this->loadSprints();
        $this->loadData();
        $this->dispatch('notify', type: 'success', message: 'Sprint started.');
    }
}
