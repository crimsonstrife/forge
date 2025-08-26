<?php
use App\Models\Project;
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
        <div class="mx-auto max-w-5xl">
            <div class="rounded-xl border border-dashed p-6 text-gray-500">
                Transitions UI coming soon.
            </div>
        </div>
    </div>
</x-app-layout>
