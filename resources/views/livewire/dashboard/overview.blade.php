<div class="row g-4">
    {{-- Column 1: My Work --}}
    <section class="col-lg-4 d-flex flex-column gap-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h3 class="h6 mb-0">{{ __('My open issues') }}</h3>
                    <a href="{{ route('issues.create.global') }}" class="small text-decoration-underline link-primary">
                        {{ __('New Issue') }}
                    </a>
                </div>

                @if($myIssues->isEmpty())
                    <p class="small text-body-secondary mb-0">{{ __('Nothing assigned. Enjoy the calm 👌') }}</p>
                @else
                    <div class="scroll-area scroll-area-lg pe-1">
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                            @foreach($myIssues as $issue)
                                <li class="d-flex align-items-start justify-content-between gap-2">
                                    <div class="flex-grow-1 min-w-0">
                                        <a class="fw-medium small text-reset text-decoration-none d-block text-break"
                                           href="{{ route('issues.show', ['project' => $issue->project, 'issue' => $issue]) }}">
                                            {{ $issue->project->key }}-{{ $issue->key }} — {{ $issue->summary }}
                                        </a>
                                        <div class="small text-body-secondary mt-1">
                                            {{ $issue->status?->name ?? '—' }}
                                            @if($issue->due_at)
                                                • {{ __('Due') }} {{ $issue->due_at->diffForHumans() }}
                                            @endif
                                        </div>
                                    </div>

                                    @if($issue->status)
                                        <span class="badge rounded-pill flex-shrink-0 text-nowrap"
                                              style="background-color: {{ $issue->status->color }}20; color: {{ $issue->status->color }}">
                                        {{ $issue->status->name }}
                                    </span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h3 class="h6 mb-2">{{ __('Due soon (next 14 days)') }}</h3>

                @if($upcomingDue->isEmpty())
                    <p class="small text-body-secondary mb-0">{{ __('No upcoming deadlines.') }}</p>
                @else
                    <div class="scroll-area scroll-area-lg pe-1">
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                            @foreach($upcomingDue as $issue)
                                <li class="d-flex align-items-center justify-content-between gap-2">
                                    <a class="small text-reset text-decoration-none d-block text-break"
                                       href="{{ route('issues.show', ['project' => $issue->project, 'issue' => $issue]) }}">
                                        {{ $issue->project->key }}-{{ $issue->key }} — {{ $issue->summary }}
                                    </a>
                                    <span class="small text-body-secondary flex-shrink-0">
                                        {{ $issue->due_at->toFormattedDateString() }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Column 2: Projects --}}
    <section class="col-lg-4 d-flex flex-column gap-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h3 class="h6 mb-0">{{ __('Projects') }}</h3>
                    <a href="{{ route('projects.create') }}" class="small text-decoration-underline link-primary">
                        {{ __('New Project') }}
                    </a>
                </div>

                @if($myProjects->isEmpty())
                    <p class="small text-body-secondary mb-0">{{ __('You’re not on any projects yet.') }}</p>
                @else
                    <div class="row row-cols-1 row-cols-sm-1 g-3">
                        @foreach($myProjects as $project)
                            <div class="col">
                                <a href="{{ route('projects.show', ['project' => $project]) }}" class="card h-100 text-reset text-decoration-none border">
                                    <div class="card-body p-3">
                                        <div class="small fw-semibold text-truncate">
                                            {{ $project->name }}
                                        </div>
                                        <div class="small text-body-secondary mt-1">
                                            {{ $project->key }} • {{ $project->open_issues_count }} {{ __('open') }}
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Column 3: Activity --}}
    <section class="col-lg-4 d-flex flex-column gap-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <h3 class="h6 mb-0">{{ __('Recent activity') }}</h3>
                </div>

                <div class="mt-3 d-flex flex-column gap-3 scroll-area scroll-area-lg pe-1">
                    @forelse($activityGroups as $groupLabel => $items)
                        <div>
                            <div class="text-uppercase small fw-semibold text-body-secondary">
                                {{ $groupLabel }}
                            </div>

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

                                                <div class="small text-body-secondary mt-1">
                                                    {{ $i['ago'] }}
                                                </div>

                                                {{-- Compact “headline” for Status change --}}
                                                @php $statusChange = collect($i['changes'])->firstWhere('key','issue_status_id'); @endphp
                                                @if($statusChange)
                                                    <div class="small d-flex align-items-center gap-2 mt-2">
                                                        <span class="text-body-secondary">{{ $statusChange['label'] }}:</span>
                                                        <span class="badge bg-body-secondary text-body">{{ $statusChange['from'] ?? '—' }}</span>
                                                        <span>→</span>
                                                        <span class="badge"
                                                              style="background-color: {{ $statusChange['to_color'] ?? 'transparent' }}20;">
                              {{ $statusChange['to'] ?? '—' }}
                            </span>
                                                    </div>
                                                @endif

                                                {{-- Toggle full diff --}}
                                                @if(!empty($i['changes']))
                                                    <div x-data="{ open: false }" class="mt-2">
                                                        <button type="button"
                                                                class="btn btn-link btn-sm p-0 text-decoration-underline"
                                                                @click="open = !open">
                                                            <span x-show="!open">{{ __('Show details') }}</span>
                                                            <span x-show="open">{{ __('Hide details') }}</span>
                                                        </button>

                                                        <div x-show="open" x-cloak class="mt-2 rounded p-3 bg-body-tertiary d-flex flex-column gap-2 small">
                                                            @foreach($i['changes'] as $c)
                                                                <div class="d-flex align-items-start gap-2">
                                                                    <div class="flex-shrink-0" style="width: 7rem;">
                                                                        <span class="text-body-secondary">{{ $c['label'] }}</span>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <div class="d-inline-flex align-items-center gap-2">
                                                                            <span class="badge bg-white text-body border">{{ $c['from'] ?? '—' }}</span>
                                                                            <span>→</span>
                                                                            <span class="badge border"
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
                        <p class="small text-body-secondary mt-2 mb-0">{{ __('No activity yet.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</div>
