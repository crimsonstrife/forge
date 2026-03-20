@php use Illuminate\Support\Str; @endphp

<div class="roadmap-page d-grid gap-4">
    <style>
        .roadmap-stat-card {
            border: 0;
            box-shadow: 0 0.35rem 1rem rgba(15, 23, 42, 0.08);
        }

        .roadmap-stat-label {
            color: var(--bs-secondary-color);
            font-size: 0.75rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .roadmap-stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            line-height: 1;
        }

        .roadmap-toolbar {
            min-width: min(100%, 24rem);
        }

        .roadmap-legend-dot {
            display: inline-block;
            width: 0.7rem;
            height: 0.7rem;
            border-radius: 999px;
            margin-right: 0.35rem;
        }

        .roadmap-group-card {
            border: 0;
            box-shadow: 0 0.45rem 1.25rem rgba(15, 23, 42, 0.08);
            position: relative;
        }

        .roadmap-group-card::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 0.35rem;
            border-radius: 1rem 0 0 1rem;
            background: var(--roadmap-tone, #6c757d);
        }

        .roadmap-metric {
            background: rgba(15, 23, 42, 0.04);
            border-radius: 0.85rem;
            padding: 0.75rem 0.9rem;
        }

        .roadmap-metric-label {
            color: var(--bs-secondary-color);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .roadmap-metric-value {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .roadmap-edge {
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 1rem;
            padding: 0.9rem 1rem;
        }

        .roadmap-empty {
            border: 1px dashed rgba(15, 23, 42, 0.18);
            border-radius: 1rem;
            color: var(--bs-secondary-color);
            padding: 2rem 1.5rem;
            text-align: center;
        }
    </style>

    <div class="row g-3">
        <div class="col-sm-6 col-xl">
            <div class="card roadmap-stat-card">
                <div class="card-body">
                    <div class="roadmap-stat-label">Releases</div>
                    <div class="roadmap-stat-value">{{ $summary['release_count'] }}</div>
                    <div class="small text-body-secondary mt-2">
                        {{ $summary['upcoming_release']['label'] ?? 'No release scheduled' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl">
            <div class="card roadmap-stat-card">
                <div class="card-body">
                    <div class="roadmap-stat-label">Milestones</div>
                    <div class="roadmap-stat-value">{{ $summary['milestone_count'] }}</div>
                    <div class="small text-body-secondary mt-2">
                        {{ $summary['upcoming_release']['window'] ?? 'Add dates to expose roadmap windows' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl">
            <div class="card roadmap-stat-card">
                <div class="card-body">
                    <div class="roadmap-stat-label">At-Risk Releases</div>
                    <div class="roadmap-stat-value">{{ $summary['at_risk_release_count'] }}</div>
                    <div class="small text-body-secondary mt-2">Readiness penalties from blockers and slippage</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl">
            <div class="card roadmap-stat-card">
                <div class="card-body">
                    <div class="roadmap-stat-label">Blocked Issues</div>
                    <div class="roadmap-stat-value">{{ $summary['blocked_issue_count'] }}</div>
                    <div class="small text-body-secondary mt-2">Open work currently blocked by active dependencies</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl">
            <div class="card roadmap-stat-card">
                <div class="card-body">
                    <div class="roadmap-stat-label">Standalone Work</div>
                    <div class="roadmap-stat-value">{{ $summary['standalone_issue_count'] }}</div>
                    <div class="small text-body-secondary mt-2">
                        {{ $summary['unscheduled_issue_count'] }} issue{{ $summary['unscheduled_issue_count'] === 1 ? '' : 's' }} without a milestone
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-end gap-3">
            <div>
                <div class="small text-uppercase text-body-secondary fw-semibold mb-2">Roadmap View</div>
                <div class="btn-group" role="group" aria-label="Roadmap grouping">
                    <button
                        type="button"
                        wire:click="$set('groupBy', 'milestone')"
                        class="btn btn-sm {{ $groupBy === 'milestone' ? 'btn-primary' : 'btn-outline-primary' }}"
                    >
                        Release / milestone
                    </button>
                    <button
                        type="button"
                        wire:click="$set('groupBy', 'parent')"
                        class="btn btn-sm {{ $groupBy === 'parent' ? 'btn-primary' : 'btn-outline-primary' }}"
                    >
                        Epic parent
                    </button>
                </div>
                <div class="small text-body-secondary mt-2">
                    {{ $groupBy === 'milestone'
                        ? 'Readiness rolls up into releases and milestones, with dependency pressure across release windows.'
                        : 'Work rolls up under parent scopes so blocked-by risk is visible at the epic layer.' }}
                </div>
            </div>

            <div class="roadmap-toolbar">
                <label class="form-label small text-body-secondary mb-1">Milestone burnup / burndown</label>
                <select wire:model.live="selectedMilestoneId" class="form-select form-select-sm" @disabled($milestoneOptions === [])>
                    @forelse ($milestoneOptions as $option)
                        <option value="{{ $option['id'] }}">{{ $option['label'] }}</option>
                    @empty
                        <option value="">No milestones available</option>
                    @endforelse
                </select>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                        <div>
                            <div class="h5 mb-1">Roadmap timeline</div>
                            <div class="small text-body-secondary">
                                {{ $groupBy === 'milestone'
                                    ? 'Timeline windows colored by risk so release slip and dependency drag show up together.'
                                    : 'Parent scopes inherit schedule windows from their child work and milestone targets.' }}
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-3 small text-body-secondary">
                            <span><span class="roadmap-legend-dot" style="background:#198754"></span>Ready</span>
                            <span><span class="roadmap-legend-dot" style="background:#0d6efd"></span>On track</span>
                            <span><span class="roadmap-legend-dot" style="background:#f59f00"></span>At risk</span>
                            <span><span class="roadmap-legend-dot" style="background:#dc3545"></span>High risk</span>
                        </div>
                    </div>

                    @if ($timelineChart['series'] === [])
                        <div class="roadmap-empty">
                            Add milestone dates or scheduled issue windows to populate the roadmap timeline.
                        </div>
                    @else
                        <script type="application/json" id="project-roadmap-timeline-payload">@json($timelineChart, JSON_THROW_ON_ERROR)</script>
                        <div id="project-roadmap-timeline-chart" wire:ignore style="min-height: {{ $timelineChart['height'] }}px; width: 100%;"></div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="h5 mb-1">Dependency pressure</div>
                    <div class="small text-body-secondary mb-3">
                        Active blockers only. This is the current critical path surface, not every historical link.
                    </div>

                    @if ($dependencyEdges === [])
                        <div class="roadmap-empty">
                            No cross-group blockers are active right now.
                        </div>
                    @else
                        <div class="d-grid gap-2">
                            @foreach ($dependencyEdges as $edge)
                                <div class="roadmap-edge">
                                    <div class="small text-body-secondary mb-1">Blocking relationship</div>
                                    <div class="fw-semibold">
                                        @if ($edge['to_url'])
                                            <a href="{{ $edge['to_url'] }}" class="text-decoration-none">{{ $edge['to'] }}</a>
                                        @else
                                            {{ $edge['to'] }}
                                        @endif
                                        <span class="text-body-secondary">blocked by</span>
                                        @if ($edge['from_url'])
                                            <a href="{{ $edge['from_url'] }}" class="text-decoration-none">{{ $edge['from'] }}</a>
                                        @else
                                            {{ $edge['from'] }}
                                        @endif
                                    </div>
                                    <div class="small text-body-secondary mt-1">
                                        {{ $edge['count'] }} blocked issue{{ $edge['count'] === 1 ? '' : 's' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                        <div>
                            <div class="h5 mb-1">Burnup / burndown</div>
                            <div class="small text-body-secondary">
                                @if ($burnChart['empty'])
                                    Pick a milestone or release to inspect delivery trend.
                                @else
                                    {{ $burnChart['title'] }}
                                    @if ($burnChart['subtitle'])
                                        · {{ $burnChart['subtitle'] }}
                                    @endif
                                    @if ($burnChart['window'])
                                        · {{ $burnChart['window'] }}
                                    @endif
                                @endif
                            </div>
                        </div>

                        @unless ($burnChart['empty'])
                            <div class="d-flex flex-wrap gap-3 text-end">
                                <div>
                                    <div class="small text-body-secondary">Scope</div>
                                    <div class="fw-semibold">{{ $burnChart['stats']['total'] }}</div>
                                </div>
                                <div>
                                    <div class="small text-body-secondary">Done</div>
                                    <div class="fw-semibold">{{ $burnChart['stats']['done'] }}</div>
                                </div>
                                <div>
                                    <div class="small text-body-secondary">Open</div>
                                    <div class="fw-semibold">{{ $burnChart['stats']['open'] }}</div>
                                </div>
                            </div>
                        @endunless
                    </div>

                    @if ($burnChart['empty'])
                        <div class="roadmap-empty">
                            Create a milestone or release to start tracking burnup and burndown.
                        </div>
                    @else
                        <script type="application/json" id="project-roadmap-burn-payload">@json($burnChart, JSON_THROW_ON_ERROR)</script>
                        <div id="project-roadmap-burn-chart" wire:ignore style="min-height: 320px; width: 100%;"></div>
                        <div class="small text-body-secondary mt-3">{{ $burnChart['note'] }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="h5 mb-1">{{ $groupBy === 'milestone' ? 'Release readiness' : 'Scope readiness' }}</div>
                    <div class="small text-body-secondary mb-3">
                        Lowest readiness first so blockers and schedule gaps surface before they land in the timeline.
                    </div>

                    @php
                        $priorityGroups = collect($groups)
                            ->filter(fn (array $group) => $group['total_issues'] > 0)
                            ->sortBy(fn (array $group) => sprintf('%03d-%03d-%s', $group['readiness_percent'], 999 - $group['blocked_issue_count'], $group['name']))
                            ->take(6);
                    @endphp

                    @if ($priorityGroups->isEmpty())
                        <div class="roadmap-empty">
                            No roadmap groups have scoped issues yet.
                        </div>
                    @else
                        <div class="d-grid gap-2">
                            @foreach ($priorityGroups as $group)
                                <div class="roadmap-edge">
                                    <div class="d-flex justify-content-between gap-3">
                                        <div>
                                            <div class="fw-semibold">
                                                @if ($group['url'])
                                                    <a href="{{ $group['url'] }}" class="text-decoration-none">{{ $group['name'] }}</a>
                                                @else
                                                    {{ $group['name'] }}
                                                @endif
                                            </div>
                                            <div class="small text-body-secondary">
                                                {{ $group['date_window_label'] ?? 'No schedule window yet' }}
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge {{ $group['risk_badge_class'] }}">{{ $group['risk_label'] }}</span>
                                            <div class="small text-body-secondary mt-1">Readiness {{ $group['readiness_percent'] }}%</div>
                                        </div>
                                    </div>
                                    <div class="small text-body-secondary mt-2">
                                        {{ $group['done_issues'] }}/{{ $group['total_issues'] }} done
                                        · {{ $group['blocked_issue_count'] }} blocked
                                        · {{ $group['overdue_issue_count'] }} overdue
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div>
        <div class="d-flex flex-wrap align-items-end justify-content-between gap-2 mb-3">
            <div>
                <div class="h5 mb-1">Roadmap groups</div>
                <div class="small text-body-secondary">
                    {{ $groupBy === 'milestone'
                        ? 'Each card shows release readiness, blockers, and missing schedule data for a milestone bucket.'
                        : 'Each card shows how a parent scope is progressing across the milestones it touches.' }}
                </div>
            </div>
        </div>

        @if ($groups === [])
            <div class="roadmap-empty">
                No roadmap groups are available yet.
            </div>
        @else
            <div class="row g-3">
                @foreach ($groups as $group)
                    <div class="col-12 col-xl-6">
                        <div class="card roadmap-group-card" style="--roadmap-tone: {{ $group['chart_color'] }};">
                            <div class="card-body ps-4">
                                <div class="d-flex justify-content-between gap-3">
                                    <div>
                                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                            @if ($group['url'])
                                                <a href="{{ $group['url'] }}" class="text-decoration-none fw-semibold">{{ $group['name'] }}</a>
                                            @else
                                                <span class="fw-semibold">{{ $group['name'] }}</span>
                                            @endif

                                            <span class="badge {{ $group['type_badge_class'] }}">{{ Str::headline($group['type']) }}</span>

                                            @if ($group['state_label'])
                                                <span class="badge text-bg-light border">{{ $group['state_label'] }}</span>
                                            @endif
                                        </div>

                                        <div class="small text-body-secondary">
                                            {{ $group['subtitle'] ?? $group['date_window_label'] ?? 'No schedule window yet' }}
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <span class="badge {{ $group['risk_badge_class'] }}">{{ $group['risk_label'] }}</span>
                                        <div class="small text-body-secondary mt-2">Readiness {{ $group['readiness_percent'] }}%</div>
                                    </div>
                                </div>

                                <div class="progress mt-3" role="progressbar" aria-label="Roadmap progress" aria-valuenow="{{ $group['progress_percent'] }}" aria-valuemin="0" aria-valuemax="100" style="height: 0.8rem;">
                                    <div class="progress-bar" style="width: {{ $group['progress_percent'] }}%; background-color: {{ $group['chart_color'] }};">
                                        {{ $group['progress_percent'] }}%
                                    </div>
                                </div>

                                <div class="row g-2 mt-2">
                                    <div class="col-6">
                                        <div class="roadmap-metric">
                                            <div class="roadmap-metric-label">Done</div>
                                            <div class="roadmap-metric-value">{{ $group['done_issues'] }}/{{ $group['total_issues'] }}</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="roadmap-metric">
                                            <div class="roadmap-metric-label">Blocked</div>
                                            <div class="roadmap-metric-value">{{ $group['blocked_issue_count'] }}</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="roadmap-metric">
                                            <div class="roadmap-metric-label">Overdue</div>
                                            <div class="roadmap-metric-value">{{ $group['overdue_issue_count'] }}</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="roadmap-metric">
                                            <div class="roadmap-metric-label">Points</div>
                                            <div class="roadmap-metric-value">{{ $group['story_points_done'] }}/{{ $group['story_points_total'] }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="small text-body-secondary mt-3">
                                    {{ $group['date_window_label'] ?? 'No schedule window yet' }}
                                    @if ($group['kind'] === 'milestone')
                                        · touches {{ $group['scope_count'] }} parent scope{{ $group['scope_count'] === 1 ? '' : 's' }}
                                    @else
                                        · touches {{ $group['milestone_count'] }} milestone{{ $group['milestone_count'] === 1 ? '' : 's' }}
                                    @endif
                                    · {{ $group['unassigned_issue_count'] }} unassigned
                                    · {{ $group['unscheduled_issue_count'] }} missing dates
                                </div>

                                @if ($group['blocked_by'] !== [])
                                    <div class="mt-3">
                                        <div class="small fw-semibold mb-2">Blocked by</div>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach ($group['blocked_by'] as $blockedBy)
                                                <span class="badge text-bg-light border">
                                                    @if ($blockedBy['url'])
                                                        <a href="{{ $blockedBy['url'] }}" class="text-decoration-none text-reset">{{ $blockedBy['label'] }}</a>
                                                    @else
                                                        {{ $blockedBy['label'] }}
                                                    @endif
                                                    <span class="ms-1 text-body-secondary">{{ $blockedBy['count'] }}</span>
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @elseif ($group['self_blocked_issue_count'] > 0)
                                    <div class="mt-3 small text-body-secondary">
                                        {{ $group['self_blocked_issue_count'] }} issue{{ $group['self_blocked_issue_count'] === 1 ? '' : 's' }} are blocked from inside this same group.
                                    </div>
                                @endif

                                @if ($group['blocked_issue_samples'] !== [])
                                    <div class="mt-3 small text-body-secondary">
                                        <span class="fw-semibold text-body">Sample blocked work:</span>
                                        @foreach ($group['blocked_issue_samples'] as $sample)
                                            <span @class(['ms-2' => ! $loop->first])>
                                                <a href="{{ $sample['url'] }}" class="text-decoration-none">{{ $sample['key'] }}</a>
                                                <span>{{ $sample['summary'] }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('livewire:navigated', renderProjectRoadmapCharts);
        document.addEventListener('DOMContentLoaded', renderProjectRoadmapCharts);
        window.addEventListener('load', renderProjectRoadmapCharts);

        let projectRoadmapTimelineChart;
        let projectRoadmapBurnChart;

        function readRoadmapPayload(id) {
            const node = document.getElementById(id);
            if (!node) return null;

            try {
                return JSON.parse(node.textContent);
            } catch (error) {
                return null;
            }
        }

        function renderProjectRoadmapCharts() {
            if (typeof ApexCharts === 'undefined') {
                return;
            }

            renderRoadmapTimelineChart();
            renderRoadmapBurnChart();
        }

        function renderRoadmapTimelineChart() {
            const el = document.getElementById('project-roadmap-timeline-chart');
            const payload = readRoadmapPayload('project-roadmap-timeline-payload');

            if (!el || !payload || !payload.series || payload.series.length === 0) {
                if (projectRoadmapTimelineChart) {
                    projectRoadmapTimelineChart.destroy();
                    projectRoadmapTimelineChart = null;
                }

                return;
            }

            const options = {
                chart: {
                    type: 'rangeBar',
                    height: payload.height || 420,
                    toolbar: { show: false },
                    animations: { easing: 'easeout', speed: 260 },
                    events: {
                        dataPointSelection: function (event, ctx, config) {
                            const d = config.w.config.series[config.seriesIndex].data[config.dataPointIndex];
                            if (d && d.url) {
                                window.location = d.url;
                            }
                        }
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        borderRadius: 8,
                        rangeBarGroupRows: false,
                    }
                },
                dataLabels: { enabled: false },
                grid: { borderColor: 'rgba(15, 23, 42, 0.08)' },
                legend: { show: false },
                series: payload.series,
                tooltip: {
                    custom: ({ seriesIndex, dataPointIndex, w }) => {
                        const d = w.config.series[seriesIndex].data[dataPointIndex];

                        return `<div class="p-2">
                            <div class="fw-semibold">${d.x}</div>
                            <div class="small text-muted">${d.window || 'No schedule window yet'}</div>
                            <div class="mt-2 small">
                                Readiness ${d.readiness}% · Progress ${d.progress}%<br>
                                Open ${d.open} · Done ${d.done} · Blocked ${d.blocked}<br>
                                Risk ${d.risk}
                            </div>
                        </div>`;
                    }
                },
                xaxis: {
                    type: 'datetime'
                },
                yaxis: {
                    labels: {
                        maxWidth: 260
                    }
                }
            };

            if (projectRoadmapTimelineChart) {
                projectRoadmapTimelineChart.destroy();
            }

            projectRoadmapTimelineChart = new ApexCharts(el, options);
            projectRoadmapTimelineChart.render();
        }

        function renderRoadmapBurnChart() {
            const el = document.getElementById('project-roadmap-burn-chart');
            const payload = readRoadmapPayload('project-roadmap-burn-payload');

            if (!el || !payload || !payload.series || payload.series.length === 0) {
                if (projectRoadmapBurnChart) {
                    projectRoadmapBurnChart.destroy();
                    projectRoadmapBurnChart = null;
                }

                return;
            }

            const options = {
                chart: {
                    type: 'line',
                    height: 320,
                    toolbar: { show: false },
                    animations: { easing: 'easeout', speed: 260 }
                },
                colors: ['#0d6efd', '#198754', '#dc3545'],
                dataLabels: { enabled: false },
                grid: { borderColor: 'rgba(15, 23, 42, 0.08)' },
                series: payload.series,
                stroke: {
                    width: [3, 3, 3],
                    curve: 'smooth'
                },
                tooltip: {
                    shared: true,
                    intersect: false
                },
                xaxis: {
                    categories: payload.labels
                },
                yaxis: {
                    min: 0,
                    forceNiceScale: true
                }
            };

            if (projectRoadmapBurnChart) {
                projectRoadmapBurnChart.destroy();
            }

            projectRoadmapBurnChart = new ApexCharts(el, options);
            projectRoadmapBurnChart.render();
        }

        Livewire.hook('morph.updated', renderProjectRoadmapCharts);
    </script>
@endpush
