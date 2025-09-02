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
    public ?string $projectId = null;
    public ?string $parentId = null;

    /** Project picker (global page only) */
    public ?string $selectedProjectId = null;

    /** Resolved models */
    public ?Project $project = null;
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

    /** @var array<int, array{id:string,name:string,key:string}> */
    public array $projectOptions = [];

    public function mount(?string $projectId = null, ?string $parentId = null, array $projectOptions = []): void
    {
        $this->projectId      = $projectId;
        $this->parentId       = $parentId;
        $this->projectOptions = $projectOptions;

        if ($this->projectId) {
            $this->hydrateProjectAndOptions($this->projectId);
            $this->hydrateParentIfAny();
        }
    }

    public function rules(): array
    {
        $rules = [
            'summary'         => 'required|string|max:200',
            'description'     => 'nullable|string|max:10000',
            'assigneeId'      => 'nullable|uuid|exists:users,id',
            'typeId'          => 'nullable|exists:issue_types,id',
            'priorityId'      => 'nullable|exists:issue_priorities,id',
            'storyPoints'     => 'nullable|integer|min:0',
            'estimateMinutes' => 'nullable|integer|min:0',
        ];

        if ($this->project === null) {
            $rules['selectedProjectId'] = 'required|uuid|exists:projects,id';
        }

        return $rules;
    }

    public function updatedSelectedProjectId(?string $value): void
    {
        $this->resetErrorBag();
        if ($value) {
            $this->hydrateProjectAndOptions($value);
            $this->hydrateParentIfAny();
        } else {
            $this->project = null;
            $this->typeOptions = $this->priorityOptions = $this->assigneeOptions = [];
            $this->typeId = $this->priorityId = null;
        }
    }

    public function save(): void
    {
        $this->validate();

        $project = $this->project ?? Project::query()->select(['id','key'])->findOrFail($this->selectedProjectId);
        $this->authorize('create', [Issue::class, $project]);

        if ($this->parent) {
            $subtaskId = $project->issueTypes()->where('issue_types.key','SUBTASK')->value('issue_types.id');
            if ($subtaskId) {
                $this->typeId = (string) $subtaskId;
            }
        }

        $statusId = session('project.initial_status.' . $project->id)
            ?: $project->issueStatuses()->orderBy('project_issue_statuses.order')->value('issue_statuses.id');

        if (! $statusId) {
            $this->addError('typeId', 'Cannot create issue: no initial status configured.');
            return;
        }

        $issue = new Issue([
            'project_id'        => $project->getKey(),
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

        $this->redirectRoute('issues.show', ['project' => $project, 'issue' => $issue]);
    }

    public function render(): View
    {
        return view('livewire.issues.create-issue-form');
    }

    private function hydrateProjectAndOptions(string $id): void
    {
        $this->project   = Project::query()->select(['id','key'])->findOrFail($id);
        $this->projectId = $id;

        $this->authorize('create', [Issue::class, $this->project]);

        $this->typeOptions = $this->project->issueTypes()
            ->orderBy('project_issue_types.order')
            ->get(['issue_types.id','issue_types.name'])
            ->map(fn ($t) => ['id' => (string) $t->id, 'name' => $t->name])
            ->all();

        $this->priorityOptions = $this->project->issuePriorities()
            ->orderBy('project_issue_priorities.order')
            ->get(['issue_priorities.id','issue_priorities.name'])
            ->map(fn ($p) => ['id' => (string) $p->id, 'name' => $p->name])
            ->all();

        $this->assigneeOptions = $this->project->users()
            ->orderBy('name')
            ->get(['users.id','users.name'])
            ->map(fn ($u) => ['id' => (string) $u->id, 'name' => $u->name])
            ->all();

        $preferredTypeId = null;
        if ($this->parent) {
            $preferredTypeId = $this->project->issueTypes()->where('issue_types.key','SUBTASK')->value('issue_types.id');
        }
        $this->typeId     = $preferredTypeId ? (string) $preferredTypeId : ($this->typeOptions[0]['id'] ?? null);
        $this->priorityId = $this->priorityOptions[0]['id'] ?? null;

        $initial = $this->project->issueStatuses()->wherePivot('is_initial', true)->value('issue_statuses.id')
            ?: $this->project->issueStatuses()->orderBy('project_issue_statuses.order')->value('issue_statuses.id');

        if ($initial) {
            session(['project.initial_status.' . $this->project->id => $initial]);
        }
    }

    private function hydrateParentIfAny(): void
    {
        if (! $this->parentId || ! $this->project) {
            return;
        }

        $this->parent = Issue::query()
            ->select(['id','key','summary','project_id'])
            ->where('project_id', $this->project->id)
            ->findOrFail($this->parentId);
    }
}
