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
        <div class="d-flex align-items-center justify-content-between gap-3">
            <div>
                <h2 class="h4 mb-1">{{ $project->key }} — {{ $issue->key }}</h2>
                <a href="{{ route('projects.show', ['project' => $project]) }}" class="link-primary small">Back to project</a>
                @if($issue->parent)
                    <div class="small mt-1">
                        <span class="text-body-secondary">Parent:</span>
                        <a class="link-primary" href="{{ route('issues.show', ['project' => $project, 'issue' => $issue->parent]) }}">
                            {{ $issue->parent->key }} — {{ $issue->parent->summary }}
                        </a>
                    </div>
                @endif
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('projects.timeline', ['project' => $project]) }}" class="btn btn-outline-secondary btn-sm">Timeline</a>
                <a href="{{ route('projects.calendar', ['project' => $project]) }}" class="btn btn-outline-secondary btn-sm">Calendar</a>
                <a href="{{ route('projects.board', ['project' => $project]) }}" class="btn btn-outline-secondary btn-sm">Kanban</a>
                <a href="{{ route('projects.scrum', ['project' => $project]) }}" class="btn btn-outline-secondary btn-sm">Sprint</a>
                @can('issues.create')
                    <a href="{{ route('issues.create', ['project' => $project]) }}" class="btn btn-primary btn-sm">New issue</a>
                @endcan
                @can('update', $issue)
                    <a href="{{ route('issues.edit', ['project'=>$project, 'issue'=>$issue]) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container" style="max-width: 1000px">
            <div class="d-grid gap-3">

                {{-- Header card --}}
                <div class="card shadow-sm">
                    <div class="card-body d-flex gap-4 justify-content-between">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge text-bg-light">{{ $issue->key }}</span>
                                <span class="d-inline-flex align-items-center gap-1 small">
                                    <span class="rounded-circle d-inline-block" style="width:.5rem;height:.5rem;background: {{ $issue->status?->color ?? '#9ca3af' }}"></span>
                                    {{ $issue->status?->name }}
                                </span>
                                <span class="small">{{ $issue->type?->name }}</span>
                                <span class="small">{{ $issue->priority?->name }}</span>
                            </div>
                            <h3 class="h5 mt-2 mb-2">{{ $issue->summary }}</h3>
                            @if($issue->description)
                                <div class="text-body">{{ $issue->description }}</div>
                            @endif

                            @if($issue->tags->isNotEmpty())
                                <div class="mt-3 d-flex flex-wrap gap-2">
                                    @foreach($issue->tags as $tag)
                                        <span class="badge text-bg-secondary">{{ $tag->name }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div style="width: 260px" class="flex-shrink-0">
                            <dl class="small mb-0">
                                <div class="d-flex justify-content-between mb-1">
                                    <dt class="text-body-secondary">Reporter</dt>
                                    <dd class="mb-0 d-flex align-items-center gap-2">
                                        @if($issue->reporter)
                                            <x-avatar :src="$issue->reporter->profile_photo_url" :name="$issue->reporter->name" preset="md" />
                                            {{ $issue->reporter->name }}
                                        @else
                                            —
                                        @endif
                                    </dd>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <dt class="text-body-secondary">Assignee</dt>
                                    <dd class="mb-0 d-flex align-items-center gap-2">
                                        <x-avatar-group class="tw-flex">
                                            @if($issue->assignee)
                                                <x-avatar :src="$issue->assignee->profile_photo_url" :name="$issue->assignee->name" preset="md" />
                                                {{ $issue->assignee->name }}
                                            @else
                                                {{ __('Unassigned') }}
                                            @endif
                                        </x-avatar-group>
                                    </dd>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <dt class="text-body-secondary">Story points</dt>
                                    <dd class="mb-0">{{ $issue->story_points ?? '—' }}</dd>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <dt class="text-body-secondary">Estimate</dt>
                                    <dd class="mb-0">{{ $issue->estimate_minutes ? $issue->estimate_minutes.'m' : '—' }}</dd>
                                </div>
                                <div class="pt-2">
                                    <div class="text-body-secondary small mb-1">Progress</div>
                                    <wa-progress-bar aria-label="Progress" value="{{round($issue->progress())}}" style="--track-height: 6px;">{{ (int) round($issue->progress() * 100) }}%</wa-progress-bar>
                                </div>
                                <div class="pt-2 text-body-secondary small">
                                    {{ __('Updated') }} {{ $issue->updated_at?->diffForHumans() }}
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                {{-- Attachments --}}
                <div class="card shadow-sm">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <h4 class="h6 mb-0">Attachments ({{ $issue->attachments_count }})</h4>
                        @can('update', $issue)
                            <livewire:issues.attachment-upload :issue="$issue"/>
                        @endcan
                    </div>
                    <ul class="list-group list-group-flush">
                        @forelse($attachments as $file)
                            <li class="list-group-item d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <span>📎</span>
                                    <div>
                                        <div class="fw-semibold small">{{ $file->file_name }}</div>
                                        <div class="text-body-secondary small">{{ number_format($file->size / 1024, 1) }} KB · uploaded {{ $file->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                                <a class="small" href="{{ route('issues.attachments.download', [$project, $issue, $file]) }}">Download</a>
                            </li>
                        @empty
                            <li class="list-group-item text-body-secondary small py-4">No attachments.</li>
                        @endforelse
                    </ul>
                </div>

                {{-- Timer + time entries --}}
                <livewire:issues.focus-timer :issue="$issue" class="mb-2"/>
                <livewire:issues.time-entries-panel :issue="$issue"/>

                {{-- Sub-issues --}}
                <div class="card shadow-sm">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="h6 mb-1">Sub-issues ({{ $childrenTotal }})</h4>
                            @if($childrenTotal > 0)
                                <div class="text-body-secondary small">
                                    {{ $childrenDone }} of {{ $childrenTotal }} done
                                    @if($childrenPointsTotal > 0)
                                        · {{ $childrenPointsDone }}/{{ $childrenPointsTotal }} pts
                                    @endif
                                </div>
                                <wa-progress-bar aria-label="Progress" value="{{ $childrenProgressPct }}" style="--track-height: 6px;">{{ $childrenProgressPct }}%</wa-progress-bar>
                            @endif
                        </div>
                        <div>
                            @can('create', [App\Models\Issue::class, $project])
                                <a href="{{ route('issues.create', ['project' => $project, 'parent' => $issue->key]) }}" class="btn btn-outline-secondary btn-sm">
                                    + New sub-issue
                                </a>
                            @endcan
                        </div>
                    </div>

                    @if($childrenTotal === 0)
                        <div class="card-body text-body-secondary small">No sub-issues yet.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>Key</th>
                                    <th>Summary</th>
                                    <th>Status</th>
                                    <th>Assignee</th>
                                    <th>Type</th>
                                    <th>Priority</th>
                                    <th class="text-end">Pts</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($children as $child)
                                    <tr>
                                        <td>
                                            <a class="link-primary" href="{{ route('issues.show', ['project'=>$project, 'issue'=>$child]) }}">
                                                {{ $child->key }}
                                            </a>
                                        </td>
                                        <td>{{ $child->summary }}</td>
                                        <td>
                                            <span class="d-inline-flex align-items-center gap-1">
                                                <span class="rounded-circle d-inline-block" style="width:.5rem;height:.5rem;background: {{ $child->status?->color ?? '#9ca3af' }}"></span>
                                                {{ $child->status?->name ?? '—' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($child->assignee)
                                                <span class="d-inline-flex align-items-center gap-2">
                                                    <x-avatar :src="$child->assignee->profile_photo_url" :name="$child->assignee->name" preset="sm" />
                                                    {{ $child->assignee->name }}
                                                </span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>{{ $child->type?->name ?? '—' }}</td>
                                        <td>{{ $child->priority?->name ?? '—' }}</td>
                                        <td class="text-end">{{ $child->story_points ?? '—' }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- Activity --}}
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="h6">Activity</h4>
                        <div class="mt-3">
                            @forelse($activityGroups as $groupLabel => $items)
                                <div class="mb-3">
                                    <div class="text-uppercase text-body-secondary small fw-semibold">{{ $groupLabel }}</div>
                                    <ul class="list-unstyled mb-0 mt-2">
                                        @foreach($items as $i)
                                            <li class="border rounded p-3 mb-2">
                                                <div class="d-flex gap-2">
                                                    <x-avatar :src="$i['actor_avatar'] ?? asset('images/default-avatar.png')" :name="$i['actor_name']" preset="sm" />
                                                    <div class="flex-grow-1">
                                                        <div class="small">
                                                            <span class="fw-semibold">{{ $i['actor_name'] }}</span>
                                                            <span class="text-body-secondary">{{ strtolower($i['verb']) }}</span>
                                                            @if($i['target_url'])
                                                                <a href="{{ $i['target_url'] }}" class="link-primary fw-semibold">{{ $i['target_label'] }}</a>
                                                            @else
                                                                <span class="fw-semibold">{{ $i['target_label'] }}</span>
                                                            @endif
                                                        </div>
                                                        <div class="text-body-secondary small">{{ $i['ago'] }}</div>

                                                        @php $statusChange = collect($i['changes'])->firstWhere('key','issue_status_id'); @endphp
                                                        @if($statusChange)
                                                            <div class="small mt-2 d-flex align-items-center gap-2">
                                                                <span class="text-body-secondary">{{ $statusChange['label'] }}:</span>
                                                                <span class="badge text-bg-light">{{ $statusChange['from'] ?? '—' }}</span>
                                                                <span>→</span>
                                                                <span class="badge" style="background-color: {{ $statusChange['to_color'] ?? 'transparent' }}20;color: inherit;">
                                                                    {{ $statusChange['to'] ?? '—' }}
                                                                </span>
                                                            </div>
                                                        @endif

                                                        @if(!empty($i['changes']))
                                                            <div x-data="{ open:false }" class="mt-2">
                                                                <button type="button" class="btn btn-link btn-sm p-0" @click="open = !open">
                                                                    <span x-show="!open">Show details</span>
                                                                    <span x-show="open">Hide details</span>
                                                                </button>
                                                                <div x-show="open" x-cloak class="mt-2 border rounded p-2 small">
                                                                    @foreach($i['changes'] as $c)
                                                                        <div class="d-flex gap-2">
                                                                            <div class="text-body-secondary" style="width: 9rem">{{ $c['label'] }}</div>
                                                                            <div class="flex-grow-1 d-flex align-items-center gap-2">
                                                                                <span class="badge text-bg-light">{{ $c['from'] ?? '—' }}</span>
                                                                                <span>→</span>
                                                                                <span class="badge" @if($c['to_color']) style="background-color: {{ $c['to_color'] }}20;color: inherit;" @endif>
                                                                                    {{ $c['to'] ?? '—' }}
                                                                                </span>
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
                                <p class="text-body-secondary small mb-0">No activity yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Comments --}}
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="h6">Comments ({{ $issue->comments_count }})</h4>
                        <livewire:issues.comments :issue="$issue"/>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
