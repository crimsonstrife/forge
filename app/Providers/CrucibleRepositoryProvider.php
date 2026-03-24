<?php

namespace App\Providers;

use App\Contracts\RepositoryProviderInterface;
use App\Models\Repository;
use App\Services\CrucibleService;
use RuntimeException;

final class CrucibleRepositoryProvider implements RepositoryProviderInterface
{
    public function __construct(private CrucibleService $crucible)
    {
    }

    public function fetchAllIssues(Repository $repository, string $token): array
    {
        throw new RuntimeException('Forge issue import is not supported for Crucible repositories.');
    }

    public function normalizeWebhook(array $headers, string $rawPayload): ?array
    {
        return null;
    }

    public function resolveRepositoryFromWebhook(array $headers, string $rawPayload): ?Repository
    {
        return null;
    }

    public function searchBranches(Repository $repository, string $token, string $query = '', int $limit = 20): array
    {
        return $this->crucible->searchBranches($repository, $query, $limit, $token);
    }

    public function searchPullRequests(Repository $repository, string $token, string $query = '', int $limit = 20): array
    {
        return $this->crucible->searchPullRequests($repository, $query, $limit, $token);
    }

    public function createBranch(Repository $repository, string $token, string $newBranch, ?string $fromRef = null): array
    {
        throw new RuntimeException('Create branch is not available from Forge for Crucible repositories yet.');
    }

    public function createPullRequest(
        Repository $repository,
        string $token,
        string $title,
        string $head,
        string $base,
        ?string $body = null
    ): array {
        throw new RuntimeException('Open pull request is not available from Forge for Crucible repositories yet.');
    }

    public function getDefaultBranch(Repository $repository, string $token): string
    {
        return $this->crucible->getDefaultBranch($repository, $token);
    }
}
