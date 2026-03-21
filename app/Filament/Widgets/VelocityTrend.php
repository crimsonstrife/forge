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
            $sprintIds = $sprints->pluck('id')->all();
            $deliveredBySprint = DB::table('issues as issues')
                ->join('sprints as sprints', 'sprints.id', '=', 'issues.sprint_id')
                ->leftJoin('issue_metrics as metrics', 'metrics.issue_id', '=', 'issues.id')
                ->where('issues.project_id', $this->projectId)
                ->whereIn('issues.sprint_id', $sprintIds)
                ->whereNotNull('metrics.first_done_at')
                ->whereRaw('DATE(metrics.first_done_at) BETWEEN sprints.start_date AND sprints.end_date')
                ->groupBy('issues.sprint_id')
                ->get([
                    'issues.sprint_id',
                    DB::raw('COUNT(*) as issue_count'),
                    DB::raw('COALESCE(SUM(COALESCE(issues.story_points, 0)), 0) as point_count'),
                ])
                ->keyBy('sprint_id');

            $labels = [];
            $completedPoints = [];
            $completedIssues = [];
            $capacity = [];

            foreach ($sprints as $sprint) {
                $delivered = $deliveredBySprint->get($sprint->id);

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
