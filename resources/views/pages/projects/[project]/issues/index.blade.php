<?php

use App\Models\User;
use App\Services\Issues\IssueStatusTransitionService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\Project;
use App\Models\Issue;
use App\Models\IssueStatus;
use App\Models\IssueType;
use App\Models\IssuePriority;
use Illuminate\Http\Request;
use Illuminate\View\View;

use function Laravel\Folio\{name, middleware, render};

name('issues.index');
middleware(['auth', 'verified']);

render(function (View $view, Project $project, Request $request) {
    $term = trim((string)$request->string('q'));

    $numberSearch = null;
    $keySearch = null;

    if ($term !== '') {
        if (preg_match('/^#?(\d+)$/', $term, $m)) {
            $numberSearch = (int)$m[1];
        }
        if (preg_match('/^[A-Z]+-\d+$/i', $term)) {
            $keySearch = strtoupper($term);
        }
    }

    // Sorting (allow-listed)
    $sort = (string)$request->string('sort');
    $allowedSorts = [
        'updated_desc', 'updated_asc',
        'created_desc', 'created_asc',
        'number_desc', 'number_asc',
        'key_desc', 'key_asc',
        'summary_desc', 'summary_asc',
        'priority_desc', 'priority_asc',
        'status_desc', 'status_asc',
        'type_desc', 'type_asc',
        'assignee_desc', 'assignee_asc',
        'attachments_desc', 'attachments_asc',
        'comments_desc', 'comments_asc',
    ];
    if (!in_array($sort, $allowedSorts, true)) {
        $sort = 'updated_desc';
    }

    $query = Issue::query()
        ->select('issues.*')
        // use withCount for attachments and comments for better performance
        ->withCount(['attachments', 'comments'])
        ->selectSub(
            IssuePriority::query()
                ->select('name')
                ->whereColumn('issue_priorities.id', 'issues.issue_priority_id'),
            'priority_name'
        )
        ->selectSub(
            IssueStatus::query()
                ->select('name')
                ->whereColumn('issue_statuses.id', 'issues.issue_status_id'),
            'status_name'
        )
        ->selectSub(
            User::query()
                ->select('name')
                ->whereColumn('users.id', 'issues.assignee_id'),
            'assignee_name'
        )
        ->selectSub(
            IssueType::query()
                ->select('name')
                ->whereColumn('issue_types.id', 'issues.issue_type_id'),
            'type_name'
        )
        ->where('project_id', $project->id)
        ->when($term !== '', function ($q) use ($term, $numberSearch, $keySearch) {
            $q->where(function ($qq) use ($term, $numberSearch, $keySearch) {
                $qq->where('summary', 'like', '%' . $term . '%')
                    ->orWhere('description', 'like', '%' . $term . '%');

                if ($numberSearch !== null) {
                    $qq->orWhere('number', $numberSearch);
                }

                if ($keySearch !== null) {
                    $qq->orWhere('key', $keySearch);
                }
            });
        })
        ->when($request->filled('status'), function ($q) use ($request) {
            $q->whereRelation('status', 'id', (string)$request->string('status'));
        })
        ->when($request->filled('type'), function ($q) use ($request) {
            $q->whereRelation('type', 'id', (string)$request->string('type'));
        })
        ->when($request->filled('priority'), function ($q) use ($request) {
            $q->whereRelation('priority', 'id', (string)$request->string('priority'));
        })
        ->when($request->boolean('assigned_to_me'), function ($q) {
            $q->where('assignee_id', auth()->id());
        })
        ->with(['status:id,name,color', 'priority:id,name', 'assignee:id,name,profile_photo_path'])
        ->withMeta();

    // Apply sorting
    switch ($sort) {
        case 'updated_asc':
            $query->orderBy('updated_at', 'asc');
            break;
        case 'updated_desc':
            $query->orderBy('updated_at', 'desc');
            break;

        case 'created_asc':
            $query->orderBy('created_at', 'asc');
            break;
        case 'created_desc':
            $query->orderBy('created_at', 'desc');
            break;

        case 'number_asc':
            $query->orderBy('number', 'asc');
            break;
        case 'number_desc':
            $query->orderBy('number', 'desc');
            break;

        case 'key_asc':
            $query->orderBy('key', 'asc');
            break;
        case 'key_desc':
            $query->orderBy('key', 'desc');
            break;

        case 'summary_asc':
            $query->orderBy('summary', 'asc');
            break;
        case 'summary_desc':
            $query->orderBy('summary', 'desc');
            break;

        case 'priority_asc':
            $query->orderByRaw("COALESCE(priority_name, 'zzzz') ASC")
                ->orderBy('updated_at', 'desc');
            break;
        case 'priority_desc':
            $query->orderByRaw("COALESCE(priority_name, '') DESC")
                ->orderBy('updated_at', 'desc');
            break;

        case 'status_asc':
            $query->orderByRaw("COALESCE(status_name, 'zzzz') ASC")
                ->orderBy('updated_at', 'desc');
            break;
        case 'status_desc':
            $query->orderByRaw("COALESCE(status_name, '') DESC")
                ->orderBy('updated_at', 'desc');
            break;

        case 'type_asc':
            $query->orderByRaw("COALESCE(type_name, 'zzzz') ASC")
                ->orderBy('updated_at', 'desc');
            break;
        case 'type_desc':
            $query->orderByRaw("COALESCE(type_name, '') DESC")
                ->orderBy('updated_at', 'desc');
            break;

        case 'assignee_asc':
            $query->orderByRaw("COALESCE(assignee_name, 'zzzz') ASC")
                ->orderBy('updated_at', 'desc');
            break;
        case 'assignee_desc':
            $query->orderByRaw("COALESCE(assignee_name, '') DESC")
                ->orderBy('updated_at', 'desc');
            break;

        case 'attachments_asc':
            $query->orderBy('attachments_count', 'asc')->orderBy('updated_at', 'desc');
            break;
        case 'attachments_desc':
            $query->orderBy('attachments_count', 'desc')->orderBy('updated_at', 'desc');
            break;

        case 'comments_asc':
            $query->orderBy('comments_count', 'asc')->orderBy('updated_at', 'desc');
            break;
        case 'comments_desc':
            $query->orderBy('comments_count', 'desc')->orderBy('updated_at', 'desc');
            break;
    }

    $issues = $query->paginate(20)->withQueryString();

    /** Allowed transitions per issue (current page only) */
    $transitionService = app(IssueStatusTransitionService::class);
    /** @var array<string, Collection<IssueStatus>> $allowedTransitions */
    $allowedTransitions = [];
    foreach ($issues as $row) {
        $allowedTransitions[$row->id] = $transitionService->allowedToStatusesForIssue($row);
    }

    // Status filters (same as before)
    $statuses = IssueStatus::query()
        ->select('issue_statuses.id', 'issue_statuses.name', 'issue_statuses.color')
        ->whereIn('issue_statuses.id', function ($q) use ($project) {
            $q->select('issue_status_id')
                ->from('project_issue_statuses')
                ->where('project_id', $project->id);
        })
        ->orderBy('issue_statuses.name')
        ->get();

    if ($statuses->isEmpty()) {
        $statuses = IssueStatus::query()
            ->select('issue_statuses.id', 'issue_statuses.name', 'issue_statuses.color')
            ->whereIn(
                'issue_statuses.id',
                Issue::query()
                    ->where('project_id', $project->id)
                    ->whereNotNull('issue_status_id')
                    ->select('issue_status_id')
            )
            ->orderBy('issue_statuses.name')
            ->get();
    }

    if ($statuses->isEmpty()) {
        $statuses = IssueStatus::query()
            ->orderBy('name')
            ->get(['id', 'name', 'color']);
    }

    $types = IssueType::query()->orderBy('name')->get(['id', 'name']);
    $priorities = IssuePriority::query()->orderBy('name')->get(['id', 'name']);

    return $view->with(compact('project', 'issues', 'statuses', 'types', 'priorities', 'allowedTransitions'));
});
?>
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between gap-3">
            <div>
                <h2 class="h4 mb-1">{{ $project->key }} — {{ __('Issues') }}</h2>
                <a href="{{ route('projects.show', ['project' => $project]) }}"
                   class="link-primary small">{{ __('Back to project') }}</a>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('projects.timeline', ['project' => $project]) }}"
                   class="btn btn-outline-secondary btn-sm">Timeline</a>
                <a href="{{ route('projects.calendar', ['project' => $project]) }}"
                   class="btn btn-outline-secondary btn-sm">Calendar</a>
                <a href="{{ route('projects.board', ['project' => $project]) }}"
                   class="btn btn-outline-secondary btn-sm">Kanban</a>
                <a href="{{ route('projects.scrum', ['project' => $project]) }}"
                   class="btn btn-outline-secondary btn-sm">Sprint</a>
                @can('issues.create')
                    <a href="{{ route('issues.create', ['project' => $project]) }}" class="btn btn-primary btn-sm">New
                        issue</a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container mx-auto py-4">
            <form class="d-flex flex-wrap align-items-center gap-2 mb-3" method="get" role="search">
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       placeholder="Search summary, description, #123, ABC-123…"
                       class="form-control w-auto"
                       style="min-width: 18rem;">

                <select name="status" class="form-select w-auto">
                    <option value="">All statuses</option>
                    @foreach($statuses as $s)
                        <option
                            value="{{ $s->id }}" @selected((string)request('status') === (string)$s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>

                <select name="type" class="form-select w-auto">
                    <option value="">All types</option>
                    @foreach($types as $t)
                        <option
                            value="{{ $t->id }}" @selected((string)request('type') === (string)$t->id)>{{ $t->name }}</option>
                    @endforeach
                </select>

                <select name="priority" class="form-select w-auto">
                    <option value="">All priorities</option>
                    @foreach($priorities as $p)
                        <option
                            value="{{ $p->id }}" @selected((string)request('priority') === (string)$p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>

                <div class="form-check ms-2">
                    <input class="form-check-input" type="checkbox" name="assigned_to_me" value="1"
                           id="me" @checked(request('assigned_to_me'))>
                    <label class="form-check-label" for="me">Assigned to me</label>
                </div>

                <button class="btn btn-outline-secondary">Filter</button>

                <a href="{{ route('issues.index', ['project' => $project]) }}"
                   class="btn btn-link text-decoration-none">Clear</a>
            </form>

            @php
                $currentSort = request('sort', 'updated_desc');
                [$sortCol, $sortDir] = array_pad(explode('_', $currentSort), 2, 'desc');

                $sortLink = static function (string $column) use ($sortCol, $sortDir) {
                    $next = ($sortCol === $column && $sortDir === 'asc') ? 'desc' : 'asc';
                    return request()->fullUrlWithQuery(['sort' => "{$column}_{$next}", 'page' => null]);
                };

                $sortIndicator = static function (string $column) use ($sortCol, $sortDir) {
                    if ($sortCol !== $column) { return ''; }
                    return $sortDir === 'asc' ? '↑' : '↓';
                };
            @endphp

            <div class="table-responsive" style="min-height: 25rem;">
                <table class="table table-hover align-middle">
                    <thead>
                    <tr>
                        <th class="text-nowrap">
                            <a class="link-body-emphasis text-decoration-none" href="{{ $sortLink('key') }}">
                                Key {{ $sortIndicator('key') }}
                            </a>
                        </th>
                        <th class="text-nowrap">
                            <a class="link-body-emphasis text-decoration-none" href="{{ $sortLink('type') }}">
                                Type {{ $sortIndicator('type') }}
                            </a>
                        </th>
                        <th class="text-wrap" style="max-width: 25rem;">
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
                        <th class="text-nowrap text-center">
                            <a class="link-body-emphasis text-decoration-none" href="{{ $sortLink('attachments') }}">
                                <span title="Attachments"><wa-icon family="solid"
                                                                   name="paperclip"/> {{ $sortIndicator('attachments') }}
                            </a>
                        </th>
                        <th class="text-nowrap text-center">
                            <a class="link-body-emphasis text-decoration-none" href="{{ $sortLink('comments') }}">
                                <span title="Comments"><wa-icon family="solid"
                                                                name="comments"/></span> {{ $sortIndicator('comments') }}
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
                                <a class="badge text-bg-light font-monospace text-decoration-none"
                                   href="{{ route('issues.show', ['project'=>$project, 'issue'=>$issue]) }}">
                                    {{ $issue->key }}
                                </a>
                            </td>
                            <td class="text-nowrap">
                                {{ $issue->type?->name ?? '—' }}
                            </td>
                            <td class="text-wrap" style="max-width: 25rem;overflow-wrap: normal;text-wrap: wrap;">
                                <a class="text-decoration-none"
                                   href="{{ route('issues.show', ['project'=>$project, 'issue'=>$issue]) }}">
                                    <strong>{{ $issue->summary }}</strong>
                                    @if($issue->description)
                                        <div
                                            class="small text-body-secondary text-truncate">{{ strip_tags($issue->description) }}</div>
                                    @endif
                                </a>
                            </td>
                            <td class="text-nowrap">
                    <span class="d-inline-flex align-items-center gap-1">
                        <span class="rounded-circle d-inline-block"
                              style="width:.5rem;height:.5rem;background: {{ $issue->status?->color ?? '#9ca3af' }}"></span>
                        {{ $issue->status?->name ?? '—' }}
                    </span>
                            </td>
                            <td class="text-nowrap">
                                {{ $issue->priority?->name ?? '—' }}
                            </td>
                            <td class="text-nowrap">
                                @if($issue->assignee)
                                    <span class="d-inline-flex align-items-center gap-2">
                            <x-avatar :src="$issue->assignee->profile_photo_url" :name="$issue->assignee->name"
                                      preset="sm"/>
                            {{ $issue->assignee->name }}
                        </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-center">{{ (int) ($issue->attachments_count ?? 0) }}</td>
                            <td class="text-center">{{ (int) ($issue->comments_count ?? 0) }}</td>
                            <td class="text-nowrap">{{ $issue->updated_at?->diffForHumans() }}</td>
                            <td class="text-nowrap text-end">
                                @can('update', $issue)
                                    <div class="btn-group">
                                        <a href="{{ route('issues.edit', ['project' => $project, 'issue' => $issue]) }}"
                                           class="btn btn-sm btn-outline-secondary">
                                            Edit
                                        </a>

                                        <button class="btn btn-sm btn-outline-primary dropdown-toggle"
                                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Status
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @php $allowed = $allowedTransitions[$issue->id] ?? collect(); @endphp
                                            @forelse($allowed as $st)
                                                <li>
                                                    <form method="POST"
                                                          action="{{ route('issues.transition', ['project' => $project, 'issue' => $issue]) }}">
                                                        @csrf
                                                        <input type="hidden" name="to_status_id" value="{{ $st->id }}">
                                                        {{-- return to the same filtered/sorted page --}}
                                                        <input type="hidden" name="redirect"
                                                               value="{{ request()->fullUrl() }}">
                                                        <button type="submit"
                                                                class="dropdown-item d-flex align-items-center gap-2">
                                <span class="rounded-circle d-inline-block"
                                      style="width:.5rem;height:.5rem;background: {{ $st->color ?? '#9ca3af' }}"></span>
                                                            <span>{{ $st->name }}</span>
                                                        </button>
                                                    </form>
                                                </li>
                                            @empty
                                                <li><span
                                                        class="dropdown-item-text text-body-secondary">No transitions</span>
                                                </li>
                                            @endforelse
                                        </ul>
                                    </div>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-body-secondary py-4">No issues match your
                                filters.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $issues->onEachSide(1)->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
