const Ft = Math.min
const vt = Math.max
const Ht = Math.round
const st = (n) => ({ x: n, y: n })
const ji = { left: 'right', right: 'left', bottom: 'top', top: 'bottom' }
const qi = { start: 'end', end: 'start' }
function De (n, t, e) {
  return vt(n, Ft(t, e))
}
function Vt (n, t) {
  return typeof n === 'function' ? n(t) : n
}
function yt (n) {
  return n.split('-')[0]
}
function Wt (n) {
  return n.split('-')[1]
}
function Ae (n) {
  return n === 'x' ? 'y' : 'x'
}
function Ce (n) {
  return n === 'y' ? 'height' : 'width'
}
const Ji = new Set(['top', 'bottom'])
function ht (n) {
  return Ji.has(yt(n)) ? 'y' : 'x'
}
function Le (n) {
  return Ae(ht(n))
}
function Je (n, t, e) {
  e === void 0 && (e = !1)
  const i = Wt(n)
  const o = Le(n)
  const s = Ce(o)
  let r =
        o === 'x'
          ? i === (e ? 'end' : 'start')
            ? 'right'
            : 'left'
          : i === 'start'
            ? 'bottom'
            : 'top'
  return (t.reference[s] > t.floating[s] && (r = Bt(r)), [r, Bt(r)])
}
function Qe (n) {
  const t = Bt(n)
  return [ie(n), t, ie(t)]
}
function ie (n) {
  return n.replace(/start|end/g, (t) => qi[t])
}
const je = ['left', 'right']
const qe = ['right', 'left']
const Qi = ['top', 'bottom']
const Zi = ['bottom', 'top']
function tn (n, t, e) {
  switch (n) {
    case 'top':
    case 'bottom':
      return e ? (t ? qe : je) : t ? je : qe
    case 'left':
    case 'right':
      return t ? Qi : Zi
    default:
      return []
  }
}
function Ze (n, t, e, i) {
  const o = Wt(n)
  let s = tn(yt(n), e === 'start', i)
  return (
    o && ((s = s.map((r) => r + '-' + o)), t && (s = s.concat(s.map(ie)))),
    s
  )
}
function Bt (n) {
  return n.replace(/left|right|bottom|top/g, (t) => ji[t])
}
function en (n) {
  return { top: 0, right: 0, bottom: 0, left: 0, ...n }
}
function ti (n) {
  return typeof n !== 'number'
    ? en(n)
    : { top: n, right: n, bottom: n, left: n }
}
function Et (n) {
  const { x: t, y: e, width: i, height: o } = n
  return {
    width: i,
    height: o,
    top: e,
    left: t,
    right: t + i,
    bottom: e + o,
    x: t,
    y: e
  }
}
function ei (n, t, e) {
  const { reference: i, floating: o } = n
  const s = ht(t)
  const r = Le(t)
  const a = Ce(r)
  const l = yt(t)
  const c = s === 'y'
  const f = i.x + i.width / 2 - o.width / 2
  const d = i.y + i.height / 2 - o.height / 2
  const p = i[a] / 2 - o[a] / 2
  let u
  switch (l) {
    case 'top':
      u = { x: f, y: i.y - o.height }
      break
    case 'bottom':
      u = { x: f, y: i.y + i.height }
      break
    case 'right':
      u = { x: i.x + i.width, y: d }
      break
    case 'left':
      u = { x: i.x - o.width, y: d }
      break
    default:
      u = { x: i.x, y: i.y }
  }
  switch (Wt(t)) {
    case 'start':
      u[r] -= p * (e && c ? -1 : 1)
      break
    case 'end':
      u[r] += p * (e && c ? -1 : 1)
      break
  }
  return u
}
const ii = async (n, t, e) => {
  const {
    placement: i = 'bottom',
    strategy: o = 'absolute',
    middleware: s = [],
    platform: r
  } = e
  const a = s.filter(Boolean)
  const l = await (r.isRTL == null ? void 0 : r.isRTL(t))
  let c = await r.getElementRects({ reference: n, floating: t, strategy: o })
  let { x: f, y: d } = ei(c, i, l)
  let p = i
  let u = {}
  let g = 0
  for (let m = 0; m < a.length; m++) {
    const { name: S, fn: O } = a[m]
    const {
      x: w,
      y: D,
      data: A,
      reset: C
    } = await O({
      x: f,
      y: d,
      initialPlacement: i,
      placement: p,
      strategy: o,
      middlewareData: u,
      rects: c,
      platform: r,
      elements: { reference: n, floating: t }
    });
    ((f = w ?? f),
    (d = D ?? d),
    (u = { ...u, [S]: { ...u[S], ...A } }),
    C &&
                g <= 50 &&
                (g++,
                typeof C === 'object' &&
                    (C.placement && (p = C.placement),
                    C.rects &&
                        (c =
                            C.rects === !0
                              ? await r.getElementRects({
                                reference: n,
                                floating: t,
                                strategy: o
                              })
                              : C.rects),
                    ({ x: f, y: d } = ei(c, p, l))),
                (m = -1)))
  }
  return { x: f, y: d, placement: p, strategy: o, middlewareData: u }
}
async function Ie (n, t) {
  let e
  t === void 0 && (t = {})
  const { x: i, y: o, platform: s, rects: r, elements: a, strategy: l } = n
  const {
    boundary: c = 'clippingAncestors',
    rootBoundary: f = 'viewport',
    elementContext: d = 'floating',
    altBoundary: p = !1,
    padding: u = 0
  } = Vt(t, n)
  const g = ti(u)
  const S = a[p ? (d === 'floating' ? 'reference' : 'floating') : d]
  const O = Et(
    await s.getClippingRect({
      element:
                (e = await (s.isElement == null ? void 0 : s.isElement(S))) ==
                    null || e
                  ? S
                  : S.contextElement ||
                      (await (s.getDocumentElement == null
                        ? void 0
                        : s.getDocumentElement(a.floating))),
      boundary: c,
      rootBoundary: f,
      strategy: l
    })
  )
  const w =
        d === 'floating'
          ? {
              x: i,
              y: o,
              width: r.floating.width,
              height: r.floating.height
            }
          : r.reference
  const D = await (s.getOffsetParent == null
    ? void 0
    : s.getOffsetParent(a.floating))
  const A = (await (s.isElement == null ? void 0 : s.isElement(D)))
    ? (await (s.getScale == null ? void 0 : s.getScale(D))) || {
        x: 1,
        y: 1
      }
    : { x: 1, y: 1 }
  const C = Et(
    s.convertOffsetParentRelativeRectToViewportRelativeRect
      ? await s.convertOffsetParentRelativeRectToViewportRelativeRect({
        elements: a,
        rect: w,
        offsetParent: D,
        strategy: l
      })
      : w
  )
  return {
    top: (O.top - C.top + g.top) / A.y,
    bottom: (C.bottom - O.bottom + g.bottom) / A.y,
    left: (O.left - C.left + g.left) / A.x,
    right: (C.right - O.right + g.right) / A.x
  }
}
const ni = function (n) {
  return (
    n === void 0 && (n = {}),
    {
      name: 'flip',
      options: n,
      async fn (t) {
        let e, i
        const {
          placement: o,
          middlewareData: s,
          rects: r,
          initialPlacement: a,
          platform: l,
          elements: c
        } = t
        const {
          mainAxis: f = !0,
          crossAxis: d = !0,
          fallbackPlacements: p,
          fallbackStrategy: u = 'bestFit',
          fallbackAxisSideDirection: g = 'none',
          flipAlignment: m = !0,
          ...S
        } = Vt(n, t)
        if ((e = s.arrow) != null && e.alignmentOffset) return {}
        const O = yt(o)
        const w = ht(a)
        const D = yt(a) === a
        const A = await (l.isRTL == null
          ? void 0
          : l.isRTL(c.floating))
        const C = p || (D || !m ? [Bt(a)] : Qe(a))
        const H = g !== 'none'
        !p && H && C.push(...Ze(a, m, g, A))
        const q = [a, ...C]
        const J = await Ie(t, S)
        const R = []
        let z = ((i = s.flip) == null ? void 0 : i.overflows) || []
        if ((f && R.push(J[O]), d)) {
          const F = Je(o, r, A)
          R.push(J[F[0]], J[F[1]])
        }
        if (
          ((z = [...z, { placement: o, overflows: R }]),
          !R.every((F) => F <= 0))
        ) {
          let L, Y
          const F =
                        (((L = s.flip) == null ? void 0 : L.index) || 0) + 1
          const Q = q[F]
          if (
            Q &&
                        (!(d === 'alignment' ? w !== ht(Q) : !1) ||
                            z.every((T) =>
                              ht(T.placement) === w ? T.overflows[0] > 0 : !0
                            ))
          ) {
            return {
              data: { index: F, overflows: z },
              reset: { placement: Q }
            }
          }
          let V =
                        (Y = z
                          .filter((X) => X.overflows[0] <= 0)
                          .sort(
                            (X, T) => X.overflows[1] - T.overflows[1]
                          )[0]) == null
                          ? void 0
                          : Y.placement
          if (!V) {
            switch (u) {
              case 'bestFit': {
                let B
                const X =
                                    (B = z
                                      .filter((T) => {
                                        if (H) {
                                          const et = ht(T.placement)
                                          return et === w || et === 'y'
                                        }
                                        return !0
                                      })
                                      .map((T) => [
                                        T.placement,
                                        T.overflows
                                          .filter((et) => et > 0)
                                          .reduce((et, ee) => et + ee, 0)
                                      ])
                                      .sort((T, et) => T[1] - et[1])[0]) ==
                                    null
                                      ? void 0
                                      : B[0]
                X && (V = X)
                break
              }
              case 'initialPlacement':
                V = a
                break
            }
          }
          if (o !== V) return { reset: { placement: V } }
        }
        return {}
      }
    }
  )
}
const nn = new Set(['left', 'top'])
async function on (n, t) {
  const { placement: e, platform: i, elements: o } = n
  const s = await (i.isRTL == null ? void 0 : i.isRTL(o.floating))
  const r = yt(e)
  const a = Wt(e)
  const l = ht(e) === 'y'
  const c = nn.has(r) ? -1 : 1
  const f = s && l ? -1 : 1
  const d = Vt(t, n)
  let {
    mainAxis: p,
    crossAxis: u,
    alignmentAxis: g
  } = typeof d === 'number'
    ? { mainAxis: d, crossAxis: 0, alignmentAxis: null }
    : {
        mainAxis: d.mainAxis || 0,
        crossAxis: d.crossAxis || 0,
        alignmentAxis: d.alignmentAxis
      }
  return (
    a && typeof g === 'number' && (u = a === 'end' ? g * -1 : g),
    l ? { x: u * f, y: p * c } : { x: p * c, y: u * f }
  )
}
const oi = function (n) {
  return (
    n === void 0 && (n = 0),
    {
      name: 'offset',
      options: n,
      async fn (t) {
        let e, i
        const { x: o, y: s, placement: r, middlewareData: a } = t
        const l = await on(t, n)
        return r === ((e = a.offset) == null ? void 0 : e.placement) &&
                    (i = a.arrow) != null &&
                    i.alignmentOffset
          ? {}
          : {
              x: o + l.x,
              y: s + l.y,
              data: { ...l, placement: r }
            }
      }
    }
  )
}
const si = function (n) {
  return (
    n === void 0 && (n = {}),
    {
      name: 'shift',
      options: n,
      async fn (t) {
        const { x: e, y: i, placement: o } = t
        const {
          mainAxis: s = !0,
          crossAxis: r = !1,
          limiter: a = {
            fn: (S) => {
              const { x: O, y: w } = S
              return { x: O, y: w }
            }
          },
          ...l
        } = Vt(n, t)
        const c = { x: e, y: i }
        const f = await Ie(t, l)
        const d = ht(yt(o))
        const p = Ae(d)
        let u = c[p]
        let g = c[d]
        if (s) {
          const S = p === 'y' ? 'top' : 'left'
          const O = p === 'y' ? 'bottom' : 'right'
          const w = u + f[S]
          const D = u - f[O]
          u = De(w, u, D)
        }
        if (r) {
          const S = d === 'y' ? 'top' : 'left'
          const O = d === 'y' ? 'bottom' : 'right'
          const w = g + f[S]
          const D = g - f[O]
          g = De(w, g, D)
        }
        const m = a.fn({ ...t, [p]: u, [d]: g })
        return {
          ...m,
          data: {
            x: m.x - e,
            y: m.y - i,
            enabled: { [p]: s, [d]: r }
          }
        }
      }
    }
  )
}
function oe () {
  return typeof window < 'u'
}
function Ot (n) {
  return ai(n) ? (n.nodeName || '').toLowerCase() : '#document'
}
function U (n) {
  let t
  return (
    (n == null || (t = n.ownerDocument) == null ? void 0 : t.defaultView) ||
        window
  )
}
function ct (n) {
  let t
  return (t = (ai(n) ? n.ownerDocument : n.document) || window.document) ==
        null
    ? void 0
    : t.documentElement
}
function ai (n) {
  return oe() ? n instanceof Node || n instanceof U(n).Node : !1
}
function it (n) {
  return oe() ? n instanceof Element || n instanceof U(n).Element : !1
}
function rt (n) {
  return oe()
    ? n instanceof HTMLElement || n instanceof U(n).HTMLElement
    : !1
}
function ri (n) {
  return !oe() || typeof ShadowRoot > 'u'
    ? !1
    : n instanceof ShadowRoot || n instanceof U(n).ShadowRoot
}
const sn = new Set(['inline', 'contents'])
function It (n) {
  const { overflow: t, overflowX: e, overflowY: i, display: o } = nt(n)
  return /auto|scroll|overlay|hidden|clip/.test(t + i + e) && !sn.has(o)
}
const rn = new Set(['table', 'td', 'th'])
function li (n) {
  return rn.has(Ot(n))
}
const an = [':popover-open', ':modal']
function zt (n) {
  return an.some((t) => {
    try {
      return n.matches(t)
    } catch {
      return !1
    }
  })
}
const ln = ['transform', 'translate', 'scale', 'rotate', 'perspective']
const cn = [
  'transform',
  'translate',
  'scale',
  'rotate',
  'perspective',
  'filter'
]
const dn = ['paint', 'layout', 'strict', 'content']
function se (n) {
  const t = re()
  const e = it(n) ? nt(n) : n
  return (
    ln.some((i) => (e[i] ? e[i] !== 'none' : !1)) ||
        (e.containerType ? e.containerType !== 'normal' : !1) ||
        (!t && (e.backdropFilter ? e.backdropFilter !== 'none' : !1)) ||
        (!t && (e.filter ? e.filter !== 'none' : !1)) ||
        cn.some((i) => (e.willChange || '').includes(i)) ||
        dn.some((i) => (e.contain || '').includes(i))
  )
}
function ci (n) {
  let t = ut(n)
  for (; rt(t) && !Dt(t);) {
    if (se(t)) return t
    if (zt(t)) return null
    t = ut(t)
  }
  return null
}
function re () {
  return typeof CSS > 'u' || !CSS.supports
    ? !1
    : CSS.supports('-webkit-backdrop-filter', 'none')
}
const fn = new Set(['html', 'body', '#document'])
function Dt (n) {
  return fn.has(Ot(n))
}
function nt (n) {
  return U(n).getComputedStyle(n)
}
function $t (n) {
  return it(n)
    ? { scrollLeft: n.scrollLeft, scrollTop: n.scrollTop }
    : { scrollLeft: n.scrollX, scrollTop: n.scrollY }
}
function ut (n) {
  if (Ot(n) === 'html') return n
  const t = n.assignedSlot || n.parentNode || (ri(n) && n.host) || ct(n)
  return ri(t) ? t.host : t
}
function di (n) {
  const t = ut(n)
  return Dt(t)
    ? n.ownerDocument
      ? n.ownerDocument.body
      : n.body
    : rt(t) && It(t)
      ? t
      : di(t)
}
function ne (n, t, e) {
  let i;
  (t === void 0 && (t = []), e === void 0 && (e = !0))
  const o = di(n)
  const s = o === ((i = n.ownerDocument) == null ? void 0 : i.body)
  const r = U(o)
  if (s) {
    const a = ae(r)
    return t.concat(
      r,
      r.visualViewport || [],
      It(o) ? o : [],
      a && e ? ne(a) : []
    )
  }
  return t.concat(o, ne(o, [], e))
}
function ae (n) {
  return n.parent && Object.getPrototypeOf(n.parent) ? n.frameElement : null
}
function pi (n) {
  const t = nt(n)
  let e = parseFloat(t.width) || 0
  let i = parseFloat(t.height) || 0
  const o = rt(n)
  const s = o ? n.offsetWidth : e
  const r = o ? n.offsetHeight : i
  const a = Ht(e) !== s || Ht(i) !== r
  return (a && ((e = s), (i = r)), { width: e, height: i, $: a })
}
function gi (n) {
  return it(n) ? n : n.contextElement
}
function Tt (n) {
  const t = gi(n)
  if (!rt(t)) return st(1)
  const e = t.getBoundingClientRect()
  const { width: i, height: o, $: s } = pi(t)
  let r = (s ? Ht(e.width) : e.width) / i
  let a = (s ? Ht(e.height) : e.height) / o
  return (
    (!r || !Number.isFinite(r)) && (r = 1),
    (!a || !Number.isFinite(a)) && (a = 1),
    { x: r, y: a }
  )
}
const hn = st(0)
function mi (n) {
  const t = U(n)
  return !re() || !t.visualViewport
    ? hn
    : { x: t.visualViewport.offsetLeft, y: t.visualViewport.offsetTop }
}
function un (n, t, e) {
  return (t === void 0 && (t = !1), !e || (t && e !== U(n)) ? !1 : t)
}
function Xt (n, t, e, i) {
  (t === void 0 && (t = !1), e === void 0 && (e = !1))
  const o = n.getBoundingClientRect()
  const s = gi(n)
  let r = st(1)
  t && (i ? it(i) && (r = Tt(i)) : (r = Tt(n)))
  const a = un(s, e, i) ? mi(s) : st(0)
  let l = (o.left + a.x) / r.x
  let c = (o.top + a.y) / r.y
  let f = o.width / r.x
  let d = o.height / r.y
  if (s) {
    const p = U(s)
    const u = i && it(i) ? U(i) : i
    let g = p
    let m = ae(g)
    for (; m && i && u !== g;) {
      const S = Tt(m)
      const O = m.getBoundingClientRect()
      const w = nt(m)
      const D = O.left + (m.clientLeft + parseFloat(w.paddingLeft)) * S.x
      const A = O.top + (m.clientTop + parseFloat(w.paddingTop)) * S.y;
      ((l *= S.x),
      (c *= S.y),
      (f *= S.x),
      (d *= S.y),
      (l += D),
      (c += A),
      (g = U(m)),
      (m = ae(g)))
    }
  }
  return Et({ width: f, height: d, x: l, y: c })
}
function le (n, t) {
  const e = $t(n).scrollLeft
  return t ? t.left + e : Xt(ct(n)).left + e
}
function bi (n, t) {
  const e = n.getBoundingClientRect()
  const i = e.left + t.scrollLeft - le(n, e)
  const o = e.top + t.scrollTop
  return { x: i, y: o }
}
function pn (n) {
  const { elements: t, rect: e, offsetParent: i, strategy: o } = n
  const s = o === 'fixed'
  const r = ct(i)
  const a = t ? zt(t.floating) : !1
  if (i === r || (a && s)) return e
  let l = { scrollLeft: 0, scrollTop: 0 }
  let c = st(1)
  const f = st(0)
  const d = rt(i)
  if (
    (d || (!d && !s)) &&
        ((Ot(i) !== 'body' || It(r)) && (l = $t(i)), rt(i))
  ) {
    const u = Xt(i);
    ((c = Tt(i)), (f.x = u.x + i.clientLeft), (f.y = u.y + i.clientTop))
  }
  const p = r && !d && !s ? bi(r, l) : st(0)
  return {
    width: e.width * c.x,
    height: e.height * c.y,
    x: e.x * c.x - l.scrollLeft * c.x + f.x + p.x,
    y: e.y * c.y - l.scrollTop * c.y + f.y + p.y
  }
}
function gn (n) {
  return Array.from(n.getClientRects())
}
function mn (n) {
  const t = ct(n)
  const e = $t(n)
  const i = n.ownerDocument.body
  const o = vt(t.scrollWidth, t.clientWidth, i.scrollWidth, i.clientWidth)
  const s = vt(
    t.scrollHeight,
    t.clientHeight,
    i.scrollHeight,
    i.clientHeight
  )
  let r = -e.scrollLeft + le(n)
  const a = -e.scrollTop
  return (
    nt(i).direction === 'rtl' &&
            (r += vt(t.clientWidth, i.clientWidth) - o),
    { width: o, height: s, x: r, y: a }
  )
}
const fi = 25
function bn (n, t) {
  const e = U(n)
  const i = ct(n)
  const o = e.visualViewport
  let s = i.clientWidth
  let r = i.clientHeight
  let a = 0
  let l = 0
  if (o) {
    ((s = o.width), (r = o.height))
    const f = re();
    (!f || (f && t === 'fixed')) && ((a = o.offsetLeft), (l = o.offsetTop))
  }
  const c = le(i)
  if (c <= 0) {
    const f = i.ownerDocument
    const d = f.body
    const p = getComputedStyle(d)
    const u =
            (f.compatMode === 'CSS1Compat' &&
                parseFloat(p.marginLeft) + parseFloat(p.marginRight)) ||
            0
    const g = Math.abs(i.clientWidth - d.clientWidth - u)
    g <= fi && (s -= g)
  } else c <= fi && (s += c)
  return { width: s, height: r, x: a, y: l }
}
const vn = new Set(['absolute', 'fixed'])
function yn (n, t) {
  const e = Xt(n, !0, t === 'fixed')
  const i = e.top + n.clientTop
  const o = e.left + n.clientLeft
  const s = rt(n) ? Tt(n) : st(1)
  const r = n.clientWidth * s.x
  const a = n.clientHeight * s.y
  const l = o * s.x
  const c = i * s.y
  return { width: r, height: a, x: l, y: c }
}
function hi (n, t, e) {
  let i
  if (t === 'viewport') i = bn(n, e)
  else if (t === 'document') i = mn(ct(n))
  else if (it(t)) i = yn(t, e)
  else {
    const o = mi(n)
    i = { x: t.x - o.x, y: t.y - o.y, width: t.width, height: t.height }
  }
  return Et(i)
}
function vi (n, t) {
  const e = ut(n)
  return e === t || !it(e) || Dt(e)
    ? !1
    : nt(e).position === 'fixed' || vi(e, t)
}
function wn (n, t) {
  const e = t.get(n)
  if (e) return e
  let i = ne(n, [], !1).filter((a) => it(a) && Ot(a) !== 'body')
  let o = null
  const s = nt(n).position === 'fixed'
  let r = s ? ut(n) : n
  for (; it(r) && !Dt(r);) {
    const a = nt(r)
    const l = se(r);
    (!l && a.position === 'fixed' && (o = null),
    (
      s
        ? !l && !o
        : (!l &&
                          a.position === 'static' &&
                          !!o &&
                          vn.has(o.position)) ||
                      (It(r) && !l && vi(n, r))
    )
      ? (i = i.filter((f) => f !== r))
      : (o = a),
    (r = ut(r)))
  }
  return (t.set(n, i), i)
}
function Sn (n) {
  const { element: t, boundary: e, rootBoundary: i, strategy: o } = n
  const r = [
    ...(e === 'clippingAncestors'
      ? zt(t)
        ? []
        : wn(t, this._c)
      : [].concat(e)),
    i
  ]
  const a = r[0]
  const l = r.reduce(
    (c, f) => {
      const d = hi(t, f, o)
      return (
        (c.top = vt(d.top, c.top)),
        (c.right = Ft(d.right, c.right)),
        (c.bottom = Ft(d.bottom, c.bottom)),
        (c.left = vt(d.left, c.left)),
        c
      )
    },
    hi(t, a, o)
  )
  return {
    width: l.right - l.left,
    height: l.bottom - l.top,
    x: l.left,
    y: l.top
  }
}
function xn (n) {
  const { width: t, height: e } = pi(n)
  return { width: t, height: e }
}
function En (n, t, e) {
  const i = rt(t)
  const o = ct(t)
  const s = e === 'fixed'
  const r = Xt(n, !0, s, t)
  let a = { scrollLeft: 0, scrollTop: 0 }
  const l = st(0)
  function c () {
    l.x = le(o)
  }
  if (i || (!i && !s)) {
    if (((Ot(t) !== 'body' || It(o)) && (a = $t(t)), i)) {
      const u = Xt(t, !0, s, t);
      ((l.x = u.x + t.clientLeft), (l.y = u.y + t.clientTop))
    } else o && c()
  }
  s && !i && o && c()
  const f = o && !i && !s ? bi(o, a) : st(0)
  const d = r.left + a.scrollLeft - l.x - f.x
  const p = r.top + a.scrollTop - l.y - f.y
  return { x: d, y: p, width: r.width, height: r.height }
}
function Te (n) {
  return nt(n).position === 'static'
}
function ui (n, t) {
  if (!rt(n) || nt(n).position === 'fixed') return null
  if (t) return t(n)
  let e = n.offsetParent
  return (ct(n) === e && (e = e.ownerDocument.body), e)
}
function yi (n, t) {
  const e = U(n)
  if (zt(n)) return e
  if (!rt(n)) {
    let o = ut(n)
    for (; o && !Dt(o);) {
      if (it(o) && !Te(o)) return o
      o = ut(o)
    }
    return e
  }
  let i = ui(n, t)
  for (; i && li(i) && Te(i);) i = ui(i, t)
  return i && Dt(i) && Te(i) && !se(i) ? e : i || ci(n) || e
}
const On = async function (n) {
  const t = this.getOffsetParent || yi
  const e = this.getDimensions
  const i = await e(n.floating)
  return {
    reference: En(n.reference, await t(n.floating), n.strategy),
    floating: { x: 0, y: 0, width: i.width, height: i.height }
  }
}
function Dn (n) {
  return nt(n).direction === 'rtl'
}
const An = {
  convertOffsetParentRelativeRectToViewportRelativeRect: pn,
  getDocumentElement: ct,
  getClippingRect: Sn,
  getOffsetParent: yi,
  getElementRects: On,
  getClientRects: gn,
  getDimensions: xn,
  getScale: Tt,
  isElement: it,
  isRTL: Dn
}
const wi = oi
const Si = si
const xi = ni
const Ei = (n, t, e) => {
  const i = new Map()
  const o = { platform: An, ...e }
  const s = { ...o.platform, _c: i }
  return ii(n, t, { ...o, platform: s })
}
function Oi (n, t) {
  const e = Object.keys(n)
  if (Object.getOwnPropertySymbols) {
    let i = Object.getOwnPropertySymbols(n);
    (t &&
            (i = i.filter(function (o) {
              return Object.getOwnPropertyDescriptor(n, o).enumerable
            })),
    e.push.apply(e, i))
  }
  return e
}
function ft (n) {
  for (let t = 1; t < arguments.length; t++) {
    var e = arguments[t] != null ? arguments[t] : {}
    t % 2
      ? Oi(Object(e), !0).forEach(function (i) {
        Cn(n, i, e[i])
      })
      : Object.getOwnPropertyDescriptors
        ? Object.defineProperties(n, Object.getOwnPropertyDescriptors(e))
        : Oi(Object(e)).forEach(function (i) {
          Object.defineProperty(
            n,
            i,
            Object.getOwnPropertyDescriptor(e, i)
          )
        })
  }
  return n
}
function ue (n) {
  '@babel/helpers - typeof'
  return (
    typeof Symbol === 'function' && typeof Symbol.iterator === 'symbol'
      ? (ue = function (t) {
          return typeof t
        })
      : (ue = function (t) {
          return t &&
                      typeof Symbol === 'function' &&
                      t.constructor === Symbol &&
                      t !== Symbol.prototype
            ? 'symbol'
            : typeof t
        }),
    ue(n)
  )
}
function Cn (n, t, e) {
  return (
    t in n
      ? Object.defineProperty(n, t, {
        value: e,
        enumerable: !0,
        configurable: !0,
        writable: !0
      })
      : (n[t] = e),
    n
  )
}
function gt () {
  return (
    (gt =
            Object.assign ||
            function (n) {
              for (let t = 1; t < arguments.length; t++) {
                const e = arguments[t]
                for (const i in e) {
                  Object.prototype.hasOwnProperty.call(e, i) &&
                            (n[i] = e[i])
                }
              }
              return n
            }),
    gt.apply(this, arguments)
  )
}
function Ln (n, t) {
  if (n == null) return {}
  const e = {}
  const i = Object.keys(n)
  let o
  let s
  for (s = 0; s < i.length; s++) {
    ((o = i[s]), !(t.indexOf(o) >= 0) && (e[o] = n[o]))
  }
  return e
}
function In (n, t) {
  if (n == null) return {}
  const e = Ln(n, t)
  let i
  let o
  if (Object.getOwnPropertySymbols) {
    const s = Object.getOwnPropertySymbols(n)
    for (o = 0; o < s.length; o++) {
      ((i = s[o]),
      !(t.indexOf(i) >= 0) &&
                    Object.prototype.propertyIsEnumerable.call(n, i) &&
                    (e[i] = n[i]))
    }
  }
  return e
}
const Tn = '1.15.6'
function pt (n) {
  if (typeof window < 'u' && window.navigator) {
    return !!navigator.userAgent.match(n)
  }
}
const mt = pt(/(?:Trident.*rv[ :]?11\.|msie|iemobile|Windows Phone)/i)
const Zt = pt(/Edge/i)
const Di = pt(/firefox/i)
const Gt = pt(/safari/i) && !pt(/chrome/i) && !pt(/android/i)
const Xe = pt(/iP(ad|od|hone)/i)
const Mi = pt(/chrome/i) && pt(/android/i)
const Pi = { capture: !1, passive: !1 }
function E (n, t, e) {
  n.addEventListener(t, e, !mt && Pi)
}
function x (n, t, e) {
  n.removeEventListener(t, e, !mt && Pi)
}
function ve (n, t) {
  if (t) {
    if ((t[0] === '>' && (t = t.substring(1)), n)) {
      try {
        if (n.matches) return n.matches(t)
        if (n.msMatchesSelector) return n.msMatchesSelector(t)
        if (n.webkitMatchesSelector) return n.webkitMatchesSelector(t)
      } catch {
        return !1
      }
    }
    return !1
  }
}
function Ni (n) {
  return n.host && n !== document && n.host.nodeType ? n.host : n.parentNode
}
function lt (n, t, e, i) {
  if (n) {
    e = e || document
    do {
      if (
        (t != null &&
                    (t[0] === '>'
                      ? n.parentNode === e && ve(n, t)
                      : ve(n, t))) ||
                (i && n === e)
      ) {
        return n
      }
      if (n === e) break
    } while ((n = Ni(n)))
  }
  return null
}
const Ai = /\s+/g
function Z (n, t, e) {
  if (n && t) {
    if (n.classList) n.classList[e ? 'add' : 'remove'](t)
    else {
      const i = (' ' + n.className + ' ')
        .replace(Ai, ' ')
        .replace(' ' + t + ' ', ' ')
      n.className = (i + (e ? ' ' + t : '')).replace(Ai, ' ')
    }
  }
}
function b (n, t, e) {
  const i = n && n.style
  if (i) {
    if (e === void 0) {
      return (
        document.defaultView && document.defaultView.getComputedStyle
          ? (e = document.defaultView.getComputedStyle(n, ''))
          : n.currentStyle && (e = n.currentStyle),
        t === void 0 ? e : e[t]
      )
    }
    (!(t in i) && t.indexOf('webkit') === -1 && (t = '-webkit-' + t),
    (i[t] = e + (typeof e === 'string' ? '' : 'px')))
  }
}
function Nt (n, t) {
  let e = ''
  if (typeof n === 'string') e = n
  else {
    do {
      const i = b(n, 'transform')
      i && i !== 'none' && (e = i + ' ' + e)
    } while (!t && (n = n.parentNode))
  }
  const o =
        window.DOMMatrix ||
        window.WebKitCSSMatrix ||
        window.CSSMatrix ||
        window.MSCSSMatrix
  return o && new o(e)
}
function ki (n, t, e) {
  if (n) {
    const i = n.getElementsByTagName(t)
    let o = 0
    const s = i.length
    if (e) for (; o < s; o++) e(i[o], o)
    return i
  }
  return []
}
function dt () {
  const n = document.scrollingElement
  return n || document.documentElement
}
function k (n, t, e, i, o) {
  if (!(!n.getBoundingClientRect && n !== window)) {
    let s, r, a, l, c, f, d
    if (
      (n !== window && n.parentNode && n !== dt()
        ? ((s = n.getBoundingClientRect()),
          (r = s.top),
          (a = s.left),
          (l = s.bottom),
          (c = s.right),
          (f = s.height),
          (d = s.width))
        : ((r = 0),
          (a = 0),
          (l = window.innerHeight),
          (c = window.innerWidth),
          (f = window.innerHeight),
          (d = window.innerWidth)),
      (t || e) && n !== window && ((o = o || n.parentNode), !mt))
    ) {
      do {
        if (
          o &&
                    o.getBoundingClientRect &&
                    (b(o, 'transform') !== 'none' ||
                        (e && b(o, 'position') !== 'static'))
        ) {
          const p = o.getBoundingClientRect();
          ((r -= p.top + parseInt(b(o, 'border-top-width'))),
          (a -= p.left + parseInt(b(o, 'border-left-width'))),
          (l = r + s.height),
          (c = a + s.width))
          break
        }
      } while ((o = o.parentNode))
    }
    if (i && n !== window) {
      const u = Nt(o || n)
      const g = u && u.a
      const m = u && u.d
      u &&
                ((r /= m),
                (a /= g),
                (d /= g),
                (f /= m),
                (l = r + f),
                (c = a + d))
    }
    return { top: r, left: a, bottom: l, right: c, width: d, height: f }
  }
}
function Ci (n, t, e) {
  for (let i = xt(n, !0), o = k(n)[t]; i;) {
    const s = k(i)[e]
    let r = void 0
    if ((e === 'top' || e === 'left' ? (r = o >= s) : (r = o <= s), !r)) {
      return i
    }
    if (i === dt()) break
    i = xt(i, !1)
  }
  return !1
}
function kt (n, t, e, i) {
  for (let o = 0, s = 0, r = n.children; s < r.length;) {
    if (
      r[s].style.display !== 'none' &&
            r[s] !== v.ghost &&
            (i || r[s] !== v.dragged) &&
            lt(r[s], e.draggable, n, !1)
    ) {
      if (o === t) return r[s]
      o++
    }
    s++
  }
  return null
}
function Ke (n, t) {
  for (
    var e = n.lastElementChild;
    e && (e === v.ghost || b(e, 'display') === 'none' || (t && !ve(e, t)));
  ) {
    e = e.previousElementSibling
  }
  return e || null
}
function ot (n, t) {
  let e = 0
  if (!n || !n.parentNode) return -1
  for (; (n = n.previousElementSibling);) {
    n.nodeName.toUpperCase() !== 'TEMPLATE' &&
            n !== v.clone &&
            (!t || ve(n, t)) &&
            e++
  }
  return e
}
function Li (n) {
  let t = 0
  let e = 0
  const i = dt()
  if (n) {
    do {
      const o = Nt(n)
      const s = o.a
      const r = o.d;
      ((t += n.scrollLeft * s), (e += n.scrollTop * r))
    } while (n !== i && (n = n.parentNode))
  }
  return [t, e]
}
function _n (n, t) {
  for (const e in n) {
    if (n.hasOwnProperty(e)) {
      for (const i in t) {
        if (t.hasOwnProperty(i) && t[i] === n[e][i]) return Number(e)
      }
    }
  }
  return -1
}
function xt (n, t) {
  if (!n || !n.getBoundingClientRect) return dt()
  let e = n
  let i = !1
  do {
    if (e.clientWidth < e.scrollWidth || e.clientHeight < e.scrollHeight) {
      const o = b(e)
      if (
        (e.clientWidth < e.scrollWidth &&
                    (o.overflowX == 'auto' || o.overflowX == 'scroll')) ||
                (e.clientHeight < e.scrollHeight &&
                    (o.overflowY == 'auto' || o.overflowY == 'scroll'))
      ) {
        if (!e.getBoundingClientRect || e === document.body) {
          return dt()
        }
        if (i || t) return e
        i = !0
      }
    }
  } while ((e = e.parentNode))
  return dt()
}
function Rn (n, t) {
  if (n && t) for (const e in t) t.hasOwnProperty(e) && (n[e] = t[e])
  return n
}
function _e (n, t) {
  return (
    Math.round(n.top) === Math.round(t.top) &&
        Math.round(n.left) === Math.round(t.left) &&
        Math.round(n.height) === Math.round(t.height) &&
        Math.round(n.width) === Math.round(t.width)
  )
}
let jt
function Bi (n, t) {
  return function () {
    if (!jt) {
      const e = arguments
      const i = this;
      (e.length === 1 ? n.call(i, e[0]) : n.apply(i, e),
      (jt = setTimeout(function () {
        jt = void 0
      }, t)))
    }
  }
}
function Mn () {
  (clearTimeout(jt), (jt = void 0))
}
function Fi (n, t, e) {
  ((n.scrollLeft += t), (n.scrollTop += e))
}
function Hi (n) {
  const t = window.Polymer
  const e = window.jQuery || window.Zepto
  return t && t.dom
    ? t.dom(n).cloneNode(!0)
    : e
      ? e(n).clone(!0)[0]
      : n.cloneNode(!0)
}
function Vi (n, t, e) {
  const i = {}
  return (
    Array.from(n.children).forEach(function (o) {
      let s, r, a, l
      if (!(!lt(o, t.draggable, n, !1) || o.animated || o === e)) {
        const c = k(o);
        ((i.left = Math.min(
          (s = i.left) !== null && s !== void 0 ? s : 1 / 0,
          c.left
        )),
        (i.top = Math.min(
          (r = i.top) !== null && r !== void 0 ? r : 1 / 0,
          c.top
        )),
        (i.right = Math.max(
          (a = i.right) !== null && a !== void 0 ? a : -1 / 0,
          c.right
        )),
        (i.bottom = Math.max(
          (l = i.bottom) !== null && l !== void 0 ? l : -1 / 0,
          c.bottom
        )))
      }
    }),
    (i.width = i.right - i.left),
    (i.height = i.bottom - i.top),
    (i.x = i.left),
    (i.y = i.top),
    i
  )
}
const j = 'Sortable' + new Date().getTime()
function Pn () {
  let n = []
  let t
  return {
    captureAnimationState: function () {
      if (((n = []), !!this.options.animation)) {
        const i = [].slice.call(this.el.children)
        i.forEach(function (o) {
          if (!(b(o, 'display') === 'none' || o === v.ghost)) {
            n.push({ target: o, rect: k(o) })
            const s = ft({}, n[n.length - 1].rect)
            if (o.thisAnimationDuration) {
              const r = Nt(o, !0)
              r && ((s.top -= r.f), (s.left -= r.e))
            }
            o.fromRect = s
          }
        })
      }
    },
    addAnimationState: function (i) {
      n.push(i)
    },
    removeAnimationState: function (i) {
      n.splice(_n(n, { target: i }), 1)
    },
    animateAll: function (i) {
      const o = this
      if (!this.options.animation) {
        (clearTimeout(t), typeof i === 'function' && i())
        return
      }
      let s = !1
      let r = 0;
      (n.forEach(function (a) {
        let l = 0
        const c = a.target
        const f = c.fromRect
        const d = k(c)
        const p = c.prevFromRect
        const u = c.prevToRect
        const g = a.rect
        const m = Nt(c, !0);
        (m && ((d.top -= m.f), (d.left -= m.e)),
        (c.toRect = d),
        c.thisAnimationDuration &&
                        _e(p, d) &&
                        !_e(f, d) &&
                        (g.top - d.top) / (g.left - d.left) ===
                            (f.top - d.top) / (f.left - d.left) &&
                        (l = kn(g, p, u, o.options)),
        _e(d, f) ||
                        ((c.prevFromRect = f),
                        (c.prevToRect = d),
                        l || (l = o.options.animation),
                        o.animate(c, g, d, l)),
        l &&
                        ((s = !0),
                        (r = Math.max(r, l)),
                        clearTimeout(c.animationResetTimer),
                        (c.animationResetTimer = setTimeout(function () {
                          ((c.animationTime = 0),
                          (c.prevFromRect = null),
                          (c.fromRect = null),
                          (c.prevToRect = null),
                          (c.thisAnimationDuration = null))
                        }, l)),
                        (c.thisAnimationDuration = l)))
      }),
      clearTimeout(t),
      s
        ? (t = setTimeout(function () {
            typeof i === 'function' && i()
          }, r))
        : typeof i === 'function' && i(),
      (n = []))
    },
    animate: function (i, o, s, r) {
      if (r) {
        (b(i, 'transition', ''), b(i, 'transform', ''))
        const a = Nt(this.el)
        const l = a && a.a
        const c = a && a.d
        const f = (o.left - s.left) / (l || 1)
        const d = (o.top - s.top) / (c || 1);
        ((i.animatingX = !!f),
        (i.animatingY = !!d),
        b(i, 'transform', 'translate3d(' + f + 'px,' + d + 'px,0)'),
        (this.forRepaintDummy = Nn(i)),
        b(
          i,
          'transition',
          'transform ' +
                            r +
                            'ms' +
                            (this.options.easing
                              ? ' ' + this.options.easing
                              : '')
        ),
        b(i, 'transform', 'translate3d(0,0,0)'),
        typeof i.animated === 'number' && clearTimeout(i.animated),
        (i.animated = setTimeout(function () {
          (b(i, 'transition', ''),
          b(i, 'transform', ''),
          (i.animated = !1),
          (i.animatingX = !1),
          (i.animatingY = !1))
        }, r)))
      }
    }
  }
}
function Nn (n) {
  return n.offsetWidth
}
function kn (n, t, e, i) {
  return (
    (Math.sqrt(Math.pow(t.top - n.top, 2) + Math.pow(t.left - n.left, 2)) /
            Math.sqrt(
              Math.pow(t.top - e.top, 2) + Math.pow(t.left - e.left, 2)
            )) *
        i.animation
  )
}
const _t = []
const Re = { initializeByDefault: !0 }
const te = {
  mount: function (t) {
    for (const e in Re) {
      Re.hasOwnProperty(e) && !(e in t) && (t[e] = Re[e])
    }
    (_t.forEach(function (i) {
      if (i.pluginName === t.pluginName) {
        throw 'Sortable: Cannot mount plugin '.concat(
          t.pluginName,
          ' more than once'
        )
      }
    }),
    _t.push(t))
  },
  pluginEvent: function (t, e, i) {
    const o = this;
    ((this.eventCanceled = !1),
    (i.cancel = function () {
      o.eventCanceled = !0
    }))
    const s = t + 'Global'
    _t.forEach(function (r) {
      e[r.pluginName] &&
                (e[r.pluginName][s] &&
                    e[r.pluginName][s](ft({ sortable: e }, i)),
                e.options[r.pluginName] &&
                    e[r.pluginName][t] &&
                    e[r.pluginName][t](ft({ sortable: e }, i)))
    })
  },
  initializePlugins: function (t, e, i, o) {
    _t.forEach(function (a) {
      const l = a.pluginName
      if (!(!t.options[l] && !a.initializeByDefault)) {
        const c = new a(t, e, t.options);
        ((c.sortable = t),
        (c.options = t.options),
        (t[l] = c),
        gt(i, c.defaults))
      }
    })
    for (const s in t.options) {
      if (t.options.hasOwnProperty(s)) {
        const r = this.modifyOption(t, s, t.options[s])
        typeof r < 'u' && (t.options[s] = r)
      }
    }
  },
  getEventProperties: function (t, e) {
    const i = {}
    return (
      _t.forEach(function (o) {
        typeof o.eventProperties === 'function' &&
                    gt(i, o.eventProperties.call(e[o.pluginName], t))
      }),
      i
    )
  },
  modifyOption: function (t, e, i) {
    let o
    return (
      _t.forEach(function (s) {
        t[s.pluginName] &&
                    s.optionListeners &&
                    typeof s.optionListeners[e] === 'function' &&
                    (o = s.optionListeners[e].call(t[s.pluginName], i))
      }),
      o
    )
  }
}
function Bn (n) {
  let t = n.sortable
  const e = n.rootEl
  const i = n.name
  const o = n.targetEl
  const s = n.cloneEl
  const r = n.toEl
  const a = n.fromEl
  const l = n.oldIndex
  const c = n.newIndex
  const f = n.oldDraggableIndex
  const d = n.newDraggableIndex
  const p = n.originalEvent
  const u = n.putSortable
  const g = n.extraEventProperties
  if (((t = t || (e && e[j])), !!t)) {
    let m
    const S = t.options
    const O = 'on' + i.charAt(0).toUpperCase() + i.substr(1);
    (window.CustomEvent && !mt && !Zt
      ? (m = new CustomEvent(i, { bubbles: !0, cancelable: !0 }))
      : ((m = document.createEvent('Event')), m.initEvent(i, !0, !0)),
    (m.to = r || e),
    (m.from = a || e),
    (m.item = o || e),
    (m.clone = s),
    (m.oldIndex = l),
    (m.newIndex = c),
    (m.oldDraggableIndex = f),
    (m.newDraggableIndex = d),
    (m.originalEvent = p),
    (m.pullMode = u ? u.lastPutMode : void 0))
    const w = ft(ft({}, g), te.getEventProperties(i, t))
    for (const D in w) m[D] = w[D];
    (e && e.dispatchEvent(m), S[O] && S[O].call(t, m))
  }
}
const Fn = ['evt']
const G = function (t, e) {
  const i =
        arguments.length > 2 && arguments[2] !== void 0 ? arguments[2] : {}
  const o = i.evt
  const s = In(i, Fn)
  te.pluginEvent.bind(v)(
    t,
    e,
    ft(
      {
        dragEl: h,
        parentEl: M,
        ghostEl: y,
        rootEl: I,
        nextEl: Lt,
        lastDownEl: pe,
        cloneEl: _,
        cloneHidden: St,
        dragStarted: Kt,
        putSortable: W,
        activeSortable: v.active,
        originalEvent: o,
        oldIndex: Pt,
        oldDraggableIndex: qt,
        newIndex: tt,
        newDraggableIndex: wt,
        hideGhostForTarget: Xi,
        unhideGhostForTarget: Ki,
        cloneNowHidden: function () {
          St = !0
        },
        cloneNowShown: function () {
          St = !1
        },
        dispatchSortableEvent: function (a) {
          K({ sortable: e, name: a, originalEvent: o })
        }
      },
      s
    )
  )
}
function K (n) {
  Bn(
    ft(
      {
        putSortable: W,
        cloneEl: _,
        targetEl: h,
        rootEl: I,
        oldIndex: Pt,
        oldDraggableIndex: qt,
        newIndex: tt,
        newDraggableIndex: wt
      },
      n
    )
  )
}
let h
let M
let y
let I
let Lt
let pe
let _
let St
let Pt
let tt
let qt
let wt
let ce
let W
let Mt = !1
let ye = !1
const we = []
let At
let at
let Me
let Pe
let Ii
let Ti
let Kt
let Rt
let Jt
let Qt = !1
let de = !1
let ge
let $
let Ne = []
let Ve = !1
const Se = []
const Ee = typeof document < 'u'
const fe = Xe
const _i = Zt || mt ? 'cssFloat' : 'float'
const Hn = Ee && !Mi && !Xe && 'draggable' in document.createElement('div')
const Wi = (function () {
  if (Ee) {
    if (mt) return !1
    const n = document.createElement('x')
    return (
      (n.style.cssText = 'pointer-events:auto'),
      n.style.pointerEvents === 'auto'
    )
  }
})()
const zi = function (t, e) {
  const i = b(t)
  const o =
        parseInt(i.width) -
        parseInt(i.paddingLeft) -
        parseInt(i.paddingRight) -
        parseInt(i.borderLeftWidth) -
        parseInt(i.borderRightWidth)
  const s = kt(t, 0, e)
  const r = kt(t, 1, e)
  const a = s && b(s)
  const l = r && b(r)
  const c =
        a && parseInt(a.marginLeft) + parseInt(a.marginRight) + k(s).width
  const f =
        l && parseInt(l.marginLeft) + parseInt(l.marginRight) + k(r).width
  if (i.display === 'flex') {
    return i.flexDirection === 'column' ||
            i.flexDirection === 'column-reverse'
      ? 'vertical'
      : 'horizontal'
  }
  if (i.display === 'grid') {
    return i.gridTemplateColumns.split(' ').length <= 1
      ? 'vertical'
      : 'horizontal'
  }
  if (s && a.float && a.float !== 'none') {
    const d = a.float === 'left' ? 'left' : 'right'
    return r && (l.clear === 'both' || l.clear === d)
      ? 'vertical'
      : 'horizontal'
  }
  return s &&
        (a.display === 'block' ||
            a.display === 'flex' ||
            a.display === 'table' ||
            a.display === 'grid' ||
            (c >= o && i[_i] === 'none') ||
            (r && i[_i] === 'none' && c + f > o))
    ? 'vertical'
    : 'horizontal'
}
const Vn = function (t, e, i) {
  const o = i ? t.left : t.top
  const s = i ? t.right : t.bottom
  const r = i ? t.width : t.height
  const a = i ? e.left : e.top
  const l = i ? e.right : e.bottom
  const c = i ? e.width : e.height
  return o === a || s === l || o + r / 2 === a + c / 2
}
const Wn = function (t, e) {
  let i
  return (
    we.some(function (o) {
      const s = o[j].options.emptyInsertThreshold
      if (!(!s || Ke(o))) {
        const r = k(o)
        const a = t >= r.left - s && t <= r.right + s
        const l = e >= r.top - s && e <= r.bottom + s
        if (a && l) return (i = o)
      }
    }),
    i
  )
}
const $i = function (t) {
  function e (s, r) {
    return function (a, l, c, f) {
      const d =
                a.options.group.name &&
                l.options.group.name &&
                a.options.group.name === l.options.group.name
      if (s == null && (r || d)) return !0
      if (s == null || s === !1) return !1
      if (r && s === 'clone') return s
      if (typeof s === 'function') {
        return e(s(a, l, c, f), r)(a, l, c, f)
      }
      const p = (r ? a : l).options.group.name
      return (
        s === !0 ||
                (typeof s === 'string' && s === p) ||
                (s.join && s.indexOf(p) > -1)
      )
    }
  }
  const i = {}
  let o = t.group;
  ((!o || ue(o) != 'object') && (o = { name: o }),
  (i.name = o.name),
  (i.checkPull = e(o.pull, !0)),
  (i.checkPut = e(o.put)),
  (i.revertClone = o.revertClone),
  (t.group = i))
}
var Xi = function () {
  !Wi && y && b(y, 'display', 'none')
}
var Ki = function () {
  !Wi && y && b(y, 'display', '')
}
Ee &&
    !Mi &&
    document.addEventListener(
      'click',
      function (n) {
        if (ye) {
          return (
            n.preventDefault(),
            n.stopPropagation && n.stopPropagation(),
            n.stopImmediatePropagation && n.stopImmediatePropagation(),
            (ye = !1),
            !1
          )
        }
      },
      !0
    )
const Ct = function (t) {
  if (h) {
    t = t.touches ? t.touches[0] : t
    const e = Wn(t.clientX, t.clientY)
    if (e) {
      const i = {}
      for (const o in t) t.hasOwnProperty(o) && (i[o] = t[o]);
      ((i.target = i.rootEl = e),
      (i.preventDefault = void 0),
      (i.stopPropagation = void 0),
      e[j]._onDragOver(i))
    }
  }
}
const zn = function (t) {
  h && h.parentNode[j]._isOutsideThisEl(t.target)
}
function v (n, t) {
  if (!(n && n.nodeType && n.nodeType === 1)) {
    throw 'Sortable: `el` must be an HTMLElement, not '.concat(
      {}.toString.call(n)
    )
  }
  ((this.el = n), (this.options = t = gt({}, t)), (n[j] = this))
  const e = {
    group: null,
    sort: !0,
    disabled: !1,
    store: null,
    handle: null,
    draggable: /^[uo]l$/i.test(n.nodeName) ? '>li' : '>*',
    swapThreshold: 1,
    invertSwap: !1,
    invertedSwapThreshold: null,
    removeCloneOnHide: !0,
    direction: function () {
      return zi(n, this.options)
    },
    ghostClass: 'sortable-ghost',
    chosenClass: 'sortable-chosen',
    dragClass: 'sortable-drag',
    ignore: 'a, img',
    filter: null,
    preventOnFilter: !0,
    animation: 0,
    easing: null,
    setData: function (r, a) {
      r.setData('Text', a.textContent)
    },
    dropBubble: !1,
    dragoverBubble: !1,
    dataIdAttr: 'data-id',
    delay: 0,
    delayOnTouchOnly: !1,
    touchStartThreshold:
            (Number.parseInt ? Number : window).parseInt(
              window.devicePixelRatio,
              10
            ) || 1,
    forceFallback: !1,
    fallbackClass: 'sortable-fallback',
    fallbackOnBody: !1,
    fallbackTolerance: 0,
    fallbackOffset: { x: 0, y: 0 },
    supportPointer:
            v.supportPointer !== !1 && 'PointerEvent' in window && (!Gt || Xe),
    emptyInsertThreshold: 5
  }
  te.initializePlugins(this, n, e)
  for (const i in e) !(i in t) && (t[i] = e[i])
  $i(t)
  for (const o in this) {
    o.charAt(0) === '_' &&
            typeof this[o] === 'function' &&
            (this[o] = this[o].bind(this))
  }
  ((this.nativeDraggable = t.forceFallback ? !1 : Hn),
  this.nativeDraggable && (this.options.touchStartThreshold = 1),
  t.supportPointer
    ? E(n, 'pointerdown', this._onTapStart)
    : (E(n, 'mousedown', this._onTapStart),
      E(n, 'touchstart', this._onTapStart)),
  this.nativeDraggable &&
            (E(n, 'dragover', this), E(n, 'dragenter', this)),
  we.push(this.el),
  t.store && t.store.get && this.sort(t.store.get(this) || []),
  gt(this, Pn()))
}
v.prototype = {
  constructor: v,
  _isOutsideThisEl: function (t) {
    !this.el.contains(t) && t !== this.el && (Rt = null)
  },
  _getDirection: function (t, e) {
    return typeof this.options.direction === 'function'
      ? this.options.direction.call(this, t, e, h)
      : this.options.direction
  },
  _onTapStart: function (t) {
    if (t.cancelable) {
      const e = this
      const i = this.el
      const o = this.options
      const s = o.preventOnFilter
      const r = t.type
      const a =
                (t.touches && t.touches[0]) ||
                (t.pointerType && t.pointerType === 'touch' && t)
      let l = (a || t).target
      const c =
                (t.target.shadowRoot &&
                    ((t.path && t.path[0]) ||
                        (t.composedPath && t.composedPath()[0]))) ||
                l
      let f = o.filter
      if (
        (qn(i),
        !h &&
                    !(
                      (/mousedown|pointerdown/.test(r) && t.button !== 0) ||
                        o.disabled
                    ) &&
                    !c.isContentEditable &&
                    !(
                      !this.nativeDraggable &&
                        Gt &&
                        l &&
                        l.tagName.toUpperCase() === 'SELECT'
                    ) &&
                    ((l = lt(l, o.draggable, i, !1)),
                    !(l && l.animated) && pe !== l))
      ) {
        if (
          ((Pt = ot(l)),
          (qt = ot(l, o.draggable)),
          typeof f === 'function')
        ) {
          if (f.call(this, t, l, this)) {
            (K({
              sortable: e,
              rootEl: c,
              name: 'filter',
              targetEl: l,
              toEl: i,
              fromEl: i
            }),
            G('filter', e, { evt: t }),
            s && t.preventDefault())
            return
          }
        } else if (
          f &&
                    ((f = f.split(',').some(function (d) {
                      if (((d = lt(c, d.trim(), i, !1)), d)) {
                        return (
                          K({
                            sortable: e,
                            rootEl: d,
                            name: 'filter',
                            targetEl: l,
                            fromEl: i,
                            toEl: i
                          }),
                          G('filter', e, { evt: t }),
                          !0
                        )
                      }
                    })),
                    f)
        ) {
          s && t.preventDefault()
          return
        }
        (o.handle && !lt(c, o.handle, i, !1)) ||
                    this._prepareDragStart(t, a, l)
      }
    }
  },
  _prepareDragStart: function (t, e, i) {
    const o = this
    const s = o.el
    const r = o.options
    const a = s.ownerDocument
    let l
    if (i && !h && i.parentNode === s) {
      const c = k(i)
      if (
        ((I = s),
        (h = i),
        (M = h.parentNode),
        (Lt = h.nextSibling),
        (pe = i),
        (ce = r.group),
        (v.dragged = h),
        (At = {
          target: h,
          clientX: (e || t).clientX,
          clientY: (e || t).clientY
        }),
        (Ii = At.clientX - c.left),
        (Ti = At.clientY - c.top),
        (this._lastX = (e || t).clientX),
        (this._lastY = (e || t).clientY),
        (h.style['will-change'] = 'all'),
        (l = function () {
          if ((G('delayEnded', o, { evt: t }), v.eventCanceled)) {
            o._onDrop()
            return
          }
          (o._disableDelayedDragEvents(),
          !Di && o.nativeDraggable && (h.draggable = !0),
          o._triggerDragStart(t, e),
          K({ sortable: o, name: 'choose', originalEvent: t }),
          Z(h, r.chosenClass, !0))
        }),
        r.ignore.split(',').forEach(function (f) {
          ki(h, f.trim(), ke)
        }),
        E(a, 'dragover', Ct),
        E(a, 'mousemove', Ct),
        E(a, 'touchmove', Ct),
        r.supportPointer
          ? (E(a, 'pointerup', o._onDrop),
            !this.nativeDraggable && E(a, 'pointercancel', o._onDrop))
          : (E(a, 'mouseup', o._onDrop),
            E(a, 'touchend', o._onDrop),
            E(a, 'touchcancel', o._onDrop)),
        Di &&
                    this.nativeDraggable &&
                    ((this.options.touchStartThreshold = 4),
                    (h.draggable = !0)),
        G('delayStart', this, { evt: t }),
        r.delay &&
                    (!r.delayOnTouchOnly || e) &&
                    (!this.nativeDraggable || !(Zt || mt)))
      ) {
        if (v.eventCanceled) {
          this._onDrop()
          return
        }
        (r.supportPointer
          ? (E(a, 'pointerup', o._disableDelayedDrag),
            E(a, 'pointercancel', o._disableDelayedDrag))
          : (E(a, 'mouseup', o._disableDelayedDrag),
            E(a, 'touchend', o._disableDelayedDrag),
            E(a, 'touchcancel', o._disableDelayedDrag)),
        E(a, 'mousemove', o._delayedDragTouchMoveHandler),
        E(a, 'touchmove', o._delayedDragTouchMoveHandler),
        r.supportPointer &&
                        E(a, 'pointermove', o._delayedDragTouchMoveHandler),
        (o._dragStartTimer = setTimeout(l, r.delay)))
      } else l()
    }
  },
  _delayedDragTouchMoveHandler: function (t) {
    const e = t.touches ? t.touches[0] : t
    Math.max(
      Math.abs(e.clientX - this._lastX),
      Math.abs(e.clientY - this._lastY)
    ) >=
            Math.floor(
              this.options.touchStartThreshold /
                    ((this.nativeDraggable && window.devicePixelRatio) || 1)
            ) && this._disableDelayedDrag()
  },
  _disableDelayedDrag: function () {
    (h && ke(h),
    clearTimeout(this._dragStartTimer),
    this._disableDelayedDragEvents())
  },
  _disableDelayedDragEvents: function () {
    const t = this.el.ownerDocument;
    (x(t, 'mouseup', this._disableDelayedDrag),
    x(t, 'touchend', this._disableDelayedDrag),
    x(t, 'touchcancel', this._disableDelayedDrag),
    x(t, 'pointerup', this._disableDelayedDrag),
    x(t, 'pointercancel', this._disableDelayedDrag),
    x(t, 'mousemove', this._delayedDragTouchMoveHandler),
    x(t, 'touchmove', this._delayedDragTouchMoveHandler),
    x(t, 'pointermove', this._delayedDragTouchMoveHandler))
  },
  _triggerDragStart: function (t, e) {
    ((e = e || (t.pointerType == 'touch' && t)),
    !this.nativeDraggable || e
      ? this.options.supportPointer
        ? E(document, 'pointermove', this._onTouchMove)
        : e
          ? E(document, 'touchmove', this._onTouchMove)
          : E(document, 'mousemove', this._onTouchMove)
      : (E(h, 'dragend', this),
        E(I, 'dragstart', this._onDragStart)))
    try {
      document.selection
        ? me(function () {
          document.selection.empty()
        })
        : window.getSelection().removeAllRanges()
    } catch {}
  },
  _dragStarted: function (t, e) {
    if (((Mt = !1), I && h)) {
      (G('dragStarted', this, { evt: e }),
      this.nativeDraggable && E(document, 'dragover', zn))
      const i = this.options;
      (!t && Z(h, i.dragClass, !1),
      Z(h, i.ghostClass, !0),
      (v.active = this),
      t && this._appendGhost(),
      K({ sortable: this, name: 'start', originalEvent: e }))
    } else this._nulling()
  },
  _emulateDragOver: function () {
    if (at) {
      ((this._lastX = at.clientX), (this._lastY = at.clientY), Xi())
      for (
        var t = document.elementFromPoint(at.clientX, at.clientY),
          e = t;
        t &&
                t.shadowRoot &&
                ((t = t.shadowRoot.elementFromPoint(at.clientX, at.clientY)),
                t !== e);
      ) {
        e = t
      }
      if ((h.parentNode[j]._isOutsideThisEl(t), e)) {
        do {
          if (e[j]) {
            let i = void 0
            if (
              ((i = e[j]._onDragOver({
                clientX: at.clientX,
                clientY: at.clientY,
                target: t,
                rootEl: e
              })),
              i && !this.options.dragoverBubble)
            ) {
              break
            }
          }
          t = e
        } while ((e = Ni(e)))
      }
      Ki()
    }
  },
  _onTouchMove: function (t) {
    if (At) {
      const e = this.options
      const i = e.fallbackTolerance
      const o = e.fallbackOffset
      const s = t.touches ? t.touches[0] : t
      let r = y && Nt(y, !0)
      const a = y && r && r.a
      const l = y && r && r.d
      const c = fe && $ && Li($)
      const f =
                (s.clientX - At.clientX + o.x) / (a || 1) +
                (c ? c[0] - Ne[0] : 0) / (a || 1)
      const d =
                (s.clientY - At.clientY + o.y) / (l || 1) +
                (c ? c[1] - Ne[1] : 0) / (l || 1)
      if (!v.active && !Mt) {
        if (
          i &&
                    Math.max(
                      Math.abs(s.clientX - this._lastX),
                      Math.abs(s.clientY - this._lastY)
                    ) < i
        ) {
          return
        }
        this._onDragStart(t, !0)
      }
      if (y) {
        r
          ? ((r.e += f - (Me || 0)), (r.f += d - (Pe || 0)))
          : (r = { a: 1, b: 0, c: 0, d: 1, e: f, f: d })
        const p = 'matrix('
          .concat(r.a, ',')
          .concat(r.b, ',')
          .concat(r.c, ',')
          .concat(r.d, ',')
          .concat(r.e, ',')
          .concat(r.f, ')');
        (b(y, 'webkitTransform', p),
        b(y, 'mozTransform', p),
        b(y, 'msTransform', p),
        b(y, 'transform', p),
        (Me = f),
        (Pe = d),
        (at = s))
      }
      t.cancelable && t.preventDefault()
    }
  },
  _appendGhost: function () {
    if (!y) {
      const t = this.options.fallbackOnBody ? document.body : I
      const e = k(h, !0, fe, !0, t)
      const i = this.options
      if (fe) {
        for (
          $ = t;
          b($, 'position') === 'static' &&
                    b($, 'transform') === 'none' &&
                    $ !== document;
        ) {
          $ = $.parentNode
        }
        ($ !== document.body && $ !== document.documentElement
          ? ($ === document && ($ = dt()),
            (e.top += $.scrollTop),
            (e.left += $.scrollLeft))
          : ($ = dt()),
        (Ne = Li($)))
      }
      ((y = h.cloneNode(!0)),
      Z(y, i.ghostClass, !1),
      Z(y, i.fallbackClass, !0),
      Z(y, i.dragClass, !0),
      b(y, 'transition', ''),
      b(y, 'transform', ''),
      b(y, 'box-sizing', 'border-box'),
      b(y, 'margin', 0),
      b(y, 'top', e.top),
      b(y, 'left', e.left),
      b(y, 'width', e.width),
      b(y, 'height', e.height),
      b(y, 'opacity', '0.8'),
      b(y, 'position', fe ? 'absolute' : 'fixed'),
      b(y, 'zIndex', '100000'),
      b(y, 'pointerEvents', 'none'),
      (v.ghost = y),
      t.appendChild(y),
      b(
        y,
        'transform-origin',
        (Ii / parseInt(y.style.width)) * 100 +
                        '% ' +
                        (Ti / parseInt(y.style.height)) * 100 +
                        '%'
      ))
    }
  },
  _onDragStart: function (t, e) {
    const i = this
    const o = t.dataTransfer
    const s = i.options
    if ((G('dragStart', this, { evt: t }), v.eventCanceled)) {
      this._onDrop()
      return
    }
    (G('setupClone', this),
    v.eventCanceled ||
                ((_ = Hi(h)),
                _.removeAttribute('id'),
                (_.draggable = !1),
                (_.style['will-change'] = ''),
                this._hideClone(),
                Z(_, this.options.chosenClass, !1),
                (v.clone = _)),
    (i.cloneId = me(function () {
      (G('clone', i),
      !v.eventCanceled &&
                        (i.options.removeCloneOnHide || I.insertBefore(_, h),
                        i._hideClone(),
                        K({ sortable: i, name: 'clone' })))
    })),
    !e && Z(h, s.dragClass, !0),
    e
      ? ((ye = !0), (i._loopId = setInterval(i._emulateDragOver, 50)))
      : (x(document, 'mouseup', i._onDrop),
        x(document, 'touchend', i._onDrop),
        x(document, 'touchcancel', i._onDrop),
        o &&
                      ((o.effectAllowed = 'move'),
                      s.setData && s.setData.call(i, o, h)),
        E(document, 'drop', i),
        b(h, 'transform', 'translateZ(0)')),
    (Mt = !0),
    (i._dragStartId = me(i._dragStarted.bind(i, e, t))),
    E(document, 'selectstart', i),
    (Kt = !0),
    window.getSelection().removeAllRanges(),
    Gt && b(document.body, 'user-select', 'none'))
  },
  _onDragOver: function (t) {
    const e = this.el
    let i = t.target
    let o
    let s
    let r
    const a = this.options
    const l = a.group
    const c = v.active
    const f = ce === l
    const d = a.sort
    const p = W || c
    let u
    const g = this
    let m = !1
    if (Ve) return
    function S (T, et) {
      G(
        T,
        g,
        ft(
          {
            evt: t,
            isOwner: f,
            axis: u ? 'vertical' : 'horizontal',
            revert: r,
            dragRect: o,
            targetRect: s,
            canSort: d,
            fromSortable: p,
            target: i,
            completed: w,
            onMove: function (Ge, Gi) {
              return he(I, e, h, o, Ge, k(Ge), t, Gi)
            },
            changed: D
          },
          et
        )
      )
    }
    function O () {
      (S('dragOverAnimationCapture'),
      g.captureAnimationState(),
      g !== p && p.captureAnimationState())
    }
    function w (T) {
      return (
        S('dragOverCompleted', { insertion: T }),
        T &&
                    (f ? c._hideClone() : c._showClone(g),
                    g !== p &&
                        (Z(
                          h,
                          W ? W.options.ghostClass : c.options.ghostClass,
                          !1
                        ),
                        Z(h, a.ghostClass, !0)),
                    W !== g && g !== v.active
                      ? (W = g)
                      : g === v.active && W && (W = null),
                    p === g && (g._ignoreWhileAnimating = i),
                    g.animateAll(function () {
                      (S('dragOverAnimationComplete'),
                      (g._ignoreWhileAnimating = null))
                    }),
                    g !== p &&
                        (p.animateAll(), (p._ignoreWhileAnimating = null))),
        ((i === h && !h.animated) || (i === e && !i.animated)) &&
                    (Rt = null),
        !a.dragoverBubble &&
                    !t.rootEl &&
                    i !== document &&
                    (h.parentNode[j]._isOutsideThisEl(t.target), !T && Ct(t)),
        !a.dragoverBubble && t.stopPropagation && t.stopPropagation(),
        (m = !0)
      )
    }
    function D () {
      ((tt = ot(h)),
      (wt = ot(h, a.draggable)),
      K({
        sortable: g,
        name: 'change',
        toEl: e,
        newIndex: tt,
        newDraggableIndex: wt,
        originalEvent: t
      }))
    }
    if (
      (t.preventDefault !== void 0 && t.cancelable && t.preventDefault(),
      (i = lt(i, a.draggable, e, !0)),
      S('dragOver'),
      v.eventCanceled)
    ) {
      return m
    }
    if (
      h.contains(t.target) ||
            (i.animated && i.animatingX && i.animatingY) ||
            g._ignoreWhileAnimating === i
    ) {
      return w(!1)
    }
    if (
      ((ye = !1),
      c &&
                !a.disabled &&
                (f
                  ? d || (r = M !== I)
                  : W === this ||
                      ((this.lastPutMode = ce.checkPull(this, c, h, t)) &&
                          l.checkPut(this, c, h, t))))
    ) {
      if (
        ((u = this._getDirection(t, i) === 'vertical'),
        (o = k(h)),
        S('dragOverValid'),
        v.eventCanceled)
      ) {
        return m
      }
      if (r) {
        return (
          (M = I),
          O(),
          this._hideClone(),
          S('revert'),
          v.eventCanceled ||
                        (Lt ? I.insertBefore(h, Lt) : I.appendChild(h)),
          w(!0)
        )
      }
      const A = Ke(e, a.draggable)
      if (!A || (Yn(t, u, this) && !A.animated)) {
        if (A === h) return w(!1)
        if (
          (A && e === t.target && (i = A),
          i && (s = k(i)),
          he(I, e, h, o, i, s, t, !!i) !== !1)
        ) {
          return (
            O(),
            A && A.nextSibling
              ? e.insertBefore(h, A.nextSibling)
              : e.appendChild(h),
            (M = e),
            D(),
            w(!0)
          )
        }
      } else if (A && Kn(t, u, this)) {
        const C = kt(e, 0, a, !0)
        if (C === h) return w(!1)
        if (((i = C), (s = k(i)), he(I, e, h, o, i, s, t, !1) !== !1)) {
          return (O(), e.insertBefore(h, C), (M = e), D(), w(!0))
        }
      } else if (i.parentNode === e) {
        s = k(i)
        let H = 0
        let q
        const J = h.parentNode !== e
        const R = !Vn(
          (h.animated && h.toRect) || o,
          (i.animated && i.toRect) || s,
          u
        )
        const z = u ? 'top' : 'left'
        const L = Ci(i, 'top', 'top') || Ci(h, 'top', 'top')
        const Y = L ? L.scrollTop : void 0;
        (Rt !== i &&
                    ((q = s[z]), (Qt = !1), (de = (!R && a.invertSwap) || J)),
        (H = Un(
          t,
          i,
          s,
          u,
          R ? 1 : a.swapThreshold,
          a.invertedSwapThreshold == null
            ? a.swapThreshold
            : a.invertedSwapThreshold,
          de,
          Rt === i
        )))
        let B
        if (H !== 0) {
          let F = ot(h)
          do ((F -= H), (B = M.children[F]))
          while (B && (b(B, 'display') === 'none' || B === y))
        }
        if (H === 0 || B === i) return w(!1);
        ((Rt = i), (Jt = H))
        const Q = i.nextElementSibling
        let V = !1
        V = H === 1
        const X = he(I, e, h, o, i, s, t, V)
        if (X !== !1) {
          return (
            (X === 1 || X === -1) && (V = X === 1),
            (Ve = !0),
            setTimeout(Xn, 30),
            O(),
            V && !Q
              ? e.appendChild(h)
              : i.parentNode.insertBefore(h, V ? Q : i),
            L && Fi(L, 0, Y - L.scrollTop),
            (M = h.parentNode),
            q !== void 0 && !de && (ge = Math.abs(q - k(i)[z])),
            D(),
            w(!0)
          )
        }
      }
      if (e.contains(h)) return w(!1)
    }
    return !1
  },
  _ignoreWhileAnimating: null,
  _offMoveEvents: function () {
    (x(document, 'mousemove', this._onTouchMove),
    x(document, 'touchmove', this._onTouchMove),
    x(document, 'pointermove', this._onTouchMove),
    x(document, 'dragover', Ct),
    x(document, 'mousemove', Ct),
    x(document, 'touchmove', Ct))
  },
  _offUpEvents: function () {
    const t = this.el.ownerDocument;
    (x(t, 'mouseup', this._onDrop),
    x(t, 'touchend', this._onDrop),
    x(t, 'pointerup', this._onDrop),
    x(t, 'pointercancel', this._onDrop),
    x(t, 'touchcancel', this._onDrop),
    x(document, 'selectstart', this))
  },
  _onDrop: function (t) {
    const e = this.el
    const i = this.options
    if (
      ((tt = ot(h)),
      (wt = ot(h, i.draggable)),
      G('drop', this, { evt: t }),
      (M = h && h.parentNode),
      (tt = ot(h)),
      (wt = ot(h, i.draggable)),
      v.eventCanceled)
    ) {
      this._nulling()
      return
    }
    ((Mt = !1),
    (de = !1),
    (Qt = !1),
    clearInterval(this._loopId),
    clearTimeout(this._dragStartTimer),
    We(this.cloneId),
    We(this._dragStartId),
    this.nativeDraggable &&
                (x(document, 'drop', this),
                x(e, 'dragstart', this._onDragStart)),
    this._offMoveEvents(),
    this._offUpEvents(),
    Gt && b(document.body, 'user-select', ''),
    b(h, 'transform', ''),
    t &&
                (Kt &&
                    (t.cancelable && t.preventDefault(),
                    !i.dropBubble && t.stopPropagation()),
                y && y.parentNode && y.parentNode.removeChild(y),
                (I === M || (W && W.lastPutMode !== 'clone')) &&
                    _ &&
                    _.parentNode &&
                    _.parentNode.removeChild(_),
                h &&
                    (this.nativeDraggable && x(h, 'dragend', this),
                    ke(h),
                    (h.style['will-change'] = ''),
                    Kt &&
                        !Mt &&
                        Z(
                          h,
                          W ? W.options.ghostClass : this.options.ghostClass,
                          !1
                        ),
                    Z(h, this.options.chosenClass, !1),
                    K({
                      sortable: this,
                      name: 'unchoose',
                      toEl: M,
                      newIndex: null,
                      newDraggableIndex: null,
                      originalEvent: t
                    }),
                    I !== M
                      ? (tt >= 0 &&
                              (K({
                                rootEl: M,
                                name: 'add',
                                toEl: M,
                                fromEl: I,
                                originalEvent: t
                              }),
                              K({
                                sortable: this,
                                name: 'remove',
                                toEl: M,
                                originalEvent: t
                              }),
                              K({
                                rootEl: M,
                                name: 'sort',
                                toEl: M,
                                fromEl: I,
                                originalEvent: t
                              }),
                              K({
                                sortable: this,
                                name: 'sort',
                                toEl: M,
                                originalEvent: t
                              })),
                        W && W.save())
                      : tt !== Pt &&
                          tt >= 0 &&
                          (K({
                            sortable: this,
                            name: 'update',
                            toEl: M,
                            originalEvent: t
                          }),
                          K({
                            sortable: this,
                            name: 'sort',
                            toEl: M,
                            originalEvent: t
                          })),
                    v.active &&
                        ((tt == null || tt === -1) && ((tt = Pt), (wt = qt)),
                        K({
                          sortable: this,
                          name: 'end',
                          toEl: M,
                          originalEvent: t
                        }),
                        this.save()))),
    this._nulling())
  },
  _nulling: function () {
    (G('nulling', this),
    (I =
                h =
                M =
                y =
                Lt =
                _ =
                pe =
                St =
                At =
                at =
                Kt =
                tt =
                wt =
                Pt =
                qt =
                Rt =
                Jt =
                W =
                ce =
                v.dragged =
                v.ghost =
                v.clone =
                v.active =
                    null),
    Se.forEach(function (t) {
      t.checked = !0
    }),
    (Se.length = Me = Pe = 0))
  },
  handleEvent: function (t) {
    switch (t.type) {
      case 'drop':
      case 'dragend':
        this._onDrop(t)
        break
      case 'dragenter':
      case 'dragover':
        h && (this._onDragOver(t), $n(t))
        break
      case 'selectstart':
        t.preventDefault()
        break
    }
  },
  toArray: function () {
    for (
      var t = [],
        e,
        i = this.el.children,
        o = 0,
        s = i.length,
        r = this.options;
      o < s;
      o++
    ) {
      ((e = i[o]),
      lt(e, r.draggable, this.el, !1) &&
                    t.push(e.getAttribute(r.dataIdAttr) || jn(e)))
    }
    return t
  },
  sort: function (t, e) {
    const i = {}
    const o = this.el;
    (this.toArray().forEach(function (s, r) {
      const a = o.children[r]
      lt(a, this.options.draggable, o, !1) && (i[s] = a)
    }, this),
    e && this.captureAnimationState(),
    t.forEach(function (s) {
      i[s] && (o.removeChild(i[s]), o.appendChild(i[s]))
    }),
    e && this.animateAll())
  },
  save: function () {
    const t = this.options.store
    t && t.set && t.set(this)
  },
  closest: function (t, e) {
    return lt(t, e || this.options.draggable, this.el, !1)
  },
  option: function (t, e) {
    const i = this.options
    if (e === void 0) return i[t]
    const o = te.modifyOption(this, t, e);
    (typeof o < 'u' ? (i[t] = o) : (i[t] = e), t === 'group' && $i(i))
  },
  destroy: function () {
    G('destroy', this)
    let t = this.el;
    ((t[j] = null),
    x(t, 'mousedown', this._onTapStart),
    x(t, 'touchstart', this._onTapStart),
    x(t, 'pointerdown', this._onTapStart),
    this.nativeDraggable &&
                (x(t, 'dragover', this), x(t, 'dragenter', this)),
    Array.prototype.forEach.call(
      t.querySelectorAll('[draggable]'),
      function (e) {
        e.removeAttribute('draggable')
      }
    ),
    this._onDrop(),
    this._disableDelayedDragEvents(),
    we.splice(we.indexOf(this.el), 1),
    (this.el = t = null))
  },
  _hideClone: function () {
    if (!St) {
      if ((G('hideClone', this), v.eventCanceled)) return;
      (b(_, 'display', 'none'),
      this.options.removeCloneOnHide &&
                    _.parentNode &&
                    _.parentNode.removeChild(_),
      (St = !0))
    }
  },
  _showClone: function (t) {
    if (t.lastPutMode !== 'clone') {
      this._hideClone()
      return
    }
    if (St) {
      if ((G('showClone', this), v.eventCanceled)) return;
      (h.parentNode == I && !this.options.group.revertClone
        ? I.insertBefore(_, h)
        : Lt
          ? I.insertBefore(_, Lt)
          : I.appendChild(_),
      this.options.group.revertClone && this.animate(h, _),
      b(_, 'display', ''),
      (St = !1))
    }
  }
}
function $n (n) {
  (n.dataTransfer && (n.dataTransfer.dropEffect = 'move'),
  n.cancelable && n.preventDefault())
}
function he (n, t, e, i, o, s, r, a) {
  let l
  const c = n[j]
  const f = c.options.onMove
  let d
  return (
    window.CustomEvent && !mt && !Zt
      ? (l = new CustomEvent('move', { bubbles: !0, cancelable: !0 }))
      : ((l = document.createEvent('Event')),
        l.initEvent('move', !0, !0)),
    (l.to = t),
    (l.from = n),
    (l.dragged = e),
    (l.draggedRect = i),
    (l.related = o || t),
    (l.relatedRect = s || k(t)),
    (l.willInsertAfter = a),
    (l.originalEvent = r),
    n.dispatchEvent(l),
    f && (d = f.call(c, l, r)),
    d
  )
}
function ke (n) {
  n.draggable = !1
}
function Xn () {
  Ve = !1
}
function Kn (n, t, e) {
  const i = k(kt(e.el, 0, e.options, !0))
  const o = Vi(e.el, e.options, y)
  const s = 10
  return t
    ? n.clientX < o.left - s || (n.clientY < i.top && n.clientX < i.right)
    : n.clientY < o.top - s || (n.clientY < i.bottom && n.clientX < i.left)
}
function Yn (n, t, e) {
  const i = k(Ke(e.el, e.options.draggable))
  const o = Vi(e.el, e.options, y)
  const s = 10
  return t
    ? n.clientX > o.right + s ||
              (n.clientY > i.bottom && n.clientX > i.left)
    : n.clientY > o.bottom + s ||
              (n.clientX > i.right && n.clientY > i.top)
}
function Un (n, t, e, i, o, s, r, a) {
  const l = i ? n.clientY : n.clientX
  const c = i ? e.height : e.width
  const f = i ? e.top : e.left
  const d = i ? e.bottom : e.right
  let p = !1
  if (!r) {
    if (a && ge < c * o) {
      if (
        (!Qt &&
                    (Jt === 1 ? l > f + (c * s) / 2 : l < d - (c * s) / 2) &&
                    (Qt = !0),
        Qt)
      ) {
        p = !0
      } else if (Jt === 1 ? l < f + ge : l > d - ge) return -Jt
    } else if (l > f + (c * (1 - o)) / 2 && l < d - (c * (1 - o)) / 2) {
      return Gn(t)
    }
  }
  return (
    (p = p || r),
    p && (l < f + (c * s) / 2 || l > d - (c * s) / 2)
      ? l > f + c / 2
        ? 1
        : -1
      : 0
  )
}
function Gn (n) {
  return ot(h) < ot(n) ? 1 : -1
}
function jn (n) {
  for (
    var t = n.tagName + n.className + n.src + n.href + n.textContent,
      e = t.length,
      i = 0;
    e--;
  ) {
    i += t.charCodeAt(e)
  }
  return i.toString(36)
}
function qn (n) {
  Se.length = 0
  for (let t = n.getElementsByTagName('input'), e = t.length; e--;) {
    const i = t[e]
    i.checked && Se.push(i)
  }
}
function me (n) {
  return setTimeout(n, 0)
}
function We (n) {
  return clearTimeout(n)
}
Ee &&
    E(document, 'touchmove', function (n) {
      (v.active || Mt) && n.cancelable && n.preventDefault()
    })
v.utils = {
  on: E,
  off: x,
  css: b,
  find: ki,
  is: function (t, e) {
    return !!lt(t, e, t, !1)
  },
  extend: Rn,
  throttle: Bi,
  closest: lt,
  toggleClass: Z,
  clone: Hi,
  index: ot,
  nextTick: me,
  cancelNextTick: We,
  detectDirection: zi,
  getChild: kt,
  expando: j
}
v.get = function (n) {
  return n[j]
}
v.mount = function () {
  for (var n = arguments.length, t = new Array(n), e = 0; e < n; e++) {
    t[e] = arguments[e]
  }
  (t[0].constructor === Array && (t = t[0]),
  t.forEach(function (i) {
    if (!i.prototype || !i.prototype.constructor) {
      throw 'Sortable: Mounted plugin must be a constructor function, not '.concat(
        {}.toString.call(i)
      )
    }
    (i.utils && (v.utils = ft(ft({}, v.utils), i.utils)), te.mount(i))
  }))
}
v.create = function (n, t) {
  return new v(n, t)
}
v.version = Tn
let N = []
let Yt
let ze
let $e = !1
let Be
let Fe
let xe
let Ut
function Jn () {
  function n () {
    this.defaults = {
      scroll: !0,
      forceAutoScrollFallback: !1,
      scrollSensitivity: 30,
      scrollSpeed: 10,
      bubbleScroll: !0
    }
    for (const t in this) {
      t.charAt(0) === '_' &&
                typeof this[t] === 'function' &&
                (this[t] = this[t].bind(this))
    }
  }
  return (
    (n.prototype = {
      dragStarted: function (e) {
        const i = e.originalEvent
        this.sortable.nativeDraggable
          ? E(document, 'dragover', this._handleAutoScroll)
          : this.options.supportPointer
            ? E(
              document,
              'pointermove',
              this._handleFallbackAutoScroll
            )
            : i.touches
              ? E(
                document,
                'touchmove',
                this._handleFallbackAutoScroll
              )
              : E(
                document,
                'mousemove',
                this._handleFallbackAutoScroll
              )
      },
      dragOverCompleted: function (e) {
        const i = e.originalEvent
        !this.options.dragOverBubble &&
                    !i.rootEl &&
                    this._handleAutoScroll(i)
      },
      drop: function () {
        (this.sortable.nativeDraggable
          ? x(document, 'dragover', this._handleAutoScroll)
          : (x(
              document,
              'pointermove',
              this._handleFallbackAutoScroll
            ),
            x(document, 'touchmove', this._handleFallbackAutoScroll),
            x(document, 'mousemove', this._handleFallbackAutoScroll)),
        Ri(),
        be(),
        Mn())
      },
      nulling: function () {
        ((xe = ze = Yt = $e = Ut = Be = Fe = null), (N.length = 0))
      },
      _handleFallbackAutoScroll: function (e) {
        this._handleAutoScroll(e, !0)
      },
      _handleAutoScroll: function (e, i) {
        const o = this
        const s = (e.touches ? e.touches[0] : e).clientX
        const r = (e.touches ? e.touches[0] : e).clientY
        const a = document.elementFromPoint(s, r)
        if (
          ((xe = e),
          i || this.options.forceAutoScrollFallback || Zt || mt || Gt)
        ) {
          He(e, this.options, a, i)
          let l = xt(a, !0)
          $e &&
                        (!Ut || s !== Be || r !== Fe) &&
                        (Ut && Ri(),
                        (Ut = setInterval(function () {
                          const c = xt(document.elementFromPoint(s, r), !0);
                          (c !== l && ((l = c), be()),
                          He(e, o.options, c, i))
                        }, 10)),
                        (Be = s),
                        (Fe = r))
        } else {
          if (!this.options.bubbleScroll || xt(a, !0) === dt()) {
            be()
            return
          }
          He(e, this.options, xt(a, !1), !1)
        }
      }
    }),
    gt(n, { pluginName: 'scroll', initializeByDefault: !0 })
  )
}
function be () {
  (N.forEach(function (n) {
    clearInterval(n.pid)
  }),
  (N = []))
}
function Ri () {
  clearInterval(Ut)
}
var He = Bi(function (n, t, e, i) {
  if (t.scroll) {
    const o = (n.touches ? n.touches[0] : n).clientX
    const s = (n.touches ? n.touches[0] : n).clientY
    const r = t.scrollSensitivity
    const a = t.scrollSpeed
    const l = dt()
    let c = !1
    let f
    ze !== e &&
            ((ze = e),
            be(),
            (Yt = t.scroll),
            (f = t.scrollFn),
            Yt === !0 && (Yt = xt(e, !0)))
    let d = 0
    let p = Yt
    do {
      const u = p
      const g = k(u)
      const m = g.top
      const S = g.bottom
      const O = g.left
      const w = g.right
      const D = g.width
      const A = g.height
      let C = void 0
      let H = void 0
      const q = u.scrollWidth
      const J = u.scrollHeight
      const R = b(u)
      const z = u.scrollLeft
      const L = u.scrollTop
      u === l
        ? ((C =
                      D < q &&
                      (R.overflowX === 'auto' ||
                          R.overflowX === 'scroll' ||
                          R.overflowX === 'visible')),
          (H =
                      A < J &&
                      (R.overflowY === 'auto' ||
                          R.overflowY === 'scroll' ||
                          R.overflowY === 'visible')))
        : ((C =
                      D < q &&
                      (R.overflowX === 'auto' || R.overflowX === 'scroll')),
          (H =
                      A < J &&
                      (R.overflowY === 'auto' || R.overflowY === 'scroll')))
      const Y =
                C &&
                (Math.abs(w - o) <= r && z + D < q) -
                    (Math.abs(O - o) <= r && !!z)
      const B =
                H &&
                (Math.abs(S - s) <= r && L + A < J) -
                    (Math.abs(m - s) <= r && !!L)
      if (!N[d]) for (let F = 0; F <= d; F++) N[F] || (N[F] = {});
      ((N[d].vx != Y || N[d].vy != B || N[d].el !== u) &&
                ((N[d].el = u),
                (N[d].vx = Y),
                (N[d].vy = B),
                clearInterval(N[d].pid),
                (Y != 0 || B != 0) &&
                    ((c = !0),
                    (N[d].pid = setInterval(
                      function () {
                        i && this.layer === 0 && v.active._onTouchMove(xe)
                        const Q = N[this.layer].vy
                          ? N[this.layer].vy * a
                          : 0
                        const V = N[this.layer].vx
                          ? N[this.layer].vx * a
                          : 0;
                        (typeof f === 'function' &&
                                f.call(
                                  v.dragged.parentNode[j],
                                  V,
                                  Q,
                                  n,
                                  xe,
                                  N[this.layer].el
                                ) !== 'continue') ||
                                Fi(N[this.layer].el, V, Q)
                      }.bind({ layer: d }),
                      24
                    )))),
      d++)
    } while (t.bubbleScroll && p !== l && (p = xt(p, !1)))
    $e = c
  }
}, 30)
const Yi = function (t) {
  const e = t.originalEvent
  const i = t.putSortable
  const o = t.dragEl
  const s = t.activeSortable
  const r = t.dispatchSortableEvent
  const a = t.hideGhostForTarget
  const l = t.unhideGhostForTarget
  if (e) {
    const c = i || s
    a()
    const f =
            e.changedTouches && e.changedTouches.length
              ? e.changedTouches[0]
              : e
    const d = document.elementFromPoint(f.clientX, f.clientY);
    (l(),
    c &&
                !c.el.contains(d) &&
                (r('spill'), this.onSpill({ dragEl: o, putSortable: i })))
  }
}
function Ye () {}
Ye.prototype = {
  startIndex: null,
  dragStart: function (t) {
    const e = t.oldDraggableIndex
    this.startIndex = e
  },
  onSpill: function (t) {
    const e = t.dragEl
    const i = t.putSortable;
    (this.sortable.captureAnimationState(), i && i.captureAnimationState())
    const o = kt(this.sortable.el, this.startIndex, this.options);
    (o
      ? this.sortable.el.insertBefore(e, o)
      : this.sortable.el.appendChild(e),
    this.sortable.animateAll(),
    i && i.animateAll())
  },
  drop: Yi
}
gt(Ye, { pluginName: 'revertOnSpill' })
function Ue () {}
Ue.prototype = {
  onSpill: function (t) {
    const e = t.dragEl
    const i = t.putSortable
    const o = i || this.sortable;
    (o.captureAnimationState(),
    e.parentNode && e.parentNode.removeChild(e),
    o.animateAll())
  },
  drop: Yi
}
gt(Ue, { pluginName: 'removeOnSpill' })
v.mount(new Jn())
v.mount(Ue, Ye)
const Ui = v
function bt (n) {
  return n == null || n === '' || (typeof n === 'string' && n.trim() === '')
}
function P (n) {
  return !bt(n)
}
const Oe = class {
  constructor ({
    canOptionLabelsWrap: t = !0,
    canSelectPlaceholder: e = !0,
    element: i,
    getOptionLabelUsing: o = null,
    getOptionLabelsUsing: s = null,
    getOptionsUsing: r = null,
    getSearchResultsUsing: a = null,
    hasDynamicOptions: l = !1,
    hasDynamicSearchResults: c = !0,
    hasInitialNoOptionsMessage: f = !1,
    initialOptionLabel: d = null,
    initialOptionLabels: p = null,
    initialState: u = null,
    isAutofocused: g = !1,
    isDisabled: m = !1,
    isHtmlAllowed: S = !1,
    isMultiple: O = !1,
    isReorderable: w = !1,
    isSearchable: D = !1,
    livewireId: A = null,
    loadingMessage: C = 'Loading...',
    maxItems: H = null,
    maxItemsMessage: q = 'Maximum number of items selected',
    noOptionsMessage: J = 'No options available',
    noSearchResultsMessage: R = 'No results found',
    onStateChange: z = () => {},
    options: L,
    optionsLimit: Y = null,
    placeholder: B,
    position: F = null,
    searchableOptionFields: Q = ['label'],
    searchDebounce: V = 1e3,
    searchingMessage: X = 'Searching...',
    searchPrompt: T = 'Search...',
    state: et,
    statePath: ee = null
  }) {
    ((this.canOptionLabelsWrap = t),
    (this.canSelectPlaceholder = e),
    (this.element = i),
    (this.getOptionLabelUsing = o),
    (this.getOptionLabelsUsing = s),
    (this.getOptionsUsing = r),
    (this.getSearchResultsUsing = a),
    (this.hasDynamicOptions = l),
    (this.hasDynamicSearchResults = c),
    (this.hasInitialNoOptionsMessage = f),
    (this.initialOptionLabel = d),
    (this.initialOptionLabels = p),
    (this.initialState = u),
    (this.isAutofocused = g),
    (this.isDisabled = m),
    (this.isHtmlAllowed = S),
    (this.isMultiple = O),
    (this.isReorderable = w),
    (this.isSearchable = D),
    (this.livewireId = A),
    (this.loadingMessage = C),
    (this.maxItems = H),
    (this.maxItemsMessage = q),
    (this.noOptionsMessage = J),
    (this.noSearchResultsMessage = R),
    (this.onStateChange = z),
    (this.options = L),
    (this.optionsLimit = Y),
    (this.originalOptions = JSON.parse(JSON.stringify(L))),
    (this.placeholder = B),
    (this.position = F),
    (this.searchableOptionFields = Array.isArray(Q) ? Q : ['label']),
    (this.searchDebounce = V),
    (this.searchingMessage = X),
    (this.searchPrompt = T),
    (this.state = et),
    (this.statePath = ee),
    (this.activeSearchId = 0),
    (this.labelRepository = {}),
    (this.isOpen = !1),
    (this.selectedIndex = -1),
    (this.searchQuery = ''),
    (this.searchTimeout = null),
    (this.isSearching = !1),
    (this.selectedDisplayVersion = 0),
    this.render(),
    this.setUpEventListeners(),
    this.isAutofocused && this.selectButton.focus())
  }

  populateLabelRepositoryFromOptions (t) {
    if (!(!t || !Array.isArray(t))) {
      for (const e of t) {
        e.options && Array.isArray(e.options)
          ? this.populateLabelRepositoryFromOptions(e.options)
          : e.value !== void 0 &&
                      e.label !== void 0 &&
                      (this.labelRepository[e.value] = e.label)
      }
    }
  }

  render () {
    (this.populateLabelRepositoryFromOptions(this.options),
    (this.container = document.createElement('div')),
    (this.container.className = 'fi-select-input-ctn'),
    this.canOptionLabelsWrap ||
                this.container.classList.add(
                  'fi-select-input-ctn-option-labels-not-wrapped'
                ),
    this.container.setAttribute('aria-haspopup', 'listbox'),
    (this.selectButton = document.createElement('button')),
    (this.selectButton.className = 'fi-select-input-btn'),
    (this.selectButton.type = 'button'),
    this.selectButton.setAttribute('aria-expanded', 'false'),
    (this.selectedDisplay = document.createElement('div')),
    (this.selectedDisplay.className = 'fi-select-input-value-ctn'),
    this.updateSelectedDisplay(),
    this.selectButton.appendChild(this.selectedDisplay),
    (this.dropdown = document.createElement('div')),
    (this.dropdown.className = 'fi-dropdown-panel fi-scrollable'),
    this.dropdown.setAttribute('role', 'listbox'),
    this.dropdown.setAttribute('tabindex', '-1'),
    (this.dropdown.style.display = 'none'),
    (this.dropdownId = `fi-select-input-dropdown-${Math.random().toString(36).substring(2, 11)}`),
    (this.dropdown.id = this.dropdownId),
    this.isMultiple &&
                this.dropdown.setAttribute('aria-multiselectable', 'true'),
    this.isSearchable &&
                ((this.searchContainer = document.createElement('div')),
                (this.searchContainer.className = 'fi-select-input-search-ctn'),
                (this.searchInput = document.createElement('input')),
                (this.searchInput.className = 'fi-input'),
                (this.searchInput.type = 'text'),
                (this.searchInput.placeholder = this.searchPrompt),
                this.searchInput.setAttribute('aria-label', 'Search'),
                this.searchContainer.appendChild(this.searchInput),
                this.dropdown.appendChild(this.searchContainer),
                this.searchInput.addEventListener('input', (t) => {
                  this.isDisabled || this.handleSearch(t)
                }),
                this.searchInput.addEventListener('keydown', (t) => {
                  if (!this.isDisabled) {
                    if (t.key === 'Tab') {
                      t.preventDefault()
                      const e = this.getVisibleOptions()
                      if (e.length === 0) return;
                      (t.shiftKey
                        ? (this.selectedIndex = e.length - 1)
                        : (this.selectedIndex = 0),
                      e.forEach((i) => {
                        i.classList.remove('fi-selected')
                      }),
                      e[this.selectedIndex].classList.add(
                        'fi-selected'
                      ),
                      e[this.selectedIndex].focus())
                    } else if (t.key === 'ArrowDown') {
                      if (
                        (t.preventDefault(),
                        t.stopPropagation(),
                        this.getVisibleOptions().length === 0)
                      ) {
                        return
                      }
                      ((this.selectedIndex = -1),
                      this.searchInput.blur(),
                      this.focusNextOption())
                    } else if (t.key === 'ArrowUp') {
                      (t.preventDefault(), t.stopPropagation())
                      const e = this.getVisibleOptions()
                      if (e.length === 0) return;
                      ((this.selectedIndex = e.length - 1),
                      this.searchInput.blur(),
                      e[this.selectedIndex].classList.add(
                        'fi-selected'
                      ),
                      e[this.selectedIndex].focus(),
                      e[this.selectedIndex].id &&
                                    this.dropdown.setAttribute(
                                      'aria-activedescendant',
                                      e[this.selectedIndex].id
                                    ),
                      this.scrollOptionIntoView(
                        e[this.selectedIndex]
                      ))
                    } else if (t.key === 'Enter') {
                      if (
                        (t.preventDefault(),
                        t.stopPropagation(),
                        this.isSearching)
                      ) {
                        return
                      }
                      const e = this.getVisibleOptions()
                      if (e.length === 0) return
                      const i = e.find((s) => {
                        const r =
                                    s.getAttribute('aria-disabled') === 'true'
                        const a = s.classList.contains('fi-disabled')
                        const l = s.offsetParent === null
                        return !(r || a || l)
                      })
                      if (!i) return
                      const o = i.getAttribute('data-value')
                      if (o === null) return
                      this.selectOption(o)
                    }
                  }
                })),
    (this.optionsList = document.createElement('ul')),
    this.renderOptions(),
    this.container.appendChild(this.selectButton),
    this.container.appendChild(this.dropdown),
    this.element.appendChild(this.container),
    this.applyDisabledState())
  }

  renderOptions () {
    this.optionsList.innerHTML = ''
    let t = 0
    const e = this.options
    let i = 0
    let o = !1;
    (this.options.forEach((a) => {
      a.options && Array.isArray(a.options)
        ? ((i += a.options.length), (o = !0))
        : i++
    }),
    o
      ? (this.optionsList.className = 'fi-select-input-options-ctn')
      : i > 0 && (this.optionsList.className = 'fi-dropdown-list'))
    let s = o ? null : this.optionsList
    let r = 0
    for (const a of e) {
      if (this.optionsLimit && r >= this.optionsLimit) break
      if (a.options && Array.isArray(a.options)) {
        let l = a.options
        if (
          (this.isMultiple &&
                        Array.isArray(this.state) &&
                        this.state.length > 0 &&
                        (l = a.options.filter(
                          (c) => !this.state.includes(c.value)
                        )),
          l.length > 0)
        ) {
          if (this.optionsLimit) {
            const c = this.optionsLimit - r
            c < l.length && (l = l.slice(0, c))
          }
          (this.renderOptionGroup(a.label, l),
          (r += l.length),
          (t += l.length))
        }
      } else {
        if (
          this.isMultiple &&
                    Array.isArray(this.state) &&
                    this.state.includes(a.value)
        ) {
          continue
        }
        !s &&
                    o &&
                    ((s = document.createElement('ul')),
                    (s.className = 'fi-dropdown-list'),
                    this.optionsList.appendChild(s))
        const l = this.createOptionElement(a.value, a);
        (s.appendChild(l), r++, t++)
      }
    }
    t === 0
      ? (this.searchQuery
          ? this.showNoResultsMessage()
          : this.hasInitialNoOptionsMessage || this.hasDynamicOptions
            ? this.showNoOptionsMessage()
            : this.isMultiple &&
                      this.isOpen &&
                      !this.isSearchable &&
                      this.closeDropdown(),
        this.optionsList.parentNode === this.dropdown &&
                  this.dropdown.removeChild(this.optionsList))
      : (this.hideLoadingState(),
        this.optionsList.parentNode !== this.dropdown &&
                  this.dropdown.appendChild(this.optionsList))
  }

  renderOptionGroup (t, e) {
    if (e.length === 0) return
    const i = document.createElement('li')
    i.className = 'fi-select-input-option-group'
    const o = document.createElement('div');
    ((o.className = 'fi-dropdown-header'), (o.textContent = t))
    const s = document.createElement('ul');
    ((s.className = 'fi-dropdown-list'),
    e.forEach((r) => {
      const a = this.createOptionElement(r.value, r)
      s.appendChild(a)
    }),
    i.appendChild(o),
    i.appendChild(s),
    this.optionsList.appendChild(i))
  }

  createOptionElement (t, e) {
    let i = t
    let o = e
    let s = !1
    typeof e === 'object' &&
            e !== null &&
            'label' in e &&
            'value' in e &&
            ((i = e.value), (o = e.label), (s = e.isDisabled || !1))
    const r = document.createElement('li');
    ((r.className = 'fi-dropdown-list-item fi-select-input-option'),
    s && r.classList.add('fi-disabled'))
    const a = `fi-select-input-option-${Math.random().toString(36).substring(2, 11)}`
    if (
      ((r.id = a),
      r.setAttribute('role', 'option'),
      r.setAttribute('data-value', i),
      r.setAttribute('tabindex', '0'),
      s && r.setAttribute('aria-disabled', 'true'),
      this.isHtmlAllowed && typeof o === 'string')
    ) {
      const f = document.createElement('div')
      f.innerHTML = o
      const d = f.textContent || f.innerText || o
      r.setAttribute('aria-label', d)
    }
    const l = this.isMultiple
      ? Array.isArray(this.state) && this.state.includes(i)
      : this.state === i;
    (r.setAttribute('aria-selected', l ? 'true' : 'false'),
    l && r.classList.add('fi-selected'))
    const c = document.createElement('span')
    return (
      this.isHtmlAllowed ? (c.innerHTML = o) : (c.textContent = o),
      r.appendChild(c),
      s ||
                r.addEventListener('click', (f) => {
                  (f.preventDefault(),
                  f.stopPropagation(),
                  this.selectOption(i),
                  this.isMultiple &&
                            (this.isSearchable && this.searchInput
                              ? setTimeout(() => {
                                this.searchInput.focus()
                              }, 0)
                              : setTimeout(() => {
                                r.focus()
                              }, 0)))
                }),
      r
    )
  }

  async updateSelectedDisplay () {
    this.selectedDisplayVersion = this.selectedDisplayVersion + 1
    const t = this.selectedDisplayVersion
    const e = document.createDocumentFragment()
    if (this.isMultiple) {
      if (!Array.isArray(this.state) || this.state.length === 0) {
        const o = document.createElement('span');
        ((o.textContent = this.placeholder),
        o.classList.add('fi-select-input-placeholder'),
        e.appendChild(o))
      } else {
        const o = await this.getLabelsForMultipleSelection()
        if (t !== this.selectedDisplayVersion) return
        this.addBadgesForSelectedOptions(o, e)
      }
      t === this.selectedDisplayVersion &&
                (this.selectedDisplay.replaceChildren(e),
                this.isOpen && this.positionDropdown())
      return
    }
    if (this.state === null || this.state === '') {
      const o = document.createElement('span')
      if (
        ((o.textContent = this.placeholder),
        o.classList.add('fi-select-input-placeholder'),
        e.appendChild(o),
        t === this.selectedDisplayVersion)
      ) {
        this.selectedDisplay.replaceChildren(e)
        const s = this.container.querySelector(
          '.fi-select-input-value-remove-btn'
        );
        (s && s.remove(),
        this.container.classList.remove(
          'fi-select-input-ctn-clearable'
        ))
      }
      return
    }
    const i = await this.getLabelForSingleSelection()
    t === this.selectedDisplayVersion &&
            (this.addSingleSelectionDisplay(i, e),
            t === this.selectedDisplayVersion &&
                this.selectedDisplay.replaceChildren(e))
  }

  async getLabelsForMultipleSelection () {
    const t = this.getSelectedOptionLabels()
    const e = []
    if (Array.isArray(this.state)) {
      for (const o of this.state) {
        if (!P(this.labelRepository[o])) {
          if (P(t[o])) {
            this.labelRepository[o] = t[o]
            continue
          }
          e.push(o.toString())
        }
      }
    }
    if (
      e.length > 0 &&
            P(this.initialOptionLabels) &&
            JSON.stringify(this.state) === JSON.stringify(this.initialState)
    ) {
      if (Array.isArray(this.initialOptionLabels)) {
        for (const o of this.initialOptionLabels) {
          P(o) &&
                        o.value !== void 0 &&
                        o.label !== void 0 &&
                        e.includes(o.value) &&
                        (this.labelRepository[o.value] = o.label)
        }
      }
    } else if (e.length > 0 && this.getOptionLabelsUsing) {
      try {
        const o = await this.getOptionLabelsUsing()
        for (const s of o) {
          P(s) &&
                        s.value !== void 0 &&
                        s.label !== void 0 &&
                        (this.labelRepository[s.value] = s.label)
        }
      } catch (o) {
        console.error('Error fetching option labels:', o)
      }
    }
    const i = []
    if (Array.isArray(this.state)) {
      for (const o of this.state) {
        P(this.labelRepository[o])
          ? i.push(this.labelRepository[o])
          : P(t[o])
            ? i.push(t[o])
            : i.push(o)
      }
    }
    return i
  }

  createBadgeElement (t, e) {
    const i = document.createElement('span');
    ((i.className =
            'fi-badge fi-size-md fi-color fi-color-primary fi-text-color-600 dark:fi-text-color-200'),
    P(t) && i.setAttribute('data-value', t))
    const o = document.createElement('span')
    o.className = 'fi-badge-label-ctn'
    const s = document.createElement('span');
    ((s.className = 'fi-badge-label'),
    this.canOptionLabelsWrap && s.classList.add('fi-wrapped'),
    this.isHtmlAllowed ? (s.innerHTML = e) : (s.textContent = e),
    o.appendChild(s),
    i.appendChild(o))
    const r = this.createRemoveButton(t, e)
    return (i.appendChild(r), i)
  }

  createRemoveButton (t, e) {
    const i = document.createElement('button')
    return (
      (i.type = 'button'),
      (i.className = 'fi-badge-delete-btn'),
      (i.innerHTML =
                '<svg class="fi-icon fi-size-xs" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" data-slot="icon"><path d="M5.28 4.22a.75.75 0 0 0-1.06 1.06L6.94 8l-2.72 2.72a.75.75 0 1 0 1.06 1.06L8 9.06l2.72 2.72a.75.75 0 1 0 1.06-1.06L9.06 8l2.72-2.72a.75.75 0 0 0-1.06-1.06L8 6.94 5.28 4.22Z"></path></svg>'),
      i.setAttribute(
        'aria-label',
        'Remove ' +
                    (this.isHtmlAllowed ? e.replace(/<[^>]*>/g, '') : e)
      ),
      i.addEventListener('click', (o) => {
        (o.stopPropagation(), P(t) && this.selectOption(t))
      }),
      i.addEventListener('keydown', (o) => {
        (o.key === ' ' || o.key === 'Enter') &&
                    (o.preventDefault(),
                    o.stopPropagation(),
                    P(t) && this.selectOption(t))
      }),
      i
    )
  }

  addBadgesForSelectedOptions (t, e = this.selectedDisplay) {
    const i = document.createElement('div');
    ((i.className = 'fi-select-input-value-badges-ctn'),
    t.forEach((o, s) => {
      const r = Array.isArray(this.state) ? this.state[s] : null
      const a = this.createBadgeElement(r, o)
      i.appendChild(a)
    }),
    e.appendChild(i),
    this.isReorderable &&
                (i.addEventListener('click', (o) => {
                  o.stopPropagation()
                }),
                i.addEventListener('mousedown', (o) => {
                  o.stopPropagation()
                }),
                new Ui(i, {
                  animation: 150,
                  onEnd: () => {
                    const o = [];
                    (i.querySelectorAll('[data-value]').forEach((s) => {
                      o.push(s.getAttribute('data-value'))
                    }),
                    (this.state = o),
                    this.onStateChange(this.state))
                  }
                })))
  }

  async getLabelForSingleSelection () {
    let t = this.labelRepository[this.state]
    if (
      (bt(t) && (t = this.getSelectedOptionLabel(this.state)),
      bt(t) &&
                P(this.initialOptionLabel) &&
                this.state === this.initialState)
    ) {
      ((t = this.initialOptionLabel),
      P(this.state) && (this.labelRepository[this.state] = t))
    } else if (bt(t) && this.getOptionLabelUsing) {
      try {
        ((t = await this.getOptionLabelUsing()),
        P(t) &&
                        P(this.state) &&
                        (this.labelRepository[this.state] = t))
      } catch (e) {
        (console.error('Error fetching option label:', e),
        (t = this.state))
      }
    } else bt(t) && (t = this.state)
    return t
  }

  addSingleSelectionDisplay (t, e = this.selectedDisplay) {
    const i = document.createElement('span')
    if (
      ((i.className = 'fi-select-input-value-label'),
      this.isHtmlAllowed ? (i.innerHTML = t) : (i.textContent = t),
      e.appendChild(i),
      !this.canSelectPlaceholder ||
                this.container.querySelector(
                  '.fi-select-input-value-remove-btn'
                ))
    ) {
      return
    }
    const o = document.createElement('button');
    ((o.type = 'button'),
    (o.className = 'fi-select-input-value-remove-btn'),
    (o.innerHTML =
                '<svg class="fi-icon fi-size-sm" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>'),
    o.setAttribute('aria-label', 'Clear selection'),
    o.addEventListener('click', (s) => {
      (s.stopPropagation(), this.selectOption(''))
    }),
    o.addEventListener('keydown', (s) => {
      (s.key === ' ' || s.key === 'Enter') &&
                    (s.preventDefault(),
                    s.stopPropagation(),
                    this.selectOption(''))
    }),
    this.container.appendChild(o),
    this.container.classList.add('fi-select-input-ctn-clearable'))
  }

  getSelectedOptionLabel (t) {
    if (P(this.labelRepository[t])) return this.labelRepository[t]
    let e = ''
    for (const i of this.options) {
      if (i.options && Array.isArray(i.options)) {
        for (const o of i.options) {
          if (o.value === t) {
            ((e = o.label), (this.labelRepository[t] = e))
            break
          }
        }
      } else if (i.value === t) {
        ((e = i.label), (this.labelRepository[t] = e))
        break
      }
    }
    return e
  }

  setUpEventListeners () {
    ((this.buttonClickListener = () => {
      this.toggleDropdown()
    }),
    (this.documentClickListener = (t) => {
      !this.container.contains(t.target) &&
                    this.isOpen &&
                    this.closeDropdown()
    }),
    (this.buttonKeydownListener = (t) => {
      this.isDisabled || this.handleSelectButtonKeydown(t)
    }),
    (this.dropdownKeydownListener = (t) => {
      this.isDisabled ||
                    (this.isSearchable &&
                        document.activeElement === this.searchInput &&
                        !['Tab', 'Escape'].includes(t.key)) ||
                    this.handleDropdownKeydown(t)
    }),
    this.selectButton.addEventListener(
      'click',
      this.buttonClickListener
    ),
    document.addEventListener('click', this.documentClickListener),
    this.selectButton.addEventListener(
      'keydown',
      this.buttonKeydownListener
    ),
    this.dropdown.addEventListener(
      'keydown',
      this.dropdownKeydownListener
    ),
    !this.isMultiple &&
                this.livewireId &&
                this.statePath &&
                this.getOptionLabelUsing &&
                ((this.refreshOptionLabelListener = async (t) => {
                  if (
                    t.detail.livewireId === this.livewireId &&
                        t.detail.statePath === this.statePath &&
                        P(this.state)
                  ) {
                    try {
                      delete this.labelRepository[this.state]
                      const e = await this.getOptionLabelUsing()
                      P(e) && (this.labelRepository[this.state] = e)
                      const i = this.selectedDisplay.querySelector(
                        '.fi-select-input-value-label'
                      );
                      (P(i) &&
                                (this.isHtmlAllowed
                                  ? (i.innerHTML = e)
                                  : (i.textContent = e)),
                      this.updateOptionLabelInList(this.state, e))
                    } catch (e) {
                      console.error('Error refreshing option label:', e)
                    }
                  }
                }),
                window.addEventListener(
                  'filament-forms::select.refreshSelectedOptionLabel',
                  this.refreshOptionLabelListener
                )))
  }

  updateOptionLabelInList (t, e) {
    this.labelRepository[t] = e
    const i = this.getVisibleOptions()
    for (const o of i) {
      if (o.getAttribute('data-value') === String(t)) {
        if (((o.innerHTML = ''), this.isHtmlAllowed)) {
          const s = document.createElement('span');
          ((s.innerHTML = e), o.appendChild(s))
        } else o.appendChild(document.createTextNode(e))
        break
      }
    }
    for (const o of this.options) {
      if (o.options && Array.isArray(o.options)) {
        for (const s of o.options) {
          if (s.value === t) {
            s.label = e
            break
          }
        }
      } else if (o.value === t) {
        o.label = e
        break
      }
    }
    for (const o of this.originalOptions) {
      if (o.options && Array.isArray(o.options)) {
        for (const s of o.options) {
          if (s.value === t) {
            s.label = e
            break
          }
        }
      } else if (o.value === t) {
        o.label = e
        break
      }
    }
  }

  handleSelectButtonKeydown (t) {
    switch (t.key) {
      case 'ArrowDown':
        (t.preventDefault(),
        t.stopPropagation(),
        this.isOpen ? this.focusNextOption() : this.openDropdown())
        break
      case 'ArrowUp':
        (t.preventDefault(),
        t.stopPropagation(),
        this.isOpen
          ? this.focusPreviousOption()
          : this.openDropdown())
        break
      case ' ':
        if ((t.preventDefault(), this.isOpen)) {
          if (this.selectedIndex >= 0) {
            const e = this.getVisibleOptions()[this.selectedIndex]
            e && e.click()
          }
        } else this.openDropdown()
        break
      case 'Enter':
        break
      case 'Escape':
        this.isOpen && (t.preventDefault(), this.closeDropdown())
        break
      case 'Tab':
        this.isOpen && this.closeDropdown()
        break
      default:
        if (
          this.isSearchable &&
                    !t.ctrlKey &&
                    !t.metaKey &&
                    !t.altKey &&
                    typeof t.key === 'string' &&
                    t.key.length === 1
        ) {
          t.preventDefault()
          const e = t.key;
          (this.isOpen || this.openDropdown(),
          this.searchInput &&
                            (this.searchInput.focus(),
                            (this.searchInput.value =
                                (this.searchInput.value || '') + e),
                            this.searchInput.dispatchEvent(
                              new Event('input', { bubbles: !0 })
                            )))
        }
        break
    }
  }

  handleDropdownKeydown (t) {
    switch (t.key) {
      case 'ArrowDown':
        (t.preventDefault(),
        t.stopPropagation(),
        this.focusNextOption())
        break
      case 'ArrowUp':
        (t.preventDefault(),
        t.stopPropagation(),
        this.focusPreviousOption())
        break
      case ' ':
        if ((t.preventDefault(), this.selectedIndex >= 0)) {
          const e = this.getVisibleOptions()[this.selectedIndex]
          e && e.click()
        }
        break
      case 'Enter':
        if ((t.preventDefault(), this.selectedIndex >= 0)) {
          const e = this.getVisibleOptions()[this.selectedIndex]
          e && e.click()
        } else {
          const e = this.element.closest('form')
          e && e.submit()
        }
        break
      case 'Escape':
        (t.preventDefault(),
        this.closeDropdown(),
        this.selectButton.focus())
        break
      case 'Tab':
        this.closeDropdown()
        break
      default:
        if (
          this.isSearchable &&
                    !t.ctrlKey &&
                    !t.metaKey &&
                    !t.altKey &&
                    typeof t.key === 'string' &&
                    t.key.length === 1
        ) {
          t.preventDefault()
          const e = t.key
          this.searchInput &&
                        (this.searchInput.focus(),
                        (this.searchInput.value =
                            (this.searchInput.value || '') + e),
                        this.searchInput.dispatchEvent(
                          new Event('input', { bubbles: !0 })
                        ))
        }
        break
    }
  }

  toggleDropdown () {
    if (!this.isDisabled) {
      if (this.isOpen) {
        this.closeDropdown()
        return
      }
      (this.isMultiple &&
                !this.isSearchable &&
                !this.hasAvailableOptions()) ||
                this.openDropdown()
    }
  }

  hasAvailableOptions () {
    for (const t of this.options) {
      if (t.options && Array.isArray(t.options)) {
        for (const e of t.options) {
          if (
            !Array.isArray(this.state) ||
                        !this.state.includes(e.value)
          ) {
            return !0
          }
        }
      } else if (
        !Array.isArray(this.state) ||
                !this.state.includes(t.value)
      ) {
        return !0
      }
    }
    return !1
  }

  async openDropdown () {
    ((this.dropdown.style.display = 'block'),
    (this.dropdown.style.opacity = '0'))
    const t =
            this.selectButton.closest('.fi-fixed-positioning-context') !==
                null &&
            this.selectButton.closest('.fi-absolute-positioning-context') ===
                null
    if (
      ((this.dropdown.style.position = t ? 'fixed' : 'absolute'),
      (this.dropdown.style.width = `${this.selectButton.offsetWidth}px`),
      this.selectButton.setAttribute('aria-expanded', 'true'),
      (this.isOpen = !0),
      this.positionDropdown(),
      this.resizeListener ||
                ((this.resizeListener = () => {
                  ((this.dropdown.style.width = `${this.selectButton.offsetWidth}px`),
                  this.positionDropdown())
                }),
                window.addEventListener('resize', this.resizeListener)),
      this.scrollListener ||
                ((this.scrollListener = () => this.positionDropdown()),
                window.addEventListener('scroll', this.scrollListener, !0)),
      (this.dropdown.style.opacity = '1'),
      this.isSearchable &&
                this.searchInput &&
                ((this.searchInput.value = ''),
                (this.searchQuery = ''),
                this.hasDynamicOptions ||
                    ((this.options = JSON.parse(
                      JSON.stringify(this.originalOptions)
                    )),
                    this.renderOptions())),
      this.hasDynamicOptions && this.getOptionsUsing)
    ) {
      this.showLoadingState(!1)
      try {
        const e = await this.getOptionsUsing()
        const i = Array.isArray(e)
          ? e
          : e && Array.isArray(e.options)
            ? e.options
            : []
        if (
          ((this.options = i),
          (this.originalOptions = JSON.parse(JSON.stringify(i))),
          this.populateLabelRepositoryFromOptions(i),
          this.isSearchable &&
                        this.searchInput &&
                        ((this.searchInput.value &&
                            this.searchInput.value.trim() !== '') ||
                            (this.searchQuery &&
                                this.searchQuery.trim() !== '')))
        ) {
          const o = (this.searchInput.value || this.searchQuery || '')
            .trim()
            .toLowerCase();
          (this.hideLoadingState(), this.filterOptions(o))
        } else this.renderOptions()
      } catch (e) {
        (console.error('Error fetching options:', e),
        this.hideLoadingState())
      }
    } else {
      (!this.hasInitialNoOptionsMessage || this.searchQuery) &&
                this.hideLoadingState()
    }
    if (this.isSearchable && this.searchInput) this.searchInput.focus()
    else {
      this.selectedIndex = -1
      const e = this.getVisibleOptions()
      if (this.isMultiple) {
        if (Array.isArray(this.state) && this.state.length > 0) {
          for (let i = 0; i < e.length; i++) {
            if (
              this.state.includes(e[i].getAttribute('data-value'))
            ) {
              this.selectedIndex = i
              break
            }
          }
        }
      } else {
        for (let i = 0; i < e.length; i++) {
          if (e[i].getAttribute('data-value') === this.state) {
            this.selectedIndex = i
            break
          }
        }
      }
      (this.selectedIndex === -1 &&
                e.length > 0 &&
                (this.selectedIndex = 0),
      this.selectedIndex >= 0 &&
                    (e[this.selectedIndex].classList.add('fi-selected'),
                    e[this.selectedIndex].focus()))
    }
  }

  positionDropdown () {
    const t = this.position === 'top' ? 'top-start' : 'bottom-start'
    const e = [wi(4), Si({ padding: 5 })]
    this.position !== 'top' && this.position !== 'bottom' && e.push(xi())
    const i =
            this.selectButton.closest('.fi-fixed-positioning-context') !==
                null &&
            this.selectButton.closest('.fi-absolute-positioning-context') ===
                null
    Ei(this.selectButton, this.dropdown, {
      placement: t,
      middleware: e,
      strategy: i ? 'fixed' : 'absolute'
    }).then(({ x: o, y: s }) => {
      Object.assign(this.dropdown.style, {
        left: `${o}px`,
        top: `${s}px`
      })
    })
  }

  closeDropdown () {
    ((this.dropdown.style.display = 'none'),
    this.selectButton.setAttribute('aria-expanded', 'false'),
    (this.isOpen = !1),
    this.searchTimeout &&
                (clearTimeout(this.searchTimeout), (this.searchTimeout = null)),
    this.activeSearchId++,
    (this.isSearching = !1),
    this.hideLoadingState(),
    this.resizeListener &&
                (window.removeEventListener('resize', this.resizeListener),
                (this.resizeListener = null)),
    this.scrollListener &&
                (window.removeEventListener('scroll', this.scrollListener, !0),
                (this.scrollListener = null)),
    this.getVisibleOptions().forEach((e) => {
      e.classList.remove('fi-selected')
    }),
    this.dropdown.removeAttribute('aria-activedescendant'))
  }

  focusNextOption () {
    const t = this.getVisibleOptions()
    if (t.length !== 0) {
      if (
        (this.selectedIndex >= 0 &&
                    this.selectedIndex < t.length &&
                    t[this.selectedIndex].classList.remove('fi-selected'),
        this.selectedIndex === t.length - 1 &&
                    this.isSearchable &&
                    this.searchInput)
      ) {
        ((this.selectedIndex = -1),
        this.searchInput.focus(),
        this.dropdown.removeAttribute('aria-activedescendant'))
        return
      }
      ((this.selectedIndex = (this.selectedIndex + 1) % t.length),
      t[this.selectedIndex].classList.add('fi-selected'),
      t[this.selectedIndex].focus(),
      t[this.selectedIndex].id &&
                    this.dropdown.setAttribute(
                      'aria-activedescendant',
                      t[this.selectedIndex].id
                    ),
      this.scrollOptionIntoView(t[this.selectedIndex]))
    }
  }

  focusPreviousOption () {
    const t = this.getVisibleOptions()
    if (t.length !== 0) {
      if (
        (this.selectedIndex >= 0 &&
                    this.selectedIndex < t.length &&
                    t[this.selectedIndex].classList.remove('fi-selected'),
        (this.selectedIndex === 0 || this.selectedIndex === -1) &&
                    this.isSearchable &&
                    this.searchInput)
      ) {
        ((this.selectedIndex = -1),
        this.searchInput.focus(),
        this.dropdown.removeAttribute('aria-activedescendant'))
        return
      }
      ((this.selectedIndex =
                (this.selectedIndex - 1 + t.length) % t.length),
      t[this.selectedIndex].classList.add('fi-selected'),
      t[this.selectedIndex].focus(),
      t[this.selectedIndex].id &&
                    this.dropdown.setAttribute(
                      'aria-activedescendant',
                      t[this.selectedIndex].id
                    ),
      this.scrollOptionIntoView(t[this.selectedIndex]))
    }
  }

  scrollOptionIntoView (t) {
    if (!t) return
    const e = this.dropdown.getBoundingClientRect()
    const i = t.getBoundingClientRect()
    i.bottom > e.bottom
      ? (this.dropdown.scrollTop += i.bottom - e.bottom)
      : i.top < e.top && (this.dropdown.scrollTop -= e.top - i.top)
  }

  getVisibleOptions () {
    let t = []
    this.optionsList.classList.contains('fi-dropdown-list')
      ? (t = Array.from(
          this.optionsList.querySelectorAll(
            ':scope > li[role="option"]'
          )
        ))
      : (t = Array.from(
          this.optionsList.querySelectorAll(
            ':scope > ul.fi-dropdown-list > li[role="option"]'
          )
        ))
    const e = Array.from(
      this.optionsList.querySelectorAll(
        'li.fi-select-input-option-group > ul > li[role="option"]'
      )
    )
    return [...t, ...e]
  }

  getSelectedOptionLabels () {
    if (!Array.isArray(this.state) || this.state.length === 0) return {}
    const t = {}
    for (const e of this.state) {
      let i = !1
      for (const o of this.options) {
        if (o.options && Array.isArray(o.options)) {
          for (const s of o.options) {
            if (s.value === e) {
              ((t[e] = s.label), (i = !0))
              break
            }
          }
          if (i) break
        } else if (o.value === e) {
          ((t[e] = o.label), (i = !0))
          break
        }
      }
    }
    return t
  }

  handleSearch (t) {
    const e = t.target.value.trim()
    if (
      ((this.searchQuery = e),
      this.searchTimeout && clearTimeout(this.searchTimeout),
      e === '')
    ) {
      ((this.options = JSON.parse(JSON.stringify(this.originalOptions))),
      this.renderOptions())
      return
    }
    if (
      !this.getSearchResultsUsing ||
            typeof this.getSearchResultsUsing !== 'function' ||
            !this.hasDynamicSearchResults
    ) {
      this.filterOptions(e)
      return
    }
    this.searchTimeout = setTimeout(async () => {
      this.searchTimeout = null
      const i = ++this.activeSearchId
      this.isSearching = !0
      try {
        this.showLoadingState(!0)
        const o = await this.getSearchResultsUsing(e)
        if (i !== this.activeSearchId || !this.isOpen) return
        const s = Array.isArray(o)
          ? o
          : o && Array.isArray(o.options)
            ? o.options
            : [];
        ((this.options = s),
        this.populateLabelRepositoryFromOptions(s),
        this.hideLoadingState(),
        this.renderOptions(),
        this.isOpen && this.positionDropdown(),
        this.options.length === 0 && this.showNoResultsMessage())
      } catch (o) {
        i === this.activeSearchId &&
                    (console.error('Error fetching search results:', o),
                    this.hideLoadingState(),
                    (this.options = JSON.parse(
                      JSON.stringify(this.originalOptions)
                    )),
                    this.renderOptions())
      } finally {
        i === this.activeSearchId && (this.isSearching = !1)
      }
    }, this.searchDebounce)
  }

  showLoadingState (t = !1) {
    (this.optionsList.parentNode === this.dropdown &&
            this.dropdown.removeChild(this.optionsList),
    this.hideLoadingState())
    const e = document.createElement('div');
    ((e.className = 'fi-select-input-message'),
    (e.textContent = t ? this.searchingMessage : this.loadingMessage),
    this.dropdown.appendChild(e))
  }

  hideLoadingState () {
    const t = this.dropdown.querySelector('.fi-select-input-message')
    t && t.remove()
  }

  showNoOptionsMessage () {
    (this.optionsList.parentNode === this.dropdown &&
            this.dropdown.removeChild(this.optionsList),
    this.hideLoadingState())
    const t = document.createElement('div');
    ((t.className = 'fi-select-input-message'),
    (t.textContent = this.noOptionsMessage),
    this.dropdown.appendChild(t))
  }

  showNoResultsMessage () {
    (this.optionsList.parentNode === this.dropdown &&
            this.dropdown.removeChild(this.optionsList),
    this.hideLoadingState())
    const t = document.createElement('div');
    ((t.className = 'fi-select-input-message'),
    (t.textContent = this.noSearchResultsMessage),
    this.dropdown.appendChild(t))
  }

  filterOptions (t) {
    const e = this.searchableOptionFields.includes('label')
    const i = this.searchableOptionFields.includes('value')
    t = t.toLowerCase()
    const o = []
    for (const s of this.originalOptions) {
      if (s.options && Array.isArray(s.options)) {
        const r = s.options.filter(
          (a) =>
            (e && a.label.toLowerCase().includes(t)) ||
                        (i && String(a.value).toLowerCase().includes(t))
        )
        r.length > 0 && o.push({ label: s.label, options: r })
      } else {
        ((e && s.label.toLowerCase().includes(t)) ||
                    (i && String(s.value).toLowerCase().includes(t))) &&
                    o.push(s)
      }
    }
    ((this.options = o),
    this.renderOptions(),
    this.options.length === 0 && this.showNoResultsMessage(),
    this.isOpen && this.positionDropdown())
  }

  selectOption (t) {
    if (this.isDisabled) return
    if (!this.isMultiple) {
      ((this.state = t),
      this.updateSelectedDisplay(),
      this.renderOptions(),
      this.closeDropdown(),
      this.selectButton.focus(),
      this.onStateChange(this.state))
      return
    }
    let e = Array.isArray(this.state) ? [...this.state] : []
    if (e.includes(t)) {
      const o = this.selectedDisplay.querySelector(`[data-value="${t}"]`)
      if (P(o)) {
        const s = o.parentElement
        P(s) && s.children.length === 1
          ? ((e = e.filter((r) => r !== t)),
            (this.state = e),
            this.updateSelectedDisplay())
          : (o.remove(),
            (e = e.filter((r) => r !== t)),
            (this.state = e))
      } else {
        ((e = e.filter((s) => s !== t)),
        (this.state = e),
        this.updateSelectedDisplay())
      }
      (this.renderOptions(),
      this.isOpen && this.positionDropdown(),
      this.maintainFocusInMultipleMode(),
      this.onStateChange(this.state))
      return
    }
    if (this.maxItems && e.length >= this.maxItems) {
      this.maxItemsMessage && alert(this.maxItemsMessage)
      return
    }
    (e.push(t), (this.state = e))
    const i = this.selectedDisplay.querySelector(
      '.fi-select-input-value-badges-ctn'
    );
    (bt(i) ? this.updateSelectedDisplay() : this.addSingleBadge(t, i),
    this.renderOptions(),
    this.isOpen && this.positionDropdown(),
    this.maintainFocusInMultipleMode(),
    this.onStateChange(this.state))
  }

  async addSingleBadge (t, e) {
    let i = this.labelRepository[t]
    if (
      (bt(i) &&
                ((i = this.getSelectedOptionLabel(t)),
                P(i) && (this.labelRepository[t] = i)),
      bt(i) && this.getOptionLabelsUsing)
    ) {
      try {
        const s = await this.getOptionLabelsUsing()
        for (const r of s) {
          if (P(r) && r.value === t && r.label !== void 0) {
            ((i = r.label), (this.labelRepository[t] = i))
            break
          }
        }
      } catch (s) {
        console.error('Error fetching option label:', s)
      }
    }
    bt(i) && (i = t)
    const o = this.createBadgeElement(t, i)
    e.appendChild(o)
  }

  maintainFocusInMultipleMode () {
    if (this.isSearchable && this.searchInput) {
      this.searchInput.focus()
      return
    }
    const t = this.getVisibleOptions()
    if (t.length !== 0) {
      if (
        ((this.selectedIndex = -1),
        Array.isArray(this.state) && this.state.length > 0)
      ) {
        for (let e = 0; e < t.length; e++) {
          if (this.state.includes(t[e].getAttribute('data-value'))) {
            this.selectedIndex = e
            break
          }
        }
      }
      (this.selectedIndex === -1 && (this.selectedIndex = 0),
      t[this.selectedIndex].classList.add('fi-selected'),
      t[this.selectedIndex].focus())
    }
  }

  disable () {
    this.isDisabled ||
            ((this.isDisabled = !0),
            this.applyDisabledState(),
            this.isOpen && this.closeDropdown())
  }

  enable () {
    this.isDisabled && ((this.isDisabled = !1), this.applyDisabledState())
  }

  applyDisabledState () {
    if (this.isDisabled) {
      if (
        (this.selectButton.setAttribute('disabled', 'disabled'),
        this.selectButton.setAttribute('aria-disabled', 'true'),
        this.selectButton.classList.add('fi-disabled'),
        this.isMultiple &&
                    this.container
                      .querySelectorAll('.fi-select-input-badge-remove')
                      .forEach((e) => {
                        (e.setAttribute('disabled', 'disabled'),
                        e.classList.add('fi-disabled'))
                      }),
        !this.isMultiple && this.canSelectPlaceholder)
      ) {
        const t = this.container.querySelector(
          '.fi-select-input-value-remove-btn'
        )
        t &&
                    (t.setAttribute('disabled', 'disabled'),
                    t.classList.add('fi-disabled'))
      }
      this.isSearchable &&
                this.searchInput &&
                (this.searchInput.setAttribute('disabled', 'disabled'),
                this.searchInput.classList.add('fi-disabled'))
    } else {
      if (
        (this.selectButton.removeAttribute('disabled'),
        this.selectButton.removeAttribute('aria-disabled'),
        this.selectButton.classList.remove('fi-disabled'),
        this.isMultiple &&
                    this.container
                      .querySelectorAll('.fi-select-input-badge-remove')
                      .forEach((e) => {
                        (e.removeAttribute('disabled'),
                        e.classList.remove('fi-disabled'))
                      }),
        !this.isMultiple && this.canSelectPlaceholder)
      ) {
        const t = this.container.querySelector(
          '.fi-select-input-value-remove-btn'
        )
        t &&
                    (t.removeAttribute('disabled'),
                    t.classList.add('fi-disabled'))
      }
      this.isSearchable &&
                this.searchInput &&
                (this.searchInput.removeAttribute('disabled'),
                this.searchInput.classList.remove('fi-disabled'))
    }
  }

  destroy () {
    (this.selectButton &&
            this.buttonClickListener &&
            this.selectButton.removeEventListener(
              'click',
              this.buttonClickListener
            ),
    this.documentClickListener &&
                document.removeEventListener(
                  'click',
                  this.documentClickListener
                ),
    this.selectButton &&
                this.buttonKeydownListener &&
                this.selectButton.removeEventListener(
                  'keydown',
                  this.buttonKeydownListener
                ),
    this.dropdown &&
                this.dropdownKeydownListener &&
                this.dropdown.removeEventListener(
                  'keydown',
                  this.dropdownKeydownListener
                ),
    this.resizeListener &&
                (window.removeEventListener('resize', this.resizeListener),
                (this.resizeListener = null)),
    this.scrollListener &&
                (window.removeEventListener('scroll', this.scrollListener, !0),
                (this.scrollListener = null)),
    this.refreshOptionLabelListener &&
                window.removeEventListener(
                  'filament-forms::select.refreshSelectedOptionLabel',
                  this.refreshOptionLabelListener
                ),
    this.isOpen && this.closeDropdown(),
    this.searchTimeout &&
                (clearTimeout(this.searchTimeout), (this.searchTimeout = null)),
    this.container && this.container.remove())
  }
}
function Qn ({
  canOptionLabelsWrap: n,
  canSelectPlaceholder: t,
  getOptionLabelUsing: e,
  getOptionsUsing: i,
  getSearchResultsUsing: o,
  hasDynamicOptions: s,
  hasDynamicSearchResults: r,
  hasInitialNoOptionsMessage: a,
  initialOptionLabel: l,
  isDisabled: c,
  isHtmlAllowed: f,
  isNative: d,
  isSearchable: p,
  loadingMessage: u,
  name: g,
  noOptionsMessage: m,
  noSearchResultsMessage: S,
  options: O,
  optionsLimit: w,
  placeholder: D,
  position: A,
  recordKey: C,
  searchableOptionFields: H,
  searchDebounce: q,
  searchingMessage: J,
  searchPrompt: R,
  state: z
}) {
  return {
    error: void 0,
    isLoading: !1,
    select: null,
    state: z,
    init () {
      (d ||
                (this.select = new Oe({
                  canOptionLabelsWrap: n,
                  canSelectPlaceholder: t,
                  element: this.$refs.select,
                  getOptionLabelUsing: e,
                  getOptionsUsing: i,
                  getSearchResultsUsing: o,
                  hasDynamicOptions: s,
                  hasDynamicSearchResults: r,
                  hasInitialNoOptionsMessage: a,
                  initialOptionLabel: l,
                  isDisabled: c,
                  isHtmlAllowed: f,
                  isSearchable: p,
                  loadingMessage: u,
                  noOptionsMessage: m,
                  noSearchResultsMessage: S,
                  onStateChange: (L) => {
                    this.state = L
                  },
                  options: O,
                  optionsLimit: w,
                  placeholder: D,
                  position: A,
                  searchableOptionFields: H,
                  searchDebounce: q,
                  searchingMessage: J,
                  searchPrompt: R,
                  state: this.state
                })),
      Livewire.hook(
        'commit',
        ({
          component: L,
          commit: Y,
          succeed: B,
          fail: F,
          respond: Q
        }) => {
          B(({ snapshot: V, effect: X }) => {
            this.$nextTick(() => {
              if (
                this.isLoading ||
                                    L.id !==
                                        this.$root.closest('[wire\\:id]')
                                          ?.attributes['wire:id'].value
              ) {
                return
              }
              const T = this.getServerState()
              T === void 0 ||
                                    this.getNormalizedState() === T ||
                                    (this.state = T)
            })
          })
        }
      ),
      this.$watch('state', async (L) => {
        !d &&
                        this.select &&
                        this.select.state !== L &&
                        ((this.select.state = L),
                        this.select.updateSelectedDisplay(),
                        this.select.renderOptions())
        const Y = this.getServerState()
        if (Y === void 0 || this.getNormalizedState() === Y) return
        this.isLoading = !0
        const B = await this.$wire.updateTableColumnState(
          g,
          C,
          this.state
        );
        ((this.error = B?.error ?? void 0),
        !this.error &&
                            this.$refs.serverState &&
                            (this.$refs.serverState.value =
                                this.getNormalizedState()),
        (this.isLoading = !1))
      }))
    },
    getServerState () {
      if (this.$refs.serverState) {
        return [null, void 0].includes(this.$refs.serverState.value)
          ? ''
          : this.$refs.serverState.value.replaceAll('\\"', '"')
      }
    },
    getNormalizedState () {
      const L = Alpine.raw(this.state)
      return [null, void 0].includes(L) ? '' : L
    },
    destroy () {
      this.select && (this.select.destroy(), (this.select = null))
    }
  }
}
export { Qn as default }
/*! Bundled license information:

sortablejs/modular/sortable.esm.js:
  (**!
   * Sortable 1.15.6
   * @author	RubaXa   <trash@rubaxa.org>
   * @author	owenm    <owen23355@gmail.com>
   * @license MIT
   *)
*/
