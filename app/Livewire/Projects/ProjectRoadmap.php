<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use App\Services\Projects\RoadmapService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;

final class ProjectRoadmap extends Component
{
    public Project $project;

    #[Url(as: 'view')]
    public string $groupBy = 'milestone';

    #[Url(as: 'milestone')]
    public ?string $selectedMilestoneId = null;

    public function mount(Project $project): void
    {
        $this->authorize('view', $project);
        $this->project = $project;

        if ($this->selectedMilestoneId === null) {
            $this->selectedMilestoneId = $project->milestones()
                ->releases()
                ->orderByRaw('CASE WHEN due_at IS NULL THEN 1 ELSE 0 END')
                ->orderBy('due_at')
                ->value('id')
                ?? $project->milestones()
                    ->orderByRaw('CASE WHEN due_at IS NULL THEN 1 ELSE 0 END')
                    ->orderBy('due_at')
                    ->value('id');
        }
    }

    public function render()
    {
        $payload = app(RoadmapService::class)->build(
            project: $this->project,
            groupBy: $this->groupBy,
            selectedMilestoneId: $this->selectedMilestoneId
        );

        return view('livewire.projects.project-roadmap', $payload);
    }

    public function authorize($ability, mixed $arguments = []): void
    {
        if (Gate::denies($ability, $arguments)) {
            throw new AuthorizationException;
        }
    }
}
