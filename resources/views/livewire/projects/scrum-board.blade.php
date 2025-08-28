<div class="space-y-6" x-data>
    {{-- Backlog --}}
    <div class="rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700">
        <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <div class="font-semibold">Backlog</div>
            <div class="text-xs text-gray-500">{{ count($backlog) }} items</div>
        </div>
        <div class="p-3 grid gap-2 sm:grid-cols-2 lg:grid-cols-3" wire:ignore>
            @foreach ($backlog as $item)
                <div
                    class="rounded-lg bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 p-3 flex items-center justify-between"
                    data-backlog-card
                    data-issue-id="{{ $item['id'] }}"
                >
                    <div>
                        <div class="text-xs text-gray-500">{{ $item['key'] }}</div>
                        <div class="text-sm">{{ $item['summary'] }}</div>
                    </div>
                    <button type="button"
                            class="text-xs px-2 py-1 border rounded-md hover:bg-gray-100 dark:hover:bg-gray-700"
                            x-on:click="window.Livewire.dispatch('scrum:move-to-sprint', {issueId: '{{ $item['id'] }}'})">
                        Add to sprint
                    </button>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Sprint board (columns by status) --}}
    <div class="grid gap-4 md:gap-6" style="grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));">
        @foreach ($sprintColumns as $col)
            <div class="rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 flex flex-col">
                <div class="px-3 py-2 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <span class="text-sm font-semibold">{{ $col['name'] }}</span>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-gray-200/70 dark:bg-gray-700/70">
                        {{ count($sprintLists[$col['id']] ?? []) }}
                    </span>
                </div>

                <div class="p-2 space-y-2 min-h-24" wire:ignore data-sprint-col data-status-id="{{ $col['id'] }}">
                    @foreach (($sprintLists[$col['id']] ?? []) as $card)
                        <div class="rounded-lg bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 p-3"
                             data-sprint-card data-issue-id="{{ $card['id'] }}">
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span class="font-medium">{{ $card['key'] }}</span>
                                <button type="button"
                                        class="px-2 py-0.5 border rounded"
                                        x-on:click="window.Livewire.dispatch('scrum:move-to-backlog', {issueId: '{{ $card['id'] }}'})">
                                    ← Backlog
                                </button>
                            </div>
                            <div class="mt-1 text-sm">{{ $card['summary'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const groupName = 'sprint-board-{{ $project->id }}';
        document.querySelectorAll('[data-sprint-col]').forEach(col => {
            new Sortable(col, {
                group: groupName,
                animation: 150,
                onAdd: (evt) => drop(evt),
                onUpdate: (evt) => drop(evt),
            });
        });

        function drop(evt) {
            const issueId = evt.item?.dataset?.issueId;
            const toStatusId = parseInt(evt.to?.dataset?.statusId ?? 0, 10);
            if (!issueId || !toStatusId) return;

            window.Livewire.dispatch('scrum:status-changed', {
                issueId,
                toStatusId,
            });
        }
    });
</script>
