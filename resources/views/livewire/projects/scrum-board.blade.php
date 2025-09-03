@php use App\Models\Sprint; @endphp
<div class="space-y-6" x-data>
    {{-- Sprint header --}}
    <div class="rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700">
        <div class="px-4 py-3 flex items-center justify-between">
            <div>
                @if ($currentSprintId)
                    @php
                        /** @var Sprint|null $activeSprint */
                        $activeSprint = $project->sprints()->whereKey($currentSprintId)->first();
                    @endphp
                    <div class="font-semibold">Active
                        Sprint{{ $activeSprint?->name ? ': '.$activeSprint->name : '' }}</div>
                    @if ($activeSprint && $activeSprint->start_date && $activeSprint->end_date)
                        <div class="text-xs text-gray-500">
                            {{ $activeSprint->start_date->toFormattedDateString() }} &rarr; {{ $activeSprint->end_date->toFormattedDateString() }}
                        </div>
                    @endif
                @else
                    <div class="font-semibold">No active sprint</div>
                    <div class="text-xs text-gray-500">Move to sprint is disabled until a sprint is activated.</div>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <button type="button"
                        class="text-xs px-3 py-1.5 border rounded-md hover:bg-gray-100 dark:hover:bg-gray-700"
                        wire:click="openSprints">
                    View sprints
                </button>

                @if ($currentSprintId)
                    <button type="button"
                            class="text-xs px-3 py-1.5 border rounded-md bg-rose-50 dark:bg-rose-900/30 hover:bg-rose-100 dark:hover:bg-rose-900/50"
                            wire:click="openEndSprint">
                        End sprint
                    </button>
                @else
                    <button type="button"
                            class="text-xs px-3 py-1.5 border rounded-md bg-emerald-50 dark:bg-emerald-900/30 hover:bg-emerald-100 dark:hover:bg-emerald-900/50"
                            wire:click="openCreateSprint">
                        New sprint
                    </button>
                @endif
            </div>
        </div>
    </div>
    {{-- Backlog --}}
    <div class="rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700">
        <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <div class="font-semibold">Backlog</div>
            <div class="text-xs text-gray-500">{{ count($backlog) }} items</div>
        </div>
        <div class="p-3 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($backlog as $item)
                <div
                    class="rounded-lg bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 p-3 flex items-center justify-between"
                    data-backlog-card
                    data-issue-id="{{ $item['id'] }}"
                    wire:key="backlog-{{ $item['id'] }}"
                    style="--tier-color: {{ $item['type_color'] ?? '#607D8B' }};"
                >
                    <div class="pr-3">
                        <div class="text-xs text-gray-500 flex items-center gap-2">
                            <span class="font-medium">{{ $item['key'] }}</span>
                            {{-- Tier chip --}}
                            <x-issues.tier-badge
                                :color="$item['type_color'] ?? '#607D8B'"
                                :icon="$item['type_icon'] ?? 'filter_none'"
                            />
                        </div>
                        <div class="mt-1 text-sm">{{ $item['summary'] }}</div>

                        @if(($item['progress'] ?? null) !== null)
                            <div class="mt-2">
                                <div class="w-full h-1.5 rounded bg-gray-200 dark:bg-gray-700">
                                    <div class="h-1.5 rounded" style="width: {{ $item['progress'] }}%; background: {{ $item['type_color'] }};"></div>
                                </div>
                                <div class="mt-1 text-[10px] text-gray-500">
                                    {{ $item['children_done'] }} / {{ $item['children_total'] }} ({{ $item['progress'] }}%)
                                </div>
                            </div>
                        @endif
                    </div>

                    @if ($currentSprintId)
                        <button type="button"
                                class="text-xs px-2 py-1 border rounded-md hover:bg-gray-100 dark:hover:bg-gray-700"
                                x-on:click="window.Livewire.dispatch('scrum:move-to-sprint', {issueId: '{{ $item['id'] }}'})">
                            Add to sprint
                        </button>
                    @else
                        <span class="text-xs text-gray-400">No active sprint</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Sprint board (columns by status) --}}
    <div class="grid gap-4 md:gap-6" style="grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));">
        @foreach ($sprintColumns as $col)
            <div
                class="rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 flex flex-col"
                wire:key="col-{{ $col['id'] }}">
                <div class="px-3 py-2 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <span class="text-sm font-semibold">{{ $col['name'] }}</span>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-gray-200/70 dark:bg-gray-700/70">
                        {{ count($sprintLists[$col['id']] ?? []) }}
                    </span>
                </div>

                <div class="p-2 space-y-2 min-h-24" data-sprint-col data-status-id="{{ $col['id'] }}">
                    @foreach (($sprintLists[$col['id']] ?? []) as $card)
                        <div
                            class="rounded-lg bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 p-3"
                            data-sprint-card data-issue-id="{{ $card['id'] }}" wire:key="card-{{ $card['id'] }}"
                            style="--tier-color: {{ $card['type_color'] ?? '#607D8B' }};"
                        >
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span class="font-medium">{{ $card['key'] }}</span>
                                <button type="button"
                                        class="px-2 py-0.5 border rounded"
                                        x-on:click="window.Livewire.dispatch('scrum:move-to-backlog', {issueId: '{{ $card['id'] }}'})">
                                    ← Backlog
                                </button>
                            </div>

                            <div class="mt-1 text-sm flex items-center justify-between gap-2">
                                <span>{{ $card['summary'] }}</span>
                                {{-- Tier chip --}}
                                <x-issues.tier-badge
                                    :color="$card['type_color'] ?? '#607D8B'"
                                    :icon="$card['type_icon'] ?? 'filter_none'"
                                />
                            </div>

                            @if(($card['progress'] ?? null) !== null)
                                <div class="mt-2">
                                    <div class="w-full h-1.5 rounded bg-gray-200 dark:bg-gray-700">
                                        <div class="h-1.5 rounded" style="width: {{ $card['progress'] }}%; background: {{ $card['type_color'] }};"></div>
                                    </div>
                                    <div class="mt-1 text-[10px] text-gray-500">
                                        {{ $card['children_done'] }} / {{ $card['children_total'] }} ({{ $card['progress'] }}%)
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    {{-- Create Sprint Modal --}}
    @if ($showCreateSprint)
        <div class="fixed inset-0 z-40 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" wire:click="$set('showCreateSprint', false)"></div>
            <div class="relative z-50 w-full max-w-lg rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 p-4">
                <div class="text-sm font-semibold mb-2">Create sprint</div>

                <div class="grid gap-3">
                    <div>
                        <label class="text-xs text-gray-500">Name</label>
                        <input type="text" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800"
                               wire:model.defer="newSprint.name">
                        @error('newSprint.name') <div class="text-xs text-rose-500 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="text-xs text-gray-500">Goal (optional)</label>
                        <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800"
                                  wire:model.defer="newSprint.goal"></textarea>
                        @error('newSprint.goal') <div class="text-xs text-rose-500 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-gray-500">Start date</label>
                            <input type="date" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800"
                                   wire:model.defer="newSprint.start_date">
                            @error('newSprint.start_date') <div class="text-xs text-rose-500 mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">End date</label>
                            <input type="date" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800"
                                   wire:model.defer="newSprint.end_date">
                            @error('newSprint.end_date') <div class="text-xs text-rose-500 mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <label class="inline-flex items-center gap-2 text-xs">
                        <input type="checkbox" wire:model.defer="newSprint.start_now"
                               class="rounded border-gray-300 dark:border-gray-700">
                        Start sprint immediately
                    </label>
                </div>

                <div class="mt-4 flex items-center justify-end gap-2">
                    <button type="button" class="text-xs px-3 py-1.5 border rounded-md"
                            wire:click="$set('showCreateSprint', false)">Cancel</button>
                    <button type="button" class="text-xs px-3 py-1.5 border rounded-md bg-emerald-600 text-white"
                            wire:click="createSprint">Create</button>
                </div>
            </div>
        </div>
    @endif
    {{-- End Sprint Modal --}}
    @if ($showEndSprint)
        <div class="fixed inset-0 z-40 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" wire:click="$set('showEndSprint', false)"></div>
            <div class="relative z-50 w-full max-w-lg rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 p-4">
                <div class="text-sm font-semibold mb-2">End sprint</div>
                <p class="text-xs text-gray-500 mb-3">
                    Do you want to move incomplete issues back to the backlog?
                </p>

                <label class="inline-flex items-center gap-2 text-xs">
                    <input type="checkbox" wire:model="rolloverIncomplete"
                           class="rounded border-gray-300 dark:border-gray-700">
                    Move incomplete issues to backlog
                </label>

                <div class="mt-4 flex items-center justify-end gap-2">
                    <button type="button" class="text-xs px-3 py-1.5 border rounded-md"
                            wire:click="$set('showEndSprint', false)">Cancel</button>
                    <button type="button" class="text-xs px-3 py-1.5 border rounded-md bg-rose-600 text-white"
                            wire:click="endCurrentSprint">End sprint</button>
                </div>
            </div>
        </div>
    @endif
    {{-- View Sprints Modal --}}
    @if ($showSprintsModal)
        <div class="fixed inset-0 z-40 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" wire:click="$set('showSprintsModal', false)"></div>
            <div class="relative z-50 w-full max-w-2xl rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="text-sm font-semibold">Sprints</div>
                    <div class="flex items-center gap-2">
                        <button type="button" class="text-xs px-3 py-1.5 border rounded-md"
                                wire:click="$set('showSprintsModal', false)">Close</button>
                        <button type="button" class="text-xs px-3 py-1.5 border rounded-md bg-emerald-50 dark:bg-emerald-900/30"
                                wire:click="openCreateSprint">New sprint</button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="text-left text-xs text-gray-500 border-b border-gray-200 dark:border-gray-700">
                            <th class="py-2 pr-2">Name</th>
                            <th class="py-2 pr-2">State</th>
                            <th class="py-2 pr-2">Start</th>
                            <th class="py-2 pr-2">End</th>
                            <th class="py-2 pr-2"></th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($sprints as $s)
                            <tr>
                                <td class="py-2 pr-2">{{ $s['name'] }}</td>
                                <td class="py-2 pr-2">
                                <span class="text-xs px-2 py-0.5 rounded-full border">
                                    {{ $s['state'] }}
                                </span>
                                </td>
                                <td class="py-2 pr-2">{{ $s['start_date'] ?? '—' }}</td>
                                <td class="py-2 pr-2">{{ $s['end_date'] ?? '—' }}</td>
                                <td class="py-2 pr-2 text-right">
                                    @if ($s['state'] === \App\Enums\SprintState::Planned)
                                        <button type="button" class="text-xs px-2 py-1 border rounded-md"
                                                wire:click="startSprint('{{ $s['id'] }}')">
                                            Start
                                        </button>
                                    @elseif ($s['state'] === \App\Enums\SprintState::Active)
                                        <button type="button" class="text-xs px-2 py-1 border rounded-md"
                                                wire:click="openEndSprint">
                                            End
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 text-center text-xs text-gray-500">No sprints yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
    const initSprintSortable = () => {
        const groupName = 'sprint-board-{{ (string) $project->id }}';

        document.querySelectorAll('[data-sprint-col]').forEach(col => {
            // Destroy any previously attached Sortable instance before creating a new one
            if (col._sortable) {
                try {
                    col._sortable.destroy();
                } catch (e) {
                }
            }

            col._sortable = new Sortable(col, {
                group: groupName,
                animation: 150,
                onAdd: (evt) => handleDrop(evt),
                onUpdate: (evt) => handleDrop(evt),
            });
        });
    };

    function handleDrop(evt) {
        const issueId = evt.item?.dataset?.issueId;
        const toStatusId = parseInt(evt.to?.dataset?.statusId ?? 0, 10);
        if (!issueId || !toStatusId) {
            return;
        }
        window.Livewire.dispatch('scrum:status-changed', {issueId, toStatusId});
    }

    document.addEventListener('livewire:init', () => {
        initSprintSortable();
        // Re-init after every Livewire DOM update so the lists stay sortable
        if (window.Livewire?.hook) {
            Livewire.hook('message.processed', () => initSprintSortable());
        } else {
            // Fallback for environments without hook (shouldn't happen on v3)
            document.addEventListener('livewire:update', () => initSprintSortable());
        }
    });
</script>
