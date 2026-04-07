<?php

use App\Models\Project;
use Illuminate\View\View;

use function Laravel\Folio\{middleware, name, render};

name('projects.roadmap');
middleware(['auth', 'verified']);

render(function (View $view, Project $project): void {
    $view->with('project', $project);
});
?>

<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
            <div>
                <h2 class="h4 mb-1">{{ $project->key }} — {{ $project->name }}</h2>
                <div class="small text-body-secondary d-flex flex-wrap gap-3">
                    <span class="badge bg-body-tertiary text-body">
                        {{ ucfirst($project->stage->value ?? 'planning') }}
                    </span>
                    @if ($project->organization?->name)
                        <span>Org: {{ $project->organization->name }}</span>
                    @endif
                    @if ($project->teams->isNotEmpty())
                        <span>Teams: {{ $project->teams->pluck('name')->implode(', ') }}</span>
                    @endif
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                @can('issues.create', $project)
                    <a href="{{ route('issues.create', ['project' => $project]) }}" class="btn btn-outline-primary btn-sm">New issue</a>
                @endcan
                @can('view', $project)
                    <a href="{{ route('projects.milestones.index', $project) }}" class="btn btn-outline-secondary btn-sm">Milestones</a>
                @endcan
                @can('update', $project)
                    <a href="{{ route('projects.edit', ['project' => $project]) }}" class="btn btn-secondary btn-sm">Edit Project</a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container py-4">
            <x-projects.page-card :project="$project">
                <livewire:projects.project-roadmap :project="$project" />
            </x-projects.page-card>
        </div>
    </div>
</x-app-layout>
