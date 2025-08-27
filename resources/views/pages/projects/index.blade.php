<?php

use App\Models\Project;
use Illuminate\View\View;

use function Laravel\Folio\{name, middleware, render};

name('projects.index');
middleware(['auth','verified']);

render(function (View $view) {
    $u = auth()->user();

    $projects = Project::query()
        ->visibleTo($u)
        ->latest()
        ->paginate(12)
        ->withQueryString();

    return $view->with('projects', $projects);
});
?>

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Projects</h2>
            @can('create', App\Models\Project::class)
                <a href="{{ route('projects.create') }}"
                   class="inline-flex items-center rounded-lg px-3 py-2 bg-primary-600 text-white hover:bg-primary-700">
                    New Project
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($projects as $project)
                    <a href="{{ route('projects.show', ['project' => $project]) }}"
                       class="rounded-xl border border-gray-200/60 dark:border-gray-700/60 p-4 hover:bg-gray-50 dark:hover:bg-gray-800">
                        <div class="flex items-center justify-between">
                            <div class="text-sm uppercase tracking-wide text-gray-500">{{ $project->key }}</div>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700">
                                {{ $project->stage->value ?? 'planning' }}
                            </span>
                        </div>
                        <div class="mt-2 font-semibold text-gray-900 dark:text-gray-100">{{ $project->name }}</div>
                        <div class="mt-1 text-sm text-gray-600 line-clamp-2">{{ $project->description }}</div>
                    </a>
                @empty
                    <div class="col-span-full text-center text-gray-500">No projects yet.</div>
                @endforelse
            </div>

            {{ $projects->onEachSide(1)->links() }}
        </div>
    </div>
</x-app-layout>
