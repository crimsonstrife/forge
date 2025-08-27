<?php

use App\Models\Project;
use App\Models\Issue;
use Illuminate\View\View;

use function Laravel\Folio\{name, middleware, render};

name('issues.index');
middleware(['auth','verified']);

render(function (View $view, Project $project) {
    $issues = Issue::query()
        ->where('project_id', $project->id)
        ->latest()
        ->paginate(20)
        ->withQueryString();

    return $view->with(compact('project', 'issues'));
});
?>

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
                {{ $project->key }} — Issues
            </h2>
            @can('issues.create')
                <a href="{{ route('issues.create', ['project' => $project]) }}"
                   class="inline-flex items-center rounded-lg px-3 py-2 bg-primary-600 text-white hover:bg-primary-700">
                    New Issue
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-4">
            <div class="divide-y divide-gray-200/60 dark:divide-gray-700/60 rounded-xl border border-gray-200/60 dark:border-gray-700/60 bg-white dark:bg-gray-800">
                @forelse($issues as $issue)
                    <a class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-800/60"
                       href="{{ route('issues.show', ['project'=>$project, 'issue'=>$issue]) }}">
                        <div class="flex items-center justify-between">
                            <div class="font-medium">{{ $issue->summary }}</div>
                            <div class="text-xs text-gray-500">{{ $issue->updated_at?->diffForHumans() }}</div>
                        </div>
                        <div class="text-sm text-gray-600 line-clamp-2">{{ $issue->description }}</div>
                    </a>
                @empty
                    <div class="p-6 text-center text-gray-500">No issues yet.</div>
                @endforelse
            </div>

            {{ $issues->onEachSide(1)->links() }}
        </div>
    </div>
</x-app-layout>
