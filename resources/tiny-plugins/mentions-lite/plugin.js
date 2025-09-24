(function () {
    tinymce.PluginManager.add("mentions-lite", function (editor) {
        // sourcery skip: avoid-function-declarations-in-blocks
        function termOf(pattern) {
            if (typeof pattern === "string") {
                return pattern;
            }
            if (pattern && typeof pattern === "object") {
                return pattern.term ?? pattern.query ?? pattern.text ?? "";
            }
            return "";
        }

        function buildUrl(path, params) {
            const u = new URL(path, location.origin);
            params = params || {};
            for (const k in params) {
                const v = params[k];
                if (v !== undefined && v !== null && String(v) !== "") {
                    u.searchParams.set(k, String(v));
                }
            }
            return u.toString();
        }

        // sourcery skip: avoid-function-declarations-in-blocks
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

        // sourcery skip: avoid-function-declarations-in-blocks
        function toObj(v) {
            try {
                return typeof v === "string" ? JSON.parse(v) : v || {};
            } catch {
                return {};
            }
        }

        // sourcery skip: avoid-function-declarations-in-blocks
        function isApi(a) {
            return (
                a &&
                typeof a === "object" &&
                (typeof a.hide === "function" ||
                    typeof a.replace === "function")
            );
        }
        // sourcery skip: avoid-function-declarations-in-blocks
        function isRange(a) {
            return (
                a &&
                typeof a === "object" &&
                (a.startContainer || a.commonAncestorContainer || a.nativeRange)
            );
        }

        // sourcery skip: avoid-function-declarations-in-blocks
        function resolveActionArgs(a, b, c) {
            let api = null;
            let rng = null;
            let value = null;
            const args = [a, b, c];
            for (let i = 0; i < 3; i++) {
                const x = args[i];
                if (typeof x === "string" && value === null) {
                    value = x;
                    continue;
                }
                if (x && typeof x === "object") {
                    if (isApi(x)) {
                        api = x;
                        continue;
                    }
                    if (isRange(x)) {
                        rng = x;
                        continue;
                    }
                    if (typeof x.value === "string" && value === null) {
                        value = x.value;
                        continue;
                    }
                }
            }
            return { api, rng, value };
        }

        // sourcery skip: avoid-function-declarations-in-blocks
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
            if (api && typeof api.hide === "function") {
                api.hide();
            }
        }

        // Build safe HTML for insertion without calling escape helpers
        // sourcery skip: avoid-function-declarations-in-blocks
        function buildMentionHTML(v, prefix, type) {
            const doc = editor.getDoc();
            const a = doc.createElement("a");
            a.setAttribute("href", v.url || "#");
            a.setAttribute("data-mention-type", type);
            if (v.id != null) {
                a.setAttribute("data-mention-id", String(v.id));
            }
            a.appendChild(doc.createTextNode(prefix + (v.label || "")));
            return a.outerHTML + "&nbsp;";
        }

        // For the dropdown UI we still escape (using Tiny’s function if available)
        // sourcery skip: avoid-function-declarations-in-blocks
        function escapeHtmlSafe(s) {
            const str = s == null ? "" : String(s);
            const fn =
                tinymce &&
                tinymce.util &&
                tinymce.util.Tools &&
                tinymce.util.Tools.escapeHtml;
            if (typeof fn === "function") {
                return fn(str);
            }
            // tiny fallback
            return str.replace(/[&<>"']/g, function (m) {
                return {
                    "&": "&amp;",
                    "<": "&lt;",
                    ">": "&gt;",
                    '"': "&quot;",
                    "'": "&#39;",
                }[m];
            });
        }

        // sourcery skip: avoid-function-declarations-in-blocks
        function renderCard(_api, data) {
            const v = toObj(data && data.value);
            const el = document.createElement("div");
            el.className = "mention-item";
            el.innerHTML =
                "<div><strong>" +
                escapeHtmlSafe(v.label || (data && data.text) || "") +
                "</strong><small>" +
                escapeHtmlSafe(v.sublabel || "") +
                "</small></div>";
            return el; // Tiny wires click; we don’t manually attach handlers here.
        }

        // sourcery skip: avoid-using-var
        const projectId =
            (editor.getElement() &&
                editor.getElement().dataset &&
                editor.getElement().dataset.projectId) ||
            "";

        // @user
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
                const out = [];
                for (let i = 0; i < list.length; i++) {
                    const u = list[i];
                    out.push({
                        type: "menuitem",
                        text: u.label,
                        value: JSON.stringify(u),
                    }); // value MUST be a string
                }
                return out;
            },
            onAction: function (api, rng, value) {
                const v = toObj(value);
                insertViaApiOrEditor(
                    api,
                    rng,
                    buildMentionHTML(v, "@", "user"),
                );
            },
            itemRenderer: renderCard,
        });

        // #issue
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
            onAction: function (api, rng, value) {
                const v = toObj(value);
                insertViaApiOrEditor(
                    api,
                    rng,
                    buildMentionHTML(v, "#", "issue"),
                );
            },
            itemRenderer: renderCard,
        });

        // toolbar
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
