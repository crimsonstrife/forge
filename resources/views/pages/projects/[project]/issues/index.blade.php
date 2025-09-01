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
        ->when($request->filled('status'), fn ($q) => $q->whereRelation('status', 'id', $request->string('status')))
        ->when($request->boolean('assigned_to_me'), fn ($q) => $q->where('assignee_id', auth()->id()))
        ->withMeta()
        ->latest()
        ->paginate(20)
        ->withQueryString();

    return $view->with(compact('project', 'issues'));
});
?>

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
                    {{ $project->key }} — Issues
                </h2>
                <a href="{{ route('projects.show', ['project' => $project]) }}" class="text-sm text-primary-600 hover:underline">Back to project</a>
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
                    <a href="{{ route('issues.create', ['project' => $project]) }}" class="inline-flex items-center rounded-lg px-3 py-2 border">New issue</a>
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
        <div class="mx-auto max-w-7xl space-y-4">
            <form class="mt-3 flex gap-2" method="get">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search summary..."
                       class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2 w-64">
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" name="assigned_to_me" value="1" @checked(request('assigned_to_me'))>
                    Assigned to me
                </label>
                <button class="rounded-lg px-3 py-2 border border-gray-300 dark:border-gray-700">Filter</button>
            </form>
            <div class="divide-y divide-gray-200/60 dark:divide-gray-700/60 rounded-xl border border-gray-200/60 dark:border-gray-700/60 bg-white dark:bg-gray-800">
                @forelse($issues as $issue)
                    <a class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-800/60"
                       href="{{ route('issues.show', ['project'=>$project, 'issue'=>$issue]) }}">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-mono px-2 py-1 rounded bg-gray-100 dark:bg-gray-700">
                                    {{ $issue->key }}
                                </span>
                                <div class="font-medium">{{ $issue->summary }}</div>
                            </div>
                            <div class="flex items-center gap-3 text-xs text-gray-500">
                                <span class="inline-flex items-center gap-1">
                                    <span class="h-2 w-2 rounded-full" style="background: {{ $issue->status?->color ?? '#9ca3af' }}"></span>
                                    {{ $issue->status?->name }}
                                </span>
                                <span>{{ $issue->priority?->name }}</span>
                                @if($issue->assignee)
                                    <span class="inline-flex items-center gap-2">
                                        <img src="{{ $issue->assignee->profile_photo_url }}" class="h-5 w-5 rounded-full" alt="">
                                        {{ $issue->assignee->name }}
                                    </span>
                                @endif
                                <span title="Attachments">📎 {{ $issue->attachments_count }}</span>
                                <span title="Comments">💬 {{ $issue->comments_count }}</span>
                                <span>{{ $issue->updated_at?->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div class="mt-1 text-sm text-gray-600 dark:text-gray-300 line-clamp-2">
                            {{ $issue->description }}
                        </div>
                    </a>
                @empty
                    <div class="p-6 text-center text-gray-500">No issues yet.</div>
                @endforelse
            </div>

            {{ $issues->onEachSide(1)->links() }}
        </div>
    </div>
</x-app-layout>
