<?php

use App\Models\Activity;
use App\Models\IssuePriority;
use App\Models\IssueStatus;
use App\Models\IssueType;
use App\Models\Project;
use App\Models\Issue;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

use function Laravel\Folio\{name, middleware, render};

name('issues.show');
middleware(['auth', 'verified']);

render(function (View $view, Project $project, Issue $issue) {
    $issue->loadMissing([
        'project:id,key',
        'status:id,name,color,is_done',
        'type:id,key,name',
        'priority:id,name',
        'assignee:id,name,profile_photo_path',
        'reporter:id,name,profile_photo_path',
        'tags',
        'parent:id,key,summary,project_id',
        'children' => fn ($q) => $q
            ->with([
                'status:id,name,color,is_done',
                'assignee:id,name,profile_photo_path',
                'project:id,key',
                'type:id,key,name',
                'priority:id,name',
            ])
            ->latest(),
    ]);

    $issue->loadCount([
        'comments',
        'media as attachments_count' => fn ($m) => $m->where('collection_name', 'attachments'),
    ]);

    // Recent activity for THIS issue
    /** @var Collection<int, Activity> $rawActivity */
    $rawActivity = Activity::query()
        ->where(function ($q) use ($issue) {
            // Direct subject match (Issue model)
            $q->where(function ($qq) use ($issue) {
                $qq->where('subject_type', Issue::class)->where('subject_id', $issue->id);
            })
                // Or any logs that referenced the issue in properties (common for attachments, comments, timers, etc.)
                ->orWhere('properties->issue_id', (string)$issue->id)
                ->orWhere('properties->issue_key', $issue->key);
        })
        ->latest()
        ->limit(100)
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
    $causerIds = $rawActivity->where('causer_type', User::class)->pluck('causer_id')->filter()->unique();

    // Preload users
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
    $enriched = $rawActivity->map(function (Activity $a) use ($usersById, $labelMap, $statusMap, $assigneeMap, $priorityMap, $typeMap, $parentMap, $issue, $project) {
        /** @var array<string, mixed> $props */
        $props = (array)($a->properties ?? []);
        $new = (array)($props['attributes'] ?? $props['new'] ?? []);
        $old = (array)($props['old'] ?? $props['attributes_before'] ?? []);

        $actor = $a->causer_type === User::class ? $usersById->get($a->causer_id) : null;
        $actorName = $actor?->name ?? 'System';
        $actorAvatar = $actor?->profile_photo_url
            ?? $actor?->profile_photo_path
            ?? null;

        // Target is this issue (fallbacks if activity carries a different subject but references this issue)
        $targetLabel = "{$issue->key}: {$issue->summary}";
        $targetUrl = route('issues.show', ['project' => $project, 'issue' => $issue]);

        if ($a->subject_type === Issue::class && $a->subject_id === $issue->id) {
            // already correct target
        } elseif (isset($props['issue_key'], $props['issue_summary'])) {
            $targetLabel = "{$props['issue_key']}: {$props['issue_summary']}";
        }

        // Event verb
        $verb = $a->event ?: (Str::contains((string)$a->description, '.') ? Str::after((string)$a->description, '.') : (string)$a->description);
        $verb = Str::of($verb)->replace(['issue.', 'project.'], '')->headline();

        // Build field diffs
        $changes = [];
        $keys = array_unique(array_merge(array_keys($new), array_keys($old)));
        foreach ($keys as $k) {
            $label = $labelMap[$k] ?? Str::of($k)->headline()->toString();

            $from = $old[$k] ?? null;
            $to = $new[$k] ?? null;

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

            if (($from ?? '') === ($to ?? '')) {
                continue;
            }

            $changes[] = [
                'label' => $label,
                'from' => $from,
                'to' => $to,
                'key' => $k,
                'to_color' => $k === 'issue_status_id' && isset($new['issue_status_id'])
                    ? ($statusMap->get($new['issue_status_id'])->color ?? null)
                    : null,
            ];
        }

        return [
            'id' => $a->id,
            'actor_name' => $actorName,
            'actor_avatar' => $actorAvatar,
            'verb' => (string)$verb,
            'target_label' => $targetLabel,
            'target_url' => $targetUrl,
            'changes' => $changes,
            'created_at' => $a->created_at,
            'ago' => $a->created_at?->diffForHumans(),
        ];
    });

    // Group headings: Today / Yesterday / Date
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

    $children = $issue->children;
    $childrenTotal = $children->count();
    $childrenDone = $children->filter(fn ($c) => (bool)$c->status?->is_done)->count();
    $childrenPointsTotal = (int)$children->sum('story_points');
    $childrenPointsDone = (int)$children->filter(fn ($c) => (bool)$c->status?->is_done)->sum('story_points');
    $childrenProgressPct = $childrenTotal > 0 ? (int)round(($childrenDone / $childrenTotal) * 100) : 0;

    $attachments = $issue->media()
        ->where('collection_name', 'attachments')
        ->latest()
        ->get();

    return $view->with(compact(
        'attachments',
        'project',
        'issue',
        'children',
        'childrenTotal',
        'childrenDone',
        'childrenPointsTotal',
        'childrenPointsDone',
        'childrenProgressPct',
        'activityGroups',
    ));
});
?>

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
                    {{ $project->key }} — {{ $issue->key }}
                </h2>
                <a href="{{ route('projects.show', ['project' => $project]) }}"
                   class="text-sm text-primary-600 hover:underline">Back to project</a>
                @if($issue->parent)
                    <div class="mt-2 text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Parent:</span>
                        <a class="underline"
                           href="{{ route('issues.show', ['project' => $project, 'issue' => $issue->parent]) }}">
                            {{ $issue->parent->key }} — {{ $issue->parent->summary }}
                        </a>
                    </div>
                @endif
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
                @can('update', $issue)
                    <a href="{{ route('issues.edit', ['project'=>$project, 'issue'=>$issue]) }}"
                       class="rounded-lg px-3 py-2 border border-gray-300 dark:border-gray-700">Edit</a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-5xl space-y-6">

            <!-- Header card -->
            <div class="rounded-xl border border-gray-200/60 dark:border-gray-700/60 bg-white dark:bg-gray-800 p-6">
                <div class="flex items-start justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-3">
                            <span
                                class="text-xs font-mono px-2 py-1 rounded bg-gray-100 dark:bg-gray-700">{{ $issue->key }}</span>
                            <span class="inline-flex items-center gap-1 text-sm">
                                <span class="h-2 w-2 rounded-full"
                                      style="background: {{ $issue->status?->color ?? '#9ca3af' }}"></span>
                                {{ $issue->status?->name }}
                            </span>
                            <span class="text-sm">{{ $issue->type?->name }}</span>
                            <span class="text-sm">{{ $issue->priority?->name }}</span>
                        </div>
                        <h3 class="mt-2 text-xl font-semibold">{{ $issue->summary }}</h3>
                        <p class="mt-3 text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $issue->description }}</p>

                        @if($issue->tags->isNotEmpty())
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach($issue->tags as $tag)
                                    <span
                                        class="text-xs px-2 py-1 rounded bg-gray-100 dark:bg-gray-700">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="shrink-0 w-64">
                        <dl class="text-sm space-y-2">
                            <div class="flex justify-between">
                                <dt>Reporter</dt>
                                <dd class="flex items-center gap-2">
                                    @if($issue->reporter)
                                        <img class="h-5 w-5 rounded-full"
                                             src="{{ $issue->reporter->profile_photo_url }}">
                                        {{ $issue->reporter->name }}
                                    @else
                                        —
                                    @endif
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt>Assignee</dt>
                                <dd class="flex items-center gap-2">
                                    @if($issue->assignee)
                                        <img class="h-5 w-5 rounded-full"
                                             src="{{ $issue->assignee->profile_photo_url }}">
                                        {{ $issue->assignee->name }}
                                    @else
                                        Unassigned
                                    @endif
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt>Story points</dt>
                                <dd>{{ $issue->story_points ?? '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt>Estimate</dt>
                                <dd>{{ $issue->estimate_minutes ? $issue->estimate_minutes.'m' : '—' }}</dd>
                            </div>
                            <div class="pt-3">
                                <div class="text-xs mb-1">Progress</div>
                                <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded">
                                    <div class="h-2 rounded bg-primary-600"
                                         style="width: {{ (int) round($issue->progress() * 100) }}%"></div>
                                </div>
                            </div>
                            <div class="pt-3 text-xs text-gray-500">
                                Updated {{ $issue->updated_at?->diffForHumans() }}
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Attachments -->
            <div class="rounded-xl border border-gray-200/60 dark:border-gray-700/60 bg-white dark:bg-gray-800 p-6">
                <div class="flex items-center justify-between">
                    <h4 class="font-semibold">Attachments ({{ $issue->attachments_count }})</h4>
                    @can('update', $issue)
                        <livewire:issues.attachment-upload :issue="$issue"/>
                    @endcan
                </div>
                <ul class="mt-4 divide-y divide-gray-200/60 dark:divide-gray-700/60">
                    @forelse($attachments as $file)
                        <li class="py-3 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span>📎</span>
                                <div>
                                    <div class="text-sm font-medium">{{ $file->file_name }}</div>
                                    <div class="text-xs text-gray-500">{{ number_format($file->size / 1024, 1) }} KB ·
                                        uploaded {{ $file->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            <a class="text-sm underline"
                               href="{{ route('issues.attachments.download', [$project, $issue, $file]) }}">Download</a>
                        </li>
                    @empty
                        <li class="py-6 text-sm text-gray-500">No attachments.</li>
                    @endforelse
                </ul>
            </div>
            <livewire:issues.focus-timer :issue="$issue" class="mb-4"/>
            <livewire:issues.time-entries-panel :issue="$issue"/>

            <!-- Sub-issues -->
            <div class="rounded-xl border border-gray-200/60 dark:border-gray-700/60 bg-white dark:bg-gray-800 p-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h4 class="font-semibold">Sub-issues ({{ $childrenTotal }})</h4>
                        @if($childrenTotal > 0)
                            <div class="mt-2 text-xs text-gray-500">
                                {{ $childrenDone }} of {{ $childrenTotal }} done
                                @if($childrenPointsTotal > 0)
                                    · {{ $childrenPointsDone }}/{{ $childrenPointsTotal }} pts
                                @endif
                            </div>
                            <div class="mt-2 h-2 bg-gray-200 dark:bg-gray-700 rounded">
                                <div class="h-2 rounded bg-primary-600"
                                     style="width: {{ $childrenProgressPct }}%"></div>
                            </div>
                        @endif
                    </div>

                    <div class="shrink-0">
                        @can('create', [App\Models\Issue::class, $project])
                            <a href="{{ route('issues.create', ['project' => $project, 'parent' => $issue->key]) }}"
                               class="inline-flex items-center gap-2 rounded-lg px-3 py-2 border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                + New sub-issue
                            </a>
                        @endcan
                    </div>
                </div>

                @if($childrenTotal === 0)
                    <p class="mt-4 text-sm text-gray-500">No sub-issues yet.</p>
                @else
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr class="text-left">
                                <th class="py-2 pr-4">Key</th>
                                <th class="py-2 pr-4">Summary</th>
                                <th class="py-2 pr-4">Status</th>
                                <th class="py-2 pr-4">Assignee</th>
                                <th class="py-2 pr-4">Type</th>
                                <th class="py-2 pr-4">Priority</th>
                                <th class="py-2 pr-0">Pts</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200/60 dark:divide-gray-700/60">
                            @foreach($children as $child)
                                <tr>
                                    <td class="py-2 pr-4 font-mono">
                                        <a class="underline"
                                           href="{{ route('issues.show', ['project'=>$project, 'issue'=>$child]) }}">
                                            {{ $child->key }}
                                        </a>
                                    </td>
                                    <td class="py-2 pr-4">{{ $child->summary }}</td>
                                    <td class="py-2 pr-4">
                            <span class="inline-flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full"
                                      style="background: {{ $child->status?->color ?? '#9ca3af' }}"></span>
                                {{ $child->status?->name ?? '—' }}
                            </span>
                                    </td>
                                    <td class="py-2 pr-4">
                                        @if($child->assignee)
                                            <span class="inline-flex items-center gap-2">
                                    <img class="h-4 w-4 rounded-full" src="{{ $child->assignee->profile_photo_url }}">
                                    {{ $child->assignee->name }}
                                </span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="py-2 pr-4">{{ $child->type?->name ?? '—' }}</td>
                                    <td class="py-2 pr-4">{{ $child->priority?->name ?? '—' }}</td>
                                    <td class="py-2 pr-0 text-right">{{ $child->story_points ?? '—' }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Activity -->
            <div class="rounded-xl border border-gray-200/60 dark:border-gray-700/60 bg-white dark:bg-gray-800 p-6">
                <div class="flex items-center justify-between">
                    <h4 class="font-semibold">Activity</h4>
                </div>

                <div class="mt-4 space-y-6">
                    @forelse($activityGroups as $groupLabel => $items)
                        <div>
                            <h5 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                {{ $groupLabel }}
                            </h5>

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

                                                {{-- Compact “headline” for key change (Status) --}}
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

            <!-- Comments -->
            <div class="rounded-xl border border-gray-200/60 dark:border-gray-700/60 bg-white dark:bg-gray-800 p-6">
                <h4 class="font-semibold">Comments ({{ $issue->comments_count }})</h4>
                <livewire:issues.comments :issue="$issue"/>
            </div>
        </div>
    </div>
</x-app-layout>
