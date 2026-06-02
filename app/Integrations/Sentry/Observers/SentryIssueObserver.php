<?php

namespace App\Integrations\Sentry\Observers;

use App\Integrations\Sentry\Jobs\SyncIssueStatusToSentryJob;
use App\Integrations\Sentry\Support\SentrySyncContext;
use App\Models\Issue;
use App\Models\IssueExternalRef;
use App\Models\IssueStatus;
use App\Settings\SentrySettings;
use Illuminate\Support\Facades\Auth;

final class SentryIssueObserver
{
    public function updated(Issue $issue): void
    {
        if (SentrySyncContext::isActive()) {
            return;
        }

        if (! $issue->wasChanged('issue_status_id')) {
            return;
        }

        if (! app(SentrySettings::class)->enabled) {
            return;
        }

        $hasLink = IssueExternalRef::query()
            ->where('provider', 'sentry')
            ->where('issue_id', $issue->id)
            ->exists();

        if (! $hasLink) {
            return;
        }

        $fromDone = $this->isDone((int) ($issue->getOriginal('issue_status_id') ?? 0));
        $toDone = $this->isDone((int) $issue->issue_status_id);

        if ($fromDone === $toDone) {
            return;
        }

        SyncIssueStatusToSentryJob::dispatch(
            issueId: (string) $issue->id,
            resolve: $toDone,
            actorId: Auth::id() ? (string) Auth::id() : null,
        )->afterCommit();
    }

    private function isDone(int $statusId): bool
    {
        if ($statusId === 0) {
            return false;
        }
        $status = IssueStatus::query()->find($statusId);

        return $status instanceof IssueStatus && (bool) $status->is_done;
    }
}
