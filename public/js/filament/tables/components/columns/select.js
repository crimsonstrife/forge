const tt = Math.min
const $ = Math.max
const et = Math.round
const T = (i) => ({ x: i, y: i })
const Gt = { left: 'right', right: 'left', bottom: 'top', top: 'bottom' }
const Xt = { start: 'end', end: 'start' }
function pt (i, t, e) {
  return $(i, tt(t, e))
}
function it (i, t) {
  return typeof i === 'function' ? i(t) : i
}
function z (i) {
  return i.split('-')[0]
}
function st (i) {
  return i.split('-')[1]
}
function mt (i) {
  return i === 'x' ? 'y' : 'x'
}
function gt (i) {
  return i === 'y' ? 'height' : 'width'
}
const Qt = new Set(['top', 'bottom'])
function B (i) {
  return Qt.has(z(i)) ? 'y' : 'x'
}
function bt (i) {
  return mt(B(i))
}
function Ot (i, t, e) {
  e === void 0 && (e = !1)
  const s = st(i)
  const n = bt(i)
  const o = gt(n)
  let r =
        n === 'x'
          ? s === (e ? 'end' : 'start')
            ? 'right'
            : 'left'
          : s === 'start'
            ? 'bottom'
            : 'top'
  return (t.reference[o] > t.floating[o] && (r = Z(r)), [r, Z(r)])
}
function At (i) {
  const t = Z(i)
  return [lt(i), t, lt(t)]
}
function lt (i) {
  return i.replace(/start|end/g, (t) => Xt[t])
}
const vt = ['left', 'right']
const Lt = ['right', 'left']
const Zt = ['top', 'bottom']
const te = ['bottom', 'top']
function ee (i, t, e) {
  switch (i) {
    case 'top':
    case 'bottom':
      return e ? (t ? Lt : vt) : t ? vt : Lt
    case 'left':
    case 'right':
      return t ? Zt : te
    default:
      return []
  }
}
function St (i, t, e, s) {
  const n = st(i)
  let o = ee(z(i), e === 'start', s)
  return (
    n && ((o = o.map((r) => r + '-' + n)), t && (o = o.concat(o.map(lt)))),
    o
  )
}
function Z (i) {
  return i.replace(/left|right|bottom|top/g, (t) => Gt[t])
}
function ie (i) {
  return { top: 0, right: 0, bottom: 0, left: 0, ...i }
}
function Ct (i) {
  return typeof i !== 'number'
    ? ie(i)
    : { top: i, right: i, bottom: i, left: i }
}
function U (i) {
  const { x: t, y: e, width: s, height: n } = i
  return {
    width: s,
    height: n,
    top: e,
    left: t,
    right: t + s,
    bottom: e + n,
    x: t,
    y: e
  }
}
function Et (i, t, e) {
  const { reference: s, floating: n } = i
  const o = B(t)
  const r = bt(t)
  const l = gt(r)
  const a = z(t)
  const c = o === 'y'
  const h = s.x + s.width / 2 - n.width / 2
  const d = s.y + s.height / 2 - n.height / 2
  const u = s[l] / 2 - n[l] / 2
  let f
  switch (a) {
    case 'top':
      f = { x: h, y: s.y - n.height }
      break
    case 'bottom':
      f = { x: h, y: s.y + s.height }
      break
    case 'right':
      f = { x: s.x + s.width, y: d }
      break
    case 'left':
      f = { x: s.x - n.width, y: d }
      break
    default:
      f = { x: s.x, y: s.y }
  }
  switch (st(t)) {
    case 'start':
      f[r] -= u * (e && c ? -1 : 1)
      break
    case 'end':
      f[r] += u * (e && c ? -1 : 1)
      break
  }
  return f
}
const Dt = async (i, t, e) => {
  const {
    placement: s = 'bottom',
    strategy: n = 'absolute',
    middleware: o = [],
    platform: r
  } = e
  const l = o.filter(Boolean)
  const a = await (r.isRTL == null ? void 0 : r.isRTL(t))
  let c = await r.getElementRects({ reference: i, floating: t, strategy: n })
  let { x: h, y: d } = Et(c, s, a)
  let u = s
  let f = {}
  let p = 0
  for (let m = 0; m < l.length; m++) {
    const { name: g, fn: y } = l[m]
    const {
      x: b,
      y: v,
      data: O,
      reset: x
    } = await y({
      x: h,
      y: d,
      initialPlacement: s,
      placement: u,
      strategy: n,
      middlewareData: f,
      rects: c,
      platform: r,
      elements: { reference: i, floating: t }
    });
    ((h = b ?? h),
    (d = v ?? d),
    (f = { ...f, [g]: { ...f[g], ...O } }),
    x &&
                p <= 50 &&
                (p++,
                typeof x === 'object' &&
                    (x.placement && (u = x.placement),
                    x.rects &&
                        (c =
                            x.rects === !0
                              ? await r.getElementRects({
                                reference: i,
                                floating: t,
                                strategy: n
                              })
                              : x.rects),
                    ({ x: h, y: d } = Et(c, u, a))),
                (m = -1)))
  }
  return { x: h, y: d, placement: u, strategy: n, middlewareData: f }
}
async function wt (i, t) {
  let e
  t === void 0 && (t = {})
  const { x: s, y: n, platform: o, rects: r, elements: l, strategy: a } = i
  const {
    boundary: c = 'clippingAncestors',
    rootBoundary: h = 'viewport',
    elementContext: d = 'floating',
    altBoundary: u = !1,
    padding: f = 0
  } = it(t, i)
  const p = Ct(f)
  const g = l[u ? (d === 'floating' ? 'reference' : 'floating') : d]
  const y = U(
    await o.getClippingRect({
      element:
                (e = await (o.isElement == null ? void 0 : o.isElement(g))) ==
                    null || e
                  ? g
                  : g.contextElement ||
                      (await (o.getDocumentElement == null
                        ? void 0
                        : o.getDocumentElement(l.floating))),
      boundary: c,
      rootBoundary: h,
      strategy: a
    })
  )
  const b =
        d === 'floating'
          ? {
              x: s,
              y: n,
              width: r.floating.width,
              height: r.floating.height
            }
          : r.reference
  const v = await (o.getOffsetParent == null
    ? void 0
    : o.getOffsetParent(l.floating))
  const O = (await (o.isElement == null ? void 0 : o.isElement(v)))
    ? (await (o.getScale == null ? void 0 : o.getScale(v))) || {
        x: 1,
        y: 1
      }
    : { x: 1, y: 1 }
  const x = U(
    o.convertOffsetParentRelativeRectToViewportRelativeRect
      ? await o.convertOffsetParentRelativeRectToViewportRelativeRect({
        elements: l,
        rect: b,
        offsetParent: v,
        strategy: a
      })
      : b
  )
  return {
    top: (y.top - x.top + p.top) / O.y,
    bottom: (x.bottom - y.bottom + p.bottom) / O.y,
    left: (y.left - x.left + p.left) / O.x,
    right: (x.right - y.right + p.right) / O.x
  }
}
const Rt = function (i) {
  return (
    i === void 0 && (i = {}),
    {
      name: 'flip',
      options: i,
      async fn (t) {
        let e, s
        const {
          placement: n,
          middlewareData: o,
          rects: r,
          initialPlacement: l,
          platform: a,
          elements: c
        } = t
        const {
          mainAxis: h = !0,
          crossAxis: d = !0,
          fallbackPlacements: u,
          fallbackStrategy: f = 'bestFit',
          fallbackAxisSideDirection: p = 'none',
          flipAlignment: m = !0,
          ...g
        } = it(i, t)
        if ((e = o.arrow) != null && e.alignmentOffset) return {}
        const y = z(n)
        const b = B(l)
        const v = z(l) === l
        const O = await (a.isRTL == null
          ? void 0
          : a.isRTL(c.floating))
        const x = u || (v || !m ? [Z(l)] : At(l))
        const q = p !== 'none'
        !u && q && x.push(...St(l, m, p, O))
        const G = [l, ...x]
        const W = await wt(t, g)
        const L = []
        let S = ((s = o.flip) == null ? void 0 : s.overflows) || []
        if ((h && L.push(W[y]), d)) {
          const D = Ot(n, r, O)
          L.push(W[D[0]], W[D[1]])
        }
        if (
          ((S = [...S, { placement: n, overflows: L }]),
          !L.every((D) => D <= 0))
        ) {
          let H, X
          const D =
                        (((H = o.flip) == null ? void 0 : H.index) || 0) + 1
          const J = G[D]
          if (
            J &&
                        (!(d === 'alignment' ? b !== B(J) : !1) ||
                            S.every(
                              (I) =>
                                I.overflows[0] > 0 && B(I.placement) === b
                            ))
          ) {
            return {
              data: { index: D, overflows: S },
              reset: { placement: J }
            }
          }
          let R =
                        (X = S.filter((M) => M.overflows[0] <= 0).sort(
                          (M, I) => M.overflows[1] - I.overflows[1]
                        )[0]) == null
                          ? void 0
                          : X.placement
          if (!R) {
            switch (f) {
              case 'bestFit': {
                let Q
                const M =
                                    (Q = S.filter((I) => {
                                      if (q) {
                                        const V = B(I.placement)
                                        return V === b || V === 'y'
                                      }
                                      return !0
                                    })
                                      .map((I) => [
                                        I.placement,
                                        I.overflows
                                          .filter((V) => V > 0)
                                          .reduce((V, Yt) => V + Yt, 0)
                                      ])
                                      .sort((I, V) => I[1] - V[1])[0]) == null
                                      ? void 0
                                      : Q[0]
                M && (R = M)
                break
              }
              case 'initialPlacement':
                R = l
                break
            }
          }
          if (n !== R) return { reset: { placement: R } }
        }
        return {}
      }
    }
  )
}
const se = new Set(['left', 'top'])
async function ne (i, t) {
  const { placement: e, platform: s, elements: n } = i
  const o = await (s.isRTL == null ? void 0 : s.isRTL(n.floating))
  const r = z(e)
  const l = st(e)
  const a = B(e) === 'y'
  const c = se.has(r) ? -1 : 1
  const h = o && a ? -1 : 1
  const d = it(t, i)
  let {
    mainAxis: u,
    crossAxis: f,
    alignmentAxis: p
  } = typeof d === 'number'
    ? { mainAxis: d, crossAxis: 0, alignmentAxis: null }
    : {
        mainAxis: d.mainAxis || 0,
        crossAxis: d.crossAxis || 0,
        alignmentAxis: d.alignmentAxis
      }
  return (
    l && typeof p === 'number' && (f = l === 'end' ? p * -1 : p),
    a ? { x: f * h, y: u * c } : { x: u * c, y: f * h }
  )
}
const It = function (i) {
  return (
    i === void 0 && (i = 0),
    {
      name: 'offset',
      options: i,
      async fn (t) {
        let e, s
        const { x: n, y: o, placement: r, middlewareData: l } = t
        const a = await ne(t, i)
        return r === ((e = l.offset) == null ? void 0 : e.placement) &&
                    (s = l.arrow) != null &&
                    s.alignmentOffset
          ? {}
          : {
              x: n + a.x,
              y: o + a.y,
              data: { ...a, placement: r }
            }
      }
    }
  )
}
const Tt = function (i) {
  return (
    i === void 0 && (i = {}),
    {
      name: 'shift',
      options: i,
      async fn (t) {
        const { x: e, y: s, placement: n } = t
        const {
          mainAxis: o = !0,
          crossAxis: r = !1,
          limiter: l = {
            fn: (g) => {
              const { x: y, y: b } = g
              return { x: y, y: b }
            }
          },
          ...a
        } = it(i, t)
        const c = { x: e, y: s }
        const h = await wt(t, a)
        const d = B(z(n))
        const u = mt(d)
        let f = c[u]
        let p = c[d]
        if (o) {
          const g = u === 'y' ? 'top' : 'left'
          const y = u === 'y' ? 'bottom' : 'right'
          const b = f + h[g]
          const v = f - h[y]
          f = pt(b, f, v)
        }
        if (r) {
          const g = d === 'y' ? 'top' : 'left'
          const y = d === 'y' ? 'bottom' : 'right'
          const b = p + h[g]
          const v = p - h[y]
          p = pt(b, p, v)
        }
        const m = l.fn({ ...t, [u]: f, [d]: p })
        return {
          ...m,
          data: {
            x: m.x - e,
            y: m.y - s,
            enabled: { [u]: o, [d]: r }
          }
        }
      }
    }
  )
}
function ct () {
  return typeof window < 'u'
}
function K (i) {
  return Pt(i) ? (i.nodeName || '').toLowerCase() : '#document'
}
function A (i) {
  let t
  return (
    (i == null || (t = i.ownerDocument) == null ? void 0 : t.defaultView) ||
        window
  )
}
function P (i) {
  let t
  return (t = (Pt(i) ? i.ownerDocument : i.document) || window.document) ==
        null
    ? void 0
    : t.documentElement
}
function Pt (i) {
  return ct() ? i instanceof Node || i instanceof A(i).Node : !1
}
function C (i) {
  return ct() ? i instanceof Element || i instanceof A(i).Element : !1
}
function k (i) {
  return ct()
    ? i instanceof HTMLElement || i instanceof A(i).HTMLElement
    : !1
}
function kt (i) {
  return !ct() || typeof ShadowRoot > 'u'
    ? !1
    : i instanceof ShadowRoot || i instanceof A(i).ShadowRoot
}
const oe = new Set(['inline', 'contents'])
function j (i) {
  const { overflow: t, overflowX: e, overflowY: s, display: n } = E(i)
  return /auto|scroll|overlay|hidden|clip/.test(t + s + e) && !oe.has(n)
}
const re = new Set(['table', 'td', 'th'])
function Mt (i) {
  return re.has(K(i))
}
const le = [':popover-open', ':modal']
function nt (i) {
  return le.some((t) => {
    try {
      return i.matches(t)
    } catch {
      return !1
    }
  })
}
const ae = ['transform', 'translate', 'scale', 'rotate', 'perspective']
const ce = [
  'transform',
  'translate',
  'scale',
  'rotate',
  'perspective',
  'filter'
]
const de = ['paint', 'layout', 'strict', 'content']
function dt (i) {
  const t = ht()
  const e = C(i) ? E(i) : i
  return (
    ae.some((s) => (e[s] ? e[s] !== 'none' : !1)) ||
        (e.containerType ? e.containerType !== 'normal' : !1) ||
        (!t && (e.backdropFilter ? e.backdropFilter !== 'none' : !1)) ||
        (!t && (e.filter ? e.filter !== 'none' : !1)) ||
        ce.some((s) => (e.willChange || '').includes(s)) ||
        de.some((s) => (e.contain || '').includes(s))
  )
}
function Bt (i) {
  let t = N(i)
  for (; k(t) && !_(t);) {
    if (dt(t)) return t
    if (nt(t)) return null
    t = N(t)
  }
  return null
}
function ht () {
  return typeof CSS > 'u' || !CSS.supports
    ? !1
    : CSS.supports('-webkit-backdrop-filter', 'none')
}
const he = new Set(['html', 'body', '#document'])
function _ (i) {
  return he.has(K(i))
}
function E (i) {
  return A(i).getComputedStyle(i)
}
function ot (i) {
  return C(i)
    ? { scrollLeft: i.scrollLeft, scrollTop: i.scrollTop }
    : { scrollLeft: i.scrollX, scrollTop: i.scrollY }
}
function N (i) {
  if (K(i) === 'html') return i
  const t = i.assignedSlot || i.parentNode || (kt(i) && i.host) || P(i)
  return kt(t) ? t.host : t
}
function Nt (i) {
  const t = N(i)
  return _(t)
    ? i.ownerDocument
      ? i.ownerDocument.body
      : i.body
    : k(t) && j(t)
      ? t
      : Nt(t)
}
function at (i, t, e) {
  let s;
  (t === void 0 && (t = []), e === void 0 && (e = !0))
  const n = Nt(i)
  const o = n === ((s = i.ownerDocument) == null ? void 0 : s.body)
  const r = A(n)
  if (o) {
    const l = ft(r)
    return t.concat(
      r,
      r.visualViewport || [],
      j(n) ? n : [],
      l && e ? at(l) : []
    )
  }
  return t.concat(n, at(n, [], e))
}
function ft (i) {
  return i.parent && Object.getPrototypeOf(i.parent) ? i.frameElement : null
}
function Vt (i) {
  const t = E(i)
  let e = parseFloat(t.width) || 0
  let s = parseFloat(t.height) || 0
  const n = k(i)
  const o = n ? i.offsetWidth : e
  const r = n ? i.offsetHeight : s
  const l = et(e) !== o || et(s) !== r
  return (l && ((e = o), (s = r)), { width: e, height: s, $: l })
}
function $t (i) {
  return C(i) ? i : i.contextElement
}
function Y (i) {
  const t = $t(i)
  if (!k(t)) return T(1)
  const e = t.getBoundingClientRect()
  const { width: s, height: n, $: o } = Vt(t)
  let r = (o ? et(e.width) : e.width) / s
  let l = (o ? et(e.height) : e.height) / n
  return (
    (!r || !Number.isFinite(r)) && (r = 1),
    (!l || !Number.isFinite(l)) && (l = 1),
    { x: r, y: l }
  )
}
const fe = T(0)
function zt (i) {
  const t = A(i)
  return !ht() || !t.visualViewport
    ? fe
    : { x: t.visualViewport.offsetLeft, y: t.visualViewport.offsetTop }
}
function ue (i, t, e) {
  return (t === void 0 && (t = !1), !e || (t && e !== A(i)) ? !1 : t)
}
function rt (i, t, e, s) {
  (t === void 0 && (t = !1), e === void 0 && (e = !1))
  const n = i.getBoundingClientRect()
  const o = $t(i)
  let r = T(1)
  t && (s ? C(s) && (r = Y(s)) : (r = Y(i)))
  const l = ue(o, e, s) ? zt(o) : T(0)
  let a = (n.left + l.x) / r.x
  let c = (n.top + l.y) / r.y
  let h = n.width / r.x
  let d = n.height / r.y
  if (o) {
    const u = A(o)
    const f = s && C(s) ? A(s) : s
    let p = u
    let m = ft(p)
    for (; m && s && f !== p;) {
      const g = Y(m)
      const y = m.getBoundingClientRect()
      const b = E(m)
      const v = y.left + (m.clientLeft + parseFloat(b.paddingLeft)) * g.x
      const O = y.top + (m.clientTop + parseFloat(b.paddingTop)) * g.y;
      ((a *= g.x),
      (c *= g.y),
      (h *= g.x),
      (d *= g.y),
      (a += v),
      (c += O),
      (p = A(m)),
      (m = ft(p)))
    }
  }
  return U({ width: h, height: d, x: a, y: c })
}
function xt (i, t) {
  const e = ot(i).scrollLeft
  return t ? t.left + e : rt(P(i)).left + e
}
function Wt (i, t, e) {
  e === void 0 && (e = !1)
  const s = i.getBoundingClientRect()
  const n = s.left + t.scrollLeft - (e ? 0 : xt(i, s))
  const o = s.top + t.scrollTop
  return { x: n, y: o }
}
function pe (i) {
  const { elements: t, rect: e, offsetParent: s, strategy: n } = i
  const o = n === 'fixed'
  const r = P(s)
  const l = t ? nt(t.floating) : !1
  if (s === r || (l && o)) return e
  let a = { scrollLeft: 0, scrollTop: 0 }
  let c = T(1)
  const h = T(0)
  const d = k(s)
  if ((d || (!d && !o)) && ((K(s) !== 'body' || j(r)) && (a = ot(s)), k(s))) {
    const f = rt(s);
    ((c = Y(s)), (h.x = f.x + s.clientLeft), (h.y = f.y + s.clientTop))
  }
  const u = r && !d && !o ? Wt(r, a, !0) : T(0)
  return {
    width: e.width * c.x,
    height: e.height * c.y,
    x: e.x * c.x - a.scrollLeft * c.x + h.x + u.x,
    y: e.y * c.y - a.scrollTop * c.y + h.y + u.y
  }
}
function me (i) {
  return Array.from(i.getClientRects())
}
function ge (i) {
  const t = P(i)
  const e = ot(i)
  const s = i.ownerDocument.body
  const n = $(t.scrollWidth, t.clientWidth, s.scrollWidth, s.clientWidth)
  const o = $(t.scrollHeight, t.clientHeight, s.scrollHeight, s.clientHeight)
  let r = -e.scrollLeft + xt(i)
  const l = -e.scrollTop
  return (
    E(s).direction === 'rtl' && (r += $(t.clientWidth, s.clientWidth) - n),
    { width: n, height: o, x: r, y: l }
  )
}
function be (i, t) {
  const e = A(i)
  const s = P(i)
  const n = e.visualViewport
  let o = s.clientWidth
  let r = s.clientHeight
  let l = 0
  let a = 0
  if (n) {
    ((o = n.width), (r = n.height))
    const c = ht();
    (!c || (c && t === 'fixed')) && ((l = n.offsetLeft), (a = n.offsetTop))
  }
  return { width: o, height: r, x: l, y: a }
}
const we = new Set(['absolute', 'fixed'])
function ye (i, t) {
  const e = rt(i, !0, t === 'fixed')
  const s = e.top + i.clientTop
  const n = e.left + i.clientLeft
  const o = k(i) ? Y(i) : T(1)
  const r = i.clientWidth * o.x
  const l = i.clientHeight * o.y
  const a = n * o.x
  const c = s * o.y
  return { width: r, height: l, x: a, y: c }
}
function Ft (i, t, e) {
  let s
  if (t === 'viewport') s = be(i, e)
  else if (t === 'document') s = ge(P(i))
  else if (C(t)) s = ye(t, e)
  else {
    const n = zt(i)
    s = { x: t.x - n.x, y: t.y - n.y, width: t.width, height: t.height }
  }
  return U(s)
}
function Ut (i, t) {
  const e = N(i)
  return e === t || !C(e) || _(e)
    ? !1
    : E(e).position === 'fixed' || Ut(e, t)
}
function xe (i, t) {
  const e = t.get(i)
  if (e) return e
  let s = at(i, [], !1).filter((l) => C(l) && K(l) !== 'body')
  let n = null
  const o = E(i).position === 'fixed'
  let r = o ? N(i) : i
  for (; C(r) && !_(r);) {
    const l = E(r)
    const a = dt(r);
    (!a && l.position === 'fixed' && (n = null),
    (
      o
        ? !a && !n
        : (!a &&
                          l.position === 'static' &&
                          !!n &&
                          we.has(n.position)) ||
                      (j(r) && !a && Ut(i, r))
    )
      ? (s = s.filter((h) => h !== r))
      : (n = l),
    (r = N(r)))
  }
  return (t.set(i, s), s)
}
function ve (i) {
  const { element: t, boundary: e, rootBoundary: s, strategy: n } = i
  const r = [
    ...(e === 'clippingAncestors'
      ? nt(t)
        ? []
        : xe(t, this._c)
      : [].concat(e)),
    s
  ]
  const l = r[0]
  const a = r.reduce(
    (c, h) => {
      const d = Ft(t, h, n)
      return (
        (c.top = $(d.top, c.top)),
        (c.right = tt(d.right, c.right)),
        (c.bottom = tt(d.bottom, c.bottom)),
        (c.left = $(d.left, c.left)),
        c
      )
    },
    Ft(t, l, n)
  )
  return {
    width: a.right - a.left,
    height: a.bottom - a.top,
    x: a.left,
    y: a.top
  }
}
function Le (i) {
  const { width: t, height: e } = Vt(i)
  return { width: t, height: e }
}
function Oe (i, t, e) {
  const s = k(t)
  const n = P(t)
  const o = e === 'fixed'
  const r = rt(i, !0, o, t)
  let l = { scrollLeft: 0, scrollTop: 0 }
  const a = T(0)
  function c () {
    a.x = xt(n)
  }
  if (s || (!s && !o)) {
    if (((K(t) !== 'body' || j(n)) && (l = ot(t)), s)) {
      const f = rt(t, !0, o, t);
      ((a.x = f.x + t.clientLeft), (a.y = f.y + t.clientTop))
    } else n && c()
  }
  o && !s && n && c()
  const h = n && !s && !o ? Wt(n, l) : T(0)
  const d = r.left + l.scrollLeft - a.x - h.x
  const u = r.top + l.scrollTop - a.y - h.y
  return { x: d, y: u, width: r.width, height: r.height }
}
function yt (i) {
  return E(i).position === 'static'
}
function Ht (i, t) {
  if (!k(i) || E(i).position === 'fixed') return null
  if (t) return t(i)
  let e = i.offsetParent
  return (P(i) === e && (e = e.ownerDocument.body), e)
}
function Kt (i, t) {
  const e = A(i)
  if (nt(i)) return e
  if (!k(i)) {
    let n = N(i)
    for (; n && !_(n);) {
      if (C(n) && !yt(n)) return n
      n = N(n)
    }
    return e
  }
  let s = Ht(i, t)
  for (; s && Mt(s) && yt(s);) s = Ht(s, t)
  return s && _(s) && yt(s) && !dt(s) ? e : s || Bt(i) || e
}
const Ae = async function (i) {
  const t = this.getOffsetParent || Kt
  const e = this.getDimensions
  const s = await e(i.floating)
  return {
    reference: Oe(i.reference, await t(i.floating), i.strategy),
    floating: { x: 0, y: 0, width: s.width, height: s.height }
  }
}
function Se (i) {
  return E(i).direction === 'rtl'
}
const Ce = {
  convertOffsetParentRelativeRectToViewportRelativeRect: pe,
  getDocumentElement: P,
  getClippingRect: ve,
  getOffsetParent: Kt,
  getElementRects: Ae,
  getClientRects: me,
  getDimensions: Le,
  getScale: Y,
  isElement: C,
  isRTL: Se
}
const _t = It
const qt = Tt
const Jt = Rt
const jt = (i, t, e) => {
  const s = new Map()
  const n = { platform: Ce, ...e }
  const o = { ...n.platform, _c: s }
  return Dt(i, t, { ...n, platform: o })
}
function F (i) {
  return i == null || i === '' || (typeof i === 'string' && i.trim() === '')
}
function w (i) {
  return !F(i)
}
const ut = class {
  constructor ({
    element: t,
    options: e,
    placeholder: s,
    state: n,
    canOptionLabelsWrap: o = !0,
    canSelectPlaceholder: r = !0,
    initialOptionLabel: l = null,
    initialOptionLabels: a = null,
    initialState: c = null,
    isHtmlAllowed: h = !1,
    isAutofocused: d = !1,
    isDisabled: u = !1,
    isMultiple: f = !1,
    isSearchable: p = !1,
    getOptionLabelUsing: m = null,
    getOptionLabelsUsing: g = null,
    getOptionsUsing: y = null,
    getSearchResultsUsing: b = null,
    hasDynamicOptions: v = !1,
    hasDynamicSearchResults: O = !0,
    searchPrompt: x = 'Search...',
    searchDebounce: q = 1e3,
    loadingMessage: G = 'Loading...',
    searchingMessage: W = 'Searching...',
    noSearchResultsMessage: L = 'No results found',
    maxItems: S = null,
    maxItemsMessage: H = 'Maximum number of items selected',
    optionsLimit: X = null,
    position: Q = null,
    searchableOptionFields: D = ['label'],
    livewireId: J = null,
    statePath: R = null,
    onStateChange: M = () => {}
  }) {
    ((this.element = t),
    (this.options = e),
    (this.originalOptions = JSON.parse(JSON.stringify(e))),
    (this.placeholder = s),
    (this.state = n),
    (this.canOptionLabelsWrap = o),
    (this.canSelectPlaceholder = r),
    (this.initialOptionLabel = l),
    (this.initialOptionLabels = a),
    (this.initialState = c),
    (this.isHtmlAllowed = h),
    (this.isAutofocused = d),
    (this.isDisabled = u),
    (this.isMultiple = f),
    (this.isSearchable = p),
    (this.getOptionLabelUsing = m),
    (this.getOptionLabelsUsing = g),
    (this.getOptionsUsing = y),
    (this.getSearchResultsUsing = b),
    (this.hasDynamicOptions = v),
    (this.hasDynamicSearchResults = O),
    (this.searchPrompt = x),
    (this.searchDebounce = q),
    (this.loadingMessage = G),
    (this.searchingMessage = W),
    (this.noSearchResultsMessage = L),
    (this.maxItems = S),
    (this.maxItemsMessage = H),
    (this.optionsLimit = X),
    (this.position = Q),
    (this.searchableOptionFields = Array.isArray(D) ? D : ['label']),
    (this.livewireId = J),
    (this.statePath = R),
    (this.onStateChange = M),
    (this.labelRepository = {}),
    (this.isOpen = !1),
    (this.selectedIndex = -1),
    (this.searchQuery = ''),
    (this.searchTimeout = null),
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
                      e.forEach((s) => {
                        s.classList.remove('fi-selected')
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
    let s = 0
    let n = !1;
    (this.options.forEach((l) => {
      l.options && Array.isArray(l.options)
        ? ((s += l.options.length), (n = !0))
        : s++
    }),
    n
      ? (this.optionsList.className = 'fi-select-input-options-ctn')
      : s > 0 && (this.optionsList.className = 'fi-dropdown-list'))
    let o = n ? null : this.optionsList
    let r = 0
    for (const l of e) {
      if (this.optionsLimit && r >= this.optionsLimit) break
      if (l.options && Array.isArray(l.options)) {
        let a = l.options
        if (
          (this.isMultiple &&
                        Array.isArray(this.state) &&
                        this.state.length > 0 &&
                        (a = l.options.filter(
                          (c) => !this.state.includes(c.value)
                        )),
          a.length > 0)
        ) {
          if (this.optionsLimit) {
            const c = this.optionsLimit - r
            c < a.length && (a = a.slice(0, c))
          }
          (this.renderOptionGroup(l.label, a),
          (r += a.length),
          (t += a.length))
        }
      } else {
        if (
          this.isMultiple &&
                    Array.isArray(this.state) &&
                    this.state.includes(l.value)
        ) {
          continue
        }
        !o &&
                    n &&
                    ((o = document.createElement('ul')),
                    (o.className = 'fi-dropdown-list'),
                    this.optionsList.appendChild(o))
        const a = this.createOptionElement(l.value, l);
        (o.appendChild(a), r++, t++)
      }
    }
    t === 0
      ? (this.searchQuery
          ? this.showNoResultsMessage()
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
    const s = document.createElement('li')
    s.className = 'fi-select-input-option-group'
    const n = document.createElement('div');
    ((n.className = 'fi-dropdown-header'), (n.textContent = t))
    const o = document.createElement('ul');
    ((o.className = 'fi-dropdown-list'),
    e.forEach((r) => {
      const l = this.createOptionElement(r.value, r)
      o.appendChild(l)
    }),
    s.appendChild(n),
    s.appendChild(o),
    this.optionsList.appendChild(s))
  }

  createOptionElement (t, e) {
    let s = t
    let n = e
    let o = !1
    typeof e === 'object' &&
            e !== null &&
            'label' in e &&
            'value' in e &&
            ((s = e.value), (n = e.label), (o = e.isDisabled || !1))
    const r = document.createElement('li');
    ((r.className = 'fi-dropdown-list-item fi-select-input-option'),
    o && r.classList.add('fi-disabled'))
    const l = `fi-select-input-option-${Math.random().toString(36).substring(2, 11)}`
    if (
      ((r.id = l),
      r.setAttribute('role', 'option'),
      r.setAttribute('data-value', s),
      r.setAttribute('tabindex', '0'),
      o && r.setAttribute('aria-disabled', 'true'),
      this.isHtmlAllowed && typeof n === 'string')
    ) {
      const h = document.createElement('div')
      h.innerHTML = n
      const d = h.textContent || h.innerText || n
      r.setAttribute('aria-label', d)
    }
    const a = this.isMultiple
      ? Array.isArray(this.state) && this.state.includes(s)
      : this.state === s;
    (r.setAttribute('aria-selected', a ? 'true' : 'false'),
    a && r.classList.add('fi-selected'))
    const c = document.createElement('span')
    return (
      this.isHtmlAllowed ? (c.innerHTML = n) : (c.textContent = n),
      r.appendChild(c),
      o ||
                r.addEventListener('click', (h) => {
                  (h.preventDefault(),
                  h.stopPropagation(),
                  this.selectOption(s),
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
    if (((this.selectedDisplay.innerHTML = ''), this.isMultiple)) {
      if (!Array.isArray(this.state) || this.state.length === 0) {
        const s = document.createElement('span');
        ((s.textContent = this.placeholder),
        s.classList.add('fi-select-input-placeholder'),
        this.selectedDisplay.appendChild(s))
        return
      }
      const e = await this.getLabelsForMultipleSelection();
      (this.addBadgesForSelectedOptions(e),
      this.isOpen && this.positionDropdown())
      return
    }
    if (this.state === null || this.state === '') {
      const e = document.createElement('span');
      ((e.textContent = this.placeholder),
      e.classList.add('fi-select-input-placeholder'),
      this.selectedDisplay.appendChild(e))
      return
    }
    const t = await this.getLabelForSingleSelection()
    this.addSingleSelectionDisplay(t)
  }

  async getLabelsForMultipleSelection () {
    const t = this.getSelectedOptionLabels()
    const e = []
    if (Array.isArray(this.state)) {
      for (const n of this.state) {
        if (!w(this.labelRepository[n])) {
          if (w(t[n])) {
            this.labelRepository[n] = t[n]
            continue
          }
          e.push(n.toString())
        }
      }
    }
    if (
      e.length > 0 &&
            w(this.initialOptionLabels) &&
            JSON.stringify(this.state) === JSON.stringify(this.initialState)
    ) {
      if (Array.isArray(this.initialOptionLabels)) {
        for (const n of this.initialOptionLabels) {
          w(n) &&
                        n.value !== void 0 &&
                        n.label !== void 0 &&
                        e.includes(n.value) &&
                        (this.labelRepository[n.value] = n.label)
        }
      }
    } else if (e.length > 0 && this.getOptionLabelsUsing) {
      try {
        const n = await this.getOptionLabelsUsing()
        for (const o of n) {
          w(o) &&
                        o.value !== void 0 &&
                        o.label !== void 0 &&
                        (this.labelRepository[o.value] = o.label)
        }
      } catch (n) {
        console.error('Error fetching option labels:', n)
      }
    }
    const s = []
    if (Array.isArray(this.state)) {
      for (const n of this.state) {
        w(this.labelRepository[n])
          ? s.push(this.labelRepository[n])
          : w(t[n])
            ? s.push(t[n])
            : s.push(n)
      }
    }
    return s
  }

  createBadgeElement (t, e) {
    const s = document.createElement('span');
    ((s.className =
            'fi-badge fi-size-md fi-color fi-color-primary fi-text-color-600 dark:fi-text-color-200'),
    w(t) && s.setAttribute('data-value', t))
    const n = document.createElement('span')
    n.className = 'fi-badge-label-ctn'
    const o = document.createElement('span');
    ((o.className = 'fi-badge-label'),
    this.canOptionLabelsWrap && o.classList.add('fi-wrapped'),
    this.isHtmlAllowed ? (o.innerHTML = e) : (o.textContent = e),
    n.appendChild(o),
    s.appendChild(n))
    const r = this.createRemoveButton(t, e)
    return (s.appendChild(r), s)
  }

  createRemoveButton (t, e) {
    const s = document.createElement('button')
    return (
      (s.type = 'button'),
      (s.className = 'fi-badge-delete-btn'),
      (s.innerHTML =
                '<svg class="fi-icon fi-size-xs" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" data-slot="icon"><path d="M5.28 4.22a.75.75 0 0 0-1.06 1.06L6.94 8l-2.72 2.72a.75.75 0 1 0 1.06 1.06L8 9.06l2.72 2.72a.75.75 0 1 0 1.06-1.06L9.06 8l2.72-2.72a.75.75 0 0 0-1.06-1.06L8 6.94 5.28 4.22Z"></path></svg>'),
      s.setAttribute(
        'aria-label',
        'Remove ' +
                    (this.isHtmlAllowed ? e.replace(/<[^>]*>/g, '') : e)
      ),
      s.addEventListener('click', (n) => {
        (n.stopPropagation(), w(t) && this.selectOption(t))
      }),
      s.addEventListener('keydown', (n) => {
        (n.key === ' ' || n.key === 'Enter') &&
                    (n.preventDefault(),
                    n.stopPropagation(),
                    w(t) && this.selectOption(t))
      }),
      s
    )
  }

  addBadgesForSelectedOptions (t) {
    const e = document.createElement('div');
    ((e.className = 'fi-select-input-value-badges-ctn'),
    t.forEach((s, n) => {
      const o = Array.isArray(this.state) ? this.state[n] : null
      const r = this.createBadgeElement(o, s)
      e.appendChild(r)
    }),
    this.selectedDisplay.appendChild(e))
  }

  async getLabelForSingleSelection () {
    let t = this.labelRepository[this.state]
    if (
      (F(t) && (t = this.getSelectedOptionLabel(this.state)),
      F(t) &&
                w(this.initialOptionLabel) &&
                this.state === this.initialState)
    ) {
      ((t = this.initialOptionLabel),
      w(this.state) && (this.labelRepository[this.state] = t))
    } else if (F(t) && this.getOptionLabelUsing) {
      try {
        ((t = await this.getOptionLabelUsing()),
        w(t) &&
                        w(this.state) &&
                        (this.labelRepository[this.state] = t))
      } catch (e) {
        (console.error('Error fetching option label:', e),
        (t = this.state))
      }
    } else F(t) && (t = this.state)
    return t
  }

  addSingleSelectionDisplay (t) {
    const e = document.createElement('span')
    if (
      ((e.className = 'fi-select-input-value-label'),
      this.isHtmlAllowed ? (e.innerHTML = t) : (e.textContent = t),
      this.selectedDisplay.appendChild(e),
      !this.canSelectPlaceholder)
    ) {
      return
    }
    const s = document.createElement('button');
    ((s.type = 'button'),
    (s.className = 'fi-select-input-value-remove-btn'),
    (s.innerHTML =
                '<svg class="fi-icon fi-size-sm" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>'),
    s.setAttribute('aria-label', 'Clear selection'),
    s.addEventListener('click', (n) => {
      (n.stopPropagation(), this.selectOption(''))
    }),
    s.addEventListener('keydown', (n) => {
      (n.key === ' ' || n.key === 'Enter') &&
                    (n.preventDefault(),
                    n.stopPropagation(),
                    this.selectOption(''))
    }),
    this.selectedDisplay.appendChild(s))
  }

  getSelectedOptionLabel (t) {
    if (w(this.labelRepository[t])) return this.labelRepository[t]
    let e = ''
    for (const s of this.options) {
      if (s.options && Array.isArray(s.options)) {
        for (const n of s.options) {
          if (n.value === t) {
            ((e = n.label), (this.labelRepository[t] = e))
            break
          }
        }
      } else if (s.value === t) {
        ((e = s.label), (this.labelRepository[t] = e))
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
                        w(this.state)
                  ) {
                    try {
                      delete this.labelRepository[this.state]
                      const e = await this.getOptionLabelUsing()
                      w(e) && (this.labelRepository[this.state] = e)
                      const s = this.selectedDisplay.querySelector(
                        '.fi-select-input-value-label'
                      );
                      (w(s) &&
                                (this.isHtmlAllowed
                                  ? (s.innerHTML = e)
                                  : (s.textContent = e)),
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
    const s = this.getVisibleOptions()
    for (const n of s) {
      if (n.getAttribute('data-value') === String(t)) {
        if (((n.innerHTML = ''), this.isHtmlAllowed)) {
          const o = document.createElement('span');
          ((o.innerHTML = e), n.appendChild(o))
        } else n.appendChild(document.createTextNode(e))
        break
      }
    }
    for (const n of this.options) {
      if (n.options && Array.isArray(n.options)) {
        for (const o of n.options) {
          if (o.value === t) {
            o.label = e
            break
          }
        }
      } else if (n.value === t) {
        n.label = e
        break
      }
    }
    for (const n of this.originalOptions) {
      if (n.options && Array.isArray(n.options)) {
        for (const o of n.options) {
          if (o.value === t) {
            o.label = e
            break
          }
        }
      } else if (n.value === t) {
        n.label = e
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
            this.selectButton.closest('.fi-absolute-positioning-context') !==
            null
    if (
      ((this.dropdown.style.position = t ? 'absolute' : 'fixed'),
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
      this.hasDynamicOptions && this.getOptionsUsing)
    ) {
      this.showLoadingState(!1)
      try {
        const e = await this.getOptionsUsing();
        ((this.options = e),
        (this.originalOptions = JSON.parse(JSON.stringify(e))),
        this.populateLabelRepositoryFromOptions(e),
        this.renderOptions())
      } catch (e) {
        (console.error('Error fetching options:', e),
        this.hideLoadingState())
      }
    }
    if ((this.hideLoadingState(), this.isSearchable && this.searchInput)) {
      ((this.searchInput.value = ''),
      this.searchInput.focus(),
      (this.searchQuery = ''),
      (this.options = JSON.parse(
        JSON.stringify(this.originalOptions)
      )),
      this.renderOptions())
    } else {
      this.selectedIndex = -1
      const e = this.getVisibleOptions()
      if (this.isMultiple) {
        if (Array.isArray(this.state) && this.state.length > 0) {
          for (let s = 0; s < e.length; s++) {
            if (
              this.state.includes(e[s].getAttribute('data-value'))
            ) {
              this.selectedIndex = s
              break
            }
          }
        }
      } else {
        for (let s = 0; s < e.length; s++) {
          if (e[s].getAttribute('data-value') === this.state) {
            this.selectedIndex = s
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
    const e = [_t(4), qt({ padding: 5 })]
    this.position !== 'top' && this.position !== 'bottom' && e.push(Jt())
    const s =
            this.selectButton.closest('.fi-absolute-positioning-context') !==
            null
    jt(this.selectButton, this.dropdown, {
      placement: t,
      middleware: e,
      strategy: s ? 'absolute' : 'fixed'
    }).then(({ x: n, y: o }) => {
      Object.assign(this.dropdown.style, {
        left: `${n}px`,
        top: `${o}px`
      })
    })
  }

  closeDropdown () {
    ((this.dropdown.style.display = 'none'),
    this.selectButton.setAttribute('aria-expanded', 'false'),
    (this.isOpen = !1),
    this.resizeListener &&
                (window.removeEventListener('resize', this.resizeListener),
                (this.resizeListener = null)),
    this.scrollListener &&
                (window.removeEventListener('scroll', this.scrollListener, !0),
                (this.scrollListener = null)),
    this.getVisibleOptions().forEach((e) => {
      e.classList.remove('fi-selected')
    }))
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
    const s = t.getBoundingClientRect()
    s.bottom > e.bottom
      ? (this.dropdown.scrollTop += s.bottom - e.bottom)
      : s.top < e.top && (this.dropdown.scrollTop -= e.top - s.top)
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
      let s = !1
      for (const n of this.options) {
        if (n.options && Array.isArray(n.options)) {
          for (const o of n.options) {
            if (o.value === e) {
              ((t[e] = o.label), (s = !0))
              break
            }
          }
          if (s) break
        } else if (n.value === e) {
          ((t[e] = n.label), (s = !0))
          break
        }
      }
    }
    return t
  }

  handleSearch (t) {
    const e = t.target.value.trim().toLowerCase()
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
      try {
        this.showLoadingState(!0)
        const s = await this.getSearchResultsUsing(e);
        ((this.options = s),
        this.populateLabelRepositoryFromOptions(s),
        this.hideLoadingState(),
        this.renderOptions(),
        this.isOpen && this.positionDropdown(),
        this.options.length === 0 && this.showNoResultsMessage())
      } catch (s) {
        (console.error('Error fetching search results:', s),
        this.hideLoadingState(),
        (this.options = JSON.parse(
          JSON.stringify(this.originalOptions)
        )),
        this.renderOptions())
      }
    }, this.searchDebounce)
  }

  showLoadingState (t = !1) {
    (this.optionsList.parentNode === this.dropdown &&
            (this.optionsList.innerHTML = ''),
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

  showNoResultsMessage () {
    (this.optionsList.parentNode === this.dropdown &&
            this.optionsList.children.length > 0 &&
            (this.optionsList.innerHTML = ''),
    this.hideLoadingState())
    const t = document.createElement('div');
    ((t.className = 'fi-select-input-message'),
    (t.textContent = this.noSearchResultsMessage),
    this.dropdown.appendChild(t))
  }

  filterOptions (t) {
    const e = this.searchableOptionFields.includes('label')
    const s = this.searchableOptionFields.includes('value')
    const n = []
    for (const o of this.originalOptions) {
      if (o.options && Array.isArray(o.options)) {
        const r = o.options.filter(
          (l) =>
            (e && l.label.toLowerCase().includes(t)) ||
                        (s && String(l.value).toLowerCase().includes(t))
        )
        r.length > 0 && n.push({ label: o.label, options: r })
      } else {
        ((e && o.label.toLowerCase().includes(t)) ||
                    (s && String(o.value).toLowerCase().includes(t))) &&
                    n.push(o)
      }
    }
    ((this.options = n),
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
      const n = this.selectedDisplay.querySelector(`[data-value="${t}"]`)
      if (w(n)) {
        const o = n.parentElement
        w(o) && o.children.length === 1
          ? ((e = e.filter((r) => r !== t)),
            (this.state = e),
            this.updateSelectedDisplay())
          : (n.remove(),
            (e = e.filter((r) => r !== t)),
            (this.state = e))
      } else {
        ((e = e.filter((o) => o !== t)),
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
    const s = this.selectedDisplay.querySelector(
      '.fi-select-input-value-badges-ctn'
    );
    (F(s) ? this.updateSelectedDisplay() : this.addSingleBadge(t, s),
    this.renderOptions(),
    this.isOpen && this.positionDropdown(),
    this.maintainFocusInMultipleMode(),
    this.onStateChange(this.state))
  }

  async addSingleBadge (t, e) {
    let s = this.labelRepository[t]
    if (
      (F(s) &&
                ((s = this.getSelectedOptionLabel(t)),
                w(s) && (this.labelRepository[t] = s)),
      F(s) && this.getOptionLabelsUsing)
    ) {
      try {
        const o = await this.getOptionLabelsUsing()
        for (const r of o) {
          if (w(r) && r.value === t && r.label !== void 0) {
            ((s = r.label), (this.labelRepository[t] = s))
            break
          }
        }
      } catch (o) {
        console.error('Error fetching option label:', o)
      }
    }
    F(s) && (s = t)
    const n = this.createBadgeElement(t, s)
    e.appendChild(n)
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
function Ee ({
  canOptionLabelsWrap: i,
  canSelectPlaceholder: t,
  getOptionLabelUsing: e,
  getOptionsUsing: s,
  getSearchResultsUsing: n,
  hasDynamicOptions: o,
  hasDynamicSearchResults: r,
  initialOptionLabel: l,
  isDisabled: a,
  isHtmlAllowed: c,
  isNative: h,
  isSearchable: d,
  loadingMessage: u,
  name: f,
  noSearchResultsMessage: p,
  options: m,
  optionsLimit: g,
  placeholder: y,
  position: b,
  recordKey: v,
  searchableOptionFields: O,
  searchDebounce: x,
  searchingMessage: q,
  searchPrompt: G,
  state: W
}) {
  return {
    error: void 0,
    isLoading: !1,
    select: null,
    state: W,
    init () {
      (h ||
                (this.select = new ut({
                  element: this.$refs.select,
                  options: m,
                  placeholder: y,
                  state: this.state,
                  canOptionLabelsWrap: i,
                  canSelectPlaceholder: t,
                  initialOptionLabel: l,
                  isHtmlAllowed: c,
                  isDisabled: a,
                  isSearchable: d,
                  getOptionLabelUsing: e,
                  getOptionsUsing: s,
                  getSearchResultsUsing: n,
                  hasDynamicOptions: o,
                  hasDynamicSearchResults: r,
                  searchPrompt: G,
                  searchDebounce: x,
                  loadingMessage: u,
                  searchingMessage: q,
                  noSearchResultsMessage: p,
                  optionsLimit: g,
                  position: b,
                  searchableOptionFields: O,
                  onStateChange: (L) => {
                    this.state = L
                  }
                })),
      Livewire.hook(
        'commit',
        ({
          component: L,
          commit: S,
          succeed: H,
          fail: X,
          respond: Q
        }) => {
          H(({ snapshot: D, effect: J }) => {
            this.$nextTick(() => {
              if (
                this.isLoading ||
                                    L.id !==
                                        this.$root.closest('[wire\\:id]')
                                          ?.attributes['wire:id'].value
              ) {
                return
              }
              const R = this.getServerState()
              R === void 0 ||
                                    this.getNormalizedState() === R ||
                                    (this.state = R)
            })
          })
        }
      ),
      this.$watch('state', async (L) => {
        !h &&
                        this.select &&
                        this.select.state !== L &&
                        ((this.select.state = L),
                        this.select.updateSelectedDisplay(),
                        this.select.renderOptions())
        const S = this.getServerState()
        if (S === void 0 || this.getNormalizedState() === S) return
        this.isLoading = !0
        const H = await this.$wire.updateTableColumnState(
          f,
          v,
          this.state
        );
        ((this.error = H?.error ?? void 0),
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
export { Ee as default }
