<?php

namespace App\Jobs;

use App\Models\Sprint;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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

    public int $tries = 5;

    /** @var array<int, int> */
    public array $backoff = [5, 15, 30, 60];

    private const TRANSACTION_ATTEMPTS = 5;

    public function __construct(
        private readonly string $projectId,
        private readonly string $sprintId,
        private readonly Carbon $forDate
    ) {}

    public function handle(): void
    {
        /** @var Sprint|null $sprint */
        $sprint = Sprint::query()
            ->select(['id', 'project_id', 'start_date', 'end_date'])
            ->whereKey($this->sprintId)
            ->first();

        if ($sprint === null || (string) $sprint->project_id !== (string) $this->projectId) {
            return;
        }

        $day = $this->forDate->copy()->startOfDay();
        $sprintStart = $sprint->start_date?->copy()->startOfDay();
        $sprintEnd = $sprint->end_date?->copy()->endOfDay();

        if ($sprintStart === null || $sprintEnd === null || $day->lt($sprintStart) || $day->gt($sprintEnd)) {
            return;
        }

        $q = DB::table('issues as issues')
            ->leftJoin('issue_metrics as metrics', 'metrics.issue_id', '=', 'issues.id')
            ->where('issues.project_id', $this->projectId)
            ->where('issues.sprint_id', $this->sprintId)
            ->where('issues.created_at', '<=', $day->endOfDay());

        $remaining = (clone $q)
            ->where(function ($query) use ($day): void {
                $query->whereNull('metrics.first_done_at')
                    ->orWhere('metrics.first_done_at', '>', $day->endOfDay());
            })
            ->selectRaw('COALESCE(SUM(COALESCE(issues.story_points, 0)), 0) as points, COUNT(*) as count')
            ->first();

        $remainingPoints = (int) ($remaining->points ?? 0);
        $remainingIssues = (int) ($remaining->count ?? 0);

        DB::transaction(function () use ($day, $remainingPoints, $remainingIssues): void {
            DB::table('report_sprint_daily_summaries')->upsert(
                [[
                    'id' => (string) Str::uuid(),
                    'project_id' => $this->projectId,
                    'sprint_id' => $this->sprintId,
                    'report_date' => $day->toDateString(),
                    'remaining_points' => $remainingPoints,
                    'remaining_issues' => $remainingIssues,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]],
                ['project_id', 'sprint_id', 'report_date'],
                ['remaining_points', 'remaining_issues', 'updated_at'],
            );
        }, self::TRANSACTION_ATTEMPTS);
    }
}
