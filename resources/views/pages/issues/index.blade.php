<?php

use App\Models\Issue;
use App\Models\IssuePriority;
use App\Models\IssueStatus;
use App\Models\IssueType;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\SavedIssueView;
use App\Models\Sprint;
use App\Models\User;
use App\Services\Issues\IssueExplorerService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

use function Laravel\Folio\{middleware, name, render};

name('issues.explorer');
middleware(['auth', 'verified']);

render(function (View $view, Request $request, IssueExplorerService $explorer) {
    /** @var User $user */
    $user = auth()->user();

    abort_unless($user->can('issues.view') || $user->hasPermissionTo('is-super-admin'), 403);

    $projects = $explorer->visibleProjectsQuery($user)
        ->orderBy('name')
        ->get(['id', 'name', 'key']);

    $projectIds = $projects->pluck('id');

    $visibleIssues = Issue::query()->whereIn('project_id', $projectIds);

    $activeView = null;
    $viewSlug = trim((string) $request->query('view', ''));

    if ($viewSlug !== '') {
        $activeView = SavedIssueView::query()
            ->visibleTo($user)
            ->with(['user:id,name', 'team:id,name'])
            ->where('slug', $viewSlug)
            ->firstOrFail();
    }

    $savedState = $activeView
        ? [
            'query' => (string) ($activeView->query ?? ''),
            'sort' => (string) ($activeView->sort ?: 'updated_desc'),
            'next' => (bool) Arr::get($activeView->filters, 'next', false),
            'filters' => collect(IssueExplorerService::FILTER_KEYS)
                ->mapWithKeys(fn (string $key): array => [$key => (string) Arr::get($activeView->filters, $key, '')])
                ->all(),
        ]
        : [];

    $state = $explorer->stateFromRequest($request, $savedState);
    $queryParams = $explorer->toQueryParameters($state, $activeView?->slug);
    $saveState = $explorer->stateForStorage($state);

    $issues = $explorer->apply(
        $explorer->baseQuery($user),
        $user,
        $state
    )->paginate(20)->appends($queryParams);

    $assigneeIds = (clone $visibleIssues)
        ->whereNotNull('assignee_id')
        ->distinct()
        ->pluck('assignee_id');

    $reporterIds = (clone $visibleIssues)
        ->whereNotNull('reporter_id')
        ->distinct()
        ->pluck('reporter_id');

    $assignees = User::query()
        ->whereIn('id', $assigneeIds)
        ->orderBy('name')
        ->get(['id', 'name']);

    $reporters = User::query()
        ->whereIn('id', $reporterIds)
        ->orderBy('name')
        ->get(['id', 'name']);

    $statuses = IssueStatus::query()->orderBy('name')->get(['id', 'name', 'color']);
    $types = IssueType::query()->orderBy('name')->get(['id', 'name']);
    $priorities = IssuePriority::query()->orderBy('name')->get(['id', 'name']);

    $milestones = Milestone::query()
        ->whereIn('project_id', $projectIds)
        ->orderBy('name')
        ->get(['id', 'project_id', 'name', 'version']);

    $sprints = Sprint::query()
        ->whereIn('project_id', $projectIds)
        ->orderBy('name')
        ->get(['id', 'project_id', 'name']);

    $savedViews = SavedIssueView::query()
        ->visibleTo($user)
        ->with('user:id,name')
        ->orderBy('name')
        ->get();

    [$myViews, $sharedViews] = $savedViews->partition(
        fn (SavedIssueView $savedView): bool => $savedView->isOwnedBy($user)
    );

    return $view->with(compact(
        'activeView',
        'assignees',
        'issues',
        'milestones',
        'myViews',
        'priorities',
        'projects',
        'queryParams',
        'reporters',
        'saveState',
        'sharedViews',
        'sprints',
        'state',
        'statuses',
        'types'
    ));
});
?>
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
            <div>
                <h2 class="h4 mb-1">{{ __('Issue Explorer') }}</h2>
                <p class="text-body-secondary mb-0">Query work across projects, then save and share the views that matter.</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ url('/issues') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                <a href="{{ route('issues.create.global') }}" class="btn btn-primary btn-sm">New issue</a>
            </div>
        </div>
    </x-slot>

    @php
        $explorerUrl = static function (array $params = []) {
            $query = http_build_query(array_filter(
                $params,
                static fn (mixed $value): bool => $value !== null && $value !== ''
            ));

            return $query !== '' ? url('/issues').'?'.$query : url('/issues');
        };
        $currentSort = $state['sort'];
        [$sortCol, $sortDir] = array_pad(explode('_', $currentSort), 2, 'desc');
        $sortLink = static function (string $column) use ($explorerUrl, $queryParams, $sortCol, $sortDir) {
            $next = ($sortCol === $column && $sortDir === 'asc') ? 'desc' : 'asc';

            return $explorerUrl(array_merge($queryParams, [
                'sort' => "{$column}_{$next}",
                'page' => null,
            ]));
        };
        $sortIndicator = static function (string $column) use ($sortCol, $sortDir) {
            if ($sortCol !== $column) {
                return '';
            }

            return $sortDir === 'asc' ? '↑' : '↓';
        };
        $saveStateJson = e(json_encode($saveState));
    @endphp

    <div class="py-4">
        <div class="container mx-auto py-4">
            <div class="row g-4">
                <div class="col-12 col-xl-3">
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h3 class="h6 mb-0">Saved views</h3>
                                @if($activeView)
                                    <span class="badge text-bg-primary">Active</span>
                                @endif
                            </div>

                            @if($activeView)
                                <div class="rounded border bg-body-tertiary p-3 mb-3">
                                    <div class="fw-semibold">{{ $activeView->name }}</div>
                                    <div class="small text-body-secondary mt-1">
                                        {{ $activeView->isOwnedBy(auth()->user()) ? 'Your view' : 'Shared by '.$activeView->user->name }}
                                        @if($activeView->is_shared)
                                            <span class="ms-1">• Shared</span>
                                        @endif
                                    </div>
                                    @if($activeView->isOwnedBy(auth()->user()))
                                        <form method="POST" action="{{ route('issues.views.destroy', $activeView) }}" class="mt-3">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">Delete view</button>
                                        </form>
                                    @endif
                                </div>
                            @endif

                            <form method="POST" action="{{ route('issues.views.store') }}" class="vstack gap-3">
                                @csrf
                                <div>
                                    <label for="saved-view-name" class="form-label small text-uppercase text-body-secondary">Save current view</label>
                                    <input id="saved-view-name"
                                           type="text"
                                           name="name"
                                           class="form-control"
                                           maxlength="80"
                                           placeholder="Release blockers"
                                           required>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_shared" id="saved-view-shared" value="1">
                                    <label class="form-check-label" for="saved-view-shared">
                                        Share with current team
                                    </label>
                                </div>

                                <input type="hidden" name="state" value="{{ $saveStateJson }}">

                                <button type="submit" class="btn btn-outline-primary btn-sm">Save view</button>
                            </form>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <h3 class="h6 mb-3">My views</h3>
                            <div class="list-group list-group-flush">
                                @forelse($myViews as $savedView)
                                    <a href="{{ $explorerUrl(['view' => $savedView->slug]) }}"
                                       class="list-group-item list-group-item-action px-0 {{ $activeView?->id === $savedView->id ? 'active border-0 rounded' : 'border-0' }}">
                                        <div class="fw-medium">{{ $savedView->name }}</div>
                                        <div class="small {{ $activeView?->id === $savedView->id ? 'text-white-50' : 'text-body-secondary' }}">
                                            {{ $savedView->is_shared ? 'Shared with team' : 'Private view' }}
                                        </div>
                                    </a>
                                @empty
                                    <div class="small text-body-secondary">No saved views yet.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <h3 class="h6 mb-3">Shared views</h3>
                            <div class="list-group list-group-flush">
                                @forelse($sharedViews as $savedView)
                                    <a href="{{ $explorerUrl(['view' => $savedView->slug]) }}"
                                       class="list-group-item list-group-item-action px-0 {{ $activeView?->id === $savedView->id ? 'active border-0 rounded' : 'border-0' }}">
                                        <div class="fw-medium">{{ $savedView->name }}</div>
                                        <div class="small {{ $activeView?->id === $savedView->id ? 'text-white-50' : 'text-body-secondary' }}">
                                            Shared by {{ $savedView->user->name }}
                                        </div>
                                    </a>
                                @empty
                                    <div class="small text-body-secondary">No team views yet.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h3 class="h6 mb-3">Query syntax</h3>
                            <div class="small text-body-secondary">
                                <div><code>project:FORGE status:"In Progress" assignee:me</code></div>
                                <div class="mt-2"><code>ticket:SD-104 due&lt;=2026-03-31</code></div>
                                <div class="mt-2"><code>tag:ops is:next updated&gt;=2026-03-01</code></div>
                                <div class="mt-3">Supported fields: <code>project</code>, <code>assignee</code>, <code>reporter</code>, <code>status</code>, <code>type</code>, <code>priority</code>, <code>milestone</code>, <code>sprint</code>, <code>tag</code>, <code>ticket</code>, <code>due</code>, <code>updated</code>, <code>is</code>, <code>has</code>.</div>
                                <div class="mt-2">Repeated field clauses OR together. Free-text terms AND together.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-9">
                    <div class="card shadow-sm mb-3" data-tour="issues-explorer-query">
                        <div class="card-body">
                            <form method="GET" action="{{ url('/issues') }}" class="vstack gap-3">
                                @if($activeView)
                                    <input type="hidden" name="view" value="{{ $activeView->slug }}">
                                @endif
                                <input type="hidden" name="next" value="0">

                                <div class="row g-3 align-items-end">
                                    <div class="col-12 col-lg-8">
                                        <label for="issue-query" class="form-label small text-uppercase text-body-secondary">Query</label>
                                        <input id="issue-query"
                                               type="text"
                                               name="query"
                                               value="{{ $state['query'] }}"
                                               class="form-control"
                                               placeholder='project:FORGE status:"In Progress" assignee:me'>
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-2">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="next" value="1" id="issue-next" @checked($state['next'])>
                                            <label class="form-check-label" for="issue-next">Only next</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-2 d-flex gap-2">
                                        <button type="submit" class="btn btn-primary w-100">Apply</button>
                                        <a href="{{ url('/issues') }}" class="btn btn-outline-secondary">Reset</a>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="filter-project" class="form-label small text-uppercase text-body-secondary">Project</label>
                                        <select id="filter-project" name="project" class="form-select">
                                            <option value="">All projects</option>
                                            @foreach($projects as $project)
                                                <option value="{{ $project->id }}" @selected($state['filters']['project'] === (string) $project->id)>
                                                    {{ $project->key }} — {{ $project->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="filter-assignee" class="form-label small text-uppercase text-body-secondary">Assignee</label>
                                        <select id="filter-assignee" name="assignee" class="form-select">
                                            <option value="">All assignees</option>
                                            <option value="me" @selected($state['filters']['assignee'] === 'me')>Assigned to me</option>
                                            <option value="unassigned" @selected($state['filters']['assignee'] === 'unassigned')>Unassigned</option>
                                            @foreach($assignees as $assignee)
                                                <option value="{{ $assignee->id }}" @selected($state['filters']['assignee'] === (string) $assignee->id)>
                                                    {{ $assignee->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="filter-reporter" class="form-label small text-uppercase text-body-secondary">Reporter</label>
                                        <select id="filter-reporter" name="reporter" class="form-select">
                                            <option value="">All reporters</option>
                                            <option value="me" @selected($state['filters']['reporter'] === 'me')>Reported by me</option>
                                            @foreach($reporters as $reporter)
                                                <option value="{{ $reporter->id }}" @selected($state['filters']['reporter'] === (string) $reporter->id)>
                                                    {{ $reporter->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-3">
                                        <label for="filter-status" class="form-label small text-uppercase text-body-secondary">Status</label>
                                        <select id="filter-status" name="status" class="form-select">
                                            <option value="">All statuses</option>
                                            @foreach($statuses as $status)
                                                <option value="{{ $status->id }}" @selected($state['filters']['status'] === (string) $status->id)>
                                                    {{ $status->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-3">
                                        <label for="filter-type" class="form-label small text-uppercase text-body-secondary">Type</label>
                                        <select id="filter-type" name="type" class="form-select">
                                            <option value="">All types</option>
                                            @foreach($types as $type)
                                                <option value="{{ $type->id }}" @selected($state['filters']['type'] === (string) $type->id)>
                                                    {{ $type->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-3">
                                        <label for="filter-priority" class="form-label small text-uppercase text-body-secondary">Priority</label>
                                        <select id="filter-priority" name="priority" class="form-select">
                                            <option value="">All priorities</option>
                                            @foreach($priorities as $priority)
                                                <option value="{{ $priority->id }}" @selected($state['filters']['priority'] === (string) $priority->id)>
                                                    {{ $priority->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-3">
                                        <label for="filter-sprint" class="form-label small text-uppercase text-body-secondary">Sprint</label>
                                        <select id="filter-sprint" name="sprint" class="form-select">
                                            <option value="">Any sprint</option>
                                            <option value="backlog" @selected($state['filters']['sprint'] === 'backlog')>Backlog</option>
                                            @foreach($sprints as $sprint)
                                                <option value="{{ $sprint->id }}" @selected($state['filters']['sprint'] === (string) $sprint->id)>
                                                    {{ $sprint->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="filter-milestone" class="form-label small text-uppercase text-body-secondary">Milestone</label>
                                        <select id="filter-milestone" name="milestone" class="form-select">
                                            <option value="">Any milestone</option>
                                            <option value="none" @selected($state['filters']['milestone'] === 'none')>No milestone</option>
                                            @foreach($milestones as $milestone)
                                                <option value="{{ $milestone->id }}" @selected($state['filters']['milestone'] === (string) $milestone->id)>
                                                    {{ $milestone->name }}@if($milestone->version) — {{ $milestone->version }}@endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="filter-tag" class="form-label small text-uppercase text-body-secondary">Tag</label>
                                        <input id="filter-tag"
                                               type="text"
                                               name="tag"
                                               value="{{ $state['filters']['tag'] }}"
                                               class="form-control"
                                               placeholder="ops">
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="filter-ticket" class="form-label small text-uppercase text-body-secondary">Linked ticket</label>
                                        <input id="filter-ticket"
                                               type="text"
                                               name="ticket"
                                               value="{{ $state['filters']['ticket'] }}"
                                               class="form-control"
                                               placeholder="SD-104">
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-3">
                                        <label for="filter-due-from" class="form-label small text-uppercase text-body-secondary">Due from</label>
                                        <input id="filter-due-from" type="date" name="due_from" value="{{ $state['filters']['due_from'] }}" class="form-control">
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-3">
                                        <label for="filter-due-to" class="form-label small text-uppercase text-body-secondary">Due to</label>
                                        <input id="filter-due-to" type="date" name="due_to" value="{{ $state['filters']['due_to'] }}" class="form-control">
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-3">
                                        <label for="filter-updated-from" class="form-label small text-uppercase text-body-secondary">Updated from</label>
                                        <input id="filter-updated-from" type="date" name="updated_from" value="{{ $state['filters']['updated_from'] }}" class="form-control">
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-3">
                                        <label for="filter-updated-to" class="form-label small text-uppercase text-body-secondary">Updated to</label>
                                        <input id="filter-updated-to" type="date" name="updated_to" value="{{ $state['filters']['updated_to'] }}" class="form-control">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <div>
                            <div class="small text-uppercase text-body-secondary">Results</div>
                            <div class="h5 mb-0">{{ number_format($issues->total()) }} issues</div>
                        </div>
                        @if($activeView)
                            <div class="small text-body-secondary">
                                Using saved view <strong>{{ $activeView->name }}</strong>
                            </div>
                        @endif
                    </div>

                    <div class="card shadow-sm">
                        <div class="table-responsive" style="min-height: 28rem;">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                <tr>
                                    <th class="text-nowrap">
                                        <a class="link-body-emphasis text-decoration-none" href="{{ $sortLink('project') }}">
                                            Project {{ $sortIndicator('project') }}
                                        </a>
                                    </th>
                                    <th class="text-nowrap">
                                        <a class="link-body-emphasis text-decoration-none" href="{{ $sortLink('key') }}">
                                            Key {{ $sortIndicator('key') }}
                                        </a>
                                    </th>
                                    <th style="min-width: 24rem;">
                                        <a class="link-body-emphasis text-decoration-none" href="{{ $sortLink('summary') }}">
                                            Summary {{ $sortIndicator('summary') }}
                                        </a>
                                    </th>
                                    <th class="text-nowrap">
                                        <a class="link-body-emphasis text-decoration-none" href="{{ $sortLink('status') }}">
                                            Status {{ $sortIndicator('status') }}
                                        </a>
                                    </th>
                                    <th class="text-nowrap">
                                        <a class="link-body-emphasis text-decoration-none" href="{{ $sortLink('priority') }}">
                                            Priority {{ $sortIndicator('priority') }}
                                        </a>
                                    </th>
                                    <th class="text-nowrap">
                                        <a class="link-body-emphasis text-decoration-none" href="{{ $sortLink('assignee') }}">
                                            Assignee {{ $sortIndicator('assignee') }}
                                        </a>
                                    </th>
                                    <th class="text-nowrap">
                                        <a class="link-body-emphasis text-decoration-none" href="{{ $sortLink('reporter') }}">
                                            Reporter {{ $sortIndicator('reporter') }}
                                        </a>
                                    </th>
                                    <th class="text-nowrap">
                                        <a class="link-body-emphasis text-decoration-none" href="{{ $sortLink('milestone') }}">
                                            Milestone {{ $sortIndicator('milestone') }}
                                        </a>
                                    </th>
                                    <th class="text-nowrap">
                                        <a class="link-body-emphasis text-decoration-none" href="{{ $sortLink('sprint') }}">
                                            Sprint {{ $sortIndicator('sprint') }}
                                        </a>
                                    </th>
                                    <th class="text-nowrap text-center">Tickets</th>
                                    <th class="text-nowrap">
                                        <a class="link-body-emphasis text-decoration-none" href="{{ $sortLink('due') }}">
                                            Due {{ $sortIndicator('due') }}
                                        </a>
                                    </th>
                                    <th class="text-nowrap">
                                        <a class="link-body-emphasis text-decoration-none" href="{{ $sortLink('updated') }}">
                                            Updated {{ $sortIndicator('updated') }}
                                        </a>
                                    </th>
                                    <th class="text-nowrap text-end">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($issues as $issue)
                                    <tr>
                                        <td class="text-nowrap">
                                            <div class="fw-semibold">{{ $issue->project?->key ?? '—' }}</div>
                                            <div class="small text-body-secondary">{{ $issue->project_name ?? 'Unknown project' }}</div>
                                        </td>
                                        <td class="text-nowrap">
                                            <a class="badge text-bg-light font-monospace text-decoration-none"
                                               href="{{ route('issues.show', ['project' => $issue->project, 'issue' => $issue]) }}">
                                                {{ $issue->key }}
                                            </a>
                                        </td>
                                        <td>
                                            <a class="text-decoration-none d-block"
                                               href="{{ route('issues.show', ['project' => $issue->project, 'issue' => $issue]) }}">
                                                <div class="fw-semibold text-body-emphasis">{{ $issue->summary }}</div>
                                                @if($issue->description)
                                                    <div class="small text-body-secondary text-truncate">
                                                        {{ strip_tags($issue->description) }}
                                                    </div>
                                                @endif
                                            </a>
                                            <div class="d-flex flex-wrap gap-1 mt-2">
                                                @if($issue->type?->name)
                                                    <span class="badge text-bg-light">{{ $issue->type->name }}</span>
                                                @endif
                                                @if($issue->is_next)
                                                    <span class="badge text-bg-primary">Next</span>
                                                @endif
                                                @foreach($issue->tags->take(3) as $tag)
                                                    <span class="badge text-bg-secondary">{{ $tag->name }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="text-nowrap">
                                            <span class="d-inline-flex align-items-center gap-1">
                                                <span class="rounded-circle d-inline-block"
                                                      style="width:.5rem;height:.5rem;background: {{ $issue->status?->color ?? '#9ca3af' }}"></span>
                                                {{ $issue->status?->name ?? '—' }}
                                            </span>
                                        </td>
                                        <td class="text-nowrap">{{ $issue->priority?->name ?? '—' }}</td>
                                        <td class="text-nowrap">
                                            @if($issue->assignee)
                                                <span class="d-inline-flex align-items-center gap-2">
                                                    <x-avatar :src="$issue->assignee->profile_photo_url" :name="$issue->assignee->name" preset="sm"/>
                                                    {{ $issue->assignee->name }}
                                                </span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="text-nowrap">
                                            @if($issue->reporter)
                                                <span class="d-inline-flex align-items-center gap-2">
                                                    <x-avatar :src="$issue->reporter->profile_photo_url" :name="$issue->reporter->name" preset="sm"/>
                                                    {{ $issue->reporter->name }}
                                                </span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="text-nowrap">{{ $issue->milestone?->name ?? '—' }}</td>
                                        <td class="text-nowrap">{{ $issue->sprint?->name ?? 'Backlog' }}</td>
                                        <td class="text-center">{{ (int) $issue->tickets_count }}</td>
                                        <td class="text-nowrap {{ $issue->due_at && $issue->due_at->isPast() ? 'text-danger' : '' }}">
                                            @if($issue->due_at)
                                                <div>{{ $issue->due_at->format('M j, Y') }}</div>
                                                <div class="small text-body-secondary">{{ $issue->due_at->diffForHumans() }}</div>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="text-nowrap">{{ $issue->updated_at?->diffForHumans() }}</td>
                                        <td class="text-nowrap text-end">
                                            <div class="btn-group">
                                                <a href="{{ route('issues.show', ['project' => $issue->project, 'issue' => $issue]) }}"
                                                   class="btn btn-sm btn-outline-secondary">Open</a>
                                                @can('update', $issue)
                                                    <a href="{{ route('issues.edit', ['project' => $issue->project, 'issue' => $issue]) }}"
                                                       class="btn btn-sm btn-outline-primary">Edit</a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="13" class="text-center text-body-secondary py-5">
                                            No issues match the current filters.
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-3">
                        {{ $issues->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
