<?php

use function Laravel\Folio\{name, middleware};

name('projects.board');
middleware(['auth','verified']);

?>
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
                {{ $project->key }} — Kanban
            </h2>
            <a href="{{ route('projects.show', ['project' => $project]) }}" class="text-sm text-primary-600 hover:underline">Back to project</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl">
            <livewire:projects.kanban-board :project="$project" />
        </div>
    </div>
</x-app-layout>
