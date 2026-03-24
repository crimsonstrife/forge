<?php

namespace App\Livewire\Issues;

use App\Models\Issue;
use App\Models\User;
use App\Services\Issues\IssueCollaborationService;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Component;

final class FollowersPanel extends Component
{
    use AuthorizesRequests;

    public Issue $issue;

    public function mount(Issue $issue): void
    {
        $this->authorize('view', $issue);
        $this->issue = $issue;
    }

    public function toggle(IssueCollaborationService $collaboration): void
    {
        /** @var User|null $user */
        $user = auth()->user();
        if (! $user) {
            return;
        }

        $this->authorize('view', $this->issue);

        $following = $this->issue->followerUsers()
            ->whereKey($user->getKey())
            ->exists();

        if ($following) {
            $collaboration->unfollow($this->issue, $user);
            $this->dispatch('notify', title: 'Stopped following');
        } else {
            $collaboration->follow($this->issue, $user);
            $this->dispatch('notify', title: 'Following issue');
        }

        $this->dispatch('issue-followers-updated', id: $this->issue->getKey());
    }

    #[On('issue-followers-updated')]
    public function refreshIfMatch(string $id): void
    {
        if ($id !== (string) $this->issue->getKey()) {
            return;
        }

        $this->issue->refresh();
    }

    public function render(IssueCollaborationService $collaboration): View
    {
        $followers = $collaboration->followersForIssue($this->issue);
        $isFollowing = auth()->check()
            ? $followers->contains(fn (User $user) => (string) $user->getKey() === (string) auth()->id())
            : false;

        return view('livewire.issues.followers-panel', [
            'followers' => $followers,
            'isFollowing' => $isFollowing,
        ]);
    }
}
