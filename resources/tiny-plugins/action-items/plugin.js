(function () {
  const uuid = () =>
    'ai-' +
        ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, (c) =>
          (
            c ^
                (crypto.getRandomValues(new Uint8Array(1))[0] & (15 >> (c / 4)))
          ).toString(16)
        )

  tinymce.PluginManager.add('action-items', function (editor) {
    const insertChecklist = () => {
      const listId = uuid()
      const itemId = uuid()
      editor.insertContent(
                `<ul class="ai-checklist" data-ai-list-id="${listId}">
           <li data-ai-id="${itemId}" data-ai-checked="false" role="checkbox" aria-checked="false" tabindex="0">
             <label class="ai-item">
               <input type="checkbox" aria-hidden="true">
               <span class="ai-text">New action item</span>
             </label>
           </li>
         </ul>`
      )
    }

    // Toggle by click or keyboard inside the editor
    const maybeToggle = (li) => {
      const isChecked = li.getAttribute('data-ai-checked') === 'true'
      const next = !isChecked
      li.setAttribute('data-ai-checked', String(next))
      li.setAttribute('aria-checked', String(next))
      const cb = li.querySelector('input[type="checkbox"]')
      if (cb) {
        cb.checked = next
      }
      editor.undoManager.add()
    }

    editor.on('click', (e) => {
      const li = e.target?.closest && e.target.closest('li[data-ai-id]')
      if (li && editor.getBody().contains(li)) {
        maybeToggle(li)
      }
    })
    editor.on('keydown', (e) => {
      if (e.key === ' ' || e.key === 'Enter') {
        const li = editor.selection
          ?.getNode()
          ?.closest?.('li[data-ai-id]')
        if (li) {
          maybeToggle(li)
          e.preventDefault()
        }
      }
    })

    // Sync checkboxes on load
    const sync = () => {
      editor
        .getBody()
        .querySelectorAll('li[data-ai-id]')
        .forEach((li) => {
          const checked =
                        li.getAttribute('data-ai-checked') === 'true'
          let cb = li.querySelector('input[type="checkbox"]')
          if (!cb) {
            cb = editor.getDoc().createElement('input')
            cb.type = 'checkbox'
            cb.setAttribute('aria-hidden', 'true');
            (li.querySelector('label.ai-item') || li).insertBefore(
              cb,
              li.firstChild
            )
          }
          cb.checked = checked
          li.setAttribute('role', 'checkbox')
          li.setAttribute('aria-checked', String(checked))
          li.setAttribute('tabindex', '0')
        })
    }
    editor.on('LoadContent SetContent', sync)

    editor.ui.registry.addButton('checklist', {
      text: 'Checklist',
      tooltip: 'Insert checklist',
      onAction: insertChecklist
    })

    return { getMetadata: () => ({ name: 'Action Items' }) }
  })
})()
