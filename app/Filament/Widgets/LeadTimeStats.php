<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LeadTimeStats extends BaseWidget
{
    protected ?string $heading = 'Lead Time (Created → Done)';

    public ?string $projectId = null;
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    protected function getStats(): array
    {
        if (blank($this->projectId)) {
            return [ Stat::make('P50', '0m'), Stat::make('P75', '0m'), Stat::make('P90', '0m') ];
        }

        $key = sprintf('reports:leadp:%s:%s:%s', $this->projectId, $this->dateFrom, $this->dateTo);

        /** @var array{p50:int,p75:int,p90:int} $p */
        $p = Cache::remember($key, 300, function (): array {
            $q = DB::table('issue_metrics')
                ->where('project_id', $this->projectId)
                ->whereNotNull('first_done_at');

            if ($this->dateFrom) { $q->whereDate('first_done_at', '>=', $this->dateFrom); }
            if ($this->dateTo)   { $q->whereDate('first_done_at', '<=', $this->dateTo); }

            /** @var Collection<int,int> $mins */
            $mins = $q->orderBy('lead_time_min')->pluck('lead_time_min')->map(fn ($v) => (int) $v)->values();

            return [
                'p50' => $this->percentile($mins, 0.50),
                'p75' => $this->percentile($mins, 0.75),
                'p90' => $this->percentile($mins, 0.90),
            ];
        });

        return [
            Stat::make('P50', $this->fmt($p['p50'])),
            Stat::make('P75', $this->fmt($p['p75'])),
            Stat::make('P90', $this->fmt($p['p90'])),
        ];
    }

    /** @param Collection<int,int> $sorted */
    private function percentile(\Illuminate\Support\Collection $sorted, float $p): int
    {
        $n = $sorted->count();
        if ($n === 0) { return 0; }
        $rank = ($n - 1) * $p;
        $low  = (int) floor($rank);
        $high = (int) ceil($rank);
        if ($low === $high) { return (int) $sorted[$low]; }
        $w = $rank - $low;
        return (int) round((1 - $w) * (int) $sorted[$low] + $w * (int) $sorted[$high]);
    }

    private function fmt(int $mins): string
    {
        $d = intdiv($mins, 1440);
        $h = intdiv($mins % 1440, 60);
        return $d > 0 ? "{$d}d {$h}h" : "{$h}h";
    }
}
