<?php

use App\Models\Project;
use Illuminate\View\View;
use function Laravel\Folio\{name, middleware, render};

name('projects.timeline');
middleware(['auth','verified']);

render(function (View $view, Project $project): void {
    $view->with('project', $project);
});
?>

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
                    {{ $project->key }} — Timeline
                </h2>
                <a href="{{ route('projects.show', ['project' => $project]) }}" class="text-sm text-primary-600 hover:underline">Back to project</a>
                <div class="mt-1 text-sm text-gray-500 dark:text-gray-400 flex flex-wrap gap-3">
                    <span class="inline-flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-xs">
                            {{ ucfirst($project->stage->value ?? 'planning') }}
                        </span>
                    </span>
                    @if ($project->organization?->name)
                        <span>Org: {{ $project->organization->name }}</span>
                    @endif
                    @if ($project->teams->isNotEmpty())
                        <span>Teams:
                            {{ $project->teams->pluck('name')->implode(', ') }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('projects.timeline', ['project' => $project]) }}" class="inline-flex items-center rounded-lg px-3 py-2 border">Timeline</a>
                <a href="{{ route('projects.calendar', ['project' => $project]) }}" class="inline-flex items-center rounded-lg px-3 py-2 border">Calendar</a>
                <a href="{{ route('projects.board', ['project' => $project]) }}" class="inline-flex items-center rounded-lg px-3 py-2 border">Kanban</a>
                <a href="{{ route('projects.scrum', ['project' => $project]) }}" class="inline-flex items-center rounded-lg px-3 py-2 border">Sprint</a>
                @can('issues.create')
                    <a href="{{ route('issues.create', ['project' => $project]) }}" class="inline-flex items-center rounded-lg px-3 py-2 border">New issue</a>
                @endcan
                @can('update', $project)
                    <a href="{{ route('projects.edit', ['project' => $project]) }}"
                       class="inline-flex items-center rounded-lg px-3 py-2 border">
                        Edit Project
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6">
        <livewire:projects.project-timeline :project="$project"/>
        </div>
    </div>
</x-app-layout>
