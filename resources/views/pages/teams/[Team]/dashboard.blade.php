<?php

use App\Models\Team;
use App\Services\Dashboards\SharedDashboardService;
use Illuminate\Support\Facades\Gate;

use function Laravel\Folio\{name, middleware, render};

name('teams.dashboard');
middleware(['auth', 'verified']);

render(function (\Illuminate\View\View $view, Team $team) {
    Gate::authorize('view', $team);

    return $view->with(
        ['team' => $team] + app(SharedDashboardService::class)->forTeam(auth()->user(), $team)
    );
});
?>

<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h2 class="h4 mb-0">{{ $team->name }}</h2>
                <div class="small text-body-secondary">{{ __('Team dashboard') }}</div>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('teams.show', ['team' => $team]) }}">
                    {{ __('Team Settings') }}
                </a>
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('dashboard') }}">
                    {{ __('Main Dashboard') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container mx-auto py-4 d-flex flex-column gap-4">
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3">
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="text-uppercase small text-body-secondary">{{ __('Members') }}</div>
                            <div class="display-6 mb-0">{{ $members->count() }}</div>
                        </div>
                    </div>
                </div>
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
                                <p class="small text-body-secondary mb-0">{{ __('This team does not have any shared projects yet.') }}</p>
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
                                                        @if($project->organization)
                                                            <div class="small text-body-secondary mt-2">
                                                                {{ __('Organization') }}: {{ $project->organization->name }}
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
                                                        @if($project->organization)
                                                            <div class="small text-body-secondary mt-2">
                                                                {{ __('Organization') }}: {{ $project->organization->name }}
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
                            <h3 class="h6 mb-3">{{ __('Due soon') }}</h3>

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
                            <h3 class="h6 mb-3">{{ __('Team members') }}</h3>

                            @if($members->isEmpty())
                                <p class="small text-body-secondary mb-0">{{ __('No members yet.') }}</p>
                            @else
                                <div class="list-group list-group-flush">
                                    @foreach($members as $member)
                                        <div class="list-group-item px-0 d-flex align-items-center gap-3">
                                            <x-avatar :src="$member->profile_photo_url" :name="$member->name" preset="sm" />
                                            <div>
                                                <div class="fw-medium">{{ $member->name }}</div>
                                                <div class="small text-body-secondary">{{ $member->email }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h3 class="h6 mb-0">{{ __('Team goals') }}</h3>
                                <a class="small text-decoration-underline" href="{{ route('goals.index') }}">{{ __('Browse goals') }}</a>
                            </div>

                            @if($goals->isEmpty())
                                <p class="small text-body-secondary mb-0">{{ __('No team goals yet.') }}</p>
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
