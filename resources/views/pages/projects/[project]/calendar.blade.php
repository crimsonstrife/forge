<?php

use App\Models\Project;
use Illuminate\View\View;
use function Laravel\Folio\{name, middleware, render};

name('projects.calendar');
middleware(['auth','verified']);

render(function (View $view, Project $project): void {
    $view->with('project', $project);
});
?>

<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">Calendar — {{ $project->name }}</h2>
    </x-slot>

    <div class="container py-3">
        <livewire:projects.project-calendar :project="$project"/>
        <div class="mt-3">
            <a class="btn btn-outline-secondary btn-sm" href="{{ route('projects.calendar.ics', $project) }}">
                Export iCal (.ics)
            </a>
        </div>
    </div>
</x-app-layout>
