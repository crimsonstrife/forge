<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\MilestoneState;
use App\Enums\MilestoneType;
use App\Http\Requests\Milestones\StoreMilestoneRequest;
use App\Http\Requests\Milestones\UpdateMilestoneRequest;
use App\Models\Milestone;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ProjectMilestoneController extends Controller
{
    public function index(Request $request, Project $project): View
    {
        $this->authorize('view', $project);

        $type = (string) $request->query('type', 'all');
        $state = (string) $request->query('state', 'all');
        $search = trim((string) $request->query('q', ''));

        $milestones = $project->milestones()
            ->withCount(['issues', 'sprints'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->when($type !== 'all', fn ($q) => $q->where('type', $type))
            ->when($state !== 'all', fn ($q) => $q->where('state', $state))
            ->orderByRaw('coalesce(due_at, starts_at) asc')
            ->paginate(15)
            ->withQueryString();

        return view('projects.milestones.index', [
            'project' => $project,
            'milestones' => $milestones,
            'filters' => [
                'type' => $type,
                'state' => $state,
                'q' => $search,
            ],
            'typeOptions' => $this->enumOptions(MilestoneType::cases()),
            'stateOptions' => $this->enumOptions(MilestoneState::cases()),
        ]);
    }

    public function create(Project $project): View
    {
        $this->authorize('update', $project);

        return view('projects.milestones.create', [
            'project' => $project,
            'typeOptions' => $this->enumOptions(MilestoneType::cases()),
            'stateOptions' => $this->enumOptions(MilestoneState::cases()),
        ]);
    }

    public function store(StoreMilestoneRequest $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        $project->milestones()->create($request->validated());

        return redirect()
            ->route('projects.milestones.index', $project)
            ->with('status', 'Milestone created.');
    }

    public function edit(Project $project, Milestone $milestone): View
    {
        $this->authorize('update', $project);

        return view('projects.milestones.edit', [
            'project' => $project,
            'milestone' => $milestone,
            'typeOptions' => $this->enumOptions(MilestoneType::cases()),
            'stateOptions' => $this->enumOptions(MilestoneState::cases()),
        ]);
    }

    public function update(UpdateMilestoneRequest $request, Project $project, Milestone $milestone): RedirectResponse
    {
        $this->authorize('update', $project);

        $milestone->update($request->validated());

        return redirect()
            ->route('projects.milestones.index', $project)
            ->with('status', 'Milestone updated.');
    }

    public function destroy(Project $project, Milestone $milestone): RedirectResponse
    {
        $this->authorize('update', $project);

        $milestone->delete();

        return redirect()
            ->route('projects.milestones.index', $project)
            ->with('status', 'Milestone deleted.');
    }

    public function show(Request $request, Project $project, Milestone $milestone): View
    {
        $this->authorize('view', $project);

        // If you also have a MilestonePolicy, keep this. If not, remove it.
        // $this->authorize('view', $milestone);

        $milestone->loadMissing('project:id,name,key');

        $filters = [
            'q' => (string) $request->query('q', ''),
            'status' => (string) $request->query('status', 'all'), // all|open|done|overdue
        ];

        $issuesQuery = $milestone->issues()
            ->with([
                'status:id,name,color,is_done',
                'type:id,key,name',
                'priority:id,name',
                'assignee:id,name,profile_photo_path',
                'project:id,key',
            ])
            ->orderByRaw('case when due_at is null then 1 else 0 end')
            ->orderBy('due_at')
            ->orderBy('number');

        if ($filters['q'] !== '') {
            $q = $filters['q'];

            $issuesQuery->where(function ($qq) use ($q) {
                $qq->where('summary', 'like', '%' . $q . '%')
                    ->orWhere('key', 'like', '%' . $q . '%');
            });
        }

        if ($filters['status'] === 'open') {
            $issuesQuery->whereHas('status', fn ($q) => $q->where('is_done', false));
        } elseif ($filters['status'] === 'done') {
            $issuesQuery->whereHas('status', fn ($q) => $q->where('is_done', true));
        } elseif ($filters['status'] === 'overdue') {
            $issuesQuery
                ->whereNotNull('due_at')
                ->where('due_at', '<', now())
                ->whereHas('status', fn ($q) => $q->where('is_done', false));
        }

        $issues = $issuesQuery->paginate(25)->withQueryString();

        // Summary stats (kept simple + reliable)
        $totalIssues = $milestone->issues()->count();
        $doneIssues = $milestone->issues()
            ->whereHas('status', fn ($q) => $q->where('is_done', true))
            ->count();

        $overdueIssues = $milestone->issues()
            ->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->whereHas('status', fn ($q) => $q->where('is_done', false))
            ->count();

        $pointsTotal = (int) $milestone->issues()->sum('story_points');
        $pointsDone = (int) $milestone->issues()
            ->whereHas('status', fn ($q) => $q->where('is_done', true))
            ->sum('story_points');

        $estimateTotalMinutes = (int) $milestone->issues()->sum('estimate_minutes');
        $estimateDoneMinutes = (int) $milestone->issues()
            ->whereHas('status', fn ($q) => $q->where('is_done', true))
            ->sum('estimate_minutes');

        $sprints = $milestone->sprints()
            ->withCount('issues')
            ->orderByRaw('case when start_date is null then 1 else 0 end')
            ->orderBy('start_date')
            ->orderBy('name')
            ->get(['id', 'name', 'state', 'start_date', 'end_date']);

        return view('projects.milestones.show', [
            'project' => $project,
            'milestone' => $milestone,
            'issues' => $issues,
            'sprints' => $sprints,
            'filters' => $filters,
            'totalIssues' => $totalIssues,
            'doneIssues' => $doneIssues,
            'overdueIssues' => $overdueIssues,
            'pointsTotal' => $pointsTotal,
            'pointsDone' => $pointsDone,
            'estimateTotalMinutes' => $estimateTotalMinutes,
            'estimateDoneMinutes' => $estimateDoneMinutes,
        ]);
    }

    /**
     * @param  array<int, \BackedEnum>  $cases
     * @return array<string, string>
     */
    private function enumOptions(array $cases): array
    {
        $options = [];

        foreach ($cases as $case) {
            $label = method_exists($case, 'getLabel') ? (string) $case->getLabel() : $case->name;
            $options[$case->value] = $label;
        }

        return $options;
    }
}
