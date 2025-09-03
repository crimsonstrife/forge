<?php
use function Laravel\Folio\{name, middleware};
name('projects.create');
middleware(['auth','verified']);
?>
<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">{{ __('Create Project') }}</h2>
    </x-slot>

    <div class="py-4">
        <div class="container mx-auto px-3" style="max-width: 720px">
            <livewire:projects.create-project-form />
        </div>
    </div>
</x-app-layout>
