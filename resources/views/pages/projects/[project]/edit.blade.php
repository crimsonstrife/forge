<?php
use App\Models\Project;
use Illuminate\View\View;

use function Laravel\Folio\{name, middleware, render};

name('projects.edit');
middleware(['auth','verified']);

render(function (View $view, Project $project) {
    $project->loadMissing(['repositoryLink.repository']);
    return $view->with(compact('project'));
});
?>
<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">{{ __('Edit') }} {{ $project->key }}</h2>
    </x-slot>

    <div class="container py-4">
        <div class="row g-4">
            <div class="container mx-auto px-3" style="max-width: 980px">
                    <livewire:projects.edit-project-form :project="$project" />
            </div>
        </div>
        <div class="row g-4">
            <div class="container mx-auto px-3" style="max-width: 980px">
                @include('partials.projects.repository-panel', ['project' => $project])
            </div>
        </div>
    </div>
</x-app-layout>
