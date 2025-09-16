// resources/js/kanban.js
import Sortable from 'sortablejs';

function initKanbanSortables() {
    document.querySelectorAll('[data-kanban-list]').forEach((el) => {
        // Prevent double-init on morphs
        if (el.__kanbanInited) { return; }
        el.__kanbanInited = true;

        Sortable.create(el, {
            group: 'kanban',
            handle: '.dd-handle',
            animation: 150,
            ghostClass: 'dd-placeholder',
            filter: 'input, textarea, [contenteditable], .js-no-drag',
            preventOnFilter: false,
            onEnd: (evt) => {
                const toStatusId = evt.to?.dataset?.statusId; // status ids are ints; strings are fine
                // KEEP UUIDs AS STRINGS (no Number()/parseInt)
                const orderedIssueIds = [...evt.to.querySelectorAll('[data-issue-id]')]
                    .map(li => li.dataset.issueId);

                // Find the closest Livewire component root for this list and call `reorder`
                const compRoot = evt.to.closest('[wire\\:id]');
                if (!compRoot) { return; }

                const comp = window.Livewire?.find?.(compRoot.getAttribute('wire:id'));
                if (comp) {
                    comp.call('reorder', Number(toStatusId), orderedIssueIds);
                }
            },
        });
    });
}

// Expose globally for Alpine x-init
window.initKanbanSortables = initKanbanSortables;

// Re-init after Livewire DOM updates (morphs & navigation)
document.addEventListener('livewire:navigated', initKanbanSortables);
document.addEventListener('kanban:init', initKanbanSortables);

// Fallback initial load
window.addEventListener('DOMContentLoaded', initKanbanSortables);
