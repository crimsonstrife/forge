<?php

namespace App\Providers;

use App\Models\Repository;
use App\Contracts\RepositoryProviderInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use JsonException;

final class GitHubRepositoryProvider implements RepositoryProviderInterface
{
    /**
     * @throws ConnectionException
     */
    public function fetchAllIssues(Repository $repository, string $token): array
    {
        $issues = [];
        $page = 1;

        do {
            $resp = Http::withToken($token)
                ->acceptJson()
                ->get("https://api.github.com/repos/{$repository->owner}/{$repository->name}/issues", [
                    'state' => 'all',
                    'per_page' => 100,
                    'page' => $page,
                ]);

            $batch = collect($resp->json() ?? [])
                ->filter(fn ($i) => empty($i['pull_request'])) // exclude PRs
                ->map(function ($i) {
                    return [
                        'external_issue_id' => (string)$i['id'],
                        'number' => (int)$i['number'],
                        'title' => $i['title'],
                        'body' => $i['body'],
                        'state' => $i['state'], // open|closed
                        'url' => $i['html_url'],
                        'assignee_login' => Arr::get($i, 'assignee.login'),
                        'reporter_login' => Arr::get($i, 'user.login'),
                        'labels' => collect($i['labels'] ?? [])->pluck('name')->all(),
                        'created_at' => $i['created_at'],
                        'updated_at' => $i['updated_at'],
                        'closed_at'  => $i['closed_at'] ?? null,
                        'raw' => $i,
                    ];
                })->values()->all();

            $issues = array_merge($issues, $batch);
            $page++;
        } while (!empty($batch));

        return $issues;
    }

    /**
     * @throws JsonException
     */
    public function normalizeWebhook(array $headers, string $rawPayload): ?array
    {
        $event = $headers['X-GitHub-Event'] ?? null;
        if ($event !== 'issues') {
            return null;
        }

        $payload = json_decode($rawPayload, true, 512, JSON_THROW_ON_ERROR);
        if (!$payload) {
            return null;
        }

        $action = $payload['action'] ?? null;
        $issue  = $payload['issue']  ?? null;
        $repo   = $payload['repository'] ?? null;
        if (!$issue || !$repo) {
            return null;
        }

        if (!empty($issue['pull_request'])) {
            return null;
        } // ignore PRs

        return [
            'provider' => 'github',
            'host' => 'github.com',
            'owner' => $repo['owner']['login'] ?? '',
            'name'  => $repo['name'] ?? '',
            'external_issue_id' => (string)$issue['id'],
            'number' => (int)$issue['number'],
            'state'  => $issue['state'],
            'title'  => $issue['title'],
            'body'   => $issue['body'],
            'labels' => collect($issue['labels'] ?? [])->pluck('name')->all(),
            'assignee_login' => Arr::get($issue, 'assignee.login'),
            'reporter_login' => Arr::get($issue, 'user.login'),
            'raw' => $payload,
            'action' => $action, // opened|closed|edited|reopened|assigned|unassigned|labeled|unlabeled
        ];
    }

    /**
     * @throws JsonException
     */
    public function resolveRepositoryFromWebhook(array $headers, string $rawPayload): ?Repository
    {
        // Optional: validate signature if using GitHub App / webhook secret
        $payload = json_decode($rawPayload, true, 512, JSON_THROW_ON_ERROR);
        $repo = $payload['repository'] ?? null;
        if (!$repo) {
            return null;
        }

        return Repository::query()
            ->where('provider', 'github')
            ->where('host', 'github.com')
            ->where('owner', $repo['owner']['login'] ?? '')
            ->where('name', $repo['name'] ?? '')
            ->first();
    }
}
