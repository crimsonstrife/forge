<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CycleTimeHistogram extends ChartWidget
{
    protected ?string $heading = 'Cycle Time Histogram';

    public ?string $projectId = null;
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        if (blank($this->projectId)) {
            return ['labels' => [], 'datasets' => []];
        }

        $cacheKey = sprintf('reports:cth:%s:%s:%s', $this->projectId, $this->dateFrom, $this->dateTo);

        return Cache::remember($cacheKey, 300, function (): array {
            $q = DB::table('issue_metrics')
                ->where('project_id', $this->projectId)
                ->whereNotNull('first_done_at');

            if ($this->dateFrom) {
                $q->whereDate('first_done_at', '>=', $this->dateFrom);
            }
            if ($this->dateTo) {
                $q->whereDate('first_done_at', '<=', $this->dateTo);
            }

            $mins = $q->pluck('cycle_time_min')->map(fn ($v) => (int) $v);

            if ($mins->isEmpty()) {
                return ['labels' => [], 'datasets' => []];
            }

            // buckets in minutes
            $buckets = [
                ['label' => '≤ 1d',   'max' => 1 * 24 * 60],
                ['label' => '1–2d',   'max' => 2 * 24 * 60],
                ['label' => '2–3d',   'max' => 3 * 24 * 60],
                ['label' => '3–7d',   'max' => 7 * 24 * 60],
                ['label' => '7–14d',  'max' => 14 * 24 * 60],
                ['label' => '14–30d', 'max' => 30 * 24 * 60],
                ['label' => '> 30d',  'max' => PHP_INT_MAX],
            ];

            $labels = array_column($buckets, 'label');
            $data   = array_fill(0, count($buckets), 0);

            foreach ($mins as $m) {
                foreach ($buckets as $i => $b) {
                    if ($m <= $b['max']) {
                        $data[$i]++;
                        break;
                    }
                }
            }

            return ['labels' => $labels, 'datasets' => [[ 'label' => 'Issues', 'data' => $data ]]];
        });
    }
}
