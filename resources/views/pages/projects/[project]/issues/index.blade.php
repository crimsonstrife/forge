<?php
use App\Models\Project;
use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\View\View;
use function Laravel\Folio\{name, middleware, render};

name('issues.index');
middleware(['auth','verified']);

render(function (View $view, Project $project, Request $request) {
    $issues = Issue::query()
        ->where('project_id', $project->id)
        ->when($request->filled('status'), fn ($q) => $q->whereRelation('status','id',$request->string('status')))
        ->when($request->boolean('assigned_to_me'), fn ($q) => $q->where('assignee_id', auth()->id()))
        ->withMeta()
        ->latest()
        ->paginate(20)
        ->withQueryString();

    return $view->with(compact('project','issues'));
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
        <div class="container">
            <form class="d-flex align-items-center gap-2 mb-3" method="get">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search summary…" class="form-control w-auto" style="min-width: 18rem;">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="assigned_to_me" value="1" id="me" @checked(request('assigned_to_me'))>
                    <label class="form-check-label" for="me">Assigned to me</label>
                </div>
                <button class="btn btn-outline-secondary">Filter</button>
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
                                <span title="Attachments">📎 {{ $issue->attachments_count }}</span>
                                <span title="Comments">💬 {{ $issue->comments_count }}</span>
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
                    <div class="text-center text-body-secondary py-4">No issues yet.</div>
                @endforelse
            </div>

            <div class="mt-3">
                {{ $issues->onEachSide(1)->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
