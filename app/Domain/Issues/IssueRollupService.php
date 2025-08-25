<?php
namespace App\Domain\Issues;

use App\Models\Issue;
use App\Models\IssueStatus;
use Illuminate\Support\Facades\DB;
use App\Models\Activity;

final class IssueRollupService
{
    /**
     * Recalculate roll-up metrics for a parent issue and (optionally) bubble upward.
     */
    public function recalc(string $parentIssueId, bool $bubble = true): void
    {
        /** @var Issue|null $parent */
        $parent = Issue::query()->find($parentIssueId);
        if (! $parent) {
            return;
        }

        // Snapshot before to decide whether to log
        $before = [
            'children_count'         => $parent->children_count,
            'children_done_count'    => $parent->children_done_count,
            'children_points_total'  => $parent->children_points_total,
            'children_points_done'   => $parent->children_points_done,
            'progress_percent'       => $parent->progress_percent,
        ];

        // Compute with a couple of tight queries
        $children = Issue::query()
            ->select(['id','issue_status_id','story_points'])
            ->where('parent_id', $parent->getKey())
            ->get();

        $total = $children->count();

        if ($total === 0) {
            $doneCount   = 0;
            $pointsTotal = 0;
            $pointsDone  = 0;
            $percent     = 0;
        } else {
            $doneStatusIds = IssueStatus::query()
                ->where('is_done', true)
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->all();

            $doneCount = $children->filter(
                fn (Issue $c): bool => in_array((string) $c->status_id, $doneStatusIds, true)
            )->count();

            $pointsTotal = (int) $children->sum('story_points');
            $pointsDone  = (int) $children->filter(
                fn (Issue $c): bool => in_array((string) $c->status_id, $doneStatusIds, true)
            )->sum('story_points');

            $percent = (int) round(($doneCount / max(1, $total)) * 100);
        }

        // Persist if changed
        $updates = [
            'children_count'         => $total,
            'children_done_count'    => $doneCount,
            'children_points_total'  => $pointsTotal,
            'children_points_done'   => $pointsDone,
            'progress_percent'       => $percent,
        ];

        if ($updates !== $before) {
            Issue::query()->whereKey($parent->getKey())->update($updates);

            // Activity entry (concise)
            activity('forge.issue')
                ->performedOn($parent)
                ->withProperties([
                    'old' => $before,
                    'new' => $updates,
                ])
                ->event('progress_updated')
                ->log('issue.progress_updated');
        }

        // Bubble to parent's parent if you allow deeper trees
        if ($bubble && $parent->parent_id) {
            $this->recalc((string) $parent->parent_id, true);
        }
    }
}
