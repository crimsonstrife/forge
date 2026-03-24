<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BuildProjectDailyReportsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly string $projectId,
        private readonly Carbon $forDate
    ) {
    }

    public function handle(): void
    {
        $start = $this->forDate->copy()->startOfDay();
        $end   = $this->forDate->copy()->endOfDay();

        $issues = DB::table('issues as issues')
            ->leftJoin('issue_metrics as metrics', 'metrics.issue_id', '=', 'issues.id')
            ->leftJoin('issue_statuses as statuses', 'statuses.id', '=', 'issues.issue_status_id')
            ->where('issues.project_id', $this->projectId)
            ->where('issues.created_at', '<=', $end)
            ->get([
                'issues.id',
                'issues.updated_at as issue_updated_at',
                'metrics.first_started_at',
                'metrics.first_done_at',
                'statuses.is_done as current_status_is_done',
            ]);

        $open = 0;
        $wip = 0;
        $done = 0;

        foreach ($issues as $issue) {
            $firstStartedAt = filled($issue->first_started_at)
                ? Carbon::parse($issue->first_started_at)
                : null;
            $firstDoneAt = filled($issue->first_done_at)
                ? Carbon::parse($issue->first_done_at)
                : null;

            if ($firstDoneAt?->lte($end)) {
                $done++;
                continue;
            }

            if ($firstStartedAt?->lte($end)) {
                $wip++;
                continue;
            }

            if ((bool) ($issue->current_status_is_done ?? false) && Carbon::parse($issue->issue_updated_at)->lte($end)) {
                $done++;
                continue;
            }

            $open++;
        }

        $throughput = DB::table('issue_metrics')
            ->where('project_id', $this->projectId)
            ->whereBetween('first_done_at', [$start, $end])
            ->count();

        $doneIssues = DB::table('issue_metrics')
            ->where('project_id', $this->projectId)
            ->whereBetween('first_done_at', [$start, $end])
            ->orderBy('cycle_time_min')
            ->pluck('cycle_time_min')
            ->map(static fn ($minutes): int => (int) $minutes)
            ->sort()
            ->values();

        $median = $this->percentile($doneIssues, 0.5);
        $p75    = $this->percentile($doneIssues, 0.75);

        DB::transaction(function () use ($open, $wip, $done, $throughput, $median, $p75, $start): void {
            DB::table('report_project_daily_summaries')->upsert(
                [[
                    'id' => (string) Str::uuid(), // for insert only
                    'project_id' => $this->projectId,
                    'report_date' => $start->toDateString(),
                    'open_count' => $open,
                    'wip_count' => $wip,
                    'done_count' => $done,
                    'throughput_count' => $throughput,
                    'median_cycle_time_minutes' => $median,
                    'p75_cycle_time_minutes' => $p75,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]],
                // unique-by columns (must match your unique index)
                ['project_id', 'report_date'],
                // columns to update on conflict (exclude 'id', 'project_id', 'report_date', 'created_at')
                [
                    'open_count',
                    'wip_count',
                    'done_count',
                    'throughput_count',
                    'median_cycle_time_minutes',
                    'p75_cycle_time_minutes',
                    'updated_at',
                ],
            );

        });

        $rankedStatusEvents = DB::table('issue_status_events')
            ->where('changed_at', '<=', $end)
            ->selectRaw(
                'issue_id, to_status_id, ROW_NUMBER() OVER (
                    PARTITION BY issue_id
                    ORDER BY changed_at DESC, created_at DESC, id DESC
                ) as status_rank'
            );

        $latestStatuses = DB::query()
            ->fromSub($rankedStatusEvents, 'ranked_status_events')
            ->where('status_rank', 1)
            ->select([
                'ranked_status_events.issue_id',
                'ranked_status_events.to_status_id',
            ]);

        $counts = DB::table('issues as issues')
            ->leftJoinSub($latestStatuses, 'latest_statuses', static function ($join): void {
                $join->on('latest_statuses.issue_id', '=', 'issues.id');
            })
            ->selectRaw('COALESCE(latest_statuses.to_status_id, issues.issue_status_id) as issue_status_id, COUNT(*) as total')
            ->where('issues.project_id', $this->projectId)
            ->where('issues.created_at', '<=', $end)
            ->groupByRaw('COALESCE(latest_statuses.to_status_id, issues.issue_status_id)')
            ->pluck('total', 'issue_status_id');

        $values = [];
        foreach ($counts as $statusId => $total) {
            $values[] = [
                'id' => (string) Str::uuid(), // for insert only
                'project_id' => $this->projectId,
                'report_date' => $start->toDateString(),
                'issue_status_id' => (int) $statusId,
                'count' => (int) $total,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if ($values === []) {
            DB::table('report_cfd_snapshots')
                ->where('project_id', $this->projectId)
                ->where('report_date', $start->toDateString())
                ->delete();

            return;
        }

        DB::table('report_cfd_snapshots')->upsert(
            $values,
            ['project_id', 'report_date', 'issue_status_id'],
            ['count', 'updated_at'],
        );
    }

    /**
     * @param Collection<int,int> $sortedMinutes
     */
    private function percentile(Collection $sortedMinutes, float $p): int
    {
        $n = $sortedMinutes->count();
        if ($n === 0) {
            return 0;
        }

        $rank = ($n - 1) * $p;
        $low  = (int) floor($rank);
        $high = (int) ceil($rank);

        if ($low === $high) {
            return (int) $sortedMinutes[$low];
        }

        $weight = $rank - $low;
        return (int) round((1 - $weight) * $sortedMinutes[$low] + $weight * $sortedMinutes[$high]);
    }
}
