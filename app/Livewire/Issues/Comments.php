<?php

namespace App\Livewire\Issues;

use App\Models\Comment;
use App\Models\Issue;
use App\Models\User;
use App\Services\Issues\IssueCommentService;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Threaded comments for an Issue.
 *
 * @property-read Collection<int, Comment> $tree
 */
final class Comments extends Component
{
    use AuthorizesRequests;

    public Issue $issue;

    #[Validate(['body' => 'required|string|min:2|max:20000'])]
    public string $body = '';

    /** @var array<string,string> Per-comment reply bodies keyed by parent comment id */
    public array $replyBodies = [];

    /** The comment id currently showing the inline reply box */
    public ?string $replyFor = null;

    public int $composerNonce = 0;

    /** @var array<string,int> */
    public array $replyEditorNonces = [];

    public function mount(Issue $issue): void
    {
        $this->authorize('view', $issue);
        $this->issue = $issue;
    }

    /**
     * Post a new top-level comment.
     */
    public function add(): void
    {
        $this->authorize('update', $this->issue);
        $this->validate();

        /** @var User $user */
        $user = auth()->user();

        try {
            app(IssueCommentService::class)->create($this->issue, $user, $this->body);
        } catch (ValidationException $e) {
            $this->setErrorBag($e->validator->errors());
            return;
        }

        $this->reset('body');
        $this->composerNonce++;
        $this->dispatch('issue-followers-updated', id: $this->issue->getKey());
        $this->dispatch('notify', title: 'Comment added');
    }

    /**
     * Show the inline reply box for a given comment.
     */
    public function startReply(string $commentId): void
    {
        $this->replyFor = $commentId;
        $this->replyBodies[$commentId] = $this->replyBodies[$commentId] ?? '';
        $this->replyEditorNonces[$commentId] = $this->replyEditorNonces[$commentId] ?? 0;
    }

    /**
     * Cancel replying.
     */
    public function cancelReply(): void
    {
        $this->replyFor = null;
    }

    /**
     * Post a reply under the given parent comment.
     */
    public function postReply(string $parentId): void
    {
        $this->authorize('update', $this->issue);

        $this->validate([
            "replyBodies.$parentId" => 'required|string|min:2|max:20000',
        ]);

        /** @var User $user */
        $user = auth()->user();

        try {
            app(IssueCommentService::class)->create(
                issue: $this->issue,
                author: $user,
                body: $this->replyBodies[$parentId],
                parentId: $parentId,
            );
        } catch (ValidationException $e) {
            $this->addError("replyBodies.$parentId", __('Comment cannot be empty.'));
            return;
        }

        // Clear only this reply box
        $this->replyBodies[$parentId] = '';
        $this->replyEditorNonces[$parentId] = ($this->replyEditorNonces[$parentId] ?? 0) + 1;
        $this->replyFor = null;

        $this->dispatch('issue-followers-updated', id: $this->issue->getKey());
        $this->dispatch('notify', title: 'Reply posted');
    }

    public function render(): View
    {
        // Load all comments once, build an in-memory tree to avoid N+1.
        /** @var Collection<int,Comment> $all */
        $all = $this->issue
            ->comments()
            ->with('user:id,name,profile_photo_path')
            ->orderBy('created_at') // chronological within each thread
            ->get();

        $byParent = $all->groupBy('parent_id');

        $tree = $this->buildTree($byParent, null);

        return view('livewire.issues.comments', [
            'tree'  => $tree,
            'count' => $all->count(),
        ]);
    }
    /**
     * Recursively build a tree of comments grouped by parent_id.
     *
     * @param Collection $byParent
     * @param string|null $parentId
     * @return Collection<int, Comment>
     */
    private function buildTree(Collection $byParent, ?string $parentId): Collection
    {
        /** @var Collection<int,Comment> $level */
        $level = $byParent->get($parentId, collect());

        return $level->map(function (Comment $c) use ($byParent) {
            // Attach a non-persistent relation for eager-rendering
            $c->setRelation('children_eager', $this->buildTree($byParent, $c->getKey()));
            return $c;
        });
    }
}
