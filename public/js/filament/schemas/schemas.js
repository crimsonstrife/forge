(() => {
  const d = () => ({
    isSticky: !1,
    init () {
      this.evaluatePageScrollPosition()
    },
    evaluatePageScrollPosition () {
      const n = this.$el.getBoundingClientRect()
      const e = n.top > window.innerHeight
      const i =
                n.top < window.innerHeight && n.bottom > window.innerHeight
      this.isSticky = e || i
    }
  })
  const m = function (n, e, i) {
    let t = n
    if ((e.startsWith('/') && ((i = !0), (e = e.slice(1))), i)) {
      return e
    }
    for (; e.startsWith('../');) {
      ((t = t.includes('.') ? t.slice(0, t.lastIndexOf('.')) : null),
      (e = e.slice(3)))
    }
    return ['', null, void 0].includes(t)
      ? e
      : ['', null, void 0].includes(e)
          ? t
          : `${t}.${e}`
  }
  const u = (n) => {
    const e = Alpine.findClosest(n, (i) => i.__livewire)
    if (!e) throw 'Could not find Livewire component in DOM tree.'
    return e.__livewire
  }
  document.addEventListener('alpine:init', () => {
    (window.Alpine.data('filamentSchema', ({ livewireId: n }) => ({
      handleFormValidationError (e) {
        e.detail.livewireId === n &&
                    this.$nextTick(() => {
                      const i = this.$el.querySelector(
                        '[data-validation-error]'
                      )
                      if (!i) return
                      let t = i
                      for (; t;) {
                        (t.dispatchEvent(new CustomEvent('expand')),
                        (t = t.parentNode))
                      }
                      setTimeout(
                        () =>
                          i
                            .closest('[data-field-wrapper]')
                            .scrollIntoView({
                              behavior: 'smooth',
                              block: 'start',
                              inline: 'start'
                            }),
                        200
                      )
                    })
      }
    })),
    window.Alpine.data(
      'filamentSchemaComponent',
      ({ path: n, containerPath: e, isLive: i, $wire: t }) => ({
        $statePath: n,
        $get: (r, l) => t.$get(m(e, r, l)),
        $set: (r, l, a, o = null) => (
          o ?? (o = i),
          t.$set(m(e, r, a), l, o)
        ),
        get $state () {
          return t.$get(n)
        }
      })
    ),
    window.Alpine.data('filamentActionsSchemaComponent', d),
    Livewire.hook(
      'commit',
      ({
        component: n,
        commit: e,
        respond: i,
        succeed: t,
        fail: r
      }) => {
        t(({ snapshot: l, effects: a }) => {
          a.dispatches?.forEach((o) => {
            if (!o.params?.awaitSchemaComponent) return
            const s = Array.from(
              n.el.querySelectorAll(
                                    `[wire\\:partial="schema-component::${o.params.awaitSchemaComponent}"]`
              )
            ).filter((c) => u(c) === n)
            if (s.length !== 1) {
              if (s.length > 1) {
                throw `Multiple schema components found with key [${o.params.awaitSchemaComponent}].`
              }
              window.addEventListener(
                                    `schema-component-${n.id}-${o.params.awaitSchemaComponent}-loaded`,
                                    () => {
                                      window.dispatchEvent(
                                        new CustomEvent(o.name, {
                                          detail: o.params
                                        })
                                      )
                                    },
                                    { once: !0 }
              )
            }
          })
        })
      }
    ))
  })
})()
