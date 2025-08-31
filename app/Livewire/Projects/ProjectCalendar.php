<?php

namespace App\Livewire\Projects;

use App\Models\Issue;
use App\Models\Project;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Project calendar powered by FullCalendar.
 *
 * @property-read Project $project
 */
final class ProjectCalendar extends Component
{
    public Project $project;

    /** @var array{status?:array<int>, assignees?:array<int>} */
    public array $filters = [];

    public function mount(Project $project): void
    {
        $this->authorize('view', $project);
        $this->project = $project;
    }

    /**
     * Return FullCalendar-compatible events.
     * @return array<int, array<string,mixed>>
     */
    public function getEvents(): array
    {
        $query = Issue::query()
            ->where('project_id', $this->project->id)
            ->where(function ($q) {
                $q->whereNotNull('starts_at')->orWhereNotNull('due_at');   // ← only dated issues
            })
            ->with(['assignee:id,name', 'status:id,name,color']);

        if (! empty($this->filters['status'])) {
            $query->whereIn('issue_status_id', $this->filters['status']);
        }
        if (! empty($this->filters['assignees'])) {
            $query->whereIn('assignee_id', $this->filters['assignees']);
        }

        $issues = $query->get();

        return $issues->map(function (Issue $i): array {
            $start = $i->starts_at?->toIso8601String();
            $end   = $i->due_at?->toIso8601String();

            // only due date → all-day
            if (! $start && $end) {
                return [
                    'id'    => "issue-{$i->id}",
                    'title' => trim(($i->key ?? '') . ' — ' . $i->summary),
                    'start' => $end,
                    'allDay' => true,
                    'url'   => route('issues.show', ['project' => $this->project, 'issue' => $i]), // ← correct route
                    'backgroundColor' => $i->status?->color ?? '#6c757d',
                ];
            }

            // start only → 1h block
            if ($start && ! $end) {
                $end = $i->starts_at?->addHour()->toIso8601String();
            }

            return [
                'id'    => "issue-{$i->id}",
                'title' => trim(($i->key ?? '') . ' — ' . $i->summary),
                'start' => $start,
                'end'   => $end,
                'allDay' => false,
                'url'   => route('issues.show', ['project' => $this->project, 'issue' => $i]), // ← correct route
                'backgroundColor' => $i->status?->color ?? '#6c757d',
            ];
        })->values()->all();
    }

    /**
     * Handle event move/resize from FullCalendar.
     */
    #[On('calendar:move-issue')]
    public function updateIssueDates(int $issueId, string $startIso = null, string $endIso = null): void
    {
        /** @var Issue $issue */
        $issue = Issue::query()->where('project_id', $this->project->id)->findOrFail($issueId);

        $this->authorize('update', $issue);

        $issue->starts_at = $startIso ? CarbonImmutable::parse($startIso) : null;
        $issue->due_at    = $endIso ? CarbonImmutable::parse($endIso) : null;
        $issue->save();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Issue dates updated.',
        ]);
    }

    public function render()
    {
        return view('livewire.projects.project-calendar');
    }

    public function authorize($ability, mixed $arguments = []): void
    {
        if (Gate::denies($ability, $arguments)) {
            throw new AuthorizationException();
        }
    }
}
