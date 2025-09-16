<?php

namespace App\Observers;

use App\Domain\Issues\IssueRollupService;
use App\Jobs\RecalculateIssueRollups;
use App\Models\Goal;
use App\Models\Issue;
use App\Models\Project;
use App\Services\GoalProgressService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class IssueObserver
{
    /**
     * @throws Throwable
     */
    public function creating(Issue $issue): void
    {
        // If already set (e.g., import), skip.
        if ($issue->key && $issue->number) {
            return;
        }

        if (! $issue->project_id) {
            throw new RuntimeException('Issue requires a project_id to generate a key.');
        }

        // Generate number & key atomically.
        $attempts = 0;
        retry:
        $attempts++;
        try {
            DB::transaction(static function () use ($issue) {
                /** @var Project $project */
                $project = Project::query()->lockForUpdate()->findOrFail($issue->project_id);

                $next = (int) $project->next_issue_number + 1;

                // Assign
                $issue->number = $next;
                $issue->key = sprintf('%s-%d', strtoupper($project->key), $next);

                // Bump counter
                $project->next_issue_number = $next;
                $project->save();
            }, 3);
        } catch (QueryException|Throwable $e) {
             if ($attempts < 3) { goto retry; }
             throw $e;
        }
    }

    public function updating(Issue $issue): void
    {
        // Make key/number immutable after creation.
        if ($issue->isDirty('key') || $issue->isDirty('number')) {
            $issue->key = $issue->getOriginal('key');
            $issue->number = $issue->getOriginal('number');
        }
    }

    public function created(Issue $issue): void
    {
        Project::whereKey($issue->project_id)
            ->where('next_issue_number', '<', $issue->number)
            ->update(['next_issue_number' => $issue->number]);

        if ($issue->parent_id) {
            $this->dispatchRollup((string) $issue->parent_id);
        }
    }

    public function deleted(Issue $issue): void
    {
        if ($issue->parent_id) {
            $this->dispatchRollup((string) $issue->parent_id);
        }
    }

    public function updated(Issue $issue): void
    {
        $changed = $issue->getChanges();
        $touched = array_intersect_key($changed, array_flip(['parent_id', 'issue_status_id', 'story_points']));
        if ($touched === []) {
            return;
        }

        if ($issue->wasChanged('issue_status_id')) {
            $goals = $issue->goals ?? $issue->morphToMany(Goal::class, 'linkable', 'goal_links')->get();
            foreach ($goals as $goal) {
                app(GoalProgressService::class)->recalcGoal($goal);
            }
        }

        $originalParent = (string) ($issue->getOriginal('parent_id') ?? '');
        $newParent      = (string) ($issue->parent_id ?? '');

        if ($originalParent && $originalParent !== $newParent) {
            $this->dispatchRollup($originalParent);
        }
        if ($newParent) {
            $this->dispatchRollup($newParent);
        }
    }

    private function dispatchRollup(string $parentIssueId): void
    {
        $default = (string) Config::get('queue.default', 'sync');

        if ($default === 'sync') {
            // Run immediately in-process.
            app(IssueRollupService::class)->recalc($parentIssueId, true);
            return;
        }

        // Queue and guarantee it runs after DB commit.
        RecalculateIssueRollups::dispatch($parentIssueId)->afterCommit();
    }
}
