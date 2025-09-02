<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    {{-- Column 1: My Work --}}
    <section class="space-y-4">
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">My open issues</h3>
                <a href="{{ route('issues.create.global') }}"
                   class="text-xs underline underline-offset-4 text-indigo-600 dark:text-indigo-400">
                    New Issue
                </a>
            </div>

            @if($myIssues->isEmpty())
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Nothing assigned. Enjoy the calm 👌</p>
            @else
                <ul class="space-y-2">
                    @foreach($myIssues as $issue)
                        <li class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <a class="font-medium text-sm text-zinc-800 dark:text-zinc-100 truncate"
                                   href="{{ route('issues.show', ['project' => $issue->project, 'issue' => $issue]) }}">
                                    {{ $issue->project->key }}-{{ $issue->key }} — {{ $issue->summary }}
                                </a>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                    {{ $issue->status?->name ?? '—' }}
                                    @if($issue->due_at)
                                        • Due {{ $issue->due_at->diffForHumans() }}
                                    @endif
                                </div>
                            </div>
                            @if($issue->status)
                                <span class="shrink-0 rounded px-2 py-0.5 text-[11px]"
                                      style="background-color: {{ $issue->status->color }}20; color: {{ $issue->status->color }}">
                                    {{ $issue->status->name }}
                                </span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-4">
            <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-3">Due soon (next 14 days)</h3>
            @if($upcomingDue->isEmpty())
                <p class="text-sm text-zinc-500 dark:text-zinc-400">No upcoming deadlines.</p>
            @else
                <ul class="space-y-2">
                    @foreach($upcomingDue as $issue)
                        <li class="flex items-center justify-between gap-3">
                            <a class="text-sm text-zinc-800 dark:text-zinc-100 truncate"
                               href="{{ route('issues.show', ['project' => $issue->project, 'issue' => $issue]) }}">
                                {{ $issue->project->key }}-{{ $issue->key }} — {{ $issue->summary }}
                            </a>
                            <span class="text-xs text-zinc-500 dark:text-zinc-400 shrink-0">
                                {{ $issue->due_at->toFormattedDateString() }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>

    {{-- Column 2: Projects --}}
    <section class="space-y-4">
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Projects</h3>
                <a href="{{ route('projects.create') }}"
                   class="text-xs underline underline-offset-4 text-indigo-600 dark:text-indigo-400">
                    New Project
                </a>
            </div>

            @if($myProjects->isEmpty())
                <p class="text-sm text-zinc-500 dark:text-zinc-400">You’re not on any projects yet.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($myProjects as $project)
                        <a href="{{ route('projects.show', ['project' => $project]) }}"
                           class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-3 hover:bg-zinc-50 dark:hover:bg-zinc-900/50 transition">
                            <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">
                                {{ $project->name }}
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                {{ $project->key }} • {{ $project->open_issues_count }} open
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('projects.board', ['project' => $project]) }}"
                                   class="text-[11px] underline underline-offset-4 text-indigo-600 dark:text-indigo-400">
                                    Open Board
                                </a>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Column 3: Activity --}}
    <section class="space-y-4">
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-4">
            <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-3">Recent activity</h3>
            @if($recentActivity->isEmpty())
                <p class="text-sm text-zinc-500 dark:text-zinc-400">No recent activity.</p>
            @else
                <ul class="space-y-2">
                    @foreach($recentActivity as $event)
                        <li class="text-sm text-zinc-800 dark:text-zinc-100">
                            <span class="text-zinc-600 dark:text-zinc-400">
                                {{ $event->created_at->diffForHumans() }}
                            </span>
                            — {{ $event->description }}
                            @if($event->project)
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">({{ $event->project->key }})</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>
</div>
