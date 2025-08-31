<?php

use App\Models\Project;
use Illuminate\View\View;
use function Laravel\Folio\{name, middleware, render};

name('projects.timeline');
middleware(['auth','verified']);

render(function (View $view, Project $project): void {
    $view->with('project', $project);
});
?>

<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">Timeline — {{ $project->name }}</h2>
        <a href="{{ route('projects.show', ['project' => $project]) }}" class="text-sm text-primary-600 hover:underline">Back to project</a>
    </x-slot>

    <div class="container py-3">
        <livewire:projects.project-timeline :project="$project"/>
    </div>
</x-app-layout>
