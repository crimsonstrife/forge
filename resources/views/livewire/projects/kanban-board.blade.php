{{-- name: projects.kanban-board --}}
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
                    @foreach ($lists[$col['id']] ?? [] as $issue)
                        <li class="dd-item"
                            data-issue-id="{{ $issue['id'] }}"
                            wire:key="issue-{{ $issue['id'] }}"
                        >
                            <h3 class="title dd-handle">
                                <i class="material-icons">filter_none</i>
                                {{ $issue['summary'] }}
                            </h3>

                            @if(!empty($issue['description'] ?? null))
                                <div class="text">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($issue['description']), 160) }}
                                </div>
                            @endif

                            <div class="actions d-flex gap-2">
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
</div>
