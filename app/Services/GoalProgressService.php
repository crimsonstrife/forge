<?php
namespace App\Services;

use App\Enums\KRAutomation;
use App\Models\Goal;
use App\Models\GoalKeyResult;

final class GoalProgressService
{
    public function recalcGoal(Goal $goal): float
    {
        $goal->loadMissing('keyResults');

        $totalWeight = max($goal->keyResults->sum('weight'), 1);
        $score = 0.0;

        foreach ($goal->keyResults as $kr) {
            $this->applyAutomation($kr);
            $score += ($kr->percentComplete() * $kr->weight);
        }

        $percent = round($score / $totalWeight, 2);
        $goal->progress = $percent;
        $goal->saveQuietly();

        return $percent;
    }

    private function applyAutomation(GoalKeyResult $kr): void
    {
        if ($kr->automation === KRAutomation::Manual) {
            if ($last = $kr->checkins()->latest('created_at')->first()) {
                $kr->current_value = $last->value;
                $kr->saveQuietly();
            }
            return;
        }

        if ($kr->automation === KRAutomation::IssuesDonePercent) {
            $goal = $kr->goal()->with('goal.links')->firstOrFail();
            $issueIds = $goal->issues()->select('issues.id')->pluck('id');

            if ($issueIds->isEmpty()) {
                $kr->current_value = 0;
                $kr->saveQuietly();
                return;
            }

            $total = $issueIds->count();
            $done = \App\Models\Issue::query()
                ->whereIn('id', $issueIds)
                ->whereHas('status', fn ($q) => $q->where('is_done', true))
                ->count();

            $kr->current_value = $total > 0 ? ($done / $total) * 100.0 : 0.0;
            $kr->saveQuietly();
        }

        // StoryPointsDonePercent can be added similarly using your points field.
    }
}
