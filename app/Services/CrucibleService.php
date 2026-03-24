<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
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
    public function searchRepositories(string $query = '', int $limit = 20): array
    {
        if (! $this->isConfigured()) {
            return [];
        }

        $needle = strtolower(trim($query));
        $matches = collect();
        $page = 1;
        $maxPages = 5;

        do {
            $payload = $this->fetchIndexPage($page);
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
    public function getRepository(string $organizationSlug, string $repositorySlug): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $response = $this->client()->get(sprintf(
            '%s/api/v1/%s/%s',
            $this->baseUrl(),
            rawurlencode($organizationSlug),
            rawurlencode($repositorySlug),
        ));

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
     * @return array<string, mixed>
     */
    private function fetchIndexPage(int $page = 1): array
    {
        $response = $this->client()->get($this->baseUrl() . '/api/v1/repositories', [
            'page' => $page,
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Crucible repository search failed (HTTP ' . $response->status() . ').');
        }

        $payload = $response->json();

        return is_array($payload) ? $payload : [];
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
