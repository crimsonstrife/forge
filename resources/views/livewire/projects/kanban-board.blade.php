<div
    x-data
    class="grid gap-4 md:gap-6"
    style="grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));"
>
    @foreach ($columns as $col)
        <div class="rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 flex flex-col">
            <div class="px-3 py-2 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <span class="text-sm font-semibold">
                    {{ $col['name'] }}
                </span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-200/70 dark:bg-gray-700/70">
                    {{ count($lists[$col['id']] ?? []) }}
                </span>
            </div>

            <div
                class="p-2 space-y-2 min-h-24"
                wire:ignore
                data-kanban-column
                data-status-id="{{ $col['id'] }}"
            >
                @foreach (($lists[$col['id']] ?? []) as $card)
                    <div
                        class="rounded-lg bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 p-3 cursor-move"
                        data-kanban-card
                        data-issue-id="{{ $card['id'] }}"
                    >
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span class="font-medium">{{ $card['key'] }}</span>
                            <span>{{ $card['type'] }}</span>
                        </div>
                        <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $card['summary'] }}</div>
                        @if($card['assignee'])
                            <div class="mt-2 text-xs text-gray-500">Assignee: {{ $card['assignee'] }}</div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="p-2">
                @can('create', App\Models\Issue::class)
                    <a href="#"
                       class="block w-full text-center text-xs border rounded-md py-1 hover:bg-gray-100 dark:hover:bg-gray-700">
                        + New issue
                    </a>
                @endcan
            </div>
        </div>
    @endforeach
</div>

{{-- SortableJS --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const groupName = 'kanban-{{ $project->id }}';
        const boards = document.querySelectorAll('[data-kanban-column]');

        boards.forEach(board => {
            new Sortable(board, {
                group: groupName,
                animation: 150,
                ghostClass: 'opacity-60',
                dragClass: 'ring-2 ring-primary-500',
                onAdd: (evt) => handleDrop(evt),
                onUpdate: (evt) => handleDrop(evt),
            });
        });

        function handleDrop(evt) {
            const item = evt.item;
            const to = evt.to;
            const issueId = item?.dataset?.issueId;
            const toStatusId = parseInt(to?.dataset?.statusId ?? 0, 10);
            const newIndex = evt.newIndex ?? 0;
            const fromStatusId = parseInt(evt.from?.dataset?.statusId ?? 0, 10);

            if (!issueId || !toStatusId) return;

            // Livewire v3 global dispatch
            window.Livewire.dispatch('kanban:issue-dropped', {
                issueId,
                toStatusId,
                newIndex,
                fromStatusId,
            });
        }
    });
</script>
