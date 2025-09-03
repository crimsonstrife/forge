<div
    x-data="{
        view: @entangle('viewMode'),
        toList(){ this.view = 'list' },
        toBoard(){ this.view = 'kanban' },
    }"
    class="kanban-wrapper position-relative"
>
    {{-- Header / controls --}}
    <div class="d-flex align-items-center justify-content-between py-2 px-3">
        <h1 class="kanban-h1 m-0">
            <i class="material-icons align-text-bottom">check</i>
            {{ $project->name }} – Board
        </h1>

        <div class="btn-group">
            <button type="button" class="btn btn-light" :class="view==='kanban' && 'active'" @click="toBoard">
                <i class="material-icons">view_column</i>
            </button>
            <button type="button" class="btn btn-light" :class="view==='list' && 'active'" @click="toList">
                <i class="material-icons">view_list</i>
            </button>
        </div>
    </div>

    {{-- Columns --}}
    <div class="dd d-flex flex-wrap gap-3 px-3 pb-3"
         x-init="$nextTick(() => window.initKanbanSortables())"
    >
        @foreach ($columns as $col)
            <ol class="kanban-col"
                :class="view"
                style="--kanban-accent: {{ $col['color'] ?? '#78909C' }};"
            >
                <div class="kanban__title">
                    <h2 class="m-0">
                        <i class="material-icons">folder</i> {{ $col['name'] }}
                    </h2>
                </div>

                <ul class="dd-list kanban-list"
                    data-kanban-list
                    data-status-id="{{ $col['id'] }}"
                >
                    @foreach (($lists[$col['id']] ?? []) as $issue)
                        <li class="dd-item"
                            data-issue-id="{{ $issue['id'] }}"
                            wire:key="issue-{{ $issue['id'] }}"
                            style="--tier-color: {{ $issue['type_color'] ?? '#607D8B' }};"
                        >
                            <h3 class="title dd-handle d-flex align-items-start justify-content-between"
                                :class="view==='kanban' ? 'card--' + '{{ $issue['tier'] ?? 'other' }}' : ''">
                                <div class="me-2">
                                    <i class="material-icons me-1 align-text-bottom">filter_none</i>
                                    {{ $issue['summary'] }}
                                </div>

                                {{-- Tier chip --}}
                                <x-issues.tier-badge
                                    :color="$issue['type_color'] ?? '#607D8B'"
                                    :icon="$issue['type_icon'] ?? 'filter_none'"
                                />
                            </h3>

                            @if(!empty($issue['description'] ?? null))
                                <div class="text">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($issue['description']), 160) }}
                                </div>
                            @endif

                            {{-- Roll-up progress (only when parent) --}}
                            @if(($issue['progress'] ?? null) !== null)
                                <div class="mt-2">
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar"
                                             role="progressbar"
                                             style="width: {{ $issue['progress'] }}%; background-color: {{ $issue['type_color'] ?? '#607D8B' }};"
                                             aria-valuenow="{{ $issue['progress'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1 small text-muted">
                                        <span>{{ $issue['children_done'] }} / {{ $issue['children_total'] }}</span>
                                        <span>{{ $issue['progress'] }}%</span>
                                    </div>
                                </div>
                            @endif

                            <div class="actions d-flex gap-2 mt-2">
                                <button type="button" class="btn btn-sm btn-light"
                                        wire:click="openIssue({{ $issue['id'] }})"
                                        title="Open">
                                    <i class="material-icons">edit</i>
                                </button>
                                <button type="button" class="btn btn-sm btn-light"
                                        wire:click="quickColor({{ $issue['id'] }})"
                                        title="Label">
                                    <i class="material-icons">label</i>
                                </button>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div class="actions mt-2">
                    <button class="addbutt btn" type="button"
                            wire:click="createIssue({{ $col['id'] }})">
                        <i class="material-icons">control_point</i> Add new
                    </button>
                </div>
            </ol>
        @endforeach
    </div>

    {{-- Styles (once) --}}
    <style>
        .kanban-list .dd-item .title{border-left:3px solid var(--tier-color,#607D8B);padding-left:.5rem;border-radius:.25rem}
        .kanban-list .dd-item .title.card--epic{padding-top:.35rem;padding-bottom:.35rem}
        .kanban-list .dd-item .title.card--story{padding-top:.25rem;padding-bottom:.25rem}
        .kanban-list .dd-item .title.card--task{padding-top:.15rem;padding-bottom:.15rem}
        .kanban-list .dd-item .title.card--subtask{padding-top:.1rem;padding-bottom:.1rem;opacity:.95}

        .kanban-col.list .dd-item{border-left:4px solid var(--tier-color,#607D8B);padding-left:.5rem}
    </style>
