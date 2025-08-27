<?php

namespace App\Livewire\Issues;

use App\Models\Issue;
use App\Models\Project;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Illuminate\Support\Str;

final class CreateIssueForm extends Component
{
    use AuthorizesRequests;

    public Project $project;

    #[Validate('required|string|max:200')]
    public string $summary = '';

    #[Validate('nullable|string|max:10000')]
    public ?string $description = null;

    public array $typeOptions = [];
    public array $priorityOptions = [];
    public array $statusOptions = [];

    public ?string $type_id = null;
    public ?string $priority_id = null;

    public function mount(Project $project): void
    {
        $this->project = $project;

        // simple permission check
        abort_unless(auth()->user()->can('issues.create'), 403);

        $this->typeOptions = $project->issueTypes()->orderBy('project_issue_types.order')
            ->get(['issue_types.id','issue_types.name'])
            ->map(fn ($t) => ['id' => (string)$t->id,'name' => $t->name])->all();

        $this->priorityOptions = $project->issuePriorities()->orderBy('project_issue_priorities.order')
            ->get(['issue_priorities.id','issue_priorities.name'])
            ->map(fn ($p) => ['id' => (string)$p->id,'name' => $p->name])->all();

        $initialStatusId = $project->initialStatusId();

        $this->type_id = $this->typeOptions[0]['id'] ?? null;
        $this->priority_id = $this->priorityOptions[0]['id'] ?? null;

        // Stash initial status in session for use on save
        session(['project.initial_status.'.$project->id => $initialStatusId]);
    }

    public function save()
    {
        $data = $this->validate();

        $issue = new Issue();
        $issue->project_id  = $this->project->id;
        $issue->summary     = $data['summary'];
        $issue->description = $data['description'] ?? null;
        $issue->issue_type_id     = $this->type_id;
        $issue->issue_priority_id = $this->priority_id;
        $issue->issue_status_id   = session('project.initial_status.'.$this->project->id);

        $issue->reporter_id = auth()->id();
        $issue->save();

        session()->forget('project.initial_status.'.$this->project->id);

        session()->flash('flash.banner', 'Issue created');
        session()->flash('flash.bannerStyle', 'success');

        return redirect()->route('issues.show', ['project' => $this->project, 'issue' => $issue]);
    }

    public function render()
    {
        return view('livewire.issues.create-issue-form');
    }
}
