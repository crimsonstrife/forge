import Sortable from 'sortablejs'

window.initKanbanSortables = () => {
  // One Sortable per column (same group so items can move between columns)
  document.querySelectorAll('[data-kanban-list]').forEach((el) => {
    Sortable.create(el, {
      group: 'kanban',
      handle: '.dd-handle',
      animation: 150,
      ghostClass: 'dd-placeholder',
      onEnd: (evt) => {
        const toStatusId = evt.to?.dataset?.statusId
        const orderedIssueIds = [
          ...evt.to.querySelectorAll('[data-issue-id]')
        ].map((li) => Number(li.dataset.issueId))

        // Tell Livewire the new order for the target column (and column change if moved)
        if (
          window.Livewire &&
                    typeof window.Livewire.find === 'function'
        ) {
          // find the first component on the page (this view)
          const comp = Object.values(
            window.Livewire.components.componentsById || {}
          )[0]
          if (comp) {
            comp.call(
              'reorder',
              Number(toStatusId),
              orderedIssueIds
            )
          }
        }
      }
    })
  })
}
