<?php

namespace App\Integrations\Sentry\Services;

use App\Models\Issue;
use App\Models\IssueExternalRef;
use App\Models\IssueStatus;
use App\Models\Project;
use App\Models\User;
use App\Settings\SentrySettings;
use Illuminate\Support\Facades\Log;

final class SentryIssueSyncService
{
    public function __construct(private readonly SentrySettings $settings) {}

    /**
     * Inbound from an alert-rule-action firing (resource = 'event_alert').
     *
     * @param  array<string,mixed>  $payload
     */
    public function upsertFromAlert(array $payload): void
    {
        $data = (array) ($payload['data'] ?? []);
        $issueData = (array) ($data['event']['issue_id'] ?? null) ?: (array) ($data['issue'] ?? []);
        $sentryId = $this->extractSentryIssueId($payload);
        $title = (string) ($data['issue']['title'] ?? $data['event']['title'] ?? '(no title)');
        $culprit = (string) ($data['issue']['culprit'] ?? $data['event']['culprit'] ?? '');
        $level = (string) ($data['issue']['level'] ?? $data['event']['level'] ?? '');
        $webUrl = (string) ($data['issue']['web_url'] ?? $data['issue']['permalink'] ?? $data['event']['web_url'] ?? '');
        $project = $this->resolveProject($data);

        if ($sentryId === '' || ! $project instanceof Project) {
            Log::warning('Sentry alert webhook missing issue id or project — skipping', [
                'sentry_id' => $sentryId,
                'project_routed' => $project?->id,
            ]);

            return;
        }

        $ref = IssueExternalRef::query()
            ->where('provider', 'sentry')
            ->where('external_issue_id', $sentryId)
            ->first();

        if ($ref instanceof IssueExternalRef) {
            $ref->update([
                'state' => 'open',
                'payload' => $payload,
                'url' => $webUrl !== '' ? $webUrl : $ref->url,
            ]);

            return;
        }

        $issue = new Issue;
        $issue->project_id = (string) $project->id;
        $issue->issue_type_id = $this->settings->default_issue_type_id;
        $issue->issue_priority_id = $this->settings->default_priority_id;
        $issue->issue_status_id = $this->firstOpenStatusId($project);
        $issue->reporter_id = $this->systemUserId();
        $issue->summary = mb_strimwidth($title, 0, 255, '…');
        $issue->description = $this->buildDescription($title, $culprit, $level, $webUrl);
        $issue->save();

        IssueExternalRef::query()->create([
            'issue_id' => $issue->id,
            'provider' => 'sentry',
            'external_issue_id' => $sentryId,
            'state' => 'open',
            'url' => $webUrl !== '' ? $webUrl : null,
            'payload' => $payload,
        ]);
    }

    /**
     * Inbound from the 'issue' resource (status changed on the Sentry side).
     *
     * @param  array<string,mixed>  $payload
     */
    public function syncStatus(array $payload): void
    {
        $action = (string) ($payload['action'] ?? '');
        $sentryId = $this->extractSentryIssueId($payload);

        if ($sentryId === '') {
            return;
        }

        $ref = IssueExternalRef::query()
            ->where('provider', 'sentry')
            ->where('external_issue_id', $sentryId)
            ->first();

        if (! $ref instanceof IssueExternalRef) {
            return;
        }

        /** @var Issue|null $issue */
        $issue = Issue::query()->find($ref->issue_id);
        if (! $issue instanceof Issue) {
            return;
        }

        $project = $issue->project;

        if (in_array($action, ['resolved', 'archived', 'ignored'], true)) {
            $issue->issue_status_id = $this->firstDoneStatusId($project) ?? $issue->issue_status_id;
            if (empty($issue->closed_at)) {
                $issue->closed_at = now();
            }
            $ref->state = 'closed';
        } elseif ($action === 'unresolved') {
            $issue->issue_status_id = $this->firstOpenStatusId($project) ?? $issue->issue_status_id;
            $issue->closed_at = null;
            $ref->state = 'open';
        }

        $issue->save();
        $ref->payload = $payload;
        $ref->save();
    }

    /**
     * @param  array<string,mixed>  $payload
     */
    private function extractSentryIssueId(array $payload): string
    {
        $data = (array) ($payload['data'] ?? []);

        return (string) (
            $data['issue']['id']
            ?? $data['event']['issue_id']
            ?? $data['issue_id']
            ?? $payload['issue']['id']
            ?? ''
        );
    }

    /**
     * @param  array<string,mixed>  $data
     */
    private function resolveProject(array $data): ?Project
    {
        $forgeProjectId = (string) (
            $data['issue_alert']['settings']['forge_project_id']
            ?? $data['settings']['forge_project_id']
            ?? ''
        );

        if ($forgeProjectId !== '') {
            $project = Project::query()->find($forgeProjectId);
            if ($project instanceof Project) {
                return $project;
            }
        }

        if ($this->settings->default_project_id) {
            return Project::query()->find($this->settings->default_project_id);
        }

        return null;
    }

    private function firstOpenStatusId(?Project $project): ?int
    {
        return $this->firstStatusIdMatching($project, isDone: false);
    }

    private function firstDoneStatusId(?Project $project): ?int
    {
        return $this->firstStatusIdMatching($project, isDone: true);
    }

    private function firstStatusIdMatching(?Project $project, bool $isDone): ?int
    {
        if ($project instanceof Project) {
            $scoped = $project->issueStatuses()
                ->where('issue_statuses.is_done', $isDone)
                ->orderBy('issue_statuses.order')
                ->value('issue_statuses.id');
            if ($scoped !== null) {
                return (int) $scoped;
            }
        }

        $value = IssueStatus::query()
            ->where('is_done', $isDone)
            ->orderBy('order')
            ->value('id');

        return $value !== null ? (int) $value : null;
    }

    private function systemUserId(): ?string
    {
        $email = (string) config('sentry-integration.system_user_email');
        if ($email === '') {
            return null;
        }

        return User::query()->where('email', $email)->value('id');
    }

    private function buildDescription(string $title, string $culprit, string $level, string $webUrl): string
    {
        $parts = ['**Sentry alert**', '', $title];

        if ($culprit !== '') {
            $parts[] = '';
            $parts[] = 'Culprit: '.$culprit;
        }
        if ($level !== '') {
            $parts[] = 'Level: '.$level;
        }
        if ($webUrl !== '') {
            $parts[] = '';
            $parts[] = 'Open in Sentry: '.$webUrl;
        }

        return implode("\n", $parts);
    }
}
