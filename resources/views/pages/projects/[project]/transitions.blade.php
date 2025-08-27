<?php
use function Laravel\Folio\{name, middleware};

name('projects.transitions');
middleware(['auth','verified']);
?>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
            {{ $project->key }} — Status Transitions
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-6xl">
            <livewire:projects.project-transitions :project="$project" />
        </div>
    </div>
</x-app-layout>
