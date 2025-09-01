<?php

use App\Models\Activity;
use App\Models\Project;
use App\Models\Issue;
use App\Models\User;
use App\Models\IssueStatus;
use App\Models\IssuePriority;
use App\Models\IssueType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

    $statusSummary = $statuses->map(fn($s) => [
        'id' => (string)$s->id,
        'name' => $s->name,
        'key' => $s->key,
        'color' => $s->color,
        'is_done' => (bool)$s->is_done,
        'count' => (int)($statusCounts[(string)$s->id] ?? 0),
    ])->values();

    // My assigned issues in this project
    $myIssues = Issue::query()
        ->where('project_id', $project->id)
        ->where('assignee_id', $user->id)
        ->latest()
        ->limit(8)
        ->get(['id', 'key', 'summary', 'issue_status_id', 'issue_priority_id', 'updated_at']);

    // Recent activity touching this project (project subject OR issue props reference)
    /** @var \Illuminate\Support\Collection<int, \Spatie\Activitylog\Models\Activity> $rawActivity */
    $rawActivity = Activity::query()
        ->where(function ($q) use ($project) {
            $q->where(function ($qq) use ($project) {
                $qq->where('subject_type', Project::class)->where('subject_id', $project->id);
            })->orWhere('properties->project_id', (string)$project->id);
        })
        ->latest()
        ->limit(50) // a bit more so grouping feels meaningful; UI will still show a slice
        ->get([
            'id',
            'description',
            'event',
            'created_at',
            'properties',
            'causer_id',
            'causer_type',
            'subject_type',
            'subject_id',
            'log_name',
        ]);

    // Collect IDs to avoid N+1s
    $issueIds = $rawActivity->where('subject_type', Issue::class)->pluck('subject_id')->filter()->unique();
    $causerIds = $rawActivity->where('causer_type', User::class)->pluck('causer_id')->filter()->unique();

    // Preload issues / users
    $issuesById = Issue::query()
        ->whereIn('id', $issueIds)
        ->get(['id', 'key', 'summary'])
        ->keyBy('id');

    $usersById = User::query()
        ->whereIn('id', $causerIds)
        ->get(['id', 'name', 'profile_photo_path'])
        ->keyBy('id');

    // Scan changed fields to preload lookups for nice labels
    $statusIds = [];
    $assigneeIds = [];
    $priorityIds = [];
    $typeIds = [];
    $parentIds = [];

    foreach ($rawActivity as $a) {
        /** @var array<string, mixed> $props */
        $props = (array)($a->properties ?? []);
        $new = (array)($props['attributes'] ?? $props['new'] ?? []);
        $old = (array)($props['old'] ?? $props['attributes_before'] ?? []);

        // Pull possible IDs for translation
        foreach (['issue_status_id', 'assignee_id', 'issue_priority_id', 'issue_type_id', 'parent_id'] as $k) {
            if (isset($new[$k])) {
                ${Str::of($k)->before('_id')->append('Ids')}[] = $new[$k];
            }
            if (isset($old[$k])) {
                ${Str::of($k)->before('_id')->append('Ids')}[] = $old[$k];
            }
        }
    }

    $statusMap = IssueStatus::query()->whereIn('id', array_filter($statusIds))->get(['id', 'name', 'color'])->keyBy('id');
    $assigneeMap = User::query()->whereIn('id', array_filter($assigneeIds))->get(['id', 'name', 'profile_photo_path'])->keyBy('id');
    $priorityMap = IssuePriority::query()->whereIn('id', array_filter($priorityIds))->get(['id', 'name'])->keyBy('id');
    $typeMap = IssueType::query()->whereIn('id', array_filter($typeIds))->get(['id', 'name'])->keyBy('id');
    $parentMap = Issue::query()->whereIn('id', array_filter($parentIds))->get(['id', 'key', 'summary'])->keyBy('id');

    // Human labels for common fields
    $labelMap = [
        'summary' => 'Summary',
        'description' => 'Description',
        'issue_status_id' => 'Status',
        'assignee_id' => 'Assignee',
        'issue_priority_id' => 'Priority',
        'issue_type_id' => 'Type',
        'parent_id' => 'Parent',
    ];

    // Turn activities into UI-ready items
    $enriched = $rawActivity->map(function (Activity $a) use ($usersById, $issuesById, $labelMap, $statusMap, $assigneeMap, $priorityMap, $typeMap, $parentMap, $project) {
        /** @var array<string, mixed> $props */
        $props = (array)($a->properties ?? []);
        $new = (array)($props['attributes'] ?? $props['new'] ?? []);
        $old = (array)($props['old'] ?? $props['attributes_before'] ?? []);

        $actor = $a->causer_type === User::class ? $usersById->get($a->causer_id) : null;
        $actorName = $actor?->name ?? 'System';
        $actorAvatar = $actor?->profile_photo_url
            ?? $actor?->profile_photo_path
            ?? null;

        // Target
        $targetType = $a->subject_type === Issue::class ? 'issue' : ($a->subject_type === Project::class ? 'project' : ($a->log_name ?: 'record'));
        $targetLabel = 'record';
        $targetUrl = null;

        if ($a->subject_type === Issue::class) {
            $issue = $issuesById->get($a->subject_id);
            if ($issue) {
                $targetLabel = "{$issue->key}: {$issue->summary}";
                $targetUrl = route('issues.show', ['project' => $project, 'issue' => $issue]);
            }
        } elseif ($a->subject_type === Project::class) {
            $targetLabel = "{$project->key} — {$project->name}";
            $targetUrl = route('projects.show', ['project' => $project]);
        } else {
            // Fallbacks via props if present
            if (isset($props['issue_key'], $props['issue_summary'])) {
                $targetLabel = "{$props['issue_key']}: {$props['issue_summary']}";
                $targetUrl = route('issues.index', ['project' => $project, 'search' => $props['issue_key']]);
            }
        }

        // Event verb
        $verb = $a->event ?: (Str::contains((string)$a->description, '.') ? Str::after((string)$a->description, '.') : (string)$a->description);
        $verb = Str::of($verb)->replace(['issue.', 'project.'], '')->headline(); // e.g. "Updated"

        // Build field diffs
        $changes = [];
        $keys = array_unique(array_merge(array_keys($new), array_keys($old)));
        foreach ($keys as $k) {
            $label = $labelMap[$k] ?? Str::of($k)->headline()->toString();

            $from = $old[$k] ?? null;
            $to = $new[$k] ?? null;

            // Pretty values for reference fields
            if ($k === 'issue_status_id') {
                $from = $from ? ($statusMap->get($from)?->name ?? $from) : null;
                $to = $to ? ($statusMap->get($to)?->name ?? $to) : null;
            } elseif ($k === 'assignee_id') {
                $from = $from ? ($assigneeMap->get($from)?->name ?? $from) : null;
                $to = $to ? ($assigneeMap->get($to)?->name ?? $to) : null;
            } elseif ($k === 'issue_priority_id') {
                $from = $from ? ($priorityMap->get($from)?->name ?? $from) : null;
                $to = $to ? ($priorityMap->get($to)?->name ?? $to) : null;
            } elseif ($k === 'issue_type_id') {
                $from = $from ? ($typeMap->get($from)?->name ?? $from) : null;
                $to = $to ? ($typeMap->get($to)?->name ?? $to) : null;
            } elseif ($k === 'parent_id') {
                $from = $from ? (($p = $parentMap->get($from)) ? "{$p->key}: {$p->summary}" : $from) : null;
                $to = $to ? (($p = $parentMap->get($to)) ? "{$p->key}: {$p->summary}" : $to) : null;
            }

            // Skip noise if nothing meaningful changed
            if (($from ?? '') === ($to ?? '')) {
                continue;
            }

            $changes[] = [
                'label' => $label,
                'from' => $from,
                'to' => $to,
                'key' => $k,
                // status color for pill on "to"
                'to_color' => $k === 'issue_status_id' && isset($new['issue_status_id'])
                    ? ($statusMap->get($new['issue_status_id'])->color ?? null)
                    : null,
            ];
        }

        return [
            'id' => $a->id,
            'actor_name' => $actorName,
            'actor_avatar' => $actorAvatar,
            'verb' => (string)$verb, // e.g. "Updated", "Created"
            'target_type' => $targetType,
            'target_label' => $targetLabel,
            'target_url' => $targetUrl,
            'changes' => $changes,
            'created_at' => $a->created_at,
            'ago' => $a->created_at?->diffForHumans(),
        ];
    });

    // Group for headings: Today / Yesterday / Date
    $activityGroups = $enriched->groupBy(function (array $i) {
        $dt = $i['created_at'];
        if ($dt?->isToday()) {
            return 'Today';
        }
        if ($dt?->isYesterday()) {
            return 'Yesterday';
        }
        return $dt?->toFormattedDateString() ?? 'Recent';
    });

    return $view->with(compact('statusSummary', 'myIssues', 'activityGroups'));
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
                <a href="{{ route('projects.timeline', ['project' => $project]) }}"
                   class="inline-flex items-center rounded-lg px-3 py-2 border">Timeline</a>
                <a href="{{ route('projects.calendar', ['project' => $project]) }}"
                   class="inline-flex items-center rounded-lg px-3 py-2 border">Calendar</a>
                <a href="{{ route('projects.board', ['project' => $project]) }}"
                   class="inline-flex items-center rounded-lg px-3 py-2 border">Kanban</a>
                <a href="{{ route('projects.scrum', ['project' => $project]) }}"
                   class="inline-flex items-center rounded-lg px-3 py-2 border">Sprint</a>
                @can('issues.create')
                    <a href="{{ route('issues.create', ['project' => $project]) }}"
                       class="inline-flex items-center rounded-lg px-3 py-2 border">New issue</a>
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
                            <div
                                class="flex items-center justify-between rounded-lg bg-gray-50 dark:bg-gray-800 px-3 py-2">
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
                    <div class="flex items-center justify-between">
                        <h3 class="font-medium text-gray-900 dark:text-gray-100">Recent activity</h3>
                    </div>

                    <div class="mt-4 space-y-6">
                        @forelse($activityGroups as $groupLabel => $items)
                            <div>
                                <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                    {{ $groupLabel }}
                                </h4>

                                <ul class="mt-2 space-y-3">
                                    @foreach($items as $i)
                                        <li class="rounded-lg border border-gray-200/60 dark:border-gray-700/60 p-3">
                                            <div class="flex items-start gap-3">
                                                <img
                                                    src="{{ $i['actor_avatar'] ?? asset('images/default-avatar.png') }}"
                                                    alt=""
                                                    class="h-8 w-8 rounded-full object-cover"
                                                >

                                                <div class="min-w-0 flex-1">
                                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                                        <span class="font-medium">{{ $i['actor_name'] }}</span>
                                                        <span class="text-gray-600 dark:text-gray-300">
                                            {{ strtolower($i['verb']) }}
                                        </span>

                                                        @if($i['target_url'])
                                                            <a href="{{ $i['target_url'] }}" class="font-medium underline decoration-dotted">
                                                                {{ $i['target_label'] }}
                                                            </a>
                                                        @else
                                                            <span class="font-medium">{{ $i['target_label'] }}</span>
                                                        @endif
                                                    </div>

                                                    <div class="mt-0.5 text-xs text-gray-500">
                                                        {{ $i['ago'] }}
                                                    </div>

                                                    {{-- Compact “headline” for key changes (e.g., Status) --}}
                                                    @php
                                                        $statusChange = collect($i['changes'])->firstWhere('key','issue_status_id');
                                                    @endphp

                                                    @if($statusChange)
                                                        <div class="mt-2 text-xs flex items-center gap-2">
                                                            <span class="text-gray-500">{{ $statusChange['label'] }}:</span>
                                                            <span class="rounded px-1.5 py-0.5 bg-gray-100 dark:bg-gray-800">
                                                {{ $statusChange['from'] ?? '—' }}
                                            </span>
                                                            <span>→</span>
                                                            <span class="rounded px-1.5 py-0.5"
                                                                  style="background-color: {{ $statusChange['to_color'] ?? 'transparent' }}20">
                                                {{ $statusChange['to'] ?? '—' }}
                                            </span>
                                                        </div>
                                                    @endif

                                                    {{-- Toggle for full field diff --}}
                                                    @if(!empty($i['changes']))
                                                        <div x-data="{ open: false }" class="mt-2">
                                                            <button type="button"
                                                                    class="text-xs text-primary-600 hover:underline"
                                                                    @click="open = !open">
                                                                <span x-show="!open">Show details</span>
                                                                <span x-show="open">Hide details</span>
                                                            </button>

                                                            <div x-show="open" x-cloak class="mt-2 rounded-lg bg-gray-50 dark:bg-gray-800/50 p-3 space-y-2 text-xs">
                                                                @foreach($i['changes'] as $c)
                                                                    <div class="flex items-start gap-2">
                                                                        <div class="shrink-0 w-28 text-gray-500">{{ $c['label'] }}</div>
                                                                        <div class="flex-1">
                                                                            <div class="inline-flex items-center gap-2">
                                                                <span class="rounded px-1.5 py-0.5 bg-white dark:bg-gray-900 border border-gray-200/60 dark:border-gray-700/60">
                                                                    {{ $c['from'] ?? '—' }}
                                                                </span>
                                                                                <span>→</span>
                                                                                <span class="rounded px-1.5 py-0.5 border border-gray-200/60 dark:border-gray-700/60"
                                                                                      @if($c['to_color']) style="background-color: {{ $c['to_color'] }}20" @endif>
                                                                    {{ $c['to'] ?? '—' }}
                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 mt-2">No activity yet.</p>
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
                        <livewire:issues.quick-create :project="$project"/>
                    </div>
                @endcan
            </aside>
        </div>
    </div>
</x-app-layout>

