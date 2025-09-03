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

    $statusSummary = $statuses->map(fn ($s) => [
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
        <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
            <div>
                <h2 class="h4 mb-1">{{ $project->key }} — {{ $project->name }}</h2>
                <div class="small text-body-secondary d-flex flex-wrap gap-3">
          <span class="badge bg-body-tertiary text-body">
            {{ ucfirst($project->stage->value ?? 'planning') }}
          </span>
                    @if ($project->organization?->name)
                        <span>Org: {{ $project->organization->name }}</span>
                    @endif
                    @if ($project->teams->isNotEmpty())
                        <span>Teams: {{ $project->teams->pluck('name')->implode(', ') }}</span>
                    @endif
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('projects.timeline', ['project' => $project]) }}" class="btn btn-outline-secondary btn-sm">Timeline</a>
                <a href="{{ route('projects.calendar', ['project' => $project]) }}" class="btn btn-outline-secondary btn-sm">Calendar</a>
                <a href="{{ route('projects.board', ['project' => $project]) }}" class="btn btn-outline-secondary btn-sm">Kanban</a>
                <a href="{{ route('projects.scrum', ['project' => $project]) }}" class="btn btn-outline-secondary btn-sm">Sprint</a>
                @can('issues.create')
                    <a href="{{ route('issues.create', ['project' => $project]) }}" class="btn btn-outline-primary btn-sm">New issue</a>
                @endcan
                @can('update', $project)
                    <a href="{{ route('projects.edit', ['project' => $project]) }}" class="btn btn-secondary btn-sm">Edit Project</a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container">
            <div class="row g-4">
                {{-- Left: Overview --}}
                <div class="col-lg-8 d-flex flex-column gap-4">
                    {{-- Status summary --}}
                    <div class="card">
                        <div class="card-body">
                            <h3 class="h6 mb-2">{{ __('Issues by status') }}</h3>
                            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-2 mt-1">
                                @forelse($statusSummary as $s)
                                    <div class="col">
                                        <div class="d-flex align-items-center justify-content-between rounded border bg-body-tertiary px-3 py-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="rounded-circle d-inline-block" style="width:.5rem;height:.5rem;background:{{ $s['color'] }}"></span>
                                                <span class="small">{{ $s['name'] }}</span>
                                            </div>
                                            <div class="fw-semibold">{{ $s['count'] }}</div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col">
                                        <p class="small text-body-secondary mb-0">{{ __('No statuses configured.') }}</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- My work --}}
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <h3 class="h6 mb-0">{{ __('My assigned issues') }}</h3>
                                <a href="{{ route('issues.index', ['project' => $project, 'assignee' => auth()->id()]) }}" class="small text-decoration-underline">{{ __('View all') }}</a>
                            </div>

                            <div class="mt-2">
                                @forelse($myIssues as $issue)
                                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-start"
                                       href="{{ route('issues.show', ['project' => $project, 'issue' => $issue]) }}">
                                        <div class="me-3">
                                            <div class="fw-medium">{{ $issue->summary }}</div>
                                        </div>
                                        <div class="small text-body-secondary">{{ $issue->updated_at->diffForHumans() }}</div>
                                    </a>
                                @empty
                                    <p class="small text-body-secondary mb-0">{{ __('No issues assigned to you yet.') }}</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Activity --}}
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <h3 class="h6 mb-0">{{ __('Recent activity') }}</h3>
                            </div>

                            <div class="mt-3 d-flex flex-column gap-4">
                                @forelse($activityGroups as $groupLabel => $items)
                                    <div>
                                        <div class="text-uppercase small fw-semibold text-body-secondary">{{ $groupLabel }}</div>
                                        <ul class="list-unstyled mt-2 d-flex flex-column gap-2">
                                            @foreach($items as $i)
                                                <li class="border rounded p-3">
                                                    <div class="d-flex align-items-start gap-3">
                                                        <img
                                                            src="{{ $i['actor_avatar'] ?? asset('images/default-avatar.png') }}"
                                                            alt=""
                                                            class="rounded-circle object-fit-cover"
                                                            style="width: 32px; height: 32px;"
                                                        >
                                                        <div class="flex-grow-1 min-w-0">
                                                            <div class="small">
                                                                <span class="fw-medium">{{ $i['actor_name'] }}</span>
                                                                <span class="text-body-secondary">{{ strtolower($i['verb']) }}</span>
                                                                @if($i['target_url'])
                                                                    <a href="{{ $i['target_url'] }}" class="fw-medium text-decoration-underline">
                                                                        {{ $i['target_label'] }}
                                                                    </a>
                                                                @else
                                                                    <span class="fw-medium">{{ $i['target_label'] }}</span>
                                                                @endif
                                                            </div>
                                                            <div class="small text-body-secondary mt-1">{{ $i['ago'] }}</div>

                                                            @php $statusChange = collect($i['changes'])->firstWhere('key','issue_status_id'); @endphp
                                                            @if($statusChange)
                                                                <div class="small d-flex align-items-center gap-2 mt-2">
                                                                    <span class="text-body-secondary">{{ $statusChange['label'] }}:</span>
                                                                    <span class="badge bg-body-secondary text-body">{{ $statusChange['from'] ?? '—' }}</span>
                                                                    <span>→</span>
                                                                    <span class="badge" style="background-color: {{ $statusChange['to_color'] ?? 'transparent' }}20;">
                                    {{ $statusChange['to'] ?? '—' }}
                                  </span>
                                                                </div>
                                                            @endif

                                                            @if(!empty($i['changes']))
                                                                <div x-data="{ open: false }" class="mt-2">
                                                                    <button type="button" class="btn btn-link btn-sm p-0 text-decoration-underline" @click="open = !open">
                                                                        <span x-show="!open">{{ __('Show details') }}</span>
                                                                        <span x-show="open">{{ __('Hide details') }}</span>
                                                                    </button>
                                                                    <div x-show="open" x-cloak class="mt-2 rounded p-3 bg-body-tertiary d-flex flex-column gap-2 small">
                                                                        @foreach($i['changes'] as $c)
                                                                            <div class="d-flex align-items-start gap-2">
                                                                                <div class="flex-shrink-0 text-body-secondary" style="width:7rem">{{ $c['label'] }}</div>
                                                                                <div class="flex-grow-1">
                                                                                    <div class="d-inline-flex align-items-center gap-2">
                                                                                        <span class="badge bg-white text-body border">{{ $c['from'] ?? '—' }}</span>
                                                                                        <span>→</span>
                                                                                        <span class="badge border" @if($c['to_color']) style="background-color: {{ $c['to_color'] }}20" @endif>
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
                                    <p class="small text-body-secondary mb-0">{{ __('No activity yet.') }}</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Sidebar --}}
                <aside class="col-lg-4 d-flex flex-column gap-4">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="h6 mb-2">{{ __('Project info') }}</h3>
                            <dl class="row small mb-0">
                                @if($project->lead_id)
                                    <dt class="col-5 text-body-secondary">{{ __('Lead') }}</dt>
                                    <dd class="col-7 mb-2">{{ optional($project->users->firstWhere('id', $project->lead_id))->name ?? '—' }}</dd>
                                @endif
                                <dt class="col-5 text-body-secondary">{{ __('Created') }}</dt>
                                <dd class="col-7 mb-2">{{ $project->created_at?->toFormattedDateString() ?? '—' }}</dd>
                                <dt class="col-5 text-body-secondary">{{ __('Teams') }}</dt>
                                <dd class="col-7 mb-0">{{ $project->teams->pluck('name')->implode(', ') ?: '—' }}</dd>
                            </dl>
                        </div>
                    </div>

                    @can('update', $project)
                        <div class="card">
                            <div class="card-body">
                                <h3 class="h6 mb-2">{{ __('Admin') }}</h3>
                                <ul class="list-unstyled small mb-0 d-flex flex-column gap-2">
                                    <li><a class="link-primary text-decoration-underline" href="{{ route('projects.edit', ['project' => $project]) }}">{{ __('Edit details') }}</a></li>
                                    <li><a class="link-primary text-decoration-underline" href="{{ route('projects.transitions', ['project' => $project]) }}">{{ __('Status transitions') }}</a></li>
                                </ul>
                            </div>
                        </div>
                    @endcan

                    @can('issues.create')
                        <div class="card">
                            <div class="card-body">
                                <livewire:issues.quick-create :project="$project"/>
                            </div>
                        </div>
                    @endcan
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
