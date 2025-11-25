const tt = Math.min;
const H = Math.max;
const et = Math.round;
const E = (s) => ({ x: s, y: s });
const Gt = { left: "right", right: "left", bottom: "top", top: "bottom" };
const Qt = { start: "end", end: "start" };
function mt(s, t, e) {
    return H(s, tt(t, e));
}
function it(s, t) {
    return typeof s === "function" ? s(t) : s;
}
function z(s) {
    return s.split("-")[0];
}
function st(s) {
    return s.split("-")[1];
}
function gt(s) {
    return s === "x" ? "y" : "x";
}
function bt(s) {
    return s === "y" ? "height" : "width";
}
const Zt = new Set(["top", "bottom"]);
function M(s) {
    return Zt.has(z(s)) ? "y" : "x";
}
function yt(s) {
    return gt(M(s));
}
function Ot(s, t, e) {
    e === void 0 && (e = !1);
    const i = st(s);
    const n = yt(s);
    const o = bt(n);
    let r =
        n === "x"
            ? i === (e ? "end" : "start")
                ? "right"
                : "left"
            : i === "start"
              ? "bottom"
              : "top";
    return (t.reference[o] > t.floating[o] && (r = Z(r)), [r, Z(r)]);
}
function At(s) {
    const t = Z(s);
    return [lt(s), t, lt(t)];
}
function lt(s) {
    return s.replace(/start|end/g, (t) => Qt[t]);
}
const vt = ["left", "right"];
const Lt = ["right", "left"];
const te = ["top", "bottom"];
const ee = ["bottom", "top"];
function ie(s, t, e) {
    switch (s) {
        case "top":
        case "bottom":
            return e ? (t ? Lt : vt) : t ? vt : Lt;
        case "left":
        case "right":
            return t ? te : ee;
        default:
            return [];
    }
}
function St(s, t, e, i) {
    const n = st(s);
    let o = ie(z(s), e === "start", i);
    return (
        n && ((o = o.map((r) => r + "-" + n)), t && (o = o.concat(o.map(lt)))),
        o
    );
}
function Z(s) {
    return s.replace(/left|right|bottom|top/g, (t) => Gt[t]);
}
function se(s) {
    return { top: 0, right: 0, bottom: 0, left: 0, ...s };
}
function Ct(s) {
    return typeof s !== "number"
        ? se(s)
        : { top: s, right: s, bottom: s, left: s };
}
function U(s) {
    const { x: t, y: e, width: i, height: n } = s;
    return {
        width: i,
        height: n,
        top: e,
        left: t,
        right: t + i,
        bottom: e + n,
        x: t,
        y: e,
    };
}
function Dt(s, t, e) {
    const { reference: i, floating: n } = s;
    const o = M(t);
    const r = yt(t);
    const l = bt(r);
    const a = z(t);
    const c = o === "y";
    const h = i.x + i.width / 2 - n.width / 2;
    const d = i.y + i.height / 2 - n.height / 2;
    const p = i[l] / 2 - n[l] / 2;
    let f;
    switch (a) {
        case "top":
            f = { x: h, y: i.y - n.height };
            break;
        case "bottom":
            f = { x: h, y: i.y + i.height };
            break;
        case "right":
            f = { x: i.x + i.width, y: d };
            break;
        case "left":
            f = { x: i.x - n.width, y: d };
            break;
        default:
            f = { x: i.x, y: i.y };
    }
    switch (st(t)) {
        case "start":
            f[r] -= p * (e && c ? -1 : 1);
            break;
        case "end":
            f[r] += p * (e && c ? -1 : 1);
            break;
    }
    return f;
}
const Et = async (s, t, e) => {
    const {
        placement: i = "bottom",
        strategy: n = "absolute",
        middleware: o = [],
        platform: r,
    } = e;
    const l = o.filter(Boolean);
    const a = await (r.isRTL == null ? void 0 : r.isRTL(t));
    let c = await r.getElementRects({ reference: s, floating: t, strategy: n });
    let { x: h, y: d } = Dt(c, i, a);
    let p = i;
    let f = {};
    let u = 0;
    for (let m = 0; m < l.length; m++) {
        const { name: g, fn: w } = l[m];
        const {
            x: b,
            y: v,
            data: L,
            reset: x,
        } = await w({
            x: h,
            y: d,
            initialPlacement: i,
            placement: p,
            strategy: n,
            middlewareData: f,
            rects: c,
            platform: r,
            elements: { reference: s, floating: t },
        });
        ((h = b ?? h),
            (d = v ?? d),
            (f = { ...f, [g]: { ...f[g], ...L } }),
            x &&
                u <= 50 &&
                (u++,
                typeof x === "object" &&
                    (x.placement && (p = x.placement),
                    x.rects &&
                        (c =
                            x.rects === !0
                                ? await r.getElementRects({
                                      reference: s,
                                      floating: t,
                                      strategy: n,
                                  })
                                : x.rects),
                    ({ x: h, y: d } = Dt(c, p, a))),
                (m = -1)));
    }
    return { x: h, y: d, placement: p, strategy: n, middlewareData: f };
};
async function wt(s, t) {
    let e;
    t === void 0 && (t = {});
    const { x: i, y: n, platform: o, rects: r, elements: l, strategy: a } = s;
    const {
        boundary: c = "clippingAncestors",
        rootBoundary: h = "viewport",
        elementContext: d = "floating",
        altBoundary: p = !1,
        padding: f = 0,
    } = it(t, s);
    const u = Ct(f);
    const g = l[p ? (d === "floating" ? "reference" : "floating") : d];
    const w = U(
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
            strategy: a,
        }),
    );
    const b =
        d === "floating"
            ? { x: i, y: n, width: r.floating.width, height: r.floating.height }
            : r.reference;
    const v = await (o.getOffsetParent == null
        ? void 0
        : o.getOffsetParent(l.floating));
    const L = (await (o.isElement == null ? void 0 : o.isElement(v)))
        ? (await (o.getScale == null ? void 0 : o.getScale(v))) || {
              x: 1,
              y: 1,
          }
        : { x: 1, y: 1 };
    const x = U(
        o.convertOffsetParentRelativeRectToViewportRelativeRect
            ? await o.convertOffsetParentRelativeRectToViewportRelativeRect({
                  elements: l,
                  rect: b,
                  offsetParent: v,
                  strategy: a,
              })
            : b,
    );
    return {
        top: (w.top - x.top + u.top) / L.y,
        bottom: (x.bottom - w.bottom + u.bottom) / L.y,
        left: (w.left - x.left + u.left) / L.x,
        right: (x.right - w.right + u.right) / L.x,
    };
}
const Rt = function (s) {
    return (
        s === void 0 && (s = {}),
        {
            name: "flip",
            options: s,
            async fn(t) {
                let e, i;
                const {
                    placement: n,
                    middlewareData: o,
                    rects: r,
                    initialPlacement: l,
                    platform: a,
                    elements: c,
                } = t;
                const {
                    mainAxis: h = !0,
                    crossAxis: d = !0,
                    fallbackPlacements: p,
                    fallbackStrategy: f = "bestFit",
                    fallbackAxisSideDirection: u = "none",
                    flipAlignment: m = !0,
                    ...g
                } = it(s, t);
                if ((e = o.arrow) != null && e.alignmentOffset) return {};
                const w = z(n);
                const b = M(l);
                const v = z(l) === l;
                const L = await (a.isRTL == null
                    ? void 0
                    : a.isRTL(c.floating));
                const x = p || (v || !m ? [Z(l)] : At(l));
                const J = u !== "none";
                !p && J && x.push(...St(l, m, u, L));
                const Q = [l, ...x];
                const W = await wt(t, g);
                const F = [];
                let I = ((i = o.flip) == null ? void 0 : i.overflows) || [];
                if ((h && F.push(W[w]), d)) {
                    const A = Ot(n, r, L);
                    F.push(W[A[0]], W[A[1]]);
                }
                if (
                    ((I = [...I, { placement: n, overflows: F }]),
                    !F.every((A) => A <= 0))
                ) {
                    let j, q;
                    const A =
                        (((j = o.flip) == null ? void 0 : j.index) || 0) + 1;
                    const k = Q[A];
                    if (
                        k &&
                        (!(d === "alignment" ? b !== M(k) : !1) ||
                            I.every((D) =>
                                M(D.placement) === b ? D.overflows[0] > 0 : !0,
                            ))
                    ) {
                        return {
                            data: { index: A, overflows: I },
                            reset: { placement: k },
                        };
                    }
                    let $ =
                        (q = I.filter((P) => P.overflows[0] <= 0).sort(
                            (P, D) => P.overflows[1] - D.overflows[1],
                        )[0]) == null
                            ? void 0
                            : q.placement;
                    if (!$) {
                        switch (f) {
                            case "bestFit": {
                                let X;
                                const P =
                                    (X = I.filter((D) => {
                                        if (J) {
                                            const V = M(D.placement);
                                            return V === b || V === "y";
                                        }
                                        return !0;
                                    })
                                        .map((D) => [
                                            D.placement,
                                            D.overflows
                                                .filter((V) => V > 0)
                                                .reduce((V, Yt) => V + Yt, 0),
                                        ])
                                        .sort((D, V) => D[1] - V[1])[0]) == null
                                        ? void 0
                                        : X[0];
                                P && ($ = P);
                                break;
                            }
                            case "initialPlacement":
                                $ = l;
                                break;
                        }
                    }
                    if (n !== $) return { reset: { placement: $ } };
                }
                return {};
            },
        }
    );
};
const ne = new Set(["left", "top"]);
async function oe(s, t) {
    const { placement: e, platform: i, elements: n } = s;
    const o = await (i.isRTL == null ? void 0 : i.isRTL(n.floating));
    const r = z(e);
    const l = st(e);
    const a = M(e) === "y";
    const c = ne.has(r) ? -1 : 1;
    const h = o && a ? -1 : 1;
    const d = it(t, s);
    let {
        mainAxis: p,
        crossAxis: f,
        alignmentAxis: u,
    } = typeof d === "number"
        ? { mainAxis: d, crossAxis: 0, alignmentAxis: null }
        : {
              mainAxis: d.mainAxis || 0,
              crossAxis: d.crossAxis || 0,
              alignmentAxis: d.alignmentAxis,
          };
    return (
        l && typeof u === "number" && (f = l === "end" ? u * -1 : u),
        a ? { x: f * h, y: p * c } : { x: p * c, y: f * h }
    );
}
const It = function (s) {
    return (
        s === void 0 && (s = 0),
        {
            name: "offset",
            options: s,
            async fn(t) {
                let e, i;
                const { x: n, y: o, placement: r, middlewareData: l } = t;
                const a = await oe(t, s);
                return r === ((e = l.offset) == null ? void 0 : e.placement) &&
                    (i = l.arrow) != null &&
                    i.alignmentOffset
                    ? {}
                    : { x: n + a.x, y: o + a.y, data: { ...a, placement: r } };
            },
        }
    );
};
const kt = function (s) {
    return (
        s === void 0 && (s = {}),
        {
            name: "shift",
            options: s,
            async fn(t) {
                const { x: e, y: i, placement: n } = t;
                const {
                    mainAxis: o = !0,
                    crossAxis: r = !1,
                    limiter: l = {
                        fn: (g) => {
                            const { x: w, y: b } = g;
                            return { x: w, y: b };
                        },
                    },
                    ...a
                } = it(s, t);
                const c = { x: e, y: i };
                const h = await wt(t, a);
                const d = M(z(n));
                const p = gt(d);
                let f = c[p];
                let u = c[d];
                if (o) {
                    const g = p === "y" ? "top" : "left";
                    const w = p === "y" ? "bottom" : "right";
                    const b = f + h[g];
                    const v = f - h[w];
                    f = mt(b, f, v);
                }
                if (r) {
                    const g = d === "y" ? "top" : "left";
                    const w = d === "y" ? "bottom" : "right";
                    const b = u + h[g];
                    const v = u - h[w];
                    u = mt(b, u, v);
                }
                const m = l.fn({ ...t, [p]: f, [d]: u });
                return {
                    ...m,
                    data: {
                        x: m.x - e,
                        y: m.y - i,
                        enabled: { [p]: o, [d]: r },
                    },
                };
            },
        }
    );
};
function ct() {
    return typeof window < "u";
}
function K(s) {
    return Pt(s) ? (s.nodeName || "").toLowerCase() : "#document";
}
function O(s) {
    let t;
    return (
        (s == null || (t = s.ownerDocument) == null ? void 0 : t.defaultView) ||
        window
    );
}
function T(s) {
    let t;
    return (t = (Pt(s) ? s.ownerDocument : s.document) || window.document) ==
        null
        ? void 0
        : t.documentElement;
}
function Pt(s) {
    return ct() ? s instanceof Node || s instanceof O(s).Node : !1;
}
function S(s) {
    return ct() ? s instanceof Element || s instanceof O(s).Element : !1;
}
function R(s) {
    return ct()
        ? s instanceof HTMLElement || s instanceof O(s).HTMLElement
        : !1;
}
function Tt(s) {
    return !ct() || typeof ShadowRoot > "u"
        ? !1
        : s instanceof ShadowRoot || s instanceof O(s).ShadowRoot;
}
const re = new Set(["inline", "contents"]);
function Y(s) {
    const { overflow: t, overflowX: e, overflowY: i, display: n } = C(s);
    return /auto|scroll|overlay|hidden|clip/.test(t + i + e) && !re.has(n);
}
const le = new Set(["table", "td", "th"]);
function Mt(s) {
    return le.has(K(s));
}
const ae = [":popover-open", ":modal"];
function nt(s) {
    return ae.some((t) => {
        try {
            return s.matches(t);
        } catch {
            return !1;
        }
    });
}
const ce = ["transform", "translate", "scale", "rotate", "perspective"];
const he = [
    "transform",
    "translate",
    "scale",
    "rotate",
    "perspective",
    "filter",
];
const de = ["paint", "layout", "strict", "content"];
function ht(s) {
    const t = dt();
    const e = S(s) ? C(s) : s;
    return (
        ce.some((i) => (e[i] ? e[i] !== "none" : !1)) ||
        (e.containerType ? e.containerType !== "normal" : !1) ||
        (!t && (e.backdropFilter ? e.backdropFilter !== "none" : !1)) ||
        (!t && (e.filter ? e.filter !== "none" : !1)) ||
        he.some((i) => (e.willChange || "").includes(i)) ||
        de.some((i) => (e.contain || "").includes(i))
    );
}
function Bt(s) {
    let t = B(s);
    for (; R(t) && !_(t); ) {
        if (ht(t)) return t;
        if (nt(t)) return null;
        t = B(t);
    }
    return null;
}
function dt() {
    return typeof CSS > "u" || !CSS.supports
        ? !1
        : CSS.supports("-webkit-backdrop-filter", "none");
}
const fe = new Set(["html", "body", "#document"]);
function _(s) {
    return fe.has(K(s));
}
function C(s) {
    return O(s).getComputedStyle(s);
}
function ot(s) {
    return S(s)
        ? { scrollLeft: s.scrollLeft, scrollTop: s.scrollTop }
        : { scrollLeft: s.scrollX, scrollTop: s.scrollY };
}
function B(s) {
    if (K(s) === "html") return s;
    const t = s.assignedSlot || s.parentNode || (Tt(s) && s.host) || T(s);
    return Tt(t) ? t.host : t;
}
function Nt(s) {
    const t = B(s);
    return _(t)
        ? s.ownerDocument
            ? s.ownerDocument.body
            : s.body
        : R(t) && Y(t)
          ? t
          : Nt(t);
}
function at(s, t, e) {
    let i;
    (t === void 0 && (t = []), e === void 0 && (e = !0));
    const n = Nt(s);
    const o = n === ((i = s.ownerDocument) == null ? void 0 : i.body);
    const r = O(n);
    if (o) {
        const l = ft(r);
        return t.concat(
            r,
            r.visualViewport || [],
            Y(n) ? n : [],
            l && e ? at(l) : [],
        );
    }
    return t.concat(n, at(n, [], e));
}
function ft(s) {
    return s.parent && Object.getPrototypeOf(s.parent) ? s.frameElement : null;
}
function zt(s) {
    const t = C(s);
    let e = parseFloat(t.width) || 0;
    let i = parseFloat(t.height) || 0;
    const n = R(s);
    const o = n ? s.offsetWidth : e;
    const r = n ? s.offsetHeight : i;
    const l = et(e) !== o || et(i) !== r;
    return (l && ((e = o), (i = r)), { width: e, height: i, $: l });
}
function Wt(s) {
    return S(s) ? s : s.contextElement;
}
function G(s) {
    const t = Wt(s);
    if (!R(t)) return E(1);
    const e = t.getBoundingClientRect();
    const { width: i, height: n, $: o } = zt(t);
    let r = (o ? et(e.width) : e.width) / i;
    let l = (o ? et(e.height) : e.height) / n;
    return (
        (!r || !Number.isFinite(r)) && (r = 1),
        (!l || !Number.isFinite(l)) && (l = 1),
        { x: r, y: l }
    );
}
const pe = E(0);
function $t(s) {
    const t = O(s);
    return !dt() || !t.visualViewport
        ? pe
        : { x: t.visualViewport.offsetLeft, y: t.visualViewport.offsetTop };
}
function ue(s, t, e) {
    return (t === void 0 && (t = !1), !e || (t && e !== O(s)) ? !1 : t);
}
function rt(s, t, e, i) {
    (t === void 0 && (t = !1), e === void 0 && (e = !1));
    const n = s.getBoundingClientRect();
    const o = Wt(s);
    let r = E(1);
    t && (i ? S(i) && (r = G(i)) : (r = G(s)));
    const l = ue(o, e, i) ? $t(o) : E(0);
    let a = (n.left + l.x) / r.x;
    let c = (n.top + l.y) / r.y;
    let h = n.width / r.x;
    let d = n.height / r.y;
    if (o) {
        const p = O(o);
        const f = i && S(i) ? O(i) : i;
        let u = p;
        let m = ft(u);
        for (; m && i && f !== u; ) {
            const g = G(m);
            const w = m.getBoundingClientRect();
            const b = C(m);
            const v = w.left + (m.clientLeft + parseFloat(b.paddingLeft)) * g.x;
            const L = w.top + (m.clientTop + parseFloat(b.paddingTop)) * g.y;
            ((a *= g.x),
                (c *= g.y),
                (h *= g.x),
                (d *= g.y),
                (a += v),
                (c += L),
                (u = O(m)),
                (m = ft(u)));
        }
    }
    return U({ width: h, height: d, x: a, y: c });
}
function pt(s, t) {
    const e = ot(s).scrollLeft;
    return t ? t.left + e : rt(T(s)).left + e;
}
function Ut(s, t) {
    const e = s.getBoundingClientRect();
    const i = e.left + t.scrollLeft - pt(s, e);
    const n = e.top + t.scrollTop;
    return { x: i, y: n };
}
function me(s) {
    const { elements: t, rect: e, offsetParent: i, strategy: n } = s;
    const o = n === "fixed";
    const r = T(i);
    const l = t ? nt(t.floating) : !1;
    if (i === r || (l && o)) return e;
    let a = { scrollLeft: 0, scrollTop: 0 };
    let c = E(1);
    const h = E(0);
    const d = R(i);
    if ((d || (!d && !o)) && ((K(i) !== "body" || Y(r)) && (a = ot(i)), R(i))) {
        const f = rt(i);
        ((c = G(i)), (h.x = f.x + i.clientLeft), (h.y = f.y + i.clientTop));
    }
    const p = r && !d && !o ? Ut(r, a) : E(0);
    return {
        width: e.width * c.x,
        height: e.height * c.y,
        x: e.x * c.x - a.scrollLeft * c.x + h.x + p.x,
        y: e.y * c.y - a.scrollTop * c.y + h.y + p.y,
    };
}
function ge(s) {
    return Array.from(s.getClientRects());
}
function be(s) {
    const t = T(s);
    const e = ot(s);
    const i = s.ownerDocument.body;
    const n = H(t.scrollWidth, t.clientWidth, i.scrollWidth, i.clientWidth);
    const o = H(t.scrollHeight, t.clientHeight, i.scrollHeight, i.clientHeight);
    let r = -e.scrollLeft + pt(s);
    const l = -e.scrollTop;
    return (
        C(i).direction === "rtl" && (r += H(t.clientWidth, i.clientWidth) - n),
        { width: n, height: o, x: r, y: l }
    );
}
const Ft = 25;
function ye(s, t) {
    const e = O(s);
    const i = T(s);
    const n = e.visualViewport;
    let o = i.clientWidth;
    let r = i.clientHeight;
    let l = 0;
    let a = 0;
    if (n) {
        ((o = n.width), (r = n.height));
        const h = dt();
        (!h || (h && t === "fixed")) && ((l = n.offsetLeft), (a = n.offsetTop));
    }
    const c = pt(i);
    if (c <= 0) {
        const h = i.ownerDocument;
        const d = h.body;
        const p = getComputedStyle(d);
        const f =
            (h.compatMode === "CSS1Compat" &&
                parseFloat(p.marginLeft) + parseFloat(p.marginRight)) ||
            0;
        const u = Math.abs(i.clientWidth - d.clientWidth - f);
        u <= Ft && (o -= u);
    } else c <= Ft && (o += c);
    return { width: o, height: r, x: l, y: a };
}
const we = new Set(["absolute", "fixed"]);
function xe(s, t) {
    const e = rt(s, !0, t === "fixed");
    const i = e.top + s.clientTop;
    const n = e.left + s.clientLeft;
    const o = R(s) ? G(s) : E(1);
    const r = s.clientWidth * o.x;
    const l = s.clientHeight * o.y;
    const a = n * o.x;
    const c = i * o.y;
    return { width: r, height: l, x: a, y: c };
}
function Vt(s, t, e) {
    let i;
    if (t === "viewport") i = ye(s, e);
    else if (t === "document") i = be(T(s));
    else if (S(t)) i = xe(t, e);
    else {
        const n = $t(s);
        i = { x: t.x - n.x, y: t.y - n.y, width: t.width, height: t.height };
    }
    return U(i);
}
function Kt(s, t) {
    const e = B(s);
    return e === t || !S(e) || _(e)
        ? !1
        : C(e).position === "fixed" || Kt(e, t);
}
function ve(s, t) {
    const e = t.get(s);
    if (e) return e;
    let i = at(s, [], !1).filter((l) => S(l) && K(l) !== "body");
    let n = null;
    const o = C(s).position === "fixed";
    let r = o ? B(s) : s;
    for (; S(r) && !_(r); ) {
        const l = C(r);
        const a = ht(r);
        (!a && l.position === "fixed" && (n = null),
            (
                o
                    ? !a && !n
                    : (!a &&
                          l.position === "static" &&
                          !!n &&
                          we.has(n.position)) ||
                      (Y(r) && !a && Kt(s, r))
            )
                ? (i = i.filter((h) => h !== r))
                : (n = l),
            (r = B(r)));
    }
    return (t.set(s, i), i);
}
function Le(s) {
    const { element: t, boundary: e, rootBoundary: i, strategy: n } = s;
    const r = [
        ...(e === "clippingAncestors"
            ? nt(t)
                ? []
                : ve(t, this._c)
            : [].concat(e)),
        i,
    ];
    const l = r[0];
    const a = r.reduce(
        (c, h) => {
            const d = Vt(t, h, n);
            return (
                (c.top = H(d.top, c.top)),
                (c.right = tt(d.right, c.right)),
                (c.bottom = tt(d.bottom, c.bottom)),
                (c.left = H(d.left, c.left)),
                c
            );
        },
        Vt(t, l, n),
    );
    return {
        width: a.right - a.left,
        height: a.bottom - a.top,
        x: a.left,
        y: a.top,
    };
}
function Oe(s) {
    const { width: t, height: e } = zt(s);
    return { width: t, height: e };
}
function Ae(s, t, e) {
    const i = R(t);
    const n = T(t);
    const o = e === "fixed";
    const r = rt(s, !0, o, t);
    let l = { scrollLeft: 0, scrollTop: 0 };
    const a = E(0);
    function c() {
        a.x = pt(n);
    }
    if (i || (!i && !o)) {
        if (((K(t) !== "body" || Y(n)) && (l = ot(t)), i)) {
            const f = rt(t, !0, o, t);
            ((a.x = f.x + t.clientLeft), (a.y = f.y + t.clientTop));
        } else n && c();
    }
    o && !i && n && c();
    const h = n && !i && !o ? Ut(n, l) : E(0);
    const d = r.left + l.scrollLeft - a.x - h.x;
    const p = r.top + l.scrollTop - a.y - h.y;
    return { x: d, y: p, width: r.width, height: r.height };
}
function xt(s) {
    return C(s).position === "static";
}
function Ht(s, t) {
    if (!R(s) || C(s).position === "fixed") return null;
    if (t) return t(s);
    let e = s.offsetParent;
    return (T(s) === e && (e = e.ownerDocument.body), e);
}
function _t(s, t) {
    const e = O(s);
    if (nt(s)) return e;
    if (!R(s)) {
        let n = B(s);
        for (; n && !_(n); ) {
            if (S(n) && !xt(n)) return n;
            n = B(n);
        }
        return e;
    }
    let i = Ht(s, t);
    for (; i && Mt(i) && xt(i); ) i = Ht(i, t);
    return i && _(i) && xt(i) && !ht(i) ? e : i || Bt(s) || e;
}
const Se = async function (s) {
    const t = this.getOffsetParent || _t;
    const e = this.getDimensions;
    const i = await e(s.floating);
    return {
        reference: Ae(s.reference, await t(s.floating), s.strategy),
        floating: { x: 0, y: 0, width: i.width, height: i.height },
    };
};
function Ce(s) {
    return C(s).direction === "rtl";
}
const De = {
    convertOffsetParentRelativeRectToViewportRelativeRect: me,
    getDocumentElement: T,
    getClippingRect: Le,
    getOffsetParent: _t,
    getElementRects: Se,
    getClientRects: ge,
    getDimensions: Oe,
    getScale: G,
    isElement: S,
    isRTL: Ce,
};
const Jt = It;
const jt = kt;
const qt = Rt;
const Xt = (s, t, e) => {
    const i = new Map();
    const n = { platform: De, ...e };
    const o = { ...n.platform, _c: i };
    return Et(s, t, { ...n, platform: o });
};
function N(s) {
    return s == null || s === "" || (typeof s === "string" && s.trim() === "");
}
function y(s) {
    return !N(s);
}
const ut = class {
    constructor({
        element: t,
        options: e,
        placeholder: i,
        state: n,
        canOptionLabelsWrap: o = !0,
        canSelectPlaceholder: r = !0,
        initialOptionLabel: l = null,
        initialOptionLabels: a = null,
        initialState: c = null,
        isHtmlAllowed: h = !1,
        isAutofocused: d = !1,
        isDisabled: p = !1,
        isMultiple: f = !1,
        isSearchable: u = !1,
        getOptionLabelUsing: m = null,
        getOptionLabelsUsing: g = null,
        getOptionsUsing: w = null,
        getSearchResultsUsing: b = null,
        hasDynamicOptions: v = !1,
        hasDynamicSearchResults: L = !0,
        searchPrompt: x = "Search...",
        searchDebounce: J = 1e3,
        loadingMessage: Q = "Loading...",
        searchingMessage: W = "Searching...",
        noSearchResultsMessage: F = "No results found",
        maxItems: I = null,
        maxItemsMessage: j = "Maximum number of items selected",
        optionsLimit: q = null,
        position: X = null,
        searchableOptionFields: A = ["label"],
        livewireId: k = null,
        statePath: $ = null,
        onStateChange: P = () => {},
    }) {
        ((this.element = t),
            (this.options = e),
            (this.originalOptions = JSON.parse(JSON.stringify(e))),
            (this.placeholder = i),
            (this.state = n),
            (this.canOptionLabelsWrap = o),
            (this.canSelectPlaceholder = r),
            (this.initialOptionLabel = l),
            (this.initialOptionLabels = a),
            (this.initialState = c),
            (this.isHtmlAllowed = h),
            (this.isAutofocused = d),
            (this.isDisabled = p),
            (this.isMultiple = f),
            (this.isSearchable = u),
            (this.getOptionLabelUsing = m),
            (this.getOptionLabelsUsing = g),
            (this.getOptionsUsing = w),
            (this.getSearchResultsUsing = b),
            (this.hasDynamicOptions = v),
            (this.hasDynamicSearchResults = L),
            (this.searchPrompt = x),
            (this.searchDebounce = J),
            (this.loadingMessage = Q),
            (this.searchingMessage = W),
            (this.noSearchResultsMessage = F),
            (this.maxItems = I),
            (this.maxItemsMessage = j),
            (this.optionsLimit = q),
            (this.position = X),
            (this.searchableOptionFields = Array.isArray(A) ? A : ["label"]),
            (this.livewireId = k),
            (this.statePath = $),
            (this.onStateChange = P),
            (this.labelRepository = {}),
            (this.isOpen = !1),
            (this.selectedIndex = -1),
            (this.searchQuery = ""),
            (this.searchTimeout = null),
            (this.isSearching = !1),
            (this.selectedDisplayVersion = 0),
            this.render(),
            this.setUpEventListeners(),
            this.isAutofocused && this.selectButton.focus());
    }

    populateLabelRepositoryFromOptions(t) {
        if (!(!t || !Array.isArray(t))) {
            for (const e of t) {
                e.options && Array.isArray(e.options)
                    ? this.populateLabelRepositoryFromOptions(e.options)
                    : e.value !== void 0 &&
                      e.label !== void 0 &&
                      (this.labelRepository[e.value] = e.label);
            }
        }
    }

    render() {
        (this.populateLabelRepositoryFromOptions(this.options),
            (this.container = document.createElement("div")),
            (this.container.className = "fi-select-input-ctn"),
            this.canOptionLabelsWrap ||
                this.container.classList.add(
                    "fi-select-input-ctn-option-labels-not-wrapped",
                ),
            this.container.setAttribute("aria-haspopup", "listbox"),
            (this.selectButton = document.createElement("button")),
            (this.selectButton.className = "fi-select-input-btn"),
            (this.selectButton.type = "button"),
            this.selectButton.setAttribute("aria-expanded", "false"),
            (this.selectedDisplay = document.createElement("div")),
            (this.selectedDisplay.className = "fi-select-input-value-ctn"),
            this.updateSelectedDisplay(),
            this.selectButton.appendChild(this.selectedDisplay),
            (this.dropdown = document.createElement("div")),
            (this.dropdown.className = "fi-dropdown-panel fi-scrollable"),
            this.dropdown.setAttribute("role", "listbox"),
            this.dropdown.setAttribute("tabindex", "-1"),
            (this.dropdown.style.display = "none"),
            (this.dropdownId = `fi-select-input-dropdown-${Math.random().toString(36).substring(2, 11)}`),
            (this.dropdown.id = this.dropdownId),
            this.isMultiple &&
                this.dropdown.setAttribute("aria-multiselectable", "true"),
            this.isSearchable &&
                ((this.searchContainer = document.createElement("div")),
                (this.searchContainer.className = "fi-select-input-search-ctn"),
                (this.searchInput = document.createElement("input")),
                (this.searchInput.className = "fi-input"),
                (this.searchInput.type = "text"),
                (this.searchInput.placeholder = this.searchPrompt),
                this.searchInput.setAttribute("aria-label", "Search"),
                this.searchContainer.appendChild(this.searchInput),
                this.dropdown.appendChild(this.searchContainer),
                this.searchInput.addEventListener("input", (t) => {
                    this.isDisabled || this.handleSearch(t);
                }),
                this.searchInput.addEventListener("keydown", (t) => {
                    if (!this.isDisabled) {
                        if (t.key === "Tab") {
                            t.preventDefault();
                            const e = this.getVisibleOptions();
                            if (e.length === 0) return;
                            (t.shiftKey
                                ? (this.selectedIndex = e.length - 1)
                                : (this.selectedIndex = 0),
                                e.forEach((i) => {
                                    i.classList.remove("fi-selected");
                                }),
                                e[this.selectedIndex].classList.add(
                                    "fi-selected",
                                ),
                                e[this.selectedIndex].focus());
                        } else if (t.key === "ArrowDown") {
                            if (
                                (t.preventDefault(),
                                t.stopPropagation(),
                                this.getVisibleOptions().length === 0)
                            ) {
                                return;
                            }
                            ((this.selectedIndex = -1),
                                this.searchInput.blur(),
                                this.focusNextOption());
                        } else if (t.key === "ArrowUp") {
                            (t.preventDefault(), t.stopPropagation());
                            const e = this.getVisibleOptions();
                            if (e.length === 0) return;
                            ((this.selectedIndex = e.length - 1),
                                this.searchInput.blur(),
                                e[this.selectedIndex].classList.add(
                                    "fi-selected",
                                ),
                                e[this.selectedIndex].focus(),
                                e[this.selectedIndex].id &&
                                    this.dropdown.setAttribute(
                                        "aria-activedescendant",
                                        e[this.selectedIndex].id,
                                    ),
                                this.scrollOptionIntoView(
                                    e[this.selectedIndex],
                                ));
                        } else if (t.key === "Enter") {
                            if (
                                (t.preventDefault(),
                                t.stopPropagation(),
                                this.isSearching)
                            ) {
                                return;
                            }
                            const e = this.getVisibleOptions();
                            if (e.length === 0) return;
                            const i = e.find((o) => {
                                const r =
                                    o.getAttribute("aria-disabled") === "true";
                                const l = o.classList.contains("fi-disabled");
                                const a = o.offsetParent === null;
                                return !(r || l || a);
                            });
                            if (!i) return;
                            const n = i.getAttribute("data-value");
                            if (n === null) return;
                            this.selectOption(n);
                        }
                    }
                })),
            (this.optionsList = document.createElement("ul")),
            this.renderOptions(),
            this.container.appendChild(this.selectButton),
            this.container.appendChild(this.dropdown),
            this.element.appendChild(this.container),
            this.applyDisabledState());
    }

    renderOptions() {
        this.optionsList.innerHTML = "";
        let t = 0;
        const e = this.options;
        let i = 0;
        let n = !1;
        (this.options.forEach((l) => {
            l.options && Array.isArray(l.options)
                ? ((i += l.options.length), (n = !0))
                : i++;
        }),
            n
                ? (this.optionsList.className = "fi-select-input-options-ctn")
                : i > 0 && (this.optionsList.className = "fi-dropdown-list"));
        let o = n ? null : this.optionsList;
        let r = 0;
        for (const l of e) {
            if (this.optionsLimit && r >= this.optionsLimit) break;
            if (l.options && Array.isArray(l.options)) {
                let a = l.options;
                if (
                    (this.isMultiple &&
                        Array.isArray(this.state) &&
                        this.state.length > 0 &&
                        (a = l.options.filter(
                            (c) => !this.state.includes(c.value),
                        )),
                    a.length > 0)
                ) {
                    if (this.optionsLimit) {
                        const c = this.optionsLimit - r;
                        c < a.length && (a = a.slice(0, c));
                    }
                    (this.renderOptionGroup(l.label, a),
                        (r += a.length),
                        (t += a.length));
                }
            } else {
                if (
                    this.isMultiple &&
                    Array.isArray(this.state) &&
                    this.state.includes(l.value)
                ) {
                    continue;
                }
                !o &&
                    n &&
                    ((o = document.createElement("ul")),
                    (o.className = "fi-dropdown-list"),
                    this.optionsList.appendChild(o));
                const a = this.createOptionElement(l.value, l);
                (o.appendChild(a), r++, t++);
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
                  this.dropdown.appendChild(this.optionsList));
    }

    renderOptionGroup(t, e) {
        if (e.length === 0) return;
        const i = document.createElement("li");
        i.className = "fi-select-input-option-group";
        const n = document.createElement("div");
        ((n.className = "fi-dropdown-header"), (n.textContent = t));
        const o = document.createElement("ul");
        ((o.className = "fi-dropdown-list"),
            e.forEach((r) => {
                const l = this.createOptionElement(r.value, r);
                o.appendChild(l);
            }),
            i.appendChild(n),
            i.appendChild(o),
            this.optionsList.appendChild(i));
    }

    createOptionElement(t, e) {
        let i = t;
        let n = e;
        let o = !1;
        typeof e === "object" &&
            e !== null &&
            "label" in e &&
            "value" in e &&
            ((i = e.value), (n = e.label), (o = e.isDisabled || !1));
        const r = document.createElement("li");
        ((r.className = "fi-dropdown-list-item fi-select-input-option"),
            o && r.classList.add("fi-disabled"));
        const l = `fi-select-input-option-${Math.random().toString(36).substring(2, 11)}`;
        if (
            ((r.id = l),
            r.setAttribute("role", "option"),
            r.setAttribute("data-value", i),
            r.setAttribute("tabindex", "0"),
            o && r.setAttribute("aria-disabled", "true"),
            this.isHtmlAllowed && typeof n === "string")
        ) {
            const h = document.createElement("div");
            h.innerHTML = n;
            const d = h.textContent || h.innerText || n;
            r.setAttribute("aria-label", d);
        }
        const a = this.isMultiple
            ? Array.isArray(this.state) && this.state.includes(i)
            : this.state === i;
        (r.setAttribute("aria-selected", a ? "true" : "false"),
            a && r.classList.add("fi-selected"));
        const c = document.createElement("span");
        return (
            this.isHtmlAllowed ? (c.innerHTML = n) : (c.textContent = n),
            r.appendChild(c),
            o ||
                r.addEventListener("click", (h) => {
                    (h.preventDefault(),
                        h.stopPropagation(),
                        this.selectOption(i),
                        this.isMultiple &&
                            (this.isSearchable && this.searchInput
                                ? setTimeout(() => {
                                      this.searchInput.focus();
                                  }, 0)
                                : setTimeout(() => {
                                      r.focus();
                                  }, 0)));
                }),
            r
        );
    }

    async updateSelectedDisplay() {
        this.selectedDisplayVersion = this.selectedDisplayVersion + 1;
        const t = this.selectedDisplayVersion;
        const e = document.createDocumentFragment();
        if (this.isMultiple) {
            if (!Array.isArray(this.state) || this.state.length === 0) {
                const n = document.createElement("span");
                ((n.textContent = this.placeholder),
                    n.classList.add("fi-select-input-placeholder"),
                    e.appendChild(n));
            } else {
                const n = await this.getLabelsForMultipleSelection();
                if (t !== this.selectedDisplayVersion) return;
                this.addBadgesForSelectedOptions(n, e);
            }
            t === this.selectedDisplayVersion &&
                (this.selectedDisplay.replaceChildren(e),
                this.isOpen && this.positionDropdown());
            return;
        }
        if (this.state === null || this.state === "") {
            const n = document.createElement("span");
            ((n.textContent = this.placeholder),
                n.classList.add("fi-select-input-placeholder"),
                e.appendChild(n),
                t === this.selectedDisplayVersion &&
                    this.selectedDisplay.replaceChildren(e));
            return;
        }
        const i = await this.getLabelForSingleSelection();
        t === this.selectedDisplayVersion &&
            (this.addSingleSelectionDisplay(i, e),
            t === this.selectedDisplayVersion &&
                this.selectedDisplay.replaceChildren(e));
    }

    async getLabelsForMultipleSelection() {
        const t = this.getSelectedOptionLabels();
        const e = [];
        if (Array.isArray(this.state)) {
            for (const n of this.state) {
                if (!y(this.labelRepository[n])) {
                    if (y(t[n])) {
                        this.labelRepository[n] = t[n];
                        continue;
                    }
                    e.push(n.toString());
                }
            }
        }
        if (
            e.length > 0 &&
            y(this.initialOptionLabels) &&
            JSON.stringify(this.state) === JSON.stringify(this.initialState)
        ) {
            if (Array.isArray(this.initialOptionLabels)) {
                for (const n of this.initialOptionLabels) {
                    y(n) &&
                        n.value !== void 0 &&
                        n.label !== void 0 &&
                        e.includes(n.value) &&
                        (this.labelRepository[n.value] = n.label);
                }
            }
        } else if (e.length > 0 && this.getOptionLabelsUsing) {
            try {
                const n = await this.getOptionLabelsUsing();
                for (const o of n) {
                    y(o) &&
                        o.value !== void 0 &&
                        o.label !== void 0 &&
                        (this.labelRepository[o.value] = o.label);
                }
            } catch (n) {
                console.error("Error fetching option labels:", n);
            }
        }
        const i = [];
        if (Array.isArray(this.state)) {
            for (const n of this.state) {
                y(this.labelRepository[n])
                    ? i.push(this.labelRepository[n])
                    : y(t[n])
                      ? i.push(t[n])
                      : i.push(n);
            }
        }
        return i;
    }

    createBadgeElement(t, e) {
        const i = document.createElement("span");
        ((i.className =
            "fi-badge fi-size-md fi-color fi-color-primary fi-text-color-600 dark:fi-text-color-200"),
            y(t) && i.setAttribute("data-value", t));
        const n = document.createElement("span");
        n.className = "fi-badge-label-ctn";
        const o = document.createElement("span");
        ((o.className = "fi-badge-label"),
            this.canOptionLabelsWrap && o.classList.add("fi-wrapped"),
            this.isHtmlAllowed ? (o.innerHTML = e) : (o.textContent = e),
            n.appendChild(o),
            i.appendChild(n));
        const r = this.createRemoveButton(t, e);
        return (i.appendChild(r), i);
    }

    createRemoveButton(t, e) {
        const i = document.createElement("button");
        return (
            (i.type = "button"),
            (i.className = "fi-badge-delete-btn"),
            (i.innerHTML =
                '<svg class="fi-icon fi-size-xs" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" data-slot="icon"><path d="M5.28 4.22a.75.75 0 0 0-1.06 1.06L6.94 8l-2.72 2.72a.75.75 0 1 0 1.06 1.06L8 9.06l2.72 2.72a.75.75 0 1 0 1.06-1.06L9.06 8l2.72-2.72a.75.75 0 0 0-1.06-1.06L8 6.94 5.28 4.22Z"></path></svg>'),
            i.setAttribute(
                "aria-label",
                "Remove " +
                    (this.isHtmlAllowed ? e.replace(/<[^>]*>/g, "") : e),
            ),
            i.addEventListener("click", (n) => {
                (n.stopPropagation(), y(t) && this.selectOption(t));
            }),
            i.addEventListener("keydown", (n) => {
                (n.key === " " || n.key === "Enter") &&
                    (n.preventDefault(),
                    n.stopPropagation(),
                    y(t) && this.selectOption(t));
            }),
            i
        );
    }

    addBadgesForSelectedOptions(t, e = this.selectedDisplay) {
        const i = document.createElement("div");
        ((i.className = "fi-select-input-value-badges-ctn"),
            t.forEach((n, o) => {
                const r = Array.isArray(this.state) ? this.state[o] : null;
                const l = this.createBadgeElement(r, n);
                i.appendChild(l);
            }),
            e.appendChild(i));
    }

    async getLabelForSingleSelection() {
        let t = this.labelRepository[this.state];
        if (
            (N(t) && (t = this.getSelectedOptionLabel(this.state)),
            N(t) &&
                y(this.initialOptionLabel) &&
                this.state === this.initialState)
        ) {
            ((t = this.initialOptionLabel),
                y(this.state) && (this.labelRepository[this.state] = t));
        } else if (N(t) && this.getOptionLabelUsing) {
            try {
                ((t = await this.getOptionLabelUsing()),
                    y(t) &&
                        y(this.state) &&
                        (this.labelRepository[this.state] = t));
            } catch (e) {
                (console.error("Error fetching option label:", e),
                    (t = this.state));
            }
        } else N(t) && (t = this.state);
        return t;
    }

    addSingleSelectionDisplay(t, e = this.selectedDisplay) {
        const i = document.createElement("span");
        if (
            ((i.className = "fi-select-input-value-label"),
            this.isHtmlAllowed ? (i.innerHTML = t) : (i.textContent = t),
            e.appendChild(i),
            !this.canSelectPlaceholder)
        ) {
            return;
        }
        const n = document.createElement("button");
        ((n.type = "button"),
            (n.className = "fi-select-input-value-remove-btn"),
            (n.innerHTML =
                '<svg class="fi-icon fi-size-sm" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>'),
            n.setAttribute("aria-label", "Clear selection"),
            n.addEventListener("click", (o) => {
                (o.stopPropagation(), this.selectOption(""));
            }),
            n.addEventListener("keydown", (o) => {
                (o.key === " " || o.key === "Enter") &&
                    (o.preventDefault(),
                    o.stopPropagation(),
                    this.selectOption(""));
            }),
            e.appendChild(n));
    }

    getSelectedOptionLabel(t) {
        if (y(this.labelRepository[t])) return this.labelRepository[t];
        let e = "";
        for (const i of this.options) {
            if (i.options && Array.isArray(i.options)) {
                for (const n of i.options) {
                    if (n.value === t) {
                        ((e = n.label), (this.labelRepository[t] = e));
                        break;
                    }
                }
            } else if (i.value === t) {
                ((e = i.label), (this.labelRepository[t] = e));
                break;
            }
        }
        return e;
    }

    setUpEventListeners() {
        ((this.buttonClickListener = () => {
            this.toggleDropdown();
        }),
            (this.documentClickListener = (t) => {
                !this.container.contains(t.target) &&
                    this.isOpen &&
                    this.closeDropdown();
            }),
            (this.buttonKeydownListener = (t) => {
                this.isDisabled || this.handleSelectButtonKeydown(t);
            }),
            (this.dropdownKeydownListener = (t) => {
                this.isDisabled ||
                    (this.isSearchable &&
                        document.activeElement === this.searchInput &&
                        !["Tab", "Escape"].includes(t.key)) ||
                    this.handleDropdownKeydown(t);
            }),
            this.selectButton.addEventListener(
                "click",
                this.buttonClickListener,
            ),
            document.addEventListener("click", this.documentClickListener),
            this.selectButton.addEventListener(
                "keydown",
                this.buttonKeydownListener,
            ),
            this.dropdown.addEventListener(
                "keydown",
                this.dropdownKeydownListener,
            ),
            !this.isMultiple &&
                this.livewireId &&
                this.statePath &&
                this.getOptionLabelUsing &&
                ((this.refreshOptionLabelListener = async (t) => {
                    if (
                        t.detail.livewireId === this.livewireId &&
                        t.detail.statePath === this.statePath &&
                        y(this.state)
                    ) {
                        try {
                            delete this.labelRepository[this.state];
                            const e = await this.getOptionLabelUsing();
                            y(e) && (this.labelRepository[this.state] = e);
                            const i = this.selectedDisplay.querySelector(
                                ".fi-select-input-value-label",
                            );
                            (y(i) &&
                                (this.isHtmlAllowed
                                    ? (i.innerHTML = e)
                                    : (i.textContent = e)),
                                this.updateOptionLabelInList(this.state, e));
                        } catch (e) {
                            console.error("Error refreshing option label:", e);
                        }
                    }
                }),
                window.addEventListener(
                    "filament-forms::select.refreshSelectedOptionLabel",
                    this.refreshOptionLabelListener,
                )));
    }

    updateOptionLabelInList(t, e) {
        this.labelRepository[t] = e;
        const i = this.getVisibleOptions();
        for (const n of i) {
            if (n.getAttribute("data-value") === String(t)) {
                if (((n.innerHTML = ""), this.isHtmlAllowed)) {
                    const o = document.createElement("span");
                    ((o.innerHTML = e), n.appendChild(o));
                } else n.appendChild(document.createTextNode(e));
                break;
            }
        }
        for (const n of this.options) {
            if (n.options && Array.isArray(n.options)) {
                for (const o of n.options) {
                    if (o.value === t) {
                        o.label = e;
                        break;
                    }
                }
            } else if (n.value === t) {
                n.label = e;
                break;
            }
        }
        for (const n of this.originalOptions) {
            if (n.options && Array.isArray(n.options)) {
                for (const o of n.options) {
                    if (o.value === t) {
                        o.label = e;
                        break;
                    }
                }
            } else if (n.value === t) {
                n.label = e;
                break;
            }
        }
    }

    handleSelectButtonKeydown(t) {
        switch (t.key) {
            case "ArrowDown":
                (t.preventDefault(),
                    t.stopPropagation(),
                    this.isOpen ? this.focusNextOption() : this.openDropdown());
                break;
            case "ArrowUp":
                (t.preventDefault(),
                    t.stopPropagation(),
                    this.isOpen
                        ? this.focusPreviousOption()
                        : this.openDropdown());
                break;
            case " ":
                if ((t.preventDefault(), this.isOpen)) {
                    if (this.selectedIndex >= 0) {
                        const e = this.getVisibleOptions()[this.selectedIndex];
                        e && e.click();
                    }
                } else this.openDropdown();
                break;
            case "Enter":
                break;
            case "Escape":
                this.isOpen && (t.preventDefault(), this.closeDropdown());
                break;
            case "Tab":
                this.isOpen && this.closeDropdown();
                break;
            default:
                if (
                    this.isSearchable &&
                    !t.ctrlKey &&
                    !t.metaKey &&
                    !t.altKey &&
                    typeof t.key === "string" &&
                    t.key.length === 1
                ) {
                    t.preventDefault();
                    const e = t.key;
                    (this.isOpen || this.openDropdown(),
                        this.searchInput &&
                            (this.searchInput.focus(),
                            (this.searchInput.value =
                                (this.searchInput.value || "") + e),
                            this.searchInput.dispatchEvent(
                                new Event("input", { bubbles: !0 }),
                            )));
                }
                break;
        }
    }

    handleDropdownKeydown(t) {
        switch (t.key) {
            case "ArrowDown":
                (t.preventDefault(),
                    t.stopPropagation(),
                    this.focusNextOption());
                break;
            case "ArrowUp":
                (t.preventDefault(),
                    t.stopPropagation(),
                    this.focusPreviousOption());
                break;
            case " ":
                if ((t.preventDefault(), this.selectedIndex >= 0)) {
                    const e = this.getVisibleOptions()[this.selectedIndex];
                    e && e.click();
                }
                break;
            case "Enter":
                if ((t.preventDefault(), this.selectedIndex >= 0)) {
                    const e = this.getVisibleOptions()[this.selectedIndex];
                    e && e.click();
                } else {
                    const e = this.element.closest("form");
                    e && e.submit();
                }
                break;
            case "Escape":
                (t.preventDefault(),
                    this.closeDropdown(),
                    this.selectButton.focus());
                break;
            case "Tab":
                this.closeDropdown();
                break;
            default:
                if (
                    this.isSearchable &&
                    !t.ctrlKey &&
                    !t.metaKey &&
                    !t.altKey &&
                    typeof t.key === "string" &&
                    t.key.length === 1
                ) {
                    t.preventDefault();
                    const e = t.key;
                    this.searchInput &&
                        (this.searchInput.focus(),
                        (this.searchInput.value =
                            (this.searchInput.value || "") + e),
                        this.searchInput.dispatchEvent(
                            new Event("input", { bubbles: !0 }),
                        ));
                }
                break;
        }
    }

    toggleDropdown() {
        if (!this.isDisabled) {
            if (this.isOpen) {
                this.closeDropdown();
                return;
            }
            (this.isMultiple &&
                !this.isSearchable &&
                !this.hasAvailableOptions()) ||
                this.openDropdown();
        }
    }

    hasAvailableOptions() {
        for (const t of this.options) {
            if (t.options && Array.isArray(t.options)) {
                for (const e of t.options) {
                    if (
                        !Array.isArray(this.state) ||
                        !this.state.includes(e.value)
                    ) {
                        return !0;
                    }
                }
            } else if (
                !Array.isArray(this.state) ||
                !this.state.includes(t.value)
            ) {
                return !0;
            }
        }
        return !1;
    }

    async openDropdown() {
        ((this.dropdown.style.display = "block"),
            (this.dropdown.style.opacity = "0"));
        const t =
            this.selectButton.closest(".fi-fixed-positioning-context") !==
                null &&
            this.selectButton.closest(".fi-absolute-positioning-context") ===
                null;
        if (
            ((this.dropdown.style.position = t ? "fixed" : "absolute"),
            (this.dropdown.style.width = `${this.selectButton.offsetWidth}px`),
            this.selectButton.setAttribute("aria-expanded", "true"),
            (this.isOpen = !0),
            this.positionDropdown(),
            this.resizeListener ||
                ((this.resizeListener = () => {
                    ((this.dropdown.style.width = `${this.selectButton.offsetWidth}px`),
                        this.positionDropdown());
                }),
                window.addEventListener("resize", this.resizeListener)),
            this.scrollListener ||
                ((this.scrollListener = () => this.positionDropdown()),
                window.addEventListener("scroll", this.scrollListener, !0)),
            (this.dropdown.style.opacity = "1"),
            this.hasDynamicOptions && this.getOptionsUsing)
        ) {
            this.showLoadingState(!1);
            try {
                const e = await this.getOptionsUsing();
                const i = Array.isArray(e)
                    ? e
                    : e && Array.isArray(e.options)
                      ? e.options
                      : [];
                ((this.options = i),
                    (this.originalOptions = JSON.parse(JSON.stringify(i))),
                    this.populateLabelRepositoryFromOptions(i),
                    this.renderOptions());
            } catch (e) {
                (console.error("Error fetching options:", e),
                    this.hideLoadingState());
            }
        }
        if ((this.hideLoadingState(), this.isSearchable && this.searchInput)) {
            ((this.searchInput.value = ""),
                this.searchInput.focus(),
                (this.searchQuery = ""),
                (this.options = JSON.parse(
                    JSON.stringify(this.originalOptions),
                )),
                this.renderOptions());
        } else {
            this.selectedIndex = -1;
            const e = this.getVisibleOptions();
            if (this.isMultiple) {
                if (Array.isArray(this.state) && this.state.length > 0) {
                    for (let i = 0; i < e.length; i++) {
                        if (
                            this.state.includes(e[i].getAttribute("data-value"))
                        ) {
                            this.selectedIndex = i;
                            break;
                        }
                    }
                }
            } else {
                for (let i = 0; i < e.length; i++) {
                    if (e[i].getAttribute("data-value") === this.state) {
                        this.selectedIndex = i;
                        break;
                    }
                }
            }
            (this.selectedIndex === -1 &&
                e.length > 0 &&
                (this.selectedIndex = 0),
                this.selectedIndex >= 0 &&
                    (e[this.selectedIndex].classList.add("fi-selected"),
                    e[this.selectedIndex].focus()));
        }
    }

    positionDropdown() {
        const t = this.position === "top" ? "top-start" : "bottom-start";
        const e = [Jt(4), jt({ padding: 5 })];
        this.position !== "top" && this.position !== "bottom" && e.push(qt());
        const i =
            this.selectButton.closest(".fi-fixed-positioning-context") !==
                null &&
            this.selectButton.closest(".fi-absolute-positioning-context") ===
                null;
        Xt(this.selectButton, this.dropdown, {
            placement: t,
            middleware: e,
            strategy: i ? "fixed" : "absolute",
        }).then(({ x: n, y: o }) => {
            Object.assign(this.dropdown.style, {
                left: `${n}px`,
                top: `${o}px`,
            });
        });
    }

    closeDropdown() {
        ((this.dropdown.style.display = "none"),
            this.selectButton.setAttribute("aria-expanded", "false"),
            (this.isOpen = !1),
            this.resizeListener &&
                (window.removeEventListener("resize", this.resizeListener),
                (this.resizeListener = null)),
            this.scrollListener &&
                (window.removeEventListener("scroll", this.scrollListener, !0),
                (this.scrollListener = null)),
            this.getVisibleOptions().forEach((e) => {
                e.classList.remove("fi-selected");
            }));
    }

    focusNextOption() {
        const t = this.getVisibleOptions();
        if (t.length !== 0) {
            if (
                (this.selectedIndex >= 0 &&
                    this.selectedIndex < t.length &&
                    t[this.selectedIndex].classList.remove("fi-selected"),
                this.selectedIndex === t.length - 1 &&
                    this.isSearchable &&
                    this.searchInput)
            ) {
                ((this.selectedIndex = -1),
                    this.searchInput.focus(),
                    this.dropdown.removeAttribute("aria-activedescendant"));
                return;
            }
            ((this.selectedIndex = (this.selectedIndex + 1) % t.length),
                t[this.selectedIndex].classList.add("fi-selected"),
                t[this.selectedIndex].focus(),
                t[this.selectedIndex].id &&
                    this.dropdown.setAttribute(
                        "aria-activedescendant",
                        t[this.selectedIndex].id,
                    ),
                this.scrollOptionIntoView(t[this.selectedIndex]));
        }
    }

    focusPreviousOption() {
        const t = this.getVisibleOptions();
        if (t.length !== 0) {
            if (
                (this.selectedIndex >= 0 &&
                    this.selectedIndex < t.length &&
                    t[this.selectedIndex].classList.remove("fi-selected"),
                (this.selectedIndex === 0 || this.selectedIndex === -1) &&
                    this.isSearchable &&
                    this.searchInput)
            ) {
                ((this.selectedIndex = -1),
                    this.searchInput.focus(),
                    this.dropdown.removeAttribute("aria-activedescendant"));
                return;
            }
            ((this.selectedIndex =
                (this.selectedIndex - 1 + t.length) % t.length),
                t[this.selectedIndex].classList.add("fi-selected"),
                t[this.selectedIndex].focus(),
                t[this.selectedIndex].id &&
                    this.dropdown.setAttribute(
                        "aria-activedescendant",
                        t[this.selectedIndex].id,
                    ),
                this.scrollOptionIntoView(t[this.selectedIndex]));
        }
    }

    scrollOptionIntoView(t) {
        if (!t) return;
        const e = this.dropdown.getBoundingClientRect();
        const i = t.getBoundingClientRect();
        i.bottom > e.bottom
            ? (this.dropdown.scrollTop += i.bottom - e.bottom)
            : i.top < e.top && (this.dropdown.scrollTop -= e.top - i.top);
    }

    getVisibleOptions() {
        let t = [];
        this.optionsList.classList.contains("fi-dropdown-list")
            ? (t = Array.from(
                  this.optionsList.querySelectorAll(
                      ':scope > li[role="option"]',
                  ),
              ))
            : (t = Array.from(
                  this.optionsList.querySelectorAll(
                      ':scope > ul.fi-dropdown-list > li[role="option"]',
                  ),
              ));
        const e = Array.from(
            this.optionsList.querySelectorAll(
                'li.fi-select-input-option-group > ul > li[role="option"]',
            ),
        );
        return [...t, ...e];
    }

    getSelectedOptionLabels() {
        if (!Array.isArray(this.state) || this.state.length === 0) return {};
        const t = {};
        for (const e of this.state) {
            let i = !1;
            for (const n of this.options) {
                if (n.options && Array.isArray(n.options)) {
                    for (const o of n.options) {
                        if (o.value === e) {
                            ((t[e] = o.label), (i = !0));
                            break;
                        }
                    }
                    if (i) break;
                } else if (n.value === e) {
                    ((t[e] = n.label), (i = !0));
                    break;
                }
            }
        }
        return t;
    }

    handleSearch(t) {
        const e = t.target.value.trim();
        if (
            ((this.searchQuery = e),
            this.searchTimeout && clearTimeout(this.searchTimeout),
            e === "")
        ) {
            ((this.options = JSON.parse(JSON.stringify(this.originalOptions))),
                this.renderOptions());
            return;
        }
        if (
            !this.getSearchResultsUsing ||
            typeof this.getSearchResultsUsing !== "function" ||
            !this.hasDynamicSearchResults
        ) {
            this.filterOptions(e);
            return;
        }
        this.searchTimeout = setTimeout(async () => {
            ((this.searchTimeout = null), (this.isSearching = !0));
            try {
                this.showLoadingState(!0);
                const i = await this.getSearchResultsUsing(e);
                const n = Array.isArray(i)
                    ? i
                    : i && Array.isArray(i.options)
                      ? i.options
                      : [];
                ((this.options = n),
                    this.populateLabelRepositoryFromOptions(n),
                    this.hideLoadingState(),
                    this.renderOptions(),
                    this.isOpen && this.positionDropdown(),
                    this.options.length === 0 && this.showNoResultsMessage());
            } catch (i) {
                (console.error("Error fetching search results:", i),
                    this.hideLoadingState(),
                    (this.options = JSON.parse(
                        JSON.stringify(this.originalOptions),
                    )),
                    this.renderOptions());
            } finally {
                this.isSearching = !1;
            }
        }, this.searchDebounce);
    }

    showLoadingState(t = !1) {
        (this.optionsList.parentNode === this.dropdown &&
            this.dropdown.removeChild(this.optionsList),
            this.hideLoadingState());
        const e = document.createElement("div");
        ((e.className = "fi-select-input-message"),
            (e.textContent = t ? this.searchingMessage : this.loadingMessage),
            this.dropdown.appendChild(e));
    }

    hideLoadingState() {
        const t = this.dropdown.querySelector(".fi-select-input-message");
        t && t.remove();
    }

    showNoResultsMessage() {
        (this.optionsList.parentNode === this.dropdown &&
            this.dropdown.removeChild(this.optionsList),
            this.hideLoadingState());
        const t = document.createElement("div");
        ((t.className = "fi-select-input-message"),
            (t.textContent = this.noSearchResultsMessage),
            this.dropdown.appendChild(t));
    }

    filterOptions(t) {
        const e = this.searchableOptionFields.includes("label");
        const i = this.searchableOptionFields.includes("value");
        t = t.toLowerCase();
        const n = [];
        for (const o of this.originalOptions) {
            if (o.options && Array.isArray(o.options)) {
                const r = o.options.filter(
                    (l) =>
                        (e && l.label.toLowerCase().includes(t)) ||
                        (i && String(l.value).toLowerCase().includes(t)),
                );
                r.length > 0 && n.push({ label: o.label, options: r });
            } else {
                ((e && o.label.toLowerCase().includes(t)) ||
                    (i && String(o.value).toLowerCase().includes(t))) &&
                    n.push(o);
            }
        }
        ((this.options = n),
            this.renderOptions(),
            this.options.length === 0 && this.showNoResultsMessage(),
            this.isOpen && this.positionDropdown());
    }

    selectOption(t) {
        if (this.isDisabled) return;
        if (!this.isMultiple) {
            ((this.state = t),
                this.updateSelectedDisplay(),
                this.renderOptions(),
                this.closeDropdown(),
                this.selectButton.focus(),
                this.onStateChange(this.state));
            return;
        }
        let e = Array.isArray(this.state) ? [...this.state] : [];
        if (e.includes(t)) {
            const n = this.selectedDisplay.querySelector(`[data-value="${t}"]`);
            if (y(n)) {
                const o = n.parentElement;
                y(o) && o.children.length === 1
                    ? ((e = e.filter((r) => r !== t)),
                      (this.state = e),
                      this.updateSelectedDisplay())
                    : (n.remove(),
                      (e = e.filter((r) => r !== t)),
                      (this.state = e));
            } else {
                ((e = e.filter((o) => o !== t)),
                    (this.state = e),
                    this.updateSelectedDisplay());
            }
            (this.renderOptions(),
                this.isOpen && this.positionDropdown(),
                this.maintainFocusInMultipleMode(),
                this.onStateChange(this.state));
            return;
        }
        if (this.maxItems && e.length >= this.maxItems) {
            this.maxItemsMessage && alert(this.maxItemsMessage);
            return;
        }
        (e.push(t), (this.state = e));
        const i = this.selectedDisplay.querySelector(
            ".fi-select-input-value-badges-ctn",
        );
        (N(i) ? this.updateSelectedDisplay() : this.addSingleBadge(t, i),
            this.renderOptions(),
            this.isOpen && this.positionDropdown(),
            this.maintainFocusInMultipleMode(),
            this.onStateChange(this.state));
    }

    async addSingleBadge(t, e) {
        let i = this.labelRepository[t];
        if (
            (N(i) &&
                ((i = this.getSelectedOptionLabel(t)),
                y(i) && (this.labelRepository[t] = i)),
            N(i) && this.getOptionLabelsUsing)
        ) {
            try {
                const o = await this.getOptionLabelsUsing();
                for (const r of o) {
                    if (y(r) && r.value === t && r.label !== void 0) {
                        ((i = r.label), (this.labelRepository[t] = i));
                        break;
                    }
                }
            } catch (o) {
                console.error("Error fetching option label:", o);
            }
        }
        N(i) && (i = t);
        const n = this.createBadgeElement(t, i);
        e.appendChild(n);
    }

    maintainFocusInMultipleMode() {
        if (this.isSearchable && this.searchInput) {
            this.searchInput.focus();
            return;
        }
        const t = this.getVisibleOptions();
        if (t.length !== 0) {
            if (
                ((this.selectedIndex = -1),
                Array.isArray(this.state) && this.state.length > 0)
            ) {
                for (let e = 0; e < t.length; e++) {
                    if (this.state.includes(t[e].getAttribute("data-value"))) {
                        this.selectedIndex = e;
                        break;
                    }
                }
            }
            (this.selectedIndex === -1 && (this.selectedIndex = 0),
                t[this.selectedIndex].classList.add("fi-selected"),
                t[this.selectedIndex].focus());
        }
    }

    disable() {
        this.isDisabled ||
            ((this.isDisabled = !0),
            this.applyDisabledState(),
            this.isOpen && this.closeDropdown());
    }

    enable() {
        this.isDisabled && ((this.isDisabled = !1), this.applyDisabledState());
    }

    applyDisabledState() {
        if (this.isDisabled) {
            if (
                (this.selectButton.setAttribute("disabled", "disabled"),
                this.selectButton.setAttribute("aria-disabled", "true"),
                this.selectButton.classList.add("fi-disabled"),
                this.isMultiple &&
                    this.container
                        .querySelectorAll(".fi-select-input-badge-remove")
                        .forEach((e) => {
                            (e.setAttribute("disabled", "disabled"),
                                e.classList.add("fi-disabled"));
                        }),
                !this.isMultiple && this.canSelectPlaceholder)
            ) {
                const t = this.container.querySelector(
                    ".fi-select-input-value-remove-btn",
                );
                t &&
                    (t.setAttribute("disabled", "disabled"),
                    t.classList.add("fi-disabled"));
            }
            this.isSearchable &&
                this.searchInput &&
                (this.searchInput.setAttribute("disabled", "disabled"),
                this.searchInput.classList.add("fi-disabled"));
        } else {
            if (
                (this.selectButton.removeAttribute("disabled"),
                this.selectButton.removeAttribute("aria-disabled"),
                this.selectButton.classList.remove("fi-disabled"),
                this.isMultiple &&
                    this.container
                        .querySelectorAll(".fi-select-input-badge-remove")
                        .forEach((e) => {
                            (e.removeAttribute("disabled"),
                                e.classList.remove("fi-disabled"));
                        }),
                !this.isMultiple && this.canSelectPlaceholder)
            ) {
                const t = this.container.querySelector(
                    ".fi-select-input-value-remove-btn",
                );
                t &&
                    (t.removeAttribute("disabled"),
                    t.classList.add("fi-disabled"));
            }
            this.isSearchable &&
                this.searchInput &&
                (this.searchInput.removeAttribute("disabled"),
                this.searchInput.classList.remove("fi-disabled"));
        }
    }

    destroy() {
        (this.selectButton &&
            this.buttonClickListener &&
            this.selectButton.removeEventListener(
                "click",
                this.buttonClickListener,
            ),
            this.documentClickListener &&
                document.removeEventListener(
                    "click",
                    this.documentClickListener,
                ),
            this.selectButton &&
                this.buttonKeydownListener &&
                this.selectButton.removeEventListener(
                    "keydown",
                    this.buttonKeydownListener,
                ),
            this.dropdown &&
                this.dropdownKeydownListener &&
                this.dropdown.removeEventListener(
                    "keydown",
                    this.dropdownKeydownListener,
                ),
            this.resizeListener &&
                (window.removeEventListener("resize", this.resizeListener),
                (this.resizeListener = null)),
            this.scrollListener &&
                (window.removeEventListener("scroll", this.scrollListener, !0),
                (this.scrollListener = null)),
            this.refreshOptionLabelListener &&
                window.removeEventListener(
                    "filament-forms::select.refreshSelectedOptionLabel",
                    this.refreshOptionLabelListener,
                ),
            this.isOpen && this.closeDropdown(),
            this.searchTimeout &&
                (clearTimeout(this.searchTimeout), (this.searchTimeout = null)),
            this.container && this.container.remove());
    }
};
function Ee({
    canOptionLabelsWrap: s,
    canSelectPlaceholder: t,
    isHtmlAllowed: e,
    getOptionLabelUsing: i,
    getOptionLabelsUsing: n,
    getOptionsUsing: o,
    getSearchResultsUsing: r,
    initialOptionLabel: l,
    initialOptionLabels: a,
    initialState: c,
    isAutofocused: h,
    isDisabled: d,
    isMultiple: p,
    isSearchable: f,
    hasDynamicOptions: u,
    hasDynamicSearchResults: m,
    livewireId: g,
    loadingMessage: w,
    maxItems: b,
    maxItemsMessage: v,
    noSearchResultsMessage: L,
    options: x,
    optionsLimit: J,
    placeholder: Q,
    position: W,
    searchDebounce: F,
    searchingMessage: I,
    searchPrompt: j,
    searchableOptionFields: q,
    state: X,
    statePath: A,
}) {
    return {
        select: null,
        state: X,
        init() {
            ((this.select = new ut({
                element: this.$refs.select,
                options: x,
                placeholder: Q,
                state: this.state,
                canOptionLabelsWrap: s,
                canSelectPlaceholder: t,
                initialOptionLabel: l,
                initialOptionLabels: a,
                initialState: c,
                isHtmlAllowed: e,
                isAutofocused: h,
                isDisabled: d,
                isMultiple: p,
                isSearchable: f,
                getOptionLabelUsing: i,
                getOptionLabelsUsing: n,
                getOptionsUsing: o,
                getSearchResultsUsing: r,
                hasDynamicOptions: u,
                hasDynamicSearchResults: m,
                searchPrompt: j,
                searchDebounce: F,
                loadingMessage: w,
                searchingMessage: I,
                noSearchResultsMessage: L,
                maxItems: b,
                maxItemsMessage: v,
                optionsLimit: J,
                position: W,
                searchableOptionFields: q,
                livewireId: g,
                statePath: A,
                onStateChange: (k) => {
                    this.state = k;
                },
            })),
                this.$watch("state", (k) => {
                    this.select &&
                        this.select.state !== k &&
                        ((this.select.state = k),
                        this.select.updateSelectedDisplay(),
                        this.select.renderOptions());
                }));
        },
        destroy() {
            this.select && (this.select.destroy(), (this.select = null));
        },
    };
}
export { Ee as default };
