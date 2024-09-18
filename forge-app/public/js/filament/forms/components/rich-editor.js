const Mi = '2.1.5'
const K = '[data-trix-attachment]'
const je = {
  preview: { presentation: 'gallery', caption: { name: !0, size: !0 } },
  file: { caption: { size: !0 } }
}
var y = {
  default: { tagName: 'div', parse: !1 },
  quote: { tagName: 'blockquote', nestable: !0 },
  heading1: { tagName: 'h1', terminal: !0, breakOnReturn: !0, group: !1 },
  code: {
    tagName: 'pre',
    terminal: !0,
    htmlAttributes: ['language'],
    text: { plaintext: !0 }
  },
  bulletList: { tagName: 'ul', parse: !1 },
  bullet: {
    tagName: 'li',
    listAttribute: 'bulletList',
    group: !1,
    nestable: !0,
    test (n) {
      return Je(n.parentNode) === y[this.listAttribute].tagName
    }
  },
  numberList: { tagName: 'ol', parse: !1 },
  number: {
    tagName: 'li',
    listAttribute: 'numberList',
    group: !1,
    nestable: !0,
    test (n) {
      return Je(n.parentNode) === y[this.listAttribute].tagName
    }
  },
  attachmentGallery: {
    tagName: 'div',
    exclusive: !0,
    terminal: !0,
    parse: !1,
    group: !1
  }
}
var Je = (n) => {
  let t
  return n == null || (t = n.tagName) === null || t === void 0
    ? void 0
    : t.toLowerCase()
}
const Ke = navigator.userAgent.match(/android\s([0-9]+.*Chrome)/i)
const se = Ke && parseInt(Ke[1])
const St = {
  composesExistingText: /Android.*Chrome/.test(navigator.userAgent),
  recentAndroid: se && se > 12,
  samsungAndroid: se && navigator.userAgent.match(/Android.*SM-/),
  forcesObjectResizing: /Trident.*rv:11/.test(navigator.userAgent),
  supportsInputEvents:
        typeof InputEvent < 'u' &&
        ['data', 'getTargetRanges', 'inputType'].every(
          (n) => n in InputEvent.prototype
        )
}
const h = {
  attachFiles: 'Attach Files',
  bold: 'Bold',
  bullets: 'Bullets',
  byte: 'Byte',
  bytes: 'Bytes',
  captionPlaceholder: 'Add a caption\u2026',
  code: 'Code',
  heading1: 'Heading',
  indent: 'Increase Level',
  italic: 'Italic',
  link: 'Link',
  numbers: 'Numbers',
  outdent: 'Decrease Level',
  quote: 'Quote',
  redo: 'Redo',
  remove: 'Remove',
  strike: 'Strikethrough',
  undo: 'Undo',
  unlink: 'Unlink',
  url: 'URL',
  urlPlaceholder: 'Enter a URL\u2026',
  GB: 'GB',
  KB: 'KB',
  MB: 'MB',
  PB: 'PB',
  TB: 'TB'
}
const ji = [h.bytes, h.KB, h.MB, h.GB, h.TB, h.PB]
const vi = {
  prefix: 'IEC',
  precision: 2,
  formatter (n) {
    switch (n) {
      case 0:
        return '0 '.concat(h.bytes)
      case 1:
        return '1 '.concat(h.byte)
      default:
        let t
        this.prefix === 'SI'
          ? (t = 1e3)
          : this.prefix === 'IEC' && (t = 1024)
        const e = Math.floor(Math.log(n) / Math.log(t))
        const i = (n / Math.pow(t, e))
          .toFixed(this.precision)
          .replace(/0*$/, '')
          .replace(/\.$/, '')
        return ''.concat(i, ' ').concat(ji[e])
    }
  }
}
const te = '\uFEFF'
const U = '\xA0'
const Ai = function (n) {
  for (const t in n) {
    const e = n[t]
    this[t] = e
  }
  return this
}
const We = document.documentElement
const Wi = We.matches
const p = function (n) {
  let {
    onElement: t,
    matchingSelector: e,
    withCallback: i,
    inPhase: r,
    preventDefault: o,
    times: s
  } = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {}
  const a = t || We
  const l = e
  const c = r === 'capturing'
  const u = function (b) {
    s != null && --s == 0 && u.destroy()
    const A = q(b.target, { matchingSelector: l })
    A != null && (i?.call(A, b, A), o && b.preventDefault())
  }
  return (
    (u.destroy = () => a.removeEventListener(n, u, c)),
    a.addEventListener(n, u, c),
    u
  )
}
const vt = function (n) {
  let {
    onElement: t,
    bubbles: e,
    cancelable: i,
    attributes: r
  } = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {}
  const o = t ?? We;
  (e = e !== !1), (i = i !== !1)
  const s = document.createEvent('Events')
  return s.initEvent(n, e, i), r != null && Ai.call(s, r), o.dispatchEvent(s)
}
const xi = function (n, t) {
  if (n?.nodeType === 1) return Wi.call(n, t)
}
var q = function (n) {
  const { matchingSelector: t, untilNode: e } =
        arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {}
  for (; n && n.nodeType !== Node.ELEMENT_NODE;) n = n.parentNode
  if (n != null) {
    if (t == null) return n
    if (n.closest && e == null) return n.closest(t)
    for (; n && n !== e;) {
      if (xi(n, t)) return n
      n = n.parentNode
    }
  }
}
const Ue = (n) => document.activeElement !== n && J(n, document.activeElement)
var J = function (n, t) {
  if (n && t) {
    for (; t;) {
      if (t === n) return !0
      t = t.parentNode
    }
  }
}
const ae = function (n) {
  let t
  if ((t = n) === null || t === void 0 || !t.parentNode) return
  let e = 0
  for (n = n.previousSibling; n;) e++, (n = n.previousSibling)
  return e
}
const V = (n) => {
  let t
  return n == null || (t = n.parentNode) === null || t === void 0
    ? void 0
    : t.removeChild(n)
}
const Ft = function (n) {
  const {
    onlyNodesOfType: t,
    usingFilter: e,
    expandEntityReferences: i
  } = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {}
  const r = (() => {
    switch (t) {
      case 'element':
        return NodeFilter.SHOW_ELEMENT
      case 'text':
        return NodeFilter.SHOW_TEXT
      case 'comment':
        return NodeFilter.SHOW_COMMENT
      default:
        return NodeFilter.SHOW_ALL
    }
  })()
  return document.createTreeWalker(n, r, e ?? null, i === !0)
}
const x = (n) => {
  let t
  return n == null || (t = n.tagName) === null || t === void 0
    ? void 0
    : t.toLowerCase()
}
const d = function (n) {
  let t
  let e
  let i = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {}
  typeof n === 'object'
    ? ((i = n), (n = i.tagName))
    : (i = { attributes: i })
  const r = document.createElement(n)
  if (
    (i.editable != null &&
            (i.attributes == null && (i.attributes = {}),
            (i.attributes.contenteditable = i.editable)),
    i.attributes)
  ) {
    for (t in i.attributes) (e = i.attributes[t]), r.setAttribute(t, e)
  }
  if (i.style) for (t in i.style) (e = i.style[t]), (r.style[t] = e)
  if (i.data) for (t in i.data) (e = i.data[t]), (r.dataset[t] = e)
  return (
    i.className &&
            i.className.split(' ').forEach((o) => {
              r.classList.add(o)
            }),
    i.textContent && (r.textContent = i.textContent),
    i.childNodes &&
            [].concat(i.childNodes).forEach((o) => {
              r.appendChild(o)
            }),
    r
  )
}
let pt
const At = function () {
  if (pt != null) return pt
  pt = []
  for (const n in y) {
    const t = y[n]
    t.tagName && pt.push(t.tagName)
  }
  return pt
}
const le = (n) => ot(n?.firstChild)
const $e = function (n) {
  const { strict: t } =
        arguments.length > 1 && arguments[1] !== void 0
          ? arguments[1]
          : { strict: !0 }
  return t
    ? ot(n)
    : ot(n) ||
              (!ot(n.firstChild) &&
                  (function (e) {
                    return (
                      At().includes(x(e)) && !At().includes(x(e.firstChild))
                    )
                  })(n))
}
var ot = (n) => Ui(n) && n?.data === 'block'
var Ui = (n) => n?.nodeType === Node.COMMENT_NODE
const st = function (n) {
  const { name: t } =
        arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {}
  if (n) {
    return xt(n)
      ? n.data === te
        ? !t || n.parentNode.dataset.trixCursorTarget === t
        : void 0
      : st(n.firstChild)
  }
}
const $ = (n) => xi(n, K)
const yi = (n) => xt(n) && n?.data === ''
var xt = (n) => n?.nodeType === Node.TEXT_NODE
const qe = {
  level2Enabled: !0,
  getLevel () {
    return this.level2Enabled && St.supportsInputEvents ? 2 : 0
  },
  pickFiles (n) {
    const t = d('input', {
      type: 'file',
      multiple: !0,
      hidden: !0,
      id: this.fileInputId
    })
    t.addEventListener('change', () => {
      n(t.files), V(t)
    }),
    V(document.getElementById(this.fileInputId)),
    document.body.appendChild(t),
    t.click()
  }
}
const Tt = {
  removeBlankTableCells: !1,
  tableCellSeparator: ' | ',
  tableRowSeparator: `
`
}
const Y = {
  bold: {
    tagName: 'strong',
    inheritable: !0,
    parser (n) {
      const t = window.getComputedStyle(n)
      return t.fontWeight === 'bold' || t.fontWeight >= 600
    }
  },
  italic: {
    tagName: 'em',
    inheritable: !0,
    parser: (n) => window.getComputedStyle(n).fontStyle === 'italic'
  },
  href: {
    groupTagName: 'a',
    parser (n) {
      const t = 'a:not('.concat(K, ')')
      const e = n.closest(t)
      if (e) return e.getAttribute('href')
    }
  },
  strike: { tagName: 'del', inheritable: !0 },
  frozen: { style: { backgroundColor: 'highlight' } }
}
const Ci = {
  getDefaultHTML: () =>
        `<div class="trix-button-row">
      <span class="trix-button-group trix-button-group--text-tools" data-trix-button-group="text-tools">
        <button type="button" class="trix-button trix-button--icon trix-button--icon-bold" data-trix-attribute="bold" data-trix-key="b" title="`
          .concat(h.bold, '" tabindex="-1">')
          .concat(
            h.bold,
                `</button>
        <button type="button" class="trix-button trix-button--icon trix-button--icon-italic" data-trix-attribute="italic" data-trix-key="i" title="`
          )
          .concat(h.italic, '" tabindex="-1">')
          .concat(
            h.italic,
                `</button>
        <button type="button" class="trix-button trix-button--icon trix-button--icon-strike" data-trix-attribute="strike" title="`
          )
          .concat(h.strike, '" tabindex="-1">')
          .concat(
            h.strike,
                `</button>
        <button type="button" class="trix-button trix-button--icon trix-button--icon-link" data-trix-attribute="href" data-trix-action="link" data-trix-key="k" title="`
          )
          .concat(h.link, '" tabindex="-1">')
          .concat(
            h.link,
                `</button>
      </span>

      <span class="trix-button-group trix-button-group--block-tools" data-trix-button-group="block-tools">
        <button type="button" class="trix-button trix-button--icon trix-button--icon-heading-1" data-trix-attribute="heading1" title="`
          )
          .concat(h.heading1, '" tabindex="-1">')
          .concat(
            h.heading1,
                `</button>
        <button type="button" class="trix-button trix-button--icon trix-button--icon-quote" data-trix-attribute="quote" title="`
          )
          .concat(h.quote, '" tabindex="-1">')
          .concat(
            h.quote,
                `</button>
        <button type="button" class="trix-button trix-button--icon trix-button--icon-code" data-trix-attribute="code" title="`
          )
          .concat(h.code, '" tabindex="-1">')
          .concat(
            h.code,
                `</button>
        <button type="button" class="trix-button trix-button--icon trix-button--icon-bullet-list" data-trix-attribute="bullet" title="`
          )
          .concat(h.bullets, '" tabindex="-1">')
          .concat(
            h.bullets,
                `</button>
        <button type="button" class="trix-button trix-button--icon trix-button--icon-number-list" data-trix-attribute="number" title="`
          )
          .concat(h.numbers, '" tabindex="-1">')
          .concat(
            h.numbers,
                `</button>
        <button type="button" class="trix-button trix-button--icon trix-button--icon-decrease-nesting-level" data-trix-action="decreaseNestingLevel" title="`
          )
          .concat(h.outdent, '" tabindex="-1">')
          .concat(
            h.outdent,
                `</button>
        <button type="button" class="trix-button trix-button--icon trix-button--icon-increase-nesting-level" data-trix-action="increaseNestingLevel" title="`
          )
          .concat(h.indent, '" tabindex="-1">')
          .concat(
            h.indent,
                `</button>
      </span>

      <span class="trix-button-group trix-button-group--file-tools" data-trix-button-group="file-tools">
        <button type="button" class="trix-button trix-button--icon trix-button--icon-attach" data-trix-action="attachFiles" title="`
          )
          .concat(h.attachFiles, '" tabindex="-1">')
          .concat(
            h.attachFiles,
                `</button>
      </span>

      <span class="trix-button-group-spacer"></span>

      <span class="trix-button-group trix-button-group--history-tools" data-trix-button-group="history-tools">
        <button type="button" class="trix-button trix-button--icon trix-button--icon-undo" data-trix-action="undo" data-trix-key="z" title="`
          )
          .concat(h.undo, '" tabindex="-1">')
          .concat(
            h.undo,
                `</button>
        <button type="button" class="trix-button trix-button--icon trix-button--icon-redo" data-trix-action="redo" data-trix-key="shift+z" title="`
          )
          .concat(h.redo, '" tabindex="-1">')
          .concat(
            h.redo,
                `</button>
      </span>
    </div>

    <div class="trix-dialogs" data-trix-dialogs>
      <div class="trix-dialog trix-dialog--link" data-trix-dialog="href" data-trix-dialog-attribute="href">
        <div class="trix-dialog__link-fields">
          <input type="url" name="href" class="trix-input trix-input--dialog" placeholder="`
          )
          .concat(h.urlPlaceholder, '" aria-label="')
          .concat(
            h.url,
                `" required data-trix-input>
          <div class="trix-button-group">
            <input type="button" class="trix-button trix-button--dialog" value="`
          )
          .concat(
            h.link,
                `" data-trix-method="setAttribute">
            <input type="button" class="trix-button trix-button--dialog" value="`
          )
          .concat(
            h.unlink,
                `" data-trix-method="removeAttribute">
          </div>
        </div>
      </div>
    </div>`
          )
}
const Re = { interval: 5e3 }
const Lt = Object.freeze({
  __proto__: null,
  attachments: je,
  blockAttributes: y,
  browser: St,
  css: {
    attachment: 'attachment',
    attachmentCaption: 'attachment__caption',
    attachmentCaptionEditor: 'attachment__caption-editor',
    attachmentMetadata: 'attachment__metadata',
    attachmentMetadataContainer: 'attachment__metadata-container',
    attachmentName: 'attachment__name',
    attachmentProgress: 'attachment__progress',
    attachmentSize: 'attachment__size',
    attachmentToolbar: 'attachment__toolbar',
    attachmentGallery: 'attachment-gallery'
  },
  fileSize: vi,
  input: qe,
  keyNames: {
    8: 'backspace',
    9: 'tab',
    13: 'return',
    27: 'escape',
    37: 'left',
    39: 'right',
    46: 'delete',
    68: 'd',
    72: 'h',
    79: 'o'
  },
  lang: h,
  parser: Tt,
  textAttributes: Y,
  toolbar: Ci,
  undo: Re
})
const f = class {
  static proxyMethod (t) {
    const { name: e, toMethod: i, toProperty: r, optional: o } = qi(t)
    this.prototype[e] = function () {
      let s, a
      let l, c
      return (
        i
          ? (a = o
              ? (l = this[i]) === null || l === void 0
                  ? void 0
                  : l.call(this)
              : this[i]())
          : r && (a = this[r]),
        o
          ? ((s = (c = a) === null || c === void 0 ? void 0 : c[e]),
            s ? Ge.call(s, a, arguments) : void 0)
          : ((s = a[e]), Ge.call(s, a, arguments))
      )
    }
  }
}
var qi = function (n) {
  const t = n.match(Vi)
  if (!t) {
    throw new Error("can't parse @proxyMethod expression: ".concat(n))
  }
  const e = { name: t[4] }
  return (
    t[2] != null ? (e.toMethod = t[1]) : (e.toProperty = t[1]),
    t[3] != null && (e.optional = !0),
    e
  )
}
var { apply: Ge } = Function.prototype
var Vi = new RegExp('^(.+?)(\\(\\))?(\\?)?\\.(.+?)$')
let ce
let ue
let he
const Z = class extends f {
  static box () {
    const t =
            arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : ''
    return t instanceof this ? t : this.fromUCS2String(t?.toString())
  }

  static fromUCS2String (t) {
    return new this(t, Ee(t))
  }

  static fromCodepoints (t) {
    return new this(Se(t), t)
  }

  constructor (t, e) {
    super(...arguments),
    (this.ucs2String = t),
    (this.codepoints = e),
    (this.length = this.codepoints.length),
    (this.ucs2Length = this.ucs2String.length)
  }

  offsetToUCS2Offset (t) {
    return Se(this.codepoints.slice(0, Math.max(0, t))).length
  }

  offsetFromUCS2Offset (t) {
    return Ee(this.ucs2String.slice(0, Math.max(0, t))).length
  }

  slice () {
    return this.constructor.fromCodepoints(
      this.codepoints.slice(...arguments)
    )
  }

  charAt (t) {
    return this.slice(t, t + 1)
  }

  isEqualTo (t) {
    return this.constructor.box(t).ucs2String === this.ucs2String
  }

  toJSON () {
    return this.ucs2String
  }

  getCacheKey () {
    return this.ucs2String
  }

  toString () {
    return this.ucs2String
  }
}
const Hi =
    ((ce = Array.from) === null || ce === void 0
      ? void 0
      : ce.call(Array, '\u{1F47C}').length) === 1
const zi =
    ((ue = ' '.codePointAt) === null || ue === void 0
      ? void 0
      : ue.call(' ', 0)) != null
const _i =
    ((he = String.fromCodePoint) === null || he === void 0
      ? void 0
      : he.call(String, 32, 128124)) === ' \u{1F47C}'
let Ee
let Se;
(Ee =
    Hi && zi
      ? (n) => Array.from(n).map((t) => t.codePointAt(0))
      : function (n) {
        const t = []
        let e = 0
        const { length: i } = n
        for (; e < i;) {
          let r = n.charCodeAt(e++)
          if (r >= 55296 && r <= 56319 && e < i) {
            const o = n.charCodeAt(e++);
            (64512 & o) == 56320
              ? (r = ((1023 & r) << 10) + (1023 & o) + 65536)
              : e--
          }
          t.push(r)
        }
        return t
      }),
(Se = _i
  ? (n) => String.fromCodePoint(...Array.from(n || []))
  : function (n) {
    return (() => {
      const t = []
      return (
        Array.from(n).forEach((e) => {
          let i = ''
          e > 65535 &&
                              ((e -= 65536),
                              (i += String.fromCharCode(
                                ((e >>> 10) & 1023) | 55296
                              )),
                              (e = 56320 | (1023 & e))),
          t.push(i + String.fromCharCode(e))
        }),
        t
      )
    })().join('')
  })
let Ji = 0
const O = class extends f {
  static fromJSONString (t) {
    return this.fromJSON(JSON.parse(t))
  }

  constructor () {
    super(...arguments), (this.id = ++Ji)
  }

  hasSameConstructorAs (t) {
    return this.constructor === t?.constructor
  }

  isEqualTo (t) {
    return this === t
  }

  inspect () {
    const t = []
    const e = this.contentsForInspection() || {}
    for (const i in e) {
      const r = e[i]
      t.push(''.concat(i, '=').concat(r))
    }
    return '#<'
      .concat(this.constructor.name, ':')
      .concat(this.id)
      .concat(t.length ? ' '.concat(t.join(', ')) : '', '>')
  }

  contentsForInspection () {}
  toJSONString () {
    return JSON.stringify(this)
  }

  toUTF16String () {
    return Z.box(this)
  }

  getCacheKey () {
    return this.id.toString()
  }
}
const Q = function () {
  const n =
        arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : []
  const t =
        arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : []
  if (n.length !== t.length) return !1
  for (let e = 0; e < n.length; e++) if (n[e] !== t[e]) return !1
  return !0
}
const Ve = function (n) {
  const t = n.slice(0)
  for (
    var e = arguments.length, i = new Array(e > 1 ? e - 1 : 0), r = 1;
    r < e;
    r++
  ) {
    i[r - 1] = arguments[r]
  }
  return t.splice(...i), t
}
const Ki =
    /[\u05BE\u05C0\u05C3\u05D0-\u05EA\u05F0-\u05F4\u061B\u061F\u0621-\u063A\u0640-\u064A\u066D\u0671-\u06B7\u06BA-\u06BE\u06C0-\u06CE\u06D0-\u06D5\u06E5\u06E6\u200F\u202B\u202E\uFB1F-\uFB28\uFB2A-\uFB36\uFB38-\uFB3C\uFB3E\uFB40\uFB41\uFB43\uFB44\uFB46-\uFBB1\uFBD3-\uFD3D\uFD50-\uFD8F\uFD92-\uFDC7\uFDF0-\uFDFB\uFE70-\uFE72\uFE74\uFE76-\uFEFC]/
const $i = (function () {
  const n = d('input', { dir: 'auto', name: 'x', dirName: 'x.dir' })
  const t = d('textarea', { dir: 'auto', name: 'y', dirName: 'y.dir' })
  const e = d('form')
  e.appendChild(n), e.appendChild(t)
  const i = (function () {
    try {
      return new FormData(e).has(t.dirName)
    } catch {
      return !1
    }
  })()
  const r = (function () {
    try {
      return n.matches(':dir(ltr),:dir(rtl)')
    } catch {
      return !1
    }
  })()
  return i
    ? function (o) {
      return (t.value = o), new FormData(e).get(t.dirName)
    }
    : r
      ? function (o) {
        return (n.value = o), n.matches(':dir(rtl)') ? 'rtl' : 'ltr'
      }
      : function (o) {
        const s = o.trim().charAt(0)
        return Ki.test(s) ? 'rtl' : 'ltr'
      }
})()
let de = null
let ge = null
let me = null
let Dt = null
const Le = () => (de || (de = Xi().concat(Gi())), de)
const v = (n) => y[n]
var Gi = () => (ge || (ge = Object.keys(y)), ge)
const De = (n) => Y[n]
var Xi = () => (me || (me = Object.keys(Y)), me)
const ki = function (n, t) {
  Yi(n).textContent = t.replace(/%t/g, n)
}
var Yi = function (n) {
  const t = document.createElement('style')
  t.setAttribute('type', 'text/css'),
  t.setAttribute('data-tag-name', n.toLowerCase())
  const e = Zi()
  return (
    e && t.setAttribute('nonce', e),
    document.head.insertBefore(t, document.head.firstChild),
    t
  )
}
var Zi = function () {
  const n = Xe('trix-csp-nonce') || Xe('csp-nonce')
  if (n) return n.getAttribute('content')
}
var Xe = (n) => document.head.querySelector('meta[name='.concat(n, ']'))
const Ye = { 'application/x-trix-feature-detection': 'test' }
const Ri = function (n) {
  const t = n.getData('text/plain')
  const e = n.getData('text/html')
  if (!t || !e) return t?.length
  {
    const { body: i } = new DOMParser().parseFromString(e, 'text/html')
    if (i.textContent === t) return !i.querySelector('*')
  }
}
const Ei = /Mac|^iP/.test(navigator.platform)
  ? (n) => n.metaKey
  : (n) => n.ctrlKey
const He = (n) => setTimeout(n, 1)
const Si = function () {
  const n =
        arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : {}
  const t = {}
  for (const e in n) {
    const i = n[e]
    t[e] = i
  }
  return t
}
const dt = function () {
  const n =
        arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : {}
  const t =
        arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {}
  if (Object.keys(n).length !== Object.keys(t).length) return !1
  for (const e in n) if (n[e] !== t[e]) return !1
  return !0
}
const g = function (n) {
  if (n != null) {
    return (
      Array.isArray(n) || (n = [n, n]),
      [Ze(n[0]), Ze(n[1] != null ? n[1] : n[0])]
    )
  }
}
const N = function (n) {
  if (n == null) return
  const [t, e] = g(n)
  return we(t, e)
}
const Pt = function (n, t) {
  if (n == null || t == null) return
  const [e, i] = g(n)
  const [r, o] = g(t)
  return we(e, r) && we(i, o)
}
var Ze = function (n) {
  return typeof n === 'number' ? n : Si(n)
}
var we = function (n, t) {
  return typeof n === 'number' ? n === t : dt(n, t)
}
const It = class extends f {
  constructor () {
    super(...arguments),
    (this.update = this.update.bind(this)),
    (this.selectionManagers = [])
  }

  start () {
    this.started ||
            ((this.started = !0),
            document.addEventListener('selectionchange', this.update, !0))
  }

  stop () {
    if (this.started) {
      return (
        (this.started = !1),
        document.removeEventListener('selectionchange', this.update, !0)
      )
    }
  }

  registerSelectionManager (t) {
    if (!this.selectionManagers.includes(t)) {
      return this.selectionManagers.push(t), this.start()
    }
  }

  unregisterSelectionManager (t) {
    if (
      ((this.selectionManagers = this.selectionManagers.filter(
        (e) => e !== t
      )),
      this.selectionManagers.length === 0)
    ) {
      return this.stop()
    }
  }

  notifySelectionManagersOfSelectionChange () {
    return this.selectionManagers.map((t) => t.selectionDidChange())
  }

  update () {
    this.notifySelectionManagersOfSelectionChange()
  }

  reset () {
    this.update()
  }
}
const tt = new It()
const Li = function () {
  const n = window.getSelection()
  if (n.rangeCount > 0) return n
}
const yt = function () {
  let n
  const t = (n = Li()) === null || n === void 0 ? void 0 : n.getRangeAt(0)
  if (t && !Qi(t)) return t
}
const Di = function (n) {
  const t = window.getSelection()
  return t.removeAllRanges(), t.addRange(n), tt.update()
}
var Qi = (n) => Qe(n.startContainer) || Qe(n.endContainer)
var Qe = (n) => !Object.getPrototypeOf(n)
const bt = (n) =>
  n
    .replace(new RegExp(''.concat(te), 'g'), '')
    .replace(new RegExp(''.concat(U), 'g'), ' ')
const ze = new RegExp('[^\\S'.concat(U, ']'))
const _e = (n) =>
  n
    .replace(new RegExp(''.concat(ze.source), 'g'), ' ')
    .replace(/\ {2,}/g, ' ')
const ti = function (n, t) {
  if (n.isEqualTo(t)) return ['', '']
  const e = pe(n, t)
  const { length: i } = e.utf16String
  let r
  if (i) {
    const { offset: o } = e
    const s = n.codepoints.slice(0, o).concat(n.codepoints.slice(o + i))
    r = pe(t, Z.fromCodepoints(s))
  } else r = pe(t, n)
  return [e.utf16String.toString(), r.utf16String.toString()]
}
var pe = function (n, t) {
  let e = 0
  let i = n.length
  let r = t.length
  for (; e < i && n.charAt(e).isEqualTo(t.charAt(e));) e++
  for (; i > e + 1 && n.charAt(i - 1).isEqualTo(t.charAt(r - 1));) {
    i--, r--
  }
  return { utf16String: n.slice(e, i), offset: e }
}
var C = class extends O {
  static fromCommonAttributesOfObjects () {
    const t =
            arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : []
    if (!t.length) return new this()
    let e = ft(t[0])
    let i = e.getKeys()
    return (
      t.slice(1).forEach((r) => {
        (i = e.getKeysCommonToHash(ft(r))), (e = e.slice(i))
      }),
      e
    )
  }

  static box (t) {
    return ft(t)
  }

  constructor () {
    const t =
            arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : {}
    super(...arguments), (this.values = Bt(t))
  }

  add (t, e) {
    return this.merge(tn(t, e))
  }

  remove (t) {
    return new C(Bt(this.values, t))
  }

  get (t) {
    return this.values[t]
  }

  has (t) {
    return t in this.values
  }

  merge (t) {
    return new C(en(this.values, nn(t)))
  }

  slice (t) {
    const e = {}
    return (
      Array.from(t).forEach((i) => {
        this.has(i) && (e[i] = this.values[i])
      }),
      new C(e)
    )
  }

  getKeys () {
    return Object.keys(this.values)
  }

  getKeysCommonToHash (t) {
    return (
      (t = ft(t)),
      this.getKeys().filter((e) => this.values[e] === t.values[e])
    )
  }

  isEqualTo (t) {
    return Q(this.toArray(), ft(t).toArray())
  }

  isEmpty () {
    return this.getKeys().length === 0
  }

  toArray () {
    if (!this.array) {
      const t = []
      for (const e in this.values) {
        const i = this.values[e]
        t.push(t.push(e, i))
      }
      this.array = t.slice(0)
    }
    return this.array
  }

  toObject () {
    return Bt(this.values)
  }

  toJSON () {
    return this.toObject()
  }

  contentsForInspection () {
    return { values: JSON.stringify(this.values) }
  }
}
var tn = function (n, t) {
  const e = {}
  return (e[n] = t), e
}
var en = function (n, t) {
  const e = Bt(n)
  for (const i in t) {
    const r = t[i]
    e[i] = r
  }
  return e
}
var Bt = function (n, t) {
  const e = {}
  return (
    Object.keys(n)
      .sort()
      .forEach((i) => {
        i !== t && (e[i] = n[i])
      }),
    e
  )
}
var ft = function (n) {
  return n instanceof C ? n : new C(n)
}
var nn = function (n) {
  return n instanceof C ? n.values : n
}
const Ct = class {
  static groupObjects () {
    let t
    const e =
            arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : []
    let { depth: i, asTree: r } =
            arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {}
    r && i == null && (i = 0)
    const o = []
    return (
      Array.from(e).forEach((s) => {
        let a
        if (t) {
          let l, c, u
          if (
            (l = s.canBeGrouped) !== null &&
                        l !== void 0 &&
                        l.call(s, i) &&
                        (c = (u = t[t.length - 1]).canBeGroupedWith) !== null &&
                        c !== void 0 &&
                        c.call(u, s, i)
          ) {
            return void t.push(s)
          }
          o.push(new this(t, { depth: i, asTree: r })), (t = null)
        }
        (a = s.canBeGrouped) !== null && a !== void 0 && a.call(s, i)
          ? (t = [s])
          : o.push(s)
      }),
      t && o.push(new this(t, { depth: i, asTree: r })),
      o
    )
  }

  constructor () {
    const t =
            arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : []
    const { depth: e, asTree: i } =
            arguments.length > 1 ? arguments[1] : void 0;
    (this.objects = t),
    i &&
                ((this.depth = e),
                (this.objects = this.constructor.groupObjects(this.objects, {
                  asTree: i,
                  depth: this.depth + 1
                })))
  }

  getObjects () {
    return this.objects
  }

  getDepth () {
    return this.depth
  }

  getCacheKey () {
    const t = ['objectGroup']
    return (
      Array.from(this.getObjects()).forEach((e) => {
        t.push(e.getCacheKey())
      }),
      t.join('/')
    )
  }
}
const Te = class extends f {
  constructor () {
    const t =
            arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : []
    super(...arguments),
    (this.objects = {}),
    Array.from(t).forEach((e) => {
      const i = JSON.stringify(e)
      this.objects[i] == null && (this.objects[i] = e)
    })
  }

  find (t) {
    const e = JSON.stringify(t)
    return this.objects[e]
  }
}
const Be = class {
  constructor (t) {
    this.reset(t)
  }

  add (t) {
    const e = ei(t)
    this.elements[e] = t
  }

  remove (t) {
    const e = ei(t)
    const i = this.elements[e]
    if (i) return delete this.elements[e], i
  }

  reset () {
    const t =
            arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : []
    return (
      (this.elements = {}),
      Array.from(t).forEach((e) => {
        this.add(e)
      }),
      t
    )
  }
}
var ei = (n) => n.dataset.trixStoreKey
const at = class extends f {
  isPerforming () {
    return this.performing === !0
  }

  hasPerformed () {
    return this.performed === !0
  }

  hasSucceeded () {
    return this.performed && this.succeeded
  }

  hasFailed () {
    return this.performed && !this.succeeded
  }

  getPromise () {
    return (
      this.promise ||
                (this.promise = new Promise(
                  (t, e) => (
                    (this.performing = !0),
                    this.perform((i, r) => {
                      (this.succeeded = i),
                      (this.performing = !1),
                      (this.performed = !0),
                      this.succeeded ? t(r) : e(r)
                    })
                  )
                )),
      this.promise
    )
  }

  perform (t) {
    return t(!1)
  }

  release () {
    let t, e;
    (t = this.promise) === null ||
            t === void 0 ||
            (e = t.cancel) === null ||
            e === void 0 ||
            e.call(t),
    (this.promise = null),
    (this.performing = null),
    (this.performed = null),
    (this.succeeded = null)
  }
}
at.proxyMethod('getPromise().then'), at.proxyMethod('getPromise().catch')
const M = class extends f {
  constructor (t) {
    const e =
            arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {}
    super(...arguments),
    (this.object = t),
    (this.options = e),
    (this.childViews = []),
    (this.rootView = this)
  }

  getNodes () {
    return (
      this.nodes || (this.nodes = this.createNodes()),
      this.nodes.map((t) => t.cloneNode(!0))
    )
  }

  invalidate () {
    let t
    return (
      (this.nodes = null),
      (this.childViews = []),
      (t = this.parentView) === null || t === void 0
        ? void 0
        : t.invalidate()
    )
  }

  invalidateViewForObject (t) {
    let e
    return (e = this.findViewForObject(t)) === null || e === void 0
      ? void 0
      : e.invalidate()
  }

  findOrCreateCachedChildView (t, e, i) {
    let r = this.getCachedViewForObject(e)
    return (
      r
        ? this.recordChildView(r)
        : ((r = this.createChildView(...arguments)),
          this.cacheViewForObject(r, e)),
      r
    )
  }

  createChildView (t, e) {
    const i =
            arguments.length > 2 && arguments[2] !== void 0 ? arguments[2] : {}
    e instanceof Ct && ((i.viewClass = t), (t = Fe))
    const r = new t(e, i)
    return this.recordChildView(r)
  }

  recordChildView (t) {
    return (
      (t.parentView = this),
      (t.rootView = this.rootView),
      this.childViews.push(t),
      t
    )
  }

  getAllChildViews () {
    let t = []
    return (
      this.childViews.forEach((e) => {
        t.push(e), (t = t.concat(e.getAllChildViews()))
      }),
      t
    )
  }

  findElement () {
    return this.findElementForObject(this.object)
  }

  findElementForObject (t) {
    const e = t?.id
    if (e) {
      return this.rootView.element.querySelector(
        "[data-trix-id='".concat(e, "']")
      )
    }
  }

  findViewForObject (t) {
    for (const e of this.getAllChildViews()) if (e.object === t) return e
  }

  getViewCache () {
    return this.rootView !== this
      ? this.rootView.getViewCache()
      : this.isViewCachingEnabled()
        ? (this.viewCache || (this.viewCache = {}), this.viewCache)
        : void 0
  }

  isViewCachingEnabled () {
    return this.shouldCacheViews !== !1
  }

  enableViewCaching () {
    this.shouldCacheViews = !0
  }

  disableViewCaching () {
    this.shouldCacheViews = !1
  }

  getCachedViewForObject (t) {
    let e
    return (e = this.getViewCache()) === null || e === void 0
      ? void 0
      : e[t.getCacheKey()]
  }

  cacheViewForObject (t, e) {
    const i = this.getViewCache()
    i && (i[e.getCacheKey()] = t)
  }

  garbageCollectCachedViews () {
    const t = this.getViewCache()
    if (t) {
      const e = this.getAllChildViews()
        .concat(this)
        .map((i) => i.object.getCacheKey())
      for (const i in t) e.includes(i) || delete t[i]
    }
  }
}
var Fe = class extends M {
  constructor () {
    super(...arguments),
    (this.objectGroup = this.object),
    (this.viewClass = this.options.viewClass),
    delete this.options.viewClass
  }

  getChildViews () {
    return (
      this.childViews.length ||
                Array.from(this.objectGroup.getObjects()).forEach((t) => {
                  this.findOrCreateCachedChildView(
                    this.viewClass,
                    t,
                    this.options
                  )
                }),
      this.childViews
    )
  }

  createNodes () {
    const t = this.createContainerElement()
    return (
      this.getChildViews().forEach((e) => {
        Array.from(e.getNodes()).forEach((i) => {
          t.appendChild(i)
        })
      }),
      [t]
    )
  }

  createContainerElement () {
    const t =
            arguments.length > 0 && arguments[0] !== void 0
              ? arguments[0]
              : this.objectGroup.getDepth()
    return this.getChildViews()[0].createContainerElement(t)
  }
}
const rn = 'style href src width height language class'.split(' ')
const on = 'javascript:'.split(' ')
const sn = 'script iframe form noscript'.split(' ')
const lt = class extends f {
  static setHTML (t, e) {
    const i = new this(e).sanitize()
    const r = i.getHTML ? i.getHTML() : i.outerHTML
    t.innerHTML = r
  }

  static sanitize (t, e) {
    const i = new this(t, e)
    return i.sanitize(), i
  }

  constructor (t) {
    const {
      allowedAttributes: e,
      forbiddenProtocols: i,
      forbiddenElements: r
    } = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {}
    super(...arguments),
    (this.allowedAttributes = e || rn),
    (this.forbiddenProtocols = i || on),
    (this.forbiddenElements = r || sn),
    (this.body = an(t))
  }

  sanitize () {
    return this.sanitizeElements(), this.normalizeListElementNesting()
  }

  getHTML () {
    return this.body.innerHTML
  }

  getBody () {
    return this.body
  }

  sanitizeElements () {
    const t = Ft(this.body)
    const e = []
    for (; t.nextNode();) {
      const i = t.currentNode
      switch (i.nodeType) {
        case Node.ELEMENT_NODE:
          this.elementIsRemovable(i)
            ? e.push(i)
            : this.sanitizeElement(i)
          break
        case Node.COMMENT_NODE:
          e.push(i)
      }
    }
    return e.forEach((i) => V(i)), this.body
  }

  sanitizeElement (t) {
    return (
      t.hasAttribute('href') &&
                this.forbiddenProtocols.includes(t.protocol) &&
                t.removeAttribute('href'),
      Array.from(t.attributes).forEach((e) => {
        const { name: i } = e
        this.allowedAttributes.includes(i) ||
                    i.indexOf('data-trix') === 0 ||
                    t.removeAttribute(i)
      }),
      t
    )
  }

  normalizeListElementNesting () {
    return (
      Array.from(this.body.querySelectorAll('ul,ol')).forEach((t) => {
        const e = t.previousElementSibling
        e && x(e) === 'li' && e.appendChild(t)
      }),
      this.body
    )
  }

  elementIsRemovable (t) {
    if (t?.nodeType === Node.ELEMENT_NODE) {
      return (
        this.elementIsForbidden(t) || this.elementIsntSerializable(t)
      )
    }
  }

  elementIsForbidden (t) {
    return this.forbiddenElements.includes(x(t))
  }

  elementIsntSerializable (t) {
    return t.getAttribute('data-trix-serialize') === 'false' && !$(t)
  }
}
var an = function () {
  let n = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : ''
  n = n.replace(/<\/html[^>]*>[^]*$/i, '</html>')
  const t = document.implementation.createHTMLDocument('')
  return (
    (t.documentElement.innerHTML = n),
    Array.from(t.head.querySelectorAll('style')).forEach((e) => {
      t.body.appendChild(e)
    }),
    t.body
  )
}
const { css: W } = Lt
const kt = class extends M {
  constructor () {
    super(...arguments),
    (this.attachment = this.object),
    (this.attachment.uploadProgressDelegate = this),
    (this.attachmentPiece = this.options.piece)
  }

  createContentNodes () {
    return []
  }

  createNodes () {
    let t
    const e = (t = d({
      tagName: 'figure',
      className: this.getClassName(),
      data: this.getData(),
      editable: !1
    }))
    const i = this.getHref()
    return (
      i &&
                ((t = d({
                  tagName: 'a',
                  editable: !1,
                  attributes: { href: i, tabindex: -1 }
                })),
                e.appendChild(t)),
      this.attachment.hasContent()
        ? lt.setHTML(t, this.attachment.getContent())
        : this.createContentNodes().forEach((r) => {
          t.appendChild(r)
        }),
      t.appendChild(this.createCaptionElement()),
      this.attachment.isPending() &&
                ((this.progressElement = d({
                  tagName: 'progress',
                  attributes: {
                    class: W.attachmentProgress,
                    value: this.attachment.getUploadProgress(),
                    max: 100
                  },
                  data: {
                    trixMutable: !0,
                    trixStoreKey: [
                      'progressElement',
                      this.attachment.id
                    ].join('/')
                  }
                })),
                e.appendChild(this.progressElement)),
      [ii('left'), e, ii('right')]
    )
  }

  createCaptionElement () {
    const t = d({
      tagName: 'figcaption',
      className: W.attachmentCaption
    })
    const e = this.attachmentPiece.getCaption()
    if (e) {
      t.classList.add(''.concat(W.attachmentCaption, '--edited')),
      (t.textContent = e)
    } else {
      let i
      let r
      const o = this.getCaptionConfig()
      if (
        (o.name && (i = this.attachment.getFilename()),
        o.size && (r = this.attachment.getFormattedFilesize()),
        i)
      ) {
        const s = d({
          tagName: 'span',
          className: W.attachmentName,
          textContent: i
        })
        t.appendChild(s)
      }
      if (r) {
        i && t.appendChild(document.createTextNode(' '))
        const s = d({
          tagName: 'span',
          className: W.attachmentSize,
          textContent: r
        })
        t.appendChild(s)
      }
    }
    return t
  }

  getClassName () {
    const t = [
      W.attachment,
      ''.concat(W.attachment, '--').concat(this.attachment.getType())
    ]
    const e = this.attachment.getExtension()
    return (
      e && t.push(''.concat(W.attachment, '--').concat(e)), t.join(' ')
    )
  }

  getData () {
    const t = {
      trixAttachment: JSON.stringify(this.attachment),
      trixContentType: this.attachment.getContentType(),
      trixId: this.attachment.id
    }
    const { attributes: e } = this.attachmentPiece
    return (
      e.isEmpty() || (t.trixAttributes = JSON.stringify(e)),
      this.attachment.isPending() && (t.trixSerialize = !1),
      t
    )
  }

  getHref () {
    if (!ln(this.attachment.getContent(), 'a')) {
      return this.attachment.getHref()
    }
  }

  getCaptionConfig () {
    let t
    const e = this.attachment.getType()
    const i = Si((t = je[e]) === null || t === void 0 ? void 0 : t.caption)
    return e === 'file' && (i.name = !0), i
  }

  findProgressElement () {
    let t
    return (t = this.findElement()) === null || t === void 0
      ? void 0
      : t.querySelector('progress')
  }

  attachmentDidChangeUploadProgress () {
    const t = this.attachment.getUploadProgress()
    const e = this.findProgressElement()
    e && (e.value = t)
  }
}
var ii = (n) =>
  d({
    tagName: 'span',
    textContent: te,
    data: { trixCursorTarget: n, trixSerialize: !1 }
  })
var ln = function (n, t) {
  const e = d('div')
  return lt.setHTML(e, n || ''), e.querySelector(t)
}
const Nt = class extends kt {
  constructor () {
    super(...arguments), (this.attachment.previewDelegate = this)
  }

  createContentNodes () {
    return (
      (this.image = d({
        tagName: 'img',
        attributes: { src: '' },
        data: { trixMutable: !0 }
      })),
      this.refresh(this.image),
      [this.image]
    )
  }

  createCaptionElement () {
    const t = super.createCaptionElement(...arguments)
    return (
      t.textContent ||
                t.setAttribute('data-trix-placeholder', h.captionPlaceholder),
      t
    )
  }

  refresh (t) {
    let e
    if (
      (t ||
                (t =
                    (e = this.findElement()) === null || e === void 0
                      ? void 0
                      : e.querySelector('img')),
      t)
    ) {
      return this.updateAttributesForImage(t)
    }
  }

  updateAttributesForImage (t) {
    const e = this.attachment.getURL()
    const i = this.attachment.getPreviewURL()
    if (((t.src = i || e), i === e)) {
      t.removeAttribute('data-trix-serialized-attributes')
    } else {
      const a = JSON.stringify({ src: e })
      t.setAttribute('data-trix-serialized-attributes', a)
    }
    const r = this.attachment.getWidth()
    const o = this.attachment.getHeight()
    r != null && (t.width = r), o != null && (t.height = o)
    const s = [
      'imageElement',
      this.attachment.id,
      t.src,
      t.width,
      t.height
    ].join('/')
    t.dataset.trixStoreKey = s
  }

  attachmentDidChangeAttributes () {
    return this.refresh(this.image), this.refresh()
  }
}
const Ot = class extends M {
  constructor () {
    super(...arguments),
    (this.piece = this.object),
    (this.attributes = this.piece.getAttributes()),
    (this.textConfig = this.options.textConfig),
    (this.context = this.options.context),
    this.piece.attachment
      ? (this.attachment = this.piece.attachment)
      : (this.string = this.piece.toString())
  }

  createNodes () {
    let t = this.attachment
      ? this.createAttachmentNodes()
      : this.createStringNodes()
    const e = this.createElement()
    if (e) {
      const i = (function (r) {
        for (
          ;
          (o = r) !== null && o !== void 0 && o.firstElementChild;

        ) {
          var o
          r = r.firstElementChild
        }
        return r
      })(e)
      Array.from(t).forEach((r) => {
        i.appendChild(r)
      }),
      (t = [e])
    }
    return t
  }

  createAttachmentNodes () {
    const t = this.attachment.isPreviewable() ? Nt : kt
    return this.createChildView(t, this.piece.attachment, {
      piece: this.piece
    }).getNodes()
  }

  createStringNodes () {
    let t
    if ((t = this.textConfig) !== null && t !== void 0 && t.plaintext) {
      return [document.createTextNode(this.string)]
    }
    {
      const e = []
      const i = this.string.split(`
`)
      for (let r = 0; r < i.length; r++) {
        const o = i[r]
        if (r > 0) {
          const s = d('br')
          e.push(s)
        }
        if (o.length) {
          const s = document.createTextNode(this.preserveSpaces(o))
          e.push(s)
        }
      }
      return e
    }
  }

  createElement () {
    let t
    let e
    let i
    const r = {}
    for (e in this.attributes) {
      i = this.attributes[e]
      const s = De(e)
      if (s) {
        if (s.tagName) {
          var o
          const a = d(s.tagName)
          o ? (o.appendChild(a), (o = a)) : (t = o = a)
        }
        if ((s.styleProperty && (r[s.styleProperty] = i), s.style)) {
          for (e in s.style) (i = s.style[e]), (r[e] = i)
        }
      }
    }
    if (Object.keys(r).length) {
      for (e in (t || (t = d('span')), r)) {
        (i = r[e]), (t.style[e] = i)
      }
    }
    return t
  }

  createContainerElement () {
    for (const t in this.attributes) {
      const e = this.attributes[t]
      const i = De(t)
      if (i && i.groupTagName) {
        const r = {}
        return (r[t] = e), d(i.groupTagName, r)
      }
    }
  }

  preserveSpaces (t) {
    return (
      this.context.isLast && (t = t.replace(/\ $/, U)),
      (t = t
        .replace(/(\S)\ {3}(\S)/g, '$1 '.concat(U, ' $2'))
        .replace(/\ {2}/g, ''.concat(U, ' '))
        .replace(/\ {2}/g, ' '.concat(U))),
      (this.context.isFirst || this.context.followsWhitespace) &&
                (t = t.replace(/^\ /, U)),
      t
    )
  }
}
const Mt = class extends M {
  constructor () {
    super(...arguments),
    (this.text = this.object),
    (this.textConfig = this.options.textConfig)
  }

  createNodes () {
    const t = []
    const e = Ct.groupObjects(this.getPieces())
    const i = e.length - 1
    for (let o = 0; o < e.length; o++) {
      const s = e[o]
      const a = {}
      o === 0 && (a.isFirst = !0),
      o === i && (a.isLast = !0),
      cn(r) && (a.followsWhitespace = !0)
      const l = this.findOrCreateCachedChildView(Ot, s, {
        textConfig: this.textConfig,
        context: a
      })
      t.push(...Array.from(l.getNodes() || []))
      var r = s
    }
    return t
  }

  getPieces () {
    return Array.from(this.text.getPieces()).filter(
      (t) => !t.hasAttribute('blockBreak')
    )
  }
}
var cn = (n) => /\s$/.test(n?.toString())
const { css: ni } = Lt
const jt = class extends M {
  constructor () {
    super(...arguments),
    (this.block = this.object),
    (this.attributes = this.block.getAttributes())
  }

  createNodes () {
    const t = [document.createComment('block')]
    if (this.block.isEmpty()) t.push(d('br'))
    else {
      let e
      const i =
                (e = v(this.block.getLastAttribute())) === null || e === void 0
                  ? void 0
                  : e.text
      const r = this.findOrCreateCachedChildView(Mt, this.block.text, {
        textConfig: i
      })
      t.push(...Array.from(r.getNodes() || [])),
      this.shouldAddExtraNewlineElement() && t.push(d('br'))
    }
    if (this.attributes.length) return t
    {
      let i
      const { tagName: r } = y.default
      this.block.isRTL() && (i = { dir: 'rtl' })
      const o = d({ tagName: r, attributes: i })
      return t.forEach((s) => o.appendChild(s)), [o]
    }
  }

  createContainerElement (t) {
    const e = {}
    let i
    const r = this.attributes[t]
    const { tagName: o, htmlAttributes: s = [] } = v(r)
    if (
      (t === 0 && this.block.isRTL() && Object.assign(e, { dir: 'rtl' }),
      r === 'attachmentGallery')
    ) {
      const a = this.block.getBlockBreakPosition()
      i = ''
        .concat(ni.attachmentGallery, ' ')
        .concat(ni.attachmentGallery, '--')
        .concat(a)
    }
    return (
      Object.entries(this.block.htmlAttributes).forEach((a) => {
        const [l, c] = a
        s.includes(l) && (e[l] = c)
      }),
      d({ tagName: o, className: i, attributes: e })
    )
  }

  shouldAddExtraNewlineElement () {
    return /\n\n$/.test(this.block.toString())
  }
}
const ct = class extends M {
  static render (t) {
    const e = d('div')
    const i = new this(t, { element: e })
    return i.render(), i.sync(), e
  }

  constructor () {
    super(...arguments),
    (this.element = this.options.element),
    (this.elementStore = new Be()),
    this.setDocument(this.object)
  }

  setDocument (t) {
    t.isEqualTo(this.document) || (this.document = this.object = t)
  }

  render () {
    if (
      ((this.childViews = []),
      (this.shadowElement = d('div')),
      !this.document.isEmpty())
    ) {
      const t = Ct.groupObjects(this.document.getBlocks(), {
        asTree: !0
      })
      Array.from(t).forEach((e) => {
        const i = this.findOrCreateCachedChildView(jt, e)
        Array.from(i.getNodes()).map((r) =>
          this.shadowElement.appendChild(r)
        )
      })
    }
  }

  isSynced () {
    return un(this.shadowElement, this.element)
  }

  sync () {
    const t = this.createDocumentFragmentForSync()
    for (; this.element.lastChild;) {
      this.element.removeChild(this.element.lastChild)
    }
    return this.element.appendChild(t), this.didSync()
  }

  didSync () {
    return (
      this.elementStore.reset(ri(this.element)),
      He(() => this.garbageCollectCachedViews())
    )
  }

  createDocumentFragmentForSync () {
    const t = document.createDocumentFragment()
    return (
      Array.from(this.shadowElement.childNodes).forEach((e) => {
        t.appendChild(e.cloneNode(!0))
      }),
      Array.from(ri(t)).forEach((e) => {
        const i = this.elementStore.remove(e)
        i && e.parentNode.replaceChild(i, e)
      }),
      t
    )
  }
}
var ri = (n) => n.querySelectorAll('[data-trix-store-key]')
var un = (n, t) => oi(n.innerHTML) === oi(t.innerHTML)
var oi = (n) => n.replace(/&nbsp;/g, ' ')
function wt (n) {
  let t, e
  function i (o, s) {
    try {
      const a = n[o](s)
      const l = a.value
      const c = l instanceof hn
      Promise.resolve(c ? l.v : l).then(
        function (u) {
          if (c) {
            const b = o === 'return' ? 'return' : 'next'
            if (!l.k || u.done) return i(b, u)
            u = n[b](u).value
          }
          r(a.done ? 'return' : 'normal', u)
        },
        function (u) {
          i('throw', u)
        }
      )
    } catch (u) {
      r('throw', u)
    }
  }
  function r (o, s) {
    switch (o) {
      case 'return':
        t.resolve({ value: s, done: !0 })
        break
      case 'throw':
        t.reject(s)
        break
      default:
        t.resolve({ value: s, done: !1 })
    }
    (t = t.next) ? i(t.key, t.arg) : (e = null)
  }
  (this._invoke = function (o, s) {
    return new Promise(function (a, l) {
      const c = { key: o, arg: s, resolve: a, reject: l, next: null }
      e ? (e = e.next = c) : ((t = e = c), i(o, s))
    })
  }),
  typeof n.return !== 'function' && (this.return = void 0)
}
function hn (n, t) {
  (this.v = n), (this.k = t)
}
function E (n, t, e) {
  return (
    (t = dn(t)) in n
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
function dn (n) {
  const t = (function (e, i) {
    if (typeof e !== 'object' || e === null) return e
    const r = e[Symbol.toPrimitive]
    if (r !== void 0) {
      const o = r.call(e, i || 'default')
      if (typeof o !== 'object') return o
      throw new TypeError('@@toPrimitive must return a primitive value.')
    }
    return (i === 'string' ? String : Number)(e)
  })(n, 'string')
  return typeof t === 'symbol' ? t : String(t)
}
(wt.prototype[
  (typeof Symbol === 'function' && Symbol.asyncIterator) || '@@asyncIterator'
] = function () {
  return this
}),
(wt.prototype.next = function (n) {
  return this._invoke('next', n)
}),
(wt.prototype.throw = function (n) {
  return this._invoke('throw', n)
}),
(wt.prototype.return = function (n) {
  return this._invoke('return', n)
})
const j = class extends O {
  static registerType (t, e) {
    (e.type = t), (this.types[t] = e)
  }

  static fromJSON (t) {
    const e = this.types[t.type]
    if (e) return e.fromJSON(t)
  }

  constructor (t) {
    const e =
            arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {}
    super(...arguments), (this.attributes = C.box(e))
  }

  copyWithAttributes (t) {
    return new this.constructor(this.getValue(), t)
  }

  copyWithAdditionalAttributes (t) {
    return this.copyWithAttributes(this.attributes.merge(t))
  }

  copyWithoutAttribute (t) {
    return this.copyWithAttributes(this.attributes.remove(t))
  }

  copy () {
    return this.copyWithAttributes(this.attributes)
  }

  getAttribute (t) {
    return this.attributes.get(t)
  }

  getAttributesHash () {
    return this.attributes
  }

  getAttributes () {
    return this.attributes.toObject()
  }

  hasAttribute (t) {
    return this.attributes.has(t)
  }

  hasSameStringValueAsPiece (t) {
    return t && this.toString() === t.toString()
  }

  hasSameAttributesAsPiece (t) {
    return (
      t &&
            (this.attributes === t.attributes ||
                this.attributes.isEqualTo(t.attributes))
    )
  }

  isBlockBreak () {
    return !1
  }

  isEqualTo (t) {
    return (
      super.isEqualTo(...arguments) ||
            (this.hasSameConstructorAs(t) &&
                this.hasSameStringValueAsPiece(t) &&
                this.hasSameAttributesAsPiece(t))
    )
  }

  isEmpty () {
    return this.length === 0
  }

  isSerializable () {
    return !0
  }

  toJSON () {
    return {
      type: this.constructor.type,
      attributes: this.getAttributes()
    }
  }

  contentsForInspection () {
    return {
      type: this.constructor.type,
      attributes: this.attributes.inspect()
    }
  }

  canBeGrouped () {
    return this.hasAttribute('href')
  }

  canBeGroupedWith (t) {
    return this.getAttribute('href') === t.getAttribute('href')
  }

  getLength () {
    return this.length
  }

  canBeConsolidatedWith (t) {
    return !1
  }
}
E(j, 'types', {})
const Wt = class extends at {
  constructor (t) {
    super(...arguments), (this.url = t)
  }

  perform (t) {
    const e = new Image();
    (e.onload = () => (
      (e.width = this.width = e.naturalWidth),
      (e.height = this.height = e.naturalHeight),
      t(!0, e)
    )),
    (e.onerror = () => t(!1)),
    (e.src = this.url)
  }
}
var H = class extends O {
  static attachmentForFile (t) {
    const e = new this(this.attributesForFile(t))
    return e.setFile(t), e
  }

  static attributesForFile (t) {
    return new C({
      filename: t.name,
      filesize: t.size,
      contentType: t.type
    })
  }

  static fromJSON (t) {
    return new this(t)
  }

  constructor () {
    const t =
            arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : {}
    super(t),
    (this.releaseFile = this.releaseFile.bind(this)),
    (this.attributes = C.box(t)),
    this.didChangeAttributes()
  }

  getAttribute (t) {
    return this.attributes.get(t)
  }

  hasAttribute (t) {
    return this.attributes.has(t)
  }

  getAttributes () {
    return this.attributes.toObject()
  }

  setAttributes () {
    const t =
            arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : {}
    const e = this.attributes.merge(t)
    let i, r, o, s
    if (!this.attributes.isEqualTo(e)) {
      return (
        (this.attributes = e),
        this.didChangeAttributes(),
        (i = this.previewDelegate) === null ||
                    i === void 0 ||
                    (r = i.attachmentDidChangeAttributes) === null ||
                    r === void 0 ||
                    r.call(i, this),
        (o = this.delegate) === null ||
                o === void 0 ||
                (s = o.attachmentDidChangeAttributes) === null ||
                s === void 0
          ? void 0
          : s.call(o, this)
      )
    }
  }

  didChangeAttributes () {
    if (this.isPreviewable()) return this.preloadURL()
  }

  isPending () {
    return this.file != null && !(this.getURL() || this.getHref())
  }

  isPreviewable () {
    return this.attributes.has('previewable')
      ? this.attributes.get('previewable')
      : H.previewablePattern.test(this.getContentType())
  }

  getType () {
    return this.hasContent()
      ? 'content'
      : this.isPreviewable()
        ? 'preview'
        : 'file'
  }

  getURL () {
    return this.attributes.get('url')
  }

  getHref () {
    return this.attributes.get('href')
  }

  getFilename () {
    return this.attributes.get('filename') || ''
  }

  getFilesize () {
    return this.attributes.get('filesize')
  }

  getFormattedFilesize () {
    const t = this.attributes.get('filesize')
    return typeof t === 'number' ? vi.formatter(t) : ''
  }

  getExtension () {
    let t
    return (t = this.getFilename().match(/\.(\w+)$/)) === null ||
            t === void 0
      ? void 0
      : t[1].toLowerCase()
  }

  getContentType () {
    return this.attributes.get('contentType')
  }

  hasContent () {
    return this.attributes.has('content')
  }

  getContent () {
    return this.attributes.get('content')
  }

  getWidth () {
    return this.attributes.get('width')
  }

  getHeight () {
    return this.attributes.get('height')
  }

  getFile () {
    return this.file
  }

  setFile (t) {
    if (((this.file = t), this.isPreviewable())) {
      return this.preloadFile()
    }
  }

  releaseFile () {
    this.releasePreloadedFile(), (this.file = null)
  }

  getUploadProgress () {
    return this.uploadProgress != null ? this.uploadProgress : 0
  }

  setUploadProgress (t) {
    let e, i
    if (this.uploadProgress !== t) {
      return (
        (this.uploadProgress = t),
        (e = this.uploadProgressDelegate) === null ||
                e === void 0 ||
                (i = e.attachmentDidChangeUploadProgress) === null ||
                i === void 0
          ? void 0
          : i.call(e, this)
      )
    }
  }

  toJSON () {
    return this.getAttributes()
  }

  getCacheKey () {
    return [
      super.getCacheKey(...arguments),
      this.attributes.getCacheKey(),
      this.getPreviewURL()
    ].join('/')
  }

  getPreviewURL () {
    return this.previewURL || this.preloadingURL
  }

  setPreviewURL (t) {
    let e, i, r, o
    if (t !== this.getPreviewURL()) {
      return (
        (this.previewURL = t),
        (e = this.previewDelegate) === null ||
                    e === void 0 ||
                    (i = e.attachmentDidChangeAttributes) === null ||
                    i === void 0 ||
                    i.call(e, this),
        (r = this.delegate) === null ||
                r === void 0 ||
                (o = r.attachmentDidChangePreviewURL) === null ||
                o === void 0
          ? void 0
          : o.call(r, this)
      )
    }
  }

  preloadURL () {
    return this.preload(this.getURL(), this.releaseFile)
  }

  preloadFile () {
    if (this.file) {
      return (
        (this.fileObjectURL = URL.createObjectURL(this.file)),
        this.preload(this.fileObjectURL)
      )
    }
  }

  releasePreloadedFile () {
    this.fileObjectURL &&
            (URL.revokeObjectURL(this.fileObjectURL),
            (this.fileObjectURL = null))
  }

  preload (t, e) {
    if (t && t !== this.getPreviewURL()) {
      return (
        (this.preloadingURL = t),
        new Wt(t)
          .then((i) => {
            const { width: r, height: o } = i
            return (
              (this.getWidth() && this.getHeight()) ||
                                this.setAttributes({ width: r, height: o }),
              (this.preloadingURL = null),
              this.setPreviewURL(t),
              e?.()
            )
          })
          .catch(() => ((this.preloadingURL = null), e?.()))
      )
    }
  }
}
E(H, 'previewablePattern', /^image(\/(gif|png|webp|jpe?g)|$)/)
var z = class extends j {
  static fromJSON (t) {
    return new this(H.fromJSON(t.attachment), t.attributes)
  }

  constructor (t) {
    super(...arguments),
    (this.attachment = t),
    (this.length = 1),
    this.ensureAttachmentExclusivelyHasAttribute('href'),
    this.attachment.hasContent() || this.removeProhibitedAttributes()
  }

  ensureAttachmentExclusivelyHasAttribute (t) {
    this.hasAttribute(t) &&
            (this.attachment.hasAttribute(t) ||
                this.attachment.setAttributes(this.attributes.slice([t])),
            (this.attributes = this.attributes.remove(t)))
  }

  removeProhibitedAttributes () {
    const t = this.attributes.slice(z.permittedAttributes)
    t.isEqualTo(this.attributes) || (this.attributes = t)
  }

  getValue () {
    return this.attachment
  }

  isSerializable () {
    return !this.attachment.isPending()
  }

  getCaption () {
    return this.attributes.get('caption') || ''
  }

  isEqualTo (t) {
    let e
    return (
      super.isEqualTo(t) &&
            this.attachment.id ===
                (t == null || (e = t.attachment) === null || e === void 0
                  ? void 0
                  : e.id)
    )
  }

  toString () {
    return '\uFFFC'
  }

  toJSON () {
    const t = super.toJSON(...arguments)
    return (t.attachment = this.attachment), t
  }

  getCacheKey () {
    return [
      super.getCacheKey(...arguments),
      this.attachment.getCacheKey()
    ].join('/')
  }

  toConsole () {
    return JSON.stringify(this.toString())
  }
}
E(z, 'permittedAttributes', ['caption', 'presentation']),
j.registerType('attachment', z)
const Rt = class extends j {
  static fromJSON (t) {
    return new this(t.string, t.attributes)
  }

  constructor (t) {
    super(...arguments),
    (this.string = ((e) =>
      e.replace(
        /\r\n?/g,
                    `
`
      ))(t)),
    (this.length = this.string.length)
  }

  getValue () {
    return this.string
  }

  toString () {
    return this.string.toString()
  }

  isBlockBreak () {
    return (
      this.toString() ===
                `
` && this.getAttribute('blockBreak') === !0
    )
  }

  toJSON () {
    const t = super.toJSON(...arguments)
    return (t.string = this.string), t
  }

  canBeConsolidatedWith (t) {
    return (
      t &&
            this.hasSameConstructorAs(t) &&
            this.hasSameAttributesAsPiece(t)
    )
  }

  consolidateWith (t) {
    return new this.constructor(
      this.toString() + t.toString(),
      this.attributes
    )
  }

  splitAtOffset (t) {
    let e, i
    return (
      t === 0
        ? ((e = null), (i = this))
        : t === this.length
          ? ((e = this), (i = null))
          : ((e = new this.constructor(
              this.string.slice(0, t),
              this.attributes
            )),
            (i = new this.constructor(
              this.string.slice(t),
              this.attributes
            ))),
      [e, i]
    )
  }

  toConsole () {
    let { string: t } = this
    return (
      t.length > 15 && (t = t.slice(0, 14) + '\u2026'),
      JSON.stringify(t.toString())
    )
  }
}
j.registerType('string', Rt)
const ut = class extends O {
  static box (t) {
    return t instanceof this ? t : new this(t)
  }

  constructor () {
    const t =
            arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : []
    super(...arguments),
    (this.objects = t.slice(0)),
    (this.length = this.objects.length)
  }

  indexOf (t) {
    return this.objects.indexOf(t)
  }

  splice () {
    for (var t = arguments.length, e = new Array(t), i = 0; i < t; i++) {
      e[i] = arguments[i]
    }
    return new this.constructor(Ve(this.objects, ...e))
  }

  eachObject (t) {
    return this.objects.map((e, i) => t(e, i))
  }

  insertObjectAtIndex (t, e) {
    return this.splice(e, 0, t)
  }

  insertSplittableListAtIndex (t, e) {
    return this.splice(e, 0, ...t.objects)
  }

  insertSplittableListAtPosition (t, e) {
    const [i, r] = this.splitObjectAtPosition(e)
    return new this.constructor(i).insertSplittableListAtIndex(t, r)
  }

  editObjectAtIndex (t, e) {
    return this.replaceObjectAtIndex(e(this.objects[t]), t)
  }

  replaceObjectAtIndex (t, e) {
    return this.splice(e, 1, t)
  }

  removeObjectAtIndex (t) {
    return this.splice(t, 1)
  }

  getObjectAtIndex (t) {
    return this.objects[t]
  }

  getSplittableListInRange (t) {
    const [e, i, r] = this.splitObjectsAtRange(t)
    return new this.constructor(e.slice(i, r + 1))
  }

  selectSplittableList (t) {
    const e = this.objects.filter((i) => t(i))
    return new this.constructor(e)
  }

  removeObjectsInRange (t) {
    const [e, i, r] = this.splitObjectsAtRange(t)
    return new this.constructor(e).splice(i, r - i + 1)
  }

  transformObjectsInRange (t, e) {
    const [i, r, o] = this.splitObjectsAtRange(t)
    const s = i.map((a, l) => (r <= l && l <= o ? e(a) : a))
    return new this.constructor(s)
  }

  splitObjectsAtRange (t) {
    let e
    let [i, r, o] = this.splitObjectAtPosition(mn(t))
    return (
      ([i, e] = new this.constructor(i).splitObjectAtPosition(pn(t) + o)),
      [i, r, e - 1]
    )
  }

  getObjectAtPosition (t) {
    const { index: e } = this.findIndexAndOffsetAtPosition(t)
    return this.objects[e]
  }

  splitObjectAtPosition (t) {
    let e
    let i
    const { index: r, offset: o } = this.findIndexAndOffsetAtPosition(t)
    const s = this.objects.slice(0)
    if (r != null) {
      if (o === 0) (e = r), (i = 0)
      else {
        const a = this.getObjectAtIndex(r)
        const [l, c] = a.splitAtOffset(o)
        s.splice(r, 1, l, c), (e = r + 1), (i = l.getLength() - o)
      }
    } else (e = s.length), (i = 0)
    return [s, e, i]
  }

  consolidate () {
    const t = []
    let e = this.objects[0]
    return (
      this.objects.slice(1).forEach((i) => {
        let r, o;
        (r = (o = e).canBeConsolidatedWith) !== null &&
                r !== void 0 &&
                r.call(o, i)
          ? (e = e.consolidateWith(i))
          : (t.push(e), (e = i))
      }),
      e && t.push(e),
      new this.constructor(t)
    )
  }

  consolidateFromIndexToIndex (t, e) {
    const i = this.objects.slice(0).slice(t, e + 1)
    const r = new this.constructor(i).consolidate().toArray()
    return this.splice(t, i.length, ...r)
  }

  findIndexAndOffsetAtPosition (t) {
    let e
    let i = 0
    for (e = 0; e < this.objects.length; e++) {
      const r = i + this.objects[e].getLength()
      if (i <= t && t < r) return { index: e, offset: t - i }
      i = r
    }
    return { index: null, offset: null }
  }

  findPositionAtIndexAndOffset (t, e) {
    let i = 0
    for (let r = 0; r < this.objects.length; r++) {
      const o = this.objects[r]
      if (r < t) i += o.getLength()
      else if (r === t) {
        i += e
        break
      }
    }
    return i
  }

  getEndPosition () {
    return (
      this.endPosition == null &&
                ((this.endPosition = 0),
                this.objects.forEach(
                  (t) => (this.endPosition += t.getLength())
                )),
      this.endPosition
    )
  }

  toString () {
    return this.objects.join('')
  }

  toArray () {
    return this.objects.slice(0)
  }

  toJSON () {
    return this.toArray()
  }

  isEqualTo (t) {
    return super.isEqualTo(...arguments) || gn(this.objects, t?.objects)
  }

  contentsForInspection () {
    return {
      objects: '['.concat(
        this.objects.map((t) => t.inspect()).join(', '),
        ']'
      )
    }
  }
}
var gn = function (n) {
  const t =
        arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : []
  if (n.length !== t.length) return !1
  let e = !0
  for (let i = 0; i < n.length; i++) {
    const r = n[i]
    e && !r.isEqualTo(t[i]) && (e = !1)
  }
  return e
}
var mn = (n) => n[0]
var pn = (n) => n[1]
const R = class extends O {
  static textForAttachmentWithAttributes (t, e) {
    return new this([new z(t, e)])
  }

  static textForStringWithAttributes (t, e) {
    return new this([new Rt(t, e)])
  }

  static fromJSON (t) {
    return new this(Array.from(t).map((e) => j.fromJSON(e)))
  }

  constructor () {
    const t =
            arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : []
    super(...arguments)
    const e = t.filter((i) => !i.isEmpty())
    this.pieceList = new ut(e)
  }

  copy () {
    return this.copyWithPieceList(this.pieceList)
  }

  copyWithPieceList (t) {
    return new this.constructor(t.consolidate().toArray())
  }

  copyUsingObjectMap (t) {
    const e = this.getPieces().map((i) => t.find(i) || i)
    return new this.constructor(e)
  }

  appendText (t) {
    return this.insertTextAtPosition(t, this.getLength())
  }

  insertTextAtPosition (t, e) {
    return this.copyWithPieceList(
      this.pieceList.insertSplittableListAtPosition(t.pieceList, e)
    )
  }

  removeTextAtRange (t) {
    return this.copyWithPieceList(this.pieceList.removeObjectsInRange(t))
  }

  replaceTextAtRange (t, e) {
    return this.removeTextAtRange(e).insertTextAtPosition(t, e[0])
  }

  moveTextFromRangeToPosition (t, e) {
    if (t[0] <= e && e <= t[1]) return
    const i = this.getTextAtRange(t)
    const r = i.getLength()
    return (
      t[0] < e && (e -= r),
      this.removeTextAtRange(t).insertTextAtPosition(i, e)
    )
  }

  addAttributeAtRange (t, e, i) {
    const r = {}
    return (r[t] = e), this.addAttributesAtRange(r, i)
  }

  addAttributesAtRange (t, e) {
    return this.copyWithPieceList(
      this.pieceList.transformObjectsInRange(e, (i) =>
        i.copyWithAdditionalAttributes(t)
      )
    )
  }

  removeAttributeAtRange (t, e) {
    return this.copyWithPieceList(
      this.pieceList.transformObjectsInRange(e, (i) =>
        i.copyWithoutAttribute(t)
      )
    )
  }

  setAttributesAtRange (t, e) {
    return this.copyWithPieceList(
      this.pieceList.transformObjectsInRange(e, (i) =>
        i.copyWithAttributes(t)
      )
    )
  }

  getAttributesAtPosition (t) {
    let e
    return (
      ((e = this.pieceList.getObjectAtPosition(t)) === null ||
            e === void 0
        ? void 0
        : e.getAttributes()) || {}
    )
  }

  getCommonAttributes () {
    const t = Array.from(this.pieceList.toArray()).map((e) =>
      e.getAttributes()
    )
    return C.fromCommonAttributesOfObjects(t).toObject()
  }

  getCommonAttributesAtRange (t) {
    return this.getTextAtRange(t).getCommonAttributes() || {}
  }

  getExpandedRangeForAttributeAtOffset (t, e) {
    let i
    let r = (i = e)
    const o = this.getLength()
    for (; r > 0 && this.getCommonAttributesAtRange([r - 1, i])[t];) {
      r--
    }
    for (; i < o && this.getCommonAttributesAtRange([e, i + 1])[t];) {
      i++
    }
    return [r, i]
  }

  getTextAtRange (t) {
    return this.copyWithPieceList(
      this.pieceList.getSplittableListInRange(t)
    )
  }

  getStringAtRange (t) {
    return this.pieceList.getSplittableListInRange(t).toString()
  }

  getStringAtPosition (t) {
    return this.getStringAtRange([t, t + 1])
  }

  startsWithString (t) {
    return this.getStringAtRange([0, t.length]) === t
  }

  endsWithString (t) {
    const e = this.getLength()
    return this.getStringAtRange([e - t.length, e]) === t
  }

  getAttachmentPieces () {
    return this.pieceList.toArray().filter((t) => !!t.attachment)
  }

  getAttachments () {
    return this.getAttachmentPieces().map((t) => t.attachment)
  }

  getAttachmentAndPositionById (t) {
    let e = 0
    for (const r of this.pieceList.toArray()) {
      var i
      if (
        ((i = r.attachment) === null || i === void 0
          ? void 0
          : i.id) === t
      ) {
        return { attachment: r.attachment, position: e }
      }
      e += r.length
    }
    return { attachment: null, position: null }
  }

  getAttachmentById (t) {
    const { attachment: e } = this.getAttachmentAndPositionById(t)
    return e
  }

  getRangeOfAttachment (t) {
    const e = this.getAttachmentAndPositionById(t.id)
    const i = e.position
    if ((t = e.attachment)) return [i, i + 1]
  }

  updateAttributesForAttachment (t, e) {
    const i = this.getRangeOfAttachment(e)
    return i ? this.addAttributesAtRange(t, i) : this
  }

  getLength () {
    return this.pieceList.getEndPosition()
  }

  isEmpty () {
    return this.getLength() === 0
  }

  isEqualTo (t) {
    let e
    return (
      super.isEqualTo(t) ||
            (t == null || (e = t.pieceList) === null || e === void 0
              ? void 0
              : e.isEqualTo(this.pieceList))
    )
  }

  isBlockBreak () {
    return (
      this.getLength() === 1 &&
            this.pieceList.getObjectAtIndex(0).isBlockBreak()
    )
  }

  eachPiece (t) {
    return this.pieceList.eachObject(t)
  }

  getPieces () {
    return this.pieceList.toArray()
  }

  getPieceAtPosition (t) {
    return this.pieceList.getObjectAtPosition(t)
  }

  contentsForInspection () {
    return { pieceList: this.pieceList.inspect() }
  }

  toSerializableText () {
    const t = this.pieceList.selectSplittableList((e) =>
      e.isSerializable()
    )
    return this.copyWithPieceList(t)
  }

  toString () {
    return this.pieceList.toString()
  }

  toJSON () {
    return this.pieceList.toJSON()
  }

  toConsole () {
    return JSON.stringify(
      this.pieceList.toArray().map((t) => JSON.parse(t.toConsole()))
    )
  }

  getDirection () {
    return $i(this.toString())
  }

  isRTL () {
    return this.getDirection() === 'rtl'
  }
}
var S = class extends O {
  static fromJSON (t) {
    return new this(R.fromJSON(t.text), t.attributes, t.htmlAttributes)
  }

  constructor (t, e, i) {
    super(...arguments),
    (this.text = fn(t || new R())),
    (this.attributes = e || []),
    (this.htmlAttributes = i || {})
  }

  isEmpty () {
    return this.text.isBlockBreak()
  }

  isEqualTo (t) {
    return (
      !!super.isEqualTo(t) ||
            (this.text.isEqualTo(t?.text) &&
                Q(this.attributes, t?.attributes) &&
                dt(this.htmlAttributes, t?.htmlAttributes))
    )
  }

  copyWithText (t) {
    return new S(t, this.attributes, this.htmlAttributes)
  }

  copyWithoutText () {
    return this.copyWithText(null)
  }

  copyWithAttributes (t) {
    return new S(this.text, t, this.htmlAttributes)
  }

  copyWithoutAttributes () {
    return this.copyWithAttributes(null)
  }

  copyUsingObjectMap (t) {
    const e = t.find(this.text)
    return e
      ? this.copyWithText(e)
      : this.copyWithText(this.text.copyUsingObjectMap(t))
  }

  addAttribute (t) {
    const e = this.attributes.concat(si(t))
    return this.copyWithAttributes(e)
  }

  addHTMLAttribute (t, e) {
    const i = Object.assign({}, this.htmlAttributes, { [t]: e })
    return new S(this.text, this.attributes, i)
  }

  removeAttribute (t) {
    const { listAttribute: e } = v(t)
    const i = li(li(this.attributes, t), e)
    return this.copyWithAttributes(i)
  }

  removeLastAttribute () {
    return this.removeAttribute(this.getLastAttribute())
  }

  getLastAttribute () {
    return ai(this.attributes)
  }

  getAttributes () {
    return this.attributes.slice(0)
  }

  getAttributeLevel () {
    return this.attributes.length
  }

  getAttributeAtLevel (t) {
    return this.attributes[t - 1]
  }

  hasAttribute (t) {
    return this.attributes.includes(t)
  }

  hasAttributes () {
    return this.getAttributeLevel() > 0
  }

  getLastNestableAttribute () {
    return ai(this.getNestableAttributes())
  }

  getNestableAttributes () {
    return this.attributes.filter((t) => v(t).nestable)
  }

  getNestingLevel () {
    return this.getNestableAttributes().length
  }

  decreaseNestingLevel () {
    const t = this.getLastNestableAttribute()
    return t ? this.removeAttribute(t) : this
  }

  increaseNestingLevel () {
    const t = this.getLastNestableAttribute()
    if (t) {
      const e = this.attributes.lastIndexOf(t)
      const i = Ve(this.attributes, e + 1, 0, ...si(t))
      return this.copyWithAttributes(i)
    }
    return this
  }

  getListItemAttributes () {
    return this.attributes.filter((t) => v(t).listAttribute)
  }

  isListItem () {
    let t
    return (t = v(this.getLastAttribute())) === null || t === void 0
      ? void 0
      : t.listAttribute
  }

  isTerminalBlock () {
    let t
    return (t = v(this.getLastAttribute())) === null || t === void 0
      ? void 0
      : t.terminal
  }

  breaksOnReturn () {
    let t
    return (t = v(this.getLastAttribute())) === null || t === void 0
      ? void 0
      : t.breakOnReturn
  }

  findLineBreakInDirectionFromPosition (t, e) {
    const i = this.toString()
    let r
    switch (t) {
      case 'forward':
        r = i.indexOf(
                    `
`,
                    e
        )
        break
      case 'backward':
        r = i.slice(0, e).lastIndexOf(`
`)
    }
    if (r !== -1) return r
  }

  contentsForInspection () {
    return { text: this.text.inspect(), attributes: this.attributes }
  }

  toString () {
    return this.text.toString()
  }

  toJSON () {
    return {
      text: this.text,
      attributes: this.attributes,
      htmlAttributes: this.htmlAttributes
    }
  }

  getDirection () {
    return this.text.getDirection()
  }

  isRTL () {
    return this.text.isRTL()
  }

  getLength () {
    return this.text.getLength()
  }

  canBeConsolidatedWith (t) {
    return (
      !this.hasAttributes() &&
            !t.hasAttributes() &&
            this.getDirection() === t.getDirection()
    )
  }

  consolidateWith (t) {
    const e = R.textForStringWithAttributes(`
`)
    const i = this.getTextWithoutBlockBreak().appendText(e)
    return this.copyWithText(i.appendText(t.text))
  }

  splitAtOffset (t) {
    let e, i
    return (
      t === 0
        ? ((e = null), (i = this))
        : t === this.getLength()
          ? ((e = this), (i = null))
          : ((e = this.copyWithText(this.text.getTextAtRange([0, t]))),
            (i = this.copyWithText(
              this.text.getTextAtRange([t, this.getLength()])
            ))),
      [e, i]
    )
  }

  getBlockBreakPosition () {
    return this.text.getLength() - 1
  }

  getTextWithoutBlockBreak () {
    return wi(this.text)
      ? this.text.getTextAtRange([0, this.getBlockBreakPosition()])
      : this.text.copy()
  }

  canBeGrouped (t) {
    return this.attributes[t]
  }

  canBeGroupedWith (t, e) {
    const i = t.getAttributes()
    const r = i[e]
    const o = this.attributes[e]
    return (
      o === r &&
            !(
              v(o).group === !1 &&
                !(() => {
                  if (!Dt) {
                    Dt = []
                    for (const s in y) {
                      const { listAttribute: a } = y[s]
                      a != null && Dt.push(a)
                    }
                  }
                  return Dt
                })().includes(i[e + 1])
            ) &&
            (this.getDirection() === t.getDirection() || t.isEmpty())
    )
  }
}
var fn = function (n) {
  return (n = bn(n)), (n = An(n))
}
var bn = function (n) {
  let t = !1
  const e = n.getPieces()
  let i = e.slice(0, e.length - 1)
  const r = e[e.length - 1]
  return r
    ? ((i = i.map((o) => (o.isBlockBreak() ? ((t = !0), xn(o)) : o))),
      t ? new R([...i, r]) : n)
    : n
}
const vn = R.textForStringWithAttributes(
    `
`,
    { blockBreak: !0 }
)
var An = function (n) {
  return wi(n) ? n : n.appendText(vn)
}
var wi = function (n) {
  const t = n.getLength()
  return t === 0 ? !1 : n.getTextAtRange([t - 1, t]).isBlockBreak()
}
var xn = (n) => n.copyWithoutAttribute('blockBreak')
var si = function (n) {
  const { listAttribute: t } = v(n)
  return t ? [t, n] : [n]
}
var ai = (n) => n.slice(-1)[0]
var li = function (n, t) {
  const e = n.lastIndexOf(t)
  return e === -1 ? n : Ve(n, e, 1)
}
const k = class extends O {
  static fromJSON (t) {
    return new this(Array.from(t).map((e) => S.fromJSON(e)))
  }

  static fromString (t, e) {
    const i = R.textForStringWithAttributes(t, e)
    return new this([new S(i)])
  }

  constructor () {
    let t =
            arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : []
    super(...arguments),
    t.length === 0 && (t = [new S()]),
    (this.blockList = ut.box(t))
  }

  isEmpty () {
    const t = this.getBlockAtIndex(0)
    return this.blockList.length === 1 && t.isEmpty() && !t.hasAttributes()
  }

  copy () {
    const t = (arguments.length > 0 && arguments[0] !== void 0
      ? arguments[0]
      : {}
    ).consolidateBlocks
      ? this.blockList.consolidate().toArray()
      : this.blockList.toArray()
    return new this.constructor(t)
  }

  copyUsingObjectsFromDocument (t) {
    const e = new Te(t.getObjects())
    return this.copyUsingObjectMap(e)
  }

  copyUsingObjectMap (t) {
    const e = this.getBlocks().map(
      (i) => t.find(i) || i.copyUsingObjectMap(t)
    )
    return new this.constructor(e)
  }

  copyWithBaseBlockAttributes () {
    const t =
            arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : []
    const e = this.getBlocks().map((i) => {
      const r = t.concat(i.getAttributes())
      return i.copyWithAttributes(r)
    })
    return new this.constructor(e)
  }

  replaceBlock (t, e) {
    const i = this.blockList.indexOf(t)
    return i === -1
      ? this
      : new this.constructor(this.blockList.replaceObjectAtIndex(e, i))
  }

  insertDocumentAtRange (t, e) {
    const { blockList: i } = t
    e = g(e)
    let [r] = e
    const { index: o, offset: s } = this.locationFromPosition(r)
    let a = this
    const l = this.getBlockAtPosition(r)
    return (
      N(e) && l.isEmpty() && !l.hasAttributes()
        ? (a = new this.constructor(a.blockList.removeObjectAtIndex(o)))
        : l.getBlockBreakPosition() === s && r++,
      (a = a.removeTextAtRange(e)),
      new this.constructor(
        a.blockList.insertSplittableListAtPosition(i, r)
      )
    )
  }

  mergeDocumentAtRange (t, e) {
    let i, r
    e = g(e)
    const [o] = e
    const s = this.locationFromPosition(o)
    const a = this.getBlockAtIndex(s.index).getAttributes()
    const l = t.getBaseBlockAttributes()
    const c = a.slice(-l.length)
    if (Q(l, c)) {
      const A = a.slice(0, -l.length)
      i = t.copyWithBaseBlockAttributes(A)
    } else {
      i = t
        .copy({ consolidateBlocks: !0 })
        .copyWithBaseBlockAttributes(a)
    }
    const u = i.getBlockCount()
    const b = i.getBlockAtIndex(0)
    if (Q(a, b.getAttributes())) {
      const A = b.getTextWithoutBlockBreak()
      if (((r = this.insertTextAtRange(A, e)), u > 1)) {
        i = new this.constructor(i.getBlocks().slice(1))
        const L = o + A.getLength()
        r = r.insertDocumentAtRange(i, L)
      }
    } else r = this.insertDocumentAtRange(i, e)
    return r
  }

  insertTextAtRange (t, e) {
    e = g(e)
    const [i] = e
    const { index: r, offset: o } = this.locationFromPosition(i)
    const s = this.removeTextAtRange(e)
    return new this.constructor(
      s.blockList.editObjectAtIndex(r, (a) =>
        a.copyWithText(a.text.insertTextAtPosition(t, o))
      )
    )
  }

  removeTextAtRange (t) {
    let e
    t = g(t)
    const [i, r] = t
    if (N(t)) return this
    const [o, s] = Array.from(this.locationRangeFromRange(t))
    const a = o.index
    const l = o.offset
    const c = this.getBlockAtIndex(a)
    const u = s.index
    const b = s.offset
    const A = this.getBlockAtIndex(u)
    if (
      r - i == 1 &&
            c.getBlockBreakPosition() === l &&
            A.getBlockBreakPosition() !== b &&
            A.text.getStringAtPosition(b) ===
                `
`
    ) {
      e = this.blockList.editObjectAtIndex(u, (L) =>
        L.copyWithText(L.text.removeTextAtRange([b, b + 1]))
      )
    } else {
      let L
      const gt = c.text.getTextAtRange([0, l])
      const P = A.text.getTextAtRange([b, A.getLength()])
      const it = gt.appendText(P)
      L =
                a !== u &&
                l === 0 &&
                c.getAttributeLevel() >= A.getAttributeLevel()
                  ? A.copyWithText(it)
                  : c.copyWithText(it)
      const mt = u + 1 - a
      e = this.blockList.splice(a, mt, L)
    }
    return new this.constructor(e)
  }

  moveTextFromRangeToPosition (t, e) {
    let i
    t = g(t)
    const [r, o] = t
    if (r <= e && e <= o) return this
    let s = this.getDocumentAtRange(t)
    let a = this.removeTextAtRange(t)
    const l = r < e
    l && (e -= s.getLength())
    const [c, ...u] = s.getBlocks()
    return (
      u.length === 0
        ? ((i = c.getTextWithoutBlockBreak()), l && (e += 1))
        : (i = c.text),
      (a = a.insertTextAtRange(i, e)),
      u.length === 0
        ? a
        : ((s = new this.constructor(u)),
          (e += i.getLength()),
          a.insertDocumentAtRange(s, e))
    )
  }

  addAttributeAtRange (t, e, i) {
    let { blockList: r } = this
    return (
      this.eachBlockAtRange(
        i,
        (o, s, a) =>
          (r = r.editObjectAtIndex(a, function () {
            return v(t)
              ? o.addAttribute(t, e)
              : s[0] === s[1]
                ? o
                : o.copyWithText(
                  o.text.addAttributeAtRange(t, e, s)
                )
          }))
      ),
      new this.constructor(r)
    )
  }

  addAttribute (t, e) {
    let { blockList: i } = this
    return (
      this.eachBlock(
        (r, o) =>
          (i = i.editObjectAtIndex(o, () => r.addAttribute(t, e)))
      ),
      new this.constructor(i)
    )
  }

  removeAttributeAtRange (t, e) {
    let { blockList: i } = this
    return (
      this.eachBlockAtRange(e, function (r, o, s) {
        v(t)
          ? (i = i.editObjectAtIndex(s, () => r.removeAttribute(t)))
          : o[0] !== o[1] &&
                      (i = i.editObjectAtIndex(s, () =>
                        r.copyWithText(r.text.removeAttributeAtRange(t, o))
                      ))
      }),
      new this.constructor(i)
    )
  }

  updateAttributesForAttachment (t, e) {
    const i = this.getRangeOfAttachment(e)
    const [r] = Array.from(i)
    const { index: o } = this.locationFromPosition(r)
    const s = this.getTextAtIndex(o)
    return new this.constructor(
      this.blockList.editObjectAtIndex(o, (a) =>
        a.copyWithText(s.updateAttributesForAttachment(t, e))
      )
    )
  }

  removeAttributeForAttachment (t, e) {
    const i = this.getRangeOfAttachment(e)
    return this.removeAttributeAtRange(t, i)
  }

  setHTMLAttributeAtPosition (t, e, i) {
    const r = this.getBlockAtPosition(t)
    const o = r.addHTMLAttribute(e, i)
    return this.replaceBlock(r, o)
  }

  insertBlockBreakAtRange (t) {
    let e
    t = g(t)
    const [i] = t
    const { offset: r } = this.locationFromPosition(i)
    const o = this.removeTextAtRange(t)
    return (
      r === 0 && (e = [new S()]),
      new this.constructor(
        o.blockList.insertSplittableListAtPosition(new ut(e), i)
      )
    )
  }

  applyBlockAttributeAtRange (t, e, i) {
    const r = this.expandRangeToLineBreaksAndSplitBlocks(i)
    let o = r.document
    i = r.range
    const s = v(t)
    if (s.listAttribute) {
      o = o.removeLastListAttributeAtRange(i, {
        exceptAttributeName: t
      })
      const a = o.convertLineBreaksToBlockBreaksInRange(i);
      (o = a.document), (i = a.range)
    } else {
      o = s.exclusive
        ? o.removeBlockAttributesAtRange(i)
        : s.terminal
          ? o.removeLastTerminalAttributeAtRange(i)
          : o.consolidateBlocksAtRange(i)
    }
    return o.addAttributeAtRange(t, e, i)
  }

  removeLastListAttributeAtRange (t) {
    const e =
            arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {}
    let { blockList: i } = this
    return (
      this.eachBlockAtRange(t, function (r, o, s) {
        const a = r.getLastAttribute()
        a &&
                    v(a).listAttribute &&
                    a !== e.exceptAttributeName &&
                    (i = i.editObjectAtIndex(s, () => r.removeAttribute(a)))
      }),
      new this.constructor(i)
    )
  }

  removeLastTerminalAttributeAtRange (t) {
    let { blockList: e } = this
    return (
      this.eachBlockAtRange(t, function (i, r, o) {
        const s = i.getLastAttribute()
        s &&
                    v(s).terminal &&
                    (e = e.editObjectAtIndex(o, () => i.removeAttribute(s)))
      }),
      new this.constructor(e)
    )
  }

  removeBlockAttributesAtRange (t) {
    let { blockList: e } = this
    return (
      this.eachBlockAtRange(t, function (i, r, o) {
        i.hasAttributes() &&
                    (e = e.editObjectAtIndex(o, () =>
                      i.copyWithoutAttributes()
                    ))
      }),
      new this.constructor(e)
    )
  }

  expandRangeToLineBreaksAndSplitBlocks (t) {
    let e
    t = g(t)
    let [i, r] = t
    const o = this.locationFromPosition(i)
    const s = this.locationFromPosition(r)
    let a = this
    const l = a.getBlockAtIndex(o.index)
    if (
      ((o.offset = l.findLineBreakInDirectionFromPosition(
        'backward',
        o.offset
      )),
      o.offset != null &&
                ((e = a.positionFromLocation(o)),
                (a = a.insertBlockBreakAtRange([e, e + 1])),
                (s.index += 1),
                (s.offset -= a.getBlockAtIndex(o.index).getLength()),
                (o.index += 1)),
      (o.offset = 0),
      s.offset === 0 && s.index > o.index)
    ) {
      (s.index -= 1),
      (s.offset = a.getBlockAtIndex(s.index).getBlockBreakPosition())
    } else {
      const c = a.getBlockAtIndex(s.index)
      c.text.getStringAtRange([s.offset - 1, s.offset]) ===
            `
`
        ? (s.offset -= 1)
        : (s.offset = c.findLineBreakInDirectionFromPosition(
            'forward',
            s.offset
          )),
      s.offset !== c.getBlockBreakPosition() &&
                    ((e = a.positionFromLocation(s)),
                    (a = a.insertBlockBreakAtRange([e, e + 1])))
    }
    return (
      (i = a.positionFromLocation(o)),
      (r = a.positionFromLocation(s)),
      { document: a, range: (t = g([i, r])) }
    )
  }

  convertLineBreaksToBlockBreaksInRange (t) {
    t = g(t)
    let [e] = t
    const i = this.getStringAtRange(t).slice(0, -1)
    let r = this
    return (
      i.replace(/.*?\n/g, function (o) {
        (e += o.length), (r = r.insertBlockBreakAtRange([e - 1, e]))
      }),
      { document: r, range: t }
    )
  }

  consolidateBlocksAtRange (t) {
    t = g(t)
    const [e, i] = t
    const r = this.locationFromPosition(e).index
    const o = this.locationFromPosition(i).index
    return new this.constructor(
      this.blockList.consolidateFromIndexToIndex(r, o)
    )
  }

  getDocumentAtRange (t) {
    t = g(t)
    const e = this.blockList.getSplittableListInRange(t).toArray()
    return new this.constructor(e)
  }

  getStringAtRange (t) {
    let e
    const i = (t = g(t))
    return (
      i[i.length - 1] !== this.getLength() && (e = -1),
      this.getDocumentAtRange(t).toString().slice(0, e)
    )
  }

  getBlockAtIndex (t) {
    return this.blockList.getObjectAtIndex(t)
  }

  getBlockAtPosition (t) {
    const { index: e } = this.locationFromPosition(t)
    return this.getBlockAtIndex(e)
  }

  getTextAtIndex (t) {
    let e
    return (e = this.getBlockAtIndex(t)) === null || e === void 0
      ? void 0
      : e.text
  }

  getTextAtPosition (t) {
    const { index: e } = this.locationFromPosition(t)
    return this.getTextAtIndex(e)
  }

  getPieceAtPosition (t) {
    const { index: e, offset: i } = this.locationFromPosition(t)
    return this.getTextAtIndex(e).getPieceAtPosition(i)
  }

  getCharacterAtPosition (t) {
    const { index: e, offset: i } = this.locationFromPosition(t)
    return this.getTextAtIndex(e).getStringAtRange([i, i + 1])
  }

  getLength () {
    return this.blockList.getEndPosition()
  }

  getBlocks () {
    return this.blockList.toArray()
  }

  getBlockCount () {
    return this.blockList.length
  }

  getEditCount () {
    return this.editCount
  }

  eachBlock (t) {
    return this.blockList.eachObject(t)
  }

  eachBlockAtRange (t, e) {
    let i, r
    t = g(t)
    const [o, s] = t
    const a = this.locationFromPosition(o)
    const l = this.locationFromPosition(s)
    if (a.index === l.index) {
      return (
        (i = this.getBlockAtIndex(a.index)),
        (r = [a.offset, l.offset]),
        e(i, r, a.index)
      )
    }
    for (let c = a.index; c <= l.index; c++) {
      if (((i = this.getBlockAtIndex(c)), i)) {
        switch (c) {
          case a.index:
            r = [a.offset, i.text.getLength()]
            break
          case l.index:
            r = [0, l.offset]
            break
          default:
            r = [0, i.text.getLength()]
        }
        e(i, r, c)
      }
    }
  }

  getCommonAttributesAtRange (t) {
    t = g(t)
    const [e] = t
    if (N(t)) return this.getCommonAttributesAtPosition(e)
    {
      const i = []
      const r = []
      return (
        this.eachBlockAtRange(t, function (o, s) {
          if (s[0] !== s[1]) {
            return (
              i.push(o.text.getCommonAttributesAtRange(s)),
              r.push(ci(o))
            )
          }
        }),
        C.fromCommonAttributesOfObjects(i)
          .merge(C.fromCommonAttributesOfObjects(r))
          .toObject()
      )
    }
  }

  getCommonAttributesAtPosition (t) {
    let e
    let i
    const { index: r, offset: o } = this.locationFromPosition(t)
    const s = this.getBlockAtIndex(r)
    if (!s) return {}
    const a = ci(s)
    const l = s.text.getAttributesAtPosition(o)
    const c = s.text.getAttributesAtPosition(o - 1)
    const u = Object.keys(Y).filter((b) => Y[b].inheritable)
    for (e in c) {
      (i = c[e]), (i === l[e] || u.includes(e)) && (a[e] = i)
    }
    return a
  }

  getRangeOfCommonAttributeAtPosition (t, e) {
    const { index: i, offset: r } = this.locationFromPosition(e)
    const o = this.getTextAtIndex(i)
    const [s, a] = Array.from(o.getExpandedRangeForAttributeAtOffset(t, r))
    const l = this.positionFromLocation({ index: i, offset: s })
    const c = this.positionFromLocation({ index: i, offset: a })
    return g([l, c])
  }

  getBaseBlockAttributes () {
    let t = this.getBlockAtIndex(0).getAttributes()
    for (let e = 1; e < this.getBlockCount(); e++) {
      const i = this.getBlockAtIndex(e).getAttributes()
      const r = Math.min(t.length, i.length)
      t = (() => {
        const o = []
        for (let s = 0; s < r && i[s] === t[s]; s++) o.push(i[s])
        return o
      })()
    }
    return t
  }

  getAttachmentById (t) {
    for (const e of this.getAttachments()) if (e.id === t) return e
  }

  getAttachmentPieces () {
    let t = []
    return (
      this.blockList.eachObject((e) => {
        const { text: i } = e
        return (t = t.concat(i.getAttachmentPieces()))
      }),
      t
    )
  }

  getAttachments () {
    return this.getAttachmentPieces().map((t) => t.attachment)
  }

  getRangeOfAttachment (t) {
    let e = 0
    const i = this.blockList.toArray()
    for (let r = 0; r < i.length; r++) {
      const { text: o } = i[r]
      const s = o.getRangeOfAttachment(t)
      if (s) return g([e + s[0], e + s[1]])
      e += o.getLength()
    }
  }

  getLocationRangeOfAttachment (t) {
    const e = this.getRangeOfAttachment(t)
    return this.locationRangeFromRange(e)
  }

  getAttachmentPieceForAttachment (t) {
    for (const e of this.getAttachmentPieces()) {
      if (e.attachment === t) return e
    }
  }

  findRangesForBlockAttribute (t) {
    let e = 0
    const i = []
    return (
      this.getBlocks().forEach((r) => {
        const o = r.getLength()
        r.hasAttribute(t) && i.push([e, e + o]), (e += o)
      }),
      i
    )
  }

  findRangesForTextAttribute (t) {
    const { withValue: e } =
            arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {}
    let i = 0
    let r = []
    const o = []
    return (
      this.getPieces().forEach((s) => {
        const a = s.getLength();
        (function (l) {
          return e ? l.getAttribute(t) === e : l.hasAttribute(t)
        })(s) &&
                    (r[1] === i ? (r[1] = i + a) : o.push((r = [i, i + a]))),
        (i += a)
      }),
      o
    )
  }

  locationFromPosition (t) {
    const e = this.blockList.findIndexAndOffsetAtPosition(Math.max(0, t))
    if (e.index != null) return e
    {
      const i = this.getBlocks()
      return {
        index: i.length - 1,
        offset: i[i.length - 1].getLength()
      }
    }
  }

  positionFromLocation (t) {
    return this.blockList.findPositionAtIndexAndOffset(t.index, t.offset)
  }

  locationRangeFromPosition (t) {
    return g(this.locationFromPosition(t))
  }

  locationRangeFromRange (t) {
    if (!(t = g(t))) return
    const [e, i] = Array.from(t)
    const r = this.locationFromPosition(e)
    const o = this.locationFromPosition(i)
    return g([r, o])
  }

  rangeFromLocationRange (t) {
    let e
    t = g(t)
    const i = this.positionFromLocation(t[0])
    return N(t) || (e = this.positionFromLocation(t[1])), g([i, e])
  }

  isEqualTo (t) {
    return this.blockList.isEqualTo(t?.blockList)
  }

  getTexts () {
    return this.getBlocks().map((t) => t.text)
  }

  getPieces () {
    const t = []
    return (
      Array.from(this.getTexts()).forEach((e) => {
        t.push(...Array.from(e.getPieces() || []))
      }),
      t
    )
  }

  getObjects () {
    return this.getBlocks()
      .concat(this.getTexts())
      .concat(this.getPieces())
  }

  toSerializableDocument () {
    const t = []
    return (
      this.blockList.eachObject((e) =>
        t.push(e.copyWithText(e.text.toSerializableText()))
      ),
      new this.constructor(t)
    )
  }

  toString () {
    return this.blockList.toString()
  }

  toJSON () {
    return this.blockList.toJSON()
  }

  toConsole () {
    return JSON.stringify(
      this.blockList.toArray().map((t) => JSON.parse(t.text.toConsole()))
    )
  }
}
var ci = function (n) {
  const t = {}
  const e = n.getLastAttribute()
  return e && (t[e] = !0), t
}
const fe = function (n) {
  const t =
        arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {}
  return { string: (n = bt(n)), attributes: t, type: 'string' }
}
const ui = (n, t) => {
  try {
    return JSON.parse(n.getAttribute('data-trix-'.concat(t)))
  } catch {
    return {}
  }
}
const et = class extends f {
  static parse (t, e) {
    const i = new this(t, e)
    return i.parse(), i
  }

  constructor (t) {
    const { referenceElement: e } =
            arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {}
    super(...arguments),
    (this.html = t),
    (this.referenceElement = e),
    (this.blocks = []),
    (this.blockElements = []),
    (this.processedElements = [])
  }

  getDocument () {
    return k.fromJSON(this.blocks)
  }

  parse () {
    try {
      this.createHiddenContainer(),
      lt.setHTML(this.containerElement, this.html)
      const t = Ft(this.containerElement, { usingFilter: Cn })
      for (; t.nextNode();) this.processNode(t.currentNode)
      return this.translateBlockElementMarginsToNewlines()
    } finally {
      this.removeHiddenContainer()
    }
  }

  createHiddenContainer () {
    return this.referenceElement
      ? ((this.containerElement = this.referenceElement.cloneNode(!1)),
        this.containerElement.removeAttribute('id'),
        this.containerElement.setAttribute('data-trix-internal', ''),
        (this.containerElement.style.display = 'none'),
        this.referenceElement.parentNode.insertBefore(
          this.containerElement,
          this.referenceElement.nextSibling
        ))
      : ((this.containerElement = d({
          tagName: 'div',
          style: { display: 'none' }
        })),
        document.body.appendChild(this.containerElement))
  }

  removeHiddenContainer () {
    return V(this.containerElement)
  }

  processNode (t) {
    switch (t.nodeType) {
      case Node.TEXT_NODE:
        if (!this.isInsignificantTextNode(t)) {
          return (
            this.appendBlockForTextNode(t), this.processTextNode(t)
          )
        }
        break
      case Node.ELEMENT_NODE:
        return this.appendBlockForElement(t), this.processElement(t)
    }
  }

  appendBlockForTextNode (t) {
    const e = t.parentNode
    if (
      e === this.currentBlockElement &&
            this.isBlockElement(t.previousSibling)
    ) {
      return this.appendStringWithAttributes(`
`)
    }
    if (e === this.containerElement || this.isBlockElement(e)) {
      let i
      const r = this.getBlockAttributes(e)
      const o = this.getBlockHTMLAttributes(e)
      Q(
        r,
        (i = this.currentBlock) === null || i === void 0
          ? void 0
          : i.attributes
      ) ||
                ((this.currentBlock = this.appendBlockForAttributesWithElement(
                  r,
                  e,
                  o
                )),
                (this.currentBlockElement = e))
    }
  }

  appendBlockForElement (t) {
    const e = this.isBlockElement(t)
    const i = J(this.currentBlockElement, t)
    if (e && !this.isBlockElement(t.firstChild)) {
      if (
        !this.isInsignificantTextNode(t.firstChild) ||
                !this.isBlockElement(t.firstElementChild)
      ) {
        const r = this.getBlockAttributes(t)
        const o = this.getBlockHTMLAttributes(t)
        if (t.firstChild) {
          if (i && Q(r, this.currentBlock.attributes)) {
            return this.appendStringWithAttributes(`
`)
          }
          (this.currentBlock =
                        this.appendBlockForAttributesWithElement(r, t, o)),
          (this.currentBlockElement = t)
        }
      }
    } else if (this.currentBlockElement && !i && !e) {
      const r = this.findParentBlockElement(t)
      if (r) return this.appendBlockForElement(r);
      (this.currentBlock = this.appendEmptyBlock()),
      (this.currentBlockElement = null)
    }
  }

  findParentBlockElement (t) {
    let { parentElement: e } = t
    for (; e && e !== this.containerElement;) {
      if (this.isBlockElement(e) && this.blockElements.includes(e)) {
        return e
      }
      e = e.parentElement
    }
    return null
  }

  processTextNode (t) {
    let e = t.data
    let i
    return (
      hi(t.parentNode) ||
                ((e = _e(e)),
                Ti(
                  (i = t.previousSibling) === null || i === void 0
                    ? void 0
                    : i.textContent
                ) && (e = kn(e))),
      this.appendStringWithAttributes(
        e,
        this.getTextAttributes(t.parentNode)
      )
    )
  }

  processElement (t) {
    let e
    if ($(t)) {
      if (((e = ui(t, 'attachment')), Object.keys(e).length)) {
        const i = this.getTextAttributes(t)
        this.appendAttachmentWithAttributes(e, i), (t.innerHTML = '')
      }
      return this.processedElements.push(t)
    }
    switch (x(t)) {
      case 'br':
        return (
          this.isExtraBR(t) ||
                        this.isBlockElement(t.nextSibling) ||
                        this.appendStringWithAttributes(
                            `
`,
                            this.getTextAttributes(t)
                        ),
          this.processedElements.push(t)
        )
      case 'img':
        e = { url: t.getAttribute('src'), contentType: 'image' }
        const i = ((r) => {
          const o = r.getAttribute('width')
          const s = r.getAttribute('height')
          const a = {}
          return (
            o && (a.width = parseInt(o, 10)),
            s && (a.height = parseInt(s, 10)),
            a
          )
        })(t)
        for (const r in i) {
          const o = i[r]
          e[r] = o
        }
        return (
          this.appendAttachmentWithAttributes(
            e,
            this.getTextAttributes(t)
          ),
          this.processedElements.push(t)
        )
      case 'tr':
        if (this.needsTableSeparator(t)) {
          return this.appendStringWithAttributes(
            Tt.tableRowSeparator
          )
        }
        break
      case 'td':
        if (this.needsTableSeparator(t)) {
          return this.appendStringWithAttributes(
            Tt.tableCellSeparator
          )
        }
    }
  }

  appendBlockForAttributesWithElement (t, e) {
    const i =
            arguments.length > 2 && arguments[2] !== void 0 ? arguments[2] : {}
    this.blockElements.push(e)
    const r = (function () {
      return {
        text: [],
        attributes:
                    arguments.length > 0 && arguments[0] !== void 0
                      ? arguments[0]
                      : {},
        htmlAttributes:
                    arguments.length > 1 && arguments[1] !== void 0
                      ? arguments[1]
                      : {}
      }
    })(t, i)
    return this.blocks.push(r), r
  }

  appendEmptyBlock () {
    return this.appendBlockForAttributesWithElement([], null)
  }

  appendStringWithAttributes (t, e) {
    return this.appendPiece(fe(t, e))
  }

  appendAttachmentWithAttributes (t, e) {
    return this.appendPiece(
      (function (i) {
        return {
          attachment: i,
          attributes:
                        arguments.length > 1 && arguments[1] !== void 0
                          ? arguments[1]
                          : {},
          type: 'attachment'
        }
      })(t, e)
    )
  }

  appendPiece (t) {
    return (
      this.blocks.length === 0 && this.appendEmptyBlock(),
      this.blocks[this.blocks.length - 1].text.push(t)
    )
  }

  appendStringToTextAtIndex (t, e) {
    const { text: i } = this.blocks[e]
    const r = i[i.length - 1]
    if (r?.type !== 'string') return i.push(fe(t))
    r.string += t
  }

  prependStringToTextAtIndex (t, e) {
    const { text: i } = this.blocks[e]
    const r = i[0]
    if (r?.type !== 'string') return i.unshift(fe(t))
    r.string = t + r.string
  }

  getTextAttributes (t) {
    let e
    const i = {}
    for (const r in Y) {
      const o = Y[r]
      if (
        o.tagName &&
                q(t, {
                  matchingSelector: o.tagName,
                  untilNode: this.containerElement
                })
      ) {
        i[r] = !0
      } else if (o.parser) {
        if (((e = o.parser(t)), e)) {
          let s = !1
          for (const a of this.findBlockElementAncestors(t)) {
            if (o.parser(a) === e) {
              s = !0
              break
            }
          }
          s || (i[r] = e)
        }
      } else {
        o.styleProperty &&
                    ((e = t.style[o.styleProperty]), e && (i[r] = e))
      }
    }
    if ($(t)) {
      const r = ui(t, 'attributes')
      for (const o in r) (e = r[o]), (i[o] = e)
    }
    return i
  }

  getBlockAttributes (t) {
    const e = []
    for (; t && t !== this.containerElement;) {
      for (const r in y) {
        const o = y[r]
        var i
        o.parse !== !1 &&
                    x(t) === o.tagName &&
                    (((i = o.test) !== null && i !== void 0 && i.call(o, t)) ||
                        !o.test) &&
                    (e.push(r), o.listAttribute && e.push(o.listAttribute))
      }
      t = t.parentNode
    }
    return e.reverse()
  }

  getBlockHTMLAttributes (t) {
    const e = {}
    const i = Object.values(y).find((r) => r.tagName === x(t))
    return (
      (i?.htmlAttributes || []).forEach((r) => {
        t.hasAttribute(r) && (e[r] = t.getAttribute(r))
      }),
      e
    )
  }

  findBlockElementAncestors (t) {
    const e = []
    for (; t && t !== this.containerElement;) {
      const i = x(t)
      At().includes(i) && e.push(t), (t = t.parentNode)
    }
    return e
  }

  isBlockElement (t) {
    if (
      t?.nodeType === Node.ELEMENT_NODE &&
            !$(t) &&
            !q(t, {
              matchingSelector: 'td',
              untilNode: this.containerElement
            })
    ) {
      return (
        At().includes(x(t)) ||
                window.getComputedStyle(t).display === 'block'
      )
    }
  }

  isInsignificantTextNode (t) {
    if (t?.nodeType !== Node.TEXT_NODE || !Rn(t.data)) return
    const { parentNode: e, previousSibling: i, nextSibling: r } = t
    return (yn(e.previousSibling) &&
            !this.isBlockElement(e.previousSibling)) ||
            hi(e)
      ? void 0
      : !i || this.isBlockElement(i) || !r || this.isBlockElement(r)
  }

  isExtraBR (t) {
    return (
      x(t) === 'br' &&
            this.isBlockElement(t.parentNode) &&
            t.parentNode.lastChild === t
    )
  }

  needsTableSeparator (t) {
    if (Tt.removeBlankTableCells) {
      let e
      const i =
                (e = t.previousSibling) === null || e === void 0
                  ? void 0
                  : e.textContent
      return i && /\S/.test(i)
    }
    return t.previousSibling
  }

  translateBlockElementMarginsToNewlines () {
    const t = this.getMarginOfDefaultBlockElement()
    for (let e = 0; e < this.blocks.length; e++) {
      const i = this.getMarginOfBlockElementAtIndex(e)
      i &&
                (i.top > 2 * t.top &&
                    this.prependStringToTextAtIndex(
                        `
`,
                        e
                    ),
                i.bottom > 2 * t.bottom &&
                    this.appendStringToTextAtIndex(
                        `
`,
                        e
                    ))
    }
  }

  getMarginOfBlockElementAtIndex (t) {
    const e = this.blockElements[t]
    if (
      e &&
            e.textContent &&
            !At().includes(x(e)) &&
            !this.processedElements.includes(e)
    ) {
      return di(e)
    }
  }

  getMarginOfDefaultBlockElement () {
    const t = d(y.default.tagName)
    return this.containerElement.appendChild(t), di(t)
  }
}
var hi = function (n) {
  const { whiteSpace: t } = window.getComputedStyle(n)
  return ['pre', 'pre-wrap', 'pre-line'].includes(t)
}
var yn = (n) => n && !Ti(n.textContent)
var di = function (n) {
  const t = window.getComputedStyle(n)
  if (t.display === 'block') {
    return {
      top: parseInt(t.marginTop),
      bottom: parseInt(t.marginBottom)
    }
  }
}
var Cn = function (n) {
  return x(n) === 'style'
    ? NodeFilter.FILTER_REJECT
    : NodeFilter.FILTER_ACCEPT
}
var kn = (n) => n.replace(new RegExp('^'.concat(ze.source, '+')), '')
var Rn = (n) => new RegExp('^'.concat(ze.source, '*$')).test(n)
var Ti = (n) => /\s$/.test(n)
const En = [
  'contenteditable',
  'data-trix-id',
  'data-trix-store-key',
  'data-trix-mutable',
  'data-trix-placeholder',
  'tabindex'
]
const Pe = 'data-trix-serialized-attributes'
const Sn = '['.concat(Pe, ']')
const Ln = new RegExp('<!--block-->', 'g')
const Dn = {
  'application/json': function (n) {
    let t
    if (n instanceof k) t = n
    else {
      if (!(n instanceof HTMLElement)) {
        throw new Error('unserializable object')
      }
      t = et.parse(n.innerHTML).getDocument()
    }
    return t.toSerializableDocument().toJSONString()
  },
  'text/html': function (n) {
    let t
    if (n instanceof k) t = ct.render(n)
    else {
      if (!(n instanceof HTMLElement)) {
        throw new Error('unserializable object')
      }
      t = n.cloneNode(!0)
    }
    return (
      Array.from(
        t.querySelectorAll('[data-trix-serialize=false]')
      ).forEach((e) => {
        V(e)
      }),
      En.forEach((e) => {
        Array.from(t.querySelectorAll('['.concat(e, ']'))).forEach(
          (i) => {
            i.removeAttribute(e)
          }
        )
      }),
      Array.from(t.querySelectorAll(Sn)).forEach((e) => {
        try {
          const i = JSON.parse(e.getAttribute(Pe))
          e.removeAttribute(Pe)
          for (const r in i) {
            const o = i[r]
            e.setAttribute(r, o)
          }
        } catch {}
      }),
      t.innerHTML.replace(Ln, '')
    )
  }
}
const wn = Object.freeze({ __proto__: null })
const m = class extends f {
  constructor (t, e) {
    super(...arguments),
    (this.attachmentManager = t),
    (this.attachment = e),
    (this.id = this.attachment.id),
    (this.file = this.attachment.file)
  }

  remove () {
    return this.attachmentManager.requestRemovalOfAttachment(
      this.attachment
    )
  }
}
m.proxyMethod('attachment.getAttribute'),
m.proxyMethod('attachment.hasAttribute'),
m.proxyMethod('attachment.setAttribute'),
m.proxyMethod('attachment.getAttributes'),
m.proxyMethod('attachment.setAttributes'),
m.proxyMethod('attachment.isPending'),
m.proxyMethod('attachment.isPreviewable'),
m.proxyMethod('attachment.getURL'),
m.proxyMethod('attachment.getHref'),
m.proxyMethod('attachment.getFilename'),
m.proxyMethod('attachment.getFilesize'),
m.proxyMethod('attachment.getFormattedFilesize'),
m.proxyMethod('attachment.getExtension'),
m.proxyMethod('attachment.getContentType'),
m.proxyMethod('attachment.getFile'),
m.proxyMethod('attachment.setFile'),
m.proxyMethod('attachment.releaseFile'),
m.proxyMethod('attachment.getUploadProgress'),
m.proxyMethod('attachment.setUploadProgress')
const Ut = class extends f {
  constructor () {
    const t =
            arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : []
    super(...arguments),
    (this.managedAttachments = {}),
    Array.from(t).forEach((e) => {
      this.manageAttachment(e)
    })
  }

  getAttachments () {
    const t = []
    for (const e in this.managedAttachments) {
      const i = this.managedAttachments[e]
      t.push(i)
    }
    return t
  }

  manageAttachment (t) {
    return (
      this.managedAttachments[t.id] ||
                (this.managedAttachments[t.id] = new m(this, t)),
      this.managedAttachments[t.id]
    )
  }

  attachmentIsManaged (t) {
    return t.id in this.managedAttachments
  }

  requestRemovalOfAttachment (t) {
    let e, i
    if (this.attachmentIsManaged(t)) {
      return (e = this.delegate) === null ||
                e === void 0 ||
                (i = e.attachmentManagerDidRequestRemovalOfAttachment) ===
                    null ||
                i === void 0
        ? void 0
        : i.call(e, t)
    }
  }

  unmanageAttachment (t) {
    const e = this.managedAttachments[t.id]
    return delete this.managedAttachments[t.id], e
  }
}
const qt = class {
  constructor (t) {
    (this.composition = t), (this.document = this.composition.document)
    const e = this.composition.getSelectedRange();
    (this.startPosition = e[0]),
    (this.endPosition = e[1]),
    (this.startLocation = this.document.locationFromPosition(
      this.startPosition
    )),
    (this.endLocation = this.document.locationFromPosition(
      this.endPosition
    )),
    (this.block = this.document.getBlockAtIndex(
      this.endLocation.index
    )),
    (this.breaksOnReturn = this.block.breaksOnReturn()),
    (this.previousCharacter = this.block.text.getStringAtPosition(
      this.endLocation.offset - 1
    )),
    (this.nextCharacter = this.block.text.getStringAtPosition(
      this.endLocation.offset
    ))
  }

  shouldInsertBlockBreak () {
    return this.block.hasAttributes() &&
            this.block.isListItem() &&
            !this.block.isEmpty()
      ? this.startLocation.offset !== 0
      : this.breaksOnReturn &&
                  this.nextCharacter !==
                      `
`
  }

  shouldBreakFormattedBlock () {
    return (
      this.block.hasAttributes() &&
            !this.block.isListItem() &&
            ((this.breaksOnReturn &&
                this.nextCharacter ===
                    `
`) ||
                this.previousCharacter ===
                    `
`)
    )
  }

  shouldDecreaseListLevel () {
    return (
      this.block.hasAttributes() &&
            this.block.isListItem() &&
            this.block.isEmpty()
    )
  }

  shouldPrependListItem () {
    return (
      this.block.isListItem() &&
            this.startLocation.offset === 0 &&
            !this.block.isEmpty()
    )
  }

  shouldRemoveLastBlockAttribute () {
    return (
      this.block.hasAttributes() &&
            !this.block.isListItem() &&
            this.block.isEmpty()
    )
  }
}
const F = class extends f {
  constructor () {
    super(...arguments),
    (this.document = new k()),
    (this.attachments = []),
    (this.currentAttributes = {}),
    (this.revision = 0)
  }

  setDocument (t) {
    let e, i
    if (!t.isEqualTo(this.document)) {
      return (
        (this.document = t),
        this.refreshAttachments(),
        this.revision++,
        (e = this.delegate) === null ||
                e === void 0 ||
                (i = e.compositionDidChangeDocument) === null ||
                i === void 0
          ? void 0
          : i.call(e, t)
      )
    }
  }

  getSnapshot () {
    return {
      document: this.document,
      selectedRange: this.getSelectedRange()
    }
  }

  loadSnapshot (t) {
    let e, i, r, o
    const { document: s, selectedRange: a } = t
    return (
      (e = this.delegate) === null ||
                e === void 0 ||
                (i = e.compositionWillLoadSnapshot) === null ||
                i === void 0 ||
                i.call(e),
      this.setDocument(s ?? new k()),
      this.setSelection(a ?? [0, 0]),
      (r = this.delegate) === null ||
            r === void 0 ||
            (o = r.compositionDidLoadSnapshot) === null ||
            o === void 0
        ? void 0
        : o.call(r)
    )
  }

  insertText (t) {
    const { updatePosition: e } =
            arguments.length > 1 && arguments[1] !== void 0
              ? arguments[1]
              : { updatePosition: !0 }
    const i = this.getSelectedRange()
    this.setDocument(this.document.insertTextAtRange(t, i))
    const r = i[0]
    const o = r + t.getLength()
    return (
      e && this.setSelection(o),
      this.notifyDelegateOfInsertionAtRange([r, o])
    )
  }

  insertBlock () {
    const t =
            arguments.length > 0 && arguments[0] !== void 0
              ? arguments[0]
              : new S()
    const e = new k([t])
    return this.insertDocument(e)
  }

  insertDocument () {
    const t =
            arguments.length > 0 && arguments[0] !== void 0
              ? arguments[0]
              : new k()
    const e = this.getSelectedRange()
    this.setDocument(this.document.insertDocumentAtRange(t, e))
    const i = e[0]
    const r = i + t.getLength()
    return (
      this.setSelection(r), this.notifyDelegateOfInsertionAtRange([i, r])
    )
  }

  insertString (t, e) {
    const i = this.getCurrentTextAttributes()
    const r = R.textForStringWithAttributes(t, i)
    return this.insertText(r, e)
  }

  insertBlockBreak () {
    const t = this.getSelectedRange()
    this.setDocument(this.document.insertBlockBreakAtRange(t))
    const e = t[0]
    const i = e + 1
    return (
      this.setSelection(i), this.notifyDelegateOfInsertionAtRange([e, i])
    )
  }

  insertLineBreak () {
    const t = new qt(this)
    if (t.shouldDecreaseListLevel()) {
      return this.decreaseListLevel(), this.setSelection(t.startPosition)
    }
    if (t.shouldPrependListItem()) {
      const e = new k([t.block.copyWithoutText()])
      return this.insertDocument(e)
    }
    return t.shouldInsertBlockBreak()
      ? this.insertBlockBreak()
      : t.shouldRemoveLastBlockAttribute()
        ? this.removeLastBlockAttribute()
        : t.shouldBreakFormattedBlock()
          ? this.breakFormattedBlock(t)
          : this.insertString(`
`)
  }

  insertHTML (t) {
    const e = et.parse(t).getDocument()
    const i = this.getSelectedRange()
    this.setDocument(this.document.mergeDocumentAtRange(e, i))
    const r = i[0]
    const o = r + e.getLength() - 1
    return (
      this.setSelection(o), this.notifyDelegateOfInsertionAtRange([r, o])
    )
  }

  replaceHTML (t) {
    const e = et
      .parse(t)
      .getDocument()
      .copyUsingObjectsFromDocument(this.document)
    const i = this.getLocationRange({ strict: !1 })
    const r = this.document.rangeFromLocationRange(i)
    return this.setDocument(e), this.setSelection(r)
  }

  insertFile (t) {
    return this.insertFiles([t])
  }

  insertFiles (t) {
    const e = []
    return (
      Array.from(t).forEach((i) => {
        let r
        if (
          (r = this.delegate) !== null &&
                    r !== void 0 &&
                    r.compositionShouldAcceptFile(i)
        ) {
          const o = H.attachmentForFile(i)
          e.push(o)
        }
      }),
      this.insertAttachments(e)
    )
  }

  insertAttachment (t) {
    return this.insertAttachments([t])
  }

  insertAttachments (t) {
    let e = new R()
    return (
      Array.from(t).forEach((i) => {
        let r
        const o = i.getType()
        const s =
                    (r = je[o]) === null || r === void 0
                      ? void 0
                      : r.presentation
        const a = this.getCurrentTextAttributes()
        s && (a.presentation = s)
        const l = R.textForAttachmentWithAttributes(i, a)
        e = e.appendText(l)
      }),
      this.insertText(e)
    )
  }

  shouldManageDeletingInDirection (t) {
    const e = this.getLocationRange()
    if (N(e)) {
      if (
        (t === 'backward' && e[0].offset === 0) ||
                this.shouldManageMovingCursorInDirection(t)
      ) {
        return !0
      }
    } else if (e[0].index !== e[1].index) return !0
    return !1
  }

  deleteInDirection (t) {
    let e
    let i
    let r
    const { length: o } =
            arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {}
    const s = this.getLocationRange()
    let a = this.getSelectedRange()
    const l = N(a)
    if (
      (l
        ? (i = t === 'backward' && s[0].offset === 0)
        : (r = s[0].index !== s[1].index),
      i && this.canDecreaseBlockAttributeLevel())
    ) {
      const c = this.getBlock()
      if (
        (c.isListItem()
          ? this.decreaseListLevel()
          : this.decreaseBlockAttributeLevel(),
        this.setSelection(a[0]),
        c.isEmpty())
      ) {
        return !1
      }
    }
    return (
      l &&
                ((a = this.getExpandedRangeInDirection(t, { length: o })),
                t === 'backward' && (e = this.getAttachmentAtRange(a))),
      e
        ? (this.editAttachment(e), !1)
        : (this.setDocument(this.document.removeTextAtRange(a)),
          this.setSelection(a[0]),
          !i && !r && void 0)
    )
  }

  moveTextFromRange (t) {
    const [e] = Array.from(this.getSelectedRange())
    return (
      this.setDocument(this.document.moveTextFromRangeToPosition(t, e)),
      this.setSelection(e)
    )
  }

  removeAttachment (t) {
    const e = this.document.getRangeOfAttachment(t)
    if (e) {
      return (
        this.stopEditingAttachment(),
        this.setDocument(this.document.removeTextAtRange(e)),
        this.setSelection(e[0])
      )
    }
  }

  removeLastBlockAttribute () {
    const [t, e] = Array.from(this.getSelectedRange())
    const i = this.document.getBlockAtPosition(e)
    return (
      this.removeCurrentAttribute(i.getLastAttribute()),
      this.setSelection(t)
    )
  }

  insertPlaceholder () {
    return (
      (this.placeholderPosition = this.getPosition()),
      this.insertString(' ')
    )
  }

  selectPlaceholder () {
    if (this.placeholderPosition != null) {
      return (
        this.setSelectedRange([
          this.placeholderPosition,
          this.placeholderPosition + 1
        ]),
        this.getSelectedRange()
      )
    }
  }

  forgetPlaceholder () {
    this.placeholderPosition = null
  }

  hasCurrentAttribute (t) {
    const e = this.currentAttributes[t]
    return e != null && e !== !1
  }

  toggleCurrentAttribute (t) {
    const e = !this.currentAttributes[t]
    return e
      ? this.setCurrentAttribute(t, e)
      : this.removeCurrentAttribute(t)
  }

  canSetCurrentAttribute (t) {
    return v(t)
      ? this.canSetCurrentBlockAttribute(t)
      : this.canSetCurrentTextAttribute(t)
  }

  canSetCurrentTextAttribute (t) {
    const e = this.getSelectedDocument()
    if (e) {
      for (const i of Array.from(e.getAttachments())) {
        if (!i.hasContent()) return !1
      }
      return !0
    }
  }

  canSetCurrentBlockAttribute (t) {
    const e = this.getBlock()
    if (e) return !e.isTerminalBlock()
  }

  setCurrentAttribute (t, e) {
    return v(t)
      ? this.setBlockAttribute(t, e)
      : (this.setTextAttribute(t, e),
        (this.currentAttributes[t] = e),
        this.notifyDelegateOfCurrentAttributesChange())
  }

  setHTMLAtributeAtPosition (t, e, i) {
    let r
    const o = this.document.getBlockAtPosition(t)
    const s =
            (r = v(o.getLastAttribute())) === null || r === void 0
              ? void 0
              : r.htmlAttributes
    if (o && s != null && s.includes(e)) {
      const a = this.document.setHTMLAttributeAtPosition(t, e, i)
      this.setDocument(a)
    }
  }

  setTextAttribute (t, e) {
    const i = this.getSelectedRange()
    if (!i) return
    const [r, o] = Array.from(i)
    if (r !== o) {
      return this.setDocument(this.document.addAttributeAtRange(t, e, i))
    }
    if (t === 'href') {
      const s = R.textForStringWithAttributes(e, { href: e })
      return this.insertText(s)
    }
  }

  setBlockAttribute (t, e) {
    const i = this.getSelectedRange()
    if (this.canSetCurrentAttribute(t)) {
      return (
        this.setDocument(
          this.document.applyBlockAttributeAtRange(t, e, i)
        ),
        this.setSelection(i)
      )
    }
  }

  removeCurrentAttribute (t) {
    return v(t)
      ? (this.removeBlockAttribute(t), this.updateCurrentAttributes())
      : (this.removeTextAttribute(t),
        delete this.currentAttributes[t],
        this.notifyDelegateOfCurrentAttributesChange())
  }

  removeTextAttribute (t) {
    const e = this.getSelectedRange()
    if (e) {
      return this.setDocument(this.document.removeAttributeAtRange(t, e))
    }
  }

  removeBlockAttribute (t) {
    const e = this.getSelectedRange()
    if (e) {
      return this.setDocument(this.document.removeAttributeAtRange(t, e))
    }
  }

  canDecreaseNestingLevel () {
    let t
    return (
      ((t = this.getBlock()) === null || t === void 0
        ? void 0
        : t.getNestingLevel()) > 0
    )
  }

  canIncreaseNestingLevel () {
    let t
    const e = this.getBlock()
    if (e) {
      if (
        (t = v(e.getLastNestableAttribute())) === null ||
                t === void 0 ||
                !t.listAttribute
      ) {
        return e.getNestingLevel() > 0
      }
      {
        const i = this.getPreviousBlock()
        if (i) {
          return (function () {
            const r =
                            arguments.length > 1 && arguments[1] !== void 0
                              ? arguments[1]
                              : []
            return Q(
              (arguments.length > 0 && arguments[0] !== void 0
                ? arguments[0]
                : []
              ).slice(0, r.length),
              r
            )
          })(i.getListItemAttributes(), e.getListItemAttributes())
        }
      }
    }
  }

  decreaseNestingLevel () {
    const t = this.getBlock()
    if (t) {
      return this.setDocument(
        this.document.replaceBlock(t, t.decreaseNestingLevel())
      )
    }
  }

  increaseNestingLevel () {
    const t = this.getBlock()
    if (t) {
      return this.setDocument(
        this.document.replaceBlock(t, t.increaseNestingLevel())
      )
    }
  }

  canDecreaseBlockAttributeLevel () {
    let t
    return (
      ((t = this.getBlock()) === null || t === void 0
        ? void 0
        : t.getAttributeLevel()) > 0
    )
  }

  decreaseBlockAttributeLevel () {
    let t
    const e =
            (t = this.getBlock()) === null || t === void 0
              ? void 0
              : t.getLastAttribute()
    if (e) return this.removeCurrentAttribute(e)
  }

  decreaseListLevel () {
    let [t] = Array.from(this.getSelectedRange())
    const { index: e } = this.document.locationFromPosition(t)
    let i = e
    const r = this.getBlock().getAttributeLevel()
    let o = this.document.getBlockAtIndex(i + 1)
    for (; o && o.isListItem() && !(o.getAttributeLevel() <= r);) {
      i++, (o = this.document.getBlockAtIndex(i + 1))
    }
    t = this.document.positionFromLocation({ index: e, offset: 0 })
    const s = this.document.positionFromLocation({ index: i, offset: 0 })
    return this.setDocument(
      this.document.removeLastListAttributeAtRange([t, s])
    )
  }

  updateCurrentAttributes () {
    const t = this.getSelectedRange({ ignoreLock: !0 })
    if (t) {
      const e = this.document.getCommonAttributesAtRange(t)
      if (
        (Array.from(Le()).forEach((i) => {
          e[i] || this.canSetCurrentAttribute(i) || (e[i] = !1)
        }),
        !dt(e, this.currentAttributes))
      ) {
        return (
          (this.currentAttributes = e),
          this.notifyDelegateOfCurrentAttributesChange()
        )
      }
    }
  }

  getCurrentAttributes () {
    return Ai.call({}, this.currentAttributes)
  }

  getCurrentTextAttributes () {
    const t = {}
    for (const e in this.currentAttributes) {
      const i = this.currentAttributes[e]
      i !== !1 && De(e) && (t[e] = i)
    }
    return t
  }

  freezeSelection () {
    return this.setCurrentAttribute('frozen', !0)
  }

  thawSelection () {
    return this.removeCurrentAttribute('frozen')
  }

  hasFrozenSelection () {
    return this.hasCurrentAttribute('frozen')
  }

  setSelection (t) {
    let e
    const i = this.document.locationRangeFromRange(t)
    return (e = this.delegate) === null || e === void 0
      ? void 0
      : e.compositionDidRequestChangingSelectionToLocationRange(i)
  }

  getSelectedRange () {
    const t = this.getLocationRange()
    if (t) return this.document.rangeFromLocationRange(t)
  }

  setSelectedRange (t) {
    const e = this.document.locationRangeFromRange(t)
    return this.getSelectionManager().setLocationRange(e)
  }

  getPosition () {
    const t = this.getLocationRange()
    if (t) return this.document.positionFromLocation(t[0])
  }

  getLocationRange (t) {
    return this.targetLocationRange
      ? this.targetLocationRange
      : this.getSelectionManager().getLocationRange(t) ||
                  g({ index: 0, offset: 0 })
  }

  withTargetLocationRange (t, e) {
    let i
    this.targetLocationRange = t
    try {
      i = e()
    } finally {
      this.targetLocationRange = null
    }
    return i
  }

  withTargetRange (t, e) {
    const i = this.document.locationRangeFromRange(t)
    return this.withTargetLocationRange(i, e)
  }

  withTargetDOMRange (t, e) {
    const i = this.createLocationRangeFromDOMRange(t, { strict: !1 })
    return this.withTargetLocationRange(i, e)
  }

  getExpandedRangeInDirection (t) {
    const { length: e } =
            arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {}
    let [i, r] = Array.from(this.getSelectedRange())
    return (
      t === 'backward'
        ? e
          ? (i -= e)
          : (i = this.translateUTF16PositionFromOffset(i, -1))
        : e
          ? (r += e)
          : (r = this.translateUTF16PositionFromOffset(r, 1)),
      g([i, r])
    )
  }

  shouldManageMovingCursorInDirection (t) {
    if (this.editingAttachment) return !0
    const e = this.getExpandedRangeInDirection(t)
    return this.getAttachmentAtRange(e) != null
  }

  moveCursorInDirection (t) {
    let e, i
    if (this.editingAttachment) {
      i = this.document.getRangeOfAttachment(this.editingAttachment)
    } else {
      const r = this.getSelectedRange();
      (i = this.getExpandedRangeInDirection(t)), (e = !Pt(r, i))
    }
    if (
      (t === 'backward'
        ? this.setSelectedRange(i[0])
        : this.setSelectedRange(i[1]),
      e)
    ) {
      const r = this.getAttachmentAtRange(i)
      if (r) return this.editAttachment(r)
    }
  }

  expandSelectionInDirection (t) {
    const { length: e } =
            arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {}
    const i = this.getExpandedRangeInDirection(t, { length: e })
    return this.setSelectedRange(i)
  }

  expandSelectionForEditing () {
    if (this.hasCurrentAttribute('href')) {
      return this.expandSelectionAroundCommonAttribute('href')
    }
  }

  expandSelectionAroundCommonAttribute (t) {
    const e = this.getPosition()
    const i = this.document.getRangeOfCommonAttributeAtPosition(t, e)
    return this.setSelectedRange(i)
  }

  selectionContainsAttachments () {
    let t
    return (
      ((t = this.getSelectedAttachments()) === null || t === void 0
        ? void 0
        : t.length) > 0
    )
  }

  selectionIsInCursorTarget () {
    return (
      this.editingAttachment ||
            this.positionIsCursorTarget(this.getPosition())
    )
  }

  positionIsCursorTarget (t) {
    const e = this.document.locationFromPosition(t)
    if (e) return this.locationIsCursorTarget(e)
  }

  positionIsBlockBreak (t) {
    let e
    return (e = this.document.getPieceAtPosition(t)) === null ||
            e === void 0
      ? void 0
      : e.isBlockBreak()
  }

  getSelectedDocument () {
    const t = this.getSelectedRange()
    if (t) return this.document.getDocumentAtRange(t)
  }

  getSelectedAttachments () {
    let t
    return (t = this.getSelectedDocument()) === null || t === void 0
      ? void 0
      : t.getAttachments()
  }

  getAttachments () {
    return this.attachments.slice(0)
  }

  refreshAttachments () {
    const t = this.document.getAttachments()
    const { added: e, removed: i } = (function () {
      const r =
                arguments.length > 0 && arguments[0] !== void 0
                  ? arguments[0]
                  : []
      const o =
                arguments.length > 1 && arguments[1] !== void 0
                  ? arguments[1]
                  : []
      const s = []
      const a = []
      const l = new Set()
      r.forEach((u) => {
        l.add(u)
      })
      const c = new Set()
      return (
        o.forEach((u) => {
          c.add(u), l.has(u) || s.push(u)
        }),
        r.forEach((u) => {
          c.has(u) || a.push(u)
        }),
        { added: s, removed: a }
      )
    })(this.attachments, t)
    return (
      (this.attachments = t),
      Array.from(i).forEach((r) => {
        let o, s;
        (r.delegate = null),
        (o = this.delegate) === null ||
                        o === void 0 ||
                        (s = o.compositionDidRemoveAttachment) === null ||
                        s === void 0 ||
                        s.call(o, r)
      }),
      (() => {
        const r = []
        return (
          Array.from(e).forEach((o) => {
            let s, a;
            (o.delegate = this),
            r.push(
              (s = this.delegate) === null ||
                                    s === void 0 ||
                                    (a = s.compositionDidAddAttachment) ===
                                        null ||
                                    a === void 0
                ? void 0
                : a.call(s, o)
            )
          }),
          r
        )
      })()
    )
  }

  attachmentDidChangeAttributes (t) {
    let e, i
    return (
      this.revision++,
      (e = this.delegate) === null ||
            e === void 0 ||
            (i = e.compositionDidEditAttachment) === null ||
            i === void 0
        ? void 0
        : i.call(e, t)
    )
  }

  attachmentDidChangePreviewURL (t) {
    let e, i
    return (
      this.revision++,
      (e = this.delegate) === null ||
            e === void 0 ||
            (i = e.compositionDidChangeAttachmentPreviewURL) === null ||
            i === void 0
        ? void 0
        : i.call(e, t)
    )
  }

  editAttachment (t, e) {
    let i, r
    if (t !== this.editingAttachment) {
      return (
        this.stopEditingAttachment(),
        (this.editingAttachment = t),
        (i = this.delegate) === null ||
                i === void 0 ||
                (r = i.compositionDidStartEditingAttachment) === null ||
                r === void 0
          ? void 0
          : r.call(i, this.editingAttachment, e)
      )
    }
  }

  stopEditingAttachment () {
    let t, e
    this.editingAttachment &&
            ((t = this.delegate) === null ||
                t === void 0 ||
                (e = t.compositionDidStopEditingAttachment) === null ||
                e === void 0 ||
                e.call(t, this.editingAttachment),
            (this.editingAttachment = null))
  }

  updateAttributesForAttachment (t, e) {
    return this.setDocument(
      this.document.updateAttributesForAttachment(t, e)
    )
  }

  removeAttributeForAttachment (t, e) {
    return this.setDocument(
      this.document.removeAttributeForAttachment(t, e)
    )
  }

  breakFormattedBlock (t) {
    let { document: e } = t
    const { block: i } = t
    let r = t.startPosition
    let o = [r - 1, r]
    i.getBlockBreakPosition() === t.startLocation.offset
      ? (i.breaksOnReturn() &&
              t.nextCharacter ===
                  `
`
          ? (r += 1)
          : (e = e.removeTextAtRange(o)),
        (o = [r, r]))
      : t.nextCharacter ===
                `
`
        ? t.previousCharacter ===
                `
`
          ? (o = [r - 1, r + 1])
          : ((o = [r, r + 1]), (r += 1))
        : t.startLocation.offset - 1 != 0 && (r += 1)
    const s = new k([i.removeLastAttribute().copyWithoutText()])
    return (
      this.setDocument(e.insertDocumentAtRange(s, o)),
      this.setSelection(r)
    )
  }

  getPreviousBlock () {
    const t = this.getLocationRange()
    if (t) {
      const { index: e } = t[0]
      if (e > 0) return this.document.getBlockAtIndex(e - 1)
    }
  }

  getBlock () {
    const t = this.getLocationRange()
    if (t) return this.document.getBlockAtIndex(t[0].index)
  }

  getAttachmentAtRange (t) {
    const e = this.document.getDocumentAtRange(t)
    if (
      e.toString() ===
            ''.concat(
              '\uFFFC',
                `
`
            )
    ) {
      return e.getAttachments()[0]
    }
  }

  notifyDelegateOfCurrentAttributesChange () {
    let t, e
    return (t = this.delegate) === null ||
            t === void 0 ||
            (e = t.compositionDidChangeCurrentAttributes) === null ||
            e === void 0
      ? void 0
      : e.call(t, this.currentAttributes)
  }

  notifyDelegateOfInsertionAtRange (t) {
    let e, i
    return (e = this.delegate) === null ||
            e === void 0 ||
            (i = e.compositionDidPerformInsertionAtRange) === null ||
            i === void 0
      ? void 0
      : i.call(e, t)
  }

  translateUTF16PositionFromOffset (t, e) {
    const i = this.document.toUTF16String()
    const r = i.offsetFromUCS2Offset(t)
    return i.offsetToUCS2Offset(r + e)
  }
}
F.proxyMethod('getSelectionManager().getPointRange'),
F.proxyMethod('getSelectionManager().setLocationRangeFromPointRange'),
F.proxyMethod('getSelectionManager().createLocationRangeFromDOMRange'),
F.proxyMethod('getSelectionManager().locationIsCursorTarget'),
F.proxyMethod('getSelectionManager().selectionIsExpanded'),
F.proxyMethod('delegate?.getSelectionManager')
const Et = class extends f {
  constructor (t) {
    super(...arguments),
    (this.composition = t),
    (this.undoEntries = []),
    (this.redoEntries = [])
  }

  recordUndoEntry (t) {
    const { context: e, consolidatable: i } =
            arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {}
    const r = this.undoEntries.slice(-1)[0]
    if (!i || !Tn(r, t, e)) {
      const o = this.createEntry({ description: t, context: e })
      this.undoEntries.push(o), (this.redoEntries = [])
    }
  }

  undo () {
    const t = this.undoEntries.pop()
    if (t) {
      const e = this.createEntry(t)
      return (
        this.redoEntries.push(e),
        this.composition.loadSnapshot(t.snapshot)
      )
    }
  }

  redo () {
    const t = this.redoEntries.pop()
    if (t) {
      const e = this.createEntry(t)
      return (
        this.undoEntries.push(e),
        this.composition.loadSnapshot(t.snapshot)
      )
    }
  }

  canUndo () {
    return this.undoEntries.length > 0
  }

  canRedo () {
    return this.redoEntries.length > 0
  }

  createEntry () {
    const { description: t, context: e } =
            arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : {}
    return {
      description: t?.toString(),
      context: JSON.stringify(e),
      snapshot: this.composition.getSnapshot()
    }
  }
}
var Tn = (n, t, e) =>
  n?.description === t?.toString() && n?.context === JSON.stringify(e)
const be = 'attachmentGallery'
const Vt = class {
  constructor (t) {
    (this.document = t.document), (this.selectedRange = t.selectedRange)
  }

  perform () {
    return this.removeBlockAttribute(), this.applyBlockAttribute()
  }

  getSnapshot () {
    return {
      document: this.document,
      selectedRange: this.selectedRange
    }
  }

  removeBlockAttribute () {
    return this.findRangesOfBlocks().map(
      (t) =>
        (this.document = this.document.removeAttributeAtRange(be, t))
    )
  }

  applyBlockAttribute () {
    let t = 0
    this.findRangesOfPieces().forEach((e) => {
      e[1] - e[0] > 1 &&
                ((e[0] += t),
                (e[1] += t),
                this.document.getCharacterAtPosition(e[1]) !==
                    `
` &&
                    ((this.document = this.document.insertBlockBreakAtRange(
                      e[1]
                    )),
                    e[1] < this.selectedRange[1] &&
                        this.moveSelectedRangeForward(),
                    e[1]++,
                    t++),
                e[0] !== 0 &&
                    this.document.getCharacterAtPosition(e[0] - 1) !==
                        `
` &&
                    ((this.document = this.document.insertBlockBreakAtRange(
                      e[0]
                    )),
                    e[0] < this.selectedRange[0] &&
                        this.moveSelectedRangeForward(),
                    e[0]++,
                    t++),
                (this.document = this.document.applyBlockAttributeAtRange(
                  be,
                  !0,
                  e
                )))
    })
  }

  findRangesOfBlocks () {
    return this.document.findRangesForBlockAttribute(be)
  }

  findRangesOfPieces () {
    return this.document.findRangesForTextAttribute('presentation', {
      withValue: 'gallery'
    })
  }

  moveSelectedRangeForward () {
    (this.selectedRange[0] += 1), (this.selectedRange[1] += 1)
  }
}
const Bi = function (n) {
  const t = new Vt(n)
  return t.perform(), t.getSnapshot()
}
const Bn = [Bi]
const Ht = class {
  constructor (t, e, i) {
    (this.insertFiles = this.insertFiles.bind(this)),
    (this.composition = t),
    (this.selectionManager = e),
    (this.element = i),
    (this.undoManager = new Et(this.composition)),
    (this.filters = Bn.slice(0))
  }

  loadDocument (t) {
    return this.loadSnapshot({ document: t, selectedRange: [0, 0] })
  }

  loadHTML () {
    const t =
            arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : ''
    const e = et.parse(t, { referenceElement: this.element }).getDocument()
    return this.loadDocument(e)
  }

  loadJSON (t) {
    let { document: e, selectedRange: i } = t
    return (
      (e = k.fromJSON(e)),
      this.loadSnapshot({ document: e, selectedRange: i })
    )
  }

  loadSnapshot (t) {
    return (
      (this.undoManager = new Et(this.composition)),
      this.composition.loadSnapshot(t)
    )
  }

  getDocument () {
    return this.composition.document
  }

  getSelectedDocument () {
    return this.composition.getSelectedDocument()
  }

  getSnapshot () {
    return this.composition.getSnapshot()
  }

  toJSON () {
    return this.getSnapshot()
  }

  deleteInDirection (t) {
    return this.composition.deleteInDirection(t)
  }

  insertAttachment (t) {
    return this.composition.insertAttachment(t)
  }

  insertAttachments (t) {
    return this.composition.insertAttachments(t)
  }

  insertDocument (t) {
    return this.composition.insertDocument(t)
  }

  insertFile (t) {
    return this.composition.insertFile(t)
  }

  insertFiles (t) {
    return this.composition.insertFiles(t)
  }

  insertHTML (t) {
    return this.composition.insertHTML(t)
  }

  insertString (t) {
    return this.composition.insertString(t)
  }

  insertText (t) {
    return this.composition.insertText(t)
  }

  insertLineBreak () {
    return this.composition.insertLineBreak()
  }

  getSelectedRange () {
    return this.composition.getSelectedRange()
  }

  getPosition () {
    return this.composition.getPosition()
  }

  getClientRectAtPosition (t) {
    const e = this.getDocument().locationRangeFromRange([t, t + 1])
    return this.selectionManager.getClientRectAtLocationRange(e)
  }

  expandSelectionInDirection (t) {
    return this.composition.expandSelectionInDirection(t)
  }

  moveCursorInDirection (t) {
    return this.composition.moveCursorInDirection(t)
  }

  setSelectedRange (t) {
    return this.composition.setSelectedRange(t)
  }

  activateAttribute (t) {
    const e =
            !(arguments.length > 1 && arguments[1] !== void 0) || arguments[1]
    return this.composition.setCurrentAttribute(t, e)
  }

  attributeIsActive (t) {
    return this.composition.hasCurrentAttribute(t)
  }

  canActivateAttribute (t) {
    return this.composition.canSetCurrentAttribute(t)
  }

  deactivateAttribute (t) {
    return this.composition.removeCurrentAttribute(t)
  }

  setHTMLAtributeAtPosition (t, e, i) {
    this.composition.setHTMLAtributeAtPosition(t, e, i)
  }

  canDecreaseNestingLevel () {
    return this.composition.canDecreaseNestingLevel()
  }

  canIncreaseNestingLevel () {
    return this.composition.canIncreaseNestingLevel()
  }

  decreaseNestingLevel () {
    if (this.canDecreaseNestingLevel()) {
      return this.composition.decreaseNestingLevel()
    }
  }

  increaseNestingLevel () {
    if (this.canIncreaseNestingLevel()) {
      return this.composition.increaseNestingLevel()
    }
  }

  canRedo () {
    return this.undoManager.canRedo()
  }

  canUndo () {
    return this.undoManager.canUndo()
  }

  recordUndoEntry (t) {
    const { context: e, consolidatable: i } =
            arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {}
    return this.undoManager.recordUndoEntry(t, {
      context: e,
      consolidatable: i
    })
  }

  redo () {
    if (this.canRedo()) return this.undoManager.redo()
  }

  undo () {
    if (this.canUndo()) return this.undoManager.undo()
  }
}
const zt = class {
  constructor (t) {
    this.element = t
  }

  findLocationFromContainerAndOffset (t, e) {
    const { strict: i } =
            arguments.length > 2 && arguments[2] !== void 0
              ? arguments[2]
              : { strict: !0 }
    let r = 0
    let o = !1
    const s = { index: 0, offset: 0 }
    const a = this.findAttachmentElementParentForNode(t)
    a && ((t = a.parentNode), (e = ae(a)))
    const l = Ft(this.element, { usingFilter: Fi })
    for (; l.nextNode();) {
      const c = l.currentNode
      if (c === t && xt(t)) {
        st(c) || (s.offset += e)
        break
      }
      if (c.parentNode === t) {
        if (r++ === e) break
      } else if (!J(t, c) && r > 0) break
      $e(c, { strict: i })
        ? (o && s.index++, (s.offset = 0), (o = !0))
        : (s.offset += ve(c))
    }
    return s
  }

  findContainerAndOffsetFromLocation (t) {
    let e, i
    if (t.index === 0 && t.offset === 0) {
      for (e = this.element, i = 0; e.firstChild;) {
        if (((e = e.firstChild), le(e))) {
          i = 1
          break
        }
      }
      return [e, i]
    }
    let [r, o] = this.findNodeAndOffsetFromLocation(t)
    if (r) {
      if (xt(r)) {
        ve(r) === 0
          ? ((e = r.parentNode.parentNode),
            (i = ae(r.parentNode)),
            st(r, { name: 'right' }) && i++)
          : ((e = r), (i = t.offset - o))
      } else {
        if (((e = r.parentNode), !$e(r.previousSibling) && !le(e))) {
          for (
            ;
            r === e.lastChild &&
                        ((r = e), (e = e.parentNode), !le(e));

          );
        }
        (i = ae(r)), t.offset !== 0 && i++
      }
      return [e, i]
    }
  }

  findNodeAndOffsetFromLocation (t) {
    let e
    let i
    let r = 0
    for (const o of this.getSignificantNodesForIndex(t.index)) {
      const s = ve(o)
      if (t.offset <= r + s) {
        if (xt(o)) {
          if (((e = o), (i = r), t.offset === i && st(e))) break
        } else e || ((e = o), (i = r))
      }
      if (((r += s), r > t.offset)) break
    }
    return [e, i]
  }

  findAttachmentElementParentForNode (t) {
    for (; t && t !== this.element;) {
      if ($(t)) return t
      t = t.parentNode
    }
  }

  getSignificantNodesForIndex (t) {
    const e = []
    const i = Ft(this.element, { usingFilter: Fn })
    let r = !1
    for (; i.nextNode();) {
      const s = i.currentNode
      var o
      if (ot(s)) {
        if ((o != null ? o++ : (o = 0), o === t)) r = !0
        else if (r) break
      } else r && e.push(s)
    }
    return e
  }
}
var ve = function (n) {
  return n.nodeType === Node.TEXT_NODE
    ? st(n)
      ? 0
      : n.textContent.length
    : x(n) === 'br' || $(n)
      ? 1
      : 0
}
var Fn = function (n) {
  return Pn(n) === NodeFilter.FILTER_ACCEPT
    ? Fi(n)
    : NodeFilter.FILTER_REJECT
}
var Pn = function (n) {
  return yi(n) ? NodeFilter.FILTER_REJECT : NodeFilter.FILTER_ACCEPT
}
var Fi = function (n) {
  return $(n.parentNode)
    ? NodeFilter.FILTER_REJECT
    : NodeFilter.FILTER_ACCEPT
}
const _t = class {
  createDOMRangeFromPoint (t) {
    let e
    const { x: i, y: r } = t
    if (document.caretPositionFromPoint) {
      const { offsetNode: o, offset: s } =
                document.caretPositionFromPoint(i, r)
      return (e = document.createRange()), e.setStart(o, s), e
    }
    if (document.caretRangeFromPoint) {
      return document.caretRangeFromPoint(i, r)
    }
    if (document.body.createTextRange) {
      const o = yt()
      try {
        const s = document.body.createTextRange()
        s.moveToPoint(i, r), s.select()
      } catch {}
      return (e = yt()), Di(o), e
    }
  }

  getClientRectsForDOMRange (t) {
    const e = Array.from(t.getClientRects())
    return [e[0], e[e.length - 1]]
  }
}
const I = class extends f {
  constructor (t) {
    super(...arguments),
    (this.didMouseDown = this.didMouseDown.bind(this)),
    (this.selectionDidChange = this.selectionDidChange.bind(this)),
    (this.element = t),
    (this.locationMapper = new zt(this.element)),
    (this.pointMapper = new _t()),
    (this.lockCount = 0),
    p('mousedown', {
      onElement: this.element,
      withCallback: this.didMouseDown
    })
  }

  getLocationRange () {
    const t =
            arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : {}
    return t.strict === !1
      ? this.createLocationRangeFromDOMRange(yt())
      : t.ignoreLock
        ? this.currentLocationRange
        : this.lockedLocationRange
          ? this.lockedLocationRange
          : this.currentLocationRange
  }

  setLocationRange (t) {
    if (this.lockedLocationRange) return
    t = g(t)
    const e = this.createDOMRangeFromLocationRange(t)
    e && (Di(e), this.updateCurrentLocationRange(t))
  }

  setLocationRangeFromPointRange (t) {
    t = g(t)
    const e = this.getLocationAtPoint(t[0])
    const i = this.getLocationAtPoint(t[1])
    this.setLocationRange([e, i])
  }

  getClientRectAtLocationRange (t) {
    const e = this.createDOMRangeFromLocationRange(t)
    if (e) return this.getClientRectsForDOMRange(e)[1]
  }

  locationIsCursorTarget (t) {
    const e = Array.from(this.findNodeAndOffsetFromLocation(t))[0]
    return st(e)
  }

  lock () {
    this.lockCount++ == 0 &&
            (this.updateCurrentLocationRange(),
            (this.lockedLocationRange = this.getLocationRange()))
  }

  unlock () {
    if (--this.lockCount == 0) {
      const { lockedLocationRange: t } = this
      if (((this.lockedLocationRange = null), t != null)) {
        return this.setLocationRange(t)
      }
    }
  }

  clearSelection () {
    let t
    return (t = Li()) === null || t === void 0
      ? void 0
      : t.removeAllRanges()
  }

  selectionIsCollapsed () {
    let t
    return (
      ((t = yt()) === null || t === void 0 ? void 0 : t.collapsed) === !0
    )
  }

  selectionIsExpanded () {
    return !this.selectionIsCollapsed()
  }

  createLocationRangeFromDOMRange (t, e) {
    if (t == null || !this.domRangeWithinElement(t)) return
    const i = this.findLocationFromContainerAndOffset(
      t.startContainer,
      t.startOffset,
      e
    )
    if (!i) return
    const r = t.collapsed
      ? void 0
      : this.findLocationFromContainerAndOffset(
        t.endContainer,
        t.endOffset,
        e
      )
    return g([i, r])
  }

  didMouseDown () {
    return this.pauseTemporarily()
  }

  pauseTemporarily () {
    let t
    this.paused = !0
    const e = () => {
      if (
        ((this.paused = !1),
        clearTimeout(i),
        Array.from(t).forEach((r) => {
          r.destroy()
        }),
        J(document, this.element))
      ) {
        return this.selectionDidChange()
      }
    }
    const i = setTimeout(e, 200)
    t = ['mousemove', 'keydown'].map((r) =>
      p(r, { onElement: document, withCallback: e })
    )
  }

  selectionDidChange () {
    if (!this.paused && !Ue(this.element)) {
      return this.updateCurrentLocationRange()
    }
  }

  updateCurrentLocationRange (t) {
    let e, i
    if (
      (t ?? (t = this.createLocationRangeFromDOMRange(yt()))) &&
            !Pt(t, this.currentLocationRange)
    ) {
      return (
        (this.currentLocationRange = t),
        (e = this.delegate) === null ||
                e === void 0 ||
                (i = e.locationRangeDidChange) === null ||
                i === void 0
          ? void 0
          : i.call(e, this.currentLocationRange.slice(0))
      )
    }
  }

  createDOMRangeFromLocationRange (t) {
    const e = this.findContainerAndOffsetFromLocation(t[0])
    const i = N(t) ? e : this.findContainerAndOffsetFromLocation(t[1]) || e
    if (e != null && i != null) {
      const r = document.createRange()
      return (
        r.setStart(...Array.from(e || [])),
        r.setEnd(...Array.from(i || [])),
        r
      )
    }
  }

  getLocationAtPoint (t) {
    const e = this.createDOMRangeFromPoint(t)
    let i
    if (e) {
      return (i = this.createLocationRangeFromDOMRange(e)) === null ||
                i === void 0
        ? void 0
        : i[0]
    }
  }

  domRangeWithinElement (t) {
    return t.collapsed
      ? J(this.element, t.startContainer)
      : J(this.element, t.startContainer) &&
                  J(this.element, t.endContainer)
  }
}
I.proxyMethod('locationMapper.findLocationFromContainerAndOffset'),
I.proxyMethod('locationMapper.findContainerAndOffsetFromLocation'),
I.proxyMethod('locationMapper.findNodeAndOffsetFromLocation'),
I.proxyMethod('pointMapper.createDOMRangeFromPoint'),
I.proxyMethod('pointMapper.getClientRectsForDOMRange')
const Pi = Object.freeze({
  __proto__: null,
  Attachment: H,
  AttachmentManager: Ut,
  AttachmentPiece: z,
  Block: S,
  Composition: F,
  Document: k,
  Editor: Ht,
  HTMLParser: et,
  HTMLSanitizer: lt,
  LineBreakInsertion: qt,
  LocationMapper: zt,
  ManagedAttachment: m,
  Piece: j,
  PointMapper: _t,
  SelectionManager: I,
  SplittableList: ut,
  StringPiece: Rt,
  Text: R,
  UndoManager: Et
})
const In = Object.freeze({
  __proto__: null,
  ObjectView: M,
  AttachmentView: kt,
  BlockView: jt,
  DocumentView: ct,
  PieceView: Ot,
  PreviewableAttachmentView: Nt,
  TextView: Mt
})
const { lang: Ae, css: _, keyNames: Nn } = Lt
const xe = function (n) {
  return function () {
    const t = n.apply(this, arguments)
    t.do(), this.undos || (this.undos = []), this.undos.push(t.undo)
  }
}
const Jt = class extends f {
  constructor (t, e, i) {
    const r =
            arguments.length > 3 && arguments[3] !== void 0 ? arguments[3] : {}
    super(...arguments),
    E(
      this,
      'makeElementMutable',
      xe(() => ({
        do: () => {
          this.element.dataset.trixMutable = !0
        },
        undo: () => delete this.element.dataset.trixMutable
      }))
    ),
    E(
      this,
      'addToolbar',
      xe(() => {
        const o = d({
          tagName: 'div',
          className: _.attachmentToolbar,
          data: { trixMutable: !0 },
          childNodes: d({
            tagName: 'div',
            className: 'trix-button-row',
            childNodes: d({
              tagName: 'span',
              className:
                                    'trix-button-group trix-button-group--actions',
              childNodes: d({
                tagName: 'button',
                className:
                                        'trix-button trix-button--remove',
                textContent: Ae.remove,
                attributes: { title: Ae.remove },
                data: { trixAction: 'remove' }
              })
            })
          })
        })
        return (
          this.attachment.isPreviewable() &&
                            o.appendChild(
                              d({
                                tagName: 'div',
                                className: _.attachmentMetadataContainer,
                                childNodes: d({
                                  tagName: 'span',
                                  className: _.attachmentMetadata,
                                  childNodes: [
                                    d({
                                      tagName: 'span',
                                      className: _.attachmentName,
                                      textContent:
                                                    this.attachment.getFilename(),
                                      attributes: {
                                        title: this.attachment.getFilename()
                                      }
                                    }),
                                    d({
                                      tagName: 'span',
                                      className: _.attachmentSize,
                                      textContent:
                                                    this.attachment.getFormattedFilesize()
                                    })
                                  ]
                                })
                              })
                            ),
          p('click', {
            onElement: o,
            withCallback: this.didClickToolbar
          }),
          p('click', {
            onElement: o,
            matchingSelector: '[data-trix-action]',
            withCallback: this.didClickActionButton
          }),
          vt('trix-attachment-before-toolbar', {
            onElement: this.element,
            attributes: {
              toolbar: o,
              attachment: this.attachment
            }
          }),
          {
            do: () => this.element.appendChild(o),
            undo: () => V(o)
          }
        )
      })
    ),
    E(
      this,
      'installCaptionEditor',
      xe(() => {
        const o = d({
          tagName: 'textarea',
          className: _.attachmentCaptionEditor,
          attributes: { placeholder: Ae.captionPlaceholder },
          data: { trixMutable: !0 }
        })
        o.value = this.attachmentPiece.getCaption()
        const s = o.cloneNode()
        s.classList.add('trix-autoresize-clone'), (s.tabIndex = -1)
        const a = function () {
          (s.value = o.value),
          (o.style.height = s.scrollHeight + 'px')
        }
        p('input', { onElement: o, withCallback: a }),
        p('input', {
          onElement: o,
          withCallback: this.didInputCaption
        }),
        p('keydown', {
          onElement: o,
          withCallback: this.didKeyDownCaption
        }),
        p('change', {
          onElement: o,
          withCallback: this.didChangeCaption
        }),
        p('blur', {
          onElement: o,
          withCallback: this.didBlurCaption
        })
        const l = this.element.querySelector('figcaption')
        const c = l.cloneNode()
        return {
          do: () => {
            if (
              ((l.style.display = 'none'),
              c.appendChild(o),
              c.appendChild(s),
              c.classList.add(
                ''.concat(_.attachmentCaption, '--editing')
              ),
              l.parentElement.insertBefore(c, l),
              a(),
              this.options.editCaption)
            ) {
              return He(() => o.focus())
            }
          },
          undo () {
            V(c), (l.style.display = null)
          }
        }
      })
    ),
    (this.didClickToolbar = this.didClickToolbar.bind(this)),
    (this.didClickActionButton = this.didClickActionButton.bind(this)),
    (this.didKeyDownCaption = this.didKeyDownCaption.bind(this)),
    (this.didInputCaption = this.didInputCaption.bind(this)),
    (this.didChangeCaption = this.didChangeCaption.bind(this)),
    (this.didBlurCaption = this.didBlurCaption.bind(this)),
    (this.attachmentPiece = t),
    (this.element = e),
    (this.container = i),
    (this.options = r),
    (this.attachment = this.attachmentPiece.attachment),
    x(this.element) === 'a' && (this.element = this.element.firstChild),
    this.install()
  }

  install () {
    this.makeElementMutable(),
    this.addToolbar(),
    this.attachment.isPreviewable() && this.installCaptionEditor()
  }

  uninstall () {
    let t
    let e = this.undos.pop()
    for (this.savePendingCaption(); e;) e(), (e = this.undos.pop());
    (t = this.delegate) === null ||
            t === void 0 ||
            t.didUninstallAttachmentEditor(this)
  }

  savePendingCaption () {
    if (this.pendingCaption != null) {
      const o = this.pendingCaption
      let t, e, i, r;
      (this.pendingCaption = null),
      o
        ? (t = this.delegate) === null ||
                      t === void 0 ||
                      (e =
                          t.attachmentEditorDidRequestUpdatingAttributesForAttachment) ===
                          null ||
                      e === void 0 ||
                      e.call(t, { caption: o }, this.attachment)
        : (i = this.delegate) === null ||
                      i === void 0 ||
                      (r =
                          i.attachmentEditorDidRequestRemovingAttributeForAttachment) ===
                          null ||
                      r === void 0 ||
                      r.call(i, 'caption', this.attachment)
    }
  }

  didClickToolbar (t) {
    return t.preventDefault(), t.stopPropagation()
  }

  didClickActionButton (t) {
    let e
    if (t.target.getAttribute('data-trix-action') === 'remove') {
      return (e = this.delegate) === null || e === void 0
        ? void 0
        : e.attachmentEditorDidRequestRemovalOfAttachment(
          this.attachment
        )
    }
  }

  didKeyDownCaption (t) {
    let e, i
    if (Nn[t.keyCode] === 'return') {
      return (
        t.preventDefault(),
        this.savePendingCaption(),
        (e = this.delegate) === null ||
                e === void 0 ||
                (i = e.attachmentEditorDidRequestDeselectingAttachment) ===
                    null ||
                i === void 0
          ? void 0
          : i.call(e, this.attachment)
      )
    }
  }

  didInputCaption (t) {
    this.pendingCaption = t.target.value.replace(/\s/g, ' ').trim()
  }

  didChangeCaption (t) {
    return this.savePendingCaption()
  }

  didBlurCaption (t) {
    return this.savePendingCaption()
  }
}
const Kt = class extends f {
  constructor (t, e) {
    super(...arguments),
    (this.didFocus = this.didFocus.bind(this)),
    (this.didBlur = this.didBlur.bind(this)),
    (this.didClickAttachment = this.didClickAttachment.bind(this)),
    (this.element = t),
    (this.composition = e),
    (this.documentView = new ct(this.composition.document, {
      element: this.element
    })),
    p('focus', {
      onElement: this.element,
      withCallback: this.didFocus
    }),
    p('blur', {
      onElement: this.element,
      withCallback: this.didBlur
    }),
    p('click', {
      onElement: this.element,
      matchingSelector: 'a[contenteditable=false]',
      preventDefault: !0
    }),
    p('mousedown', {
      onElement: this.element,
      matchingSelector: K,
      withCallback: this.didClickAttachment
    }),
    p('click', {
      onElement: this.element,
      matchingSelector: 'a'.concat(K),
      preventDefault: !0
    })
  }

  didFocus (t) {
    let e
    const i = () => {
      let r, o
      if (!this.focused) {
        return (
          (this.focused = !0),
          (r = this.delegate) === null ||
                    r === void 0 ||
                    (o = r.compositionControllerDidFocus) === null ||
                    o === void 0
            ? void 0
            : o.call(r)
        )
      }
    }
    return (
      ((e = this.blurPromise) === null || e === void 0
        ? void 0
        : e.then(i)) || i()
    )
  }

  didBlur (t) {
    this.blurPromise = new Promise((e) =>
      He(() => {
        let i, r
        return (
          Ue(this.element) ||
                        ((this.focused = null),
                        (i = this.delegate) === null ||
                            i === void 0 ||
                            (r = i.compositionControllerDidBlur) === null ||
                            r === void 0 ||
                            r.call(i)),
          (this.blurPromise = null),
          e()
        )
      })
    )
  }

  didClickAttachment (t, e) {
    let i, r
    const o = this.findAttachmentForElement(e)
    const s = !!q(t.target, { matchingSelector: 'figcaption' })
    return (i = this.delegate) === null ||
            i === void 0 ||
            (r = i.compositionControllerDidSelectAttachment) === null ||
            r === void 0
      ? void 0
      : r.call(i, o, { editCaption: s })
  }

  getSerializableElement () {
    return this.isEditingAttachment()
      ? this.documentView.shadowElement
      : this.element
  }

  render () {
    let t, e, i, r, o, s
    return (
      this.revision !== this.composition.revision &&
                (this.documentView.setDocument(this.composition.document),
                this.documentView.render(),
                (this.revision = this.composition.revision)),
      this.canSyncDocumentView() &&
                !this.documentView.isSynced() &&
                ((i = this.delegate) === null ||
                    i === void 0 ||
                    (r = i.compositionControllerWillSyncDocumentView) ===
                        null ||
                    r === void 0 ||
                    r.call(i),
                this.documentView.sync(),
                (o = this.delegate) === null ||
                    o === void 0 ||
                    (s = o.compositionControllerDidSyncDocumentView) === null ||
                    s === void 0 ||
                    s.call(o)),
      (t = this.delegate) === null ||
            t === void 0 ||
            (e = t.compositionControllerDidRender) === null ||
            e === void 0
        ? void 0
        : e.call(t)
    )
  }

  rerenderViewForObject (t) {
    return this.invalidateViewForObject(t), this.render()
  }

  invalidateViewForObject (t) {
    return this.documentView.invalidateViewForObject(t)
  }

  isViewCachingEnabled () {
    return this.documentView.isViewCachingEnabled()
  }

  enableViewCaching () {
    return this.documentView.enableViewCaching()
  }

  disableViewCaching () {
    return this.documentView.disableViewCaching()
  }

  refreshViewCache () {
    return this.documentView.garbageCollectCachedViews()
  }

  isEditingAttachment () {
    return !!this.attachmentEditor
  }

  installAttachmentEditorForAttachment (t, e) {
    let i
    if (
      ((i = this.attachmentEditor) === null || i === void 0
        ? void 0
        : i.attachment) === t
    ) {
      return
    }
    const r = this.documentView.findElementForObject(t)
    if (!r) return
    this.uninstallAttachmentEditor()
    const o = this.composition.document.getAttachmentPieceForAttachment(t);
    (this.attachmentEditor = new Jt(o, r, this.element, e)),
    (this.attachmentEditor.delegate = this)
  }

  uninstallAttachmentEditor () {
    let t
    return (t = this.attachmentEditor) === null || t === void 0
      ? void 0
      : t.uninstall()
  }

  didUninstallAttachmentEditor () {
    return (this.attachmentEditor = null), this.render()
  }

  attachmentEditorDidRequestUpdatingAttributesForAttachment (t, e) {
    let i, r
    return (
      (i = this.delegate) === null ||
                i === void 0 ||
                (r = i.compositionControllerWillUpdateAttachment) === null ||
                r === void 0 ||
                r.call(i, e),
      this.composition.updateAttributesForAttachment(t, e)
    )
  }

  attachmentEditorDidRequestRemovingAttributeForAttachment (t, e) {
    let i, r
    return (
      (i = this.delegate) === null ||
                i === void 0 ||
                (r = i.compositionControllerWillUpdateAttachment) === null ||
                r === void 0 ||
                r.call(i, e),
      this.composition.removeAttributeForAttachment(t, e)
    )
  }

  attachmentEditorDidRequestRemovalOfAttachment (t) {
    let e, i
    return (e = this.delegate) === null ||
            e === void 0 ||
            (i = e.compositionControllerDidRequestRemovalOfAttachment) ===
                null ||
            i === void 0
      ? void 0
      : i.call(e, t)
  }

  attachmentEditorDidRequestDeselectingAttachment (t) {
    let e, i
    return (e = this.delegate) === null ||
            e === void 0 ||
            (i = e.compositionControllerDidRequestDeselectingAttachment) ===
                null ||
            i === void 0
      ? void 0
      : i.call(e, t)
  }

  canSyncDocumentView () {
    return !this.isEditingAttachment()
  }

  findAttachmentForElement (t) {
    return this.composition.document.getAttachmentById(
      parseInt(t.dataset.trixId, 10)
    )
  }
}
const $t = class extends f {}
const Ii = 'data-trix-mutable'
const On = '['.concat(Ii, ']')
const Mn = {
  attributes: !0,
  childList: !0,
  characterData: !0,
  characterDataOldValue: !0,
  subtree: !0
}
const Gt = class extends f {
  constructor (t) {
    super(t),
    (this.didMutate = this.didMutate.bind(this)),
    (this.element = t),
    (this.observer = new window.MutationObserver(this.didMutate)),
    this.start()
  }

  start () {
    return this.reset(), this.observer.observe(this.element, Mn)
  }

  stop () {
    return this.observer.disconnect()
  }

  didMutate (t) {
    let e, i
    if (
      (this.mutations.push(
        ...Array.from(this.findSignificantMutations(t) || [])
      ),
      this.mutations.length)
    ) {
      return (
        (e = this.delegate) === null ||
                    e === void 0 ||
                    (i = e.elementDidMutate) === null ||
                    i === void 0 ||
                    i.call(e, this.getMutationSummary()),
        this.reset()
      )
    }
  }

  reset () {
    this.mutations = []
  }

  findSignificantMutations (t) {
    return t.filter((e) => this.mutationIsSignificant(e))
  }

  mutationIsSignificant (t) {
    if (this.nodeIsMutable(t.target)) return !1
    for (const e of Array.from(this.nodesModifiedByMutation(t))) {
      if (this.nodeIsSignificant(e)) return !0
    }
    return !1
  }

  nodeIsSignificant (t) {
    return t !== this.element && !this.nodeIsMutable(t) && !yi(t)
  }

  nodeIsMutable (t) {
    return q(t, { matchingSelector: On })
  }

  nodesModifiedByMutation (t) {
    const e = []
    switch (t.type) {
      case 'attributes':
        t.attributeName !== Ii && e.push(t.target)
        break
      case 'characterData':
        e.push(t.target.parentNode), e.push(t.target)
        break
      case 'childList':
        e.push(...Array.from(t.addedNodes || [])),
        e.push(...Array.from(t.removedNodes || []))
    }
    return e
  }

  getMutationSummary () {
    return this.getTextMutationSummary()
  }

  getTextMutationSummary () {
    const { additions: t, deletions: e } =
            this.getTextChangesFromCharacterData()
    const i = this.getTextChangesFromChildList()
    Array.from(i.additions).forEach((a) => {
      Array.from(t).includes(a) || t.push(a)
    }),
    e.push(...Array.from(i.deletions || []))
    const r = {}
    const o = t.join('')
    o && (r.textAdded = o)
    const s = e.join('')
    return s && (r.textDeleted = s), r
  }

  getMutationsByType (t) {
    return Array.from(this.mutations).filter((e) => e.type === t)
  }

  getTextChangesFromChildList () {
    let t
    let e
    const i = []
    const r = []
    return (
      Array.from(this.getMutationsByType('childList')).forEach((o) => {
        i.push(...Array.from(o.addedNodes || [])),
        r.push(...Array.from(o.removedNodes || []))
      }),
      i.length === 0 && r.length === 1 && ot(r[0])
        ? ((t = []),
          (e = [
                      `
`
          ]))
        : ((t = Ie(i)), (e = Ie(r))),
      {
        additions: t.filter((o, s) => o !== e[s]).map(bt),
        deletions: e.filter((o, s) => o !== t[s]).map(bt)
      }
    )
  }

  getTextChangesFromCharacterData () {
    let t
    let e
    const i = this.getMutationsByType('characterData')
    if (i.length) {
      const r = i[0]
      const o = i[i.length - 1]
      const s = (function (a, l) {
        let c, u
        return (
          (a = Z.box(a)),
          (l = Z.box(l)).length < a.length
            ? ([u, c] = ti(a, l))
            : ([c, u] = ti(l, a)),
          { added: c, removed: u }
        )
      })(bt(r.oldValue), bt(o.target.data));
      (t = s.added), (e = s.removed)
    }
    return { additions: t ? [t] : [], deletions: e ? [e] : [] }
  }
}
var Ie = function () {
  const n =
        arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : []
  const t = []
  for (const e of Array.from(n)) {
    switch (e.nodeType) {
      case Node.TEXT_NODE:
        t.push(e.data)
        break
      case Node.ELEMENT_NODE:
        x(e) === 'br'
          ? t.push(`
`)
          : t.push(...Array.from(Ie(e.childNodes) || []))
    }
  }
  return t
}
const Xt = class extends at {
  constructor (t) {
    super(...arguments), (this.file = t)
  }

  perform (t) {
    const e = new FileReader()
    return (
      (e.onerror = () => t(!1)),
      (e.onload = () => {
        e.onerror = null
        try {
          e.abort()
        } catch {}
        return t(!0, this.file)
      }),
      e.readAsArrayBuffer(this.file)
    )
  }
}
const Ne = class {
  constructor (t) {
    this.element = t
  }

  shouldIgnore (t) {
    return (
      !!St.samsungAndroid &&
            ((this.previousEvent = this.event),
            (this.event = t),
            this.checkSamsungKeyboardBuggyModeStart(),
            this.checkSamsungKeyboardBuggyModeEnd(),
            this.buggyMode)
    )
  }

  checkSamsungKeyboardBuggyModeStart () {
    this.insertingLongTextAfterUnidentifiedChar() &&
            jn(this.element.innerText, this.event.data) &&
            ((this.buggyMode = !0), this.event.preventDefault())
  }

  checkSamsungKeyboardBuggyModeEnd () {
    this.buggyMode &&
            this.event.inputType !== 'insertText' &&
            (this.buggyMode = !1)
  }

  insertingLongTextAfterUnidentifiedChar () {
    let t
    return (
      this.isBeforeInputInsertText() &&
            this.previousEventWasUnidentifiedKeydown() &&
            ((t = this.event.data) === null || t === void 0
              ? void 0
              : t.length) > 50
    )
  }

  isBeforeInputInsertText () {
    return (
      this.event.type === 'beforeinput' &&
            this.event.inputType === 'insertText'
    )
  }

  previousEventWasUnidentifiedKeydown () {
    let t, e
    return (
      ((t = this.previousEvent) === null || t === void 0
        ? void 0
        : t.type) === 'keydown' &&
            ((e = this.previousEvent) === null || e === void 0
              ? void 0
              : e.key) === 'Unidentified'
    )
  }
}
var jn = (n, t) => gi(n) === gi(t)
const Wn = new RegExp(
  '('.concat('\uFFFC', '|').concat(te, '|').concat(U, '|\\s)+'),
  'g'
)
var gi = (n) => n.replace(Wn, ' ').trim()
const ht = class extends f {
  constructor (t) {
    super(...arguments),
    (this.element = t),
    (this.mutationObserver = new Gt(this.element)),
    (this.mutationObserver.delegate = this),
    (this.flakyKeyboardDetector = new Ne(this.element))
    for (const e in this.constructor.events) {
      p(e, {
        onElement: this.element,
        withCallback: this.handlerFor(e)
      })
    }
  }

  elementDidMutate (t) {}
  editorWillSyncDocumentView () {
    return this.mutationObserver.stop()
  }

  editorDidSyncDocumentView () {
    return this.mutationObserver.start()
  }

  requestRender () {
    let t, e
    return (t = this.delegate) === null ||
            t === void 0 ||
            (e = t.inputControllerDidRequestRender) === null ||
            e === void 0
      ? void 0
      : e.call(t)
  }

  requestReparse () {
    let t, e
    return (
      (t = this.delegate) === null ||
                t === void 0 ||
                (e = t.inputControllerDidRequestReparse) === null ||
                e === void 0 ||
                e.call(t),
      this.requestRender()
    )
  }

  attachFiles (t) {
    const e = Array.from(t).map((i) => new Xt(i))
    return Promise.all(e).then((i) => {
      this.handleInput(function () {
        let r, o
        return (
          (r = this.delegate) === null ||
                        r === void 0 ||
                        r.inputControllerWillAttachFiles(),
          (o = this.responder) === null ||
                        o === void 0 ||
                        o.insertFiles(i),
          this.requestRender()
        )
      })
    })
  }

  handlerFor (t) {
    return (e) => {
      e.defaultPrevented ||
                this.handleInput(() => {
                  if (!Ue(this.element)) {
                    if (this.flakyKeyboardDetector.shouldIgnore(e)) {
                      return
                    }
                    (this.eventName = t),
                    this.constructor.events[t].call(this, e)
                  }
                })
    }
  }

  handleInput (t) {
    try {
      let e;
      (e = this.delegate) === null ||
                e === void 0 ||
                e.inputControllerWillHandleInput(),
      t.call(this)
    } finally {
      let i;
      (i = this.delegate) === null ||
                i === void 0 ||
                i.inputControllerDidHandleInput()
    }
  }

  createLinkHTML (t, e) {
    const i = document.createElement('a')
    return (i.href = t), (i.textContent = e || t), i.outerHTML
  }
}
let ye
E(ht, 'events', {})
const { browser: Un, keyNames: Ni } = Lt
let qn = 0
const w = class extends ht {
  constructor () {
    super(...arguments), this.resetInputSummary()
  }

  setInputSummary () {
    const t =
            arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : {}
    this.inputSummary.eventName = this.eventName
    for (const e in t) {
      const i = t[e]
      this.inputSummary[e] = i
    }
    return this.inputSummary
  }

  resetInputSummary () {
    this.inputSummary = {}
  }

  reset () {
    return this.resetInputSummary(), tt.reset()
  }

  elementDidMutate (t) {
    let e, i
    return this.isComposing()
      ? (e = this.delegate) === null ||
              e === void 0 ||
              (i = e.inputControllerDidAllowUnhandledInput) === null ||
              i === void 0
          ? void 0
          : i.call(e)
      : this.handleInput(function () {
        return (
          this.mutationIsSignificant(t) &&
                          (this.mutationIsExpected(t)
                            ? this.requestRender()
                            : this.requestReparse()),
          this.reset()
        )
      })
  }

  mutationIsExpected (t) {
    const { textAdded: e, textDeleted: i } = t
    if (this.inputSummary.preferDocument) return !0
    const r =
            e != null
              ? e === this.inputSummary.textAdded
              : !this.inputSummary.textAdded
    const o =
            i != null
              ? this.inputSummary.didDelete
              : !this.inputSummary.didDelete
    const s =
            [
                `
`,
                ` 
`
            ].includes(e) && !r
    const a =
            i ===
                `
` && !o
    if ((s && !a) || (a && !s)) {
      const c = this.getSelectedRange()
      if (c) {
        let l
        const u = s
          ? e.replace(/\n$/, '').length || -1
          : e?.length || 1
        if (
          (l = this.responder) !== null &&
                    l !== void 0 &&
                    l.positionIsBlockBreak(c[1] + u)
        ) {
          return !0
        }
      }
    }
    return r && o
  }

  mutationIsSignificant (t) {
    let e
    const i = Object.keys(t).length > 0
    const r =
            ((e = this.compositionInput) === null || e === void 0
              ? void 0
              : e.getEndData()) === ''
    return i || !r
  }

  getCompositionInput () {
    if (this.isComposing()) return this.compositionInput
    this.compositionInput = new B(this)
  }

  isComposing () {
    return this.compositionInput && !this.compositionInput.isEnded()
  }

  deleteInDirection (t, e) {
    let i
    return ((i = this.responder) === null || i === void 0
      ? void 0
      : i.deleteInDirection(t)) !== !1
      ? this.setInputSummary({ didDelete: !0 })
      : e
        ? (e.preventDefault(), this.requestRender())
        : void 0
  }

  serializeSelectionToDataTransfer (t) {
    let e
    if (
      !(function (r) {
        if (r == null || !r.setData) return !1
        for (const o in Ye) {
          const s = Ye[o]
          try {
            if ((r.setData(o, s), !r.getData(o) === s)) {
              return !1
            }
          } catch {
            return !1
          }
        }
        return !0
      })(t)
    ) {
      return
    }
    const i =
            (e = this.responder) === null || e === void 0
              ? void 0
              : e.getSelectedDocument().toSerializableDocument()
    return (
      t.setData('application/x-trix-document', JSON.stringify(i)),
      t.setData('text/html', ct.render(i).innerHTML),
      t.setData('text/plain', i.toString().replace(/\n$/, '')),
      !0
    )
  }

  canAcceptDataTransfer (t) {
    const e = {}
    return (
      Array.from(t?.types || []).forEach((i) => {
        e[i] = !0
      }),
      e.Files ||
                e['application/x-trix-document'] ||
                e['text/html'] ||
                e['text/plain']
    )
  }

  getPastedHTMLUsingHiddenElement (t) {
    const e = this.getSelectedRange()
    const i = {
      position: 'absolute',
      left: ''.concat(window.pageXOffset, 'px'),
      top: ''.concat(window.pageYOffset, 'px'),
      opacity: 0
    }
    const r = d({ style: i, tagName: 'div', editable: !0 })
    return (
      document.body.appendChild(r),
      r.focus(),
      requestAnimationFrame(() => {
        const o = r.innerHTML
        return V(r), this.setSelectedRange(e), t(o)
      })
    )
  }
}
E(w, 'events', {
  keydown (n) {
    this.isComposing() || this.resetInputSummary(),
    (this.inputSummary.didInput = !0)
    const t = Ni[n.keyCode]
    if (t) {
      let e
      let r = this.keys;
      ['ctrl', 'alt', 'shift', 'meta'].forEach((o) => {
        let s
        n[''.concat(o, 'Key')] &&
                    (o === 'ctrl' && (o = 'control'),
                    (r = (s = r) === null || s === void 0 ? void 0 : s[o]))
      }),
      ((e = r) === null || e === void 0 ? void 0 : e[t]) != null &&
                    (this.setInputSummary({ keyName: t }),
                    tt.reset(),
                    r[t].call(this, n))
    }
    if (Ei(n)) {
      const r = String.fromCharCode(n.keyCode).toLowerCase()
      if (r) {
        let i
        const o = ['alt', 'shift']
          .map((s) => {
            if (n[''.concat(s, 'Key')]) return s
          })
          .filter((s) => s)
        o.push(r),
        (i = this.delegate) !== null &&
                        i !== void 0 &&
                        i.inputControllerDidReceiveKeyboardCommand(o) &&
                        n.preventDefault()
      }
    }
  },
  keypress (n) {
    if (
      this.inputSummary.eventName != null ||
            n.metaKey ||
            (n.ctrlKey && !n.altKey)
    ) {
      return
    }
    const t = zn(n)
    let e, i
    return t
      ? ((e = this.delegate) === null ||
                  e === void 0 ||
                  e.inputControllerWillPerformTyping(),
        (i = this.responder) === null ||
                  i === void 0 ||
                  i.insertString(t),
        this.setInputSummary({
          textAdded: t,
          didDelete: this.selectionIsExpanded()
        }))
      : void 0
  },
  textInput (n) {
    const { data: t } = n
    const { textAdded: e } = this.inputSummary
    if (e && e !== t && e.toUpperCase() === t) {
      let i
      const r = this.getSelectedRange()
      return (
        this.setSelectedRange([r[0], r[1] + e.length]),
        (i = this.responder) === null ||
                    i === void 0 ||
                    i.insertString(t),
        this.setInputSummary({ textAdded: t }),
        this.setSelectedRange(r)
      )
    }
  },
  dragenter (n) {
    n.preventDefault()
  },
  dragstart (n) {
    let t, e
    return (
      this.serializeSelectionToDataTransfer(n.dataTransfer),
      (this.draggedRange = this.getSelectedRange()),
      (t = this.delegate) === null ||
            t === void 0 ||
            (e = t.inputControllerDidStartDrag) === null ||
            e === void 0
        ? void 0
        : e.call(t)
    )
  },
  dragover (n) {
    if (this.draggedRange || this.canAcceptDataTransfer(n.dataTransfer)) {
      n.preventDefault()
      const i = { x: n.clientX, y: n.clientY }
      let t, e
      if (!dt(i, this.draggingPoint)) {
        return (
          (this.draggingPoint = i),
          (t = this.delegate) === null ||
                    t === void 0 ||
                    (e = t.inputControllerDidReceiveDragOverPoint) === null ||
                    e === void 0
            ? void 0
            : e.call(t, this.draggingPoint)
        )
      }
    }
  },
  dragend (n) {
    let t, e;
    (t = this.delegate) === null ||
            t === void 0 ||
            (e = t.inputControllerDidCancelDrag) === null ||
            e === void 0 ||
            e.call(t),
    (this.draggedRange = null),
    (this.draggingPoint = null)
  },
  drop (n) {
    let t, e
    n.preventDefault()
    const i =
            (t = n.dataTransfer) === null || t === void 0 ? void 0 : t.files
    const r = n.dataTransfer.getData('application/x-trix-document')
    const o = { x: n.clientX, y: n.clientY }
    if (
      ((e = this.responder) === null ||
                e === void 0 ||
                e.setLocationRangeFromPointRange(o),
      i != null && i.length)
    ) {
      this.attachFiles(i)
    } else if (this.draggedRange) {
      let s, a;
      (s = this.delegate) === null ||
                s === void 0 ||
                s.inputControllerWillMoveText(),
      (a = this.responder) === null ||
                    a === void 0 ||
                    a.moveTextFromRange(this.draggedRange),
      (this.draggedRange = null),
      this.requestRender()
    } else if (r) {
      let l
      const c = k.fromJSONString(r);
      (l = this.responder) === null ||
                l === void 0 ||
                l.insertDocument(c),
      this.requestRender()
    }
    (this.draggedRange = null), (this.draggingPoint = null)
  },
  cut (n) {
    let t, e
    if (
      (t = this.responder) !== null &&
            t !== void 0 &&
            t.selectionIsExpanded() &&
            (this.serializeSelectionToDataTransfer(n.clipboardData) &&
                n.preventDefault(),
            (e = this.delegate) === null ||
                e === void 0 ||
                e.inputControllerWillCutText(),
            this.deleteInDirection('backward'),
            n.defaultPrevented)
    ) {
      return this.requestRender()
    }
  },
  copy (n) {
    let t;
    (t = this.responder) !== null &&
            t !== void 0 &&
            t.selectionIsExpanded() &&
            this.serializeSelectionToDataTransfer(n.clipboardData) &&
            n.preventDefault()
  },
  paste (n) {
    const t = n.clipboardData || n.testClipboardData
    const e = { clipboard: t }
    if (!t || _n(n)) {
      return void this.getPastedHTMLUsingHiddenElement((D) => {
        let nt, re, oe
        return (
          (e.type = 'text/html'),
          (e.html = D),
          (nt = this.delegate) === null ||
                        nt === void 0 ||
                        nt.inputControllerWillPaste(e),
          (re = this.responder) === null ||
                        re === void 0 ||
                        re.insertHTML(e.html),
          this.requestRender(),
          (oe = this.delegate) === null || oe === void 0
            ? void 0
            : oe.inputControllerDidPaste(e)
        )
      })
    }
    const i = t.getData('URL')
    const r = t.getData('text/html')
    const o = t.getData('public.url-name')
    if (i) {
      let s, a, l
      let D;
      (e.type = 'text/html'),
      (D = o ? _e(o).trim() : i),
      (e.html = this.createLinkHTML(i, D)),
      (s = this.delegate) === null ||
                    s === void 0 ||
                    s.inputControllerWillPaste(e),
      this.setInputSummary({
        textAdded: D,
        didDelete: this.selectionIsExpanded()
      }),
      (a = this.responder) === null ||
                    a === void 0 ||
                    a.insertHTML(e.html),
      this.requestRender(),
      (l = this.delegate) === null ||
                    l === void 0 ||
                    l.inputControllerDidPaste(e)
    } else if (Ri(t)) {
      let c, u, b;
      (e.type = 'text/plain'),
      (e.string = t.getData('text/plain')),
      (c = this.delegate) === null ||
                    c === void 0 ||
                    c.inputControllerWillPaste(e),
      this.setInputSummary({
        textAdded: e.string,
        didDelete: this.selectionIsExpanded()
      }),
      (u = this.responder) === null ||
                    u === void 0 ||
                    u.insertString(e.string),
      this.requestRender(),
      (b = this.delegate) === null ||
                    b === void 0 ||
                    b.inputControllerDidPaste(e)
    } else if (r) {
      let A, L, gt;
      (e.type = 'text/html'),
      (e.html = r),
      (A = this.delegate) === null ||
                    A === void 0 ||
                    A.inputControllerWillPaste(e),
      (L = this.responder) === null ||
                    L === void 0 ||
                    L.insertHTML(e.html),
      this.requestRender(),
      (gt = this.delegate) === null ||
                    gt === void 0 ||
                    gt.inputControllerDidPaste(e)
    } else if (Array.from(t.types).includes('Files')) {
      let P, it
      const D =
                (P = t.items) === null ||
                P === void 0 ||
                (P = P[0]) === null ||
                P === void 0 ||
                (it = P.getAsFile) === null ||
                it === void 0
                  ? void 0
                  : it.call(P)
      if (D) {
        let mt, ie, ne
        const nt = Vn(D)
        !D.name &&
                    nt &&
                    (D.name = 'pasted-file-'.concat(++qn, '.').concat(nt)),
        (e.type = 'File'),
        (e.file = D),
        (mt = this.delegate) === null ||
                        mt === void 0 ||
                        mt.inputControllerWillAttachFiles(),
        (ie = this.responder) === null ||
                        ie === void 0 ||
                        ie.insertFile(e.file),
        this.requestRender(),
        (ne = this.delegate) === null ||
                        ne === void 0 ||
                        ne.inputControllerDidPaste(e)
      }
    }
    n.preventDefault()
  },
  compositionstart (n) {
    return this.getCompositionInput().start(n.data)
  },
  compositionupdate (n) {
    return this.getCompositionInput().update(n.data)
  },
  compositionend (n) {
    return this.getCompositionInput().end(n.data)
  },
  beforeinput (n) {
    this.inputSummary.didInput = !0
  },
  input (n) {
    return (this.inputSummary.didInput = !0), n.stopPropagation()
  }
}),
E(w, 'keys', {
  backspace (n) {
    let t
    return (
      (t = this.delegate) === null ||
                    t === void 0 ||
                    t.inputControllerWillPerformTyping(),
      this.deleteInDirection('backward', n)
    )
  },
  delete (n) {
    let t
    return (
      (t = this.delegate) === null ||
                    t === void 0 ||
                    t.inputControllerWillPerformTyping(),
      this.deleteInDirection('forward', n)
    )
  },
  return (n) {
    let t, e
    return (
      this.setInputSummary({ preferDocument: !0 }),
      (t = this.delegate) === null ||
                    t === void 0 ||
                    t.inputControllerWillPerformTyping(),
      (e = this.responder) === null || e === void 0
        ? void 0
        : e.insertLineBreak()
    )
  },
  tab (n) {
    let t, e;
    (t = this.responder) !== null &&
                t !== void 0 &&
                t.canIncreaseNestingLevel() &&
                ((e = this.responder) === null ||
                    e === void 0 ||
                    e.increaseNestingLevel(),
                this.requestRender(),
                n.preventDefault())
  },
  left (n) {
    let t
    if (this.selectionIsInCursorTarget()) {
      return (
        n.preventDefault(),
        (t = this.responder) === null || t === void 0
          ? void 0
          : t.moveCursorInDirection('backward')
      )
    }
  },
  right (n) {
    let t
    if (this.selectionIsInCursorTarget()) {
      return (
        n.preventDefault(),
        (t = this.responder) === null || t === void 0
          ? void 0
          : t.moveCursorInDirection('forward')
      )
    }
  },
  control: {
    d (n) {
      let t
      return (
        (t = this.delegate) === null ||
                        t === void 0 ||
                        t.inputControllerWillPerformTyping(),
        this.deleteInDirection('forward', n)
      )
    },
    h (n) {
      let t
      return (
        (t = this.delegate) === null ||
                        t === void 0 ||
                        t.inputControllerWillPerformTyping(),
        this.deleteInDirection('backward', n)
      )
    },
    o (n) {
      let t, e
      return (
        n.preventDefault(),
        (t = this.delegate) === null ||
                        t === void 0 ||
                        t.inputControllerWillPerformTyping(),
        (e = this.responder) === null ||
                        e === void 0 ||
                        e.insertString(
                            `
`,
                            { updatePosition: !1 }
                        ),
        this.requestRender()
      )
    }
  },
  shift: {
    return (n) {
      let t, e;
      (t = this.delegate) === null ||
                    t === void 0 ||
                    t.inputControllerWillPerformTyping(),
      (e = this.responder) === null ||
                        e === void 0 ||
                        e.insertString(`
`),
      this.requestRender(),
      n.preventDefault()
    },
    tab (n) {
      let t, e;
      (t = this.responder) !== null &&
                    t !== void 0 &&
                    t.canDecreaseNestingLevel() &&
                    ((e = this.responder) === null ||
                        e === void 0 ||
                        e.decreaseNestingLevel(),
                    this.requestRender(),
                    n.preventDefault())
    },
    left (n) {
      if (this.selectionIsInCursorTarget()) {
        return (
          n.preventDefault(),
          this.expandSelectionInDirection('backward')
        )
      }
    },
    right (n) {
      if (this.selectionIsInCursorTarget()) {
        return (
          n.preventDefault(),
          this.expandSelectionInDirection('forward')
        )
      }
    }
  },
  alt: {
    backspace (n) {
      let t
      return (
        this.setInputSummary({ preferDocument: !1 }),
        (t = this.delegate) === null || t === void 0
          ? void 0
          : t.inputControllerWillPerformTyping()
      )
    }
  },
  meta: {
    backspace (n) {
      let t
      return (
        this.setInputSummary({ preferDocument: !1 }),
        (t = this.delegate) === null || t === void 0
          ? void 0
          : t.inputControllerWillPerformTyping()
      )
    }
  }
}),
w.proxyMethod('responder?.getSelectedRange'),
w.proxyMethod('responder?.setSelectedRange'),
w.proxyMethod('responder?.expandSelectionInDirection'),
w.proxyMethod('responder?.selectionIsInCursorTarget'),
w.proxyMethod('responder?.selectionIsExpanded')
var Vn = (n) => {
  let t
  return (t = n.type) === null ||
        t === void 0 ||
        (t = t.match(/\/(\w+)$/)) === null ||
        t === void 0
    ? void 0
    : t[1]
}
const Hn = !(
  (ye = ' '.codePointAt) === null ||
    ye === void 0 ||
    !ye.call(' ', 0)
)
var zn = function (n) {
  if (n.key && Hn && n.key.codePointAt(0) === n.keyCode) return n.key
  {
    let t
    if (
      (n.which === null
        ? (t = n.keyCode)
        : n.which !== 0 && n.charCode !== 0 && (t = n.charCode),
      t != null && Ni[t] !== 'escape')
    ) {
      return Z.fromCodepoints([t]).toString()
    }
  }
}
var _n = function (n) {
  const t = n.clipboardData
  if (t) {
    if (t.types.includes('text/html')) {
      for (const e of t.types) {
        const i = /^CorePasteboardFlavorType/.test(e)
        const r = /^dyn\./.test(e) && t.getData(e)
        if (i || r) return !0
      }
      return !1
    }
    {
      const e = t.types.includes('com.apple.webarchive')
      const i = t.types.includes('com.apple.flat-rtfd')
      return e || i
    }
  }
}
var B = class extends f {
  constructor (t) {
    super(...arguments),
    (this.inputController = t),
    (this.responder = this.inputController.responder),
    (this.delegate = this.inputController.delegate),
    (this.inputSummary = this.inputController.inputSummary),
    (this.data = {})
  }

  start (t) {
    if (((this.data.start = t), this.isSignificant())) {
      let e, i
      this.inputSummary.eventName === 'keypress' &&
                this.inputSummary.textAdded &&
                ((i = this.responder) === null ||
                    i === void 0 ||
                    i.deleteInDirection('left')),
      this.selectionIsExpanded() ||
                    (this.insertPlaceholder(), this.requestRender()),
      (this.range =
                    (e = this.responder) === null || e === void 0
                      ? void 0
                      : e.getSelectedRange())
    }
  }

  update (t) {
    if (((this.data.update = t), this.isSignificant())) {
      const e = this.selectPlaceholder()
      e && (this.forgetPlaceholder(), (this.range = e))
    }
  }

  end (t) {
    return (
      (this.data.end = t),
      this.isSignificant()
        ? (this.forgetPlaceholder(),
          this.canApplyToDocument()
            ? (this.setInputSummary({
                preferDocument: !0,
                didInput: !1
              }),
              (e = this.delegate) === null ||
                            e === void 0 ||
                            e.inputControllerWillPerformTyping(),
              (i = this.responder) === null ||
                            i === void 0 ||
                            i.setSelectedRange(this.range),
              (r = this.responder) === null ||
                            r === void 0 ||
                            r.insertString(this.data.end),
              (o = this.responder) === null || o === void 0
                ? void 0
                : o.setSelectedRange(
                  this.range[0] + this.data.end.length
                ))
            : this.data.start != null || this.data.update != null
              ? (this.requestReparse(), this.inputController.reset())
              : void 0)
        : this.inputController.reset()
    )
    let e, i, r, o
  }

  getEndData () {
    return this.data.end
  }

  isEnded () {
    return this.getEndData() != null
  }

  isSignificant () {
    return !Un.composesExistingText || this.inputSummary.didInput
  }

  canApplyToDocument () {
    let t, e
    return (
      ((t = this.data.start) === null || t === void 0
        ? void 0
        : t.length) === 0 &&
            ((e = this.data.end) === null || e === void 0 ? void 0 : e.length) >
                0 &&
            this.range
    )
  }
}
B.proxyMethod('inputController.setInputSummary'),
B.proxyMethod('inputController.requestRender'),
B.proxyMethod('inputController.requestReparse'),
B.proxyMethod('responder?.selectionIsExpanded'),
B.proxyMethod('responder?.insertPlaceholder'),
B.proxyMethod('responder?.selectPlaceholder'),
B.proxyMethod('responder?.forgetPlaceholder')
const G = class extends ht {
  constructor () {
    super(...arguments), (this.render = this.render.bind(this))
  }

  elementDidMutate () {
    return this.scheduledRender
      ? this.composing
        ? (t = this.delegate) === null ||
                  t === void 0 ||
                  (e = t.inputControllerDidAllowUnhandledInput) === null ||
                  e === void 0
            ? void 0
            : e.call(t)
        : void 0
      : this.reparse()
    let t, e
  }

  scheduleRender () {
    return this.scheduledRender
      ? this.scheduledRender
      : (this.scheduledRender = requestAnimationFrame(this.render))
  }

  render () {
    let t, e
    cancelAnimationFrame(this.scheduledRender),
    (this.scheduledRender = null),
    this.composing ||
                (e = this.delegate) === null ||
                e === void 0 ||
                e.render(),
    (t = this.afterRender) === null || t === void 0 || t.call(this),
    (this.afterRender = null)
  }

  reparse () {
    let t
    return (t = this.delegate) === null || t === void 0
      ? void 0
      : t.reparse()
  }

  insertString () {
    let t
    const e =
            arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : ''
    const i = arguments.length > 1 ? arguments[1] : void 0
    return (
      (t = this.delegate) === null ||
                t === void 0 ||
                t.inputControllerWillPerformTyping(),
      this.withTargetDOMRange(function () {
        let r
        return (r = this.responder) === null || r === void 0
          ? void 0
          : r.insertString(e, i)
      })
    )
  }

  toggleAttributeIfSupported (t) {
    let e
    if (Le().includes(t)) {
      return (
        (e = this.delegate) === null ||
                    e === void 0 ||
                    e.inputControllerWillPerformFormatting(t),
        this.withTargetDOMRange(function () {
          let i
          return (i = this.responder) === null || i === void 0
            ? void 0
            : i.toggleCurrentAttribute(t)
        })
      )
    }
  }

  activateAttributeIfSupported (t, e) {
    let i
    if (Le().includes(t)) {
      return (
        (i = this.delegate) === null ||
                    i === void 0 ||
                    i.inputControllerWillPerformFormatting(t),
        this.withTargetDOMRange(function () {
          let r
          return (r = this.responder) === null || r === void 0
            ? void 0
            : r.setCurrentAttribute(t, e)
        })
      )
    }
  }

  deleteInDirection (t) {
    const { recordUndoEntry: e } =
            arguments.length > 1 && arguments[1] !== void 0
              ? arguments[1]
              : { recordUndoEntry: !0 }
    let i
    e &&
            ((i = this.delegate) === null ||
                i === void 0 ||
                i.inputControllerWillPerformTyping())
    const r = () => {
      let s
      return (s = this.responder) === null || s === void 0
        ? void 0
        : s.deleteInDirection(t)
    }
    const o = this.getTargetDOMRange({ minLength: this.composing ? 1 : 2 })
    return o ? this.withTargetDOMRange(o, r) : r()
  }

  withTargetDOMRange (t, e) {
    let i
    return (
      typeof t === 'function' &&
                ((e = t), (t = this.getTargetDOMRange())),
      t
        ? (i = this.responder) === null || i === void 0
            ? void 0
            : i.withTargetDOMRange(t, e.bind(this))
        : (tt.reset(), e.call(this))
    )
  }

  getTargetDOMRange () {
    let t, e
    const { minLength: i } =
            arguments.length > 0 && arguments[0] !== void 0
              ? arguments[0]
              : { minLength: 0 }
    const r =
            (t = (e = this.event).getTargetRanges) === null || t === void 0
              ? void 0
              : t.call(e)
    if (r && r.length) {
      const o = Jn(r[0])
      if (i === 0 || o.toString().length >= i) return o
    }
  }

  withEvent (t, e) {
    let i
    this.event = t
    try {
      i = e.call(this)
    } finally {
      this.event = null
    }
    return i
  }
}
E(G, 'events', {
  keydown (n) {
    if (Ei(n)) {
      let t
      const e = Gn(n);
      (t = this.delegate) !== null &&
                t !== void 0 &&
                t.inputControllerDidReceiveKeyboardCommand(e) &&
                n.preventDefault()
    } else {
      let e = n.key
      n.altKey && (e += '+Alt'), n.shiftKey && (e += '+Shift')
      const i = this.constructor.keys[e]
      if (i) return this.withEvent(n, i)
    }
  },
  paste (n) {
    let t
    let e
    const i =
            (t = n.clipboardData) === null || t === void 0
              ? void 0
              : t.getData('URL')
    return Oi(n)
      ? (n.preventDefault(), this.attachFiles(n.clipboardData.files))
      : $n(n)
        ? (n.preventDefault(),
          (e = {
            type: 'text/plain',
            string: n.clipboardData.getData('text/plain')
          }),
          (r = this.delegate) === null ||
                    r === void 0 ||
                    r.inputControllerWillPaste(e),
          (o = this.responder) === null ||
                    o === void 0 ||
                    o.insertString(e.string),
          this.render(),
          (s = this.delegate) === null || s === void 0
            ? void 0
            : s.inputControllerDidPaste(e))
        : i
          ? (n.preventDefault(),
            (e = { type: 'text/html', html: this.createLinkHTML(i) }),
            (a = this.delegate) === null ||
                      a === void 0 ||
                      a.inputControllerWillPaste(e),
            (l = this.responder) === null ||
                      l === void 0 ||
                      l.insertHTML(e.html),
            this.render(),
            (c = this.delegate) === null || c === void 0
              ? void 0
              : c.inputControllerDidPaste(e))
          : void 0
    let r, o, s, a, l, c
  },
  beforeinput (n) {
    const t = this.constructor.inputTypes[n.inputType]
    t && (this.withEvent(n, t), this.scheduleRender())
  },
  input (n) {
    tt.reset()
  },
  dragstart (n) {
    let t, e;
    (t = this.responder) !== null &&
            t !== void 0 &&
            t.selectionContainsAttachments() &&
            (n.dataTransfer.setData('application/x-trix-dragging', !0),
            (this.dragging = {
              range:
                    (e = this.responder) === null || e === void 0
                      ? void 0
                      : e.getSelectedRange(),
              point: ke(n)
            }))
  },
  dragenter (n) {
    Ce(n) && n.preventDefault()
  },
  dragover (n) {
    if (this.dragging) {
      n.preventDefault()
      const e = ke(n)
      let t
      if (!dt(e, this.dragging.point)) {
        return (
          (this.dragging.point = e),
          (t = this.responder) === null || t === void 0
            ? void 0
            : t.setLocationRangeFromPointRange(e)
        )
      }
    } else Ce(n) && n.preventDefault()
  },
  drop (n) {
    let t, e
    if (this.dragging) {
      return (
        n.preventDefault(),
        (t = this.delegate) === null ||
                    t === void 0 ||
                    t.inputControllerWillMoveText(),
        (e = this.responder) === null ||
                    e === void 0 ||
                    e.moveTextFromRange(this.dragging.range),
        (this.dragging = null),
        this.scheduleRender()
      )
    }
    if (Ce(n)) {
      let i
      n.preventDefault()
      const r = ke(n)
      return (
        (i = this.responder) === null ||
                    i === void 0 ||
                    i.setLocationRangeFromPointRange(r),
        this.attachFiles(n.dataTransfer.files)
      )
    }
  },
  dragend () {
    let n
    this.dragging &&
            ((n = this.responder) === null ||
                n === void 0 ||
                n.setSelectedRange(this.dragging.range),
            (this.dragging = null))
  },
  compositionend (n) {
    this.composing &&
            ((this.composing = !1), St.recentAndroid || this.scheduleRender())
  }
}),
E(G, 'keys', {
  ArrowLeft () {
    let n, t
    if (
      (n = this.responder) !== null &&
                n !== void 0 &&
                n.shouldManageMovingCursorInDirection('backward')
    ) {
      return (
        this.event.preventDefault(),
        (t = this.responder) === null || t === void 0
          ? void 0
          : t.moveCursorInDirection('backward')
      )
    }
  },
  ArrowRight () {
    let n, t
    if (
      (n = this.responder) !== null &&
                n !== void 0 &&
                n.shouldManageMovingCursorInDirection('forward')
    ) {
      return (
        this.event.preventDefault(),
        (t = this.responder) === null || t === void 0
          ? void 0
          : t.moveCursorInDirection('forward')
      )
    }
  },
  Backspace () {
    let n, t, e
    if (
      (n = this.responder) !== null &&
                n !== void 0 &&
                n.shouldManageDeletingInDirection('backward')
    ) {
      return (
        this.event.preventDefault(),
        (t = this.delegate) === null ||
                        t === void 0 ||
                        t.inputControllerWillPerformTyping(),
        (e = this.responder) === null ||
                        e === void 0 ||
                        e.deleteInDirection('backward'),
        this.render()
      )
    }
  },
  Tab () {
    let n, t
    if (
      (n = this.responder) !== null &&
                n !== void 0 &&
                n.canIncreaseNestingLevel()
    ) {
      return (
        this.event.preventDefault(),
        (t = this.responder) === null ||
                        t === void 0 ||
                        t.increaseNestingLevel(),
        this.render()
      )
    }
  },
  'Tab+Shift' () {
    let n, t
    if (
      (n = this.responder) !== null &&
                n !== void 0 &&
                n.canDecreaseNestingLevel()
    ) {
      return (
        this.event.preventDefault(),
        (t = this.responder) === null ||
                        t === void 0 ||
                        t.decreaseNestingLevel(),
        this.render()
      )
    }
  }
}),
E(G, 'inputTypes', {
  deleteByComposition () {
    return this.deleteInDirection('backward', { recordUndoEntry: !1 })
  },
  deleteByCut () {
    return this.deleteInDirection('backward')
  },
  deleteByDrag () {
    return (
      this.event.preventDefault(),
      this.withTargetDOMRange(function () {
        let n
        this.deleteByDragRange =
                        (n = this.responder) === null || n === void 0
                          ? void 0
                          : n.getSelectedRange()
      })
    )
  },
  deleteCompositionText () {
    return this.deleteInDirection('backward', { recordUndoEntry: !1 })
  },
  deleteContent () {
    return this.deleteInDirection('backward')
  },
  deleteContentBackward () {
    return this.deleteInDirection('backward')
  },
  deleteContentForward () {
    return this.deleteInDirection('forward')
  },
  deleteEntireSoftLine () {
    return this.deleteInDirection('forward')
  },
  deleteHardLineBackward () {
    return this.deleteInDirection('backward')
  },
  deleteHardLineForward () {
    return this.deleteInDirection('forward')
  },
  deleteSoftLineBackward () {
    return this.deleteInDirection('backward')
  },
  deleteSoftLineForward () {
    return this.deleteInDirection('forward')
  },
  deleteWordBackward () {
    return this.deleteInDirection('backward')
  },
  deleteWordForward () {
    return this.deleteInDirection('forward')
  },
  formatBackColor () {
    return this.activateAttributeIfSupported(
      'backgroundColor',
      this.event.data
    )
  },
  formatBold () {
    return this.toggleAttributeIfSupported('bold')
  },
  formatFontColor () {
    return this.activateAttributeIfSupported('color', this.event.data)
  },
  formatFontName () {
    return this.activateAttributeIfSupported('font', this.event.data)
  },
  formatIndent () {
    let n
    if (
      (n = this.responder) !== null &&
                n !== void 0 &&
                n.canIncreaseNestingLevel()
    ) {
      return this.withTargetDOMRange(function () {
        let t
        return (t = this.responder) === null || t === void 0
          ? void 0
          : t.increaseNestingLevel()
      })
    }
  },
  formatItalic () {
    return this.toggleAttributeIfSupported('italic')
  },
  formatJustifyCenter () {
    return this.toggleAttributeIfSupported('justifyCenter')
  },
  formatJustifyFull () {
    return this.toggleAttributeIfSupported('justifyFull')
  },
  formatJustifyLeft () {
    return this.toggleAttributeIfSupported('justifyLeft')
  },
  formatJustifyRight () {
    return this.toggleAttributeIfSupported('justifyRight')
  },
  formatOutdent () {
    let n
    if (
      (n = this.responder) !== null &&
                n !== void 0 &&
                n.canDecreaseNestingLevel()
    ) {
      return this.withTargetDOMRange(function () {
        let t
        return (t = this.responder) === null || t === void 0
          ? void 0
          : t.decreaseNestingLevel()
      })
    }
  },
  formatRemove () {
    this.withTargetDOMRange(function () {
      for (const e in (n = this.responder) === null || n === void 0
        ? void 0
        : n.getCurrentAttributes()) {
        var n, t;
        (t = this.responder) === null ||
                        t === void 0 ||
                        t.removeCurrentAttribute(e)
      }
    })
  },
  formatSetBlockTextDirection () {
    return this.activateAttributeIfSupported(
      'blockDir',
      this.event.data
    )
  },
  formatSetInlineTextDirection () {
    return this.activateAttributeIfSupported(
      'textDir',
      this.event.data
    )
  },
  formatStrikeThrough () {
    return this.toggleAttributeIfSupported('strike')
  },
  formatSubscript () {
    return this.toggleAttributeIfSupported('sub')
  },
  formatSuperscript () {
    return this.toggleAttributeIfSupported('sup')
  },
  formatUnderline () {
    return this.toggleAttributeIfSupported('underline')
  },
  historyRedo () {
    let n
    return (n = this.delegate) === null || n === void 0
      ? void 0
      : n.inputControllerWillPerformRedo()
  },
  historyUndo () {
    let n
    return (n = this.delegate) === null || n === void 0
      ? void 0
      : n.inputControllerWillPerformUndo()
  },
  insertCompositionText () {
    return (this.composing = !0), this.insertString(this.event.data)
  },
  insertFromComposition () {
    return (this.composing = !1), this.insertString(this.event.data)
  },
  insertFromDrop () {
    const n = this.deleteByDragRange
    let t
    if (n) {
      return (
        (this.deleteByDragRange = null),
        (t = this.delegate) === null ||
                        t === void 0 ||
                        t.inputControllerWillMoveText(),
        this.withTargetDOMRange(function () {
          let e
          return (e = this.responder) === null || e === void 0
            ? void 0
            : e.moveTextFromRange(n)
        })
      )
    }
  },
  insertFromPaste () {
    const { dataTransfer: n } = this.event
    const t = { dataTransfer: n }
    const e = n.getData('URL')
    const i = n.getData('text/html')
    if (e) {
      let r
      let l
      this.event.preventDefault(), (t.type = 'text/html')
      const c = n.getData('public.url-name');
      (l = c ? _e(c).trim() : e),
      (t.html = this.createLinkHTML(e, l)),
      (r = this.delegate) === null ||
                        r === void 0 ||
                        r.inputControllerWillPaste(t),
      this.withTargetDOMRange(function () {
        let u
        return (u = this.responder) === null || u === void 0
          ? void 0
          : u.insertHTML(t.html)
      }),
      (this.afterRender = () => {
        let u
        return (u = this.delegate) === null || u === void 0
          ? void 0
          : u.inputControllerDidPaste(t)
      })
    } else if (Ri(n)) {
      let o;
      (t.type = 'text/plain'),
      (t.string = n.getData('text/plain')),
      (o = this.delegate) === null ||
                        o === void 0 ||
                        o.inputControllerWillPaste(t),
      this.withTargetDOMRange(function () {
        let l
        return (l = this.responder) === null || l === void 0
          ? void 0
          : l.insertString(t.string)
      }),
      (this.afterRender = () => {
        let l
        return (l = this.delegate) === null || l === void 0
          ? void 0
          : l.inputControllerDidPaste(t)
      })
    } else if (Kn(this.event)) {
      let s;
      (t.type = 'File'),
      (t.file = n.files[0]),
      (s = this.delegate) === null ||
                        s === void 0 ||
                        s.inputControllerWillPaste(t),
      this.withTargetDOMRange(function () {
        let l
        return (l = this.responder) === null || l === void 0
          ? void 0
          : l.insertFile(t.file)
      }),
      (this.afterRender = () => {
        let l
        return (l = this.delegate) === null || l === void 0
          ? void 0
          : l.inputControllerDidPaste(t)
      })
    } else if (i) {
      let a
      this.event.preventDefault(),
      (t.type = 'text/html'),
      (t.html = i),
      (a = this.delegate) === null ||
                        a === void 0 ||
                        a.inputControllerWillPaste(t),
      this.withTargetDOMRange(function () {
        let l
        return (l = this.responder) === null || l === void 0
          ? void 0
          : l.insertHTML(t.html)
      }),
      (this.afterRender = () => {
        let l
        return (l = this.delegate) === null || l === void 0
          ? void 0
          : l.inputControllerDidPaste(t)
      })
    }
  },
  insertFromYank () {
    return this.insertString(this.event.data)
  },
  insertLineBreak () {
    return this.insertString(`
`)
  },
  insertLink () {
    return this.activateAttributeIfSupported('href', this.event.data)
  },
  insertOrderedList () {
    return this.toggleAttributeIfSupported('number')
  },
  insertParagraph () {
    let n
    return (
      (n = this.delegate) === null ||
                    n === void 0 ||
                    n.inputControllerWillPerformTyping(),
      this.withTargetDOMRange(function () {
        let t
        return (t = this.responder) === null || t === void 0
          ? void 0
          : t.insertLineBreak()
      })
    )
  },
  insertReplacementText () {
    const n = this.event.dataTransfer.getData('text/plain')
    const t = this.event.getTargetRanges()[0]
    this.withTargetDOMRange(t, () => {
      this.insertString(n, { updatePosition: !1 })
    })
  },
  insertText () {
    let n
    return this.insertString(
      this.event.data ||
                    ((n = this.event.dataTransfer) === null || n === void 0
                      ? void 0
                      : n.getData('text/plain'))
    )
  },
  insertTranspose () {
    return this.insertString(this.event.data)
  },
  insertUnorderedList () {
    return this.toggleAttributeIfSupported('bullet')
  }
})
var Jn = function (n) {
  const t = document.createRange()
  return (
    t.setStart(n.startContainer, n.startOffset),
    t.setEnd(n.endContainer, n.endOffset),
    t
  )
}
var Ce = (n) => {
  let t
  return Array.from(
    ((t = n.dataTransfer) === null || t === void 0 ? void 0 : t.types) ||
            []
  ).includes('Files')
}
var Kn = (n) => {
  let t
  return (
    ((t = n.dataTransfer.files) === null || t === void 0 ? void 0 : t[0]) &&
        !Oi(n) &&
        !((e) => {
          const { dataTransfer: i } = e
          return (
            i.types.includes('Files') &&
                i.types.includes('text/html') &&
                i
                  .getData('text/html')
                  .includes('urn:schemas-microsoft-com:office:office')
          )
        })(n)
  )
}
var Oi = function (n) {
  const t = n.clipboardData
  if (t) {
    return (
      Array.from(t.types).filter((e) => e.match(/file/i)).length ===
                t.types.length && t.files.length >= 1
    )
  }
}
var $n = function (n) {
  const t = n.clipboardData
  if (t) return t.types.includes('text/plain') && t.types.length === 1
}
var Gn = function (n) {
  const t = []
  return (
    n.altKey && t.push('alt'),
    n.shiftKey && t.push('shift'),
    t.push(n.key),
    t
  )
}
var ke = (n) => ({ x: n.clientX, y: n.clientY })
const Oe = '[data-trix-attribute]'
const Me = '[data-trix-action]'
const Xn = ''.concat(Oe, ', ').concat(Me)
const ee = '[data-trix-dialog]'
const Yn = ''.concat(ee, '[data-trix-active]')
const Zn = ''.concat(ee, ' [data-trix-method]')
const mi = ''.concat(ee, ' [data-trix-input]')
const pi = (n, t) => (
  t || (t = rt(n)),
  n.querySelector("[data-trix-input][name='".concat(t, "']"))
)
const fi = (n) => n.getAttribute('data-trix-action')
var rt = (n) =>
  n.getAttribute('data-trix-attribute') ||
    n.getAttribute('data-trix-dialog-attribute')
const Yt = class extends f {
  constructor (t) {
    super(t),
    (this.didClickActionButton = this.didClickActionButton.bind(this)),
    (this.didClickAttributeButton =
                this.didClickAttributeButton.bind(this)),
    (this.didClickDialogButton = this.didClickDialogButton.bind(this)),
    (this.didKeyDownDialogInput =
                this.didKeyDownDialogInput.bind(this)),
    (this.element = t),
    (this.attributes = {}),
    (this.actions = {}),
    this.resetDialogInputs(),
    p('mousedown', {
      onElement: this.element,
      matchingSelector: Me,
      withCallback: this.didClickActionButton
    }),
    p('mousedown', {
      onElement: this.element,
      matchingSelector: Oe,
      withCallback: this.didClickAttributeButton
    }),
    p('click', {
      onElement: this.element,
      matchingSelector: Xn,
      preventDefault: !0
    }),
    p('click', {
      onElement: this.element,
      matchingSelector: Zn,
      withCallback: this.didClickDialogButton
    }),
    p('keydown', {
      onElement: this.element,
      matchingSelector: mi,
      withCallback: this.didKeyDownDialogInput
    })
  }

  didClickActionButton (t, e) {
    let i;
    (i = this.delegate) === null ||
            i === void 0 ||
            i.toolbarDidClickButton(),
    t.preventDefault()
    const r = fi(e)
    return this.getDialog(r)
      ? this.toggleDialog(r)
      : (o = this.delegate) === null || o === void 0
          ? void 0
          : o.toolbarDidInvokeAction(r, e)
    let o
  }

  didClickAttributeButton (t, e) {
    let i;
    (i = this.delegate) === null ||
            i === void 0 ||
            i.toolbarDidClickButton(),
    t.preventDefault()
    const r = rt(e)
    let o
    return (
      this.getDialog(r)
        ? this.toggleDialog(r)
        : (o = this.delegate) === null ||
                  o === void 0 ||
                  o.toolbarDidToggleAttribute(r),
      this.refreshAttributeButtons()
    )
  }

  didClickDialogButton (t, e) {
    const i = q(e, { matchingSelector: ee })
    return this[e.getAttribute('data-trix-method')].call(this, i)
  }

  didKeyDownDialogInput (t, e) {
    if (t.keyCode === 13) {
      t.preventDefault()
      const i = e.getAttribute('name')
      const r = this.getDialog(i)
      this.setAttribute(r)
    }
    if (t.keyCode === 27) return t.preventDefault(), this.hideDialog()
  }

  updateActions (t) {
    return (this.actions = t), this.refreshActionButtons()
  }

  refreshActionButtons () {
    return this.eachActionButton((t, e) => {
      t.disabled = this.actions[e] === !1
    })
  }

  eachActionButton (t) {
    return Array.from(this.element.querySelectorAll(Me)).map((e) =>
      t(e, fi(e))
    )
  }

  updateAttributes (t) {
    return (this.attributes = t), this.refreshAttributeButtons()
  }

  refreshAttributeButtons () {
    return this.eachAttributeButton(
      (t, e) => (
        (t.disabled = this.attributes[e] === !1),
        this.attributes[e] || this.dialogIsVisible(e)
          ? (t.setAttribute('data-trix-active', ''),
            t.classList.add('trix-active'))
          : (t.removeAttribute('data-trix-active'),
            t.classList.remove('trix-active'))
      )
    )
  }

  eachAttributeButton (t) {
    return Array.from(this.element.querySelectorAll(Oe)).map((e) =>
      t(e, rt(e))
    )
  }

  applyKeyboardCommand (t) {
    const e = JSON.stringify(t.sort())
    for (const i of Array.from(
      this.element.querySelectorAll('[data-trix-key]')
    )) {
      const r = i.getAttribute('data-trix-key').split('+')
      if (JSON.stringify(r.sort()) === e) {
        return vt('mousedown', { onElement: i }), !0
      }
    }
    return !1
  }

  dialogIsVisible (t) {
    const e = this.getDialog(t)
    if (e) return e.hasAttribute('data-trix-active')
  }

  toggleDialog (t) {
    return this.dialogIsVisible(t) ? this.hideDialog() : this.showDialog(t)
  }

  showDialog (t) {
    let e, i
    this.hideDialog(),
    (e = this.delegate) === null ||
                e === void 0 ||
                e.toolbarWillShowDialog()
    const r = this.getDialog(t)
    r.setAttribute('data-trix-active', ''),
    r.classList.add('trix-active'),
    Array.from(r.querySelectorAll('input[disabled]')).forEach((s) => {
      s.removeAttribute('disabled')
    })
    const o = rt(r)
    if (o) {
      const s = pi(r, t)
      s && ((s.value = this.attributes[o] || ''), s.select())
    }
    return (i = this.delegate) === null || i === void 0
      ? void 0
      : i.toolbarDidShowDialog(t)
  }

  setAttribute (t) {
    const e = rt(t)
    const i = pi(t, e)
    return i.willValidate && !i.checkValidity()
      ? (i.setAttribute('data-trix-validate', ''),
        i.classList.add('trix-validate'),
        i.focus())
      : ((r = this.delegate) === null ||
                  r === void 0 ||
                  r.toolbarDidUpdateAttribute(e, i.value),
        this.hideDialog())
    let r
  }

  removeAttribute (t) {
    let e
    const i = rt(t)
    return (
      (e = this.delegate) === null ||
                e === void 0 ||
                e.toolbarDidRemoveAttribute(i),
      this.hideDialog()
    )
  }

  hideDialog () {
    const t = this.element.querySelector(Yn)
    let e
    if (t) {
      return (
        t.removeAttribute('data-trix-active'),
        t.classList.remove('trix-active'),
        this.resetDialogInputs(),
        (e = this.delegate) === null || e === void 0
          ? void 0
          : e.toolbarDidHideDialog(
            ((i) => i.getAttribute('data-trix-dialog'))(t)
          )
      )
    }
  }

  resetDialogInputs () {
    Array.from(this.element.querySelectorAll(mi)).forEach((t) => {
      t.setAttribute('disabled', 'disabled'),
      t.removeAttribute('data-trix-validate'),
      t.classList.remove('trix-validate')
    })
  }

  getDialog (t) {
    return this.element.querySelector('[data-trix-dialog='.concat(t, ']'))
  }
}
const X = class extends $t {
  constructor (t) {
    const { editorElement: e, document: i, html: r } = t
    super(...arguments),
    (this.editorElement = e),
    (this.selectionManager = new I(this.editorElement)),
    (this.selectionManager.delegate = this),
    (this.composition = new F()),
    (this.composition.delegate = this),
    (this.attachmentManager = new Ut(
      this.composition.getAttachments()
    )),
    (this.attachmentManager.delegate = this),
    (this.inputController =
                qe.getLevel() === 2
                  ? new G(this.editorElement)
                  : new w(this.editorElement)),
    (this.inputController.delegate = this),
    (this.inputController.responder = this.composition),
    (this.compositionController = new Kt(
      this.editorElement,
      this.composition
    )),
    (this.compositionController.delegate = this),
    (this.toolbarController = new Yt(
      this.editorElement.toolbarElement
    )),
    (this.toolbarController.delegate = this),
    (this.editor = new Ht(
      this.composition,
      this.selectionManager,
      this.editorElement
    )),
    i ? this.editor.loadDocument(i) : this.editor.loadHTML(r)
  }

  registerSelectionManager () {
    return tt.registerSelectionManager(this.selectionManager)
  }

  unregisterSelectionManager () {
    return tt.unregisterSelectionManager(this.selectionManager)
  }

  render () {
    return this.compositionController.render()
  }

  reparse () {
    return this.composition.replaceHTML(this.editorElement.innerHTML)
  }

  compositionDidChangeDocument (t) {
    if (
      (this.notifyEditorElement('document-change'), !this.handlingInput)
    ) {
      return this.render()
    }
  }

  compositionDidChangeCurrentAttributes (t) {
    return (
      (this.currentAttributes = t),
      this.toolbarController.updateAttributes(this.currentAttributes),
      this.updateCurrentActions(),
      this.notifyEditorElement('attributes-change', {
        attributes: this.currentAttributes
      })
    )
  }

  compositionDidPerformInsertionAtRange (t) {
    this.pasting && (this.pastedRange = t)
  }

  compositionShouldAcceptFile (t) {
    return this.notifyEditorElement('file-accept', { file: t })
  }

  compositionDidAddAttachment (t) {
    const e = this.attachmentManager.manageAttachment(t)
    return this.notifyEditorElement('attachment-add', {
      attachment: e
    })
  }

  compositionDidEditAttachment (t) {
    this.compositionController.rerenderViewForObject(t)
    const e = this.attachmentManager.manageAttachment(t)
    return (
      this.notifyEditorElement('attachment-edit', { attachment: e }),
      this.notifyEditorElement('change')
    )
  }

  compositionDidChangeAttachmentPreviewURL (t) {
    return (
      this.compositionController.invalidateViewForObject(t),
      this.notifyEditorElement('change')
    )
  }

  compositionDidRemoveAttachment (t) {
    const e = this.attachmentManager.unmanageAttachment(t)
    return this.notifyEditorElement('attachment-remove', {
      attachment: e
    })
  }

  compositionDidStartEditingAttachment (t, e) {
    return (
      (this.attachmentLocationRange =
                this.composition.document.getLocationRangeOfAttachment(t)),
      this.compositionController.installAttachmentEditorForAttachment(
        t,
        e
      ),
      this.selectionManager.setLocationRange(this.attachmentLocationRange)
    )
  }

  compositionDidStopEditingAttachment (t) {
    this.compositionController.uninstallAttachmentEditor(),
    (this.attachmentLocationRange = null)
  }

  compositionDidRequestChangingSelectionToLocationRange (t) {
    if (!this.loadingSnapshot || this.isFocused()) {
      return (
        (this.requestedLocationRange = t),
        (this.compositionRevisionWhenLocationRangeRequested =
                    this.composition.revision),
        this.handlingInput ? void 0 : this.render()
      )
    }
  }

  compositionWillLoadSnapshot () {
    this.loadingSnapshot = !0
  }

  compositionDidLoadSnapshot () {
    this.compositionController.refreshViewCache(),
    this.render(),
    (this.loadingSnapshot = !1)
  }

  getSelectionManager () {
    return this.selectionManager
  }

  attachmentManagerDidRequestRemovalOfAttachment (t) {
    return this.removeAttachment(t)
  }

  compositionControllerWillSyncDocumentView () {
    return (
      this.inputController.editorWillSyncDocumentView(),
      this.selectionManager.lock(),
      this.selectionManager.clearSelection()
    )
  }

  compositionControllerDidSyncDocumentView () {
    return (
      this.inputController.editorDidSyncDocumentView(),
      this.selectionManager.unlock(),
      this.updateCurrentActions(),
      this.notifyEditorElement('sync')
    )
  }

  compositionControllerDidRender () {
    this.requestedLocationRange &&
            (this.compositionRevisionWhenLocationRangeRequested ===
                this.composition.revision &&
                this.selectionManager.setLocationRange(
                  this.requestedLocationRange
                ),
            (this.requestedLocationRange = null),
            (this.compositionRevisionWhenLocationRangeRequested = null)),
    this.renderedCompositionRevision !== this.composition.revision &&
                (this.runEditorFilters(),
                this.composition.updateCurrentAttributes(),
                this.notifyEditorElement('render')),
    (this.renderedCompositionRevision = this.composition.revision)
  }

  compositionControllerDidFocus () {
    return (
      this.isFocusedInvisibly() &&
                this.setLocationRange({ index: 0, offset: 0 }),
      this.toolbarController.hideDialog(),
      this.notifyEditorElement('focus')
    )
  }

  compositionControllerDidBlur () {
    return this.notifyEditorElement('blur')
  }

  compositionControllerDidSelectAttachment (t, e) {
    return (
      this.toolbarController.hideDialog(),
      this.composition.editAttachment(t, e)
    )
  }

  compositionControllerDidRequestDeselectingAttachment (t) {
    const e =
            this.attachmentLocationRange ||
            this.composition.document.getLocationRangeOfAttachment(t)
    return this.selectionManager.setLocationRange(e[1])
  }

  compositionControllerWillUpdateAttachment (t) {
    return this.editor.recordUndoEntry('Edit Attachment', {
      context: t.id,
      consolidatable: !0
    })
  }

  compositionControllerDidRequestRemovalOfAttachment (t) {
    return this.removeAttachment(t)
  }

  inputControllerWillHandleInput () {
    (this.handlingInput = !0), (this.requestedRender = !1)
  }

  inputControllerDidRequestRender () {
    this.requestedRender = !0
  }

  inputControllerDidHandleInput () {
    if (((this.handlingInput = !1), this.requestedRender)) {
      return (this.requestedRender = !1), this.render()
    }
  }

  inputControllerDidAllowUnhandledInput () {
    return this.notifyEditorElement('change')
  }

  inputControllerDidRequestReparse () {
    return this.reparse()
  }

  inputControllerWillPerformTyping () {
    return this.recordTypingUndoEntry()
  }

  inputControllerWillPerformFormatting (t) {
    return this.recordFormattingUndoEntry(t)
  }

  inputControllerWillCutText () {
    return this.editor.recordUndoEntry('Cut')
  }

  inputControllerWillPaste (t) {
    return (
      this.editor.recordUndoEntry('Paste'),
      (this.pasting = !0),
      this.notifyEditorElement('before-paste', { paste: t })
    )
  }

  inputControllerDidPaste (t) {
    return (
      (t.range = this.pastedRange),
      (this.pastedRange = null),
      (this.pasting = null),
      this.notifyEditorElement('paste', { paste: t })
    )
  }

  inputControllerWillMoveText () {
    return this.editor.recordUndoEntry('Move')
  }

  inputControllerWillAttachFiles () {
    return this.editor.recordUndoEntry('Drop Files')
  }

  inputControllerWillPerformUndo () {
    return this.editor.undo()
  }

  inputControllerWillPerformRedo () {
    return this.editor.redo()
  }

  inputControllerDidReceiveKeyboardCommand (t) {
    return this.toolbarController.applyKeyboardCommand(t)
  }

  inputControllerDidStartDrag () {
    this.locationRangeBeforeDrag = this.selectionManager.getLocationRange()
  }

  inputControllerDidReceiveDragOverPoint (t) {
    return this.selectionManager.setLocationRangeFromPointRange(t)
  }

  inputControllerDidCancelDrag () {
    this.selectionManager.setLocationRange(this.locationRangeBeforeDrag),
    (this.locationRangeBeforeDrag = null)
  }

  locationRangeDidChange (t) {
    return (
      this.composition.updateCurrentAttributes(),
      this.updateCurrentActions(),
      this.attachmentLocationRange &&
                !Pt(this.attachmentLocationRange, t) &&
                this.composition.stopEditingAttachment(),
      this.notifyEditorElement('selection-change')
    )
  }

  toolbarDidClickButton () {
    if (!this.getLocationRange()) {
      return this.setLocationRange({ index: 0, offset: 0 })
    }
  }

  toolbarDidInvokeAction (t, e) {
    return this.invokeAction(t, e)
  }

  toolbarDidToggleAttribute (t) {
    if (
      (this.recordFormattingUndoEntry(t),
      this.composition.toggleCurrentAttribute(t),
      this.render(),
      !this.selectionFrozen)
    ) {
      return this.editorElement.focus()
    }
  }

  toolbarDidUpdateAttribute (t, e) {
    if (
      (this.recordFormattingUndoEntry(t),
      this.composition.setCurrentAttribute(t, e),
      this.render(),
      !this.selectionFrozen)
    ) {
      return this.editorElement.focus()
    }
  }

  toolbarDidRemoveAttribute (t) {
    if (
      (this.recordFormattingUndoEntry(t),
      this.composition.removeCurrentAttribute(t),
      this.render(),
      !this.selectionFrozen)
    ) {
      return this.editorElement.focus()
    }
  }

  toolbarWillShowDialog (t) {
    return (
      this.composition.expandSelectionForEditing(), this.freezeSelection()
    )
  }

  toolbarDidShowDialog (t) {
    return this.notifyEditorElement('toolbar-dialog-show', {
      dialogName: t
    })
  }

  toolbarDidHideDialog (t) {
    return (
      this.thawSelection(),
      this.editorElement.focus(),
      this.notifyEditorElement('toolbar-dialog-hide', {
        dialogName: t
      })
    )
  }

  freezeSelection () {
    if (!this.selectionFrozen) {
      return (
        this.selectionManager.lock(),
        this.composition.freezeSelection(),
        (this.selectionFrozen = !0),
        this.render()
      )
    }
  }

  thawSelection () {
    if (this.selectionFrozen) {
      return (
        this.composition.thawSelection(),
        this.selectionManager.unlock(),
        (this.selectionFrozen = !1),
        this.render()
      )
    }
  }

  canInvokeAction (t) {
    return (
      !!this.actionIsExternal(t) ||
            !(
              (e = this.actions[t]) === null ||
                e === void 0 ||
                (e = e.test) === null ||
                e === void 0 ||
                !e.call(this)
            )
    )
    let e
  }

  invokeAction (t, e) {
    return this.actionIsExternal(t)
      ? this.notifyEditorElement('action-invoke', {
        actionName: t,
        invokingElement: e
      })
      : (i = this.actions[t]) === null ||
                i === void 0 ||
                (i = i.perform) === null ||
                i === void 0
          ? void 0
          : i.call(this)
    let i
  }

  actionIsExternal (t) {
    return /^x-./.test(t)
  }

  getCurrentActions () {
    const t = {}
    for (const e in this.actions) t[e] = this.canInvokeAction(e)
    return t
  }

  updateCurrentActions () {
    const t = this.getCurrentActions()
    if (!dt(t, this.currentActions)) {
      return (
        (this.currentActions = t),
        this.toolbarController.updateActions(this.currentActions),
        this.notifyEditorElement('actions-change', {
          actions: this.currentActions
        })
      )
    }
  }

  runEditorFilters () {
    let t = this.composition.getSnapshot()
    if (
      (Array.from(this.editor.filters).forEach((r) => {
        const { document: o, selectedRange: s } = t;
        (t = r.call(this.editor, t) || {}),
        t.document || (t.document = o),
        t.selectedRange || (t.selectedRange = s)
      }),
      (e = t),
      (i = this.composition.getSnapshot()),
      !Pt(e.selectedRange, i.selectedRange) ||
                !e.document.isEqualTo(i.document))
    ) {
      return this.composition.loadSnapshot(t)
    }
    let e, i
  }

  updateInputElement () {
    const t = (function (e, i) {
      const r = Dn[i]
      if (r) return r(e)
      throw new Error('unknown content type: '.concat(i))
    })(this.compositionController.getSerializableElement(), 'text/html')
    return this.editorElement.setInputElementValue(t)
  }

  notifyEditorElement (t, e) {
    switch (t) {
      case 'document-change':
        this.documentChangedSinceLastRender = !0
        break
      case 'render':
        this.documentChangedSinceLastRender &&
                    ((this.documentChangedSinceLastRender = !1),
                    this.notifyEditorElement('change'))
        break
      case 'change':
      case 'attachment-add':
      case 'attachment-edit':
      case 'attachment-remove':
        this.updateInputElement()
    }
    return this.editorElement.notify(t, e)
  }

  removeAttachment (t) {
    return (
      this.editor.recordUndoEntry('Delete Attachment'),
      this.composition.removeAttachment(t),
      this.render()
    )
  }

  recordFormattingUndoEntry (t) {
    const e = v(t)
    const i = this.selectionManager.getLocationRange()
    if (e || !N(i)) {
      return this.editor.recordUndoEntry('Formatting', {
        context: this.getUndoContext(),
        consolidatable: !0
      })
    }
  }

  recordTypingUndoEntry () {
    return this.editor.recordUndoEntry('Typing', {
      context: this.getUndoContext(this.currentAttributes),
      consolidatable: !0
    })
  }

  getUndoContext () {
    for (var t = arguments.length, e = new Array(t), i = 0; i < t; i++) {
      e[i] = arguments[i]
    }
    return [
      this.getLocationContext(),
      this.getTimeContext(),
      ...Array.from(e)
    ]
  }

  getLocationContext () {
    const t = this.selectionManager.getLocationRange()
    return N(t) ? t[0].index : t
  }

  getTimeContext () {
    return Re.interval > 0
      ? Math.floor(new Date().getTime() / Re.interval)
      : 0
  }

  isFocused () {
    let t
    return (
      this.editorElement ===
            ((t = this.editorElement.ownerDocument) === null || t === void 0
              ? void 0
              : t.activeElement)
    )
  }

  isFocusedInvisibly () {
    return this.isFocused() && !this.getLocationRange()
  }

  get actions () {
    return this.constructor.actions
  }
}
E(X, 'actions', {
  undo: {
    test () {
      return this.editor.canUndo()
    },
    perform () {
      return this.editor.undo()
    }
  },
  redo: {
    test () {
      return this.editor.canRedo()
    },
    perform () {
      return this.editor.redo()
    }
  },
  link: {
    test () {
      return this.editor.canActivateAttribute('href')
    }
  },
  increaseNestingLevel: {
    test () {
      return this.editor.canIncreaseNestingLevel()
    },
    perform () {
      return this.editor.increaseNestingLevel() && this.render()
    }
  },
  decreaseNestingLevel: {
    test () {
      return this.editor.canDecreaseNestingLevel()
    },
    perform () {
      return this.editor.decreaseNestingLevel() && this.render()
    }
  },
  attachFiles: {
    test: () => !0,
    perform () {
      return qe.pickFiles(this.editor.insertFiles)
    }
  }
}),
X.proxyMethod('getSelectionManager().setLocationRange'),
X.proxyMethod('getSelectionManager().getLocationRange')
const Qn = Object.freeze({
  __proto__: null,
  AttachmentEditorController: Jt,
  CompositionController: Kt,
  Controller: $t,
  EditorController: X,
  InputController: ht,
  Level0InputController: w,
  Level2InputController: G,
  ToolbarController: Yt
})
const tr = Object.freeze({
  __proto__: null,
  MutationObserver: Gt,
  SelectionChangeObserver: It
})
const er = Object.freeze({
  __proto__: null,
  FileVerificationOperation: Xt,
  ImagePreloadOperation: Wt
})
ki(
  'trix-toolbar',
    `%t {
  display: block;
}

%t {
  white-space: nowrap;
}

%t [data-trix-dialog] {
  display: none;
}

%t [data-trix-dialog][data-trix-active] {
  display: block;
}

%t [data-trix-dialog] [data-trix-validate]:invalid {
  background-color: #ffdddd;
}`
)
const Zt = class extends HTMLElement {
  connectedCallback () {
    this.innerHTML === '' && (this.innerHTML = Ci.getDefaultHTML())
  }
}
let ir = 0
const nr = function (n) {
  if (!n.hasAttribute('contenteditable')) {
    return (
      n.setAttribute('contenteditable', ''),
      (function (t) {
        const e =
                    arguments.length > 1 && arguments[1] !== void 0
                      ? arguments[1]
                      : {}
        return (e.times = 1), p(t, e)
      })('focus', { onElement: n, withCallback: () => rr(n) })
    )
  }
}
var rr = function (n) {
  return or(n), sr(n)
}
var or = function (n) {
  let t, e
  if (
    (t = (e = document).queryCommandSupported) !== null &&
        t !== void 0 &&
        t.call(e, 'enableObjectResizing')
  ) {
    return (
      document.execCommand('enableObjectResizing', !1, !1),
      p('mscontrolselect', { onElement: n, preventDefault: !0 })
    )
  }
}
var sr = function (n) {
  let t, e
  if (
    (t = (e = document).queryCommandSupported) !== null &&
        t !== void 0 &&
        t.call(e, 'DefaultParagraphSeparator')
  ) {
    const { tagName: i } = y.default
    if (['div', 'p'].includes(i)) {
      return document.execCommand('DefaultParagraphSeparator', !1, i)
    }
  }
}
const bi = St.forcesObjectResizing
  ? { display: 'inline', width: 'auto' }
  : { display: 'inline-block', width: '1px' }
ki(
  'trix-editor',
    `%t {
    display: block;
}

%t:empty::before {
    content: attr(placeholder);
    color: graytext;
    cursor: text;
    pointer-events: none;
    white-space: pre-line;
}

%t a[contenteditable=false] {
    cursor: text;
}

%t img {
    max-width: 100%;
    height: auto;
}

%t `
      .concat(
        K,
            ` figcaption textarea {
    resize: none;
}

%t `
      )
      .concat(
        K,
            ` figcaption textarea.trix-autoresize-clone {
    position: absolute;
    left: -9999px;
    max-height: 0px;
}

%t `
      )
      .concat(
        K,
            ` figcaption[data-trix-placeholder]:empty::before {
    content: attr(data-trix-placeholder);
    color: graytext;
}

%t [data-trix-cursor-target] {
    display: `
      )
      .concat(
        bi.display,
            ` !important;
    width: `
      )
      .concat(
        bi.width,
            ` !important;
    padding: 0 !important;
    margin: 0 !important;
    border: none !important;
}

%t [data-trix-cursor-target=left] {
    vertical-align: top !important;
    margin-left: -1px !important;
}

%t [data-trix-cursor-target=right] {
    vertical-align: bottom !important;
    margin-right: -1px !important;
}`
      )
)
const Qt = class extends HTMLElement {
  get trixId () {
    return this.hasAttribute('trix-id')
      ? this.getAttribute('trix-id')
      : (this.setAttribute('trix-id', ++ir), this.trixId)
  }

  get labels () {
    const t = []
    this.id &&
            this.ownerDocument &&
            t.push(
              ...Array.from(
                this.ownerDocument.querySelectorAll(
                  "label[for='".concat(this.id, "']")
                ) || []
              )
            )
    const e = q(this, { matchingSelector: 'label' })
    return e && [this, null].includes(e.control) && t.push(e), t
  }

  get toolbarElement () {
    let t
    if (this.hasAttribute('toolbar')) {
      return (t = this.ownerDocument) === null || t === void 0
        ? void 0
        : t.getElementById(this.getAttribute('toolbar'))
    }
    if (this.parentNode) {
      const e = 'trix-toolbar-'.concat(this.trixId)
      this.setAttribute('toolbar', e)
      const i = d('trix-toolbar', { id: e })
      return this.parentNode.insertBefore(i, this), i
    }
  }

  get form () {
    let t
    return (t = this.inputElement) === null || t === void 0
      ? void 0
      : t.form
  }

  get inputElement () {
    let t
    if (this.hasAttribute('input')) {
      return (t = this.ownerDocument) === null || t === void 0
        ? void 0
        : t.getElementById(this.getAttribute('input'))
    }
    if (this.parentNode) {
      const e = 'trix-input-'.concat(this.trixId)
      this.setAttribute('input', e)
      const i = d('input', { type: 'hidden', id: e })
      return this.parentNode.insertBefore(i, this.nextElementSibling), i
    }
  }

  get editor () {
    let t
    return (t = this.editorController) === null || t === void 0
      ? void 0
      : t.editor
  }

  get name () {
    let t
    return (t = this.inputElement) === null || t === void 0
      ? void 0
      : t.name
  }

  get value () {
    let t
    return (t = this.inputElement) === null || t === void 0
      ? void 0
      : t.value
  }

  set value (t) {
    let e;
    (this.defaultValue = t),
    (e = this.editor) === null ||
                e === void 0 ||
                e.loadHTML(this.defaultValue)
  }

  notify (t, e) {
    if (this.editorController) {
      return vt('trix-'.concat(t), {
        onElement: this,
        attributes: e
      })
    }
  }

  setInputElementValue (t) {
    this.inputElement && (this.inputElement.value = t)
  }

  connectedCallback () {
    this.hasAttribute('data-trix-internal') ||
            (nr(this),
            (function (t) {
              t.hasAttribute('role') || t.setAttribute('role', 'textbox')
            })(this),
            (function (t) {
              if (
                t.hasAttribute('aria-label') ||
                    t.hasAttribute('aria-labelledby')
              ) {
                return
              }
              const e = function () {
                const i = Array.from(t.labels)
                  .map((o) => {
                    if (!o.contains(t)) return o.textContent
                  })
                  .filter((o) => o)
                const r = i.join(' ')
                return r
                  ? t.setAttribute('aria-label', r)
                  : t.removeAttribute('aria-label')
              }
              e(), p('focus', { onElement: t, withCallback: e })
            })(this),
            this.editorController ||
                (vt('trix-before-initialize', { onElement: this }),
                (this.editorController = new X({
                  editorElement: this,
                  html: (this.defaultValue = this.value)
                })),
                requestAnimationFrame(() =>
                  vt('trix-initialize', { onElement: this })
                )),
            this.editorController.registerSelectionManager(),
            this.registerResetListener(),
            this.registerClickListener(),
            (function (t) {
              !document.querySelector(':focus') &&
                    t.hasAttribute('autofocus') &&
                    document.querySelector('[autofocus]') === t &&
                    t.focus()
            })(this))
  }

  disconnectedCallback () {
    let t
    return (
      (t = this.editorController) === null ||
                t === void 0 ||
                t.unregisterSelectionManager(),
      this.unregisterResetListener(),
      this.unregisterClickListener()
    )
  }

  registerResetListener () {
    return (
      (this.resetListener = this.resetBubbled.bind(this)),
      window.addEventListener('reset', this.resetListener, !1)
    )
  }

  unregisterResetListener () {
    return window.removeEventListener('reset', this.resetListener, !1)
  }

  registerClickListener () {
    return (
      (this.clickListener = this.clickBubbled.bind(this)),
      window.addEventListener('click', this.clickListener, !1)
    )
  }

  unregisterClickListener () {
    return window.removeEventListener('click', this.clickListener, !1)
  }

  resetBubbled (t) {
    if (!t.defaultPrevented && t.target === this.form) {
      return this.reset()
    }
  }

  clickBubbled (t) {
    if (t.defaultPrevented || this.contains(t.target)) return
    const e = q(t.target, { matchingSelector: 'label' })
    return e && Array.from(this.labels).includes(e) ? this.focus() : void 0
  }

  reset () {
    this.value = this.defaultValue
  }
}
const T = {
  VERSION: Mi,
  config: Lt,
  core: wn,
  models: Pi,
  views: In,
  controllers: Qn,
  observers: tr,
  operations: er,
  elements: Object.freeze({
    __proto__: null,
    TrixEditorElement: Qt,
    TrixToolbarElement: Zt
  }),
  filters: Object.freeze({
    __proto__: null,
    Filter: Vt,
    attachmentGalleryFilter: Bi
  })
}
Object.assign(T, Pi),
(window.Trix = T),
setTimeout(function () {
  customElements.get('trix-toolbar') ||
            customElements.define('trix-toolbar', Zt),
  customElements.get('trix-editor') ||
                customElements.define('trix-editor', Qt)
}, 0)
T.config.blockAttributes.default.tagName = 'p'
T.config.blockAttributes.default.breakOnReturn = !0
T.config.blockAttributes.heading = {
  tagName: 'h2',
  terminal: !0,
  breakOnReturn: !0,
  group: !1
}
T.config.blockAttributes.subHeading = {
  tagName: 'h3',
  terminal: !0,
  breakOnReturn: !0,
  group: !1
}
T.config.textAttributes.underline = {
  style: { textDecoration: 'underline' },
  inheritable: !0,
  parser: (n) =>
    window.getComputedStyle(n).textDecoration.includes('underline')
}
T.Block.prototype.breaksOnReturn = function () {
  const n = this.getLastAttribute()
  return T.config.blockAttributes[n || 'default']?.breakOnReturn ?? !1
}
T.LineBreakInsertion.prototype.shouldInsertBlockBreak = function () {
  return this.block.hasAttributes() &&
        this.block.isListItem() &&
        !this.block.isEmpty()
    ? this.startLocation.offset > 0
    : this.shouldBreakFormattedBlock()
      ? !1
      : this.breaksOnReturn
}
function ar ({ state: n }) {
  return {
    state: n,
    init: function () {
      (this.$refs.trixValue.value = this.state),
      this.$refs.trix.editor?.loadHTML(this.state ?? ''),
      this.$watch('state', () => {
        document.activeElement !== this.$refs.trix &&
                        ((this.$refs.trixValue.value = this.state),
                        this.$refs.trix.editor?.loadHTML(this.state ?? ''))
      })
    }
  }
}
export { ar as default }
