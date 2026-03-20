<?php

namespace App\Services\Issues;

use App\Models\Comment;
use App\Models\Issue;
use App\Models\IssueFollower;
use App\Models\IssueNotificationPreference;
use App\Models\IssueStatus;
use App\Models\User;
use App\Notifications\IssueActivityNotification;
use App\Notifications\MentionNotification;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class IssueCollaborationService
{
    public function follow(Issue $issue, User|string $user): IssueFollower
    {
        $userId = $user instanceof User ? (string) $user->getKey() : (string) $user;

        return IssueFollower::query()->firstOrCreate([
            'issue_id' => (string) $issue->getKey(),
            'user_id' => $userId,
        ]);
    }

    public function unfollow(Issue $issue, User|string $user): void
    {
        $userId = $user instanceof User ? (string) $user->getKey() : (string) $user;

        IssueFollower::query()
            ->where('issue_id', (string) $issue->getKey())
            ->where('user_id', $userId)
            ->delete();
    }

    public function ensureCoreFollowers(Issue $issue): void
    {
        foreach (array_filter([(string) $issue->reporter_id, (string) $issue->assignee_id]) as $userId) {
            $this->follow($issue, $userId);
        }
    }

    public function preferences(User $user): IssueNotificationPreference
    {
        return $user->issueNotificationPreference()->firstOrCreate([]);
    }

    /**
     * @return EloquentCollection<int, User>
     */
    public function followersForIssue(Issue $issue): EloquentCollection
    {
        $this->ensureCoreFollowers($issue);

        return $issue->followerUsers()
            ->select(['users.id', 'users.name', 'users.email', 'users.profile_photo_path'])
            ->orderBy('users.name')
            ->get();
    }

    /**
     * @param iterable<int, User> $mentionedUsers
     */
    public function notifyComment(Comment $comment, User $actor, iterable $mentionedUsers = []): void
    {
        $issue = $this->issueForComment($comment);
        if (! $issue) {
            return;
        }

        $this->follow($issue, $actor);

        $mentionedIds = collect($mentionedUsers)
            ->map(fn (User $user) => (string) $user->getKey())
            ->unique()
            ->values()
            ->all();

        $summary = __(':actor commented: :snippet', [
            'actor' => $actor->name,
            'snippet' => $this->commentSnippet($comment),
        ]);

        foreach ($this->followersForIssue($issue) as $recipient) {
            if ((string) $recipient->getKey() === (string) $actor->getKey()) {
                continue;
            }

            if (in_array((string) $recipient->getKey(), $mentionedIds, true)) {
                continue;
            }

            if (! $this->preferences($recipient)->enabledFor('comment')) {
                continue;
            }

            $recipient->notify(new IssueActivityNotification(
                issue: $issue,
                event: 'comment',
                title: __('New comment on :issue', ['issue' => $issue->key]),
                summary: $summary,
                url: $this->commentUrl($issue, $comment),
            ));
        }
    }

    /**
     * @param iterable<int, User> $mentionedUsers
     */
    public function notifyMentions(Comment $comment, User $actor, iterable $mentionedUsers): void
    {
        $issue = $this->issueForComment($comment);
        if (! $issue) {
            return;
        }

        foreach (collect($mentionedUsers)->unique(fn (User $user) => (string) $user->getKey()) as $recipient) {
            if ((string) $recipient->getKey() === (string) $actor->getKey()) {
                continue;
            }

            $this->follow($issue, $recipient);
            $comment->mention($recipient, false);

            if (! $this->preferences($recipient)->enabledFor('mention')) {
                continue;
            }

            $recipient->notify(new MentionNotification($comment));
        }
    }

    public function notifyStatusChanged(Issue $issue, ?IssueStatus $fromStatus, ?IssueStatus $toStatus, ?User $actor = null): void
    {
        $summary = $actor
            ? __(':actor moved :issue from :from to :to', [
                'actor' => $actor->name,
                'issue' => $issue->key,
                'from' => $fromStatus?->name ?? __('Unspecified'),
                'to' => $toStatus?->name ?? __('Unspecified'),
            ])
            : __(':issue moved from :from to :to', [
                'issue' => $issue->key,
                'from' => $fromStatus?->name ?? __('Unspecified'),
                'to' => $toStatus?->name ?? __('Unspecified'),
            ]);

        $this->notifyFollowers(
            issue: $issue,
            event: 'status_change',
            title: __('Status changed on :issue', ['issue' => $issue->key]),
            summary: $summary,
            actor: $actor,
        );
    }

    public function notifyLinkChanged(Issue $issue, string $summary, ?User $actor = null): void
    {
        $this->notifyFollowers(
            issue: $issue,
            event: 'link_change',
            title: __('Links updated on :issue', ['issue' => $issue->key]),
            summary: $summary,
            actor: $actor,
        );
    }

    private function notifyFollowers(Issue $issue, string $event, string $title, string $summary, ?User $actor = null): void
    {
        foreach ($this->followersForIssue($issue) as $recipient) {
            if ($actor && (string) $recipient->getKey() === (string) $actor->getKey()) {
                continue;
            }

            if (! $this->preferences($recipient)->enabledFor($event)) {
                continue;
            }

            $recipient->notify(new IssueActivityNotification(
                issue: $issue,
                event: $event,
                title: $title,
                summary: $summary,
                url: $this->issueUrl($issue),
            ));
        }
    }

    private function issueForComment(Comment $comment): ?Issue
    {
        $comment->loadMissing('commentable.project:id,key');

        return $comment->commentable instanceof Issue
            ? $comment->commentable
            : null;
    }

    private function issueUrl(Issue $issue): string
    {
        $issue->loadMissing('project:id,key');

        return route('issues.show', ['project' => $issue->project, 'issue' => $issue]);
    }

    private function commentUrl(Issue $issue, Comment $comment): string
    {
        return $this->issueUrl($issue).'#comment-'.$comment->getKey();
    }

    private function commentSnippet(Comment $comment): string
    {
        return (string) str(strip_tags((string) $comment->body))->squish()->limit(160);
    }
}
