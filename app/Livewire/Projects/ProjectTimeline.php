<?php

namespace App\Livewire\Projects;

use App\Models\Issue;
use App\Models\Project;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

/**
 * Gantt-like timeline using ApexCharts RangeBar.
 */
final class ProjectTimeline extends Component
{
    public Project $project;

    /** @var 'assignee'|'status' */
    public string $groupBy = 'assignee';

    public function mount(Project $project): void
    {
        $this->authorize('view', $project);
        $this->project = $project;
    }

    /**
     * @return array{series: array<int, array{name:string, data: array<int, array{x:string, y: array{0:int,1:int}, issueId:string, url:string, fillColor:string}>>>}
     */
    public function getChartData(): array
    {
        $issues = Issue::query()
            ->where('project_id', $this->project->id)
            ->whereNotNull('starts_at')
            ->whereNotNull('due_at')
            ->with([
                'assignee:id,name',
                'status:id,name',
                'type:id,tier', // <- for tier color
            ])
            ->get([
                'id','key','summary',
                'assignee_id','issue_status_id','issue_type_id',
                'starts_at','due_at',
            ]);

        $buckets = [];

        foreach ($issues as $i) {
            // Compute start/end safely
            $start = $i->starts_at;
            $end   = $i->due_at;
            if ($end->lessThan($start)) {
                [$start, $end] = [$end, $start];
            }

            // Group label
            $label = $this->groupBy === 'assignee'
                ? ($i->assignee?->name ?? 'Unassigned')
                : ($i->status?->name ?? 'Unknown');

            // Tier color
            $tier  = $i->type?->tier?->value ?? 'other';
            $color = method_exists($i->type, 'badgeColor')
                ? $i->type->badgeColor()
                : match ($tier) {
                    'epic'    => '#7e57c2',
                    'story'   => '#1e88e5',
                    'task'    => '#9e9e9e',
                    'subtask' => '#78909C',
                    default   => '#607D8B',
                };

            $buckets[$label][] = [
                'x' => trim(($i->key ?? '') . ' — ' . $i->summary),
                'y' => [$start->valueOf(), $end->valueOf()], // ms since epoch
                'issueId'   => (string) $i->id,
                'url'       => route('issues.show', ['issue' => $i->key, 'project' => $this->project->id]),
                'fillColor' => $color, // <- ApexCharts per-point color
            ];
        }

        $series = [];
        foreach ($buckets as $label => $rows) {
            $series[] = ['name' => $label, 'data' => $rows];
        }

        return ['series' => $series];
    }


    public function render()
    {
        return view('livewire.projects.project-timeline');
    }

    public function authorize($ability, mixed $arguments = []): void
    {
        if (Gate::denies($ability, $arguments)) {
            throw new AuthorizationException();
        }
    }
}
