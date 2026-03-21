<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\Sprint;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class VelocityTrend extends ChartWidget
{
    protected ?string $heading = 'Velocity by Sprint';

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

        $cacheKey = sprintf('reports:velocity:%s:%s:%s', $this->projectId, $this->dateFrom, $this->dateTo);

        return Cache::remember($cacheKey, 300, function (): array {
            $sprints = Sprint::query()
                ->where('project_id', $this->projectId)
                ->whereNotNull('start_date')
                ->whereNotNull('end_date')
                ->when($this->dateFrom, fn ($query) => $query->whereDate('end_date', '>=', $this->dateFrom))
                ->when($this->dateTo, fn ($query) => $query->whereDate('start_date', '<=', $this->dateTo))
                ->orderByDesc('start_date')
                ->when(blank($this->dateFrom) && blank($this->dateTo), fn ($query) => $query->limit(12))
                ->get(['id', 'name', 'capacity', 'start_date', 'end_date'])
                ->reverse()
                ->values();

            if ($sprints->isEmpty()) {
                return ['labels' => [], 'datasets' => []];
            }

            $project = Project::query()->find($this->projectId);
            $defaultTarget = is_numeric($project?->setting('sprints.velocity_target'))
                ? (int) $project->setting('sprints.velocity_target')
                : null;

            $labels = [];
            $completedPoints = [];
            $completedIssues = [];
            $capacity = [];

            foreach ($sprints as $sprint) {
                $start = $sprint->start_date->copy()->startOfDay();
                $end = $sprint->end_date->copy()->endOfDay();

                $delivered = DB::table('issues as issues')
                    ->leftJoin('issue_metrics as metrics', 'metrics.issue_id', '=', 'issues.id')
                    ->where('issues.project_id', $this->projectId)
                    ->where('issues.sprint_id', $sprint->id)
                    ->whereBetween('metrics.first_done_at', [$start, $end])
                    ->selectRaw('COUNT(*) as issue_count, COALESCE(SUM(COALESCE(issues.story_points, 0)), 0) as point_count')
                    ->first();

                $labels[] = $sprint->name;
                $completedPoints[] = (int) ($delivered->point_count ?? 0);
                $completedIssues[] = (int) ($delivered->issue_count ?? 0);
                $capacity[] = $sprint->capacity ?? $defaultTarget;
            }

            $datasets = [
                ['label' => 'Delivered points', 'data' => $completedPoints],
                ['label' => 'Delivered issues', 'data' => $completedIssues],
            ];

            if (collect($capacity)->filter(static fn ($value) => $value !== null)->isNotEmpty()) {
                $datasets[] = ['label' => 'Capacity target', 'data' => $capacity];
            }

            return [
                'labels' => $labels,
                'datasets' => $datasets,
            ];
        });
    }
}
