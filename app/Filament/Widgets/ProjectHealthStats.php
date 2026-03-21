<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProjectHealthStats extends BaseWidget
{
    protected ?string $heading = 'Project Health';

    public ?string $projectId = null;
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    protected function getStats(): array
    {
        if (blank($this->projectId)) {
            return [
                Stat::make('Open', '0'),
                Stat::make('WIP', '0'),
                Stat::make('Done', '0'),
                Stat::make('Done in Range', '0'),
                Stat::make('Overdue', '0'),
                Stat::make('Median Cycle', '0m'),
            ];
        }

        $asOf = $this->dateTo
            ? Carbon::parse($this->dateTo)->endOfDay()
            : now();
        $rangeStart = $this->dateFrom
            ? Carbon::parse($this->dateFrom)->startOfDay()
            : $asOf->copy()->subDays(29)->startOfDay();
        $rangeEnd = $asOf->copy();

        $summary = DB::table('report_project_daily_summaries')
            ->where('project_id', $this->projectId)
            ->whereDate('report_date', '<=', $asOf->toDateString())
            ->orderByDesc('report_date')
            ->first(['open_count', 'wip_count', 'done_count']);

        if ($summary !== null) {
            $open = (int) $summary->open_count;
            $wip = (int) $summary->wip_count;
            $done = (int) $summary->done_count;
        } else {
            $counts = DB::table('issues as issues')
                ->leftJoin('issue_metrics as metrics', 'metrics.issue_id', '=', 'issues.id')
                ->leftJoin('issue_statuses as statuses', 'statuses.id', '=', 'issues.issue_status_id')
                ->where('issues.project_id', $this->projectId)
                ->where('issues.created_at', '<=', $asOf)
                ->selectRaw(
                    'SUM(CASE
                        WHEN metrics.first_done_at IS NOT NULL AND metrics.first_done_at <= ? THEN 1
                        WHEN statuses.is_done = 1 AND issues.updated_at <= ? THEN 1
                        ELSE 0
                    END) as done_count,
                    SUM(CASE
                        WHEN (metrics.first_done_at IS NULL OR metrics.first_done_at > ?)
                         AND metrics.first_started_at IS NOT NULL
                         AND metrics.first_started_at <= ? THEN 1
                        ELSE 0
                    END) as wip_count,
                    SUM(CASE
                        WHEN (metrics.first_done_at IS NULL OR metrics.first_done_at > ?)
                         AND (metrics.first_started_at IS NULL OR metrics.first_started_at > ?)
                         AND NOT (statuses.is_done = 1 AND issues.updated_at <= ?) THEN 1
                        ELSE 0
                    END) as open_count',
                    [$asOf, $asOf, $asOf, $asOf, $asOf, $asOf, $asOf]
                )
                ->first();

            $open = (int) ($counts?->open_count ?? 0);
            $wip = (int) ($counts?->wip_count ?? 0);
            $done = (int) ($counts?->done_count ?? 0);
        }

        $overdue = DB::table('issues as issues')
            ->leftJoin('issue_metrics as metrics', 'metrics.issue_id', '=', 'issues.id')
            ->where('issues.project_id', $this->projectId)
            ->where('issues.created_at', '<=', $asOf)
            ->whereNotNull('issues.due_at')
            ->where('issues.due_at', '<', $asOf)
            ->where(function ($query) use ($asOf): void {
                $query->whereNull('metrics.first_done_at')
                    ->orWhere('metrics.first_done_at', '>', $asOf);
            })
            ->count();

        $doneInRange = DB::table('issue_metrics')
            ->where('project_id', $this->projectId)
            ->whereBetween('first_done_at', [$rangeStart, $rangeEnd])
            ->count();

        $mq = DB::table('issue_metrics')
            ->where('project_id', $this->projectId)
            ->whereBetween('first_done_at', [$rangeStart, $rangeEnd])
            ->orderBy('cycle_time_min');

        $count = $mq->count();
        $median = 0;

        if ($count > 0) {
            if ($count % 2 === 1) {
                $medianRow = $mq->skip((int) floor($count / 2))
                    ->take(1)
                    ->first(['cycle_time_min']);
                $median = (int) ($medianRow?->cycle_time_min ?? 0);
            } else {
                $middleRows = $mq->skip((int) ($count / 2) - 1)
                    ->take(2)
                    ->get(['cycle_time_min']);
                $median = $middleRows->count() === 2
                    ? (int) round(((int) $middleRows[0]->cycle_time_min + (int) $middleRows[1]->cycle_time_min) / 2)
                    : (int) ($middleRows[0]->cycle_time_min ?? 0);
            }
        }

        return [
            Stat::make('Open', (string) $open),
            Stat::make('WIP', (string) $wip),
            Stat::make('Done', (string) $done),
            Stat::make('Done in Range', (string) $doneInRange),
            Stat::make('Overdue', (string) $overdue),
            Stat::make('Median Cycle', $this->formatMinutes($median)),
        ];
    }

    private function formatMinutes(int $minutes): string
    {
        $days = intdiv($minutes, 1440);
        $hours = intdiv($minutes % 1440, 60);
        $mins = $minutes % 60;

        if ($days > 0) {
            return $mins > 0 ? "{$days}d {$hours}h {$mins}m" : "{$days}d {$hours}h";
        }

        if ($hours > 0) {
            return $mins > 0 ? "{$hours}h {$mins}m" : "{$hours}h";
        }

        return "{$mins}m";
    }
}
