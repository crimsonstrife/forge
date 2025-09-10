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
    <div class="dd d-flex flex-nowrap gap-3 px-3 pb-3 overflow-auto" x-init="$nextTick(() => window.initKanbanSortables())">
        @foreach ($columns as $col)
            <ol class="kanban-col" :class="view" style="--kanban-accent: {{ $col['color'] ?? '#78909C' }};">
                <div class="kanban__title">
                    <h2 class="m-0">
                        <i class="material-icons">folder</i> {{ $col['name'] }}
                    </h2>
                </div>
                <wa-scroller orientation="vertical" style="max-height: 600px;" without-scrollbar>
                    <ul class="dd-list kanban-list" data-kanban-list data-status-id="{{ $col['id'] }}">
                        @foreach (($lists[$col['id']] ?? []) as $issue)
                            {{-- inside each <li class="dd-item" ...> --}}
                            <li class="dd-item" data-issue-id="{{ $issue['id'] }}" wire:key="issue-{{ $issue['id'] }}" style="--tier-color: {{ $issue['type_color'] ?? '#607D8B' }};" x-data="{ editingTitle: false, titleDraft: @js($issue['summary']), editingDesc: false, descDraft: @js($issue['description'] ?? ''), saveTitle(){ $wire.updateIssueField('{{ $issue['id'] }}', 'summary', this.titleDraft); this.editingTitle = false;}, saveDesc(){ $wire.updateIssueField('{{ $issue['id'] }}', 'description', this.descDraft); this.editingDesc = false; } }">
                                <h3 class="title dd-handle d-flex align-items-start justify-content-between"
                                    :class="view==='kanban' ? 'card--' + '{{ $issue['tier'] ?? 'other' }}' : ''">
                                    <div class="me-2 w-100">
                                        <!-- Display mode -->
                                        <div x-show="!editingTitle" class="fw-semibold" @click.stop="editingTitle = true" role="button">
                                            <i class="material-icons me-1 align-text-bottom">filter_none</i>
                                            <span x-text="titleDraft"></span>
                                        </div>

                                        <!-- Edit mode -->
                                        <div x-show="editingTitle" class="js-no-drag w-100">
                                            <input type="text"
                                                   class="form-control form-control-sm"
                                                   x-model="titleDraft"
                                                   maxlength="200"
                                                   @keydown.enter.prevent="saveTitle()"
                                                   @keydown.escape.prevent="editingTitle=false"
                                                   @blur="saveTitle()"
                                                   autofocus
                                            />
                                        </div>
                                    </div>

                                    {{-- Tier chip --}}
                                    <x-issues.tier-badge
                                        :color="$issue['type_color'] ?? '#607D8B'"
                                        :icon="$issue['type_icon'] ?? 'filter_none'"
                                    />
                                </h3>

                                {{-- Description --}}
                                <div class="text">
                                    <!-- Display mode -->
                                    <div x-show="!editingDesc" @click.stop="editingDesc = true" role="button">
                                        @php($desc = \Illuminate\Support\Str::limit(strip_tags($issue['description'] ?? ''), 160))
                                        <span x-text="descDraft || @js($desc)"></span>
                                        <span x-show="!(descDraft?.length) && '{{ $desc }}' === ''" class="text-muted">Add a description…</span>
                                    </div>

                                    <!-- Edit mode -->
                                    <div x-show="editingDesc" class="js-no-drag">
            <textarea class="form-control form-control-sm"
                      rows="3"
                      x-model="descDraft"
                      maxlength="10000"
                      @keydown.meta.enter.prevent="saveDesc()"
                      @keydown.ctrl.enter.prevent="saveDesc()"
                      @keydown.escape.prevent="editingDesc=false"
                      @blur="saveDesc()"
                      autofocus
            ></textarea>
                                        <div class="form-text">Press ⌘/Ctrl+Enter to save, Esc to cancel.</div>
                                    </div>
                                </div>

                                {{-- Roll-up progress (unchanged) --}}
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
                                            wire:click="openIssue('{{ $issue['key'] }}')"
                                            title="Open">
                                        <i class="material-icons">edit</i>
                                    </button>

                                    <button type="button" class="btn btn-sm btn-light js-no-drag" title="Label"
                                            {{-- keep passing UUID string if quickColor expects id --}}
                                            wire:click="quickColor('{{ $issue['id'] }}')">
                                        <i class="material-icons">label</i>
                                    </button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </wa-scroller>

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
