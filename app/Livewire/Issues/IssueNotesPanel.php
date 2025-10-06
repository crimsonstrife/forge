<?php

namespace App\Livewire\Issues;

use App\Models\Issue;
use App\Models\Note;
use App\Services\Issues\IssueDefaultsResolver;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Manage notes attached to a specific Issue.
 */
final class IssueNotesPanel extends Component
{
    public Issue $issue;

    /** @var Collection<int, Note> */
    public $notes;

    #[Validate('nullable|string|max:255')]
    public ?string $newTitle = null;

    #[Validate('nullable|string|max:10000')]
    public ?string $newBody = null;

    public ?string $editingId = null;

    public string $editingTitle = '';
    public string $editingBody = '';

    public function mount(Issue $issue): void
    {
        $this->issue = $issue;
        $this->refresh();
    }

    public function render(): View
    {
        return view('livewire.issues.issue-notes-panel');
    }

    public function refresh(): void
    {
        $this->notes = Note::query()
            ->where('issue_id', $this->issue->id)
            ->latest('created_at')
            ->get(['id','user_id','title','body','created_at']);
    }

    public function add(): void
    {
        $this->validate();
        Note::query()->create([
            'user_id' => Auth::id(),
            'issue_id' => $this->issue->id,
            'title' => $this->newTitle ?: null,
            'body' => $this->newBody ?: null,
            'tags' => null,
        ]);

        $this->reset(['newTitle','newBody']);
        $this->refresh();
    }

    public function edit(string $id): void
    {
        /** @var Note|null $n */
        $n = Note::query()->where('issue_id', $this->issue->id)->whereKey($id)->first();
        if ($n === null) {
            return;
        }

        $this->editingId = $n->id;
        $this->editingTitle = (string)($n->title ?? '');
        $this->editingBody = (string)($n->body ?? '');
    }

    public function save(): void
    {
        if ($this->editingId === null) {
            return;
        }

        /** @var Note|null $n */
        $n = Note::query()->where('issue_id', $this->issue->id)->whereKey($this->editingId)->first();
        if ($n === null) {
            return;
        }

        // Author or anyone who can update the issue
        if ($n->user_id !== (string) Auth::id()) {
            $this->authorize('update', $this->issue);
        }

        $n->title = trim($this->editingTitle) !== '' ? $this->editingTitle : null;
        $n->body = trim($this->editingBody) !== '' ? $this->editingBody : null;
        $n->save();

        $this->editingId = null;
        $this->editingTitle = '';
        $this->editingBody = '';
        $this->refresh();
    }

    public function cancel(): void
    {
        $this->editingId = null;
        $this->editingTitle = '';
        $this->editingBody = '';
    }

    public function delete(string $id): void
    {
        /** @var Note|null $n */
        $n = Note::query()->where('issue_id', $this->issue->id)->whereKey($id)->first();
        if ($n === null) {
            return;
        }

        if ($n->user_id !== (string) Auth::id()) {
            $this->authorize('update', $this->issue);
        }

        $n->delete();
        $this->refresh();
    }

    public function convertToIssue(string $id): void
    {
        /** @var Note|null $n */
        $n = Note::query()->where('issue_id', $this->issue->id)->whereKey($id)->first();
        if ($n === null) {
            return;
        }

        $this->authorize('create', [Issue::class, $this->issue->project]);

        $summary = $n->title ?: 'New Issue';
        $defaults = IssueDefaultsResolver::for($this->issue->project);

        $new = Issue::query()->create([
            'project_id'        => $this->issue->project_id,
            'issue_type_id'     => $defaults->typeId(),
            'issue_status_id'   => $defaults->statusId(),
            'issue_priority_id' => $defaults->priorityId(),
            'summary'           => $summary,
            'description'       => $n->body,
            'assignee_id'       => Auth::id(),
        ]);

        $n->issue_id = $new->id; // link note to the newly created issue
        $n->save();

        $this->dispatch('banner-message', type: 'success', message: 'Issue created from note.');
        $this->refresh();
    }
}
