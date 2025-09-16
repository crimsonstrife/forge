<?php

namespace App\Livewire\Issues;

use App\Models\Issue;
use App\Models\IssueLink;
use App\Models\IssueLinkType;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

final class ManageLinks extends Component
{
    use AuthorizesRequests;

    public Issue $issue;

    public string $q = '';

    #[Validate(['typeId' => 'required|uuid|exists:issue_link_types,id'])]
    public ?string $typeId = null;

    /** @var array<int, array{id:string,key:string,summary:string,project_key:string}> */
    public array $results = [];

    public function mount(Issue $issue): void
    {
        $this->authorize('view', $issue);
        $this->issue = $issue;
    }

    #[Computed]
    public function types()
    {
        return IssueLinkType::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'inverse_name', 'is_symmetric']);
    }

    public function updatedQ(string $value): void
    {
        $this->search();
    }

    // (extra belt-and-suspenders in case of modifiers/rename)
    public function updated(string $name, $value): void
    {
        if ($name === 'q') {
            $this->search();
        }
    }

    public function search(): void
    {
        $q = trim($this->q);
        if ($q === '') {
            $this->results = [];
            return;
        }

        $query = Issue::query()
            ->with(['project:id,key'])
            ->whereKeyNot($this->issue->getKey())
            ->where(function ($w) use ($q) {
                $w->where('key', 'like', "%{$q}%")
                    ->orWhere('summary', 'like', "%{$q}%");
            })
            ->limit(15);

        $candidates = $query->get(['id', 'key', 'summary', 'project_id']);

        // If Gate blocks everything, fall back to showing matches the user can *likely* see.
        $visible = $candidates->filter(function (Issue $i) {
            try {
                return Gate::allows('view', $i);
            } catch (\Throwable $e) {
                return false;
            }
        });

        if ($visible->isEmpty() && $candidates->isNotEmpty()) {
            // Fallback: show raw matches
            $visible = $candidates;
        }

        $this->results = $visible->map(fn (Issue $i) => [
            'id'          => $i->getKey(),
            'key'         => $i->key,
            'summary'     => (string) $i->summary,
            'project_key' => (string) $i->project?->key,
        ])->values()->all();
    }


    public function link(string $toIssueId): void
    {
        $this->authorize('link', $this->issue);

        $this->validate();

        abort_if($toIssueId === $this->issue->getKey(), 422, 'Cannot link an issue to itself.');

        $type = IssueLinkType::query()->findOrFail($this->typeId);

        // Guard against duplicates (both directions) – DB unique also protects this
        $exists = IssueLink::query()
            ->where('issue_link_type_id', $type->id)
            ->where(function ($q) use ($toIssueId) {
                $q->where(fn ($qq) => $qq->where('from_issue_id', $this->issue->getKey())->where('to_issue_id', $toIssueId))
                    ->orWhere(fn ($qq) => $qq->where('from_issue_id', $toIssueId)->where('to_issue_id', $this->issue->getKey()));
            })
            ->exists();

        if ($exists) {
            $this->dispatch('notify', title: 'Already linked', body: 'This link already exists.');
            return;
        }

        $link = IssueLink::query()->create([
            'issue_link_type_id' => $type->id,
            'from_issue_id' => $this->issue->getKey(),
            'to_issue_id' => $toIssueId,
            'created_by_id' => auth()->id(),
        ]);

        // Activity on both issues
        activity('forge.issue')
            ->performedOn($this->issue)
            ->causedBy(auth()->user())
            ->withProperties([
                'link_type_key' => $type->key,
                'link_out_label' => $type->name,
                'to_issue_id' => $toIssueId,
            ])
            ->log('issue.link_added');

        activity('forge.issue')
            ->performedOn(Issue::query()->find($toIssueId))
            ->causedBy(auth()->user())
            ->withProperties([
                'link_type_key' => $type->key,
                'link_in_label' => $type->inwardLabel(),
                'from_issue_id' => $this->issue->getKey(),
            ])
            ->log('issue.link_added');

        $this->reset('q', 'results');
        $this->dispatch('issue-links-updated');
        $this->dispatch('notify', title: 'Linked', body: 'Issue link added.');
    }

    public function unlink(string $linkId): void
    {
        $link = IssueLink::query()
            ->with(['type', 'from', 'to'])
            ->findOrFail($linkId);

        // You can unlink if you can update either endpoint; keep it strict to the current (from or to includes this issue)
        abort_unless($link->from_issue_id === $this->issue->getKey() || $link->to_issue_id === $this->issue->getKey(), 403);
        $this->authorize('link', $this->issue);

        $type = $link->type;
        $other = $link->from_issue_id === $this->issue->getKey() ? $link->to : $link->from;

        $link->delete();

        activity('forge.issue')
            ->performedOn($this->issue)
            ->causedBy(auth()->user())
            ->withProperties([
                'link_type_key' => $type->key,
                'other_issue_id' => $other?->getKey(),
            ])
            ->log('issue.link_removed');

        $this->dispatch('issue-links-updated');
        $this->dispatch('notify', title: 'Unlinked', body: 'Issue link removed.');
    }

    public function render(): View
    {
        $issue = $this->issue->loadMissing(
            ['outgoingLinks', 'incomingLinks', 'outgoingLinks.type', 'incomingLinks.type', 'outgoingLinks.to.project:id,key', 'incomingLinks.from.project:id,key']
        );

        // Shape for view
        $outgoing = $issue->outgoingLinks->map(fn ($l) => [
            'id' => $l->id,
            'label' => $l->type->outwardLabel(), // e.g. "blocks"
            'issue_key' => $l->to->key,
            'summary' => $l->to->summary,
            'project_key' => $l->to->project?->key,
            'url' => route('issues.show', ['project' => $l->to->project, 'issue' => $l->to]),
        ]);

        $incoming = $issue->incomingLinks->map(fn ($l) => [
            'id' => $l->id,
            'label' => $l->type->inwardLabel(), // e.g. "is blocked by"
            'issue_key' => $l->from->key,
            'summary' => $l->from->summary,
            'project_key' => $l->from->project?->key,
            'url' => route('issues.show', ['project' => $l->from->project, 'issue' => $l->from]),
        ]);

        return view('livewire.issues.manage-links', [
            'outgoing' => $outgoing,
            'incoming' => $incoming,
        ]);
    }
}
