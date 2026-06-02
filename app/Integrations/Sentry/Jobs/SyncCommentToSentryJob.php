<?php

namespace App\Integrations\Sentry\Jobs;

use App\Integrations\Sentry\Services\SentryClient;
use App\Models\Comment;
use App\Models\IssueExternalRef;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class SyncCommentToSentryJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly string $commentId)
    {
        $queue = config('sentry-integration.queue');
        if (is_string($queue) && $queue !== '') {
            $this->onQueue($queue);
        }
    }

    public function handle(SentryClient $client): void
    {
        if (! $client->isConfigured()) {
            return;
        }

        /** @var Comment|null $comment */
        $comment = Comment::query()->find($this->commentId);
        if (! $comment instanceof Comment) {
            return;
        }

        $issueId = (string) ($comment->commentable_id ?? $comment->issue_id ?? '');
        if ($issueId === '') {
            return;
        }

        $ref = IssueExternalRef::query()
            ->where('provider', 'sentry')
            ->where('issue_id', $issueId)
            ->first();

        if (! $ref instanceof IssueExternalRef) {
            return;
        }

        $sentryId = (string) $ref->external_issue_id;
        if ($sentryId === '') {
            return;
        }

        $author = $comment->user_id ? User::query()->find($comment->user_id) : null;
        $name = $author instanceof User ? $author->name : 'a Forge user';

        $client->postComment(
            $sentryId,
            sprintf('%s via Forge: %s', $name, strip_tags((string) $comment->body)),
        );
    }
}
