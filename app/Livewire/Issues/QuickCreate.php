<?php

namespace App\Livewire\Issues;

use App\Models\Issue;
use App\Models\Project;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class QuickCreate extends Component
{
    use AuthorizesRequests;

    public Project $project;

    #[Validate('required|string|max:200')]
    public string $summary = '';

    public function mount(Project $project): void
    {
        $this->project = $project;
        abort_unless(auth()->user()->can('issues.create'), 403);
    }

    public function save(): void
    {
        $this->validate();

        $issue = new Issue();
        $issue->project_id = $this->project->id;
        $issue->summary    = $this->summary;
        $issue->issue_status_id  = $this->project->initialStatusId();
        $issue->issue_reporter_id = auth()->id();
        $issue->issue_priority_id = $this->project->defaultPriorityId();
        $issue->save();

        $this->summary = '';
        session()->flash('flash.banner', 'Issue created');
        session()->flash('flash.bannerStyle', 'success');

        $this->dispatch('issue-created', id: $issue->id);
    }

    public function render(): mixed
    {
        return view('livewire.issues.quick-create');
    }
}
