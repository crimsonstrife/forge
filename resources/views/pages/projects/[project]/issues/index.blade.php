<?php
use App\Models\Project;
use App\Models\Issue;
use App\Models\IssueStatus;
use App\Models\IssueType;
use App\Models\IssuePriority;
use Illuminate\Http\Request;
use Illuminate\View\View;

use function Laravel\Folio\{name, middleware, render};

name('issues.index');
middleware(['auth','verified']);

render(function (View $view, Project $project, Request $request) {
    $term = trim((string) $request->string('q'));

    $numberSearch = null;
    $keySearch = null;

    if ($term !== '') {
        if (preg_match('/^#?(\d+)$/', $term, $m)) {
            $numberSearch = (int) $m[1];
        }
        if (preg_match('/^[A-Z]+-\d+$/i', $term)) {
            $keySearch = strtoupper($term);
        }
    }

    $issues = Issue::query()
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
            $q->whereRelation('status', 'id', (string) $request->string('status'));
        })
        ->when($request->filled('type'), function ($q) use ($request) {
            $q->whereRelation('type', 'id', (string) $request->string('type'));
        })
        ->when($request->filled('priority'), function ($q) use ($request) {
            $q->whereRelation('priority', 'id', (string) $request->string('priority'));
        })
        ->when($request->boolean('assigned_to_me'), function ($q) {
            $q->where('assignee_id', auth()->id());
        })
        ->with(['status:id,name,color', 'priority:id,name', 'assignee:id,name,profile_photo_path'])
        ->withMeta()
        ->latest()
        ->paginate(20)
        ->withQueryString();

    // Preferred: statuses explicitly enabled for this project via pivot
    $statuses = IssueStatus::query()
        ->select('issue_statuses.id', 'issue_statuses.name', 'issue_statuses.color')
        ->whereIn('issue_statuses.id', function ($q) use ($project) {
            $q->select('issue_status_id')
                ->from('project_issue_statuses')
                ->where('project_id', $project->id);
        })
        ->orderBy('issue_statuses.name')
        ->get();

    // Fallback #1: any statuses actually used by issues in this project
    if ($statuses->isEmpty()) {
        $statuses = IssueStatus::query()
            ->select('issue_statuses.id', 'issue_statuses.name', 'issue_statuses.color')
            ->whereIn('issue_statuses.id', Issue::query()
                ->where('project_id', $project->id)
                ->whereNotNull('issue_status_id')
                ->select('issue_status_id')
            )
            ->orderBy('issue_statuses.name')
            ->get();
    }

    // Fallback #2: all statuses (global)
    if ($statuses->isEmpty()) {
        $statuses = IssueStatus::query()
            ->orderBy('name')
            ->get(['id', 'name', 'color']);
    }

    $types = IssueType::query()
        ->orderBy('name')
        ->get(['id','name']);

    $priorities = IssuePriority::query()
        ->orderBy('name')
        ->get(['id','name']);

    return $view->with(compact('project', 'issues', 'statuses', 'types', 'priorities'));
});
?>
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between gap-3">
            <div>
                <h2 class="h4 mb-1">{{ $project->key }} — {{ __('Issues') }}</h2>
                <a href="{{ route('projects.show', ['project' => $project]) }}" class="link-primary small">{{ __('Back to project') }}</a>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('projects.timeline', ['project' => $project]) }}" class="btn btn-outline-secondary btn-sm">Timeline</a>
                <a href="{{ route('projects.calendar', ['project' => $project]) }}" class="btn btn-outline-secondary btn-sm">Calendar</a>
                <a href="{{ route('projects.board', ['project' => $project]) }}" class="btn btn-outline-secondary btn-sm">Kanban</a>
                <a href="{{ route('projects.scrum', ['project' => $project]) }}" class="btn btn-outline-secondary btn-sm">Sprint</a>
                @can('issues.create')
                    <a href="{{ route('issues.create', ['project' => $project]) }}" class="btn btn-primary btn-sm">New issue</a>
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
                        <option value="{{ $s->id }}" @selected((string)request('status') === (string)$s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>

                <select name="type" class="form-select w-auto">
                    <option value="">All types</option>
                    @foreach($types as $t)
                        <option value="{{ $t->id }}" @selected((string)request('type') === (string)$t->id)>{{ $t->name }}</option>
                    @endforeach
                </select>

                <select name="priority" class="form-select w-auto">
                    <option value="">All priorities</option>
                    @foreach($priorities as $p)
                        <option value="{{ $p->id }}" @selected((string)request('priority') === (string)$p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>

                <div class="form-check ms-2">
                    <input class="form-check-input" type="checkbox" name="assigned_to_me" value="1" id="me" @checked(request('assigned_to_me'))>
                    <label class="form-check-label" for="me">Assigned to me</label>
                </div>

                <button class="btn btn-outline-secondary">Filter</button>
                <a href="{{ route('issues.index', ['project' => $project]) }}" class="btn btn-link text-decoration-none">Clear</a>
            </form>

            <div class="list-group">
                @forelse($issues as $issue)
                    <a class="list-group-item list-group-item-action" href="{{ route('issues.show', ['project'=>$project, 'issue'=>$issue]) }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge text-bg-light font-monospace">{{ $issue->key }}</span>
                                <strong>{{ $issue->summary }}</strong>
                            </div>
                            <div class="d-flex align-items-center gap-3 small text-body-secondary">
                                <span class="d-inline-flex align-items-center gap-1">
                                    <span class="rounded-circle d-inline-block" style="width:.5rem;height:.5rem;background: {{ $issue->status?->color ?? '#9ca3af' }}"></span>
                                    {{ $issue->status?->name }}
                                </span>
                                <span>{{ $issue->priority?->name }}</span>
                                @if($issue->assignee)
                                    <span class="d-inline-flex align-items-center gap-2">
                                        <x-avatar :src="$issue->assignee->profile_photo_url" :name="$issue->assignee->name" preset="sm" />
                                        {{ $issue->assignee->name }}
                                    </span>
                                @endif
                                <span title="Attachments"><wa-icon family="solid" name="paperclip" /> {{ $issue->attachments_count ?? "0" }}</span>
                                <span title="Comments"><wa-icon family="solid" name="comment" /> {{ $issue->comments_count ?? "0" }}</span>
                                <span>{{ $issue->updated_at?->diffForHumans() }}</span>
                            </div>
                        </div>
                        @if($issue->description)
                            <div class="mt-1 small text-body-secondary text-truncate">
                                {{ strip_tags($issue->description) }}
                            </div>
                        @endif
                    </a>
                @empty
                    <div class="text-center text-body-secondary py-4">No issues match your filters.</div>
                @endforelse
            </div>

            <div class="mt-3">
                {{ $issues->onEachSide(1)->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
