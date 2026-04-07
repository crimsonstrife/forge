<?php
use App\Models\Project;
use App\Support\Codex\CodexConnection;
use Illuminate\View\View;

use function Laravel\Folio\{name, middleware, render};

name('projects.edit');
middleware(['auth','verified']);

render(function (View $view, Project $project) {
    $project->loadMissing(['repositoryLink.repository']);
    $codex = app(CodexConnection::class);

    return $view->with(compact('project', 'codex'));
});
?>
<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">{{ __('Edit') }} {{ $project->key }}</h2>
    </x-slot>

    <div class="container py-4">
        <div class="row g-4">
            <div class="container px-3">
                    <livewire:projects.edit-project-form :project="$project" />

                    @if($codex->enabled())
                        <div class="mt-4">
                            <livewire:projects.connect-codex-workspace :project="$project" />
                        </div>
                    @endif
            </div>
        </div>
    </div>
</x-app-layout>
