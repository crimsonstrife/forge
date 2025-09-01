<?php

namespace App\Livewire\Issues;

use App\Models\Issue;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class CreateIssueForm extends Component
{
    use AuthorizesRequests;

    /** Incoming scalars from Blade */
    public string $projectId;
    public ?string $parentId = null;

    /** Resolved models for internal use */
    public Project $project;
    public ?Issue $parent = null;

    #[Validate('required|string|max:200')]
    public string $summary = '';

    #[Validate('nullable|string|max:10000')]
    public ?string $description = null;

    public ?int $storyPoints = null;
    public ?int $estimateMinutes = null;
    #[Validate('nullable|uuid|exists:users,id')]
    public ?string $assigneeId = null;

    #[Validate('required|exists:issue_types,id')]
    public ?string $typeId = null;

    #[Validate('nullable|exists:issue_priorities,id')]
    public ?string $priorityId = null;

    /** @var array<int, array{id:string,name:string}> */
    public array $typeOptions = [];

    /** @var array<int, array{id:string,name:string}> */
    public array $priorityOptions = [];

    /** @var array<int, array{id:string,name:string}> */
    public array $assigneeOptions = [];

    public function mount(string $projectId, ?string $parentId = null): void
    {
        $this->project   = Project::query()->select(['id', 'key'])->findOrFail($projectId);
        $this->projectId = $projectId;

        $this->parentId  = $parentId;
        if ($this->parentId) {
            // Constrain to same project to be safe
            $this->parent = Issue::query()
                ->select(['id', 'key', 'summary', 'project_id'])
                ->where('project_id', $this->project->id)
                ->findOrFail($this->parentId);
        }

        $this->authorize('create', [Issue::class, $this->project]);

        // Options
        $this->typeOptions = $this->project->issueTypes()
            ->orderBy('project_issue_types.order')
            ->get(['issue_types.id', 'issue_types.name'])
            ->map(fn ($t) => ['id' => (string) $t->id, 'name' => $t->name])
            ->all();

        $this->priorityOptions = $this->project->issuePriorities()
            ->orderBy('project_issue_priorities.order')
            ->get(['issue_priorities.id', 'issue_priorities.name'])
            ->map(fn ($p) => ['id' => (string) $p->id, 'name' => $p->name])
            ->all();

        // Assignees: users attached to the project
        $this->assigneeOptions = $this->project->users()
            ->orderBy('name')
            ->get(['users.id','users.name'])
            ->map(fn($u) => ['id' => (string) $u->id, 'name' => $u->name])
            ->all();

        // Default type: SUBTASK if creating under a parent, otherwise first available
        $preferredTypeId = null;
        if ($this->parent) {
            $preferredTypeId = $this->project->issueTypes()
                ->where('issue_types.key', 'SUBTASK')
                ->value('issue_types.id');
        }
        $this->typeId     = $preferredTypeId ? (string) $preferredTypeId : ($this->typeOptions[0]['id'] ?? null);
        $this->priorityId = $this->priorityOptions[0]['id'] ?? null;

        // Initial status (friendly fallback if misconfigured)
        $initial = $this->project->issueStatuses()->wherePivot('is_initial', true)->value('issue_statuses.id')
            ?: $this->project->issueStatuses()->orderBy('project_issue_statuses.order')->value('issue_statuses.id');

        if ($initial) {
            session(['project.initial_status.' . $this->project->id => $initial]);
        } else {
            $this->addError('typeId', 'This project has no initial status configured.');
        }
    }

    public function save(): void
    {
        $this->validate();

        // Enforce SUBTASK if creating under a parent (can’t be bypassed via devtools)
        if ($this->parent) {
            $subtaskId = $this->project->issueTypes()->where('issue_types.key','SUBTASK')->value('issue_types.id');
            if ($subtaskId) {
                $this->typeId = (string) $subtaskId;
            }
        }

        $statusId = session('project.initial_status.' . $this->project->id)
            ?: $this->project->issueStatuses()->orderBy('project_issue_statuses.order')->value('issue_statuses.id');

        if (!$statusId) {
            $this->addError('typeId', 'Cannot create issue: no initial status configured.');
            return;
        }

        $issue = new Issue([
            'project_id'        => $this->project->getKey(),
            'parent_id'         => $this->parent?->getKey(),
            'summary'           => $this->summary,
            'description'       => $this->description,
            'story_points'      => $this->storyPoints,
            'estimate_minutes'  => $this->estimateMinutes,
            'issue_type_id'     => $this->typeId,
            'issue_priority_id' => $this->priorityId,
            'issue_status_id'   => $statusId,
            'assignee_id'       => $this->assigneeId,
            'reporter_id'       => auth()->id(),
        ]);

        $issue->save();

        $this->redirectRoute('issues.show', ['project' => $this->project, 'issue' => $issue]);
    }

    public function render(): View
    {
        return view('livewire.issues.create-issue-form');
    }
}
