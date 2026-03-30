<?php

use App\Models\Organization;
use App\Services\Dashboards\SharedDashboardService;
use Illuminate\Support\Facades\Gate;

use function Laravel\Folio\{name, middleware, render};

name('organizations.show');
middleware(['auth', 'verified']);

render(function (\Illuminate\View\View $view, Organization $organization) {
    Gate::authorize('view', $organization);

    return $view->with(
        ['organization' => $organization] + app(SharedDashboardService::class)->forOrganization(auth()->user(), $organization)
    );
});
?>

<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h2 class="h4 mb-0">{{ $organization->name }}</h2>
                <div class="small text-body-secondary">{{ __('Organization dashboard') }}</div>
            </div>
            <div class="d-flex gap-2">
                @can('update', $organization)
                    <a class="btn btn-sm btn-primary"
                       href="{{ route('organizations.edit', ['organization' => $organization]) }}">Edit</a>
                @endcan
                <a class="btn btn-sm btn-outline-secondary"
                   href="{{ route('organizations.index') }}">Back</a>
            </div>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="alert alert-success my-3">{{ session('status') }}</div>
    @endif

    <div class="py-4">
        <div class="container mx-auto py-4 d-flex flex-column gap-4">
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3">
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="text-uppercase small text-body-secondary">{{ __('Projects') }}</div>
                            <div class="display-6 mb-0">{{ $projectCount }}</div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="text-uppercase small text-body-secondary">{{ __('Teams') }}</div>
                            <div class="display-6 mb-0">{{ $teams->count() }}</div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="text-uppercase small text-body-secondary">{{ __('Open issues') }}</div>
                            <div class="display-6 mb-0">{{ $openIssuesCount }}</div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="text-uppercase small text-body-secondary">{{ __('Active goals') }}</div>
                            <div class="display-6 mb-0">{{ $activeGoalsCount }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <section class="col-lg-8 d-flex flex-column gap-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h3 class="h6 mb-0">{{ __('Shared projects') }}</h3>
                                <a class="small text-decoration-underline" href="{{ route('projects.index') }}">{{ __('Browse all') }}</a>
                            </div>

                            @if($projects->isEmpty())
                                <p class="small text-body-secondary mb-0">{{ __('No shared projects are attached to this organization yet.') }}</p>
                            @else
                                <div class="row row-cols-1 row-cols-md-2 g-3">
                                    @foreach($projects as $project)
                                        <div class="col">
                                            @can('view', $project)
                                                <a href="{{ route('projects.show', ['project' => $project]) }}" class="card h-100 text-reset text-decoration-none border">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <div class="small text-body-secondary">{{ $project->key }}</div>
                                                            <span class="badge bg-body-tertiary text-body">{{ $project->open_issues_count }} {{ __('open') }}</span>
                                                        </div>
                                                        <div class="fw-semibold mt-2">{{ $project->name }}</div>
                                                        @if($project->teams->isNotEmpty())
                                                            <div class="small text-body-secondary mt-2">
                                                                {{ __('Teams') }}: {{ $project->teams->pluck('name')->implode(', ') }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </a>
                                            @else
                                                <div class="card h-100 border">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <div class="small text-body-secondary">{{ $project->key }}</div>
                                                            <span class="badge bg-body-tertiary text-body">{{ $project->open_issues_count }} {{ __('open') }}</span>
                                                        </div>
                                                        <div class="fw-semibold mt-2">{{ $project->name }}</div>
                                                        @if($project->teams->isNotEmpty())
                                                            <div class="small text-body-secondary mt-2">
                                                                {{ __('Teams') }}: {{ $project->teams->pluck('name')->implode(', ') }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endcan
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h3 class="h6 mb-3">{{ __('Due soon across the organization') }}</h3>

                            @if($dueSoon->isEmpty())
                                <p class="small text-body-secondary mb-0">{{ __('Nothing due in the next two weeks.') }}</p>
                            @else
                                <div class="list-group list-group-flush">
                                    @foreach($dueSoon as $issue)
                                        @can('view', $issue)
                                            <a href="{{ route('issues.show', ['project' => $issue->project, 'issue' => $issue]) }}"
                                               class="list-group-item list-group-item-action px-0 d-flex align-items-center justify-content-between gap-3">
                                                <div>
                                                    <div class="fw-medium">{{ $issue->key }} — {{ $issue->summary }}</div>
                                                    <div class="small text-body-secondary">{{ $issue->project?->name }} • {{ $issue->status?->name ?? __('Open') }}</div>
                                                </div>
                                                <div class="small text-body-secondary text-nowrap">{{ $issue->due_at?->toFormattedDateString() }}</div>
                                            </a>
                                        @else
                                            <div class="list-group-item px-0 d-flex align-items-center justify-content-between gap-3">
                                                <div>
                                                    <div class="fw-medium">{{ $issue->key }} — {{ $issue->summary }}</div>
                                                    <div class="small text-body-secondary">{{ $issue->project?->name }} • {{ $issue->status?->name ?? __('Open') }}</div>
                                                </div>
                                                <div class="small text-body-secondary text-nowrap">{{ $issue->due_at?->toFormattedDateString() }}</div>
                                            </div>
                                        @endcan
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h3 class="h6 mb-3">{{ __('Recent activity') }}</h3>

                            @if($recentActivity->isEmpty())
                                <p class="small text-body-secondary mb-0">{{ __('No shared activity yet.') }}</p>
                            @else
                                <div class="d-flex flex-column gap-3">
                                    @foreach($recentActivity as $activity)
                                        <div class="border rounded p-3">
                                            <div class="small">
                                                <span class="fw-medium">{{ $activity['actor_name'] }}</span>
                                                <span class="text-body-secondary">{{ strtolower($activity['verb']) }}</span>
                                                @if($activity['target_url'])
                                                    <a href="{{ $activity['target_url'] }}" class="fw-medium text-decoration-underline">
                                                        {{ $activity['target_label'] }}
                                                    </a>
                                                @else
                                                    <span class="fw-medium">{{ $activity['target_label'] }}</span>
                                                @endif
                                            </div>
                                            <div class="small text-body-secondary mt-1">{{ $activity['ago'] }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </section>

                <section class="col-lg-4 d-flex flex-column gap-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h3 class="h6 mb-3">{{ __('Teams in this organization') }}</h3>

                            @if($teams->isEmpty())
                                <p class="small text-body-secondary mb-0">{{ __('No teams are attached to the visible projects in this organization.') }}</p>
                            @else
                                <div class="list-group list-group-flush">
                                    @foreach($teams as $team)
                                        @can('view', $team)
                                            <a href="{{ route('teams.dashboard', ['team' => $team]) }}"
                                               class="list-group-item list-group-item-action px-0 d-flex align-items-center justify-content-between">
                                                <span class="fw-medium">{{ $team->name }}</span>
                                                <span class="badge bg-body-tertiary text-body">{{ $team->scoped_projects_count }}</span>
                                            </a>
                                        @else
                                            <div class="list-group-item px-0 d-flex align-items-center justify-content-between">
                                                <span class="fw-medium">{{ $team->name }}</span>
                                                <span class="badge bg-body-tertiary text-body">{{ $team->scoped_projects_count }}</span>
                                            </div>
                                        @endcan
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h3 class="h6 mb-0">{{ __('Organization goals') }}</h3>
                                <a class="small text-decoration-underline" href="{{ route('goals.index') }}">{{ __('Browse goals') }}</a>
                            </div>

                            @if($goals->isEmpty())
                                <p class="small text-body-secondary mb-0">{{ __('No organization goals yet.') }}</p>
                            @else
                                <div class="list-group list-group-flush">
                                    @foreach($goals as $goal)
                                        @can('view', $goal)
                                            <a href="{{ route('goals.show', ['goal' => $goal]) }}"
                                               class="list-group-item list-group-item-action px-0">
                                                <div class="d-flex align-items-center justify-content-between gap-2">
                                                    <span class="fw-medium">{{ $goal->name }}</span>
                                                    <span class="badge bg-body-tertiary text-body">{{ (int) $goal->progress }}%</span>
                                                </div>
                                                <div class="small text-body-secondary mt-1">
                                                    {{ str($goal->status->value)->replace('_', ' ')->title() }}
                                                    @if($goal->due_date)
                                                        • {{ __('Due') }} {{ $goal->due_date->toFormattedDateString() }}
                                                    @endif
                                                </div>
                                            </a>
                                        @else
                                            <div class="list-group-item px-0">
                                                <div class="d-flex align-items-center justify-content-between gap-2">
                                                    <span class="fw-medium">{{ $goal->name }}</span>
                                                    <span class="badge bg-body-tertiary text-body">{{ (int) $goal->progress }}%</span>
                                                </div>
                                                <div class="small text-body-secondary mt-1">
                                                    {{ str($goal->status->value)->replace('_', ' ')->title() }}
                                                    @if($goal->due_date)
                                                        • {{ __('Due') }} {{ $goal->due_date->toFormattedDateString() }}
                                                    @endif
                                                </div>
                                            </div>
                                        @endcan
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
