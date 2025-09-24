(function () {
    tinymce.PluginManager.add('mentions-lite', function (editor) {
        const esc = tinymce.util.Tools.escapeHtml;

        const termOf = (pattern) => {
            if (typeof pattern === 'string') { return pattern; }
            if (pattern && typeof pattern === 'object') {
                return pattern.term ?? pattern.query ?? pattern.text ?? '';
            }
            return '';
        };

        // Build URL with params
        const buildUrl = (path, params = {}) => {
            const u = new URL(path, location.origin);
            Object.entries(params).forEach(([k, v]) => {
                if (v !== undefined && v !== null && String(v) !== '') {
                    u.searchParams.set(k, String(v));
                }
            });
            return u.toString();
        };

        // Fetch JSON with session cookies + headers so Sanctum treats it as stateful
        const fetchJSON = async (url) => {
            const res = await fetch(url, {
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            if (!res.ok) {
                // Surface auth/redirect problems as empty results instead of blowing up the UI
                console.warn('mentions-lite request failed', res.status, url);
                return [];
            }
            // If a redirect returned HTML, this will throw and we’ll catch below.
            return res.json();
        };

        const renderCard = (api, data) => {
            const v = toObj(data.value);
            const el = document.createElement('div');
            el.className = 'mention-item';
            el.innerHTML = `<div><strong>${esc(v.label || '')}</strong>
                   <small>${esc(v.sublabel || '')}</small></div>`;
            // ensure clicking the custom element triggers onAction
            el.addEventListener('click', () => api.onAction({ ...data, value: JSON.stringify(v) }));
            return el;
        };

        // Scope issues to a project via <textarea data-project-id="...">
        const projectId = editor.getElement()?.dataset?.projectId || '';

        const toObj = (v) => {
          try { return typeof v === 'string' ? JSON.parse(v) : (v ?? {}); }
          catch { return {}; }
        };

        // @user mentions
        editor.ui.registry.addAutocompleter('mentionsUsers', {
            trigger: '@', ch: '@', minChars: 1, columns: 1,
            fetch: async (pattern) => {
                try {
                    const q = termOf(pattern);
                    const url = buildUrl('/api/v1/mentions/users', { q });
                    const list = await fetchJSON(url); // [{id,label,sublabel,url}]
                    return list.map(u => ({ type: 'menuitem', text: u.label, value: JSON.stringify(u) }));
                } catch (e) {
                    console.warn('mentionsUsers fetch error', e);
                    return [];
                }
            },
            onAction: (api, rng, value) => {
                const v = toObj(value);
                editor.selection.setRng(rng);
                editor.insertContent(
                      `<a href="${v.url}" data-mention-type="user" data-mention-id="${esc(v.id)}">@${esc(v.label)}</a>&nbsp;`
                );
                api.hide();
            },
            itemRenderer: renderCard
        });

        // #issue mentions
        editor.ui.registry.addAutocompleter('mentionsIssues', {
            trigger: '#', ch: '#', minChars: 1,
            fetch: async (pattern) => {
                try {
                    const q = termOf(pattern);
                    const url = buildUrl('/api/v1/mentions/issues', { q, project_id: projectId });
                    const list = await fetchJSON(url); // [{id,label,sublabel,url}]
                    return list.map(i => ({ type: 'menuitem', text: i.label, value: JSON.stringify(i) }));
                } catch (e) {
                    console.warn('mentionsIssues fetch error', e);
                    return [];
                }
            },
            onAction: (api, rng, value) => {
                const v = toObj(value);
                editor.selection.setRng(rng);
                editor.insertContent(
                      `<a href="${v.url}" data-mention-type="issue" data-mention-id="${esc(v.id)}">#${esc(v.label)}</a>&nbsp;`
                );
                api.hide();
            }
        });

        // Toolbar helpers
        editor.ui.registry.addButton('mentionUser',  { text: '@', onAction: () => editor.execCommand('mceInsertContent', false, '@') });
        editor.ui.registry.addButton('mentionIssue', { text: '#', onAction: () => editor.execCommand('mceInsertContent', false, '#') });
    });
})();
