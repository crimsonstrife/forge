<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ThroughputTrend extends ChartWidget
{
    protected ?string $heading = 'Throughput';
    public ?string $projectId = null;
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $cacheKey = sprintf('reports:tp:%s:%s:%s', $this->projectId, $this->dateFrom, $this->dateTo);

        return Cache::remember($cacheKey, 300, function (): array {
            $q = DB::table('report_project_daily_summaries')
                ->where('project_id', $this->projectId);

            if ($this->dateFrom) { $q->whereDate('report_date', '>=', $this->dateFrom); }
            if ($this->dateTo)   { $q->whereDate('report_date', '<=', $this->dateTo); }

            $rows = $q->orderBy('report_date')->get(['report_date', 'throughput_count']);

            if ($rows->isEmpty()) {
                return ['labels' => [], 'datasets' => [[ 'label' => 'Done per day', 'data' => [] ]]];
            }

            // Ensure missing days render as 0
            $labels = [];
            $data   = [];
            $cursor = Carbon::parse($rows->first()->report_date);
            $last   = Carbon::parse($rows->last()->report_date);
            $map    = $rows->keyBy('report_date');

            while ($cursor->lte($last)) {
                $key = $cursor->toDateString();
                $labels[] = $key;
                $data[] = (int) ($map[$key]->throughput_count ?? 0);
                $cursor->addDay();
            }

            return [
                'labels'   => $labels,
                'datasets' => [[ 'label' => 'Done per day', 'data' => $data ]],
            ];
        });
    }
}
