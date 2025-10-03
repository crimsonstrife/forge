<?php

namespace App\Livewire\Notes;

use App\Models\Issue;
use App\Models\Note;
use App\Models\Project;
use App\Services\Issues\IssueDefaultsResolver;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Quick scratchpad for capturing Notes and converting them to Issues.
 * - If $projectId is provided, "Convert to Issue" targets that project automatically.
 * - Otherwise, user can pick a project from a dropdown shown in the view.
 */
final class QuickCapture extends Component
{
    use AuthorizesRequests;

    public ?string $projectId = null;
    public ?string $selectedProjectId = null;

    #[Validate('nullable|string|max:255')]
    public ?string $title = null;

    #[Validate('nullable|string|max:10000')]
    public ?string $body = null;

    public function mount(?string $projectId = null): void
    {
        $this->projectId = $projectId;
        $this->selectedProjectId = $projectId;

        // Solo-friendly: if no project was passed and there’s only one project, preselect it
        if ($this->projectId === null) {
            /** @var Collection<int,string> $ids */
            $ids = Project::query()->orderBy('name')->limit(2)->pluck('id');
            if ($ids->count() === 1) {
                $this->selectedProjectId = (string) $ids->first();
            }
        }
    }
    /** List of selectable projects when no fixed project is passed. */
    #[Computed]
    public function projectOptions(): Collection
    {
        return $this->projectId
            ? collect()
            : Project::query()->orderBy('name')->get(['id', 'name']);
    }

    public function render(): View
    {
        return view('livewire.notes.quick-capture', [
            'recent' => Note::query()
                ->where('user_id', Auth::id())
                ->with(['issue:id,project_id,key,summary'])
                ->latest()
                ->limit(5)
                ->get(['id','issue_id','title','created_at']),
        ]);
    }

    public function save(): void
    {
        $this->validate();

        Note::query()->create([
            'user_id' => Auth::id(),
            'issue_id' => null,
            'title' => $this->title ?: null,
            'body' => $this->body ?: null,
            'tags' => null,
        ]);

        $this->resetFields();
        $this->dispatch('note-saved');
    }

    public function convertToIssue(): void
    {
        $this->validate();

        $targetProjectId = $this->projectId ?: $this->selectedProjectId;
        if (! $targetProjectId) {
            $this->dispatch('banner-message', type: 'warning', message: 'Choose a project first.');
            return;
        }

        /** @var Project $project */
        $project = Project::query()->findOrFail($targetProjectId);
        $this->authorize('create', [Issue::class, $project]);

        $defaults = IssueDefaultsResolver::for($project);

        $issue = Issue::query()->create([
            'project_id'        => $project->id,
            'issue_type_id'     => $defaults->typeId(),
            'issue_status_id'   => $defaults->statusId(),
            'issue_priority_id' => $defaults->priorityId(),
            'summary'           => $this->title ?: 'New issue',
            'description'       => $this->body ?: null,
            'assignee_id'       => Auth::id(),
        ]);

        Note::query()->create([
            'user_id'  => Auth::id(),
            'issue_id' => $issue->id,
            'title'    => $this->title ?: 'Converted note',
            'body'     => $this->body ?: null,
            'tags'     => null,
        ]);

        $this->resetFields();
        $this->dispatch('issue-created', id: $issue->id);
    }

    private function resetFields(): void
    {
        $this->reset(['title','body']);
    }
}
