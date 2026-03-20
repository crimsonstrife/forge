<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Issue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class MentionNotification extends Notification
{
    public Issue $issue;

    public string $url;

    public function __construct(public Comment $comment)
    {
        $this->comment->loadMissing([
            'user:id,name',
            'commentable.project:id,key',
        ]);

        $commentable = $this->comment->commentable;
        if (! $commentable instanceof Issue) {
            throw new \RuntimeException('Mention notifications require issue comments.');
        }

        $this->issue = $commentable;
        $this->url = route('issues.show', ['project' => $this->issue->project, 'issue' => $this->issue]).'#comment-'.$this->comment->getKey();
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $actor = $this->comment->user;
        $snippet = Str::of(strip_tags((string) $this->comment->body))->squish()->limit(160)->toString();

        return [
            'event' => 'mention',
            'issue_id' => (string) $this->issue->getKey(),
            'issue_key' => (string) $this->issue->key,
            'project_id' => $this->issue->project_id,
            'comment_id' => (string) $this->comment->getKey(),
            'title' => __('You were mentioned on :issue', ['issue' => $this->issue->key]),
            'summary' => __(':actor mentioned you: :snippet', [
                'actor' => $actor?->name ?? __('Someone'),
                'snippet' => $snippet,
            ]),
            'url' => $this->url,
        ];
    }
}
