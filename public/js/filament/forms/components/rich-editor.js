function he(t) {
    this.content = t;
}
he.prototype = {
    constructor: he,
    find: function (t) {
        for (let e = 0; e < this.content.length; e += 2) {
            if (this.content[e] === t) return e;
        }
        return -1;
    },
    get: function (t) {
        const e = this.find(t);
        return e == -1 ? void 0 : this.content[e + 1];
    },
    update: function (t, e, n) {
        const r = n && n != t ? this.remove(n) : this;
        const o = r.find(t);
        const i = r.content.slice();
        return (
            o == -1 ? i.push(n || t, e) : ((i[o + 1] = e), n && (i[o] = n)),
            new he(i)
        );
    },
    remove: function (t) {
        const e = this.find(t);
        if (e == -1) return this;
        const n = this.content.slice();
        return (n.splice(e, 2), new he(n));
    },
    addToStart: function (t, e) {
        return new he([t, e].concat(this.remove(t).content));
    },
    addToEnd: function (t, e) {
        const n = this.remove(t).content.slice();
        return (n.push(t, e), new he(n));
    },
    addBefore: function (t, e, n) {
        const r = this.remove(e);
        const o = r.content.slice();
        const i = r.find(t);
        return (o.splice(i == -1 ? o.length : i, 0, e, n), new he(o));
    },
    forEach: function (t) {
        for (let e = 0; e < this.content.length; e += 2) {
            t(this.content[e], this.content[e + 1]);
        }
    },
    prepend: function (t) {
        return (
            (t = he.from(t)),
            t.size ? new he(t.content.concat(this.subtract(t).content)) : this
        );
    },
    append: function (t) {
        return (
            (t = he.from(t)),
            t.size ? new he(this.subtract(t).content.concat(t.content)) : this
        );
    },
    subtract: function (t) {
        let e = this;
        t = he.from(t);
        for (let n = 0; n < t.content.length; n += 2) {
            e = e.remove(t.content[n]);
        }
        return e;
    },
    toObject: function () {
        const t = {};
        return (
            this.forEach(function (e, n) {
                t[e] = n;
            }),
            t
        );
    },
    get size() {
        return this.content.length >> 1;
    },
};
he.from = function (t) {
    if (t instanceof he) return t;
    const e = [];
    if (t) for (const n in t) e.push(n, t[n]);
    return new he(e);
};
const Ci = he;
function ya(t, e, n) {
    for (let r = 0; ; r++) {
        if (r == t.childCount || r == e.childCount) {
            return t.childCount == e.childCount ? null : n;
        }
        const o = t.child(r);
        const i = e.child(r);
        if (o == i) {
            n += o.nodeSize;
            continue;
        }
        if (!o.sameMarkup(i)) return n;
        if (o.isText && o.text != i.text) {
            for (let s = 0; o.text[s] == i.text[s]; s++) n++;
            return n;
        }
        if (o.content.size || i.content.size) {
            const s = ya(o.content, i.content, n + 1);
            if (s != null) return s;
        }
        n += o.nodeSize;
    }
}
function ba(t, e, n, r) {
    for (let o = t.childCount, i = e.childCount; ; ) {
        if (o == 0 || i == 0) return o == i ? null : { a: n, b: r };
        const s = t.child(--o);
        const l = e.child(--i);
        const a = s.nodeSize;
        if (s == l) {
            ((n -= a), (r -= a));
            continue;
        }
        if (!s.sameMarkup(l)) return { a: n, b: r };
        if (s.isText && s.text != l.text) {
            let c = 0;
            const d = Math.min(s.text.length, l.text.length);
            for (
                ;
                c < d &&
                s.text[s.text.length - c - 1] == l.text[l.text.length - c - 1];

            ) {
                (c++, n--, r--);
            }
            return { a: n, b: r };
        }
        if (s.content.size || l.content.size) {
            const c = ba(s.content, l.content, n - 1, r - 1);
            if (c) return c;
        }
        ((n -= a), (r -= a));
    }
}
const C = class t {
    constructor(e, n) {
        if (((this.content = e), (this.size = n || 0), n == null)) {
            for (let r = 0; r < e.length; r++) this.size += e[r].nodeSize;
        }
    }

    nodesBetween(e, n, r, o = 0, i) {
        for (let s = 0, l = 0; l < n; s++) {
            const a = this.content[s];
            const c = l + a.nodeSize;
            if (c > e && r(a, o + l, i || null, s) !== !1 && a.content.size) {
                const d = l + 1;
                a.nodesBetween(
                    Math.max(0, e - d),
                    Math.min(a.content.size, n - d),
                    r,
                    o + d,
                );
            }
            l = c;
        }
    }

    descendants(e) {
        this.nodesBetween(0, this.size, e);
    }

    textBetween(e, n, r, o) {
        let i = "";
        let s = !0;
        return (
            this.nodesBetween(
                e,
                n,
                (l, a) => {
                    const c = l.isText
                        ? l.text.slice(Math.max(e, a) - a, n - a)
                        : l.isLeaf
                          ? o
                              ? typeof o === "function"
                                  ? o(l)
                                  : o
                              : l.type.spec.leafText
                                ? l.type.spec.leafText(l)
                                : ""
                          : "";
                    (l.isBlock &&
                        ((l.isLeaf && c) || l.isTextblock) &&
                        r &&
                        (s ? (s = !1) : (i += r)),
                        (i += c));
                },
                0,
            ),
            i
        );
    }

    append(e) {
        if (!e.size) return this;
        if (!this.size) return e;
        const n = this.lastChild;
        const r = e.firstChild;
        const o = this.content.slice();
        let i = 0;
        for (
            n.isText &&
            n.sameMarkup(r) &&
            ((o[o.length - 1] = n.withText(n.text + r.text)), (i = 1));
            i < e.content.length;
            i++
        ) {
            o.push(e.content[i]);
        }
        return new t(o, this.size + e.size);
    }

    cut(e, n = this.size) {
        if (e == 0 && n == this.size) return this;
        const r = [];
        let o = 0;
        if (n > e) {
            for (let i = 0, s = 0; s < n; i++) {
                let l = this.content[i];
                const a = s + l.nodeSize;
                (a > e &&
                    ((s < e || a > n) &&
                        (l.isText
                            ? (l = l.cut(
                                  Math.max(0, e - s),
                                  Math.min(l.text.length, n - s),
                              ))
                            : (l = l.cut(
                                  Math.max(0, e - s - 1),
                                  Math.min(l.content.size, n - s - 1),
                              ))),
                    r.push(l),
                    (o += l.nodeSize)),
                    (s = a));
            }
        }
        return new t(r, o);
    }

    cutByIndex(e, n) {
        return e == n
            ? t.empty
            : e == 0 && n == this.content.length
              ? this
              : new t(this.content.slice(e, n));
    }

    replaceChild(e, n) {
        const r = this.content[e];
        if (r == n) return this;
        const o = this.content.slice();
        const i = this.size + n.nodeSize - r.nodeSize;
        return ((o[e] = n), new t(o, i));
    }

    addToStart(e) {
        return new t([e].concat(this.content), this.size + e.nodeSize);
    }

    addToEnd(e) {
        return new t(this.content.concat(e), this.size + e.nodeSize);
    }

    eq(e) {
        if (this.content.length != e.content.length) return !1;
        for (let n = 0; n < this.content.length; n++) {
            if (!this.content[n].eq(e.content[n])) return !1;
        }
        return !0;
    }

    get firstChild() {
        return this.content.length ? this.content[0] : null;
    }

    get lastChild() {
        return this.content.length
            ? this.content[this.content.length - 1]
            : null;
    }

    get childCount() {
        return this.content.length;
    }

    child(e) {
        const n = this.content[e];
        if (!n) {
            throw new RangeError("Index " + e + " out of range for " + this);
        }
        return n;
    }

    maybeChild(e) {
        return this.content[e] || null;
    }

    forEach(e) {
        for (let n = 0, r = 0; n < this.content.length; n++) {
            const o = this.content[n];
            (e(o, r, n), (r += o.nodeSize));
        }
    }

    findDiffStart(e, n = 0) {
        return ya(this, e, n);
    }

    findDiffEnd(e, n = this.size, r = e.size) {
        return ba(this, e, n, r);
    }

    findIndex(e) {
        if (e == 0) return Cr(0, e);
        if (e == this.size) return Cr(this.content.length, e);
        if (e > this.size || e < 0) {
            throw new RangeError(`Position ${e} outside of fragment (${this})`);
        }
        for (let n = 0, r = 0; ; n++) {
            const o = this.child(n);
            const i = r + o.nodeSize;
            if (i >= e) return i == e ? Cr(n + 1, i) : Cr(n, r);
            r = i;
        }
    }

    toString() {
        return "<" + this.toStringInner() + ">";
    }

    toStringInner() {
        return this.content.join(", ");
    }

    toJSON() {
        return this.content.length ? this.content.map((e) => e.toJSON()) : null;
    }

    static fromJSON(e, n) {
        if (!n) return t.empty;
        if (!Array.isArray(n)) {
            throw new RangeError("Invalid input for Fragment.fromJSON");
        }
        return new t(n.map(e.nodeFromJSON));
    }

    static fromArray(e) {
        if (!e.length) return t.empty;
        let n;
        let r = 0;
        for (let o = 0; o < e.length; o++) {
            const i = e[o];
            ((r += i.nodeSize),
                o && i.isText && e[o - 1].sameMarkup(i)
                    ? (n || (n = e.slice(0, o)),
                      (n[n.length - 1] = i.withText(
                          n[n.length - 1].text + i.text,
                      )))
                    : n && n.push(i));
        }
        return new t(n || e, r);
    }

    static from(e) {
        if (!e) return t.empty;
        if (e instanceof t) return e;
        if (Array.isArray(e)) return this.fromArray(e);
        if (e.attrs) return new t([e], e.nodeSize);
        throw new RangeError(
            "Can not convert " +
                e +
                " to a Fragment" +
                (e.nodesBetween
                    ? " (looks like multiple versions of prosemirror-model were loaded)"
                    : ""),
        );
    }
};
C.empty = new C([], 0);
const vi = { index: 0, offset: 0 };
function Cr(t, e) {
    return ((vi.index = t), (vi.offset = e), vi);
}
function Mr(t, e) {
    if (t === e) return !0;
    if (!(t && typeof t === "object") || !(e && typeof e === "object")) {
        return !1;
    }
    const n = Array.isArray(t);
    if (Array.isArray(e) != n) return !1;
    if (n) {
        if (t.length != e.length) return !1;
        for (let r = 0; r < t.length; r++) if (!Mr(t[r], e[r])) return !1;
    } else {
        for (const r in t) if (!(r in e) || !Mr(t[r], e[r])) return !1;
        for (const r in e) if (!(r in t)) return !1;
    }
    return !0;
}
const q = class t {
    constructor(e, n) {
        ((this.type = e), (this.attrs = n));
    }

    addToSet(e) {
        let n;
        let r = !1;
        for (let o = 0; o < e.length; o++) {
            const i = e[o];
            if (this.eq(i)) return e;
            if (this.type.excludes(i.type)) n || (n = e.slice(0, o));
            else {
                if (i.type.excludes(this.type)) return e;
                (!r &&
                    i.type.rank > this.type.rank &&
                    (n || (n = e.slice(0, o)), n.push(this), (r = !0)),
                    n && n.push(i));
            }
        }
        return (n || (n = e.slice()), r || n.push(this), n);
    }

    removeFromSet(e) {
        for (let n = 0; n < e.length; n++) {
            if (this.eq(e[n])) return e.slice(0, n).concat(e.slice(n + 1));
        }
        return e;
    }

    isInSet(e) {
        for (let n = 0; n < e.length; n++) if (this.eq(e[n])) return !0;
        return !1;
    }

    eq(e) {
        return this == e || (this.type == e.type && Mr(this.attrs, e.attrs));
    }

    toJSON() {
        const e = { type: this.type.name };
        for (const n in this.attrs) {
            e.attrs = this.attrs;
            break;
        }
        return e;
    }

    static fromJSON(e, n) {
        if (!n) throw new RangeError("Invalid input for Mark.fromJSON");
        const r = e.marks[n.type];
        if (!r) {
            throw new RangeError(
                `There is no mark type ${n.type} in this schema`,
            );
        }
        const o = r.create(n.attrs);
        return (r.checkAttrs(o.attrs), o);
    }

    static sameSet(e, n) {
        if (e == n) return !0;
        if (e.length != n.length) return !1;
        for (let r = 0; r < e.length; r++) if (!e[r].eq(n[r])) return !1;
        return !0;
    }

    static setFrom(e) {
        if (!e || (Array.isArray(e) && e.length == 0)) return t.none;
        if (e instanceof t) return [e];
        const n = e.slice();
        return (n.sort((r, o) => r.type.rank - o.type.rank), n);
    }
};
q.none = [];
const Bt = class extends Error {};
const E = class t {
    constructor(e, n, r) {
        ((this.content = e), (this.openStart = n), (this.openEnd = r));
    }

    get size() {
        return this.content.size - this.openStart - this.openEnd;
    }

    insertAt(e, n) {
        const r = xa(this.content, e + this.openStart, n);
        return r && new t(r, this.openStart, this.openEnd);
    }

    removeBetween(e, n) {
        return new t(
            wa(this.content, e + this.openStart, n + this.openStart),
            this.openStart,
            this.openEnd,
        );
    }

    eq(e) {
        return (
            this.content.eq(e.content) &&
            this.openStart == e.openStart &&
            this.openEnd == e.openEnd
        );
    }

    toString() {
        return this.content + "(" + this.openStart + "," + this.openEnd + ")";
    }

    toJSON() {
        if (!this.content.size) return null;
        const e = { content: this.content.toJSON() };
        return (
            this.openStart > 0 && (e.openStart = this.openStart),
            this.openEnd > 0 && (e.openEnd = this.openEnd),
            e
        );
    }

    static fromJSON(e, n) {
        if (!n) return t.empty;
        const r = n.openStart || 0;
        const o = n.openEnd || 0;
        if (typeof r !== "number" || typeof o !== "number") {
            throw new RangeError("Invalid input for Slice.fromJSON");
        }
        return new t(C.fromJSON(e, n.content), r, o);
    }

    static maxOpen(e, n = !0) {
        let r = 0;
        let o = 0;
        for (
            let i = e.firstChild;
            i && !i.isLeaf && (n || !i.type.spec.isolating);
            i = i.firstChild
        ) {
            r++;
        }
        for (
            let i = e.lastChild;
            i && !i.isLeaf && (n || !i.type.spec.isolating);
            i = i.lastChild
        ) {
            o++;
        }
        return new t(e, r, o);
    }
};
E.empty = new E(C.empty, 0, 0);
function wa(t, e, n) {
    const { index: r, offset: o } = t.findIndex(e);
    const i = t.maybeChild(r);
    const { index: s, offset: l } = t.findIndex(n);
    if (o == e || i.isText) {
        if (l != n && !t.child(s).isText) {
            throw new RangeError("Removing non-flat range");
        }
        return t.cut(0, e).append(t.cut(n));
    }
    if (r != s) throw new RangeError("Removing non-flat range");
    return t.replaceChild(r, i.copy(wa(i.content, e - o - 1, n - o - 1)));
}
function xa(t, e, n, r) {
    const { index: o, offset: i } = t.findIndex(e);
    const s = t.maybeChild(o);
    if (i == e || s.isText) {
        return r && !r.canReplace(o, o, n)
            ? null
            : t.cut(0, e).append(n).append(t.cut(e));
    }
    const l = xa(s.content, e - i - 1, n, s);
    return l && t.replaceChild(o, s.copy(l));
}
function Qh(t, e, n) {
    if (n.openStart > t.depth) {
        throw new Bt("Inserted content deeper than insertion position");
    }
    if (t.depth - n.openStart != e.depth - n.openEnd) {
        throw new Bt("Inconsistent open depths");
    }
    return ka(t, e, n, 0);
}
function ka(t, e, n, r) {
    const o = t.index(r);
    const i = t.node(r);
    if (o == e.index(r) && r < t.depth - n.openStart) {
        const s = ka(t, e, n, r + 1);
        return i.copy(i.content.replaceChild(o, s));
    } else if (n.content.size) {
        if (!n.openStart && !n.openEnd && t.depth == r && e.depth == r) {
            const s = t.parent;
            const l = s.content;
            return zt(
                s,
                l
                    .cut(0, t.parentOffset)
                    .append(n.content)
                    .append(l.cut(e.parentOffset)),
            );
        } else {
            const { start: s, end: l } = Zh(n, t);
            return zt(i, Ca(t, s, l, e, r));
        }
    } else return zt(i, Tr(t, e, r));
}
function Sa(t, e) {
    if (!e.type.compatibleContent(t.type)) {
        throw new Bt("Cannot join " + e.type.name + " onto " + t.type.name);
    }
}
function Ti(t, e, n) {
    const r = t.node(n);
    return (Sa(r, e.node(n)), r);
}
function Lt(t, e) {
    const n = e.length - 1;
    n >= 0 && t.isText && t.sameMarkup(e[n])
        ? (e[n] = t.withText(e[n].text + t.text))
        : e.push(t);
}
function Rn(t, e, n, r) {
    const o = (e || t).node(n);
    let i = 0;
    const s = e ? e.index(n) : o.childCount;
    t &&
        ((i = t.index(n)),
        t.depth > n ? i++ : t.textOffset && (Lt(t.nodeAfter, r), i++));
    for (let l = i; l < s; l++) Lt(o.child(l), r);
    e && e.depth == n && e.textOffset && Lt(e.nodeBefore, r);
}
function zt(t, e) {
    return (t.type.checkContent(e), t.copy(e));
}
function Ca(t, e, n, r, o) {
    const i = t.depth > o && Ti(t, e, o + 1);
    const s = r.depth > o && Ti(n, r, o + 1);
    const l = [];
    return (
        Rn(null, t, o, l),
        i && s && e.index(o) == n.index(o)
            ? (Sa(i, s), Lt(zt(i, Ca(t, e, n, r, o + 1)), l))
            : (i && Lt(zt(i, Tr(t, e, o + 1)), l),
              Rn(e, n, o, l),
              s && Lt(zt(s, Tr(n, r, o + 1)), l)),
        Rn(r, null, o, l),
        new C(l)
    );
}
function Tr(t, e, n) {
    const r = [];
    if ((Rn(null, t, n, r), t.depth > n)) {
        const o = Ti(t, e, n + 1);
        Lt(zt(o, Tr(t, e, n + 1)), r);
    }
    return (Rn(e, null, n, r), new C(r));
}
function Zh(t, e) {
    const n = e.depth - t.openStart;
    let o = e.node(n).copy(t.content);
    for (let i = n - 1; i >= 0; i--) o = e.node(i).copy(C.from(o));
    return {
        start: o.resolveNoCache(t.openStart + n),
        end: o.resolveNoCache(o.content.size - t.openEnd - n),
    };
}
const Ar = class t {
    constructor(e, n, r) {
        ((this.pos = e),
            (this.path = n),
            (this.parentOffset = r),
            (this.depth = n.length / 3 - 1));
    }

    resolveDepth(e) {
        return e == null ? this.depth : e < 0 ? this.depth + e : e;
    }

    get parent() {
        return this.node(this.depth);
    }

    get doc() {
        return this.node(0);
    }

    node(e) {
        return this.path[this.resolveDepth(e) * 3];
    }

    index(e) {
        return this.path[this.resolveDepth(e) * 3 + 1];
    }

    indexAfter(e) {
        return (
            (e = this.resolveDepth(e)),
            this.index(e) + (e == this.depth && !this.textOffset ? 0 : 1)
        );
    }

    start(e) {
        return (
            (e = this.resolveDepth(e)),
            e == 0 ? 0 : this.path[e * 3 - 1] + 1
        );
    }

    end(e) {
        return (
            (e = this.resolveDepth(e)),
            this.start(e) + this.node(e).content.size
        );
    }

    before(e) {
        if (((e = this.resolveDepth(e)), !e)) {
            throw new RangeError(
                "There is no position before the top-level node",
            );
        }
        return e == this.depth + 1 ? this.pos : this.path[e * 3 - 1];
    }

    after(e) {
        if (((e = this.resolveDepth(e)), !e)) {
            throw new RangeError(
                "There is no position after the top-level node",
            );
        }
        return e == this.depth + 1
            ? this.pos
            : this.path[e * 3 - 1] + this.path[e * 3].nodeSize;
    }

    get textOffset() {
        return this.pos - this.path[this.path.length - 1];
    }

    get nodeAfter() {
        const e = this.parent;
        const n = this.index(this.depth);
        if (n == e.childCount) return null;
        const r = this.pos - this.path[this.path.length - 1];
        const o = e.child(n);
        return r ? e.child(n).cut(r) : o;
    }

    get nodeBefore() {
        const e = this.index(this.depth);
        const n = this.pos - this.path[this.path.length - 1];
        return n
            ? this.parent.child(e).cut(0, n)
            : e == 0
              ? null
              : this.parent.child(e - 1);
    }

    posAtIndex(e, n) {
        n = this.resolveDepth(n);
        const r = this.path[n * 3];
        let o = n == 0 ? 0 : this.path[n * 3 - 1] + 1;
        for (let i = 0; i < e; i++) o += r.child(i).nodeSize;
        return o;
    }

    marks() {
        const e = this.parent;
        const n = this.index();
        if (e.content.size == 0) return q.none;
        if (this.textOffset) return e.child(n).marks;
        let r = e.maybeChild(n - 1);
        let o = e.maybeChild(n);
        if (!r) {
            const l = r;
            ((r = o), (o = l));
        }
        let i = r.marks;
        for (let s = 0; s < i.length; s++) {
            i[s].type.spec.inclusive === !1 &&
                (!o || !i[s].isInSet(o.marks)) &&
                (i = i[s--].removeFromSet(i));
        }
        return i;
    }

    marksAcross(e) {
        const n = this.parent.maybeChild(this.index());
        if (!n || !n.isInline) return null;
        let r = n.marks;
        const o = e.parent.maybeChild(e.index());
        for (let i = 0; i < r.length; i++) {
            r[i].type.spec.inclusive === !1 &&
                (!o || !r[i].isInSet(o.marks)) &&
                (r = r[i--].removeFromSet(r));
        }
        return r;
    }

    sharedDepth(e) {
        for (let n = this.depth; n > 0; n--) {
            if (this.start(n) <= e && this.end(n) >= e) return n;
        }
        return 0;
    }

    blockRange(e = this, n) {
        if (e.pos < this.pos) return e.blockRange(this);
        for (
            let r =
                this.depth -
                (this.parent.inlineContent || this.pos == e.pos ? 1 : 0);
            r >= 0;
            r--
        ) {
            if (e.pos <= this.end(r) && (!n || n(this.node(r)))) {
                return new Ht(this, e, r);
            }
        }
        return null;
    }

    sameParent(e) {
        return this.pos - this.parentOffset == e.pos - e.parentOffset;
    }

    max(e) {
        return e.pos > this.pos ? e : this;
    }

    min(e) {
        return e.pos < this.pos ? e : this;
    }

    toString() {
        let e = "";
        for (let n = 1; n <= this.depth; n++) {
            e +=
                (e ? "/" : "") +
                this.node(n).type.name +
                "_" +
                this.index(n - 1);
        }
        return e + ":" + this.parentOffset;
    }

    static resolve(e, n) {
        if (!(n >= 0 && n <= e.content.size)) {
            throw new RangeError("Position " + n + " out of range");
        }
        const r = [];
        let o = 0;
        let i = n;
        for (let s = e; ; ) {
            const { index: l, offset: a } = s.content.findIndex(i);
            const c = i - a;
            if ((r.push(s, l, o + a), !c || ((s = s.child(l)), s.isText))) {
                break;
            }
            ((i = c - 1), (o += a + 1));
        }
        return new t(n, r, i);
    }

    static resolveCached(e, n) {
        let r = aa.get(e);
        if (r) {
            for (let i = 0; i < r.elts.length; i++) {
                const s = r.elts[i];
                if (s.pos == n) return s;
            }
        } else aa.set(e, (r = new Ai()));
        const o = (r.elts[r.i] = t.resolve(e, n));
        return ((r.i = (r.i + 1) % ep), o);
    }
};
var Ai = class {
    constructor() {
        ((this.elts = []), (this.i = 0));
    }
};
var ep = 12;
var aa = new WeakMap();
var Ht = class {
    constructor(e, n, r) {
        ((this.$from = e), (this.$to = n), (this.depth = r));
    }

    get start() {
        return this.$from.before(this.depth + 1);
    }

    get end() {
        return this.$to.after(this.depth + 1);
    }

    get parent() {
        return this.$from.node(this.depth);
    }

    get startIndex() {
        return this.$from.index(this.depth);
    }

    get endIndex() {
        return this.$to.indexAfter(this.depth);
    }
};
const tp = Object.create(null);
const le = class t {
    constructor(e, n, r, o = q.none) {
        ((this.type = e),
            (this.attrs = n),
            (this.marks = o),
            (this.content = r || C.empty));
    }

    get children() {
        return this.content.content;
    }

    get nodeSize() {
        return this.isLeaf ? 1 : 2 + this.content.size;
    }

    get childCount() {
        return this.content.childCount;
    }

    child(e) {
        return this.content.child(e);
    }

    maybeChild(e) {
        return this.content.maybeChild(e);
    }

    forEach(e) {
        this.content.forEach(e);
    }

    nodesBetween(e, n, r, o = 0) {
        this.content.nodesBetween(e, n, r, o, this);
    }

    descendants(e) {
        this.nodesBetween(0, this.content.size, e);
    }

    get textContent() {
        return this.isLeaf && this.type.spec.leafText
            ? this.type.spec.leafText(this)
            : this.textBetween(0, this.content.size, "");
    }

    textBetween(e, n, r, o) {
        return this.content.textBetween(e, n, r, o);
    }

    get firstChild() {
        return this.content.firstChild;
    }

    get lastChild() {
        return this.content.lastChild;
    }

    eq(e) {
        return this == e || (this.sameMarkup(e) && this.content.eq(e.content));
    }

    sameMarkup(e) {
        return this.hasMarkup(e.type, e.attrs, e.marks);
    }

    hasMarkup(e, n, r) {
        return (
            this.type == e &&
            Mr(this.attrs, n || e.defaultAttrs || tp) &&
            q.sameSet(this.marks, r || q.none)
        );
    }

    copy(e = null) {
        return e == this.content
            ? this
            : new t(this.type, this.attrs, e, this.marks);
    }

    mark(e) {
        return e == this.marks
            ? this
            : new t(this.type, this.attrs, this.content, e);
    }

    cut(e, n = this.content.size) {
        return e == 0 && n == this.content.size
            ? this
            : this.copy(this.content.cut(e, n));
    }

    slice(e, n = this.content.size, r = !1) {
        if (e == n) return E.empty;
        const o = this.resolve(e);
        const i = this.resolve(n);
        const s = r ? 0 : o.sharedDepth(n);
        const l = o.start(s);
        const c = o.node(s).content.cut(o.pos - l, i.pos - l);
        return new E(c, o.depth - s, i.depth - s);
    }

    replace(e, n, r) {
        return Qh(this.resolve(e), this.resolve(n), r);
    }

    nodeAt(e) {
        for (let n = this; ; ) {
            const { index: r, offset: o } = n.content.findIndex(e);
            if (((n = n.maybeChild(r)), !n)) return null;
            if (o == e || n.isText) return n;
            e -= o + 1;
        }
    }

    childAfter(e) {
        const { index: n, offset: r } = this.content.findIndex(e);
        return { node: this.content.maybeChild(n), index: n, offset: r };
    }

    childBefore(e) {
        if (e == 0) return { node: null, index: 0, offset: 0 };
        const { index: n, offset: r } = this.content.findIndex(e);
        if (r < e) return { node: this.content.child(n), index: n, offset: r };
        const o = this.content.child(n - 1);
        return { node: o, index: n - 1, offset: r - o.nodeSize };
    }

    resolve(e) {
        return Ar.resolveCached(this, e);
    }

    resolveNoCache(e) {
        return Ar.resolve(this, e);
    }

    rangeHasMark(e, n, r) {
        let o = !1;
        return (
            n > e &&
                this.nodesBetween(
                    e,
                    n,
                    (i) => (r.isInSet(i.marks) && (o = !0), !o),
                ),
            o
        );
    }

    get isBlock() {
        return this.type.isBlock;
    }

    get isTextblock() {
        return this.type.isTextblock;
    }

    get inlineContent() {
        return this.type.inlineContent;
    }

    get isInline() {
        return this.type.isInline;
    }

    get isText() {
        return this.type.isText;
    }

    get isLeaf() {
        return this.type.isLeaf;
    }

    get isAtom() {
        return this.type.isAtom;
    }

    toString() {
        if (this.type.spec.toDebugString) {
            return this.type.spec.toDebugString(this);
        }
        let e = this.type.name;
        return (
            this.content.size &&
                (e += "(" + this.content.toStringInner() + ")"),
            va(this.marks, e)
        );
    }

    contentMatchAt(e) {
        const n = this.type.contentMatch.matchFragment(this.content, 0, e);
        if (!n) {
            throw new Error(
                "Called contentMatchAt on a node with invalid content",
            );
        }
        return n;
    }

    canReplace(e, n, r = C.empty, o = 0, i = r.childCount) {
        const s = this.contentMatchAt(e).matchFragment(r, o, i);
        const l = s && s.matchFragment(this.content, n);
        if (!l || !l.validEnd) return !1;
        for (let a = o; a < i; a++) {
            if (!this.type.allowsMarks(r.child(a).marks)) return !1;
        }
        return !0;
    }

    canReplaceWith(e, n, r, o) {
        if (o && !this.type.allowsMarks(o)) return !1;
        const i = this.contentMatchAt(e).matchType(r);
        const s = i && i.matchFragment(this.content, n);
        return s ? s.validEnd : !1;
    }

    canAppend(e) {
        return e.content.size
            ? this.canReplace(this.childCount, this.childCount, e.content)
            : this.type.compatibleContent(e.type);
    }

    check() {
        (this.type.checkContent(this.content),
            this.type.checkAttrs(this.attrs));
        let e = q.none;
        for (let n = 0; n < this.marks.length; n++) {
            const r = this.marks[n];
            (r.type.checkAttrs(r.attrs), (e = r.addToSet(e)));
        }
        if (!q.sameSet(e, this.marks)) {
            throw new RangeError(
                `Invalid collection of marks for node ${this.type.name}: ${this.marks.map((n) => n.type.name)}`,
            );
        }
        this.content.forEach((n) => n.check());
    }

    toJSON() {
        const e = { type: this.type.name };
        for (const n in this.attrs) {
            e.attrs = this.attrs;
            break;
        }
        return (
            this.content.size && (e.content = this.content.toJSON()),
            this.marks.length && (e.marks = this.marks.map((n) => n.toJSON())),
            e
        );
    }

    static fromJSON(e, n) {
        if (!n) throw new RangeError("Invalid input for Node.fromJSON");
        let r;
        if (n.marks) {
            if (!Array.isArray(n.marks)) {
                throw new RangeError("Invalid mark data for Node.fromJSON");
            }
            r = n.marks.map(e.markFromJSON);
        }
        if (n.type == "text") {
            if (typeof n.text !== "string") {
                throw new RangeError("Invalid text node in JSON");
            }
            return e.text(n.text, r);
        }
        const o = C.fromJSON(e, n.content);
        const i = e.nodeType(n.type).create(n.attrs, o, r);
        return (i.type.checkAttrs(i.attrs), i);
    }
};
le.prototype.text = void 0;
const Ei = class t extends le {
    constructor(e, n, r, o) {
        if ((super(e, n, null, o), !r)) {
            throw new RangeError("Empty text nodes are not allowed");
        }
        this.text = r;
    }

    toString() {
        return this.type.spec.toDebugString
            ? this.type.spec.toDebugString(this)
            : va(this.marks, JSON.stringify(this.text));
    }

    get textContent() {
        return this.text;
    }

    textBetween(e, n) {
        return this.text.slice(e, n);
    }

    get nodeSize() {
        return this.text.length;
    }

    mark(e) {
        return e == this.marks
            ? this
            : new t(this.type, this.attrs, this.text, e);
    }

    withText(e) {
        return e == this.text
            ? this
            : new t(this.type, this.attrs, e, this.marks);
    }

    cut(e = 0, n = this.text.length) {
        return e == 0 && n == this.text.length
            ? this
            : this.withText(this.text.slice(e, n));
    }

    eq(e) {
        return this.sameMarkup(e) && this.text == e.text;
    }

    toJSON() {
        const e = super.toJSON();
        return ((e.text = this.text), e);
    }
};
function va(t, e) {
    for (let n = t.length - 1; n >= 0; n--) e = t[n].type.name + "(" + e + ")";
    return e;
}
const $t = class t {
    constructor(e) {
        ((this.validEnd = e), (this.next = []), (this.wrapCache = []));
    }

    static parse(e, n) {
        const r = new Ni(e, n);
        if (r.next == null) return t.empty;
        const o = Ma(r);
        r.next && r.err("Unexpected trailing text");
        const i = ap(lp(o));
        return (cp(i, r), i);
    }

    matchType(e) {
        for (let n = 0; n < this.next.length; n++) {
            if (this.next[n].type == e) return this.next[n].next;
        }
        return null;
    }

    matchFragment(e, n = 0, r = e.childCount) {
        let o = this;
        for (let i = n; o && i < r; i++) o = o.matchType(e.child(i).type);
        return o;
    }

    get inlineContent() {
        return this.next.length != 0 && this.next[0].type.isInline;
    }

    get defaultType() {
        for (let e = 0; e < this.next.length; e++) {
            const { type: n } = this.next[e];
            if (!(n.isText || n.hasRequiredAttrs())) return n;
        }
        return null;
    }

    compatible(e) {
        for (let n = 0; n < this.next.length; n++) {
            for (let r = 0; r < e.next.length; r++) {
                if (this.next[n].type == e.next[r].type) return !0;
            }
        }
        return !1;
    }

    fillBefore(e, n = !1, r = 0) {
        const o = [this];
        function i(s, l) {
            const a = s.matchFragment(e, r);
            if (a && (!n || a.validEnd)) {
                return C.from(l.map((c) => c.createAndFill()));
            }
            for (let c = 0; c < s.next.length; c++) {
                const { type: d, next: u } = s.next[c];
                if (!(d.isText || d.hasRequiredAttrs()) && o.indexOf(u) == -1) {
                    o.push(u);
                    const f = i(u, l.concat(d));
                    if (f) return f;
                }
            }
            return null;
        }
        return i(this, []);
    }

    findWrapping(e) {
        for (let r = 0; r < this.wrapCache.length; r += 2) {
            if (this.wrapCache[r] == e) return this.wrapCache[r + 1];
        }
        const n = this.computeWrapping(e);
        return (this.wrapCache.push(e, n), n);
    }

    computeWrapping(e) {
        const n = Object.create(null);
        const r = [{ match: this, type: null, via: null }];
        for (; r.length; ) {
            const o = r.shift();
            const i = o.match;
            if (i.matchType(e)) {
                const s = [];
                for (let l = o; l.type; l = l.via) s.push(l.type);
                return s.reverse();
            }
            for (let s = 0; s < i.next.length; s++) {
                const { type: l, next: a } = i.next[s];
                !l.isLeaf &&
                    !l.hasRequiredAttrs() &&
                    !(l.name in n) &&
                    (!o.type || a.validEnd) &&
                    (r.push({ match: l.contentMatch, type: l, via: o }),
                    (n[l.name] = !0));
            }
        }
        return null;
    }

    get edgeCount() {
        return this.next.length;
    }

    edge(e) {
        if (e >= this.next.length) {
            throw new RangeError(
                `There's no ${e}th edge in this content match`,
            );
        }
        return this.next[e];
    }

    toString() {
        const e = [];
        function n(r) {
            e.push(r);
            for (let o = 0; o < r.next.length; o++) {
                e.indexOf(r.next[o].next) == -1 && n(r.next[o].next);
            }
        }
        return (
            n(this),
            e.map((r, o) => {
                let i = o + (r.validEnd ? "*" : " ") + " ";
                for (let s = 0; s < r.next.length; s++) {
                    i +=
                        (s ? ", " : "") +
                        r.next[s].type.name +
                        "->" +
                        e.indexOf(r.next[s].next);
                }
                return i;
            }).join(`
`)
        );
    }
};
$t.empty = new $t(!0);
var Ni = class {
    constructor(e, n) {
        ((this.string = e),
            (this.nodeTypes = n),
            (this.inline = null),
            (this.pos = 0),
            (this.tokens = e.split(/\s*(?=\b|\W|$)/)),
            this.tokens[this.tokens.length - 1] == "" && this.tokens.pop(),
            this.tokens[0] == "" && this.tokens.shift());
    }

    get next() {
        return this.tokens[this.pos];
    }

    eat(e) {
        return this.next == e && (this.pos++ || !0);
    }

    err(e) {
        throw new SyntaxError(
            e + " (in content expression '" + this.string + "')",
        );
    }
};
function Ma(t) {
    const e = [];
    do e.push(np(t));
    while (t.eat("|"));
    return e.length == 1 ? e[0] : { type: "choice", exprs: e };
}
function np(t) {
    const e = [];
    do e.push(rp(t));
    while (t.next && t.next != ")" && t.next != "|");
    return e.length == 1 ? e[0] : { type: "seq", exprs: e };
}
function rp(t) {
    let e = sp(t);
    for (;;) {
        if (t.eat("+")) e = { type: "plus", expr: e };
        else if (t.eat("*")) e = { type: "star", expr: e };
        else if (t.eat("?")) e = { type: "opt", expr: e };
        else if (t.eat("{")) e = op(t, e);
        else break;
    }
    return e;
}
function ca(t) {
    /\D/.test(t.next) && t.err("Expected number, got '" + t.next + "'");
    const e = Number(t.next);
    return (t.pos++, e);
}
function op(t, e) {
    const n = ca(t);
    let r = n;
    return (
        t.eat(",") && (t.next != "}" ? (r = ca(t)) : (r = -1)),
        t.eat("}") || t.err("Unclosed braced range"),
        { type: "range", min: n, max: r, expr: e }
    );
}
function ip(t, e) {
    const n = t.nodeTypes;
    const r = n[e];
    if (r) return [r];
    const o = [];
    for (const i in n) {
        const s = n[i];
        s.isInGroup(e) && o.push(s);
    }
    return (
        o.length == 0 && t.err("No node type or group '" + e + "' found"),
        o
    );
}
function sp(t) {
    if (t.eat("(")) {
        const e = Ma(t);
        return (t.eat(")") || t.err("Missing closing paren"), e);
    } else if (/\W/.test(t.next)) t.err("Unexpected token '" + t.next + "'");
    else {
        const e = ip(t, t.next).map(
            (n) => (
                t.inline == null
                    ? (t.inline = n.isInline)
                    : t.inline != n.isInline &&
                      t.err("Mixing inline and block content"),
                { type: "name", value: n }
            ),
        );
        return (t.pos++, e.length == 1 ? e[0] : { type: "choice", exprs: e });
    }
}
function lp(t) {
    const e = [[]];
    return (o(i(t, 0), n()), e);
    function n() {
        return e.push([]) - 1;
    }
    function r(s, l, a) {
        const c = { term: a, to: l };
        return (e[s].push(c), c);
    }
    function o(s, l) {
        s.forEach((a) => (a.to = l));
    }
    function i(s, l) {
        if (s.type == "choice") {
            return s.exprs.reduce((a, c) => a.concat(i(c, l)), []);
        }
        if (s.type == "seq") {
            for (let a = 0; ; a++) {
                const c = i(s.exprs[a], l);
                if (a == s.exprs.length - 1) return c;
                o(c, (l = n()));
            }
        } else if (s.type == "star") {
            const a = n();
            return (r(l, a), o(i(s.expr, a), a), [r(a)]);
        } else if (s.type == "plus") {
            const a = n();
            return (o(i(s.expr, l), a), o(i(s.expr, a), a), [r(a)]);
        } else {
            if (s.type == "opt") return [r(l)].concat(i(s.expr, l));
            if (s.type == "range") {
                let a = l;
                for (let c = 0; c < s.min; c++) {
                    const d = n();
                    (o(i(s.expr, a), d), (a = d));
                }
                if (s.max == -1) o(i(s.expr, a), a);
                else {
                    for (let c = s.min; c < s.max; c++) {
                        const d = n();
                        (r(a, d), o(i(s.expr, a), d), (a = d));
                    }
                }
                return [r(a)];
            } else {
                if (s.type == "name") return [r(l, void 0, s.value)];
                throw new Error("Unknown expr type");
            }
        }
    }
}
function Ta(t, e) {
    return e - t;
}
function da(t, e) {
    const n = [];
    return (r(e), n.sort(Ta));
    function r(o) {
        const i = t[o];
        if (i.length == 1 && !i[0].term) return r(i[0].to);
        n.push(o);
        for (let s = 0; s < i.length; s++) {
            const { term: l, to: a } = i[s];
            !l && n.indexOf(a) == -1 && r(a);
        }
    }
}
function ap(t) {
    const e = Object.create(null);
    return n(da(t, 0));
    function n(r) {
        const o = [];
        r.forEach((s) => {
            t[s].forEach(({ term: l, to: a }) => {
                if (!l) return;
                let c;
                for (let d = 0; d < o.length; d++) {
                    o[d][0] == l && (c = o[d][1]);
                }
                da(t, a).forEach((d) => {
                    (c || o.push([l, (c = [])]),
                        c.indexOf(d) == -1 && c.push(d));
                });
            });
        });
        const i = (e[r.join(",")] = new $t(r.indexOf(t.length - 1) > -1));
        for (let s = 0; s < o.length; s++) {
            const l = o[s][1].sort(Ta);
            i.next.push({ type: o[s][0], next: e[l.join(",")] || n(l) });
        }
        return i;
    }
}
function cp(t, e) {
    for (let n = 0, r = [t]; n < r.length; n++) {
        const o = r[n];
        let i = !o.validEnd;
        const s = [];
        for (let l = 0; l < o.next.length; l++) {
            const { type: a, next: c } = o.next[l];
            (s.push(a.name),
                i && !(a.isText || a.hasRequiredAttrs()) && (i = !1),
                r.indexOf(c) == -1 && r.push(c));
        }
        i &&
            e.err(
                "Only non-generatable nodes (" +
                    s.join(", ") +
                    ") in a required position (see https://prosemirror.net/docs/guide/#generatable)",
            );
    }
}
function Aa(t) {
    const e = Object.create(null);
    for (const n in t) {
        const r = t[n];
        if (!r.hasDefault) return null;
        e[n] = r.default;
    }
    return e;
}
function Ea(t, e) {
    const n = Object.create(null);
    for (const r in t) {
        let o = e && e[r];
        if (o === void 0) {
            const i = t[r];
            if (i.hasDefault) o = i.default;
            else throw new RangeError("No value supplied for attribute " + r);
        }
        n[r] = o;
    }
    return n;
}
function Na(t, e, n, r) {
    for (const o in e) {
        if (!(o in t)) {
            throw new RangeError(
                `Unsupported attribute ${o} for ${n} of type ${o}`,
            );
        }
    }
    for (const o in t) {
        const i = t[o];
        i.validate && i.validate(e[o]);
    }
}
function Oa(t, e) {
    const n = Object.create(null);
    if (e) for (const r in e) n[r] = new Oi(t, r, e[r]);
    return n;
}
const Er = class t {
    constructor(e, n, r) {
        ((this.name = e),
            (this.schema = n),
            (this.spec = r),
            (this.markSet = null),
            (this.groups = r.group ? r.group.split(" ") : []),
            (this.attrs = Oa(e, r.attrs)),
            (this.defaultAttrs = Aa(this.attrs)),
            (this.contentMatch = null),
            (this.inlineContent = null),
            (this.isBlock = !(r.inline || e == "text")),
            (this.isText = e == "text"));
    }

    get isInline() {
        return !this.isBlock;
    }

    get isTextblock() {
        return this.isBlock && this.inlineContent;
    }

    get isLeaf() {
        return this.contentMatch == $t.empty;
    }

    get isAtom() {
        return this.isLeaf || !!this.spec.atom;
    }

    isInGroup(e) {
        return this.groups.indexOf(e) > -1;
    }

    get whitespace() {
        return this.spec.whitespace || (this.spec.code ? "pre" : "normal");
    }

    hasRequiredAttrs() {
        for (const e in this.attrs) if (this.attrs[e].isRequired) return !0;
        return !1;
    }

    compatibleContent(e) {
        return this == e || this.contentMatch.compatible(e.contentMatch);
    }

    computeAttrs(e) {
        return !e && this.defaultAttrs ? this.defaultAttrs : Ea(this.attrs, e);
    }

    create(e = null, n, r) {
        if (this.isText) {
            throw new Error("NodeType.create can't construct text nodes");
        }
        return new le(this, this.computeAttrs(e), C.from(n), q.setFrom(r));
    }

    createChecked(e = null, n, r) {
        return (
            (n = C.from(n)),
            this.checkContent(n),
            new le(this, this.computeAttrs(e), n, q.setFrom(r))
        );
    }

    createAndFill(e = null, n, r) {
        if (((e = this.computeAttrs(e)), (n = C.from(n)), n.size)) {
            const s = this.contentMatch.fillBefore(n);
            if (!s) return null;
            n = s.append(n);
        }
        const o = this.contentMatch.matchFragment(n);
        const i = o && o.fillBefore(C.empty, !0);
        return i ? new le(this, e, n.append(i), q.setFrom(r)) : null;
    }

    validContent(e) {
        const n = this.contentMatch.matchFragment(e);
        if (!n || !n.validEnd) return !1;
        for (let r = 0; r < e.childCount; r++) {
            if (!this.allowsMarks(e.child(r).marks)) return !1;
        }
        return !0;
    }

    checkContent(e) {
        if (!this.validContent(e)) {
            throw new RangeError(
                `Invalid content for node ${this.name}: ${e.toString().slice(0, 50)}`,
            );
        }
    }

    checkAttrs(e) {
        Na(this.attrs, e, "node", this.name);
    }

    allowsMarkType(e) {
        return this.markSet == null || this.markSet.indexOf(e) > -1;
    }

    allowsMarks(e) {
        if (this.markSet == null) return !0;
        for (let n = 0; n < e.length; n++) {
            if (!this.allowsMarkType(e[n].type)) return !1;
        }
        return !0;
    }

    allowedMarks(e) {
        if (this.markSet == null) return e;
        let n;
        for (let r = 0; r < e.length; r++) {
            this.allowsMarkType(e[r].type)
                ? n && n.push(e[r])
                : n || (n = e.slice(0, r));
        }
        return n ? (n.length ? n : q.none) : e;
    }

    static compile(e, n) {
        const r = Object.create(null);
        e.forEach((i, s) => (r[i] = new t(i, n, s)));
        const o = n.spec.topNode || "doc";
        if (!r[o]) {
            throw new RangeError(
                "Schema is missing its top node type ('" + o + "')",
            );
        }
        if (!r.text) throw new RangeError("Every schema needs a 'text' type");
        for (const i in r.text.attrs) {
            throw new RangeError(
                "The text node type should not have attributes",
            );
        }
        return r;
    }
};
function dp(t, e, n) {
    const r = n.split("|");
    return (o) => {
        const i = o === null ? "null" : typeof o;
        if (r.indexOf(i) < 0) {
            throw new RangeError(
                `Expected value of type ${r} for attribute ${e} on type ${t}, got ${i}`,
            );
        }
    };
}
var Oi = class {
    constructor(e, n, r) {
        ((this.hasDefault = Object.prototype.hasOwnProperty.call(r, "default")),
            (this.default = r.default),
            (this.validate =
                typeof r.validate === "string"
                    ? dp(e, n, r.validate)
                    : r.validate));
    }

    get isRequired() {
        return !this.hasDefault;
    }
};
const In = class t {
    constructor(e, n, r, o) {
        ((this.name = e),
            (this.rank = n),
            (this.schema = r),
            (this.spec = o),
            (this.attrs = Oa(e, o.attrs)),
            (this.excluded = null));
        const i = Aa(this.attrs);
        this.instance = i ? new q(this, i) : null;
    }

    create(e = null) {
        return !e && this.instance
            ? this.instance
            : new q(this, Ea(this.attrs, e));
    }

    static compile(e, n) {
        const r = Object.create(null);
        let o = 0;
        return (e.forEach((i, s) => (r[i] = new t(i, o++, n, s))), r);
    }

    removeFromSet(e) {
        for (let n = 0; n < e.length; n++) {
            e[n].type == this &&
                ((e = e.slice(0, n).concat(e.slice(n + 1))), n--);
        }
        return e;
    }

    isInSet(e) {
        for (let n = 0; n < e.length; n++) if (e[n].type == this) return e[n];
    }

    checkAttrs(e) {
        Na(this.attrs, e, "mark", this.name);
    }

    excludes(e) {
        return this.excluded.indexOf(e) > -1;
    }
};
const an = class {
    constructor(e) {
        ((this.linebreakReplacement = null),
            (this.cached = Object.create(null)));
        const n = (this.spec = {});
        for (const o in e) n[o] = e[o];
        ((n.nodes = Ci.from(e.nodes)),
            (n.marks = Ci.from(e.marks || {})),
            (this.nodes = Er.compile(this.spec.nodes, this)),
            (this.marks = In.compile(this.spec.marks, this)));
        const r = Object.create(null);
        for (const o in this.nodes) {
            if (o in this.marks) {
                throw new RangeError(o + " can not be both a node and a mark");
            }
            const i = this.nodes[o];
            const s = i.spec.content || "";
            const l = i.spec.marks;
            if (
                ((i.contentMatch = r[s] || (r[s] = $t.parse(s, this.nodes))),
                (i.inlineContent = i.contentMatch.inlineContent),
                i.spec.linebreakReplacement)
            ) {
                if (this.linebreakReplacement) {
                    throw new RangeError("Multiple linebreak nodes defined");
                }
                if (!i.isInline || !i.isLeaf) {
                    throw new RangeError(
                        "Linebreak replacement nodes must be inline leaf nodes",
                    );
                }
                this.linebreakReplacement = i;
            }
            i.markSet =
                l == "_"
                    ? null
                    : l
                      ? ua(this, l.split(" "))
                      : l == "" || !i.inlineContent
                        ? []
                        : null;
        }
        for (const o in this.marks) {
            const i = this.marks[o];
            const s = i.spec.excludes;
            i.excluded =
                s == null ? [i] : s == "" ? [] : ua(this, s.split(" "));
        }
        ((this.nodeFromJSON = (o) => le.fromJSON(this, o)),
            (this.markFromJSON = (o) => q.fromJSON(this, o)),
            (this.topNodeType = this.nodes[this.spec.topNode || "doc"]),
            (this.cached.wrappings = Object.create(null)));
    }

    node(e, n = null, r, o) {
        if (typeof e === "string") e = this.nodeType(e);
        else if (e instanceof Er) {
            if (e.schema != this) {
                throw new RangeError(
                    "Node type from different schema used (" + e.name + ")",
                );
            }
        } else throw new RangeError("Invalid node type: " + e);
        return e.createChecked(n, r, o);
    }

    text(e, n) {
        const r = this.nodes.text;
        return new Ei(r, r.defaultAttrs, e, q.setFrom(n));
    }

    mark(e, n) {
        return (typeof e === "string" && (e = this.marks[e]), e.create(n));
    }

    nodeType(e) {
        const n = this.nodes[e];
        if (!n) throw new RangeError("Unknown node type: " + e);
        return n;
    }
};
function ua(t, e) {
    const n = [];
    for (let r = 0; r < e.length; r++) {
        const o = e[r];
        const i = t.marks[o];
        let s = i;
        if (i) n.push(i);
        else {
            for (const l in t.marks) {
                const a = t.marks[l];
                (o == "_" ||
                    (a.spec.group &&
                        a.spec.group.split(" ").indexOf(o) > -1)) &&
                    n.push((s = a));
            }
        }
        if (!s) throw new SyntaxError("Unknown mark type: '" + e[r] + "'");
    }
    return n;
}
function up(t) {
    return t.tag != null;
}
function fp(t) {
    return t.style != null;
}
const Ue = class t {
    constructor(e, n) {
        ((this.schema = e),
            (this.rules = n),
            (this.tags = []),
            (this.styles = []));
        const r = (this.matchedStyles = []);
        (n.forEach((o) => {
            if (up(o)) this.tags.push(o);
            else if (fp(o)) {
                const i = /[^=]*/.exec(o.style)[0];
                (r.indexOf(i) < 0 && r.push(i), this.styles.push(o));
            }
        }),
            (this.normalizeLists = !this.tags.some((o) => {
                if (!/^(ul|ol)\b/.test(o.tag) || !o.node) return !1;
                const i = e.nodes[o.node];
                return i.contentMatch.matchType(i);
            })));
    }

    parse(e, n = {}) {
        const r = new Nr(this, n, !1);
        return (r.addAll(e, q.none, n.from, n.to), r.finish());
    }

    parseSlice(e, n = {}) {
        const r = new Nr(this, n, !0);
        return (r.addAll(e, q.none, n.from, n.to), E.maxOpen(r.finish()));
    }

    matchTag(e, n, r) {
        for (
            let o = r ? this.tags.indexOf(r) + 1 : 0;
            o < this.tags.length;
            o++
        ) {
            const i = this.tags[o];
            if (
                mp(e, i.tag) &&
                (i.namespace === void 0 || e.namespaceURI == i.namespace) &&
                (!i.context || n.matchesContext(i.context))
            ) {
                if (i.getAttrs) {
                    const s = i.getAttrs(e);
                    if (s === !1) continue;
                    i.attrs = s || void 0;
                }
                return i;
            }
        }
    }

    matchStyle(e, n, r, o) {
        for (
            let i = o ? this.styles.indexOf(o) + 1 : 0;
            i < this.styles.length;
            i++
        ) {
            const s = this.styles[i];
            const l = s.style;
            if (
                !(
                    l.indexOf(e) != 0 ||
                    (s.context && !r.matchesContext(s.context)) ||
                    (l.length > e.length &&
                        (l.charCodeAt(e.length) != 61 ||
                            l.slice(e.length + 1) != n))
                )
            ) {
                if (s.getAttrs) {
                    const a = s.getAttrs(n);
                    if (a === !1) continue;
                    s.attrs = a || void 0;
                }
                return s;
            }
        }
    }

    static schemaRules(e) {
        const n = [];
        function r(o) {
            const i = o.priority == null ? 50 : o.priority;
            let s = 0;
            for (; s < n.length; s++) {
                const l = n[s];
                if ((l.priority == null ? 50 : l.priority) < i) break;
            }
            n.splice(s, 0, o);
        }
        for (const o in e.marks) {
            const i = e.marks[o].spec.parseDOM;
            i &&
                i.forEach((s) => {
                    (r((s = ha(s))),
                        s.mark || s.ignore || s.clearMark || (s.mark = o));
                });
        }
        for (const o in e.nodes) {
            const i = e.nodes[o].spec.parseDOM;
            i &&
                i.forEach((s) => {
                    (r((s = ha(s))),
                        s.node || s.ignore || s.mark || (s.node = o));
                });
        }
        return n;
    }

    static fromSchema(e) {
        return (
            e.cached.domParser ||
            (e.cached.domParser = new t(e, t.schemaRules(e)))
        );
    }
};
const Ra = {
    address: !0,
    article: !0,
    aside: !0,
    blockquote: !0,
    canvas: !0,
    dd: !0,
    div: !0,
    dl: !0,
    fieldset: !0,
    figcaption: !0,
    figure: !0,
    footer: !0,
    form: !0,
    h1: !0,
    h2: !0,
    h3: !0,
    h4: !0,
    h5: !0,
    h6: !0,
    header: !0,
    hgroup: !0,
    hr: !0,
    li: !0,
    noscript: !0,
    ol: !0,
    output: !0,
    p: !0,
    pre: !0,
    section: !0,
    table: !0,
    tfoot: !0,
    ul: !0,
};
const hp = {
    head: !0,
    noscript: !0,
    object: !0,
    script: !0,
    style: !0,
    title: !0,
};
const Da = { ol: !0, ul: !0 };
const Pn = 1;
const Ri = 2;
const Dn = 4;
function fa(t, e, n) {
    return e != null
        ? (e ? Pn : 0) | (e === "full" ? Ri : 0)
        : t && t.whitespace == "pre"
          ? Pn | Ri
          : n & ~Dn;
}
const ln = class {
    constructor(e, n, r, o, i, s) {
        ((this.type = e),
            (this.attrs = n),
            (this.marks = r),
            (this.solid = o),
            (this.options = s),
            (this.content = []),
            (this.activeMarks = q.none),
            (this.match = i || (s & Dn ? null : e.contentMatch)));
    }

    findWrapping(e) {
        if (!this.match) {
            if (!this.type) return [];
            const n = this.type.contentMatch.fillBefore(C.from(e));
            if (n) this.match = this.type.contentMatch.matchFragment(n);
            else {
                const r = this.type.contentMatch;
                let o;
                return (o = r.findWrapping(e.type))
                    ? ((this.match = r), o)
                    : null;
            }
        }
        return this.match.findWrapping(e.type);
    }

    finish(e) {
        if (!(this.options & Pn)) {
            const r = this.content[this.content.length - 1];
            let o;
            if (r && r.isText && (o = /[ \t\r\n\u000c]+$/.exec(r.text))) {
                const i = r;
                r.text.length == o[0].length
                    ? this.content.pop()
                    : (this.content[this.content.length - 1] = i.withText(
                          i.text.slice(0, i.text.length - o[0].length),
                      ));
            }
        }
        let n = C.from(this.content);
        return (
            !e &&
                this.match &&
                (n = n.append(this.match.fillBefore(C.empty, !0))),
            this.type ? this.type.create(this.attrs, n, this.marks) : n
        );
    }

    inlineContext(e) {
        return this.type
            ? this.type.inlineContent
            : this.content.length
              ? this.content[0].isInline
              : e.parentNode &&
                !Ra.hasOwnProperty(e.parentNode.nodeName.toLowerCase());
    }
};
var Nr = class {
    constructor(e, n, r) {
        ((this.parser = e),
            (this.options = n),
            (this.isOpen = r),
            (this.open = 0),
            (this.localPreserveWS = !1));
        const o = n.topNode;
        let i;
        const s = fa(null, n.preserveWhitespace, 0) | (r ? Dn : 0);
        (o
            ? (i = new ln(
                  o.type,
                  o.attrs,
                  q.none,
                  !0,
                  n.topMatch || o.type.contentMatch,
                  s,
              ))
            : r
              ? (i = new ln(null, null, q.none, !0, null, s))
              : (i = new ln(e.schema.topNodeType, null, q.none, !0, null, s)),
            (this.nodes = [i]),
            (this.find = n.findPositions),
            (this.needsBlock = !1));
    }

    get top() {
        return this.nodes[this.open];
    }

    addDOM(e, n) {
        e.nodeType == 3
            ? this.addTextNode(e, n)
            : e.nodeType == 1 && this.addElement(e, n);
    }

    addTextNode(e, n) {
        let r = e.nodeValue;
        const o = this.top;
        const i =
            o.options & Ri
                ? "full"
                : this.localPreserveWS || (o.options & Pn) > 0;
        const { schema: s } = this.parser;
        if (i === "full" || o.inlineContext(e) || /[^ \t\r\n\u000c]/.test(r)) {
            if (i) {
                if (i === "full") {
                    r = r.replace(
                        /\r\n?/g,
                        `
`,
                    );
                } else if (
                    s.linebreakReplacement &&
                    /[\r\n]/.test(r) &&
                    this.top.findWrapping(s.linebreakReplacement.create())
                ) {
                    const l = r.split(/\r?\n|\r/);
                    for (let a = 0; a < l.length; a++) {
                        (a &&
                            this.insertNode(
                                s.linebreakReplacement.create(),
                                n,
                                !0,
                            ),
                            l[a] &&
                                this.insertNode(
                                    s.text(l[a]),
                                    n,
                                    !/\S/.test(l[a]),
                                ));
                    }
                    r = "";
                } else r = r.replace(/\r?\n|\r/g, " ");
            } else if (
                ((r = r.replace(/[ \t\r\n\u000c]+/g, " ")),
                /^[ \t\r\n\u000c]/.test(r) &&
                    this.open == this.nodes.length - 1)
            ) {
                const l = o.content[o.content.length - 1];
                const a = e.previousSibling;
                (!l ||
                    (a && a.nodeName == "BR") ||
                    (l.isText && /[ \t\r\n\u000c]$/.test(l.text))) &&
                    (r = r.slice(1));
            }
            (r && this.insertNode(s.text(r), n, !/\S/.test(r)),
                this.findInText(e));
        } else this.findInside(e);
    }

    addElement(e, n, r) {
        const o = this.localPreserveWS;
        let i = this.top;
        (e.tagName == "PRE" || /pre/.test(e.style && e.style.whiteSpace)) &&
            (this.localPreserveWS = !0);
        const s = e.nodeName.toLowerCase();
        let l;
        Da.hasOwnProperty(s) && this.parser.normalizeLists && pp(e);
        const a =
            (this.options.ruleFromNode && this.options.ruleFromNode(e)) ||
            (l = this.parser.matchTag(e, this, r));
        e: if (a ? a.ignore : hp.hasOwnProperty(s)) {
            (this.findInside(e), this.ignoreFallback(e, n));
        } else if (!a || a.skip || a.closeParent) {
            a && a.closeParent
                ? (this.open = Math.max(0, this.open - 1))
                : a && a.skip.nodeType && (e = a.skip);
            let c;
            const d = this.needsBlock;
            if (Ra.hasOwnProperty(s)) {
                (i.content.length &&
                    i.content[0].isInline &&
                    this.open &&
                    (this.open--, (i = this.top)),
                    (c = !0),
                    i.type || (this.needsBlock = !0));
            } else if (!e.firstChild) {
                this.leafFallback(e, n);
                break e;
            }
            const u = a && a.skip ? n : this.readStyles(e, n);
            (u && this.addAll(e, u), c && this.sync(i), (this.needsBlock = d));
        } else {
            const c = this.readStyles(e, n);
            c &&
                this.addElementByRule(e, a, c, a.consuming === !1 ? l : void 0);
        }
        this.localPreserveWS = o;
    }

    leafFallback(e, n) {
        e.nodeName == "BR" &&
            this.top.type &&
            this.top.type.inlineContent &&
            this.addTextNode(
                e.ownerDocument.createTextNode(`
`),
                n,
            );
    }

    ignoreFallback(e, n) {
        e.nodeName == "BR" &&
            (!this.top.type || !this.top.type.inlineContent) &&
            this.findPlace(this.parser.schema.text("-"), n, !0);
    }

    readStyles(e, n) {
        const r = e.style;
        if (r && r.length) {
            for (let o = 0; o < this.parser.matchedStyles.length; o++) {
                const i = this.parser.matchedStyles[o];
                const s = r.getPropertyValue(i);
                if (s) {
                    for (let l = void 0; ; ) {
                        const a = this.parser.matchStyle(i, s, this, l);
                        if (!a) break;
                        if (a.ignore) return null;
                        if (
                            (a.clearMark
                                ? (n = n.filter((c) => !a.clearMark(c)))
                                : (n = n.concat(
                                      this.parser.schema.marks[a.mark].create(
                                          a.attrs,
                                      ),
                                  )),
                            a.consuming === !1)
                        ) {
                            l = a;
                        } else break;
                    }
                }
            }
        }
        return n;
    }

    addElementByRule(e, n, r, o) {
        let i, s;
        if (n.node) {
            if (((s = this.parser.schema.nodes[n.node]), s.isLeaf)) {
                this.insertNode(s.create(n.attrs), r, e.nodeName == "BR") ||
                    this.leafFallback(e, r);
            } else {
                const a = this.enter(
                    s,
                    n.attrs || null,
                    r,
                    n.preserveWhitespace,
                );
                a && ((i = !0), (r = a));
            }
        } else {
            const a = this.parser.schema.marks[n.mark];
            r = r.concat(a.create(n.attrs));
        }
        const l = this.top;
        if (s && s.isLeaf) this.findInside(e);
        else if (o) this.addElement(e, r, o);
        else if (n.getContent) {
            (this.findInside(e),
                n
                    .getContent(e, this.parser.schema)
                    .forEach((a) => this.insertNode(a, r, !1)));
        } else {
            let a = e;
            (typeof n.contentElement === "string"
                ? (a = e.querySelector(n.contentElement))
                : typeof n.contentElement === "function"
                  ? (a = n.contentElement(e))
                  : n.contentElement && (a = n.contentElement),
                this.findAround(e, a, !0),
                this.addAll(a, r),
                this.findAround(e, a, !1));
        }
        i && this.sync(l) && this.open--;
    }

    addAll(e, n, r, o) {
        let i = r || 0;
        for (
            let s = r ? e.childNodes[r] : e.firstChild,
                l = o == null ? null : e.childNodes[o];
            s != l;
            s = s.nextSibling, ++i
        ) {
            (this.findAtPoint(e, i), this.addDOM(s, n));
        }
        this.findAtPoint(e, i);
    }

    findPlace(e, n, r) {
        let o, i;
        for (let s = this.open, l = 0; s >= 0; s--) {
            const a = this.nodes[s];
            const c = a.findWrapping(e);
            if (
                c &&
                (!o || o.length > c.length + l) &&
                ((o = c), (i = a), !c.length)
            ) {
                break;
            }
            if (a.solid) {
                if (r) break;
                l += 2;
            }
        }
        if (!o) return null;
        this.sync(i);
        for (let s = 0; s < o.length; s++) {
            n = this.enterInner(o[s], null, n, !1);
        }
        return n;
    }

    insertNode(e, n, r) {
        if (e.isInline && this.needsBlock && !this.top.type) {
            const i = this.textblockFromContext();
            i && (n = this.enterInner(i, null, n));
        }
        const o = this.findPlace(e, n, r);
        if (o) {
            this.closeExtra();
            const i = this.top;
            i.match && (i.match = i.match.matchType(e.type));
            let s = q.none;
            for (const l of o.concat(e.marks)) {
                (i.type ? i.type.allowsMarkType(l.type) : pa(l.type, e.type)) &&
                    (s = l.addToSet(s));
            }
            return (i.content.push(e.mark(s)), !0);
        }
        return !1;
    }

    enter(e, n, r, o) {
        let i = this.findPlace(e.create(n), r, !1);
        return (i && (i = this.enterInner(e, n, r, !0, o)), i);
    }

    enterInner(e, n, r, o = !1, i) {
        this.closeExtra();
        const s = this.top;
        s.match = s.match && s.match.matchType(e);
        let l = fa(e, i, s.options);
        s.options & Dn && s.content.length == 0 && (l |= Dn);
        let a = q.none;
        return (
            (r = r.filter((c) =>
                (s.type ? s.type.allowsMarkType(c.type) : pa(c.type, e))
                    ? ((a = c.addToSet(a)), !1)
                    : !0,
            )),
            this.nodes.push(new ln(e, n, a, o, null, l)),
            this.open++,
            r
        );
    }

    closeExtra(e = !1) {
        let n = this.nodes.length - 1;
        if (n > this.open) {
            for (; n > this.open; n--) {
                this.nodes[n - 1].content.push(this.nodes[n].finish(e));
            }
            this.nodes.length = this.open + 1;
        }
    }

    finish() {
        return (
            (this.open = 0),
            this.closeExtra(this.isOpen),
            this.nodes[0].finish(!!(this.isOpen || this.options.topOpen))
        );
    }

    sync(e) {
        for (let n = this.open; n >= 0; n--) {
            if (this.nodes[n] == e) return ((this.open = n), !0);
            this.localPreserveWS && (this.nodes[n].options |= Pn);
        }
        return !1;
    }

    get currentPos() {
        this.closeExtra();
        let e = 0;
        for (let n = this.open; n >= 0; n--) {
            const r = this.nodes[n].content;
            for (let o = r.length - 1; o >= 0; o--) e += r[o].nodeSize;
            n && e++;
        }
        return e;
    }

    findAtPoint(e, n) {
        if (this.find) {
            for (let r = 0; r < this.find.length; r++) {
                this.find[r].node == e &&
                    this.find[r].offset == n &&
                    (this.find[r].pos = this.currentPos);
            }
        }
    }

    findInside(e) {
        if (this.find) {
            for (let n = 0; n < this.find.length; n++) {
                this.find[n].pos == null &&
                    e.nodeType == 1 &&
                    e.contains(this.find[n].node) &&
                    (this.find[n].pos = this.currentPos);
            }
        }
    }

    findAround(e, n, r) {
        if (e != n && this.find) {
            for (let o = 0; o < this.find.length; o++) {
                this.find[o].pos == null &&
                    e.nodeType == 1 &&
                    e.contains(this.find[o].node) &&
                    n.compareDocumentPosition(this.find[o].node) &
                        (r ? 2 : 4) &&
                    (this.find[o].pos = this.currentPos);
            }
        }
    }

    findInText(e) {
        if (this.find) {
            for (let n = 0; n < this.find.length; n++) {
                this.find[n].node == e &&
                    (this.find[n].pos =
                        this.currentPos -
                        (e.nodeValue.length - this.find[n].offset));
            }
        }
    }

    matchesContext(e) {
        if (e.indexOf("|") > -1) {
            return e.split(/\s*\|\s*/).some(this.matchesContext, this);
        }
        const n = e.split("/");
        const r = this.options.context;
        const o = !this.isOpen && (!r || r.parent.type == this.nodes[0].type);
        const i = -(r ? r.depth + 1 : 0) + (o ? 0 : 1);
        const s = (l, a) => {
            for (; l >= 0; l--) {
                const c = n[l];
                if (c == "") {
                    if (l == n.length - 1 || l == 0) continue;
                    for (; a >= i; a--) if (s(l - 1, a)) return !0;
                    return !1;
                } else {
                    const d =
                        a > 0 || (a == 0 && o)
                            ? this.nodes[a].type
                            : r && a >= i
                              ? r.node(a - i).type
                              : null;
                    if (!d || (d.name != c && !d.isInGroup(c))) return !1;
                    a--;
                }
            }
            return !0;
        };
        return s(n.length - 1, this.open);
    }

    textblockFromContext() {
        const e = this.options.context;
        if (e) {
            for (let n = e.depth; n >= 0; n--) {
                const r = e.node(n).contentMatchAt(e.indexAfter(n)).defaultType;
                if (r && r.isTextblock && r.defaultAttrs) return r;
            }
        }
        for (const n in this.parser.schema.nodes) {
            const r = this.parser.schema.nodes[n];
            if (r.isTextblock && r.defaultAttrs) return r;
        }
    }
};
function pp(t) {
    for (let e = t.firstChild, n = null; e; e = e.nextSibling) {
        const r = e.nodeType == 1 ? e.nodeName.toLowerCase() : null;
        r && Da.hasOwnProperty(r) && n
            ? (n.appendChild(e), (e = n))
            : r == "li"
              ? (n = e)
              : r && (n = null);
    }
}
function mp(t, e) {
    return (
        t.matches ||
        t.msMatchesSelector ||
        t.webkitMatchesSelector ||
        t.mozMatchesSelector
    ).call(t, e);
}
function ha(t) {
    const e = {};
    for (const n in t) e[n] = t[n];
    return e;
}
function pa(t, e) {
    const n = e.schema.nodes;
    for (const r in n) {
        const o = n[r];
        if (!o.allowsMarkType(t)) continue;
        const i = [];
        const s = (l) => {
            i.push(l);
            for (let a = 0; a < l.edgeCount; a++) {
                const { type: c, next: d } = l.edge(a);
                if (c == e || (i.indexOf(d) < 0 && s(d))) return !0;
            }
        };
        if (s(o.contentMatch)) return !0;
    }
}
const it = class t {
    constructor(e, n) {
        ((this.nodes = e), (this.marks = n));
    }

    serializeFragment(e, n = {}, r) {
        r || (r = Mi(n).createDocumentFragment());
        let o = r;
        const i = [];
        return (
            e.forEach((s) => {
                if (i.length || s.marks.length) {
                    let l = 0;
                    let a = 0;
                    for (; l < i.length && a < s.marks.length; ) {
                        const c = s.marks[a];
                        if (!this.marks[c.type.name]) {
                            a++;
                            continue;
                        }
                        if (!c.eq(i[l][0]) || c.type.spec.spanning === !1) {
                            break;
                        }
                        (l++, a++);
                    }
                    for (; l < i.length; ) o = i.pop()[1];
                    for (; a < s.marks.length; ) {
                        const c = s.marks[a++];
                        const d = this.serializeMark(c, s.isInline, n);
                        d &&
                            (i.push([c, o]),
                            o.appendChild(d.dom),
                            (o = d.contentDOM || d.dom));
                    }
                }
                o.appendChild(this.serializeNodeInner(s, n));
            }),
            r
        );
    }

    serializeNodeInner(e, n) {
        const { dom: r, contentDOM: o } = vr(
            Mi(n),
            this.nodes[e.type.name](e),
            null,
            e.attrs,
        );
        if (o) {
            if (e.isLeaf) {
                throw new RangeError(
                    "Content hole not allowed in a leaf node spec",
                );
            }
            this.serializeFragment(e.content, n, o);
        }
        return r;
    }

    serializeNode(e, n = {}) {
        let r = this.serializeNodeInner(e, n);
        for (let o = e.marks.length - 1; o >= 0; o--) {
            const i = this.serializeMark(e.marks[o], e.isInline, n);
            i && ((i.contentDOM || i.dom).appendChild(r), (r = i.dom));
        }
        return r;
    }

    serializeMark(e, n, r = {}) {
        const o = this.marks[e.type.name];
        return o && vr(Mi(r), o(e, n), null, e.attrs);
    }

    static renderSpec(e, n, r = null, o) {
        return vr(e, n, r, o);
    }

    static fromSchema(e) {
        return (
            e.cached.domSerializer ||
            (e.cached.domSerializer = new t(
                this.nodesFromSchema(e),
                this.marksFromSchema(e),
            ))
        );
    }

    static nodesFromSchema(e) {
        const n = ma(e.nodes);
        return (n.text || (n.text = (r) => r.text), n);
    }

    static marksFromSchema(e) {
        return ma(e.marks);
    }
};
function ma(t) {
    const e = {};
    for (const n in t) {
        const r = t[n].spec.toDOM;
        r && (e[n] = r);
    }
    return e;
}
function Mi(t) {
    return t.document || window.document;
}
const ga = new WeakMap();
function gp(t) {
    let e = ga.get(t);
    return (e === void 0 && ga.set(t, (e = yp(t))), e);
}
function yp(t) {
    let e = null;
    function n(r) {
        if (r && typeof r === "object") {
            if (Array.isArray(r)) {
                if (typeof r[0] === "string") (e || (e = []), e.push(r));
                else for (let o = 0; o < r.length; o++) n(r[o]);
            } else for (const o in r) n(r[o]);
        }
    }
    return (n(t), e);
}
function vr(t, e, n, r) {
    if (typeof e === "string") return { dom: t.createTextNode(e) };
    if (e.nodeType != null) return { dom: e };
    if (e.dom && e.dom.nodeType != null) return e;
    let o = e[0];
    let i;
    if (typeof o !== "string") {
        throw new RangeError("Invalid array passed to renderSpec");
    }
    if (r && (i = gp(r)) && i.indexOf(e) > -1) {
        throw new RangeError(
            "Using an array from an attribute object as a DOM spec. This may be an attempted cross site scripting attack.",
        );
    }
    const s = o.indexOf(" ");
    s > 0 && ((n = o.slice(0, s)), (o = o.slice(s + 1)));
    let l;
    const a = n ? t.createElementNS(n, o) : t.createElement(o);
    const c = e[1];
    let d = 1;
    if (c && typeof c === "object" && c.nodeType == null && !Array.isArray(c)) {
        d = 2;
        for (const u in c) {
            if (c[u] != null) {
                const f = u.indexOf(" ");
                f > 0
                    ? a.setAttributeNS(u.slice(0, f), u.slice(f + 1), c[u])
                    : u == "style" && a.style
                      ? (a.style.cssText = c[u])
                      : a.setAttribute(u, c[u]);
            }
        }
    }
    for (let u = d; u < e.length; u++) {
        const f = e[u];
        if (f === 0) {
            if (u < e.length - 1 || u > d) {
                throw new RangeError(
                    "Content hole must be the only child of its parent node",
                );
            }
            return { dom: a, contentDOM: a };
        } else {
            const { dom: h, contentDOM: p } = vr(t, f, n, r);
            if ((a.appendChild(h), p)) {
                if (l) throw new RangeError("Multiple content holes");
                l = p;
            }
        }
    }
    return { dom: a, contentDOM: l };
}
const La = 65535;
const za = Math.pow(2, 16);
function bp(t, e) {
    return t + e * za;
}
function Ia(t) {
    return t & La;
}
function wp(t) {
    return (t - (t & La)) / za;
}
const Ba = 1;
const Ha = 2;
const Or = 4;
const $a = 8;
const Bn = class {
    constructor(e, n, r) {
        ((this.pos = e), (this.delInfo = n), (this.recover = r));
    }

    get deleted() {
        return (this.delInfo & $a) > 0;
    }

    get deletedBefore() {
        return (this.delInfo & (Ba | Or)) > 0;
    }

    get deletedAfter() {
        return (this.delInfo & (Ha | Or)) > 0;
    }

    get deletedAcross() {
        return (this.delInfo & Or) > 0;
    }
};
const st = class t {
    constructor(e, n = !1) {
        if (((this.ranges = e), (this.inverted = n), !e.length && t.empty)) {
            return t.empty;
        }
    }

    recover(e) {
        let n = 0;
        const r = Ia(e);
        if (!this.inverted) {
            for (let o = 0; o < r; o++) {
                n += this.ranges[o * 3 + 2] - this.ranges[o * 3 + 1];
            }
        }
        return this.ranges[r * 3] + n + wp(e);
    }

    mapResult(e, n = 1) {
        return this._map(e, n, !1);
    }

    map(e, n = 1) {
        return this._map(e, n, !0);
    }

    _map(e, n, r) {
        let o = 0;
        const i = this.inverted ? 2 : 1;
        const s = this.inverted ? 1 : 2;
        for (let l = 0; l < this.ranges.length; l += 3) {
            const a = this.ranges[l] - (this.inverted ? o : 0);
            if (a > e) break;
            const c = this.ranges[l + i];
            const d = this.ranges[l + s];
            const u = a + c;
            if (e <= u) {
                const f = c ? (e == a ? -1 : e == u ? 1 : n) : n;
                const h = a + o + (f < 0 ? 0 : d);
                if (r) return h;
                const p = e == (n < 0 ? a : u) ? null : bp(l / 3, e - a);
                let m = e == a ? Ha : e == u ? Ba : Or;
                return (
                    (n < 0 ? e != a : e != u) && (m |= $a),
                    new Bn(h, m, p)
                );
            }
            o += d - c;
        }
        return r ? e + o : new Bn(e + o, 0, null);
    }

    touches(e, n) {
        let r = 0;
        const o = Ia(n);
        const i = this.inverted ? 2 : 1;
        const s = this.inverted ? 1 : 2;
        for (let l = 0; l < this.ranges.length; l += 3) {
            const a = this.ranges[l] - (this.inverted ? r : 0);
            if (a > e) break;
            const c = this.ranges[l + i];
            const d = a + c;
            if (e <= d && l == o * 3) return !0;
            r += this.ranges[l + s] - c;
        }
        return !1;
    }

    forEach(e) {
        const n = this.inverted ? 2 : 1;
        const r = this.inverted ? 1 : 2;
        for (let o = 0, i = 0; o < this.ranges.length; o += 3) {
            const s = this.ranges[o];
            const l = s - (this.inverted ? i : 0);
            const a = s + (this.inverted ? 0 : i);
            const c = this.ranges[o + n];
            const d = this.ranges[o + r];
            (e(l, l + c, a, a + d), (i += d - c));
        }
    }

    invert() {
        return new t(this.ranges, !this.inverted);
    }

    toString() {
        return (this.inverted ? "-" : "") + JSON.stringify(this.ranges);
    }

    static offset(e) {
        return e == 0 ? t.empty : new t(e < 0 ? [0, -e, 0] : [0, 0, e]);
    }
};
st.empty = new st([]);
const Hn = class t {
    constructor(e, n, r = 0, o = e ? e.length : 0) {
        ((this.mirror = n),
            (this.from = r),
            (this.to = o),
            (this._maps = e || []),
            (this.ownData = !(e || n)));
    }

    get maps() {
        return this._maps;
    }

    slice(e = 0, n = this.maps.length) {
        return new t(this._maps, this.mirror, e, n);
    }

    appendMap(e, n) {
        (this.ownData ||
            ((this._maps = this._maps.slice()),
            (this.mirror = this.mirror && this.mirror.slice()),
            (this.ownData = !0)),
            (this.to = this._maps.push(e)),
            n != null && this.setMirror(this._maps.length - 1, n));
    }

    appendMapping(e) {
        for (let n = 0, r = this._maps.length; n < e._maps.length; n++) {
            const o = e.getMirror(n);
            this.appendMap(e._maps[n], o != null && o < n ? r + o : void 0);
        }
    }

    getMirror(e) {
        if (this.mirror) {
            for (let n = 0; n < this.mirror.length; n++) {
                if (this.mirror[n] == e) {
                    return this.mirror[n + (n % 2 ? -1 : 1)];
                }
            }
        }
    }

    setMirror(e, n) {
        (this.mirror || (this.mirror = []), this.mirror.push(e, n));
    }

    appendMappingInverted(e) {
        for (
            let n = e.maps.length - 1, r = this._maps.length + e._maps.length;
            n >= 0;
            n--
        ) {
            const o = e.getMirror(n);
            this.appendMap(
                e._maps[n].invert(),
                o != null && o > n ? r - o - 1 : void 0,
            );
        }
    }

    invert() {
        const e = new t();
        return (e.appendMappingInverted(this), e);
    }

    map(e, n = 1) {
        if (this.mirror) return this._map(e, n, !0);
        for (let r = this.from; r < this.to; r++) e = this._maps[r].map(e, n);
        return e;
    }

    mapResult(e, n = 1) {
        return this._map(e, n, !1);
    }

    _map(e, n, r) {
        let o = 0;
        for (let i = this.from; i < this.to; i++) {
            const s = this._maps[i];
            const l = s.mapResult(e, n);
            if (l.recover != null) {
                const a = this.getMirror(i);
                if (a != null && a > i && a < this.to) {
                    ((i = a), (e = this._maps[a].recover(l.recover)));
                    continue;
                }
            }
            ((o |= l.delInfo), (e = l.pos));
        }
        return r ? e : new Bn(e, o, null);
    }
};
const Di = Object.create(null);
const ae = class {
    getMap() {
        return st.empty;
    }

    merge(e) {
        return null;
    }

    static fromJSON(e, n) {
        if (!n || !n.stepType) {
            throw new RangeError("Invalid input for Step.fromJSON");
        }
        const r = Di[n.stepType];
        if (!r) throw new RangeError(`No step type ${n.stepType} defined`);
        return r.fromJSON(e, n);
    }

    static jsonID(e, n) {
        if (e in Di) throw new RangeError("Duplicate use of step JSON ID " + e);
        return ((Di[e] = n), (n.prototype.jsonID = e), n);
    }
};
const ce = class t {
    constructor(e, n) {
        ((this.doc = e), (this.failed = n));
    }

    static ok(e) {
        return new t(e, null);
    }

    static fail(e) {
        return new t(null, e);
    }

    static fromReplace(e, n, r, o) {
        try {
            return t.ok(e.replace(n, r, o));
        } catch (i) {
            if (i instanceof Bt) return t.fail(i.message);
            throw i;
        }
    }
};
function Bi(t, e, n) {
    const r = [];
    for (let o = 0; o < t.childCount; o++) {
        let i = t.child(o);
        (i.content.size && (i = i.copy(Bi(i.content, e, i))),
            i.isInline && (i = e(i, n, o)),
            r.push(i));
    }
    return C.fromArray(r);
}
const $n = class t extends ae {
    constructor(e, n, r) {
        (super(), (this.from = e), (this.to = n), (this.mark = r));
    }

    apply(e) {
        const n = e.slice(this.from, this.to);
        const r = e.resolve(this.from);
        const o = r.node(r.sharedDepth(this.to));
        const i = new E(
            Bi(
                n.content,
                (s, l) =>
                    !s.isAtom || !l.type.allowsMarkType(this.mark.type)
                        ? s
                        : s.mark(this.mark.addToSet(s.marks)),
                o,
            ),
            n.openStart,
            n.openEnd,
        );
        return ce.fromReplace(e, this.from, this.to, i);
    }

    invert() {
        return new lt(this.from, this.to, this.mark);
    }

    map(e) {
        const n = e.mapResult(this.from, 1);
        const r = e.mapResult(this.to, -1);
        return (n.deleted && r.deleted) || n.pos >= r.pos
            ? null
            : new t(n.pos, r.pos, this.mark);
    }

    merge(e) {
        return e instanceof t &&
            e.mark.eq(this.mark) &&
            this.from <= e.to &&
            this.to >= e.from
            ? new t(
                  Math.min(this.from, e.from),
                  Math.max(this.to, e.to),
                  this.mark,
              )
            : null;
    }

    toJSON() {
        return {
            stepType: "addMark",
            mark: this.mark.toJSON(),
            from: this.from,
            to: this.to,
        };
    }

    static fromJSON(e, n) {
        if (typeof n.from !== "number" || typeof n.to !== "number") {
            throw new RangeError("Invalid input for AddMarkStep.fromJSON");
        }
        return new t(n.from, n.to, e.markFromJSON(n.mark));
    }
};
ae.jsonID("addMark", $n);
var lt = class t extends ae {
    constructor(e, n, r) {
        (super(), (this.from = e), (this.to = n), (this.mark = r));
    }

    apply(e) {
        const n = e.slice(this.from, this.to);
        const r = new E(
            Bi(n.content, (o) => o.mark(this.mark.removeFromSet(o.marks)), e),
            n.openStart,
            n.openEnd,
        );
        return ce.fromReplace(e, this.from, this.to, r);
    }

    invert() {
        return new $n(this.from, this.to, this.mark);
    }

    map(e) {
        const n = e.mapResult(this.from, 1);
        const r = e.mapResult(this.to, -1);
        return (n.deleted && r.deleted) || n.pos >= r.pos
            ? null
            : new t(n.pos, r.pos, this.mark);
    }

    merge(e) {
        return e instanceof t &&
            e.mark.eq(this.mark) &&
            this.from <= e.to &&
            this.to >= e.from
            ? new t(
                  Math.min(this.from, e.from),
                  Math.max(this.to, e.to),
                  this.mark,
              )
            : null;
    }

    toJSON() {
        return {
            stepType: "removeMark",
            mark: this.mark.toJSON(),
            from: this.from,
            to: this.to,
        };
    }

    static fromJSON(e, n) {
        if (typeof n.from !== "number" || typeof n.to !== "number") {
            throw new RangeError("Invalid input for RemoveMarkStep.fromJSON");
        }
        return new t(n.from, n.to, e.markFromJSON(n.mark));
    }
};
ae.jsonID("removeMark", lt);
const Fn = class t extends ae {
    constructor(e, n) {
        (super(), (this.pos = e), (this.mark = n));
    }

    apply(e) {
        const n = e.nodeAt(this.pos);
        if (!n) return ce.fail("No node at mark step's position");
        const r = n.type.create(n.attrs, null, this.mark.addToSet(n.marks));
        return ce.fromReplace(
            e,
            this.pos,
            this.pos + 1,
            new E(C.from(r), 0, n.isLeaf ? 0 : 1),
        );
    }

    invert(e) {
        const n = e.nodeAt(this.pos);
        if (n) {
            const r = this.mark.addToSet(n.marks);
            if (r.length == n.marks.length) {
                for (let o = 0; o < n.marks.length; o++) {
                    if (!n.marks[o].isInSet(r)) {
                        return new t(this.pos, n.marks[o]);
                    }
                }
                return new t(this.pos, this.mark);
            }
        }
        return new cn(this.pos, this.mark);
    }

    map(e) {
        const n = e.mapResult(this.pos, 1);
        return n.deletedAfter ? null : new t(n.pos, this.mark);
    }

    toJSON() {
        return {
            stepType: "addNodeMark",
            pos: this.pos,
            mark: this.mark.toJSON(),
        };
    }

    static fromJSON(e, n) {
        if (typeof n.pos !== "number") {
            throw new RangeError("Invalid input for AddNodeMarkStep.fromJSON");
        }
        return new t(n.pos, e.markFromJSON(n.mark));
    }
};
ae.jsonID("addNodeMark", Fn);
var cn = class t extends ae {
    constructor(e, n) {
        (super(), (this.pos = e), (this.mark = n));
    }

    apply(e) {
        const n = e.nodeAt(this.pos);
        if (!n) return ce.fail("No node at mark step's position");
        const r = n.type.create(
            n.attrs,
            null,
            this.mark.removeFromSet(n.marks),
        );
        return ce.fromReplace(
            e,
            this.pos,
            this.pos + 1,
            new E(C.from(r), 0, n.isLeaf ? 0 : 1),
        );
    }

    invert(e) {
        const n = e.nodeAt(this.pos);
        return !n || !this.mark.isInSet(n.marks)
            ? this
            : new Fn(this.pos, this.mark);
    }

    map(e) {
        const n = e.mapResult(this.pos, 1);
        return n.deletedAfter ? null : new t(n.pos, this.mark);
    }

    toJSON() {
        return {
            stepType: "removeNodeMark",
            pos: this.pos,
            mark: this.mark.toJSON(),
        };
    }

    static fromJSON(e, n) {
        if (typeof n.pos !== "number") {
            throw new RangeError(
                "Invalid input for RemoveNodeMarkStep.fromJSON",
            );
        }
        return new t(n.pos, e.markFromJSON(n.mark));
    }
};
ae.jsonID("removeNodeMark", cn);
const pe = class t extends ae {
    constructor(e, n, r, o = !1) {
        (super(),
            (this.from = e),
            (this.to = n),
            (this.slice = r),
            (this.structure = o));
    }

    apply(e) {
        return this.structure && Li(e, this.from, this.to)
            ? ce.fail("Structure replace would overwrite content")
            : ce.fromReplace(e, this.from, this.to, this.slice);
    }

    getMap() {
        return new st([this.from, this.to - this.from, this.slice.size]);
    }

    invert(e) {
        return new t(
            this.from,
            this.from + this.slice.size,
            e.slice(this.from, this.to),
        );
    }

    map(e) {
        const n = e.mapResult(this.from, 1);
        const r = e.mapResult(this.to, -1);
        return n.deletedAcross && r.deletedAcross
            ? null
            : new t(n.pos, Math.max(n.pos, r.pos), this.slice, this.structure);
    }

    merge(e) {
        if (!(e instanceof t) || e.structure || this.structure) return null;
        if (
            this.from + this.slice.size == e.from &&
            !this.slice.openEnd &&
            !e.slice.openStart
        ) {
            const n =
                this.slice.size + e.slice.size == 0
                    ? E.empty
                    : new E(
                          this.slice.content.append(e.slice.content),
                          this.slice.openStart,
                          e.slice.openEnd,
                      );
            return new t(
                this.from,
                this.to + (e.to - e.from),
                n,
                this.structure,
            );
        } else if (
            e.to == this.from &&
            !this.slice.openStart &&
            !e.slice.openEnd
        ) {
            const n =
                this.slice.size + e.slice.size == 0
                    ? E.empty
                    : new E(
                          e.slice.content.append(this.slice.content),
                          e.slice.openStart,
                          this.slice.openEnd,
                      );
            return new t(e.from, this.to, n, this.structure);
        } else return null;
    }

    toJSON() {
        const e = { stepType: "replace", from: this.from, to: this.to };
        return (
            this.slice.size && (e.slice = this.slice.toJSON()),
            this.structure && (e.structure = !0),
            e
        );
    }

    static fromJSON(e, n) {
        if (typeof n.from !== "number" || typeof n.to !== "number") {
            throw new RangeError("Invalid input for ReplaceStep.fromJSON");
        }
        return new t(n.from, n.to, E.fromJSON(e, n.slice), !!n.structure);
    }
};
ae.jsonID("replace", pe);
const re = class t extends ae {
    constructor(e, n, r, o, i, s, l = !1) {
        (super(),
            (this.from = e),
            (this.to = n),
            (this.gapFrom = r),
            (this.gapTo = o),
            (this.slice = i),
            (this.insert = s),
            (this.structure = l));
    }

    apply(e) {
        if (
            this.structure &&
            (Li(e, this.from, this.gapFrom) || Li(e, this.gapTo, this.to))
        ) {
            return ce.fail("Structure gap-replace would overwrite content");
        }
        const n = e.slice(this.gapFrom, this.gapTo);
        if (n.openStart || n.openEnd) return ce.fail("Gap is not a flat range");
        const r = this.slice.insertAt(this.insert, n.content);
        return r
            ? ce.fromReplace(e, this.from, this.to, r)
            : ce.fail("Content does not fit in gap");
    }

    getMap() {
        return new st([
            this.from,
            this.gapFrom - this.from,
            this.insert,
            this.gapTo,
            this.to - this.gapTo,
            this.slice.size - this.insert,
        ]);
    }

    invert(e) {
        const n = this.gapTo - this.gapFrom;
        return new t(
            this.from,
            this.from + this.slice.size + n,
            this.from + this.insert,
            this.from + this.insert + n,
            e
                .slice(this.from, this.to)
                .removeBetween(
                    this.gapFrom - this.from,
                    this.gapTo - this.from,
                ),
            this.gapFrom - this.from,
            this.structure,
        );
    }

    map(e) {
        const n = e.mapResult(this.from, 1);
        const r = e.mapResult(this.to, -1);
        const o = this.from == this.gapFrom ? n.pos : e.map(this.gapFrom, -1);
        const i = this.to == this.gapTo ? r.pos : e.map(this.gapTo, 1);
        return (n.deletedAcross && r.deletedAcross) || o < n.pos || i > r.pos
            ? null
            : new t(
                  n.pos,
                  r.pos,
                  o,
                  i,
                  this.slice,
                  this.insert,
                  this.structure,
              );
    }

    toJSON() {
        const e = {
            stepType: "replaceAround",
            from: this.from,
            to: this.to,
            gapFrom: this.gapFrom,
            gapTo: this.gapTo,
            insert: this.insert,
        };
        return (
            this.slice.size && (e.slice = this.slice.toJSON()),
            this.structure && (e.structure = !0),
            e
        );
    }

    static fromJSON(e, n) {
        if (
            typeof n.from !== "number" ||
            typeof n.to !== "number" ||
            typeof n.gapFrom !== "number" ||
            typeof n.gapTo !== "number" ||
            typeof n.insert !== "number"
        ) {
            throw new RangeError(
                "Invalid input for ReplaceAroundStep.fromJSON",
            );
        }
        return new t(
            n.from,
            n.to,
            n.gapFrom,
            n.gapTo,
            E.fromJSON(e, n.slice),
            n.insert,
            !!n.structure,
        );
    }
};
ae.jsonID("replaceAround", re);
function Li(t, e, n) {
    const r = t.resolve(e);
    let o = n - e;
    let i = r.depth;
    for (; o > 0 && i > 0 && r.indexAfter(i) == r.node(i).childCount; ) {
        (i--, o--);
    }
    if (o > 0) {
        let s = r.node(i).maybeChild(r.indexAfter(i));
        for (; o > 0; ) {
            if (!s || s.isLeaf) return !0;
            ((s = s.firstChild), o--);
        }
    }
    return !1;
}
function xp(t, e, n, r) {
    const o = [];
    const i = [];
    let s;
    let l;
    (t.doc.nodesBetween(e, n, (a, c, d) => {
        if (!a.isInline) return;
        const u = a.marks;
        if (!r.isInSet(u) && d.type.allowsMarkType(r.type)) {
            const f = Math.max(c, e);
            const h = Math.min(c + a.nodeSize, n);
            const p = r.addToSet(u);
            for (let m = 0; m < u.length; m++) {
                u[m].isInSet(p) ||
                    (s && s.to == f && s.mark.eq(u[m])
                        ? (s.to = h)
                        : o.push((s = new lt(f, h, u[m]))));
            }
            l && l.to == f ? (l.to = h) : i.push((l = new $n(f, h, r)));
        }
    }),
        o.forEach((a) => t.step(a)),
        i.forEach((a) => t.step(a)));
}
function kp(t, e, n, r) {
    const o = [];
    let i = 0;
    (t.doc.nodesBetween(e, n, (s, l) => {
        if (!s.isInline) return;
        i++;
        let a = null;
        if (r instanceof In) {
            let c = s.marks;
            let d;
            for (; (d = r.isInSet(c)); ) {
                ((a || (a = [])).push(d), (c = d.removeFromSet(c)));
            }
        } else r ? r.isInSet(s.marks) && (a = [r]) : (a = s.marks);
        if (a && a.length) {
            const c = Math.min(l + s.nodeSize, n);
            for (let d = 0; d < a.length; d++) {
                const u = a[d];
                let f;
                for (let h = 0; h < o.length; h++) {
                    const p = o[h];
                    p.step == i - 1 && u.eq(o[h].style) && (f = p);
                }
                f
                    ? ((f.to = c), (f.step = i))
                    : o.push({
                          style: u,
                          from: Math.max(l, e),
                          to: c,
                          step: i,
                      });
            }
        }
    }),
        o.forEach((s) => t.step(new lt(s.from, s.to, s.style))));
}
function Hi(t, e, n, r = n.contentMatch, o = !0) {
    const i = t.doc.nodeAt(e);
    const s = [];
    let l = e + 1;
    for (let a = 0; a < i.childCount; a++) {
        const c = i.child(a);
        const d = l + c.nodeSize;
        const u = r.matchType(c.type);
        if (!u) s.push(new pe(l, d, E.empty));
        else {
            r = u;
            for (let f = 0; f < c.marks.length; f++) {
                n.allowsMarkType(c.marks[f].type) ||
                    t.step(new lt(l, d, c.marks[f]));
            }
            if (o && c.isText && n.whitespace != "pre") {
                let f;
                const h = /\r?\n|\r/g;
                let p;
                for (; (f = h.exec(c.text)); ) {
                    (p ||
                        (p = new E(
                            C.from(n.schema.text(" ", n.allowedMarks(c.marks))),
                            0,
                            0,
                        )),
                        s.push(
                            new pe(l + f.index, l + f.index + f[0].length, p),
                        ));
                }
            }
        }
        l = d;
    }
    if (!r.validEnd) {
        const a = r.fillBefore(C.empty, !0);
        t.replace(l, l, new E(a, 0, 0));
    }
    for (let a = s.length - 1; a >= 0; a--) t.step(s[a]);
}
function Sp(t, e, n) {
    return (
        (e == 0 || t.canReplace(e, t.childCount)) &&
        (n == t.childCount || t.canReplace(0, n))
    );
}
function at(t) {
    const n = t.parent.content.cutByIndex(t.startIndex, t.endIndex);
    for (let r = t.depth; ; --r) {
        const o = t.$from.node(r);
        const i = t.$from.index(r);
        const s = t.$to.indexAfter(r);
        if (r < t.depth && o.canReplace(i, s, n)) return r;
        if (r == 0 || o.type.spec.isolating || !Sp(o, i, s)) break;
    }
    return null;
}
function Cp(t, e, n) {
    const { $from: r, $to: o, depth: i } = e;
    const s = r.before(i + 1);
    const l = o.after(i + 1);
    let a = s;
    let c = l;
    let d = C.empty;
    let u = 0;
    for (let p = i, m = !1; p > n; p--) {
        m || r.index(p) > 0
            ? ((m = !0), (d = C.from(r.node(p).copy(d))), u++)
            : a--;
    }
    let f = C.empty;
    let h = 0;
    for (let p = i, m = !1; p > n; p--) {
        m || o.after(p + 1) < o.end(p)
            ? ((m = !0), (f = C.from(o.node(p).copy(f))), h++)
            : c++;
    }
    t.step(new re(a, c, s, l, new E(d.append(f), u, h), d.size - u, !0));
}
function un(t, e, n = null, r = t) {
    const o = vp(t, e);
    const i = o && Mp(r, e);
    return i ? o.map(Pa).concat({ type: e, attrs: n }).concat(i.map(Pa)) : null;
}
function Pa(t) {
    return { type: t, attrs: null };
}
function vp(t, e) {
    const { parent: n, startIndex: r, endIndex: o } = t;
    const i = n.contentMatchAt(r).findWrapping(e);
    if (!i) return null;
    const s = i.length ? i[0] : e;
    return n.canReplaceWith(r, o, s) ? i : null;
}
function Mp(t, e) {
    const { parent: n, startIndex: r, endIndex: o } = t;
    const i = n.child(r);
    const s = e.contentMatch.findWrapping(i.type);
    if (!s) return null;
    let a = (s.length ? s[s.length - 1] : e).contentMatch;
    for (let c = r; a && c < o; c++) a = a.matchType(n.child(c).type);
    return !a || !a.validEnd ? null : s;
}
function Tp(t, e, n) {
    let r = C.empty;
    for (let s = n.length - 1; s >= 0; s--) {
        if (r.size) {
            const l = n[s].type.contentMatch.matchFragment(r);
            if (!l || !l.validEnd) {
                throw new RangeError(
                    "Wrapper type given to Transform.wrap does not form valid content of its parent wrapper",
                );
            }
        }
        r = C.from(n[s].type.create(n[s].attrs, r));
    }
    const o = e.start;
    const i = e.end;
    t.step(new re(o, i, o, i, new E(r, 0, 0), n.length, !0));
}
function Ap(t, e, n, r, o) {
    if (!r.isTextblock) {
        throw new RangeError(
            "Type given to setBlockType should be a textblock",
        );
    }
    const i = t.steps.length;
    t.doc.nodesBetween(e, n, (s, l) => {
        const a = typeof o === "function" ? o(s) : o;
        if (
            s.isTextblock &&
            !s.hasMarkup(r, a) &&
            Ep(t.doc, t.mapping.slice(i).map(l), r)
        ) {
            let c = null;
            if (r.schema.linebreakReplacement) {
                const h = r.whitespace == "pre";
                const p = !!r.contentMatch.matchType(
                    r.schema.linebreakReplacement,
                );
                h && !p ? (c = !1) : !h && p && (c = !0);
            }
            (c === !1 && _a(t, s, l, i),
                Hi(t, t.mapping.slice(i).map(l, 1), r, void 0, c === null));
            const d = t.mapping.slice(i);
            const u = d.map(l, 1);
            const f = d.map(l + s.nodeSize, 1);
            return (
                t.step(
                    new re(
                        u,
                        f,
                        u + 1,
                        f - 1,
                        new E(C.from(r.create(a, null, s.marks)), 0, 0),
                        1,
                        !0,
                    ),
                ),
                c === !0 && Fa(t, s, l, i),
                !1
            );
        }
    });
}
function Fa(t, e, n, r) {
    e.forEach((o, i) => {
        if (o.isText) {
            let s;
            const l = /\r?\n|\r/g;
            for (; (s = l.exec(o.text)); ) {
                const a = t.mapping.slice(r).map(n + 1 + i + s.index);
                t.replaceWith(
                    a,
                    a + 1,
                    e.type.schema.linebreakReplacement.create(),
                );
            }
        }
    });
}
function _a(t, e, n, r) {
    e.forEach((o, i) => {
        if (o.type == o.type.schema.linebreakReplacement) {
            const s = t.mapping.slice(r).map(n + 1 + i);
            t.replaceWith(
                s,
                s + 1,
                e.type.schema.text(`
`),
            );
        }
    });
}
function Ep(t, e, n) {
    const r = t.resolve(e);
    const o = r.index();
    return r.parent.canReplaceWith(o, o + 1, n);
}
function Np(t, e, n, r, o) {
    const i = t.doc.nodeAt(e);
    if (!i) throw new RangeError("No node at given position");
    n || (n = i.type);
    const s = n.create(r, null, o || i.marks);
    if (i.isLeaf) return t.replaceWith(e, e + i.nodeSize, s);
    if (!n.validContent(i.content)) {
        throw new RangeError("Invalid content for node type " + n.name);
    }
    t.step(
        new re(
            e,
            e + i.nodeSize,
            e + 1,
            e + i.nodeSize - 1,
            new E(C.from(s), 0, 0),
            1,
            !0,
        ),
    );
}
function Ae(t, e, n = 1, r) {
    const o = t.resolve(e);
    const i = o.depth - n;
    const s = (r && r[r.length - 1]) || o.parent;
    if (
        i < 0 ||
        o.parent.type.spec.isolating ||
        !o.parent.canReplace(o.index(), o.parent.childCount) ||
        !s.type.validContent(
            o.parent.content.cutByIndex(o.index(), o.parent.childCount),
        )
    ) {
        return !1;
    }
    for (let c = o.depth - 1, d = n - 2; c > i; c--, d--) {
        const u = o.node(c);
        const f = o.index(c);
        if (u.type.spec.isolating) return !1;
        let h = u.content.cutByIndex(f, u.childCount);
        const p = r && r[d + 1];
        p && (h = h.replaceChild(0, p.type.create(p.attrs)));
        const m = (r && r[d]) || u;
        if (!u.canReplace(f + 1, u.childCount) || !m.type.validContent(h)) {
            return !1;
        }
    }
    const l = o.indexAfter(i);
    const a = r && r[0];
    return o.node(i).canReplaceWith(l, l, a ? a.type : o.node(i + 1).type);
}
function Op(t, e, n = 1, r) {
    const o = t.doc.resolve(e);
    let i = C.empty;
    let s = C.empty;
    for (let l = o.depth, a = o.depth - n, c = n - 1; l > a; l--, c--) {
        i = C.from(o.node(l).copy(i));
        const d = r && r[c];
        s = C.from(d ? d.type.create(d.attrs, s) : o.node(l).copy(s));
    }
    t.step(new pe(e, e, new E(i.append(s), n, n), !0));
}
function Oe(t, e) {
    const n = t.resolve(e);
    const r = n.index();
    return Va(n.nodeBefore, n.nodeAfter) && n.parent.canReplace(r, r + 1);
}
function Rp(t, e) {
    e.content.size || t.type.compatibleContent(e.type);
    let n = t.contentMatchAt(t.childCount);
    const { linebreakReplacement: r } = t.type.schema;
    for (let o = 0; o < e.childCount; o++) {
        const i = e.child(o);
        const s = i.type == r ? t.type.schema.nodes.text : i.type;
        if (((n = n.matchType(s)), !n || !t.type.allowsMarks(i.marks))) {
            return !1;
        }
    }
    return n.validEnd;
}
function Va(t, e) {
    return !!(t && e && !t.isLeaf && Rp(t, e));
}
function Ft(t, e, n = -1) {
    const r = t.resolve(e);
    for (let o = r.depth; ; o--) {
        let i;
        let s;
        let l = r.index(o);
        if (
            (o == r.depth
                ? ((i = r.nodeBefore), (s = r.nodeAfter))
                : n > 0
                  ? ((i = r.node(o + 1)), l++, (s = r.node(o).maybeChild(l)))
                  : ((i = r.node(o).maybeChild(l - 1)), (s = r.node(o + 1))),
            i && !i.isTextblock && Va(i, s) && r.node(o).canReplace(l, l + 1))
        ) {
            return e;
        }
        if (o == 0) break;
        e = n < 0 ? r.before(o) : r.after(o);
    }
}
function Dp(t, e, n) {
    let r = null;
    const { linebreakReplacement: o } = t.doc.type.schema;
    const i = t.doc.resolve(e - n);
    const s = i.node().type;
    if (o && s.inlineContent) {
        const d = s.whitespace == "pre";
        const u = !!s.contentMatch.matchType(o);
        d && !u ? (r = !1) : !d && u && (r = !0);
    }
    const l = t.steps.length;
    if (r === !1) {
        const d = t.doc.resolve(e + n);
        _a(t, d.node(), d.before(), l);
    }
    s.inlineContent &&
        Hi(t, e + n - 1, s, i.node().contentMatchAt(i.index()), r == null);
    const a = t.mapping.slice(l);
    const c = a.map(e - n);
    if ((t.step(new pe(c, a.map(e + n, -1), E.empty, !0)), r === !0)) {
        const d = t.doc.resolve(c);
        Fa(t, d.node(), d.before(), t.steps.length);
    }
    return t;
}
function Ip(t, e, n) {
    const r = t.resolve(e);
    if (r.parent.canReplaceWith(r.index(), r.index(), n)) return e;
    if (r.parentOffset == 0) {
        for (let o = r.depth - 1; o >= 0; o--) {
            const i = r.index(o);
            if (r.node(o).canReplaceWith(i, i, n)) return r.before(o + 1);
            if (i > 0) return null;
        }
    }
    if (r.parentOffset == r.parent.content.size) {
        for (let o = r.depth - 1; o >= 0; o--) {
            const i = r.indexAfter(o);
            if (r.node(o).canReplaceWith(i, i, n)) return r.after(o + 1);
            if (i < r.node(o).childCount) return null;
        }
    }
    return null;
}
function Ir(t, e, n) {
    const r = t.resolve(e);
    if (!n.content.size) return e;
    let o = n.content;
    for (let i = 0; i < n.openStart; i++) o = o.firstChild.content;
    for (let i = 1; i <= (n.openStart == 0 && n.size ? 2 : 1); i++) {
        for (let s = r.depth; s >= 0; s--) {
            const l =
                s == r.depth
                    ? 0
                    : r.pos <= (r.start(s + 1) + r.end(s + 1)) / 2
                      ? -1
                      : 1;
            const a = r.index(s) + (l > 0 ? 1 : 0);
            const c = r.node(s);
            let d = !1;
            if (i == 1) d = c.canReplace(a, a, o);
            else {
                const u = c.contentMatchAt(a).findWrapping(o.firstChild.type);
                d = u && c.canReplaceWith(a, a, u[0]);
            }
            if (d) {
                return l == 0
                    ? r.pos
                    : l < 0
                      ? r.before(s + 1)
                      : r.after(s + 1);
            }
        }
    }
    return null;
}
function _n(t, e, n = e, r = E.empty) {
    if (e == n && !r.size) return null;
    const o = t.resolve(e);
    const i = t.resolve(n);
    return Wa(o, i, r) ? new pe(e, n, r) : new zi(o, i, r).fit();
}
function Wa(t, e, n) {
    return (
        !n.openStart &&
        !n.openEnd &&
        t.start() == e.start() &&
        t.parent.canReplace(t.index(), e.index(), n.content)
    );
}
var zi = class {
    constructor(e, n, r) {
        ((this.$from = e),
            (this.$to = n),
            (this.unplaced = r),
            (this.frontier = []),
            (this.placed = C.empty));
        for (let o = 0; o <= e.depth; o++) {
            const i = e.node(o);
            this.frontier.push({
                type: i.type,
                match: i.contentMatchAt(e.indexAfter(o)),
            });
        }
        for (let o = e.depth; o > 0; o--) {
            this.placed = C.from(e.node(o).copy(this.placed));
        }
    }

    get depth() {
        return this.frontier.length - 1;
    }

    fit() {
        for (; this.unplaced.size; ) {
            const c = this.findFittable();
            c ? this.placeNodes(c) : this.openMore() || this.dropNode();
        }
        const e = this.mustMoveInline();
        const n = this.placed.size - this.depth - this.$from.depth;
        const r = this.$from;
        const o = this.close(e < 0 ? this.$to : r.doc.resolve(e));
        if (!o) return null;
        let i = this.placed;
        let s = r.depth;
        let l = o.depth;
        for (; s && l && i.childCount == 1; ) {
            ((i = i.firstChild.content), s--, l--);
        }
        const a = new E(i, s, l);
        return e > -1
            ? new re(r.pos, e, this.$to.pos, this.$to.end(), a, n)
            : a.size || r.pos != this.$to.pos
              ? new pe(r.pos, o.pos, a)
              : null;
    }

    findFittable() {
        let e = this.unplaced.openStart;
        for (
            let n = this.unplaced.content, r = 0, o = this.unplaced.openEnd;
            r < e;
            r++
        ) {
            const i = n.firstChild;
            if (
                (n.childCount > 1 && (o = 0), i.type.spec.isolating && o <= r)
            ) {
                e = r;
                break;
            }
            n = i.content;
        }
        for (let n = 1; n <= 2; n++) {
            for (let r = n == 1 ? e : this.unplaced.openStart; r >= 0; r--) {
                let o;
                let i = null;
                r
                    ? ((i = Ii(this.unplaced.content, r - 1).firstChild),
                      (o = i.content))
                    : (o = this.unplaced.content);
                const s = o.firstChild;
                for (let l = this.depth; l >= 0; l--) {
                    const { type: a, match: c } = this.frontier[l];
                    let d;
                    let u = null;
                    if (
                        n == 1 &&
                        (s
                            ? c.matchType(s.type) ||
                              (u = c.fillBefore(C.from(s), !1))
                            : i && a.compatibleContent(i.type))
                    ) {
                        return {
                            sliceDepth: r,
                            frontierDepth: l,
                            parent: i,
                            inject: u,
                        };
                    }
                    if (n == 2 && s && (d = c.findWrapping(s.type))) {
                        return {
                            sliceDepth: r,
                            frontierDepth: l,
                            parent: i,
                            wrap: d,
                        };
                    }
                    if (i && c.matchType(i.type)) break;
                }
            }
        }
    }

    openMore() {
        const { content: e, openStart: n, openEnd: r } = this.unplaced;
        const o = Ii(e, n);
        return !o.childCount || o.firstChild.isLeaf
            ? !1
            : ((this.unplaced = new E(
                  e,
                  n + 1,
                  Math.max(r, o.size + n >= e.size - r ? n + 1 : 0),
              )),
              !0);
    }

    dropNode() {
        const { content: e, openStart: n, openEnd: r } = this.unplaced;
        const o = Ii(e, n);
        if (o.childCount <= 1 && n > 0) {
            const i = e.size - n <= n + o.size;
            this.unplaced = new E(Ln(e, n - 1, 1), n - 1, i ? n - 1 : r);
        } else this.unplaced = new E(Ln(e, n, 1), n, r);
    }

    placeNodes({
        sliceDepth: e,
        frontierDepth: n,
        parent: r,
        inject: o,
        wrap: i,
    }) {
        for (; this.depth > n; ) this.closeFrontierNode();
        if (i) for (let m = 0; m < i.length; m++) this.openFrontierNode(i[m]);
        const s = this.unplaced;
        const l = r ? r.content : s.content;
        const a = s.openStart - e;
        let c = 0;
        const d = [];
        let { match: u, type: f } = this.frontier[n];
        if (o) {
            for (let m = 0; m < o.childCount; m++) d.push(o.child(m));
            u = u.matchFragment(o);
        }
        let h = l.size + e - (s.content.size - s.openEnd);
        for (; c < l.childCount; ) {
            const m = l.child(c);
            const g = u.matchType(m.type);
            if (!g) break;
            (c++,
                (c > 1 || a == 0 || m.content.size) &&
                    ((u = g),
                    d.push(
                        ja(
                            m.mark(f.allowedMarks(m.marks)),
                            c == 1 ? a : 0,
                            c == l.childCount ? h : -1,
                        ),
                    )));
        }
        const p = c == l.childCount;
        (p || (h = -1),
            (this.placed = zn(this.placed, n, C.from(d))),
            (this.frontier[n].match = u),
            p &&
                h < 0 &&
                r &&
                r.type == this.frontier[this.depth].type &&
                this.frontier.length > 1 &&
                this.closeFrontierNode());
        for (let m = 0, g = l; m < h; m++) {
            const y = g.lastChild;
            (this.frontier.push({
                type: y.type,
                match: y.contentMatchAt(y.childCount),
            }),
                (g = y.content));
        }
        this.unplaced = p
            ? e == 0
                ? E.empty
                : new E(
                      Ln(s.content, e - 1, 1),
                      e - 1,
                      h < 0 ? s.openEnd : e - 1,
                  )
            : new E(Ln(s.content, e, c), s.openStart, s.openEnd);
    }

    mustMoveInline() {
        if (!this.$to.parent.isTextblock) return -1;
        const e = this.frontier[this.depth];
        let n;
        if (
            !e.type.isTextblock ||
            !Pi(this.$to, this.$to.depth, e.type, e.match, !1) ||
            (this.$to.depth == this.depth &&
                (n = this.findCloseLevel(this.$to)) &&
                n.depth == this.depth)
        ) {
            return -1;
        }
        let { depth: r } = this.$to;
        let o = this.$to.after(r);
        for (; r > 1 && o == this.$to.end(--r); ) ++o;
        return o;
    }

    findCloseLevel(e) {
        e: for (let n = Math.min(this.depth, e.depth); n >= 0; n--) {
            const { match: r, type: o } = this.frontier[n];
            const i =
                n < e.depth && e.end(n + 1) == e.pos + (e.depth - (n + 1));
            const s = Pi(e, n, o, r, i);
            if (s) {
                for (let l = n - 1; l >= 0; l--) {
                    const { match: a, type: c } = this.frontier[l];
                    const d = Pi(e, l, c, a, !0);
                    if (!d || d.childCount) continue e;
                }
                return {
                    depth: n,
                    fit: s,
                    move: i ? e.doc.resolve(e.after(n + 1)) : e,
                };
            }
        }
    }

    close(e) {
        const n = this.findCloseLevel(e);
        if (!n) return null;
        for (; this.depth > n.depth; ) this.closeFrontierNode();
        (n.fit.childCount && (this.placed = zn(this.placed, n.depth, n.fit)),
            (e = n.move));
        for (let r = n.depth + 1; r <= e.depth; r++) {
            const o = e.node(r);
            const i = o.type.contentMatch.fillBefore(o.content, !0, e.index(r));
            this.openFrontierNode(o.type, o.attrs, i);
        }
        return e;
    }

    openFrontierNode(e, n = null, r) {
        const o = this.frontier[this.depth];
        ((o.match = o.match.matchType(e)),
            (this.placed = zn(this.placed, this.depth, C.from(e.create(n, r)))),
            this.frontier.push({ type: e, match: e.contentMatch }));
    }

    closeFrontierNode() {
        const n = this.frontier.pop().match.fillBefore(C.empty, !0);
        n.childCount &&
            (this.placed = zn(this.placed, this.frontier.length, n));
    }
};
function Ln(t, e, n) {
    return e == 0
        ? t.cutByIndex(n, t.childCount)
        : t.replaceChild(
              0,
              t.firstChild.copy(Ln(t.firstChild.content, e - 1, n)),
          );
}
function zn(t, e, n) {
    return e == 0
        ? t.append(n)
        : t.replaceChild(
              t.childCount - 1,
              t.lastChild.copy(zn(t.lastChild.content, e - 1, n)),
          );
}
function Ii(t, e) {
    for (let n = 0; n < e; n++) t = t.firstChild.content;
    return t;
}
function ja(t, e, n) {
    if (e <= 0) return t;
    let r = t.content;
    return (
        e > 1 &&
            (r = r.replaceChild(
                0,
                ja(r.firstChild, e - 1, r.childCount == 1 ? n - 1 : 0),
            )),
        e > 0 &&
            ((r = t.type.contentMatch.fillBefore(r).append(r)),
            n <= 0 &&
                (r = r.append(
                    t.type.contentMatch
                        .matchFragment(r)
                        .fillBefore(C.empty, !0),
                ))),
        t.copy(r)
    );
}
function Pi(t, e, n, r, o) {
    const i = t.node(e);
    const s = o ? t.indexAfter(e) : t.index(e);
    if (s == i.childCount && !n.compatibleContent(i.type)) return null;
    const l = r.fillBefore(i.content, !0, s);
    return l && !Pp(n, i.content, s) ? l : null;
}
function Pp(t, e, n) {
    for (let r = n; r < e.childCount; r++) {
        if (!t.allowsMarks(e.child(r).marks)) return !0;
    }
    return !1;
}
function Lp(t) {
    return t.spec.defining || t.spec.definingForContent;
}
function zp(t, e, n, r) {
    if (!r.size) return t.deleteRange(e, n);
    const o = t.doc.resolve(e);
    const i = t.doc.resolve(n);
    if (Wa(o, i, r)) return t.step(new pe(e, n, r));
    const s = Ua(o, t.doc.resolve(n));
    s[s.length - 1] == 0 && s.pop();
    let l = -(o.depth + 1);
    s.unshift(l);
    for (let f = o.depth, h = o.pos - 1; f > 0; f--, h--) {
        const p = o.node(f).type.spec;
        if (p.defining || p.definingAsContext || p.isolating) break;
        s.indexOf(f) > -1 ? (l = f) : o.before(f) == h && s.splice(1, 0, -f);
    }
    const a = s.indexOf(l);
    const c = [];
    let d = r.openStart;
    for (let f = r.content, h = 0; ; h++) {
        const p = f.firstChild;
        if ((c.push(p), h == r.openStart)) break;
        f = p.content;
    }
    for (let f = d - 1; f >= 0; f--) {
        const h = c[f];
        const p = Lp(h.type);
        if (p && !h.sameMarkup(o.node(Math.abs(l) - 1))) d = f;
        else if (p || !h.type.isTextblock) break;
    }
    for (let f = r.openStart; f >= 0; f--) {
        const h = (f + d + 1) % (r.openStart + 1);
        const p = c[h];
        if (p) {
            for (let m = 0; m < s.length; m++) {
                let g = s[(m + a) % s.length];
                let y = !0;
                g < 0 && ((y = !1), (g = -g));
                const b = o.node(g - 1);
                const k = o.index(g - 1);
                if (b.canReplaceWith(k, k, p.type, p.marks)) {
                    return t.replace(
                        o.before(g),
                        y ? i.after(g) : n,
                        new E(Ka(r.content, 0, r.openStart, h), h, r.openEnd),
                    );
                }
            }
        }
    }
    const u = t.steps.length;
    for (
        let f = s.length - 1;
        f >= 0 && (t.replace(e, n, r), !(t.steps.length > u));
        f--
    ) {
        const h = s[f];
        h < 0 || ((e = o.before(h)), (n = i.after(h)));
    }
}
function Ka(t, e, n, r, o) {
    if (e < n) {
        const i = t.firstChild;
        t = t.replaceChild(0, i.copy(Ka(i.content, e + 1, n, r, i)));
    }
    if (e > r) {
        const i = o.contentMatchAt(0);
        const s = i.fillBefore(t).append(t);
        t = s.append(i.matchFragment(s).fillBefore(C.empty, !0));
    }
    return t;
}
function Bp(t, e, n, r) {
    if (!r.isInline && e == n && t.doc.resolve(e).parent.content.size) {
        const o = Ip(t.doc, e, r.type);
        o != null && (e = n = o);
    }
    t.replaceRange(e, n, new E(C.from(r), 0, 0));
}
function Hp(t, e, n) {
    const r = t.doc.resolve(e);
    const o = t.doc.resolve(n);
    const i = Ua(r, o);
    for (let s = 0; s < i.length; s++) {
        const l = i[s];
        const a = s == i.length - 1;
        if ((a && l == 0) || r.node(l).type.contentMatch.validEnd) {
            return t.delete(r.start(l), o.end(l));
        }
        if (
            l > 0 &&
            (a || r.node(l - 1).canReplace(r.index(l - 1), o.indexAfter(l - 1)))
        ) {
            return t.delete(r.before(l), o.after(l));
        }
    }
    for (let s = 1; s <= r.depth && s <= o.depth; s++) {
        if (
            e - r.start(s) == r.depth - s &&
            n > r.end(s) &&
            o.end(s) - n != o.depth - s &&
            r.start(s - 1) == o.start(s - 1) &&
            r.node(s - 1).canReplace(r.index(s - 1), o.index(s - 1))
        ) {
            return t.delete(r.before(s), n);
        }
    }
    t.delete(e, n);
}
function Ua(t, e) {
    const n = [];
    const r = Math.min(t.depth, e.depth);
    for (let o = r; o >= 0; o--) {
        const i = t.start(o);
        if (
            i < t.pos - (t.depth - o) ||
            e.end(o) > e.pos + (e.depth - o) ||
            t.node(o).type.spec.isolating ||
            e.node(o).type.spec.isolating
        ) {
            break;
        }
        (i == e.start(o) ||
            (o == t.depth &&
                o == e.depth &&
                t.parent.inlineContent &&
                e.parent.inlineContent &&
                o &&
                e.start(o - 1) == i - 1)) &&
            n.push(o);
    }
    return n;
}
const Rr = class t extends ae {
    constructor(e, n, r) {
        (super(), (this.pos = e), (this.attr = n), (this.value = r));
    }

    apply(e) {
        const n = e.nodeAt(this.pos);
        if (!n) return ce.fail("No node at attribute step's position");
        const r = Object.create(null);
        for (const i in n.attrs) r[i] = n.attrs[i];
        r[this.attr] = this.value;
        const o = n.type.create(r, null, n.marks);
        return ce.fromReplace(
            e,
            this.pos,
            this.pos + 1,
            new E(C.from(o), 0, n.isLeaf ? 0 : 1),
        );
    }

    getMap() {
        return st.empty;
    }

    invert(e) {
        return new t(this.pos, this.attr, e.nodeAt(this.pos).attrs[this.attr]);
    }

    map(e) {
        const n = e.mapResult(this.pos, 1);
        return n.deletedAfter ? null : new t(n.pos, this.attr, this.value);
    }

    toJSON() {
        return {
            stepType: "attr",
            pos: this.pos,
            attr: this.attr,
            value: this.value,
        };
    }

    static fromJSON(e, n) {
        if (typeof n.pos !== "number" || typeof n.attr !== "string") {
            throw new RangeError("Invalid input for AttrStep.fromJSON");
        }
        return new t(n.pos, n.attr, n.value);
    }
};
ae.jsonID("attr", Rr);
const Dr = class t extends ae {
    constructor(e, n) {
        (super(), (this.attr = e), (this.value = n));
    }

    apply(e) {
        const n = Object.create(null);
        for (const o in e.attrs) n[o] = e.attrs[o];
        n[this.attr] = this.value;
        const r = e.type.create(n, e.content, e.marks);
        return ce.ok(r);
    }

    getMap() {
        return st.empty;
    }

    invert(e) {
        return new t(this.attr, e.attrs[this.attr]);
    }

    map(e) {
        return this;
    }

    toJSON() {
        return { stepType: "docAttr", attr: this.attr, value: this.value };
    }

    static fromJSON(e, n) {
        if (typeof n.attr !== "string") {
            throw new RangeError("Invalid input for DocAttrStep.fromJSON");
        }
        return new t(n.attr, n.value);
    }
};
ae.jsonID("docAttr", Dr);
let dn = class extends Error {};
dn = function t(e) {
    const n = Error.call(this, e);
    return ((n.__proto__ = t.prototype), n);
};
dn.prototype = Object.create(Error.prototype);
dn.prototype.constructor = dn;
dn.prototype.name = "TransformError";
const St = class {
    constructor(e) {
        ((this.doc = e),
            (this.steps = []),
            (this.docs = []),
            (this.mapping = new Hn()));
    }

    get before() {
        return this.docs.length ? this.docs[0] : this.doc;
    }

    step(e) {
        const n = this.maybeStep(e);
        if (n.failed) throw new dn(n.failed);
        return this;
    }

    maybeStep(e) {
        const n = e.apply(this.doc);
        return (n.failed || this.addStep(e, n.doc), n);
    }

    get docChanged() {
        return this.steps.length > 0;
    }

    addStep(e, n) {
        (this.docs.push(this.doc),
            this.steps.push(e),
            this.mapping.appendMap(e.getMap()),
            (this.doc = n));
    }

    replace(e, n = e, r = E.empty) {
        const o = _n(this.doc, e, n, r);
        return (o && this.step(o), this);
    }

    replaceWith(e, n, r) {
        return this.replace(e, n, new E(C.from(r), 0, 0));
    }

    delete(e, n) {
        return this.replace(e, n, E.empty);
    }

    insert(e, n) {
        return this.replaceWith(e, e, n);
    }

    replaceRange(e, n, r) {
        return (zp(this, e, n, r), this);
    }

    replaceRangeWith(e, n, r) {
        return (Bp(this, e, n, r), this);
    }

    deleteRange(e, n) {
        return (Hp(this, e, n), this);
    }

    lift(e, n) {
        return (Cp(this, e, n), this);
    }

    join(e, n = 1) {
        return (Dp(this, e, n), this);
    }

    wrap(e, n) {
        return (Tp(this, e, n), this);
    }

    setBlockType(e, n = e, r, o = null) {
        return (Ap(this, e, n, r, o), this);
    }

    setNodeMarkup(e, n, r = null, o) {
        return (Np(this, e, n, r, o), this);
    }

    setNodeAttribute(e, n, r) {
        return (this.step(new Rr(e, n, r)), this);
    }

    setDocAttribute(e, n) {
        return (this.step(new Dr(e, n)), this);
    }

    addNodeMark(e, n) {
        return (this.step(new Fn(e, n)), this);
    }

    removeNodeMark(e, n) {
        const r = this.doc.nodeAt(e);
        if (!r) throw new RangeError("No node at position " + e);
        if (n instanceof q) n.isInSet(r.marks) && this.step(new cn(e, n));
        else {
            let o = r.marks;
            let i;
            const s = [];
            for (; (i = n.isInSet(o)); ) {
                (s.push(new cn(e, i)), (o = i.removeFromSet(o)));
            }
            for (let l = s.length - 1; l >= 0; l--) this.step(s[l]);
        }
        return this;
    }

    split(e, n = 1, r) {
        return (Op(this, e, n, r), this);
    }

    addMark(e, n, r) {
        return (xp(this, e, n, r), this);
    }

    removeMark(e, n, r) {
        return (kp(this, e, n, r), this);
    }

    clearIncompatible(e, n, r) {
        return (Hi(this, e, n, r), this);
    }
};
const $i = Object.create(null);
const I = class {
    constructor(e, n, r) {
        ((this.$anchor = e),
            (this.$head = n),
            (this.ranges = r || [new hn(e.min(n), e.max(n))]));
    }

    get anchor() {
        return this.$anchor.pos;
    }

    get head() {
        return this.$head.pos;
    }

    get from() {
        return this.$from.pos;
    }

    get to() {
        return this.$to.pos;
    }

    get $from() {
        return this.ranges[0].$from;
    }

    get $to() {
        return this.ranges[0].$to;
    }

    get empty() {
        const e = this.ranges;
        for (let n = 0; n < e.length; n++) {
            if (e[n].$from.pos != e[n].$to.pos) return !1;
        }
        return !0;
    }

    content() {
        return this.$from.doc.slice(this.from, this.to, !0);
    }

    replace(e, n = E.empty) {
        let r = n.content.lastChild;
        let o = null;
        for (let l = 0; l < n.openEnd; l++) ((o = r), (r = r.lastChild));
        const i = e.steps.length;
        const s = this.ranges;
        for (let l = 0; l < s.length; l++) {
            const { $from: a, $to: c } = s[l];
            const d = e.mapping.slice(i);
            (e.replaceRange(d.map(a.pos), d.map(c.pos), l ? E.empty : n),
                l == 0 &&
                    Ga(e, i, (r ? r.isInline : o && o.isTextblock) ? -1 : 1));
        }
    }

    replaceWith(e, n) {
        const r = e.steps.length;
        const o = this.ranges;
        for (let i = 0; i < o.length; i++) {
            const { $from: s, $to: l } = o[i];
            const a = e.mapping.slice(r);
            const c = a.map(s.pos);
            const d = a.map(l.pos);
            i
                ? e.deleteRange(c, d)
                : (e.replaceRangeWith(c, d, n), Ga(e, r, n.isInline ? -1 : 1));
        }
    }

    static findFrom(e, n, r = !1) {
        const o = e.parent.inlineContent
            ? new R(e)
            : fn(e.node(0), e.parent, e.pos, e.index(), n, r);
        if (o) return o;
        for (let i = e.depth - 1; i >= 0; i--) {
            const s =
                n < 0
                    ? fn(
                          e.node(0),
                          e.node(i),
                          e.before(i + 1),
                          e.index(i),
                          n,
                          r,
                      )
                    : fn(
                          e.node(0),
                          e.node(i),
                          e.after(i + 1),
                          e.index(i) + 1,
                          n,
                          r,
                      );
            if (s) return s;
        }
        return null;
    }

    static near(e, n = 1) {
        return this.findFrom(e, n) || this.findFrom(e, -n) || new be(e.node(0));
    }

    static atStart(e) {
        return fn(e, e, 0, 0, 1) || new be(e);
    }

    static atEnd(e) {
        return fn(e, e, e.content.size, e.childCount, -1) || new be(e);
    }

    static fromJSON(e, n) {
        if (!n || !n.type) {
            throw new RangeError("Invalid input for Selection.fromJSON");
        }
        const r = $i[n.type];
        if (!r) throw new RangeError(`No selection type ${n.type} defined`);
        return r.fromJSON(e, n);
    }

    static jsonID(e, n) {
        if (e in $i) {
            throw new RangeError("Duplicate use of selection JSON ID " + e);
        }
        return (($i[e] = n), (n.prototype.jsonID = e), n);
    }

    getBookmark() {
        return R.between(this.$anchor, this.$head).getBookmark();
    }
};
I.prototype.visible = !0;
var hn = class {
    constructor(e, n) {
        ((this.$from = e), (this.$to = n));
    }
};
let qa = !1;
function Ja(t) {
    !qa &&
        !t.parent.inlineContent &&
        ((qa = !0),
        console.warn(
            "TextSelection endpoint not pointing into a node with inline content (" +
                t.parent.type.name +
                ")",
        ));
}
var R = class t extends I {
    constructor(e, n = e) {
        (Ja(e), Ja(n), super(e, n));
    }

    get $cursor() {
        return this.$anchor.pos == this.$head.pos ? this.$head : null;
    }

    map(e, n) {
        const r = e.resolve(n.map(this.head));
        if (!r.parent.inlineContent) return I.near(r);
        const o = e.resolve(n.map(this.anchor));
        return new t(o.parent.inlineContent ? o : r, r);
    }

    replace(e, n = E.empty) {
        if ((super.replace(e, n), n == E.empty)) {
            const r = this.$from.marksAcross(this.$to);
            r && e.ensureMarks(r);
        }
    }

    eq(e) {
        return e instanceof t && e.anchor == this.anchor && e.head == this.head;
    }

    getBookmark() {
        return new Lr(this.anchor, this.head);
    }

    toJSON() {
        return { type: "text", anchor: this.anchor, head: this.head };
    }

    static fromJSON(e, n) {
        if (typeof n.anchor !== "number" || typeof n.head !== "number") {
            throw new RangeError("Invalid input for TextSelection.fromJSON");
        }
        return new t(e.resolve(n.anchor), e.resolve(n.head));
    }

    static create(e, n, r = n) {
        const o = e.resolve(n);
        return new this(o, r == n ? o : e.resolve(r));
    }

    static between(e, n, r) {
        const o = e.pos - n.pos;
        if (((!r || o) && (r = o >= 0 ? 1 : -1), !n.parent.inlineContent)) {
            const i = I.findFrom(n, r, !0) || I.findFrom(n, -r, !0);
            if (i) n = i.$head;
            else return I.near(n, r);
        }
        return (
            e.parent.inlineContent ||
                (o == 0
                    ? (e = n)
                    : ((e = (I.findFrom(e, -r, !0) || I.findFrom(e, r, !0))
                          .$anchor),
                      e.pos < n.pos != o < 0 && (e = n))),
            new t(e, n)
        );
    }
};
I.jsonID("text", R);
var Lr = class t {
    constructor(e, n) {
        ((this.anchor = e), (this.head = n));
    }

    map(e) {
        return new t(e.map(this.anchor), e.map(this.head));
    }

    resolve(e) {
        return R.between(e.resolve(this.anchor), e.resolve(this.head));
    }
};
const P = class t extends I {
    constructor(e) {
        const n = e.nodeAfter;
        const r = e.node(0).resolve(e.pos + n.nodeSize);
        (super(e, r), (this.node = n));
    }

    map(e, n) {
        const { deleted: r, pos: o } = n.mapResult(this.anchor);
        const i = e.resolve(o);
        return r ? I.near(i) : new t(i);
    }

    content() {
        return new E(C.from(this.node), 0, 0);
    }

    eq(e) {
        return e instanceof t && e.anchor == this.anchor;
    }

    toJSON() {
        return { type: "node", anchor: this.anchor };
    }

    getBookmark() {
        return new _i(this.anchor);
    }

    static fromJSON(e, n) {
        if (typeof n.anchor !== "number") {
            throw new RangeError("Invalid input for NodeSelection.fromJSON");
        }
        return new t(e.resolve(n.anchor));
    }

    static create(e, n) {
        return new t(e.resolve(n));
    }

    static isSelectable(e) {
        return !e.isText && e.type.spec.selectable !== !1;
    }
};
P.prototype.visible = !1;
I.jsonID("node", P);
var _i = class t {
    constructor(e) {
        this.anchor = e;
    }

    map(e) {
        const { deleted: n, pos: r } = e.mapResult(this.anchor);
        return n ? new Lr(r, r) : new t(r);
    }

    resolve(e) {
        const n = e.resolve(this.anchor);
        const r = n.nodeAfter;
        return r && P.isSelectable(r) ? new P(n) : I.near(n);
    }
};
var be = class t extends I {
    constructor(e) {
        super(e.resolve(0), e.resolve(e.content.size));
    }

    replace(e, n = E.empty) {
        if (n == E.empty) {
            e.delete(0, e.doc.content.size);
            const r = I.atStart(e.doc);
            r.eq(e.selection) || e.setSelection(r);
        } else super.replace(e, n);
    }

    toJSON() {
        return { type: "all" };
    }

    static fromJSON(e) {
        return new t(e);
    }

    map(e) {
        return new t(e);
    }

    eq(e) {
        return e instanceof t;
    }

    getBookmark() {
        return $p;
    }
};
I.jsonID("all", be);
var $p = {
    map() {
        return this;
    },
    resolve(t) {
        return new be(t);
    },
};
function fn(t, e, n, r, o, i = !1) {
    if (e.inlineContent) return R.create(t, n);
    for (
        let s = r - (o > 0 ? 0 : 1);
        o > 0 ? s < e.childCount : s >= 0;
        s += o
    ) {
        const l = e.child(s);
        if (l.isAtom) {
            if (!i && P.isSelectable(l)) {
                return P.create(t, n - (o < 0 ? l.nodeSize : 0));
            }
        } else {
            const a = fn(t, l, n + o, o < 0 ? l.childCount : 0, o, i);
            if (a) return a;
        }
        n += l.nodeSize * o;
    }
    return null;
}
function Ga(t, e, n) {
    const r = t.steps.length - 1;
    if (r < e) return;
    const o = t.steps[r];
    if (!(o instanceof pe || o instanceof re)) return;
    const i = t.mapping.maps[r];
    let s;
    (i.forEach((l, a, c, d) => {
        s == null && (s = d);
    }),
        t.setSelection(I.near(t.doc.resolve(s), n)));
}
const Xa = 1;
const Pr = 2;
const Ya = 4;
const Vi = class extends St {
    constructor(e) {
        (super(e.doc),
            (this.curSelectionFor = 0),
            (this.updated = 0),
            (this.meta = Object.create(null)),
            (this.time = Date.now()),
            (this.curSelection = e.selection),
            (this.storedMarks = e.storedMarks));
    }

    get selection() {
        return (
            this.curSelectionFor < this.steps.length &&
                ((this.curSelection = this.curSelection.map(
                    this.doc,
                    this.mapping.slice(this.curSelectionFor),
                )),
                (this.curSelectionFor = this.steps.length)),
            this.curSelection
        );
    }

    setSelection(e) {
        if (e.$from.doc != this.doc) {
            throw new RangeError(
                "Selection passed to setSelection must point at the current document",
            );
        }
        return (
            (this.curSelection = e),
            (this.curSelectionFor = this.steps.length),
            (this.updated = (this.updated | Xa) & ~Pr),
            (this.storedMarks = null),
            this
        );
    }

    get selectionSet() {
        return (this.updated & Xa) > 0;
    }

    setStoredMarks(e) {
        return ((this.storedMarks = e), (this.updated |= Pr), this);
    }

    ensureMarks(e) {
        return (
            q.sameSet(this.storedMarks || this.selection.$from.marks(), e) ||
                this.setStoredMarks(e),
            this
        );
    }

    addStoredMark(e) {
        return this.ensureMarks(
            e.addToSet(this.storedMarks || this.selection.$head.marks()),
        );
    }

    removeStoredMark(e) {
        return this.ensureMarks(
            e.removeFromSet(this.storedMarks || this.selection.$head.marks()),
        );
    }

    get storedMarksSet() {
        return (this.updated & Pr) > 0;
    }

    addStep(e, n) {
        (super.addStep(e, n),
            (this.updated = this.updated & ~Pr),
            (this.storedMarks = null));
    }

    setTime(e) {
        return ((this.time = e), this);
    }

    replaceSelection(e) {
        return (this.selection.replace(this, e), this);
    }

    replaceSelectionWith(e, n = !0) {
        const r = this.selection;
        return (
            n &&
                (e = e.mark(
                    this.storedMarks ||
                        (r.empty
                            ? r.$from.marks()
                            : r.$from.marksAcross(r.$to) || q.none),
                )),
            r.replaceWith(this, e),
            this
        );
    }

    deleteSelection() {
        return (this.selection.replace(this), this);
    }

    insertText(e, n, r) {
        const o = this.doc.type.schema;
        if (n == null) {
            return e
                ? this.replaceSelectionWith(o.text(e), !0)
                : this.deleteSelection();
        }
        {
            if ((r == null && (r = n), !e)) return this.deleteRange(n, r);
            let i = this.storedMarks;
            if (!i) {
                const s = this.doc.resolve(n);
                i = r == n ? s.marks() : s.marksAcross(this.doc.resolve(r));
            }
            return (
                this.replaceRangeWith(n, r, o.text(e, i)),
                !this.selection.empty &&
                    this.selection.to == n + e.length &&
                    this.setSelection(I.near(this.selection.$to)),
                this
            );
        }
    }

    setMeta(e, n) {
        return ((this.meta[typeof e === "string" ? e : e.key] = n), this);
    }

    getMeta(e) {
        return this.meta[typeof e === "string" ? e : e.key];
    }

    get isGeneric() {
        for (const e in this.meta) return !1;
        return !0;
    }

    scrollIntoView() {
        return ((this.updated |= Ya), this);
    }

    get scrolledIntoView() {
        return (this.updated & Ya) > 0;
    }
};
function Qa(t, e) {
    return !e || !t ? t : t.bind(e);
}
const _t = class {
    constructor(e, n, r) {
        ((this.name = e),
            (this.init = Qa(n.init, r)),
            (this.apply = Qa(n.apply, r)));
    }
};
const Fp = [
    new _t("doc", {
        init(t) {
            return t.doc || t.schema.topNodeType.createAndFill();
        },
        apply(t) {
            return t.doc;
        },
    }),
    new _t("selection", {
        init(t, e) {
            return t.selection || I.atStart(e.doc);
        },
        apply(t) {
            return t.selection;
        },
    }),
    new _t("storedMarks", {
        init(t) {
            return t.storedMarks || null;
        },
        apply(t, e, n, r) {
            return r.selection.$cursor ? t.storedMarks : null;
        },
    }),
    new _t("scrollToSelection", {
        init() {
            return 0;
        },
        apply(t, e) {
            return t.scrolledIntoView ? e + 1 : e;
        },
    }),
];
const Vn = class {
    constructor(e, n) {
        ((this.schema = e),
            (this.plugins = []),
            (this.pluginsByKey = Object.create(null)),
            (this.fields = Fp.slice()),
            n &&
                n.forEach((r) => {
                    if (this.pluginsByKey[r.key]) {
                        throw new RangeError(
                            "Adding different instances of a keyed plugin (" +
                                r.key +
                                ")",
                        );
                    }
                    (this.plugins.push(r),
                        (this.pluginsByKey[r.key] = r),
                        r.spec.state &&
                            this.fields.push(new _t(r.key, r.spec.state, r)));
                }));
    }
};
const zr = class t {
    constructor(e) {
        this.config = e;
    }

    get schema() {
        return this.config.schema;
    }

    get plugins() {
        return this.config.plugins;
    }

    apply(e) {
        return this.applyTransaction(e).state;
    }

    filterTransaction(e, n = -1) {
        for (let r = 0; r < this.config.plugins.length; r++) {
            if (r != n) {
                const o = this.config.plugins[r];
                if (
                    o.spec.filterTransaction &&
                    !o.spec.filterTransaction.call(o, e, this)
                ) {
                    return !1;
                }
            }
        }
        return !0;
    }

    applyTransaction(e) {
        if (!this.filterTransaction(e)) {
            return { state: this, transactions: [] };
        }
        const n = [e];
        let r = this.applyInner(e);
        let o = null;
        for (;;) {
            let i = !1;
            for (let s = 0; s < this.config.plugins.length; s++) {
                const l = this.config.plugins[s];
                if (l.spec.appendTransaction) {
                    const a = o ? o[s].n : 0;
                    const c = o ? o[s].state : this;
                    const d =
                        a < n.length &&
                        l.spec.appendTransaction.call(
                            l,
                            a ? n.slice(a) : n,
                            c,
                            r,
                        );
                    if (d && r.filterTransaction(d, s)) {
                        if ((d.setMeta("appendedTransaction", e), !o)) {
                            o = [];
                            for (
                                let u = 0;
                                u < this.config.plugins.length;
                                u++
                            ) {
                                o.push(
                                    u < s
                                        ? { state: r, n: n.length }
                                        : { state: this, n: 0 },
                                );
                            }
                        }
                        (n.push(d), (r = r.applyInner(d)), (i = !0));
                    }
                    o && (o[s] = { state: r, n: n.length });
                }
            }
            if (!i) return { state: r, transactions: n };
        }
    }

    applyInner(e) {
        if (!e.before.eq(this.doc)) {
            throw new RangeError("Applying a mismatched transaction");
        }
        const n = new t(this.config);
        const r = this.config.fields;
        for (let o = 0; o < r.length; o++) {
            const i = r[o];
            n[i.name] = i.apply(e, this[i.name], this, n);
        }
        return n;
    }

    get tr() {
        return new Vi(this);
    }

    static create(e) {
        const n = new Vn(e.doc ? e.doc.type.schema : e.schema, e.plugins);
        const r = new t(n);
        for (let o = 0; o < n.fields.length; o++) {
            r[n.fields[o].name] = n.fields[o].init(e, r);
        }
        return r;
    }

    reconfigure(e) {
        const n = new Vn(this.schema, e.plugins);
        const r = n.fields;
        const o = new t(n);
        for (let i = 0; i < r.length; i++) {
            const s = r[i].name;
            o[s] = this.hasOwnProperty(s) ? this[s] : r[i].init(e, o);
        }
        return o;
    }

    toJSON(e) {
        const n = {
            doc: this.doc.toJSON(),
            selection: this.selection.toJSON(),
        };
        if (
            (this.storedMarks &&
                (n.storedMarks = this.storedMarks.map((r) => r.toJSON())),
            e && typeof e === "object")
        ) {
            for (const r in e) {
                if (r == "doc" || r == "selection") {
                    throw new RangeError(
                        "The JSON fields `doc` and `selection` are reserved",
                    );
                }
                const o = e[r];
                const i = o.spec.state;
                i && i.toJSON && (n[r] = i.toJSON.call(o, this[o.key]));
            }
        }
        return n;
    }

    static fromJSON(e, n, r) {
        if (!n) throw new RangeError("Invalid input for EditorState.fromJSON");
        if (!e.schema) {
            throw new RangeError("Required config field 'schema' missing");
        }
        const o = new Vn(e.schema, e.plugins);
        const i = new t(o);
        return (
            o.fields.forEach((s) => {
                if (s.name == "doc") i.doc = le.fromJSON(e.schema, n.doc);
                else if (s.name == "selection") {
                    i.selection = I.fromJSON(i.doc, n.selection);
                } else if (s.name == "storedMarks") {
                    n.storedMarks &&
                        (i.storedMarks = n.storedMarks.map(
                            e.schema.markFromJSON,
                        ));
                } else {
                    if (r) {
                        for (const l in r) {
                            const a = r[l];
                            const c = a.spec.state;
                            if (
                                a.key == s.name &&
                                c &&
                                c.fromJSON &&
                                Object.prototype.hasOwnProperty.call(n, l)
                            ) {
                                i[s.name] = c.fromJSON.call(a, e, n[l], i);
                                return;
                            }
                        }
                    }
                    i[s.name] = s.init(e, i);
                }
            }),
            i
        );
    }
};
function Za(t, e, n) {
    for (const r in t) {
        let o = t[r];
        (o instanceof Function
            ? (o = o.bind(e))
            : r == "handleDOMEvents" && (o = Za(o, e, {})),
            (n[r] = o));
    }
    return n;
}
const L = class {
    constructor(e) {
        ((this.spec = e),
            (this.props = {}),
            e.props && Za(e.props, this, this.props),
            (this.key = e.key ? e.key.key : ec("plugin")));
    }

    getState(e) {
        return e[this.key];
    }
};
const Fi = Object.create(null);
function ec(t) {
    return t in Fi ? t + "$" + ++Fi[t] : ((Fi[t] = 0), t + "$");
}
const H = class {
    constructor(e = "key") {
        this.key = ec(e);
    }

    get(e) {
        return e.config.pluginsByKey[this.key];
    }

    getState(e) {
        return e[this.key];
    }
};
const Br = (t, e) =>
    t.selection.empty
        ? !1
        : (e && e(t.tr.deleteSelection().scrollIntoView()), !0);
function nc(t, e) {
    const { $cursor: n } = t.selection;
    return !n || (e ? !e.endOfTextblock("backward", t) : n.parentOffset > 0)
        ? null
        : n;
}
const ji = (t, e, n) => {
    const r = nc(t, n);
    if (!r) return !1;
    const o = Ui(r);
    if (!o) {
        const s = r.blockRange();
        const l = s && at(s);
        return l == null ? !1 : (e && e(t.tr.lift(s, l).scrollIntoView()), !0);
    }
    const i = o.nodeBefore;
    if (uc(t, o, e, -1)) return !0;
    if (r.parent.content.size == 0 && (pn(i, "end") || P.isSelectable(i))) {
        for (let s = r.depth; ; s--) {
            const l = _n(t.doc, r.before(s), r.after(s), E.empty);
            if (l && l.slice.size < l.to - l.from) {
                if (e) {
                    const a = t.tr.step(l);
                    (a.setSelection(
                        pn(i, "end")
                            ? I.findFrom(
                                  a.doc.resolve(a.mapping.map(o.pos, -1)),
                                  -1,
                              )
                            : P.create(a.doc, o.pos - i.nodeSize),
                    ),
                        e(a.scrollIntoView()));
                }
                return !0;
            }
            if (s == 1 || r.node(s - 1).childCount > 1) break;
        }
    }
    return i.isAtom && o.depth == r.depth - 1
        ? (e && e(t.tr.delete(o.pos - i.nodeSize, o.pos).scrollIntoView()), !0)
        : !1;
};
const rc = (t, e, n) => {
    const r = nc(t, n);
    if (!r) return !1;
    const o = Ui(r);
    return o ? ic(t, o, e) : !1;
};
const oc = (t, e, n) => {
    const r = sc(t, n);
    if (!r) return !1;
    const o = Gi(r);
    return o ? ic(t, o, e) : !1;
};
function ic(t, e, n) {
    const r = e.nodeBefore;
    let o = r;
    let i = e.pos - 1;
    for (; !o.isTextblock; i--) {
        if (o.type.spec.isolating) return !1;
        const d = o.lastChild;
        if (!d) return !1;
        o = d;
    }
    const s = e.nodeAfter;
    let l = s;
    let a = e.pos + 1;
    for (; !l.isTextblock; a++) {
        if (l.type.spec.isolating) return !1;
        const d = l.firstChild;
        if (!d) return !1;
        l = d;
    }
    const c = _n(t.doc, i, a, E.empty);
    if (!c || c.from != i || (c instanceof pe && c.slice.size >= a - i)) {
        return !1;
    }
    if (n) {
        const d = t.tr.step(c);
        (d.setSelection(R.create(d.doc, i)), n(d.scrollIntoView()));
    }
    return !0;
}
function pn(t, e, n = !1) {
    for (let r = t; r; r = e == "start" ? r.firstChild : r.lastChild) {
        if (r.isTextblock) return !0;
        if (n && r.childCount != 1) return !1;
    }
    return !1;
}
const Ki = (t, e, n) => {
    const { $head: r, empty: o } = t.selection;
    let i = r;
    if (!o) return !1;
    if (r.parent.isTextblock) {
        if (n ? !n.endOfTextblock("backward", t) : r.parentOffset > 0) {
            return !1;
        }
        i = Ui(r);
    }
    const s = i && i.nodeBefore;
    return !s || !P.isSelectable(s)
        ? !1
        : (e &&
              e(
                  t.tr
                      .setSelection(P.create(t.doc, i.pos - s.nodeSize))
                      .scrollIntoView(),
              ),
          !0);
};
function Ui(t) {
    if (!t.parent.type.spec.isolating) {
        for (let e = t.depth - 1; e >= 0; e--) {
            if (t.index(e) > 0) return t.doc.resolve(t.before(e + 1));
            if (t.node(e).type.spec.isolating) break;
        }
    }
    return null;
}
function sc(t, e) {
    const { $cursor: n } = t.selection;
    return !n ||
        (e
            ? !e.endOfTextblock("forward", t)
            : n.parentOffset < n.parent.content.size)
        ? null
        : n;
}
const qi = (t, e, n) => {
    const r = sc(t, n);
    if (!r) return !1;
    const o = Gi(r);
    if (!o) return !1;
    const i = o.nodeAfter;
    if (uc(t, o, e, 1)) return !0;
    if (r.parent.content.size == 0 && (pn(i, "start") || P.isSelectable(i))) {
        const s = _n(t.doc, r.before(), r.after(), E.empty);
        if (s && s.slice.size < s.to - s.from) {
            if (e) {
                const l = t.tr.step(s);
                (l.setSelection(
                    pn(i, "start")
                        ? I.findFrom(l.doc.resolve(l.mapping.map(o.pos)), 1)
                        : P.create(l.doc, l.mapping.map(o.pos)),
                ),
                    e(l.scrollIntoView()));
            }
            return !0;
        }
    }
    return i.isAtom && o.depth == r.depth - 1
        ? (e && e(t.tr.delete(o.pos, o.pos + i.nodeSize).scrollIntoView()), !0)
        : !1;
};
const Ji = (t, e, n) => {
    const { $head: r, empty: o } = t.selection;
    let i = r;
    if (!o) return !1;
    if (r.parent.isTextblock) {
        if (
            n
                ? !n.endOfTextblock("forward", t)
                : r.parentOffset < r.parent.content.size
        ) {
            return !1;
        }
        i = Gi(r);
    }
    const s = i && i.nodeAfter;
    return !s || !P.isSelectable(s)
        ? !1
        : (e && e(t.tr.setSelection(P.create(t.doc, i.pos)).scrollIntoView()),
          !0);
};
function Gi(t) {
    if (!t.parent.type.spec.isolating) {
        for (let e = t.depth - 1; e >= 0; e--) {
            const n = t.node(e);
            if (t.index(e) + 1 < n.childCount) {
                return t.doc.resolve(t.after(e + 1));
            }
            if (n.type.spec.isolating) break;
        }
    }
    return null;
}
const lc = (t, e) => {
    const n = t.selection;
    const r = n instanceof P;
    let o;
    if (r) {
        if (n.node.isTextblock || !Oe(t.doc, n.from)) return !1;
        o = n.from;
    } else if (((o = Ft(t.doc, n.from, -1)), o == null)) return !1;
    if (e) {
        const i = t.tr.join(o);
        (r &&
            i.setSelection(
                P.create(i.doc, o - t.doc.resolve(o).nodeBefore.nodeSize),
            ),
            e(i.scrollIntoView()));
    }
    return !0;
};
const ac = (t, e) => {
    const n = t.selection;
    let r;
    if (n instanceof P) {
        if (n.node.isTextblock || !Oe(t.doc, n.to)) return !1;
        r = n.to;
    } else if (((r = Ft(t.doc, n.to, 1)), r == null)) return !1;
    return (e && e(t.tr.join(r).scrollIntoView()), !0);
};
const cc = (t, e) => {
    const { $from: n, $to: r } = t.selection;
    const o = n.blockRange(r);
    const i = o && at(o);
    return i == null ? !1 : (e && e(t.tr.lift(o, i).scrollIntoView()), !0);
};
const Xi = (t, e) => {
    const { $head: n, $anchor: r } = t.selection;
    return !n.parent.type.spec.code || !n.sameParent(r)
        ? !1
        : (e &&
              e(
                  t.tr
                      .insertText(
                          `
`,
                      )
                      .scrollIntoView(),
              ),
          !0);
};
function Yi(t) {
    for (let e = 0; e < t.edgeCount; e++) {
        const { type: n } = t.edge(e);
        if (n.isTextblock && !n.hasRequiredAttrs()) return n;
    }
    return null;
}
const Qi = (t, e) => {
    const { $head: n, $anchor: r } = t.selection;
    if (!n.parent.type.spec.code || !n.sameParent(r)) return !1;
    const o = n.node(-1);
    const i = n.indexAfter(-1);
    const s = Yi(o.contentMatchAt(i));
    if (!s || !o.canReplaceWith(i, i, s)) return !1;
    if (e) {
        const l = n.after();
        const a = t.tr.replaceWith(l, l, s.createAndFill());
        (a.setSelection(I.near(a.doc.resolve(l), 1)), e(a.scrollIntoView()));
    }
    return !0;
};
const Zi = (t, e) => {
    const n = t.selection;
    const { $from: r, $to: o } = n;
    if (n instanceof be || r.parent.inlineContent || o.parent.inlineContent) {
        return !1;
    }
    const i = Yi(o.parent.contentMatchAt(o.indexAfter()));
    if (!i || !i.isTextblock) return !1;
    if (e) {
        const s = (!r.parentOffset && o.index() < o.parent.childCount ? r : o)
            .pos;
        const l = t.tr.insert(s, i.createAndFill());
        (l.setSelection(R.create(l.doc, s + 1)), e(l.scrollIntoView()));
    }
    return !0;
};
const es = (t, e) => {
    const { $cursor: n } = t.selection;
    if (!n || n.parent.content.size) return !1;
    if (n.depth > 1 && n.after() != n.end(-1)) {
        const i = n.before();
        if (Ae(t.doc, i)) return (e && e(t.tr.split(i).scrollIntoView()), !0);
    }
    const r = n.blockRange();
    const o = r && at(r);
    return o == null ? !1 : (e && e(t.tr.lift(r, o).scrollIntoView()), !0);
};
function _p(t) {
    return (e, n) => {
        const { $from: r, $to: o } = e.selection;
        if (e.selection instanceof P && e.selection.node.isBlock) {
            return !r.parentOffset || !Ae(e.doc, r.pos)
                ? !1
                : (n && n(e.tr.split(r.pos).scrollIntoView()), !0);
        }
        if (!r.depth) return !1;
        const i = [];
        let s;
        let l;
        let a = !1;
        let c = !1;
        for (let h = r.depth; ; h--) {
            if (r.node(h).isBlock) {
                ((a = r.end(h) == r.pos + (r.depth - h)),
                    (c = r.start(h) == r.pos - (r.depth - h)),
                    (l = Yi(
                        r.node(h - 1).contentMatchAt(r.indexAfter(h - 1)),
                    )));
                const m = t && t(o.parent, a, r);
                (i.unshift(m || (a && l ? { type: l } : null)), (s = h));
                break;
            } else {
                if (h == 1) return !1;
                i.unshift(null);
            }
        }
        const d = e.tr;
        (e.selection instanceof R || e.selection instanceof be) &&
            d.deleteSelection();
        const u = d.mapping.map(r.pos);
        let f = Ae(d.doc, u, i.length, i);
        if (
            (f ||
                ((i[0] = l ? { type: l } : null),
                (f = Ae(d.doc, u, i.length, i))),
            !f)
        ) {
            return !1;
        }
        if ((d.split(u, i.length, i), !a && c && r.node(s).type != l)) {
            const h = d.mapping.map(r.before(s));
            const p = d.doc.resolve(h);
            l &&
                r.node(s - 1).canReplaceWith(p.index(), p.index() + 1, l) &&
                d.setNodeMarkup(d.mapping.map(r.before(s)), l);
        }
        return (n && n(d.scrollIntoView()), !0);
    };
}
const Vp = _p();
const dc = (t, e) => {
    const { $from: n, to: r } = t.selection;
    let o;
    const i = n.sharedDepth(r);
    return i == 0
        ? !1
        : ((o = n.before(i)),
          e && e(t.tr.setSelection(P.create(t.doc, o))),
          !0);
};
const Wp = (t, e) => (e && e(t.tr.setSelection(new be(t.doc))), !0);
function jp(t, e, n) {
    const r = e.nodeBefore;
    const o = e.nodeAfter;
    const i = e.index();
    return !r || !o || !r.type.compatibleContent(o.type)
        ? !1
        : !r.content.size && e.parent.canReplace(i - 1, i)
          ? (n && n(t.tr.delete(e.pos - r.nodeSize, e.pos).scrollIntoView()),
            !0)
          : !e.parent.canReplace(i, i + 1) ||
              !(o.isTextblock || Oe(t.doc, e.pos))
            ? !1
            : (n && n(t.tr.join(e.pos).scrollIntoView()), !0);
}
function uc(t, e, n, r) {
    const o = e.nodeBefore;
    const i = e.nodeAfter;
    let s;
    let l;
    const a = o.type.spec.isolating || i.type.spec.isolating;
    if (!a && jp(t, e, n)) return !0;
    const c = !a && e.parent.canReplace(e.index(), e.index() + 1);
    if (
        c &&
        (s = (l = o.contentMatchAt(o.childCount)).findWrapping(i.type)) &&
        l.matchType(s[0] || i.type).validEnd
    ) {
        if (n) {
            const h = e.pos + i.nodeSize;
            let p = C.empty;
            for (let y = s.length - 1; y >= 0; y--) {
                p = C.from(s[y].create(null, p));
            }
            p = C.from(o.copy(p));
            const m = t.tr.step(
                new re(e.pos - 1, h, e.pos, h, new E(p, 1, 0), s.length, !0),
            );
            const g = m.doc.resolve(h + 2 * s.length);
            (g.nodeAfter &&
                g.nodeAfter.type == o.type &&
                Oe(m.doc, g.pos) &&
                m.join(g.pos),
                n(m.scrollIntoView()));
        }
        return !0;
    }
    const d = i.type.spec.isolating || (r > 0 && a) ? null : I.findFrom(e, 1);
    const u = d && d.$from.blockRange(d.$to);
    const f = u && at(u);
    if (f != null && f >= e.depth) {
        return (n && n(t.tr.lift(u, f).scrollIntoView()), !0);
    }
    if (c && pn(i, "start", !0) && pn(o, "end")) {
        let h = o;
        const p = [];
        for (; p.push(h), !h.isTextblock; ) h = h.lastChild;
        let m = i;
        let g = 1;
        for (; !m.isTextblock; m = m.firstChild) g++;
        if (h.canReplace(h.childCount, h.childCount, m.content)) {
            if (n) {
                let y = C.empty;
                for (let k = p.length - 1; k >= 0; k--) {
                    y = C.from(p[k].copy(y));
                }
                const b = t.tr.step(
                    new re(
                        e.pos - p.length,
                        e.pos + i.nodeSize,
                        e.pos + g,
                        e.pos + i.nodeSize - g,
                        new E(y, p.length, 0),
                        0,
                        !0,
                    ),
                );
                n(b.scrollIntoView());
            }
            return !0;
        }
    }
    return !1;
}
function fc(t) {
    return function (e, n) {
        const r = e.selection;
        const o = t < 0 ? r.$from : r.$to;
        let i = o.depth;
        for (; o.node(i).isInline; ) {
            if (!i) return !1;
            i--;
        }
        return o.node(i).isTextblock
            ? (n &&
                  n(
                      e.tr.setSelection(
                          R.create(e.doc, t < 0 ? o.start(i) : o.end(i)),
                      ),
                  ),
              !0)
            : !1;
    };
}
const ts = fc(-1);
const ns = fc(1);
function hc(t, e = null) {
    return function (n, r) {
        const { $from: o, $to: i } = n.selection;
        const s = o.blockRange(i);
        const l = s && un(s, t, e);
        return l ? (r && r(n.tr.wrap(s, l).scrollIntoView()), !0) : !1;
    };
}
function rs(t, e = null) {
    return function (n, r) {
        let o = !1;
        for (let i = 0; i < n.selection.ranges.length && !o; i++) {
            const {
                $from: { pos: s },
                $to: { pos: l },
            } = n.selection.ranges[i];
            n.doc.nodesBetween(s, l, (a, c) => {
                if (o) return !1;
                if (!(!a.isTextblock || a.hasMarkup(t, e))) {
                    if (a.type == t) o = !0;
                    else {
                        const d = n.doc.resolve(c);
                        const u = d.index();
                        o = d.parent.canReplaceWith(u, u + 1, t);
                    }
                }
            });
        }
        if (!o) return !1;
        if (r) {
            const i = n.tr;
            for (let s = 0; s < n.selection.ranges.length; s++) {
                const {
                    $from: { pos: l },
                    $to: { pos: a },
                } = n.selection.ranges[s];
                i.setBlockType(l, a, t, e);
            }
            r(i.scrollIntoView());
        }
        return !0;
    };
}
function is(...t) {
    return function (e, n, r) {
        for (let o = 0; o < t.length; o++) if (t[o](e, n, r)) return !0;
        return !1;
    };
}
const Wi = is(Br, ji, Ki);
const tc = is(Br, qi, Ji);
const Ct = {
    Enter: is(Xi, Zi, es, Vp),
    "Mod-Enter": Qi,
    Backspace: Wi,
    "Mod-Backspace": Wi,
    "Shift-Backspace": Wi,
    Delete: tc,
    "Mod-Delete": tc,
    "Mod-a": Wp,
};
const Kp = {
    "Ctrl-h": Ct.Backspace,
    "Alt-Backspace": Ct["Mod-Backspace"],
    "Ctrl-d": Ct.Delete,
    "Ctrl-Alt-Backspace": Ct["Mod-Delete"],
    "Alt-Delete": Ct["Mod-Delete"],
    "Alt-d": Ct["Mod-Delete"],
    "Ctrl-a": ts,
    "Ctrl-e": ns,
};
for (const t in Ct) Kp[t] = Ct[t];
const Gx =
    typeof navigator < "u"
        ? /Mac|iP(hone|[oa]d)/.test(navigator.platform)
        : typeof os < "u" && os.platform
          ? os.platform() == "darwin"
          : !1;
function pc(t, e = null) {
    return function (n, r) {
        const { $from: o, $to: i } = n.selection;
        const s = o.blockRange(i);
        if (!s) return !1;
        const l = r ? n.tr : null;
        return Up(l, s, t, e) ? (r && r(l.scrollIntoView()), !0) : !1;
    };
}
function Up(t, e, n, r = null) {
    let o = !1;
    let i = e;
    const s = e.$from.doc;
    if (
        e.depth >= 2 &&
        e.$from.node(e.depth - 1).type.compatibleContent(n) &&
        e.startIndex == 0
    ) {
        if (e.$from.index(e.depth - 1) == 0) return !1;
        const a = s.resolve(e.start - 2);
        ((i = new Ht(a, a, e.depth)),
            e.endIndex < e.parent.childCount &&
                (e = new Ht(e.$from, s.resolve(e.$to.end(e.depth)), e.depth)),
            (o = !0));
    }
    const l = un(i, n, r, e);
    return l ? (t && qp(t, e, l, o, n), !0) : !1;
}
function qp(t, e, n, r, o) {
    let i = C.empty;
    for (let d = n.length - 1; d >= 0; d--) {
        i = C.from(n[d].type.create(n[d].attrs, i));
    }
    t.step(
        new re(
            e.start - (r ? 2 : 0),
            e.end,
            e.start,
            e.end,
            new E(i, 0, 0),
            n.length,
            !0,
        ),
    );
    let s = 0;
    for (let d = 0; d < n.length; d++) n[d].type == o && (s = d + 1);
    const l = n.length - s;
    let a = e.start + n.length - (r ? 2 : 0);
    const c = e.parent;
    for (let d = e.startIndex, u = e.endIndex, f = !0; d < u; d++, f = !1) {
        (!f && Ae(t.doc, a, l) && (t.split(a, l), (a += 2 * l)),
            (a += c.child(d).nodeSize));
    }
    return t;
}
function mc(t) {
    return function (e, n) {
        const { $from: r, $to: o } = e.selection;
        const i = r.blockRange(
            o,
            (s) => s.childCount > 0 && s.firstChild.type == t,
        );
        return i
            ? n
                ? r.node(i.depth - 1).type == t
                    ? Jp(e, n, t, i)
                    : Gp(e, n, i)
                : !0
            : !1;
    };
}
function Jp(t, e, n, r) {
    const o = t.tr;
    const i = r.end;
    const s = r.$to.end(r.depth);
    i < s &&
        (o.step(
            new re(
                i - 1,
                s,
                i,
                s,
                new E(C.from(n.create(null, r.parent.copy())), 1, 0),
                1,
                !0,
            ),
        ),
        (r = new Ht(o.doc.resolve(r.$from.pos), o.doc.resolve(s), r.depth)));
    const l = at(r);
    if (l == null) return !1;
    o.lift(r, l);
    const a = o.doc.resolve(o.mapping.map(i, -1) - 1);
    return (
        Oe(o.doc, a.pos) &&
            a.nodeBefore.type == a.nodeAfter.type &&
            o.join(a.pos),
        e(o.scrollIntoView()),
        !0
    );
}
function Gp(t, e, n) {
    const r = t.tr;
    const o = n.parent;
    for (let h = n.end, p = n.endIndex - 1, m = n.startIndex; p > m; p--) {
        ((h -= o.child(p).nodeSize), r.delete(h - 1, h + 1));
    }
    const i = r.doc.resolve(n.start);
    const s = i.nodeAfter;
    if (r.mapping.map(n.end) != n.start + i.nodeAfter.nodeSize) return !1;
    const l = n.startIndex == 0;
    const a = n.endIndex == o.childCount;
    const c = i.node(-1);
    const d = i.index(-1);
    if (
        !c.canReplace(
            d + (l ? 0 : 1),
            d + 1,
            s.content.append(a ? C.empty : C.from(o)),
        )
    ) {
        return !1;
    }
    const u = i.pos;
    const f = u + s.nodeSize;
    return (
        r.step(
            new re(
                u - (l ? 1 : 0),
                f + (a ? 1 : 0),
                u + 1,
                f - 1,
                new E(
                    (l ? C.empty : C.from(o.copy(C.empty))).append(
                        a ? C.empty : C.from(o.copy(C.empty)),
                    ),
                    l ? 0 : 1,
                    a ? 0 : 1,
                ),
                l ? 0 : 1,
            ),
        ),
        e(r.scrollIntoView()),
        !0
    );
}
function gc(t) {
    return function (e, n) {
        const { $from: r, $to: o } = e.selection;
        const i = r.blockRange(
            o,
            (c) => c.childCount > 0 && c.firstChild.type == t,
        );
        if (!i) return !1;
        const s = i.startIndex;
        if (s == 0) return !1;
        const l = i.parent;
        const a = l.child(s - 1);
        if (a.type != t) return !1;
        if (n) {
            const c = a.lastChild && a.lastChild.type == l.type;
            const d = C.from(c ? t.create() : null);
            const u = new E(
                C.from(t.create(null, C.from(l.type.create(null, d)))),
                c ? 3 : 1,
                0,
            );
            const f = i.start;
            const h = i.end;
            n(
                e.tr
                    .step(new re(f - (c ? 3 : 1), h, f, h, u, 1, !0))
                    .scrollIntoView(),
            );
        }
        return !0;
    };
}
const de = function (t) {
    for (let e = 0; ; e++) if (((t = t.previousSibling), !t)) return e;
};
const wn = function (t) {
    const e = t.assignedSlot || t.parentNode;
    return e && e.nodeType == 11 ? e.host : e;
};
let us = null;
const dt = function (t, e, n) {
    const r = us || (us = document.createRange());
    return (r.setEnd(t, n ?? t.nodeValue.length), r.setStart(t, e || 0), r);
};
const Xp = function () {
    us = null;
};
const Jt = function (t, e, n, r) {
    return n && (yc(t, e, n, r, -1) || yc(t, e, n, r, 1));
};
const Yp = /^(img|br|input|textarea|hr)$/i;
function yc(t, e, n, r, o) {
    for (var i; ; ) {
        if (t == n && e == r) return !0;
        if (e == (o < 0 ? 0 : De(t))) {
            const s = t.parentNode;
            if (
                !s ||
                s.nodeType != 1 ||
                Xn(t) ||
                Yp.test(t.nodeName) ||
                t.contentEditable == "false"
            ) {
                return !1;
            }
            ((e = de(t) + (o < 0 ? 0 : 1)), (t = s));
        } else if (t.nodeType == 1) {
            const s = t.childNodes[e + (o < 0 ? -1 : 0)];
            if (s.nodeType == 1 && s.contentEditable == "false") {
                if (
                    !((i = s.pmViewDesc) === null || i === void 0) &&
                    i.ignoreForSelection
                ) {
                    e += o;
                } else return !1;
            } else ((t = s), (e = o < 0 ? De(t) : 0));
        } else return !1;
    }
}
function De(t) {
    return t.nodeType == 3 ? t.nodeValue.length : t.childNodes.length;
}
function Qp(t, e) {
    for (;;) {
        if (t.nodeType == 3 && e) return t;
        if (t.nodeType == 1 && e > 0) {
            if (t.contentEditable == "false") return null;
            ((t = t.childNodes[e - 1]), (e = De(t)));
        } else if (t.parentNode && !Xn(t)) ((e = de(t)), (t = t.parentNode));
        else return null;
    }
}
function Zp(t, e) {
    for (;;) {
        if (t.nodeType == 3 && e < t.nodeValue.length) return t;
        if (t.nodeType == 1 && e < t.childNodes.length) {
            if (t.contentEditable == "false") return null;
            ((t = t.childNodes[e]), (e = 0));
        } else if (t.parentNode && !Xn(t)) {
            ((e = de(t) + 1), (t = t.parentNode));
        } else return null;
    }
}
function em(t, e, n) {
    for (let r = e == 0, o = e == De(t); r || o; ) {
        if (t == n) return !0;
        const i = de(t);
        if (((t = t.parentNode), !t)) return !1;
        ((r = r && i == 0), (o = o && i == De(t)));
    }
}
function Xn(t) {
    let e;
    for (let n = t; n && !(e = n.pmViewDesc); n = n.parentNode);
    return e && e.node && e.node.isBlock && (e.dom == t || e.contentDOM == t);
}
const Jr = function (t) {
    return (
        t.focusNode &&
        Jt(t.focusNode, t.focusOffset, t.anchorNode, t.anchorOffset)
    );
};
function Vt(t, e) {
    const n = document.createEvent("Event");
    return (
        n.initEvent("keydown", !0, !0),
        (n.keyCode = t),
        (n.key = n.code = e),
        n
    );
}
function tm(t) {
    let e = t.activeElement;
    for (; e && e.shadowRoot; ) e = e.shadowRoot.activeElement;
    return e;
}
function nm(t, e, n) {
    if (t.caretPositionFromPoint) {
        try {
            const r = t.caretPositionFromPoint(e, n);
            if (r) {
                return {
                    node: r.offsetNode,
                    offset: Math.min(De(r.offsetNode), r.offset),
                };
            }
        } catch {}
    }
    if (t.caretRangeFromPoint) {
        const r = t.caretRangeFromPoint(e, n);
        if (r) {
            return {
                node: r.startContainer,
                offset: Math.min(De(r.startContainer), r.startOffset),
            };
        }
    }
}
const qe = typeof navigator < "u" ? navigator : null;
const bc = typeof document < "u" ? document : null;
const Nt = (qe && qe.userAgent) || "";
const fs = /Edge\/(\d+)/.exec(Nt);
const Xc = /MSIE \d/.exec(Nt);
const hs = /Trident\/(?:[7-9]|\d{2,})\..*rv:(\d+)/.exec(Nt);
const Se = !!(Xc || hs || fs);
const At = Xc ? document.documentMode : hs ? +hs[1] : fs ? +fs[1] : 0;
const Ie = !Se && /gecko\/(\d+)/i.test(Nt);
Ie && +(/Firefox\/(\d+)/.exec(Nt) || [0, 0])[1];
const ps = !Se && /Chrome\/(\d+)/.exec(Nt);
const ge = !!ps;
const Yc = ps ? +ps[1] : 0;
const we = !Se && !!qe && /Apple Computer/.test(qe.vendor);
const xn = we && (/Mobile\/\w+/.test(Nt) || (!!qe && qe.maxTouchPoints > 2));
const Re = xn || (qe ? /Mac/.test(qe.platform) : !1);
const rm = qe ? /Win/.test(qe.platform) : !1;
const ut = /Android \d/.test(Nt);
const Yn = !!bc && "webkitFontSmoothing" in bc.documentElement.style;
const om = Yn
    ? +(/\bAppleWebKit\/(\d+)/.exec(navigator.userAgent) || [0, 0])[1]
    : 0;
function im(t) {
    const e = t.defaultView && t.defaultView.visualViewport;
    return e
        ? { left: 0, right: e.width, top: 0, bottom: e.height }
        : {
              left: 0,
              right: t.documentElement.clientWidth,
              top: 0,
              bottom: t.documentElement.clientHeight,
          };
}
function ct(t, e) {
    return typeof t === "number" ? t : t[e];
}
function sm(t) {
    const e = t.getBoundingClientRect();
    const n = e.width / t.offsetWidth || 1;
    const r = e.height / t.offsetHeight || 1;
    return {
        left: e.left,
        right: e.left + t.clientWidth * n,
        top: e.top,
        bottom: e.top + t.clientHeight * r,
    };
}
function wc(t, e, n) {
    const r = t.someProp("scrollThreshold") || 0;
    const o = t.someProp("scrollMargin") || 5;
    const i = t.dom.ownerDocument;
    for (let s = n || t.dom; s; ) {
        if (s.nodeType != 1) {
            s = wn(s);
            continue;
        }
        const l = s;
        const a = l == i.body;
        const c = a ? im(i) : sm(l);
        let d = 0;
        let u = 0;
        if (
            (e.top < c.top + ct(r, "top")
                ? (u = -(c.top - e.top + ct(o, "top")))
                : e.bottom > c.bottom - ct(r, "bottom") &&
                  (u =
                      e.bottom - e.top > c.bottom - c.top
                          ? e.top + ct(o, "top") - c.top
                          : e.bottom - c.bottom + ct(o, "bottom")),
            e.left < c.left + ct(r, "left")
                ? (d = -(c.left - e.left + ct(o, "left")))
                : e.right > c.right - ct(r, "right") &&
                  (d = e.right - c.right + ct(o, "right")),
            d || u)
        ) {
            if (a) i.defaultView.scrollBy(d, u);
            else {
                const h = l.scrollLeft;
                const p = l.scrollTop;
                (u && (l.scrollTop += u), d && (l.scrollLeft += d));
                const m = l.scrollLeft - h;
                const g = l.scrollTop - p;
                e = {
                    left: e.left - m,
                    top: e.top - g,
                    right: e.right - m,
                    bottom: e.bottom - g,
                };
            }
        }
        const f = a ? "fixed" : getComputedStyle(s).position;
        if (/^(fixed|sticky)$/.test(f)) break;
        s = f == "absolute" ? s.offsetParent : wn(s);
    }
}
function lm(t) {
    const e = t.dom.getBoundingClientRect();
    const n = Math.max(0, e.top);
    let r;
    let o;
    for (
        let i = (e.left + e.right) / 2, s = n + 1;
        s < Math.min(innerHeight, e.bottom);
        s += 5
    ) {
        const l = t.root.elementFromPoint(i, s);
        if (!l || l == t.dom || !t.dom.contains(l)) continue;
        const a = l.getBoundingClientRect();
        if (a.top >= n - 20) {
            ((r = l), (o = a.top));
            break;
        }
    }
    return { refDOM: r, refTop: o, stack: Qc(t.dom) };
}
function Qc(t) {
    const e = [];
    const n = t.ownerDocument;
    for (
        let r = t;
        r && (e.push({ dom: r, top: r.scrollTop, left: r.scrollLeft }), t != n);
        r = wn(r)
    );
    return e;
}
function am({ refDOM: t, refTop: e, stack: n }) {
    const r = t ? t.getBoundingClientRect().top : 0;
    Zc(n, r == 0 ? 0 : r - e);
}
function Zc(t, e) {
    for (let n = 0; n < t.length; n++) {
        const { dom: r, top: o, left: i } = t[n];
        (r.scrollTop != o + e && (r.scrollTop = o + e),
            r.scrollLeft != i && (r.scrollLeft = i));
    }
}
let mn = null;
function cm(t) {
    if (t.setActive) return t.setActive();
    if (mn) return t.focus(mn);
    const e = Qc(t);
    (t.focus(
        mn == null
            ? {
                  get preventScroll() {
                      return ((mn = { preventScroll: !0 }), !0);
                  },
              }
            : void 0,
    ),
        mn || ((mn = !1), Zc(e, 0)));
}
function ed(t, e) {
    let n;
    let r = 2e8;
    let o;
    let i = 0;
    let s = e.top;
    let l = e.top;
    let a;
    let c;
    for (let d = t.firstChild, u = 0; d; d = d.nextSibling, u++) {
        let f;
        if (d.nodeType == 1) f = d.getClientRects();
        else if (d.nodeType == 3) f = dt(d).getClientRects();
        else continue;
        for (let h = 0; h < f.length; h++) {
            const p = f[h];
            if (p.top <= s && p.bottom >= l) {
                ((s = Math.max(p.bottom, s)), (l = Math.min(p.top, l)));
                const m =
                    p.left > e.left
                        ? p.left - e.left
                        : p.right < e.left
                          ? e.left - p.right
                          : 0;
                if (m < r) {
                    ((n = d),
                        (r = m),
                        (o =
                            m && n.nodeType == 3
                                ? {
                                      left: p.right < e.left ? p.right : p.left,
                                      top: e.top,
                                  }
                                : e),
                        d.nodeType == 1 &&
                            m &&
                            (i =
                                u +
                                (e.left >= (p.left + p.right) / 2 ? 1 : 0)));
                    continue;
                }
            } else {
                p.top > e.top &&
                    !a &&
                    p.left <= e.left &&
                    p.right >= e.left &&
                    ((a = d),
                    (c = {
                        left: Math.max(p.left, Math.min(p.right, e.left)),
                        top: p.top,
                    }));
            }
            !n &&
                ((e.left >= p.right && e.top >= p.top) ||
                    (e.left >= p.left && e.top >= p.bottom)) &&
                (i = u + 1);
        }
    }
    return (
        !n && a && ((n = a), (o = c), (r = 0)),
        n && n.nodeType == 3
            ? dm(n, o)
            : !n || (r && n.nodeType == 1)
              ? { node: t, offset: i }
              : ed(n, o)
    );
}
function dm(t, e) {
    const n = t.nodeValue.length;
    const r = document.createRange();
    for (let o = 0; o < n; o++) {
        (r.setEnd(t, o + 1), r.setStart(t, o));
        const i = vt(r, 1);
        if (i.top != i.bottom && Ns(e, i)) {
            return {
                node: t,
                offset: o + (e.left >= (i.left + i.right) / 2 ? 1 : 0),
            };
        }
    }
    return { node: t, offset: 0 };
}
function Ns(t, e) {
    return (
        t.left >= e.left - 1 &&
        t.left <= e.right + 1 &&
        t.top >= e.top - 1 &&
        t.top <= e.bottom + 1
    );
}
function um(t, e) {
    const n = t.parentNode;
    return n &&
        /^li$/i.test(n.nodeName) &&
        e.left < t.getBoundingClientRect().left
        ? n
        : t;
}
function fm(t, e, n) {
    const { node: r, offset: o } = ed(e, n);
    let i = -1;
    if (r.nodeType == 1 && !r.firstChild) {
        const s = r.getBoundingClientRect();
        i = s.left != s.right && n.left > (s.left + s.right) / 2 ? 1 : -1;
    }
    return t.docView.posFromDOM(r, o, i);
}
function hm(t, e, n, r) {
    let o = -1;
    for (let i = e, s = !1; i != t.dom; ) {
        const l = t.docView.nearestDesc(i, !0);
        let a;
        if (!l) return null;
        if (
            l.dom.nodeType == 1 &&
            ((l.node.isBlock && l.parent) || !l.contentDOM) &&
            ((a = l.dom.getBoundingClientRect()).width || a.height) &&
            (l.node.isBlock &&
                l.parent &&
                !/^T(R|BODY|HEAD|FOOT)$/.test(l.dom.nodeName) &&
                ((!s && a.left > r.left) || a.top > r.top
                    ? (o = l.posBefore)
                    : ((!s && a.right < r.left) || a.bottom < r.top) &&
                      (o = l.posAfter),
                (s = !0)),
            !l.contentDOM && o < 0 && !l.node.isText)
        ) {
            return (
                l.node.isBlock
                    ? r.top < (a.top + a.bottom) / 2
                    : r.left < (a.left + a.right) / 2
            )
                ? l.posBefore
                : l.posAfter;
        }
        i = l.dom.parentNode;
    }
    return o > -1 ? o : t.docView.posFromDOM(e, n, -1);
}
function td(t, e, n) {
    const r = t.childNodes.length;
    if (r && n.top < n.bottom) {
        for (
            let o = Math.max(
                    0,
                    Math.min(
                        r - 1,
                        Math.floor((r * (e.top - n.top)) / (n.bottom - n.top)) -
                            2,
                    ),
                ),
                i = o;
            ;

        ) {
            const s = t.childNodes[i];
            if (s.nodeType == 1) {
                const l = s.getClientRects();
                for (let a = 0; a < l.length; a++) {
                    const c = l[a];
                    if (Ns(e, c)) return td(s, e, c);
                }
            }
            if ((i = (i + 1) % r) == o) break;
        }
    }
    return t;
}
function pm(t, e) {
    const n = t.dom.ownerDocument;
    let r;
    let o = 0;
    const i = nm(n, e.left, e.top);
    i && ({ node: r, offset: o } = i);
    let s = (t.root.elementFromPoint ? t.root : n).elementFromPoint(
        e.left,
        e.top,
    );
    let l;
    if (!s || !t.dom.contains(s.nodeType != 1 ? s.parentNode : s)) {
        const c = t.dom.getBoundingClientRect();
        if (!Ns(e, c) || ((s = td(t.dom, e, c)), !s)) return null;
    }
    if (we) for (let c = s; r && c; c = wn(c)) c.draggable && (r = void 0);
    if (((s = um(s, e)), r)) {
        if (
            Ie &&
            r.nodeType == 1 &&
            ((o = Math.min(o, r.childNodes.length)), o < r.childNodes.length)
        ) {
            const d = r.childNodes[o];
            let u;
            d.nodeName == "IMG" &&
                (u = d.getBoundingClientRect()).right <= e.left &&
                u.bottom > e.top &&
                o++;
        }
        let c;
        (Yn &&
            o &&
            r.nodeType == 1 &&
            (c = r.childNodes[o - 1]).nodeType == 1 &&
            c.contentEditable == "false" &&
            c.getBoundingClientRect().top >= e.top &&
            o--,
            r == t.dom &&
            o == r.childNodes.length - 1 &&
            r.lastChild.nodeType == 1 &&
            e.top > r.lastChild.getBoundingClientRect().bottom
                ? (l = t.state.doc.content.size)
                : (o == 0 ||
                      r.nodeType != 1 ||
                      r.childNodes[o - 1].nodeName != "BR") &&
                  (l = hm(t, r, o, e)));
    }
    l == null && (l = fm(t, s, e));
    const a = t.docView.nearestDesc(s, !0);
    return { pos: l, inside: a ? a.posAtStart - a.border : -1 };
}
function xc(t) {
    return t.top < t.bottom || t.left < t.right;
}
function vt(t, e) {
    const n = t.getClientRects();
    if (n.length) {
        const r = n[e < 0 ? 0 : n.length - 1];
        if (xc(r)) return r;
    }
    return Array.prototype.find.call(n, xc) || t.getBoundingClientRect();
}
const mm = /[\u0590-\u05f4\u0600-\u06ff\u0700-\u08ac]/;
function nd(t, e, n) {
    const {
        node: r,
        offset: o,
        atom: i,
    } = t.docView.domFromPos(e, n < 0 ? -1 : 1);
    const s = Yn || Ie;
    if (r.nodeType == 3) {
        if (
            s &&
            (mm.test(r.nodeValue) || (n < 0 ? !o : o == r.nodeValue.length))
        ) {
            const a = vt(dt(r, o, o), n);
            if (
                Ie &&
                o &&
                /\s/.test(r.nodeValue[o - 1]) &&
                o < r.nodeValue.length
            ) {
                const c = vt(dt(r, o - 1, o - 1), -1);
                if (c.top == a.top) {
                    const d = vt(dt(r, o, o + 1), -1);
                    if (d.top != a.top) return Wn(d, d.left < c.left);
                }
            }
            return a;
        } else {
            let a = o;
            let c = o;
            let d = n < 0 ? 1 : -1;
            return (
                n < 0 && !o
                    ? (c++, (d = -1))
                    : n >= 0 && o == r.nodeValue.length
                      ? (a--, (d = 1))
                      : n < 0
                        ? a--
                        : c++,
                Wn(vt(dt(r, a, c), d), d < 0)
            );
        }
    }
    if (!t.state.doc.resolve(e - (i || 0)).parent.inlineContent) {
        if (i == null && o && (n < 0 || o == De(r))) {
            const a = r.childNodes[o - 1];
            if (a.nodeType == 1) return ss(a.getBoundingClientRect(), !1);
        }
        if (i == null && o < De(r)) {
            const a = r.childNodes[o];
            if (a.nodeType == 1) return ss(a.getBoundingClientRect(), !0);
        }
        return ss(r.getBoundingClientRect(), n >= 0);
    }
    if (i == null && o && (n < 0 || o == De(r))) {
        const a = r.childNodes[o - 1];
        const c =
            a.nodeType == 3
                ? dt(a, De(a) - (s ? 0 : 1))
                : a.nodeType == 1 && (a.nodeName != "BR" || !a.nextSibling)
                  ? a
                  : null;
        if (c) return Wn(vt(c, 1), !1);
    }
    if (i == null && o < De(r)) {
        let a = r.childNodes[o];
        for (; a.pmViewDesc && a.pmViewDesc.ignoreForCoords; ) {
            a = a.nextSibling;
        }
        const c = a
            ? a.nodeType == 3
                ? dt(a, 0, s ? 0 : 1)
                : a.nodeType == 1
                  ? a
                  : null
            : null;
        if (c) return Wn(vt(c, -1), !0);
    }
    return Wn(vt(r.nodeType == 3 ? dt(r) : r, -n), n >= 0);
}
function Wn(t, e) {
    if (t.width == 0) return t;
    const n = e ? t.left : t.right;
    return { top: t.top, bottom: t.bottom, left: n, right: n };
}
function ss(t, e) {
    if (t.height == 0) return t;
    const n = e ? t.top : t.bottom;
    return { top: n, bottom: n, left: t.left, right: t.right };
}
function rd(t, e, n) {
    const r = t.state;
    const o = t.root.activeElement;
    (r != e && t.updateState(e), o != t.dom && t.focus());
    try {
        return n();
    } finally {
        (r != e && t.updateState(r), o != t.dom && o && o.focus());
    }
}
function gm(t, e, n) {
    const r = e.selection;
    const o = n == "up" ? r.$from : r.$to;
    return rd(t, e, () => {
        let { node: i } = t.docView.domFromPos(o.pos, n == "up" ? -1 : 1);
        for (;;) {
            const l = t.docView.nearestDesc(i, !0);
            if (!l) break;
            if (l.node.isBlock) {
                i = l.contentDOM || l.dom;
                break;
            }
            i = l.dom.parentNode;
        }
        const s = nd(t, o.pos, 1);
        for (let l = i.firstChild; l; l = l.nextSibling) {
            let a;
            if (l.nodeType == 1) a = l.getClientRects();
            else if (l.nodeType == 3) {
                a = dt(l, 0, l.nodeValue.length).getClientRects();
            } else continue;
            for (let c = 0; c < a.length; c++) {
                const d = a[c];
                if (
                    d.bottom > d.top + 1 &&
                    (n == "up"
                        ? s.top - d.top > (d.bottom - s.top) * 2
                        : d.bottom - s.bottom > (s.bottom - d.top) * 2)
                ) {
                    return !1;
                }
            }
        }
        return !0;
    });
}
const ym = /[\u0590-\u08ac]/;
function bm(t, e, n) {
    const { $head: r } = e.selection;
    if (!r.parent.isTextblock) return !1;
    const o = r.parentOffset;
    const i = !o;
    const s = o == r.parent.content.size;
    const l = t.domSelection();
    return l
        ? !ym.test(r.parent.textContent) || !l.modify
            ? n == "left" || n == "backward"
                ? i
                : s
            : rd(t, e, () => {
                  const {
                      focusNode: a,
                      focusOffset: c,
                      anchorNode: d,
                      anchorOffset: u,
                  } = t.domSelectionRange();
                  const f = l.caretBidiLevel;
                  l.modify("move", n, "character");
                  const h = r.depth ? t.docView.domAfterPos(r.before()) : t.dom;
                  const { focusNode: p, focusOffset: m } =
                      t.domSelectionRange();
                  const g =
                      (p && !h.contains(p.nodeType == 1 ? p : p.parentNode)) ||
                      (a == p && c == m);
                  try {
                      (l.collapse(d, u),
                          a &&
                              (a != d || c != u) &&
                              l.extend &&
                              l.extend(a, c));
                  } catch {}
                  return (f != null && (l.caretBidiLevel = f), g);
              })
        : r.pos == r.start() || r.pos == r.end();
}
let kc = null;
let Sc = null;
let Cc = !1;
function wm(t, e, n) {
    return kc == e && Sc == n
        ? Cc
        : ((kc = e),
          (Sc = n),
          (Cc = n == "up" || n == "down" ? gm(t, e, n) : bm(t, e, n)));
}
const Pe = 0;
const vc = 1;
const Wt = 2;
const Je = 3;
const Gt = class {
    constructor(e, n, r, o) {
        ((this.parent = e),
            (this.children = n),
            (this.dom = r),
            (this.contentDOM = o),
            (this.dirty = Pe),
            (r.pmViewDesc = this));
    }

    matchesWidget(e) {
        return !1;
    }

    matchesMark(e) {
        return !1;
    }

    matchesNode(e, n, r) {
        return !1;
    }

    matchesHack(e) {
        return !1;
    }

    parseRule() {
        return null;
    }

    stopEvent(e) {
        return !1;
    }

    get size() {
        let e = 0;
        for (let n = 0; n < this.children.length; n++) {
            e += this.children[n].size;
        }
        return e;
    }

    get border() {
        return 0;
    }

    destroy() {
        ((this.parent = void 0),
            this.dom.pmViewDesc == this && (this.dom.pmViewDesc = void 0));
        for (let e = 0; e < this.children.length; e++) {
            this.children[e].destroy();
        }
    }

    posBeforeChild(e) {
        for (let n = 0, r = this.posAtStart; ; n++) {
            const o = this.children[n];
            if (o == e) return r;
            r += o.size;
        }
    }

    get posBefore() {
        return this.parent.posBeforeChild(this);
    }

    get posAtStart() {
        return this.parent ? this.parent.posBeforeChild(this) + this.border : 0;
    }

    get posAfter() {
        return this.posBefore + this.size;
    }

    get posAtEnd() {
        return this.posAtStart + this.size - 2 * this.border;
    }

    localPosFromDOM(e, n, r) {
        if (
            this.contentDOM &&
            this.contentDOM.contains(e.nodeType == 1 ? e : e.parentNode)
        ) {
            if (r < 0) {
                let i, s;
                if (e == this.contentDOM) i = e.childNodes[n - 1];
                else {
                    for (; e.parentNode != this.contentDOM; ) e = e.parentNode;
                    i = e.previousSibling;
                }
                for (; i && !((s = i.pmViewDesc) && s.parent == this); ) {
                    i = i.previousSibling;
                }
                return i ? this.posBeforeChild(s) + s.size : this.posAtStart;
            } else {
                let i, s;
                if (e == this.contentDOM) i = e.childNodes[n];
                else {
                    for (; e.parentNode != this.contentDOM; ) e = e.parentNode;
                    i = e.nextSibling;
                }
                for (; i && !((s = i.pmViewDesc) && s.parent == this); ) {
                    i = i.nextSibling;
                }
                return i ? this.posBeforeChild(s) : this.posAtEnd;
            }
        }
        let o;
        if (e == this.dom && this.contentDOM) o = n > de(this.contentDOM);
        else if (
            this.contentDOM &&
            this.contentDOM != this.dom &&
            this.dom.contains(this.contentDOM)
        ) {
            o = e.compareDocumentPosition(this.contentDOM) & 2;
        } else if (this.dom.firstChild) {
            if (n == 0) {
                for (let i = e; ; i = i.parentNode) {
                    if (i == this.dom) {
                        o = !1;
                        break;
                    }
                    if (i.previousSibling) break;
                }
            }
            if (o == null && n == e.childNodes.length) {
                for (let i = e; ; i = i.parentNode) {
                    if (i == this.dom) {
                        o = !0;
                        break;
                    }
                    if (i.nextSibling) break;
                }
            }
        }
        return (o ?? r > 0) ? this.posAtEnd : this.posAtStart;
    }

    nearestDesc(e, n = !1) {
        for (let r = !0, o = e; o; o = o.parentNode) {
            const i = this.getDesc(o);
            let s;
            if (i && (!n || i.node)) {
                if (
                    r &&
                    (s = i.nodeDOM) &&
                    !(s.nodeType == 1
                        ? s.contains(e.nodeType == 1 ? e : e.parentNode)
                        : s == e)
                ) {
                    r = !1;
                } else return i;
            }
        }
    }

    getDesc(e) {
        const n = e.pmViewDesc;
        for (let r = n; r; r = r.parent) if (r == this) return n;
    }

    posFromDOM(e, n, r) {
        for (let o = e; o; o = o.parentNode) {
            const i = this.getDesc(o);
            if (i) return i.localPosFromDOM(e, n, r);
        }
        return -1;
    }

    descAt(e) {
        for (let n = 0, r = 0; n < this.children.length; n++) {
            let o = this.children[n];
            const i = r + o.size;
            if (r == e && i != r) {
                for (; !o.border && o.children.length; ) {
                    for (let s = 0; s < o.children.length; s++) {
                        const l = o.children[s];
                        if (l.size) {
                            o = l;
                            break;
                        }
                    }
                }
                return o;
            }
            if (e < i) return o.descAt(e - r - o.border);
            r = i;
        }
    }

    domFromPos(e, n) {
        if (!this.contentDOM) return { node: this.dom, offset: 0, atom: e + 1 };
        let r = 0;
        let o = 0;
        for (let i = 0; r < this.children.length; r++) {
            const s = this.children[r];
            const l = i + s.size;
            if (l > e || s instanceof Fr) {
                o = e - i;
                break;
            }
            i = l;
        }
        if (o) {
            return this.children[r].domFromPos(o - this.children[r].border, n);
        }
        for (
            let i;
            r &&
            !(i = this.children[r - 1]).size &&
            i instanceof Hr &&
            i.side >= 0;
            r--
        );
        if (n <= 0) {
            let i;
            let s = !0;
            for (
                ;
                (i = r ? this.children[r - 1] : null),
                    !(!i || i.dom.parentNode == this.contentDOM);
                r--, s = !1
            );
            return i && n && s && !i.border && !i.domAtom
                ? i.domFromPos(i.size, n)
                : { node: this.contentDOM, offset: i ? de(i.dom) + 1 : 0 };
        } else {
            let i;
            let s = !0;
            for (
                ;
                (i = r < this.children.length ? this.children[r] : null),
                    !(!i || i.dom.parentNode == this.contentDOM);
                r++, s = !1
            );
            return i && s && !i.border && !i.domAtom
                ? i.domFromPos(0, n)
                : {
                      node: this.contentDOM,
                      offset: i ? de(i.dom) : this.contentDOM.childNodes.length,
                  };
        }
    }

    parseRange(e, n, r = 0) {
        if (this.children.length == 0) {
            return {
                node: this.contentDOM,
                from: e,
                to: n,
                fromOffset: 0,
                toOffset: this.contentDOM.childNodes.length,
            };
        }
        let o = -1;
        let i = -1;
        for (let s = r, l = 0; ; l++) {
            const a = this.children[l];
            const c = s + a.size;
            if (o == -1 && e <= c) {
                const d = s + a.border;
                if (
                    e >= d &&
                    n <= c - a.border &&
                    a.node &&
                    a.contentDOM &&
                    this.contentDOM.contains(a.contentDOM)
                ) {
                    return a.parseRange(e, n, d);
                }
                e = s;
                for (let u = l; u > 0; u--) {
                    const f = this.children[u - 1];
                    if (
                        f.size &&
                        f.dom.parentNode == this.contentDOM &&
                        !f.emptyChildAt(1)
                    ) {
                        o = de(f.dom) + 1;
                        break;
                    }
                    e -= f.size;
                }
                o == -1 && (o = 0);
            }
            if (o > -1 && (c > n || l == this.children.length - 1)) {
                n = c;
                for (let d = l + 1; d < this.children.length; d++) {
                    const u = this.children[d];
                    if (
                        u.size &&
                        u.dom.parentNode == this.contentDOM &&
                        !u.emptyChildAt(-1)
                    ) {
                        i = de(u.dom);
                        break;
                    }
                    n += u.size;
                }
                i == -1 && (i = this.contentDOM.childNodes.length);
                break;
            }
            s = c;
        }
        return {
            node: this.contentDOM,
            from: e,
            to: n,
            fromOffset: o,
            toOffset: i,
        };
    }

    emptyChildAt(e) {
        if (this.border || !this.contentDOM || !this.children.length) return !1;
        const n = this.children[e < 0 ? 0 : this.children.length - 1];
        return n.size == 0 || n.emptyChildAt(e);
    }

    domAfterPos(e) {
        const { node: n, offset: r } = this.domFromPos(e, 0);
        if (n.nodeType != 1 || r == n.childNodes.length) {
            throw new RangeError("No node after pos " + e);
        }
        return n.childNodes[r];
    }

    setSelection(e, n, r, o = !1) {
        const i = Math.min(e, n);
        const s = Math.max(e, n);
        for (let h = 0, p = 0; h < this.children.length; h++) {
            const m = this.children[h];
            const g = p + m.size;
            if (i > p && s < g) {
                return m.setSelection(e - p - m.border, n - p - m.border, r, o);
            }
            p = g;
        }
        let l = this.domFromPos(e, e ? -1 : 1);
        let a = n == e ? l : this.domFromPos(n, n ? -1 : 1);
        const c = r.root.getSelection();
        const d = r.domSelectionRange();
        let u = !1;
        if ((Ie || we) && e == n) {
            const { node: h, offset: p } = l;
            if (h.nodeType == 3) {
                if (
                    ((u = !!(
                        p &&
                        h.nodeValue[p - 1] ==
                            `
`
                    )),
                    u && p == h.nodeValue.length)
                ) {
                    for (let m = h, g; m; m = m.parentNode) {
                        if ((g = m.nextSibling)) {
                            g.nodeName == "BR" &&
                                (l = a =
                                    { node: g.parentNode, offset: de(g) + 1 });
                            break;
                        }
                        const y = m.pmViewDesc;
                        if (y && y.node && y.node.isBlock) break;
                    }
                }
            } else {
                const m = h.childNodes[p - 1];
                u = m && (m.nodeName == "BR" || m.contentEditable == "false");
            }
        }
        if (
            Ie &&
            d.focusNode &&
            d.focusNode != a.node &&
            d.focusNode.nodeType == 1
        ) {
            const h = d.focusNode.childNodes[d.focusOffset];
            h && h.contentEditable == "false" && (o = !0);
        }
        if (
            !(o || (u && we)) &&
            Jt(l.node, l.offset, d.anchorNode, d.anchorOffset) &&
            Jt(a.node, a.offset, d.focusNode, d.focusOffset)
        ) {
            return;
        }
        let f = !1;
        if ((c.extend || e == n) && !(u && Ie)) {
            c.collapse(l.node, l.offset);
            try {
                (e != n && c.extend(a.node, a.offset), (f = !0));
            } catch {}
        }
        if (!f) {
            if (e > n) {
                const p = l;
                ((l = a), (a = p));
            }
            const h = document.createRange();
            (h.setEnd(a.node, a.offset),
                h.setStart(l.node, l.offset),
                c.removeAllRanges(),
                c.addRange(h));
        }
    }

    ignoreMutation(e) {
        return !this.contentDOM && e.type != "selection";
    }

    get contentLost() {
        return (
            this.contentDOM &&
            this.contentDOM != this.dom &&
            !this.dom.contains(this.contentDOM)
        );
    }

    markDirty(e, n) {
        for (let r = 0, o = 0; o < this.children.length; o++) {
            const i = this.children[o];
            const s = r + i.size;
            if (r == s ? e <= s && n >= r : e < s && n > r) {
                const l = r + i.border;
                const a = s - i.border;
                if (e >= l && n <= a) {
                    ((this.dirty = e == r || n == s ? Wt : vc),
                        e == l &&
                        n == a &&
                        (i.contentLost || i.dom.parentNode != this.contentDOM)
                            ? (i.dirty = Je)
                            : i.markDirty(e - l, n - l));
                    return;
                } else {
                    i.dirty =
                        i.dom == i.contentDOM &&
                        i.dom.parentNode == this.contentDOM &&
                        !i.children.length
                            ? Wt
                            : Je;
                }
            }
            r = s;
        }
        this.dirty = Wt;
    }

    markParentsDirty() {
        let e = 1;
        for (let n = this.parent; n; n = n.parent, e++) {
            const r = e == 1 ? Wt : vc;
            n.dirty < r && (n.dirty = r);
        }
    }

    get domAtom() {
        return !1;
    }

    get ignoreForCoords() {
        return !1;
    }

    get ignoreForSelection() {
        return !1;
    }

    isText(e) {
        return !1;
    }
};
var Hr = class extends Gt {
    constructor(e, n, r, o) {
        let i;
        let s = n.type.toDOM;
        if (
            (typeof s === "function" &&
                (s = s(r, () => {
                    if (!i) return o;
                    if (i.parent) return i.parent.posBeforeChild(i);
                })),
            !n.type.spec.raw)
        ) {
            if (s.nodeType != 1) {
                const l = document.createElement("span");
                (l.appendChild(s), (s = l));
            }
            ((s.contentEditable = "false"),
                s.classList.add("ProseMirror-widget"));
        }
        (super(e, [], s, null),
            (this.widget = n),
            (this.widget = n),
            (i = this));
    }

    matchesWidget(e) {
        return this.dirty == Pe && e.type.eq(this.widget.type);
    }

    parseRule() {
        return { ignore: !0 };
    }

    stopEvent(e) {
        const n = this.widget.spec.stopEvent;
        return n ? n(e) : !1;
    }

    ignoreMutation(e) {
        return e.type != "selection" || this.widget.spec.ignoreSelection;
    }

    destroy() {
        (this.widget.type.destroy(this.dom), super.destroy());
    }

    get domAtom() {
        return !0;
    }

    get ignoreForSelection() {
        return !!this.widget.type.spec.relaxedSide;
    }

    get side() {
        return this.widget.type.side;
    }
};
const ms = class extends Gt {
    constructor(e, n, r, o) {
        (super(e, [], n, null), (this.textDOM = r), (this.text = o));
    }

    get size() {
        return this.text.length;
    }

    localPosFromDOM(e, n) {
        return e != this.textDOM
            ? this.posAtStart + (n ? this.size : 0)
            : this.posAtStart + n;
    }

    domFromPos(e) {
        return { node: this.textDOM, offset: e };
    }

    ignoreMutation(e) {
        return e.type === "characterData" && e.target.nodeValue == e.oldValue;
    }
};
const kn = class t extends Gt {
    constructor(e, n, r, o, i) {
        (super(e, [], r, o), (this.mark = n), (this.spec = i));
    }

    static create(e, n, r, o) {
        const i = o.nodeViews[n.type.name];
        let s = i && i(n, o, r);
        return (
            (!s || !s.dom) &&
                (s = it.renderSpec(
                    document,
                    n.type.spec.toDOM(n, r),
                    null,
                    n.attrs,
                )),
            new t(e, n, s.dom, s.contentDOM || s.dom, s)
        );
    }

    parseRule() {
        return this.dirty & Je || this.mark.type.spec.reparseInView
            ? null
            : {
                  mark: this.mark.type.name,
                  attrs: this.mark.attrs,
                  contentElement: this.contentDOM,
              };
    }

    matchesMark(e) {
        return this.dirty != Je && this.mark.eq(e);
    }

    markDirty(e, n) {
        if ((super.markDirty(e, n), this.dirty != Pe)) {
            let r = this.parent;
            for (; !r.node; ) r = r.parent;
            (r.dirty < this.dirty && (r.dirty = this.dirty), (this.dirty = Pe));
        }
    }

    slice(e, n, r) {
        const o = t.create(this.parent, this.mark, !0, r);
        let i = this.children;
        const s = this.size;
        (n < s && (i = ws(i, n, s, r)), e > 0 && (i = ws(i, 0, e, r)));
        for (let l = 0; l < i.length; l++) i[l].parent = o;
        return ((o.children = i), o);
    }

    ignoreMutation(e) {
        return this.spec.ignoreMutation
            ? this.spec.ignoreMutation(e)
            : super.ignoreMutation(e);
    }

    destroy() {
        (this.spec.destroy && this.spec.destroy(), super.destroy());
    }
};
const Et = class t extends Gt {
    constructor(e, n, r, o, i, s, l, a, c) {
        (super(e, [], i, s),
            (this.node = n),
            (this.outerDeco = r),
            (this.innerDeco = o),
            (this.nodeDOM = l));
    }

    static create(e, n, r, o, i, s) {
        const l = i.nodeViews[n.type.name];
        let a;
        const c =
            l &&
            l(
                n,
                i,
                () => {
                    if (!a) return s;
                    if (a.parent) return a.parent.posBeforeChild(a);
                },
                r,
                o,
            );
        let d = c && c.dom;
        let u = c && c.contentDOM;
        if (n.isText) {
            if (!d) d = document.createTextNode(n.text);
            else if (d.nodeType != 3) {
                throw new RangeError(
                    "Text must be rendered as a DOM text node",
                );
            }
        } else {
            d ||
                ({ dom: d, contentDOM: u } = it.renderSpec(
                    document,
                    n.type.spec.toDOM(n),
                    null,
                    n.attrs,
                ));
        }
        !u &&
            !n.isText &&
            d.nodeName != "BR" &&
            (d.hasAttribute("contenteditable") || (d.contentEditable = "false"),
            n.type.spec.draggable && (d.draggable = !0));
        const f = d;
        return (
            (d = sd(d, r, n)),
            c
                ? (a = new gs(e, n, r, o, d, u || null, f, c, i, s + 1))
                : n.isText
                  ? new $r(e, n, r, o, d, f, i)
                  : new t(e, n, r, o, d, u || null, f, i, s + 1)
        );
    }

    parseRule() {
        if (this.node.type.spec.reparseInView) return null;
        const e = { node: this.node.type.name, attrs: this.node.attrs };
        if (
            (this.node.type.whitespace == "pre" &&
                (e.preserveWhitespace = "full"),
            !this.contentDOM)
        ) {
            e.getContent = () => this.node.content;
        } else if (!this.contentLost) e.contentElement = this.contentDOM;
        else {
            for (let n = this.children.length - 1; n >= 0; n--) {
                const r = this.children[n];
                if (this.dom.contains(r.dom.parentNode)) {
                    e.contentElement = r.dom.parentNode;
                    break;
                }
            }
            e.contentElement || (e.getContent = () => C.empty);
        }
        return e;
    }

    matchesNode(e, n, r) {
        return (
            this.dirty == Pe &&
            e.eq(this.node) &&
            _r(n, this.outerDeco) &&
            r.eq(this.innerDeco)
        );
    }

    get size() {
        return this.node.nodeSize;
    }

    get border() {
        return this.node.isLeaf ? 0 : 1;
    }

    updateChildren(e, n) {
        const r = this.node.inlineContent;
        let o = n;
        const i = e.composing ? this.localCompositionInfo(e, n) : null;
        const s = i && i.pos > -1 ? i : null;
        const l = i && i.pos < 0;
        const a = new bs(this, s && s.node, e);
        (Cm(
            this.node,
            this.innerDeco,
            (c, d, u) => {
                (c.spec.marks
                    ? a.syncToMarks(c.spec.marks, r, e)
                    : c.type.side >= 0 &&
                      !u &&
                      a.syncToMarks(
                          d == this.node.childCount
                              ? q.none
                              : this.node.child(d).marks,
                          r,
                          e,
                      ),
                    a.placeWidget(c, e, o));
            },
            (c, d, u, f) => {
                a.syncToMarks(c.marks, r, e);
                let h;
                (a.findNodeMatch(c, d, u, f) ||
                    (l &&
                        e.state.selection.from > o &&
                        e.state.selection.to < o + c.nodeSize &&
                        (h = a.findIndexWithChild(i.node)) > -1 &&
                        a.updateNodeAt(c, d, u, h, e)) ||
                    a.updateNextNode(c, d, u, e, f, o) ||
                    a.addNode(c, d, u, e, o),
                    (o += c.nodeSize));
            },
        ),
            a.syncToMarks([], r, e),
            this.node.isTextblock && a.addTextblockHacks(),
            a.destroyRest(),
            (a.changed || this.dirty == Wt) &&
                (s && this.protectLocalComposition(e, s),
                od(this.contentDOM, this.children, e),
                xn && vm(this.dom)));
    }

    localCompositionInfo(e, n) {
        const { from: r, to: o } = e.state.selection;
        if (
            !(e.state.selection instanceof R) ||
            r < n ||
            o > n + this.node.content.size
        ) {
            return null;
        }
        const i = e.input.compositionNode;
        if (!i || !this.dom.contains(i.parentNode)) return null;
        if (this.node.inlineContent) {
            const s = i.nodeValue;
            const l = Mm(this.node.content, s, r - n, o - n);
            return l < 0 ? null : { node: i, pos: l, text: s };
        } else return { node: i, pos: -1, text: "" };
    }

    protectLocalComposition(e, { node: n, pos: r, text: o }) {
        if (this.getDesc(n)) return;
        let i = n;
        for (; i.parentNode != this.contentDOM; i = i.parentNode) {
            for (; i.previousSibling; ) {
                i.parentNode.removeChild(i.previousSibling);
            }
            for (; i.nextSibling; ) i.parentNode.removeChild(i.nextSibling);
            i.pmViewDesc && (i.pmViewDesc = void 0);
        }
        const s = new ms(this, i, n, o);
        (e.input.compositionNodes.push(s),
            (this.children = ws(this.children, r, r + o.length, e, s)));
    }

    update(e, n, r, o) {
        return this.dirty == Je || !e.sameMarkup(this.node)
            ? !1
            : (this.updateInner(e, n, r, o), !0);
    }

    updateInner(e, n, r, o) {
        (this.updateOuterDeco(n),
            (this.node = e),
            (this.innerDeco = r),
            this.contentDOM && this.updateChildren(o, this.posAtStart),
            (this.dirty = Pe));
    }

    updateOuterDeco(e) {
        if (_r(e, this.outerDeco)) return;
        const n = this.nodeDOM.nodeType != 1;
        const r = this.dom;
        ((this.dom = id(
            this.dom,
            this.nodeDOM,
            ys(this.outerDeco, this.node, n),
            ys(e, this.node, n),
        )),
            this.dom != r &&
                ((r.pmViewDesc = void 0), (this.dom.pmViewDesc = this)),
            (this.outerDeco = e));
    }

    selectNode() {
        this.nodeDOM.nodeType == 1 &&
            (this.nodeDOM.classList.add("ProseMirror-selectednode"),
            (this.contentDOM || !this.node.type.spec.draggable) &&
                (this.nodeDOM.draggable = !0));
    }

    deselectNode() {
        this.nodeDOM.nodeType == 1 &&
            (this.nodeDOM.classList.remove("ProseMirror-selectednode"),
            (this.contentDOM || !this.node.type.spec.draggable) &&
                this.nodeDOM.removeAttribute("draggable"));
    }

    get domAtom() {
        return this.node.isAtom;
    }
};
function Mc(t, e, n, r, o) {
    sd(r, e, t);
    const i = new Et(void 0, t, e, n, r, r, r, o, 0);
    return (i.contentDOM && i.updateChildren(o, 0), i);
}
var $r = class t extends Et {
    constructor(e, n, r, o, i, s, l) {
        super(e, n, r, o, i, null, s, l, 0);
    }

    parseRule() {
        let e = this.nodeDOM.parentNode;
        for (; e && e != this.dom && !e.pmIsDeco; ) e = e.parentNode;
        return { skip: e || !0 };
    }

    update(e, n, r, o) {
        return this.dirty == Je ||
            (this.dirty != Pe && !this.inParent()) ||
            !e.sameMarkup(this.node)
            ? !1
            : (this.updateOuterDeco(n),
              (this.dirty != Pe || e.text != this.node.text) &&
                  e.text != this.nodeDOM.nodeValue &&
                  ((this.nodeDOM.nodeValue = e.text),
                  o.trackWrites == this.nodeDOM && (o.trackWrites = null)),
              (this.node = e),
              (this.dirty = Pe),
              !0);
    }

    inParent() {
        const e = this.parent.contentDOM;
        for (let n = this.nodeDOM; n; n = n.parentNode) if (n == e) return !0;
        return !1;
    }

    domFromPos(e) {
        return { node: this.nodeDOM, offset: e };
    }

    localPosFromDOM(e, n, r) {
        return e == this.nodeDOM
            ? this.posAtStart + Math.min(n, this.node.text.length)
            : super.localPosFromDOM(e, n, r);
    }

    ignoreMutation(e) {
        return e.type != "characterData" && e.type != "selection";
    }

    slice(e, n, r) {
        const o = this.node.cut(e, n);
        const i = document.createTextNode(o.text);
        return new t(this.parent, o, this.outerDeco, this.innerDeco, i, i, r);
    }

    markDirty(e, n) {
        (super.markDirty(e, n),
            this.dom != this.nodeDOM &&
                (e == 0 || n == this.nodeDOM.nodeValue.length) &&
                (this.dirty = Je));
    }

    get domAtom() {
        return !1;
    }

    isText(e) {
        return this.node.text == e;
    }
};
var Fr = class extends Gt {
    parseRule() {
        return { ignore: !0 };
    }

    matchesHack(e) {
        return this.dirty == Pe && this.dom.nodeName == e;
    }

    get domAtom() {
        return !0;
    }

    get ignoreForCoords() {
        return this.dom.nodeName == "IMG";
    }
};
var gs = class extends Et {
    constructor(e, n, r, o, i, s, l, a, c, d) {
        (super(e, n, r, o, i, s, l, c, d), (this.spec = a));
    }

    update(e, n, r, o) {
        if (this.dirty == Je) return !1;
        if (
            this.spec.update &&
            (this.node.type == e.type || this.spec.multiType)
        ) {
            const i = this.spec.update(e, n, r);
            return (i && this.updateInner(e, n, r, o), i);
        } else {
            return !this.contentDOM && !e.isLeaf
                ? !1
                : super.update(e, n, r, o);
        }
    }

    selectNode() {
        this.spec.selectNode ? this.spec.selectNode() : super.selectNode();
    }

    deselectNode() {
        this.spec.deselectNode
            ? this.spec.deselectNode()
            : super.deselectNode();
    }

    setSelection(e, n, r, o) {
        this.spec.setSelection
            ? this.spec.setSelection(e, n, r.root)
            : super.setSelection(e, n, r, o);
    }

    destroy() {
        (this.spec.destroy && this.spec.destroy(), super.destroy());
    }

    stopEvent(e) {
        return this.spec.stopEvent ? this.spec.stopEvent(e) : !1;
    }

    ignoreMutation(e) {
        return this.spec.ignoreMutation
            ? this.spec.ignoreMutation(e)
            : super.ignoreMutation(e);
    }
};
function od(t, e, n) {
    let r = t.firstChild;
    let o = !1;
    for (let i = 0; i < e.length; i++) {
        const s = e[i];
        const l = s.dom;
        if (l.parentNode == t) {
            for (; l != r; ) ((r = Tc(r)), (o = !0));
            r = r.nextSibling;
        } else ((o = !0), t.insertBefore(l, r));
        if (s instanceof kn) {
            const a = r ? r.previousSibling : t.lastChild;
            (od(s.contentDOM, s.children, n),
                (r = a ? a.nextSibling : t.firstChild));
        }
    }
    for (; r; ) ((r = Tc(r)), (o = !0));
    o && n.trackWrites == t && (n.trackWrites = null);
}
const jn = function (t) {
    t && (this.nodeName = t);
};
jn.prototype = Object.create(null);
const jt = [new jn()];
function ys(t, e, n) {
    if (t.length == 0) return jt;
    let r = n ? jt[0] : new jn();
    const o = [r];
    for (let i = 0; i < t.length; i++) {
        const s = t[i].type.attrs;
        if (s) {
            s.nodeName && o.push((r = new jn(s.nodeName)));
            for (const l in s) {
                const a = s[l];
                a != null &&
                    (n &&
                        o.length == 1 &&
                        o.push((r = new jn(e.isInline ? "span" : "div"))),
                    l == "class"
                        ? (r.class = (r.class ? r.class + " " : "") + a)
                        : l == "style"
                          ? (r.style = (r.style ? r.style + ";" : "") + a)
                          : l != "nodeName" && (r[l] = a));
            }
        }
    }
    return o;
}
function id(t, e, n, r) {
    if (n == jt && r == jt) return e;
    let o = e;
    for (let i = 0; i < r.length; i++) {
        const s = r[i];
        let l = n[i];
        if (i) {
            let a;
            ((l &&
                l.nodeName == s.nodeName &&
                o != t &&
                (a = o.parentNode) &&
                a.nodeName.toLowerCase() == s.nodeName) ||
                ((a = document.createElement(s.nodeName)),
                (a.pmIsDeco = !0),
                a.appendChild(o),
                (l = jt[0])),
                (o = a));
        }
        xm(o, l || jt[0], s);
    }
    return o;
}
function xm(t, e, n) {
    for (const r in e) {
        r != "class" &&
            r != "style" &&
            r != "nodeName" &&
            !(r in n) &&
            t.removeAttribute(r);
    }
    for (const r in n) {
        r != "class" &&
            r != "style" &&
            r != "nodeName" &&
            n[r] != e[r] &&
            t.setAttribute(r, n[r]);
    }
    if (e.class != n.class) {
        const r = e.class ? e.class.split(" ").filter(Boolean) : [];
        const o = n.class ? n.class.split(" ").filter(Boolean) : [];
        for (let i = 0; i < r.length; i++) {
            o.indexOf(r[i]) == -1 && t.classList.remove(r[i]);
        }
        for (let i = 0; i < o.length; i++) {
            r.indexOf(o[i]) == -1 && t.classList.add(o[i]);
        }
        t.classList.length == 0 && t.removeAttribute("class");
    }
    if (e.style != n.style) {
        if (e.style) {
            const r =
                /\s*([\w\-\xa1-\uffff]+)\s*:(?:"(?:\\.|[^"])*"|'(?:\\.|[^'])*'|\(.*?\)|[^;])*/g;
            let o;
            for (; (o = r.exec(e.style)); ) t.style.removeProperty(o[1]);
        }
        n.style && (t.style.cssText += n.style);
    }
}
function sd(t, e, n) {
    return id(t, t, jt, ys(e, n, t.nodeType != 1));
}
function _r(t, e) {
    if (t.length != e.length) return !1;
    for (let n = 0; n < t.length; n++) if (!t[n].type.eq(e[n].type)) return !1;
    return !0;
}
function Tc(t) {
    const e = t.nextSibling;
    return (t.parentNode.removeChild(t), e);
}
var bs = class {
    constructor(e, n, r) {
        ((this.lock = n),
            (this.view = r),
            (this.index = 0),
            (this.stack = []),
            (this.changed = !1),
            (this.top = e),
            (this.preMatch = km(e.node.content, e)));
    }

    destroyBetween(e, n) {
        if (e != n) {
            for (let r = e; r < n; r++) this.top.children[r].destroy();
            (this.top.children.splice(e, n - e), (this.changed = !0));
        }
    }

    destroyRest() {
        this.destroyBetween(this.index, this.top.children.length);
    }

    syncToMarks(e, n, r) {
        let o = 0;
        let i = this.stack.length >> 1;
        const s = Math.min(i, e.length);
        for (
            ;
            o < s &&
            (o == i - 1 ? this.top : this.stack[(o + 1) << 1]).matchesMark(
                e[o],
            ) &&
            e[o].type.spec.spanning !== !1;

        ) {
            o++;
        }
        for (; o < i; ) {
            (this.destroyRest(),
                (this.top.dirty = Pe),
                (this.index = this.stack.pop()),
                (this.top = this.stack.pop()),
                i--);
        }
        for (; i < e.length; ) {
            this.stack.push(this.top, this.index + 1);
            let l = -1;
            for (
                let a = this.index;
                a < Math.min(this.index + 3, this.top.children.length);
                a++
            ) {
                const c = this.top.children[a];
                if (c.matchesMark(e[i]) && !this.isLocked(c.dom)) {
                    l = a;
                    break;
                }
            }
            if (l > -1) {
                (l > this.index &&
                    ((this.changed = !0), this.destroyBetween(this.index, l)),
                    (this.top = this.top.children[this.index]));
            } else {
                const a = kn.create(this.top, e[i], n, r);
                (this.top.children.splice(this.index, 0, a),
                    (this.top = a),
                    (this.changed = !0));
            }
            ((this.index = 0), i++);
        }
    }

    findNodeMatch(e, n, r, o) {
        let i = -1;
        let s;
        if (
            o >= this.preMatch.index &&
            (s = this.preMatch.matches[o - this.preMatch.index]).parent ==
                this.top &&
            s.matchesNode(e, n, r)
        ) {
            i = this.top.children.indexOf(s, this.index);
        } else {
            for (
                let l = this.index,
                    a = Math.min(this.top.children.length, l + 5);
                l < a;
                l++
            ) {
                const c = this.top.children[l];
                if (c.matchesNode(e, n, r) && !this.preMatch.matched.has(c)) {
                    i = l;
                    break;
                }
            }
        }
        return i < 0
            ? !1
            : (this.destroyBetween(this.index, i), this.index++, !0);
    }

    updateNodeAt(e, n, r, o, i) {
        const s = this.top.children[o];
        return (
            s.dirty == Je && s.dom == s.contentDOM && (s.dirty = Wt),
            s.update(e, n, r, i)
                ? (this.destroyBetween(this.index, o), this.index++, !0)
                : !1
        );
    }

    findIndexWithChild(e) {
        for (;;) {
            const n = e.parentNode;
            if (!n) return -1;
            if (n == this.top.contentDOM) {
                const r = e.pmViewDesc;
                if (r) {
                    for (
                        let o = this.index;
                        o < this.top.children.length;
                        o++
                    ) {
                        if (this.top.children[o] == r) return o;
                    }
                }
                return -1;
            }
            e = n;
        }
    }

    updateNextNode(e, n, r, o, i, s) {
        for (let l = this.index; l < this.top.children.length; l++) {
            const a = this.top.children[l];
            if (a instanceof Et) {
                const c = this.preMatch.matched.get(a);
                if (c != null && c != i) return !1;
                const d = a.dom;
                let u;
                const f =
                    this.isLocked(d) &&
                    !(
                        e.isText &&
                        a.node &&
                        a.node.isText &&
                        a.nodeDOM.nodeValue == e.text &&
                        a.dirty != Je &&
                        _r(n, a.outerDeco)
                    );
                if (!f && a.update(e, n, r, o)) {
                    return (
                        this.destroyBetween(this.index, l),
                        a.dom != d && (this.changed = !0),
                        this.index++,
                        !0
                    );
                }
                if (!f && (u = this.recreateWrapper(a, e, n, r, o, s))) {
                    return (
                        this.destroyBetween(this.index, l),
                        (this.top.children[this.index] = u),
                        u.contentDOM &&
                            ((u.dirty = Wt),
                            u.updateChildren(o, s + 1),
                            (u.dirty = Pe)),
                        (this.changed = !0),
                        this.index++,
                        !0
                    );
                }
                break;
            }
        }
        return !1;
    }

    recreateWrapper(e, n, r, o, i, s) {
        if (
            e.dirty ||
            n.isAtom ||
            !e.children.length ||
            !e.node.content.eq(n.content) ||
            !_r(r, e.outerDeco) ||
            !o.eq(e.innerDeco)
        ) {
            return null;
        }
        const l = Et.create(this.top, n, r, o, i, s);
        if (l.contentDOM) {
            ((l.children = e.children), (e.children = []));
            for (const a of l.children) a.parent = l;
        }
        return (e.destroy(), l);
    }

    addNode(e, n, r, o, i) {
        const s = Et.create(this.top, e, n, r, o, i);
        (s.contentDOM && s.updateChildren(o, i + 1),
            this.top.children.splice(this.index++, 0, s),
            (this.changed = !0));
    }

    placeWidget(e, n, r) {
        const o =
            this.index < this.top.children.length
                ? this.top.children[this.index]
                : null;
        if (
            o &&
            o.matchesWidget(e) &&
            (e == o.widget || !o.widget.type.toDOM.parentNode)
        ) {
            this.index++;
        } else {
            const i = new Hr(this.top, e, n, r);
            (this.top.children.splice(this.index++, 0, i), (this.changed = !0));
        }
    }

    addTextblockHacks() {
        let e = this.top.children[this.index - 1];
        let n = this.top;
        for (; e instanceof kn; ) {
            ((n = e), (e = n.children[n.children.length - 1]));
        }
        (!e ||
            !(e instanceof $r) ||
            /\n$/.test(e.node.text) ||
            (this.view.requiresGeckoHackNode && /\s$/.test(e.node.text))) &&
            ((we || ge) &&
                e &&
                e.dom.contentEditable == "false" &&
                this.addHackNode("IMG", n),
            this.addHackNode("BR", this.top));
    }

    addHackNode(e, n) {
        if (
            n == this.top &&
            this.index < n.children.length &&
            n.children[this.index].matchesHack(e)
        ) {
            this.index++;
        } else {
            const r = document.createElement(e);
            (e == "IMG" &&
                ((r.className = "ProseMirror-separator"), (r.alt = "")),
                e == "BR" && (r.className = "ProseMirror-trailingBreak"));
            const o = new Fr(this.top, [], r, null);
            (n != this.top
                ? n.children.push(o)
                : n.children.splice(this.index++, 0, o),
                (this.changed = !0));
        }
    }

    isLocked(e) {
        return (
            this.lock &&
            (e == this.lock ||
                (e.nodeType == 1 && e.contains(this.lock.parentNode)))
        );
    }
};
function km(t, e) {
    let n = e;
    let r = n.children.length;
    let o = t.childCount;
    const i = new Map();
    const s = [];
    e: for (; o > 0; ) {
        let l;
        for (;;) {
            if (r) {
                const c = n.children[r - 1];
                if (c instanceof kn) ((n = c), (r = c.children.length));
                else {
                    ((l = c), r--);
                    break;
                }
            } else {
                if (n == e) break e;
                ((r = n.parent.children.indexOf(n)), (n = n.parent));
            }
        }
        const a = l.node;
        if (a) {
            if (a != t.child(o - 1)) break;
            (--o, i.set(l, o), s.push(l));
        }
    }
    return { index: o, matched: i, matches: s.reverse() };
}
function Sm(t, e) {
    return t.type.side - e.type.side;
}
function Cm(t, e, n, r) {
    const o = e.locals(t);
    let i = 0;
    if (o.length == 0) {
        for (let c = 0; c < t.childCount; c++) {
            const d = t.child(c);
            (r(d, o, e.forChild(i, d), c), (i += d.nodeSize));
        }
        return;
    }
    let s = 0;
    const l = [];
    let a = null;
    for (let c = 0; ; ) {
        let d, u;
        for (; s < o.length && o[s].to == i; ) {
            const g = o[s++];
            g.widget && (d ? (u || (u = [d])).push(g) : (d = g));
        }
        if (d) {
            if (u) {
                u.sort(Sm);
                for (let g = 0; g < u.length; g++) n(u[g], c, !!a);
            } else n(d, c, !!a);
        }
        let f, h;
        if (a) ((h = -1), (f = a), (a = null));
        else if (c < t.childCount) ((h = c), (f = t.child(c++)));
        else break;
        for (let g = 0; g < l.length; g++) l[g].to <= i && l.splice(g--, 1);
        for (; s < o.length && o[s].from <= i && o[s].to > i; ) l.push(o[s++]);
        let p = i + f.nodeSize;
        if (f.isText) {
            let g = p;
            s < o.length && o[s].from < g && (g = o[s].from);
            for (let y = 0; y < l.length; y++) l[y].to < g && (g = l[y].to);
            g < p &&
                ((a = f.cut(g - i)), (f = f.cut(0, g - i)), (p = g), (h = -1));
        } else for (; s < o.length && o[s].to < p; ) s++;
        const m =
            f.isInline && !f.isLeaf ? l.filter((g) => !g.inline) : l.slice();
        (r(f, m, e.forChild(i, f), h), (i = p));
    }
}
function vm(t) {
    if (t.nodeName == "UL" || t.nodeName == "OL") {
        const e = t.style.cssText;
        ((t.style.cssText = e + "; list-style: square !important"),
            window.getComputedStyle(t).listStyle,
            (t.style.cssText = e));
    }
}
function Mm(t, e, n, r) {
    for (let o = 0, i = 0; o < t.childCount && i <= r; ) {
        const s = t.child(o++);
        const l = i;
        if (((i += s.nodeSize), !s.isText)) continue;
        let a = s.text;
        for (; o < t.childCount; ) {
            const c = t.child(o++);
            if (((i += c.nodeSize), !c.isText)) break;
            a += c.text;
        }
        if (i >= n) {
            if (i >= r && a.slice(r - e.length - l, r - l) == e) {
                return r - e.length;
            }
            const c = l < r ? a.lastIndexOf(e, r - l - 1) : -1;
            if (c >= 0 && c + e.length + l >= n) return l + c;
            if (
                n == r &&
                a.length >= r + e.length - l &&
                a.slice(r - l, r - l + e.length) == e
            ) {
                return r;
            }
        }
    }
    return -1;
}
function ws(t, e, n, r, o) {
    const i = [];
    for (let s = 0, l = 0; s < t.length; s++) {
        const a = t[s];
        const c = l;
        const d = (l += a.size);
        c >= n || d <= e
            ? i.push(a)
            : (c < e && i.push(a.slice(0, e - c, r)),
              o && (i.push(o), (o = void 0)),
              d > n && i.push(a.slice(n - c, a.size, r)));
    }
    return i;
}
function Os(t, e = null) {
    const n = t.domSelectionRange();
    const r = t.state.doc;
    if (!n.focusNode) return null;
    let o = t.docView.nearestDesc(n.focusNode);
    const i = o && o.size == 0;
    let s = t.docView.posFromDOM(n.focusNode, n.focusOffset, 1);
    if (s < 0) return null;
    let l = r.resolve(s);
    let a;
    let c;
    if (Jr(n)) {
        for (a = s; o && !o.node; ) o = o.parent;
        const u = o.node;
        if (
            o &&
            u.isAtom &&
            P.isSelectable(u) &&
            o.parent &&
            !(u.isInline && em(n.focusNode, n.focusOffset, o.dom))
        ) {
            const f = o.posBefore;
            c = new P(s == f ? l : r.resolve(f));
        }
    } else {
        if (
            n instanceof t.dom.ownerDocument.defaultView.Selection &&
            n.rangeCount > 1
        ) {
            let u = s;
            let f = s;
            for (let h = 0; h < n.rangeCount; h++) {
                const p = n.getRangeAt(h);
                ((u = Math.min(
                    u,
                    t.docView.posFromDOM(p.startContainer, p.startOffset, 1),
                )),
                    (f = Math.max(
                        f,
                        t.docView.posFromDOM(p.endContainer, p.endOffset, -1),
                    )));
            }
            if (u < 0) return null;
            (([a, s] = f == t.state.selection.anchor ? [f, u] : [u, f]),
                (l = r.resolve(s)));
        } else a = t.docView.posFromDOM(n.anchorNode, n.anchorOffset, 1);
        if (a < 0) return null;
    }
    const d = r.resolve(a);
    if (!c) {
        const u =
            e == "pointer" || (t.state.selection.head < l.pos && !i) ? 1 : -1;
        c = Rs(t, d, l, u);
    }
    return c;
}
function ld(t) {
    return t.editable
        ? t.hasFocus()
        : cd(t) &&
              document.activeElement &&
              document.activeElement.contains(t.dom);
}
function ft(t, e = !1) {
    const n = t.state.selection;
    if ((ad(t, n), !!ld(t))) {
        if (!e && t.input.mouseDown && t.input.mouseDown.allowDefault && ge) {
            const r = t.domSelectionRange();
            const o = t.domObserver.currentSelection;
            if (
                r.anchorNode &&
                o.anchorNode &&
                Jt(r.anchorNode, r.anchorOffset, o.anchorNode, o.anchorOffset)
            ) {
                ((t.input.mouseDown.delayedSelectionSync = !0),
                    t.domObserver.setCurSelection());
                return;
            }
        }
        if ((t.domObserver.disconnectSelection(), t.cursorWrapper)) Am(t);
        else {
            const { anchor: r, head: o } = n;
            let i;
            let s;
            (Ac &&
                !(n instanceof R) &&
                (n.$from.parent.inlineContent || (i = Ec(t, n.from)),
                !n.empty && !n.$from.parent.inlineContent && (s = Ec(t, n.to))),
                t.docView.setSelection(r, o, t, e),
                Ac && (i && Nc(i), s && Nc(s)),
                n.visible
                    ? t.dom.classList.remove("ProseMirror-hideselection")
                    : (t.dom.classList.add("ProseMirror-hideselection"),
                      "onselectionchange" in document && Tm(t)));
        }
        (t.domObserver.setCurSelection(), t.domObserver.connectSelection());
    }
}
var Ac = we || (ge && Yc < 63);
function Ec(t, e) {
    const { node: n, offset: r } = t.docView.domFromPos(e, 0);
    const o = r < n.childNodes.length ? n.childNodes[r] : null;
    const i = r ? n.childNodes[r - 1] : null;
    if (we && o && o.contentEditable == "false") return ls(o);
    if (
        (!o || o.contentEditable == "false") &&
        (!i || i.contentEditable == "false")
    ) {
        if (o) return ls(o);
        if (i) return ls(i);
    }
}
function ls(t) {
    return (
        (t.contentEditable = "true"),
        we && t.draggable && ((t.draggable = !1), (t.wasDraggable = !0)),
        t
    );
}
function Nc(t) {
    ((t.contentEditable = "false"),
        t.wasDraggable && ((t.draggable = !0), (t.wasDraggable = null)));
}
function Tm(t) {
    const e = t.dom.ownerDocument;
    e.removeEventListener("selectionchange", t.input.hideSelectionGuard);
    const n = t.domSelectionRange();
    const r = n.anchorNode;
    const o = n.anchorOffset;
    e.addEventListener(
        "selectionchange",
        (t.input.hideSelectionGuard = () => {
            (n.anchorNode != r || n.anchorOffset != o) &&
                (e.removeEventListener(
                    "selectionchange",
                    t.input.hideSelectionGuard,
                ),
                setTimeout(() => {
                    (!ld(t) || t.state.selection.visible) &&
                        t.dom.classList.remove("ProseMirror-hideselection");
                }, 20));
        }),
    );
}
function Am(t) {
    const e = t.domSelection();
    if (!e) return;
    const n = t.cursorWrapper.dom;
    const r = n.nodeName == "IMG";
    (r ? e.collapse(n.parentNode, de(n) + 1) : e.collapse(n, 0),
        !r &&
            !t.state.selection.visible &&
            Se &&
            At <= 11 &&
            ((n.disabled = !0), (n.disabled = !1)));
}
function ad(t, e) {
    if (e instanceof P) {
        const n = t.docView.descAt(e.from);
        n != t.lastSelectedViewDesc &&
            (Oc(t), n && n.selectNode(), (t.lastSelectedViewDesc = n));
    } else Oc(t);
}
function Oc(t) {
    t.lastSelectedViewDesc &&
        (t.lastSelectedViewDesc.parent && t.lastSelectedViewDesc.deselectNode(),
        (t.lastSelectedViewDesc = void 0));
}
function Rs(t, e, n, r) {
    return (
        t.someProp("createSelectionBetween", (o) => o(t, e, n)) ||
        R.between(e, n, r)
    );
}
function Rc(t) {
    return t.editable && !t.hasFocus() ? !1 : cd(t);
}
function cd(t) {
    const e = t.domSelectionRange();
    if (!e.anchorNode) return !1;
    try {
        return (
            t.dom.contains(
                e.anchorNode.nodeType == 3
                    ? e.anchorNode.parentNode
                    : e.anchorNode,
            ) &&
            (t.editable ||
                t.dom.contains(
                    e.focusNode.nodeType == 3
                        ? e.focusNode.parentNode
                        : e.focusNode,
                ))
        );
    } catch {
        return !1;
    }
}
function Em(t) {
    const e = t.docView.domFromPos(t.state.selection.anchor, 0);
    const n = t.domSelectionRange();
    return Jt(e.node, e.offset, n.anchorNode, n.anchorOffset);
}
function xs(t, e) {
    const { $anchor: n, $head: r } = t.selection;
    const o = e > 0 ? n.max(r) : n.min(r);
    const i = o.parent.inlineContent
        ? o.depth
            ? t.doc.resolve(e > 0 ? o.after() : o.before())
            : null
        : o;
    return i && I.findFrom(i, e);
}
function Mt(t, e) {
    return (t.dispatch(t.state.tr.setSelection(e).scrollIntoView()), !0);
}
function Dc(t, e, n) {
    const r = t.state.selection;
    if (r instanceof R) {
        if (n.indexOf("s") > -1) {
            const { $head: o } = r;
            const i = o.textOffset ? null : e < 0 ? o.nodeBefore : o.nodeAfter;
            if (!i || i.isText || !i.isLeaf) return !1;
            const s = t.state.doc.resolve(
                o.pos + i.nodeSize * (e < 0 ? -1 : 1),
            );
            return Mt(t, new R(r.$anchor, s));
        } else if (r.empty) {
            if (t.endOfTextblock(e > 0 ? "forward" : "backward")) {
                const o = xs(t.state, e);
                return o && o instanceof P ? Mt(t, o) : !1;
            } else if (!(Re && n.indexOf("m") > -1)) {
                const o = r.$head;
                const i = o.textOffset
                    ? null
                    : e < 0
                      ? o.nodeBefore
                      : o.nodeAfter;
                let s;
                if (!i || i.isText) return !1;
                const l = e < 0 ? o.pos - i.nodeSize : o.pos;
                return i.isAtom || ((s = t.docView.descAt(l)) && !s.contentDOM)
                    ? P.isSelectable(i)
                        ? Mt(
                              t,
                              new P(
                                  e < 0
                                      ? t.state.doc.resolve(o.pos - i.nodeSize)
                                      : o,
                              ),
                          )
                        : Yn
                          ? Mt(
                                t,
                                new R(
                                    t.state.doc.resolve(
                                        e < 0 ? l : l + i.nodeSize,
                                    ),
                                ),
                            )
                          : !1
                    : !1;
            }
        } else return !1;
    } else {
        if (r instanceof P && r.node.isInline) {
            return Mt(t, new R(e > 0 ? r.$to : r.$from));
        }
        {
            const o = xs(t.state, e);
            return o ? Mt(t, o) : !1;
        }
    }
}
function Vr(t) {
    return t.nodeType == 3 ? t.nodeValue.length : t.childNodes.length;
}
function Kn(t, e) {
    const n = t.pmViewDesc;
    return n && n.size == 0 && (e < 0 || t.nextSibling || t.nodeName != "BR");
}
function gn(t, e) {
    return e < 0 ? Nm(t) : Om(t);
}
function Nm(t) {
    const e = t.domSelectionRange();
    let n = e.focusNode;
    let r = e.focusOffset;
    if (!n) return;
    let o;
    let i;
    let s = !1;
    for (
        Ie &&
        n.nodeType == 1 &&
        r < Vr(n) &&
        Kn(n.childNodes[r], -1) &&
        (s = !0);
        ;

    ) {
        if (r > 0) {
            if (n.nodeType != 1) break;
            {
                const l = n.childNodes[r - 1];
                if (Kn(l, -1)) ((o = n), (i = --r));
                else if (l.nodeType == 3) ((n = l), (r = n.nodeValue.length));
                else break;
            }
        } else {
            if (dd(n)) break;
            {
                let l = n.previousSibling;
                for (; l && Kn(l, -1); ) {
                    ((o = n.parentNode), (i = de(l)), (l = l.previousSibling));
                }
                if (l) ((n = l), (r = Vr(n)));
                else {
                    if (((n = n.parentNode), n == t.dom)) break;
                    r = 0;
                }
            }
        }
    }
    s ? ks(t, n, r) : o && ks(t, o, i);
}
function Om(t) {
    const e = t.domSelectionRange();
    let n = e.focusNode;
    let r = e.focusOffset;
    if (!n) return;
    let o = Vr(n);
    let i;
    let s;
    for (;;) {
        if (r < o) {
            if (n.nodeType != 1) break;
            const l = n.childNodes[r];
            if (Kn(l, 1)) ((i = n), (s = ++r));
            else break;
        } else {
            if (dd(n)) break;
            {
                let l = n.nextSibling;
                for (; l && Kn(l, 1); ) {
                    ((i = l.parentNode), (s = de(l) + 1), (l = l.nextSibling));
                }
                if (l) ((n = l), (r = 0), (o = Vr(n)));
                else {
                    if (((n = n.parentNode), n == t.dom)) break;
                    r = o = 0;
                }
            }
        }
    }
    i && ks(t, i, s);
}
function dd(t) {
    const e = t.pmViewDesc;
    return e && e.node && e.node.isBlock;
}
function Rm(t, e) {
    for (; t && e == t.childNodes.length && !Xn(t); ) {
        ((e = de(t) + 1), (t = t.parentNode));
    }
    for (; t && e < t.childNodes.length; ) {
        const n = t.childNodes[e];
        if (n.nodeType == 3) return n;
        if (n.nodeType == 1 && n.contentEditable == "false") break;
        ((t = n), (e = 0));
    }
}
function Dm(t, e) {
    for (; t && !e && !Xn(t); ) ((e = de(t)), (t = t.parentNode));
    for (; t && e; ) {
        const n = t.childNodes[e - 1];
        if (n.nodeType == 3) return n;
        if (n.nodeType == 1 && n.contentEditable == "false") break;
        ((t = n), (e = t.childNodes.length));
    }
}
function ks(t, e, n) {
    if (e.nodeType != 3) {
        let i, s;
        (s = Rm(e, n))
            ? ((e = s), (n = 0))
            : (i = Dm(e, n)) && ((e = i), (n = i.nodeValue.length));
    }
    const r = t.domSelection();
    if (!r) return;
    if (Jr(r)) {
        const i = document.createRange();
        (i.setEnd(e, n), i.setStart(e, n), r.removeAllRanges(), r.addRange(i));
    } else r.extend && r.extend(e, n);
    t.domObserver.setCurSelection();
    const { state: o } = t;
    setTimeout(() => {
        t.state == o && ft(t);
    }, 50);
}
function Ic(t, e) {
    const n = t.state.doc.resolve(e);
    if (!(ge || rm) && n.parent.inlineContent) {
        const o = t.coordsAtPos(e);
        if (e > n.start()) {
            const i = t.coordsAtPos(e - 1);
            const s = (i.top + i.bottom) / 2;
            if (s > o.top && s < o.bottom && Math.abs(i.left - o.left) > 1) {
                return i.left < o.left ? "ltr" : "rtl";
            }
        }
        if (e < n.end()) {
            const i = t.coordsAtPos(e + 1);
            const s = (i.top + i.bottom) / 2;
            if (s > o.top && s < o.bottom && Math.abs(i.left - o.left) > 1) {
                return i.left > o.left ? "ltr" : "rtl";
            }
        }
    }
    return getComputedStyle(t.dom).direction == "rtl" ? "rtl" : "ltr";
}
function Pc(t, e, n) {
    const r = t.state.selection;
    if (
        (r instanceof R && !r.empty) ||
        n.indexOf("s") > -1 ||
        (Re && n.indexOf("m") > -1)
    ) {
        return !1;
    }
    const { $from: o, $to: i } = r;
    if (!o.parent.inlineContent || t.endOfTextblock(e < 0 ? "up" : "down")) {
        const s = xs(t.state, e);
        if (s && s instanceof P) return Mt(t, s);
    }
    if (!o.parent.inlineContent) {
        const s = e < 0 ? o : i;
        const l = r instanceof be ? I.near(s, e) : I.findFrom(s, e);
        return l ? Mt(t, l) : !1;
    }
    return !1;
}
function Lc(t, e) {
    if (!(t.state.selection instanceof R)) return !0;
    const { $head: n, $anchor: r, empty: o } = t.state.selection;
    if (!n.sameParent(r)) return !0;
    if (!o) return !1;
    if (t.endOfTextblock(e > 0 ? "forward" : "backward")) return !0;
    const i = !n.textOffset && (e < 0 ? n.nodeBefore : n.nodeAfter);
    if (i && !i.isText) {
        const s = t.state.tr;
        return (
            e < 0
                ? s.delete(n.pos - i.nodeSize, n.pos)
                : s.delete(n.pos, n.pos + i.nodeSize),
            t.dispatch(s),
            !0
        );
    }
    return !1;
}
function zc(t, e, n) {
    (t.domObserver.stop(), (e.contentEditable = n), t.domObserver.start());
}
function Im(t) {
    if (!we || t.state.selection.$head.parentOffset > 0) return !1;
    const { focusNode: e, focusOffset: n } = t.domSelectionRange();
    if (
        e &&
        e.nodeType == 1 &&
        n == 0 &&
        e.firstChild &&
        e.firstChild.contentEditable == "false"
    ) {
        const r = e.firstChild;
        (zc(t, r, "true"), setTimeout(() => zc(t, r, "false"), 20));
    }
    return !1;
}
function Pm(t) {
    let e = "";
    return (
        t.ctrlKey && (e += "c"),
        t.metaKey && (e += "m"),
        t.altKey && (e += "a"),
        t.shiftKey && (e += "s"),
        e
    );
}
function Lm(t, e) {
    const n = e.keyCode;
    const r = Pm(e);
    if (n == 8 || (Re && n == 72 && r == "c")) return Lc(t, -1) || gn(t, -1);
    if ((n == 46 && !e.shiftKey) || (Re && n == 68 && r == "c")) {
        return Lc(t, 1) || gn(t, 1);
    }
    if (n == 13 || n == 27) return !0;
    if (n == 37 || (Re && n == 66 && r == "c")) {
        const o =
            n == 37 ? (Ic(t, t.state.selection.from) == "ltr" ? -1 : 1) : -1;
        return Dc(t, o, r) || gn(t, o);
    } else if (n == 39 || (Re && n == 70 && r == "c")) {
        const o =
            n == 39 ? (Ic(t, t.state.selection.from) == "ltr" ? 1 : -1) : 1;
        return Dc(t, o, r) || gn(t, o);
    } else {
        if (n == 38 || (Re && n == 80 && r == "c")) {
            return Pc(t, -1, r) || gn(t, -1);
        }
        if (n == 40 || (Re && n == 78 && r == "c")) {
            return Im(t) || Pc(t, 1, r) || gn(t, 1);
        }
        if (
            r == (Re ? "m" : "c") &&
            (n == 66 || n == 73 || n == 89 || n == 90)
        ) {
            return !0;
        }
    }
    return !1;
}
function Ds(t, e) {
    t.someProp("transformCopied", (h) => {
        e = h(e, t);
    });
    const n = [];
    let { content: r, openStart: o, openEnd: i } = e;
    for (
        ;
        o > 1 && i > 1 && r.childCount == 1 && r.firstChild.childCount == 1;

    ) {
        (o--, i--);
        const h = r.firstChild;
        (n.push(h.type.name, h.attrs != h.type.defaultAttrs ? h.attrs : null),
            (r = h.content));
    }
    const s =
        t.someProp("clipboardSerializer") || it.fromSchema(t.state.schema);
    const l = gd();
    const a = l.createElement("div");
    a.appendChild(s.serializeFragment(r, { document: l }));
    let c = a.firstChild;
    let d;
    let u = 0;
    for (; c && c.nodeType == 1 && (d = md[c.nodeName.toLowerCase()]); ) {
        for (let h = d.length - 1; h >= 0; h--) {
            const p = l.createElement(d[h]);
            for (; a.firstChild; ) p.appendChild(a.firstChild);
            (a.appendChild(p), u++);
        }
        c = a.firstChild;
    }
    c &&
        c.nodeType == 1 &&
        c.setAttribute(
            "data-pm-slice",
            `${o} ${i}${u ? ` -${u}` : ""} ${JSON.stringify(n)}`,
        );
    const f =
        t.someProp("clipboardTextSerializer", (h) => h(e, t)) ||
        e.content.textBetween(
            0,
            e.content.size,
            `

`,
        );
    return { dom: a, text: f, slice: e };
}
function ud(t, e, n, r, o) {
    const i = o.parent.type.spec.code;
    let s;
    let l;
    if (!n && !e) return null;
    const a = !!e && (r || i || !n);
    if (a) {
        if (
            (t.someProp("transformPastedText", (f) => {
                e = f(e, i || r, t);
            }),
            i)
        ) {
            return (
                (l = new E(
                    C.from(
                        t.state.schema.text(
                            e.replace(
                                /\r\n?/g,
                                `
`,
                            ),
                        ),
                    ),
                    0,
                    0,
                )),
                t.someProp("transformPasted", (f) => {
                    l = f(l, t, !0);
                }),
                l
            );
        }
        const u = t.someProp("clipboardTextParser", (f) => f(e, o, r, t));
        if (u) l = u;
        else {
            const f = o.marks();
            const { schema: h } = t.state;
            const p = it.fromSchema(h);
            ((s = document.createElement("div")),
                e.split(/(?:\r\n?|\n)+/).forEach((m) => {
                    const g = s.appendChild(document.createElement("p"));
                    m && g.appendChild(p.serializeNode(h.text(m, f)));
                }));
        }
    } else {
        (t.someProp("transformPastedHTML", (u) => {
            n = u(n, t);
        }),
            (s = $m(n)),
            Yn && Fm(s));
    }
    const c = s && s.querySelector("[data-pm-slice]");
    const d =
        c &&
        /^(\d+) (\d+)(?: -(\d+))? (.*)/.exec(
            c.getAttribute("data-pm-slice") || "",
        );
    if (d && d[3]) {
        for (let u = +d[3]; u > 0; u--) {
            let f = s.firstChild;
            for (; f && f.nodeType != 1; ) f = f.nextSibling;
            if (!f) break;
            s = f;
        }
    }
    if (
        (l ||
            (l = (
                t.someProp("clipboardParser") ||
                t.someProp("domParser") ||
                Ue.fromSchema(t.state.schema)
            ).parseSlice(s, {
                preserveWhitespace: !!(a || d),
                context: o,
                ruleFromNode(f) {
                    return f.nodeName == "BR" &&
                        !f.nextSibling &&
                        f.parentNode &&
                        !zm.test(f.parentNode.nodeName)
                        ? { ignore: !0 }
                        : null;
                },
            })),
        d)
    ) {
        l = _m(Bc(l, +d[1], +d[2]), d[4]);
    } else if (
        ((l = E.maxOpen(Bm(l.content, o), !0)), l.openStart || l.openEnd)
    ) {
        let u = 0;
        let f = 0;
        for (
            let h = l.content.firstChild;
            u < l.openStart && !h.type.spec.isolating;
            u++, h = h.firstChild
        );
        for (
            let h = l.content.lastChild;
            f < l.openEnd && !h.type.spec.isolating;
            f++, h = h.lastChild
        );
        l = Bc(l, u, f);
    }
    return (
        t.someProp("transformPasted", (u) => {
            l = u(l, t, a);
        }),
        l
    );
}
var zm =
    /^(a|abbr|acronym|b|cite|code|del|em|i|ins|kbd|label|output|q|ruby|s|samp|span|strong|sub|sup|time|u|tt|var)$/i;
function Bm(t, e) {
    if (t.childCount < 2) return t;
    for (let n = e.depth; n >= 0; n--) {
        let o = e.node(n).contentMatchAt(e.index(n));
        let i;
        let s = [];
        if (
            (t.forEach((l) => {
                if (!s) return;
                const a = o.findWrapping(l.type);
                let c;
                if (!a) return (s = null);
                if (
                    (c =
                        s.length && i.length && hd(a, i, l, s[s.length - 1], 0))
                ) {
                    s[s.length - 1] = c;
                } else {
                    s.length &&
                        (s[s.length - 1] = pd(s[s.length - 1], i.length));
                    const d = fd(l, a);
                    (s.push(d), (o = o.matchType(d.type)), (i = a));
                }
            }),
            s)
        ) {
            return C.from(s);
        }
    }
    return t;
}
function fd(t, e, n = 0) {
    for (let r = e.length - 1; r >= n; r--) t = e[r].create(null, C.from(t));
    return t;
}
function hd(t, e, n, r, o) {
    if (o < t.length && o < e.length && t[o] == e[o]) {
        const i = hd(t, e, n, r.lastChild, o + 1);
        if (i) return r.copy(r.content.replaceChild(r.childCount - 1, i));
        if (
            r
                .contentMatchAt(r.childCount)
                .matchType(o == t.length - 1 ? n.type : t[o + 1])
        ) {
            return r.copy(r.content.append(C.from(fd(n, t, o + 1))));
        }
    }
}
function pd(t, e) {
    if (e == 0) return t;
    const n = t.content.replaceChild(t.childCount - 1, pd(t.lastChild, e - 1));
    const r = t.contentMatchAt(t.childCount).fillBefore(C.empty, !0);
    return t.copy(n.append(r));
}
function Ss(t, e, n, r, o, i) {
    const s = e < 0 ? t.firstChild : t.lastChild;
    let l = s.content;
    return (
        t.childCount > 1 && (i = 0),
        o < r - 1 && (l = Ss(l, e, n, r, o + 1, i)),
        o >= n &&
            (l =
                e < 0
                    ? s
                          .contentMatchAt(0)
                          .fillBefore(l, i <= o)
                          .append(l)
                    : l.append(
                          s
                              .contentMatchAt(s.childCount)
                              .fillBefore(C.empty, !0),
                      )),
        t.replaceChild(e < 0 ? 0 : t.childCount - 1, s.copy(l))
    );
}
function Bc(t, e, n) {
    return (
        e < t.openStart &&
            (t = new E(
                Ss(t.content, -1, e, t.openStart, 0, t.openEnd),
                e,
                t.openEnd,
            )),
        n < t.openEnd &&
            (t = new E(Ss(t.content, 1, n, t.openEnd, 0, 0), t.openStart, n)),
        t
    );
}
var md = {
    thead: ["table"],
    tbody: ["table"],
    tfoot: ["table"],
    caption: ["table"],
    colgroup: ["table"],
    col: ["table", "colgroup"],
    tr: ["table", "tbody"],
    td: ["table", "tbody", "tr"],
    th: ["table", "tbody", "tr"],
};
let Hc = null;
function gd() {
    return Hc || (Hc = document.implementation.createHTMLDocument("title"));
}
let as = null;
function Hm(t) {
    const e = window.trustedTypes;
    return e
        ? (as ||
              (as =
                  e.defaultPolicy ||
                  e.createPolicy("ProseMirrorClipboard", {
                      createHTML: (n) => n,
                  })),
          as.createHTML(t))
        : t;
}
function $m(t) {
    const e = /^(\s*<meta [^>]*>)*/.exec(t);
    e && (t = t.slice(e[0].length));
    let n = gd().createElement("div");
    const r = /<([a-z][^>\s]+)/i.exec(t);
    let o;
    if (
        ((o = r && md[r[1].toLowerCase()]) &&
            (t =
                o.map((i) => "<" + i + ">").join("") +
                t +
                o
                    .map((i) => "</" + i + ">")
                    .reverse()
                    .join("")),
        (n.innerHTML = Hm(t)),
        o)
    ) {
        for (let i = 0; i < o.length; i++) n = n.querySelector(o[i]) || n;
    }
    return n;
}
function Fm(t) {
    const e = t.querySelectorAll(
        ge ? "span:not([class]):not([style])" : "span.Apple-converted-space",
    );
    for (let n = 0; n < e.length; n++) {
        const r = e[n];
        r.childNodes.length == 1 &&
            r.textContent == "\xA0" &&
            r.parentNode &&
            r.parentNode.replaceChild(t.ownerDocument.createTextNode(" "), r);
    }
}
function _m(t, e) {
    if (!t.size) return t;
    const n = t.content.firstChild.type.schema;
    let r;
    try {
        r = JSON.parse(e);
    } catch {
        return t;
    }
    let { content: o, openStart: i, openEnd: s } = t;
    for (let l = r.length - 2; l >= 0; l -= 2) {
        const a = n.nodes[r[l]];
        if (!a || a.hasRequiredAttrs()) break;
        ((o = C.from(a.create(r[l + 1], o))), i++, s++);
    }
    return new E(o, i, s);
}
const xe = {};
const ke = {};
const Vm = { touchstart: !0, touchmove: !0 };
const Cs = class {
    constructor() {
        ((this.shiftKey = !1),
            (this.mouseDown = null),
            (this.lastKeyCode = null),
            (this.lastKeyCodeTime = 0),
            (this.lastClick = { time: 0, x: 0, y: 0, type: "", button: 0 }),
            (this.lastSelectionOrigin = null),
            (this.lastSelectionTime = 0),
            (this.lastIOSEnter = 0),
            (this.lastIOSEnterFallbackTimeout = -1),
            (this.lastFocus = 0),
            (this.lastTouch = 0),
            (this.lastChromeDelete = 0),
            (this.composing = !1),
            (this.compositionNode = null),
            (this.composingTimeout = -1),
            (this.compositionNodes = []),
            (this.compositionEndedAt = -2e8),
            (this.compositionID = 1),
            (this.compositionPendingChanges = 0),
            (this.domChangeCount = 0),
            (this.eventHandlers = Object.create(null)),
            (this.hideSelectionGuard = null));
    }
};
function Wm(t) {
    for (const e in xe) {
        const n = xe[e];
        t.dom.addEventListener(
            e,
            (t.input.eventHandlers[e] = (r) => {
                Km(t, r) &&
                    !Is(t, r) &&
                    (t.editable || !(r.type in ke)) &&
                    n(t, r);
            }),
            Vm[e] ? { passive: !0 } : void 0,
        );
    }
    (we && t.dom.addEventListener("input", () => null), vs(t));
}
function Tt(t, e) {
    ((t.input.lastSelectionOrigin = e),
        (t.input.lastSelectionTime = Date.now()));
}
function jm(t) {
    t.domObserver.stop();
    for (const e in t.input.eventHandlers) {
        t.dom.removeEventListener(e, t.input.eventHandlers[e]);
    }
    (clearTimeout(t.input.composingTimeout),
        clearTimeout(t.input.lastIOSEnterFallbackTimeout));
}
function vs(t) {
    t.someProp("handleDOMEvents", (e) => {
        for (const n in e) {
            t.input.eventHandlers[n] ||
                t.dom.addEventListener(
                    n,
                    (t.input.eventHandlers[n] = (r) => Is(t, r)),
                );
        }
    });
}
function Is(t, e) {
    return t.someProp("handleDOMEvents", (n) => {
        const r = n[e.type];
        return r ? r(t, e) || e.defaultPrevented : !1;
    });
}
function Km(t, e) {
    if (!e.bubbles) return !0;
    if (e.defaultPrevented) return !1;
    for (let n = e.target; n != t.dom; n = n.parentNode) {
        if (
            !n ||
            n.nodeType == 11 ||
            (n.pmViewDesc && n.pmViewDesc.stopEvent(e))
        ) {
            return !1;
        }
    }
    return !0;
}
function Um(t, e) {
    !Is(t, e) &&
        xe[e.type] &&
        (t.editable || !(e.type in ke)) &&
        xe[e.type](t, e);
}
ke.keydown = (t, e) => {
    const n = e;
    if (
        ((t.input.shiftKey = n.keyCode == 16 || n.shiftKey),
        !bd(t, n) &&
            ((t.input.lastKeyCode = n.keyCode),
            (t.input.lastKeyCodeTime = Date.now()),
            !(ut && ge && n.keyCode == 13)))
    ) {
        if (
            (n.keyCode != 229 && t.domObserver.forceFlush(),
            xn && n.keyCode == 13 && !n.ctrlKey && !n.altKey && !n.metaKey)
        ) {
            const r = Date.now();
            ((t.input.lastIOSEnter = r),
                (t.input.lastIOSEnterFallbackTimeout = setTimeout(() => {
                    t.input.lastIOSEnter == r &&
                        (t.someProp("handleKeyDown", (o) =>
                            o(t, Vt(13, "Enter")),
                        ),
                        (t.input.lastIOSEnter = 0));
                }, 200)));
        } else {
            t.someProp("handleKeyDown", (r) => r(t, n)) || Lm(t, n)
                ? n.preventDefault()
                : Tt(t, "key");
        }
    }
};
ke.keyup = (t, e) => {
    e.keyCode == 16 && (t.input.shiftKey = !1);
};
ke.keypress = (t, e) => {
    const n = e;
    if (
        bd(t, n) ||
        !n.charCode ||
        (n.ctrlKey && !n.altKey) ||
        (Re && n.metaKey)
    ) {
        return;
    }
    if (t.someProp("handleKeyPress", (o) => o(t, n))) {
        n.preventDefault();
        return;
    }
    const r = t.state.selection;
    if (!(r instanceof R) || !r.$from.sameParent(r.$to)) {
        const o = String.fromCharCode(n.charCode);
        const i = () => t.state.tr.insertText(o).scrollIntoView();
        (!/[\r\n]/.test(o) &&
            !t.someProp("handleTextInput", (s) =>
                s(t, r.$from.pos, r.$to.pos, o, i),
            ) &&
            t.dispatch(i()),
            n.preventDefault());
    }
};
function Gr(t) {
    return { left: t.clientX, top: t.clientY };
}
function qm(t, e) {
    const n = e.x - t.clientX;
    const r = e.y - t.clientY;
    return n * n + r * r < 100;
}
function Ps(t, e, n, r, o) {
    if (r == -1) return !1;
    const i = t.state.doc.resolve(r);
    for (let s = i.depth + 1; s > 0; s--) {
        if (
            t.someProp(e, (l) =>
                s > i.depth
                    ? l(t, n, i.nodeAfter, i.before(s), o, !0)
                    : l(t, n, i.node(s), i.before(s), o, !1),
            )
        ) {
            return !0;
        }
    }
    return !1;
}
function bn(t, e, n) {
    if ((t.focused || t.focus(), t.state.selection.eq(e))) return;
    const r = t.state.tr.setSelection(e);
    (n == "pointer" && r.setMeta("pointer", !0), t.dispatch(r));
}
function Jm(t, e) {
    if (e == -1) return !1;
    const n = t.state.doc.resolve(e);
    const r = n.nodeAfter;
    return r && r.isAtom && P.isSelectable(r)
        ? (bn(t, new P(n), "pointer"), !0)
        : !1;
}
function Gm(t, e) {
    if (e == -1) return !1;
    const n = t.state.selection;
    let r;
    let o;
    n instanceof P && (r = n.node);
    const i = t.state.doc.resolve(e);
    for (let s = i.depth + 1; s > 0; s--) {
        const l = s > i.depth ? i.nodeAfter : i.node(s);
        if (P.isSelectable(l)) {
            r &&
            n.$from.depth > 0 &&
            s >= n.$from.depth &&
            i.before(n.$from.depth + 1) == n.$from.pos
                ? (o = i.before(n.$from.depth))
                : (o = i.before(s));
            break;
        }
    }
    return o != null ? (bn(t, P.create(t.state.doc, o), "pointer"), !0) : !1;
}
function Xm(t, e, n, r, o) {
    return (
        Ps(t, "handleClickOn", e, n, r) ||
        t.someProp("handleClick", (i) => i(t, e, r)) ||
        (o ? Gm(t, n) : Jm(t, n))
    );
}
function Ym(t, e, n, r) {
    return (
        Ps(t, "handleDoubleClickOn", e, n, r) ||
        t.someProp("handleDoubleClick", (o) => o(t, e, r))
    );
}
function Qm(t, e, n, r) {
    return (
        Ps(t, "handleTripleClickOn", e, n, r) ||
        t.someProp("handleTripleClick", (o) => o(t, e, r)) ||
        Zm(t, n, r)
    );
}
function Zm(t, e, n) {
    if (n.button != 0) return !1;
    const r = t.state.doc;
    if (e == -1) {
        return r.inlineContent
            ? (bn(t, R.create(r, 0, r.content.size), "pointer"), !0)
            : !1;
    }
    const o = r.resolve(e);
    for (let i = o.depth + 1; i > 0; i--) {
        const s = i > o.depth ? o.nodeAfter : o.node(i);
        const l = o.before(i);
        if (s.inlineContent) {
            bn(t, R.create(r, l + 1, l + 1 + s.content.size), "pointer");
        } else if (P.isSelectable(s)) bn(t, P.create(r, l), "pointer");
        else continue;
        return !0;
    }
}
function Ls(t) {
    return Wr(t);
}
const yd = Re ? "metaKey" : "ctrlKey";
xe.mousedown = (t, e) => {
    const n = e;
    t.input.shiftKey = n.shiftKey;
    const r = Ls(t);
    const o = Date.now();
    let i = "singleClick";
    (o - t.input.lastClick.time < 500 &&
        qm(n, t.input.lastClick) &&
        !n[yd] &&
        t.input.lastClick.button == n.button &&
        (t.input.lastClick.type == "singleClick"
            ? (i = "doubleClick")
            : t.input.lastClick.type == "doubleClick" && (i = "tripleClick")),
        (t.input.lastClick = {
            time: o,
            x: n.clientX,
            y: n.clientY,
            type: i,
            button: n.button,
        }));
    const s = t.posAtCoords(Gr(n));
    s &&
        (i == "singleClick"
            ? (t.input.mouseDown && t.input.mouseDown.done(),
              (t.input.mouseDown = new Ms(t, s, n, !!r)))
            : (i == "doubleClick" ? Ym : Qm)(t, s.pos, s.inside, n)
              ? n.preventDefault()
              : Tt(t, "pointer"));
};
var Ms = class {
    constructor(e, n, r, o) {
        ((this.view = e),
            (this.pos = n),
            (this.event = r),
            (this.flushed = o),
            (this.delayedSelectionSync = !1),
            (this.mightDrag = null),
            (this.startDoc = e.state.doc),
            (this.selectNode = !!r[yd]),
            (this.allowDefault = r.shiftKey));
        let i, s;
        if (n.inside > -1) ((i = e.state.doc.nodeAt(n.inside)), (s = n.inside));
        else {
            const d = e.state.doc.resolve(n.pos);
            ((i = d.parent), (s = d.depth ? d.before() : 0));
        }
        const l = o ? null : r.target;
        const a = l ? e.docView.nearestDesc(l, !0) : null;
        this.target = a && a.nodeDOM.nodeType == 1 ? a.nodeDOM : null;
        const { selection: c } = e.state;
        (((r.button == 0 &&
            i.type.spec.draggable &&
            i.type.spec.selectable !== !1) ||
            (c instanceof P && c.from <= s && c.to > s)) &&
            (this.mightDrag = {
                node: i,
                pos: s,
                addAttr: !!(this.target && !this.target.draggable),
                setUneditable: !!(
                    this.target &&
                    Ie &&
                    !this.target.hasAttribute("contentEditable")
                ),
            }),
            this.target &&
                this.mightDrag &&
                (this.mightDrag.addAttr || this.mightDrag.setUneditable) &&
                (this.view.domObserver.stop(),
                this.mightDrag.addAttr && (this.target.draggable = !0),
                this.mightDrag.setUneditable &&
                    setTimeout(() => {
                        this.view.input.mouseDown == this &&
                            this.target.setAttribute(
                                "contentEditable",
                                "false",
                            );
                    }, 20),
                this.view.domObserver.start()),
            e.root.addEventListener("mouseup", (this.up = this.up.bind(this))),
            e.root.addEventListener(
                "mousemove",
                (this.move = this.move.bind(this)),
            ),
            Tt(e, "pointer"));
    }

    done() {
        (this.view.root.removeEventListener("mouseup", this.up),
            this.view.root.removeEventListener("mousemove", this.move),
            this.mightDrag &&
                this.target &&
                (this.view.domObserver.stop(),
                this.mightDrag.addAttr &&
                    this.target.removeAttribute("draggable"),
                this.mightDrag.setUneditable &&
                    this.target.removeAttribute("contentEditable"),
                this.view.domObserver.start()),
            this.delayedSelectionSync && setTimeout(() => ft(this.view)),
            (this.view.input.mouseDown = null));
    }

    up(e) {
        if ((this.done(), !this.view.dom.contains(e.target))) return;
        let n = this.pos;
        (this.view.state.doc != this.startDoc &&
            (n = this.view.posAtCoords(Gr(e))),
            this.updateAllowDefault(e),
            this.allowDefault || !n
                ? Tt(this.view, "pointer")
                : Xm(this.view, n.pos, n.inside, e, this.selectNode)
                  ? e.preventDefault()
                  : e.button == 0 &&
                      (this.flushed ||
                          (we &&
                              this.mightDrag &&
                              !this.mightDrag.node.isAtom) ||
                          (ge &&
                              !this.view.state.selection.visible &&
                              Math.min(
                                  Math.abs(
                                      n.pos - this.view.state.selection.from,
                                  ),
                                  Math.abs(
                                      n.pos - this.view.state.selection.to,
                                  ),
                              ) <= 2))
                    ? (bn(
                          this.view,
                          I.near(this.view.state.doc.resolve(n.pos)),
                          "pointer",
                      ),
                      e.preventDefault())
                    : Tt(this.view, "pointer"));
    }

    move(e) {
        (this.updateAllowDefault(e),
            Tt(this.view, "pointer"),
            e.buttons == 0 && this.done());
    }

    updateAllowDefault(e) {
        !this.allowDefault &&
            (Math.abs(this.event.x - e.clientX) > 4 ||
                Math.abs(this.event.y - e.clientY) > 4) &&
            (this.allowDefault = !0);
    }
};
xe.touchstart = (t) => {
    ((t.input.lastTouch = Date.now()), Ls(t), Tt(t, "pointer"));
};
xe.touchmove = (t) => {
    ((t.input.lastTouch = Date.now()), Tt(t, "pointer"));
};
xe.contextmenu = (t) => Ls(t);
function bd(t, e) {
    return t.composing
        ? !0
        : we && Math.abs(e.timeStamp - t.input.compositionEndedAt) < 500
          ? ((t.input.compositionEndedAt = -2e8), !0)
          : !1;
}
const eg = ut ? 5e3 : -1;
ke.compositionstart = ke.compositionupdate = (t) => {
    if (!t.composing) {
        t.domObserver.flush();
        const { state: e } = t;
        const n = e.selection.$to;
        if (
            e.selection instanceof R &&
            (e.storedMarks ||
                (!n.textOffset &&
                    n.parentOffset &&
                    n.nodeBefore.marks.some(
                        (r) => r.type.spec.inclusive === !1,
                    )))
        ) {
            ((t.markCursor = t.state.storedMarks || n.marks()),
                Wr(t, !0),
                (t.markCursor = null));
        } else if (
            (Wr(t, !e.selection.empty),
            Ie &&
                e.selection.empty &&
                n.parentOffset &&
                !n.textOffset &&
                n.nodeBefore.marks.length)
        ) {
            const r = t.domSelectionRange();
            for (
                let o = r.focusNode, i = r.focusOffset;
                o && o.nodeType == 1 && i != 0;

            ) {
                const s = i < 0 ? o.lastChild : o.childNodes[i - 1];
                if (!s) break;
                if (s.nodeType == 3) {
                    const l = t.domSelection();
                    l && l.collapse(s, s.nodeValue.length);
                    break;
                } else ((o = s), (i = -1));
            }
        }
        t.input.composing = !0;
    }
    wd(t, eg);
};
ke.compositionend = (t, e) => {
    t.composing &&
        ((t.input.composing = !1),
        (t.input.compositionEndedAt = e.timeStamp),
        (t.input.compositionPendingChanges = t.domObserver.pendingRecords()
            .length
            ? t.input.compositionID
            : 0),
        (t.input.compositionNode = null),
        t.input.compositionPendingChanges &&
            Promise.resolve().then(() => t.domObserver.flush()),
        t.input.compositionID++,
        wd(t, 20));
};
function wd(t, e) {
    (clearTimeout(t.input.composingTimeout),
        e > -1 && (t.input.composingTimeout = setTimeout(() => Wr(t), e)));
}
function xd(t) {
    for (
        t.composing &&
        ((t.input.composing = !1), (t.input.compositionEndedAt = ng()));
        t.input.compositionNodes.length > 0;

    ) {
        t.input.compositionNodes.pop().markParentsDirty();
    }
}
function tg(t) {
    const e = t.domSelectionRange();
    if (!e.focusNode) return null;
    const n = Qp(e.focusNode, e.focusOffset);
    const r = Zp(e.focusNode, e.focusOffset);
    if (n && r && n != r) {
        const o = r.pmViewDesc;
        const i = t.domObserver.lastChangedTextNode;
        if (n == i || r == i) return i;
        if (!o || !o.isText(r.nodeValue)) return r;
        if (t.input.compositionNode == r) {
            const s = n.pmViewDesc;
            if (!(!s || !s.isText(n.nodeValue))) return r;
        }
    }
    return n || r;
}
function ng() {
    const t = document.createEvent("Event");
    return (t.initEvent("event", !0, !0), t.timeStamp);
}
function Wr(t, e = !1) {
    if (!(ut && t.domObserver.flushingSoon >= 0)) {
        if (
            (t.domObserver.forceFlush(),
            xd(t),
            e || (t.docView && t.docView.dirty))
        ) {
            const n = Os(t);
            const r = t.state.selection;
            return (
                n && !n.eq(r)
                    ? t.dispatch(t.state.tr.setSelection(n))
                    : (t.markCursor || e) &&
                        !r.$from.node(r.$from.sharedDepth(r.to)).inlineContent
                      ? t.dispatch(t.state.tr.deleteSelection())
                      : t.updateState(t.state),
                !0
            );
        }
        return !1;
    }
}
function rg(t, e) {
    if (!t.dom.parentNode) return;
    const n = t.dom.parentNode.appendChild(document.createElement("div"));
    (n.appendChild(e),
        (n.style.cssText = "position: fixed; left: -10000px; top: 10px"));
    const r = getSelection();
    const o = document.createRange();
    (o.selectNodeContents(e),
        t.dom.blur(),
        r.removeAllRanges(),
        r.addRange(o),
        setTimeout(() => {
            (n.parentNode && n.parentNode.removeChild(n), t.focus());
        }, 50));
}
const Un = (Se && At < 15) || (xn && om < 604);
xe.copy = ke.cut = (t, e) => {
    const n = e;
    const r = t.state.selection;
    const o = n.type == "cut";
    if (r.empty) return;
    const i = Un ? null : n.clipboardData;
    const s = r.content();
    const { dom: l, text: a } = Ds(t, s);
    (i
        ? (n.preventDefault(),
          i.clearData(),
          i.setData("text/html", l.innerHTML),
          i.setData("text/plain", a))
        : rg(t, l),
        o &&
            t.dispatch(
                t.state.tr
                    .deleteSelection()
                    .scrollIntoView()
                    .setMeta("uiEvent", "cut"),
            ));
};
function og(t) {
    return t.openStart == 0 && t.openEnd == 0 && t.content.childCount == 1
        ? t.content.firstChild
        : null;
}
function ig(t, e) {
    if (!t.dom.parentNode) return;
    const n = t.input.shiftKey || t.state.selection.$from.parent.type.spec.code;
    const r = t.dom.parentNode.appendChild(
        document.createElement(n ? "textarea" : "div"),
    );
    (n || (r.contentEditable = "true"),
        (r.style.cssText = "position: fixed; left: -10000px; top: 10px"),
        r.focus());
    const o = t.input.shiftKey && t.input.lastKeyCode != 45;
    setTimeout(() => {
        (t.focus(),
            r.parentNode && r.parentNode.removeChild(r),
            n
                ? qn(t, r.value, null, o, e)
                : qn(t, r.textContent, r.innerHTML, o, e));
    }, 50);
}
function qn(t, e, n, r, o) {
    const i = ud(t, e, n, r, t.state.selection.$from);
    if (t.someProp("handlePaste", (a) => a(t, o, i || E.empty))) return !0;
    if (!i) return !1;
    const s = og(i);
    const l = s
        ? t.state.tr.replaceSelectionWith(s, r)
        : t.state.tr.replaceSelection(i);
    return (
        t.dispatch(
            l.scrollIntoView().setMeta("paste", !0).setMeta("uiEvent", "paste"),
        ),
        !0
    );
}
function kd(t) {
    const e = t.getData("text/plain") || t.getData("Text");
    if (e) return e;
    const n = t.getData("text/uri-list");
    return n ? n.replace(/\r?\n/g, " ") : "";
}
ke.paste = (t, e) => {
    const n = e;
    if (t.composing && !ut) return;
    const r = Un ? null : n.clipboardData;
    const o = t.input.shiftKey && t.input.lastKeyCode != 45;
    r && qn(t, kd(r), r.getData("text/html"), o, n)
        ? n.preventDefault()
        : ig(t, n);
};
const jr = class {
    constructor(e, n, r) {
        ((this.slice = e), (this.move = n), (this.node = r));
    }
};
const sg = Re ? "altKey" : "ctrlKey";
function Sd(t, e) {
    const n = t.someProp("dragCopies", (r) => !r(e));
    return n ?? !e[sg];
}
xe.dragstart = (t, e) => {
    const n = e;
    const r = t.input.mouseDown;
    if ((r && r.done(), !n.dataTransfer)) return;
    const o = t.state.selection;
    const i = o.empty ? null : t.posAtCoords(Gr(n));
    let s;
    if (
        !(i && i.pos >= o.from && i.pos <= (o instanceof P ? o.to - 1 : o.to))
    ) {
        if (r && r.mightDrag) s = P.create(t.state.doc, r.mightDrag.pos);
        else if (n.target && n.target.nodeType == 1) {
            const u = t.docView.nearestDesc(n.target, !0);
            u &&
                u.node.type.spec.draggable &&
                u != t.docView &&
                (s = P.create(t.state.doc, u.posBefore));
        }
    }
    const l = (s || t.state.selection).content();
    const { dom: a, text: c, slice: d } = Ds(t, l);
    ((!n.dataTransfer.files.length || !ge || Yc > 120) &&
        n.dataTransfer.clearData(),
        n.dataTransfer.setData(Un ? "Text" : "text/html", a.innerHTML),
        (n.dataTransfer.effectAllowed = "copyMove"),
        Un || n.dataTransfer.setData("text/plain", c),
        (t.dragging = new jr(d, Sd(t, n), s)));
};
xe.dragend = (t) => {
    const e = t.dragging;
    window.setTimeout(() => {
        t.dragging == e && (t.dragging = null);
    }, 50);
};
ke.dragover = ke.dragenter = (t, e) => e.preventDefault();
ke.drop = (t, e) => {
    const n = e;
    const r = t.dragging;
    if (((t.dragging = null), !n.dataTransfer)) return;
    const o = t.posAtCoords(Gr(n));
    if (!o) return;
    const i = t.state.doc.resolve(o.pos);
    let s = r && r.slice;
    s
        ? t.someProp("transformPasted", (p) => {
              s = p(s, t, !1);
          })
        : (s = ud(
              t,
              kd(n.dataTransfer),
              Un ? null : n.dataTransfer.getData("text/html"),
              !1,
              i,
          ));
    const l = !!(r && Sd(t, n));
    if (t.someProp("handleDrop", (p) => p(t, n, s || E.empty, l))) {
        n.preventDefault();
        return;
    }
    if (!s) return;
    n.preventDefault();
    let a = s ? Ir(t.state.doc, i.pos, s) : i.pos;
    a == null && (a = i.pos);
    const c = t.state.tr;
    if (l) {
        const { node: p } = r;
        p ? p.replace(c) : c.deleteSelection();
    }
    const d = c.mapping.map(a);
    const u = s.openStart == 0 && s.openEnd == 0 && s.content.childCount == 1;
    const f = c.doc;
    if (
        (u
            ? c.replaceRangeWith(d, d, s.content.firstChild)
            : c.replaceRange(d, d, s),
        c.doc.eq(f))
    ) {
        return;
    }
    const h = c.doc.resolve(d);
    if (
        u &&
        P.isSelectable(s.content.firstChild) &&
        h.nodeAfter &&
        h.nodeAfter.sameMarkup(s.content.firstChild)
    ) {
        c.setSelection(new P(h));
    } else {
        let p = c.mapping.map(a);
        (c.mapping.maps[c.mapping.maps.length - 1].forEach(
            (m, g, y, b) => (p = b),
        ),
            c.setSelection(Rs(t, h, c.doc.resolve(p))));
    }
    (t.focus(), t.dispatch(c.setMeta("uiEvent", "drop")));
};
xe.focus = (t) => {
    ((t.input.lastFocus = Date.now()),
        t.focused ||
            (t.domObserver.stop(),
            t.dom.classList.add("ProseMirror-focused"),
            t.domObserver.start(),
            (t.focused = !0),
            setTimeout(() => {
                t.docView &&
                    t.hasFocus() &&
                    !t.domObserver.currentSelection.eq(t.domSelectionRange()) &&
                    ft(t);
            }, 20)));
};
xe.blur = (t, e) => {
    const n = e;
    t.focused &&
        (t.domObserver.stop(),
        t.dom.classList.remove("ProseMirror-focused"),
        t.domObserver.start(),
        n.relatedTarget &&
            t.dom.contains(n.relatedTarget) &&
            t.domObserver.currentSelection.clear(),
        (t.focused = !1));
};
xe.beforeinput = (t, e) => {
    if (ge && ut && e.inputType == "deleteContentBackward") {
        t.domObserver.flushSoon();
        const { domChangeCount: r } = t.input;
        setTimeout(() => {
            if (
                t.input.domChangeCount != r ||
                (t.dom.blur(),
                t.focus(),
                t.someProp("handleKeyDown", (i) => i(t, Vt(8, "Backspace"))))
            ) {
                return;
            }
            const { $cursor: o } = t.state.selection;
            o &&
                o.pos > 0 &&
                t.dispatch(
                    t.state.tr.delete(o.pos - 1, o.pos).scrollIntoView(),
                );
        }, 50);
    }
};
for (const t in ke) xe[t] = ke[t];
function Jn(t, e) {
    if (t == e) return !0;
    for (const n in t) if (t[n] !== e[n]) return !1;
    for (const n in e) if (!(n in t)) return !1;
    return !0;
}
const Kr = class t {
    constructor(e, n) {
        ((this.toDOM = e),
            (this.spec = n || Ut),
            (this.side = this.spec.side || 0));
    }

    map(e, n, r, o) {
        const { pos: i, deleted: s } = e.mapResult(
            n.from + o,
            this.side < 0 ? -1 : 1,
        );
        return s ? null : new Q(i - r, i - r, this);
    }

    valid() {
        return !0;
    }

    eq(e) {
        return (
            this == e ||
            (e instanceof t &&
                ((this.spec.key && this.spec.key == e.spec.key) ||
                    (this.toDOM == e.toDOM && Jn(this.spec, e.spec))))
        );
    }

    destroy(e) {
        this.spec.destroy && this.spec.destroy(e);
    }
};
const Kt = class t {
    constructor(e, n) {
        ((this.attrs = e), (this.spec = n || Ut));
    }

    map(e, n, r, o) {
        const i = e.map(n.from + o, this.spec.inclusiveStart ? -1 : 1) - r;
        const s = e.map(n.to + o, this.spec.inclusiveEnd ? 1 : -1) - r;
        return i >= s ? null : new Q(i, s, this);
    }

    valid(e, n) {
        return n.from < n.to;
    }

    eq(e) {
        return (
            this == e ||
            (e instanceof t && Jn(this.attrs, e.attrs) && Jn(this.spec, e.spec))
        );
    }

    static is(e) {
        return e.type instanceof t;
    }

    destroy() {}
};
const Ts = class t {
    constructor(e, n) {
        ((this.attrs = e), (this.spec = n || Ut));
    }

    map(e, n, r, o) {
        const i = e.mapResult(n.from + o, 1);
        if (i.deleted) return null;
        const s = e.mapResult(n.to + o, -1);
        return s.deleted || s.pos <= i.pos
            ? null
            : new Q(i.pos - r, s.pos - r, this);
    }

    valid(e, n) {
        const { index: r, offset: o } = e.content.findIndex(n.from);
        let i;
        return (
            o == n.from && !(i = e.child(r)).isText && o + i.nodeSize == n.to
        );
    }

    eq(e) {
        return (
            this == e ||
            (e instanceof t && Jn(this.attrs, e.attrs) && Jn(this.spec, e.spec))
        );
    }

    destroy() {}
};
var Q = class t {
    constructor(e, n, r) {
        ((this.from = e), (this.to = n), (this.type = r));
    }

    copy(e, n) {
        return new t(e, n, this.type);
    }

    eq(e, n = 0) {
        return (
            this.type.eq(e.type) &&
            this.from + n == e.from &&
            this.to + n == e.to
        );
    }

    map(e, n, r) {
        return this.type.map(e, this, n, r);
    }

    static widget(e, n, r) {
        return new t(e, e, new Kr(n, r));
    }

    static inline(e, n, r, o) {
        return new t(e, n, new Kt(r, o));
    }

    static node(e, n, r, o) {
        return new t(e, n, new Ts(r, o));
    }

    get spec() {
        return this.type.spec;
    }

    get inline() {
        return this.type instanceof Kt;
    }

    get widget() {
        return this.type instanceof Kr;
    }
};
const yn = [];
var Ut = {};
const X = class t {
    constructor(e, n) {
        ((this.local = e.length ? e : yn), (this.children = n.length ? n : yn));
    }

    static create(e, n) {
        return n.length ? qr(n, e, 0, Ut) : me;
    }

    find(e, n, r) {
        const o = [];
        return (this.findInner(e ?? 0, n ?? 1e9, o, 0, r), o);
    }

    findInner(e, n, r, o, i) {
        for (let s = 0; s < this.local.length; s++) {
            const l = this.local[s];
            l.from <= n &&
                l.to >= e &&
                (!i || i(l.spec)) &&
                r.push(l.copy(l.from + o, l.to + o));
        }
        for (let s = 0; s < this.children.length; s += 3) {
            if (this.children[s] < n && this.children[s + 1] > e) {
                const l = this.children[s] + 1;
                this.children[s + 2].findInner(e - l, n - l, r, o + l, i);
            }
        }
    }

    map(e, n, r) {
        return this == me || e.maps.length == 0
            ? this
            : this.mapInner(e, n, 0, 0, r || Ut);
    }

    mapInner(e, n, r, o, i) {
        let s;
        for (let l = 0; l < this.local.length; l++) {
            const a = this.local[l].map(e, r, o);
            a && a.type.valid(n, a)
                ? (s || (s = [])).push(a)
                : i.onRemove && i.onRemove(this.local[l].spec);
        }
        return this.children.length
            ? lg(this.children, s || [], e, n, r, o, i)
            : s
              ? new t(s.sort(qt), yn)
              : me;
    }

    add(e, n) {
        return n.length
            ? this == me
                ? t.create(e, n)
                : this.addInner(e, n, 0)
            : this;
    }

    addInner(e, n, r) {
        let o;
        let i = 0;
        e.forEach((l, a) => {
            const c = a + r;
            let d;
            if ((d = vd(n, l, c))) {
                for (
                    o || (o = this.children.slice());
                    i < o.length && o[i] < a;

                ) {
                    i += 3;
                }
                (o[i] == a
                    ? (o[i + 2] = o[i + 2].addInner(l, d, c + 1))
                    : o.splice(i, 0, a, a + l.nodeSize, qr(d, l, c + 1, Ut)),
                    (i += 3));
            }
        });
        const s = Cd(i ? Md(n) : n, -r);
        for (let l = 0; l < s.length; l++) {
            s[l].type.valid(e, s[l]) || s.splice(l--, 1);
        }
        return new t(
            s.length ? this.local.concat(s).sort(qt) : this.local,
            o || this.children,
        );
    }

    remove(e) {
        return e.length == 0 || this == me ? this : this.removeInner(e, 0);
    }

    removeInner(e, n) {
        let r = this.children;
        let o = this.local;
        for (let i = 0; i < r.length; i += 3) {
            let s;
            const l = r[i] + n;
            const a = r[i + 1] + n;
            for (let d = 0, u; d < e.length; d++) {
                (u = e[d]) &&
                    u.from > l &&
                    u.to < a &&
                    ((e[d] = null), (s || (s = [])).push(u));
            }
            if (!s) continue;
            r == this.children && (r = this.children.slice());
            const c = r[i + 2].removeInner(s, l + 1);
            c != me ? (r[i + 2] = c) : (r.splice(i, 3), (i -= 3));
        }
        if (o.length) {
            for (let i = 0, s; i < e.length; i++) {
                if ((s = e[i])) {
                    for (let l = 0; l < o.length; l++) {
                        o[l].eq(s, n) &&
                            (o == this.local && (o = this.local.slice()),
                            o.splice(l--, 1));
                    }
                }
            }
        }
        return r == this.children && o == this.local
            ? this
            : o.length || r.length
              ? new t(o, r)
              : me;
    }

    forChild(e, n) {
        if (this == me) return this;
        if (n.isLeaf) return t.empty;
        let r, o;
        for (let l = 0; l < this.children.length; l += 3) {
            if (this.children[l] >= e) {
                this.children[l] == e && (r = this.children[l + 2]);
                break;
            }
        }
        const i = e + 1;
        const s = i + n.content.size;
        for (let l = 0; l < this.local.length; l++) {
            const a = this.local[l];
            if (a.from < s && a.to > i && a.type instanceof Kt) {
                const c = Math.max(i, a.from) - i;
                const d = Math.min(s, a.to) - i;
                c < d && (o || (o = [])).push(a.copy(c, d));
            }
        }
        if (o) {
            const l = new t(o.sort(qt), yn);
            return r ? new Ur([l, r]) : l;
        }
        return r || me;
    }

    eq(e) {
        if (this == e) return !0;
        if (
            !(e instanceof t) ||
            this.local.length != e.local.length ||
            this.children.length != e.children.length
        ) {
            return !1;
        }
        for (let n = 0; n < this.local.length; n++) {
            if (!this.local[n].eq(e.local[n])) return !1;
        }
        for (let n = 0; n < this.children.length; n += 3) {
            if (
                this.children[n] != e.children[n] ||
                this.children[n + 1] != e.children[n + 1] ||
                !this.children[n + 2].eq(e.children[n + 2])
            ) {
                return !1;
            }
        }
        return !0;
    }

    locals(e) {
        return zs(this.localsInner(e));
    }

    localsInner(e) {
        if (this == me) return yn;
        if (e.inlineContent || !this.local.some(Kt.is)) return this.local;
        const n = [];
        for (let r = 0; r < this.local.length; r++) {
            this.local[r].type instanceof Kt || n.push(this.local[r]);
        }
        return n;
    }

    forEachSet(e) {
        e(this);
    }
};
X.empty = new X([], []);
X.removeOverlap = zs;
var me = X.empty;
var Ur = class t {
    constructor(e) {
        this.members = e;
    }

    map(e, n) {
        const r = this.members.map((o) => o.map(e, n, Ut));
        return t.from(r);
    }

    forChild(e, n) {
        if (n.isLeaf) return X.empty;
        let r = [];
        for (let o = 0; o < this.members.length; o++) {
            const i = this.members[o].forChild(e, n);
            i != me && (i instanceof t ? (r = r.concat(i.members)) : r.push(i));
        }
        return t.from(r);
    }

    eq(e) {
        if (!(e instanceof t) || e.members.length != this.members.length) {
            return !1;
        }
        for (let n = 0; n < this.members.length; n++) {
            if (!this.members[n].eq(e.members[n])) return !1;
        }
        return !0;
    }

    locals(e) {
        let n;
        let r = !0;
        for (let o = 0; o < this.members.length; o++) {
            const i = this.members[o].localsInner(e);
            if (i.length) {
                if (!n) n = i;
                else {
                    r && ((n = n.slice()), (r = !1));
                    for (let s = 0; s < i.length; s++) n.push(i[s]);
                }
            }
        }
        return n ? zs(r ? n : n.sort(qt)) : yn;
    }

    static from(e) {
        switch (e.length) {
            case 0:
                return me;
            case 1:
                return e[0];
            default:
                return new t(
                    e.every((n) => n instanceof X)
                        ? e
                        : e.reduce(
                              (n, r) =>
                                  n.concat(r instanceof X ? r : r.members),
                              [],
                          ),
                );
        }
    }

    forEachSet(e) {
        for (let n = 0; n < this.members.length; n++) {
            this.members[n].forEachSet(e);
        }
    }
};
function lg(t, e, n, r, o, i, s) {
    const l = t.slice();
    for (let c = 0, d = i; c < n.maps.length; c++) {
        let u = 0;
        (n.maps[c].forEach((f, h, p, m) => {
            const g = m - p - (h - f);
            for (let y = 0; y < l.length; y += 3) {
                const b = l[y + 1];
                if (b < 0 || f > b + d - u) continue;
                const k = l[y] + d - u;
                h >= k
                    ? (l[y + 1] = f <= k ? -2 : -1)
                    : f >= d && g && ((l[y] += g), (l[y + 1] += g));
            }
            u += g;
        }),
            (d = n.maps[c].map(d, -1)));
    }
    let a = !1;
    for (let c = 0; c < l.length; c += 3) {
        if (l[c + 1] < 0) {
            if (l[c + 1] == -2) {
                ((a = !0), (l[c + 1] = -1));
                continue;
            }
            const d = n.map(t[c] + i);
            const u = d - o;
            if (u < 0 || u >= r.content.size) {
                a = !0;
                continue;
            }
            const f = n.map(t[c + 1] + i, -1);
            const h = f - o;
            const { index: p, offset: m } = r.content.findIndex(u);
            const g = r.maybeChild(p);
            if (g && m == u && m + g.nodeSize == h) {
                const y = l[c + 2].mapInner(n, g, d + 1, t[c] + i + 1, s);
                y != me
                    ? ((l[c] = u), (l[c + 1] = h), (l[c + 2] = y))
                    : ((l[c + 1] = -2), (a = !0));
            } else a = !0;
        }
    }
    if (a) {
        const c = ag(l, t, e, n, o, i, s);
        const d = qr(c, r, 0, s);
        e = d.local;
        for (let u = 0; u < l.length; u += 3) {
            l[u + 1] < 0 && (l.splice(u, 3), (u -= 3));
        }
        for (let u = 0, f = 0; u < d.children.length; u += 3) {
            const h = d.children[u];
            for (; f < l.length && l[f] < h; ) f += 3;
            l.splice(f, 0, d.children[u], d.children[u + 1], d.children[u + 2]);
        }
    }
    return new X(e.sort(qt), l);
}
function Cd(t, e) {
    if (!e || !t.length) return t;
    const n = [];
    for (let r = 0; r < t.length; r++) {
        const o = t[r];
        n.push(new Q(o.from + e, o.to + e, o.type));
    }
    return n;
}
function ag(t, e, n, r, o, i, s) {
    function l(a, c) {
        for (let d = 0; d < a.local.length; d++) {
            const u = a.local[d].map(r, o, c);
            u ? n.push(u) : s.onRemove && s.onRemove(a.local[d].spec);
        }
        for (let d = 0; d < a.children.length; d += 3) {
            l(a.children[d + 2], a.children[d] + c + 1);
        }
    }
    for (let a = 0; a < t.length; a += 3) {
        t[a + 1] == -1 && l(t[a + 2], e[a] + i + 1);
    }
    return n;
}
function vd(t, e, n) {
    if (e.isLeaf) return null;
    const r = n + e.nodeSize;
    let o = null;
    for (let i = 0, s; i < t.length; i++) {
        (s = t[i]) &&
            s.from > n &&
            s.to < r &&
            ((o || (o = [])).push(s), (t[i] = null));
    }
    return o;
}
function Md(t) {
    const e = [];
    for (let n = 0; n < t.length; n++) t[n] != null && e.push(t[n]);
    return e;
}
function qr(t, e, n, r) {
    const o = [];
    let i = !1;
    e.forEach((l, a) => {
        const c = vd(t, l, a + n);
        if (c) {
            i = !0;
            const d = qr(c, l, n + a + 1, r);
            d != me && o.push(a, a + l.nodeSize, d);
        }
    });
    const s = Cd(i ? Md(t) : t, -n).sort(qt);
    for (let l = 0; l < s.length; l++) {
        s[l].type.valid(e, s[l]) ||
            (r.onRemove && r.onRemove(s[l].spec), s.splice(l--, 1));
    }
    return s.length || o.length ? new X(s, o) : me;
}
function qt(t, e) {
    return t.from - e.from || t.to - e.to;
}
function zs(t) {
    let e = t;
    for (let n = 0; n < e.length - 1; n++) {
        const r = e[n];
        if (r.from != r.to) {
            for (let o = n + 1; o < e.length; o++) {
                const i = e[o];
                if (i.from == r.from) {
                    i.to != r.to &&
                        (e == t && (e = t.slice()),
                        (e[o] = i.copy(i.from, r.to)),
                        $c(e, o + 1, i.copy(r.to, i.to)));
                    continue;
                } else {
                    i.from < r.to &&
                        (e == t && (e = t.slice()),
                        (e[n] = r.copy(r.from, i.from)),
                        $c(e, o, r.copy(i.from, r.to)));
                    break;
                }
            }
        }
    }
    return e;
}
function $c(t, e, n) {
    for (; e < t.length && qt(n, t[e]) > 0; ) e++;
    t.splice(e, 0, n);
}
function cs(t) {
    const e = [];
    return (
        t.someProp("decorations", (n) => {
            const r = n(t.state);
            r && r != me && e.push(r);
        }),
        t.cursorWrapper &&
            e.push(X.create(t.state.doc, [t.cursorWrapper.deco])),
        Ur.from(e)
    );
}
const cg = {
    childList: !0,
    characterData: !0,
    characterDataOldValue: !0,
    attributes: !0,
    attributeOldValue: !0,
    subtree: !0,
};
const dg = Se && At <= 11;
const As = class {
    constructor() {
        ((this.anchorNode = null),
            (this.anchorOffset = 0),
            (this.focusNode = null),
            (this.focusOffset = 0));
    }

    set(e) {
        ((this.anchorNode = e.anchorNode),
            (this.anchorOffset = e.anchorOffset),
            (this.focusNode = e.focusNode),
            (this.focusOffset = e.focusOffset));
    }

    clear() {
        this.anchorNode = this.focusNode = null;
    }

    eq(e) {
        return (
            e.anchorNode == this.anchorNode &&
            e.anchorOffset == this.anchorOffset &&
            e.focusNode == this.focusNode &&
            e.focusOffset == this.focusOffset
        );
    }
};
const Es = class {
    constructor(e, n) {
        ((this.view = e),
            (this.handleDOMChange = n),
            (this.queue = []),
            (this.flushingSoon = -1),
            (this.observer = null),
            (this.currentSelection = new As()),
            (this.onCharData = null),
            (this.suppressingSelectionUpdates = !1),
            (this.lastChangedTextNode = null),
            (this.observer =
                window.MutationObserver &&
                new window.MutationObserver((r) => {
                    for (let o = 0; o < r.length; o++) this.queue.push(r[o]);
                    Se &&
                    At <= 11 &&
                    r.some(
                        (o) =>
                            (o.type == "childList" && o.removedNodes.length) ||
                            (o.type == "characterData" &&
                                o.oldValue.length > o.target.nodeValue.length),
                    )
                        ? this.flushSoon()
                        : this.flush();
                })),
            dg &&
                (this.onCharData = (r) => {
                    (this.queue.push({
                        target: r.target,
                        type: "characterData",
                        oldValue: r.prevValue,
                    }),
                        this.flushSoon());
                }),
            (this.onSelectionChange = this.onSelectionChange.bind(this)));
    }

    flushSoon() {
        this.flushingSoon < 0 &&
            (this.flushingSoon = window.setTimeout(() => {
                ((this.flushingSoon = -1), this.flush());
            }, 20));
    }

    forceFlush() {
        this.flushingSoon > -1 &&
            (window.clearTimeout(this.flushingSoon),
            (this.flushingSoon = -1),
            this.flush());
    }

    start() {
        (this.observer &&
            (this.observer.takeRecords(),
            this.observer.observe(this.view.dom, cg)),
            this.onCharData &&
                this.view.dom.addEventListener(
                    "DOMCharacterDataModified",
                    this.onCharData,
                ),
            this.connectSelection());
    }

    stop() {
        if (this.observer) {
            const e = this.observer.takeRecords();
            if (e.length) {
                for (let n = 0; n < e.length; n++) this.queue.push(e[n]);
                window.setTimeout(() => this.flush(), 20);
            }
            this.observer.disconnect();
        }
        (this.onCharData &&
            this.view.dom.removeEventListener(
                "DOMCharacterDataModified",
                this.onCharData,
            ),
            this.disconnectSelection());
    }

    connectSelection() {
        this.view.dom.ownerDocument.addEventListener(
            "selectionchange",
            this.onSelectionChange,
        );
    }

    disconnectSelection() {
        this.view.dom.ownerDocument.removeEventListener(
            "selectionchange",
            this.onSelectionChange,
        );
    }

    suppressSelectionUpdates() {
        ((this.suppressingSelectionUpdates = !0),
            setTimeout(() => (this.suppressingSelectionUpdates = !1), 50));
    }

    onSelectionChange() {
        if (Rc(this.view)) {
            if (this.suppressingSelectionUpdates) return ft(this.view);
            if (Se && At <= 11 && !this.view.state.selection.empty) {
                const e = this.view.domSelectionRange();
                if (
                    e.focusNode &&
                    Jt(e.focusNode, e.focusOffset, e.anchorNode, e.anchorOffset)
                ) {
                    return this.flushSoon();
                }
            }
            this.flush();
        }
    }

    setCurSelection() {
        this.currentSelection.set(this.view.domSelectionRange());
    }

    ignoreSelectionChange(e) {
        if (!e.focusNode) return !0;
        const n = new Set();
        let r;
        for (let i = e.focusNode; i; i = wn(i)) n.add(i);
        for (let i = e.anchorNode; i; i = wn(i)) {
            if (n.has(i)) {
                r = i;
                break;
            }
        }
        const o = r && this.view.docView.nearestDesc(r);
        if (
            o &&
            o.ignoreMutation({
                type: "selection",
                target: r.nodeType == 3 ? r.parentNode : r,
            })
        ) {
            return (this.setCurSelection(), !0);
        }
    }

    pendingRecords() {
        if (this.observer) {
            for (const e of this.observer.takeRecords()) this.queue.push(e);
        }
        return this.queue;
    }

    flush() {
        const { view: e } = this;
        if (!e.docView || this.flushingSoon > -1) return;
        const n = this.pendingRecords();
        n.length && (this.queue = []);
        const r = e.domSelectionRange();
        const o =
            !this.suppressingSelectionUpdates &&
            !this.currentSelection.eq(r) &&
            Rc(e) &&
            !this.ignoreSelectionChange(r);
        let i = -1;
        let s = -1;
        let l = !1;
        const a = [];
        if (e.editable) {
            for (let d = 0; d < n.length; d++) {
                const u = this.registerMutation(n[d], a);
                u &&
                    ((i = i < 0 ? u.from : Math.min(u.from, i)),
                    (s = s < 0 ? u.to : Math.max(u.to, s)),
                    u.typeOver && (l = !0));
            }
        }
        if (Ie && a.length) {
            const d = a.filter((u) => u.nodeName == "BR");
            if (d.length == 2) {
                const [u, f] = d;
                u.parentNode && u.parentNode.parentNode == f.parentNode
                    ? f.remove()
                    : u.remove();
            } else {
                const { focusNode: u } = this.currentSelection;
                for (const f of d) {
                    const h = f.parentNode;
                    h &&
                        h.nodeName == "LI" &&
                        (!u || hg(e, u) != h) &&
                        f.remove();
                }
            }
        }
        let c = null;
        i < 0 &&
        o &&
        e.input.lastFocus > Date.now() - 200 &&
        Math.max(e.input.lastTouch, e.input.lastClick.time) <
            Date.now() - 300 &&
        Jr(r) &&
        (c = Os(e)) &&
        c.eq(I.near(e.state.doc.resolve(0), 1))
            ? ((e.input.lastFocus = 0),
              ft(e),
              this.currentSelection.set(r),
              e.scrollToSelection())
            : (i > -1 || o) &&
              (i > -1 && (e.docView.markDirty(i, s), ug(e)),
              this.handleDOMChange(i, s, l, a),
              e.docView && e.docView.dirty
                  ? e.updateState(e.state)
                  : this.currentSelection.eq(r) || ft(e),
              this.currentSelection.set(r));
    }

    registerMutation(e, n) {
        if (n.indexOf(e.target) > -1) return null;
        const r = this.view.docView.nearestDesc(e.target);
        if (
            (e.type == "attributes" &&
                (r == this.view.docView ||
                    e.attributeName == "contenteditable" ||
                    (e.attributeName == "style" &&
                        !e.oldValue &&
                        !e.target.getAttribute("style")))) ||
            !r ||
            r.ignoreMutation(e)
        ) {
            return null;
        }
        if (e.type == "childList") {
            for (let d = 0; d < e.addedNodes.length; d++) {
                const u = e.addedNodes[d];
                (n.push(u), u.nodeType == 3 && (this.lastChangedTextNode = u));
            }
            if (
                r.contentDOM &&
                r.contentDOM != r.dom &&
                !r.contentDOM.contains(e.target)
            ) {
                return { from: r.posBefore, to: r.posAfter };
            }
            let o = e.previousSibling;
            let i = e.nextSibling;
            if (Se && At <= 11 && e.addedNodes.length) {
                for (let d = 0; d < e.addedNodes.length; d++) {
                    const { previousSibling: u, nextSibling: f } =
                        e.addedNodes[d];
                    ((!u ||
                        Array.prototype.indexOf.call(e.addedNodes, u) < 0) &&
                        (o = u),
                        (!f ||
                            Array.prototype.indexOf.call(e.addedNodes, f) <
                                0) &&
                            (i = f));
                }
            }
            const s = o && o.parentNode == e.target ? de(o) + 1 : 0;
            const l = r.localPosFromDOM(e.target, s, -1);
            const a =
                i && i.parentNode == e.target
                    ? de(i)
                    : e.target.childNodes.length;
            const c = r.localPosFromDOM(e.target, a, 1);
            return { from: l, to: c };
        } else {
            return e.type == "attributes"
                ? { from: r.posAtStart - r.border, to: r.posAtEnd + r.border }
                : ((this.lastChangedTextNode = e.target),
                  {
                      from: r.posAtStart,
                      to: r.posAtEnd,
                      typeOver: e.target.nodeValue == e.oldValue,
                  });
        }
    }
};
const Fc = new WeakMap();
let _c = !1;
function ug(t) {
    if (
        !Fc.has(t) &&
        (Fc.set(t, null),
        ["normal", "nowrap", "pre-line"].indexOf(
            getComputedStyle(t.dom).whiteSpace,
        ) !== -1)
    ) {
        if (((t.requiresGeckoHackNode = Ie), _c)) return;
        (console.warn(
            "ProseMirror expects the CSS white-space property to be set, preferably to 'pre-wrap'. It is recommended to load style/prosemirror.css from the prosemirror-view package.",
        ),
            (_c = !0));
    }
}
function Vc(t, e) {
    let n = e.startContainer;
    let r = e.startOffset;
    let o = e.endContainer;
    let i = e.endOffset;
    const s = t.domAtPos(t.state.selection.anchor);
    return (
        Jt(s.node, s.offset, o, i) && ([n, r, o, i] = [o, i, n, r]),
        { anchorNode: n, anchorOffset: r, focusNode: o, focusOffset: i }
    );
}
function fg(t, e) {
    if (e.getComposedRanges) {
        const o = e.getComposedRanges(t.root)[0];
        if (o) return Vc(t, o);
    }
    let n;
    function r(o) {
        (o.preventDefault(),
            o.stopImmediatePropagation(),
            (n = o.getTargetRanges()[0]));
    }
    return (
        t.dom.addEventListener("beforeinput", r, !0),
        document.execCommand("indent"),
        t.dom.removeEventListener("beforeinput", r, !0),
        n ? Vc(t, n) : null
    );
}
function hg(t, e) {
    for (let n = e.parentNode; n && n != t.dom; n = n.parentNode) {
        const r = t.docView.nearestDesc(n, !0);
        if (r && r.node.isBlock) return n;
    }
    return null;
}
function pg(t, e, n) {
    let {
        node: r,
        fromOffset: o,
        toOffset: i,
        from: s,
        to: l,
    } = t.docView.parseRange(e, n);
    const a = t.domSelectionRange();
    let c;
    const d = a.anchorNode;
    if (
        (d &&
            t.dom.contains(d.nodeType == 1 ? d : d.parentNode) &&
            ((c = [{ node: d, offset: a.anchorOffset }]),
            Jr(a) || c.push({ node: a.focusNode, offset: a.focusOffset })),
        ge && t.input.lastKeyCode === 8)
    ) {
        for (let g = i; g > o; g--) {
            const y = r.childNodes[g - 1];
            const b = y.pmViewDesc;
            if (y.nodeName == "BR" && !b) {
                i = g;
                break;
            }
            if (!b || b.size) break;
        }
    }
    const u = t.state.doc;
    const f = t.someProp("domParser") || Ue.fromSchema(t.state.schema);
    const h = u.resolve(s);
    let p = null;
    const m = f.parse(r, {
        topNode: h.parent,
        topMatch: h.parent.contentMatchAt(h.index()),
        topOpen: !0,
        from: o,
        to: i,
        preserveWhitespace: h.parent.type.whitespace == "pre" ? "full" : !0,
        findPositions: c,
        ruleFromNode: mg,
        context: h,
    });
    if (c && c[0].pos != null) {
        const g = c[0].pos;
        let y = c[1] && c[1].pos;
        (y == null && (y = g), (p = { anchor: g + s, head: y + s }));
    }
    return { doc: m, sel: p, from: s, to: l };
}
function mg(t) {
    const e = t.pmViewDesc;
    if (e) return e.parseRule();
    if (t.nodeName == "BR" && t.parentNode) {
        if (we && /^(ul|ol)$/i.test(t.parentNode.nodeName)) {
            const n = document.createElement("div");
            return (n.appendChild(document.createElement("li")), { skip: n });
        } else if (
            t.parentNode.lastChild == t ||
            (we && /^(tr|table)$/i.test(t.parentNode.nodeName))
        ) {
            return { ignore: !0 };
        }
    } else if (t.nodeName == "IMG" && t.getAttribute("mark-placeholder")) {
        return { ignore: !0 };
    }
    return null;
}
const gg =
    /^(a|abbr|acronym|b|bd[io]|big|br|button|cite|code|data(list)?|del|dfn|em|i|img|ins|kbd|label|map|mark|meter|output|q|ruby|s|samp|small|span|strong|su[bp]|time|u|tt|var)$/i;
function yg(t, e, n, r, o) {
    const i =
        t.input.compositionPendingChanges ||
        (t.composing ? t.input.compositionID : 0);
    if (((t.input.compositionPendingChanges = 0), e < 0)) {
        const w =
            t.input.lastSelectionTime > Date.now() - 50
                ? t.input.lastSelectionOrigin
                : null;
        const N = Os(t, w);
        if (N && !t.state.selection.eq(N)) {
            if (
                ge &&
                ut &&
                t.input.lastKeyCode === 13 &&
                Date.now() - 100 < t.input.lastKeyCodeTime &&
                t.someProp("handleKeyDown", (M) => M(t, Vt(13, "Enter")))
            ) {
                return;
            }
            const D = t.state.tr.setSelection(N);
            (w == "pointer"
                ? D.setMeta("pointer", !0)
                : w == "key" && D.scrollIntoView(),
                i && D.setMeta("composition", i),
                t.dispatch(D));
        }
        return;
    }
    const s = t.state.doc.resolve(e);
    const l = s.sharedDepth(n);
    ((e = s.before(l + 1)), (n = t.state.doc.resolve(n).after(l + 1)));
    const a = t.state.selection;
    const c = pg(t, e, n);
    const d = t.state.doc;
    const u = d.slice(c.from, c.to);
    let f;
    let h;
    (t.input.lastKeyCode === 8 && Date.now() - 100 < t.input.lastKeyCodeTime
        ? ((f = t.state.selection.to), (h = "end"))
        : ((f = t.state.selection.from), (h = "start")),
        (t.input.lastKeyCode = null));
    let p = xg(u.content, c.doc.content, c.from, f, h);
    if (
        (p && t.input.domChangeCount++,
        ((xn && t.input.lastIOSEnter > Date.now() - 225) || ut) &&
            o.some((w) => w.nodeType == 1 && !gg.test(w.nodeName)) &&
            (!p || p.endA >= p.endB) &&
            t.someProp("handleKeyDown", (w) => w(t, Vt(13, "Enter"))))
    ) {
        t.input.lastIOSEnter = 0;
        return;
    }
    if (!p) {
        if (
            r &&
            a instanceof R &&
            !a.empty &&
            a.$head.sameParent(a.$anchor) &&
            !t.composing &&
            !(c.sel && c.sel.anchor != c.sel.head)
        ) {
            p = { start: a.from, endA: a.to, endB: a.to };
        } else {
            if (c.sel) {
                const w = Wc(t, t.state.doc, c.sel);
                if (w && !w.eq(t.state.selection)) {
                    const N = t.state.tr.setSelection(w);
                    (i && N.setMeta("composition", i), t.dispatch(N));
                }
            }
            return;
        }
    }
    (t.state.selection.from < t.state.selection.to &&
        p.start == p.endB &&
        t.state.selection instanceof R &&
        (p.start > t.state.selection.from &&
        p.start <= t.state.selection.from + 2 &&
        t.state.selection.from >= c.from
            ? (p.start = t.state.selection.from)
            : p.endA < t.state.selection.to &&
              p.endA >= t.state.selection.to - 2 &&
              t.state.selection.to <= c.to &&
              ((p.endB += t.state.selection.to - p.endA),
              (p.endA = t.state.selection.to))),
        Se &&
            At <= 11 &&
            p.endB == p.start + 1 &&
            p.endA == p.start &&
            p.start > c.from &&
            c.doc.textBetween(p.start - c.from - 1, p.start - c.from + 1) ==
                " \xA0" &&
            (p.start--, p.endA--, p.endB--));
    const m = c.doc.resolveNoCache(p.start - c.from);
    let g = c.doc.resolveNoCache(p.endB - c.from);
    const y = d.resolve(p.start);
    const b = m.sameParent(g) && m.parent.inlineContent && y.end() >= p.endA;
    if (
        ((xn &&
            t.input.lastIOSEnter > Date.now() - 225 &&
            (!b || o.some((w) => w.nodeName == "DIV" || w.nodeName == "P"))) ||
            (!b &&
                m.pos < c.doc.content.size &&
                (!m.sameParent(g) || !m.parent.inlineContent) &&
                m.pos < g.pos &&
                !/\S/.test(c.doc.textBetween(m.pos, g.pos, "", "")))) &&
        t.someProp("handleKeyDown", (w) => w(t, Vt(13, "Enter")))
    ) {
        t.input.lastIOSEnter = 0;
        return;
    }
    if (
        t.state.selection.anchor > p.start &&
        wg(d, p.start, p.endA, m, g) &&
        t.someProp("handleKeyDown", (w) => w(t, Vt(8, "Backspace")))
    ) {
        ut && ge && t.domObserver.suppressSelectionUpdates();
        return;
    }
    (ge && p.endB == p.start && (t.input.lastChromeDelete = Date.now()),
        ut &&
            !b &&
            m.start() != g.start() &&
            g.parentOffset == 0 &&
            m.depth == g.depth &&
            c.sel &&
            c.sel.anchor == c.sel.head &&
            c.sel.head == p.endA &&
            ((p.endB -= 2),
            (g = c.doc.resolveNoCache(p.endB - c.from)),
            setTimeout(() => {
                t.someProp("handleKeyDown", function (w) {
                    return w(t, Vt(13, "Enter"));
                });
            }, 20)));
    const k = p.start;
    const v = p.endA;
    const x = (w) => {
        const N =
            w ||
            t.state.tr.replace(
                k,
                v,
                c.doc.slice(p.start - c.from, p.endB - c.from),
            );
        if (c.sel) {
            const D = Wc(t, N.doc, c.sel);
            D &&
                !(
                    (ge &&
                        t.composing &&
                        D.empty &&
                        (p.start != p.endB ||
                            t.input.lastChromeDelete < Date.now() - 100) &&
                        (D.head == k || D.head == N.mapping.map(v) - 1)) ||
                    (Se && D.empty && D.head == k)
                ) &&
                N.setSelection(D);
        }
        return (i && N.setMeta("composition", i), N.scrollIntoView());
    };
    let S;
    if (b) {
        if (m.pos == g.pos) {
            Se &&
                At <= 11 &&
                m.parentOffset == 0 &&
                (t.domObserver.suppressSelectionUpdates(),
                setTimeout(() => ft(t), 20));
            const w = x(t.state.tr.delete(k, v));
            const N = d.resolve(p.start).marksAcross(d.resolve(p.endA));
            (N && w.ensureMarks(N), t.dispatch(w));
        } else if (
            p.endA == p.endB &&
            (S = bg(
                m.parent.content.cut(m.parentOffset, g.parentOffset),
                y.parent.content.cut(y.parentOffset, p.endA - y.start()),
            ))
        ) {
            const w = x(t.state.tr);
            (S.type == "add"
                ? w.addMark(k, v, S.mark)
                : w.removeMark(k, v, S.mark),
                t.dispatch(w));
        } else if (
            m.parent.child(m.index()).isText &&
            m.index() == g.index() - (g.textOffset ? 0 : 1)
        ) {
            const w = m.parent.textBetween(m.parentOffset, g.parentOffset);
            const N = () => x(t.state.tr.insertText(w, k, v));
            t.someProp("handleTextInput", (D) => D(t, k, v, w, N)) ||
                t.dispatch(N());
        } else t.dispatch(x());
    } else t.dispatch(x());
}
function Wc(t, e, n) {
    return Math.max(n.anchor, n.head) > e.content.size
        ? null
        : Rs(t, e.resolve(n.anchor), e.resolve(n.head));
}
function bg(t, e) {
    const n = t.firstChild.marks;
    const r = e.firstChild.marks;
    let o = n;
    let i = r;
    let s;
    let l;
    let a;
    for (let d = 0; d < r.length; d++) o = r[d].removeFromSet(o);
    for (let d = 0; d < n.length; d++) i = n[d].removeFromSet(i);
    if (o.length == 1 && i.length == 0) {
        ((l = o[0]), (s = "add"), (a = (d) => d.mark(l.addToSet(d.marks))));
    } else if (o.length == 0 && i.length == 1) {
        ((l = i[0]),
            (s = "remove"),
            (a = (d) => d.mark(l.removeFromSet(d.marks))));
    } else return null;
    const c = [];
    for (let d = 0; d < e.childCount; d++) c.push(a(e.child(d)));
    if (C.from(c).eq(t)) return { mark: l, type: s };
}
function wg(t, e, n, r, o) {
    if (n - e <= o.pos - r.pos || ds(r, !0, !1) < o.pos) return !1;
    const i = t.resolve(e);
    if (!r.parent.isTextblock) {
        const l = i.nodeAfter;
        return l != null && n == e + l.nodeSize;
    }
    if (i.parentOffset < i.parent.content.size || !i.parent.isTextblock) {
        return !1;
    }
    const s = t.resolve(ds(i, !0, !0));
    return !s.parent.isTextblock || s.pos > n || ds(s, !0, !1) < n
        ? !1
        : r.parent.content.cut(r.parentOffset).eq(s.parent.content);
}
function ds(t, e, n) {
    let r = t.depth;
    let o = e ? t.end() : t.pos;
    for (; r > 0 && (e || t.indexAfter(r) == t.node(r).childCount); ) {
        (r--, o++, (e = !1));
    }
    if (n) {
        let i = t.node(r).maybeChild(t.indexAfter(r));
        for (; i && !i.isLeaf; ) ((i = i.firstChild), o++);
    }
    return o;
}
function xg(t, e, n, r, o) {
    let i = t.findDiffStart(e, n);
    if (i == null) return null;
    let { a: s, b: l } = t.findDiffEnd(e, n + t.size, n + e.size);
    if (o == "end") {
        const a = Math.max(0, i - Math.min(s, l));
        r -= s + a - i;
    }
    if (s < i && t.size < e.size) {
        const a = r <= i && r >= s ? i - r : 0;
        ((i -= a),
            i &&
                i < e.size &&
                jc(e.textBetween(i - 1, i + 1)) &&
                (i += a ? 1 : -1),
            (l = i + (l - s)),
            (s = i));
    } else if (l < i) {
        const a = r <= i && r >= l ? i - r : 0;
        ((i -= a),
            i &&
                i < t.size &&
                jc(t.textBetween(i - 1, i + 1)) &&
                (i += a ? 1 : -1),
            (s = i + (s - l)),
            (l = i));
    }
    return { start: i, endA: s, endB: l };
}
function jc(t) {
    if (t.length != 2) return !1;
    const e = t.charCodeAt(0);
    const n = t.charCodeAt(1);
    return e >= 56320 && e <= 57343 && n >= 55296 && n <= 56319;
}
const Gn = class {
    constructor(e, n) {
        ((this._root = null),
            (this.focused = !1),
            (this.trackWrites = null),
            (this.mounted = !1),
            (this.markCursor = null),
            (this.cursorWrapper = null),
            (this.lastSelectedViewDesc = void 0),
            (this.input = new Cs()),
            (this.prevDirectPlugins = []),
            (this.pluginViews = []),
            (this.requiresGeckoHackNode = !1),
            (this.dragging = null),
            (this._props = n),
            (this.state = n.state),
            (this.directPlugins = n.plugins || []),
            this.directPlugins.forEach(Gc),
            (this.dispatch = this.dispatch.bind(this)),
            (this.dom = (e && e.mount) || document.createElement("div")),
            e &&
                (e.appendChild
                    ? e.appendChild(this.dom)
                    : typeof e === "function"
                      ? e(this.dom)
                      : e.mount && (this.mounted = !0)),
            (this.editable = qc(this)),
            Uc(this),
            (this.nodeViews = Jc(this)),
            (this.docView = Mc(
                this.state.doc,
                Kc(this),
                cs(this),
                this.dom,
                this,
            )),
            (this.domObserver = new Es(this, (r, o, i, s) =>
                yg(this, r, o, i, s),
            )),
            this.domObserver.start(),
            Wm(this),
            this.updatePluginViews());
    }

    get composing() {
        return this.input.composing;
    }

    get props() {
        if (this._props.state != this.state) {
            const e = this._props;
            this._props = {};
            for (const n in e) this._props[n] = e[n];
            this._props.state = this.state;
        }
        return this._props;
    }

    update(e) {
        e.handleDOMEvents != this._props.handleDOMEvents && vs(this);
        const n = this._props;
        ((this._props = e),
            e.plugins &&
                (e.plugins.forEach(Gc), (this.directPlugins = e.plugins)),
            this.updateStateInner(e.state, n));
    }

    setProps(e) {
        const n = {};
        for (const r in this._props) n[r] = this._props[r];
        n.state = this.state;
        for (const r in e) n[r] = e[r];
        this.update(n);
    }

    updateState(e) {
        this.updateStateInner(e, this._props);
    }

    updateStateInner(e, n) {
        let r;
        const o = this.state;
        let i = !1;
        let s = !1;
        (e.storedMarks && this.composing && (xd(this), (s = !0)),
            (this.state = e));
        const l = o.plugins != e.plugins || this._props.plugins != n.plugins;
        if (
            l ||
            this._props.plugins != n.plugins ||
            this._props.nodeViews != n.nodeViews
        ) {
            const h = Jc(this);
            Sg(h, this.nodeViews) && ((this.nodeViews = h), (i = !0));
        }
        ((l || n.handleDOMEvents != this._props.handleDOMEvents) && vs(this),
            (this.editable = qc(this)),
            Uc(this));
        const a = cs(this);
        const c = Kc(this);
        const d =
            o.plugins != e.plugins && !o.doc.eq(e.doc)
                ? "reset"
                : e.scrollToSelection > o.scrollToSelection
                  ? "to selection"
                  : "preserve";
        const u = i || !this.docView.matchesNode(e.doc, c, a);
        (u || !e.selection.eq(o.selection)) && (s = !0);
        const f =
            d == "preserve" &&
            s &&
            this.dom.style.overflowAnchor == null &&
            lm(this);
        if (s) {
            this.domObserver.stop();
            let h =
                u &&
                (Se || ge) &&
                !this.composing &&
                !o.selection.empty &&
                !e.selection.empty &&
                kg(o.selection, e.selection);
            if (u) {
                const p = ge
                    ? (this.trackWrites = this.domSelectionRange().focusNode)
                    : null;
                (this.composing && (this.input.compositionNode = tg(this)),
                    (i || !this.docView.update(e.doc, c, a, this)) &&
                        (this.docView.updateOuterDeco(c),
                        this.docView.destroy(),
                        (this.docView = Mc(e.doc, c, a, this.dom, this))),
                    p && !this.trackWrites && (h = !0));
            }
            (h ||
            !(
                this.input.mouseDown &&
                this.domObserver.currentSelection.eq(
                    this.domSelectionRange(),
                ) &&
                Em(this)
            )
                ? ft(this, h)
                : (ad(this, e.selection), this.domObserver.setCurSelection()),
                this.domObserver.start());
        }
        (this.updatePluginViews(o),
            !((r = this.dragging) === null || r === void 0) &&
                r.node &&
                !o.doc.eq(e.doc) &&
                this.updateDraggedNode(this.dragging, o),
            d == "reset"
                ? (this.dom.scrollTop = 0)
                : d == "to selection"
                  ? this.scrollToSelection()
                  : f && am(f));
    }

    scrollToSelection() {
        const e = this.domSelectionRange().focusNode;
        if (!(!e || !this.dom.contains(e.nodeType == 1 ? e : e.parentNode))) {
            if (!this.someProp("handleScrollToSelection", (n) => n(this))) {
                if (this.state.selection instanceof P) {
                    const n = this.docView.domAfterPos(
                        this.state.selection.from,
                    );
                    n.nodeType == 1 && wc(this, n.getBoundingClientRect(), e);
                } else {
                    wc(this, this.coordsAtPos(this.state.selection.head, 1), e);
                }
            }
        }
    }

    destroyPluginViews() {
        let e;
        for (; (e = this.pluginViews.pop()); ) e.destroy && e.destroy();
    }

    updatePluginViews(e) {
        if (
            !e ||
            e.plugins != this.state.plugins ||
            this.directPlugins != this.prevDirectPlugins
        ) {
            ((this.prevDirectPlugins = this.directPlugins),
                this.destroyPluginViews());
            for (let n = 0; n < this.directPlugins.length; n++) {
                const r = this.directPlugins[n];
                r.spec.view && this.pluginViews.push(r.spec.view(this));
            }
            for (let n = 0; n < this.state.plugins.length; n++) {
                const r = this.state.plugins[n];
                r.spec.view && this.pluginViews.push(r.spec.view(this));
            }
        } else {
            for (let n = 0; n < this.pluginViews.length; n++) {
                const r = this.pluginViews[n];
                r.update && r.update(this, e);
            }
        }
    }

    updateDraggedNode(e, n) {
        const r = e.node;
        let o = -1;
        if (this.state.doc.nodeAt(r.from) == r.node) o = r.from;
        else {
            const i =
                r.from + (this.state.doc.content.size - n.doc.content.size);
            (i > 0 && this.state.doc.nodeAt(i)) == r.node && (o = i);
        }
        this.dragging = new jr(
            e.slice,
            e.move,
            o < 0 ? void 0 : P.create(this.state.doc, o),
        );
    }

    someProp(e, n) {
        const r = this._props && this._props[e];
        let o;
        if (r != null && (o = n ? n(r) : r)) return o;
        for (let s = 0; s < this.directPlugins.length; s++) {
            const l = this.directPlugins[s].props[e];
            if (l != null && (o = n ? n(l) : l)) return o;
        }
        const i = this.state.plugins;
        if (i) {
            for (let s = 0; s < i.length; s++) {
                const l = i[s].props[e];
                if (l != null && (o = n ? n(l) : l)) return o;
            }
        }
    }

    hasFocus() {
        if (Se) {
            let e = this.root.activeElement;
            if (e == this.dom) return !0;
            if (!e || !this.dom.contains(e)) return !1;
            for (; e && this.dom != e && this.dom.contains(e); ) {
                if (e.contentEditable == "false") return !1;
                e = e.parentElement;
            }
            return !0;
        }
        return this.root.activeElement == this.dom;
    }

    focus() {
        (this.domObserver.stop(),
            this.editable && cm(this.dom),
            ft(this),
            this.domObserver.start());
    }

    get root() {
        const e = this._root;
        if (e == null) {
            for (let n = this.dom.parentNode; n; n = n.parentNode) {
                if (n.nodeType == 9 || (n.nodeType == 11 && n.host)) {
                    return (
                        n.getSelection ||
                            (Object.getPrototypeOf(n).getSelection = () =>
                                n.ownerDocument.getSelection()),
                        (this._root = n)
                    );
                }
            }
        }
        return e || document;
    }

    updateRoot() {
        this._root = null;
    }

    posAtCoords(e) {
        return pm(this, e);
    }

    coordsAtPos(e, n = 1) {
        return nd(this, e, n);
    }

    domAtPos(e, n = 0) {
        return this.docView.domFromPos(e, n);
    }

    nodeDOM(e) {
        const n = this.docView.descAt(e);
        return n ? n.nodeDOM : null;
    }

    posAtDOM(e, n, r = -1) {
        const o = this.docView.posFromDOM(e, n, r);
        if (o == null) {
            throw new RangeError("DOM position not inside the editor");
        }
        return o;
    }

    endOfTextblock(e, n) {
        return wm(this, n || this.state, e);
    }

    pasteHTML(e, n) {
        return qn(this, "", e, !1, n || new ClipboardEvent("paste"));
    }

    pasteText(e, n) {
        return qn(this, e, null, !0, n || new ClipboardEvent("paste"));
    }

    serializeForClipboard(e) {
        return Ds(this, e);
    }

    destroy() {
        this.docView &&
            (jm(this),
            this.destroyPluginViews(),
            this.mounted
                ? (this.docView.update(this.state.doc, [], cs(this), this),
                  (this.dom.textContent = ""))
                : this.dom.parentNode &&
                  this.dom.parentNode.removeChild(this.dom),
            this.docView.destroy(),
            (this.docView = null),
            Xp());
    }

    get isDestroyed() {
        return this.docView == null;
    }

    dispatchEvent(e) {
        return Um(this, e);
    }

    domSelectionRange() {
        const e = this.domSelection();
        return e
            ? (we &&
                  this.root.nodeType === 11 &&
                  tm(this.dom.ownerDocument) == this.dom &&
                  fg(this, e)) ||
                  e
            : {
                  focusNode: null,
                  focusOffset: 0,
                  anchorNode: null,
                  anchorOffset: 0,
              };
    }

    domSelection() {
        return this.root.getSelection();
    }
};
Gn.prototype.dispatch = function (t) {
    const e = this._props.dispatchTransaction;
    e ? e.call(this, t) : this.updateState(this.state.apply(t));
};
function Kc(t) {
    const e = Object.create(null);
    return (
        (e.class = "ProseMirror"),
        (e.contenteditable = String(t.editable)),
        t.someProp("attributes", (n) => {
            if ((typeof n === "function" && (n = n(t.state)), n)) {
                for (const r in n) {
                    r == "class"
                        ? (e.class += " " + n[r])
                        : r == "style"
                          ? (e.style = (e.style ? e.style + ";" : "") + n[r])
                          : !e[r] &&
                            r != "contenteditable" &&
                            r != "nodeName" &&
                            (e[r] = String(n[r]));
                }
            }
        }),
        e.translate || (e.translate = "no"),
        [Q.node(0, t.state.doc.content.size, e)]
    );
}
function Uc(t) {
    if (t.markCursor) {
        const e = document.createElement("img");
        ((e.className = "ProseMirror-separator"),
            e.setAttribute("mark-placeholder", "true"),
            e.setAttribute("alt", ""),
            (t.cursorWrapper = {
                dom: e,
                deco: Q.widget(t.state.selection.from, e, {
                    raw: !0,
                    marks: t.markCursor,
                }),
            }));
    } else t.cursorWrapper = null;
}
function qc(t) {
    return !t.someProp("editable", (e) => e(t.state) === !1);
}
function kg(t, e) {
    const n = Math.min(
        t.$anchor.sharedDepth(t.head),
        e.$anchor.sharedDepth(e.head),
    );
    return t.$anchor.start(n) != e.$anchor.start(n);
}
function Jc(t) {
    const e = Object.create(null);
    function n(r) {
        for (const o in r) {
            Object.prototype.hasOwnProperty.call(e, o) || (e[o] = r[o]);
        }
    }
    return (t.someProp("nodeViews", n), t.someProp("markViews", n), e);
}
function Sg(t, e) {
    let n = 0;
    let r = 0;
    for (const o in t) {
        if (t[o] != e[o]) return !0;
        n++;
    }
    for (const o in e) r++;
    return n != r;
}
function Gc(t) {
    if (t.spec.state || t.spec.filterTransaction || t.spec.appendTransaction) {
        throw new RangeError(
            "Plugins passed directly to the view must not have a state component",
        );
    }
}
const ht = {
    8: "Backspace",
    9: "Tab",
    10: "Enter",
    12: "NumLock",
    13: "Enter",
    16: "Shift",
    17: "Control",
    18: "Alt",
    20: "CapsLock",
    27: "Escape",
    32: " ",
    33: "PageUp",
    34: "PageDown",
    35: "End",
    36: "Home",
    37: "ArrowLeft",
    38: "ArrowUp",
    39: "ArrowRight",
    40: "ArrowDown",
    44: "PrintScreen",
    45: "Insert",
    46: "Delete",
    59: ";",
    61: "=",
    91: "Meta",
    92: "Meta",
    106: "*",
    107: "+",
    108: ",",
    109: "-",
    110: ".",
    111: "/",
    144: "NumLock",
    145: "ScrollLock",
    160: "Shift",
    161: "Shift",
    162: "Control",
    163: "Control",
    164: "Alt",
    165: "Alt",
    173: "-",
    186: ";",
    187: "=",
    188: ",",
    189: "-",
    190: ".",
    191: "/",
    192: "`",
    219: "[",
    220: "\\",
    221: "]",
    222: "'",
};
const Yr = {
    48: ")",
    49: "!",
    50: "@",
    51: "#",
    52: "$",
    53: "%",
    54: "^",
    55: "&",
    56: "*",
    57: "(",
    59: ":",
    61: "+",
    173: "_",
    186: ":",
    187: "+",
    188: "<",
    189: "_",
    190: ">",
    191: "?",
    192: "~",
    219: "{",
    220: "|",
    221: "}",
    222: '"',
};
const Cg = typeof navigator < "u" && /Mac/.test(navigator.platform);
const vg =
    typeof navigator < "u" &&
    /MSIE \d|Trident\/(?:[7-9]|\d{2,})\..*rv:(\d+)/.exec(navigator.userAgent);
for (oe = 0; oe < 10; oe++) ht[48 + oe] = ht[96 + oe] = String(oe);
var oe;
for (oe = 1; oe <= 24; oe++) ht[oe + 111] = "F" + oe;
var oe;
for (oe = 65; oe <= 90; oe++) {
    ((ht[oe] = String.fromCharCode(oe + 32)),
        (Yr[oe] = String.fromCharCode(oe)));
}
var oe;
for (Xr in ht) Yr.hasOwnProperty(Xr) || (Yr[Xr] = ht[Xr]);
let Xr;
function Td(t) {
    const e =
        (Cg && t.metaKey && t.shiftKey && !t.ctrlKey && !t.altKey) ||
        (vg && t.shiftKey && t.key && t.key.length == 1) ||
        t.key == "Unidentified";
    let n =
        (!e && t.key) ||
        (t.shiftKey ? Yr : ht)[t.keyCode] ||
        t.key ||
        "Unidentified";
    return (
        n == "Esc" && (n = "Escape"),
        n == "Del" && (n = "Delete"),
        n == "Left" && (n = "ArrowLeft"),
        n == "Up" && (n = "ArrowUp"),
        n == "Right" && (n = "ArrowRight"),
        n == "Down" && (n = "ArrowDown"),
        n
    );
}
const Mg =
    typeof navigator < "u" && /Mac|iP(hone|[oa]d)/.test(navigator.platform);
const Tg = typeof navigator < "u" && /Win/.test(navigator.platform);
function Ag(t) {
    const e = t.split(/-(?!$)/);
    let n = e[e.length - 1];
    n == "Space" && (n = " ");
    let r, o, i, s;
    for (let l = 0; l < e.length - 1; l++) {
        const a = e[l];
        if (/^(cmd|meta|m)$/i.test(a)) s = !0;
        else if (/^a(lt)?$/i.test(a)) r = !0;
        else if (/^(c|ctrl|control)$/i.test(a)) o = !0;
        else if (/^s(hift)?$/i.test(a)) i = !0;
        else if (/^mod$/i.test(a)) Mg ? (s = !0) : (o = !0);
        else throw new Error("Unrecognized modifier name: " + a);
    }
    return (
        r && (n = "Alt-" + n),
        o && (n = "Ctrl-" + n),
        s && (n = "Meta-" + n),
        i && (n = "Shift-" + n),
        n
    );
}
function Eg(t) {
    const e = Object.create(null);
    for (const n in t) e[Ag(n)] = t[n];
    return e;
}
function Bs(t, e, n = !0) {
    return (
        e.altKey && (t = "Alt-" + t),
        e.ctrlKey && (t = "Ctrl-" + t),
        e.metaKey && (t = "Meta-" + t),
        n && e.shiftKey && (t = "Shift-" + t),
        t
    );
}
function Ad(t) {
    return new L({ props: { handleKeyDown: Qn(t) } });
}
function Qn(t) {
    const e = Eg(t);
    return function (n, r) {
        const o = Td(r);
        let i;
        const s = e[Bs(o, r)];
        if (s && s(n.state, n.dispatch, n)) return !0;
        if (o.length == 1 && o != " ") {
            if (r.shiftKey) {
                const l = e[Bs(o, r, !1)];
                if (l && l(n.state, n.dispatch, n)) return !0;
            }
            if (
                (r.altKey || r.metaKey || r.ctrlKey) &&
                !(Tg && r.ctrlKey && r.altKey) &&
                (i = ht[r.keyCode]) &&
                i != o
            ) {
                const l = e[Bs(i, r)];
                if (l && l(n.state, n.dispatch, n)) return !0;
            }
        }
        return !1;
    };
}
const Ng = Object.defineProperty;
const Ws = (t, e) => {
    for (const n in e) Ng(t, n, { get: e[n], enumerable: !0 });
};
function io(t) {
    const { state: e, transaction: n } = t;
    let { selection: r } = n;
    let { doc: o } = n;
    let { storedMarks: i } = n;
    return {
        ...e,
        apply: e.apply.bind(e),
        applyTransaction: e.applyTransaction.bind(e),
        plugins: e.plugins,
        schema: e.schema,
        reconfigure: e.reconfigure.bind(e),
        toJSON: e.toJSON.bind(e),
        get storedMarks() {
            return i;
        },
        get selection() {
            return r;
        },
        get doc() {
            return o;
        },
        get tr() {
            return ((r = n.selection), (o = n.doc), (i = n.storedMarks), n);
        },
    };
}
const so = class {
    constructor(t) {
        ((this.editor = t.editor),
            (this.rawCommands = this.editor.extensionManager.commands),
            (this.customState = t.state));
    }

    get hasCustomState() {
        return !!this.customState;
    }

    get state() {
        return this.customState || this.editor.state;
    }

    get commands() {
        const { rawCommands: t, editor: e, state: n } = this;
        const { view: r } = e;
        const { tr: o } = n;
        const i = this.buildProps(o);
        return Object.fromEntries(
            Object.entries(t).map(([s, l]) => [
                s,
                (...c) => {
                    const d = l(...c)(i);
                    return (
                        !o.getMeta("preventDispatch") &&
                            !this.hasCustomState &&
                            r.dispatch(o),
                        d
                    );
                },
            ]),
        );
    }

    get chain() {
        return () => this.createChain();
    }

    get can() {
        return () => this.createCan();
    }

    createChain(t, e = !0) {
        const { rawCommands: n, editor: r, state: o } = this;
        const { view: i } = r;
        const s = [];
        const l = !!t;
        const a = t || o.tr;
        const c = () => (
            !l &&
                e &&
                !a.getMeta("preventDispatch") &&
                !this.hasCustomState &&
                i.dispatch(a),
            s.every((u) => u === !0)
        );
        const d = {
            ...Object.fromEntries(
                Object.entries(n).map(([u, f]) => [
                    u,
                    (...p) => {
                        const m = this.buildProps(a, e);
                        const g = f(...p)(m);
                        return (s.push(g), d);
                    },
                ]),
            ),
            run: c,
        };
        return d;
    }

    createCan(t) {
        const { rawCommands: e, state: n } = this;
        const r = !1;
        const o = t || n.tr;
        const i = this.buildProps(o, r);
        return {
            ...Object.fromEntries(
                Object.entries(e).map(([l, a]) => [
                    l,
                    (...c) => a(...c)({ ...i, dispatch: void 0 }),
                ]),
            ),
            chain: () => this.createChain(o, r),
        };
    }

    buildProps(t, e = !0) {
        const { rawCommands: n, editor: r, state: o } = this;
        const { view: i } = r;
        const s = {
            tr: t,
            editor: r,
            view: i,
            state: io({ state: o, transaction: t }),
            dispatch: e ? () => {} : void 0,
            chain: () => this.createChain(t, e),
            can: () => this.createCan(t),
            get commands() {
                return Object.fromEntries(
                    Object.entries(n).map(([l, a]) => [
                        l,
                        (...c) => a(...c)(s),
                    ]),
                );
            },
        };
        return s;
    }
};
const Ld = {};
Ws(Ld, {
    blur: () => Og,
    clearContent: () => Rg,
    clearNodes: () => Dg,
    command: () => Ig,
    createParagraphNear: () => Pg,
    cut: () => Lg,
    deleteCurrentNode: () => zg,
    deleteNode: () => Bg,
    deleteRange: () => Hg,
    deleteSelection: () => $g,
    enter: () => Fg,
    exitCode: () => _g,
    extendMarkRange: () => Vg,
    first: () => Wg,
    focus: () => Kg,
    forEach: () => Ug,
    insertContent: () => qg,
    insertContentAt: () => Xg,
    joinBackward: () => Zg,
    joinDown: () => Qg,
    joinForward: () => ey,
    joinItemBackward: () => ty,
    joinItemForward: () => ny,
    joinTextblockBackward: () => ry,
    joinTextblockForward: () => oy,
    joinUp: () => Yg,
    keyboardShortcut: () => sy,
    lift: () => ly,
    liftEmptyBlock: () => ay,
    liftListItem: () => cy,
    newlineInCode: () => dy,
    resetAttributes: () => uy,
    scrollIntoView: () => fy,
    selectAll: () => hy,
    selectNodeBackward: () => py,
    selectNodeForward: () => my,
    selectParentNode: () => gy,
    selectTextblockEnd: () => yy,
    selectTextblockStart: () => by,
    setContent: () => wy,
    setMark: () => Oy,
    setMeta: () => Ry,
    setNode: () => Dy,
    setNodeSelection: () => Iy,
    setTextSelection: () => Py,
    sinkListItem: () => Ly,
    splitBlock: () => zy,
    splitListItem: () => By,
    toggleList: () => Hy,
    toggleMark: () => $y,
    toggleNode: () => Fy,
    toggleWrap: () => _y,
    undoInputRule: () => Vy,
    unsetAllMarks: () => Wy,
    unsetMark: () => jy,
    updateAttributes: () => Ky,
    wrapIn: () => Uy,
    wrapInList: () => qy,
});
var Og =
    () =>
    ({ editor: t, view: e }) => (
        requestAnimationFrame(() => {
            let n;
            t.isDestroyed ||
                (e.dom.blur(),
                (n = window?.getSelection()) == null || n.removeAllRanges());
        }),
        !0
    );
var Rg =
    (t = !0) =>
    ({ commands: e }) =>
        e.setContent("", { emitUpdate: t });
var Dg =
    () =>
    ({ state: t, tr: e, dispatch: n }) => {
        const { selection: r } = e;
        const { ranges: o } = r;
        return (
            n &&
                o.forEach(({ $from: i, $to: s }) => {
                    t.doc.nodesBetween(i.pos, s.pos, (l, a) => {
                        if (l.type.isText) return;
                        const { doc: c, mapping: d } = e;
                        const u = c.resolve(d.map(a));
                        const f = c.resolve(d.map(a + l.nodeSize));
                        const h = u.blockRange(f);
                        if (!h) return;
                        const p = at(h);
                        if (l.type.isTextblock) {
                            const { defaultType: m } = u.parent.contentMatchAt(
                                u.index(),
                            );
                            e.setNodeMarkup(h.start, m);
                        }
                        (p || p === 0) && e.lift(h, p);
                    });
                }),
            !0
        );
    };
var Ig = (t) => (e) => t(e);
var Pg =
    () =>
    ({ state: t, dispatch: e }) =>
        Zi(t, e);
var Lg =
    (t, e) =>
    ({ editor: n, tr: r }) => {
        const { state: o } = n;
        const i = o.doc.slice(t.from, t.to);
        r.deleteRange(t.from, t.to);
        const s = r.mapping.map(e);
        return (
            r.insert(s, i.content),
            r.setSelection(new R(r.doc.resolve(Math.max(s - 1, 0)))),
            !0
        );
    };
var zg =
    () =>
    ({ tr: t, dispatch: e }) => {
        const { selection: n } = t;
        const r = n.$anchor.node();
        if (r.content.size > 0) return !1;
        const o = t.selection.$anchor;
        for (let i = o.depth; i > 0; i -= 1) {
            if (o.node(i).type === r.type) {
                if (e) {
                    const l = o.before(i);
                    const a = o.after(i);
                    t.delete(l, a).scrollIntoView();
                }
                return !0;
            }
        }
        return !1;
    };
function ee(t, e) {
    if (typeof t === "string") {
        if (!e.nodes[t]) {
            throw Error(
                `There is no node type named '${t}'. Maybe you forgot to add the extension?`,
            );
        }
        return e.nodes[t];
    }
    return t;
}
var Bg =
    (t) =>
    ({ tr: e, state: n, dispatch: r }) => {
        const o = ee(t, n.schema);
        const i = e.selection.$anchor;
        for (let s = i.depth; s > 0; s -= 1) {
            if (i.node(s).type === o) {
                if (r) {
                    const a = i.before(s);
                    const c = i.after(s);
                    e.delete(a, c).scrollIntoView();
                }
                return !0;
            }
        }
        return !1;
    };
var Hg =
    (t) =>
    ({ tr: e, dispatch: n }) => {
        const { from: r, to: o } = t;
        return (n && e.delete(r, o), !0);
    };
var $g =
    () =>
    ({ state: t, dispatch: e }) =>
        Br(t, e);
var Fg =
    () =>
    ({ commands: t }) =>
        t.keyboardShortcut("Enter");
var _g =
    () =>
    ({ state: t, dispatch: e }) =>
        Qi(t, e);
function js(t) {
    return Object.prototype.toString.call(t) === "[object RegExp]";
}
function ro(t, e, n = { strict: !0 }) {
    const r = Object.keys(e);
    return r.length
        ? r.every((o) =>
              n.strict
                  ? e[o] === t[o]
                  : js(e[o])
                    ? e[o].test(t[o])
                    : e[o] === t[o],
          )
        : !0;
}
function zd(t, e, n = {}) {
    return t.find(
        (r) =>
            r.type === e &&
            ro(
                Object.fromEntries(Object.keys(n).map((o) => [o, r.attrs[o]])),
                n,
            ),
    );
}
function Ed(t, e, n = {}) {
    return !!zd(t, e, n);
}
function Ks(t, e, n) {
    let r;
    if (!t || !e) return;
    let o = t.parent.childAfter(t.parentOffset);
    if (
        ((!o.node || !o.node.marks.some((d) => d.type === e)) &&
            (o = t.parent.childBefore(t.parentOffset)),
        !o.node ||
            !o.node.marks.some((d) => d.type === e) ||
            ((n = n || ((r = o.node.marks[0]) == null ? void 0 : r.attrs)),
            !zd([...o.node.marks], e, n)))
    ) {
        return;
    }
    let s = o.index;
    let l = t.start() + o.offset;
    let a = s + 1;
    let c = l + o.node.nodeSize;
    for (; s > 0 && Ed([...t.parent.child(s - 1).marks], e, n); ) {
        ((s -= 1), (l -= t.parent.child(s).nodeSize));
    }
    for (
        ;
        a < t.parent.childCount && Ed([...t.parent.child(a).marks], e, n);

    ) {
        ((c += t.parent.child(a).nodeSize), (a += 1));
    }
    return { from: l, to: c };
}
function mt(t, e) {
    if (typeof t === "string") {
        if (!e.marks[t]) {
            throw Error(
                `There is no mark type named '${t}'. Maybe you forgot to add the extension?`,
            );
        }
        return e.marks[t];
    }
    return t;
}
var Vg =
    (t, e = {}) =>
    ({ tr: n, state: r, dispatch: o }) => {
        const i = mt(t, r.schema);
        const { doc: s, selection: l } = n;
        const { $from: a, from: c, to: d } = l;
        if (o) {
            const u = Ks(a, i, e);
            if (u && u.from <= c && u.to >= d) {
                const f = R.create(s, u.from, u.to);
                n.setSelection(f);
            }
        }
        return !0;
    };
var Wg = (t) => (e) => {
    const n = typeof t === "function" ? t(e) : t;
    for (let r = 0; r < n.length; r += 1) if (n[r](e)) return !0;
    return !1;
};
function lo(t) {
    return t instanceof R;
}
function pt(t = 0, e = 0, n = 0) {
    return Math.min(Math.max(t, e), n);
}
function Bd(t, e = null) {
    if (!e) return null;
    const n = I.atStart(t);
    const r = I.atEnd(t);
    if (e === "start" || e === !0) return n;
    if (e === "end") return r;
    const o = n.from;
    const i = r.to;
    return e === "all"
        ? R.create(t, pt(0, o, i), pt(t.content.size, o, i))
        : R.create(t, pt(e, o, i), pt(e, o, i));
}
function jg() {
    return (
        navigator.platform === "Android" || /android/i.test(navigator.userAgent)
    );
}
function Us() {
    return (
        [
            "iPad Simulator",
            "iPhone Simulator",
            "iPod Simulator",
            "iPad",
            "iPhone",
            "iPod",
        ].includes(navigator.platform) ||
        (navigator.userAgent.includes("Mac") && "ontouchend" in document)
    );
}
var Kg =
    (t = null, e = {}) =>
    ({ editor: n, view: r, tr: o, dispatch: i }) => {
        e = { scrollIntoView: !0, ...e };
        const s = () => {
            ((Us() || jg()) && r.dom.focus(),
                requestAnimationFrame(() => {
                    n.isDestroyed ||
                        (r.focus(),
                        e?.scrollIntoView && n.commands.scrollIntoView());
                }));
        };
        if ((r.hasFocus() && t === null) || t === !1) return !0;
        if (i && t === null && !lo(n.state.selection)) return (s(), !0);
        const l = Bd(o.doc, t) || n.state.selection;
        const a = n.state.selection.eq(l);
        return (
            i &&
                (a || o.setSelection(l),
                a && o.storedMarks && o.setStoredMarks(o.storedMarks),
                s()),
            !0
        );
    };
var Ug = (t, e) => (n) => t.every((r, o) => e(r, { ...n, index: o }));
var qg =
    (t, e) =>
    ({ tr: n, commands: r }) =>
        r.insertContentAt({ from: n.selection.from, to: n.selection.to }, t, e);
const Hd = (t) => {
    const e = t.childNodes;
    for (let n = e.length - 1; n >= 0; n -= 1) {
        const r = e[n];
        r.nodeType === 3 && r.nodeValue && /^(\n\s\s|\n)$/.test(r.nodeValue)
            ? t.removeChild(r)
            : r.nodeType === 1 && Hd(r);
    }
    return t;
};
function Qr(t) {
    if (typeof window > "u") {
        throw new Error(
            "[tiptap error]: there is no window object available, so this function cannot be used",
        );
    }
    const e = `<body>${t}</body>`;
    const n = new window.DOMParser().parseFromString(e, "text/html").body;
    return Hd(n);
}
function Zn(t, e, n) {
    if (t instanceof le || t instanceof C) return t;
    n = { slice: !0, parseOptions: {}, ...n };
    const r = typeof t === "object" && t !== null;
    const o = typeof t === "string";
    if (r) {
        try {
            if (Array.isArray(t) && t.length > 0) {
                return C.fromArray(t.map((l) => e.nodeFromJSON(l)));
            }
            const s = e.nodeFromJSON(t);
            return (n.errorOnInvalidContent && s.check(), s);
        } catch (i) {
            if (n.errorOnInvalidContent) {
                throw new Error("[tiptap error]: Invalid JSON content", {
                    cause: i,
                });
            }
            return (
                console.warn(
                    "[tiptap warn]: Invalid content.",
                    "Passed value:",
                    t,
                    "Error:",
                    i,
                ),
                Zn("", e, n)
            );
        }
    }
    if (o) {
        if (n.errorOnInvalidContent) {
            let s = !1;
            let l = "";
            const a = new an({
                topNode: e.spec.topNode,
                marks: e.spec.marks,
                nodes: e.spec.nodes.append({
                    __tiptap__private__unknown__catch__all__node: {
                        content: "inline*",
                        group: "block",
                        parseDOM: [
                            {
                                tag: "*",
                                getAttrs: (c) => (
                                    (s = !0),
                                    (l =
                                        typeof c === "string"
                                            ? c
                                            : c.outerHTML),
                                    null
                                ),
                            },
                        ],
                    },
                }),
            });
            if (
                (n.slice
                    ? Ue.fromSchema(a).parseSlice(Qr(t), n.parseOptions)
                    : Ue.fromSchema(a).parse(Qr(t), n.parseOptions),
                n.errorOnInvalidContent && s)
            ) {
                throw new Error("[tiptap error]: Invalid HTML content", {
                    cause: new Error(`Invalid element found: ${l}`),
                });
            }
        }
        const i = Ue.fromSchema(e);
        return n.slice
            ? i.parseSlice(Qr(t), n.parseOptions).content
            : i.parse(Qr(t), n.parseOptions);
    }
    return Zn("", e, n);
}
function Jg(t, e, n) {
    const r = t.steps.length - 1;
    if (r < e) return;
    const o = t.steps[r];
    if (!(o instanceof pe || o instanceof re)) return;
    const i = t.mapping.maps[r];
    let s = 0;
    (i.forEach((l, a, c, d) => {
        s === 0 && (s = d);
    }),
        t.setSelection(I.near(t.doc.resolve(s), n)));
}
const Gg = (t) => !("type" in t);
var Xg =
    (t, e, n) =>
    ({ tr: r, dispatch: o, editor: i }) => {
        let s;
        if (o) {
            n = {
                parseOptions: i.options.parseOptions,
                updateSelection: !0,
                applyInputRules: !1,
                applyPasteRules: !1,
                ...n,
            };
            let l;
            const a = (g) => {
                i.emit("contentError", {
                    editor: i,
                    error: g,
                    disableCollaboration: () => {
                        "collaboration" in i.storage &&
                            typeof i.storage.collaboration === "object" &&
                            i.storage.collaboration &&
                            (i.storage.collaboration.isDisabled = !0);
                    },
                });
            };
            const c = { preserveWhitespace: "full", ...n.parseOptions };
            if (
                !n.errorOnInvalidContent &&
                !i.options.enableContentCheck &&
                i.options.emitContentError
            ) {
                try {
                    Zn(e, i.schema, {
                        parseOptions: c,
                        errorOnInvalidContent: !0,
                    });
                } catch (g) {
                    a(g);
                }
            }
            try {
                l = Zn(e, i.schema, {
                    parseOptions: c,
                    errorOnInvalidContent:
                        (s = n.errorOnInvalidContent) != null
                            ? s
                            : i.options.enableContentCheck,
                });
            } catch (g) {
                return (a(g), !1);
            }
            let { from: d, to: u } =
                typeof t === "number"
                    ? { from: t, to: t }
                    : { from: t.from, to: t.to };
            let f = !0;
            let h = !0;
            if (
                ((Gg(l) ? l : [l]).forEach((g) => {
                    (g.check(),
                        (f = f ? g.isText && g.marks.length === 0 : !1),
                        (h = h ? g.isBlock : !1));
                }),
                d === u && h)
            ) {
                const { parent: g } = r.doc.resolve(d);
                g.isTextblock &&
                    !g.type.spec.code &&
                    !g.childCount &&
                    ((d -= 1), (u += 1));
            }
            let m;
            if (f) {
                if (Array.isArray(e)) m = e.map((g) => g.text || "").join("");
                else if (e instanceof C) {
                    let g = "";
                    (e.forEach((y) => {
                        y.text && (g += y.text);
                    }),
                        (m = g));
                } else {
                    typeof e === "object" && e && e.text
                        ? (m = e.text)
                        : (m = e);
                }
                r.insertText(m, d, u);
            } else {
                m = l;
                const g = r.doc.resolve(d);
                const y = g.node();
                const b = g.parentOffset === 0;
                const k = y.isText || y.isTextblock;
                const v = y.content.size > 0;
                (b && k && v && (d = Math.max(0, d - 1)),
                    r.replaceWith(d, u, m));
            }
            (n.updateSelection && Jg(r, r.steps.length - 1, -1),
                n.applyInputRules &&
                    r.setMeta("applyInputRules", { from: d, text: m }),
                n.applyPasteRules &&
                    r.setMeta("applyPasteRules", { from: d, text: m }));
        }
        return !0;
    };
var Yg =
    () =>
    ({ state: t, dispatch: e }) =>
        lc(t, e);
var Qg =
    () =>
    ({ state: t, dispatch: e }) =>
        ac(t, e);
var Zg =
    () =>
    ({ state: t, dispatch: e }) =>
        ji(t, e);
var ey =
    () =>
    ({ state: t, dispatch: e }) =>
        qi(t, e);
var ty =
    () =>
    ({ state: t, dispatch: e, tr: n }) => {
        try {
            const r = Ft(t.doc, t.selection.$from.pos, -1);
            return r == null ? !1 : (n.join(r, 2), e && e(n), !0);
        } catch {
            return !1;
        }
    };
var ny =
    () =>
    ({ state: t, dispatch: e, tr: n }) => {
        try {
            const r = Ft(t.doc, t.selection.$from.pos, 1);
            return r == null ? !1 : (n.join(r, 2), e && e(n), !0);
        } catch {
            return !1;
        }
    };
var ry =
    () =>
    ({ state: t, dispatch: e }) =>
        rc(t, e);
var oy =
    () =>
    ({ state: t, dispatch: e }) =>
        oc(t, e);
function $d() {
    return typeof navigator < "u" ? /Mac/.test(navigator.platform) : !1;
}
function iy(t) {
    const e = t.split(/-(?!$)/);
    let n = e[e.length - 1];
    n === "Space" && (n = " ");
    let r, o, i, s;
    for (let l = 0; l < e.length - 1; l += 1) {
        const a = e[l];
        if (/^(cmd|meta|m)$/i.test(a)) s = !0;
        else if (/^a(lt)?$/i.test(a)) r = !0;
        else if (/^(c|ctrl|control)$/i.test(a)) o = !0;
        else if (/^s(hift)?$/i.test(a)) i = !0;
        else if (/^mod$/i.test(a)) Us() || $d() ? (s = !0) : (o = !0);
        else throw new Error(`Unrecognized modifier name: ${a}`);
    }
    return (
        r && (n = `Alt-${n}`),
        o && (n = `Ctrl-${n}`),
        s && (n = `Meta-${n}`),
        i && (n = `Shift-${n}`),
        n
    );
}
var sy =
    (t) =>
    ({ editor: e, view: n, tr: r, dispatch: o }) => {
        const i = iy(t).split(/-(?!$)/);
        const s = i.find((c) => !["Alt", "Ctrl", "Meta", "Shift"].includes(c));
        const l = new KeyboardEvent("keydown", {
            key: s === "Space" ? " " : s,
            altKey: i.includes("Alt"),
            ctrlKey: i.includes("Ctrl"),
            metaKey: i.includes("Meta"),
            shiftKey: i.includes("Shift"),
            bubbles: !0,
            cancelable: !0,
        });
        const a = e.captureTransaction(() => {
            n.someProp("handleKeyDown", (c) => c(n, l));
        });
        return (
            a?.steps.forEach((c) => {
                const d = c.map(r.mapping);
                d && o && r.maybeStep(d);
            }),
            !0
        );
    };
function Ge(t, e, n = {}) {
    const { from: r, to: o, empty: i } = t.selection;
    const s = e ? ee(e, t.schema) : null;
    const l = [];
    t.doc.nodesBetween(r, o, (u, f) => {
        if (u.isText) return;
        const h = Math.max(r, f);
        const p = Math.min(o, f + u.nodeSize);
        l.push({ node: u, from: h, to: p });
    });
    const a = o - r;
    const c = l
        .filter((u) => (s ? s.name === u.node.type.name : !0))
        .filter((u) => ro(u.node.attrs, n, { strict: !1 }));
    return i ? !!c.length : c.reduce((u, f) => u + f.to - f.from, 0) >= a;
}
var ly =
    (t, e = {}) =>
    ({ state: n, dispatch: r }) => {
        const o = ee(t, n.schema);
        return Ge(n, o, e) ? cc(n, r) : !1;
    };
var ay =
    () =>
    ({ state: t, dispatch: e }) =>
        es(t, e);
var cy =
    (t) =>
    ({ state: e, dispatch: n }) => {
        const r = ee(t, e.schema);
        return mc(r)(e, n);
    };
var dy =
    () =>
    ({ state: t, dispatch: e }) =>
        Xi(t, e);
function ao(t, e) {
    return e.nodes[t] ? "node" : e.marks[t] ? "mark" : null;
}
function Nd(t, e) {
    const n = typeof e === "string" ? [e] : e;
    return Object.keys(t).reduce(
        (r, o) => (n.includes(o) || (r[o] = t[o]), r),
        {},
    );
}
var uy =
    (t, e) =>
    ({ tr: n, state: r, dispatch: o }) => {
        let i = null;
        let s = null;
        const l = ao(typeof t === "string" ? t : t.name, r.schema);
        return l
            ? (l === "node" && (i = ee(t, r.schema)),
              l === "mark" && (s = mt(t, r.schema)),
              o &&
                  n.selection.ranges.forEach((a) => {
                      r.doc.nodesBetween(a.$from.pos, a.$to.pos, (c, d) => {
                          (i &&
                              i === c.type &&
                              n.setNodeMarkup(d, void 0, Nd(c.attrs, e)),
                              s &&
                                  c.marks.length &&
                                  c.marks.forEach((u) => {
                                      s === u.type &&
                                          n.addMark(
                                              d,
                                              d + c.nodeSize,
                                              s.create(Nd(u.attrs, e)),
                                          );
                                  }));
                      });
                  }),
              !0)
            : !1;
    };
var fy =
    () =>
    ({ tr: t, dispatch: e }) => (e && t.scrollIntoView(), !0);
var hy =
    () =>
    ({ tr: t, dispatch: e }) => {
        if (e) {
            const n = new be(t.doc);
            t.setSelection(n);
        }
        return !0;
    };
var py =
    () =>
    ({ state: t, dispatch: e }) =>
        Ki(t, e);
var my =
    () =>
    ({ state: t, dispatch: e }) =>
        Ji(t, e);
var gy =
    () =>
    ({ state: t, dispatch: e }) =>
        dc(t, e);
var yy =
    () =>
    ({ state: t, dispatch: e }) =>
        ns(t, e);
var by =
    () =>
    ({ state: t, dispatch: e }) =>
        ts(t, e);
function _s(t, e, n = {}, r = {}) {
    return Zn(t, e, {
        slice: !1,
        parseOptions: n,
        errorOnInvalidContent: r.errorOnInvalidContent,
    });
}
var wy =
    (
        t,
        {
            errorOnInvalidContent: e,
            emitUpdate: n = !0,
            parseOptions: r = {},
        } = {},
    ) =>
    ({ editor: o, tr: i, dispatch: s, commands: l }) => {
        const { doc: a } = i;
        if (r.preserveWhitespace !== "full") {
            const c = _s(t, o.schema, r, {
                errorOnInvalidContent: e ?? o.options.enableContentCheck,
            });
            return (
                s &&
                    i
                        .replaceWith(0, a.content.size, c)
                        .setMeta("preventUpdate", !n),
                !0
            );
        }
        return (
            s && i.setMeta("preventUpdate", !n),
            l.insertContentAt({ from: 0, to: a.content.size }, t, {
                parseOptions: r,
                errorOnInvalidContent: e ?? o.options.enableContentCheck,
            })
        );
    };
function Fd(t, e) {
    const n = mt(e, t.schema);
    const { from: r, to: o, empty: i } = t.selection;
    const s = [];
    i
        ? (t.storedMarks && s.push(...t.storedMarks),
          s.push(...t.selection.$head.marks()))
        : t.doc.nodesBetween(r, o, (a) => {
              s.push(...a.marks);
          });
    const l = s.find((a) => a.type.name === n.name);
    return l ? { ...l.attrs } : {};
}
function qs(t, e) {
    const n = new St(t);
    return (
        e.forEach((r) => {
            r.steps.forEach((o) => {
                n.step(o);
            });
        }),
        n
    );
}
function tr(t) {
    for (let e = 0; e < t.edgeCount; e += 1) {
        const { type: n } = t.edge(e);
        if (n.isTextblock && !n.hasRequiredAttrs()) return n;
    }
    return null;
}
function Cn(t, e) {
    const n = [];
    return (
        t.descendants((r, o) => {
            e(r) && n.push({ node: r, pos: o });
        }),
        n
    );
}
function _d(t, e, n) {
    const r = [];
    return (
        t.nodesBetween(e.from, e.to, (o, i) => {
            n(o) && r.push({ node: o, pos: i });
        }),
        r
    );
}
function Js(t, e) {
    for (let n = t.depth; n > 0; n -= 1) {
        const r = t.node(n);
        if (e(r)) {
            return {
                pos: n > 0 ? t.before(n) : 0,
                start: t.start(n),
                depth: n,
                node: r,
            };
        }
    }
}
function Xe(t) {
    return (e) => Js(e.$from, t);
}
function z(t, e, n) {
    return t.config[e] === void 0 && t.parent
        ? z(t.parent, e, n)
        : typeof t.config[e] === "function"
          ? t.config[e].bind({
                ...n,
                parent: t.parent ? z(t.parent, e, n) : null,
            })
          : t.config[e];
}
function Gs(t) {
    return t
        .map((e) => {
            const n = { name: e.name, options: e.options, storage: e.storage };
            const r = z(e, "addExtensions", n);
            return r ? [e, ...Gs(r())] : e;
        })
        .flat(10);
}
function Xs(t, e) {
    const n = it.fromSchema(e).serializeFragment(t);
    const o = document.implementation.createHTMLDocument().createElement("div");
    return (o.appendChild(n), o.innerHTML);
}
function Vd(t) {
    return typeof t === "function";
}
function J(t, e = void 0, ...n) {
    return Vd(t) ? (e ? t.bind(e)(...n) : t(...n)) : t;
}
function xy(t = {}) {
    return Object.keys(t).length === 0 && t.constructor === Object;
}
function er(t) {
    const e = t.filter((o) => o.type === "extension");
    const n = t.filter((o) => o.type === "node");
    const r = t.filter((o) => o.type === "mark");
    return { baseExtensions: e, nodeExtensions: n, markExtensions: r };
}
function Wd(t) {
    const e = [];
    const { nodeExtensions: n, markExtensions: r } = er(t);
    const o = [...n, ...r];
    const i = {
        default: null,
        validate: void 0,
        rendered: !0,
        renderHTML: null,
        parseHTML: null,
        keepOnSplit: !0,
        isRequired: !1,
    };
    return (
        t.forEach((s) => {
            const l = {
                name: s.name,
                options: s.options,
                storage: s.storage,
                extensions: o,
            };
            const a = z(s, "addGlobalAttributes", l);
            if (!a) return;
            a().forEach((d) => {
                d.types.forEach((u) => {
                    Object.entries(d.attributes).forEach(([f, h]) => {
                        e.push({ type: u, name: f, attribute: { ...i, ...h } });
                    });
                });
            });
        }),
        o.forEach((s) => {
            const l = { name: s.name, options: s.options, storage: s.storage };
            const a = z(s, "addAttributes", l);
            if (!a) return;
            const c = a();
            Object.entries(c).forEach(([d, u]) => {
                const f = { ...i, ...u };
                (typeof f?.default === "function" && (f.default = f.default()),
                    f?.isRequired && f?.default === void 0 && delete f.default,
                    e.push({ type: s.name, name: d, attribute: f }));
            });
        }),
        e
    );
}
function O(...t) {
    return t
        .filter((e) => !!e)
        .reduce((e, n) => {
            const r = { ...e };
            return (
                Object.entries(n).forEach(([o, i]) => {
                    if (!r[o]) {
                        r[o] = i;
                        return;
                    }
                    if (o === "class") {
                        const l = i ? String(i).split(" ") : [];
                        const a = r[o] ? r[o].split(" ") : [];
                        const c = l.filter((d) => !a.includes(d));
                        r[o] = [...a, ...c].join(" ");
                    } else if (o === "style") {
                        const l = i
                            ? i
                                  .split(";")
                                  .map((d) => d.trim())
                                  .filter(Boolean)
                            : [];
                        const a = r[o]
                            ? r[o]
                                  .split(";")
                                  .map((d) => d.trim())
                                  .filter(Boolean)
                            : [];
                        const c = new Map();
                        (a.forEach((d) => {
                            const [u, f] = d.split(":").map((h) => h.trim());
                            c.set(u, f);
                        }),
                            l.forEach((d) => {
                                const [u, f] = d
                                    .split(":")
                                    .map((h) => h.trim());
                                c.set(u, f);
                            }),
                            (r[o] = Array.from(c.entries())
                                .map(([d, u]) => `${d}: ${u}`)
                                .join("; ")));
                    } else r[o] = i;
                }),
                r
            );
        }, {});
}
function oo(t, e) {
    return e
        .filter((n) => n.type === t.type.name)
        .filter((n) => n.attribute.rendered)
        .map((n) =>
            n.attribute.renderHTML
                ? n.attribute.renderHTML(t.attrs) || {}
                : { [n.name]: t.attrs[n.name] },
        )
        .reduce((n, r) => O(n, r), {});
}
function ky(t) {
    return typeof t !== "string"
        ? t
        : t.match(/^[+-]?(?:\d*\.)?\d+$/)
          ? Number(t)
          : t === "true"
            ? !0
            : t === "false"
              ? !1
              : t;
}
function Od(t, e) {
    return "style" in t
        ? t
        : {
              ...t,
              getAttrs: (n) => {
                  const r = t.getAttrs ? t.getAttrs(n) : t.attrs;
                  if (r === !1) return !1;
                  const o = e.reduce((i, s) => {
                      const l = s.attribute.parseHTML
                          ? s.attribute.parseHTML(n)
                          : ky(n.getAttribute(s.name));
                      return l == null ? i : { ...i, [s.name]: l };
                  }, {});
                  return { ...r, ...o };
              },
          };
}
function Rd(t) {
    return Object.fromEntries(
        Object.entries(t).filter(([e, n]) =>
            e === "attrs" && xy(n) ? !1 : n != null,
        ),
    );
}
function Sy(t, e) {
    let n;
    const r = Wd(t);
    const { nodeExtensions: o, markExtensions: i } = er(t);
    const s = (n = o.find((c) => z(c, "topNode"))) == null ? void 0 : n.name;
    const l = Object.fromEntries(
        o.map((c) => {
            const d = r.filter((y) => y.type === c.name);
            const u = {
                name: c.name,
                options: c.options,
                storage: c.storage,
                editor: e,
            };
            const f = t.reduce((y, b) => {
                const k = z(b, "extendNodeSchema", u);
                return { ...y, ...(k ? k(c) : {}) };
            }, {});
            const h = Rd({
                ...f,
                content: J(z(c, "content", u)),
                marks: J(z(c, "marks", u)),
                group: J(z(c, "group", u)),
                inline: J(z(c, "inline", u)),
                atom: J(z(c, "atom", u)),
                selectable: J(z(c, "selectable", u)),
                draggable: J(z(c, "draggable", u)),
                code: J(z(c, "code", u)),
                whitespace: J(z(c, "whitespace", u)),
                linebreakReplacement: J(z(c, "linebreakReplacement", u)),
                defining: J(z(c, "defining", u)),
                isolating: J(z(c, "isolating", u)),
                attrs: Object.fromEntries(
                    d.map((y) => {
                        let b, k;
                        return [
                            y.name,
                            {
                                default:
                                    (b = y?.attribute) == null
                                        ? void 0
                                        : b.default,
                                validate:
                                    (k = y?.attribute) == null
                                        ? void 0
                                        : k.validate,
                            },
                        ];
                    }),
                ),
            });
            const p = J(z(c, "parseHTML", u));
            p && (h.parseDOM = p.map((y) => Od(y, d)));
            const m = z(c, "renderHTML", u);
            m && (h.toDOM = (y) => m({ node: y, HTMLAttributes: oo(y, d) }));
            const g = z(c, "renderText", u);
            return (g && (h.toText = g), [c.name, h]);
        }),
    );
    const a = Object.fromEntries(
        i.map((c) => {
            const d = r.filter((g) => g.type === c.name);
            const u = {
                name: c.name,
                options: c.options,
                storage: c.storage,
                editor: e,
            };
            const f = t.reduce((g, y) => {
                const b = z(y, "extendMarkSchema", u);
                return { ...g, ...(b ? b(c) : {}) };
            }, {});
            const h = Rd({
                ...f,
                inclusive: J(z(c, "inclusive", u)),
                excludes: J(z(c, "excludes", u)),
                group: J(z(c, "group", u)),
                spanning: J(z(c, "spanning", u)),
                code: J(z(c, "code", u)),
                attrs: Object.fromEntries(
                    d.map((g) => {
                        let y, b;
                        return [
                            g.name,
                            {
                                default:
                                    (y = g?.attribute) == null
                                        ? void 0
                                        : y.default,
                                validate:
                                    (b = g?.attribute) == null
                                        ? void 0
                                        : b.validate,
                            },
                        ];
                    }),
                ),
            });
            const p = J(z(c, "parseHTML", u));
            p && (h.parseDOM = p.map((g) => Od(g, d)));
            const m = z(c, "renderHTML", u);
            return (
                m &&
                    (h.toDOM = (g) => m({ mark: g, HTMLAttributes: oo(g, d) })),
                [c.name, h]
            );
        }),
    );
    return new an({ topNode: s, nodes: l, marks: a });
}
function Cy(t) {
    const e = t.filter((n, r) => t.indexOf(n) !== r);
    return Array.from(new Set(e));
}
function Ys(t) {
    return t.sort((n, r) => {
        const o = z(n, "priority") || 100;
        const i = z(r, "priority") || 100;
        return o > i ? -1 : o < i ? 1 : 0;
    });
}
function jd(t) {
    const e = Ys(Gs(t));
    const n = Cy(e.map((r) => r.name));
    return (
        n.length &&
            console.warn(
                `[tiptap warn]: Duplicate extension names found: [${n.map((r) => `'${r}'`).join(", ")}]. This can lead to issues.`,
            ),
        e
    );
}
function Kd(t, e, n) {
    const { from: r, to: o } = e;
    const {
        blockSeparator: i = `

`,
        textSerializers: s = {},
    } = n || {};
    let l = "";
    return (
        t.nodesBetween(r, o, (a, c, d, u) => {
            let f;
            a.isBlock && c > r && (l += i);
            const h = s?.[a.type.name];
            if (h) {
                return (
                    d &&
                        (l += h({
                            node: a,
                            pos: c,
                            parent: d,
                            index: u,
                            range: e,
                        })),
                    !1
                );
            }
            a.isText &&
                (l +=
                    (f = a?.text) == null
                        ? void 0
                        : f.slice(Math.max(r, c) - c, o - c));
        }),
        l
    );
}
function vy(t, e) {
    const n = { from: 0, to: t.content.size };
    return Kd(t, n, e);
}
function Ud(t) {
    return Object.fromEntries(
        Object.entries(t.nodes)
            .filter(([, e]) => e.spec.toText)
            .map(([e, n]) => [e, n.spec.toText]),
    );
}
function My(t, e) {
    const n = ee(e, t.schema);
    const { from: r, to: o } = t.selection;
    const i = [];
    t.doc.nodesBetween(r, o, (l) => {
        i.push(l);
    });
    const s = i.reverse().find((l) => l.type.name === n.name);
    return s ? { ...s.attrs } : {};
}
function Qs(t, e) {
    const n = ao(typeof e === "string" ? e : e.name, t.schema);
    return n === "node" ? My(t, e) : n === "mark" ? Fd(t, e) : {};
}
function Ty(t, e = JSON.stringify) {
    const n = {};
    return t.filter((r) => {
        const o = e(r);
        return Object.prototype.hasOwnProperty.call(n, o) ? !1 : (n[o] = !0);
    });
}
function Ay(t) {
    const e = Ty(t);
    return e.length === 1
        ? e
        : e.filter(
              (n, r) =>
                  !e
                      .filter((i, s) => s !== r)
                      .some(
                          (i) =>
                              n.oldRange.from >= i.oldRange.from &&
                              n.oldRange.to <= i.oldRange.to &&
                              n.newRange.from >= i.newRange.from &&
                              n.newRange.to <= i.newRange.to,
                      ),
          );
}
function Zs(t) {
    const { mapping: e, steps: n } = t;
    const r = [];
    return (
        e.maps.forEach((o, i) => {
            const s = [];
            if (o.ranges.length) {
                o.forEach((l, a) => {
                    s.push({ from: l, to: a });
                });
            } else {
                const { from: l, to: a } = n[i];
                if (l === void 0 || a === void 0) return;
                s.push({ from: l, to: a });
            }
            s.forEach(({ from: l, to: a }) => {
                const c = e.slice(i).map(l, -1);
                const d = e.slice(i).map(a);
                const u = e.invert().map(c, -1);
                const f = e.invert().map(d);
                r.push({
                    oldRange: { from: u, to: f },
                    newRange: { from: c, to: d },
                });
            });
        }),
        Ay(r)
    );
}
function co(t, e, n) {
    const r = [];
    return (
        t === e
            ? n
                  .resolve(t)
                  .marks()
                  .forEach((o) => {
                      const i = n.resolve(t);
                      const s = Ks(i, o.type);
                      s && r.push({ mark: o, ...s });
                  })
            : n.nodesBetween(t, e, (o, i) => {
                  !o ||
                      o?.nodeSize === void 0 ||
                      r.push(
                          ...o.marks.map((s) => ({
                              from: i,
                              to: i + o.nodeSize,
                              mark: s,
                          })),
                      );
              }),
        r
    );
}
const qd = (t, e, n, r = 20) => {
    const o = t.doc.resolve(n);
    let i = r;
    let s = null;
    for (; i > 0 && s === null; ) {
        const l = o.node(i);
        l?.type.name === e ? (s = l) : (i -= 1);
    }
    return [s, i];
};
function Hs(t, e) {
    return e.nodes[t] || e.marks[t] || null;
}
function no(t, e, n) {
    return Object.fromEntries(
        Object.entries(n).filter(([r]) => {
            const o = t.find((i) => i.type === e && i.name === r);
            return o ? o.attribute.keepOnSplit : !1;
        }),
    );
}
const Ey = (t, e = 500) => {
    let n = "";
    const r = t.parentOffset;
    return (
        t.parent.nodesBetween(Math.max(0, r - e), r, (o, i, s, l) => {
            let a, c;
            const d =
                ((c = (a = o.type.spec).toText) == null
                    ? void 0
                    : c.call(a, { node: o, pos: i, parent: s, index: l })) ||
                o.textContent ||
                "%leaf%";
            n += o.isAtom && !o.isText ? d : d.slice(0, Math.max(0, r - i));
        }),
        n
    );
};
function Vs(t, e, n = {}) {
    const { empty: r, ranges: o } = t.selection;
    const i = e ? mt(e, t.schema) : null;
    if (r) {
        return !!(t.storedMarks || t.selection.$from.marks())
            .filter((u) => (i ? i.name === u.type.name : !0))
            .find((u) => ro(u.attrs, n, { strict: !1 }));
    }
    let s = 0;
    const l = [];
    if (
        (o.forEach(({ $from: u, $to: f }) => {
            const h = u.pos;
            const p = f.pos;
            t.doc.nodesBetween(h, p, (m, g) => {
                if (!m.isText && !m.marks.length) return;
                const y = Math.max(h, g);
                const b = Math.min(p, g + m.nodeSize);
                const k = b - y;
                ((s += k),
                    l.push(
                        ...m.marks.map((v) => ({ mark: v, from: y, to: b })),
                    ));
            });
        }),
        s === 0)
    ) {
        return !1;
    }
    const a = l
        .filter((u) => (i ? i.name === u.mark.type.name : !0))
        .filter((u) => ro(u.mark.attrs, n, { strict: !1 }))
        .reduce((u, f) => u + f.to - f.from, 0);
    const c = l
        .filter((u) => (i ? u.mark.type !== i && u.mark.type.excludes(i) : !0))
        .reduce((u, f) => u + f.to - f.from, 0);
    return (a > 0 ? a + c : a) >= s;
}
function el(t, e, n = {}) {
    if (!e) return Ge(t, null, n) || Vs(t, null, n);
    const r = ao(e, t.schema);
    return r === "node" ? Ge(t, e, n) : r === "mark" ? Vs(t, e, n) : !1;
}
const Jd = (t, e) => {
    const { $from: n, $to: r, $anchor: o } = t.selection;
    if (e) {
        const i = Xe((l) => l.type.name === e)(t.selection);
        if (!i) return !1;
        const s = t.doc.resolve(i.pos + 1);
        return o.pos + 1 === s.end();
    }
    return !(r.parentOffset < r.parent.nodeSize - 2 || n.pos !== r.pos);
};
const Gd = (t) => {
    const { $from: e, $to: n } = t.selection;
    return !(e.parentOffset > 0 || e.pos !== n.pos);
};
function Dd(t, e) {
    return Array.isArray(e)
        ? e.some((n) => (typeof n === "string" ? n : n.name) === t.name)
        : e;
}
function Id(t, e) {
    const { nodeExtensions: n } = er(e);
    const r = n.find((s) => s.name === t);
    if (!r) return !1;
    const o = { name: r.name, options: r.options, storage: r.storage };
    const i = J(z(r, "group", o));
    return typeof i !== "string" ? !1 : i.split(" ").includes("list");
}
function nr(t, { checkChildren: e = !0, ignoreWhitespace: n = !1 } = {}) {
    let r;
    if (n) {
        if (t.type.name === "hardBreak") return !0;
        if (t.isText) return /^\s*$/m.test((r = t.text) != null ? r : "");
    }
    if (t.isText) return !t.text;
    if (t.isAtom || t.isLeaf) return !1;
    if (t.content.childCount === 0) return !0;
    if (e) {
        let o = !0;
        return (
            t.content.forEach((i) => {
                o !== !1 &&
                    (nr(i, { ignoreWhitespace: n, checkChildren: e }) ||
                        (o = !1));
            }),
            o
        );
    }
    return !1;
}
function uo(t) {
    return t instanceof P;
}
function Xd(t, e, n) {
    const o = t.state.doc.content.size;
    const i = pt(e, 0, o);
    const s = pt(n, 0, o);
    const l = t.coordsAtPos(i);
    const a = t.coordsAtPos(s, -1);
    const c = Math.min(l.top, a.top);
    const d = Math.max(l.bottom, a.bottom);
    const u = Math.min(l.left, a.left);
    const f = Math.max(l.right, a.right);
    const h = f - u;
    const p = d - c;
    const y = {
        top: c,
        bottom: d,
        left: u,
        right: f,
        width: h,
        height: p,
        x: u,
        y: c,
    };
    return { ...y, toJSON: () => y };
}
function Ny(t, e, n) {
    let r;
    const { selection: o } = e;
    let i = null;
    if ((lo(o) && (i = o.$cursor), i)) {
        const l = (r = t.storedMarks) != null ? r : i.marks();
        return (
            i.parent.type.allowsMarkType(n) &&
            (!!n.isInSet(l) || !l.some((c) => c.type.excludes(n)))
        );
    }
    const { ranges: s } = o;
    return s.some(({ $from: l, $to: a }) => {
        let c =
            l.depth === 0
                ? t.doc.inlineContent && t.doc.type.allowsMarkType(n)
                : !1;
        return (
            t.doc.nodesBetween(l.pos, a.pos, (d, u, f) => {
                if (c) return !1;
                if (d.isInline) {
                    const h = !f || f.type.allowsMarkType(n);
                    const p =
                        !!n.isInSet(d.marks) ||
                        !d.marks.some((m) => m.type.excludes(n));
                    c = h && p;
                }
                return !c;
            }),
            c
        );
    });
}
var Oy =
    (t, e = {}) =>
    ({ tr: n, state: r, dispatch: o }) => {
        const { selection: i } = n;
        const { empty: s, ranges: l } = i;
        const a = mt(t, r.schema);
        if (o) {
            if (s) {
                const c = Fd(r, a);
                n.addStoredMark(a.create({ ...c, ...e }));
            } else {
                l.forEach((c) => {
                    const d = c.$from.pos;
                    const u = c.$to.pos;
                    r.doc.nodesBetween(d, u, (f, h) => {
                        const p = Math.max(h, d);
                        const m = Math.min(h + f.nodeSize, u);
                        f.marks.find((y) => y.type === a)
                            ? f.marks.forEach((y) => {
                                  a === y.type &&
                                      n.addMark(
                                          p,
                                          m,
                                          a.create({ ...y.attrs, ...e }),
                                      );
                              })
                            : n.addMark(p, m, a.create(e));
                    });
                });
            }
        }
        return Ny(r, n, a);
    };
var Ry =
    (t, e) =>
    ({ tr: n }) => (n.setMeta(t, e), !0);
var Dy =
    (t, e = {}) =>
    ({ state: n, dispatch: r, chain: o }) => {
        const i = ee(t, n.schema);
        let s;
        return (
            n.selection.$anchor.sameParent(n.selection.$head) &&
                (s = n.selection.$anchor.parent.attrs),
            i.isTextblock
                ? o()
                      .command(({ commands: l }) =>
                          rs(i, { ...s, ...e })(n) ? !0 : l.clearNodes(),
                      )
                      .command(({ state: l }) => rs(i, { ...s, ...e })(l, r))
                      .run()
                : (console.warn(
                      '[tiptap warn]: Currently "setNode()" only supports text block nodes.',
                  ),
                  !1)
        );
    };
var Iy =
    (t) =>
    ({ tr: e, dispatch: n }) => {
        if (n) {
            const { doc: r } = e;
            const o = pt(t, 0, r.content.size);
            const i = P.create(r, o);
            e.setSelection(i);
        }
        return !0;
    };
var Py =
    (t) =>
    ({ tr: e, dispatch: n }) => {
        if (n) {
            const { doc: r } = e;
            const { from: o, to: i } =
                typeof t === "number" ? { from: t, to: t } : t;
            const s = R.atStart(r).from;
            const l = R.atEnd(r).to;
            const a = pt(o, s, l);
            const c = pt(i, s, l);
            const d = R.create(r, a, c);
            e.setSelection(d);
        }
        return !0;
    };
var Ly =
    (t) =>
    ({ state: e, dispatch: n }) => {
        const r = ee(t, e.schema);
        return gc(r)(e, n);
    };
function Pd(t, e) {
    const n =
        t.storedMarks ||
        (t.selection.$to.parentOffset && t.selection.$from.marks());
    if (n) {
        const r = n.filter((o) => e?.includes(o.type.name));
        t.tr.ensureMarks(r);
    }
}
var zy =
    ({ keepMarks: t = !0 } = {}) =>
    ({ tr: e, state: n, dispatch: r, editor: o }) => {
        const { selection: i, doc: s } = e;
        const { $from: l, $to: a } = i;
        const c = o.extensionManager.attributes;
        const d = no(c, l.node().type.name, l.node().attrs);
        if (i instanceof P && i.node.isBlock) {
            return !l.parentOffset || !Ae(s, l.pos)
                ? !1
                : (r &&
                      (t && Pd(n, o.extensionManager.splittableMarks),
                      e.split(l.pos).scrollIntoView()),
                  !0);
        }
        if (!l.parent.isBlock) return !1;
        const u = a.parentOffset === a.parent.content.size;
        const f =
            l.depth === 0
                ? void 0
                : tr(l.node(-1).contentMatchAt(l.indexAfter(-1)));
        let h = u && f ? [{ type: f, attrs: d }] : void 0;
        let p = Ae(e.doc, e.mapping.map(l.pos), 1, h);
        if (
            (!h &&
                !p &&
                Ae(
                    e.doc,
                    e.mapping.map(l.pos),
                    1,
                    f ? [{ type: f }] : void 0,
                ) &&
                ((p = !0), (h = f ? [{ type: f, attrs: d }] : void 0)),
            r)
        ) {
            if (
                p &&
                (i instanceof R && e.deleteSelection(),
                e.split(e.mapping.map(l.pos), 1, h),
                f && !u && !l.parentOffset && l.parent.type !== f)
            ) {
                const m = e.mapping.map(l.before());
                const g = e.doc.resolve(m);
                l.node(-1).canReplaceWith(g.index(), g.index() + 1, f) &&
                    e.setNodeMarkup(e.mapping.map(l.before()), f);
            }
            (t && Pd(n, o.extensionManager.splittableMarks),
                e.scrollIntoView());
        }
        return p;
    };
var By =
    (t, e = {}) =>
    ({ tr: n, state: r, dispatch: o, editor: i }) => {
        let s;
        const l = ee(t, r.schema);
        const { $from: a, $to: c } = r.selection;
        const d = r.selection.node;
        if ((d && d.isBlock) || a.depth < 2 || !a.sameParent(c)) return !1;
        const u = a.node(-1);
        if (u.type !== l) return !1;
        const f = i.extensionManager.attributes;
        if (
            a.parent.content.size === 0 &&
            a.node(-1).childCount === a.indexAfter(-1)
        ) {
            if (
                a.depth === 2 ||
                a.node(-3).type !== l ||
                a.index(-2) !== a.node(-2).childCount - 1
            ) {
                return !1;
            }
            if (o) {
                let y = C.empty;
                const b = a.index(-1) ? 1 : a.index(-2) ? 2 : 3;
                for (let N = a.depth - b; N >= a.depth - 3; N -= 1) {
                    y = C.from(a.node(N).copy(y));
                }
                const k =
                    a.indexAfter(-1) < a.node(-2).childCount
                        ? 1
                        : a.indexAfter(-2) < a.node(-3).childCount
                          ? 2
                          : 3;
                const v = {
                    ...no(f, a.node().type.name, a.node().attrs),
                    ...e,
                };
                const x =
                    ((s = l.contentMatch.defaultType) == null
                        ? void 0
                        : s.createAndFill(v)) || void 0;
                y = y.append(C.from(l.createAndFill(null, x) || void 0));
                const S = a.before(a.depth - (b - 1));
                n.replace(S, a.after(-k), new E(y, 4 - b, 0));
                let w = -1;
                (n.doc.nodesBetween(S, n.doc.content.size, (N, D) => {
                    if (w > -1) return !1;
                    N.isTextblock && N.content.size === 0 && (w = D + 1);
                }),
                    w > -1 && n.setSelection(R.near(n.doc.resolve(w))),
                    n.scrollIntoView());
            }
            return !0;
        }
        const h = c.pos === a.end() ? u.contentMatchAt(0).defaultType : null;
        const p = { ...no(f, u.type.name, u.attrs), ...e };
        const m = { ...no(f, a.node().type.name, a.node().attrs), ...e };
        n.delete(a.pos, c.pos);
        const g = h
            ? [
                  { type: l, attrs: p },
                  { type: h, attrs: m },
              ]
            : [{ type: l, attrs: p }];
        if (!Ae(n.doc, a.pos, 2)) return !1;
        if (o) {
            const { selection: y, storedMarks: b } = r;
            const { splittableMarks: k } = i.extensionManager;
            const v = b || (y.$to.parentOffset && y.$from.marks());
            if ((n.split(a.pos, 2, g).scrollIntoView(), !v || !o)) return !0;
            const x = v.filter((S) => k.includes(S.type.name));
            n.ensureMarks(x);
        }
        return !0;
    };
const $s = (t, e) => {
    const n = Xe((s) => s.type === e)(t.selection);
    if (!n) return !0;
    const r = t.doc.resolve(Math.max(0, n.pos - 1)).before(n.depth);
    if (r === void 0) return !0;
    const o = t.doc.nodeAt(r);
    return (n.node.type === o?.type && Oe(t.doc, n.pos) && t.join(n.pos), !0);
};
const Fs = (t, e) => {
    const n = Xe((s) => s.type === e)(t.selection);
    if (!n) return !0;
    const r = t.doc.resolve(n.start).after(n.depth);
    if (r === void 0) return !0;
    const o = t.doc.nodeAt(r);
    return (n.node.type === o?.type && Oe(t.doc, r) && t.join(r), !0);
};
var Hy =
    (t, e, n, r = {}) =>
    ({
        editor: o,
        tr: i,
        state: s,
        dispatch: l,
        chain: a,
        commands: c,
        can: d,
    }) => {
        const { extensions: u, splittableMarks: f } = o.extensionManager;
        const h = ee(t, s.schema);
        const p = ee(e, s.schema);
        const { selection: m, storedMarks: g } = s;
        const { $from: y, $to: b } = m;
        const k = y.blockRange(b);
        const v = g || (m.$to.parentOffset && m.$from.marks());
        if (!k) return !1;
        const x = Xe((S) => Id(S.type.name, u))(m);
        if (k.depth >= 1 && x && k.depth - x.depth <= 1) {
            if (x.node.type === h) return c.liftListItem(p);
            if (
                Id(x.node.type.name, u) &&
                h.validContent(x.node.content) &&
                l
            ) {
                return a()
                    .command(() => (i.setNodeMarkup(x.pos, h), !0))
                    .command(() => $s(i, h))
                    .command(() => Fs(i, h))
                    .run();
            }
        }
        return !n || !v || !l
            ? a()
                  .command(() => (d().wrapInList(h, r) ? !0 : c.clearNodes()))
                  .wrapInList(h, r)
                  .command(() => $s(i, h))
                  .command(() => Fs(i, h))
                  .run()
            : a()
                  .command(() => {
                      const S = d().wrapInList(h, r);
                      const w = v.filter((N) => f.includes(N.type.name));
                      return (i.ensureMarks(w), S ? !0 : c.clearNodes());
                  })
                  .wrapInList(h, r)
                  .command(() => $s(i, h))
                  .command(() => Fs(i, h))
                  .run();
    };
var $y =
    (t, e = {}, n = {}) =>
    ({ state: r, commands: o }) => {
        const { extendEmptyMarkRange: i = !1 } = n;
        const s = mt(t, r.schema);
        return Vs(r, s, e)
            ? o.unsetMark(s, { extendEmptyMarkRange: i })
            : o.setMark(s, e);
    };
var Fy =
    (t, e, n = {}) =>
    ({ state: r, commands: o }) => {
        const i = ee(t, r.schema);
        const s = ee(e, r.schema);
        const l = Ge(r, i, n);
        let a;
        return (
            r.selection.$anchor.sameParent(r.selection.$head) &&
                (a = r.selection.$anchor.parent.attrs),
            l ? o.setNode(s, a) : o.setNode(i, { ...a, ...n })
        );
    };
var _y =
    (t, e = {}) =>
    ({ state: n, commands: r }) => {
        const o = ee(t, n.schema);
        return Ge(n, o, e) ? r.lift(o) : r.wrapIn(o, e);
    };
var Vy =
    () =>
    ({ state: t, dispatch: e }) => {
        const n = t.plugins;
        for (let r = 0; r < n.length; r += 1) {
            const o = n[r];
            let i;
            if (o.spec.isInputRules && (i = o.getState(t))) {
                if (e) {
                    const s = t.tr;
                    const l = i.transform;
                    for (let a = l.steps.length - 1; a >= 0; a -= 1) {
                        s.step(l.steps[a].invert(l.docs[a]));
                    }
                    if (i.text) {
                        const a = s.doc.resolve(i.from).marks();
                        s.replaceWith(i.from, i.to, t.schema.text(i.text, a));
                    } else s.delete(i.from, i.to);
                }
                return !0;
            }
        }
        return !1;
    };
var Wy =
    () =>
    ({ tr: t, dispatch: e }) => {
        const { selection: n } = t;
        const { empty: r, ranges: o } = n;
        return (
            r ||
                (e &&
                    o.forEach((i) => {
                        t.removeMark(i.$from.pos, i.$to.pos);
                    })),
            !0
        );
    };
var jy =
    (t, e = {}) =>
    ({ tr: n, state: r, dispatch: o }) => {
        let i;
        const { extendEmptyMarkRange: s = !1 } = e;
        const { selection: l } = n;
        const a = mt(t, r.schema);
        const { $from: c, empty: d, ranges: u } = l;
        if (!o) return !0;
        if (d && s) {
            let { from: f, to: h } = l;
            const p =
                (i = c.marks().find((g) => g.type === a)) == null
                    ? void 0
                    : i.attrs;
            const m = Ks(c, a, p);
            (m && ((f = m.from), (h = m.to)), n.removeMark(f, h, a));
        } else {
            u.forEach((f) => {
                n.removeMark(f.$from.pos, f.$to.pos, a);
            });
        }
        return (n.removeStoredMark(a), !0);
    };
var Ky =
    (t, e = {}) =>
    ({ tr: n, state: r, dispatch: o }) => {
        let i = null;
        let s = null;
        const l = ao(typeof t === "string" ? t : t.name, r.schema);
        return l
            ? (l === "node" && (i = ee(t, r.schema)),
              l === "mark" && (s = mt(t, r.schema)),
              o &&
                  n.selection.ranges.forEach((a) => {
                      const c = a.$from.pos;
                      const d = a.$to.pos;
                      let u;
                      let f;
                      let h;
                      let p;
                      (n.selection.empty
                          ? r.doc.nodesBetween(c, d, (m, g) => {
                                i &&
                                    i === m.type &&
                                    ((h = Math.max(g, c)),
                                    (p = Math.min(g + m.nodeSize, d)),
                                    (u = g),
                                    (f = m));
                            })
                          : r.doc.nodesBetween(c, d, (m, g) => {
                                (g < c &&
                                    i &&
                                    i === m.type &&
                                    ((h = Math.max(g, c)),
                                    (p = Math.min(g + m.nodeSize, d)),
                                    (u = g),
                                    (f = m)),
                                    g >= c &&
                                        g <= d &&
                                        (i &&
                                            i === m.type &&
                                            n.setNodeMarkup(g, void 0, {
                                                ...m.attrs,
                                                ...e,
                                            }),
                                        s &&
                                            m.marks.length &&
                                            m.marks.forEach((y) => {
                                                if (s === y.type) {
                                                    const b = Math.max(g, c);
                                                    const k = Math.min(
                                                        g + m.nodeSize,
                                                        d,
                                                    );
                                                    n.addMark(
                                                        b,
                                                        k,
                                                        s.create({
                                                            ...y.attrs,
                                                            ...e,
                                                        }),
                                                    );
                                                }
                                            })));
                            }),
                          f &&
                              (u !== void 0 &&
                                  n.setNodeMarkup(u, void 0, {
                                      ...f.attrs,
                                      ...e,
                                  }),
                              s &&
                                  f.marks.length &&
                                  f.marks.forEach((m) => {
                                      s === m.type &&
                                          n.addMark(
                                              h,
                                              p,
                                              s.create({ ...m.attrs, ...e }),
                                          );
                                  })));
                  }),
              !0)
            : !1;
    };
var Uy =
    (t, e = {}) =>
    ({ state: n, dispatch: r }) => {
        const o = ee(t, n.schema);
        return hc(o, e)(n, r);
    };
var qy =
    (t, e = {}) =>
    ({ state: n, dispatch: r }) => {
        const o = ee(t, n.schema);
        return pc(o, e)(n, r);
    };
const Jy = class {
    constructor() {
        this.callbacks = {};
    }

    on(t, e) {
        return (
            this.callbacks[t] || (this.callbacks[t] = []),
            this.callbacks[t].push(e),
            this
        );
    }

    emit(t, ...e) {
        const n = this.callbacks[t];
        return (n && n.forEach((r) => r.apply(this, e)), this);
    }

    off(t, e) {
        const n = this.callbacks[t];
        return (
            n &&
                (e
                    ? (this.callbacks[t] = n.filter((r) => r !== e))
                    : delete this.callbacks[t]),
            this
        );
    }

    once(t, e) {
        const n = (...r) => {
            (this.off(t, n), e.apply(this, r));
        };
        return this.on(t, n);
    }

    removeAllListeners() {
        this.callbacks = {};
    }
};
const fo = class {
    constructor(t) {
        let e;
        ((this.find = t.find),
            (this.handler = t.handler),
            (this.undoable = (e = t.undoable) != null ? e : !0));
    }
};
const Gy = (t, e) => {
    if (js(e)) return e.exec(t);
    const n = e(t);
    if (!n) return null;
    const r = [n.text];
    return (
        (r.index = n.index),
        (r.input = t),
        (r.data = n.data),
        n.replaceWith &&
            (n.text.includes(n.replaceWith) ||
                console.warn(
                    '[tiptap warn]: "inputRuleMatch.replaceWith" must be part of "inputRuleMatch.text".',
                ),
            r.push(n.replaceWith)),
        r
    );
};
function Zr(t) {
    let e;
    const { editor: n, from: r, to: o, text: i, rules: s, plugin: l } = t;
    const { view: a } = n;
    if (a.composing) return !1;
    const c = a.state.doc.resolve(r);
    if (
        c.parent.type.spec.code ||
        ((e = c.nodeBefore || c.nodeAfter) != null &&
            e.marks.find((f) => f.type.spec.code))
    ) {
        return !1;
    }
    let d = !1;
    const u = Ey(c) + i;
    return (
        s.forEach((f) => {
            if (d) return;
            const h = Gy(u, f.find);
            if (!h) return;
            const p = a.state.tr;
            const m = io({ state: a.state, transaction: p });
            const g = { from: r - (h[0].length - i.length), to: o };
            const {
                commands: y,
                chain: b,
                can: k,
            } = new so({ editor: n, state: m });
            f.handler({
                state: m,
                range: g,
                match: h,
                commands: y,
                chain: b,
                can: k,
            }) === null ||
                !p.steps.length ||
                (f.undoable &&
                    p.setMeta(l, { transform: p, from: r, to: o, text: i }),
                a.dispatch(p),
                (d = !0));
        }),
        d
    );
}
function Xy(t) {
    const { editor: e, rules: n } = t;
    const r = new L({
        state: {
            init() {
                return null;
            },
            apply(o, i, s) {
                const l = o.getMeta(r);
                if (l) return l;
                const a = o.getMeta("applyInputRules");
                return (
                    !!a &&
                        setTimeout(() => {
                            let { text: d } = a;
                            typeof d === "string"
                                ? (d = d)
                                : (d = Xs(C.from(d), s.schema));
                            const { from: u } = a;
                            const f = u + d.length;
                            Zr({
                                editor: e,
                                from: u,
                                to: f,
                                text: d,
                                rules: n,
                                plugin: r,
                            });
                        }),
                    o.selectionSet || o.docChanged ? null : i
                );
            },
        },
        props: {
            handleTextInput(o, i, s, l) {
                return Zr({
                    editor: e,
                    from: i,
                    to: s,
                    text: l,
                    rules: n,
                    plugin: r,
                });
            },
            handleDOMEvents: {
                compositionend: (o) => (
                    setTimeout(() => {
                        const { $cursor: i } = o.state.selection;
                        i &&
                            Zr({
                                editor: e,
                                from: i.pos,
                                to: i.pos,
                                text: "",
                                rules: n,
                                plugin: r,
                            });
                    }),
                    !1
                ),
            },
            handleKeyDown(o, i) {
                if (i.key !== "Enter") return !1;
                const { $cursor: s } = o.state.selection;
                return s
                    ? Zr({
                          editor: e,
                          from: s.pos,
                          to: s.pos,
                          text: `
`,
                          rules: n,
                          plugin: r,
                      })
                    : !1;
            },
        },
        isInputRules: !0,
    });
    return r;
}
function Yy(t) {
    return Object.prototype.toString.call(t).slice(8, -1);
}
function eo(t) {
    return Yy(t) !== "Object"
        ? !1
        : t.constructor === Object &&
              Object.getPrototypeOf(t) === Object.prototype;
}
function Yd(t, e) {
    const n = { ...t };
    return (
        eo(t) &&
            eo(e) &&
            Object.keys(e).forEach((r) => {
                eo(e[r]) && eo(t[r]) ? (n[r] = Yd(t[r], e[r])) : (n[r] = e[r]);
            }),
        n
    );
}
const tl = class {
    constructor(t = {}) {
        ((this.type = "extendable"),
            (this.parent = null),
            (this.child = null),
            (this.name = ""),
            (this.config = { name: this.name }),
            (this.config = { ...this.config, ...t }),
            (this.name = this.config.name));
    }

    get options() {
        return { ...(J(z(this, "addOptions", { name: this.name })) || {}) };
    }

    get storage() {
        return {
            ...(J(
                z(this, "addStorage", {
                    name: this.name,
                    options: this.options,
                }),
            ) || {}),
        };
    }

    configure(t = {}) {
        const e = this.extend({
            ...this.config,
            addOptions: () => Yd(this.options, t),
        });
        return ((e.name = this.name), (e.parent = this.parent), e);
    }

    extend(t = {}) {
        const e = new this.constructor({ ...this.config, ...t });
        return (
            (e.parent = this),
            (this.child = e),
            (e.name = "name" in t ? t.name : e.parent.name),
            e
        );
    }
};
const Z = class Qd extends tl {
    constructor() {
        (super(...arguments), (this.type = "mark"));
    }

    static create(e = {}) {
        const n = typeof e === "function" ? e() : e;
        return new Qd(n);
    }

    static handleExit({ editor: e, mark: n }) {
        const { tr: r } = e.state;
        const o = e.state.selection.$from;
        if (o.pos === o.end()) {
            const s = o.marks();
            if (!s.find((c) => c?.type.name === n.name)) return !1;
            const a = s.find((c) => c?.type.name === n.name);
            return (
                a && r.removeStoredMark(a),
                r.insertText(" ", o.pos),
                e.view.dispatch(r),
                !0
            );
        }
        return !1;
    }

    configure(e) {
        return super.configure(e);
    }

    extend(e) {
        const n = typeof e === "function" ? e() : e;
        return super.extend(n);
    }
};
function Qy(t) {
    return typeof t === "number";
}
const Zy = class {
    constructor(t) {
        ((this.find = t.find), (this.handler = t.handler));
    }
};
const e0 = (t, e, n) => {
    if (js(e)) return [...t.matchAll(e)];
    const r = e(t, n);
    return r
        ? r.map((o) => {
              const i = [o.text];
              return (
                  (i.index = o.index),
                  (i.input = t),
                  (i.data = o.data),
                  o.replaceWith &&
                      (o.text.includes(o.replaceWith) ||
                          console.warn(
                              '[tiptap warn]: "pasteRuleMatch.replaceWith" must be part of "pasteRuleMatch.text".',
                          ),
                      i.push(o.replaceWith)),
                  i
              );
          })
        : [];
};
function t0(t) {
    const {
        editor: e,
        state: n,
        from: r,
        to: o,
        rule: i,
        pasteEvent: s,
        dropEvent: l,
    } = t;
    const { commands: a, chain: c, can: d } = new so({ editor: e, state: n });
    const u = [];
    return (
        n.doc.nodesBetween(r, o, (h, p) => {
            let m, g, y, b, k;
            if (
                ((g = (m = h.type) == null ? void 0 : m.spec) != null &&
                    g.code) ||
                !(h.isText || h.isTextblock || h.isInline)
            ) {
                return;
            }
            const v =
                (k =
                    (b = (y = h.content) == null ? void 0 : y.size) != null
                        ? b
                        : h.nodeSize) != null
                    ? k
                    : 0;
            const x = Math.max(r, p);
            const S = Math.min(o, p + v);
            if (x >= S) return;
            const w = h.isText
                ? h.text || ""
                : h.textBetween(x - p, S - p, void 0, "\uFFFC");
            e0(w, i.find, s).forEach((D) => {
                if (D.index === void 0) return;
                const M = x + D.index + 1;
                const B = M + D[0].length;
                const F = {
                    from: n.tr.mapping.map(M),
                    to: n.tr.mapping.map(B),
                };
                const K = i.handler({
                    state: n,
                    range: F,
                    match: D,
                    commands: a,
                    chain: c,
                    can: d,
                    pasteEvent: s,
                    dropEvent: l,
                });
                u.push(K);
            });
        }),
        u.every((h) => h !== null)
    );
}
let to = null;
const n0 = (t) => {
    let e;
    const n = new ClipboardEvent("paste", {
        clipboardData: new DataTransfer(),
    });
    return ((e = n.clipboardData) == null || e.setData("text/html", t), n);
};
function r0(t) {
    const { editor: e, rules: n } = t;
    let r = null;
    let o = !1;
    let i = !1;
    let s = typeof ClipboardEvent < "u" ? new ClipboardEvent("paste") : null;
    let l;
    try {
        l = typeof DragEvent < "u" ? new DragEvent("drop") : null;
    } catch {
        l = null;
    }
    const a = ({ state: d, from: u, to: f, rule: h, pasteEvt: p }) => {
        const m = d.tr;
        const g = io({ state: d, transaction: m });
        if (
            !(
                !t0({
                    editor: e,
                    state: g,
                    from: Math.max(u - 1, 0),
                    to: f.b - 1,
                    rule: h,
                    pasteEvent: p,
                    dropEvent: l,
                }) || !m.steps.length
            )
        ) {
            try {
                l = typeof DragEvent < "u" ? new DragEvent("drop") : null;
            } catch {
                l = null;
            }
            return (
                (s =
                    typeof ClipboardEvent < "u"
                        ? new ClipboardEvent("paste")
                        : null),
                m
            );
        }
    };
    return n.map(
        (d) =>
            new L({
                view(u) {
                    const f = (p) => {
                        let m;
                        ((r =
                            (m = u.dom.parentElement) != null &&
                            m.contains(p.target)
                                ? u.dom.parentElement
                                : null),
                            r && (to = e));
                    };
                    const h = () => {
                        to && (to = null);
                    };
                    return (
                        window.addEventListener("dragstart", f),
                        window.addEventListener("dragend", h),
                        {
                            destroy() {
                                (window.removeEventListener("dragstart", f),
                                    window.removeEventListener("dragend", h));
                            },
                        }
                    );
                },
                props: {
                    handleDOMEvents: {
                        drop: (u, f) => {
                            if (
                                ((i = r === u.dom.parentElement), (l = f), !i)
                            ) {
                                const h = to;
                                h?.isEditable &&
                                    setTimeout(() => {
                                        const p = h.state.selection;
                                        p &&
                                            h.commands.deleteRange({
                                                from: p.from,
                                                to: p.to,
                                            });
                                    }, 10);
                            }
                            return !1;
                        },
                        paste: (u, f) => {
                            let h;
                            const p =
                                (h = f.clipboardData) == null
                                    ? void 0
                                    : h.getData("text/html");
                            return (
                                (s = f),
                                (o = !!p?.includes("data-pm-slice")),
                                !1
                            );
                        },
                    },
                },
                appendTransaction: (u, f, h) => {
                    const p = u[0];
                    const m = p.getMeta("uiEvent") === "paste" && !o;
                    const g = p.getMeta("uiEvent") === "drop" && !i;
                    const y = p.getMeta("applyPasteRules");
                    const b = !!y;
                    if (!m && !g && !b) return;
                    if (b) {
                        let { text: x } = y;
                        typeof x === "string"
                            ? (x = x)
                            : (x = Xs(C.from(x), h.schema));
                        const { from: S } = y;
                        const w = S + x.length;
                        const N = n0(x);
                        return a({
                            rule: d,
                            state: h,
                            from: S,
                            to: { b: w },
                            pasteEvt: N,
                        });
                    }
                    const k = f.doc.content.findDiffStart(h.doc.content);
                    const v = f.doc.content.findDiffEnd(h.doc.content);
                    if (!(!Qy(k) || !v || k === v.b)) {
                        return a({
                            rule: d,
                            state: h,
                            from: k,
                            to: v,
                            pasteEvt: s,
                        });
                    }
                },
            }),
    );
}
const ho = class {
    constructor(t, e) {
        ((this.splittableMarks = []),
            (this.editor = e),
            (this.baseExtensions = t),
            (this.extensions = jd(t)),
            (this.schema = Sy(this.extensions, e)),
            this.setupExtensions());
    }

    get commands() {
        return this.extensions.reduce((t, e) => {
            const n = {
                name: e.name,
                options: e.options,
                storage: this.editor.extensionStorage[e.name],
                editor: this.editor,
                type: Hs(e.name, this.schema),
            };
            const r = z(e, "addCommands", n);
            return r ? { ...t, ...r() } : t;
        }, {});
    }

    get plugins() {
        const { editor: t } = this;
        return Ys([...this.extensions].reverse()).flatMap((r) => {
            const o = {
                name: r.name,
                options: r.options,
                storage: this.editor.extensionStorage[r.name],
                editor: t,
                type: Hs(r.name, this.schema),
            };
            const i = [];
            const s = z(r, "addKeyboardShortcuts", o);
            let l = {};
            if (
                (r.type === "mark" &&
                    z(r, "exitable", o) &&
                    (l.ArrowRight = () => Z.handleExit({ editor: t, mark: r })),
                s)
            ) {
                const f = Object.fromEntries(
                    Object.entries(s()).map(([h, p]) => [
                        h,
                        () => p({ editor: t }),
                    ]),
                );
                l = { ...l, ...f };
            }
            const a = Ad(l);
            i.push(a);
            const c = z(r, "addInputRules", o);
            if (Dd(r, t.options.enableInputRules) && c) {
                const f = c();
                if (f && f.length) {
                    const h = Xy({ editor: t, rules: f });
                    const p = Array.isArray(h) ? h : [h];
                    i.push(...p);
                }
            }
            const d = z(r, "addPasteRules", o);
            if (Dd(r, t.options.enablePasteRules) && d) {
                const f = d();
                if (f && f.length) {
                    const h = r0({ editor: t, rules: f });
                    i.push(...h);
                }
            }
            const u = z(r, "addProseMirrorPlugins", o);
            if (u) {
                const f = u();
                i.push(...f);
            }
            return i;
        });
    }

    get attributes() {
        return Wd(this.extensions);
    }

    get nodeViews() {
        const { editor: t } = this;
        const { nodeExtensions: e } = er(this.extensions);
        return Object.fromEntries(
            e
                .filter((n) => !!z(n, "addNodeView"))
                .map((n) => {
                    const r = this.attributes.filter((a) => a.type === n.name);
                    const o = {
                        name: n.name,
                        options: n.options,
                        storage: this.editor.extensionStorage[n.name],
                        editor: t,
                        type: ee(n.name, this.schema),
                    };
                    const i = z(n, "addNodeView", o);
                    if (!i) return [];
                    const s = i();
                    if (!s) return [];
                    const l = (a, c, d, u, f) => {
                        const h = oo(a, r);
                        return s({
                            node: a,
                            view: c,
                            getPos: d,
                            decorations: u,
                            innerDecorations: f,
                            editor: t,
                            extension: n,
                            HTMLAttributes: h,
                        });
                    };
                    return [n.name, l];
                }),
        );
    }

    get markViews() {
        const { editor: t } = this;
        const { markExtensions: e } = er(this.extensions);
        return Object.fromEntries(
            e
                .filter((n) => !!z(n, "addMarkView"))
                .map((n) => {
                    const r = this.attributes.filter((l) => l.type === n.name);
                    const o = {
                        name: n.name,
                        options: n.options,
                        storage: this.editor.extensionStorage[n.name],
                        editor: t,
                        type: mt(n.name, this.schema),
                    };
                    const i = z(n, "addMarkView", o);
                    if (!i) return [];
                    const s = (l, a, c) => {
                        const d = oo(l, r);
                        return i()({
                            mark: l,
                            view: a,
                            inline: c,
                            editor: t,
                            extension: n,
                            HTMLAttributes: d,
                            updateAttributes: (u) => {
                                p0(l, t, u);
                            },
                        });
                    };
                    return [n.name, s];
                }),
        );
    }

    setupExtensions() {
        const t = this.extensions;
        ((this.editor.extensionStorage = Object.fromEntries(
            t.map((e) => [e.name, e.storage]),
        )),
            t.forEach((e) => {
                let n;
                const r = {
                    name: e.name,
                    options: e.options,
                    storage: this.editor.extensionStorage[e.name],
                    editor: this.editor,
                    type: Hs(e.name, this.schema),
                };
                e.type === "mark" &&
                    ((n = J(z(e, "keepOnSplit", r))) == null || n) &&
                    this.splittableMarks.push(e.name);
                const o = z(e, "onBeforeCreate", r);
                const i = z(e, "onCreate", r);
                const s = z(e, "onUpdate", r);
                const l = z(e, "onSelectionUpdate", r);
                const a = z(e, "onTransaction", r);
                const c = z(e, "onFocus", r);
                const d = z(e, "onBlur", r);
                const u = z(e, "onDestroy", r);
                (o && this.editor.on("beforeCreate", o),
                    i && this.editor.on("create", i),
                    s && this.editor.on("update", s),
                    l && this.editor.on("selectionUpdate", l),
                    a && this.editor.on("transaction", a),
                    c && this.editor.on("focus", c),
                    d && this.editor.on("blur", d),
                    u && this.editor.on("destroy", u));
            }));
    }
};
ho.resolve = jd;
ho.sort = Ys;
ho.flatten = Gs;
const o0 = {};
Ws(o0, {
    ClipboardTextSerializer: () => eu,
    Commands: () => tu,
    Delete: () => nu,
    Drop: () => ru,
    Editable: () => ou,
    FocusEvents: () => su,
    Keymap: () => lu,
    Paste: () => au,
    Tabindex: () => cu,
    focusEventsPluginKey: () => iu,
});
const j = class Zd extends tl {
    constructor() {
        (super(...arguments), (this.type = "extension"));
    }

    static create(e = {}) {
        const n = typeof e === "function" ? e() : e;
        return new Zd(n);
    }

    configure(e) {
        return super.configure(e);
    }

    extend(e) {
        const n = typeof e === "function" ? e() : e;
        return super.extend(n);
    }
};
var eu = j.create({
    name: "clipboardTextSerializer",
    addOptions() {
        return { blockSeparator: void 0 };
    },
    addProseMirrorPlugins() {
        return [
            new L({
                key: new H("clipboardTextSerializer"),
                props: {
                    clipboardTextSerializer: () => {
                        const { editor: t } = this;
                        const { state: e, schema: n } = t;
                        const { doc: r, selection: o } = e;
                        const { ranges: i } = o;
                        const s = Math.min(...i.map((d) => d.$from.pos));
                        const l = Math.max(...i.map((d) => d.$to.pos));
                        const a = Ud(n);
                        return Kd(
                            r,
                            { from: s, to: l },
                            {
                                ...(this.options.blockSeparator !== void 0
                                    ? {
                                          blockSeparator:
                                              this.options.blockSeparator,
                                      }
                                    : {}),
                                textSerializers: a,
                            },
                        );
                    },
                },
            }),
        ];
    },
});
var tu = j.create({
    name: "commands",
    addCommands() {
        return { ...Ld };
    },
});
var nu = j.create({
    name: "delete",
    onUpdate({ transaction: t, appendedTransactions: e }) {
        let n, r, o;
        const i = () => {
            let s, l, a, c;
            if (
                (c =
                    (a =
                        (l =
                            (s = this.editor.options.coreExtensionOptions) ==
                            null
                                ? void 0
                                : s.delete) == null
                            ? void 0
                            : l.filterTransaction) == null
                        ? void 0
                        : a.call(l, t)) != null
                    ? c
                    : t.getMeta("y-sync$")
            ) {
                return;
            }
            const d = qs(t.before, [t, ...e]);
            Zs(d).forEach((h) => {
                d.mapping.mapResult(h.oldRange.from).deletedAfter &&
                    d.mapping.mapResult(h.oldRange.to).deletedBefore &&
                    d.before.nodesBetween(
                        h.oldRange.from,
                        h.oldRange.to,
                        (p, m) => {
                            const g = m + p.nodeSize - 2;
                            const y =
                                h.oldRange.from <= m && g <= h.oldRange.to;
                            this.editor.emit("delete", {
                                type: "node",
                                node: p,
                                from: m,
                                to: g,
                                newFrom: d.mapping.map(m),
                                newTo: d.mapping.map(g),
                                deletedRange: h.oldRange,
                                newRange: h.newRange,
                                partial: !y,
                                editor: this.editor,
                                transaction: t,
                                combinedTransform: d,
                            });
                        },
                    );
            });
            const f = d.mapping;
            d.steps.forEach((h, p) => {
                let m, g;
                if (h instanceof lt) {
                    const y = f.slice(p).map(h.from, -1);
                    const b = f.slice(p).map(h.to);
                    const k = f.invert().map(y, -1);
                    const v = f.invert().map(b);
                    const x =
                        (m = d.doc.nodeAt(y - 1)) == null
                            ? void 0
                            : m.marks.some((w) => w.eq(h.mark));
                    const S =
                        (g = d.doc.nodeAt(b)) == null
                            ? void 0
                            : g.marks.some((w) => w.eq(h.mark));
                    this.editor.emit("delete", {
                        type: "mark",
                        mark: h.mark,
                        from: h.from,
                        to: h.to,
                        deletedRange: { from: k, to: v },
                        newRange: { from: y, to: b },
                        partial: !!(S || x),
                        editor: this.editor,
                        transaction: t,
                        combinedTransform: d,
                    });
                }
            });
        };
        (o =
            (r =
                (n = this.editor.options.coreExtensionOptions) == null
                    ? void 0
                    : n.delete) == null
                ? void 0
                : r.async) == null || o
            ? setTimeout(i, 0)
            : i();
    },
});
var ru = j.create({
    name: "drop",
    addProseMirrorPlugins() {
        return [
            new L({
                key: new H("tiptapDrop"),
                props: {
                    handleDrop: (t, e, n, r) => {
                        this.editor.emit("drop", {
                            editor: this.editor,
                            event: e,
                            slice: n,
                            moved: r,
                        });
                    },
                },
            }),
        ];
    },
});
var ou = j.create({
    name: "editable",
    addProseMirrorPlugins() {
        return [
            new L({
                key: new H("editable"),
                props: { editable: () => this.editor.options.editable },
            }),
        ];
    },
});
var iu = new H("focusEvents");
var su = j.create({
    name: "focusEvents",
    addProseMirrorPlugins() {
        const { editor: t } = this;
        return [
            new L({
                key: iu,
                props: {
                    handleDOMEvents: {
                        focus: (e, n) => {
                            t.isFocused = !0;
                            const r = t.state.tr
                                .setMeta("focus", { event: n })
                                .setMeta("addToHistory", !1);
                            return (e.dispatch(r), !1);
                        },
                        blur: (e, n) => {
                            t.isFocused = !1;
                            const r = t.state.tr
                                .setMeta("blur", { event: n })
                                .setMeta("addToHistory", !1);
                            return (e.dispatch(r), !1);
                        },
                    },
                },
            }),
        ];
    },
});
var lu = j.create({
    name: "keymap",
    addKeyboardShortcuts() {
        const t = () =>
            this.editor.commands.first(({ commands: s }) => [
                () => s.undoInputRule(),
                () =>
                    s.command(({ tr: l }) => {
                        const { selection: a, doc: c } = l;
                        const { empty: d, $anchor: u } = a;
                        const { pos: f, parent: h } = u;
                        const p =
                            u.parent.isTextblock && f > 0
                                ? l.doc.resolve(f - 1)
                                : u;
                        const m = p.parent.type.spec.isolating;
                        const g = u.pos - u.parentOffset;
                        const y =
                            m && p.parent.childCount === 1
                                ? g === u.pos
                                : I.atStart(c).from === f;
                        return !d ||
                            !h.type.isTextblock ||
                            h.textContent.length ||
                            !y ||
                            (y && u.parent.type.name === "paragraph")
                            ? !1
                            : s.clearNodes();
                    }),
                () => s.deleteSelection(),
                () => s.joinBackward(),
                () => s.selectNodeBackward(),
            ]);
        const e = () =>
            this.editor.commands.first(({ commands: s }) => [
                () => s.deleteSelection(),
                () => s.deleteCurrentNode(),
                () => s.joinForward(),
                () => s.selectNodeForward(),
            ]);
        const r = {
            Enter: () =>
                this.editor.commands.first(({ commands: s }) => [
                    () => s.newlineInCode(),
                    () => s.createParagraphNear(),
                    () => s.liftEmptyBlock(),
                    () => s.splitBlock(),
                ]),
            "Mod-Enter": () => this.editor.commands.exitCode(),
            Backspace: t,
            "Mod-Backspace": t,
            "Shift-Backspace": t,
            Delete: e,
            "Mod-Delete": e,
            "Mod-a": () => this.editor.commands.selectAll(),
        };
        const o = { ...r };
        const i = {
            ...r,
            "Ctrl-h": t,
            "Alt-Backspace": t,
            "Ctrl-d": e,
            "Ctrl-Alt-Backspace": e,
            "Alt-Delete": e,
            "Alt-d": e,
            "Ctrl-a": () => this.editor.commands.selectTextblockStart(),
            "Ctrl-e": () => this.editor.commands.selectTextblockEnd(),
        };
        return Us() || $d() ? i : o;
    },
    addProseMirrorPlugins() {
        return [
            new L({
                key: new H("clearDocument"),
                appendTransaction: (t, e, n) => {
                    if (t.some((m) => m.getMeta("composition"))) return;
                    const r = t.some((m) => m.docChanged) && !e.doc.eq(n.doc);
                    const o = t.some((m) => m.getMeta("preventClearDocument"));
                    if (!r || o) return;
                    const { empty: i, from: s, to: l } = e.selection;
                    const a = I.atStart(e.doc).from;
                    const c = I.atEnd(e.doc).to;
                    if (i || !(s === a && l === c) || !nr(n.doc)) return;
                    const f = n.tr;
                    const h = io({ state: n, transaction: f });
                    const { commands: p } = new so({
                        editor: this.editor,
                        state: h,
                    });
                    if ((p.clearNodes(), !!f.steps.length)) return f;
                },
            }),
        ];
    },
});
var au = j.create({
    name: "paste",
    addProseMirrorPlugins() {
        return [
            new L({
                key: new H("tiptapPaste"),
                props: {
                    handlePaste: (t, e, n) => {
                        this.editor.emit("paste", {
                            editor: this.editor,
                            event: e,
                            slice: n,
                        });
                    },
                },
            }),
        ];
    },
});
var cu = j.create({
    name: "tabindex",
    addProseMirrorPlugins() {
        return [
            new L({
                key: new H("tabindex"),
                props: {
                    attributes: () =>
                        this.editor.isEditable ? { tabindex: "0" } : {},
                },
            }),
        ];
    },
});
const i0 = class Sn {
    constructor(e, n, r = !1, o = null) {
        ((this.currentNode = null),
            (this.actualDepth = null),
            (this.isBlock = r),
            (this.resolvedPos = e),
            (this.editor = n),
            (this.currentNode = o));
    }

    get name() {
        return this.node.type.name;
    }

    get node() {
        return this.currentNode || this.resolvedPos.node();
    }

    get element() {
        return this.editor.view.domAtPos(this.pos).node;
    }

    get depth() {
        let e;
        return (e = this.actualDepth) != null ? e : this.resolvedPos.depth;
    }

    get pos() {
        return this.resolvedPos.pos;
    }

    get content() {
        return this.node.content;
    }

    set content(e) {
        let n = this.from;
        let r = this.to;
        if (this.isBlock) {
            if (this.content.size === 0) {
                console.error(
                    `You can\u2019t set content on a block node. Tried to set content on ${this.name} at ${this.pos}`,
                );
                return;
            }
            ((n = this.from + 1), (r = this.to - 1));
        }
        this.editor.commands.insertContentAt({ from: n, to: r }, e);
    }

    get attributes() {
        return this.node.attrs;
    }

    get textContent() {
        return this.node.textContent;
    }

    get size() {
        return this.node.nodeSize;
    }

    get from() {
        return this.isBlock
            ? this.pos
            : this.resolvedPos.start(this.resolvedPos.depth);
    }

    get range() {
        return { from: this.from, to: this.to };
    }

    get to() {
        return this.isBlock
            ? this.pos + this.size
            : this.resolvedPos.end(this.resolvedPos.depth) +
                  (this.node.isText ? 0 : 1);
    }

    get parent() {
        if (this.depth === 0) return null;
        const e = this.resolvedPos.start(this.resolvedPos.depth - 1);
        const n = this.resolvedPos.doc.resolve(e);
        return new Sn(n, this.editor);
    }

    get before() {
        let e = this.resolvedPos.doc.resolve(
            this.from - (this.isBlock ? 1 : 2),
        );
        return (
            e.depth !== this.depth &&
                (e = this.resolvedPos.doc.resolve(this.from - 3)),
            new Sn(e, this.editor)
        );
    }

    get after() {
        let e = this.resolvedPos.doc.resolve(this.to + (this.isBlock ? 2 : 1));
        return (
            e.depth !== this.depth &&
                (e = this.resolvedPos.doc.resolve(this.to + 3)),
            new Sn(e, this.editor)
        );
    }

    get children() {
        const e = [];
        return (
            this.node.content.forEach((n, r) => {
                const o = n.isBlock && !n.isTextblock;
                const i = n.isAtom && !n.isText;
                const s = this.pos + r + (i ? 0 : 1);
                if (s < 0 || s > this.resolvedPos.doc.nodeSize - 2) return;
                const l = this.resolvedPos.doc.resolve(s);
                if (!o && l.depth <= this.depth) return;
                const a = new Sn(l, this.editor, o, o ? n : null);
                (o && (a.actualDepth = this.depth + 1),
                    e.push(new Sn(l, this.editor, o, o ? n : null)));
            }),
            e
        );
    }

    get firstChild() {
        return this.children[0] || null;
    }

    get lastChild() {
        const e = this.children;
        return e[e.length - 1] || null;
    }

    closest(e, n = {}) {
        let r = null;
        let o = this.parent;
        for (; o && !r; ) {
            if (o.node.type.name === e) {
                if (Object.keys(n).length > 0) {
                    const i = o.node.attrs;
                    const s = Object.keys(n);
                    for (let l = 0; l < s.length; l += 1) {
                        const a = s[l];
                        if (i[a] !== n[a]) break;
                    }
                } else r = o;
            }
            o = o.parent;
        }
        return r;
    }

    querySelector(e, n = {}) {
        return this.querySelectorAll(e, n, !0)[0] || null;
    }

    querySelectorAll(e, n = {}, r = !1) {
        let o = [];
        if (!this.children || this.children.length === 0) return o;
        const i = Object.keys(n);
        return (
            this.children.forEach((s) => {
                (r && o.length > 0) ||
                    (s.node.type.name === e &&
                        i.every((a) => n[a] === s.node.attrs[a]) &&
                        o.push(s),
                    !(r && o.length > 0) &&
                        (o = o.concat(s.querySelectorAll(e, n, r))));
            }),
            o
        );
    }

    setAttribute(e) {
        const { tr: n } = this.editor.state;
        (n.setNodeMarkup(this.from, void 0, { ...this.node.attrs, ...e }),
            this.editor.view.dispatch(n));
    }
};
const s0 = `.ProseMirror {
  position: relative;
}

.ProseMirror {
  word-wrap: break-word;
  white-space: pre-wrap;
  white-space: break-spaces;
  -webkit-font-variant-ligatures: none;
  font-variant-ligatures: none;
  font-feature-settings: "liga" 0; /* the above doesn't seem to work in Edge */
}

.ProseMirror [contenteditable="false"] {
  white-space: normal;
}

.ProseMirror [contenteditable="false"] [contenteditable="true"] {
  white-space: pre-wrap;
}

.ProseMirror pre {
  white-space: pre-wrap;
}

img.ProseMirror-separator {
  display: inline !important;
  border: none !important;
  margin: 0 !important;
  width: 0 !important;
  height: 0 !important;
}

.ProseMirror-gapcursor {
  display: none;
  pointer-events: none;
  position: absolute;
  margin: 0;
}

.ProseMirror-gapcursor:after {
  content: "";
  display: block;
  position: absolute;
  top: -2px;
  width: 20px;
  border-top: 1px solid black;
  animation: ProseMirror-cursor-blink 1.1s steps(2, start) infinite;
}

@keyframes ProseMirror-cursor-blink {
  to {
    visibility: hidden;
  }
}

.ProseMirror-hideselection *::selection {
  background: transparent;
}

.ProseMirror-hideselection *::-moz-selection {
  background: transparent;
}

.ProseMirror-hideselection * {
  caret-color: transparent;
}

.ProseMirror-focused .ProseMirror-gapcursor {
  display: block;
}`;
function l0(t, e, n) {
    const r = document.querySelector(
        `style[data-tiptap-style${n ? `-${n}` : ""}]`,
    );
    if (r !== null) return r;
    const o = document.createElement("style");
    return (
        e && o.setAttribute("nonce", e),
        o.setAttribute(`data-tiptap-style${n ? `-${n}` : ""}`, ""),
        (o.innerHTML = t),
        document.getElementsByTagName("head")[0].appendChild(o),
        o
    );
}
const du = class extends Jy {
    constructor(t = {}) {
        (super(),
            (this.css = null),
            (this.className = "tiptap"),
            (this.editorView = null),
            (this.isFocused = !1),
            (this.isInitialized = !1),
            (this.extensionStorage = {}),
            (this.instanceId = Math.random().toString(36).slice(2, 9)),
            (this.options = {
                element:
                    typeof document < "u"
                        ? document.createElement("div")
                        : null,
                content: "",
                injectCSS: !0,
                injectNonce: void 0,
                extensions: [],
                autofocus: !1,
                editable: !0,
                editorProps: {},
                parseOptions: {},
                coreExtensionOptions: {},
                enableInputRules: !0,
                enablePasteRules: !0,
                enableCoreExtensions: !0,
                enableContentCheck: !1,
                emitContentError: !1,
                onBeforeCreate: () => null,
                onCreate: () => null,
                onMount: () => null,
                onUnmount: () => null,
                onUpdate: () => null,
                onSelectionUpdate: () => null,
                onTransaction: () => null,
                onFocus: () => null,
                onBlur: () => null,
                onDestroy: () => null,
                onContentError: ({ error: r }) => {
                    throw r;
                },
                onPaste: () => null,
                onDrop: () => null,
                onDelete: () => null,
            }),
            (this.isCapturingTransaction = !1),
            (this.capturedTransaction = null),
            this.setOptions(t),
            this.createExtensionManager(),
            this.createCommandManager(),
            this.createSchema(),
            this.on("beforeCreate", this.options.onBeforeCreate),
            this.emit("beforeCreate", { editor: this }),
            this.on("mount", this.options.onMount),
            this.on("unmount", this.options.onUnmount),
            this.on("contentError", this.options.onContentError),
            this.on("create", this.options.onCreate),
            this.on("update", this.options.onUpdate),
            this.on("selectionUpdate", this.options.onSelectionUpdate),
            this.on("transaction", this.options.onTransaction),
            this.on("focus", this.options.onFocus),
            this.on("blur", this.options.onBlur),
            this.on("destroy", this.options.onDestroy),
            this.on("drop", ({ event: r, slice: o, moved: i }) =>
                this.options.onDrop(r, o, i),
            ),
            this.on("paste", ({ event: r, slice: o }) =>
                this.options.onPaste(r, o),
            ),
            this.on("delete", this.options.onDelete));
        const e = this.createDoc();
        const n = Bd(e, this.options.autofocus);
        ((this.editorState = zr.create({
            doc: e,
            schema: this.schema,
            selection: n || void 0,
        })),
            this.options.element && this.mount(this.options.element));
    }

    mount(t) {
        if (typeof document > "u") {
            throw new Error(
                "[tiptap error]: The editor cannot be mounted because there is no 'document' defined in this environment.",
            );
        }
        (this.createView(t),
            this.emit("mount", { editor: this }),
            this.css &&
                !document.head.contains(this.css) &&
                document.head.appendChild(this.css),
            window.setTimeout(() => {
                this.isDestroyed ||
                    (this.commands.focus(this.options.autofocus),
                    this.emit("create", { editor: this }),
                    (this.isInitialized = !0));
            }, 0));
    }

    unmount() {
        if (this.editorView) {
            const t = this.editorView.dom;
            (t?.editor && delete t.editor, this.editorView.destroy());
        }
        if (
            ((this.editorView = null),
            (this.isInitialized = !1),
            this.css && !document.querySelectorAll(`.${this.className}`).length)
        ) {
            try {
                typeof this.css.remove === "function"
                    ? this.css.remove()
                    : this.css.parentNode &&
                      this.css.parentNode.removeChild(this.css);
            } catch (t) {
                console.warn("Failed to remove CSS element:", t);
            }
        }
        ((this.css = null), this.emit("unmount", { editor: this }));
    }

    get storage() {
        return this.extensionStorage;
    }

    get commands() {
        return this.commandManager.commands;
    }

    chain() {
        return this.commandManager.chain();
    }

    can() {
        return this.commandManager.can();
    }

    injectCSS() {
        this.options.injectCSS &&
            typeof document < "u" &&
            (this.css = l0(s0, this.options.injectNonce));
    }

    setOptions(t = {}) {
        ((this.options = { ...this.options, ...t }),
            !(!this.editorView || !this.state || this.isDestroyed) &&
                (this.options.editorProps &&
                    this.view.setProps(this.options.editorProps),
                this.view.updateState(this.state)));
    }

    setEditable(t, e = !0) {
        (this.setOptions({ editable: t }),
            e &&
                this.emit("update", {
                    editor: this,
                    transaction: this.state.tr,
                    appendedTransactions: [],
                }));
    }

    get isEditable() {
        return this.options.editable && this.view && this.view.editable;
    }

    get view() {
        return this.editorView
            ? this.editorView
            : new Proxy(
                  {
                      state: this.editorState,
                      updateState: (t) => {
                          this.editorState = t;
                      },
                      dispatch: (t) => {
                          this.dispatchTransaction(t);
                      },
                      composing: !1,
                      dragging: null,
                      editable: !0,
                      isDestroyed: !1,
                  },
                  {
                      get: (t, e) => {
                          if (this.editorView) return this.editorView[e];
                          if (e === "state") return this.editorState;
                          if (e in t) return Reflect.get(t, e);
                          throw new Error(
                              `[tiptap error]: The editor view is not available. Cannot access view['${e}']. The editor may not be mounted yet.`,
                          );
                      },
                  },
              );
    }

    get state() {
        return (
            this.editorView && (this.editorState = this.view.state),
            this.editorState
        );
    }

    registerPlugin(t, e) {
        const n = Vd(e)
            ? e(t, [...this.state.plugins])
            : [...this.state.plugins, t];
        const r = this.state.reconfigure({ plugins: n });
        return (this.view.updateState(r), r);
    }

    unregisterPlugin(t) {
        if (this.isDestroyed) return;
        const e = this.state.plugins;
        let n = e;
        if (
            ([].concat(t).forEach((o) => {
                const i = typeof o === "string" ? `${o}$` : o.key;
                n = n.filter((s) => !s.key.startsWith(i));
            }),
            e.length === n.length)
        ) {
            return;
        }
        const r = this.state.reconfigure({ plugins: n });
        return (this.view.updateState(r), r);
    }

    createExtensionManager() {
        let t, e;
        const r = [
            ...(this.options.enableCoreExtensions
                ? [
                      ou,
                      eu.configure({
                          blockSeparator:
                              (e =
                                  (t = this.options.coreExtensionOptions) ==
                                  null
                                      ? void 0
                                      : t.clipboardTextSerializer) == null
                                  ? void 0
                                  : e.blockSeparator,
                      }),
                      tu,
                      su,
                      lu,
                      cu,
                      ru,
                      au,
                      nu,
                  ].filter((o) =>
                      typeof this.options.enableCoreExtensions === "object"
                          ? this.options.enableCoreExtensions[o.name] !== !1
                          : !0,
                  )
                : []),
            ...this.options.extensions,
        ].filter((o) => ["extension", "node", "mark"].includes(o?.type));
        this.extensionManager = new ho(r, this);
    }

    createCommandManager() {
        this.commandManager = new so({ editor: this });
    }

    createSchema() {
        this.schema = this.extensionManager.schema;
    }

    createDoc() {
        let t;
        try {
            t = _s(
                this.options.content,
                this.schema,
                this.options.parseOptions,
                { errorOnInvalidContent: this.options.enableContentCheck },
            );
        } catch (e) {
            if (
                !(e instanceof Error) ||
                ![
                    "[tiptap error]: Invalid JSON content",
                    "[tiptap error]: Invalid HTML content",
                ].includes(e.message)
            ) {
                throw e;
            }
            (this.emit("contentError", {
                editor: this,
                error: e,
                disableCollaboration: () => {
                    ("collaboration" in this.storage &&
                        typeof this.storage.collaboration === "object" &&
                        this.storage.collaboration &&
                        (this.storage.collaboration.isDisabled = !0),
                        (this.options.extensions =
                            this.options.extensions.filter(
                                (n) => n.name !== "collaboration",
                            )),
                        this.createExtensionManager());
                },
            }),
                (t = _s(
                    this.options.content,
                    this.schema,
                    this.options.parseOptions,
                    { errorOnInvalidContent: !1 },
                )));
        }
        return t;
    }

    createView(t) {
        let e;
        this.editorView = new Gn(t, {
            ...this.options.editorProps,
            attributes: {
                role: "textbox",
                ...((e = this.options.editorProps) == null
                    ? void 0
                    : e.attributes),
            },
            dispatchTransaction: this.dispatchTransaction.bind(this),
            state: this.editorState,
            markViews: this.extensionManager.markViews,
            nodeViews: this.extensionManager.nodeViews,
        });
        const n = this.state.reconfigure({
            plugins: this.extensionManager.plugins,
        });
        (this.view.updateState(n), this.prependClass(), this.injectCSS());
        const r = this.view.dom;
        r.editor = this;
    }

    createNodeViews() {
        this.view.isDestroyed ||
            this.view.setProps({
                markViews: this.extensionManager.markViews,
                nodeViews: this.extensionManager.nodeViews,
            });
    }

    prependClass() {
        this.view.dom.className = `${this.className} ${this.view.dom.className}`;
    }

    captureTransaction(t) {
        ((this.isCapturingTransaction = !0),
            t(),
            (this.isCapturingTransaction = !1));
        const e = this.capturedTransaction;
        return ((this.capturedTransaction = null), e);
    }

    dispatchTransaction(t) {
        if (this.view.isDestroyed) return;
        if (this.isCapturingTransaction) {
            if (!this.capturedTransaction) {
                this.capturedTransaction = t;
                return;
            }
            t.steps.forEach((c) => {
                let d;
                return (d = this.capturedTransaction) == null
                    ? void 0
                    : d.step(c);
            });
            return;
        }
        const { state: e, transactions: n } = this.state.applyTransaction(t);
        const r = !this.state.selection.eq(e.selection);
        const o = n.includes(t);
        const i = this.state;
        if (
            (this.emit("beforeTransaction", {
                editor: this,
                transaction: t,
                nextState: e,
            }),
            !o)
        ) {
            return;
        }
        (this.view.updateState(e),
            this.emit("transaction", {
                editor: this,
                transaction: t,
                appendedTransactions: n.slice(1),
            }),
            r &&
                this.emit("selectionUpdate", { editor: this, transaction: t }));
        const s = n.findLast((c) => c.getMeta("focus") || c.getMeta("blur"));
        const l = s?.getMeta("focus");
        const a = s?.getMeta("blur");
        (l &&
            this.emit("focus", {
                editor: this,
                event: l.event,
                transaction: s,
            }),
            a &&
                this.emit("blur", {
                    editor: this,
                    event: a.event,
                    transaction: s,
                }),
            !(
                t.getMeta("preventUpdate") ||
                !n.some((c) => c.docChanged) ||
                i.doc.eq(e.doc)
            ) &&
                this.emit("update", {
                    editor: this,
                    transaction: t,
                    appendedTransactions: n.slice(1),
                }));
    }

    getAttributes(t) {
        return Qs(this.state, t);
    }

    isActive(t, e) {
        const n = typeof t === "string" ? t : null;
        const r = typeof t === "string" ? e : t;
        return el(this.state, n, r);
    }

    getJSON() {
        return this.state.doc.toJSON();
    }

    getHTML() {
        return Xs(this.state.doc.content, this.schema);
    }

    getText(t) {
        const {
            blockSeparator: e = `

`,
            textSerializers: n = {},
        } = t || {};
        return vy(this.state.doc, {
            blockSeparator: e,
            textSerializers: { ...Ud(this.schema), ...n },
        });
    }

    get isEmpty() {
        return nr(this.state.doc);
    }

    destroy() {
        (this.emit("destroy"), this.unmount(), this.removeAllListeners());
    }

    get isDestroyed() {
        let t, e;
        return (e = (t = this.editorView) == null ? void 0 : t.isDestroyed) !=
            null
            ? e
            : !0;
    }

    $node(t, e) {
        let n;
        return (
            ((n = this.$doc) == null ? void 0 : n.querySelector(t, e)) || null
        );
    }

    $nodes(t, e) {
        let n;
        return (
            ((n = this.$doc) == null ? void 0 : n.querySelectorAll(t, e)) ||
            null
        );
    }

    $pos(t) {
        const e = this.state.doc.resolve(t);
        return new i0(e, this);
    }

    get $doc() {
        return this.$pos(0);
    }
};
function Le(t) {
    return new fo({
        find: t.find,
        handler: ({ state: e, range: n, match: r }) => {
            const o = J(t.getAttributes, void 0, r);
            if (o === !1 || o === null) return null;
            const { tr: i } = e;
            const s = r[r.length - 1];
            const l = r[0];
            if (s) {
                const a = l.search(/\S/);
                const c = n.from + l.indexOf(s);
                const d = c + s.length;
                if (
                    co(n.from, n.to, e.doc)
                        .filter((h) =>
                            h.mark.type.excluded.find(
                                (m) => m === t.type && m !== h.mark.type,
                            ),
                        )
                        .filter((h) => h.to > c).length
                ) {
                    return null;
                }
                (d < n.to && i.delete(d, n.to),
                    c > n.from && i.delete(n.from + a, c));
                const f = n.from + a + s.length;
                (i.addMark(n.from + a, f, t.type.create(o || {})),
                    i.removeStoredMark(t.type));
            }
        },
        undoable: t.undoable,
    });
}
function po(t) {
    return new fo({
        find: t.find,
        handler: ({ state: e, range: n, match: r }) => {
            const o = J(t.getAttributes, void 0, r) || {};
            const { tr: i } = e;
            const s = n.from;
            let l = n.to;
            const a = t.type.create(o);
            if (r[1]) {
                const c = r[0].lastIndexOf(r[1]);
                let d = s + c;
                d > l ? (d = l) : (l = d + r[1].length);
                const u = r[0][r[0].length - 1];
                (i.insertText(u, s + r[0].length - 1), i.replaceWith(d, l, a));
            } else if (r[0]) {
                const c = t.type.isInline ? s : s - 1;
                i.insert(c, t.type.create(o)).delete(
                    i.mapping.map(s),
                    i.mapping.map(l),
                );
            }
            i.scrollIntoView();
        },
        undoable: t.undoable,
    });
}
function rr(t) {
    return new fo({
        find: t.find,
        handler: ({ state: e, range: n, match: r }) => {
            const o = e.doc.resolve(n.from);
            const i = J(t.getAttributes, void 0, r) || {};
            if (
                !o
                    .node(-1)
                    .canReplaceWith(o.index(-1), o.indexAfter(-1), t.type)
            ) {
                return null;
            }
            e.tr.delete(n.from, n.to).setBlockType(n.from, n.from, t.type, i);
        },
        undoable: t.undoable,
    });
}
function Ye(t) {
    return new fo({
        find: t.find,
        handler: ({ state: e, range: n, match: r, chain: o }) => {
            const i = J(t.getAttributes, void 0, r) || {};
            const s = e.tr.delete(n.from, n.to);
            const a = s.doc.resolve(n.from).blockRange();
            const c = a && un(a, t.type, i);
            if (!c) return null;
            if ((s.wrap(a, c), t.keepMarks && t.editor)) {
                const { selection: u, storedMarks: f } = e;
                const { splittableMarks: h } = t.editor.extensionManager;
                const p = f || (u.$to.parentOffset && u.$from.marks());
                if (p) {
                    const m = p.filter((g) => h.includes(g.type.name));
                    s.ensureMarks(m);
                }
            }
            if (t.keepAttributes) {
                const u =
                    t.type.name === "bulletList" ||
                    t.type.name === "orderedList"
                        ? "listItem"
                        : "taskList";
                o().updateAttributes(u, i).run();
            }
            const d = s.doc.resolve(n.from - 1).nodeBefore;
            d &&
                d.type === t.type &&
                Oe(s.doc, n.from - 1) &&
                (!t.joinPredicate || t.joinPredicate(r, d)) &&
                s.join(n.from - 1);
        },
        undoable: t.undoable,
    });
}
const a0 = (t) => "touches" in t;
const uu = class {
    constructor(t) {
        ((this.directions = [
            "bottom-left",
            "bottom-right",
            "top-left",
            "top-right",
        ]),
            (this.minSize = { height: 8, width: 8 }),
            (this.preserveAspectRatio = !1),
            (this.classNames = {
                container: "",
                wrapper: "",
                handle: "",
                resizing: "",
            }),
            (this.initialWidth = 0),
            (this.initialHeight = 0),
            (this.aspectRatio = 1),
            (this.isResizing = !1),
            (this.activeHandle = null),
            (this.startX = 0),
            (this.startY = 0),
            (this.startWidth = 0),
            (this.startHeight = 0),
            (this.isShiftKeyPressed = !1),
            (this.handleMouseMove = (s) => {
                if (!this.isResizing || !this.activeHandle) return;
                const l = s.clientX - this.startX;
                const a = s.clientY - this.startY;
                this.handleResize(l, a);
            }),
            (this.handleTouchMove = (s) => {
                if (!this.isResizing || !this.activeHandle) return;
                const l = s.touches[0];
                if (!l) return;
                const a = l.clientX - this.startX;
                const c = l.clientY - this.startY;
                this.handleResize(a, c);
            }),
            (this.handleMouseUp = () => {
                if (!this.isResizing) return;
                const s = this.element.offsetWidth;
                const l = this.element.offsetHeight;
                (this.onCommit(s, l),
                    (this.isResizing = !1),
                    (this.activeHandle = null),
                    (this.container.dataset.resizeState = "false"),
                    this.classNames.resizing &&
                        this.container.classList.remove(
                            this.classNames.resizing,
                        ),
                    document.removeEventListener(
                        "mousemove",
                        this.handleMouseMove,
                    ),
                    document.removeEventListener("mouseup", this.handleMouseUp),
                    document.removeEventListener("keydown", this.handleKeyDown),
                    document.removeEventListener("keyup", this.handleKeyUp));
            }),
            (this.handleKeyDown = (s) => {
                s.key === "Shift" && (this.isShiftKeyPressed = !0);
            }),
            (this.handleKeyUp = (s) => {
                s.key === "Shift" && (this.isShiftKeyPressed = !1);
            }));
        let e, n, r, o, i;
        ((this.node = t.node),
            (this.element = t.element),
            (this.contentElement = t.contentElement),
            (this.getPos = t.getPos),
            (this.onResize = t.onResize),
            (this.onCommit = t.onCommit),
            (this.onUpdate = t.onUpdate),
            (e = t.options) != null &&
                e.min &&
                (this.minSize = { ...this.minSize, ...t.options.min }),
            (n = t.options) != null && n.max && (this.maxSize = t.options.max),
            (r = t?.options) != null &&
                r.directions &&
                (this.directions = t.options.directions),
            (o = t.options) != null &&
                o.preserveAspectRatio &&
                (this.preserveAspectRatio = t.options.preserveAspectRatio),
            (i = t.options) != null &&
                i.className &&
                (this.classNames = {
                    container: t.options.className.container || "",
                    wrapper: t.options.className.wrapper || "",
                    handle: t.options.className.handle || "",
                    resizing: t.options.className.resizing || "",
                }),
            (this.wrapper = this.createWrapper()),
            (this.container = this.createContainer()),
            this.applyInitialSize(),
            this.attachHandles());
    }

    get dom() {
        return this.container;
    }

    get contentDOM() {
        return this.contentElement;
    }

    update(t, e, n) {
        return t.type !== this.node.type
            ? !1
            : ((this.node = t), this.onUpdate ? this.onUpdate(t, e, n) : !0);
    }

    destroy() {
        (this.isResizing &&
            ((this.container.dataset.resizeState = "false"),
            this.classNames.resizing &&
                this.container.classList.remove(this.classNames.resizing),
            document.removeEventListener("mousemove", this.handleMouseMove),
            document.removeEventListener("mouseup", this.handleMouseUp),
            document.removeEventListener("keydown", this.handleKeyDown),
            document.removeEventListener("keyup", this.handleKeyUp),
            (this.isResizing = !1),
            (this.activeHandle = null)),
            this.container.remove());
    }

    createContainer() {
        const t = document.createElement("div");
        return (
            (t.dataset.resizeContainer = ""),
            (t.dataset.node = this.node.type.name),
            (t.style.display = "flex"),
            (t.style.justifyContent = "flex-start"),
            (t.style.alignItems = "flex-start"),
            this.classNames.container &&
                (t.className = this.classNames.container),
            t.appendChild(this.wrapper),
            t
        );
    }

    createWrapper() {
        const t = document.createElement("div");
        return (
            (t.style.position = "relative"),
            (t.style.display = "block"),
            (t.dataset.resizeWrapper = ""),
            this.classNames.wrapper && (t.className = this.classNames.wrapper),
            t.appendChild(this.element),
            t
        );
    }

    createHandle(t) {
        const e = document.createElement("div");
        return (
            (e.dataset.resizeHandle = t),
            (e.style.position = "absolute"),
            this.classNames.handle && (e.className = this.classNames.handle),
            e
        );
    }

    positionHandle(t, e) {
        const n = e.includes("top");
        const r = e.includes("bottom");
        const o = e.includes("left");
        const i = e.includes("right");
        (n && (t.style.top = "0"),
            r && (t.style.bottom = "0"),
            o && (t.style.left = "0"),
            i && (t.style.right = "0"),
            (e === "top" || e === "bottom") &&
                ((t.style.left = "0"), (t.style.right = "0")),
            (e === "left" || e === "right") &&
                ((t.style.top = "0"), (t.style.bottom = "0")));
    }

    attachHandles() {
        this.directions.forEach((t) => {
            const e = this.createHandle(t);
            (this.positionHandle(e, t),
                e.addEventListener("mousedown", (n) =>
                    this.handleResizeStart(n, t),
                ),
                e.addEventListener("touchstart", (n) =>
                    this.handleResizeStart(n, t),
                ),
                this.wrapper.appendChild(e));
        });
    }

    applyInitialSize() {
        const t = this.node.attrs.width;
        const e = this.node.attrs.height;
        (t
            ? ((this.element.style.width = `${t}px`), (this.initialWidth = t))
            : (this.initialWidth = this.element.offsetWidth),
            e
                ? ((this.element.style.height = `${e}px`),
                  (this.initialHeight = e))
                : (this.initialHeight = this.element.offsetHeight),
            this.initialWidth > 0 &&
                this.initialHeight > 0 &&
                (this.aspectRatio = this.initialWidth / this.initialHeight));
    }

    handleResizeStart(t, e) {
        (t.preventDefault(),
            t.stopPropagation(),
            (this.isResizing = !0),
            (this.activeHandle = e),
            a0(t)
                ? ((this.startX = t.touches[0].clientX),
                  (this.startY = t.touches[0].clientY))
                : ((this.startX = t.clientX), (this.startY = t.clientY)),
            (this.startWidth = this.element.offsetWidth),
            (this.startHeight = this.element.offsetHeight),
            this.startWidth > 0 &&
                this.startHeight > 0 &&
                (this.aspectRatio = this.startWidth / this.startHeight));
        const n = this.getPos();
        ((this.container.dataset.resizeState = "true"),
            this.classNames.resizing &&
                this.container.classList.add(this.classNames.resizing),
            document.addEventListener("mousemove", this.handleMouseMove),
            document.addEventListener("touchmove", this.handleTouchMove),
            document.addEventListener("mouseup", this.handleMouseUp),
            document.addEventListener("keydown", this.handleKeyDown),
            document.addEventListener("keyup", this.handleKeyUp));
    }

    handleResize(t, e) {
        if (!this.activeHandle) return;
        const n = this.preserveAspectRatio || this.isShiftKeyPressed;
        const { width: r, height: o } = this.calculateNewDimensions(
            this.activeHandle,
            t,
            e,
        );
        const i = this.applyConstraints(r, o, n);
        ((this.element.style.width = `${i.width}px`),
            (this.element.style.height = `${i.height}px`),
            this.onResize && this.onResize(i.width, i.height));
    }

    calculateNewDimensions(t, e, n) {
        let r = this.startWidth;
        let o = this.startHeight;
        const i = t.includes("right");
        const s = t.includes("left");
        const l = t.includes("bottom");
        const a = t.includes("top");
        return (
            i ? (r = this.startWidth + e) : s && (r = this.startWidth - e),
            l ? (o = this.startHeight + n) : a && (o = this.startHeight - n),
            (t === "right" || t === "left") &&
                (r = this.startWidth + (i ? e : -e)),
            (t === "top" || t === "bottom") &&
                (o = this.startHeight + (l ? n : -n)),
            this.preserveAspectRatio || this.isShiftKeyPressed
                ? this.applyAspectRatio(r, o, t)
                : { width: r, height: o }
        );
    }

    applyConstraints(t, e, n) {
        let r, o, i, s;
        if (!n) {
            let c = Math.max(this.minSize.width, t);
            let d = Math.max(this.minSize.height, e);
            return (
                (r = this.maxSize) != null &&
                    r.width &&
                    (c = Math.min(this.maxSize.width, c)),
                (o = this.maxSize) != null &&
                    o.height &&
                    (d = Math.min(this.maxSize.height, d)),
                { width: c, height: d }
            );
        }
        let l = t;
        let a = e;
        return (
            l < this.minSize.width &&
                ((l = this.minSize.width), (a = l / this.aspectRatio)),
            a < this.minSize.height &&
                ((a = this.minSize.height), (l = a * this.aspectRatio)),
            (i = this.maxSize) != null &&
                i.width &&
                l > this.maxSize.width &&
                ((l = this.maxSize.width), (a = l / this.aspectRatio)),
            (s = this.maxSize) != null &&
                s.height &&
                a > this.maxSize.height &&
                ((a = this.maxSize.height), (l = a * this.aspectRatio)),
            { width: l, height: a }
        );
    }

    applyAspectRatio(t, e, n) {
        const r = n === "left" || n === "right";
        const o = n === "top" || n === "bottom";
        return r
            ? { width: t, height: t / this.aspectRatio }
            : o
              ? { width: e * this.aspectRatio, height: e }
              : { width: t, height: t / this.aspectRatio };
    }
};
function fu(t, e) {
    const { selection: n } = t;
    const { $from: r } = n;
    if (n instanceof P) {
        const i = r.index();
        return r.parent.canReplaceWith(i, i + 1, e);
    }
    let o = r.depth;
    for (; o >= 0; ) {
        const i = r.index(o);
        if (r.node(o).contentMatchAt(i).matchType(e)) return !0;
        o -= 1;
    }
    return !1;
}
function hu(t) {
    return t.replace(/[-/\\^$*+?.()|[\]{}]/g, "\\$&");
}
const c0 = {};
Ws(c0, {
    createAtomBlockMarkdownSpec: () => d0,
    createBlockMarkdownSpec: () => Xt,
    createInlineMarkdownSpec: () => h0,
    parseAttributes: () => nl,
    parseIndentedBlocks: () => mo,
    renderNestedMarkdownContent: () => or,
    serializeAttributes: () => rl,
});
function nl(t) {
    if (!t?.trim()) return {};
    const e = {};
    const n = [];
    const r = t.replace(
        /["']([^"']*)["']/g,
        (c) => (n.push(c), `__QUOTED_${n.length - 1}__`),
    );
    const o = r.match(/(?:^|\s)\.([a-zA-Z][\w-]*)/g);
    if (o) {
        const c = o.map((d) => d.trim().slice(1));
        e.class = c.join(" ");
    }
    const i = r.match(/(?:^|\s)#([a-zA-Z][\w-]*)/);
    i && (e.id = i[1]);
    const s = /([a-zA-Z][\w-]*)\s*=\s*(__QUOTED_\d+__)/g;
    Array.from(r.matchAll(s)).forEach(([, c, d]) => {
        let u;
        const f = parseInt(
            ((u = d.match(/__QUOTED_(\d+)__/)) == null ? void 0 : u[1]) || "0",
            10,
        );
        const h = n[f];
        h && (e[c] = h.slice(1, -1));
    });
    const a = r
        .replace(/(?:^|\s)\.([a-zA-Z][\w-]*)/g, "")
        .replace(/(?:^|\s)#([a-zA-Z][\w-]*)/g, "")
        .replace(/([a-zA-Z][\w-]*)\s*=\s*__QUOTED_\d+__/g, "")
        .trim();
    return (
        a &&
            a
                .split(/\s+/)
                .filter(Boolean)
                .forEach((d) => {
                    d.match(/^[a-zA-Z][\w-]*$/) && (e[d] = !0);
                }),
        e
    );
}
function rl(t) {
    if (!t || Object.keys(t).length === 0) return "";
    const e = [];
    return (
        t.class &&
            String(t.class)
                .split(/\s+/)
                .filter(Boolean)
                .forEach((r) => e.push(`.${r}`)),
        t.id && e.push(`#${t.id}`),
        Object.entries(t).forEach(([n, r]) => {
            n === "class" ||
                n === "id" ||
                (r === !0
                    ? e.push(n)
                    : r !== !1 && r != null && e.push(`${n}="${String(r)}"`));
        }),
        e.join(" ")
    );
}
function d0(t) {
    const {
        nodeName: e,
        name: n,
        parseAttributes: r = nl,
        serializeAttributes: o = rl,
        defaultAttributes: i = {},
        requiredAttributes: s = [],
        allowedAttributes: l,
    } = t;
    const a = n || e;
    const c = (d) => {
        if (!l) return d;
        const u = {};
        return (
            l.forEach((f) => {
                f in d && (u[f] = d[f]);
            }),
            u
        );
    };
    return {
        parseMarkdown: (d, u) => {
            const f = { ...i, ...d.attributes };
            return u.createNode(e, f, []);
        },
        markdownTokenizer: {
            name: e,
            level: "block",
            start(d) {
                let u;
                const f = new RegExp(`^:::${a}(?:\\s|$)`, "m");
                const h = (u = d.match(f)) == null ? void 0 : u.index;
                return h !== void 0 ? h : -1;
            },
            tokenize(d, u, f) {
                const h = new RegExp(
                    `^:::${a}(?:\\s+\\{([^}]*)\\})?\\s*:::(?:\\n|$)`,
                );
                const p = d.match(h);
                if (!p) return;
                const m = p[1] || "";
                const g = r(m);
                if (!s.find((b) => !(b in g))) {
                    return { type: e, raw: p[0], attributes: g };
                }
            },
        },
        renderMarkdown: (d) => {
            const u = c(d.attrs || {});
            const f = o(u);
            const h = f ? ` {${f}}` : "";
            return `:::${a}${h} :::`;
        },
    };
}
function Xt(t) {
    const {
        nodeName: e,
        name: n,
        getContent: r,
        parseAttributes: o = nl,
        serializeAttributes: i = rl,
        defaultAttributes: s = {},
        content: l = "block",
        allowedAttributes: a,
    } = t;
    const c = n || e;
    const d = (u) => {
        if (!a) return u;
        const f = {};
        return (
            a.forEach((h) => {
                h in u && (f[h] = u[h]);
            }),
            f
        );
    };
    return {
        parseMarkdown: (u, f) => {
            let h;
            if (r) {
                const m = r(u);
                h = typeof m === "string" ? [{ type: "text", text: m }] : m;
            } else {
                l === "block"
                    ? (h = f.parseChildren(u.tokens || []))
                    : (h = f.parseInline(u.tokens || []));
            }
            const p = { ...s, ...u.attributes };
            return f.createNode(e, p, h);
        },
        markdownTokenizer: {
            name: e,
            level: "block",
            start(u) {
                let f;
                const h = new RegExp(`^:::${c}`, "m");
                const p = (f = u.match(h)) == null ? void 0 : f.index;
                return p !== void 0 ? p : -1;
            },
            tokenize(u, f, h) {
                let p;
                const m = new RegExp(`^:::${c}(?:\\s+\\{([^}]*)\\})?\\s*\\n`);
                const g = u.match(m);
                if (!g) return;
                const [y, b = ""] = g;
                const k = o(b);
                let v = 1;
                const x = y.length;
                let S = "";
                const w = /^:::([\w-]*)(\s.*)?/gm;
                const N = u.slice(x);
                for (w.lastIndex = 0; ; ) {
                    const D = w.exec(N);
                    if (D === null) break;
                    const M = D.index;
                    const B = D[1];
                    if (!((p = D[2]) != null && p.endsWith(":::"))) {
                        if (B) v += 1;
                        else if (((v -= 1), v === 0)) {
                            const F = N.slice(0, M);
                            S = F.trim();
                            const K = u.slice(0, x + M + D[0].length);
                            let _ = [];
                            if (S) {
                                if (l === "block") {
                                    for (
                                        _ = h.blockTokens(F),
                                            _.forEach((T) => {
                                                T.text &&
                                                    (!T.tokens ||
                                                        T.tokens.length ===
                                                            0) &&
                                                    (T.tokens = h.inlineTokens(
                                                        T.text,
                                                    ));
                                            });
                                        _.length > 0;

                                    ) {
                                        const T = _[_.length - 1];
                                        if (
                                            T.type === "paragraph" &&
                                            (!T.text || T.text.trim() === "")
                                        ) {
                                            _.pop();
                                        } else break;
                                    }
                                } else _ = h.inlineTokens(S);
                            }
                            return {
                                type: e,
                                raw: K,
                                attributes: k,
                                content: S,
                                tokens: _,
                            };
                        }
                    }
                }
            },
        },
        renderMarkdown: (u, f) => {
            const h = d(u.attrs || {});
            const p = i(h);
            const m = p ? ` {${p}}` : "";
            const g = f.renderChildren(
                u.content || [],
                `

`,
            );
            return `:::${c}${m}

${g}

:::`;
        },
    };
}
function u0(t) {
    if (!t.trim()) return {};
    const e = {};
    const n = /(\w+)=(?:"([^"]*)"|'([^']*)')/g;
    let r = n.exec(t);
    for (; r !== null; ) {
        const [, o, i, s] = r;
        ((e[o] = i || s), (r = n.exec(t)));
    }
    return e;
}
function f0(t) {
    return Object.entries(t)
        .filter(([, e]) => e != null)
        .map(([e, n]) => `${e}="${n}"`)
        .join(" ");
}
function h0(t) {
    const {
        nodeName: e,
        name: n,
        getContent: r,
        parseAttributes: o = u0,
        serializeAttributes: i = f0,
        defaultAttributes: s = {},
        selfClosing: l = !1,
        allowedAttributes: a,
    } = t;
    const c = n || e;
    const d = (f) => {
        if (!a) return f;
        const h = {};
        return (
            a.forEach((p) => {
                p in f && (h[p] = f[p]);
            }),
            h
        );
    };
    const u = c.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
    return {
        parseMarkdown: (f, h) => {
            const p = { ...s, ...f.attributes };
            if (l) return h.createNode(e, p);
            const m = r ? r(f) : f.content || "";
            return m
                ? h.createNode(e, p, [h.createTextNode(m)])
                : h.createNode(e, p, []);
        },
        markdownTokenizer: {
            name: e,
            level: "inline",
            start(f) {
                const h = l
                    ? new RegExp(`\\[${u}\\s*[^\\]]*\\]`)
                    : new RegExp(
                          `\\[${u}\\s*[^\\]]*\\][\\s\\S]*?\\[\\/${u}\\]`,
                      );
                const p = f.match(h);
                const m = p?.index;
                return m !== void 0 ? m : -1;
            },
            tokenize(f, h, p) {
                const m = l
                    ? new RegExp(`^\\[${u}\\s*([^\\]]*)\\]`)
                    : new RegExp(
                          `^\\[${u}\\s*([^\\]]*)\\]([\\s\\S]*?)\\[\\/${u}\\]`,
                      );
                const g = f.match(m);
                if (!g) return;
                let y = "";
                let b = "";
                if (l) {
                    const [, v] = g;
                    b = v;
                } else {
                    const [, v, x] = g;
                    ((b = v), (y = x || ""));
                }
                const k = o(b.trim());
                return { type: e, raw: g[0], content: y.trim(), attributes: k };
            },
        },
        renderMarkdown: (f) => {
            let h = "";
            r
                ? (h = r(f))
                : f.content &&
                  f.content.length > 0 &&
                  (h = f.content
                      .filter((y) => y.type === "text")
                      .map((y) => y.text)
                      .join(""));
            const p = d(f.attrs || {});
            const m = i(p);
            const g = m ? ` ${m}` : "";
            return l ? `[${c}${g}]` : `[${c}${g}]${h}[/${c}]`;
        },
    };
}
function mo(t, e, n) {
    let r, o, i, s;
    const l = t.split(`
`);
    const a = [];
    let c = "";
    let d = 0;
    const u = e.baseIndentSize || 2;
    for (; d < l.length; ) {
        const f = l[d];
        const h = f.match(e.itemPattern);
        if (!h) {
            if (a.length > 0) break;
            if (f.trim() === "") {
                d += 1;
                continue;
            } else return;
        }
        const p = e.extractItemData(h);
        const { indentLevel: m, mainContent: g } = p;
        c = `${c}${f}
`;
        const y = [g];
        for (d += 1; d < l.length; ) {
            const x = l[d];
            if (x.trim() === "") {
                const w = l.slice(d + 1).findIndex((M) => M.trim() !== "");
                if (w === -1) break;
                if (
                    (((o =
                        (r = l[d + 1 + w].match(/^(\s*)/)) == null
                            ? void 0
                            : r[1]) == null
                        ? void 0
                        : o.length) || 0) > m
                ) {
                    (y.push(x),
                        (c = `${c}${x}
`),
                        (d += 1));
                    continue;
                } else break;
            }
            if (
                (((s = (i = x.match(/^(\s*)/)) == null ? void 0 : i[1]) == null
                    ? void 0
                    : s.length) || 0) > m
            ) {
                (y.push(x),
                    (c = `${c}${x}
`),
                    (d += 1));
            } else break;
        }
        let b;
        const k = y.slice(1);
        if (k.length > 0) {
            const x = k.map((S) => S.slice(m + u)).join(`
`);
            x.trim() &&
                (e.customNestedParser
                    ? (b = e.customNestedParser(x))
                    : (b = n.blockTokens(x)));
        }
        const v = e.createToken(p, b);
        a.push(v);
    }
    if (a.length !== 0) return { items: a, raw: c.trim() };
}
function or(t, e, n, r) {
    if (!t || !Array.isArray(t.content)) return "";
    const o = typeof n === "function" ? n(r) : n;
    const [i, ...s] = t.content;
    const l = e.renderChildren([i]);
    const a = [`${o}${l}`];
    return (
        s &&
            s.length > 0 &&
            s.forEach((c) => {
                const d = e.renderChildren([c]);
                if (d) {
                    const u = d
                        .split(
                            `
`,
                        )
                        .map((f) => (f ? e.indent(f) : "")).join(`
`);
                    a.push(u);
                }
            }),
        a.join(`
`)
    );
}
function p0(t, e, n = {}) {
    const { state: r } = e;
    const { doc: o, tr: i } = r;
    const s = t;
    (o.descendants((l, a) => {
        const c = i.mapping.map(a);
        const d = i.mapping.map(a) + l.nodeSize;
        let u = null;
        if (
            (l.marks.forEach((h) => {
                if (h !== s) return !1;
                u = h;
            }),
            !u)
        ) {
            return;
        }
        let f = !1;
        if (
            (Object.keys(n).forEach((h) => {
                n[h] !== u.attrs[h] && (f = !0);
            }),
            f)
        ) {
            const h = t.type.create({ ...t.attrs, ...n });
            (i.removeMark(c, d, t.type), i.addMark(c, d, h));
        }
    }),
        i.docChanged && e.view.dispatch(i));
}
const $ = class pu extends tl {
    constructor() {
        (super(...arguments), (this.type = "node"));
    }

    static create(e = {}) {
        const n = typeof e === "function" ? e() : e;
        return new pu(n);
    }

    configure(e) {
        return super.configure(e);
    }

    extend(e) {
        const n = typeof e === "function" ? e() : e;
        return super.extend(n);
    }
};
function Ce(t) {
    return new Zy({
        find: t.find,
        handler: ({ state: e, range: n, match: r, pasteEvent: o }) => {
            const i = J(t.getAttributes, void 0, r, o);
            if (i === !1 || i === null) return null;
            const { tr: s } = e;
            const l = r[r.length - 1];
            const a = r[0];
            let c = n.to;
            if (l) {
                const d = a.search(/\S/);
                const u = n.from + a.indexOf(l);
                const f = u + l.length;
                if (
                    co(n.from, n.to, e.doc)
                        .filter((p) =>
                            p.mark.type.excluded.find(
                                (g) => g === t.type && g !== p.mark.type,
                            ),
                        )
                        .filter((p) => p.to > u).length
                ) {
                    return null;
                }
                (f < n.to && s.delete(f, n.to),
                    u > n.from && s.delete(n.from + d, u),
                    (c = n.from + d + l.length),
                    s.addMark(n.from + d, c, t.type.create(i || {})),
                    s.removeStoredMark(t.type));
            }
        },
    });
}
function mu(t = {}) {
    return new L({
        view(e) {
            return new ol(e, t);
        },
    });
}
var ol = class {
    constructor(e, n) {
        let r;
        ((this.editorView = e),
            (this.cursorPos = null),
            (this.element = null),
            (this.timeout = -1),
            (this.width = (r = n.width) !== null && r !== void 0 ? r : 1),
            (this.color = n.color === !1 ? void 0 : n.color || "black"),
            (this.class = n.class),
            (this.handlers = ["dragover", "dragend", "drop", "dragleave"].map(
                (o) => {
                    const i = (s) => {
                        this[o](s);
                    };
                    return (
                        e.dom.addEventListener(o, i),
                        { name: o, handler: i }
                    );
                },
            )));
    }

    destroy() {
        this.handlers.forEach(({ name: e, handler: n }) =>
            this.editorView.dom.removeEventListener(e, n),
        );
    }

    update(e, n) {
        this.cursorPos != null &&
            n.doc != e.state.doc &&
            (this.cursorPos > e.state.doc.content.size
                ? this.setCursor(null)
                : this.updateOverlay());
    }

    setCursor(e) {
        e != this.cursorPos &&
            ((this.cursorPos = e),
            e == null
                ? (this.element.parentNode.removeChild(this.element),
                  (this.element = null))
                : this.updateOverlay());
    }

    updateOverlay() {
        const e = this.editorView.state.doc.resolve(this.cursorPos);
        const n = !e.parent.inlineContent;
        let r;
        const o = this.editorView.dom;
        const i = o.getBoundingClientRect();
        const s = i.width / o.offsetWidth;
        const l = i.height / o.offsetHeight;
        if (n) {
            const u = e.nodeBefore;
            const f = e.nodeAfter;
            if (u || f) {
                const h = this.editorView.nodeDOM(
                    this.cursorPos - (u ? u.nodeSize : 0),
                );
                if (h) {
                    const p = h.getBoundingClientRect();
                    let m = u ? p.bottom : p.top;
                    u &&
                        f &&
                        (m =
                            (m +
                                this.editorView
                                    .nodeDOM(this.cursorPos)
                                    .getBoundingClientRect().top) /
                            2);
                    const g = (this.width / 2) * l;
                    r = {
                        left: p.left,
                        right: p.right,
                        top: m - g,
                        bottom: m + g,
                    };
                }
            }
        }
        if (!r) {
            const u = this.editorView.coordsAtPos(this.cursorPos);
            const f = (this.width / 2) * s;
            r = {
                left: u.left - f,
                right: u.left + f,
                top: u.top,
                bottom: u.bottom,
            };
        }
        const a = this.editorView.dom.offsetParent;
        (this.element ||
            ((this.element = a.appendChild(document.createElement("div"))),
            this.class && (this.element.className = this.class),
            (this.element.style.cssText =
                "position: absolute; z-index: 50; pointer-events: none;"),
            this.color && (this.element.style.backgroundColor = this.color)),
            this.element.classList.toggle("prosemirror-dropcursor-block", n),
            this.element.classList.toggle("prosemirror-dropcursor-inline", !n));
        let c, d;
        if (
            !a ||
            (a == document.body && getComputedStyle(a).position == "static")
        ) {
            ((c = -pageXOffset), (d = -pageYOffset));
        } else {
            const u = a.getBoundingClientRect();
            const f = u.width / a.offsetWidth;
            const h = u.height / a.offsetHeight;
            ((c = u.left - a.scrollLeft * f), (d = u.top - a.scrollTop * h));
        }
        ((this.element.style.left = (r.left - c) / s + "px"),
            (this.element.style.top = (r.top - d) / l + "px"),
            (this.element.style.width = (r.right - r.left) / s + "px"),
            (this.element.style.height = (r.bottom - r.top) / l + "px"));
    }

    scheduleRemoval(e) {
        (clearTimeout(this.timeout),
            (this.timeout = setTimeout(() => this.setCursor(null), e)));
    }

    dragover(e) {
        if (!this.editorView.editable) return;
        const n = this.editorView.posAtCoords({
            left: e.clientX,
            top: e.clientY,
        });
        const r =
            n && n.inside >= 0 && this.editorView.state.doc.nodeAt(n.inside);
        const o = r && r.type.spec.disableDropCursor;
        const i = typeof o === "function" ? o(this.editorView, n, e) : o;
        if (n && !i) {
            let s = n.pos;
            if (this.editorView.dragging && this.editorView.dragging.slice) {
                const l = Ir(
                    this.editorView.state.doc,
                    s,
                    this.editorView.dragging.slice,
                );
                l != null && (s = l);
            }
            (this.setCursor(s), this.scheduleRemoval(5e3));
        }
    }

    dragend() {
        this.scheduleRemoval(20);
    }

    drop() {
        this.scheduleRemoval(20);
    }

    dragleave(e) {
        this.editorView.dom.contains(e.relatedTarget) || this.setCursor(null);
    }
};
const se = class t extends I {
    constructor(e) {
        super(e, e);
    }

    map(e, n) {
        const r = e.resolve(n.map(this.head));
        return t.valid(r) ? new t(r) : I.near(r);
    }

    content() {
        return E.empty;
    }

    eq(e) {
        return e instanceof t && e.head == this.head;
    }

    toJSON() {
        return { type: "gapcursor", pos: this.head };
    }

    static fromJSON(e, n) {
        if (typeof n.pos !== "number") {
            throw new RangeError("Invalid input for GapCursor.fromJSON");
        }
        return new t(e.resolve(n.pos));
    }

    getBookmark() {
        return new il(this.anchor);
    }

    static valid(e) {
        const n = e.parent;
        if (n.isTextblock || !m0(e) || !g0(e)) return !1;
        const r = n.type.spec.allowGapCursor;
        if (r != null) return r;
        const o = n.contentMatchAt(e.index()).defaultType;
        return o && o.isTextblock;
    }

    static findGapCursorFrom(e, n, r = !1) {
        e: for (;;) {
            if (!r && t.valid(e)) return e;
            let o = e.pos;
            let i = null;
            for (let s = e.depth; ; s--) {
                const l = e.node(s);
                if (n > 0 ? e.indexAfter(s) < l.childCount : e.index(s) > 0) {
                    i = l.child(n > 0 ? e.indexAfter(s) : e.index(s) - 1);
                    break;
                } else if (s == 0) return null;
                o += n;
                const a = e.doc.resolve(o);
                if (t.valid(a)) return a;
            }
            for (;;) {
                const s = n > 0 ? i.firstChild : i.lastChild;
                if (!s) {
                    if (i.isAtom && !i.isText && !P.isSelectable(i)) {
                        ((e = e.doc.resolve(o + i.nodeSize * n)), (r = !1));
                        continue e;
                    }
                    break;
                }
                ((i = s), (o += n));
                const l = e.doc.resolve(o);
                if (t.valid(l)) return l;
            }
            return null;
        }
    }
};
se.prototype.visible = !1;
se.findFrom = se.findGapCursorFrom;
I.jsonID("gapcursor", se);
var il = class t {
    constructor(e) {
        this.pos = e;
    }

    map(e) {
        return new t(e.map(this.pos));
    }

    resolve(e) {
        const n = e.resolve(this.pos);
        return se.valid(n) ? new se(n) : I.near(n);
    }
};
function gu(t) {
    return t.isAtom || t.spec.isolating || t.spec.createGapCursor;
}
function m0(t) {
    for (let e = t.depth; e >= 0; e--) {
        const n = t.index(e);
        const r = t.node(e);
        if (n == 0) {
            if (r.type.spec.isolating) return !0;
            continue;
        }
        for (let o = r.child(n - 1); ; o = o.lastChild) {
            if ((o.childCount == 0 && !o.inlineContent) || gu(o.type)) {
                return !0;
            }
            if (o.inlineContent) return !1;
        }
    }
    return !0;
}
function g0(t) {
    for (let e = t.depth; e >= 0; e--) {
        const n = t.indexAfter(e);
        const r = t.node(e);
        if (n == r.childCount) {
            if (r.type.spec.isolating) return !0;
            continue;
        }
        for (let o = r.child(n); ; o = o.firstChild) {
            if ((o.childCount == 0 && !o.inlineContent) || gu(o.type)) {
                return !0;
            }
            if (o.inlineContent) return !1;
        }
    }
    return !0;
}
function yu() {
    return new L({
        props: {
            decorations: x0,
            createSelectionBetween(t, e, n) {
                return e.pos == n.pos && se.valid(n) ? new se(n) : null;
            },
            handleClick: b0,
            handleKeyDown: y0,
            handleDOMEvents: { beforeinput: w0 },
        },
    });
}
var y0 = Qn({
    ArrowLeft: go("horiz", -1),
    ArrowRight: go("horiz", 1),
    ArrowUp: go("vert", -1),
    ArrowDown: go("vert", 1),
});
function go(t, e) {
    const n = t == "vert" ? (e > 0 ? "down" : "up") : e > 0 ? "right" : "left";
    return function (r, o, i) {
        const s = r.selection;
        let l = e > 0 ? s.$to : s.$from;
        let a = s.empty;
        if (s instanceof R) {
            if (!i.endOfTextblock(n) || l.depth == 0) return !1;
            ((a = !1), (l = r.doc.resolve(e > 0 ? l.after() : l.before())));
        }
        const c = se.findGapCursorFrom(l, e, a);
        return c ? (o && o(r.tr.setSelection(new se(c))), !0) : !1;
    };
}
function b0(t, e, n) {
    if (!t || !t.editable) return !1;
    const r = t.state.doc.resolve(e);
    if (!se.valid(r)) return !1;
    const o = t.posAtCoords({ left: n.clientX, top: n.clientY });
    return o && o.inside > -1 && P.isSelectable(t.state.doc.nodeAt(o.inside))
        ? !1
        : (t.dispatch(t.state.tr.setSelection(new se(r))), !0);
}
function w0(t, e) {
    if (
        e.inputType != "insertCompositionText" ||
        !(t.state.selection instanceof se)
    ) {
        return !1;
    }
    const { $from: n } = t.state.selection;
    const r = n.parent
        .contentMatchAt(n.index())
        .findWrapping(t.state.schema.nodes.text);
    if (!r) return !1;
    let o = C.empty;
    for (let s = r.length - 1; s >= 0; s--) {
        o = C.from(r[s].createAndFill(null, o));
    }
    const i = t.state.tr.replace(n.pos, n.pos, new E(o, 0, 0));
    return (
        i.setSelection(R.near(i.doc.resolve(n.pos + 1))),
        t.dispatch(i),
        !1
    );
}
function x0(t) {
    if (!(t.selection instanceof se)) return null;
    const e = document.createElement("div");
    return (
        (e.className = "ProseMirror-gapcursor"),
        X.create(t.doc, [Q.widget(t.selection.head, e, { key: "gapcursor" })])
    );
}
const yo = 200;
const ue = function () {};
ue.prototype.append = function (e) {
    return e.length
        ? ((e = ue.from(e)),
          (!this.length && e) ||
              (e.length < yo && this.leafAppend(e)) ||
              (this.length < yo && e.leafPrepend(this)) ||
              this.appendInner(e))
        : this;
};
ue.prototype.prepend = function (e) {
    return e.length ? ue.from(e).append(this) : this;
};
ue.prototype.appendInner = function (e) {
    return new k0(this, e);
};
ue.prototype.slice = function (e, n) {
    return (
        e === void 0 && (e = 0),
        n === void 0 && (n = this.length),
        e >= n
            ? ue.empty
            : this.sliceInner(Math.max(0, e), Math.min(this.length, n))
    );
};
ue.prototype.get = function (e) {
    if (!(e < 0 || e >= this.length)) return this.getInner(e);
};
ue.prototype.forEach = function (e, n, r) {
    (n === void 0 && (n = 0),
        r === void 0 && (r = this.length),
        n <= r
            ? this.forEachInner(e, n, r, 0)
            : this.forEachInvertedInner(e, n, r, 0));
};
ue.prototype.map = function (e, n, r) {
    (n === void 0 && (n = 0), r === void 0 && (r = this.length));
    const o = [];
    return (
        this.forEach(
            function (i, s) {
                return o.push(e(i, s));
            },
            n,
            r,
        ),
        o
    );
};
ue.from = function (e) {
    return e instanceof ue ? e : e && e.length ? new bu(e) : ue.empty;
};
var bu = (function (t) {
    function e(r) {
        (t.call(this), (this.values = r));
    }
    (t && (e.__proto__ = t),
        (e.prototype = Object.create(t && t.prototype)),
        (e.prototype.constructor = e));
    const n = { length: { configurable: !0 }, depth: { configurable: !0 } };
    return (
        (e.prototype.flatten = function () {
            return this.values;
        }),
        (e.prototype.sliceInner = function (o, i) {
            return o == 0 && i == this.length
                ? this
                : new e(this.values.slice(o, i));
        }),
        (e.prototype.getInner = function (o) {
            return this.values[o];
        }),
        (e.prototype.forEachInner = function (o, i, s, l) {
            for (let a = i; a < s; a++) {
                if (o(this.values[a], l + a) === !1) return !1;
            }
        }),
        (e.prototype.forEachInvertedInner = function (o, i, s, l) {
            for (let a = i - 1; a >= s; a--) {
                if (o(this.values[a], l + a) === !1) return !1;
            }
        }),
        (e.prototype.leafAppend = function (o) {
            if (this.length + o.length <= yo) {
                return new e(this.values.concat(o.flatten()));
            }
        }),
        (e.prototype.leafPrepend = function (o) {
            if (this.length + o.length <= yo) {
                return new e(o.flatten().concat(this.values));
            }
        }),
        (n.length.get = function () {
            return this.values.length;
        }),
        (n.depth.get = function () {
            return 0;
        }),
        Object.defineProperties(e.prototype, n),
        e
    );
})(ue);
ue.empty = new bu([]);
var k0 = (function (t) {
    function e(n, r) {
        (t.call(this),
            (this.left = n),
            (this.right = r),
            (this.length = n.length + r.length),
            (this.depth = Math.max(n.depth, r.depth) + 1));
    }
    return (
        t && (e.__proto__ = t),
        (e.prototype = Object.create(t && t.prototype)),
        (e.prototype.constructor = e),
        (e.prototype.flatten = function () {
            return this.left.flatten().concat(this.right.flatten());
        }),
        (e.prototype.getInner = function (r) {
            return r < this.left.length
                ? this.left.get(r)
                : this.right.get(r - this.left.length);
        }),
        (e.prototype.forEachInner = function (r, o, i, s) {
            const l = this.left.length;
            if (
                (o < l &&
                    this.left.forEachInner(r, o, Math.min(i, l), s) === !1) ||
                (i > l &&
                    this.right.forEachInner(
                        r,
                        Math.max(o - l, 0),
                        Math.min(this.length, i) - l,
                        s + l,
                    ) === !1)
            ) {
                return !1;
            }
        }),
        (e.prototype.forEachInvertedInner = function (r, o, i, s) {
            const l = this.left.length;
            if (
                (o > l &&
                    this.right.forEachInvertedInner(
                        r,
                        o - l,
                        Math.max(i, l) - l,
                        s + l,
                    ) === !1) ||
                (i < l &&
                    this.left.forEachInvertedInner(r, Math.min(o, l), i, s) ===
                        !1)
            ) {
                return !1;
            }
        }),
        (e.prototype.sliceInner = function (r, o) {
            if (r == 0 && o == this.length) return this;
            const i = this.left.length;
            return o <= i
                ? this.left.slice(r, o)
                : r >= i
                  ? this.right.slice(r - i, o - i)
                  : this.left.slice(r, i).append(this.right.slice(0, o - i));
        }),
        (e.prototype.leafAppend = function (r) {
            const o = this.right.leafAppend(r);
            if (o) return new e(this.left, o);
        }),
        (e.prototype.leafPrepend = function (r) {
            const o = this.left.leafPrepend(r);
            if (o) return new e(o, this.right);
        }),
        (e.prototype.appendInner = function (r) {
            return this.left.depth >= Math.max(this.right.depth, r.depth) + 1
                ? new e(this.left, new e(this.right, r))
                : new e(this, r);
        }),
        e
    );
})(ue);
const sl = ue;
const S0 = 500;
const Qt = class t {
    constructor(e, n) {
        ((this.items = e), (this.eventCount = n));
    }

    popEvent(e, n) {
        if (this.eventCount == 0) return null;
        let r = this.items.length;
        for (; ; r--) {
            if (this.items.get(r - 1).selection) {
                --r;
                break;
            }
        }
        let o, i;
        n && ((o = this.remapping(r, this.items.length)), (i = o.maps.length));
        const s = e.tr;
        let l;
        let a;
        const c = [];
        const d = [];
        return (
            this.items.forEach(
                (u, f) => {
                    if (!u.step) {
                        (o ||
                            ((o = this.remapping(r, f + 1)),
                            (i = o.maps.length)),
                            i--,
                            d.push(u));
                        return;
                    }
                    if (o) {
                        d.push(new Qe(u.map));
                        const h = u.step.map(o.slice(i));
                        let p;
                        (h &&
                            s.maybeStep(h).doc &&
                            ((p = s.mapping.maps[s.mapping.maps.length - 1]),
                            c.push(
                                new Qe(p, void 0, void 0, c.length + d.length),
                            )),
                            i--,
                            p && o.appendMap(p, i));
                    } else s.maybeStep(u.step);
                    if (u.selection) {
                        return (
                            (l = o ? u.selection.map(o.slice(i)) : u.selection),
                            (a = new t(
                                this.items
                                    .slice(0, r)
                                    .append(d.reverse().concat(c)),
                                this.eventCount - 1,
                            )),
                            !1
                        );
                    }
                },
                this.items.length,
                0,
            ),
            { remaining: a, transform: s, selection: l }
        );
    }

    addTransform(e, n, r, o) {
        const i = [];
        let s = this.eventCount;
        let l = this.items;
        let a = !o && l.length ? l.get(l.length - 1) : null;
        for (let d = 0; d < e.steps.length; d++) {
            const u = e.steps[d].invert(e.docs[d]);
            let f = new Qe(e.mapping.maps[d], u, n);
            let h;
            ((h = a && a.merge(f)) &&
                ((f = h), d ? i.pop() : (l = l.slice(0, l.length - 1))),
                i.push(f),
                n && (s++, (n = void 0)),
                o || (a = f));
        }
        const c = s - r.depth;
        return (c > v0 && ((l = C0(l, c)), (s -= c)), new t(l.append(i), s));
    }

    remapping(e, n) {
        const r = new Hn();
        return (
            this.items.forEach(
                (o, i) => {
                    const s =
                        o.mirrorOffset != null && i - o.mirrorOffset >= e
                            ? r.maps.length - o.mirrorOffset
                            : void 0;
                    r.appendMap(o.map, s);
                },
                e,
                n,
            ),
            r
        );
    }

    addMaps(e) {
        return this.eventCount == 0
            ? this
            : new t(
                  this.items.append(e.map((n) => new Qe(n))),
                  this.eventCount,
              );
    }

    rebased(e, n) {
        if (!this.eventCount) return this;
        const r = [];
        const o = Math.max(0, this.items.length - n);
        const i = e.mapping;
        let s = e.steps.length;
        let l = this.eventCount;
        this.items.forEach((f) => {
            f.selection && l--;
        }, o);
        let a = n;
        this.items.forEach((f) => {
            const h = i.getMirror(--a);
            if (h == null) return;
            s = Math.min(s, h);
            const p = i.maps[h];
            if (f.step) {
                const m = e.steps[h].invert(e.docs[h]);
                const g = f.selection && f.selection.map(i.slice(a + 1, h));
                (g && l++, r.push(new Qe(p, m, g)));
            } else r.push(new Qe(p));
        }, o);
        const c = [];
        for (let f = n; f < s; f++) c.push(new Qe(i.maps[f]));
        const d = this.items.slice(0, o).append(c).append(r);
        let u = new t(d, l);
        return (
            u.emptyItemCount() > S0 &&
                (u = u.compress(this.items.length - r.length)),
            u
        );
    }

    emptyItemCount() {
        let e = 0;
        return (
            this.items.forEach((n) => {
                n.step || e++;
            }),
            e
        );
    }

    compress(e = this.items.length) {
        const n = this.remapping(0, e);
        let r = n.maps.length;
        const o = [];
        let i = 0;
        return (
            this.items.forEach(
                (s, l) => {
                    if (l >= e) (o.push(s), s.selection && i++);
                    else if (s.step) {
                        const a = s.step.map(n.slice(r));
                        const c = a && a.getMap();
                        if ((r--, c && n.appendMap(c, r), a)) {
                            const d =
                                s.selection && s.selection.map(n.slice(r));
                            d && i++;
                            const u = new Qe(c.invert(), a, d);
                            let f;
                            const h = o.length - 1;
                            (f = o.length && o[h].merge(u))
                                ? (o[h] = f)
                                : o.push(u);
                        }
                    } else s.map && r--;
                },
                this.items.length,
                0,
            ),
            new t(sl.from(o.reverse()), i)
        );
    }
};
Qt.empty = new Qt(sl.empty, 0);
function C0(t, e) {
    let n;
    return (
        t.forEach((r, o) => {
            if (r.selection && e-- == 0) return ((n = o), !1);
        }),
        t.slice(n)
    );
}
var Qe = class t {
    constructor(e, n, r, o) {
        ((this.map = e),
            (this.step = n),
            (this.selection = r),
            (this.mirrorOffset = o));
    }

    merge(e) {
        if (this.step && e.step && !e.selection) {
            const n = e.step.merge(this.step);
            if (n) return new t(n.getMap().invert(), n, this.selection);
        }
    }
};
const Ze = class {
    constructor(e, n, r, o, i) {
        ((this.done = e),
            (this.undone = n),
            (this.prevRanges = r),
            (this.prevTime = o),
            (this.prevComposition = i));
    }
};
var v0 = 20;
function M0(t, e, n, r) {
    const o = n.getMeta(Yt);
    let i;
    if (o) return o.historyState;
    n.getMeta(E0) && (t = new Ze(t.done, t.undone, null, 0, -1));
    const s = n.getMeta("appendedTransaction");
    if (n.steps.length == 0) return t;
    if (s && s.getMeta(Yt)) {
        return s.getMeta(Yt).redo
            ? new Ze(
                  t.done.addTransform(n, void 0, r, bo(e)),
                  t.undone,
                  wu(n.mapping.maps),
                  t.prevTime,
                  t.prevComposition,
              )
            : new Ze(
                  t.done,
                  t.undone.addTransform(n, void 0, r, bo(e)),
                  null,
                  t.prevTime,
                  t.prevComposition,
              );
    }
    if (
        n.getMeta("addToHistory") !== !1 &&
        !(s && s.getMeta("addToHistory") === !1)
    ) {
        const l = n.getMeta("composition");
        const a =
            t.prevTime == 0 ||
            (!s &&
                t.prevComposition != l &&
                (t.prevTime < (n.time || 0) - r.newGroupDelay ||
                    !T0(n, t.prevRanges)));
        const c = s ? ll(t.prevRanges, n.mapping) : wu(n.mapping.maps);
        return new Ze(
            t.done.addTransform(
                n,
                a ? e.selection.getBookmark() : void 0,
                r,
                bo(e),
            ),
            Qt.empty,
            c,
            n.time,
            l ?? t.prevComposition,
        );
    } else {
        return (i = n.getMeta("rebased"))
            ? new Ze(
                  t.done.rebased(n, i),
                  t.undone.rebased(n, i),
                  ll(t.prevRanges, n.mapping),
                  t.prevTime,
                  t.prevComposition,
              )
            : new Ze(
                  t.done.addMaps(n.mapping.maps),
                  t.undone.addMaps(n.mapping.maps),
                  ll(t.prevRanges, n.mapping),
                  t.prevTime,
                  t.prevComposition,
              );
    }
}
function T0(t, e) {
    if (!e) return !1;
    if (!t.docChanged) return !0;
    let n = !1;
    return (
        t.mapping.maps[0].forEach((r, o) => {
            for (let i = 0; i < e.length; i += 2) {
                r <= e[i + 1] && o >= e[i] && (n = !0);
            }
        }),
        n
    );
}
function wu(t) {
    const e = [];
    for (let n = t.length - 1; n >= 0 && e.length == 0; n--) {
        t[n].forEach((r, o, i, s) => e.push(i, s));
    }
    return e;
}
function ll(t, e) {
    if (!t) return null;
    const n = [];
    for (let r = 0; r < t.length; r += 2) {
        const o = e.map(t[r], 1);
        const i = e.map(t[r + 1], -1);
        o <= i && n.push(o, i);
    }
    return n;
}
function A0(t, e, n) {
    const r = bo(e);
    const o = Yt.get(e).spec.config;
    const i = (n ? t.undone : t.done).popEvent(e, r);
    if (!i) return null;
    const s = i.selection.resolve(i.transform.doc);
    const l = (n ? t.done : t.undone).addTransform(
        i.transform,
        e.selection.getBookmark(),
        o,
        r,
    );
    const a = new Ze(n ? l : i.remaining, n ? i.remaining : l, null, 0, -1);
    return i.transform
        .setSelection(s)
        .setMeta(Yt, { redo: n, historyState: a });
}
let al = !1;
let xu = null;
function bo(t) {
    const e = t.plugins;
    if (xu != e) {
        ((al = !1), (xu = e));
        for (let n = 0; n < e.length; n++) {
            if (e[n].spec.historyPreserveItems) {
                al = !0;
                break;
            }
        }
    }
    return al;
}
var Yt = new H("history");
var E0 = new H("closeHistory");
function ku(t = {}) {
    return (
        (t = { depth: t.depth || 100, newGroupDelay: t.newGroupDelay || 500 }),
        new L({
            key: Yt,
            state: {
                init() {
                    return new Ze(Qt.empty, Qt.empty, null, 0, -1);
                },
                apply(e, n, r) {
                    return M0(n, r, e, t);
                },
            },
            config: t,
            props: {
                handleDOMEvents: {
                    beforeinput(e, n) {
                        const r = n.inputType;
                        const o =
                            r == "historyUndo"
                                ? cl
                                : r == "historyRedo"
                                  ? dl
                                  : null;
                        return o
                            ? (n.preventDefault(), o(e.state, e.dispatch))
                            : !1;
                    },
                },
            },
        })
    );
}
function wo(t, e) {
    return (n, r) => {
        const o = Yt.getState(n);
        if (!o || (t ? o.undone : o.done).eventCount == 0) return !1;
        if (r) {
            const i = A0(o, n, t);
            i && r(e ? i.scrollIntoView() : i);
        }
        return !0;
    };
}
var cl = wo(!1, !0);
var dl = wo(!0, !0);
const t1 = wo(!1, !1);
const n1 = wo(!0, !1);
const a1 = j.create({
    name: "characterCount",
    addOptions() {
        return {
            limit: null,
            mode: "textSize",
            textCounter: (t) => t.length,
            wordCounter: (t) => t.split(" ").filter((e) => e !== "").length,
        };
    },
    addStorage() {
        return { characters: () => 0, words: () => 0 };
    },
    onBeforeCreate() {
        ((this.storage.characters = (t) => {
            const e = t?.node || this.editor.state.doc;
            if ((t?.mode || this.options.mode) === "textSize") {
                const r = e.textBetween(0, e.content.size, void 0, " ");
                return this.options.textCounter(r);
            }
            return e.nodeSize;
        }),
            (this.storage.words = (t) => {
                const e = t?.node || this.editor.state.doc;
                const n = e.textBetween(0, e.content.size, " ", " ");
                return this.options.wordCounter(n);
            }));
    },
    addProseMirrorPlugins() {
        let t = !1;
        return [
            new L({
                key: new H("characterCount"),
                appendTransaction: (e, n, r) => {
                    if (t) return;
                    const o = this.options.limit;
                    if (o == null || o === 0) {
                        t = !0;
                        return;
                    }
                    const i = this.storage.characters({ node: r.doc });
                    if (i > o) {
                        const s = i - o;
                        const l = 0;
                        const a = s;
                        console.warn(
                            `[CharacterCount] Initial content exceeded limit of ${o} characters. Content was automatically trimmed.`,
                        );
                        const c = r.tr.deleteRange(l, a);
                        return ((t = !0), c);
                    }
                    t = !0;
                },
                filterTransaction: (e, n) => {
                    const r = this.options.limit;
                    if (
                        !e.docChanged ||
                        r === 0 ||
                        r === null ||
                        r === void 0
                    ) {
                        return !0;
                    }
                    const o = this.storage.characters({ node: n.doc });
                    const i = this.storage.characters({ node: e.doc });
                    if (i <= r || (o > r && i > r && i <= o)) return !0;
                    if ((o > r && i > r && i > o) || !e.getMeta("paste")) {
                        return !1;
                    }
                    const l = e.selection.$head.pos;
                    const a = i - r;
                    const c = l - a;
                    const d = l;
                    return (
                        e.deleteRange(c, d),
                        !(this.storage.characters({ node: e.doc }) > r)
                    );
                },
            }),
        ];
    },
});
const Cu = j.create({
    name: "dropCursor",
    addOptions() {
        return { color: "currentColor", width: 1, class: void 0 };
    },
    addProseMirrorPlugins() {
        return [mu(this.options)];
    },
});
const p1 = j.create({
    name: "focus",
    addOptions() {
        return { className: "has-focus", mode: "all" };
    },
    addProseMirrorPlugins() {
        return [
            new L({
                key: new H("focus"),
                props: {
                    decorations: ({ doc: t, selection: e }) => {
                        const { isEditable: n, isFocused: r } = this.editor;
                        const { anchor: o } = e;
                        const i = [];
                        if (!n || !r) return X.create(t, []);
                        let s = 0;
                        this.options.mode === "deepest" &&
                            t.descendants((a, c) => {
                                if (a.isText) return;
                                if (!(o >= c && o <= c + a.nodeSize - 1)) {
                                    return !1;
                                }
                                s += 1;
                            });
                        let l = 0;
                        return (
                            t.descendants((a, c) => {
                                if (
                                    a.isText ||
                                    !(o >= c && o <= c + a.nodeSize - 1)
                                ) {
                                    return !1;
                                }
                                if (
                                    ((l += 1),
                                    (this.options.mode === "deepest" &&
                                        s - l > 0) ||
                                        (this.options.mode === "shallowest" &&
                                            l > 1))
                                ) {
                                    return this.options.mode === "deepest";
                                }
                                i.push(
                                    Q.node(c, c + a.nodeSize, {
                                        class: this.options.className,
                                    }),
                                );
                            }),
                            X.create(t, i)
                        );
                    },
                },
            }),
        ];
    },
});
const vu = j.create({
    name: "gapCursor",
    addProseMirrorPlugins() {
        return [yu()];
    },
    extendNodeSchema(t) {
        let e;
        const n = { name: t.name, options: t.options, storage: t.storage };
        return {
            allowGapCursor:
                (e = J(z(t, "allowGapCursor", n))) != null ? e : null,
        };
    },
});
const ul = j.create({
    name: "placeholder",
    addOptions() {
        return {
            emptyEditorClass: "is-editor-empty",
            emptyNodeClass: "is-empty",
            placeholder: "Write something \u2026",
            showOnlyWhenEditable: !0,
            showOnlyCurrent: !0,
            includeChildren: !1,
        };
    },
    addProseMirrorPlugins() {
        return [
            new L({
                key: new H("placeholder"),
                props: {
                    decorations: ({ doc: t, selection: e }) => {
                        const n =
                            this.editor.isEditable ||
                            !this.options.showOnlyWhenEditable;
                        const { anchor: r } = e;
                        const o = [];
                        if (!n) return null;
                        const i = this.editor.isEmpty;
                        return (
                            t.descendants((s, l) => {
                                const a = r >= l && r <= l + s.nodeSize;
                                const c = !s.isLeaf && nr(s);
                                if ((a || !this.options.showOnlyCurrent) && c) {
                                    const d = [this.options.emptyNodeClass];
                                    i && d.push(this.options.emptyEditorClass);
                                    const u = Q.node(l, l + s.nodeSize, {
                                        class: d.join(" "),
                                        "data-placeholder":
                                            typeof this.options.placeholder ===
                                            "function"
                                                ? this.options.placeholder({
                                                      editor: this.editor,
                                                      node: s,
                                                      pos: l,
                                                      hasAnchor: a,
                                                  })
                                                : this.options.placeholder,
                                    });
                                    o.push(u);
                                }
                                return this.options.includeChildren;
                            }),
                            X.create(t, o)
                        );
                    },
                },
            }),
        ];
    },
});
const C1 = j.create({
    name: "selection",
    addOptions() {
        return { className: "selection" };
    },
    addProseMirrorPlugins() {
        const { editor: t, options: e } = this;
        return [
            new L({
                key: new H("selection"),
                props: {
                    decorations(n) {
                        return n.selection.empty ||
                            t.isFocused ||
                            !t.isEditable ||
                            uo(n.selection) ||
                            t.view.dragging
                            ? null
                            : X.create(n.doc, [
                                  Q.inline(n.selection.from, n.selection.to, {
                                      class: e.className,
                                  }),
                              ]);
                    },
                },
            }),
        ];
    },
});
function Su({ types: t, node: e }) {
    return (e && Array.isArray(t) && t.includes(e.type)) || e?.type === t;
}
const T1 = j.create({
    name: "trailingNode",
    addOptions() {
        return { node: void 0, notAfter: [] };
    },
    addProseMirrorPlugins() {
        let t;
        const e = new H(this.name);
        const n =
            ((t = this.editor.schema.topNodeType.contentMatch.defaultType) ==
            null
                ? void 0
                : t.name) ||
            this.options.node ||
            "paragraph";
        const r = Object.entries(this.editor.schema.nodes)
            .map(([, o]) => o)
            .filter((o) =>
                (this.options.notAfter || []).concat(n).includes(o.name),
            );
        return [
            new L({
                key: e,
                appendTransaction: (o, i, s) => {
                    const { doc: l, tr: a, schema: c } = s;
                    const d = e.getState(s);
                    const u = l.content.size;
                    const f = c.nodes[n];
                    if (d) return a.insert(u, f.create());
                },
                state: {
                    init: (o, i) => {
                        const s = i.tr.doc.lastChild;
                        return !Su({ node: s, types: r });
                    },
                    apply: (o, i) => {
                        if (!o.docChanged) return i;
                        const s = o.doc.lastChild;
                        return !Su({ node: s, types: r });
                    },
                },
            }),
        ];
    },
});
const Mu = j.create({
    name: "undoRedo",
    addOptions() {
        return { depth: 100, newGroupDelay: 500 };
    },
    addCommands() {
        return {
            undo:
                () =>
                ({ state: t, dispatch: e }) =>
                    cl(t, e),
            redo:
                () =>
                ({ state: t, dispatch: e }) =>
                    dl(t, e),
        };
    },
    addProseMirrorPlugins() {
        return [ku(this.options)];
    },
    addKeyboardShortcuts() {
        return {
            "Mod-z": () => this.editor.commands.undo(),
            "Shift-Mod-z": () => this.editor.commands.redo(),
            "Mod-y": () => this.editor.commands.redo(),
            "Mod-\u044F": () => this.editor.commands.undo(),
            "Shift-Mod-\u044F": () => this.editor.commands.redo(),
        };
    },
});
const vn = (t, e) => {
    if (t === "slot") return 0;
    if (t instanceof Function) return t(e);
    const { children: n, ...r } = e ?? {};
    if (t === "svg") {
        throw new Error(
            "SVG elements are not supported in the JSX syntax, use the array syntax instead",
        );
    }
    return [t, r, n];
};
const N0 = /^\s*>\s$/;
const O0 = $.create({
    name: "blockquote",
    addOptions() {
        return { HTMLAttributes: {} };
    },
    content: "block+",
    group: "block",
    defining: !0,
    parseHTML() {
        return [{ tag: "blockquote" }];
    },
    renderHTML({ HTMLAttributes: t }) {
        return vn("blockquote", {
            ...O(this.options.HTMLAttributes, t),
            children: vn("slot", {}),
        });
    },
    parseMarkdown: (t, e) =>
        e.createNode("blockquote", void 0, e.parseChildren(t.tokens || [])),
    renderMarkdown: (t, e) => {
        if (!t.content) return "";
        const n = [];
        return (
            t.content.forEach((o) => {
                const s = e
                    .renderChildren(o)
                    .split(
                        `
`,
                    )
                    .map((l) => `> ${l}`).join(`
`);
                n.push(s);
            }),
            n.flatMap((o) => [o, "> "]).slice(0, -1).join(`
`)
        );
    },
    addCommands() {
        return {
            setBlockquote:
                () =>
                ({ commands: t }) =>
                    t.wrapIn(this.name),
            toggleBlockquote:
                () =>
                ({ commands: t }) =>
                    t.toggleWrap(this.name),
            unsetBlockquote:
                () =>
                ({ commands: t }) =>
                    t.lift(this.name),
        };
    },
    addKeyboardShortcuts() {
        return { "Mod-Shift-b": () => this.editor.commands.toggleBlockquote() };
    },
    addInputRules() {
        return [Ye({ find: N0, type: this.type })];
    },
});
const Tu = O0;
const R0 = /(?:^|\s)(\*\*(?!\s+\*\*)((?:[^*]+))\*\*(?!\s+\*\*))$/;
const D0 = /(?:^|\s)(\*\*(?!\s+\*\*)((?:[^*]+))\*\*(?!\s+\*\*))/g;
const I0 = /(?:^|\s)(__(?!\s+__)((?:[^_]+))__(?!\s+__))$/;
const P0 = /(?:^|\s)(__(?!\s+__)((?:[^_]+))__(?!\s+__))/g;
const L0 = Z.create({
    name: "bold",
    addOptions() {
        return { HTMLAttributes: {} };
    },
    parseHTML() {
        return [
            { tag: "strong" },
            {
                tag: "b",
                getAttrs: (t) => t.style.fontWeight !== "normal" && null,
            },
            {
                style: "font-weight=400",
                clearMark: (t) => t.type.name === this.name,
            },
            {
                style: "font-weight",
                getAttrs: (t) => /^(bold(er)?|[5-9]\d{2,})$/.test(t) && null,
            },
        ];
    },
    renderHTML({ HTMLAttributes: t }) {
        return vn("strong", {
            ...O(this.options.HTMLAttributes, t),
            children: vn("slot", {}),
        });
    },
    markdownTokenName: "strong",
    parseMarkdown: (t, e) => e.applyMark("bold", e.parseInline(t.tokens || [])),
    renderMarkdown: (t, e) => `**${e.renderChildren(t)}**`,
    addCommands() {
        return {
            setBold:
                () =>
                ({ commands: t }) =>
                    t.setMark(this.name),
            toggleBold:
                () =>
                ({ commands: t }) =>
                    t.toggleMark(this.name),
            unsetBold:
                () =>
                ({ commands: t }) =>
                    t.unsetMark(this.name),
        };
    },
    addKeyboardShortcuts() {
        return {
            "Mod-b": () => this.editor.commands.toggleBold(),
            "Mod-B": () => this.editor.commands.toggleBold(),
        };
    },
    addInputRules() {
        return [
            Le({ find: R0, type: this.type }),
            Le({ find: I0, type: this.type }),
        ];
    },
    addPasteRules() {
        return [
            Ce({ find: D0, type: this.type }),
            Ce({ find: P0, type: this.type }),
        ];
    },
});
const Au = L0;
const z0 = /(^|[^`])`([^`]+)`(?!`)$/;
const B0 = /(^|[^`])`([^`]+)`(?!`)/g;
const H0 = Z.create({
    name: "code",
    addOptions() {
        return { HTMLAttributes: {} };
    },
    excludes: "_",
    code: !0,
    exitable: !0,
    parseHTML() {
        return [{ tag: "code" }];
    },
    renderHTML({ HTMLAttributes: t }) {
        return ["code", O(this.options.HTMLAttributes, t), 0];
    },
    markdownTokenName: "codespan",
    parseMarkdown: (t, e) =>
        e.applyMark("code", [{ type: "text", text: t.text || "" }]),
    renderMarkdown: (t, e) =>
        t.content ? `\`${e.renderChildren(t.content)}\`` : "",
    addCommands() {
        return {
            setCode:
                () =>
                ({ commands: t }) =>
                    t.setMark(this.name),
            toggleCode:
                () =>
                ({ commands: t }) =>
                    t.toggleMark(this.name),
            unsetCode:
                () =>
                ({ commands: t }) =>
                    t.unsetMark(this.name),
        };
    },
    addKeyboardShortcuts() {
        return { "Mod-e": () => this.editor.commands.toggleCode() };
    },
    addInputRules() {
        return [Le({ find: z0, type: this.type })];
    },
    addPasteRules() {
        return [Ce({ find: B0, type: this.type })];
    },
});
const Eu = H0;
const fl = 4;
const $0 = /^```([a-z]+)?[\s\n]$/;
const F0 = /^~~~([a-z]+)?[\s\n]$/;
const _0 = $.create({
    name: "codeBlock",
    addOptions() {
        return {
            languageClassPrefix: "language-",
            exitOnTripleEnter: !0,
            exitOnArrowDown: !0,
            defaultLanguage: null,
            enableTabIndentation: !1,
            tabSize: fl,
            HTMLAttributes: {},
        };
    },
    content: "text*",
    marks: "",
    group: "block",
    code: !0,
    defining: !0,
    addAttributes() {
        return {
            language: {
                default: this.options.defaultLanguage,
                parseHTML: (t) => {
                    let e;
                    const { languageClassPrefix: n } = this.options;
                    if (!n) return null;
                    const i = [
                        ...(((e = t.firstElementChild) == null
                            ? void 0
                            : e.classList) || []),
                    ]
                        .filter((s) => s.startsWith(n))
                        .map((s) => s.replace(n, ""))[0];
                    return i || null;
                },
                rendered: !1,
            },
        };
    },
    parseHTML() {
        return [{ tag: "pre", preserveWhitespace: "full" }];
    },
    renderHTML({ node: t, HTMLAttributes: e }) {
        return [
            "pre",
            O(this.options.HTMLAttributes, e),
            [
                "code",
                {
                    class: t.attrs.language
                        ? this.options.languageClassPrefix + t.attrs.language
                        : null,
                },
                0,
            ],
        ];
    },
    markdownTokenName: "code",
    parseMarkdown: (t, e) => {
        let n;
        return ((n = t.raw) == null ? void 0 : n.startsWith("```")) === !1 &&
            t.codeBlockStyle !== "indented"
            ? []
            : e.createNode(
                  "codeBlock",
                  { language: t.lang || null },
                  t.text ? [e.createTextNode(t.text)] : [],
              );
    },
    renderMarkdown: (t, e) => {
        let n;
        let r = "";
        const o = ((n = t.attrs) == null ? void 0 : n.language) || "";
        return (
            t.content
                ? (r = [`\`\`\`${o}`, e.renderChildren(t.content), "```"].join(`
`))
                : (r = `\`\`\`${o}

\`\`\``),
            r
        );
    },
    addCommands() {
        return {
            setCodeBlock:
                (t) =>
                ({ commands: e }) =>
                    e.setNode(this.name, t),
            toggleCodeBlock:
                (t) =>
                ({ commands: e }) =>
                    e.toggleNode(this.name, "paragraph", t),
        };
    },
    addKeyboardShortcuts() {
        return {
            "Mod-Alt-c": () => this.editor.commands.toggleCodeBlock(),
            Backspace: () => {
                const { empty: t, $anchor: e } = this.editor.state.selection;
                const n = e.pos === 1;
                return !t || e.parent.type.name !== this.name
                    ? !1
                    : n || !e.parent.textContent.length
                      ? this.editor.commands.clearNodes()
                      : !1;
            },
            Tab: ({ editor: t }) => {
                let e;
                if (!this.options.enableTabIndentation) return !1;
                const n = (e = this.options.tabSize) != null ? e : fl;
                const { state: r } = t;
                const { selection: o } = r;
                const { $from: i, empty: s } = o;
                if (i.parent.type !== this.type) return !1;
                const l = " ".repeat(n);
                return s
                    ? t.commands.insertContent(l)
                    : t.commands.command(({ tr: a }) => {
                          const { from: c, to: d } = o;
                          const h = r.doc
                              .textBetween(
                                  c,
                                  d,
                                  `
`,
                                  `
`,
                              )
                              .split(
                                  `
`,
                              )
                              .map((p) => l + p).join(`
`);
                          return (a.replaceWith(c, d, r.schema.text(h)), !0);
                      });
            },
            "Shift-Tab": ({ editor: t }) => {
                let e;
                if (!this.options.enableTabIndentation) return !1;
                const n = (e = this.options.tabSize) != null ? e : fl;
                const { state: r } = t;
                const { selection: o } = r;
                const { $from: i, empty: s } = o;
                return i.parent.type !== this.type
                    ? !1
                    : s
                      ? t.commands.command(({ tr: l }) => {
                            let a;
                            const { pos: c } = i;
                            const d = i.start();
                            const u = i.end();
                            const h = r.doc.textBetween(
                                d,
                                u,
                                `
`,
                                `
`,
                            ).split(`
`);
                            let p = 0;
                            let m = 0;
                            const g = c - d;
                            for (let S = 0; S < h.length; S += 1) {
                                if (m + h[S].length >= g) {
                                    p = S;
                                    break;
                                }
                                m += h[S].length + 1;
                            }
                            const b =
                                ((a = h[p].match(/^ */)) == null
                                    ? void 0
                                    : a[0]) || "";
                            const k = Math.min(b.length, n);
                            if (k === 0) return !0;
                            let v = d;
                            for (let S = 0; S < p; S += 1) v += h[S].length + 1;
                            return (
                                l.delete(v, v + k),
                                c - v <= k &&
                                    l.setSelection(R.create(l.doc, v)),
                                !0
                            );
                        })
                      : t.commands.command(({ tr: l }) => {
                            const { from: a, to: c } = o;
                            const f = r.doc
                                .textBetween(
                                    a,
                                    c,
                                    `
`,
                                    `
`,
                                )
                                .split(
                                    `
`,
                                )
                                .map((h) => {
                                    let p;
                                    const m =
                                        ((p = h.match(/^ */)) == null
                                            ? void 0
                                            : p[0]) || "";
                                    const g = Math.min(m.length, n);
                                    return h.slice(g);
                                }).join(`
`);
                            return (l.replaceWith(a, c, r.schema.text(f)), !0);
                        });
            },
            Enter: ({ editor: t }) => {
                if (!this.options.exitOnTripleEnter) return !1;
                const { state: e } = t;
                const { selection: n } = e;
                const { $from: r, empty: o } = n;
                if (!o || r.parent.type !== this.type) return !1;
                const i = r.parentOffset === r.parent.nodeSize - 2;
                const s = r.parent.textContent.endsWith(`

`);
                return !i || !s
                    ? !1
                    : t
                          .chain()
                          .command(
                              ({ tr: l }) => (l.delete(r.pos - 2, r.pos), !0),
                          )
                          .exitCode()
                          .run();
            },
            ArrowDown: ({ editor: t }) => {
                if (!this.options.exitOnArrowDown) return !1;
                const { state: e } = t;
                const { selection: n, doc: r } = e;
                const { $from: o, empty: i } = n;
                if (
                    !i ||
                    o.parent.type !== this.type ||
                    !(o.parentOffset === o.parent.nodeSize - 2)
                ) {
                    return !1;
                }
                const l = o.after();
                return l === void 0
                    ? !1
                    : r.nodeAt(l)
                      ? t.commands.command(
                            ({ tr: c }) => (
                                c.setSelection(I.near(r.resolve(l))),
                                !0
                            ),
                        )
                      : t.commands.exitCode();
            },
        };
    },
    addInputRules() {
        return [
            rr({
                find: $0,
                type: this.type,
                getAttributes: (t) => ({ language: t[1] }),
            }),
            rr({
                find: F0,
                type: this.type,
                getAttributes: (t) => ({ language: t[1] }),
            }),
        ];
    },
    addProseMirrorPlugins() {
        return [
            new L({
                key: new H("codeBlockVSCodeHandler"),
                props: {
                    handlePaste: (t, e) => {
                        if (
                            !e.clipboardData ||
                            this.editor.isActive(this.type.name)
                        ) {
                            return !1;
                        }
                        const n = e.clipboardData.getData("text/plain");
                        const r = e.clipboardData.getData("vscode-editor-data");
                        const o = r ? JSON.parse(r) : void 0;
                        const i = o?.mode;
                        if (!n || !i) return !1;
                        const { tr: s, schema: l } = t.state;
                        const a = l.text(
                            n.replace(
                                /\r\n?/g,
                                `
`,
                            ),
                        );
                        return (
                            s.replaceSelectionWith(
                                this.type.create({ language: i }, a),
                            ),
                            s.selection.$from.parent.type !== this.type &&
                                s.setSelection(
                                    R.near(
                                        s.doc.resolve(
                                            Math.max(0, s.selection.from - 2),
                                        ),
                                    ),
                                ),
                            s.setMeta("paste", !0),
                            t.dispatch(s),
                            !0
                        );
                    },
                },
            }),
        ];
    },
});
const Nu = _0;
const Ou = $.create({
    name: "customBlock",
    group: "block",
    atom: !0,
    defining: !0,
    draggable: !0,
    selectable: !0,
    isolating: !0,
    allowGapCursor: !0,
    inline: !1,
    addNodeView() {
        return ({
            editor: t,
            node: e,
            getPos: n,
            HTMLAttributes: r,
            decorations: o,
            extension: i,
        }) => {
            const s = document.createElement("div");
            (s.setAttribute("data-config", e.attrs.config),
                s.setAttribute("data-id", e.attrs.id),
                s.setAttribute("data-type", "customBlock"));
            const l = document.createElement("div");
            if (
                ((l.className =
                    "fi-fo-rich-editor-custom-block-header fi-not-prose"),
                s.appendChild(l),
                t.isEditable &&
                    typeof e.attrs.config === "object" &&
                    e.attrs.config !== null &&
                    Object.keys(e.attrs.config).length > 0)
            ) {
                const c = document.createElement("div");
                ((c.className = "fi-fo-rich-editor-custom-block-edit-btn-ctn"),
                    l.appendChild(c));
                const d = document.createElement("button");
                ((d.className = "fi-icon-btn"),
                    (d.type = "button"),
                    (d.innerHTML = i.options.editCustomBlockButtonIconHtml),
                    d.addEventListener("click", () =>
                        i.options.editCustomBlockUsing(
                            e.attrs.id,
                            e.attrs.config,
                        ),
                    ),
                    c.appendChild(d));
            }
            const a = document.createElement("p");
            if (
                ((a.className = "fi-fo-rich-editor-custom-block-heading"),
                (a.textContent = e.attrs.label),
                l.appendChild(a),
                t.isEditable)
            ) {
                const c = document.createElement("div");
                ((c.className =
                    "fi-fo-rich-editor-custom-block-delete-btn-ctn"),
                    l.appendChild(c));
                const d = document.createElement("button");
                ((d.className = "fi-icon-btn"),
                    (d.type = "button"),
                    (d.innerHTML = i.options.deleteCustomBlockButtonIconHtml),
                    d.addEventListener("click", () =>
                        t.chain().setNodeSelection(n()).deleteSelection().run(),
                    ),
                    c.appendChild(d));
            }
            if (e.attrs.preview) {
                const c = document.createElement("div");
                ((c.className =
                    "fi-fo-rich-editor-custom-block-preview fi-not-prose"),
                    (c.innerHTML = new TextDecoder().decode(
                        Uint8Array.from(atob(e.attrs.preview), (d) =>
                            d.charCodeAt(0),
                        ),
                    )),
                    s.appendChild(c));
            }
            return { dom: s };
        };
    },
    addOptions() {
        return {
            deleteCustomBlockButtonIconHtml: null,
            editCustomBlockButtonIconHtml: null,
            editCustomBlockUsing: () => {},
            insertCustomBlockUsing: () => {},
        };
    },
    addAttributes() {
        return {
            config: {
                default: null,
                parseHTML: (t) => JSON.parse(t.getAttribute("data-config")),
            },
            id: {
                default: null,
                parseHTML: (t) => t.getAttribute("data-id"),
                renderHTML: (t) => (t.id ? { "data-id": t.id } : {}),
            },
            label: {
                default: null,
                parseHTML: (t) => t.getAttribute("data-label"),
                rendered: !1,
            },
            preview: {
                default: null,
                parseHTML: (t) => t.getAttribute("data-preview"),
                rendered: !1,
            },
        };
    },
    parseHTML() {
        return [{ tag: `div[data-type="${this.name}"]` }];
    },
    renderHTML({ HTMLAttributes: t }) {
        return ["div", O(t)];
    },
    addKeyboardShortcuts() {
        return {
            Backspace: () =>
                this.editor.commands.command(({ tr: t, state: e }) => {
                    let n = !1;
                    const { selection: r } = e;
                    const { empty: o, anchor: i } = r;
                    if (!o) return !1;
                    let s = new le();
                    let l = 0;
                    return (
                        e.doc.nodesBetween(i - 1, i, (a, c) => {
                            if (a.type.name === this.name) {
                                return ((n = !0), (s = a), (l = c), !1);
                            }
                        }),
                        n
                    );
                }),
        };
    },
    addProseMirrorPlugins() {
        const { insertCustomBlockUsing: t } = this.options;
        return [
            new L({
                props: {
                    handleDrop(e, n) {
                        if (
                            !n ||
                            (n.preventDefault(),
                            !n.dataTransfer.getData("customBlock"))
                        ) {
                            return !1;
                        }
                        const r = n.dataTransfer.getData("customBlock");
                        return (
                            t(
                                r,
                                e.posAtCoords({
                                    left: n.clientX,
                                    top: n.clientY,
                                }).pos,
                            ),
                            !1
                        );
                    },
                },
            }),
        ];
    },
});
const xo = (t, e) => e.view.domAtPos(t).node.offsetParent !== null;
const V0 = (t, e, n) => {
    for (let r = t.depth; r > 0; r -= 1) {
        const o = t.node(r);
        const i = e(o);
        const s = xo(t.start(r), n);
        if (i && s) {
            return {
                pos: r > 0 ? t.before(r) : 0,
                start: t.start(r),
                depth: r,
                node: o,
            };
        }
    }
};
const Ru = (t, e) => {
    const { state: n, view: r, extensionManager: o } = t;
    const { schema: i, selection: s } = n;
    const { empty: l, $anchor: a } = s;
    const c = !!o.extensions.find((y) => y.name === "gapCursor");
    if (
        !l ||
        a.parent.type !== i.nodes.detailsSummary ||
        !c ||
        (e === "right" && a.parentOffset !== a.parent.nodeSize - 2)
    ) {
        return !1;
    }
    const d = Xe((y) => y.type === i.nodes.details)(s);
    if (!d) return !1;
    const u = Cn(d.node, (y) => y.type === i.nodes.detailsContent);
    if (!u.length || xo(d.start + u[0].pos + 1, t)) return !1;
    const h = n.doc.resolve(d.pos + d.node.nodeSize);
    const p = se.findFrom(h, 1, !1);
    if (!p) return !1;
    const { tr: m } = n;
    const g = new se(p);
    return (m.setSelection(g), m.scrollIntoView(), r.dispatch(m), !0);
};
const Du = $.create({
    name: "details",
    content: "detailsSummary detailsContent",
    group: "block",
    defining: !0,
    isolating: !0,
    allowGapCursor: !1,
    addOptions() {
        return { persist: !1, openClassName: "is-open", HTMLAttributes: {} };
    },
    addAttributes() {
        return this.options.persist
            ? {
                  open: {
                      default: !1,
                      parseHTML: (t) => t.hasAttribute("open"),
                      renderHTML: ({ open: t }) => (t ? { open: "" } : {}),
                  },
              }
            : [];
    },
    parseHTML() {
        return [{ tag: "details" }];
    },
    renderHTML({ HTMLAttributes: t }) {
        return ["details", O(this.options.HTMLAttributes, t), 0];
    },
    ...Xt({ nodeName: "details", content: "block" }),
    addNodeView() {
        return ({ editor: t, getPos: e, node: n, HTMLAttributes: r }) => {
            const o = document.createElement("div");
            const i = O(this.options.HTMLAttributes, r, {
                "data-type": this.name,
            });
            Object.entries(i).forEach(([c, d]) => o.setAttribute(c, d));
            const s = document.createElement("button");
            ((s.type = "button"), o.append(s));
            const l = document.createElement("div");
            o.append(l);
            const a = (c) => {
                if (c !== void 0) {
                    if (c) {
                        if (o.classList.contains(this.options.openClassName)) {
                            return;
                        }
                        o.classList.add(this.options.openClassName);
                    } else {
                        if (!o.classList.contains(this.options.openClassName)) {
                            return;
                        }
                        o.classList.remove(this.options.openClassName);
                    }
                } else o.classList.toggle(this.options.openClassName);
                const d = new Event("toggleDetailsContent");
                const u = l.querySelector(
                    ':scope > div[data-type="detailsContent"]',
                );
                u?.dispatchEvent(d);
            };
            return (
                n.attrs.open && setTimeout(() => a()),
                s.addEventListener("click", () => {
                    if ((a(), !this.options.persist)) {
                        t.commands.focus(void 0, { scrollIntoView: !1 });
                        return;
                    }
                    if (t.isEditable && typeof e === "function") {
                        const { from: c, to: d } = t.state.selection;
                        t.chain()
                            .command(({ tr: u }) => {
                                const f = e();
                                if (!f) return !1;
                                const h = u.doc.nodeAt(f);
                                return h?.type !== this.type
                                    ? !1
                                    : (u.setNodeMarkup(f, void 0, {
                                          open: !h.attrs.open,
                                      }),
                                      !0);
                            })
                            .setTextSelection({ from: c, to: d })
                            .focus(void 0, { scrollIntoView: !1 })
                            .run();
                    }
                }),
                {
                    dom: o,
                    contentDOM: l,
                    ignoreMutation(c) {
                        return c.type === "selection"
                            ? !1
                            : !o.contains(c.target) || o === c.target;
                    },
                    update: (c) =>
                        c.type !== this.type
                            ? !1
                            : (c.attrs.open !== void 0 && a(c.attrs.open), !0),
                }
            );
        };
    },
    addCommands() {
        return {
            setDetails:
                () =>
                ({ state: t, chain: e }) => {
                    let n;
                    const { schema: r, selection: o } = t;
                    const { $from: i, $to: s } = o;
                    const l = i.blockRange(s);
                    if (!l) return !1;
                    const a = t.doc.slice(l.start, l.end);
                    if (
                        !r.nodes.detailsContent.contentMatch.matchFragment(
                            a.content,
                        )
                    ) {
                        return !1;
                    }
                    const d =
                        ((n = a.toJSON()) == null ? void 0 : n.content) || [];
                    return e()
                        .insertContentAt(
                            { from: l.start, to: l.end },
                            {
                                type: this.name,
                                content: [
                                    { type: "detailsSummary" },
                                    { type: "detailsContent", content: d },
                                ],
                            },
                        )
                        .setTextSelection(l.start + 2)
                        .run();
                },
            unsetDetails:
                () =>
                ({ state: t, chain: e }) => {
                    const { selection: n, schema: r } = t;
                    const o = Xe((y) => y.type === this.type)(n);
                    if (!o) return !1;
                    const i = Cn(
                        o.node,
                        (y) => y.type === r.nodes.detailsSummary,
                    );
                    const s = Cn(
                        o.node,
                        (y) => y.type === r.nodes.detailsContent,
                    );
                    if (!i.length || !s.length) return !1;
                    const l = i[0];
                    const a = s[0];
                    const c = o.pos;
                    const d = t.doc.resolve(c);
                    const u = c + o.node.nodeSize;
                    const f = { from: c, to: u };
                    const h = a.node.content.toJSON() || [];
                    const p = d.parent.type.contentMatch.defaultType;
                    const g = [p?.create(null, l.node.content).toJSON(), ...h];
                    return e()
                        .insertContentAt(f, g)
                        .setTextSelection(c + 1)
                        .run();
                },
        };
    },
    addKeyboardShortcuts() {
        return {
            Backspace: () => {
                const { schema: t, selection: e } = this.editor.state;
                const { empty: n, $anchor: r } = e;
                return !n || r.parent.type !== t.nodes.detailsSummary
                    ? !1
                    : r.parentOffset !== 0
                      ? this.editor.commands.command(({ tr: o }) => {
                            const i = r.pos - 1;
                            const s = r.pos;
                            return (o.delete(i, s), !0);
                        })
                      : this.editor.commands.unsetDetails();
            },
            Enter: ({ editor: t }) => {
                const { state: e, view: n } = t;
                const { schema: r, selection: o } = e;
                const { $head: i } = o;
                if (i.parent.type !== r.nodes.detailsSummary) return !1;
                const s = xo(i.after() + 1, t);
                const l = s ? e.doc.nodeAt(i.after()) : i.node(-2);
                if (!l) return !1;
                const a = s ? 0 : i.indexAfter(-1);
                const c = tr(l.contentMatchAt(a));
                if (!c || !l.canReplaceWith(a, a, c)) return !1;
                const d = c.createAndFill();
                if (!d) return !1;
                const u = s ? i.after() + 1 : i.after(-1);
                const f = e.tr.replaceWith(u, u, d);
                const h = f.doc.resolve(u);
                const p = I.near(h, 1);
                return (
                    f.setSelection(p),
                    f.scrollIntoView(),
                    n.dispatch(f),
                    !0
                );
            },
            ArrowRight: ({ editor: t }) => Ru(t, "right"),
            ArrowDown: ({ editor: t }) => Ru(t, "down"),
        };
    },
    addProseMirrorPlugins() {
        return [
            new L({
                key: new H("detailsSelection"),
                appendTransaction: (t, e, n) => {
                    const { editor: r, type: o } = this;
                    if (
                        r.view.composing ||
                        !t.some((y) => y.selectionSet) ||
                        !e.selection.empty ||
                        !n.selection.empty ||
                        !el(n, o.name)
                    ) {
                        return;
                    }
                    const { $from: a } = n.selection;
                    if (xo(a.pos, r)) return;
                    const d = V0(a, (y) => y.type === o, r);
                    if (!d) return;
                    const u = Cn(
                        d.node,
                        (y) => y.type === n.schema.nodes.detailsSummary,
                    );
                    if (!u.length) return;
                    const f = u[0];
                    const p =
                        (e.selection.from < n.selection.from
                            ? "forward"
                            : "backward") === "forward"
                            ? d.start + f.pos
                            : d.pos + f.pos + f.node.nodeSize;
                    const m = R.create(n.doc, p);
                    return n.tr.setSelection(m);
                },
            }),
        ];
    },
});
const Iu = $.create({
    name: "detailsContent",
    content: "block+",
    defining: !0,
    selectable: !1,
    addOptions() {
        return { HTMLAttributes: {} };
    },
    parseHTML() {
        return [{ tag: `div[data-type="${this.name}"]` }];
    },
    renderHTML({ HTMLAttributes: t }) {
        return [
            "div",
            O(this.options.HTMLAttributes, t, { "data-type": this.name }),
            0,
        ];
    },
    addNodeView() {
        return ({ HTMLAttributes: t }) => {
            const e = document.createElement("div");
            const n = O(this.options.HTMLAttributes, t, {
                "data-type": this.name,
                hidden: "hidden",
            });
            return (
                Object.entries(n).forEach(([r, o]) => e.setAttribute(r, o)),
                e.addEventListener("toggleDetailsContent", () => {
                    e.toggleAttribute("hidden");
                }),
                {
                    dom: e,
                    contentDOM: e,
                    ignoreMutation(r) {
                        return r.type === "selection"
                            ? !1
                            : !e.contains(r.target) || e === r.target;
                    },
                    update: (r) => r.type === this.type,
                }
            );
        };
    },
    addKeyboardShortcuts() {
        return {
            Enter: ({ editor: t }) => {
                const { state: e, view: n } = t;
                const { selection: r } = e;
                const { $from: o, empty: i } = r;
                const s = Xe((B) => B.type === this.type)(r);
                if (!i || !s || !s.node.childCount) return !1;
                const l = o.index(s.depth);
                const { childCount: a } = s.node;
                if (!(a === l + 1)) return !1;
                const d = s.node.type.contentMatch.defaultType;
                const u = d?.createAndFill();
                if (!u) return !1;
                const f = e.doc.resolve(s.pos + 1);
                const h = a - 1;
                const p = s.node.child(h);
                const m = f.posAtIndex(h, s.depth);
                if (!p.eq(u)) return !1;
                const y = o.node(-3);
                if (!y) return !1;
                const b = o.indexAfter(-3);
                const k = tr(y.contentMatchAt(b));
                if (!k || !y.canReplaceWith(b, b, k)) return !1;
                const v = k.createAndFill();
                if (!v) return !1;
                const { tr: x } = e;
                const S = o.after(-2);
                x.replaceWith(S, S, v);
                const w = x.doc.resolve(S);
                const N = I.near(w, 1);
                x.setSelection(N);
                const D = m;
                const M = m + p.nodeSize;
                return (x.delete(D, M), x.scrollIntoView(), n.dispatch(x), !0);
            },
        };
    },
    ...Xt({ nodeName: "detailsContent" }),
});
const Pu = $.create({
    name: "detailsSummary",
    content: "text*",
    defining: !0,
    selectable: !1,
    isolating: !0,
    addOptions() {
        return { HTMLAttributes: {} };
    },
    parseHTML() {
        return [{ tag: "summary" }];
    },
    renderHTML({ HTMLAttributes: t }) {
        return ["summary", O(this.options.HTMLAttributes, t), 0];
    },
    ...Xt({ nodeName: "detailsSummary", content: "inline" }),
});
const W0 = $.create({
    name: "doc",
    topNode: !0,
    content: "block+",
    renderMarkdown: (t, e) =>
        t.content
            ? e.renderChildren(
                  t.content,
                  `

`,
              )
            : "",
});
const Lu = W0;
const zu = $.create({
    name: "grid",
    group: "block",
    defining: !0,
    isolating: !0,
    allowGapCursor: !1,
    content: "gridColumn+",
    addOptions() {
        return { HTMLAttributes: { class: "grid-layout" } };
    },
    addAttributes() {
        return {
            "data-cols": {
                default: 2,
                parseHTML: (t) => t.getAttribute("data-cols"),
            },
            "data-from-breakpoint": {
                default: "md",
                parseHTML: (t) => t.getAttribute("data-from-breakpoint"),
            },
            style: {
                default: null,
                parseHTML: (t) => t.getAttribute("style"),
                renderHTML: (t) => ({
                    style: `grid-template-columns: repeat(${t["data-cols"]}, 1fr)`,
                }),
            },
        };
    },
    parseHTML() {
        return [
            {
                tag: "div",
                getAttrs: (t) => t.classList.contains("grid-layout") && null,
            },
        ];
    },
    renderHTML({ HTMLAttributes: t }) {
        return ["div", O(this.options.HTMLAttributes, t), 0];
    },
    addCommands() {
        return {
            insertGrid:
                ({
                    columns: t = [1, 1],
                    fromBreakpoint: e,
                    coordinates: n = null,
                } = {}) =>
                ({ tr: r, dispatch: o, editor: i }) => {
                    const s = i.schema.nodes.gridColumn;
                    const l = Array.isArray(t) && t.length ? t : [1, 1];
                    const a = [];
                    for (let u = 0; u < l.length; u += 1) {
                        a.push(
                            s.createAndFill({
                                "data-col-span": Number(l[u] ?? 1) || 1,
                            }),
                        );
                    }
                    const c = l
                        .map((u) => Number(u) || 1)
                        .reduce((u, f) => u + f, 0);
                    const d = i.schema.nodes.grid.createChecked(
                        { "data-cols": c, "data-from-breakpoint": e },
                        a,
                    );
                    if (o) {
                        const u = r.selection.anchor + 1;
                        [null, void 0].includes(n?.from)
                            ? r
                                  .replaceSelectionWith(d)
                                  .scrollIntoView()
                                  .setSelection(R.near(r.doc.resolve(u)))
                            : r
                                  .replaceRangeWith(n.from, n.to, d)
                                  .scrollIntoView()
                                  .setSelection(R.near(r.doc.resolve(n.from)));
                    }
                    return !0;
                },
        };
    },
});
const Bu = $.create({
    name: "gridColumn",
    content: "block+",
    isolating: !0,
    addOptions() {
        return { HTMLAttributes: { class: "grid-layout-col" } };
    },
    addAttributes() {
        return {
            "data-col-span": {
                default: 1,
                parseHTML: (t) => t.getAttribute("data-col-span"),
                renderHTML: (t) => ({
                    "data-col-span": t["data-col-span"] ?? 1,
                }),
            },
            style: {
                default: null,
                parseHTML: (t) => t.getAttribute("style"),
                renderHTML: (t) => ({
                    style: `grid-column: span ${t["data-col-span"] ?? 1};`,
                }),
            },
        };
    },
    parseHTML() {
        return [
            {
                tag: "div",
                getAttrs: (t) =>
                    t.classList.contains("grid-layout-col") && null,
            },
        ];
    },
    renderHTML({ HTMLAttributes: t }) {
        return ["div", O(this.options.HTMLAttributes, t), 0];
    },
});
const j0 = $.create({
    name: "hardBreak",
    markdownTokenName: "br",
    addOptions() {
        return { keepMarks: !0, HTMLAttributes: {} };
    },
    inline: !0,
    group: "inline",
    selectable: !1,
    linebreakReplacement: !0,
    parseHTML() {
        return [{ tag: "br" }];
    },
    renderHTML({ HTMLAttributes: t }) {
        return ["br", O(this.options.HTMLAttributes, t)];
    },
    renderText() {
        return `
`;
    },
    renderMarkdown: (t, e) =>
        e.indent(`
`),
    parseMarkdown: () => ({ type: "hardBreak" }),
    addCommands() {
        return {
            setHardBreak:
                () =>
                ({ commands: t, chain: e, state: n, editor: r }) =>
                    t.first([
                        () => t.exitCode(),
                        () =>
                            t.command(() => {
                                const { selection: o, storedMarks: i } = n;
                                if (o.$from.parent.type.spec.isolating) {
                                    return !1;
                                }
                                const { keepMarks: s } = this.options;
                                const { splittableMarks: l } =
                                    r.extensionManager;
                                const a =
                                    i ||
                                    (o.$to.parentOffset && o.$from.marks());
                                return e()
                                    .insertContent({ type: this.name })
                                    .command(({ tr: c, dispatch: d }) => {
                                        if (d && a && s) {
                                            const u = a.filter((f) =>
                                                l.includes(f.type.name),
                                            );
                                            c.ensureMarks(u);
                                        }
                                        return !0;
                                    })
                                    .run();
                            }),
                    ]),
        };
    },
    addKeyboardShortcuts() {
        return {
            "Mod-Enter": () => this.editor.commands.setHardBreak(),
            "Shift-Enter": () => this.editor.commands.setHardBreak(),
        };
    },
});
const Hu = j0;
const K0 = $.create({
    name: "heading",
    addOptions() {
        return { levels: [1, 2, 3, 4, 5, 6], HTMLAttributes: {} };
    },
    content: "inline*",
    group: "block",
    defining: !0,
    addAttributes() {
        return { level: { default: 1, rendered: !1 } };
    },
    parseHTML() {
        return this.options.levels.map((t) => ({
            tag: `h${t}`,
            attrs: { level: t },
        }));
    },
    renderHTML({ node: t, HTMLAttributes: e }) {
        return [
            `h${this.options.levels.includes(t.attrs.level) ? t.attrs.level : this.options.levels[0]}`,
            O(this.options.HTMLAttributes, e),
            0,
        ];
    },
    parseMarkdown: (t, e) =>
        e.createNode(
            "heading",
            { level: t.depth || 1 },
            e.parseInline(t.tokens || []),
        ),
    renderMarkdown: (t, e) => {
        let n;
        const r =
            (n = t.attrs) != null && n.level ? parseInt(t.attrs.level, 10) : 1;
        const o = "#".repeat(r);
        return t.content ? `${o} ${e.renderChildren(t.content)}` : "";
    },
    addCommands() {
        return {
            setHeading:
                (t) =>
                ({ commands: e }) =>
                    this.options.levels.includes(t.level)
                        ? e.setNode(this.name, t)
                        : !1,
            toggleHeading:
                (t) =>
                ({ commands: e }) =>
                    this.options.levels.includes(t.level)
                        ? e.toggleNode(this.name, "paragraph", t)
                        : !1,
        };
    },
    addKeyboardShortcuts() {
        return this.options.levels.reduce(
            (t, e) => ({
                ...t,
                [`Mod-Alt-${e}`]: () =>
                    this.editor.commands.toggleHeading({ level: e }),
            }),
            {},
        );
    },
    addInputRules() {
        return this.options.levels.map((t) =>
            rr({
                find: new RegExp(
                    `^(#{${Math.min(...this.options.levels)},${t}})\\s$`,
                ),
                type: this.type,
                getAttributes: { level: t },
            }),
        );
    },
});
const $u = K0;
const U0 = /(?:^|\s)(==(?!\s+==)((?:[^=]+))==(?!\s+==))$/;
const q0 = /(?:^|\s)(==(?!\s+==)((?:[^=]+))==(?!\s+==))/g;
const J0 = Z.create({
    name: "highlight",
    addOptions() {
        return { multicolor: !1, HTMLAttributes: {} };
    },
    addAttributes() {
        return this.options.multicolor
            ? {
                  color: {
                      default: null,
                      parseHTML: (t) =>
                          t.getAttribute("data-color") ||
                          t.style.backgroundColor,
                      renderHTML: (t) =>
                          t.color
                              ? {
                                    "data-color": t.color,
                                    style: `background-color: ${t.color}; color: inherit`,
                                }
                              : {},
                  },
              }
            : {};
    },
    parseHTML() {
        return [{ tag: "mark" }];
    },
    renderHTML({ HTMLAttributes: t }) {
        return ["mark", O(this.options.HTMLAttributes, t), 0];
    },
    renderMarkdown: (t, e) => `==${e.renderChildren(t)}==`,
    parseMarkdown: (t, e) =>
        e.applyMark("highlight", e.parseInline(t.tokens || [])),
    markdownTokenizer: {
        name: "highlight",
        level: "inline",
        start: (t) => t.indexOf("=="),
        tokenize(t, e, n) {
            const o = /^(==)([^=]+)(==)/.exec(t);
            if (o) {
                const i = o[2].trim();
                const s = n.inlineTokens(i);
                return { type: "highlight", raw: o[0], text: i, tokens: s };
            }
        },
    },
    addCommands() {
        return {
            setHighlight:
                (t) =>
                ({ commands: e }) =>
                    e.setMark(this.name, t),
            toggleHighlight:
                (t) =>
                ({ commands: e }) =>
                    e.toggleMark(this.name, t),
            unsetHighlight:
                () =>
                ({ commands: t }) =>
                    t.unsetMark(this.name),
        };
    },
    addKeyboardShortcuts() {
        return { "Mod-Shift-h": () => this.editor.commands.toggleHighlight() };
    },
    addInputRules() {
        return [Le({ find: U0, type: this.type })];
    },
    addPasteRules() {
        return [Ce({ find: q0, type: this.type })];
    },
});
const Fu = J0;
const G0 = $.create({
    name: "horizontalRule",
    addOptions() {
        return { HTMLAttributes: {}, nextNodeType: "paragraph" };
    },
    group: "block",
    parseHTML() {
        return [{ tag: "hr" }];
    },
    renderHTML({ HTMLAttributes: t }) {
        return ["hr", O(this.options.HTMLAttributes, t)];
    },
    markdownTokenName: "hr",
    parseMarkdown: (t, e) => e.createNode("horizontalRule"),
    renderMarkdown: () => "---",
    addCommands() {
        return {
            setHorizontalRule:
                () =>
                ({ chain: t, state: e }) => {
                    if (!fu(e, e.schema.nodes[this.name])) return !1;
                    const { selection: n } = e;
                    const { $to: r } = n;
                    const o = t();
                    return (
                        uo(n)
                            ? o.insertContentAt(r.pos, { type: this.name })
                            : o.insertContent({ type: this.name }),
                        o
                            .command(({ state: i, tr: s, dispatch: l }) => {
                                if (l) {
                                    const { $to: a } = s.selection;
                                    const c = a.end();
                                    if (a.nodeAfter) {
                                        a.nodeAfter.isTextblock
                                            ? s.setSelection(
                                                  R.create(s.doc, a.pos + 1),
                                              )
                                            : a.nodeAfter.isBlock
                                              ? s.setSelection(
                                                    P.create(s.doc, a.pos),
                                                )
                                              : s.setSelection(
                                                    R.create(s.doc, a.pos),
                                                );
                                    } else {
                                        const d =
                                            i.schema.nodes[
                                                this.options.nextNodeType
                                            ] ||
                                            a.parent.type.contentMatch
                                                .defaultType;
                                        const u = d?.create();
                                        u &&
                                            (s.insert(c, u),
                                            s.setSelection(
                                                R.create(s.doc, c + 1),
                                            ));
                                    }
                                    s.scrollIntoView();
                                }
                                return !0;
                            })
                            .run()
                    );
                },
        };
    },
    addInputRules() {
        return [po({ find: /^(?:---|—-|___\s|\*\*\*\s)$/, type: this.type })];
    },
});
const _u = G0;
const X0 = /(?:^|\s)(\*(?!\s+\*)((?:[^*]+))\*(?!\s+\*))$/;
const Y0 = /(?:^|\s)(\*(?!\s+\*)((?:[^*]+))\*(?!\s+\*))/g;
const Q0 = /(?:^|\s)(_(?!\s+_)((?:[^_]+))_(?!\s+_))$/;
const Z0 = /(?:^|\s)(_(?!\s+_)((?:[^_]+))_(?!\s+_))/g;
const eb = Z.create({
    name: "italic",
    addOptions() {
        return { HTMLAttributes: {} };
    },
    parseHTML() {
        return [
            { tag: "em" },
            {
                tag: "i",
                getAttrs: (t) => t.style.fontStyle !== "normal" && null,
            },
            {
                style: "font-style=normal",
                clearMark: (t) => t.type.name === this.name,
            },
            { style: "font-style=italic" },
        ];
    },
    renderHTML({ HTMLAttributes: t }) {
        return ["em", O(this.options.HTMLAttributes, t), 0];
    },
    addCommands() {
        return {
            setItalic:
                () =>
                ({ commands: t }) =>
                    t.setMark(this.name),
            toggleItalic:
                () =>
                ({ commands: t }) =>
                    t.toggleMark(this.name),
            unsetItalic:
                () =>
                ({ commands: t }) =>
                    t.unsetMark(this.name),
        };
    },
    markdownTokenName: "em",
    parseMarkdown: (t, e) =>
        e.applyMark("italic", e.parseInline(t.tokens || [])),
    renderMarkdown: (t, e) => `*${e.renderChildren(t)}*`,
    addKeyboardShortcuts() {
        return {
            "Mod-i": () => this.editor.commands.toggleItalic(),
            "Mod-I": () => this.editor.commands.toggleItalic(),
        };
    },
    addInputRules() {
        return [
            Le({ find: X0, type: this.type }),
            Le({ find: Q0, type: this.type }),
        ];
    },
    addPasteRules() {
        return [
            Ce({ find: Y0, type: this.type }),
            Ce({ find: Z0, type: this.type }),
        ];
    },
});
const Vu = eb;
const tb = /(?:^|\s)(!\[(.+|:?)]\((\S+)(?:(?:\s+)["'](\S+)["'])?\))$/;
const nb = $.create({
    name: "image",
    addOptions() {
        return { inline: !1, allowBase64: !1, HTMLAttributes: {}, resize: !1 };
    },
    inline() {
        return this.options.inline;
    },
    group() {
        return this.options.inline ? "inline" : "block";
    },
    draggable: !0,
    addAttributes() {
        return {
            src: { default: null },
            alt: { default: null },
            title: { default: null },
            width: { default: null },
            height: { default: null },
        };
    },
    parseHTML() {
        return [
            {
                tag: this.options.allowBase64
                    ? "img[src]"
                    : 'img[src]:not([src^="data:"])',
            },
        ];
    },
    renderHTML({ HTMLAttributes: t }) {
        return ["img", O(this.options.HTMLAttributes, t)];
    },
    parseMarkdown: (t, e) =>
        e.createNode("image", { src: t.href, title: t.title, alt: t.text }),
    renderMarkdown: (t) => {
        let e, n, r, o, i, s;
        const l = (n = (e = t.attrs) == null ? void 0 : e.src) != null ? n : "";
        const a = (o = (r = t.attrs) == null ? void 0 : r.alt) != null ? o : "";
        const c =
            (s = (i = t.attrs) == null ? void 0 : i.title) != null ? s : "";
        return c ? `![${a}](${l} "${c}")` : `![${a}](${l})`;
    },
    addNodeView() {
        if (
            !this.options.resize ||
            !this.options.resize.enabled ||
            typeof document > "u" ||
            !this.editor.isEditable
        ) {
            return null;
        }
        const {
            directions: t,
            minWidth: e,
            minHeight: n,
            alwaysPreserveAspectRatio: r,
        } = this.options.resize;
        return ({ node: o, getPos: i, HTMLAttributes: s }) => {
            const l = document.createElement("img");
            (Object.entries(s).forEach(([d, u]) => {
                if (u != null) {
                    switch (d) {
                        case "width":
                        case "height":
                            break;
                        default:
                            l.setAttribute(d, u);
                            break;
                    }
                }
            }),
                (l.src = s.src));
            const a = new uu({
                element: l,
                node: o,
                getPos: i,
                onResize: (d, u) => {
                    ((l.style.width = `${d}px`), (l.style.height = `${u}px`));
                },
                onCommit: (d, u) => {
                    const f = i();
                    f !== void 0 &&
                        this.editor
                            .chain()
                            .setNodeSelection(f)
                            .updateAttributes(this.name, {
                                width: d,
                                height: u,
                            })
                            .run();
                },
                onUpdate: (d, u, f) => d.type === o.type,
                options: {
                    directions: t,
                    min: { width: e, height: n },
                    preserveAspectRatio: r === !0,
                },
            });
            const c = a.dom;
            return (
                (c.style.visibility = "hidden"),
                (c.style.pointerEvents = "none"),
                (l.onload = () => {
                    ((c.style.visibility = ""), (c.style.pointerEvents = ""));
                }),
                a
            );
        };
    },
    addCommands() {
        return {
            setImage:
                (t) =>
                ({ commands: e }) =>
                    e.insertContent({ type: this.name, attrs: t }),
        };
    },
    addInputRules() {
        return [
            po({
                find: tb,
                type: this.type,
                getAttributes: (t) => {
                    const [, , e, n, r] = t;
                    return { src: n, alt: e, title: r };
                },
            }),
        ];
    },
});
const Wu = nb;
const ju = Wu.extend({
    addAttributes() {
        return {
            ...this.parent?.(),
            id: {
                default: null,
                parseHTML: (t) => t.getAttribute("data-id"),
                renderHTML: (t) => (t.id ? { "data-id": t.id } : {}),
            },
        };
    },
});
const Ku = $.create({
    name: "lead",
    group: "block",
    content: "block+",
    addOptions() {
        return { HTMLAttributes: { class: "lead" } };
    },
    parseHTML() {
        return [{ tag: "div", getAttrs: (t) => t.classList.contains("lead") }];
    },
    renderHTML({ HTMLAttributes: t }) {
        return ["div", O(this.options.HTMLAttributes, t), 0];
    },
    addCommands() {
        return {
            toggleLead:
                () =>
                ({ commands: t }) =>
                    t.toggleWrap(this.name),
        };
    },
});
const rb =
    "aaa1rp3bb0ott3vie4c1le2ogado5udhabi7c0ademy5centure6ountant0s9o1tor4d0s1ult4e0g1ro2tna4f0l1rica5g0akhan5ency5i0g1rbus3force5tel5kdn3l0ibaba4pay4lfinanz6state5y2sace3tom5m0azon4ericanexpress7family11x2fam3ica3sterdam8nalytics7droid5quan4z2o0l2partments8p0le4q0uarelle8r0ab1mco4chi3my2pa2t0e3s0da2ia2sociates9t0hleta5torney7u0ction5di0ble3o3spost5thor3o0s4w0s2x0a2z0ure5ba0by2idu3namex4d1k2r0celona5laycard4s5efoot5gains6seball5ketball8uhaus5yern5b0c1t1va3cg1n2d1e0ats2uty4er2rlin4st0buy5t2f1g1h0arti5i0ble3d1ke2ng0o3o1z2j1lack0friday9ockbuster8g1omberg7ue3m0s1w2n0pparibas9o0ats3ehringer8fa2m1nd2o0k0ing5sch2tik2on4t1utique6x2r0adesco6idgestone9oadway5ker3ther5ussels7s1t1uild0ers6siness6y1zz3v1w1y1z0h3ca0b1fe2l0l1vinklein9m0era3p2non3petown5ital0one8r0avan4ds2e0er0s4s2sa1e1h1ino4t0ering5holic7ba1n1re3c1d1enter4o1rn3f0a1d2g1h0anel2nel4rity4se2t2eap3intai5ristmas6ome4urch5i0priani6rcle4sco3tadel4i0c2y3k1l0aims4eaning6ick2nic1que6othing5ud3ub0med6m1n1o0ach3des3ffee4llege4ogne5m0mbank4unity6pany2re3uter5sec4ndos3struction8ulting7tact3ractors9oking4l1p2rsica5untry4pon0s4rses6pa2r0edit0card4union9icket5own3s1uise0s6u0isinella9v1w1x1y0mru3ou3z2dad1nce3ta1e1ing3sun4y2clk3ds2e0al0er2s3gree4livery5l1oitte5ta3mocrat6ntal2ist5si0gn4v2hl2iamonds6et2gital5rect0ory7scount3ver5h2y2j1k1m1np2o0cs1tor4g1mains5t1wnload7rive4tv2ubai3nlop4pont4rban5vag2r2z2earth3t2c0o2deka3u0cation8e1g1mail3erck5nergy4gineer0ing9terprises10pson4quipment8r0icsson6ni3s0q1tate5t1u0rovision8s2vents5xchange6pert3osed4ress5traspace10fage2il1rwinds6th3mily4n0s2rm0ers5shion4t3edex3edback6rrari3ero6i0delity5o2lm2nal1nce1ial7re0stone6mdale6sh0ing5t0ness6j1k1lickr3ghts4r2orist4wers5y2m1o0o0d1tball6rd1ex2sale4um3undation8x2r0ee1senius7l1ogans4ntier7tr2ujitsu5n0d2rniture7tbol5yi3ga0l0lery3o1up4me0s3p1rden4y2b0iz3d0n2e0a1nt0ing5orge5f1g0ee3h1i0ft0s3ves2ing5l0ass3e1obal2o4m0ail3bh2o1x2n1odaddy5ld0point6f2o0dyear5g0le4p1t1v2p1q1r0ainger5phics5tis4een3ipe3ocery4up4s1t1u0cci3ge2ide2tars5ru3w1y2hair2mburg5ngout5us3bo2dfc0bank7ealth0care8lp1sinki6re1mes5iphop4samitsu7tachi5v2k0t2m1n1ockey4ldings5iday5medepot5goods5s0ense7nda3rse3spital5t0ing5t0els3mail5use3w2r1sbc3t1u0ghes5yatt3undai7ibm2cbc2e1u2d1e0ee3fm2kano4l1m0amat4db2mo0bilien9n0c1dustries8finiti5o2g1k1stitute6urance4e4t0ernational10uit4vestments10o1piranga7q1r0ish4s0maili5t0anbul7t0au2v3jaguar4va3cb2e0ep2tzt3welry6io2ll2m0p2nj2o0bs1urg4t1y2p0morgan6rs3uegos4niper7kaufen5ddi3e0rryhotels6properties14fh2g1h1i0a1ds2m1ndle4tchen5wi3m1n1oeln3matsu5sher5p0mg2n2r0d1ed3uokgroup8w1y0oto4z2la0caixa5mborghini8er3nd0rover6xess5salle5t0ino3robe5w0yer5b1c1ds2ease3clerc5frak4gal2o2xus4gbt3i0dl2fe0insurance9style7ghting6ke2lly3mited4o2ncoln4k2ve1ing5k1lc1p2oan0s3cker3us3l1ndon4tte1o3ve3pl0financial11r1s1t0d0a3u0ndbeck6xe1ury5v1y2ma0drid4if1son4keup4n0agement7go3p1rket0ing3s4riott5shalls7ttel5ba2c0kinsey7d1e0d0ia3et2lbourne7me1orial6n0u2rckmsd7g1h1iami3crosoft7l1ni1t2t0subishi9k1l0b1s2m0a2n1o0bi0le4da2e1i1m1nash3ey2ster5rmon3tgage6scow4to0rcycles9v0ie4p1q1r1s0d2t0n1r2u0seum3ic4v1w1x1y1z2na0b1goya4me2vy3ba2c1e0c1t0bank4flix4work5ustar5w0s2xt0direct7us4f0l2g0o2hk2i0co2ke1on3nja3ssan1y5l1o0kia3rton4w0ruz3tv4p1r0a1w2tt2u1yc2z2obi1server7ffice5kinawa6layan0group9lo3m0ega4ne1g1l0ine5oo2pen3racle3nge4g0anic5igins6saka4tsuka4t2vh3pa0ge2nasonic7ris2s1tners4s1y3y2ccw3e0t2f0izer5g1h0armacy6d1ilips5one2to0graphy6s4ysio5ics1tet2ures6d1n0g1k2oneer5zza4k1l0ace2y0station9umbing5s3m1n0c2ohl2ker3litie5rn2st3r0axi3ess3ime3o0d0uctions8f1gressive8mo2perties3y5tection8u0dential9s1t1ub2w0c2y2qa1pon3uebec3st5racing4dio4e0ad1lestate6tor2y4cipes5d0stone5umbrella9hab3ise0n3t2liance6n0t0als5pair3ort3ublican8st0aurant8view0s5xroth6ich0ardli6oh3l1o1p2o0cks3deo3gers4om3s0vp3u0gby3hr2n2w0e2yukyu6sa0arland6fe0ty4kura4le1on3msclub4ung5ndvik0coromant12ofi4p1rl2s1ve2xo3b0i1s2c0b1haeffler7midt4olarships8ol3ule3warz5ience5ot3d1e0arch3t2cure1ity6ek2lect4ner3rvices6ven3w1x0y3fr2g1h0angrila6rp3ell3ia1ksha5oes2p0ping5uji3w3i0lk2na1gles5te3j1k0i0n2y0pe4l0ing4m0art3ile4n0cf3o0ccer3ial4ftbank4ware6hu2lar2utions7ng1y2y2pa0ce3ort2t3r0l2s1t0ada2ples4r1tebank4farm7c0group6ockholm6rage3e3ream4udio2y3yle4u0cks3pplies3y2ort5rf1gery5zuki5v1watch4iss4x1y0dney4stems6z2tab1ipei4lk2obao4rget4tamotors6r2too4x0i3c0i2d0k2eam2ch0nology8l1masek5nnis4va3f1g1h0d1eater2re6iaa2ckets5enda4ps2res2ol4j0maxx4x2k0maxx5l1m0all4n1o0day3kyo3ols3p1ray3shiba5tal3urs3wn2yota3s3r0ade1ing4ining5vel0ers0insurance16ust3v2t1ube2i1nes3shu4v0s2w1z2ua1bank3s2g1k1nicom3versity8o2ol2ps2s1y1z2va0cations7na1guard7c1e0gas3ntures6risign5m\xF6gensberater2ung14sicherung10t2g1i0ajes4deo3g1king4llas4n1p1rgin4sa1ion4va1o3laanderen9n1odka3lvo3te1ing3o2yage5u2wales2mart4ter4ng0gou5tch0es6eather0channel12bcam3er2site5d0ding5ibo2r3f1hoswho6ien2ki2lliamhill9n0dows4e1ners6me2olterskluwer11odside6rk0s2ld3w2s1tc1f3xbox3erox4ihuan4n2xx2yz3yachts4hoo3maxun5ndex5e1odobashi7ga2kohama6u0tube6t1un3za0ppos4ra3ero3ip2m1one3uerich6w2";
const ob =
    "\u03B5\u03BB1\u03C52\u0431\u04331\u0435\u043B3\u0434\u0435\u0442\u04384\u0435\u044E2\u043A\u0430\u0442\u043E\u043B\u0438\u043A6\u043E\u043C3\u043C\u043A\u04342\u043E\u043D1\u0441\u043A\u0432\u04306\u043E\u043D\u043B\u0430\u0439\u043D5\u0440\u04333\u0440\u0443\u04412\u04442\u0441\u0430\u0439\u04423\u0440\u04313\u0443\u043A\u04403\u049B\u0430\u04373\u0570\u0561\u05753\u05D9\u05E9\u05E8\u05D0\u05DC5\u05E7\u05D5\u05DD3\u0627\u0628\u0648\u0638\u0628\u064A5\u0631\u0627\u0645\u0643\u06485\u0644\u0627\u0631\u062F\u06464\u0628\u062D\u0631\u064A\u06465\u062C\u0632\u0627\u0626\u06315\u0633\u0639\u0648\u062F\u064A\u06296\u0639\u0644\u064A\u0627\u06465\u0645\u063A\u0631\u06285\u0645\u0627\u0631\u0627\u062A5\u06CC\u0631\u0627\u06465\u0628\u0627\u0631\u062A2\u0632\u0627\u06314\u064A\u062A\u06433\u06BE\u0627\u0631\u062A5\u062A\u0648\u0646\u06334\u0633\u0648\u062F\u0627\u06463\u0631\u064A\u06295\u0634\u0628\u0643\u06294\u0639\u0631\u0627\u06422\u06282\u0645\u0627\u06464\u0641\u0644\u0633\u0637\u064A\u06466\u0642\u0637\u06313\u0643\u0627\u062B\u0648\u0644\u064A\u06436\u0648\u06453\u0645\u0635\u06312\u0644\u064A\u0633\u064A\u06275\u0648\u0631\u064A\u062A\u0627\u0646\u064A\u06277\u0642\u06394\u0647\u0645\u0631\u0627\u06475\u067E\u0627\u06A9\u0633\u062A\u0627\u06467\u0680\u0627\u0631\u062A4\u0915\u0949\u092E3\u0928\u0947\u091F3\u092D\u093E\u0930\u09240\u092E\u094D3\u094B\u09245\u0938\u0902\u0917\u0920\u09285\u09AC\u09BE\u0982\u09B2\u09BE5\u09AD\u09BE\u09B0\u09A42\u09F0\u09A44\u0A2D\u0A3E\u0A30\u0A244\u0AAD\u0ABE\u0AB0\u0AA44\u0B2D\u0B3E\u0B30\u0B244\u0B87\u0BA8\u0BCD\u0BA4\u0BBF\u0BAF\u0BBE6\u0BB2\u0B99\u0BCD\u0B95\u0BC86\u0B9A\u0BBF\u0B99\u0BCD\u0B95\u0BAA\u0BCD\u0BAA\u0BC2\u0BB0\u0BCD11\u0C2D\u0C3E\u0C30\u0C24\u0C4D5\u0CAD\u0CBE\u0CB0\u0CA44\u0D2D\u0D3E\u0D30\u0D24\u0D025\u0DBD\u0D82\u0D9A\u0DCF4\u0E04\u0E2D\u0E213\u0E44\u0E17\u0E223\u0EA5\u0EB2\u0EA73\u10D2\u10D42\u307F\u3093\u306A3\u30A2\u30DE\u30BE\u30F34\u30AF\u30E9\u30A6\u30C94\u30B0\u30FC\u30B0\u30EB4\u30B3\u30E02\u30B9\u30C8\u30A23\u30BB\u30FC\u30EB3\u30D5\u30A1\u30C3\u30B7\u30E7\u30F36\u30DD\u30A4\u30F3\u30C84\u4E16\u754C2\u4E2D\u4FE11\u56FD1\u570B1\u6587\u7F513\u4E9A\u9A6C\u900A3\u4F01\u4E1A2\u4F5B\u5C712\u4FE1\u606F2\u5065\u5EB72\u516B\u53662\u516C\u53F81\u76CA2\u53F0\u6E7E1\u70632\u5546\u57CE1\u5E971\u68072\u5609\u91CC0\u5927\u9152\u5E975\u5728\u7EBF2\u5927\u62FF2\u5929\u4E3B\u65593\u5A31\u4E502\u5BB6\u96FB2\u5E7F\u4E1C2\u5FAE\u535A2\u6148\u55842\u6211\u7231\u4F603\u624B\u673A2\u62DB\u80582\u653F\u52A11\u5E9C2\u65B0\u52A0\u57612\u95FB2\u65F6\u5C1A2\u66F8\u7C4D2\u673A\u67842\u6DE1\u9A6C\u95213\u6E38\u620F2\u6FB3\u95802\u70B9\u770B2\u79FB\u52A82\u7EC4\u7EC7\u673A\u67844\u7F51\u57401\u5E971\u7AD91\u7EDC2\u8054\u901A2\u8C37\u6B4C2\u8D2D\u72692\u901A\u8CA92\u96C6\u56E22\u96FB\u8A0A\u76C8\u79D14\u98DE\u5229\u6D663\u98DF\u54C12\u9910\u53852\u9999\u683C\u91CC\u62C93\u6E2F2\uB2F7\uB1371\uCEF42\uC0BC\uC1312\uD55C\uAD6D2";
const wl = "numeric";
const xl = "ascii";
const kl = "alpha";
const lr = "asciinumeric";
const sr = "alphanumeric";
const Sl = "domain";
const Qu = "emoji";
const ib = "scheme";
const sb = "slashscheme";
const hl = "whitespace";
function lb(t, e) {
    return (t in e || (e[t] = []), e[t]);
}
function Zt(t, e, n) {
    (e[wl] && ((e[lr] = !0), (e[sr] = !0)),
        e[xl] && ((e[lr] = !0), (e[kl] = !0)),
        e[lr] && (e[sr] = !0),
        e[kl] && (e[sr] = !0),
        e[sr] && (e[Sl] = !0),
        e[Qu] && (e[Sl] = !0));
    for (const r in e) {
        const o = lb(r, n);
        o.indexOf(t) < 0 && o.push(t);
    }
}
function ab(t, e) {
    const n = {};
    for (const r in e) e[r].indexOf(t) >= 0 && (n[r] = !0);
    return n;
}
function ve(t = null) {
    ((this.j = {}), (this.jr = []), (this.jd = null), (this.t = t));
}
ve.groups = {};
ve.prototype = {
    accepts() {
        return !!this.t;
    },
    go(t) {
        const e = this;
        const n = e.j[t];
        if (n) return n;
        for (let r = 0; r < e.jr.length; r++) {
            const o = e.jr[r][0];
            const i = e.jr[r][1];
            if (i && o.test(t)) return i;
        }
        return e.jd;
    },
    has(t, e = !1) {
        return e ? t in this.j : !!this.go(t);
    },
    ta(t, e, n, r) {
        for (let o = 0; o < t.length; o++) this.tt(t[o], e, n, r);
    },
    tr(t, e, n, r) {
        r = r || ve.groups;
        let o;
        return (
            e && e.j ? (o = e) : ((o = new ve(e)), n && r && Zt(e, n, r)),
            this.jr.push([t, o]),
            o
        );
    },
    ts(t, e, n, r) {
        let o = this;
        const i = t.length;
        if (!i) return o;
        for (let s = 0; s < i - 1; s++) o = o.tt(t[s]);
        return o.tt(t[i - 1], e, n, r);
    },
    tt(t, e, n, r) {
        r = r || ve.groups;
        const o = this;
        if (e && e.j) return ((o.j[t] = e), e);
        const i = e;
        let s;
        const l = o.go(t);
        if (
            (l
                ? ((s = new ve()),
                  Object.assign(s.j, l.j),
                  s.jr.push.apply(s.jr, l.jr),
                  (s.jd = l.jd),
                  (s.t = l.t))
                : (s = new ve()),
            i)
        ) {
            if (r) {
                if (s.t && typeof s.t === "string") {
                    const a = Object.assign(ab(s.t, r), n);
                    Zt(i, a, r);
                } else n && Zt(i, n, r);
            }
            s.t = i;
        }
        return ((o.j[t] = s), s);
    },
};
const V = (t, e, n, r, o) => t.ta(e, n, r, o);
const te = (t, e, n, r, o) => t.tr(e, n, r, o);
const Uu = (t, e, n, r, o) => t.ts(e, n, r, o);
const A = (t, e, n, r, o) => t.tt(e, n, r, o);
const bt = "WORD";
const Cl = "UWORD";
const Zu = "ASCIINUMERICAL";
const ef = "ALPHANUMERICAL";
const hr = "LOCALHOST";
const vl = "TLD";
const Ml = "UTLD";
const vo = "SCHEME";
const Mn = "SLASH_SCHEME";
const Al = "NUM";
const Tl = "WS";
const El = "NL";
const ar = "OPENBRACE";
const cr = "CLOSEBRACE";
const Mo = "OPENBRACKET";
const To = "CLOSEBRACKET";
const Ao = "OPENPAREN";
const Eo = "CLOSEPAREN";
const No = "OPENANGLEBRACKET";
const Oo = "CLOSEANGLEBRACKET";
const Ro = "FULLWIDTHLEFTPAREN";
const Do = "FULLWIDTHRIGHTPAREN";
const Io = "LEFTCORNERBRACKET";
const Po = "RIGHTCORNERBRACKET";
const Lo = "LEFTWHITECORNERBRACKET";
const zo = "RIGHTWHITECORNERBRACKET";
const Bo = "FULLWIDTHLESSTHAN";
const Ho = "FULLWIDTHGREATERTHAN";
const $o = "AMPERSAND";
const Fo = "APOSTROPHE";
const _o = "ASTERISK";
const Rt = "AT";
const Vo = "BACKSLASH";
const Wo = "BACKTICK";
const jo = "CARET";
const Dt = "COLON";
const Nl = "COMMA";
const Ko = "DOLLAR";
const et = "DOT";
const Uo = "EQUALS";
const Ol = "EXCLAMATION";
const Be = "HYPHEN";
const dr = "PERCENT";
const qo = "PIPE";
const Jo = "PLUS";
const Go = "POUND";
const ur = "QUERY";
const Rl = "QUOTE";
const tf = "FULLWIDTHMIDDLEDOT";
const Dl = "SEMI";
const tt = "SLASH";
const fr = "TILDE";
const Xo = "UNDERSCORE";
const nf = "EMOJI";
const Yo = "SYM";
const rf = Object.freeze({
    __proto__: null,
    ALPHANUMERICAL: ef,
    AMPERSAND: $o,
    APOSTROPHE: Fo,
    ASCIINUMERICAL: Zu,
    ASTERISK: _o,
    AT: Rt,
    BACKSLASH: Vo,
    BACKTICK: Wo,
    CARET: jo,
    CLOSEANGLEBRACKET: Oo,
    CLOSEBRACE: cr,
    CLOSEBRACKET: To,
    CLOSEPAREN: Eo,
    COLON: Dt,
    COMMA: Nl,
    DOLLAR: Ko,
    DOT: et,
    EMOJI: nf,
    EQUALS: Uo,
    EXCLAMATION: Ol,
    FULLWIDTHGREATERTHAN: Ho,
    FULLWIDTHLEFTPAREN: Ro,
    FULLWIDTHLESSTHAN: Bo,
    FULLWIDTHMIDDLEDOT: tf,
    FULLWIDTHRIGHTPAREN: Do,
    HYPHEN: Be,
    LEFTCORNERBRACKET: Io,
    LEFTWHITECORNERBRACKET: Lo,
    LOCALHOST: hr,
    NL: El,
    NUM: Al,
    OPENANGLEBRACKET: No,
    OPENBRACE: ar,
    OPENBRACKET: Mo,
    OPENPAREN: Ao,
    PERCENT: dr,
    PIPE: qo,
    PLUS: Jo,
    POUND: Go,
    QUERY: ur,
    QUOTE: Rl,
    RIGHTCORNERBRACKET: Po,
    RIGHTWHITECORNERBRACKET: zo,
    SCHEME: vo,
    SEMI: Dl,
    SLASH: tt,
    SLASH_SCHEME: Mn,
    SYM: Yo,
    TILDE: fr,
    TLD: vl,
    UNDERSCORE: Xo,
    UTLD: Ml,
    UWORD: Cl,
    WORD: bt,
    WS: Tl,
});
const gt = /[a-z]/;
const ir = /\p{L}/u;
const pl = /\p{Emoji}/u;
const yt = /\d/;
const ml = /\s/;
const qu = "\r";
const gl = `
`;
const cb = "\uFE0F";
const db = "\u200D";
const yl = "\uFFFC";
let ko = null;
let So = null;
function ub(t = []) {
    const e = {};
    ve.groups = e;
    const n = new ve();
    (ko == null && (ko = Ju(rb)),
        So == null && (So = Ju(ob)),
        A(n, "'", Fo),
        A(n, "{", ar),
        A(n, "}", cr),
        A(n, "[", Mo),
        A(n, "]", To),
        A(n, "(", Ao),
        A(n, ")", Eo),
        A(n, "<", No),
        A(n, ">", Oo),
        A(n, "\uFF08", Ro),
        A(n, "\uFF09", Do),
        A(n, "\u300C", Io),
        A(n, "\u300D", Po),
        A(n, "\u300E", Lo),
        A(n, "\u300F", zo),
        A(n, "\uFF1C", Bo),
        A(n, "\uFF1E", Ho),
        A(n, "&", $o),
        A(n, "*", _o),
        A(n, "@", Rt),
        A(n, "`", Wo),
        A(n, "^", jo),
        A(n, ":", Dt),
        A(n, ",", Nl),
        A(n, "$", Ko),
        A(n, ".", et),
        A(n, "=", Uo),
        A(n, "!", Ol),
        A(n, "-", Be),
        A(n, "%", dr),
        A(n, "|", qo),
        A(n, "+", Jo),
        A(n, "#", Go),
        A(n, "?", ur),
        A(n, '"', Rl),
        A(n, "/", tt),
        A(n, ";", Dl),
        A(n, "~", fr),
        A(n, "_", Xo),
        A(n, "\\", Vo),
        A(n, "\u30FB", tf));
    const r = te(n, yt, Al, { [wl]: !0 });
    te(r, yt, r);
    const o = te(r, gt, Zu, { [lr]: !0 });
    const i = te(r, ir, ef, { [sr]: !0 });
    const s = te(n, gt, bt, { [xl]: !0 });
    (te(s, yt, o), te(s, gt, s), te(o, yt, o), te(o, gt, o));
    const l = te(n, ir, Cl, { [kl]: !0 });
    (te(l, gt),
        te(l, yt, i),
        te(l, ir, l),
        te(i, yt, i),
        te(i, gt),
        te(i, ir, i));
    const a = A(n, gl, El, { [hl]: !0 });
    const c = A(n, qu, Tl, { [hl]: !0 });
    const d = te(n, ml, Tl, { [hl]: !0 });
    (A(n, yl, d),
        A(c, gl, a),
        A(c, yl, d),
        te(c, ml, d),
        A(d, qu),
        A(d, gl),
        te(d, ml, d),
        A(d, yl, d));
    const u = te(n, pl, nf, { [Qu]: !0 });
    (A(u, "#"), te(u, pl, u), A(u, cb, u));
    const f = A(u, db);
    (A(f, "#"), te(f, pl, u));
    const h = [
        [gt, s],
        [yt, o],
    ];
    const p = [
        [gt, null],
        [ir, l],
        [yt, i],
    ];
    for (let m = 0; m < ko.length; m++) Ot(n, ko[m], vl, bt, h);
    for (let m = 0; m < So.length; m++) Ot(n, So[m], Ml, Cl, p);
    (Zt(vl, { tld: !0, ascii: !0 }, e),
        Zt(Ml, { utld: !0, alpha: !0 }, e),
        Ot(n, "file", vo, bt, h),
        Ot(n, "mailto", vo, bt, h),
        Ot(n, "http", Mn, bt, h),
        Ot(n, "https", Mn, bt, h),
        Ot(n, "ftp", Mn, bt, h),
        Ot(n, "ftps", Mn, bt, h),
        Zt(vo, { scheme: !0, ascii: !0 }, e),
        Zt(Mn, { slashscheme: !0, ascii: !0 }, e),
        (t = t.sort((m, g) => (m[0] > g[0] ? 1 : -1))));
    for (let m = 0; m < t.length; m++) {
        const g = t[m][0];
        const b = t[m][1] ? { [ib]: !0 } : { [sb]: !0 };
        (g.indexOf("-") >= 0
            ? (b[Sl] = !0)
            : gt.test(g)
              ? yt.test(g)
                  ? (b[lr] = !0)
                  : (b[xl] = !0)
              : (b[wl] = !0),
            Uu(n, g, g, b));
    }
    return (
        Uu(n, "localhost", hr, { ascii: !0 }),
        (n.jd = new ve(Yo)),
        { start: n, tokens: Object.assign({ groups: e }, rf) }
    );
}
function of(t, e) {
    const n = fb(e.replace(/[A-Z]/g, (l) => l.toLowerCase()));
    const r = n.length;
    const o = [];
    let i = 0;
    let s = 0;
    for (; s < r; ) {
        let l = t;
        let a = null;
        let c = 0;
        let d = null;
        let u = -1;
        let f = -1;
        for (; s < r && (a = l.go(n[s])); ) {
            ((l = a),
                l.accepts()
                    ? ((u = 0), (f = 0), (d = l))
                    : u >= 0 && ((u += n[s].length), f++),
                (c += n[s].length),
                (i += n[s].length),
                s++);
        }
        ((i -= u),
            (s -= f),
            (c -= u),
            o.push({ t: d.t, v: e.slice(i - c, i), s: i - c, e: i }));
    }
    return o;
}
function fb(t) {
    const e = [];
    const n = t.length;
    let r = 0;
    for (; r < n; ) {
        const o = t.charCodeAt(r);
        let i;
        const s =
            o < 55296 ||
            o > 56319 ||
            r + 1 === n ||
            (i = t.charCodeAt(r + 1)) < 56320 ||
            i > 57343
                ? t[r]
                : t.slice(r, r + 2);
        (e.push(s), (r += s.length));
    }
    return e;
}
function Ot(t, e, n, r, o) {
    let i;
    const s = e.length;
    for (let l = 0; l < s - 1; l++) {
        const a = e[l];
        (t.j[a]
            ? (i = t.j[a])
            : ((i = new ve(r)), (i.jr = o.slice()), (t.j[a] = i)),
            (t = i));
    }
    return ((i = new ve(n)), (i.jr = o.slice()), (t.j[e[s - 1]] = i), i);
}
function Ju(t) {
    const e = [];
    const n = [];
    let r = 0;
    const o = "0123456789";
    for (; r < t.length; ) {
        let i = 0;
        for (; o.indexOf(t[r + i]) >= 0; ) i++;
        if (i > 0) {
            e.push(n.join(""));
            for (let s = parseInt(t.substring(r, r + i), 10); s > 0; s--) {
                n.pop();
            }
            r += i;
        } else (n.push(t[r]), r++);
    }
    return e;
}
const pr = {
    defaultProtocol: "http",
    events: null,
    format: Gu,
    formatHref: Gu,
    nl2br: !1,
    tagName: "a",
    target: null,
    rel: null,
    validate: !0,
    truncate: 1 / 0,
    className: null,
    attributes: null,
    ignoreTags: [],
    render: null,
};
function Il(t, e = null) {
    let n = Object.assign({}, pr);
    t && (n = Object.assign(n, t instanceof Il ? t.o : t));
    const r = n.ignoreTags;
    const o = [];
    for (let i = 0; i < r.length; i++) o.push(r[i].toUpperCase());
    ((this.o = n), e && (this.defaultRender = e), (this.ignoreTags = o));
}
Il.prototype = {
    o: pr,
    ignoreTags: [],
    defaultRender(t) {
        return t;
    },
    check(t) {
        return this.get("validate", t.toString(), t);
    },
    get(t, e, n) {
        const r = e != null;
        let o = this.o[t];
        return (
            o &&
            (typeof o === "object"
                ? ((o = n.t in o ? o[n.t] : pr[t]),
                  typeof o === "function" && r && (o = o(e, n)))
                : typeof o === "function" && r && (o = o(e, n.t, n)),
            o)
        );
    },
    getObj(t, e, n) {
        let r = this.o[t];
        return (typeof r === "function" && e != null && (r = r(e, n.t, n)), r);
    },
    render(t) {
        const e = t.render(this);
        return (this.get("render", null, t) || this.defaultRender)(e, t.t, t);
    },
};
function Gu(t) {
    return t;
}
function sf(t, e) {
    ((this.t = "token"), (this.v = t), (this.tk = e));
}
sf.prototype = {
    isLink: !1,
    toString() {
        return this.v;
    },
    toHref(t) {
        return this.toString();
    },
    toFormattedString(t) {
        const e = this.toString();
        const n = t.get("truncate", e, this);
        const r = t.get("format", e, this);
        return n && r.length > n ? r.substring(0, n) + "\u2026" : r;
    },
    toFormattedHref(t) {
        return t.get("formatHref", this.toHref(t.get("defaultProtocol")), this);
    },
    startIndex() {
        return this.tk[0].s;
    },
    endIndex() {
        return this.tk[this.tk.length - 1].e;
    },
    toObject(t = pr.defaultProtocol) {
        return {
            type: this.t,
            value: this.toString(),
            isLink: this.isLink,
            href: this.toHref(t),
            start: this.startIndex(),
            end: this.endIndex(),
        };
    },
    toFormattedObject(t) {
        return {
            type: this.t,
            value: this.toFormattedString(t),
            isLink: this.isLink,
            href: this.toFormattedHref(t),
            start: this.startIndex(),
            end: this.endIndex(),
        };
    },
    validate(t) {
        return t.get("validate", this.toString(), this);
    },
    render(t) {
        const e = this;
        const n = this.toHref(t.get("defaultProtocol"));
        const r = t.get("formatHref", n, this);
        const o = t.get("tagName", n, e);
        const i = this.toFormattedString(t);
        const s = {};
        const l = t.get("className", n, e);
        const a = t.get("target", n, e);
        const c = t.get("rel", n, e);
        const d = t.getObj("attributes", n, e);
        const u = t.getObj("events", n, e);
        return (
            (s.href = r),
            l && (s.class = l),
            a && (s.target = a),
            c && (s.rel = c),
            d && Object.assign(s, d),
            { tagName: o, attributes: s, content: i, eventListeners: u }
        );
    },
};
function Qo(t, e) {
    class n extends sf {
        constructor(o, i) {
            (super(o, i), (this.t = t));
        }
    }
    for (const r in e) n.prototype[r] = e[r];
    return ((n.t = t), n);
}
const Xu = Qo("email", {
    isLink: !0,
    toHref() {
        return "mailto:" + this.toString();
    },
});
const Yu = Qo("text");
const hb = Qo("nl");
const Co = Qo("url", {
    isLink: !0,
    toHref(t = pr.defaultProtocol) {
        return this.hasProtocol() ? this.v : `${t}://${this.v}`;
    },
    hasProtocol() {
        const t = this.tk;
        return t.length >= 2 && t[0].t !== hr && t[1].t === Dt;
    },
});
const ze = (t) => new ve(t);
function pb({ groups: t }) {
    const e = t.domain.concat([
        $o,
        _o,
        Rt,
        Vo,
        Wo,
        jo,
        Ko,
        Uo,
        Be,
        Al,
        dr,
        qo,
        Jo,
        Go,
        tt,
        Yo,
        fr,
        Xo,
    ]);
    const n = [
        Fo,
        Dt,
        Nl,
        et,
        Ol,
        dr,
        ur,
        Rl,
        Dl,
        No,
        Oo,
        ar,
        cr,
        To,
        Mo,
        Ao,
        Eo,
        Ro,
        Do,
        Io,
        Po,
        Lo,
        zo,
        Bo,
        Ho,
    ];
    const r = [
        $o,
        Fo,
        _o,
        Vo,
        Wo,
        jo,
        Ko,
        Uo,
        Be,
        ar,
        cr,
        dr,
        qo,
        Jo,
        Go,
        ur,
        tt,
        Yo,
        fr,
        Xo,
    ];
    const o = ze();
    const i = A(o, fr);
    (V(i, r, i), V(i, t.domain, i));
    const s = ze();
    const l = ze();
    const a = ze();
    (V(o, t.domain, s),
        V(o, t.scheme, l),
        V(o, t.slashscheme, a),
        V(s, r, i),
        V(s, t.domain, s));
    const c = A(s, Rt);
    (A(i, Rt, c), A(l, Rt, c), A(a, Rt, c));
    const d = A(i, et);
    (V(d, r, i), V(d, t.domain, i));
    const u = ze();
    (V(c, t.domain, u), V(u, t.domain, u));
    const f = A(u, et);
    V(f, t.domain, u);
    const h = ze(Xu);
    (V(f, t.tld, h), V(f, t.utld, h), A(c, hr, h));
    const p = A(u, Be);
    (A(p, Be, p),
        V(p, t.domain, u),
        V(h, t.domain, u),
        A(h, et, f),
        A(h, Be, p));
    const m = A(h, Dt);
    V(m, t.numeric, Xu);
    const g = A(s, Be);
    const y = A(s, et);
    (A(g, Be, g), V(g, t.domain, s), V(y, r, i), V(y, t.domain, s));
    const b = ze(Co);
    (V(y, t.tld, b),
        V(y, t.utld, b),
        V(b, t.domain, s),
        V(b, r, i),
        A(b, et, y),
        A(b, Be, g),
        A(b, Rt, c));
    const k = A(b, Dt);
    const v = ze(Co);
    V(k, t.numeric, v);
    const x = ze(Co);
    const S = ze();
    (V(x, e, x), V(x, n, S), V(S, e, x), V(S, n, S), A(b, tt, x), A(v, tt, x));
    const w = A(l, Dt);
    const N = A(a, Dt);
    const D = A(N, tt);
    const M = A(D, tt);
    (V(l, t.domain, s),
        A(l, et, y),
        A(l, Be, g),
        V(a, t.domain, s),
        A(a, et, y),
        A(a, Be, g),
        V(w, t.domain, x),
        A(w, tt, x),
        A(w, ur, x),
        V(M, t.domain, x),
        V(M, e, x),
        A(M, tt, x));
    const B = [
        [ar, cr],
        [Mo, To],
        [Ao, Eo],
        [No, Oo],
        [Ro, Do],
        [Io, Po],
        [Lo, zo],
        [Bo, Ho],
    ];
    for (let F = 0; F < B.length; F++) {
        const [K, _] = B[F];
        const T = A(x, K);
        (A(S, K, T), A(T, _, x));
        const W = ze(Co);
        V(T, e, W);
        const U = ze();
        (V(T, n),
            V(W, e, W),
            V(W, n, U),
            V(U, e, W),
            V(U, n, U),
            A(W, _, x),
            A(U, _, x));
    }
    return (A(o, hr, b), A(o, El, hb), { start: o, tokens: rf });
}
function mb(t, e, n) {
    const r = n.length;
    let o = 0;
    const i = [];
    let s = [];
    for (; o < r; ) {
        let l = t;
        let a = null;
        let c = null;
        let d = 0;
        let u = null;
        let f = -1;
        for (; o < r && !(a = l.go(n[o].t)); ) s.push(n[o++]);
        for (; o < r && (c = a || l.go(n[o].t)); ) {
            ((a = null),
                (l = c),
                l.accepts() ? ((f = 0), (u = l)) : f >= 0 && f++,
                o++,
                d++);
        }
        if (f < 0) ((o -= d), o < r && (s.push(n[o]), o++));
        else {
            (s.length > 0 && (i.push(bl(Yu, e, s)), (s = [])),
                (o -= f),
                (d -= f));
            const h = u.t;
            const p = n.slice(o - d, o);
            i.push(bl(h, e, p));
        }
    }
    return (s.length > 0 && i.push(bl(Yu, e, s)), i);
}
function bl(t, e, n) {
    const r = n[0].s;
    const o = n[n.length - 1].e;
    const i = e.slice(r, o);
    return new t(i, n);
}
const gb = (typeof console < "u" && console && console.warn) || (() => {});
const yb =
    "until manual call of linkify.init(). Register all schemes and plugins before invoking linkify the first time.";
const Y = {
    scanner: null,
    parser: null,
    tokenQueue: [],
    pluginQueue: [],
    customSchemes: [],
    initialized: !1,
};
function lf() {
    return (
        (ve.groups = {}),
        (Y.scanner = null),
        (Y.parser = null),
        (Y.tokenQueue = []),
        (Y.pluginQueue = []),
        (Y.customSchemes = []),
        (Y.initialized = !1),
        Y
    );
}
function Pl(t, e = !1) {
    if (
        (Y.initialized &&
            gb(
                `linkifyjs: already initialized - will not register custom scheme "${t}" ${yb}`,
            ),
        !/^[0-9a-z]+(-[0-9a-z]+)*$/.test(t))
    ) {
        throw new Error(`linkifyjs: incorrect scheme format.
1. Must only contain digits, lowercase ASCII letters or "-"
2. Cannot start or end with "-"
3. "-" cannot repeat`);
    }
    Y.customSchemes.push([t, e]);
}
function bb() {
    Y.scanner = ub(Y.customSchemes);
    for (let t = 0; t < Y.tokenQueue.length; t++) {
        Y.tokenQueue[t][1]({ scanner: Y.scanner });
    }
    Y.parser = pb(Y.scanner.tokens);
    for (let t = 0; t < Y.pluginQueue.length; t++) {
        Y.pluginQueue[t][1]({ scanner: Y.scanner, parser: Y.parser });
    }
    return ((Y.initialized = !0), Y);
}
function Zo(t) {
    return (
        Y.initialized || bb(),
        mb(Y.parser.start, t, of(Y.scanner.start, t))
    );
}
Zo.scan = of;
function ei(t, e = null, n = null) {
    if (e && typeof e === "object") {
        if (n) {
            throw Error(`linkifyjs: Invalid link type ${e}; must be a string`);
        }
        ((n = e), (e = null));
    }
    const r = new Il(n);
    const o = Zo(t);
    const i = [];
    for (let s = 0; s < o.length; s++) {
        const l = o[s];
        l.isLink &&
            (!e || l.t === e) &&
            r.check(l) &&
            i.push(l.toFormattedObject(r));
    }
    return i;
}
const Ll = "[\0- \xA0\u1680\u180E\u2000-\u2029\u205F\u3000]";
const wb = new RegExp(Ll);
const xb = new RegExp(`${Ll}$`);
const kb = new RegExp(Ll, "g");
function Sb(t) {
    return t.length === 1
        ? t[0].isLink
        : t.length === 3 && t[1].isLink
          ? ["()", "[]"].includes(t[0].value + t[2].value)
          : !1;
}
function Cb(t) {
    return new L({
        key: new H("autolink"),
        appendTransaction: (e, n, r) => {
            const o = e.some((c) => c.docChanged) && !n.doc.eq(r.doc);
            const i = e.some((c) => c.getMeta("preventAutolink"));
            if (!o || i) return;
            const { tr: s } = r;
            const l = qs(n.doc, [...e]);
            if (
                (Zs(l).forEach(({ newRange: c }) => {
                    const d = _d(r.doc, c, (h) => h.isTextblock);
                    let u;
                    let f;
                    if (d.length > 1) {
                        ((u = d[0]),
                            (f = r.doc.textBetween(
                                u.pos,
                                u.pos + u.node.nodeSize,
                                void 0,
                                " ",
                            )));
                    } else if (d.length) {
                        const h = r.doc.textBetween(c.from, c.to, " ", " ");
                        if (!xb.test(h)) return;
                        ((u = d[0]),
                            (f = r.doc.textBetween(u.pos, c.to, void 0, " ")));
                    }
                    if (u && f) {
                        const h = f.split(wb).filter(Boolean);
                        if (h.length <= 0) return !1;
                        const p = h[h.length - 1];
                        const m = u.pos + f.lastIndexOf(p);
                        if (!p) return !1;
                        const g = Zo(p).map((y) =>
                            y.toObject(t.defaultProtocol),
                        );
                        if (!Sb(g)) return !1;
                        g.filter((y) => y.isLink)
                            .map((y) => ({
                                ...y,
                                from: m + y.start + 1,
                                to: m + y.end + 1,
                            }))
                            .filter((y) =>
                                r.schema.marks.code
                                    ? !r.doc.rangeHasMark(
                                          y.from,
                                          y.to,
                                          r.schema.marks.code,
                                      )
                                    : !0,
                            )
                            .filter((y) => t.validate(y.value))
                            .filter((y) => t.shouldAutoLink(y.value))
                            .forEach((y) => {
                                co(y.from, y.to, r.doc).some(
                                    (b) => b.mark.type === t.type,
                                ) ||
                                    s.addMark(
                                        y.from,
                                        y.to,
                                        t.type.create({ href: y.href }),
                                    );
                            });
                    }
                }),
                !!s.steps.length)
            ) {
                return s;
            }
        },
    });
}
function vb(t) {
    return new L({
        key: new H("handleClickLink"),
        props: {
            handleClick: (e, n, r) => {
                let o, i;
                if (r.button !== 0 || !e.editable) return !1;
                let s = null;
                if (r.target instanceof HTMLAnchorElement) s = r.target;
                else {
                    let d = r.target;
                    const u = [];
                    for (; d.nodeName !== "DIV"; ) {
                        (u.push(d), (d = d.parentNode));
                    }
                    s = u.find((f) => f.nodeName === "A");
                }
                if (!s) return !1;
                const l = Qs(e.state, t.type.name);
                const a = (o = s?.href) != null ? o : l.href;
                const c = (i = s?.target) != null ? i : l.target;
                return (
                    t.enableClickSelection &&
                        t.editor.commands.extendMarkRange(t.type.name),
                    s && a ? (window.open(a, c), !0) : !1
                );
            },
        },
    });
}
function Mb(t) {
    return new L({
        key: new H("handlePasteLink"),
        props: {
            handlePaste: (e, n, r) => {
                const { shouldAutoLink: o } = t;
                const { state: i } = e;
                const { selection: s } = i;
                const { empty: l } = s;
                if (l) return !1;
                let a = "";
                r.content.forEach((d) => {
                    a += d.textContent;
                });
                const c = ei(a, { defaultProtocol: t.defaultProtocol }).find(
                    (d) => d.isLink && d.value === a,
                );
                return !a || !c || (o !== void 0 && !o(c.href))
                    ? !1
                    : t.editor.commands.setMark(t.type, { href: c.href });
            },
        },
    });
}
function en(t, e) {
    const n = [
        "http",
        "https",
        "ftp",
        "ftps",
        "mailto",
        "tel",
        "callto",
        "sms",
        "cid",
        "xmpp",
    ];
    return (
        e &&
            e.forEach((r) => {
                const o = typeof r === "string" ? r : r.scheme;
                o && n.push(o);
            }),
        !t ||
            t
                .replace(kb, "")
                .match(
                    new RegExp(
                        `^(?:(?:${n.join("|")}):|[^a-z]|[a-z0-9+.-]+(?:[^a-z+.-:]|$))`,
                        "i",
                    ),
                )
    );
}
const Tb = Z.create({
    name: "link",
    priority: 1e3,
    keepOnSplit: !1,
    exitable: !0,
    onCreate() {
        (this.options.validate &&
            !this.options.shouldAutoLink &&
            ((this.options.shouldAutoLink = this.options.validate),
            console.warn(
                "The `validate` option is deprecated. Rename to the `shouldAutoLink` option instead.",
            )),
            this.options.protocols.forEach((t) => {
                if (typeof t === "string") {
                    Pl(t);
                    return;
                }
                Pl(t.scheme, t.optionalSlashes);
            }));
    },
    onDestroy() {
        lf();
    },
    inclusive() {
        return this.options.autolink;
    },
    addOptions() {
        return {
            openOnClick: !0,
            enableClickSelection: !1,
            linkOnPaste: !0,
            autolink: !0,
            protocols: [],
            defaultProtocol: "http",
            HTMLAttributes: {
                target: "_blank",
                rel: "noopener noreferrer nofollow",
                class: null,
            },
            isAllowedUri: (t, e) => !!en(t, e.protocols),
            validate: (t) => !!t,
            shouldAutoLink: (t) => !!t,
        };
    },
    addAttributes() {
        return {
            href: {
                default: null,
                parseHTML(t) {
                    return t.getAttribute("href");
                },
            },
            target: { default: this.options.HTMLAttributes.target },
            rel: { default: this.options.HTMLAttributes.rel },
            class: { default: this.options.HTMLAttributes.class },
        };
    },
    parseHTML() {
        return [
            {
                tag: "a[href]",
                getAttrs: (t) => {
                    const e = t.getAttribute("href");
                    return !e ||
                        !this.options.isAllowedUri(e, {
                            defaultValidate: (n) =>
                                !!en(n, this.options.protocols),
                            protocols: this.options.protocols,
                            defaultProtocol: this.options.defaultProtocol,
                        })
                        ? !1
                        : null;
                },
            },
        ];
    },
    renderHTML({ HTMLAttributes: t }) {
        return this.options.isAllowedUri(t.href, {
            defaultValidate: (e) => !!en(e, this.options.protocols),
            protocols: this.options.protocols,
            defaultProtocol: this.options.defaultProtocol,
        })
            ? ["a", O(this.options.HTMLAttributes, t), 0]
            : ["a", O(this.options.HTMLAttributes, { ...t, href: "" }), 0];
    },
    markdownTokenName: "link",
    parseMarkdown: (t, e) =>
        e.applyMark("link", e.parseInline(t.tokens || []), {
            href: t.href,
            title: t.title || null,
        }),
    renderMarkdown: (t, e) => {
        let n;
        const r = ((n = t.attrs) == null ? void 0 : n.href) || "";
        return `[${e.renderChildren(t)}](${r})`;
    },
    addCommands() {
        return {
            setLink:
                (t) =>
                ({ chain: e }) => {
                    const { href: n } = t;
                    return this.options.isAllowedUri(n, {
                        defaultValidate: (r) => !!en(r, this.options.protocols),
                        protocols: this.options.protocols,
                        defaultProtocol: this.options.defaultProtocol,
                    })
                        ? e()
                              .setMark(this.name, t)
                              .setMeta("preventAutolink", !0)
                              .run()
                        : !1;
                },
            toggleLink:
                (t) =>
                ({ chain: e }) => {
                    const { href: n } = t || {};
                    return n &&
                        !this.options.isAllowedUri(n, {
                            defaultValidate: (r) =>
                                !!en(r, this.options.protocols),
                            protocols: this.options.protocols,
                            defaultProtocol: this.options.defaultProtocol,
                        })
                        ? !1
                        : e()
                              .toggleMark(this.name, t, {
                                  extendEmptyMarkRange: !0,
                              })
                              .setMeta("preventAutolink", !0)
                              .run();
                },
            unsetLink:
                () =>
                ({ chain: t }) =>
                    t()
                        .unsetMark(this.name, { extendEmptyMarkRange: !0 })
                        .setMeta("preventAutolink", !0)
                        .run(),
        };
    },
    addPasteRules() {
        return [
            Ce({
                find: (t) => {
                    const e = [];
                    if (t) {
                        const { protocols: n, defaultProtocol: r } =
                            this.options;
                        const o = ei(t).filter(
                            (i) =>
                                i.isLink &&
                                this.options.isAllowedUri(i.value, {
                                    defaultValidate: (s) => !!en(s, n),
                                    protocols: n,
                                    defaultProtocol: r,
                                }),
                        );
                        o.length &&
                            o.forEach((i) => {
                                this.options.shouldAutoLink(i.value) &&
                                    e.push({
                                        text: i.value,
                                        data: { href: i.href },
                                        index: i.start,
                                    });
                            });
                    }
                    return e;
                },
                type: this.type,
                getAttributes: (t) => {
                    let e;
                    return { href: (e = t.data) == null ? void 0 : e.href };
                },
            }),
        ];
    },
    addProseMirrorPlugins() {
        const t = [];
        const { protocols: e, defaultProtocol: n } = this.options;
        return (
            this.options.autolink &&
                t.push(
                    Cb({
                        type: this.type,
                        defaultProtocol: this.options.defaultProtocol,
                        validate: (r) =>
                            this.options.isAllowedUri(r, {
                                defaultValidate: (o) => !!en(o, e),
                                protocols: e,
                                defaultProtocol: n,
                            }),
                        shouldAutoLink: this.options.shouldAutoLink,
                    }),
                ),
            this.options.openOnClick === !0 &&
                t.push(
                    vb({
                        type: this.type,
                        editor: this.editor,
                        enableClickSelection: this.options.enableClickSelection,
                    }),
                ),
            this.options.linkOnPaste &&
                t.push(
                    Mb({
                        editor: this.editor,
                        defaultProtocol: this.options.defaultProtocol,
                        type: this.type,
                        shouldAutoLink: this.options.shouldAutoLink,
                    }),
                ),
            t
        );
    },
});
const af = Tb;
const Ab = Object.defineProperty;
const Eb = (t, e) => {
    for (const n in e) Ab(t, n, { get: e[n], enumerable: !0 });
};
const Nb = "listItem";
const cf = "textStyle";
const df = /^\s*([-+*])\s$/;
const Hl = $.create({
    name: "bulletList",
    addOptions() {
        return {
            itemTypeName: "listItem",
            HTMLAttributes: {},
            keepMarks: !1,
            keepAttributes: !1,
        };
    },
    group: "block list",
    content() {
        return `${this.options.itemTypeName}+`;
    },
    parseHTML() {
        return [{ tag: "ul" }];
    },
    renderHTML({ HTMLAttributes: t }) {
        return ["ul", O(this.options.HTMLAttributes, t), 0];
    },
    markdownTokenName: "list",
    parseMarkdown: (t, e) =>
        t.type !== "list" || t.ordered
            ? []
            : {
                  type: "bulletList",
                  content: t.items ? e.parseChildren(t.items) : [],
              },
    renderMarkdown: (t, e) =>
        t.content
            ? e.renderChildren(
                  t.content,
                  `
`,
              )
            : "",
    markdownOptions: { indentsContent: !0 },
    addCommands() {
        return {
            toggleBulletList:
                () =>
                ({ commands: t, chain: e }) =>
                    this.options.keepAttributes
                        ? e()
                              .toggleList(
                                  this.name,
                                  this.options.itemTypeName,
                                  this.options.keepMarks,
                              )
                              .updateAttributes(
                                  Nb,
                                  this.editor.getAttributes(cf),
                              )
                              .run()
                        : t.toggleList(
                              this.name,
                              this.options.itemTypeName,
                              this.options.keepMarks,
                          ),
        };
    },
    addKeyboardShortcuts() {
        return { "Mod-Shift-8": () => this.editor.commands.toggleBulletList() };
    },
    addInputRules() {
        let t = Ye({ find: df, type: this.type });
        return (
            (this.options.keepMarks || this.options.keepAttributes) &&
                (t = Ye({
                    find: df,
                    type: this.type,
                    keepMarks: this.options.keepMarks,
                    keepAttributes: this.options.keepAttributes,
                    getAttributes: () => this.editor.getAttributes(cf),
                    editor: this.editor,
                })),
            [t]
        );
    },
});
const $l = $.create({
    name: "listItem",
    addOptions() {
        return {
            HTMLAttributes: {},
            bulletListTypeName: "bulletList",
            orderedListTypeName: "orderedList",
        };
    },
    content: "paragraph block*",
    defining: !0,
    parseHTML() {
        return [{ tag: "li" }];
    },
    renderHTML({ HTMLAttributes: t }) {
        return ["li", O(this.options.HTMLAttributes, t), 0];
    },
    markdownTokenName: "list_item",
    parseMarkdown: (t, e) => {
        if (t.type !== "list_item") return [];
        let n = [];
        if (t.tokens && t.tokens.length > 0) {
            if (t.tokens.some((o) => o.type === "paragraph")) {
                n = e.parseChildren(t.tokens);
            } else {
                const o = t.tokens[0];
                if (o && o.type === "text" && o.tokens && o.tokens.length > 0) {
                    if (
                        ((n = [
                            {
                                type: "paragraph",
                                content: e.parseInline(o.tokens),
                            },
                        ]),
                        t.tokens.length > 1)
                    ) {
                        const s = t.tokens.slice(1);
                        const l = e.parseChildren(s);
                        n.push(...l);
                    }
                } else n = e.parseChildren(t.tokens);
            }
        }
        return (
            n.length === 0 && (n = [{ type: "paragraph", content: [] }]),
            { type: "listItem", content: n }
        );
    },
    renderMarkdown: (t, e, n) =>
        or(
            t,
            e,
            (r) =>
                r.parentType === "bulletList"
                    ? "- "
                    : r.parentType === "orderedList"
                      ? `${r.index + 1}. `
                      : "- ",
            n,
        ),
    addKeyboardShortcuts() {
        return {
            Enter: () => this.editor.commands.splitListItem(this.name),
            Tab: () => this.editor.commands.sinkListItem(this.name),
            "Shift-Tab": () => this.editor.commands.liftListItem(this.name),
        };
    },
});
const Ob = {};
Eb(Ob, {
    findListItemPos: () => mr,
    getNextListDepth: () => Fl,
    handleBackspace: () => zl,
    handleDelete: () => Bl,
    hasListBefore: () => pf,
    hasListItemAfter: () => Rb,
    hasListItemBefore: () => mf,
    listItemHasSubList: () => gf,
    nextListIsDeeper: () => yf,
    nextListIsHigher: () => bf,
});
var mr = (t, e) => {
    const { $from: n } = e.selection;
    const r = ee(t, e.schema);
    let o = null;
    let i = n.depth;
    let s = n.pos;
    let l = null;
    for (; i > 0 && l === null; ) {
        ((o = n.node(i)), o.type === r ? (l = i) : ((i -= 1), (s -= 1)));
    }
    return l === null ? null : { $pos: e.doc.resolve(s), depth: l };
};
var Fl = (t, e) => {
    const n = mr(t, e);
    if (!n) return !1;
    const [, r] = qd(e, t, n.$pos.pos + 4);
    return r;
};
var pf = (t, e, n) => {
    const { $anchor: r } = t.selection;
    const o = Math.max(0, r.pos - 2);
    const i = t.doc.resolve(o).node();
    return !(!i || !n.includes(i.type.name));
};
var mf = (t, e) => {
    let n;
    const { $anchor: r } = e.selection;
    const o = e.doc.resolve(r.pos - 2);
    return !(
        o.index() === 0 ||
        ((n = o.nodeBefore) == null ? void 0 : n.type.name) !== t
    );
};
var gf = (t, e, n) => {
    if (!n) return !1;
    const r = ee(t, e.schema);
    let o = !1;
    return (
        n.descendants((i) => {
            i.type === r && (o = !0);
        }),
        o
    );
};
var zl = (t, e, n) => {
    if (t.commands.undoInputRule()) return !0;
    if (t.state.selection.from !== t.state.selection.to) return !1;
    if (!Ge(t.state, e) && pf(t.state, e, n)) {
        const { $anchor: l } = t.state.selection;
        const a = t.state.doc.resolve(l.before() - 1);
        const c = [];
        a.node().descendants((f, h) => {
            f.type.name === e && c.push({ node: f, pos: h });
        });
        const d = c.at(-1);
        if (!d) return !1;
        const u = t.state.doc.resolve(a.start() + d.pos + 1);
        return t
            .chain()
            .cut({ from: l.start() - 1, to: l.end() + 1 }, u.end())
            .joinForward()
            .run();
    }
    if (!Ge(t.state, e) || !Gd(t.state)) return !1;
    const r = mr(e, t.state);
    if (!r) return !1;
    const i = t.state.doc.resolve(r.$pos.pos - 2).node(r.depth);
    const s = gf(e, t.state, i);
    return mf(e, t.state) && !s
        ? t.commands.joinItemBackward()
        : t.chain().liftListItem(e).run();
};
var yf = (t, e) => {
    const n = Fl(t, e);
    const r = mr(t, e);
    return !r || !n ? !1 : n > r.depth;
};
var bf = (t, e) => {
    const n = Fl(t, e);
    const r = mr(t, e);
    return !r || !n ? !1 : n < r.depth;
};
var Bl = (t, e) => {
    if (!Ge(t.state, e) || !Jd(t.state, e)) return !1;
    const { selection: n } = t.state;
    const { $from: r, $to: o } = n;
    return !n.empty && r.sameParent(o)
        ? !1
        : yf(e, t.state)
          ? t
                .chain()
                .focus(t.state.selection.from + 4)
                .lift(e)
                .joinBackward()
                .run()
          : bf(e, t.state)
            ? t.chain().joinForward().joinBackward().run()
            : t.commands.joinItemForward();
};
var Rb = (t, e) => {
    let n;
    const { $anchor: r } = e.selection;
    const o = e.doc.resolve(r.pos - r.parentOffset - 2);
    return !(
        o.index() === o.parent.childCount - 1 ||
        ((n = o.nodeAfter) == null ? void 0 : n.type.name) !== t
    );
};
const Db = j.create({
    name: "listKeymap",
    addOptions() {
        return {
            listTypes: [
                {
                    itemName: "listItem",
                    wrapperNames: ["bulletList", "orderedList"],
                },
                { itemName: "taskItem", wrapperNames: ["taskList"] },
            ],
        };
    },
    addKeyboardShortcuts() {
        return {
            Delete: ({ editor: t }) => {
                let e = !1;
                return (
                    this.options.listTypes.forEach(({ itemName: n }) => {
                        t.state.schema.nodes[n] !== void 0 &&
                            Bl(t, n) &&
                            (e = !0);
                    }),
                    e
                );
            },
            "Mod-Delete": ({ editor: t }) => {
                let e = !1;
                return (
                    this.options.listTypes.forEach(({ itemName: n }) => {
                        t.state.schema.nodes[n] !== void 0 &&
                            Bl(t, n) &&
                            (e = !0);
                    }),
                    e
                );
            },
            Backspace: ({ editor: t }) => {
                let e = !1;
                return (
                    this.options.listTypes.forEach(
                        ({ itemName: n, wrapperNames: r }) => {
                            t.state.schema.nodes[n] !== void 0 &&
                                zl(t, n, r) &&
                                (e = !0);
                        },
                    ),
                    e
                );
            },
            "Mod-Backspace": ({ editor: t }) => {
                let e = !1;
                return (
                    this.options.listTypes.forEach(
                        ({ itemName: n, wrapperNames: r }) => {
                            t.state.schema.nodes[n] !== void 0 &&
                                zl(t, n, r) &&
                                (e = !0);
                        },
                    ),
                    e
                );
            },
        };
    },
});
const uf = /^(\s*)(\d+)\.\s+(.*)$/;
const Ib = /^\s/;
function Pb(t) {
    const e = [];
    let n = 0;
    let r = 0;
    for (; n < t.length; ) {
        const o = t[n];
        const i = o.match(uf);
        if (!i) break;
        const [, s, l, a] = i;
        const c = s.length;
        let d = a;
        let u = n + 1;
        const f = [o];
        for (; u < t.length; ) {
            const h = t[u];
            if (h.match(uf)) break;
            if (h.trim() === "") {
                (f.push(h),
                    (d += `
`),
                    (u += 1));
            } else if (h.match(Ib)) {
                (f.push(h),
                    (d += `
${h.slice(c + 2)}`),
                    (u += 1));
            } else break;
        }
        (e.push({
            indent: c,
            number: parseInt(l, 10),
            content: d.trim(),
            raw: f.join(`
`),
        }),
            (r = u),
            (n = u));
    }
    return [e, r];
}
function wf(t, e, n) {
    let r;
    const o = [];
    let i = 0;
    for (; i < t.length; ) {
        const s = t[i];
        if (s.indent === e) {
            const l = s.content.split(`
`);
            const a = ((r = l[0]) == null ? void 0 : r.trim()) || "";
            const c = [];
            a &&
                c.push({
                    type: "paragraph",
                    raw: a,
                    tokens: n.inlineTokens(a),
                });
            const d = l
                .slice(1)
                .join(
                    `
`,
                )
                .trim();
            if (d) {
                const h = n.blockTokens(d);
                c.push(...h);
            }
            let u = i + 1;
            const f = [];
            for (; u < t.length && t[u].indent > e; ) (f.push(t[u]), (u += 1));
            if (f.length > 0) {
                const h = Math.min(...f.map((m) => m.indent));
                const p = wf(f, h, n);
                c.push({
                    type: "list",
                    ordered: !0,
                    start: f[0].number,
                    items: p,
                    raw: f.map((m) => m.raw).join(`
`),
                });
            }
            (o.push({ type: "list_item", raw: s.raw, tokens: c }), (i = u));
        } else i += 1;
    }
    return o;
}
function Lb(t, e) {
    return t.map((n) => {
        if (n.type !== "list_item") return e.parseChildren([n])[0];
        const r = [];
        return (
            n.tokens &&
                n.tokens.length > 0 &&
                n.tokens.forEach((o) => {
                    if (
                        o.type === "paragraph" ||
                        o.type === "list" ||
                        o.type === "blockquote" ||
                        o.type === "code"
                    ) {
                        r.push(...e.parseChildren([o]));
                    } else if (o.type === "text" && o.tokens) {
                        const i = e.parseChildren([o]);
                        r.push({ type: "paragraph", content: i });
                    } else {
                        const i = e.parseChildren([o]);
                        i.length > 0 && r.push(...i);
                    }
                }),
            { type: "listItem", content: r }
        );
    });
}
const zb = "listItem";
const ff = "textStyle";
const hf = /^(\d+)\.\s$/;
const _l = $.create({
    name: "orderedList",
    addOptions() {
        return {
            itemTypeName: "listItem",
            HTMLAttributes: {},
            keepMarks: !1,
            keepAttributes: !1,
        };
    },
    group: "block list",
    content() {
        return `${this.options.itemTypeName}+`;
    },
    addAttributes() {
        return {
            start: {
                default: 1,
                parseHTML: (t) =>
                    t.hasAttribute("start")
                        ? parseInt(t.getAttribute("start") || "", 10)
                        : 1,
            },
            type: { default: null, parseHTML: (t) => t.getAttribute("type") },
        };
    },
    parseHTML() {
        return [{ tag: "ol" }];
    },
    renderHTML({ HTMLAttributes: t }) {
        const { start: e, ...n } = t;
        return e === 1
            ? ["ol", O(this.options.HTMLAttributes, n), 0]
            : ["ol", O(this.options.HTMLAttributes, t), 0];
    },
    markdownTokenName: "list",
    parseMarkdown: (t, e) => {
        if (t.type !== "list" || !t.ordered) return [];
        const n = t.start || 1;
        const r = t.items ? Lb(t.items, e) : [];
        return n !== 1
            ? { type: "orderedList", attrs: { start: n }, content: r }
            : { type: "orderedList", content: r };
    },
    renderMarkdown: (t, e) =>
        t.content
            ? e.renderChildren(
                  t.content,
                  `
`,
              )
            : "",
    markdownTokenizer: {
        name: "orderedList",
        level: "block",
        start: (t) => {
            const e = t.match(/^(\s*)(\d+)\.\s+/);
            const n = e?.index;
            return n !== void 0 ? n : -1;
        },
        tokenize: (t, e, n) => {
            let r;
            const o = t.split(`
`);
            const [i, s] = Pb(o);
            if (i.length === 0) return;
            const l = wf(i, 0, n);
            return l.length === 0
                ? void 0
                : {
                      type: "list",
                      ordered: !0,
                      start: ((r = i[0]) == null ? void 0 : r.number) || 1,
                      items: l,
                      raw: o.slice(0, s).join(`
`),
                  };
        },
    },
    markdownOptions: { indentsContent: !0 },
    addCommands() {
        return {
            toggleOrderedList:
                () =>
                ({ commands: t, chain: e }) =>
                    this.options.keepAttributes
                        ? e()
                              .toggleList(
                                  this.name,
                                  this.options.itemTypeName,
                                  this.options.keepMarks,
                              )
                              .updateAttributes(
                                  zb,
                                  this.editor.getAttributes(ff),
                              )
                              .run()
                        : t.toggleList(
                              this.name,
                              this.options.itemTypeName,
                              this.options.keepMarks,
                          ),
        };
    },
    addKeyboardShortcuts() {
        return {
            "Mod-Shift-7": () => this.editor.commands.toggleOrderedList(),
        };
    },
    addInputRules() {
        let t = Ye({
            find: hf,
            type: this.type,
            getAttributes: (e) => ({ start: +e[1] }),
            joinPredicate: (e, n) => n.childCount + n.attrs.start === +e[1],
        });
        return (
            (this.options.keepMarks || this.options.keepAttributes) &&
                (t = Ye({
                    find: hf,
                    type: this.type,
                    keepMarks: this.options.keepMarks,
                    keepAttributes: this.options.keepAttributes,
                    getAttributes: (e) => ({
                        start: +e[1],
                        ...this.editor.getAttributes(ff),
                    }),
                    joinPredicate: (e, n) =>
                        n.childCount + n.attrs.start === +e[1],
                    editor: this.editor,
                })),
            [t]
        );
    },
});
const Bb = /^\s*(\[([( |x])?\])\s$/;
const Hb = $.create({
    name: "taskItem",
    addOptions() {
        return {
            nested: !1,
            HTMLAttributes: {},
            taskListTypeName: "taskList",
            a11y: void 0,
        };
    },
    content() {
        return this.options.nested ? "paragraph block*" : "paragraph+";
    },
    defining: !0,
    addAttributes() {
        return {
            checked: {
                default: !1,
                keepOnSplit: !1,
                parseHTML: (t) => {
                    const e = t.getAttribute("data-checked");
                    return e === "" || e === "true";
                },
                renderHTML: (t) => ({ "data-checked": t.checked }),
            },
        };
    },
    parseHTML() {
        return [{ tag: `li[data-type="${this.name}"]`, priority: 51 }];
    },
    renderHTML({ node: t, HTMLAttributes: e }) {
        return [
            "li",
            O(this.options.HTMLAttributes, e, { "data-type": this.name }),
            [
                "label",
                [
                    "input",
                    {
                        type: "checkbox",
                        checked: t.attrs.checked ? "checked" : null,
                    },
                ],
                ["span"],
            ],
            ["div", 0],
        ];
    },
    parseMarkdown: (t, e) => {
        const n = [];
        if (
            (t.tokens && t.tokens.length > 0
                ? n.push(e.createNode("paragraph", {}, e.parseInline(t.tokens)))
                : t.text
                  ? n.push(
                        e.createNode("paragraph", {}, [
                            e.createNode("text", { text: t.text }),
                        ]),
                    )
                  : n.push(e.createNode("paragraph", {}, [])),
            t.nestedTokens && t.nestedTokens.length > 0)
        ) {
            const r = e.parseChildren(t.nestedTokens);
            n.push(...r);
        }
        return e.createNode("taskItem", { checked: t.checked || !1 }, n);
    },
    renderMarkdown: (t, e) => {
        let n;
        const o = `- [${(n = t.attrs) != null && n.checked ? "x" : " "}] `;
        return or(t, e, o);
    },
    addKeyboardShortcuts() {
        const t = {
            Enter: () => this.editor.commands.splitListItem(this.name),
            "Shift-Tab": () => this.editor.commands.liftListItem(this.name),
        };
        return this.options.nested
            ? { ...t, Tab: () => this.editor.commands.sinkListItem(this.name) }
            : t;
    },
    addNodeView() {
        return ({ node: t, HTMLAttributes: e, getPos: n, editor: r }) => {
            const o = document.createElement("li");
            const i = document.createElement("label");
            const s = document.createElement("span");
            const l = document.createElement("input");
            const a = document.createElement("div");
            const c = (d) => {
                let u, f;
                l.ariaLabel =
                    ((f =
                        (u = this.options.a11y) == null
                            ? void 0
                            : u.checkboxLabel) == null
                        ? void 0
                        : f.call(u, d, l.checked)) ||
                    `Task item checkbox for ${d.textContent || "empty task item"}`;
            };
            return (
                c(t),
                (i.contentEditable = "false"),
                (l.type = "checkbox"),
                l.addEventListener("mousedown", (d) => d.preventDefault()),
                l.addEventListener("change", (d) => {
                    if (!r.isEditable && !this.options.onReadOnlyChecked) {
                        l.checked = !l.checked;
                        return;
                    }
                    const { checked: u } = d.target;
                    (r.isEditable &&
                        typeof n === "function" &&
                        r
                            .chain()
                            .focus(void 0, { scrollIntoView: !1 })
                            .command(({ tr: f }) => {
                                const h = n();
                                if (typeof h !== "number") return !1;
                                const p = f.doc.nodeAt(h);
                                return (
                                    f.setNodeMarkup(h, void 0, {
                                        ...p?.attrs,
                                        checked: u,
                                    }),
                                    !0
                                );
                            })
                            .run(),
                        !r.isEditable &&
                            this.options.onReadOnlyChecked &&
                            (this.options.onReadOnlyChecked(t, u) ||
                                (l.checked = !l.checked)));
                }),
                Object.entries(this.options.HTMLAttributes).forEach(
                    ([d, u]) => {
                        o.setAttribute(d, u);
                    },
                ),
                (o.dataset.checked = t.attrs.checked),
                (l.checked = t.attrs.checked),
                i.append(l, s),
                o.append(i, a),
                Object.entries(e).forEach(([d, u]) => {
                    o.setAttribute(d, u);
                }),
                {
                    dom: o,
                    contentDOM: a,
                    update: (d) =>
                        d.type !== this.type
                            ? !1
                            : ((o.dataset.checked = d.attrs.checked),
                              (l.checked = d.attrs.checked),
                              c(d),
                              !0),
                }
            );
        };
    },
    addInputRules() {
        return [
            Ye({
                find: Bb,
                type: this.type,
                getAttributes: (t) => ({ checked: t[t.length - 1] === "x" }),
            }),
        ];
    },
});
const $b = $.create({
    name: "taskList",
    addOptions() {
        return { itemTypeName: "taskItem", HTMLAttributes: {} };
    },
    group: "block list",
    content() {
        return `${this.options.itemTypeName}+`;
    },
    parseHTML() {
        return [{ tag: `ul[data-type="${this.name}"]`, priority: 51 }];
    },
    renderHTML({ HTMLAttributes: t }) {
        return [
            "ul",
            O(this.options.HTMLAttributes, t, { "data-type": this.name }),
            0,
        ];
    },
    parseMarkdown: (t, e) =>
        e.createNode("taskList", {}, e.parseChildren(t.items || [])),
    renderMarkdown: (t, e) =>
        t.content
            ? e.renderChildren(
                  t.content,
                  `
`,
              )
            : "",
    markdownTokenizer: {
        name: "taskList",
        level: "block",
        start(t) {
            let e;
            const n =
                (e = t.match(/^\s*[-+*]\s+\[([ xX])\]\s+/)) == null
                    ? void 0
                    : e.index;
            return n !== void 0 ? n : -1;
        },
        tokenize(t, e, n) {
            const r = (i) => {
                const s = mo(
                    i,
                    {
                        itemPattern: /^(\s*)([-+*])\s+\[([ xX])\]\s+(.*)$/,
                        extractItemData: (l) => ({
                            indentLevel: l[1].length,
                            mainContent: l[4],
                            checked: l[3].toLowerCase() === "x",
                        }),
                        createToken: (l, a) => ({
                            type: "taskItem",
                            raw: "",
                            mainContent: l.mainContent,
                            indentLevel: l.indentLevel,
                            checked: l.checked,
                            text: l.mainContent,
                            tokens: n.inlineTokens(l.mainContent),
                            nestedTokens: a,
                        }),
                        customNestedParser: r,
                    },
                    n,
                );
                return s
                    ? [{ type: "taskList", raw: s.raw, items: s.items }]
                    : n.blockTokens(i);
            };
            const o = mo(
                t,
                {
                    itemPattern: /^(\s*)([-+*])\s+\[([ xX])\]\s+(.*)$/,
                    extractItemData: (i) => ({
                        indentLevel: i[1].length,
                        mainContent: i[4],
                        checked: i[3].toLowerCase() === "x",
                    }),
                    createToken: (i, s) => ({
                        type: "taskItem",
                        raw: "",
                        mainContent: i.mainContent,
                        indentLevel: i.indentLevel,
                        checked: i.checked,
                        text: i.mainContent,
                        tokens: n.inlineTokens(i.mainContent),
                        nestedTokens: s,
                    }),
                    customNestedParser: r,
                },
                n,
            );
            if (o) return { type: "taskList", raw: o.raw, items: o.items };
        },
    },
    markdownOptions: { indentsContent: !0 },
    addCommands() {
        return {
            toggleTaskList:
                () =>
                ({ commands: t }) =>
                    t.toggleList(this.name, this.options.itemTypeName),
        };
    },
    addKeyboardShortcuts() {
        return { "Mod-Shift-9": () => this.editor.commands.toggleTaskList() };
    },
});
const GC = j.create({
    name: "listKit",
    addExtensions() {
        const t = [];
        return (
            this.options.bulletList !== !1 &&
                t.push(Hl.configure(this.options.bulletList)),
            this.options.listItem !== !1 &&
                t.push($l.configure(this.options.listItem)),
            this.options.listKeymap !== !1 &&
                t.push(Db.configure(this.options.listKeymap)),
            this.options.orderedList !== !1 &&
                t.push(_l.configure(this.options.orderedList)),
            this.options.taskItem !== !1 &&
                t.push(Hb.configure(this.options.taskItem)),
            this.options.taskList !== !1 &&
                t.push($b.configure(this.options.taskList)),
            t
        );
    },
});
const ti = (t, e, n = {}) => {
    t.dom
        .closest("form")
        ?.dispatchEvent(
            new CustomEvent(e, { composed: !0, cancelable: !0, detail: n }),
        );
};
const xf = ({
    files: t,
    acceptedTypes: e,
    acceptedTypesValidationMessage: n,
    maxSize: r,
    maxSizeValidationMessage: o,
}) => {
    for (const i of t) {
        if (e && !e.includes(i.type)) return n;
        if (r && i.size > +r * 1024) return o;
    }
    return null;
};
const Fb = ({
    editor: t,
    acceptedTypes: e,
    acceptedTypesValidationMessage: n,
    get$WireUsing: r,
    key: o,
    maxSize: i,
    maxSizeValidationMessage: s,
    statePath: l,
    uploadingMessage: a,
}) => {
    const c = (d) =>
        r().callSchemaComponentMethod(
            o,
            "getUploadedFileAttachmentTemporaryUrl",
            { attachment: d },
        );
    return new L({
        key: new H("localFiles"),
        props: {
            handleDrop(d, u) {
                if (!u.dataTransfer?.files.length) return !1;
                const f = Array.from(u.dataTransfer.files);
                const h = xf({
                    files: f,
                    acceptedTypes: e,
                    acceptedTypesValidationMessage: n,
                    maxSize: i,
                    maxSizeValidationMessage: s,
                });
                if (h) {
                    return (
                        d.dom.dispatchEvent(
                            new CustomEvent(
                                "rich-editor-file-validation-message",
                                {
                                    bubbles: !0,
                                    detail: {
                                        key: o,
                                        livewireId: r().id,
                                        validationMessage: h,
                                    },
                                },
                            ),
                        ),
                        !1
                    );
                }
                if (!f.length) return !1;
                (ti(d, "form-processing-started", { message: a }),
                    u.preventDefault(),
                    u.stopPropagation());
                const p = d.posAtCoords({ left: u.clientX, top: u.clientY });
                return (
                    f.forEach((m, g) => {
                        (t.setEditable(!1),
                            d.dom.dispatchEvent(
                                new CustomEvent("rich-editor-uploading-file", {
                                    bubbles: !0,
                                    detail: { key: o, livewireId: r().id },
                                }),
                            ));
                        const y = ("10000000-1000-4000-8000" + -1e11).replace(
                            /[018]/g,
                            (b) =>
                                (
                                    b ^
                                    (crypto.getRandomValues(
                                        new Uint8Array(1),
                                    )[0] &
                                        (15 >> (b / 4)))
                                ).toString(16),
                        );
                        r().upload(
                            `componentFileAttachments.${l}.${y}`,
                            m,
                            () => {
                                c(y).then((b) => {
                                    b &&
                                        (t
                                            .chain()
                                            .insertContentAt(p?.pos ?? 0, {
                                                type: "image",
                                                attrs: { id: y, src: b },
                                            })
                                            .run(),
                                        t.setEditable(!0),
                                        d.dom.dispatchEvent(
                                            new CustomEvent(
                                                "rich-editor-uploaded-file",
                                                {
                                                    bubbles: !0,
                                                    detail: {
                                                        key: o,
                                                        livewireId: r().id,
                                                    },
                                                },
                                            ),
                                        ),
                                        g === f.length - 1 &&
                                            ti(d, "form-processing-finished"));
                                });
                            },
                        );
                    }),
                    !0
                );
            },
            handlePaste(d, u) {
                if (
                    !u.clipboardData?.files.length ||
                    u.clipboardData?.getData("text").length
                ) {
                    return !1;
                }
                const f = Array.from(u.clipboardData.files);
                const h = xf({
                    files: f,
                    acceptedTypes: e,
                    acceptedTypesValidationMessage: n,
                    maxSize: i,
                    maxSizeValidationMessage: s,
                });
                return h
                    ? (d.dom.dispatchEvent(
                          new CustomEvent(
                              "rich-editor-file-validation-message",
                              {
                                  bubbles: !0,
                                  detail: {
                                      key: o,
                                      livewireId: r().id,
                                      validationMessage: h,
                                  },
                              },
                          ),
                      ),
                      !1)
                    : f.length
                      ? (u.preventDefault(),
                        u.stopPropagation(),
                        ti(d, "form-processing-started", { message: a }),
                        f.forEach((p, m) => {
                            (t.setEditable(!1),
                                d.dom.dispatchEvent(
                                    new CustomEvent(
                                        "rich-editor-uploading-file",
                                        {
                                            bubbles: !0,
                                            detail: {
                                                key: o,
                                                livewireId: r().id,
                                            },
                                        },
                                    ),
                                ));
                            const g = (
                                "10000000-1000-4000-8000" + -1e11
                            ).replace(/[018]/g, (y) =>
                                (
                                    y ^
                                    (crypto.getRandomValues(
                                        new Uint8Array(1),
                                    )[0] &
                                        (15 >> (y / 4)))
                                ).toString(16),
                            );
                            r().upload(
                                `componentFileAttachments.${l}.${g}`,
                                p,
                                () => {
                                    c(g).then((y) => {
                                        y &&
                                            (t
                                                .chain()
                                                .insertContentAt(
                                                    t.state.selection.anchor,
                                                    {
                                                        type: "image",
                                                        attrs: {
                                                            id: g,
                                                            src: y,
                                                        },
                                                    },
                                                )
                                                .run(),
                                            t.setEditable(!0),
                                            d.dom.dispatchEvent(
                                                new CustomEvent(
                                                    "rich-editor-uploaded-file",
                                                    {
                                                        bubbles: !0,
                                                        detail: {
                                                            key: o,
                                                            livewireId: r().id,
                                                        },
                                                    },
                                                ),
                                            ),
                                            m === f.length - 1 &&
                                                ti(
                                                    d,
                                                    "form-processing-finished",
                                                ));
                                    });
                                },
                            );
                        }),
                        !0)
                      : !1;
            },
        },
    });
};
const kf = j.create({
    name: "localFiles",
    addOptions() {
        return {
            acceptedTypes: [],
            acceptedTypesValidationMessage: null,
            key: null,
            maxSize: null,
            maxSizeValidationMessage: null,
            statePath: null,
            uploadingMessage: null,
            get$WireUsing: null,
        };
    },
    addProseMirrorPlugins() {
        return [Fb({ editor: this.editor, ...this.options })];
    },
});
function _b(t) {
    let e;
    const {
        char: n,
        allowSpaces: r,
        allowToIncludeChar: o,
        allowedPrefixes: i,
        startOfLine: s,
        $position: l,
    } = t;
    const a = r && !o;
    const c = hu(n);
    const d = new RegExp(`\\s${c}$`);
    const u = s ? "^" : "";
    const f = o ? "" : c;
    const h = a
        ? new RegExp(`${u}${c}.*?(?=\\s${f}|$)`, "gm")
        : new RegExp(`${u}(?:^)?${c}[^\\s${f}]*`, "gm");
    const p =
        ((e = l.nodeBefore) == null ? void 0 : e.isText) && l.nodeBefore.text;
    if (!p) return null;
    const m = l.pos - p.length;
    const g = Array.from(p.matchAll(h)).pop();
    if (!g || g.input === void 0 || g.index === void 0) return null;
    const y = g.input.slice(Math.max(0, g.index - 1), g.index);
    const b = new RegExp(`^[${i?.join("")}\0]?$`).test(y);
    if (i !== null && !b) return null;
    const k = m + g.index;
    let v = k + g[0].length;
    return (
        a && d.test(p.slice(v - 1, v + 1)) && ((g[0] += " "), (v += 1)),
        k < l.pos && v >= l.pos
            ? {
                  range: { from: k, to: v },
                  query: g[0].slice(n.length),
                  text: g[0],
              }
            : null
    );
}
const Vb = new H("suggestion");
function Wb({
    pluginKey: t = Vb,
    editor: e,
    char: n = "@",
    allowSpaces: r = !1,
    allowToIncludeChar: o = !1,
    allowedPrefixes: i = [" "],
    startOfLine: s = !1,
    decorationTag: l = "span",
    decorationClass: a = "suggestion",
    decorationContent: c = "",
    decorationEmptyClass: d = "is-empty",
    command: u = () => null,
    items: f = () => [],
    render: h = () => ({}),
    allow: p = () => !0,
    findSuggestionMatch: m = _b,
}) {
    let g;
    const y = h?.();
    const b = () => {
        const S = e.state.selection.$anchor.pos;
        const w = e.view.coordsAtPos(S);
        const { top: N, right: D, bottom: M, left: B } = w;
        try {
            return new DOMRect(B, N, D - B, M - N);
        } catch {
            return null;
        }
    };
    const k = (S, w) =>
        w
            ? () => {
                  const N = t.getState(e.state);
                  const D = N?.decorationId;
                  const M = S.dom.querySelector(`[data-decoration-id="${D}"]`);
                  return M?.getBoundingClientRect() || null;
              }
            : b;
    function v(S, w) {
        let N;
        try {
            const M = t.getState(S.state);
            const B = M?.decorationId
                ? S.dom.querySelector(
                      `[data-decoration-id="${M.decorationId}"]`,
                  )
                : null;
            const F = {
                editor: e,
                range: M?.range || { from: 0, to: 0 },
                query: M?.query || null,
                text: M?.text || null,
                items: [],
                command: (K) =>
                    u({
                        editor: e,
                        range: M?.range || { from: 0, to: 0 },
                        props: K,
                    }),
                decorationNode: B,
                clientRect: k(S, B),
            };
            (N = y?.onExit) == null || N.call(y, F);
        } catch {}
        const D = S.state.tr.setMeta(w, { exit: !0 });
        S.dispatch(D);
    }
    const x = new L({
        key: t,
        view() {
            return {
                update: async (S, w) => {
                    let N, D, M, B, F, K, _;
                    const T = (N = this.key) == null ? void 0 : N.getState(w);
                    const W =
                        (D = this.key) == null ? void 0 : D.getState(S.state);
                    const U =
                        T.active && W.active && T.range.from !== W.range.from;
                    const ie = !T.active && W.active;
                    const Te = T.active && !W.active;
                    const Pt = !ie && !Te && T.query !== W.query;
                    const On = ie || (U && Pt);
                    const Sr = Pt || U;
                    const Si = Te || (U && Pt);
                    if (!On && !Sr && !Si) return;
                    const sn = Si && !On ? T : W;
                    const la = S.dom.querySelector(
                        `[data-decoration-id="${sn.decorationId}"]`,
                    );
                    ((g = {
                        editor: e,
                        range: sn.range,
                        query: sn.query,
                        text: sn.text,
                        items: [],
                        command: (Yh) =>
                            u({ editor: e, range: sn.range, props: Yh }),
                        decorationNode: la,
                        clientRect: k(S, la),
                    }),
                        On && ((M = y?.onBeforeStart) == null || M.call(y, g)),
                        Sr && ((B = y?.onBeforeUpdate) == null || B.call(y, g)),
                        (Sr || On) &&
                            (g.items = await f({ editor: e, query: sn.query })),
                        Si && ((F = y?.onExit) == null || F.call(y, g)),
                        Sr && ((K = y?.onUpdate) == null || K.call(y, g)),
                        On && ((_ = y?.onStart) == null || _.call(y, g)));
                },
                destroy: () => {
                    let S;
                    g && ((S = y?.onExit) == null || S.call(y, g));
                },
            };
        },
        state: {
            init() {
                return {
                    active: !1,
                    range: { from: 0, to: 0 },
                    query: null,
                    text: null,
                    composing: !1,
                };
            },
            apply(S, w, N, D) {
                const { isEditable: M } = e;
                const { composing: B } = e.view;
                const { selection: F } = S;
                const { empty: K, from: _ } = F;
                const T = { ...w };
                const W = S.getMeta(t);
                if (W && W.exit) {
                    return (
                        (T.active = !1),
                        (T.decorationId = null),
                        (T.range = { from: 0, to: 0 }),
                        (T.query = null),
                        (T.text = null),
                        T
                    );
                }
                if (((T.composing = B), M && (K || e.view.composing))) {
                    (_ < w.range.from || _ > w.range.to) &&
                        !B &&
                        !w.composing &&
                        (T.active = !1);
                    const U = m({
                        char: n,
                        allowSpaces: r,
                        allowToIncludeChar: o,
                        allowedPrefixes: i,
                        startOfLine: s,
                        $position: F.$from,
                    });
                    const ie = `id_${Math.floor(Math.random() * 4294967295)}`;
                    U &&
                    p({
                        editor: e,
                        state: D,
                        range: U.range,
                        isActive: w.active,
                    })
                        ? ((T.active = !0),
                          (T.decorationId = w.decorationId
                              ? w.decorationId
                              : ie),
                          (T.range = U.range),
                          (T.query = U.query),
                          (T.text = U.text))
                        : (T.active = !1);
                } else T.active = !1;
                return (
                    T.active ||
                        ((T.decorationId = null),
                        (T.range = { from: 0, to: 0 }),
                        (T.query = null),
                        (T.text = null)),
                    T
                );
            },
        },
        props: {
            handleKeyDown(S, w) {
                let N, D, M, B;
                const { active: F, range: K } = x.getState(S.state);
                if (!F) return !1;
                if (w.key === "Escape" || w.key === "Esc") {
                    const T = x.getState(S.state);
                    const W = (N = g?.decorationNode) != null ? N : null;
                    const U =
                        W ??
                        (T?.decorationId
                            ? S.dom.querySelector(
                                  `[data-decoration-id="${T.decorationId}"]`,
                              )
                            : null);
                    if (
                        ((D = y?.onKeyDown) == null
                            ? void 0
                            : D.call(y, {
                                  view: S,
                                  event: w,
                                  range: T.range,
                              })) ||
                        !1
                    ) {
                        return !0;
                    }
                    const Te = {
                        editor: e,
                        range: T.range,
                        query: T.query,
                        text: T.text,
                        items: [],
                        command: (Pt) =>
                            u({ editor: e, range: T.range, props: Pt }),
                        decorationNode: U,
                        clientRect: U
                            ? () => U.getBoundingClientRect() || null
                            : null,
                    };
                    return (
                        (M = y?.onExit) == null || M.call(y, Te),
                        v(S, t),
                        !0
                    );
                }
                return (
                    ((B = y?.onKeyDown) == null
                        ? void 0
                        : B.call(y, { view: S, event: w, range: K })) || !1
                );
            },
            decorations(S) {
                const {
                    active: w,
                    range: N,
                    decorationId: D,
                    query: M,
                } = x.getState(S);
                if (!w) return null;
                const B = !M?.length;
                const F = [a];
                return (
                    B && F.push(d),
                    X.create(S.doc, [
                        Q.inline(N.from, N.to, {
                            nodeName: l,
                            class: F.join(" "),
                            "data-decoration-id": D,
                            "data-decoration-content": c,
                        }),
                    ])
                );
            },
        },
    });
    return x;
}
const Sf = Wb;
const jb = function ({
    editor: t,
    overrideSuggestionOptions: e,
    extensionName: n,
}) {
    const r = new H();
    return {
        editor: t,
        char: "{{",
        pluginKey: r,
        command: ({ editor: o, range: i, props: s }) => {
            (o.view.state.selection.$to.nodeAfter?.text?.startsWith(" ") &&
                (i.to += 1),
                o
                    .chain()
                    .focus()
                    .insertContentAt(i, [
                        { type: n, attrs: { ...s } },
                        { type: "text", text: " " },
                    ])
                    .run(),
                o.view.dom.ownerDocument.defaultView
                    ?.getSelection()
                    ?.collapseToEnd());
        },
        allow: ({ state: o, range: i }) => {
            const s = o.doc.resolve(i.from);
            const l = o.schema.nodes[n];
            return !!s.parent.type.contentMatch.matchType(l);
        },
        ...e,
    };
};
const Cf = $.create({
    name: "mergeTag",
    priority: 101,
    addStorage() {
        return {
            mergeTags: [],
            suggestions: [],
            getSuggestionFromChar: () => null,
        };
    },
    addOptions() {
        return {
            HTMLAttributes: {},
            renderText({ node: t }) {
                return `{{ ${this.mergeTags[t.attrs.id]} }}`;
            },
            deleteTriggerWithBackspace: !1,
            renderHTML({ options: t, node: e }) {
                return [
                    "span",
                    O(this.HTMLAttributes, t.HTMLAttributes),
                    `${this.mergeTags[e.attrs.id]}`,
                ];
            },
            suggestions: [],
            suggestion: {},
        };
    },
    group: "inline",
    inline: !0,
    selectable: !1,
    atom: !0,
    addAttributes() {
        return {
            id: {
                default: null,
                parseHTML: (t) => t.getAttribute("data-id"),
                renderHTML: (t) => (t.id ? { "data-id": t.id } : {}),
            },
        };
    },
    parseHTML() {
        return [{ tag: `span[data-type="${this.name}"]` }];
    },
    renderHTML({ node: t, HTMLAttributes: e }) {
        const n =
            this.editor?.extensionStorage?.[this.name]?.getSuggestionFromChar(
                "{{",
            );
        const r = { ...this.options };
        r.HTMLAttributes = O(
            { "data-type": this.name },
            this.options.HTMLAttributes,
            e,
        );
        const o = this.options.renderHTML({
            options: r,
            node: t,
            suggestion: n,
        });
        return typeof o === "string"
            ? [
                  "span",
                  O({ "data-type": this.name }, this.options.HTMLAttributes, e),
                  o,
              ]
            : o;
    },
    renderText({ node: t }) {
        const e = {
            options: this.options,
            node: t,
            suggestion:
                this.editor?.extensionStorage?.[
                    this.name
                ]?.getSuggestionFromChar("{{"),
        };
        return this.options.renderText(e);
    },
    addKeyboardShortcuts() {
        return {
            Backspace: () =>
                this.editor.commands.command(({ tr: t, state: e }) => {
                    let n = !1;
                    const { selection: r } = e;
                    const { empty: o, anchor: i } = r;
                    if (!o) return !1;
                    let s = new le();
                    let l = 0;
                    return (
                        e.doc.nodesBetween(i - 1, i, (a, c) => {
                            if (a.type.name === this.name) {
                                return ((n = !0), (s = a), (l = c), !1);
                            }
                        }),
                        n &&
                            t.insertText(
                                this.options.deleteTriggerWithBackspace
                                    ? ""
                                    : "{{",
                                l,
                                l + s.nodeSize,
                            ),
                        n
                    );
                }),
        };
    },
    addProseMirrorPlugins() {
        return [
            ...this.storage.suggestions.map(Sf),
            new L({
                props: {
                    handleDrop(t, e) {
                        if (
                            !e ||
                            (e.preventDefault(),
                            !e.dataTransfer.getData("mergeTag"))
                        ) {
                            return !1;
                        }
                        const n = e.dataTransfer.getData("mergeTag");
                        return (
                            t.dispatch(
                                t.state.tr.insert(
                                    t.posAtCoords({
                                        left: e.clientX,
                                        top: e.clientY,
                                    }).pos,
                                    t.state.schema.nodes.mergeTag.create({
                                        id: n,
                                    }),
                                ),
                            ),
                            !1
                        );
                    },
                },
            }),
        ];
    },
    onBeforeCreate() {
        ((this.storage.suggestions = (
            this.options.suggestions.length
                ? this.options.suggestions
                : [this.options.suggestion]
        ).map((t) =>
            jb({
                editor: this.editor,
                overrideSuggestionOptions: t,
                extensionName: this.name,
            }),
        )),
            (this.storage.getSuggestionFromChar = (t) => {
                const e = this.storage.suggestions.find((n) => n.char === t);
                return (
                    e ||
                    (this.storage.suggestions.length
                        ? this.storage.suggestions[0]
                        : null)
                );
            }));
    },
});
const Kb = $.create({
    name: "paragraph",
    priority: 1e3,
    addOptions() {
        return { HTMLAttributes: {} };
    },
    group: "block",
    content: "inline*",
    parseHTML() {
        return [{ tag: "p" }];
    },
    renderHTML({ HTMLAttributes: t }) {
        return ["p", O(this.options.HTMLAttributes, t), 0];
    },
    parseMarkdown: (t, e) => {
        const n = t.tokens || [];
        return n.length === 1 && n[0].type === "image"
            ? e.parseChildren([n[0]])
            : e.createNode("paragraph", void 0, e.parseInline(n));
    },
    renderMarkdown: (t, e) =>
        !t || !Array.isArray(t.content) ? "" : e.renderChildren(t.content),
    addCommands() {
        return {
            setParagraph:
                () =>
                ({ commands: t }) =>
                    t.setNode(this.name),
        };
    },
    addKeyboardShortcuts() {
        return { "Mod-Alt-0": () => this.editor.commands.setParagraph() };
    },
});
const vf = Kb;
const Vl = ul;
const Mf = Z.create({
    name: "small",
    parseHTML() {
        return [{ tag: "small" }];
    },
    renderHTML({ HTMLAttributes: t }) {
        return ["small", t, 0];
    },
    addCommands() {
        return {
            setSmall:
                () =>
                ({ commands: t }) =>
                    t.setMark(this.name),
            toggleSmall:
                () =>
                ({ commands: t }) =>
                    t.toggleMark(this.name),
            unsetSmall:
                () =>
                ({ commands: t }) =>
                    t.unsetMark(this.name),
        };
    },
});
const Tf = Z.create({
    name: "textColor",
    addOptions() {
        return { textColors: {} };
    },
    parseHTML() {
        return [
            { tag: "span", getAttrs: (t) => t.classList?.contains("color") },
        ];
    },
    renderHTML({ HTMLAttributes: t }) {
        const e = { ...t };
        const n = t.class;
        e.class = ["color", n].filter(Boolean).join(" ");
        const r = t["data-color"];
        const i = (this.options.textColors || {})[r];
        const s = typeof r === "string" && r.length > 0;
        const l = i
            ? `--color: ${i.color}; --dark-color: ${i.darkColor}`
            : s
              ? `--color: ${r}; --dark-color: ${r}`
              : null;
        if (l) {
            const a = typeof t.style === "string" ? t.style : "";
            e.style = a ? `${l}; ${a}` : l;
        }
        return ["span", e, 0];
    },
    addAttributes() {
        return {
            "data-color": {
                default: null,
                parseHTML: (t) => t.getAttribute("data-color"),
                renderHTML: (t) =>
                    t["data-color"] ? { "data-color": t["data-color"] } : {},
            },
        };
    },
    addCommands() {
        return {
            setTextColor:
                ({ color: t }) =>
                ({ commands: e }) =>
                    e.setMark(this.name, { "data-color": t }),
            unsetTextColor:
                () =>
                ({ commands: t }) =>
                    t.unsetMark(this.name),
        };
    },
});
const Ub = /(?:^|\s)(~~(?!\s+~~)((?:[^~]+))~~(?!\s+~~))$/;
const qb = /(?:^|\s)(~~(?!\s+~~)((?:[^~]+))~~(?!\s+~~))/g;
const Jb = Z.create({
    name: "strike",
    addOptions() {
        return { HTMLAttributes: {} };
    },
    parseHTML() {
        return [
            { tag: "s" },
            { tag: "del" },
            { tag: "strike" },
            {
                style: "text-decoration",
                consuming: !1,
                getAttrs: (t) => (t.includes("line-through") ? {} : !1),
            },
        ];
    },
    renderHTML({ HTMLAttributes: t }) {
        return ["s", O(this.options.HTMLAttributes, t), 0];
    },
    markdownTokenName: "del",
    parseMarkdown: (t, e) =>
        e.applyMark("strike", e.parseInline(t.tokens || [])),
    renderMarkdown: (t, e) => `~~${e.renderChildren(t)}~~`,
    addCommands() {
        return {
            setStrike:
                () =>
                ({ commands: t }) =>
                    t.setMark(this.name),
            toggleStrike:
                () =>
                ({ commands: t }) =>
                    t.toggleMark(this.name),
            unsetStrike:
                () =>
                ({ commands: t }) =>
                    t.unsetMark(this.name),
        };
    },
    addKeyboardShortcuts() {
        return { "Mod-Shift-s": () => this.editor.commands.toggleStrike() };
    },
    addInputRules() {
        return [Le({ find: Ub, type: this.type })];
    },
    addPasteRules() {
        return [Ce({ find: qb, type: this.type })];
    },
});
const Af = Jb;
const Gb = Z.create({
    name: "subscript",
    addOptions() {
        return { HTMLAttributes: {} };
    },
    parseHTML() {
        return [
            { tag: "sub" },
            {
                style: "vertical-align",
                getAttrs(t) {
                    return t !== "sub" ? !1 : null;
                },
            },
        ];
    },
    renderHTML({ HTMLAttributes: t }) {
        return ["sub", O(this.options.HTMLAttributes, t), 0];
    },
    addCommands() {
        return {
            setSubscript:
                () =>
                ({ commands: t }) =>
                    t.setMark(this.name),
            toggleSubscript:
                () =>
                ({ commands: t }) =>
                    t.toggleMark(this.name),
            unsetSubscript:
                () =>
                ({ commands: t }) =>
                    t.unsetMark(this.name),
        };
    },
    addKeyboardShortcuts() {
        return { "Mod-,": () => this.editor.commands.toggleSubscript() };
    },
});
const Ef = Gb;
const Xb = Z.create({
    name: "superscript",
    addOptions() {
        return { HTMLAttributes: {} };
    },
    parseHTML() {
        return [
            { tag: "sup" },
            {
                style: "vertical-align",
                getAttrs(t) {
                    return t !== "super" ? !1 : null;
                },
            },
        ];
    },
    renderHTML({ HTMLAttributes: t }) {
        return ["sup", O(this.options.HTMLAttributes, t), 0];
    },
    addCommands() {
        return {
            setSuperscript:
                () =>
                ({ commands: t }) =>
                    t.setMark(this.name),
            toggleSuperscript:
                () =>
                ({ commands: t }) =>
                    t.toggleMark(this.name),
            unsetSuperscript:
                () =>
                ({ commands: t }) =>
                    t.unsetMark(this.name),
        };
    },
    addKeyboardShortcuts() {
        return { "Mod-.": () => this.editor.commands.toggleSuperscript() };
    },
});
const Nf = Xb;
let jl, Kl;
if (typeof WeakMap < "u") {
    const t = new WeakMap();
    ((jl = (e) => t.get(e)), (Kl = (e, n) => (t.set(e, n), n)));
} else {
    const t = [];
    let n = 0;
    ((jl = (r) => {
        for (let o = 0; o < t.length; o += 2) if (t[o] == r) return t[o + 1];
    }),
        (Kl = (r, o) => (n == 10 && (n = 0), (t[n++] = r), (t[n++] = o))));
}
const ne = class {
    constructor(t, e, n, r) {
        ((this.width = t),
            (this.height = e),
            (this.map = n),
            (this.problems = r));
    }

    findCell(t) {
        for (let e = 0; e < this.map.length; e++) {
            const n = this.map[e];
            if (n != t) continue;
            const r = e % this.width;
            const o = (e / this.width) | 0;
            let i = r + 1;
            let s = o + 1;
            for (let l = 1; i < this.width && this.map[e + l] == n; l++) i++;
            for (
                let l = 1;
                s < this.height && this.map[e + this.width * l] == n;
                l++
            ) {
                s++;
            }
            return { left: r, top: o, right: i, bottom: s };
        }
        throw new RangeError(`No cell with offset ${t} found`);
    }

    colCount(t) {
        for (let e = 0; e < this.map.length; e++) {
            if (this.map[e] == t) return e % this.width;
        }
        throw new RangeError(`No cell with offset ${t} found`);
    }

    nextCell(t, e, n) {
        const { left: r, right: o, top: i, bottom: s } = this.findCell(t);
        return e == "horiz"
            ? (n < 0 ? r == 0 : o == this.width)
                ? null
                : this.map[i * this.width + (n < 0 ? r - 1 : o)]
            : (n < 0 ? i == 0 : s == this.height)
              ? null
              : this.map[r + this.width * (n < 0 ? i - 1 : s)];
    }

    rectBetween(t, e) {
        const { left: n, right: r, top: o, bottom: i } = this.findCell(t);
        const { left: s, right: l, top: a, bottom: c } = this.findCell(e);
        return {
            left: Math.min(n, s),
            top: Math.min(o, a),
            right: Math.max(r, l),
            bottom: Math.max(i, c),
        };
    }

    cellsInRect(t) {
        const e = [];
        const n = {};
        for (let r = t.top; r < t.bottom; r++) {
            for (let o = t.left; o < t.right; o++) {
                const i = r * this.width + o;
                const s = this.map[i];
                n[s] ||
                    ((n[s] = !0),
                    !(
                        (o == t.left && o && this.map[i - 1] == s) ||
                        (r == t.top && r && this.map[i - this.width] == s)
                    ) && e.push(s));
            }
        }
        return e;
    }

    positionAt(t, e, n) {
        for (let r = 0, o = 0; ; r++) {
            const i = o + n.child(r).nodeSize;
            if (r == t) {
                let s = e + t * this.width;
                const l = (t + 1) * this.width;
                for (; s < l && this.map[s] < o; ) s++;
                return s == l ? i - 1 : this.map[s];
            }
            o = i;
        }
    }

    static get(t) {
        return jl(t) || Kl(t, Yb(t));
    }
};
function Yb(t) {
    if (t.type.spec.tableRole != "table") {
        throw new RangeError("Not a table node: " + t.type.name);
    }
    const e = Qb(t);
    const n = t.childCount;
    const r = [];
    let o = 0;
    let i = null;
    const s = [];
    for (let c = 0, d = e * n; c < d; c++) r[c] = 0;
    for (let c = 0, d = 0; c < n; c++) {
        const u = t.child(c);
        d++;
        for (let p = 0; ; p++) {
            for (; o < r.length && r[o] != 0; ) o++;
            if (p == u.childCount) break;
            const m = u.child(p);
            const { colspan: g, rowspan: y, colwidth: b } = m.attrs;
            for (let k = 0; k < y; k++) {
                if (k + c >= n) {
                    (i || (i = [])).push({
                        type: "overlong_rowspan",
                        pos: d,
                        n: y - k,
                    });
                    break;
                }
                const v = o + k * e;
                for (let x = 0; x < g; x++) {
                    r[v + x] == 0
                        ? (r[v + x] = d)
                        : (i || (i = [])).push({
                              type: "collision",
                              row: c,
                              pos: d,
                              n: g - x,
                          });
                    const S = b && b[x];
                    if (S) {
                        const w = ((v + x) % e) * 2;
                        const N = s[w];
                        N == null || (N != S && s[w + 1] == 1)
                            ? ((s[w] = S), (s[w + 1] = 1))
                            : N == S && s[w + 1]++;
                    }
                }
            }
            ((o += g), (d += m.nodeSize));
        }
        const f = (c + 1) * e;
        let h = 0;
        for (; o < f; ) r[o++] == 0 && h++;
        (h && (i || (i = [])).push({ type: "missing", row: c, n: h }), d++);
    }
    (e === 0 || n === 0) && (i || (i = [])).push({ type: "zero_sized" });
    const l = new ne(e, n, r, i);
    let a = !1;
    for (let c = 0; !a && c < s.length; c += 2) {
        s[c] != null && s[c + 1] < n && (a = !0);
    }
    return (a && Zb(l, s, t), l);
}
function Qb(t) {
    let e = -1;
    let n = !1;
    for (let r = 0; r < t.childCount; r++) {
        const o = t.child(r);
        let i = 0;
        if (n) {
            for (let s = 0; s < r; s++) {
                const l = t.child(s);
                for (let a = 0; a < l.childCount; a++) {
                    const c = l.child(a);
                    s + c.attrs.rowspan > r && (i += c.attrs.colspan);
                }
            }
        }
        for (let s = 0; s < o.childCount; s++) {
            const l = o.child(s);
            ((i += l.attrs.colspan), l.attrs.rowspan > 1 && (n = !0));
        }
        e == -1 ? (e = i) : e != i && (e = Math.max(e, i));
    }
    return e;
}
function Zb(t, e, n) {
    t.problems || (t.problems = []);
    const r = {};
    for (let o = 0; o < t.map.length; o++) {
        const i = t.map[o];
        if (r[i]) continue;
        r[i] = !0;
        const s = n.nodeAt(i);
        if (!s) throw new RangeError(`No cell with offset ${i} found`);
        let l = null;
        const a = s.attrs;
        for (let c = 0; c < a.colspan; c++) {
            const d = (o + c) % t.width;
            const u = e[d * 2];
            u != null &&
                (!a.colwidth || a.colwidth[c] != u) &&
                ((l || (l = ew(a)))[c] = u);
        }
        l &&
            t.problems.unshift({
                type: "colwidth mismatch",
                pos: i,
                colwidth: l,
            });
    }
}
function ew(t) {
    if (t.colwidth) return t.colwidth.slice();
    const e = [];
    for (let n = 0; n < t.colspan; n++) e.push(0);
    return e;
}
function ye(t) {
    let e = t.cached.tableNodeTypes;
    if (!e) {
        e = t.cached.tableNodeTypes = {};
        for (const n in t.nodes) {
            const r = t.nodes[n];
            const o = r.spec.tableRole;
            o && (e[o] = r);
        }
    }
    return e;
}
const It = new H("selectingCells");
function Tn(t) {
    for (let e = t.depth - 1; e > 0; e--) {
        if (t.node(e).type.spec.tableRole == "row") {
            return t.node(0).resolve(t.before(e + 1));
        }
    }
    return null;
}
function tw(t) {
    for (let e = t.depth; e > 0; e--) {
        const n = t.node(e).type.spec.tableRole;
        if (n === "cell" || n === "header_cell") return t.node(e);
    }
    return null;
}
function Ve(t) {
    const e = t.selection.$head;
    for (let n = e.depth; n > 0; n--) {
        if (e.node(n).type.spec.tableRole == "row") return !0;
    }
    return !1;
}
function li(t) {
    const e = t.selection;
    if ("$anchorCell" in e && e.$anchorCell) {
        return e.$anchorCell.pos > e.$headCell.pos
            ? e.$anchorCell
            : e.$headCell;
    }
    if ("node" in e && e.node && e.node.type.spec.tableRole == "cell") {
        return e.$anchor;
    }
    const n = Tn(e.$head) || nw(e.$head);
    if (n) return n;
    throw new RangeError(`No cell found around position ${e.head}`);
}
function nw(t) {
    for (let e = t.nodeAfter, n = t.pos; e; e = e.firstChild, n++) {
        const r = e.type.spec.tableRole;
        if (r == "cell" || r == "header_cell") return t.doc.resolve(n);
    }
    for (let e = t.nodeBefore, n = t.pos; e; e = e.lastChild, n--) {
        const r = e.type.spec.tableRole;
        if (r == "cell" || r == "header_cell") {
            return t.doc.resolve(n - e.nodeSize);
        }
    }
}
function Ul(t) {
    return t.parent.type.spec.tableRole == "row" && !!t.nodeAfter;
}
function rw(t) {
    return t.node(0).resolve(t.pos + t.nodeAfter.nodeSize);
}
function Gl(t, e) {
    return t.depth == e.depth && t.pos >= e.start(-1) && t.pos <= e.end(-1);
}
function $f(t, e, n) {
    const r = t.node(-1);
    const o = ne.get(r);
    const i = t.start(-1);
    const s = o.nextCell(t.pos - i, e, n);
    return s == null ? null : t.node(0).resolve(i + s);
}
function tn(t, e, n = 1) {
    const r = { ...t, colspan: t.colspan - n };
    return (
        r.colwidth &&
            ((r.colwidth = r.colwidth.slice()),
            r.colwidth.splice(e, n),
            r.colwidth.some((o) => o > 0) || (r.colwidth = null)),
        r
    );
}
function Ff(t, e, n = 1) {
    const r = { ...t, colspan: t.colspan + n };
    if (r.colwidth) {
        r.colwidth = r.colwidth.slice();
        for (let o = 0; o < n; o++) r.colwidth.splice(e, 0, 0);
    }
    return r;
}
function ow(t, e, n) {
    const r = ye(e.type.schema).header_cell;
    for (let o = 0; o < t.height; o++) {
        if (e.nodeAt(t.map[n + o * t.width]).type != r) return !1;
    }
    return !0;
}
const G = class wt extends I {
    constructor(e, n = e) {
        const r = e.node(-1);
        const o = ne.get(r);
        const i = e.start(-1);
        const s = o.rectBetween(e.pos - i, n.pos - i);
        const l = e.node(0);
        const a = o.cellsInRect(s).filter((d) => d != n.pos - i);
        a.unshift(n.pos - i);
        const c = a.map((d) => {
            const u = r.nodeAt(d);
            if (!u) throw RangeError(`No cell with offset ${d} found`);
            const f = i + d + 1;
            return new hn(l.resolve(f), l.resolve(f + u.content.size));
        });
        (super(c[0].$from, c[0].$to, c),
            (this.$anchorCell = e),
            (this.$headCell = n));
    }

    map(e, n) {
        const r = e.resolve(n.map(this.$anchorCell.pos));
        const o = e.resolve(n.map(this.$headCell.pos));
        if (Ul(r) && Ul(o) && Gl(r, o)) {
            const i = this.$anchorCell.node(-1) != r.node(-1);
            return i && this.isRowSelection()
                ? wt.rowSelection(r, o)
                : i && this.isColSelection()
                  ? wt.colSelection(r, o)
                  : new wt(r, o);
        }
        return R.between(r, o);
    }

    content() {
        const e = this.$anchorCell.node(-1);
        const n = ne.get(e);
        const r = this.$anchorCell.start(-1);
        const o = n.rectBetween(
            this.$anchorCell.pos - r,
            this.$headCell.pos - r,
        );
        const i = {};
        const s = [];
        for (let a = o.top; a < o.bottom; a++) {
            const c = [];
            for (
                let d = a * n.width + o.left, u = o.left;
                u < o.right;
                u++, d++
            ) {
                const f = n.map[d];
                if (i[f]) continue;
                i[f] = !0;
                const h = n.findCell(f);
                let p = e.nodeAt(f);
                if (!p) throw RangeError(`No cell with offset ${f} found`);
                const m = o.left - h.left;
                const g = h.right - o.right;
                if (m > 0 || g > 0) {
                    let y = p.attrs;
                    if (
                        (m > 0 && (y = tn(y, 0, m)),
                        g > 0 && (y = tn(y, y.colspan - g, g)),
                        h.left < o.left)
                    ) {
                        if (((p = p.type.createAndFill(y)), !p)) {
                            throw RangeError(
                                `Could not create cell with attrs ${JSON.stringify(y)}`,
                            );
                        }
                    } else p = p.type.create(y, p.content);
                }
                if (h.top < o.top || h.bottom > o.bottom) {
                    const y = {
                        ...p.attrs,
                        rowspan:
                            Math.min(h.bottom, o.bottom) -
                            Math.max(h.top, o.top),
                    };
                    h.top < o.top
                        ? (p = p.type.createAndFill(y))
                        : (p = p.type.create(y, p.content));
                }
                c.push(p);
            }
            s.push(e.child(a).copy(C.from(c)));
        }
        const l = this.isColSelection() && this.isRowSelection() ? e : s;
        return new E(C.from(l), 1, 1);
    }

    replace(e, n = E.empty) {
        const r = e.steps.length;
        const o = this.ranges;
        for (let s = 0; s < o.length; s++) {
            const { $from: l, $to: a } = o[s];
            const c = e.mapping.slice(r);
            e.replace(c.map(l.pos), c.map(a.pos), s ? E.empty : n);
        }
        const i = I.findFrom(
            e.doc.resolve(e.mapping.slice(r).map(this.to)),
            -1,
        );
        i && e.setSelection(i);
    }

    replaceWith(e, n) {
        this.replace(e, new E(C.from(n), 0, 0));
    }

    forEachCell(e) {
        const n = this.$anchorCell.node(-1);
        const r = ne.get(n);
        const o = this.$anchorCell.start(-1);
        const i = r.cellsInRect(
            r.rectBetween(this.$anchorCell.pos - o, this.$headCell.pos - o),
        );
        for (let s = 0; s < i.length; s++) e(n.nodeAt(i[s]), o + i[s]);
    }

    isColSelection() {
        const e = this.$anchorCell.index(-1);
        const n = this.$headCell.index(-1);
        if (Math.min(e, n) > 0) return !1;
        const r = e + this.$anchorCell.nodeAfter.attrs.rowspan;
        const o = n + this.$headCell.nodeAfter.attrs.rowspan;
        return Math.max(r, o) == this.$headCell.node(-1).childCount;
    }

    static colSelection(e, n = e) {
        const r = e.node(-1);
        const o = ne.get(r);
        const i = e.start(-1);
        const s = o.findCell(e.pos - i);
        const l = o.findCell(n.pos - i);
        const a = e.node(0);
        return (
            s.top <= l.top
                ? (s.top > 0 && (e = a.resolve(i + o.map[s.left])),
                  l.bottom < o.height &&
                      (n = a.resolve(
                          i + o.map[o.width * (o.height - 1) + l.right - 1],
                      )))
                : (l.top > 0 && (n = a.resolve(i + o.map[l.left])),
                  s.bottom < o.height &&
                      (e = a.resolve(
                          i + o.map[o.width * (o.height - 1) + s.right - 1],
                      ))),
            new wt(e, n)
        );
    }

    isRowSelection() {
        const e = this.$anchorCell.node(-1);
        const n = ne.get(e);
        const r = this.$anchorCell.start(-1);
        const o = n.colCount(this.$anchorCell.pos - r);
        const i = n.colCount(this.$headCell.pos - r);
        if (Math.min(o, i) > 0) return !1;
        const s = o + this.$anchorCell.nodeAfter.attrs.colspan;
        const l = i + this.$headCell.nodeAfter.attrs.colspan;
        return Math.max(s, l) == n.width;
    }

    eq(e) {
        return (
            e instanceof wt &&
            e.$anchorCell.pos == this.$anchorCell.pos &&
            e.$headCell.pos == this.$headCell.pos
        );
    }

    static rowSelection(e, n = e) {
        const r = e.node(-1);
        const o = ne.get(r);
        const i = e.start(-1);
        const s = o.findCell(e.pos - i);
        const l = o.findCell(n.pos - i);
        const a = e.node(0);
        return (
            s.left <= l.left
                ? (s.left > 0 && (e = a.resolve(i + o.map[s.top * o.width])),
                  l.right < o.width &&
                      (n = a.resolve(i + o.map[o.width * (l.top + 1) - 1])))
                : (l.left > 0 && (n = a.resolve(i + o.map[l.top * o.width])),
                  s.right < o.width &&
                      (e = a.resolve(i + o.map[o.width * (s.top + 1) - 1]))),
            new wt(e, n)
        );
    }

    toJSON() {
        return {
            type: "cell",
            anchor: this.$anchorCell.pos,
            head: this.$headCell.pos,
        };
    }

    static fromJSON(e, n) {
        return new wt(e.resolve(n.anchor), e.resolve(n.head));
    }

    static create(e, n, r = n) {
        return new wt(e.resolve(n), e.resolve(r));
    }

    getBookmark() {
        return new iw(this.$anchorCell.pos, this.$headCell.pos);
    }
};
G.prototype.visible = !1;
I.jsonID("cell", G);
var iw = class _f {
    constructor(e, n) {
        ((this.anchor = e), (this.head = n));
    }

    map(e) {
        return new _f(e.map(this.anchor), e.map(this.head));
    }

    resolve(e) {
        const n = e.resolve(this.anchor);
        const r = e.resolve(this.head);
        return n.parent.type.spec.tableRole == "row" &&
            r.parent.type.spec.tableRole == "row" &&
            n.index() < n.parent.childCount &&
            r.index() < r.parent.childCount &&
            Gl(n, r)
            ? new G(n, r)
            : I.near(r, 1);
    }
};
function sw(t) {
    if (!(t.selection instanceof G)) return null;
    const e = [];
    return (
        t.selection.forEachCell((n, r) => {
            e.push(Q.node(r, r + n.nodeSize, { class: "selectedCell" }));
        }),
        X.create(t.doc, e)
    );
}
function lw({ $from: t, $to: e }) {
    if (t.pos == e.pos || t.pos < e.pos - 6) return !1;
    let n = t.pos;
    let r = e.pos;
    let o = t.depth;
    for (; o >= 0 && !(t.after(o + 1) < t.end(o)); o--, n++);
    for (let i = e.depth; i >= 0 && !(e.before(i + 1) > e.start(i)); i--, r--);
    return n == r && /row|table/.test(t.node(o).type.spec.tableRole);
}
function aw({ $from: t, $to: e }) {
    let n, r;
    for (let o = t.depth; o > 0; o--) {
        const i = t.node(o);
        if (
            i.type.spec.tableRole === "cell" ||
            i.type.spec.tableRole === "header_cell"
        ) {
            n = i;
            break;
        }
    }
    for (let o = e.depth; o > 0; o--) {
        const i = e.node(o);
        if (
            i.type.spec.tableRole === "cell" ||
            i.type.spec.tableRole === "header_cell"
        ) {
            r = i;
            break;
        }
    }
    return n !== r && e.parentOffset === 0;
}
function cw(t, e, n) {
    const r = (e || t).selection;
    const o = (e || t).doc;
    let i;
    let s;
    if (r instanceof P && (s = r.node.type.spec.tableRole)) {
        if (s == "cell" || s == "header_cell") i = G.create(o, r.from);
        else if (s == "row") {
            const l = o.resolve(r.from + 1);
            i = G.rowSelection(l, l);
        } else if (!n) {
            const l = ne.get(r.node);
            const a = r.from + 1;
            const c = a + l.map[l.width * l.height - 1];
            i = G.create(o, a + 1, c);
        }
    } else {
        r instanceof R && lw(r)
            ? (i = R.create(o, r.from))
            : r instanceof R &&
              aw(r) &&
              (i = R.create(o, r.$from.start(), r.$from.end()));
    }
    return (i && (e || (e = t.tr)).setSelection(i), e);
}
const dw = new H("fix-tables");
function Vf(t, e, n, r) {
    const o = t.childCount;
    const i = e.childCount;
    e: for (let s = 0, l = 0; s < i; s++) {
        const a = e.child(s);
        for (let c = l, d = Math.min(o, s + 3); c < d; c++) {
            if (t.child(c) == a) {
                ((l = c + 1), (n += a.nodeSize));
                continue e;
            }
        }
        (r(a, n),
            l < o && t.child(l).sameMarkup(a)
                ? Vf(t.child(l), a, n + 1, r)
                : a.nodesBetween(0, a.content.size, r, n + 1),
            (n += a.nodeSize));
    }
}
function Xl(t, e) {
    let n;
    const r = (o, i) => {
        o.type.spec.tableRole == "table" && (n = uw(t, o, i, n));
    };
    return (
        e ? e.doc != t.doc && Vf(e.doc, t.doc, 0, r) : t.doc.descendants(r),
        n
    );
}
function uw(t, e, n, r) {
    const o = ne.get(e);
    if (!o.problems) return r;
    r || (r = t.tr);
    const i = [];
    for (let a = 0; a < o.height; a++) i.push(0);
    for (let a = 0; a < o.problems.length; a++) {
        const c = o.problems[a];
        if (c.type == "collision") {
            const d = e.nodeAt(c.pos);
            if (!d) continue;
            const u = d.attrs;
            for (let f = 0; f < u.rowspan; f++) i[c.row + f] += c.n;
            r.setNodeMarkup(
                r.mapping.map(n + 1 + c.pos),
                null,
                tn(u, u.colspan - c.n, c.n),
            );
        } else if (c.type == "missing") i[c.row] += c.n;
        else if (c.type == "overlong_rowspan") {
            const d = e.nodeAt(c.pos);
            if (!d) continue;
            r.setNodeMarkup(r.mapping.map(n + 1 + c.pos), null, {
                ...d.attrs,
                rowspan: d.attrs.rowspan - c.n,
            });
        } else if (c.type == "colwidth mismatch") {
            const d = e.nodeAt(c.pos);
            if (!d) continue;
            r.setNodeMarkup(r.mapping.map(n + 1 + c.pos), null, {
                ...d.attrs,
                colwidth: c.colwidth,
            });
        } else if (c.type == "zero_sized") {
            const d = r.mapping.map(n);
            r.delete(d, d + e.nodeSize);
        }
    }
    let s, l;
    for (let a = 0; a < i.length; a++) i[a] && (s == null && (s = a), (l = a));
    for (let a = 0, c = n + 1; a < o.height; a++) {
        const d = e.child(a);
        const u = c + d.nodeSize;
        const f = i[a];
        if (f > 0) {
            let h = "cell";
            d.firstChild && (h = d.firstChild.type.spec.tableRole);
            const p = [];
            for (let g = 0; g < f; g++) {
                const y = ye(t.schema)[h].createAndFill();
                y && p.push(y);
            }
            const m = (a == 0 || s == a - 1) && l == a ? c + 1 : u - 1;
            r.insert(r.mapping.map(m), p);
        }
        c = u;
    }
    return r.setMeta(dw, { fixTables: !0 });
}
function nt(t) {
    const e = t.selection;
    const n = li(t);
    const r = n.node(-1);
    const o = n.start(-1);
    const i = ne.get(r);
    return {
        ...(e instanceof G
            ? i.rectBetween(e.$anchorCell.pos - o, e.$headCell.pos - o)
            : i.findCell(n.pos - o)),
        tableStart: o,
        map: i,
        table: r,
    };
}
function Wf(t, { map: e, tableStart: n, table: r }, o) {
    let i = o > 0 ? -1 : 0;
    ow(e, r, o + i) && (i = o == 0 || o == e.width ? null : 0);
    for (let s = 0; s < e.height; s++) {
        const l = s * e.width + o;
        if (o > 0 && o < e.width && e.map[l - 1] == e.map[l]) {
            const a = e.map[l];
            const c = r.nodeAt(a);
            (t.setNodeMarkup(
                t.mapping.map(n + a),
                null,
                Ff(c.attrs, o - e.colCount(a)),
            ),
                (s += c.attrs.rowspan - 1));
        } else {
            const a =
                i == null
                    ? ye(r.type.schema).cell
                    : r.nodeAt(e.map[l + i]).type;
            const c = e.positionAt(s, o, r);
            t.insert(t.mapping.map(n + c), a.createAndFill());
        }
    }
    return t;
}
function jf(t, e) {
    if (!Ve(t)) return !1;
    if (e) {
        const n = nt(t);
        e(Wf(t.tr, n, n.left));
    }
    return !0;
}
function Kf(t, e) {
    if (!Ve(t)) return !1;
    if (e) {
        const n = nt(t);
        e(Wf(t.tr, n, n.right));
    }
    return !0;
}
function fw(t, { map: e, table: n, tableStart: r }, o) {
    const i = t.mapping.maps.length;
    for (let s = 0; s < e.height; ) {
        const l = s * e.width + o;
        const a = e.map[l];
        const c = n.nodeAt(a);
        const d = c.attrs;
        if (
            (o > 0 && e.map[l - 1] == a) ||
            (o < e.width - 1 && e.map[l + 1] == a)
        ) {
            t.setNodeMarkup(
                t.mapping.slice(i).map(r + a),
                null,
                tn(d, o - e.colCount(a)),
            );
        } else {
            const u = t.mapping.slice(i).map(r + a);
            t.delete(u, u + c.nodeSize);
        }
        s += d.rowspan;
    }
}
function Uf(t, e) {
    if (!Ve(t)) return !1;
    if (e) {
        const n = nt(t);
        const r = t.tr;
        if (n.left == 0 && n.right == n.map.width) return !1;
        for (let o = n.right - 1; fw(r, n, o), o != n.left; o--) {
            const i = n.tableStart ? r.doc.nodeAt(n.tableStart - 1) : r.doc;
            if (!i) throw RangeError("No table found");
            ((n.table = i), (n.map = ne.get(i)));
        }
        e(r);
    }
    return !0;
}
function hw(t, e, n) {
    let r;
    const o = ye(e.type.schema).header_cell;
    for (let i = 0; i < t.width; i++) {
        if (
            ((r = e.nodeAt(t.map[i + n * t.width])) == null
                ? void 0
                : r.type) != o
        ) {
            return !1;
        }
    }
    return !0;
}
function qf(t, { map: e, tableStart: n, table: r }, o) {
    let i;
    let s = n;
    for (let c = 0; c < o; c++) s += r.child(c).nodeSize;
    const l = [];
    let a = o > 0 ? -1 : 0;
    hw(e, r, o + a) && (a = o == 0 || o == e.height ? null : 0);
    for (let c = 0, d = e.width * o; c < e.width; c++, d++) {
        if (o > 0 && o < e.height && e.map[d] == e.map[d - e.width]) {
            const u = e.map[d];
            const f = r.nodeAt(u).attrs;
            (t.setNodeMarkup(n + u, null, { ...f, rowspan: f.rowspan + 1 }),
                (c += f.colspan - 1));
        } else {
            const u =
                a == null
                    ? ye(r.type.schema).cell
                    : (i = r.nodeAt(e.map[d + a * e.width])) == null
                      ? void 0
                      : i.type;
            const f = u?.createAndFill();
            f && l.push(f);
        }
    }
    return (t.insert(s, ye(r.type.schema).row.create(null, l)), t);
}
function Jf(t, e) {
    if (!Ve(t)) return !1;
    if (e) {
        const n = nt(t);
        e(qf(t.tr, n, n.top));
    }
    return !0;
}
function Gf(t, e) {
    if (!Ve(t)) return !1;
    if (e) {
        const n = nt(t);
        e(qf(t.tr, n, n.bottom));
    }
    return !0;
}
function pw(t, { map: e, table: n, tableStart: r }, o) {
    let i = 0;
    for (let c = 0; c < o; c++) i += n.child(c).nodeSize;
    const s = i + n.child(o).nodeSize;
    const l = t.mapping.maps.length;
    t.delete(i + r, s + r);
    const a = new Set();
    for (let c = 0, d = o * e.width; c < e.width; c++, d++) {
        const u = e.map[d];
        if (!a.has(u)) {
            if ((a.add(u), o > 0 && u == e.map[d - e.width])) {
                const f = n.nodeAt(u).attrs;
                (t.setNodeMarkup(t.mapping.slice(l).map(u + r), null, {
                    ...f,
                    rowspan: f.rowspan - 1,
                }),
                    (c += f.colspan - 1));
            } else if (o < e.height && u == e.map[d + e.width]) {
                const f = n.nodeAt(u);
                const h = f.attrs;
                const p = f.type.create(
                    { ...h, rowspan: f.attrs.rowspan - 1 },
                    f.content,
                );
                const m = e.positionAt(o + 1, c, n);
                (t.insert(t.mapping.slice(l).map(r + m), p),
                    (c += h.colspan - 1));
            }
        }
    }
}
function Xf(t, e) {
    if (!Ve(t)) return !1;
    if (e) {
        const n = nt(t);
        const r = t.tr;
        if (n.top == 0 && n.bottom == n.map.height) return !1;
        for (let o = n.bottom - 1; pw(r, n, o), o != n.top; o--) {
            const i = n.tableStart ? r.doc.nodeAt(n.tableStart - 1) : r.doc;
            if (!i) throw RangeError("No table found");
            ((n.table = i), (n.map = ne.get(n.table)));
        }
        e(r);
    }
    return !0;
}
function Of(t) {
    const e = t.content;
    return (
        e.childCount == 1 &&
        e.child(0).isTextblock &&
        e.child(0).childCount == 0
    );
}
function mw({ width: t, height: e, map: n }, r) {
    let o = r.top * t + r.left;
    let i = o;
    let s = (r.bottom - 1) * t + r.left;
    let l = o + (r.right - r.left - 1);
    for (let a = r.top; a < r.bottom; a++) {
        if (
            (r.left > 0 && n[i] == n[i - 1]) ||
            (r.right < t && n[l] == n[l + 1])
        ) {
            return !0;
        }
        ((i += t), (l += t));
    }
    for (let a = r.left; a < r.right; a++) {
        if (
            (r.top > 0 && n[o] == n[o - t]) ||
            (r.bottom < e && n[s] == n[s + t])
        ) {
            return !0;
        }
        (o++, s++);
    }
    return !1;
}
function Yl(t, e) {
    const n = t.selection;
    if (!(n instanceof G) || n.$anchorCell.pos == n.$headCell.pos) return !1;
    const r = nt(t);
    const { map: o } = r;
    if (mw(o, r)) return !1;
    if (e) {
        const i = t.tr;
        const s = {};
        let l = C.empty;
        let a;
        let c;
        for (let d = r.top; d < r.bottom; d++) {
            for (let u = r.left; u < r.right; u++) {
                const f = o.map[d * o.width + u];
                const h = r.table.nodeAt(f);
                if (!(s[f] || !h)) {
                    if (((s[f] = !0), a == null)) ((a = f), (c = h));
                    else {
                        Of(h) || (l = l.append(h.content));
                        const p = i.mapping.map(f + r.tableStart);
                        i.delete(p, p + h.nodeSize);
                    }
                }
            }
        }
        if (a == null || c == null) return !0;
        if (
            (i.setNodeMarkup(a + r.tableStart, null, {
                ...Ff(
                    c.attrs,
                    c.attrs.colspan,
                    r.right - r.left - c.attrs.colspan,
                ),
                rowspan: r.bottom - r.top,
            }),
            l.size)
        ) {
            const d = a + 1 + c.content.size;
            const u = Of(c) ? a + 1 : d;
            i.replaceWith(u + r.tableStart, d + r.tableStart, l);
        }
        (i.setSelection(new G(i.doc.resolve(a + r.tableStart))), e(i));
    }
    return !0;
}
function Ql(t, e) {
    const n = ye(t.schema);
    return gw(({ node: r }) => n[r.type.spec.tableRole])(t, e);
}
function gw(t) {
    return (e, n) => {
        let r;
        const o = e.selection;
        let i;
        let s;
        if (o instanceof G) {
            if (o.$anchorCell.pos != o.$headCell.pos) return !1;
            ((i = o.$anchorCell.nodeAfter), (s = o.$anchorCell.pos));
        } else {
            if (((i = tw(o.$from)), !i)) return !1;
            s = (r = Tn(o.$from)) == null ? void 0 : r.pos;
        }
        if (
            i == null ||
            s == null ||
            (i.attrs.colspan == 1 && i.attrs.rowspan == 1)
        ) {
            return !1;
        }
        if (n) {
            let l = i.attrs;
            const a = [];
            const c = l.colwidth;
            (l.rowspan > 1 && (l = { ...l, rowspan: 1 }),
                l.colspan > 1 && (l = { ...l, colspan: 1 }));
            const d = nt(e);
            const u = e.tr;
            for (let h = 0; h < d.right - d.left; h++) {
                a.push(c ? { ...l, colwidth: c && c[h] ? [c[h]] : null } : l);
            }
            let f;
            for (let h = d.top; h < d.bottom; h++) {
                let p = d.map.positionAt(h, d.left, d.table);
                h == d.top && (p += i.nodeSize);
                for (let m = d.left, g = 0; m < d.right; m++, g++) {
                    (m == d.left && h == d.top) ||
                        u.insert(
                            (f = u.mapping.map(p + d.tableStart, 1)),
                            t({ node: i, row: h, col: m }).createAndFill(a[g]),
                        );
                }
            }
            (u.setNodeMarkup(s, t({ node: i, row: d.top, col: d.left }), a[0]),
                o instanceof G &&
                    u.setSelection(
                        new G(
                            u.doc.resolve(o.$anchorCell.pos),
                            f ? u.doc.resolve(f) : void 0,
                        ),
                    ),
                n(u));
        }
        return !0;
    };
}
function Yf(t, e) {
    return function (n, r) {
        if (!Ve(n)) return !1;
        const o = li(n);
        if (o.nodeAfter.attrs[t] === e) return !1;
        if (r) {
            const i = n.tr;
            (n.selection instanceof G
                ? n.selection.forEachCell((s, l) => {
                      s.attrs[t] !== e &&
                          i.setNodeMarkup(l, null, { ...s.attrs, [t]: e });
                  })
                : i.setNodeMarkup(o.pos, null, {
                      ...o.nodeAfter.attrs,
                      [t]: e,
                  }),
                r(i));
        }
        return !0;
    };
}
function yw(t) {
    return function (e, n) {
        if (!Ve(e)) return !1;
        if (n) {
            const r = ye(e.schema);
            const o = nt(e);
            const i = e.tr;
            const s = o.map.cellsInRect(
                t == "column"
                    ? {
                          left: o.left,
                          top: 0,
                          right: o.right,
                          bottom: o.map.height,
                      }
                    : t == "row"
                      ? {
                            left: 0,
                            top: o.top,
                            right: o.map.width,
                            bottom: o.bottom,
                        }
                      : o,
            );
            const l = s.map((a) => o.table.nodeAt(a));
            for (let a = 0; a < s.length; a++) {
                l[a].type == r.header_cell &&
                    i.setNodeMarkup(o.tableStart + s[a], r.cell, l[a].attrs);
            }
            if (i.steps.length == 0) {
                for (let a = 0; a < s.length; a++) {
                    i.setNodeMarkup(
                        o.tableStart + s[a],
                        r.header_cell,
                        l[a].attrs,
                    );
                }
            }
            n(i);
        }
        return !0;
    };
}
function Rf(t, e, n) {
    const r = e.map.cellsInRect({
        left: 0,
        top: 0,
        right: t == "row" ? e.map.width : 1,
        bottom: t == "column" ? e.map.height : 1,
    });
    for (let o = 0; o < r.length; o++) {
        const i = e.table.nodeAt(r[o]);
        if (i && i.type !== n.header_cell) return !1;
    }
    return !0;
}
function An(t, e) {
    return (
        (e = e || { useDeprecatedLogic: !1 }),
        e.useDeprecatedLogic
            ? yw(t)
            : function (n, r) {
                  if (!Ve(n)) return !1;
                  if (r) {
                      const o = ye(n.schema);
                      const i = nt(n);
                      const s = n.tr;
                      const l = Rf("row", i, o);
                      const a = Rf("column", i, o);
                      const d = (t === "column" ? l : t === "row" ? a : !1)
                          ? 1
                          : 0;
                      const u =
                          t == "column"
                              ? {
                                    left: 0,
                                    top: d,
                                    right: 1,
                                    bottom: i.map.height,
                                }
                              : t == "row"
                                ? {
                                      left: d,
                                      top: 0,
                                      right: i.map.width,
                                      bottom: 1,
                                  }
                                : i;
                      const f =
                          t == "column"
                              ? a
                                  ? o.cell
                                  : o.header_cell
                              : t == "row"
                                ? l
                                    ? o.cell
                                    : o.header_cell
                                : o.cell;
                      (i.map.cellsInRect(u).forEach((h) => {
                          const p = h + i.tableStart;
                          const m = s.doc.nodeAt(p);
                          m && s.setNodeMarkup(p, f, m.attrs);
                      }),
                          r(s));
                  }
                  return !0;
              }
    );
}
const Lv = An("row", { useDeprecatedLogic: !0 });
const zv = An("column", { useDeprecatedLogic: !0 });
const Qf = An("cell", { useDeprecatedLogic: !0 });
function bw(t, e) {
    if (e < 0) {
        const n = t.nodeBefore;
        if (n) return t.pos - n.nodeSize;
        for (let r = t.index(-1) - 1, o = t.before(); r >= 0; r--) {
            const i = t.node(-1).child(r);
            const s = i.lastChild;
            if (s) return o - 1 - s.nodeSize;
            o -= i.nodeSize;
        }
    } else {
        if (t.index() < t.parent.childCount - 1) {
            return t.pos + t.nodeAfter.nodeSize;
        }
        const n = t.node(-1);
        for (let r = t.indexAfter(-1), o = t.after(); r < n.childCount; r++) {
            const i = n.child(r);
            if (i.childCount) return o + 1;
            o += i.nodeSize;
        }
    }
    return null;
}
function Zl(t) {
    return function (e, n) {
        if (!Ve(e)) return !1;
        const r = bw(li(e), t);
        if (r == null) return !1;
        if (n) {
            const o = e.doc.resolve(r);
            n(e.tr.setSelection(R.between(o, rw(o))).scrollIntoView());
        }
        return !0;
    };
}
function Zf(t, e) {
    const n = t.selection.$anchor;
    for (let r = n.depth; r > 0; r--) {
        if (n.node(r).type.spec.tableRole == "table") {
            return (
                e && e(t.tr.delete(n.before(r), n.after(r)).scrollIntoView()),
                !0
            );
        }
    }
    return !1;
}
function ni(t, e) {
    const n = t.selection;
    if (!(n instanceof G)) return !1;
    if (e) {
        const r = t.tr;
        const o = ye(t.schema).cell.createAndFill().content;
        (n.forEachCell((i, s) => {
            i.content.eq(o) ||
                r.replace(
                    r.mapping.map(s + 1),
                    r.mapping.map(s + i.nodeSize - 1),
                    new E(o, 0, 0),
                );
        }),
            r.docChanged && e(r));
    }
    return !0;
}
function ww(t) {
    if (!t.size) return null;
    let { content: e, openStart: n, openEnd: r } = t;
    for (
        ;
        e.childCount == 1 &&
        ((n > 0 && r > 0) || e.child(0).type.spec.tableRole == "table");

    ) {
        (n--, r--, (e = e.child(0).content));
    }
    const o = e.child(0);
    const i = o.type.spec.tableRole;
    const s = o.type.schema;
    const l = [];
    if (i == "row") {
        for (let a = 0; a < e.childCount; a++) {
            let c = e.child(a).content;
            const d = a ? 0 : Math.max(0, n - 1);
            const u = a < e.childCount - 1 ? 0 : Math.max(0, r - 1);
            ((d || u) && (c = ql(ye(s).row, new E(c, d, u)).content),
                l.push(c));
        }
    } else if (i == "cell" || i == "header_cell") {
        l.push(n || r ? ql(ye(s).row, new E(e, n, r)).content : e);
    } else return null;
    return xw(s, l);
}
function xw(t, e) {
    const n = [];
    for (let o = 0; o < e.length; o++) {
        const i = e[o];
        for (let s = i.childCount - 1; s >= 0; s--) {
            const { rowspan: l, colspan: a } = i.child(s).attrs;
            for (let c = o; c < o + l; c++) n[c] = (n[c] || 0) + a;
        }
    }
    let r = 0;
    for (let o = 0; o < n.length; o++) r = Math.max(r, n[o]);
    for (let o = 0; o < n.length; o++) {
        if ((o >= e.length && e.push(C.empty), n[o] < r)) {
            const i = ye(t).cell.createAndFill();
            const s = [];
            for (let l = n[o]; l < r; l++) s.push(i);
            e[o] = e[o].append(C.from(s));
        }
    }
    return { height: e.length, width: r, rows: e };
}
function ql(t, e) {
    const n = t.createAndFill();
    return new St(n).replace(0, n.content.size, e).doc;
}
function kw({ width: t, height: e, rows: n }, r, o) {
    if (t != r) {
        const i = [];
        const s = [];
        for (let l = 0; l < n.length; l++) {
            const a = n[l];
            const c = [];
            for (let d = i[l] || 0, u = 0; d < r; u++) {
                let f = a.child(u % a.childCount);
                (d + f.attrs.colspan > r &&
                    (f = f.type.createChecked(
                        tn(f.attrs, f.attrs.colspan, d + f.attrs.colspan - r),
                        f.content,
                    )),
                    c.push(f),
                    (d += f.attrs.colspan));
                for (let h = 1; h < f.attrs.rowspan; h++) {
                    i[l + h] = (i[l + h] || 0) + f.attrs.colspan;
                }
            }
            s.push(C.from(c));
        }
        ((n = s), (t = r));
    }
    if (e != o) {
        const i = [];
        for (let s = 0, l = 0; s < o; s++, l++) {
            const a = [];
            const c = n[l % e];
            for (let d = 0; d < c.childCount; d++) {
                let u = c.child(d);
                (s + u.attrs.rowspan > o &&
                    (u = u.type.create(
                        {
                            ...u.attrs,
                            rowspan: Math.max(1, o - u.attrs.rowspan),
                        },
                        u.content,
                    )),
                    a.push(u));
            }
            i.push(C.from(a));
        }
        ((n = i), (e = o));
    }
    return { width: t, height: e, rows: n };
}
function Sw(t, e, n, r, o, i, s) {
    const l = t.doc.type.schema;
    const a = ye(l);
    let c;
    let d;
    if (o > e.width) {
        for (let u = 0, f = 0; u < e.height; u++) {
            const h = n.child(u);
            f += h.nodeSize;
            const p = [];
            let m;
            h.lastChild == null || h.lastChild.type == a.cell
                ? (m = c || (c = a.cell.createAndFill()))
                : (m = d || (d = a.header_cell.createAndFill()));
            for (let g = e.width; g < o; g++) p.push(m);
            t.insert(t.mapping.slice(s).map(f - 1 + r), p);
        }
    }
    if (i > e.height) {
        const u = [];
        for (
            let p = 0, m = (e.height - 1) * e.width;
            p < Math.max(e.width, o);
            p++
        ) {
            const g =
                p >= e.width
                    ? !1
                    : n.nodeAt(e.map[m + p]).type == a.header_cell;
            u.push(
                g
                    ? d || (d = a.header_cell.createAndFill())
                    : c || (c = a.cell.createAndFill()),
            );
        }
        const f = a.row.create(null, C.from(u));
        const h = [];
        for (let p = e.height; p < i; p++) h.push(f);
        t.insert(t.mapping.slice(s).map(r + n.nodeSize - 2), h);
    }
    return !!(c || d);
}
function Df(t, e, n, r, o, i, s, l) {
    if (s == 0 || s == e.height) return !1;
    let a = !1;
    for (let c = o; c < i; c++) {
        const d = s * e.width + c;
        const u = e.map[d];
        if (e.map[d - e.width] == u) {
            a = !0;
            const f = n.nodeAt(u);
            const { top: h, left: p } = e.findCell(u);
            (t.setNodeMarkup(t.mapping.slice(l).map(u + r), null, {
                ...f.attrs,
                rowspan: s - h,
            }),
                t.insert(
                    t.mapping.slice(l).map(e.positionAt(s, p, n)),
                    f.type.createAndFill({
                        ...f.attrs,
                        rowspan: h + f.attrs.rowspan - s,
                    }),
                ),
                (c += f.attrs.colspan - 1));
        }
    }
    return a;
}
function If(t, e, n, r, o, i, s, l) {
    if (s == 0 || s == e.width) return !1;
    let a = !1;
    for (let c = o; c < i; c++) {
        const d = c * e.width + s;
        const u = e.map[d];
        if (e.map[d - 1] == u) {
            a = !0;
            const f = n.nodeAt(u);
            const h = e.colCount(u);
            const p = t.mapping.slice(l).map(u + r);
            (t.setNodeMarkup(
                p,
                null,
                tn(f.attrs, s - h, f.attrs.colspan - (s - h)),
            ),
                t.insert(
                    p + f.nodeSize,
                    f.type.createAndFill(tn(f.attrs, 0, s - h)),
                ),
                (c += f.attrs.rowspan - 1));
        }
    }
    return a;
}
function Pf(t, e, n, r, o) {
    let i = n ? t.doc.nodeAt(n - 1) : t.doc;
    if (!i) throw new Error("No table found");
    let s = ne.get(i);
    const { top: l, left: a } = r;
    const c = a + o.width;
    const d = l + o.height;
    const u = t.tr;
    let f = 0;
    function h() {
        if (((i = n ? u.doc.nodeAt(n - 1) : u.doc), !i)) {
            throw new Error("No table found");
        }
        ((s = ne.get(i)), (f = u.mapping.maps.length));
    }
    (Sw(u, s, i, n, c, d, f) && h(),
        Df(u, s, i, n, a, c, l, f) && h(),
        Df(u, s, i, n, a, c, d, f) && h(),
        If(u, s, i, n, l, d, a, f) && h(),
        If(u, s, i, n, l, d, c, f) && h());
    for (let p = l; p < d; p++) {
        const m = s.positionAt(p, a, i);
        const g = s.positionAt(p, c, i);
        u.replace(
            u.mapping.slice(f).map(m + n),
            u.mapping.slice(f).map(g + n),
            new E(o.rows[p - l], 0, 0),
        );
    }
    (h(),
        u.setSelection(
            new G(
                u.doc.resolve(n + s.positionAt(l, a, i)),
                u.doc.resolve(n + s.positionAt(d - 1, c - 1, i)),
            ),
        ),
        e(u));
}
const Cw = Qn({
    ArrowLeft: ri("horiz", -1),
    ArrowRight: ri("horiz", 1),
    ArrowUp: ri("vert", -1),
    ArrowDown: ri("vert", 1),
    "Shift-ArrowLeft": oi("horiz", -1),
    "Shift-ArrowRight": oi("horiz", 1),
    "Shift-ArrowUp": oi("vert", -1),
    "Shift-ArrowDown": oi("vert", 1),
    Backspace: ni,
    "Mod-Backspace": ni,
    Delete: ni,
    "Mod-Delete": ni,
});
function ii(t, e, n) {
    return n.eq(t.selection)
        ? !1
        : (e && e(t.tr.setSelection(n).scrollIntoView()), !0);
}
function ri(t, e) {
    return (n, r, o) => {
        if (!o) return !1;
        const i = n.selection;
        if (i instanceof G) return ii(n, r, I.near(i.$headCell, e));
        if (t != "horiz" && !i.empty) return !1;
        const s = eh(o, t, e);
        if (s == null) return !1;
        if (t == "horiz") return ii(n, r, I.near(n.doc.resolve(i.head + e), e));
        {
            const l = n.doc.resolve(s);
            const a = $f(l, t, e);
            let c;
            return (
                a
                    ? (c = I.near(a, 1))
                    : e < 0
                      ? (c = I.near(n.doc.resolve(l.before(-1)), -1))
                      : (c = I.near(n.doc.resolve(l.after(-1)), 1)),
                ii(n, r, c)
            );
        }
    };
}
function oi(t, e) {
    return (n, r, o) => {
        if (!o) return !1;
        const i = n.selection;
        let s;
        if (i instanceof G) s = i;
        else {
            const a = eh(o, t, e);
            if (a == null) return !1;
            s = new G(n.doc.resolve(a));
        }
        const l = $f(s.$headCell, t, e);
        return l ? ii(n, r, new G(s.$anchorCell, l)) : !1;
    };
}
function vw(t, e) {
    const n = t.state.doc;
    const r = Tn(n.resolve(e));
    return r ? (t.dispatch(t.state.tr.setSelection(new G(r))), !0) : !1;
}
function Mw(t, e, n) {
    if (!Ve(t.state)) return !1;
    let r = ww(n);
    const o = t.state.selection;
    if (o instanceof G) {
        r ||
            (r = {
                width: 1,
                height: 1,
                rows: [C.from(ql(ye(t.state.schema).cell, n))],
            });
        const i = o.$anchorCell.node(-1);
        const s = o.$anchorCell.start(-1);
        const l = ne
            .get(i)
            .rectBetween(o.$anchorCell.pos - s, o.$headCell.pos - s);
        return (
            (r = kw(r, l.right - l.left, l.bottom - l.top)),
            Pf(t.state, t.dispatch, s, l, r),
            !0
        );
    } else if (r) {
        const i = li(t.state);
        const s = i.start(-1);
        return (
            Pf(
                t.state,
                t.dispatch,
                s,
                ne.get(i.node(-1)).findCell(i.pos - s),
                r,
            ),
            !0
        );
    } else return !1;
}
function Tw(t, e) {
    let n;
    if (e.ctrlKey || e.metaKey) return;
    const r = Lf(t, e.target);
    let o;
    if (e.shiftKey && t.state.selection instanceof G) {
        (i(t.state.selection.$anchorCell, e), e.preventDefault());
    } else if (
        e.shiftKey &&
        r &&
        (o = Tn(t.state.selection.$anchor)) != null &&
        ((n = Wl(t, e)) == null ? void 0 : n.pos) != o.pos
    ) {
        (i(o, e), e.preventDefault());
    } else if (!r) return;
    function i(a, c) {
        let d = Wl(t, c);
        const u = It.getState(t.state) == null;
        if (!d || !Gl(a, d)) {
            if (u) d = a;
            else return;
        }
        const f = new G(a, d);
        if (u || !t.state.selection.eq(f)) {
            const h = t.state.tr.setSelection(f);
            (u && h.setMeta(It, a.pos), t.dispatch(h));
        }
    }
    function s() {
        (t.root.removeEventListener("mouseup", s),
            t.root.removeEventListener("dragstart", s),
            t.root.removeEventListener("mousemove", l),
            It.getState(t.state) != null &&
                t.dispatch(t.state.tr.setMeta(It, -1)));
    }
    function l(a) {
        const c = a;
        const d = It.getState(t.state);
        let u;
        if (d != null) u = t.state.doc.resolve(d);
        else if (Lf(t, c.target) != r && ((u = Wl(t, e)), !u)) return s();
        u && i(u, c);
    }
    (t.root.addEventListener("mouseup", s),
        t.root.addEventListener("dragstart", s),
        t.root.addEventListener("mousemove", l));
}
function eh(t, e, n) {
    if (!(t.state.selection instanceof R)) return null;
    const { $head: r } = t.state.selection;
    for (let o = r.depth - 1; o >= 0; o--) {
        const i = r.node(o);
        if (
            (n < 0 ? r.index(o) : r.indexAfter(o)) != (n < 0 ? 0 : i.childCount)
        ) {
            return null;
        }
        if (
            i.type.spec.tableRole == "cell" ||
            i.type.spec.tableRole == "header_cell"
        ) {
            const l = r.before(o);
            const a =
                e == "vert"
                    ? n > 0
                        ? "down"
                        : "up"
                    : n > 0
                      ? "right"
                      : "left";
            return t.endOfTextblock(a) ? l : null;
        }
    }
    return null;
}
function Lf(t, e) {
    for (; e && e != t.dom; e = e.parentNode) {
        if (e.nodeName == "TD" || e.nodeName == "TH") return e;
    }
    return null;
}
function Wl(t, e) {
    const n = t.posAtCoords({ left: e.clientX, top: e.clientY });
    return n && n ? Tn(t.state.doc.resolve(n.pos)) : null;
}
const Aw = class {
    constructor(t, e) {
        ((this.node = t),
            (this.defaultCellMinWidth = e),
            (this.dom = document.createElement("div")),
            (this.dom.className = "tableWrapper"),
            (this.table = this.dom.appendChild(
                document.createElement("table"),
            )),
            this.table.style.setProperty("--default-cell-min-width", `${e}px`),
            (this.colgroup = this.table.appendChild(
                document.createElement("colgroup"),
            )),
            Jl(t, this.colgroup, this.table, e),
            (this.contentDOM = this.table.appendChild(
                document.createElement("tbody"),
            )));
    }

    update(t) {
        return t.type != this.node.type
            ? !1
            : ((this.node = t),
              Jl(t, this.colgroup, this.table, this.defaultCellMinWidth),
              !0);
    }

    ignoreMutation(t) {
        return (
            t.type == "attributes" &&
            (t.target == this.table || this.colgroup.contains(t.target))
        );
    }
};
function Jl(t, e, n, r, o, i) {
    let s;
    let l = 0;
    let a = !0;
    let c = e.firstChild;
    const d = t.firstChild;
    if (d) {
        for (let u = 0, f = 0; u < d.childCount; u++) {
            const { colspan: h, colwidth: p } = d.child(u).attrs;
            for (let m = 0; m < h; m++, f++) {
                const g = o == f ? i : p && p[m];
                const y = g ? g + "px" : "";
                if (((l += g || r), g || (a = !1), c)) {
                    (c.style.width != y && (c.style.width = y),
                        (c = c.nextSibling));
                } else {
                    const b = document.createElement("col");
                    ((b.style.width = y), e.appendChild(b));
                }
            }
        }
        for (; c; ) {
            const u = c.nextSibling;
            ((s = c.parentNode) == null || s.removeChild(c), (c = u));
        }
        a
            ? ((n.style.width = l + "px"), (n.style.minWidth = ""))
            : ((n.style.width = ""), (n.style.minWidth = l + "px"));
    }
}
const Ee = new H("tableColumnResizing");
function th({
    handleWidth: t = 5,
    cellMinWidth: e = 25,
    defaultCellMinWidth: n = 100,
    View: r = Aw,
    lastColumnResizable: o = !0,
} = {}) {
    const i = new L({
        key: Ee,
        state: {
            init(s, l) {
                let a, c;
                const d =
                    (c = (a = i.spec) == null ? void 0 : a.props) == null
                        ? void 0
                        : c.nodeViews;
                const u = ye(l.schema).table.name;
                return (
                    r && d && (d[u] = (f, h) => new r(f, n, h)),
                    new Ew(-1, !1)
                );
            },
            apply(s, l) {
                return l.apply(s);
            },
        },
        props: {
            attributes: (s) => {
                const l = Ee.getState(s);
                return l && l.activeHandle > -1
                    ? { class: "resize-cursor" }
                    : {};
            },
            handleDOMEvents: {
                mousemove: (s, l) => {
                    Nw(s, l, t, o);
                },
                mouseleave: (s) => {
                    Ow(s);
                },
                mousedown: (s, l) => {
                    Rw(s, l, e, n);
                },
            },
            decorations: (s) => {
                const l = Ee.getState(s);
                if (l && l.activeHandle > -1) return zw(s, l.activeHandle);
            },
            nodeViews: {},
        },
    });
    return i;
}
var Ew = class si {
    constructor(e, n) {
        ((this.activeHandle = e), (this.dragging = n));
    }

    apply(e) {
        const n = this;
        const r = e.getMeta(Ee);
        if (r && r.setHandle != null) return new si(r.setHandle, !1);
        if (r && r.setDragging !== void 0) {
            return new si(n.activeHandle, r.setDragging);
        }
        if (n.activeHandle > -1 && e.docChanged) {
            let o = e.mapping.map(n.activeHandle, -1);
            return (Ul(e.doc.resolve(o)) || (o = -1), new si(o, n.dragging));
        }
        return n;
    }
};
function Nw(t, e, n, r) {
    if (!t.editable) return;
    const o = Ee.getState(t.state);
    if (o && !o.dragging) {
        const i = Iw(e.target);
        let s = -1;
        if (i) {
            const { left: l, right: a } = i.getBoundingClientRect();
            e.clientX - l <= n
                ? (s = zf(t, e, "left", n))
                : a - e.clientX <= n && (s = zf(t, e, "right", n));
        }
        if (s != o.activeHandle) {
            if (!r && s !== -1) {
                const l = t.state.doc.resolve(s);
                const a = l.node(-1);
                const c = ne.get(a);
                const d = l.start(-1);
                if (
                    c.colCount(l.pos - d) + l.nodeAfter.attrs.colspan - 1 ==
                    c.width - 1
                ) {
                    return;
                }
            }
            nh(t, s);
        }
    }
}
function Ow(t) {
    if (!t.editable) return;
    const e = Ee.getState(t.state);
    e && e.activeHandle > -1 && !e.dragging && nh(t, -1);
}
function Rw(t, e, n, r) {
    let o;
    if (!t.editable) return !1;
    const i = (o = t.dom.ownerDocument.defaultView) != null ? o : window;
    const s = Ee.getState(t.state);
    if (!s || s.activeHandle == -1 || s.dragging) return !1;
    const l = t.state.doc.nodeAt(s.activeHandle);
    const a = Dw(t, s.activeHandle, l.attrs);
    t.dispatch(
        t.state.tr.setMeta(Ee, {
            setDragging: { startX: e.clientX, startWidth: a },
        }),
    );
    function c(u) {
        (i.removeEventListener("mouseup", c),
            i.removeEventListener("mousemove", d));
        const f = Ee.getState(t.state);
        f?.dragging &&
            (Pw(t, f.activeHandle, Bf(f.dragging, u, n)),
            t.dispatch(t.state.tr.setMeta(Ee, { setDragging: null })));
    }
    function d(u) {
        if (!u.which) return c(u);
        const f = Ee.getState(t.state);
        if (f && f.dragging) {
            const h = Bf(f.dragging, u, n);
            Hf(t, f.activeHandle, h, r);
        }
    }
    return (
        Hf(t, s.activeHandle, a, r),
        i.addEventListener("mouseup", c),
        i.addEventListener("mousemove", d),
        e.preventDefault(),
        !0
    );
}
function Dw(t, e, { colspan: n, colwidth: r }) {
    const o = r && r[r.length - 1];
    if (o) return o;
    const i = t.domAtPos(e);
    let l = i.node.childNodes[i.offset].offsetWidth;
    let a = n;
    if (r) for (let c = 0; c < n; c++) r[c] && ((l -= r[c]), a--);
    return l / a;
}
function Iw(t) {
    for (; t && t.nodeName != "TD" && t.nodeName != "TH"; ) {
        t =
            t.classList && t.classList.contains("ProseMirror")
                ? null
                : t.parentNode;
    }
    return t;
}
function zf(t, e, n, r) {
    const o = n == "right" ? -r : r;
    const i = t.posAtCoords({ left: e.clientX + o, top: e.clientY });
    if (!i) return -1;
    const { pos: s } = i;
    const l = Tn(t.state.doc.resolve(s));
    if (!l) return -1;
    if (n == "right") return l.pos;
    const a = ne.get(l.node(-1));
    const c = l.start(-1);
    const d = a.map.indexOf(l.pos - c);
    return d % a.width == 0 ? -1 : c + a.map[d - 1];
}
function Bf(t, e, n) {
    const r = e.clientX - t.startX;
    return Math.max(n, t.startWidth + r);
}
function nh(t, e) {
    t.dispatch(t.state.tr.setMeta(Ee, { setHandle: e }));
}
function Pw(t, e, n) {
    const r = t.state.doc.resolve(e);
    const o = r.node(-1);
    const i = ne.get(o);
    const s = r.start(-1);
    const l = i.colCount(r.pos - s) + r.nodeAfter.attrs.colspan - 1;
    const a = t.state.tr;
    for (let c = 0; c < i.height; c++) {
        const d = c * i.width + l;
        if (c && i.map[d] == i.map[d - i.width]) continue;
        const u = i.map[d];
        const f = o.nodeAt(u).attrs;
        const h = f.colspan == 1 ? 0 : l - i.colCount(u);
        if (f.colwidth && f.colwidth[h] == n) continue;
        const p = f.colwidth ? f.colwidth.slice() : Lw(f.colspan);
        ((p[h] = n), a.setNodeMarkup(s + u, null, { ...f, colwidth: p }));
    }
    a.docChanged && t.dispatch(a);
}
function Hf(t, e, n, r) {
    const o = t.state.doc.resolve(e);
    const i = o.node(-1);
    const s = o.start(-1);
    const l = ne.get(i).colCount(o.pos - s) + o.nodeAfter.attrs.colspan - 1;
    let a = t.domAtPos(o.start(-1)).node;
    for (; a && a.nodeName != "TABLE"; ) a = a.parentNode;
    a && Jl(i, a.firstChild, a, r, l, n);
}
function Lw(t) {
    return Array(t).fill(0);
}
function zw(t, e) {
    let n;
    const r = [];
    const o = t.doc.resolve(e);
    const i = o.node(-1);
    if (!i) return X.empty;
    const s = ne.get(i);
    const l = o.start(-1);
    const a = s.colCount(o.pos - l) + o.nodeAfter.attrs.colspan - 1;
    for (let c = 0; c < s.height; c++) {
        const d = a + c * s.width;
        if (
            (a == s.width - 1 || s.map[d] != s.map[d + 1]) &&
            (c == 0 || s.map[d] != s.map[d - s.width])
        ) {
            const u = s.map[d];
            const f = l + u + i.nodeAt(u).nodeSize - 1;
            const h = document.createElement("div");
            ((h.className = "column-resize-handle"),
                (n = Ee.getState(t)) != null &&
                    n.dragging &&
                    r.push(
                        Q.node(l + u, l + u + i.nodeAt(u).nodeSize, {
                            class: "column-resize-dragging",
                        }),
                    ),
                r.push(Q.widget(f, h)));
        }
    }
    return X.create(t.doc, r);
}
function rh({ allowTableNodeSelection: t = !1 } = {}) {
    return new L({
        key: It,
        state: {
            init() {
                return null;
            },
            apply(e, n) {
                const r = e.getMeta(It);
                if (r != null) return r == -1 ? null : r;
                if (n == null || !e.docChanged) return n;
                const { deleted: o, pos: i } = e.mapping.mapResult(n);
                return o ? null : i;
            },
        },
        props: {
            decorations: sw,
            handleDOMEvents: { mousedown: Tw },
            createSelectionBetween(e) {
                return It.getState(e.state) != null ? e.state.selection : null;
            },
            handleTripleClick: vw,
            handleKeyDown: Cw,
            handlePaste: Mw,
        },
        appendTransaction(e, n, r) {
            return cw(r, Xl(r, n), t);
        },
    });
}
const Bw = $.create({
    name: "tableCell",
    addOptions() {
        return { HTMLAttributes: {} };
    },
    content: "block+",
    addAttributes() {
        return {
            colspan: { default: 1 },
            rowspan: { default: 1 },
            colwidth: {
                default: null,
                parseHTML: (t) => {
                    let e, n;
                    const r = t.getAttribute("colwidth");
                    const o = r
                        ? r.split(",").map((i) => parseInt(i, 10))
                        : null;
                    if (!o) {
                        const i =
                            (e = t.closest("table")) == null
                                ? void 0
                                : e.querySelectorAll("colgroup > col");
                        const s = Array.from(
                            ((n = t.parentElement) == null
                                ? void 0
                                : n.children) || [],
                        ).indexOf(t);
                        if (s && s > -1 && i && i[s]) {
                            const l = i[s].getAttribute("width");
                            return l ? [parseInt(l, 10)] : null;
                        }
                    }
                    return o;
                },
            },
        };
    },
    tableRole: "cell",
    isolating: !0,
    parseHTML() {
        return [{ tag: "td" }];
    },
    renderHTML({ HTMLAttributes: t }) {
        return ["td", O(this.options.HTMLAttributes, t), 0];
    },
});
const Hw = $.create({
    name: "tableHeader",
    addOptions() {
        return { HTMLAttributes: {} };
    },
    content: "block+",
    addAttributes() {
        return {
            colspan: { default: 1 },
            rowspan: { default: 1 },
            colwidth: {
                default: null,
                parseHTML: (t) => {
                    const e = t.getAttribute("colwidth");
                    return e ? e.split(",").map((r) => parseInt(r, 10)) : null;
                },
            },
        };
    },
    tableRole: "header_cell",
    isolating: !0,
    parseHTML() {
        return [{ tag: "th" }];
    },
    renderHTML({ HTMLAttributes: t }) {
        return ["th", O(this.options.HTMLAttributes, t), 0];
    },
});
const $w = $.create({
    name: "tableRow",
    addOptions() {
        return { HTMLAttributes: {} };
    },
    content: "(tableCell | tableHeader)*",
    tableRole: "row",
    parseHTML() {
        return [{ tag: "tr" }];
    },
    renderHTML({ HTMLAttributes: t }) {
        return ["tr", O(this.options.HTMLAttributes, t), 0];
    },
});
function ea(t, e) {
    return e ? ["width", `${Math.max(e, t)}px`] : ["min-width", `${t}px`];
}
function oh(t, e, n, r, o, i) {
    let s;
    let l = 0;
    let a = !0;
    let c = e.firstChild;
    const d = t.firstChild;
    if (d !== null) {
        for (let u = 0, f = 0; u < d.childCount; u += 1) {
            const { colspan: h, colwidth: p } = d.child(u).attrs;
            for (let m = 0; m < h; m += 1, f += 1) {
                const g = o === f ? i : p && p[m];
                const y = g ? `${g}px` : "";
                if (((l += g || r), g || (a = !1), c)) {
                    if (c.style.width !== y) {
                        const [b, k] = ea(r, g);
                        c.style.setProperty(b, k);
                    }
                    c = c.nextSibling;
                } else {
                    const b = document.createElement("col");
                    const [k, v] = ea(r, g);
                    (b.style.setProperty(k, v), e.appendChild(b));
                }
            }
        }
    }
    for (; c; ) {
        const u = c.nextSibling;
        ((s = c.parentNode) == null || s.removeChild(c), (c = u));
    }
    a
        ? ((n.style.width = `${l}px`), (n.style.minWidth = ""))
        : ((n.style.width = ""), (n.style.minWidth = `${l}px`));
}
const Fw = class {
    constructor(t, e) {
        ((this.node = t),
            (this.cellMinWidth = e),
            (this.dom = document.createElement("div")),
            (this.dom.className = "tableWrapper"),
            (this.table = this.dom.appendChild(
                document.createElement("table"),
            )),
            (this.colgroup = this.table.appendChild(
                document.createElement("colgroup"),
            )),
            oh(t, this.colgroup, this.table, e),
            (this.contentDOM = this.table.appendChild(
                document.createElement("tbody"),
            )));
    }

    update(t) {
        return t.type !== this.node.type
            ? !1
            : ((this.node = t),
              oh(t, this.colgroup, this.table, this.cellMinWidth),
              !0);
    }

    ignoreMutation(t) {
        const e = t.target;
        const n = this.dom.contains(e);
        const r = this.contentDOM.contains(e);
        return !!(
            n &&
            !r &&
            (t.type === "attributes" ||
                t.type === "childList" ||
                t.type === "characterData")
        );
    }
};
function _w(t, e, n, r) {
    let o = 0;
    let i = !0;
    const s = [];
    const l = t.firstChild;
    if (!l) return {};
    for (let u = 0, f = 0; u < l.childCount; u += 1) {
        const { colspan: h, colwidth: p } = l.child(u).attrs;
        for (let m = 0; m < h; m += 1, f += 1) {
            const g = n === f ? r : p && p[m];
            ((o += g || e), g || (i = !1));
            const [y, b] = ea(e, g);
            s.push(["col", { style: `${y}: ${b}` }]);
        }
    }
    const a = i ? `${o}px` : "";
    const c = i ? "" : `${o}px`;
    return {
        colgroup: ["colgroup", {}, ...s],
        tableWidth: a,
        tableMinWidth: c,
    };
}
function ih(t, e) {
    return e ? t.createChecked(null, e) : t.createAndFill();
}
function Vw(t) {
    if (t.cached.tableNodeTypes) return t.cached.tableNodeTypes;
    const e = {};
    return (
        Object.keys(t.nodes).forEach((n) => {
            const r = t.nodes[n];
            r.spec.tableRole && (e[r.spec.tableRole] = r);
        }),
        (t.cached.tableNodeTypes = e),
        e
    );
}
function Ww(t, e, n, r, o) {
    const i = Vw(t);
    const s = [];
    const l = [];
    for (let c = 0; c < n; c += 1) {
        const d = ih(i.cell, o);
        if ((d && l.push(d), r)) {
            const u = ih(i.header_cell, o);
            u && s.push(u);
        }
    }
    const a = [];
    for (let c = 0; c < e; c += 1) {
        a.push(i.row.createChecked(null, r && c === 0 ? s : l));
    }
    return i.table.createChecked(null, a);
}
function jw(t) {
    return t instanceof G;
}
const ai = ({ editor: t }) => {
    const { selection: e } = t.state;
    if (!jw(e)) return !1;
    let n = 0;
    const r = Js(e.ranges[0].$from, (i) => i.type.name === "table");
    return (
        r?.node.descendants((i) => {
            if (i.type.name === "table") return !1;
            ["tableCell", "tableHeader"].includes(i.type.name) && (n += 1);
        }),
        n === e.ranges.length ? (t.commands.deleteTable(), !0) : !1
    );
};
const Kw = "";
function Uw(t) {
    return (t || "").replace(/\s+/g, " ").trim();
}
function qw(t, e, n = {}) {
    let r;
    const o = (r = n.cellLineSeparator) != null ? r : Kw;
    if (!t || !t.content || t.content.length === 0) return "";
    const i = [];
    t.content.forEach((p) => {
        const m = [];
        (p.content &&
            p.content.forEach((g) => {
                let y = "";
                g.content && Array.isArray(g.content) && g.content.length > 1
                    ? (y = g.content.map((x) => e.renderChildren(x)).join(o))
                    : (y = g.content ? e.renderChildren(g.content) : "");
                const b = Uw(y);
                const k = g.type === "tableHeader";
                m.push({ text: b, isHeader: k });
            }),
            i.push(m));
    });
    const s = i.reduce((p, m) => Math.max(p, m.length), 0);
    if (s === 0) return "";
    const l = new Array(s).fill(0);
    i.forEach((p) => {
        let m;
        for (let g = 0; g < s; g += 1) {
            const b = (((m = p[g]) == null ? void 0 : m.text) || "").length;
            (b > l[g] && (l[g] = b), l[g] < 3 && (l[g] = 3));
        }
    });
    const a = (p, m) => p + " ".repeat(Math.max(0, m - p.length));
    const c = i[0];
    const d = c.some((p) => p.isHeader);
    let u = `
`;
    const f = new Array(s)
        .fill(0)
        .map((p, m) => (d && c[m] && c[m].text) || "");
    return (
        (u += `| ${f.map((p, m) => a(p, l[m])).join(" | ")} |
`),
        (u += `| ${l.map((p) => "-".repeat(Math.max(3, p))).join(" | ")} |
`),
        (d ? i.slice(1) : i).forEach((p) => {
            u += `| ${new Array(s)
                .fill(0)
                .map((m, g) => a((p[g] && p[g].text) || "", l[g]))
                .join(" | ")} |
`;
        }),
        u
    );
}
const Jw = qw;
const Gw = $.create({
    name: "table",
    addOptions() {
        return {
            HTMLAttributes: {},
            resizable: !1,
            renderWrapper: !1,
            handleWidth: 5,
            cellMinWidth: 25,
            View: Fw,
            lastColumnResizable: !0,
            allowTableNodeSelection: !1,
        };
    },
    content: "tableRow+",
    tableRole: "table",
    isolating: !0,
    group: "block",
    parseHTML() {
        return [{ tag: "table" }];
    },
    renderHTML({ node: t, HTMLAttributes: e }) {
        const {
            colgroup: n,
            tableWidth: r,
            tableMinWidth: o,
        } = _w(t, this.options.cellMinWidth);
        const i = [
            "table",
            O(this.options.HTMLAttributes, e, {
                style: r ? `width: ${r}` : `min-width: ${o}`,
            }),
            n,
            ["tbody", 0],
        ];
        return this.options.renderWrapper
            ? ["div", { class: "tableWrapper" }, i]
            : i;
    },
    parseMarkdown: (t, e) => {
        const n = [];
        if (t.header) {
            const r = [];
            (t.header.forEach((o) => {
                r.push(
                    e.createNode("tableHeader", {}, [
                        { type: "paragraph", content: e.parseInline(o.tokens) },
                    ]),
                );
            }),
                n.push(e.createNode("tableRow", {}, r)));
        }
        return (
            t.rows &&
                t.rows.forEach((r) => {
                    const o = [];
                    (r.forEach((i) => {
                        o.push(
                            e.createNode("tableCell", {}, [
                                {
                                    type: "paragraph",
                                    content: e.parseInline(i.tokens),
                                },
                            ]),
                        );
                    }),
                        n.push(e.createNode("tableRow", {}, o)));
                }),
            e.createNode("table", void 0, n)
        );
    },
    renderMarkdown: (t, e) => Jw(t, e),
    addCommands() {
        return {
            insertTable:
                ({ rows: t = 3, cols: e = 3, withHeaderRow: n = !0 } = {}) =>
                ({ tr: r, dispatch: o, editor: i }) => {
                    const s = Ww(i.schema, t, e, n);
                    if (o) {
                        const l = r.selection.from + 1;
                        r.replaceSelectionWith(s)
                            .scrollIntoView()
                            .setSelection(R.near(r.doc.resolve(l)));
                    }
                    return !0;
                },
            addColumnBefore:
                () =>
                ({ state: t, dispatch: e }) =>
                    jf(t, e),
            addColumnAfter:
                () =>
                ({ state: t, dispatch: e }) =>
                    Kf(t, e),
            deleteColumn:
                () =>
                ({ state: t, dispatch: e }) =>
                    Uf(t, e),
            addRowBefore:
                () =>
                ({ state: t, dispatch: e }) =>
                    Jf(t, e),
            addRowAfter:
                () =>
                ({ state: t, dispatch: e }) =>
                    Gf(t, e),
            deleteRow:
                () =>
                ({ state: t, dispatch: e }) =>
                    Xf(t, e),
            deleteTable:
                () =>
                ({ state: t, dispatch: e }) =>
                    Zf(t, e),
            mergeCells:
                () =>
                ({ state: t, dispatch: e }) =>
                    Yl(t, e),
            splitCell:
                () =>
                ({ state: t, dispatch: e }) =>
                    Ql(t, e),
            toggleHeaderColumn:
                () =>
                ({ state: t, dispatch: e }) =>
                    An("column")(t, e),
            toggleHeaderRow:
                () =>
                ({ state: t, dispatch: e }) =>
                    An("row")(t, e),
            toggleHeaderCell:
                () =>
                ({ state: t, dispatch: e }) =>
                    Qf(t, e),
            mergeOrSplit:
                () =>
                ({ state: t, dispatch: e }) =>
                    Yl(t, e) ? !0 : Ql(t, e),
            setCellAttribute:
                (t, e) =>
                ({ state: n, dispatch: r }) =>
                    Yf(t, e)(n, r),
            goToNextCell:
                () =>
                ({ state: t, dispatch: e }) =>
                    Zl(1)(t, e),
            goToPreviousCell:
                () =>
                ({ state: t, dispatch: e }) =>
                    Zl(-1)(t, e),
            fixTables:
                () =>
                ({ state: t, dispatch: e }) => (e && Xl(t), !0),
            setCellSelection:
                (t) =>
                ({ tr: e, dispatch: n }) => {
                    if (n) {
                        const r = G.create(e.doc, t.anchorCell, t.headCell);
                        e.setSelection(r);
                    }
                    return !0;
                },
        };
    },
    addKeyboardShortcuts() {
        return {
            Tab: () =>
                this.editor.commands.goToNextCell()
                    ? !0
                    : this.editor.can().addRowAfter()
                      ? this.editor.chain().addRowAfter().goToNextCell().run()
                      : !1,
            "Shift-Tab": () => this.editor.commands.goToPreviousCell(),
            Backspace: ai,
            "Mod-Backspace": ai,
            Delete: ai,
            "Mod-Delete": ai,
        };
    },
    addProseMirrorPlugins() {
        return [
            ...(this.options.resizable && this.editor.isEditable
                ? [
                      th({
                          handleWidth: this.options.handleWidth,
                          cellMinWidth: this.options.cellMinWidth,
                          defaultCellMinWidth: this.options.cellMinWidth,
                          View: this.options.View,
                          lastColumnResizable: this.options.lastColumnResizable,
                      }),
                  ]
                : []),
            rh({
                allowTableNodeSelection: this.options.allowTableNodeSelection,
            }),
        ];
    },
    extendNodeSchema(t) {
        const e = { name: t.name, options: t.options, storage: t.storage };
        return { tableRole: J(z(t, "tableRole", e)) };
    },
});
const sh = j.create({
    name: "tableKit",
    addExtensions() {
        const t = [];
        return (
            this.options.table !== !1 &&
                t.push(Gw.configure(this.options.table)),
            this.options.tableCell !== !1 &&
                t.push(Bw.configure(this.options.tableCell)),
            this.options.tableHeader !== !1 &&
                t.push(Hw.configure(this.options.tableHeader)),
            this.options.tableRow !== !1 &&
                t.push($w.configure(this.options.tableRow)),
            t
        );
    },
});
const Xw = $.create({
    name: "text",
    group: "inline",
    parseMarkdown: (t) => ({ type: "text", text: t.text || "" }),
    renderMarkdown: (t) => t.text || "",
});
const lh = Xw;
const Yw = j.create({
    name: "textAlign",
    addOptions() {
        return {
            types: [],
            alignments: ["left", "center", "right", "justify"],
            defaultAlignment: null,
        };
    },
    addGlobalAttributes() {
        return [
            {
                types: this.options.types,
                attributes: {
                    textAlign: {
                        default: this.options.defaultAlignment,
                        parseHTML: (t) => {
                            const e = t.style.textAlign;
                            return this.options.alignments.includes(e)
                                ? e
                                : this.options.defaultAlignment;
                        },
                        renderHTML: (t) =>
                            t.textAlign
                                ? { style: `text-align: ${t.textAlign}` }
                                : {},
                    },
                },
            },
        ];
    },
    addCommands() {
        return {
            setTextAlign:
                (t) =>
                ({ commands: e }) =>
                    this.options.alignments.includes(t)
                        ? this.options.types
                              .map((n) =>
                                  e.updateAttributes(n, { textAlign: t }),
                              )
                              .every((n) => n)
                        : !1,
            unsetTextAlign:
                () =>
                ({ commands: t }) =>
                    this.options.types
                        .map((e) => t.resetAttributes(e, "textAlign"))
                        .every((e) => e),
            toggleTextAlign:
                (t) =>
                ({ editor: e, commands: n }) =>
                    this.options.alignments.includes(t)
                        ? e.isActive({ textAlign: t })
                            ? n.unsetTextAlign()
                            : n.setTextAlign(t)
                        : !1,
        };
    },
    addKeyboardShortcuts() {
        return {
            "Mod-Shift-l": () => this.editor.commands.setTextAlign("left"),
            "Mod-Shift-e": () => this.editor.commands.setTextAlign("center"),
            "Mod-Shift-r": () => this.editor.commands.setTextAlign("right"),
            "Mod-Shift-j": () => this.editor.commands.setTextAlign("justify"),
        };
    },
});
const ah = Yw;
const Qw = Z.create({
    name: "underline",
    addOptions() {
        return { HTMLAttributes: {} };
    },
    parseHTML() {
        return [
            { tag: "u" },
            {
                style: "text-decoration",
                consuming: !1,
                getAttrs: (t) => (t.includes("underline") ? {} : !1),
            },
        ];
    },
    renderHTML({ HTMLAttributes: t }) {
        return ["u", O(this.options.HTMLAttributes, t), 0];
    },
    parseMarkdown(t, e) {
        return e.applyMark(
            this.name || "underline",
            e.parseInline(t.tokens || []),
        );
    },
    renderMarkdown(t, e) {
        return `++${e.renderChildren(t)}++`;
    },
    markdownTokenizer: {
        name: "underline",
        level: "inline",
        start(t) {
            return t.indexOf("++");
        },
        tokenize(t, e, n) {
            const o = /^(\+\+)([\s\S]+?)(\+\+)/.exec(t);
            if (!o) return;
            const i = o[2].trim();
            return {
                type: "underline",
                raw: o[0],
                text: i,
                tokens: n.inlineTokens(i),
            };
        },
    },
    addCommands() {
        return {
            setUnderline:
                () =>
                ({ commands: t }) =>
                    t.setMark(this.name),
            toggleUnderline:
                () =>
                ({ commands: t }) =>
                    t.toggleMark(this.name),
            unsetUnderline:
                () =>
                ({ commands: t }) =>
                    t.unsetMark(this.name),
        };
    },
    addKeyboardShortcuts() {
        return {
            "Mod-u": () => this.editor.commands.toggleUnderline(),
            "Mod-U": () => this.editor.commands.toggleUnderline(),
        };
    },
});
const ch = Qw;
const ta = ["top", "right", "bottom", "left"];
const dh = ["start", "end"];
const na = ta.reduce(
    (t, e) => t.concat(e, e + "-" + dh[0], e + "-" + dh[1]),
    [],
);
const He = Math.min;
const fe = Math.max;
const br = Math.round;
const We = (t) => ({ x: t, y: t });
const Zw = { left: "right", right: "left", bottom: "top", top: "bottom" };
const ex = { start: "end", end: "start" };
function ci(t, e, n) {
    return fe(t, He(e, n));
}
function rt(t, e) {
    return typeof t === "function" ? t(e) : t;
}
function Ne(t) {
    return t.split("-")[0];
}
function $e(t) {
    return t.split("-")[1];
}
function ra(t) {
    return t === "x" ? "y" : "x";
}
function di(t) {
    return t === "y" ? "height" : "width";
}
const tx = new Set(["top", "bottom"]);
function je(t) {
    return tx.has(Ne(t)) ? "y" : "x";
}
function ui(t) {
    return ra(je(t));
}
function oa(t, e, n) {
    n === void 0 && (n = !1);
    const r = $e(t);
    const o = ui(t);
    const i = di(o);
    let s =
        o === "x"
            ? r === (n ? "end" : "start")
                ? "right"
                : "left"
            : r === "start"
              ? "bottom"
              : "top";
    return (e.reference[i] > e.floating[i] && (s = yr(s)), [s, yr(s)]);
}
function hh(t) {
    const e = yr(t);
    return [gr(t), e, gr(e)];
}
function gr(t) {
    return t.replace(/start|end/g, (e) => ex[e]);
}
const uh = ["left", "right"];
const fh = ["right", "left"];
const nx = ["top", "bottom"];
const rx = ["bottom", "top"];
function ox(t, e, n) {
    switch (t) {
        case "top":
        case "bottom":
            return n ? (e ? fh : uh) : e ? uh : fh;
        case "left":
        case "right":
            return e ? nx : rx;
        default:
            return [];
    }
}
function ph(t, e, n, r) {
    const o = $e(t);
    let i = ox(Ne(t), n === "start", r);
    return (
        o && ((i = i.map((s) => s + "-" + o)), e && (i = i.concat(i.map(gr)))),
        i
    );
}
function yr(t) {
    return t.replace(/left|right|bottom|top/g, (e) => Zw[e]);
}
function ix(t) {
    return { top: 0, right: 0, bottom: 0, left: 0, ...t };
}
function fi(t) {
    return typeof t !== "number"
        ? ix(t)
        : { top: t, right: t, bottom: t, left: t };
}
function xt(t) {
    const { x: e, y: n, width: r, height: o } = t;
    return {
        width: r,
        height: o,
        top: n,
        left: e,
        right: e + r,
        bottom: n + o,
        x: e,
        y: n,
    };
}
function mh(t, e, n) {
    const { reference: r, floating: o } = t;
    const i = je(e);
    const s = ui(e);
    const l = di(s);
    const a = Ne(e);
    const c = i === "y";
    const d = r.x + r.width / 2 - o.width / 2;
    const u = r.y + r.height / 2 - o.height / 2;
    const f = r[l] / 2 - o[l] / 2;
    let h;
    switch (a) {
        case "top":
            h = { x: d, y: r.y - o.height };
            break;
        case "bottom":
            h = { x: d, y: r.y + r.height };
            break;
        case "right":
            h = { x: r.x + r.width, y: u };
            break;
        case "left":
            h = { x: r.x - o.width, y: u };
            break;
        default:
            h = { x: r.x, y: r.y };
    }
    switch ($e(e)) {
        case "start":
            h[s] -= f * (n && c ? -1 : 1);
            break;
        case "end":
            h[s] += f * (n && c ? -1 : 1);
            break;
    }
    return h;
}
const bh = async (t, e, n) => {
    const {
        placement: r = "bottom",
        strategy: o = "absolute",
        middleware: i = [],
        platform: s,
    } = n;
    const l = i.filter(Boolean);
    const a = await (s.isRTL == null ? void 0 : s.isRTL(e));
    let c = await s.getElementRects({ reference: t, floating: e, strategy: o });
    let { x: d, y: u } = mh(c, r, a);
    let f = r;
    let h = {};
    let p = 0;
    for (let m = 0; m < l.length; m++) {
        const { name: g, fn: y } = l[m];
        const {
            x: b,
            y: k,
            data: v,
            reset: x,
        } = await y({
            x: d,
            y: u,
            initialPlacement: r,
            placement: f,
            strategy: o,
            middlewareData: h,
            rects: c,
            platform: s,
            elements: { reference: t, floating: e },
        });
        ((d = b ?? d),
            (u = k ?? u),
            (h = { ...h, [g]: { ...h[g], ...v } }),
            x &&
                p <= 50 &&
                (p++,
                typeof x === "object" &&
                    (x.placement && (f = x.placement),
                    x.rects &&
                        (c =
                            x.rects === !0
                                ? await s.getElementRects({
                                      reference: t,
                                      floating: e,
                                      strategy: o,
                                  })
                                : x.rects),
                    ({ x: d, y: u } = mh(c, f, a))),
                (m = -1)));
    }
    return { x: d, y: u, placement: f, strategy: o, middlewareData: h };
};
async function nn(t, e) {
    let n;
    e === void 0 && (e = {});
    const { x: r, y: o, platform: i, rects: s, elements: l, strategy: a } = t;
    const {
        boundary: c = "clippingAncestors",
        rootBoundary: d = "viewport",
        elementContext: u = "floating",
        altBoundary: f = !1,
        padding: h = 0,
    } = rt(e, t);
    const p = fi(h);
    const g = l[f ? (u === "floating" ? "reference" : "floating") : u];
    const y = xt(
        await i.getClippingRect({
            element:
                (n = await (i.isElement == null ? void 0 : i.isElement(g))) ==
                    null || n
                    ? g
                    : g.contextElement ||
                      (await (i.getDocumentElement == null
                          ? void 0
                          : i.getDocumentElement(l.floating))),
            boundary: c,
            rootBoundary: d,
            strategy: a,
        }),
    );
    const b =
        u === "floating"
            ? { x: r, y: o, width: s.floating.width, height: s.floating.height }
            : s.reference;
    const k = await (i.getOffsetParent == null
        ? void 0
        : i.getOffsetParent(l.floating));
    const v = (await (i.isElement == null ? void 0 : i.isElement(k)))
        ? (await (i.getScale == null ? void 0 : i.getScale(k))) || {
              x: 1,
              y: 1,
          }
        : { x: 1, y: 1 };
    const x = xt(
        i.convertOffsetParentRelativeRectToViewportRelativeRect
            ? await i.convertOffsetParentRelativeRectToViewportRelativeRect({
                  elements: l,
                  rect: b,
                  offsetParent: k,
                  strategy: a,
              })
            : b,
    );
    return {
        top: (y.top - x.top + p.top) / v.y,
        bottom: (x.bottom - y.bottom + p.bottom) / v.y,
        left: (y.left - x.left + p.left) / v.x,
        right: (x.right - y.right + p.right) / v.x,
    };
}
const wh = (t) => ({
    name: "arrow",
    options: t,
    async fn(e) {
        const {
            x: n,
            y: r,
            placement: o,
            rects: i,
            platform: s,
            elements: l,
            middlewareData: a,
        } = e;
        const { element: c, padding: d = 0 } = rt(t, e) || {};
        if (c == null) return {};
        const u = fi(d);
        const f = { x: n, y: r };
        const h = ui(o);
        const p = di(h);
        const m = await s.getDimensions(c);
        const g = h === "y";
        const y = g ? "top" : "left";
        const b = g ? "bottom" : "right";
        const k = g ? "clientHeight" : "clientWidth";
        const v = i.reference[p] + i.reference[h] - f[h] - i.floating[p];
        const x = f[h] - i.reference[h];
        const S = await (s.getOffsetParent == null
            ? void 0
            : s.getOffsetParent(c));
        let w = S ? S[k] : 0;
        (!w || !(await (s.isElement == null ? void 0 : s.isElement(S)))) &&
            (w = l.floating[k] || i.floating[p]);
        const N = v / 2 - x / 2;
        const D = w / 2 - m[p] / 2 - 1;
        const M = He(u[y], D);
        const B = He(u[b], D);
        const F = M;
        const K = w - m[p] - B;
        const _ = w / 2 - m[p] / 2 + N;
        const T = ci(F, _, K);
        const W =
            !a.arrow &&
            $e(o) != null &&
            _ !== T &&
            i.reference[p] / 2 - (_ < F ? M : B) - m[p] / 2 < 0;
        const U = W ? (_ < F ? _ - F : _ - K) : 0;
        return {
            [h]: f[h] + U,
            data: {
                [h]: T,
                centerOffset: _ - T - U,
                ...(W && { alignmentOffset: U }),
            },
            reset: W,
        };
    },
});
function sx(t, e, n) {
    return (
        t
            ? [...n.filter((o) => $e(o) === t), ...n.filter((o) => $e(o) !== t)]
            : n.filter((o) => Ne(o) === o)
    ).filter((o) => (t ? $e(o) === t || (e ? gr(o) !== o : !1) : !0));
}
const xh = function (t) {
    return (
        t === void 0 && (t = {}),
        {
            name: "autoPlacement",
            options: t,
            async fn(e) {
                let n, r, o;
                const {
                    rects: i,
                    middlewareData: s,
                    placement: l,
                    platform: a,
                    elements: c,
                } = e;
                const {
                    crossAxis: d = !1,
                    alignment: u,
                    allowedPlacements: f = na,
                    autoAlignment: h = !0,
                    ...p
                } = rt(t, e);
                const m = u !== void 0 || f === na ? sx(u || null, h, f) : f;
                const g = await nn(e, p);
                const y =
                    ((n = s.autoPlacement) == null ? void 0 : n.index) || 0;
                const b = m[y];
                if (b == null) return {};
                const k = oa(
                    b,
                    i,
                    await (a.isRTL == null ? void 0 : a.isRTL(c.floating)),
                );
                if (l !== b) return { reset: { placement: m[0] } };
                const v = [g[Ne(b)], g[k[0]], g[k[1]]];
                const x = [
                    ...(((r = s.autoPlacement) == null
                        ? void 0
                        : r.overflows) || []),
                    { placement: b, overflows: v },
                ];
                const S = m[y + 1];
                if (S) {
                    return {
                        data: { index: y + 1, overflows: x },
                        reset: { placement: S },
                    };
                }
                const w = x
                    .map((M) => {
                        const B = $e(M.placement);
                        return [
                            M.placement,
                            B && d
                                ? M.overflows
                                      .slice(0, 2)
                                      .reduce((F, K) => F + K, 0)
                                : M.overflows[0],
                            M.overflows,
                        ];
                    })
                    .sort((M, B) => M[1] - B[1]);
                const D =
                    ((o = w.filter((M) =>
                        M[2].slice(0, $e(M[0]) ? 2 : 3).every((B) => B <= 0),
                    )[0]) == null
                        ? void 0
                        : o[0]) || w[0][0];
                return D !== l
                    ? {
                          data: { index: y + 1, overflows: x },
                          reset: { placement: D },
                      }
                    : {};
            },
        }
    );
};
const kh = function (t) {
    return (
        t === void 0 && (t = {}),
        {
            name: "flip",
            options: t,
            async fn(e) {
                let n, r;
                const {
                    placement: o,
                    middlewareData: i,
                    rects: s,
                    initialPlacement: l,
                    platform: a,
                    elements: c,
                } = e;
                const {
                    mainAxis: d = !0,
                    crossAxis: u = !0,
                    fallbackPlacements: f,
                    fallbackStrategy: h = "bestFit",
                    fallbackAxisSideDirection: p = "none",
                    flipAlignment: m = !0,
                    ...g
                } = rt(t, e);
                if ((n = i.arrow) != null && n.alignmentOffset) return {};
                const y = Ne(o);
                const b = je(l);
                const k = Ne(l) === l;
                const v = await (a.isRTL == null
                    ? void 0
                    : a.isRTL(c.floating));
                const x = f || (k || !m ? [yr(l)] : hh(l));
                const S = p !== "none";
                !f && S && x.push(...ph(l, m, p, v));
                const w = [l, ...x];
                const N = await nn(e, g);
                const D = [];
                let M = ((r = i.flip) == null ? void 0 : r.overflows) || [];
                if ((d && D.push(N[y]), u)) {
                    const _ = oa(o, s, v);
                    D.push(N[_[0]], N[_[1]]);
                }
                if (
                    ((M = [...M, { placement: o, overflows: D }]),
                    !D.every((_) => _ <= 0))
                ) {
                    let B, F;
                    const _ =
                        (((B = i.flip) == null ? void 0 : B.index) || 0) + 1;
                    const T = w[_];
                    if (
                        T &&
                        (!(u === "alignment" ? b !== je(T) : !1) ||
                            M.every((ie) =>
                                je(ie.placement) === b
                                    ? ie.overflows[0] > 0
                                    : !0,
                            ))
                    ) {
                        return {
                            data: { index: _, overflows: M },
                            reset: { placement: T },
                        };
                    }
                    let W =
                        (F = M.filter((U) => U.overflows[0] <= 0).sort(
                            (U, ie) => U.overflows[1] - ie.overflows[1],
                        )[0]) == null
                            ? void 0
                            : F.placement;
                    if (!W) {
                        switch (h) {
                            case "bestFit": {
                                let K;
                                const U =
                                    (K = M.filter((ie) => {
                                        if (S) {
                                            const Te = je(ie.placement);
                                            return Te === b || Te === "y";
                                        }
                                        return !0;
                                    })
                                        .map((ie) => [
                                            ie.placement,
                                            ie.overflows
                                                .filter((Te) => Te > 0)
                                                .reduce((Te, Pt) => Te + Pt, 0),
                                        ])
                                        .sort((ie, Te) => ie[1] - Te[1])[0]) ==
                                    null
                                        ? void 0
                                        : K[0];
                                U && (W = U);
                                break;
                            }
                            case "initialPlacement":
                                W = l;
                                break;
                        }
                    }
                    if (o !== W) return { reset: { placement: W } };
                }
                return {};
            },
        }
    );
};
function gh(t, e) {
    return {
        top: t.top - e.height,
        right: t.right - e.width,
        bottom: t.bottom - e.height,
        left: t.left - e.width,
    };
}
function yh(t) {
    return ta.some((e) => t[e] >= 0);
}
const Sh = function (t) {
    return (
        t === void 0 && (t = {}),
        {
            name: "hide",
            options: t,
            async fn(e) {
                const { rects: n } = e;
                const { strategy: r = "referenceHidden", ...o } = rt(t, e);
                switch (r) {
                    case "referenceHidden": {
                        const i = await nn(e, {
                            ...o,
                            elementContext: "reference",
                        });
                        const s = gh(i, n.reference);
                        return {
                            data: {
                                referenceHiddenOffsets: s,
                                referenceHidden: yh(s),
                            },
                        };
                    }
                    case "escaped": {
                        const i = await nn(e, { ...o, altBoundary: !0 });
                        const s = gh(i, n.floating);
                        return { data: { escapedOffsets: s, escaped: yh(s) } };
                    }
                    default:
                        return {};
                }
            },
        }
    );
};
function Ch(t) {
    const e = He(...t.map((i) => i.left));
    const n = He(...t.map((i) => i.top));
    const r = fe(...t.map((i) => i.right));
    const o = fe(...t.map((i) => i.bottom));
    return { x: e, y: n, width: r - e, height: o - n };
}
function lx(t) {
    const e = t.slice().sort((o, i) => o.y - i.y);
    const n = [];
    let r = null;
    for (let o = 0; o < e.length; o++) {
        const i = e[o];
        (!r || i.y - r.y > r.height / 2 ? n.push([i]) : n[n.length - 1].push(i),
            (r = i));
    }
    return n.map((o) => xt(Ch(o)));
}
const vh = function (t) {
    return (
        t === void 0 && (t = {}),
        {
            name: "inline",
            options: t,
            async fn(e) {
                const {
                    placement: n,
                    elements: r,
                    rects: o,
                    platform: i,
                    strategy: s,
                } = e;
                const { padding: l = 2, x: a, y: c } = rt(t, e);
                const d = Array.from(
                    (await (i.getClientRects == null
                        ? void 0
                        : i.getClientRects(r.reference))) || [],
                );
                const u = lx(d);
                const f = xt(Ch(d));
                const h = fi(l);
                function p() {
                    if (
                        u.length === 2 &&
                        u[0].left > u[1].right &&
                        a != null &&
                        c != null
                    ) {
                        return (
                            u.find(
                                (g) =>
                                    a > g.left - h.left &&
                                    a < g.right + h.right &&
                                    c > g.top - h.top &&
                                    c < g.bottom + h.bottom,
                            ) || f
                        );
                    }
                    if (u.length >= 2) {
                        if (je(n) === "y") {
                            const M = u[0];
                            const B = u[u.length - 1];
                            const F = Ne(n) === "top";
                            const K = M.top;
                            const _ = B.bottom;
                            const T = F ? M.left : B.left;
                            const W = F ? M.right : B.right;
                            const U = W - T;
                            const ie = _ - K;
                            return {
                                top: K,
                                bottom: _,
                                left: T,
                                right: W,
                                width: U,
                                height: ie,
                                x: T,
                                y: K,
                            };
                        }
                        const g = Ne(n) === "left";
                        const y = fe(...u.map((M) => M.right));
                        const b = He(...u.map((M) => M.left));
                        const k = u.filter((M) =>
                            g ? M.left === b : M.right === y,
                        );
                        const v = k[0].top;
                        const x = k[k.length - 1].bottom;
                        const S = b;
                        const w = y;
                        const N = w - S;
                        const D = x - v;
                        return {
                            top: v,
                            bottom: x,
                            left: S,
                            right: w,
                            width: N,
                            height: D,
                            x: S,
                            y: v,
                        };
                    }
                    return f;
                }
                const m = await i.getElementRects({
                    reference: { getBoundingClientRect: p },
                    floating: r.floating,
                    strategy: s,
                });
                return o.reference.x !== m.reference.x ||
                    o.reference.y !== m.reference.y ||
                    o.reference.width !== m.reference.width ||
                    o.reference.height !== m.reference.height
                    ? { reset: { rects: m } }
                    : {};
            },
        }
    );
};
const ax = new Set(["left", "top"]);
async function cx(t, e) {
    const { placement: n, platform: r, elements: o } = t;
    const i = await (r.isRTL == null ? void 0 : r.isRTL(o.floating));
    const s = Ne(n);
    const l = $e(n);
    const a = je(n) === "y";
    const c = ax.has(s) ? -1 : 1;
    const d = i && a ? -1 : 1;
    const u = rt(e, t);
    let {
        mainAxis: f,
        crossAxis: h,
        alignmentAxis: p,
    } = typeof u === "number"
        ? { mainAxis: u, crossAxis: 0, alignmentAxis: null }
        : {
              mainAxis: u.mainAxis || 0,
              crossAxis: u.crossAxis || 0,
              alignmentAxis: u.alignmentAxis,
          };
    return (
        l && typeof p === "number" && (h = l === "end" ? p * -1 : p),
        a ? { x: h * d, y: f * c } : { x: f * c, y: h * d }
    );
}
const Mh = function (t) {
    return (
        t === void 0 && (t = 0),
        {
            name: "offset",
            options: t,
            async fn(e) {
                let n, r;
                const { x: o, y: i, placement: s, middlewareData: l } = e;
                const a = await cx(e, t);
                return s === ((n = l.offset) == null ? void 0 : n.placement) &&
                    (r = l.arrow) != null &&
                    r.alignmentOffset
                    ? {}
                    : { x: o + a.x, y: i + a.y, data: { ...a, placement: s } };
            },
        }
    );
};
const Th = function (t) {
    return (
        t === void 0 && (t = {}),
        {
            name: "shift",
            options: t,
            async fn(e) {
                const { x: n, y: r, placement: o } = e;
                const {
                    mainAxis: i = !0,
                    crossAxis: s = !1,
                    limiter: l = {
                        fn: (g) => {
                            const { x: y, y: b } = g;
                            return { x: y, y: b };
                        },
                    },
                    ...a
                } = rt(t, e);
                const c = { x: n, y: r };
                const d = await nn(e, a);
                const u = je(Ne(o));
                const f = ra(u);
                let h = c[f];
                let p = c[u];
                if (i) {
                    const g = f === "y" ? "top" : "left";
                    const y = f === "y" ? "bottom" : "right";
                    const b = h + d[g];
                    const k = h - d[y];
                    h = ci(b, h, k);
                }
                if (s) {
                    const g = u === "y" ? "top" : "left";
                    const y = u === "y" ? "bottom" : "right";
                    const b = p + d[g];
                    const k = p - d[y];
                    p = ci(b, p, k);
                }
                const m = l.fn({ ...e, [f]: h, [u]: p });
                return {
                    ...m,
                    data: {
                        x: m.x - n,
                        y: m.y - r,
                        enabled: { [f]: i, [u]: s },
                    },
                };
            },
        }
    );
};
const Ah = function (t) {
    return (
        t === void 0 && (t = {}),
        {
            name: "size",
            options: t,
            async fn(e) {
                let n, r;
                const { placement: o, rects: i, platform: s, elements: l } = e;
                const { apply: a = () => {}, ...c } = rt(t, e);
                const d = await nn(e, c);
                const u = Ne(o);
                const f = $e(o);
                const h = je(o) === "y";
                const { width: p, height: m } = i.floating;
                let g;
                let y;
                u === "top" || u === "bottom"
                    ? ((g = u),
                      (y =
                          f ===
                          ((await (s.isRTL == null
                              ? void 0
                              : s.isRTL(l.floating)))
                              ? "start"
                              : "end")
                              ? "left"
                              : "right"))
                    : ((y = u), (g = f === "end" ? "top" : "bottom"));
                const b = m - d.top - d.bottom;
                const k = p - d.left - d.right;
                const v = He(m - d[g], b);
                const x = He(p - d[y], k);
                const S = !e.middlewareData.shift;
                let w = v;
                let N = x;
                if (
                    ((n = e.middlewareData.shift) != null &&
                        n.enabled.x &&
                        (N = k),
                    (r = e.middlewareData.shift) != null &&
                        r.enabled.y &&
                        (w = b),
                    S && !f)
                ) {
                    const M = fe(d.left, 0);
                    const B = fe(d.right, 0);
                    const F = fe(d.top, 0);
                    const K = fe(d.bottom, 0);
                    h
                        ? (N =
                              p -
                              2 *
                                  (M !== 0 || B !== 0
                                      ? M + B
                                      : fe(d.left, d.right)))
                        : (w =
                              m -
                              2 *
                                  (F !== 0 || K !== 0
                                      ? F + K
                                      : fe(d.top, d.bottom)));
                }
                await a({ ...e, availableWidth: N, availableHeight: w });
                const D = await s.getDimensions(l.floating);
                return p !== D.width || m !== D.height
                    ? { reset: { rects: !0 } }
                    : {};
            },
        }
    );
};
function pi() {
    return typeof window < "u";
}
function rn(t) {
    return Nh(t) ? (t.nodeName || "").toLowerCase() : "#document";
}
function Me(t) {
    let e;
    return (
        (t == null || (e = t.ownerDocument) == null ? void 0 : e.defaultView) ||
        window
    );
}
function ot(t) {
    let e;
    return (e = (Nh(t) ? t.ownerDocument : t.document) || window.document) ==
        null
        ? void 0
        : e.documentElement;
}
function Nh(t) {
    return pi() ? t instanceof Node || t instanceof Me(t).Node : !1;
}
function Fe(t) {
    return pi() ? t instanceof Element || t instanceof Me(t).Element : !1;
}
function Ke(t) {
    return pi()
        ? t instanceof HTMLElement || t instanceof Me(t).HTMLElement
        : !1;
}
function Eh(t) {
    return !pi() || typeof ShadowRoot > "u"
        ? !1
        : t instanceof ShadowRoot || t instanceof Me(t).ShadowRoot;
}
const dx = new Set(["inline", "contents"]);
function En(t) {
    const { overflow: e, overflowX: n, overflowY: r, display: o } = _e(t);
    return /auto|scroll|overlay|hidden|clip/.test(e + r + n) && !dx.has(o);
}
const ux = new Set(["table", "td", "th"]);
function Oh(t) {
    return ux.has(rn(t));
}
const fx = [":popover-open", ":modal"];
function wr(t) {
    return fx.some((e) => {
        try {
            return t.matches(e);
        } catch {
            return !1;
        }
    });
}
const hx = ["transform", "translate", "scale", "rotate", "perspective"];
const px = [
    "transform",
    "translate",
    "scale",
    "rotate",
    "perspective",
    "filter",
];
const mx = ["paint", "layout", "strict", "content"];
function mi(t) {
    const e = gi();
    const n = Fe(t) ? _e(t) : t;
    return (
        hx.some((r) => (n[r] ? n[r] !== "none" : !1)) ||
        (n.containerType ? n.containerType !== "normal" : !1) ||
        (!e && (n.backdropFilter ? n.backdropFilter !== "none" : !1)) ||
        (!e && (n.filter ? n.filter !== "none" : !1)) ||
        px.some((r) => (n.willChange || "").includes(r)) ||
        mx.some((r) => (n.contain || "").includes(r))
    );
}
function Rh(t) {
    let e = kt(t);
    for (; Ke(e) && !on(e); ) {
        if (mi(e)) return e;
        if (wr(e)) return null;
        e = kt(e);
    }
    return null;
}
function gi() {
    return typeof CSS > "u" || !CSS.supports
        ? !1
        : CSS.supports("-webkit-backdrop-filter", "none");
}
const gx = new Set(["html", "body", "#document"]);
function on(t) {
    return gx.has(rn(t));
}
function _e(t) {
    return Me(t).getComputedStyle(t);
}
function xr(t) {
    return Fe(t)
        ? { scrollLeft: t.scrollLeft, scrollTop: t.scrollTop }
        : { scrollLeft: t.scrollX, scrollTop: t.scrollY };
}
function kt(t) {
    if (rn(t) === "html") return t;
    const e = t.assignedSlot || t.parentNode || (Eh(t) && t.host) || ot(t);
    return Eh(e) ? e.host : e;
}
function Dh(t) {
    const e = kt(t);
    return on(e)
        ? t.ownerDocument
            ? t.ownerDocument.body
            : t.body
        : Ke(e) && En(e)
          ? e
          : Dh(e);
}
function hi(t, e, n) {
    let r;
    (e === void 0 && (e = []), n === void 0 && (n = !0));
    const o = Dh(t);
    const i = o === ((r = t.ownerDocument) == null ? void 0 : r.body);
    const s = Me(o);
    if (i) {
        const l = yi(s);
        return e.concat(
            s,
            s.visualViewport || [],
            En(o) ? o : [],
            l && n ? hi(l) : [],
        );
    }
    return e.concat(o, hi(o, [], n));
}
function yi(t) {
    return t.parent && Object.getPrototypeOf(t.parent) ? t.frameElement : null;
}
function zh(t) {
    const e = _e(t);
    let n = parseFloat(e.width) || 0;
    let r = parseFloat(e.height) || 0;
    const o = Ke(t);
    const i = o ? t.offsetWidth : n;
    const s = o ? t.offsetHeight : r;
    const l = br(n) !== i || br(r) !== s;
    return (l && ((n = i), (r = s)), { width: n, height: r, $: l });
}
function Bh(t) {
    return Fe(t) ? t : t.contextElement;
}
function Nn(t) {
    const e = Bh(t);
    if (!Ke(e)) return We(1);
    const n = e.getBoundingClientRect();
    const { width: r, height: o, $: i } = zh(e);
    let s = (i ? br(n.width) : n.width) / r;
    let l = (i ? br(n.height) : n.height) / o;
    return (
        (!s || !Number.isFinite(s)) && (s = 1),
        (!l || !Number.isFinite(l)) && (l = 1),
        { x: s, y: l }
    );
}
const yx = We(0);
function Hh(t) {
    const e = Me(t);
    return !gi() || !e.visualViewport
        ? yx
        : { x: e.visualViewport.offsetLeft, y: e.visualViewport.offsetTop };
}
function bx(t, e, n) {
    return (e === void 0 && (e = !1), !n || (e && n !== Me(t)) ? !1 : e);
}
function kr(t, e, n, r) {
    (e === void 0 && (e = !1), n === void 0 && (n = !1));
    const o = t.getBoundingClientRect();
    const i = Bh(t);
    let s = We(1);
    e && (r ? Fe(r) && (s = Nn(r)) : (s = Nn(t)));
    const l = bx(i, n, r) ? Hh(i) : We(0);
    let a = (o.left + l.x) / s.x;
    let c = (o.top + l.y) / s.y;
    let d = o.width / s.x;
    let u = o.height / s.y;
    if (i) {
        const f = Me(i);
        const h = r && Fe(r) ? Me(r) : r;
        let p = f;
        let m = yi(p);
        for (; m && r && h !== p; ) {
            const g = Nn(m);
            const y = m.getBoundingClientRect();
            const b = _e(m);
            const k = y.left + (m.clientLeft + parseFloat(b.paddingLeft)) * g.x;
            const v = y.top + (m.clientTop + parseFloat(b.paddingTop)) * g.y;
            ((a *= g.x),
                (c *= g.y),
                (d *= g.x),
                (u *= g.y),
                (a += k),
                (c += v),
                (p = Me(m)),
                (m = yi(p)));
        }
    }
    return xt({ width: d, height: u, x: a, y: c });
}
function bi(t, e) {
    const n = xr(t).scrollLeft;
    return e ? e.left + n : kr(ot(t)).left + n;
}
function $h(t, e) {
    const n = t.getBoundingClientRect();
    const r = n.left + e.scrollLeft - bi(t, n);
    const o = n.top + e.scrollTop;
    return { x: r, y: o };
}
function wx(t) {
    const { elements: e, rect: n, offsetParent: r, strategy: o } = t;
    const i = o === "fixed";
    const s = ot(r);
    const l = e ? wr(e.floating) : !1;
    if (r === s || (l && i)) return n;
    let a = { scrollLeft: 0, scrollTop: 0 };
    let c = We(1);
    const d = We(0);
    const u = Ke(r);
    if (
        (u || (!u && !i)) &&
        ((rn(r) !== "body" || En(s)) && (a = xr(r)), Ke(r))
    ) {
        const h = kr(r);
        ((c = Nn(r)), (d.x = h.x + r.clientLeft), (d.y = h.y + r.clientTop));
    }
    const f = s && !u && !i ? $h(s, a) : We(0);
    return {
        width: n.width * c.x,
        height: n.height * c.y,
        x: n.x * c.x - a.scrollLeft * c.x + d.x + f.x,
        y: n.y * c.y - a.scrollTop * c.y + d.y + f.y,
    };
}
function xx(t) {
    return Array.from(t.getClientRects());
}
function kx(t) {
    const e = ot(t);
    const n = xr(t);
    const r = t.ownerDocument.body;
    const o = fe(e.scrollWidth, e.clientWidth, r.scrollWidth, r.clientWidth);
    const i = fe(
        e.scrollHeight,
        e.clientHeight,
        r.scrollHeight,
        r.clientHeight,
    );
    let s = -n.scrollLeft + bi(t);
    const l = -n.scrollTop;
    return (
        _e(r).direction === "rtl" &&
            (s += fe(e.clientWidth, r.clientWidth) - o),
        { width: o, height: i, x: s, y: l }
    );
}
const Ih = 25;
function Sx(t, e) {
    const n = Me(t);
    const r = ot(t);
    const o = n.visualViewport;
    let i = r.clientWidth;
    let s = r.clientHeight;
    let l = 0;
    let a = 0;
    if (o) {
        ((i = o.width), (s = o.height));
        const d = gi();
        (!d || (d && e === "fixed")) && ((l = o.offsetLeft), (a = o.offsetTop));
    }
    const c = bi(r);
    if (c <= 0) {
        const d = r.ownerDocument;
        const u = d.body;
        const f = getComputedStyle(u);
        const h =
            (d.compatMode === "CSS1Compat" &&
                parseFloat(f.marginLeft) + parseFloat(f.marginRight)) ||
            0;
        const p = Math.abs(r.clientWidth - u.clientWidth - h);
        p <= Ih && (i -= p);
    } else c <= Ih && (i += c);
    return { width: i, height: s, x: l, y: a };
}
const Cx = new Set(["absolute", "fixed"]);
function vx(t, e) {
    const n = kr(t, !0, e === "fixed");
    const r = n.top + t.clientTop;
    const o = n.left + t.clientLeft;
    const i = Ke(t) ? Nn(t) : We(1);
    const s = t.clientWidth * i.x;
    const l = t.clientHeight * i.y;
    const a = o * i.x;
    const c = r * i.y;
    return { width: s, height: l, x: a, y: c };
}
function Ph(t, e, n) {
    let r;
    if (e === "viewport") r = Sx(t, n);
    else if (e === "document") r = kx(ot(t));
    else if (Fe(e)) r = vx(e, n);
    else {
        const o = Hh(t);
        r = { x: e.x - o.x, y: e.y - o.y, width: e.width, height: e.height };
    }
    return xt(r);
}
function Fh(t, e) {
    const n = kt(t);
    return n === e || !Fe(n) || on(n)
        ? !1
        : _e(n).position === "fixed" || Fh(n, e);
}
function Mx(t, e) {
    const n = e.get(t);
    if (n) return n;
    let r = hi(t, [], !1).filter((l) => Fe(l) && rn(l) !== "body");
    let o = null;
    const i = _e(t).position === "fixed";
    let s = i ? kt(t) : t;
    for (; Fe(s) && !on(s); ) {
        const l = _e(s);
        const a = mi(s);
        (!a && l.position === "fixed" && (o = null),
            (
                i
                    ? !a && !o
                    : (!a &&
                          l.position === "static" &&
                          !!o &&
                          Cx.has(o.position)) ||
                      (En(s) && !a && Fh(t, s))
            )
                ? (r = r.filter((d) => d !== s))
                : (o = l),
            (s = kt(s)));
    }
    return (e.set(t, r), r);
}
function Tx(t) {
    const { element: e, boundary: n, rootBoundary: r, strategy: o } = t;
    const s = [
        ...(n === "clippingAncestors"
            ? wr(e)
                ? []
                : Mx(e, this._c)
            : [].concat(n)),
        r,
    ];
    const l = s[0];
    const a = s.reduce(
        (c, d) => {
            const u = Ph(e, d, o);
            return (
                (c.top = fe(u.top, c.top)),
                (c.right = He(u.right, c.right)),
                (c.bottom = He(u.bottom, c.bottom)),
                (c.left = fe(u.left, c.left)),
                c
            );
        },
        Ph(e, l, o),
    );
    return {
        width: a.right - a.left,
        height: a.bottom - a.top,
        x: a.left,
        y: a.top,
    };
}
function Ax(t) {
    const { width: e, height: n } = zh(t);
    return { width: e, height: n };
}
function Ex(t, e, n) {
    const r = Ke(e);
    const o = ot(e);
    const i = n === "fixed";
    const s = kr(t, !0, i, e);
    let l = { scrollLeft: 0, scrollTop: 0 };
    const a = We(0);
    function c() {
        a.x = bi(o);
    }
    if (r || (!r && !i)) {
        if (((rn(e) !== "body" || En(o)) && (l = xr(e)), r)) {
            const h = kr(e, !0, i, e);
            ((a.x = h.x + e.clientLeft), (a.y = h.y + e.clientTop));
        } else o && c();
    }
    i && !r && o && c();
    const d = o && !r && !i ? $h(o, l) : We(0);
    const u = s.left + l.scrollLeft - a.x - d.x;
    const f = s.top + l.scrollTop - a.y - d.y;
    return { x: u, y: f, width: s.width, height: s.height };
}
function ia(t) {
    return _e(t).position === "static";
}
function Lh(t, e) {
    if (!Ke(t) || _e(t).position === "fixed") return null;
    if (e) return e(t);
    let n = t.offsetParent;
    return (ot(t) === n && (n = n.ownerDocument.body), n);
}
function _h(t, e) {
    const n = Me(t);
    if (wr(t)) return n;
    if (!Ke(t)) {
        let o = kt(t);
        for (; o && !on(o); ) {
            if (Fe(o) && !ia(o)) return o;
            o = kt(o);
        }
        return n;
    }
    let r = Lh(t, e);
    for (; r && Oh(r) && ia(r); ) r = Lh(r, e);
    return r && on(r) && ia(r) && !mi(r) ? n : r || Rh(t) || n;
}
const Nx = async function (t) {
    const e = this.getOffsetParent || _h;
    const n = this.getDimensions;
    const r = await n(t.floating);
    return {
        reference: Ex(t.reference, await e(t.floating), t.strategy),
        floating: { x: 0, y: 0, width: r.width, height: r.height },
    };
};
function Ox(t) {
    return _e(t).direction === "rtl";
}
const Rx = {
    convertOffsetParentRelativeRectToViewportRelativeRect: wx,
    getDocumentElement: ot,
    getClippingRect: Tx,
    getOffsetParent: _h,
    getElementRects: Nx,
    getClientRects: xx,
    getDimensions: Ax,
    getScale: Nn,
    isElement: Fe,
    isRTL: Ox,
};
const Vh = Mh;
const Wh = xh;
const wi = Th;
const xi = kh;
const jh = Ah;
const Kh = Sh;
const Uh = wh;
const qh = vh;
const ki = (t, e, n) => {
    const r = new Map();
    const o = { platform: Rx, ...n };
    const i = { ...o.platform, _c: r };
    return bh(t, e, { ...o, platform: i });
};
const Jh = (t, e) => {
    ki(
        {
            getBoundingClientRect: () => {
                const { from: r, to: o } = t.state.selection;
                const i = t.view.coordsAtPos(r);
                const s = t.view.coordsAtPos(o);
                return {
                    top: Math.min(i.top, s.top),
                    bottom: Math.max(i.bottom, s.bottom),
                    left: Math.min(i.left, s.left),
                    right: Math.max(i.right, s.right),
                    width: Math.abs(s.right - i.left),
                    height: Math.abs(s.bottom - i.top),
                    x: Math.min(i.left, s.left),
                    y: Math.min(i.top, s.top),
                };
            },
        },
        e,
        {
            placement: "bottom-start",
            strategy: "absolute",
            middleware: [wi(), xi()],
        },
    ).then(({ x: r, y: o, strategy: i }) => {
        ((e.style.width = "max-content"),
            (e.style.position = i),
            (e.style.left = `${r}px`),
            (e.style.top = `${o}px`));
    });
};
const Gh = ({ mergeTags: t, noMergeTagSearchResultsMessage: e }) => ({
    items: ({ query: n }) =>
        Object.entries(t)
            .filter(
                ([r, o]) =>
                    r
                        .toLowerCase()
                        .replace(/\s/g, "")
                        .includes(n.toLowerCase()) ||
                    o
                        .toLowerCase()
                        .replace(/\s/g, "")
                        .includes(n.toLowerCase()),
            )
            .map(([r, o]) => ({ id: r, label: o })),
    render: () => {
        let n;
        let r = 0;
        let o = null;
        const i = () => {
            const u = document.createElement("div");
            return ((u.className = "fi-dropdown-panel fi-dropdown-list"), u);
        };
        const s = () => {
            if (!n || !o) return;
            const u = o.items || [];
            if (((n.innerHTML = ""), u.length)) {
                u.forEach((f, h) => {
                    const p = document.createElement("button");
                    ((p.className = `fi-dropdown-list-item fi-dropdown-list-item-label ${h === r ? "fi-selected" : ""}`),
                        (p.textContent = f.label),
                        (p.type = "button"),
                        p.addEventListener("click", () => l(h)),
                        n.appendChild(p));
                });
            } else {
                const f = document.createElement("div");
                ((f.className = "fi-dropdown-header"),
                    (f.textContent = e),
                    n.appendChild(f));
            }
        };
        const l = (u) => {
            if (!o) return;
            const h = (o.items || [])[u];
            h && o.command({ id: h.id });
        };
        const a = () => {
            if (!o) return;
            const u = o.items || [];
            u.length !== 0 && ((r = (r + u.length - 1) % u.length), s());
        };
        const c = () => {
            if (!o) return;
            const u = o.items || [];
            u.length !== 0 && ((r = (r + 1) % u.length), s());
        };
        const d = () => {
            l(r);
        };
        return {
            onStart: (u) => {
                ((o = u),
                    (r = 0),
                    (n = i()),
                    (n.style.position = "absolute"),
                    s(),
                    document.body.appendChild(n),
                    u.clientRect && Jh(u.editor, n));
            },
            onUpdate: (u) => {
                ((o = u), (r = 0), s(), u.clientRect && Jh(u.editor, n));
            },
            onKeyDown: (u) =>
                u.event.key === "Escape"
                    ? (n && n.parentNode && n.parentNode.removeChild(n), !0)
                    : u.event.key === "ArrowUp"
                      ? (a(), !0)
                      : u.event.key === "ArrowDown"
                        ? (c(), !0)
                        : u.event.key === "Enter"
                          ? (d(), !0)
                          : !1,
            onExit: () => {
                n && n.parentNode && n.parentNode.removeChild(n);
            },
        };
    },
});
const Xh = async ({
    acceptedFileTypes: t,
    acceptedFileTypesValidationMessage: e,
    canAttachFiles: n,
    customExtensionUrls: r,
    deleteCustomBlockButtonIconHtml: o,
    editCustomBlockButtonIconHtml: i,
    editCustomBlockUsing: s,
    insertCustomBlockUsing: l,
    key: a,
    maxFileSize: c,
    maxFileSizeValidationMessage: d,
    mergeTags: u,
    noMergeTagSearchResultsMessage: f,
    placeholder: h,
    statePath: p,
    textColors: m,
    uploadingFileMessage: g,
    $wire: y,
}) => {
    const b = [
        Tu,
        Au,
        Hl,
        Eu,
        Nu,
        Ou.configure({
            deleteCustomBlockButtonIconHtml: o,
            editCustomBlockButtonIconHtml: i,
            editCustomBlockUsing: s,
            insertCustomBlockUsing: l,
        }),
        Du,
        Pu,
        Iu,
        Lu,
        Cu,
        vu,
        zu,
        Bu,
        Hu,
        $u,
        Fu,
        _u,
        Vu,
        ju.configure({ inline: !0 }),
        Ku,
        af.configure({ autolink: !0, openOnClick: !1 }),
        $l,
        ...(n
            ? [
                  kf.configure({
                      acceptedTypes: t,
                      acceptedTypesValidationMessage: e,
                      get$WireUsing: () => y,
                      key: a,
                      maxSize: c,
                      maxSizeValidationMessage: d,
                      statePath: p,
                      uploadingMessage: g,
                  }),
              ]
            : []),
        ...(Object.keys(u).length
            ? [
                  Cf.configure({
                      deleteTriggerWithBackspace: !0,
                      suggestion: Gh({
                          mergeTags: u,
                          noMergeTagSearchResultsMessage: f,
                      }),
                      mergeTags: u,
                  }),
              ]
            : []),
        _l,
        vf,
        Vl.configure({ placeholder: h }),
        Tf.configure({ textColors: m }),
        Mf,
        Af,
        Ef,
        Nf,
        sh.configure({ table: { resizable: !0 } }),
        lh,
        ah.configure({
            types: ["heading", "paragraph"],
            alignments: ["start", "center", "end", "justify"],
            defaultAlignment: "start",
        }),
        ch,
        Mu,
    ];
    const k = await Promise.all(
        r.map(async (v) => {
            new RegExp("^(?:[a-z+]+:)?//", "i").test(v) ||
                (v = new URL(v, document.baseURI).href);
            try {
                const S = (await import(v)).default;
                return typeof S === "function" ? S() : S;
            } catch (S) {
                return (
                    console.error(
                        `Failed to load rich editor custom extension from [${v}]:`,
                        S,
                    ),
                    null
                );
            }
        }),
    );
    for (let v of k) {
        if (!v || !v.name) continue;
        const x = b.findIndex((S) => S.name === v.name);
        (v.name === "placeholder" &&
            v.parent === null &&
            (v = Vl.configure(v.options)),
            x !== -1 ? (b[x] = v) : b.push(v));
    }
    return b;
};
function Dx(t, e) {
    const n = Math.min(t.top, e.top);
    const r = Math.max(t.bottom, e.bottom);
    const o = Math.min(t.left, e.left);
    const s = Math.max(t.right, e.right) - o;
    const l = r - n;
    const a = o;
    const c = n;
    return new DOMRect(a, c, s, l);
}
const Ix = class {
    constructor({
        editor: t,
        element: e,
        view: n,
        updateDelay: r = 250,
        resizeDelay: o = 60,
        shouldShow: i,
        appendTo: s,
        getReferencedVirtualElement: l,
        options: a,
    }) {
        ((this.preventHide = !1),
            (this.isVisible = !1),
            (this.scrollTarget = window),
            (this.floatingUIOptions = {
                strategy: "absolute",
                placement: "top",
                offset: 8,
                flip: {},
                shift: {},
                arrow: !1,
                size: !1,
                autoPlacement: !1,
                hide: !1,
                inline: !1,
                onShow: void 0,
                onHide: void 0,
                onUpdate: void 0,
                onDestroy: void 0,
            }),
            (this.shouldShow = ({ view: d, state: u, from: f, to: h }) => {
                const { doc: p, selection: m } = u;
                const { empty: g } = m;
                const y = !p.textBetween(f, h).length && lo(u.selection);
                const b = this.element.contains(document.activeElement);
                return !(
                    !(d.hasFocus() || b) ||
                    g ||
                    y ||
                    !this.editor.isEditable
                );
            }),
            (this.mousedownHandler = () => {
                this.preventHide = !0;
            }),
            (this.dragstartHandler = () => {
                this.hide();
            }),
            (this.resizeHandler = () => {
                (this.resizeDebounceTimer &&
                    clearTimeout(this.resizeDebounceTimer),
                    (this.resizeDebounceTimer = window.setTimeout(() => {
                        this.updatePosition();
                    }, this.resizeDelay)));
            }),
            (this.focusHandler = () => {
                setTimeout(() => this.update(this.editor.view));
            }),
            (this.blurHandler = ({ event: d }) => {
                let u;
                if (this.editor.isDestroyed) {
                    this.destroy();
                    return;
                }
                if (this.preventHide) {
                    this.preventHide = !1;
                    return;
                }
                (d?.relatedTarget &&
                    (u = this.element.parentNode) != null &&
                    u.contains(d.relatedTarget)) ||
                    (d?.relatedTarget !== this.editor.view.dom && this.hide());
            }),
            (this.handleDebouncedUpdate = (d, u) => {
                const f = !u?.selection.eq(d.state.selection);
                const h = !u?.doc.eq(d.state.doc);
                (!f && !h) ||
                    (this.updateDebounceTimer &&
                        clearTimeout(this.updateDebounceTimer),
                    (this.updateDebounceTimer = window.setTimeout(() => {
                        this.updateHandler(d, f, h, u);
                    }, this.updateDelay)));
            }),
            (this.updateHandler = (d, u, f, h) => {
                const { composing: p } = d;
                if (p || (!u && !f)) return;
                if (!this.getShouldShow(h)) {
                    this.hide();
                    return;
                }
                (this.updatePosition(), this.show());
            }),
            (this.transactionHandler = ({ transaction: d }) => {
                d.getMeta("bubbleMenu") === "updatePosition" &&
                    this.updatePosition();
            }));
        let c;
        ((this.editor = t),
            (this.element = e),
            (this.view = n),
            (this.updateDelay = r),
            (this.resizeDelay = o),
            (this.appendTo = s),
            (this.scrollTarget = (c = a?.scrollTarget) != null ? c : window),
            (this.getReferencedVirtualElement = l),
            (this.floatingUIOptions = { ...this.floatingUIOptions, ...a }),
            (this.element.tabIndex = 0),
            i && (this.shouldShow = i),
            this.element.addEventListener("mousedown", this.mousedownHandler, {
                capture: !0,
            }),
            this.view.dom.addEventListener("dragstart", this.dragstartHandler),
            this.editor.on("focus", this.focusHandler),
            this.editor.on("blur", this.blurHandler),
            this.editor.on("transaction", this.transactionHandler),
            window.addEventListener("resize", this.resizeHandler),
            this.scrollTarget.addEventListener("scroll", this.resizeHandler),
            this.update(n, n.state),
            this.getShouldShow() && (this.show(), this.updatePosition()));
    }

    get middlewares() {
        const t = [];
        return (
            this.floatingUIOptions.flip &&
                t.push(
                    xi(
                        typeof this.floatingUIOptions.flip !== "boolean"
                            ? this.floatingUIOptions.flip
                            : void 0,
                    ),
                ),
            this.floatingUIOptions.shift &&
                t.push(
                    wi(
                        typeof this.floatingUIOptions.shift !== "boolean"
                            ? this.floatingUIOptions.shift
                            : void 0,
                    ),
                ),
            this.floatingUIOptions.offset &&
                t.push(
                    Vh(
                        typeof this.floatingUIOptions.offset !== "boolean"
                            ? this.floatingUIOptions.offset
                            : void 0,
                    ),
                ),
            this.floatingUIOptions.arrow &&
                t.push(Uh(this.floatingUIOptions.arrow)),
            this.floatingUIOptions.size &&
                t.push(
                    jh(
                        typeof this.floatingUIOptions.size !== "boolean"
                            ? this.floatingUIOptions.size
                            : void 0,
                    ),
                ),
            this.floatingUIOptions.autoPlacement &&
                t.push(
                    Wh(
                        typeof this.floatingUIOptions.autoPlacement !==
                            "boolean"
                            ? this.floatingUIOptions.autoPlacement
                            : void 0,
                    ),
                ),
            this.floatingUIOptions.hide &&
                t.push(
                    Kh(
                        typeof this.floatingUIOptions.hide !== "boolean"
                            ? this.floatingUIOptions.hide
                            : void 0,
                    ),
                ),
            this.floatingUIOptions.inline &&
                t.push(
                    qh(
                        typeof this.floatingUIOptions.inline !== "boolean"
                            ? this.floatingUIOptions.inline
                            : void 0,
                    ),
                ),
            t
        );
    }

    get virtualElement() {
        let t;
        const { selection: e } = this.editor.state;
        const n =
            (t = this.getReferencedVirtualElement) == null
                ? void 0
                : t.call(this);
        if (n) return n;
        const r = Xd(this.view, e.from, e.to);
        let o = { getBoundingClientRect: () => r, getClientRects: () => [r] };
        if (e instanceof P) {
            let i = this.view.nodeDOM(e.from);
            const s = i.dataset.nodeViewWrapper
                ? i
                : i.querySelector("[data-node-view-wrapper]");
            (s && (i = s),
                i &&
                    (o = {
                        getBoundingClientRect: () => i.getBoundingClientRect(),
                        getClientRects: () => [i.getBoundingClientRect()],
                    }));
        }
        if (e instanceof G) {
            const { $anchorCell: i, $headCell: s } = e;
            const l = i ? i.pos : s.pos;
            const a = s ? s.pos : i.pos;
            const c = this.view.nodeDOM(l);
            const d = this.view.nodeDOM(a);
            if (!c || !d) return;
            const u =
                c === d
                    ? c.getBoundingClientRect()
                    : Dx(c.getBoundingClientRect(), d.getBoundingClientRect());
            o = { getBoundingClientRect: () => u, getClientRects: () => [u] };
        }
        return o;
    }

    updatePosition() {
        const t = this.virtualElement;
        t &&
            ki(t, this.element, {
                placement: this.floatingUIOptions.placement,
                strategy: this.floatingUIOptions.strategy,
                middleware: this.middlewares,
            }).then(({ x: e, y: n, strategy: r }) => {
                ((this.element.style.width = "max-content"),
                    (this.element.style.position = r),
                    (this.element.style.left = `${e}px`),
                    (this.element.style.top = `${n}px`),
                    this.isVisible &&
                        this.floatingUIOptions.onUpdate &&
                        this.floatingUIOptions.onUpdate());
            });
    }

    update(t, e) {
        const { state: n } = t;
        const r = n.selection.from !== n.selection.to;
        if (this.updateDelay > 0 && r) {
            this.handleDebouncedUpdate(t, e);
            return;
        }
        const o = !e?.selection.eq(t.state.selection);
        const i = !e?.doc.eq(t.state.doc);
        this.updateHandler(t, o, i, e);
    }

    getShouldShow(t) {
        let e;
        const { state: n } = this.view;
        const { selection: r } = n;
        const { ranges: o } = r;
        const i = Math.min(...o.map((a) => a.$from.pos));
        const s = Math.max(...o.map((a) => a.$to.pos));
        return (
            ((e = this.shouldShow) == null
                ? void 0
                : e.call(this, {
                      editor: this.editor,
                      element: this.element,
                      view: this.view,
                      state: n,
                      oldState: t,
                      from: i,
                      to: s,
                  })) || !1
        );
    }

    show() {
        let t;
        if (this.isVisible) return;
        ((this.element.style.visibility = "visible"),
            (this.element.style.opacity = "1"));
        const e =
            typeof this.appendTo === "function"
                ? this.appendTo()
                : this.appendTo;
        ((t = e ?? this.view.dom.parentElement) == null ||
            t.appendChild(this.element),
            this.floatingUIOptions.onShow && this.floatingUIOptions.onShow(),
            (this.isVisible = !0));
    }

    hide() {
        this.isVisible &&
            ((this.element.style.visibility = "hidden"),
            (this.element.style.opacity = "0"),
            this.element.remove(),
            this.floatingUIOptions.onHide && this.floatingUIOptions.onHide(),
            (this.isVisible = !1));
    }

    destroy() {
        (this.hide(),
            this.element.removeEventListener(
                "mousedown",
                this.mousedownHandler,
                { capture: !0 },
            ),
            this.view.dom.removeEventListener(
                "dragstart",
                this.dragstartHandler,
            ),
            window.removeEventListener("resize", this.resizeHandler),
            this.scrollTarget.removeEventListener("scroll", this.resizeHandler),
            this.editor.off("focus", this.focusHandler),
            this.editor.off("blur", this.blurHandler),
            this.editor.off("transaction", this.transactionHandler),
            this.floatingUIOptions.onDestroy &&
                this.floatingUIOptions.onDestroy());
    }
};
const sa = (t) =>
    new L({
        key: typeof t.pluginKey === "string" ? new H(t.pluginKey) : t.pluginKey,
        view: (e) => new Ix({ view: e, ...t }),
    });
const lT = j.create({
    name: "bubbleMenu",
    addOptions() {
        return {
            element: null,
            pluginKey: "bubbleMenu",
            updateDelay: void 0,
            appendTo: void 0,
            shouldShow: null,
        };
    },
    addProseMirrorPlugins() {
        return this.options.element
            ? [
                  sa({
                      pluginKey: this.options.pluginKey,
                      editor: this.editor,
                      element: this.options.element,
                      updateDelay: this.options.updateDelay,
                      options: this.options.options,
                      appendTo: this.options.appendTo,
                      getReferencedVirtualElement:
                          this.options.getReferencedVirtualElement,
                      shouldShow: this.options.shouldShow,
                  }),
              ]
            : [];
    },
});
function Px({
    acceptedFileTypes: t,
    acceptedFileTypesValidationMessage: e,
    activePanel: n,
    canAttachFiles: r,
    deleteCustomBlockButtonIconHtml: o,
    editCustomBlockButtonIconHtml: i,
    extensions: s,
    key: l,
    isDisabled: a,
    isLiveDebounced: c,
    isLiveOnBlur: d,
    liveDebounce: u,
    livewireId: f,
    maxFileSize: h,
    maxFileSizeValidationMessage: p,
    mergeTags: m,
    noMergeTagSearchResultsMessage: g,
    placeholder: y,
    state: b,
    statePath: k,
    textColors: v,
    uploadingFileMessage: x,
    floatingToolbars: S,
}) {
    let w;
    let N = [];
    let D = !1;
    return {
        state: b,
        activePanel: n,
        editorSelection: { type: "text", anchor: 1, head: 1 },
        isUploadingFile: !1,
        fileValidationMessage: null,
        shouldUpdateState: !0,
        editorUpdatedAt: Date.now(),
        async init() {
            ((w = new du({
                editable: !a,
                element: this.$refs.editor,
                extensions: await Xh({
                    acceptedFileTypes: t,
                    acceptedFileTypesValidationMessage: e,
                    canAttachFiles: r,
                    customExtensionUrls: s,
                    deleteCustomBlockButtonIconHtml: o,
                    editCustomBlockButtonIconHtml: i,
                    editCustomBlockUsing: (T, W) =>
                        this.$wire.mountAction(
                            "customBlock",
                            {
                                editorSelection: this.editorSelection,
                                id: T,
                                config: W,
                                mode: "edit",
                            },
                            { schemaComponent: l },
                        ),
                    insertCustomBlockUsing: (T, W = null) =>
                        this.$wire.mountAction(
                            "customBlock",
                            { id: T, dragPosition: W, mode: "insert" },
                            { schemaComponent: l },
                        ),
                    key: l,
                    maxFileSize: h,
                    maxFileSizeValidationMessage: p,
                    mergeTags: m,
                    noMergeTagSearchResultsMessage: g,
                    placeholder: y,
                    statePath: k,
                    textColors: v,
                    uploadingFileMessage: x,
                    $wire: this.$wire,
                    floatingToolbars: S,
                }),
                content: this.state,
            })),
                Object.keys(S).forEach((T) => {
                    const W = this.$refs[`floatingToolbar::${T}`];
                    if (!W) {
                        console.warn(`Floating toolbar [${T}] not found.`);
                        return;
                    }
                    w.registerPlugin(
                        sa({
                            editor: w,
                            element: W,
                            pluginKey: `floatingToolbar::${T}`,
                            shouldShow: ({ editor: U }) =>
                                U.isFocused && U.isActive(T),
                            options: { placement: "bottom", offset: 15 },
                        }),
                    );
                }),
                w.on("create", () => {
                    this.editorUpdatedAt = Date.now();
                }));
            const M = Alpine.debounce(() => {
                D || this.$wire.commit();
            }, u ?? 300);
            (w.on("update", ({ editor: T }) =>
                this.$nextTick(() => {
                    D ||
                        ((this.editorUpdatedAt = Date.now()),
                        (this.state = T.getJSON()),
                        (this.shouldUpdateState = !1),
                        (this.fileValidationMessage = null),
                        c && M());
                }),
            ),
                w.on("selectionUpdate", ({ transaction: T }) => {
                    D ||
                        ((this.editorUpdatedAt = Date.now()),
                        (this.editorSelection = T.selection.toJSON()));
                }),
                d &&
                    w.on("blur", () => {
                        D || this.$wire.commit();
                    }),
                this.$watch("state", () => {
                    if (!D) {
                        if (!this.shouldUpdateState) {
                            this.shouldUpdateState = !0;
                            return;
                        }
                        w.commands.setContent(this.state);
                    }
                }));
            const B = (T) => {
                T.detail.livewireId === f &&
                    T.detail.key === l &&
                    this.runEditorCommands(T.detail);
            };
            (window.addEventListener("run-rich-editor-commands", B),
                N.push(["run-rich-editor-commands", B]));
            const F = (T) => {
                T.detail.livewireId === f &&
                    T.detail.key === l &&
                    ((this.isUploadingFile = !0),
                    (this.fileValidationMessage = null),
                    T.stopPropagation());
            };
            (window.addEventListener("rich-editor-uploading-file", F),
                N.push(["rich-editor-uploading-file", F]));
            const K = (T) => {
                T.detail.livewireId === f &&
                    T.detail.key === l &&
                    ((this.isUploadingFile = !1), T.stopPropagation());
            };
            (window.addEventListener("rich-editor-uploaded-file", K),
                N.push(["rich-editor-uploaded-file", K]));
            const _ = (T) => {
                T.detail.livewireId === f &&
                    T.detail.key === l &&
                    ((this.isUploadingFile = !1),
                    (this.fileValidationMessage = T.detail.validationMessage),
                    T.stopPropagation());
            };
            (window.addEventListener("rich-editor-file-validation-message", _),
                N.push(["rich-editor-file-validation-message", _]),
                window.dispatchEvent(
                    new CustomEvent(`schema-component-${f}-${l}-loaded`),
                ));
        },
        getEditor() {
            return w;
        },
        $getEditor() {
            return this.getEditor();
        },
        setEditorSelection(M) {
            M &&
                ((this.editorSelection = M),
                w
                    .chain()
                    .command(
                        ({ tr: B }) => (
                            B.setSelection(
                                I.fromJSON(w.state.doc, this.editorSelection),
                            ),
                            !0
                        ),
                    )
                    .run());
        },
        runEditorCommands({ commands: M, editorSelection: B }) {
            this.setEditorSelection(B);
            let F = w.chain();
            (M.forEach((K) => (F = F[K.name](...(K.arguments ?? [])))),
                F.run());
        },
        togglePanel(M = null) {
            if (this.isPanelActive(M)) {
                this.activePanel = null;
                return;
            }
            this.activePanel = M;
        },
        isPanelActive(M = null) {
            return M === null
                ? this.activePanel !== null
                : this.activePanel === M;
        },
        insertMergeTag(M) {
            w.chain()
                .focus()
                .insertContent([
                    { type: "mergeTag", attrs: { id: M } },
                    { type: "text", text: " " },
                ])
                .run();
        },
        destroy() {
            ((D = !0),
                N.forEach(([M, B]) => {
                    window.removeEventListener(M, B);
                }),
                (N = []),
                w && (w.destroy(), (w = null)),
                (this.shouldUpdateState = !0));
        },
    };
}
export { Px as default };
