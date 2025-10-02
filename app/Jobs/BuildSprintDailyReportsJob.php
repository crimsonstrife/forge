<?php

namespace App\Jobs;

use App\Models\Issue;
use App\Models\Sprint;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder as EBuilder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class BuildSprintDailyReportsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly string $projectId,
        private readonly string $sprintId,
        private readonly Carbon $forDate
    ) {
    }

    public function handle(): void
    {
        /** @var Sprint|null $sprint */
        $sprint = Sprint::query()
            ->select(['id', 'project_id', 'starts_at', 'ends_at'])
            ->whereKey($this->sprintId)
            ->first();

        if ($sprint === null || (string) $sprint->project_id !== (string) $this->projectId) {
            return;
        }

        $day = $this->forDate->copy()->startOfDay();

        // Remaining = items in sprint that are NOT done as of this day
        $q = Issue::query()
            ->join('issue_statuses', 'issue_statuses.id', '=', 'issues.issue_status_id')
            ->where('issues.project_id', $this->projectId)
            ->where('issues.sprint_id', $this->sprintId)
            ->where('issues.created_at', '<=', $day->endOfDay());

        // Treat "remaining" as statuses where is_done = false on that day
        $remaining = (clone $q)
            ->where('issue_statuses.is_done', false)
            ->selectRaw('COALESCE(SUM(CASE WHEN issues.story_points IS NULL THEN 0 ELSE issues.story_points END), 0) as points, COUNT(*) as count')
            ->first();

        $remainingPoints = (int) ($remaining->points ?? 0);
        $remainingIssues = (int) ($remaining->count ?? 0);

        DB::table('report_sprint_daily_summaries')->upsert(
            [[
                'id'                => (string) Str::uuid(),
                'project_id'        => $this->projectId,
                'sprint_id'         => $this->sprintId,
                'report_date'       => $day->toDateString(),
                'remaining_points'  => $remainingPoints,
                'remaining_issues'  => $remainingIssues,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]],
            ['project_id', 'sprint_id', 'report_date'],
            ['remaining_points', 'remaining_issues', 'updated_at'],
        );
    }
}
