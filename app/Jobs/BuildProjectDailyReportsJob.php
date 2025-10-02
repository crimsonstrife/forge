<?php

namespace App\Jobs;

use App\Models\Issue;
use App\Models\IssueStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

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

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        $start = $this->forDate->copy()->startOfDay();
        $end   = $this->forDate->copy()->endOfDay();

        $issues = Issue::query()
            ->select(['id', 'project_id', 'issue_status_id', 'created_at', 'updated_at'])
            ->where('project_id', $this->projectId)
            ->where('created_at', '<=', $end)
            ->get();

        $statusById = IssueStatus::query()
            ->select(['id', 'name', 'is_done'])
            ->get()
            ->keyBy('id');

        $open = 0;
        $wip = 0;
        $done = 0;

        foreach ($issues as $issue) {
            $status = $statusById[(int) $issue->issue_status_id] ?? null;
            if ($status === null) {
                \Log::warning('Issue status not found', [
                    'issue_id' => $issue->id,
                    'issue_status_id' => $issue->issue_status_id,
                ]);
                // Optionally, skip counting this issue
                continue;
            }
            if ($status->is_done) {
                $done++;
            } else {
                $issue->updated_at->greaterThan($start->copy()->subDays(7)) ? $wip++ : $open++;
            }
        }

        $throughput = Issue::query()
            ->where('project_id', $this->projectId)
            ->whereBetween('updated_at', [$start, $end])
            ->whereHas('status', static function (Builder $q): void {
                $q->where('is_done', true);
            })
            ->count();

        $doneIssues = Issue::query()
            ->where('project_id', $this->projectId)
            ->whereHas('status', static fn (Builder $q) => $q->where('is_done', true))
            ->whereDate('updated_at', '<=', $end)
            ->select(['created_at', 'updated_at'])
            ->get()
            ->map(static fn (Issue $i): int => (int) $i->created_at->diffInMinutes($i->updated_at))
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

        $counts = Issue::query()
            ->selectRaw('issue_status_id, COUNT(*) as total')
            ->where('project_id', $this->projectId)
            ->where('created_at', '<=', $end)
            ->groupBy('issue_status_id')
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
