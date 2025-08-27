<?php
use App\Models\Project;
use App\Models\Issue;

use function Laravel\Folio\{name, middleware};

name('issues.show');
middleware(['auth','verified']);
?>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
            {{ $project->key }} — Issue
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-4xl space-y-4">
            <div class="rounded-xl border border-gray-200/60 dark:border-gray-700/60 bg-white dark:bg-gray-800 p-6">
                <h3 class="text-lg font-semibold">{{ $issue->summary }}</h3>
                <p class="mt-2 text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $issue->description }}</p>
            </div>
            <div class="text-sm text-gray-500">
                Updated {{ $issue->updated_at?->diffForHumans() }}
            </div>
        </div>
    </div>
</x-app-layout>
