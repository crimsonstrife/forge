<?php

namespace App\Livewire\Feedback;

use App\Models\FeedbackBoard;
use App\Models\FeedbackComment;
use App\Models\FeedbackPost;
use App\Services\Feedback\MarkdownRenderer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdminDiscussion extends Component
{
    public FeedbackBoard $board;

    public ?string $selectedPostId = null;

    public string $replyBody = '';

    public function mount(FeedbackBoard $board): void
    {
        $this->board = $board;
        $this->selectedPostId = $board->posts()->latest('last_activity_at')->value('id');
    }

    public function selectPost(string $postId): void
    {
        $this->selectedPostId = $postId;
    }

    public function submitReply(MarkdownRenderer $markdown): void
    {
        $this->validate([
            'replyBody' => ['required', 'string', 'max:5000'],
        ]);

        $post = $this->selectedPost();
        if ($post === null) {
            return;
        }

        DB::transaction(function () use ($post, $markdown): void {
            FeedbackComment::query()->create([
                'post_id' => $post->getKey(),
                'staff_user_id' => auth()->id(),
                'body' => $this->replyBody,
                'body_html' => $markdown->render($this->replyBody),
                'is_staff_reply' => true,
            ]);

            $post->forceFill([
                'comment_count' => $post->comments()->count(),
                'last_activity_at' => now(),
            ])->save();
        });

        $this->replyBody = '';
    }

    /** @return Collection<int,FeedbackPost> */
    public function getPostsProperty(): Collection
    {
        return $this->board->posts()
            ->with(['status', 'category', 'identity'])
            ->latest('last_activity_at')
            ->limit(50)
            ->get();
    }

    public function selectedPost(): ?FeedbackPost
    {
        if ($this->selectedPostId === null) {
            return null;
        }

        return FeedbackPost::query()
            ->with(['status', 'category', 'identity', 'comments.identity', 'comments.staffUser'])
            ->where('board_id', $this->board->getKey())
            ->find($this->selectedPostId);
    }

    public function render()
    {
        return view('livewire.feedback.admin-discussion', [
            'posts' => $this->posts,
            'selectedPost' => $this->selectedPost(),
        ])->layout('components.layouts.app');
    }
}
