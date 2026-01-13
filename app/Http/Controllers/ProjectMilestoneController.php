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
