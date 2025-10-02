<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ThroughputTrend extends ChartWidget
{
    protected ?string $heading = 'Throughput (30d)';
    public ?string $projectId = null;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $rows = DB::table('report_project_daily_summaries')
            ->where('project_id', $this->projectId)
            ->orderByDesc('report_date')
            ->limit(30)
            ->get(['report_date', 'throughput_count'])
            ->reverse()
            ->values();

        return [
            'labels' => $rows->pluck('report_date')->all(),
            'datasets' => [[
                'label' => 'Done per day',
                'data' => $rows->pluck('throughput_count')->map(fn ($v) => (int) $v)->all(),
            ]],
        ];
    }
}
