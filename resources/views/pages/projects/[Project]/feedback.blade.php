<?php

use function Laravel\Folio\{middleware, name};

name('projects.feedback');
middleware(['auth', 'verified']);

?>
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
            <div>
                <h2 class="h4 mb-1">{{ $project->key }} — {{ $project->name }}</h2>
                <div class="small text-body-secondary d-flex flex-wrap gap-3">
                    <span class="badge bg-body-tertiary text-body">
                        {{ ucfirst($project->stage->value ?? 'planning') }}
                    </span>
                    @if ($project->organization?->name)
                        <span>Org: {{ $project->organization->name }}</span>
                    @endif
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container py-4">
            <x-projects.page-card :project="$project">
                <livewire:projects.project-feedback-posts :project="$project" />
            </x-projects.page-card>
        </div>
    </div>
</x-app-layout>
