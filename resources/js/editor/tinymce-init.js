window.tinyEditor = function tinyEditor(opts) {
    const st = {
        id: opts.elId,
        height: Number(opts.height ?? 320),
        baseUrl: String(opts.baseUrl ?? '/vendor/tinymce'),
        toolbar: String(opts.toolbar ?? 'undo redo | bold italic link | bullist numlist | code'),
        pluginsStr: String(opts.plugins ?? 'link lists code'),
        wireModel: opts.wireModel ?? null,
        contentCss: Array.isArray(opts.contentCss) ? opts.contentCss : [],
        externalPlugins: opts.externalPlugins ?? {}, // { 'action-items': '/js/tinymce-actionitems.js', ... }
        suffix: '.min', // will switch to '' if we load non-min core
        initial: opts.initial ?? null,
    };

    const load = (src) => new Promise((res, rej) => {
        if ([...document.scripts].some(s => s.src === new URL(src, location.origin).href)) {
          return res();
        }
        const s = document.createElement('script'); s.src = src; s.onload = res; s.onerror = () => rej(new Error(src)); document.head.appendChild(s);
    });

    const ensureCore = async () => {
        try { await load(`${st.baseUrl}/tinymce.min.js`); st.suffix = '.min'; return; } catch {}
        await load(`${st.baseUrl}/tinymce.js`); st.suffix = ''; // fallback
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
        Object.keys(st.externalPlugins).forEach(id => want.add(id));
        return [...want].join(' ');
    };

    return {
        async init() {
            const node = document.getElementById(st.id);
            if (!node) {
              return;
            }

            await ensureCore();
            if (!window.tinymce) { console.error('TinyMCE not found at', st.baseUrl); return; }
            await ensureExternal();

            try { tinymce.remove('#' + st.id); } catch {}

            tinymce.init({
                selector: `#${st.id}`,
                base_url: st.baseUrl,
                suffix: st.suffix,
                license_key: 'gpl',
                menubar: false,
                branding: false,
                height: st.height,
                plugins: normalizePlugins(),
                toolbar: st.toolbar,
                content_css: st.contentCss,
                extended_valid_elements: 'li[data-ai-id|data-ai-checked]',
                setup(ed) {
                    ed.on('init', () => { if (st.initial) {
                                            ed.setContent(st.initial);
                                          } });
                    const push = () => { if (st.wireModel) {
                                           $wire.$set(st.wireModel, ed.getContent());
                                         } };
                    ed.on('change input undo redo keyup blur', push);
                },
            });
        },
        destroy() { try { if (window.tinymce) {
                            tinymce.remove('#' + st.id);
                          } } catch {} },
    };
};
