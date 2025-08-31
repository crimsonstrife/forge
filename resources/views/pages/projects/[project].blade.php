<?php

use App\Models\Project;
use App\Models\Issue;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use function Laravel\Folio\{name, middleware, render};

name('projects.show');
middleware(['auth', 'verified']);

/** Provide page data */
render(function (View $view, Project $project) {
    $user = auth()->user();

    // Lightweight eager-loads for the header/sidebar
    $project->loadMissing([
        'organization:id,name',
        'teams:id,name',
        'users:id,name',
    ]);

    // Counts by status for this project
    $statusCounts = Issue::query()
        ->where('project_id', $project->id)
        ->selectRaw('issue_status_id, COUNT(*) as total')
        ->groupBy('issue_status_id')
        ->pluck('total', 'issue_status_id');

    // Project’s configured statuses (ordered) with counts
    $statuses = $project->issueStatuses()
        ->withPivot(['order'])
        ->orderBy('project_issue_statuses.order')
        ->get(['issue_statuses.id', 'issue_statuses.name', 'issue_statuses.key', 'issue_statuses.color', 'issue_statuses.is_done']);

    $statusSummary = $statuses->map(fn ($s) => [
        'id'      => (string) $s->id,
        'name'    => $s->name,
        'key'     => $s->key,
        'color'   => $s->color,
        'is_done' => (bool) $s->is_done,
        'count'   => (int) ($statusCounts[(string) $s->id] ?? 0),
    ])->values();

    // My assigned issues in this project
    $myIssues = Issue::query()
        ->where('project_id', $project->id)
        ->where('assignee_id', $user->id)
        ->latest()
        ->limit(8)
        ->get(['id', 'key', 'summary', 'issue_status_id', 'issue_priority_id', 'updated_at']);

    // Recent activity touching this project (project subject OR issue props reference)
    $activity = \Spatie\Activitylog\Models\Activity::query()
        ->where(function ($q) use ($project) {
            $q->where(function ($qq) use ($project) {
                $qq->where('subject_type', Project::class)->where('subject_id', $project->id);
            })->orWhere('properties->project_id', (string) $project->id);
        })
        ->latest()
        ->limit(15)
        ->get(['id', 'description', 'created_at', 'properties', 'causer_id', 'causer_type', 'log_name', 'event']);

    return $view->with(compact('statusSummary', 'myIssues', 'activity'));
});
?>

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
                    {{ $project->key }} — {{ $project->name }}
                </h2>
                <div class="mt-1 text-sm text-gray-500 dark:text-gray-400 flex flex-wrap gap-3">
                    <span class="inline-flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-xs">
                            {{ ucfirst($project->stage->value ?? 'planning') }}
                        </span>
                    </span>
                    @if ($project->organization?->name)
                        <span>Org: {{ $project->organization->name }}</span>
                    @endif
                    @if ($project->teams->isNotEmpty())
                        <span>Teams:
                            {{ $project->teams->pluck('name')->implode(', ') }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('projects.timeline', ['project' => $project]) }}" class="inline-flex items-center rounded-lg px-3 py-2 border">Timeline</a>
                <a href="{{ route('projects.calendar', ['project' => $project]) }}" class="inline-flex items-center rounded-lg px-3 py-2 border">Calendar</a>
                <a href="{{ route('projects.board', ['project' => $project]) }}" class="inline-flex items-center rounded-lg px-3 py-2 border">Kanban</a>
                <a href="{{ route('projects.scrum', ['project' => $project]) }}" class="inline-flex items-center rounded-lg px-3 py-2 border">Sprint</a>
                @can('issues.create')
                    <a href="{{ route('issues.create', ['project' => $project]) }}"
                       class="inline-flex items-center rounded-lg px-3 py-2 border">
                        New Issue
                    </a>
                @endcan
                @can('update', $project)
                    <a href="{{ route('projects.edit', ['project' => $project]) }}"
                       class="inline-flex items-center rounded-lg px-3 py-2 border">
                        Edit Project
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl grid gap-6 lg:grid-cols-3">
            {{-- Left: Overview --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Status summary --}}
                <div class="rounded-xl border border-gray-200/60 dark:border-gray-700/60 p-4">
                    <h3 class="font-medium text-gray-900 dark:text-gray-100">Issues by status</h3>
                    <div class="mt-3 grid gap-3 sm:grid-cols-2 md:grid-cols-3">
                        @forelse($statusSummary as $s)
                            <div class="flex items-center justify-between rounded-lg bg-gray-50 dark:bg-gray-800 px-3 py-2">
                                <div class="flex items-center gap-2">
                                    <span class="inline-block w-2 h-2 rounded-full"
                                          style="background: {{ $s['color'] }}"></span>
                                    <span class="text-sm">{{ $s['name'] }}</span>
                                </div>
                                <div class="font-semibold">{{ $s['count'] }}</div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No statuses configured.</p>
                        @endforelse
                    </div>
                </div>

                {{-- My work --}}
                <div class="rounded-xl border border-gray-200/60 dark:border-gray-700/60 p-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-medium text-gray-900 dark:text-gray-100">My assigned issues</h3>
                        <a href="{{ route('issues.index', ['project' => $project, 'assignee' => auth()->id()]) }}"
                           class="text-sm text-primary-600 hover:underline">View all</a>
                    </div>
                    <div class="mt-3 divide-y divide-gray-200/60 dark:divide-gray-700/60">
                        @forelse($myIssues as $issue)
                            <a class="block py-3 hover:bg-gray-50 dark:hover:bg-gray-800/60 px-2 rounded-md"
                               href="{{ route('issues.show', ['project' => $project, 'issue' => $issue]) }}">
                                <div class="flex items-center justify-between">
                                    <div class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $issue->summary }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $issue->updated_at->diffForHumans() }}
                                    </div>
                                </div>
                            </a>
                        @empty
                            <p class="text-sm text-gray-500">No issues assigned to you yet.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Activity --}}
                <div class="rounded-xl border border-gray-200/60 dark:border-gray-700/60 p-4">
                    <h3 class="font-medium text-gray-900 dark:text-gray-100">Recent activity</h3>
                    <div class="mt-3 space-y-3">
                        @forelse($activity as $a)
                            <div class="text-sm">
                                <div class="text-gray-900 dark:text-gray-100">
                                    {{ str_replace('issue.', '', (string) $a->description) }}
                                </div>
                                <div class="text-gray-500">
                                    {{ $a->created_at->diffForHumans() }}
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No activity yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Right: Sidebar --}}
            <aside class="space-y-6">
                <div class="rounded-xl border border-gray-200/60 dark:border-gray-700/60 p-4">
                    <h3 class="font-medium text-gray-900 dark:text-gray-100">Project info</h3>
                    <dl class="mt-2 space-y-2 text-sm">
                        @if($project->lead_id)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Lead</dt>
                                <dd class="text-gray-900 dark:text-gray-100">
                                    {{ optional($project->users->firstWhere('id', $project->lead_id))->name ?? '—' }}
                                </dd>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Created</dt>
                            <dd class="text-gray-900 dark:text-gray-100">
                                {{ $project->created_at?->toFormattedDateString() ?? '—' }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Teams</dt>
                            <dd class="text-gray-900 dark:text-gray-100">
                                {{ $project->teams->pluck('name')->implode(', ') ?: '—' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                @can('update', $project)
                    <div class="rounded-xl border border-gray-200/60 dark:border-gray-700/60 p-4">
                        <h3 class="font-medium text-gray-900 dark:text-gray-100">Admin</h3>
                        <ul class="mt-2 text-sm space-y-2">
                            <li>
                                <a class="text-primary-600 hover:underline"
                                   href="{{ route('projects.edit', ['project' => $project]) }}">
                                    Edit details
                                </a>
                            </li>
                            <li>
                                <a class="text-primary-600 hover:underline"
                                   href="{{ route('projects.transitions', ['project' => $project]) }}">
                                    Status transitions
                                </a>
                            </li>
                        </ul>
                    </div>
                @endcan

                @can('issues.create')
                    {{-- Quick create Issues --}}
                    <div class="rounded-xl border border-gray-200/60 dark:border-gray-700/60 p-4">
                        <livewire:issues.quick-create :project="$project" />
                    </div>
                @endcan
            </aside>
        </div>
    </div>
</x-app-layout>

