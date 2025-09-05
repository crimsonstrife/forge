<?php
namespace App\Contracts;

use App\Models\Repository;

interface RepositoryProviderInterface
{
    /**
     * Fetch all issues (open + closed) for a repo, paginated internally.
     * Returns normalized arrays for upsert.
     * @return array<int, array<string,mixed>>
     */
    public function fetchAllIssues(Repository $repository, string $token): array;

    /**
     * Given a webhook payload, return a normalized “issue changed” event
     * or null if irrelevant.
     * @return array<string,mixed>|null
     */
    public function normalizeWebhook(array $headers, string $rawPayload): ?array;

    /**
     * Validate webhook signature and return the matching Repository or null.
     */
    public function resolveRepositoryFromWebhook(array $headers, string $rawPayload): ?Repository;
}
