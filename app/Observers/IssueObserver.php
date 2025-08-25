<?php

namespace App\Observers;

use App\Jobs\RecalculateIssueRollups;
use App\Models\Issue;

class IssueObserver
{
    public function created(Issue $issue): void
    {
        if ($issue->parent_id) {
            RecalculateIssueRollups::dispatch((string) $issue->parent_id);
        }
    }

    public function deleted(Issue $issue): void
    {
        if ($issue->parent_id) {
            RecalculateIssueRollups::dispatch((string) $issue->parent_id);
        }
    }

    public function updated(Issue $issue): void
    {
        $changed = $issue->getChanges();

        $touched = array_intersect_key($changed, array_flip([
            'parent_id', 'issue_status_id', 'story_points',
        ]));

        if ($touched === []) {
            return;
        }

        // If re-parented, recalc old and new parents
        $originalParent = (string) ($issue->getOriginal('parent_id') ?? '');
        $newParent      = (string) ($issue->parent_id ?? '');

        if ($originalParent && $originalParent !== $newParent) {
            RecalculateIssueRollups::dispatch($originalParent);
        }
        if ($newParent) {
            RecalculateIssueRollups::dispatch($newParent);
        }
    }
}
