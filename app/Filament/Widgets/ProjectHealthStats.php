<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ProjectHealthStats extends BaseWidget
{
    protected ?string $heading = 'Project Health';
    public ?string $projectId = null;

    protected function getStats(): array
    {
        $row = DB::table('report_project_daily_summaries')
            ->where('project_id', $this->projectId)
            ->orderByDesc('report_date')
            ->first();

        $open    = (int) ($row->open_count ?? 0);
        $wip     = (int) ($row->wip_count ?? 0);
        $done    = (int) ($row->done_count ?? 0);
        $tp      = (int) ($row->throughput_count ?? 0);
        $median  = (int) ($row->median_cycle_time_minutes ?? 0);

        return [
            Stat::make('Open', (string) $open),
            Stat::make('WIP', (string) $wip),
            Stat::make('Done', (string) $done),
            Stat::make('Throughput (24h)', (string) $tp),
            Stat::make('Median Cycle (m)', (string) $median),
        ];
    }
}
