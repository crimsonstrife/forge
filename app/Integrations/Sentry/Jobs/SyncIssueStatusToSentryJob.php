<?php

namespace App\Integrations\Sentry\Jobs;

use App\Integrations\Sentry\Services\SentryClient;
use App\Models\Issue;
use App\Models\IssueExternalRef;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class SyncIssueStatusToSentryJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly string $issueId,
        public readonly bool $resolve,
        public readonly ?string $actorId = null,
    ) {
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

        /** @var Issue|null $issue */
        $issue = Issue::query()->find($this->issueId);
        if (! $issue instanceof Issue) {
            return;
        }

        $ref = IssueExternalRef::query()
            ->where('provider', 'sentry')
            ->where('issue_id', $this->issueId)
            ->first();

        if (! $ref instanceof IssueExternalRef) {
            return;
        }

        $sentryId = (string) $ref->external_issue_id;
        if ($sentryId === '') {
            return;
        }

        if ($this->resolve) {
            $client->resolveIssue($sentryId);
            $client->postComment(
                $sentryId,
                $this->provenanceLine('Resolved', $issue),
            );

            $ref->state = 'closed';
            $ref->save();

            return;
        }

        $client->unresolveIssue($sentryId);
        $client->postComment(
            $sentryId,
            $this->provenanceLine('Reopened', $issue),
        );

        $ref->state = 'open';
        $ref->save();
    }

    private function provenanceLine(string $verb, Issue $issue): string
    {
        $actor = $this->actorId ? User::query()->find($this->actorId) : null;
        $name = $actor instanceof User ? $actor->name : 'a Forge user';
        $key = (string) ($issue->key ?? $issue->id);

        return sprintf('%s by %s via Forge — issue %s.', $verb, $name, $key);
    }
}
