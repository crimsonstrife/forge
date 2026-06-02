<?php

namespace App\Integrations\Sentry\Services;

use App\Settings\SentrySettings;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class SentryClient
{
    public function __construct(private readonly SentrySettings $settings)
    {
    }

    public function isConfigured(): bool
    {
        return $this->settings->enabled && filled($this->settings->auth_token);
    }

    /**
     * @throws ConnectionException
     */
    public function resolveIssue(string $sentryIssueId): void
    {
        $this->putIssueStatus($sentryIssueId, 'resolved');
    }

    /**
     * @throws ConnectionException
     */
    public function unresolveIssue(string $sentryIssueId): void
    {
        $this->putIssueStatus($sentryIssueId, 'unresolved');
    }

    /**
     * @throws ConnectionException
     */
    public function postComment(string $sentryIssueId, string $body): void
    {
        $this->ensureConfigured();

        $resp = $this->client()->post(
            "/issues/{$sentryIssueId}/comments/",
            ['text' => $body],
        );

        $this->assertOk($resp, 'post comment');
    }

    /**
     * @return array<string,mixed>
     *
     * @throws ConnectionException
     */
    public function createIssueAlertRule(
        string $sentryProjectSlug,
        string $ruleName,
        string $forgeProjectId,
        int $issueTypeId,
        int $priorityId,
        int $frequency = 5,
    ): array {
        $this->ensureAlertRuleConfigured();

        $resp = $this->client()->post(
            sprintf(
                '/projects/%s/%s/rules/',
                rawurlencode((string) $this->settings->org_slug),
                rawurlencode($sentryProjectSlug),
            ),
            [
                'name' => $ruleName,
                'frequency' => max(5, min(43200, $frequency)),
                'actionMatch' => 'all',
                'filterMatch' => 'all',
                'conditions' => [
                    ['id' => 'sentry.rules.conditions.first_seen_event.FirstSeenEventCondition'],
                ],
                'filters' => [],
                'actions' => [
                    [
                        'id' => 'sentry.rules.actions.notify_event_sentry_app.NotifyEventSentryAppAction',
                        'settings' => [
                            ['name' => 'forge_project_id', 'value' => $forgeProjectId],
                            ['name' => 'forge_issue_type_id', 'value' => (string) $issueTypeId],
                            ['name' => 'forge_priority_id', 'value' => (string) $priorityId],
                        ],
                        'sentryAppInstallationUuid' => (string) $this->settings->installation_uuid,
                    ],
                ],
            ],
        );

        $this->assertOk($resp, 'create issue alert rule');

        $json = $resp->json();

        return is_array($json) ? $json : [];
    }

    /**
     * @throws ConnectionException
     */
    public function ping(): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        return $this->client()->get('/')->successful();
    }

    /**
     * @throws ConnectionException
     */
    private function putIssueStatus(string $sentryIssueId, string $status): void
    {
        $this->ensureConfigured();

        $resp = $this->client()->put(
            "/issues/{$sentryIssueId}/",
            ['status' => $status],
        );

        $this->assertOk($resp, "set status={$status}");
    }

    private function client(): PendingRequest
    {
        return Http::withToken($this->settings->auth_token ?? '')
            ->baseUrl($this->settings->api_base ?: (string) config('sentry-integration.api_base'))
            ->asJson()
            ->acceptJson()
            ->timeout(10);
    }

    private function ensureConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Sentry integration is not enabled or missing an auth token.');
        }
    }

    private function ensureAlertRuleConfigured(): void
    {
        $this->ensureConfigured();

        if (! filled($this->settings->org_slug)) {
            throw new RuntimeException('Sentry integration is missing an org slug.');
        }

        if (! filled($this->settings->installation_uuid)) {
            throw new RuntimeException('Sentry installation UUID has not been recorded yet.');
        }
    }

    private function assertOk(Response $resp, string $what): void
    {
        if (! $resp->successful()) {
            throw new RuntimeException(sprintf(
                'Sentry %s failed: HTTP %d %s',
                $what,
                $resp->status(),
                $this->maskToken(mb_strimwidth((string) $resp->body(), 0, 500)),
            ));
        }
    }

    private function maskToken(string $msg): string
    {
        $token = (string) ($this->settings->auth_token ?? '');
        if ($token === '') {
            return $msg;
        }

        return str_replace($token, '***', $msg);
    }
}
