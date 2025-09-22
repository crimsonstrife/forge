(function () {
  tinymce.PluginManager.add('mentions-lite', function (editor) {
    const renderCard = (api, data) => {
      const el = document.createElement('div')
      el.className = 'mention-item'
      el.innerHTML = `<div><strong>${tinymce.util.Tools.escapeHtml(data.value.label)}</strong>
                        <small>${tinymce.util.Tools.escapeHtml(data.value.sublabel || '')}</small>
                      </div>`
      el.addEventListener('click', () => api.onAction(data))
      return el
    }

    editor.ui.registry.addAutocompleter('mentionsUsers', {
      ch: '@',
      minChars: 1,
      columns: 1,
      fetch: async (pattern) => {
        const res = await fetch(
                    `/api/mentions/users?q=${encodeURIComponent(pattern.term)}`
        )
        const list = await res.json() // [{id,label,sublabel,url}]
        return list.map((u) => ({
          type: 'cardmenuitem',
          value: u,
          label: u.label
        }))
      },
      onAction: (api, rng, value) => {
        editor.selection.setRng(rng)
        editor.insertContent(
                    `<a href="${value.url}" data-mention-type="user" data-mention-id="${value.id}">@${tinymce.util.Tools.escapeHtml(value.label)}</a>&nbsp;`
        )
        api.hide()
      },
      itemRenderer: renderCard
    })

    editor.ui.registry.addAutocompleter('mentionsIssues', {
      ch: '#',
      minChars: 1,
      fetch: async (pattern) => {
        const res = await fetch(
                    `/api/mentions/issues?q=${encodeURIComponent(pattern.term)}`
        )
        const list = await res.json() // [{id,label,sublabel,url}]
        return list.map((i) => ({
          type: 'menuitem',
          text: i.label,
          value: i
        }))
      },
      onAction: (api, rng, value) => {
        editor.selection.setRng(rng)
        editor.insertContent(
                    `<a href="${value.url}" data-mention-type="issue" data-mention-id="${value.id}">#${tinymce.util.Tools.escapeHtml(value.label)}</a>&nbsp;`
        )
        api.hide()
      }
    })

    // Optional toolbar helpers
    editor.ui.registry.addButton('mentionUser', {
      text: '@',
      onAction: () => editor.execCommand('mceInsertContent', false, '@')
    })
    editor.ui.registry.addButton('mentionIssue', {
      text: '#',
      onAction: () => editor.execCommand('mceInsertContent', false, '#')
    })
  })
})()
