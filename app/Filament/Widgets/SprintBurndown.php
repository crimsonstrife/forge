<?php

namespace App\Filament\Widgets;

use App\Models\Sprint;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SprintBurndown extends ChartWidget
{
    protected ?string $heading = 'Sprint Burndown';

    public ?string $projectId = null;
    public ?string $sprintId = null;

    public static function canView(): bool
    {
        return true;
    }

    protected function getType(): string
    {
        return 'line';
    }

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('sprintId')
                ->label('Sprint')
                ->options(fn (): array => Sprint::query()
                    ->where('project_id', $this->projectId)
                    ->orderByDesc('starts_at')
                    ->pluck('name', 'id')
                    ->all()
                )
                ->reactive(),
        ]);
    }

    protected function getData(): array
    {
        if (blank($this->projectId) || blank($this->sprintId)) {
            return ['labels' => [], 'datasets' => []];
        }

        $cacheKey = sprintf('reports:burndown:%s:%s', $this->projectId, $this->sprintId);

        return Cache::remember($cacheKey, 300, function (): array {
            /** @var Sprint|null $sprint */
            $sprint = Sprint::query()
                ->select(['id','starts_at','ends_at'])
                ->whereKey($this->sprintId)
                ->first();

            if ($sprint === null) {
                return ['labels' => [], 'datasets' => []];
            }

            $start = Carbon::parse($sprint->starts_at)->startOfDay();
            $end   = Carbon::parse($sprint->ends_at)->startOfDay();

            $rows = DB::table('report_sprint_daily_summaries')
                ->where('project_id', $this->projectId)
                ->where('sprint_id', $this->sprintId)
                ->whereBetween('report_date', [$start->toDateString(), $end->toDateString()])
                ->orderBy('report_date')
                ->get(['report_date', 'remaining_points', 'remaining_issues']);

            if ($rows->isEmpty()) {
                return ['labels' => [], 'datasets' => []];
            }

            // Build continuous date range
            $labels = [];
            $actual = [];
            $actualIssues = [];
            $map = $rows->keyBy('report_date');

            $cursor = $start->copy();
            $last   = $end->copy();

            // Start line from first day's remaining points
            $startPoints = (int) ($map[$start->toDateString()]->remaining_points ?? 0);
            $days = max(1, $start->diffInDays($end));
            $ideal = [];

            while ($cursor->lte($last)) {
                $key = $cursor->toDateString();
                $labels[] = $key;

                $actual[] = (int) ($map[$key]->remaining_points ?? (count($actual) ? end($actual) : $startPoints));
                $actualIssues[] = (int) ($map[$key]->remaining_issues ?? 0);

                $dayIdx = $cursor->diffInDays($start);
                $ideal[] = (int) round($startPoints * (1 - ($dayIdx / $days)));

                $cursor->addDay();
            }

            return [
                'labels' => $labels,
                'datasets' => [
                    ['label' => 'Ideal (points)', 'data' => $ideal],
                    ['label' => 'Remaining (points)', 'data' => $actual],
                    ['label' => 'Remaining (issues)', 'data' => $actualIssues],
                ],
            ];
        });
    }
}
