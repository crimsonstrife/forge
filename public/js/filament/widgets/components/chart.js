const Xc = Object.defineProperty;
const Kc = (s, t, e) =>
    t in s
        ? Xc(s, t, { enumerable: !0, configurable: !0, writable: !0, value: e })
        : (s[t] = e);
const S = (s, t, e) => Kc(s, typeof t !== "symbol" ? t + "" : t, e);
function ks(s) {
    return (s + 0.5) | 0;
}
const ee = (s, t, e) => Math.max(Math.min(s, e), t);
function ws(s) {
    return ee(ks(s * 2.55), 0, 255);
}
function se(s) {
    return ee(ks(s * 255), 0, 255);
}
function Bt(s) {
    return ee(ks(s / 2.55) / 100, 0, 1);
}
function Or(s) {
    return ee(ks(s * 100), 0, 100);
}
const _t = {
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
const En = [..."0123456789ABCDEF"];
const Jc = (s) => En[s & 15];
const Qc = (s) => En[(s & 240) >> 4] + En[s & 15];
const vi = (s) => (s & 240) >> 4 === (s & 15);
const th = (s) => vi(s.r) && vi(s.g) && vi(s.b) && vi(s.a);
function eh(s) {
    const t = s.length;
    let e;
    return (
        s[0] === "#" &&
            (t === 4 || t === 5
                ? (e = {
                      r: 255 & (_t[s[1]] * 17),
                      g: 255 & (_t[s[2]] * 17),
                      b: 255 & (_t[s[3]] * 17),
                      a: t === 5 ? _t[s[4]] * 17 : 255,
                  })
                : (t === 7 || t === 9) &&
                  (e = {
                      r: (_t[s[1]] << 4) | _t[s[2]],
                      g: (_t[s[3]] << 4) | _t[s[4]],
                      b: (_t[s[5]] << 4) | _t[s[6]],
                      a: t === 9 ? (_t[s[7]] << 4) | _t[s[8]] : 255,
                  })),
        e
    );
}
const sh = (s, t) => (s < 255 ? t(s) : "");
function ih(s) {
    const t = th(s) ? Jc : Qc;
    return s ? "#" + t(s.r) + t(s.g) + t(s.b) + sh(s.a, t) : void 0;
}
const nh =
    /^(hsla?|hwb|hsv)\(\s*([-+.e\d]+)(?:deg)?[\s,]+([-+.e\d]+)%[\s,]+([-+.e\d]+)%(?:[\s,]+([-+.e\d]+)(%)?)?\s*\)$/;
function Cr(s, t, e) {
    const i = t * Math.min(e, 1 - e);
    const n = (o, r = (o + s / 30) % 12) =>
        e - i * Math.max(Math.min(r - 3, 9 - r, 1), -1);
    return [n(0), n(8), n(4)];
}
function oh(s, t, e) {
    const i = (n, o = (n + s / 60) % 6) =>
        e - e * t * Math.max(Math.min(o, 4 - o, 1), 0);
    return [i(5), i(3), i(1)];
}
function rh(s, t, e) {
    const i = Cr(s, 1, 0.5);
    let n;
    for (
        t + e > 1 && ((n = 1 / (t + e)), (t *= n), (e *= n)), n = 0;
        n < 3;
        n++
    ) {
        ((i[n] *= 1 - t - e), (i[n] += t));
    }
    return i;
}
function ah(s, t, e, i, n) {
    return s === n
        ? (t - e) / i + (t < e ? 6 : 0)
        : t === n
          ? (e - s) / i + 2
          : (s - t) / i + 4;
}
function In(s) {
    const e = s.r / 255;
    const i = s.g / 255;
    const n = s.b / 255;
    const o = Math.max(e, i, n);
    const r = Math.min(e, i, n);
    const a = (o + r) / 2;
    let l;
    let c;
    let h;
    return (
        o !== r &&
            ((h = o - r),
            (c = a > 0.5 ? h / (2 - o - r) : h / (o + r)),
            (l = ah(e, i, n, h, o)),
            (l = l * 60 + 0.5)),
        [l | 0, c || 0, a]
    );
}
function Cn(s, t, e, i) {
    return (Array.isArray(t) ? s(t[0], t[1], t[2]) : s(t, e, i)).map(se);
}
function Fn(s, t, e) {
    return Cn(Cr, s, t, e);
}
function lh(s, t, e) {
    return Cn(rh, s, t, e);
}
function ch(s, t, e) {
    return Cn(oh, s, t, e);
}
function Fr(s) {
    return ((s % 360) + 360) % 360;
}
function hh(s) {
    const t = nh.exec(s);
    let e = 255;
    let i;
    if (!t) return;
    t[5] !== i && (e = t[6] ? ws(+t[5]) : se(+t[5]));
    const n = Fr(+t[2]);
    const o = +t[3] / 100;
    const r = +t[4] / 100;
    return (
        t[1] === "hwb"
            ? (i = lh(n, o, r))
            : t[1] === "hsv"
              ? (i = ch(n, o, r))
              : (i = Fn(n, o, r)),
        { r: i[0], g: i[1], b: i[2], a: e }
    );
}
function uh(s, t) {
    let e = In(s);
    ((e[0] = Fr(e[0] + t)),
        (e = Fn(e)),
        (s.r = e[0]),
        (s.g = e[1]),
        (s.b = e[2]));
}
function dh(s) {
    if (!s) return;
    const t = In(s);
    const e = t[0];
    const i = Or(t[1]);
    const n = Or(t[2]);
    return s.a < 255
        ? `hsla(${e}, ${i}%, ${n}%, ${Bt(s.a)})`
        : `hsl(${e}, ${i}%, ${n}%)`;
}
const Dr = {
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
const Er = {
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
function fh() {
    const s = {};
    const t = Object.keys(Er);
    const e = Object.keys(Dr);
    let i;
    let n;
    let o;
    let r;
    let a;
    for (i = 0; i < t.length; i++) {
        for (r = a = t[i], n = 0; n < e.length; n++) {
            ((o = e[n]), (a = a.replace(o, Dr[o])));
        }
        ((o = parseInt(Er[r], 16)),
            (s[a] = [(o >> 16) & 255, (o >> 8) & 255, o & 255]));
    }
    return s;
}
let Ti;
function mh(s) {
    Ti || ((Ti = fh()), (Ti.transparent = [0, 0, 0, 0]));
    const t = Ti[s.toLowerCase()];
    return t && { r: t[0], g: t[1], b: t[2], a: t.length === 4 ? t[3] : 255 };
}
const gh =
    /^rgba?\(\s*([-+.\d]+)(%)?[\s,]+([-+.e\d]+)(%)?[\s,]+([-+.e\d]+)(%)?(?:[\s,/]+([-+.e\d]+)(%)?)?\s*\)$/;
function ph(s) {
    const t = gh.exec(s);
    let e = 255;
    let i;
    let n;
    let o;
    if (t) {
        if (t[7] !== i) {
            const r = +t[7];
            e = t[8] ? ws(r) : ee(r * 255, 0, 255);
        }
        return (
            (i = +t[1]),
            (n = +t[3]),
            (o = +t[5]),
            (i = 255 & (t[2] ? ws(i) : ee(i, 0, 255))),
            (n = 255 & (t[4] ? ws(n) : ee(n, 0, 255))),
            (o = 255 & (t[6] ? ws(o) : ee(o, 0, 255))),
            { r: i, g: n, b: o, a: e }
        );
    }
}
function yh(s) {
    return (
        s &&
        (s.a < 255
            ? `rgba(${s.r}, ${s.g}, ${s.b}, ${Bt(s.a)})`
            : `rgb(${s.r}, ${s.g}, ${s.b})`)
    );
}
const Dn = (s) =>
    s <= 0.0031308 ? s * 12.92 : Math.pow(s, 1 / 2.4) * 1.055 - 0.055;
const Re = (s) =>
    s <= 0.04045 ? s / 12.92 : Math.pow((s + 0.055) / 1.055, 2.4);
function bh(s, t, e) {
    const i = Re(Bt(s.r));
    const n = Re(Bt(s.g));
    const o = Re(Bt(s.b));
    return {
        r: se(Dn(i + e * (Re(Bt(t.r)) - i))),
        g: se(Dn(n + e * (Re(Bt(t.g)) - n))),
        b: se(Dn(o + e * (Re(Bt(t.b)) - o))),
        a: s.a + e * (t.a - s.a),
    };
}
function Oi(s, t, e) {
    if (s) {
        let i = In(s);
        ((i[t] = Math.max(0, Math.min(i[t] + i[t] * e, t === 0 ? 360 : 1))),
            (i = Fn(i)),
            (s.r = i[0]),
            (s.g = i[1]),
            (s.b = i[2]));
    }
}
function Ar(s, t) {
    return s && Object.assign(t || {}, s);
}
function Ir(s) {
    let t = { r: 0, g: 0, b: 0, a: 255 };
    return (
        Array.isArray(s)
            ? s.length >= 3 &&
              ((t = { r: s[0], g: s[1], b: s[2], a: 255 }),
              s.length > 3 && (t.a = se(s[3])))
            : ((t = Ar(s, { r: 0, g: 0, b: 0, a: 1 })), (t.a = se(t.a))),
        t
    );
}
function xh(s) {
    return s.charAt(0) === "r" ? ph(s) : hh(s);
}
const Ss = class s {
    constructor(t) {
        if (t instanceof s) return t;
        const e = typeof t;
        let i;
        (e === "object"
            ? (i = Ir(t))
            : e === "string" && (i = eh(t) || mh(t) || xh(t)),
            (this._rgb = i),
            (this._valid = !!i));
    }

    get valid() {
        return this._valid;
    }

    get rgb() {
        const t = Ar(this._rgb);
        return (t && (t.a = Bt(t.a)), t);
    }

    set rgb(t) {
        this._rgb = Ir(t);
    }

    rgbString() {
        return this._valid ? yh(this._rgb) : void 0;
    }

    hexString() {
        return this._valid ? ih(this._rgb) : void 0;
    }

    hslString() {
        return this._valid ? dh(this._rgb) : void 0;
    }

    mix(t, e) {
        if (t) {
            const i = this.rgb;
            const n = t.rgb;
            let o;
            const r = e === o ? 0.5 : e;
            const a = 2 * r - 1;
            const l = i.a - n.a;
            const c = ((a * l === -1 ? a : (a + l) / (1 + a * l)) + 1) / 2;
            ((o = 1 - c),
                (i.r = 255 & (c * i.r + o * n.r + 0.5)),
                (i.g = 255 & (c * i.g + o * n.g + 0.5)),
                (i.b = 255 & (c * i.b + o * n.b + 0.5)),
                (i.a = r * i.a + (1 - r) * n.a),
                (this.rgb = i));
        }
        return this;
    }

    interpolate(t, e) {
        return (t && (this._rgb = bh(this._rgb, t._rgb, e)), this);
    }

    clone() {
        return new s(this.rgb);
    }

    alpha(t) {
        return ((this._rgb.a = se(t)), this);
    }

    clearer(t) {
        const e = this._rgb;
        return ((e.a *= 1 - t), this);
    }

    greyscale() {
        const t = this._rgb;
        const e = ks(t.r * 0.3 + t.g * 0.59 + t.b * 0.11);
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
        return (Oi(this._rgb, 2, t), this);
    }

    darken(t) {
        return (Oi(this._rgb, 2, -t), this);
    }

    saturate(t) {
        return (Oi(this._rgb, 1, t), this);
    }

    desaturate(t) {
        return (Oi(this._rgb, 1, -t), this);
    }

    rotate(t) {
        return (uh(this._rgb, t), this);
    }
};
function Nt() {}
const jr = (() => {
    let s = 0;
    return () => s++;
})();
function A(s) {
    return s == null;
}
function $(s) {
    if (Array.isArray && Array.isArray(s)) return !0;
    const t = Object.prototype.toString.call(s);
    return t.slice(0, 7) === "[object" && t.slice(-6) === "Array]";
}
function L(s) {
    return (
        s !== null && Object.prototype.toString.call(s) === "[object Object]"
    );
}
function Z(s) {
    return (typeof s === "number" || s instanceof Number) && isFinite(+s);
}
function ut(s, t) {
    return Z(s) ? s : t;
}
function I(s, t) {
    return typeof s > "u" ? t : s;
}
const Ur = (s, t) =>
    typeof s === "string" && s.endsWith("%") ? parseFloat(s) / 100 : +s / t;
const Nn = (s, t) =>
    typeof s === "string" && s.endsWith("%") ? (parseFloat(s) / 100) * t : +s;
function B(s, t, e) {
    if (s && typeof s.call === "function") return s.apply(e, t);
}
function V(s, t, e, i) {
    let n, o, r;
    if ($(s)) {
        if (((o = s.length), i)) {
            for (n = o - 1; n >= 0; n--) t.call(e, s[n], n);
        } else for (n = 0; n < o; n++) t.call(e, s[n], n);
    } else if (L(s)) {
        for (r = Object.keys(s), o = r.length, n = 0; n < o; n++) {
            t.call(e, s[r[n]], r[n]);
        }
    }
}
function Ts(s, t) {
    let e, i, n, o;
    if (!s || !t || s.length !== t.length) return !1;
    for (e = 0, i = s.length; e < i; ++e) {
        if (
            ((n = s[e]),
            (o = t[e]),
            n.datasetIndex !== o.datasetIndex || n.index !== o.index)
        ) {
            return !1;
        }
    }
    return !0;
}
function Fi(s) {
    if ($(s)) return s.map(Fi);
    if (L(s)) {
        const t = Object.create(null);
        const e = Object.keys(s);
        const i = e.length;
        let n = 0;
        for (; n < i; ++n) t[e[n]] = Fi(s[e[n]]);
        return t;
    }
    return s;
}
function Yr(s) {
    return ["__proto__", "prototype", "constructor"].indexOf(s) === -1;
}
function _h(s, t, e, i) {
    if (!Yr(s)) return;
    const n = t[s];
    const o = e[s];
    L(n) && L(o) ? ze(n, o, i) : (t[s] = Fi(o));
}
function ze(s, t, e) {
    const i = $(t) ? t : [t];
    const n = i.length;
    if (!L(s)) return s;
    e = e || {};
    const o = e.merger || _h;
    let r;
    for (let a = 0; a < n; ++a) {
        if (((r = i[a]), !L(r))) continue;
        const l = Object.keys(r);
        for (let c = 0, h = l.length; c < h; ++c) o(l[c], s, r, e);
    }
    return s;
}
function He(s, t) {
    return ze(s, t, { merger: wh });
}
function wh(s, t, e) {
    if (!Yr(s)) return;
    const i = t[s];
    const n = e[s];
    L(i) && L(n)
        ? He(i, n)
        : Object.prototype.hasOwnProperty.call(t, s) || (t[s] = Fi(n));
}
const Lr = { "": (s) => s, x: (s) => s.x, y: (s) => s.y };
function Sh(s) {
    const t = s.split(".");
    const e = [];
    let i = "";
    for (const n of t) {
        ((i += n),
            i.endsWith("\\")
                ? (i = i.slice(0, -1) + ".")
                : (e.push(i), (i = "")));
    }
    return e;
}
function kh(s) {
    const t = Sh(s);
    return (e) => {
        for (const i of t) {
            if (i === "") break;
            e = e && e[i];
        }
        return e;
    };
}
function Ut(s, t) {
    return (Lr[t] || (Lr[t] = kh(t)))(s);
}
function Ni(s) {
    return s.charAt(0).toUpperCase() + s.slice(1);
}
const Be = (s) => typeof s < "u";
const $t = (s) => typeof s === "function";
const Rn = (s, t) => {
    if (s.size !== t.size) return !1;
    for (const e of s) if (!t.has(e)) return !1;
    return !0;
};
function Zr(s) {
    return (
        s.type === "mouseup" || s.type === "click" || s.type === "contextmenu"
    );
}
const N = Math.PI;
const j = 2 * N;
const Mh = j + N;
const Ai = Number.POSITIVE_INFINITY;
const vh = N / 180;
const X = N / 2;
const be = N / 4;
const Pr = (N * 2) / 3;
const jt = Math.log10;
const Ot = Math.sign;
function $e(s, t, e) {
    return Math.abs(s - t) < e;
}
function Wn(s) {
    const t = Math.round(s);
    s = $e(s, t, s / 1e3) ? t : s;
    const e = Math.pow(10, Math.floor(jt(s)));
    const i = s / e;
    return (i <= 1 ? 1 : i <= 2 ? 2 : i <= 5 ? 5 : 10) * e;
}
function qr(s) {
    const t = [];
    const e = Math.sqrt(s);
    let i;
    for (i = 1; i < e; i++) s % i === 0 && (t.push(i), t.push(s / i));
    return (e === (e | 0) && t.push(e), t.sort((n, o) => n - o).pop(), t);
}
function Th(s) {
    return (
        typeof s === "symbol" ||
        (typeof s === "object" &&
            s !== null &&
            !(Symbol.toPrimitive in s || "toString" in s || "valueOf" in s))
    );
}
function we(s) {
    return !Th(s) && !isNaN(parseFloat(s)) && isFinite(s);
}
function Gr(s, t) {
    const e = Math.round(s);
    return e - t <= s && e + t >= s;
}
function zn(s, t, e) {
    let i, n, o;
    for (i = 0, n = s.length; i < n; i++) {
        ((o = s[i][e]),
            isNaN(o) ||
                ((t.min = Math.min(t.min, o)), (t.max = Math.max(t.max, o))));
    }
}
function wt(s) {
    return s * (N / 180);
}
function Ri(s) {
    return s * (180 / N);
}
function Vn(s) {
    if (!Z(s)) return;
    let t = 1;
    let e = 0;
    for (; Math.round(s * t) / t !== s; ) ((t *= 10), e++);
    return e;
}
function Hn(s, t) {
    const e = t.x - s.x;
    const i = t.y - s.y;
    const n = Math.sqrt(e * e + i * i);
    let o = Math.atan2(i, e);
    return (o < -0.5 * N && (o += j), { angle: o, distance: n });
}
function Li(s, t) {
    return Math.sqrt(Math.pow(t.x - s.x, 2) + Math.pow(t.y - s.y, 2));
}
function Oh(s, t) {
    return ((s - t + Mh) % j) - N;
}
function ot(s) {
    return ((s % j) + j) % j;
}
function je(s, t, e, i) {
    const n = ot(s);
    const o = ot(t);
    const r = ot(e);
    const a = ot(o - n);
    const l = ot(r - n);
    const c = ot(n - o);
    const h = ot(n - r);
    return n === o || n === r || (i && o === r) || (a > l && c < h);
}
function tt(s, t, e) {
    return Math.max(t, Math.min(e, s));
}
function Xr(s) {
    return tt(s, -32768, 32767);
}
function Rt(s, t, e, i = 1e-6) {
    return s >= Math.min(t, e) - i && s <= Math.max(t, e) + i;
}
function Wi(s, t, e) {
    e = e || ((r) => s[r] < t);
    let i = s.length - 1;
    let n = 0;
    let o;
    for (; i - n > 1; ) ((o = (n + i) >> 1), e(o) ? (n = o) : (i = o));
    return { lo: n, hi: i };
}
const Lt = (s, t, e, i) =>
    Wi(
        s,
        e,
        i
            ? (n) => {
                  const o = s[n][t];
                  return o < e || (o === e && s[n + 1][t] === e);
              }
            : (n) => s[n][t] < e,
    );
const Kr = (s, t, e) => Wi(s, e, (i) => s[i][t] >= e);
function Jr(s, t, e) {
    let i = 0;
    let n = s.length;
    for (; i < n && s[i] < t; ) i++;
    for (; n > i && s[n - 1] > e; ) n--;
    return i > 0 || n < s.length ? s.slice(i, n) : s;
}
const Qr = ["push", "pop", "shift", "splice", "unshift"];
function ta(s, t) {
    if (s._chartjs) {
        s._chartjs.listeners.push(t);
        return;
    }
    (Object.defineProperty(s, "_chartjs", {
        configurable: !0,
        enumerable: !1,
        value: { listeners: [t] },
    }),
        Qr.forEach((e) => {
            const i = "_onData" + Ni(e);
            const n = s[e];
            Object.defineProperty(s, e, {
                configurable: !0,
                enumerable: !1,
                value(...o) {
                    const r = n.apply(this, o);
                    return (
                        s._chartjs.listeners.forEach((a) => {
                            typeof a[i] === "function" && a[i](...o);
                        }),
                        r
                    );
                },
            });
        }));
}
function Bn(s, t) {
    const e = s._chartjs;
    if (!e) return;
    const i = e.listeners;
    const n = i.indexOf(t);
    (n !== -1 && i.splice(n, 1),
        !(i.length > 0) &&
            (Qr.forEach((o) => {
                delete s[o];
            }),
            delete s._chartjs));
}
function $n(s) {
    const t = new Set(s);
    return t.size === s.length ? s : Array.from(t);
}
const jn = (function () {
    return typeof window > "u"
        ? function (s) {
              return s();
          }
        : window.requestAnimationFrame;
})();
function Un(s, t) {
    let e = [];
    let i = !1;
    return function (...n) {
        ((e = n),
            i ||
                ((i = !0),
                jn.call(window, () => {
                    ((i = !1), s.apply(t, e));
                })));
    };
}
function ea(s, t) {
    let e;
    return function (...i) {
        return (
            t ? (clearTimeout(e), (e = setTimeout(s, t, i))) : s.apply(this, i),
            t
        );
    };
}
const zi = (s) => (s === "start" ? "left" : s === "end" ? "right" : "center");
const rt = (s, t, e) => (s === "start" ? t : s === "end" ? e : (t + e) / 2);
const sa = (s, t, e, i) =>
    s === (i ? "left" : "right") ? e : s === "center" ? (t + e) / 2 : t;
function Yn(s, t, e) {
    const i = t.length;
    let n = 0;
    let o = i;
    if (s._sorted) {
        const { iScale: r, vScale: a, _parsed: l } = s;
        const c =
            s.dataset && s.dataset.options ? s.dataset.options.spanGaps : null;
        const h = r.axis;
        const {
            min: u,
            max: d,
            minDefined: f,
            maxDefined: m,
        } = r.getUserBounds();
        if (f) {
            if (
                ((n = Math.min(
                    Lt(l, h, u).lo,
                    e ? i : Lt(t, h, r.getPixelForValue(u)).lo,
                )),
                c)
            ) {
                const g = l
                    .slice(0, n + 1)
                    .reverse()
                    .findIndex((p) => !A(p[a.axis]));
                n -= Math.max(0, g);
            }
            n = tt(n, 0, i - 1);
        }
        if (m) {
            let g = Math.max(
                Lt(l, r.axis, d, !0).hi + 1,
                e ? 0 : Lt(t, h, r.getPixelForValue(d), !0).hi + 1,
            );
            if (c) {
                const p = l.slice(g - 1).findIndex((y) => !A(y[a.axis]));
                g += Math.max(0, p);
            }
            o = tt(g, n, i) - n;
        } else o = i - n;
    }
    return { start: n, count: o };
}
function Zn(s) {
    const { xScale: t, yScale: e, _scaleRanges: i } = s;
    const n = { xmin: t.min, xmax: t.max, ymin: e.min, ymax: e.max };
    if (!i) return ((s._scaleRanges = n), !0);
    const o =
        i.xmin !== t.min ||
        i.xmax !== t.max ||
        i.ymin !== e.min ||
        i.ymax !== e.max;
    return (Object.assign(i, n), o);
}
const Di = (s) => s === 0 || s === 1;
const Nr = (s, t, e) =>
    -(Math.pow(2, 10 * (s -= 1)) * Math.sin(((s - t) * j) / e));
const Rr = (s, t, e) => Math.pow(2, -10 * s) * Math.sin(((s - t) * j) / e) + 1;
var We = {
    linear: (s) => s,
    easeInQuad: (s) => s * s,
    easeOutQuad: (s) => -s * (s - 2),
    easeInOutQuad: (s) =>
        (s /= 0.5) < 1 ? 0.5 * s * s : -0.5 * (--s * (s - 2) - 1),
    easeInCubic: (s) => s * s * s,
    easeOutCubic: (s) => (s -= 1) * s * s + 1,
    easeInOutCubic: (s) =>
        (s /= 0.5) < 1 ? 0.5 * s * s * s : 0.5 * ((s -= 2) * s * s + 2),
    easeInQuart: (s) => s * s * s * s,
    easeOutQuart: (s) => -((s -= 1) * s * s * s - 1),
    easeInOutQuart: (s) =>
        (s /= 0.5) < 1
            ? 0.5 * s * s * s * s
            : -0.5 * ((s -= 2) * s * s * s - 2),
    easeInQuint: (s) => s * s * s * s * s,
    easeOutQuint: (s) => (s -= 1) * s * s * s * s + 1,
    easeInOutQuint: (s) =>
        (s /= 0.5) < 1
            ? 0.5 * s * s * s * s * s
            : 0.5 * ((s -= 2) * s * s * s * s + 2),
    easeInSine: (s) => -Math.cos(s * X) + 1,
    easeOutSine: (s) => Math.sin(s * X),
    easeInOutSine: (s) => -0.5 * (Math.cos(N * s) - 1),
    easeInExpo: (s) => (s === 0 ? 0 : Math.pow(2, 10 * (s - 1))),
    easeOutExpo: (s) => (s === 1 ? 1 : -Math.pow(2, -10 * s) + 1),
    easeInOutExpo: (s) =>
        Di(s)
            ? s
            : s < 0.5
              ? 0.5 * Math.pow(2, 10 * (s * 2 - 1))
              : 0.5 * (-Math.pow(2, -10 * (s * 2 - 1)) + 2),
    easeInCirc: (s) => (s >= 1 ? s : -(Math.sqrt(1 - s * s) - 1)),
    easeOutCirc: (s) => Math.sqrt(1 - (s -= 1) * s),
    easeInOutCirc: (s) =>
        (s /= 0.5) < 1
            ? -0.5 * (Math.sqrt(1 - s * s) - 1)
            : 0.5 * (Math.sqrt(1 - (s -= 2) * s) + 1),
    easeInElastic: (s) => (Di(s) ? s : Nr(s, 0.075, 0.3)),
    easeOutElastic: (s) => (Di(s) ? s : Rr(s, 0.075, 0.3)),
    easeInOutElastic(s) {
        return Di(s)
            ? s
            : s < 0.5
              ? 0.5 * Nr(s * 2, 0.1125, 0.45)
              : 0.5 + 0.5 * Rr(s * 2 - 1, 0.1125, 0.45);
    },
    easeInBack(s) {
        return s * s * ((1.70158 + 1) * s - 1.70158);
    },
    easeOutBack(s) {
        return (s -= 1) * s * ((1.70158 + 1) * s + 1.70158) + 1;
    },
    easeInOutBack(s) {
        let t = 1.70158;
        return (s /= 0.5) < 1
            ? 0.5 * (s * s * (((t *= 1.525) + 1) * s - t))
            : 0.5 * ((s -= 2) * s * (((t *= 1.525) + 1) * s + t) + 2);
    },
    easeInBounce: (s) => 1 - We.easeOutBounce(1 - s),
    easeOutBounce(s) {
        return s < 1 / 2.75
            ? 7.5625 * s * s
            : s < 2 / 2.75
              ? 7.5625 * (s -= 1.5 / 2.75) * s + 0.75
              : s < 2.5 / 2.75
                ? 7.5625 * (s -= 2.25 / 2.75) * s + 0.9375
                : 7.5625 * (s -= 2.625 / 2.75) * s + 0.984375;
    },
    easeInOutBounce: (s) =>
        s < 0.5
            ? We.easeInBounce(s * 2) * 0.5
            : We.easeOutBounce(s * 2 - 1) * 0.5 + 0.5,
};
function qn(s) {
    if (s && typeof s === "object") {
        const t = s.toString();
        return (
            t === "[object CanvasPattern]" || t === "[object CanvasGradient]"
        );
    }
    return !1;
}
function Gn(s) {
    return qn(s) ? s : new Ss(s);
}
function An(s) {
    return qn(s) ? s : new Ss(s).saturate(0.5).darken(0.1).hexString();
}
const Dh = ["x", "y", "borderWidth", "radius", "tension"];
const Eh = ["color", "borderColor", "backgroundColor"];
function Ih(s) {
    (s.set("animation", {
        delay: void 0,
        duration: 1e3,
        easing: "easeOutQuart",
        fn: void 0,
        from: void 0,
        loop: void 0,
        to: void 0,
        type: void 0,
    }),
        s.describe("animation", {
            _fallback: !1,
            _indexable: !1,
            _scriptable: (t) =>
                t !== "onProgress" && t !== "onComplete" && t !== "fn",
        }),
        s.set("animations", {
            colors: { type: "color", properties: Eh },
            numbers: { type: "number", properties: Dh },
        }),
        s.describe("animations", { _fallback: "animation" }),
        s.set("transitions", {
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
function Ch(s) {
    s.set("layout", {
        autoPadding: !0,
        padding: { top: 0, right: 0, bottom: 0, left: 0 },
    });
}
const Wr = new Map();
function Fh(s, t) {
    t = t || {};
    const e = s + JSON.stringify(t);
    let i = Wr.get(e);
    return (i || ((i = new Intl.NumberFormat(s, t)), Wr.set(e, i)), i);
}
function Ue(s, t, e) {
    return Fh(t, e).format(s);
}
var ia = {
    values(s) {
        return $(s) ? s : "" + s;
    },
    numeric(s, t, e) {
        if (s === 0) return "0";
        const i = this.chart.options.locale;
        let n;
        let o = s;
        if (e.length > 1) {
            const c = Math.max(
                Math.abs(e[0].value),
                Math.abs(e[e.length - 1].value),
            );
            ((c < 1e-4 || c > 1e15) && (n = "scientific"), (o = Ah(s, e)));
        }
        const r = jt(Math.abs(o));
        const a = isNaN(r) ? 1 : Math.max(Math.min(-1 * Math.floor(r), 20), 0);
        const l = {
            notation: n,
            minimumFractionDigits: a,
            maximumFractionDigits: a,
        };
        return (Object.assign(l, this.options.ticks.format), Ue(s, i, l));
    },
    logarithmic(s, t, e) {
        if (s === 0) return "0";
        const i = e[t].significand || s / Math.pow(10, Math.floor(jt(s)));
        return [1, 2, 3, 5, 10, 15].includes(i) || t > 0.8 * e.length
            ? ia.numeric.call(this, s, t, e)
            : "";
    },
};
function Ah(s, t) {
    let e = t.length > 3 ? t[2].value - t[1].value : t[1].value - t[0].value;
    return (
        Math.abs(e) >= 1 && s !== Math.floor(s) && (e = s - Math.floor(s)),
        e
    );
}
const Os = { formatters: ia };
function Lh(s) {
    (s.set("scale", {
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
            callback: Os.formatters.values,
            minor: {},
            major: {},
            align: "center",
            crossAlign: "near",
            showLabelBackdrop: !1,
            backdropColor: "rgba(255, 255, 255, 0.75)",
            backdropPadding: 2,
        },
    }),
        s.route("scale.ticks", "color", "", "color"),
        s.route("scale.grid", "color", "", "borderColor"),
        s.route("scale.border", "color", "", "borderColor"),
        s.route("scale.title", "color", "", "color"),
        s.describe("scale", {
            _fallback: !1,
            _scriptable: (t) =>
                !t.startsWith("before") &&
                !t.startsWith("after") &&
                t !== "callback" &&
                t !== "parser",
            _indexable: (t) =>
                t !== "borderDash" && t !== "tickBorderDash" && t !== "dash",
        }),
        s.describe("scales", { _fallback: "scale" }),
        s.describe("scale.ticks", {
            _scriptable: (t) => t !== "backdropPadding" && t !== "callback",
            _indexable: (t) => t !== "backdropPadding",
        }));
}
const ne = Object.create(null);
const Vi = Object.create(null);
function Ms(s, t) {
    if (!t) return s;
    const e = t.split(".");
    for (let i = 0, n = e.length; i < n; ++i) {
        const o = e[i];
        s = s[o] || (s[o] = Object.create(null));
    }
    return s;
}
function Ln(s, t, e) {
    return typeof t === "string" ? ze(Ms(s, t), e) : ze(Ms(s, ""), t);
}
const Pn = class {
    constructor(t, e) {
        ((this.animation = void 0),
            (this.backgroundColor = "rgba(0,0,0,0.1)"),
            (this.borderColor = "rgba(0,0,0,0.1)"),
            (this.color = "#666"),
            (this.datasets = {}),
            (this.devicePixelRatio = (i) =>
                i.chart.platform.getDevicePixelRatio()),
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
            (this.hoverBackgroundColor = (i, n) => An(n.backgroundColor)),
            (this.hoverBorderColor = (i, n) => An(n.borderColor)),
            (this.hoverColor = (i, n) => An(n.color)),
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
        return Ln(this, t, e);
    }

    get(t) {
        return Ms(this, t);
    }

    describe(t, e) {
        return Ln(Vi, t, e);
    }

    override(t, e) {
        return Ln(ne, t, e);
    }

    route(t, e, i, n) {
        const o = Ms(this, t);
        const r = Ms(this, i);
        const a = "_" + e;
        Object.defineProperties(o, {
            [a]: { value: o[e], writable: !0 },
            [e]: {
                enumerable: !0,
                get() {
                    const l = this[a];
                    const c = r[n];
                    return L(l) ? Object.assign({}, c, l) : I(l, c);
                },
                set(l) {
                    this[a] = l;
                },
            },
        });
    }

    apply(t) {
        t.forEach((e) => e(this));
    }
};
const U = new Pn(
    {
        _scriptable: (s) => !s.startsWith("on"),
        _indexable: (s) => s !== "events",
        hover: { _fallback: "interaction" },
        interaction: { _scriptable: !1, _indexable: !1 },
    },
    [Ih, Ch, Lh],
);
function Ph(s) {
    return !s || A(s.size) || A(s.family)
        ? null
        : (s.style ? s.style + " " : "") +
              (s.weight ? s.weight + " " : "") +
              s.size +
              "px " +
              s.family;
}
function vs(s, t, e, i, n) {
    let o = t[n];
    return (
        o || ((o = t[n] = s.measureText(n).width), e.push(n)),
        o > i && (i = o),
        i
    );
}
function na(s, t, e, i) {
    i = i || {};
    let n = (i.data = i.data || {});
    let o = (i.garbageCollect = i.garbageCollect || []);
    (i.font !== t &&
        ((n = i.data = {}), (o = i.garbageCollect = []), (i.font = t)),
        s.save(),
        (s.font = t));
    let r = 0;
    const a = e.length;
    let l;
    let c;
    let h;
    let u;
    let d;
    for (l = 0; l < a; l++) {
        if (((u = e[l]), u != null && !$(u))) r = vs(s, n, o, r, u);
        else if ($(u)) {
            for (c = 0, h = u.length; c < h; c++) {
                ((d = u[c]), d != null && !$(d) && (r = vs(s, n, o, r, d)));
            }
        }
    }
    s.restore();
    const f = o.length / 2;
    if (f > e.length) {
        for (l = 0; l < f; l++) delete n[o[l]];
        o.splice(0, f);
    }
    return r;
}
function oe(s, t, e) {
    const i = s.currentDevicePixelRatio;
    const n = e !== 0 ? Math.max(e / 2, 0.5) : 0;
    return Math.round((t - n) * i) / i + n;
}
function Xn(s, t) {
    (!t && !s) ||
        ((t = t || s.getContext("2d")),
        t.save(),
        t.resetTransform(),
        t.clearRect(0, 0, s.width, s.height),
        t.restore());
}
function Hi(s, t, e, i) {
    Kn(s, t, e, i, null);
}
function Kn(s, t, e, i, n) {
    let o;
    let r;
    let a;
    let l;
    let c;
    let h;
    let u;
    let d;
    const f = t.pointStyle;
    const m = t.rotation;
    const g = t.radius;
    let p = (m || 0) * vh;
    if (
        f &&
        typeof f === "object" &&
        ((o = f.toString()),
        o === "[object HTMLImageElement]" || o === "[object HTMLCanvasElement]")
    ) {
        (s.save(),
            s.translate(e, i),
            s.rotate(p),
            s.drawImage(f, -f.width / 2, -f.height / 2, f.width, f.height),
            s.restore());
        return;
    }
    if (!(isNaN(g) || g <= 0)) {
        switch ((s.beginPath(), f)) {
            default:
                (n ? s.ellipse(e, i, n / 2, g, 0, 0, j) : s.arc(e, i, g, 0, j),
                    s.closePath());
                break;
            case "triangle":
                ((h = n ? n / 2 : g),
                    s.moveTo(e + Math.sin(p) * h, i - Math.cos(p) * g),
                    (p += Pr),
                    s.lineTo(e + Math.sin(p) * h, i - Math.cos(p) * g),
                    (p += Pr),
                    s.lineTo(e + Math.sin(p) * h, i - Math.cos(p) * g),
                    s.closePath());
                break;
            case "rectRounded":
                ((c = g * 0.516),
                    (l = g - c),
                    (r = Math.cos(p + be) * l),
                    (u = Math.cos(p + be) * (n ? n / 2 - c : l)),
                    (a = Math.sin(p + be) * l),
                    (d = Math.sin(p + be) * (n ? n / 2 - c : l)),
                    s.arc(e - u, i - a, c, p - N, p - X),
                    s.arc(e + d, i - r, c, p - X, p),
                    s.arc(e + u, i + a, c, p, p + X),
                    s.arc(e - d, i + r, c, p + X, p + N),
                    s.closePath());
                break;
            case "rect":
                if (!m) {
                    ((l = Math.SQRT1_2 * g),
                        (h = n ? n / 2 : l),
                        s.rect(e - h, i - l, 2 * h, 2 * l));
                    break;
                }
                p += be;
            case "rectRot":
                ((u = Math.cos(p) * (n ? n / 2 : g)),
                    (r = Math.cos(p) * g),
                    (a = Math.sin(p) * g),
                    (d = Math.sin(p) * (n ? n / 2 : g)),
                    s.moveTo(e - u, i - a),
                    s.lineTo(e + d, i - r),
                    s.lineTo(e + u, i + a),
                    s.lineTo(e - d, i + r),
                    s.closePath());
                break;
            case "crossRot":
                p += be;
            case "cross":
                ((u = Math.cos(p) * (n ? n / 2 : g)),
                    (r = Math.cos(p) * g),
                    (a = Math.sin(p) * g),
                    (d = Math.sin(p) * (n ? n / 2 : g)),
                    s.moveTo(e - u, i - a),
                    s.lineTo(e + u, i + a),
                    s.moveTo(e + d, i - r),
                    s.lineTo(e - d, i + r));
                break;
            case "star":
                ((u = Math.cos(p) * (n ? n / 2 : g)),
                    (r = Math.cos(p) * g),
                    (a = Math.sin(p) * g),
                    (d = Math.sin(p) * (n ? n / 2 : g)),
                    s.moveTo(e - u, i - a),
                    s.lineTo(e + u, i + a),
                    s.moveTo(e + d, i - r),
                    s.lineTo(e - d, i + r),
                    (p += be),
                    (u = Math.cos(p) * (n ? n / 2 : g)),
                    (r = Math.cos(p) * g),
                    (a = Math.sin(p) * g),
                    (d = Math.sin(p) * (n ? n / 2 : g)),
                    s.moveTo(e - u, i - a),
                    s.lineTo(e + u, i + a),
                    s.moveTo(e + d, i - r),
                    s.lineTo(e - d, i + r));
                break;
            case "line":
                ((r = n ? n / 2 : Math.cos(p) * g),
                    (a = Math.sin(p) * g),
                    s.moveTo(e - r, i - a),
                    s.lineTo(e + r, i + a));
                break;
            case "dash":
                (s.moveTo(e, i),
                    s.lineTo(
                        e + Math.cos(p) * (n ? n / 2 : g),
                        i + Math.sin(p) * g,
                    ));
                break;
            case !1:
                s.closePath();
                break;
        }
        (s.fill(), t.borderWidth > 0 && s.stroke());
    }
}
function Pt(s, t, e) {
    return (
        (e = e || 0.5),
        !t ||
            (s &&
                s.x > t.left - e &&
                s.x < t.right + e &&
                s.y > t.top - e &&
                s.y < t.bottom + e)
    );
}
function Ds(s, t) {
    (s.save(),
        s.beginPath(),
        s.rect(t.left, t.top, t.right - t.left, t.bottom - t.top),
        s.clip());
}
function Es(s) {
    s.restore();
}
function oa(s, t, e, i, n) {
    if (!t) return s.lineTo(e.x, e.y);
    if (n === "middle") {
        const o = (t.x + e.x) / 2;
        (s.lineTo(o, t.y), s.lineTo(o, e.y));
    } else (n === "after") != !!i ? s.lineTo(t.x, e.y) : s.lineTo(e.x, t.y);
    s.lineTo(e.x, e.y);
}
function ra(s, t, e, i) {
    if (!t) return s.lineTo(e.x, e.y);
    s.bezierCurveTo(
        i ? t.cp1x : t.cp2x,
        i ? t.cp1y : t.cp2y,
        i ? e.cp2x : e.cp1x,
        i ? e.cp2y : e.cp1y,
        e.x,
        e.y,
    );
}
function Nh(s, t) {
    (t.translation && s.translate(t.translation[0], t.translation[1]),
        A(t.rotation) || s.rotate(t.rotation),
        t.color && (s.fillStyle = t.color),
        t.textAlign && (s.textAlign = t.textAlign),
        t.textBaseline && (s.textBaseline = t.textBaseline));
}
function Rh(s, t, e, i, n) {
    if (n.strikethrough || n.underline) {
        const o = s.measureText(i);
        const r = t - o.actualBoundingBoxLeft;
        const a = t + o.actualBoundingBoxRight;
        const l = e - o.actualBoundingBoxAscent;
        const c = e + o.actualBoundingBoxDescent;
        const h = n.strikethrough ? (l + c) / 2 : c;
        ((s.strokeStyle = s.fillStyle),
            s.beginPath(),
            (s.lineWidth = n.decorationWidth || 2),
            s.moveTo(r, h),
            s.lineTo(a, h),
            s.stroke());
    }
}
function Wh(s, t) {
    const e = s.fillStyle;
    ((s.fillStyle = t.color),
        s.fillRect(t.left, t.top, t.width, t.height),
        (s.fillStyle = e));
}
function re(s, t, e, i, n, o = {}) {
    const r = $(t) ? t : [t];
    const a = o.strokeWidth > 0 && o.strokeColor !== "";
    let l;
    let c;
    for (s.save(), s.font = n.string, Nh(s, o), l = 0; l < r.length; ++l) {
        ((c = r[l]),
            o.backdrop && Wh(s, o.backdrop),
            a &&
                (o.strokeColor && (s.strokeStyle = o.strokeColor),
                A(o.strokeWidth) || (s.lineWidth = o.strokeWidth),
                s.strokeText(c, e, i, o.maxWidth)),
            s.fillText(c, e, i, o.maxWidth),
            Rh(s, e, i, c, o),
            (i += Number(n.lineHeight)));
    }
    s.restore();
}
function Ye(s, t) {
    const { x: e, y: i, w: n, h: o, radius: r } = t;
    (s.arc(e + r.topLeft, i + r.topLeft, r.topLeft, 1.5 * N, N, !0),
        s.lineTo(e, i + o - r.bottomLeft),
        s.arc(e + r.bottomLeft, i + o - r.bottomLeft, r.bottomLeft, N, X, !0),
        s.lineTo(e + n - r.bottomRight, i + o),
        s.arc(
            e + n - r.bottomRight,
            i + o - r.bottomRight,
            r.bottomRight,
            X,
            0,
            !0,
        ),
        s.lineTo(e + n, i + r.topRight),
        s.arc(e + n - r.topRight, i + r.topRight, r.topRight, 0, -X, !0),
        s.lineTo(e + r.topLeft, i));
}
const zh = /^(normal|(\d+(?:\.\d+)?)(px|em|%)?)$/;
const Vh =
    /^(normal|italic|initial|inherit|unset|(oblique( -?[0-9]?[0-9]deg)?))$/;
function Hh(s, t) {
    const e = ("" + s).match(zh);
    if (!e || e[1] === "normal") return t * 1.2;
    switch (((s = +e[2]), e[3])) {
        case "px":
            return s;
        case "%":
            s /= 100;
            break;
    }
    return t * s;
}
const Bh = (s) => +s || 0;
function Bi(s, t) {
    const e = {};
    const i = L(t);
    const n = i ? Object.keys(t) : t;
    const o = L(s) ? (i ? (r) => I(s[r], s[t[r]]) : (r) => s[r]) : () => s;
    for (const r of n) e[r] = Bh(o(r));
    return e;
}
function Jn(s) {
    return Bi(s, { top: "y", right: "x", bottom: "y", left: "x" });
}
function ae(s) {
    return Bi(s, ["topLeft", "topRight", "bottomLeft", "bottomRight"]);
}
function at(s) {
    const t = Jn(s);
    return ((t.width = t.left + t.right), (t.height = t.top + t.bottom), t);
}
function Q(s, t) {
    ((s = s || {}), (t = t || U.font));
    let e = I(s.size, t.size);
    typeof e === "string" && (e = parseInt(e, 10));
    let i = I(s.style, t.style);
    i &&
        !("" + i).match(Vh) &&
        (console.warn('Invalid font style specified: "' + i + '"'),
        (i = void 0));
    const n = {
        family: I(s.family, t.family),
        lineHeight: Hh(I(s.lineHeight, t.lineHeight), e),
        size: e,
        style: i,
        weight: I(s.weight, t.weight),
        string: "",
    };
    return ((n.string = Ph(n)), n);
}
function Ze(s, t, e, i) {
    let n = !0;
    let o;
    let r;
    let a;
    for (o = 0, r = s.length; o < r; ++o) {
        if (
            ((a = s[o]),
            a !== void 0 &&
                (t !== void 0 &&
                    typeof a === "function" &&
                    ((a = a(t)), (n = !1)),
                e !== void 0 && $(a) && ((a = a[e % a.length]), (n = !1)),
                a !== void 0))
        ) {
            return (i && !n && (i.cacheable = !1), a);
        }
    }
}
function aa(s, t, e) {
    const { min: i, max: n } = s;
    const o = Nn(t, (n - i) / 2);
    const r = (a, l) => (e && a === 0 ? 0 : a + l);
    return { min: r(i, -Math.abs(o)), max: r(n, o) };
}
function Yt(s, t) {
    return Object.assign(Object.create(s), t);
}
function $i(s, t = [""], e, i, n = () => s[0]) {
    const o = e || s;
    typeof i > "u" && (i = ha("_fallback", s));
    const r = {
        [Symbol.toStringTag]: "Object",
        _cacheable: !0,
        _scopes: s,
        _rootScopes: o,
        _fallback: i,
        _getTarget: n,
        override: (a) => $i([a, ...s], t, o, i),
    };
    return new Proxy(r, {
        deleteProperty(a, l) {
            return (delete a[l], delete a._keys, delete s[0][l], !0);
        },
        get(a, l) {
            return la(a, l, () => Xh(l, t, s, a));
        },
        getOwnPropertyDescriptor(a, l) {
            return Reflect.getOwnPropertyDescriptor(a._scopes[0], l);
        },
        getPrototypeOf() {
            return Reflect.getPrototypeOf(s[0]);
        },
        has(a, l) {
            return Vr(a).includes(l);
        },
        ownKeys(a) {
            return Vr(a);
        },
        set(a, l, c) {
            const h = a._storage || (a._storage = n());
            return ((a[l] = h[l] = c), delete a._keys, !0);
        },
    });
}
function _e(s, t, e, i) {
    const n = {
        _cacheable: !1,
        _proxy: s,
        _context: t,
        _subProxy: e,
        _stack: new Set(),
        _descriptors: Qn(s, i),
        setContext: (o) => _e(s, o, e, i),
        override: (o) => _e(s.override(o), t, e, i),
    };
    return new Proxy(n, {
        deleteProperty(o, r) {
            return (delete o[r], delete s[r], !0);
        },
        get(o, r, a) {
            return la(o, r, () => jh(o, r, a));
        },
        getOwnPropertyDescriptor(o, r) {
            return o._descriptors.allKeys
                ? Reflect.has(s, r)
                    ? { enumerable: !0, configurable: !0 }
                    : void 0
                : Reflect.getOwnPropertyDescriptor(s, r);
        },
        getPrototypeOf() {
            return Reflect.getPrototypeOf(s);
        },
        has(o, r) {
            return Reflect.has(s, r);
        },
        ownKeys() {
            return Reflect.ownKeys(s);
        },
        set(o, r, a) {
            return ((s[r] = a), delete o[r], !0);
        },
    });
}
function Qn(s, t = { scriptable: !0, indexable: !0 }) {
    const {
        _scriptable: e = t.scriptable,
        _indexable: i = t.indexable,
        _allKeys: n = t.allKeys,
    } = s;
    return {
        allKeys: n,
        scriptable: e,
        indexable: i,
        isScriptable: $t(e) ? e : () => e,
        isIndexable: $t(i) ? i : () => i,
    };
}
const $h = (s, t) => (s ? s + Ni(t) : t);
const to = (s, t) =>
    L(t) &&
    s !== "adapters" &&
    (Object.getPrototypeOf(t) === null || t.constructor === Object);
function la(s, t, e) {
    if (Object.prototype.hasOwnProperty.call(s, t) || t === "constructor") {
        return s[t];
    }
    const i = e();
    return ((s[t] = i), i);
}
function jh(s, t, e) {
    const { _proxy: i, _context: n, _subProxy: o, _descriptors: r } = s;
    let a = i[t];
    return (
        $t(a) && r.isScriptable(t) && (a = Uh(t, a, s, e)),
        $(a) && a.length && (a = Yh(t, a, s, r.isIndexable)),
        to(t, a) && (a = _e(a, n, o && o[t], r)),
        a
    );
}
function Uh(s, t, e, i) {
    const { _proxy: n, _context: o, _subProxy: r, _stack: a } = e;
    if (a.has(s)) {
        throw new Error(
            "Recursion detected: " + Array.from(a).join("->") + "->" + s,
        );
    }
    a.add(s);
    let l = t(o, r || i);
    return (a.delete(s), to(s, l) && (l = eo(n._scopes, n, s, l)), l);
}
function Yh(s, t, e, i) {
    const { _proxy: n, _context: o, _subProxy: r, _descriptors: a } = e;
    if (typeof o.index < "u" && i(s)) return t[o.index % t.length];
    if (L(t[0])) {
        const l = t;
        const c = n._scopes.filter((h) => h !== l);
        t = [];
        for (const h of l) {
            const u = eo(c, n, s, h);
            t.push(_e(u, o, r && r[s], a));
        }
    }
    return t;
}
function ca(s, t, e) {
    return $t(s) ? s(t, e) : s;
}
const Zh = (s, t) => (s === !0 ? t : typeof s === "string" ? Ut(t, s) : void 0);
function qh(s, t, e, i, n) {
    for (const o of t) {
        const r = Zh(e, o);
        if (r) {
            s.add(r);
            const a = ca(r._fallback, e, n);
            if (typeof a < "u" && a !== e && a !== i) return a;
        } else if (r === !1 && typeof i < "u" && e !== i) return null;
    }
    return !1;
}
function eo(s, t, e, i) {
    const n = t._rootScopes;
    const o = ca(t._fallback, e, i);
    const r = [...s, ...n];
    const a = new Set();
    a.add(i);
    let l = zr(a, r, e, o || e, i);
    return l === null ||
        (typeof o < "u" && o !== e && ((l = zr(a, r, o, l, i)), l === null))
        ? !1
        : $i(Array.from(a), [""], n, o, () => Gh(t, e, i));
}
function zr(s, t, e, i, n) {
    for (; e; ) e = qh(s, t, e, i, n);
    return e;
}
function Gh(s, t, e) {
    const i = s._getTarget();
    t in i || (i[t] = {});
    const n = i[t];
    return $(n) && L(e) ? e : n || {};
}
function Xh(s, t, e, i) {
    let n;
    for (const o of t) {
        if (((n = ha($h(o, s), e)), typeof n < "u")) {
            return to(s, n) ? eo(e, i, s, n) : n;
        }
    }
}
function ha(s, t) {
    for (const e of t) {
        if (!e) continue;
        const i = e[s];
        if (typeof i < "u") return i;
    }
}
function Vr(s) {
    let t = s._keys;
    return (t || (t = s._keys = Kh(s._scopes)), t);
}
function Kh(s) {
    const t = new Set();
    for (const e of s) {
        for (const i of Object.keys(e).filter((n) => !n.startsWith("_"))) {
            t.add(i);
        }
    }
    return Array.from(t);
}
function so(s, t, e, i) {
    const { iScale: n } = s;
    const { key: o = "r" } = this._parsing;
    const r = new Array(i);
    let a;
    let l;
    let c;
    let h;
    for (a = 0, l = i; a < l; ++a) {
        ((c = a + e), (h = t[c]), (r[a] = { r: n.parse(Ut(h, o), c) }));
    }
    return r;
}
const Jh = Number.EPSILON || 1e-14;
const Ve = (s, t) => t < s.length && !s[t].skip && s[t];
const ua = (s) => (s === "x" ? "y" : "x");
function Qh(s, t, e, i) {
    const n = s.skip ? t : s;
    const o = t;
    const r = e.skip ? t : e;
    const a = Li(o, n);
    const l = Li(r, o);
    let c = a / (a + l);
    let h = l / (a + l);
    ((c = isNaN(c) ? 0 : c), (h = isNaN(h) ? 0 : h));
    const u = i * c;
    const d = i * h;
    return {
        previous: { x: o.x - u * (r.x - n.x), y: o.y - u * (r.y - n.y) },
        next: { x: o.x + d * (r.x - n.x), y: o.y + d * (r.y - n.y) },
    };
}
function tu(s, t, e) {
    const i = s.length;
    let n;
    let o;
    let r;
    let a;
    let l;
    let c = Ve(s, 0);
    for (let h = 0; h < i - 1; ++h) {
        if (((l = c), (c = Ve(s, h + 1)), !(!l || !c))) {
            if ($e(t[h], 0, Jh)) {
                e[h] = e[h + 1] = 0;
                continue;
            }
            ((n = e[h] / t[h]),
                (o = e[h + 1] / t[h]),
                (a = Math.pow(n, 2) + Math.pow(o, 2)),
                !(a <= 9) &&
                    ((r = 3 / Math.sqrt(a)),
                    (e[h] = n * r * t[h]),
                    (e[h + 1] = o * r * t[h])));
        }
    }
}
function eu(s, t, e = "x") {
    const i = ua(e);
    const n = s.length;
    let o;
    let r;
    let a;
    let l = Ve(s, 0);
    for (let c = 0; c < n; ++c) {
        if (((r = a), (a = l), (l = Ve(s, c + 1)), !a)) continue;
        const h = a[e];
        const u = a[i];
        (r &&
            ((o = (h - r[e]) / 3),
            (a[`cp1${e}`] = h - o),
            (a[`cp1${i}`] = u - o * t[c])),
            l &&
                ((o = (l[e] - h) / 3),
                (a[`cp2${e}`] = h + o),
                (a[`cp2${i}`] = u + o * t[c])));
    }
}
function su(s, t = "x") {
    const e = ua(t);
    const i = s.length;
    const n = Array(i).fill(0);
    const o = Array(i);
    let r;
    let a;
    let l;
    let c = Ve(s, 0);
    for (r = 0; r < i; ++r) {
        if (((a = l), (l = c), (c = Ve(s, r + 1)), !!l)) {
            if (c) {
                const h = c[t] - l[t];
                n[r] = h !== 0 ? (c[e] - l[e]) / h : 0;
            }
            o[r] = a
                ? c
                    ? Ot(n[r - 1]) !== Ot(n[r])
                        ? 0
                        : (n[r - 1] + n[r]) / 2
                    : n[r - 1]
                : n[r];
        }
    }
    (tu(s, n, o), eu(s, o, t));
}
function Ei(s, t, e) {
    return Math.max(Math.min(s, e), t);
}
function iu(s, t) {
    let e;
    let i;
    let n;
    let o;
    let r;
    let a = Pt(s[0], t);
    for (e = 0, i = s.length; e < i; ++e) {
        ((r = o),
            (o = a),
            (a = e < i - 1 && Pt(s[e + 1], t)),
            o &&
                ((n = s[e]),
                r &&
                    ((n.cp1x = Ei(n.cp1x, t.left, t.right)),
                    (n.cp1y = Ei(n.cp1y, t.top, t.bottom))),
                a &&
                    ((n.cp2x = Ei(n.cp2x, t.left, t.right)),
                    (n.cp2y = Ei(n.cp2y, t.top, t.bottom)))));
    }
}
function da(s, t, e, i, n) {
    let o, r, a, l;
    if (
        (t.spanGaps && (s = s.filter((c) => !c.skip)),
        t.cubicInterpolationMode === "monotone")
    ) {
        su(s, n);
    } else {
        let c = i ? s[s.length - 1] : s[0];
        for (o = 0, r = s.length; o < r; ++o) {
            ((a = s[o]),
                (l = Qh(
                    c,
                    a,
                    s[Math.min(o + 1, r - (i ? 0 : 1)) % r],
                    t.tension,
                )),
                (a.cp1x = l.previous.x),
                (a.cp1y = l.previous.y),
                (a.cp2x = l.next.x),
                (a.cp2y = l.next.y),
                (c = a));
        }
    }
    t.capBezierPoints && iu(s, e);
}
function ji() {
    return typeof window < "u" && typeof document < "u";
}
function Ui(s) {
    let t = s.parentNode;
    return (t && t.toString() === "[object ShadowRoot]" && (t = t.host), t);
}
function Pi(s, t, e) {
    let i;
    return (
        typeof s === "string"
            ? ((i = parseInt(s, 10)),
              s.indexOf("%") !== -1 && (i = (i / 100) * t.parentNode[e]))
            : (i = s),
        i
    );
}
const Yi = (s) => s.ownerDocument.defaultView.getComputedStyle(s, null);
function nu(s, t) {
    return Yi(s).getPropertyValue(t);
}
const ou = ["top", "right", "bottom", "left"];
function xe(s, t, e) {
    const i = {};
    e = e ? "-" + e : "";
    for (let n = 0; n < 4; n++) {
        const o = ou[n];
        i[o] = parseFloat(s[t + "-" + o + e]) || 0;
    }
    return ((i.width = i.left + i.right), (i.height = i.top + i.bottom), i);
}
const ru = (s, t, e) => (s > 0 || t > 0) && (!e || !e.shadowRoot);
function au(s, t) {
    const e = s.touches;
    const i = e && e.length ? e[0] : s;
    const { offsetX: n, offsetY: o } = i;
    let r = !1;
    let a;
    let l;
    if (ru(n, o, s.target)) ((a = n), (l = o));
    else {
        const c = t.getBoundingClientRect();
        ((a = i.clientX - c.left), (l = i.clientY - c.top), (r = !0));
    }
    return { x: a, y: l, box: r };
}
function le(s, t) {
    if ("native" in s) return s;
    const { canvas: e, currentDevicePixelRatio: i } = t;
    const n = Yi(e);
    const o = n.boxSizing === "border-box";
    const r = xe(n, "padding");
    const a = xe(n, "border", "width");
    const { x: l, y: c, box: h } = au(s, e);
    const u = r.left + (h && a.left);
    const d = r.top + (h && a.top);
    let { width: f, height: m } = t;
    return (
        o && ((f -= r.width + a.width), (m -= r.height + a.height)),
        {
            x: Math.round((((l - u) / f) * e.width) / i),
            y: Math.round((((c - d) / m) * e.height) / i),
        }
    );
}
function lu(s, t, e) {
    let i, n;
    if (t === void 0 || e === void 0) {
        const o = s && Ui(s);
        if (!o) ((t = s.clientWidth), (e = s.clientHeight));
        else {
            const r = o.getBoundingClientRect();
            const a = Yi(o);
            const l = xe(a, "border", "width");
            const c = xe(a, "padding");
            ((t = r.width - c.width - l.width),
                (e = r.height - c.height - l.height),
                (i = Pi(a.maxWidth, o, "clientWidth")),
                (n = Pi(a.maxHeight, o, "clientHeight")));
        }
    }
    return { width: t, height: e, maxWidth: i || Ai, maxHeight: n || Ai };
}
const Ii = (s) => Math.round(s * 10) / 10;
function fa(s, t, e, i) {
    const n = Yi(s);
    const o = xe(n, "margin");
    const r = Pi(n.maxWidth, s, "clientWidth") || Ai;
    const a = Pi(n.maxHeight, s, "clientHeight") || Ai;
    const l = lu(s, t, e);
    let { width: c, height: h } = l;
    if (n.boxSizing === "content-box") {
        const d = xe(n, "border", "width");
        const f = xe(n, "padding");
        ((c -= f.width + d.width), (h -= f.height + d.height));
    }
    return (
        (c = Math.max(0, c - o.width)),
        (h = Math.max(0, i ? c / i : h - o.height)),
        (c = Ii(Math.min(c, r, l.maxWidth))),
        (h = Ii(Math.min(h, a, l.maxHeight))),
        c && !h && (h = Ii(c / 2)),
        (t !== void 0 || e !== void 0) &&
            i &&
            l.height &&
            h > l.height &&
            ((h = l.height), (c = Ii(Math.floor(h * i)))),
        { width: c, height: h }
    );
}
function io(s, t, e) {
    const i = t || 1;
    const n = Math.floor(s.height * i);
    const o = Math.floor(s.width * i);
    ((s.height = Math.floor(s.height)), (s.width = Math.floor(s.width)));
    const r = s.canvas;
    return (
        r.style &&
            (e || (!r.style.height && !r.style.width)) &&
            ((r.style.height = `${s.height}px`),
            (r.style.width = `${s.width}px`)),
        s.currentDevicePixelRatio !== i || r.height !== n || r.width !== o
            ? ((s.currentDevicePixelRatio = i),
              (r.height = n),
              (r.width = o),
              s.ctx.setTransform(i, 0, 0, i, 0, 0),
              !0)
            : !1
    );
}
const ma = (function () {
    let s = !1;
    try {
        const t = {
            get passive() {
                return ((s = !0), !1);
            },
        };
        ji() &&
            (window.addEventListener("test", null, t),
            window.removeEventListener("test", null, t));
    } catch {}
    return s;
})();
function no(s, t) {
    const e = nu(s, t);
    const i = e && e.match(/^(\d+)(\.\d+)?px$/);
    return i ? +i[1] : void 0;
}
function ie(s, t, e, i) {
    return { x: s.x + e * (t.x - s.x), y: s.y + e * (t.y - s.y) };
}
function ga(s, t, e, i) {
    return {
        x: s.x + e * (t.x - s.x),
        y:
            i === "middle"
                ? e < 0.5
                    ? s.y
                    : t.y
                : i === "after"
                  ? e < 1
                      ? s.y
                      : t.y
                  : e > 0
                    ? t.y
                    : s.y,
    };
}
function pa(s, t, e, i) {
    const n = { x: s.cp2x, y: s.cp2y };
    const o = { x: t.cp1x, y: t.cp1y };
    const r = ie(s, n, e);
    const a = ie(n, o, e);
    const l = ie(o, t, e);
    const c = ie(r, a, e);
    const h = ie(a, l, e);
    return ie(c, h, e);
}
const cu = function (s, t) {
    return {
        x(e) {
            return s + s + t - e;
        },
        setWidth(e) {
            t = e;
        },
        textAlign(e) {
            return e === "center" ? e : e === "right" ? "left" : "right";
        },
        xPlus(e, i) {
            return e - i;
        },
        leftForLtr(e, i) {
            return e - i;
        },
    };
};
const hu = function () {
    return {
        x(s) {
            return s;
        },
        setWidth(s) {},
        textAlign(s) {
            return s;
        },
        xPlus(s, t) {
            return s + t;
        },
        leftForLtr(s, t) {
            return s;
        },
    };
};
function Se(s, t, e) {
    return s ? cu(t, e) : hu();
}
function oo(s, t) {
    let e, i;
    (t === "ltr" || t === "rtl") &&
        ((e = s.canvas.style),
        (i = [
            e.getPropertyValue("direction"),
            e.getPropertyPriority("direction"),
        ]),
        e.setProperty("direction", t, "important"),
        (s.prevTextDirection = i));
}
function ro(s, t) {
    t !== void 0 &&
        (delete s.prevTextDirection,
        s.canvas.style.setProperty("direction", t[0], t[1]));
}
function ya(s) {
    return s === "angle"
        ? { between: je, compare: Oh, normalize: ot }
        : { between: Rt, compare: (t, e) => t - e, normalize: (t) => t };
}
function Hr({ start: s, end: t, count: e, loop: i, style: n }) {
    return {
        start: s % e,
        end: t % e,
        loop: i && (t - s + 1) % e === 0,
        style: n,
    };
}
function uu(s, t, e) {
    const { property: i, start: n, end: o } = e;
    const { between: r, normalize: a } = ya(i);
    const l = t.length;
    let { start: c, end: h, loop: u } = s;
    let d;
    let f;
    if (u) {
        for (
            c += l, h += l, d = 0, f = l;
            d < f && r(a(t[c % l][i]), n, o);
            ++d
        ) {
            (c--, h--);
        }
        ((c %= l), (h %= l));
    }
    return (h < c && (h += l), { start: c, end: h, loop: u, style: s.style });
}
function ao(s, t, e) {
    if (!e) return [s];
    const { property: i, start: n, end: o } = e;
    const r = t.length;
    const { compare: a, between: l, normalize: c } = ya(i);
    const { start: h, end: u, loop: d, style: f } = uu(s, t, e);
    const m = [];
    let g = !1;
    let p = null;
    let y;
    let b;
    let _;
    const w = () => l(n, _, y) && a(n, _) !== 0;
    const x = () => a(o, y) === 0 || l(o, _, y);
    const k = () => g || w();
    const M = () => !g || x();
    for (let v = h, O = h; v <= u; ++v) {
        ((b = t[v % r]),
            !b.skip &&
                ((y = c(b[i])),
                y !== _ &&
                    ((g = l(y, n, o)),
                    p === null && k() && (p = a(y, n) === 0 ? v : O),
                    p !== null &&
                        M() &&
                        (m.push(
                            Hr({
                                start: p,
                                end: v,
                                loop: d,
                                count: r,
                                style: f,
                            }),
                        ),
                        (p = null)),
                    (O = v),
                    (_ = y))));
    }
    return (
        p !== null &&
            m.push(Hr({ start: p, end: u, loop: d, count: r, style: f })),
        m
    );
}
function lo(s, t) {
    const e = [];
    const i = s.segments;
    for (let n = 0; n < i.length; n++) {
        const o = ao(i[n], s.points, t);
        o.length && e.push(...o);
    }
    return e;
}
function du(s, t, e, i) {
    let n = 0;
    let o = t - 1;
    if (e && !i) for (; n < t && !s[n].skip; ) n++;
    for (; n < t && s[n].skip; ) n++;
    for (n %= t, e && (o += n); o > n && s[o % t].skip; ) o--;
    return ((o %= t), { start: n, end: o });
}
function fu(s, t, e, i) {
    const n = s.length;
    const o = [];
    let r = t;
    let a = s[t];
    let l;
    for (l = t + 1; l <= e; ++l) {
        const c = s[l % n];
        (c.skip || c.stop
            ? a.skip ||
              ((i = !1),
              o.push({ start: t % n, end: (l - 1) % n, loop: i }),
              (t = r = c.stop ? l : null))
            : ((r = l), a.skip && (t = l)),
            (a = c));
    }
    return (r !== null && o.push({ start: t % n, end: r % n, loop: i }), o);
}
function ba(s, t) {
    const e = s.points;
    const i = s.options.spanGaps;
    const n = e.length;
    if (!n) return [];
    const o = !!s._loop;
    const { start: r, end: a } = du(e, n, o, i);
    if (i === !0) return Br(s, [{ start: r, end: a, loop: o }], e, t);
    const l = a < r ? a + n : a;
    const c = !!s._fullLoop && r === 0 && a === n - 1;
    return Br(s, fu(e, r, l, c), e, t);
}
function Br(s, t, e, i) {
    return !i || !i.setContext || !e ? t : mu(s, t, e, i);
}
function mu(s, t, e, i) {
    const n = s._chart.getContext();
    const o = $r(s.options);
    const {
        _datasetIndex: r,
        options: { spanGaps: a },
    } = s;
    const l = e.length;
    const c = [];
    let h = o;
    let u = t[0].start;
    let d = u;
    function f(m, g, p, y) {
        const b = a ? -1 : 1;
        if (m !== g) {
            for (m += l; e[m % l].skip; ) m -= b;
            for (; e[g % l].skip; ) g += b;
            m % l !== g % l &&
                (c.push({ start: m % l, end: g % l, loop: p, style: y }),
                (h = y),
                (u = g % l));
        }
    }
    for (const m of t) {
        u = a ? u : m.start;
        let g = e[u % l];
        let p;
        for (d = u + 1; d <= m.end; d++) {
            const y = e[d % l];
            ((p = $r(
                i.setContext(
                    Yt(n, {
                        type: "segment",
                        p0: g,
                        p1: y,
                        p0DataIndex: (d - 1) % l,
                        p1DataIndex: d % l,
                        datasetIndex: r,
                    }),
                ),
            )),
                gu(p, h) && f(u, d - 1, m.loop, h),
                (g = y),
                (h = p));
        }
        u < d - 1 && f(u, d - 1, m.loop, h);
    }
    return c;
}
function $r(s) {
    return {
        backgroundColor: s.backgroundColor,
        borderCapStyle: s.borderCapStyle,
        borderDash: s.borderDash,
        borderDashOffset: s.borderDashOffset,
        borderJoinStyle: s.borderJoinStyle,
        borderWidth: s.borderWidth,
        borderColor: s.borderColor,
    };
}
function gu(s, t) {
    if (!t) return !1;
    const e = [];
    const i = function (n, o) {
        return qn(o) ? (e.includes(o) || e.push(o), e.indexOf(o)) : o;
    };
    return JSON.stringify(s, i) !== JSON.stringify(t, i);
}
function Ci(s, t, e) {
    return s.options.clip ? s[e] : t[e];
}
function pu(s, t) {
    const { xScale: e, yScale: i } = s;
    return e && i
        ? {
              left: Ci(e, t, "left"),
              right: Ci(e, t, "right"),
              top: Ci(i, t, "top"),
              bottom: Ci(i, t, "bottom"),
          }
        : t;
}
function co(s, t) {
    const e = t._clip;
    if (e.disabled) return !1;
    const i = pu(t, s.chartArea);
    return {
        left: e.left === !1 ? 0 : i.left - (e.left === !0 ? 0 : e.left),
        right:
            e.right === !1 ? s.width : i.right + (e.right === !0 ? 0 : e.right),
        top: e.top === !1 ? 0 : i.top - (e.top === !0 ? 0 : e.top),
        bottom:
            e.bottom === !1
                ? s.height
                : i.bottom + (e.bottom === !0 ? 0 : e.bottom),
    };
}
const So = class {
    constructor() {
        ((this._request = null),
            (this._charts = new Map()),
            (this._running = !1),
            (this._lastDate = void 0));
    }

    _notify(t, e, i, n) {
        const o = e.listeners[n];
        const r = e.duration;
        o.forEach((a) =>
            a({
                chart: t,
                initial: e.initial,
                numSteps: r,
                currentStep: Math.min(i - e.start, r),
            }),
        );
    }

    _refresh() {
        this._request ||
            ((this._running = !0),
            (this._request = jn.call(window, () => {
                (this._update(),
                    (this._request = null),
                    this._running && this._refresh());
            })));
    }

    _update(t = Date.now()) {
        let e = 0;
        (this._charts.forEach((i, n) => {
            if (!i.running || !i.items.length) return;
            const o = i.items;
            let r = o.length - 1;
            let a = !1;
            let l;
            for (; r >= 0; --r) {
                ((l = o[r]),
                    l._active
                        ? (l._total > i.duration && (i.duration = l._total),
                          l.tick(t),
                          (a = !0))
                        : ((o[r] = o[o.length - 1]), o.pop()));
            }
            (a && (n.draw(), this._notify(n, i, t, "progress")),
                o.length ||
                    ((i.running = !1),
                    this._notify(n, i, t, "complete"),
                    (i.initial = !1)),
                (e += o.length));
        }),
            (this._lastDate = t),
            e === 0 && (this._running = !1));
    }

    _getAnims(t) {
        const e = this._charts;
        let i = e.get(t);
        return (
            i ||
                ((i = {
                    running: !1,
                    initial: !0,
                    items: [],
                    listeners: { complete: [], progress: [] },
                }),
                e.set(t, i)),
            i
        );
    }

    listen(t, e, i) {
        this._getAnims(t).listeners[e].push(i);
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
                (i, n) => Math.max(i, n._duration),
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
        const i = e.items;
        let n = i.length - 1;
        for (; n >= 0; --n) i[n].cancel();
        ((e.items = []), this._notify(t, e, Date.now(), "complete"));
    }

    remove(t) {
        return this._charts.delete(t);
    }
};
const Zt = new So();
const xa = "transparent";
const yu = {
    boolean(s, t, e) {
        return e > 0.5 ? t : s;
    },
    color(s, t, e) {
        const i = Gn(s || xa);
        const n = i.valid && Gn(t || xa);
        return n && n.valid ? n.mix(i, e).hexString() : t;
    },
    number(s, t, e) {
        return s + (t - s) * e;
    },
};
const ko = class {
    constructor(t, e, i, n) {
        const o = e[i];
        n = Ze([t.to, n, o, t.from]);
        const r = Ze([t.from, o, n]);
        ((this._active = !0),
            (this._fn = t.fn || yu[t.type || typeof r]),
            (this._easing = We[t.easing] || We.linear),
            (this._start = Math.floor(Date.now() + (t.delay || 0))),
            (this._duration = this._total = Math.floor(t.duration)),
            (this._loop = !!t.loop),
            (this._target = e),
            (this._prop = i),
            (this._from = r),
            (this._to = n),
            (this._promises = void 0));
    }

    active() {
        return this._active;
    }

    update(t, e, i) {
        if (this._active) {
            this._notify(!1);
            const n = this._target[this._prop];
            const o = i - this._start;
            const r = this._duration - o;
            ((this._start = i),
                (this._duration = Math.floor(Math.max(r, t.duration))),
                (this._total += o),
                (this._loop = !!t.loop),
                (this._to = Ze([t.to, e, n, t.from])),
                (this._from = Ze([t.from, n, e])));
        }
    }

    cancel() {
        this._active &&
            (this.tick(Date.now()), (this._active = !1), this._notify(!1));
    }

    tick(t) {
        const e = t - this._start;
        const i = this._duration;
        const n = this._prop;
        const o = this._from;
        const r = this._loop;
        const a = this._to;
        let l;
        if (((this._active = o !== a && (r || e < i)), !this._active)) {
            ((this._target[n] = a), this._notify(!0));
            return;
        }
        if (e < 0) {
            this._target[n] = o;
            return;
        }
        ((l = (e / i) % 2),
            (l = r && l > 1 ? 2 - l : l),
            (l = this._easing(Math.min(1, Math.max(0, l)))),
            (this._target[n] = this._fn(o, a, l)));
    }

    wait() {
        const t = this._promises || (this._promises = []);
        return new Promise((e, i) => {
            t.push({ res: e, rej: i });
        });
    }

    _notify(t) {
        const e = t ? "res" : "rej";
        const i = this._promises || [];
        for (let n = 0; n < i.length; n++) i[n][e]();
    }
};
const en = class {
    constructor(t, e) {
        ((this._chart = t), (this._properties = new Map()), this.configure(e));
    }

    configure(t) {
        if (!L(t)) return;
        const e = Object.keys(U.animation);
        const i = this._properties;
        Object.getOwnPropertyNames(t).forEach((n) => {
            const o = t[n];
            if (!L(o)) return;
            const r = {};
            for (const a of e) r[a] = o[a];
            (($(o.properties) && o.properties) || [n]).forEach((a) => {
                (a === n || !i.has(a)) && i.set(a, r);
            });
        });
    }

    _animateOptions(t, e) {
        const i = e.options;
        const n = xu(t, i);
        if (!n) return [];
        const o = this._createAnimations(n, i);
        return (
            i.$shared &&
                bu(t.options.$animations, i).then(
                    () => {
                        t.options = i;
                    },
                    () => {},
                ),
            o
        );
    }

    _createAnimations(t, e) {
        const i = this._properties;
        const n = [];
        const o = t.$animations || (t.$animations = {});
        const r = Object.keys(e);
        const a = Date.now();
        let l;
        for (l = r.length - 1; l >= 0; --l) {
            const c = r[l];
            if (c.charAt(0) === "$") continue;
            if (c === "options") {
                n.push(...this._animateOptions(t, e));
                continue;
            }
            const h = e[c];
            let u = o[c];
            const d = i.get(c);
            if (u) {
                if (d && u.active()) {
                    u.update(d, h, a);
                    continue;
                } else u.cancel();
            }
            if (!d || !d.duration) {
                t[c] = h;
                continue;
            }
            ((o[c] = u = new ko(d, t, c, h)), n.push(u));
        }
        return n;
    }

    update(t, e) {
        if (this._properties.size === 0) {
            Object.assign(t, e);
            return;
        }
        const i = this._createAnimations(t, e);
        if (i.length) return (Zt.add(this._chart, i), !0);
    }
};
function bu(s, t) {
    const e = [];
    const i = Object.keys(t);
    for (let n = 0; n < i.length; n++) {
        const o = s[i[n]];
        o && o.active() && e.push(o.wait());
    }
    return Promise.all(e);
}
function xu(s, t) {
    if (!t) return;
    let e = s.options;
    if (!e) {
        s.options = t;
        return;
    }
    return (
        e.$shared &&
            (s.options = e =
                Object.assign({}, e, { $shared: !1, $animations: {} })),
        e
    );
}
function _a(s, t) {
    const e = (s && s.options) || {};
    const i = e.reverse;
    const n = e.min === void 0 ? t : 0;
    const o = e.max === void 0 ? t : 0;
    return { start: i ? o : n, end: i ? n : o };
}
function _u(s, t, e) {
    if (e === !1) return !1;
    const i = _a(s, e);
    const n = _a(t, e);
    return { top: n.end, right: i.end, bottom: n.start, left: i.start };
}
function wu(s) {
    let t, e, i, n;
    return (
        L(s)
            ? ((t = s.top), (e = s.right), (i = s.bottom), (n = s.left))
            : (t = e = i = n = s),
        { top: t, right: e, bottom: i, left: n, disabled: s === !1 }
    );
}
function bl(s, t) {
    const e = [];
    const i = s._getSortedDatasetMetas(t);
    let n;
    let o;
    for (n = 0, o = i.length; n < o; ++n) e.push(i[n].index);
    return e;
}
function wa(s, t, e, i = {}) {
    const n = s.keys;
    const o = i.mode === "single";
    let r;
    let a;
    let l;
    let c;
    if (t === null) return;
    let h = !1;
    for (r = 0, a = n.length; r < a; ++r) {
        if (((l = +n[r]), l === e)) {
            if (((h = !0), i.all)) continue;
            break;
        }
        ((c = s.values[l]),
            Z(c) && (o || t === 0 || Ot(t) === Ot(c)) && (t += c));
    }
    return !h && !i.all ? 0 : t;
}
function Su(s, t) {
    const { iScale: e, vScale: i } = t;
    const n = e.axis === "x" ? "x" : "y";
    const o = i.axis === "x" ? "x" : "y";
    const r = Object.keys(s);
    const a = new Array(r.length);
    let l;
    let c;
    let h;
    for (l = 0, c = r.length; l < c; ++l) {
        ((h = r[l]), (a[l] = { [n]: h, [o]: s[h] }));
    }
    return a;
}
function ho(s, t) {
    const e = s && s.options.stacked;
    return e || (e === void 0 && t.stack !== void 0);
}
function ku(s, t, e) {
    return `${s.id}.${t.id}.${e.stack || e.type}`;
}
function Mu(s) {
    const { min: t, max: e, minDefined: i, maxDefined: n } = s.getUserBounds();
    return {
        min: i ? t : Number.NEGATIVE_INFINITY,
        max: n ? e : Number.POSITIVE_INFINITY,
    };
}
function vu(s, t, e) {
    const i = s[t] || (s[t] = {});
    return i[e] || (i[e] = {});
}
function Sa(s, t, e, i) {
    for (const n of t.getMatchingVisibleMetas(i).reverse()) {
        const o = s[n.index];
        if ((e && o > 0) || (!e && o < 0)) return n.index;
    }
    return null;
}
function ka(s, t) {
    const { chart: e, _cachedMeta: i } = s;
    const n = e._stacks || (e._stacks = {});
    const { iScale: o, vScale: r, index: a } = i;
    const l = o.axis;
    const c = r.axis;
    const h = ku(o, r, i);
    const u = t.length;
    let d;
    for (let f = 0; f < u; ++f) {
        const m = t[f];
        const { [l]: g, [c]: p } = m;
        const y = m._stacks || (m._stacks = {});
        ((d = y[c] = vu(n, h, g)),
            (d[a] = p),
            (d._top = Sa(d, r, !0, i.type)),
            (d._bottom = Sa(d, r, !1, i.type)));
        const b = d._visualValues || (d._visualValues = {});
        b[a] = p;
    }
}
function uo(s, t) {
    const e = s.scales;
    return Object.keys(e)
        .filter((i) => e[i].axis === t)
        .shift();
}
function Tu(s, t) {
    return Yt(s, {
        active: !1,
        dataset: void 0,
        datasetIndex: t,
        index: t,
        mode: "default",
        type: "dataset",
    });
}
function Ou(s, t, e) {
    return Yt(s, {
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
function Is(s, t) {
    const e = s.controller.index;
    const i = s.vScale && s.vScale.axis;
    if (i) {
        t = t || s._parsed;
        for (const n of t) {
            const o = n._stacks;
            if (!o || o[i] === void 0 || o[i][e] === void 0) return;
            (delete o[i][e],
                o[i]._visualValues !== void 0 &&
                    o[i]._visualValues[e] !== void 0 &&
                    delete o[i]._visualValues[e]);
        }
    }
}
const fo = (s) => s === "reset" || s === "none";
const Ma = (s, t) => (t ? s : Object.assign({}, s));
const Du = (s, t, e) =>
    s && !t.hidden && t._stacked && { keys: bl(e, !0), values: null };
const pt = class {
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
            (t._stacked = ho(t.vScale, t)),
            this.addElements(),
            this.options.fill &&
                !this.chart.isPluginEnabled("filler") &&
                console.warn(
                    "Tried to use the 'fill' option without the 'Filler' plugin enabled. Please import and register the 'Filler' plugin and make sure it is not disabled in the options",
                ));
    }

    updateIndex(t) {
        (this.index !== t && Is(this._cachedMeta), (this.index = t));
    }

    linkScales() {
        const t = this.chart;
        const e = this._cachedMeta;
        const i = this.getDataset();
        const n = (u, d, f, m) => (u === "x" ? d : u === "r" ? m : f);
        const o = (e.xAxisID = I(i.xAxisID, uo(t, "x")));
        const r = (e.yAxisID = I(i.yAxisID, uo(t, "y")));
        const a = (e.rAxisID = I(i.rAxisID, uo(t, "r")));
        const l = e.indexAxis;
        const c = (e.iAxisID = n(l, o, r, a));
        const h = (e.vAxisID = n(l, r, o, a));
        ((e.xScale = this.getScaleForId(o)),
            (e.yScale = this.getScaleForId(r)),
            (e.rScale = this.getScaleForId(a)),
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
        (this._data && Bn(this._data, this), t._stacked && Is(t));
    }

    _dataCheck() {
        const t = this.getDataset();
        const e = t.data || (t.data = []);
        const i = this._data;
        if (L(e)) {
            const n = this._cachedMeta;
            this._data = Su(e, n);
        } else if (i !== e) {
            if (i) {
                Bn(i, this);
                const n = this._cachedMeta;
                (Is(n), (n._parsed = []));
            }
            (e && Object.isExtensible(e) && ta(e, this),
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
        const i = this.getDataset();
        let n = !1;
        this._dataCheck();
        const o = e._stacked;
        ((e._stacked = ho(e.vScale, e)),
            e.stack !== i.stack && ((n = !0), Is(e), (e.stack = i.stack)),
            this._resyncElements(t),
            (n || o !== e._stacked) &&
                (ka(this, e._parsed), (e._stacked = ho(e.vScale, e))));
    }

    configure() {
        const t = this.chart.config;
        const e = t.datasetScopeKeys(this._type);
        const i = t.getOptionScopes(this.getDataset(), e, !0);
        ((this.options = t.createResolver(i, this.getContext())),
            (this._parsing = this.options.parsing),
            (this._cachedDataOpts = {}));
    }

    parse(t, e) {
        const { _cachedMeta: i, _data: n } = this;
        const { iScale: o, _stacked: r } = i;
        const a = o.axis;
        let l = t === 0 && e === n.length ? !0 : i._sorted;
        let c = t > 0 && i._parsed[t - 1];
        let h;
        let u;
        let d;
        if (this._parsing === !1) ((i._parsed = n), (i._sorted = !0), (d = n));
        else {
            $(n[t])
                ? (d = this.parseArrayData(i, n, t, e))
                : L(n[t])
                  ? (d = this.parseObjectData(i, n, t, e))
                  : (d = this.parsePrimitiveData(i, n, t, e));
            const f = () => u[a] === null || (c && u[a] < c[a]);
            for (h = 0; h < e; ++h) {
                ((i._parsed[h + t] = u = d[h]),
                    l && (f() && (l = !1), (c = u)));
            }
            i._sorted = l;
        }
        r && ka(this, d);
    }

    parsePrimitiveData(t, e, i, n) {
        const { iScale: o, vScale: r } = t;
        const a = o.axis;
        const l = r.axis;
        const c = o.getLabels();
        const h = o === r;
        const u = new Array(n);
        let d;
        let f;
        let m;
        for (d = 0, f = n; d < f; ++d) {
            ((m = d + i),
                (u[d] = { [a]: h || o.parse(c[m], m), [l]: r.parse(e[m], m) }));
        }
        return u;
    }

    parseArrayData(t, e, i, n) {
        const { xScale: o, yScale: r } = t;
        const a = new Array(n);
        let l;
        let c;
        let h;
        let u;
        for (l = 0, c = n; l < c; ++l) {
            ((h = l + i),
                (u = e[h]),
                (a[l] = { x: o.parse(u[0], h), y: r.parse(u[1], h) }));
        }
        return a;
    }

    parseObjectData(t, e, i, n) {
        const { xScale: o, yScale: r } = t;
        const { xAxisKey: a = "x", yAxisKey: l = "y" } = this._parsing;
        const c = new Array(n);
        let h;
        let u;
        let d;
        let f;
        for (h = 0, u = n; h < u; ++h) {
            ((d = h + i),
                (f = e[d]),
                (c[h] = { x: o.parse(Ut(f, a), d), y: r.parse(Ut(f, l), d) }));
        }
        return c;
    }

    getParsed(t) {
        return this._cachedMeta._parsed[t];
    }

    getDataElement(t) {
        return this._cachedMeta.data[t];
    }

    applyStack(t, e, i) {
        const n = this.chart;
        const o = this._cachedMeta;
        const r = e[t.axis];
        const a = { keys: bl(n, !0), values: e._stacks[t.axis]._visualValues };
        return wa(a, r, o.index, { mode: i });
    }

    updateRangeFromParsed(t, e, i, n) {
        const o = i[e.axis];
        let r = o === null ? NaN : o;
        const a = n && i._stacks[e.axis];
        (n && a && ((n.values = a), (r = wa(n, o, this._cachedMeta.index))),
            (t.min = Math.min(t.min, r)),
            (t.max = Math.max(t.max, r)));
    }

    getMinMax(t, e) {
        const i = this._cachedMeta;
        const n = i._parsed;
        const o = i._sorted && t === i.iScale;
        const r = n.length;
        const a = this._getOtherScale(t);
        const l = Du(e, i, this.chart);
        const c = {
            min: Number.POSITIVE_INFINITY,
            max: Number.NEGATIVE_INFINITY,
        };
        const { min: h, max: u } = Mu(a);
        let d;
        let f;
        function m() {
            f = n[d];
            const g = f[a.axis];
            return !Z(f[t.axis]) || h > g || u < g;
        }
        for (
            d = 0;
            d < r && !(!m() && (this.updateRangeFromParsed(c, t, f, l), o));
            ++d
        );
        if (o) {
            for (d = r - 1; d >= 0; --d) {
                if (!m()) {
                    this.updateRangeFromParsed(c, t, f, l);
                    break;
                }
            }
        }
        return c;
    }

    getAllParsedValues(t) {
        const e = this._cachedMeta._parsed;
        const i = [];
        let n;
        let o;
        let r;
        for (n = 0, o = e.length; n < o; ++n) {
            ((r = e[n][t.axis]), Z(r) && i.push(r));
        }
        return i;
    }

    getMaxOverflow() {
        return !1;
    }

    getLabelAndValue(t) {
        const e = this._cachedMeta;
        const i = e.iScale;
        const n = e.vScale;
        const o = this.getParsed(t);
        return {
            label: i ? "" + i.getLabelForValue(o[i.axis]) : "",
            value: n ? "" + n.getLabelForValue(o[n.axis]) : "",
        };
    }

    _update(t) {
        const e = this._cachedMeta;
        (this.update(t || "default"),
            (e._clip = wu(
                I(
                    this.options.clip,
                    _u(e.xScale, e.yScale, this.getMaxOverflow()),
                ),
            )));
    }

    update(t) {}
    draw() {
        const t = this._ctx;
        const e = this.chart;
        const i = this._cachedMeta;
        const n = i.data || [];
        const o = e.chartArea;
        const r = [];
        const a = this._drawStart || 0;
        const l = this._drawCount || n.length - a;
        const c = this.options.drawActiveElementsOnTop;
        let h;
        for (i.dataset && i.dataset.draw(t, o, a, l), h = a; h < a + l; ++h) {
            const u = n[h];
            u.hidden || (u.active && c ? r.push(u) : u.draw(t, o));
        }
        for (h = 0; h < r.length; ++h) r[h].draw(t, o);
    }

    getStyle(t, e) {
        const i = e ? "active" : "default";
        return t === void 0 && this._cachedMeta.dataset
            ? this.resolveDatasetElementOptions(i)
            : this.resolveDataElementOptions(t || 0, i);
    }

    getContext(t, e, i) {
        const n = this.getDataset();
        let o;
        if (t >= 0 && t < this._cachedMeta.data.length) {
            const r = this._cachedMeta.data[t];
            ((o = r.$context || (r.$context = Ou(this.getContext(), t, r))),
                (o.parsed = this.getParsed(t)),
                (o.raw = n.data[t]),
                (o.index = o.dataIndex = t));
        } else {
            ((o =
                this.$context ||
                (this.$context = Tu(this.chart.getContext(), this.index))),
                (o.dataset = n),
                (o.index = o.datasetIndex = this.index));
        }
        return ((o.active = !!e), (o.mode = i), o);
    }

    resolveDatasetElementOptions(t) {
        return this._resolveElementOptions(this.datasetElementType.id, t);
    }

    resolveDataElementOptions(t, e) {
        return this._resolveElementOptions(this.dataElementType.id, e, t);
    }

    _resolveElementOptions(t, e = "default", i) {
        const n = e === "active";
        const o = this._cachedDataOpts;
        const r = t + "-" + e;
        const a = o[r];
        const l = this.enableOptionSharing && Be(i);
        if (a) return Ma(a, l);
        const c = this.chart.config;
        const h = c.datasetElementScopeKeys(this._type, t);
        const u = n ? [`${t}Hover`, "hover", t, ""] : [t, ""];
        const d = c.getOptionScopes(this.getDataset(), h);
        const f = Object.keys(U.elements[t]);
        const m = () => this.getContext(i, n, e);
        const g = c.resolveNamedOptions(d, f, m, u);
        return (
            g.$shared && ((g.$shared = l), (o[r] = Object.freeze(Ma(g, l)))),
            g
        );
    }

    _resolveAnimations(t, e, i) {
        const n = this.chart;
        const o = this._cachedDataOpts;
        const r = `animation-${e}`;
        const a = o[r];
        if (a) return a;
        let l;
        if (n.options.animation !== !1) {
            const h = this.chart.config;
            const u = h.datasetAnimationScopeKeys(this._type, e);
            const d = h.getOptionScopes(this.getDataset(), u);
            l = h.createResolver(d, this.getContext(t, i, e));
        }
        const c = new en(n, l && l.animations);
        return (l && l._cacheable && (o[r] = Object.freeze(c)), c);
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
        return !e || fo(t) || this.chart._animationsDisabled;
    }

    _getSharedOptions(t, e) {
        const i = this.resolveDataElementOptions(t, e);
        const n = this._sharedOptions;
        const o = this.getSharedOptions(i);
        const r = this.includeOptions(e, o) || o !== n;
        return (
            this.updateSharedOptions(o, e, i),
            { sharedOptions: o, includeOptions: r }
        );
    }

    updateElement(t, e, i, n) {
        fo(n)
            ? Object.assign(t, i)
            : this._resolveAnimations(e, n).update(t, i);
    }

    updateSharedOptions(t, e, i) {
        t && !fo(e) && this._resolveAnimations(void 0, e).update(t, i);
    }

    _setStyle(t, e, i, n) {
        t.active = n;
        const o = this.getStyle(e, n);
        this._resolveAnimations(e, i, n).update(t, {
            options: (!n && this.getSharedOptions(o)) || o,
        });
    }

    removeHoverStyle(t, e, i) {
        this._setStyle(t, i, "active", !1);
    }

    setHoverStyle(t, e, i) {
        this._setStyle(t, i, "active", !0);
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
        const i = this._cachedMeta.data;
        for (const [a, l, c] of this._syncList) this[a](l, c);
        this._syncList = [];
        const n = i.length;
        const o = e.length;
        const r = Math.min(o, n);
        (r && this.parse(0, r),
            o > n
                ? this._insertElements(n, o - n, t)
                : o < n && this._removeElements(o, n - o));
    }

    _insertElements(t, e, i = !0) {
        const n = this._cachedMeta;
        const o = n.data;
        const r = t + e;
        let a;
        const l = (c) => {
            for (c.length += e, a = c.length - 1; a >= r; a--) c[a] = c[a - e];
        };
        for (l(o), a = t; a < r; ++a) o[a] = new this.dataElementType();
        (this._parsing && l(n._parsed),
            this.parse(t, e),
            i && this.updateElements(o, t, e, "reset"));
    }

    updateElements(t, e, i, n) {}
    _removeElements(t, e) {
        const i = this._cachedMeta;
        if (this._parsing) {
            const n = i._parsed.splice(t, e);
            i._stacked && Is(i, n);
        }
        i.data.splice(t, e);
    }

    _sync(t) {
        if (this._parsing) this._syncList.push(t);
        else {
            const [e, i, n] = t;
            this[e](i, n);
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
        const i = arguments.length - 2;
        i && this._sync(["_insertElements", t, i]);
    }

    _onDataUnshift() {
        this._sync(["_insertElements", 0, arguments.length]);
    }
};
(S(pt, "defaults", {}),
    S(pt, "datasetElementType", null),
    S(pt, "dataElementType", null));
function Eu(s, t) {
    if (!s._cache.$bar) {
        const e = s.getMatchingVisibleMetas(t);
        let i = [];
        for (let n = 0, o = e.length; n < o; n++) {
            i = i.concat(e[n].controller.getAllParsedValues(s));
        }
        s._cache.$bar = $n(i.sort((n, o) => n - o));
    }
    return s._cache.$bar;
}
function Iu(s) {
    const t = s.iScale;
    const e = Eu(t, s.type);
    let i = t._length;
    let n;
    let o;
    let r;
    let a;
    const l = () => {
        r === 32767 ||
            r === -32768 ||
            (Be(a) && (i = Math.min(i, Math.abs(r - a) || i)), (a = r));
    };
    for (n = 0, o = e.length; n < o; ++n) ((r = t.getPixelForValue(e[n])), l());
    for (a = void 0, n = 0, o = t.ticks.length; n < o; ++n) {
        ((r = t.getPixelForTick(n)), l());
    }
    return i;
}
function Cu(s, t, e, i) {
    const n = e.barThickness;
    let o;
    let r;
    return (
        A(n)
            ? ((o = t.min * e.categoryPercentage), (r = e.barPercentage))
            : ((o = n * i), (r = 1)),
        { chunk: o / i, ratio: r, start: t.pixels[s] - o / 2 }
    );
}
function Fu(s, t, e, i) {
    const n = t.pixels;
    const o = n[s];
    let r = s > 0 ? n[s - 1] : null;
    let a = s < n.length - 1 ? n[s + 1] : null;
    const l = e.categoryPercentage;
    (r === null && (r = o - (a === null ? t.end - t.start : a - o)),
        a === null && (a = o + o - r));
    const c = o - ((o - Math.min(r, a)) / 2) * l;
    return {
        chunk: ((Math.abs(a - r) / 2) * l) / i,
        ratio: e.barPercentage,
        start: c,
    };
}
function Au(s, t, e, i) {
    const n = e.parse(s[0], i);
    const o = e.parse(s[1], i);
    const r = Math.min(n, o);
    const a = Math.max(n, o);
    let l = r;
    let c = a;
    (Math.abs(r) > Math.abs(a) && ((l = a), (c = r)),
        (t[e.axis] = c),
        (t._custom = {
            barStart: l,
            barEnd: c,
            start: n,
            end: o,
            min: r,
            max: a,
        }));
}
function xl(s, t, e, i) {
    return ($(s) ? Au(s, t, e, i) : (t[e.axis] = e.parse(s, i)), t);
}
function va(s, t, e, i) {
    const n = s.iScale;
    const o = s.vScale;
    const r = n.getLabels();
    const a = n === o;
    const l = [];
    let c;
    let h;
    let u;
    let d;
    for (c = e, h = e + i; c < h; ++c) {
        ((d = t[c]),
            (u = {}),
            (u[n.axis] = a || n.parse(r[c], c)),
            l.push(xl(d, u, o, c)));
    }
    return l;
}
function mo(s) {
    return s && s.barStart !== void 0 && s.barEnd !== void 0;
}
function Lu(s, t, e) {
    return s !== 0
        ? Ot(s)
        : (t.isHorizontal() ? 1 : -1) * (t.min >= e ? 1 : -1);
}
function Pu(s) {
    let t, e, i, n, o;
    return (
        s.horizontal
            ? ((t = s.base > s.x), (e = "left"), (i = "right"))
            : ((t = s.base < s.y), (e = "bottom"), (i = "top")),
        t ? ((n = "end"), (o = "start")) : ((n = "start"), (o = "end")),
        { start: e, end: i, reverse: t, top: n, bottom: o }
    );
}
function Nu(s, t, e, i) {
    let n = t.borderSkipped;
    const o = {};
    if (!n) {
        s.borderSkipped = o;
        return;
    }
    if (n === !0) {
        s.borderSkipped = { top: !0, right: !0, bottom: !0, left: !0 };
        return;
    }
    const { start: r, end: a, reverse: l, top: c, bottom: h } = Pu(s);
    (n === "middle" &&
        e &&
        ((s.enableBorderRadius = !0),
        (e._top || 0) === i
            ? (n = c)
            : (e._bottom || 0) === i
              ? (n = h)
              : ((o[Ta(h, r, a, l)] = !0), (n = c))),
        (o[Ta(n, r, a, l)] = !0),
        (s.borderSkipped = o));
}
function Ta(s, t, e, i) {
    return (i ? ((s = Ru(s, t, e)), (s = Oa(s, e, t))) : (s = Oa(s, t, e)), s);
}
function Ru(s, t, e) {
    return s === t ? e : s === e ? t : s;
}
function Oa(s, t, e) {
    return s === "start" ? t : s === "end" ? e : s;
}
function Wu(s, { inflateAmount: t }, e) {
    s.inflateAmount = t === "auto" ? (e === 1 ? 0.33 : 0) : t;
}
const Ge = class extends pt {
    parsePrimitiveData(t, e, i, n) {
        return va(t, e, i, n);
    }

    parseArrayData(t, e, i, n) {
        return va(t, e, i, n);
    }

    parseObjectData(t, e, i, n) {
        const { iScale: o, vScale: r } = t;
        const { xAxisKey: a = "x", yAxisKey: l = "y" } = this._parsing;
        const c = o.axis === "x" ? a : l;
        const h = r.axis === "x" ? a : l;
        const u = [];
        let d;
        let f;
        let m;
        let g;
        for (d = i, f = i + n; d < f; ++d) {
            ((g = e[d]),
                (m = {}),
                (m[o.axis] = o.parse(Ut(g, c), d)),
                u.push(xl(Ut(g, h), m, r, d)));
        }
        return u;
    }

    updateRangeFromParsed(t, e, i, n) {
        super.updateRangeFromParsed(t, e, i, n);
        const o = i._custom;
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
        const { iScale: i, vScale: n } = e;
        const o = this.getParsed(t);
        const r = o._custom;
        const a = mo(r)
            ? "[" + r.start + ", " + r.end + "]"
            : "" + n.getLabelForValue(o[n.axis]);
        return { label: "" + i.getLabelForValue(o[i.axis]), value: a };
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

    updateElements(t, e, i, n) {
        const o = n === "reset";
        const {
            index: r,
            _cachedMeta: { vScale: a },
        } = this;
        const l = a.getBasePixel();
        const c = a.isHorizontal();
        const h = this._getRuler();
        const { sharedOptions: u, includeOptions: d } = this._getSharedOptions(
            e,
            n,
        );
        for (let f = e; f < e + i; f++) {
            const m = this.getParsed(f);
            const g =
                o || A(m[a.axis])
                    ? { base: l, head: l }
                    : this._calculateBarValuePixels(f);
            const p = this._calculateBarIndexPixels(f, h);
            const y = (m._stacks || {})[a.axis];
            const b = {
                horizontal: c,
                base: g.base,
                enableBorderRadius:
                    !y || mo(m._custom) || r === y._top || r === y._bottom,
                x: c ? g.head : p.center,
                y: c ? p.center : g.head,
                height: c ? p.size : Math.abs(g.size),
                width: c ? Math.abs(g.size) : p.size,
            };
            d &&
                (b.options =
                    u ||
                    this.resolveDataElementOptions(
                        f,
                        t[f].active ? "active" : n,
                    ));
            const _ = b.options || t[f].options;
            (Nu(b, _, y, r),
                Wu(b, _, h.ratio),
                this.updateElement(t[f], f, b, n));
        }
    }

    _getStacks(t, e) {
        const { iScale: i } = this._cachedMeta;
        const n = i
            .getMatchingVisibleMetas(this._type)
            .filter((h) => h.controller.options.grouped);
        const o = i.options.stacked;
        const r = [];
        const a = this._cachedMeta.controller.getParsed(e);
        const l = a && a[i.axis];
        const c = (h) => {
            const u = h._parsed.find((f) => f[i.axis] === l);
            const d = u && u[h.vScale.axis];
            if (A(d) || isNaN(d)) return !0;
        };
        for (const h of n) {
            if (
                !(e !== void 0 && c(h)) &&
                ((o === !1 ||
                    r.indexOf(h.stack) === -1 ||
                    (o === void 0 && h.stack === void 0)) &&
                    r.push(h.stack),
                h.index === t)
            ) {
                break;
            }
        }
        return (r.length || r.push(void 0), r);
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
            .filter((i) => t[i].axis === e)
            .shift();
    }

    _getAxis() {
        const t = {};
        const e = this.getFirstScaleIdForIndexAxis();
        for (const i of this.chart.data.datasets) {
            t[
                I(
                    this.chart.options.indexAxis === "x"
                        ? i.xAxisID
                        : i.yAxisID,
                    e,
                )
            ] = !0;
        }
        return Object.keys(t);
    }

    _getStackIndex(t, e, i) {
        const n = this._getStacks(t, i);
        const o = e !== void 0 ? n.indexOf(e) : -1;
        return o === -1 ? n.length - 1 : o;
    }

    _getRuler() {
        const t = this.options;
        const e = this._cachedMeta;
        const i = e.iScale;
        const n = [];
        let o;
        let r;
        for (o = 0, r = e.data.length; o < r; ++o) {
            n.push(i.getPixelForValue(this.getParsed(o)[i.axis], o));
        }
        const a = t.barThickness;
        return {
            min: a || Iu(e),
            pixels: n,
            start: i._startPixel,
            end: i._endPixel,
            stackCount: this._getStackCount(),
            scale: i,
            grouped: t.grouped,
            ratio: a ? 1 : t.categoryPercentage * t.barPercentage,
        };
    }

    _calculateBarValuePixels(t) {
        const {
            _cachedMeta: { vScale: e, _stacked: i, index: n },
            options: { base: o, minBarLength: r },
        } = this;
        const a = o || 0;
        const l = this.getParsed(t);
        const c = l._custom;
        const h = mo(c);
        let u = l[e.axis];
        let d = 0;
        let f = i ? this.applyStack(e, l, i) : u;
        let m;
        let g;
        (f !== u && ((d = f - u), (f = u)),
            h &&
                ((u = c.barStart),
                (f = c.barEnd - c.barStart),
                u !== 0 && Ot(u) !== Ot(c.barEnd) && (d = 0),
                (d += u)));
        const p = !A(o) && !h ? o : d;
        let y = e.getPixelForValue(p);
        if (
            (this.chart.getDataVisibility(t)
                ? (m = e.getPixelForValue(d + f))
                : (m = y),
            (g = m - y),
            Math.abs(g) < r)
        ) {
            ((g = Lu(g, e, a) * r), u === a && (y -= g / 2));
            const b = e.getPixelForDecimal(0);
            const _ = e.getPixelForDecimal(1);
            const w = Math.min(b, _);
            const x = Math.max(b, _);
            ((y = Math.max(Math.min(y, x), w)),
                (m = y + g),
                i &&
                    !h &&
                    (l._stacks[e.axis]._visualValues[n] =
                        e.getValueForPixel(m) - e.getValueForPixel(y)));
        }
        if (y === e.getPixelForValue(a)) {
            const b = (Ot(g) * e.getLineWidthForValue(a)) / 2;
            ((y += b), (g -= b));
        }
        return { size: g, base: y, head: m, center: m + g / 2 };
    }

    _calculateBarIndexPixels(t, e) {
        const i = e.scale;
        const n = this.options;
        const o = n.skipNull;
        const r = I(n.maxBarThickness, 1 / 0);
        let a;
        let l;
        const c = this._getAxisCount();
        if (e.grouped) {
            const h = o ? this._getStackCount(t) : e.stackCount;
            const u =
                n.barThickness === "flex"
                    ? Fu(t, e, n, h * c)
                    : Cu(t, e, n, h * c);
            const d =
                this.chart.options.indexAxis === "x"
                    ? this.getDataset().xAxisID
                    : this.getDataset().yAxisID;
            const f = this._getAxis().indexOf(
                I(d, this.getFirstScaleIdForIndexAxis()),
            );
            const m =
                this._getStackIndex(
                    this.index,
                    this._cachedMeta.stack,
                    o ? t : void 0,
                ) + f;
            ((a = u.start + u.chunk * m + u.chunk / 2),
                (l = Math.min(r, u.chunk * u.ratio)));
        } else {
            ((a = i.getPixelForValue(this.getParsed(t)[i.axis], t)),
                (l = Math.min(r, e.min * e.ratio)));
        }
        return { base: a - l / 2, head: a + l / 2, center: a, size: l };
    }

    draw() {
        const t = this._cachedMeta;
        const e = t.vScale;
        const i = t.data;
        const n = i.length;
        let o = 0;
        for (; o < n; ++o) {
            this.getParsed(o)[e.axis] !== null &&
                !i[o].hidden &&
                i[o].draw(this._ctx);
        }
    }
};
(S(Ge, "id", "bar"),
    S(Ge, "defaults", {
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
    S(Ge, "overrides", {
        scales: {
            _index_: { type: "category", offset: !0, grid: { offset: !0 } },
            _value_: { type: "linear", beginAtZero: !0 },
        },
    }));
const Xe = class extends pt {
    initialize() {
        ((this.enableOptionSharing = !0), super.initialize());
    }

    parsePrimitiveData(t, e, i, n) {
        const o = super.parsePrimitiveData(t, e, i, n);
        for (let r = 0; r < o.length; r++) {
            o[r]._custom = this.resolveDataElementOptions(r + i).radius;
        }
        return o;
    }

    parseArrayData(t, e, i, n) {
        const o = super.parseArrayData(t, e, i, n);
        for (let r = 0; r < o.length; r++) {
            const a = e[i + r];
            o[r]._custom = I(
                a[2],
                this.resolveDataElementOptions(r + i).radius,
            );
        }
        return o;
    }

    parseObjectData(t, e, i, n) {
        const o = super.parseObjectData(t, e, i, n);
        for (let r = 0; r < o.length; r++) {
            const a = e[i + r];
            o[r]._custom = I(
                a && a.r && +a.r,
                this.resolveDataElementOptions(r + i).radius,
            );
        }
        return o;
    }

    getMaxOverflow() {
        const t = this._cachedMeta.data;
        let e = 0;
        for (let i = t.length - 1; i >= 0; --i) {
            e = Math.max(e, t[i].size(this.resolveDataElementOptions(i)) / 2);
        }
        return e > 0 && e;
    }

    getLabelAndValue(t) {
        const e = this._cachedMeta;
        const i = this.chart.data.labels || [];
        const { xScale: n, yScale: o } = e;
        const r = this.getParsed(t);
        const a = n.getLabelForValue(r.x);
        const l = o.getLabelForValue(r.y);
        const c = r._custom;
        return {
            label: i[t] || "",
            value: "(" + a + ", " + l + (c ? ", " + c : "") + ")",
        };
    }

    update(t) {
        const e = this._cachedMeta.data;
        this.updateElements(e, 0, e.length, t);
    }

    updateElements(t, e, i, n) {
        const o = n === "reset";
        const { iScale: r, vScale: a } = this._cachedMeta;
        const { sharedOptions: l, includeOptions: c } = this._getSharedOptions(
            e,
            n,
        );
        const h = r.axis;
        const u = a.axis;
        for (let d = e; d < e + i; d++) {
            const f = t[d];
            const m = !o && this.getParsed(d);
            const g = {};
            const p = (g[h] = o
                ? r.getPixelForDecimal(0.5)
                : r.getPixelForValue(m[h]));
            const y = (g[u] = o ? a.getBasePixel() : a.getPixelForValue(m[u]));
            ((g.skip = isNaN(p) || isNaN(y)),
                c &&
                    ((g.options =
                        l ||
                        this.resolveDataElementOptions(
                            d,
                            f.active ? "active" : n,
                        )),
                    o && (g.options.radius = 0)),
                this.updateElement(f, d, g, n));
        }
    }

    resolveDataElementOptions(t, e) {
        const i = this.getParsed(t);
        let n = super.resolveDataElementOptions(t, e);
        n.$shared && (n = Object.assign({}, n, { $shared: !1 }));
        const o = n.radius;
        return (
            e !== "active" && (n.radius = 0),
            (n.radius += I(i && i._custom, o)),
            n
        );
    }
};
(S(Xe, "id", "bubble"),
    S(Xe, "defaults", {
        datasetElementType: !1,
        dataElementType: "point",
        animations: {
            numbers: {
                type: "number",
                properties: ["x", "y", "borderWidth", "radius"],
            },
        },
    }),
    S(Xe, "overrides", {
        scales: { x: { type: "linear" }, y: { type: "linear" } },
    }));
function zu(s, t, e) {
    let i = 1;
    let n = 1;
    let o = 0;
    let r = 0;
    if (t < j) {
        const a = s;
        const l = a + t;
        const c = Math.cos(a);
        const h = Math.sin(a);
        const u = Math.cos(l);
        const d = Math.sin(l);
        const f = (_, w, x) =>
            je(_, a, l, !0) ? 1 : Math.max(w, w * e, x, x * e);
        const m = (_, w, x) =>
            je(_, a, l, !0) ? -1 : Math.min(w, w * e, x, x * e);
        const g = f(0, c, u);
        const p = f(X, h, d);
        const y = m(N, c, u);
        const b = m(N + X, h, d);
        ((i = (g - y) / 2),
            (n = (p - b) / 2),
            (o = -(g + y) / 2),
            (r = -(p + b) / 2));
    }
    return { ratioX: i, ratioY: n, offsetX: o, offsetY: r };
}
const Gt = class extends pt {
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
        const i = this.getDataset().data;
        const n = this._cachedMeta;
        if (this._parsing === !1) n._parsed = i;
        else {
            let o = (l) => +i[l];
            if (L(i[t])) {
                const { key: l = "value" } = this._parsing;
                o = (c) => +Ut(i[c], l);
            }
            let r, a;
            for (r = t, a = t + e; r < a; ++r) n._parsed[r] = o(r);
        }
    }

    _getRotation() {
        return wt(this.options.rotation - 90);
    }

    _getCircumference() {
        return wt(this.options.circumference);
    }

    _getRotationExtents() {
        let t = j;
        let e = -j;
        for (let i = 0; i < this.chart.data.datasets.length; ++i) {
            if (
                this.chart.isDatasetVisible(i) &&
                this.chart.getDatasetMeta(i).type === this._type
            ) {
                const n = this.chart.getDatasetMeta(i).controller;
                const o = n._getRotation();
                const r = n._getCircumference();
                ((t = Math.min(t, o)), (e = Math.max(e, o + r)));
            }
        }
        return { rotation: t, circumference: e - t };
    }

    update(t) {
        const e = this.chart;
        const { chartArea: i } = e;
        const n = this._cachedMeta;
        const o = n.data;
        const r =
            this.getMaxBorderWidth() +
            this.getMaxOffset(o) +
            this.options.spacing;
        const a = Math.max((Math.min(i.width, i.height) - r) / 2, 0);
        const l = Math.min(Ur(this.options.cutout, a), 1);
        const c = this._getRingWeight(this.index);
        const { circumference: h, rotation: u } = this._getRotationExtents();
        const { ratioX: d, ratioY: f, offsetX: m, offsetY: g } = zu(u, h, l);
        const p = (i.width - r) / d;
        const y = (i.height - r) / f;
        const b = Math.max(Math.min(p, y) / 2, 0);
        const _ = Nn(this.options.radius, b);
        const w = Math.max(_ * l, 0);
        const x = (_ - w) / this._getVisibleDatasetWeightTotal();
        ((this.offsetX = m * _),
            (this.offsetY = g * _),
            (n.total = this.calculateTotal()),
            (this.outerRadius = _ - x * this._getRingWeightOffset(this.index)),
            (this.innerRadius = Math.max(this.outerRadius - x * c, 0)),
            this.updateElements(o, 0, o.length, t));
    }

    _circumference(t, e) {
        const i = this.options;
        const n = this._cachedMeta;
        const o = this._getCircumference();
        return (e && i.animation.animateRotate) ||
            !this.chart.getDataVisibility(t) ||
            n._parsed[t] === null ||
            n.data[t].hidden
            ? 0
            : this.calculateCircumference((n._parsed[t] * o) / j);
    }

    updateElements(t, e, i, n) {
        const o = n === "reset";
        const r = this.chart;
        const a = r.chartArea;
        const c = r.options.animation;
        const h = (a.left + a.right) / 2;
        const u = (a.top + a.bottom) / 2;
        const d = o && c.animateScale;
        const f = d ? 0 : this.innerRadius;
        const m = d ? 0 : this.outerRadius;
        const { sharedOptions: g, includeOptions: p } = this._getSharedOptions(
            e,
            n,
        );
        let y = this._getRotation();
        let b;
        for (b = 0; b < e; ++b) y += this._circumference(b, o);
        for (b = e; b < e + i; ++b) {
            const _ = this._circumference(b, o);
            const w = t[b];
            const x = {
                x: h + this.offsetX,
                y: u + this.offsetY,
                startAngle: y,
                endAngle: y + _,
                circumference: _,
                outerRadius: m,
                innerRadius: f,
            };
            (p &&
                (x.options =
                    g ||
                    this.resolveDataElementOptions(b, w.active ? "active" : n)),
                (y += _),
                this.updateElement(w, b, x, n));
        }
    }

    calculateTotal() {
        const t = this._cachedMeta;
        const e = t.data;
        let i = 0;
        let n;
        for (n = 0; n < e.length; n++) {
            const o = t._parsed[n];
            o !== null &&
                !isNaN(o) &&
                this.chart.getDataVisibility(n) &&
                !e[n].hidden &&
                (i += Math.abs(o));
        }
        return i;
    }

    calculateCircumference(t) {
        const e = this._cachedMeta.total;
        return e > 0 && !isNaN(t) ? j * (Math.abs(t) / e) : 0;
    }

    getLabelAndValue(t) {
        const e = this._cachedMeta;
        const i = this.chart;
        const n = i.data.labels || [];
        const o = Ue(e._parsed[t], i.options.locale);
        return { label: n[t] || "", value: o };
    }

    getMaxBorderWidth(t) {
        let e = 0;
        const i = this.chart;
        let n;
        let o;
        let r;
        let a;
        let l;
        if (!t) {
            for (n = 0, o = i.data.datasets.length; n < o; ++n) {
                if (i.isDatasetVisible(n)) {
                    ((r = i.getDatasetMeta(n)),
                        (t = r.data),
                        (a = r.controller));
                    break;
                }
            }
        }
        if (!t) return 0;
        for (n = 0, o = t.length; n < o; ++n) {
            ((l = a.resolveDataElementOptions(n)),
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
        for (let i = 0, n = t.length; i < n; ++i) {
            const o = this.resolveDataElementOptions(i);
            e = Math.max(e, o.offset || 0, o.hoverOffset || 0);
        }
        return e;
    }

    _getRingWeightOffset(t) {
        let e = 0;
        for (let i = 0; i < t; ++i) {
            this.chart.isDatasetVisible(i) && (e += this._getRingWeight(i));
        }
        return e;
    }

    _getRingWeight(t) {
        return Math.max(I(this.chart.data.datasets[t].weight, 1), 0);
    }

    _getVisibleDatasetWeightTotal() {
        return this._getRingWeightOffset(this.chart.data.datasets.length) || 1;
    }
};
(S(Gt, "id", "doughnut"),
    S(Gt, "defaults", {
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
    S(Gt, "descriptors", {
        _scriptable: (t) => t !== "spacing",
        _indexable: (t) =>
            t !== "spacing" &&
            !t.startsWith("borderDash") &&
            !t.startsWith("hoverBorderDash"),
    }),
    S(Gt, "overrides", {
        aspectRatio: 1,
        plugins: {
            legend: {
                labels: {
                    generateLabels(t) {
                        const e = t.data;
                        if (e.labels.length && e.datasets.length) {
                            const {
                                labels: { pointStyle: i, color: n },
                            } = t.legend.options;
                            return e.labels.map((o, r) => {
                                const l = t
                                    .getDatasetMeta(0)
                                    .controller.getStyle(r);
                                return {
                                    text: o,
                                    fillStyle: l.backgroundColor,
                                    strokeStyle: l.borderColor,
                                    fontColor: n,
                                    lineWidth: l.borderWidth,
                                    pointStyle: i,
                                    hidden: !t.getDataVisibility(r),
                                    index: r,
                                };
                            });
                        }
                        return [];
                    },
                },
                onClick(t, e, i) {
                    (i.chart.toggleDataVisibility(e.index), i.chart.update());
                },
            },
        },
    }));
const Ke = class extends pt {
    initialize() {
        ((this.enableOptionSharing = !0),
            (this.supportsDecimation = !0),
            super.initialize());
    }

    update(t) {
        const e = this._cachedMeta;
        const { dataset: i, data: n = [], _dataset: o } = e;
        const r = this.chart._animationsDisabled;
        let { start: a, count: l } = Yn(e, n, r);
        ((this._drawStart = a),
            (this._drawCount = l),
            Zn(e) && ((a = 0), (l = n.length)),
            (i._chart = this.chart),
            (i._datasetIndex = this.index),
            (i._decimated = !!o._decimated),
            (i.points = n));
        const c = this.resolveDatasetElementOptions(t);
        (this.options.showLine || (c.borderWidth = 0),
            (c.segment = this.options.segment),
            this.updateElement(i, void 0, { animated: !r, options: c }, t),
            this.updateElements(n, a, l, t));
    }

    updateElements(t, e, i, n) {
        const o = n === "reset";
        const {
            iScale: r,
            vScale: a,
            _stacked: l,
            _dataset: c,
        } = this._cachedMeta;
        const { sharedOptions: h, includeOptions: u } = this._getSharedOptions(
            e,
            n,
        );
        const d = r.axis;
        const f = a.axis;
        const { spanGaps: m, segment: g } = this.options;
        const p = we(m) ? m : Number.POSITIVE_INFINITY;
        const y = this.chart._animationsDisabled || o || n === "none";
        const b = e + i;
        const _ = t.length;
        let w = e > 0 && this.getParsed(e - 1);
        for (let x = 0; x < _; ++x) {
            const k = t[x];
            const M = y ? k : {};
            if (x < e || x >= b) {
                M.skip = !0;
                continue;
            }
            const v = this.getParsed(x);
            const O = A(v[f]);
            const E = (M[d] = r.getPixelForValue(v[d], x));
            const C = (M[f] =
                o || O
                    ? a.getBasePixel()
                    : a.getPixelForValue(
                          l ? this.applyStack(a, v, l) : v[f],
                          x,
                      ));
            ((M.skip = isNaN(E) || isNaN(C) || O),
                (M.stop = x > 0 && Math.abs(v[d] - w[d]) > p),
                g && ((M.parsed = v), (M.raw = c.data[x])),
                u &&
                    (M.options =
                        h ||
                        this.resolveDataElementOptions(
                            x,
                            k.active ? "active" : n,
                        )),
                y || this.updateElement(k, x, M, n),
                (w = v));
        }
    }

    getMaxOverflow() {
        const t = this._cachedMeta;
        const e = t.dataset;
        const i = (e.options && e.options.borderWidth) || 0;
        const n = t.data || [];
        if (!n.length) return i;
        const o = n[0].size(this.resolveDataElementOptions(0));
        const r = n[n.length - 1].size(
            this.resolveDataElementOptions(n.length - 1),
        );
        return Math.max(i, o, r) / 2;
    }

    draw() {
        const t = this._cachedMeta;
        (t.dataset.updateControlPoints(this.chart.chartArea, t.iScale.axis),
            super.draw());
    }
};
(S(Ke, "id", "line"),
    S(Ke, "defaults", {
        datasetElementType: "line",
        dataElementType: "point",
        showLine: !0,
        spanGaps: !1,
    }),
    S(Ke, "overrides", {
        scales: { _index_: { type: "category" }, _value_: { type: "linear" } },
    }));
const Oe = class extends pt {
    constructor(t, e) {
        (super(t, e), (this.innerRadius = void 0), (this.outerRadius = void 0));
    }

    getLabelAndValue(t) {
        const e = this._cachedMeta;
        const i = this.chart;
        const n = i.data.labels || [];
        const o = Ue(e._parsed[t].r, i.options.locale);
        return { label: n[t] || "", value: o };
    }

    parseObjectData(t, e, i, n) {
        return so.bind(this)(t, e, i, n);
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
            t.data.forEach((i, n) => {
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
        const i = t.options;
        const n = Math.min(e.right - e.left, e.bottom - e.top);
        const o = Math.max(n / 2, 0);
        const r = Math.max(
            i.cutoutPercentage ? (o / 100) * i.cutoutPercentage : 1,
            0,
        );
        const a = (o - r) / t.getVisibleDatasetCount();
        ((this.outerRadius = o - a * this.index),
            (this.innerRadius = this.outerRadius - a));
    }

    updateElements(t, e, i, n) {
        const o = n === "reset";
        const r = this.chart;
        const l = r.options.animation;
        const c = this._cachedMeta.rScale;
        const h = c.xCenter;
        const u = c.yCenter;
        const d = c.getIndexAngle(0) - 0.5 * N;
        let f = d;
        let m;
        const g = 360 / this.countVisibleElements();
        for (m = 0; m < e; ++m) f += this._computeAngle(m, n, g);
        for (m = e; m < e + i; m++) {
            const p = t[m];
            let y = f;
            let b = f + this._computeAngle(m, n, g);
            let _ = r.getDataVisibility(m)
                ? c.getDistanceFromCenterForValue(this.getParsed(m).r)
                : 0;
            ((f = b),
                o &&
                    (l.animateScale && (_ = 0),
                    l.animateRotate && (y = b = d)));
            const w = {
                x: h,
                y: u,
                innerRadius: 0,
                outerRadius: _,
                startAngle: y,
                endAngle: b,
                options: this.resolveDataElementOptions(
                    m,
                    p.active ? "active" : n,
                ),
            };
            this.updateElement(p, m, w, n);
        }
    }

    countVisibleElements() {
        const t = this._cachedMeta;
        let e = 0;
        return (
            t.data.forEach((i, n) => {
                !isNaN(this.getParsed(n).r) &&
                    this.chart.getDataVisibility(n) &&
                    e++;
            }),
            e
        );
    }

    _computeAngle(t, e, i) {
        return this.chart.getDataVisibility(t)
            ? wt(this.resolveDataElementOptions(t, e).angle || i)
            : 0;
    }
};
(S(Oe, "id", "polarArea"),
    S(Oe, "defaults", {
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
    S(Oe, "overrides", {
        aspectRatio: 1,
        plugins: {
            legend: {
                labels: {
                    generateLabels(t) {
                        const e = t.data;
                        if (e.labels.length && e.datasets.length) {
                            const {
                                labels: { pointStyle: i, color: n },
                            } = t.legend.options;
                            return e.labels.map((o, r) => {
                                const l = t
                                    .getDatasetMeta(0)
                                    .controller.getStyle(r);
                                return {
                                    text: o,
                                    fillStyle: l.backgroundColor,
                                    strokeStyle: l.borderColor,
                                    fontColor: n,
                                    lineWidth: l.borderWidth,
                                    pointStyle: i,
                                    hidden: !t.getDataVisibility(r),
                                    index: r,
                                };
                            });
                        }
                        return [];
                    },
                },
                onClick(t, e, i) {
                    (i.chart.toggleDataVisibility(e.index), i.chart.update());
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
const Ps = class extends Gt {};
(S(Ps, "id", "pie"),
    S(Ps, "defaults", {
        cutout: 0,
        rotation: 0,
        circumference: 360,
        radius: "100%",
    }));
const Je = class extends pt {
    getLabelAndValue(t) {
        const e = this._cachedMeta.vScale;
        const i = this.getParsed(t);
        return {
            label: e.getLabels()[t],
            value: "" + e.getLabelForValue(i[e.axis]),
        };
    }

    parseObjectData(t, e, i, n) {
        return so.bind(this)(t, e, i, n);
    }

    update(t) {
        const e = this._cachedMeta;
        const i = e.dataset;
        const n = e.data || [];
        const o = e.iScale.getLabels();
        if (((i.points = n), t !== "resize")) {
            const r = this.resolveDatasetElementOptions(t);
            this.options.showLine || (r.borderWidth = 0);
            const a = {
                _loop: !0,
                _fullLoop: o.length === n.length,
                options: r,
            };
            this.updateElement(i, void 0, a, t);
        }
        this.updateElements(n, 0, n.length, t);
    }

    updateElements(t, e, i, n) {
        const o = this._cachedMeta.rScale;
        const r = n === "reset";
        for (let a = e; a < e + i; a++) {
            const l = t[a];
            const c = this.resolveDataElementOptions(
                a,
                l.active ? "active" : n,
            );
            const h = o.getPointPositionForValue(a, this.getParsed(a).r);
            const u = r ? o.xCenter : h.x;
            const d = r ? o.yCenter : h.y;
            const f = {
                x: u,
                y: d,
                angle: h.angle,
                skip: isNaN(u) || isNaN(d),
                options: c,
            };
            this.updateElement(l, a, f, n);
        }
    }
};
(S(Je, "id", "radar"),
    S(Je, "defaults", {
        datasetElementType: "line",
        dataElementType: "point",
        indexAxis: "r",
        showLine: !0,
        elements: { line: { fill: "start" } },
    }),
    S(Je, "overrides", {
        aspectRatio: 1,
        scales: { r: { type: "radialLinear" } },
    }));
const Qe = class extends pt {
    getLabelAndValue(t) {
        const e = this._cachedMeta;
        const i = this.chart.data.labels || [];
        const { xScale: n, yScale: o } = e;
        const r = this.getParsed(t);
        const a = n.getLabelForValue(r.x);
        const l = o.getLabelForValue(r.y);
        return { label: i[t] || "", value: "(" + a + ", " + l + ")" };
    }

    update(t) {
        const e = this._cachedMeta;
        const { data: i = [] } = e;
        const n = this.chart._animationsDisabled;
        let { start: o, count: r } = Yn(e, i, n);
        if (
            ((this._drawStart = o),
            (this._drawCount = r),
            Zn(e) && ((o = 0), (r = i.length)),
            this.options.showLine)
        ) {
            this.datasetElementType || this.addElements();
            const { dataset: a, _dataset: l } = e;
            ((a._chart = this.chart),
                (a._datasetIndex = this.index),
                (a._decimated = !!l._decimated),
                (a.points = i));
            const c = this.resolveDatasetElementOptions(t);
            ((c.segment = this.options.segment),
                this.updateElement(a, void 0, { animated: !n, options: c }, t));
        } else {
            this.datasetElementType &&
                (delete e.dataset, (this.datasetElementType = !1));
        }
        this.updateElements(i, o, r, t);
    }

    addElements() {
        const { showLine: t } = this.options;
        (!this.datasetElementType &&
            t &&
            (this.datasetElementType = this.chart.registry.getElement("line")),
            super.addElements());
    }

    updateElements(t, e, i, n) {
        const o = n === "reset";
        const {
            iScale: r,
            vScale: a,
            _stacked: l,
            _dataset: c,
        } = this._cachedMeta;
        const h = this.resolveDataElementOptions(e, n);
        const u = this.getSharedOptions(h);
        const d = this.includeOptions(n, u);
        const f = r.axis;
        const m = a.axis;
        const { spanGaps: g, segment: p } = this.options;
        const y = we(g) ? g : Number.POSITIVE_INFINITY;
        const b = this.chart._animationsDisabled || o || n === "none";
        let _ = e > 0 && this.getParsed(e - 1);
        for (let w = e; w < e + i; ++w) {
            const x = t[w];
            const k = this.getParsed(w);
            const M = b ? x : {};
            const v = A(k[m]);
            const O = (M[f] = r.getPixelForValue(k[f], w));
            const E = (M[m] =
                o || v
                    ? a.getBasePixel()
                    : a.getPixelForValue(
                          l ? this.applyStack(a, k, l) : k[m],
                          w,
                      ));
            ((M.skip = isNaN(O) || isNaN(E) || v),
                (M.stop = w > 0 && Math.abs(k[f] - _[f]) > y),
                p && ((M.parsed = k), (M.raw = c.data[w])),
                d &&
                    (M.options =
                        u ||
                        this.resolveDataElementOptions(
                            w,
                            x.active ? "active" : n,
                        )),
                b || this.updateElement(x, w, M, n),
                (_ = k));
        }
        this.updateSharedOptions(u, n, h);
    }

    getMaxOverflow() {
        const t = this._cachedMeta;
        const e = t.data || [];
        if (!this.options.showLine) {
            let a = 0;
            for (let l = e.length - 1; l >= 0; --l) {
                a = Math.max(
                    a,
                    e[l].size(this.resolveDataElementOptions(l)) / 2,
                );
            }
            return a > 0 && a;
        }
        const i = t.dataset;
        const n = (i.options && i.options.borderWidth) || 0;
        if (!e.length) return n;
        const o = e[0].size(this.resolveDataElementOptions(0));
        const r = e[e.length - 1].size(
            this.resolveDataElementOptions(e.length - 1),
        );
        return Math.max(n, o, r) / 2;
    }
};
(S(Qe, "id", "scatter"),
    S(Qe, "defaults", {
        datasetElementType: !1,
        dataElementType: "point",
        showLine: !1,
        fill: !1,
    }),
    S(Qe, "overrides", {
        interaction: { mode: "point" },
        scales: { x: { type: "linear" }, y: { type: "linear" } },
    }));
const Vu = Object.freeze({
    __proto__: null,
    BarController: Ge,
    BubbleController: Xe,
    DoughnutController: Gt,
    LineController: Ke,
    PieController: Ps,
    PolarAreaController: Oe,
    RadarController: Je,
    ScatterController: Qe,
});
function ke() {
    throw new Error(
        "This method is not implemented: Check that a complete date adapter is provided.",
    );
}
const Mo = class s {
    constructor(t) {
        S(this, "options");
        this.options = t || {};
    }

    static override(t) {
        Object.assign(s.prototype, t);
    }

    init() {}
    formats() {
        return ke();
    }

    parse() {
        return ke();
    }

    format() {
        return ke();
    }

    add() {
        return ke();
    }

    diff() {
        return ke();
    }

    startOf() {
        return ke();
    }

    endOf() {
        return ke();
    }
};
const No = { _date: Mo };
function Hu(s, t, e, i) {
    const { controller: n, data: o, _sorted: r } = s;
    const a = n._cachedMeta.iScale;
    const l =
        s.dataset && s.dataset.options ? s.dataset.options.spanGaps : null;
    if (a && t === a.axis && t !== "r" && r && o.length) {
        const c = a._reversePixels ? Kr : Lt;
        if (i) {
            if (n._sharedOptions) {
                const h = o[0];
                const u = typeof h.getRange === "function" && h.getRange(t);
                if (u) {
                    const d = c(o, t, e - u);
                    const f = c(o, t, e + u);
                    return { lo: d.lo, hi: f.hi };
                }
            }
        } else {
            const h = c(o, t, e);
            if (l) {
                const { vScale: u } = n._cachedMeta;
                const { _parsed: d } = s;
                const f = d
                    .slice(0, h.lo + 1)
                    .reverse()
                    .findIndex((g) => !A(g[u.axis]));
                h.lo -= Math.max(0, f);
                const m = d.slice(h.hi).findIndex((g) => !A(g[u.axis]));
                h.hi += Math.max(0, m);
            }
            return h;
        }
    }
    return { lo: 0, hi: o.length - 1 };
}
function Ys(s, t, e, i, n) {
    const o = s.getSortedVisibleDatasetMetas();
    const r = e[t];
    for (let a = 0, l = o.length; a < l; ++a) {
        const { index: c, data: h } = o[a];
        const { lo: u, hi: d } = Hu(o[a], t, r, n);
        for (let f = u; f <= d; ++f) {
            const m = h[f];
            m.skip || i(m, c, f);
        }
    }
}
function Bu(s) {
    const t = s.indexOf("x") !== -1;
    const e = s.indexOf("y") !== -1;
    return function (i, n) {
        const o = t ? Math.abs(i.x - n.x) : 0;
        const r = e ? Math.abs(i.y - n.y) : 0;
        return Math.sqrt(Math.pow(o, 2) + Math.pow(r, 2));
    };
}
function go(s, t, e, i, n) {
    const o = [];
    return (
        (!n && !s.isPointInArea(t)) ||
            Ys(
                s,
                e,
                t,
                function (a, l, c) {
                    (!n && !Pt(a, s.chartArea, 0)) ||
                        (a.inRange(t.x, t.y, i) &&
                            o.push({ element: a, datasetIndex: l, index: c }));
                },
                !0,
            ),
        o
    );
}
function $u(s, t, e, i) {
    const n = [];
    function o(r, a, l) {
        const { startAngle: c, endAngle: h } = r.getProps(
            ["startAngle", "endAngle"],
            i,
        );
        const { angle: u } = Hn(r, { x: t.x, y: t.y });
        je(u, c, h) && n.push({ element: r, datasetIndex: a, index: l });
    }
    return (Ys(s, e, t, o), n);
}
function ju(s, t, e, i, n, o) {
    let r = [];
    const a = Bu(e);
    let l = Number.POSITIVE_INFINITY;
    function c(h, u, d) {
        const f = h.inRange(t.x, t.y, n);
        if (i && !f) return;
        const m = h.getCenterPoint(n);
        if (!(!!o || s.isPointInArea(m)) && !f) return;
        const p = a(t, m);
        p < l
            ? ((r = [{ element: h, datasetIndex: u, index: d }]), (l = p))
            : p === l && r.push({ element: h, datasetIndex: u, index: d });
    }
    return (Ys(s, e, t, c), r);
}
function po(s, t, e, i, n, o) {
    return !o && !s.isPointInArea(t)
        ? []
        : e === "r" && !i
          ? $u(s, t, e, n)
          : ju(s, t, e, i, n, o);
}
function Da(s, t, e, i, n) {
    const o = [];
    const r = e === "x" ? "inXRange" : "inYRange";
    let a = !1;
    return (
        Ys(s, e, t, (l, c, h) => {
            l[r] &&
                l[r](t[e], n) &&
                (o.push({ element: l, datasetIndex: c, index: h }),
                (a = a || l.inRange(t.x, t.y, n)));
        }),
        i && !a ? [] : o
    );
}
const Uu = {
    evaluateInteractionItems: Ys,
    modes: {
        index(s, t, e, i) {
            const n = le(t, s);
            const o = e.axis || "x";
            const r = e.includeInvisible || !1;
            const a = e.intersect ? go(s, n, o, i, r) : po(s, n, o, !1, i, r);
            const l = [];
            return a.length
                ? (s.getSortedVisibleDatasetMetas().forEach((c) => {
                      const h = a[0].index;
                      const u = c.data[h];
                      u &&
                          !u.skip &&
                          l.push({
                              element: u,
                              datasetIndex: c.index,
                              index: h,
                          });
                  }),
                  l)
                : [];
        },
        dataset(s, t, e, i) {
            const n = le(t, s);
            const o = e.axis || "xy";
            const r = e.includeInvisible || !1;
            let a = e.intersect ? go(s, n, o, i, r) : po(s, n, o, !1, i, r);
            if (a.length > 0) {
                const l = a[0].datasetIndex;
                const c = s.getDatasetMeta(l).data;
                a = [];
                for (let h = 0; h < c.length; ++h) {
                    a.push({ element: c[h], datasetIndex: l, index: h });
                }
            }
            return a;
        },
        point(s, t, e, i) {
            const n = le(t, s);
            const o = e.axis || "xy";
            const r = e.includeInvisible || !1;
            return go(s, n, o, i, r);
        },
        nearest(s, t, e, i) {
            const n = le(t, s);
            const o = e.axis || "xy";
            const r = e.includeInvisible || !1;
            return po(s, n, o, e.intersect, i, r);
        },
        x(s, t, e, i) {
            const n = le(t, s);
            return Da(s, n, "x", e.intersect, i);
        },
        y(s, t, e, i) {
            const n = le(t, s);
            return Da(s, n, "y", e.intersect, i);
        },
    },
};
const _l = ["left", "top", "right", "bottom"];
function Cs(s, t) {
    return s.filter((e) => e.pos === t);
}
function Ea(s, t) {
    return s.filter((e) => _l.indexOf(e.pos) === -1 && e.box.axis === t);
}
function Fs(s, t) {
    return s.sort((e, i) => {
        const n = t ? i : e;
        const o = t ? e : i;
        return n.weight === o.weight ? n.index - o.index : n.weight - o.weight;
    });
}
function Yu(s) {
    const t = [];
    let e;
    let i;
    let n;
    let o;
    let r;
    let a;
    for (e = 0, i = (s || []).length; e < i; ++e) {
        ((n = s[e]),
            ({
                position: o,
                options: { stack: r, stackWeight: a = 1 },
            } = n),
            t.push({
                index: e,
                box: n,
                pos: o,
                horizontal: n.isHorizontal(),
                weight: n.weight,
                stack: r && o + r,
                stackWeight: a,
            }));
    }
    return t;
}
function Zu(s) {
    const t = {};
    for (const e of s) {
        const { stack: i, pos: n, stackWeight: o } = e;
        if (!i || !_l.includes(n)) continue;
        const r = t[i] || (t[i] = { count: 0, placed: 0, weight: 0, size: 0 });
        (r.count++, (r.weight += o));
    }
    return t;
}
function qu(s, t) {
    const e = Zu(s);
    const { vBoxMaxWidth: i, hBoxMaxHeight: n } = t;
    let o;
    let r;
    let a;
    for (o = 0, r = s.length; o < r; ++o) {
        a = s[o];
        const { fullSize: l } = a.box;
        const c = e[a.stack];
        const h = c && a.stackWeight / c.weight;
        a.horizontal
            ? ((a.width = h ? h * i : l && t.availableWidth), (a.height = n))
            : ((a.width = i), (a.height = h ? h * n : l && t.availableHeight));
    }
    return e;
}
function Gu(s) {
    const t = Yu(s);
    const e = Fs(
        t.filter((c) => c.box.fullSize),
        !0,
    );
    const i = Fs(Cs(t, "left"), !0);
    const n = Fs(Cs(t, "right"));
    const o = Fs(Cs(t, "top"), !0);
    const r = Fs(Cs(t, "bottom"));
    const a = Ea(t, "x");
    const l = Ea(t, "y");
    return {
        fullSize: e,
        leftAndTop: i.concat(o),
        rightAndBottom: n.concat(l).concat(r).concat(a),
        chartArea: Cs(t, "chartArea"),
        vertical: i.concat(n).concat(l),
        horizontal: o.concat(r).concat(a),
    };
}
function Ia(s, t, e, i) {
    return Math.max(s[e], t[e]) + Math.max(s[i], t[i]);
}
function wl(s, t) {
    ((s.top = Math.max(s.top, t.top)),
        (s.left = Math.max(s.left, t.left)),
        (s.bottom = Math.max(s.bottom, t.bottom)),
        (s.right = Math.max(s.right, t.right)));
}
function Xu(s, t, e, i) {
    const { pos: n, box: o } = e;
    const r = s.maxPadding;
    if (!L(n)) {
        e.size && (s[n] -= e.size);
        const u = i[e.stack] || { size: 0, count: 1 };
        ((u.size = Math.max(u.size, e.horizontal ? o.height : o.width)),
            (e.size = u.size / u.count),
            (s[n] += e.size));
    }
    o.getPadding && wl(r, o.getPadding());
    const a = Math.max(0, t.outerWidth - Ia(r, s, "left", "right"));
    const l = Math.max(0, t.outerHeight - Ia(r, s, "top", "bottom"));
    const c = a !== s.w;
    const h = l !== s.h;
    return (
        (s.w = a),
        (s.h = l),
        e.horizontal ? { same: c, other: h } : { same: h, other: c }
    );
}
function Ku(s) {
    const t = s.maxPadding;
    function e(i) {
        const n = Math.max(t[i] - s[i], 0);
        return ((s[i] += n), n);
    }
    ((s.y += e("top")), (s.x += e("left")), e("right"), e("bottom"));
}
function Ju(s, t) {
    const e = t.maxPadding;
    function i(n) {
        const o = { left: 0, top: 0, right: 0, bottom: 0 };
        return (
            n.forEach((r) => {
                o[r] = Math.max(t[r], e[r]);
            }),
            o
        );
    }
    return i(s ? ["left", "right"] : ["top", "bottom"]);
}
function Ns(s, t, e, i) {
    const n = [];
    let o;
    let r;
    let a;
    let l;
    let c;
    let h;
    for (o = 0, r = s.length, c = 0; o < r; ++o) {
        ((a = s[o]),
            (l = a.box),
            l.update(a.width || t.w, a.height || t.h, Ju(a.horizontal, t)));
        const { same: u, other: d } = Xu(t, e, a, i);
        ((c |= u && n.length), (h = h || d), l.fullSize || n.push(a));
    }
    return (c && Ns(n, t, e, i)) || h;
}
function Zi(s, t, e, i, n) {
    ((s.top = e),
        (s.left = t),
        (s.right = t + i),
        (s.bottom = e + n),
        (s.width = i),
        (s.height = n));
}
function Ca(s, t, e, i) {
    const n = e.padding;
    let { x: o, y: r } = t;
    for (const a of s) {
        const l = a.box;
        const c = i[a.stack] || { count: 1, placed: 0, weight: 1 };
        const h = a.stackWeight / c.weight || 1;
        if (a.horizontal) {
            const u = t.w * h;
            const d = c.size || l.height;
            (Be(c.start) && (r = c.start),
                l.fullSize
                    ? Zi(l, n.left, r, e.outerWidth - n.right - n.left, d)
                    : Zi(l, t.left + c.placed, r, u, d),
                (c.start = r),
                (c.placed += u),
                (r = l.bottom));
        } else {
            const u = t.h * h;
            const d = c.size || l.width;
            (Be(c.start) && (o = c.start),
                l.fullSize
                    ? Zi(l, o, n.top, d, e.outerHeight - n.bottom - n.top)
                    : Zi(l, o, t.top + c.placed, d, u),
                (c.start = o),
                (c.placed += u),
                (o = l.right));
        }
    }
    ((t.x = o), (t.y = r));
}
const ct = {
    addBox(s, t) {
        (s.boxes || (s.boxes = []),
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
            s.boxes.push(t));
    },
    removeBox(s, t) {
        const e = s.boxes ? s.boxes.indexOf(t) : -1;
        e !== -1 && s.boxes.splice(e, 1);
    },
    configure(s, t, e) {
        ((t.fullSize = e.fullSize),
            (t.position = e.position),
            (t.weight = e.weight));
    },
    update(s, t, e, i) {
        if (!s) return;
        const n = at(s.options.layout.padding);
        const o = Math.max(t - n.width, 0);
        const r = Math.max(e - n.height, 0);
        const a = Gu(s.boxes);
        const l = a.vertical;
        const c = a.horizontal;
        V(s.boxes, (g) => {
            typeof g.beforeLayout === "function" && g.beforeLayout();
        });
        const h =
            l.reduce(
                (g, p) =>
                    p.box.options && p.box.options.display === !1 ? g : g + 1,
                0,
            ) || 1;
        const u = Object.freeze({
            outerWidth: t,
            outerHeight: e,
            padding: n,
            availableWidth: o,
            availableHeight: r,
            vBoxMaxWidth: o / 2 / h,
            hBoxMaxHeight: r / 2,
        });
        const d = Object.assign({}, n);
        wl(d, at(i));
        const f = Object.assign(
            { maxPadding: d, w: o, h: r, x: n.left, y: n.top },
            n,
        );
        const m = qu(l.concat(c), u);
        (Ns(a.fullSize, f, u, m),
            Ns(l, f, u, m),
            Ns(c, f, u, m) && Ns(l, f, u, m),
            Ku(f),
            Ca(a.leftAndTop, f, u, m),
            (f.x += f.w),
            (f.y += f.h),
            Ca(a.rightAndBottom, f, u, m),
            (s.chartArea = {
                left: f.left,
                top: f.top,
                right: f.left + f.w,
                bottom: f.top + f.h,
                height: f.h,
                width: f.w,
            }),
            V(a.chartArea, (g) => {
                const p = g.box;
                (Object.assign(p, s.chartArea),
                    p.update(f.w, f.h, {
                        left: 0,
                        top: 0,
                        right: 0,
                        bottom: 0,
                    }));
            }));
    },
};
const sn = class {
    acquireContext(t, e) {}
    releaseContext(t) {
        return !1;
    }

    addEventListener(t, e, i) {}
    removeEventListener(t, e, i) {}
    getDevicePixelRatio() {
        return 1;
    }

    getMaximumSize(t, e, i, n) {
        return (
            (e = Math.max(0, e || t.width)),
            (i = i || t.height),
            { width: e, height: Math.max(0, n ? Math.floor(e / n) : i) }
        );
    }

    isAttached(t) {
        return !0;
    }

    updateConfig(t) {}
};
const vo = class extends sn {
    acquireContext(t) {
        return (t && t.getContext && t.getContext("2d")) || null;
    }

    updateConfig(t) {
        t.options.animation = !1;
    }
};
const Qi = "$chartjs";
const Qu = {
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
const Fa = (s) => s === null || s === "";
function td(s, t) {
    const e = s.style;
    const i = s.getAttribute("height");
    const n = s.getAttribute("width");
    if (
        ((s[Qi] = {
            initial: {
                height: i,
                width: n,
                style: { display: e.display, height: e.height, width: e.width },
            },
        }),
        (e.display = e.display || "block"),
        (e.boxSizing = e.boxSizing || "border-box"),
        Fa(n))
    ) {
        const o = no(s, "width");
        o !== void 0 && (s.width = o);
    }
    if (Fa(i)) {
        if (s.style.height === "") s.height = s.width / (t || 2);
        else {
            const o = no(s, "height");
            o !== void 0 && (s.height = o);
        }
    }
    return s;
}
const Sl = ma ? { passive: !0 } : !1;
function ed(s, t, e) {
    s && s.addEventListener(t, e, Sl);
}
function sd(s, t, e) {
    s && s.canvas && s.canvas.removeEventListener(t, e, Sl);
}
function id(s, t) {
    const e = Qu[s.type] || s.type;
    const { x: i, y: n } = le(s, t);
    return {
        type: e,
        chart: t,
        native: s,
        x: i !== void 0 ? i : null,
        y: n !== void 0 ? n : null,
    };
}
function nn(s, t) {
    for (const e of s) if (e === t || e.contains(t)) return !0;
}
function nd(s, t, e) {
    const i = s.canvas;
    const n = new MutationObserver((o) => {
        let r = !1;
        for (const a of o) {
            ((r = r || nn(a.addedNodes, i)), (r = r && !nn(a.removedNodes, i)));
        }
        r && e();
    });
    return (n.observe(document, { childList: !0, subtree: !0 }), n);
}
function od(s, t, e) {
    const i = s.canvas;
    const n = new MutationObserver((o) => {
        let r = !1;
        for (const a of o) {
            ((r = r || nn(a.removedNodes, i)), (r = r && !nn(a.addedNodes, i)));
        }
        r && e();
    });
    return (n.observe(document, { childList: !0, subtree: !0 }), n);
}
const $s = new Map();
let Aa = 0;
function kl() {
    const s = window.devicePixelRatio;
    s !== Aa &&
        ((Aa = s),
        $s.forEach((t, e) => {
            e.currentDevicePixelRatio !== s && t();
        }));
}
function rd(s, t) {
    ($s.size || window.addEventListener("resize", kl), $s.set(s, t));
}
function ad(s) {
    ($s.delete(s), $s.size || window.removeEventListener("resize", kl));
}
function ld(s, t, e) {
    const i = s.canvas;
    const n = i && Ui(i);
    if (!n) return;
    const o = Un((a, l) => {
        const c = n.clientWidth;
        (e(a, l), c < n.clientWidth && e());
    }, window);
    const r = new ResizeObserver((a) => {
        const l = a[0];
        const c = l.contentRect.width;
        const h = l.contentRect.height;
        (c === 0 && h === 0) || o(c, h);
    });
    return (r.observe(n), rd(s, o), r);
}
function yo(s, t, e) {
    (e && e.disconnect(), t === "resize" && ad(s));
}
function cd(s, t, e) {
    const i = s.canvas;
    const n = Un((o) => {
        s.ctx !== null && e(id(o, s));
    }, s);
    return (ed(i, t, n), n);
}
const To = class extends sn {
    acquireContext(t, e) {
        const i = t && t.getContext && t.getContext("2d");
        return i && i.canvas === t ? (td(t, e), i) : null;
    }

    releaseContext(t) {
        const e = t.canvas;
        if (!e[Qi]) return !1;
        const i = e[Qi].initial;
        ["height", "width"].forEach((o) => {
            const r = i[o];
            A(r) ? e.removeAttribute(o) : e.setAttribute(o, r);
        });
        const n = i.style || {};
        return (
            Object.keys(n).forEach((o) => {
                e.style[o] = n[o];
            }),
            (e.width = e.width),
            delete e[Qi],
            !0
        );
    }

    addEventListener(t, e, i) {
        this.removeEventListener(t, e);
        const n = t.$proxies || (t.$proxies = {});
        const r = { attach: nd, detach: od, resize: ld }[e] || cd;
        n[e] = r(t, e, i);
    }

    removeEventListener(t, e) {
        const i = t.$proxies || (t.$proxies = {});
        const n = i[e];
        if (!n) return;
        ((({ attach: yo, detach: yo, resize: yo })[e] || sd)(t, e, n),
            (i[e] = void 0));
    }

    getDevicePixelRatio() {
        return window.devicePixelRatio;
    }

    getMaximumSize(t, e, i, n) {
        return fa(t, e, i, n);
    }

    isAttached(t) {
        const e = t && Ui(t);
        return !!(e && e.isConnected);
    }
};
function hd(s) {
    return !ji() ||
        (typeof OffscreenCanvas < "u" && s instanceof OffscreenCanvas)
        ? vo
        : To;
}
const yt = class {
    constructor() {
        S(this, "x");
        S(this, "y");
        S(this, "active", !1);
        S(this, "options");
        S(this, "$animations");
    }

    tooltipPosition(t) {
        const { x: e, y: i } = this.getProps(["x", "y"], t);
        return { x: e, y: i };
    }

    hasValue() {
        return we(this.x) && we(this.y);
    }

    getProps(t, e) {
        const i = this.$animations;
        if (!e || !i) return this;
        const n = {};
        return (
            t.forEach((o) => {
                n[o] = i[o] && i[o].active() ? i[o]._to : this[o];
            }),
            n
        );
    }
};
(S(yt, "defaults", {}), S(yt, "defaultRoutes"));
function ud(s, t) {
    const e = s.options.ticks;
    const i = dd(s);
    const n = Math.min(e.maxTicksLimit || i, i);
    const o = e.major.enabled ? md(t) : [];
    const r = o.length;
    const a = o[0];
    const l = o[r - 1];
    const c = [];
    if (r > n) return (gd(t, c, o, r / n), c);
    const h = fd(o, t, n);
    if (r > 0) {
        let u;
        let d;
        const f = r > 1 ? Math.round((l - a) / (r - 1)) : null;
        for (qi(t, c, h, A(f) ? 0 : a - f, a), u = 0, d = r - 1; u < d; u++) {
            qi(t, c, h, o[u], o[u + 1]);
        }
        return (qi(t, c, h, l, A(f) ? t.length : l + f), c);
    }
    return (qi(t, c, h), c);
}
function dd(s) {
    const t = s.options.offset;
    const e = s._tickSize();
    const i = s._length / e + (t ? 0 : 1);
    const n = s._maxLength / e;
    return Math.floor(Math.min(i, n));
}
function fd(s, t, e) {
    const i = pd(s);
    const n = t.length / e;
    if (!i) return Math.max(n, 1);
    const o = qr(i);
    for (let r = 0, a = o.length - 1; r < a; r++) {
        const l = o[r];
        if (l > n) return l;
    }
    return Math.max(n, 1);
}
function md(s) {
    const t = [];
    let e;
    let i;
    for (e = 0, i = s.length; e < i; e++) s[e].major && t.push(e);
    return t;
}
function gd(s, t, e, i) {
    let n = 0;
    let o = e[0];
    let r;
    for (i = Math.ceil(i), r = 0; r < s.length; r++) {
        r === o && (t.push(s[r]), n++, (o = e[n * i]));
    }
}
function qi(s, t, e, i, n) {
    const o = I(i, 0);
    const r = Math.min(I(n, s.length), s.length);
    let a = 0;
    let l;
    let c;
    let h;
    for (
        e = Math.ceil(e),
            n && ((l = n - i), (e = l / Math.floor(l / e))),
            h = o;
        h < 0;

    ) {
        (a++, (h = Math.round(o + a * e)));
    }
    for (c = Math.max(o, 0); c < r; c++) {
        c === h && (t.push(s[c]), a++, (h = Math.round(o + a * e)));
    }
}
function pd(s) {
    const t = s.length;
    let e;
    let i;
    if (t < 2) return !1;
    for (i = s[0], e = 1; e < t; ++e) if (s[e] - s[e - 1] !== i) return !1;
    return i;
}
const yd = (s) => (s === "left" ? "right" : s === "right" ? "left" : s);
const La = (s, t, e) => (t === "top" || t === "left" ? s[t] + e : s[t] - e);
const Pa = (s, t) => Math.min(t || s, s);
function Na(s, t) {
    const e = [];
    const i = s.length / t;
    const n = s.length;
    let o = 0;
    for (; o < n; o += i) e.push(s[Math.floor(o)]);
    return e;
}
function bd(s, t, e) {
    const i = s.ticks.length;
    const n = Math.min(t, i - 1);
    const o = s._startPixel;
    const r = s._endPixel;
    const a = 1e-6;
    let l = s.getPixelForTick(n);
    let c;
    if (
        !(
            e &&
            (i === 1
                ? (c = Math.max(l - o, r - l))
                : t === 0
                  ? (c = (s.getPixelForTick(1) - l) / 2)
                  : (c = (l - s.getPixelForTick(n - 1)) / 2),
            (l += n < t ? c : -c),
            l < o - a || l > r + a)
        )
    ) {
        return l;
    }
}
function xd(s, t) {
    V(s, (e) => {
        const i = e.gc;
        const n = i.length / 2;
        let o;
        if (n > t) {
            for (o = 0; o < n; ++o) delete e.data[i[o]];
            i.splice(0, n);
        }
    });
}
function As(s) {
    return s.drawTicks ? s.tickLength : 0;
}
function Ra(s, t) {
    if (!s.display) return 0;
    const e = Q(s.font, t);
    const i = at(s.padding);
    return ($(s.text) ? s.text.length : 1) * e.lineHeight + i.height;
}
function _d(s, t) {
    return Yt(s, { scale: t, type: "scale" });
}
function wd(s, t, e) {
    return Yt(s, { tick: e, index: t, type: "tick" });
}
function Sd(s, t, e) {
    let i = zi(s);
    return (((e && t !== "right") || (!e && t === "right")) && (i = yd(i)), i);
}
function kd(s, t, e, i) {
    const { top: n, left: o, bottom: r, right: a, chart: l } = s;
    const { chartArea: c, scales: h } = l;
    let u = 0;
    let d;
    let f;
    let m;
    const g = r - n;
    const p = a - o;
    if (s.isHorizontal()) {
        if (((f = rt(i, o, a)), L(e))) {
            const y = Object.keys(e)[0];
            const b = e[y];
            m = h[y].getPixelForValue(b) + g - t;
        } else {
            e === "center"
                ? (m = (c.bottom + c.top) / 2 + g - t)
                : (m = La(s, e, t));
        }
        d = a - o;
    } else {
        if (L(e)) {
            const y = Object.keys(e)[0];
            const b = e[y];
            f = h[y].getPixelForValue(b) - p + t;
        } else {
            e === "center"
                ? (f = (c.left + c.right) / 2 - p + t)
                : (f = La(s, e, t));
        }
        ((m = rt(i, r, n)), (u = e === "left" ? -X : X));
    }
    return { titleX: f, titleY: m, maxWidth: d, rotation: u };
}
const Ee = class s extends yt {
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
            _suggestedMin: i,
            _suggestedMax: n,
        } = this;
        return (
            (t = ut(t, Number.POSITIVE_INFINITY)),
            (e = ut(e, Number.NEGATIVE_INFINITY)),
            (i = ut(i, Number.POSITIVE_INFINITY)),
            (n = ut(n, Number.NEGATIVE_INFINITY)),
            { min: ut(t, i), max: ut(e, n), minDefined: Z(t), maxDefined: Z(e) }
        );
    }

    getMinMax(t) {
        let {
            min: e,
            max: i,
            minDefined: n,
            maxDefined: o,
        } = this.getUserBounds();
        let r;
        if (n && o) return { min: e, max: i };
        const a = this.getMatchingVisibleMetas();
        for (let l = 0, c = a.length; l < c; ++l) {
            ((r = a[l].controller.getMinMax(this, t)),
                n || (e = Math.min(e, r.min)),
                o || (i = Math.max(i, r.max)));
        }
        return (
            (e = o && e > i ? i : e),
            (i = n && e > i ? e : i),
            { min: ut(e, ut(i, e)), max: ut(i, ut(e, i)) }
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
        B(this.options.beforeUpdate, [this]);
    }

    update(t, e, i) {
        const { beginAtZero: n, grace: o, ticks: r } = this.options;
        const a = r.sampleSize;
        (this.beforeUpdate(),
            (this.maxWidth = t),
            (this.maxHeight = e),
            (this._margins = i =
                Object.assign({ left: 0, right: 0, top: 0, bottom: 0 }, i)),
            (this.ticks = null),
            (this._labelSizes = null),
            (this._gridLineItems = null),
            (this._labelItems = null),
            this.beforeSetDimensions(),
            this.setDimensions(),
            this.afterSetDimensions(),
            (this._maxLength = this.isHorizontal()
                ? this.width + i.left + i.right
                : this.height + i.top + i.bottom),
            this._dataLimitsCached ||
                (this.beforeDataLimits(),
                this.determineDataLimits(),
                this.afterDataLimits(),
                (this._range = aa(this, o, n)),
                (this._dataLimitsCached = !0)),
            this.beforeBuildTicks(),
            (this.ticks = this.buildTicks() || []),
            this.afterBuildTicks());
        const l = a < this.ticks.length;
        (this._convertTicksToLabels(l ? Na(this.ticks, a) : this.ticks),
            this.configure(),
            this.beforeCalculateLabelRotation(),
            this.calculateLabelRotation(),
            this.afterCalculateLabelRotation(),
            r.display &&
                (r.autoSkip || r.source === "auto") &&
                ((this.ticks = ud(this, this.ticks)),
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
        let i;
        (this.isHorizontal()
            ? ((e = this.left), (i = this.right))
            : ((e = this.top), (i = this.bottom), (t = !t)),
            (this._startPixel = e),
            (this._endPixel = i),
            (this._reversePixels = t),
            (this._length = i - e),
            (this._alignToPixels = this.options.alignToPixels));
    }

    afterUpdate() {
        B(this.options.afterUpdate, [this]);
    }

    beforeSetDimensions() {
        B(this.options.beforeSetDimensions, [this]);
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
        B(this.options.afterSetDimensions, [this]);
    }

    _callHooks(t) {
        (this.chart.notifyPlugins(t, this.getContext()),
            B(this.options[t], [this]));
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
        B(this.options.beforeTickToLabelConversion, [this]);
    }

    generateTickLabels(t) {
        const e = this.options.ticks;
        let i;
        let n;
        let o;
        for (i = 0, n = t.length; i < n; i++) {
            ((o = t[i]), (o.label = B(e.callback, [o.value, i, t], this)));
        }
    }

    afterTickToLabelConversion() {
        B(this.options.afterTickToLabelConversion, [this]);
    }

    beforeCalculateLabelRotation() {
        B(this.options.beforeCalculateLabelRotation, [this]);
    }

    calculateLabelRotation() {
        const t = this.options;
        const e = t.ticks;
        const i = Pa(this.ticks.length, t.ticks.maxTicksLimit);
        const n = e.minRotation || 0;
        const o = e.maxRotation;
        let r = n;
        let a;
        let l;
        let c;
        if (
            !this._isVisible() ||
            !e.display ||
            n >= o ||
            i <= 1 ||
            !this.isHorizontal()
        ) {
            this.labelRotation = n;
            return;
        }
        const h = this._getLabelSizes();
        const u = h.widest.width;
        const d = h.highest.height;
        const f = tt(this.chart.width - u, 0, this.maxWidth);
        ((a = t.offset ? this.maxWidth / i : f / (i - 1)),
            u + 6 > a &&
                ((a = f / (i - (t.offset ? 0.5 : 1))),
                (l =
                    this.maxHeight -
                    As(t.grid) -
                    e.padding -
                    Ra(t.title, this.chart.options.font)),
                (c = Math.sqrt(u * u + d * d)),
                (r = Ri(
                    Math.min(
                        Math.asin(tt((h.highest.height + 6) / a, -1, 1)),
                        Math.asin(tt(l / c, -1, 1)) -
                            Math.asin(tt(d / c, -1, 1)),
                    ),
                )),
                (r = Math.max(n, Math.min(o, r)))),
            (this.labelRotation = r));
    }

    afterCalculateLabelRotation() {
        B(this.options.afterCalculateLabelRotation, [this]);
    }

    afterAutoSkip() {}
    beforeFit() {
        B(this.options.beforeFit, [this]);
    }

    fit() {
        const t = { width: 0, height: 0 };
        const {
            chart: e,
            options: { ticks: i, title: n, grid: o },
        } = this;
        const r = this._isVisible();
        const a = this.isHorizontal();
        if (r) {
            const l = Ra(n, e.options.font);
            if (
                (a
                    ? ((t.width = this.maxWidth), (t.height = As(o) + l))
                    : ((t.height = this.maxHeight), (t.width = As(o) + l)),
                i.display && this.ticks.length)
            ) {
                const {
                    first: c,
                    last: h,
                    widest: u,
                    highest: d,
                } = this._getLabelSizes();
                const f = i.padding * 2;
                const m = wt(this.labelRotation);
                const g = Math.cos(m);
                const p = Math.sin(m);
                if (a) {
                    const y = i.mirror ? 0 : p * u.width + g * d.height;
                    t.height = Math.min(this.maxHeight, t.height + y + f);
                } else {
                    const y = i.mirror ? 0 : g * u.width + p * d.height;
                    t.width = Math.min(this.maxWidth, t.width + y + f);
                }
                this._calculatePadding(c, h, p, g);
            }
        }
        (this._handleMargins(),
            a
                ? ((this.width = this._length =
                      e.width - this._margins.left - this._margins.right),
                  (this.height = t.height))
                : ((this.width = t.width),
                  (this.height = this._length =
                      e.height - this._margins.top - this._margins.bottom)));
    }

    _calculatePadding(t, e, i, n) {
        const {
            ticks: { align: o, padding: r },
            position: a,
        } = this.options;
        const l = this.labelRotation !== 0;
        const c = a !== "top" && this.axis === "x";
        if (this.isHorizontal()) {
            const h = this.getPixelForTick(0) - this.left;
            const u = this.right - this.getPixelForTick(this.ticks.length - 1);
            let d = 0;
            let f = 0;
            (l
                ? c
                    ? ((d = n * t.width), (f = i * e.height))
                    : ((d = i * t.height), (f = n * e.width))
                : o === "start"
                  ? (f = e.width)
                  : o === "end"
                    ? (d = t.width)
                    : o !== "inner" && ((d = t.width / 2), (f = e.width / 2)),
                (this.paddingLeft = Math.max(
                    ((d - h + r) * this.width) / (this.width - h),
                    0,
                )),
                (this.paddingRight = Math.max(
                    ((f - u + r) * this.width) / (this.width - u),
                    0,
                )));
        } else {
            let h = e.height / 2;
            let u = t.height / 2;
            (o === "start"
                ? ((h = 0), (u = t.height))
                : o === "end" && ((h = e.height), (u = 0)),
                (this.paddingTop = h + r),
                (this.paddingBottom = u + r));
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
        B(this.options.afterFit, [this]);
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
        let e, i;
        for (e = 0, i = t.length; e < i; e++) {
            A(t[e].label) && (t.splice(e, 1), i--, e--);
        }
        this.afterTickToLabelConversion();
    }

    _getLabelSizes() {
        let t = this._labelSizes;
        if (!t) {
            const e = this.options.ticks.sampleSize;
            let i = this.ticks;
            (e < i.length && (i = Na(i, e)),
                (this._labelSizes = t =
                    this._computeLabelSizes(
                        i,
                        i.length,
                        this.options.ticks.maxTicksLimit,
                    )));
        }
        return t;
    }

    _computeLabelSizes(t, e, i) {
        const { ctx: n, _longestTextCache: o } = this;
        const r = [];
        const a = [];
        const l = Math.floor(e / Pa(e, i));
        let c = 0;
        let h = 0;
        let u;
        let d;
        let f;
        let m;
        let g;
        let p;
        let y;
        let b;
        let _;
        let w;
        let x;
        for (u = 0; u < e; u += l) {
            if (
                ((m = t[u].label),
                (g = this._resolveTickFontOptions(u)),
                (n.font = p = g.string),
                (y = o[p] = o[p] || { data: {}, gc: [] }),
                (b = g.lineHeight),
                (_ = w = 0),
                !A(m) && !$(m))
            ) {
                ((_ = vs(n, y.data, y.gc, _, m)), (w = b));
            } else if ($(m)) {
                for (d = 0, f = m.length; d < f; ++d) {
                    ((x = m[d]),
                        !A(x) &&
                            !$(x) &&
                            ((_ = vs(n, y.data, y.gc, _, x)), (w += b)));
                }
            }
            (r.push(_), a.push(w), (c = Math.max(_, c)), (h = Math.max(w, h)));
        }
        xd(o, e);
        const k = r.indexOf(c);
        const M = a.indexOf(h);
        const v = (O) => ({ width: r[O] || 0, height: a[O] || 0 });
        return {
            first: v(0),
            last: v(e - 1),
            widest: v(k),
            highest: v(M),
            widths: r,
            heights: a,
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
        return Xr(this._alignToPixels ? oe(this.chart, e, 0) : e);
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
            const i = e[t];
            return i.$context || (i.$context = wd(this.getContext(), t, i));
        }
        return (
            this.$context || (this.$context = _d(this.chart.getContext(), this))
        );
    }

    _tickSize() {
        const t = this.options.ticks;
        const e = wt(this.labelRotation);
        const i = Math.abs(Math.cos(e));
        const n = Math.abs(Math.sin(e));
        const o = this._getLabelSizes();
        const r = t.autoSkipPadding || 0;
        const a = o ? o.widest.width + r : 0;
        const l = o ? o.highest.height + r : 0;
        return this.isHorizontal()
            ? l * i > a * n
                ? a / i
                : l / n
            : l * n < a * i
              ? l / i
              : a / n;
    }

    _isVisible() {
        const t = this.options.display;
        return t !== "auto" ? !!t : this.getMatchingVisibleMetas().length > 0;
    }

    _computeGridLineItems(t) {
        const e = this.axis;
        const i = this.chart;
        const n = this.options;
        const { grid: o, position: r, border: a } = n;
        const l = o.offset;
        const c = this.isHorizontal();
        const u = this.ticks.length + (l ? 1 : 0);
        const d = As(o);
        const f = [];
        const m = a.setContext(this.getContext());
        const g = m.display ? m.width : 0;
        const p = g / 2;
        const y = function (Y) {
            return oe(i, Y, g);
        };
        let b;
        let _;
        let w;
        let x;
        let k;
        let M;
        let v;
        let O;
        let E;
        let C;
        let P;
        let nt;
        if (r === "top") {
            ((b = y(this.bottom)),
                (M = this.bottom - d),
                (O = b - p),
                (C = y(t.top) + p),
                (nt = t.bottom));
        } else if (r === "bottom") {
            ((b = y(this.top)),
                (C = t.top),
                (nt = y(t.bottom) - p),
                (M = b + p),
                (O = this.top + d));
        } else if (r === "left") {
            ((b = y(this.right)),
                (k = this.right - d),
                (v = b - p),
                (E = y(t.left) + p),
                (P = t.right));
        } else if (r === "right") {
            ((b = y(this.left)),
                (E = t.left),
                (P = y(t.right) - p),
                (k = b + p),
                (v = this.left + d));
        } else if (e === "x") {
            if (r === "center") b = y((t.top + t.bottom) / 2 + 0.5);
            else if (L(r)) {
                const Y = Object.keys(r)[0];
                const J = r[Y];
                b = y(this.chart.scales[Y].getPixelForValue(J));
            }
            ((C = t.top), (nt = t.bottom), (M = b + p), (O = M + d));
        } else if (e === "y") {
            if (r === "center") b = y((t.left + t.right) / 2);
            else if (L(r)) {
                const Y = Object.keys(r)[0];
                const J = r[Y];
                b = y(this.chart.scales[Y].getPixelForValue(J));
            }
            ((k = b - p), (v = k - d), (E = t.left), (P = t.right));
        }
        const gt = I(n.ticks.maxTicksLimit, u);
        const H = Math.max(1, Math.ceil(u / gt));
        for (_ = 0; _ < u; _ += H) {
            const Y = this.getContext(_);
            const J = o.setContext(Y);
            const Tt = a.setContext(Y);
            const lt = J.lineWidth;
            const Pe = J.color;
            const Mi = Tt.dash || [];
            const Ne = Tt.dashOffset;
            const xs = J.tickWidth;
            const pe = J.tickColor;
            const _s = J.tickBorderDash || [];
            const ye = J.tickBorderDashOffset;
            ((w = bd(this, _, l)),
                w !== void 0 &&
                    ((x = oe(i, w, lt)),
                    c ? (k = v = E = P = x) : (M = O = C = nt = x),
                    f.push({
                        tx1: k,
                        ty1: M,
                        tx2: v,
                        ty2: O,
                        x1: E,
                        y1: C,
                        x2: P,
                        y2: nt,
                        width: lt,
                        color: Pe,
                        borderDash: Mi,
                        borderDashOffset: Ne,
                        tickWidth: xs,
                        tickColor: pe,
                        tickBorderDash: _s,
                        tickBorderDashOffset: ye,
                    })));
        }
        return ((this._ticksLength = u), (this._borderValue = b), f);
    }

    _computeLabelItems(t) {
        const e = this.axis;
        const i = this.options;
        const { position: n, ticks: o } = i;
        const r = this.isHorizontal();
        const a = this.ticks;
        const { align: l, crossAlign: c, padding: h, mirror: u } = o;
        const d = As(i.grid);
        const f = d + h;
        const m = u ? -h : f;
        const g = -wt(this.labelRotation);
        const p = [];
        let y;
        let b;
        let _;
        let w;
        let x;
        let k;
        let M;
        let v;
        let O;
        let E;
        let C;
        let P;
        let nt = "middle";
        if (n === "top") {
            ((k = this.bottom - m), (M = this._getXAxisLabelAlignment()));
        } else if (n === "bottom") {
            ((k = this.top + m), (M = this._getXAxisLabelAlignment()));
        } else if (n === "left") {
            const H = this._getYAxisLabelAlignment(d);
            ((M = H.textAlign), (x = H.x));
        } else if (n === "right") {
            const H = this._getYAxisLabelAlignment(d);
            ((M = H.textAlign), (x = H.x));
        } else if (e === "x") {
            if (n === "center") k = (t.top + t.bottom) / 2 + f;
            else if (L(n)) {
                const H = Object.keys(n)[0];
                const Y = n[H];
                k = this.chart.scales[H].getPixelForValue(Y) + f;
            }
            M = this._getXAxisLabelAlignment();
        } else if (e === "y") {
            if (n === "center") x = (t.left + t.right) / 2 - f;
            else if (L(n)) {
                const H = Object.keys(n)[0];
                const Y = n[H];
                x = this.chart.scales[H].getPixelForValue(Y);
            }
            M = this._getYAxisLabelAlignment(d).textAlign;
        }
        e === "y" &&
            (l === "start" ? (nt = "top") : l === "end" && (nt = "bottom"));
        const gt = this._getLabelSizes();
        for (y = 0, b = a.length; y < b; ++y) {
            ((_ = a[y]), (w = _.label));
            const H = o.setContext(this.getContext(y));
            ((v = this.getPixelForTick(y) + o.labelOffset),
                (O = this._resolveTickFontOptions(y)),
                (E = O.lineHeight),
                (C = $(w) ? w.length : 1));
            const Y = C / 2;
            const J = H.color;
            const Tt = H.textStrokeColor;
            const lt = H.textStrokeWidth;
            let Pe = M;
            r
                ? ((x = v),
                  M === "inner" &&
                      (y === b - 1
                          ? (Pe = this.options.reverse ? "left" : "right")
                          : y === 0
                            ? (Pe = this.options.reverse ? "right" : "left")
                            : (Pe = "center")),
                  n === "top"
                      ? c === "near" || g !== 0
                          ? (P = -C * E + E / 2)
                          : c === "center"
                            ? (P = -gt.highest.height / 2 - Y * E + E)
                            : (P = -gt.highest.height + E / 2)
                      : c === "near" || g !== 0
                        ? (P = E / 2)
                        : c === "center"
                          ? (P = gt.highest.height / 2 - Y * E)
                          : (P = gt.highest.height - C * E),
                  u && (P *= -1),
                  g !== 0 &&
                      !H.showLabelBackdrop &&
                      (x += (E / 2) * Math.sin(g)))
                : ((k = v), (P = ((1 - C) * E) / 2));
            let Mi;
            if (H.showLabelBackdrop) {
                const Ne = at(H.backdropPadding);
                const xs = gt.heights[y];
                const pe = gt.widths[y];
                let _s = P - Ne.top;
                let ye = 0 - Ne.left;
                switch (nt) {
                    case "middle":
                        _s -= xs / 2;
                        break;
                    case "bottom":
                        _s -= xs;
                        break;
                }
                switch (M) {
                    case "center":
                        ye -= pe / 2;
                        break;
                    case "right":
                        ye -= pe;
                        break;
                    case "inner":
                        y === b - 1 ? (ye -= pe) : y > 0 && (ye -= pe / 2);
                        break;
                }
                Mi = {
                    left: ye,
                    top: _s,
                    width: pe + Ne.width,
                    height: xs + Ne.height,
                    color: H.backdropColor,
                };
            }
            p.push({
                label: w,
                font: O,
                textOffset: P,
                options: {
                    rotation: g,
                    color: J,
                    strokeColor: Tt,
                    strokeWidth: lt,
                    textAlign: Pe,
                    textBaseline: nt,
                    translation: [x, k],
                    backdrop: Mi,
                },
            });
        }
        return p;
    }

    _getXAxisLabelAlignment() {
        const { position: t, ticks: e } = this.options;
        if (-wt(this.labelRotation)) return t === "top" ? "left" : "right";
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
            ticks: { crossAlign: i, mirror: n, padding: o },
        } = this.options;
        const r = this._getLabelSizes();
        const a = t + o;
        const l = r.widest.width;
        let c;
        let h;
        return (
            e === "left"
                ? n
                    ? ((h = this.right + o),
                      i === "near"
                          ? (c = "left")
                          : i === "center"
                            ? ((c = "center"), (h += l / 2))
                            : ((c = "right"), (h += l)))
                    : ((h = this.right - a),
                      i === "near"
                          ? (c = "right")
                          : i === "center"
                            ? ((c = "center"), (h -= l / 2))
                            : ((c = "left"), (h = this.left)))
                : e === "right"
                  ? n
                      ? ((h = this.left + o),
                        i === "near"
                            ? (c = "right")
                            : i === "center"
                              ? ((c = "center"), (h -= l / 2))
                              : ((c = "left"), (h -= l)))
                      : ((h = this.left + a),
                        i === "near"
                            ? (c = "left")
                            : i === "center"
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
            left: i,
            top: n,
            width: o,
            height: r,
        } = this;
        e && (t.save(), (t.fillStyle = e), t.fillRect(i, n, o, r), t.restore());
    }

    getLineWidthForValue(t) {
        const e = this.options.grid;
        if (!this._isVisible() || !e.display) return 0;
        const n = this.ticks.findIndex((o) => o.value === t);
        return n >= 0 ? e.setContext(this.getContext(n)).lineWidth : 0;
    }

    drawGrid(t) {
        const e = this.options.grid;
        const i = this.ctx;
        const n =
            this._gridLineItems ||
            (this._gridLineItems = this._computeGridLineItems(t));
        let o;
        let r;
        const a = (l, c, h) => {
            !h.width ||
                !h.color ||
                (i.save(),
                (i.lineWidth = h.width),
                (i.strokeStyle = h.color),
                i.setLineDash(h.borderDash || []),
                (i.lineDashOffset = h.borderDashOffset),
                i.beginPath(),
                i.moveTo(l.x, l.y),
                i.lineTo(c.x, c.y),
                i.stroke(),
                i.restore());
        };
        if (e.display) {
            for (o = 0, r = n.length; o < r; ++o) {
                const l = n[o];
                (e.drawOnChartArea &&
                    a({ x: l.x1, y: l.y1 }, { x: l.x2, y: l.y2 }, l),
                    e.drawTicks &&
                        a(
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
            options: { border: i, grid: n },
        } = this;
        const o = i.setContext(this.getContext());
        const r = i.display ? o.width : 0;
        if (!r) return;
        const a = n.setContext(this.getContext(0)).lineWidth;
        const l = this._borderValue;
        let c;
        let h;
        let u;
        let d;
        (this.isHorizontal()
            ? ((c = oe(t, this.left, r) - r / 2),
              (h = oe(t, this.right, a) + a / 2),
              (u = d = l))
            : ((u = oe(t, this.top, r) - r / 2),
              (d = oe(t, this.bottom, a) + a / 2),
              (c = h = l)),
            e.save(),
            (e.lineWidth = o.width),
            (e.strokeStyle = o.color),
            e.beginPath(),
            e.moveTo(c, u),
            e.lineTo(h, d),
            e.stroke(),
            e.restore());
    }

    drawLabels(t) {
        if (!this.options.ticks.display) return;
        const i = this.ctx;
        const n = this._computeLabelArea();
        n && Ds(i, n);
        const o = this.getLabelItems(t);
        for (const r of o) {
            const a = r.options;
            const l = r.font;
            const c = r.label;
            const h = r.textOffset;
            re(i, c, 0, h, l, a);
        }
        n && Es(i);
    }

    drawTitle() {
        const {
            ctx: t,
            options: { position: e, title: i, reverse: n },
        } = this;
        if (!i.display) return;
        const o = Q(i.font);
        const r = at(i.padding);
        const a = i.align;
        let l = o.lineHeight / 2;
        e === "bottom" || e === "center" || L(e)
            ? ((l += r.bottom),
              $(i.text) && (l += o.lineHeight * (i.text.length - 1)))
            : (l += r.top);
        const {
            titleX: c,
            titleY: h,
            maxWidth: u,
            rotation: d,
        } = kd(this, l, e, a);
        re(t, i.text, 0, 0, o, {
            color: i.color,
            maxWidth: u,
            rotation: d,
            textAlign: Sd(a, e, n),
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
        const i = I(t.grid && t.grid.z, -1);
        const n = I(t.border && t.border.z, 0);
        return !this._isVisible() || this.draw !== s.prototype.draw
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
                      z: i,
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
        const i = this.axis + "AxisID";
        const n = [];
        let o;
        let r;
        for (o = 0, r = e.length; o < r; ++o) {
            const a = e[o];
            a[i] === this.id && (!t || a.type === t) && n.push(a);
        }
        return n;
    }

    _resolveTickFontOptions(t) {
        const e = this.options.ticks.setContext(this.getContext(t));
        return Q(e.font);
    }

    _maxDigits() {
        const t = this._resolveTickFontOptions(0).lineHeight;
        return (this.isHorizontal() ? this.width : this.height) / t;
    }
};
const ss = class {
    constructor(t, e, i) {
        ((this.type = t),
            (this.scope = e),
            (this.override = i),
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
        let i;
        Td(e) && (i = this.register(e));
        const n = this.items;
        const o = t.id;
        const r = this.scope + "." + o;
        if (!o) throw new Error("class does not have id: " + t);
        return (
            o in n ||
                ((n[o] = t),
                Md(t, r, i),
                this.override && U.override(t.id, t.overrides)),
            r
        );
    }

    get(t) {
        return this.items[t];
    }

    unregister(t) {
        const e = this.items;
        const i = t.id;
        const n = this.scope;
        (i in e && delete e[i],
            n && i in U[n] && (delete U[n][i], this.override && delete ne[i]));
    }
};
function Md(s, t, e) {
    const i = ze(Object.create(null), [
        e ? U.get(e) : {},
        U.get(t),
        s.defaults,
    ]);
    (U.set(t, i),
        s.defaultRoutes && vd(t, s.defaultRoutes),
        s.descriptors && U.describe(t, s.descriptors));
}
function vd(s, t) {
    Object.keys(t).forEach((e) => {
        const i = e.split(".");
        const n = i.pop();
        const o = [s].concat(i).join(".");
        const r = t[e].split(".");
        const a = r.pop();
        const l = r.join(".");
        U.route(o, n, l, a);
    });
}
function Td(s) {
    return "id" in s && "defaults" in s;
}
const Oo = class {
    constructor() {
        ((this.controllers = new ss(pt, "datasets", !0)),
            (this.elements = new ss(yt, "elements")),
            (this.plugins = new ss(Object, "plugins")),
            (this.scales = new ss(Ee, "scales")),
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

    _each(t, e, i) {
        [...e].forEach((n) => {
            const o = i || this._getRegistryForType(n);
            i || o.isForType(n) || (o === this.plugins && n.id)
                ? this._exec(t, o, n)
                : V(n, (r) => {
                      const a = i || this._getRegistryForType(r);
                      this._exec(t, a, r);
                  });
        });
    }

    _exec(t, e, i) {
        const n = Ni(t);
        (B(i["before" + n], [], i), e[t](i), B(i["after" + n], [], i));
    }

    _getRegistryForType(t) {
        for (let e = 0; e < this._typedRegistries.length; e++) {
            const i = this._typedRegistries[e];
            if (i.isForType(t)) return i;
        }
        return this.plugins;
    }

    _get(t, e, i) {
        const n = e.get(t);
        if (n === void 0) {
            throw new Error('"' + t + '" is not a registered ' + i + ".");
        }
        return n;
    }
};
const zt = new Oo();
const Do = class {
    constructor() {
        this._init = [];
    }

    notify(t, e, i, n) {
        e === "beforeInit" &&
            ((this._init = this._createDescriptors(t, !0)),
            this._notify(this._init, t, "install"));
        const o = n ? this._descriptors(t).filter(n) : this._descriptors(t);
        const r = this._notify(o, t, e, i);
        return (
            e === "afterDestroy" &&
                (this._notify(o, t, "stop"),
                this._notify(this._init, t, "uninstall")),
            r
        );
    }

    _notify(t, e, i, n) {
        n = n || {};
        for (const o of t) {
            const r = o.plugin;
            const a = r[i];
            const l = [e, n, o.options];
            if (B(a, l, r) === !1 && n.cancelable) return !1;
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
        const i = t && t.config;
        const n = I(i.options && i.options.plugins, {});
        const o = Od(i);
        return n === !1 && !e ? [] : Ed(t, o, n, e);
    }

    _notifyStateChanges(t) {
        const e = this._oldCache || [];
        const i = this._cache;
        const n = (o, r) =>
            o.filter((a) => !r.some((l) => a.plugin.id === l.plugin.id));
        (this._notify(n(e, i), t, "stop"), this._notify(n(i, e), t, "start"));
    }
};
function Od(s) {
    const t = {};
    const e = [];
    const i = Object.keys(zt.plugins.items);
    for (let o = 0; o < i.length; o++) e.push(zt.getPlugin(i[o]));
    const n = s.plugins || [];
    for (let o = 0; o < n.length; o++) {
        const r = n[o];
        e.indexOf(r) === -1 && (e.push(r), (t[r.id] = !0));
    }
    return { plugins: e, localIds: t };
}
function Dd(s, t) {
    return !t && s === !1 ? null : s === !0 ? {} : s;
}
function Ed(s, { plugins: t, localIds: e }, i, n) {
    const o = [];
    const r = s.getContext();
    for (const a of t) {
        const l = a.id;
        const c = Dd(i[l], n);
        c !== null &&
            o.push({
                plugin: a,
                options: Id(s.config, { plugin: a, local: e[l] }, c, r),
            });
    }
    return o;
}
function Id(s, { plugin: t, local: e }, i, n) {
    const o = s.pluginScopeKeys(t);
    const r = s.getOptionScopes(i, o);
    return (
        e && t.defaults && r.push(t.defaults),
        s.createResolver(r, n, [""], {
            scriptable: !1,
            indexable: !1,
            allKeys: !0,
        })
    );
}
function Eo(s, t) {
    const e = U.datasets[s] || {};
    return (
        ((t.datasets || {})[s] || {}).indexAxis ||
        t.indexAxis ||
        e.indexAxis ||
        "x"
    );
}
function Cd(s, t) {
    let e = s;
    return (
        s === "_index_"
            ? (e = t)
            : s === "_value_" && (e = t === "x" ? "y" : "x"),
        e
    );
}
function Fd(s, t) {
    return s === t ? "_index_" : "_value_";
}
function Wa(s) {
    if (s === "x" || s === "y" || s === "r") return s;
}
function Ad(s) {
    if (s === "top" || s === "bottom") return "x";
    if (s === "left" || s === "right") return "y";
}
function Io(s, ...t) {
    if (Wa(s)) return s;
    for (const e of t) {
        const i =
            e.axis ||
            Ad(e.position) ||
            (s.length > 1 && Wa(s[0].toLowerCase()));
        if (i) return i;
    }
    throw new Error(
        `Cannot determine type of '${s}' axis. Please provide 'axis' or 'position' option.`,
    );
}
function za(s, t, e) {
    if (e[t + "AxisID"] === s) return { axis: t };
}
function Ld(s, t) {
    if (t.data && t.data.datasets) {
        const e = t.data.datasets.filter(
            (i) => i.xAxisID === s || i.yAxisID === s,
        );
        if (e.length) return za(s, "x", e[0]) || za(s, "y", e[0]);
    }
    return {};
}
function Pd(s, t) {
    const e = ne[s.type] || { scales: {} };
    const i = t.scales || {};
    const n = Eo(s.type, t);
    const o = Object.create(null);
    return (
        Object.keys(i).forEach((r) => {
            const a = i[r];
            if (!L(a)) {
                return console.error(
                    `Invalid scale configuration for scale: ${r}`,
                );
            }
            if (a._proxy) {
                return console.warn(
                    `Ignoring resolver passed as options for scale: ${r}`,
                );
            }
            const l = Io(r, a, Ld(r, s), U.scales[a.type]);
            const c = Fd(l, n);
            const h = e.scales || {};
            o[r] = He(Object.create(null), [{ axis: l }, a, h[l], h[c]]);
        }),
        s.data.datasets.forEach((r) => {
            const a = r.type || s.type;
            const l = r.indexAxis || Eo(a, t);
            const h = (ne[a] || {}).scales || {};
            Object.keys(h).forEach((u) => {
                const d = Cd(u, l);
                const f = r[d + "AxisID"] || d;
                ((o[f] = o[f] || Object.create(null)),
                    He(o[f], [{ axis: d }, i[f], h[u]]));
            });
        }),
        Object.keys(o).forEach((r) => {
            const a = o[r];
            He(a, [U.scales[a.type], U.scale]);
        }),
        o
    );
}
function Ml(s) {
    const t = s.options || (s.options = {});
    ((t.plugins = I(t.plugins, {})), (t.scales = Pd(s, t)));
}
function vl(s) {
    return (
        (s = s || {}),
        (s.datasets = s.datasets || []),
        (s.labels = s.labels || []),
        s
    );
}
function Nd(s) {
    return ((s = s || {}), (s.data = vl(s.data)), Ml(s), s);
}
const Va = new Map();
const Tl = new Set();
function Gi(s, t) {
    let e = Va.get(s);
    return (e || ((e = t()), Va.set(s, e), Tl.add(e)), e);
}
const Ls = (s, t, e) => {
    const i = Ut(t, e);
    i !== void 0 && s.add(i);
};
const Co = class {
    constructor(t) {
        ((this._config = Nd(t)),
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
        this._config.data = vl(t);
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
        (this.clearCache(), Ml(t));
    }

    clearCache() {
        (this._scopeCache.clear(), this._resolverCache.clear());
    }

    datasetScopeKeys(t) {
        return Gi(t, () => [[`datasets.${t}`, ""]]);
    }

    datasetAnimationScopeKeys(t, e) {
        return Gi(`${t}.transition.${e}`, () => [
            [`datasets.${t}.transitions.${e}`, `transitions.${e}`],
            [`datasets.${t}`, ""],
        ]);
    }

    datasetElementScopeKeys(t, e) {
        return Gi(`${t}-${e}`, () => [
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
        const i = this.type;
        return Gi(`${i}-plugin-${e}`, () => [
            [`plugins.${e}`, ...(t.additionalOptionScopes || [])],
        ]);
    }

    _cachedScopes(t, e) {
        const i = this._scopeCache;
        let n = i.get(t);
        return ((!n || e) && ((n = new Map()), i.set(t, n)), n);
    }

    getOptionScopes(t, e, i) {
        const { options: n, type: o } = this;
        const r = this._cachedScopes(t, i);
        const a = r.get(e);
        if (a) return a;
        const l = new Set();
        e.forEach((h) => {
            (t && (l.add(t), h.forEach((u) => Ls(l, t, u))),
                h.forEach((u) => Ls(l, n, u)),
                h.forEach((u) => Ls(l, ne[o] || {}, u)),
                h.forEach((u) => Ls(l, U, u)),
                h.forEach((u) => Ls(l, Vi, u)));
        });
        const c = Array.from(l);
        return (
            c.length === 0 && c.push(Object.create(null)),
            Tl.has(e) && r.set(e, c),
            c
        );
    }

    chartOptionScopes() {
        const { options: t, type: e } = this;
        return [t, ne[e] || {}, U.datasets[e] || {}, { type: e }, U, Vi];
    }

    resolveNamedOptions(t, e, i, n = [""]) {
        const o = { $shared: !0 };
        const { resolver: r, subPrefixes: a } = Ha(this._resolverCache, t, n);
        let l = r;
        if (Wd(r, e)) {
            ((o.$shared = !1), (i = $t(i) ? i() : i));
            const c = this.createResolver(t, i, a);
            l = _e(r, i, c);
        }
        for (const c of e) o[c] = l[c];
        return o;
    }

    createResolver(t, e, i = [""], n) {
        const { resolver: o } = Ha(this._resolverCache, t, i);
        return L(e) ? _e(o, e, void 0, n) : o;
    }
};
function Ha(s, t, e) {
    let i = s.get(t);
    i || ((i = new Map()), s.set(t, i));
    const n = e.join();
    let o = i.get(n);
    return (
        o ||
            ((o = {
                resolver: $i(t, e),
                subPrefixes: e.filter(
                    (a) => !a.toLowerCase().includes("hover"),
                ),
            }),
            i.set(n, o)),
        o
    );
}
const Rd = (s) => L(s) && Object.getOwnPropertyNames(s).some((t) => $t(s[t]));
function Wd(s, t) {
    const { isScriptable: e, isIndexable: i } = Qn(s);
    for (const n of t) {
        const o = e(n);
        const r = i(n);
        const a = (r || o) && s[n];
        if ((o && ($t(a) || Rd(a))) || (r && $(a))) return !0;
    }
    return !1;
}
const zd = "4.5.0";
const Vd = ["top", "bottom", "left", "right", "chartArea"];
function Ba(s, t) {
    return s === "top" || s === "bottom" || (Vd.indexOf(s) === -1 && t === "x");
}
function $a(s, t) {
    return function (e, i) {
        return e[s] === i[s] ? e[t] - i[t] : e[s] - i[s];
    };
}
function ja(s) {
    const t = s.chart;
    const e = t.options.animation;
    (t.notifyPlugins("afterRender"), B(e && e.onComplete, [s], t));
}
function Hd(s) {
    const t = s.chart;
    const e = t.options.animation;
    B(e && e.onProgress, [s], t);
}
function Ol(s) {
    return (
        ji() && typeof s === "string"
            ? (s = document.getElementById(s))
            : s && s.length && (s = s[0]),
        s && s.canvas && (s = s.canvas),
        s
    );
}
const tn = {};
const Ua = (s) => {
    const t = Ol(s);
    return Object.values(tn)
        .filter((e) => e.canvas === t)
        .pop();
};
function Bd(s, t, e) {
    const i = Object.keys(s);
    for (const n of i) {
        const o = +n;
        if (o >= t) {
            const r = s[n];
            (delete s[n], (e > 0 || o > t) && (s[o + e] = r));
        }
    }
}
function $d(s, t, e, i) {
    return !e || s.type === "mouseout" ? null : i ? t : s;
}
const St = class {
    static register(...t) {
        (zt.add(...t), Ya());
    }

    static unregister(...t) {
        (zt.remove(...t), Ya());
    }

    constructor(t, e) {
        const i = (this.config = new Co(e));
        const n = Ol(t);
        const o = Ua(n);
        if (o) {
            throw new Error(
                "Canvas is already in use. Chart with ID '" +
                    o.id +
                    "' must be destroyed before the canvas with ID '" +
                    o.canvas.id +
                    "' can be reused.",
            );
        }
        const r = i.createResolver(i.chartOptionScopes(), this.getContext());
        ((this.platform = new (i.platform || hd(n))()),
            this.platform.updateConfig(i));
        const a = this.platform.acquireContext(n, r.aspectRatio);
        const l = a && a.canvas;
        const c = l && l.height;
        const h = l && l.width;
        if (
            ((this.id = jr()),
            (this.ctx = a),
            (this.canvas = l),
            (this.width = h),
            (this.height = c),
            (this._options = r),
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
            (this._plugins = new Do()),
            (this.$proxies = {}),
            (this._hiddenIndices = {}),
            (this.attached = !1),
            (this._animationsDisabled = void 0),
            (this.$context = void 0),
            (this._doResize = ea((u) => this.update(u), r.resizeDelay || 0)),
            (this._dataChanges = []),
            (tn[this.id] = this),
            !a || !l)
        ) {
            console.error(
                "Failed to create chart: can't acquire context from the given item",
            );
            return;
        }
        (Zt.listen(this, "complete", ja),
            Zt.listen(this, "progress", Hd),
            this._initialize(),
            this.attached && this.update());
    }

    get aspectRatio() {
        const {
            options: { aspectRatio: t, maintainAspectRatio: e },
            width: i,
            height: n,
            _aspectRatio: o,
        } = this;
        return A(t) ? (e && o ? o : n ? i / n : null) : t;
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
        return zt;
    }

    _initialize() {
        return (
            this.notifyPlugins("beforeInit"),
            this.options.responsive
                ? this.resize()
                : io(this, this.options.devicePixelRatio),
            this.bindEvents(),
            this.notifyPlugins("afterInit"),
            this
        );
    }

    clear() {
        return (Xn(this.canvas, this.ctx), this);
    }

    stop() {
        return (Zt.stop(this), this);
    }

    resize(t, e) {
        Zt.running(this)
            ? (this._resizeBeforeDraw = { width: t, height: e })
            : this._resize(t, e);
    }

    _resize(t, e) {
        const i = this.options;
        const n = this.canvas;
        const o = i.maintainAspectRatio && this.aspectRatio;
        const r = this.platform.getMaximumSize(n, t, e, o);
        const a = i.devicePixelRatio || this.platform.getDevicePixelRatio();
        const l = this.width ? "resize" : "attach";
        ((this.width = r.width),
            (this.height = r.height),
            (this._aspectRatio = this.aspectRatio),
            io(this, a, !0) &&
                (this.notifyPlugins("resize", { size: r }),
                B(i.onResize, [this, r], this),
                this.attached && this._doResize(l) && this.render()));
    }

    ensureScalesHaveIDs() {
        const e = this.options.scales || {};
        V(e, (i, n) => {
            i.id = n;
        });
    }

    buildOrUpdateScales() {
        const t = this.options;
        const e = t.scales;
        const i = this.scales;
        const n = Object.keys(i).reduce((r, a) => ((r[a] = !1), r), {});
        let o = [];
        (e &&
            (o = o.concat(
                Object.keys(e).map((r) => {
                    const a = e[r];
                    const l = Io(r, a);
                    const c = l === "r";
                    const h = l === "x";
                    return {
                        options: a,
                        dposition: c ? "chartArea" : h ? "bottom" : "left",
                        dtype: c ? "radialLinear" : h ? "category" : "linear",
                    };
                }),
            )),
            V(o, (r) => {
                const a = r.options;
                const l = a.id;
                const c = Io(l, a);
                const h = I(a.type, r.dtype);
                ((a.position === void 0 ||
                    Ba(a.position, c) !== Ba(r.dposition)) &&
                    (a.position = r.dposition),
                    (n[l] = !0));
                let u = null;
                if (l in i && i[l].type === h) u = i[l];
                else {
                    const d = zt.getScale(h);
                    ((u = new d({
                        id: l,
                        type: h,
                        ctx: this.ctx,
                        chart: this,
                    })),
                        (i[u.id] = u));
                }
                u.init(a, t);
            }),
            V(n, (r, a) => {
                r || delete i[a];
            }),
            V(i, (r) => {
                (ct.configure(this, r, r.options), ct.addBox(this, r));
            }));
    }

    _updateMetasets() {
        const t = this._metasets;
        const e = this.data.datasets.length;
        const i = t.length;
        if ((t.sort((n, o) => n.index - o.index), i > e)) {
            for (let n = e; n < i; ++n) this._destroyDatasetMeta(n);
            t.splice(e, i - e);
        }
        this._sortedMetasets = t.slice(0).sort($a("order", "index"));
    }

    _removeUnreferencedMetasets() {
        const {
            _metasets: t,
            data: { datasets: e },
        } = this;
        (t.length > e.length && delete this._stacks,
            t.forEach((i, n) => {
                e.filter((o) => o === i._dataset).length === 0 &&
                    this._destroyDatasetMeta(n);
            }));
    }

    buildOrUpdateControllers() {
        const t = [];
        const e = this.data.datasets;
        let i;
        let n;
        for (
            this._removeUnreferencedMetasets(), i = 0, n = e.length;
            i < n;
            i++
        ) {
            const o = e[i];
            let r = this.getDatasetMeta(i);
            const a = o.type || this.config.type;
            if (
                (r.type &&
                    r.type !== a &&
                    (this._destroyDatasetMeta(i), (r = this.getDatasetMeta(i))),
                (r.type = a),
                (r.indexAxis = o.indexAxis || Eo(a, this.options)),
                (r.order = o.order || 0),
                (r.index = i),
                (r.label = "" + o.label),
                (r.visible = this.isDatasetVisible(i)),
                r.controller)
            ) {
                (r.controller.updateIndex(i), r.controller.linkScales());
            } else {
                const l = zt.getController(a);
                const { datasetElementType: c, dataElementType: h } =
                    U.datasets[a];
                (Object.assign(l, {
                    dataElementType: zt.getElement(h),
                    datasetElementType: c && zt.getElement(c),
                }),
                    (r.controller = new l(this, i)),
                    t.push(r.controller));
            }
        }
        return (this._updateMetasets(), t);
    }

    _resetElements() {
        V(
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
        const i = (this._options = e.createResolver(
            e.chartOptionScopes(),
            this.getContext(),
        ));
        const n = (this._animationsDisabled = !i.animation);
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
        let r = 0;
        for (let c = 0, h = this.data.datasets.length; c < h; c++) {
            const { controller: u } = this.getDatasetMeta(c);
            const d = !n && o.indexOf(u) === -1;
            (u.buildOrUpdateElements(d),
                (r = Math.max(+u.getMaxOverflow(), r)));
        }
        ((r = this._minPadding = i.layout.autoPadding ? r : 0),
            this._updateLayout(r),
            n ||
                V(o, (c) => {
                    c.reset();
                }),
            this._updateDatasets(t),
            this.notifyPlugins("afterUpdate", { mode: t }),
            this._layers.sort($a("z", "_idx")));
        const { _active: a, _lastEvent: l } = this;
        (l
            ? this._eventHandler(l, !0)
            : a.length && this._updateHoverStyles(a, a, !0),
            this.render());
    }

    _updateScales() {
        (V(this.scales, (t) => {
            ct.removeBox(this, t);
        }),
            this.ensureScalesHaveIDs(),
            this.buildOrUpdateScales());
    }

    _checkEventBindings() {
        const t = this.options;
        const e = new Set(Object.keys(this._listeners));
        const i = new Set(t.events);
        (!Rn(e, i) || !!this._responsiveListeners !== t.responsive) &&
            (this.unbindEvents(), this.bindEvents());
    }

    _updateHiddenIndices() {
        const { _hiddenIndices: t } = this;
        const e = this._getUniformDataChanges() || [];
        for (const { method: i, start: n, count: o } of e) {
            const r = i === "_removeElements" ? -o : o;
            Bd(t, n, r);
        }
    }

    _getUniformDataChanges() {
        const t = this._dataChanges;
        if (!t || !t.length) return;
        this._dataChanges = [];
        const e = this.data.datasets.length;
        const i = (o) =>
            new Set(
                t
                    .filter((r) => r[0] === o)
                    .map((r, a) => a + "," + r.splice(1).join(",")),
            );
        const n = i(0);
        for (let o = 1; o < e; o++) if (!Rn(n, i(o))) return;
        return Array.from(n)
            .map((o) => o.split(","))
            .map((o) => ({ method: o[1], start: +o[2], count: +o[3] }));
    }

    _updateLayout(t) {
        if (this.notifyPlugins("beforeLayout", { cancelable: !0 }) === !1) {
            return;
        }
        ct.update(this, this.width, this.height, t);
        const e = this.chartArea;
        const i = e.width <= 0 || e.height <= 0;
        ((this._layers = []),
            V(
                this.boxes,
                (n) => {
                    (i && n.position === "chartArea") ||
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
            for (let e = 0, i = this.data.datasets.length; e < i; ++e) {
                this.getDatasetMeta(e).controller.configure();
            }
            for (let e = 0, i = this.data.datasets.length; e < i; ++e) {
                this._updateDataset(e, $t(t) ? t({ datasetIndex: e }) : t);
            }
            this.notifyPlugins("afterDatasetsUpdate", { mode: t });
        }
    }

    _updateDataset(t, e) {
        const i = this.getDatasetMeta(t);
        const n = { meta: i, index: t, mode: e, cancelable: !0 };
        this.notifyPlugins("beforeDatasetUpdate", n) !== !1 &&
            (i.controller._update(e),
            (n.cancelable = !1),
            this.notifyPlugins("afterDatasetUpdate", n));
    }

    render() {
        this.notifyPlugins("beforeRender", { cancelable: !0 }) !== !1 &&
            (Zt.has(this)
                ? this.attached && !Zt.running(this) && Zt.start(this)
                : (this.draw(), ja({ chart: this })));
    }

    draw() {
        let t;
        if (this._resizeBeforeDraw) {
            const { width: i, height: n } = this._resizeBeforeDraw;
            ((this._resizeBeforeDraw = null), this._resize(i, n));
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
        const i = [];
        let n;
        let o;
        for (n = 0, o = e.length; n < o; ++n) {
            const r = e[n];
            (!t || r.visible) && i.push(r);
        }
        return i;
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
        const i = { meta: t, index: t.index, cancelable: !0 };
        const n = co(this, t);
        this.notifyPlugins("beforeDatasetDraw", i) !== !1 &&
            (n && Ds(e, n),
            t.controller.draw(),
            n && Es(e),
            (i.cancelable = !1),
            this.notifyPlugins("afterDatasetDraw", i));
    }

    isPointInArea(t) {
        return Pt(t, this.chartArea, this._minPadding);
    }

    getElementsAtEventForMode(t, e, i, n) {
        const o = Uu.modes[e];
        return typeof o === "function" ? o(this, t, i, n) : [];
    }

    getDatasetMeta(t) {
        const e = this.data.datasets[t];
        const i = this._metasets;
        let n = i.filter((o) => o && o._dataset === e).pop();
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
                i.push(n)),
            n
        );
    }

    getContext() {
        return (
            this.$context ||
            (this.$context = Yt(null, { chart: this, type: "chart" }))
        );
    }

    getVisibleDatasetCount() {
        return this.getSortedVisibleDatasetMetas().length;
    }

    isDatasetVisible(t) {
        const e = this.data.datasets[t];
        if (!e) return !1;
        const i = this.getDatasetMeta(t);
        return typeof i.hidden === "boolean" ? !i.hidden : !e.hidden;
    }

    setDatasetVisibility(t, e) {
        const i = this.getDatasetMeta(t);
        i.hidden = !e;
    }

    toggleDataVisibility(t) {
        this._hiddenIndices[t] = !this._hiddenIndices[t];
    }

    getDataVisibility(t) {
        return !this._hiddenIndices[t];
    }

    _updateVisibility(t, e, i) {
        const n = i ? "show" : "hide";
        const o = this.getDatasetMeta(t);
        const r = o.controller._resolveAnimations(void 0, n);
        Be(e)
            ? ((o.data[e].hidden = !i), this.update())
            : (this.setDatasetVisibility(t, i),
              r.update(o, { visible: i }),
              this.update((a) => (a.datasetIndex === t ? n : void 0)));
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
            this.stop(), Zt.remove(this), t = 0, e = this.data.datasets.length;
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
                Xn(t, e),
                this.platform.releaseContext(e),
                (this.canvas = null),
                (this.ctx = null)),
            delete tn[this.id],
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
        const i = (o, r) => {
            (e.addEventListener(this, o, r), (t[o] = r));
        };
        const n = (o, r, a) => {
            ((o.offsetX = r), (o.offsetY = a), this._eventHandler(o));
        };
        V(this.options.events, (o) => i(o, n));
    }

    bindResponsiveEvents() {
        this._responsiveListeners || (this._responsiveListeners = {});
        const t = this._responsiveListeners;
        const e = this.platform;
        const i = (l, c) => {
            (e.addEventListener(this, l, c), (t[l] = c));
        };
        const n = (l, c) => {
            t[l] && (e.removeEventListener(this, l, c), delete t[l]);
        };
        const o = (l, c) => {
            this.canvas && this.resize(l, c);
        };
        let r;
        const a = () => {
            (n("attach", a),
                (this.attached = !0),
                this.resize(),
                i("resize", o),
                i("detach", r));
        };
        ((r = () => {
            ((this.attached = !1),
                n("resize", o),
                this._stop(),
                this._resize(0, 0),
                i("attach", a));
        }),
            e.isAttached(this.canvas) ? a() : r());
    }

    unbindEvents() {
        (V(this._listeners, (t, e) => {
            this.platform.removeEventListener(this, e, t);
        }),
            (this._listeners = {}),
            V(this._responsiveListeners, (t, e) => {
                this.platform.removeEventListener(this, e, t);
            }),
            (this._responsiveListeners = void 0));
    }

    updateHoverStyle(t, e, i) {
        const n = i ? "set" : "remove";
        let o;
        let r;
        let a;
        let l;
        for (
            e === "dataset" &&
                ((o = this.getDatasetMeta(t[0].datasetIndex)),
                o.controller["_" + n + "DatasetHoverStyle"]()),
                a = 0,
                l = t.length;
            a < l;
            ++a
        ) {
            r = t[a];
            const c = r && this.getDatasetMeta(r.datasetIndex).controller;
            c && c[n + "HoverStyle"](r.element, r.datasetIndex, r.index);
        }
    }

    getActiveElements() {
        return this._active || [];
    }

    setActiveElements(t) {
        const e = this._active || [];
        const i = t.map(({ datasetIndex: o, index: r }) => {
            const a = this.getDatasetMeta(o);
            if (!a) throw new Error("No dataset found at index " + o);
            return { datasetIndex: o, element: a.data[r], index: r };
        });
        !Ts(i, e) &&
            ((this._active = i),
            (this._lastEvent = null),
            this._updateHoverStyles(i, e));
    }

    notifyPlugins(t, e, i) {
        return this._plugins.notify(this, t, e, i);
    }

    isPluginEnabled(t) {
        return (
            this._plugins._cache.filter((e) => e.plugin.id === t).length === 1
        );
    }

    _updateHoverStyles(t, e, i) {
        const n = this.options.hover;
        const o = (l, c) =>
            l.filter(
                (h) =>
                    !c.some(
                        (u) =>
                            h.datasetIndex === u.datasetIndex &&
                            h.index === u.index,
                    ),
            );
        const r = o(e, t);
        const a = i ? t : o(t, e);
        (r.length && this.updateHoverStyle(r, n.mode, !1),
            a.length && n.mode && this.updateHoverStyle(a, n.mode, !0));
    }

    _eventHandler(t, e) {
        const i = {
            event: t,
            replay: e,
            cancelable: !0,
            inChartArea: this.isPointInArea(t),
        };
        const n = (r) =>
            (r.options.events || this.options.events).includes(t.native.type);
        if (this.notifyPlugins("beforeEvent", i, n) === !1) return;
        const o = this._handleEvent(t, e, i.inChartArea);
        return (
            (i.cancelable = !1),
            this.notifyPlugins("afterEvent", i, n),
            (o || i.changed) && this.render(),
            this
        );
    }

    _handleEvent(t, e, i) {
        const { _active: n = [], options: o } = this;
        const r = e;
        const a = this._getActiveElements(t, n, i, r);
        const l = Zr(t);
        const c = $d(t, this._lastEvent, i, l);
        i &&
            ((this._lastEvent = null),
            B(o.onHover, [t, a, this], this),
            l && B(o.onClick, [t, a, this], this));
        const h = !Ts(a, n);
        return (
            (h || e) && ((this._active = a), this._updateHoverStyles(a, n, e)),
            (this._lastEvent = c),
            h
        );
    }

    _getActiveElements(t, e, i, n) {
        if (t.type === "mouseout") return [];
        if (!i) return e;
        const o = this.options.hover;
        return this.getElementsAtEventForMode(t, o.mode, o, n);
    }
};
(S(St, "defaults", U),
    S(St, "instances", tn),
    S(St, "overrides", ne),
    S(St, "registry", zt),
    S(St, "version", zd),
    S(St, "getChart", Ua));
function Ya() {
    return V(St.instances, (s) => s._plugins.invalidate());
}
function jd(s, t, e) {
    const {
        startAngle: i,
        x: n,
        y: o,
        outerRadius: r,
        innerRadius: a,
        options: l,
    } = t;
    const { borderWidth: c, borderJoinStyle: h } = l;
    const u = Math.min(c / r, ot(i - e));
    if ((s.beginPath(), s.arc(n, o, r - c / 2, i + u / 2, e - u / 2), a > 0)) {
        const d = Math.min(c / a, ot(i - e));
        s.arc(n, o, a + c / 2, e - d / 2, i + d / 2, !0);
    } else {
        const d = Math.min(c / 2, r * ot(i - e));
        if (h === "round") s.arc(n, o, d, e - N / 2, i + N / 2, !0);
        else if (h === "bevel") {
            const f = 2 * d * d;
            const m = -f * Math.cos(e + N / 2) + n;
            const g = -f * Math.sin(e + N / 2) + o;
            const p = f * Math.cos(i + N / 2) + n;
            const y = f * Math.sin(i + N / 2) + o;
            (s.lineTo(m, g), s.lineTo(p, y));
        }
    }
    (s.closePath(),
        s.moveTo(0, 0),
        s.rect(0, 0, s.canvas.width, s.canvas.height),
        s.clip("evenodd"));
}
function Ud(s, t, e) {
    const {
        startAngle: i,
        pixelMargin: n,
        x: o,
        y: r,
        outerRadius: a,
        innerRadius: l,
    } = t;
    let c = n / a;
    (s.beginPath(),
        s.arc(o, r, a, i - c, e + c),
        l > n
            ? ((c = n / l), s.arc(o, r, l, e + c, i - c, !0))
            : s.arc(o, r, n, e + X, i - X),
        s.closePath(),
        s.clip());
}
function Yd(s) {
    return Bi(s, ["outerStart", "outerEnd", "innerStart", "innerEnd"]);
}
function Zd(s, t, e, i) {
    const n = Yd(s.options.borderRadius);
    const o = (e - t) / 2;
    const r = Math.min(o, (i * t) / 2);
    const a = (l) => {
        const c = ((e - Math.min(o, l)) * i) / 2;
        return tt(l, 0, Math.min(o, c));
    };
    return {
        outerStart: a(n.outerStart),
        outerEnd: a(n.outerEnd),
        innerStart: tt(n.innerStart, 0, r),
        innerEnd: tt(n.innerEnd, 0, r),
    };
}
function qe(s, t, e, i) {
    return { x: e + s * Math.cos(t), y: i + s * Math.sin(t) };
}
function on(s, t, e, i, n, o) {
    const { x: r, y: a, startAngle: l, pixelMargin: c, innerRadius: h } = t;
    const u = Math.max(t.outerRadius + i + e - c, 0);
    const d = h > 0 ? h + i + e + c : 0;
    let f = 0;
    const m = n - l;
    if (i) {
        const H = h > 0 ? h - i : 0;
        const Y = u > 0 ? u - i : 0;
        const J = (H + Y) / 2;
        const Tt = J !== 0 ? (m * J) / (J + i) : m;
        f = (m - Tt) / 2;
    }
    const g = Math.max(0.001, m * u - e / N) / u;
    const p = (m - g) / 2;
    const y = l + p + f;
    const b = n - p - f;
    const {
        outerStart: _,
        outerEnd: w,
        innerStart: x,
        innerEnd: k,
    } = Zd(t, d, u, b - y);
    const M = u - _;
    const v = u - w;
    const O = y + _ / M;
    const E = b - w / v;
    const C = d + x;
    const P = d + k;
    const nt = y + x / C;
    const gt = b - k / P;
    if ((s.beginPath(), o)) {
        const H = (O + E) / 2;
        if ((s.arc(r, a, u, O, H), s.arc(r, a, u, H, E), w > 0)) {
            const lt = qe(v, E, r, a);
            s.arc(lt.x, lt.y, w, E, b + X);
        }
        const Y = qe(P, b, r, a);
        if ((s.lineTo(Y.x, Y.y), k > 0)) {
            const lt = qe(P, gt, r, a);
            s.arc(lt.x, lt.y, k, b + X, gt + Math.PI);
        }
        const J = (b - k / d + (y + x / d)) / 2;
        if (
            (s.arc(r, a, d, b - k / d, J, !0),
            s.arc(r, a, d, J, y + x / d, !0),
            x > 0)
        ) {
            const lt = qe(C, nt, r, a);
            s.arc(lt.x, lt.y, x, nt + Math.PI, y - X);
        }
        const Tt = qe(M, y, r, a);
        if ((s.lineTo(Tt.x, Tt.y), _ > 0)) {
            const lt = qe(M, O, r, a);
            s.arc(lt.x, lt.y, _, y - X, O);
        }
    } else {
        s.moveTo(r, a);
        const H = Math.cos(O) * u + r;
        const Y = Math.sin(O) * u + a;
        s.lineTo(H, Y);
        const J = Math.cos(E) * u + r;
        const Tt = Math.sin(E) * u + a;
        s.lineTo(J, Tt);
    }
    s.closePath();
}
function qd(s, t, e, i, n) {
    const { fullCircles: o, startAngle: r, circumference: a } = t;
    let l = t.endAngle;
    if (o) {
        on(s, t, e, i, l, n);
        for (let c = 0; c < o; ++c) s.fill();
        isNaN(a) || (l = r + (a % j || j));
    }
    return (on(s, t, e, i, l, n), s.fill(), l);
}
function Gd(s, t, e, i, n) {
    const { fullCircles: o, startAngle: r, circumference: a, options: l } = t;
    const {
        borderWidth: c,
        borderJoinStyle: h,
        borderDash: u,
        borderDashOffset: d,
        borderRadius: f,
    } = l;
    const m = l.borderAlign === "inner";
    if (!c) return;
    (s.setLineDash(u || []),
        (s.lineDashOffset = d),
        m
            ? ((s.lineWidth = c * 2), (s.lineJoin = h || "round"))
            : ((s.lineWidth = c), (s.lineJoin = h || "bevel")));
    let g = t.endAngle;
    if (o) {
        on(s, t, e, i, g, n);
        for (let p = 0; p < o; ++p) s.stroke();
        isNaN(a) || (g = r + (a % j || j));
    }
    (m && Ud(s, t, g),
        l.selfJoin && g - r >= N && f === 0 && h !== "miter" && jd(s, t, g),
        o || (on(s, t, e, i, g, n), s.stroke()));
}
const ve = class extends yt {
    constructor(e) {
        super();
        S(this, "circumference");
        S(this, "endAngle");
        S(this, "fullCircles");
        S(this, "innerRadius");
        S(this, "outerRadius");
        S(this, "pixelMargin");
        S(this, "startAngle");
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

    inRange(e, i, n) {
        const o = this.getProps(["x", "y"], n);
        const { angle: r, distance: a } = Hn(o, { x: e, y: i });
        const {
            startAngle: l,
            endAngle: c,
            innerRadius: h,
            outerRadius: u,
            circumference: d,
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
        const m = I(d, c - l);
        const g = je(r, l, c) && l !== c;
        const p = m >= j || g;
        const y = Rt(a, h + f, u + f);
        return p && y;
    }

    getCenterPoint(e) {
        const {
            x: i,
            y: n,
            startAngle: o,
            endAngle: r,
            innerRadius: a,
            outerRadius: l,
        } = this.getProps(
            ["x", "y", "startAngle", "endAngle", "innerRadius", "outerRadius"],
            e,
        );
        const { offset: c, spacing: h } = this.options;
        const u = (o + r) / 2;
        const d = (a + l + h + c) / 2;
        return { x: i + Math.cos(u) * d, y: n + Math.sin(u) * d };
    }

    tooltipPosition(e) {
        return this.getCenterPoint(e);
    }

    draw(e) {
        const { options: i, circumference: n } = this;
        const o = (i.offset || 0) / 4;
        const r = (i.spacing || 0) / 2;
        const a = i.circular;
        if (
            ((this.pixelMargin = i.borderAlign === "inner" ? 0.33 : 0),
            (this.fullCircles = n > j ? Math.floor(n / j) : 0),
            n === 0 || this.innerRadius < 0 || this.outerRadius < 0)
        ) {
            return;
        }
        e.save();
        const l = (this.startAngle + this.endAngle) / 2;
        e.translate(Math.cos(l) * o, Math.sin(l) * o);
        const c = 1 - Math.sin(Math.min(N, n || 0));
        const h = o * c;
        ((e.fillStyle = i.backgroundColor),
            (e.strokeStyle = i.borderColor),
            qd(e, this, h, r, a),
            Gd(e, this, h, r, a),
            e.restore());
    }
};
(S(ve, "id", "arc"),
    S(ve, "defaults", {
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
    S(ve, "defaultRoutes", { backgroundColor: "backgroundColor" }),
    S(ve, "descriptors", {
        _scriptable: !0,
        _indexable: (e) => e !== "borderDash",
    }));
function Dl(s, t, e = t) {
    ((s.lineCap = I(e.borderCapStyle, t.borderCapStyle)),
        s.setLineDash(I(e.borderDash, t.borderDash)),
        (s.lineDashOffset = I(e.borderDashOffset, t.borderDashOffset)),
        (s.lineJoin = I(e.borderJoinStyle, t.borderJoinStyle)),
        (s.lineWidth = I(e.borderWidth, t.borderWidth)),
        (s.strokeStyle = I(e.borderColor, t.borderColor)));
}
function Xd(s, t, e) {
    s.lineTo(e.x, e.y);
}
function Kd(s) {
    return s.stepped
        ? oa
        : s.tension || s.cubicInterpolationMode === "monotone"
          ? ra
          : Xd;
}
function El(s, t, e = {}) {
    const i = s.length;
    const { start: n = 0, end: o = i - 1 } = e;
    const { start: r, end: a } = t;
    const l = Math.max(n, r);
    const c = Math.min(o, a);
    const h = (n < r && o < r) || (n > a && o > a);
    return {
        count: i,
        start: l,
        loop: t.loop,
        ilen: c < l && !h ? i + c - l : c - l,
    };
}
function Jd(s, t, e, i) {
    const { points: n, options: o } = t;
    const { count: r, start: a, loop: l, ilen: c } = El(n, e, i);
    const h = Kd(o);
    let { move: u = !0, reverse: d } = i || {};
    let f;
    let m;
    let g;
    for (f = 0; f <= c; ++f) {
        ((m = n[(a + (d ? c - f : f)) % r]),
            !m.skip &&
                (u ? (s.moveTo(m.x, m.y), (u = !1)) : h(s, g, m, d, o.stepped),
                (g = m)));
    }
    return (
        l && ((m = n[(a + (d ? c : 0)) % r]), h(s, g, m, d, o.stepped)),
        !!l
    );
}
function Qd(s, t, e, i) {
    const n = t.points;
    const { count: o, start: r, ilen: a } = El(n, e, i);
    const { move: l = !0, reverse: c } = i || {};
    let h = 0;
    let u = 0;
    let d;
    let f;
    let m;
    let g;
    let p;
    let y;
    const b = (w) => (r + (c ? a - w : w)) % o;
    const _ = () => {
        g !== p && (s.lineTo(h, p), s.lineTo(h, g), s.lineTo(h, y));
    };
    for (l && ((f = n[b(0)]), s.moveTo(f.x, f.y)), d = 0; d <= a; ++d) {
        if (((f = n[b(d)]), f.skip)) continue;
        const w = f.x;
        const x = f.y;
        const k = w | 0;
        (k === m
            ? (x < g ? (g = x) : x > p && (p = x), (h = (u * h + w) / ++u))
            : (_(), s.lineTo(w, x), (m = k), (u = 0), (g = p = x)),
            (y = x));
    }
    _();
}
function Fo(s) {
    const t = s.options;
    const e = t.borderDash && t.borderDash.length;
    return !s._decimated &&
        !s._loop &&
        !t.tension &&
        t.cubicInterpolationMode !== "monotone" &&
        !t.stepped &&
        !e
        ? Qd
        : Jd;
}
function tf(s) {
    return s.stepped
        ? ga
        : s.tension || s.cubicInterpolationMode === "monotone"
          ? pa
          : ie;
}
function ef(s, t, e, i) {
    let n = t._path;
    (n || ((n = t._path = new Path2D()), t.path(n, e, i) && n.closePath()),
        Dl(s, t.options),
        s.stroke(n));
}
function sf(s, t, e, i) {
    const { segments: n, options: o } = t;
    const r = Fo(t);
    for (const a of n) {
        (Dl(s, o, a.style),
            s.beginPath(),
            r(s, t, a, { start: e, end: e + i - 1 }) && s.closePath(),
            s.stroke());
    }
}
const nf = typeof Path2D === "function";
function of(s, t, e, i) {
    nf && !t.options.segment ? ef(s, t, e, i) : sf(s, t, e, i);
}
const Vt = class extends yt {
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
        const i = this.options;
        if (
            (i.tension || i.cubicInterpolationMode === "monotone") &&
            !i.stepped &&
            !this._pointsUpdated
        ) {
            const n = i.spanGaps ? this._loop : this._fullLoop;
            (da(this._points, i, t, n, e), (this._pointsUpdated = !0));
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
            this._segments || (this._segments = ba(this, this.options.segment))
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
        const i = t.length;
        return i && e[t[i - 1].end];
    }

    interpolate(t, e) {
        const i = this.options;
        const n = t[e];
        const o = this.points;
        const r = lo(this, { property: e, start: n, end: n });
        if (!r.length) return;
        const a = [];
        const l = tf(i);
        let c;
        let h;
        for (c = 0, h = r.length; c < h; ++c) {
            const { start: u, end: d } = r[c];
            const f = o[u];
            const m = o[d];
            if (f === m) {
                a.push(f);
                continue;
            }
            const g = Math.abs((n - f[e]) / (m[e] - f[e]));
            const p = l(f, m, g, i.stepped);
            ((p[e] = t[e]), a.push(p));
        }
        return a.length === 1 ? a[0] : a;
    }

    pathSegment(t, e, i) {
        return Fo(this)(t, this, e, i);
    }

    path(t, e, i) {
        const n = this.segments;
        const o = Fo(this);
        let r = this._loop;
        ((e = e || 0), (i = i || this.points.length - e));
        for (const a of n) r &= o(t, this, a, { start: e, end: e + i - 1 });
        return !!r;
    }

    draw(t, e, i, n) {
        const o = this.options || {};
        ((this.points || []).length &&
            o.borderWidth &&
            (t.save(), of(t, this, i, n), t.restore()),
            this.animated &&
                ((this._pointsUpdated = !1), (this._path = void 0)));
    }
};
(S(Vt, "id", "line"),
    S(Vt, "defaults", {
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
    S(Vt, "defaultRoutes", {
        backgroundColor: "backgroundColor",
        borderColor: "borderColor",
    }),
    S(Vt, "descriptors", {
        _scriptable: !0,
        _indexable: (t) => t !== "borderDash" && t !== "fill",
    }));
function Za(s, t, e, i) {
    const n = s.options;
    const { [e]: o } = s.getProps([e], i);
    return Math.abs(t - o) < n.radius + n.hitRadius;
}
const ts = class extends yt {
    constructor(e) {
        super();
        S(this, "parsed");
        S(this, "skip");
        S(this, "stop");
        ((this.options = void 0),
            (this.parsed = void 0),
            (this.skip = void 0),
            (this.stop = void 0),
            e && Object.assign(this, e));
    }

    inRange(e, i, n) {
        const o = this.options;
        const { x: r, y: a } = this.getProps(["x", "y"], n);
        return (
            Math.pow(e - r, 2) + Math.pow(i - a, 2) <
            Math.pow(o.hitRadius + o.radius, 2)
        );
    }

    inXRange(e, i) {
        return Za(this, e, "x", i);
    }

    inYRange(e, i) {
        return Za(this, e, "y", i);
    }

    getCenterPoint(e) {
        const { x: i, y: n } = this.getProps(["x", "y"], e);
        return { x: i, y: n };
    }

    size(e) {
        e = e || this.options || {};
        let i = e.radius || 0;
        i = Math.max(i, (i && e.hoverRadius) || 0);
        const n = (i && e.borderWidth) || 0;
        return (i + n) * 2;
    }

    draw(e, i) {
        const n = this.options;
        this.skip ||
            n.radius < 0.1 ||
            !Pt(this, i, this.size(n) / 2) ||
            ((e.strokeStyle = n.borderColor),
            (e.lineWidth = n.borderWidth),
            (e.fillStyle = n.backgroundColor),
            Hi(e, n, this.x, this.y));
    }

    getRange() {
        const e = this.options || {};
        return e.radius + e.hitRadius;
    }
};
(S(ts, "id", "point"),
    S(ts, "defaults", {
        borderWidth: 1,
        hitRadius: 1,
        hoverBorderWidth: 1,
        hoverRadius: 4,
        pointStyle: "circle",
        radius: 3,
        rotation: 0,
    }),
    S(ts, "defaultRoutes", {
        backgroundColor: "backgroundColor",
        borderColor: "borderColor",
    }));
function Il(s, t) {
    const {
        x: e,
        y: i,
        base: n,
        width: o,
        height: r,
    } = s.getProps(["x", "y", "base", "width", "height"], t);
    let a;
    let l;
    let c;
    let h;
    let u;
    return (
        s.horizontal
            ? ((u = r / 2),
              (a = Math.min(e, n)),
              (l = Math.max(e, n)),
              (c = i - u),
              (h = i + u))
            : ((u = o / 2),
              (a = e - u),
              (l = e + u),
              (c = Math.min(i, n)),
              (h = Math.max(i, n))),
        { left: a, top: c, right: l, bottom: h }
    );
}
function ce(s, t, e, i) {
    return s ? 0 : tt(t, e, i);
}
function rf(s, t, e) {
    const i = s.options.borderWidth;
    const n = s.borderSkipped;
    const o = Jn(i);
    return {
        t: ce(n.top, o.top, 0, e),
        r: ce(n.right, o.right, 0, t),
        b: ce(n.bottom, o.bottom, 0, e),
        l: ce(n.left, o.left, 0, t),
    };
}
function af(s, t, e) {
    const { enableBorderRadius: i } = s.getProps(["enableBorderRadius"]);
    const n = s.options.borderRadius;
    const o = ae(n);
    const r = Math.min(t, e);
    const a = s.borderSkipped;
    const l = i || L(n);
    return {
        topLeft: ce(!l || a.top || a.left, o.topLeft, 0, r),
        topRight: ce(!l || a.top || a.right, o.topRight, 0, r),
        bottomLeft: ce(!l || a.bottom || a.left, o.bottomLeft, 0, r),
        bottomRight: ce(!l || a.bottom || a.right, o.bottomRight, 0, r),
    };
}
function lf(s) {
    const t = Il(s);
    const e = t.right - t.left;
    const i = t.bottom - t.top;
    const n = rf(s, e / 2, i / 2);
    const o = af(s, e / 2, i / 2);
    return {
        outer: { x: t.left, y: t.top, w: e, h: i, radius: o },
        inner: {
            x: t.left + n.l,
            y: t.top + n.t,
            w: e - n.l - n.r,
            h: i - n.t - n.b,
            radius: {
                topLeft: Math.max(0, o.topLeft - Math.max(n.t, n.l)),
                topRight: Math.max(0, o.topRight - Math.max(n.t, n.r)),
                bottomLeft: Math.max(0, o.bottomLeft - Math.max(n.b, n.l)),
                bottomRight: Math.max(0, o.bottomRight - Math.max(n.b, n.r)),
            },
        },
    };
}
function bo(s, t, e, i) {
    const n = t === null;
    const o = e === null;
    const a = s && !(n && o) && Il(s, i);
    return a && (n || Rt(t, a.left, a.right)) && (o || Rt(e, a.top, a.bottom));
}
function cf(s) {
    return s.topLeft || s.topRight || s.bottomLeft || s.bottomRight;
}
function hf(s, t) {
    s.rect(t.x, t.y, t.w, t.h);
}
function xo(s, t, e = {}) {
    const i = s.x !== e.x ? -t : 0;
    const n = s.y !== e.y ? -t : 0;
    const o = (s.x + s.w !== e.x + e.w ? t : 0) - i;
    const r = (s.y + s.h !== e.y + e.h ? t : 0) - n;
    return { x: s.x + i, y: s.y + n, w: s.w + o, h: s.h + r, radius: s.radius };
}
const es = class extends yt {
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
            options: { borderColor: i, backgroundColor: n },
        } = this;
        const { inner: o, outer: r } = lf(this);
        const a = cf(r.radius) ? Ye : hf;
        (t.save(),
            (r.w !== o.w || r.h !== o.h) &&
                (t.beginPath(),
                a(t, xo(r, e, o)),
                t.clip(),
                a(t, xo(o, -e, r)),
                (t.fillStyle = i),
                t.fill("evenodd")),
            t.beginPath(),
            a(t, xo(o, e)),
            (t.fillStyle = n),
            t.fill(),
            t.restore());
    }

    inRange(t, e, i) {
        return bo(this, t, e, i);
    }

    inXRange(t, e) {
        return bo(this, t, null, e);
    }

    inYRange(t, e) {
        return bo(this, null, t, e);
    }

    getCenterPoint(t) {
        const {
            x: e,
            y: i,
            base: n,
            horizontal: o,
        } = this.getProps(["x", "y", "base", "horizontal"], t);
        return { x: o ? (e + n) / 2 : e, y: o ? i : (i + n) / 2 };
    }

    getRange(t) {
        return t === "x" ? this.width / 2 : this.height / 2;
    }
};
(S(es, "id", "bar"),
    S(es, "defaults", {
        borderSkipped: "start",
        borderWidth: 0,
        borderRadius: 0,
        inflateAmount: "auto",
        pointStyle: void 0,
    }),
    S(es, "defaultRoutes", {
        backgroundColor: "backgroundColor",
        borderColor: "borderColor",
    }));
const uf = Object.freeze({
    __proto__: null,
    ArcElement: ve,
    BarElement: es,
    LineElement: Vt,
    PointElement: ts,
});
const Ao = [
    "rgb(54, 162, 235)",
    "rgb(255, 99, 132)",
    "rgb(255, 159, 64)",
    "rgb(255, 205, 86)",
    "rgb(75, 192, 192)",
    "rgb(153, 102, 255)",
    "rgb(201, 203, 207)",
];
const qa = Ao.map((s) => s.replace("rgb(", "rgba(").replace(")", ", 0.5)"));
function Cl(s) {
    return Ao[s % Ao.length];
}
function Fl(s) {
    return qa[s % qa.length];
}
function df(s, t) {
    return ((s.borderColor = Cl(t)), (s.backgroundColor = Fl(t)), ++t);
}
function ff(s, t) {
    return ((s.backgroundColor = s.data.map(() => Cl(t++))), t);
}
function mf(s, t) {
    return ((s.backgroundColor = s.data.map(() => Fl(t++))), t);
}
function gf(s) {
    let t = 0;
    return (e, i) => {
        const n = s.getDatasetMeta(i).controller;
        n instanceof Gt
            ? (t = ff(e, t))
            : n instanceof Oe
              ? (t = mf(e, t))
              : n && (t = df(e, t));
    };
}
function Ga(s) {
    let t;
    for (t in s) if (s[t].borderColor || s[t].backgroundColor) return !0;
    return !1;
}
function pf(s) {
    return s && (s.borderColor || s.backgroundColor);
}
function yf() {
    return (
        U.borderColor !== "rgba(0,0,0,0.1)" ||
        U.backgroundColor !== "rgba(0,0,0,0.1)"
    );
}
const bf = {
    id: "colors",
    defaults: { enabled: !0, forceOverride: !1 },
    beforeLayout(s, t, e) {
        if (!e.enabled) return;
        const {
            data: { datasets: i },
            options: n,
        } = s.config;
        const { elements: o } = n;
        const r = Ga(i) || pf(n) || (o && Ga(o)) || yf();
        if (!e.forceOverride && r) return;
        const a = gf(s);
        i.forEach(a);
    },
};
function xf(s, t, e, i, n) {
    const o = n.samples || i;
    if (o >= e) return s.slice(t, t + e);
    const r = [];
    const a = (e - 2) / (o - 2);
    let l = 0;
    const c = t + e - 1;
    let h = t;
    let u;
    let d;
    let f;
    let m;
    let g;
    for (r[l++] = s[h], u = 0; u < o - 2; u++) {
        let p = 0;
        let y = 0;
        let b;
        const _ = Math.floor((u + 1) * a) + 1 + t;
        const w = Math.min(Math.floor((u + 2) * a) + 1, e) + t;
        const x = w - _;
        for (b = _; b < w; b++) ((p += s[b].x), (y += s[b].y));
        ((p /= x), (y /= x));
        const k = Math.floor(u * a) + 1 + t;
        const M = Math.min(Math.floor((u + 1) * a) + 1, e) + t;
        const { x: v, y: O } = s[h];
        for (f = m = -1, b = k; b < M; b++) {
            ((m =
                0.5 *
                Math.abs((v - p) * (s[b].y - O) - (v - s[b].x) * (y - O))),
                m > f && ((f = m), (d = s[b]), (g = b)));
        }
        ((r[l++] = d), (h = g));
    }
    return ((r[l++] = s[c]), r);
}
function _f(s, t, e, i) {
    let n = 0;
    let o = 0;
    let r;
    let a;
    let l;
    let c;
    let h;
    let u;
    let d;
    let f;
    let m;
    let g;
    const p = [];
    const y = t + e - 1;
    const b = s[t].x;
    const w = s[y].x - b;
    for (r = t; r < t + e; ++r) {
        ((a = s[r]), (l = ((a.x - b) / w) * i), (c = a.y));
        const x = l | 0;
        if (x === h) {
            (c < m ? ((m = c), (u = r)) : c > g && ((g = c), (d = r)),
                (n = (o * n + a.x) / ++o));
        } else {
            const k = r - 1;
            if (!A(u) && !A(d)) {
                const M = Math.min(u, d);
                const v = Math.max(u, d);
                (M !== f && M !== k && p.push({ ...s[M], x: n }),
                    v !== f && v !== k && p.push({ ...s[v], x: n }));
            }
            (r > 0 && k !== f && p.push(s[k]),
                p.push(a),
                (h = x),
                (o = 0),
                (m = g = c),
                (u = d = f = r));
        }
    }
    return p;
}
function Al(s) {
    if (s._decimated) {
        const t = s._data;
        (delete s._decimated,
            delete s._data,
            Object.defineProperty(s, "data", {
                configurable: !0,
                enumerable: !0,
                writable: !0,
                value: t,
            }));
    }
}
function Xa(s) {
    s.data.datasets.forEach((t) => {
        Al(t);
    });
}
function wf(s, t) {
    const e = t.length;
    let i = 0;
    let n;
    const { iScale: o } = s;
    const { min: r, max: a, minDefined: l, maxDefined: c } = o.getUserBounds();
    return (
        l && (i = tt(Lt(t, o.axis, r).lo, 0, e - 1)),
        c ? (n = tt(Lt(t, o.axis, a).hi + 1, i, e) - i) : (n = e - i),
        { start: i, count: n }
    );
}
const Sf = {
    id: "decimation",
    defaults: { algorithm: "min-max", enabled: !1 },
    beforeElementsUpdate: (s, t, e) => {
        if (!e.enabled) {
            Xa(s);
            return;
        }
        const i = s.width;
        s.data.datasets.forEach((n, o) => {
            const { _data: r, indexAxis: a } = n;
            const l = s.getDatasetMeta(o);
            const c = r || n.data;
            if (
                Ze([a, s.options.indexAxis]) === "y" ||
                !l.controller.supportsDecimation
            ) {
                return;
            }
            const h = s.scales[l.xAxisID];
            if (
                (h.type !== "linear" && h.type !== "time") ||
                s.options.parsing
            ) {
                return;
            }
            const { start: u, count: d } = wf(l, c);
            const f = e.threshold || 4 * i;
            if (d <= f) {
                Al(n);
                return;
            }
            A(r) &&
                ((n._data = c),
                delete n.data,
                Object.defineProperty(n, "data", {
                    configurable: !0,
                    enumerable: !0,
                    get: function () {
                        return this._decimated;
                    },
                    set: function (g) {
                        this._data = g;
                    },
                }));
            let m;
            switch (e.algorithm) {
                case "lttb":
                    m = xf(c, u, d, i, e);
                    break;
                case "min-max":
                    m = _f(c, u, d, i);
                    break;
                default:
                    throw new Error(
                        `Unsupported decimation algorithm '${e.algorithm}'`,
                    );
            }
            n._decimated = m;
        });
    },
    destroy(s) {
        Xa(s);
    },
};
function kf(s, t, e) {
    const i = s.segments;
    const n = s.points;
    const o = t.points;
    const r = [];
    for (const a of i) {
        let { start: l, end: c } = a;
        c = ln(l, c, n);
        const h = Lo(e, n[l], n[c], a.loop);
        if (!t.segments) {
            r.push({ source: a, target: h, start: n[l], end: n[c] });
            continue;
        }
        const u = lo(t, h);
        for (const d of u) {
            const f = Lo(e, o[d.start], o[d.end], d.loop);
            const m = ao(a, n, f);
            for (const g of m) {
                r.push({
                    source: g,
                    target: d,
                    start: { [e]: Ka(h, f, "start", Math.max) },
                    end: { [e]: Ka(h, f, "end", Math.min) },
                });
            }
        }
    }
    return r;
}
function Lo(s, t, e, i) {
    if (i) return;
    let n = t[s];
    let o = e[s];
    return (
        s === "angle" && ((n = ot(n)), (o = ot(o))),
        { property: s, start: n, end: o }
    );
}
function Mf(s, t) {
    const { x: e = null, y: i = null } = s || {};
    const n = t.points;
    const o = [];
    return (
        t.segments.forEach(({ start: r, end: a }) => {
            a = ln(r, a, n);
            const l = n[r];
            const c = n[a];
            i !== null
                ? (o.push({ x: l.x, y: i }), o.push({ x: c.x, y: i }))
                : e !== null &&
                  (o.push({ x: e, y: l.y }), o.push({ x: e, y: c.y }));
        }),
        o
    );
}
function ln(s, t, e) {
    for (; t > s; t--) {
        const i = e[t];
        if (!isNaN(i.x) && !isNaN(i.y)) break;
    }
    return t;
}
function Ka(s, t, e, i) {
    return s && t ? i(s[e], t[e]) : s ? s[e] : t ? t[e] : 0;
}
function Ll(s, t) {
    let e = [];
    let i = !1;
    return (
        $(s) ? ((i = !0), (e = s)) : (e = Mf(s, t)),
        e.length
            ? new Vt({
                  points: e,
                  options: { tension: 0 },
                  _loop: i,
                  _fullLoop: i,
              })
            : null
    );
}
function Ja(s) {
    return s && s.fill !== !1;
}
function vf(s, t, e) {
    let n = s[t].fill;
    const o = [t];
    let r;
    if (!e) return n;
    for (; n !== !1 && o.indexOf(n) === -1; ) {
        if (!Z(n)) return n;
        if (((r = s[n]), !r)) return !1;
        if (r.visible) return n;
        (o.push(n), (n = r.fill));
    }
    return !1;
}
function Tf(s, t, e) {
    const i = If(s);
    if (L(i)) return isNaN(i.value) ? !1 : i;
    const n = parseFloat(i);
    return Z(n) && Math.floor(n) === n
        ? Of(i[0], t, n, e)
        : ["origin", "start", "end", "stack", "shape"].indexOf(i) >= 0 && i;
}
function Of(s, t, e, i) {
    return (
        (s === "-" || s === "+") && (e = t + e),
        e === t || e < 0 || e >= i ? !1 : e
    );
}
function Df(s, t) {
    let e = null;
    return (
        s === "start"
            ? (e = t.bottom)
            : s === "end"
              ? (e = t.top)
              : L(s)
                ? (e = t.getPixelForValue(s.value))
                : t.getBasePixel && (e = t.getBasePixel()),
        e
    );
}
function Ef(s, t, e) {
    let i;
    return (
        s === "start"
            ? (i = e)
            : s === "end"
              ? (i = t.options.reverse ? t.min : t.max)
              : L(s)
                ? (i = s.value)
                : (i = t.getBaseValue()),
        i
    );
}
function If(s) {
    const t = s.options;
    const e = t.fill;
    let i = I(e && e.target, e);
    return (
        i === void 0 && (i = !!t.backgroundColor),
        i === !1 || i === null ? !1 : i === !0 ? "origin" : i
    );
}
function Cf(s) {
    const { scale: t, index: e, line: i } = s;
    const n = [];
    const o = i.segments;
    const r = i.points;
    const a = Ff(t, e);
    a.push(Ll({ x: null, y: t.bottom }, i));
    for (let l = 0; l < o.length; l++) {
        const c = o[l];
        for (let h = c.start; h <= c.end; h++) Af(n, r[h], a);
    }
    return new Vt({ points: n, options: {} });
}
function Ff(s, t) {
    const e = [];
    const i = s.getMatchingVisibleMetas("line");
    for (let n = 0; n < i.length; n++) {
        const o = i[n];
        if (o.index === t) break;
        o.hidden || e.unshift(o.dataset);
    }
    return e;
}
function Af(s, t, e) {
    const i = [];
    for (let n = 0; n < e.length; n++) {
        const o = e[n];
        const { first: r, last: a, point: l } = Lf(o, t, "x");
        if (!(!l || (r && a))) {
            if (r) i.unshift(l);
            else if ((s.push(l), !a)) break;
        }
    }
    s.push(...i);
}
function Lf(s, t, e) {
    const i = s.interpolate(t, e);
    if (!i) return {};
    const n = i[e];
    const o = s.segments;
    const r = s.points;
    let a = !1;
    let l = !1;
    for (let c = 0; c < o.length; c++) {
        const h = o[c];
        const u = r[h.start][e];
        const d = r[h.end][e];
        if (Rt(n, u, d)) {
            ((a = n === u), (l = n === d));
            break;
        }
    }
    return { first: a, last: l, point: i };
}
const rn = class {
    constructor(t) {
        ((this.x = t.x), (this.y = t.y), (this.radius = t.radius));
    }

    pathSegment(t, e, i) {
        const { x: n, y: o, radius: r } = this;
        return (
            (e = e || { start: 0, end: j }),
            t.arc(n, o, r, e.end, e.start, !0),
            !i.bounds
        );
    }

    interpolate(t) {
        const { x: e, y: i, radius: n } = this;
        const o = t.angle;
        return { x: e + Math.cos(o) * n, y: i + Math.sin(o) * n, angle: o };
    }
};
function Pf(s) {
    const { chart: t, fill: e, line: i } = s;
    if (Z(e)) return Nf(t, e);
    if (e === "stack") return Cf(s);
    if (e === "shape") return !0;
    const n = Rf(s);
    return n instanceof rn ? n : Ll(n, i);
}
function Nf(s, t) {
    const e = s.getDatasetMeta(t);
    return e && s.isDatasetVisible(t) ? e.dataset : null;
}
function Rf(s) {
    return (s.scale || {}).getPointPositionForValue ? zf(s) : Wf(s);
}
function Wf(s) {
    const { scale: t = {}, fill: e } = s;
    const i = Df(e, t);
    if (Z(i)) {
        const n = t.isHorizontal();
        return { x: n ? i : null, y: n ? null : i };
    }
    return null;
}
function zf(s) {
    const { scale: t, fill: e } = s;
    const i = t.options;
    const n = t.getLabels().length;
    const o = i.reverse ? t.max : t.min;
    const r = Ef(e, t, o);
    const a = [];
    if (i.grid.circular) {
        const l = t.getPointPositionForValue(0, o);
        return new rn({
            x: l.x,
            y: l.y,
            radius: t.getDistanceFromCenterForValue(r),
        });
    }
    for (let l = 0; l < n; ++l) a.push(t.getPointPositionForValue(l, r));
    return a;
}
function _o(s, t, e) {
    const i = Pf(t);
    const { chart: n, index: o, line: r, scale: a, axis: l } = t;
    const c = r.options;
    const h = c.fill;
    const u = c.backgroundColor;
    const { above: d = u, below: f = u } = h || {};
    const m = n.getDatasetMeta(o);
    const g = co(n, m);
    i &&
        r.points.length &&
        (Ds(s, e),
        Vf(s, {
            line: r,
            target: i,
            above: d,
            below: f,
            area: e,
            scale: a,
            axis: l,
            clip: g,
        }),
        Es(s));
}
function Vf(s, t) {
    const {
        line: e,
        target: i,
        above: n,
        below: o,
        area: r,
        scale: a,
        clip: l,
    } = t;
    const c = e._loop ? "angle" : t.axis;
    s.save();
    let h = o;
    (o !== n &&
        (c === "x"
            ? (Qa(s, i, r.top),
              wo(s, {
                  line: e,
                  target: i,
                  color: n,
                  scale: a,
                  property: c,
                  clip: l,
              }),
              s.restore(),
              s.save(),
              Qa(s, i, r.bottom))
            : c === "y" &&
              (tl(s, i, r.left),
              wo(s, {
                  line: e,
                  target: i,
                  color: o,
                  scale: a,
                  property: c,
                  clip: l,
              }),
              s.restore(),
              s.save(),
              tl(s, i, r.right),
              (h = n))),
        wo(s, { line: e, target: i, color: h, scale: a, property: c, clip: l }),
        s.restore());
}
function Qa(s, t, e) {
    const { segments: i, points: n } = t;
    let o = !0;
    let r = !1;
    s.beginPath();
    for (const a of i) {
        const { start: l, end: c } = a;
        const h = n[l];
        const u = n[ln(l, c, n)];
        (o
            ? (s.moveTo(h.x, h.y), (o = !1))
            : (s.lineTo(h.x, e), s.lineTo(h.x, h.y)),
            (r = !!t.pathSegment(s, a, { move: r })),
            r ? s.closePath() : s.lineTo(u.x, e));
    }
    (s.lineTo(t.first().x, e), s.closePath(), s.clip());
}
function tl(s, t, e) {
    const { segments: i, points: n } = t;
    let o = !0;
    let r = !1;
    s.beginPath();
    for (const a of i) {
        const { start: l, end: c } = a;
        const h = n[l];
        const u = n[ln(l, c, n)];
        (o
            ? (s.moveTo(h.x, h.y), (o = !1))
            : (s.lineTo(e, h.y), s.lineTo(h.x, h.y)),
            (r = !!t.pathSegment(s, a, { move: r })),
            r ? s.closePath() : s.lineTo(e, u.y));
    }
    (s.lineTo(e, t.first().y), s.closePath(), s.clip());
}
function wo(s, t) {
    const { line: e, target: i, property: n, color: o, scale: r, clip: a } = t;
    const l = kf(e, i, n);
    for (const { source: c, target: h, start: u, end: d } of l) {
        const { style: { backgroundColor: f = o } = {} } = c;
        const m = i !== !0;
        (s.save(),
            (s.fillStyle = f),
            Hf(s, r, a, m && Lo(n, u, d)),
            s.beginPath());
        const g = !!e.pathSegment(s, c);
        let p;
        if (m) {
            g ? s.closePath() : el(s, i, d, n);
            const y = !!i.pathSegment(s, h, { move: g, reverse: !0 });
            ((p = g && y), p || el(s, i, u, n));
        }
        (s.closePath(), s.fill(p ? "evenodd" : "nonzero"), s.restore());
    }
}
function Hf(s, t, e, i) {
    const n = t.chart.chartArea;
    const { property: o, start: r, end: a } = i || {};
    if (o === "x" || o === "y") {
        let l, c, h, u;
        (o === "x"
            ? ((l = r), (c = n.top), (h = a), (u = n.bottom))
            : ((l = n.left), (c = r), (h = n.right), (u = a)),
            s.beginPath(),
            e &&
                ((l = Math.max(l, e.left)),
                (h = Math.min(h, e.right)),
                (c = Math.max(c, e.top)),
                (u = Math.min(u, e.bottom))),
            s.rect(l, c, h - l, u - c),
            s.clip());
    }
}
function el(s, t, e, i) {
    const n = t.interpolate(e, i);
    n && s.lineTo(n.x, n.y);
}
const Bf = {
    id: "filler",
    afterDatasetsUpdate(s, t, e) {
        const i = (s.data.datasets || []).length;
        const n = [];
        let o;
        let r;
        let a;
        let l;
        for (r = 0; r < i; ++r) {
            ((o = s.getDatasetMeta(r)),
                (a = o.dataset),
                (l = null),
                a &&
                    a.options &&
                    a instanceof Vt &&
                    (l = {
                        visible: s.isDatasetVisible(r),
                        index: r,
                        fill: Tf(a, r, i),
                        chart: s,
                        axis: o.controller.options.indexAxis,
                        scale: o.vScale,
                        line: a,
                    }),
                (o.$filler = l),
                n.push(l));
        }
        for (r = 0; r < i; ++r) {
            ((l = n[r]),
                !(!l || l.fill === !1) && (l.fill = vf(n, r, e.propagate)));
        }
    },
    beforeDraw(s, t, e) {
        const i = e.drawTime === "beforeDraw";
        const n = s.getSortedVisibleDatasetMetas();
        const o = s.chartArea;
        for (let r = n.length - 1; r >= 0; --r) {
            const a = n[r].$filler;
            a &&
                (a.line.updateControlPoints(o, a.axis),
                i && a.fill && _o(s.ctx, a, o));
        }
    },
    beforeDatasetsDraw(s, t, e) {
        if (e.drawTime !== "beforeDatasetsDraw") return;
        const i = s.getSortedVisibleDatasetMetas();
        for (let n = i.length - 1; n >= 0; --n) {
            const o = i[n].$filler;
            Ja(o) && _o(s.ctx, o, s.chartArea);
        }
    },
    beforeDatasetDraw(s, t, e) {
        const i = t.meta.$filler;
        !Ja(i) ||
            e.drawTime !== "beforeDatasetDraw" ||
            _o(s.ctx, i, s.chartArea);
    },
    defaults: { propagate: !0, drawTime: "beforeDatasetDraw" },
};
const sl = (s, t) => {
    let { boxHeight: e = t, boxWidth: i = t } = s;
    return (
        s.usePointStyle &&
            ((e = Math.min(e, t)), (i = s.pointStyleWidth || Math.min(i, t))),
        { boxWidth: i, boxHeight: e, itemHeight: Math.max(t, e) }
    );
};
const $f = (s, t) =>
    s !== null &&
    t !== null &&
    s.datasetIndex === t.datasetIndex &&
    s.index === t.index;
const an = class extends yt {
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

    update(t, e, i) {
        ((this.maxWidth = t),
            (this.maxHeight = e),
            (this._margins = i),
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
        let e = B(t.generateLabels, [this.chart], this) || [];
        (t.filter && (e = e.filter((i) => t.filter(i, this.chart.data))),
            t.sort && (e = e.sort((i, n) => t.sort(i, n, this.chart.data))),
            this.options.reverse && e.reverse(),
            (this.legendItems = e));
    }

    fit() {
        const { options: t, ctx: e } = this;
        if (!t.display) {
            this.width = this.height = 0;
            return;
        }
        const i = t.labels;
        const n = Q(i.font);
        const o = n.size;
        const r = this._computeTitleHeight();
        const { boxWidth: a, itemHeight: l } = sl(i, o);
        let c;
        let h;
        ((e.font = n.string),
            this.isHorizontal()
                ? ((c = this.maxWidth), (h = this._fitRows(r, o, a, l) + 10))
                : ((h = this.maxHeight), (c = this._fitCols(r, n, a, l) + 10)),
            (this.width = Math.min(c, t.maxWidth || this.maxWidth)),
            (this.height = Math.min(h, t.maxHeight || this.maxHeight)));
    }

    _fitRows(t, e, i, n) {
        const {
            ctx: o,
            maxWidth: r,
            options: {
                labels: { padding: a },
            },
        } = this;
        const l = (this.legendHitBoxes = []);
        const c = (this.lineWidths = [0]);
        const h = n + a;
        let u = t;
        ((o.textAlign = "left"), (o.textBaseline = "middle"));
        let d = -1;
        let f = -h;
        return (
            this.legendItems.forEach((m, g) => {
                const p = i + e / 2 + o.measureText(m.text).width;
                ((g === 0 || c[c.length - 1] + p + 2 * a > r) &&
                    ((u += h),
                    (c[c.length - (g > 0 ? 0 : 1)] = 0),
                    (f += h),
                    d++),
                    (l[g] = { left: 0, top: f, row: d, width: p, height: n }),
                    (c[c.length - 1] += p + a));
            }),
            u
        );
    }

    _fitCols(t, e, i, n) {
        const {
            ctx: o,
            maxHeight: r,
            options: {
                labels: { padding: a },
            },
        } = this;
        const l = (this.legendHitBoxes = []);
        const c = (this.columnSizes = []);
        const h = r - t;
        let u = a;
        let d = 0;
        let f = 0;
        let m = 0;
        let g = 0;
        return (
            this.legendItems.forEach((p, y) => {
                const { itemWidth: b, itemHeight: _ } = jf(i, e, o, p, n);
                (y > 0 &&
                    f + _ + 2 * a > h &&
                    ((u += d + a),
                    c.push({ width: d, height: f }),
                    (m += d + a),
                    g++,
                    (d = f = 0)),
                    (l[y] = { left: m, top: f, col: g, width: b, height: _ }),
                    (d = Math.max(d, b)),
                    (f += _ + a));
            }),
            (u += d),
            c.push({ width: d, height: f }),
            u
        );
    }

    adjustHitBoxes() {
        if (!this.options.display) return;
        const t = this._computeTitleHeight();
        const {
            legendHitBoxes: e,
            options: {
                align: i,
                labels: { padding: n },
                rtl: o,
            },
        } = this;
        const r = Se(o, this.left, this.width);
        if (this.isHorizontal()) {
            let a = 0;
            let l = rt(i, this.left + n, this.right - this.lineWidths[a]);
            for (const c of e) {
                (a !== c.row &&
                    ((a = c.row),
                    (l = rt(
                        i,
                        this.left + n,
                        this.right - this.lineWidths[a],
                    ))),
                    (c.top += this.top + t + n),
                    (c.left = r.leftForLtr(r.x(l), c.width)),
                    (l += c.width + n));
            }
        } else {
            let a = 0;
            let l = rt(
                i,
                this.top + t + n,
                this.bottom - this.columnSizes[a].height,
            );
            for (const c of e) {
                (c.col !== a &&
                    ((a = c.col),
                    (l = rt(
                        i,
                        this.top + t + n,
                        this.bottom - this.columnSizes[a].height,
                    ))),
                    (c.top = l),
                    (c.left += this.left + n),
                    (c.left = r.leftForLtr(r.x(c.left), c.width)),
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
            (Ds(t, this), this._draw(), Es(t));
        }
    }

    _draw() {
        const { options: t, columnSizes: e, lineWidths: i, ctx: n } = this;
        const { align: o, labels: r } = t;
        const a = U.color;
        const l = Se(t.rtl, this.left, this.width);
        const c = Q(r.font);
        const { padding: h } = r;
        const u = c.size;
        const d = u / 2;
        let f;
        (this.drawTitle(),
            (n.textAlign = l.textAlign("left")),
            (n.textBaseline = "middle"),
            (n.lineWidth = 0.5),
            (n.font = c.string));
        const { boxWidth: m, boxHeight: g, itemHeight: p } = sl(r, u);
        const y = function (k, M, v) {
            if (isNaN(m) || m <= 0 || isNaN(g) || g < 0) return;
            n.save();
            const O = I(v.lineWidth, 1);
            if (
                ((n.fillStyle = I(v.fillStyle, a)),
                (n.lineCap = I(v.lineCap, "butt")),
                (n.lineDashOffset = I(v.lineDashOffset, 0)),
                (n.lineJoin = I(v.lineJoin, "miter")),
                (n.lineWidth = O),
                (n.strokeStyle = I(v.strokeStyle, a)),
                n.setLineDash(I(v.lineDash, [])),
                r.usePointStyle)
            ) {
                const E = {
                    radius: (g * Math.SQRT2) / 2,
                    pointStyle: v.pointStyle,
                    rotation: v.rotation,
                    borderWidth: O,
                };
                const C = l.xPlus(k, m / 2);
                const P = M + d;
                Kn(n, E, C, P, r.pointStyleWidth && m);
            } else {
                const E = M + Math.max((u - g) / 2, 0);
                const C = l.leftForLtr(k, m);
                const P = ae(v.borderRadius);
                (n.beginPath(),
                    Object.values(P).some((nt) => nt !== 0)
                        ? Ye(n, { x: C, y: E, w: m, h: g, radius: P })
                        : n.rect(C, E, m, g),
                    n.fill(),
                    O !== 0 && n.stroke());
            }
            n.restore();
        };
        const b = function (k, M, v) {
            re(n, v.text, k, M + p / 2, c, {
                strikethrough: v.hidden,
                textAlign: l.textAlign(v.textAlign),
            });
        };
        const _ = this.isHorizontal();
        const w = this._computeTitleHeight();
        (_
            ? (f = {
                  x: rt(o, this.left + h, this.right - i[0]),
                  y: this.top + h + w,
                  line: 0,
              })
            : (f = {
                  x: this.left + h,
                  y: rt(o, this.top + w + h, this.bottom - e[0].height),
                  line: 0,
              }),
            oo(this.ctx, t.textDirection));
        const x = p + h;
        (this.legendItems.forEach((k, M) => {
            ((n.strokeStyle = k.fontColor), (n.fillStyle = k.fontColor));
            const v = n.measureText(k.text).width;
            const O = l.textAlign(k.textAlign || (k.textAlign = r.textAlign));
            const E = m + d + v;
            let C = f.x;
            let P = f.y;
            (l.setWidth(this.width),
                _
                    ? M > 0 &&
                      C + E + h > this.right &&
                      ((P = f.y += x),
                      f.line++,
                      (C = f.x = rt(o, this.left + h, this.right - i[f.line])))
                    : M > 0 &&
                      P + x > this.bottom &&
                      ((C = f.x = C + e[f.line].width + h),
                      f.line++,
                      (P = f.y =
                          rt(
                              o,
                              this.top + w + h,
                              this.bottom - e[f.line].height,
                          ))));
            const nt = l.x(C);
            if (
                (y(nt, P, k),
                (C = sa(O, C + m + d, _ ? C + E : this.right, t.rtl)),
                b(l.x(C), P, k),
                _)
            ) {
                f.x += E + h;
            } else if (typeof k.text !== "string") {
                const gt = c.lineHeight;
                f.y += Pl(k, gt) + h;
            } else f.y += x;
        }),
            ro(this.ctx, t.textDirection));
    }

    drawTitle() {
        const t = this.options;
        const e = t.title;
        const i = Q(e.font);
        const n = at(e.padding);
        if (!e.display) return;
        const o = Se(t.rtl, this.left, this.width);
        const r = this.ctx;
        const a = e.position;
        const l = i.size / 2;
        const c = n.top + l;
        let h;
        let u = this.left;
        let d = this.width;
        if (this.isHorizontal()) {
            ((d = Math.max(...this.lineWidths)),
                (h = this.top + c),
                (u = rt(t.align, u, this.right - d)));
        } else {
            const m = this.columnSizes.reduce(
                (g, p) => Math.max(g, p.height),
                0,
            );
            h =
                c +
                rt(
                    t.align,
                    this.top,
                    this.bottom -
                        m -
                        t.labels.padding -
                        this._computeTitleHeight(),
                );
        }
        const f = rt(a, u, u + d);
        ((r.textAlign = o.textAlign(zi(a))),
            (r.textBaseline = "middle"),
            (r.strokeStyle = e.color),
            (r.fillStyle = e.color),
            (r.font = i.string),
            re(r, e.text, f, h, i));
    }

    _computeTitleHeight() {
        const t = this.options.title;
        const e = Q(t.font);
        const i = at(t.padding);
        return t.display ? e.lineHeight + i.height : 0;
    }

    _getLegendItemAt(t, e) {
        let i, n, o;
        if (Rt(t, this.left, this.right) && Rt(e, this.top, this.bottom)) {
            for (o = this.legendHitBoxes, i = 0; i < o.length; ++i) {
                if (
                    ((n = o[i]),
                    Rt(t, n.left, n.left + n.width) &&
                        Rt(e, n.top, n.top + n.height))
                ) {
                    return this.legendItems[i];
                }
            }
        }
        return null;
    }

    handleEvent(t) {
        const e = this.options;
        if (!Zf(t.type, e)) return;
        const i = this._getLegendItemAt(t.x, t.y);
        if (t.type === "mousemove" || t.type === "mouseout") {
            const n = this._hoveredItem;
            const o = $f(n, i);
            (n && !o && B(e.onLeave, [t, n, this], this),
                (this._hoveredItem = i),
                i && !o && B(e.onHover, [t, i, this], this));
        } else i && B(e.onClick, [t, i, this], this);
    }
};
function jf(s, t, e, i, n) {
    const o = Uf(i, s, t, e);
    const r = Yf(n, i, t.lineHeight);
    return { itemWidth: o, itemHeight: r };
}
function Uf(s, t, e, i) {
    let n = s.text;
    return (
        n &&
            typeof n !== "string" &&
            (n = n.reduce((o, r) => (o.length > r.length ? o : r))),
        t + e.size / 2 + i.measureText(n).width
    );
}
function Yf(s, t, e) {
    let i = s;
    return (typeof t.text !== "string" && (i = Pl(t, e)), i);
}
function Pl(s, t) {
    const e = s.text ? s.text.length : 0;
    return t * e;
}
function Zf(s, t) {
    return !!(
        ((s === "mousemove" || s === "mouseout") && (t.onHover || t.onLeave)) ||
        (t.onClick && (s === "click" || s === "mouseup"))
    );
}
const qf = {
    id: "legend",
    _element: an,
    start(s, t, e) {
        const i = (s.legend = new an({ ctx: s.ctx, options: e, chart: s }));
        (ct.configure(s, i, e), ct.addBox(s, i));
    },
    stop(s) {
        (ct.removeBox(s, s.legend), delete s.legend);
    },
    beforeUpdate(s, t, e) {
        const i = s.legend;
        (ct.configure(s, i, e), (i.options = e));
    },
    afterUpdate(s) {
        const t = s.legend;
        (t.buildLabels(), t.adjustHitBoxes());
    },
    afterEvent(s, t) {
        t.replay || s.legend.handleEvent(t.event);
    },
    defaults: {
        display: !0,
        position: "top",
        align: "center",
        fullSize: !0,
        reverse: !1,
        weight: 1e3,
        onClick(s, t, e) {
            const i = t.datasetIndex;
            const n = e.chart;
            n.isDatasetVisible(i)
                ? (n.hide(i), (t.hidden = !0))
                : (n.show(i), (t.hidden = !1));
        },
        onHover: null,
        onLeave: null,
        labels: {
            color: (s) => s.chart.options.color,
            boxWidth: 40,
            padding: 10,
            generateLabels(s) {
                const t = s.data.datasets;
                const {
                    labels: {
                        usePointStyle: e,
                        pointStyle: i,
                        textAlign: n,
                        color: o,
                        useBorderRadius: r,
                        borderRadius: a,
                    },
                } = s.legend.options;
                return s._getSortedDatasetMetas().map((l) => {
                    const c = l.controller.getStyle(e ? 0 : void 0);
                    const h = at(c.borderWidth);
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
                        pointStyle: i || c.pointStyle,
                        rotation: c.rotation,
                        textAlign: n || c.textAlign,
                        borderRadius: r && (a || c.borderRadius),
                        datasetIndex: l.index,
                    };
                }, this);
            },
        },
        title: {
            color: (s) => s.chart.options.color,
            display: !1,
            position: "center",
            text: "",
        },
    },
    descriptors: {
        _scriptable: (s) => !s.startsWith("on"),
        labels: {
            _scriptable: (s) =>
                !["generateLabels", "filter", "sort"].includes(s),
        },
    },
};
const js = class extends yt {
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
        const i = this.options;
        if (((this.left = 0), (this.top = 0), !i.display)) {
            this.width = this.height = this.right = this.bottom = 0;
            return;
        }
        ((this.width = this.right = t), (this.height = this.bottom = e));
        const n = $(i.text) ? i.text.length : 1;
        this._padding = at(i.padding);
        const o = n * Q(i.font).lineHeight + this._padding.height;
        this.isHorizontal() ? (this.height = o) : (this.width = o);
    }

    isHorizontal() {
        const t = this.options.position;
        return t === "top" || t === "bottom";
    }

    _drawArgs(t) {
        const { top: e, left: i, bottom: n, right: o, options: r } = this;
        const a = r.align;
        let l = 0;
        let c;
        let h;
        let u;
        return (
            this.isHorizontal()
                ? ((h = rt(a, i, o)), (u = e + t), (c = o - i))
                : (r.position === "left"
                      ? ((h = i + t), (u = rt(a, n, e)), (l = N * -0.5))
                      : ((h = o - t), (u = rt(a, e, n)), (l = N * 0.5)),
                  (c = n - e)),
            { titleX: h, titleY: u, maxWidth: c, rotation: l }
        );
    }

    draw() {
        const t = this.ctx;
        const e = this.options;
        if (!e.display) return;
        const i = Q(e.font);
        const o = i.lineHeight / 2 + this._padding.top;
        const {
            titleX: r,
            titleY: a,
            maxWidth: l,
            rotation: c,
        } = this._drawArgs(o);
        re(t, e.text, 0, 0, i, {
            color: e.color,
            maxWidth: l,
            rotation: c,
            textAlign: zi(e.align),
            textBaseline: "middle",
            translation: [r, a],
        });
    }
};
function Gf(s, t) {
    const e = new js({ ctx: s.ctx, options: t, chart: s });
    (ct.configure(s, e, t), ct.addBox(s, e), (s.titleBlock = e));
}
const Xf = {
    id: "title",
    _element: js,
    start(s, t, e) {
        Gf(s, e);
    },
    stop(s) {
        const t = s.titleBlock;
        (ct.removeBox(s, t), delete s.titleBlock);
    },
    beforeUpdate(s, t, e) {
        const i = s.titleBlock;
        (ct.configure(s, i, e), (i.options = e));
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
const Xi = new WeakMap();
const Kf = {
    id: "subtitle",
    start(s, t, e) {
        const i = new js({ ctx: s.ctx, options: e, chart: s });
        (ct.configure(s, i, e), ct.addBox(s, i), Xi.set(s, i));
    },
    stop(s) {
        (ct.removeBox(s, Xi.get(s)), Xi.delete(s));
    },
    beforeUpdate(s, t, e) {
        const i = Xi.get(s);
        (ct.configure(s, i, e), (i.options = e));
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
const Rs = {
    average(s) {
        if (!s.length) return !1;
        let t;
        let e;
        const i = new Set();
        let n = 0;
        let o = 0;
        for (t = 0, e = s.length; t < e; ++t) {
            const a = s[t].element;
            if (a && a.hasValue()) {
                const l = a.tooltipPosition();
                (i.add(l.x), (n += l.y), ++o);
            }
        }
        return o === 0 || i.size === 0
            ? !1
            : { x: [...i].reduce((a, l) => a + l) / i.size, y: n / o };
    },
    nearest(s, t) {
        if (!s.length) return !1;
        let e = t.x;
        let i = t.y;
        let n = Number.POSITIVE_INFINITY;
        let o;
        let r;
        let a;
        for (o = 0, r = s.length; o < r; ++o) {
            const l = s[o].element;
            if (l && l.hasValue()) {
                const c = l.getCenterPoint();
                const h = Li(t, c);
                h < n && ((n = h), (a = l));
            }
        }
        if (a) {
            const l = a.tooltipPosition();
            ((e = l.x), (i = l.y));
        }
        return { x: e, y: i };
    },
};
function Wt(s, t) {
    return (t && ($(t) ? Array.prototype.push.apply(s, t) : s.push(t)), s);
}
function qt(s) {
    return (typeof s === "string" || s instanceof String) &&
        s.indexOf(`
`) > -1
        ? s.split(`
`)
        : s;
}
function Jf(s, t) {
    const { element: e, datasetIndex: i, index: n } = t;
    const o = s.getDatasetMeta(i).controller;
    const { label: r, value: a } = o.getLabelAndValue(n);
    return {
        chart: s,
        label: r,
        parsed: o.getParsed(n),
        raw: s.data.datasets[i].data[n],
        formattedValue: a,
        dataset: o.getDataset(),
        dataIndex: n,
        datasetIndex: i,
        element: e,
    };
}
function il(s, t) {
    const e = s.chart.ctx;
    const { body: i, footer: n, title: o } = s;
    const { boxWidth: r, boxHeight: a } = t;
    const l = Q(t.bodyFont);
    const c = Q(t.titleFont);
    const h = Q(t.footerFont);
    const u = o.length;
    const d = n.length;
    const f = i.length;
    const m = at(t.padding);
    let g = m.height;
    let p = 0;
    let y = i.reduce(
        (w, x) => w + x.before.length + x.lines.length + x.after.length,
        0,
    );
    if (
        ((y += s.beforeBody.length + s.afterBody.length),
        u &&
            (g +=
                u * c.lineHeight +
                (u - 1) * t.titleSpacing +
                t.titleMarginBottom),
        y)
    ) {
        const w = t.displayColors ? Math.max(a, l.lineHeight) : l.lineHeight;
        g += f * w + (y - f) * l.lineHeight + (y - 1) * t.bodySpacing;
    }
    d &&
        (g += t.footerMarginTop + d * h.lineHeight + (d - 1) * t.footerSpacing);
    let b = 0;
    const _ = function (w) {
        p = Math.max(p, e.measureText(w).width + b);
    };
    return (
        e.save(),
        (e.font = c.string),
        V(s.title, _),
        (e.font = l.string),
        V(s.beforeBody.concat(s.afterBody), _),
        (b = t.displayColors ? r + 2 + t.boxPadding : 0),
        V(i, (w) => {
            (V(w.before, _), V(w.lines, _), V(w.after, _));
        }),
        (b = 0),
        (e.font = h.string),
        V(s.footer, _),
        e.restore(),
        (p += m.width),
        { width: p, height: g }
    );
}
function Qf(s, t) {
    const { y: e, height: i } = t;
    return e < i / 2 ? "top" : e > s.height - i / 2 ? "bottom" : "center";
}
function tm(s, t, e, i) {
    const { x: n, width: o } = i;
    const r = e.caretSize + e.caretPadding;
    if (
        (s === "left" && n + o + r > t.width) ||
        (s === "right" && n - o - r < 0)
    ) {
        return !0;
    }
}
function em(s, t, e, i) {
    const { x: n, width: o } = e;
    const {
        width: r,
        chartArea: { left: a, right: l },
    } = s;
    let c = "center";
    return (
        i === "center"
            ? (c = n <= (a + l) / 2 ? "left" : "right")
            : n <= o / 2
              ? (c = "left")
              : n >= r - o / 2 && (c = "right"),
        tm(c, s, t, e) && (c = "center"),
        c
    );
}
function nl(s, t, e) {
    const i = e.yAlign || t.yAlign || Qf(s, e);
    return { xAlign: e.xAlign || t.xAlign || em(s, t, e, i), yAlign: i };
}
function sm(s, t) {
    let { x: e, width: i } = s;
    return (t === "right" ? (e -= i) : t === "center" && (e -= i / 2), e);
}
function im(s, t, e) {
    let { y: i, height: n } = s;
    return (
        t === "top" ? (i += e) : t === "bottom" ? (i -= n + e) : (i -= n / 2),
        i
    );
}
function ol(s, t, e, i) {
    const { caretSize: n, caretPadding: o, cornerRadius: r } = s;
    const { xAlign: a, yAlign: l } = e;
    const c = n + o;
    const { topLeft: h, topRight: u, bottomLeft: d, bottomRight: f } = ae(r);
    let m = sm(t, a);
    const g = im(t, l, c);
    return (
        l === "center"
            ? a === "left"
                ? (m += c)
                : a === "right" && (m -= c)
            : a === "left"
              ? (m -= Math.max(h, d) + n)
              : a === "right" && (m += Math.max(u, f) + n),
        { x: tt(m, 0, i.width - t.width), y: tt(g, 0, i.height - t.height) }
    );
}
function Ki(s, t, e) {
    const i = at(e.padding);
    return t === "center"
        ? s.x + s.width / 2
        : t === "right"
          ? s.x + s.width - i.right
          : s.x + i.left;
}
function rl(s) {
    return Wt([], qt(s));
}
function nm(s, t, e) {
    return Yt(s, { tooltip: t, tooltipItems: e, type: "tooltip" });
}
function al(s, t) {
    const e =
        t && t.dataset && t.dataset.tooltip && t.dataset.tooltip.callbacks;
    return e ? s.override(e) : s;
}
const Nl = {
    beforeTitle: Nt,
    title(s) {
        if (s.length > 0) {
            const t = s[0];
            const e = t.chart.data.labels;
            const i = e ? e.length : 0;
            if (this && this.options && this.options.mode === "dataset") {
                return t.dataset.label || "";
            }
            if (t.label) return t.label;
            if (i > 0 && t.dataIndex < i) return e[t.dataIndex];
        }
        return "";
    },
    afterTitle: Nt,
    beforeBody: Nt,
    beforeLabel: Nt,
    label(s) {
        if (this && this.options && this.options.mode === "dataset") {
            return s.label + ": " + s.formattedValue || s.formattedValue;
        }
        let t = s.dataset.label || "";
        t && (t += ": ");
        const e = s.formattedValue;
        return (A(e) || (t += e), t);
    },
    labelColor(s) {
        const e = s.chart
            .getDatasetMeta(s.datasetIndex)
            .controller.getStyle(s.dataIndex);
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
    labelPointStyle(s) {
        const e = s.chart
            .getDatasetMeta(s.datasetIndex)
            .controller.getStyle(s.dataIndex);
        return { pointStyle: e.pointStyle, rotation: e.rotation };
    },
    afterLabel: Nt,
    afterBody: Nt,
    beforeFooter: Nt,
    footer: Nt,
    afterFooter: Nt,
};
function dt(s, t, e, i) {
    const n = s[t].call(e, i);
    return typeof n > "u" ? Nl[t].call(e, i) : n;
}
const Bs = class extends yt {
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
        const i = this.options.setContext(this.getContext());
        const n = i.enabled && e.options.animation && i.animations;
        const o = new en(this.chart, n);
        return (n._cacheable && (this._cachedAnimations = Object.freeze(o)), o);
    }

    getContext() {
        return (
            this.$context ||
            (this.$context = nm(
                this.chart.getContext(),
                this,
                this._tooltipItems,
            ))
        );
    }

    getTitle(t, e) {
        const { callbacks: i } = e;
        const n = dt(i, "beforeTitle", this, t);
        const o = dt(i, "title", this, t);
        const r = dt(i, "afterTitle", this, t);
        let a = [];
        return ((a = Wt(a, qt(n))), (a = Wt(a, qt(o))), (a = Wt(a, qt(r))), a);
    }

    getBeforeBody(t, e) {
        return rl(dt(e.callbacks, "beforeBody", this, t));
    }

    getBody(t, e) {
        const { callbacks: i } = e;
        const n = [];
        return (
            V(t, (o) => {
                const r = { before: [], lines: [], after: [] };
                const a = al(i, o);
                (Wt(r.before, qt(dt(a, "beforeLabel", this, o))),
                    Wt(r.lines, dt(a, "label", this, o)),
                    Wt(r.after, qt(dt(a, "afterLabel", this, o))),
                    n.push(r));
            }),
            n
        );
    }

    getAfterBody(t, e) {
        return rl(dt(e.callbacks, "afterBody", this, t));
    }

    getFooter(t, e) {
        const { callbacks: i } = e;
        const n = dt(i, "beforeFooter", this, t);
        const o = dt(i, "footer", this, t);
        const r = dt(i, "afterFooter", this, t);
        let a = [];
        return ((a = Wt(a, qt(n))), (a = Wt(a, qt(o))), (a = Wt(a, qt(r))), a);
    }

    _createItems(t) {
        const e = this._active;
        const i = this.chart.data;
        const n = [];
        const o = [];
        const r = [];
        let a = [];
        let l;
        let c;
        for (l = 0, c = e.length; l < c; ++l) a.push(Jf(this.chart, e[l]));
        return (
            t.filter && (a = a.filter((h, u, d) => t.filter(h, u, d, i))),
            t.itemSort && (a = a.sort((h, u) => t.itemSort(h, u, i))),
            V(a, (h) => {
                const u = al(t.callbacks, h);
                (n.push(dt(u, "labelColor", this, h)),
                    o.push(dt(u, "labelPointStyle", this, h)),
                    r.push(dt(u, "labelTextColor", this, h)));
            }),
            (this.labelColors = n),
            (this.labelPointStyles = o),
            (this.labelTextColors = r),
            (this.dataPoints = a),
            a
        );
    }

    update(t, e) {
        const i = this.options.setContext(this.getContext());
        const n = this._active;
        let o;
        let r = [];
        if (!n.length) this.opacity !== 0 && (o = { opacity: 0 });
        else {
            const a = Rs[i.position].call(this, n, this._eventPosition);
            ((r = this._createItems(i)),
                (this.title = this.getTitle(r, i)),
                (this.beforeBody = this.getBeforeBody(r, i)),
                (this.body = this.getBody(r, i)),
                (this.afterBody = this.getAfterBody(r, i)),
                (this.footer = this.getFooter(r, i)));
            const l = (this._size = il(this, i));
            const c = Object.assign({}, a, l);
            const h = nl(this.chart, i, c);
            const u = ol(i, c, h, this.chart);
            ((this.xAlign = h.xAlign),
                (this.yAlign = h.yAlign),
                (o = {
                    opacity: 1,
                    x: u.x,
                    y: u.y,
                    width: l.width,
                    height: l.height,
                    caretX: a.x,
                    caretY: a.y,
                }));
        }
        ((this._tooltipItems = r),
            (this.$context = void 0),
            o && this._resolveAnimations().update(this, o),
            t &&
                i.external &&
                i.external.call(this, {
                    chart: this.chart,
                    tooltip: this,
                    replay: e,
                }));
    }

    drawCaret(t, e, i, n) {
        const o = this.getCaretPosition(t, i, n);
        (e.lineTo(o.x1, o.y1), e.lineTo(o.x2, o.y2), e.lineTo(o.x3, o.y3));
    }

    getCaretPosition(t, e, i) {
        const { xAlign: n, yAlign: o } = this;
        const { caretSize: r, cornerRadius: a } = i;
        const {
            topLeft: l,
            topRight: c,
            bottomLeft: h,
            bottomRight: u,
        } = ae(a);
        const { x: d, y: f } = t;
        const { width: m, height: g } = e;
        let p;
        let y;
        let b;
        let _;
        let w;
        let x;
        return (
            o === "center"
                ? ((w = f + g / 2),
                  n === "left"
                      ? ((p = d), (y = p - r), (_ = w + r), (x = w - r))
                      : ((p = d + m), (y = p + r), (_ = w - r), (x = w + r)),
                  (b = p))
                : (n === "left"
                      ? (y = d + Math.max(l, h) + r)
                      : n === "right"
                        ? (y = d + m - Math.max(c, u) - r)
                        : (y = this.caretX),
                  o === "top"
                      ? ((_ = f), (w = _ - r), (p = y - r), (b = y + r))
                      : ((_ = f + g), (w = _ + r), (p = y + r), (b = y - r)),
                  (x = _)),
            { x1: p, x2: y, x3: b, y1: _, y2: w, y3: x }
        );
    }

    drawTitle(t, e, i) {
        const n = this.title;
        const o = n.length;
        let r;
        let a;
        let l;
        if (o) {
            const c = Se(i.rtl, this.x, this.width);
            for (
                t.x = Ki(this, i.titleAlign, i),
                    e.textAlign = c.textAlign(i.titleAlign),
                    e.textBaseline = "middle",
                    r = Q(i.titleFont),
                    a = i.titleSpacing,
                    e.fillStyle = i.titleColor,
                    e.font = r.string,
                    l = 0;
                l < o;
                ++l
            ) {
                (e.fillText(n[l], c.x(t.x), t.y + r.lineHeight / 2),
                    (t.y += r.lineHeight + a),
                    l + 1 === o && (t.y += i.titleMarginBottom - a));
            }
        }
    }

    _drawColorBox(t, e, i, n, o) {
        const r = this.labelColors[i];
        const a = this.labelPointStyles[i];
        const { boxHeight: l, boxWidth: c } = o;
        const h = Q(o.bodyFont);
        const u = Ki(this, "left", o);
        const d = n.x(u);
        const f = l < h.lineHeight ? (h.lineHeight - l) / 2 : 0;
        const m = e.y + f;
        if (o.usePointStyle) {
            const g = {
                radius: Math.min(c, l) / 2,
                pointStyle: a.pointStyle,
                rotation: a.rotation,
                borderWidth: 1,
            };
            const p = n.leftForLtr(d, c) + c / 2;
            const y = m + l / 2;
            ((t.strokeStyle = o.multiKeyBackground),
                (t.fillStyle = o.multiKeyBackground),
                Hi(t, g, p, y),
                (t.strokeStyle = r.borderColor),
                (t.fillStyle = r.backgroundColor),
                Hi(t, g, p, y));
        } else {
            ((t.lineWidth = L(r.borderWidth)
                ? Math.max(...Object.values(r.borderWidth))
                : r.borderWidth || 1),
                (t.strokeStyle = r.borderColor),
                t.setLineDash(r.borderDash || []),
                (t.lineDashOffset = r.borderDashOffset || 0));
            const g = n.leftForLtr(d, c);
            const p = n.leftForLtr(n.xPlus(d, 1), c - 2);
            const y = ae(r.borderRadius);
            Object.values(y).some((b) => b !== 0)
                ? (t.beginPath(),
                  (t.fillStyle = o.multiKeyBackground),
                  Ye(t, { x: g, y: m, w: c, h: l, radius: y }),
                  t.fill(),
                  t.stroke(),
                  (t.fillStyle = r.backgroundColor),
                  t.beginPath(),
                  Ye(t, { x: p, y: m + 1, w: c - 2, h: l - 2, radius: y }),
                  t.fill())
                : ((t.fillStyle = o.multiKeyBackground),
                  t.fillRect(g, m, c, l),
                  t.strokeRect(g, m, c, l),
                  (t.fillStyle = r.backgroundColor),
                  t.fillRect(p, m + 1, c - 2, l - 2));
        }
        t.fillStyle = this.labelTextColors[i];
    }

    drawBody(t, e, i) {
        const { body: n } = this;
        const {
            bodySpacing: o,
            bodyAlign: r,
            displayColors: a,
            boxHeight: l,
            boxWidth: c,
            boxPadding: h,
        } = i;
        const u = Q(i.bodyFont);
        let d = u.lineHeight;
        let f = 0;
        const m = Se(i.rtl, this.x, this.width);
        const g = function (v) {
            (e.fillText(v, m.x(t.x + f), t.y + d / 2), (t.y += d + o));
        };
        const p = m.textAlign(r);
        let y;
        let b;
        let _;
        let w;
        let x;
        let k;
        let M;
        for (
            e.textAlign = r,
                e.textBaseline = "middle",
                e.font = u.string,
                t.x = Ki(this, p, i),
                e.fillStyle = i.bodyColor,
                V(this.beforeBody, g),
                f =
                    a && p !== "right"
                        ? r === "center"
                            ? c / 2 + h
                            : c + 2 + h
                        : 0,
                w = 0,
                k = n.length;
            w < k;
            ++w
        ) {
            for (
                y = n[w],
                    b = this.labelTextColors[w],
                    e.fillStyle = b,
                    V(y.before, g),
                    _ = y.lines,
                    a &&
                        _.length &&
                        (this._drawColorBox(e, t, w, m, i),
                        (d = Math.max(u.lineHeight, l))),
                    x = 0,
                    M = _.length;
                x < M;
                ++x
            ) {
                (g(_[x]), (d = u.lineHeight));
            }
            V(y.after, g);
        }
        ((f = 0), (d = u.lineHeight), V(this.afterBody, g), (t.y -= o));
    }

    drawFooter(t, e, i) {
        const n = this.footer;
        const o = n.length;
        let r;
        let a;
        if (o) {
            const l = Se(i.rtl, this.x, this.width);
            for (
                t.x = Ki(this, i.footerAlign, i),
                    t.y += i.footerMarginTop,
                    e.textAlign = l.textAlign(i.footerAlign),
                    e.textBaseline = "middle",
                    r = Q(i.footerFont),
                    e.fillStyle = i.footerColor,
                    e.font = r.string,
                    a = 0;
                a < o;
                ++a
            ) {
                (e.fillText(n[a], l.x(t.x), t.y + r.lineHeight / 2),
                    (t.y += r.lineHeight + i.footerSpacing));
            }
        }
    }

    drawBackground(t, e, i, n) {
        const { xAlign: o, yAlign: r } = this;
        const { x: a, y: l } = t;
        const { width: c, height: h } = i;
        const {
            topLeft: u,
            topRight: d,
            bottomLeft: f,
            bottomRight: m,
        } = ae(n.cornerRadius);
        ((e.fillStyle = n.backgroundColor),
            (e.strokeStyle = n.borderColor),
            (e.lineWidth = n.borderWidth),
            e.beginPath(),
            e.moveTo(a + u, l),
            r === "top" && this.drawCaret(t, e, i, n),
            e.lineTo(a + c - d, l),
            e.quadraticCurveTo(a + c, l, a + c, l + d),
            r === "center" && o === "right" && this.drawCaret(t, e, i, n),
            e.lineTo(a + c, l + h - m),
            e.quadraticCurveTo(a + c, l + h, a + c - m, l + h),
            r === "bottom" && this.drawCaret(t, e, i, n),
            e.lineTo(a + f, l + h),
            e.quadraticCurveTo(a, l + h, a, l + h - f),
            r === "center" && o === "left" && this.drawCaret(t, e, i, n),
            e.lineTo(a, l + u),
            e.quadraticCurveTo(a, l, a + u, l),
            e.closePath(),
            e.fill(),
            n.borderWidth > 0 && e.stroke());
    }

    _updateAnimationTarget(t) {
        const e = this.chart;
        const i = this.$animations;
        const n = i && i.x;
        const o = i && i.y;
        if (n || o) {
            const r = Rs[t.position].call(
                this,
                this._active,
                this._eventPosition,
            );
            if (!r) return;
            const a = (this._size = il(this, t));
            const l = Object.assign({}, r, this._size);
            const c = nl(e, t, l);
            const h = ol(t, l, c, e);
            (n._to !== h.x || o._to !== h.y) &&
                ((this.xAlign = c.xAlign),
                (this.yAlign = c.yAlign),
                (this.width = a.width),
                (this.height = a.height),
                (this.caretX = r.x),
                (this.caretY = r.y),
                this._resolveAnimations().update(this, h));
        }
    }

    _willRender() {
        return !!this.opacity;
    }

    draw(t) {
        const e = this.options.setContext(this.getContext());
        let i = this.opacity;
        if (!i) return;
        this._updateAnimationTarget(e);
        const n = { width: this.width, height: this.height };
        const o = { x: this.x, y: this.y };
        i = Math.abs(i) < 0.001 ? 0 : i;
        const r = at(e.padding);
        const a =
            this.title.length ||
            this.beforeBody.length ||
            this.body.length ||
            this.afterBody.length ||
            this.footer.length;
        e.enabled &&
            a &&
            (t.save(),
            (t.globalAlpha = i),
            this.drawBackground(o, t, n, e),
            oo(t, e.textDirection),
            (o.y += r.top),
            this.drawTitle(o, t, e),
            this.drawBody(o, t, e),
            this.drawFooter(o, t, e),
            ro(t, e.textDirection),
            t.restore());
    }

    getActiveElements() {
        return this._active || [];
    }

    setActiveElements(t, e) {
        const i = this._active;
        const n = t.map(({ datasetIndex: a, index: l }) => {
            const c = this.chart.getDatasetMeta(a);
            if (!c) throw new Error("Cannot find a dataset at index " + a);
            return { datasetIndex: a, element: c.data[l], index: l };
        });
        const o = !Ts(i, n);
        const r = this._positionChanged(n, e);
        (o || r) &&
            ((this._active = n),
            (this._eventPosition = e),
            (this._ignoreReplayEvents = !0),
            this.update(!0));
    }

    handleEvent(t, e, i = !0) {
        if (e && this._ignoreReplayEvents) return !1;
        this._ignoreReplayEvents = !1;
        const n = this.options;
        const o = this._active || [];
        const r = this._getActiveElements(t, o, e, i);
        const a = this._positionChanged(r, t);
        const l = e || !Ts(r, o) || a;
        return (
            l &&
                ((this._active = r),
                (n.enabled || n.external) &&
                    ((this._eventPosition = { x: t.x, y: t.y }),
                    this.update(!0, e))),
            l
        );
    }

    _getActiveElements(t, e, i, n) {
        const o = this.options;
        if (t.type === "mouseout") return [];
        if (!n) {
            return e.filter(
                (a) =>
                    this.chart.data.datasets[a.datasetIndex] &&
                    this.chart
                        .getDatasetMeta(a.datasetIndex)
                        .controller.getParsed(a.index) !== void 0,
            );
        }
        const r = this.chart.getElementsAtEventForMode(t, o.mode, o, i);
        return (o.reverse && r.reverse(), r);
    }

    _positionChanged(t, e) {
        const { caretX: i, caretY: n, options: o } = this;
        const r = Rs[o.position].call(this, t, e);
        return r !== !1 && (i !== r.x || n !== r.y);
    }
};
S(Bs, "positioners", Rs);
const om = {
    id: "tooltip",
    _element: Bs,
    positioners: Rs,
    afterInit(s, t, e) {
        e && (s.tooltip = new Bs({ chart: s, options: e }));
    },
    beforeUpdate(s, t, e) {
        s.tooltip && s.tooltip.initialize(e);
    },
    reset(s, t, e) {
        s.tooltip && s.tooltip.initialize(e);
    },
    afterDraw(s) {
        const t = s.tooltip;
        if (t && t._willRender()) {
            const e = { tooltip: t };
            if (
                s.notifyPlugins("beforeTooltipDraw", {
                    ...e,
                    cancelable: !0,
                }) === !1
            ) {
                return;
            }
            (t.draw(s.ctx), s.notifyPlugins("afterTooltipDraw", e));
        }
    },
    afterEvent(s, t) {
        if (s.tooltip) {
            const e = t.replay;
            s.tooltip.handleEvent(t.event, e, t.inChartArea) &&
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
        boxHeight: (s, t) => t.bodyFont.size,
        boxWidth: (s, t) => t.bodyFont.size,
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
        callbacks: Nl,
    },
    defaultRoutes: { bodyFont: "font", footerFont: "font", titleFont: "font" },
    descriptors: {
        _scriptable: (s) =>
            s !== "filter" && s !== "itemSort" && s !== "external",
        _indexable: !1,
        callbacks: { _scriptable: !1, _indexable: !1 },
        animation: { _fallback: !1 },
        animations: { _fallback: "animation" },
    },
    additionalOptionScopes: ["interaction"],
};
const rm = Object.freeze({
    __proto__: null,
    Colors: bf,
    Decimation: Sf,
    Filler: Bf,
    Legend: qf,
    SubTitle: Kf,
    Title: Xf,
    Tooltip: om,
});
const am = (s, t, e, i) => (
    typeof t === "string"
        ? ((e = s.push(t) - 1), i.unshift({ index: e, label: t }))
        : isNaN(t) && (e = null),
    e
);
function lm(s, t, e, i) {
    const n = s.indexOf(t);
    if (n === -1) return am(s, t, e, i);
    const o = s.lastIndexOf(t);
    return n !== o ? e : n;
}
const cm = (s, t) => (s === null ? null : tt(Math.round(s), 0, t));
function ll(s) {
    const t = this.getLabels();
    return s >= 0 && s < t.length ? t[s] : s;
}
const Ws = class extends Ee {
    constructor(t) {
        (super(t),
            (this._startValue = void 0),
            (this._valueRange = 0),
            (this._addedLabels = []));
    }

    init(t) {
        const e = this._addedLabels;
        if (e.length) {
            const i = this.getLabels();
            for (const { index: n, label: o } of e) {
                i[n] === o && i.splice(n, 1);
            }
            this._addedLabels = [];
        }
        super.init(t);
    }

    parse(t, e) {
        if (A(t)) return null;
        const i = this.getLabels();
        return (
            (e =
                isFinite(e) && i[e] === t
                    ? e
                    : lm(i, t, I(e, t), this._addedLabels)),
            cm(e, i.length - 1)
        );
    }

    determineDataLimits() {
        const { minDefined: t, maxDefined: e } = this.getUserBounds();
        let { min: i, max: n } = this.getMinMax(!0);
        (this.options.bounds === "ticks" &&
            (t || (i = 0), e || (n = this.getLabels().length - 1)),
            (this.min = i),
            (this.max = n));
    }

    buildTicks() {
        const t = this.min;
        const e = this.max;
        const i = this.options.offset;
        const n = [];
        let o = this.getLabels();
        ((o = t === 0 && e === o.length - 1 ? o : o.slice(t, e + 1)),
            (this._valueRange = Math.max(o.length - (i ? 0 : 1), 1)),
            (this._startValue = this.min - (i ? 0.5 : 0)));
        for (let r = t; r <= e; r++) n.push({ value: r });
        return n;
    }

    getLabelForValue(t) {
        return ll.call(this, t);
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
(S(Ws, "id", "category"), S(Ws, "defaults", { ticks: { callback: ll } }));
function hm(s, t) {
    const e = [];
    const {
        bounds: n,
        step: o,
        min: r,
        max: a,
        precision: l,
        count: c,
        maxTicks: h,
        maxDigits: u,
        includeBounds: d,
    } = s;
    const f = o || 1;
    const m = h - 1;
    const { min: g, max: p } = t;
    const y = !A(r);
    const b = !A(a);
    const _ = !A(c);
    const w = (p - g) / (u + 1);
    let x = Wn((p - g) / m / f) * f;
    let k;
    let M;
    let v;
    let O;
    if (x < 1e-14 && !y && !b) return [{ value: g }, { value: p }];
    ((O = Math.ceil(p / x) - Math.floor(g / x)),
        O > m && (x = Wn((O * x) / m / f) * f),
        A(l) || ((k = Math.pow(10, l)), (x = Math.ceil(x * k) / k)),
        n === "ticks"
            ? ((M = Math.floor(g / x) * x), (v = Math.ceil(p / x) * x))
            : ((M = g), (v = p)),
        y && b && o && Gr((a - r) / o, x / 1e3)
            ? ((O = Math.round(Math.min((a - r) / x, h))),
              (x = (a - r) / O),
              (M = r),
              (v = a))
            : _
              ? ((M = y ? r : M),
                (v = b ? a : v),
                (O = c - 1),
                (x = (v - M) / O))
              : ((O = (v - M) / x),
                $e(O, Math.round(O), x / 1e3)
                    ? (O = Math.round(O))
                    : (O = Math.ceil(O))));
    const E = Math.max(Vn(x), Vn(M));
    ((k = Math.pow(10, A(l) ? E : l)),
        (M = Math.round(M * k) / k),
        (v = Math.round(v * k) / k));
    let C = 0;
    for (
        y &&
        (d && M !== r
            ? (e.push({ value: r }),
              M < r && C++,
              $e(Math.round((M + C * x) * k) / k, r, cl(r, w, s)) && C++)
            : M < r && C++);
        C < O;
        ++C
    ) {
        const P = Math.round((M + C * x) * k) / k;
        if (b && P > a) break;
        e.push({ value: P });
    }
    return (
        b && d && v !== a
            ? e.length && $e(e[e.length - 1].value, a, cl(a, w, s))
                ? (e[e.length - 1].value = a)
                : e.push({ value: a })
            : (!b || v === a) && e.push({ value: v }),
        e
    );
}
function cl(s, t, { horizontal: e, minRotation: i }) {
    const n = wt(i);
    const o = (e ? Math.sin(n) : Math.cos(n)) || 0.001;
    const r = 0.75 * t * ("" + s).length;
    return Math.min(t / o, r);
}
const is = class extends Ee {
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
        const { minDefined: e, maxDefined: i } = this.getUserBounds();
        let { min: n, max: o } = this;
        const r = (l) => (n = e ? n : l);
        const a = (l) => (o = i ? o : l);
        if (t) {
            const l = Ot(n);
            const c = Ot(o);
            l < 0 && c < 0 ? a(0) : l > 0 && c > 0 && r(0);
        }
        if (n === o) {
            const l = o === 0 ? 1 : Math.abs(o * 0.05);
            (a(o + l), t || r(n - l));
        }
        ((this.min = n), (this.max = o));
    }

    getTickLimit() {
        const t = this.options.ticks;
        let { maxTicksLimit: e, stepSize: i } = t;
        let n;
        return (
            i
                ? ((n = Math.ceil(this.max / i) - Math.floor(this.min / i) + 1),
                  n > 1e3 &&
                      (console.warn(
                          `scales.${this.id}.ticks.stepSize: ${i} would result generating up to ${n} ticks. Limiting to 1000.`,
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
        let i = this.getTickLimit();
        i = Math.max(2, i);
        const n = {
            maxTicks: i,
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
        const r = hm(n, o);
        return (
            t.bounds === "ticks" && zn(r, this, "value"),
            t.reverse
                ? (r.reverse(), (this.start = this.max), (this.end = this.min))
                : ((this.start = this.min), (this.end = this.max)),
            r
        );
    }

    configure() {
        const t = this.ticks;
        let e = this.min;
        let i = this.max;
        if ((super.configure(), this.options.offset && t.length)) {
            const n = (i - e) / Math.max(t.length - 1, 1) / 2;
            ((e -= n), (i += n));
        }
        ((this._startValue = e),
            (this._endValue = i),
            (this._valueRange = i - e));
    }

    getLabelForValue(t) {
        return Ue(t, this.chart.options.locale, this.options.ticks.format);
    }
};
const zs = class extends is {
    determineDataLimits() {
        const { min: t, max: e } = this.getMinMax(!0);
        ((this.min = Z(t) ? t : 0),
            (this.max = Z(e) ? e : 1),
            this.handleTickRangeOptions());
    }

    computeTickLimit() {
        const t = this.isHorizontal();
        const e = t ? this.width : this.height;
        const i = wt(this.options.ticks.minRotation);
        const n = (t ? Math.sin(i) : Math.cos(i)) || 0.001;
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
(S(zs, "id", "linear"),
    S(zs, "defaults", { ticks: { callback: Os.formatters.numeric } }));
const Us = (s) => Math.floor(jt(s));
const Me = (s, t) => Math.pow(10, Us(s) + t);
function hl(s) {
    return s / Math.pow(10, Us(s)) === 1;
}
function ul(s, t, e) {
    const i = Math.pow(10, e);
    const n = Math.floor(s / i);
    return Math.ceil(t / i) - n;
}
function um(s, t) {
    const e = t - s;
    let i = Us(e);
    for (; ul(s, t, i) > 10; ) i++;
    for (; ul(s, t, i) < 10; ) i--;
    return Math.min(i, Us(s));
}
function dm(s, { min: t, max: e }) {
    t = ut(s.min, t);
    const i = [];
    const n = Us(t);
    let o = um(t, e);
    let r = o < 0 ? Math.pow(10, Math.abs(o)) : 1;
    const a = Math.pow(10, o);
    const l = n > o ? Math.pow(10, n) : 0;
    const c = Math.round((t - l) * r) / r;
    const h = Math.floor((t - l) / a / 10) * a * 10;
    let u = Math.floor((c - h) / Math.pow(10, o));
    let d = ut(s.min, Math.round((l + h + u * Math.pow(10, o)) * r) / r);
    for (; d < e; ) {
        (i.push({ value: d, major: hl(d), significand: u }),
            u >= 10 ? (u = u < 15 ? 15 : 20) : u++,
            u >= 20 && (o++, (u = 2), (r = o >= 0 ? 1 : r)),
            (d = Math.round((l + h + u * Math.pow(10, o)) * r) / r));
    }
    const f = ut(s.max, d);
    return (i.push({ value: f, major: hl(f), significand: u }), i);
}
const Vs = class extends Ee {
    constructor(t) {
        (super(t),
            (this.start = void 0),
            (this.end = void 0),
            (this._startValue = void 0),
            (this._valueRange = 0));
    }

    parse(t, e) {
        const i = is.prototype.parse.apply(this, [t, e]);
        if (i === 0) {
            this._zero = !0;
            return;
        }
        return Z(i) && i > 0 ? i : null;
    }

    determineDataLimits() {
        const { min: t, max: e } = this.getMinMax(!0);
        ((this.min = Z(t) ? Math.max(0, t) : null),
            (this.max = Z(e) ? Math.max(0, e) : null),
            this.options.beginAtZero && (this._zero = !0),
            this._zero &&
                this.min !== this._suggestedMin &&
                !Z(this._userMin) &&
                (this.min =
                    t === Me(this.min, 0) ? Me(this.min, -1) : Me(this.min, 0)),
            this.handleTickRangeOptions());
    }

    handleTickRangeOptions() {
        const { minDefined: t, maxDefined: e } = this.getUserBounds();
        let i = this.min;
        let n = this.max;
        const o = (a) => (i = t ? i : a);
        const r = (a) => (n = e ? n : a);
        (i === n && (i <= 0 ? (o(1), r(10)) : (o(Me(i, -1)), r(Me(n, 1)))),
            i <= 0 && o(Me(n, -1)),
            n <= 0 && r(Me(i, 1)),
            (this.min = i),
            (this.max = n));
    }

    buildTicks() {
        const t = this.options;
        const e = { min: this._userMin, max: this._userMax };
        const i = dm(e, this);
        return (
            t.bounds === "ticks" && zn(i, this, "value"),
            t.reverse
                ? (i.reverse(), (this.start = this.max), (this.end = this.min))
                : ((this.start = this.min), (this.end = this.max)),
            i
        );
    }

    getLabelForValue(t) {
        return t === void 0
            ? "0"
            : Ue(t, this.chart.options.locale, this.options.ticks.format);
    }

    configure() {
        const t = this.min;
        (super.configure(),
            (this._startValue = jt(t)),
            (this._valueRange = jt(this.max) - jt(t)));
    }

    getPixelForValue(t) {
        return (
            (t === void 0 || t === 0) && (t = this.min),
            t === null || isNaN(t)
                ? NaN
                : this.getPixelForDecimal(
                      t === this.min
                          ? 0
                          : (jt(t) - this._startValue) / this._valueRange,
                  )
        );
    }

    getValueForPixel(t) {
        const e = this.getDecimalForPixel(t);
        return Math.pow(10, this._startValue + e * this._valueRange);
    }
};
(S(Vs, "id", "logarithmic"),
    S(Vs, "defaults", {
        ticks: { callback: Os.formatters.logarithmic, major: { enabled: !0 } },
    }));
function Po(s) {
    const t = s.ticks;
    if (t.display && s.display) {
        const e = at(t.backdropPadding);
        return I(t.font && t.font.size, U.font.size) + e.height;
    }
    return 0;
}
function fm(s, t, e) {
    return (
        (e = $(e) ? e : [e]),
        { w: na(s, t.string, e), h: e.length * t.lineHeight }
    );
}
function dl(s, t, e, i, n) {
    return s === i || s === n
        ? { start: t - e / 2, end: t + e / 2 }
        : s < i || s > n
          ? { start: t - e, end: t }
          : { start: t, end: t + e };
}
function mm(s) {
    const t = {
        l: s.left + s._padding.left,
        r: s.right - s._padding.right,
        t: s.top + s._padding.top,
        b: s.bottom - s._padding.bottom,
    };
    const e = Object.assign({}, t);
    const i = [];
    const n = [];
    const o = s._pointLabels.length;
    const r = s.options.pointLabels;
    const a = r.centerPointLabels ? N / o : 0;
    for (let l = 0; l < o; l++) {
        const c = r.setContext(s.getPointLabelContext(l));
        n[l] = c.padding;
        const h = s.getPointPosition(l, s.drawingArea + n[l], a);
        const u = Q(c.font);
        const d = fm(s.ctx, u, s._pointLabels[l]);
        i[l] = d;
        const f = ot(s.getIndexAngle(l) + a);
        const m = Math.round(Ri(f));
        const g = dl(m, h.x, d.w, 0, 180);
        const p = dl(m, h.y, d.h, 90, 270);
        gm(e, t, f, g, p);
    }
    (s.setCenterPoint(t.l - e.l, e.r - t.r, t.t - e.t, e.b - t.b),
        (s._pointLabelItems = bm(s, i, n)));
}
function gm(s, t, e, i, n) {
    const o = Math.abs(Math.sin(e));
    const r = Math.abs(Math.cos(e));
    let a = 0;
    let l = 0;
    (i.start < t.l
        ? ((a = (t.l - i.start) / o), (s.l = Math.min(s.l, t.l - a)))
        : i.end > t.r &&
          ((a = (i.end - t.r) / o), (s.r = Math.max(s.r, t.r + a))),
        n.start < t.t
            ? ((l = (t.t - n.start) / r), (s.t = Math.min(s.t, t.t - l)))
            : n.end > t.b &&
              ((l = (n.end - t.b) / r), (s.b = Math.max(s.b, t.b + l))));
}
function pm(s, t, e) {
    const i = s.drawingArea;
    const { extra: n, additionalAngle: o, padding: r, size: a } = e;
    const l = s.getPointPosition(t, i + n + r, o);
    const c = Math.round(Ri(ot(l.angle + X)));
    const h = wm(l.y, a.h, c);
    const u = xm(c);
    const d = _m(l.x, a.w, u);
    return {
        visible: !0,
        x: l.x,
        y: h,
        textAlign: u,
        left: d,
        top: h,
        right: d + a.w,
        bottom: h + a.h,
    };
}
function ym(s, t) {
    if (!t) return !0;
    const { left: e, top: i, right: n, bottom: o } = s;
    return !(
        Pt({ x: e, y: i }, t) ||
        Pt({ x: e, y: o }, t) ||
        Pt({ x: n, y: i }, t) ||
        Pt({ x: n, y: o }, t)
    );
}
function bm(s, t, e) {
    const i = [];
    const n = s._pointLabels.length;
    const o = s.options;
    const { centerPointLabels: r, display: a } = o.pointLabels;
    const l = { extra: Po(o) / 2, additionalAngle: r ? N / n : 0 };
    let c;
    for (let h = 0; h < n; h++) {
        ((l.padding = e[h]), (l.size = t[h]));
        const u = pm(s, h, l);
        (i.push(u),
            a === "auto" && ((u.visible = ym(u, c)), u.visible && (c = u)));
    }
    return i;
}
function xm(s) {
    return s === 0 || s === 180 ? "center" : s < 180 ? "left" : "right";
}
function _m(s, t, e) {
    return (e === "right" ? (s -= t) : e === "center" && (s -= t / 2), s);
}
function wm(s, t, e) {
    return (
        e === 90 || e === 270 ? (s -= t / 2) : (e > 270 || e < 90) && (s -= t),
        s
    );
}
function Sm(s, t, e) {
    const { left: i, top: n, right: o, bottom: r } = e;
    const { backdropColor: a } = t;
    if (!A(a)) {
        const l = ae(t.borderRadius);
        const c = at(t.backdropPadding);
        s.fillStyle = a;
        const h = i - c.left;
        const u = n - c.top;
        const d = o - i + c.width;
        const f = r - n + c.height;
        Object.values(l).some((m) => m !== 0)
            ? (s.beginPath(),
              Ye(s, { x: h, y: u, w: d, h: f, radius: l }),
              s.fill())
            : s.fillRect(h, u, d, f);
    }
}
function km(s, t) {
    const {
        ctx: e,
        options: { pointLabels: i },
    } = s;
    for (let n = t - 1; n >= 0; n--) {
        const o = s._pointLabelItems[n];
        if (!o.visible) continue;
        const r = i.setContext(s.getPointLabelContext(n));
        Sm(e, r, o);
        const a = Q(r.font);
        const { x: l, y: c, textAlign: h } = o;
        re(e, s._pointLabels[n], l, c + a.lineHeight / 2, a, {
            color: r.color,
            textAlign: h,
            textBaseline: "middle",
        });
    }
}
function Rl(s, t, e, i) {
    const { ctx: n } = s;
    if (e) n.arc(s.xCenter, s.yCenter, t, 0, j);
    else {
        let o = s.getPointPosition(0, t);
        n.moveTo(o.x, o.y);
        for (let r = 1; r < i; r++) {
            ((o = s.getPointPosition(r, t)), n.lineTo(o.x, o.y));
        }
    }
}
function Mm(s, t, e, i, n) {
    const o = s.ctx;
    const r = t.circular;
    const { color: a, lineWidth: l } = t;
    (!r && !i) ||
        !a ||
        !l ||
        e < 0 ||
        (o.save(),
        (o.strokeStyle = a),
        (o.lineWidth = l),
        o.setLineDash(n.dash || []),
        (o.lineDashOffset = n.dashOffset),
        o.beginPath(),
        Rl(s, e, r, i),
        o.closePath(),
        o.stroke(),
        o.restore());
}
function vm(s, t, e) {
    return Yt(s, { label: e, index: t, type: "pointLabel" });
}
const Te = class extends is {
    constructor(t) {
        (super(t),
            (this.xCenter = void 0),
            (this.yCenter = void 0),
            (this.drawingArea = void 0),
            (this._pointLabels = []),
            (this._pointLabelItems = []));
    }

    setDimensions() {
        const t = (this._padding = at(Po(this.options) / 2));
        const e = (this.width = this.maxWidth - t.width);
        const i = (this.height = this.maxHeight - t.height);
        ((this.xCenter = Math.floor(this.left + e / 2 + t.left)),
            (this.yCenter = Math.floor(this.top + i / 2 + t.top)),
            (this.drawingArea = Math.floor(Math.min(e, i) / 2)));
    }

    determineDataLimits() {
        const { min: t, max: e } = this.getMinMax(!1);
        ((this.min = Z(t) && !isNaN(t) ? t : 0),
            (this.max = Z(e) && !isNaN(e) ? e : 0),
            this.handleTickRangeOptions());
    }

    computeTickLimit() {
        return Math.ceil(this.drawingArea / Po(this.options));
    }

    generateTickLabels(t) {
        (is.prototype.generateTickLabels.call(this, t),
            (this._pointLabels = this.getLabels()
                .map((e, i) => {
                    const n = B(
                        this.options.pointLabels.callback,
                        [e, i],
                        this,
                    );
                    return n || n === 0 ? n : "";
                })
                .filter((e, i) => this.chart.getDataVisibility(i))));
    }

    fit() {
        const t = this.options;
        t.display && t.pointLabels.display
            ? mm(this)
            : this.setCenterPoint(0, 0, 0, 0);
    }

    setCenterPoint(t, e, i, n) {
        ((this.xCenter += Math.floor((t - e) / 2)),
            (this.yCenter += Math.floor((i - n) / 2)),
            (this.drawingArea -= Math.min(
                this.drawingArea / 2,
                Math.max(t, e, i, n),
            )));
    }

    getIndexAngle(t) {
        const e = j / (this._pointLabels.length || 1);
        const i = this.options.startAngle || 0;
        return ot(t * e + wt(i));
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
            const i = e[t];
            return vm(this.getContext(), t, i);
        }
    }

    getPointPosition(t, e, i = 0) {
        const n = this.getIndexAngle(t) - X + i;
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
            top: i,
            right: n,
            bottom: o,
        } = this._pointLabelItems[t];
        return { left: e, top: i, right: n, bottom: o };
    }

    drawBackground() {
        const {
            backgroundColor: t,
            grid: { circular: e },
        } = this.options;
        if (t) {
            const i = this.ctx;
            (i.save(),
                i.beginPath(),
                Rl(
                    this,
                    this.getDistanceFromCenterForValue(this._endValue),
                    e,
                    this._pointLabels.length,
                ),
                i.closePath(),
                (i.fillStyle = t),
                i.fill(),
                i.restore());
        }
    }

    drawGrid() {
        const t = this.ctx;
        const e = this.options;
        const { angleLines: i, grid: n, border: o } = e;
        const r = this._pointLabels.length;
        let a;
        let l;
        let c;
        if (
            (e.pointLabels.display && km(this, r),
            n.display &&
                this.ticks.forEach((h, u) => {
                    if (u !== 0 || (u === 0 && this.min < 0)) {
                        l = this.getDistanceFromCenterForValue(h.value);
                        const d = this.getContext(u);
                        const f = n.setContext(d);
                        const m = o.setContext(d);
                        Mm(this, f, l, r, m);
                    }
                }),
            i.display)
        ) {
            for (t.save(), a = r - 1; a >= 0; a--) {
                const h = i.setContext(this.getPointLabelContext(a));
                const { color: u, lineWidth: d } = h;
                !d ||
                    !u ||
                    ((t.lineWidth = d),
                    (t.strokeStyle = u),
                    t.setLineDash(h.borderDash),
                    (t.lineDashOffset = h.borderDashOffset),
                    (l = this.getDistanceFromCenterForValue(
                        e.reverse ? this.min : this.max,
                    )),
                    (c = this.getPointPosition(a, l)),
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
        const i = e.ticks;
        if (!i.display) return;
        const n = this.getIndexAngle(0);
        let o;
        let r;
        (t.save(),
            t.translate(this.xCenter, this.yCenter),
            t.rotate(n),
            (t.textAlign = "center"),
            (t.textBaseline = "middle"),
            this.ticks.forEach((a, l) => {
                if (l === 0 && this.min >= 0 && !e.reverse) return;
                const c = i.setContext(this.getContext(l));
                const h = Q(c.font);
                if (
                    ((o = this.getDistanceFromCenterForValue(
                        this.ticks[l].value,
                    )),
                    c.showLabelBackdrop)
                ) {
                    ((t.font = h.string),
                        (r = t.measureText(a.label).width),
                        (t.fillStyle = c.backdropColor));
                    const u = at(c.backdropPadding);
                    t.fillRect(
                        -r / 2 - u.left,
                        -o - h.size / 2 - u.top,
                        r + u.width,
                        h.size + u.height,
                    );
                }
                re(t, a.label, 0, -o, h, {
                    color: c.color,
                    strokeColor: c.textStrokeColor,
                    strokeWidth: c.textStrokeWidth,
                });
            }),
            t.restore());
    }

    drawTitle() {}
};
(S(Te, "id", "radialLinear"),
    S(Te, "defaults", {
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
        ticks: { showLabelBackdrop: !0, callback: Os.formatters.numeric },
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
    S(Te, "defaultRoutes", {
        "angleLines.color": "borderColor",
        "pointLabels.color": "color",
        "ticks.color": "color",
    }),
    S(Te, "descriptors", { angleLines: { _fallback: "grid" } }));
const cn = {
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
const ft = Object.keys(cn);
function fl(s, t) {
    return s - t;
}
function ml(s, t) {
    if (A(t)) return null;
    const e = s._adapter;
    const { parser: i, round: n, isoWeekday: o } = s._parseOpts;
    let r = t;
    return (
        typeof i === "function" && (r = i(r)),
        Z(r) || (r = typeof i === "string" ? e.parse(r, i) : e.parse(r)),
        r === null
            ? null
            : (n &&
                  (r =
                      n === "week" && (we(o) || o === !0)
                          ? e.startOf(r, "isoWeek", o)
                          : e.startOf(r, n)),
              +r)
    );
}
function gl(s, t, e, i) {
    const n = ft.length;
    for (let o = ft.indexOf(s); o < n - 1; ++o) {
        const r = cn[ft[o]];
        const a = r.steps ? r.steps : Number.MAX_SAFE_INTEGER;
        if (r.common && Math.ceil((e - t) / (a * r.size)) <= i) return ft[o];
    }
    return ft[n - 1];
}
function Tm(s, t, e, i, n) {
    for (let o = ft.length - 1; o >= ft.indexOf(e); o--) {
        const r = ft[o];
        if (cn[r].common && s._adapter.diff(n, i, r) >= t - 1) return r;
    }
    return ft[e ? ft.indexOf(e) : 0];
}
function Om(s) {
    for (let t = ft.indexOf(s) + 1, e = ft.length; t < e; ++t) {
        if (cn[ft[t]].common) return ft[t];
    }
}
function pl(s, t, e) {
    if (!e) s[t] = !0;
    else if (e.length) {
        const { lo: i, hi: n } = Wi(e, t);
        const o = e[i] >= t ? e[i] : e[n];
        s[o] = !0;
    }
}
function Dm(s, t, e, i) {
    const n = s._adapter;
    const o = +n.startOf(t[0].value, i);
    const r = t[t.length - 1].value;
    let a;
    let l;
    for (a = o; a <= r; a = +n.add(a, 1, i)) {
        ((l = e[a]), l >= 0 && (t[l].major = !0));
    }
    return t;
}
function yl(s, t, e) {
    const i = [];
    const n = {};
    const o = t.length;
    let r;
    let a;
    for (r = 0; r < o; ++r) {
        ((a = t[r]), (n[a] = r), i.push({ value: a, major: !1 }));
    }
    return o === 0 || !e ? i : Dm(s, i, n, e);
}
const De = class extends Ee {
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
        const i = t.time || (t.time = {});
        const n = (this._adapter = new No._date(t.adapters.date));
        (n.init(e),
            He(i.displayFormats, n.formats()),
            (this._parseOpts = {
                parser: i.parser,
                round: i.round,
                isoWeekday: i.isoWeekday,
            }),
            super.init(t),
            (this._normalized = e.normalized));
    }

    parse(t, e) {
        return t === void 0 ? null : ml(this, t);
    }

    beforeLayout() {
        (super.beforeLayout(),
            (this._cache = { data: [], labels: [], all: [] }));
    }

    determineDataLimits() {
        const t = this.options;
        const e = this._adapter;
        const i = t.time.unit || "day";
        let {
            min: n,
            max: o,
            minDefined: r,
            maxDefined: a,
        } = this.getUserBounds();
        function l(c) {
            (!r && !isNaN(c.min) && (n = Math.min(n, c.min)),
                !a && !isNaN(c.max) && (o = Math.max(o, c.max)));
        }
        ((!r || !a) &&
            (l(this._getLabelBounds()),
            (t.bounds !== "ticks" || t.ticks.source !== "labels") &&
                l(this.getMinMax(!1))),
            (n = Z(n) && !isNaN(n) ? n : +e.startOf(Date.now(), i)),
            (o = Z(o) && !isNaN(o) ? o : +e.endOf(Date.now(), i) + 1),
            (this.min = Math.min(n, o - 1)),
            (this.max = Math.max(n + 1, o)));
    }

    _getLabelBounds() {
        const t = this.getLabelTimestamps();
        let e = Number.POSITIVE_INFINITY;
        let i = Number.NEGATIVE_INFINITY;
        return (
            t.length && ((e = t[0]), (i = t[t.length - 1])),
            { min: e, max: i }
        );
    }

    buildTicks() {
        const t = this.options;
        const e = t.time;
        const i = t.ticks;
        const n =
            i.source === "labels"
                ? this.getLabelTimestamps()
                : this._generate();
        t.bounds === "ticks" &&
            n.length &&
            ((this.min = this._userMin || n[0]),
            (this.max = this._userMax || n[n.length - 1]));
        const o = this.min;
        const r = this.max;
        const a = Jr(n, o, r);
        return (
            (this._unit =
                e.unit ||
                (i.autoSkip
                    ? gl(
                          e.minUnit,
                          this.min,
                          this.max,
                          this._getLabelCapacity(o),
                      )
                    : Tm(this, a.length, e.minUnit, this.min, this.max))),
            (this._majorUnit =
                !i.major.enabled || this._unit === "year"
                    ? void 0
                    : Om(this._unit)),
            this.initOffsets(n),
            t.reverse && a.reverse(),
            yl(this, a, this._majorUnit)
        );
    }

    afterAutoSkip() {
        this.options.offsetAfterAutoskip &&
            this.initOffsets(this.ticks.map((t) => +t.value));
    }

    initOffsets(t = []) {
        let e = 0;
        let i = 0;
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
                ? (i = o)
                : (i = (o - this.getDecimalForValue(t[t.length - 2])) / 2));
        const r = t.length < 3 ? 0.5 : 0.25;
        ((e = tt(e, 0, r)),
            (i = tt(i, 0, r)),
            (this._offsets = { start: e, end: i, factor: 1 / (e + 1 + i) }));
    }

    _generate() {
        const t = this._adapter;
        const e = this.min;
        const i = this.max;
        const n = this.options;
        const o = n.time;
        const r = o.unit || gl(o.minUnit, e, i, this._getLabelCapacity(e));
        const a = I(n.ticks.stepSize, 1);
        const l = r === "week" ? o.isoWeekday : !1;
        const c = we(l) || l === !0;
        const h = {};
        let u = e;
        let d;
        let f;
        if (
            (c && (u = +t.startOf(u, "isoWeek", l)),
            (u = +t.startOf(u, c ? "day" : r)),
            t.diff(i, e, r) > 1e5 * a)
        ) {
            throw new Error(
                e +
                    " and " +
                    i +
                    " are too far apart with stepSize of " +
                    a +
                    " " +
                    r,
            );
        }
        const m = n.ticks.source === "data" && this.getDataTimestamps();
        for (d = u, f = 0; d < i; d = +t.add(d, a, r), f++) pl(h, d, m);
        return (
            (d === i || n.bounds === "ticks" || f === 1) && pl(h, d, m),
            Object.keys(h)
                .sort(fl)
                .map((g) => +g)
        );
    }

    getLabelForValue(t) {
        const e = this._adapter;
        const i = this.options.time;
        return i.tooltipFormat
            ? e.format(t, i.tooltipFormat)
            : e.format(t, i.displayFormats.datetime);
    }

    format(t, e) {
        const n = this.options.time.displayFormats;
        const o = this._unit;
        const r = e || n[o];
        return this._adapter.format(t, r);
    }

    _tickFormatFunction(t, e, i, n) {
        const o = this.options;
        const r = o.ticks.callback;
        if (r) return B(r, [t, e, i], this);
        const a = o.time.displayFormats;
        const l = this._unit;
        const c = this._majorUnit;
        const h = l && a[l];
        const u = c && a[c];
        const d = i[e];
        const f = c && u && d && d.major;
        return this._adapter.format(t, n || (f ? u : h));
    }

    generateTickLabels(t) {
        let e, i, n;
        for (e = 0, i = t.length; e < i; ++e) {
            ((n = t[e]), (n.label = this._tickFormatFunction(n.value, e, t)));
        }
    }

    getDecimalForValue(t) {
        return t === null ? NaN : (t - this.min) / (this.max - this.min);
    }

    getPixelForValue(t) {
        const e = this._offsets;
        const i = this.getDecimalForValue(t);
        return this.getPixelForDecimal((e.start + i) * e.factor);
    }

    getValueForPixel(t) {
        const e = this._offsets;
        const i = this.getDecimalForPixel(t) / e.factor - e.end;
        return this.min + i * (this.max - this.min);
    }

    _getLabelSize(t) {
        const e = this.options.ticks;
        const i = this.ctx.measureText(t).width;
        const n = wt(this.isHorizontal() ? e.maxRotation : e.minRotation);
        const o = Math.cos(n);
        const r = Math.sin(n);
        const a = this._resolveTickFontOptions(0).size;
        return { w: i * o + a * r, h: i * r + a * o };
    }

    _getLabelCapacity(t) {
        const e = this.options.time;
        const i = e.displayFormats;
        const n = i[e.unit] || i.millisecond;
        const o = this._tickFormatFunction(
            t,
            0,
            yl(this, [t], this._majorUnit),
            n,
        );
        const r = this._getLabelSize(o);
        const a =
            Math.floor(
                this.isHorizontal() ? this.width / r.w : this.height / r.h,
            ) - 1;
        return a > 0 ? a : 1;
    }

    getDataTimestamps() {
        let t = this._cache.data || [];
        let e;
        let i;
        if (t.length) return t;
        const n = this.getMatchingVisibleMetas();
        if (this._normalized && n.length) {
            return (this._cache.data =
                n[0].controller.getAllParsedValues(this));
        }
        for (e = 0, i = n.length; e < i; ++e) {
            t = t.concat(n[e].controller.getAllParsedValues(this));
        }
        return (this._cache.data = this.normalize(t));
    }

    getLabelTimestamps() {
        const t = this._cache.labels || [];
        let e;
        let i;
        if (t.length) return t;
        const n = this.getLabels();
        for (e = 0, i = n.length; e < i; ++e) t.push(ml(this, n[e]));
        return (this._cache.labels = this._normalized ? t : this.normalize(t));
    }

    normalize(t) {
        return $n(t.sort(fl));
    }
};
(S(De, "id", "time"),
    S(De, "defaults", {
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
function Ji(s, t, e) {
    let i = 0;
    let n = s.length - 1;
    let o;
    let r;
    let a;
    let l;
    e
        ? (t >= s[i].pos &&
              t <= s[n].pos &&
              ({ lo: i, hi: n } = Lt(s, "pos", t)),
          ({ pos: o, time: a } = s[i]),
          ({ pos: r, time: l } = s[n]))
        : (t >= s[i].time &&
              t <= s[n].time &&
              ({ lo: i, hi: n } = Lt(s, "time", t)),
          ({ time: o, pos: a } = s[i]),
          ({ time: r, pos: l } = s[n]));
    const c = r - o;
    return c ? a + ((l - a) * (t - o)) / c : a;
}
const Hs = class extends De {
    constructor(t) {
        (super(t),
            (this._table = []),
            (this._minPos = void 0),
            (this._tableRange = void 0));
    }

    initOffsets() {
        const t = this._getTimestampsForTable();
        const e = (this._table = this.buildLookupTable(t));
        ((this._minPos = Ji(e, this.min)),
            (this._tableRange = Ji(e, this.max) - this._minPos),
            super.initOffsets(t));
    }

    buildLookupTable(t) {
        const { min: e, max: i } = this;
        const n = [];
        const o = [];
        let r;
        let a;
        let l;
        let c;
        let h;
        for (r = 0, a = t.length; r < a; ++r) {
            ((c = t[r]), c >= e && c <= i && n.push(c));
        }
        if (n.length < 2) {
            return [
                { time: e, pos: 0 },
                { time: i, pos: 1 },
            ];
        }
        for (r = 0, a = n.length; r < a; ++r) {
            ((h = n[r + 1]),
                (l = n[r - 1]),
                (c = n[r]),
                Math.round((h + l) / 2) !== c &&
                    o.push({ time: c, pos: r / (a - 1) }));
        }
        return o;
    }

    _generate() {
        const t = this.min;
        const e = this.max;
        const i = super.getDataTimestamps();
        return (
            (!i.includes(t) || !i.length) && i.splice(0, 0, t),
            (!i.includes(e) || i.length === 1) && i.push(e),
            i.sort((n, o) => n - o)
        );
    }

    _getTimestampsForTable() {
        let t = this._cache.all || [];
        if (t.length) return t;
        const e = this.getDataTimestamps();
        const i = this.getLabelTimestamps();
        return (
            e.length && i.length
                ? (t = this.normalize(e.concat(i)))
                : (t = e.length ? e : i),
            (t = this._cache.all = t),
            t
        );
    }

    getDecimalForValue(t) {
        return (Ji(this._table, t) - this._minPos) / this._tableRange;
    }

    getValueForPixel(t) {
        const e = this._offsets;
        const i = this.getDecimalForPixel(t) / e.factor - e.end;
        return Ji(this._table, i * this._tableRange + this._minPos, !0);
    }
};
(S(Hs, "id", "timeseries"), S(Hs, "defaults", De.defaults));
const Em = Object.freeze({
    __proto__: null,
    CategoryScale: Ws,
    LinearScale: zs,
    LogarithmicScale: Vs,
    RadialLinearScale: Te,
    TimeScale: De,
    TimeSeriesScale: Hs,
});
const Wl = [Vu, uf, rm, Em];
St.register(...Wl);
const Ht = St;
const Xt = class extends Error {};
const hn = class extends Xt {
    constructor(t) {
        super(`Invalid DateTime: ${t.toMessage()}`);
    }
};
const un = class extends Xt {
    constructor(t) {
        super(`Invalid Interval: ${t.toMessage()}`);
    }
};
const dn = class extends Xt {
    constructor(t) {
        super(`Invalid Duration: ${t.toMessage()}`);
    }
};
const Dt = class extends Xt {};
const ns = class extends Xt {
    constructor(t) {
        super(`Invalid unit ${t}`);
    }
};
const K = class extends Xt {};
const Et = class extends Xt {
    constructor() {
        super("Zone is an abstract class");
    }
};
const T = "numeric";
const It = "short";
const bt = "long";
const he = { year: T, month: T, day: T };
const Zs = { year: T, month: It, day: T };
const Ro = { year: T, month: It, day: T, weekday: It };
const qs = { year: T, month: bt, day: T };
const Gs = { year: T, month: bt, day: T, weekday: bt };
const Xs = { hour: T, minute: T };
const Ks = { hour: T, minute: T, second: T };
const Js = { hour: T, minute: T, second: T, timeZoneName: It };
const Qs = { hour: T, minute: T, second: T, timeZoneName: bt };
const ti = { hour: T, minute: T, hourCycle: "h23" };
const ei = { hour: T, minute: T, second: T, hourCycle: "h23" };
const si = {
    hour: T,
    minute: T,
    second: T,
    hourCycle: "h23",
    timeZoneName: It,
};
const ii = {
    hour: T,
    minute: T,
    second: T,
    hourCycle: "h23",
    timeZoneName: bt,
};
const ni = { year: T, month: T, day: T, hour: T, minute: T };
const oi = { year: T, month: T, day: T, hour: T, minute: T, second: T };
const ri = { year: T, month: It, day: T, hour: T, minute: T };
const ai = { year: T, month: It, day: T, hour: T, minute: T, second: T };
const Wo = { year: T, month: It, day: T, weekday: It, hour: T, minute: T };
const li = { year: T, month: bt, day: T, hour: T, minute: T, timeZoneName: It };
const ci = {
    year: T,
    month: bt,
    day: T,
    hour: T,
    minute: T,
    second: T,
    timeZoneName: It,
};
const hi = {
    year: T,
    month: bt,
    day: T,
    weekday: bt,
    hour: T,
    minute: T,
    timeZoneName: bt,
};
const ui = {
    year: T,
    month: bt,
    day: T,
    weekday: bt,
    hour: T,
    minute: T,
    second: T,
    timeZoneName: bt,
};
const mt = class {
    get type() {
        throw new Et();
    }

    get name() {
        throw new Et();
    }

    get ianaName() {
        return this.name;
    }

    get isUniversal() {
        throw new Et();
    }

    offsetName(t, e) {
        throw new Et();
    }

    formatOffset(t, e) {
        throw new Et();
    }

    offset(t) {
        throw new Et();
    }

    equals(t) {
        throw new Et();
    }

    get isValid() {
        throw new Et();
    }
};
let zo = null;
const ue = class s extends mt {
    static get instance() {
        return (zo === null && (zo = new s()), zo);
    }

    get type() {
        return "system";
    }

    get name() {
        return new Intl.DateTimeFormat().resolvedOptions().timeZone;
    }

    get isUniversal() {
        return !1;
    }

    offsetName(t, { format: e, locale: i }) {
        return mn(t, e, i);
    }

    formatOffset(t, e) {
        return de(this.offset(t), e);
    }

    offset(t) {
        return -new Date(t).getTimezoneOffset();
    }

    equals(t) {
        return t.type === "system";
    }

    get isValid() {
        return !0;
    }
};
const Ho = new Map();
function Im(s) {
    let t = Ho.get(s);
    return (
        t === void 0 &&
            ((t = new Intl.DateTimeFormat("en-US", {
                hour12: !1,
                timeZone: s,
                year: "numeric",
                month: "2-digit",
                day: "2-digit",
                hour: "2-digit",
                minute: "2-digit",
                second: "2-digit",
                era: "short",
            })),
            Ho.set(s, t)),
        t
    );
}
const Cm = { year: 0, month: 1, day: 2, era: 3, hour: 4, minute: 5, second: 6 };
function Fm(s, t) {
    const e = s.format(t).replace(/\u200E/g, "");
    const i = /(\d+)\/(\d+)\/(\d+) (AD|BC),? (\d+):(\d+):(\d+)/.exec(e);
    const [, n, o, r, a, l, c, h] = i;
    return [r, n, o, a, l, c, h];
}
function Am(s, t) {
    const e = s.formatToParts(t);
    const i = [];
    for (let n = 0; n < e.length; n++) {
        const { type: o, value: r } = e[n];
        const a = Cm[o];
        o === "era" ? (i[a] = r) : D(a) || (i[a] = parseInt(r, 10));
    }
    return i;
}
const Vo = new Map();
const ht = class s extends mt {
    static create(t) {
        let e = Vo.get(t);
        return (e === void 0 && Vo.set(t, (e = new s(t))), e);
    }

    static resetCache() {
        (Vo.clear(), Ho.clear());
    }

    static isValidSpecifier(t) {
        return this.isValidZone(t);
    }

    static isValidZone(t) {
        if (!t) return !1;
        try {
            return (
                new Intl.DateTimeFormat("en-US", { timeZone: t }).format(),
                !0
            );
        } catch {
            return !1;
        }
    }

    constructor(t) {
        (super(), (this.zoneName = t), (this.valid = s.isValidZone(t)));
    }

    get type() {
        return "iana";
    }

    get name() {
        return this.zoneName;
    }

    get isUniversal() {
        return !1;
    }

    offsetName(t, { format: e, locale: i }) {
        return mn(t, e, i, this.name);
    }

    formatOffset(t, e) {
        return de(this.offset(t), e);
    }

    offset(t) {
        if (!this.valid) return NaN;
        const e = new Date(t);
        if (isNaN(e)) return NaN;
        const i = Im(this.name);
        let [n, o, r, a, l, c, h] = i.formatToParts ? Am(i, e) : Fm(i, e);
        a === "BC" && (n = -Math.abs(n) + 1);
        const d = os({
            year: n,
            month: o,
            day: r,
            hour: l === 24 ? 0 : l,
            minute: c,
            second: h,
            millisecond: 0,
        });
        let f = +e;
        const m = f % 1e3;
        return ((f -= m >= 0 ? m : 1e3 + m), (d - f) / (60 * 1e3));
    }

    equals(t) {
        return t.type === "iana" && t.name === this.name;
    }

    get isValid() {
        return this.valid;
    }
};
const zl = {};
function Lm(s, t = {}) {
    const e = JSON.stringify([s, t]);
    let i = zl[e];
    return (i || ((i = new Intl.ListFormat(s, t)), (zl[e] = i)), i);
}
const Bo = new Map();
function $o(s, t = {}) {
    const e = JSON.stringify([s, t]);
    let i = Bo.get(e);
    return (
        i === void 0 && ((i = new Intl.DateTimeFormat(s, t)), Bo.set(e, i)),
        i
    );
}
const jo = new Map();
function Pm(s, t = {}) {
    const e = JSON.stringify([s, t]);
    let i = jo.get(e);
    return (
        i === void 0 && ((i = new Intl.NumberFormat(s, t)), jo.set(e, i)),
        i
    );
}
const Uo = new Map();
function Nm(s, t = {}) {
    const { base: e, ...i } = t;
    const n = JSON.stringify([s, i]);
    let o = Uo.get(n);
    return (
        o === void 0 && ((o = new Intl.RelativeTimeFormat(s, t)), Uo.set(n, o)),
        o
    );
}
let di = null;
function Rm() {
    return (
        di || ((di = new Intl.DateTimeFormat().resolvedOptions().locale), di)
    );
}
const Yo = new Map();
function Vl(s) {
    let t = Yo.get(s);
    return (
        t === void 0 &&
            ((t = new Intl.DateTimeFormat(s).resolvedOptions()), Yo.set(s, t)),
        t
    );
}
const Zo = new Map();
function Wm(s) {
    let t = Zo.get(s);
    if (!t) {
        const e = new Intl.Locale(s);
        ((t = "getWeekInfo" in e ? e.getWeekInfo() : e.weekInfo),
            "minimalDays" in t || (t = { ...Hl, ...t }),
            Zo.set(s, t));
    }
    return t;
}
function zm(s) {
    const t = s.indexOf("-x-");
    t !== -1 && (s = s.substring(0, t));
    const e = s.indexOf("-u-");
    if (e === -1) return [s];
    {
        let i, n;
        try {
            ((i = $o(s).resolvedOptions()), (n = s));
        } catch {
            const l = s.substring(0, e);
            ((i = $o(l).resolvedOptions()), (n = l));
        }
        const { numberingSystem: o, calendar: r } = i;
        return [n, o, r];
    }
}
function Vm(s, t, e) {
    return (
        (e || t) &&
            (s.includes("-u-") || (s += "-u"),
            e && (s += `-ca-${e}`),
            t && (s += `-nu-${t}`)),
        s
    );
}
function Hm(s) {
    const t = [];
    for (let e = 1; e <= 12; e++) {
        const i = F.utc(2009, e, 1);
        t.push(s(i));
    }
    return t;
}
function Bm(s) {
    const t = [];
    for (let e = 1; e <= 7; e++) {
        const i = F.utc(2016, 11, 13 + e);
        t.push(s(i));
    }
    return t;
}
function gn(s, t, e, i) {
    const n = s.listingMode();
    return n === "error" ? null : n === "en" ? e(t) : i(t);
}
function $m(s) {
    return s.numberingSystem && s.numberingSystem !== "latn"
        ? !1
        : s.numberingSystem === "latn" ||
              !s.locale ||
              s.locale.startsWith("en") ||
              Vl(s.locale).numberingSystem === "latn";
}
const qo = class {
    constructor(t, e, i) {
        ((this.padTo = i.padTo || 0), (this.floor = i.floor || !1));
        const { padTo: n, floor: o, ...r } = i;
        if (!e || Object.keys(r).length > 0) {
            const a = { useGrouping: !1, ...i };
            (i.padTo > 0 && (a.minimumIntegerDigits = i.padTo),
                (this.inf = Pm(t, a)));
        }
    }

    format(t) {
        if (this.inf) {
            const e = this.floor ? Math.floor(t) : t;
            return this.inf.format(e);
        } else {
            const e = this.floor ? Math.floor(t) : rs(t, 3);
            return q(e, this.padTo);
        }
    }
};
const Go = class {
    constructor(t, e, i) {
        ((this.opts = i), (this.originalZone = void 0));
        let n;
        if (this.opts.timeZone) this.dt = t;
        else if (t.zone.type === "fixed") {
            const r = -1 * (t.offset / 60);
            const a = r >= 0 ? `Etc/GMT+${r}` : `Etc/GMT${r}`;
            t.offset !== 0 && ht.create(a).valid
                ? ((n = a), (this.dt = t))
                : ((n = "UTC"),
                  (this.dt =
                      t.offset === 0
                          ? t
                          : t.setZone("UTC").plus({ minutes: t.offset })),
                  (this.originalZone = t.zone));
        } else {
            t.zone.type === "system"
                ? (this.dt = t)
                : t.zone.type === "iana"
                  ? ((this.dt = t), (n = t.zone.name))
                  : ((n = "UTC"),
                    (this.dt = t.setZone("UTC").plus({ minutes: t.offset })),
                    (this.originalZone = t.zone));
        }
        const o = { ...this.opts };
        ((o.timeZone = o.timeZone || n), (this.dtf = $o(e, o)));
    }

    format() {
        return this.originalZone
            ? this.formatToParts()
                  .map(({ value: t }) => t)
                  .join("")
            : this.dtf.format(this.dt.toJSDate());
    }

    formatToParts() {
        const t = this.dtf.formatToParts(this.dt.toJSDate());
        return this.originalZone
            ? t.map((e) => {
                  if (e.type === "timeZoneName") {
                      const i = this.originalZone.offsetName(this.dt.ts, {
                          locale: this.dt.locale,
                          format: this.opts.timeZoneName,
                      });
                      return { ...e, value: i };
                  } else return e;
              })
            : t;
    }

    resolvedOptions() {
        return this.dtf.resolvedOptions();
    }
};
const Xo = class {
    constructor(t, e, i) {
        ((this.opts = { style: "long", ...i }),
            !e && pn() && (this.rtf = Nm(t, i)));
    }

    format(t, e) {
        return this.rtf
            ? this.rtf.format(t, e)
            : Bl(e, t, this.opts.numeric, this.opts.style !== "long");
    }

    formatToParts(t, e) {
        return this.rtf ? this.rtf.formatToParts(t, e) : [];
    }
};
var Hl = { firstDay: 1, minimalDays: 4, weekend: [6, 7] };
const W = class s {
    static fromOpts(t) {
        return s.create(
            t.locale,
            t.numberingSystem,
            t.outputCalendar,
            t.weekSettings,
            t.defaultToEN,
        );
    }

    static create(t, e, i, n, o = !1) {
        const r = t || R.defaultLocale;
        const a = r || (o ? "en-US" : Rm());
        const l = e || R.defaultNumberingSystem;
        const c = i || R.defaultOutputCalendar;
        const h = fi(n) || R.defaultWeekSettings;
        return new s(a, l, c, h, r);
    }

    static resetCache() {
        ((di = null),
            Bo.clear(),
            jo.clear(),
            Uo.clear(),
            Yo.clear(),
            Zo.clear());
    }

    static fromObject({
        locale: t,
        numberingSystem: e,
        outputCalendar: i,
        weekSettings: n,
    } = {}) {
        return s.create(t, e, i, n);
    }

    constructor(t, e, i, n, o) {
        const [r, a, l] = zm(t);
        ((this.locale = r),
            (this.numberingSystem = e || a || null),
            (this.outputCalendar = i || l || null),
            (this.weekSettings = n),
            (this.intl = Vm(
                this.locale,
                this.numberingSystem,
                this.outputCalendar,
            )),
            (this.weekdaysCache = { format: {}, standalone: {} }),
            (this.monthsCache = { format: {}, standalone: {} }),
            (this.meridiemCache = null),
            (this.eraCache = {}),
            (this.specifiedLocale = o),
            (this.fastNumbersCached = null));
    }

    get fastNumbers() {
        return (
            this.fastNumbersCached == null &&
                (this.fastNumbersCached = $m(this)),
            this.fastNumbersCached
        );
    }

    listingMode() {
        const t = this.isEnglish();
        const e =
            (this.numberingSystem === null ||
                this.numberingSystem === "latn") &&
            (this.outputCalendar === null || this.outputCalendar === "gregory");
        return t && e ? "en" : "intl";
    }

    clone(t) {
        return !t || Object.getOwnPropertyNames(t).length === 0
            ? this
            : s.create(
                  t.locale || this.specifiedLocale,
                  t.numberingSystem || this.numberingSystem,
                  t.outputCalendar || this.outputCalendar,
                  fi(t.weekSettings) || this.weekSettings,
                  t.defaultToEN || !1,
              );
    }

    redefaultToEN(t = {}) {
        return this.clone({ ...t, defaultToEN: !0 });
    }

    redefaultToSystem(t = {}) {
        return this.clone({ ...t, defaultToEN: !1 });
    }

    months(t, e = !1) {
        return gn(this, t, Ko, () => {
            const i = this.intl === "ja" || this.intl.startsWith("ja-");
            e &= !i;
            const n = e ? { month: t, day: "numeric" } : { month: t };
            const o = e ? "format" : "standalone";
            if (!this.monthsCache[o][t]) {
                const r = i
                    ? (a) => this.dtFormatter(a, n).format()
                    : (a) => this.extract(a, n, "month");
                this.monthsCache[o][t] = Hm(r);
            }
            return this.monthsCache[o][t];
        });
    }

    weekdays(t, e = !1) {
        return gn(this, t, Jo, () => {
            const i = e
                ? { weekday: t, year: "numeric", month: "long", day: "numeric" }
                : { weekday: t };
            const n = e ? "format" : "standalone";
            return (
                this.weekdaysCache[n][t] ||
                    (this.weekdaysCache[n][t] = Bm((o) =>
                        this.extract(o, i, "weekday"),
                    )),
                this.weekdaysCache[n][t]
            );
        });
    }

    meridiems() {
        return gn(
            this,
            void 0,
            () => Qo,
            () => {
                if (!this.meridiemCache) {
                    const t = { hour: "numeric", hourCycle: "h12" };
                    this.meridiemCache = [
                        F.utc(2016, 11, 13, 9),
                        F.utc(2016, 11, 13, 19),
                    ].map((e) => this.extract(e, t, "dayperiod"));
                }
                return this.meridiemCache;
            },
        );
    }

    eras(t) {
        return gn(this, t, tr, () => {
            const e = { era: t };
            return (
                this.eraCache[t] ||
                    (this.eraCache[t] = [
                        F.utc(-40, 1, 1),
                        F.utc(2017, 1, 1),
                    ].map((i) => this.extract(i, e, "era"))),
                this.eraCache[t]
            );
        });
    }

    extract(t, e, i) {
        const n = this.dtFormatter(t, e);
        const o = n.formatToParts();
        const r = o.find((a) => a.type.toLowerCase() === i);
        return r ? r.value : null;
    }

    numberFormatter(t = {}) {
        return new qo(this.intl, t.forceSimple || this.fastNumbers, t);
    }

    dtFormatter(t, e = {}) {
        return new Go(t, this.intl, e);
    }

    relFormatter(t = {}) {
        return new Xo(this.intl, this.isEnglish(), t);
    }

    listFormatter(t = {}) {
        return Lm(this.intl, t);
    }

    isEnglish() {
        return (
            this.locale === "en" ||
            this.locale.toLowerCase() === "en-us" ||
            Vl(this.intl).locale.startsWith("en-us")
        );
    }

    getWeekSettings() {
        return this.weekSettings
            ? this.weekSettings
            : yn()
              ? Wm(this.locale)
              : Hl;
    }

    getStartOfWeek() {
        return this.getWeekSettings().firstDay;
    }

    getMinDaysInFirstWeek() {
        return this.getWeekSettings().minimalDays;
    }

    getWeekendDays() {
        return this.getWeekSettings().weekend;
    }

    equals(t) {
        return (
            this.locale === t.locale &&
            this.numberingSystem === t.numberingSystem &&
            this.outputCalendar === t.outputCalendar
        );
    }

    toString() {
        return `Locale(${this.locale}, ${this.numberingSystem}, ${this.outputCalendar})`;
    }
};
let sr = null;
const et = class s extends mt {
    static get utcInstance() {
        return (sr === null && (sr = new s(0)), sr);
    }

    static instance(t) {
        return t === 0 ? s.utcInstance : new s(t);
    }

    static parseSpecifier(t) {
        if (t) {
            const e = t.match(/^utc(?:([+-]\d{1,2})(?::(\d{2}))?)?$/i);
            if (e) return new s(Ie(e[1], e[2]));
        }
        return null;
    }

    constructor(t) {
        (super(), (this.fixed = t));
    }

    get type() {
        return "fixed";
    }

    get name() {
        return this.fixed === 0 ? "UTC" : `UTC${de(this.fixed, "narrow")}`;
    }

    get ianaName() {
        return this.fixed === 0
            ? "Etc/UTC"
            : `Etc/GMT${de(-this.fixed, "narrow")}`;
    }

    offsetName() {
        return this.name;
    }

    formatOffset(t, e) {
        return de(this.fixed, e);
    }

    get isUniversal() {
        return !0;
    }

    offset() {
        return this.fixed;
    }

    equals(t) {
        return t.type === "fixed" && t.fixed === this.fixed;
    }

    get isValid() {
        return !0;
    }
};
const as = class extends mt {
    constructor(t) {
        (super(), (this.zoneName = t));
    }

    get type() {
        return "invalid";
    }

    get name() {
        return this.zoneName;
    }

    get isUniversal() {
        return !1;
    }

    offsetName() {
        return null;
    }

    formatOffset() {
        return "";
    }

    offset() {
        return NaN;
    }

    equals() {
        return !1;
    }

    get isValid() {
        return !1;
    }
};
function Ct(s, t) {
    let e;
    if (D(s) || s === null) return t;
    if (s instanceof mt) return s;
    if ($l(s)) {
        const i = s.toLowerCase();
        return i === "default"
            ? t
            : i === "local" || i === "system"
              ? ue.instance
              : i === "utc" || i === "gmt"
                ? et.utcInstance
                : et.parseSpecifier(i) || ht.create(s);
    } else {
        return Ft(s)
            ? et.instance(s)
            : typeof s === "object" &&
                "offset" in s &&
                typeof s.offset === "function"
              ? s
              : new as(s);
    }
}
const nr = {
    arab: "[\u0660-\u0669]",
    arabext: "[\u06F0-\u06F9]",
    bali: "[\u1B50-\u1B59]",
    beng: "[\u09E6-\u09EF]",
    deva: "[\u0966-\u096F]",
    fullwide: "[\uFF10-\uFF19]",
    gujr: "[\u0AE6-\u0AEF]",
    hanidec:
        "[\u3007|\u4E00|\u4E8C|\u4E09|\u56DB|\u4E94|\u516D|\u4E03|\u516B|\u4E5D]",
    khmr: "[\u17E0-\u17E9]",
    knda: "[\u0CE6-\u0CEF]",
    laoo: "[\u0ED0-\u0ED9]",
    limb: "[\u1946-\u194F]",
    mlym: "[\u0D66-\u0D6F]",
    mong: "[\u1810-\u1819]",
    mymr: "[\u1040-\u1049]",
    orya: "[\u0B66-\u0B6F]",
    tamldec: "[\u0BE6-\u0BEF]",
    telu: "[\u0C66-\u0C6F]",
    thai: "[\u0E50-\u0E59]",
    tibt: "[\u0F20-\u0F29]",
    latn: "\\d",
};
const jl = {
    arab: [1632, 1641],
    arabext: [1776, 1785],
    bali: [6992, 7001],
    beng: [2534, 2543],
    deva: [2406, 2415],
    fullwide: [65296, 65303],
    gujr: [2790, 2799],
    khmr: [6112, 6121],
    knda: [3302, 3311],
    laoo: [3792, 3801],
    limb: [6470, 6479],
    mlym: [3430, 3439],
    mong: [6160, 6169],
    mymr: [4160, 4169],
    orya: [2918, 2927],
    tamldec: [3046, 3055],
    telu: [3174, 3183],
    thai: [3664, 3673],
    tibt: [3872, 3881],
};
const jm = nr.hanidec.replace(/[\[|\]]/g, "").split("");
function Ul(s) {
    let t = parseInt(s, 10);
    if (isNaN(t)) {
        t = "";
        for (let e = 0; e < s.length; e++) {
            const i = s.charCodeAt(e);
            if (s[e].search(nr.hanidec) !== -1) t += jm.indexOf(s[e]);
            else {
                for (const n in jl) {
                    const [o, r] = jl[n];
                    i >= o && i <= r && (t += i - o);
                }
            }
        }
        return parseInt(t, 10);
    } else return t;
}
const ir = new Map();
function Yl() {
    ir.clear();
}
function kt({ numberingSystem: s }, t = "") {
    const e = s || "latn";
    let i = ir.get(e);
    i === void 0 && ((i = new Map()), ir.set(e, i));
    let n = i.get(t);
    return (n === void 0 && ((n = new RegExp(`${nr[e]}${t}`)), i.set(t, n)), n);
}
let Zl = () => Date.now();
let ql = "system";
let Gl = null;
let Xl = null;
let Kl = null;
let Jl = 60;
let Ql;
let tc = null;
var R = class {
    static get now() {
        return Zl;
    }

    static set now(t) {
        Zl = t;
    }

    static set defaultZone(t) {
        ql = t;
    }

    static get defaultZone() {
        return Ct(ql, ue.instance);
    }

    static get defaultLocale() {
        return Gl;
    }

    static set defaultLocale(t) {
        Gl = t;
    }

    static get defaultNumberingSystem() {
        return Xl;
    }

    static set defaultNumberingSystem(t) {
        Xl = t;
    }

    static get defaultOutputCalendar() {
        return Kl;
    }

    static set defaultOutputCalendar(t) {
        Kl = t;
    }

    static get defaultWeekSettings() {
        return tc;
    }

    static set defaultWeekSettings(t) {
        tc = fi(t);
    }

    static get twoDigitCutoffYear() {
        return Jl;
    }

    static set twoDigitCutoffYear(t) {
        Jl = t % 100;
    }

    static get throwOnInvalid() {
        return Ql;
    }

    static set throwOnInvalid(t) {
        Ql = t;
    }

    static resetCaches() {
        (W.resetCache(), ht.resetCache(), F.resetCache(), Yl());
    }
};
const st = class {
    constructor(t, e) {
        ((this.reason = t), (this.explanation = e));
    }

    toMessage() {
        return this.explanation
            ? `${this.reason}: ${this.explanation}`
            : this.reason;
    }
};
const ec = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
const sc = [0, 31, 60, 91, 121, 152, 182, 213, 244, 274, 305, 335];
function Mt(s, t) {
    return new st(
        "unit out of range",
        `you specified ${t} (of type ${typeof t}) as a ${s}, which is invalid`,
    );
}
function bn(s, t, e) {
    const i = new Date(Date.UTC(s, t - 1, e));
    s < 100 && s >= 0 && i.setUTCFullYear(i.getUTCFullYear() - 1900);
    const n = i.getUTCDay();
    return n === 0 ? 7 : n;
}
function ic(s, t, e) {
    return e + (Fe(s) ? sc : ec)[t - 1];
}
function nc(s, t) {
    const e = Fe(s) ? sc : ec;
    const i = e.findIndex((o) => o < t);
    const n = t - e[i];
    return { month: i + 1, day: n };
}
function xn(s, t) {
    return ((s - t + 7) % 7) + 1;
}
function mi(s, t = 4, e = 1) {
    const { year: i, month: n, day: o } = s;
    const r = ic(i, n, o);
    const a = xn(bn(i, n, o), e);
    let l = Math.floor((r - a + 14 - t) / 7);
    let c;
    return (
        l < 1
            ? ((c = i - 1), (l = Ce(c, t, e)))
            : l > Ce(i, t, e)
              ? ((c = i + 1), (l = 1))
              : (c = i),
        { weekYear: c, weekNumber: l, weekday: a, ...pi(s) }
    );
}
function or(s, t = 4, e = 1) {
    const { weekYear: i, weekNumber: n, weekday: o } = s;
    const r = xn(bn(i, 1, t), e);
    const a = fe(i);
    let l = n * 7 + o - r - 7 + t;
    let c;
    l < 1
        ? ((c = i - 1), (l += fe(c)))
        : l > a
          ? ((c = i + 1), (l -= fe(i)))
          : (c = i);
    const { month: h, day: u } = nc(c, l);
    return { year: c, month: h, day: u, ...pi(s) };
}
function _n(s) {
    const { year: t, month: e, day: i } = s;
    const n = ic(t, e, i);
    return { year: t, ordinal: n, ...pi(s) };
}
function rr(s) {
    const { year: t, ordinal: e } = s;
    const { month: i, day: n } = nc(t, e);
    return { year: t, month: i, day: n, ...pi(s) };
}
function ar(s, t) {
    if (!D(s.localWeekday) || !D(s.localWeekNumber) || !D(s.localWeekYear)) {
        if (!D(s.weekday) || !D(s.weekNumber) || !D(s.weekYear)) {
            throw new Dt(
                "Cannot mix locale-based week fields with ISO-based week fields",
            );
        }
        return (
            D(s.localWeekday) || (s.weekday = s.localWeekday),
            D(s.localWeekNumber) || (s.weekNumber = s.localWeekNumber),
            D(s.localWeekYear) || (s.weekYear = s.localWeekYear),
            delete s.localWeekday,
            delete s.localWeekNumber,
            delete s.localWeekYear,
            {
                minDaysInFirstWeek: t.getMinDaysInFirstWeek(),
                startOfWeek: t.getStartOfWeek(),
            }
        );
    } else return { minDaysInFirstWeek: 4, startOfWeek: 1 };
}
function oc(s, t = 4, e = 1) {
    const i = gi(s.weekYear);
    const n = xt(s.weekNumber, 1, Ce(s.weekYear, t, e));
    const o = xt(s.weekday, 1, 7);
    return i
        ? n
            ? o
                ? !1
                : Mt("weekday", s.weekday)
            : Mt("week", s.weekNumber)
        : Mt("weekYear", s.weekYear);
}
function rc(s) {
    const t = gi(s.year);
    const e = xt(s.ordinal, 1, fe(s.year));
    return t ? (e ? !1 : Mt("ordinal", s.ordinal)) : Mt("year", s.year);
}
function lr(s) {
    const t = gi(s.year);
    const e = xt(s.month, 1, 12);
    const i = xt(s.day, 1, ls(s.year, s.month));
    return t
        ? e
            ? i
                ? !1
                : Mt("day", s.day)
            : Mt("month", s.month)
        : Mt("year", s.year);
}
function cr(s) {
    const { hour: t, minute: e, second: i, millisecond: n } = s;
    const o = xt(t, 0, 23) || (t === 24 && e === 0 && i === 0 && n === 0);
    const r = xt(e, 0, 59);
    const a = xt(i, 0, 59);
    const l = xt(n, 0, 999);
    return o
        ? r
            ? a
                ? l
                    ? !1
                    : Mt("millisecond", n)
                : Mt("second", i)
            : Mt("minute", e)
        : Mt("hour", t);
}
function D(s) {
    return typeof s > "u";
}
function Ft(s) {
    return typeof s === "number";
}
function gi(s) {
    return typeof s === "number" && s % 1 === 0;
}
function $l(s) {
    return typeof s === "string";
}
function lc(s) {
    return Object.prototype.toString.call(s) === "[object Date]";
}
function pn() {
    try {
        return typeof Intl < "u" && !!Intl.RelativeTimeFormat;
    } catch {
        return !1;
    }
}
function yn() {
    try {
        return (
            typeof Intl < "u" &&
            !!Intl.Locale &&
            ("weekInfo" in Intl.Locale.prototype ||
                "getWeekInfo" in Intl.Locale.prototype)
        );
    } catch {
        return !1;
    }
}
function cc(s) {
    return Array.isArray(s) ? s : [s];
}
function hr(s, t, e) {
    if (s.length !== 0) {
        return s.reduce((i, n) => {
            const o = [t(n), n];
            return i && e(i[0], o[0]) === i[0] ? i : o;
        }, null)[1];
    }
}
function hc(s, t) {
    return t.reduce((e, i) => ((e[i] = s[i]), e), {});
}
function me(s, t) {
    return Object.prototype.hasOwnProperty.call(s, t);
}
function fi(s) {
    if (s == null) return null;
    if (typeof s !== "object") throw new K("Week settings must be an object");
    if (
        !xt(s.firstDay, 1, 7) ||
        !xt(s.minimalDays, 1, 7) ||
        !Array.isArray(s.weekend) ||
        s.weekend.some((t) => !xt(t, 1, 7))
    ) {
        throw new K("Invalid week settings");
    }
    return {
        firstDay: s.firstDay,
        minimalDays: s.minimalDays,
        weekend: Array.from(s.weekend),
    };
}
function xt(s, t, e) {
    return gi(s) && s >= t && s <= e;
}
function Um(s, t) {
    return s - t * Math.floor(s / t);
}
function q(s, t = 2) {
    const e = s < 0;
    let i;
    return (
        e
            ? (i = "-" + ("" + -s).padStart(t, "0"))
            : (i = ("" + s).padStart(t, "0")),
        i
    );
}
function Kt(s) {
    if (!(D(s) || s === null || s === "")) return parseInt(s, 10);
}
function ge(s) {
    if (!(D(s) || s === null || s === "")) return parseFloat(s);
}
function yi(s) {
    if (!(D(s) || s === null || s === "")) {
        const t = parseFloat("0." + s) * 1e3;
        return Math.floor(t);
    }
}
function rs(s, t, e = "round") {
    const i = 10 ** t;
    switch (e) {
        case "expand":
            return s > 0 ? Math.ceil(s * i) / i : Math.floor(s * i) / i;
        case "trunc":
            return Math.trunc(s * i) / i;
        case "round":
            return Math.round(s * i) / i;
        case "floor":
            return Math.floor(s * i) / i;
        case "ceil":
            return Math.ceil(s * i) / i;
        default:
            throw new RangeError(`Value rounding ${e} is out of range`);
    }
}
function Fe(s) {
    return s % 4 === 0 && (s % 100 !== 0 || s % 400 === 0);
}
function fe(s) {
    return Fe(s) ? 366 : 365;
}
function ls(s, t) {
    const e = Um(t - 1, 12) + 1;
    const i = s + (t - e) / 12;
    return e === 2
        ? Fe(i)
            ? 29
            : 28
        : [31, null, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][e - 1];
}
function os(s) {
    let t = Date.UTC(
        s.year,
        s.month - 1,
        s.day,
        s.hour,
        s.minute,
        s.second,
        s.millisecond,
    );
    return (
        s.year < 100 &&
            s.year >= 0 &&
            ((t = new Date(t)), t.setUTCFullYear(s.year, s.month - 1, s.day)),
        +t
    );
}
function ac(s, t, e) {
    return -xn(bn(s, 1, t), e) + t - 1;
}
function Ce(s, t = 4, e = 1) {
    const i = ac(s, t, e);
    const n = ac(s + 1, t, e);
    return (fe(s) - i + n) / 7;
}
function bi(s) {
    return s > 99 ? s : s > R.twoDigitCutoffYear ? 1900 + s : 2e3 + s;
}
function mn(s, t, e, i = null) {
    const n = new Date(s);
    const o = {
        hourCycle: "h23",
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
    };
    i && (o.timeZone = i);
    const r = { timeZoneName: t, ...o };
    const a = new Intl.DateTimeFormat(e, r)
        .formatToParts(n)
        .find((l) => l.type.toLowerCase() === "timezonename");
    return a ? a.value : null;
}
function Ie(s, t) {
    let e = parseInt(s, 10);
    Number.isNaN(e) && (e = 0);
    const i = parseInt(t, 10) || 0;
    const n = e < 0 || Object.is(e, -0) ? -i : i;
    return e * 60 + n;
}
function ur(s) {
    const t = Number(s);
    if (typeof s === "boolean" || s === "" || !Number.isFinite(t)) {
        throw new K(`Invalid unit value ${s}`);
    }
    return t;
}
function cs(s, t) {
    const e = {};
    for (const i in s) {
        if (me(s, i)) {
            const n = s[i];
            if (n == null) continue;
            e[t(i)] = ur(n);
        }
    }
    return e;
}
function de(s, t) {
    const e = Math.trunc(Math.abs(s / 60));
    const i = Math.trunc(Math.abs(s % 60));
    const n = s >= 0 ? "+" : "-";
    switch (t) {
        case "short":
            return `${n}${q(e, 2)}:${q(i, 2)}`;
        case "narrow":
            return `${n}${e}${i > 0 ? `:${i}` : ""}`;
        case "techie":
            return `${n}${q(e, 2)}${q(i, 2)}`;
        default:
            throw new RangeError(
                `Value format ${t} is out of range for property format`,
            );
    }
}
function pi(s) {
    return hc(s, ["hour", "minute", "second", "millisecond"]);
}
const Ym = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
];
const dr = [
    "Jan",
    "Feb",
    "Mar",
    "Apr",
    "May",
    "Jun",
    "Jul",
    "Aug",
    "Sep",
    "Oct",
    "Nov",
    "Dec",
];
const Zm = ["J", "F", "M", "A", "M", "J", "J", "A", "S", "O", "N", "D"];
function Ko(s) {
    switch (s) {
        case "narrow":
            return [...Zm];
        case "short":
            return [...dr];
        case "long":
            return [...Ym];
        case "numeric":
            return [
                "1",
                "2",
                "3",
                "4",
                "5",
                "6",
                "7",
                "8",
                "9",
                "10",
                "11",
                "12",
            ];
        case "2-digit":
            return [
                "01",
                "02",
                "03",
                "04",
                "05",
                "06",
                "07",
                "08",
                "09",
                "10",
                "11",
                "12",
            ];
        default:
            return null;
    }
}
const fr = [
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday",
    "Sunday",
];
const mr = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
const qm = ["M", "T", "W", "T", "F", "S", "S"];
function Jo(s) {
    switch (s) {
        case "narrow":
            return [...qm];
        case "short":
            return [...mr];
        case "long":
            return [...fr];
        case "numeric":
            return ["1", "2", "3", "4", "5", "6", "7"];
        default:
            return null;
    }
}
var Qo = ["AM", "PM"];
const Gm = ["Before Christ", "Anno Domini"];
const Xm = ["BC", "AD"];
const Km = ["B", "A"];
function tr(s) {
    switch (s) {
        case "narrow":
            return [...Km];
        case "short":
            return [...Xm];
        case "long":
            return [...Gm];
        default:
            return null;
    }
}
function uc(s) {
    return Qo[s.hour < 12 ? 0 : 1];
}
function dc(s, t) {
    return Jo(t)[s.weekday - 1];
}
function fc(s, t) {
    return Ko(t)[s.month - 1];
}
function mc(s, t) {
    return tr(t)[s.year < 0 ? 0 : 1];
}
function Bl(s, t, e = "always", i = !1) {
    const n = {
        years: ["year", "yr."],
        quarters: ["quarter", "qtr."],
        months: ["month", "mo."],
        weeks: ["week", "wk."],
        days: ["day", "day", "days"],
        hours: ["hour", "hr."],
        minutes: ["minute", "min."],
        seconds: ["second", "sec."],
    };
    const o = ["hours", "minutes", "seconds"].indexOf(s) === -1;
    if (e === "auto" && o) {
        const u = s === "days";
        switch (t) {
            case 1:
                return u ? "tomorrow" : `next ${n[s][0]}`;
            case -1:
                return u ? "yesterday" : `last ${n[s][0]}`;
            case 0:
                return u ? "today" : `this ${n[s][0]}`;
            default:
        }
    }
    const r = Object.is(t, -0) || t < 0;
    const a = Math.abs(t);
    const l = a === 1;
    const c = n[s];
    const h = i ? (l ? c[1] : c[2] || c[1]) : l ? n[s][0] : s;
    return r ? `${a} ${h} ago` : `in ${a} ${h}`;
}
function gc(s, t) {
    let e = "";
    for (const i of s) i.literal ? (e += i.val) : (e += t(i.val));
    return e;
}
const Jm = {
    D: he,
    DD: Zs,
    DDD: qs,
    DDDD: Gs,
    t: Xs,
    tt: Ks,
    ttt: Js,
    tttt: Qs,
    T: ti,
    TT: ei,
    TTT: si,
    TTTT: ii,
    f: ni,
    ff: ri,
    fff: li,
    ffff: hi,
    F: oi,
    FF: ai,
    FFF: ci,
    FFFF: ui,
};
const it = class s {
    static create(t, e = {}) {
        return new s(t, e);
    }

    static parseFormat(t) {
        let e = null;
        let i = "";
        let n = !1;
        const o = [];
        for (let r = 0; r < t.length; r++) {
            const a = t.charAt(r);
            a === "'"
                ? ((i.length > 0 || n) &&
                      o.push({
                          literal: n || /^\s+$/.test(i),
                          val: i === "" ? "'" : i,
                      }),
                  (e = null),
                  (i = ""),
                  (n = !n))
                : n || a === e
                  ? (i += a)
                  : (i.length > 0 &&
                        o.push({ literal: /^\s+$/.test(i), val: i }),
                    (i = a),
                    (e = a));
        }
        return (
            i.length > 0 && o.push({ literal: n || /^\s+$/.test(i), val: i }),
            o
        );
    }

    static macroTokenToFormatOpts(t) {
        return Jm[t];
    }

    constructor(t, e) {
        ((this.opts = e), (this.loc = t), (this.systemLoc = null));
    }

    formatWithSystemDefault(t, e) {
        return (
            this.systemLoc === null &&
                (this.systemLoc = this.loc.redefaultToSystem()),
            this.systemLoc.dtFormatter(t, { ...this.opts, ...e }).format()
        );
    }

    dtFormatter(t, e = {}) {
        return this.loc.dtFormatter(t, { ...this.opts, ...e });
    }

    formatDateTime(t, e) {
        return this.dtFormatter(t, e).format();
    }

    formatDateTimeParts(t, e) {
        return this.dtFormatter(t, e).formatToParts();
    }

    formatInterval(t, e) {
        return this.dtFormatter(t.start, e).dtf.formatRange(
            t.start.toJSDate(),
            t.end.toJSDate(),
        );
    }

    resolvedOptions(t, e) {
        return this.dtFormatter(t, e).resolvedOptions();
    }

    num(t, e = 0, i = void 0) {
        if (this.opts.forceSimple) return q(t, e);
        const n = { ...this.opts };
        return (
            e > 0 && (n.padTo = e),
            i && (n.signDisplay = i),
            this.loc.numberFormatter(n).format(t)
        );
    }

    formatDateTimeFromString(t, e) {
        const i = this.loc.listingMode() === "en";
        const n =
            this.loc.outputCalendar && this.loc.outputCalendar !== "gregory";
        const o = (f, m) => this.loc.extract(t, f, m);
        const r = (f) =>
            t.isOffsetFixed && t.offset === 0 && f.allowZ
                ? "Z"
                : t.isValid
                  ? t.zone.formatOffset(t.ts, f.format)
                  : "";
        const a = () =>
            i ? uc(t) : o({ hour: "numeric", hourCycle: "h12" }, "dayperiod");
        const l = (f, m) =>
            i
                ? fc(t, f)
                : o(m ? { month: f } : { month: f, day: "numeric" }, "month");
        const c = (f, m) =>
            i
                ? dc(t, f)
                : o(
                      m
                          ? { weekday: f }
                          : { weekday: f, month: "long", day: "numeric" },
                      "weekday",
                  );
        const h = (f) => {
            const m = s.macroTokenToFormatOpts(f);
            return m ? this.formatWithSystemDefault(t, m) : f;
        };
        const u = (f) => (i ? mc(t, f) : o({ era: f }, "era"));
        const d = (f) => {
            switch (f) {
                case "S":
                    return this.num(t.millisecond);
                case "u":
                case "SSS":
                    return this.num(t.millisecond, 3);
                case "s":
                    return this.num(t.second);
                case "ss":
                    return this.num(t.second, 2);
                case "uu":
                    return this.num(Math.floor(t.millisecond / 10), 2);
                case "uuu":
                    return this.num(Math.floor(t.millisecond / 100));
                case "m":
                    return this.num(t.minute);
                case "mm":
                    return this.num(t.minute, 2);
                case "h":
                    return this.num(t.hour % 12 === 0 ? 12 : t.hour % 12);
                case "hh":
                    return this.num(t.hour % 12 === 0 ? 12 : t.hour % 12, 2);
                case "H":
                    return this.num(t.hour);
                case "HH":
                    return this.num(t.hour, 2);
                case "Z":
                    return r({ format: "narrow", allowZ: this.opts.allowZ });
                case "ZZ":
                    return r({ format: "short", allowZ: this.opts.allowZ });
                case "ZZZ":
                    return r({ format: "techie", allowZ: this.opts.allowZ });
                case "ZZZZ":
                    return t.zone.offsetName(t.ts, {
                        format: "short",
                        locale: this.loc.locale,
                    });
                case "ZZZZZ":
                    return t.zone.offsetName(t.ts, {
                        format: "long",
                        locale: this.loc.locale,
                    });
                case "z":
                    return t.zoneName;
                case "a":
                    return a();
                case "d":
                    return n ? o({ day: "numeric" }, "day") : this.num(t.day);
                case "dd":
                    return n
                        ? o({ day: "2-digit" }, "day")
                        : this.num(t.day, 2);
                case "c":
                    return this.num(t.weekday);
                case "ccc":
                    return c("short", !0);
                case "cccc":
                    return c("long", !0);
                case "ccccc":
                    return c("narrow", !0);
                case "E":
                    return this.num(t.weekday);
                case "EEE":
                    return c("short", !1);
                case "EEEE":
                    return c("long", !1);
                case "EEEEE":
                    return c("narrow", !1);
                case "L":
                    return n
                        ? o({ month: "numeric", day: "numeric" }, "month")
                        : this.num(t.month);
                case "LL":
                    return n
                        ? o({ month: "2-digit", day: "numeric" }, "month")
                        : this.num(t.month, 2);
                case "LLL":
                    return l("short", !0);
                case "LLLL":
                    return l("long", !0);
                case "LLLLL":
                    return l("narrow", !0);
                case "M":
                    return n
                        ? o({ month: "numeric" }, "month")
                        : this.num(t.month);
                case "MM":
                    return n
                        ? o({ month: "2-digit" }, "month")
                        : this.num(t.month, 2);
                case "MMM":
                    return l("short", !1);
                case "MMMM":
                    return l("long", !1);
                case "MMMMM":
                    return l("narrow", !1);
                case "y":
                    return n
                        ? o({ year: "numeric" }, "year")
                        : this.num(t.year);
                case "yy":
                    return n
                        ? o({ year: "2-digit" }, "year")
                        : this.num(t.year.toString().slice(-2), 2);
                case "yyyy":
                    return n
                        ? o({ year: "numeric" }, "year")
                        : this.num(t.year, 4);
                case "yyyyyy":
                    return n
                        ? o({ year: "numeric" }, "year")
                        : this.num(t.year, 6);
                case "G":
                    return u("short");
                case "GG":
                    return u("long");
                case "GGGGG":
                    return u("narrow");
                case "kk":
                    return this.num(t.weekYear.toString().slice(-2), 2);
                case "kkkk":
                    return this.num(t.weekYear, 4);
                case "W":
                    return this.num(t.weekNumber);
                case "WW":
                    return this.num(t.weekNumber, 2);
                case "n":
                    return this.num(t.localWeekNumber);
                case "nn":
                    return this.num(t.localWeekNumber, 2);
                case "ii":
                    return this.num(t.localWeekYear.toString().slice(-2), 2);
                case "iiii":
                    return this.num(t.localWeekYear, 4);
                case "o":
                    return this.num(t.ordinal);
                case "ooo":
                    return this.num(t.ordinal, 3);
                case "q":
                    return this.num(t.quarter);
                case "qq":
                    return this.num(t.quarter, 2);
                case "X":
                    return this.num(Math.floor(t.ts / 1e3));
                case "x":
                    return this.num(t.ts);
                default:
                    return h(f);
            }
        };
        return gc(s.parseFormat(e), d);
    }

    formatDurationFromString(t, e) {
        const i = this.opts.signMode === "negativeLargestOnly" ? -1 : 1;
        const n = (h) => {
            switch (h[0]) {
                case "S":
                    return "milliseconds";
                case "s":
                    return "seconds";
                case "m":
                    return "minutes";
                case "h":
                    return "hours";
                case "d":
                    return "days";
                case "w":
                    return "weeks";
                case "M":
                    return "months";
                case "y":
                    return "years";
                default:
                    return null;
            }
        };
        const o = (h, u) => (d) => {
            const f = n(d);
            if (f) {
                const m = u.isNegativeDuration && f !== u.largestUnit ? i : 1;
                let g;
                return (
                    this.opts.signMode === "negativeLargestOnly" &&
                    f !== u.largestUnit
                        ? (g = "never")
                        : this.opts.signMode === "all"
                          ? (g = "always")
                          : (g = "auto"),
                    this.num(h.get(f) * m, d.length, g)
                );
            } else return d;
        };
        const r = s.parseFormat(e);
        const a = r.reduce(
            (h, { literal: u, val: d }) => (u ? h : h.concat(d)),
            [],
        );
        const l = t.shiftTo(...a.map(n).filter((h) => h));
        const c = {
            isNegativeDuration: l < 0,
            largestUnit: Object.keys(l.values)[0],
        };
        return gc(r, o(l, c));
    }
};
const yc =
    /[A-Za-z_+-]{1,256}(?::?\/[A-Za-z0-9_+-]{1,256}(?:\/[A-Za-z0-9_+-]{1,256})?)?/;
function us(...s) {
    const t = s.reduce((e, i) => e + i.source, "");
    return RegExp(`^${t}$`);
}
function ds(...s) {
    return (t) =>
        s
            .reduce(
                ([e, i, n], o) => {
                    const [r, a, l] = o(t, n);
                    return [{ ...e, ...r }, a || i, l];
                },
                [{}, null, 1],
            )
            .slice(0, 2);
}
function fs(s, ...t) {
    if (s == null) return [null, null];
    for (const [e, i] of t) {
        const n = e.exec(s);
        if (n) return i(n);
    }
    return [null, null];
}
function bc(...s) {
    return (t, e) => {
        const i = {};
        let n;
        for (n = 0; n < s.length; n++) i[s[n]] = Kt(t[e + n]);
        return [i, null, e + n];
    };
}
const xc = /(?:([Zz])|([+-]\d\d)(?::?(\d\d))?)/;
const Qm = `(?:${xc.source}?(?:\\[(${yc.source})\\])?)?`;
const gr = /(\d\d)(?::?(\d\d)(?::?(\d\d)(?:[.,](\d{1,30}))?)?)?/;
const _c = RegExp(`${gr.source}${Qm}`);
const pr = RegExp(`(?:[Tt]${_c.source})?`);
const tg = /([+-]\d{6}|\d{4})(?:-?(\d\d)(?:-?(\d\d))?)?/;
const eg = /(\d{4})-?W(\d\d)(?:-?(\d))?/;
const sg = /(\d{4})-?(\d{3})/;
const ig = bc("weekYear", "weekNumber", "weekDay");
const ng = bc("year", "ordinal");
const og = /(\d{4})-(\d\d)-(\d\d)/;
const wc = RegExp(`${gr.source} ?(?:${xc.source}|(${yc.source}))?`);
const rg = RegExp(`(?: ${wc.source})?`);
function hs(s, t, e) {
    const i = s[t];
    return D(i) ? e : Kt(i);
}
function ag(s, t) {
    return [
        { year: hs(s, t), month: hs(s, t + 1, 1), day: hs(s, t + 2, 1) },
        null,
        t + 3,
    ];
}
function ms(s, t) {
    return [
        {
            hours: hs(s, t, 0),
            minutes: hs(s, t + 1, 0),
            seconds: hs(s, t + 2, 0),
            milliseconds: yi(s[t + 3]),
        },
        null,
        t + 4,
    ];
}
function xi(s, t) {
    const e = !s[t] && !s[t + 1];
    const i = Ie(s[t + 1], s[t + 2]);
    const n = e ? null : et.instance(i);
    return [{}, n, t + 3];
}
function _i(s, t) {
    const e = s[t] ? ht.create(s[t]) : null;
    return [{}, e, t + 1];
}
const lg = RegExp(`^T?${gr.source}$`);
const cg =
    /^-?P(?:(?:(-?\d{1,20}(?:\.\d{1,20})?)Y)?(?:(-?\d{1,20}(?:\.\d{1,20})?)M)?(?:(-?\d{1,20}(?:\.\d{1,20})?)W)?(?:(-?\d{1,20}(?:\.\d{1,20})?)D)?(?:T(?:(-?\d{1,20}(?:\.\d{1,20})?)H)?(?:(-?\d{1,20}(?:\.\d{1,20})?)M)?(?:(-?\d{1,20})(?:[.,](-?\d{1,20}))?S)?)?)$/;
function hg(s) {
    const [t, e, i, n, o, r, a, l, c] = s;
    const h = t[0] === "-";
    const u = l && l[0] === "-";
    const d = (f, m = !1) => (f !== void 0 && (m || (f && h)) ? -f : f);
    return [
        {
            years: d(ge(e)),
            months: d(ge(i)),
            weeks: d(ge(n)),
            days: d(ge(o)),
            hours: d(ge(r)),
            minutes: d(ge(a)),
            seconds: d(ge(l), l === "-0"),
            milliseconds: d(yi(c), u),
        },
    ];
}
const ug = {
    GMT: 0,
    EDT: -4 * 60,
    EST: -5 * 60,
    CDT: -5 * 60,
    CST: -6 * 60,
    MDT: -6 * 60,
    MST: -7 * 60,
    PDT: -7 * 60,
    PST: -8 * 60,
};
function yr(s, t, e, i, n, o, r) {
    const a = {
        year: t.length === 2 ? bi(Kt(t)) : Kt(t),
        month: dr.indexOf(e) + 1,
        day: Kt(i),
        hour: Kt(n),
        minute: Kt(o),
    };
    return (
        r && (a.second = Kt(r)),
        s && (a.weekday = s.length > 3 ? fr.indexOf(s) + 1 : mr.indexOf(s) + 1),
        a
    );
}
const dg =
    /^(?:(Mon|Tue|Wed|Thu|Fri|Sat|Sun),\s)?(\d{1,2})\s(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\s(\d{2,4})\s(\d\d):(\d\d)(?::(\d\d))?\s(?:(UT|GMT|[ECMP][SD]T)|([Zz])|(?:([+-]\d\d)(\d\d)))$/;
function fg(s) {
    const [, t, e, i, n, o, r, a, l, c, h, u] = s;
    const d = yr(t, n, i, e, o, r, a);
    let f;
    return (l ? (f = ug[l]) : c ? (f = 0) : (f = Ie(h, u)), [d, new et(f)]);
}
function mg(s) {
    return s
        .replace(/\([^()]*\)|[\n\t]/g, " ")
        .replace(/(\s\s+)/g, " ")
        .trim();
}
const gg =
    /^(Mon|Tue|Wed|Thu|Fri|Sat|Sun), (\d\d) (Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec) (\d{4}) (\d\d):(\d\d):(\d\d) GMT$/;
const pg =
    /^(Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday), (\d\d)-(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)-(\d\d) (\d\d):(\d\d):(\d\d) GMT$/;
const yg =
    /^(Mon|Tue|Wed|Thu|Fri|Sat|Sun) (Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec) ( \d|\d\d) (\d\d):(\d\d):(\d\d) (\d{4})$/;
function pc(s) {
    const [, t, e, i, n, o, r, a] = s;
    return [yr(t, n, i, e, o, r, a), et.utcInstance];
}
function bg(s) {
    const [, t, e, i, n, o, r, a] = s;
    return [yr(t, a, e, i, n, o, r), et.utcInstance];
}
const xg = us(tg, pr);
const _g = us(eg, pr);
const wg = us(sg, pr);
const Sg = us(_c);
const Sc = ds(ag, ms, xi, _i);
const kg = ds(ig, ms, xi, _i);
const Mg = ds(ng, ms, xi, _i);
const vg = ds(ms, xi, _i);
function kc(s) {
    return fs(s, [xg, Sc], [_g, kg], [wg, Mg], [Sg, vg]);
}
function Mc(s) {
    return fs(mg(s), [dg, fg]);
}
function vc(s) {
    return fs(s, [gg, pc], [pg, pc], [yg, bg]);
}
function Tc(s) {
    return fs(s, [cg, hg]);
}
const Tg = ds(ms);
function Oc(s) {
    return fs(s, [lg, Tg]);
}
const Og = us(og, rg);
const Dg = us(wc);
const Eg = ds(ms, xi, _i);
function Dc(s) {
    return fs(s, [Og, Sc], [Dg, Eg]);
}
const Ec = "Invalid Duration";
const Fc = {
    weeks: {
        days: 7,
        hours: 7 * 24,
        minutes: 7 * 24 * 60,
        seconds: 7 * 24 * 60 * 60,
        milliseconds: 7 * 24 * 60 * 60 * 1e3,
    },
    days: {
        hours: 24,
        minutes: 24 * 60,
        seconds: 24 * 60 * 60,
        milliseconds: 24 * 60 * 60 * 1e3,
    },
    hours: { minutes: 60, seconds: 60 * 60, milliseconds: 60 * 60 * 1e3 },
    minutes: { seconds: 60, milliseconds: 60 * 1e3 },
    seconds: { milliseconds: 1e3 },
};
const Ig = {
    years: {
        quarters: 4,
        months: 12,
        weeks: 52,
        days: 365,
        hours: 365 * 24,
        minutes: 365 * 24 * 60,
        seconds: 365 * 24 * 60 * 60,
        milliseconds: 365 * 24 * 60 * 60 * 1e3,
    },
    quarters: {
        months: 3,
        weeks: 13,
        days: 91,
        hours: 91 * 24,
        minutes: 91 * 24 * 60,
        seconds: 91 * 24 * 60 * 60,
        milliseconds: 91 * 24 * 60 * 60 * 1e3,
    },
    months: {
        weeks: 4,
        days: 30,
        hours: 30 * 24,
        minutes: 30 * 24 * 60,
        seconds: 30 * 24 * 60 * 60,
        milliseconds: 30 * 24 * 60 * 60 * 1e3,
    },
    ...Fc,
};
const vt = 146097 / 400;
const gs = 146097 / 4800;
const Cg = {
    years: {
        quarters: 4,
        months: 12,
        weeks: vt / 7,
        days: vt,
        hours: vt * 24,
        minutes: vt * 24 * 60,
        seconds: vt * 24 * 60 * 60,
        milliseconds: vt * 24 * 60 * 60 * 1e3,
    },
    quarters: {
        months: 3,
        weeks: vt / 28,
        days: vt / 4,
        hours: (vt * 24) / 4,
        minutes: (vt * 24 * 60) / 4,
        seconds: (vt * 24 * 60 * 60) / 4,
        milliseconds: (vt * 24 * 60 * 60 * 1e3) / 4,
    },
    months: {
        weeks: gs / 7,
        days: gs,
        hours: gs * 24,
        minutes: gs * 24 * 60,
        seconds: gs * 24 * 60 * 60,
        milliseconds: gs * 24 * 60 * 60 * 1e3,
    },
    ...Fc,
};
const Ae = [
    "years",
    "quarters",
    "months",
    "weeks",
    "days",
    "hours",
    "minutes",
    "seconds",
    "milliseconds",
];
const Fg = Ae.slice(0).reverse();
function Jt(s, t, e = !1) {
    const i = {
        values: e ? t.values : { ...s.values, ...(t.values || {}) },
        loc: s.loc.clone(t.loc),
        conversionAccuracy: t.conversionAccuracy || s.conversionAccuracy,
        matrix: t.matrix || s.matrix,
    };
    return new G(i);
}
function Ac(s, t) {
    let e = t.milliseconds ?? 0;
    for (const i of Fg.slice(1)) t[i] && (e += t[i] * s[i].milliseconds);
    return e;
}
function Ic(s, t) {
    const e = Ac(s, t) < 0 ? -1 : 1;
    (Ae.reduceRight((i, n) => {
        if (D(t[n])) return i;
        if (i) {
            const o = t[i] * e;
            const r = s[n][i];
            const a = Math.floor(o / r);
            ((t[n] += a * e), (t[i] -= a * r * e));
        }
        return n;
    }, null),
        Ae.reduce((i, n) => {
            if (D(t[n])) return i;
            if (i) {
                const o = t[i] % 1;
                ((t[i] -= o), (t[n] += o * s[i][n]));
            }
            return n;
        }, null));
}
function Cc(s) {
    const t = {};
    for (const [e, i] of Object.entries(s)) i !== 0 && (t[e] = i);
    return t;
}
var G = class s {
    constructor(t) {
        const e = t.conversionAccuracy === "longterm" || !1;
        let i = e ? Cg : Ig;
        (t.matrix && (i = t.matrix),
            (this.values = t.values),
            (this.loc = t.loc || W.create()),
            (this.conversionAccuracy = e ? "longterm" : "casual"),
            (this.invalid = t.invalid || null),
            (this.matrix = i),
            (this.isLuxonDuration = !0));
    }

    static fromMillis(t, e) {
        return s.fromObject({ milliseconds: t }, e);
    }

    static fromObject(t, e = {}) {
        if (t == null || typeof t !== "object") {
            throw new K(
                `Duration.fromObject: argument expected to be an object, got ${t === null ? "null" : typeof t}`,
            );
        }
        return new s({
            values: cs(t, s.normalizeUnit),
            loc: W.fromObject(e),
            conversionAccuracy: e.conversionAccuracy,
            matrix: e.matrix,
        });
    }

    static fromDurationLike(t) {
        if (Ft(t)) return s.fromMillis(t);
        if (s.isDuration(t)) return t;
        if (typeof t === "object") return s.fromObject(t);
        throw new K(`Unknown duration argument ${t} of type ${typeof t}`);
    }

    static fromISO(t, e) {
        const [i] = Tc(t);
        return i
            ? s.fromObject(i, e)
            : s.invalid(
                  "unparsable",
                  `the input "${t}" can't be parsed as ISO 8601`,
              );
    }

    static fromISOTime(t, e) {
        const [i] = Oc(t);
        return i
            ? s.fromObject(i, e)
            : s.invalid(
                  "unparsable",
                  `the input "${t}" can't be parsed as ISO 8601`,
              );
    }

    static invalid(t, e = null) {
        if (!t) throw new K("need to specify a reason the Duration is invalid");
        const i = t instanceof st ? t : new st(t, e);
        if (R.throwOnInvalid) throw new dn(i);
        return new s({ invalid: i });
    }

    static normalizeUnit(t) {
        const e = {
            year: "years",
            years: "years",
            quarter: "quarters",
            quarters: "quarters",
            month: "months",
            months: "months",
            week: "weeks",
            weeks: "weeks",
            day: "days",
            days: "days",
            hour: "hours",
            hours: "hours",
            minute: "minutes",
            minutes: "minutes",
            second: "seconds",
            seconds: "seconds",
            millisecond: "milliseconds",
            milliseconds: "milliseconds",
        }[t && t.toLowerCase()];
        if (!e) throw new ns(t);
        return e;
    }

    static isDuration(t) {
        return (t && t.isLuxonDuration) || !1;
    }

    get locale() {
        return this.isValid ? this.loc.locale : null;
    }

    get numberingSystem() {
        return this.isValid ? this.loc.numberingSystem : null;
    }

    toFormat(t, e = {}) {
        const i = { ...e, floor: e.round !== !1 && e.floor !== !1 };
        return this.isValid
            ? it.create(this.loc, i).formatDurationFromString(this, t)
            : Ec;
    }

    toHuman(t = {}) {
        if (!this.isValid) return Ec;
        const e = t.showZeros !== !1;
        const i = Ae.map((n) => {
            const o = this.values[n];
            return D(o) || (o === 0 && !e)
                ? null
                : this.loc
                      .numberFormatter({
                          style: "unit",
                          unitDisplay: "long",
                          ...t,
                          unit: n.slice(0, -1),
                      })
                      .format(o);
        }).filter((n) => n);
        return this.loc
            .listFormatter({
                type: "conjunction",
                style: t.listStyle || "narrow",
                ...t,
            })
            .format(i);
    }

    toObject() {
        return this.isValid ? { ...this.values } : {};
    }

    toISO() {
        if (!this.isValid) return null;
        let t = "P";
        return (
            this.years !== 0 && (t += this.years + "Y"),
            (this.months !== 0 || this.quarters !== 0) &&
                (t += this.months + this.quarters * 3 + "M"),
            this.weeks !== 0 && (t += this.weeks + "W"),
            this.days !== 0 && (t += this.days + "D"),
            (this.hours !== 0 ||
                this.minutes !== 0 ||
                this.seconds !== 0 ||
                this.milliseconds !== 0) &&
                (t += "T"),
            this.hours !== 0 && (t += this.hours + "H"),
            this.minutes !== 0 && (t += this.minutes + "M"),
            (this.seconds !== 0 || this.milliseconds !== 0) &&
                (t += rs(this.seconds + this.milliseconds / 1e3, 3) + "S"),
            t === "P" && (t += "T0S"),
            t
        );
    }

    toISOTime(t = {}) {
        if (!this.isValid) return null;
        const e = this.toMillis();
        return e < 0 || e >= 864e5
            ? null
            : ((t = {
                  suppressMilliseconds: !1,
                  suppressSeconds: !1,
                  includePrefix: !1,
                  format: "extended",
                  ...t,
                  includeOffset: !1,
              }),
              F.fromMillis(e, { zone: "UTC" }).toISOTime(t));
    }

    toJSON() {
        return this.toISO();
    }

    toString() {
        return this.toISO();
    }

    [Symbol.for("nodejs.util.inspect.custom")]() {
        return this.isValid
            ? `Duration { values: ${JSON.stringify(this.values)} }`
            : `Duration { Invalid, reason: ${this.invalidReason} }`;
    }

    toMillis() {
        return this.isValid ? Ac(this.matrix, this.values) : NaN;
    }

    valueOf() {
        return this.toMillis();
    }

    plus(t) {
        if (!this.isValid) return this;
        const e = s.fromDurationLike(t);
        const i = {};
        for (const n of Ae) {
            (me(e.values, n) || me(this.values, n)) &&
                (i[n] = e.get(n) + this.get(n));
        }
        return Jt(this, { values: i }, !0);
    }

    minus(t) {
        if (!this.isValid) return this;
        const e = s.fromDurationLike(t);
        return this.plus(e.negate());
    }

    mapUnits(t) {
        if (!this.isValid) return this;
        const e = {};
        for (const i of Object.keys(this.values)) {
            e[i] = ur(t(this.values[i], i));
        }
        return Jt(this, { values: e }, !0);
    }

    get(t) {
        return this[s.normalizeUnit(t)];
    }

    set(t) {
        if (!this.isValid) return this;
        const e = { ...this.values, ...cs(t, s.normalizeUnit) };
        return Jt(this, { values: e });
    }

    reconfigure({
        locale: t,
        numberingSystem: e,
        conversionAccuracy: i,
        matrix: n,
    } = {}) {
        const r = {
            loc: this.loc.clone({ locale: t, numberingSystem: e }),
            matrix: n,
            conversionAccuracy: i,
        };
        return Jt(this, r);
    }

    as(t) {
        return this.isValid ? this.shiftTo(t).get(t) : NaN;
    }

    normalize() {
        if (!this.isValid) return this;
        const t = this.toObject();
        return (Ic(this.matrix, t), Jt(this, { values: t }, !0));
    }

    rescale() {
        if (!this.isValid) return this;
        const t = Cc(this.normalize().shiftToAll().toObject());
        return Jt(this, { values: t }, !0);
    }

    shiftTo(...t) {
        if (!this.isValid) return this;
        if (t.length === 0) return this;
        t = t.map((r) => s.normalizeUnit(r));
        const e = {};
        const i = {};
        const n = this.toObject();
        let o;
        for (const r of Ae) {
            if (t.indexOf(r) >= 0) {
                o = r;
                let a = 0;
                for (const c in i) {
                    ((a += this.matrix[c][r] * i[c]), (i[c] = 0));
                }
                Ft(n[r]) && (a += n[r]);
                const l = Math.trunc(a);
                ((e[r] = l), (i[r] = (a * 1e3 - l * 1e3) / 1e3));
            } else Ft(n[r]) && (i[r] = n[r]);
        }
        for (const r in i) {
            i[r] !== 0 && (e[o] += r === o ? i[r] : i[r] / this.matrix[o][r]);
        }
        return (Ic(this.matrix, e), Jt(this, { values: e }, !0));
    }

    shiftToAll() {
        return this.isValid
            ? this.shiftTo(
                  "years",
                  "months",
                  "weeks",
                  "days",
                  "hours",
                  "minutes",
                  "seconds",
                  "milliseconds",
              )
            : this;
    }

    negate() {
        if (!this.isValid) return this;
        const t = {};
        for (const e of Object.keys(this.values)) {
            t[e] = this.values[e] === 0 ? 0 : -this.values[e];
        }
        return Jt(this, { values: t }, !0);
    }

    removeZeros() {
        if (!this.isValid) return this;
        const t = Cc(this.values);
        return Jt(this, { values: t }, !0);
    }

    get years() {
        return this.isValid ? this.values.years || 0 : NaN;
    }

    get quarters() {
        return this.isValid ? this.values.quarters || 0 : NaN;
    }

    get months() {
        return this.isValid ? this.values.months || 0 : NaN;
    }

    get weeks() {
        return this.isValid ? this.values.weeks || 0 : NaN;
    }

    get days() {
        return this.isValid ? this.values.days || 0 : NaN;
    }

    get hours() {
        return this.isValid ? this.values.hours || 0 : NaN;
    }

    get minutes() {
        return this.isValid ? this.values.minutes || 0 : NaN;
    }

    get seconds() {
        return this.isValid ? this.values.seconds || 0 : NaN;
    }

    get milliseconds() {
        return this.isValid ? this.values.milliseconds || 0 : NaN;
    }

    get isValid() {
        return this.invalid === null;
    }

    get invalidReason() {
        return this.invalid ? this.invalid.reason : null;
    }

    get invalidExplanation() {
        return this.invalid ? this.invalid.explanation : null;
    }

    equals(t) {
        if (!this.isValid || !t.isValid || !this.loc.equals(t.loc)) return !1;
        function e(i, n) {
            return i === void 0 || i === 0 ? n === void 0 || n === 0 : i === n;
        }
        for (const i of Ae) if (!e(this.values[i], t.values[i])) return !1;
        return !0;
    }
};
const ps = "Invalid Interval";
function Ag(s, t) {
    return !s || !s.isValid
        ? Qt.invalid("missing or invalid start")
        : !t || !t.isValid
          ? Qt.invalid("missing or invalid end")
          : t < s
            ? Qt.invalid(
                  "end before start",
                  `The end of an interval must be after its start, but you had start=${s.toISO()} and end=${t.toISO()}`,
              )
            : null;
}
var Qt = class s {
    constructor(t) {
        ((this.s = t.start),
            (this.e = t.end),
            (this.invalid = t.invalid || null),
            (this.isLuxonInterval = !0));
    }

    static invalid(t, e = null) {
        if (!t) throw new K("need to specify a reason the Interval is invalid");
        const i = t instanceof st ? t : new st(t, e);
        if (R.throwOnInvalid) throw new un(i);
        return new s({ invalid: i });
    }

    static fromDateTimes(t, e) {
        const i = ys(t);
        const n = ys(e);
        const o = Ag(i, n);
        return o ?? new s({ start: i, end: n });
    }

    static after(t, e) {
        const i = G.fromDurationLike(e);
        const n = ys(t);
        return s.fromDateTimes(n, n.plus(i));
    }

    static before(t, e) {
        const i = G.fromDurationLike(e);
        const n = ys(t);
        return s.fromDateTimes(n.minus(i), n);
    }

    static fromISO(t, e) {
        const [i, n] = (t || "").split("/", 2);
        if (i && n) {
            let o, r;
            try {
                ((o = F.fromISO(i, e)), (r = o.isValid));
            } catch {
                r = !1;
            }
            let a, l;
            try {
                ((a = F.fromISO(n, e)), (l = a.isValid));
            } catch {
                l = !1;
            }
            if (r && l) return s.fromDateTimes(o, a);
            if (r) {
                const c = G.fromISO(n, e);
                if (c.isValid) return s.after(o, c);
            } else if (l) {
                const c = G.fromISO(i, e);
                if (c.isValid) return s.before(a, c);
            }
        }
        return s.invalid(
            "unparsable",
            `the input "${t}" can't be parsed as ISO 8601`,
        );
    }

    static isInterval(t) {
        return (t && t.isLuxonInterval) || !1;
    }

    get start() {
        return this.isValid ? this.s : null;
    }

    get end() {
        return this.isValid ? this.e : null;
    }

    get lastDateTime() {
        return this.isValid && this.e ? this.e.minus(1) : null;
    }

    get isValid() {
        return this.invalidReason === null;
    }

    get invalidReason() {
        return this.invalid ? this.invalid.reason : null;
    }

    get invalidExplanation() {
        return this.invalid ? this.invalid.explanation : null;
    }

    length(t = "milliseconds") {
        return this.isValid ? this.toDuration(t).get(t) : NaN;
    }

    count(t = "milliseconds", e) {
        if (!this.isValid) return NaN;
        const i = this.start.startOf(t, e);
        let n;
        return (
            e?.useLocaleWeeks
                ? (n = this.end.reconfigure({ locale: i.locale }))
                : (n = this.end),
            (n = n.startOf(t, e)),
            Math.floor(n.diff(i, t).get(t)) +
                (n.valueOf() !== this.end.valueOf())
        );
    }

    hasSame(t) {
        return this.isValid
            ? this.isEmpty() || this.e.minus(1).hasSame(this.s, t)
            : !1;
    }

    isEmpty() {
        return this.s.valueOf() === this.e.valueOf();
    }

    isAfter(t) {
        return this.isValid ? this.s > t : !1;
    }

    isBefore(t) {
        return this.isValid ? this.e <= t : !1;
    }

    contains(t) {
        return this.isValid ? this.s <= t && this.e > t : !1;
    }

    set({ start: t, end: e } = {}) {
        return this.isValid ? s.fromDateTimes(t || this.s, e || this.e) : this;
    }

    splitAt(...t) {
        if (!this.isValid) return [];
        const e = t
            .map(ys)
            .filter((r) => this.contains(r))
            .sort((r, a) => r.toMillis() - a.toMillis());
        const i = [];
        let { s: n } = this;
        let o = 0;
        for (; n < this.e; ) {
            const r = e[o] || this.e;
            const a = +r > +this.e ? this.e : r;
            (i.push(s.fromDateTimes(n, a)), (n = a), (o += 1));
        }
        return i;
    }

    splitBy(t) {
        const e = G.fromDurationLike(t);
        if (!this.isValid || !e.isValid || e.as("milliseconds") === 0) {
            return [];
        }
        let { s: i } = this;
        let n = 1;
        let o;
        const r = [];
        for (; i < this.e; ) {
            const a = this.start.plus(e.mapUnits((l) => l * n));
            ((o = +a > +this.e ? this.e : a),
                r.push(s.fromDateTimes(i, o)),
                (i = o),
                (n += 1));
        }
        return r;
    }

    divideEqually(t) {
        return this.isValid ? this.splitBy(this.length() / t).slice(0, t) : [];
    }

    overlaps(t) {
        return this.e > t.s && this.s < t.e;
    }

    abutsStart(t) {
        return this.isValid ? +this.e == +t.s : !1;
    }

    abutsEnd(t) {
        return this.isValid ? +t.e == +this.s : !1;
    }

    engulfs(t) {
        return this.isValid ? this.s <= t.s && this.e >= t.e : !1;
    }

    equals(t) {
        return !this.isValid || !t.isValid
            ? !1
            : this.s.equals(t.s) && this.e.equals(t.e);
    }

    intersection(t) {
        if (!this.isValid) return this;
        const e = this.s > t.s ? this.s : t.s;
        const i = this.e < t.e ? this.e : t.e;
        return e >= i ? null : s.fromDateTimes(e, i);
    }

    union(t) {
        if (!this.isValid) return this;
        const e = this.s < t.s ? this.s : t.s;
        const i = this.e > t.e ? this.e : t.e;
        return s.fromDateTimes(e, i);
    }

    static merge(t) {
        const [e, i] = t
            .sort((n, o) => n.s - o.s)
            .reduce(
                ([n, o], r) =>
                    o
                        ? o.overlaps(r) || o.abutsStart(r)
                            ? [n, o.union(r)]
                            : [n.concat([o]), r]
                        : [n, r],
                [[], null],
            );
        return (i && e.push(i), e);
    }

    static xor(t) {
        let e = null;
        let i = 0;
        const n = [];
        const o = t.map((l) => [
            { time: l.s, type: "s" },
            { time: l.e, type: "e" },
        ]);
        const r = Array.prototype.concat(...o);
        const a = r.sort((l, c) => l.time - c.time);
        for (const l of a) {
            ((i += l.type === "s" ? 1 : -1),
                i === 1
                    ? (e = l.time)
                    : (e && +e != +l.time && n.push(s.fromDateTimes(e, l.time)),
                      (e = null)));
        }
        return s.merge(n);
    }

    difference(...t) {
        return s
            .xor([this].concat(t))
            .map((e) => this.intersection(e))
            .filter((e) => e && !e.isEmpty());
    }

    toString() {
        return this.isValid
            ? `[${this.s.toISO()} \u2013 ${this.e.toISO()})`
            : ps;
    }

    [Symbol.for("nodejs.util.inspect.custom")]() {
        return this.isValid
            ? `Interval { start: ${this.s.toISO()}, end: ${this.e.toISO()} }`
            : `Interval { Invalid, reason: ${this.invalidReason} }`;
    }

    toLocaleString(t = he, e = {}) {
        return this.isValid
            ? it.create(this.s.loc.clone(e), t).formatInterval(this)
            : ps;
    }

    toISO(t) {
        return this.isValid ? `${this.s.toISO(t)}/${this.e.toISO(t)}` : ps;
    }

    toISODate() {
        return this.isValid
            ? `${this.s.toISODate()}/${this.e.toISODate()}`
            : ps;
    }

    toISOTime(t) {
        return this.isValid
            ? `${this.s.toISOTime(t)}/${this.e.toISOTime(t)}`
            : ps;
    }

    toFormat(t, { separator: e = " \u2013 " } = {}) {
        return this.isValid
            ? `${this.s.toFormat(t)}${e}${this.e.toFormat(t)}`
            : ps;
    }

    toDuration(t, e) {
        return this.isValid
            ? this.e.diff(this.s, t, e)
            : G.invalid(this.invalidReason);
    }

    mapEndpoints(t) {
        return s.fromDateTimes(t(this.s), t(this.e));
    }
};
const te = class {
    static hasDST(t = R.defaultZone) {
        const e = F.now().setZone(t).set({ month: 12 });
        return !t.isUniversal && e.offset !== e.set({ month: 6 }).offset;
    }

    static isValidIANAZone(t) {
        return ht.isValidZone(t);
    }

    static normalizeZone(t) {
        return Ct(t, R.defaultZone);
    }

    static getStartOfWeek({ locale: t = null, locObj: e = null } = {}) {
        return (e || W.create(t)).getStartOfWeek();
    }

    static getMinimumDaysInFirstWeek({
        locale: t = null,
        locObj: e = null,
    } = {}) {
        return (e || W.create(t)).getMinDaysInFirstWeek();
    }

    static getWeekendWeekdays({ locale: t = null, locObj: e = null } = {}) {
        return (e || W.create(t)).getWeekendDays().slice();
    }

    static months(
        t = "long",
        {
            locale: e = null,
            numberingSystem: i = null,
            locObj: n = null,
            outputCalendar: o = "gregory",
        } = {},
    ) {
        return (n || W.create(e, i, o)).months(t);
    }

    static monthsFormat(
        t = "long",
        {
            locale: e = null,
            numberingSystem: i = null,
            locObj: n = null,
            outputCalendar: o = "gregory",
        } = {},
    ) {
        return (n || W.create(e, i, o)).months(t, !0);
    }

    static weekdays(
        t = "long",
        { locale: e = null, numberingSystem: i = null, locObj: n = null } = {},
    ) {
        return (n || W.create(e, i, null)).weekdays(t);
    }

    static weekdaysFormat(
        t = "long",
        { locale: e = null, numberingSystem: i = null, locObj: n = null } = {},
    ) {
        return (n || W.create(e, i, null)).weekdays(t, !0);
    }

    static meridiems({ locale: t = null } = {}) {
        return W.create(t).meridiems();
    }

    static eras(t = "short", { locale: e = null } = {}) {
        return W.create(e, null, "gregory").eras(t);
    }

    static features() {
        return { relative: pn(), localeWeek: yn() };
    }
};
function Lc(s, t) {
    const e = (n) => n.toUTC(0, { keepLocalTime: !0 }).startOf("day").valueOf();
    const i = e(t) - e(s);
    return Math.floor(G.fromMillis(i).as("days"));
}
function Lg(s, t, e) {
    const i = [
        ["years", (l, c) => c.year - l.year],
        ["quarters", (l, c) => c.quarter - l.quarter + (c.year - l.year) * 4],
        ["months", (l, c) => c.month - l.month + (c.year - l.year) * 12],
        [
            "weeks",
            (l, c) => {
                const h = Lc(l, c);
                return (h - (h % 7)) / 7;
            },
        ],
        ["days", Lc],
    ];
    const n = {};
    const o = s;
    let r;
    let a;
    for (const [l, c] of i) {
        e.indexOf(l) >= 0 &&
            ((r = l),
            (n[l] = c(s, t)),
            (a = o.plus(n)),
            a > t
                ? (n[l]--,
                  (s = o.plus(n)),
                  s > t && ((a = s), n[l]--, (s = o.plus(n))))
                : (s = a));
    }
    return [s, n, a, r];
}
function Pc(s, t, e, i) {
    let [n, o, r, a] = Lg(s, t, e);
    const l = t - n;
    const c = e.filter(
        (u) => ["hours", "minutes", "seconds", "milliseconds"].indexOf(u) >= 0,
    );
    c.length === 0 &&
        (r < t && (r = n.plus({ [a]: 1 })),
        r !== n && (o[a] = (o[a] || 0) + l / (r - n)));
    const h = G.fromObject(o, i);
    return c.length > 0
        ? G.fromMillis(l, i)
              .shiftTo(...c)
              .plus(h)
        : h;
}
const Pg = "missing Intl.DateTimeFormat.formatToParts support";
function z(s, t = (e) => e) {
    return { regex: s, deser: ([e]) => t(Ul(e)) };
}
const Ng = "\xA0";
const Wc = `[ ${Ng}]`;
const zc = new RegExp(Wc, "g");
function Rg(s) {
    return s.replace(/\./g, "\\.?").replace(zc, Wc);
}
function Nc(s) {
    return s.replace(/\./g, "").replace(zc, " ").toLowerCase();
}
function At(s, t) {
    return s === null
        ? null
        : {
              regex: RegExp(s.map(Rg).join("|")),
              deser: ([e]) => s.findIndex((i) => Nc(e) === Nc(i)) + t,
          };
}
function Rc(s, t) {
    return { regex: s, deser: ([, e, i]) => Ie(e, i), groups: t };
}
function wn(s) {
    return { regex: s, deser: ([t]) => t };
}
function Wg(s) {
    return s.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&");
}
function zg(s, t) {
    const e = kt(t);
    const i = kt(t, "{2}");
    const n = kt(t, "{3}");
    const o = kt(t, "{4}");
    const r = kt(t, "{6}");
    const a = kt(t, "{1,2}");
    const l = kt(t, "{1,3}");
    const c = kt(t, "{1,6}");
    const h = kt(t, "{1,9}");
    const u = kt(t, "{2,4}");
    const d = kt(t, "{4,6}");
    const f = (p) => ({
        regex: RegExp(Wg(p.val)),
        deser: ([y]) => y,
        literal: !0,
    });
    const g = ((p) => {
        if (s.literal) return f(p);
        switch (p.val) {
            case "G":
                return At(t.eras("short"), 0);
            case "GG":
                return At(t.eras("long"), 0);
            case "y":
                return z(c);
            case "yy":
                return z(u, bi);
            case "yyyy":
                return z(o);
            case "yyyyy":
                return z(d);
            case "yyyyyy":
                return z(r);
            case "M":
                return z(a);
            case "MM":
                return z(i);
            case "MMM":
                return At(t.months("short", !0), 1);
            case "MMMM":
                return At(t.months("long", !0), 1);
            case "L":
                return z(a);
            case "LL":
                return z(i);
            case "LLL":
                return At(t.months("short", !1), 1);
            case "LLLL":
                return At(t.months("long", !1), 1);
            case "d":
                return z(a);
            case "dd":
                return z(i);
            case "o":
                return z(l);
            case "ooo":
                return z(n);
            case "HH":
                return z(i);
            case "H":
                return z(a);
            case "hh":
                return z(i);
            case "h":
                return z(a);
            case "mm":
                return z(i);
            case "m":
                return z(a);
            case "q":
                return z(a);
            case "qq":
                return z(i);
            case "s":
                return z(a);
            case "ss":
                return z(i);
            case "S":
                return z(l);
            case "SSS":
                return z(n);
            case "u":
                return wn(h);
            case "uu":
                return wn(a);
            case "uuu":
                return z(e);
            case "a":
                return At(t.meridiems(), 0);
            case "kkkk":
                return z(o);
            case "kk":
                return z(u, bi);
            case "W":
                return z(a);
            case "WW":
                return z(i);
            case "E":
            case "c":
                return z(e);
            case "EEE":
                return At(t.weekdays("short", !1), 1);
            case "EEEE":
                return At(t.weekdays("long", !1), 1);
            case "ccc":
                return At(t.weekdays("short", !0), 1);
            case "cccc":
                return At(t.weekdays("long", !0), 1);
            case "Z":
            case "ZZ":
                return Rc(
                    new RegExp(`([+-]${a.source})(?::(${i.source}))?`),
                    2,
                );
            case "ZZZ":
                return Rc(new RegExp(`([+-]${a.source})(${i.source})?`), 2);
            case "z":
                return wn(/[a-z_+-/]{1,256}?/i);
            case " ":
                return wn(/[^\S\n\r]/);
            default:
                return f(p);
        }
    })(s) || { invalidReason: Pg };
    return ((g.token = s), g);
}
const Vg = {
    year: { "2-digit": "yy", numeric: "yyyyy" },
    month: { numeric: "M", "2-digit": "MM", short: "MMM", long: "MMMM" },
    day: { numeric: "d", "2-digit": "dd" },
    weekday: { short: "EEE", long: "EEEE" },
    dayperiod: "a",
    dayPeriod: "a",
    hour12: { numeric: "h", "2-digit": "hh" },
    hour24: { numeric: "H", "2-digit": "HH" },
    minute: { numeric: "m", "2-digit": "mm" },
    second: { numeric: "s", "2-digit": "ss" },
    timeZoneName: { long: "ZZZZZ", short: "ZZZ" },
};
function Hg(s, t, e) {
    const { type: i, value: n } = s;
    if (i === "literal") {
        const l = /^\s+$/.test(n);
        return { literal: !l, val: l ? " " : n };
    }
    const o = t[i];
    let r = i;
    i === "hour" &&
        (t.hour12 != null
            ? (r = t.hour12 ? "hour12" : "hour24")
            : t.hourCycle != null
              ? t.hourCycle === "h11" || t.hourCycle === "h12"
                  ? (r = "hour12")
                  : (r = "hour24")
              : (r = e.hour12 ? "hour12" : "hour24"));
    let a = Vg[r];
    if ((typeof a === "object" && (a = a[o]), a)) {
        return { literal: !1, val: a };
    }
}
function Bg(s) {
    return [
        `^${s.map((e) => e.regex).reduce((e, i) => `${e}(${i.source})`, "")}$`,
        s,
    ];
}
function $g(s, t, e) {
    const i = s.match(t);
    if (i) {
        const n = {};
        let o = 1;
        for (const r in e) {
            if (me(e, r)) {
                const a = e[r];
                const l = a.groups ? a.groups + 1 : 1;
                (!a.literal &&
                    a.token &&
                    (n[a.token.val[0]] = a.deser(i.slice(o, o + l))),
                    (o += l));
            }
        }
        return [i, n];
    } else return [i, {}];
}
function jg(s) {
    const t = (o) => {
        switch (o) {
            case "S":
                return "millisecond";
            case "s":
                return "second";
            case "m":
                return "minute";
            case "h":
            case "H":
                return "hour";
            case "d":
                return "day";
            case "o":
                return "ordinal";
            case "L":
            case "M":
                return "month";
            case "y":
                return "year";
            case "E":
            case "c":
                return "weekday";
            case "W":
                return "weekNumber";
            case "k":
                return "weekYear";
            case "q":
                return "quarter";
            default:
                return null;
        }
    };
    let e = null;
    let i;
    return (
        D(s.z) || (e = ht.create(s.z)),
        D(s.Z) || (e || (e = new et(s.Z)), (i = s.Z)),
        D(s.q) || (s.M = (s.q - 1) * 3 + 1),
        D(s.h) ||
            (s.h < 12 && s.a === 1
                ? (s.h += 12)
                : s.h === 12 && s.a === 0 && (s.h = 0)),
        s.G === 0 && s.y && (s.y = -s.y),
        D(s.u) || (s.S = yi(s.u)),
        [
            Object.keys(s).reduce((o, r) => {
                const a = t(r);
                return (a && (o[a] = s[r]), o);
            }, {}),
            e,
            i,
        ]
    );
}
let br = null;
function Ug() {
    return (br || (br = F.fromMillis(1555555555555)), br);
}
function Yg(s, t) {
    if (s.literal) return s;
    const e = it.macroTokenToFormatOpts(s.val);
    const i = wr(e, t);
    return i == null || i.includes(void 0) ? s : i;
}
function xr(s, t) {
    return Array.prototype.concat(...s.map((e) => Yg(e, t)));
}
const wi = class {
    constructor(t, e) {
        if (
            ((this.locale = t),
            (this.format = e),
            (this.tokens = xr(it.parseFormat(e), t)),
            (this.units = this.tokens.map((i) => zg(i, t))),
            (this.disqualifyingUnit = this.units.find((i) => i.invalidReason)),
            !this.disqualifyingUnit)
        ) {
            const [i, n] = Bg(this.units);
            ((this.regex = RegExp(i, "i")), (this.handlers = n));
        }
    }

    explainFromTokens(t) {
        if (this.isValid) {
            const [e, i] = $g(t, this.regex, this.handlers);
            const [n, o, r] = i ? jg(i) : [null, null, void 0];
            if (me(i, "a") && me(i, "H")) {
                throw new Dt(
                    "Can't include meridiem when specifying 24-hour format",
                );
            }
            return {
                input: t,
                tokens: this.tokens,
                regex: this.regex,
                rawMatches: e,
                matches: i,
                result: n,
                zone: o,
                specificOffset: r,
            };
        } else {
            return {
                input: t,
                tokens: this.tokens,
                invalidReason: this.invalidReason,
            };
        }
    }

    get isValid() {
        return !this.disqualifyingUnit;
    }

    get invalidReason() {
        return this.disqualifyingUnit
            ? this.disqualifyingUnit.invalidReason
            : null;
    }
};
function _r(s, t, e) {
    return new wi(s, e).explainFromTokens(t);
}
function Vc(s, t, e) {
    const {
        result: i,
        zone: n,
        specificOffset: o,
        invalidReason: r,
    } = _r(s, t, e);
    return [i, n, o, r];
}
function wr(s, t) {
    if (!s) return null;
    const i = it.create(t, s).dtFormatter(Ug());
    const n = i.formatToParts();
    const o = i.resolvedOptions();
    return n.map((r) => Hg(r, s, o));
}
const Sr = "Invalid DateTime";
const Hc = 864e13;
function Si(s) {
    return new st("unsupported zone", `the zone "${s.name}" is not supported`);
}
function kr(s) {
    return (s.weekData === null && (s.weekData = mi(s.c)), s.weekData);
}
function Mr(s) {
    return (
        s.localWeekData === null &&
            (s.localWeekData = mi(
                s.c,
                s.loc.getMinDaysInFirstWeek(),
                s.loc.getStartOfWeek(),
            )),
        s.localWeekData
    );
}
function Le(s, t) {
    const e = {
        ts: s.ts,
        zone: s.zone,
        c: s.c,
        o: s.o,
        loc: s.loc,
        invalid: s.invalid,
    };
    return new F({ ...e, ...t, old: e });
}
function qc(s, t, e) {
    let i = s - t * 60 * 1e3;
    const n = e.offset(i);
    if (t === n) return [i, t];
    i -= (n - t) * 60 * 1e3;
    const o = e.offset(i);
    return n === o ? [i, n] : [s - Math.min(n, o) * 60 * 1e3, Math.max(n, o)];
}
function Sn(s, t) {
    s += t * 60 * 1e3;
    const e = new Date(s);
    return {
        year: e.getUTCFullYear(),
        month: e.getUTCMonth() + 1,
        day: e.getUTCDate(),
        hour: e.getUTCHours(),
        minute: e.getUTCMinutes(),
        second: e.getUTCSeconds(),
        millisecond: e.getUTCMilliseconds(),
    };
}
function Mn(s, t, e) {
    return qc(os(s), t, e);
}
function Bc(s, t) {
    const e = s.o;
    const i = s.c.year + Math.trunc(t.years);
    const n = s.c.month + Math.trunc(t.months) + Math.trunc(t.quarters) * 3;
    const o = {
        ...s.c,
        year: i,
        month: n,
        day:
            Math.min(s.c.day, ls(i, n)) +
            Math.trunc(t.days) +
            Math.trunc(t.weeks) * 7,
    };
    const r = G.fromObject({
        years: t.years - Math.trunc(t.years),
        quarters: t.quarters - Math.trunc(t.quarters),
        months: t.months - Math.trunc(t.months),
        weeks: t.weeks - Math.trunc(t.weeks),
        days: t.days - Math.trunc(t.days),
        hours: t.hours,
        minutes: t.minutes,
        seconds: t.seconds,
        milliseconds: t.milliseconds,
    }).as("milliseconds");
    const a = os(o);
    let [l, c] = qc(a, e, s.zone);
    return (r !== 0 && ((l += r), (c = s.zone.offset(l))), { ts: l, o: c });
}
function bs(s, t, e, i, n, o) {
    const { setZone: r, zone: a } = e;
    if ((s && Object.keys(s).length !== 0) || t) {
        const l = t || a;
        const c = F.fromObject(s, { ...e, zone: l, specificOffset: o });
        return r ? c : c.setZone(a);
    } else {
        return F.invalid(
            new st("unparsable", `the input "${n}" can't be parsed as ${i}`),
        );
    }
}
function kn(s, t, e = !0) {
    return s.isValid
        ? it
              .create(W.create("en-US"), { allowZ: e, forceSimple: !0 })
              .formatDateTimeFromString(s, t)
        : null;
}
function vr(s, t, e) {
    const i = s.c.year > 9999 || s.c.year < 0;
    let n = "";
    if (
        (i && s.c.year >= 0 && (n += "+"),
        (n += q(s.c.year, i ? 6 : 4)),
        e === "year")
    ) {
        return n;
    }
    if (t) {
        if (((n += "-"), (n += q(s.c.month)), e === "month")) return n;
        n += "-";
    } else if (((n += q(s.c.month)), e === "month")) return n;
    return ((n += q(s.c.day)), n);
}
function $c(s, t, e, i, n, o, r) {
    const a = !e || s.c.millisecond !== 0 || s.c.second !== 0;
    let l = "";
    switch (r) {
        case "day":
        case "month":
        case "year":
            break;
        default:
            if (((l += q(s.c.hour)), r === "hour")) break;
            if (t) {
                if (((l += ":"), (l += q(s.c.minute)), r === "minute")) break;
                a && ((l += ":"), (l += q(s.c.second)));
            } else {
                if (((l += q(s.c.minute)), r === "minute")) break;
                a && (l += q(s.c.second));
            }
            if (r === "second") break;
            a &&
                (!i || s.c.millisecond !== 0) &&
                ((l += "."), (l += q(s.c.millisecond, 3)));
    }
    return (
        n &&
            (s.isOffsetFixed && s.offset === 0 && !o
                ? (l += "Z")
                : s.o < 0
                  ? ((l += "-"),
                    (l += q(Math.trunc(-s.o / 60))),
                    (l += ":"),
                    (l += q(Math.trunc(-s.o % 60))))
                  : ((l += "+"),
                    (l += q(Math.trunc(s.o / 60))),
                    (l += ":"),
                    (l += q(Math.trunc(s.o % 60))))),
        o && (l += "[" + s.zone.ianaName + "]"),
        l
    );
}
const Gc = { month: 1, day: 1, hour: 0, minute: 0, second: 0, millisecond: 0 };
const Zg = {
    weekNumber: 1,
    weekday: 1,
    hour: 0,
    minute: 0,
    second: 0,
    millisecond: 0,
};
const qg = { ordinal: 1, hour: 0, minute: 0, second: 0, millisecond: 0 };
const vn = ["year", "month", "day", "hour", "minute", "second", "millisecond"];
const Gg = [
    "weekYear",
    "weekNumber",
    "weekday",
    "hour",
    "minute",
    "second",
    "millisecond",
];
const Xg = ["year", "ordinal", "hour", "minute", "second", "millisecond"];
function Tn(s) {
    const t = {
        year: "year",
        years: "year",
        month: "month",
        months: "month",
        day: "day",
        days: "day",
        hour: "hour",
        hours: "hour",
        minute: "minute",
        minutes: "minute",
        quarter: "quarter",
        quarters: "quarter",
        second: "second",
        seconds: "second",
        millisecond: "millisecond",
        milliseconds: "millisecond",
        weekday: "weekday",
        weekdays: "weekday",
        weeknumber: "weekNumber",
        weeksnumber: "weekNumber",
        weeknumbers: "weekNumber",
        weekyear: "weekYear",
        weekyears: "weekYear",
        ordinal: "ordinal",
    }[s.toLowerCase()];
    if (!t) throw new ns(s);
    return t;
}
function jc(s) {
    switch (s.toLowerCase()) {
        case "localweekday":
        case "localweekdays":
            return "localWeekday";
        case "localweeknumber":
        case "localweeknumbers":
            return "localWeekNumber";
        case "localweekyear":
        case "localweekyears":
            return "localWeekYear";
        default:
            return Tn(s);
    }
}
function Kg(s) {
    if ((ki === void 0 && (ki = R.now()), s.type !== "iana")) {
        return s.offset(ki);
    }
    const t = s.name;
    let e = Tr.get(t);
    return (e === void 0 && ((e = s.offset(ki)), Tr.set(t, e)), e);
}
function Uc(s, t) {
    const e = Ct(t.zone, R.defaultZone);
    if (!e.isValid) return F.invalid(Si(e));
    const i = W.fromObject(t);
    let n;
    let o;
    if (D(s.year)) n = R.now();
    else {
        for (const l of vn) D(s[l]) && (s[l] = Gc[l]);
        const r = lr(s) || cr(s);
        if (r) return F.invalid(r);
        const a = Kg(e);
        [n, o] = Mn(s, a, e);
    }
    return new F({ ts: n, zone: e, loc: i, o });
}
function Yc(s, t, e) {
    const i = D(e.round) ? !0 : e.round;
    const n = D(e.rounding) ? "trunc" : e.rounding;
    const o = (a, l) => (
        (a = rs(a, i || e.calendary ? 0 : 2, e.calendary ? "round" : n)),
        t.loc.clone(e).relFormatter(e).format(a, l)
    );
    const r = (a) =>
        e.calendary
            ? t.hasSame(s, a)
                ? 0
                : t.startOf(a).diff(s.startOf(a), a).get(a)
            : t.diff(s, a).get(a);
    if (e.unit) return o(r(e.unit), e.unit);
    for (const a of e.units) {
        const l = r(a);
        if (Math.abs(l) >= 1) return o(l, a);
    }
    return o(s > t ? -0 : 0, e.units[e.units.length - 1]);
}
function Zc(s) {
    let t = {};
    let e;
    return (
        s.length > 0 && typeof s[s.length - 1] === "object"
            ? ((t = s[s.length - 1]),
              (e = Array.from(s).slice(0, s.length - 1)))
            : (e = Array.from(s)),
        [t, e]
    );
}
let ki;
var Tr = new Map();
var F = class s {
    constructor(t) {
        const e = t.zone || R.defaultZone;
        let i =
            t.invalid ||
            (Number.isNaN(t.ts) ? new st("invalid input") : null) ||
            (e.isValid ? null : Si(e));
        this.ts = D(t.ts) ? R.now() : t.ts;
        let n = null;
        let o = null;
        if (!i) {
            if (t.old && t.old.ts === this.ts && t.old.zone.equals(e)) {
                [n, o] = [t.old.c, t.old.o];
            } else {
                const a = Ft(t.o) && !t.old ? t.o : e.offset(this.ts);
                ((n = Sn(this.ts, a)),
                    (i = Number.isNaN(n.year) ? new st("invalid input") : null),
                    (n = i ? null : n),
                    (o = i ? null : a));
            }
        }
        ((this._zone = e),
            (this.loc = t.loc || W.create()),
            (this.invalid = i),
            (this.weekData = null),
            (this.localWeekData = null),
            (this.c = n),
            (this.o = o),
            (this.isLuxonDateTime = !0));
    }

    static now() {
        return new s({});
    }

    static local() {
        const [t, e] = Zc(arguments);
        const [i, n, o, r, a, l, c] = e;
        return Uc(
            {
                year: i,
                month: n,
                day: o,
                hour: r,
                minute: a,
                second: l,
                millisecond: c,
            },
            t,
        );
    }

    static utc() {
        const [t, e] = Zc(arguments);
        const [i, n, o, r, a, l, c] = e;
        return (
            (t.zone = et.utcInstance),
            Uc(
                {
                    year: i,
                    month: n,
                    day: o,
                    hour: r,
                    minute: a,
                    second: l,
                    millisecond: c,
                },
                t,
            )
        );
    }

    static fromJSDate(t, e = {}) {
        const i = lc(t) ? t.valueOf() : NaN;
        if (Number.isNaN(i)) return s.invalid("invalid input");
        const n = Ct(e.zone, R.defaultZone);
        return n.isValid
            ? new s({ ts: i, zone: n, loc: W.fromObject(e) })
            : s.invalid(Si(n));
    }

    static fromMillis(t, e = {}) {
        if (Ft(t)) {
            return t < -Hc || t > Hc
                ? s.invalid("Timestamp out of range")
                : new s({
                      ts: t,
                      zone: Ct(e.zone, R.defaultZone),
                      loc: W.fromObject(e),
                  });
        }
        throw new K(
            `fromMillis requires a numerical input, but received a ${typeof t} with value ${t}`,
        );
    }

    static fromSeconds(t, e = {}) {
        if (Ft(t)) {
            return new s({
                ts: t * 1e3,
                zone: Ct(e.zone, R.defaultZone),
                loc: W.fromObject(e),
            });
        }
        throw new K("fromSeconds requires a numerical input");
    }

    static fromObject(t, e = {}) {
        t = t || {};
        const i = Ct(e.zone, R.defaultZone);
        if (!i.isValid) return s.invalid(Si(i));
        const n = W.fromObject(e);
        const o = cs(t, jc);
        const { minDaysInFirstWeek: r, startOfWeek: a } = ar(o, n);
        const l = R.now();
        const c = D(e.specificOffset) ? i.offset(l) : e.specificOffset;
        const h = !D(o.ordinal);
        const u = !D(o.year);
        const d = !D(o.month) || !D(o.day);
        const f = u || d;
        const m = o.weekYear || o.weekNumber;
        if ((f || h) && m) {
            throw new Dt(
                "Can't mix weekYear/weekNumber units with year/month/day or ordinals",
            );
        }
        if (d && h) throw new Dt("Can't mix ordinal dates with month/day");
        const g = m || (o.weekday && !f);
        let p;
        let y;
        let b = Sn(l, c);
        g
            ? ((p = Gg), (y = Zg), (b = mi(b, r, a)))
            : h
              ? ((p = Xg), (y = qg), (b = _n(b)))
              : ((p = vn), (y = Gc));
        let _ = !1;
        for (const E of p) {
            const C = o[E];
            D(C) ? (_ ? (o[E] = y[E]) : (o[E] = b[E])) : (_ = !0);
        }
        const w = g ? oc(o, r, a) : h ? rc(o) : lr(o);
        const x = w || cr(o);
        if (x) return s.invalid(x);
        const k = g ? or(o, r, a) : h ? rr(o) : o;
        const [M, v] = Mn(k, c, i);
        const O = new s({ ts: M, zone: i, o: v, loc: n });
        return o.weekday && f && t.weekday !== O.weekday
            ? s.invalid(
                  "mismatched weekday",
                  `you can't specify both a weekday of ${o.weekday} and a date of ${O.toISO()}`,
              )
            : O.isValid
              ? O
              : s.invalid(O.invalid);
    }

    static fromISO(t, e = {}) {
        const [i, n] = kc(t);
        return bs(i, n, e, "ISO 8601", t);
    }

    static fromRFC2822(t, e = {}) {
        const [i, n] = Mc(t);
        return bs(i, n, e, "RFC 2822", t);
    }

    static fromHTTP(t, e = {}) {
        const [i, n] = vc(t);
        return bs(i, n, e, "HTTP", e);
    }

    static fromFormat(t, e, i = {}) {
        if (D(t) || D(e)) {
            throw new K("fromFormat requires an input string and a format");
        }
        const { locale: n = null, numberingSystem: o = null } = i;
        const r = W.fromOpts({
            locale: n,
            numberingSystem: o,
            defaultToEN: !0,
        });
        const [a, l, c, h] = Vc(r, t, e);
        return h ? s.invalid(h) : bs(a, l, i, `format ${e}`, t, c);
    }

    static fromString(t, e, i = {}) {
        return s.fromFormat(t, e, i);
    }

    static fromSQL(t, e = {}) {
        const [i, n] = Dc(t);
        return bs(i, n, e, "SQL", t);
    }

    static invalid(t, e = null) {
        if (!t) throw new K("need to specify a reason the DateTime is invalid");
        const i = t instanceof st ? t : new st(t, e);
        if (R.throwOnInvalid) throw new hn(i);
        return new s({ invalid: i });
    }

    static isDateTime(t) {
        return (t && t.isLuxonDateTime) || !1;
    }

    static parseFormatForOpts(t, e = {}) {
        const i = wr(t, W.fromObject(e));
        return i ? i.map((n) => (n ? n.val : null)).join("") : null;
    }

    static expandFormat(t, e = {}) {
        return xr(it.parseFormat(t), W.fromObject(e))
            .map((n) => n.val)
            .join("");
    }

    static resetCache() {
        ((ki = void 0), Tr.clear());
    }

    get(t) {
        return this[t];
    }

    get isValid() {
        return this.invalid === null;
    }

    get invalidReason() {
        return this.invalid ? this.invalid.reason : null;
    }

    get invalidExplanation() {
        return this.invalid ? this.invalid.explanation : null;
    }

    get locale() {
        return this.isValid ? this.loc.locale : null;
    }

    get numberingSystem() {
        return this.isValid ? this.loc.numberingSystem : null;
    }

    get outputCalendar() {
        return this.isValid ? this.loc.outputCalendar : null;
    }

    get zone() {
        return this._zone;
    }

    get zoneName() {
        return this.isValid ? this.zone.name : null;
    }

    get year() {
        return this.isValid ? this.c.year : NaN;
    }

    get quarter() {
        return this.isValid ? Math.ceil(this.c.month / 3) : NaN;
    }

    get month() {
        return this.isValid ? this.c.month : NaN;
    }

    get day() {
        return this.isValid ? this.c.day : NaN;
    }

    get hour() {
        return this.isValid ? this.c.hour : NaN;
    }

    get minute() {
        return this.isValid ? this.c.minute : NaN;
    }

    get second() {
        return this.isValid ? this.c.second : NaN;
    }

    get millisecond() {
        return this.isValid ? this.c.millisecond : NaN;
    }

    get weekYear() {
        return this.isValid ? kr(this).weekYear : NaN;
    }

    get weekNumber() {
        return this.isValid ? kr(this).weekNumber : NaN;
    }

    get weekday() {
        return this.isValid ? kr(this).weekday : NaN;
    }

    get isWeekend() {
        return this.isValid && this.loc.getWeekendDays().includes(this.weekday);
    }

    get localWeekday() {
        return this.isValid ? Mr(this).weekday : NaN;
    }

    get localWeekNumber() {
        return this.isValid ? Mr(this).weekNumber : NaN;
    }

    get localWeekYear() {
        return this.isValid ? Mr(this).weekYear : NaN;
    }

    get ordinal() {
        return this.isValid ? _n(this.c).ordinal : NaN;
    }

    get monthShort() {
        return this.isValid
            ? te.months("short", { locObj: this.loc })[this.month - 1]
            : null;
    }

    get monthLong() {
        return this.isValid
            ? te.months("long", { locObj: this.loc })[this.month - 1]
            : null;
    }

    get weekdayShort() {
        return this.isValid
            ? te.weekdays("short", { locObj: this.loc })[this.weekday - 1]
            : null;
    }

    get weekdayLong() {
        return this.isValid
            ? te.weekdays("long", { locObj: this.loc })[this.weekday - 1]
            : null;
    }

    get offset() {
        return this.isValid ? +this.o : NaN;
    }

    get offsetNameShort() {
        return this.isValid
            ? this.zone.offsetName(this.ts, {
                  format: "short",
                  locale: this.locale,
              })
            : null;
    }

    get offsetNameLong() {
        return this.isValid
            ? this.zone.offsetName(this.ts, {
                  format: "long",
                  locale: this.locale,
              })
            : null;
    }

    get isOffsetFixed() {
        return this.isValid ? this.zone.isUniversal : null;
    }

    get isInDST() {
        return this.isOffsetFixed
            ? !1
            : this.offset > this.set({ month: 1, day: 1 }).offset ||
                  this.offset > this.set({ month: 5 }).offset;
    }

    getPossibleOffsets() {
        if (!this.isValid || this.isOffsetFixed) return [this];
        const t = 864e5;
        const e = 6e4;
        const i = os(this.c);
        const n = this.zone.offset(i - t);
        const o = this.zone.offset(i + t);
        const r = this.zone.offset(i - n * e);
        const a = this.zone.offset(i - o * e);
        if (r === a) return [this];
        const l = i - r * e;
        const c = i - a * e;
        const h = Sn(l, r);
        const u = Sn(c, a);
        return h.hour === u.hour &&
            h.minute === u.minute &&
            h.second === u.second &&
            h.millisecond === u.millisecond
            ? [Le(this, { ts: l }), Le(this, { ts: c })]
            : [this];
    }

    get isInLeapYear() {
        return Fe(this.year);
    }

    get daysInMonth() {
        return ls(this.year, this.month);
    }

    get daysInYear() {
        return this.isValid ? fe(this.year) : NaN;
    }

    get weeksInWeekYear() {
        return this.isValid ? Ce(this.weekYear) : NaN;
    }

    get weeksInLocalWeekYear() {
        return this.isValid
            ? Ce(
                  this.localWeekYear,
                  this.loc.getMinDaysInFirstWeek(),
                  this.loc.getStartOfWeek(),
              )
            : NaN;
    }

    resolvedLocaleOptions(t = {}) {
        const {
            locale: e,
            numberingSystem: i,
            calendar: n,
        } = it.create(this.loc.clone(t), t).resolvedOptions(this);
        return { locale: e, numberingSystem: i, outputCalendar: n };
    }

    toUTC(t = 0, e = {}) {
        return this.setZone(et.instance(t), e);
    }

    toLocal() {
        return this.setZone(R.defaultZone);
    }

    setZone(t, { keepLocalTime: e = !1, keepCalendarTime: i = !1 } = {}) {
        if (((t = Ct(t, R.defaultZone)), t.equals(this.zone))) return this;
        if (t.isValid) {
            let n = this.ts;
            if (e || i) {
                const o = t.offset(this.ts);
                const r = this.toObject();
                [n] = Mn(r, o, t);
            }
            return Le(this, { ts: n, zone: t });
        } else return s.invalid(Si(t));
    }

    reconfigure({ locale: t, numberingSystem: e, outputCalendar: i } = {}) {
        const n = this.loc.clone({
            locale: t,
            numberingSystem: e,
            outputCalendar: i,
        });
        return Le(this, { loc: n });
    }

    setLocale(t) {
        return this.reconfigure({ locale: t });
    }

    set(t) {
        if (!this.isValid) return this;
        const e = cs(t, jc);
        const { minDaysInFirstWeek: i, startOfWeek: n } = ar(e, this.loc);
        const o = !D(e.weekYear) || !D(e.weekNumber) || !D(e.weekday);
        const r = !D(e.ordinal);
        const a = !D(e.year);
        const l = !D(e.month) || !D(e.day);
        const c = a || l;
        const h = e.weekYear || e.weekNumber;
        if ((c || r) && h) {
            throw new Dt(
                "Can't mix weekYear/weekNumber units with year/month/day or ordinals",
            );
        }
        if (l && r) throw new Dt("Can't mix ordinal dates with month/day");
        let u;
        o
            ? (u = or({ ...mi(this.c, i, n), ...e }, i, n))
            : D(e.ordinal)
              ? ((u = { ...this.toObject(), ...e }),
                D(e.day) && (u.day = Math.min(ls(u.year, u.month), u.day)))
              : (u = rr({ ..._n(this.c), ...e }));
        const [d, f] = Mn(u, this.o, this.zone);
        return Le(this, { ts: d, o: f });
    }

    plus(t) {
        if (!this.isValid) return this;
        const e = G.fromDurationLike(t);
        return Le(this, Bc(this, e));
    }

    minus(t) {
        if (!this.isValid) return this;
        const e = G.fromDurationLike(t).negate();
        return Le(this, Bc(this, e));
    }

    startOf(t, { useLocaleWeeks: e = !1 } = {}) {
        if (!this.isValid) return this;
        const i = {};
        const n = G.normalizeUnit(t);
        switch (n) {
            case "years":
                i.month = 1;
            case "quarters":
            case "months":
                i.day = 1;
            case "weeks":
            case "days":
                i.hour = 0;
            case "hours":
                i.minute = 0;
            case "minutes":
                i.second = 0;
            case "seconds":
                i.millisecond = 0;
                break;
            case "milliseconds":
                break;
        }
        if (n === "weeks") {
            if (e) {
                const o = this.loc.getStartOfWeek();
                const { weekday: r } = this;
                (r < o && (i.weekNumber = this.weekNumber - 1),
                    (i.weekday = o));
            } else i.weekday = 1;
        }
        if (n === "quarters") {
            const o = Math.ceil(this.month / 3);
            i.month = (o - 1) * 3 + 1;
        }
        return this.set(i);
    }

    endOf(t, e) {
        return this.isValid
            ? this.plus({ [t]: 1 })
                  .startOf(t, e)
                  .minus(1)
            : this;
    }

    toFormat(t, e = {}) {
        return this.isValid
            ? it
                  .create(this.loc.redefaultToEN(e))
                  .formatDateTimeFromString(this, t)
            : Sr;
    }

    toLocaleString(t = he, e = {}) {
        return this.isValid
            ? it.create(this.loc.clone(e), t).formatDateTime(this)
            : Sr;
    }

    toLocaleParts(t = {}) {
        return this.isValid
            ? it.create(this.loc.clone(t), t).formatDateTimeParts(this)
            : [];
    }

    toISO({
        format: t = "extended",
        suppressSeconds: e = !1,
        suppressMilliseconds: i = !1,
        includeOffset: n = !0,
        extendedZone: o = !1,
        precision: r = "milliseconds",
    } = {}) {
        if (!this.isValid) return null;
        r = Tn(r);
        const a = t === "extended";
        let l = vr(this, a, r);
        return (
            vn.indexOf(r) >= 3 && (l += "T"),
            (l += $c(this, a, e, i, n, o, r)),
            l
        );
    }

    toISODate({ format: t = "extended", precision: e = "day" } = {}) {
        return this.isValid ? vr(this, t === "extended", Tn(e)) : null;
    }

    toISOWeekDate() {
        return kn(this, "kkkk-'W'WW-c");
    }

    toISOTime({
        suppressMilliseconds: t = !1,
        suppressSeconds: e = !1,
        includeOffset: i = !0,
        includePrefix: n = !1,
        extendedZone: o = !1,
        format: r = "extended",
        precision: a = "milliseconds",
    } = {}) {
        return this.isValid
            ? ((a = Tn(a)),
              (n && vn.indexOf(a) >= 3 ? "T" : "") +
                  $c(this, r === "extended", e, t, i, o, a))
            : null;
    }

    toRFC2822() {
        return kn(this, "EEE, dd LLL yyyy HH:mm:ss ZZZ", !1);
    }

    toHTTP() {
        return kn(this.toUTC(), "EEE, dd LLL yyyy HH:mm:ss 'GMT'");
    }

    toSQLDate() {
        return this.isValid ? vr(this, !0) : null;
    }

    toSQLTime({
        includeOffset: t = !0,
        includeZone: e = !1,
        includeOffsetSpace: i = !0,
    } = {}) {
        let n = "HH:mm:ss.SSS";
        return (
            (e || t) && (i && (n += " "), e ? (n += "z") : t && (n += "ZZ")),
            kn(this, n, !0)
        );
    }

    toSQL(t = {}) {
        return this.isValid ? `${this.toSQLDate()} ${this.toSQLTime(t)}` : null;
    }

    toString() {
        return this.isValid ? this.toISO() : Sr;
    }

    [Symbol.for("nodejs.util.inspect.custom")]() {
        return this.isValid
            ? `DateTime { ts: ${this.toISO()}, zone: ${this.zone.name}, locale: ${this.locale} }`
            : `DateTime { Invalid, reason: ${this.invalidReason} }`;
    }

    valueOf() {
        return this.toMillis();
    }

    toMillis() {
        return this.isValid ? this.ts : NaN;
    }

    toSeconds() {
        return this.isValid ? this.ts / 1e3 : NaN;
    }

    toUnixInteger() {
        return this.isValid ? Math.floor(this.ts / 1e3) : NaN;
    }

    toJSON() {
        return this.toISO();
    }

    toBSON() {
        return this.toJSDate();
    }

    toObject(t = {}) {
        if (!this.isValid) return {};
        const e = { ...this.c };
        return (
            t.includeConfig &&
                ((e.outputCalendar = this.outputCalendar),
                (e.numberingSystem = this.loc.numberingSystem),
                (e.locale = this.loc.locale)),
            e
        );
    }

    toJSDate() {
        return new Date(this.isValid ? this.ts : NaN);
    }

    diff(t, e = "milliseconds", i = {}) {
        if (!this.isValid || !t.isValid) {
            return G.invalid("created by diffing an invalid DateTime");
        }
        const n = {
            locale: this.locale,
            numberingSystem: this.numberingSystem,
            ...i,
        };
        const o = cc(e).map(G.normalizeUnit);
        const r = t.valueOf() > this.valueOf();
        const a = r ? this : t;
        const l = r ? t : this;
        const c = Pc(a, l, o, n);
        return r ? c.negate() : c;
    }

    diffNow(t = "milliseconds", e = {}) {
        return this.diff(s.now(), t, e);
    }

    until(t) {
        return this.isValid ? Qt.fromDateTimes(this, t) : this;
    }

    hasSame(t, e, i) {
        if (!this.isValid) return !1;
        const n = t.valueOf();
        const o = this.setZone(t.zone, { keepLocalTime: !0 });
        return o.startOf(e, i) <= n && n <= o.endOf(e, i);
    }

    equals(t) {
        return (
            this.isValid &&
            t.isValid &&
            this.valueOf() === t.valueOf() &&
            this.zone.equals(t.zone) &&
            this.loc.equals(t.loc)
        );
    }

    toRelative(t = {}) {
        if (!this.isValid) return null;
        const e = t.base || s.fromObject({}, { zone: this.zone });
        const i = t.padding ? (this < e ? -t.padding : t.padding) : 0;
        let n = ["years", "months", "days", "hours", "minutes", "seconds"];
        let o = t.unit;
        return (
            Array.isArray(t.unit) && ((n = t.unit), (o = void 0)),
            Yc(e, this.plus(i), { ...t, numeric: "always", units: n, unit: o })
        );
    }

    toRelativeCalendar(t = {}) {
        return this.isValid
            ? Yc(t.base || s.fromObject({}, { zone: this.zone }), this, {
                  ...t,
                  numeric: "auto",
                  units: ["years", "months", "days"],
                  calendary: !0,
              })
            : null;
    }

    static min(...t) {
        if (!t.every(s.isDateTime)) {
            throw new K("min requires all arguments be DateTimes");
        }
        return hr(t, (e) => e.valueOf(), Math.min);
    }

    static max(...t) {
        if (!t.every(s.isDateTime)) {
            throw new K("max requires all arguments be DateTimes");
        }
        return hr(t, (e) => e.valueOf(), Math.max);
    }

    static fromFormatExplain(t, e, i = {}) {
        const { locale: n = null, numberingSystem: o = null } = i;
        const r = W.fromOpts({
            locale: n,
            numberingSystem: o,
            defaultToEN: !0,
        });
        return _r(r, t, e);
    }

    static fromStringExplain(t, e, i = {}) {
        return s.fromFormatExplain(t, e, i);
    }

    static buildFormatParser(t, e = {}) {
        const { locale: i = null, numberingSystem: n = null } = e;
        const o = W.fromOpts({
            locale: i,
            numberingSystem: n,
            defaultToEN: !0,
        });
        return new wi(o, t);
    }

    static fromFormatParser(t, e, i = {}) {
        if (D(t) || D(e)) {
            throw new K(
                "fromFormatParser requires an input string and a format parser",
            );
        }
        const { locale: n = null, numberingSystem: o = null } = i;
        const r = W.fromOpts({
            locale: n,
            numberingSystem: o,
            defaultToEN: !0,
        });
        if (!r.equals(e.locale)) {
            throw new K(
                `fromFormatParser called with a locale of ${r}, but the format parser was created for ${e.locale}`,
            );
        }
        const {
            result: a,
            zone: l,
            specificOffset: c,
            invalidReason: h,
        } = e.explainFromTokens(t);
        return h ? s.invalid(h) : bs(a, l, i, `format ${e.format}`, t, c);
    }

    static get DATE_SHORT() {
        return he;
    }

    static get DATE_MED() {
        return Zs;
    }

    static get DATE_MED_WITH_WEEKDAY() {
        return Ro;
    }

    static get DATE_FULL() {
        return qs;
    }

    static get DATE_HUGE() {
        return Gs;
    }

    static get TIME_SIMPLE() {
        return Xs;
    }

    static get TIME_WITH_SECONDS() {
        return Ks;
    }

    static get TIME_WITH_SHORT_OFFSET() {
        return Js;
    }

    static get TIME_WITH_LONG_OFFSET() {
        return Qs;
    }

    static get TIME_24_SIMPLE() {
        return ti;
    }

    static get TIME_24_WITH_SECONDS() {
        return ei;
    }

    static get TIME_24_WITH_SHORT_OFFSET() {
        return si;
    }

    static get TIME_24_WITH_LONG_OFFSET() {
        return ii;
    }

    static get DATETIME_SHORT() {
        return ni;
    }

    static get DATETIME_SHORT_WITH_SECONDS() {
        return oi;
    }

    static get DATETIME_MED() {
        return ri;
    }

    static get DATETIME_MED_WITH_SECONDS() {
        return ai;
    }

    static get DATETIME_MED_WITH_WEEKDAY() {
        return Wo;
    }

    static get DATETIME_FULL() {
        return li;
    }

    static get DATETIME_FULL_WITH_SECONDS() {
        return ci;
    }

    static get DATETIME_HUGE() {
        return hi;
    }

    static get DATETIME_HUGE_WITH_SECONDS() {
        return ui;
    }
};
function ys(s) {
    if (F.isDateTime(s)) return s;
    if (s && s.valueOf && Ft(s.valueOf())) return F.fromJSDate(s);
    if (s && typeof s === "object") return F.fromObject(s);
    throw new K(`Unknown datetime argument: ${s}, of type ${typeof s}`);
}
const Jg = {
    datetime: F.DATETIME_MED_WITH_SECONDS,
    millisecond: "h:mm:ss.SSS a",
    second: F.TIME_WITH_SECONDS,
    minute: F.TIME_SIMPLE,
    hour: { hour: "numeric" },
    day: { day: "numeric", month: "short" },
    week: "DD",
    month: { month: "short", year: "numeric" },
    quarter: "'Q'q - yyyy",
    year: { year: "numeric" },
};
No._date.override({
    _id: "luxon",
    _create: function (s) {
        return F.fromMillis(s, this.options);
    },
    init(s) {
        this.options.locale || (this.options.locale = s.locale);
    },
    formats: function () {
        return Jg;
    },
    parse: function (s, t) {
        const e = this.options;
        const i = typeof s;
        return s === null || i === "undefined"
            ? null
            : (i === "number"
                  ? (s = this._create(s))
                  : i === "string"
                    ? typeof t === "string"
                        ? (s = F.fromFormat(s, t, e))
                        : (s = F.fromISO(s, e))
                    : s instanceof Date
                      ? (s = F.fromJSDate(s, e))
                      : i === "object" &&
                        !(s instanceof F) &&
                        (s = F.fromObject(s, e)),
              s.isValid ? s.valueOf() : null);
    },
    format: function (s, t) {
        const e = this._create(s);
        return typeof t === "string" ? e.toFormat(t) : e.toLocaleString(t);
    },
    add: function (s, t, e) {
        const i = {};
        return ((i[e] = t), this._create(s).plus(i).valueOf());
    },
    diff: function (s, t, e) {
        return this._create(s).diff(this._create(t)).as(e).valueOf();
    },
    startOf: function (s, t, e) {
        if (t === "isoWeek") {
            e = Math.trunc(Math.min(Math.max(0, e), 6));
            const i = this._create(s);
            return i
                .minus({ days: (i.weekday - e + 7) % 7 })
                .startOf("day")
                .valueOf();
        }
        return t ? this._create(s).startOf(t).valueOf() : s;
    },
    endOf: function (s, t) {
        return this._create(s).endOf(t).valueOf();
    },
});
function On({ cachedData: s, options: t, type: e }) {
    return {
        init() {
            (this.initChart(),
                this.$wire.$on("updateChartData", ({ data: i }) => {
                    ((On = this.getChart()),
                        (On.data = i),
                        On.update("resize"));
                }),
                Alpine.effect(() => {
                    (Alpine.store("theme"),
                        this.$nextTick(() => {
                            this.getChart() &&
                                (this.getChart().destroy(), this.initChart());
                        }));
                }),
                window
                    .matchMedia("(prefers-color-scheme: dark)")
                    .addEventListener("change", () => {
                        Alpine.store("theme") === "system" &&
                            this.$nextTick(() => {
                                (this.getChart().destroy(), this.initChart());
                            });
                    }));
        },
        initChart(i = null) {
            let r, a, l, c, h, u, d, f, m, g, p, y, b, _;
            if (
                !this.$refs.canvas ||
                !this.$refs.backgroundColorElement ||
                !this.$refs.borderColorElement ||
                !this.$refs.textColorElement ||
                !this.$refs.gridColorElement
            ) {
                return;
            }
            ((Ht.defaults.animation.duration = 0),
                (Ht.defaults.backgroundColor = getComputedStyle(
                    this.$refs.backgroundColorElement,
                ).color));
            const n = getComputedStyle(this.$refs.borderColorElement).color;
            ((Ht.defaults.borderColor = n),
                (Ht.defaults.color = getComputedStyle(
                    this.$refs.textColorElement,
                ).color),
                (Ht.defaults.font.family = getComputedStyle(
                    this.$el,
                ).fontFamily),
                (Ht.defaults.plugins.legend.labels.boxWidth = 12),
                (Ht.defaults.plugins.legend.position = "bottom"));
            const o = getComputedStyle(this.$refs.gridColorElement).color;
            return (
                t ?? (t = {}),
                t.borderWidth ?? (t.borderWidth = 2),
                t.pointBackgroundColor ?? (t.pointBackgroundColor = n),
                t.pointHitRadius ?? (t.pointHitRadius = 4),
                t.pointRadius ?? (t.pointRadius = 2),
                t.scales ?? (t.scales = {}),
                (r = t.scales).x ?? (r.x = {}),
                (a = t.scales.x).border ?? (a.border = {}),
                (l = t.scales.x.border).display ?? (l.display = !1),
                (c = t.scales.x).grid ?? (c.grid = {}),
                (h = t.scales.x.grid).color ?? (h.color = o),
                (u = t.scales.x.grid).display ?? (u.display = !1),
                (d = t.scales).y ?? (d.y = {}),
                (f = t.scales.y).border ?? (f.border = {}),
                (m = t.scales.y.border).display ?? (m.display = !1),
                (g = t.scales.y).grid ?? (g.grid = {}),
                (p = t.scales.y.grid).color ?? (p.color = o),
                ["doughnut", "pie"].includes(e) &&
                    ((y = t.scales.x).display ?? (y.display = !1),
                    (b = t.scales.y).display ?? (b.display = !1),
                    (_ = t.scales.y.grid).display ?? (_.display = !1)),
                new Ht(this.$refs.canvas, {
                    type: e,
                    data: i ?? s,
                    options: t,
                    plugins: window.filamentChartJsPlugins ?? [],
                })
            );
        },
        getChart() {
            return this.$refs.canvas ? Ht.getChart(this.$refs.canvas) : null;
        },
    };
}
export { On as default };
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

chartjs-adapter-luxon/dist/chartjs-adapter-luxon.esm.js:
  (*!
   * chartjs-adapter-luxon v1.3.1
   * https://www.chartjs.org
   * (c) 2023 chartjs-adapter-luxon Contributors
   * Released under the MIT license
   *)
*/
