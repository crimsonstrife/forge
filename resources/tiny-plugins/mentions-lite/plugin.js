(function () {
    tinymce.PluginManager.add('mentions-lite', function (editor) {
        const esc = tinymce.util.Tools.escapeHtml;

        const renderCard = (api, data) => {
            const el = document.createElement('div');
            el.className = 'mention-item';
            el.innerHTML = `<div><strong>${esc(data.value.label)}</strong>
                        <small>${esc(data.value.sublabel || '')}</small>
                      </div>`;
            el.addEventListener('click', () => api.onAction(data));
            return el;
        };

        // @user mentions
        editor.ui.registry.addAutocompleter('mentionsUsers', {
            // TinyMCE 7+ requires "trigger"; keep "ch" for older versions (harmless on 7+)
            trigger: '@',
            ch: '@',
            minChars: 1,
            columns: 1,
            fetch: async (pattern) => {
                const res = await fetch(`/api/mentions/users?q=${encodeURIComponent(pattern.term)}`);
                const list = await res.json(); // [{id,label,sublabel,url}]
                return list.map(u => ({ type: 'cardmenuitem', value: u, label: u.label }));
            },
            onAction: (api, rng, value) => {
                editor.selection.setRng(rng);
                editor.insertContent(
                    `<a href="${value.url}" data-mention-type="user" data-mention-id="${esc(value.id)}">@${esc(value.label)}</a>&nbsp;`
                );
                api.hide();
            },
            itemRenderer: renderCard
        });

        // #issue mentions
        editor.ui.registry.addAutocompleter('mentionsIssues', {
            trigger: '#',
            ch: '#',
            minChars: 1,
            fetch: async (pattern) => {
                const res = await fetch(`/api/mentions/issues?q=${encodeURIComponent(pattern.term)}`);
                const list = await res.json(); // [{id,label,sublabel,url}]
                return list.map(i => ({ type: 'menuitem', text: i.label, value: i }));
            },
            onAction: (api, rng, value) => {
                editor.selection.setRng(rng);
                editor.insertContent(
                    `<a href="${value.url}" data-mention-type="issue" data-mention-id="${esc(value.id)}">#${esc(value.label)}</a>&nbsp;`
                );
                api.hide();
            }
        });

        // Toolbar helpers
        editor.ui.registry.addButton('mentionUser',  { text: '@', onAction: () => editor.execCommand('mceInsertContent', false, '@') });
        editor.ui.registry.addButton('mentionIssue', { text: '#', onAction: () => editor.execCommand('mceInsertContent', false, '#') });
    });
})();
