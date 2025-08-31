<?php
use App\Models\Project;
use function Laravel\Folio\{name, middleware};

name('issues.create');
middleware(['auth','verified']);
?>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
            {{ $project->key }} — New Issue
        </h2>
        <a href="{{ route('projects.show', ['project' => $project]) }}" class="text-sm text-primary-600 hover:underline">Back to project</a>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-3xl">
            <livewire:issues.create-issue-form :project="$project" />
        </div>
    </div>
</x-app-layout>
