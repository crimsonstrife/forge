const Zo = Object.defineProperty;
const Jo = (i, t, e) =>
    t in i
        ? Zo(i, t, { enumerable: !0, configurable: !0, writable: !0, value: e })
        : (i[t] = e);
const M = (i, t, e) => Jo(i, typeof t !== "symbol" ? t + "" : t, e);
function ye(i) {
    return (i + 0.5) | 0;
}
const wt = (i, t, e) => Math.max(Math.min(i, e), t);
function xe(i) {
    return wt(ye(i * 2.55), 0, 255);
}
function St(i) {
    return wt(ye(i * 255), 0, 255);
}
function mt(i) {
    return wt(ye(i / 2.55) / 100, 0, 1);
}
function zs(i) {
    return wt(ye(i * 100), 0, 100);
}
const nt = {
    0: 0,
    1: 1,
    2: 2,
    3: 3,
    4: 4,
    5: 5,
    6: 6,
    7: 7,
    8: 8,
    9: 9,
    A: 10,
    B: 11,
    C: 12,
    D: 13,
    E: 14,
    F: 15,
    a: 10,
    b: 11,
    c: 12,
    d: 13,
    e: 14,
    f: 15,
};
const Ai = [..."0123456789ABCDEF"];
const Qo = (i) => Ai[i & 15];
const ta = (i) => Ai[(i & 240) >> 4] + Ai[i & 15];
const Ye = (i) => (i & 240) >> 4 === (i & 15);
const ea = (i) => Ye(i.r) && Ye(i.g) && Ye(i.b) && Ye(i.a);
function ia(i) {
    const t = i.length;
    let e;
    return (
        i[0] === "#" &&
            (t === 4 || t === 5
                ? (e = {
                      r: 255 & (nt[i[1]] * 17),
                      g: 255 & (nt[i[2]] * 17),
                      b: 255 & (nt[i[3]] * 17),
                      a: t === 5 ? nt[i[4]] * 17 : 255,
                  })
                : (t === 7 || t === 9) &&
                  (e = {
                      r: (nt[i[1]] << 4) | nt[i[2]],
                      g: (nt[i[3]] << 4) | nt[i[4]],
                      b: (nt[i[5]] << 4) | nt[i[6]],
                      a: t === 9 ? (nt[i[7]] << 4) | nt[i[8]] : 255,
                  })),
        e
    );
}
const sa = (i, t) => (i < 255 ? t(i) : "");
function na(i) {
    const t = ea(i) ? Qo : ta;
    return i ? "#" + t(i.r) + t(i.g) + t(i.b) + sa(i.a, t) : void 0;
}
const oa =
    /^(hsla?|hwb|hsv)\(\s*([-+.e\d]+)(?:deg)?[\s,]+([-+.e\d]+)%[\s,]+([-+.e\d]+)%(?:[\s,]+([-+.e\d]+)(%)?)?\s*\)$/;
function Ns(i, t, e) {
    const s = t * Math.min(e, 1 - e);
    const n = (o, a = (o + i / 30) % 12) =>
        e - s * Math.max(Math.min(a - 3, 9 - a, 1), -1);
    return [n(0), n(8), n(4)];
}
function aa(i, t, e) {
    const s = (n, o = (n + i / 60) % 6) =>
        e - e * t * Math.max(Math.min(o, 4 - o, 1), 0);
    return [s(5), s(3), s(1)];
}
function ra(i, t, e) {
    const s = Ns(i, 1, 0.5);
    let n;
    for (
        t + e > 1 && ((n = 1 / (t + e)), (t *= n), (e *= n)), n = 0;
        n < 3;
        n++
    ) {
        ((s[n] *= 1 - t - e), (s[n] += t));
    }
    return s;
}
function la(i, t, e, s, n) {
    return i === n
        ? (t - e) / s + (t < e ? 6 : 0)
        : t === n
          ? (e - i) / s + 2
          : (i - t) / s + 4;
}
function Ti(i) {
    const e = i.r / 255;
    const s = i.g / 255;
    const n = i.b / 255;
    const o = Math.max(e, s, n);
    const a = Math.min(e, s, n);
    const r = (o + a) / 2;
    let l;
    let c;
    let h;
    return (
        o !== a &&
            ((h = o - a),
            (c = r > 0.5 ? h / (2 - o - a) : h / (o + a)),
            (l = la(e, s, n, h, o)),
            (l = l * 60 + 0.5)),
        [l | 0, c || 0, r]
    );
}
function Li(i, t, e, s) {
    return (Array.isArray(t) ? i(t[0], t[1], t[2]) : i(t, e, s)).map(St);
}
function Ri(i, t, e) {
    return Li(Ns, i, t, e);
}
function ca(i, t, e) {
    return Li(ra, i, t, e);
}
function ha(i, t, e) {
    return Li(aa, i, t, e);
}
function Hs(i) {
    return ((i % 360) + 360) % 360;
}
function da(i) {
    const t = oa.exec(i);
    let e = 255;
    let s;
    if (!t) return;
    t[5] !== s && (e = t[6] ? xe(+t[5]) : St(+t[5]));
    const n = Hs(+t[2]);
    const o = +t[3] / 100;
    const a = +t[4] / 100;
    return (
        t[1] === "hwb"
            ? (s = ca(n, o, a))
            : t[1] === "hsv"
              ? (s = ha(n, o, a))
              : (s = Ri(n, o, a)),
        { r: s[0], g: s[1], b: s[2], a: e }
    );
}
function ua(i, t) {
    let e = Ti(i);
    ((e[0] = Hs(e[0] + t)),
        (e = Ri(e)),
        (i.r = e[0]),
        (i.g = e[1]),
        (i.b = e[2]));
}
function fa(i) {
    if (!i) return;
    const t = Ti(i);
    const e = t[0];
    const s = zs(t[1]);
    const n = zs(t[2]);
    return i.a < 255
        ? `hsla(${e}, ${s}%, ${n}%, ${mt(i.a)})`
        : `hsl(${e}, ${s}%, ${n}%)`;
}
const Bs = {
    x: "dark",
    Z: "light",
    Y: "re",
    X: "blu",
    W: "gr",
    V: "medium",
    U: "slate",
    A: "ee",
    T: "ol",
    S: "or",
    B: "ra",
    C: "lateg",
    D: "ights",
    R: "in",
    Q: "turquois",
    E: "hi",
    P: "ro",
    O: "al",
    N: "le",
    M: "de",
    L: "yello",
    F: "en",
    K: "ch",
    G: "arks",
    H: "ea",
    I: "ightg",
    J: "wh",
};
const Vs = {
    OiceXe: "f0f8ff",
    antiquewEte: "faebd7",
    aqua: "ffff",
    aquamarRe: "7fffd4",
    azuY: "f0ffff",
    beige: "f5f5dc",
    bisque: "ffe4c4",
    black: "0",
    blanKedOmond: "ffebcd",
    Xe: "ff",
    XeviTet: "8a2be2",
    bPwn: "a52a2a",
    burlywood: "deb887",
    caMtXe: "5f9ea0",
    KartYuse: "7fff00",
    KocTate: "d2691e",
    cSO: "ff7f50",
    cSnflowerXe: "6495ed",
    cSnsilk: "fff8dc",
    crimson: "dc143c",
    cyan: "ffff",
    xXe: "8b",
    xcyan: "8b8b",
    xgTMnPd: "b8860b",
    xWay: "a9a9a9",
    xgYF: "6400",
    xgYy: "a9a9a9",
    xkhaki: "bdb76b",
    xmagFta: "8b008b",
    xTivegYF: "556b2f",
    xSange: "ff8c00",
    xScEd: "9932cc",
    xYd: "8b0000",
    xsOmon: "e9967a",
    xsHgYF: "8fbc8f",
    xUXe: "483d8b",
    xUWay: "2f4f4f",
    xUgYy: "2f4f4f",
    xQe: "ced1",
    xviTet: "9400d3",
    dAppRk: "ff1493",
    dApskyXe: "bfff",
    dimWay: "696969",
    dimgYy: "696969",
    dodgerXe: "1e90ff",
    fiYbrick: "b22222",
    flSOwEte: "fffaf0",
    foYstWAn: "228b22",
    fuKsia: "ff00ff",
    gaRsbSo: "dcdcdc",
    ghostwEte: "f8f8ff",
    gTd: "ffd700",
    gTMnPd: "daa520",
    Way: "808080",
    gYF: "8000",
    gYFLw: "adff2f",
    gYy: "808080",
    honeyMw: "f0fff0",
    hotpRk: "ff69b4",
    RdianYd: "cd5c5c",
    Rdigo: "4b0082",
    ivSy: "fffff0",
    khaki: "f0e68c",
    lavFMr: "e6e6fa",
    lavFMrXsh: "fff0f5",
    lawngYF: "7cfc00",
    NmoncEffon: "fffacd",
    ZXe: "add8e6",
    ZcSO: "f08080",
    Zcyan: "e0ffff",
    ZgTMnPdLw: "fafad2",
    ZWay: "d3d3d3",
    ZgYF: "90ee90",
    ZgYy: "d3d3d3",
    ZpRk: "ffb6c1",
    ZsOmon: "ffa07a",
    ZsHgYF: "20b2aa",
    ZskyXe: "87cefa",
    ZUWay: "778899",
    ZUgYy: "778899",
    ZstAlXe: "b0c4de",
    ZLw: "ffffe0",
    lime: "ff00",
    limegYF: "32cd32",
    lRF: "faf0e6",
    magFta: "ff00ff",
    maPon: "800000",
    VaquamarRe: "66cdaa",
    VXe: "cd",
    VScEd: "ba55d3",
    VpurpN: "9370db",
    VsHgYF: "3cb371",
    VUXe: "7b68ee",
    VsprRggYF: "fa9a",
    VQe: "48d1cc",
    VviTetYd: "c71585",
    midnightXe: "191970",
    mRtcYam: "f5fffa",
    mistyPse: "ffe4e1",
    moccasR: "ffe4b5",
    navajowEte: "ffdead",
    navy: "80",
    Tdlace: "fdf5e6",
    Tive: "808000",
    TivedBb: "6b8e23",
    Sange: "ffa500",
    SangeYd: "ff4500",
    ScEd: "da70d6",
    pOegTMnPd: "eee8aa",
    pOegYF: "98fb98",
    pOeQe: "afeeee",
    pOeviTetYd: "db7093",
    papayawEp: "ffefd5",
    pHKpuff: "ffdab9",
    peru: "cd853f",
    pRk: "ffc0cb",
    plum: "dda0dd",
    powMrXe: "b0e0e6",
    purpN: "800080",
    YbeccapurpN: "663399",
    Yd: "ff0000",
    Psybrown: "bc8f8f",
    PyOXe: "4169e1",
    saddNbPwn: "8b4513",
    sOmon: "fa8072",
    sandybPwn: "f4a460",
    sHgYF: "2e8b57",
    sHshell: "fff5ee",
    siFna: "a0522d",
    silver: "c0c0c0",
    skyXe: "87ceeb",
    UXe: "6a5acd",
    UWay: "708090",
    UgYy: "708090",
    snow: "fffafa",
    sprRggYF: "ff7f",
    stAlXe: "4682b4",
    tan: "d2b48c",
    teO: "8080",
    tEstN: "d8bfd8",
    tomato: "ff6347",
    Qe: "40e0d0",
    viTet: "ee82ee",
    JHt: "f5deb3",
    wEte: "ffffff",
    wEtesmoke: "f5f5f5",
    Lw: "ffff00",
    LwgYF: "9acd32",
};
function ga() {
    const i = {};
    const t = Object.keys(Vs);
    const e = Object.keys(Bs);
    let s;
    let n;
    let o;
    let a;
    let r;
    for (s = 0; s < t.length; s++) {
        for (a = r = t[s], n = 0; n < e.length; n++) {
            ((o = e[n]), (r = r.replace(o, Bs[o])));
        }
        ((o = parseInt(Vs[a], 16)),
            (i[r] = [(o >> 16) & 255, (o >> 8) & 255, o & 255]));
    }
    return i;
}
let Ue;
function pa(i) {
    Ue || ((Ue = ga()), (Ue.transparent = [0, 0, 0, 0]));
    const t = Ue[i.toLowerCase()];
    return t && { r: t[0], g: t[1], b: t[2], a: t.length === 4 ? t[3] : 255 };
}
const ma =
    /^rgba?\(\s*([-+.\d]+)(%)?[\s,]+([-+.e\d]+)(%)?[\s,]+([-+.e\d]+)(%)?(?:[\s,/]+([-+.e\d]+)(%)?)?\s*\)$/;
function ba(i) {
    const t = ma.exec(i);
    let e = 255;
    let s;
    let n;
    let o;
    if (t) {
        if (t[7] !== s) {
            const a = +t[7];
            e = t[8] ? xe(a) : wt(a * 255, 0, 255);
        }
        return (
            (s = +t[1]),
            (n = +t[3]),
            (o = +t[5]),
            (s = 255 & (t[2] ? xe(s) : wt(s, 0, 255))),
            (n = 255 & (t[4] ? xe(n) : wt(n, 0, 255))),
            (o = 255 & (t[6] ? xe(o) : wt(o, 0, 255))),
            { r: s, g: n, b: o, a: e }
        );
    }
}
function xa(i) {
    return (
        i &&
        (i.a < 255
            ? `rgba(${i.r}, ${i.g}, ${i.b}, ${mt(i.a)})`
            : `rgb(${i.r}, ${i.g}, ${i.b})`)
    );
}
const Ci = (i) =>
    i <= 0.0031308 ? i * 12.92 : Math.pow(i, 1 / 2.4) * 1.055 - 0.055;
const qt = (i) =>
    i <= 0.04045 ? i / 12.92 : Math.pow((i + 0.055) / 1.055, 2.4);
function _a(i, t, e) {
    const s = qt(mt(i.r));
    const n = qt(mt(i.g));
    const o = qt(mt(i.b));
    return {
        r: St(Ci(s + e * (qt(mt(t.r)) - s))),
        g: St(Ci(n + e * (qt(mt(t.g)) - n))),
        b: St(Ci(o + e * (qt(mt(t.b)) - o))),
        a: i.a + e * (t.a - i.a),
    };
}
function Xe(i, t, e) {
    if (i) {
        let s = Ti(i);
        ((s[t] = Math.max(0, Math.min(s[t] + s[t] * e, t === 0 ? 360 : 1))),
            (s = Ri(s)),
            (i.r = s[0]),
            (i.g = s[1]),
            (i.b = s[2]));
    }
}
function js(i, t) {
    return i && Object.assign(t || {}, i);
}
function Ws(i) {
    let t = { r: 0, g: 0, b: 0, a: 255 };
    return (
        Array.isArray(i)
            ? i.length >= 3 &&
              ((t = { r: i[0], g: i[1], b: i[2], a: 255 }),
              i.length > 3 && (t.a = St(i[3])))
            : ((t = js(i, { r: 0, g: 0, b: 0, a: 1 })), (t.a = St(t.a))),
        t
    );
}
function ya(i) {
    return i.charAt(0) === "r" ? ba(i) : da(i);
}
const _e = class i {
    constructor(t) {
        if (t instanceof i) return t;
        const e = typeof t;
        let s;
        (e === "object"
            ? (s = Ws(t))
            : e === "string" && (s = ia(t) || pa(t) || ya(t)),
            (this._rgb = s),
            (this._valid = !!s));
    }

    get valid() {
        return this._valid;
    }

    get rgb() {
        const t = js(this._rgb);
        return (t && (t.a = mt(t.a)), t);
    }

    set rgb(t) {
        this._rgb = Ws(t);
    }

    rgbString() {
        return this._valid ? xa(this._rgb) : void 0;
    }

    hexString() {
        return this._valid ? na(this._rgb) : void 0;
    }

    hslString() {
        return this._valid ? fa(this._rgb) : void 0;
    }

    mix(t, e) {
        if (t) {
            const s = this.rgb;
            const n = t.rgb;
            let o;
            const a = e === o ? 0.5 : e;
            const r = 2 * a - 1;
            const l = s.a - n.a;
            const c = ((r * l === -1 ? r : (r + l) / (1 + r * l)) + 1) / 2;
            ((o = 1 - c),
                (s.r = 255 & (c * s.r + o * n.r + 0.5)),
                (s.g = 255 & (c * s.g + o * n.g + 0.5)),
                (s.b = 255 & (c * s.b + o * n.b + 0.5)),
                (s.a = a * s.a + (1 - a) * n.a),
                (this.rgb = s));
        }
        return this;
    }

    interpolate(t, e) {
        return (t && (this._rgb = _a(this._rgb, t._rgb, e)), this);
    }

    clone() {
        return new i(this.rgb);
    }

    alpha(t) {
        return ((this._rgb.a = St(t)), this);
    }

    clearer(t) {
        const e = this._rgb;
        return ((e.a *= 1 - t), this);
    }

    greyscale() {
        const t = this._rgb;
        const e = ye(t.r * 0.3 + t.g * 0.59 + t.b * 0.11);
        return ((t.r = t.g = t.b = e), this);
    }

    opaquer(t) {
        const e = this._rgb;
        return ((e.a *= 1 + t), this);
    }

    negate() {
        const t = this._rgb;
        return ((t.r = 255 - t.r), (t.g = 255 - t.g), (t.b = 255 - t.b), this);
    }

    lighten(t) {
        return (Xe(this._rgb, 2, t), this);
    }

    darken(t) {
        return (Xe(this._rgb, 2, -t), this);
    }

    saturate(t) {
        return (Xe(this._rgb, 1, t), this);
    }

    desaturate(t) {
        return (Xe(this._rgb, 1, -t), this);
    }

    rotate(t) {
        return (ua(this._rgb, t), this);
    }
};
function dt() {}
const tn = (() => {
    let i = 0;
    return () => i++;
})();
function A(i) {
    return i == null;
}
function z(i) {
    if (Array.isArray && Array.isArray(i)) return !0;
    const t = Object.prototype.toString.call(i);
    return t.slice(0, 7) === "[object" && t.slice(-6) === "Array]";
}
function T(i) {
    return (
        i !== null && Object.prototype.toString.call(i) === "[object Object]"
    );
}
function N(i) {
    return (typeof i === "number" || i instanceof Number) && isFinite(+i);
}
function J(i, t) {
    return N(i) ? i : t;
}
function D(i, t) {
    return typeof i > "u" ? t : i;
}
const en = (i, t) =>
    typeof i === "string" && i.endsWith("%") ? parseFloat(i) / 100 : +i / t;
const zi = (i, t) =>
    typeof i === "string" && i.endsWith("%") ? (parseFloat(i) / 100) * t : +i;
function F(i, t, e) {
    if (i && typeof i.call === "function") return i.apply(e, t);
}
function E(i, t, e, s) {
    let n, o, a;
    if (z(i)) {
        if (((o = i.length), s)) {
            for (n = o - 1; n >= 0; n--) t.call(e, i[n], n);
        } else for (n = 0; n < o; n++) t.call(e, i[n], n);
    } else if (T(i)) {
        for (a = Object.keys(i), o = a.length, n = 0; n < o; n++) {
            t.call(e, i[a[n]], a[n]);
        }
    }
}
function ke(i, t) {
    let e, s, n, o;
    if (!i || !t || i.length !== t.length) return !1;
    for (e = 0, s = i.length; e < s; ++e) {
        if (
            ((n = i[e]),
            (o = t[e]),
            n.datasetIndex !== o.datasetIndex || n.index !== o.index)
        ) {
            return !1;
        }
    }
    return !0;
}
function Je(i) {
    if (z(i)) return i.map(Je);
    if (T(i)) {
        const t = Object.create(null);
        const e = Object.keys(i);
        const s = e.length;
        let n = 0;
        for (; n < s; ++n) t[e[n]] = Je(i[e[n]]);
        return t;
    }
    return i;
}
function sn(i) {
    return ["__proto__", "prototype", "constructor"].indexOf(i) === -1;
}
function va(i, t, e, s) {
    if (!sn(i)) return;
    const n = t[i];
    const o = e[i];
    T(n) && T(o) ? Zt(n, o, s) : (t[i] = Je(o));
}
function Zt(i, t, e) {
    const s = z(t) ? t : [t];
    const n = s.length;
    if (!T(i)) return i;
    e = e || {};
    const o = e.merger || va;
    let a;
    for (let r = 0; r < n; ++r) {
        if (((a = s[r]), !T(a))) continue;
        const l = Object.keys(a);
        for (let c = 0, h = l.length; c < h; ++c) o(l[c], i, a, e);
    }
    return i;
}
function Qt(i, t) {
    return Zt(i, t, { merger: Ma });
}
function Ma(i, t, e) {
    if (!sn(i)) return;
    const s = t[i];
    const n = e[i];
    T(s) && T(n)
        ? Qt(s, n)
        : Object.prototype.hasOwnProperty.call(t, i) || (t[i] = Je(n));
}
const $s = { "": (i) => i, x: (i) => i.x, y: (i) => i.y };
function ka(i) {
    const t = i.split(".");
    const e = [];
    let s = "";
    for (const n of t) {
        ((s += n),
            s.endsWith("\\")
                ? (s = s.slice(0, -1) + ".")
                : (e.push(s), (s = "")));
    }
    return e;
}
function wa(i) {
    const t = ka(i);
    return (e) => {
        for (const s of t) {
            if (s === "") break;
            e = e && e[s];
        }
        return e;
    };
}
function _t(i, t) {
    return ($s[t] || ($s[t] = wa(t)))(i);
}
function ii(i) {
    return i.charAt(0).toUpperCase() + i.slice(1);
}
const te = (i) => typeof i < "u";
const bt = (i) => typeof i === "function";
const Bi = (i, t) => {
    if (i.size !== t.size) return !1;
    for (const e of i) if (!t.has(e)) return !1;
    return !0;
};
function nn(i) {
    return (
        i.type === "mouseup" || i.type === "click" || i.type === "contextmenu"
    );
}
const R = Math.PI;
const B = 2 * R;
const Sa = B + R;
const Qe = Number.POSITIVE_INFINITY;
const Pa = R / 180;
const H = R / 2;
const It = R / 4;
const Ys = (R * 2) / 3;
const xt = Math.log10;
const lt = Math.sign;
function ee(i, t, e) {
    return Math.abs(i - t) < e;
}
function Vi(i) {
    const t = Math.round(i);
    i = ee(i, t, i / 1e3) ? t : i;
    const e = Math.pow(10, Math.floor(xt(i)));
    const s = i / e;
    return (s <= 1 ? 1 : s <= 2 ? 2 : s <= 5 ? 5 : 10) * e;
}
function on(i) {
    const t = [];
    const e = Math.sqrt(i);
    let s;
    for (s = 1; s < e; s++) i % s === 0 && (t.push(s), t.push(i / s));
    return (e === (e | 0) && t.push(e), t.sort((n, o) => n - o).pop(), t);
}
function Da(i) {
    return (
        typeof i === "symbol" ||
        (typeof i === "object" &&
            i !== null &&
            !(Symbol.toPrimitive in i || "toString" in i || "valueOf" in i))
    );
}
function Bt(i) {
    return !Da(i) && !isNaN(parseFloat(i)) && isFinite(i);
}
function an(i, t) {
    const e = Math.round(i);
    return e - t <= i && e + t >= i;
}
function Wi(i, t, e) {
    let s, n, o;
    for (s = 0, n = i.length; s < n; s++) {
        ((o = i[s][e]),
            isNaN(o) ||
                ((t.min = Math.min(t.min, o)), (t.max = Math.max(t.max, o))));
    }
}
function ot(i) {
    return i * (R / 180);
}
function si(i) {
    return i * (180 / R);
}
function Ni(i) {
    if (!N(i)) return;
    let t = 1;
    let e = 0;
    for (; Math.round(i * t) / t !== i; ) ((t *= 10), e++);
    return e;
}
function Hi(i, t) {
    const e = t.x - i.x;
    const s = t.y - i.y;
    const n = Math.sqrt(e * e + s * s);
    let o = Math.atan2(s, e);
    return (o < -0.5 * R && (o += B), { angle: o, distance: n });
}
function ti(i, t) {
    return Math.sqrt(Math.pow(t.x - i.x, 2) + Math.pow(t.y - i.y, 2));
}
function Oa(i, t) {
    return ((i - t + Sa) % B) - R;
}
function X(i) {
    return ((i % B) + B) % B;
}
function ie(i, t, e, s) {
    const n = X(i);
    const o = X(t);
    const a = X(e);
    const r = X(o - n);
    const l = X(a - n);
    const c = X(n - o);
    const h = X(n - a);
    return n === o || n === a || (s && o === a) || (r > l && c < h);
}
function Y(i, t, e) {
    return Math.max(t, Math.min(e, i));
}
function rn(i) {
    return Y(i, -32768, 32767);
}
function ut(i, t, e, s = 1e-6) {
    return i >= Math.min(t, e) - s && i <= Math.max(t, e) + s;
}
function ni(i, t, e) {
    e = e || ((a) => i[a] < t);
    let s = i.length - 1;
    let n = 0;
    let o;
    for (; s - n > 1; ) ((o = (n + s) >> 1), e(o) ? (n = o) : (s = o));
    return { lo: n, hi: s };
}
const ct = (i, t, e, s) =>
    ni(
        i,
        e,
        s
            ? (n) => {
                  const o = i[n][t];
                  return o < e || (o === e && i[n + 1][t] === e);
              }
            : (n) => i[n][t] < e,
    );
const ln = (i, t, e) => ni(i, e, (s) => i[s][t] >= e);
function cn(i, t, e) {
    let s = 0;
    let n = i.length;
    for (; s < n && i[s] < t; ) s++;
    for (; n > s && i[n - 1] > e; ) n--;
    return s > 0 || n < i.length ? i.slice(s, n) : i;
}
const hn = ["push", "pop", "shift", "splice", "unshift"];
function dn(i, t) {
    if (i._chartjs) {
        i._chartjs.listeners.push(t);
        return;
    }
    (Object.defineProperty(i, "_chartjs", {
        configurable: !0,
        enumerable: !1,
        value: { listeners: [t] },
    }),
        hn.forEach((e) => {
            const s = "_onData" + ii(e);
            const n = i[e];
            Object.defineProperty(i, e, {
                configurable: !0,
                enumerable: !1,
                value(...o) {
                    const a = n.apply(this, o);
                    return (
                        i._chartjs.listeners.forEach((r) => {
                            typeof r[s] === "function" && r[s](...o);
                        }),
                        a
                    );
                },
            });
        }));
}
function ji(i, t) {
    const e = i._chartjs;
    if (!e) return;
    const s = e.listeners;
    const n = s.indexOf(t);
    (n !== -1 && s.splice(n, 1),
        !(s.length > 0) &&
            (hn.forEach((o) => {
                delete i[o];
            }),
            delete i._chartjs));
}
function $i(i) {
    const t = new Set(i);
    return t.size === i.length ? i : Array.from(t);
}
const Yi = (function () {
    return typeof window > "u"
        ? function (i) {
              return i();
          }
        : window.requestAnimationFrame;
})();
function Ui(i, t) {
    let e = [];
    let s = !1;
    return function (...n) {
        ((e = n),
            s ||
                ((s = !0),
                Yi.call(window, () => {
                    ((s = !1), i.apply(t, e));
                })));
    };
}
function un(i, t) {
    let e;
    return function (...s) {
        return (
            t ? (clearTimeout(e), (e = setTimeout(i, t, s))) : i.apply(this, s),
            t
        );
    };
}
const oi = (i) => (i === "start" ? "left" : i === "end" ? "right" : "center");
const K = (i, t, e) => (i === "start" ? t : i === "end" ? e : (t + e) / 2);
const fn = (i, t, e, s) =>
    i === (s ? "left" : "right") ? e : i === "center" ? (t + e) / 2 : t;
function Xi(i, t, e) {
    const s = t.length;
    let n = 0;
    let o = s;
    if (i._sorted) {
        const { iScale: a, vScale: r, _parsed: l } = i;
        const c =
            i.dataset && i.dataset.options ? i.dataset.options.spanGaps : null;
        const h = a.axis;
        const {
            min: d,
            max: u,
            minDefined: f,
            maxDefined: g,
        } = a.getUserBounds();
        if (f) {
            if (
                ((n = Math.min(
                    ct(l, h, d).lo,
                    e ? s : ct(t, h, a.getPixelForValue(d)).lo,
                )),
                c)
            ) {
                const p = l
                    .slice(0, n + 1)
                    .reverse()
                    .findIndex((m) => !A(m[r.axis]));
                n -= Math.max(0, p);
            }
            n = Y(n, 0, s - 1);
        }
        if (g) {
            let p = Math.max(
                ct(l, a.axis, u, !0).hi + 1,
                e ? 0 : ct(t, h, a.getPixelForValue(u), !0).hi + 1,
            );
            if (c) {
                const m = l.slice(p - 1).findIndex((b) => !A(b[r.axis]));
                p += Math.max(0, m);
            }
            o = Y(p, n, s) - n;
        } else o = s - n;
    }
    return { start: n, count: o };
}
function Ki(i) {
    const { xScale: t, yScale: e, _scaleRanges: s } = i;
    const n = { xmin: t.min, xmax: t.max, ymin: e.min, ymax: e.max };
    if (!s) return ((i._scaleRanges = n), !0);
    const o =
        s.xmin !== t.min ||
        s.xmax !== t.max ||
        s.ymin !== e.min ||
        s.ymax !== e.max;
    return (Object.assign(s, n), o);
}
const Ke = (i) => i === 0 || i === 1;
const Us = (i, t, e) =>
    -(Math.pow(2, 10 * (i -= 1)) * Math.sin(((i - t) * B) / e));
const Xs = (i, t, e) => Math.pow(2, -10 * i) * Math.sin(((i - t) * B) / e) + 1;
var Gt = {
    linear: (i) => i,
    easeInQuad: (i) => i * i,
    easeOutQuad: (i) => -i * (i - 2),
    easeInOutQuad: (i) =>
        (i /= 0.5) < 1 ? 0.5 * i * i : -0.5 * (--i * (i - 2) - 1),
    easeInCubic: (i) => i * i * i,
    easeOutCubic: (i) => (i -= 1) * i * i + 1,
    easeInOutCubic: (i) =>
        (i /= 0.5) < 1 ? 0.5 * i * i * i : 0.5 * ((i -= 2) * i * i + 2),
    easeInQuart: (i) => i * i * i * i,
    easeOutQuart: (i) => -((i -= 1) * i * i * i - 1),
    easeInOutQuart: (i) =>
        (i /= 0.5) < 1
            ? 0.5 * i * i * i * i
            : -0.5 * ((i -= 2) * i * i * i - 2),
    easeInQuint: (i) => i * i * i * i * i,
    easeOutQuint: (i) => (i -= 1) * i * i * i * i + 1,
    easeInOutQuint: (i) =>
        (i /= 0.5) < 1
            ? 0.5 * i * i * i * i * i
            : 0.5 * ((i -= 2) * i * i * i * i + 2),
    easeInSine: (i) => -Math.cos(i * H) + 1,
    easeOutSine: (i) => Math.sin(i * H),
    easeInOutSine: (i) => -0.5 * (Math.cos(R * i) - 1),
    easeInExpo: (i) => (i === 0 ? 0 : Math.pow(2, 10 * (i - 1))),
    easeOutExpo: (i) => (i === 1 ? 1 : -Math.pow(2, -10 * i) + 1),
    easeInOutExpo: (i) =>
        Ke(i)
            ? i
            : i < 0.5
              ? 0.5 * Math.pow(2, 10 * (i * 2 - 1))
              : 0.5 * (-Math.pow(2, -10 * (i * 2 - 1)) + 2),
    easeInCirc: (i) => (i >= 1 ? i : -(Math.sqrt(1 - i * i) - 1)),
    easeOutCirc: (i) => Math.sqrt(1 - (i -= 1) * i),
    easeInOutCirc: (i) =>
        (i /= 0.5) < 1
            ? -0.5 * (Math.sqrt(1 - i * i) - 1)
            : 0.5 * (Math.sqrt(1 - (i -= 2) * i) + 1),
    easeInElastic: (i) => (Ke(i) ? i : Us(i, 0.075, 0.3)),
    easeOutElastic: (i) => (Ke(i) ? i : Xs(i, 0.075, 0.3)),
    easeInOutElastic(i) {
        return Ke(i)
            ? i
            : i < 0.5
              ? 0.5 * Us(i * 2, 0.1125, 0.45)
              : 0.5 + 0.5 * Xs(i * 2 - 1, 0.1125, 0.45);
    },
    easeInBack(i) {
        return i * i * ((1.70158 + 1) * i - 1.70158);
    },
    easeOutBack(i) {
        return (i -= 1) * i * ((1.70158 + 1) * i + 1.70158) + 1;
    },
    easeInOutBack(i) {
        let t = 1.70158;
        return (i /= 0.5) < 1
            ? 0.5 * (i * i * (((t *= 1.525) + 1) * i - t))
            : 0.5 * ((i -= 2) * i * (((t *= 1.525) + 1) * i + t) + 2);
    },
    easeInBounce: (i) => 1 - Gt.easeOutBounce(1 - i),
    easeOutBounce(i) {
        return i < 1 / 2.75
            ? 7.5625 * i * i
            : i < 2 / 2.75
              ? 7.5625 * (i -= 1.5 / 2.75) * i + 0.75
              : i < 2.5 / 2.75
                ? 7.5625 * (i -= 2.25 / 2.75) * i + 0.9375
                : 7.5625 * (i -= 2.625 / 2.75) * i + 0.984375;
    },
    easeInOutBounce: (i) =>
        i < 0.5
            ? Gt.easeInBounce(i * 2) * 0.5
            : Gt.easeOutBounce(i * 2 - 1) * 0.5 + 0.5,
};
function qi(i) {
    if (i && typeof i === "object") {
        const t = i.toString();
        return (
            t === "[object CanvasPattern]" || t === "[object CanvasGradient]"
        );
    }
    return !1;
}
function Gi(i) {
    return qi(i) ? i : new _e(i);
}
function Ei(i) {
    return qi(i) ? i : new _e(i).saturate(0.5).darken(0.1).hexString();
}
const Ca = ["x", "y", "borderWidth", "radius", "tension"];
const Aa = ["color", "borderColor", "backgroundColor"];
function Ta(i) {
    (i.set("animation", {
        delay: void 0,
        duration: 1e3,
        easing: "easeOutQuart",
        fn: void 0,
        from: void 0,
        loop: void 0,
        to: void 0,
        type: void 0,
    }),
        i.describe("animation", {
            _fallback: !1,
            _indexable: !1,
            _scriptable: (t) =>
                t !== "onProgress" && t !== "onComplete" && t !== "fn",
        }),
        i.set("animations", {
            colors: { type: "color", properties: Aa },
            numbers: { type: "number", properties: Ca },
        }),
        i.describe("animations", { _fallback: "animation" }),
        i.set("transitions", {
            active: { animation: { duration: 400 } },
            resize: { animation: { duration: 0 } },
            show: {
                animations: {
                    colors: { from: "transparent" },
                    visible: { type: "boolean", duration: 0 },
                },
            },
            hide: {
                animations: {
                    colors: { to: "transparent" },
                    visible: {
                        type: "boolean",
                        easing: "linear",
                        fn: (t) => t | 0,
                    },
                },
            },
        }));
}
function La(i) {
    i.set("layout", {
        autoPadding: !0,
        padding: { top: 0, right: 0, bottom: 0, left: 0 },
    });
}
const Ks = new Map();
function Ra(i, t) {
    t = t || {};
    const e = i + JSON.stringify(t);
    let s = Ks.get(e);
    return (s || ((s = new Intl.NumberFormat(i, t)), Ks.set(e, s)), s);
}
function se(i, t, e) {
    return Ra(t, e).format(i);
}
var gn = {
    values(i) {
        return z(i) ? i : "" + i;
    },
    numeric(i, t, e) {
        if (i === 0) return "0";
        const s = this.chart.options.locale;
        let n;
        let o = i;
        if (e.length > 1) {
            const c = Math.max(
                Math.abs(e[0].value),
                Math.abs(e[e.length - 1].value),
            );
            ((c < 1e-4 || c > 1e15) && (n = "scientific"), (o = Ea(i, e)));
        }
        const a = xt(Math.abs(o));
        const r = isNaN(a) ? 1 : Math.max(Math.min(-1 * Math.floor(a), 20), 0);
        const l = {
            notation: n,
            minimumFractionDigits: r,
            maximumFractionDigits: r,
        };
        return (Object.assign(l, this.options.ticks.format), se(i, s, l));
    },
    logarithmic(i, t, e) {
        if (i === 0) return "0";
        const s = e[t].significand || i / Math.pow(10, Math.floor(xt(i)));
        return [1, 2, 3, 5, 10, 15].includes(s) || t > 0.8 * e.length
            ? gn.numeric.call(this, i, t, e)
            : "";
    },
};
function Ea(i, t) {
    let e = t.length > 3 ? t[2].value - t[1].value : t[1].value - t[0].value;
    return (
        Math.abs(e) >= 1 && i !== Math.floor(i) && (e = i - Math.floor(i)),
        e
    );
}
const we = { formatters: gn };
function Ia(i) {
    (i.set("scale", {
        display: !0,
        offset: !1,
        reverse: !1,
        beginAtZero: !1,
        bounds: "ticks",
        clip: !0,
        grace: 0,
        grid: {
            display: !0,
            lineWidth: 1,
            drawOnChartArea: !0,
            drawTicks: !0,
            tickLength: 8,
            tickWidth: (t, e) => e.lineWidth,
            tickColor: (t, e) => e.color,
            offset: !1,
        },
        border: { display: !0, dash: [], dashOffset: 0, width: 1 },
        title: { display: !1, text: "", padding: { top: 4, bottom: 4 } },
        ticks: {
            minRotation: 0,
            maxRotation: 50,
            mirror: !1,
            textStrokeWidth: 0,
            textStrokeColor: "",
            padding: 3,
            display: !0,
            autoSkip: !0,
            autoSkipPadding: 3,
            labelOffset: 0,
            callback: we.formatters.values,
            minor: {},
            major: {},
            align: "center",
            crossAlign: "near",
            showLabelBackdrop: !1,
            backdropColor: "rgba(255, 255, 255, 0.75)",
            backdropPadding: 2,
        },
    }),
        i.route("scale.ticks", "color", "", "color"),
        i.route("scale.grid", "color", "", "borderColor"),
        i.route("scale.border", "color", "", "borderColor"),
        i.route("scale.title", "color", "", "color"),
        i.describe("scale", {
            _fallback: !1,
            _scriptable: (t) =>
                !t.startsWith("before") &&
                !t.startsWith("after") &&
                t !== "callback" &&
                t !== "parser",
            _indexable: (t) =>
                t !== "borderDash" && t !== "tickBorderDash" && t !== "dash",
        }),
        i.describe("scales", { _fallback: "scale" }),
        i.describe("scale.ticks", {
            _scriptable: (t) => t !== "backdropPadding" && t !== "callback",
            _indexable: (t) => t !== "backdropPadding",
        }));
}
const Dt = Object.create(null);
const ai = Object.create(null);
function ve(i, t) {
    if (!t) return i;
    const e = t.split(".");
    for (let s = 0, n = e.length; s < n; ++s) {
        const o = e[s];
        i = i[o] || (i[o] = Object.create(null));
    }
    return i;
}
function Ii(i, t, e) {
    return typeof t === "string" ? Zt(ve(i, t), e) : Zt(ve(i, ""), t);
}
const Fi = class {
    constructor(t, e) {
        ((this.animation = void 0),
            (this.backgroundColor = "rgba(0,0,0,0.1)"),
            (this.borderColor = "rgba(0,0,0,0.1)"),
            (this.color = "#666"),
            (this.datasets = {}),
            (this.devicePixelRatio = (s) =>
                s.chart.platform.getDevicePixelRatio()),
            (this.elements = {}),
            (this.events = [
                "mousemove",
                "mouseout",
                "click",
                "touchstart",
                "touchmove",
            ]),
            (this.font = {
                family: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
                size: 12,
                style: "normal",
                lineHeight: 1.2,
                weight: null,
            }),
            (this.hover = {}),
            (this.hoverBackgroundColor = (s, n) => Ei(n.backgroundColor)),
            (this.hoverBorderColor = (s, n) => Ei(n.borderColor)),
            (this.hoverColor = (s, n) => Ei(n.color)),
            (this.indexAxis = "x"),
            (this.interaction = {
                mode: "nearest",
                intersect: !0,
                includeInvisible: !1,
            }),
            (this.maintainAspectRatio = !0),
            (this.onHover = null),
            (this.onClick = null),
            (this.parsing = !0),
            (this.plugins = {}),
            (this.responsive = !0),
            (this.scale = void 0),
            (this.scales = {}),
            (this.showLine = !0),
            (this.drawActiveElementsOnTop = !0),
            this.describe(t),
            this.apply(e));
    }

    set(t, e) {
        return Ii(this, t, e);
    }

    get(t) {
        return ve(this, t);
    }

    describe(t, e) {
        return Ii(ai, t, e);
    }

    override(t, e) {
        return Ii(Dt, t, e);
    }

    route(t, e, s, n) {
        const o = ve(this, t);
        const a = ve(this, s);
        const r = "_" + e;
        Object.defineProperties(o, {
            [r]: { value: o[e], writable: !0 },
            [e]: {
                enumerable: !0,
                get() {
                    const l = this[r];
                    const c = a[n];
                    return T(l) ? Object.assign({}, c, l) : D(l, c);
                },
                set(l) {
                    this[r] = l;
                },
            },
        });
    }

    apply(t) {
        t.forEach((e) => e(this));
    }
};
const V = new Fi(
    {
        _scriptable: (i) => !i.startsWith("on"),
        _indexable: (i) => i !== "events",
        hover: { _fallback: "interaction" },
        interaction: { _scriptable: !1, _indexable: !1 },
    },
    [Ta, La, Ia],
);
function Fa(i) {
    return !i || A(i.size) || A(i.family)
        ? null
        : (i.style ? i.style + " " : "") +
              (i.weight ? i.weight + " " : "") +
              i.size +
              "px " +
              i.family;
}
function Me(i, t, e, s, n) {
    let o = t[n];
    return (
        o || ((o = t[n] = i.measureText(n).width), e.push(n)),
        o > s && (s = o),
        s
    );
}
function pn(i, t, e, s) {
    s = s || {};
    let n = (s.data = s.data || {});
    let o = (s.garbageCollect = s.garbageCollect || []);
    (s.font !== t &&
        ((n = s.data = {}), (o = s.garbageCollect = []), (s.font = t)),
        i.save(),
        (i.font = t));
    let a = 0;
    const r = e.length;
    let l;
    let c;
    let h;
    let d;
    let u;
    for (l = 0; l < r; l++) {
        if (((d = e[l]), d != null && !z(d))) a = Me(i, n, o, a, d);
        else if (z(d)) {
            for (c = 0, h = d.length; c < h; c++) {
                ((u = d[c]), u != null && !z(u) && (a = Me(i, n, o, a, u)));
            }
        }
    }
    i.restore();
    const f = o.length / 2;
    if (f > e.length) {
        for (l = 0; l < f; l++) delete n[o[l]];
        o.splice(0, f);
    }
    return a;
}
function Ot(i, t, e) {
    const s = i.currentDevicePixelRatio;
    const n = e !== 0 ? Math.max(e / 2, 0.5) : 0;
    return Math.round((t - n) * s) / s + n;
}
function Zi(i, t) {
    (!t && !i) ||
        ((t = t || i.getContext("2d")),
        t.save(),
        t.resetTransform(),
        t.clearRect(0, 0, i.width, i.height),
        t.restore());
}
function ri(i, t, e, s) {
    Ji(i, t, e, s, null);
}
function Ji(i, t, e, s, n) {
    let o;
    let a;
    let r;
    let l;
    let c;
    let h;
    let d;
    let u;
    const f = t.pointStyle;
    const g = t.rotation;
    const p = t.radius;
    let m = (g || 0) * Pa;
    if (
        f &&
        typeof f === "object" &&
        ((o = f.toString()),
        o === "[object HTMLImageElement]" || o === "[object HTMLCanvasElement]")
    ) {
        (i.save(),
            i.translate(e, s),
            i.rotate(m),
            i.drawImage(f, -f.width / 2, -f.height / 2, f.width, f.height),
            i.restore());
        return;
    }
    if (!(isNaN(p) || p <= 0)) {
        switch ((i.beginPath(), f)) {
            default:
                (n ? i.ellipse(e, s, n / 2, p, 0, 0, B) : i.arc(e, s, p, 0, B),
                    i.closePath());
                break;
            case "triangle":
                ((h = n ? n / 2 : p),
                    i.moveTo(e + Math.sin(m) * h, s - Math.cos(m) * p),
                    (m += Ys),
                    i.lineTo(e + Math.sin(m) * h, s - Math.cos(m) * p),
                    (m += Ys),
                    i.lineTo(e + Math.sin(m) * h, s - Math.cos(m) * p),
                    i.closePath());
                break;
            case "rectRounded":
                ((c = p * 0.516),
                    (l = p - c),
                    (a = Math.cos(m + It) * l),
                    (d = Math.cos(m + It) * (n ? n / 2 - c : l)),
                    (r = Math.sin(m + It) * l),
                    (u = Math.sin(m + It) * (n ? n / 2 - c : l)),
                    i.arc(e - d, s - r, c, m - R, m - H),
                    i.arc(e + u, s - a, c, m - H, m),
                    i.arc(e + d, s + r, c, m, m + H),
                    i.arc(e - u, s + a, c, m + H, m + R),
                    i.closePath());
                break;
            case "rect":
                if (!g) {
                    ((l = Math.SQRT1_2 * p),
                        (h = n ? n / 2 : l),
                        i.rect(e - h, s - l, 2 * h, 2 * l));
                    break;
                }
                m += It;
            case "rectRot":
                ((d = Math.cos(m) * (n ? n / 2 : p)),
                    (a = Math.cos(m) * p),
                    (r = Math.sin(m) * p),
                    (u = Math.sin(m) * (n ? n / 2 : p)),
                    i.moveTo(e - d, s - r),
                    i.lineTo(e + u, s - a),
                    i.lineTo(e + d, s + r),
                    i.lineTo(e - u, s + a),
                    i.closePath());
                break;
            case "crossRot":
                m += It;
            case "cross":
                ((d = Math.cos(m) * (n ? n / 2 : p)),
                    (a = Math.cos(m) * p),
                    (r = Math.sin(m) * p),
                    (u = Math.sin(m) * (n ? n / 2 : p)),
                    i.moveTo(e - d, s - r),
                    i.lineTo(e + d, s + r),
                    i.moveTo(e + u, s - a),
                    i.lineTo(e - u, s + a));
                break;
            case "star":
                ((d = Math.cos(m) * (n ? n / 2 : p)),
                    (a = Math.cos(m) * p),
                    (r = Math.sin(m) * p),
                    (u = Math.sin(m) * (n ? n / 2 : p)),
                    i.moveTo(e - d, s - r),
                    i.lineTo(e + d, s + r),
                    i.moveTo(e + u, s - a),
                    i.lineTo(e - u, s + a),
                    (m += It),
                    (d = Math.cos(m) * (n ? n / 2 : p)),
                    (a = Math.cos(m) * p),
                    (r = Math.sin(m) * p),
                    (u = Math.sin(m) * (n ? n / 2 : p)),
                    i.moveTo(e - d, s - r),
                    i.lineTo(e + d, s + r),
                    i.moveTo(e + u, s - a),
                    i.lineTo(e - u, s + a));
                break;
            case "line":
                ((a = n ? n / 2 : Math.cos(m) * p),
                    (r = Math.sin(m) * p),
                    i.moveTo(e - a, s - r),
                    i.lineTo(e + a, s + r));
                break;
            case "dash":
                (i.moveTo(e, s),
                    i.lineTo(
                        e + Math.cos(m) * (n ? n / 2 : p),
                        s + Math.sin(m) * p,
                    ));
                break;
            case !1:
                i.closePath();
                break;
        }
        (i.fill(), t.borderWidth > 0 && i.stroke());
    }
}
function ht(i, t, e) {
    return (
        (e = e || 0.5),
        !t ||
            (i &&
                i.x > t.left - e &&
                i.x < t.right + e &&
                i.y > t.top - e &&
                i.y < t.bottom + e)
    );
}
function Se(i, t) {
    (i.save(),
        i.beginPath(),
        i.rect(t.left, t.top, t.right - t.left, t.bottom - t.top),
        i.clip());
}
function Pe(i) {
    i.restore();
}
function mn(i, t, e, s, n) {
    if (!t) return i.lineTo(e.x, e.y);
    if (n === "middle") {
        const o = (t.x + e.x) / 2;
        (i.lineTo(o, t.y), i.lineTo(o, e.y));
    } else (n === "after") != !!s ? i.lineTo(t.x, e.y) : i.lineTo(e.x, t.y);
    i.lineTo(e.x, e.y);
}
function bn(i, t, e, s) {
    if (!t) return i.lineTo(e.x, e.y);
    i.bezierCurveTo(
        s ? t.cp1x : t.cp2x,
        s ? t.cp1y : t.cp2y,
        s ? e.cp2x : e.cp1x,
        s ? e.cp2y : e.cp1y,
        e.x,
        e.y,
    );
}
function za(i, t) {
    (t.translation && i.translate(t.translation[0], t.translation[1]),
        A(t.rotation) || i.rotate(t.rotation),
        t.color && (i.fillStyle = t.color),
        t.textAlign && (i.textAlign = t.textAlign),
        t.textBaseline && (i.textBaseline = t.textBaseline));
}
function Ba(i, t, e, s, n) {
    if (n.strikethrough || n.underline) {
        const o = i.measureText(s);
        const a = t - o.actualBoundingBoxLeft;
        const r = t + o.actualBoundingBoxRight;
        const l = e - o.actualBoundingBoxAscent;
        const c = e + o.actualBoundingBoxDescent;
        const h = n.strikethrough ? (l + c) / 2 : c;
        ((i.strokeStyle = i.fillStyle),
            i.beginPath(),
            (i.lineWidth = n.decorationWidth || 2),
            i.moveTo(a, h),
            i.lineTo(r, h),
            i.stroke());
    }
}
function Va(i, t) {
    const e = i.fillStyle;
    ((i.fillStyle = t.color),
        i.fillRect(t.left, t.top, t.width, t.height),
        (i.fillStyle = e));
}
function Ct(i, t, e, s, n, o = {}) {
    const a = z(t) ? t : [t];
    const r = o.strokeWidth > 0 && o.strokeColor !== "";
    let l;
    let c;
    for (i.save(), i.font = n.string, za(i, o), l = 0; l < a.length; ++l) {
        ((c = a[l]),
            o.backdrop && Va(i, o.backdrop),
            r &&
                (o.strokeColor && (i.strokeStyle = o.strokeColor),
                A(o.strokeWidth) || (i.lineWidth = o.strokeWidth),
                i.strokeText(c, e, s, o.maxWidth)),
            i.fillText(c, e, s, o.maxWidth),
            Ba(i, e, s, c, o),
            (s += Number(n.lineHeight)));
    }
    i.restore();
}
function ne(i, t) {
    const { x: e, y: s, w: n, h: o, radius: a } = t;
    (i.arc(e + a.topLeft, s + a.topLeft, a.topLeft, 1.5 * R, R, !0),
        i.lineTo(e, s + o - a.bottomLeft),
        i.arc(e + a.bottomLeft, s + o - a.bottomLeft, a.bottomLeft, R, H, !0),
        i.lineTo(e + n - a.bottomRight, s + o),
        i.arc(
            e + n - a.bottomRight,
            s + o - a.bottomRight,
            a.bottomRight,
            H,
            0,
            !0,
        ),
        i.lineTo(e + n, s + a.topRight),
        i.arc(e + n - a.topRight, s + a.topRight, a.topRight, 0, -H, !0),
        i.lineTo(e + a.topLeft, s));
}
const Wa = /^(normal|(\d+(?:\.\d+)?)(px|em|%)?)$/;
const Na =
    /^(normal|italic|initial|inherit|unset|(oblique( -?[0-9]?[0-9]deg)?))$/;
function Ha(i, t) {
    const e = ("" + i).match(Wa);
    if (!e || e[1] === "normal") return t * 1.2;
    switch (((i = +e[2]), e[3])) {
        case "px":
            return i;
        case "%":
            i /= 100;
            break;
    }
    return t * i;
}
const ja = (i) => +i || 0;
function li(i, t) {
    const e = {};
    const s = T(t);
    const n = s ? Object.keys(t) : t;
    const o = T(i) ? (s ? (a) => D(i[a], i[t[a]]) : (a) => i[a]) : () => i;
    for (const a of n) e[a] = ja(o(a));
    return e;
}
function Qi(i) {
    return li(i, { top: "y", right: "x", bottom: "y", left: "x" });
}
function At(i) {
    return li(i, ["topLeft", "topRight", "bottomLeft", "bottomRight"]);
}
function q(i) {
    const t = Qi(i);
    return ((t.width = t.left + t.right), (t.height = t.top + t.bottom), t);
}
function $(i, t) {
    ((i = i || {}), (t = t || V.font));
    let e = D(i.size, t.size);
    typeof e === "string" && (e = parseInt(e, 10));
    let s = D(i.style, t.style);
    s &&
        !("" + s).match(Na) &&
        (console.warn('Invalid font style specified: "' + s + '"'),
        (s = void 0));
    const n = {
        family: D(i.family, t.family),
        lineHeight: Ha(D(i.lineHeight, t.lineHeight), e),
        size: e,
        style: s,
        weight: D(i.weight, t.weight),
        string: "",
    };
    return ((n.string = Fa(n)), n);
}
function oe(i, t, e, s) {
    let n = !0;
    let o;
    let a;
    let r;
    for (o = 0, a = i.length; o < a; ++o) {
        if (
            ((r = i[o]),
            r !== void 0 &&
                (t !== void 0 &&
                    typeof r === "function" &&
                    ((r = r(t)), (n = !1)),
                e !== void 0 && z(r) && ((r = r[e % r.length]), (n = !1)),
                r !== void 0))
        ) {
            return (s && !n && (s.cacheable = !1), r);
        }
    }
}
function xn(i, t, e) {
    const { min: s, max: n } = i;
    const o = zi(t, (n - s) / 2);
    const a = (r, l) => (e && r === 0 ? 0 : r + l);
    return { min: a(s, -Math.abs(o)), max: a(n, o) };
}
function yt(i, t) {
    return Object.assign(Object.create(i), t);
}
function ci(i, t = [""], e, s, n = () => i[0]) {
    const o = e || i;
    typeof s > "u" && (s = vn("_fallback", i));
    const a = {
        [Symbol.toStringTag]: "Object",
        _cacheable: !0,
        _scopes: i,
        _rootScopes: o,
        _fallback: s,
        _getTarget: n,
        override: (r) => ci([r, ...i], t, o, s),
    };
    return new Proxy(a, {
        deleteProperty(r, l) {
            return (delete r[l], delete r._keys, delete i[0][l], !0);
        },
        get(r, l) {
            return _n(r, l, () => Za(l, t, i, r));
        },
        getOwnPropertyDescriptor(r, l) {
            return Reflect.getOwnPropertyDescriptor(r._scopes[0], l);
        },
        getPrototypeOf() {
            return Reflect.getPrototypeOf(i[0]);
        },
        has(r, l) {
            return Gs(r).includes(l);
        },
        ownKeys(r) {
            return Gs(r);
        },
        set(r, l, c) {
            const h = r._storage || (r._storage = n());
            return ((r[l] = h[l] = c), delete r._keys, !0);
        },
    });
}
function zt(i, t, e, s) {
    const n = {
        _cacheable: !1,
        _proxy: i,
        _context: t,
        _subProxy: e,
        _stack: new Set(),
        _descriptors: ts(i, s),
        setContext: (o) => zt(i, o, e, s),
        override: (o) => zt(i.override(o), t, e, s),
    };
    return new Proxy(n, {
        deleteProperty(o, a) {
            return (delete o[a], delete i[a], !0);
        },
        get(o, a, r) {
            return _n(o, a, () => Ya(o, a, r));
        },
        getOwnPropertyDescriptor(o, a) {
            return o._descriptors.allKeys
                ? Reflect.has(i, a)
                    ? { enumerable: !0, configurable: !0 }
                    : void 0
                : Reflect.getOwnPropertyDescriptor(i, a);
        },
        getPrototypeOf() {
            return Reflect.getPrototypeOf(i);
        },
        has(o, a) {
            return Reflect.has(i, a);
        },
        ownKeys() {
            return Reflect.ownKeys(i);
        },
        set(o, a, r) {
            return ((i[a] = r), delete o[a], !0);
        },
    });
}
function ts(i, t = { scriptable: !0, indexable: !0 }) {
    const {
        _scriptable: e = t.scriptable,
        _indexable: s = t.indexable,
        _allKeys: n = t.allKeys,
    } = i;
    return {
        allKeys: n,
        scriptable: e,
        indexable: s,
        isScriptable: bt(e) ? e : () => e,
        isIndexable: bt(s) ? s : () => s,
    };
}
const $a = (i, t) => (i ? i + ii(t) : t);
const es = (i, t) =>
    T(t) &&
    i !== "adapters" &&
    (Object.getPrototypeOf(t) === null || t.constructor === Object);
function _n(i, t, e) {
    if (Object.prototype.hasOwnProperty.call(i, t) || t === "constructor") {
        return i[t];
    }
    const s = e();
    return ((i[t] = s), s);
}
function Ya(i, t, e) {
    const { _proxy: s, _context: n, _subProxy: o, _descriptors: a } = i;
    let r = s[t];
    return (
        bt(r) && a.isScriptable(t) && (r = Ua(t, r, i, e)),
        z(r) && r.length && (r = Xa(t, r, i, a.isIndexable)),
        es(t, r) && (r = zt(r, n, o && o[t], a)),
        r
    );
}
function Ua(i, t, e, s) {
    const { _proxy: n, _context: o, _subProxy: a, _stack: r } = e;
    if (r.has(i)) {
        throw new Error(
            "Recursion detected: " + Array.from(r).join("->") + "->" + i,
        );
    }
    r.add(i);
    let l = t(o, a || s);
    return (r.delete(i), es(i, l) && (l = is(n._scopes, n, i, l)), l);
}
function Xa(i, t, e, s) {
    const { _proxy: n, _context: o, _subProxy: a, _descriptors: r } = e;
    if (typeof o.index < "u" && s(i)) return t[o.index % t.length];
    if (T(t[0])) {
        const l = t;
        const c = n._scopes.filter((h) => h !== l);
        t = [];
        for (const h of l) {
            const d = is(c, n, i, h);
            t.push(zt(d, o, a && a[i], r));
        }
    }
    return t;
}
function yn(i, t, e) {
    return bt(i) ? i(t, e) : i;
}
const Ka = (i, t) => (i === !0 ? t : typeof i === "string" ? _t(t, i) : void 0);
function qa(i, t, e, s, n) {
    for (const o of t) {
        const a = Ka(e, o);
        if (a) {
            i.add(a);
            const r = yn(a._fallback, e, n);
            if (typeof r < "u" && r !== e && r !== s) return r;
        } else if (a === !1 && typeof s < "u" && e !== s) return null;
    }
    return !1;
}
function is(i, t, e, s) {
    const n = t._rootScopes;
    const o = yn(t._fallback, e, s);
    const a = [...i, ...n];
    const r = new Set();
    r.add(s);
    let l = qs(r, a, e, o || e, s);
    return l === null ||
        (typeof o < "u" && o !== e && ((l = qs(r, a, o, l, s)), l === null))
        ? !1
        : ci(Array.from(r), [""], n, o, () => Ga(t, e, s));
}
function qs(i, t, e, s, n) {
    for (; e; ) e = qa(i, t, e, s, n);
    return e;
}
function Ga(i, t, e) {
    const s = i._getTarget();
    t in s || (s[t] = {});
    const n = s[t];
    return z(n) && T(e) ? e : n || {};
}
function Za(i, t, e, s) {
    let n;
    for (const o of t) {
        if (((n = vn($a(o, i), e)), typeof n < "u")) {
            return es(i, n) ? is(e, s, i, n) : n;
        }
    }
}
function vn(i, t) {
    for (const e of t) {
        if (!e) continue;
        const s = e[i];
        if (typeof s < "u") return s;
    }
}
function Gs(i) {
    let t = i._keys;
    return (t || (t = i._keys = Ja(i._scopes)), t);
}
function Ja(i) {
    const t = new Set();
    for (const e of i) {
        for (const s of Object.keys(e).filter((n) => !n.startsWith("_"))) {
            t.add(s);
        }
    }
    return Array.from(t);
}
function ss(i, t, e, s) {
    const { iScale: n } = i;
    const { key: o = "r" } = this._parsing;
    const a = new Array(s);
    let r;
    let l;
    let c;
    let h;
    for (r = 0, l = s; r < l; ++r) {
        ((c = r + e), (h = t[c]), (a[r] = { r: n.parse(_t(h, o), c) }));
    }
    return a;
}
const Qa = Number.EPSILON || 1e-14;
const Jt = (i, t) => t < i.length && !i[t].skip && i[t];
const Mn = (i) => (i === "x" ? "y" : "x");
function tr(i, t, e, s) {
    const n = i.skip ? t : i;
    const o = t;
    const a = e.skip ? t : e;
    const r = ti(o, n);
    const l = ti(a, o);
    let c = r / (r + l);
    let h = l / (r + l);
    ((c = isNaN(c) ? 0 : c), (h = isNaN(h) ? 0 : h));
    const d = s * c;
    const u = s * h;
    return {
        previous: { x: o.x - d * (a.x - n.x), y: o.y - d * (a.y - n.y) },
        next: { x: o.x + u * (a.x - n.x), y: o.y + u * (a.y - n.y) },
    };
}
function er(i, t, e) {
    const s = i.length;
    let n;
    let o;
    let a;
    let r;
    let l;
    let c = Jt(i, 0);
    for (let h = 0; h < s - 1; ++h) {
        if (((l = c), (c = Jt(i, h + 1)), !(!l || !c))) {
            if (ee(t[h], 0, Qa)) {
                e[h] = e[h + 1] = 0;
                continue;
            }
            ((n = e[h] / t[h]),
                (o = e[h + 1] / t[h]),
                (r = Math.pow(n, 2) + Math.pow(o, 2)),
                !(r <= 9) &&
                    ((a = 3 / Math.sqrt(r)),
                    (e[h] = n * a * t[h]),
                    (e[h + 1] = o * a * t[h])));
        }
    }
}
function ir(i, t, e = "x") {
    const s = Mn(e);
    const n = i.length;
    let o;
    let a;
    let r;
    let l = Jt(i, 0);
    for (let c = 0; c < n; ++c) {
        if (((a = r), (r = l), (l = Jt(i, c + 1)), !r)) continue;
        const h = r[e];
        const d = r[s];
        (a &&
            ((o = (h - a[e]) / 3),
            (r[`cp1${e}`] = h - o),
            (r[`cp1${s}`] = d - o * t[c])),
            l &&
                ((o = (l[e] - h) / 3),
                (r[`cp2${e}`] = h + o),
                (r[`cp2${s}`] = d + o * t[c])));
    }
}
function sr(i, t = "x") {
    const e = Mn(t);
    const s = i.length;
    const n = Array(s).fill(0);
    const o = Array(s);
    let a;
    let r;
    let l;
    let c = Jt(i, 0);
    for (a = 0; a < s; ++a) {
        if (((r = l), (l = c), (c = Jt(i, a + 1)), !!l)) {
            if (c) {
                const h = c[t] - l[t];
                n[a] = h !== 0 ? (c[e] - l[e]) / h : 0;
            }
            o[a] = r
                ? c
                    ? lt(n[a - 1]) !== lt(n[a])
                        ? 0
                        : (n[a - 1] + n[a]) / 2
                    : n[a - 1]
                : n[a];
        }
    }
    (er(i, n, o), ir(i, o, t));
}
function qe(i, t, e) {
    return Math.max(Math.min(i, e), t);
}
function nr(i, t) {
    let e;
    let s;
    let n;
    let o;
    let a;
    let r = ht(i[0], t);
    for (e = 0, s = i.length; e < s; ++e) {
        ((a = o),
            (o = r),
            (r = e < s - 1 && ht(i[e + 1], t)),
            o &&
                ((n = i[e]),
                a &&
                    ((n.cp1x = qe(n.cp1x, t.left, t.right)),
                    (n.cp1y = qe(n.cp1y, t.top, t.bottom))),
                r &&
                    ((n.cp2x = qe(n.cp2x, t.left, t.right)),
                    (n.cp2y = qe(n.cp2y, t.top, t.bottom)))));
    }
}
function kn(i, t, e, s, n) {
    let o, a, r, l;
    if (
        (t.spanGaps && (i = i.filter((c) => !c.skip)),
        t.cubicInterpolationMode === "monotone")
    ) {
        sr(i, n);
    } else {
        let c = s ? i[i.length - 1] : i[0];
        for (o = 0, a = i.length; o < a; ++o) {
            ((r = i[o]),
                (l = tr(
                    c,
                    r,
                    i[Math.min(o + 1, a - (s ? 0 : 1)) % a],
                    t.tension,
                )),
                (r.cp1x = l.previous.x),
                (r.cp1y = l.previous.y),
                (r.cp2x = l.next.x),
                (r.cp2y = l.next.y),
                (c = r));
        }
    }
    t.capBezierPoints && nr(i, e);
}
function hi() {
    return typeof window < "u" && typeof document < "u";
}
function di(i) {
    let t = i.parentNode;
    return (t && t.toString() === "[object ShadowRoot]" && (t = t.host), t);
}
function ei(i, t, e) {
    let s;
    return (
        typeof i === "string"
            ? ((s = parseInt(i, 10)),
              i.indexOf("%") !== -1 && (s = (s / 100) * t.parentNode[e]))
            : (s = i),
        s
    );
}
const ui = (i) => i.ownerDocument.defaultView.getComputedStyle(i, null);
function or(i, t) {
    return ui(i).getPropertyValue(t);
}
const ar = ["top", "right", "bottom", "left"];
function Ft(i, t, e) {
    const s = {};
    e = e ? "-" + e : "";
    for (let n = 0; n < 4; n++) {
        const o = ar[n];
        s[o] = parseFloat(i[t + "-" + o + e]) || 0;
    }
    return ((s.width = s.left + s.right), (s.height = s.top + s.bottom), s);
}
const rr = (i, t, e) => (i > 0 || t > 0) && (!e || !e.shadowRoot);
function lr(i, t) {
    const e = i.touches;
    const s = e && e.length ? e[0] : i;
    const { offsetX: n, offsetY: o } = s;
    let a = !1;
    let r;
    let l;
    if (rr(n, o, i.target)) ((r = n), (l = o));
    else {
        const c = t.getBoundingClientRect();
        ((r = s.clientX - c.left), (l = s.clientY - c.top), (a = !0));
    }
    return { x: r, y: l, box: a };
}
function Tt(i, t) {
    if ("native" in i) return i;
    const { canvas: e, currentDevicePixelRatio: s } = t;
    const n = ui(e);
    const o = n.boxSizing === "border-box";
    const a = Ft(n, "padding");
    const r = Ft(n, "border", "width");
    const { x: l, y: c, box: h } = lr(i, e);
    const d = a.left + (h && r.left);
    const u = a.top + (h && r.top);
    let { width: f, height: g } = t;
    return (
        o && ((f -= a.width + r.width), (g -= a.height + r.height)),
        {
            x: Math.round((((l - d) / f) * e.width) / s),
            y: Math.round((((c - u) / g) * e.height) / s),
        }
    );
}
function cr(i, t, e) {
    let s, n;
    if (t === void 0 || e === void 0) {
        const o = i && di(i);
        if (!o) ((t = i.clientWidth), (e = i.clientHeight));
        else {
            const a = o.getBoundingClientRect();
            const r = ui(o);
            const l = Ft(r, "border", "width");
            const c = Ft(r, "padding");
            ((t = a.width - c.width - l.width),
                (e = a.height - c.height - l.height),
                (s = ei(r.maxWidth, o, "clientWidth")),
                (n = ei(r.maxHeight, o, "clientHeight")));
        }
    }
    return { width: t, height: e, maxWidth: s || Qe, maxHeight: n || Qe };
}
const Ge = (i) => Math.round(i * 10) / 10;
function wn(i, t, e, s) {
    const n = ui(i);
    const o = Ft(n, "margin");
    const a = ei(n.maxWidth, i, "clientWidth") || Qe;
    const r = ei(n.maxHeight, i, "clientHeight") || Qe;
    const l = cr(i, t, e);
    let { width: c, height: h } = l;
    if (n.boxSizing === "content-box") {
        const u = Ft(n, "border", "width");
        const f = Ft(n, "padding");
        ((c -= f.width + u.width), (h -= f.height + u.height));
    }
    return (
        (c = Math.max(0, c - o.width)),
        (h = Math.max(0, s ? c / s : h - o.height)),
        (c = Ge(Math.min(c, a, l.maxWidth))),
        (h = Ge(Math.min(h, r, l.maxHeight))),
        c && !h && (h = Ge(c / 2)),
        (t !== void 0 || e !== void 0) &&
            s &&
            l.height &&
            h > l.height &&
            ((h = l.height), (c = Ge(Math.floor(h * s)))),
        { width: c, height: h }
    );
}
function ns(i, t, e) {
    const s = t || 1;
    const n = Math.floor(i.height * s);
    const o = Math.floor(i.width * s);
    ((i.height = Math.floor(i.height)), (i.width = Math.floor(i.width)));
    const a = i.canvas;
    return (
        a.style &&
            (e || (!a.style.height && !a.style.width)) &&
            ((a.style.height = `${i.height}px`),
            (a.style.width = `${i.width}px`)),
        i.currentDevicePixelRatio !== s || a.height !== n || a.width !== o
            ? ((i.currentDevicePixelRatio = s),
              (a.height = n),
              (a.width = o),
              i.ctx.setTransform(s, 0, 0, s, 0, 0),
              !0)
            : !1
    );
}
const Sn = (function () {
    let i = !1;
    try {
        const t = {
            get passive() {
                return ((i = !0), !1);
            },
        };
        hi() &&
            (window.addEventListener("test", null, t),
            window.removeEventListener("test", null, t));
    } catch {}
    return i;
})();
function os(i, t) {
    const e = or(i, t);
    const s = e && e.match(/^(\d+)(\.\d+)?px$/);
    return s ? +s[1] : void 0;
}
function Pt(i, t, e, s) {
    return { x: i.x + e * (t.x - i.x), y: i.y + e * (t.y - i.y) };
}
function Pn(i, t, e, s) {
    return {
        x: i.x + e * (t.x - i.x),
        y:
            s === "middle"
                ? e < 0.5
                    ? i.y
                    : t.y
                : s === "after"
                  ? e < 1
                      ? i.y
                      : t.y
                  : e > 0
                    ? t.y
                    : i.y,
    };
}
function Dn(i, t, e, s) {
    const n = { x: i.cp2x, y: i.cp2y };
    const o = { x: t.cp1x, y: t.cp1y };
    const a = Pt(i, n, e);
    const r = Pt(n, o, e);
    const l = Pt(o, t, e);
    const c = Pt(a, r, e);
    const h = Pt(r, l, e);
    return Pt(c, h, e);
}
const hr = function (i, t) {
    return {
        x(e) {
            return i + i + t - e;
        },
        setWidth(e) {
            t = e;
        },
        textAlign(e) {
            return e === "center" ? e : e === "right" ? "left" : "right";
        },
        xPlus(e, s) {
            return e - s;
        },
        leftForLtr(e, s) {
            return e - s;
        },
    };
};
const dr = function () {
    return {
        x(i) {
            return i;
        },
        setWidth(i) {},
        textAlign(i) {
            return i;
        },
        xPlus(i, t) {
            return i + t;
        },
        leftForLtr(i, t) {
            return i;
        },
    };
};
function Vt(i, t, e) {
    return i ? hr(t, e) : dr();
}
function as(i, t) {
    let e, s;
    (t === "ltr" || t === "rtl") &&
        ((e = i.canvas.style),
        (s = [
            e.getPropertyValue("direction"),
            e.getPropertyPriority("direction"),
        ]),
        e.setProperty("direction", t, "important"),
        (i.prevTextDirection = s));
}
function rs(i, t) {
    t !== void 0 &&
        (delete i.prevTextDirection,
        i.canvas.style.setProperty("direction", t[0], t[1]));
}
function On(i) {
    return i === "angle"
        ? { between: ie, compare: Oa, normalize: X }
        : { between: ut, compare: (t, e) => t - e, normalize: (t) => t };
}
function Zs({ start: i, end: t, count: e, loop: s, style: n }) {
    return {
        start: i % e,
        end: t % e,
        loop: s && (t - i + 1) % e === 0,
        style: n,
    };
}
function ur(i, t, e) {
    const { property: s, start: n, end: o } = e;
    const { between: a, normalize: r } = On(s);
    const l = t.length;
    let { start: c, end: h, loop: d } = i;
    let u;
    let f;
    if (d) {
        for (
            c += l, h += l, u = 0, f = l;
            u < f && a(r(t[c % l][s]), n, o);
            ++u
        ) {
            (c--, h--);
        }
        ((c %= l), (h %= l));
    }
    return (h < c && (h += l), { start: c, end: h, loop: d, style: i.style });
}
function ls(i, t, e) {
    if (!e) return [i];
    const { property: s, start: n, end: o } = e;
    const a = t.length;
    const { compare: r, between: l, normalize: c } = On(s);
    const { start: h, end: d, loop: u, style: f } = ur(i, t, e);
    const g = [];
    let p = !1;
    let m = null;
    let b;
    let x;
    let v;
    const y = () => l(n, v, b) && r(n, v) !== 0;
    const _ = () => r(o, b) === 0 || l(o, v, b);
    const k = () => p || y();
    const w = () => !p || _();
    for (let S = h, P = h; S <= d; ++S) {
        ((x = t[S % a]),
            !x.skip &&
                ((b = c(x[s])),
                b !== v &&
                    ((p = l(b, n, o)),
                    m === null && k() && (m = r(b, n) === 0 ? S : P),
                    m !== null &&
                        w() &&
                        (g.push(
                            Zs({
                                start: m,
                                end: S,
                                loop: u,
                                count: a,
                                style: f,
                            }),
                        ),
                        (m = null)),
                    (P = S),
                    (v = b))));
    }
    return (
        m !== null &&
            g.push(Zs({ start: m, end: d, loop: u, count: a, style: f })),
        g
    );
}
function cs(i, t) {
    const e = [];
    const s = i.segments;
    for (let n = 0; n < s.length; n++) {
        const o = ls(s[n], i.points, t);
        o.length && e.push(...o);
    }
    return e;
}
function fr(i, t, e, s) {
    let n = 0;
    let o = t - 1;
    if (e && !s) for (; n < t && !i[n].skip; ) n++;
    for (; n < t && i[n].skip; ) n++;
    for (n %= t, e && (o += n); o > n && i[o % t].skip; ) o--;
    return ((o %= t), { start: n, end: o });
}
function gr(i, t, e, s) {
    const n = i.length;
    const o = [];
    let a = t;
    let r = i[t];
    let l;
    for (l = t + 1; l <= e; ++l) {
        const c = i[l % n];
        (c.skip || c.stop
            ? r.skip ||
              ((s = !1),
              o.push({ start: t % n, end: (l - 1) % n, loop: s }),
              (t = a = c.stop ? l : null))
            : ((a = l), r.skip && (t = l)),
            (r = c));
    }
    return (a !== null && o.push({ start: t % n, end: a % n, loop: s }), o);
}
function Cn(i, t) {
    const e = i.points;
    const s = i.options.spanGaps;
    const n = e.length;
    if (!n) return [];
    const o = !!i._loop;
    const { start: a, end: r } = fr(e, n, o, s);
    if (s === !0) return Js(i, [{ start: a, end: r, loop: o }], e, t);
    const l = r < a ? r + n : r;
    const c = !!i._fullLoop && a === 0 && r === n - 1;
    return Js(i, gr(e, a, l, c), e, t);
}
function Js(i, t, e, s) {
    return !s || !s.setContext || !e ? t : pr(i, t, e, s);
}
function pr(i, t, e, s) {
    const n = i._chart.getContext();
    const o = Qs(i.options);
    const {
        _datasetIndex: a,
        options: { spanGaps: r },
    } = i;
    const l = e.length;
    const c = [];
    let h = o;
    let d = t[0].start;
    let u = d;
    function f(g, p, m, b) {
        const x = r ? -1 : 1;
        if (g !== p) {
            for (g += l; e[g % l].skip; ) g -= x;
            for (; e[p % l].skip; ) p += x;
            g % l !== p % l &&
                (c.push({ start: g % l, end: p % l, loop: m, style: b }),
                (h = b),
                (d = p % l));
        }
    }
    for (const g of t) {
        d = r ? d : g.start;
        let p = e[d % l];
        let m;
        for (u = d + 1; u <= g.end; u++) {
            const b = e[u % l];
            ((m = Qs(
                s.setContext(
                    yt(n, {
                        type: "segment",
                        p0: p,
                        p1: b,
                        p0DataIndex: (u - 1) % l,
                        p1DataIndex: u % l,
                        datasetIndex: a,
                    }),
                ),
            )),
                mr(m, h) && f(d, u - 1, g.loop, h),
                (p = b),
                (h = m));
        }
        d < u - 1 && f(d, u - 1, g.loop, h);
    }
    return c;
}
function Qs(i) {
    return {
        backgroundColor: i.backgroundColor,
        borderCapStyle: i.borderCapStyle,
        borderDash: i.borderDash,
        borderDashOffset: i.borderDashOffset,
        borderJoinStyle: i.borderJoinStyle,
        borderWidth: i.borderWidth,
        borderColor: i.borderColor,
    };
}
function mr(i, t) {
    if (!t) return !1;
    const e = [];
    const s = function (n, o) {
        return qi(o) ? (e.includes(o) || e.push(o), e.indexOf(o)) : o;
    };
    return JSON.stringify(i, s) !== JSON.stringify(t, s);
}
function Ze(i, t, e) {
    return i.options.clip ? i[e] : t[e];
}
function br(i, t) {
    const { xScale: e, yScale: s } = i;
    return e && s
        ? {
              left: Ze(e, t, "left"),
              right: Ze(e, t, "right"),
              top: Ze(s, t, "top"),
              bottom: Ze(s, t, "bottom"),
          }
        : t;
}
function hs(i, t) {
    const e = t._clip;
    if (e.disabled) return !1;
    const s = br(t, i.chartArea);
    return {
        left: e.left === !1 ? 0 : s.left - (e.left === !0 ? 0 : e.left),
        right:
            e.right === !1 ? i.width : s.right + (e.right === !0 ? 0 : e.right),
        top: e.top === !1 ? 0 : s.top - (e.top === !0 ? 0 : e.top),
        bottom:
            e.bottom === !1
                ? i.height
                : s.bottom + (e.bottom === !0 ? 0 : e.bottom),
    };
}
const Ms = class {
    constructor() {
        ((this._request = null),
            (this._charts = new Map()),
            (this._running = !1),
            (this._lastDate = void 0));
    }

    _notify(t, e, s, n) {
        const o = e.listeners[n];
        const a = e.duration;
        o.forEach((r) =>
            r({
                chart: t,
                initial: e.initial,
                numSteps: a,
                currentStep: Math.min(s - e.start, a),
            }),
        );
    }

    _refresh() {
        this._request ||
            ((this._running = !0),
            (this._request = Yi.call(window, () => {
                (this._update(),
                    (this._request = null),
                    this._running && this._refresh());
            })));
    }

    _update(t = Date.now()) {
        let e = 0;
        (this._charts.forEach((s, n) => {
            if (!s.running || !s.items.length) return;
            const o = s.items;
            let a = o.length - 1;
            let r = !1;
            let l;
            for (; a >= 0; --a) {
                ((l = o[a]),
                    l._active
                        ? (l._total > s.duration && (s.duration = l._total),
                          l.tick(t),
                          (r = !0))
                        : ((o[a] = o[o.length - 1]), o.pop()));
            }
            (r && (n.draw(), this._notify(n, s, t, "progress")),
                o.length ||
                    ((s.running = !1),
                    this._notify(n, s, t, "complete"),
                    (s.initial = !1)),
                (e += o.length));
        }),
            (this._lastDate = t),
            e === 0 && (this._running = !1));
    }

    _getAnims(t) {
        const e = this._charts;
        let s = e.get(t);
        return (
            s ||
                ((s = {
                    running: !1,
                    initial: !0,
                    items: [],
                    listeners: { complete: [], progress: [] },
                }),
                e.set(t, s)),
            s
        );
    }

    listen(t, e, s) {
        this._getAnims(t).listeners[e].push(s);
    }

    add(t, e) {
        !e || !e.length || this._getAnims(t).items.push(...e);
    }

    has(t) {
        return this._getAnims(t).items.length > 0;
    }

    start(t) {
        const e = this._charts.get(t);
        e &&
            ((e.running = !0),
            (e.start = Date.now()),
            (e.duration = e.items.reduce(
                (s, n) => Math.max(s, n._duration),
                0,
            )),
            this._refresh());
    }

    running(t) {
        if (!this._running) return !1;
        const e = this._charts.get(t);
        return !(!e || !e.running || !e.items.length);
    }

    stop(t) {
        const e = this._charts.get(t);
        if (!e || !e.items.length) return;
        const s = e.items;
        let n = s.length - 1;
        for (; n >= 0; --n) s[n].cancel();
        ((e.items = []), this._notify(t, e, Date.now(), "complete"));
    }

    remove(t) {
        return this._charts.delete(t);
    }
};
const vt = new Ms();
const An = "transparent";
const xr = {
    boolean(i, t, e) {
        return e > 0.5 ? t : i;
    },
    color(i, t, e) {
        const s = Gi(i || An);
        const n = s.valid && Gi(t || An);
        return n && n.valid ? n.mix(s, e).hexString() : t;
    },
    number(i, t, e) {
        return i + (t - i) * e;
    },
};
const ks = class {
    constructor(t, e, s, n) {
        const o = e[s];
        n = oe([t.to, n, o, t.from]);
        const a = oe([t.from, o, n]);
        ((this._active = !0),
            (this._fn = t.fn || xr[t.type || typeof a]),
            (this._easing = Gt[t.easing] || Gt.linear),
            (this._start = Math.floor(Date.now() + (t.delay || 0))),
            (this._duration = this._total = Math.floor(t.duration)),
            (this._loop = !!t.loop),
            (this._target = e),
            (this._prop = s),
            (this._from = a),
            (this._to = n),
            (this._promises = void 0));
    }

    active() {
        return this._active;
    }

    update(t, e, s) {
        if (this._active) {
            this._notify(!1);
            const n = this._target[this._prop];
            const o = s - this._start;
            const a = this._duration - o;
            ((this._start = s),
                (this._duration = Math.floor(Math.max(a, t.duration))),
                (this._total += o),
                (this._loop = !!t.loop),
                (this._to = oe([t.to, e, n, t.from])),
                (this._from = oe([t.from, n, e])));
        }
    }

    cancel() {
        this._active &&
            (this.tick(Date.now()), (this._active = !1), this._notify(!1));
    }

    tick(t) {
        const e = t - this._start;
        const s = this._duration;
        const n = this._prop;
        const o = this._from;
        const a = this._loop;
        const r = this._to;
        let l;
        if (((this._active = o !== r && (a || e < s)), !this._active)) {
            ((this._target[n] = r), this._notify(!0));
            return;
        }
        if (e < 0) {
            this._target[n] = o;
            return;
        }
        ((l = (e / s) % 2),
            (l = a && l > 1 ? 2 - l : l),
            (l = this._easing(Math.min(1, Math.max(0, l)))),
            (this._target[n] = this._fn(o, r, l)));
    }

    wait() {
        const t = this._promises || (this._promises = []);
        return new Promise((e, s) => {
            t.push({ res: e, rej: s });
        });
    }

    _notify(t) {
        const e = t ? "res" : "rej";
        const s = this._promises || [];
        for (let n = 0; n < s.length; n++) s[n][e]();
    }
};
const vi = class {
    constructor(t, e) {
        ((this._chart = t), (this._properties = new Map()), this.configure(e));
    }

    configure(t) {
        if (!T(t)) return;
        const e = Object.keys(V.animation);
        const s = this._properties;
        Object.getOwnPropertyNames(t).forEach((n) => {
            const o = t[n];
            if (!T(o)) return;
            const a = {};
            for (const r of e) a[r] = o[r];
            ((z(o.properties) && o.properties) || [n]).forEach((r) => {
                (r === n || !s.has(r)) && s.set(r, a);
            });
        });
    }

    _animateOptions(t, e) {
        const s = e.options;
        const n = yr(t, s);
        if (!n) return [];
        const o = this._createAnimations(n, s);
        return (
            s.$shared &&
                _r(t.options.$animations, s).then(
                    () => {
                        t.options = s;
                    },
                    () => {},
                ),
            o
        );
    }

    _createAnimations(t, e) {
        const s = this._properties;
        const n = [];
        const o = t.$animations || (t.$animations = {});
        const a = Object.keys(e);
        const r = Date.now();
        let l;
        for (l = a.length - 1; l >= 0; --l) {
            const c = a[l];
            if (c.charAt(0) === "$") continue;
            if (c === "options") {
                n.push(...this._animateOptions(t, e));
                continue;
            }
            const h = e[c];
            let d = o[c];
            const u = s.get(c);
            if (d) {
                if (u && d.active()) {
                    d.update(u, h, r);
                    continue;
                } else d.cancel();
            }
            if (!u || !u.duration) {
                t[c] = h;
                continue;
            }
            ((o[c] = d = new ks(u, t, c, h)), n.push(d));
        }
        return n;
    }

    update(t, e) {
        if (this._properties.size === 0) {
            Object.assign(t, e);
            return;
        }
        const s = this._createAnimations(t, e);
        if (s.length) return (vt.add(this._chart, s), !0);
    }
};
function _r(i, t) {
    const e = [];
    const s = Object.keys(t);
    for (let n = 0; n < s.length; n++) {
        const o = i[s[n]];
        o && o.active() && e.push(o.wait());
    }
    return Promise.all(e);
}
function yr(i, t) {
    if (!t) return;
    let e = i.options;
    if (!e) {
        i.options = t;
        return;
    }
    return (
        e.$shared &&
            (i.options = e =
                Object.assign({}, e, { $shared: !1, $animations: {} })),
        e
    );
}
function Tn(i, t) {
    const e = (i && i.options) || {};
    const s = e.reverse;
    const n = e.min === void 0 ? t : 0;
    const o = e.max === void 0 ? t : 0;
    return { start: s ? o : n, end: s ? n : o };
}
function vr(i, t, e) {
    if (e === !1) return !1;
    const s = Tn(i, e);
    const n = Tn(t, e);
    return { top: n.end, right: s.end, bottom: n.start, left: s.start };
}
function Mr(i) {
    let t, e, s, n;
    return (
        T(i)
            ? ((t = i.top), (e = i.right), (s = i.bottom), (n = i.left))
            : (t = e = s = n = i),
        { top: t, right: e, bottom: s, left: n, disabled: i === !1 }
    );
}
function Ao(i, t) {
    const e = [];
    const s = i._getSortedDatasetMetas(t);
    let n;
    let o;
    for (n = 0, o = s.length; n < o; ++n) e.push(s[n].index);
    return e;
}
function Ln(i, t, e, s = {}) {
    const n = i.keys;
    const o = s.mode === "single";
    let a;
    let r;
    let l;
    let c;
    if (t === null) return;
    let h = !1;
    for (a = 0, r = n.length; a < r; ++a) {
        if (((l = +n[a]), l === e)) {
            if (((h = !0), s.all)) continue;
            break;
        }
        ((c = i.values[l]),
            N(c) && (o || t === 0 || lt(t) === lt(c)) && (t += c));
    }
    return !h && !s.all ? 0 : t;
}
function kr(i, t) {
    const { iScale: e, vScale: s } = t;
    const n = e.axis === "x" ? "x" : "y";
    const o = s.axis === "x" ? "x" : "y";
    const a = Object.keys(i);
    const r = new Array(a.length);
    let l;
    let c;
    let h;
    for (l = 0, c = a.length; l < c; ++l) {
        ((h = a[l]), (r[l] = { [n]: h, [o]: i[h] }));
    }
    return r;
}
function ds(i, t) {
    const e = i && i.options.stacked;
    return e || (e === void 0 && t.stack !== void 0);
}
function wr(i, t, e) {
    return `${i.id}.${t.id}.${e.stack || e.type}`;
}
function Sr(i) {
    const { min: t, max: e, minDefined: s, maxDefined: n } = i.getUserBounds();
    return {
        min: s ? t : Number.NEGATIVE_INFINITY,
        max: n ? e : Number.POSITIVE_INFINITY,
    };
}
function Pr(i, t, e) {
    const s = i[t] || (i[t] = {});
    return s[e] || (s[e] = {});
}
function Rn(i, t, e, s) {
    for (const n of t.getMatchingVisibleMetas(s).reverse()) {
        const o = i[n.index];
        if ((e && o > 0) || (!e && o < 0)) return n.index;
    }
    return null;
}
function En(i, t) {
    const { chart: e, _cachedMeta: s } = i;
    const n = e._stacks || (e._stacks = {});
    const { iScale: o, vScale: a, index: r } = s;
    const l = o.axis;
    const c = a.axis;
    const h = wr(o, a, s);
    const d = t.length;
    let u;
    for (let f = 0; f < d; ++f) {
        const g = t[f];
        const { [l]: p, [c]: m } = g;
        const b = g._stacks || (g._stacks = {});
        ((u = b[c] = Pr(n, h, p)),
            (u[r] = m),
            (u._top = Rn(u, a, !0, s.type)),
            (u._bottom = Rn(u, a, !1, s.type)));
        const x = u._visualValues || (u._visualValues = {});
        x[r] = m;
    }
}
function us(i, t) {
    const e = i.scales;
    return Object.keys(e)
        .filter((s) => e[s].axis === t)
        .shift();
}
function Dr(i, t) {
    return yt(i, {
        active: !1,
        dataset: void 0,
        datasetIndex: t,
        index: t,
        mode: "default",
        type: "dataset",
    });
}
function Or(i, t, e) {
    return yt(i, {
        active: !1,
        dataIndex: t,
        parsed: void 0,
        raw: void 0,
        element: e,
        index: t,
        mode: "default",
        type: "data",
    });
}
function De(i, t) {
    const e = i.controller.index;
    const s = i.vScale && i.vScale.axis;
    if (s) {
        t = t || i._parsed;
        for (const n of t) {
            const o = n._stacks;
            if (!o || o[s] === void 0 || o[s][e] === void 0) return;
            (delete o[s][e],
                o[s]._visualValues !== void 0 &&
                    o[s]._visualValues[e] !== void 0 &&
                    delete o[s]._visualValues[e]);
        }
    }
}
const fs = (i) => i === "reset" || i === "none";
const In = (i, t) => (t ? i : Object.assign({}, i));
const Cr = (i, t, e) =>
    i && !t.hidden && t._stacked && { keys: Ao(e, !0), values: null };
const it = class {
    constructor(t, e) {
        ((this.chart = t),
            (this._ctx = t.ctx),
            (this.index = e),
            (this._cachedDataOpts = {}),
            (this._cachedMeta = this.getMeta()),
            (this._type = this._cachedMeta.type),
            (this.options = void 0),
            (this._parsing = !1),
            (this._data = void 0),
            (this._objectData = void 0),
            (this._sharedOptions = void 0),
            (this._drawStart = void 0),
            (this._drawCount = void 0),
            (this.enableOptionSharing = !1),
            (this.supportsDecimation = !1),
            (this.$context = void 0),
            (this._syncList = []),
            (this.datasetElementType = new.target.datasetElementType),
            (this.dataElementType = new.target.dataElementType),
            this.initialize());
    }

    initialize() {
        const t = this._cachedMeta;
        (this.configure(),
            this.linkScales(),
            (t._stacked = ds(t.vScale, t)),
            this.addElements(),
            this.options.fill &&
                !this.chart.isPluginEnabled("filler") &&
                console.warn(
                    "Tried to use the 'fill' option without the 'Filler' plugin enabled. Please import and register the 'Filler' plugin and make sure it is not disabled in the options",
                ));
    }

    updateIndex(t) {
        (this.index !== t && De(this._cachedMeta), (this.index = t));
    }

    linkScales() {
        const t = this.chart;
        const e = this._cachedMeta;
        const s = this.getDataset();
        const n = (d, u, f, g) => (d === "x" ? u : d === "r" ? g : f);
        const o = (e.xAxisID = D(s.xAxisID, us(t, "x")));
        const a = (e.yAxisID = D(s.yAxisID, us(t, "y")));
        const r = (e.rAxisID = D(s.rAxisID, us(t, "r")));
        const l = e.indexAxis;
        const c = (e.iAxisID = n(l, o, a, r));
        const h = (e.vAxisID = n(l, a, o, r));
        ((e.xScale = this.getScaleForId(o)),
            (e.yScale = this.getScaleForId(a)),
            (e.rScale = this.getScaleForId(r)),
            (e.iScale = this.getScaleForId(c)),
            (e.vScale = this.getScaleForId(h)));
    }

    getDataset() {
        return this.chart.data.datasets[this.index];
    }

    getMeta() {
        return this.chart.getDatasetMeta(this.index);
    }

    getScaleForId(t) {
        return this.chart.scales[t];
    }

    _getOtherScale(t) {
        const e = this._cachedMeta;
        return t === e.iScale ? e.vScale : e.iScale;
    }

    reset() {
        this._update("reset");
    }

    _destroy() {
        const t = this._cachedMeta;
        (this._data && ji(this._data, this), t._stacked && De(t));
    }

    _dataCheck() {
        const t = this.getDataset();
        const e = t.data || (t.data = []);
        const s = this._data;
        if (T(e)) {
            const n = this._cachedMeta;
            this._data = kr(e, n);
        } else if (s !== e) {
            if (s) {
                ji(s, this);
                const n = this._cachedMeta;
                (De(n), (n._parsed = []));
            }
            (e && Object.isExtensible(e) && dn(e, this),
                (this._syncList = []),
                (this._data = e));
        }
    }

    addElements() {
        const t = this._cachedMeta;
        (this._dataCheck(),
            this.datasetElementType &&
                (t.dataset = new this.datasetElementType()));
    }

    buildOrUpdateElements(t) {
        const e = this._cachedMeta;
        const s = this.getDataset();
        let n = !1;
        this._dataCheck();
        const o = e._stacked;
        ((e._stacked = ds(e.vScale, e)),
            e.stack !== s.stack && ((n = !0), De(e), (e.stack = s.stack)),
            this._resyncElements(t),
            (n || o !== e._stacked) &&
                (En(this, e._parsed), (e._stacked = ds(e.vScale, e))));
    }

    configure() {
        const t = this.chart.config;
        const e = t.datasetScopeKeys(this._type);
        const s = t.getOptionScopes(this.getDataset(), e, !0);
        ((this.options = t.createResolver(s, this.getContext())),
            (this._parsing = this.options.parsing),
            (this._cachedDataOpts = {}));
    }

    parse(t, e) {
        const { _cachedMeta: s, _data: n } = this;
        const { iScale: o, _stacked: a } = s;
        const r = o.axis;
        let l = t === 0 && e === n.length ? !0 : s._sorted;
        let c = t > 0 && s._parsed[t - 1];
        let h;
        let d;
        let u;
        if (this._parsing === !1) ((s._parsed = n), (s._sorted = !0), (u = n));
        else {
            z(n[t])
                ? (u = this.parseArrayData(s, n, t, e))
                : T(n[t])
                  ? (u = this.parseObjectData(s, n, t, e))
                  : (u = this.parsePrimitiveData(s, n, t, e));
            const f = () => d[r] === null || (c && d[r] < c[r]);
            for (h = 0; h < e; ++h) {
                ((s._parsed[h + t] = d = u[h]),
                    l && (f() && (l = !1), (c = d)));
            }
            s._sorted = l;
        }
        a && En(this, u);
    }

    parsePrimitiveData(t, e, s, n) {
        const { iScale: o, vScale: a } = t;
        const r = o.axis;
        const l = a.axis;
        const c = o.getLabels();
        const h = o === a;
        const d = new Array(n);
        let u;
        let f;
        let g;
        for (u = 0, f = n; u < f; ++u) {
            ((g = u + s),
                (d[u] = { [r]: h || o.parse(c[g], g), [l]: a.parse(e[g], g) }));
        }
        return d;
    }

    parseArrayData(t, e, s, n) {
        const { xScale: o, yScale: a } = t;
        const r = new Array(n);
        let l;
        let c;
        let h;
        let d;
        for (l = 0, c = n; l < c; ++l) {
            ((h = l + s),
                (d = e[h]),
                (r[l] = { x: o.parse(d[0], h), y: a.parse(d[1], h) }));
        }
        return r;
    }

    parseObjectData(t, e, s, n) {
        const { xScale: o, yScale: a } = t;
        const { xAxisKey: r = "x", yAxisKey: l = "y" } = this._parsing;
        const c = new Array(n);
        let h;
        let d;
        let u;
        let f;
        for (h = 0, d = n; h < d; ++h) {
            ((u = h + s),
                (f = e[u]),
                (c[h] = { x: o.parse(_t(f, r), u), y: a.parse(_t(f, l), u) }));
        }
        return c;
    }

    getParsed(t) {
        return this._cachedMeta._parsed[t];
    }

    getDataElement(t) {
        return this._cachedMeta.data[t];
    }

    applyStack(t, e, s) {
        const n = this.chart;
        const o = this._cachedMeta;
        const a = e[t.axis];
        const r = { keys: Ao(n, !0), values: e._stacks[t.axis]._visualValues };
        return Ln(r, a, o.index, { mode: s });
    }

    updateRangeFromParsed(t, e, s, n) {
        const o = s[e.axis];
        let a = o === null ? NaN : o;
        const r = n && s._stacks[e.axis];
        (n && r && ((n.values = r), (a = Ln(n, o, this._cachedMeta.index))),
            (t.min = Math.min(t.min, a)),
            (t.max = Math.max(t.max, a)));
    }

    getMinMax(t, e) {
        const s = this._cachedMeta;
        const n = s._parsed;
        const o = s._sorted && t === s.iScale;
        const a = n.length;
        const r = this._getOtherScale(t);
        const l = Cr(e, s, this.chart);
        const c = {
            min: Number.POSITIVE_INFINITY,
            max: Number.NEGATIVE_INFINITY,
        };
        const { min: h, max: d } = Sr(r);
        let u;
        let f;
        function g() {
            f = n[u];
            const p = f[r.axis];
            return !N(f[t.axis]) || h > p || d < p;
        }
        for (
            u = 0;
            u < a && !(!g() && (this.updateRangeFromParsed(c, t, f, l), o));
            ++u
        );
        if (o) {
            for (u = a - 1; u >= 0; --u) {
                if (!g()) {
                    this.updateRangeFromParsed(c, t, f, l);
                    break;
                }
            }
        }
        return c;
    }

    getAllParsedValues(t) {
        const e = this._cachedMeta._parsed;
        const s = [];
        let n;
        let o;
        let a;
        for (n = 0, o = e.length; n < o; ++n) {
            ((a = e[n][t.axis]), N(a) && s.push(a));
        }
        return s;
    }

    getMaxOverflow() {
        return !1;
    }

    getLabelAndValue(t) {
        const e = this._cachedMeta;
        const s = e.iScale;
        const n = e.vScale;
        const o = this.getParsed(t);
        return {
            label: s ? "" + s.getLabelForValue(o[s.axis]) : "",
            value: n ? "" + n.getLabelForValue(o[n.axis]) : "",
        };
    }

    _update(t) {
        const e = this._cachedMeta;
        (this.update(t || "default"),
            (e._clip = Mr(
                D(
                    this.options.clip,
                    vr(e.xScale, e.yScale, this.getMaxOverflow()),
                ),
            )));
    }

    update(t) {}
    draw() {
        const t = this._ctx;
        const e = this.chart;
        const s = this._cachedMeta;
        const n = s.data || [];
        const o = e.chartArea;
        const a = [];
        const r = this._drawStart || 0;
        const l = this._drawCount || n.length - r;
        const c = this.options.drawActiveElementsOnTop;
        let h;
        for (s.dataset && s.dataset.draw(t, o, r, l), h = r; h < r + l; ++h) {
            const d = n[h];
            d.hidden || (d.active && c ? a.push(d) : d.draw(t, o));
        }
        for (h = 0; h < a.length; ++h) a[h].draw(t, o);
    }

    getStyle(t, e) {
        const s = e ? "active" : "default";
        return t === void 0 && this._cachedMeta.dataset
            ? this.resolveDatasetElementOptions(s)
            : this.resolveDataElementOptions(t || 0, s);
    }

    getContext(t, e, s) {
        const n = this.getDataset();
        let o;
        if (t >= 0 && t < this._cachedMeta.data.length) {
            const a = this._cachedMeta.data[t];
            ((o = a.$context || (a.$context = Or(this.getContext(), t, a))),
                (o.parsed = this.getParsed(t)),
                (o.raw = n.data[t]),
                (o.index = o.dataIndex = t));
        } else {
            ((o =
                this.$context ||
                (this.$context = Dr(this.chart.getContext(), this.index))),
                (o.dataset = n),
                (o.index = o.datasetIndex = this.index));
        }
        return ((o.active = !!e), (o.mode = s), o);
    }

    resolveDatasetElementOptions(t) {
        return this._resolveElementOptions(this.datasetElementType.id, t);
    }

    resolveDataElementOptions(t, e) {
        return this._resolveElementOptions(this.dataElementType.id, e, t);
    }

    _resolveElementOptions(t, e = "default", s) {
        const n = e === "active";
        const o = this._cachedDataOpts;
        const a = t + "-" + e;
        const r = o[a];
        const l = this.enableOptionSharing && te(s);
        if (r) return In(r, l);
        const c = this.chart.config;
        const h = c.datasetElementScopeKeys(this._type, t);
        const d = n ? [`${t}Hover`, "hover", t, ""] : [t, ""];
        const u = c.getOptionScopes(this.getDataset(), h);
        const f = Object.keys(V.elements[t]);
        const g = () => this.getContext(s, n, e);
        const p = c.resolveNamedOptions(u, f, g, d);
        return (
            p.$shared && ((p.$shared = l), (o[a] = Object.freeze(In(p, l)))),
            p
        );
    }

    _resolveAnimations(t, e, s) {
        const n = this.chart;
        const o = this._cachedDataOpts;
        const a = `animation-${e}`;
        const r = o[a];
        if (r) return r;
        let l;
        if (n.options.animation !== !1) {
            const h = this.chart.config;
            const d = h.datasetAnimationScopeKeys(this._type, e);
            const u = h.getOptionScopes(this.getDataset(), d);
            l = h.createResolver(u, this.getContext(t, s, e));
        }
        const c = new vi(n, l && l.animations);
        return (l && l._cacheable && (o[a] = Object.freeze(c)), c);
    }

    getSharedOptions(t) {
        if (t.$shared) {
            return (
                this._sharedOptions ||
                (this._sharedOptions = Object.assign({}, t))
            );
        }
    }

    includeOptions(t, e) {
        return !e || fs(t) || this.chart._animationsDisabled;
    }

    _getSharedOptions(t, e) {
        const s = this.resolveDataElementOptions(t, e);
        const n = this._sharedOptions;
        const o = this.getSharedOptions(s);
        const a = this.includeOptions(e, o) || o !== n;
        return (
            this.updateSharedOptions(o, e, s),
            { sharedOptions: o, includeOptions: a }
        );
    }

    updateElement(t, e, s, n) {
        fs(n)
            ? Object.assign(t, s)
            : this._resolveAnimations(e, n).update(t, s);
    }

    updateSharedOptions(t, e, s) {
        t && !fs(e) && this._resolveAnimations(void 0, e).update(t, s);
    }

    _setStyle(t, e, s, n) {
        t.active = n;
        const o = this.getStyle(e, n);
        this._resolveAnimations(e, s, n).update(t, {
            options: (!n && this.getSharedOptions(o)) || o,
        });
    }

    removeHoverStyle(t, e, s) {
        this._setStyle(t, s, "active", !1);
    }

    setHoverStyle(t, e, s) {
        this._setStyle(t, s, "active", !0);
    }

    _removeDatasetHoverStyle() {
        const t = this._cachedMeta.dataset;
        t && this._setStyle(t, void 0, "active", !1);
    }

    _setDatasetHoverStyle() {
        const t = this._cachedMeta.dataset;
        t && this._setStyle(t, void 0, "active", !0);
    }

    _resyncElements(t) {
        const e = this._data;
        const s = this._cachedMeta.data;
        for (const [r, l, c] of this._syncList) this[r](l, c);
        this._syncList = [];
        const n = s.length;
        const o = e.length;
        const a = Math.min(o, n);
        (a && this.parse(0, a),
            o > n
                ? this._insertElements(n, o - n, t)
                : o < n && this._removeElements(o, n - o));
    }

    _insertElements(t, e, s = !0) {
        const n = this._cachedMeta;
        const o = n.data;
        const a = t + e;
        let r;
        const l = (c) => {
            for (c.length += e, r = c.length - 1; r >= a; r--) c[r] = c[r - e];
        };
        for (l(o), r = t; r < a; ++r) o[r] = new this.dataElementType();
        (this._parsing && l(n._parsed),
            this.parse(t, e),
            s && this.updateElements(o, t, e, "reset"));
    }

    updateElements(t, e, s, n) {}
    _removeElements(t, e) {
        const s = this._cachedMeta;
        if (this._parsing) {
            const n = s._parsed.splice(t, e);
            s._stacked && De(s, n);
        }
        s.data.splice(t, e);
    }

    _sync(t) {
        if (this._parsing) this._syncList.push(t);
        else {
            const [e, s, n] = t;
            this[e](s, n);
        }
        this.chart._dataChanges.push([this.index, ...t]);
    }

    _onDataPush() {
        const t = arguments.length;
        this._sync(["_insertElements", this.getDataset().data.length - t, t]);
    }

    _onDataPop() {
        this._sync(["_removeElements", this._cachedMeta.data.length - 1, 1]);
    }

    _onDataShift() {
        this._sync(["_removeElements", 0, 1]);
    }

    _onDataSplice(t, e) {
        e && this._sync(["_removeElements", t, e]);
        const s = arguments.length - 2;
        s && this._sync(["_insertElements", t, s]);
    }

    _onDataUnshift() {
        this._sync(["_insertElements", 0, arguments.length]);
    }
};
(M(it, "defaults", {}),
    M(it, "datasetElementType", null),
    M(it, "dataElementType", null));
function Ar(i, t) {
    if (!i._cache.$bar) {
        const e = i.getMatchingVisibleMetas(t);
        let s = [];
        for (let n = 0, o = e.length; n < o; n++) {
            s = s.concat(e[n].controller.getAllParsedValues(i));
        }
        i._cache.$bar = $i(s.sort((n, o) => n - o));
    }
    return i._cache.$bar;
}
function Tr(i) {
    const t = i.iScale;
    const e = Ar(t, i.type);
    let s = t._length;
    let n;
    let o;
    let a;
    let r;
    const l = () => {
        a === 32767 ||
            a === -32768 ||
            (te(r) && (s = Math.min(s, Math.abs(a - r) || s)), (r = a));
    };
    for (n = 0, o = e.length; n < o; ++n) ((a = t.getPixelForValue(e[n])), l());
    for (r = void 0, n = 0, o = t.ticks.length; n < o; ++n) {
        ((a = t.getPixelForTick(n)), l());
    }
    return s;
}
function Lr(i, t, e, s) {
    const n = e.barThickness;
    let o;
    let a;
    return (
        A(n)
            ? ((o = t.min * e.categoryPercentage), (a = e.barPercentage))
            : ((o = n * s), (a = 1)),
        { chunk: o / s, ratio: a, start: t.pixels[i] - o / 2 }
    );
}
function Rr(i, t, e, s) {
    const n = t.pixels;
    const o = n[i];
    let a = i > 0 ? n[i - 1] : null;
    let r = i < n.length - 1 ? n[i + 1] : null;
    const l = e.categoryPercentage;
    (a === null && (a = o - (r === null ? t.end - t.start : r - o)),
        r === null && (r = o + o - a));
    const c = o - ((o - Math.min(a, r)) / 2) * l;
    return {
        chunk: ((Math.abs(r - a) / 2) * l) / s,
        ratio: e.barPercentage,
        start: c,
    };
}
function Er(i, t, e, s) {
    const n = e.parse(i[0], s);
    const o = e.parse(i[1], s);
    const a = Math.min(n, o);
    const r = Math.max(n, o);
    let l = a;
    let c = r;
    (Math.abs(a) > Math.abs(r) && ((l = r), (c = a)),
        (t[e.axis] = c),
        (t._custom = {
            barStart: l,
            barEnd: c,
            start: n,
            end: o,
            min: a,
            max: r,
        }));
}
function To(i, t, e, s) {
    return (z(i) ? Er(i, t, e, s) : (t[e.axis] = e.parse(i, s)), t);
}
function Fn(i, t, e, s) {
    const n = i.iScale;
    const o = i.vScale;
    const a = n.getLabels();
    const r = n === o;
    const l = [];
    let c;
    let h;
    let d;
    let u;
    for (c = e, h = e + s; c < h; ++c) {
        ((u = t[c]),
            (d = {}),
            (d[n.axis] = r || n.parse(a[c], c)),
            l.push(To(u, d, o, c)));
    }
    return l;
}
function gs(i) {
    return i && i.barStart !== void 0 && i.barEnd !== void 0;
}
function Ir(i, t, e) {
    return i !== 0
        ? lt(i)
        : (t.isHorizontal() ? 1 : -1) * (t.min >= e ? 1 : -1);
}
function Fr(i) {
    let t, e, s, n, o;
    return (
        i.horizontal
            ? ((t = i.base > i.x), (e = "left"), (s = "right"))
            : ((t = i.base < i.y), (e = "bottom"), (s = "top")),
        t ? ((n = "end"), (o = "start")) : ((n = "start"), (o = "end")),
        { start: e, end: s, reverse: t, top: n, bottom: o }
    );
}
function zr(i, t, e, s) {
    let n = t.borderSkipped;
    const o = {};
    if (!n) {
        i.borderSkipped = o;
        return;
    }
    if (n === !0) {
        i.borderSkipped = { top: !0, right: !0, bottom: !0, left: !0 };
        return;
    }
    const { start: a, end: r, reverse: l, top: c, bottom: h } = Fr(i);
    (n === "middle" &&
        e &&
        ((i.enableBorderRadius = !0),
        (e._top || 0) === s
            ? (n = c)
            : (e._bottom || 0) === s
              ? (n = h)
              : ((o[zn(h, a, r, l)] = !0), (n = c))),
        (o[zn(n, a, r, l)] = !0),
        (i.borderSkipped = o));
}
function zn(i, t, e, s) {
    return (s ? ((i = Br(i, t, e)), (i = Bn(i, e, t))) : (i = Bn(i, t, e)), i);
}
function Br(i, t, e) {
    return i === t ? e : i === e ? t : i;
}
function Bn(i, t, e) {
    return i === "start" ? t : i === "end" ? e : i;
}
function Vr(i, { inflateAmount: t }, e) {
    i.inflateAmount = t === "auto" ? (e === 1 ? 0.33 : 0) : t;
}
const re = class extends it {
    parsePrimitiveData(t, e, s, n) {
        return Fn(t, e, s, n);
    }

    parseArrayData(t, e, s, n) {
        return Fn(t, e, s, n);
    }

    parseObjectData(t, e, s, n) {
        const { iScale: o, vScale: a } = t;
        const { xAxisKey: r = "x", yAxisKey: l = "y" } = this._parsing;
        const c = o.axis === "x" ? r : l;
        const h = a.axis === "x" ? r : l;
        const d = [];
        let u;
        let f;
        let g;
        let p;
        for (u = s, f = s + n; u < f; ++u) {
            ((p = e[u]),
                (g = {}),
                (g[o.axis] = o.parse(_t(p, c), u)),
                d.push(To(_t(p, h), g, a, u)));
        }
        return d;
    }

    updateRangeFromParsed(t, e, s, n) {
        super.updateRangeFromParsed(t, e, s, n);
        const o = s._custom;
        o &&
            e === this._cachedMeta.vScale &&
            ((t.min = Math.min(t.min, o.min)),
            (t.max = Math.max(t.max, o.max)));
    }

    getMaxOverflow() {
        return 0;
    }

    getLabelAndValue(t) {
        const e = this._cachedMeta;
        const { iScale: s, vScale: n } = e;
        const o = this.getParsed(t);
        const a = o._custom;
        const r = gs(a)
            ? "[" + a.start + ", " + a.end + "]"
            : "" + n.getLabelForValue(o[n.axis]);
        return { label: "" + s.getLabelForValue(o[s.axis]), value: r };
    }

    initialize() {
        ((this.enableOptionSharing = !0), super.initialize());
        const t = this._cachedMeta;
        t.stack = this.getDataset().stack;
    }

    update(t) {
        const e = this._cachedMeta;
        this.updateElements(e.data, 0, e.data.length, t);
    }

    updateElements(t, e, s, n) {
        const o = n === "reset";
        const {
            index: a,
            _cachedMeta: { vScale: r },
        } = this;
        const l = r.getBasePixel();
        const c = r.isHorizontal();
        const h = this._getRuler();
        const { sharedOptions: d, includeOptions: u } = this._getSharedOptions(
            e,
            n,
        );
        for (let f = e; f < e + s; f++) {
            const g = this.getParsed(f);
            const p =
                o || A(g[r.axis])
                    ? { base: l, head: l }
                    : this._calculateBarValuePixels(f);
            const m = this._calculateBarIndexPixels(f, h);
            const b = (g._stacks || {})[r.axis];
            const x = {
                horizontal: c,
                base: p.base,
                enableBorderRadius:
                    !b || gs(g._custom) || a === b._top || a === b._bottom,
                x: c ? p.head : m.center,
                y: c ? m.center : p.head,
                height: c ? m.size : Math.abs(p.size),
                width: c ? Math.abs(p.size) : m.size,
            };
            u &&
                (x.options =
                    d ||
                    this.resolveDataElementOptions(
                        f,
                        t[f].active ? "active" : n,
                    ));
            const v = x.options || t[f].options;
            (zr(x, v, b, a),
                Vr(x, v, h.ratio),
                this.updateElement(t[f], f, x, n));
        }
    }

    _getStacks(t, e) {
        const { iScale: s } = this._cachedMeta;
        const n = s
            .getMatchingVisibleMetas(this._type)
            .filter((h) => h.controller.options.grouped);
        const o = s.options.stacked;
        const a = [];
        const r = this._cachedMeta.controller.getParsed(e);
        const l = r && r[s.axis];
        const c = (h) => {
            const d = h._parsed.find((f) => f[s.axis] === l);
            const u = d && d[h.vScale.axis];
            if (A(u) || isNaN(u)) return !0;
        };
        for (const h of n) {
            if (
                !(e !== void 0 && c(h)) &&
                ((o === !1 ||
                    a.indexOf(h.stack) === -1 ||
                    (o === void 0 && h.stack === void 0)) &&
                    a.push(h.stack),
                h.index === t)
            ) {
                break;
            }
        }
        return (a.length || a.push(void 0), a);
    }

    _getStackCount(t) {
        return this._getStacks(void 0, t).length;
    }

    _getAxisCount() {
        return this._getAxis().length;
    }

    getFirstScaleIdForIndexAxis() {
        const t = this.chart.scales;
        const e = this.chart.options.indexAxis;
        return Object.keys(t)
            .filter((s) => t[s].axis === e)
            .shift();
    }

    _getAxis() {
        const t = {};
        const e = this.getFirstScaleIdForIndexAxis();
        for (const s of this.chart.data.datasets) {
            t[
                D(
                    this.chart.options.indexAxis === "x"
                        ? s.xAxisID
                        : s.yAxisID,
                    e,
                )
            ] = !0;
        }
        return Object.keys(t);
    }

    _getStackIndex(t, e, s) {
        const n = this._getStacks(t, s);
        const o = e !== void 0 ? n.indexOf(e) : -1;
        return o === -1 ? n.length - 1 : o;
    }

    _getRuler() {
        const t = this.options;
        const e = this._cachedMeta;
        const s = e.iScale;
        const n = [];
        let o;
        let a;
        for (o = 0, a = e.data.length; o < a; ++o) {
            n.push(s.getPixelForValue(this.getParsed(o)[s.axis], o));
        }
        const r = t.barThickness;
        return {
            min: r || Tr(e),
            pixels: n,
            start: s._startPixel,
            end: s._endPixel,
            stackCount: this._getStackCount(),
            scale: s,
            grouped: t.grouped,
            ratio: r ? 1 : t.categoryPercentage * t.barPercentage,
        };
    }

    _calculateBarValuePixels(t) {
        const {
            _cachedMeta: { vScale: e, _stacked: s, index: n },
            options: { base: o, minBarLength: a },
        } = this;
        const r = o || 0;
        const l = this.getParsed(t);
        const c = l._custom;
        const h = gs(c);
        let d = l[e.axis];
        let u = 0;
        let f = s ? this.applyStack(e, l, s) : d;
        let g;
        let p;
        (f !== d && ((u = f - d), (f = d)),
            h &&
                ((d = c.barStart),
                (f = c.barEnd - c.barStart),
                d !== 0 && lt(d) !== lt(c.barEnd) && (u = 0),
                (u += d)));
        const m = !A(o) && !h ? o : u;
        let b = e.getPixelForValue(m);
        if (
            (this.chart.getDataVisibility(t)
                ? (g = e.getPixelForValue(u + f))
                : (g = b),
            (p = g - b),
            Math.abs(p) < a)
        ) {
            ((p = Ir(p, e, r) * a), d === r && (b -= p / 2));
            const x = e.getPixelForDecimal(0);
            const v = e.getPixelForDecimal(1);
            const y = Math.min(x, v);
            const _ = Math.max(x, v);
            ((b = Math.max(Math.min(b, _), y)),
                (g = b + p),
                s &&
                    !h &&
                    (l._stacks[e.axis]._visualValues[n] =
                        e.getValueForPixel(g) - e.getValueForPixel(b)));
        }
        if (b === e.getPixelForValue(r)) {
            const x = (lt(p) * e.getLineWidthForValue(r)) / 2;
            ((b += x), (p -= x));
        }
        return { size: p, base: b, head: g, center: g + p / 2 };
    }

    _calculateBarIndexPixels(t, e) {
        const s = e.scale;
        const n = this.options;
        const o = n.skipNull;
        const a = D(n.maxBarThickness, 1 / 0);
        let r;
        let l;
        const c = this._getAxisCount();
        if (e.grouped) {
            const h = o ? this._getStackCount(t) : e.stackCount;
            const d =
                n.barThickness === "flex"
                    ? Rr(t, e, n, h * c)
                    : Lr(t, e, n, h * c);
            const u =
                this.chart.options.indexAxis === "x"
                    ? this.getDataset().xAxisID
                    : this.getDataset().yAxisID;
            const f = this._getAxis().indexOf(
                D(u, this.getFirstScaleIdForIndexAxis()),
            );
            const g =
                this._getStackIndex(
                    this.index,
                    this._cachedMeta.stack,
                    o ? t : void 0,
                ) + f;
            ((r = d.start + d.chunk * g + d.chunk / 2),
                (l = Math.min(a, d.chunk * d.ratio)));
        } else {
            ((r = s.getPixelForValue(this.getParsed(t)[s.axis], t)),
                (l = Math.min(a, e.min * e.ratio)));
        }
        return { base: r - l / 2, head: r + l / 2, center: r, size: l };
    }

    draw() {
        const t = this._cachedMeta;
        const e = t.vScale;
        const s = t.data;
        const n = s.length;
        let o = 0;
        for (; o < n; ++o) {
            this.getParsed(o)[e.axis] !== null &&
                !s[o].hidden &&
                s[o].draw(this._ctx);
        }
    }
};
(M(re, "id", "bar"),
    M(re, "defaults", {
        datasetElementType: !1,
        dataElementType: "bar",
        categoryPercentage: 0.8,
        barPercentage: 0.9,
        grouped: !0,
        animations: {
            numbers: {
                type: "number",
                properties: ["x", "y", "base", "width", "height"],
            },
        },
    }),
    M(re, "overrides", {
        scales: {
            _index_: { type: "category", offset: !0, grid: { offset: !0 } },
            _value_: { type: "linear", beginAtZero: !0 },
        },
    }));
const le = class extends it {
    initialize() {
        ((this.enableOptionSharing = !0), super.initialize());
    }

    parsePrimitiveData(t, e, s, n) {
        const o = super.parsePrimitiveData(t, e, s, n);
        for (let a = 0; a < o.length; a++) {
            o[a]._custom = this.resolveDataElementOptions(a + s).radius;
        }
        return o;
    }

    parseArrayData(t, e, s, n) {
        const o = super.parseArrayData(t, e, s, n);
        for (let a = 0; a < o.length; a++) {
            const r = e[s + a];
            o[a]._custom = D(
                r[2],
                this.resolveDataElementOptions(a + s).radius,
            );
        }
        return o;
    }

    parseObjectData(t, e, s, n) {
        const o = super.parseObjectData(t, e, s, n);
        for (let a = 0; a < o.length; a++) {
            const r = e[s + a];
            o[a]._custom = D(
                r && r.r && +r.r,
                this.resolveDataElementOptions(a + s).radius,
            );
        }
        return o;
    }

    getMaxOverflow() {
        const t = this._cachedMeta.data;
        let e = 0;
        for (let s = t.length - 1; s >= 0; --s) {
            e = Math.max(e, t[s].size(this.resolveDataElementOptions(s)) / 2);
        }
        return e > 0 && e;
    }

    getLabelAndValue(t) {
        const e = this._cachedMeta;
        const s = this.chart.data.labels || [];
        const { xScale: n, yScale: o } = e;
        const a = this.getParsed(t);
        const r = n.getLabelForValue(a.x);
        const l = o.getLabelForValue(a.y);
        const c = a._custom;
        return {
            label: s[t] || "",
            value: "(" + r + ", " + l + (c ? ", " + c : "") + ")",
        };
    }

    update(t) {
        const e = this._cachedMeta.data;
        this.updateElements(e, 0, e.length, t);
    }

    updateElements(t, e, s, n) {
        const o = n === "reset";
        const { iScale: a, vScale: r } = this._cachedMeta;
        const { sharedOptions: l, includeOptions: c } = this._getSharedOptions(
            e,
            n,
        );
        const h = a.axis;
        const d = r.axis;
        for (let u = e; u < e + s; u++) {
            const f = t[u];
            const g = !o && this.getParsed(u);
            const p = {};
            const m = (p[h] = o
                ? a.getPixelForDecimal(0.5)
                : a.getPixelForValue(g[h]));
            const b = (p[d] = o ? r.getBasePixel() : r.getPixelForValue(g[d]));
            ((p.skip = isNaN(m) || isNaN(b)),
                c &&
                    ((p.options =
                        l ||
                        this.resolveDataElementOptions(
                            u,
                            f.active ? "active" : n,
                        )),
                    o && (p.options.radius = 0)),
                this.updateElement(f, u, p, n));
        }
    }

    resolveDataElementOptions(t, e) {
        const s = this.getParsed(t);
        let n = super.resolveDataElementOptions(t, e);
        n.$shared && (n = Object.assign({}, n, { $shared: !1 }));
        const o = n.radius;
        return (
            e !== "active" && (n.radius = 0),
            (n.radius += D(s && s._custom, o)),
            n
        );
    }
};
(M(le, "id", "bubble"),
    M(le, "defaults", {
        datasetElementType: !1,
        dataElementType: "point",
        animations: {
            numbers: {
                type: "number",
                properties: ["x", "y", "borderWidth", "radius"],
            },
        },
    }),
    M(le, "overrides", {
        scales: { x: { type: "linear" }, y: { type: "linear" } },
    }));
function Wr(i, t, e) {
    let s = 1;
    let n = 1;
    let o = 0;
    let a = 0;
    if (t < B) {
        const r = i;
        const l = r + t;
        const c = Math.cos(r);
        const h = Math.sin(r);
        const d = Math.cos(l);
        const u = Math.sin(l);
        const f = (v, y, _) =>
            ie(v, r, l, !0) ? 1 : Math.max(y, y * e, _, _ * e);
        const g = (v, y, _) =>
            ie(v, r, l, !0) ? -1 : Math.min(y, y * e, _, _ * e);
        const p = f(0, c, d);
        const m = f(H, h, u);
        const b = g(R, c, d);
        const x = g(R + H, h, u);
        ((s = (p - b) / 2),
            (n = (m - x) / 2),
            (o = -(p + b) / 2),
            (a = -(m + x) / 2));
    }
    return { ratioX: s, ratioY: n, offsetX: o, offsetY: a };
}
const kt = class extends it {
    constructor(t, e) {
        (super(t, e),
            (this.enableOptionSharing = !0),
            (this.innerRadius = void 0),
            (this.outerRadius = void 0),
            (this.offsetX = void 0),
            (this.offsetY = void 0));
    }

    linkScales() {}
    parse(t, e) {
        const s = this.getDataset().data;
        const n = this._cachedMeta;
        if (this._parsing === !1) n._parsed = s;
        else {
            let o = (l) => +s[l];
            if (T(s[t])) {
                const { key: l = "value" } = this._parsing;
                o = (c) => +_t(s[c], l);
            }
            let a, r;
            for (a = t, r = t + e; a < r; ++a) n._parsed[a] = o(a);
        }
    }

    _getRotation() {
        return ot(this.options.rotation - 90);
    }

    _getCircumference() {
        return ot(this.options.circumference);
    }

    _getRotationExtents() {
        let t = B;
        let e = -B;
        for (let s = 0; s < this.chart.data.datasets.length; ++s) {
            if (
                this.chart.isDatasetVisible(s) &&
                this.chart.getDatasetMeta(s).type === this._type
            ) {
                const n = this.chart.getDatasetMeta(s).controller;
                const o = n._getRotation();
                const a = n._getCircumference();
                ((t = Math.min(t, o)), (e = Math.max(e, o + a)));
            }
        }
        return { rotation: t, circumference: e - t };
    }

    update(t) {
        const e = this.chart;
        const { chartArea: s } = e;
        const n = this._cachedMeta;
        const o = n.data;
        const a =
            this.getMaxBorderWidth() +
            this.getMaxOffset(o) +
            this.options.spacing;
        const r = Math.max((Math.min(s.width, s.height) - a) / 2, 0);
        const l = Math.min(en(this.options.cutout, r), 1);
        const c = this._getRingWeight(this.index);
        const { circumference: h, rotation: d } = this._getRotationExtents();
        const { ratioX: u, ratioY: f, offsetX: g, offsetY: p } = Wr(d, h, l);
        const m = (s.width - a) / u;
        const b = (s.height - a) / f;
        const x = Math.max(Math.min(m, b) / 2, 0);
        const v = zi(this.options.radius, x);
        const y = Math.max(v * l, 0);
        const _ = (v - y) / this._getVisibleDatasetWeightTotal();
        ((this.offsetX = g * v),
            (this.offsetY = p * v),
            (n.total = this.calculateTotal()),
            (this.outerRadius = v - _ * this._getRingWeightOffset(this.index)),
            (this.innerRadius = Math.max(this.outerRadius - _ * c, 0)),
            this.updateElements(o, 0, o.length, t));
    }

    _circumference(t, e) {
        const s = this.options;
        const n = this._cachedMeta;
        const o = this._getCircumference();
        return (e && s.animation.animateRotate) ||
            !this.chart.getDataVisibility(t) ||
            n._parsed[t] === null ||
            n.data[t].hidden
            ? 0
            : this.calculateCircumference((n._parsed[t] * o) / B);
    }

    updateElements(t, e, s, n) {
        const o = n === "reset";
        const a = this.chart;
        const r = a.chartArea;
        const c = a.options.animation;
        const h = (r.left + r.right) / 2;
        const d = (r.top + r.bottom) / 2;
        const u = o && c.animateScale;
        const f = u ? 0 : this.innerRadius;
        const g = u ? 0 : this.outerRadius;
        const { sharedOptions: p, includeOptions: m } = this._getSharedOptions(
            e,
            n,
        );
        let b = this._getRotation();
        let x;
        for (x = 0; x < e; ++x) b += this._circumference(x, o);
        for (x = e; x < e + s; ++x) {
            const v = this._circumference(x, o);
            const y = t[x];
            const _ = {
                x: h + this.offsetX,
                y: d + this.offsetY,
                startAngle: b,
                endAngle: b + v,
                circumference: v,
                outerRadius: g,
                innerRadius: f,
            };
            (m &&
                (_.options =
                    p ||
                    this.resolveDataElementOptions(x, y.active ? "active" : n)),
                (b += v),
                this.updateElement(y, x, _, n));
        }
    }

    calculateTotal() {
        const t = this._cachedMeta;
        const e = t.data;
        let s = 0;
        let n;
        for (n = 0; n < e.length; n++) {
            const o = t._parsed[n];
            o !== null &&
                !isNaN(o) &&
                this.chart.getDataVisibility(n) &&
                !e[n].hidden &&
                (s += Math.abs(o));
        }
        return s;
    }

    calculateCircumference(t) {
        const e = this._cachedMeta.total;
        return e > 0 && !isNaN(t) ? B * (Math.abs(t) / e) : 0;
    }

    getLabelAndValue(t) {
        const e = this._cachedMeta;
        const s = this.chart;
        const n = s.data.labels || [];
        const o = se(e._parsed[t], s.options.locale);
        return { label: n[t] || "", value: o };
    }

    getMaxBorderWidth(t) {
        let e = 0;
        const s = this.chart;
        let n;
        let o;
        let a;
        let r;
        let l;
        if (!t) {
            for (n = 0, o = s.data.datasets.length; n < o; ++n) {
                if (s.isDatasetVisible(n)) {
                    ((a = s.getDatasetMeta(n)),
                        (t = a.data),
                        (r = a.controller));
                    break;
                }
            }
        }
        if (!t) return 0;
        for (n = 0, o = t.length; n < o; ++n) {
            ((l = r.resolveDataElementOptions(n)),
                l.borderAlign !== "inner" &&
                    (e = Math.max(
                        e,
                        l.borderWidth || 0,
                        l.hoverBorderWidth || 0,
                    )));
        }
        return e;
    }

    getMaxOffset(t) {
        let e = 0;
        for (let s = 0, n = t.length; s < n; ++s) {
            const o = this.resolveDataElementOptions(s);
            e = Math.max(e, o.offset || 0, o.hoverOffset || 0);
        }
        return e;
    }

    _getRingWeightOffset(t) {
        let e = 0;
        for (let s = 0; s < t; ++s) {
            this.chart.isDatasetVisible(s) && (e += this._getRingWeight(s));
        }
        return e;
    }

    _getRingWeight(t) {
        return Math.max(D(this.chart.data.datasets[t].weight, 1), 0);
    }

    _getVisibleDatasetWeightTotal() {
        return this._getRingWeightOffset(this.chart.data.datasets.length) || 1;
    }
};
(M(kt, "id", "doughnut"),
    M(kt, "defaults", {
        datasetElementType: !1,
        dataElementType: "arc",
        animation: { animateRotate: !0, animateScale: !1 },
        animations: {
            numbers: {
                type: "number",
                properties: [
                    "circumference",
                    "endAngle",
                    "innerRadius",
                    "outerRadius",
                    "startAngle",
                    "x",
                    "y",
                    "offset",
                    "borderWidth",
                    "spacing",
                ],
            },
        },
        cutout: "50%",
        rotation: 0,
        circumference: 360,
        radius: "100%",
        spacing: 0,
        indexAxis: "r",
    }),
    M(kt, "descriptors", {
        _scriptable: (t) => t !== "spacing",
        _indexable: (t) =>
            t !== "spacing" &&
            !t.startsWith("borderDash") &&
            !t.startsWith("hoverBorderDash"),
    }),
    M(kt, "overrides", {
        aspectRatio: 1,
        plugins: {
            legend: {
                labels: {
                    generateLabels(t) {
                        const e = t.data;
                        if (e.labels.length && e.datasets.length) {
                            const {
                                labels: { pointStyle: s, color: n },
                            } = t.legend.options;
                            return e.labels.map((o, a) => {
                                const l = t
                                    .getDatasetMeta(0)
                                    .controller.getStyle(a);
                                return {
                                    text: o,
                                    fillStyle: l.backgroundColor,
                                    strokeStyle: l.borderColor,
                                    fontColor: n,
                                    lineWidth: l.borderWidth,
                                    pointStyle: s,
                                    hidden: !t.getDataVisibility(a),
                                    index: a,
                                };
                            });
                        }
                        return [];
                    },
                },
                onClick(t, e, s) {
                    (s.chart.toggleDataVisibility(e.index), s.chart.update());
                },
            },
        },
    }));
const ce = class extends it {
    initialize() {
        ((this.enableOptionSharing = !0),
            (this.supportsDecimation = !0),
            super.initialize());
    }

    update(t) {
        const e = this._cachedMeta;
        const { dataset: s, data: n = [], _dataset: o } = e;
        const a = this.chart._animationsDisabled;
        let { start: r, count: l } = Xi(e, n, a);
        ((this._drawStart = r),
            (this._drawCount = l),
            Ki(e) && ((r = 0), (l = n.length)),
            (s._chart = this.chart),
            (s._datasetIndex = this.index),
            (s._decimated = !!o._decimated),
            (s.points = n));
        const c = this.resolveDatasetElementOptions(t);
        (this.options.showLine || (c.borderWidth = 0),
            (c.segment = this.options.segment),
            this.updateElement(s, void 0, { animated: !a, options: c }, t),
            this.updateElements(n, r, l, t));
    }

    updateElements(t, e, s, n) {
        const o = n === "reset";
        const {
            iScale: a,
            vScale: r,
            _stacked: l,
            _dataset: c,
        } = this._cachedMeta;
        const { sharedOptions: h, includeOptions: d } = this._getSharedOptions(
            e,
            n,
        );
        const u = a.axis;
        const f = r.axis;
        const { spanGaps: g, segment: p } = this.options;
        const m = Bt(g) ? g : Number.POSITIVE_INFINITY;
        const b = this.chart._animationsDisabled || o || n === "none";
        const x = e + s;
        const v = t.length;
        let y = e > 0 && this.getParsed(e - 1);
        for (let _ = 0; _ < v; ++_) {
            const k = t[_];
            const w = b ? k : {};
            if (_ < e || _ >= x) {
                w.skip = !0;
                continue;
            }
            const S = this.getParsed(_);
            const P = A(S[f]);
            const O = (w[u] = a.getPixelForValue(S[u], _));
            const C = (w[f] =
                o || P
                    ? r.getBasePixel()
                    : r.getPixelForValue(
                          l ? this.applyStack(r, S, l) : S[f],
                          _,
                      ));
            ((w.skip = isNaN(O) || isNaN(C) || P),
                (w.stop = _ > 0 && Math.abs(S[u] - y[u]) > m),
                p && ((w.parsed = S), (w.raw = c.data[_])),
                d &&
                    (w.options =
                        h ||
                        this.resolveDataElementOptions(
                            _,
                            k.active ? "active" : n,
                        )),
                b || this.updateElement(k, _, w, n),
                (y = S));
        }
    }

    getMaxOverflow() {
        const t = this._cachedMeta;
        const e = t.dataset;
        const s = (e.options && e.options.borderWidth) || 0;
        const n = t.data || [];
        if (!n.length) return s;
        const o = n[0].size(this.resolveDataElementOptions(0));
        const a = n[n.length - 1].size(
            this.resolveDataElementOptions(n.length - 1),
        );
        return Math.max(s, o, a) / 2;
    }

    draw() {
        const t = this._cachedMeta;
        (t.dataset.updateControlPoints(this.chart.chartArea, t.iScale.axis),
            super.draw());
    }
};
(M(ce, "id", "line"),
    M(ce, "defaults", {
        datasetElementType: "line",
        dataElementType: "point",
        showLine: !0,
        spanGaps: !1,
    }),
    M(ce, "overrides", {
        scales: { _index_: { type: "category" }, _value_: { type: "linear" } },
    }));
const $t = class extends it {
    constructor(t, e) {
        (super(t, e), (this.innerRadius = void 0), (this.outerRadius = void 0));
    }

    getLabelAndValue(t) {
        const e = this._cachedMeta;
        const s = this.chart;
        const n = s.data.labels || [];
        const o = se(e._parsed[t].r, s.options.locale);
        return { label: n[t] || "", value: o };
    }

    parseObjectData(t, e, s, n) {
        return ss.bind(this)(t, e, s, n);
    }

    update(t) {
        const e = this._cachedMeta.data;
        (this._updateRadius(), this.updateElements(e, 0, e.length, t));
    }

    getMinMax() {
        const t = this._cachedMeta;
        const e = {
            min: Number.POSITIVE_INFINITY,
            max: Number.NEGATIVE_INFINITY,
        };
        return (
            t.data.forEach((s, n) => {
                const o = this.getParsed(n).r;
                !isNaN(o) &&
                    this.chart.getDataVisibility(n) &&
                    (o < e.min && (e.min = o), o > e.max && (e.max = o));
            }),
            e
        );
    }

    _updateRadius() {
        const t = this.chart;
        const e = t.chartArea;
        const s = t.options;
        const n = Math.min(e.right - e.left, e.bottom - e.top);
        const o = Math.max(n / 2, 0);
        const a = Math.max(
            s.cutoutPercentage ? (o / 100) * s.cutoutPercentage : 1,
            0,
        );
        const r = (o - a) / t.getVisibleDatasetCount();
        ((this.outerRadius = o - r * this.index),
            (this.innerRadius = this.outerRadius - r));
    }

    updateElements(t, e, s, n) {
        const o = n === "reset";
        const a = this.chart;
        const l = a.options.animation;
        const c = this._cachedMeta.rScale;
        const h = c.xCenter;
        const d = c.yCenter;
        const u = c.getIndexAngle(0) - 0.5 * R;
        let f = u;
        let g;
        const p = 360 / this.countVisibleElements();
        for (g = 0; g < e; ++g) f += this._computeAngle(g, n, p);
        for (g = e; g < e + s; g++) {
            const m = t[g];
            let b = f;
            let x = f + this._computeAngle(g, n, p);
            let v = a.getDataVisibility(g)
                ? c.getDistanceFromCenterForValue(this.getParsed(g).r)
                : 0;
            ((f = x),
                o &&
                    (l.animateScale && (v = 0),
                    l.animateRotate && (b = x = u)));
            const y = {
                x: h,
                y: d,
                innerRadius: 0,
                outerRadius: v,
                startAngle: b,
                endAngle: x,
                options: this.resolveDataElementOptions(
                    g,
                    m.active ? "active" : n,
                ),
            };
            this.updateElement(m, g, y, n);
        }
    }

    countVisibleElements() {
        const t = this._cachedMeta;
        let e = 0;
        return (
            t.data.forEach((s, n) => {
                !isNaN(this.getParsed(n).r) &&
                    this.chart.getDataVisibility(n) &&
                    e++;
            }),
            e
        );
    }

    _computeAngle(t, e, s) {
        return this.chart.getDataVisibility(t)
            ? ot(this.resolveDataElementOptions(t, e).angle || s)
            : 0;
    }
};
(M($t, "id", "polarArea"),
    M($t, "defaults", {
        dataElementType: "arc",
        animation: { animateRotate: !0, animateScale: !0 },
        animations: {
            numbers: {
                type: "number",
                properties: [
                    "x",
                    "y",
                    "startAngle",
                    "endAngle",
                    "innerRadius",
                    "outerRadius",
                ],
            },
        },
        indexAxis: "r",
        startAngle: 0,
    }),
    M($t, "overrides", {
        aspectRatio: 1,
        plugins: {
            legend: {
                labels: {
                    generateLabels(t) {
                        const e = t.data;
                        if (e.labels.length && e.datasets.length) {
                            const {
                                labels: { pointStyle: s, color: n },
                            } = t.legend.options;
                            return e.labels.map((o, a) => {
                                const l = t
                                    .getDatasetMeta(0)
                                    .controller.getStyle(a);
                                return {
                                    text: o,
                                    fillStyle: l.backgroundColor,
                                    strokeStyle: l.borderColor,
                                    fontColor: n,
                                    lineWidth: l.borderWidth,
                                    pointStyle: s,
                                    hidden: !t.getDataVisibility(a),
                                    index: a,
                                };
                            });
                        }
                        return [];
                    },
                },
                onClick(t, e, s) {
                    (s.chart.toggleDataVisibility(e.index), s.chart.update());
                },
            },
        },
        scales: {
            r: {
                type: "radialLinear",
                angleLines: { display: !1 },
                beginAtZero: !0,
                grid: { circular: !0 },
                pointLabels: { display: !1 },
                startAngle: 0,
            },
        },
    }));
const Le = class extends kt {};
(M(Le, "id", "pie"),
    M(Le, "defaults", {
        cutout: 0,
        rotation: 0,
        circumference: 360,
        radius: "100%",
    }));
const he = class extends it {
    getLabelAndValue(t) {
        const e = this._cachedMeta.vScale;
        const s = this.getParsed(t);
        return {
            label: e.getLabels()[t],
            value: "" + e.getLabelForValue(s[e.axis]),
        };
    }

    parseObjectData(t, e, s, n) {
        return ss.bind(this)(t, e, s, n);
    }

    update(t) {
        const e = this._cachedMeta;
        const s = e.dataset;
        const n = e.data || [];
        const o = e.iScale.getLabels();
        if (((s.points = n), t !== "resize")) {
            const a = this.resolveDatasetElementOptions(t);
            this.options.showLine || (a.borderWidth = 0);
            const r = {
                _loop: !0,
                _fullLoop: o.length === n.length,
                options: a,
            };
            this.updateElement(s, void 0, r, t);
        }
        this.updateElements(n, 0, n.length, t);
    }

    updateElements(t, e, s, n) {
        const o = this._cachedMeta.rScale;
        const a = n === "reset";
        for (let r = e; r < e + s; r++) {
            const l = t[r];
            const c = this.resolveDataElementOptions(
                r,
                l.active ? "active" : n,
            );
            const h = o.getPointPositionForValue(r, this.getParsed(r).r);
            const d = a ? o.xCenter : h.x;
            const u = a ? o.yCenter : h.y;
            const f = {
                x: d,
                y: u,
                angle: h.angle,
                skip: isNaN(d) || isNaN(u),
                options: c,
            };
            this.updateElement(l, r, f, n);
        }
    }
};
(M(he, "id", "radar"),
    M(he, "defaults", {
        datasetElementType: "line",
        dataElementType: "point",
        indexAxis: "r",
        showLine: !0,
        elements: { line: { fill: "start" } },
    }),
    M(he, "overrides", {
        aspectRatio: 1,
        scales: { r: { type: "radialLinear" } },
    }));
const de = class extends it {
    getLabelAndValue(t) {
        const e = this._cachedMeta;
        const s = this.chart.data.labels || [];
        const { xScale: n, yScale: o } = e;
        const a = this.getParsed(t);
        const r = n.getLabelForValue(a.x);
        const l = o.getLabelForValue(a.y);
        return { label: s[t] || "", value: "(" + r + ", " + l + ")" };
    }

    update(t) {
        const e = this._cachedMeta;
        const { data: s = [] } = e;
        const n = this.chart._animationsDisabled;
        let { start: o, count: a } = Xi(e, s, n);
        if (
            ((this._drawStart = o),
            (this._drawCount = a),
            Ki(e) && ((o = 0), (a = s.length)),
            this.options.showLine)
        ) {
            this.datasetElementType || this.addElements();
            const { dataset: r, _dataset: l } = e;
            ((r._chart = this.chart),
                (r._datasetIndex = this.index),
                (r._decimated = !!l._decimated),
                (r.points = s));
            const c = this.resolveDatasetElementOptions(t);
            ((c.segment = this.options.segment),
                this.updateElement(r, void 0, { animated: !n, options: c }, t));
        } else {
            this.datasetElementType &&
                (delete e.dataset, (this.datasetElementType = !1));
        }
        this.updateElements(s, o, a, t);
    }

    addElements() {
        const { showLine: t } = this.options;
        (!this.datasetElementType &&
            t &&
            (this.datasetElementType = this.chart.registry.getElement("line")),
            super.addElements());
    }

    updateElements(t, e, s, n) {
        const o = n === "reset";
        const {
            iScale: a,
            vScale: r,
            _stacked: l,
            _dataset: c,
        } = this._cachedMeta;
        const h = this.resolveDataElementOptions(e, n);
        const d = this.getSharedOptions(h);
        const u = this.includeOptions(n, d);
        const f = a.axis;
        const g = r.axis;
        const { spanGaps: p, segment: m } = this.options;
        const b = Bt(p) ? p : Number.POSITIVE_INFINITY;
        const x = this.chart._animationsDisabled || o || n === "none";
        let v = e > 0 && this.getParsed(e - 1);
        for (let y = e; y < e + s; ++y) {
            const _ = t[y];
            const k = this.getParsed(y);
            const w = x ? _ : {};
            const S = A(k[g]);
            const P = (w[f] = a.getPixelForValue(k[f], y));
            const O = (w[g] =
                o || S
                    ? r.getBasePixel()
                    : r.getPixelForValue(
                          l ? this.applyStack(r, k, l) : k[g],
                          y,
                      ));
            ((w.skip = isNaN(P) || isNaN(O) || S),
                (w.stop = y > 0 && Math.abs(k[f] - v[f]) > b),
                m && ((w.parsed = k), (w.raw = c.data[y])),
                u &&
                    (w.options =
                        d ||
                        this.resolveDataElementOptions(
                            y,
                            _.active ? "active" : n,
                        )),
                x || this.updateElement(_, y, w, n),
                (v = k));
        }
        this.updateSharedOptions(d, n, h);
    }

    getMaxOverflow() {
        const t = this._cachedMeta;
        const e = t.data || [];
        if (!this.options.showLine) {
            let r = 0;
            for (let l = e.length - 1; l >= 0; --l) {
                r = Math.max(
                    r,
                    e[l].size(this.resolveDataElementOptions(l)) / 2,
                );
            }
            return r > 0 && r;
        }
        const s = t.dataset;
        const n = (s.options && s.options.borderWidth) || 0;
        if (!e.length) return n;
        const o = e[0].size(this.resolveDataElementOptions(0));
        const a = e[e.length - 1].size(
            this.resolveDataElementOptions(e.length - 1),
        );
        return Math.max(n, o, a) / 2;
    }
};
(M(de, "id", "scatter"),
    M(de, "defaults", {
        datasetElementType: !1,
        dataElementType: "point",
        showLine: !1,
        fill: !1,
    }),
    M(de, "overrides", {
        interaction: { mode: "point" },
        scales: { x: { type: "linear" }, y: { type: "linear" } },
    }));
const Nr = Object.freeze({
    __proto__: null,
    BarController: re,
    BubbleController: le,
    DoughnutController: kt,
    LineController: ce,
    PieController: Le,
    PolarAreaController: $t,
    RadarController: he,
    ScatterController: de,
});
function Wt() {
    throw new Error(
        "This method is not implemented: Check that a complete date adapter is provided.",
    );
}
const ws = class i {
    constructor(t) {
        M(this, "options");
        this.options = t || {};
    }

    static override(t) {
        Object.assign(i.prototype, t);
    }

    init() {}
    formats() {
        return Wt();
    }

    parse() {
        return Wt();
    }

    format() {
        return Wt();
    }

    add() {
        return Wt();
    }

    diff() {
        return Wt();
    }

    startOf() {
        return Wt();
    }

    endOf() {
        return Wt();
    }
};
const Hr = { _date: ws };
function jr(i, t, e, s) {
    const { controller: n, data: o, _sorted: a } = i;
    const r = n._cachedMeta.iScale;
    const l =
        i.dataset && i.dataset.options ? i.dataset.options.spanGaps : null;
    if (r && t === r.axis && t !== "r" && a && o.length) {
        const c = r._reversePixels ? ln : ct;
        if (s) {
            if (n._sharedOptions) {
                const h = o[0];
                const d = typeof h.getRange === "function" && h.getRange(t);
                if (d) {
                    const u = c(o, t, e - d);
                    const f = c(o, t, e + d);
                    return { lo: u.lo, hi: f.hi };
                }
            }
        } else {
            const h = c(o, t, e);
            if (l) {
                const { vScale: d } = n._cachedMeta;
                const { _parsed: u } = i;
                const f = u
                    .slice(0, h.lo + 1)
                    .reverse()
                    .findIndex((p) => !A(p[d.axis]));
                h.lo -= Math.max(0, f);
                const g = u.slice(h.hi).findIndex((p) => !A(p[d.axis]));
                h.hi += Math.max(0, g);
            }
            return h;
        }
    }
    return { lo: 0, hi: o.length - 1 };
}
function je(i, t, e, s, n) {
    const o = i.getSortedVisibleDatasetMetas();
    const a = e[t];
    for (let r = 0, l = o.length; r < l; ++r) {
        const { index: c, data: h } = o[r];
        const { lo: d, hi: u } = jr(o[r], t, a, n);
        for (let f = d; f <= u; ++f) {
            const g = h[f];
            g.skip || s(g, c, f);
        }
    }
}
function $r(i) {
    const t = i.indexOf("x") !== -1;
    const e = i.indexOf("y") !== -1;
    return function (s, n) {
        const o = t ? Math.abs(s.x - n.x) : 0;
        const a = e ? Math.abs(s.y - n.y) : 0;
        return Math.sqrt(Math.pow(o, 2) + Math.pow(a, 2));
    };
}
function ps(i, t, e, s, n) {
    const o = [];
    return (
        (!n && !i.isPointInArea(t)) ||
            je(
                i,
                e,
                t,
                function (r, l, c) {
                    (!n && !ht(r, i.chartArea, 0)) ||
                        (r.inRange(t.x, t.y, s) &&
                            o.push({ element: r, datasetIndex: l, index: c }));
                },
                !0,
            ),
        o
    );
}
function Yr(i, t, e, s) {
    const n = [];
    function o(a, r, l) {
        const { startAngle: c, endAngle: h } = a.getProps(
            ["startAngle", "endAngle"],
            s,
        );
        const { angle: d } = Hi(a, { x: t.x, y: t.y });
        ie(d, c, h) && n.push({ element: a, datasetIndex: r, index: l });
    }
    return (je(i, e, t, o), n);
}
function Ur(i, t, e, s, n, o) {
    let a = [];
    const r = $r(e);
    let l = Number.POSITIVE_INFINITY;
    function c(h, d, u) {
        const f = h.inRange(t.x, t.y, n);
        if (s && !f) return;
        const g = h.getCenterPoint(n);
        if (!(!!o || i.isPointInArea(g)) && !f) return;
        const m = r(t, g);
        m < l
            ? ((a = [{ element: h, datasetIndex: d, index: u }]), (l = m))
            : m === l && a.push({ element: h, datasetIndex: d, index: u });
    }
    return (je(i, e, t, c), a);
}
function ms(i, t, e, s, n, o) {
    return !o && !i.isPointInArea(t)
        ? []
        : e === "r" && !s
          ? Yr(i, t, e, n)
          : Ur(i, t, e, s, n, o);
}
function Vn(i, t, e, s, n) {
    const o = [];
    const a = e === "x" ? "inXRange" : "inYRange";
    let r = !1;
    return (
        je(i, e, t, (l, c, h) => {
            l[a] &&
                l[a](t[e], n) &&
                (o.push({ element: l, datasetIndex: c, index: h }),
                (r = r || l.inRange(t.x, t.y, n)));
        }),
        s && !r ? [] : o
    );
}
const Xr = {
    evaluateInteractionItems: je,
    modes: {
        index(i, t, e, s) {
            const n = Tt(t, i);
            const o = e.axis || "x";
            const a = e.includeInvisible || !1;
            const r = e.intersect ? ps(i, n, o, s, a) : ms(i, n, o, !1, s, a);
            const l = [];
            return r.length
                ? (i.getSortedVisibleDatasetMetas().forEach((c) => {
                      const h = r[0].index;
                      const d = c.data[h];
                      d &&
                          !d.skip &&
                          l.push({
                              element: d,
                              datasetIndex: c.index,
                              index: h,
                          });
                  }),
                  l)
                : [];
        },
        dataset(i, t, e, s) {
            const n = Tt(t, i);
            const o = e.axis || "xy";
            const a = e.includeInvisible || !1;
            let r = e.intersect ? ps(i, n, o, s, a) : ms(i, n, o, !1, s, a);
            if (r.length > 0) {
                const l = r[0].datasetIndex;
                const c = i.getDatasetMeta(l).data;
                r = [];
                for (let h = 0; h < c.length; ++h) {
                    r.push({ element: c[h], datasetIndex: l, index: h });
                }
            }
            return r;
        },
        point(i, t, e, s) {
            const n = Tt(t, i);
            const o = e.axis || "xy";
            const a = e.includeInvisible || !1;
            return ps(i, n, o, s, a);
        },
        nearest(i, t, e, s) {
            const n = Tt(t, i);
            const o = e.axis || "xy";
            const a = e.includeInvisible || !1;
            return ms(i, n, o, e.intersect, s, a);
        },
        x(i, t, e, s) {
            const n = Tt(t, i);
            return Vn(i, n, "x", e.intersect, s);
        },
        y(i, t, e, s) {
            const n = Tt(t, i);
            return Vn(i, n, "y", e.intersect, s);
        },
    },
};
const Lo = ["left", "top", "right", "bottom"];
function Oe(i, t) {
    return i.filter((e) => e.pos === t);
}
function Wn(i, t) {
    return i.filter((e) => Lo.indexOf(e.pos) === -1 && e.box.axis === t);
}
function Ce(i, t) {
    return i.sort((e, s) => {
        const n = t ? s : e;
        const o = t ? e : s;
        return n.weight === o.weight ? n.index - o.index : n.weight - o.weight;
    });
}
function Kr(i) {
    const t = [];
    let e;
    let s;
    let n;
    let o;
    let a;
    let r;
    for (e = 0, s = (i || []).length; e < s; ++e) {
        ((n = i[e]),
            ({
                position: o,
                options: { stack: a, stackWeight: r = 1 },
            } = n),
            t.push({
                index: e,
                box: n,
                pos: o,
                horizontal: n.isHorizontal(),
                weight: n.weight,
                stack: a && o + a,
                stackWeight: r,
            }));
    }
    return t;
}
function qr(i) {
    const t = {};
    for (const e of i) {
        const { stack: s, pos: n, stackWeight: o } = e;
        if (!s || !Lo.includes(n)) continue;
        const a = t[s] || (t[s] = { count: 0, placed: 0, weight: 0, size: 0 });
        (a.count++, (a.weight += o));
    }
    return t;
}
function Gr(i, t) {
    const e = qr(i);
    const { vBoxMaxWidth: s, hBoxMaxHeight: n } = t;
    let o;
    let a;
    let r;
    for (o = 0, a = i.length; o < a; ++o) {
        r = i[o];
        const { fullSize: l } = r.box;
        const c = e[r.stack];
        const h = c && r.stackWeight / c.weight;
        r.horizontal
            ? ((r.width = h ? h * s : l && t.availableWidth), (r.height = n))
            : ((r.width = s), (r.height = h ? h * n : l && t.availableHeight));
    }
    return e;
}
function Zr(i) {
    const t = Kr(i);
    const e = Ce(
        t.filter((c) => c.box.fullSize),
        !0,
    );
    const s = Ce(Oe(t, "left"), !0);
    const n = Ce(Oe(t, "right"));
    const o = Ce(Oe(t, "top"), !0);
    const a = Ce(Oe(t, "bottom"));
    const r = Wn(t, "x");
    const l = Wn(t, "y");
    return {
        fullSize: e,
        leftAndTop: s.concat(o),
        rightAndBottom: n.concat(l).concat(a).concat(r),
        chartArea: Oe(t, "chartArea"),
        vertical: s.concat(n).concat(l),
        horizontal: o.concat(a).concat(r),
    };
}
function Nn(i, t, e, s) {
    return Math.max(i[e], t[e]) + Math.max(i[s], t[s]);
}
function Ro(i, t) {
    ((i.top = Math.max(i.top, t.top)),
        (i.left = Math.max(i.left, t.left)),
        (i.bottom = Math.max(i.bottom, t.bottom)),
        (i.right = Math.max(i.right, t.right)));
}
function Jr(i, t, e, s) {
    const { pos: n, box: o } = e;
    const a = i.maxPadding;
    if (!T(n)) {
        e.size && (i[n] -= e.size);
        const d = s[e.stack] || { size: 0, count: 1 };
        ((d.size = Math.max(d.size, e.horizontal ? o.height : o.width)),
            (e.size = d.size / d.count),
            (i[n] += e.size));
    }
    o.getPadding && Ro(a, o.getPadding());
    const r = Math.max(0, t.outerWidth - Nn(a, i, "left", "right"));
    const l = Math.max(0, t.outerHeight - Nn(a, i, "top", "bottom"));
    const c = r !== i.w;
    const h = l !== i.h;
    return (
        (i.w = r),
        (i.h = l),
        e.horizontal ? { same: c, other: h } : { same: h, other: c }
    );
}
function Qr(i) {
    const t = i.maxPadding;
    function e(s) {
        const n = Math.max(t[s] - i[s], 0);
        return ((i[s] += n), n);
    }
    ((i.y += e("top")), (i.x += e("left")), e("right"), e("bottom"));
}
function tl(i, t) {
    const e = t.maxPadding;
    function s(n) {
        const o = { left: 0, top: 0, right: 0, bottom: 0 };
        return (
            n.forEach((a) => {
                o[a] = Math.max(t[a], e[a]);
            }),
            o
        );
    }
    return s(i ? ["left", "right"] : ["top", "bottom"]);
}
function Re(i, t, e, s) {
    const n = [];
    let o;
    let a;
    let r;
    let l;
    let c;
    let h;
    for (o = 0, a = i.length, c = 0; o < a; ++o) {
        ((r = i[o]),
            (l = r.box),
            l.update(r.width || t.w, r.height || t.h, tl(r.horizontal, t)));
        const { same: d, other: u } = Jr(t, e, r, s);
        ((c |= d && n.length), (h = h || u), l.fullSize || n.push(r));
    }
    return (c && Re(n, t, e, s)) || h;
}
function fi(i, t, e, s, n) {
    ((i.top = e),
        (i.left = t),
        (i.right = t + s),
        (i.bottom = e + n),
        (i.width = s),
        (i.height = n));
}
function Hn(i, t, e, s) {
    const n = e.padding;
    let { x: o, y: a } = t;
    for (const r of i) {
        const l = r.box;
        const c = s[r.stack] || { count: 1, placed: 0, weight: 1 };
        const h = r.stackWeight / c.weight || 1;
        if (r.horizontal) {
            const d = t.w * h;
            const u = c.size || l.height;
            (te(c.start) && (a = c.start),
                l.fullSize
                    ? fi(l, n.left, a, e.outerWidth - n.right - n.left, u)
                    : fi(l, t.left + c.placed, a, d, u),
                (c.start = a),
                (c.placed += d),
                (a = l.bottom));
        } else {
            const d = t.h * h;
            const u = c.size || l.width;
            (te(c.start) && (o = c.start),
                l.fullSize
                    ? fi(l, o, n.top, u, e.outerHeight - n.bottom - n.top)
                    : fi(l, o, t.top + c.placed, u, d),
                (c.start = o),
                (c.placed += d),
                (o = l.right));
        }
    }
    ((t.x = o), (t.y = a));
}
const Z = {
    addBox(i, t) {
        (i.boxes || (i.boxes = []),
            (t.fullSize = t.fullSize || !1),
            (t.position = t.position || "top"),
            (t.weight = t.weight || 0),
            (t._layers =
                t._layers ||
                function () {
                    return [
                        {
                            z: 0,
                            draw(e) {
                                t.draw(e);
                            },
                        },
                    ];
                }),
            i.boxes.push(t));
    },
    removeBox(i, t) {
        const e = i.boxes ? i.boxes.indexOf(t) : -1;
        e !== -1 && i.boxes.splice(e, 1);
    },
    configure(i, t, e) {
        ((t.fullSize = e.fullSize),
            (t.position = e.position),
            (t.weight = e.weight));
    },
    update(i, t, e, s) {
        if (!i) return;
        const n = q(i.options.layout.padding);
        const o = Math.max(t - n.width, 0);
        const a = Math.max(e - n.height, 0);
        const r = Zr(i.boxes);
        const l = r.vertical;
        const c = r.horizontal;
        E(i.boxes, (p) => {
            typeof p.beforeLayout === "function" && p.beforeLayout();
        });
        const h =
            l.reduce(
                (p, m) =>
                    m.box.options && m.box.options.display === !1 ? p : p + 1,
                0,
            ) || 1;
        const d = Object.freeze({
            outerWidth: t,
            outerHeight: e,
            padding: n,
            availableWidth: o,
            availableHeight: a,
            vBoxMaxWidth: o / 2 / h,
            hBoxMaxHeight: a / 2,
        });
        const u = Object.assign({}, n);
        Ro(u, q(s));
        const f = Object.assign(
            { maxPadding: u, w: o, h: a, x: n.left, y: n.top },
            n,
        );
        const g = Gr(l.concat(c), d);
        (Re(r.fullSize, f, d, g),
            Re(l, f, d, g),
            Re(c, f, d, g) && Re(l, f, d, g),
            Qr(f),
            Hn(r.leftAndTop, f, d, g),
            (f.x += f.w),
            (f.y += f.h),
            Hn(r.rightAndBottom, f, d, g),
            (i.chartArea = {
                left: f.left,
                top: f.top,
                right: f.left + f.w,
                bottom: f.top + f.h,
                height: f.h,
                width: f.w,
            }),
            E(r.chartArea, (p) => {
                const m = p.box;
                (Object.assign(m, i.chartArea),
                    m.update(f.w, f.h, {
                        left: 0,
                        top: 0,
                        right: 0,
                        bottom: 0,
                    }));
            }));
    },
};
const Mi = class {
    acquireContext(t, e) {}
    releaseContext(t) {
        return !1;
    }

    addEventListener(t, e, s) {}
    removeEventListener(t, e, s) {}
    getDevicePixelRatio() {
        return 1;
    }

    getMaximumSize(t, e, s, n) {
        return (
            (e = Math.max(0, e || t.width)),
            (s = s || t.height),
            { width: e, height: Math.max(0, n ? Math.floor(e / n) : s) }
        );
    }

    isAttached(t) {
        return !0;
    }

    updateConfig(t) {}
};
const Ss = class extends Mi {
    acquireContext(t) {
        return (t && t.getContext && t.getContext("2d")) || null;
    }

    updateConfig(t) {
        t.options.animation = !1;
    }
};
const _i = "$chartjs";
const el = {
    touchstart: "mousedown",
    touchmove: "mousemove",
    touchend: "mouseup",
    pointerenter: "mouseenter",
    pointerdown: "mousedown",
    pointermove: "mousemove",
    pointerup: "mouseup",
    pointerleave: "mouseout",
    pointerout: "mouseout",
};
const jn = (i) => i === null || i === "";
function il(i, t) {
    const e = i.style;
    const s = i.getAttribute("height");
    const n = i.getAttribute("width");
    if (
        ((i[_i] = {
            initial: {
                height: s,
                width: n,
                style: { display: e.display, height: e.height, width: e.width },
            },
        }),
        (e.display = e.display || "block"),
        (e.boxSizing = e.boxSizing || "border-box"),
        jn(n))
    ) {
        const o = os(i, "width");
        o !== void 0 && (i.width = o);
    }
    if (jn(s)) {
        if (i.style.height === "") i.height = i.width / (t || 2);
        else {
            const o = os(i, "height");
            o !== void 0 && (i.height = o);
        }
    }
    return i;
}
const Eo = Sn ? { passive: !0 } : !1;
function sl(i, t, e) {
    i && i.addEventListener(t, e, Eo);
}
function nl(i, t, e) {
    i && i.canvas && i.canvas.removeEventListener(t, e, Eo);
}
function ol(i, t) {
    const e = el[i.type] || i.type;
    const { x: s, y: n } = Tt(i, t);
    return {
        type: e,
        chart: t,
        native: i,
        x: s !== void 0 ? s : null,
        y: n !== void 0 ? n : null,
    };
}
function ki(i, t) {
    for (const e of i) if (e === t || e.contains(t)) return !0;
}
function al(i, t, e) {
    const s = i.canvas;
    const n = new MutationObserver((o) => {
        let a = !1;
        for (const r of o) {
            ((a = a || ki(r.addedNodes, s)), (a = a && !ki(r.removedNodes, s)));
        }
        a && e();
    });
    return (n.observe(document, { childList: !0, subtree: !0 }), n);
}
function rl(i, t, e) {
    const s = i.canvas;
    const n = new MutationObserver((o) => {
        let a = !1;
        for (const r of o) {
            ((a = a || ki(r.removedNodes, s)), (a = a && !ki(r.addedNodes, s)));
        }
        a && e();
    });
    return (n.observe(document, { childList: !0, subtree: !0 }), n);
}
const We = new Map();
let $n = 0;
function Io() {
    const i = window.devicePixelRatio;
    i !== $n &&
        (($n = i),
        We.forEach((t, e) => {
            e.currentDevicePixelRatio !== i && t();
        }));
}
function ll(i, t) {
    (We.size || window.addEventListener("resize", Io), We.set(i, t));
}
function cl(i) {
    (We.delete(i), We.size || window.removeEventListener("resize", Io));
}
function hl(i, t, e) {
    const s = i.canvas;
    const n = s && di(s);
    if (!n) return;
    const o = Ui((r, l) => {
        const c = n.clientWidth;
        (e(r, l), c < n.clientWidth && e());
    }, window);
    const a = new ResizeObserver((r) => {
        const l = r[0];
        const c = l.contentRect.width;
        const h = l.contentRect.height;
        (c === 0 && h === 0) || o(c, h);
    });
    return (a.observe(n), ll(i, o), a);
}
function bs(i, t, e) {
    (e && e.disconnect(), t === "resize" && cl(i));
}
function dl(i, t, e) {
    const s = i.canvas;
    const n = Ui((o) => {
        i.ctx !== null && e(ol(o, i));
    }, i);
    return (sl(s, t, n), n);
}
const Ps = class extends Mi {
    acquireContext(t, e) {
        const s = t && t.getContext && t.getContext("2d");
        return s && s.canvas === t ? (il(t, e), s) : null;
    }

    releaseContext(t) {
        const e = t.canvas;
        if (!e[_i]) return !1;
        const s = e[_i].initial;
        ["height", "width"].forEach((o) => {
            const a = s[o];
            A(a) ? e.removeAttribute(o) : e.setAttribute(o, a);
        });
        const n = s.style || {};
        return (
            Object.keys(n).forEach((o) => {
                e.style[o] = n[o];
            }),
            (e.width = e.width),
            delete e[_i],
            !0
        );
    }

    addEventListener(t, e, s) {
        this.removeEventListener(t, e);
        const n = t.$proxies || (t.$proxies = {});
        const a = { attach: al, detach: rl, resize: hl }[e] || dl;
        n[e] = a(t, e, s);
    }

    removeEventListener(t, e) {
        const s = t.$proxies || (t.$proxies = {});
        const n = s[e];
        if (!n) return;
        ((({ attach: bs, detach: bs, resize: bs })[e] || nl)(t, e, n),
            (s[e] = void 0));
    }

    getDevicePixelRatio() {
        return window.devicePixelRatio;
    }

    getMaximumSize(t, e, s, n) {
        return wn(t, e, s, n);
    }

    isAttached(t) {
        const e = t && di(t);
        return !!(e && e.isConnected);
    }
};
function ul(i) {
    return !hi() ||
        (typeof OffscreenCanvas < "u" && i instanceof OffscreenCanvas)
        ? Ss
        : Ps;
}
const st = class {
    constructor() {
        M(this, "x");
        M(this, "y");
        M(this, "active", !1);
        M(this, "options");
        M(this, "$animations");
    }

    tooltipPosition(t) {
        const { x: e, y: s } = this.getProps(["x", "y"], t);
        return { x: e, y: s };
    }

    hasValue() {
        return Bt(this.x) && Bt(this.y);
    }

    getProps(t, e) {
        const s = this.$animations;
        if (!e || !s) return this;
        const n = {};
        return (
            t.forEach((o) => {
                n[o] = s[o] && s[o].active() ? s[o]._to : this[o];
            }),
            n
        );
    }
};
(M(st, "defaults", {}), M(st, "defaultRoutes"));
function fl(i, t) {
    const e = i.options.ticks;
    const s = gl(i);
    const n = Math.min(e.maxTicksLimit || s, s);
    const o = e.major.enabled ? ml(t) : [];
    const a = o.length;
    const r = o[0];
    const l = o[a - 1];
    const c = [];
    if (a > n) return (bl(t, c, o, a / n), c);
    const h = pl(o, t, n);
    if (a > 0) {
        let d;
        let u;
        const f = a > 1 ? Math.round((l - r) / (a - 1)) : null;
        for (gi(t, c, h, A(f) ? 0 : r - f, r), d = 0, u = a - 1; d < u; d++) {
            gi(t, c, h, o[d], o[d + 1]);
        }
        return (gi(t, c, h, l, A(f) ? t.length : l + f), c);
    }
    return (gi(t, c, h), c);
}
function gl(i) {
    const t = i.options.offset;
    const e = i._tickSize();
    const s = i._length / e + (t ? 0 : 1);
    const n = i._maxLength / e;
    return Math.floor(Math.min(s, n));
}
function pl(i, t, e) {
    const s = xl(i);
    const n = t.length / e;
    if (!s) return Math.max(n, 1);
    const o = on(s);
    for (let a = 0, r = o.length - 1; a < r; a++) {
        const l = o[a];
        if (l > n) return l;
    }
    return Math.max(n, 1);
}
function ml(i) {
    const t = [];
    let e;
    let s;
    for (e = 0, s = i.length; e < s; e++) i[e].major && t.push(e);
    return t;
}
function bl(i, t, e, s) {
    let n = 0;
    let o = e[0];
    let a;
    for (s = Math.ceil(s), a = 0; a < i.length; a++) {
        a === o && (t.push(i[a]), n++, (o = e[n * s]));
    }
}
function gi(i, t, e, s, n) {
    const o = D(s, 0);
    const a = Math.min(D(n, i.length), i.length);
    let r = 0;
    let l;
    let c;
    let h;
    for (
        e = Math.ceil(e),
            n && ((l = n - s), (e = l / Math.floor(l / e))),
            h = o;
        h < 0;

    ) {
        (r++, (h = Math.round(o + r * e)));
    }
    for (c = Math.max(o, 0); c < a; c++) {
        c === h && (t.push(i[c]), r++, (h = Math.round(o + r * e)));
    }
}
function xl(i) {
    const t = i.length;
    let e;
    let s;
    if (t < 2) return !1;
    for (s = i[0], e = 1; e < t; ++e) if (i[e] - i[e - 1] !== s) return !1;
    return s;
}
const _l = (i) => (i === "left" ? "right" : i === "right" ? "left" : i);
const Yn = (i, t, e) => (t === "top" || t === "left" ? i[t] + e : i[t] - e);
const Un = (i, t) => Math.min(t || i, i);
function Xn(i, t) {
    const e = [];
    const s = i.length / t;
    const n = i.length;
    let o = 0;
    for (; o < n; o += s) e.push(i[Math.floor(o)]);
    return e;
}
function yl(i, t, e) {
    const s = i.ticks.length;
    const n = Math.min(t, s - 1);
    const o = i._startPixel;
    const a = i._endPixel;
    const r = 1e-6;
    let l = i.getPixelForTick(n);
    let c;
    if (
        !(
            e &&
            (s === 1
                ? (c = Math.max(l - o, a - l))
                : t === 0
                  ? (c = (i.getPixelForTick(1) - l) / 2)
                  : (c = (l - i.getPixelForTick(n - 1)) / 2),
            (l += n < t ? c : -c),
            l < o - r || l > a + r)
        )
    ) {
        return l;
    }
}
function vl(i, t) {
    E(i, (e) => {
        const s = e.gc;
        const n = s.length / 2;
        let o;
        if (n > t) {
            for (o = 0; o < n; ++o) delete e.data[s[o]];
            s.splice(0, n);
        }
    });
}
function Ae(i) {
    return i.drawTicks ? i.tickLength : 0;
}
function Kn(i, t) {
    if (!i.display) return 0;
    const e = $(i.font, t);
    const s = q(i.padding);
    return (z(i.text) ? i.text.length : 1) * e.lineHeight + s.height;
}
function Ml(i, t) {
    return yt(i, { scale: t, type: "scale" });
}
function kl(i, t, e) {
    return yt(i, { tick: e, index: t, type: "tick" });
}
function wl(i, t, e) {
    let s = oi(i);
    return (((e && t !== "right") || (!e && t === "right")) && (s = _l(s)), s);
}
function Sl(i, t, e, s) {
    const { top: n, left: o, bottom: a, right: r, chart: l } = i;
    const { chartArea: c, scales: h } = l;
    let d = 0;
    let u;
    let f;
    let g;
    const p = a - n;
    const m = r - o;
    if (i.isHorizontal()) {
        if (((f = K(s, o, r)), T(e))) {
            const b = Object.keys(e)[0];
            const x = e[b];
            g = h[b].getPixelForValue(x) + p - t;
        } else {
            e === "center"
                ? (g = (c.bottom + c.top) / 2 + p - t)
                : (g = Yn(i, e, t));
        }
        u = r - o;
    } else {
        if (T(e)) {
            const b = Object.keys(e)[0];
            const x = e[b];
            f = h[b].getPixelForValue(x) - m + t;
        } else {
            e === "center"
                ? (f = (c.left + c.right) / 2 - m + t)
                : (f = Yn(i, e, t));
        }
        ((g = K(s, a, n)), (d = e === "left" ? -H : H));
    }
    return { titleX: f, titleY: g, maxWidth: u, rotation: d };
}
const Ut = class i extends st {
    constructor(t) {
        (super(),
            (this.id = t.id),
            (this.type = t.type),
            (this.options = void 0),
            (this.ctx = t.ctx),
            (this.chart = t.chart),
            (this.top = void 0),
            (this.bottom = void 0),
            (this.left = void 0),
            (this.right = void 0),
            (this.width = void 0),
            (this.height = void 0),
            (this._margins = { left: 0, right: 0, top: 0, bottom: 0 }),
            (this.maxWidth = void 0),
            (this.maxHeight = void 0),
            (this.paddingTop = void 0),
            (this.paddingBottom = void 0),
            (this.paddingLeft = void 0),
            (this.paddingRight = void 0),
            (this.axis = void 0),
            (this.labelRotation = void 0),
            (this.min = void 0),
            (this.max = void 0),
            (this._range = void 0),
            (this.ticks = []),
            (this._gridLineItems = null),
            (this._labelItems = null),
            (this._labelSizes = null),
            (this._length = 0),
            (this._maxLength = 0),
            (this._longestTextCache = {}),
            (this._startPixel = void 0),
            (this._endPixel = void 0),
            (this._reversePixels = !1),
            (this._userMax = void 0),
            (this._userMin = void 0),
            (this._suggestedMax = void 0),
            (this._suggestedMin = void 0),
            (this._ticksLength = 0),
            (this._borderValue = 0),
            (this._cache = {}),
            (this._dataLimitsCached = !1),
            (this.$context = void 0));
    }

    init(t) {
        ((this.options = t.setContext(this.getContext())),
            (this.axis = t.axis),
            (this._userMin = this.parse(t.min)),
            (this._userMax = this.parse(t.max)),
            (this._suggestedMin = this.parse(t.suggestedMin)),
            (this._suggestedMax = this.parse(t.suggestedMax)));
    }

    parse(t, e) {
        return t;
    }

    getUserBounds() {
        let {
            _userMin: t,
            _userMax: e,
            _suggestedMin: s,
            _suggestedMax: n,
        } = this;
        return (
            (t = J(t, Number.POSITIVE_INFINITY)),
            (e = J(e, Number.NEGATIVE_INFINITY)),
            (s = J(s, Number.POSITIVE_INFINITY)),
            (n = J(n, Number.NEGATIVE_INFINITY)),
            { min: J(t, s), max: J(e, n), minDefined: N(t), maxDefined: N(e) }
        );
    }

    getMinMax(t) {
        let {
            min: e,
            max: s,
            minDefined: n,
            maxDefined: o,
        } = this.getUserBounds();
        let a;
        if (n && o) return { min: e, max: s };
        const r = this.getMatchingVisibleMetas();
        for (let l = 0, c = r.length; l < c; ++l) {
            ((a = r[l].controller.getMinMax(this, t)),
                n || (e = Math.min(e, a.min)),
                o || (s = Math.max(s, a.max)));
        }
        return (
            (e = o && e > s ? s : e),
            (s = n && e > s ? e : s),
            { min: J(e, J(s, e)), max: J(s, J(e, s)) }
        );
    }

    getPadding() {
        return {
            left: this.paddingLeft || 0,
            top: this.paddingTop || 0,
            right: this.paddingRight || 0,
            bottom: this.paddingBottom || 0,
        };
    }

    getTicks() {
        return this.ticks;
    }

    getLabels() {
        const t = this.chart.data;
        return (
            this.options.labels ||
            (this.isHorizontal() ? t.xLabels : t.yLabels) ||
            t.labels ||
            []
        );
    }

    getLabelItems(t = this.chart.chartArea) {
        return (
            this._labelItems || (this._labelItems = this._computeLabelItems(t))
        );
    }

    beforeLayout() {
        ((this._cache = {}), (this._dataLimitsCached = !1));
    }

    beforeUpdate() {
        F(this.options.beforeUpdate, [this]);
    }

    update(t, e, s) {
        const { beginAtZero: n, grace: o, ticks: a } = this.options;
        const r = a.sampleSize;
        (this.beforeUpdate(),
            (this.maxWidth = t),
            (this.maxHeight = e),
            (this._margins = s =
                Object.assign({ left: 0, right: 0, top: 0, bottom: 0 }, s)),
            (this.ticks = null),
            (this._labelSizes = null),
            (this._gridLineItems = null),
            (this._labelItems = null),
            this.beforeSetDimensions(),
            this.setDimensions(),
            this.afterSetDimensions(),
            (this._maxLength = this.isHorizontal()
                ? this.width + s.left + s.right
                : this.height + s.top + s.bottom),
            this._dataLimitsCached ||
                (this.beforeDataLimits(),
                this.determineDataLimits(),
                this.afterDataLimits(),
                (this._range = xn(this, o, n)),
                (this._dataLimitsCached = !0)),
            this.beforeBuildTicks(),
            (this.ticks = this.buildTicks() || []),
            this.afterBuildTicks());
        const l = r < this.ticks.length;
        (this._convertTicksToLabels(l ? Xn(this.ticks, r) : this.ticks),
            this.configure(),
            this.beforeCalculateLabelRotation(),
            this.calculateLabelRotation(),
            this.afterCalculateLabelRotation(),
            a.display &&
                (a.autoSkip || a.source === "auto") &&
                ((this.ticks = fl(this, this.ticks)),
                (this._labelSizes = null),
                this.afterAutoSkip()),
            l && this._convertTicksToLabels(this.ticks),
            this.beforeFit(),
            this.fit(),
            this.afterFit(),
            this.afterUpdate());
    }

    configure() {
        let t = this.options.reverse;
        let e;
        let s;
        (this.isHorizontal()
            ? ((e = this.left), (s = this.right))
            : ((e = this.top), (s = this.bottom), (t = !t)),
            (this._startPixel = e),
            (this._endPixel = s),
            (this._reversePixels = t),
            (this._length = s - e),
            (this._alignToPixels = this.options.alignToPixels));
    }

    afterUpdate() {
        F(this.options.afterUpdate, [this]);
    }

    beforeSetDimensions() {
        F(this.options.beforeSetDimensions, [this]);
    }

    setDimensions() {
        (this.isHorizontal()
            ? ((this.width = this.maxWidth),
              (this.left = 0),
              (this.right = this.width))
            : ((this.height = this.maxHeight),
              (this.top = 0),
              (this.bottom = this.height)),
            (this.paddingLeft = 0),
            (this.paddingTop = 0),
            (this.paddingRight = 0),
            (this.paddingBottom = 0));
    }

    afterSetDimensions() {
        F(this.options.afterSetDimensions, [this]);
    }

    _callHooks(t) {
        (this.chart.notifyPlugins(t, this.getContext()),
            F(this.options[t], [this]));
    }

    beforeDataLimits() {
        this._callHooks("beforeDataLimits");
    }

    determineDataLimits() {}
    afterDataLimits() {
        this._callHooks("afterDataLimits");
    }

    beforeBuildTicks() {
        this._callHooks("beforeBuildTicks");
    }

    buildTicks() {
        return [];
    }

    afterBuildTicks() {
        this._callHooks("afterBuildTicks");
    }

    beforeTickToLabelConversion() {
        F(this.options.beforeTickToLabelConversion, [this]);
    }

    generateTickLabels(t) {
        const e = this.options.ticks;
        let s;
        let n;
        let o;
        for (s = 0, n = t.length; s < n; s++) {
            ((o = t[s]), (o.label = F(e.callback, [o.value, s, t], this)));
        }
    }

    afterTickToLabelConversion() {
        F(this.options.afterTickToLabelConversion, [this]);
    }

    beforeCalculateLabelRotation() {
        F(this.options.beforeCalculateLabelRotation, [this]);
    }

    calculateLabelRotation() {
        const t = this.options;
        const e = t.ticks;
        const s = Un(this.ticks.length, t.ticks.maxTicksLimit);
        const n = e.minRotation || 0;
        const o = e.maxRotation;
        let a = n;
        let r;
        let l;
        let c;
        if (
            !this._isVisible() ||
            !e.display ||
            n >= o ||
            s <= 1 ||
            !this.isHorizontal()
        ) {
            this.labelRotation = n;
            return;
        }
        const h = this._getLabelSizes();
        const d = h.widest.width;
        const u = h.highest.height;
        const f = Y(this.chart.width - d, 0, this.maxWidth);
        ((r = t.offset ? this.maxWidth / s : f / (s - 1)),
            d + 6 > r &&
                ((r = f / (s - (t.offset ? 0.5 : 1))),
                (l =
                    this.maxHeight -
                    Ae(t.grid) -
                    e.padding -
                    Kn(t.title, this.chart.options.font)),
                (c = Math.sqrt(d * d + u * u)),
                (a = si(
                    Math.min(
                        Math.asin(Y((h.highest.height + 6) / r, -1, 1)),
                        Math.asin(Y(l / c, -1, 1)) - Math.asin(Y(u / c, -1, 1)),
                    ),
                )),
                (a = Math.max(n, Math.min(o, a)))),
            (this.labelRotation = a));
    }

    afterCalculateLabelRotation() {
        F(this.options.afterCalculateLabelRotation, [this]);
    }

    afterAutoSkip() {}
    beforeFit() {
        F(this.options.beforeFit, [this]);
    }

    fit() {
        const t = { width: 0, height: 0 };
        const {
            chart: e,
            options: { ticks: s, title: n, grid: o },
        } = this;
        const a = this._isVisible();
        const r = this.isHorizontal();
        if (a) {
            const l = Kn(n, e.options.font);
            if (
                (r
                    ? ((t.width = this.maxWidth), (t.height = Ae(o) + l))
                    : ((t.height = this.maxHeight), (t.width = Ae(o) + l)),
                s.display && this.ticks.length)
            ) {
                const {
                    first: c,
                    last: h,
                    widest: d,
                    highest: u,
                } = this._getLabelSizes();
                const f = s.padding * 2;
                const g = ot(this.labelRotation);
                const p = Math.cos(g);
                const m = Math.sin(g);
                if (r) {
                    const b = s.mirror ? 0 : m * d.width + p * u.height;
                    t.height = Math.min(this.maxHeight, t.height + b + f);
                } else {
                    const b = s.mirror ? 0 : p * d.width + m * u.height;
                    t.width = Math.min(this.maxWidth, t.width + b + f);
                }
                this._calculatePadding(c, h, m, p);
            }
        }
        (this._handleMargins(),
            r
                ? ((this.width = this._length =
                      e.width - this._margins.left - this._margins.right),
                  (this.height = t.height))
                : ((this.width = t.width),
                  (this.height = this._length =
                      e.height - this._margins.top - this._margins.bottom)));
    }

    _calculatePadding(t, e, s, n) {
        const {
            ticks: { align: o, padding: a },
            position: r,
        } = this.options;
        const l = this.labelRotation !== 0;
        const c = r !== "top" && this.axis === "x";
        if (this.isHorizontal()) {
            const h = this.getPixelForTick(0) - this.left;
            const d = this.right - this.getPixelForTick(this.ticks.length - 1);
            let u = 0;
            let f = 0;
            (l
                ? c
                    ? ((u = n * t.width), (f = s * e.height))
                    : ((u = s * t.height), (f = n * e.width))
                : o === "start"
                  ? (f = e.width)
                  : o === "end"
                    ? (u = t.width)
                    : o !== "inner" && ((u = t.width / 2), (f = e.width / 2)),
                (this.paddingLeft = Math.max(
                    ((u - h + a) * this.width) / (this.width - h),
                    0,
                )),
                (this.paddingRight = Math.max(
                    ((f - d + a) * this.width) / (this.width - d),
                    0,
                )));
        } else {
            let h = e.height / 2;
            let d = t.height / 2;
            (o === "start"
                ? ((h = 0), (d = t.height))
                : o === "end" && ((h = e.height), (d = 0)),
                (this.paddingTop = h + a),
                (this.paddingBottom = d + a));
        }
    }

    _handleMargins() {
        this._margins &&
            ((this._margins.left = Math.max(
                this.paddingLeft,
                this._margins.left,
            )),
            (this._margins.top = Math.max(this.paddingTop, this._margins.top)),
            (this._margins.right = Math.max(
                this.paddingRight,
                this._margins.right,
            )),
            (this._margins.bottom = Math.max(
                this.paddingBottom,
                this._margins.bottom,
            )));
    }

    afterFit() {
        F(this.options.afterFit, [this]);
    }

    isHorizontal() {
        const { axis: t, position: e } = this.options;
        return e === "top" || e === "bottom" || t === "x";
    }

    isFullSize() {
        return this.options.fullSize;
    }

    _convertTicksToLabels(t) {
        (this.beforeTickToLabelConversion(), this.generateTickLabels(t));
        let e, s;
        for (e = 0, s = t.length; e < s; e++) {
            A(t[e].label) && (t.splice(e, 1), s--, e--);
        }
        this.afterTickToLabelConversion();
    }

    _getLabelSizes() {
        let t = this._labelSizes;
        if (!t) {
            const e = this.options.ticks.sampleSize;
            let s = this.ticks;
            (e < s.length && (s = Xn(s, e)),
                (this._labelSizes = t =
                    this._computeLabelSizes(
                        s,
                        s.length,
                        this.options.ticks.maxTicksLimit,
                    )));
        }
        return t;
    }

    _computeLabelSizes(t, e, s) {
        const { ctx: n, _longestTextCache: o } = this;
        const a = [];
        const r = [];
        const l = Math.floor(e / Un(e, s));
        let c = 0;
        let h = 0;
        let d;
        let u;
        let f;
        let g;
        let p;
        let m;
        let b;
        let x;
        let v;
        let y;
        let _;
        for (d = 0; d < e; d += l) {
            if (
                ((g = t[d].label),
                (p = this._resolveTickFontOptions(d)),
                (n.font = m = p.string),
                (b = o[m] = o[m] || { data: {}, gc: [] }),
                (x = p.lineHeight),
                (v = y = 0),
                !A(g) && !z(g))
            ) {
                ((v = Me(n, b.data, b.gc, v, g)), (y = x));
            } else if (z(g)) {
                for (u = 0, f = g.length; u < f; ++u) {
                    ((_ = g[u]),
                        !A(_) &&
                            !z(_) &&
                            ((v = Me(n, b.data, b.gc, v, _)), (y += x)));
                }
            }
            (a.push(v), r.push(y), (c = Math.max(v, c)), (h = Math.max(y, h)));
        }
        vl(o, e);
        const k = a.indexOf(c);
        const w = r.indexOf(h);
        const S = (P) => ({ width: a[P] || 0, height: r[P] || 0 });
        return {
            first: S(0),
            last: S(e - 1),
            widest: S(k),
            highest: S(w),
            widths: a,
            heights: r,
        };
    }

    getLabelForValue(t) {
        return t;
    }

    getPixelForValue(t, e) {
        return NaN;
    }

    getValueForPixel(t) {}
    getPixelForTick(t) {
        const e = this.ticks;
        return t < 0 || t > e.length - 1
            ? null
            : this.getPixelForValue(e[t].value);
    }

    getPixelForDecimal(t) {
        this._reversePixels && (t = 1 - t);
        const e = this._startPixel + t * this._length;
        return rn(this._alignToPixels ? Ot(this.chart, e, 0) : e);
    }

    getDecimalForPixel(t) {
        const e = (t - this._startPixel) / this._length;
        return this._reversePixels ? 1 - e : e;
    }

    getBasePixel() {
        return this.getPixelForValue(this.getBaseValue());
    }

    getBaseValue() {
        const { min: t, max: e } = this;
        return t < 0 && e < 0 ? e : t > 0 && e > 0 ? t : 0;
    }

    getContext(t) {
        const e = this.ticks || [];
        if (t >= 0 && t < e.length) {
            const s = e[t];
            return s.$context || (s.$context = kl(this.getContext(), t, s));
        }
        return (
            this.$context || (this.$context = Ml(this.chart.getContext(), this))
        );
    }

    _tickSize() {
        const t = this.options.ticks;
        const e = ot(this.labelRotation);
        const s = Math.abs(Math.cos(e));
        const n = Math.abs(Math.sin(e));
        const o = this._getLabelSizes();
        const a = t.autoSkipPadding || 0;
        const r = o ? o.widest.width + a : 0;
        const l = o ? o.highest.height + a : 0;
        return this.isHorizontal()
            ? l * s > r * n
                ? r / s
                : l / n
            : l * n < r * s
              ? l / s
              : r / n;
    }

    _isVisible() {
        const t = this.options.display;
        return t !== "auto" ? !!t : this.getMatchingVisibleMetas().length > 0;
    }

    _computeGridLineItems(t) {
        const e = this.axis;
        const s = this.chart;
        const n = this.options;
        const { grid: o, position: a, border: r } = n;
        const l = o.offset;
        const c = this.isHorizontal();
        const d = this.ticks.length + (l ? 1 : 0);
        const u = Ae(o);
        const f = [];
        const g = r.setContext(this.getContext());
        const p = g.display ? g.width : 0;
        const m = p / 2;
        const b = function (W) {
            return Ot(s, W, p);
        };
        let x;
        let v;
        let y;
        let _;
        let k;
        let w;
        let S;
        let P;
        let O;
        let C;
        let L;
        let U;
        if (a === "top") {
            ((x = b(this.bottom)),
                (w = this.bottom - u),
                (P = x - m),
                (C = b(t.top) + m),
                (U = t.bottom));
        } else if (a === "bottom") {
            ((x = b(this.top)),
                (C = t.top),
                (U = b(t.bottom) - m),
                (w = x + m),
                (P = this.top + u));
        } else if (a === "left") {
            ((x = b(this.right)),
                (k = this.right - u),
                (S = x - m),
                (O = b(t.left) + m),
                (L = t.right));
        } else if (a === "right") {
            ((x = b(this.left)),
                (O = t.left),
                (L = b(t.right) - m),
                (k = x + m),
                (S = this.left + u));
        } else if (e === "x") {
            if (a === "center") x = b((t.top + t.bottom) / 2 + 0.5);
            else if (T(a)) {
                const W = Object.keys(a)[0];
                const j = a[W];
                x = b(this.chart.scales[W].getPixelForValue(j));
            }
            ((C = t.top), (U = t.bottom), (w = x + m), (P = w + u));
        } else if (e === "y") {
            if (a === "center") x = b((t.left + t.right) / 2);
            else if (T(a)) {
                const W = Object.keys(a)[0];
                const j = a[W];
                x = b(this.chart.scales[W].getPixelForValue(j));
            }
            ((k = x - m), (S = k - u), (O = t.left), (L = t.right));
        }
        const et = D(n.ticks.maxTicksLimit, d);
        const I = Math.max(1, Math.ceil(d / et));
        for (v = 0; v < d; v += I) {
            const W = this.getContext(v);
            const j = o.setContext(W);
            const rt = r.setContext(W);
            const G = j.lineWidth;
            const Xt = j.color;
            const $e = rt.dash || [];
            const Kt = rt.dashOffset;
            const me = j.tickWidth;
            const Rt = j.tickColor;
            const be = j.tickBorderDash || [];
            const Et = j.tickBorderDashOffset;
            ((y = yl(this, v, l)),
                y !== void 0 &&
                    ((_ = Ot(s, y, G)),
                    c ? (k = S = O = L = _) : (w = P = C = U = _),
                    f.push({
                        tx1: k,
                        ty1: w,
                        tx2: S,
                        ty2: P,
                        x1: O,
                        y1: C,
                        x2: L,
                        y2: U,
                        width: G,
                        color: Xt,
                        borderDash: $e,
                        borderDashOffset: Kt,
                        tickWidth: me,
                        tickColor: Rt,
                        tickBorderDash: be,
                        tickBorderDashOffset: Et,
                    })));
        }
        return ((this._ticksLength = d), (this._borderValue = x), f);
    }

    _computeLabelItems(t) {
        const e = this.axis;
        const s = this.options;
        const { position: n, ticks: o } = s;
        const a = this.isHorizontal();
        const r = this.ticks;
        const { align: l, crossAlign: c, padding: h, mirror: d } = o;
        const u = Ae(s.grid);
        const f = u + h;
        const g = d ? -h : f;
        const p = -ot(this.labelRotation);
        const m = [];
        let b;
        let x;
        let v;
        let y;
        let _;
        let k;
        let w;
        let S;
        let P;
        let O;
        let C;
        let L;
        let U = "middle";
        if (n === "top") {
            ((k = this.bottom - g), (w = this._getXAxisLabelAlignment()));
        } else if (n === "bottom") {
            ((k = this.top + g), (w = this._getXAxisLabelAlignment()));
        } else if (n === "left") {
            const I = this._getYAxisLabelAlignment(u);
            ((w = I.textAlign), (_ = I.x));
        } else if (n === "right") {
            const I = this._getYAxisLabelAlignment(u);
            ((w = I.textAlign), (_ = I.x));
        } else if (e === "x") {
            if (n === "center") k = (t.top + t.bottom) / 2 + f;
            else if (T(n)) {
                const I = Object.keys(n)[0];
                const W = n[I];
                k = this.chart.scales[I].getPixelForValue(W) + f;
            }
            w = this._getXAxisLabelAlignment();
        } else if (e === "y") {
            if (n === "center") _ = (t.left + t.right) / 2 - f;
            else if (T(n)) {
                const I = Object.keys(n)[0];
                const W = n[I];
                _ = this.chart.scales[I].getPixelForValue(W);
            }
            w = this._getYAxisLabelAlignment(u).textAlign;
        }
        e === "y" &&
            (l === "start" ? (U = "top") : l === "end" && (U = "bottom"));
        const et = this._getLabelSizes();
        for (b = 0, x = r.length; b < x; ++b) {
            ((v = r[b]), (y = v.label));
            const I = o.setContext(this.getContext(b));
            ((S = this.getPixelForTick(b) + o.labelOffset),
                (P = this._resolveTickFontOptions(b)),
                (O = P.lineHeight),
                (C = z(y) ? y.length : 1));
            const W = C / 2;
            const j = I.color;
            const rt = I.textStrokeColor;
            const G = I.textStrokeWidth;
            let Xt = w;
            a
                ? ((_ = S),
                  w === "inner" &&
                      (b === x - 1
                          ? (Xt = this.options.reverse ? "left" : "right")
                          : b === 0
                            ? (Xt = this.options.reverse ? "right" : "left")
                            : (Xt = "center")),
                  n === "top"
                      ? c === "near" || p !== 0
                          ? (L = -C * O + O / 2)
                          : c === "center"
                            ? (L = -et.highest.height / 2 - W * O + O)
                            : (L = -et.highest.height + O / 2)
                      : c === "near" || p !== 0
                        ? (L = O / 2)
                        : c === "center"
                          ? (L = et.highest.height / 2 - W * O)
                          : (L = et.highest.height - C * O),
                  d && (L *= -1),
                  p !== 0 &&
                      !I.showLabelBackdrop &&
                      (_ += (O / 2) * Math.sin(p)))
                : ((k = S), (L = ((1 - C) * O) / 2));
            let $e;
            if (I.showLabelBackdrop) {
                const Kt = q(I.backdropPadding);
                const me = et.heights[b];
                const Rt = et.widths[b];
                let be = L - Kt.top;
                let Et = 0 - Kt.left;
                switch (U) {
                    case "middle":
                        be -= me / 2;
                        break;
                    case "bottom":
                        be -= me;
                        break;
                }
                switch (w) {
                    case "center":
                        Et -= Rt / 2;
                        break;
                    case "right":
                        Et -= Rt;
                        break;
                    case "inner":
                        b === x - 1 ? (Et -= Rt) : b > 0 && (Et -= Rt / 2);
                        break;
                }
                $e = {
                    left: Et,
                    top: be,
                    width: Rt + Kt.width,
                    height: me + Kt.height,
                    color: I.backdropColor,
                };
            }
            m.push({
                label: y,
                font: P,
                textOffset: L,
                options: {
                    rotation: p,
                    color: j,
                    strokeColor: rt,
                    strokeWidth: G,
                    textAlign: Xt,
                    textBaseline: U,
                    translation: [_, k],
                    backdrop: $e,
                },
            });
        }
        return m;
    }

    _getXAxisLabelAlignment() {
        const { position: t, ticks: e } = this.options;
        if (-ot(this.labelRotation)) return t === "top" ? "left" : "right";
        let n = "center";
        return (
            e.align === "start"
                ? (n = "left")
                : e.align === "end"
                  ? (n = "right")
                  : e.align === "inner" && (n = "inner"),
            n
        );
    }

    _getYAxisLabelAlignment(t) {
        const {
            position: e,
            ticks: { crossAlign: s, mirror: n, padding: o },
        } = this.options;
        const a = this._getLabelSizes();
        const r = t + o;
        const l = a.widest.width;
        let c;
        let h;
        return (
            e === "left"
                ? n
                    ? ((h = this.right + o),
                      s === "near"
                          ? (c = "left")
                          : s === "center"
                            ? ((c = "center"), (h += l / 2))
                            : ((c = "right"), (h += l)))
                    : ((h = this.right - r),
                      s === "near"
                          ? (c = "right")
                          : s === "center"
                            ? ((c = "center"), (h -= l / 2))
                            : ((c = "left"), (h = this.left)))
                : e === "right"
                  ? n
                      ? ((h = this.left + o),
                        s === "near"
                            ? (c = "right")
                            : s === "center"
                              ? ((c = "center"), (h -= l / 2))
                              : ((c = "left"), (h -= l)))
                      : ((h = this.left + r),
                        s === "near"
                            ? (c = "left")
                            : s === "center"
                              ? ((c = "center"), (h += l / 2))
                              : ((c = "right"), (h = this.right)))
                  : (c = "right"),
            { textAlign: c, x: h }
        );
    }

    _computeLabelArea() {
        if (this.options.ticks.mirror) return;
        const t = this.chart;
        const e = this.options.position;
        if (e === "left" || e === "right") {
            return {
                top: 0,
                left: this.left,
                bottom: t.height,
                right: this.right,
            };
        }
        if (e === "top" || e === "bottom") {
            return {
                top: this.top,
                left: 0,
                bottom: this.bottom,
                right: t.width,
            };
        }
    }

    drawBackground() {
        const {
            ctx: t,
            options: { backgroundColor: e },
            left: s,
            top: n,
            width: o,
            height: a,
        } = this;
        e && (t.save(), (t.fillStyle = e), t.fillRect(s, n, o, a), t.restore());
    }

    getLineWidthForValue(t) {
        const e = this.options.grid;
        if (!this._isVisible() || !e.display) return 0;
        const n = this.ticks.findIndex((o) => o.value === t);
        return n >= 0 ? e.setContext(this.getContext(n)).lineWidth : 0;
    }

    drawGrid(t) {
        const e = this.options.grid;
        const s = this.ctx;
        const n =
            this._gridLineItems ||
            (this._gridLineItems = this._computeGridLineItems(t));
        let o;
        let a;
        const r = (l, c, h) => {
            !h.width ||
                !h.color ||
                (s.save(),
                (s.lineWidth = h.width),
                (s.strokeStyle = h.color),
                s.setLineDash(h.borderDash || []),
                (s.lineDashOffset = h.borderDashOffset),
                s.beginPath(),
                s.moveTo(l.x, l.y),
                s.lineTo(c.x, c.y),
                s.stroke(),
                s.restore());
        };
        if (e.display) {
            for (o = 0, a = n.length; o < a; ++o) {
                const l = n[o];
                (e.drawOnChartArea &&
                    r({ x: l.x1, y: l.y1 }, { x: l.x2, y: l.y2 }, l),
                    e.drawTicks &&
                        r(
                            { x: l.tx1, y: l.ty1 },
                            { x: l.tx2, y: l.ty2 },
                            {
                                color: l.tickColor,
                                width: l.tickWidth,
                                borderDash: l.tickBorderDash,
                                borderDashOffset: l.tickBorderDashOffset,
                            },
                        ));
            }
        }
    }

    drawBorder() {
        const {
            chart: t,
            ctx: e,
            options: { border: s, grid: n },
        } = this;
        const o = s.setContext(this.getContext());
        const a = s.display ? o.width : 0;
        if (!a) return;
        const r = n.setContext(this.getContext(0)).lineWidth;
        const l = this._borderValue;
        let c;
        let h;
        let d;
        let u;
        (this.isHorizontal()
            ? ((c = Ot(t, this.left, a) - a / 2),
              (h = Ot(t, this.right, r) + r / 2),
              (d = u = l))
            : ((d = Ot(t, this.top, a) - a / 2),
              (u = Ot(t, this.bottom, r) + r / 2),
              (c = h = l)),
            e.save(),
            (e.lineWidth = o.width),
            (e.strokeStyle = o.color),
            e.beginPath(),
            e.moveTo(c, d),
            e.lineTo(h, u),
            e.stroke(),
            e.restore());
    }

    drawLabels(t) {
        if (!this.options.ticks.display) return;
        const s = this.ctx;
        const n = this._computeLabelArea();
        n && Se(s, n);
        const o = this.getLabelItems(t);
        for (const a of o) {
            const r = a.options;
            const l = a.font;
            const c = a.label;
            const h = a.textOffset;
            Ct(s, c, 0, h, l, r);
        }
        n && Pe(s);
    }

    drawTitle() {
        const {
            ctx: t,
            options: { position: e, title: s, reverse: n },
        } = this;
        if (!s.display) return;
        const o = $(s.font);
        const a = q(s.padding);
        const r = s.align;
        let l = o.lineHeight / 2;
        e === "bottom" || e === "center" || T(e)
            ? ((l += a.bottom),
              z(s.text) && (l += o.lineHeight * (s.text.length - 1)))
            : (l += a.top);
        const {
            titleX: c,
            titleY: h,
            maxWidth: d,
            rotation: u,
        } = Sl(this, l, e, r);
        Ct(t, s.text, 0, 0, o, {
            color: s.color,
            maxWidth: d,
            rotation: u,
            textAlign: wl(r, e, n),
            textBaseline: "middle",
            translation: [c, h],
        });
    }

    draw(t) {
        this._isVisible() &&
            (this.drawBackground(),
            this.drawGrid(t),
            this.drawBorder(),
            this.drawTitle(),
            this.drawLabels(t));
    }

    _layers() {
        const t = this.options;
        const e = (t.ticks && t.ticks.z) || 0;
        const s = D(t.grid && t.grid.z, -1);
        const n = D(t.border && t.border.z, 0);
        return !this._isVisible() || this.draw !== i.prototype.draw
            ? [
                  {
                      z: e,
                      draw: (o) => {
                          this.draw(o);
                      },
                  },
              ]
            : [
                  {
                      z: s,
                      draw: (o) => {
                          (this.drawBackground(),
                              this.drawGrid(o),
                              this.drawTitle());
                      },
                  },
                  {
                      z: n,
                      draw: () => {
                          this.drawBorder();
                      },
                  },
                  {
                      z: e,
                      draw: (o) => {
                          this.drawLabels(o);
                      },
                  },
              ];
    }

    getMatchingVisibleMetas(t) {
        const e = this.chart.getSortedVisibleDatasetMetas();
        const s = this.axis + "AxisID";
        const n = [];
        let o;
        let a;
        for (o = 0, a = e.length; o < a; ++o) {
            const r = e[o];
            r[s] === this.id && (!t || r.type === t) && n.push(r);
        }
        return n;
    }

    _resolveTickFontOptions(t) {
        const e = this.options.ticks.setContext(this.getContext(t));
        return $(e.font);
    }

    _maxDigits() {
        const t = this._resolveTickFontOptions(0).lineHeight;
        return (this.isHorizontal() ? this.width : this.height) / t;
    }
};
const ge = class {
    constructor(t, e, s) {
        ((this.type = t),
            (this.scope = e),
            (this.override = s),
            (this.items = Object.create(null)));
    }

    isForType(t) {
        return Object.prototype.isPrototypeOf.call(
            this.type.prototype,
            t.prototype,
        );
    }

    register(t) {
        const e = Object.getPrototypeOf(t);
        let s;
        Ol(e) && (s = this.register(e));
        const n = this.items;
        const o = t.id;
        const a = this.scope + "." + o;
        if (!o) throw new Error("class does not have id: " + t);
        return (
            o in n ||
                ((n[o] = t),
                Pl(t, a, s),
                this.override && V.override(t.id, t.overrides)),
            a
        );
    }

    get(t) {
        return this.items[t];
    }

    unregister(t) {
        const e = this.items;
        const s = t.id;
        const n = this.scope;
        (s in e && delete e[s],
            n && s in V[n] && (delete V[n][s], this.override && delete Dt[s]));
    }
};
function Pl(i, t, e) {
    const s = Zt(Object.create(null), [
        e ? V.get(e) : {},
        V.get(t),
        i.defaults,
    ]);
    (V.set(t, s),
        i.defaultRoutes && Dl(t, i.defaultRoutes),
        i.descriptors && V.describe(t, i.descriptors));
}
function Dl(i, t) {
    Object.keys(t).forEach((e) => {
        const s = e.split(".");
        const n = s.pop();
        const o = [i].concat(s).join(".");
        const a = t[e].split(".");
        const r = a.pop();
        const l = a.join(".");
        V.route(o, n, l, r);
    });
}
function Ol(i) {
    return "id" in i && "defaults" in i;
}
const Ds = class {
    constructor() {
        ((this.controllers = new ge(it, "datasets", !0)),
            (this.elements = new ge(st, "elements")),
            (this.plugins = new ge(Object, "plugins")),
            (this.scales = new ge(Ut, "scales")),
            (this._typedRegistries = [
                this.controllers,
                this.scales,
                this.elements,
            ]));
    }

    add(...t) {
        this._each("register", t);
    }

    remove(...t) {
        this._each("unregister", t);
    }

    addControllers(...t) {
        this._each("register", t, this.controllers);
    }

    addElements(...t) {
        this._each("register", t, this.elements);
    }

    addPlugins(...t) {
        this._each("register", t, this.plugins);
    }

    addScales(...t) {
        this._each("register", t, this.scales);
    }

    getController(t) {
        return this._get(t, this.controllers, "controller");
    }

    getElement(t) {
        return this._get(t, this.elements, "element");
    }

    getPlugin(t) {
        return this._get(t, this.plugins, "plugin");
    }

    getScale(t) {
        return this._get(t, this.scales, "scale");
    }

    removeControllers(...t) {
        this._each("unregister", t, this.controllers);
    }

    removeElements(...t) {
        this._each("unregister", t, this.elements);
    }

    removePlugins(...t) {
        this._each("unregister", t, this.plugins);
    }

    removeScales(...t) {
        this._each("unregister", t, this.scales);
    }

    _each(t, e, s) {
        [...e].forEach((n) => {
            const o = s || this._getRegistryForType(n);
            s || o.isForType(n) || (o === this.plugins && n.id)
                ? this._exec(t, o, n)
                : E(n, (a) => {
                      const r = s || this._getRegistryForType(a);
                      this._exec(t, r, a);
                  });
        });
    }

    _exec(t, e, s) {
        const n = ii(t);
        (F(s["before" + n], [], s), e[t](s), F(s["after" + n], [], s));
    }

    _getRegistryForType(t) {
        for (let e = 0; e < this._typedRegistries.length; e++) {
            const s = this._typedRegistries[e];
            if (s.isForType(t)) return s;
        }
        return this.plugins;
    }

    _get(t, e, s) {
        const n = e.get(t);
        if (n === void 0) {
            throw new Error('"' + t + '" is not a registered ' + s + ".");
        }
        return n;
    }
};
const gt = new Ds();
const Os = class {
    constructor() {
        this._init = [];
    }

    notify(t, e, s, n) {
        e === "beforeInit" &&
            ((this._init = this._createDescriptors(t, !0)),
            this._notify(this._init, t, "install"));
        const o = n ? this._descriptors(t).filter(n) : this._descriptors(t);
        const a = this._notify(o, t, e, s);
        return (
            e === "afterDestroy" &&
                (this._notify(o, t, "stop"),
                this._notify(this._init, t, "uninstall")),
            a
        );
    }

    _notify(t, e, s, n) {
        n = n || {};
        for (const o of t) {
            const a = o.plugin;
            const r = a[s];
            const l = [e, n, o.options];
            if (F(r, l, a) === !1 && n.cancelable) return !1;
        }
        return !0;
    }

    invalidate() {
        A(this._cache) ||
            ((this._oldCache = this._cache), (this._cache = void 0));
    }

    _descriptors(t) {
        if (this._cache) return this._cache;
        const e = (this._cache = this._createDescriptors(t));
        return (this._notifyStateChanges(t), e);
    }

    _createDescriptors(t, e) {
        const s = t && t.config;
        const n = D(s.options && s.options.plugins, {});
        const o = Cl(s);
        return n === !1 && !e ? [] : Tl(t, o, n, e);
    }

    _notifyStateChanges(t) {
        const e = this._oldCache || [];
        const s = this._cache;
        const n = (o, a) =>
            o.filter((r) => !a.some((l) => r.plugin.id === l.plugin.id));
        (this._notify(n(e, s), t, "stop"), this._notify(n(s, e), t, "start"));
    }
};
function Cl(i) {
    const t = {};
    const e = [];
    const s = Object.keys(gt.plugins.items);
    for (let o = 0; o < s.length; o++) e.push(gt.getPlugin(s[o]));
    const n = i.plugins || [];
    for (let o = 0; o < n.length; o++) {
        const a = n[o];
        e.indexOf(a) === -1 && (e.push(a), (t[a.id] = !0));
    }
    return { plugins: e, localIds: t };
}
function Al(i, t) {
    return !t && i === !1 ? null : i === !0 ? {} : i;
}
function Tl(i, { plugins: t, localIds: e }, s, n) {
    const o = [];
    const a = i.getContext();
    for (const r of t) {
        const l = r.id;
        const c = Al(s[l], n);
        c !== null &&
            o.push({
                plugin: r,
                options: Ll(i.config, { plugin: r, local: e[l] }, c, a),
            });
    }
    return o;
}
function Ll(i, { plugin: t, local: e }, s, n) {
    const o = i.pluginScopeKeys(t);
    const a = i.getOptionScopes(s, o);
    return (
        e && t.defaults && a.push(t.defaults),
        i.createResolver(a, n, [""], {
            scriptable: !1,
            indexable: !1,
            allKeys: !0,
        })
    );
}
function Cs(i, t) {
    const e = V.datasets[i] || {};
    return (
        ((t.datasets || {})[i] || {}).indexAxis ||
        t.indexAxis ||
        e.indexAxis ||
        "x"
    );
}
function Rl(i, t) {
    let e = i;
    return (
        i === "_index_"
            ? (e = t)
            : i === "_value_" && (e = t === "x" ? "y" : "x"),
        e
    );
}
function El(i, t) {
    return i === t ? "_index_" : "_value_";
}
function qn(i) {
    if (i === "x" || i === "y" || i === "r") return i;
}
function Il(i) {
    if (i === "top" || i === "bottom") return "x";
    if (i === "left" || i === "right") return "y";
}
function As(i, ...t) {
    if (qn(i)) return i;
    for (const e of t) {
        const s =
            e.axis ||
            Il(e.position) ||
            (i.length > 1 && qn(i[0].toLowerCase()));
        if (s) return s;
    }
    throw new Error(
        `Cannot determine type of '${i}' axis. Please provide 'axis' or 'position' option.`,
    );
}
function Gn(i, t, e) {
    if (e[t + "AxisID"] === i) return { axis: t };
}
function Fl(i, t) {
    if (t.data && t.data.datasets) {
        const e = t.data.datasets.filter(
            (s) => s.xAxisID === i || s.yAxisID === i,
        );
        if (e.length) return Gn(i, "x", e[0]) || Gn(i, "y", e[0]);
    }
    return {};
}
function zl(i, t) {
    const e = Dt[i.type] || { scales: {} };
    const s = t.scales || {};
    const n = Cs(i.type, t);
    const o = Object.create(null);
    return (
        Object.keys(s).forEach((a) => {
            const r = s[a];
            if (!T(r)) {
                return console.error(
                    `Invalid scale configuration for scale: ${a}`,
                );
            }
            if (r._proxy) {
                return console.warn(
                    `Ignoring resolver passed as options for scale: ${a}`,
                );
            }
            const l = As(a, r, Fl(a, i), V.scales[r.type]);
            const c = El(l, n);
            const h = e.scales || {};
            o[a] = Qt(Object.create(null), [{ axis: l }, r, h[l], h[c]]);
        }),
        i.data.datasets.forEach((a) => {
            const r = a.type || i.type;
            const l = a.indexAxis || Cs(r, t);
            const h = (Dt[r] || {}).scales || {};
            Object.keys(h).forEach((d) => {
                const u = Rl(d, l);
                const f = a[u + "AxisID"] || u;
                ((o[f] = o[f] || Object.create(null)),
                    Qt(o[f], [{ axis: u }, s[f], h[d]]));
            });
        }),
        Object.keys(o).forEach((a) => {
            const r = o[a];
            Qt(r, [V.scales[r.type], V.scale]);
        }),
        o
    );
}
function Fo(i) {
    const t = i.options || (i.options = {});
    ((t.plugins = D(t.plugins, {})), (t.scales = zl(i, t)));
}
function zo(i) {
    return (
        (i = i || {}),
        (i.datasets = i.datasets || []),
        (i.labels = i.labels || []),
        i
    );
}
function Bl(i) {
    return ((i = i || {}), (i.data = zo(i.data)), Fo(i), i);
}
const Zn = new Map();
const Bo = new Set();
function pi(i, t) {
    let e = Zn.get(i);
    return (e || ((e = t()), Zn.set(i, e), Bo.add(e)), e);
}
const Te = (i, t, e) => {
    const s = _t(t, e);
    s !== void 0 && i.add(s);
};
const Ts = class {
    constructor(t) {
        ((this._config = Bl(t)),
            (this._scopeCache = new Map()),
            (this._resolverCache = new Map()));
    }

    get platform() {
        return this._config.platform;
    }

    get type() {
        return this._config.type;
    }

    set type(t) {
        this._config.type = t;
    }

    get data() {
        return this._config.data;
    }

    set data(t) {
        this._config.data = zo(t);
    }

    get options() {
        return this._config.options;
    }

    set options(t) {
        this._config.options = t;
    }

    get plugins() {
        return this._config.plugins;
    }

    update() {
        const t = this._config;
        (this.clearCache(), Fo(t));
    }

    clearCache() {
        (this._scopeCache.clear(), this._resolverCache.clear());
    }

    datasetScopeKeys(t) {
        return pi(t, () => [[`datasets.${t}`, ""]]);
    }

    datasetAnimationScopeKeys(t, e) {
        return pi(`${t}.transition.${e}`, () => [
            [`datasets.${t}.transitions.${e}`, `transitions.${e}`],
            [`datasets.${t}`, ""],
        ]);
    }

    datasetElementScopeKeys(t, e) {
        return pi(`${t}-${e}`, () => [
            [
                `datasets.${t}.elements.${e}`,
                `datasets.${t}`,
                `elements.${e}`,
                "",
            ],
        ]);
    }

    pluginScopeKeys(t) {
        const e = t.id;
        const s = this.type;
        return pi(`${s}-plugin-${e}`, () => [
            [`plugins.${e}`, ...(t.additionalOptionScopes || [])],
        ]);
    }

    _cachedScopes(t, e) {
        const s = this._scopeCache;
        let n = s.get(t);
        return ((!n || e) && ((n = new Map()), s.set(t, n)), n);
    }

    getOptionScopes(t, e, s) {
        const { options: n, type: o } = this;
        const a = this._cachedScopes(t, s);
        const r = a.get(e);
        if (r) return r;
        const l = new Set();
        e.forEach((h) => {
            (t && (l.add(t), h.forEach((d) => Te(l, t, d))),
                h.forEach((d) => Te(l, n, d)),
                h.forEach((d) => Te(l, Dt[o] || {}, d)),
                h.forEach((d) => Te(l, V, d)),
                h.forEach((d) => Te(l, ai, d)));
        });
        const c = Array.from(l);
        return (
            c.length === 0 && c.push(Object.create(null)),
            Bo.has(e) && a.set(e, c),
            c
        );
    }

    chartOptionScopes() {
        const { options: t, type: e } = this;
        return [t, Dt[e] || {}, V.datasets[e] || {}, { type: e }, V, ai];
    }

    resolveNamedOptions(t, e, s, n = [""]) {
        const o = { $shared: !0 };
        const { resolver: a, subPrefixes: r } = Jn(this._resolverCache, t, n);
        let l = a;
        if (Wl(a, e)) {
            ((o.$shared = !1), (s = bt(s) ? s() : s));
            const c = this.createResolver(t, s, r);
            l = zt(a, s, c);
        }
        for (const c of e) o[c] = l[c];
        return o;
    }

    createResolver(t, e, s = [""], n) {
        const { resolver: o } = Jn(this._resolverCache, t, s);
        return T(e) ? zt(o, e, void 0, n) : o;
    }
};
function Jn(i, t, e) {
    let s = i.get(t);
    s || ((s = new Map()), i.set(t, s));
    const n = e.join();
    let o = s.get(n);
    return (
        o ||
            ((o = {
                resolver: ci(t, e),
                subPrefixes: e.filter(
                    (r) => !r.toLowerCase().includes("hover"),
                ),
            }),
            s.set(n, o)),
        o
    );
}
const Vl = (i) => T(i) && Object.getOwnPropertyNames(i).some((t) => bt(i[t]));
function Wl(i, t) {
    const { isScriptable: e, isIndexable: s } = ts(i);
    for (const n of t) {
        const o = e(n);
        const a = s(n);
        const r = (a || o) && i[n];
        if ((o && (bt(r) || Vl(r))) || (a && z(r))) return !0;
    }
    return !1;
}
const Nl = "4.5.0";
const Hl = ["top", "bottom", "left", "right", "chartArea"];
function Qn(i, t) {
    return i === "top" || i === "bottom" || (Hl.indexOf(i) === -1 && t === "x");
}
function to(i, t) {
    return function (e, s) {
        return e[i] === s[i] ? e[t] - s[t] : e[i] - s[i];
    };
}
function eo(i) {
    const t = i.chart;
    const e = t.options.animation;
    (t.notifyPlugins("afterRender"), F(e && e.onComplete, [i], t));
}
function jl(i) {
    const t = i.chart;
    const e = t.options.animation;
    F(e && e.onProgress, [i], t);
}
function Vo(i) {
    return (
        hi() && typeof i === "string"
            ? (i = document.getElementById(i))
            : i && i.length && (i = i[0]),
        i && i.canvas && (i = i.canvas),
        i
    );
}
const yi = {};
const io = (i) => {
    const t = Vo(i);
    return Object.values(yi)
        .filter((e) => e.canvas === t)
        .pop();
};
function $l(i, t, e) {
    const s = Object.keys(i);
    for (const n of s) {
        const o = +n;
        if (o >= t) {
            const a = i[n];
            (delete i[n], (e > 0 || o > t) && (i[o + e] = a));
        }
    }
}
function Yl(i, t, e, s) {
    return !e || i.type === "mouseout" ? null : s ? t : i;
}
const at = class {
    static register(...t) {
        (gt.add(...t), so());
    }

    static unregister(...t) {
        (gt.remove(...t), so());
    }

    constructor(t, e) {
        const s = (this.config = new Ts(e));
        const n = Vo(t);
        const o = io(n);
        if (o) {
            throw new Error(
                "Canvas is already in use. Chart with ID '" +
                    o.id +
                    "' must be destroyed before the canvas with ID '" +
                    o.canvas.id +
                    "' can be reused.",
            );
        }
        const a = s.createResolver(s.chartOptionScopes(), this.getContext());
        ((this.platform = new (s.platform || ul(n))()),
            this.platform.updateConfig(s));
        const r = this.platform.acquireContext(n, a.aspectRatio);
        const l = r && r.canvas;
        const c = l && l.height;
        const h = l && l.width;
        if (
            ((this.id = tn()),
            (this.ctx = r),
            (this.canvas = l),
            (this.width = h),
            (this.height = c),
            (this._options = a),
            (this._aspectRatio = this.aspectRatio),
            (this._layers = []),
            (this._metasets = []),
            (this._stacks = void 0),
            (this.boxes = []),
            (this.currentDevicePixelRatio = void 0),
            (this.chartArea = void 0),
            (this._active = []),
            (this._lastEvent = void 0),
            (this._listeners = {}),
            (this._responsiveListeners = void 0),
            (this._sortedMetasets = []),
            (this.scales = {}),
            (this._plugins = new Os()),
            (this.$proxies = {}),
            (this._hiddenIndices = {}),
            (this.attached = !1),
            (this._animationsDisabled = void 0),
            (this.$context = void 0),
            (this._doResize = un((d) => this.update(d), a.resizeDelay || 0)),
            (this._dataChanges = []),
            (yi[this.id] = this),
            !r || !l)
        ) {
            console.error(
                "Failed to create chart: can't acquire context from the given item",
            );
            return;
        }
        (vt.listen(this, "complete", eo),
            vt.listen(this, "progress", jl),
            this._initialize(),
            this.attached && this.update());
    }

    get aspectRatio() {
        const {
            options: { aspectRatio: t, maintainAspectRatio: e },
            width: s,
            height: n,
            _aspectRatio: o,
        } = this;
        return A(t) ? (e && o ? o : n ? s / n : null) : t;
    }

    get data() {
        return this.config.data;
    }

    set data(t) {
        this.config.data = t;
    }

    get options() {
        return this._options;
    }

    set options(t) {
        this.config.options = t;
    }

    get registry() {
        return gt;
    }

    _initialize() {
        return (
            this.notifyPlugins("beforeInit"),
            this.options.responsive
                ? this.resize()
                : ns(this, this.options.devicePixelRatio),
            this.bindEvents(),
            this.notifyPlugins("afterInit"),
            this
        );
    }

    clear() {
        return (Zi(this.canvas, this.ctx), this);
    }

    stop() {
        return (vt.stop(this), this);
    }

    resize(t, e) {
        vt.running(this)
            ? (this._resizeBeforeDraw = { width: t, height: e })
            : this._resize(t, e);
    }

    _resize(t, e) {
        const s = this.options;
        const n = this.canvas;
        const o = s.maintainAspectRatio && this.aspectRatio;
        const a = this.platform.getMaximumSize(n, t, e, o);
        const r = s.devicePixelRatio || this.platform.getDevicePixelRatio();
        const l = this.width ? "resize" : "attach";
        ((this.width = a.width),
            (this.height = a.height),
            (this._aspectRatio = this.aspectRatio),
            ns(this, r, !0) &&
                (this.notifyPlugins("resize", { size: a }),
                F(s.onResize, [this, a], this),
                this.attached && this._doResize(l) && this.render()));
    }

    ensureScalesHaveIDs() {
        const e = this.options.scales || {};
        E(e, (s, n) => {
            s.id = n;
        });
    }

    buildOrUpdateScales() {
        const t = this.options;
        const e = t.scales;
        const s = this.scales;
        const n = Object.keys(s).reduce((a, r) => ((a[r] = !1), a), {});
        let o = [];
        (e &&
            (o = o.concat(
                Object.keys(e).map((a) => {
                    const r = e[a];
                    const l = As(a, r);
                    const c = l === "r";
                    const h = l === "x";
                    return {
                        options: r,
                        dposition: c ? "chartArea" : h ? "bottom" : "left",
                        dtype: c ? "radialLinear" : h ? "category" : "linear",
                    };
                }),
            )),
            E(o, (a) => {
                const r = a.options;
                const l = r.id;
                const c = As(l, r);
                const h = D(r.type, a.dtype);
                ((r.position === void 0 ||
                    Qn(r.position, c) !== Qn(a.dposition)) &&
                    (r.position = a.dposition),
                    (n[l] = !0));
                let d = null;
                if (l in s && s[l].type === h) d = s[l];
                else {
                    const u = gt.getScale(h);
                    ((d = new u({
                        id: l,
                        type: h,
                        ctx: this.ctx,
                        chart: this,
                    })),
                        (s[d.id] = d));
                }
                d.init(r, t);
            }),
            E(n, (a, r) => {
                a || delete s[r];
            }),
            E(s, (a) => {
                (Z.configure(this, a, a.options), Z.addBox(this, a));
            }));
    }

    _updateMetasets() {
        const t = this._metasets;
        const e = this.data.datasets.length;
        const s = t.length;
        if ((t.sort((n, o) => n.index - o.index), s > e)) {
            for (let n = e; n < s; ++n) this._destroyDatasetMeta(n);
            t.splice(e, s - e);
        }
        this._sortedMetasets = t.slice(0).sort(to("order", "index"));
    }

    _removeUnreferencedMetasets() {
        const {
            _metasets: t,
            data: { datasets: e },
        } = this;
        (t.length > e.length && delete this._stacks,
            t.forEach((s, n) => {
                e.filter((o) => o === s._dataset).length === 0 &&
                    this._destroyDatasetMeta(n);
            }));
    }

    buildOrUpdateControllers() {
        const t = [];
        const e = this.data.datasets;
        let s;
        let n;
        for (
            this._removeUnreferencedMetasets(), s = 0, n = e.length;
            s < n;
            s++
        ) {
            const o = e[s];
            let a = this.getDatasetMeta(s);
            const r = o.type || this.config.type;
            if (
                (a.type &&
                    a.type !== r &&
                    (this._destroyDatasetMeta(s), (a = this.getDatasetMeta(s))),
                (a.type = r),
                (a.indexAxis = o.indexAxis || Cs(r, this.options)),
                (a.order = o.order || 0),
                (a.index = s),
                (a.label = "" + o.label),
                (a.visible = this.isDatasetVisible(s)),
                a.controller)
            ) {
                (a.controller.updateIndex(s), a.controller.linkScales());
            } else {
                const l = gt.getController(r);
                const { datasetElementType: c, dataElementType: h } =
                    V.datasets[r];
                (Object.assign(l, {
                    dataElementType: gt.getElement(h),
                    datasetElementType: c && gt.getElement(c),
                }),
                    (a.controller = new l(this, s)),
                    t.push(a.controller));
            }
        }
        return (this._updateMetasets(), t);
    }

    _resetElements() {
        E(
            this.data.datasets,
            (t, e) => {
                this.getDatasetMeta(e).controller.reset();
            },
            this,
        );
    }

    reset() {
        (this._resetElements(), this.notifyPlugins("reset"));
    }

    update(t) {
        const e = this.config;
        e.update();
        const s = (this._options = e.createResolver(
            e.chartOptionScopes(),
            this.getContext(),
        ));
        const n = (this._animationsDisabled = !s.animation);
        if (
            (this._updateScales(),
            this._checkEventBindings(),
            this._updateHiddenIndices(),
            this._plugins.invalidate(),
            this.notifyPlugins("beforeUpdate", { mode: t, cancelable: !0 }) ===
                !1)
        ) {
            return;
        }
        const o = this.buildOrUpdateControllers();
        this.notifyPlugins("beforeElementsUpdate");
        let a = 0;
        for (let c = 0, h = this.data.datasets.length; c < h; c++) {
            const { controller: d } = this.getDatasetMeta(c);
            const u = !n && o.indexOf(d) === -1;
            (d.buildOrUpdateElements(u),
                (a = Math.max(+d.getMaxOverflow(), a)));
        }
        ((a = this._minPadding = s.layout.autoPadding ? a : 0),
            this._updateLayout(a),
            n ||
                E(o, (c) => {
                    c.reset();
                }),
            this._updateDatasets(t),
            this.notifyPlugins("afterUpdate", { mode: t }),
            this._layers.sort(to("z", "_idx")));
        const { _active: r, _lastEvent: l } = this;
        (l
            ? this._eventHandler(l, !0)
            : r.length && this._updateHoverStyles(r, r, !0),
            this.render());
    }

    _updateScales() {
        (E(this.scales, (t) => {
            Z.removeBox(this, t);
        }),
            this.ensureScalesHaveIDs(),
            this.buildOrUpdateScales());
    }

    _checkEventBindings() {
        const t = this.options;
        const e = new Set(Object.keys(this._listeners));
        const s = new Set(t.events);
        (!Bi(e, s) || !!this._responsiveListeners !== t.responsive) &&
            (this.unbindEvents(), this.bindEvents());
    }

    _updateHiddenIndices() {
        const { _hiddenIndices: t } = this;
        const e = this._getUniformDataChanges() || [];
        for (const { method: s, start: n, count: o } of e) {
            const a = s === "_removeElements" ? -o : o;
            $l(t, n, a);
        }
    }

    _getUniformDataChanges() {
        const t = this._dataChanges;
        if (!t || !t.length) return;
        this._dataChanges = [];
        const e = this.data.datasets.length;
        const s = (o) =>
            new Set(
                t
                    .filter((a) => a[0] === o)
                    .map((a, r) => r + "," + a.splice(1).join(",")),
            );
        const n = s(0);
        for (let o = 1; o < e; o++) if (!Bi(n, s(o))) return;
        return Array.from(n)
            .map((o) => o.split(","))
            .map((o) => ({ method: o[1], start: +o[2], count: +o[3] }));
    }

    _updateLayout(t) {
        if (this.notifyPlugins("beforeLayout", { cancelable: !0 }) === !1) {
            return;
        }
        Z.update(this, this.width, this.height, t);
        const e = this.chartArea;
        const s = e.width <= 0 || e.height <= 0;
        ((this._layers = []),
            E(
                this.boxes,
                (n) => {
                    (s && n.position === "chartArea") ||
                        (n.configure && n.configure(),
                        this._layers.push(...n._layers()));
                },
                this,
            ),
            this._layers.forEach((n, o) => {
                n._idx = o;
            }),
            this.notifyPlugins("afterLayout"));
    }

    _updateDatasets(t) {
        if (
            this.notifyPlugins("beforeDatasetsUpdate", {
                mode: t,
                cancelable: !0,
            }) !== !1
        ) {
            for (let e = 0, s = this.data.datasets.length; e < s; ++e) {
                this.getDatasetMeta(e).controller.configure();
            }
            for (let e = 0, s = this.data.datasets.length; e < s; ++e) {
                this._updateDataset(e, bt(t) ? t({ datasetIndex: e }) : t);
            }
            this.notifyPlugins("afterDatasetsUpdate", { mode: t });
        }
    }

    _updateDataset(t, e) {
        const s = this.getDatasetMeta(t);
        const n = { meta: s, index: t, mode: e, cancelable: !0 };
        this.notifyPlugins("beforeDatasetUpdate", n) !== !1 &&
            (s.controller._update(e),
            (n.cancelable = !1),
            this.notifyPlugins("afterDatasetUpdate", n));
    }

    render() {
        this.notifyPlugins("beforeRender", { cancelable: !0 }) !== !1 &&
            (vt.has(this)
                ? this.attached && !vt.running(this) && vt.start(this)
                : (this.draw(), eo({ chart: this })));
    }

    draw() {
        let t;
        if (this._resizeBeforeDraw) {
            const { width: s, height: n } = this._resizeBeforeDraw;
            ((this._resizeBeforeDraw = null), this._resize(s, n));
        }
        if (
            (this.clear(),
            this.width <= 0 ||
                this.height <= 0 ||
                this.notifyPlugins("beforeDraw", { cancelable: !0 }) === !1)
        ) {
            return;
        }
        const e = this._layers;
        for (t = 0; t < e.length && e[t].z <= 0; ++t) e[t].draw(this.chartArea);
        for (this._drawDatasets(); t < e.length; ++t) e[t].draw(this.chartArea);
        this.notifyPlugins("afterDraw");
    }

    _getSortedDatasetMetas(t) {
        const e = this._sortedMetasets;
        const s = [];
        let n;
        let o;
        for (n = 0, o = e.length; n < o; ++n) {
            const a = e[n];
            (!t || a.visible) && s.push(a);
        }
        return s;
    }

    getSortedVisibleDatasetMetas() {
        return this._getSortedDatasetMetas(!0);
    }

    _drawDatasets() {
        if (
            this.notifyPlugins("beforeDatasetsDraw", { cancelable: !0 }) === !1
        ) {
            return;
        }
        const t = this.getSortedVisibleDatasetMetas();
        for (let e = t.length - 1; e >= 0; --e) this._drawDataset(t[e]);
        this.notifyPlugins("afterDatasetsDraw");
    }

    _drawDataset(t) {
        const e = this.ctx;
        const s = { meta: t, index: t.index, cancelable: !0 };
        const n = hs(this, t);
        this.notifyPlugins("beforeDatasetDraw", s) !== !1 &&
            (n && Se(e, n),
            t.controller.draw(),
            n && Pe(e),
            (s.cancelable = !1),
            this.notifyPlugins("afterDatasetDraw", s));
    }

    isPointInArea(t) {
        return ht(t, this.chartArea, this._minPadding);
    }

    getElementsAtEventForMode(t, e, s, n) {
        const o = Xr.modes[e];
        return typeof o === "function" ? o(this, t, s, n) : [];
    }

    getDatasetMeta(t) {
        const e = this.data.datasets[t];
        const s = this._metasets;
        let n = s.filter((o) => o && o._dataset === e).pop();
        return (
            n ||
                ((n = {
                    type: null,
                    data: [],
                    dataset: null,
                    controller: null,
                    hidden: null,
                    xAxisID: null,
                    yAxisID: null,
                    order: (e && e.order) || 0,
                    index: t,
                    _dataset: e,
                    _parsed: [],
                    _sorted: !1,
                }),
                s.push(n)),
            n
        );
    }

    getContext() {
        return (
            this.$context ||
            (this.$context = yt(null, { chart: this, type: "chart" }))
        );
    }

    getVisibleDatasetCount() {
        return this.getSortedVisibleDatasetMetas().length;
    }

    isDatasetVisible(t) {
        const e = this.data.datasets[t];
        if (!e) return !1;
        const s = this.getDatasetMeta(t);
        return typeof s.hidden === "boolean" ? !s.hidden : !e.hidden;
    }

    setDatasetVisibility(t, e) {
        const s = this.getDatasetMeta(t);
        s.hidden = !e;
    }

    toggleDataVisibility(t) {
        this._hiddenIndices[t] = !this._hiddenIndices[t];
    }

    getDataVisibility(t) {
        return !this._hiddenIndices[t];
    }

    _updateVisibility(t, e, s) {
        const n = s ? "show" : "hide";
        const o = this.getDatasetMeta(t);
        const a = o.controller._resolveAnimations(void 0, n);
        te(e)
            ? ((o.data[e].hidden = !s), this.update())
            : (this.setDatasetVisibility(t, s),
              a.update(o, { visible: s }),
              this.update((r) => (r.datasetIndex === t ? n : void 0)));
    }

    hide(t, e) {
        this._updateVisibility(t, e, !1);
    }

    show(t, e) {
        this._updateVisibility(t, e, !0);
    }

    _destroyDatasetMeta(t) {
        const e = this._metasets[t];
        (e && e.controller && e.controller._destroy(),
            delete this._metasets[t]);
    }

    _stop() {
        let t, e;
        for (
            this.stop(), vt.remove(this), t = 0, e = this.data.datasets.length;
            t < e;
            ++t
        ) {
            this._destroyDatasetMeta(t);
        }
    }

    destroy() {
        this.notifyPlugins("beforeDestroy");
        const { canvas: t, ctx: e } = this;
        (this._stop(),
            this.config.clearCache(),
            t &&
                (this.unbindEvents(),
                Zi(t, e),
                this.platform.releaseContext(e),
                (this.canvas = null),
                (this.ctx = null)),
            delete yi[this.id],
            this.notifyPlugins("afterDestroy"));
    }

    toBase64Image(...t) {
        return this.canvas.toDataURL(...t);
    }

    bindEvents() {
        (this.bindUserEvents(),
            this.options.responsive
                ? this.bindResponsiveEvents()
                : (this.attached = !0));
    }

    bindUserEvents() {
        const t = this._listeners;
        const e = this.platform;
        const s = (o, a) => {
            (e.addEventListener(this, o, a), (t[o] = a));
        };
        const n = (o, a, r) => {
            ((o.offsetX = a), (o.offsetY = r), this._eventHandler(o));
        };
        E(this.options.events, (o) => s(o, n));
    }

    bindResponsiveEvents() {
        this._responsiveListeners || (this._responsiveListeners = {});
        const t = this._responsiveListeners;
        const e = this.platform;
        const s = (l, c) => {
            (e.addEventListener(this, l, c), (t[l] = c));
        };
        const n = (l, c) => {
            t[l] && (e.removeEventListener(this, l, c), delete t[l]);
        };
        const o = (l, c) => {
            this.canvas && this.resize(l, c);
        };
        let a;
        const r = () => {
            (n("attach", r),
                (this.attached = !0),
                this.resize(),
                s("resize", o),
                s("detach", a));
        };
        ((a = () => {
            ((this.attached = !1),
                n("resize", o),
                this._stop(),
                this._resize(0, 0),
                s("attach", r));
        }),
            e.isAttached(this.canvas) ? r() : a());
    }

    unbindEvents() {
        (E(this._listeners, (t, e) => {
            this.platform.removeEventListener(this, e, t);
        }),
            (this._listeners = {}),
            E(this._responsiveListeners, (t, e) => {
                this.platform.removeEventListener(this, e, t);
            }),
            (this._responsiveListeners = void 0));
    }

    updateHoverStyle(t, e, s) {
        const n = s ? "set" : "remove";
        let o;
        let a;
        let r;
        let l;
        for (
            e === "dataset" &&
                ((o = this.getDatasetMeta(t[0].datasetIndex)),
                o.controller["_" + n + "DatasetHoverStyle"]()),
                r = 0,
                l = t.length;
            r < l;
            ++r
        ) {
            a = t[r];
            const c = a && this.getDatasetMeta(a.datasetIndex).controller;
            c && c[n + "HoverStyle"](a.element, a.datasetIndex, a.index);
        }
    }

    getActiveElements() {
        return this._active || [];
    }

    setActiveElements(t) {
        const e = this._active || [];
        const s = t.map(({ datasetIndex: o, index: a }) => {
            const r = this.getDatasetMeta(o);
            if (!r) throw new Error("No dataset found at index " + o);
            return { datasetIndex: o, element: r.data[a], index: a };
        });
        !ke(s, e) &&
            ((this._active = s),
            (this._lastEvent = null),
            this._updateHoverStyles(s, e));
    }

    notifyPlugins(t, e, s) {
        return this._plugins.notify(this, t, e, s);
    }

    isPluginEnabled(t) {
        return (
            this._plugins._cache.filter((e) => e.plugin.id === t).length === 1
        );
    }

    _updateHoverStyles(t, e, s) {
        const n = this.options.hover;
        const o = (l, c) =>
            l.filter(
                (h) =>
                    !c.some(
                        (d) =>
                            h.datasetIndex === d.datasetIndex &&
                            h.index === d.index,
                    ),
            );
        const a = o(e, t);
        const r = s ? t : o(t, e);
        (a.length && this.updateHoverStyle(a, n.mode, !1),
            r.length && n.mode && this.updateHoverStyle(r, n.mode, !0));
    }

    _eventHandler(t, e) {
        const s = {
            event: t,
            replay: e,
            cancelable: !0,
            inChartArea: this.isPointInArea(t),
        };
        const n = (a) =>
            (a.options.events || this.options.events).includes(t.native.type);
        if (this.notifyPlugins("beforeEvent", s, n) === !1) return;
        const o = this._handleEvent(t, e, s.inChartArea);
        return (
            (s.cancelable = !1),
            this.notifyPlugins("afterEvent", s, n),
            (o || s.changed) && this.render(),
            this
        );
    }

    _handleEvent(t, e, s) {
        const { _active: n = [], options: o } = this;
        const a = e;
        const r = this._getActiveElements(t, n, s, a);
        const l = nn(t);
        const c = Yl(t, this._lastEvent, s, l);
        s &&
            ((this._lastEvent = null),
            F(o.onHover, [t, r, this], this),
            l && F(o.onClick, [t, r, this], this));
        const h = !ke(r, n);
        return (
            (h || e) && ((this._active = r), this._updateHoverStyles(r, n, e)),
            (this._lastEvent = c),
            h
        );
    }

    _getActiveElements(t, e, s, n) {
        if (t.type === "mouseout") return [];
        if (!s) return e;
        const o = this.options.hover;
        return this.getElementsAtEventForMode(t, o.mode, o, n);
    }
};
(M(at, "defaults", V),
    M(at, "instances", yi),
    M(at, "overrides", Dt),
    M(at, "registry", gt),
    M(at, "version", Nl),
    M(at, "getChart", io));
function so() {
    return E(at.instances, (i) => i._plugins.invalidate());
}
function Ul(i, t, e) {
    const {
        startAngle: s,
        x: n,
        y: o,
        outerRadius: a,
        innerRadius: r,
        options: l,
    } = t;
    const { borderWidth: c, borderJoinStyle: h } = l;
    const d = Math.min(c / a, X(s - e));
    if ((i.beginPath(), i.arc(n, o, a - c / 2, s + d / 2, e - d / 2), r > 0)) {
        const u = Math.min(c / r, X(s - e));
        i.arc(n, o, r + c / 2, e - u / 2, s + u / 2, !0);
    } else {
        const u = Math.min(c / 2, a * X(s - e));
        if (h === "round") i.arc(n, o, u, e - R / 2, s + R / 2, !0);
        else if (h === "bevel") {
            const f = 2 * u * u;
            const g = -f * Math.cos(e + R / 2) + n;
            const p = -f * Math.sin(e + R / 2) + o;
            const m = f * Math.cos(s + R / 2) + n;
            const b = f * Math.sin(s + R / 2) + o;
            (i.lineTo(g, p), i.lineTo(m, b));
        }
    }
    (i.closePath(),
        i.moveTo(0, 0),
        i.rect(0, 0, i.canvas.width, i.canvas.height),
        i.clip("evenodd"));
}
function Xl(i, t, e) {
    const {
        startAngle: s,
        pixelMargin: n,
        x: o,
        y: a,
        outerRadius: r,
        innerRadius: l,
    } = t;
    let c = n / r;
    (i.beginPath(),
        i.arc(o, a, r, s - c, e + c),
        l > n
            ? ((c = n / l), i.arc(o, a, l, e + c, s - c, !0))
            : i.arc(o, a, n, e + H, s - H),
        i.closePath(),
        i.clip());
}
function Kl(i) {
    return li(i, ["outerStart", "outerEnd", "innerStart", "innerEnd"]);
}
function ql(i, t, e, s) {
    const n = Kl(i.options.borderRadius);
    const o = (e - t) / 2;
    const a = Math.min(o, (s * t) / 2);
    const r = (l) => {
        const c = ((e - Math.min(o, l)) * s) / 2;
        return Y(l, 0, Math.min(o, c));
    };
    return {
        outerStart: r(n.outerStart),
        outerEnd: r(n.outerEnd),
        innerStart: Y(n.innerStart, 0, a),
        innerEnd: Y(n.innerEnd, 0, a),
    };
}
function ae(i, t, e, s) {
    return { x: e + i * Math.cos(t), y: s + i * Math.sin(t) };
}
function wi(i, t, e, s, n, o) {
    const { x: a, y: r, startAngle: l, pixelMargin: c, innerRadius: h } = t;
    const d = Math.max(t.outerRadius + s + e - c, 0);
    const u = h > 0 ? h + s + e + c : 0;
    let f = 0;
    const g = n - l;
    if (s) {
        const I = h > 0 ? h - s : 0;
        const W = d > 0 ? d - s : 0;
        const j = (I + W) / 2;
        const rt = j !== 0 ? (g * j) / (j + s) : g;
        f = (g - rt) / 2;
    }
    const p = Math.max(0.001, g * d - e / R) / d;
    const m = (g - p) / 2;
    const b = l + m + f;
    const x = n - m - f;
    const {
        outerStart: v,
        outerEnd: y,
        innerStart: _,
        innerEnd: k,
    } = ql(t, u, d, x - b);
    const w = d - v;
    const S = d - y;
    const P = b + v / w;
    const O = x - y / S;
    const C = u + _;
    const L = u + k;
    const U = b + _ / C;
    const et = x - k / L;
    if ((i.beginPath(), o)) {
        const I = (P + O) / 2;
        if ((i.arc(a, r, d, P, I), i.arc(a, r, d, I, O), y > 0)) {
            const G = ae(S, O, a, r);
            i.arc(G.x, G.y, y, O, x + H);
        }
        const W = ae(L, x, a, r);
        if ((i.lineTo(W.x, W.y), k > 0)) {
            const G = ae(L, et, a, r);
            i.arc(G.x, G.y, k, x + H, et + Math.PI);
        }
        const j = (x - k / u + (b + _ / u)) / 2;
        if (
            (i.arc(a, r, u, x - k / u, j, !0),
            i.arc(a, r, u, j, b + _ / u, !0),
            _ > 0)
        ) {
            const G = ae(C, U, a, r);
            i.arc(G.x, G.y, _, U + Math.PI, b - H);
        }
        const rt = ae(w, b, a, r);
        if ((i.lineTo(rt.x, rt.y), v > 0)) {
            const G = ae(w, P, a, r);
            i.arc(G.x, G.y, v, b - H, P);
        }
    } else {
        i.moveTo(a, r);
        const I = Math.cos(P) * d + a;
        const W = Math.sin(P) * d + r;
        i.lineTo(I, W);
        const j = Math.cos(O) * d + a;
        const rt = Math.sin(O) * d + r;
        i.lineTo(j, rt);
    }
    i.closePath();
}
function Gl(i, t, e, s, n) {
    const { fullCircles: o, startAngle: a, circumference: r } = t;
    let l = t.endAngle;
    if (o) {
        wi(i, t, e, s, l, n);
        for (let c = 0; c < o; ++c) i.fill();
        isNaN(r) || (l = a + (r % B || B));
    }
    return (wi(i, t, e, s, l, n), i.fill(), l);
}
function Zl(i, t, e, s, n) {
    const { fullCircles: o, startAngle: a, circumference: r, options: l } = t;
    const {
        borderWidth: c,
        borderJoinStyle: h,
        borderDash: d,
        borderDashOffset: u,
        borderRadius: f,
    } = l;
    const g = l.borderAlign === "inner";
    if (!c) return;
    (i.setLineDash(d || []),
        (i.lineDashOffset = u),
        g
            ? ((i.lineWidth = c * 2), (i.lineJoin = h || "round"))
            : ((i.lineWidth = c), (i.lineJoin = h || "bevel")));
    let p = t.endAngle;
    if (o) {
        wi(i, t, e, s, p, n);
        for (let m = 0; m < o; ++m) i.stroke();
        isNaN(r) || (p = a + (r % B || B));
    }
    (g && Xl(i, t, p),
        l.selfJoin && p - a >= R && f === 0 && h !== "miter" && Ul(i, t, p),
        o || (wi(i, t, e, s, p, n), i.stroke()));
}
const Ht = class extends st {
    constructor(e) {
        super();
        M(this, "circumference");
        M(this, "endAngle");
        M(this, "fullCircles");
        M(this, "innerRadius");
        M(this, "outerRadius");
        M(this, "pixelMargin");
        M(this, "startAngle");
        ((this.options = void 0),
            (this.circumference = void 0),
            (this.startAngle = void 0),
            (this.endAngle = void 0),
            (this.innerRadius = void 0),
            (this.outerRadius = void 0),
            (this.pixelMargin = 0),
            (this.fullCircles = 0),
            e && Object.assign(this, e));
    }

    inRange(e, s, n) {
        const o = this.getProps(["x", "y"], n);
        const { angle: a, distance: r } = Hi(o, { x: e, y: s });
        const {
            startAngle: l,
            endAngle: c,
            innerRadius: h,
            outerRadius: d,
            circumference: u,
        } = this.getProps(
            [
                "startAngle",
                "endAngle",
                "innerRadius",
                "outerRadius",
                "circumference",
            ],
            n,
        );
        const f = (this.options.spacing + this.options.borderWidth) / 2;
        const g = D(u, c - l);
        const p = ie(a, l, c) && l !== c;
        const m = g >= B || p;
        const b = ut(r, h + f, d + f);
        return m && b;
    }

    getCenterPoint(e) {
        const {
            x: s,
            y: n,
            startAngle: o,
            endAngle: a,
            innerRadius: r,
            outerRadius: l,
        } = this.getProps(
            ["x", "y", "startAngle", "endAngle", "innerRadius", "outerRadius"],
            e,
        );
        const { offset: c, spacing: h } = this.options;
        const d = (o + a) / 2;
        const u = (r + l + h + c) / 2;
        return { x: s + Math.cos(d) * u, y: n + Math.sin(d) * u };
    }

    tooltipPosition(e) {
        return this.getCenterPoint(e);
    }

    draw(e) {
        const { options: s, circumference: n } = this;
        const o = (s.offset || 0) / 4;
        const a = (s.spacing || 0) / 2;
        const r = s.circular;
        if (
            ((this.pixelMargin = s.borderAlign === "inner" ? 0.33 : 0),
            (this.fullCircles = n > B ? Math.floor(n / B) : 0),
            n === 0 || this.innerRadius < 0 || this.outerRadius < 0)
        ) {
            return;
        }
        e.save();
        const l = (this.startAngle + this.endAngle) / 2;
        e.translate(Math.cos(l) * o, Math.sin(l) * o);
        const c = 1 - Math.sin(Math.min(R, n || 0));
        const h = o * c;
        ((e.fillStyle = s.backgroundColor),
            (e.strokeStyle = s.borderColor),
            Gl(e, this, h, a, r),
            Zl(e, this, h, a, r),
            e.restore());
    }
};
(M(Ht, "id", "arc"),
    M(Ht, "defaults", {
        borderAlign: "center",
        borderColor: "#fff",
        borderDash: [],
        borderDashOffset: 0,
        borderJoinStyle: void 0,
        borderRadius: 0,
        borderWidth: 2,
        offset: 0,
        spacing: 0,
        angle: void 0,
        circular: !0,
        selfJoin: !1,
    }),
    M(Ht, "defaultRoutes", { backgroundColor: "backgroundColor" }),
    M(Ht, "descriptors", {
        _scriptable: !0,
        _indexable: (e) => e !== "borderDash",
    }));
function Wo(i, t, e = t) {
    ((i.lineCap = D(e.borderCapStyle, t.borderCapStyle)),
        i.setLineDash(D(e.borderDash, t.borderDash)),
        (i.lineDashOffset = D(e.borderDashOffset, t.borderDashOffset)),
        (i.lineJoin = D(e.borderJoinStyle, t.borderJoinStyle)),
        (i.lineWidth = D(e.borderWidth, t.borderWidth)),
        (i.strokeStyle = D(e.borderColor, t.borderColor)));
}
function Jl(i, t, e) {
    i.lineTo(e.x, e.y);
}
function Ql(i) {
    return i.stepped
        ? mn
        : i.tension || i.cubicInterpolationMode === "monotone"
          ? bn
          : Jl;
}
function No(i, t, e = {}) {
    const s = i.length;
    const { start: n = 0, end: o = s - 1 } = e;
    const { start: a, end: r } = t;
    const l = Math.max(n, a);
    const c = Math.min(o, r);
    const h = (n < a && o < a) || (n > r && o > r);
    return {
        count: s,
        start: l,
        loop: t.loop,
        ilen: c < l && !h ? s + c - l : c - l,
    };
}
function tc(i, t, e, s) {
    const { points: n, options: o } = t;
    const { count: a, start: r, loop: l, ilen: c } = No(n, e, s);
    const h = Ql(o);
    let { move: d = !0, reverse: u } = s || {};
    let f;
    let g;
    let p;
    for (f = 0; f <= c; ++f) {
        ((g = n[(r + (u ? c - f : f)) % a]),
            !g.skip &&
                (d ? (i.moveTo(g.x, g.y), (d = !1)) : h(i, p, g, u, o.stepped),
                (p = g)));
    }
    return (
        l && ((g = n[(r + (u ? c : 0)) % a]), h(i, p, g, u, o.stepped)),
        !!l
    );
}
function ec(i, t, e, s) {
    const n = t.points;
    const { count: o, start: a, ilen: r } = No(n, e, s);
    const { move: l = !0, reverse: c } = s || {};
    let h = 0;
    let d = 0;
    let u;
    let f;
    let g;
    let p;
    let m;
    let b;
    const x = (y) => (a + (c ? r - y : y)) % o;
    const v = () => {
        p !== m && (i.lineTo(h, m), i.lineTo(h, p), i.lineTo(h, b));
    };
    for (l && ((f = n[x(0)]), i.moveTo(f.x, f.y)), u = 0; u <= r; ++u) {
        if (((f = n[x(u)]), f.skip)) continue;
        const y = f.x;
        const _ = f.y;
        const k = y | 0;
        (k === g
            ? (_ < p ? (p = _) : _ > m && (m = _), (h = (d * h + y) / ++d))
            : (v(), i.lineTo(y, _), (g = k), (d = 0), (p = m = _)),
            (b = _));
    }
    v();
}
function Ls(i) {
    const t = i.options;
    const e = t.borderDash && t.borderDash.length;
    return !i._decimated &&
        !i._loop &&
        !t.tension &&
        t.cubicInterpolationMode !== "monotone" &&
        !t.stepped &&
        !e
        ? ec
        : tc;
}
function ic(i) {
    return i.stepped
        ? Pn
        : i.tension || i.cubicInterpolationMode === "monotone"
          ? Dn
          : Pt;
}
function sc(i, t, e, s) {
    let n = t._path;
    (n || ((n = t._path = new Path2D()), t.path(n, e, s) && n.closePath()),
        Wo(i, t.options),
        i.stroke(n));
}
function nc(i, t, e, s) {
    const { segments: n, options: o } = t;
    const a = Ls(t);
    for (const r of n) {
        (Wo(i, o, r.style),
            i.beginPath(),
            a(i, t, r, { start: e, end: e + s - 1 }) && i.closePath(),
            i.stroke());
    }
}
const oc = typeof Path2D === "function";
function ac(i, t, e, s) {
    oc && !t.options.segment ? sc(i, t, e, s) : nc(i, t, e, s);
}
const pt = class extends st {
    constructor(t) {
        (super(),
            (this.animated = !0),
            (this.options = void 0),
            (this._chart = void 0),
            (this._loop = void 0),
            (this._fullLoop = void 0),
            (this._path = void 0),
            (this._points = void 0),
            (this._segments = void 0),
            (this._decimated = !1),
            (this._pointsUpdated = !1),
            (this._datasetIndex = void 0),
            t && Object.assign(this, t));
    }

    updateControlPoints(t, e) {
        const s = this.options;
        if (
            (s.tension || s.cubicInterpolationMode === "monotone") &&
            !s.stepped &&
            !this._pointsUpdated
        ) {
            const n = s.spanGaps ? this._loop : this._fullLoop;
            (kn(this._points, s, t, n, e), (this._pointsUpdated = !0));
        }
    }

    set points(t) {
        ((this._points = t),
            delete this._segments,
            delete this._path,
            (this._pointsUpdated = !1));
    }

    get points() {
        return this._points;
    }

    get segments() {
        return (
            this._segments || (this._segments = Cn(this, this.options.segment))
        );
    }

    first() {
        const t = this.segments;
        const e = this.points;
        return t.length && e[t[0].start];
    }

    last() {
        const t = this.segments;
        const e = this.points;
        const s = t.length;
        return s && e[t[s - 1].end];
    }

    interpolate(t, e) {
        const s = this.options;
        const n = t[e];
        const o = this.points;
        const a = cs(this, { property: e, start: n, end: n });
        if (!a.length) return;
        const r = [];
        const l = ic(s);
        let c;
        let h;
        for (c = 0, h = a.length; c < h; ++c) {
            const { start: d, end: u } = a[c];
            const f = o[d];
            const g = o[u];
            if (f === g) {
                r.push(f);
                continue;
            }
            const p = Math.abs((n - f[e]) / (g[e] - f[e]));
            const m = l(f, g, p, s.stepped);
            ((m[e] = t[e]), r.push(m));
        }
        return r.length === 1 ? r[0] : r;
    }

    pathSegment(t, e, s) {
        return Ls(this)(t, this, e, s);
    }

    path(t, e, s) {
        const n = this.segments;
        const o = Ls(this);
        let a = this._loop;
        ((e = e || 0), (s = s || this.points.length - e));
        for (const r of n) a &= o(t, this, r, { start: e, end: e + s - 1 });
        return !!a;
    }

    draw(t, e, s, n) {
        const o = this.options || {};
        ((this.points || []).length &&
            o.borderWidth &&
            (t.save(), ac(t, this, s, n), t.restore()),
            this.animated &&
                ((this._pointsUpdated = !1), (this._path = void 0)));
    }
};
(M(pt, "id", "line"),
    M(pt, "defaults", {
        borderCapStyle: "butt",
        borderDash: [],
        borderDashOffset: 0,
        borderJoinStyle: "miter",
        borderWidth: 3,
        capBezierPoints: !0,
        cubicInterpolationMode: "default",
        fill: !1,
        spanGaps: !1,
        stepped: !1,
        tension: 0,
    }),
    M(pt, "defaultRoutes", {
        backgroundColor: "backgroundColor",
        borderColor: "borderColor",
    }),
    M(pt, "descriptors", {
        _scriptable: !0,
        _indexable: (t) => t !== "borderDash" && t !== "fill",
    }));
function no(i, t, e, s) {
    const n = i.options;
    const { [e]: o } = i.getProps([e], s);
    return Math.abs(t - o) < n.radius + n.hitRadius;
}
const ue = class extends st {
    constructor(e) {
        super();
        M(this, "parsed");
        M(this, "skip");
        M(this, "stop");
        ((this.options = void 0),
            (this.parsed = void 0),
            (this.skip = void 0),
            (this.stop = void 0),
            e && Object.assign(this, e));
    }

    inRange(e, s, n) {
        const o = this.options;
        const { x: a, y: r } = this.getProps(["x", "y"], n);
        return (
            Math.pow(e - a, 2) + Math.pow(s - r, 2) <
            Math.pow(o.hitRadius + o.radius, 2)
        );
    }

    inXRange(e, s) {
        return no(this, e, "x", s);
    }

    inYRange(e, s) {
        return no(this, e, "y", s);
    }

    getCenterPoint(e) {
        const { x: s, y: n } = this.getProps(["x", "y"], e);
        return { x: s, y: n };
    }

    size(e) {
        e = e || this.options || {};
        let s = e.radius || 0;
        s = Math.max(s, (s && e.hoverRadius) || 0);
        const n = (s && e.borderWidth) || 0;
        return (s + n) * 2;
    }

    draw(e, s) {
        const n = this.options;
        this.skip ||
            n.radius < 0.1 ||
            !ht(this, s, this.size(n) / 2) ||
            ((e.strokeStyle = n.borderColor),
            (e.lineWidth = n.borderWidth),
            (e.fillStyle = n.backgroundColor),
            ri(e, n, this.x, this.y));
    }

    getRange() {
        const e = this.options || {};
        return e.radius + e.hitRadius;
    }
};
(M(ue, "id", "point"),
    M(ue, "defaults", {
        borderWidth: 1,
        hitRadius: 1,
        hoverBorderWidth: 1,
        hoverRadius: 4,
        pointStyle: "circle",
        radius: 3,
        rotation: 0,
    }),
    M(ue, "defaultRoutes", {
        backgroundColor: "backgroundColor",
        borderColor: "borderColor",
    }));
function Ho(i, t) {
    const {
        x: e,
        y: s,
        base: n,
        width: o,
        height: a,
    } = i.getProps(["x", "y", "base", "width", "height"], t);
    let r;
    let l;
    let c;
    let h;
    let d;
    return (
        i.horizontal
            ? ((d = a / 2),
              (r = Math.min(e, n)),
              (l = Math.max(e, n)),
              (c = s - d),
              (h = s + d))
            : ((d = o / 2),
              (r = e - d),
              (l = e + d),
              (c = Math.min(s, n)),
              (h = Math.max(s, n))),
        { left: r, top: c, right: l, bottom: h }
    );
}
function Lt(i, t, e, s) {
    return i ? 0 : Y(t, e, s);
}
function rc(i, t, e) {
    const s = i.options.borderWidth;
    const n = i.borderSkipped;
    const o = Qi(s);
    return {
        t: Lt(n.top, o.top, 0, e),
        r: Lt(n.right, o.right, 0, t),
        b: Lt(n.bottom, o.bottom, 0, e),
        l: Lt(n.left, o.left, 0, t),
    };
}
function lc(i, t, e) {
    const { enableBorderRadius: s } = i.getProps(["enableBorderRadius"]);
    const n = i.options.borderRadius;
    const o = At(n);
    const a = Math.min(t, e);
    const r = i.borderSkipped;
    const l = s || T(n);
    return {
        topLeft: Lt(!l || r.top || r.left, o.topLeft, 0, a),
        topRight: Lt(!l || r.top || r.right, o.topRight, 0, a),
        bottomLeft: Lt(!l || r.bottom || r.left, o.bottomLeft, 0, a),
        bottomRight: Lt(!l || r.bottom || r.right, o.bottomRight, 0, a),
    };
}
function cc(i) {
    const t = Ho(i);
    const e = t.right - t.left;
    const s = t.bottom - t.top;
    const n = rc(i, e / 2, s / 2);
    const o = lc(i, e / 2, s / 2);
    return {
        outer: { x: t.left, y: t.top, w: e, h: s, radius: o },
        inner: {
            x: t.left + n.l,
            y: t.top + n.t,
            w: e - n.l - n.r,
            h: s - n.t - n.b,
            radius: {
                topLeft: Math.max(0, o.topLeft - Math.max(n.t, n.l)),
                topRight: Math.max(0, o.topRight - Math.max(n.t, n.r)),
                bottomLeft: Math.max(0, o.bottomLeft - Math.max(n.b, n.l)),
                bottomRight: Math.max(0, o.bottomRight - Math.max(n.b, n.r)),
            },
        },
    };
}
function xs(i, t, e, s) {
    const n = t === null;
    const o = e === null;
    const r = i && !(n && o) && Ho(i, s);
    return r && (n || ut(t, r.left, r.right)) && (o || ut(e, r.top, r.bottom));
}
function hc(i) {
    return i.topLeft || i.topRight || i.bottomLeft || i.bottomRight;
}
function dc(i, t) {
    i.rect(t.x, t.y, t.w, t.h);
}
function _s(i, t, e = {}) {
    const s = i.x !== e.x ? -t : 0;
    const n = i.y !== e.y ? -t : 0;
    const o = (i.x + i.w !== e.x + e.w ? t : 0) - s;
    const a = (i.y + i.h !== e.y + e.h ? t : 0) - n;
    return { x: i.x + s, y: i.y + n, w: i.w + o, h: i.h + a, radius: i.radius };
}
const fe = class extends st {
    constructor(t) {
        (super(),
            (this.options = void 0),
            (this.horizontal = void 0),
            (this.base = void 0),
            (this.width = void 0),
            (this.height = void 0),
            (this.inflateAmount = void 0),
            t && Object.assign(this, t));
    }

    draw(t) {
        const {
            inflateAmount: e,
            options: { borderColor: s, backgroundColor: n },
        } = this;
        const { inner: o, outer: a } = cc(this);
        const r = hc(a.radius) ? ne : dc;
        (t.save(),
            (a.w !== o.w || a.h !== o.h) &&
                (t.beginPath(),
                r(t, _s(a, e, o)),
                t.clip(),
                r(t, _s(o, -e, a)),
                (t.fillStyle = s),
                t.fill("evenodd")),
            t.beginPath(),
            r(t, _s(o, e)),
            (t.fillStyle = n),
            t.fill(),
            t.restore());
    }

    inRange(t, e, s) {
        return xs(this, t, e, s);
    }

    inXRange(t, e) {
        return xs(this, t, null, e);
    }

    inYRange(t, e) {
        return xs(this, null, t, e);
    }

    getCenterPoint(t) {
        const {
            x: e,
            y: s,
            base: n,
            horizontal: o,
        } = this.getProps(["x", "y", "base", "horizontal"], t);
        return { x: o ? (e + n) / 2 : e, y: o ? s : (s + n) / 2 };
    }

    getRange(t) {
        return t === "x" ? this.width / 2 : this.height / 2;
    }
};
(M(fe, "id", "bar"),
    M(fe, "defaults", {
        borderSkipped: "start",
        borderWidth: 0,
        borderRadius: 0,
        inflateAmount: "auto",
        pointStyle: void 0,
    }),
    M(fe, "defaultRoutes", {
        backgroundColor: "backgroundColor",
        borderColor: "borderColor",
    }));
const uc = Object.freeze({
    __proto__: null,
    ArcElement: Ht,
    BarElement: fe,
    LineElement: pt,
    PointElement: ue,
});
const Rs = [
    "rgb(54, 162, 235)",
    "rgb(255, 99, 132)",
    "rgb(255, 159, 64)",
    "rgb(255, 205, 86)",
    "rgb(75, 192, 192)",
    "rgb(153, 102, 255)",
    "rgb(201, 203, 207)",
];
const oo = Rs.map((i) => i.replace("rgb(", "rgba(").replace(")", ", 0.5)"));
function jo(i) {
    return Rs[i % Rs.length];
}
function $o(i) {
    return oo[i % oo.length];
}
function fc(i, t) {
    return ((i.borderColor = jo(t)), (i.backgroundColor = $o(t)), ++t);
}
function gc(i, t) {
    return ((i.backgroundColor = i.data.map(() => jo(t++))), t);
}
function pc(i, t) {
    return ((i.backgroundColor = i.data.map(() => $o(t++))), t);
}
function mc(i) {
    let t = 0;
    return (e, s) => {
        const n = i.getDatasetMeta(s).controller;
        n instanceof kt
            ? (t = gc(e, t))
            : n instanceof $t
              ? (t = pc(e, t))
              : n && (t = fc(e, t));
    };
}
function ao(i) {
    let t;
    for (t in i) if (i[t].borderColor || i[t].backgroundColor) return !0;
    return !1;
}
function bc(i) {
    return i && (i.borderColor || i.backgroundColor);
}
function xc() {
    return (
        V.borderColor !== "rgba(0,0,0,0.1)" ||
        V.backgroundColor !== "rgba(0,0,0,0.1)"
    );
}
const _c = {
    id: "colors",
    defaults: { enabled: !0, forceOverride: !1 },
    beforeLayout(i, t, e) {
        if (!e.enabled) return;
        const {
            data: { datasets: s },
            options: n,
        } = i.config;
        const { elements: o } = n;
        const a = ao(s) || bc(n) || (o && ao(o)) || xc();
        if (!e.forceOverride && a) return;
        const r = mc(i);
        s.forEach(r);
    },
};
function yc(i, t, e, s, n) {
    const o = n.samples || s;
    if (o >= e) return i.slice(t, t + e);
    const a = [];
    const r = (e - 2) / (o - 2);
    let l = 0;
    const c = t + e - 1;
    let h = t;
    let d;
    let u;
    let f;
    let g;
    let p;
    for (a[l++] = i[h], d = 0; d < o - 2; d++) {
        let m = 0;
        let b = 0;
        let x;
        const v = Math.floor((d + 1) * r) + 1 + t;
        const y = Math.min(Math.floor((d + 2) * r) + 1, e) + t;
        const _ = y - v;
        for (x = v; x < y; x++) ((m += i[x].x), (b += i[x].y));
        ((m /= _), (b /= _));
        const k = Math.floor(d * r) + 1 + t;
        const w = Math.min(Math.floor((d + 1) * r) + 1, e) + t;
        const { x: S, y: P } = i[h];
        for (f = g = -1, x = k; x < w; x++) {
            ((g =
                0.5 *
                Math.abs((S - m) * (i[x].y - P) - (S - i[x].x) * (b - P))),
                g > f && ((f = g), (u = i[x]), (p = x)));
        }
        ((a[l++] = u), (h = p));
    }
    return ((a[l++] = i[c]), a);
}
function vc(i, t, e, s) {
    let n = 0;
    let o = 0;
    let a;
    let r;
    let l;
    let c;
    let h;
    let d;
    let u;
    let f;
    let g;
    let p;
    const m = [];
    const b = t + e - 1;
    const x = i[t].x;
    const y = i[b].x - x;
    for (a = t; a < t + e; ++a) {
        ((r = i[a]), (l = ((r.x - x) / y) * s), (c = r.y));
        const _ = l | 0;
        if (_ === h) {
            (c < g ? ((g = c), (d = a)) : c > p && ((p = c), (u = a)),
                (n = (o * n + r.x) / ++o));
        } else {
            const k = a - 1;
            if (!A(d) && !A(u)) {
                const w = Math.min(d, u);
                const S = Math.max(d, u);
                (w !== f && w !== k && m.push({ ...i[w], x: n }),
                    S !== f && S !== k && m.push({ ...i[S], x: n }));
            }
            (a > 0 && k !== f && m.push(i[k]),
                m.push(r),
                (h = _),
                (o = 0),
                (g = p = c),
                (d = u = f = a));
        }
    }
    return m;
}
function Yo(i) {
    if (i._decimated) {
        const t = i._data;
        (delete i._decimated,
            delete i._data,
            Object.defineProperty(i, "data", {
                configurable: !0,
                enumerable: !0,
                writable: !0,
                value: t,
            }));
    }
}
function ro(i) {
    i.data.datasets.forEach((t) => {
        Yo(t);
    });
}
function Mc(i, t) {
    const e = t.length;
    let s = 0;
    let n;
    const { iScale: o } = i;
    const { min: a, max: r, minDefined: l, maxDefined: c } = o.getUserBounds();
    return (
        l && (s = Y(ct(t, o.axis, a).lo, 0, e - 1)),
        c ? (n = Y(ct(t, o.axis, r).hi + 1, s, e) - s) : (n = e - s),
        { start: s, count: n }
    );
}
const kc = {
    id: "decimation",
    defaults: { algorithm: "min-max", enabled: !1 },
    beforeElementsUpdate: (i, t, e) => {
        if (!e.enabled) {
            ro(i);
            return;
        }
        const s = i.width;
        i.data.datasets.forEach((n, o) => {
            const { _data: a, indexAxis: r } = n;
            const l = i.getDatasetMeta(o);
            const c = a || n.data;
            if (
                oe([r, i.options.indexAxis]) === "y" ||
                !l.controller.supportsDecimation
            ) {
                return;
            }
            const h = i.scales[l.xAxisID];
            if (
                (h.type !== "linear" && h.type !== "time") ||
                i.options.parsing
            ) {
                return;
            }
            const { start: d, count: u } = Mc(l, c);
            const f = e.threshold || 4 * s;
            if (u <= f) {
                Yo(n);
                return;
            }
            A(a) &&
                ((n._data = c),
                delete n.data,
                Object.defineProperty(n, "data", {
                    configurable: !0,
                    enumerable: !0,
                    get: function () {
                        return this._decimated;
                    },
                    set: function (p) {
                        this._data = p;
                    },
                }));
            let g;
            switch (e.algorithm) {
                case "lttb":
                    g = yc(c, d, u, s, e);
                    break;
                case "min-max":
                    g = vc(c, d, u, s);
                    break;
                default:
                    throw new Error(
                        `Unsupported decimation algorithm '${e.algorithm}'`,
                    );
            }
            n._decimated = g;
        });
    },
    destroy(i) {
        ro(i);
    },
};
function wc(i, t, e) {
    const s = i.segments;
    const n = i.points;
    const o = t.points;
    const a = [];
    for (const r of s) {
        let { start: l, end: c } = r;
        c = Di(l, c, n);
        const h = Es(e, n[l], n[c], r.loop);
        if (!t.segments) {
            a.push({ source: r, target: h, start: n[l], end: n[c] });
            continue;
        }
        const d = cs(t, h);
        for (const u of d) {
            const f = Es(e, o[u.start], o[u.end], u.loop);
            const g = ls(r, n, f);
            for (const p of g) {
                a.push({
                    source: p,
                    target: u,
                    start: { [e]: lo(h, f, "start", Math.max) },
                    end: { [e]: lo(h, f, "end", Math.min) },
                });
            }
        }
    }
    return a;
}
function Es(i, t, e, s) {
    if (s) return;
    let n = t[i];
    let o = e[i];
    return (
        i === "angle" && ((n = X(n)), (o = X(o))),
        { property: i, start: n, end: o }
    );
}
function Sc(i, t) {
    const { x: e = null, y: s = null } = i || {};
    const n = t.points;
    const o = [];
    return (
        t.segments.forEach(({ start: a, end: r }) => {
            r = Di(a, r, n);
            const l = n[a];
            const c = n[r];
            s !== null
                ? (o.push({ x: l.x, y: s }), o.push({ x: c.x, y: s }))
                : e !== null &&
                  (o.push({ x: e, y: l.y }), o.push({ x: e, y: c.y }));
        }),
        o
    );
}
function Di(i, t, e) {
    for (; t > i; t--) {
        const s = e[t];
        if (!isNaN(s.x) && !isNaN(s.y)) break;
    }
    return t;
}
function lo(i, t, e, s) {
    return i && t ? s(i[e], t[e]) : i ? i[e] : t ? t[e] : 0;
}
function Uo(i, t) {
    let e = [];
    let s = !1;
    return (
        z(i) ? ((s = !0), (e = i)) : (e = Sc(i, t)),
        e.length
            ? new pt({
                  points: e,
                  options: { tension: 0 },
                  _loop: s,
                  _fullLoop: s,
              })
            : null
    );
}
function co(i) {
    return i && i.fill !== !1;
}
function Pc(i, t, e) {
    let n = i[t].fill;
    const o = [t];
    let a;
    if (!e) return n;
    for (; n !== !1 && o.indexOf(n) === -1; ) {
        if (!N(n)) return n;
        if (((a = i[n]), !a)) return !1;
        if (a.visible) return n;
        (o.push(n), (n = a.fill));
    }
    return !1;
}
function Dc(i, t, e) {
    const s = Tc(i);
    if (T(s)) return isNaN(s.value) ? !1 : s;
    const n = parseFloat(s);
    return N(n) && Math.floor(n) === n
        ? Oc(s[0], t, n, e)
        : ["origin", "start", "end", "stack", "shape"].indexOf(s) >= 0 && s;
}
function Oc(i, t, e, s) {
    return (
        (i === "-" || i === "+") && (e = t + e),
        e === t || e < 0 || e >= s ? !1 : e
    );
}
function Cc(i, t) {
    let e = null;
    return (
        i === "start"
            ? (e = t.bottom)
            : i === "end"
              ? (e = t.top)
              : T(i)
                ? (e = t.getPixelForValue(i.value))
                : t.getBasePixel && (e = t.getBasePixel()),
        e
    );
}
function Ac(i, t, e) {
    let s;
    return (
        i === "start"
            ? (s = e)
            : i === "end"
              ? (s = t.options.reverse ? t.min : t.max)
              : T(i)
                ? (s = i.value)
                : (s = t.getBaseValue()),
        s
    );
}
function Tc(i) {
    const t = i.options;
    const e = t.fill;
    let s = D(e && e.target, e);
    return (
        s === void 0 && (s = !!t.backgroundColor),
        s === !1 || s === null ? !1 : s === !0 ? "origin" : s
    );
}
function Lc(i) {
    const { scale: t, index: e, line: s } = i;
    const n = [];
    const o = s.segments;
    const a = s.points;
    const r = Rc(t, e);
    r.push(Uo({ x: null, y: t.bottom }, s));
    for (let l = 0; l < o.length; l++) {
        const c = o[l];
        for (let h = c.start; h <= c.end; h++) Ec(n, a[h], r);
    }
    return new pt({ points: n, options: {} });
}
function Rc(i, t) {
    const e = [];
    const s = i.getMatchingVisibleMetas("line");
    for (let n = 0; n < s.length; n++) {
        const o = s[n];
        if (o.index === t) break;
        o.hidden || e.unshift(o.dataset);
    }
    return e;
}
function Ec(i, t, e) {
    const s = [];
    for (let n = 0; n < e.length; n++) {
        const o = e[n];
        const { first: a, last: r, point: l } = Ic(o, t, "x");
        if (!(!l || (a && r))) {
            if (a) s.unshift(l);
            else if ((i.push(l), !r)) break;
        }
    }
    i.push(...s);
}
function Ic(i, t, e) {
    const s = i.interpolate(t, e);
    if (!s) return {};
    const n = s[e];
    const o = i.segments;
    const a = i.points;
    let r = !1;
    let l = !1;
    for (let c = 0; c < o.length; c++) {
        const h = o[c];
        const d = a[h.start][e];
        const u = a[h.end][e];
        if (ut(n, d, u)) {
            ((r = n === d), (l = n === u));
            break;
        }
    }
    return { first: r, last: l, point: s };
}
const Si = class {
    constructor(t) {
        ((this.x = t.x), (this.y = t.y), (this.radius = t.radius));
    }

    pathSegment(t, e, s) {
        const { x: n, y: o, radius: a } = this;
        return (
            (e = e || { start: 0, end: B }),
            t.arc(n, o, a, e.end, e.start, !0),
            !s.bounds
        );
    }

    interpolate(t) {
        const { x: e, y: s, radius: n } = this;
        const o = t.angle;
        return { x: e + Math.cos(o) * n, y: s + Math.sin(o) * n, angle: o };
    }
};
function Fc(i) {
    const { chart: t, fill: e, line: s } = i;
    if (N(e)) return zc(t, e);
    if (e === "stack") return Lc(i);
    if (e === "shape") return !0;
    const n = Bc(i);
    return n instanceof Si ? n : Uo(n, s);
}
function zc(i, t) {
    const e = i.getDatasetMeta(t);
    return e && i.isDatasetVisible(t) ? e.dataset : null;
}
function Bc(i) {
    return (i.scale || {}).getPointPositionForValue ? Wc(i) : Vc(i);
}
function Vc(i) {
    const { scale: t = {}, fill: e } = i;
    const s = Cc(e, t);
    if (N(s)) {
        const n = t.isHorizontal();
        return { x: n ? s : null, y: n ? null : s };
    }
    return null;
}
function Wc(i) {
    const { scale: t, fill: e } = i;
    const s = t.options;
    const n = t.getLabels().length;
    const o = s.reverse ? t.max : t.min;
    const a = Ac(e, t, o);
    const r = [];
    if (s.grid.circular) {
        const l = t.getPointPositionForValue(0, o);
        return new Si({
            x: l.x,
            y: l.y,
            radius: t.getDistanceFromCenterForValue(a),
        });
    }
    for (let l = 0; l < n; ++l) r.push(t.getPointPositionForValue(l, a));
    return r;
}
function ys(i, t, e) {
    const s = Fc(t);
    const { chart: n, index: o, line: a, scale: r, axis: l } = t;
    const c = a.options;
    const h = c.fill;
    const d = c.backgroundColor;
    const { above: u = d, below: f = d } = h || {};
    const g = n.getDatasetMeta(o);
    const p = hs(n, g);
    s &&
        a.points.length &&
        (Se(i, e),
        Nc(i, {
            line: a,
            target: s,
            above: u,
            below: f,
            area: e,
            scale: r,
            axis: l,
            clip: p,
        }),
        Pe(i));
}
function Nc(i, t) {
    const {
        line: e,
        target: s,
        above: n,
        below: o,
        area: a,
        scale: r,
        clip: l,
    } = t;
    const c = e._loop ? "angle" : t.axis;
    i.save();
    let h = o;
    (o !== n &&
        (c === "x"
            ? (ho(i, s, a.top),
              vs(i, {
                  line: e,
                  target: s,
                  color: n,
                  scale: r,
                  property: c,
                  clip: l,
              }),
              i.restore(),
              i.save(),
              ho(i, s, a.bottom))
            : c === "y" &&
              (uo(i, s, a.left),
              vs(i, {
                  line: e,
                  target: s,
                  color: o,
                  scale: r,
                  property: c,
                  clip: l,
              }),
              i.restore(),
              i.save(),
              uo(i, s, a.right),
              (h = n))),
        vs(i, { line: e, target: s, color: h, scale: r, property: c, clip: l }),
        i.restore());
}
function ho(i, t, e) {
    const { segments: s, points: n } = t;
    let o = !0;
    let a = !1;
    i.beginPath();
    for (const r of s) {
        const { start: l, end: c } = r;
        const h = n[l];
        const d = n[Di(l, c, n)];
        (o
            ? (i.moveTo(h.x, h.y), (o = !1))
            : (i.lineTo(h.x, e), i.lineTo(h.x, h.y)),
            (a = !!t.pathSegment(i, r, { move: a })),
            a ? i.closePath() : i.lineTo(d.x, e));
    }
    (i.lineTo(t.first().x, e), i.closePath(), i.clip());
}
function uo(i, t, e) {
    const { segments: s, points: n } = t;
    let o = !0;
    let a = !1;
    i.beginPath();
    for (const r of s) {
        const { start: l, end: c } = r;
        const h = n[l];
        const d = n[Di(l, c, n)];
        (o
            ? (i.moveTo(h.x, h.y), (o = !1))
            : (i.lineTo(e, h.y), i.lineTo(h.x, h.y)),
            (a = !!t.pathSegment(i, r, { move: a })),
            a ? i.closePath() : i.lineTo(e, d.y));
    }
    (i.lineTo(e, t.first().y), i.closePath(), i.clip());
}
function vs(i, t) {
    const { line: e, target: s, property: n, color: o, scale: a, clip: r } = t;
    const l = wc(e, s, n);
    for (const { source: c, target: h, start: d, end: u } of l) {
        const { style: { backgroundColor: f = o } = {} } = c;
        const g = s !== !0;
        (i.save(),
            (i.fillStyle = f),
            Hc(i, a, r, g && Es(n, d, u)),
            i.beginPath());
        const p = !!e.pathSegment(i, c);
        let m;
        if (g) {
            p ? i.closePath() : fo(i, s, u, n);
            const b = !!s.pathSegment(i, h, { move: p, reverse: !0 });
            ((m = p && b), m || fo(i, s, d, n));
        }
        (i.closePath(), i.fill(m ? "evenodd" : "nonzero"), i.restore());
    }
}
function Hc(i, t, e, s) {
    const n = t.chart.chartArea;
    const { property: o, start: a, end: r } = s || {};
    if (o === "x" || o === "y") {
        let l, c, h, d;
        (o === "x"
            ? ((l = a), (c = n.top), (h = r), (d = n.bottom))
            : ((l = n.left), (c = a), (h = n.right), (d = r)),
            i.beginPath(),
            e &&
                ((l = Math.max(l, e.left)),
                (h = Math.min(h, e.right)),
                (c = Math.max(c, e.top)),
                (d = Math.min(d, e.bottom))),
            i.rect(l, c, h - l, d - c),
            i.clip());
    }
}
function fo(i, t, e, s) {
    const n = t.interpolate(e, s);
    n && i.lineTo(n.x, n.y);
}
const jc = {
    id: "filler",
    afterDatasetsUpdate(i, t, e) {
        const s = (i.data.datasets || []).length;
        const n = [];
        let o;
        let a;
        let r;
        let l;
        for (a = 0; a < s; ++a) {
            ((o = i.getDatasetMeta(a)),
                (r = o.dataset),
                (l = null),
                r &&
                    r.options &&
                    r instanceof pt &&
                    (l = {
                        visible: i.isDatasetVisible(a),
                        index: a,
                        fill: Dc(r, a, s),
                        chart: i,
                        axis: o.controller.options.indexAxis,
                        scale: o.vScale,
                        line: r,
                    }),
                (o.$filler = l),
                n.push(l));
        }
        for (a = 0; a < s; ++a) {
            ((l = n[a]),
                !(!l || l.fill === !1) && (l.fill = Pc(n, a, e.propagate)));
        }
    },
    beforeDraw(i, t, e) {
        const s = e.drawTime === "beforeDraw";
        const n = i.getSortedVisibleDatasetMetas();
        const o = i.chartArea;
        for (let a = n.length - 1; a >= 0; --a) {
            const r = n[a].$filler;
            r &&
                (r.line.updateControlPoints(o, r.axis),
                s && r.fill && ys(i.ctx, r, o));
        }
    },
    beforeDatasetsDraw(i, t, e) {
        if (e.drawTime !== "beforeDatasetsDraw") return;
        const s = i.getSortedVisibleDatasetMetas();
        for (let n = s.length - 1; n >= 0; --n) {
            const o = s[n].$filler;
            co(o) && ys(i.ctx, o, i.chartArea);
        }
    },
    beforeDatasetDraw(i, t, e) {
        const s = t.meta.$filler;
        !co(s) ||
            e.drawTime !== "beforeDatasetDraw" ||
            ys(i.ctx, s, i.chartArea);
    },
    defaults: { propagate: !0, drawTime: "beforeDatasetDraw" },
};
const go = (i, t) => {
    let { boxHeight: e = t, boxWidth: s = t } = i;
    return (
        i.usePointStyle &&
            ((e = Math.min(e, t)), (s = i.pointStyleWidth || Math.min(s, t))),
        { boxWidth: s, boxHeight: e, itemHeight: Math.max(t, e) }
    );
};
const $c = (i, t) =>
    i !== null &&
    t !== null &&
    i.datasetIndex === t.datasetIndex &&
    i.index === t.index;
const Pi = class extends st {
    constructor(t) {
        (super(),
            (this._added = !1),
            (this.legendHitBoxes = []),
            (this._hoveredItem = null),
            (this.doughnutMode = !1),
            (this.chart = t.chart),
            (this.options = t.options),
            (this.ctx = t.ctx),
            (this.legendItems = void 0),
            (this.columnSizes = void 0),
            (this.lineWidths = void 0),
            (this.maxHeight = void 0),
            (this.maxWidth = void 0),
            (this.top = void 0),
            (this.bottom = void 0),
            (this.left = void 0),
            (this.right = void 0),
            (this.height = void 0),
            (this.width = void 0),
            (this._margins = void 0),
            (this.position = void 0),
            (this.weight = void 0),
            (this.fullSize = void 0));
    }

    update(t, e, s) {
        ((this.maxWidth = t),
            (this.maxHeight = e),
            (this._margins = s),
            this.setDimensions(),
            this.buildLabels(),
            this.fit());
    }

    setDimensions() {
        this.isHorizontal()
            ? ((this.width = this.maxWidth),
              (this.left = this._margins.left),
              (this.right = this.width))
            : ((this.height = this.maxHeight),
              (this.top = this._margins.top),
              (this.bottom = this.height));
    }

    buildLabels() {
        const t = this.options.labels || {};
        let e = F(t.generateLabels, [this.chart], this) || [];
        (t.filter && (e = e.filter((s) => t.filter(s, this.chart.data))),
            t.sort && (e = e.sort((s, n) => t.sort(s, n, this.chart.data))),
            this.options.reverse && e.reverse(),
            (this.legendItems = e));
    }

    fit() {
        const { options: t, ctx: e } = this;
        if (!t.display) {
            this.width = this.height = 0;
            return;
        }
        const s = t.labels;
        const n = $(s.font);
        const o = n.size;
        const a = this._computeTitleHeight();
        const { boxWidth: r, itemHeight: l } = go(s, o);
        let c;
        let h;
        ((e.font = n.string),
            this.isHorizontal()
                ? ((c = this.maxWidth), (h = this._fitRows(a, o, r, l) + 10))
                : ((h = this.maxHeight), (c = this._fitCols(a, n, r, l) + 10)),
            (this.width = Math.min(c, t.maxWidth || this.maxWidth)),
            (this.height = Math.min(h, t.maxHeight || this.maxHeight)));
    }

    _fitRows(t, e, s, n) {
        const {
            ctx: o,
            maxWidth: a,
            options: {
                labels: { padding: r },
            },
        } = this;
        const l = (this.legendHitBoxes = []);
        const c = (this.lineWidths = [0]);
        const h = n + r;
        let d = t;
        ((o.textAlign = "left"), (o.textBaseline = "middle"));
        let u = -1;
        let f = -h;
        return (
            this.legendItems.forEach((g, p) => {
                const m = s + e / 2 + o.measureText(g.text).width;
                ((p === 0 || c[c.length - 1] + m + 2 * r > a) &&
                    ((d += h),
                    (c[c.length - (p > 0 ? 0 : 1)] = 0),
                    (f += h),
                    u++),
                    (l[p] = { left: 0, top: f, row: u, width: m, height: n }),
                    (c[c.length - 1] += m + r));
            }),
            d
        );
    }

    _fitCols(t, e, s, n) {
        const {
            ctx: o,
            maxHeight: a,
            options: {
                labels: { padding: r },
            },
        } = this;
        const l = (this.legendHitBoxes = []);
        const c = (this.columnSizes = []);
        const h = a - t;
        let d = r;
        let u = 0;
        let f = 0;
        let g = 0;
        let p = 0;
        return (
            this.legendItems.forEach((m, b) => {
                const { itemWidth: x, itemHeight: v } = Yc(s, e, o, m, n);
                (b > 0 &&
                    f + v + 2 * r > h &&
                    ((d += u + r),
                    c.push({ width: u, height: f }),
                    (g += u + r),
                    p++,
                    (u = f = 0)),
                    (l[b] = { left: g, top: f, col: p, width: x, height: v }),
                    (u = Math.max(u, x)),
                    (f += v + r));
            }),
            (d += u),
            c.push({ width: u, height: f }),
            d
        );
    }

    adjustHitBoxes() {
        if (!this.options.display) return;
        const t = this._computeTitleHeight();
        const {
            legendHitBoxes: e,
            options: {
                align: s,
                labels: { padding: n },
                rtl: o,
            },
        } = this;
        const a = Vt(o, this.left, this.width);
        if (this.isHorizontal()) {
            let r = 0;
            let l = K(s, this.left + n, this.right - this.lineWidths[r]);
            for (const c of e) {
                (r !== c.row &&
                    ((r = c.row),
                    (l = K(s, this.left + n, this.right - this.lineWidths[r]))),
                    (c.top += this.top + t + n),
                    (c.left = a.leftForLtr(a.x(l), c.width)),
                    (l += c.width + n));
            }
        } else {
            let r = 0;
            let l = K(
                s,
                this.top + t + n,
                this.bottom - this.columnSizes[r].height,
            );
            for (const c of e) {
                (c.col !== r &&
                    ((r = c.col),
                    (l = K(
                        s,
                        this.top + t + n,
                        this.bottom - this.columnSizes[r].height,
                    ))),
                    (c.top = l),
                    (c.left += this.left + n),
                    (c.left = a.leftForLtr(a.x(c.left), c.width)),
                    (l += c.height + n));
            }
        }
    }

    isHorizontal() {
        return (
            this.options.position === "top" ||
            this.options.position === "bottom"
        );
    }

    draw() {
        if (this.options.display) {
            const t = this.ctx;
            (Se(t, this), this._draw(), Pe(t));
        }
    }

    _draw() {
        const { options: t, columnSizes: e, lineWidths: s, ctx: n } = this;
        const { align: o, labels: a } = t;
        const r = V.color;
        const l = Vt(t.rtl, this.left, this.width);
        const c = $(a.font);
        const { padding: h } = a;
        const d = c.size;
        const u = d / 2;
        let f;
        (this.drawTitle(),
            (n.textAlign = l.textAlign("left")),
            (n.textBaseline = "middle"),
            (n.lineWidth = 0.5),
            (n.font = c.string));
        const { boxWidth: g, boxHeight: p, itemHeight: m } = go(a, d);
        const b = function (k, w, S) {
            if (isNaN(g) || g <= 0 || isNaN(p) || p < 0) return;
            n.save();
            const P = D(S.lineWidth, 1);
            if (
                ((n.fillStyle = D(S.fillStyle, r)),
                (n.lineCap = D(S.lineCap, "butt")),
                (n.lineDashOffset = D(S.lineDashOffset, 0)),
                (n.lineJoin = D(S.lineJoin, "miter")),
                (n.lineWidth = P),
                (n.strokeStyle = D(S.strokeStyle, r)),
                n.setLineDash(D(S.lineDash, [])),
                a.usePointStyle)
            ) {
                const O = {
                    radius: (p * Math.SQRT2) / 2,
                    pointStyle: S.pointStyle,
                    rotation: S.rotation,
                    borderWidth: P,
                };
                const C = l.xPlus(k, g / 2);
                const L = w + u;
                Ji(n, O, C, L, a.pointStyleWidth && g);
            } else {
                const O = w + Math.max((d - p) / 2, 0);
                const C = l.leftForLtr(k, g);
                const L = At(S.borderRadius);
                (n.beginPath(),
                    Object.values(L).some((U) => U !== 0)
                        ? ne(n, { x: C, y: O, w: g, h: p, radius: L })
                        : n.rect(C, O, g, p),
                    n.fill(),
                    P !== 0 && n.stroke());
            }
            n.restore();
        };
        const x = function (k, w, S) {
            Ct(n, S.text, k, w + m / 2, c, {
                strikethrough: S.hidden,
                textAlign: l.textAlign(S.textAlign),
            });
        };
        const v = this.isHorizontal();
        const y = this._computeTitleHeight();
        (v
            ? (f = {
                  x: K(o, this.left + h, this.right - s[0]),
                  y: this.top + h + y,
                  line: 0,
              })
            : (f = {
                  x: this.left + h,
                  y: K(o, this.top + y + h, this.bottom - e[0].height),
                  line: 0,
              }),
            as(this.ctx, t.textDirection));
        const _ = m + h;
        (this.legendItems.forEach((k, w) => {
            ((n.strokeStyle = k.fontColor), (n.fillStyle = k.fontColor));
            const S = n.measureText(k.text).width;
            const P = l.textAlign(k.textAlign || (k.textAlign = a.textAlign));
            const O = g + u + S;
            let C = f.x;
            let L = f.y;
            (l.setWidth(this.width),
                v
                    ? w > 0 &&
                      C + O + h > this.right &&
                      ((L = f.y += _),
                      f.line++,
                      (C = f.x = K(o, this.left + h, this.right - s[f.line])))
                    : w > 0 &&
                      L + _ > this.bottom &&
                      ((C = f.x = C + e[f.line].width + h),
                      f.line++,
                      (L = f.y =
                          K(
                              o,
                              this.top + y + h,
                              this.bottom - e[f.line].height,
                          ))));
            const U = l.x(C);
            if (
                (b(U, L, k),
                (C = fn(P, C + g + u, v ? C + O : this.right, t.rtl)),
                x(l.x(C), L, k),
                v)
            ) {
                f.x += O + h;
            } else if (typeof k.text !== "string") {
                const et = c.lineHeight;
                f.y += Xo(k, et) + h;
            } else f.y += _;
        }),
            rs(this.ctx, t.textDirection));
    }

    drawTitle() {
        const t = this.options;
        const e = t.title;
        const s = $(e.font);
        const n = q(e.padding);
        if (!e.display) return;
        const o = Vt(t.rtl, this.left, this.width);
        const a = this.ctx;
        const r = e.position;
        const l = s.size / 2;
        const c = n.top + l;
        let h;
        let d = this.left;
        let u = this.width;
        if (this.isHorizontal()) {
            ((u = Math.max(...this.lineWidths)),
                (h = this.top + c),
                (d = K(t.align, d, this.right - u)));
        } else {
            const g = this.columnSizes.reduce(
                (p, m) => Math.max(p, m.height),
                0,
            );
            h =
                c +
                K(
                    t.align,
                    this.top,
                    this.bottom -
                        g -
                        t.labels.padding -
                        this._computeTitleHeight(),
                );
        }
        const f = K(r, d, d + u);
        ((a.textAlign = o.textAlign(oi(r))),
            (a.textBaseline = "middle"),
            (a.strokeStyle = e.color),
            (a.fillStyle = e.color),
            (a.font = s.string),
            Ct(a, e.text, f, h, s));
    }

    _computeTitleHeight() {
        const t = this.options.title;
        const e = $(t.font);
        const s = q(t.padding);
        return t.display ? e.lineHeight + s.height : 0;
    }

    _getLegendItemAt(t, e) {
        let s, n, o;
        if (ut(t, this.left, this.right) && ut(e, this.top, this.bottom)) {
            for (o = this.legendHitBoxes, s = 0; s < o.length; ++s) {
                if (
                    ((n = o[s]),
                    ut(t, n.left, n.left + n.width) &&
                        ut(e, n.top, n.top + n.height))
                ) {
                    return this.legendItems[s];
                }
            }
        }
        return null;
    }

    handleEvent(t) {
        const e = this.options;
        if (!Kc(t.type, e)) return;
        const s = this._getLegendItemAt(t.x, t.y);
        if (t.type === "mousemove" || t.type === "mouseout") {
            const n = this._hoveredItem;
            const o = $c(n, s);
            (n && !o && F(e.onLeave, [t, n, this], this),
                (this._hoveredItem = s),
                s && !o && F(e.onHover, [t, s, this], this));
        } else s && F(e.onClick, [t, s, this], this);
    }
};
function Yc(i, t, e, s, n) {
    const o = Uc(s, i, t, e);
    const a = Xc(n, s, t.lineHeight);
    return { itemWidth: o, itemHeight: a };
}
function Uc(i, t, e, s) {
    let n = i.text;
    return (
        n &&
            typeof n !== "string" &&
            (n = n.reduce((o, a) => (o.length > a.length ? o : a))),
        t + e.size / 2 + s.measureText(n).width
    );
}
function Xc(i, t, e) {
    let s = i;
    return (typeof t.text !== "string" && (s = Xo(t, e)), s);
}
function Xo(i, t) {
    const e = i.text ? i.text.length : 0;
    return t * e;
}
function Kc(i, t) {
    return !!(
        ((i === "mousemove" || i === "mouseout") && (t.onHover || t.onLeave)) ||
        (t.onClick && (i === "click" || i === "mouseup"))
    );
}
const qc = {
    id: "legend",
    _element: Pi,
    start(i, t, e) {
        const s = (i.legend = new Pi({ ctx: i.ctx, options: e, chart: i }));
        (Z.configure(i, s, e), Z.addBox(i, s));
    },
    stop(i) {
        (Z.removeBox(i, i.legend), delete i.legend);
    },
    beforeUpdate(i, t, e) {
        const s = i.legend;
        (Z.configure(i, s, e), (s.options = e));
    },
    afterUpdate(i) {
        const t = i.legend;
        (t.buildLabels(), t.adjustHitBoxes());
    },
    afterEvent(i, t) {
        t.replay || i.legend.handleEvent(t.event);
    },
    defaults: {
        display: !0,
        position: "top",
        align: "center",
        fullSize: !0,
        reverse: !1,
        weight: 1e3,
        onClick(i, t, e) {
            const s = t.datasetIndex;
            const n = e.chart;
            n.isDatasetVisible(s)
                ? (n.hide(s), (t.hidden = !0))
                : (n.show(s), (t.hidden = !1));
        },
        onHover: null,
        onLeave: null,
        labels: {
            color: (i) => i.chart.options.color,
            boxWidth: 40,
            padding: 10,
            generateLabels(i) {
                const t = i.data.datasets;
                const {
                    labels: {
                        usePointStyle: e,
                        pointStyle: s,
                        textAlign: n,
                        color: o,
                        useBorderRadius: a,
                        borderRadius: r,
                    },
                } = i.legend.options;
                return i._getSortedDatasetMetas().map((l) => {
                    const c = l.controller.getStyle(e ? 0 : void 0);
                    const h = q(c.borderWidth);
                    return {
                        text: t[l.index].label,
                        fillStyle: c.backgroundColor,
                        fontColor: o,
                        hidden: !l.visible,
                        lineCap: c.borderCapStyle,
                        lineDash: c.borderDash,
                        lineDashOffset: c.borderDashOffset,
                        lineJoin: c.borderJoinStyle,
                        lineWidth: (h.width + h.height) / 4,
                        strokeStyle: c.borderColor,
                        pointStyle: s || c.pointStyle,
                        rotation: c.rotation,
                        textAlign: n || c.textAlign,
                        borderRadius: a && (r || c.borderRadius),
                        datasetIndex: l.index,
                    };
                }, this);
            },
        },
        title: {
            color: (i) => i.chart.options.color,
            display: !1,
            position: "center",
            text: "",
        },
    },
    descriptors: {
        _scriptable: (i) => !i.startsWith("on"),
        labels: {
            _scriptable: (i) =>
                !["generateLabels", "filter", "sort"].includes(i),
        },
    },
};
const Ne = class extends st {
    constructor(t) {
        (super(),
            (this.chart = t.chart),
            (this.options = t.options),
            (this.ctx = t.ctx),
            (this._padding = void 0),
            (this.top = void 0),
            (this.bottom = void 0),
            (this.left = void 0),
            (this.right = void 0),
            (this.width = void 0),
            (this.height = void 0),
            (this.position = void 0),
            (this.weight = void 0),
            (this.fullSize = void 0));
    }

    update(t, e) {
        const s = this.options;
        if (((this.left = 0), (this.top = 0), !s.display)) {
            this.width = this.height = this.right = this.bottom = 0;
            return;
        }
        ((this.width = this.right = t), (this.height = this.bottom = e));
        const n = z(s.text) ? s.text.length : 1;
        this._padding = q(s.padding);
        const o = n * $(s.font).lineHeight + this._padding.height;
        this.isHorizontal() ? (this.height = o) : (this.width = o);
    }

    isHorizontal() {
        const t = this.options.position;
        return t === "top" || t === "bottom";
    }

    _drawArgs(t) {
        const { top: e, left: s, bottom: n, right: o, options: a } = this;
        const r = a.align;
        let l = 0;
        let c;
        let h;
        let d;
        return (
            this.isHorizontal()
                ? ((h = K(r, s, o)), (d = e + t), (c = o - s))
                : (a.position === "left"
                      ? ((h = s + t), (d = K(r, n, e)), (l = R * -0.5))
                      : ((h = o - t), (d = K(r, e, n)), (l = R * 0.5)),
                  (c = n - e)),
            { titleX: h, titleY: d, maxWidth: c, rotation: l }
        );
    }

    draw() {
        const t = this.ctx;
        const e = this.options;
        if (!e.display) return;
        const s = $(e.font);
        const o = s.lineHeight / 2 + this._padding.top;
        const {
            titleX: a,
            titleY: r,
            maxWidth: l,
            rotation: c,
        } = this._drawArgs(o);
        Ct(t, e.text, 0, 0, s, {
            color: e.color,
            maxWidth: l,
            rotation: c,
            textAlign: oi(e.align),
            textBaseline: "middle",
            translation: [a, r],
        });
    }
};
function Gc(i, t) {
    const e = new Ne({ ctx: i.ctx, options: t, chart: i });
    (Z.configure(i, e, t), Z.addBox(i, e), (i.titleBlock = e));
}
const Zc = {
    id: "title",
    _element: Ne,
    start(i, t, e) {
        Gc(i, e);
    },
    stop(i) {
        const t = i.titleBlock;
        (Z.removeBox(i, t), delete i.titleBlock);
    },
    beforeUpdate(i, t, e) {
        const s = i.titleBlock;
        (Z.configure(i, s, e), (s.options = e));
    },
    defaults: {
        align: "center",
        display: !1,
        font: { weight: "bold" },
        fullSize: !0,
        padding: 10,
        position: "top",
        text: "",
        weight: 2e3,
    },
    defaultRoutes: { color: "color" },
    descriptors: { _scriptable: !0, _indexable: !1 },
};
const mi = new WeakMap();
const Jc = {
    id: "subtitle",
    start(i, t, e) {
        const s = new Ne({ ctx: i.ctx, options: e, chart: i });
        (Z.configure(i, s, e), Z.addBox(i, s), mi.set(i, s));
    },
    stop(i) {
        (Z.removeBox(i, mi.get(i)), mi.delete(i));
    },
    beforeUpdate(i, t, e) {
        const s = mi.get(i);
        (Z.configure(i, s, e), (s.options = e));
    },
    defaults: {
        align: "center",
        display: !1,
        font: { weight: "normal" },
        fullSize: !0,
        padding: 0,
        position: "top",
        text: "",
        weight: 1500,
    },
    defaultRoutes: { color: "color" },
    descriptors: { _scriptable: !0, _indexable: !1 },
};
const Ee = {
    average(i) {
        if (!i.length) return !1;
        let t;
        let e;
        const s = new Set();
        let n = 0;
        let o = 0;
        for (t = 0, e = i.length; t < e; ++t) {
            const r = i[t].element;
            if (r && r.hasValue()) {
                const l = r.tooltipPosition();
                (s.add(l.x), (n += l.y), ++o);
            }
        }
        return o === 0 || s.size === 0
            ? !1
            : { x: [...s].reduce((r, l) => r + l) / s.size, y: n / o };
    },
    nearest(i, t) {
        if (!i.length) return !1;
        let e = t.x;
        let s = t.y;
        let n = Number.POSITIVE_INFINITY;
        let o;
        let a;
        let r;
        for (o = 0, a = i.length; o < a; ++o) {
            const l = i[o].element;
            if (l && l.hasValue()) {
                const c = l.getCenterPoint();
                const h = ti(t, c);
                h < n && ((n = h), (r = l));
            }
        }
        if (r) {
            const l = r.tooltipPosition();
            ((e = l.x), (s = l.y));
        }
        return { x: e, y: s };
    },
};
function ft(i, t) {
    return (t && (z(t) ? Array.prototype.push.apply(i, t) : i.push(t)), i);
}
function Mt(i) {
    return (typeof i === "string" || i instanceof String) &&
        i.indexOf(`
`) > -1
        ? i.split(`
`)
        : i;
}
function Qc(i, t) {
    const { element: e, datasetIndex: s, index: n } = t;
    const o = i.getDatasetMeta(s).controller;
    const { label: a, value: r } = o.getLabelAndValue(n);
    return {
        chart: i,
        label: a,
        parsed: o.getParsed(n),
        raw: i.data.datasets[s].data[n],
        formattedValue: r,
        dataset: o.getDataset(),
        dataIndex: n,
        datasetIndex: s,
        element: e,
    };
}
function po(i, t) {
    const e = i.chart.ctx;
    const { body: s, footer: n, title: o } = i;
    const { boxWidth: a, boxHeight: r } = t;
    const l = $(t.bodyFont);
    const c = $(t.titleFont);
    const h = $(t.footerFont);
    const d = o.length;
    const u = n.length;
    const f = s.length;
    const g = q(t.padding);
    let p = g.height;
    let m = 0;
    let b = s.reduce(
        (y, _) => y + _.before.length + _.lines.length + _.after.length,
        0,
    );
    if (
        ((b += i.beforeBody.length + i.afterBody.length),
        d &&
            (p +=
                d * c.lineHeight +
                (d - 1) * t.titleSpacing +
                t.titleMarginBottom),
        b)
    ) {
        const y = t.displayColors ? Math.max(r, l.lineHeight) : l.lineHeight;
        p += f * y + (b - f) * l.lineHeight + (b - 1) * t.bodySpacing;
    }
    u &&
        (p += t.footerMarginTop + u * h.lineHeight + (u - 1) * t.footerSpacing);
    let x = 0;
    const v = function (y) {
        m = Math.max(m, e.measureText(y).width + x);
    };
    return (
        e.save(),
        (e.font = c.string),
        E(i.title, v),
        (e.font = l.string),
        E(i.beforeBody.concat(i.afterBody), v),
        (x = t.displayColors ? a + 2 + t.boxPadding : 0),
        E(s, (y) => {
            (E(y.before, v), E(y.lines, v), E(y.after, v));
        }),
        (x = 0),
        (e.font = h.string),
        E(i.footer, v),
        e.restore(),
        (m += g.width),
        { width: m, height: p }
    );
}
function th(i, t) {
    const { y: e, height: s } = t;
    return e < s / 2 ? "top" : e > i.height - s / 2 ? "bottom" : "center";
}
function eh(i, t, e, s) {
    const { x: n, width: o } = s;
    const a = e.caretSize + e.caretPadding;
    if (
        (i === "left" && n + o + a > t.width) ||
        (i === "right" && n - o - a < 0)
    ) {
        return !0;
    }
}
function ih(i, t, e, s) {
    const { x: n, width: o } = e;
    const {
        width: a,
        chartArea: { left: r, right: l },
    } = i;
    let c = "center";
    return (
        s === "center"
            ? (c = n <= (r + l) / 2 ? "left" : "right")
            : n <= o / 2
              ? (c = "left")
              : n >= a - o / 2 && (c = "right"),
        eh(c, i, t, e) && (c = "center"),
        c
    );
}
function mo(i, t, e) {
    const s = e.yAlign || t.yAlign || th(i, e);
    return { xAlign: e.xAlign || t.xAlign || ih(i, t, e, s), yAlign: s };
}
function sh(i, t) {
    let { x: e, width: s } = i;
    return (t === "right" ? (e -= s) : t === "center" && (e -= s / 2), e);
}
function nh(i, t, e) {
    let { y: s, height: n } = i;
    return (
        t === "top" ? (s += e) : t === "bottom" ? (s -= n + e) : (s -= n / 2),
        s
    );
}
function bo(i, t, e, s) {
    const { caretSize: n, caretPadding: o, cornerRadius: a } = i;
    const { xAlign: r, yAlign: l } = e;
    const c = n + o;
    const { topLeft: h, topRight: d, bottomLeft: u, bottomRight: f } = At(a);
    let g = sh(t, r);
    const p = nh(t, l, c);
    return (
        l === "center"
            ? r === "left"
                ? (g += c)
                : r === "right" && (g -= c)
            : r === "left"
              ? (g -= Math.max(h, u) + n)
              : r === "right" && (g += Math.max(d, f) + n),
        { x: Y(g, 0, s.width - t.width), y: Y(p, 0, s.height - t.height) }
    );
}
function bi(i, t, e) {
    const s = q(e.padding);
    return t === "center"
        ? i.x + i.width / 2
        : t === "right"
          ? i.x + i.width - s.right
          : i.x + s.left;
}
function xo(i) {
    return ft([], Mt(i));
}
function oh(i, t, e) {
    return yt(i, { tooltip: t, tooltipItems: e, type: "tooltip" });
}
function _o(i, t) {
    const e =
        t && t.dataset && t.dataset.tooltip && t.dataset.tooltip.callbacks;
    return e ? i.override(e) : i;
}
const Ko = {
    beforeTitle: dt,
    title(i) {
        if (i.length > 0) {
            const t = i[0];
            const e = t.chart.data.labels;
            const s = e ? e.length : 0;
            if (this && this.options && this.options.mode === "dataset") {
                return t.dataset.label || "";
            }
            if (t.label) return t.label;
            if (s > 0 && t.dataIndex < s) return e[t.dataIndex];
        }
        return "";
    },
    afterTitle: dt,
    beforeBody: dt,
    beforeLabel: dt,
    label(i) {
        if (this && this.options && this.options.mode === "dataset") {
            return i.label + ": " + i.formattedValue || i.formattedValue;
        }
        let t = i.dataset.label || "";
        t && (t += ": ");
        const e = i.formattedValue;
        return (A(e) || (t += e), t);
    },
    labelColor(i) {
        const e = i.chart
            .getDatasetMeta(i.datasetIndex)
            .controller.getStyle(i.dataIndex);
        return {
            borderColor: e.borderColor,
            backgroundColor: e.backgroundColor,
            borderWidth: e.borderWidth,
            borderDash: e.borderDash,
            borderDashOffset: e.borderDashOffset,
            borderRadius: 0,
        };
    },
    labelTextColor() {
        return this.options.bodyColor;
    },
    labelPointStyle(i) {
        const e = i.chart
            .getDatasetMeta(i.datasetIndex)
            .controller.getStyle(i.dataIndex);
        return { pointStyle: e.pointStyle, rotation: e.rotation };
    },
    afterLabel: dt,
    afterBody: dt,
    beforeFooter: dt,
    footer: dt,
    afterFooter: dt,
};
function Q(i, t, e, s) {
    const n = i[t].call(e, s);
    return typeof n > "u" ? Ko[t].call(e, s) : n;
}
const Ve = class extends st {
    constructor(t) {
        (super(),
            (this.opacity = 0),
            (this._active = []),
            (this._eventPosition = void 0),
            (this._size = void 0),
            (this._cachedAnimations = void 0),
            (this._tooltipItems = []),
            (this.$animations = void 0),
            (this.$context = void 0),
            (this.chart = t.chart),
            (this.options = t.options),
            (this.dataPoints = void 0),
            (this.title = void 0),
            (this.beforeBody = void 0),
            (this.body = void 0),
            (this.afterBody = void 0),
            (this.footer = void 0),
            (this.xAlign = void 0),
            (this.yAlign = void 0),
            (this.x = void 0),
            (this.y = void 0),
            (this.height = void 0),
            (this.width = void 0),
            (this.caretX = void 0),
            (this.caretY = void 0),
            (this.labelColors = void 0),
            (this.labelPointStyles = void 0),
            (this.labelTextColors = void 0));
    }

    initialize(t) {
        ((this.options = t),
            (this._cachedAnimations = void 0),
            (this.$context = void 0));
    }

    _resolveAnimations() {
        const t = this._cachedAnimations;
        if (t) return t;
        const e = this.chart;
        const s = this.options.setContext(this.getContext());
        const n = s.enabled && e.options.animation && s.animations;
        const o = new vi(this.chart, n);
        return (n._cacheable && (this._cachedAnimations = Object.freeze(o)), o);
    }

    getContext() {
        return (
            this.$context ||
            (this.$context = oh(
                this.chart.getContext(),
                this,
                this._tooltipItems,
            ))
        );
    }

    getTitle(t, e) {
        const { callbacks: s } = e;
        const n = Q(s, "beforeTitle", this, t);
        const o = Q(s, "title", this, t);
        const a = Q(s, "afterTitle", this, t);
        let r = [];
        return ((r = ft(r, Mt(n))), (r = ft(r, Mt(o))), (r = ft(r, Mt(a))), r);
    }

    getBeforeBody(t, e) {
        return xo(Q(e.callbacks, "beforeBody", this, t));
    }

    getBody(t, e) {
        const { callbacks: s } = e;
        const n = [];
        return (
            E(t, (o) => {
                const a = { before: [], lines: [], after: [] };
                const r = _o(s, o);
                (ft(a.before, Mt(Q(r, "beforeLabel", this, o))),
                    ft(a.lines, Q(r, "label", this, o)),
                    ft(a.after, Mt(Q(r, "afterLabel", this, o))),
                    n.push(a));
            }),
            n
        );
    }

    getAfterBody(t, e) {
        return xo(Q(e.callbacks, "afterBody", this, t));
    }

    getFooter(t, e) {
        const { callbacks: s } = e;
        const n = Q(s, "beforeFooter", this, t);
        const o = Q(s, "footer", this, t);
        const a = Q(s, "afterFooter", this, t);
        let r = [];
        return ((r = ft(r, Mt(n))), (r = ft(r, Mt(o))), (r = ft(r, Mt(a))), r);
    }

    _createItems(t) {
        const e = this._active;
        const s = this.chart.data;
        const n = [];
        const o = [];
        const a = [];
        let r = [];
        let l;
        let c;
        for (l = 0, c = e.length; l < c; ++l) r.push(Qc(this.chart, e[l]));
        return (
            t.filter && (r = r.filter((h, d, u) => t.filter(h, d, u, s))),
            t.itemSort && (r = r.sort((h, d) => t.itemSort(h, d, s))),
            E(r, (h) => {
                const d = _o(t.callbacks, h);
                (n.push(Q(d, "labelColor", this, h)),
                    o.push(Q(d, "labelPointStyle", this, h)),
                    a.push(Q(d, "labelTextColor", this, h)));
            }),
            (this.labelColors = n),
            (this.labelPointStyles = o),
            (this.labelTextColors = a),
            (this.dataPoints = r),
            r
        );
    }

    update(t, e) {
        const s = this.options.setContext(this.getContext());
        const n = this._active;
        let o;
        let a = [];
        if (!n.length) this.opacity !== 0 && (o = { opacity: 0 });
        else {
            const r = Ee[s.position].call(this, n, this._eventPosition);
            ((a = this._createItems(s)),
                (this.title = this.getTitle(a, s)),
                (this.beforeBody = this.getBeforeBody(a, s)),
                (this.body = this.getBody(a, s)),
                (this.afterBody = this.getAfterBody(a, s)),
                (this.footer = this.getFooter(a, s)));
            const l = (this._size = po(this, s));
            const c = Object.assign({}, r, l);
            const h = mo(this.chart, s, c);
            const d = bo(s, c, h, this.chart);
            ((this.xAlign = h.xAlign),
                (this.yAlign = h.yAlign),
                (o = {
                    opacity: 1,
                    x: d.x,
                    y: d.y,
                    width: l.width,
                    height: l.height,
                    caretX: r.x,
                    caretY: r.y,
                }));
        }
        ((this._tooltipItems = a),
            (this.$context = void 0),
            o && this._resolveAnimations().update(this, o),
            t &&
                s.external &&
                s.external.call(this, {
                    chart: this.chart,
                    tooltip: this,
                    replay: e,
                }));
    }

    drawCaret(t, e, s, n) {
        const o = this.getCaretPosition(t, s, n);
        (e.lineTo(o.x1, o.y1), e.lineTo(o.x2, o.y2), e.lineTo(o.x3, o.y3));
    }

    getCaretPosition(t, e, s) {
        const { xAlign: n, yAlign: o } = this;
        const { caretSize: a, cornerRadius: r } = s;
        const {
            topLeft: l,
            topRight: c,
            bottomLeft: h,
            bottomRight: d,
        } = At(r);
        const { x: u, y: f } = t;
        const { width: g, height: p } = e;
        let m;
        let b;
        let x;
        let v;
        let y;
        let _;
        return (
            o === "center"
                ? ((y = f + p / 2),
                  n === "left"
                      ? ((m = u), (b = m - a), (v = y + a), (_ = y - a))
                      : ((m = u + g), (b = m + a), (v = y - a), (_ = y + a)),
                  (x = m))
                : (n === "left"
                      ? (b = u + Math.max(l, h) + a)
                      : n === "right"
                        ? (b = u + g - Math.max(c, d) - a)
                        : (b = this.caretX),
                  o === "top"
                      ? ((v = f), (y = v - a), (m = b - a), (x = b + a))
                      : ((v = f + p), (y = v + a), (m = b + a), (x = b - a)),
                  (_ = v)),
            { x1: m, x2: b, x3: x, y1: v, y2: y, y3: _ }
        );
    }

    drawTitle(t, e, s) {
        const n = this.title;
        const o = n.length;
        let a;
        let r;
        let l;
        if (o) {
            const c = Vt(s.rtl, this.x, this.width);
            for (
                t.x = bi(this, s.titleAlign, s),
                    e.textAlign = c.textAlign(s.titleAlign),
                    e.textBaseline = "middle",
                    a = $(s.titleFont),
                    r = s.titleSpacing,
                    e.fillStyle = s.titleColor,
                    e.font = a.string,
                    l = 0;
                l < o;
                ++l
            ) {
                (e.fillText(n[l], c.x(t.x), t.y + a.lineHeight / 2),
                    (t.y += a.lineHeight + r),
                    l + 1 === o && (t.y += s.titleMarginBottom - r));
            }
        }
    }

    _drawColorBox(t, e, s, n, o) {
        const a = this.labelColors[s];
        const r = this.labelPointStyles[s];
        const { boxHeight: l, boxWidth: c } = o;
        const h = $(o.bodyFont);
        const d = bi(this, "left", o);
        const u = n.x(d);
        const f = l < h.lineHeight ? (h.lineHeight - l) / 2 : 0;
        const g = e.y + f;
        if (o.usePointStyle) {
            const p = {
                radius: Math.min(c, l) / 2,
                pointStyle: r.pointStyle,
                rotation: r.rotation,
                borderWidth: 1,
            };
            const m = n.leftForLtr(u, c) + c / 2;
            const b = g + l / 2;
            ((t.strokeStyle = o.multiKeyBackground),
                (t.fillStyle = o.multiKeyBackground),
                ri(t, p, m, b),
                (t.strokeStyle = a.borderColor),
                (t.fillStyle = a.backgroundColor),
                ri(t, p, m, b));
        } else {
            ((t.lineWidth = T(a.borderWidth)
                ? Math.max(...Object.values(a.borderWidth))
                : a.borderWidth || 1),
                (t.strokeStyle = a.borderColor),
                t.setLineDash(a.borderDash || []),
                (t.lineDashOffset = a.borderDashOffset || 0));
            const p = n.leftForLtr(u, c);
            const m = n.leftForLtr(n.xPlus(u, 1), c - 2);
            const b = At(a.borderRadius);
            Object.values(b).some((x) => x !== 0)
                ? (t.beginPath(),
                  (t.fillStyle = o.multiKeyBackground),
                  ne(t, { x: p, y: g, w: c, h: l, radius: b }),
                  t.fill(),
                  t.stroke(),
                  (t.fillStyle = a.backgroundColor),
                  t.beginPath(),
                  ne(t, { x: m, y: g + 1, w: c - 2, h: l - 2, radius: b }),
                  t.fill())
                : ((t.fillStyle = o.multiKeyBackground),
                  t.fillRect(p, g, c, l),
                  t.strokeRect(p, g, c, l),
                  (t.fillStyle = a.backgroundColor),
                  t.fillRect(m, g + 1, c - 2, l - 2));
        }
        t.fillStyle = this.labelTextColors[s];
    }

    drawBody(t, e, s) {
        const { body: n } = this;
        const {
            bodySpacing: o,
            bodyAlign: a,
            displayColors: r,
            boxHeight: l,
            boxWidth: c,
            boxPadding: h,
        } = s;
        const d = $(s.bodyFont);
        let u = d.lineHeight;
        let f = 0;
        const g = Vt(s.rtl, this.x, this.width);
        const p = function (S) {
            (e.fillText(S, g.x(t.x + f), t.y + u / 2), (t.y += u + o));
        };
        const m = g.textAlign(a);
        let b;
        let x;
        let v;
        let y;
        let _;
        let k;
        let w;
        for (
            e.textAlign = a,
                e.textBaseline = "middle",
                e.font = d.string,
                t.x = bi(this, m, s),
                e.fillStyle = s.bodyColor,
                E(this.beforeBody, p),
                f =
                    r && m !== "right"
                        ? a === "center"
                            ? c / 2 + h
                            : c + 2 + h
                        : 0,
                y = 0,
                k = n.length;
            y < k;
            ++y
        ) {
            for (
                b = n[y],
                    x = this.labelTextColors[y],
                    e.fillStyle = x,
                    E(b.before, p),
                    v = b.lines,
                    r &&
                        v.length &&
                        (this._drawColorBox(e, t, y, g, s),
                        (u = Math.max(d.lineHeight, l))),
                    _ = 0,
                    w = v.length;
                _ < w;
                ++_
            ) {
                (p(v[_]), (u = d.lineHeight));
            }
            E(b.after, p);
        }
        ((f = 0), (u = d.lineHeight), E(this.afterBody, p), (t.y -= o));
    }

    drawFooter(t, e, s) {
        const n = this.footer;
        const o = n.length;
        let a;
        let r;
        if (o) {
            const l = Vt(s.rtl, this.x, this.width);
            for (
                t.x = bi(this, s.footerAlign, s),
                    t.y += s.footerMarginTop,
                    e.textAlign = l.textAlign(s.footerAlign),
                    e.textBaseline = "middle",
                    a = $(s.footerFont),
                    e.fillStyle = s.footerColor,
                    e.font = a.string,
                    r = 0;
                r < o;
                ++r
            ) {
                (e.fillText(n[r], l.x(t.x), t.y + a.lineHeight / 2),
                    (t.y += a.lineHeight + s.footerSpacing));
            }
        }
    }

    drawBackground(t, e, s, n) {
        const { xAlign: o, yAlign: a } = this;
        const { x: r, y: l } = t;
        const { width: c, height: h } = s;
        const {
            topLeft: d,
            topRight: u,
            bottomLeft: f,
            bottomRight: g,
        } = At(n.cornerRadius);
        ((e.fillStyle = n.backgroundColor),
            (e.strokeStyle = n.borderColor),
            (e.lineWidth = n.borderWidth),
            e.beginPath(),
            e.moveTo(r + d, l),
            a === "top" && this.drawCaret(t, e, s, n),
            e.lineTo(r + c - u, l),
            e.quadraticCurveTo(r + c, l, r + c, l + u),
            a === "center" && o === "right" && this.drawCaret(t, e, s, n),
            e.lineTo(r + c, l + h - g),
            e.quadraticCurveTo(r + c, l + h, r + c - g, l + h),
            a === "bottom" && this.drawCaret(t, e, s, n),
            e.lineTo(r + f, l + h),
            e.quadraticCurveTo(r, l + h, r, l + h - f),
            a === "center" && o === "left" && this.drawCaret(t, e, s, n),
            e.lineTo(r, l + d),
            e.quadraticCurveTo(r, l, r + d, l),
            e.closePath(),
            e.fill(),
            n.borderWidth > 0 && e.stroke());
    }

    _updateAnimationTarget(t) {
        const e = this.chart;
        const s = this.$animations;
        const n = s && s.x;
        const o = s && s.y;
        if (n || o) {
            const a = Ee[t.position].call(
                this,
                this._active,
                this._eventPosition,
            );
            if (!a) return;
            const r = (this._size = po(this, t));
            const l = Object.assign({}, a, this._size);
            const c = mo(e, t, l);
            const h = bo(t, l, c, e);
            (n._to !== h.x || o._to !== h.y) &&
                ((this.xAlign = c.xAlign),
                (this.yAlign = c.yAlign),
                (this.width = r.width),
                (this.height = r.height),
                (this.caretX = a.x),
                (this.caretY = a.y),
                this._resolveAnimations().update(this, h));
        }
    }

    _willRender() {
        return !!this.opacity;
    }

    draw(t) {
        const e = this.options.setContext(this.getContext());
        let s = this.opacity;
        if (!s) return;
        this._updateAnimationTarget(e);
        const n = { width: this.width, height: this.height };
        const o = { x: this.x, y: this.y };
        s = Math.abs(s) < 0.001 ? 0 : s;
        const a = q(e.padding);
        const r =
            this.title.length ||
            this.beforeBody.length ||
            this.body.length ||
            this.afterBody.length ||
            this.footer.length;
        e.enabled &&
            r &&
            (t.save(),
            (t.globalAlpha = s),
            this.drawBackground(o, t, n, e),
            as(t, e.textDirection),
            (o.y += a.top),
            this.drawTitle(o, t, e),
            this.drawBody(o, t, e),
            this.drawFooter(o, t, e),
            rs(t, e.textDirection),
            t.restore());
    }

    getActiveElements() {
        return this._active || [];
    }

    setActiveElements(t, e) {
        const s = this._active;
        const n = t.map(({ datasetIndex: r, index: l }) => {
            const c = this.chart.getDatasetMeta(r);
            if (!c) throw new Error("Cannot find a dataset at index " + r);
            return { datasetIndex: r, element: c.data[l], index: l };
        });
        const o = !ke(s, n);
        const a = this._positionChanged(n, e);
        (o || a) &&
            ((this._active = n),
            (this._eventPosition = e),
            (this._ignoreReplayEvents = !0),
            this.update(!0));
    }

    handleEvent(t, e, s = !0) {
        if (e && this._ignoreReplayEvents) return !1;
        this._ignoreReplayEvents = !1;
        const n = this.options;
        const o = this._active || [];
        const a = this._getActiveElements(t, o, e, s);
        const r = this._positionChanged(a, t);
        const l = e || !ke(a, o) || r;
        return (
            l &&
                ((this._active = a),
                (n.enabled || n.external) &&
                    ((this._eventPosition = { x: t.x, y: t.y }),
                    this.update(!0, e))),
            l
        );
    }

    _getActiveElements(t, e, s, n) {
        const o = this.options;
        if (t.type === "mouseout") return [];
        if (!n) {
            return e.filter(
                (r) =>
                    this.chart.data.datasets[r.datasetIndex] &&
                    this.chart
                        .getDatasetMeta(r.datasetIndex)
                        .controller.getParsed(r.index) !== void 0,
            );
        }
        const a = this.chart.getElementsAtEventForMode(t, o.mode, o, s);
        return (o.reverse && a.reverse(), a);
    }

    _positionChanged(t, e) {
        const { caretX: s, caretY: n, options: o } = this;
        const a = Ee[o.position].call(this, t, e);
        return a !== !1 && (s !== a.x || n !== a.y);
    }
};
M(Ve, "positioners", Ee);
const ah = {
    id: "tooltip",
    _element: Ve,
    positioners: Ee,
    afterInit(i, t, e) {
        e && (i.tooltip = new Ve({ chart: i, options: e }));
    },
    beforeUpdate(i, t, e) {
        i.tooltip && i.tooltip.initialize(e);
    },
    reset(i, t, e) {
        i.tooltip && i.tooltip.initialize(e);
    },
    afterDraw(i) {
        const t = i.tooltip;
        if (t && t._willRender()) {
            const e = { tooltip: t };
            if (
                i.notifyPlugins("beforeTooltipDraw", {
                    ...e,
                    cancelable: !0,
                }) === !1
            ) {
                return;
            }
            (t.draw(i.ctx), i.notifyPlugins("afterTooltipDraw", e));
        }
    },
    afterEvent(i, t) {
        if (i.tooltip) {
            const e = t.replay;
            i.tooltip.handleEvent(t.event, e, t.inChartArea) &&
                (t.changed = !0);
        }
    },
    defaults: {
        enabled: !0,
        external: null,
        position: "average",
        backgroundColor: "rgba(0,0,0,0.8)",
        titleColor: "#fff",
        titleFont: { weight: "bold" },
        titleSpacing: 2,
        titleMarginBottom: 6,
        titleAlign: "left",
        bodyColor: "#fff",
        bodySpacing: 2,
        bodyFont: {},
        bodyAlign: "left",
        footerColor: "#fff",
        footerSpacing: 2,
        footerMarginTop: 6,
        footerFont: { weight: "bold" },
        footerAlign: "left",
        padding: 6,
        caretPadding: 2,
        caretSize: 5,
        cornerRadius: 6,
        boxHeight: (i, t) => t.bodyFont.size,
        boxWidth: (i, t) => t.bodyFont.size,
        multiKeyBackground: "#fff",
        displayColors: !0,
        boxPadding: 0,
        borderColor: "rgba(0,0,0,0)",
        borderWidth: 0,
        animation: { duration: 400, easing: "easeOutQuart" },
        animations: {
            numbers: {
                type: "number",
                properties: ["x", "y", "width", "height", "caretX", "caretY"],
            },
            opacity: { easing: "linear", duration: 200 },
        },
        callbacks: Ko,
    },
    defaultRoutes: { bodyFont: "font", footerFont: "font", titleFont: "font" },
    descriptors: {
        _scriptable: (i) =>
            i !== "filter" && i !== "itemSort" && i !== "external",
        _indexable: !1,
        callbacks: { _scriptable: !1, _indexable: !1 },
        animation: { _fallback: !1 },
        animations: { _fallback: "animation" },
    },
    additionalOptionScopes: ["interaction"],
};
const rh = Object.freeze({
    __proto__: null,
    Colors: _c,
    Decimation: kc,
    Filler: jc,
    Legend: qc,
    SubTitle: Jc,
    Title: Zc,
    Tooltip: ah,
});
const lh = (i, t, e, s) => (
    typeof t === "string"
        ? ((e = i.push(t) - 1), s.unshift({ index: e, label: t }))
        : isNaN(t) && (e = null),
    e
);
function ch(i, t, e, s) {
    const n = i.indexOf(t);
    if (n === -1) return lh(i, t, e, s);
    const o = i.lastIndexOf(t);
    return n !== o ? e : n;
}
const hh = (i, t) => (i === null ? null : Y(Math.round(i), 0, t));
function yo(i) {
    const t = this.getLabels();
    return i >= 0 && i < t.length ? t[i] : i;
}
const Ie = class extends Ut {
    constructor(t) {
        (super(t),
            (this._startValue = void 0),
            (this._valueRange = 0),
            (this._addedLabels = []));
    }

    init(t) {
        const e = this._addedLabels;
        if (e.length) {
            const s = this.getLabels();
            for (const { index: n, label: o } of e) {
                s[n] === o && s.splice(n, 1);
            }
            this._addedLabels = [];
        }
        super.init(t);
    }

    parse(t, e) {
        if (A(t)) return null;
        const s = this.getLabels();
        return (
            (e =
                isFinite(e) && s[e] === t
                    ? e
                    : ch(s, t, D(e, t), this._addedLabels)),
            hh(e, s.length - 1)
        );
    }

    determineDataLimits() {
        const { minDefined: t, maxDefined: e } = this.getUserBounds();
        let { min: s, max: n } = this.getMinMax(!0);
        (this.options.bounds === "ticks" &&
            (t || (s = 0), e || (n = this.getLabels().length - 1)),
            (this.min = s),
            (this.max = n));
    }

    buildTicks() {
        const t = this.min;
        const e = this.max;
        const s = this.options.offset;
        const n = [];
        let o = this.getLabels();
        ((o = t === 0 && e === o.length - 1 ? o : o.slice(t, e + 1)),
            (this._valueRange = Math.max(o.length - (s ? 0 : 1), 1)),
            (this._startValue = this.min - (s ? 0.5 : 0)));
        for (let a = t; a <= e; a++) n.push({ value: a });
        return n;
    }

    getLabelForValue(t) {
        return yo.call(this, t);
    }

    configure() {
        (super.configure(),
            this.isHorizontal() ||
                (this._reversePixels = !this._reversePixels));
    }

    getPixelForValue(t) {
        return (
            typeof t !== "number" && (t = this.parse(t)),
            t === null
                ? NaN
                : this.getPixelForDecimal(
                      (t - this._startValue) / this._valueRange,
                  )
        );
    }

    getPixelForTick(t) {
        const e = this.ticks;
        return t < 0 || t > e.length - 1
            ? null
            : this.getPixelForValue(e[t].value);
    }

    getValueForPixel(t) {
        return Math.round(
            this._startValue + this.getDecimalForPixel(t) * this._valueRange,
        );
    }

    getBasePixel() {
        return this.bottom;
    }
};
(M(Ie, "id", "category"), M(Ie, "defaults", { ticks: { callback: yo } }));
function dh(i, t) {
    const e = [];
    const {
        bounds: n,
        step: o,
        min: a,
        max: r,
        precision: l,
        count: c,
        maxTicks: h,
        maxDigits: d,
        includeBounds: u,
    } = i;
    const f = o || 1;
    const g = h - 1;
    const { min: p, max: m } = t;
    const b = !A(a);
    const x = !A(r);
    const v = !A(c);
    const y = (m - p) / (d + 1);
    let _ = Vi((m - p) / g / f) * f;
    let k;
    let w;
    let S;
    let P;
    if (_ < 1e-14 && !b && !x) return [{ value: p }, { value: m }];
    ((P = Math.ceil(m / _) - Math.floor(p / _)),
        P > g && (_ = Vi((P * _) / g / f) * f),
        A(l) || ((k = Math.pow(10, l)), (_ = Math.ceil(_ * k) / k)),
        n === "ticks"
            ? ((w = Math.floor(p / _) * _), (S = Math.ceil(m / _) * _))
            : ((w = p), (S = m)),
        b && x && o && an((r - a) / o, _ / 1e3)
            ? ((P = Math.round(Math.min((r - a) / _, h))),
              (_ = (r - a) / P),
              (w = a),
              (S = r))
            : v
              ? ((w = b ? a : w),
                (S = x ? r : S),
                (P = c - 1),
                (_ = (S - w) / P))
              : ((P = (S - w) / _),
                ee(P, Math.round(P), _ / 1e3)
                    ? (P = Math.round(P))
                    : (P = Math.ceil(P))));
    const O = Math.max(Ni(_), Ni(w));
    ((k = Math.pow(10, A(l) ? O : l)),
        (w = Math.round(w * k) / k),
        (S = Math.round(S * k) / k));
    let C = 0;
    for (
        b &&
        (u && w !== a
            ? (e.push({ value: a }),
              w < a && C++,
              ee(Math.round((w + C * _) * k) / k, a, vo(a, y, i)) && C++)
            : w < a && C++);
        C < P;
        ++C
    ) {
        const L = Math.round((w + C * _) * k) / k;
        if (x && L > r) break;
        e.push({ value: L });
    }
    return (
        x && u && S !== r
            ? e.length && ee(e[e.length - 1].value, r, vo(r, y, i))
                ? (e[e.length - 1].value = r)
                : e.push({ value: r })
            : (!x || S === r) && e.push({ value: S }),
        e
    );
}
function vo(i, t, { horizontal: e, minRotation: s }) {
    const n = ot(s);
    const o = (e ? Math.sin(n) : Math.cos(n)) || 0.001;
    const a = 0.75 * t * ("" + i).length;
    return Math.min(t / o, a);
}
const pe = class extends Ut {
    constructor(t) {
        (super(t),
            (this.start = void 0),
            (this.end = void 0),
            (this._startValue = void 0),
            (this._endValue = void 0),
            (this._valueRange = 0));
    }

    parse(t, e) {
        return A(t) ||
            ((typeof t === "number" || t instanceof Number) && !isFinite(+t))
            ? null
            : +t;
    }

    handleTickRangeOptions() {
        const { beginAtZero: t } = this.options;
        const { minDefined: e, maxDefined: s } = this.getUserBounds();
        let { min: n, max: o } = this;
        const a = (l) => (n = e ? n : l);
        const r = (l) => (o = s ? o : l);
        if (t) {
            const l = lt(n);
            const c = lt(o);
            l < 0 && c < 0 ? r(0) : l > 0 && c > 0 && a(0);
        }
        if (n === o) {
            const l = o === 0 ? 1 : Math.abs(o * 0.05);
            (r(o + l), t || a(n - l));
        }
        ((this.min = n), (this.max = o));
    }

    getTickLimit() {
        const t = this.options.ticks;
        let { maxTicksLimit: e, stepSize: s } = t;
        let n;
        return (
            s
                ? ((n = Math.ceil(this.max / s) - Math.floor(this.min / s) + 1),
                  n > 1e3 &&
                      (console.warn(
                          `scales.${this.id}.ticks.stepSize: ${s} would result generating up to ${n} ticks. Limiting to 1000.`,
                      ),
                      (n = 1e3)))
                : ((n = this.computeTickLimit()), (e = e || 11)),
            e && (n = Math.min(e, n)),
            n
        );
    }

    computeTickLimit() {
        return Number.POSITIVE_INFINITY;
    }

    buildTicks() {
        const t = this.options;
        const e = t.ticks;
        let s = this.getTickLimit();
        s = Math.max(2, s);
        const n = {
            maxTicks: s,
            bounds: t.bounds,
            min: t.min,
            max: t.max,
            precision: e.precision,
            step: e.stepSize,
            count: e.count,
            maxDigits: this._maxDigits(),
            horizontal: this.isHorizontal(),
            minRotation: e.minRotation || 0,
            includeBounds: e.includeBounds !== !1,
        };
        const o = this._range || this;
        const a = dh(n, o);
        return (
            t.bounds === "ticks" && Wi(a, this, "value"),
            t.reverse
                ? (a.reverse(), (this.start = this.max), (this.end = this.min))
                : ((this.start = this.min), (this.end = this.max)),
            a
        );
    }

    configure() {
        const t = this.ticks;
        let e = this.min;
        let s = this.max;
        if ((super.configure(), this.options.offset && t.length)) {
            const n = (s - e) / Math.max(t.length - 1, 1) / 2;
            ((e -= n), (s += n));
        }
        ((this._startValue = e),
            (this._endValue = s),
            (this._valueRange = s - e));
    }

    getLabelForValue(t) {
        return se(t, this.chart.options.locale, this.options.ticks.format);
    }
};
const Fe = class extends pe {
    determineDataLimits() {
        const { min: t, max: e } = this.getMinMax(!0);
        ((this.min = N(t) ? t : 0),
            (this.max = N(e) ? e : 1),
            this.handleTickRangeOptions());
    }

    computeTickLimit() {
        const t = this.isHorizontal();
        const e = t ? this.width : this.height;
        const s = ot(this.options.ticks.minRotation);
        const n = (t ? Math.sin(s) : Math.cos(s)) || 0.001;
        const o = this._resolveTickFontOptions(0);
        return Math.ceil(e / Math.min(40, o.lineHeight / n));
    }

    getPixelForValue(t) {
        return t === null
            ? NaN
            : this.getPixelForDecimal(
                  (t - this._startValue) / this._valueRange,
              );
    }

    getValueForPixel(t) {
        return this._startValue + this.getDecimalForPixel(t) * this._valueRange;
    }
};
(M(Fe, "id", "linear"),
    M(Fe, "defaults", { ticks: { callback: we.formatters.numeric } }));
const He = (i) => Math.floor(xt(i));
const Nt = (i, t) => Math.pow(10, He(i) + t);
function Mo(i) {
    return i / Math.pow(10, He(i)) === 1;
}
function ko(i, t, e) {
    const s = Math.pow(10, e);
    const n = Math.floor(i / s);
    return Math.ceil(t / s) - n;
}
function uh(i, t) {
    const e = t - i;
    let s = He(e);
    for (; ko(i, t, s) > 10; ) s++;
    for (; ko(i, t, s) < 10; ) s--;
    return Math.min(s, He(i));
}
function fh(i, { min: t, max: e }) {
    t = J(i.min, t);
    const s = [];
    const n = He(t);
    let o = uh(t, e);
    let a = o < 0 ? Math.pow(10, Math.abs(o)) : 1;
    const r = Math.pow(10, o);
    const l = n > o ? Math.pow(10, n) : 0;
    const c = Math.round((t - l) * a) / a;
    const h = Math.floor((t - l) / r / 10) * r * 10;
    let d = Math.floor((c - h) / Math.pow(10, o));
    let u = J(i.min, Math.round((l + h + d * Math.pow(10, o)) * a) / a);
    for (; u < e; ) {
        (s.push({ value: u, major: Mo(u), significand: d }),
            d >= 10 ? (d = d < 15 ? 15 : 20) : d++,
            d >= 20 && (o++, (d = 2), (a = o >= 0 ? 1 : a)),
            (u = Math.round((l + h + d * Math.pow(10, o)) * a) / a));
    }
    const f = J(i.max, u);
    return (s.push({ value: f, major: Mo(f), significand: d }), s);
}
const ze = class extends Ut {
    constructor(t) {
        (super(t),
            (this.start = void 0),
            (this.end = void 0),
            (this._startValue = void 0),
            (this._valueRange = 0));
    }

    parse(t, e) {
        const s = pe.prototype.parse.apply(this, [t, e]);
        if (s === 0) {
            this._zero = !0;
            return;
        }
        return N(s) && s > 0 ? s : null;
    }

    determineDataLimits() {
        const { min: t, max: e } = this.getMinMax(!0);
        ((this.min = N(t) ? Math.max(0, t) : null),
            (this.max = N(e) ? Math.max(0, e) : null),
            this.options.beginAtZero && (this._zero = !0),
            this._zero &&
                this.min !== this._suggestedMin &&
                !N(this._userMin) &&
                (this.min =
                    t === Nt(this.min, 0) ? Nt(this.min, -1) : Nt(this.min, 0)),
            this.handleTickRangeOptions());
    }

    handleTickRangeOptions() {
        const { minDefined: t, maxDefined: e } = this.getUserBounds();
        let s = this.min;
        let n = this.max;
        const o = (r) => (s = t ? s : r);
        const a = (r) => (n = e ? n : r);
        (s === n && (s <= 0 ? (o(1), a(10)) : (o(Nt(s, -1)), a(Nt(n, 1)))),
            s <= 0 && o(Nt(n, -1)),
            n <= 0 && a(Nt(s, 1)),
            (this.min = s),
            (this.max = n));
    }

    buildTicks() {
        const t = this.options;
        const e = { min: this._userMin, max: this._userMax };
        const s = fh(e, this);
        return (
            t.bounds === "ticks" && Wi(s, this, "value"),
            t.reverse
                ? (s.reverse(), (this.start = this.max), (this.end = this.min))
                : ((this.start = this.min), (this.end = this.max)),
            s
        );
    }

    getLabelForValue(t) {
        return t === void 0
            ? "0"
            : se(t, this.chart.options.locale, this.options.ticks.format);
    }

    configure() {
        const t = this.min;
        (super.configure(),
            (this._startValue = xt(t)),
            (this._valueRange = xt(this.max) - xt(t)));
    }

    getPixelForValue(t) {
        return (
            (t === void 0 || t === 0) && (t = this.min),
            t === null || isNaN(t)
                ? NaN
                : this.getPixelForDecimal(
                      t === this.min
                          ? 0
                          : (xt(t) - this._startValue) / this._valueRange,
                  )
        );
    }

    getValueForPixel(t) {
        const e = this.getDecimalForPixel(t);
        return Math.pow(10, this._startValue + e * this._valueRange);
    }
};
(M(ze, "id", "logarithmic"),
    M(ze, "defaults", {
        ticks: { callback: we.formatters.logarithmic, major: { enabled: !0 } },
    }));
function Is(i) {
    const t = i.ticks;
    if (t.display && i.display) {
        const e = q(t.backdropPadding);
        return D(t.font && t.font.size, V.font.size) + e.height;
    }
    return 0;
}
function gh(i, t, e) {
    return (
        (e = z(e) ? e : [e]),
        { w: pn(i, t.string, e), h: e.length * t.lineHeight }
    );
}
function wo(i, t, e, s, n) {
    return i === s || i === n
        ? { start: t - e / 2, end: t + e / 2 }
        : i < s || i > n
          ? { start: t - e, end: t }
          : { start: t, end: t + e };
}
function ph(i) {
    const t = {
        l: i.left + i._padding.left,
        r: i.right - i._padding.right,
        t: i.top + i._padding.top,
        b: i.bottom - i._padding.bottom,
    };
    const e = Object.assign({}, t);
    const s = [];
    const n = [];
    const o = i._pointLabels.length;
    const a = i.options.pointLabels;
    const r = a.centerPointLabels ? R / o : 0;
    for (let l = 0; l < o; l++) {
        const c = a.setContext(i.getPointLabelContext(l));
        n[l] = c.padding;
        const h = i.getPointPosition(l, i.drawingArea + n[l], r);
        const d = $(c.font);
        const u = gh(i.ctx, d, i._pointLabels[l]);
        s[l] = u;
        const f = X(i.getIndexAngle(l) + r);
        const g = Math.round(si(f));
        const p = wo(g, h.x, u.w, 0, 180);
        const m = wo(g, h.y, u.h, 90, 270);
        mh(e, t, f, p, m);
    }
    (i.setCenterPoint(t.l - e.l, e.r - t.r, t.t - e.t, e.b - t.b),
        (i._pointLabelItems = _h(i, s, n)));
}
function mh(i, t, e, s, n) {
    const o = Math.abs(Math.sin(e));
    const a = Math.abs(Math.cos(e));
    let r = 0;
    let l = 0;
    (s.start < t.l
        ? ((r = (t.l - s.start) / o), (i.l = Math.min(i.l, t.l - r)))
        : s.end > t.r &&
          ((r = (s.end - t.r) / o), (i.r = Math.max(i.r, t.r + r))),
        n.start < t.t
            ? ((l = (t.t - n.start) / a), (i.t = Math.min(i.t, t.t - l)))
            : n.end > t.b &&
              ((l = (n.end - t.b) / a), (i.b = Math.max(i.b, t.b + l))));
}
function bh(i, t, e) {
    const s = i.drawingArea;
    const { extra: n, additionalAngle: o, padding: a, size: r } = e;
    const l = i.getPointPosition(t, s + n + a, o);
    const c = Math.round(si(X(l.angle + H)));
    const h = Mh(l.y, r.h, c);
    const d = yh(c);
    const u = vh(l.x, r.w, d);
    return {
        visible: !0,
        x: l.x,
        y: h,
        textAlign: d,
        left: u,
        top: h,
        right: u + r.w,
        bottom: h + r.h,
    };
}
function xh(i, t) {
    if (!t) return !0;
    const { left: e, top: s, right: n, bottom: o } = i;
    return !(
        ht({ x: e, y: s }, t) ||
        ht({ x: e, y: o }, t) ||
        ht({ x: n, y: s }, t) ||
        ht({ x: n, y: o }, t)
    );
}
function _h(i, t, e) {
    const s = [];
    const n = i._pointLabels.length;
    const o = i.options;
    const { centerPointLabels: a, display: r } = o.pointLabels;
    const l = { extra: Is(o) / 2, additionalAngle: a ? R / n : 0 };
    let c;
    for (let h = 0; h < n; h++) {
        ((l.padding = e[h]), (l.size = t[h]));
        const d = bh(i, h, l);
        (s.push(d),
            r === "auto" && ((d.visible = xh(d, c)), d.visible && (c = d)));
    }
    return s;
}
function yh(i) {
    return i === 0 || i === 180 ? "center" : i < 180 ? "left" : "right";
}
function vh(i, t, e) {
    return (e === "right" ? (i -= t) : e === "center" && (i -= t / 2), i);
}
function Mh(i, t, e) {
    return (
        e === 90 || e === 270 ? (i -= t / 2) : (e > 270 || e < 90) && (i -= t),
        i
    );
}
function kh(i, t, e) {
    const { left: s, top: n, right: o, bottom: a } = e;
    const { backdropColor: r } = t;
    if (!A(r)) {
        const l = At(t.borderRadius);
        const c = q(t.backdropPadding);
        i.fillStyle = r;
        const h = s - c.left;
        const d = n - c.top;
        const u = o - s + c.width;
        const f = a - n + c.height;
        Object.values(l).some((g) => g !== 0)
            ? (i.beginPath(),
              ne(i, { x: h, y: d, w: u, h: f, radius: l }),
              i.fill())
            : i.fillRect(h, d, u, f);
    }
}
function wh(i, t) {
    const {
        ctx: e,
        options: { pointLabels: s },
    } = i;
    for (let n = t - 1; n >= 0; n--) {
        const o = i._pointLabelItems[n];
        if (!o.visible) continue;
        const a = s.setContext(i.getPointLabelContext(n));
        kh(e, a, o);
        const r = $(a.font);
        const { x: l, y: c, textAlign: h } = o;
        Ct(e, i._pointLabels[n], l, c + r.lineHeight / 2, r, {
            color: a.color,
            textAlign: h,
            textBaseline: "middle",
        });
    }
}
function qo(i, t, e, s) {
    const { ctx: n } = i;
    if (e) n.arc(i.xCenter, i.yCenter, t, 0, B);
    else {
        let o = i.getPointPosition(0, t);
        n.moveTo(o.x, o.y);
        for (let a = 1; a < s; a++) {
            ((o = i.getPointPosition(a, t)), n.lineTo(o.x, o.y));
        }
    }
}
function Sh(i, t, e, s, n) {
    const o = i.ctx;
    const a = t.circular;
    const { color: r, lineWidth: l } = t;
    (!a && !s) ||
        !r ||
        !l ||
        e < 0 ||
        (o.save(),
        (o.strokeStyle = r),
        (o.lineWidth = l),
        o.setLineDash(n.dash || []),
        (o.lineDashOffset = n.dashOffset),
        o.beginPath(),
        qo(i, e, a, s),
        o.closePath(),
        o.stroke(),
        o.restore());
}
function Ph(i, t, e) {
    return yt(i, { label: e, index: t, type: "pointLabel" });
}
const jt = class extends pe {
    constructor(t) {
        (super(t),
            (this.xCenter = void 0),
            (this.yCenter = void 0),
            (this.drawingArea = void 0),
            (this._pointLabels = []),
            (this._pointLabelItems = []));
    }

    setDimensions() {
        const t = (this._padding = q(Is(this.options) / 2));
        const e = (this.width = this.maxWidth - t.width);
        const s = (this.height = this.maxHeight - t.height);
        ((this.xCenter = Math.floor(this.left + e / 2 + t.left)),
            (this.yCenter = Math.floor(this.top + s / 2 + t.top)),
            (this.drawingArea = Math.floor(Math.min(e, s) / 2)));
    }

    determineDataLimits() {
        const { min: t, max: e } = this.getMinMax(!1);
        ((this.min = N(t) && !isNaN(t) ? t : 0),
            (this.max = N(e) && !isNaN(e) ? e : 0),
            this.handleTickRangeOptions());
    }

    computeTickLimit() {
        return Math.ceil(this.drawingArea / Is(this.options));
    }

    generateTickLabels(t) {
        (pe.prototype.generateTickLabels.call(this, t),
            (this._pointLabels = this.getLabels()
                .map((e, s) => {
                    const n = F(
                        this.options.pointLabels.callback,
                        [e, s],
                        this,
                    );
                    return n || n === 0 ? n : "";
                })
                .filter((e, s) => this.chart.getDataVisibility(s))));
    }

    fit() {
        const t = this.options;
        t.display && t.pointLabels.display
            ? ph(this)
            : this.setCenterPoint(0, 0, 0, 0);
    }

    setCenterPoint(t, e, s, n) {
        ((this.xCenter += Math.floor((t - e) / 2)),
            (this.yCenter += Math.floor((s - n) / 2)),
            (this.drawingArea -= Math.min(
                this.drawingArea / 2,
                Math.max(t, e, s, n),
            )));
    }

    getIndexAngle(t) {
        const e = B / (this._pointLabels.length || 1);
        const s = this.options.startAngle || 0;
        return X(t * e + ot(s));
    }

    getDistanceFromCenterForValue(t) {
        if (A(t)) return NaN;
        const e = this.drawingArea / (this.max - this.min);
        return this.options.reverse ? (this.max - t) * e : (t - this.min) * e;
    }

    getValueForDistanceFromCenter(t) {
        if (A(t)) return NaN;
        const e = t / (this.drawingArea / (this.max - this.min));
        return this.options.reverse ? this.max - e : this.min + e;
    }

    getPointLabelContext(t) {
        const e = this._pointLabels || [];
        if (t >= 0 && t < e.length) {
            const s = e[t];
            return Ph(this.getContext(), t, s);
        }
    }

    getPointPosition(t, e, s = 0) {
        const n = this.getIndexAngle(t) - H + s;
        return {
            x: Math.cos(n) * e + this.xCenter,
            y: Math.sin(n) * e + this.yCenter,
            angle: n,
        };
    }

    getPointPositionForValue(t, e) {
        return this.getPointPosition(t, this.getDistanceFromCenterForValue(e));
    }

    getBasePosition(t) {
        return this.getPointPositionForValue(t || 0, this.getBaseValue());
    }

    getPointLabelPosition(t) {
        const {
            left: e,
            top: s,
            right: n,
            bottom: o,
        } = this._pointLabelItems[t];
        return { left: e, top: s, right: n, bottom: o };
    }

    drawBackground() {
        const {
            backgroundColor: t,
            grid: { circular: e },
        } = this.options;
        if (t) {
            const s = this.ctx;
            (s.save(),
                s.beginPath(),
                qo(
                    this,
                    this.getDistanceFromCenterForValue(this._endValue),
                    e,
                    this._pointLabels.length,
                ),
                s.closePath(),
                (s.fillStyle = t),
                s.fill(),
                s.restore());
        }
    }

    drawGrid() {
        const t = this.ctx;
        const e = this.options;
        const { angleLines: s, grid: n, border: o } = e;
        const a = this._pointLabels.length;
        let r;
        let l;
        let c;
        if (
            (e.pointLabels.display && wh(this, a),
            n.display &&
                this.ticks.forEach((h, d) => {
                    if (d !== 0 || (d === 0 && this.min < 0)) {
                        l = this.getDistanceFromCenterForValue(h.value);
                        const u = this.getContext(d);
                        const f = n.setContext(u);
                        const g = o.setContext(u);
                        Sh(this, f, l, a, g);
                    }
                }),
            s.display)
        ) {
            for (t.save(), r = a - 1; r >= 0; r--) {
                const h = s.setContext(this.getPointLabelContext(r));
                const { color: d, lineWidth: u } = h;
                !u ||
                    !d ||
                    ((t.lineWidth = u),
                    (t.strokeStyle = d),
                    t.setLineDash(h.borderDash),
                    (t.lineDashOffset = h.borderDashOffset),
                    (l = this.getDistanceFromCenterForValue(
                        e.reverse ? this.min : this.max,
                    )),
                    (c = this.getPointPosition(r, l)),
                    t.beginPath(),
                    t.moveTo(this.xCenter, this.yCenter),
                    t.lineTo(c.x, c.y),
                    t.stroke());
            }
            t.restore();
        }
    }

    drawBorder() {}
    drawLabels() {
        const t = this.ctx;
        const e = this.options;
        const s = e.ticks;
        if (!s.display) return;
        const n = this.getIndexAngle(0);
        let o;
        let a;
        (t.save(),
            t.translate(this.xCenter, this.yCenter),
            t.rotate(n),
            (t.textAlign = "center"),
            (t.textBaseline = "middle"),
            this.ticks.forEach((r, l) => {
                if (l === 0 && this.min >= 0 && !e.reverse) return;
                const c = s.setContext(this.getContext(l));
                const h = $(c.font);
                if (
                    ((o = this.getDistanceFromCenterForValue(
                        this.ticks[l].value,
                    )),
                    c.showLabelBackdrop)
                ) {
                    ((t.font = h.string),
                        (a = t.measureText(r.label).width),
                        (t.fillStyle = c.backdropColor));
                    const d = q(c.backdropPadding);
                    t.fillRect(
                        -a / 2 - d.left,
                        -o - h.size / 2 - d.top,
                        a + d.width,
                        h.size + d.height,
                    );
                }
                Ct(t, r.label, 0, -o, h, {
                    color: c.color,
                    strokeColor: c.textStrokeColor,
                    strokeWidth: c.textStrokeWidth,
                });
            }),
            t.restore());
    }

    drawTitle() {}
};
(M(jt, "id", "radialLinear"),
    M(jt, "defaults", {
        display: !0,
        animate: !0,
        position: "chartArea",
        angleLines: {
            display: !0,
            lineWidth: 1,
            borderDash: [],
            borderDashOffset: 0,
        },
        grid: { circular: !1 },
        startAngle: 0,
        ticks: { showLabelBackdrop: !0, callback: we.formatters.numeric },
        pointLabels: {
            backdropColor: void 0,
            backdropPadding: 2,
            display: !0,
            font: { size: 10 },
            callback(t) {
                return t;
            },
            padding: 5,
            centerPointLabels: !1,
        },
    }),
    M(jt, "defaultRoutes", {
        "angleLines.color": "borderColor",
        "pointLabels.color": "color",
        "ticks.color": "color",
    }),
    M(jt, "descriptors", { angleLines: { _fallback: "grid" } }));
const Oi = {
    millisecond: { common: !0, size: 1, steps: 1e3 },
    second: { common: !0, size: 1e3, steps: 60 },
    minute: { common: !0, size: 6e4, steps: 60 },
    hour: { common: !0, size: 36e5, steps: 24 },
    day: { common: !0, size: 864e5, steps: 30 },
    week: { common: !1, size: 6048e5, steps: 4 },
    month: { common: !0, size: 2628e6, steps: 12 },
    quarter: { common: !1, size: 7884e6, steps: 4 },
    year: { common: !0, size: 3154e7 },
};
const tt = Object.keys(Oi);
function So(i, t) {
    return i - t;
}
function Po(i, t) {
    if (A(t)) return null;
    const e = i._adapter;
    const { parser: s, round: n, isoWeekday: o } = i._parseOpts;
    let a = t;
    return (
        typeof s === "function" && (a = s(a)),
        N(a) || (a = typeof s === "string" ? e.parse(a, s) : e.parse(a)),
        a === null
            ? null
            : (n &&
                  (a =
                      n === "week" && (Bt(o) || o === !0)
                          ? e.startOf(a, "isoWeek", o)
                          : e.startOf(a, n)),
              +a)
    );
}
function Do(i, t, e, s) {
    const n = tt.length;
    for (let o = tt.indexOf(i); o < n - 1; ++o) {
        const a = Oi[tt[o]];
        const r = a.steps ? a.steps : Number.MAX_SAFE_INTEGER;
        if (a.common && Math.ceil((e - t) / (r * a.size)) <= s) return tt[o];
    }
    return tt[n - 1];
}
function Dh(i, t, e, s, n) {
    for (let o = tt.length - 1; o >= tt.indexOf(e); o--) {
        const a = tt[o];
        if (Oi[a].common && i._adapter.diff(n, s, a) >= t - 1) return a;
    }
    return tt[e ? tt.indexOf(e) : 0];
}
function Oh(i) {
    for (let t = tt.indexOf(i) + 1, e = tt.length; t < e; ++t) {
        if (Oi[tt[t]].common) return tt[t];
    }
}
function Oo(i, t, e) {
    if (!e) i[t] = !0;
    else if (e.length) {
        const { lo: s, hi: n } = ni(e, t);
        const o = e[s] >= t ? e[s] : e[n];
        i[o] = !0;
    }
}
function Ch(i, t, e, s) {
    const n = i._adapter;
    const o = +n.startOf(t[0].value, s);
    const a = t[t.length - 1].value;
    let r;
    let l;
    for (r = o; r <= a; r = +n.add(r, 1, s)) {
        ((l = e[r]), l >= 0 && (t[l].major = !0));
    }
    return t;
}
function Co(i, t, e) {
    const s = [];
    const n = {};
    const o = t.length;
    let a;
    let r;
    for (a = 0; a < o; ++a) {
        ((r = t[a]), (n[r] = a), s.push({ value: r, major: !1 }));
    }
    return o === 0 || !e ? s : Ch(i, s, n, e);
}
const Yt = class extends Ut {
    constructor(t) {
        (super(t),
            (this._cache = { data: [], labels: [], all: [] }),
            (this._unit = "day"),
            (this._majorUnit = void 0),
            (this._offsets = {}),
            (this._normalized = !1),
            (this._parseOpts = void 0));
    }

    init(t, e = {}) {
        const s = t.time || (t.time = {});
        const n = (this._adapter = new Hr._date(t.adapters.date));
        (n.init(e),
            Qt(s.displayFormats, n.formats()),
            (this._parseOpts = {
                parser: s.parser,
                round: s.round,
                isoWeekday: s.isoWeekday,
            }),
            super.init(t),
            (this._normalized = e.normalized));
    }

    parse(t, e) {
        return t === void 0 ? null : Po(this, t);
    }

    beforeLayout() {
        (super.beforeLayout(),
            (this._cache = { data: [], labels: [], all: [] }));
    }

    determineDataLimits() {
        const t = this.options;
        const e = this._adapter;
        const s = t.time.unit || "day";
        let {
            min: n,
            max: o,
            minDefined: a,
            maxDefined: r,
        } = this.getUserBounds();
        function l(c) {
            (!a && !isNaN(c.min) && (n = Math.min(n, c.min)),
                !r && !isNaN(c.max) && (o = Math.max(o, c.max)));
        }
        ((!a || !r) &&
            (l(this._getLabelBounds()),
            (t.bounds !== "ticks" || t.ticks.source !== "labels") &&
                l(this.getMinMax(!1))),
            (n = N(n) && !isNaN(n) ? n : +e.startOf(Date.now(), s)),
            (o = N(o) && !isNaN(o) ? o : +e.endOf(Date.now(), s) + 1),
            (this.min = Math.min(n, o - 1)),
            (this.max = Math.max(n + 1, o)));
    }

    _getLabelBounds() {
        const t = this.getLabelTimestamps();
        let e = Number.POSITIVE_INFINITY;
        let s = Number.NEGATIVE_INFINITY;
        return (
            t.length && ((e = t[0]), (s = t[t.length - 1])),
            { min: e, max: s }
        );
    }

    buildTicks() {
        const t = this.options;
        const e = t.time;
        const s = t.ticks;
        const n =
            s.source === "labels"
                ? this.getLabelTimestamps()
                : this._generate();
        t.bounds === "ticks" &&
            n.length &&
            ((this.min = this._userMin || n[0]),
            (this.max = this._userMax || n[n.length - 1]));
        const o = this.min;
        const a = this.max;
        const r = cn(n, o, a);
        return (
            (this._unit =
                e.unit ||
                (s.autoSkip
                    ? Do(
                          e.minUnit,
                          this.min,
                          this.max,
                          this._getLabelCapacity(o),
                      )
                    : Dh(this, r.length, e.minUnit, this.min, this.max))),
            (this._majorUnit =
                !s.major.enabled || this._unit === "year"
                    ? void 0
                    : Oh(this._unit)),
            this.initOffsets(n),
            t.reverse && r.reverse(),
            Co(this, r, this._majorUnit)
        );
    }

    afterAutoSkip() {
        this.options.offsetAfterAutoskip &&
            this.initOffsets(this.ticks.map((t) => +t.value));
    }

    initOffsets(t = []) {
        let e = 0;
        let s = 0;
        let n;
        let o;
        this.options.offset &&
            t.length &&
            ((n = this.getDecimalForValue(t[0])),
            t.length === 1
                ? (e = 1 - n)
                : (e = (this.getDecimalForValue(t[1]) - n) / 2),
            (o = this.getDecimalForValue(t[t.length - 1])),
            t.length === 1
                ? (s = o)
                : (s = (o - this.getDecimalForValue(t[t.length - 2])) / 2));
        const a = t.length < 3 ? 0.5 : 0.25;
        ((e = Y(e, 0, a)),
            (s = Y(s, 0, a)),
            (this._offsets = { start: e, end: s, factor: 1 / (e + 1 + s) }));
    }

    _generate() {
        const t = this._adapter;
        const e = this.min;
        const s = this.max;
        const n = this.options;
        const o = n.time;
        const a = o.unit || Do(o.minUnit, e, s, this._getLabelCapacity(e));
        const r = D(n.ticks.stepSize, 1);
        const l = a === "week" ? o.isoWeekday : !1;
        const c = Bt(l) || l === !0;
        const h = {};
        let d = e;
        let u;
        let f;
        if (
            (c && (d = +t.startOf(d, "isoWeek", l)),
            (d = +t.startOf(d, c ? "day" : a)),
            t.diff(s, e, a) > 1e5 * r)
        ) {
            throw new Error(
                e +
                    " and " +
                    s +
                    " are too far apart with stepSize of " +
                    r +
                    " " +
                    a,
            );
        }
        const g = n.ticks.source === "data" && this.getDataTimestamps();
        for (u = d, f = 0; u < s; u = +t.add(u, r, a), f++) Oo(h, u, g);
        return (
            (u === s || n.bounds === "ticks" || f === 1) && Oo(h, u, g),
            Object.keys(h)
                .sort(So)
                .map((p) => +p)
        );
    }

    getLabelForValue(t) {
        const e = this._adapter;
        const s = this.options.time;
        return s.tooltipFormat
            ? e.format(t, s.tooltipFormat)
            : e.format(t, s.displayFormats.datetime);
    }

    format(t, e) {
        const n = this.options.time.displayFormats;
        const o = this._unit;
        const a = e || n[o];
        return this._adapter.format(t, a);
    }

    _tickFormatFunction(t, e, s, n) {
        const o = this.options;
        const a = o.ticks.callback;
        if (a) return F(a, [t, e, s], this);
        const r = o.time.displayFormats;
        const l = this._unit;
        const c = this._majorUnit;
        const h = l && r[l];
        const d = c && r[c];
        const u = s[e];
        const f = c && d && u && u.major;
        return this._adapter.format(t, n || (f ? d : h));
    }

    generateTickLabels(t) {
        let e, s, n;
        for (e = 0, s = t.length; e < s; ++e) {
            ((n = t[e]), (n.label = this._tickFormatFunction(n.value, e, t)));
        }
    }

    getDecimalForValue(t) {
        return t === null ? NaN : (t - this.min) / (this.max - this.min);
    }

    getPixelForValue(t) {
        const e = this._offsets;
        const s = this.getDecimalForValue(t);
        return this.getPixelForDecimal((e.start + s) * e.factor);
    }

    getValueForPixel(t) {
        const e = this._offsets;
        const s = this.getDecimalForPixel(t) / e.factor - e.end;
        return this.min + s * (this.max - this.min);
    }

    _getLabelSize(t) {
        const e = this.options.ticks;
        const s = this.ctx.measureText(t).width;
        const n = ot(this.isHorizontal() ? e.maxRotation : e.minRotation);
        const o = Math.cos(n);
        const a = Math.sin(n);
        const r = this._resolveTickFontOptions(0).size;
        return { w: s * o + r * a, h: s * a + r * o };
    }

    _getLabelCapacity(t) {
        const e = this.options.time;
        const s = e.displayFormats;
        const n = s[e.unit] || s.millisecond;
        const o = this._tickFormatFunction(
            t,
            0,
            Co(this, [t], this._majorUnit),
            n,
        );
        const a = this._getLabelSize(o);
        const r =
            Math.floor(
                this.isHorizontal() ? this.width / a.w : this.height / a.h,
            ) - 1;
        return r > 0 ? r : 1;
    }

    getDataTimestamps() {
        let t = this._cache.data || [];
        let e;
        let s;
        if (t.length) return t;
        const n = this.getMatchingVisibleMetas();
        if (this._normalized && n.length) {
            return (this._cache.data =
                n[0].controller.getAllParsedValues(this));
        }
        for (e = 0, s = n.length; e < s; ++e) {
            t = t.concat(n[e].controller.getAllParsedValues(this));
        }
        return (this._cache.data = this.normalize(t));
    }

    getLabelTimestamps() {
        const t = this._cache.labels || [];
        let e;
        let s;
        if (t.length) return t;
        const n = this.getLabels();
        for (e = 0, s = n.length; e < s; ++e) t.push(Po(this, n[e]));
        return (this._cache.labels = this._normalized ? t : this.normalize(t));
    }

    normalize(t) {
        return $i(t.sort(So));
    }
};
(M(Yt, "id", "time"),
    M(Yt, "defaults", {
        bounds: "data",
        adapters: {},
        time: {
            parser: !1,
            unit: !1,
            round: !1,
            isoWeekday: !1,
            minUnit: "millisecond",
            displayFormats: {},
        },
        ticks: { source: "auto", callback: !1, major: { enabled: !1 } },
    }));
function xi(i, t, e) {
    let s = 0;
    let n = i.length - 1;
    let o;
    let a;
    let r;
    let l;
    e
        ? (t >= i[s].pos &&
              t <= i[n].pos &&
              ({ lo: s, hi: n } = ct(i, "pos", t)),
          ({ pos: o, time: r } = i[s]),
          ({ pos: a, time: l } = i[n]))
        : (t >= i[s].time &&
              t <= i[n].time &&
              ({ lo: s, hi: n } = ct(i, "time", t)),
          ({ time: o, pos: r } = i[s]),
          ({ time: a, pos: l } = i[n]));
    const c = a - o;
    return c ? r + ((l - r) * (t - o)) / c : r;
}
const Be = class extends Yt {
    constructor(t) {
        (super(t),
            (this._table = []),
            (this._minPos = void 0),
            (this._tableRange = void 0));
    }

    initOffsets() {
        const t = this._getTimestampsForTable();
        const e = (this._table = this.buildLookupTable(t));
        ((this._minPos = xi(e, this.min)),
            (this._tableRange = xi(e, this.max) - this._minPos),
            super.initOffsets(t));
    }

    buildLookupTable(t) {
        const { min: e, max: s } = this;
        const n = [];
        const o = [];
        let a;
        let r;
        let l;
        let c;
        let h;
        for (a = 0, r = t.length; a < r; ++a) {
            ((c = t[a]), c >= e && c <= s && n.push(c));
        }
        if (n.length < 2) {
            return [
                { time: e, pos: 0 },
                { time: s, pos: 1 },
            ];
        }
        for (a = 0, r = n.length; a < r; ++a) {
            ((h = n[a + 1]),
                (l = n[a - 1]),
                (c = n[a]),
                Math.round((h + l) / 2) !== c &&
                    o.push({ time: c, pos: a / (r - 1) }));
        }
        return o;
    }

    _generate() {
        const t = this.min;
        const e = this.max;
        const s = super.getDataTimestamps();
        return (
            (!s.includes(t) || !s.length) && s.splice(0, 0, t),
            (!s.includes(e) || s.length === 1) && s.push(e),
            s.sort((n, o) => n - o)
        );
    }

    _getTimestampsForTable() {
        let t = this._cache.all || [];
        if (t.length) return t;
        const e = this.getDataTimestamps();
        const s = this.getLabelTimestamps();
        return (
            e.length && s.length
                ? (t = this.normalize(e.concat(s)))
                : (t = e.length ? e : s),
            (t = this._cache.all = t),
            t
        );
    }

    getDecimalForValue(t) {
        return (xi(this._table, t) - this._minPos) / this._tableRange;
    }

    getValueForPixel(t) {
        const e = this._offsets;
        const s = this.getDecimalForPixel(t) / e.factor - e.end;
        return xi(this._table, s * this._tableRange + this._minPos, !0);
    }
};
(M(Be, "id", "timeseries"), M(Be, "defaults", Yt.defaults));
const Ah = Object.freeze({
    __proto__: null,
    CategoryScale: Ie,
    LinearScale: Fe,
    LogarithmicScale: ze,
    RadialLinearScale: jt,
    TimeScale: Yt,
    TimeSeriesScale: Be,
});
const Go = [Nr, uc, rh, Ah];
at.register(...Go);
const Fs = at;
function Th({ dataChecksum: i, labels: t, values: e }) {
    return {
        dataChecksum: i,
        init() {
            (Alpine.effect(() => {
                Alpine.store("theme");
                const s = this.getChart();
                (s && s.destroy(), this.initChart());
            }),
                window
                    .matchMedia("(prefers-color-scheme: dark)")
                    .addEventListener("change", () => {
                        Alpine.store("theme") === "system" &&
                            this.$nextTick(() => {
                                const s = this.getChart();
                                (s && s.destroy(), this.initChart());
                            });
                    }));
        },
        initChart() {
            if (
                !(
                    !this.$refs.canvas ||
                    !this.$refs.backgroundColorElement ||
                    !this.$refs.borderColorElement
                )
            ) {
                return new Fs(this.$refs.canvas, {
                    type: "line",
                    data: {
                        labels: t,
                        datasets: [
                            {
                                data: e,
                                borderWidth: 2,
                                fill: "start",
                                tension: 0.5,
                                backgroundColor: getComputedStyle(
                                    this.$refs.backgroundColorElement,
                                ).color,
                                borderColor: getComputedStyle(
                                    this.$refs.borderColorElement,
                                ).color,
                            },
                        ],
                    },
                    options: {
                        animation: { duration: 0 },
                        elements: { point: { radius: 0 } },
                        maintainAspectRatio: !1,
                        plugins: { legend: { display: !1 } },
                        scales: { x: { display: !1 }, y: { display: !1 } },
                        tooltips: { enabled: !1 },
                    },
                });
            }
        },
        getChart() {
            return this.$refs.canvas ? Fs.getChart(this.$refs.canvas) : null;
        },
    };
}
export { Th as default };
/*! Bundled license information:

@kurkle/color/dist/color.esm.js:
  (*!
   * @kurkle/color v0.3.4
   * https://github.com/kurkle/color#readme
   * (c) 2024 Jukka Kurkela
   * Released under the MIT License
   *)

chart.js/dist/chunks/helpers.dataset.js:
chart.js/dist/chart.js:
  (*!
   * Chart.js v4.5.0
   * https://www.chartjs.org
   * (c) 2025 Chart.js Contributors
   * Released under the MIT License
   *)
*/
