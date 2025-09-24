(function () {
    tinymce.PluginManager.add('mentions-lite', function (editor) {
        // sourcery skip: avoid-function-declarations-in-blocks
        function termOf(pattern) {
            if (typeof pattern === 'string') {
              return pattern;
            }
            if (pattern && typeof pattern === 'object') {
              return pattern.term ?? pattern.query ?? pattern.text ?? '';
            }
            return '';
        }

        function buildUrl(path, params) {
            var u = new URL(path, location.origin);
            params = params || {};
            for (var k in params) {
                var v = params[k];
                if (v !== undefined && v !== null && String(v) !== '') {
                  u.searchParams.set(k, String(v));
                }
            }
            return u.toString();
        }

        // sourcery skip: avoid-function-declarations-in-blocks
        async function fetchJSON(url) {
            var res = await fetch(url, {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) { console.warn('mentions-lite request failed', res.status, url); return []; }
            return res.json();
        }

        // sourcery skip: avoid-function-declarations-in-blocks
        function toObj(v) {
            try { return typeof v === 'string' ? JSON.parse(v) : (v || {}); }
            catch { return {}; }
        }

        // sourcery skip: avoid-function-declarations-in-blocks
        function isApi(a)  { return a && typeof a === 'object' && (typeof a.hide === 'function' || typeof a.replace === 'function'); }
        // sourcery skip: avoid-function-declarations-in-blocks
        function isRange(a){ return a && typeof a === 'object' && (a.startContainer || a.commonAncestorContainer || a.nativeRange); }

        // sourcery skip: avoid-function-declarations-in-blocks
        function resolveActionArgs(a, b, c) {
            var api = null, rng = null, value = null, args = [a, b, c];
            for (var i = 0; i < 3; i++) {
                var x = args[i];
                if (typeof x === 'string' && value === null) { value = x; continue; }
                if (x && typeof x === 'object') {
                    if (isApi(x)) { api = x; continue; }
                    if (isRange(x)) { rng = x; continue; }
                    if (typeof x.value === 'string' && value === null) { value = x.value; continue; }
                }
            }
            return { api: api, rng: rng, value: value };
        }

        // sourcery skip: avoid-function-declarations-in-blocks
        function insertViaApiOrEditor(api, rng, html) {
            if (api && typeof api.replace === 'function') { api.replace(html); return; }
            if (rng && editor.selection && typeof editor.selection.setRng === 'function') {
              editor.selection.setRng(rng);
            }
            editor.insertContent(html);
            if (api && typeof api.hide === 'function') {
              api.hide();
            }
        }

        // Build safe HTML for insertion without calling escape helpers
        // sourcery skip: avoid-function-declarations-in-blocks
        function buildMentionHTML(v, prefix, type) {
            var doc = editor.getDoc();
            var a = doc.createElement('a');
            a.setAttribute('href', v.url || '#');
            a.setAttribute('data-mention-type', type);
            if (v.id != null) {
              a.setAttribute('data-mention-id', String(v.id));
            }
            a.appendChild(doc.createTextNode(prefix + (v.label || '')));
            return a.outerHTML + '&nbsp;';
        }

        // For the dropdown UI we still escape (using Tiny’s function if available)
        // sourcery skip: avoid-function-declarations-in-blocks
        function escapeHtmlSafe(s) {
            var str = (s == null ? '' : String(s));
            var fn = tinymce && tinymce.util && tinymce.util.Tools && tinymce.util.Tools.escapeHtml;
            if (typeof fn === 'function') {
              return fn(str);
            }
            // tiny fallback
            return str.replace(/[&<>"']/g, function (m) { return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]); });
        }

        // sourcery skip: avoid-function-declarations-in-blocks
        function renderCard(_api, data) {
            var v = toObj(data && data.value);
            var el = document.createElement('div');
            el.className = 'mention-item';
            el.innerHTML =
                '<div><strong>' + escapeHtmlSafe(v.label || (data && data.text) || '') +
                '</strong><small>' + escapeHtmlSafe(v.sublabel || '') +
                '</small></div>';
            return el; // Tiny wires click; we don’t manually attach handlers here.
        }

        // sourcery skip: avoid-using-var
        var projectId = (editor.getElement() && editor.getElement().dataset && editor.getElement().dataset.projectId) || '';

        // @user
        editor.ui.registry.addAutocompleter('mentionsUsers', {
            trigger: '@', ch: '@', minChars: 1, columns: 1,
            fetch: async function (pattern) {
                var q = termOf(pattern);
                var list = await fetchJSON(buildUrl('/api/v1/mentions/users', { q: q }));
                var out = [];
                for (var i = 0; i < list.length; i++) {
                    var u = list[i];
                    out.push({ type: 'menuitem', text: u.label, value: JSON.stringify(u) }); // value MUST be a string
                }
                return out;
            },
            onAction: function (api, rng, value) {
                var v = toObj(value);
                insertViaApiOrEditor(api, rng, buildMentionHTML(v, '@', 'user'));
            },
            itemRenderer: renderCard
        });

        // #issue
        editor.ui.registry.addAutocompleter('mentionsIssues', {
            trigger: '#', ch: '#', minChars: 1,
            fetch: async function (pattern) {
                var q = termOf(pattern);
                var list = await fetchJSON(buildUrl('/api/v1/mentions/issues', { q: q, project_id: projectId }));
                var out = [];
                for (var i = 0; i < list.length; i++) {
                    var it = list[i];
                    out.push({ type: 'menuitem', text: it.label, value: JSON.stringify(it) });
                }
                return out;
            },
            onAction: function (api, rng, value) {
                var v = toObj(value);
                insertViaApiOrEditor(api, rng, buildMentionHTML(v, '#', 'issue'));
            },
            itemRenderer: renderCard
        });

        // toolbar
        editor.ui.registry.addButton('mentionUser',  { text: '@', onAction: function () { editor.execCommand('mceInsertContent', false, '@'); }});
        editor.ui.registry.addButton('mentionIssue', { text: '#', onAction: function () { editor.execCommand('mceInsertContent', false, '#'); }});
    });
})();
