<?php
namespace App\Services;

use App\Enums\KRAutomation;
use App\Models\Goal;
use App\Models\GoalKeyResult;
use App\Models\Issue;
use App\Models\IssueStatus;
use Illuminate\Support\Collection;

final class GoalProgressService
{
    /** @var Collection<int,int>|null */
    private ?Collection $doneStatusIds = null;

    public function recalcGoal(Goal $goal): float
    {
        // Ensure KRs are available
        $goal->loadMissing('keyResults');

        // Apply automation → updates KR.current_value
        foreach ($goal->keyResults as $kr) {
            $this->applyAutomation($kr);
        }

        // Final weighted recompute lives on the model
        return $goal->recomputeProgress();
    }

    private function applyAutomation(GoalKeyResult $kr): void
    {
        if ($kr->automation === KRAutomation::Manual) {
            if ($last = $kr->checkins()->latest('created_at')->first()) {
                $kr->forceFill(['current_value' => (float) $last->value])->saveQuietly();
            }
            return;
        }

        if ($kr->automation === KRAutomation::IssuesDonePercent) {
            $this->issuesDonePercent($kr);
            return;
        }

        if ($kr->automation === KRAutomation::StoryPointsDonePercent) {
            $this->storyPointsDonePercent($kr);
            return;
        }
    }

    private function issuesDonePercent(GoalKeyResult $kr): void
    {
        $goal = $kr->goal()->with(['issues:id,status_id', 'projects:id'])->firstOrFail();

        $directIssueIds = $goal->issues()->select('issues.id')->pluck('id');
        $projectIds     = $goal->projects()->select('projects.id')->pluck('id');

        $q = Issue::query()->select('id', 'status_id');

        if ($directIssueIds->isNotEmpty()) {
            $q->whereIn('id', $directIssueIds);
        }
        if ($projectIds->isNotEmpty()) {
            $q->orWhereIn('project_id', $projectIds);
        }

        $total = (clone $q)->distinct('id')->count('id');
        if ($total === 0) {
            $kr->forceFill(['current_value' => 0.0])->saveQuietly();
            return;
        }

        $done = (clone $q)->whereIn('status_id', $this->doneStatusIds())->distinct('id')->count('id');

        $kr->forceFill(['current_value' => round(($done / $total) * 100.0, 2)])->saveQuietly();
    }

    private function storyPointsDonePercent(GoalKeyResult $kr): void
    {
        $goal = $kr->goal()->with(['issues:id,status_id,story_points', 'projects:id'])->firstOrFail();

        $directIssueIds = $goal->issues()->select('issues.id')->pluck('id');
        $projectIds     = $goal->projects()->select('projects.id')->pluck('id');

        $base = Issue::query()->select('id', 'status_id', 'story_points');

        if ($directIssueIds->isNotEmpty()) {
            $base->whereIn('id', $directIssueIds);
        }
        if ($projectIds->isNotEmpty()) {
            $base->orWhereIn('project_id', $projectIds);
        }

        // Sum story points; treat NULL as 0
        $totalPoints = (clone $base)->selectRaw('COALESCE(SUM(story_points),0) as s')->value('s');

        if ((float) $totalPoints <= 0.0) {
            $kr->forceFill(['current_value' => 0.0])->saveQuietly();
            return;
        }

        $donePoints = (clone $base)
            ->whereIn('status_id', $this->doneStatusIds())
            ->selectRaw('COALESCE(SUM(story_points),0) as s')
            ->value('s');

        $percent = round(((float) $donePoints / (float) $totalPoints) * 100.0, 2);
        $kr->forceFill(['current_value' => $percent])->saveQuietly();
    }

    /** @return Collection<int,int> */
    private function doneStatusIds(): Collection
    {
        if ($this->doneStatusIds === null) {
            $this->doneStatusIds = IssueStatus::query()->where('is_done', true)->pluck('id');
        }
        return $this->doneStatusIds;
    }
}
