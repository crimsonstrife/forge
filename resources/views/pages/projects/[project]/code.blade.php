<?php

use App\Models\Project;
use Illuminate\View\View;

use function Laravel\Folio\{name, middleware, render};

name('projects.code');
middleware(['auth', 'verified']);

render(function (View $view, Project $project) {
    // Keep it light but ensure repo link is fresh
    $project->loadMissing([
        'organization:id,name',
        'repositoryLink.repository', // needed by the panel
    ]);

    return $view->with(compact('project'));
});
?>

<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
            <div>
                <h2 class="h4 mb-1">{{ $project->key }} — {{ $project->name }}</h2>
                <div class="small text-body-secondary d-flex flex-wrap gap-3">
                    @if ($project->organization?->name)
                        <span>Org: {{ $project->organization->name }}</span>
                    @endif
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                @can('update', $project)
                    <a href="{{ route('projects.edit', ['project' => $project]) }}" class="btn btn-secondary btn-sm">
                        {{ __('Edit Project') }}
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container">
            <x-projects.page-card :project="$project">
                {{-- Primary content for the Code tab --}}
                <div class="row g-4">
                    <div class="col-lg-8 d-flex flex-column gap-4">
                        {{-- Repository connect/status panel --}}
                        @include('partials.projects.repository-panel', ['project' => $project])

                        {{-- Room for future code integrations (webhooks, deployments, pipelines, etc.) --}}
                        {{-- @include('partials.projects.webhooks-panel', ['project' => $project]) --}}
                    </div>

                    <aside class="col-lg-4 d-flex flex-column gap-4">
                        {{-- quick links or docs related to repos --}}
                        <div class="card">
                            <div class="card-body small">
                                <div class="fw-semibold mb-1">{{ __('Tips') }}</div>
                                <ul class="mb-0 ps-3">
                                    <li>{{ __('Connect a repository to import issues (open & closed).') }}</li>
                                    <li>{{ __('Assignees/reporters sync only if users linked their GitHub account.') }}</li>
                                    <li>{{ __('Projects with existing issues cannot connect to avoid conflicts.') }}</li>
                                </ul>
                            </div>
                        </div>
                    </aside>
                </div>
            </x-projects.page-card>
        </div>
    </div>
</x-app-layout>
