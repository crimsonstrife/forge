<?php

namespace App\Observers;

use App\Domain\Issues\Events\IssueAssigneeChanged;
use App\Domain\Issues\IssueRollupService;
use App\Jobs\ComputeIssueMetricsJob;
use App\Jobs\RecalculateIssueRollups;
use App\Models\Goal;
use App\Models\Issue;
use App\Models\IssueStatus;
use App\Models\IssueStatusEvent;
use App\Models\Project;
use App\Services\GoalProgressService;
use App\Services\Issues\IssueCollaborationService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Laravel\Pennant\Feature;
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

                if (empty($issue->assignee_id) && Feature::active('solo-mode') && Auth::check()) {
                    $issue->assignee_id = Auth::id();
                }

                // Bump counter
                $project->next_issue_number = $next;
                $project->save();
            }, 3);
        } catch (QueryException|Throwable $e) {
            if ($attempts < 3) {
                goto retry;
            }
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

        app(IssueCollaborationService::class)->ensureCoreFollowers($issue);

        if ($issue->parent_id) {
            $this->dispatchRollup((string) $issue->parent_id);
        }

        if ($issue->assignee_id) {
            event(new IssueAssigneeChanged(
                issueId: (string) $issue->getKey(),
                newAssigneeId: (string) $issue->assignee_id,
                actorId: auth()->id() ? (string) auth()->id() : null,
            ));
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
        $touched = array_intersect_key($changed, array_flip(['parent_id', 'issue_status_id', 'story_points', 'assignee_id']));
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

        // Assignee changed? Notify the new assignee (and optionally the old one).
        if ($issue->wasChanged('assignee_id')) {
            $newId = (string) ($issue->assignee_id ?? '');
            if ($newId !== '') {
                event(new IssueAssigneeChanged(
                    issueId: (string) $issue->getKey(),
                    newAssigneeId: (string) $issue->assignee_id,
                    actorId: auth()->id() ? (string) auth()->id() : null,
                ));
            }
        }

        if ($issue->wasChanged('issue_status_id')) {
            $fromStatus = $issue->getOriginal('issue_status_id')
                ? IssueStatus::query()->find((int) $issue->getOriginal('issue_status_id'))
                : null;
            $toStatus = IssueStatus::query()->find((int) $issue->issue_status_id);

            IssueStatusEvent::query()->updateOrCreate(
                [
                    'issue_id'      => (string) $issue->getKey(),
                    'to_status_id'  => (int) $issue->issue_status_id,
                    'changed_at'    => now(),
                ],
                [
                    'from_status_id' => $issue->getOriginal('issue_status_id') !== null
                        ? (int) $issue->getOriginal('issue_status_id')
                        : null,
                    'changed_by_id'  => Auth::id(),
                ],
            );

            app(IssueCollaborationService::class)->notifyStatusChanged(
                issue: $issue,
                fromStatus: $fromStatus,
                toStatus: $toStatus,
                actor: Auth::user(),
            );

            dispatch(new ComputeIssueMetricsJob($issue->getKey()));
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
