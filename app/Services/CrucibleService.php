<?php

namespace App\Services;

use App\Models\Repository;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class CrucibleService
{
    public function isConfigured(): bool
    {
        return (bool) config('crucible.enabled')
            && filled(config('crucible.url'))
            && filled(config('crucible.app_token'));
    }

    public function baseUrl(): string
    {
        return rtrim((string) config('crucible.url'), '/');
    }

    public function host(): string
    {
        $baseUrl = $this->baseUrl();
        $host = (string) parse_url($baseUrl, PHP_URL_HOST);
        $port = parse_url($baseUrl, PHP_URL_PORT);

        if ($host === '') {
            return '';
        }

        return $port ? "{$host}:{$port}" : $host;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function searchRepositories(string $query = '', int $limit = 20, string $forForgeUserId = ''): array
    {
        if (! $this->isConfigured()) {
            return [];
        }

        $needle = strtolower(trim($query));
        $matches = collect();
        $page = 1;
        $maxPages = 5;

        do {
            $payload = $this->fetchIndexPage($page, $query, $forForgeUserId);
            $items = collect($payload['data'] ?? [])
                ->map(fn (array $repo) => $this->normalizeRepository($repo))
                ->filter(function (array $repo) use ($needle) {
                    if ($needle === '') {
                        return true;
                    }

                    $haystack = strtolower(implode(' ', array_filter([
                        $repo['organization_slug'] ?? null,
                        $repo['organization_name'] ?? null,
                        $repo['slug'] ?? null,
                        $repo['name'] ?? null,
                        $repo['description'] ?? null,
                    ])));

                    return str_contains($haystack, $needle);
                });

            $matches = $matches->concat($items);

            $page++;
            $hasMore = filled($payload['next_page_url'] ?? null) && $page <= $maxPages && $matches->count() < $limit;
        } while ($hasMore);

        return $matches
            ->unique(fn (array $repo) => (string) ($repo['id'] ?? ($repo['organization_slug'] . '/' . $repo['slug'])))
            ->take($limit)
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRepository(string $organizationSlug, string $repositorySlug, string $forForgeUserId = ''): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $response = $this->client()->get(sprintf(
            '%s/api/v1/%s/%s',
            $this->baseUrl(),
            rawurlencode($organizationSlug),
            rawurlencode($repositorySlug),
        ), $this->userScopedQuery($forForgeUserId));

        if ($response->status() === 404) {
            return null;
        }

        if ($response->failed()) {
            throw new RuntimeException('Crucible repository lookup failed (HTTP ' . $response->status() . ').');
        }

        $data = $response->json('data');

        return is_array($data) ? $this->normalizeRepository($data) : null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function searchBranches(Repository $repository, string $query = '', int $limit = 20, string $forForgeUserId = ''): array
    {
        $items = $this->fetchCollectionFromCandidates(
            $this->repositoryEndpointCandidates($repository, 'branches'),
            $this->vcsQuery($query, $limit, $forForgeUserId),
            'Crucible branch lookup failed',
        );

        return $items
            ->map(fn (array $branch) => $this->normalizeBranch($repository, $branch))
            ->filter(fn (array $branch) => $branch['name'] !== '')
            ->take($limit)
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function searchPullRequests(Repository $repository, string $query = '', int $limit = 20, string $forForgeUserId = ''): array
    {
        $items = $this->fetchCollectionFromCandidates(
            [
                ...$this->repositoryEndpointCandidates($repository, 'pull-requests'),
                ...$this->repositoryEndpointCandidates($repository, 'pulls'),
            ],
            $this->vcsQuery($query, $limit, $forForgeUserId),
            'Crucible pull request lookup failed',
        );

        return $items
            ->map(fn (array $pullRequest) => $this->normalizePullRequest($pullRequest))
            ->filter(fn (array $pullRequest) => $pullRequest['number'] > 0)
            ->take($limit)
            ->values()
            ->all();
    }

    public function getDefaultBranch(Repository $repository, string $forForgeUserId = ''): string
    {
        if (filled($repository->default_branch)) {
            return (string) $repository->default_branch;
        }

        $remote = $this->getRepository($repository->owner, $repository->name, $forForgeUserId);

        return (string) ($remote['default_branch'] ?? 'main');
    }

    /**
     * Create a branch on a Crucible repository.
     *
     * @return array{name: string, url: string|null}
     */
    public function createBranch(Repository $repository, string $branchName, ?string $fromRef, string $forForgeUserId, ?string $forgeIssueKey = null): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Crucible integration is not configured.');
        }

        $url = sprintf(
            '%s/api/v1/%s/%s/branches',
            $this->baseUrl(),
            rawurlencode((string) $repository->owner),
            rawurlencode((string) $repository->name),
        );

        $payload = array_filter([
            'name' => $branchName,
            'from_ref' => $fromRef,
            'forge_issue_key' => $forgeIssueKey,
            'for_forge_user_id' => $forForgeUserId,
        ], static fn (mixed $v): bool => $v !== null && $v !== '');

        $response = $this->client()->post($url, $payload);

        if ($response->failed()) {
            throw new RuntimeException(
                'Crucible branch creation failed (HTTP ' . $response->status() . '): '
                . substr((string) $response->body(), 0, 300)
            );
        }

        $data = $response->json('data') ?? $response->json();

        return [
            'name' => (string) ($data['name'] ?? $branchName),
            'url' => $data['url'] ?? null,
        ];
    }

    /**
     * Create a pull request on a Crucible repository.
     *
     * @return array{number: int|null, title: string, state: string, url: string|null}
     */
    public function createPullRequest(Repository $repository, string $title, string $head, string $base, ?string $body, string $forForgeUserId, ?string $forgeIssueKey = null): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Crucible integration is not configured.');
        }

        $url = sprintf(
            '%s/api/v1/%s/%s/pull-requests',
            $this->baseUrl(),
            rawurlencode((string) $repository->owner),
            rawurlencode((string) $repository->name),
        );

        $payload = array_filter([
            'title' => $title,
            'head' => $head,
            'base' => $base,
            'body' => $body,
            'forge_issue_key' => $forgeIssueKey,
            'for_forge_user_id' => $forForgeUserId,
        ], static fn (mixed $v): bool => $v !== null && $v !== '');

        $response = $this->client()->post($url, $payload);

        if ($response->failed()) {
            throw new RuntimeException(
                'Crucible pull request creation failed (HTTP ' . $response->status() . '): '
                . substr((string) $response->body(), 0, 300)
            );
        }

        $data = $response->json('data') ?? $response->json();

        return [
            'number' => isset($data['number']) ? (int) $data['number'] : null,
            'title' => (string) ($data['title'] ?? $title),
            'state' => (string) ($data['state'] ?? 'open'),
            'url' => $data['url'] ?? null,
        ];
    }

    /**
     * Register a Forge integration on a Crucible repository.
     *
     * Calls Crucible's POST /{org}/{repo}/forge-integration endpoint to create
     * or update the ForgeIntegration record, removing the need to configure the
     * link from the Crucible side first.
     *
     * @return array{id: string, api_token: string|null}|null
     */
    public function registerForgeIntegration(string $organizationSlug, string $repositorySlug, string $forgeProjectId, string $forgeProjectName, string $forForgeUserId): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $url = sprintf(
            '%s/api/v1/%s/%s/forge-integration',
            $this->baseUrl(),
            rawurlencode($organizationSlug),
            rawurlencode($repositorySlug),
        );

        $response = $this->client()
            ->post($url, array_filter([
                'forge_project_id' => $forgeProjectId,
                'forge_project_name' => $forgeProjectName,
                'forge_url' => config('app.url'),
                'for_forge_user_id' => $forForgeUserId,
            ]));

        if ($response->failed()) {
            throw new RuntimeException(
                'Crucible integration registration failed (HTTP ' . $response->status() . '): '
                . substr((string) $response->body(), 0, 300)
            );
        }

        return $response->json('data');
    }

    /**
     * @return array<string, mixed>
     */
    private function fetchIndexPage(int $page = 1, string $query = '', string $forForgeUserId = ''): array
    {
        $response = $this->client()->get(
            $this->baseUrl() . '/api/v1/repositories',
            $this->filteredQuery(array_merge(
                ['page' => $page],
                $this->userScopedQuery($forForgeUserId),
                [
                    'q' => $query !== '' ? $query : null,
                    'search' => $query !== '' ? $query : null,
                ],
            )),
        );

        if ($response->failed()) {
            throw new RuntimeException('Crucible repository search failed (HTTP ' . $response->status() . ').');
        }

        $payload = $response->json();

        return is_array($payload) ? $payload : [];
    }

    /**
     * @param  array<int, string>  $candidates
     * @param  array<string, mixed>  $query
     * @return Collection<int, array<string, mixed>>
     */
    private function fetchCollectionFromCandidates(array $candidates, array $query, string $errorPrefix): Collection
    {
        $lastStatus = 404;

        foreach (array_values(array_unique($candidates)) as $candidate) {
            $response = $this->client()->get($candidate, $query);

            if ($response->status() === 404) {
                $lastStatus = 404;

                continue;
            }

            if ($response->failed()) {
                throw new RuntimeException($errorPrefix . ' (HTTP ' . $response->status() . ').');
            }

            return collect($this->extractCollection($response))
                ->filter(fn (mixed $item) => is_array($item))
                ->map(fn (array $item) => $item)
                ->values();
        }

        throw new RuntimeException($errorPrefix . ' (HTTP ' . $lastStatus . ').');
    }

    /**
     * @return array<int, mixed>
     */
    private function extractCollection(Response $response): array
    {
        $payload = $response->json();

        if (is_array($payload['data'] ?? null)) {
            return $payload['data'];
        }

        return is_array($payload) ? $payload : [];
    }

    /**
     * @return array<int, string>
     */
    private function repositoryEndpointCandidates(Repository $repository, string $suffix): array
    {
        $candidates = [
            sprintf(
                '%s/api/v1/%s/%s/%s',
                $this->baseUrl(),
                rawurlencode((string) $repository->owner),
                rawurlencode((string) $repository->name),
                $suffix,
            ),
        ];

        if (filled($repository->external_id)) {
            $candidates[] = sprintf(
                '%s/api/v1/repositories/%s/%s',
                $this->baseUrl(),
                rawurlencode((string) $repository->external_id),
                $suffix,
            );
        }

        return $candidates;
    }

    /**
     * @param  array<string, mixed>  $branch
     * @return array<string, mixed>
     */
    private function normalizeBranch(Repository $repository, array $branch): array
    {
        $name = (string) ($branch['name'] ?? $branch['ref'] ?? Arr::get($branch, 'branch', ''));
        $url = (string) ($branch['url'] ?? $branch['web_url'] ?? $branch['html_url'] ?? '');

        return [
            'name' => $name,
            'commit_sha' => (string) (
                $branch['commit_sha']
                ?? Arr::get($branch, 'commit.sha')
                ?? Arr::get($branch, 'target.sha')
                ?? Arr::get($branch, 'target.oid')
                ?? ''
            ),
            'protected' => (bool) ($branch['protected'] ?? Arr::get($branch, 'is_protected', false)),
            'default' => (bool) ($branch['default'] ?? ($name !== '' && $name === (string) $repository->default_branch)),
            'url' => $url !== '' ? $url : null,
        ];
    }

    /**
     * @param  array<string, mixed>  $pullRequest
     * @return array<string, mixed>
     */
    private function normalizePullRequest(array $pullRequest): array
    {
        $url = (string) ($pullRequest['url'] ?? $pullRequest['web_url'] ?? $pullRequest['html_url'] ?? '');

        return [
            'number' => (int) ($pullRequest['number'] ?? $pullRequest['iid'] ?? 0),
            'title' => (string) ($pullRequest['title'] ?? $pullRequest['name'] ?? ''),
            'state' => (string) ($pullRequest['state'] ?? $pullRequest['status'] ?? ''),
            'head' => (string) (
                Arr::get($pullRequest, 'head.ref')
                ?? $pullRequest['head']
                ?? $pullRequest['source_branch']
                ?? ''
            ),
            'base' => (string) (
                Arr::get($pullRequest, 'base.ref')
                ?? $pullRequest['base']
                ?? $pullRequest['target_branch']
                ?? ''
            ),
            'url' => $url !== '' ? $url : null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function vcsQuery(string $query = '', int $limit = 20, string $forForgeUserId = ''): array
    {
        return $this->filteredQuery(array_merge(
            $this->userScopedQuery($forForgeUserId),
            [
                'q' => $query !== '' ? $query : null,
                'search' => $query !== '' ? $query : null,
                'limit' => $limit,
                'per_page' => $limit,
            ],
        ));
    }

    /**
     * @return array<string, string>
     */
    private function userScopedQuery(string $forForgeUserId = ''): array
    {
        if ($forForgeUserId === '') {
            return [];
        }

        return [
            'for_forge_user_id' => $forForgeUserId,
        ];
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    private function filteredQuery(array $query): array
    {
        return array_filter($query, static fn (mixed $value): bool => $value !== null && $value !== '');
    }

    private function client(): PendingRequest
    {
        return Http::withToken((string) config('crucible.app_token'))
            ->timeout(10)
            ->acceptJson()
            ->withoutVerifying();
    }

    /**
     * @param  array<string, mixed>  $repository
     * @return array<string, mixed>
     */
    private function normalizeRepository(array $repository): array
    {
        $organizationSlug = (string) Arr::get($repository, 'organization.slug', '');
        $organizationName = (string) Arr::get($repository, 'organization.name', $organizationSlug);
        $repositorySlug = (string) ($repository['slug'] ?? '');
        $repositoryName = (string) ($repository['name'] ?? $repositorySlug);
        $forgeIntegration = Arr::get($repository, 'forge_integration', Arr::get($repository, 'forgeIntegration', []));

        return [
            'id' => (string) ($repository['id'] ?? ''),
            'slug' => $repositorySlug,
            'name' => $repositoryName,
            'description' => (string) ($repository['description'] ?? ''),
            'organization_slug' => $organizationSlug,
            'organization_name' => $organizationName,
            'default_branch' => (string) ($repository['default_branch'] ?? 'main'),
            'visibility' => $repository['visibility'] ?? null,
            'vcs_type' => $repository['vcs_type'] ?? null,
            'web_url' => $organizationSlug !== '' && $repositorySlug !== ''
                ? $this->baseUrl() . '/' . $organizationSlug . '/' . $repositorySlug
                : null,
            'forge_project_id' => (string) Arr::get($forgeIntegration, 'forge_project_id', ''),
            'forge_project_name' => (string) Arr::get($forgeIntegration, 'forge_project_name', ''),
            'forge_url' => (string) Arr::get($forgeIntegration, 'forge_url', ''),
        ];
    }
}
