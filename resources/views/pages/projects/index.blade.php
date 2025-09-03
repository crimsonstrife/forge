<?php

use App\Models\Project;
use Illuminate\View\View;
use function Laravel\Folio\{name, middleware, render};

name('projects.index');
middleware(['auth','verified']);

render(function (View $view) {
    $u = auth()->user();

    $projects = Project::query()
        ->visibleTo($u)
        ->latest()
        ->paginate(12)
        ->withQueryString();

    return $view->with('projects', $projects);
});
?>

<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h2 class="h4 mb-0">{{ __('Projects') }}</h2>
            @can('create', App\Models\Project::class)
                <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm">
                    {{ __('New Project') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container d-flex flex-column gap-3">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3">
                @forelse($projects as $project)
                    <div class="col">
                        <a href="{{ route('projects.show', ['project' => $project]) }}" class="card h-100 text-reset text-decoration-none">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="text-uppercase small text-body-secondary">{{ $project->key }}</div>
                                    <span class="badge bg-body-tertiary text-body text-uppercase">{{ $project->stage->value ?? 'planning' }}</span>
                                </div>
                                <div class="fw-semibold mt-2">{{ $project->name }}</div>
                                <div class="small text-body-secondary mt-1">{{ $project->description }}</div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-12 text-center text-body-secondary">{{ __('No projects yet.') }}</div>
                @endforelse
            </div>

            <div>
                {{ $projects->onEachSide(1)->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

