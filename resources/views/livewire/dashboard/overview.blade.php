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
        <div class="rounded-xl border border-gray-200/60 dark:border-gray-700/60 bg-white dark:bg-gray-800 p-4">
            <div class="flex items-center justify-between">
                <h3 class="font-medium text-gray-900 dark:text-gray-100">Recent activity</h3>
            </div>

            <div class="mt-4 space-y-6">
                @forelse($activityGroups as $groupLabel => $items)
                    <div>
                        <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            {{ $groupLabel }}
                        </h4>

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

                                            {{-- Compact “headline” for Status change --}}
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

                                            {{-- Toggle full diff --}}
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
    </section>
</div>
