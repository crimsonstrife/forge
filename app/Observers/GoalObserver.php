<?php
namespace App\Observers;

use App\Models\Goal;
use App\Services\GoalProgressService;

final class GoalObserver
{
    public function saved(Goal $goal): void
    {
        app(GoalProgressService::class)->recalcGoal($goal);
    }
}
