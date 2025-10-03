<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ProjectHealthStats extends BaseWidget
{
    protected ?string $heading = 'Project Health';

    public ?string $projectId = null;
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    protected function getStats(): array
    {
        // Latest daily summary for Open/WIP/Done/Throughput
        $row = DB::table('report_project_daily_summaries')
            ->where('project_id', $this->projectId)
            ->orderByDesc('report_date')
            ->first();

        $open   = (int) ($row?->open_count ?? 0);
        $wip    = (int) ($row?->wip_count ?? 0);
        $done   = (int) ($row?->done_count ?? 0);
        $tp24h  = (int) ($row?->throughput_count ?? 0);

        // True cycle-time median over selected range using issue_metrics
        $mq = DB::table('issue_metrics')
            ->where('project_id', $this->projectId)
            ->whereNotNull('first_done_at');

        if ($this->dateFrom) {
            $mq->whereDate('first_done_at', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $mq->whereDate('first_done_at', '<=', $this->dateTo);
        }

        // MySQL-friendly median: order + offset
        $median = (int) (optional(
            $mq->orderBy('cycle_time_min')
                ->skip(max(0, (int) floor(max(0, $mq->count() - 1) / 2)))
                ->take(1)
                ->first(['cycle_time_min'])
        )->cycle_time_min ?? 0);

        return [
            Stat::make('Open', (string) $open),
            Stat::make('WIP', (string) $wip),
            Stat::make('Done', (string) $done),
            Stat::make('Throughput (24h)', (string) $tp24h),
            Stat::make('Median Cycle (m)', (string) $median),
        ];
    }
}
