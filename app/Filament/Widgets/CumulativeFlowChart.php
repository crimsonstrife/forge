<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CumulativeFlowChart extends ChartWidget
{
    protected ?string $heading = 'Cumulative Flow';
    public ?string $projectId = null;
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $cacheKey = sprintf('reports:cfd:%s:%s:%s', $this->projectId, $this->dateFrom, $this->dateTo);

        return Cache::remember($cacheKey, 300, function (): array {
            $q = DB::table('report_cfd_snapshots')
                ->where('project_id', $this->projectId);

            if ($this->dateFrom) {
                $q->whereDate('report_date', '>=', $this->dateFrom);
            }
            if ($this->dateTo) {
                $q->whereDate('report_date', '<=', $this->dateTo);
            }

            $rows = $q->orderBy('report_date')
                ->get(['report_date', 'issue_status_id', 'count'])
                ->groupBy('report_date');

            if ($rows->isEmpty()) {
                return ['labels' => [], 'datasets' => []];
            }

            $statusIds = DB::table('issue_statuses')->orderBy('id')->pluck('name', 'id');

            $labels = [];
            $series = [];
            foreach ($statusIds as $sid => $name) {
                $series[$sid] = ['label' => $name, 'data' => []];
            }

            foreach ($rows as $date => $items) {
                $labels[] = $date;
                $counts = collect($items)->keyBy('issue_status_id');
                foreach ($statusIds as $sid => $_) {
                    $series[$sid]['data'][] = (int) ($counts[$sid]->count ?? 0);
                }
            }

            return ['labels' => $labels, 'datasets' => array_values($series)];
        });
    }
}
