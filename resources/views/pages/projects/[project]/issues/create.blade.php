<?php
use App\Models\Project;
use App\Models\Issue;
use Illuminate\View\View;
use function Laravel\Folio\{name, middleware, render};

name('issues.create');
middleware(['auth','verified']);

/**
 * Keep this closure simple: prepare $parent (optional) and always return.
 * Avoid abort() here; let the Livewire component handle authorization/validation.
 */
render(function (View $view, Project $project) {
    $parent = null;

    $parentKey = request()->string('parent')->trim()->toString();
    if ($parentKey !== '') {
        $parent = Issue::query()
            ->where('project_id', $project->getKey())
            ->where('key', $parentKey)
            ->select(['id', 'key', 'summary', 'project_id'])
            ->first();
    }

    return $view->with(compact('project', 'parent'));
});
?>
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
                Create issue
            </h2>
            <a href="{{ route('projects.show', ['project' => $project]) }}"
               class="text-sm text-primary-600 hover:underline">Back to project</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-3xl">
            <livewire:issues.create-issue-form :project-id="$project->getKey()" :parent-id="$parent?->getKey()"/>
        </div>
    </div>
</x-app-layout>

