// resources/js/editor/tinymce-init.js
// Vite module that exposes a reusable Alpine helper to initialize TinyMCE.
// Usage in Blade: x-data="tinyEditor({ elId: 'my-textarea', ... })"

(function () {
  /**
     * Safely wait for window.tinymce to exist before init.
     */
  const waitForTiny = (retries = 60, delay = 50) =>
    new Promise((resolve, reject) => {
      const tick = () => {
        if (
          window.tinymce &&
                    typeof window.tinymce.init === 'function'
        ) {
          return resolve(window.tinymce)
        }
        if (retries-- <= 0) {
          return reject(new Error('TinyMCE not loaded'))
        }
        setTimeout(tick, delay)
      }
      tick()
    })

  /**
     * Try to find the nearest Livewire component and set a property.
     * Works with Livewire v3 (wire:id on the root).
     */
  const pushToLivewire = (textareaEl, propName, html) => {
    if (!propName || !window.Livewire) {
      return
    }

    // Find nearest Livewire root
    const root = textareaEl.closest('[wire\\:id]')
    if (!root) {
      return
    }

    const compId = root.getAttribute('wire:id')
    const comp = window.Livewire.find(compId)
    if (!comp) {
      return
    }

    comp.set(propName, html)
  }

  /**
     * Alpine helper factory. Returns an object with init/destroy for x-data.
     * Pass:
     *  - elId: textarea id (required)
     *  - name: form name (optional, for vanilla forms)
     *  - height: editor height (default 320)
     *  - baseUrl: TinyMCE base_url (default '/vendor/tinymce')
     *  - externalPlugins: { action-items: '/build/assets/....js', 'mentions-lite': '/build/assets/....js' }
     *  - contentCss: ['/build/assets/action-items-....css']  (array)
     *  - plugins: built-in plugin string (default 'link lists code')
     *  - toolbar: toolbar string
     *  - wireModel: Livewire property name to keep in sync (e.g. 'description')
     */
  // sourcery skip: avoid-function-declarations-in-blocks
  function tinyEditor (cfg) {
    let editor = null
    const defaults = {
      height: 320,
      baseUrl: '/vendor/tinymce',
      externalPlugins: {},
      contentCss: [],
      licenseKey: 'gpl',
      plugins: 'link lists code',
      toolbar:
                'undo redo | bold italic link | bullist numlist | checklist mentionUser mentionIssue | code'
    }

    const options = Object.assign({}, defaults, cfg || {})
    const selector = `#${options.elId}`
    let syncTimer = null

    const syncBack = () => {
      const el = document.getElementById(options.elId)
      const html = editor.getContent()

      // keep the underlying textarea in sync (for normal form posts)
      if (el) {
        el.value = html
        el.dispatchEvent(new Event('change', { bubbles: true }))
        el.dispatchEvent(new Event('input', { bubbles: true }))
      }

      // Livewire (deferred)
      if (options.wireModel && window.Livewire) {
        const root = el.closest('[wire\\:id]')
        const comp = root
          ? window.Livewire.find(root.getAttribute('wire:id'))
          : null
        comp?.set(options.wireModel, html)
      }
    }

    // Debounce on typing; immediate on SetContent/undo/redo
    editor.on('input keyup', () => {
      clearTimeout(syncTimer)
      syncTimer = setTimeout(syncBack, 400) // << adjust debounce
    });
    ['change', 'undo', 'redo', 'SetContent'].forEach((evt) => {
      editor.on(evt, () => {
        clearTimeout(syncTimer)
        syncBack()
      })
    })

    return {
      async init () {
        await waitForTiny()

        // Avoid double init
        if (window.tinymce.get(options.elId)) {
          window.tinymce.remove(selector)
        }

        await window.tinymce.init({
          selector,
          base_url: options.baseUrl,
          height: options.height,
          menubar: false,
          license_key: options.licenseKey,
          // Built-ins & custom buttons registered by your external plugins
          plugins: options.plugins,
          toolbar: options.toolbar,
          external_plugins: options.externalPlugins,
          content_css: options.contentCss,

          // Important in modals and Livewire re-renders
          autofocus: false,

          setup: (ed) => {
            editor = ed

            // Keep data in sync
            const syncEvents = [
              'change',
              'input',
              'undo',
              'redo',
              'keyup',
              'SetContent'
            ]
            syncEvents.forEach((evt) => ed.on(evt, syncBack))

            // Clean up handle
            ed.on('remove', () => {
              editor = null
            })
          }
        })
      },

      destroy () {
        try {
          window.tinymce?.remove?.(selector)
        } catch (_) {}
        editor = null
      }
    }
  }

  /**
     * Optional auto-mount helpers (if you prefer not to use Alpine):
     * Add data-tiny on <textarea> and call TinyForge.initAll();
     * Provide URLs via data attributes: data-base-url, data-ai, data-mentions, data-ai-css, etc.
     */
  // sourcery skip: avoid-function-declarations-in-blocks
  function initAll (selector = 'textarea[data-tiny]') {
    document.querySelectorAll(selector).forEach(async (el) => {
      if (el.dataset.tinyMounted === '1') {
        return
      }

      const externalPlugins = {}
      if (el.dataset.ai) {
        externalPlugins['action-items'] = el.dataset.ai
      }
      if (el.dataset.mentions) {
        externalPlugins['mentions-lite'] = el.dataset.mentions
      }

      const contentCss = []
      if (el.dataset.aiCss) {
        contentCss.push(el.dataset.aiCss)
      }

      const inst = tinyEditor({
        elId: el.id,
        baseUrl: el.dataset.baseUrl || '/vendor/tinymce',
        externalPlugins,
        contentCss,
        plugins: el.dataset.plugins || 'link lists code',
        toolbar:
                    el.dataset.toolbar ||
                    'undo redo | bold italic link | bullist numlist | checklist mentionUser mentionIssue | code',
        wireModel: el.dataset.wireModel || null,
        height: Number(el.dataset.height || 320),
        licenseKey: el.dataset.licenseKey || 'gpl'
      })

      await inst.init()
      el.dataset.tinyMounted = '1'
      el._tinyInstance = inst
    })
  }

  function destroyAll (selector = 'textarea[data-tiny]') {
    document.querySelectorAll(selector).forEach((el) => {
      el._tinyInstance?.destroy?.()
      el.removeAttribute('data-tiny-mounted')
      delete el._tinyInstance
    })
  }

  // Expose globally for Alpine and/or manual control
  window.tinyEditor = tinyEditor
  window.TinyForge = {
    tinyEditor,
    initAll,
    destroyAll
  }

  // Optional: re-init patterns for SPA-ish flows
  document.addEventListener('livewire:navigated', () => {
    // On Livewire page nav, you can choose to re-mount if using data-tiny
    // TinyForge.destroyAll(); TinyForge.initAll();
  })
})()
