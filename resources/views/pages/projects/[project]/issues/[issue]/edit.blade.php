<?php
use App\Models\Project;
use App\Models\Issue;

use function Laravel\Folio\{name, middleware};

name('issues.edit');
middleware(['auth','verified']);
?>

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
                {{ $project->key }} — Edit {{ $issue->key }}
            </h2>
            <a href="{{ route('issues.show', ['project' => $project, 'issue' => $issue]) }}"
               class="rounded-lg px-3 py-2 border border-gray-300 dark:border-gray-700">
                Cancel
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-3xl">
            <livewire:issues.update-issue-form :project="$project" :issue="$issue" />
        </div>
    </div>
</x-app-layout>
