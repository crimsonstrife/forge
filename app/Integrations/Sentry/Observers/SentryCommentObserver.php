<?php

namespace App\Integrations\Sentry\Observers;

use App\Integrations\Sentry\Jobs\SyncCommentToSentryJob;
use App\Integrations\Sentry\Support\SentrySyncContext;
use App\Models\Comment;
use App\Models\Issue;
use App\Models\IssueExternalRef;
use App\Settings\SentrySettings;

final class SentryCommentObserver
{
    public function created(Comment $comment): void
    {
        if (SentrySyncContext::isActive()) {
            return;
        }

        if (! app(SentrySettings::class)->enabled) {
            return;
        }

        if ($comment->commentable_type !== Issue::class) {
            return;
        }

        $issueId = (string) ($comment->commentable_id ?? $comment->issue_id ?? '');
        if ($issueId === '') {
            return;
        }

        $hasLink = IssueExternalRef::query()
            ->where('provider', 'sentry')
            ->where('issue_id', $issueId)
            ->exists();

        if (! $hasLink) {
            return;
        }

        SyncCommentToSentryJob::dispatch((string) $comment->id)->afterCommit();
    }
}
