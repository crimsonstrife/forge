<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BurnupTrend extends ChartWidget
{
    protected ?string $heading = 'Burnup';

    public ?string $projectId = null;
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        if (blank($this->projectId)) {
            return ['labels' => [], 'datasets' => []];
        }

        $cacheKey = sprintf('reports:burnup:%s:%s:%s', $this->projectId, $this->dateFrom, $this->dateTo);

        return Cache::remember($cacheKey, 300, function (): array {
            $q = DB::table('report_project_daily_summaries')
                ->where('project_id', $this->projectId);

            if ($this->dateFrom) {
                $q->whereDate('report_date', '>=', $this->dateFrom);
            }
            if ($this->dateTo) {
                $q->whereDate('report_date', '<=', $this->dateTo);
            }

            $rows = $q->orderBy('report_date')
                ->get(['report_date', 'open_count', 'wip_count', 'done_count']);

            if ($rows->isEmpty()) {
                return ['labels' => [], 'datasets' => []];
            }

            $labels = [];
            $scope = [];
            $done = [];
            $cursor = Carbon::parse($rows->first()->report_date)->startOfDay();
            $last = Carbon::parse($rows->last()->report_date)->startOfDay();
            $map = $rows->keyBy('report_date');
            $lastKnown = null;

            while ($cursor->lte($last)) {
                $key = $cursor->toDateString();
                $row = $map[$key] ?? $lastKnown;

                $labels[] = $key;
                $scope[] = $row ? (int) $row->open_count + (int) $row->wip_count + (int) $row->done_count : 0;
                $done[] = $row ? (int) $row->done_count : 0;

                if (isset($map[$key])) {
                    $lastKnown = $map[$key];
                }

                $cursor->addDay();
            }

            return [
                'labels' => $labels,
                'datasets' => [
                    ['label' => 'Done', 'data' => $done],
                    ['label' => 'Total scope', 'data' => $scope],
                ],
            ];
        });
    }
}
