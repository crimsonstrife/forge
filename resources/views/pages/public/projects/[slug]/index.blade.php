<?php
use function Laravel\Folio\{name, render};
use Illuminate\View\View;
use App\Models\Project;

name('public.projects.show');

render(function (View $view, string $slug) {
    $project = Project::where('public_slug', $slug)->firstOrFail();
    return $view->with('project', $project);
});
?>

<x-guest-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">Public Tracker: {{ $project->name }}</h1>
    </x-slot>

    <livewire:public.project-kanban :project="$project" />
</x-guest-layout>
