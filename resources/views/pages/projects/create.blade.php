<?php

use function Laravel\Folio\name;

name('projects.create');

?>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Create Project</h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-3xl">
            <livewire:projects.create-project-form />
        </div>
    </div>
</x-app-layout>
