const Mc = Object.defineProperty;
const Oc = (s, t, e) =>
    t in s
        ? Mc(s, t, { enumerable: !0, configurable: !0, writable: !0, value: e })
        : (s[t] = e);
const k = (s, t, e) => Oc(s, typeof t !== "symbol" ? t + "" : t, e);
function hs(s) {
    return (s + 0.5) | 0;
}
const Zt = (s, t, e) => Math.max(Math.min(s, e), t);
function ls(s) {
    return Zt(hs(s * 2.55), 0, 255);
}
function qt(s) {
    return Zt(hs(s * 255), 0, 255);
}
function Nt(s) {
    return Zt(hs(s / 2.55) / 100, 0, 1);
}
function zo(s) {
    return Zt(hs(s * 100), 0, 100);
}
const pt = {
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
const qi = [..."0123456789ABCDEF"];
const Tc = (s) => qi[s & 15];
const Dc = (s) => qi[(s & 240) >> 4] + qi[s & 15];
const js = (s) => (s & 240) >> 4 === (s & 15);
const Cc = (s) => js(s.r) && js(s.g) && js(s.b) && js(s.a);
function Pc(s) {
    const t = s.length;
    let e;
    return (
        s[0] === "#" &&
            (t === 4 || t === 5
                ? (e = {
                      r: 255 & (pt[s[1]] * 17),
                      g: 255 & (pt[s[2]] * 17),
                      b: 255 & (pt[s[3]] * 17),
                      a: t === 5 ? pt[s[4]] * 17 : 255,
                  })
                : (t === 7 || t === 9) &&
                  (e = {
                      r: (pt[s[1]] << 4) | pt[s[2]],
                      g: (pt[s[3]] << 4) | pt[s[4]],
                      b: (pt[s[5]] << 4) | pt[s[6]],
                      a: t === 9 ? (pt[s[7]] << 4) | pt[s[8]] : 255,
                  })),
        e
    );
}
const Ic = (s, t) => (s < 255 ? t(s) : "");
function Ac(s) {
    const t = Cc(s) ? Tc : Dc;
    return s ? "#" + t(s.r) + t(s.g) + t(s.b) + Ic(s.a, t) : void 0;
}
const Ec =
    /^(hsla?|hwb|hsv)\(\s*([-+.e\d]+)(?:deg)?[\s,]+([-+.e\d]+)%[\s,]+([-+.e\d]+)%(?:[\s,]+([-+.e\d]+)(%)?)?\s*\)$/;
function Ho(s, t, e) {
    const i = t * Math.min(e, 1 - e);
    const n = (o, r = (o + s / 30) % 12) =>
        e - i * Math.max(Math.min(r - 3, 9 - r, 1), -1);
    return [n(0), n(8), n(4)];
}
function Lc(s, t, e) {
    const i = (n, o = (n + s / 60) % 6) =>
        e - e * t * Math.max(Math.min(o, 4 - o, 1), 0);
    return [i(5), i(3), i(1)];
}
function Fc(s, t, e) {
    const i = Ho(s, 1, 0.5);
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
function Rc(s, t, e, i, n) {
    return s === n
        ? (t - e) / i + (t < e ? 6 : 0)
        : t === n
          ? (e - s) / i + 2
          : (s - t) / i + 4;
}
function Gi(s) {
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
            (l = Rc(e, i, n, h, o)),
            (l = l * 60 + 0.5)),
        [l | 0, c || 0, a]
    );
}
function Xi(s, t, e, i) {
    return (Array.isArray(t) ? s(t[0], t[1], t[2]) : s(t, e, i)).map(qt);
}
function Ki(s, t, e) {
    return Xi(Ho, s, t, e);
}
function Nc(s, t, e) {
    return Xi(Fc, s, t, e);
}
function zc(s, t, e) {
    return Xi(Lc, s, t, e);
}
function $o(s) {
    return ((s % 360) + 360) % 360;
}
function Vc(s) {
    const t = Ec.exec(s);
    let e = 255;
    let i;
    if (!t) return;
    t[5] !== i && (e = t[6] ? ls(+t[5]) : qt(+t[5]));
    const n = $o(+t[2]);
    const o = +t[3] / 100;
    const r = +t[4] / 100;
    return (
        t[1] === "hwb"
            ? (i = Nc(n, o, r))
            : t[1] === "hsv"
              ? (i = zc(n, o, r))
              : (i = Ki(n, o, r)),
        { r: i[0], g: i[1], b: i[2], a: e }
    );
}
function Wc(s, t) {
    let e = Gi(s);
    ((e[0] = $o(e[0] + t)),
        (e = Ki(e)),
        (s.r = e[0]),
        (s.g = e[1]),
        (s.b = e[2]));
}
function Bc(s) {
    if (!s) return;
    const t = Gi(s);
    const e = t[0];
    const i = zo(t[1]);
    const n = zo(t[2]);
    return s.a < 255
        ? `hsla(${e}, ${i}%, ${n}%, ${Nt(s.a)})`
        : `hsl(${e}, ${i}%, ${n}%)`;
}
const Vo = {
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
const Wo = {
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
function Hc() {
    const s = {};
    const t = Object.keys(Wo);
    const e = Object.keys(Vo);
    let i;
    let n;
    let o;
    let r;
    let a;
    for (i = 0; i < t.length; i++) {
        for (r = a = t[i], n = 0; n < e.length; n++) {
            ((o = e[n]), (a = a.replace(o, Vo[o])));
        }
        ((o = parseInt(Wo[r], 16)),
            (s[a] = [(o >> 16) & 255, (o >> 8) & 255, o & 255]));
    }
    return s;
}
let Us;
function $c(s) {
    Us || ((Us = Hc()), (Us.transparent = [0, 0, 0, 0]));
    const t = Us[s.toLowerCase()];
    return t && { r: t[0], g: t[1], b: t[2], a: t.length === 4 ? t[3] : 255 };
}
const jc =
    /^rgba?\(\s*([-+.\d]+)(%)?[\s,]+([-+.e\d]+)(%)?[\s,]+([-+.e\d]+)(%)?(?:[\s,/]+([-+.e\d]+)(%)?)?\s*\)$/;
function Uc(s) {
    const t = jc.exec(s);
    let e = 255;
    let i;
    let n;
    let o;
    if (t) {
        if (t[7] !== i) {
            const r = +t[7];
            e = t[8] ? ls(r) : Zt(r * 255, 0, 255);
        }
        return (
            (i = +t[1]),
            (n = +t[3]),
            (o = +t[5]),
            (i = 255 & (t[2] ? ls(i) : Zt(i, 0, 255))),
            (n = 255 & (t[4] ? ls(n) : Zt(n, 0, 255))),
            (o = 255 & (t[6] ? ls(o) : Zt(o, 0, 255))),
            { r: i, g: n, b: o, a: e }
        );
    }
}
function Yc(s) {
    return (
        s &&
        (s.a < 255
            ? `rgba(${s.r}, ${s.g}, ${s.b}, ${Nt(s.a)})`
            : `rgb(${s.r}, ${s.g}, ${s.b})`)
    );
}
const Zi = (s) =>
    s <= 0.0031308 ? s * 12.92 : Math.pow(s, 1 / 2.4) * 1.055 - 0.055;
const Te = (s) =>
    s <= 0.04045 ? s / 12.92 : Math.pow((s + 0.055) / 1.055, 2.4);
function Zc(s, t, e) {
    const i = Te(Nt(s.r));
    const n = Te(Nt(s.g));
    const o = Te(Nt(s.b));
    return {
        r: qt(Zi(i + e * (Te(Nt(t.r)) - i))),
        g: qt(Zi(n + e * (Te(Nt(t.g)) - n))),
        b: qt(Zi(o + e * (Te(Nt(t.b)) - o))),
        a: s.a + e * (t.a - s.a),
    };
}
function Ys(s, t, e) {
    if (s) {
        let i = Gi(s);
        ((i[t] = Math.max(0, Math.min(i[t] + i[t] * e, t === 0 ? 360 : 1))),
            (i = Ki(i)),
            (s.r = i[0]),
            (s.g = i[1]),
            (s.b = i[2]));
    }
}
function jo(s, t) {
    return s && Object.assign(t || {}, s);
}
function Bo(s) {
    let t = { r: 0, g: 0, b: 0, a: 255 };
    return (
        Array.isArray(s)
            ? s.length >= 3 &&
              ((t = { r: s[0], g: s[1], b: s[2], a: 255 }),
              s.length > 3 && (t.a = qt(s[3])))
            : ((t = jo(s, { r: 0, g: 0, b: 0, a: 1 })), (t.a = qt(t.a))),
        t
    );
}
function qc(s) {
    return s.charAt(0) === "r" ? Uc(s) : Vc(s);
}
const cs = class s {
    constructor(t) {
        if (t instanceof s) return t;
        const e = typeof t;
        let i;
        (e === "object"
            ? (i = Bo(t))
            : e === "string" && (i = Pc(t) || $c(t) || qc(t)),
            (this._rgb = i),
            (this._valid = !!i));
    }

    get valid() {
        return this._valid;
    }

    get rgb() {
        const t = jo(this._rgb);
        return (t && (t.a = Nt(t.a)), t);
    }

    set rgb(t) {
        this._rgb = Bo(t);
    }

    rgbString() {
        return this._valid ? Yc(this._rgb) : void 0;
    }

    hexString() {
        return this._valid ? Ac(this._rgb) : void 0;
    }

    hslString() {
        return this._valid ? Bc(this._rgb) : void 0;
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
        return (t && (this._rgb = Zc(this._rgb, t._rgb, e)), this);
    }

    clone() {
        return new s(this.rgb);
    }

    alpha(t) {
        return ((this._rgb.a = qt(t)), this);
    }

    clearer(t) {
        const e = this._rgb;
        return ((e.a *= 1 - t), this);
    }

    greyscale() {
        const t = this._rgb;
        const e = hs(t.r * 0.3 + t.g * 0.59 + t.b * 0.11);
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
        return (Ys(this._rgb, 2, t), this);
    }

    darken(t) {
        return (Ys(this._rgb, 2, -t), this);
    }

    saturate(t) {
        return (Ys(this._rgb, 1, t), this);
    }

    desaturate(t) {
        return (Ys(this._rgb, 1, -t), this);
    }

    rotate(t) {
        return (Wc(this._rgb, t), this);
    }
};
function Pt() {}
const er = (() => {
    let s = 0;
    return () => s++;
})();
function A(s) {
    return s == null;
}
function H(s) {
    if (Array.isArray && Array.isArray(s)) return !0;
    const t = Object.prototype.toString.call(s);
    return t.slice(0, 7) === "[object" && t.slice(-6) === "Array]";
}
function E(s) {
    return (
        s !== null && Object.prototype.toString.call(s) === "[object Object]"
    );
}
function Z(s) {
    return (typeof s === "number" || s instanceof Number) && isFinite(+s);
}
function at(s, t) {
    return Z(s) ? s : t;
}
function P(s, t) {
    return typeof s > "u" ? t : s;
}
const sr = (s, t) =>
    typeof s === "string" && s.endsWith("%") ? parseFloat(s) / 100 : +s / t;
const en = (s, t) =>
    typeof s === "string" && s.endsWith("%") ? (parseFloat(s) / 100) * t : +s;
function W(s, t, e) {
    if (s && typeof s.call === "function") return s.apply(e, t);
}
function z(s, t, e, i) {
    let n, o, r;
    if (H(s)) {
        if (((o = s.length), i)) {
            for (n = o - 1; n >= 0; n--) t.call(e, s[n], n);
        } else for (n = 0; n < o; n++) t.call(e, s[n], n);
    } else if (E(s)) {
        for (r = Object.keys(s), o = r.length, n = 0; n < o; n++) {
            t.call(e, s[r[n]], r[n]);
        }
    }
}
function fs(s, t) {
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
function Ks(s) {
    if (H(s)) return s.map(Ks);
    if (E(s)) {
        const t = Object.create(null);
        const e = Object.keys(s);
        const i = e.length;
        let n = 0;
        for (; n < i; ++n) t[e[n]] = Ks(s[e[n]]);
        return t;
    }
    return s;
}
function ir(s) {
    return ["__proto__", "prototype", "constructor"].indexOf(s) === -1;
}
function Gc(s, t, e, i) {
    if (!ir(s)) return;
    const n = t[s];
    const o = e[s];
    E(n) && E(o) ? Ce(n, o, i) : (t[s] = Ks(o));
}
function Ce(s, t, e) {
    const i = H(t) ? t : [t];
    const n = i.length;
    if (!E(s)) return s;
    e = e || {};
    const o = e.merger || Gc;
    let r;
    for (let a = 0; a < n; ++a) {
        if (((r = i[a]), !E(r))) continue;
        const l = Object.keys(r);
        for (let c = 0, h = l.length; c < h; ++c) o(l[c], s, r, e);
    }
    return s;
}
function Ie(s, t) {
    return Ce(s, t, { merger: Xc });
}
function Xc(s, t, e) {
    if (!ir(s)) return;
    const i = t[s];
    const n = e[s];
    E(i) && E(n)
        ? Ie(i, n)
        : Object.prototype.hasOwnProperty.call(t, s) || (t[s] = Ks(n));
}
const Uo = { "": (s) => s, x: (s) => s.x, y: (s) => s.y };
function Kc(s) {
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
function Jc(s) {
    const t = Kc(s);
    return (e) => {
        for (const i of t) {
            if (i === "") break;
            e = e && e[i];
        }
        return e;
    };
}
function Wt(s, t) {
    return (Uo[t] || (Uo[t] = Jc(t)))(s);
}
function ei(s) {
    return s.charAt(0).toUpperCase() + s.slice(1);
}
const Ae = (s) => typeof s < "u";
const zt = (s) => typeof s === "function";
const sn = (s, t) => {
    if (s.size !== t.size) return !1;
    for (const e of s) if (!t.has(e)) return !1;
    return !0;
};
function nr(s) {
    return (
        s.type === "mouseup" || s.type === "click" || s.type === "contextmenu"
    );
}
const F = Math.PI;
const $ = 2 * F;
const Qc = $ + F;
const Js = Number.POSITIVE_INFINITY;
const th = F / 180;
const q = F / 2;
const ce = F / 4;
const Yo = (F * 2) / 3;
const Vt = Math.log10;
const St = Math.sign;
function Ee(s, t, e) {
    return Math.abs(s - t) < e;
}
function nn(s) {
    const t = Math.round(s);
    s = Ee(s, t, s / 1e3) ? t : s;
    const e = Math.pow(10, Math.floor(Vt(s)));
    const i = s / e;
    return (i <= 1 ? 1 : i <= 2 ? 2 : i <= 5 ? 5 : 10) * e;
}
function or(s) {
    const t = [];
    const e = Math.sqrt(s);
    let i;
    for (i = 1; i < e; i++) s % i === 0 && (t.push(i), t.push(s / i));
    return (e === (e | 0) && t.push(e), t.sort((n, o) => n - o).pop(), t);
}
function eh(s) {
    return (
        typeof s === "symbol" ||
        (typeof s === "object" &&
            s !== null &&
            !(Symbol.toPrimitive in s || "toString" in s || "valueOf" in s))
    );
}
function de(s) {
    return !eh(s) && !isNaN(parseFloat(s)) && isFinite(s);
}
function rr(s, t) {
    const e = Math.round(s);
    return e - t <= s && e + t >= s;
}
function on(s, t, e) {
    let i, n, o;
    for (i = 0, n = s.length; i < n; i++) {
        ((o = s[i][e]),
            isNaN(o) ||
                ((t.min = Math.min(t.min, o)), (t.max = Math.max(t.max, o))));
    }
}
function bt(s) {
    return s * (F / 180);
}
function si(s) {
    return s * (180 / F);
}
function rn(s) {
    if (!Z(s)) return;
    let t = 1;
    let e = 0;
    for (; Math.round(s * t) / t !== s; ) ((t *= 10), e++);
    return e;
}
function an(s, t) {
    const e = t.x - s.x;
    const i = t.y - s.y;
    const n = Math.sqrt(e * e + i * i);
    let o = Math.atan2(i, e);
    return (o < -0.5 * F && (o += $), { angle: o, distance: n });
}
function Qs(s, t) {
    return Math.sqrt(Math.pow(t.x - s.x, 2) + Math.pow(t.y - s.y, 2));
}
function sh(s, t) {
    return ((s - t + Qc) % $) - F;
}
function st(s) {
    return ((s % $) + $) % $;
}
function Le(s, t, e, i) {
    const n = st(s);
    const o = st(t);
    const r = st(e);
    const a = st(o - n);
    const l = st(r - n);
    const c = st(n - o);
    const h = st(n - r);
    return n === o || n === r || (i && o === r) || (a > l && c < h);
}
function J(s, t, e) {
    return Math.max(t, Math.min(e, s));
}
function ar(s) {
    return J(s, -32768, 32767);
}
function It(s, t, e, i = 1e-6) {
    return s >= Math.min(t, e) - i && s <= Math.max(t, e) + i;
}
function ii(s, t, e) {
    e = e || ((r) => s[r] < t);
    let i = s.length - 1;
    let n = 0;
    let o;
    for (; i - n > 1; ) ((o = (n + i) >> 1), e(o) ? (n = o) : (i = o));
    return { lo: n, hi: i };
}
const Dt = (s, t, e, i) =>
    ii(
        s,
        e,
        i
            ? (n) => {
                  const o = s[n][t];
                  return o < e || (o === e && s[n + 1][t] === e);
              }
            : (n) => s[n][t] < e,
    );
const lr = (s, t, e) => ii(s, e, (i) => s[i][t] >= e);
function cr(s, t, e) {
    let i = 0;
    let n = s.length;
    for (; i < n && s[i] < t; ) i++;
    for (; n > i && s[n - 1] > e; ) n--;
    return i > 0 || n < s.length ? s.slice(i, n) : s;
}
const hr = ["push", "pop", "shift", "splice", "unshift"];
function ur(s, t) {
    if (s._chartjs) {
        s._chartjs.listeners.push(t);
        return;
    }
    (Object.defineProperty(s, "_chartjs", {
        configurable: !0,
        enumerable: !1,
        value: { listeners: [t] },
    }),
        hr.forEach((e) => {
            const i = "_onData" + ei(e);
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
function ln(s, t) {
    const e = s._chartjs;
    if (!e) return;
    const i = e.listeners;
    const n = i.indexOf(t);
    (n !== -1 && i.splice(n, 1),
        !(i.length > 0) &&
            (hr.forEach((o) => {
                delete s[o];
            }),
            delete s._chartjs));
}
function cn(s) {
    const t = new Set(s);
    return t.size === s.length ? s : Array.from(t);
}
const hn = (function () {
    return typeof window > "u"
        ? function (s) {
              return s();
          }
        : window.requestAnimationFrame;
})();
function un(s, t) {
    let e = [];
    let i = !1;
    return function (...n) {
        ((e = n),
            i ||
                ((i = !0),
                hn.call(window, () => {
                    ((i = !1), s.apply(t, e));
                })));
    };
}
function dr(s, t) {
    let e;
    return function (...i) {
        return (
            t ? (clearTimeout(e), (e = setTimeout(s, t, i))) : s.apply(this, i),
            t
        );
    };
}
const ni = (s) => (s === "start" ? "left" : s === "end" ? "right" : "center");
const it = (s, t, e) => (s === "start" ? t : s === "end" ? e : (t + e) / 2);
const fr = (s, t, e, i) =>
    s === (i ? "left" : "right") ? e : s === "center" ? (t + e) / 2 : t;
function dn(s, t, e) {
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
            maxDefined: g,
        } = r.getUserBounds();
        if (f) {
            if (
                ((n = Math.min(
                    Dt(l, h, u).lo,
                    e ? i : Dt(t, h, r.getPixelForValue(u)).lo,
                )),
                c)
            ) {
                const m = l
                    .slice(0, n + 1)
                    .reverse()
                    .findIndex((p) => !A(p[a.axis]));
                n -= Math.max(0, m);
            }
            n = J(n, 0, i - 1);
        }
        if (g) {
            let m = Math.max(
                Dt(l, r.axis, d, !0).hi + 1,
                e ? 0 : Dt(t, h, r.getPixelForValue(d), !0).hi + 1,
            );
            if (c) {
                const p = l.slice(m - 1).findIndex((b) => !A(b[a.axis]));
                m += Math.max(0, p);
            }
            o = J(m, n, i) - n;
        } else o = i - n;
    }
    return { start: n, count: o };
}
function fn(s) {
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
const Zs = (s) => s === 0 || s === 1;
const Zo = (s, t, e) =>
    -(Math.pow(2, 10 * (s -= 1)) * Math.sin(((s - t) * $) / e));
const qo = (s, t, e) => Math.pow(2, -10 * s) * Math.sin(((s - t) * $) / e) + 1;
var De = {
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
    easeInSine: (s) => -Math.cos(s * q) + 1,
    easeOutSine: (s) => Math.sin(s * q),
    easeInOutSine: (s) => -0.5 * (Math.cos(F * s) - 1),
    easeInExpo: (s) => (s === 0 ? 0 : Math.pow(2, 10 * (s - 1))),
    easeOutExpo: (s) => (s === 1 ? 1 : -Math.pow(2, -10 * s) + 1),
    easeInOutExpo: (s) =>
        Zs(s)
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
    easeInElastic: (s) => (Zs(s) ? s : Zo(s, 0.075, 0.3)),
    easeOutElastic: (s) => (Zs(s) ? s : qo(s, 0.075, 0.3)),
    easeInOutElastic(s) {
        return Zs(s)
            ? s
            : s < 0.5
              ? 0.5 * Zo(s * 2, 0.1125, 0.45)
              : 0.5 + 0.5 * qo(s * 2 - 1, 0.1125, 0.45);
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
    easeInBounce: (s) => 1 - De.easeOutBounce(1 - s),
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
            ? De.easeInBounce(s * 2) * 0.5
            : De.easeOutBounce(s * 2 - 1) * 0.5 + 0.5,
};
function gn(s) {
    if (s && typeof s === "object") {
        const t = s.toString();
        return (
            t === "[object CanvasPattern]" || t === "[object CanvasGradient]"
        );
    }
    return !1;
}
function mn(s) {
    return gn(s) ? s : new cs(s);
}
function Ji(s) {
    return gn(s) ? s : new cs(s).saturate(0.5).darken(0.1).hexString();
}
const ih = ["x", "y", "borderWidth", "radius", "tension"];
const nh = ["color", "borderColor", "backgroundColor"];
function oh(s) {
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
            colors: { type: "color", properties: nh },
            numbers: { type: "number", properties: ih },
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
function rh(s) {
    s.set("layout", {
        autoPadding: !0,
        padding: { top: 0, right: 0, bottom: 0, left: 0 },
    });
}
const Go = new Map();
function ah(s, t) {
    t = t || {};
    const e = s + JSON.stringify(t);
    let i = Go.get(e);
    return (i || ((i = new Intl.NumberFormat(s, t)), Go.set(e, i)), i);
}
function Fe(s, t, e) {
    return ah(t, e).format(s);
}
var gr = {
    values(s) {
        return H(s) ? s : "" + s;
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
            ((c < 1e-4 || c > 1e15) && (n = "scientific"), (o = lh(s, e)));
        }
        const r = Vt(Math.abs(o));
        const a = isNaN(r) ? 1 : Math.max(Math.min(-1 * Math.floor(r), 20), 0);
        const l = {
            notation: n,
            minimumFractionDigits: a,
            maximumFractionDigits: a,
        };
        return (Object.assign(l, this.options.ticks.format), Fe(s, i, l));
    },
    logarithmic(s, t, e) {
        if (s === 0) return "0";
        const i = e[t].significand || s / Math.pow(10, Math.floor(Vt(s)));
        return [1, 2, 3, 5, 10, 15].includes(i) || t > 0.8 * e.length
            ? gr.numeric.call(this, s, t, e)
            : "";
    },
};
function lh(s, t) {
    let e = t.length > 3 ? t[2].value - t[1].value : t[1].value - t[0].value;
    return (
        Math.abs(e) >= 1 && s !== Math.floor(s) && (e = s - Math.floor(s)),
        e
    );
}
const gs = { formatters: gr };
function ch(s) {
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
            callback: gs.formatters.values,
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
const Xt = Object.create(null);
const oi = Object.create(null);
function us(s, t) {
    if (!t) return s;
    const e = t.split(".");
    for (let i = 0, n = e.length; i < n; ++i) {
        const o = e[i];
        s = s[o] || (s[o] = Object.create(null));
    }
    return s;
}
function Qi(s, t, e) {
    return typeof t === "string" ? Ce(us(s, t), e) : Ce(us(s, ""), t);
}
const tn = class {
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
            (this.hoverBackgroundColor = (i, n) => Ji(n.backgroundColor)),
            (this.hoverBorderColor = (i, n) => Ji(n.borderColor)),
            (this.hoverColor = (i, n) => Ji(n.color)),
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
        return Qi(this, t, e);
    }

    get(t) {
        return us(this, t);
    }

    describe(t, e) {
        return Qi(oi, t, e);
    }

    override(t, e) {
        return Qi(Xt, t, e);
    }

    route(t, e, i, n) {
        const o = us(this, t);
        const r = us(this, i);
        const a = "_" + e;
        Object.defineProperties(o, {
            [a]: { value: o[e], writable: !0 },
            [e]: {
                enumerable: !0,
                get() {
                    const l = this[a];
                    const c = r[n];
                    return E(l) ? Object.assign({}, c, l) : P(l, c);
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
const j = new tn(
    {
        _scriptable: (s) => !s.startsWith("on"),
        _indexable: (s) => s !== "events",
        hover: { _fallback: "interaction" },
        interaction: { _scriptable: !1, _indexable: !1 },
    },
    [oh, rh, ch],
);
function hh(s) {
    return !s || A(s.size) || A(s.family)
        ? null
        : (s.style ? s.style + " " : "") +
              (s.weight ? s.weight + " " : "") +
              s.size +
              "px " +
              s.family;
}
function ds(s, t, e, i, n) {
    let o = t[n];
    return (
        o || ((o = t[n] = s.measureText(n).width), e.push(n)),
        o > i && (i = o),
        i
    );
}
function mr(s, t, e, i) {
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
        if (((u = e[l]), u != null && !H(u))) r = ds(s, n, o, r, u);
        else if (H(u)) {
            for (c = 0, h = u.length; c < h; c++) {
                ((d = u[c]), d != null && !H(d) && (r = ds(s, n, o, r, d)));
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
function Kt(s, t, e) {
    const i = s.currentDevicePixelRatio;
    const n = e !== 0 ? Math.max(e / 2, 0.5) : 0;
    return Math.round((t - n) * i) / i + n;
}
function pn(s, t) {
    (!t && !s) ||
        ((t = t || s.getContext("2d")),
        t.save(),
        t.resetTransform(),
        t.clearRect(0, 0, s.width, s.height),
        t.restore());
}
function ri(s, t, e, i) {
    bn(s, t, e, i, null);
}
function bn(s, t, e, i, n) {
    let o;
    let r;
    let a;
    let l;
    let c;
    let h;
    let u;
    let d;
    const f = t.pointStyle;
    const g = t.rotation;
    const m = t.radius;
    let p = (g || 0) * th;
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
    if (!(isNaN(m) || m <= 0)) {
        switch ((s.beginPath(), f)) {
            default:
                (n ? s.ellipse(e, i, n / 2, m, 0, 0, $) : s.arc(e, i, m, 0, $),
                    s.closePath());
                break;
            case "triangle":
                ((h = n ? n / 2 : m),
                    s.moveTo(e + Math.sin(p) * h, i - Math.cos(p) * m),
                    (p += Yo),
                    s.lineTo(e + Math.sin(p) * h, i - Math.cos(p) * m),
                    (p += Yo),
                    s.lineTo(e + Math.sin(p) * h, i - Math.cos(p) * m),
                    s.closePath());
                break;
            case "rectRounded":
                ((c = m * 0.516),
                    (l = m - c),
                    (r = Math.cos(p + ce) * l),
                    (u = Math.cos(p + ce) * (n ? n / 2 - c : l)),
                    (a = Math.sin(p + ce) * l),
                    (d = Math.sin(p + ce) * (n ? n / 2 - c : l)),
                    s.arc(e - u, i - a, c, p - F, p - q),
                    s.arc(e + d, i - r, c, p - q, p),
                    s.arc(e + u, i + a, c, p, p + q),
                    s.arc(e - d, i + r, c, p + q, p + F),
                    s.closePath());
                break;
            case "rect":
                if (!g) {
                    ((l = Math.SQRT1_2 * m),
                        (h = n ? n / 2 : l),
                        s.rect(e - h, i - l, 2 * h, 2 * l));
                    break;
                }
                p += ce;
            case "rectRot":
                ((u = Math.cos(p) * (n ? n / 2 : m)),
                    (r = Math.cos(p) * m),
                    (a = Math.sin(p) * m),
                    (d = Math.sin(p) * (n ? n / 2 : m)),
                    s.moveTo(e - u, i - a),
                    s.lineTo(e + d, i - r),
                    s.lineTo(e + u, i + a),
                    s.lineTo(e - d, i + r),
                    s.closePath());
                break;
            case "crossRot":
                p += ce;
            case "cross":
                ((u = Math.cos(p) * (n ? n / 2 : m)),
                    (r = Math.cos(p) * m),
                    (a = Math.sin(p) * m),
                    (d = Math.sin(p) * (n ? n / 2 : m)),
                    s.moveTo(e - u, i - a),
                    s.lineTo(e + u, i + a),
                    s.moveTo(e + d, i - r),
                    s.lineTo(e - d, i + r));
                break;
            case "star":
                ((u = Math.cos(p) * (n ? n / 2 : m)),
                    (r = Math.cos(p) * m),
                    (a = Math.sin(p) * m),
                    (d = Math.sin(p) * (n ? n / 2 : m)),
                    s.moveTo(e - u, i - a),
                    s.lineTo(e + u, i + a),
                    s.moveTo(e + d, i - r),
                    s.lineTo(e - d, i + r),
                    (p += ce),
                    (u = Math.cos(p) * (n ? n / 2 : m)),
                    (r = Math.cos(p) * m),
                    (a = Math.sin(p) * m),
                    (d = Math.sin(p) * (n ? n / 2 : m)),
                    s.moveTo(e - u, i - a),
                    s.lineTo(e + u, i + a),
                    s.moveTo(e + d, i - r),
                    s.lineTo(e - d, i + r));
                break;
            case "line":
                ((r = n ? n / 2 : Math.cos(p) * m),
                    (a = Math.sin(p) * m),
                    s.moveTo(e - r, i - a),
                    s.lineTo(e + r, i + a));
                break;
            case "dash":
                (s.moveTo(e, i),
                    s.lineTo(
                        e + Math.cos(p) * (n ? n / 2 : m),
                        i + Math.sin(p) * m,
                    ));
                break;
            case !1:
                s.closePath();
                break;
        }
        (s.fill(), t.borderWidth > 0 && s.stroke());
    }
}
function Ct(s, t, e) {
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
function ms(s, t) {
    (s.save(),
        s.beginPath(),
        s.rect(t.left, t.top, t.right - t.left, t.bottom - t.top),
        s.clip());
}
function ps(s) {
    s.restore();
}
function pr(s, t, e, i, n) {
    if (!t) return s.lineTo(e.x, e.y);
    if (n === "middle") {
        const o = (t.x + e.x) / 2;
        (s.lineTo(o, t.y), s.lineTo(o, e.y));
    } else (n === "after") != !!i ? s.lineTo(t.x, e.y) : s.lineTo(e.x, t.y);
    s.lineTo(e.x, e.y);
}
function br(s, t, e, i) {
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
function uh(s, t) {
    (t.translation && s.translate(t.translation[0], t.translation[1]),
        A(t.rotation) || s.rotate(t.rotation),
        t.color && (s.fillStyle = t.color),
        t.textAlign && (s.textAlign = t.textAlign),
        t.textBaseline && (s.textBaseline = t.textBaseline));
}
function dh(s, t, e, i, n) {
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
function fh(s, t) {
    const e = s.fillStyle;
    ((s.fillStyle = t.color),
        s.fillRect(t.left, t.top, t.width, t.height),
        (s.fillStyle = e));
}
function Jt(s, t, e, i, n, o = {}) {
    const r = H(t) ? t : [t];
    const a = o.strokeWidth > 0 && o.strokeColor !== "";
    let l;
    let c;
    for (s.save(), s.font = n.string, uh(s, o), l = 0; l < r.length; ++l) {
        ((c = r[l]),
            o.backdrop && fh(s, o.backdrop),
            a &&
                (o.strokeColor && (s.strokeStyle = o.strokeColor),
                A(o.strokeWidth) || (s.lineWidth = o.strokeWidth),
                s.strokeText(c, e, i, o.maxWidth)),
            s.fillText(c, e, i, o.maxWidth),
            dh(s, e, i, c, o),
            (i += Number(n.lineHeight)));
    }
    s.restore();
}
function Re(s, t) {
    const { x: e, y: i, w: n, h: o, radius: r } = t;
    (s.arc(e + r.topLeft, i + r.topLeft, r.topLeft, 1.5 * F, F, !0),
        s.lineTo(e, i + o - r.bottomLeft),
        s.arc(e + r.bottomLeft, i + o - r.bottomLeft, r.bottomLeft, F, q, !0),
        s.lineTo(e + n - r.bottomRight, i + o),
        s.arc(
            e + n - r.bottomRight,
            i + o - r.bottomRight,
            r.bottomRight,
            q,
            0,
            !0,
        ),
        s.lineTo(e + n, i + r.topRight),
        s.arc(e + n - r.topRight, i + r.topRight, r.topRight, 0, -q, !0),
        s.lineTo(e + r.topLeft, i));
}
const gh = /^(normal|(\d+(?:\.\d+)?)(px|em|%)?)$/;
const mh =
    /^(normal|italic|initial|inherit|unset|(oblique( -?[0-9]?[0-9]deg)?))$/;
function ph(s, t) {
    const e = ("" + s).match(gh);
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
const bh = (s) => +s || 0;
function ai(s, t) {
    const e = {};
    const i = E(t);
    const n = i ? Object.keys(t) : t;
    const o = E(s) ? (i ? (r) => P(s[r], s[t[r]]) : (r) => s[r]) : () => s;
    for (const r of n) e[r] = bh(o(r));
    return e;
}
function yn(s) {
    return ai(s, { top: "y", right: "x", bottom: "y", left: "x" });
}
function Qt(s) {
    return ai(s, ["topLeft", "topRight", "bottomLeft", "bottomRight"]);
}
function nt(s) {
    const t = yn(s);
    return ((t.width = t.left + t.right), (t.height = t.top + t.bottom), t);
}
function X(s, t) {
    ((s = s || {}), (t = t || j.font));
    let e = P(s.size, t.size);
    typeof e === "string" && (e = parseInt(e, 10));
    let i = P(s.style, t.style);
    i &&
        !("" + i).match(mh) &&
        (console.warn('Invalid font style specified: "' + i + '"'),
        (i = void 0));
    const n = {
        family: P(s.family, t.family),
        lineHeight: ph(P(s.lineHeight, t.lineHeight), e),
        size: e,
        style: i,
        weight: P(s.weight, t.weight),
        string: "",
    };
    return ((n.string = hh(n)), n);
}
function Ne(s, t, e, i) {
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
                e !== void 0 && H(a) && ((a = a[e % a.length]), (n = !1)),
                a !== void 0))
        ) {
            return (i && !n && (i.cacheable = !1), a);
        }
    }
}
function yr(s, t, e) {
    const { min: i, max: n } = s;
    const o = en(t, (n - i) / 2);
    const r = (a, l) => (e && a === 0 ? 0 : a + l);
    return { min: r(i, -Math.abs(o)), max: r(n, o) };
}
function Bt(s, t) {
    return Object.assign(Object.create(s), t);
}
function li(s, t = [""], e, i, n = () => s[0]) {
    const o = e || s;
    typeof i > "u" && (i = wr("_fallback", s));
    const r = {
        [Symbol.toStringTag]: "Object",
        _cacheable: !0,
        _scopes: s,
        _rootScopes: o,
        _fallback: i,
        _getTarget: n,
        override: (a) => li([a, ...s], t, o, i),
    };
    return new Proxy(r, {
        deleteProperty(a, l) {
            return (delete a[l], delete a._keys, delete s[0][l], !0);
        },
        get(a, l) {
            return xr(a, l, () => Mh(l, t, s, a));
        },
        getOwnPropertyDescriptor(a, l) {
            return Reflect.getOwnPropertyDescriptor(a._scopes[0], l);
        },
        getPrototypeOf() {
            return Reflect.getPrototypeOf(s[0]);
        },
        has(a, l) {
            return Ko(a).includes(l);
        },
        ownKeys(a) {
            return Ko(a);
        },
        set(a, l, c) {
            const h = a._storage || (a._storage = n());
            return ((a[l] = h[l] = c), delete a._keys, !0);
        },
    });
}
function ue(s, t, e, i) {
    const n = {
        _cacheable: !1,
        _proxy: s,
        _context: t,
        _subProxy: e,
        _stack: new Set(),
        _descriptors: xn(s, i),
        setContext: (o) => ue(s, o, e, i),
        override: (o) => ue(s.override(o), t, e, i),
    };
    return new Proxy(n, {
        deleteProperty(o, r) {
            return (delete o[r], delete s[r], !0);
        },
        get(o, r, a) {
            return xr(o, r, () => xh(o, r, a));
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
function xn(s, t = { scriptable: !0, indexable: !0 }) {
    const {
        _scriptable: e = t.scriptable,
        _indexable: i = t.indexable,
        _allKeys: n = t.allKeys,
    } = s;
    return {
        allKeys: n,
        scriptable: e,
        indexable: i,
        isScriptable: zt(e) ? e : () => e,
        isIndexable: zt(i) ? i : () => i,
    };
}
const yh = (s, t) => (s ? s + ei(t) : t);
const _n = (s, t) =>
    E(t) &&
    s !== "adapters" &&
    (Object.getPrototypeOf(t) === null || t.constructor === Object);
function xr(s, t, e) {
    if (Object.prototype.hasOwnProperty.call(s, t) || t === "constructor") {
        return s[t];
    }
    const i = e();
    return ((s[t] = i), i);
}
function xh(s, t, e) {
    const { _proxy: i, _context: n, _subProxy: o, _descriptors: r } = s;
    let a = i[t];
    return (
        zt(a) && r.isScriptable(t) && (a = _h(t, a, s, e)),
        H(a) && a.length && (a = wh(t, a, s, r.isIndexable)),
        _n(t, a) && (a = ue(a, n, o && o[t], r)),
        a
    );
}
function _h(s, t, e, i) {
    const { _proxy: n, _context: o, _subProxy: r, _stack: a } = e;
    if (a.has(s)) {
        throw new Error(
            "Recursion detected: " + Array.from(a).join("->") + "->" + s,
        );
    }
    a.add(s);
    let l = t(o, r || i);
    return (a.delete(s), _n(s, l) && (l = wn(n._scopes, n, s, l)), l);
}
function wh(s, t, e, i) {
    const { _proxy: n, _context: o, _subProxy: r, _descriptors: a } = e;
    if (typeof o.index < "u" && i(s)) return t[o.index % t.length];
    if (E(t[0])) {
        const l = t;
        const c = n._scopes.filter((h) => h !== l);
        t = [];
        for (const h of l) {
            const u = wn(c, n, s, h);
            t.push(ue(u, o, r && r[s], a));
        }
    }
    return t;
}
function _r(s, t, e) {
    return zt(s) ? s(t, e) : s;
}
const kh = (s, t) => (s === !0 ? t : typeof s === "string" ? Wt(t, s) : void 0);
function vh(s, t, e, i, n) {
    for (const o of t) {
        const r = kh(e, o);
        if (r) {
            s.add(r);
            const a = _r(r._fallback, e, n);
            if (typeof a < "u" && a !== e && a !== i) return a;
        } else if (r === !1 && typeof i < "u" && e !== i) return null;
    }
    return !1;
}
function wn(s, t, e, i) {
    const n = t._rootScopes;
    const o = _r(t._fallback, e, i);
    const r = [...s, ...n];
    const a = new Set();
    a.add(i);
    let l = Xo(a, r, e, o || e, i);
    return l === null ||
        (typeof o < "u" && o !== e && ((l = Xo(a, r, o, l, i)), l === null))
        ? !1
        : li(Array.from(a), [""], n, o, () => Sh(t, e, i));
}
function Xo(s, t, e, i, n) {
    for (; e; ) e = vh(s, t, e, i, n);
    return e;
}
function Sh(s, t, e) {
    const i = s._getTarget();
    t in i || (i[t] = {});
    const n = i[t];
    return H(n) && E(e) ? e : n || {};
}
function Mh(s, t, e, i) {
    let n;
    for (const o of t) {
        if (((n = wr(yh(o, s), e)), typeof n < "u")) {
            return _n(s, n) ? wn(e, i, s, n) : n;
        }
    }
}
function wr(s, t) {
    for (const e of t) {
        if (!e) continue;
        const i = e[s];
        if (typeof i < "u") return i;
    }
}
function Ko(s) {
    let t = s._keys;
    return (t || (t = s._keys = Oh(s._scopes)), t);
}
function Oh(s) {
    const t = new Set();
    for (const e of s) {
        for (const i of Object.keys(e).filter((n) => !n.startsWith("_"))) {
            t.add(i);
        }
    }
    return Array.from(t);
}
function kn(s, t, e, i) {
    const { iScale: n } = s;
    const { key: o = "r" } = this._parsing;
    const r = new Array(i);
    let a;
    let l;
    let c;
    let h;
    for (a = 0, l = i; a < l; ++a) {
        ((c = a + e), (h = t[c]), (r[a] = { r: n.parse(Wt(h, o), c) }));
    }
    return r;
}
const Th = Number.EPSILON || 1e-14;
const Pe = (s, t) => t < s.length && !s[t].skip && s[t];
const kr = (s) => (s === "x" ? "y" : "x");
function Dh(s, t, e, i) {
    const n = s.skip ? t : s;
    const o = t;
    const r = e.skip ? t : e;
    const a = Qs(o, n);
    const l = Qs(r, o);
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
function Ch(s, t, e) {
    const i = s.length;
    let n;
    let o;
    let r;
    let a;
    let l;
    let c = Pe(s, 0);
    for (let h = 0; h < i - 1; ++h) {
        if (((l = c), (c = Pe(s, h + 1)), !(!l || !c))) {
            if (Ee(t[h], 0, Th)) {
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
function Ph(s, t, e = "x") {
    const i = kr(e);
    const n = s.length;
    let o;
    let r;
    let a;
    let l = Pe(s, 0);
    for (let c = 0; c < n; ++c) {
        if (((r = a), (a = l), (l = Pe(s, c + 1)), !a)) continue;
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
function Ih(s, t = "x") {
    const e = kr(t);
    const i = s.length;
    const n = Array(i).fill(0);
    const o = Array(i);
    let r;
    let a;
    let l;
    let c = Pe(s, 0);
    for (r = 0; r < i; ++r) {
        if (((a = l), (l = c), (c = Pe(s, r + 1)), !!l)) {
            if (c) {
                const h = c[t] - l[t];
                n[r] = h !== 0 ? (c[e] - l[e]) / h : 0;
            }
            o[r] = a
                ? c
                    ? St(n[r - 1]) !== St(n[r])
                        ? 0
                        : (n[r - 1] + n[r]) / 2
                    : n[r - 1]
                : n[r];
        }
    }
    (Ch(s, n, o), Ph(s, o, t));
}
function qs(s, t, e) {
    return Math.max(Math.min(s, e), t);
}
function Ah(s, t) {
    let e;
    let i;
    let n;
    let o;
    let r;
    let a = Ct(s[0], t);
    for (e = 0, i = s.length; e < i; ++e) {
        ((r = o),
            (o = a),
            (a = e < i - 1 && Ct(s[e + 1], t)),
            o &&
                ((n = s[e]),
                r &&
                    ((n.cp1x = qs(n.cp1x, t.left, t.right)),
                    (n.cp1y = qs(n.cp1y, t.top, t.bottom))),
                a &&
                    ((n.cp2x = qs(n.cp2x, t.left, t.right)),
                    (n.cp2y = qs(n.cp2y, t.top, t.bottom)))));
    }
}
function vr(s, t, e, i, n) {
    let o, r, a, l;
    if (
        (t.spanGaps && (s = s.filter((c) => !c.skip)),
        t.cubicInterpolationMode === "monotone")
    ) {
        Ih(s, n);
    } else {
        let c = i ? s[s.length - 1] : s[0];
        for (o = 0, r = s.length; o < r; ++o) {
            ((a = s[o]),
                (l = Dh(
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
    t.capBezierPoints && Ah(s, e);
}
function ci() {
    return typeof window < "u" && typeof document < "u";
}
function hi(s) {
    let t = s.parentNode;
    return (t && t.toString() === "[object ShadowRoot]" && (t = t.host), t);
}
function ti(s, t, e) {
    let i;
    return (
        typeof s === "string"
            ? ((i = parseInt(s, 10)),
              s.indexOf("%") !== -1 && (i = (i / 100) * t.parentNode[e]))
            : (i = s),
        i
    );
}
const ui = (s) => s.ownerDocument.defaultView.getComputedStyle(s, null);
function Eh(s, t) {
    return ui(s).getPropertyValue(t);
}
const Lh = ["top", "right", "bottom", "left"];
function he(s, t, e) {
    const i = {};
    e = e ? "-" + e : "";
    for (let n = 0; n < 4; n++) {
        const o = Lh[n];
        i[o] = parseFloat(s[t + "-" + o + e]) || 0;
    }
    return ((i.width = i.left + i.right), (i.height = i.top + i.bottom), i);
}
const Fh = (s, t, e) => (s > 0 || t > 0) && (!e || !e.shadowRoot);
function Rh(s, t) {
    const e = s.touches;
    const i = e && e.length ? e[0] : s;
    const { offsetX: n, offsetY: o } = i;
    let r = !1;
    let a;
    let l;
    if (Fh(n, o, s.target)) ((a = n), (l = o));
    else {
        const c = t.getBoundingClientRect();
        ((a = i.clientX - c.left), (l = i.clientY - c.top), (r = !0));
    }
    return { x: a, y: l, box: r };
}
function te(s, t) {
    if ("native" in s) return s;
    const { canvas: e, currentDevicePixelRatio: i } = t;
    const n = ui(e);
    const o = n.boxSizing === "border-box";
    const r = he(n, "padding");
    const a = he(n, "border", "width");
    const { x: l, y: c, box: h } = Rh(s, e);
    const u = r.left + (h && a.left);
    const d = r.top + (h && a.top);
    let { width: f, height: g } = t;
    return (
        o && ((f -= r.width + a.width), (g -= r.height + a.height)),
        {
            x: Math.round((((l - u) / f) * e.width) / i),
            y: Math.round((((c - d) / g) * e.height) / i),
        }
    );
}
function Nh(s, t, e) {
    let i, n;
    if (t === void 0 || e === void 0) {
        const o = s && hi(s);
        if (!o) ((t = s.clientWidth), (e = s.clientHeight));
        else {
            const r = o.getBoundingClientRect();
            const a = ui(o);
            const l = he(a, "border", "width");
            const c = he(a, "padding");
            ((t = r.width - c.width - l.width),
                (e = r.height - c.height - l.height),
                (i = ti(a.maxWidth, o, "clientWidth")),
                (n = ti(a.maxHeight, o, "clientHeight")));
        }
    }
    return { width: t, height: e, maxWidth: i || Js, maxHeight: n || Js };
}
const Gs = (s) => Math.round(s * 10) / 10;
function Sr(s, t, e, i) {
    const n = ui(s);
    const o = he(n, "margin");
    const r = ti(n.maxWidth, s, "clientWidth") || Js;
    const a = ti(n.maxHeight, s, "clientHeight") || Js;
    const l = Nh(s, t, e);
    let { width: c, height: h } = l;
    if (n.boxSizing === "content-box") {
        const d = he(n, "border", "width");
        const f = he(n, "padding");
        ((c -= f.width + d.width), (h -= f.height + d.height));
    }
    return (
        (c = Math.max(0, c - o.width)),
        (h = Math.max(0, i ? c / i : h - o.height)),
        (c = Gs(Math.min(c, r, l.maxWidth))),
        (h = Gs(Math.min(h, a, l.maxHeight))),
        c && !h && (h = Gs(c / 2)),
        (t !== void 0 || e !== void 0) &&
            i &&
            l.height &&
            h > l.height &&
            ((h = l.height), (c = Gs(Math.floor(h * i)))),
        { width: c, height: h }
    );
}
function vn(s, t, e) {
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
const Mr = (function () {
    let s = !1;
    try {
        const t = {
            get passive() {
                return ((s = !0), !1);
            },
        };
        ci() &&
            (window.addEventListener("test", null, t),
            window.removeEventListener("test", null, t));
    } catch {}
    return s;
})();
function Sn(s, t) {
    const e = Eh(s, t);
    const i = e && e.match(/^(\d+)(\.\d+)?px$/);
    return i ? +i[1] : void 0;
}
function Gt(s, t, e, i) {
    return { x: s.x + e * (t.x - s.x), y: s.y + e * (t.y - s.y) };
}
function Or(s, t, e, i) {
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
function Tr(s, t, e, i) {
    const n = { x: s.cp2x, y: s.cp2y };
    const o = { x: t.cp1x, y: t.cp1y };
    const r = Gt(s, n, e);
    const a = Gt(n, o, e);
    const l = Gt(o, t, e);
    const c = Gt(r, a, e);
    const h = Gt(a, l, e);
    return Gt(c, h, e);
}
const zh = function (s, t) {
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
const Vh = function () {
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
function fe(s, t, e) {
    return s ? zh(t, e) : Vh();
}
function Mn(s, t) {
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
function On(s, t) {
    t !== void 0 &&
        (delete s.prevTextDirection,
        s.canvas.style.setProperty("direction", t[0], t[1]));
}
function Dr(s) {
    return s === "angle"
        ? { between: Le, compare: sh, normalize: st }
        : { between: It, compare: (t, e) => t - e, normalize: (t) => t };
}
function Jo({ start: s, end: t, count: e, loop: i, style: n }) {
    return {
        start: s % e,
        end: t % e,
        loop: i && (t - s + 1) % e === 0,
        style: n,
    };
}
function Wh(s, t, e) {
    const { property: i, start: n, end: o } = e;
    const { between: r, normalize: a } = Dr(i);
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
function Tn(s, t, e) {
    if (!e) return [s];
    const { property: i, start: n, end: o } = e;
    const r = t.length;
    const { compare: a, between: l, normalize: c } = Dr(i);
    const { start: h, end: u, loop: d, style: f } = Wh(s, t, e);
    const g = [];
    let m = !1;
    let p = null;
    let b;
    let y;
    let _;
    const w = () => l(n, _, b) && a(n, _) !== 0;
    const x = () => a(o, b) === 0 || l(o, _, b);
    const v = () => m || w();
    const S = () => !m || x();
    for (let M = h, T = h; M <= u; ++M) {
        ((y = t[M % r]),
            !y.skip &&
                ((b = c(y[i])),
                b !== _ &&
                    ((m = l(b, n, o)),
                    p === null && v() && (p = a(b, n) === 0 ? M : T),
                    p !== null &&
                        S() &&
                        (g.push(
                            Jo({
                                start: p,
                                end: M,
                                loop: d,
                                count: r,
                                style: f,
                            }),
                        ),
                        (p = null)),
                    (T = M),
                    (_ = b))));
    }
    return (
        p !== null &&
            g.push(Jo({ start: p, end: u, loop: d, count: r, style: f })),
        g
    );
}
function Dn(s, t) {
    const e = [];
    const i = s.segments;
    for (let n = 0; n < i.length; n++) {
        const o = Tn(i[n], s.points, t);
        o.length && e.push(...o);
    }
    return e;
}
function Bh(s, t, e, i) {
    let n = 0;
    let o = t - 1;
    if (e && !i) for (; n < t && !s[n].skip; ) n++;
    for (; n < t && s[n].skip; ) n++;
    for (n %= t, e && (o += n); o > n && s[o % t].skip; ) o--;
    return ((o %= t), { start: n, end: o });
}
function Hh(s, t, e, i) {
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
function Cr(s, t) {
    const e = s.points;
    const i = s.options.spanGaps;
    const n = e.length;
    if (!n) return [];
    const o = !!s._loop;
    const { start: r, end: a } = Bh(e, n, o, i);
    if (i === !0) return Qo(s, [{ start: r, end: a, loop: o }], e, t);
    const l = a < r ? a + n : a;
    const c = !!s._fullLoop && r === 0 && a === n - 1;
    return Qo(s, Hh(e, r, l, c), e, t);
}
function Qo(s, t, e, i) {
    return !i || !i.setContext || !e ? t : $h(s, t, e, i);
}
function $h(s, t, e, i) {
    const n = s._chart.getContext();
    const o = tr(s.options);
    const {
        _datasetIndex: r,
        options: { spanGaps: a },
    } = s;
    const l = e.length;
    const c = [];
    let h = o;
    let u = t[0].start;
    let d = u;
    function f(g, m, p, b) {
        const y = a ? -1 : 1;
        if (g !== m) {
            for (g += l; e[g % l].skip; ) g -= y;
            for (; e[m % l].skip; ) m += y;
            g % l !== m % l &&
                (c.push({ start: g % l, end: m % l, loop: p, style: b }),
                (h = b),
                (u = m % l));
        }
    }
    for (const g of t) {
        u = a ? u : g.start;
        let m = e[u % l];
        let p;
        for (d = u + 1; d <= g.end; d++) {
            const b = e[d % l];
            ((p = tr(
                i.setContext(
                    Bt(n, {
                        type: "segment",
                        p0: m,
                        p1: b,
                        p0DataIndex: (d - 1) % l,
                        p1DataIndex: d % l,
                        datasetIndex: r,
                    }),
                ),
            )),
                jh(p, h) && f(u, d - 1, g.loop, h),
                (m = b),
                (h = p));
        }
        u < d - 1 && f(u, d - 1, g.loop, h);
    }
    return c;
}
function tr(s) {
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
function jh(s, t) {
    if (!t) return !1;
    const e = [];
    const i = function (n, o) {
        return gn(o) ? (e.includes(o) || e.push(o), e.indexOf(o)) : o;
    };
    return JSON.stringify(s, i) !== JSON.stringify(t, i);
}
function Xs(s, t, e) {
    return s.options.clip ? s[e] : t[e];
}
function Uh(s, t) {
    const { xScale: e, yScale: i } = s;
    return e && i
        ? {
              left: Xs(e, t, "left"),
              right: Xs(e, t, "right"),
              top: Xs(i, t, "top"),
              bottom: Xs(i, t, "bottom"),
          }
        : t;
}
function Cn(s, t) {
    const e = t._clip;
    if (e.disabled) return !1;
    const i = Uh(t, s.chartArea);
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
const Bn = class {
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
            (this._request = hn.call(window, () => {
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
const Ht = new Bn();
const Pr = "transparent";
const Yh = {
    boolean(s, t, e) {
        return e > 0.5 ? t : s;
    },
    color(s, t, e) {
        const i = mn(s || Pr);
        const n = i.valid && mn(t || Pr);
        return n && n.valid ? n.mix(i, e).hexString() : t;
    },
    number(s, t, e) {
        return s + (t - s) * e;
    },
};
const Hn = class {
    constructor(t, e, i, n) {
        const o = e[i];
        n = Ne([t.to, n, o, t.from]);
        const r = Ne([t.from, o, n]);
        ((this._active = !0),
            (this._fn = t.fn || Yh[t.type || typeof r]),
            (this._easing = De[t.easing] || De.linear),
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
                (this._to = Ne([t.to, e, n, t.from])),
                (this._from = Ne([t.from, n, e])));
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
const _i = class {
    constructor(t, e) {
        ((this._chart = t), (this._properties = new Map()), this.configure(e));
    }

    configure(t) {
        if (!E(t)) return;
        const e = Object.keys(j.animation);
        const i = this._properties;
        Object.getOwnPropertyNames(t).forEach((n) => {
            const o = t[n];
            if (!E(o)) return;
            const r = {};
            for (const a of e) r[a] = o[a];
            ((H(o.properties) && o.properties) || [n]).forEach((a) => {
                (a === n || !i.has(a)) && i.set(a, r);
            });
        });
    }

    _animateOptions(t, e) {
        const i = e.options;
        const n = qh(t, i);
        if (!n) return [];
        const o = this._createAnimations(n, i);
        return (
            i.$shared &&
                Zh(t.options.$animations, i).then(
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
            ((o[c] = u = new Hn(d, t, c, h)), n.push(u));
        }
        return n;
    }

    update(t, e) {
        if (this._properties.size === 0) {
            Object.assign(t, e);
            return;
        }
        const i = this._createAnimations(t, e);
        if (i.length) return (Ht.add(this._chart, i), !0);
    }
};
function Zh(s, t) {
    const e = [];
    const i = Object.keys(t);
    for (let n = 0; n < i.length; n++) {
        const o = s[i[n]];
        o && o.active() && e.push(o.wait());
    }
    return Promise.all(e);
}
function qh(s, t) {
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
function Ir(s, t) {
    const e = (s && s.options) || {};
    const i = e.reverse;
    const n = e.min === void 0 ? t : 0;
    const o = e.max === void 0 ? t : 0;
    return { start: i ? o : n, end: i ? n : o };
}
function Gh(s, t, e) {
    if (e === !1) return !1;
    const i = Ir(s, e);
    const n = Ir(t, e);
    return { top: n.end, right: i.end, bottom: n.start, left: i.start };
}
function Xh(s) {
    let t, e, i, n;
    return (
        E(s)
            ? ((t = s.top), (e = s.right), (i = s.bottom), (n = s.left))
            : (t = e = i = n = s),
        { top: t, right: e, bottom: i, left: n, disabled: s === !1 }
    );
}
function Ca(s, t) {
    const e = [];
    const i = s._getSortedDatasetMetas(t);
    let n;
    let o;
    for (n = 0, o = i.length; n < o; ++n) e.push(i[n].index);
    return e;
}
function Ar(s, t, e, i = {}) {
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
            Z(c) && (o || t === 0 || St(t) === St(c)) && (t += c));
    }
    return !h && !i.all ? 0 : t;
}
function Kh(s, t) {
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
function Pn(s, t) {
    const e = s && s.options.stacked;
    return e || (e === void 0 && t.stack !== void 0);
}
function Jh(s, t, e) {
    return `${s.id}.${t.id}.${e.stack || e.type}`;
}
function Qh(s) {
    const { min: t, max: e, minDefined: i, maxDefined: n } = s.getUserBounds();
    return {
        min: i ? t : Number.NEGATIVE_INFINITY,
        max: n ? e : Number.POSITIVE_INFINITY,
    };
}
function tu(s, t, e) {
    const i = s[t] || (s[t] = {});
    return i[e] || (i[e] = {});
}
function Er(s, t, e, i) {
    for (const n of t.getMatchingVisibleMetas(i).reverse()) {
        const o = s[n.index];
        if ((e && o > 0) || (!e && o < 0)) return n.index;
    }
    return null;
}
function Lr(s, t) {
    const { chart: e, _cachedMeta: i } = s;
    const n = e._stacks || (e._stacks = {});
    const { iScale: o, vScale: r, index: a } = i;
    const l = o.axis;
    const c = r.axis;
    const h = Jh(o, r, i);
    const u = t.length;
    let d;
    for (let f = 0; f < u; ++f) {
        const g = t[f];
        const { [l]: m, [c]: p } = g;
        const b = g._stacks || (g._stacks = {});
        ((d = b[c] = tu(n, h, m)),
            (d[a] = p),
            (d._top = Er(d, r, !0, i.type)),
            (d._bottom = Er(d, r, !1, i.type)));
        const y = d._visualValues || (d._visualValues = {});
        y[a] = p;
    }
}
function In(s, t) {
    const e = s.scales;
    return Object.keys(e)
        .filter((i) => e[i].axis === t)
        .shift();
}
function eu(s, t) {
    return Bt(s, {
        active: !1,
        dataset: void 0,
        datasetIndex: t,
        index: t,
        mode: "default",
        type: "dataset",
    });
}
function su(s, t, e) {
    return Bt(s, {
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
function bs(s, t) {
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
const An = (s) => s === "reset" || s === "none";
const Fr = (s, t) => (t ? s : Object.assign({}, s));
const iu = (s, t, e) =>
    s && !t.hidden && t._stacked && { keys: Ca(e, !0), values: null };
const ut = class {
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
            (t._stacked = Pn(t.vScale, t)),
            this.addElements(),
            this.options.fill &&
                !this.chart.isPluginEnabled("filler") &&
                console.warn(
                    "Tried to use the 'fill' option without the 'Filler' plugin enabled. Please import and register the 'Filler' plugin and make sure it is not disabled in the options",
                ));
    }

    updateIndex(t) {
        (this.index !== t && bs(this._cachedMeta), (this.index = t));
    }

    linkScales() {
        const t = this.chart;
        const e = this._cachedMeta;
        const i = this.getDataset();
        const n = (u, d, f, g) => (u === "x" ? d : u === "r" ? g : f);
        const o = (e.xAxisID = P(i.xAxisID, In(t, "x")));
        const r = (e.yAxisID = P(i.yAxisID, In(t, "y")));
        const a = (e.rAxisID = P(i.rAxisID, In(t, "r")));
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
        (this._data && ln(this._data, this), t._stacked && bs(t));
    }

    _dataCheck() {
        const t = this.getDataset();
        const e = t.data || (t.data = []);
        const i = this._data;
        if (E(e)) {
            const n = this._cachedMeta;
            this._data = Kh(e, n);
        } else if (i !== e) {
            if (i) {
                ln(i, this);
                const n = this._cachedMeta;
                (bs(n), (n._parsed = []));
            }
            (e && Object.isExtensible(e) && ur(e, this),
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
        ((e._stacked = Pn(e.vScale, e)),
            e.stack !== i.stack && ((n = !0), bs(e), (e.stack = i.stack)),
            this._resyncElements(t),
            (n || o !== e._stacked) &&
                (Lr(this, e._parsed), (e._stacked = Pn(e.vScale, e))));
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
            H(n[t])
                ? (d = this.parseArrayData(i, n, t, e))
                : E(n[t])
                  ? (d = this.parseObjectData(i, n, t, e))
                  : (d = this.parsePrimitiveData(i, n, t, e));
            const f = () => u[a] === null || (c && u[a] < c[a]);
            for (h = 0; h < e; ++h) {
                ((i._parsed[h + t] = u = d[h]),
                    l && (f() && (l = !1), (c = u)));
            }
            i._sorted = l;
        }
        r && Lr(this, d);
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
        let g;
        for (d = 0, f = n; d < f; ++d) {
            ((g = d + i),
                (u[d] = { [a]: h || o.parse(c[g], g), [l]: r.parse(e[g], g) }));
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
                (c[h] = { x: o.parse(Wt(f, a), d), y: r.parse(Wt(f, l), d) }));
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
        const a = { keys: Ca(n, !0), values: e._stacks[t.axis]._visualValues };
        return Ar(a, r, o.index, { mode: i });
    }

    updateRangeFromParsed(t, e, i, n) {
        const o = i[e.axis];
        let r = o === null ? NaN : o;
        const a = n && i._stacks[e.axis];
        (n && a && ((n.values = a), (r = Ar(n, o, this._cachedMeta.index))),
            (t.min = Math.min(t.min, r)),
            (t.max = Math.max(t.max, r)));
    }

    getMinMax(t, e) {
        const i = this._cachedMeta;
        const n = i._parsed;
        const o = i._sorted && t === i.iScale;
        const r = n.length;
        const a = this._getOtherScale(t);
        const l = iu(e, i, this.chart);
        const c = {
            min: Number.POSITIVE_INFINITY,
            max: Number.NEGATIVE_INFINITY,
        };
        const { min: h, max: u } = Qh(a);
        let d;
        let f;
        function g() {
            f = n[d];
            const m = f[a.axis];
            return !Z(f[t.axis]) || h > m || u < m;
        }
        for (
            d = 0;
            d < r && !(!g() && (this.updateRangeFromParsed(c, t, f, l), o));
            ++d
        );
        if (o) {
            for (d = r - 1; d >= 0; --d) {
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
            (e._clip = Xh(
                P(
                    this.options.clip,
                    Gh(e.xScale, e.yScale, this.getMaxOverflow()),
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
            ((o = r.$context || (r.$context = su(this.getContext(), t, r))),
                (o.parsed = this.getParsed(t)),
                (o.raw = n.data[t]),
                (o.index = o.dataIndex = t));
        } else {
            ((o =
                this.$context ||
                (this.$context = eu(this.chart.getContext(), this.index))),
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
        const l = this.enableOptionSharing && Ae(i);
        if (a) return Fr(a, l);
        const c = this.chart.config;
        const h = c.datasetElementScopeKeys(this._type, t);
        const u = n ? [`${t}Hover`, "hover", t, ""] : [t, ""];
        const d = c.getOptionScopes(this.getDataset(), h);
        const f = Object.keys(j.elements[t]);
        const g = () => this.getContext(i, n, e);
        const m = c.resolveNamedOptions(d, f, g, u);
        return (
            m.$shared && ((m.$shared = l), (o[r] = Object.freeze(Fr(m, l)))),
            m
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
        const c = new _i(n, l && l.animations);
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
        return !e || An(t) || this.chart._animationsDisabled;
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
        An(n)
            ? Object.assign(t, i)
            : this._resolveAnimations(e, n).update(t, i);
    }

    updateSharedOptions(t, e, i) {
        t && !An(e) && this._resolveAnimations(void 0, e).update(t, i);
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
            i._stacked && bs(i, n);
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
(k(ut, "defaults", {}),
    k(ut, "datasetElementType", null),
    k(ut, "dataElementType", null));
function nu(s, t) {
    if (!s._cache.$bar) {
        const e = s.getMatchingVisibleMetas(t);
        let i = [];
        for (let n = 0, o = e.length; n < o; n++) {
            i = i.concat(e[n].controller.getAllParsedValues(s));
        }
        s._cache.$bar = cn(i.sort((n, o) => n - o));
    }
    return s._cache.$bar;
}
function ou(s) {
    const t = s.iScale;
    const e = nu(t, s.type);
    let i = t._length;
    let n;
    let o;
    let r;
    let a;
    const l = () => {
        r === 32767 ||
            r === -32768 ||
            (Ae(a) && (i = Math.min(i, Math.abs(r - a) || i)), (a = r));
    };
    for (n = 0, o = e.length; n < o; ++n) ((r = t.getPixelForValue(e[n])), l());
    for (a = void 0, n = 0, o = t.ticks.length; n < o; ++n) {
        ((r = t.getPixelForTick(n)), l());
    }
    return i;
}
function ru(s, t, e, i) {
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
function au(s, t, e, i) {
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
function lu(s, t, e, i) {
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
function Pa(s, t, e, i) {
    return (H(s) ? lu(s, t, e, i) : (t[e.axis] = e.parse(s, i)), t);
}
function Rr(s, t, e, i) {
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
            l.push(Pa(d, u, o, c)));
    }
    return l;
}
function En(s) {
    return s && s.barStart !== void 0 && s.barEnd !== void 0;
}
function cu(s, t, e) {
    return s !== 0
        ? St(s)
        : (t.isHorizontal() ? 1 : -1) * (t.min >= e ? 1 : -1);
}
function hu(s) {
    let t, e, i, n, o;
    return (
        s.horizontal
            ? ((t = s.base > s.x), (e = "left"), (i = "right"))
            : ((t = s.base < s.y), (e = "bottom"), (i = "top")),
        t ? ((n = "end"), (o = "start")) : ((n = "start"), (o = "end")),
        { start: e, end: i, reverse: t, top: n, bottom: o }
    );
}
function uu(s, t, e, i) {
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
    const { start: r, end: a, reverse: l, top: c, bottom: h } = hu(s);
    (n === "middle" &&
        e &&
        ((s.enableBorderRadius = !0),
        (e._top || 0) === i
            ? (n = c)
            : (e._bottom || 0) === i
              ? (n = h)
              : ((o[Nr(h, r, a, l)] = !0), (n = c))),
        (o[Nr(n, r, a, l)] = !0),
        (s.borderSkipped = o));
}
function Nr(s, t, e, i) {
    return (i ? ((s = du(s, t, e)), (s = zr(s, e, t))) : (s = zr(s, t, e)), s);
}
function du(s, t, e) {
    return s === t ? e : s === e ? t : s;
}
function zr(s, t, e) {
    return s === "start" ? t : s === "end" ? e : s;
}
function fu(s, { inflateAmount: t }, e) {
    s.inflateAmount = t === "auto" ? (e === 1 ? 0.33 : 0) : t;
}
const Ve = class extends ut {
    parsePrimitiveData(t, e, i, n) {
        return Rr(t, e, i, n);
    }

    parseArrayData(t, e, i, n) {
        return Rr(t, e, i, n);
    }

    parseObjectData(t, e, i, n) {
        const { iScale: o, vScale: r } = t;
        const { xAxisKey: a = "x", yAxisKey: l = "y" } = this._parsing;
        const c = o.axis === "x" ? a : l;
        const h = r.axis === "x" ? a : l;
        const u = [];
        let d;
        let f;
        let g;
        let m;
        for (d = i, f = i + n; d < f; ++d) {
            ((m = e[d]),
                (g = {}),
                (g[o.axis] = o.parse(Wt(m, c), d)),
                u.push(Pa(Wt(m, h), g, r, d)));
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
        const a = En(r)
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
            const g = this.getParsed(f);
            const m =
                o || A(g[a.axis])
                    ? { base: l, head: l }
                    : this._calculateBarValuePixels(f);
            const p = this._calculateBarIndexPixels(f, h);
            const b = (g._stacks || {})[a.axis];
            const y = {
                horizontal: c,
                base: m.base,
                enableBorderRadius:
                    !b || En(g._custom) || r === b._top || r === b._bottom,
                x: c ? m.head : p.center,
                y: c ? p.center : m.head,
                height: c ? p.size : Math.abs(m.size),
                width: c ? Math.abs(m.size) : p.size,
            };
            d &&
                (y.options =
                    u ||
                    this.resolveDataElementOptions(
                        f,
                        t[f].active ? "active" : n,
                    ));
            const _ = y.options || t[f].options;
            (uu(y, _, b, r),
                fu(y, _, h.ratio),
                this.updateElement(t[f], f, y, n));
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
                P(
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
            min: a || ou(e),
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
        const h = En(c);
        let u = l[e.axis];
        let d = 0;
        let f = i ? this.applyStack(e, l, i) : u;
        let g;
        let m;
        (f !== u && ((d = f - u), (f = u)),
            h &&
                ((u = c.barStart),
                (f = c.barEnd - c.barStart),
                u !== 0 && St(u) !== St(c.barEnd) && (d = 0),
                (d += u)));
        const p = !A(o) && !h ? o : d;
        let b = e.getPixelForValue(p);
        if (
            (this.chart.getDataVisibility(t)
                ? (g = e.getPixelForValue(d + f))
                : (g = b),
            (m = g - b),
            Math.abs(m) < r)
        ) {
            ((m = cu(m, e, a) * r), u === a && (b -= m / 2));
            const y = e.getPixelForDecimal(0);
            const _ = e.getPixelForDecimal(1);
            const w = Math.min(y, _);
            const x = Math.max(y, _);
            ((b = Math.max(Math.min(b, x), w)),
                (g = b + m),
                i &&
                    !h &&
                    (l._stacks[e.axis]._visualValues[n] =
                        e.getValueForPixel(g) - e.getValueForPixel(b)));
        }
        if (b === e.getPixelForValue(a)) {
            const y = (St(m) * e.getLineWidthForValue(a)) / 2;
            ((b += y), (m -= y));
        }
        return { size: m, base: b, head: g, center: g + m / 2 };
    }

    _calculateBarIndexPixels(t, e) {
        const i = e.scale;
        const n = this.options;
        const o = n.skipNull;
        const r = P(n.maxBarThickness, 1 / 0);
        let a;
        let l;
        const c = this._getAxisCount();
        if (e.grouped) {
            const h = o ? this._getStackCount(t) : e.stackCount;
            const u =
                n.barThickness === "flex"
                    ? au(t, e, n, h * c)
                    : ru(t, e, n, h * c);
            const d =
                this.chart.options.indexAxis === "x"
                    ? this.getDataset().xAxisID
                    : this.getDataset().yAxisID;
            const f = this._getAxis().indexOf(
                P(d, this.getFirstScaleIdForIndexAxis()),
            );
            const g =
                this._getStackIndex(
                    this.index,
                    this._cachedMeta.stack,
                    o ? t : void 0,
                ) + f;
            ((a = u.start + u.chunk * g + u.chunk / 2),
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
(k(Ve, "id", "bar"),
    k(Ve, "defaults", {
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
    k(Ve, "overrides", {
        scales: {
            _index_: { type: "category", offset: !0, grid: { offset: !0 } },
            _value_: { type: "linear", beginAtZero: !0 },
        },
    }));
const We = class extends ut {
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
            o[r]._custom = P(
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
            o[r]._custom = P(
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
            const g = !o && this.getParsed(d);
            const m = {};
            const p = (m[h] = o
                ? r.getPixelForDecimal(0.5)
                : r.getPixelForValue(g[h]));
            const b = (m[u] = o ? a.getBasePixel() : a.getPixelForValue(g[u]));
            ((m.skip = isNaN(p) || isNaN(b)),
                c &&
                    ((m.options =
                        l ||
                        this.resolveDataElementOptions(
                            d,
                            f.active ? "active" : n,
                        )),
                    o && (m.options.radius = 0)),
                this.updateElement(f, d, m, n));
        }
    }

    resolveDataElementOptions(t, e) {
        const i = this.getParsed(t);
        let n = super.resolveDataElementOptions(t, e);
        n.$shared && (n = Object.assign({}, n, { $shared: !1 }));
        const o = n.radius;
        return (
            e !== "active" && (n.radius = 0),
            (n.radius += P(i && i._custom, o)),
            n
        );
    }
};
(k(We, "id", "bubble"),
    k(We, "defaults", {
        datasetElementType: !1,
        dataElementType: "point",
        animations: {
            numbers: {
                type: "number",
                properties: ["x", "y", "borderWidth", "radius"],
            },
        },
    }),
    k(We, "overrides", {
        scales: { x: { type: "linear" }, y: { type: "linear" } },
    }));
function gu(s, t, e) {
    let i = 1;
    let n = 1;
    let o = 0;
    let r = 0;
    if (t < $) {
        const a = s;
        const l = a + t;
        const c = Math.cos(a);
        const h = Math.sin(a);
        const u = Math.cos(l);
        const d = Math.sin(l);
        const f = (_, w, x) =>
            Le(_, a, l, !0) ? 1 : Math.max(w, w * e, x, x * e);
        const g = (_, w, x) =>
            Le(_, a, l, !0) ? -1 : Math.min(w, w * e, x, x * e);
        const m = f(0, c, u);
        const p = f(q, h, d);
        const b = g(F, c, u);
        const y = g(F + q, h, d);
        ((i = (m - b) / 2),
            (n = (p - y) / 2),
            (o = -(m + b) / 2),
            (r = -(p + y) / 2));
    }
    return { ratioX: i, ratioY: n, offsetX: o, offsetY: r };
}
const jt = class extends ut {
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
            if (E(i[t])) {
                const { key: l = "value" } = this._parsing;
                o = (c) => +Wt(i[c], l);
            }
            let r, a;
            for (r = t, a = t + e; r < a; ++r) n._parsed[r] = o(r);
        }
    }

    _getRotation() {
        return bt(this.options.rotation - 90);
    }

    _getCircumference() {
        return bt(this.options.circumference);
    }

    _getRotationExtents() {
        let t = $;
        let e = -$;
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
        const l = Math.min(sr(this.options.cutout, a), 1);
        const c = this._getRingWeight(this.index);
        const { circumference: h, rotation: u } = this._getRotationExtents();
        const { ratioX: d, ratioY: f, offsetX: g, offsetY: m } = gu(u, h, l);
        const p = (i.width - r) / d;
        const b = (i.height - r) / f;
        const y = Math.max(Math.min(p, b) / 2, 0);
        const _ = en(this.options.radius, y);
        const w = Math.max(_ * l, 0);
        const x = (_ - w) / this._getVisibleDatasetWeightTotal();
        ((this.offsetX = g * _),
            (this.offsetY = m * _),
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
            : this.calculateCircumference((n._parsed[t] * o) / $);
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
        const g = d ? 0 : this.outerRadius;
        const { sharedOptions: m, includeOptions: p } = this._getSharedOptions(
            e,
            n,
        );
        let b = this._getRotation();
        let y;
        for (y = 0; y < e; ++y) b += this._circumference(y, o);
        for (y = e; y < e + i; ++y) {
            const _ = this._circumference(y, o);
            const w = t[y];
            const x = {
                x: h + this.offsetX,
                y: u + this.offsetY,
                startAngle: b,
                endAngle: b + _,
                circumference: _,
                outerRadius: g,
                innerRadius: f,
            };
            (p &&
                (x.options =
                    m ||
                    this.resolveDataElementOptions(y, w.active ? "active" : n)),
                (b += _),
                this.updateElement(w, y, x, n));
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
        return e > 0 && !isNaN(t) ? $ * (Math.abs(t) / e) : 0;
    }

    getLabelAndValue(t) {
        const e = this._cachedMeta;
        const i = this.chart;
        const n = i.data.labels || [];
        const o = Fe(e._parsed[t], i.options.locale);
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
        return Math.max(P(this.chart.data.datasets[t].weight, 1), 0);
    }

    _getVisibleDatasetWeightTotal() {
        return this._getRingWeightOffset(this.chart.data.datasets.length) || 1;
    }
};
(k(jt, "id", "doughnut"),
    k(jt, "defaults", {
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
    k(jt, "descriptors", {
        _scriptable: (t) => t !== "spacing",
        _indexable: (t) =>
            t !== "spacing" &&
            !t.startsWith("borderDash") &&
            !t.startsWith("hoverBorderDash"),
    }),
    k(jt, "overrides", {
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
const Be = class extends ut {
    initialize() {
        ((this.enableOptionSharing = !0),
            (this.supportsDecimation = !0),
            super.initialize());
    }

    update(t) {
        const e = this._cachedMeta;
        const { dataset: i, data: n = [], _dataset: o } = e;
        const r = this.chart._animationsDisabled;
        let { start: a, count: l } = dn(e, n, r);
        ((this._drawStart = a),
            (this._drawCount = l),
            fn(e) && ((a = 0), (l = n.length)),
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
        const { spanGaps: g, segment: m } = this.options;
        const p = de(g) ? g : Number.POSITIVE_INFINITY;
        const b = this.chart._animationsDisabled || o || n === "none";
        const y = e + i;
        const _ = t.length;
        let w = e > 0 && this.getParsed(e - 1);
        for (let x = 0; x < _; ++x) {
            const v = t[x];
            const S = b ? v : {};
            if (x < e || x >= y) {
                S.skip = !0;
                continue;
            }
            const M = this.getParsed(x);
            const T = A(M[f]);
            const C = (S[d] = r.getPixelForValue(M[d], x));
            const I = (S[f] =
                o || T
                    ? a.getBasePixel()
                    : a.getPixelForValue(
                          l ? this.applyStack(a, M, l) : M[f],
                          x,
                      ));
            ((S.skip = isNaN(C) || isNaN(I) || T),
                (S.stop = x > 0 && Math.abs(M[d] - w[d]) > p),
                m && ((S.parsed = M), (S.raw = c.data[x])),
                u &&
                    (S.options =
                        h ||
                        this.resolveDataElementOptions(
                            x,
                            v.active ? "active" : n,
                        )),
                b || this.updateElement(v, x, S, n),
                (w = M));
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
(k(Be, "id", "line"),
    k(Be, "defaults", {
        datasetElementType: "line",
        dataElementType: "point",
        showLine: !0,
        spanGaps: !1,
    }),
    k(Be, "overrides", {
        scales: { _index_: { type: "category" }, _value_: { type: "linear" } },
    }));
const ye = class extends ut {
    constructor(t, e) {
        (super(t, e), (this.innerRadius = void 0), (this.outerRadius = void 0));
    }

    getLabelAndValue(t) {
        const e = this._cachedMeta;
        const i = this.chart;
        const n = i.data.labels || [];
        const o = Fe(e._parsed[t].r, i.options.locale);
        return { label: n[t] || "", value: o };
    }

    parseObjectData(t, e, i, n) {
        return kn.bind(this)(t, e, i, n);
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
        const d = c.getIndexAngle(0) - 0.5 * F;
        let f = d;
        let g;
        const m = 360 / this.countVisibleElements();
        for (g = 0; g < e; ++g) f += this._computeAngle(g, n, m);
        for (g = e; g < e + i; g++) {
            const p = t[g];
            let b = f;
            let y = f + this._computeAngle(g, n, m);
            let _ = r.getDataVisibility(g)
                ? c.getDistanceFromCenterForValue(this.getParsed(g).r)
                : 0;
            ((f = y),
                o &&
                    (l.animateScale && (_ = 0),
                    l.animateRotate && (b = y = d)));
            const w = {
                x: h,
                y: u,
                innerRadius: 0,
                outerRadius: _,
                startAngle: b,
                endAngle: y,
                options: this.resolveDataElementOptions(
                    g,
                    p.active ? "active" : n,
                ),
            };
            this.updateElement(p, g, w, n);
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
            ? bt(this.resolveDataElementOptions(t, e).angle || i)
            : 0;
    }
};
(k(ye, "id", "polarArea"),
    k(ye, "defaults", {
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
    k(ye, "overrides", {
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
const ks = class extends jt {};
(k(ks, "id", "pie"),
    k(ks, "defaults", {
        cutout: 0,
        rotation: 0,
        circumference: 360,
        radius: "100%",
    }));
const He = class extends ut {
    getLabelAndValue(t) {
        const e = this._cachedMeta.vScale;
        const i = this.getParsed(t);
        return {
            label: e.getLabels()[t],
            value: "" + e.getLabelForValue(i[e.axis]),
        };
    }

    parseObjectData(t, e, i, n) {
        return kn.bind(this)(t, e, i, n);
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
(k(He, "id", "radar"),
    k(He, "defaults", {
        datasetElementType: "line",
        dataElementType: "point",
        indexAxis: "r",
        showLine: !0,
        elements: { line: { fill: "start" } },
    }),
    k(He, "overrides", {
        aspectRatio: 1,
        scales: { r: { type: "radialLinear" } },
    }));
const $e = class extends ut {
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
        let { start: o, count: r } = dn(e, i, n);
        if (
            ((this._drawStart = o),
            (this._drawCount = r),
            fn(e) && ((o = 0), (r = i.length)),
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
        const g = a.axis;
        const { spanGaps: m, segment: p } = this.options;
        const b = de(m) ? m : Number.POSITIVE_INFINITY;
        const y = this.chart._animationsDisabled || o || n === "none";
        let _ = e > 0 && this.getParsed(e - 1);
        for (let w = e; w < e + i; ++w) {
            const x = t[w];
            const v = this.getParsed(w);
            const S = y ? x : {};
            const M = A(v[g]);
            const T = (S[f] = r.getPixelForValue(v[f], w));
            const C = (S[g] =
                o || M
                    ? a.getBasePixel()
                    : a.getPixelForValue(
                          l ? this.applyStack(a, v, l) : v[g],
                          w,
                      ));
            ((S.skip = isNaN(T) || isNaN(C) || M),
                (S.stop = w > 0 && Math.abs(v[f] - _[f]) > b),
                p && ((S.parsed = v), (S.raw = c.data[w])),
                d &&
                    (S.options =
                        u ||
                        this.resolveDataElementOptions(
                            w,
                            x.active ? "active" : n,
                        )),
                y || this.updateElement(x, w, S, n),
                (_ = v));
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
(k($e, "id", "scatter"),
    k($e, "defaults", {
        datasetElementType: !1,
        dataElementType: "point",
        showLine: !1,
        fill: !1,
    }),
    k($e, "overrides", {
        interaction: { mode: "point" },
        scales: { x: { type: "linear" }, y: { type: "linear" } },
    }));
const mu = Object.freeze({
    __proto__: null,
    BarController: Ve,
    BubbleController: We,
    DoughnutController: jt,
    LineController: Be,
    PieController: ks,
    PolarAreaController: ye,
    RadarController: He,
    ScatterController: $e,
});
function ge() {
    throw new Error(
        "This method is not implemented: Check that a complete date adapter is provided.",
    );
}
const $n = class s {
    constructor(t) {
        k(this, "options");
        this.options = t || {};
    }

    static override(t) {
        Object.assign(s.prototype, t);
    }

    init() {}
    formats() {
        return ge();
    }

    parse() {
        return ge();
    }

    format() {
        return ge();
    }

    add() {
        return ge();
    }

    diff() {
        return ge();
    }

    startOf() {
        return ge();
    }

    endOf() {
        return ge();
    }
};
const eo = { _date: $n };
function pu(s, t, e, i) {
    const { controller: n, data: o, _sorted: r } = s;
    const a = n._cachedMeta.iScale;
    const l =
        s.dataset && s.dataset.options ? s.dataset.options.spanGaps : null;
    if (a && t === a.axis && t !== "r" && r && o.length) {
        const c = a._reversePixels ? lr : Dt;
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
                    .findIndex((m) => !A(m[u.axis]));
                h.lo -= Math.max(0, f);
                const g = d.slice(h.hi).findIndex((m) => !A(m[u.axis]));
                h.hi += Math.max(0, g);
            }
            return h;
        }
    }
    return { lo: 0, hi: o.length - 1 };
}
function Es(s, t, e, i, n) {
    const o = s.getSortedVisibleDatasetMetas();
    const r = e[t];
    for (let a = 0, l = o.length; a < l; ++a) {
        const { index: c, data: h } = o[a];
        const { lo: u, hi: d } = pu(o[a], t, r, n);
        for (let f = u; f <= d; ++f) {
            const g = h[f];
            g.skip || i(g, c, f);
        }
    }
}
function bu(s) {
    const t = s.indexOf("x") !== -1;
    const e = s.indexOf("y") !== -1;
    return function (i, n) {
        const o = t ? Math.abs(i.x - n.x) : 0;
        const r = e ? Math.abs(i.y - n.y) : 0;
        return Math.sqrt(Math.pow(o, 2) + Math.pow(r, 2));
    };
}
function Ln(s, t, e, i, n) {
    const o = [];
    return (
        (!n && !s.isPointInArea(t)) ||
            Es(
                s,
                e,
                t,
                function (a, l, c) {
                    (!n && !Ct(a, s.chartArea, 0)) ||
                        (a.inRange(t.x, t.y, i) &&
                            o.push({ element: a, datasetIndex: l, index: c }));
                },
                !0,
            ),
        o
    );
}
function yu(s, t, e, i) {
    const n = [];
    function o(r, a, l) {
        const { startAngle: c, endAngle: h } = r.getProps(
            ["startAngle", "endAngle"],
            i,
        );
        const { angle: u } = an(r, { x: t.x, y: t.y });
        Le(u, c, h) && n.push({ element: r, datasetIndex: a, index: l });
    }
    return (Es(s, e, t, o), n);
}
function xu(s, t, e, i, n, o) {
    let r = [];
    const a = bu(e);
    let l = Number.POSITIVE_INFINITY;
    function c(h, u, d) {
        const f = h.inRange(t.x, t.y, n);
        if (i && !f) return;
        const g = h.getCenterPoint(n);
        if (!(!!o || s.isPointInArea(g)) && !f) return;
        const p = a(t, g);
        p < l
            ? ((r = [{ element: h, datasetIndex: u, index: d }]), (l = p))
            : p === l && r.push({ element: h, datasetIndex: u, index: d });
    }
    return (Es(s, e, t, c), r);
}
function Fn(s, t, e, i, n, o) {
    return !o && !s.isPointInArea(t)
        ? []
        : e === "r" && !i
          ? yu(s, t, e, n)
          : xu(s, t, e, i, n, o);
}
function Vr(s, t, e, i, n) {
    const o = [];
    const r = e === "x" ? "inXRange" : "inYRange";
    let a = !1;
    return (
        Es(s, e, t, (l, c, h) => {
            l[r] &&
                l[r](t[e], n) &&
                (o.push({ element: l, datasetIndex: c, index: h }),
                (a = a || l.inRange(t.x, t.y, n)));
        }),
        i && !a ? [] : o
    );
}
const _u = {
    evaluateInteractionItems: Es,
    modes: {
        index(s, t, e, i) {
            const n = te(t, s);
            const o = e.axis || "x";
            const r = e.includeInvisible || !1;
            const a = e.intersect ? Ln(s, n, o, i, r) : Fn(s, n, o, !1, i, r);
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
            const n = te(t, s);
            const o = e.axis || "xy";
            const r = e.includeInvisible || !1;
            let a = e.intersect ? Ln(s, n, o, i, r) : Fn(s, n, o, !1, i, r);
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
            const n = te(t, s);
            const o = e.axis || "xy";
            const r = e.includeInvisible || !1;
            return Ln(s, n, o, i, r);
        },
        nearest(s, t, e, i) {
            const n = te(t, s);
            const o = e.axis || "xy";
            const r = e.includeInvisible || !1;
            return Fn(s, n, o, e.intersect, i, r);
        },
        x(s, t, e, i) {
            const n = te(t, s);
            return Vr(s, n, "x", e.intersect, i);
        },
        y(s, t, e, i) {
            const n = te(t, s);
            return Vr(s, n, "y", e.intersect, i);
        },
    },
};
const Ia = ["left", "top", "right", "bottom"];
function ys(s, t) {
    return s.filter((e) => e.pos === t);
}
function Wr(s, t) {
    return s.filter((e) => Ia.indexOf(e.pos) === -1 && e.box.axis === t);
}
function xs(s, t) {
    return s.sort((e, i) => {
        const n = t ? i : e;
        const o = t ? e : i;
        return n.weight === o.weight ? n.index - o.index : n.weight - o.weight;
    });
}
function wu(s) {
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
function ku(s) {
    const t = {};
    for (const e of s) {
        const { stack: i, pos: n, stackWeight: o } = e;
        if (!i || !Ia.includes(n)) continue;
        const r = t[i] || (t[i] = { count: 0, placed: 0, weight: 0, size: 0 });
        (r.count++, (r.weight += o));
    }
    return t;
}
function vu(s, t) {
    const e = ku(s);
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
function Su(s) {
    const t = wu(s);
    const e = xs(
        t.filter((c) => c.box.fullSize),
        !0,
    );
    const i = xs(ys(t, "left"), !0);
    const n = xs(ys(t, "right"));
    const o = xs(ys(t, "top"), !0);
    const r = xs(ys(t, "bottom"));
    const a = Wr(t, "x");
    const l = Wr(t, "y");
    return {
        fullSize: e,
        leftAndTop: i.concat(o),
        rightAndBottom: n.concat(l).concat(r).concat(a),
        chartArea: ys(t, "chartArea"),
        vertical: i.concat(n).concat(l),
        horizontal: o.concat(r).concat(a),
    };
}
function Br(s, t, e, i) {
    return Math.max(s[e], t[e]) + Math.max(s[i], t[i]);
}
function Aa(s, t) {
    ((s.top = Math.max(s.top, t.top)),
        (s.left = Math.max(s.left, t.left)),
        (s.bottom = Math.max(s.bottom, t.bottom)),
        (s.right = Math.max(s.right, t.right)));
}
function Mu(s, t, e, i) {
    const { pos: n, box: o } = e;
    const r = s.maxPadding;
    if (!E(n)) {
        e.size && (s[n] -= e.size);
        const u = i[e.stack] || { size: 0, count: 1 };
        ((u.size = Math.max(u.size, e.horizontal ? o.height : o.width)),
            (e.size = u.size / u.count),
            (s[n] += e.size));
    }
    o.getPadding && Aa(r, o.getPadding());
    const a = Math.max(0, t.outerWidth - Br(r, s, "left", "right"));
    const l = Math.max(0, t.outerHeight - Br(r, s, "top", "bottom"));
    const c = a !== s.w;
    const h = l !== s.h;
    return (
        (s.w = a),
        (s.h = l),
        e.horizontal ? { same: c, other: h } : { same: h, other: c }
    );
}
function Ou(s) {
    const t = s.maxPadding;
    function e(i) {
        const n = Math.max(t[i] - s[i], 0);
        return ((s[i] += n), n);
    }
    ((s.y += e("top")), (s.x += e("left")), e("right"), e("bottom"));
}
function Tu(s, t) {
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
function vs(s, t, e, i) {
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
            l.update(a.width || t.w, a.height || t.h, Tu(a.horizontal, t)));
        const { same: u, other: d } = Mu(t, e, a, i);
        ((c |= u && n.length), (h = h || d), l.fullSize || n.push(a));
    }
    return (c && vs(n, t, e, i)) || h;
}
function di(s, t, e, i, n) {
    ((s.top = e),
        (s.left = t),
        (s.right = t + i),
        (s.bottom = e + n),
        (s.width = i),
        (s.height = n));
}
function Hr(s, t, e, i) {
    const n = e.padding;
    let { x: o, y: r } = t;
    for (const a of s) {
        const l = a.box;
        const c = i[a.stack] || { count: 1, placed: 0, weight: 1 };
        const h = a.stackWeight / c.weight || 1;
        if (a.horizontal) {
            const u = t.w * h;
            const d = c.size || l.height;
            (Ae(c.start) && (r = c.start),
                l.fullSize
                    ? di(l, n.left, r, e.outerWidth - n.right - n.left, d)
                    : di(l, t.left + c.placed, r, u, d),
                (c.start = r),
                (c.placed += u),
                (r = l.bottom));
        } else {
            const u = t.h * h;
            const d = c.size || l.width;
            (Ae(c.start) && (o = c.start),
                l.fullSize
                    ? di(l, o, n.top, d, e.outerHeight - n.bottom - n.top)
                    : di(l, o, t.top + c.placed, d, u),
                (c.start = o),
                (c.placed += u),
                (o = l.right));
        }
    }
    ((t.x = o), (t.y = r));
}
const rt = {
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
        const n = nt(s.options.layout.padding);
        const o = Math.max(t - n.width, 0);
        const r = Math.max(e - n.height, 0);
        const a = Su(s.boxes);
        const l = a.vertical;
        const c = a.horizontal;
        z(s.boxes, (m) => {
            typeof m.beforeLayout === "function" && m.beforeLayout();
        });
        const h =
            l.reduce(
                (m, p) =>
                    p.box.options && p.box.options.display === !1 ? m : m + 1,
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
        Aa(d, nt(i));
        const f = Object.assign(
            { maxPadding: d, w: o, h: r, x: n.left, y: n.top },
            n,
        );
        const g = vu(l.concat(c), u);
        (vs(a.fullSize, f, u, g),
            vs(l, f, u, g),
            vs(c, f, u, g) && vs(l, f, u, g),
            Ou(f),
            Hr(a.leftAndTop, f, u, g),
            (f.x += f.w),
            (f.y += f.h),
            Hr(a.rightAndBottom, f, u, g),
            (s.chartArea = {
                left: f.left,
                top: f.top,
                right: f.left + f.w,
                bottom: f.top + f.h,
                height: f.h,
                width: f.w,
            }),
            z(a.chartArea, (m) => {
                const p = m.box;
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
const wi = class {
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
const jn = class extends wi {
    acquireContext(t) {
        return (t && t.getContext && t.getContext("2d")) || null;
    }

    updateConfig(t) {
        t.options.animation = !1;
    }
};
const yi = "$chartjs";
const Du = {
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
const $r = (s) => s === null || s === "";
function Cu(s, t) {
    const e = s.style;
    const i = s.getAttribute("height");
    const n = s.getAttribute("width");
    if (
        ((s[yi] = {
            initial: {
                height: i,
                width: n,
                style: { display: e.display, height: e.height, width: e.width },
            },
        }),
        (e.display = e.display || "block"),
        (e.boxSizing = e.boxSizing || "border-box"),
        $r(n))
    ) {
        const o = Sn(s, "width");
        o !== void 0 && (s.width = o);
    }
    if ($r(i)) {
        if (s.style.height === "") s.height = s.width / (t || 2);
        else {
            const o = Sn(s, "height");
            o !== void 0 && (s.height = o);
        }
    }
    return s;
}
const Ea = Mr ? { passive: !0 } : !1;
function Pu(s, t, e) {
    s && s.addEventListener(t, e, Ea);
}
function Iu(s, t, e) {
    s && s.canvas && s.canvas.removeEventListener(t, e, Ea);
}
function Au(s, t) {
    const e = Du[s.type] || s.type;
    const { x: i, y: n } = te(s, t);
    return {
        type: e,
        chart: t,
        native: s,
        x: i !== void 0 ? i : null,
        y: n !== void 0 ? n : null,
    };
}
function ki(s, t) {
    for (const e of s) if (e === t || e.contains(t)) return !0;
}
function Eu(s, t, e) {
    const i = s.canvas;
    const n = new MutationObserver((o) => {
        let r = !1;
        for (const a of o) {
            ((r = r || ki(a.addedNodes, i)), (r = r && !ki(a.removedNodes, i)));
        }
        r && e();
    });
    return (n.observe(document, { childList: !0, subtree: !0 }), n);
}
function Lu(s, t, e) {
    const i = s.canvas;
    const n = new MutationObserver((o) => {
        let r = !1;
        for (const a of o) {
            ((r = r || ki(a.removedNodes, i)), (r = r && !ki(a.addedNodes, i)));
        }
        r && e();
    });
    return (n.observe(document, { childList: !0, subtree: !0 }), n);
}
const Ps = new Map();
let jr = 0;
function La() {
    const s = window.devicePixelRatio;
    s !== jr &&
        ((jr = s),
        Ps.forEach((t, e) => {
            e.currentDevicePixelRatio !== s && t();
        }));
}
function Fu(s, t) {
    (Ps.size || window.addEventListener("resize", La), Ps.set(s, t));
}
function Ru(s) {
    (Ps.delete(s), Ps.size || window.removeEventListener("resize", La));
}
function Nu(s, t, e) {
    const i = s.canvas;
    const n = i && hi(i);
    if (!n) return;
    const o = un((a, l) => {
        const c = n.clientWidth;
        (e(a, l), c < n.clientWidth && e());
    }, window);
    const r = new ResizeObserver((a) => {
        const l = a[0];
        const c = l.contentRect.width;
        const h = l.contentRect.height;
        (c === 0 && h === 0) || o(c, h);
    });
    return (r.observe(n), Fu(s, o), r);
}
function Rn(s, t, e) {
    (e && e.disconnect(), t === "resize" && Ru(s));
}
function zu(s, t, e) {
    const i = s.canvas;
    const n = un((o) => {
        s.ctx !== null && e(Au(o, s));
    }, s);
    return (Pu(i, t, n), n);
}
const Un = class extends wi {
    acquireContext(t, e) {
        const i = t && t.getContext && t.getContext("2d");
        return i && i.canvas === t ? (Cu(t, e), i) : null;
    }

    releaseContext(t) {
        const e = t.canvas;
        if (!e[yi]) return !1;
        const i = e[yi].initial;
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
            delete e[yi],
            !0
        );
    }

    addEventListener(t, e, i) {
        this.removeEventListener(t, e);
        const n = t.$proxies || (t.$proxies = {});
        const r = { attach: Eu, detach: Lu, resize: Nu }[e] || zu;
        n[e] = r(t, e, i);
    }

    removeEventListener(t, e) {
        const i = t.$proxies || (t.$proxies = {});
        const n = i[e];
        if (!n) return;
        ((({ attach: Rn, detach: Rn, resize: Rn })[e] || Iu)(t, e, n),
            (i[e] = void 0));
    }

    getDevicePixelRatio() {
        return window.devicePixelRatio;
    }

    getMaximumSize(t, e, i, n) {
        return Sr(t, e, i, n);
    }

    isAttached(t) {
        const e = t && hi(t);
        return !!(e && e.isConnected);
    }
};
function Vu(s) {
    return !ci() ||
        (typeof OffscreenCanvas < "u" && s instanceof OffscreenCanvas)
        ? jn
        : Un;
}
const dt = class {
    constructor() {
        k(this, "x");
        k(this, "y");
        k(this, "active", !1);
        k(this, "options");
        k(this, "$animations");
    }

    tooltipPosition(t) {
        const { x: e, y: i } = this.getProps(["x", "y"], t);
        return { x: e, y: i };
    }

    hasValue() {
        return de(this.x) && de(this.y);
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
(k(dt, "defaults", {}), k(dt, "defaultRoutes"));
function Wu(s, t) {
    const e = s.options.ticks;
    const i = Bu(s);
    const n = Math.min(e.maxTicksLimit || i, i);
    const o = e.major.enabled ? $u(t) : [];
    const r = o.length;
    const a = o[0];
    const l = o[r - 1];
    const c = [];
    if (r > n) return (ju(t, c, o, r / n), c);
    const h = Hu(o, t, n);
    if (r > 0) {
        let u;
        let d;
        const f = r > 1 ? Math.round((l - a) / (r - 1)) : null;
        for (fi(t, c, h, A(f) ? 0 : a - f, a), u = 0, d = r - 1; u < d; u++) {
            fi(t, c, h, o[u], o[u + 1]);
        }
        return (fi(t, c, h, l, A(f) ? t.length : l + f), c);
    }
    return (fi(t, c, h), c);
}
function Bu(s) {
    const t = s.options.offset;
    const e = s._tickSize();
    const i = s._length / e + (t ? 0 : 1);
    const n = s._maxLength / e;
    return Math.floor(Math.min(i, n));
}
function Hu(s, t, e) {
    const i = Uu(s);
    const n = t.length / e;
    if (!i) return Math.max(n, 1);
    const o = or(i);
    for (let r = 0, a = o.length - 1; r < a; r++) {
        const l = o[r];
        if (l > n) return l;
    }
    return Math.max(n, 1);
}
function $u(s) {
    const t = [];
    let e;
    let i;
    for (e = 0, i = s.length; e < i; e++) s[e].major && t.push(e);
    return t;
}
function ju(s, t, e, i) {
    let n = 0;
    let o = e[0];
    let r;
    for (i = Math.ceil(i), r = 0; r < s.length; r++) {
        r === o && (t.push(s[r]), n++, (o = e[n * i]));
    }
}
function fi(s, t, e, i, n) {
    const o = P(i, 0);
    const r = Math.min(P(n, s.length), s.length);
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
function Uu(s) {
    const t = s.length;
    let e;
    let i;
    if (t < 2) return !1;
    for (i = s[0], e = 1; e < t; ++e) if (s[e] - s[e - 1] !== i) return !1;
    return i;
}
const Yu = (s) => (s === "left" ? "right" : s === "right" ? "left" : s);
const Ur = (s, t, e) => (t === "top" || t === "left" ? s[t] + e : s[t] - e);
const Yr = (s, t) => Math.min(t || s, s);
function Zr(s, t) {
    const e = [];
    const i = s.length / t;
    const n = s.length;
    let o = 0;
    for (; o < n; o += i) e.push(s[Math.floor(o)]);
    return e;
}
function Zu(s, t, e) {
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
function qu(s, t) {
    z(s, (e) => {
        const i = e.gc;
        const n = i.length / 2;
        let o;
        if (n > t) {
            for (o = 0; o < n; ++o) delete e.data[i[o]];
            i.splice(0, n);
        }
    });
}
function _s(s) {
    return s.drawTicks ? s.tickLength : 0;
}
function qr(s, t) {
    if (!s.display) return 0;
    const e = X(s.font, t);
    const i = nt(s.padding);
    return (H(s.text) ? s.text.length : 1) * e.lineHeight + i.height;
}
function Gu(s, t) {
    return Bt(s, { scale: t, type: "scale" });
}
function Xu(s, t, e) {
    return Bt(s, { tick: e, index: t, type: "tick" });
}
function Ku(s, t, e) {
    let i = ni(s);
    return (((e && t !== "right") || (!e && t === "right")) && (i = Yu(i)), i);
}
function Ju(s, t, e, i) {
    const { top: n, left: o, bottom: r, right: a, chart: l } = s;
    const { chartArea: c, scales: h } = l;
    let u = 0;
    let d;
    let f;
    let g;
    const m = r - n;
    const p = a - o;
    if (s.isHorizontal()) {
        if (((f = it(i, o, a)), E(e))) {
            const b = Object.keys(e)[0];
            const y = e[b];
            g = h[b].getPixelForValue(y) + m - t;
        } else {
            e === "center"
                ? (g = (c.bottom + c.top) / 2 + m - t)
                : (g = Ur(s, e, t));
        }
        d = a - o;
    } else {
        if (E(e)) {
            const b = Object.keys(e)[0];
            const y = e[b];
            f = h[b].getPixelForValue(y) - p + t;
        } else {
            e === "center"
                ? (f = (c.left + c.right) / 2 - p + t)
                : (f = Ur(s, e, t));
        }
        ((g = it(i, r, n)), (u = e === "left" ? -q : q));
    }
    return { titleX: f, titleY: g, maxWidth: d, rotation: u };
}
const _e = class s extends dt {
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
            (t = at(t, Number.POSITIVE_INFINITY)),
            (e = at(e, Number.NEGATIVE_INFINITY)),
            (i = at(i, Number.POSITIVE_INFINITY)),
            (n = at(n, Number.NEGATIVE_INFINITY)),
            { min: at(t, i), max: at(e, n), minDefined: Z(t), maxDefined: Z(e) }
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
            { min: at(e, at(i, e)), max: at(i, at(e, i)) }
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
        W(this.options.beforeUpdate, [this]);
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
                (this._range = yr(this, o, n)),
                (this._dataLimitsCached = !0)),
            this.beforeBuildTicks(),
            (this.ticks = this.buildTicks() || []),
            this.afterBuildTicks());
        const l = a < this.ticks.length;
        (this._convertTicksToLabels(l ? Zr(this.ticks, a) : this.ticks),
            this.configure(),
            this.beforeCalculateLabelRotation(),
            this.calculateLabelRotation(),
            this.afterCalculateLabelRotation(),
            r.display &&
                (r.autoSkip || r.source === "auto") &&
                ((this.ticks = Wu(this, this.ticks)),
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
        W(this.options.afterUpdate, [this]);
    }

    beforeSetDimensions() {
        W(this.options.beforeSetDimensions, [this]);
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
        W(this.options.afterSetDimensions, [this]);
    }

    _callHooks(t) {
        (this.chart.notifyPlugins(t, this.getContext()),
            W(this.options[t], [this]));
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
        W(this.options.beforeTickToLabelConversion, [this]);
    }

    generateTickLabels(t) {
        const e = this.options.ticks;
        let i;
        let n;
        let o;
        for (i = 0, n = t.length; i < n; i++) {
            ((o = t[i]), (o.label = W(e.callback, [o.value, i, t], this)));
        }
    }

    afterTickToLabelConversion() {
        W(this.options.afterTickToLabelConversion, [this]);
    }

    beforeCalculateLabelRotation() {
        W(this.options.beforeCalculateLabelRotation, [this]);
    }

    calculateLabelRotation() {
        const t = this.options;
        const e = t.ticks;
        const i = Yr(this.ticks.length, t.ticks.maxTicksLimit);
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
        const f = J(this.chart.width - u, 0, this.maxWidth);
        ((a = t.offset ? this.maxWidth / i : f / (i - 1)),
            u + 6 > a &&
                ((a = f / (i - (t.offset ? 0.5 : 1))),
                (l =
                    this.maxHeight -
                    _s(t.grid) -
                    e.padding -
                    qr(t.title, this.chart.options.font)),
                (c = Math.sqrt(u * u + d * d)),
                (r = si(
                    Math.min(
                        Math.asin(J((h.highest.height + 6) / a, -1, 1)),
                        Math.asin(J(l / c, -1, 1)) - Math.asin(J(d / c, -1, 1)),
                    ),
                )),
                (r = Math.max(n, Math.min(o, r)))),
            (this.labelRotation = r));
    }

    afterCalculateLabelRotation() {
        W(this.options.afterCalculateLabelRotation, [this]);
    }

    afterAutoSkip() {}
    beforeFit() {
        W(this.options.beforeFit, [this]);
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
            const l = qr(n, e.options.font);
            if (
                (a
                    ? ((t.width = this.maxWidth), (t.height = _s(o) + l))
                    : ((t.height = this.maxHeight), (t.width = _s(o) + l)),
                i.display && this.ticks.length)
            ) {
                const {
                    first: c,
                    last: h,
                    widest: u,
                    highest: d,
                } = this._getLabelSizes();
                const f = i.padding * 2;
                const g = bt(this.labelRotation);
                const m = Math.cos(g);
                const p = Math.sin(g);
                if (a) {
                    const b = i.mirror ? 0 : p * u.width + m * d.height;
                    t.height = Math.min(this.maxHeight, t.height + b + f);
                } else {
                    const b = i.mirror ? 0 : m * u.width + p * d.height;
                    t.width = Math.min(this.maxWidth, t.width + b + f);
                }
                this._calculatePadding(c, h, p, m);
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
        W(this.options.afterFit, [this]);
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
            (e < i.length && (i = Zr(i, e)),
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
        const l = Math.floor(e / Yr(e, i));
        let c = 0;
        let h = 0;
        let u;
        let d;
        let f;
        let g;
        let m;
        let p;
        let b;
        let y;
        let _;
        let w;
        let x;
        for (u = 0; u < e; u += l) {
            if (
                ((g = t[u].label),
                (m = this._resolveTickFontOptions(u)),
                (n.font = p = m.string),
                (b = o[p] = o[p] || { data: {}, gc: [] }),
                (y = m.lineHeight),
                (_ = w = 0),
                !A(g) && !H(g))
            ) {
                ((_ = ds(n, b.data, b.gc, _, g)), (w = y));
            } else if (H(g)) {
                for (d = 0, f = g.length; d < f; ++d) {
                    ((x = g[d]),
                        !A(x) &&
                            !H(x) &&
                            ((_ = ds(n, b.data, b.gc, _, x)), (w += y)));
                }
            }
            (r.push(_), a.push(w), (c = Math.max(_, c)), (h = Math.max(w, h)));
        }
        qu(o, e);
        const v = r.indexOf(c);
        const S = a.indexOf(h);
        const M = (T) => ({ width: r[T] || 0, height: a[T] || 0 });
        return {
            first: M(0),
            last: M(e - 1),
            widest: M(v),
            highest: M(S),
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
        return ar(this._alignToPixels ? Kt(this.chart, e, 0) : e);
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
            return i.$context || (i.$context = Xu(this.getContext(), t, i));
        }
        return (
            this.$context || (this.$context = Gu(this.chart.getContext(), this))
        );
    }

    _tickSize() {
        const t = this.options.ticks;
        const e = bt(this.labelRotation);
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
        const d = _s(o);
        const f = [];
        const g = a.setContext(this.getContext());
        const m = g.display ? g.width : 0;
        const p = m / 2;
        const b = function (U) {
            return Kt(i, U, m);
        };
        let y;
        let _;
        let w;
        let x;
        let v;
        let S;
        let M;
        let T;
        let C;
        let I;
        let L;
        let et;
        if (r === "top") {
            ((y = b(this.bottom)),
                (S = this.bottom - d),
                (T = y - p),
                (I = b(t.top) + p),
                (et = t.bottom));
        } else if (r === "bottom") {
            ((y = b(this.top)),
                (I = t.top),
                (et = b(t.bottom) - p),
                (S = y + p),
                (T = this.top + d));
        } else if (r === "left") {
            ((y = b(this.right)),
                (v = this.right - d),
                (M = y - p),
                (C = b(t.left) + p),
                (L = t.right));
        } else if (r === "right") {
            ((y = b(this.left)),
                (C = t.left),
                (L = b(t.right) - p),
                (v = y + p),
                (M = this.left + d));
        } else if (e === "x") {
            if (r === "center") y = b((t.top + t.bottom) / 2 + 0.5);
            else if (E(r)) {
                const U = Object.keys(r)[0];
                const G = r[U];
                y = b(this.chart.scales[U].getPixelForValue(G));
            }
            ((I = t.top), (et = t.bottom), (S = y + p), (T = S + d));
        } else if (e === "y") {
            if (r === "center") y = b((t.left + t.right) / 2);
            else if (E(r)) {
                const U = Object.keys(r)[0];
                const G = r[U];
                y = b(this.chart.scales[U].getPixelForValue(G));
            }
            ((v = y - p), (M = v - d), (C = t.left), (L = t.right));
        }
        const ht = P(n.ticks.maxTicksLimit, u);
        const V = Math.max(1, Math.ceil(u / ht));
        for (_ = 0; _ < u; _ += V) {
            const U = this.getContext(_);
            const G = o.setContext(U);
            const vt = a.setContext(U);
            const ot = G.lineWidth;
            const Me = G.color;
            const $s = vt.dash || [];
            const Oe = vt.dashOffset;
            const rs = G.tickWidth;
            const ae = G.tickColor;
            const as = G.tickBorderDash || [];
            const le = G.tickBorderDashOffset;
            ((w = Zu(this, _, l)),
                w !== void 0 &&
                    ((x = Kt(i, w, ot)),
                    c ? (v = M = C = L = x) : (S = T = I = et = x),
                    f.push({
                        tx1: v,
                        ty1: S,
                        tx2: M,
                        ty2: T,
                        x1: C,
                        y1: I,
                        x2: L,
                        y2: et,
                        width: ot,
                        color: Me,
                        borderDash: $s,
                        borderDashOffset: Oe,
                        tickWidth: rs,
                        tickColor: ae,
                        tickBorderDash: as,
                        tickBorderDashOffset: le,
                    })));
        }
        return ((this._ticksLength = u), (this._borderValue = y), f);
    }

    _computeLabelItems(t) {
        const e = this.axis;
        const i = this.options;
        const { position: n, ticks: o } = i;
        const r = this.isHorizontal();
        const a = this.ticks;
        const { align: l, crossAlign: c, padding: h, mirror: u } = o;
        const d = _s(i.grid);
        const f = d + h;
        const g = u ? -h : f;
        const m = -bt(this.labelRotation);
        const p = [];
        let b;
        let y;
        let _;
        let w;
        let x;
        let v;
        let S;
        let M;
        let T;
        let C;
        let I;
        let L;
        let et = "middle";
        if (n === "top") {
            ((v = this.bottom - g), (S = this._getXAxisLabelAlignment()));
        } else if (n === "bottom") {
            ((v = this.top + g), (S = this._getXAxisLabelAlignment()));
        } else if (n === "left") {
            const V = this._getYAxisLabelAlignment(d);
            ((S = V.textAlign), (x = V.x));
        } else if (n === "right") {
            const V = this._getYAxisLabelAlignment(d);
            ((S = V.textAlign), (x = V.x));
        } else if (e === "x") {
            if (n === "center") v = (t.top + t.bottom) / 2 + f;
            else if (E(n)) {
                const V = Object.keys(n)[0];
                const U = n[V];
                v = this.chart.scales[V].getPixelForValue(U) + f;
            }
            S = this._getXAxisLabelAlignment();
        } else if (e === "y") {
            if (n === "center") x = (t.left + t.right) / 2 - f;
            else if (E(n)) {
                const V = Object.keys(n)[0];
                const U = n[V];
                x = this.chart.scales[V].getPixelForValue(U);
            }
            S = this._getYAxisLabelAlignment(d).textAlign;
        }
        e === "y" &&
            (l === "start" ? (et = "top") : l === "end" && (et = "bottom"));
        const ht = this._getLabelSizes();
        for (b = 0, y = a.length; b < y; ++b) {
            ((_ = a[b]), (w = _.label));
            const V = o.setContext(this.getContext(b));
            ((M = this.getPixelForTick(b) + o.labelOffset),
                (T = this._resolveTickFontOptions(b)),
                (C = T.lineHeight),
                (I = H(w) ? w.length : 1));
            const U = I / 2;
            const G = V.color;
            const vt = V.textStrokeColor;
            const ot = V.textStrokeWidth;
            let Me = S;
            r
                ? ((x = M),
                  S === "inner" &&
                      (b === y - 1
                          ? (Me = this.options.reverse ? "left" : "right")
                          : b === 0
                            ? (Me = this.options.reverse ? "right" : "left")
                            : (Me = "center")),
                  n === "top"
                      ? c === "near" || m !== 0
                          ? (L = -I * C + C / 2)
                          : c === "center"
                            ? (L = -ht.highest.height / 2 - U * C + C)
                            : (L = -ht.highest.height + C / 2)
                      : c === "near" || m !== 0
                        ? (L = C / 2)
                        : c === "center"
                          ? (L = ht.highest.height / 2 - U * C)
                          : (L = ht.highest.height - I * C),
                  u && (L *= -1),
                  m !== 0 &&
                      !V.showLabelBackdrop &&
                      (x += (C / 2) * Math.sin(m)))
                : ((v = M), (L = ((1 - I) * C) / 2));
            let $s;
            if (V.showLabelBackdrop) {
                const Oe = nt(V.backdropPadding);
                const rs = ht.heights[b];
                const ae = ht.widths[b];
                let as = L - Oe.top;
                let le = 0 - Oe.left;
                switch (et) {
                    case "middle":
                        as -= rs / 2;
                        break;
                    case "bottom":
                        as -= rs;
                        break;
                }
                switch (S) {
                    case "center":
                        le -= ae / 2;
                        break;
                    case "right":
                        le -= ae;
                        break;
                    case "inner":
                        b === y - 1 ? (le -= ae) : b > 0 && (le -= ae / 2);
                        break;
                }
                $s = {
                    left: le,
                    top: as,
                    width: ae + Oe.width,
                    height: rs + Oe.height,
                    color: V.backdropColor,
                };
            }
            p.push({
                label: w,
                font: T,
                textOffset: L,
                options: {
                    rotation: m,
                    color: G,
                    strokeColor: vt,
                    strokeWidth: ot,
                    textAlign: Me,
                    textBaseline: et,
                    translation: [x, v],
                    backdrop: $s,
                },
            });
        }
        return p;
    }

    _getXAxisLabelAlignment() {
        const { position: t, ticks: e } = this.options;
        if (-bt(this.labelRotation)) return t === "top" ? "left" : "right";
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
            ? ((c = Kt(t, this.left, r) - r / 2),
              (h = Kt(t, this.right, a) + a / 2),
              (u = d = l))
            : ((u = Kt(t, this.top, r) - r / 2),
              (d = Kt(t, this.bottom, a) + a / 2),
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
        n && ms(i, n);
        const o = this.getLabelItems(t);
        for (const r of o) {
            const a = r.options;
            const l = r.font;
            const c = r.label;
            const h = r.textOffset;
            Jt(i, c, 0, h, l, a);
        }
        n && ps(i);
    }

    drawTitle() {
        const {
            ctx: t,
            options: { position: e, title: i, reverse: n },
        } = this;
        if (!i.display) return;
        const o = X(i.font);
        const r = nt(i.padding);
        const a = i.align;
        let l = o.lineHeight / 2;
        e === "bottom" || e === "center" || E(e)
            ? ((l += r.bottom),
              H(i.text) && (l += o.lineHeight * (i.text.length - 1)))
            : (l += r.top);
        const {
            titleX: c,
            titleY: h,
            maxWidth: u,
            rotation: d,
        } = Ju(this, l, e, a);
        Jt(t, i.text, 0, 0, o, {
            color: i.color,
            maxWidth: u,
            rotation: d,
            textAlign: Ku(a, e, n),
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
        const i = P(t.grid && t.grid.z, -1);
        const n = P(t.border && t.border.z, 0);
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
        return X(e.font);
    }

    _maxDigits() {
        const t = this._resolveTickFontOptions(0).lineHeight;
        return (this.isHorizontal() ? this.width : this.height) / t;
    }
};
const Ye = class {
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
        ed(e) && (i = this.register(e));
        const n = this.items;
        const o = t.id;
        const r = this.scope + "." + o;
        if (!o) throw new Error("class does not have id: " + t);
        return (
            o in n ||
                ((n[o] = t),
                Qu(t, r, i),
                this.override && j.override(t.id, t.overrides)),
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
            n && i in j[n] && (delete j[n][i], this.override && delete Xt[i]));
    }
};
function Qu(s, t, e) {
    const i = Ce(Object.create(null), [
        e ? j.get(e) : {},
        j.get(t),
        s.defaults,
    ]);
    (j.set(t, i),
        s.defaultRoutes && td(t, s.defaultRoutes),
        s.descriptors && j.describe(t, s.descriptors));
}
function td(s, t) {
    Object.keys(t).forEach((e) => {
        const i = e.split(".");
        const n = i.pop();
        const o = [s].concat(i).join(".");
        const r = t[e].split(".");
        const a = r.pop();
        const l = r.join(".");
        j.route(o, n, l, a);
    });
}
function ed(s) {
    return "id" in s && "defaults" in s;
}
const Yn = class {
    constructor() {
        ((this.controllers = new Ye(ut, "datasets", !0)),
            (this.elements = new Ye(dt, "elements")),
            (this.plugins = new Ye(Object, "plugins")),
            (this.scales = new Ye(_e, "scales")),
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
                : z(n, (r) => {
                      const a = i || this._getRegistryForType(r);
                      this._exec(t, a, r);
                  });
        });
    }

    _exec(t, e, i) {
        const n = ei(t);
        (W(i["before" + n], [], i), e[t](i), W(i["after" + n], [], i));
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
const Et = new Yn();
const Zn = class {
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
            if (W(a, l, r) === !1 && n.cancelable) return !1;
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
        const n = P(i.options && i.options.plugins, {});
        const o = sd(i);
        return n === !1 && !e ? [] : nd(t, o, n, e);
    }

    _notifyStateChanges(t) {
        const e = this._oldCache || [];
        const i = this._cache;
        const n = (o, r) =>
            o.filter((a) => !r.some((l) => a.plugin.id === l.plugin.id));
        (this._notify(n(e, i), t, "stop"), this._notify(n(i, e), t, "start"));
    }
};
function sd(s) {
    const t = {};
    const e = [];
    const i = Object.keys(Et.plugins.items);
    for (let o = 0; o < i.length; o++) e.push(Et.getPlugin(i[o]));
    const n = s.plugins || [];
    for (let o = 0; o < n.length; o++) {
        const r = n[o];
        e.indexOf(r) === -1 && (e.push(r), (t[r.id] = !0));
    }
    return { plugins: e, localIds: t };
}
function id(s, t) {
    return !t && s === !1 ? null : s === !0 ? {} : s;
}
function nd(s, { plugins: t, localIds: e }, i, n) {
    const o = [];
    const r = s.getContext();
    for (const a of t) {
        const l = a.id;
        const c = id(i[l], n);
        c !== null &&
            o.push({
                plugin: a,
                options: od(s.config, { plugin: a, local: e[l] }, c, r),
            });
    }
    return o;
}
function od(s, { plugin: t, local: e }, i, n) {
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
function qn(s, t) {
    const e = j.datasets[s] || {};
    return (
        ((t.datasets || {})[s] || {}).indexAxis ||
        t.indexAxis ||
        e.indexAxis ||
        "x"
    );
}
function rd(s, t) {
    let e = s;
    return (
        s === "_index_"
            ? (e = t)
            : s === "_value_" && (e = t === "x" ? "y" : "x"),
        e
    );
}
function ad(s, t) {
    return s === t ? "_index_" : "_value_";
}
function Gr(s) {
    if (s === "x" || s === "y" || s === "r") return s;
}
function ld(s) {
    if (s === "top" || s === "bottom") return "x";
    if (s === "left" || s === "right") return "y";
}
function Gn(s, ...t) {
    if (Gr(s)) return s;
    for (const e of t) {
        const i =
            e.axis ||
            ld(e.position) ||
            (s.length > 1 && Gr(s[0].toLowerCase()));
        if (i) return i;
    }
    throw new Error(
        `Cannot determine type of '${s}' axis. Please provide 'axis' or 'position' option.`,
    );
}
function Xr(s, t, e) {
    if (e[t + "AxisID"] === s) return { axis: t };
}
function cd(s, t) {
    if (t.data && t.data.datasets) {
        const e = t.data.datasets.filter(
            (i) => i.xAxisID === s || i.yAxisID === s,
        );
        if (e.length) return Xr(s, "x", e[0]) || Xr(s, "y", e[0]);
    }
    return {};
}
function hd(s, t) {
    const e = Xt[s.type] || { scales: {} };
    const i = t.scales || {};
    const n = qn(s.type, t);
    const o = Object.create(null);
    return (
        Object.keys(i).forEach((r) => {
            const a = i[r];
            if (!E(a)) {
                return console.error(
                    `Invalid scale configuration for scale: ${r}`,
                );
            }
            if (a._proxy) {
                return console.warn(
                    `Ignoring resolver passed as options for scale: ${r}`,
                );
            }
            const l = Gn(r, a, cd(r, s), j.scales[a.type]);
            const c = ad(l, n);
            const h = e.scales || {};
            o[r] = Ie(Object.create(null), [{ axis: l }, a, h[l], h[c]]);
        }),
        s.data.datasets.forEach((r) => {
            const a = r.type || s.type;
            const l = r.indexAxis || qn(a, t);
            const h = (Xt[a] || {}).scales || {};
            Object.keys(h).forEach((u) => {
                const d = rd(u, l);
                const f = r[d + "AxisID"] || d;
                ((o[f] = o[f] || Object.create(null)),
                    Ie(o[f], [{ axis: d }, i[f], h[u]]));
            });
        }),
        Object.keys(o).forEach((r) => {
            const a = o[r];
            Ie(a, [j.scales[a.type], j.scale]);
        }),
        o
    );
}
function Fa(s) {
    const t = s.options || (s.options = {});
    ((t.plugins = P(t.plugins, {})), (t.scales = hd(s, t)));
}
function Ra(s) {
    return (
        (s = s || {}),
        (s.datasets = s.datasets || []),
        (s.labels = s.labels || []),
        s
    );
}
function ud(s) {
    return ((s = s || {}), (s.data = Ra(s.data)), Fa(s), s);
}
const Kr = new Map();
const Na = new Set();
function gi(s, t) {
    let e = Kr.get(s);
    return (e || ((e = t()), Kr.set(s, e), Na.add(e)), e);
}
const ws = (s, t, e) => {
    const i = Wt(t, e);
    i !== void 0 && s.add(i);
};
const Xn = class {
    constructor(t) {
        ((this._config = ud(t)),
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
        this._config.data = Ra(t);
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
        (this.clearCache(), Fa(t));
    }

    clearCache() {
        (this._scopeCache.clear(), this._resolverCache.clear());
    }

    datasetScopeKeys(t) {
        return gi(t, () => [[`datasets.${t}`, ""]]);
    }

    datasetAnimationScopeKeys(t, e) {
        return gi(`${t}.transition.${e}`, () => [
            [`datasets.${t}.transitions.${e}`, `transitions.${e}`],
            [`datasets.${t}`, ""],
        ]);
    }

    datasetElementScopeKeys(t, e) {
        return gi(`${t}-${e}`, () => [
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
        return gi(`${i}-plugin-${e}`, () => [
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
            (t && (l.add(t), h.forEach((u) => ws(l, t, u))),
                h.forEach((u) => ws(l, n, u)),
                h.forEach((u) => ws(l, Xt[o] || {}, u)),
                h.forEach((u) => ws(l, j, u)),
                h.forEach((u) => ws(l, oi, u)));
        });
        const c = Array.from(l);
        return (
            c.length === 0 && c.push(Object.create(null)),
            Na.has(e) && r.set(e, c),
            c
        );
    }

    chartOptionScopes() {
        const { options: t, type: e } = this;
        return [t, Xt[e] || {}, j.datasets[e] || {}, { type: e }, j, oi];
    }

    resolveNamedOptions(t, e, i, n = [""]) {
        const o = { $shared: !0 };
        const { resolver: r, subPrefixes: a } = Jr(this._resolverCache, t, n);
        let l = r;
        if (fd(r, e)) {
            ((o.$shared = !1), (i = zt(i) ? i() : i));
            const c = this.createResolver(t, i, a);
            l = ue(r, i, c);
        }
        for (const c of e) o[c] = l[c];
        return o;
    }

    createResolver(t, e, i = [""], n) {
        const { resolver: o } = Jr(this._resolverCache, t, i);
        return E(e) ? ue(o, e, void 0, n) : o;
    }
};
function Jr(s, t, e) {
    let i = s.get(t);
    i || ((i = new Map()), s.set(t, i));
    const n = e.join();
    let o = i.get(n);
    return (
        o ||
            ((o = {
                resolver: li(t, e),
                subPrefixes: e.filter(
                    (a) => !a.toLowerCase().includes("hover"),
                ),
            }),
            i.set(n, o)),
        o
    );
}
const dd = (s) => E(s) && Object.getOwnPropertyNames(s).some((t) => zt(s[t]));
function fd(s, t) {
    const { isScriptable: e, isIndexable: i } = xn(s);
    for (const n of t) {
        const o = e(n);
        const r = i(n);
        const a = (r || o) && s[n];
        if ((o && (zt(a) || dd(a))) || (r && H(a))) return !0;
    }
    return !1;
}
const gd = "4.5.0";
const md = ["top", "bottom", "left", "right", "chartArea"];
function Qr(s, t) {
    return s === "top" || s === "bottom" || (md.indexOf(s) === -1 && t === "x");
}
function ta(s, t) {
    return function (e, i) {
        return e[s] === i[s] ? e[t] - i[t] : e[s] - i[s];
    };
}
function ea(s) {
    const t = s.chart;
    const e = t.options.animation;
    (t.notifyPlugins("afterRender"), W(e && e.onComplete, [s], t));
}
function pd(s) {
    const t = s.chart;
    const e = t.options.animation;
    W(e && e.onProgress, [s], t);
}
function za(s) {
    return (
        ci() && typeof s === "string"
            ? (s = document.getElementById(s))
            : s && s.length && (s = s[0]),
        s && s.canvas && (s = s.canvas),
        s
    );
}
const xi = {};
const sa = (s) => {
    const t = za(s);
    return Object.values(xi)
        .filter((e) => e.canvas === t)
        .pop();
};
function bd(s, t, e) {
    const i = Object.keys(s);
    for (const n of i) {
        const o = +n;
        if (o >= t) {
            const r = s[n];
            (delete s[n], (e > 0 || o > t) && (s[o + e] = r));
        }
    }
}
function yd(s, t, e, i) {
    return !e || s.type === "mouseout" ? null : i ? t : s;
}
const yt = class {
    static register(...t) {
        (Et.add(...t), ia());
    }

    static unregister(...t) {
        (Et.remove(...t), ia());
    }

    constructor(t, e) {
        const i = (this.config = new Xn(e));
        const n = za(t);
        const o = sa(n);
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
        ((this.platform = new (i.platform || Vu(n))()),
            this.platform.updateConfig(i));
        const a = this.platform.acquireContext(n, r.aspectRatio);
        const l = a && a.canvas;
        const c = l && l.height;
        const h = l && l.width;
        if (
            ((this.id = er()),
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
            (this._plugins = new Zn()),
            (this.$proxies = {}),
            (this._hiddenIndices = {}),
            (this.attached = !1),
            (this._animationsDisabled = void 0),
            (this.$context = void 0),
            (this._doResize = dr((u) => this.update(u), r.resizeDelay || 0)),
            (this._dataChanges = []),
            (xi[this.id] = this),
            !a || !l)
        ) {
            console.error(
                "Failed to create chart: can't acquire context from the given item",
            );
            return;
        }
        (Ht.listen(this, "complete", ea),
            Ht.listen(this, "progress", pd),
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
        return Et;
    }

    _initialize() {
        return (
            this.notifyPlugins("beforeInit"),
            this.options.responsive
                ? this.resize()
                : vn(this, this.options.devicePixelRatio),
            this.bindEvents(),
            this.notifyPlugins("afterInit"),
            this
        );
    }

    clear() {
        return (pn(this.canvas, this.ctx), this);
    }

    stop() {
        return (Ht.stop(this), this);
    }

    resize(t, e) {
        Ht.running(this)
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
            vn(this, a, !0) &&
                (this.notifyPlugins("resize", { size: r }),
                W(i.onResize, [this, r], this),
                this.attached && this._doResize(l) && this.render()));
    }

    ensureScalesHaveIDs() {
        const e = this.options.scales || {};
        z(e, (i, n) => {
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
                    const l = Gn(r, a);
                    const c = l === "r";
                    const h = l === "x";
                    return {
                        options: a,
                        dposition: c ? "chartArea" : h ? "bottom" : "left",
                        dtype: c ? "radialLinear" : h ? "category" : "linear",
                    };
                }),
            )),
            z(o, (r) => {
                const a = r.options;
                const l = a.id;
                const c = Gn(l, a);
                const h = P(a.type, r.dtype);
                ((a.position === void 0 ||
                    Qr(a.position, c) !== Qr(r.dposition)) &&
                    (a.position = r.dposition),
                    (n[l] = !0));
                let u = null;
                if (l in i && i[l].type === h) u = i[l];
                else {
                    const d = Et.getScale(h);
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
            z(n, (r, a) => {
                r || delete i[a];
            }),
            z(i, (r) => {
                (rt.configure(this, r, r.options), rt.addBox(this, r));
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
        this._sortedMetasets = t.slice(0).sort(ta("order", "index"));
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
                (r.indexAxis = o.indexAxis || qn(a, this.options)),
                (r.order = o.order || 0),
                (r.index = i),
                (r.label = "" + o.label),
                (r.visible = this.isDatasetVisible(i)),
                r.controller)
            ) {
                (r.controller.updateIndex(i), r.controller.linkScales());
            } else {
                const l = Et.getController(a);
                const { datasetElementType: c, dataElementType: h } =
                    j.datasets[a];
                (Object.assign(l, {
                    dataElementType: Et.getElement(h),
                    datasetElementType: c && Et.getElement(c),
                }),
                    (r.controller = new l(this, i)),
                    t.push(r.controller));
            }
        }
        return (this._updateMetasets(), t);
    }

    _resetElements() {
        z(
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
                z(o, (c) => {
                    c.reset();
                }),
            this._updateDatasets(t),
            this.notifyPlugins("afterUpdate", { mode: t }),
            this._layers.sort(ta("z", "_idx")));
        const { _active: a, _lastEvent: l } = this;
        (l
            ? this._eventHandler(l, !0)
            : a.length && this._updateHoverStyles(a, a, !0),
            this.render());
    }

    _updateScales() {
        (z(this.scales, (t) => {
            rt.removeBox(this, t);
        }),
            this.ensureScalesHaveIDs(),
            this.buildOrUpdateScales());
    }

    _checkEventBindings() {
        const t = this.options;
        const e = new Set(Object.keys(this._listeners));
        const i = new Set(t.events);
        (!sn(e, i) || !!this._responsiveListeners !== t.responsive) &&
            (this.unbindEvents(), this.bindEvents());
    }

    _updateHiddenIndices() {
        const { _hiddenIndices: t } = this;
        const e = this._getUniformDataChanges() || [];
        for (const { method: i, start: n, count: o } of e) {
            const r = i === "_removeElements" ? -o : o;
            bd(t, n, r);
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
        for (let o = 1; o < e; o++) if (!sn(n, i(o))) return;
        return Array.from(n)
            .map((o) => o.split(","))
            .map((o) => ({ method: o[1], start: +o[2], count: +o[3] }));
    }

    _updateLayout(t) {
        if (this.notifyPlugins("beforeLayout", { cancelable: !0 }) === !1) {
            return;
        }
        rt.update(this, this.width, this.height, t);
        const e = this.chartArea;
        const i = e.width <= 0 || e.height <= 0;
        ((this._layers = []),
            z(
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
                this._updateDataset(e, zt(t) ? t({ datasetIndex: e }) : t);
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
            (Ht.has(this)
                ? this.attached && !Ht.running(this) && Ht.start(this)
                : (this.draw(), ea({ chart: this })));
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
        const n = Cn(this, t);
        this.notifyPlugins("beforeDatasetDraw", i) !== !1 &&
            (n && ms(e, n),
            t.controller.draw(),
            n && ps(e),
            (i.cancelable = !1),
            this.notifyPlugins("afterDatasetDraw", i));
    }

    isPointInArea(t) {
        return Ct(t, this.chartArea, this._minPadding);
    }

    getElementsAtEventForMode(t, e, i, n) {
        const o = _u.modes[e];
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
            (this.$context = Bt(null, { chart: this, type: "chart" }))
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
        Ae(e)
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
            this.stop(), Ht.remove(this), t = 0, e = this.data.datasets.length;
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
                pn(t, e),
                this.platform.releaseContext(e),
                (this.canvas = null),
                (this.ctx = null)),
            delete xi[this.id],
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
        z(this.options.events, (o) => i(o, n));
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
        (z(this._listeners, (t, e) => {
            this.platform.removeEventListener(this, e, t);
        }),
            (this._listeners = {}),
            z(this._responsiveListeners, (t, e) => {
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
        !fs(i, e) &&
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
        const l = nr(t);
        const c = yd(t, this._lastEvent, i, l);
        i &&
            ((this._lastEvent = null),
            W(o.onHover, [t, a, this], this),
            l && W(o.onClick, [t, a, this], this));
        const h = !fs(a, n);
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
(k(yt, "defaults", j),
    k(yt, "instances", xi),
    k(yt, "overrides", Xt),
    k(yt, "registry", Et),
    k(yt, "version", gd),
    k(yt, "getChart", sa));
function ia() {
    return z(yt.instances, (s) => s._plugins.invalidate());
}
function xd(s, t, e) {
    const {
        startAngle: i,
        x: n,
        y: o,
        outerRadius: r,
        innerRadius: a,
        options: l,
    } = t;
    const { borderWidth: c, borderJoinStyle: h } = l;
    const u = Math.min(c / r, st(i - e));
    if ((s.beginPath(), s.arc(n, o, r - c / 2, i + u / 2, e - u / 2), a > 0)) {
        const d = Math.min(c / a, st(i - e));
        s.arc(n, o, a + c / 2, e - d / 2, i + d / 2, !0);
    } else {
        const d = Math.min(c / 2, r * st(i - e));
        if (h === "round") s.arc(n, o, d, e - F / 2, i + F / 2, !0);
        else if (h === "bevel") {
            const f = 2 * d * d;
            const g = -f * Math.cos(e + F / 2) + n;
            const m = -f * Math.sin(e + F / 2) + o;
            const p = f * Math.cos(i + F / 2) + n;
            const b = f * Math.sin(i + F / 2) + o;
            (s.lineTo(g, m), s.lineTo(p, b));
        }
    }
    (s.closePath(),
        s.moveTo(0, 0),
        s.rect(0, 0, s.canvas.width, s.canvas.height),
        s.clip("evenodd"));
}
function _d(s, t, e) {
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
            : s.arc(o, r, n, e + q, i - q),
        s.closePath(),
        s.clip());
}
function wd(s) {
    return ai(s, ["outerStart", "outerEnd", "innerStart", "innerEnd"]);
}
function kd(s, t, e, i) {
    const n = wd(s.options.borderRadius);
    const o = (e - t) / 2;
    const r = Math.min(o, (i * t) / 2);
    const a = (l) => {
        const c = ((e - Math.min(o, l)) * i) / 2;
        return J(l, 0, Math.min(o, c));
    };
    return {
        outerStart: a(n.outerStart),
        outerEnd: a(n.outerEnd),
        innerStart: J(n.innerStart, 0, r),
        innerEnd: J(n.innerEnd, 0, r),
    };
}
function ze(s, t, e, i) {
    return { x: e + s * Math.cos(t), y: i + s * Math.sin(t) };
}
function vi(s, t, e, i, n, o) {
    const { x: r, y: a, startAngle: l, pixelMargin: c, innerRadius: h } = t;
    const u = Math.max(t.outerRadius + i + e - c, 0);
    const d = h > 0 ? h + i + e + c : 0;
    let f = 0;
    const g = n - l;
    if (i) {
        const V = h > 0 ? h - i : 0;
        const U = u > 0 ? u - i : 0;
        const G = (V + U) / 2;
        const vt = G !== 0 ? (g * G) / (G + i) : g;
        f = (g - vt) / 2;
    }
    const m = Math.max(0.001, g * u - e / F) / u;
    const p = (g - m) / 2;
    const b = l + p + f;
    const y = n - p - f;
    const {
        outerStart: _,
        outerEnd: w,
        innerStart: x,
        innerEnd: v,
    } = kd(t, d, u, y - b);
    const S = u - _;
    const M = u - w;
    const T = b + _ / S;
    const C = y - w / M;
    const I = d + x;
    const L = d + v;
    const et = b + x / I;
    const ht = y - v / L;
    if ((s.beginPath(), o)) {
        const V = (T + C) / 2;
        if ((s.arc(r, a, u, T, V), s.arc(r, a, u, V, C), w > 0)) {
            const ot = ze(M, C, r, a);
            s.arc(ot.x, ot.y, w, C, y + q);
        }
        const U = ze(L, y, r, a);
        if ((s.lineTo(U.x, U.y), v > 0)) {
            const ot = ze(L, ht, r, a);
            s.arc(ot.x, ot.y, v, y + q, ht + Math.PI);
        }
        const G = (y - v / d + (b + x / d)) / 2;
        if (
            (s.arc(r, a, d, y - v / d, G, !0),
            s.arc(r, a, d, G, b + x / d, !0),
            x > 0)
        ) {
            const ot = ze(I, et, r, a);
            s.arc(ot.x, ot.y, x, et + Math.PI, b - q);
        }
        const vt = ze(S, b, r, a);
        if ((s.lineTo(vt.x, vt.y), _ > 0)) {
            const ot = ze(S, T, r, a);
            s.arc(ot.x, ot.y, _, b - q, T);
        }
    } else {
        s.moveTo(r, a);
        const V = Math.cos(T) * u + r;
        const U = Math.sin(T) * u + a;
        s.lineTo(V, U);
        const G = Math.cos(C) * u + r;
        const vt = Math.sin(C) * u + a;
        s.lineTo(G, vt);
    }
    s.closePath();
}
function vd(s, t, e, i, n) {
    const { fullCircles: o, startAngle: r, circumference: a } = t;
    let l = t.endAngle;
    if (o) {
        vi(s, t, e, i, l, n);
        for (let c = 0; c < o; ++c) s.fill();
        isNaN(a) || (l = r + (a % $ || $));
    }
    return (vi(s, t, e, i, l, n), s.fill(), l);
}
function Sd(s, t, e, i, n) {
    const { fullCircles: o, startAngle: r, circumference: a, options: l } = t;
    const {
        borderWidth: c,
        borderJoinStyle: h,
        borderDash: u,
        borderDashOffset: d,
        borderRadius: f,
    } = l;
    const g = l.borderAlign === "inner";
    if (!c) return;
    (s.setLineDash(u || []),
        (s.lineDashOffset = d),
        g
            ? ((s.lineWidth = c * 2), (s.lineJoin = h || "round"))
            : ((s.lineWidth = c), (s.lineJoin = h || "bevel")));
    let m = t.endAngle;
    if (o) {
        vi(s, t, e, i, m, n);
        for (let p = 0; p < o; ++p) s.stroke();
        isNaN(a) || (m = r + (a % $ || $));
    }
    (g && _d(s, t, m),
        l.selfJoin && m - r >= F && f === 0 && h !== "miter" && xd(s, t, m),
        o || (vi(s, t, e, i, m, n), s.stroke()));
}
const pe = class extends dt {
    constructor(e) {
        super();
        k(this, "circumference");
        k(this, "endAngle");
        k(this, "fullCircles");
        k(this, "innerRadius");
        k(this, "outerRadius");
        k(this, "pixelMargin");
        k(this, "startAngle");
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
        const { angle: r, distance: a } = an(o, { x: e, y: i });
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
        const g = P(d, c - l);
        const m = Le(r, l, c) && l !== c;
        const p = g >= $ || m;
        const b = It(a, h + f, u + f);
        return p && b;
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
            (this.fullCircles = n > $ ? Math.floor(n / $) : 0),
            n === 0 || this.innerRadius < 0 || this.outerRadius < 0)
        ) {
            return;
        }
        e.save();
        const l = (this.startAngle + this.endAngle) / 2;
        e.translate(Math.cos(l) * o, Math.sin(l) * o);
        const c = 1 - Math.sin(Math.min(F, n || 0));
        const h = o * c;
        ((e.fillStyle = i.backgroundColor),
            (e.strokeStyle = i.borderColor),
            vd(e, this, h, r, a),
            Sd(e, this, h, r, a),
            e.restore());
    }
};
(k(pe, "id", "arc"),
    k(pe, "defaults", {
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
    k(pe, "defaultRoutes", { backgroundColor: "backgroundColor" }),
    k(pe, "descriptors", {
        _scriptable: !0,
        _indexable: (e) => e !== "borderDash",
    }));
function Va(s, t, e = t) {
    ((s.lineCap = P(e.borderCapStyle, t.borderCapStyle)),
        s.setLineDash(P(e.borderDash, t.borderDash)),
        (s.lineDashOffset = P(e.borderDashOffset, t.borderDashOffset)),
        (s.lineJoin = P(e.borderJoinStyle, t.borderJoinStyle)),
        (s.lineWidth = P(e.borderWidth, t.borderWidth)),
        (s.strokeStyle = P(e.borderColor, t.borderColor)));
}
function Md(s, t, e) {
    s.lineTo(e.x, e.y);
}
function Od(s) {
    return s.stepped
        ? pr
        : s.tension || s.cubicInterpolationMode === "monotone"
          ? br
          : Md;
}
function Wa(s, t, e = {}) {
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
function Td(s, t, e, i) {
    const { points: n, options: o } = t;
    const { count: r, start: a, loop: l, ilen: c } = Wa(n, e, i);
    const h = Od(o);
    let { move: u = !0, reverse: d } = i || {};
    let f;
    let g;
    let m;
    for (f = 0; f <= c; ++f) {
        ((g = n[(a + (d ? c - f : f)) % r]),
            !g.skip &&
                (u ? (s.moveTo(g.x, g.y), (u = !1)) : h(s, m, g, d, o.stepped),
                (m = g)));
    }
    return (
        l && ((g = n[(a + (d ? c : 0)) % r]), h(s, m, g, d, o.stepped)),
        !!l
    );
}
function Dd(s, t, e, i) {
    const n = t.points;
    const { count: o, start: r, ilen: a } = Wa(n, e, i);
    const { move: l = !0, reverse: c } = i || {};
    let h = 0;
    let u = 0;
    let d;
    let f;
    let g;
    let m;
    let p;
    let b;
    const y = (w) => (r + (c ? a - w : w)) % o;
    const _ = () => {
        m !== p && (s.lineTo(h, p), s.lineTo(h, m), s.lineTo(h, b));
    };
    for (l && ((f = n[y(0)]), s.moveTo(f.x, f.y)), d = 0; d <= a; ++d) {
        if (((f = n[y(d)]), f.skip)) continue;
        const w = f.x;
        const x = f.y;
        const v = w | 0;
        (v === g
            ? (x < m ? (m = x) : x > p && (p = x), (h = (u * h + w) / ++u))
            : (_(), s.lineTo(w, x), (g = v), (u = 0), (m = p = x)),
            (b = x));
    }
    _();
}
function Kn(s) {
    const t = s.options;
    const e = t.borderDash && t.borderDash.length;
    return !s._decimated &&
        !s._loop &&
        !t.tension &&
        t.cubicInterpolationMode !== "monotone" &&
        !t.stepped &&
        !e
        ? Dd
        : Td;
}
function Cd(s) {
    return s.stepped
        ? Or
        : s.tension || s.cubicInterpolationMode === "monotone"
          ? Tr
          : Gt;
}
function Pd(s, t, e, i) {
    let n = t._path;
    (n || ((n = t._path = new Path2D()), t.path(n, e, i) && n.closePath()),
        Va(s, t.options),
        s.stroke(n));
}
function Id(s, t, e, i) {
    const { segments: n, options: o } = t;
    const r = Kn(t);
    for (const a of n) {
        (Va(s, o, a.style),
            s.beginPath(),
            r(s, t, a, { start: e, end: e + i - 1 }) && s.closePath(),
            s.stroke());
    }
}
const Ad = typeof Path2D === "function";
function Ed(s, t, e, i) {
    Ad && !t.options.segment ? Pd(s, t, e, i) : Id(s, t, e, i);
}
const Lt = class extends dt {
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
            (vr(this._points, i, t, n, e), (this._pointsUpdated = !0));
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
            this._segments || (this._segments = Cr(this, this.options.segment))
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
        const r = Dn(this, { property: e, start: n, end: n });
        if (!r.length) return;
        const a = [];
        const l = Cd(i);
        let c;
        let h;
        for (c = 0, h = r.length; c < h; ++c) {
            const { start: u, end: d } = r[c];
            const f = o[u];
            const g = o[d];
            if (f === g) {
                a.push(f);
                continue;
            }
            const m = Math.abs((n - f[e]) / (g[e] - f[e]));
            const p = l(f, g, m, i.stepped);
            ((p[e] = t[e]), a.push(p));
        }
        return a.length === 1 ? a[0] : a;
    }

    pathSegment(t, e, i) {
        return Kn(this)(t, this, e, i);
    }

    path(t, e, i) {
        const n = this.segments;
        const o = Kn(this);
        let r = this._loop;
        ((e = e || 0), (i = i || this.points.length - e));
        for (const a of n) r &= o(t, this, a, { start: e, end: e + i - 1 });
        return !!r;
    }

    draw(t, e, i, n) {
        const o = this.options || {};
        ((this.points || []).length &&
            o.borderWidth &&
            (t.save(), Ed(t, this, i, n), t.restore()),
            this.animated &&
                ((this._pointsUpdated = !1), (this._path = void 0)));
    }
};
(k(Lt, "id", "line"),
    k(Lt, "defaults", {
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
    k(Lt, "defaultRoutes", {
        backgroundColor: "backgroundColor",
        borderColor: "borderColor",
    }),
    k(Lt, "descriptors", {
        _scriptable: !0,
        _indexable: (t) => t !== "borderDash" && t !== "fill",
    }));
function na(s, t, e, i) {
    const n = s.options;
    const { [e]: o } = s.getProps([e], i);
    return Math.abs(t - o) < n.radius + n.hitRadius;
}
const je = class extends dt {
    constructor(e) {
        super();
        k(this, "parsed");
        k(this, "skip");
        k(this, "stop");
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
        return na(this, e, "x", i);
    }

    inYRange(e, i) {
        return na(this, e, "y", i);
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
            !Ct(this, i, this.size(n) / 2) ||
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
(k(je, "id", "point"),
    k(je, "defaults", {
        borderWidth: 1,
        hitRadius: 1,
        hoverBorderWidth: 1,
        hoverRadius: 4,
        pointStyle: "circle",
        radius: 3,
        rotation: 0,
    }),
    k(je, "defaultRoutes", {
        backgroundColor: "backgroundColor",
        borderColor: "borderColor",
    }));
function Ba(s, t) {
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
function ee(s, t, e, i) {
    return s ? 0 : J(t, e, i);
}
function Ld(s, t, e) {
    const i = s.options.borderWidth;
    const n = s.borderSkipped;
    const o = yn(i);
    return {
        t: ee(n.top, o.top, 0, e),
        r: ee(n.right, o.right, 0, t),
        b: ee(n.bottom, o.bottom, 0, e),
        l: ee(n.left, o.left, 0, t),
    };
}
function Fd(s, t, e) {
    const { enableBorderRadius: i } = s.getProps(["enableBorderRadius"]);
    const n = s.options.borderRadius;
    const o = Qt(n);
    const r = Math.min(t, e);
    const a = s.borderSkipped;
    const l = i || E(n);
    return {
        topLeft: ee(!l || a.top || a.left, o.topLeft, 0, r),
        topRight: ee(!l || a.top || a.right, o.topRight, 0, r),
        bottomLeft: ee(!l || a.bottom || a.left, o.bottomLeft, 0, r),
        bottomRight: ee(!l || a.bottom || a.right, o.bottomRight, 0, r),
    };
}
function Rd(s) {
    const t = Ba(s);
    const e = t.right - t.left;
    const i = t.bottom - t.top;
    const n = Ld(s, e / 2, i / 2);
    const o = Fd(s, e / 2, i / 2);
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
function Nn(s, t, e, i) {
    const n = t === null;
    const o = e === null;
    const a = s && !(n && o) && Ba(s, i);
    return a && (n || It(t, a.left, a.right)) && (o || It(e, a.top, a.bottom));
}
function Nd(s) {
    return s.topLeft || s.topRight || s.bottomLeft || s.bottomRight;
}
function zd(s, t) {
    s.rect(t.x, t.y, t.w, t.h);
}
function zn(s, t, e = {}) {
    const i = s.x !== e.x ? -t : 0;
    const n = s.y !== e.y ? -t : 0;
    const o = (s.x + s.w !== e.x + e.w ? t : 0) - i;
    const r = (s.y + s.h !== e.y + e.h ? t : 0) - n;
    return { x: s.x + i, y: s.y + n, w: s.w + o, h: s.h + r, radius: s.radius };
}
const Ue = class extends dt {
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
        const { inner: o, outer: r } = Rd(this);
        const a = Nd(r.radius) ? Re : zd;
        (t.save(),
            (r.w !== o.w || r.h !== o.h) &&
                (t.beginPath(),
                a(t, zn(r, e, o)),
                t.clip(),
                a(t, zn(o, -e, r)),
                (t.fillStyle = i),
                t.fill("evenodd")),
            t.beginPath(),
            a(t, zn(o, e)),
            (t.fillStyle = n),
            t.fill(),
            t.restore());
    }

    inRange(t, e, i) {
        return Nn(this, t, e, i);
    }

    inXRange(t, e) {
        return Nn(this, t, null, e);
    }

    inYRange(t, e) {
        return Nn(this, null, t, e);
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
(k(Ue, "id", "bar"),
    k(Ue, "defaults", {
        borderSkipped: "start",
        borderWidth: 0,
        borderRadius: 0,
        inflateAmount: "auto",
        pointStyle: void 0,
    }),
    k(Ue, "defaultRoutes", {
        backgroundColor: "backgroundColor",
        borderColor: "borderColor",
    }));
const Vd = Object.freeze({
    __proto__: null,
    ArcElement: pe,
    BarElement: Ue,
    LineElement: Lt,
    PointElement: je,
});
const Jn = [
    "rgb(54, 162, 235)",
    "rgb(255, 99, 132)",
    "rgb(255, 159, 64)",
    "rgb(255, 205, 86)",
    "rgb(75, 192, 192)",
    "rgb(153, 102, 255)",
    "rgb(201, 203, 207)",
];
const oa = Jn.map((s) => s.replace("rgb(", "rgba(").replace(")", ", 0.5)"));
function Ha(s) {
    return Jn[s % Jn.length];
}
function $a(s) {
    return oa[s % oa.length];
}
function Wd(s, t) {
    return ((s.borderColor = Ha(t)), (s.backgroundColor = $a(t)), ++t);
}
function Bd(s, t) {
    return ((s.backgroundColor = s.data.map(() => Ha(t++))), t);
}
function Hd(s, t) {
    return ((s.backgroundColor = s.data.map(() => $a(t++))), t);
}
function $d(s) {
    let t = 0;
    return (e, i) => {
        const n = s.getDatasetMeta(i).controller;
        n instanceof jt
            ? (t = Bd(e, t))
            : n instanceof ye
              ? (t = Hd(e, t))
              : n && (t = Wd(e, t));
    };
}
function ra(s) {
    let t;
    for (t in s) if (s[t].borderColor || s[t].backgroundColor) return !0;
    return !1;
}
function jd(s) {
    return s && (s.borderColor || s.backgroundColor);
}
function Ud() {
    return (
        j.borderColor !== "rgba(0,0,0,0.1)" ||
        j.backgroundColor !== "rgba(0,0,0,0.1)"
    );
}
const Yd = {
    id: "colors",
    defaults: { enabled: !0, forceOverride: !1 },
    beforeLayout(s, t, e) {
        if (!e.enabled) return;
        const {
            data: { datasets: i },
            options: n,
        } = s.config;
        const { elements: o } = n;
        const r = ra(i) || jd(n) || (o && ra(o)) || Ud();
        if (!e.forceOverride && r) return;
        const a = $d(s);
        i.forEach(a);
    },
};
function Zd(s, t, e, i, n) {
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
    let g;
    let m;
    for (r[l++] = s[h], u = 0; u < o - 2; u++) {
        let p = 0;
        let b = 0;
        let y;
        const _ = Math.floor((u + 1) * a) + 1 + t;
        const w = Math.min(Math.floor((u + 2) * a) + 1, e) + t;
        const x = w - _;
        for (y = _; y < w; y++) ((p += s[y].x), (b += s[y].y));
        ((p /= x), (b /= x));
        const v = Math.floor(u * a) + 1 + t;
        const S = Math.min(Math.floor((u + 1) * a) + 1, e) + t;
        const { x: M, y: T } = s[h];
        for (f = g = -1, y = v; y < S; y++) {
            ((g =
                0.5 *
                Math.abs((M - p) * (s[y].y - T) - (M - s[y].x) * (b - T))),
                g > f && ((f = g), (d = s[y]), (m = y)));
        }
        ((r[l++] = d), (h = m));
    }
    return ((r[l++] = s[c]), r);
}
function qd(s, t, e, i) {
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
    let g;
    let m;
    const p = [];
    const b = t + e - 1;
    const y = s[t].x;
    const w = s[b].x - y;
    for (r = t; r < t + e; ++r) {
        ((a = s[r]), (l = ((a.x - y) / w) * i), (c = a.y));
        const x = l | 0;
        if (x === h) {
            (c < g ? ((g = c), (u = r)) : c > m && ((m = c), (d = r)),
                (n = (o * n + a.x) / ++o));
        } else {
            const v = r - 1;
            if (!A(u) && !A(d)) {
                const S = Math.min(u, d);
                const M = Math.max(u, d);
                (S !== f && S !== v && p.push({ ...s[S], x: n }),
                    M !== f && M !== v && p.push({ ...s[M], x: n }));
            }
            (r > 0 && v !== f && p.push(s[v]),
                p.push(a),
                (h = x),
                (o = 0),
                (g = m = c),
                (u = d = f = r));
        }
    }
    return p;
}
function ja(s) {
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
function aa(s) {
    s.data.datasets.forEach((t) => {
        ja(t);
    });
}
function Gd(s, t) {
    const e = t.length;
    let i = 0;
    let n;
    const { iScale: o } = s;
    const { min: r, max: a, minDefined: l, maxDefined: c } = o.getUserBounds();
    return (
        l && (i = J(Dt(t, o.axis, r).lo, 0, e - 1)),
        c ? (n = J(Dt(t, o.axis, a).hi + 1, i, e) - i) : (n = e - i),
        { start: i, count: n }
    );
}
const Xd = {
    id: "decimation",
    defaults: { algorithm: "min-max", enabled: !1 },
    beforeElementsUpdate: (s, t, e) => {
        if (!e.enabled) {
            aa(s);
            return;
        }
        const i = s.width;
        s.data.datasets.forEach((n, o) => {
            const { _data: r, indexAxis: a } = n;
            const l = s.getDatasetMeta(o);
            const c = r || n.data;
            if (
                Ne([a, s.options.indexAxis]) === "y" ||
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
            const { start: u, count: d } = Gd(l, c);
            const f = e.threshold || 4 * i;
            if (d <= f) {
                ja(n);
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
                    set: function (m) {
                        this._data = m;
                    },
                }));
            let g;
            switch (e.algorithm) {
                case "lttb":
                    g = Zd(c, u, d, i, e);
                    break;
                case "min-max":
                    g = qd(c, u, d, i);
                    break;
                default:
                    throw new Error(
                        `Unsupported decimation algorithm '${e.algorithm}'`,
                    );
            }
            n._decimated = g;
        });
    },
    destroy(s) {
        aa(s);
    },
};
function Kd(s, t, e) {
    const i = s.segments;
    const n = s.points;
    const o = t.points;
    const r = [];
    for (const a of i) {
        let { start: l, end: c } = a;
        c = Oi(l, c, n);
        const h = Qn(e, n[l], n[c], a.loop);
        if (!t.segments) {
            r.push({ source: a, target: h, start: n[l], end: n[c] });
            continue;
        }
        const u = Dn(t, h);
        for (const d of u) {
            const f = Qn(e, o[d.start], o[d.end], d.loop);
            const g = Tn(a, n, f);
            for (const m of g) {
                r.push({
                    source: m,
                    target: d,
                    start: { [e]: la(h, f, "start", Math.max) },
                    end: { [e]: la(h, f, "end", Math.min) },
                });
            }
        }
    }
    return r;
}
function Qn(s, t, e, i) {
    if (i) return;
    let n = t[s];
    let o = e[s];
    return (
        s === "angle" && ((n = st(n)), (o = st(o))),
        { property: s, start: n, end: o }
    );
}
function Jd(s, t) {
    const { x: e = null, y: i = null } = s || {};
    const n = t.points;
    const o = [];
    return (
        t.segments.forEach(({ start: r, end: a }) => {
            a = Oi(r, a, n);
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
function Oi(s, t, e) {
    for (; t > s; t--) {
        const i = e[t];
        if (!isNaN(i.x) && !isNaN(i.y)) break;
    }
    return t;
}
function la(s, t, e, i) {
    return s && t ? i(s[e], t[e]) : s ? s[e] : t ? t[e] : 0;
}
function Ua(s, t) {
    let e = [];
    let i = !1;
    return (
        H(s) ? ((i = !0), (e = s)) : (e = Jd(s, t)),
        e.length
            ? new Lt({
                  points: e,
                  options: { tension: 0 },
                  _loop: i,
                  _fullLoop: i,
              })
            : null
    );
}
function ca(s) {
    return s && s.fill !== !1;
}
function Qd(s, t, e) {
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
function tf(s, t, e) {
    const i = of(s);
    if (E(i)) return isNaN(i.value) ? !1 : i;
    const n = parseFloat(i);
    return Z(n) && Math.floor(n) === n
        ? ef(i[0], t, n, e)
        : ["origin", "start", "end", "stack", "shape"].indexOf(i) >= 0 && i;
}
function ef(s, t, e, i) {
    return (
        (s === "-" || s === "+") && (e = t + e),
        e === t || e < 0 || e >= i ? !1 : e
    );
}
function sf(s, t) {
    let e = null;
    return (
        s === "start"
            ? (e = t.bottom)
            : s === "end"
              ? (e = t.top)
              : E(s)
                ? (e = t.getPixelForValue(s.value))
                : t.getBasePixel && (e = t.getBasePixel()),
        e
    );
}
function nf(s, t, e) {
    let i;
    return (
        s === "start"
            ? (i = e)
            : s === "end"
              ? (i = t.options.reverse ? t.min : t.max)
              : E(s)
                ? (i = s.value)
                : (i = t.getBaseValue()),
        i
    );
}
function of(s) {
    const t = s.options;
    const e = t.fill;
    let i = P(e && e.target, e);
    return (
        i === void 0 && (i = !!t.backgroundColor),
        i === !1 || i === null ? !1 : i === !0 ? "origin" : i
    );
}
function rf(s) {
    const { scale: t, index: e, line: i } = s;
    const n = [];
    const o = i.segments;
    const r = i.points;
    const a = af(t, e);
    a.push(Ua({ x: null, y: t.bottom }, i));
    for (let l = 0; l < o.length; l++) {
        const c = o[l];
        for (let h = c.start; h <= c.end; h++) lf(n, r[h], a);
    }
    return new Lt({ points: n, options: {} });
}
function af(s, t) {
    const e = [];
    const i = s.getMatchingVisibleMetas("line");
    for (let n = 0; n < i.length; n++) {
        const o = i[n];
        if (o.index === t) break;
        o.hidden || e.unshift(o.dataset);
    }
    return e;
}
function lf(s, t, e) {
    const i = [];
    for (let n = 0; n < e.length; n++) {
        const o = e[n];
        const { first: r, last: a, point: l } = cf(o, t, "x");
        if (!(!l || (r && a))) {
            if (r) i.unshift(l);
            else if ((s.push(l), !a)) break;
        }
    }
    s.push(...i);
}
function cf(s, t, e) {
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
        if (It(n, u, d)) {
            ((a = n === u), (l = n === d));
            break;
        }
    }
    return { first: a, last: l, point: i };
}
const Si = class {
    constructor(t) {
        ((this.x = t.x), (this.y = t.y), (this.radius = t.radius));
    }

    pathSegment(t, e, i) {
        const { x: n, y: o, radius: r } = this;
        return (
            (e = e || { start: 0, end: $ }),
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
function hf(s) {
    const { chart: t, fill: e, line: i } = s;
    if (Z(e)) return uf(t, e);
    if (e === "stack") return rf(s);
    if (e === "shape") return !0;
    const n = df(s);
    return n instanceof Si ? n : Ua(n, i);
}
function uf(s, t) {
    const e = s.getDatasetMeta(t);
    return e && s.isDatasetVisible(t) ? e.dataset : null;
}
function df(s) {
    return (s.scale || {}).getPointPositionForValue ? gf(s) : ff(s);
}
function ff(s) {
    const { scale: t = {}, fill: e } = s;
    const i = sf(e, t);
    if (Z(i)) {
        const n = t.isHorizontal();
        return { x: n ? i : null, y: n ? null : i };
    }
    return null;
}
function gf(s) {
    const { scale: t, fill: e } = s;
    const i = t.options;
    const n = t.getLabels().length;
    const o = i.reverse ? t.max : t.min;
    const r = nf(e, t, o);
    const a = [];
    if (i.grid.circular) {
        const l = t.getPointPositionForValue(0, o);
        return new Si({
            x: l.x,
            y: l.y,
            radius: t.getDistanceFromCenterForValue(r),
        });
    }
    for (let l = 0; l < n; ++l) a.push(t.getPointPositionForValue(l, r));
    return a;
}
function Vn(s, t, e) {
    const i = hf(t);
    const { chart: n, index: o, line: r, scale: a, axis: l } = t;
    const c = r.options;
    const h = c.fill;
    const u = c.backgroundColor;
    const { above: d = u, below: f = u } = h || {};
    const g = n.getDatasetMeta(o);
    const m = Cn(n, g);
    i &&
        r.points.length &&
        (ms(s, e),
        mf(s, {
            line: r,
            target: i,
            above: d,
            below: f,
            area: e,
            scale: a,
            axis: l,
            clip: m,
        }),
        ps(s));
}
function mf(s, t) {
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
            ? (ha(s, i, r.top),
              Wn(s, {
                  line: e,
                  target: i,
                  color: n,
                  scale: a,
                  property: c,
                  clip: l,
              }),
              s.restore(),
              s.save(),
              ha(s, i, r.bottom))
            : c === "y" &&
              (ua(s, i, r.left),
              Wn(s, {
                  line: e,
                  target: i,
                  color: o,
                  scale: a,
                  property: c,
                  clip: l,
              }),
              s.restore(),
              s.save(),
              ua(s, i, r.right),
              (h = n))),
        Wn(s, { line: e, target: i, color: h, scale: a, property: c, clip: l }),
        s.restore());
}
function ha(s, t, e) {
    const { segments: i, points: n } = t;
    let o = !0;
    let r = !1;
    s.beginPath();
    for (const a of i) {
        const { start: l, end: c } = a;
        const h = n[l];
        const u = n[Oi(l, c, n)];
        (o
            ? (s.moveTo(h.x, h.y), (o = !1))
            : (s.lineTo(h.x, e), s.lineTo(h.x, h.y)),
            (r = !!t.pathSegment(s, a, { move: r })),
            r ? s.closePath() : s.lineTo(u.x, e));
    }
    (s.lineTo(t.first().x, e), s.closePath(), s.clip());
}
function ua(s, t, e) {
    const { segments: i, points: n } = t;
    let o = !0;
    let r = !1;
    s.beginPath();
    for (const a of i) {
        const { start: l, end: c } = a;
        const h = n[l];
        const u = n[Oi(l, c, n)];
        (o
            ? (s.moveTo(h.x, h.y), (o = !1))
            : (s.lineTo(e, h.y), s.lineTo(h.x, h.y)),
            (r = !!t.pathSegment(s, a, { move: r })),
            r ? s.closePath() : s.lineTo(e, u.y));
    }
    (s.lineTo(e, t.first().y), s.closePath(), s.clip());
}
function Wn(s, t) {
    const { line: e, target: i, property: n, color: o, scale: r, clip: a } = t;
    const l = Kd(e, i, n);
    for (const { source: c, target: h, start: u, end: d } of l) {
        const { style: { backgroundColor: f = o } = {} } = c;
        const g = i !== !0;
        (s.save(),
            (s.fillStyle = f),
            pf(s, r, a, g && Qn(n, u, d)),
            s.beginPath());
        const m = !!e.pathSegment(s, c);
        let p;
        if (g) {
            m ? s.closePath() : da(s, i, d, n);
            const b = !!i.pathSegment(s, h, { move: m, reverse: !0 });
            ((p = m && b), p || da(s, i, u, n));
        }
        (s.closePath(), s.fill(p ? "evenodd" : "nonzero"), s.restore());
    }
}
function pf(s, t, e, i) {
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
function da(s, t, e, i) {
    const n = t.interpolate(e, i);
    n && s.lineTo(n.x, n.y);
}
const bf = {
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
                    a instanceof Lt &&
                    (l = {
                        visible: s.isDatasetVisible(r),
                        index: r,
                        fill: tf(a, r, i),
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
                !(!l || l.fill === !1) && (l.fill = Qd(n, r, e.propagate)));
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
                i && a.fill && Vn(s.ctx, a, o));
        }
    },
    beforeDatasetsDraw(s, t, e) {
        if (e.drawTime !== "beforeDatasetsDraw") return;
        const i = s.getSortedVisibleDatasetMetas();
        for (let n = i.length - 1; n >= 0; --n) {
            const o = i[n].$filler;
            ca(o) && Vn(s.ctx, o, s.chartArea);
        }
    },
    beforeDatasetDraw(s, t, e) {
        const i = t.meta.$filler;
        !ca(i) ||
            e.drawTime !== "beforeDatasetDraw" ||
            Vn(s.ctx, i, s.chartArea);
    },
    defaults: { propagate: !0, drawTime: "beforeDatasetDraw" },
};
const fa = (s, t) => {
    let { boxHeight: e = t, boxWidth: i = t } = s;
    return (
        s.usePointStyle &&
            ((e = Math.min(e, t)), (i = s.pointStyleWidth || Math.min(i, t))),
        { boxWidth: i, boxHeight: e, itemHeight: Math.max(t, e) }
    );
};
const yf = (s, t) =>
    s !== null &&
    t !== null &&
    s.datasetIndex === t.datasetIndex &&
    s.index === t.index;
const Mi = class extends dt {
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
        let e = W(t.generateLabels, [this.chart], this) || [];
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
        const n = X(i.font);
        const o = n.size;
        const r = this._computeTitleHeight();
        const { boxWidth: a, itemHeight: l } = fa(i, o);
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
            this.legendItems.forEach((g, m) => {
                const p = i + e / 2 + o.measureText(g.text).width;
                ((m === 0 || c[c.length - 1] + p + 2 * a > r) &&
                    ((u += h),
                    (c[c.length - (m > 0 ? 0 : 1)] = 0),
                    (f += h),
                    d++),
                    (l[m] = { left: 0, top: f, row: d, width: p, height: n }),
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
        let g = 0;
        let m = 0;
        return (
            this.legendItems.forEach((p, b) => {
                const { itemWidth: y, itemHeight: _ } = xf(i, e, o, p, n);
                (b > 0 &&
                    f + _ + 2 * a > h &&
                    ((u += d + a),
                    c.push({ width: d, height: f }),
                    (g += d + a),
                    m++,
                    (d = f = 0)),
                    (l[b] = { left: g, top: f, col: m, width: y, height: _ }),
                    (d = Math.max(d, y)),
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
        const r = fe(o, this.left, this.width);
        if (this.isHorizontal()) {
            let a = 0;
            let l = it(i, this.left + n, this.right - this.lineWidths[a]);
            for (const c of e) {
                (a !== c.row &&
                    ((a = c.row),
                    (l = it(
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
            let l = it(
                i,
                this.top + t + n,
                this.bottom - this.columnSizes[a].height,
            );
            for (const c of e) {
                (c.col !== a &&
                    ((a = c.col),
                    (l = it(
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
            (ms(t, this), this._draw(), ps(t));
        }
    }

    _draw() {
        const { options: t, columnSizes: e, lineWidths: i, ctx: n } = this;
        const { align: o, labels: r } = t;
        const a = j.color;
        const l = fe(t.rtl, this.left, this.width);
        const c = X(r.font);
        const { padding: h } = r;
        const u = c.size;
        const d = u / 2;
        let f;
        (this.drawTitle(),
            (n.textAlign = l.textAlign("left")),
            (n.textBaseline = "middle"),
            (n.lineWidth = 0.5),
            (n.font = c.string));
        const { boxWidth: g, boxHeight: m, itemHeight: p } = fa(r, u);
        const b = function (v, S, M) {
            if (isNaN(g) || g <= 0 || isNaN(m) || m < 0) return;
            n.save();
            const T = P(M.lineWidth, 1);
            if (
                ((n.fillStyle = P(M.fillStyle, a)),
                (n.lineCap = P(M.lineCap, "butt")),
                (n.lineDashOffset = P(M.lineDashOffset, 0)),
                (n.lineJoin = P(M.lineJoin, "miter")),
                (n.lineWidth = T),
                (n.strokeStyle = P(M.strokeStyle, a)),
                n.setLineDash(P(M.lineDash, [])),
                r.usePointStyle)
            ) {
                const C = {
                    radius: (m * Math.SQRT2) / 2,
                    pointStyle: M.pointStyle,
                    rotation: M.rotation,
                    borderWidth: T,
                };
                const I = l.xPlus(v, g / 2);
                const L = S + d;
                bn(n, C, I, L, r.pointStyleWidth && g);
            } else {
                const C = S + Math.max((u - m) / 2, 0);
                const I = l.leftForLtr(v, g);
                const L = Qt(M.borderRadius);
                (n.beginPath(),
                    Object.values(L).some((et) => et !== 0)
                        ? Re(n, { x: I, y: C, w: g, h: m, radius: L })
                        : n.rect(I, C, g, m),
                    n.fill(),
                    T !== 0 && n.stroke());
            }
            n.restore();
        };
        const y = function (v, S, M) {
            Jt(n, M.text, v, S + p / 2, c, {
                strikethrough: M.hidden,
                textAlign: l.textAlign(M.textAlign),
            });
        };
        const _ = this.isHorizontal();
        const w = this._computeTitleHeight();
        (_
            ? (f = {
                  x: it(o, this.left + h, this.right - i[0]),
                  y: this.top + h + w,
                  line: 0,
              })
            : (f = {
                  x: this.left + h,
                  y: it(o, this.top + w + h, this.bottom - e[0].height),
                  line: 0,
              }),
            Mn(this.ctx, t.textDirection));
        const x = p + h;
        (this.legendItems.forEach((v, S) => {
            ((n.strokeStyle = v.fontColor), (n.fillStyle = v.fontColor));
            const M = n.measureText(v.text).width;
            const T = l.textAlign(v.textAlign || (v.textAlign = r.textAlign));
            const C = g + d + M;
            let I = f.x;
            let L = f.y;
            (l.setWidth(this.width),
                _
                    ? S > 0 &&
                      I + C + h > this.right &&
                      ((L = f.y += x),
                      f.line++,
                      (I = f.x = it(o, this.left + h, this.right - i[f.line])))
                    : S > 0 &&
                      L + x > this.bottom &&
                      ((I = f.x = I + e[f.line].width + h),
                      f.line++,
                      (L = f.y =
                          it(
                              o,
                              this.top + w + h,
                              this.bottom - e[f.line].height,
                          ))));
            const et = l.x(I);
            if (
                (b(et, L, v),
                (I = fr(T, I + g + d, _ ? I + C : this.right, t.rtl)),
                y(l.x(I), L, v),
                _)
            ) {
                f.x += C + h;
            } else if (typeof v.text !== "string") {
                const ht = c.lineHeight;
                f.y += Ya(v, ht) + h;
            } else f.y += x;
        }),
            On(this.ctx, t.textDirection));
    }

    drawTitle() {
        const t = this.options;
        const e = t.title;
        const i = X(e.font);
        const n = nt(e.padding);
        if (!e.display) return;
        const o = fe(t.rtl, this.left, this.width);
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
                (u = it(t.align, u, this.right - d)));
        } else {
            const g = this.columnSizes.reduce(
                (m, p) => Math.max(m, p.height),
                0,
            );
            h =
                c +
                it(
                    t.align,
                    this.top,
                    this.bottom -
                        g -
                        t.labels.padding -
                        this._computeTitleHeight(),
                );
        }
        const f = it(a, u, u + d);
        ((r.textAlign = o.textAlign(ni(a))),
            (r.textBaseline = "middle"),
            (r.strokeStyle = e.color),
            (r.fillStyle = e.color),
            (r.font = i.string),
            Jt(r, e.text, f, h, i));
    }

    _computeTitleHeight() {
        const t = this.options.title;
        const e = X(t.font);
        const i = nt(t.padding);
        return t.display ? e.lineHeight + i.height : 0;
    }

    _getLegendItemAt(t, e) {
        let i, n, o;
        if (It(t, this.left, this.right) && It(e, this.top, this.bottom)) {
            for (o = this.legendHitBoxes, i = 0; i < o.length; ++i) {
                if (
                    ((n = o[i]),
                    It(t, n.left, n.left + n.width) &&
                        It(e, n.top, n.top + n.height))
                ) {
                    return this.legendItems[i];
                }
            }
        }
        return null;
    }

    handleEvent(t) {
        const e = this.options;
        if (!kf(t.type, e)) return;
        const i = this._getLegendItemAt(t.x, t.y);
        if (t.type === "mousemove" || t.type === "mouseout") {
            const n = this._hoveredItem;
            const o = yf(n, i);
            (n && !o && W(e.onLeave, [t, n, this], this),
                (this._hoveredItem = i),
                i && !o && W(e.onHover, [t, i, this], this));
        } else i && W(e.onClick, [t, i, this], this);
    }
};
function xf(s, t, e, i, n) {
    const o = _f(i, s, t, e);
    const r = wf(n, i, t.lineHeight);
    return { itemWidth: o, itemHeight: r };
}
function _f(s, t, e, i) {
    let n = s.text;
    return (
        n &&
            typeof n !== "string" &&
            (n = n.reduce((o, r) => (o.length > r.length ? o : r))),
        t + e.size / 2 + i.measureText(n).width
    );
}
function wf(s, t, e) {
    let i = s;
    return (typeof t.text !== "string" && (i = Ya(t, e)), i);
}
function Ya(s, t) {
    const e = s.text ? s.text.length : 0;
    return t * e;
}
function kf(s, t) {
    return !!(
        ((s === "mousemove" || s === "mouseout") && (t.onHover || t.onLeave)) ||
        (t.onClick && (s === "click" || s === "mouseup"))
    );
}
const vf = {
    id: "legend",
    _element: Mi,
    start(s, t, e) {
        const i = (s.legend = new Mi({ ctx: s.ctx, options: e, chart: s }));
        (rt.configure(s, i, e), rt.addBox(s, i));
    },
    stop(s) {
        (rt.removeBox(s, s.legend), delete s.legend);
    },
    beforeUpdate(s, t, e) {
        const i = s.legend;
        (rt.configure(s, i, e), (i.options = e));
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
                    const h = nt(c.borderWidth);
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
const Is = class extends dt {
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
        const n = H(i.text) ? i.text.length : 1;
        this._padding = nt(i.padding);
        const o = n * X(i.font).lineHeight + this._padding.height;
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
                ? ((h = it(a, i, o)), (u = e + t), (c = o - i))
                : (r.position === "left"
                      ? ((h = i + t), (u = it(a, n, e)), (l = F * -0.5))
                      : ((h = o - t), (u = it(a, e, n)), (l = F * 0.5)),
                  (c = n - e)),
            { titleX: h, titleY: u, maxWidth: c, rotation: l }
        );
    }

    draw() {
        const t = this.ctx;
        const e = this.options;
        if (!e.display) return;
        const i = X(e.font);
        const o = i.lineHeight / 2 + this._padding.top;
        const {
            titleX: r,
            titleY: a,
            maxWidth: l,
            rotation: c,
        } = this._drawArgs(o);
        Jt(t, e.text, 0, 0, i, {
            color: e.color,
            maxWidth: l,
            rotation: c,
            textAlign: ni(e.align),
            textBaseline: "middle",
            translation: [r, a],
        });
    }
};
function Sf(s, t) {
    const e = new Is({ ctx: s.ctx, options: t, chart: s });
    (rt.configure(s, e, t), rt.addBox(s, e), (s.titleBlock = e));
}
const Mf = {
    id: "title",
    _element: Is,
    start(s, t, e) {
        Sf(s, e);
    },
    stop(s) {
        const t = s.titleBlock;
        (rt.removeBox(s, t), delete s.titleBlock);
    },
    beforeUpdate(s, t, e) {
        const i = s.titleBlock;
        (rt.configure(s, i, e), (i.options = e));
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
const Of = {
    id: "subtitle",
    start(s, t, e) {
        const i = new Is({ ctx: s.ctx, options: e, chart: s });
        (rt.configure(s, i, e), rt.addBox(s, i), mi.set(s, i));
    },
    stop(s) {
        (rt.removeBox(s, mi.get(s)), mi.delete(s));
    },
    beforeUpdate(s, t, e) {
        const i = mi.get(s);
        (rt.configure(s, i, e), (i.options = e));
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
const Ss = {
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
                const h = Qs(t, c);
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
function At(s, t) {
    return (t && (H(t) ? Array.prototype.push.apply(s, t) : s.push(t)), s);
}
function $t(s) {
    return (typeof s === "string" || s instanceof String) &&
        s.indexOf(`
`) > -1
        ? s.split(`
`)
        : s;
}
function Tf(s, t) {
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
function ga(s, t) {
    const e = s.chart.ctx;
    const { body: i, footer: n, title: o } = s;
    const { boxWidth: r, boxHeight: a } = t;
    const l = X(t.bodyFont);
    const c = X(t.titleFont);
    const h = X(t.footerFont);
    const u = o.length;
    const d = n.length;
    const f = i.length;
    const g = nt(t.padding);
    let m = g.height;
    let p = 0;
    let b = i.reduce(
        (w, x) => w + x.before.length + x.lines.length + x.after.length,
        0,
    );
    if (
        ((b += s.beforeBody.length + s.afterBody.length),
        u &&
            (m +=
                u * c.lineHeight +
                (u - 1) * t.titleSpacing +
                t.titleMarginBottom),
        b)
    ) {
        const w = t.displayColors ? Math.max(a, l.lineHeight) : l.lineHeight;
        m += f * w + (b - f) * l.lineHeight + (b - 1) * t.bodySpacing;
    }
    d &&
        (m += t.footerMarginTop + d * h.lineHeight + (d - 1) * t.footerSpacing);
    let y = 0;
    const _ = function (w) {
        p = Math.max(p, e.measureText(w).width + y);
    };
    return (
        e.save(),
        (e.font = c.string),
        z(s.title, _),
        (e.font = l.string),
        z(s.beforeBody.concat(s.afterBody), _),
        (y = t.displayColors ? r + 2 + t.boxPadding : 0),
        z(i, (w) => {
            (z(w.before, _), z(w.lines, _), z(w.after, _));
        }),
        (y = 0),
        (e.font = h.string),
        z(s.footer, _),
        e.restore(),
        (p += g.width),
        { width: p, height: m }
    );
}
function Df(s, t) {
    const { y: e, height: i } = t;
    return e < i / 2 ? "top" : e > s.height - i / 2 ? "bottom" : "center";
}
function Cf(s, t, e, i) {
    const { x: n, width: o } = i;
    const r = e.caretSize + e.caretPadding;
    if (
        (s === "left" && n + o + r > t.width) ||
        (s === "right" && n - o - r < 0)
    ) {
        return !0;
    }
}
function Pf(s, t, e, i) {
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
        Cf(c, s, t, e) && (c = "center"),
        c
    );
}
function ma(s, t, e) {
    const i = e.yAlign || t.yAlign || Df(s, e);
    return { xAlign: e.xAlign || t.xAlign || Pf(s, t, e, i), yAlign: i };
}
function If(s, t) {
    let { x: e, width: i } = s;
    return (t === "right" ? (e -= i) : t === "center" && (e -= i / 2), e);
}
function Af(s, t, e) {
    let { y: i, height: n } = s;
    return (
        t === "top" ? (i += e) : t === "bottom" ? (i -= n + e) : (i -= n / 2),
        i
    );
}
function pa(s, t, e, i) {
    const { caretSize: n, caretPadding: o, cornerRadius: r } = s;
    const { xAlign: a, yAlign: l } = e;
    const c = n + o;
    const { topLeft: h, topRight: u, bottomLeft: d, bottomRight: f } = Qt(r);
    let g = If(t, a);
    const m = Af(t, l, c);
    return (
        l === "center"
            ? a === "left"
                ? (g += c)
                : a === "right" && (g -= c)
            : a === "left"
              ? (g -= Math.max(h, d) + n)
              : a === "right" && (g += Math.max(u, f) + n),
        { x: J(g, 0, i.width - t.width), y: J(m, 0, i.height - t.height) }
    );
}
function pi(s, t, e) {
    const i = nt(e.padding);
    return t === "center"
        ? s.x + s.width / 2
        : t === "right"
          ? s.x + s.width - i.right
          : s.x + i.left;
}
function ba(s) {
    return At([], $t(s));
}
function Ef(s, t, e) {
    return Bt(s, { tooltip: t, tooltipItems: e, type: "tooltip" });
}
function ya(s, t) {
    const e =
        t && t.dataset && t.dataset.tooltip && t.dataset.tooltip.callbacks;
    return e ? s.override(e) : s;
}
const Za = {
    beforeTitle: Pt,
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
    afterTitle: Pt,
    beforeBody: Pt,
    beforeLabel: Pt,
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
    afterLabel: Pt,
    afterBody: Pt,
    beforeFooter: Pt,
    footer: Pt,
    afterFooter: Pt,
};
function lt(s, t, e, i) {
    const n = s[t].call(e, i);
    return typeof n > "u" ? Za[t].call(e, i) : n;
}
const Cs = class extends dt {
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
        const o = new _i(this.chart, n);
        return (n._cacheable && (this._cachedAnimations = Object.freeze(o)), o);
    }

    getContext() {
        return (
            this.$context ||
            (this.$context = Ef(
                this.chart.getContext(),
                this,
                this._tooltipItems,
            ))
        );
    }

    getTitle(t, e) {
        const { callbacks: i } = e;
        const n = lt(i, "beforeTitle", this, t);
        const o = lt(i, "title", this, t);
        const r = lt(i, "afterTitle", this, t);
        let a = [];
        return ((a = At(a, $t(n))), (a = At(a, $t(o))), (a = At(a, $t(r))), a);
    }

    getBeforeBody(t, e) {
        return ba(lt(e.callbacks, "beforeBody", this, t));
    }

    getBody(t, e) {
        const { callbacks: i } = e;
        const n = [];
        return (
            z(t, (o) => {
                const r = { before: [], lines: [], after: [] };
                const a = ya(i, o);
                (At(r.before, $t(lt(a, "beforeLabel", this, o))),
                    At(r.lines, lt(a, "label", this, o)),
                    At(r.after, $t(lt(a, "afterLabel", this, o))),
                    n.push(r));
            }),
            n
        );
    }

    getAfterBody(t, e) {
        return ba(lt(e.callbacks, "afterBody", this, t));
    }

    getFooter(t, e) {
        const { callbacks: i } = e;
        const n = lt(i, "beforeFooter", this, t);
        const o = lt(i, "footer", this, t);
        const r = lt(i, "afterFooter", this, t);
        let a = [];
        return ((a = At(a, $t(n))), (a = At(a, $t(o))), (a = At(a, $t(r))), a);
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
        for (l = 0, c = e.length; l < c; ++l) a.push(Tf(this.chart, e[l]));
        return (
            t.filter && (a = a.filter((h, u, d) => t.filter(h, u, d, i))),
            t.itemSort && (a = a.sort((h, u) => t.itemSort(h, u, i))),
            z(a, (h) => {
                const u = ya(t.callbacks, h);
                (n.push(lt(u, "labelColor", this, h)),
                    o.push(lt(u, "labelPointStyle", this, h)),
                    r.push(lt(u, "labelTextColor", this, h)));
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
            const a = Ss[i.position].call(this, n, this._eventPosition);
            ((r = this._createItems(i)),
                (this.title = this.getTitle(r, i)),
                (this.beforeBody = this.getBeforeBody(r, i)),
                (this.body = this.getBody(r, i)),
                (this.afterBody = this.getAfterBody(r, i)),
                (this.footer = this.getFooter(r, i)));
            const l = (this._size = ga(this, i));
            const c = Object.assign({}, a, l);
            const h = ma(this.chart, i, c);
            const u = pa(i, c, h, this.chart);
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
        } = Qt(a);
        const { x: d, y: f } = t;
        const { width: g, height: m } = e;
        let p;
        let b;
        let y;
        let _;
        let w;
        let x;
        return (
            o === "center"
                ? ((w = f + m / 2),
                  n === "left"
                      ? ((p = d), (b = p - r), (_ = w + r), (x = w - r))
                      : ((p = d + g), (b = p + r), (_ = w - r), (x = w + r)),
                  (y = p))
                : (n === "left"
                      ? (b = d + Math.max(l, h) + r)
                      : n === "right"
                        ? (b = d + g - Math.max(c, u) - r)
                        : (b = this.caretX),
                  o === "top"
                      ? ((_ = f), (w = _ - r), (p = b - r), (y = b + r))
                      : ((_ = f + m), (w = _ + r), (p = b + r), (y = b - r)),
                  (x = _)),
            { x1: p, x2: b, x3: y, y1: _, y2: w, y3: x }
        );
    }

    drawTitle(t, e, i) {
        const n = this.title;
        const o = n.length;
        let r;
        let a;
        let l;
        if (o) {
            const c = fe(i.rtl, this.x, this.width);
            for (
                t.x = pi(this, i.titleAlign, i),
                    e.textAlign = c.textAlign(i.titleAlign),
                    e.textBaseline = "middle",
                    r = X(i.titleFont),
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
        const h = X(o.bodyFont);
        const u = pi(this, "left", o);
        const d = n.x(u);
        const f = l < h.lineHeight ? (h.lineHeight - l) / 2 : 0;
        const g = e.y + f;
        if (o.usePointStyle) {
            const m = {
                radius: Math.min(c, l) / 2,
                pointStyle: a.pointStyle,
                rotation: a.rotation,
                borderWidth: 1,
            };
            const p = n.leftForLtr(d, c) + c / 2;
            const b = g + l / 2;
            ((t.strokeStyle = o.multiKeyBackground),
                (t.fillStyle = o.multiKeyBackground),
                ri(t, m, p, b),
                (t.strokeStyle = r.borderColor),
                (t.fillStyle = r.backgroundColor),
                ri(t, m, p, b));
        } else {
            ((t.lineWidth = E(r.borderWidth)
                ? Math.max(...Object.values(r.borderWidth))
                : r.borderWidth || 1),
                (t.strokeStyle = r.borderColor),
                t.setLineDash(r.borderDash || []),
                (t.lineDashOffset = r.borderDashOffset || 0));
            const m = n.leftForLtr(d, c);
            const p = n.leftForLtr(n.xPlus(d, 1), c - 2);
            const b = Qt(r.borderRadius);
            Object.values(b).some((y) => y !== 0)
                ? (t.beginPath(),
                  (t.fillStyle = o.multiKeyBackground),
                  Re(t, { x: m, y: g, w: c, h: l, radius: b }),
                  t.fill(),
                  t.stroke(),
                  (t.fillStyle = r.backgroundColor),
                  t.beginPath(),
                  Re(t, { x: p, y: g + 1, w: c - 2, h: l - 2, radius: b }),
                  t.fill())
                : ((t.fillStyle = o.multiKeyBackground),
                  t.fillRect(m, g, c, l),
                  t.strokeRect(m, g, c, l),
                  (t.fillStyle = r.backgroundColor),
                  t.fillRect(p, g + 1, c - 2, l - 2));
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
        const u = X(i.bodyFont);
        let d = u.lineHeight;
        let f = 0;
        const g = fe(i.rtl, this.x, this.width);
        const m = function (M) {
            (e.fillText(M, g.x(t.x + f), t.y + d / 2), (t.y += d + o));
        };
        const p = g.textAlign(r);
        let b;
        let y;
        let _;
        let w;
        let x;
        let v;
        let S;
        for (
            e.textAlign = r,
                e.textBaseline = "middle",
                e.font = u.string,
                t.x = pi(this, p, i),
                e.fillStyle = i.bodyColor,
                z(this.beforeBody, m),
                f =
                    a && p !== "right"
                        ? r === "center"
                            ? c / 2 + h
                            : c + 2 + h
                        : 0,
                w = 0,
                v = n.length;
            w < v;
            ++w
        ) {
            for (
                b = n[w],
                    y = this.labelTextColors[w],
                    e.fillStyle = y,
                    z(b.before, m),
                    _ = b.lines,
                    a &&
                        _.length &&
                        (this._drawColorBox(e, t, w, g, i),
                        (d = Math.max(u.lineHeight, l))),
                    x = 0,
                    S = _.length;
                x < S;
                ++x
            ) {
                (m(_[x]), (d = u.lineHeight));
            }
            z(b.after, m);
        }
        ((f = 0), (d = u.lineHeight), z(this.afterBody, m), (t.y -= o));
    }

    drawFooter(t, e, i) {
        const n = this.footer;
        const o = n.length;
        let r;
        let a;
        if (o) {
            const l = fe(i.rtl, this.x, this.width);
            for (
                t.x = pi(this, i.footerAlign, i),
                    t.y += i.footerMarginTop,
                    e.textAlign = l.textAlign(i.footerAlign),
                    e.textBaseline = "middle",
                    r = X(i.footerFont),
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
            bottomRight: g,
        } = Qt(n.cornerRadius);
        ((e.fillStyle = n.backgroundColor),
            (e.strokeStyle = n.borderColor),
            (e.lineWidth = n.borderWidth),
            e.beginPath(),
            e.moveTo(a + u, l),
            r === "top" && this.drawCaret(t, e, i, n),
            e.lineTo(a + c - d, l),
            e.quadraticCurveTo(a + c, l, a + c, l + d),
            r === "center" && o === "right" && this.drawCaret(t, e, i, n),
            e.lineTo(a + c, l + h - g),
            e.quadraticCurveTo(a + c, l + h, a + c - g, l + h),
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
            const r = Ss[t.position].call(
                this,
                this._active,
                this._eventPosition,
            );
            if (!r) return;
            const a = (this._size = ga(this, t));
            const l = Object.assign({}, r, this._size);
            const c = ma(e, t, l);
            const h = pa(t, l, c, e);
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
        const r = nt(e.padding);
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
            Mn(t, e.textDirection),
            (o.y += r.top),
            this.drawTitle(o, t, e),
            this.drawBody(o, t, e),
            this.drawFooter(o, t, e),
            On(t, e.textDirection),
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
        const o = !fs(i, n);
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
        const l = e || !fs(r, o) || a;
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
        const r = Ss[o.position].call(this, t, e);
        return r !== !1 && (i !== r.x || n !== r.y);
    }
};
k(Cs, "positioners", Ss);
const Lf = {
    id: "tooltip",
    _element: Cs,
    positioners: Ss,
    afterInit(s, t, e) {
        e && (s.tooltip = new Cs({ chart: s, options: e }));
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
        callbacks: Za,
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
const Ff = Object.freeze({
    __proto__: null,
    Colors: Yd,
    Decimation: Xd,
    Filler: bf,
    Legend: vf,
    SubTitle: Of,
    Title: Mf,
    Tooltip: Lf,
});
const Rf = (s, t, e, i) => (
    typeof t === "string"
        ? ((e = s.push(t) - 1), i.unshift({ index: e, label: t }))
        : isNaN(t) && (e = null),
    e
);
function Nf(s, t, e, i) {
    const n = s.indexOf(t);
    if (n === -1) return Rf(s, t, e, i);
    const o = s.lastIndexOf(t);
    return n !== o ? e : n;
}
const zf = (s, t) => (s === null ? null : J(Math.round(s), 0, t));
function xa(s) {
    const t = this.getLabels();
    return s >= 0 && s < t.length ? t[s] : s;
}
const Ms = class extends _e {
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
                    : Nf(i, t, P(e, t), this._addedLabels)),
            zf(e, i.length - 1)
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
        return xa.call(this, t);
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
(k(Ms, "id", "category"), k(Ms, "defaults", { ticks: { callback: xa } }));
function Vf(s, t) {
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
    const g = h - 1;
    const { min: m, max: p } = t;
    const b = !A(r);
    const y = !A(a);
    const _ = !A(c);
    const w = (p - m) / (u + 1);
    let x = nn((p - m) / g / f) * f;
    let v;
    let S;
    let M;
    let T;
    if (x < 1e-14 && !b && !y) return [{ value: m }, { value: p }];
    ((T = Math.ceil(p / x) - Math.floor(m / x)),
        T > g && (x = nn((T * x) / g / f) * f),
        A(l) || ((v = Math.pow(10, l)), (x = Math.ceil(x * v) / v)),
        n === "ticks"
            ? ((S = Math.floor(m / x) * x), (M = Math.ceil(p / x) * x))
            : ((S = m), (M = p)),
        b && y && o && rr((a - r) / o, x / 1e3)
            ? ((T = Math.round(Math.min((a - r) / x, h))),
              (x = (a - r) / T),
              (S = r),
              (M = a))
            : _
              ? ((S = b ? r : S),
                (M = y ? a : M),
                (T = c - 1),
                (x = (M - S) / T))
              : ((T = (M - S) / x),
                Ee(T, Math.round(T), x / 1e3)
                    ? (T = Math.round(T))
                    : (T = Math.ceil(T))));
    const C = Math.max(rn(x), rn(S));
    ((v = Math.pow(10, A(l) ? C : l)),
        (S = Math.round(S * v) / v),
        (M = Math.round(M * v) / v));
    let I = 0;
    for (
        b &&
        (d && S !== r
            ? (e.push({ value: r }),
              S < r && I++,
              Ee(Math.round((S + I * x) * v) / v, r, _a(r, w, s)) && I++)
            : S < r && I++);
        I < T;
        ++I
    ) {
        const L = Math.round((S + I * x) * v) / v;
        if (y && L > a) break;
        e.push({ value: L });
    }
    return (
        y && d && M !== a
            ? e.length && Ee(e[e.length - 1].value, a, _a(a, w, s))
                ? (e[e.length - 1].value = a)
                : e.push({ value: a })
            : (!y || M === a) && e.push({ value: M }),
        e
    );
}
function _a(s, t, { horizontal: e, minRotation: i }) {
    const n = bt(i);
    const o = (e ? Math.sin(n) : Math.cos(n)) || 0.001;
    const r = 0.75 * t * ("" + s).length;
    return Math.min(t / o, r);
}
const Ze = class extends _e {
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
            const l = St(n);
            const c = St(o);
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
        const r = Vf(n, o);
        return (
            t.bounds === "ticks" && on(r, this, "value"),
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
        return Fe(t, this.chart.options.locale, this.options.ticks.format);
    }
};
const Os = class extends Ze {
    determineDataLimits() {
        const { min: t, max: e } = this.getMinMax(!0);
        ((this.min = Z(t) ? t : 0),
            (this.max = Z(e) ? e : 1),
            this.handleTickRangeOptions());
    }

    computeTickLimit() {
        const t = this.isHorizontal();
        const e = t ? this.width : this.height;
        const i = bt(this.options.ticks.minRotation);
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
(k(Os, "id", "linear"),
    k(Os, "defaults", { ticks: { callback: gs.formatters.numeric } }));
const As = (s) => Math.floor(Vt(s));
const me = (s, t) => Math.pow(10, As(s) + t);
function wa(s) {
    return s / Math.pow(10, As(s)) === 1;
}
function ka(s, t, e) {
    const i = Math.pow(10, e);
    const n = Math.floor(s / i);
    return Math.ceil(t / i) - n;
}
function Wf(s, t) {
    const e = t - s;
    let i = As(e);
    for (; ka(s, t, i) > 10; ) i++;
    for (; ka(s, t, i) < 10; ) i--;
    return Math.min(i, As(s));
}
function Bf(s, { min: t, max: e }) {
    t = at(s.min, t);
    const i = [];
    const n = As(t);
    let o = Wf(t, e);
    let r = o < 0 ? Math.pow(10, Math.abs(o)) : 1;
    const a = Math.pow(10, o);
    const l = n > o ? Math.pow(10, n) : 0;
    const c = Math.round((t - l) * r) / r;
    const h = Math.floor((t - l) / a / 10) * a * 10;
    let u = Math.floor((c - h) / Math.pow(10, o));
    let d = at(s.min, Math.round((l + h + u * Math.pow(10, o)) * r) / r);
    for (; d < e; ) {
        (i.push({ value: d, major: wa(d), significand: u }),
            u >= 10 ? (u = u < 15 ? 15 : 20) : u++,
            u >= 20 && (o++, (u = 2), (r = o >= 0 ? 1 : r)),
            (d = Math.round((l + h + u * Math.pow(10, o)) * r) / r));
    }
    const f = at(s.max, d);
    return (i.push({ value: f, major: wa(f), significand: u }), i);
}
const Ts = class extends _e {
    constructor(t) {
        (super(t),
            (this.start = void 0),
            (this.end = void 0),
            (this._startValue = void 0),
            (this._valueRange = 0));
    }

    parse(t, e) {
        const i = Ze.prototype.parse.apply(this, [t, e]);
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
                    t === me(this.min, 0) ? me(this.min, -1) : me(this.min, 0)),
            this.handleTickRangeOptions());
    }

    handleTickRangeOptions() {
        const { minDefined: t, maxDefined: e } = this.getUserBounds();
        let i = this.min;
        let n = this.max;
        const o = (a) => (i = t ? i : a);
        const r = (a) => (n = e ? n : a);
        (i === n && (i <= 0 ? (o(1), r(10)) : (o(me(i, -1)), r(me(n, 1)))),
            i <= 0 && o(me(n, -1)),
            n <= 0 && r(me(i, 1)),
            (this.min = i),
            (this.max = n));
    }

    buildTicks() {
        const t = this.options;
        const e = { min: this._userMin, max: this._userMax };
        const i = Bf(e, this);
        return (
            t.bounds === "ticks" && on(i, this, "value"),
            t.reverse
                ? (i.reverse(), (this.start = this.max), (this.end = this.min))
                : ((this.start = this.min), (this.end = this.max)),
            i
        );
    }

    getLabelForValue(t) {
        return t === void 0
            ? "0"
            : Fe(t, this.chart.options.locale, this.options.ticks.format);
    }

    configure() {
        const t = this.min;
        (super.configure(),
            (this._startValue = Vt(t)),
            (this._valueRange = Vt(this.max) - Vt(t)));
    }

    getPixelForValue(t) {
        return (
            (t === void 0 || t === 0) && (t = this.min),
            t === null || isNaN(t)
                ? NaN
                : this.getPixelForDecimal(
                      t === this.min
                          ? 0
                          : (Vt(t) - this._startValue) / this._valueRange,
                  )
        );
    }

    getValueForPixel(t) {
        const e = this.getDecimalForPixel(t);
        return Math.pow(10, this._startValue + e * this._valueRange);
    }
};
(k(Ts, "id", "logarithmic"),
    k(Ts, "defaults", {
        ticks: { callback: gs.formatters.logarithmic, major: { enabled: !0 } },
    }));
function to(s) {
    const t = s.ticks;
    if (t.display && s.display) {
        const e = nt(t.backdropPadding);
        return P(t.font && t.font.size, j.font.size) + e.height;
    }
    return 0;
}
function Hf(s, t, e) {
    return (
        (e = H(e) ? e : [e]),
        { w: mr(s, t.string, e), h: e.length * t.lineHeight }
    );
}
function va(s, t, e, i, n) {
    return s === i || s === n
        ? { start: t - e / 2, end: t + e / 2 }
        : s < i || s > n
          ? { start: t - e, end: t }
          : { start: t, end: t + e };
}
function $f(s) {
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
    const a = r.centerPointLabels ? F / o : 0;
    for (let l = 0; l < o; l++) {
        const c = r.setContext(s.getPointLabelContext(l));
        n[l] = c.padding;
        const h = s.getPointPosition(l, s.drawingArea + n[l], a);
        const u = X(c.font);
        const d = Hf(s.ctx, u, s._pointLabels[l]);
        i[l] = d;
        const f = st(s.getIndexAngle(l) + a);
        const g = Math.round(si(f));
        const m = va(g, h.x, d.w, 0, 180);
        const p = va(g, h.y, d.h, 90, 270);
        jf(e, t, f, m, p);
    }
    (s.setCenterPoint(t.l - e.l, e.r - t.r, t.t - e.t, e.b - t.b),
        (s._pointLabelItems = Zf(s, i, n)));
}
function jf(s, t, e, i, n) {
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
function Uf(s, t, e) {
    const i = s.drawingArea;
    const { extra: n, additionalAngle: o, padding: r, size: a } = e;
    const l = s.getPointPosition(t, i + n + r, o);
    const c = Math.round(si(st(l.angle + q)));
    const h = Xf(l.y, a.h, c);
    const u = qf(c);
    const d = Gf(l.x, a.w, u);
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
function Yf(s, t) {
    if (!t) return !0;
    const { left: e, top: i, right: n, bottom: o } = s;
    return !(
        Ct({ x: e, y: i }, t) ||
        Ct({ x: e, y: o }, t) ||
        Ct({ x: n, y: i }, t) ||
        Ct({ x: n, y: o }, t)
    );
}
function Zf(s, t, e) {
    const i = [];
    const n = s._pointLabels.length;
    const o = s.options;
    const { centerPointLabels: r, display: a } = o.pointLabels;
    const l = { extra: to(o) / 2, additionalAngle: r ? F / n : 0 };
    let c;
    for (let h = 0; h < n; h++) {
        ((l.padding = e[h]), (l.size = t[h]));
        const u = Uf(s, h, l);
        (i.push(u),
            a === "auto" && ((u.visible = Yf(u, c)), u.visible && (c = u)));
    }
    return i;
}
function qf(s) {
    return s === 0 || s === 180 ? "center" : s < 180 ? "left" : "right";
}
function Gf(s, t, e) {
    return (e === "right" ? (s -= t) : e === "center" && (s -= t / 2), s);
}
function Xf(s, t, e) {
    return (
        e === 90 || e === 270 ? (s -= t / 2) : (e > 270 || e < 90) && (s -= t),
        s
    );
}
function Kf(s, t, e) {
    const { left: i, top: n, right: o, bottom: r } = e;
    const { backdropColor: a } = t;
    if (!A(a)) {
        const l = Qt(t.borderRadius);
        const c = nt(t.backdropPadding);
        s.fillStyle = a;
        const h = i - c.left;
        const u = n - c.top;
        const d = o - i + c.width;
        const f = r - n + c.height;
        Object.values(l).some((g) => g !== 0)
            ? (s.beginPath(),
              Re(s, { x: h, y: u, w: d, h: f, radius: l }),
              s.fill())
            : s.fillRect(h, u, d, f);
    }
}
function Jf(s, t) {
    const {
        ctx: e,
        options: { pointLabels: i },
    } = s;
    for (let n = t - 1; n >= 0; n--) {
        const o = s._pointLabelItems[n];
        if (!o.visible) continue;
        const r = i.setContext(s.getPointLabelContext(n));
        Kf(e, r, o);
        const a = X(r.font);
        const { x: l, y: c, textAlign: h } = o;
        Jt(e, s._pointLabels[n], l, c + a.lineHeight / 2, a, {
            color: r.color,
            textAlign: h,
            textBaseline: "middle",
        });
    }
}
function qa(s, t, e, i) {
    const { ctx: n } = s;
    if (e) n.arc(s.xCenter, s.yCenter, t, 0, $);
    else {
        let o = s.getPointPosition(0, t);
        n.moveTo(o.x, o.y);
        for (let r = 1; r < i; r++) {
            ((o = s.getPointPosition(r, t)), n.lineTo(o.x, o.y));
        }
    }
}
function Qf(s, t, e, i, n) {
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
        qa(s, e, r, i),
        o.closePath(),
        o.stroke(),
        o.restore());
}
function tg(s, t, e) {
    return Bt(s, { label: e, index: t, type: "pointLabel" });
}
const be = class extends Ze {
    constructor(t) {
        (super(t),
            (this.xCenter = void 0),
            (this.yCenter = void 0),
            (this.drawingArea = void 0),
            (this._pointLabels = []),
            (this._pointLabelItems = []));
    }

    setDimensions() {
        const t = (this._padding = nt(to(this.options) / 2));
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
        return Math.ceil(this.drawingArea / to(this.options));
    }

    generateTickLabels(t) {
        (Ze.prototype.generateTickLabels.call(this, t),
            (this._pointLabels = this.getLabels()
                .map((e, i) => {
                    const n = W(
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
            ? $f(this)
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
        const e = $ / (this._pointLabels.length || 1);
        const i = this.options.startAngle || 0;
        return st(t * e + bt(i));
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
            return tg(this.getContext(), t, i);
        }
    }

    getPointPosition(t, e, i = 0) {
        const n = this.getIndexAngle(t) - q + i;
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
                qa(
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
            (e.pointLabels.display && Jf(this, r),
            n.display &&
                this.ticks.forEach((h, u) => {
                    if (u !== 0 || (u === 0 && this.min < 0)) {
                        l = this.getDistanceFromCenterForValue(h.value);
                        const d = this.getContext(u);
                        const f = n.setContext(d);
                        const g = o.setContext(d);
                        Qf(this, f, l, r, g);
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
                const h = X(c.font);
                if (
                    ((o = this.getDistanceFromCenterForValue(
                        this.ticks[l].value,
                    )),
                    c.showLabelBackdrop)
                ) {
                    ((t.font = h.string),
                        (r = t.measureText(a.label).width),
                        (t.fillStyle = c.backdropColor));
                    const u = nt(c.backdropPadding);
                    t.fillRect(
                        -r / 2 - u.left,
                        -o - h.size / 2 - u.top,
                        r + u.width,
                        h.size + u.height,
                    );
                }
                Jt(t, a.label, 0, -o, h, {
                    color: c.color,
                    strokeColor: c.textStrokeColor,
                    strokeWidth: c.textStrokeWidth,
                });
            }),
            t.restore());
    }

    drawTitle() {}
};
(k(be, "id", "radialLinear"),
    k(be, "defaults", {
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
        ticks: { showLabelBackdrop: !0, callback: gs.formatters.numeric },
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
    k(be, "defaultRoutes", {
        "angleLines.color": "borderColor",
        "pointLabels.color": "color",
        "ticks.color": "color",
    }),
    k(be, "descriptors", { angleLines: { _fallback: "grid" } }));
const Ti = {
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
const ct = Object.keys(Ti);
function Sa(s, t) {
    return s - t;
}
function Ma(s, t) {
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
                      n === "week" && (de(o) || o === !0)
                          ? e.startOf(r, "isoWeek", o)
                          : e.startOf(r, n)),
              +r)
    );
}
function Oa(s, t, e, i) {
    const n = ct.length;
    for (let o = ct.indexOf(s); o < n - 1; ++o) {
        const r = Ti[ct[o]];
        const a = r.steps ? r.steps : Number.MAX_SAFE_INTEGER;
        if (r.common && Math.ceil((e - t) / (a * r.size)) <= i) return ct[o];
    }
    return ct[n - 1];
}
function eg(s, t, e, i, n) {
    for (let o = ct.length - 1; o >= ct.indexOf(e); o--) {
        const r = ct[o];
        if (Ti[r].common && s._adapter.diff(n, i, r) >= t - 1) return r;
    }
    return ct[e ? ct.indexOf(e) : 0];
}
function sg(s) {
    for (let t = ct.indexOf(s) + 1, e = ct.length; t < e; ++t) {
        if (Ti[ct[t]].common) return ct[t];
    }
}
function Ta(s, t, e) {
    if (!e) s[t] = !0;
    else if (e.length) {
        const { lo: i, hi: n } = ii(e, t);
        const o = e[i] >= t ? e[i] : e[n];
        s[o] = !0;
    }
}
function ig(s, t, e, i) {
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
function Da(s, t, e) {
    const i = [];
    const n = {};
    const o = t.length;
    let r;
    let a;
    for (r = 0; r < o; ++r) {
        ((a = t[r]), (n[a] = r), i.push({ value: a, major: !1 }));
    }
    return o === 0 || !e ? i : ig(s, i, n, e);
}
const xe = class extends _e {
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
        const n = (this._adapter = new eo._date(t.adapters.date));
        (n.init(e),
            Ie(i.displayFormats, n.formats()),
            (this._parseOpts = {
                parser: i.parser,
                round: i.round,
                isoWeekday: i.isoWeekday,
            }),
            super.init(t),
            (this._normalized = e.normalized));
    }

    parse(t, e) {
        return t === void 0 ? null : Ma(this, t);
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
        const a = cr(n, o, r);
        return (
            (this._unit =
                e.unit ||
                (i.autoSkip
                    ? Oa(
                          e.minUnit,
                          this.min,
                          this.max,
                          this._getLabelCapacity(o),
                      )
                    : eg(this, a.length, e.minUnit, this.min, this.max))),
            (this._majorUnit =
                !i.major.enabled || this._unit === "year"
                    ? void 0
                    : sg(this._unit)),
            this.initOffsets(n),
            t.reverse && a.reverse(),
            Da(this, a, this._majorUnit)
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
        ((e = J(e, 0, r)),
            (i = J(i, 0, r)),
            (this._offsets = { start: e, end: i, factor: 1 / (e + 1 + i) }));
    }

    _generate() {
        const t = this._adapter;
        const e = this.min;
        const i = this.max;
        const n = this.options;
        const o = n.time;
        const r = o.unit || Oa(o.minUnit, e, i, this._getLabelCapacity(e));
        const a = P(n.ticks.stepSize, 1);
        const l = r === "week" ? o.isoWeekday : !1;
        const c = de(l) || l === !0;
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
        const g = n.ticks.source === "data" && this.getDataTimestamps();
        for (d = u, f = 0; d < i; d = +t.add(d, a, r), f++) Ta(h, d, g);
        return (
            (d === i || n.bounds === "ticks" || f === 1) && Ta(h, d, g),
            Object.keys(h)
                .sort(Sa)
                .map((m) => +m)
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
        if (r) return W(r, [t, e, i], this);
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
        const n = bt(this.isHorizontal() ? e.maxRotation : e.minRotation);
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
            Da(this, [t], this._majorUnit),
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
        for (e = 0, i = n.length; e < i; ++e) t.push(Ma(this, n[e]));
        return (this._cache.labels = this._normalized ? t : this.normalize(t));
    }

    normalize(t) {
        return cn(t.sort(Sa));
    }
};
(k(xe, "id", "time"),
    k(xe, "defaults", {
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
function bi(s, t, e) {
    let i = 0;
    let n = s.length - 1;
    let o;
    let r;
    let a;
    let l;
    e
        ? (t >= s[i].pos &&
              t <= s[n].pos &&
              ({ lo: i, hi: n } = Dt(s, "pos", t)),
          ({ pos: o, time: a } = s[i]),
          ({ pos: r, time: l } = s[n]))
        : (t >= s[i].time &&
              t <= s[n].time &&
              ({ lo: i, hi: n } = Dt(s, "time", t)),
          ({ time: o, pos: a } = s[i]),
          ({ time: r, pos: l } = s[n]));
    const c = r - o;
    return c ? a + ((l - a) * (t - o)) / c : a;
}
const Ds = class extends xe {
    constructor(t) {
        (super(t),
            (this._table = []),
            (this._minPos = void 0),
            (this._tableRange = void 0));
    }

    initOffsets() {
        const t = this._getTimestampsForTable();
        const e = (this._table = this.buildLookupTable(t));
        ((this._minPos = bi(e, this.min)),
            (this._tableRange = bi(e, this.max) - this._minPos),
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
        return (bi(this._table, t) - this._minPos) / this._tableRange;
    }

    getValueForPixel(t) {
        const e = this._offsets;
        const i = this.getDecimalForPixel(t) / e.factor - e.end;
        return bi(this._table, i * this._tableRange + this._minPos, !0);
    }
};
(k(Ds, "id", "timeseries"), k(Ds, "defaults", xe.defaults));
const ng = Object.freeze({
    __proto__: null,
    CategoryScale: Ms,
    LinearScale: Os,
    LogarithmicScale: Ts,
    RadialLinearScale: be,
    TimeScale: xe,
    TimeSeriesScale: Ds,
});
const Ga = [mu, Vd, Ff, ng];
yt.register(...Ga);
const Ft = yt;
const Yt = class extends Error {};
const uo = class extends Yt {
    constructor(t) {
        super(`Invalid DateTime: ${t.toMessage()}`);
    }
};
const fo = class extends Yt {
    constructor(t) {
        super(`Invalid Interval: ${t.toMessage()}`);
    }
};
const go = class extends Yt {
    constructor(t) {
        super(`Invalid Duration: ${t.toMessage()}`);
    }
};
const ne = class extends Yt {};
const Fi = class extends Yt {
    constructor(t) {
        super(`Invalid unit ${t}`);
    }
};
const Q = class extends Yt {};
const Rt = class extends Yt {
    constructor() {
        super("Zone is an abstract class");
    }
};
const O = "numeric";
const Tt = "short";
const mt = "long";
const Ri = { year: O, month: O, day: O };
const Ol = { year: O, month: Tt, day: O };
const og = { year: O, month: Tt, day: O, weekday: Tt };
const Tl = { year: O, month: mt, day: O };
const Dl = { year: O, month: mt, day: O, weekday: mt };
const Cl = { hour: O, minute: O };
const Pl = { hour: O, minute: O, second: O };
const Il = { hour: O, minute: O, second: O, timeZoneName: Tt };
const Al = { hour: O, minute: O, second: O, timeZoneName: mt };
const El = { hour: O, minute: O, hourCycle: "h23" };
const Ll = { hour: O, minute: O, second: O, hourCycle: "h23" };
const Fl = {
    hour: O,
    minute: O,
    second: O,
    hourCycle: "h23",
    timeZoneName: Tt,
};
const Rl = {
    hour: O,
    minute: O,
    second: O,
    hourCycle: "h23",
    timeZoneName: mt,
};
const Nl = { year: O, month: O, day: O, hour: O, minute: O };
const zl = { year: O, month: O, day: O, hour: O, minute: O, second: O };
const Vl = { year: O, month: Tt, day: O, hour: O, minute: O };
const Wl = { year: O, month: Tt, day: O, hour: O, minute: O, second: O };
const rg = { year: O, month: Tt, day: O, weekday: Tt, hour: O, minute: O };
const Bl = { year: O, month: mt, day: O, hour: O, minute: O, timeZoneName: Tt };
const Hl = {
    year: O,
    month: mt,
    day: O,
    hour: O,
    minute: O,
    second: O,
    timeZoneName: Tt,
};
const $l = {
    year: O,
    month: mt,
    day: O,
    weekday: mt,
    hour: O,
    minute: O,
    timeZoneName: mt,
};
const jl = {
    year: O,
    month: mt,
    day: O,
    weekday: mt,
    hour: O,
    minute: O,
    second: O,
    timeZoneName: mt,
};
const Se = class {
    get type() {
        throw new Rt();
    }

    get name() {
        throw new Rt();
    }

    get ianaName() {
        return this.name;
    }

    get isUniversal() {
        throw new Rt();
    }

    offsetName(t, e) {
        throw new Rt();
    }

    formatOffset(t, e) {
        throw new Rt();
    }

    offset(t) {
        throw new Rt();
    }

    equals(t) {
        throw new Rt();
    }

    get isValid() {
        throw new Rt();
    }
};
let so = null;
const Ni = class s extends Se {
    static get instance() {
        return (so === null && (so = new s()), so);
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
        return ec(t, e, i);
    }

    formatOffset(t, e) {
        return zs(this.offset(t), e);
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
const mo = new Map();
function ag(s) {
    let t = mo.get(s);
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
            mo.set(s, t)),
        t
    );
}
const lg = { year: 0, month: 1, day: 2, era: 3, hour: 4, minute: 5, second: 6 };
function cg(s, t) {
    const e = s.format(t).replace(/\u200E/g, "");
    const i = /(\d+)\/(\d+)\/(\d+) (AD|BC),? (\d+):(\d+):(\d+)/.exec(e);
    const [, n, o, r, a, l, c, h] = i;
    return [r, n, o, a, l, c, h];
}
function hg(s, t) {
    const e = s.formatToParts(t);
    const i = [];
    for (let n = 0; n < e.length; n++) {
        const { type: o, value: r } = e[n];
        const a = lg[o];
        o === "era" ? (i[a] = r) : D(a) || (i[a] = parseInt(r, 10));
    }
    return i;
}
const io = new Map();
const re = class s extends Se {
    static create(t) {
        let e = io.get(t);
        return (e === void 0 && io.set(t, (e = new s(t))), e);
    }

    static resetCache() {
        (io.clear(), mo.clear());
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
        return ec(t, e, i, this.name);
    }

    formatOffset(t, e) {
        return zs(this.offset(t), e);
    }

    offset(t) {
        if (!this.valid) return NaN;
        const e = new Date(t);
        if (isNaN(e)) return NaN;
        const i = ag(this.name);
        let [n, o, r, a, l, c, h] = i.formatToParts ? hg(i, e) : cg(i, e);
        a === "BC" && (n = -Math.abs(n) + 1);
        const d = $i({
            year: n,
            month: o,
            day: r,
            hour: l === 24 ? 0 : l,
            minute: c,
            second: h,
            millisecond: 0,
        });
        let f = +e;
        const g = f % 1e3;
        return ((f -= g >= 0 ? g : 1e3 + g), (d - f) / (60 * 1e3));
    }

    equals(t) {
        return t.type === "iana" && t.name === this.name;
    }

    get isValid() {
        return this.valid;
    }
};
const Xa = {};
function ug(s, t = {}) {
    const e = JSON.stringify([s, t]);
    let i = Xa[e];
    return (i || ((i = new Intl.ListFormat(s, t)), (Xa[e] = i)), i);
}
const po = new Map();
function bo(s, t = {}) {
    const e = JSON.stringify([s, t]);
    let i = po.get(e);
    return (
        i === void 0 && ((i = new Intl.DateTimeFormat(s, t)), po.set(e, i)),
        i
    );
}
const yo = new Map();
function dg(s, t = {}) {
    const e = JSON.stringify([s, t]);
    let i = yo.get(e);
    return (
        i === void 0 && ((i = new Intl.NumberFormat(s, t)), yo.set(e, i)),
        i
    );
}
const xo = new Map();
function fg(s, t = {}) {
    const { base: e, ...i } = t;
    const n = JSON.stringify([s, i]);
    let o = xo.get(n);
    return (
        o === void 0 && ((o = new Intl.RelativeTimeFormat(s, t)), xo.set(n, o)),
        o
    );
}
let Fs = null;
function gg() {
    return (
        Fs || ((Fs = new Intl.DateTimeFormat().resolvedOptions().locale), Fs)
    );
}
const _o = new Map();
function Ul(s) {
    let t = _o.get(s);
    return (
        t === void 0 &&
            ((t = new Intl.DateTimeFormat(s).resolvedOptions()), _o.set(s, t)),
        t
    );
}
const wo = new Map();
function mg(s) {
    let t = wo.get(s);
    if (!t) {
        const e = new Intl.Locale(s);
        ((t = "getWeekInfo" in e ? e.getWeekInfo() : e.weekInfo),
            "minimalDays" in t || (t = { ...Yl, ...t }),
            wo.set(s, t));
    }
    return t;
}
function pg(s) {
    const t = s.indexOf("-x-");
    t !== -1 && (s = s.substring(0, t));
    const e = s.indexOf("-u-");
    if (e === -1) return [s];
    {
        let i, n;
        try {
            ((i = bo(s).resolvedOptions()), (n = s));
        } catch {
            const l = s.substring(0, e);
            ((i = bo(l).resolvedOptions()), (n = l));
        }
        const { numberingSystem: o, calendar: r } = i;
        return [n, o, r];
    }
}
function bg(s, t, e) {
    return (
        (e || t) &&
            (s.includes("-u-") || (s += "-u"),
            e && (s += `-ca-${e}`),
            t && (s += `-nu-${t}`)),
        s
    );
}
function yg(s) {
    const t = [];
    for (let e = 1; e <= 12; e++) {
        const i = R.utc(2009, e, 1);
        t.push(s(i));
    }
    return t;
}
function xg(s) {
    const t = [];
    for (let e = 1; e <= 7; e++) {
        const i = R.utc(2016, 11, 13 + e);
        t.push(s(i));
    }
    return t;
}
function Di(s, t, e, i) {
    const n = s.listingMode();
    return n === "error" ? null : n === "en" ? e(t) : i(t);
}
function _g(s) {
    return s.numberingSystem && s.numberingSystem !== "latn"
        ? !1
        : s.numberingSystem === "latn" ||
              !s.locale ||
              s.locale.startsWith("en") ||
              Ul(s.locale).numberingSystem === "latn";
}
const ko = class {
    constructor(t, e, i) {
        ((this.padTo = i.padTo || 0), (this.floor = i.floor || !1));
        const { padTo: n, floor: o, ...r } = i;
        if (!e || Object.keys(r).length > 0) {
            const a = { useGrouping: !1, ...i };
            (i.padTo > 0 && (a.minimumIntegerDigits = i.padTo),
                (this.inf = dg(t, a)));
        }
    }

    format(t) {
        if (this.inf) {
            const e = this.floor ? Math.floor(t) : t;
            return this.inf.format(e);
        } else {
            const e = this.floor ? Math.floor(t) : Lo(t, 3);
            return K(e, this.padTo);
        }
    }
};
const vo = class {
    constructor(t, e, i) {
        ((this.opts = i), (this.originalZone = void 0));
        let n;
        if (this.opts.timeZone) this.dt = t;
        else if (t.zone.type === "fixed") {
            const r = -1 * (t.offset / 60);
            const a = r >= 0 ? `Etc/GMT+${r}` : `Etc/GMT${r}`;
            t.offset !== 0 && re.create(a).valid
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
        ((o.timeZone = o.timeZone || n), (this.dtf = bo(e, o)));
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
const So = class {
    constructor(t, e, i) {
        ((this.opts = { style: "long", ...i }),
            !e && Ql() && (this.rtf = fg(t, i)));
    }

    format(t, e) {
        return this.rtf
            ? this.rtf.format(t, e)
            : Bg(e, t, this.opts.numeric, this.opts.style !== "long");
    }

    formatToParts(t, e) {
        return this.rtf ? this.rtf.formatToParts(t, e) : [];
    }
};
var Yl = { firstDay: 1, minimalDays: 4, weekend: [6, 7] };
const B = class s {
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
        const r = t || Y.defaultLocale;
        const a = r || (o ? "en-US" : gg());
        const l = e || Y.defaultNumberingSystem;
        const c = i || Y.defaultOutputCalendar;
        const h = To(n) || Y.defaultWeekSettings;
        return new s(a, l, c, h, r);
    }

    static resetCache() {
        ((Fs = null),
            po.clear(),
            yo.clear(),
            xo.clear(),
            _o.clear(),
            wo.clear());
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
        const [r, a, l] = pg(t);
        ((this.locale = r),
            (this.numberingSystem = e || a || null),
            (this.outputCalendar = i || l || null),
            (this.weekSettings = n),
            (this.intl = bg(
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
                (this.fastNumbersCached = _g(this)),
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
                  To(t.weekSettings) || this.weekSettings,
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
        return Di(this, t, nc, () => {
            const i = this.intl === "ja" || this.intl.startsWith("ja-");
            e &= !i;
            const n = e ? { month: t, day: "numeric" } : { month: t };
            const o = e ? "format" : "standalone";
            if (!this.monthsCache[o][t]) {
                const r = i
                    ? (a) => this.dtFormatter(a, n).format()
                    : (a) => this.extract(a, n, "month");
                this.monthsCache[o][t] = yg(r);
            }
            return this.monthsCache[o][t];
        });
    }

    weekdays(t, e = !1) {
        return Di(this, t, ac, () => {
            const i = e
                ? { weekday: t, year: "numeric", month: "long", day: "numeric" }
                : { weekday: t };
            const n = e ? "format" : "standalone";
            return (
                this.weekdaysCache[n][t] ||
                    (this.weekdaysCache[n][t] = xg((o) =>
                        this.extract(o, i, "weekday"),
                    )),
                this.weekdaysCache[n][t]
            );
        });
    }

    meridiems() {
        return Di(
            this,
            void 0,
            () => lc,
            () => {
                if (!this.meridiemCache) {
                    const t = { hour: "numeric", hourCycle: "h12" };
                    this.meridiemCache = [
                        R.utc(2016, 11, 13, 9),
                        R.utc(2016, 11, 13, 19),
                    ].map((e) => this.extract(e, t, "dayperiod"));
                }
                return this.meridiemCache;
            },
        );
    }

    eras(t) {
        return Di(this, t, cc, () => {
            const e = { era: t };
            return (
                this.eraCache[t] ||
                    (this.eraCache[t] = [
                        R.utc(-40, 1, 1),
                        R.utc(2017, 1, 1),
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
        return new ko(this.intl, t.forceSimple || this.fastNumbers, t);
    }

    dtFormatter(t, e = {}) {
        return new vo(t, this.intl, e);
    }

    relFormatter(t = {}) {
        return new So(this.intl, this.isEnglish(), t);
    }

    listFormatter(t = {}) {
        return ug(this.intl, t);
    }

    isEnglish() {
        return (
            this.locale === "en" ||
            this.locale.toLowerCase() === "en-us" ||
            Ul(this.intl).locale.startsWith("en-us")
        );
    }

    getWeekSettings() {
        return this.weekSettings
            ? this.weekSettings
            : tc()
              ? mg(this.locale)
              : Yl;
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
let no = null;
const kt = class s extends Se {
    static get utcInstance() {
        return (no === null && (no = new s(0)), no);
    }

    static instance(t) {
        return t === 0 ? s.utcInstance : new s(t);
    }

    static parseSpecifier(t) {
        if (t) {
            const e = t.match(/^utc(?:([+-]\d{1,2})(?::(\d{2}))?)?$/i);
            if (e) return new s(ji(e[1], e[2]));
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
        return this.fixed === 0 ? "UTC" : `UTC${zs(this.fixed, "narrow")}`;
    }

    get ianaName() {
        return this.fixed === 0
            ? "Etc/UTC"
            : `Etc/GMT${zs(-this.fixed, "narrow")}`;
    }

    offsetName() {
        return this.name;
    }

    formatOffset(t, e) {
        return zs(this.fixed, e);
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
const Mo = class extends Se {
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
function ie(s, t) {
    if (D(s) || s === null) return t;
    if (s instanceof Se) return s;
    if (Og(s)) {
        const e = s.toLowerCase();
        return e === "default"
            ? t
            : e === "local" || e === "system"
              ? Ni.instance
              : e === "utc" || e === "gmt"
                ? kt.utcInstance
                : kt.parseSpecifier(e) || re.create(s);
    } else {
        return oe(s)
            ? kt.instance(s)
            : typeof s === "object" &&
                "offset" in s &&
                typeof s.offset === "function"
              ? s
              : new Mo(s);
    }
}
const Po = {
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
const Ka = {
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
const wg = Po.hanidec.replace(/[\[|\]]/g, "").split("");
function kg(s) {
    let t = parseInt(s, 10);
    if (isNaN(t)) {
        t = "";
        for (let e = 0; e < s.length; e++) {
            const i = s.charCodeAt(e);
            if (s[e].search(Po.hanidec) !== -1) t += wg.indexOf(s[e]);
            else {
                for (const n in Ka) {
                    const [o, r] = Ka[n];
                    i >= o && i <= r && (t += i - o);
                }
            }
        }
        return parseInt(t, 10);
    } else return t;
}
const Oo = new Map();
function vg() {
    Oo.clear();
}
function Mt({ numberingSystem: s }, t = "") {
    const e = s || "latn";
    let i = Oo.get(e);
    i === void 0 && ((i = new Map()), Oo.set(e, i));
    let n = i.get(t);
    return (n === void 0 && ((n = new RegExp(`${Po[e]}${t}`)), i.set(t, n)), n);
}
let Ja = () => Date.now();
let Qa = "system";
let tl = null;
let el = null;
let sl = null;
let il = 60;
let nl;
let ol = null;
var Y = class {
    static get now() {
        return Ja;
    }

    static set now(t) {
        Ja = t;
    }

    static set defaultZone(t) {
        Qa = t;
    }

    static get defaultZone() {
        return ie(Qa, Ni.instance);
    }

    static get defaultLocale() {
        return tl;
    }

    static set defaultLocale(t) {
        tl = t;
    }

    static get defaultNumberingSystem() {
        return el;
    }

    static set defaultNumberingSystem(t) {
        el = t;
    }

    static get defaultOutputCalendar() {
        return sl;
    }

    static set defaultOutputCalendar(t) {
        sl = t;
    }

    static get defaultWeekSettings() {
        return ol;
    }

    static set defaultWeekSettings(t) {
        ol = To(t);
    }

    static get twoDigitCutoffYear() {
        return il;
    }

    static set twoDigitCutoffYear(t) {
        il = t % 100;
    }

    static get throwOnInvalid() {
        return nl;
    }

    static set throwOnInvalid(t) {
        nl = t;
    }

    static resetCaches() {
        (B.resetCache(), re.resetCache(), R.resetCache(), vg());
    }
};
const gt = class {
    constructor(t, e) {
        ((this.reason = t), (this.explanation = e));
    }

    toMessage() {
        return this.explanation
            ? `${this.reason}: ${this.explanation}`
            : this.reason;
    }
};
const Zl = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
const ql = [0, 31, 60, 91, 121, 152, 182, 213, 244, 274, 305, 335];
function _t(s, t) {
    return new gt(
        "unit out of range",
        `you specified ${t} (of type ${typeof t}) as a ${s}, which is invalid`,
    );
}
function Io(s, t, e) {
    const i = new Date(Date.UTC(s, t - 1, e));
    s < 100 && s >= 0 && i.setUTCFullYear(i.getUTCFullYear() - 1900);
    const n = i.getUTCDay();
    return n === 0 ? 7 : n;
}
function Gl(s, t, e) {
    return e + (Ws(s) ? ql : Zl)[t - 1];
}
function Xl(s, t) {
    const e = Ws(s) ? ql : Zl;
    const i = e.findIndex((o) => o < t);
    const n = t - e[i];
    return { month: i + 1, day: n };
}
function Ao(s, t) {
    return ((s - t + 7) % 7) + 1;
}
function zi(s, t = 4, e = 1) {
    const { year: i, month: n, day: o } = s;
    const r = Gl(i, n, o);
    const a = Ao(Io(i, n, o), e);
    let l = Math.floor((r - a + 14 - t) / 7);
    let c;
    return (
        l < 1
            ? ((c = i - 1), (l = Vs(c, t, e)))
            : l > Vs(i, t, e)
              ? ((c = i + 1), (l = 1))
              : (c = i),
        { weekYear: c, weekNumber: l, weekday: a, ...Ui(s) }
    );
}
function rl(s, t = 4, e = 1) {
    const { weekYear: i, weekNumber: n, weekday: o } = s;
    const r = Ao(Io(i, 1, t), e);
    const a = Je(i);
    let l = n * 7 + o - r - 7 + t;
    let c;
    l < 1
        ? ((c = i - 1), (l += Je(c)))
        : l > a
          ? ((c = i + 1), (l -= Je(i)))
          : (c = i);
    const { month: h, day: u } = Xl(c, l);
    return { year: c, month: h, day: u, ...Ui(s) };
}
function oo(s) {
    const { year: t, month: e, day: i } = s;
    const n = Gl(t, e, i);
    return { year: t, ordinal: n, ...Ui(s) };
}
function al(s) {
    const { year: t, ordinal: e } = s;
    const { month: i, day: n } = Xl(t, e);
    return { year: t, month: i, day: n, ...Ui(s) };
}
function ll(s, t) {
    if (!D(s.localWeekday) || !D(s.localWeekNumber) || !D(s.localWeekYear)) {
        if (!D(s.weekday) || !D(s.weekNumber) || !D(s.weekYear)) {
            throw new ne(
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
function Sg(s, t = 4, e = 1) {
    const i = Hi(s.weekYear);
    const n = wt(s.weekNumber, 1, Vs(s.weekYear, t, e));
    const o = wt(s.weekday, 1, 7);
    return i
        ? n
            ? o
                ? !1
                : _t("weekday", s.weekday)
            : _t("week", s.weekNumber)
        : _t("weekYear", s.weekYear);
}
function Mg(s) {
    const t = Hi(s.year);
    const e = wt(s.ordinal, 1, Je(s.year));
    return t ? (e ? !1 : _t("ordinal", s.ordinal)) : _t("year", s.year);
}
function Kl(s) {
    const t = Hi(s.year);
    const e = wt(s.month, 1, 12);
    const i = wt(s.day, 1, Vi(s.year, s.month));
    return t
        ? e
            ? i
                ? !1
                : _t("day", s.day)
            : _t("month", s.month)
        : _t("year", s.year);
}
function Jl(s) {
    const { hour: t, minute: e, second: i, millisecond: n } = s;
    const o = wt(t, 0, 23) || (t === 24 && e === 0 && i === 0 && n === 0);
    const r = wt(e, 0, 59);
    const a = wt(i, 0, 59);
    const l = wt(n, 0, 999);
    return o
        ? r
            ? a
                ? l
                    ? !1
                    : _t("millisecond", n)
                : _t("second", i)
            : _t("minute", e)
        : _t("hour", t);
}
function D(s) {
    return typeof s > "u";
}
function oe(s) {
    return typeof s === "number";
}
function Hi(s) {
    return typeof s === "number" && s % 1 === 0;
}
function Og(s) {
    return typeof s === "string";
}
function Tg(s) {
    return Object.prototype.toString.call(s) === "[object Date]";
}
function Ql() {
    try {
        return typeof Intl < "u" && !!Intl.RelativeTimeFormat;
    } catch {
        return !1;
    }
}
function tc() {
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
function Dg(s) {
    return Array.isArray(s) ? s : [s];
}
function cl(s, t, e) {
    if (s.length !== 0) {
        return s.reduce((i, n) => {
            const o = [t(n), n];
            return i && e(i[0], o[0]) === i[0] ? i : o;
        }, null)[1];
    }
}
function Cg(s, t) {
    return t.reduce((e, i) => ((e[i] = s[i]), e), {});
}
function es(s, t) {
    return Object.prototype.hasOwnProperty.call(s, t);
}
function To(s) {
    if (s == null) return null;
    if (typeof s !== "object") throw new Q("Week settings must be an object");
    if (
        !wt(s.firstDay, 1, 7) ||
        !wt(s.minimalDays, 1, 7) ||
        !Array.isArray(s.weekend) ||
        s.weekend.some((t) => !wt(t, 1, 7))
    ) {
        throw new Q("Invalid week settings");
    }
    return {
        firstDay: s.firstDay,
        minimalDays: s.minimalDays,
        weekend: Array.from(s.weekend),
    };
}
function wt(s, t, e) {
    return Hi(s) && s >= t && s <= e;
}
function Pg(s, t) {
    return s - t * Math.floor(s / t);
}
function K(s, t = 2) {
    const e = s < 0;
    let i;
    return (
        e
            ? (i = "-" + ("" + -s).padStart(t, "0"))
            : (i = ("" + s).padStart(t, "0")),
        i
    );
}
function se(s) {
    if (!(D(s) || s === null || s === "")) return parseInt(s, 10);
}
function we(s) {
    if (!(D(s) || s === null || s === "")) return parseFloat(s);
}
function Eo(s) {
    if (!(D(s) || s === null || s === "")) {
        const t = parseFloat("0." + s) * 1e3;
        return Math.floor(t);
    }
}
function Lo(s, t, e = "round") {
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
function Ws(s) {
    return s % 4 === 0 && (s % 100 !== 0 || s % 400 === 0);
}
function Je(s) {
    return Ws(s) ? 366 : 365;
}
function Vi(s, t) {
    const e = Pg(t - 1, 12) + 1;
    const i = s + (t - e) / 12;
    return e === 2
        ? Ws(i)
            ? 29
            : 28
        : [31, null, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][e - 1];
}
function $i(s) {
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
function hl(s, t, e) {
    return -Ao(Io(s, 1, t), e) + t - 1;
}
function Vs(s, t = 4, e = 1) {
    const i = hl(s, t, e);
    const n = hl(s + 1, t, e);
    return (Je(s) - i + n) / 7;
}
function Do(s) {
    return s > 99 ? s : s > Y.twoDigitCutoffYear ? 1900 + s : 2e3 + s;
}
function ec(s, t, e, i = null) {
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
function ji(s, t) {
    let e = parseInt(s, 10);
    Number.isNaN(e) && (e = 0);
    const i = parseInt(t, 10) || 0;
    const n = e < 0 || Object.is(e, -0) ? -i : i;
    return e * 60 + n;
}
function sc(s) {
    const t = Number(s);
    if (typeof s === "boolean" || s === "" || !Number.isFinite(t)) {
        throw new Q(`Invalid unit value ${s}`);
    }
    return t;
}
function Wi(s, t) {
    const e = {};
    for (const i in s) {
        if (es(s, i)) {
            const n = s[i];
            if (n == null) continue;
            e[t(i)] = sc(n);
        }
    }
    return e;
}
function zs(s, t) {
    const e = Math.trunc(Math.abs(s / 60));
    const i = Math.trunc(Math.abs(s % 60));
    const n = s >= 0 ? "+" : "-";
    switch (t) {
        case "short":
            return `${n}${K(e, 2)}:${K(i, 2)}`;
        case "narrow":
            return `${n}${e}${i > 0 ? `:${i}` : ""}`;
        case "techie":
            return `${n}${K(e, 2)}${K(i, 2)}`;
        default:
            throw new RangeError(
                `Value format ${t} is out of range for property format`,
            );
    }
}
function Ui(s) {
    return Cg(s, ["hour", "minute", "second", "millisecond"]);
}
const Ig = [
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
const ic = [
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
const Ag = ["J", "F", "M", "A", "M", "J", "J", "A", "S", "O", "N", "D"];
function nc(s) {
    switch (s) {
        case "narrow":
            return [...Ag];
        case "short":
            return [...ic];
        case "long":
            return [...Ig];
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
const oc = [
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday",
    "Sunday",
];
const rc = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
const Eg = ["M", "T", "W", "T", "F", "S", "S"];
function ac(s) {
    switch (s) {
        case "narrow":
            return [...Eg];
        case "short":
            return [...rc];
        case "long":
            return [...oc];
        case "numeric":
            return ["1", "2", "3", "4", "5", "6", "7"];
        default:
            return null;
    }
}
var lc = ["AM", "PM"];
const Lg = ["Before Christ", "Anno Domini"];
const Fg = ["BC", "AD"];
const Rg = ["B", "A"];
function cc(s) {
    switch (s) {
        case "narrow":
            return [...Rg];
        case "short":
            return [...Fg];
        case "long":
            return [...Lg];
        default:
            return null;
    }
}
function Ng(s) {
    return lc[s.hour < 12 ? 0 : 1];
}
function zg(s, t) {
    return ac(t)[s.weekday - 1];
}
function Vg(s, t) {
    return nc(t)[s.month - 1];
}
function Wg(s, t) {
    return cc(t)[s.year < 0 ? 0 : 1];
}
function Bg(s, t, e = "always", i = !1) {
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
        }
    }
    const r = Object.is(t, -0) || t < 0;
    const a = Math.abs(t);
    const l = a === 1;
    const c = n[s];
    const h = i ? (l ? c[1] : c[2] || c[1]) : l ? n[s][0] : s;
    return r ? `${a} ${h} ago` : `in ${a} ${h}`;
}
function ul(s, t) {
    let e = "";
    for (const i of s) i.literal ? (e += i.val) : (e += t(i.val));
    return e;
}
const Hg = {
    D: Ri,
    DD: Ol,
    DDD: Tl,
    DDDD: Dl,
    t: Cl,
    tt: Pl,
    ttt: Il,
    tttt: Al,
    T: El,
    TT: Ll,
    TTT: Fl,
    TTTT: Rl,
    f: Nl,
    ff: Vl,
    fff: Bl,
    ffff: $l,
    F: zl,
    FF: Wl,
    FFF: Hl,
    FFFF: jl,
};
const ft = class s {
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
        return Hg[t];
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
        if (this.opts.forceSimple) return K(t, e);
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
        const o = (f, g) => this.loc.extract(t, f, g);
        const r = (f) =>
            t.isOffsetFixed && t.offset === 0 && f.allowZ
                ? "Z"
                : t.isValid
                  ? t.zone.formatOffset(t.ts, f.format)
                  : "";
        const a = () =>
            i ? Ng(t) : o({ hour: "numeric", hourCycle: "h12" }, "dayperiod");
        const l = (f, g) =>
            i
                ? Vg(t, f)
                : o(g ? { month: f } : { month: f, day: "numeric" }, "month");
        const c = (f, g) =>
            i
                ? zg(t, f)
                : o(
                      g
                          ? { weekday: f }
                          : { weekday: f, month: "long", day: "numeric" },
                      "weekday",
                  );
        const h = (f) => {
            const g = s.macroTokenToFormatOpts(f);
            return g ? this.formatWithSystemDefault(t, g) : f;
        };
        const u = (f) => (i ? Wg(t, f) : o({ era: f }, "era"));
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
        return ul(s.parseFormat(e), d);
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
                const g = u.isNegativeDuration && f !== u.largestUnit ? i : 1;
                let m;
                return (
                    this.opts.signMode === "negativeLargestOnly" &&
                    f !== u.largestUnit
                        ? (m = "never")
                        : this.opts.signMode === "all"
                          ? (m = "always")
                          : (m = "auto"),
                    this.num(h.get(f) * g, d.length, m)
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
        return ul(r, o(l, c));
    }
};
const hc =
    /[A-Za-z_+-]{1,256}(?::?\/[A-Za-z0-9_+-]{1,256}(?:\/[A-Za-z0-9_+-]{1,256})?)?/;
function ss(...s) {
    const t = s.reduce((e, i) => e + i.source, "");
    return RegExp(`^${t}$`);
}
function is(...s) {
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
function ns(s, ...t) {
    if (s == null) return [null, null];
    for (const [e, i] of t) {
        const n = e.exec(s);
        if (n) return i(n);
    }
    return [null, null];
}
function uc(...s) {
    return (t, e) => {
        const i = {};
        let n;
        for (n = 0; n < s.length; n++) i[s[n]] = se(t[e + n]);
        return [i, null, e + n];
    };
}
const dc = /(?:([Zz])|([+-]\d\d)(?::?(\d\d))?)/;
const $g = `(?:${dc.source}?(?:\\[(${hc.source})\\])?)?`;
const Fo = /(\d\d)(?::?(\d\d)(?::?(\d\d)(?:[.,](\d{1,30}))?)?)?/;
const fc = RegExp(`${Fo.source}${$g}`);
const Ro = RegExp(`(?:[Tt]${fc.source})?`);
const jg = /([+-]\d{6}|\d{4})(?:-?(\d\d)(?:-?(\d\d))?)?/;
const Ug = /(\d{4})-?W(\d\d)(?:-?(\d))?/;
const Yg = /(\d{4})-?(\d{3})/;
const Zg = uc("weekYear", "weekNumber", "weekDay");
const qg = uc("year", "ordinal");
const Gg = /(\d{4})-(\d\d)-(\d\d)/;
const gc = RegExp(`${Fo.source} ?(?:${dc.source}|(${hc.source}))?`);
const Xg = RegExp(`(?: ${gc.source})?`);
function Qe(s, t, e) {
    const i = s[t];
    return D(i) ? e : se(i);
}
function Kg(s, t) {
    return [
        { year: Qe(s, t), month: Qe(s, t + 1, 1), day: Qe(s, t + 2, 1) },
        null,
        t + 3,
    ];
}
function os(s, t) {
    return [
        {
            hours: Qe(s, t, 0),
            minutes: Qe(s, t + 1, 0),
            seconds: Qe(s, t + 2, 0),
            milliseconds: Eo(s[t + 3]),
        },
        null,
        t + 4,
    ];
}
function Bs(s, t) {
    const e = !s[t] && !s[t + 1];
    const i = ji(s[t + 1], s[t + 2]);
    const n = e ? null : kt.instance(i);
    return [{}, n, t + 3];
}
function Hs(s, t) {
    const e = s[t] ? re.create(s[t]) : null;
    return [{}, e, t + 1];
}
const Jg = RegExp(`^T?${Fo.source}$`);
const Qg =
    /^-?P(?:(?:(-?\d{1,20}(?:\.\d{1,20})?)Y)?(?:(-?\d{1,20}(?:\.\d{1,20})?)M)?(?:(-?\d{1,20}(?:\.\d{1,20})?)W)?(?:(-?\d{1,20}(?:\.\d{1,20})?)D)?(?:T(?:(-?\d{1,20}(?:\.\d{1,20})?)H)?(?:(-?\d{1,20}(?:\.\d{1,20})?)M)?(?:(-?\d{1,20})(?:[.,](-?\d{1,20}))?S)?)?)$/;
function tm(s) {
    const [t, e, i, n, o, r, a, l, c] = s;
    const h = t[0] === "-";
    const u = l && l[0] === "-";
    const d = (f, g = !1) => (f !== void 0 && (g || (f && h)) ? -f : f);
    return [
        {
            years: d(we(e)),
            months: d(we(i)),
            weeks: d(we(n)),
            days: d(we(o)),
            hours: d(we(r)),
            minutes: d(we(a)),
            seconds: d(we(l), l === "-0"),
            milliseconds: d(Eo(c), u),
        },
    ];
}
const em = {
    GMT: 0,
    EDT: -240,
    EST: -300,
    CDT: -300,
    CST: -360,
    MDT: -360,
    MST: -420,
    PDT: -420,
    PST: -480,
};
function No(s, t, e, i, n, o, r) {
    const a = {
        year: t.length === 2 ? Do(se(t)) : se(t),
        month: ic.indexOf(e) + 1,
        day: se(i),
        hour: se(n),
        minute: se(o),
    };
    return (
        r && (a.second = se(r)),
        s && (a.weekday = s.length > 3 ? oc.indexOf(s) + 1 : rc.indexOf(s) + 1),
        a
    );
}
const sm =
    /^(?:(Mon|Tue|Wed|Thu|Fri|Sat|Sun),\s)?(\d{1,2})\s(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\s(\d{2,4})\s(\d\d):(\d\d)(?::(\d\d))?\s(?:(UT|GMT|[ECMP][SD]T)|([Zz])|(?:([+-]\d\d)(\d\d)))$/;
function im(s) {
    const [, t, e, i, n, o, r, a, l, c, h, u] = s;
    const d = No(t, n, i, e, o, r, a);
    let f;
    return (l ? (f = em[l]) : c ? (f = 0) : (f = ji(h, u)), [d, new kt(f)]);
}
function nm(s) {
    return s
        .replace(/\([^()]*\)|[\n\t]/g, " ")
        .replace(/(\s\s+)/g, " ")
        .trim();
}
const om =
    /^(Mon|Tue|Wed|Thu|Fri|Sat|Sun), (\d\d) (Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec) (\d{4}) (\d\d):(\d\d):(\d\d) GMT$/;
const rm =
    /^(Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday), (\d\d)-(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)-(\d\d) (\d\d):(\d\d):(\d\d) GMT$/;
const am =
    /^(Mon|Tue|Wed|Thu|Fri|Sat|Sun) (Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec) ( \d|\d\d) (\d\d):(\d\d):(\d\d) (\d{4})$/;
function dl(s) {
    const [, t, e, i, n, o, r, a] = s;
    return [No(t, n, i, e, o, r, a), kt.utcInstance];
}
function lm(s) {
    const [, t, e, i, n, o, r, a] = s;
    return [No(t, a, e, i, n, o, r), kt.utcInstance];
}
const cm = ss(jg, Ro);
const hm = ss(Ug, Ro);
const um = ss(Yg, Ro);
const dm = ss(fc);
const mc = is(Kg, os, Bs, Hs);
const fm = is(Zg, os, Bs, Hs);
const gm = is(qg, os, Bs, Hs);
const mm = is(os, Bs, Hs);
function pm(s) {
    return ns(s, [cm, mc], [hm, fm], [um, gm], [dm, mm]);
}
function bm(s) {
    return ns(nm(s), [sm, im]);
}
function ym(s) {
    return ns(s, [om, dl], [rm, dl], [am, lm]);
}
function xm(s) {
    return ns(s, [Qg, tm]);
}
const _m = is(os);
function wm(s) {
    return ns(s, [Jg, _m]);
}
const km = ss(Gg, Xg);
const vm = ss(gc);
const Sm = is(os, Bs, Hs);
function Mm(s) {
    return ns(s, [km, mc], [vm, Sm]);
}
const fl = "Invalid Duration";
const pc = {
    weeks: {
        days: 7,
        hours: 168,
        minutes: 10080,
        seconds: 10080 * 60,
        milliseconds: 10080 * 60 * 1e3,
    },
    days: {
        hours: 24,
        minutes: 1440,
        seconds: 1440 * 60,
        milliseconds: 1440 * 60 * 1e3,
    },
    hours: { minutes: 60, seconds: 3600, milliseconds: 3600 * 1e3 },
    minutes: { seconds: 60, milliseconds: 60 * 1e3 },
    seconds: { milliseconds: 1e3 },
};
const Om = {
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
        hours: 2184,
        minutes: 2184 * 60,
        seconds: 2184 * 60 * 60,
        milliseconds: 2184 * 60 * 60 * 1e3,
    },
    months: {
        weeks: 4,
        days: 30,
        hours: 720,
        minutes: 720 * 60,
        seconds: 720 * 60 * 60,
        milliseconds: 720 * 60 * 60 * 1e3,
    },
    ...pc,
};
const xt = 146097 / 400;
const qe = 146097 / 4800;
const Tm = {
    years: {
        quarters: 4,
        months: 12,
        weeks: xt / 7,
        days: xt,
        hours: xt * 24,
        minutes: xt * 24 * 60,
        seconds: xt * 24 * 60 * 60,
        milliseconds: xt * 24 * 60 * 60 * 1e3,
    },
    quarters: {
        months: 3,
        weeks: xt / 28,
        days: xt / 4,
        hours: (xt * 24) / 4,
        minutes: (xt * 24 * 60) / 4,
        seconds: (xt * 24 * 60 * 60) / 4,
        milliseconds: (xt * 24 * 60 * 60 * 1e3) / 4,
    },
    months: {
        weeks: qe / 7,
        days: qe,
        hours: qe * 24,
        minutes: qe * 24 * 60,
        seconds: qe * 24 * 60 * 60,
        milliseconds: qe * 24 * 60 * 60 * 1e3,
    },
    ...pc,
};
const ve = [
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
const Dm = ve.slice(0).reverse();
function Ut(s, t, e = !1) {
    const i = {
        values: e ? t.values : { ...s.values, ...(t.values || {}) },
        loc: s.loc.clone(t.loc),
        conversionAccuracy: t.conversionAccuracy || s.conversionAccuracy,
        matrix: t.matrix || s.matrix,
    };
    return new tt(i);
}
function bc(s, t) {
    let e = t.milliseconds ?? 0;
    for (const i of Dm.slice(1)) t[i] && (e += t[i] * s[i].milliseconds);
    return e;
}
function gl(s, t) {
    const e = bc(s, t) < 0 ? -1 : 1;
    (ve.reduceRight((i, n) => {
        if (D(t[n])) return i;
        if (i) {
            const o = t[i] * e;
            const r = s[n][i];
            const a = Math.floor(o / r);
            ((t[n] += a * e), (t[i] -= a * r * e));
        }
        return n;
    }, null),
        ve.reduce((i, n) => {
            if (D(t[n])) return i;
            if (i) {
                const o = t[i] % 1;
                ((t[i] -= o), (t[n] += o * s[i][n]));
            }
            return n;
        }, null));
}
function ml(s) {
    const t = {};
    for (const [e, i] of Object.entries(s)) i !== 0 && (t[e] = i);
    return t;
}
var tt = class s {
    constructor(t) {
        const e = t.conversionAccuracy === "longterm" || !1;
        let i = e ? Tm : Om;
        (t.matrix && (i = t.matrix),
            (this.values = t.values),
            (this.loc = t.loc || B.create()),
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
            throw new Q(
                `Duration.fromObject: argument expected to be an object, got ${t === null ? "null" : typeof t}`,
            );
        }
        return new s({
            values: Wi(t, s.normalizeUnit),
            loc: B.fromObject(e),
            conversionAccuracy: e.conversionAccuracy,
            matrix: e.matrix,
        });
    }

    static fromDurationLike(t) {
        if (oe(t)) return s.fromMillis(t);
        if (s.isDuration(t)) return t;
        if (typeof t === "object") return s.fromObject(t);
        throw new Q(`Unknown duration argument ${t} of type ${typeof t}`);
    }

    static fromISO(t, e) {
        const [i] = xm(t);
        return i
            ? s.fromObject(i, e)
            : s.invalid(
                  "unparsable",
                  `the input "${t}" can't be parsed as ISO 8601`,
              );
    }

    static fromISOTime(t, e) {
        const [i] = wm(t);
        return i
            ? s.fromObject(i, e)
            : s.invalid(
                  "unparsable",
                  `the input "${t}" can't be parsed as ISO 8601`,
              );
    }

    static invalid(t, e = null) {
        if (!t) throw new Q("need to specify a reason the Duration is invalid");
        const i = t instanceof gt ? t : new gt(t, e);
        if (Y.throwOnInvalid) throw new go(i);
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
        if (!e) throw new Fi(t);
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
            ? ft.create(this.loc, i).formatDurationFromString(this, t)
            : fl;
    }

    toHuman(t = {}) {
        if (!this.isValid) return fl;
        const e = t.showZeros !== !1;
        const i = ve
            .map((n) => {
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
            })
            .filter((n) => n);
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
                (t += Lo(this.seconds + this.milliseconds / 1e3, 3) + "S"),
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
              R.fromMillis(e, { zone: "UTC" }).toISOTime(t));
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
        return this.isValid ? bc(this.matrix, this.values) : NaN;
    }

    valueOf() {
        return this.toMillis();
    }

    plus(t) {
        if (!this.isValid) return this;
        const e = s.fromDurationLike(t);
        const i = {};
        for (const n of ve) {
            (es(e.values, n) || es(this.values, n)) &&
                (i[n] = e.get(n) + this.get(n));
        }
        return Ut(this, { values: i }, !0);
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
            e[i] = sc(t(this.values[i], i));
        }
        return Ut(this, { values: e }, !0);
    }

    get(t) {
        return this[s.normalizeUnit(t)];
    }

    set(t) {
        if (!this.isValid) return this;
        const e = { ...this.values, ...Wi(t, s.normalizeUnit) };
        return Ut(this, { values: e });
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
        return Ut(this, r);
    }

    as(t) {
        return this.isValid ? this.shiftTo(t).get(t) : NaN;
    }

    normalize() {
        if (!this.isValid) return this;
        const t = this.toObject();
        return (gl(this.matrix, t), Ut(this, { values: t }, !0));
    }

    rescale() {
        if (!this.isValid) return this;
        const t = ml(this.normalize().shiftToAll().toObject());
        return Ut(this, { values: t }, !0);
    }

    shiftTo(...t) {
        if (!this.isValid) return this;
        if (t.length === 0) return this;
        t = t.map((r) => s.normalizeUnit(r));
        const e = {};
        const i = {};
        const n = this.toObject();
        let o;
        for (const r of ve) {
            if (t.indexOf(r) >= 0) {
                o = r;
                let a = 0;
                for (const c in i) {
                    ((a += this.matrix[c][r] * i[c]), (i[c] = 0));
                }
                oe(n[r]) && (a += n[r]);
                const l = Math.trunc(a);
                ((e[r] = l), (i[r] = (a * 1e3 - l * 1e3) / 1e3));
            } else oe(n[r]) && (i[r] = n[r]);
        }
        for (const r in i) {
            i[r] !== 0 && (e[o] += r === o ? i[r] : i[r] / this.matrix[o][r]);
        }
        return (gl(this.matrix, e), Ut(this, { values: e }, !0));
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
        return Ut(this, { values: t }, !0);
    }

    removeZeros() {
        if (!this.isValid) return this;
        const t = ml(this.values);
        return Ut(this, { values: t }, !0);
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
        for (const i of ve) if (!e(this.values[i], t.values[i])) return !1;
        return !0;
    }
};
const Ge = "Invalid Interval";
function Cm(s, t) {
    return !s || !s.isValid
        ? ts.invalid("missing or invalid start")
        : !t || !t.isValid
          ? ts.invalid("missing or invalid end")
          : t < s
            ? ts.invalid(
                  "end before start",
                  `The end of an interval must be after its start, but you had start=${s.toISO()} and end=${t.toISO()}`,
              )
            : null;
}
var ts = class s {
    constructor(t) {
        ((this.s = t.start),
            (this.e = t.end),
            (this.invalid = t.invalid || null),
            (this.isLuxonInterval = !0));
    }

    static invalid(t, e = null) {
        if (!t) throw new Q("need to specify a reason the Interval is invalid");
        const i = t instanceof gt ? t : new gt(t, e);
        if (Y.throwOnInvalid) throw new fo(i);
        return new s({ invalid: i });
    }

    static fromDateTimes(t, e) {
        const i = Ls(t);
        const n = Ls(e);
        const o = Cm(i, n);
        return o ?? new s({ start: i, end: n });
    }

    static after(t, e) {
        const i = tt.fromDurationLike(e);
        const n = Ls(t);
        return s.fromDateTimes(n, n.plus(i));
    }

    static before(t, e) {
        const i = tt.fromDurationLike(e);
        const n = Ls(t);
        return s.fromDateTimes(n.minus(i), n);
    }

    static fromISO(t, e) {
        const [i, n] = (t || "").split("/", 2);
        if (i && n) {
            let o, r;
            try {
                ((o = R.fromISO(i, e)), (r = o.isValid));
            } catch {
                r = !1;
            }
            let a, l;
            try {
                ((a = R.fromISO(n, e)), (l = a.isValid));
            } catch {
                l = !1;
            }
            if (r && l) return s.fromDateTimes(o, a);
            if (r) {
                const c = tt.fromISO(n, e);
                if (c.isValid) return s.after(o, c);
            } else if (l) {
                const c = tt.fromISO(i, e);
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
            .map(Ls)
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
        const e = tt.fromDurationLike(t);
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
            : Ge;
    }

    [Symbol.for("nodejs.util.inspect.custom")]() {
        return this.isValid
            ? `Interval { start: ${this.s.toISO()}, end: ${this.e.toISO()} }`
            : `Interval { Invalid, reason: ${this.invalidReason} }`;
    }

    toLocaleString(t = Ri, e = {}) {
        return this.isValid
            ? ft.create(this.s.loc.clone(e), t).formatInterval(this)
            : Ge;
    }

    toISO(t) {
        return this.isValid ? `${this.s.toISO(t)}/${this.e.toISO(t)}` : Ge;
    }

    toISODate() {
        return this.isValid
            ? `${this.s.toISODate()}/${this.e.toISODate()}`
            : Ge;
    }

    toISOTime(t) {
        return this.isValid
            ? `${this.s.toISOTime(t)}/${this.e.toISOTime(t)}`
            : Ge;
    }

    toFormat(t, { separator: e = " \u2013 " } = {}) {
        return this.isValid
            ? `${this.s.toFormat(t)}${e}${this.e.toFormat(t)}`
            : Ge;
    }

    toDuration(t, e) {
        return this.isValid
            ? this.e.diff(this.s, t, e)
            : tt.invalid(this.invalidReason);
    }

    mapEndpoints(t) {
        return s.fromDateTimes(t(this.s), t(this.e));
    }
};
const Ke = class {
    static hasDST(t = Y.defaultZone) {
        const e = R.now().setZone(t).set({ month: 12 });
        return !t.isUniversal && e.offset !== e.set({ month: 6 }).offset;
    }

    static isValidIANAZone(t) {
        return re.isValidZone(t);
    }

    static normalizeZone(t) {
        return ie(t, Y.defaultZone);
    }

    static getStartOfWeek({ locale: t = null, locObj: e = null } = {}) {
        return (e || B.create(t)).getStartOfWeek();
    }

    static getMinimumDaysInFirstWeek({
        locale: t = null,
        locObj: e = null,
    } = {}) {
        return (e || B.create(t)).getMinDaysInFirstWeek();
    }

    static getWeekendWeekdays({ locale: t = null, locObj: e = null } = {}) {
        return (e || B.create(t)).getWeekendDays().slice();
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
        return (n || B.create(e, i, o)).months(t);
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
        return (n || B.create(e, i, o)).months(t, !0);
    }

    static weekdays(
        t = "long",
        { locale: e = null, numberingSystem: i = null, locObj: n = null } = {},
    ) {
        return (n || B.create(e, i, null)).weekdays(t);
    }

    static weekdaysFormat(
        t = "long",
        { locale: e = null, numberingSystem: i = null, locObj: n = null } = {},
    ) {
        return (n || B.create(e, i, null)).weekdays(t, !0);
    }

    static meridiems({ locale: t = null } = {}) {
        return B.create(t).meridiems();
    }

    static eras(t = "short", { locale: e = null } = {}) {
        return B.create(e, null, "gregory").eras(t);
    }

    static features() {
        return { relative: Ql(), localeWeek: tc() };
    }
};
function pl(s, t) {
    const e = (n) => n.toUTC(0, { keepLocalTime: !0 }).startOf("day").valueOf();
    const i = e(t) - e(s);
    return Math.floor(tt.fromMillis(i).as("days"));
}
function Pm(s, t, e) {
    const i = [
        ["years", (l, c) => c.year - l.year],
        ["quarters", (l, c) => c.quarter - l.quarter + (c.year - l.year) * 4],
        ["months", (l, c) => c.month - l.month + (c.year - l.year) * 12],
        [
            "weeks",
            (l, c) => {
                const h = pl(l, c);
                return (h - (h % 7)) / 7;
            },
        ],
        ["days", pl],
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
function Im(s, t, e, i) {
    let [n, o, r, a] = Pm(s, t, e);
    const l = t - n;
    const c = e.filter(
        (u) => ["hours", "minutes", "seconds", "milliseconds"].indexOf(u) >= 0,
    );
    c.length === 0 &&
        (r < t && (r = n.plus({ [a]: 1 })),
        r !== n && (o[a] = (o[a] || 0) + l / (r - n)));
    const h = tt.fromObject(o, i);
    return c.length > 0
        ? tt
              .fromMillis(l, i)
              .shiftTo(...c)
              .plus(h)
        : h;
}
const Am = "missing Intl.DateTimeFormat.formatToParts support";
function N(s, t = (e) => e) {
    return { regex: s, deser: ([e]) => t(kg(e)) };
}
const Em = "\xA0";
const yc = `[ ${Em}]`;
const xc = new RegExp(yc, "g");
function Lm(s) {
    return s.replace(/\./g, "\\.?").replace(xc, yc);
}
function bl(s) {
    return s.replace(/\./g, "").replace(xc, " ").toLowerCase();
}
function Ot(s, t) {
    return s === null
        ? null
        : {
              regex: RegExp(s.map(Lm).join("|")),
              deser: ([e]) => s.findIndex((i) => bl(e) === bl(i)) + t,
          };
}
function yl(s, t) {
    return { regex: s, deser: ([, e, i]) => ji(e, i), groups: t };
}
function Ci(s) {
    return { regex: s, deser: ([t]) => t };
}
function Fm(s) {
    return s.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&");
}
function Rm(s, t) {
    const e = Mt(t);
    const i = Mt(t, "{2}");
    const n = Mt(t, "{3}");
    const o = Mt(t, "{4}");
    const r = Mt(t, "{6}");
    const a = Mt(t, "{1,2}");
    const l = Mt(t, "{1,3}");
    const c = Mt(t, "{1,6}");
    const h = Mt(t, "{1,9}");
    const u = Mt(t, "{2,4}");
    const d = Mt(t, "{4,6}");
    const f = (p) => ({
        regex: RegExp(Fm(p.val)),
        deser: ([b]) => b,
        literal: !0,
    });
    const m = ((p) => {
        if (s.literal) return f(p);
        switch (p.val) {
            case "G":
                return Ot(t.eras("short"), 0);
            case "GG":
                return Ot(t.eras("long"), 0);
            case "y":
                return N(c);
            case "yy":
                return N(u, Do);
            case "yyyy":
                return N(o);
            case "yyyyy":
                return N(d);
            case "yyyyyy":
                return N(r);
            case "M":
                return N(a);
            case "MM":
                return N(i);
            case "MMM":
                return Ot(t.months("short", !0), 1);
            case "MMMM":
                return Ot(t.months("long", !0), 1);
            case "L":
                return N(a);
            case "LL":
                return N(i);
            case "LLL":
                return Ot(t.months("short", !1), 1);
            case "LLLL":
                return Ot(t.months("long", !1), 1);
            case "d":
                return N(a);
            case "dd":
                return N(i);
            case "o":
                return N(l);
            case "ooo":
                return N(n);
            case "HH":
                return N(i);
            case "H":
                return N(a);
            case "hh":
                return N(i);
            case "h":
                return N(a);
            case "mm":
                return N(i);
            case "m":
                return N(a);
            case "q":
                return N(a);
            case "qq":
                return N(i);
            case "s":
                return N(a);
            case "ss":
                return N(i);
            case "S":
                return N(l);
            case "SSS":
                return N(n);
            case "u":
                return Ci(h);
            case "uu":
                return Ci(a);
            case "uuu":
                return N(e);
            case "a":
                return Ot(t.meridiems(), 0);
            case "kkkk":
                return N(o);
            case "kk":
                return N(u, Do);
            case "W":
                return N(a);
            case "WW":
                return N(i);
            case "E":
            case "c":
                return N(e);
            case "EEE":
                return Ot(t.weekdays("short", !1), 1);
            case "EEEE":
                return Ot(t.weekdays("long", !1), 1);
            case "ccc":
                return Ot(t.weekdays("short", !0), 1);
            case "cccc":
                return Ot(t.weekdays("long", !0), 1);
            case "Z":
            case "ZZ":
                return yl(
                    new RegExp(`([+-]${a.source})(?::(${i.source}))?`),
                    2,
                );
            case "ZZZ":
                return yl(new RegExp(`([+-]${a.source})(${i.source})?`), 2);
            case "z":
                return Ci(/[a-z_+-/]{1,256}?/i);
            case " ":
                return Ci(/[^\S\n\r]/);
            default:
                return f(p);
        }
    })(s) || { invalidReason: Am };
    return ((m.token = s), m);
}
const Nm = {
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
function zm(s, t, e) {
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
    let a = Nm[r];
    if ((typeof a === "object" && (a = a[o]), a)) {
        return { literal: !1, val: a };
    }
}
function Vm(s) {
    return [
        `^${s.map((e) => e.regex).reduce((e, i) => `${e}(${i.source})`, "")}$`,
        s,
    ];
}
function Wm(s, t, e) {
    const i = s.match(t);
    if (i) {
        const n = {};
        let o = 1;
        for (const r in e) {
            if (es(e, r)) {
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
function Bm(s) {
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
        D(s.z) || (e = re.create(s.z)),
        D(s.Z) || (e || (e = new kt(s.Z)), (i = s.Z)),
        D(s.q) || (s.M = (s.q - 1) * 3 + 1),
        D(s.h) ||
            (s.h < 12 && s.a === 1
                ? (s.h += 12)
                : s.h === 12 && s.a === 0 && (s.h = 0)),
        s.G === 0 && s.y && (s.y = -s.y),
        D(s.u) || (s.S = Eo(s.u)),
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
let ro = null;
function Hm() {
    return (ro || (ro = R.fromMillis(1555555555555)), ro);
}
function $m(s, t) {
    if (s.literal) return s;
    const e = ft.macroTokenToFormatOpts(s.val);
    const i = kc(e, t);
    return i == null || i.includes(void 0) ? s : i;
}
function _c(s, t) {
    return Array.prototype.concat(...s.map((e) => $m(e, t)));
}
const Bi = class {
    constructor(t, e) {
        if (
            ((this.locale = t),
            (this.format = e),
            (this.tokens = _c(ft.parseFormat(e), t)),
            (this.units = this.tokens.map((i) => Rm(i, t))),
            (this.disqualifyingUnit = this.units.find((i) => i.invalidReason)),
            !this.disqualifyingUnit)
        ) {
            const [i, n] = Vm(this.units);
            ((this.regex = RegExp(i, "i")), (this.handlers = n));
        }
    }

    explainFromTokens(t) {
        if (this.isValid) {
            const [e, i] = Wm(t, this.regex, this.handlers);
            const [n, o, r] = i ? Bm(i) : [null, null, void 0];
            if (es(i, "a") && es(i, "H")) {
                throw new ne(
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
function wc(s, t, e) {
    return new Bi(s, e).explainFromTokens(t);
}
function jm(s, t, e) {
    const {
        result: i,
        zone: n,
        specificOffset: o,
        invalidReason: r,
    } = wc(s, t, e);
    return [i, n, o, r];
}
function kc(s, t) {
    if (!s) return null;
    const i = ft.create(t, s).dtFormatter(Hm());
    const n = i.formatToParts();
    const o = i.resolvedOptions();
    return n.map((r) => zm(r, s, o));
}
const ao = "Invalid DateTime";
const xl = 864e13;
function Rs(s) {
    return new gt("unsupported zone", `the zone "${s.name}" is not supported`);
}
function lo(s) {
    return (s.weekData === null && (s.weekData = zi(s.c)), s.weekData);
}
function co(s) {
    return (
        s.localWeekData === null &&
            (s.localWeekData = zi(
                s.c,
                s.loc.getMinDaysInFirstWeek(),
                s.loc.getStartOfWeek(),
            )),
        s.localWeekData
    );
}
function ke(s, t) {
    const e = {
        ts: s.ts,
        zone: s.zone,
        c: s.c,
        o: s.o,
        loc: s.loc,
        invalid: s.invalid,
    };
    return new R({ ...e, ...t, old: e });
}
function vc(s, t, e) {
    let i = s - t * 60 * 1e3;
    const n = e.offset(i);
    if (t === n) return [i, t];
    i -= (n - t) * 60 * 1e3;
    const o = e.offset(i);
    return n === o ? [i, n] : [s - Math.min(n, o) * 60 * 1e3, Math.max(n, o)];
}
function Pi(s, t) {
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
function Ai(s, t, e) {
    return vc($i(s), t, e);
}
function _l(s, t) {
    const e = s.o;
    const i = s.c.year + Math.trunc(t.years);
    const n = s.c.month + Math.trunc(t.months) + Math.trunc(t.quarters) * 3;
    const o = {
        ...s.c,
        year: i,
        month: n,
        day:
            Math.min(s.c.day, Vi(i, n)) +
            Math.trunc(t.days) +
            Math.trunc(t.weeks) * 7,
    };
    const r = tt
        .fromObject({
            years: t.years - Math.trunc(t.years),
            quarters: t.quarters - Math.trunc(t.quarters),
            months: t.months - Math.trunc(t.months),
            weeks: t.weeks - Math.trunc(t.weeks),
            days: t.days - Math.trunc(t.days),
            hours: t.hours,
            minutes: t.minutes,
            seconds: t.seconds,
            milliseconds: t.milliseconds,
        })
        .as("milliseconds");
    const a = $i(o);
    let [l, c] = vc(a, e, s.zone);
    return (r !== 0 && ((l += r), (c = s.zone.offset(l))), { ts: l, o: c });
}
function Xe(s, t, e, i, n, o) {
    const { setZone: r, zone: a } = e;
    if ((s && Object.keys(s).length !== 0) || t) {
        const l = t || a;
        const c = R.fromObject(s, { ...e, zone: l, specificOffset: o });
        return r ? c : c.setZone(a);
    } else {
        return R.invalid(
            new gt("unparsable", `the input "${n}" can't be parsed as ${i}`),
        );
    }
}
function Ii(s, t, e = !0) {
    return s.isValid
        ? ft
              .create(B.create("en-US"), { allowZ: e, forceSimple: !0 })
              .formatDateTimeFromString(s, t)
        : null;
}
function ho(s, t, e) {
    const i = s.c.year > 9999 || s.c.year < 0;
    let n = "";
    if (
        (i && s.c.year >= 0 && (n += "+"),
        (n += K(s.c.year, i ? 6 : 4)),
        e === "year")
    ) {
        return n;
    }
    if (t) {
        if (((n += "-"), (n += K(s.c.month)), e === "month")) return n;
        n += "-";
    } else if (((n += K(s.c.month)), e === "month")) return n;
    return ((n += K(s.c.day)), n);
}
function wl(s, t, e, i, n, o, r) {
    const a = !e || s.c.millisecond !== 0 || s.c.second !== 0;
    let l = "";
    switch (r) {
        case "day":
        case "month":
        case "year":
            break;
        default:
            if (((l += K(s.c.hour)), r === "hour")) break;
            if (t) {
                if (((l += ":"), (l += K(s.c.minute)), r === "minute")) break;
                a && ((l += ":"), (l += K(s.c.second)));
            } else {
                if (((l += K(s.c.minute)), r === "minute")) break;
                a && (l += K(s.c.second));
            }
            if (r === "second") break;
            a &&
                (!i || s.c.millisecond !== 0) &&
                ((l += "."), (l += K(s.c.millisecond, 3)));
    }
    return (
        n &&
            (s.isOffsetFixed && s.offset === 0 && !o
                ? (l += "Z")
                : s.o < 0
                  ? ((l += "-"),
                    (l += K(Math.trunc(-s.o / 60))),
                    (l += ":"),
                    (l += K(Math.trunc(-s.o % 60))))
                  : ((l += "+"),
                    (l += K(Math.trunc(s.o / 60))),
                    (l += ":"),
                    (l += K(Math.trunc(s.o % 60))))),
        o && (l += "[" + s.zone.ianaName + "]"),
        l
    );
}
const Sc = { month: 1, day: 1, hour: 0, minute: 0, second: 0, millisecond: 0 };
const Um = {
    weekNumber: 1,
    weekday: 1,
    hour: 0,
    minute: 0,
    second: 0,
    millisecond: 0,
};
const Ym = { ordinal: 1, hour: 0, minute: 0, second: 0, millisecond: 0 };
const Ei = ["year", "month", "day", "hour", "minute", "second", "millisecond"];
const Zm = [
    "weekYear",
    "weekNumber",
    "weekday",
    "hour",
    "minute",
    "second",
    "millisecond",
];
const qm = ["year", "ordinal", "hour", "minute", "second", "millisecond"];
function Li(s) {
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
    if (!t) throw new Fi(s);
    return t;
}
function kl(s) {
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
            return Li(s);
    }
}
function Gm(s) {
    if ((Ns === void 0 && (Ns = Y.now()), s.type !== "iana")) {
        return s.offset(Ns);
    }
    const t = s.name;
    let e = Co.get(t);
    return (e === void 0 && ((e = s.offset(Ns)), Co.set(t, e)), e);
}
function vl(s, t) {
    const e = ie(t.zone, Y.defaultZone);
    if (!e.isValid) return R.invalid(Rs(e));
    const i = B.fromObject(t);
    let n;
    let o;
    if (D(s.year)) n = Y.now();
    else {
        for (const l of Ei) D(s[l]) && (s[l] = Sc[l]);
        const r = Kl(s) || Jl(s);
        if (r) return R.invalid(r);
        const a = Gm(e);
        [n, o] = Ai(s, a, e);
    }
    return new R({ ts: n, zone: e, loc: i, o });
}
function Sl(s, t, e) {
    const i = D(e.round) ? !0 : e.round;
    const n = D(e.rounding) ? "trunc" : e.rounding;
    const o = (a, l) => (
        (a = Lo(a, i || e.calendary ? 0 : 2, e.calendary ? "round" : n)),
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
function Ml(s) {
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
let Ns;
var Co = new Map();
var R = class s {
    constructor(t) {
        const e = t.zone || Y.defaultZone;
        let i =
            t.invalid ||
            (Number.isNaN(t.ts) ? new gt("invalid input") : null) ||
            (e.isValid ? null : Rs(e));
        this.ts = D(t.ts) ? Y.now() : t.ts;
        let n = null;
        let o = null;
        if (!i) {
            if (t.old && t.old.ts === this.ts && t.old.zone.equals(e)) {
                [n, o] = [t.old.c, t.old.o];
            } else {
                const a = oe(t.o) && !t.old ? t.o : e.offset(this.ts);
                ((n = Pi(this.ts, a)),
                    (i = Number.isNaN(n.year) ? new gt("invalid input") : null),
                    (n = i ? null : n),
                    (o = i ? null : a));
            }
        }
        ((this._zone = e),
            (this.loc = t.loc || B.create()),
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
        const [t, e] = Ml(arguments);
        const [i, n, o, r, a, l, c] = e;
        return vl(
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
        const [t, e] = Ml(arguments);
        const [i, n, o, r, a, l, c] = e;
        return (
            (t.zone = kt.utcInstance),
            vl(
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
        const i = Tg(t) ? t.valueOf() : NaN;
        if (Number.isNaN(i)) return s.invalid("invalid input");
        const n = ie(e.zone, Y.defaultZone);
        return n.isValid
            ? new s({ ts: i, zone: n, loc: B.fromObject(e) })
            : s.invalid(Rs(n));
    }

    static fromMillis(t, e = {}) {
        if (oe(t)) {
            return t < -xl || t > xl
                ? s.invalid("Timestamp out of range")
                : new s({
                      ts: t,
                      zone: ie(e.zone, Y.defaultZone),
                      loc: B.fromObject(e),
                  });
        }
        throw new Q(
            `fromMillis requires a numerical input, but received a ${typeof t} with value ${t}`,
        );
    }

    static fromSeconds(t, e = {}) {
        if (oe(t)) {
            return new s({
                ts: t * 1e3,
                zone: ie(e.zone, Y.defaultZone),
                loc: B.fromObject(e),
            });
        }
        throw new Q("fromSeconds requires a numerical input");
    }

    static fromObject(t, e = {}) {
        t = t || {};
        const i = ie(e.zone, Y.defaultZone);
        if (!i.isValid) return s.invalid(Rs(i));
        const n = B.fromObject(e);
        const o = Wi(t, kl);
        const { minDaysInFirstWeek: r, startOfWeek: a } = ll(o, n);
        const l = Y.now();
        const c = D(e.specificOffset) ? i.offset(l) : e.specificOffset;
        const h = !D(o.ordinal);
        const u = !D(o.year);
        const d = !D(o.month) || !D(o.day);
        const f = u || d;
        const g = o.weekYear || o.weekNumber;
        if ((f || h) && g) {
            throw new ne(
                "Can't mix weekYear/weekNumber units with year/month/day or ordinals",
            );
        }
        if (d && h) throw new ne("Can't mix ordinal dates with month/day");
        const m = g || (o.weekday && !f);
        let p;
        let b;
        let y = Pi(l, c);
        m
            ? ((p = Zm), (b = Um), (y = zi(y, r, a)))
            : h
              ? ((p = qm), (b = Ym), (y = oo(y)))
              : ((p = Ei), (b = Sc));
        let _ = !1;
        for (const C of p) {
            const I = o[C];
            D(I) ? (_ ? (o[C] = b[C]) : (o[C] = y[C])) : (_ = !0);
        }
        const w = m ? Sg(o, r, a) : h ? Mg(o) : Kl(o);
        const x = w || Jl(o);
        if (x) return s.invalid(x);
        const v = m ? rl(o, r, a) : h ? al(o) : o;
        const [S, M] = Ai(v, c, i);
        const T = new s({ ts: S, zone: i, o: M, loc: n });
        return o.weekday && f && t.weekday !== T.weekday
            ? s.invalid(
                  "mismatched weekday",
                  `you can't specify both a weekday of ${o.weekday} and a date of ${T.toISO()}`,
              )
            : T.isValid
              ? T
              : s.invalid(T.invalid);
    }

    static fromISO(t, e = {}) {
        const [i, n] = pm(t);
        return Xe(i, n, e, "ISO 8601", t);
    }

    static fromRFC2822(t, e = {}) {
        const [i, n] = bm(t);
        return Xe(i, n, e, "RFC 2822", t);
    }

    static fromHTTP(t, e = {}) {
        const [i, n] = ym(t);
        return Xe(i, n, e, "HTTP", e);
    }

    static fromFormat(t, e, i = {}) {
        if (D(t) || D(e)) {
            throw new Q("fromFormat requires an input string and a format");
        }
        const { locale: n = null, numberingSystem: o = null } = i;
        const r = B.fromOpts({
            locale: n,
            numberingSystem: o,
            defaultToEN: !0,
        });
        const [a, l, c, h] = jm(r, t, e);
        return h ? s.invalid(h) : Xe(a, l, i, `format ${e}`, t, c);
    }

    static fromString(t, e, i = {}) {
        return s.fromFormat(t, e, i);
    }

    static fromSQL(t, e = {}) {
        const [i, n] = Mm(t);
        return Xe(i, n, e, "SQL", t);
    }

    static invalid(t, e = null) {
        if (!t) throw new Q("need to specify a reason the DateTime is invalid");
        const i = t instanceof gt ? t : new gt(t, e);
        if (Y.throwOnInvalid) throw new uo(i);
        return new s({ invalid: i });
    }

    static isDateTime(t) {
        return (t && t.isLuxonDateTime) || !1;
    }

    static parseFormatForOpts(t, e = {}) {
        const i = kc(t, B.fromObject(e));
        return i ? i.map((n) => (n ? n.val : null)).join("") : null;
    }

    static expandFormat(t, e = {}) {
        return _c(ft.parseFormat(t), B.fromObject(e))
            .map((n) => n.val)
            .join("");
    }

    static resetCache() {
        ((Ns = void 0), Co.clear());
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
        return this.isValid ? lo(this).weekYear : NaN;
    }

    get weekNumber() {
        return this.isValid ? lo(this).weekNumber : NaN;
    }

    get weekday() {
        return this.isValid ? lo(this).weekday : NaN;
    }

    get isWeekend() {
        return this.isValid && this.loc.getWeekendDays().includes(this.weekday);
    }

    get localWeekday() {
        return this.isValid ? co(this).weekday : NaN;
    }

    get localWeekNumber() {
        return this.isValid ? co(this).weekNumber : NaN;
    }

    get localWeekYear() {
        return this.isValid ? co(this).weekYear : NaN;
    }

    get ordinal() {
        return this.isValid ? oo(this.c).ordinal : NaN;
    }

    get monthShort() {
        return this.isValid
            ? Ke.months("short", { locObj: this.loc })[this.month - 1]
            : null;
    }

    get monthLong() {
        return this.isValid
            ? Ke.months("long", { locObj: this.loc })[this.month - 1]
            : null;
    }

    get weekdayShort() {
        return this.isValid
            ? Ke.weekdays("short", { locObj: this.loc })[this.weekday - 1]
            : null;
    }

    get weekdayLong() {
        return this.isValid
            ? Ke.weekdays("long", { locObj: this.loc })[this.weekday - 1]
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
        const i = $i(this.c);
        const n = this.zone.offset(i - t);
        const o = this.zone.offset(i + t);
        const r = this.zone.offset(i - n * e);
        const a = this.zone.offset(i - o * e);
        if (r === a) return [this];
        const l = i - r * e;
        const c = i - a * e;
        const h = Pi(l, r);
        const u = Pi(c, a);
        return h.hour === u.hour &&
            h.minute === u.minute &&
            h.second === u.second &&
            h.millisecond === u.millisecond
            ? [ke(this, { ts: l }), ke(this, { ts: c })]
            : [this];
    }

    get isInLeapYear() {
        return Ws(this.year);
    }

    get daysInMonth() {
        return Vi(this.year, this.month);
    }

    get daysInYear() {
        return this.isValid ? Je(this.year) : NaN;
    }

    get weeksInWeekYear() {
        return this.isValid ? Vs(this.weekYear) : NaN;
    }

    get weeksInLocalWeekYear() {
        return this.isValid
            ? Vs(
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
        } = ft.create(this.loc.clone(t), t).resolvedOptions(this);
        return { locale: e, numberingSystem: i, outputCalendar: n };
    }

    toUTC(t = 0, e = {}) {
        return this.setZone(kt.instance(t), e);
    }

    toLocal() {
        return this.setZone(Y.defaultZone);
    }

    setZone(t, { keepLocalTime: e = !1, keepCalendarTime: i = !1 } = {}) {
        if (((t = ie(t, Y.defaultZone)), t.equals(this.zone))) return this;
        if (t.isValid) {
            let n = this.ts;
            if (e || i) {
                const o = t.offset(this.ts);
                const r = this.toObject();
                [n] = Ai(r, o, t);
            }
            return ke(this, { ts: n, zone: t });
        } else return s.invalid(Rs(t));
    }

    reconfigure({ locale: t, numberingSystem: e, outputCalendar: i } = {}) {
        const n = this.loc.clone({
            locale: t,
            numberingSystem: e,
            outputCalendar: i,
        });
        return ke(this, { loc: n });
    }

    setLocale(t) {
        return this.reconfigure({ locale: t });
    }

    set(t) {
        if (!this.isValid) return this;
        const e = Wi(t, kl);
        const { minDaysInFirstWeek: i, startOfWeek: n } = ll(e, this.loc);
        const o = !D(e.weekYear) || !D(e.weekNumber) || !D(e.weekday);
        const r = !D(e.ordinal);
        const a = !D(e.year);
        const l = !D(e.month) || !D(e.day);
        const c = a || l;
        const h = e.weekYear || e.weekNumber;
        if ((c || r) && h) {
            throw new ne(
                "Can't mix weekYear/weekNumber units with year/month/day or ordinals",
            );
        }
        if (l && r) throw new ne("Can't mix ordinal dates with month/day");
        let u;
        o
            ? (u = rl({ ...zi(this.c, i, n), ...e }, i, n))
            : D(e.ordinal)
              ? ((u = { ...this.toObject(), ...e }),
                D(e.day) && (u.day = Math.min(Vi(u.year, u.month), u.day)))
              : (u = al({ ...oo(this.c), ...e }));
        const [d, f] = Ai(u, this.o, this.zone);
        return ke(this, { ts: d, o: f });
    }

    plus(t) {
        if (!this.isValid) return this;
        const e = tt.fromDurationLike(t);
        return ke(this, _l(this, e));
    }

    minus(t) {
        if (!this.isValid) return this;
        const e = tt.fromDurationLike(t).negate();
        return ke(this, _l(this, e));
    }

    startOf(t, { useLocaleWeeks: e = !1 } = {}) {
        if (!this.isValid) return this;
        const i = {};
        const n = tt.normalizeUnit(t);
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
            ? ft
                  .create(this.loc.redefaultToEN(e))
                  .formatDateTimeFromString(this, t)
            : ao;
    }

    toLocaleString(t = Ri, e = {}) {
        return this.isValid
            ? ft.create(this.loc.clone(e), t).formatDateTime(this)
            : ao;
    }

    toLocaleParts(t = {}) {
        return this.isValid
            ? ft.create(this.loc.clone(t), t).formatDateTimeParts(this)
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
        r = Li(r);
        const a = t === "extended";
        let l = ho(this, a, r);
        return (
            Ei.indexOf(r) >= 3 && (l += "T"),
            (l += wl(this, a, e, i, n, o, r)),
            l
        );
    }

    toISODate({ format: t = "extended", precision: e = "day" } = {}) {
        return this.isValid ? ho(this, t === "extended", Li(e)) : null;
    }

    toISOWeekDate() {
        return Ii(this, "kkkk-'W'WW-c");
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
            ? ((a = Li(a)),
              (n && Ei.indexOf(a) >= 3 ? "T" : "") +
                  wl(this, r === "extended", e, t, i, o, a))
            : null;
    }

    toRFC2822() {
        return Ii(this, "EEE, dd LLL yyyy HH:mm:ss ZZZ", !1);
    }

    toHTTP() {
        return Ii(this.toUTC(), "EEE, dd LLL yyyy HH:mm:ss 'GMT'");
    }

    toSQLDate() {
        return this.isValid ? ho(this, !0) : null;
    }

    toSQLTime({
        includeOffset: t = !0,
        includeZone: e = !1,
        includeOffsetSpace: i = !0,
    } = {}) {
        let n = "HH:mm:ss.SSS";
        return (
            (e || t) && (i && (n += " "), e ? (n += "z") : t && (n += "ZZ")),
            Ii(this, n, !0)
        );
    }

    toSQL(t = {}) {
        return this.isValid ? `${this.toSQLDate()} ${this.toSQLTime(t)}` : null;
    }

    toString() {
        return this.isValid ? this.toISO() : ao;
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
            return tt.invalid("created by diffing an invalid DateTime");
        }
        const n = {
            locale: this.locale,
            numberingSystem: this.numberingSystem,
            ...i,
        };
        const o = Dg(e).map(tt.normalizeUnit);
        const r = t.valueOf() > this.valueOf();
        const a = r ? this : t;
        const l = r ? t : this;
        const c = Im(a, l, o, n);
        return r ? c.negate() : c;
    }

    diffNow(t = "milliseconds", e = {}) {
        return this.diff(s.now(), t, e);
    }

    until(t) {
        return this.isValid ? ts.fromDateTimes(this, t) : this;
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
            Sl(e, this.plus(i), { ...t, numeric: "always", units: n, unit: o })
        );
    }

    toRelativeCalendar(t = {}) {
        return this.isValid
            ? Sl(t.base || s.fromObject({}, { zone: this.zone }), this, {
                  ...t,
                  numeric: "auto",
                  units: ["years", "months", "days"],
                  calendary: !0,
              })
            : null;
    }

    static min(...t) {
        if (!t.every(s.isDateTime)) {
            throw new Q("min requires all arguments be DateTimes");
        }
        return cl(t, (e) => e.valueOf(), Math.min);
    }

    static max(...t) {
        if (!t.every(s.isDateTime)) {
            throw new Q("max requires all arguments be DateTimes");
        }
        return cl(t, (e) => e.valueOf(), Math.max);
    }

    static fromFormatExplain(t, e, i = {}) {
        const { locale: n = null, numberingSystem: o = null } = i;
        const r = B.fromOpts({
            locale: n,
            numberingSystem: o,
            defaultToEN: !0,
        });
        return wc(r, t, e);
    }

    static fromStringExplain(t, e, i = {}) {
        return s.fromFormatExplain(t, e, i);
    }

    static buildFormatParser(t, e = {}) {
        const { locale: i = null, numberingSystem: n = null } = e;
        const o = B.fromOpts({
            locale: i,
            numberingSystem: n,
            defaultToEN: !0,
        });
        return new Bi(o, t);
    }

    static fromFormatParser(t, e, i = {}) {
        if (D(t) || D(e)) {
            throw new Q(
                "fromFormatParser requires an input string and a format parser",
            );
        }
        const { locale: n = null, numberingSystem: o = null } = i;
        const r = B.fromOpts({
            locale: n,
            numberingSystem: o,
            defaultToEN: !0,
        });
        if (!r.equals(e.locale)) {
            throw new Q(
                `fromFormatParser called with a locale of ${r}, but the format parser was created for ${e.locale}`,
            );
        }
        const {
            result: a,
            zone: l,
            specificOffset: c,
            invalidReason: h,
        } = e.explainFromTokens(t);
        return h ? s.invalid(h) : Xe(a, l, i, `format ${e.format}`, t, c);
    }

    static get DATE_SHORT() {
        return Ri;
    }

    static get DATE_MED() {
        return Ol;
    }

    static get DATE_MED_WITH_WEEKDAY() {
        return og;
    }

    static get DATE_FULL() {
        return Tl;
    }

    static get DATE_HUGE() {
        return Dl;
    }

    static get TIME_SIMPLE() {
        return Cl;
    }

    static get TIME_WITH_SECONDS() {
        return Pl;
    }

    static get TIME_WITH_SHORT_OFFSET() {
        return Il;
    }

    static get TIME_WITH_LONG_OFFSET() {
        return Al;
    }

    static get TIME_24_SIMPLE() {
        return El;
    }

    static get TIME_24_WITH_SECONDS() {
        return Ll;
    }

    static get TIME_24_WITH_SHORT_OFFSET() {
        return Fl;
    }

    static get TIME_24_WITH_LONG_OFFSET() {
        return Rl;
    }

    static get DATETIME_SHORT() {
        return Nl;
    }

    static get DATETIME_SHORT_WITH_SECONDS() {
        return zl;
    }

    static get DATETIME_MED() {
        return Vl;
    }

    static get DATETIME_MED_WITH_SECONDS() {
        return Wl;
    }

    static get DATETIME_MED_WITH_WEEKDAY() {
        return rg;
    }

    static get DATETIME_FULL() {
        return Bl;
    }

    static get DATETIME_FULL_WITH_SECONDS() {
        return Hl;
    }

    static get DATETIME_HUGE() {
        return $l;
    }

    static get DATETIME_HUGE_WITH_SECONDS() {
        return jl;
    }
};
function Ls(s) {
    if (R.isDateTime(s)) return s;
    if (s && s.valueOf && oe(s.valueOf())) return R.fromJSDate(s);
    if (s && typeof s === "object") return R.fromObject(s);
    throw new Q(`Unknown datetime argument: ${s}, of type ${typeof s}`);
}
const Xm = {
    datetime: R.DATETIME_MED_WITH_SECONDS,
    millisecond: "h:mm:ss.SSS a",
    second: R.TIME_WITH_SECONDS,
    minute: R.TIME_SIMPLE,
    hour: { hour: "numeric" },
    day: { day: "numeric", month: "short" },
    week: "DD",
    month: { month: "short", year: "numeric" },
    quarter: "'Q'q - yyyy",
    year: { year: "numeric" },
};
eo._date.override({
    _id: "luxon",
    _create: function (s) {
        return R.fromMillis(s, this.options);
    },
    init(s) {
        this.options.locale || (this.options.locale = s.locale);
    },
    formats: function () {
        return Xm;
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
                        ? (s = R.fromFormat(s, t, e))
                        : (s = R.fromISO(s, e))
                    : s instanceof Date
                      ? (s = R.fromJSDate(s, e))
                      : i === "object" &&
                        !(s instanceof R) &&
                        (s = R.fromObject(s, e)),
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
function Yi({ cachedData: s, options: t, type: e }) {
    return {
        init() {
            (this.initChart(),
                this.$wire.$on("updateChartData", ({ data: i }) => {
                    ((Yi = this.getChart()),
                        (Yi.data = i),
                        Yi.update("resize"));
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
            let r, a, l, c, h, u, d, f, g, m, p, b, y, _;
            if (
                !this.$refs.canvas ||
                !this.$refs.backgroundColorElement ||
                !this.$refs.borderColorElement ||
                !this.$refs.textColorElement ||
                !this.$refs.gridColorElement
            ) {
                return;
            }
            ((Ft.defaults.animation.duration = 0),
                (Ft.defaults.backgroundColor = getComputedStyle(
                    this.$refs.backgroundColorElement,
                ).color));
            const n = getComputedStyle(this.$refs.borderColorElement).color;
            ((Ft.defaults.borderColor = n),
                (Ft.defaults.color = getComputedStyle(
                    this.$refs.textColorElement,
                ).color),
                (Ft.defaults.font.family = getComputedStyle(
                    this.$el,
                ).fontFamily),
                (Ft.defaults.plugins.legend.labels.boxWidth = 12),
                (Ft.defaults.plugins.legend.position = "bottom"));
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
                (g = t.scales.y.border).display ?? (g.display = !1),
                (m = t.scales.y).grid ?? (m.grid = {}),
                (p = t.scales.y.grid).color ?? (p.color = o),
                ["doughnut", "pie"].includes(e) &&
                    ((b = t.scales.x).display ?? (b.display = !1),
                    (y = t.scales.y).display ?? (y.display = !1),
                    (_ = t.scales.y.grid).display ?? (_.display = !1)),
                new Ft(this.$refs.canvas, {
                    type: e,
                    data: i ?? s,
                    options: t,
                    plugins: window.filamentChartJsPlugins ?? [],
                })
            );
        },
        getChart() {
            return this.$refs.canvas ? Ft.getChart(this.$refs.canvas) : null;
        },
    };
}
export { Yi as default };
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
