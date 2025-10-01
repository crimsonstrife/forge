<?php

namespace App\Services\Issues;

use App\Models\Issue;
use App\Models\IssueStatus;
use App\Models\ProjectStatusTransition;
use Illuminate\Support\Collection;

final class IssueStatusTransitionService
{
    /**
     * @return Collection<int, IssueStatus>
     */
    public function allowedToStatusesForIssue(Issue $issue): Collection
    {
        if (!$issue->issue_status_id) {
            return collect();
        }

        $toIds = ProjectStatusTransition::query()
            ->forProjectFrom($issue->project_id, (int) $issue->issue_status_id)
            ->where(function ($q) use ($issue) {
                $q->where('is_global', true)
                    ->orWhere(function ($qq) use ($issue) {
                        $qq->where('is_global', false)
                            ->where('issue_type_id', $issue->issue_type_id);
                    });
            })
            ->pluck('to_status_id')
            ->unique()
            ->values();

        // Fallback: if no transitions configured, allow any status.
        if ($toIds->isEmpty()) {
            return IssueStatus::query()
                ->orderBy('name')
                ->get(['id', 'name', 'color', 'is_done']);
        }

        return IssueStatus::query()
            ->whereIn('id', $toIds)
            ->orderBy('name')
            ->get(['id', 'name', 'color', 'is_done']);
    }

    public function canTransition(Issue $issue, int $toStatusId): bool
    {
        if ((int) $issue->issue_status_id === $toStatusId) {
            return true;
        }

        $allowed = $this->allowedToStatusesForIssue($issue)->pluck('id')->all();

        // If transitions are configured, enforce them strictly:
        if (!empty($allowed)) {
            return in_array($toStatusId, $allowed, true);
        }

        // If not configured, allow any valid status id:
        return IssueStatus::query()->whereKey($toStatusId)->exists();
    }
}
