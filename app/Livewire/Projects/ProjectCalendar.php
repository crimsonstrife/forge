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
                $q->whereNotNull('starts_at')->orWhereNotNull('due_at'); // only dated issues
            })
            ->with([
                'assignee:id,name',
                'status:id,name,color',
                'type:id,tier',
            ]);

        if (! empty($this->filters['status'])) {
            $query->whereIn('issue_status_id', $this->filters['status']);
        }
        if (! empty($this->filters['assignees'])) {
            $query->whereIn('assignee_id', $this->filters['assignees']);
        }

        $issues = $query->get([
            'id','key','summary',
            'issue_status_id','issue_type_id','assignee_id',
            'starts_at','due_at',
        ]);

        return $issues->map(function (Issue $i): array {
            $start = $i->starts_at?->toIso8601String();
            $end   = $i->due_at?->toIso8601String();

            // Only due date → all-day
            $allDay = false;
            if (! $start && $end) {
                $allDay = true;
                $start = $end; // FC allDay needs a date; we use due_at as the single-day event
            }
            // Start only → default to 1h block
            if ($start && ! $end) {
                $end = $i->starts_at?->addHour()->toIso8601String();
            }

            // Tier color & icon
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
            $icon  = method_exists($i->type, 'iconName')
                ? $i->type->iconName()
                : match ($tier) {
                    'epic'    => 'all_inclusive',
                    'story'   => 'menu_book',
                    'task'    => 'check_box',
                    'subtask' => 'subdirectory_arrow_right',
                    default   => 'filter_none',
                };

            return [
                'id'    => "issue-{$i->id}",
                'title' => trim(($i->key ?? '') . ' — ' . $i->summary),
                'start' => $start,
                'end'   => $end,
                'allDay' => $allDay,
                'url'   => route('issues.show', ['project' => $this->project->id, 'issue' => $i->key]),

                // Use tier color for the event; border matches fill
                'backgroundColor' => $color,
                'borderColor'     => $color,

                // Give JS enough to render a chip and CSS rail
                'extendedProps' => [
                    'tier'      => $tier,
                    'typeColor' => $color,
                    'typeIcon'  => $icon,
                ],
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
