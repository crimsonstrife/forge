<?php

namespace App\Livewire\Issues;

use App\Models\Comment;
use App\Models\Issue;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class Comments extends Component
{
    use AuthorizesRequests;

    public Issue $issue;

    #[Validate(['body' => 'required|string|min:2|max:2000'])]
    public string $body = '';

    public function mount(Issue $issue): void
    {
        $this->authorize('view', $issue);
        $this->issue = $issue;
    }

    public function add(): void
    {
        $this->authorize('update', $this->issue);
        $this->validate();

        /** @var User $user */
        $user = auth()->user();

        $this->issue->comments()->create([
            'user_id' => $user->getKey(),
            'body' => $this->body,
        ]);

        $this->reset('body');
        $this->dispatch('notify', title: 'Comment added');
    }

    public function render(): View
    {
        $comments = $this->issue
            ->comments()
            ->with('user:id,name,profile_photo_path')
            ->latest()
            ->get();

        return view('livewire.issues.comments', compact('comments'));
    }
}
