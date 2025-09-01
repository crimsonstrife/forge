<?php
use App\Models\Project;
use App\Models\Issue;
use Illuminate\View\View;

use function Laravel\Folio\{name, middleware, render};

name('issues.show');
middleware(['auth','verified']);

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

    $children = $issue->children;
    $childrenTotal = $children->count();
    $childrenDone  = $children->filter(fn ($c) => (bool) $c->status?->is_done)->count();
    $childrenPointsTotal = (int) $children->sum('story_points');
    $childrenPointsDone  = (int) $children->filter(fn ($c) => (bool) $c->status?->is_done)->sum('story_points');
    $childrenProgressPct = $childrenTotal > 0 ? (int) round(($childrenDone / $childrenTotal) * 100) : 0;

    return $view->with(compact(
        'project',
        'issue',
        'children',
        'childrenTotal',
        'childrenDone',
        'childrenPointsTotal',
        'childrenPointsDone',
        'childrenProgressPct',
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
                <a href="{{ route('projects.show', ['project' => $project]) }}" class="text-sm text-primary-600 hover:underline">Back to project</a>
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
                <a href="{{ route('projects.timeline', ['project' => $project]) }}" class="inline-flex items-center rounded-lg px-3 py-2 border">Timeline</a>
                <a href="{{ route('projects.calendar', ['project' => $project]) }}" class="inline-flex items-center rounded-lg px-3 py-2 border">Calendar</a>
                <a href="{{ route('projects.board', ['project' => $project]) }}" class="inline-flex items-center rounded-lg px-3 py-2 border">Kanban</a>
                <a href="{{ route('projects.scrum', ['project' => $project]) }}" class="inline-flex items-center rounded-lg px-3 py-2 border">Sprint</a>
                @can('issues.create')
                    <a href="{{ route('issues.create', ['project' => $project]) }}" class="inline-flex items-center rounded-lg px-3 py-2 border">New issue</a>
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
                            <span class="text-xs font-mono px-2 py-1 rounded bg-gray-100 dark:bg-gray-700">{{ $issue->key }}</span>
                            <span class="inline-flex items-center gap-1 text-sm">
                                <span class="h-2 w-2 rounded-full" style="background: {{ $issue->status?->color ?? '#9ca3af' }}"></span>
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
                                    <span class="text-xs px-2 py-1 rounded bg-gray-100 dark:bg-gray-700">{{ $tag->name }}</span>
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
                                        <img class="h-5 w-5 rounded-full" src="{{ $issue->reporter->profile_photo_url }}">
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
                                        <img class="h-5 w-5 rounded-full" src="{{ $issue->assignee->profile_photo_url }}">
                                        {{ $issue->assignee->name }}
                                    @else
                                        Unassigned
                                    @endif
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt>Story points</dt><dd>{{ $issue->story_points ?? '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt>Estimate</dt><dd>{{ $issue->estimate_minutes ? $issue->estimate_minutes.'m' : '—' }}</dd>
                            </div>
                            <div class="pt-3">
                                <div class="text-xs mb-1">Progress</div>
                                <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded">
                                    <div class="h-2 rounded bg-primary-600" style="width: {{ (int) round($issue->progress() * 100) }}%"></div>
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
                        <livewire:issues.attachment-upload :issue="$issue" />
                    @endcan
                </div>
                <ul class="mt-4 divide-y divide-gray-200/60 dark:divide-gray-700/60">
                    @php($media = $issue->media()->where('collection_name','attachments')->latest()->get())
                    @forelse($media as $file)
                        <li class="py-3 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span>📎</span>
                                <div>
                                    <div class="text-sm font-medium">{{ $file->file_name }}</div>
                                    <div class="text-xs text-gray-500">{{ number_format($file->size / 1024, 1) }} KB · uploaded {{ $file->created_at->diffForHumans() }}</div>
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
            <livewire:issues.focus-timer :issue="$issue" class="mb-4" />
            <livewire:issues.time-entries-panel :issue="$issue" />

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
                                <div class="h-2 rounded bg-primary-600" style="width: {{ $childrenProgressPct }}%"></div>
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
                                        <a class="underline" href="{{ route('issues.show', ['project'=>$project, 'issue'=>$child]) }}">
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

            <!-- Comments -->
            <div class="rounded-xl border border-gray-200/60 dark:border-gray-700/60 bg-white dark:bg-gray-800 p-6">
                <h4 class="font-semibold">Comments ({{ $issue->comments_count }})</h4>
                <livewire:issues.comments :issue="$issue" />
            </div>
        </div>
    </div>
</x-app-layout>
