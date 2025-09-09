<?php

namespace App\Contracts;

use App\Models\Repository;

interface RepositoryProviderInterface
{
    /** @return array<int,array<string,mixed>> */
    public function fetchAllIssues(Repository $repository, string $token): array;

    /** @return array<string,mixed>|null */
    public function normalizeWebhook(array $headers, string $rawPayload): ?array;

    public function resolveRepositoryFromWebhook(array $headers, string $rawPayload): ?Repository;

    /** Branch search: return [{name, commit_sha, protected, default, url}] */
    public function searchBranches(Repository $repository, string $token, string $query = '', int $limit = 20): array;

    /** PR search: return [{number, title, state, head, base, url}] */
    public function searchPullRequests(Repository $repository, string $token, string $query = '', int $limit = 20): array;

    /** Create a branch from baseRef (default branch if null). Return {name, url}. */
    public function createBranch(Repository $repository, string $token, string $newBranch, ?string $fromRef = null): array;

    /** Create a PR. Return {number, title, state, url}. */
    public function createPullRequest(
        Repository $repository,
        string $token,
        string $title,
        string $head,   // e.g. "owner:feature/foo" or just "feature/foo" within same repo
        string $base,   // usually default branch
        ?string $body = null
    ): array;

    /** Resolve repository default branch name. */
    public function getDefaultBranch(Repository $repository, string $token): string;
}
