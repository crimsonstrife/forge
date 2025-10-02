<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class CumulativeFlowChart extends ChartWidget
{
    protected ?string $heading = 'Cumulative Flow';
    public ?string $projectId = null;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $rows = DB::table('report_cfd_snapshots')
            ->where('project_id', $this->projectId)
            ->orderBy('report_date')
            ->get(['report_date', 'issue_status_id', 'count'])
            ->groupBy('report_date');

        $statusIds = DB::table('issue_statuses')->orderBy('id')->pluck('name', 'id');

        $labels = [];
        $series = [];
        foreach ($statusIds as $sid => $name) {
            $series[$sid] = ['label' => $name, 'data' => []];
        }

        foreach ($rows as $date => $items) {
            $labels[] = $date;
            $byStatus = collect($items)->keyBy('issue_status_id');
            foreach ($statusIds as $sid => $_) {
                $series[$sid]['data'][] = (int) ($byStatus[$sid]->count ?? 0);
            }
        }

        return [
            'labels' => $labels,
            'datasets' => array_values($series),
        ];
    }
}
