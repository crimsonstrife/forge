<x-app-layout>
    <x-slot name="header">
        @php
            $typeValue = $milestone->type?->value ?? (string) $milestone->type;
            $stateValue = $milestone->state?->value ?? (string) $milestone->state;

            $typeLabel = \Illuminate\Support\Str::headline($typeValue);
            $stateLabel = \Illuminate\Support\Str::headline($stateValue);

            $pct = $totalIssues > 0 ? (int) round(($doneIssues / $totalIssues) * 100) : 0;
            $estimateTotalHours = (int) floor($estimateTotalMinutes / 60);
            $estimateDoneHours = (int) floor($estimateDoneMinutes / 60);
        @endphp

        <div class="d-flex flex-wrap gap-2 align-items-center">
            <div class="me-auto">
                <div class="h5 mb-0 d-flex align-items-center gap-2 flex-wrap">
                    <span>{{ $milestone->name }}</span>

                    <span class="badge text-bg-secondary">{{ $typeLabel }}</span>
                    <span class="badge text-bg-light border">{{ $stateLabel }}</span>

                    @if($typeValue === 'release' && $milestone->version)
                        <span class="text-muted">{{ $milestone->version }}</span>
                    @endif
                </div>

                <div class="text-muted small">
                    <a class="link-secondary text-decoration-underline" href="{{ route('projects.show', ['project' => $project]) }}">
                        {{ $project->name }}
                    </a>
                </div>
            </div>

            <a href="{{ route('projects.milestones.index', $project) }}" class="btn btn-outline-secondary">
                Back
            </a>

            @can('update', $project)
                <a href="{{ route('projects.milestones.edit', [$project, $milestone]) }}" class="btn btn-primary">
                    Edit
                </a>

                <form method="post"
                      action="{{ route('projects.milestones.destroy', [$project, $milestone]) }}"
                      onsubmit="return confirm('Delete this milestone?');">
                    @csrf
                    @method('delete')
                    <button class="btn btn-outline-danger" type="submit">Delete</button>
                </form>
            @endcan
        </div>
    </x-slot>

    <div class="container py-4 d-grid gap-3">

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <div class="row g-3">
            <div class="col-lg-8 d-grid gap-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
                            <div class="fw-semibold">Progress</div>
                            <div class="text-muted small">
                                {{ $doneIssues }} / {{ $totalIssues }} issues done
                                @if($pointsTotal > 0)
                                    · {{ $pointsDone }}/{{ $pointsTotal }} pts
                                @endif
                                @if($estimateTotalMinutes > 0)
                                    · {{ $estimateDoneHours }}/{{ $estimateTotalHours }}h est
                                @endif
                            </div>
                        </div>

                        <div class="progress" role="progressbar" aria-label="Milestone progress" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar" style="width: {{ $pct }}%;">{{ $pct }}%</div>
                        </div>

                        @if($overdueIssues > 0)
                            <div class="mt-2 small text-danger">
                                {{ $overdueIssues }} overdue issue{{ $overdueIssues === 1 ? '' : 's' }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header fw-semibold">Description</div>
                    <div class="card-body">
                        @if($milestone->description)
                            <div class="issue-content">
                                {!! \Illuminate\Support\Str::markdown($milestone->description, ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}
                            </div>
                        @else
                            <div class="text-muted small">No description yet.</div>
                        @endif
                    </div>
                </div>

                <form method="get" class="card">
                    <div class="card-body">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-7">
                                <label class="form-label">Search issues</label>
                                <input name="q" class="form-control" value="{{ $filters['q'] }}" placeholder="Key or summary...">
                            </div>

                            <div class="col-md-5">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="all" @selected($filters['status'] === 'all')>All</option>
                                    <option value="open" @selected($filters['status'] === 'open')>Open</option>
                                    <option value="done" @selected($filters['status'] === 'done')>Done</option>
                                    <option value="overdue" @selected($filters['status'] === 'overdue')>Overdue</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-3 d-flex gap-2">
                            <button class="btn btn-outline-primary" type="submit">Apply</button>
                            <a class="btn btn-outline-secondary"
                               href="{{ route('projects.milestones.show', [$project, $milestone]) }}">
                                Reset
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive border-top">
                        <table class="table table-hover mb-0 align-middle">
                            <thead>
                            <tr>
                                <th style="width: 1%;">Key</th>
                                <th>Summary</th>
                                <th>Status</th>
                                <th>Assignee</th>
                                <th>Due</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($issues as $issue)
                                <tr>
                                    <td class="text-nowrap">
                                        <a href="{{ route('issues.show', ['project' => $project, 'issue' => $issue]) }}" class="link-primary text-decoration-underline">
                                            {{ $issue->key }}
                                        </a>
                                    </td>
                                    <td class="fw-semibold">{{ $issue->summary }}</td>
                                    <td>
                                        @php $color = $issue->status->color ?? null; @endphp
                                        <span class="badge"
                                              @if($color) style="background-color: {{ $color }};" @else class="text-bg-secondary" @endif>
                                            {{ $issue->status->name }}
                                        </span>
                                    </td>
                                    <td class="text-nowrap">
                                        @if($issue->assignee)
                                            {{ $issue->assignee->name }}
                                        @else
                                            <span class="text-muted">Unassigned</span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">
                                        {{ $issue->due_at?->format('M j, Y') ?? '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No issues found.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="card-body border-top">
                        {{ $issues->links() }}
                    </div>
                </form>
            </div>

            <div class="col-lg-4 d-grid gap-3">
                <div class="card">
                    <div class="card-header fw-semibold">Dates</div>
                    <div class="card-body small d-grid gap-2">
                        <div class="d-flex justify-content-between">
                            <div class="text-muted">Start</div>
                            <div>{{ $milestone->starts_at?->format('M j, Y') ?? '—' }}</div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div class="text-muted">Due</div>
                            <div>{{ $milestone->due_at?->format('M j, Y') ?? '—' }}</div>
                        </div>

                        @if($typeValue === 'release')
                            <div class="d-flex justify-content-between">
                                <div class="text-muted">Released</div>
                                <div>{{ $milestone->released_at?->format('M j, Y') ?? '—' }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header fw-semibold">Sprints</div>
                    <div class="card-body">
                        @if($sprints->isEmpty())
                            <div class="text-muted small">No sprints linked to this milestone.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm mb-0 align-middle">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th class="text-end">Issues</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($sprints as $s)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $s->name }}</div>
                                                <div class="text-muted small">
                                                    {{ $s->start_date?->format('M j') ?? '—' }} → {{ $s->end_date?->format('M j') ?? '—' }}
                                                </div>
                                            </td>
                                            <td class="text-end">{{ $s->issues_count }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                @if(is_array($milestone->meta) && count($milestone->meta) > 0)
                    <div class="card">
                        <div class="card-header fw-semibold">Metadata</div>
                        <div class="card-body">
                            <pre class="small mb-0">{{ json_encode($milestone->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>
</x-app-layout>
