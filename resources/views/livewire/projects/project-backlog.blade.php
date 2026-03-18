<div class="d-flex flex-column gap-4">
    <div class="rounded border bg-body-tertiary p-3" data-tour="backlog-planning-controls">
        <div class="d-flex flex-column gap-3">
            <div class="d-flex flex-wrap align-items-end gap-2">
                <div style="min-width: 18rem;">
                    <label class="form-label small mb-1">Search</label>
                    <input type="text" class="form-control form-control-sm" wire:model.live.debounce.300ms="search"
                           placeholder="Search summary, description, #123, ABC-123">
                </div>

                <div>
                    <label class="form-label small mb-1">Assignee</label>
                    <select class="form-select form-select-sm" wire:model.live="assigneeId">
                        <option value="">All assignees</option>
                        @foreach ($assigneeOptions as $option)
                            <option value="{{ $option['id'] }}">{{ $option['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label small mb-1">Status</label>
                    <select class="form-select form-select-sm" wire:model.live="statusId">
                        <option value="">All statuses</option>
                        @foreach ($statusOptions as $option)
                            <option value="{{ $option['id'] }}">{{ $option['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label small mb-1">Sprint Focus</label>
                    <select class="form-select form-select-sm" wire:model.live="focusSprintId">
                        <option value="">All planning sprints</option>
                        @foreach ($sprintOptions as $option)
                            <option value="{{ $option['id'] }}">{{ $option['name'] }} ({{ ucfirst($option['state']) }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-check mt-4">
                    <input class="form-check-input" id="onlyUnsized" type="checkbox" wire:model.live="onlyUnsized">
                    <label class="form-check-label small" for="onlyUnsized">Needs estimate</label>
                </div>

                <div class="form-check mt-4">
                    <input class="form-check-input" id="onlyUnassigned" type="checkbox" wire:model.live="onlyUnassigned">
                    <label class="form-check-label small" for="onlyUnassigned">Unassigned</label>
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button type="button" class="btn btn-outline-secondary btn-sm"
                            wire:click="selectVisibleBacklog"
                            @disabled($backlog === [])>
                        Select visible
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm"
                            wire:click="clearSelection"
                            @disabled($selectedIssueIds === [])>
                        Clear selection
                    </button>
                </div>

                <div class="input-group input-group-sm" style="max-width: 22rem;">
                    <select class="form-select" wire:model="bulkSprintId">
                        <option value="">Move selected to sprint…</option>
                        @foreach ($sprintOptions as $option)
                            <option value="{{ $option['id'] }}">{{ $option['name'] }} ({{ ucfirst($option['state']) }})</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-primary"
                            wire:click="moveSelectedToSprint"
                            @disabled($selectedIssueIds === [] || $sprintOptions === [])>
                        Move selected
                    </button>
                </div>

                <button type="button" class="btn btn-outline-success btn-sm" wire:click="openCreateSprint">New sprint</button>

                <div class="small text-body-secondary ms-auto">
                    {{ count($selectedIssueIds) }} selected
                    @if ($projectVelocityTarget !== null)
                        · Default target {{ $projectVelocityTarget }} {{ $planningUnitShort }}/sprint
                    @endif
                </div>
            </div>

            @if (! $canRank)
                <div class="small text-body-secondary">
                    Ranking is disabled while search or issue filters are active so hidden issues do not get reordered accidentally.
                </div>
            @endif
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="rounded border bg-body p-3 h-100">
                <div class="small text-uppercase text-body-secondary">Backlog</div>
                <div class="fs-4 fw-semibold">{{ $summary['backlog_count'] }}</div>
                <div class="small text-body-secondary">
                    @if ($summary['visible_backlog_count'] !== $summary['backlog_count'])
                        Showing {{ $summary['visible_backlog_count'] }} after filters
                    @else
                        Ready for planning
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="rounded border bg-body p-3 h-100">
                <div class="small text-uppercase text-body-secondary">Needs Estimate</div>
                <div class="fs-4 fw-semibold">{{ $summary['unsized_count'] }}</div>
                <div class="small text-body-secondary">Backlog items missing {{ strtolower($planningUnitLabel) }}</div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="rounded border bg-body p-3 h-100">
                <div class="small text-uppercase text-body-secondary">Unassigned</div>
                <div class="fs-4 fw-semibold">{{ $summary['unassigned_count'] }}</div>
                <div class="small text-body-secondary">Backlog items with no owner</div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="rounded border bg-body p-3 h-100">
                <div class="small text-uppercase text-body-secondary">Planning Sprints</div>
                <div class="fs-4 fw-semibold">{{ $summary['planning_sprint_count'] }}</div>
                <div class="small text-body-secondary">Active and planned sprints in play</div>
            </div>
        </div>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-12 col-xl-5">
            <div class="rounded border bg-body-tertiary" data-tour="backlog-prioritized-list">
                <div class="px-3 py-2 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fw-semibold">Prioritized Backlog</div>
                        <div class="small text-body-secondary">
                            {{ $summary['visible_backlog_count'] }} visible
                            @if ($summary['visible_backlog_count'] !== $summary['backlog_count'])
                                of {{ $summary['backlog_count'] }}
                            @endif
                        </div>
                    </div>
                    <span class="badge bg-body-secondary text-body">{{ count($selectedIssueIds) }} selected</span>
                </div>

                <div class="p-3">
                    <div class="d-flex flex-column gap-2"
                         data-planning-sortable
                         data-sortable-enabled="{{ $canRank ? '1' : '0' }}"
                         data-lane="backlog">
                        @forelse ($backlog as $item)
                            <div class="card shadow-sm backlog-card" data-issue-id="{{ $item['id'] }}" wire:key="backlog-{{ $item['id'] }}" style="--tier-color: {{ $item['type_color'] }};">
                                <div class="card-body p-3 d-flex flex-column gap-2">
                                    <div class="d-flex align-items-start gap-2">
                                        <input class="form-check-input mt-1" type="checkbox" wire:model="selectedIssueIds" value="{{ $item['id'] }}">

                                        <button type="button" class="btn btn-light btn-sm px-2 py-1" data-planning-handle @disabled(! $canRank) title="Reorder">
                                            <i class="material-icons" style="font-size: 16px;">drag_indicator</i>
                                        </button>

                                        <div class="flex-grow-1 min-w-0">
                                            <div class="small text-body-secondary d-flex align-items-center gap-2 flex-wrap">
                                                <a href="{{ $item['issue_url'] }}" class="fw-medium text-decoration-none">{{ $item['key'] }}</a>
                                                <x-issues.tier-badge :color="$item['type_color']" :icon="$item['type_icon']" />
                                                <span class="badge" style="background: {{ $item['status_color'] }}20; color: {{ $item['status_color'] }};">
                                                    {{ $item['status_name'] }}
                                                </span>
                                            </div>
                                            <div class="mt-1">{{ $item['summary'] }}</div>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-wrap align-items-center gap-2 small text-body-secondary">
                                        <span class="badge bg-body-secondary text-body">{{ $item['metric_display'] }}</span>
                                        @if ($item['priority_name'])
                                            <span class="badge" style="background: {{ $item['priority_color'] }}20; color: {{ $item['priority_color'] }};">
                                                {{ $item['priority_name'] }}
                                            </span>
                                        @endif
                                        <span>{{ $item['assignee_name'] ?? 'Unassigned' }}</span>
                                    </div>

                                    <div class="d-flex flex-wrap gap-2">
                                        <select class="form-select form-select-sm"
                                                wire:change="moveIssueToSprint('{{ $item['id'] }}', $event.target.value)">
                                            <option value="">Move to sprint…</option>
                                            @foreach ($sprintOptions as $option)
                                                <option value="{{ $option['id'] }}">{{ $option['name'] }} ({{ ucfirst($option['state']) }})</option>
                                            @endforeach
                                        </select>
                                        <a href="{{ $item['issue_url'] }}" class="btn btn-outline-secondary btn-sm">Open</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded border border-dashed text-center py-5 text-body-secondary">
                                No backlog issues match the current filters.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-7" data-tour="backlog-sprint-plan">
            <div class="d-flex flex-column gap-3">
                @forelse ($sprints as $sprint)
                    <div class="rounded border bg-body-tertiary" wire:key="sprint-{{ $sprint['id'] }}">
                        <div class="px-3 py-3 border-bottom d-flex flex-wrap align-items-start justify-content-between gap-3">
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <div class="fw-semibold">{{ $sprint['name'] }}</div>
                                    <span class="badge bg-body-secondary text-body text-uppercase">{{ $sprint['state'] }}</span>
                                    @if ($sprint['capacity_display'])
                                        <span class="badge {{ $sprint['is_over_capacity'] ? 'text-bg-danger' : 'text-bg-light' }}">
                                            Capacity {{ $sprint['capacity_display'] }}
                                        </span>
                                    @endif
                                </div>

                                <div class="small text-body-secondary mt-1">
                                    {{ $sprint['issue_count'] }} committed
                                    @if ($sprint['visible_issue_count'] !== $sprint['issue_count'])
                                        · Showing {{ $sprint['visible_issue_count'] }}
                                    @endif
                                    · {{ $sprint['committed_display'] }} scheduled
                                    @if ($sprint['remaining_display'])
                                        ·
                                        <span class="{{ $sprint['is_over_capacity'] ? 'text-danger fw-semibold' : '' }}">
                                            {{ $sprint['is_over_capacity'] ? $sprint['remaining_display'].' over' : $sprint['remaining_display'].' remaining' }}
                                        </span>
                                    @endif
                                </div>

                                @if ($sprint['goal'])
                                    <div class="small mt-2">{{ $sprint['goal'] }}</div>
                                @endif

                                @if ($sprint['start_date'] || $sprint['end_date'])
                                    <div class="small text-body-secondary mt-1">
                                        {{ $sprint['start_date'] ?? 'TBD' }} → {{ $sprint['end_date'] ?? 'TBD' }}
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex flex-wrap align-items-end gap-2">
                                <div>
                                    <label class="form-label small mb-1">{{ $planningUnitLabel }} capacity</label>
                                    <input type="number"
                                           min="0"
                                           class="form-control form-control-sm"
                                           value="{{ $sprint['capacity_input'] }}"
                                           placeholder="{{ $projectVelocityTarget !== null ? $projectVelocityTarget : 'Unset' }}"
                                           wire:change="updateSprintCapacity('{{ $sprint['id'] }}', $event.target.value)">
                                    <div class="form-text">{{ $sprint['capacity_source'] }}</div>
                                </div>

                                @if ($sprint['state'] === 'planned')
                                    <button type="button" class="btn btn-outline-primary btn-sm" wire:click="startSprint('{{ $sprint['id'] }}')">
                                        Start sprint
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="p-3">
                            <div class="d-flex flex-column gap-2"
                                 data-planning-sortable
                                 data-sortable-enabled="{{ $canRank ? '1' : '0' }}"
                                 data-lane="sprint"
                                 data-sprint-id="{{ $sprint['id'] }}">
                                @forelse ($sprint['issues'] as $item)
                                    <div class="card shadow-sm backlog-card" data-issue-id="{{ $item['id'] }}" wire:key="sprint-issue-{{ $item['id'] }}" style="--tier-color: {{ $item['type_color'] }};">
                                        <div class="card-body p-3 d-flex flex-column gap-2">
                                            <div class="d-flex align-items-start gap-2">
                                                <button type="button" class="btn btn-light btn-sm px-2 py-1" data-planning-handle @disabled(! $canRank) title="Reorder">
                                                    <i class="material-icons" style="font-size: 16px;">drag_indicator</i>
                                                </button>

                                                <div class="flex-grow-1 min-w-0">
                                                    <div class="small text-body-secondary d-flex align-items-center gap-2 flex-wrap">
                                                        <a href="{{ $item['issue_url'] }}" class="fw-medium text-decoration-none">{{ $item['key'] }}</a>
                                                        <x-issues.tier-badge :color="$item['type_color']" :icon="$item['type_icon']" />
                                                        <span class="badge" style="background: {{ $item['status_color'] }}20; color: {{ $item['status_color'] }};">
                                                            {{ $item['status_name'] }}
                                                        </span>
                                                    </div>
                                                    <div class="mt-1">{{ $item['summary'] }}</div>
                                                </div>
                                            </div>

                                            <div class="d-flex flex-wrap align-items-center gap-2 small text-body-secondary">
                                                <span class="badge bg-body-secondary text-body">{{ $item['metric_display'] }}</span>
                                                @if ($item['priority_name'])
                                                    <span class="badge" style="background: {{ $item['priority_color'] }}20; color: {{ $item['priority_color'] }};">
                                                        {{ $item['priority_name'] }}
                                                    </span>
                                                @endif
                                                <span>{{ $item['assignee_name'] ?? 'Unassigned' }}</span>
                                            </div>

                                            <div class="d-flex flex-wrap gap-2">
                                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                                        wire:click="moveIssueToBacklog('{{ $item['id'] }}')">
                                                    Backlog
                                                </button>
                                                <a href="{{ $item['issue_url'] }}" class="btn btn-outline-secondary btn-sm">Open</a>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="rounded border border-dashed text-center py-4 text-body-secondary">
                                        No sprint issues match the current filters.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded border border-dashed text-center py-5 text-body-secondary bg-body-tertiary">
                        No active or planned sprints yet. Create one to start planning.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @if ($showCreateSprint)
        <div class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center z-3">
            <div class="position-absolute top-0 start-0 w-100 h-100 bg-black opacity-50" wire:click="$set('showCreateSprint', false)"></div>
            <div class="position-relative bg-body rounded border p-3" style="width: 100%; max-width: 640px;">
                <div class="fw-semibold mb-2">Create sprint</div>

                <div class="row g-2">
                    <div class="col-12">
                        <label class="form-label small">Name</label>
                        <input type="text" class="form-control" wire:model.defer="newSprint.name">
                        @error('newSprint.name') <div class="invalid-feedback d-block small">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label small">Goal (optional)</label>
                        <textarea rows="3" class="form-control" wire:model.defer="newSprint.goal"></textarea>
                        @error('newSprint.goal') <div class="invalid-feedback d-block small">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-6">
                        <label class="form-label small">Start date</label>
                        <input type="date" class="form-control" wire:model.defer="newSprint.start_date">
                        @error('newSprint.start_date') <div class="invalid-feedback d-block small">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-6">
                        <label class="form-label small">End date</label>
                        <input type="date" class="form-control" wire:model.defer="newSprint.end_date">
                        @error('newSprint.end_date') <div class="invalid-feedback d-block small">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label small">{{ $planningUnitLabel }} capacity</label>
                        <input type="number" min="0" class="form-control" wire:model.defer="newSprint.capacity" placeholder="{{ $projectVelocityTarget !== null ? $projectVelocityTarget : '' }}">
                        @error('newSprint.capacity') <div class="invalid-feedback d-block small">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 form-check">
                        <input type="checkbox" id="start_now" class="form-check-input" wire:model.defer="newSprint.start_now">
                        <label for="start_now" class="form-check-label small">Start sprint immediately</label>
                    </div>
                </div>

                <div class="mt-3 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="$set('showCreateSprint', false)">Cancel</button>
                    <button type="button" class="btn btn-primary btn-sm" wire:click="createSprint">Create sprint</button>
                </div>
            </div>
        </div>
    @endif

    <style>
        .backlog-card .card-body {
            border-left: 3px solid var(--tier-color, #607D8B);
        }

        .border-dashed {
            border-style: dashed !important;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script>
        const initBacklogPlanningSortables = () => {
            if (typeof Sortable === 'undefined') {
                return;
            }

            document.querySelectorAll('[data-planning-sortable]').forEach((lane) => {
                if (lane._sortable) {
                    try { lane._sortable.destroy(); } catch (e) {}
                }

                if (lane.dataset.sortableEnabled !== '1') {
                    return;
                }

                lane._sortable = new Sortable(lane, {
                    animation: 150,
                    handle: '[data-planning-handle]',
                    onUpdate: () => {
                        const orderedIssueIds = Array.from(lane.querySelectorAll('[data-issue-id]'))
                            .map((node) => node.dataset.issueId)
                            .filter(Boolean);

                        const laneType = lane.dataset.lane;
                        const sprintId = lane.dataset.sprintId || null;

                        window.Livewire.dispatch('backlog:reorder', {
                            lane: laneType,
                            sprintId: sprintId,
                            orderedIssueIds: orderedIssueIds,
                        });
                    },
                });
            });
        };

        document.addEventListener('livewire:init', () => {
            initBacklogPlanningSortables();

            if (window.Livewire?.hook) {
                Livewire.hook('message.processed', () => initBacklogPlanningSortables());
            } else {
                document.addEventListener('livewire:update', () => initBacklogPlanningSortables());
            }
        });
    </script>
</div>
