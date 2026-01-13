(() => {
  const re = Object.create
  const x = Object.defineProperty
  const ne = Object.getPrototypeOf
  const ie = Object.prototype.hasOwnProperty
  const ae = Object.getOwnPropertyNames
  const se = Object.getOwnPropertyDescriptor
  const oe = (t) => x(t, '__esModule', { value: !0 })
  const le = (t, n) => () => (
    n || ((n = { exports: {} }), t(n.exports, n)),
    n.exports
  )
  const fe = (t, n, i) => {
    if ((n && typeof n === 'object') || typeof n === 'function') {
      for (const l of ae(n)) {
        !ie.call(t, l) &&
                    l !== 'default' &&
                    x(t, l, {
                      get: () => n[l],
                      enumerable: !(i = se(n, l)) || i.enumerable
                    })
      }
    }
    return t
  }
  const ue = (t) =>
    fe(
      oe(
        x(
          t != null ? re(ne(t)) : {},
          'default',
          t && t.__esModule && 'default' in t
            ? { get: () => t.default, enumerable: !0 }
            : { value: t, enumerable: !0 }
        )
      ),
      t
    )
  const pe = le((t, n) => {
    (function (i, l, y) {
      if (!i) return
      for (
        var c = {
            8: 'backspace',
            9: 'tab',
            13: 'enter',
            16: 'shift',
            17: 'ctrl',
            18: 'alt',
            20: 'capslock',
            27: 'esc',
            32: 'space',
            33: 'pageup',
            34: 'pagedown',
            35: 'end',
            36: 'home',
            37: 'left',
            38: 'up',
            39: 'right',
            40: 'down',
            45: 'ins',
            46: 'del',
            91: 'meta',
            93: 'meta',
            224: 'meta'
          },
          _ = {
            106: '*',
            107: '+',
            109: '-',
            110: '.',
            111: '/',
            186: ';',
            187: '=',
            188: ',',
            189: '-',
            190: '.',
            191: '/',
            192: '`',
            219: '[',
            220: '\\',
            221: ']',
            222: "'"
          },
          g = {
            '~': '`',
            '!': '1',
            '@': '2',
            '#': '3',
            $: '4',
            '%': '5',
            '^': '6',
            '&': '7',
            '*': '8',
            '(': '9',
            ')': '0',
            _: '-',
            '+': '=',
            ':': ';',
            '"': "'",
            '<': ',',
            '>': '.',
            '?': '/',
            '|': '\\'
          },
          G = {
            option: 'alt',
            command: 'meta',
            return: 'enter',
            escape: 'esc',
            plus: '+',
            mod: /Mac|iPod|iPhone|iPad/.test(navigator.platform)
              ? 'meta'
              : 'ctrl'
          },
          A,
          b = 1;
        b < 20;
        ++b
      ) {
        c[111 + b] = 'f' + b
      }
      for (b = 0; b <= 9; ++b) c[b + 96] = b.toString()
      function P (e, r, s) {
        if (e.addEventListener) {
          e.addEventListener(r, s, !1)
          return
        }
        e.attachEvent('on' + r, s)
      }
      function T (e) {
        if (e.type == 'keypress') {
          let r = String.fromCharCode(e.which)
          return (e.shiftKey || (r = r.toLowerCase()), r)
        }
        return c[e.which]
          ? c[e.which]
          : _[e.which]
            ? _[e.which]
            : String.fromCharCode(e.which).toLowerCase()
      }
      function J (e, r) {
        return e.sort().join(',') === r.sort().join(',')
      }
      function H (e) {
        const r = []
        return (
          e.shiftKey && r.push('shift'),
          e.altKey && r.push('alt'),
          e.ctrlKey && r.push('ctrl'),
          e.metaKey && r.push('meta'),
          r
        )
      }
      function F (e) {
        if (e.preventDefault) {
          e.preventDefault()
          return
        }
        e.returnValue = !1
      }
      function B (e) {
        if (e.stopPropagation) {
          e.stopPropagation()
          return
        }
        e.cancelBubble = !0
      }
      function C (e) {
        return e == 'shift' || e == 'ctrl' || e == 'alt' || e == 'meta'
      }
      function X () {
        if (!A) {
          A = {}
          for (const e in c) {
            (e > 95 && e < 112) ||
                            (c.hasOwnProperty(e) && (A[c[e]] = e))
          }
        }
        return A
      }
      function Y (e, r, s) {
        return (
          s || (s = X()[e] ? 'keydown' : 'keypress'),
          s == 'keypress' && r.length && (s = 'keydown'),
          s
        )
      }
      function Q (e) {
        return e === '+'
          ? ['+']
          : ((e = e.replace(/\+{2}/g, '+plus')), e.split('+'))
      }
      function R (e, r) {
        let s
        let h
        let k
        const S = []
        for (s = Q(e), k = 0; k < s.length; ++k) {
          ((h = s[k]),
          G[h] && (h = G[h]),
          r &&
                            r != 'keypress' &&
                            g[h] &&
                            ((h = g[h]), S.push('shift')),
          C(h) && S.push(h))
        }
        return ((r = Y(h, S, r)), { key: h, modifiers: S, action: r })
      }
      function I (e, r) {
        return e === null || e === l
          ? !1
          : e === r
            ? !0
            : I(e.parentNode, r)
      }
      function v (e) {
        const r = this
        if (((e = e || l), !(r instanceof v))) return new v(e);
        ((r.target = e), (r._callbacks = {}), (r._directMap = {}))
        const s = {}
        let h
        let k = !1
        let S = !1
        let D = !1
        function E (a) {
          a = a || {}
          let f = !1
          let p
          for (p in s) {
            if (a[p]) {
              f = !0
              continue
            }
            s[p] = 0
          }
          f || (D = !1)
        }
        function z (a, f, p, o, d, m) {
          let u
          let w
          const M = []
          const O = p.type
          if (!r._callbacks[a]) return []
          for (
            O == 'keyup' && C(a) && (f = [a]), u = 0;
            u < r._callbacks[a].length;
            ++u
          ) {
            if (
              ((w = r._callbacks[a][u]),
              !(!o && w.seq && s[w.seq] != w.level) &&
                                O == w.action &&
                                ((O == 'keypress' &&
                                    !p.metaKey &&
                                    !p.ctrlKey) ||
                                    J(f, w.modifiers)))
            ) {
              const ee = !o && w.combo == d
              const te = o && w.seq == o && w.level == m;
              ((ee || te) && r._callbacks[a].splice(u, 1),
              M.push(w))
            }
          }
          return M
        }
        function L (a, f, p, o) {
          r.stopCallback(f, f.target || f.srcElement, p, o) ||
                        (a(f, p) === !1 && (F(f), B(f)))
        }
        r._handleKey = function (a, f, p) {
          const o = z(a, f, p)
          let d
          const m = {}
          let u = 0
          let w = !1
          for (d = 0; d < o.length; ++d) {
            o[d].seq && (u = Math.max(u, o[d].level))
          }
          for (d = 0; d < o.length; ++d) {
            if (o[d].seq) {
              if (o[d].level != u) continue;
              ((w = !0),
              (m[o[d].seq] = 1),
              L(o[d].callback, p, o[d].combo, o[d].seq))
              continue
            }
            w || L(o[d].callback, p, o[d].combo)
          }
          const M = p.type == 'keypress' && S;
          (p.type == D && !C(a) && !M && E(m),
          (S = w && p.type == 'keydown'))
        }
        function K (a) {
          typeof a.which !== 'number' && (a.which = a.keyCode)
          const f = T(a)
          if (f) {
            if (a.type == 'keyup' && k === f) {
              k = !1
              return
            }
            r.handleKey(f, H(a), a)
          }
        }
        function Z () {
          (clearTimeout(h), (h = setTimeout(E, 1e3)))
        }
        function $ (a, f, p, o) {
          s[a] = 0
          function d (O) {
            return function () {
              ((D = O), ++s[a], Z())
            }
          }
          function m (O) {
            (L(p, O, a),
            o !== 'keyup' && (k = T(O)),
            setTimeout(E, 10))
          }
          for (let u = 0; u < f.length; ++u) {
            const w = u + 1 === f.length
            const M = w ? m : d(o || R(f[u + 1]).action)
            N(f[u], M, o, a, u)
          }
        }
        function N (a, f, p, o, d) {
          ((r._directMap[a + ':' + p] = f),
          (a = a.replace(/\s+/g, ' ')))
          const m = a.split(' ')
          let u
          if (m.length > 1) {
            $(a, m, f, p)
            return
          }
          ((u = R(a, p)),
          (r._callbacks[u.key] = r._callbacks[u.key] || []),
          z(u.key, u.modifiers, { type: u.action }, o, a, d),
          r._callbacks[u.key][o ? 'unshift' : 'push']({
            callback: f,
            modifiers: u.modifiers,
            action: u.action,
            seq: o,
            level: d,
            combo: a
          }))
        }
        ((r._bindMultiple = function (a, f, p) {
          for (let o = 0; o < a.length; ++o) N(a[o], f, p)
        }),
        P(e, 'keypress', K),
        P(e, 'keydown', K),
        P(e, 'keyup', K))
      }
      ((v.prototype.bind = function (e, r, s) {
        const h = this
        return (
          (e = e instanceof Array ? e : [e]),
          h._bindMultiple.call(h, e, r, s),
          h
        )
      }),
      (v.prototype.unbind = function (e, r) {
        const s = this
        return s.bind.call(s, e, function () {}, r)
      }),
      (v.prototype.trigger = function (e, r) {
        const s = this
        return (
          s._directMap[e + ':' + r] &&
                            s._directMap[e + ':' + r]({}, e),
          s
        )
      }),
      (v.prototype.reset = function () {
        const e = this
        return ((e._callbacks = {}), (e._directMap = {}), e)
      }),
      (v.prototype.stopCallback = function (e, r) {
        const s = this
        if (
          (' ' + r.className + ' ').indexOf(' mousetrap ') > -1 ||
                        I(r, s.target)
        ) {
          return !1
        }
        if (
          'composedPath' in e &&
                        typeof e.composedPath === 'function'
        ) {
          const h = e.composedPath()[0]
          h !== e.target && (r = h)
        }
        return (
          r.tagName == 'INPUT' ||
                        r.tagName == 'SELECT' ||
                        r.tagName == 'TEXTAREA' ||
                        r.isContentEditable
        )
      }),
      (v.prototype.handleKey = function () {
        const e = this
        return e._handleKey.apply(e, arguments)
      }),
      (v.addKeycodes = function (e) {
        for (const r in e) e.hasOwnProperty(r) && (c[r] = e[r])
        A = null
      }),
      (v.init = function () {
        const e = v(l)
        for (const r in e) {
          r.charAt(0) !== '_' &&
                            (v[r] = (function (s) {
                              return function () {
                                return e[s].apply(e, arguments)
                              }
                            })(r))
        }
      }),
      v.init(),
      (i.Mousetrap = v),
      typeof n < 'u' && n.exports && (n.exports = v),
      typeof define === 'function' &&
                    define.amd &&
                    define(function () {
                      return v
                    }))
    })(
      typeof window < 'u' ? window : null,
      typeof window < 'u' ? document : null
    )
  })
  const q = ue(pe());
  (function (t) {
    if (t) {
      const n = {}
      const i = t.prototype.stopCallback;
      ((t.prototype.stopCallback = function (l, y, c, _) {
        const g = this
        return g.paused ? !0 : n[c] || n[_] ? !1 : i.call(g, l, y, c)
      }),
      (t.prototype.bindGlobal = function (l, y, c) {
        const _ = this
        if ((_.bind(l, y, c), l instanceof Array)) {
          for (let g = 0; g < l.length; g++) n[l[g]] = !0
          return
        }
        n[l] = !0
      }),
      t.init())
    }
  })(typeof Mousetrap < 'u' ? Mousetrap : void 0)
  const W = () =>
    Array.from(document.querySelectorAll('[aria-modal="true"]')).find(
      (t) => window.getComputedStyle(t).display !== 'none'
    )
  const ce = (t) => {
    t.directive(
      'mousetrap',
      (n, { modifiers: i, expression: l }, { evaluate: y }) => {
        const c = () => (l ? y(l) : n.click());
        ((i = i.map((_) =>
          _.replace(/--/g, ' ')
            .replace(/-/g, '+')
            .replace(/\bslash\b/g, '/')
        )),
        i.includes('global') &&
                        ((i = i.filter((_) => _ !== 'global')),
                        q.default.bindGlobal(i, (_) => {
                          const g = W();
                          (g && !g.contains(n)) || (_.preventDefault(), c())
                        })),
        q.default.bind(i, (_) => {
          const g = W();
          (g && !g.contains(n)) || (_.preventDefault(), c())
        }),
        document.addEventListener(
          'livewire:navigating',
          () => {
            q.default.unbind(i)
          },
          { once: !0 }
        ))
      }
    )
  }
  const j = ce
  const V = () => ({
    isOpen: window.Alpine.$persist(!0).as('isOpen'),
    isOpenDesktop: window.Alpine.$persist(!0).as('isOpenDesktop'),
    collapsedGroups: window.Alpine.$persist(null).as('collapsedGroups'),
    init () {
      ((this.resizeObserver = null),
      this.setUpResizeObserver(),
      document.addEventListener('livewire:navigated', () => {
        this.setUpResizeObserver()
      }))
    },
    setUpResizeObserver () {
      this.resizeObserver && this.resizeObserver.disconnect()
      let t = window.innerWidth;
      ((this.resizeObserver = new ResizeObserver(() => {
        const n = window.innerWidth
        const i = t >= 1024
        const l = n < 1024
        const y = n >= 1024;
        (i && l
          ? ((this.isOpenDesktop = this.isOpen),
            this.isOpen && this.close())
          : !i && y && (this.isOpen = this.isOpenDesktop),
        (t = n))
      })),
      this.resizeObserver.observe(document.body),
      window.innerWidth < 1024
        ? this.isOpen && ((this.isOpenDesktop = !0), this.close())
        : (this.isOpenDesktop = this.isOpen))
    },
    groupIsCollapsed (t) {
      return this.collapsedGroups.includes(t)
    },
    collapseGroup (t) {
      this.collapsedGroups.includes(t) ||
                (this.collapsedGroups = this.collapsedGroups.concat(t))
    },
    toggleCollapsedGroup (t) {
      this.collapsedGroups = this.collapsedGroups.includes(t)
        ? this.collapsedGroups.filter((n) => n !== t)
        : this.collapsedGroups.concat(t)
    },
    close () {
      ((this.isOpen = !1),
      window.innerWidth >= 1024 && (this.isOpenDesktop = !1))
    },
    open () {
      ((this.isOpen = !0),
      window.innerWidth >= 1024 && (this.isOpenDesktop = !0))
    }
  })
  document.addEventListener('alpine:init', () => {
    const t =
            localStorage.getItem('theme') ??
            getComputedStyle(document.documentElement).getPropertyValue(
              '--default-theme-mode'
            );
    (window.Alpine.store(
      'theme',
      t === 'dark' ||
                (t === 'system' &&
                    window.matchMedia('(prefers-color-scheme: dark)').matches)
        ? 'dark'
        : 'light'
    ),
    window.addEventListener('theme-changed', (n) => {
      let i = n.detail;
      (localStorage.setItem('theme', i),
      i === 'system' &&
                        (i = window.matchMedia('(prefers-color-scheme: dark)')
                          .matches
                          ? 'dark'
                          : 'light'),
      window.Alpine.store('theme', i))
    }),
    window
      .matchMedia('(prefers-color-scheme: dark)')
      .addEventListener('change', (n) => {
        localStorage.getItem('theme') === 'system' &&
                        window.Alpine.store(
                          'theme',
                          n.matches ? 'dark' : 'light'
                        )
      }),
    window.Alpine.effect(() => {
      window.Alpine.store('theme') === 'dark'
        ? document.documentElement.classList.add('dark')
        : document.documentElement.classList.remove('dark')
    }))
  })
  const U = window.history.replaceState
  const de = window.history.pushState
  window.history.replaceState = function (t, n, i) {
    t?.url instanceof URL && (t.url = t.url.toString())
    const l = i || t?.url || window.location.href
    const y = window.location.href
    if (l !== y) {
      U.call(window.history, t, n, i)
      return
    }
    try {
      const c = window.history.state
      JSON.stringify(t) !== JSON.stringify(c) &&
                U.call(window.history, t, n, i)
    } catch {
      U.call(window.history, t, n, i)
    }
  }
  window.history.pushState = function (t, n, i) {
    (t?.url instanceof URL && (t.url = t.url.toString()),
    de.call(window.history, t, n, i))
  }
  document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
      let t = document.querySelector(
        '.fi-main-sidebar .fi-sidebar-item.fi-active'
      )
      if (
        ((!t || t.offsetParent === null) &&
                    (t = document.querySelector(
                      '.fi-main-sidebar .fi-sidebar-group.fi-active'
                    )),
        !t || t.offsetParent === null)
      ) {
        return
      }
      const n = document.querySelector(
        '.fi-main-sidebar .fi-sidebar-nav'
      )
      n && n.scrollTo(0, t.offsetTop - window.innerHeight / 2)
    }, 10)
  })
  window.setUpUnsavedDataChangesAlert = ({
    body: t,
    livewireComponent: n,
    $wire: i
  }) => {
    window.addEventListener('beforeunload', (l) => {
      window.jsMd5(JSON.stringify(i.data).replace(/\\/g, '')) ===
                i.savedDataHash ||
                i?.__instance?.effects?.redirect ||
                (l.preventDefault(), (l.returnValue = !0))
    })
  }
  window.setUpSpaModeUnsavedDataChangesAlert = ({
    body: t,
    resolveLivewireComponentUsing: n,
    $wire: i
  }) => {
    const l = () =>
      i?.__instance?.effects?.redirect
        ? !1
        : window.jsMd5(JSON.stringify(i.data).replace(/\\/g, '')) !==
                  i.savedDataHash
    const y = () => confirm(t);
    (document.addEventListener('livewire:navigate', (c) => {
      if (typeof n() < 'u') {
        if (!l() || y()) return
        c.preventDefault()
      }
    }),
    window.addEventListener('beforeunload', (c) => {
      l() && (c.preventDefault(), (c.returnValue = !0))
    }))
  }
  window.setUpUnsavedActionChangesAlert = ({
    resolveLivewireComponentUsing: t,
    $wire: n
  }) => {
    window.addEventListener('beforeunload', (i) => {
      if (
        !(typeof t() > 'u') &&
                (n.mountedActions?.length ?? 0) &&
                !n?.__instance?.effects?.redirect
      ) {
        (i.preventDefault(), (i.returnValue = !0))
      }
    })
  }
  document.addEventListener('alpine:init', () => {
    (window.Alpine.plugin(j), window.Alpine.store('sidebar', V()))
  })
})()
