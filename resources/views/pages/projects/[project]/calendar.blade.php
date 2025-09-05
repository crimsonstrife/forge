<?php

use App\Models\Project;
use Illuminate\View\View;

use function Laravel\Folio\{name, middleware, render};

name('projects.calendar');
middleware(['auth','verified']);

render(function (View $view, Project $project): void {
    $view->with('project', $project);
});
?>

<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
            <div>
                <h2 class="h4 mb-1">{{ $project->key }} — {{ __('Calendar') }}</h2>
                <a href="{{ route('projects.show', ['project' => $project]) }}" class="small text-decoration-underline">{{ __('Back to project') }}</a>
                <div class="small text-body-secondary d-flex flex-wrap gap-3 mt-1">
                    <span class="badge bg-body-tertiary text-body">{{ ucfirst($project->stage->value ?? 'planning') }}</span>
                    @if ($project->organization?->name) <span>Org: {{ $project->organization->name }}</span>@endif
                    @if ($project->teams->isNotEmpty()) <span>Teams: {{ $project->teams->pluck('name')->implode(', ') }}</span>@endif
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="nav nav-tabs mb-3">
                    <a href="{{ route('projects.show', ['project' => $project]) }}"
                       class="nav-link {{ request()->routeIs('projects.show') ? 'active' : '' }}">
                        Overview
                    </a>
                    <a href="{{ route('projects.board', ['project' => $project]) }}"
                       class="nav-link {{ request()->routeIs('projects.board') ? 'active' : '' }}">
                        Board
                    </a>
                    <a href="{{ route('projects.scrum', ['project' => $project]) }}"
                       class="nav-link {{ request()->routeIs('projects.scrum') ? 'active' : '' }}">
                        Scrum
                    </a>
                    <a href="{{ route('projects.calendar', ['project' => $project]) }}"
                       class="nav-link {{ request()->routeIs('projects.calendar') ? 'active' : '' }}">
                        Calendar
                    </a>
                    <a href="{{ route('projects.timeline', ['project' => $project]) }}"
                       class="nav-link {{ request()->routeIs('projects.timeline') ? 'active' : '' }}">
                        Timeline
                    </a>
                    <a href="{{ route('projects.transitions', ['project' => $project]) }}"
                       class="nav-link {{ request()->routeIs('projects.transitions') ? 'active' : '' }}">
                        Transitions
                    </a>
                </div>
                @can('issues.create')
                    <a href="{{ route('issues.create', ['project' => $project]) }}" class="btn btn-outline-primary btn-sm">New issue</a>
                @endcan
                @can('update', $project)
                    <a href="{{ route('projects.edit', ['project' => $project]) }}" class="btn btn-secondary btn-sm">Edit Project</a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container d-flex flex-column gap-2">
            <livewire:projects.project-calendar :project="$project"/>
            <div>
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('projects.calendar.ics', $project) }}">Export iCal (.ics)</a>
            </div>
        </div>
    </div>
</x-app-layout>

