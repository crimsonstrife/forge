const tt = Math.min;
const $ = Math.max;
const et = Math.round;
const T = (i) => ({ x: i, y: i });
const Gt = { left: "right", right: "left", bottom: "top", top: "bottom" };
const Qt = { start: "end", end: "start" };
function mt(i, t, e) {
    return $(i, tt(t, e));
}
function it(i, t) {
    return typeof i === "function" ? i(t) : i;
}
function z(i) {
    return i.split("-")[0];
}
function st(i) {
    return i.split("-")[1];
}
function gt(i) {
    return i === "x" ? "y" : "x";
}
function bt(i) {
    return i === "y" ? "height" : "width";
}
const Zt = new Set(["top", "bottom"]);
function B(i) {
    return Zt.has(z(i)) ? "y" : "x";
}
function wt(i) {
    return gt(B(i));
}
function Ot(i, t, e) {
    e === void 0 && (e = !1);
    const s = st(i);
    const n = wt(i);
    const o = bt(n);
    let r =
        n === "x"
            ? s === (e ? "end" : "start")
                ? "right"
                : "left"
            : s === "start"
              ? "bottom"
              : "top";
    return (t.reference[o] > t.floating[o] && (r = Z(r)), [r, Z(r)]);
}
function At(i) {
    const t = Z(i);
    return [lt(i), t, lt(t)];
}
function lt(i) {
    return i.replace(/start|end/g, (t) => Qt[t]);
}
const vt = ["left", "right"];
const Lt = ["right", "left"];
const te = ["top", "bottom"];
const ee = ["bottom", "top"];
function ie(i, t, e) {
    switch (i) {
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
function St(i, t, e, s) {
    const n = st(i);
    let o = ie(z(i), e === "start", s);
    return (
        n && ((o = o.map((r) => r + "-" + n)), t && (o = o.concat(o.map(lt)))),
        o
    );
}
function Z(i) {
    return i.replace(/left|right|bottom|top/g, (t) => Gt[t]);
}
function se(i) {
    return { top: 0, right: 0, bottom: 0, left: 0, ...i };
}
function Ct(i) {
    return typeof i !== "number"
        ? se(i)
        : { top: i, right: i, bottom: i, left: i };
}
function U(i) {
    const { x: t, y: e, width: s, height: n } = i;
    return {
        width: s,
        height: n,
        top: e,
        left: t,
        right: t + s,
        bottom: e + n,
        x: t,
        y: e,
    };
}
function Dt(i, t, e) {
    const { reference: s, floating: n } = i;
    const o = B(t);
    const r = wt(t);
    const l = bt(r);
    const a = z(t);
    const c = o === "y";
    const d = s.x + s.width / 2 - n.width / 2;
    const h = s.y + s.height / 2 - n.height / 2;
    const p = s[l] / 2 - n[l] / 2;
    let f;
    switch (a) {
        case "top":
            f = { x: d, y: s.y - n.height };
            break;
        case "bottom":
            f = { x: d, y: s.y + s.height };
            break;
        case "right":
            f = { x: s.x + s.width, y: h };
            break;
        case "left":
            f = { x: s.x - n.width, y: h };
            break;
        default:
            f = { x: s.x, y: s.y };
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
const Et = async (i, t, e) => {
    const {
        placement: s = "bottom",
        strategy: n = "absolute",
        middleware: o = [],
        platform: r,
    } = e;
    const l = o.filter(Boolean);
    const a = await (r.isRTL == null ? void 0 : r.isRTL(t));
    let c = await r.getElementRects({ reference: i, floating: t, strategy: n });
    let { x: d, y: h } = Dt(c, s, a);
    let p = s;
    let f = {};
    let u = 0;
    for (let m = 0; m < l.length; m++) {
        const { name: g, fn: y } = l[m];
        const {
            x: b,
            y: v,
            data: O,
            reset: x,
        } = await y({
            x: d,
            y: h,
            initialPlacement: s,
            placement: p,
            strategy: n,
            middlewareData: f,
            rects: c,
            platform: r,
            elements: { reference: i, floating: t },
        });
        ((d = b ?? d),
            (h = v ?? h),
            (f = { ...f, [g]: { ...f[g], ...O } }),
            x &&
                u <= 50 &&
                (u++,
                typeof x === "object" &&
                    (x.placement && (p = x.placement),
                    x.rects &&
                        (c =
                            x.rects === !0
                                ? await r.getElementRects({
                                      reference: i,
                                      floating: t,
                                      strategy: n,
                                  })
                                : x.rects),
                    ({ x: d, y: h } = Dt(c, p, a))),
                (m = -1)));
    }
    return { x: d, y: h, placement: p, strategy: n, middlewareData: f };
};
async function yt(i, t) {
    let e;
    t === void 0 && (t = {});
    const { x: s, y: n, platform: o, rects: r, elements: l, strategy: a } = i;
    const {
        boundary: c = "clippingAncestors",
        rootBoundary: d = "viewport",
        elementContext: h = "floating",
        altBoundary: p = !1,
        padding: f = 0,
    } = it(t, i);
    const u = Ct(f);
    const g = l[p ? (h === "floating" ? "reference" : "floating") : h];
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
            rootBoundary: d,
            strategy: a,
        }),
    );
    const b =
        h === "floating"
            ? { x: s, y: n, width: r.floating.width, height: r.floating.height }
            : r.reference;
    const v = await (o.getOffsetParent == null
        ? void 0
        : o.getOffsetParent(l.floating));
    const O = (await (o.isElement == null ? void 0 : o.isElement(v)))
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
        top: (y.top - x.top + u.top) / O.y,
        bottom: (x.bottom - y.bottom + u.bottom) / O.y,
        left: (y.left - x.left + u.left) / O.x,
        right: (x.right - y.right + u.right) / O.x,
    };
}
const Rt = function (i) {
    return (
        i === void 0 && (i = {}),
        {
            name: "flip",
            options: i,
            async fn(t) {
                let e, s;
                const {
                    placement: n,
                    middlewareData: o,
                    rects: r,
                    initialPlacement: l,
                    platform: a,
                    elements: c,
                } = t;
                const {
                    mainAxis: d = !0,
                    crossAxis: h = !0,
                    fallbackPlacements: p,
                    fallbackStrategy: f = "bestFit",
                    fallbackAxisSideDirection: u = "none",
                    flipAlignment: m = !0,
                    ...g
                } = it(i, t);
                if ((e = o.arrow) != null && e.alignmentOffset) return {};
                const y = z(n);
                const b = B(l);
                const v = z(l) === l;
                const O = await (a.isRTL == null
                    ? void 0
                    : a.isRTL(c.floating));
                const x = p || (v || !m ? [Z(l)] : At(l));
                const q = u !== "none";
                !p && q && x.push(...St(l, m, u, O));
                const Y = [l, ...x];
                const W = await yt(t, g);
                const L = [];
                let S = ((s = o.flip) == null ? void 0 : s.overflows) || [];
                if ((d && L.push(W[y]), h)) {
                    const E = Ot(n, r, O);
                    L.push(W[E[0]], W[E[1]]);
                }
                if (
                    ((S = [...S, { placement: n, overflows: L }]),
                    !L.every((E) => E <= 0))
                ) {
                    let H, G;
                    const E =
                        (((H = o.flip) == null ? void 0 : H.index) || 0) + 1;
                    const J = Y[E];
                    if (
                        J &&
                        (!(h === "alignment" ? b !== B(J) : !1) ||
                            S.every((I) =>
                                B(I.placement) === b ? I.overflows[0] > 0 : !0,
                            ))
                    ) {
                        return {
                            data: { index: E, overflows: S },
                            reset: { placement: J },
                        };
                    }
                    let R =
                        (G = S.filter((P) => P.overflows[0] <= 0).sort(
                            (P, I) => P.overflows[1] - I.overflows[1],
                        )[0]) == null
                            ? void 0
                            : G.placement;
                    if (!R) {
                        switch (f) {
                            case "bestFit": {
                                let Q;
                                const P =
                                    (Q = S.filter((I) => {
                                        if (q) {
                                            const V = B(I.placement);
                                            return V === b || V === "y";
                                        }
                                        return !0;
                                    })
                                        .map((I) => [
                                            I.placement,
                                            I.overflows
                                                .filter((V) => V > 0)
                                                .reduce((V, Yt) => V + Yt, 0),
                                        ])
                                        .sort((I, V) => I[1] - V[1])[0]) == null
                                        ? void 0
                                        : Q[0];
                                P && (R = P);
                                break;
                            }
                            case "initialPlacement":
                                R = l;
                                break;
                        }
                    }
                    if (n !== R) return { reset: { placement: R } };
                }
                return {};
            },
        }
    );
};
const ne = new Set(["left", "top"]);
async function oe(i, t) {
    const { placement: e, platform: s, elements: n } = i;
    const o = await (s.isRTL == null ? void 0 : s.isRTL(n.floating));
    const r = z(e);
    const l = st(e);
    const a = B(e) === "y";
    const c = ne.has(r) ? -1 : 1;
    const d = o && a ? -1 : 1;
    const h = it(t, i);
    let {
        mainAxis: p,
        crossAxis: f,
        alignmentAxis: u,
    } = typeof h === "number"
        ? { mainAxis: h, crossAxis: 0, alignmentAxis: null }
        : {
              mainAxis: h.mainAxis || 0,
              crossAxis: h.crossAxis || 0,
              alignmentAxis: h.alignmentAxis,
          };
    return (
        l && typeof u === "number" && (f = l === "end" ? u * -1 : u),
        a ? { x: f * d, y: p * c } : { x: p * c, y: f * d }
    );
}
const It = function (i) {
    return (
        i === void 0 && (i = 0),
        {
            name: "offset",
            options: i,
            async fn(t) {
                let e, s;
                const { x: n, y: o, placement: r, middlewareData: l } = t;
                const a = await oe(t, i);
                return r === ((e = l.offset) == null ? void 0 : e.placement) &&
                    (s = l.arrow) != null &&
                    s.alignmentOffset
                    ? {}
                    : { x: n + a.x, y: o + a.y, data: { ...a, placement: r } };
            },
        }
    );
};
const Tt = function (i) {
    return (
        i === void 0 && (i = {}),
        {
            name: "shift",
            options: i,
            async fn(t) {
                const { x: e, y: s, placement: n } = t;
                const {
                    mainAxis: o = !0,
                    crossAxis: r = !1,
                    limiter: l = {
                        fn: (g) => {
                            const { x: y, y: b } = g;
                            return { x: y, y: b };
                        },
                    },
                    ...a
                } = it(i, t);
                const c = { x: e, y: s };
                const d = await yt(t, a);
                const h = B(z(n));
                const p = gt(h);
                let f = c[p];
                let u = c[h];
                if (o) {
                    const g = p === "y" ? "top" : "left";
                    const y = p === "y" ? "bottom" : "right";
                    const b = f + d[g];
                    const v = f - d[y];
                    f = mt(b, f, v);
                }
                if (r) {
                    const g = h === "y" ? "top" : "left";
                    const y = h === "y" ? "bottom" : "right";
                    const b = u + d[g];
                    const v = u - d[y];
                    u = mt(b, u, v);
                }
                const m = l.fn({ ...t, [p]: f, [h]: u });
                return {
                    ...m,
                    data: {
                        x: m.x - e,
                        y: m.y - s,
                        enabled: { [p]: o, [h]: r },
                    },
                };
            },
        }
    );
};
function ct() {
    return typeof window < "u";
}
function _(i) {
    return Mt(i) ? (i.nodeName || "").toLowerCase() : "#document";
}
function A(i) {
    let t;
    return (
        (i == null || (t = i.ownerDocument) == null ? void 0 : t.defaultView) ||
        window
    );
}
function M(i) {
    let t;
    return (t = (Mt(i) ? i.ownerDocument : i.document) || window.document) ==
        null
        ? void 0
        : t.documentElement;
}
function Mt(i) {
    return ct() ? i instanceof Node || i instanceof A(i).Node : !1;
}
function C(i) {
    return ct() ? i instanceof Element || i instanceof A(i).Element : !1;
}
function k(i) {
    return ct()
        ? i instanceof HTMLElement || i instanceof A(i).HTMLElement
        : !1;
}
function kt(i) {
    return !ct() || typeof ShadowRoot > "u"
        ? !1
        : i instanceof ShadowRoot || i instanceof A(i).ShadowRoot;
}
const re = new Set(["inline", "contents"]);
function j(i) {
    const { overflow: t, overflowX: e, overflowY: s, display: n } = D(i);
    return /auto|scroll|overlay|hidden|clip/.test(t + s + e) && !re.has(n);
}
const le = new Set(["table", "td", "th"]);
function Pt(i) {
    return le.has(_(i));
}
const ae = [":popover-open", ":modal"];
function nt(i) {
    return ae.some((t) => {
        try {
            return i.matches(t);
        } catch {
            return !1;
        }
    });
}
const ce = ["transform", "translate", "scale", "rotate", "perspective"];
const de = [
    "transform",
    "translate",
    "scale",
    "rotate",
    "perspective",
    "filter",
];
const he = ["paint", "layout", "strict", "content"];
function dt(i) {
    const t = ht();
    const e = C(i) ? D(i) : i;
    return (
        ce.some((s) => (e[s] ? e[s] !== "none" : !1)) ||
        (e.containerType ? e.containerType !== "normal" : !1) ||
        (!t && (e.backdropFilter ? e.backdropFilter !== "none" : !1)) ||
        (!t && (e.filter ? e.filter !== "none" : !1)) ||
        de.some((s) => (e.willChange || "").includes(s)) ||
        he.some((s) => (e.contain || "").includes(s))
    );
}
function Bt(i) {
    let t = N(i);
    for (; k(t) && !K(t); ) {
        if (dt(t)) return t;
        if (nt(t)) return null;
        t = N(t);
    }
    return null;
}
function ht() {
    return typeof CSS > "u" || !CSS.supports
        ? !1
        : CSS.supports("-webkit-backdrop-filter", "none");
}
const fe = new Set(["html", "body", "#document"]);
function K(i) {
    return fe.has(_(i));
}
function D(i) {
    return A(i).getComputedStyle(i);
}
function ot(i) {
    return C(i)
        ? { scrollLeft: i.scrollLeft, scrollTop: i.scrollTop }
        : { scrollLeft: i.scrollX, scrollTop: i.scrollY };
}
function N(i) {
    if (_(i) === "html") return i;
    const t = i.assignedSlot || i.parentNode || (kt(i) && i.host) || M(i);
    return kt(t) ? t.host : t;
}
function Nt(i) {
    const t = N(i);
    return K(t)
        ? i.ownerDocument
            ? i.ownerDocument.body
            : i.body
        : k(t) && j(t)
          ? t
          : Nt(t);
}
function at(i, t, e) {
    let s;
    (t === void 0 && (t = []), e === void 0 && (e = !0));
    const n = Nt(i);
    const o = n === ((s = i.ownerDocument) == null ? void 0 : s.body);
    const r = A(n);
    if (o) {
        const l = ft(r);
        return t.concat(
            r,
            r.visualViewport || [],
            j(n) ? n : [],
            l && e ? at(l) : [],
        );
    }
    return t.concat(n, at(n, [], e));
}
function ft(i) {
    return i.parent && Object.getPrototypeOf(i.parent) ? i.frameElement : null;
}
function $t(i) {
    const t = D(i);
    let e = parseFloat(t.width) || 0;
    let s = parseFloat(t.height) || 0;
    const n = k(i);
    const o = n ? i.offsetWidth : e;
    const r = n ? i.offsetHeight : s;
    const l = et(e) !== o || et(s) !== r;
    return (l && ((e = o), (s = r)), { width: e, height: s, $: l });
}
function zt(i) {
    return C(i) ? i : i.contextElement;
}
function X(i) {
    const t = zt(i);
    if (!k(t)) return T(1);
    const e = t.getBoundingClientRect();
    const { width: s, height: n, $: o } = $t(t);
    let r = (o ? et(e.width) : e.width) / s;
    let l = (o ? et(e.height) : e.height) / n;
    return (
        (!r || !Number.isFinite(r)) && (r = 1),
        (!l || !Number.isFinite(l)) && (l = 1),
        { x: r, y: l }
    );
}
const pe = T(0);
function Wt(i) {
    const t = A(i);
    return !ht() || !t.visualViewport
        ? pe
        : { x: t.visualViewport.offsetLeft, y: t.visualViewport.offsetTop };
}
function ue(i, t, e) {
    return (t === void 0 && (t = !1), !e || (t && e !== A(i)) ? !1 : t);
}
function rt(i, t, e, s) {
    (t === void 0 && (t = !1), e === void 0 && (e = !1));
    const n = i.getBoundingClientRect();
    const o = zt(i);
    let r = T(1);
    t && (s ? C(s) && (r = X(s)) : (r = X(i)));
    const l = ue(o, e, s) ? Wt(o) : T(0);
    let a = (n.left + l.x) / r.x;
    let c = (n.top + l.y) / r.y;
    let d = n.width / r.x;
    let h = n.height / r.y;
    if (o) {
        const p = A(o);
        const f = s && C(s) ? A(s) : s;
        let u = p;
        let m = ft(u);
        for (; m && s && f !== u; ) {
            const g = X(m);
            const y = m.getBoundingClientRect();
            const b = D(m);
            const v = y.left + (m.clientLeft + parseFloat(b.paddingLeft)) * g.x;
            const O = y.top + (m.clientTop + parseFloat(b.paddingTop)) * g.y;
            ((a *= g.x),
                (c *= g.y),
                (d *= g.x),
                (h *= g.y),
                (a += v),
                (c += O),
                (u = A(m)),
                (m = ft(u)));
        }
    }
    return U({ width: d, height: h, x: a, y: c });
}
function pt(i, t) {
    const e = ot(i).scrollLeft;
    return t ? t.left + e : rt(M(i)).left + e;
}
function Ut(i, t) {
    const e = i.getBoundingClientRect();
    const s = e.left + t.scrollLeft - pt(i, e);
    const n = e.top + t.scrollTop;
    return { x: s, y: n };
}
function me(i) {
    const { elements: t, rect: e, offsetParent: s, strategy: n } = i;
    const o = n === "fixed";
    const r = M(s);
    const l = t ? nt(t.floating) : !1;
    if (s === r || (l && o)) return e;
    let a = { scrollLeft: 0, scrollTop: 0 };
    let c = T(1);
    const d = T(0);
    const h = k(s);
    if ((h || (!h && !o)) && ((_(s) !== "body" || j(r)) && (a = ot(s)), k(s))) {
        const f = rt(s);
        ((c = X(s)), (d.x = f.x + s.clientLeft), (d.y = f.y + s.clientTop));
    }
    const p = r && !h && !o ? Ut(r, a) : T(0);
    return {
        width: e.width * c.x,
        height: e.height * c.y,
        x: e.x * c.x - a.scrollLeft * c.x + d.x + p.x,
        y: e.y * c.y - a.scrollTop * c.y + d.y + p.y,
    };
}
function ge(i) {
    return Array.from(i.getClientRects());
}
function be(i) {
    const t = M(i);
    const e = ot(i);
    const s = i.ownerDocument.body;
    const n = $(t.scrollWidth, t.clientWidth, s.scrollWidth, s.clientWidth);
    const o = $(t.scrollHeight, t.clientHeight, s.scrollHeight, s.clientHeight);
    let r = -e.scrollLeft + pt(i);
    const l = -e.scrollTop;
    return (
        D(s).direction === "rtl" && (r += $(t.clientWidth, s.clientWidth) - n),
        { width: n, height: o, x: r, y: l }
    );
}
const Ft = 25;
function we(i, t) {
    const e = A(i);
    const s = M(i);
    const n = e.visualViewport;
    let o = s.clientWidth;
    let r = s.clientHeight;
    let l = 0;
    let a = 0;
    if (n) {
        ((o = n.width), (r = n.height));
        const d = ht();
        (!d || (d && t === "fixed")) && ((l = n.offsetLeft), (a = n.offsetTop));
    }
    const c = pt(s);
    if (c <= 0) {
        const d = s.ownerDocument;
        const h = d.body;
        const p = getComputedStyle(h);
        const f =
            (d.compatMode === "CSS1Compat" &&
                parseFloat(p.marginLeft) + parseFloat(p.marginRight)) ||
            0;
        const u = Math.abs(s.clientWidth - h.clientWidth - f);
        u <= Ft && (o -= u);
    } else c <= Ft && (o += c);
    return { width: o, height: r, x: l, y: a };
}
const ye = new Set(["absolute", "fixed"]);
function xe(i, t) {
    const e = rt(i, !0, t === "fixed");
    const s = e.top + i.clientTop;
    const n = e.left + i.clientLeft;
    const o = k(i) ? X(i) : T(1);
    const r = i.clientWidth * o.x;
    const l = i.clientHeight * o.y;
    const a = n * o.x;
    const c = s * o.y;
    return { width: r, height: l, x: a, y: c };
}
function Ht(i, t, e) {
    let s;
    if (t === "viewport") s = we(i, e);
    else if (t === "document") s = be(M(i));
    else if (C(t)) s = xe(t, e);
    else {
        const n = Wt(i);
        s = { x: t.x - n.x, y: t.y - n.y, width: t.width, height: t.height };
    }
    return U(s);
}
function _t(i, t) {
    const e = N(i);
    return e === t || !C(e) || K(e)
        ? !1
        : D(e).position === "fixed" || _t(e, t);
}
function ve(i, t) {
    const e = t.get(i);
    if (e) return e;
    let s = at(i, [], !1).filter((l) => C(l) && _(l) !== "body");
    let n = null;
    const o = D(i).position === "fixed";
    let r = o ? N(i) : i;
    for (; C(r) && !K(r); ) {
        const l = D(r);
        const a = dt(r);
        (!a && l.position === "fixed" && (n = null),
            (
                o
                    ? !a && !n
                    : (!a &&
                          l.position === "static" &&
                          !!n &&
                          ye.has(n.position)) ||
                      (j(r) && !a && _t(i, r))
            )
                ? (s = s.filter((d) => d !== r))
                : (n = l),
            (r = N(r)));
    }
    return (t.set(i, s), s);
}
function Le(i) {
    const { element: t, boundary: e, rootBoundary: s, strategy: n } = i;
    const r = [
        ...(e === "clippingAncestors"
            ? nt(t)
                ? []
                : ve(t, this._c)
            : [].concat(e)),
        s,
    ];
    const l = r[0];
    const a = r.reduce(
        (c, d) => {
            const h = Ht(t, d, n);
            return (
                (c.top = $(h.top, c.top)),
                (c.right = tt(h.right, c.right)),
                (c.bottom = tt(h.bottom, c.bottom)),
                (c.left = $(h.left, c.left)),
                c
            );
        },
        Ht(t, l, n),
    );
    return {
        width: a.right - a.left,
        height: a.bottom - a.top,
        x: a.left,
        y: a.top,
    };
}
function Oe(i) {
    const { width: t, height: e } = $t(i);
    return { width: t, height: e };
}
function Ae(i, t, e) {
    const s = k(t);
    const n = M(t);
    const o = e === "fixed";
    const r = rt(i, !0, o, t);
    let l = { scrollLeft: 0, scrollTop: 0 };
    const a = T(0);
    function c() {
        a.x = pt(n);
    }
    if (s || (!s && !o)) {
        if (((_(t) !== "body" || j(n)) && (l = ot(t)), s)) {
            const f = rt(t, !0, o, t);
            ((a.x = f.x + t.clientLeft), (a.y = f.y + t.clientTop));
        } else n && c();
    }
    o && !s && n && c();
    const d = n && !s && !o ? Ut(n, l) : T(0);
    const h = r.left + l.scrollLeft - a.x - d.x;
    const p = r.top + l.scrollTop - a.y - d.y;
    return { x: h, y: p, width: r.width, height: r.height };
}
function xt(i) {
    return D(i).position === "static";
}
function Vt(i, t) {
    if (!k(i) || D(i).position === "fixed") return null;
    if (t) return t(i);
    let e = i.offsetParent;
    return (M(i) === e && (e = e.ownerDocument.body), e);
}
function Kt(i, t) {
    const e = A(i);
    if (nt(i)) return e;
    if (!k(i)) {
        let n = N(i);
        for (; n && !K(n); ) {
            if (C(n) && !xt(n)) return n;
            n = N(n);
        }
        return e;
    }
    let s = Vt(i, t);
    for (; s && Pt(s) && xt(s); ) s = Vt(s, t);
    return s && K(s) && xt(s) && !dt(s) ? e : s || Bt(i) || e;
}
const Se = async function (i) {
    const t = this.getOffsetParent || Kt;
    const e = this.getDimensions;
    const s = await e(i.floating);
    return {
        reference: Ae(i.reference, await t(i.floating), i.strategy),
        floating: { x: 0, y: 0, width: s.width, height: s.height },
    };
};
function Ce(i) {
    return D(i).direction === "rtl";
}
const De = {
    convertOffsetParentRelativeRectToViewportRelativeRect: me,
    getDocumentElement: M,
    getClippingRect: Le,
    getOffsetParent: Kt,
    getElementRects: Se,
    getClientRects: ge,
    getDimensions: Oe,
    getScale: X,
    isElement: C,
    isRTL: Ce,
};
const qt = It;
const Jt = Tt;
const jt = Rt;
const Xt = (i, t, e) => {
    const s = new Map();
    const n = { platform: De, ...e };
    const o = { ...n.platform, _c: s };
    return Et(i, t, { ...n, platform: o });
};
function F(i) {
    return i == null || i === "" || (typeof i === "string" && i.trim() === "");
}
function w(i) {
    return !F(i);
}
const ut = class {
    constructor({
        element: t,
        options: e,
        placeholder: s,
        state: n,
        canOptionLabelsWrap: o = !0,
        canSelectPlaceholder: r = !0,
        initialOptionLabel: l = null,
        initialOptionLabels: a = null,
        initialState: c = null,
        isHtmlAllowed: d = !1,
        isAutofocused: h = !1,
        isDisabled: p = !1,
        isMultiple: f = !1,
        isSearchable: u = !1,
        getOptionLabelUsing: m = null,
        getOptionLabelsUsing: g = null,
        getOptionsUsing: y = null,
        getSearchResultsUsing: b = null,
        hasDynamicOptions: v = !1,
        hasDynamicSearchResults: O = !0,
        searchPrompt: x = "Search...",
        searchDebounce: q = 1e3,
        loadingMessage: Y = "Loading...",
        searchingMessage: W = "Searching...",
        noSearchResultsMessage: L = "No results found",
        maxItems: S = null,
        maxItemsMessage: H = "Maximum number of items selected",
        optionsLimit: G = null,
        position: Q = null,
        searchableOptionFields: E = ["label"],
        livewireId: J = null,
        statePath: R = null,
        onStateChange: P = () => {},
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
            (this.isHtmlAllowed = d),
            (this.isAutofocused = h),
            (this.isDisabled = p),
            (this.isMultiple = f),
            (this.isSearchable = u),
            (this.getOptionLabelUsing = m),
            (this.getOptionLabelsUsing = g),
            (this.getOptionsUsing = y),
            (this.getSearchResultsUsing = b),
            (this.hasDynamicOptions = v),
            (this.hasDynamicSearchResults = O),
            (this.searchPrompt = x),
            (this.searchDebounce = q),
            (this.loadingMessage = Y),
            (this.searchingMessage = W),
            (this.noSearchResultsMessage = L),
            (this.maxItems = S),
            (this.maxItemsMessage = H),
            (this.optionsLimit = G),
            (this.position = Q),
            (this.searchableOptionFields = Array.isArray(E) ? E : ["label"]),
            (this.livewireId = J),
            (this.statePath = R),
            (this.onStateChange = P),
            (this.labelRepository = {}),
            (this.isOpen = !1),
            (this.selectedIndex = -1),
            (this.searchQuery = ""),
            (this.searchTimeout = null),
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
                                e.forEach((s) => {
                                    s.classList.remove("fi-selected");
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
        let s = 0;
        let n = !1;
        (this.options.forEach((l) => {
            l.options && Array.isArray(l.options)
                ? ((s += l.options.length), (n = !0))
                : s++;
        }),
            n
                ? (this.optionsList.className = "fi-select-input-options-ctn")
                : s > 0 && (this.optionsList.className = "fi-dropdown-list"));
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
        const s = document.createElement("li");
        s.className = "fi-select-input-option-group";
        const n = document.createElement("div");
        ((n.className = "fi-dropdown-header"), (n.textContent = t));
        const o = document.createElement("ul");
        ((o.className = "fi-dropdown-list"),
            e.forEach((r) => {
                const l = this.createOptionElement(r.value, r);
                o.appendChild(l);
            }),
            s.appendChild(n),
            s.appendChild(o),
            this.optionsList.appendChild(s));
    }

    createOptionElement(t, e) {
        let s = t;
        let n = e;
        let o = !1;
        typeof e === "object" &&
            e !== null &&
            "label" in e &&
            "value" in e &&
            ((s = e.value), (n = e.label), (o = e.isDisabled || !1));
        const r = document.createElement("li");
        ((r.className = "fi-dropdown-list-item fi-select-input-option"),
            o && r.classList.add("fi-disabled"));
        const l = `fi-select-input-option-${Math.random().toString(36).substring(2, 11)}`;
        if (
            ((r.id = l),
            r.setAttribute("role", "option"),
            r.setAttribute("data-value", s),
            r.setAttribute("tabindex", "0"),
            o && r.setAttribute("aria-disabled", "true"),
            this.isHtmlAllowed && typeof n === "string")
        ) {
            const d = document.createElement("div");
            d.innerHTML = n;
            const h = d.textContent || d.innerText || n;
            r.setAttribute("aria-label", h);
        }
        const a = this.isMultiple
            ? Array.isArray(this.state) && this.state.includes(s)
            : this.state === s;
        (r.setAttribute("aria-selected", a ? "true" : "false"),
            a && r.classList.add("fi-selected"));
        const c = document.createElement("span");
        return (
            this.isHtmlAllowed ? (c.innerHTML = n) : (c.textContent = n),
            r.appendChild(c),
            o ||
                r.addEventListener("click", (d) => {
                    (d.preventDefault(),
                        d.stopPropagation(),
                        this.selectOption(s),
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
        if (((this.selectedDisplay.innerHTML = ""), this.isMultiple)) {
            if (!Array.isArray(this.state) || this.state.length === 0) {
                const s = document.createElement("span");
                ((s.textContent = this.placeholder),
                    s.classList.add("fi-select-input-placeholder"),
                    this.selectedDisplay.appendChild(s));
                return;
            }
            const e = await this.getLabelsForMultipleSelection();
            (this.addBadgesForSelectedOptions(e),
                this.isOpen && this.positionDropdown());
            return;
        }
        if (this.state === null || this.state === "") {
            const e = document.createElement("span");
            ((e.textContent = this.placeholder),
                e.classList.add("fi-select-input-placeholder"),
                this.selectedDisplay.appendChild(e));
            return;
        }
        const t = await this.getLabelForSingleSelection();
        this.addSingleSelectionDisplay(t);
    }

    async getLabelsForMultipleSelection() {
        const t = this.getSelectedOptionLabels();
        const e = [];
        if (Array.isArray(this.state)) {
            for (const n of this.state) {
                if (!w(this.labelRepository[n])) {
                    if (w(t[n])) {
                        this.labelRepository[n] = t[n];
                        continue;
                    }
                    e.push(n.toString());
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
                        (this.labelRepository[n.value] = n.label);
                }
            }
        } else if (e.length > 0 && this.getOptionLabelsUsing) {
            try {
                const n = await this.getOptionLabelsUsing();
                for (const o of n) {
                    w(o) &&
                        o.value !== void 0 &&
                        o.label !== void 0 &&
                        (this.labelRepository[o.value] = o.label);
                }
            } catch (n) {
                console.error("Error fetching option labels:", n);
            }
        }
        const s = [];
        if (Array.isArray(this.state)) {
            for (const n of this.state) {
                w(this.labelRepository[n])
                    ? s.push(this.labelRepository[n])
                    : w(t[n])
                      ? s.push(t[n])
                      : s.push(n);
            }
        }
        return s;
    }

    createBadgeElement(t, e) {
        const s = document.createElement("span");
        ((s.className =
            "fi-badge fi-size-md fi-color fi-color-primary fi-text-color-600 dark:fi-text-color-200"),
            w(t) && s.setAttribute("data-value", t));
        const n = document.createElement("span");
        n.className = "fi-badge-label-ctn";
        const o = document.createElement("span");
        ((o.className = "fi-badge-label"),
            this.canOptionLabelsWrap && o.classList.add("fi-wrapped"),
            this.isHtmlAllowed ? (o.innerHTML = e) : (o.textContent = e),
            n.appendChild(o),
            s.appendChild(n));
        const r = this.createRemoveButton(t, e);
        return (s.appendChild(r), s);
    }

    createRemoveButton(t, e) {
        const s = document.createElement("button");
        return (
            (s.type = "button"),
            (s.className = "fi-badge-delete-btn"),
            (s.innerHTML =
                '<svg class="fi-icon fi-size-xs" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" data-slot="icon"><path d="M5.28 4.22a.75.75 0 0 0-1.06 1.06L6.94 8l-2.72 2.72a.75.75 0 1 0 1.06 1.06L8 9.06l2.72 2.72a.75.75 0 1 0 1.06-1.06L9.06 8l2.72-2.72a.75.75 0 0 0-1.06-1.06L8 6.94 5.28 4.22Z"></path></svg>'),
            s.setAttribute(
                "aria-label",
                "Remove " +
                    (this.isHtmlAllowed ? e.replace(/<[^>]*>/g, "") : e),
            ),
            s.addEventListener("click", (n) => {
                (n.stopPropagation(), w(t) && this.selectOption(t));
            }),
            s.addEventListener("keydown", (n) => {
                (n.key === " " || n.key === "Enter") &&
                    (n.preventDefault(),
                    n.stopPropagation(),
                    w(t) && this.selectOption(t));
            }),
            s
        );
    }

    addBadgesForSelectedOptions(t) {
        const e = document.createElement("div");
        ((e.className = "fi-select-input-value-badges-ctn"),
            t.forEach((s, n) => {
                const o = Array.isArray(this.state) ? this.state[n] : null;
                const r = this.createBadgeElement(o, s);
                e.appendChild(r);
            }),
            this.selectedDisplay.appendChild(e));
    }

    async getLabelForSingleSelection() {
        let t = this.labelRepository[this.state];
        if (
            (F(t) && (t = this.getSelectedOptionLabel(this.state)),
            F(t) &&
                w(this.initialOptionLabel) &&
                this.state === this.initialState)
        ) {
            ((t = this.initialOptionLabel),
                w(this.state) && (this.labelRepository[this.state] = t));
        } else if (F(t) && this.getOptionLabelUsing) {
            try {
                ((t = await this.getOptionLabelUsing()),
                    w(t) &&
                        w(this.state) &&
                        (this.labelRepository[this.state] = t));
            } catch (e) {
                (console.error("Error fetching option label:", e),
                    (t = this.state));
            }
        } else F(t) && (t = this.state);
        return t;
    }

    addSingleSelectionDisplay(t) {
        const e = document.createElement("span");
        if (
            ((e.className = "fi-select-input-value-label"),
            this.isHtmlAllowed ? (e.innerHTML = t) : (e.textContent = t),
            this.selectedDisplay.appendChild(e),
            !this.canSelectPlaceholder)
        ) {
            return;
        }
        const s = document.createElement("button");
        ((s.type = "button"),
            (s.className = "fi-select-input-value-remove-btn"),
            (s.innerHTML =
                '<svg class="fi-icon fi-size-sm" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>'),
            s.setAttribute("aria-label", "Clear selection"),
            s.addEventListener("click", (n) => {
                (n.stopPropagation(), this.selectOption(""));
            }),
            s.addEventListener("keydown", (n) => {
                (n.key === " " || n.key === "Enter") &&
                    (n.preventDefault(),
                    n.stopPropagation(),
                    this.selectOption(""));
            }),
            this.selectedDisplay.appendChild(s));
    }

    getSelectedOptionLabel(t) {
        if (w(this.labelRepository[t])) return this.labelRepository[t];
        let e = "";
        for (const s of this.options) {
            if (s.options && Array.isArray(s.options)) {
                for (const n of s.options) {
                    if (n.value === t) {
                        ((e = n.label), (this.labelRepository[t] = e));
                        break;
                    }
                }
            } else if (s.value === t) {
                ((e = s.label), (this.labelRepository[t] = e));
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
                        w(this.state)
                    ) {
                        try {
                            delete this.labelRepository[this.state];
                            const e = await this.getOptionLabelUsing();
                            w(e) && (this.labelRepository[this.state] = e);
                            const s = this.selectedDisplay.querySelector(
                                ".fi-select-input-value-label",
                            );
                            (w(s) &&
                                (this.isHtmlAllowed
                                    ? (s.innerHTML = e)
                                    : (s.textContent = e)),
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
        const s = this.getVisibleOptions();
        for (const n of s) {
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
            this.selectButton.closest(".fi-absolute-positioning-context") !==
            null;
        if (
            ((this.dropdown.style.position = t ? "absolute" : "fixed"),
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
                ((this.options = e),
                    (this.originalOptions = JSON.parse(JSON.stringify(e))),
                    this.populateLabelRepositoryFromOptions(e),
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
                    for (let s = 0; s < e.length; s++) {
                        if (
                            this.state.includes(e[s].getAttribute("data-value"))
                        ) {
                            this.selectedIndex = s;
                            break;
                        }
                    }
                }
            } else {
                for (let s = 0; s < e.length; s++) {
                    if (e[s].getAttribute("data-value") === this.state) {
                        this.selectedIndex = s;
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
        const e = [qt(4), Jt({ padding: 5 })];
        this.position !== "top" && this.position !== "bottom" && e.push(jt());
        const s =
            this.selectButton.closest(".fi-absolute-positioning-context") !==
            null;
        Xt(this.selectButton, this.dropdown, {
            placement: t,
            middleware: e,
            strategy: s ? "absolute" : "fixed",
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
        const s = t.getBoundingClientRect();
        s.bottom > e.bottom
            ? (this.dropdown.scrollTop += s.bottom - e.bottom)
            : s.top < e.top && (this.dropdown.scrollTop -= e.top - s.top);
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
            let s = !1;
            for (const n of this.options) {
                if (n.options && Array.isArray(n.options)) {
                    for (const o of n.options) {
                        if (o.value === e) {
                            ((t[e] = o.label), (s = !0));
                            break;
                        }
                    }
                    if (s) break;
                } else if (n.value === e) {
                    ((t[e] = n.label), (s = !0));
                    break;
                }
            }
        }
        return t;
    }

    handleSearch(t) {
        const e = t.target.value.trim().toLowerCase();
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
            try {
                this.showLoadingState(!0);
                const s = await this.getSearchResultsUsing(e);
                ((this.options = s),
                    this.populateLabelRepositoryFromOptions(s),
                    this.hideLoadingState(),
                    this.renderOptions(),
                    this.isOpen && this.positionDropdown(),
                    this.options.length === 0 && this.showNoResultsMessage());
            } catch (s) {
                (console.error("Error fetching search results:", s),
                    this.hideLoadingState(),
                    (this.options = JSON.parse(
                        JSON.stringify(this.originalOptions),
                    )),
                    this.renderOptions());
            }
        }, this.searchDebounce);
    }

    showLoadingState(t = !1) {
        (this.optionsList.parentNode === this.dropdown &&
            (this.optionsList.innerHTML = ""),
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
            this.optionsList.children.length > 0 &&
            (this.optionsList.innerHTML = ""),
            this.hideLoadingState());
        const t = document.createElement("div");
        ((t.className = "fi-select-input-message"),
            (t.textContent = this.noSearchResultsMessage),
            this.dropdown.appendChild(t));
    }

    filterOptions(t) {
        const e = this.searchableOptionFields.includes("label");
        const s = this.searchableOptionFields.includes("value");
        const n = [];
        for (const o of this.originalOptions) {
            if (o.options && Array.isArray(o.options)) {
                const r = o.options.filter(
                    (l) =>
                        (e && l.label.toLowerCase().includes(t)) ||
                        (s && String(l.value).toLowerCase().includes(t)),
                );
                r.length > 0 && n.push({ label: o.label, options: r });
            } else {
                ((e && o.label.toLowerCase().includes(t)) ||
                    (s && String(o.value).toLowerCase().includes(t))) &&
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
            if (w(n)) {
                const o = n.parentElement;
                w(o) && o.children.length === 1
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
        const s = this.selectedDisplay.querySelector(
            ".fi-select-input-value-badges-ctn",
        );
        (F(s) ? this.updateSelectedDisplay() : this.addSingleBadge(t, s),
            this.renderOptions(),
            this.isOpen && this.positionDropdown(),
            this.maintainFocusInMultipleMode(),
            this.onStateChange(this.state));
    }

    async addSingleBadge(t, e) {
        let s = this.labelRepository[t];
        if (
            (F(s) &&
                ((s = this.getSelectedOptionLabel(t)),
                w(s) && (this.labelRepository[t] = s)),
            F(s) && this.getOptionLabelsUsing)
        ) {
            try {
                const o = await this.getOptionLabelsUsing();
                for (const r of o) {
                    if (w(r) && r.value === t && r.label !== void 0) {
                        ((s = r.label), (this.labelRepository[t] = s));
                        break;
                    }
                }
            } catch (o) {
                console.error("Error fetching option label:", o);
            }
        }
        F(s) && (s = t);
        const n = this.createBadgeElement(t, s);
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
    isNative: d,
    isSearchable: h,
    loadingMessage: p,
    name: f,
    noSearchResultsMessage: u,
    options: m,
    optionsLimit: g,
    placeholder: y,
    position: b,
    recordKey: v,
    searchableOptionFields: O,
    searchDebounce: x,
    searchingMessage: q,
    searchPrompt: Y,
    state: W,
}) {
    return {
        error: void 0,
        isLoading: !1,
        select: null,
        state: W,
        init() {
            (d ||
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
                    isSearchable: h,
                    getOptionLabelUsing: e,
                    getOptionsUsing: s,
                    getSearchResultsUsing: n,
                    hasDynamicOptions: o,
                    hasDynamicSearchResults: r,
                    searchPrompt: Y,
                    searchDebounce: x,
                    loadingMessage: p,
                    searchingMessage: q,
                    noSearchResultsMessage: u,
                    optionsLimit: g,
                    position: b,
                    searchableOptionFields: O,
                    onStateChange: (L) => {
                        this.state = L;
                    },
                })),
                Livewire.hook(
                    "commit",
                    ({
                        component: L,
                        commit: S,
                        succeed: H,
                        fail: G,
                        respond: Q,
                    }) => {
                        H(({ snapshot: E, effect: J }) => {
                            this.$nextTick(() => {
                                if (
                                    this.isLoading ||
                                    L.id !==
                                        this.$root.closest("[wire\\:id]")
                                            ?.attributes["wire:id"].value
                                ) {
                                    return;
                                }
                                const R = this.getServerState();
                                R === void 0 ||
                                    this.getNormalizedState() === R ||
                                    (this.state = R);
                            });
                        });
                    },
                ),
                this.$watch("state", async (L) => {
                    !d &&
                        this.select &&
                        this.select.state !== L &&
                        ((this.select.state = L),
                        this.select.updateSelectedDisplay(),
                        this.select.renderOptions());
                    const S = this.getServerState();
                    if (S === void 0 || this.getNormalizedState() === S) return;
                    this.isLoading = !0;
                    const H = await this.$wire.updateTableColumnState(
                        f,
                        v,
                        this.state,
                    );
                    ((this.error = H?.error ?? void 0),
                        !this.error &&
                            this.$refs.serverState &&
                            (this.$refs.serverState.value =
                                this.getNormalizedState()),
                        (this.isLoading = !1));
                }));
        },
        getServerState() {
            if (this.$refs.serverState) {
                return [null, void 0].includes(this.$refs.serverState.value)
                    ? ""
                    : this.$refs.serverState.value.replaceAll('\\"', '"');
            }
        },
        getNormalizedState() {
            const L = Alpine.raw(this.state);
            return [null, void 0].includes(L) ? "" : L;
        },
        destroy() {
            this.select && (this.select.destroy(), (this.select = null));
        },
    };
}
export { Ee as default };
