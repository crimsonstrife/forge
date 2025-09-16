<?php

namespace App\Providers;

use App\Models\Repository;
use App\Contracts\RepositoryProviderInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use JsonException;
use RuntimeException;

final class GitHubRepositoryProvider implements RepositoryProviderInterface
{
    /**
     * @throws ConnectionException|RuntimeException
     */
    public function fetchAllIssues(Repository $repository, string $token): array
    {
        $open   = $this->fetchIssuesByState($repository, $token, 'open');
        $closed = $this->fetchIssuesByState($repository, $token, 'closed');

        return collect(array_merge($open, $closed))
            ->unique('number')
            ->values()
            ->all();
    }

    /**
     * @param 'open'|'closed' $state
     * @return array<int, array<string,mixed>>
     */
    private function fetchIssuesByState(Repository $repository, string $token, string $state): array
    {
        $issues = [];
        $page   = 1;

        do {
            $resp = Http::withToken($token)
                ->withHeaders([
                    'User-Agent'              => config('app.name', 'Forge'),
                    'Accept'                  => 'application/vnd.github+json',
                    'X-GitHub-Api-Version'    => '2022-11-28',
                ])
                ->acceptJson()
                ->get("https://api.github.com/repos/{$repository->owner}/{$repository->name}/issues", [
                    'state'    => $state,
                    'per_page' => 100,
                    'page'     => $page,
                ]);

            if ($resp->failed()) {
                throw new RuntimeException(
                    "GitHub API error ({$resp->status()} {$state}): " . substr($resp->body(), 0, 2000)
                );
            }

            // Map this page
            $batch = collect($resp->json() ?? [])
                ->filter(fn ($i) => empty($i['pull_request'])) // exclude PRs
                ->map(static function ($i) {
                    return [
                        'external_issue_id' => (string) $i['id'],
                        'number'            => (int) $i['number'],
                        'title'             => $i['title'],
                        'body'              => $i['body'],
                        'state'             => $i['state'], // open|closed
                        'url'               => $i['html_url'],
                        'assignee_login'    => Arr::get($i, 'assignee.login'),
                        'reporter_login'    => Arr::get($i, 'user.login'),
                        'labels'            => collect($i['labels'] ?? [])->pluck('name')->all(),
                        'created_at'        => $i['created_at'],
                        'updated_at'        => $i['updated_at'],
                        'closed_at'         => $i['closed_at'] ?? null,
                        'raw'               => $i,
                    ];
                })
                ->values()
                ->all();

            $issues = array_merge($issues, $batch);

            // Keep going **if** GitHub says there is a next page,
            // even when $batch is empty (e.g., page contains only PRs).
            $hasNext = $this->linkHeaderHasNext((string) $resp->header('Link'));
            $page++;
        } while ($hasNext);

        return $issues;
    }

    private function linkHeaderHasNext(?string $linkHeader): bool
    {
        if (!$linkHeader) {
            return false;
        }

        // Example: <https://api.github.com/...&page=2>; rel="next", <...>; rel="last"
        foreach (explode(',', $linkHeader) as $part) {
            if (str_contains($part, 'rel="next"')) {
                return true;
            }
        }
        return false;
    }

    /**
     * @throws JsonException
     */
    public function normalizeWebhook(array $headers, string $rawPayload): ?array
    {
        $event = $headers['X-GitHub-Event'] ?? null;
        if ($event !== 'issues') { return null; }

        $payload = json_decode($rawPayload, true, 512, JSON_THROW_ON_ERROR);
        if (!$payload) { return null; }

        $action = $payload['action'] ?? null;
        $issue  = $payload['issue']  ?? null;
        $repo   = $payload['repository'] ?? null;
        if (!$issue || !$repo) { return null; }

        if (!empty($issue['pull_request'])) { return null; } // ignore PRs

        return [
            'provider'          => 'github',
            'host'              => 'github.com',
            'owner'             => $repo['owner']['login'] ?? '',
            'name'              => $repo['name'] ?? '',
            'external_issue_id' => (string) $issue['id'],
            'number'            => (int) $issue['number'],
            'state'             => $issue['state'],
            'title'             => $issue['title'],
            'body'              => $issue['body'],
            'labels'            => collect($issue['labels'] ?? [])->pluck('name')->all(),
            'assignee_login'    => Arr::get($issue, 'assignee.login'),
            'reporter_login'    => Arr::get($issue, 'user.login'),
            'closed_at'         => $issue['closed_at'] ?? null,
            'raw'               => $payload,
            'action'            => $action,
        ];
    }

    /**
     * @throws JsonException
     */
    public function resolveRepositoryFromWebhook(array $headers, string $rawPayload): ?Repository
    {
        $payload = json_decode($rawPayload, true, 512, JSON_THROW_ON_ERROR);
        $repo = $payload['repository'] ?? null;
        if (!$repo) { return null; }

        return Repository::query()
            ->where('provider', 'github')
            ->where('host', 'github.com')
            ->where('owner', $repo['owner']['login'] ?? '')
            ->where('name', $repo['name'] ?? '')
            ->first();
    }

    public function searchBranches(Repository $repository, string $token, string $query = '', int $limit = 20): array
    {
        // GitHub branches list (no server-side search param) → client filter
        $resp = Http::withToken($token)
            ->withHeaders([
                'User-Agent' => config('app.name', 'Forge'),
                'Accept' => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => '2022-11-28',
            ])
            ->get("https://api.github.com/repos/{$repository->owner}/{$repository->name}/branches", [
                'per_page' => 100,
            ]);

        if ($resp->failed()) {
            throw new RuntimeException("GitHub API error (branches): {$resp->status()} ".substr($resp->body(),0,1000));
        }

        $items = collect($resp->json() ?? [])
            ->map(fn($b) => [
                'name' => $b['name'],
                'commit_sha' => Arr::get($b, 'commit.sha'),
                'protected' => (bool) ($b['protected'] ?? false),
                'default' => $b['name'] === ($repository->default_branch ?? ''),
                'url' => "https://github.com/{$repository->owner}/{$repository->name}/tree/{$b['name']}",
            ])
            ->filter(fn($b) => $query === '' || str_contains(strtolower($b['name']), strtolower($query)))
            ->take($limit)
            ->values()
            ->all();

        return $items;
    }

    public function searchPullRequests(Repository $repository, string $token, string $query = '', int $limit = 20): array
    {
        // List PRs (state=all), client-side filter on title/number/head/base
        $resp = Http::withToken($token)
            ->withHeaders([
                'User-Agent' => config('app.name', 'Forge'),
                'Accept' => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => '2022-11-28',
            ])
            ->get("https://api.github.com/repos/{$repository->owner}/{$repository->name}/pulls", [
                'state' => 'all',
                'per_page' => 100,
            ]);

        if ($resp->failed()) {
            throw new RuntimeException("GitHub API error (pulls): {$resp->status()} ".substr($resp->body(),0,1000));
        }

        $items = collect($resp->json() ?? [])
            ->map(fn($pr) => [
                'number' => (int) $pr['number'],
                'title' => $pr['title'],
                'state' => $pr['state'],
                'head' => Arr::get($pr, 'head.ref'),
                'base' => Arr::get($pr, 'base.ref'),
                'url'  => $pr['html_url'],
            ])
            ->filter(function ($pr) use ($query) {
                if ($query === '') { return true; }
                $q = strtolower($query);
                return str_contains(strtolower($pr['title']), $q)
                    || str_contains((string) $pr['number'], $q)
                    || str_contains(strtolower($pr['head'] ?? ''), $q)
                    || str_contains(strtolower($pr['base'] ?? ''), $q);
            })
            ->take($limit)
            ->values()
            ->all();

        return $items;
    }

    public function createBranch(Repository $repository, string $token, string $newBranch, ?string $fromRef = null): array
    {
        // Resolve base ref SHA
        $from = $fromRef ?: ($repository->default_branch ?: $this->getDefaultBranch($repository, $token));

        $refResp = Http::withToken($token)
            ->withHeaders([
                'User-Agent' => config('app.name', 'Forge'),
                'Accept' => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => '2022-11-28',
            ])
            ->get("https://api.github.com/repos/{$repository->owner}/{$repository->name}/git/ref/heads/{$from}");

        if ($refResp->failed()) {
            throw new RuntimeException("GitHub API error (get ref): {$refResp->status()} ".substr($refResp->body(),0,1000));
        }

        $sha = Arr::get($refResp->json(), 'object.sha');
        if (!$sha) {
            throw new RuntimeException('Unable to resolve base ref SHA.');
        }

        // Create the new ref
        $createResp = Http::withToken($token)
            ->withHeaders([
                'User-Agent' => config('app.name', 'Forge'),
                'Accept' => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => '2022-11-28',
            ])
            ->post("https://api.github.com/repos/{$repository->owner}/{$repository->name}/git/refs", [
                'ref' => "refs/heads/{$newBranch}",
                'sha' => $sha,
            ]);

        if ($createResp->failed()) {
            throw new RuntimeException("GitHub API error (create branch): {$createResp->status()} ".substr($createResp->body(),0,1000));
        }

        return [
            'name' => $newBranch,
            'url'  => "https://github.com/{$repository->owner}/{$repository->name}/tree/{$newBranch}",
        ];
    }

    public function createPullRequest(
        Repository $repository,
        string $token,
        string $title,
        string $head,
        string $base,
        ?string $body = null
    ): array {
        $resp = Http::withToken($token)
            ->withHeaders([
                'User-Agent' => config('app.name', 'Forge'),
                'Accept' => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => '2022-11-28',
            ])
            ->post("https://api.github.com/repos/{$repository->owner}/{$repository->name}/pulls", [
                'title' => $title,
                'head'  => $head, // same-repo branch name OR "owner:branch" cross-fork
                'base'  => $base,
                'body'  => $body,
            ]);

        if ($resp->failed()) {
            throw new RuntimeException("GitHub API error (create PR): {$resp->status()} ".substr($resp->body(),0,1000));
        }

        $pr = $resp->json();
        return [
            'number' => (int) $pr['number'],
            'title'  => $pr['title'],
            'state'  => $pr['state'],
            'url'    => $pr['html_url'],
        ];
    }

    public function getDefaultBranch(Repository $repository, string $token): string
    {
        if (!empty($repository->default_branch)) {
            return $repository->default_branch;
        }

        $resp = Http::withToken($token)
            ->withHeaders([
                'User-Agent' => config('app.name', 'Forge'),
                'Accept' => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => '2022-11-28',
            ])
            ->get("https://api.github.com/repos/{$repository->owner}/{$repository->name}");

        if ($resp->failed()) {
            throw new RuntimeException("GitHub API error (repo): {$resp->status()} ".substr($resp->body(),0,1000));
        }

        return (string) ($resp->json('default_branch') ?? 'main');
    }
}
