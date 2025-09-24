window.tinyEditor = function tinyEditor(opts) {
    const st = {
        id: opts.elId,
        height: Number(opts.height ?? 320),
        baseUrl: String(opts.baseUrl ?? "/vendor/tinymce"),
        toolbar: String(
            opts.toolbar ??
                "undo redo | bold italic link | bullist numlist | code",
        ),
        pluginsStr: String(opts.plugins ?? "link lists code"),
        wireModel: opts.wireModel ?? null,
        contentCss: Array.isArray(opts.contentCss) ? opts.contentCss : [],
        externalPlugins: opts.externalPlugins ?? {},
        suffix: ".min",
        initial: opts.initial ?? null,
    };

    const sleep = (ms) => new Promise((r) => setTimeout(r, ms));
    const load = (src) =>
        new Promise((res, rej) => {
            const { href } = new URL(src, location.origin);
            if ([...document.scripts].some((s) => s.src === href)) {
                return res();
            }
            const s = document.createElement("script");
            s.src = src;
            s.onload = res;
            s.onerror = () => rej(new Error(src));
            document.head.appendChild(s);
        });

    const ensureCore = async () => {
        try {
            await load(`${st.baseUrl}/tinymce.min.js`);
            st.suffix = ".min";
        } catch {
            await load(`${st.baseUrl}/tinymce.js`);
            st.suffix = "";
        }
        // give the UMD a tick to attach window.tinymce
        if (!window.tinymce) {
            await sleep(0);
        }
    };

    // Shim: accept legacy { ch: '@' } by converting to { trigger: '@' } before plugins run
    const patchAutocompleter = () => {
        const reg = window.tinymce?.ui?.registry;
        if (!reg || reg.__tinyForgePatched) {
            return;
        }
        const orig = reg.addAutocompleter.bind(reg);
        reg.addAutocompleter = (id, spec) => {
            if (spec && !("trigger" in spec) && "ch" in spec) {
                spec.trigger = spec.ch;
            }
            return orig(id, spec);
        };
        reg.__tinyForgePatched = true;
    };

    const ensureExternal = async () => {
        for (const [pid, url] of Object.entries(st.externalPlugins)) {
            if (!url) {
                continue;
            }
            if (window.tinymce?.PluginManager?.lookup?.[pid]) {
                continue;
            }
            await load(url);
        }
    };

    const normalizePlugins = () => {
        const want = new Set(st.pluginsStr.split(/\s+/).filter(Boolean));
        Object.keys(st.externalPlugins).forEach((id) => want.add(id));
        return [...want].join(" ");
    };

    const debounce = (fn, delay = 200) => {
        let t;
        return (...args) => {
            clearTimeout(t);
            t = setTimeout(() => fn(...args), delay);
        };
    };

    return {
        async init() {
            const node = document.getElementById(st.id);
            if (!node) {
                return;
            }

            await ensureCore();
            if (!window.tinymce) {
                console.warn("TinyMCE not found at", st.baseUrl);
                return;
            }

            patchAutocompleter();
            await ensureExternal();

            try {
                tinymce.remove("#" + st.id);
            } catch {}

            tinymce.init({
                selector: `#${st.id}`,
                base_url: st.baseUrl,
                suffix: st.suffix,
                license_key: "gpl",
                menubar: false,
                branding: false,
                height: st.height,
                plugins: normalizePlugins(),
                toolbar: st.toolbar,
                content_css: st.contentCss,
                extended_valid_elements: "li[data-ai-id|data-ai-checked]",
                setup: (ed) => {
                    ed.on("init", () => {
                        if (st.initial != null) {
                            ed.setContent(st.initial);
                        }
                    });

                    const pushNow = () => {
                        if (!st.wireModel) {
                            return;
                        }
                        const lw = this?.$wire;
                        if (lw?.$set) {
                            lw.$set(st.wireModel, ed.getContent());
                        }
                    };
                    const push = debounce(pushNow, 200);

                    ed.on("change input undo redo", push);
                    ed.on("blur", pushNow);

                    const form = node.closest("form");
                    if (form) {
                        form.addEventListener("submit", pushNow, {
                            capture: true,
                        });
                    }
                },
            });
        },
        destroy() {
            try {
                if (window.tinymce) {
                    tinymce.remove("#" + st.id);
                }
            } catch {}
        },
    };
};
