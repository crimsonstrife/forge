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
     * @return array<string,mixed>
     */
    public function getChartData(): array
    {
        $issues = Issue::query()
            ->where('project_id', $this->project->id)
            ->whereNotNull('starts_at')
            ->whereNotNull('due_at')
            ->with(['assignee:id,name', 'status:id,name'])
            ->get();

        $buckets = [];

        foreach ($issues as $i) {
            $label = $this->groupBy === 'assignee'
                ? ($i->assignee?->name ?? 'Unassigned')
                : ($i->status?->name ?? 'Unknown');

            $buckets[$label][] = [
                'x' => "{$i->key} — {$i->summary}",
                'y' => [
                    $i->starts_at->getTimestampMs(),
                    $i->due_at->getTimestampMs(),
                ],
                'issueId' => $i->id,
                'url' => route('projects.issues.show', ['project' => $this->project, 'issue' => $i]),
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
