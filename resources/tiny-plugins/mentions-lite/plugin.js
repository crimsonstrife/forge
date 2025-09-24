(function () {
    tinymce.PluginManager.add("mentions-lite", function (editor) {
        const esc = tinymce.util.Tools.escapeHtml;

        function termOf(pattern) {
            if (typeof pattern === "string") return pattern;
            if (pattern && typeof pattern === "object") {
                return pattern.term ?? pattern.query ?? pattern.text ?? "";
            }
            return "";
        }

        function buildUrl(path, params) {
            const u = new URL(path, location.origin);
            for (const k in params || {}) {
                const v = params[k];
                if (v !== undefined && v !== null && String(v) !== "") {
                    u.searchParams.set(k, String(v));
                }
            }
            return u.toString();
        }

        async function fetchJSON(url) {
            const res = await fetch(url, {
                credentials: "same-origin",
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });
            if (!res.ok) {
                console.warn("mentions-lite request failed", res.status, url);
                return [];
            }
            return res.json();
        }

        function toObj(v) {
            try {
                return typeof v === "string" ? JSON.parse(v) : v || {};
            } catch {
                return {};
            }
        }

        // Identify args without Array.prototype helpers to avoid minifier aliasing
        function resolveActionArgs(a, b, c) {
            let api = null;
            let rng = null;
            let value = null;

            // Scan each arg once
            const args = [a, b, c];
            for (let i = 0; i < 3; i++) {
                const x = args[i];
                if (typeof x === "string" && value === null) {
                    value = x;
                    continue;
                }
                if (x && typeof x === "object") {
                    // Autocompleter API object (has hide/replace functions)
                    if (
                        typeof x.hide === "function" ||
                        typeof x.replace === "function"
                    ) {
                        api = x;
                        continue;
                    }
                    // Range-like object
                    if (
                        x.startContainer ||
                        x.commonAncestorContainer ||
                        x.nativeRange
                    ) {
                        rng = x;
                        continue;
                    }
                    // Some builds pass { value: '...' }
                    if (typeof x.value === "string" && value === null) {
                        value = x.value;
                        continue;
                    }
                }
            }
            return { api, rng, value };
        }

        function insertViaApiOrEditor(api, rng, html) {
            if (api && typeof api.replace === "function") {
                api.replace(html);
                return;
            }
            if (
                rng &&
                editor.selection &&
                typeof editor.selection.setRng === "function"
            ) {
                editor.selection.setRng(rng);
            }
            editor.insertContent(html);
            if (api && typeof api.hide === "function") api.hide();
        }

        function renderCard(_api, data) {
            const v = toObj(data && data.value);
            const el = document.createElement("div");
            el.className = "mention-item";
            el.innerHTML =
                "<div><strong>" +
                esc(v.label || (data && data.text) || "") +
                "</strong><small>" +
                esc(v.sublabel || "") +
                "</small></div>";
            return el; // Tiny wires click -> onAction with our value
        }

        const projectId =
            (editor.getElement() &&
                editor.getElement().dataset &&
                editor.getElement().dataset.projectId) ||
            "";

        // @user mentions
        editor.ui.registry.addAutocompleter("mentionsUsers", {
            trigger: "@",
            ch: "@",
            minChars: 1,
            columns: 1,
            fetch: async function (pattern) {
                const q = termOf(pattern);
                const list = await fetchJSON(
                    buildUrl("/api/v1/mentions/users", { q }),
                );
                // Use menuitem + string value; Tiny will pass that string to onAction
                const out = [];
                for (let i = 0; i < list.length; i++) {
                    const u = list[i];
                    out.push({
                        type: "menuitem",
                        text: u.label,
                        value: JSON.stringify(u),
                    });
                }
                return out;
            },
            onAction: function (a, b, c) {
                const resolved = resolveActionArgs(a, b, c);
                const v = toObj(resolved.value);
                const html =
                    '<a href="' +
                    v.url +
                    '" data-mention-type="user" data-mention-id="' +
                    esc(v.id) +
                    '">@' +
                    esc(v.label) +
                    "</a>&nbsp;";
                insertViaApiOrEditor(resolved.api, resolved.rng, html);
            },
            itemRenderer: renderCard,
        });

        // #issue mentions
        editor.ui.registry.addAutocompleter("mentionsIssues", {
            trigger: "#",
            ch: "#",
            minChars: 1,
            fetch: async function (pattern) {
                const q = termOf(pattern);
                const list = await fetchJSON(
                    buildUrl("/api/v1/mentions/issues", {
                        q,
                        project_id: projectId,
                    }),
                );
                const out = [];
                for (let i = 0; i < list.length; i++) {
                    const it = list[i];
                    out.push({
                        type: "menuitem",
                        text: it.label,
                        value: JSON.stringify(it),
                    });
                }
                return out;
            },
            onAction: function (a, b, c) {
                const resolved = resolveActionArgs(a, b, c);
                const v = toObj(resolved.value);
                const html =
                    '<a href="' +
                    v.url +
                    '" data-mention-type="issue" data-mention-id="' +
                    esc(v.id) +
                    '">#' +
                    esc(v.label) +
                    "</a>&nbsp;";
                insertViaApiOrEditor(resolved.api, resolved.rng, html);
            },
            itemRenderer: renderCard,
        });

        editor.ui.registry.addButton("mentionUser", {
            text: "@",
            onAction: function () {
                editor.execCommand("mceInsertContent", false, "@");
            },
        });
        editor.ui.registry.addButton("mentionIssue", {
            text: "#",
            onAction: function () {
                editor.execCommand("mceInsertContent", false, "#");
            },
        });
    });
})();
