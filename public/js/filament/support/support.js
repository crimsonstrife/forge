(() => {
  const qo = Object.create
  const Ti = Object.defineProperty
  const Go = Object.getOwnPropertyDescriptor
  const Ko = Object.getOwnPropertyNames
  const Jo = Object.getPrototypeOf
  const Qo = Object.prototype.hasOwnProperty
  const Kr = (e, t) => () => (
    t || e((t = { exports: {} }).exports, t),
    t.exports
  )
  const Zo = (e, t, r, n) => {
    if ((t && typeof t === 'object') || typeof t === 'function') {
      for (const i of Ko(t)) {
        !Qo.call(e, i) &&
                    i !== r &&
                    Ti(e, i, {
                      get: () => t[i],
                      enumerable: !(n = Go(t, i)) || n.enumerable
                    })
      }
    }
    return e
  }
  const ea = (e, t, r) => (
    (r = e != null ? qo(Jo(e)) : {}),
    Zo(
      t || !e || !e.__esModule
        ? Ti(r, 'default', { value: e, enumerable: !0 })
        : r,
      e
    )
  )
  const uo = Kr(() => {})
  const po = Kr(() => {})
  const ho = Kr((Hs, yr) => {
    (function () {
      'use strict'
      const e = 'input is invalid type'
      const t = 'finalize already called'
      let r = typeof window === 'object'
      let n = r ? window : {}
      n.JS_MD5_NO_WINDOW && (r = !1)
      const i = !r && typeof self === 'object'
      const o =
                !n.JS_MD5_NO_NODE_JS &&
                typeof process === 'object' &&
                process.versions &&
                process.versions.node
      o ? (n = global) : i && (n = self)
      const a =
                !n.JS_MD5_NO_COMMON_JS && typeof yr === 'object' && yr.exports
      const c = typeof define === 'function' && define.amd
      const f = !n.JS_MD5_NO_ARRAY_BUFFER && typeof ArrayBuffer < 'u'
      const d = '0123456789abcdef'.split('')
      const y = [128, 32768, 8388608, -2147483648]
      const m = [0, 8, 16, 24]
      const O = [
        'hex',
        'array',
        'digest',
        'buffer',
        'arrayBuffer',
        'base64'
      ]
      const E =
                'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/'.split(
                  ''
                )
      let S = []
      let _
      if (f) {
        const I = new ArrayBuffer(68);
        ((_ = new Uint8Array(I)), (S = new Uint32Array(I)))
      }
      let $ = Array.isArray;
      (n.JS_MD5_NO_NODE_JS || !$) &&
                ($ = function (l) {
                  return (
                    Object.prototype.toString.call(l) === '[object Array]'
                  )
                })
      let A = ArrayBuffer.isView
      f &&
                (n.JS_MD5_NO_ARRAY_BUFFER_IS_VIEW || !A) &&
                (A = function (l) {
                  return (
                    typeof l === 'object' &&
                        l.buffer &&
                        l.buffer.constructor === ArrayBuffer
                  )
                })
      const N = function (l) {
        const h = typeof l
        if (h === 'string') return [l, !0]
        if (h !== 'object' || l === null) throw new Error(e)
        if (f && l.constructor === ArrayBuffer) {
          return [new Uint8Array(l), !1]
        }
        if (!$(l) && !A(l)) throw new Error(e)
        return [l, !1]
      }
      const Y = function (l) {
        return function (h) {
          return new X(!0).update(h)[l]()
        }
      }
      const ne = function () {
        let l = Y('hex');
        (o && (l = J(l)),
        (l.create = function () {
          return new X()
        }),
        (l.update = function (p) {
          return l.create().update(p)
        }))
        for (let h = 0; h < O.length; ++h) {
          const v = O[h]
          l[v] = Y(v)
        }
        return l
      }
      var J = function (l) {
        const h = uo()
        const v = po().Buffer
        let p
        v.from && !n.JS_MD5_NO_BUFFER_FROM
          ? (p = v.from)
          : (p = function (M) {
              return new v(M)
            })
        const j = function (M) {
          if (typeof M === 'string') {
            return h
              .createHash('md5')
              .update(M, 'utf8')
              .digest('hex')
          }
          if (M == null) throw new Error(e)
          return (
            M.constructor === ArrayBuffer &&
                            (M = new Uint8Array(M)),
            $(M) || A(M) || M.constructor === v
              ? h.createHash('md5').update(p(M)).digest('hex')
              : l(M)
          )
        }
        return j
      }
      const V = function (l) {
        return function (h, v) {
          return new Q(h, !0).update(v)[l]()
        }
      }
      const de = function () {
        const l = V('hex');
        ((l.create = function (p) {
          return new Q(p)
        }),
        (l.update = function (p, j) {
          return l.create(p).update(j)
        }))
        for (let h = 0; h < O.length; ++h) {
          const v = O[h]
          l[v] = V(v)
        }
        return l
      }
      function X (l) {
        if (l) {
          ((S[0] =
                        S[16] =
                        S[1] =
                        S[2] =
                        S[3] =
                        S[4] =
                        S[5] =
                        S[6] =
                        S[7] =
                        S[8] =
                        S[9] =
                        S[10] =
                        S[11] =
                        S[12] =
                        S[13] =
                        S[14] =
                        S[15] =
                            0),
          (this.blocks = S),
          (this.buffer8 = _))
        } else if (f) {
          const h = new ArrayBuffer(68);
          ((this.buffer8 = new Uint8Array(h)),
          (this.blocks = new Uint32Array(h)))
        } else {
          this.blocks = [
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0
          ]
        }
        ((this.h0 =
                    this.h1 =
                    this.h2 =
                    this.h3 =
                    this.start =
                    this.bytes =
                    this.hBytes =
                        0),
        (this.finalized = this.hashed = !1),
        (this.first = !0))
      }
      ((X.prototype.update = function (l) {
        if (this.finalized) throw new Error(t)
        const h = N(l)
        l = h[0]
        for (
          var v = h[1],
            p,
            j = 0,
            M,
            R = l.length,
            Z = this.blocks,
            ze = this.buffer8;
          j < R;

        ) {
          if (
            (this.hashed &&
                            ((this.hashed = !1),
                            (Z[0] = Z[16]),
                            (Z[16] =
                                Z[1] =
                                Z[2] =
                                Z[3] =
                                Z[4] =
                                Z[5] =
                                Z[6] =
                                Z[7] =
                                Z[8] =
                                Z[9] =
                                Z[10] =
                                Z[11] =
                                Z[12] =
                                Z[13] =
                                Z[14] =
                                Z[15] =
                                    0)),
            v)
          ) {
            if (f) {
              for (M = this.start; j < R && M < 64; ++j) {
                ((p = l.charCodeAt(j)),
                p < 128
                  ? (ze[M++] = p)
                  : p < 2048
                    ? ((ze[M++] = 192 | (p >>> 6)),
                      (ze[M++] = 128 | (p & 63)))
                    : p < 55296 || p >= 57344
                      ? ((ze[M++] = 224 | (p >>> 12)),
                        (ze[M++] =
                                                  128 | ((p >>> 6) & 63)),
                        (ze[M++] = 128 | (p & 63)))
                      : ((p =
                                                  65536 +
                                                  (((p & 1023) << 10) |
                                                      (l.charCodeAt(++j) &
                                                          1023))),
                        (ze[M++] = 240 | (p >>> 18)),
                        (ze[M++] =
                                                  128 | ((p >>> 12) & 63)),
                        (ze[M++] =
                                                  128 | ((p >>> 6) & 63)),
                        (ze[M++] = 128 | (p & 63))))
              }
            } else {
              for (M = this.start; j < R && M < 64; ++j) {
                ((p = l.charCodeAt(j)),
                p < 128
                  ? (Z[M >>> 2] |= p << m[M++ & 3])
                  : p < 2048
                    ? ((Z[M >>> 2] |=
                                                (192 | (p >>> 6)) <<
                                                m[M++ & 3]),
                      (Z[M >>> 2] |=
                                                (128 | (p & 63)) << m[M++ & 3]))
                    : p < 55296 || p >= 57344
                      ? ((Z[M >>> 2] |=
                                                  (224 | (p >>> 12)) <<
                                                  m[M++ & 3]),
                        (Z[M >>> 2] |=
                                                  (128 | ((p >>> 6) & 63)) <<
                                                  m[M++ & 3]),
                        (Z[M >>> 2] |=
                                                  (128 | (p & 63)) <<
                                                  m[M++ & 3]))
                      : ((p =
                                                  65536 +
                                                  (((p & 1023) << 10) |
                                                      (l.charCodeAt(++j) &
                                                          1023))),
                        (Z[M >>> 2] |=
                                                  (240 | (p >>> 18)) <<
                                                  m[M++ & 3]),
                        (Z[M >>> 2] |=
                                                  (128 | ((p >>> 12) & 63)) <<
                                                  m[M++ & 3]),
                        (Z[M >>> 2] |=
                                                  (128 | ((p >>> 6) & 63)) <<
                                                  m[M++ & 3]),
                        (Z[M >>> 2] |=
                                                  (128 | (p & 63)) <<
                                                  m[M++ & 3])))
              }
            }
          } else if (f) {
            for (M = this.start; j < R && M < 64; ++j) {
              ze[M++] = l[j]
            }
          } else {
            for (M = this.start; j < R && M < 64; ++j) {
              Z[M >>> 2] |= l[j] << m[M++ & 3]
            }
          }
          ((this.lastByteIndex = M),
          (this.bytes += M - this.start),
          M >= 64
            ? ((this.start = M - 64),
              this.hash(),
              (this.hashed = !0))
            : (this.start = M))
        }
        return (
          this.bytes > 4294967295 &&
                        ((this.hBytes += (this.bytes / 4294967296) << 0),
                        (this.bytes = this.bytes % 4294967296)),
          this
        )
      }),
      (X.prototype.finalize = function () {
        if (!this.finalized) {
          this.finalized = !0
          const l = this.blocks
          const h = this.lastByteIndex;
          ((l[h >>> 2] |= y[h & 3]),
          h >= 56 &&
                                (this.hashed || this.hash(),
                                (l[0] = l[16]),
                                (l[16] =
                                    l[1] =
                                    l[2] =
                                    l[3] =
                                    l[4] =
                                    l[5] =
                                    l[6] =
                                    l[7] =
                                    l[8] =
                                    l[9] =
                                    l[10] =
                                    l[11] =
                                    l[12] =
                                    l[13] =
                                    l[14] =
                                    l[15] =
                                        0)),
          (l[14] = this.bytes << 3),
          (l[15] = (this.hBytes << 3) | (this.bytes >>> 29)),
          this.hash())
        }
      }),
      (X.prototype.hash = function () {
        let l
        let h
        let v
        let p
        let j
        let M
        const R = this.blocks;
        (this.first
          ? ((l = R[0] - 680876937),
            (l = (((l << 7) | (l >>> 25)) - 271733879) << 0),
            (p =
                              (-1732584194 ^ (l & 2004318071)) +
                              R[1] -
                              117830708),
            (p = (((p << 12) | (p >>> 20)) + l) << 0),
            (v =
                              (-271733879 ^ (p & (l ^ -271733879))) +
                              R[2] -
                              1126478375),
            (v = (((v << 17) | (v >>> 15)) + p) << 0),
            (h = (l ^ (v & (p ^ l))) + R[3] - 1316259209),
            (h = (((h << 22) | (h >>> 10)) + v) << 0))
          : ((l = this.h0),
            (h = this.h1),
            (v = this.h2),
            (p = this.h3),
            (l += (p ^ (h & (v ^ p))) + R[0] - 680876936),
            (l = (((l << 7) | (l >>> 25)) + h) << 0),
            (p += (v ^ (l & (h ^ v))) + R[1] - 389564586),
            (p = (((p << 12) | (p >>> 20)) + l) << 0),
            (v += (h ^ (p & (l ^ h))) + R[2] + 606105819),
            (v = (((v << 17) | (v >>> 15)) + p) << 0),
            (h += (l ^ (v & (p ^ l))) + R[3] - 1044525330),
            (h = (((h << 22) | (h >>> 10)) + v) << 0)),
        (l += (p ^ (h & (v ^ p))) + R[4] - 176418897),
        (l = (((l << 7) | (l >>> 25)) + h) << 0),
        (p += (v ^ (l & (h ^ v))) + R[5] + 1200080426),
        (p = (((p << 12) | (p >>> 20)) + l) << 0),
        (v += (h ^ (p & (l ^ h))) + R[6] - 1473231341),
        (v = (((v << 17) | (v >>> 15)) + p) << 0),
        (h += (l ^ (v & (p ^ l))) + R[7] - 45705983),
        (h = (((h << 22) | (h >>> 10)) + v) << 0),
        (l += (p ^ (h & (v ^ p))) + R[8] + 1770035416),
        (l = (((l << 7) | (l >>> 25)) + h) << 0),
        (p += (v ^ (l & (h ^ v))) + R[9] - 1958414417),
        (p = (((p << 12) | (p >>> 20)) + l) << 0),
        (v += (h ^ (p & (l ^ h))) + R[10] - 42063),
        (v = (((v << 17) | (v >>> 15)) + p) << 0),
        (h += (l ^ (v & (p ^ l))) + R[11] - 1990404162),
        (h = (((h << 22) | (h >>> 10)) + v) << 0),
        (l += (p ^ (h & (v ^ p))) + R[12] + 1804603682),
        (l = (((l << 7) | (l >>> 25)) + h) << 0),
        (p += (v ^ (l & (h ^ v))) + R[13] - 40341101),
        (p = (((p << 12) | (p >>> 20)) + l) << 0),
        (v += (h ^ (p & (l ^ h))) + R[14] - 1502002290),
        (v = (((v << 17) | (v >>> 15)) + p) << 0),
        (h += (l ^ (v & (p ^ l))) + R[15] + 1236535329),
        (h = (((h << 22) | (h >>> 10)) + v) << 0),
        (l += (v ^ (p & (h ^ v))) + R[1] - 165796510),
        (l = (((l << 5) | (l >>> 27)) + h) << 0),
        (p += (h ^ (v & (l ^ h))) + R[6] - 1069501632),
        (p = (((p << 9) | (p >>> 23)) + l) << 0),
        (v += (l ^ (h & (p ^ l))) + R[11] + 643717713),
        (v = (((v << 14) | (v >>> 18)) + p) << 0),
        (h += (p ^ (l & (v ^ p))) + R[0] - 373897302),
        (h = (((h << 20) | (h >>> 12)) + v) << 0),
        (l += (v ^ (p & (h ^ v))) + R[5] - 701558691),
        (l = (((l << 5) | (l >>> 27)) + h) << 0),
        (p += (h ^ (v & (l ^ h))) + R[10] + 38016083),
        (p = (((p << 9) | (p >>> 23)) + l) << 0),
        (v += (l ^ (h & (p ^ l))) + R[15] - 660478335),
        (v = (((v << 14) | (v >>> 18)) + p) << 0),
        (h += (p ^ (l & (v ^ p))) + R[4] - 405537848),
        (h = (((h << 20) | (h >>> 12)) + v) << 0),
        (l += (v ^ (p & (h ^ v))) + R[9] + 568446438),
        (l = (((l << 5) | (l >>> 27)) + h) << 0),
        (p += (h ^ (v & (l ^ h))) + R[14] - 1019803690),
        (p = (((p << 9) | (p >>> 23)) + l) << 0),
        (v += (l ^ (h & (p ^ l))) + R[3] - 187363961),
        (v = (((v << 14) | (v >>> 18)) + p) << 0),
        (h += (p ^ (l & (v ^ p))) + R[8] + 1163531501),
        (h = (((h << 20) | (h >>> 12)) + v) << 0),
        (l += (v ^ (p & (h ^ v))) + R[13] - 1444681467),
        (l = (((l << 5) | (l >>> 27)) + h) << 0),
        (p += (h ^ (v & (l ^ h))) + R[2] - 51403784),
        (p = (((p << 9) | (p >>> 23)) + l) << 0),
        (v += (l ^ (h & (p ^ l))) + R[7] + 1735328473),
        (v = (((v << 14) | (v >>> 18)) + p) << 0),
        (h += (p ^ (l & (v ^ p))) + R[12] - 1926607734),
        (h = (((h << 20) | (h >>> 12)) + v) << 0),
        (j = h ^ v),
        (l += (j ^ p) + R[5] - 378558),
        (l = (((l << 4) | (l >>> 28)) + h) << 0),
        (p += (j ^ l) + R[8] - 2022574463),
        (p = (((p << 11) | (p >>> 21)) + l) << 0),
        (M = p ^ l),
        (v += (M ^ h) + R[11] + 1839030562),
        (v = (((v << 16) | (v >>> 16)) + p) << 0),
        (h += (M ^ v) + R[14] - 35309556),
        (h = (((h << 23) | (h >>> 9)) + v) << 0),
        (j = h ^ v),
        (l += (j ^ p) + R[1] - 1530992060),
        (l = (((l << 4) | (l >>> 28)) + h) << 0),
        (p += (j ^ l) + R[4] + 1272893353),
        (p = (((p << 11) | (p >>> 21)) + l) << 0),
        (M = p ^ l),
        (v += (M ^ h) + R[7] - 155497632),
        (v = (((v << 16) | (v >>> 16)) + p) << 0),
        (h += (M ^ v) + R[10] - 1094730640),
        (h = (((h << 23) | (h >>> 9)) + v) << 0),
        (j = h ^ v),
        (l += (j ^ p) + R[13] + 681279174),
        (l = (((l << 4) | (l >>> 28)) + h) << 0),
        (p += (j ^ l) + R[0] - 358537222),
        (p = (((p << 11) | (p >>> 21)) + l) << 0),
        (M = p ^ l),
        (v += (M ^ h) + R[3] - 722521979),
        (v = (((v << 16) | (v >>> 16)) + p) << 0),
        (h += (M ^ v) + R[6] + 76029189),
        (h = (((h << 23) | (h >>> 9)) + v) << 0),
        (j = h ^ v),
        (l += (j ^ p) + R[9] - 640364487),
        (l = (((l << 4) | (l >>> 28)) + h) << 0),
        (p += (j ^ l) + R[12] - 421815835),
        (p = (((p << 11) | (p >>> 21)) + l) << 0),
        (M = p ^ l),
        (v += (M ^ h) + R[15] + 530742520),
        (v = (((v << 16) | (v >>> 16)) + p) << 0),
        (h += (M ^ v) + R[2] - 995338651),
        (h = (((h << 23) | (h >>> 9)) + v) << 0),
        (l += (v ^ (h | ~p)) + R[0] - 198630844),
        (l = (((l << 6) | (l >>> 26)) + h) << 0),
        (p += (h ^ (l | ~v)) + R[7] + 1126891415),
        (p = (((p << 10) | (p >>> 22)) + l) << 0),
        (v += (l ^ (p | ~h)) + R[14] - 1416354905),
        (v = (((v << 15) | (v >>> 17)) + p) << 0),
        (h += (p ^ (v | ~l)) + R[5] - 57434055),
        (h = (((h << 21) | (h >>> 11)) + v) << 0),
        (l += (v ^ (h | ~p)) + R[12] + 1700485571),
        (l = (((l << 6) | (l >>> 26)) + h) << 0),
        (p += (h ^ (l | ~v)) + R[3] - 1894986606),
        (p = (((p << 10) | (p >>> 22)) + l) << 0),
        (v += (l ^ (p | ~h)) + R[10] - 1051523),
        (v = (((v << 15) | (v >>> 17)) + p) << 0),
        (h += (p ^ (v | ~l)) + R[1] - 2054922799),
        (h = (((h << 21) | (h >>> 11)) + v) << 0),
        (l += (v ^ (h | ~p)) + R[8] + 1873313359),
        (l = (((l << 6) | (l >>> 26)) + h) << 0),
        (p += (h ^ (l | ~v)) + R[15] - 30611744),
        (p = (((p << 10) | (p >>> 22)) + l) << 0),
        (v += (l ^ (p | ~h)) + R[6] - 1560198380),
        (v = (((v << 15) | (v >>> 17)) + p) << 0),
        (h += (p ^ (v | ~l)) + R[13] + 1309151649),
        (h = (((h << 21) | (h >>> 11)) + v) << 0),
        (l += (v ^ (h | ~p)) + R[4] - 145523070),
        (l = (((l << 6) | (l >>> 26)) + h) << 0),
        (p += (h ^ (l | ~v)) + R[11] - 1120210379),
        (p = (((p << 10) | (p >>> 22)) + l) << 0),
        (v += (l ^ (p | ~h)) + R[2] + 718787259),
        (v = (((v << 15) | (v >>> 17)) + p) << 0),
        (h += (p ^ (v | ~l)) + R[9] - 343485551),
        (h = (((h << 21) | (h >>> 11)) + v) << 0),
        this.first
          ? ((this.h0 = (l + 1732584193) << 0),
            (this.h1 = (h - 271733879) << 0),
            (this.h2 = (v - 1732584194) << 0),
            (this.h3 = (p + 271733878) << 0),
            (this.first = !1))
          : ((this.h0 = (this.h0 + l) << 0),
            (this.h1 = (this.h1 + h) << 0),
            (this.h2 = (this.h2 + v) << 0),
            (this.h3 = (this.h3 + p) << 0)))
      }),
      (X.prototype.hex = function () {
        this.finalize()
        const l = this.h0
        const h = this.h1
        const v = this.h2
        const p = this.h3
        return (
          d[(l >>> 4) & 15] +
                        d[l & 15] +
                        d[(l >>> 12) & 15] +
                        d[(l >>> 8) & 15] +
                        d[(l >>> 20) & 15] +
                        d[(l >>> 16) & 15] +
                        d[(l >>> 28) & 15] +
                        d[(l >>> 24) & 15] +
                        d[(h >>> 4) & 15] +
                        d[h & 15] +
                        d[(h >>> 12) & 15] +
                        d[(h >>> 8) & 15] +
                        d[(h >>> 20) & 15] +
                        d[(h >>> 16) & 15] +
                        d[(h >>> 28) & 15] +
                        d[(h >>> 24) & 15] +
                        d[(v >>> 4) & 15] +
                        d[v & 15] +
                        d[(v >>> 12) & 15] +
                        d[(v >>> 8) & 15] +
                        d[(v >>> 20) & 15] +
                        d[(v >>> 16) & 15] +
                        d[(v >>> 28) & 15] +
                        d[(v >>> 24) & 15] +
                        d[(p >>> 4) & 15] +
                        d[p & 15] +
                        d[(p >>> 12) & 15] +
                        d[(p >>> 8) & 15] +
                        d[(p >>> 20) & 15] +
                        d[(p >>> 16) & 15] +
                        d[(p >>> 28) & 15] +
                        d[(p >>> 24) & 15]
        )
      }),
      (X.prototype.toString = X.prototype.hex),
      (X.prototype.digest = function () {
        this.finalize()
        const l = this.h0
        const h = this.h1
        const v = this.h2
        const p = this.h3
        return [
          l & 255,
          (l >>> 8) & 255,
          (l >>> 16) & 255,
          (l >>> 24) & 255,
          h & 255,
          (h >>> 8) & 255,
          (h >>> 16) & 255,
          (h >>> 24) & 255,
          v & 255,
          (v >>> 8) & 255,
          (v >>> 16) & 255,
          (v >>> 24) & 255,
          p & 255,
          (p >>> 8) & 255,
          (p >>> 16) & 255,
          (p >>> 24) & 255
        ]
      }),
      (X.prototype.array = X.prototype.digest),
      (X.prototype.arrayBuffer = function () {
        this.finalize()
        const l = new ArrayBuffer(16)
        const h = new Uint32Array(l)
        return (
          (h[0] = this.h0),
          (h[1] = this.h1),
          (h[2] = this.h2),
          (h[3] = this.h3),
          l
        )
      }),
      (X.prototype.buffer = X.prototype.arrayBuffer),
      (X.prototype.base64 = function () {
        for (
          var l, h, v, p = '', j = this.array(), M = 0;
          M < 15;

        ) {
          ((l = j[M++]),
          (h = j[M++]),
          (v = j[M++]),
          (p +=
                                E[l >>> 2] +
                                E[((l << 4) | (h >>> 4)) & 63] +
                                E[((h << 2) | (v >>> 6)) & 63] +
                                E[v & 63]))
        }
        return (
          (l = j[M]),
          (p += E[l >>> 2] + E[(l << 4) & 63] + '=='),
          p
        )
      }))
      function Q (l, h) {
        let v
        const p = N(l)
        if (((l = p[0]), p[1])) {
          const j = []
          const M = l.length
          let R = 0
          let Z
          for (v = 0; v < M; ++v) {
            ((Z = l.charCodeAt(v)),
            Z < 128
              ? (j[R++] = Z)
              : Z < 2048
                ? ((j[R++] = 192 | (Z >>> 6)),
                  (j[R++] = 128 | (Z & 63)))
                : Z < 55296 || Z >= 57344
                  ? ((j[R++] = 224 | (Z >>> 12)),
                    (j[R++] = 128 | ((Z >>> 6) & 63)),
                    (j[R++] = 128 | (Z & 63)))
                  : ((Z =
                                          65536 +
                                          (((Z & 1023) << 10) |
                                              (l.charCodeAt(++v) & 1023))),
                    (j[R++] = 240 | (Z >>> 18)),
                    (j[R++] = 128 | ((Z >>> 12) & 63)),
                    (j[R++] = 128 | ((Z >>> 6) & 63)),
                    (j[R++] = 128 | (Z & 63))))
          }
          l = j
        }
        l.length > 64 && (l = new X(!0).update(l).array())
        const ze = []
        const Rt = []
        for (v = 0; v < 64; ++v) {
          const Ut = l[v] || 0;
          ((ze[v] = 92 ^ Ut), (Rt[v] = 54 ^ Ut))
        }
        (X.call(this, h),
        this.update(Rt),
        (this.oKeyPad = ze),
        (this.inner = !0),
        (this.sharedMemory = h))
      }
      ((Q.prototype = new X()),
      (Q.prototype.finalize = function () {
        if ((X.prototype.finalize.call(this), this.inner)) {
          this.inner = !1
          const l = this.array();
          (X.call(this, this.sharedMemory),
          this.update(this.oKeyPad),
          this.update(l),
          X.prototype.finalize.call(this))
        }
      }))
      const me = ne();
      ((me.md5 = me),
      (me.md5.hmac = de()),
      a
        ? (yr.exports = me)
        : ((n.md5 = me),
          c &&
                          define(function () {
                            return me
                          })))
    })()
  })
  const Hi = ['top', 'right', 'bottom', 'left']
  const Pi = ['start', 'end']
  const Mi = Hi.reduce(
    (e, t) => e.concat(t, t + '-' + Pi[0], t + '-' + Pi[1]),
    []
  )
  const Et = Math.min
  const tt = Math.max
  const hr = Math.round
  const pr = Math.floor
  const nn = (e) => ({ x: e, y: e })
  const ta = { left: 'right', right: 'left', bottom: 'top', top: 'bottom' }
  const na = { start: 'end', end: 'start' }
  function Jr (e, t, r) {
    return tt(e, Et(t, r))
  }
  function jt (e, t) {
    return typeof e === 'function' ? e(t) : e
  }
  function pt (e) {
    return e.split('-')[0]
  }
  function xt (e) {
    return e.split('-')[1]
  }
  function $i (e) {
    return e === 'x' ? 'y' : 'x'
  }
  function Qr (e) {
    return e === 'y' ? 'height' : 'width'
  }
  function Pn (e) {
    return ['top', 'bottom'].includes(pt(e)) ? 'y' : 'x'
  }
  function Zr (e) {
    return $i(Pn(e))
  }
  function Wi (e, t, r) {
    r === void 0 && (r = !1)
    const n = xt(e)
    const i = Zr(e)
    const o = Qr(i)
    let a =
            i === 'x'
              ? n === (r ? 'end' : 'start')
                ? 'right'
                : 'left'
              : n === 'start'
                ? 'bottom'
                : 'top'
    return (t.reference[o] > t.floating[o] && (a = mr(a)), [a, mr(a)])
  }
  function ra (e) {
    const t = mr(e)
    return [vr(e), t, vr(t)]
  }
  function vr (e) {
    return e.replace(/start|end/g, (t) => na[t])
  }
  function ia (e, t, r) {
    const n = ['left', 'right']
    const i = ['right', 'left']
    const o = ['top', 'bottom']
    const a = ['bottom', 'top']
    switch (e) {
      case 'top':
      case 'bottom':
        return r ? (t ? i : n) : t ? n : i
      case 'left':
      case 'right':
        return t ? o : a
      default:
        return []
    }
  }
  function oa (e, t, r, n) {
    const i = xt(e)
    let o = ia(pt(e), r === 'start', n)
    return (
      i &&
                ((o = o.map((a) => a + '-' + i)),
                t && (o = o.concat(o.map(vr)))),
      o
    )
  }
  function mr (e) {
    return e.replace(/left|right|bottom|top/g, (t) => ta[t])
  }
  function aa (e) {
    return { top: 0, right: 0, bottom: 0, left: 0, ...e }
  }
  function ei (e) {
    return typeof e !== 'number'
      ? aa(e)
      : { top: e, right: e, bottom: e, left: e }
  }
  function Dn (e) {
    return {
      ...e,
      top: e.y,
      left: e.x,
      right: e.x + e.width,
      bottom: e.y + e.height
    }
  }
  function Ri (e, t, r) {
    const { reference: n, floating: i } = e
    const o = Pn(t)
    const a = Zr(t)
    const c = Qr(a)
    const f = pt(t)
    const d = o === 'y'
    const y = n.x + n.width / 2 - i.width / 2
    const m = n.y + n.height / 2 - i.height / 2
    const O = n[c] / 2 - i[c] / 2
    let E
    switch (f) {
      case 'top':
        E = { x: y, y: n.y - i.height }
        break
      case 'bottom':
        E = { x: y, y: n.y + n.height }
        break
      case 'right':
        E = { x: n.x + n.width, y: m }
        break
      case 'left':
        E = { x: n.x - i.width, y: m }
        break
      default:
        E = { x: n.x, y: n.y }
    }
    switch (xt(t)) {
      case 'start':
        E[a] -= O * (r && d ? -1 : 1)
        break
      case 'end':
        E[a] += O * (r && d ? -1 : 1)
        break
    }
    return E
  }
  const sa = async (e, t, r) => {
    const {
      placement: n = 'bottom',
      strategy: i = 'absolute',
      middleware: o = [],
      platform: a
    } = r
    const c = o.filter(Boolean)
    const f = await (a.isRTL == null ? void 0 : a.isRTL(t))
    let d = await a.getElementRects({
      reference: e,
      floating: t,
      strategy: i
    })
    let { x: y, y: m } = Ri(d, n, f)
    let O = n
    let E = {}
    let S = 0
    for (let _ = 0; _ < c.length; _++) {
      const { name: I, fn: $ } = c[_]
      const {
        x: A,
        y: N,
        data: Y,
        reset: ne
      } = await $({
        x: y,
        y: m,
        initialPlacement: n,
        placement: O,
        strategy: i,
        middlewareData: E,
        rects: d,
        platform: a,
        elements: { reference: e, floating: t }
      });
      ((y = A ?? y),
      (m = N ?? m),
      (E = { ...E, [I]: { ...E[I], ...Y } }),
      ne &&
                    S <= 50 &&
                    (S++,
                    typeof ne === 'object' &&
                        (ne.placement && (O = ne.placement),
                        ne.rects &&
                            (d =
                                ne.rects === !0
                                  ? await a.getElementRects({
                                    reference: e,
                                    floating: t,
                                    strategy: i
                                  })
                                  : ne.rects),
                        ({ x: y, y: m } = Ri(d, O, f))),
                    (_ = -1)))
    }
    return { x: y, y: m, placement: O, strategy: i, middlewareData: E }
  }
  async function _n (e, t) {
    let r
    t === void 0 && (t = {})
    const {
      x: n,
      y: i,
      platform: o,
      rects: a,
      elements: c,
      strategy: f
    } = e
    const {
      boundary: d = 'clippingAncestors',
      rootBoundary: y = 'viewport',
      elementContext: m = 'floating',
      altBoundary: O = !1,
      padding: E = 0
    } = jt(t, e)
    const S = ei(E)
    const I = c[O ? (m === 'floating' ? 'reference' : 'floating') : m]
    const $ = Dn(
      await o.getClippingRect({
        element:
                    (r = await (o.isElement == null
                      ? void 0
                      : o.isElement(I))) == null || r
                      ? I
                      : I.contextElement ||
                          (await (o.getDocumentElement == null
                            ? void 0
                            : o.getDocumentElement(c.floating))),
        boundary: d,
        rootBoundary: y,
        strategy: f
      })
    )
    const A =
            m === 'floating' ? { ...a.floating, x: n, y: i } : a.reference
    const N = await (o.getOffsetParent == null
      ? void 0
      : o.getOffsetParent(c.floating))
    const Y = (await (o.isElement == null ? void 0 : o.isElement(N)))
      ? (await (o.getScale == null ? void 0 : o.getScale(N))) || {
          x: 1,
          y: 1
        }
      : { x: 1, y: 1 }
    const ne = Dn(
      o.convertOffsetParentRelativeRectToViewportRelativeRect
        ? await o.convertOffsetParentRelativeRectToViewportRelativeRect(
          {
            elements: c,
            rect: A,
            offsetParent: N,
            strategy: f
          }
        )
        : A
    )
    return {
      top: ($.top - ne.top + S.top) / Y.y,
      bottom: (ne.bottom - $.bottom + S.bottom) / Y.y,
      left: ($.left - ne.left + S.left) / Y.x,
      right: (ne.right - $.right + S.right) / Y.x
    }
  }
  const la = (e) => ({
    name: 'arrow',
    options: e,
    async fn (t) {
      const {
        x: r,
        y: n,
        placement: i,
        rects: o,
        platform: a,
        elements: c,
        middlewareData: f
      } = t
      const { element: d, padding: y = 0 } = jt(e, t) || {}
      if (d == null) return {}
      const m = ei(y)
      const O = { x: r, y: n }
      const E = Zr(i)
      const S = Qr(E)
      const _ = await a.getDimensions(d)
      const I = E === 'y'
      const $ = I ? 'top' : 'left'
      const A = I ? 'bottom' : 'right'
      const N = I ? 'clientHeight' : 'clientWidth'
      const Y = o.reference[S] + o.reference[E] - O[E] - o.floating[S]
      const ne = O[E] - o.reference[E]
      const J = await (a.getOffsetParent == null
        ? void 0
        : a.getOffsetParent(d))
      let V = J ? J[N] : 0;
      (!V || !(await (a.isElement == null ? void 0 : a.isElement(J)))) &&
                (V = c.floating[N] || o.floating[S])
      const de = Y / 2 - ne / 2
      const X = V / 2 - _[S] / 2 - 1
      const Q = Et(m[$], X)
      const me = Et(m[A], X)
      const l = Q
      const h = V - _[S] - me
      const v = V / 2 - _[S] / 2 + de
      const p = Jr(l, v, h)
      const j =
                !f.arrow &&
                xt(i) != null &&
                v !== p &&
                o.reference[S] / 2 - (v < l ? Q : me) - _[S] / 2 < 0
      const M = j ? (v < l ? v - l : v - h) : 0
      return {
        [E]: O[E] + M,
        data: {
          [E]: p,
          centerOffset: v - p - M,
          ...(j && { alignmentOffset: M })
        },
        reset: j
      }
    }
  })
  function ca (e, t, r) {
    return (
      e
        ? [
            ...r.filter((i) => xt(i) === e),
            ...r.filter((i) => xt(i) !== e)
          ]
        : r.filter((i) => pt(i) === i)
    ).filter((i) => (e ? xt(i) === e || (t ? vr(i) !== i : !1) : !0))
  }
  const fa = function (e) {
    return (
      e === void 0 && (e = {}),
      {
        name: 'autoPlacement',
        options: e,
        async fn (t) {
          let r, n, i
          const {
            rects: o,
            middlewareData: a,
            placement: c,
            platform: f,
            elements: d
          } = t
          const {
            crossAxis: y = !1,
            alignment: m,
            allowedPlacements: O = Mi,
            autoAlignment: E = !0,
            ...S
          } = jt(e, t)
          const _ =
                        m !== void 0 || O === Mi ? ca(m || null, E, O) : O
          const I = await _n(t, S)
          const $ =
                        ((r = a.autoPlacement) == null ? void 0 : r.index) || 0
          const A = _[$]
          if (A == null) return {}
          const N = Wi(
            A,
            o,
            await (f.isRTL == null ? void 0 : f.isRTL(d.floating))
          )
          if (c !== A) return { reset: { placement: _[0] } }
          const Y = [I[pt(A)], I[N[0]], I[N[1]]]
          const ne = [
            ...(((n = a.autoPlacement) == null
              ? void 0
              : n.overflows) || []),
            { placement: A, overflows: Y }
          ]
          const J = _[$ + 1]
          if (J) {
            return {
              data: { index: $ + 1, overflows: ne },
              reset: { placement: J }
            }
          }
          const V = ne
            .map((Q) => {
              const me = xt(Q.placement)
              return [
                Q.placement,
                me && y
                  ? Q.overflows
                    .slice(0, 2)
                    .reduce((l, h) => l + h, 0)
                  : Q.overflows[0],
                Q.overflows
              ]
            })
            .sort((Q, me) => Q[1] - me[1])
          const X =
                        ((i = V.filter((Q) =>
                          Q[2]
                            .slice(0, xt(Q[0]) ? 2 : 3)
                            .every((me) => me <= 0)
                        )[0]) == null
                          ? void 0
                          : i[0]) || V[0][0]
          return X !== c
            ? {
                data: { index: $ + 1, overflows: ne },
                reset: { placement: X }
              }
            : {}
        }
      }
    )
  }
  const ua = function (e) {
    return (
      e === void 0 && (e = {}),
      {
        name: 'flip',
        options: e,
        async fn (t) {
          let r, n
          const {
            placement: i,
            middlewareData: o,
            rects: a,
            initialPlacement: c,
            platform: f,
            elements: d
          } = t
          const {
            mainAxis: y = !0,
            crossAxis: m = !0,
            fallbackPlacements: O,
            fallbackStrategy: E = 'bestFit',
            fallbackAxisSideDirection: S = 'none',
            flipAlignment: _ = !0,
            ...I
          } = jt(e, t)
          if ((r = o.arrow) != null && r.alignmentOffset) {
            return {}
          }
          const $ = pt(i)
          const A = pt(c) === c
          const N = await (f.isRTL == null
            ? void 0
            : f.isRTL(d.floating))
          const Y = O || (A || !_ ? [mr(c)] : ra(c))
          !O && S !== 'none' && Y.push(...oa(c, _, S, N))
          const ne = [c, ...Y]
          const J = await _n(t, I)
          const V = []
          let de =
                        ((n = o.flip) == null ? void 0 : n.overflows) || []
          if ((y && V.push(J[$]), m)) {
            const l = Wi(i, a, N)
            V.push(J[l[0]], J[l[1]])
          }
          if (
            ((de = [...de, { placement: i, overflows: V }]),
            !V.every((l) => l <= 0))
          ) {
            let X, Q
            const l =
                            (((X = o.flip) == null ? void 0 : X.index) || 0) +
                            1
            const h = ne[l]
            if (h) {
              return {
                data: { index: l, overflows: de },
                reset: { placement: h }
              }
            }
            let v =
                            (Q = de
                              .filter((p) => p.overflows[0] <= 0)
                              .sort(
                                (p, j) => p.overflows[1] - j.overflows[1]
                              )[0]) == null
                              ? void 0
                              : Q.placement
            if (!v) {
              switch (E) {
                case 'bestFit': {
                  let me
                  const p =
                                        (me = de
                                          .map((j) => [
                                            j.placement,
                                            j.overflows
                                              .filter((M) => M > 0)
                                              .reduce((M, R) => M + R, 0)
                                          ])
                                          .sort((j, M) => j[1] - M[1])[0]) ==
                                        null
                                          ? void 0
                                          : me[0]
                  p && (v = p)
                  break
                }
                case 'initialPlacement':
                  v = c
                  break
              }
            }
            if (i !== v) return { reset: { placement: v } }
          }
          return {}
        }
      }
    )
  }
  function Ii (e, t) {
    return {
      top: e.top - t.height,
      right: e.right - t.width,
      bottom: e.bottom - t.height,
      left: e.left - t.width
    }
  }
  function Li (e) {
    return Hi.some((t) => e[t] >= 0)
  }
  const da = function (e) {
    return (
      e === void 0 && (e = {}),
      {
        name: 'hide',
        options: e,
        async fn (t) {
          const { rects: r } = t
          const { strategy: n = 'referenceHidden', ...i } = jt(e, t)
          switch (n) {
            case 'referenceHidden': {
              const o = await _n(t, {
                ...i,
                elementContext: 'reference'
              })
              const a = Ii(o, r.reference)
              return {
                data: {
                  referenceHiddenOffsets: a,
                  referenceHidden: Li(a)
                }
              }
            }
            case 'escaped': {
              const o = await _n(t, { ...i, altBoundary: !0 })
              const a = Ii(o, r.floating)
              return {
                data: { escapedOffsets: a, escaped: Li(a) }
              }
            }
            default:
              return {}
          }
        }
      }
    )
  }
  function Ui (e) {
    const t = Et(...e.map((o) => o.left))
    const r = Et(...e.map((o) => o.top))
    const n = tt(...e.map((o) => o.right))
    const i = tt(...e.map((o) => o.bottom))
    return { x: t, y: r, width: n - t, height: i - r }
  }
  function pa (e) {
    const t = e.slice().sort((i, o) => i.y - o.y)
    const r = []
    let n = null
    for (let i = 0; i < t.length; i++) {
      const o = t[i];
      (!n || o.y - n.y > n.height / 2
        ? r.push([o])
        : r[r.length - 1].push(o),
      (n = o))
    }
    return r.map((i) => Dn(Ui(i)))
  }
  const ha = function (e) {
    return (
      e === void 0 && (e = {}),
      {
        name: 'inline',
        options: e,
        async fn (t) {
          const {
            placement: r,
            elements: n,
            rects: i,
            platform: o,
            strategy: a
          } = t
          const { padding: c = 2, x: f, y: d } = jt(e, t)
          const y = Array.from(
            (await (o.getClientRects == null
              ? void 0
              : o.getClientRects(n.reference))) || []
          )
          const m = pa(y)
          const O = Dn(Ui(y))
          const E = ei(c)
          function S () {
            if (
              m.length === 2 &&
                            m[0].left > m[1].right &&
                            f != null &&
                            d != null
            ) {
              return (
                m.find(
                  (I) =>
                    f > I.left - E.left &&
                                        f < I.right + E.right &&
                                        d > I.top - E.top &&
                                        d < I.bottom + E.bottom
                ) || O
              )
            }
            if (m.length >= 2) {
              if (Pn(r) === 'y') {
                const Q = m[0]
                const me = m[m.length - 1]
                const l = pt(r) === 'top'
                const h = Q.top
                const v = me.bottom
                const p = l ? Q.left : me.left
                const j = l ? Q.right : me.right
                const M = j - p
                const R = v - h
                return {
                  top: h,
                  bottom: v,
                  left: p,
                  right: j,
                  width: M,
                  height: R,
                  x: p,
                  y: h
                }
              }
              const I = pt(r) === 'left'
              const $ = tt(...m.map((Q) => Q.right))
              const A = Et(...m.map((Q) => Q.left))
              const N = m.filter((Q) =>
                I ? Q.left === A : Q.right === $
              )
              const Y = N[0].top
              const ne = N[N.length - 1].bottom
              const J = A
              const V = $
              const de = V - J
              const X = ne - Y
              return {
                top: Y,
                bottom: ne,
                left: J,
                right: V,
                width: de,
                height: X,
                x: J,
                y: Y
              }
            }
            return O
          }
          const _ = await o.getElementRects({
            reference: { getBoundingClientRect: S },
            floating: n.floating,
            strategy: a
          })
          return i.reference.x !== _.reference.x ||
                        i.reference.y !== _.reference.y ||
                        i.reference.width !== _.reference.width ||
                        i.reference.height !== _.reference.height
            ? { reset: { rects: _ } }
            : {}
        }
      }
    )
  }
  async function va (e, t) {
    const { placement: r, platform: n, elements: i } = e
    const o = await (n.isRTL == null ? void 0 : n.isRTL(i.floating))
    const a = pt(r)
    const c = xt(r)
    const f = Pn(r) === 'y'
    const d = ['left', 'top'].includes(a) ? -1 : 1
    const y = o && f ? -1 : 1
    const m = jt(t, e)
    let {
      mainAxis: O,
      crossAxis: E,
      alignmentAxis: S
    } = typeof m === 'number'
      ? { mainAxis: m, crossAxis: 0, alignmentAxis: null }
      : { mainAxis: 0, crossAxis: 0, alignmentAxis: null, ...m }
    return (
      c && typeof S === 'number' && (E = c === 'end' ? S * -1 : S),
      f ? { x: E * y, y: O * d } : { x: O * d, y: E * y }
    )
  }
  const Vi = function (e) {
    return (
      e === void 0 && (e = 0),
      {
        name: 'offset',
        options: e,
        async fn (t) {
          let r, n
          const { x: i, y: o, placement: a, middlewareData: c } = t
          const f = await va(t, e)
          return a ===
                        ((r = c.offset) == null ? void 0 : r.placement) &&
                        (n = c.arrow) != null &&
                        n.alignmentOffset
            ? {}
            : {
                x: i + f.x,
                y: o + f.y,
                data: { ...f, placement: a }
              }
        }
      }
    )
  }
  const ma = function (e) {
    return (
      e === void 0 && (e = {}),
      {
        name: 'shift',
        options: e,
        async fn (t) {
          const { x: r, y: n, placement: i } = t
          const {
            mainAxis: o = !0,
            crossAxis: a = !1,
            limiter: c = {
              fn: (I) => {
                const { x: $, y: A } = I
                return { x: $, y: A }
              }
            },
            ...f
          } = jt(e, t)
          const d = { x: r, y: n }
          const y = await _n(t, f)
          const m = Pn(pt(i))
          const O = $i(m)
          let E = d[O]
          let S = d[m]
          if (o) {
            const I = O === 'y' ? 'top' : 'left'
            const $ = O === 'y' ? 'bottom' : 'right'
            const A = E + y[I]
            const N = E - y[$]
            E = Jr(A, E, N)
          }
          if (a) {
            const I = m === 'y' ? 'top' : 'left'
            const $ = m === 'y' ? 'bottom' : 'right'
            const A = S + y[I]
            const N = S - y[$]
            S = Jr(A, S, N)
          }
          const _ = c.fn({ ...t, [O]: E, [m]: S })
          return { ..._, data: { x: _.x - r, y: _.y - n } }
        }
      }
    )
  }
  const ga = function (e) {
    return (
      e === void 0 && (e = {}),
      {
        name: 'size',
        options: e,
        async fn (t) {
          const {
            placement: r,
            rects: n,
            platform: i,
            elements: o
          } = t
          const { apply: a = () => {}, ...c } = jt(e, t)
          const f = await _n(t, c)
          const d = pt(r)
          const y = xt(r)
          const m = Pn(r) === 'y'
          const { width: O, height: E } = n.floating
          let S
          let _
          d === 'top' || d === 'bottom'
            ? ((S = d),
              (_ =
                              y ===
                              ((await (i.isRTL == null
                                ? void 0
                                : i.isRTL(o.floating)))
                                ? 'start'
                                : 'end')
                                ? 'left'
                                : 'right'))
            : ((_ = d), (S = y === 'end' ? 'top' : 'bottom'))
          const I = E - f[S]
          const $ = O - f[_]
          const A = !t.middlewareData.shift
          let N = I
          let Y = $
          if (m) {
            const J = O - f.left - f.right
            Y = y || A ? Et($, J) : J
          } else {
            const J = E - f.top - f.bottom
            N = y || A ? Et(I, J) : J
          }
          if (A && !y) {
            const J = tt(f.left, 0)
            const V = tt(f.right, 0)
            const de = tt(f.top, 0)
            const X = tt(f.bottom, 0)
            m
              ? (Y =
                                  O -
                                  2 *
                                      (J !== 0 || V !== 0
                                        ? J + V
                                        : tt(f.left, f.right)))
              : (N =
                                  E -
                                  2 *
                                      (de !== 0 || X !== 0
                                        ? de + X
                                        : tt(f.top, f.bottom)))
          }
          await a({
            ...t,
            availableWidth: Y,
            availableHeight: N
          })
          const ne = await i.getDimensions(o.floating)
          return O !== ne.width || E !== ne.height
            ? { reset: { rects: !0 } }
            : {}
        }
      }
    )
  }
  function rn (e) {
    return zi(e) ? (e.nodeName || '').toLowerCase() : '#document'
  }
  function ct (e) {
    let t
    return (
      (e == null || (t = e.ownerDocument) == null
        ? void 0
        : t.defaultView) || window
    )
  }
  function Bt (e) {
    let t
    return (t =
            (zi(e) ? e.ownerDocument : e.document) || window.document) == null
      ? void 0
      : t.documentElement
  }
  function zi (e) {
    return e instanceof Node || e instanceof ct(e).Node
  }
  function Nt (e) {
    return e instanceof Element || e instanceof ct(e).Element
  }
  function Tt (e) {
    return e instanceof HTMLElement || e instanceof ct(e).HTMLElement
  }
  function Fi (e) {
    return typeof ShadowRoot > 'u'
      ? !1
      : e instanceof ShadowRoot || e instanceof ct(e).ShadowRoot
  }
  function zn (e) {
    const { overflow: t, overflowX: r, overflowY: n, display: i } = ht(e)
    return (
      /auto|scroll|overlay|hidden|clip/.test(t + n + r) &&
            !['inline', 'contents'].includes(i)
    )
  }
  function ba (e) {
    return ['table', 'td', 'th'].includes(rn(e))
  }
  function ti (e) {
    const t = ni()
    const r = ht(e)
    return (
      r.transform !== 'none' ||
            r.perspective !== 'none' ||
            (r.containerType ? r.containerType !== 'normal' : !1) ||
            (!t && (r.backdropFilter ? r.backdropFilter !== 'none' : !1)) ||
            (!t && (r.filter ? r.filter !== 'none' : !1)) ||
            ['transform', 'perspective', 'filter'].some((n) =>
              (r.willChange || '').includes(n)
            ) ||
            ['paint', 'layout', 'strict', 'content'].some((n) =>
              (r.contain || '').includes(n)
            )
    )
  }
  function ya (e) {
    let t = Tn(e)
    for (; Tt(t) && !gr(t);) {
      if (ti(t)) return t
      t = Tn(t)
    }
    return null
  }
  function ni () {
    return typeof CSS > 'u' || !CSS.supports
      ? !1
      : CSS.supports('-webkit-backdrop-filter', 'none')
  }
  function gr (e) {
    return ['html', 'body', '#document'].includes(rn(e))
  }
  function ht (e) {
    return ct(e).getComputedStyle(e)
  }
  function br (e) {
    return Nt(e)
      ? { scrollLeft: e.scrollLeft, scrollTop: e.scrollTop }
      : { scrollLeft: e.pageXOffset, scrollTop: e.pageYOffset }
  }
  function Tn (e) {
    if (rn(e) === 'html') return e
    const t = e.assignedSlot || e.parentNode || (Fi(e) && e.host) || Bt(e)
    return Fi(t) ? t.host : t
  }
  function Yi (e) {
    const t = Tn(e)
    return gr(t)
      ? e.ownerDocument
        ? e.ownerDocument.body
        : e.body
      : Tt(t) && zn(t)
        ? t
        : Yi(t)
  }
  function Vn (e, t, r) {
    let n;
    (t === void 0 && (t = []), r === void 0 && (r = !0))
    const i = Yi(e)
    const o = i === ((n = e.ownerDocument) == null ? void 0 : n.body)
    const a = ct(i)
    return o
      ? t.concat(
        a,
        a.visualViewport || [],
        zn(i) ? i : [],
        a.frameElement && r ? Vn(a.frameElement) : []
      )
      : t.concat(i, Vn(i, [], r))
  }
  function Xi (e) {
    const t = ht(e)
    let r = parseFloat(t.width) || 0
    let n = parseFloat(t.height) || 0
    const i = Tt(e)
    const o = i ? e.offsetWidth : r
    const a = i ? e.offsetHeight : n
    const c = hr(r) !== o || hr(n) !== a
    return (c && ((r = o), (n = a)), { width: r, height: n, $: c })
  }
  function ri (e) {
    return Nt(e) ? e : e.contextElement
  }
  function Cn (e) {
    const t = ri(e)
    if (!Tt(t)) return nn(1)
    const r = t.getBoundingClientRect()
    const { width: n, height: i, $: o } = Xi(t)
    let a = (o ? hr(r.width) : r.width) / n
    let c = (o ? hr(r.height) : r.height) / i
    return (
      (!a || !Number.isFinite(a)) && (a = 1),
      (!c || !Number.isFinite(c)) && (c = 1),
      { x: a, y: c }
    )
  }
  const wa = nn(0)
  function qi (e) {
    const t = ct(e)
    return !ni() || !t.visualViewport
      ? wa
      : { x: t.visualViewport.offsetLeft, y: t.visualViewport.offsetTop }
  }
  function xa (e, t, r) {
    return (t === void 0 && (t = !1), !r || (t && r !== ct(e)) ? !1 : t)
  }
  function vn (e, t, r, n) {
    (t === void 0 && (t = !1), r === void 0 && (r = !1))
    const i = e.getBoundingClientRect()
    const o = ri(e)
    let a = nn(1)
    t && (n ? Nt(n) && (a = Cn(n)) : (a = Cn(e)))
    const c = xa(o, r, n) ? qi(o) : nn(0)
    let f = (i.left + c.x) / a.x
    let d = (i.top + c.y) / a.y
    let y = i.width / a.x
    let m = i.height / a.y
    if (o) {
      const O = ct(o)
      const E = n && Nt(n) ? ct(n) : n
      let S = O
      let _ = S.frameElement
      for (; _ && n && E !== S;) {
        const I = Cn(_)
        const $ = _.getBoundingClientRect()
        const A = ht(_)
        const N =
                    $.left + (_.clientLeft + parseFloat(A.paddingLeft)) * I.x
        const Y =
                    $.top + (_.clientTop + parseFloat(A.paddingTop)) * I.y;
        ((f *= I.x),
        (d *= I.y),
        (y *= I.x),
        (m *= I.y),
        (f += N),
        (d += Y),
        (S = ct(_)),
        (_ = S.frameElement))
      }
    }
    return Dn({ width: y, height: m, x: f, y: d })
  }
  const Ea = [':popover-open', ':modal']
  function Gi (e) {
    return Ea.some((t) => {
      try {
        return e.matches(t)
      } catch {
        return !1
      }
    })
  }
  function Oa (e) {
    const { elements: t, rect: r, offsetParent: n, strategy: i } = e
    const o = i === 'fixed'
    const a = Bt(n)
    const c = t ? Gi(t.floating) : !1
    if (n === a || (c && o)) return r
    let f = { scrollLeft: 0, scrollTop: 0 }
    let d = nn(1)
    const y = nn(0)
    const m = Tt(n)
    if (
      (m || (!m && !o)) &&
            ((rn(n) !== 'body' || zn(a)) && (f = br(n)), Tt(n))
    ) {
      const O = vn(n);
      ((d = Cn(n)),
      (y.x = O.x + n.clientLeft),
      (y.y = O.y + n.clientTop))
    }
    return {
      width: r.width * d.x,
      height: r.height * d.y,
      x: r.x * d.x - f.scrollLeft * d.x + y.x,
      y: r.y * d.y - f.scrollTop * d.y + y.y
    }
  }
  function Sa (e) {
    return Array.from(e.getClientRects())
  }
  function Ki (e) {
    return vn(Bt(e)).left + br(e).scrollLeft
  }
  function Aa (e) {
    const t = Bt(e)
    const r = br(e)
    const n = e.ownerDocument.body
    const i = tt(
      t.scrollWidth,
      t.clientWidth,
      n.scrollWidth,
      n.clientWidth
    )
    const o = tt(
      t.scrollHeight,
      t.clientHeight,
      n.scrollHeight,
      n.clientHeight
    )
    let a = -r.scrollLeft + Ki(e)
    const c = -r.scrollTop
    return (
      ht(n).direction === 'rtl' &&
                (a += tt(t.clientWidth, n.clientWidth) - i),
      { width: i, height: o, x: a, y: c }
    )
  }
  function Ca (e, t) {
    const r = ct(e)
    const n = Bt(e)
    const i = r.visualViewport
    let o = n.clientWidth
    let a = n.clientHeight
    let c = 0
    let f = 0
    if (i) {
      ((o = i.width), (a = i.height))
      const d = ni();
      (!d || (d && t === 'fixed')) &&
                ((c = i.offsetLeft), (f = i.offsetTop))
    }
    return { width: o, height: a, x: c, y: f }
  }
  function Da (e, t) {
    const r = vn(e, !0, t === 'fixed')
    const n = r.top + e.clientTop
    const i = r.left + e.clientLeft
    const o = Tt(e) ? Cn(e) : nn(1)
    const a = e.clientWidth * o.x
    const c = e.clientHeight * o.y
    const f = i * o.x
    const d = n * o.y
    return { width: a, height: c, x: f, y: d }
  }
  function ki (e, t, r) {
    let n
    if (t === 'viewport') n = Ca(e, r)
    else if (t === 'document') n = Aa(Bt(e))
    else if (Nt(t)) n = Da(t, r)
    else {
      const i = qi(e)
      n = { ...t, x: t.x - i.x, y: t.y - i.y }
    }
    return Dn(n)
  }
  function Ji (e, t) {
    const r = Tn(e)
    return r === t || !Nt(r) || gr(r)
      ? !1
      : ht(r).position === 'fixed' || Ji(r, t)
  }
  function _a (e, t) {
    const r = t.get(e)
    if (r) return r
    let n = Vn(e, [], !1).filter((c) => Nt(c) && rn(c) !== 'body')
    let i = null
    const o = ht(e).position === 'fixed'
    let a = o ? Tn(e) : e
    for (; Nt(a) && !gr(a);) {
      const c = ht(a)
      const f = ti(a);
      (!f && c.position === 'fixed' && (i = null),
      (
        o
          ? !f && !i
          : (!f &&
                              c.position === 'static' &&
                              !!i &&
                              ['absolute', 'fixed'].includes(i.position)) ||
                          (zn(a) && !f && Ji(e, a))
      )
        ? (n = n.filter((y) => y !== a))
        : (i = c),
      (a = Tn(a)))
    }
    return (t.set(e, n), n)
  }
  function Ta (e) {
    const { element: t, boundary: r, rootBoundary: n, strategy: i } = e
    const a = [
      ...(r === 'clippingAncestors' ? _a(t, this._c) : [].concat(r)),
      n
    ]
    const c = a[0]
    const f = a.reduce(
      (d, y) => {
        const m = ki(t, y, i)
        return (
          (d.top = tt(m.top, d.top)),
          (d.right = Et(m.right, d.right)),
          (d.bottom = Et(m.bottom, d.bottom)),
          (d.left = tt(m.left, d.left)),
          d
        )
      },
      ki(t, c, i)
    )
    return {
      width: f.right - f.left,
      height: f.bottom - f.top,
      x: f.left,
      y: f.top
    }
  }
  function Pa (e) {
    const { width: t, height: r } = Xi(e)
    return { width: t, height: r }
  }
  function Ma (e, t, r) {
    const n = Tt(t)
    const i = Bt(t)
    const o = r === 'fixed'
    const a = vn(e, !0, o, t)
    let c = { scrollLeft: 0, scrollTop: 0 }
    const f = nn(0)
    if (n || (!n && !o)) {
      if (((rn(t) !== 'body' || zn(i)) && (c = br(t)), n)) {
        const m = vn(t, !0, o, t);
        ((f.x = m.x + t.clientLeft), (f.y = m.y + t.clientTop))
      } else i && (f.x = Ki(i))
    }
    const d = a.left + c.scrollLeft - f.x
    const y = a.top + c.scrollTop - f.y
    return { x: d, y, width: a.width, height: a.height }
  }
  function Ni (e, t) {
    return !Tt(e) || ht(e).position === 'fixed'
      ? null
      : t
        ? t(e)
        : e.offsetParent
  }
  function Qi (e, t) {
    const r = ct(e)
    if (!Tt(e) || Gi(e)) return r
    let n = Ni(e, t)
    for (; n && ba(n) && ht(n).position === 'static';) n = Ni(n, t)
    return n &&
            (rn(n) === 'html' ||
                (rn(n) === 'body' && ht(n).position === 'static' && !ti(n)))
      ? r
      : n || ya(e) || r
  }
  const Ra = async function (e) {
    const t = this.getOffsetParent || Qi
    const r = this.getDimensions
    return {
      reference: Ma(e.reference, await t(e.floating), e.strategy),
      floating: { x: 0, y: 0, ...(await r(e.floating)) }
    }
  }
  function Ia (e) {
    return ht(e).direction === 'rtl'
  }
  const La = {
    convertOffsetParentRelativeRectToViewportRelativeRect: Oa,
    getDocumentElement: Bt,
    getClippingRect: Ta,
    getOffsetParent: Qi,
    getElementRects: Ra,
    getClientRects: Sa,
    getDimensions: Pa,
    getScale: Cn,
    isElement: Nt,
    isRTL: Ia
  }
  function Fa (e, t) {
    let r = null
    let n
    const i = Bt(e)
    function o () {
      let c;
      (clearTimeout(n), (c = r) == null || c.disconnect(), (r = null))
    }
    function a (c, f) {
      (c === void 0 && (c = !1), f === void 0 && (f = 1), o())
      const {
        left: d,
        top: y,
        width: m,
        height: O
      } = e.getBoundingClientRect()
      if ((c || t(), !m || !O)) return
      const E = pr(y)
      const S = pr(i.clientWidth - (d + m))
      const _ = pr(i.clientHeight - (y + O))
      const I = pr(d)
      const A = {
        rootMargin: -E + 'px ' + -S + 'px ' + -_ + 'px ' + -I + 'px',
        threshold: tt(0, Et(1, f)) || 1
      }
      let N = !0
      function Y (ne) {
        const J = ne[0].intersectionRatio
        if (J !== f) {
          if (!N) return a()
          J
            ? a(!1, J)
            : (n = setTimeout(() => {
                a(!1, 1e-7)
              }, 100))
        }
        N = !1
      }
      try {
        r = new IntersectionObserver(Y, {
          ...A,
          root: i.ownerDocument
        })
      } catch {
        r = new IntersectionObserver(Y, A)
      }
      r.observe(e)
    }
    return (a(!0), o)
  }
  function ji (e, t, r, n) {
    n === void 0 && (n = {})
    const {
      ancestorScroll: i = !0,
      ancestorResize: o = !0,
      elementResize: a = typeof ResizeObserver === 'function',
      layoutShift: c = typeof IntersectionObserver === 'function',
      animationFrame: f = !1
    } = n
    const d = ri(e)
    const y = i || o ? [...(d ? Vn(d) : []), ...Vn(t)] : []
    y.forEach(($) => {
      (i && $.addEventListener('scroll', r, { passive: !0 }),
      o && $.addEventListener('resize', r))
    })
    const m = d && c ? Fa(d, r) : null
    let O = -1
    let E = null
    a &&
            ((E = new ResizeObserver(($) => {
              const [A] = $;
              (A &&
                    A.target === d &&
                    E &&
                    (E.unobserve(t),
                    cancelAnimationFrame(O),
                    (O = requestAnimationFrame(() => {
                      let N;
                      (N = E) == null || N.observe(t)
                    }))),
              r())
            })),
            d && !f && E.observe(d),
            E.observe(t))
    let S
    let _ = f ? vn(e) : null
    f && I()
    function I () {
      const $ = vn(e);
      (_ &&
                ($.x !== _.x ||
                    $.y !== _.y ||
                    $.width !== _.width ||
                    $.height !== _.height) &&
                r(),
      (_ = $),
      (S = requestAnimationFrame(I)))
    }
    return (
      r(),
      () => {
        let $;
        (y.forEach((A) => {
          (i && A.removeEventListener('scroll', r),
          o && A.removeEventListener('resize', r))
        }),
        m?.(),
        ($ = E) == null || $.disconnect(),
        (E = null),
        f && cancelAnimationFrame(S))
      }
    )
  }
  const ii = fa
  const Zi = ma
  const eo = ua
  const to = ga
  const no = da
  const ro = la
  const io = ha
  const Bi = (e, t, r) => {
    const n = new Map()
    const i = { platform: La, ...r }
    const o = { ...i.platform, _c: n }
    return sa(e, t, { ...i, platform: o })
  }
  const ka = (e) => {
    const t = {
      placement: 'bottom',
      strategy: 'absolute',
      middleware: []
    }
    const r = Object.keys(e)
    const n = (i) => e[i]
    return (
      r.includes('offset') && t.middleware.push(Vi(n('offset'))),
      r.includes('teleport') && (t.strategy = 'fixed'),
      r.includes('placement') && (t.placement = n('placement')),
      r.some((i) => /^auto-?placement$/i.test(i)) &&
                !r.includes('flip') &&
                t.middleware.push(ii(n('autoPlacement'))),
      r.includes('flip') && t.middleware.push(eo(n('flip'))),
      r.includes('shift') && t.middleware.push(Zi(n('shift'))),
      r.includes('inline') && t.middleware.push(io(n('inline'))),
      r.includes('arrow') && t.middleware.push(ro(n('arrow'))),
      r.includes('hide') && t.middleware.push(no(n('hide'))),
      r.includes('size') && t.middleware.push(to(n('size'))),
      t
    )
  }
  const Na = (e, t) => {
    const r = {
      component: { trap: !1 },
      float: {
        placement: 'bottom',
        strategy: 'absolute',
        middleware: []
      }
    }
    const n = (i) => e[e.indexOf(i) + 1]
    if (
      (e.includes('trap') && (r.component.trap = !0),
      e.includes('teleport') && (r.float.strategy = 'fixed'),
      e.includes('offset') && r.float.middleware.push(Vi(t.offset || 10)),
      e.includes('placement') && (r.float.placement = n('placement')),
      e.some((i) => /^auto-?placement$/i.test(i)) &&
                !e.includes('flip') &&
                r.float.middleware.push(ii(t.autoPlacement)),
      e.includes('flip') && r.float.middleware.push(eo(t.flip)),
      e.includes('shift') && r.float.middleware.push(Zi(t.shift)),
      e.includes('inline') && r.float.middleware.push(io(t.inline)),
      e.includes('arrow') && r.float.middleware.push(ro(t.arrow)),
      e.includes('hide') && r.float.middleware.push(no(t.hide)),
      e.includes('size'))
    ) {
      const i = t.size?.availableWidth ?? null
      const o = t.size?.availableHeight ?? null;
      (i && delete t.size.availableWidth,
      o && delete t.size.availableHeight,
      r.float.middleware.push(
        to({
          ...t.size,
          apply ({
            availableWidth: a,
            availableHeight: c,
            elements: f
          }) {
            Object.assign(f.floating.style, {
              maxWidth: `${i ?? a}px`,
              maxHeight: `${o ?? c}px`
            })
          }
        })
      ))
    }
    return r
  }
  const ja = (e) => {
    const t =
            '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz'.split(
              ''
            )
    let r = ''
    e || (e = Math.floor(Math.random() * t.length))
    for (let n = 0; n < e; n++) {
      r += t[Math.floor(Math.random() * t.length)]
    }
    return r
  }
  function Ba (e, t = () => {}) {
    let r = !1
    return function () {
      r ? t.apply(this, arguments) : ((r = !0), e.apply(this, arguments))
    }
  }
  function Ha (e) {
    const t = { dismissable: !0, trap: !1 }
    function r (n, i = null) {
      if (n) {
        if (
          (n.hasAttribute('aria-expanded') ||
                        n.setAttribute('aria-expanded', !1),
          i.hasAttribute('id'))
        ) {
          n.setAttribute('aria-controls', i.getAttribute('id'))
        } else {
          const o = `panel-${ja(8)}`;
          (n.setAttribute('aria-controls', o),
          i.setAttribute('id', o))
        }
        (i.setAttribute('aria-modal', !0),
        i.setAttribute('role', 'dialog'))
      }
    }
    (e.magic('float', (n) => (i = {}, o = {}) => {
      const a = { ...t, ...o }
      const c =
                Object.keys(i).length > 0 ? ka(i) : { middleware: [ii()] }
      const f = n
      const d = n.parentElement.closest('[x-data]')
      const y = d.querySelector('[x-ref="panel"]')
      r(f, y)
      function m () {
        return y.style.display == 'block'
      }
      function O () {
        ((y.style.display = 'none'),
        f.setAttribute('aria-expanded', 'false'),
        a.trap && y.setAttribute('x-trap', 'false'),
        ji(n, y, _))
      }
      function E () {
        ((y.style.display = 'block'),
        f.setAttribute('aria-expanded', 'true'),
        a.trap && y.setAttribute('x-trap', 'true'),
        _())
      }
      function S () {
        m() ? O() : E()
      }
      async function _ () {
        return await Bi(n, y, c).then(
          ({ middlewareData: I, placement: $, x: A, y: N }) => {
            if (I.arrow) {
              const Y = I.arrow?.x
              const ne = I.arrow?.y
              const J = c.middleware.filter(
                (de) => de.name == 'arrow'
              )[0].options.element
              const V = {
                top: 'bottom',
                right: 'left',
                bottom: 'top',
                left: 'right'
              }[$.split('-')[0]]
              Object.assign(J.style, {
                left: Y != null ? `${Y}px` : '',
                top: ne != null ? `${ne}px` : '',
                right: '',
                bottom: '',
                [V]: '-4px'
              })
            }
            if (I.hide) {
              const { referenceHidden: Y } = I.hide
              Object.assign(y.style, {
                visibility: Y ? 'hidden' : 'visible'
              })
            }
            Object.assign(y.style, {
              left: `${A}px`,
              top: `${N}px`
            })
          }
        )
      }
      (a.dismissable &&
                (window.addEventListener('click', (I) => {
                  !d.contains(I.target) && m() && S()
                }),
                window.addEventListener(
                  'keydown',
                  (I) => {
                    I.key === 'Escape' && m() && S()
                  },
                  !0
                )),
      S())
    }),
    e.directive(
      'float',
      (
        n,
        { modifiers: i, expression: o },
        { evaluate: a, effect: c }
      ) => {
        const f = o ? a(o) : {}
        const d = i.length > 0 ? Na(i, f) : {}
        let y = null
        d.float.strategy == 'fixed' && (n.style.position = 'fixed')
        const m = (V) =>
          n.parentElement &&
                        !n.parentElement.closest('[x-data]').contains(V.target)
            ? n.close()
            : null
        const O = (V) => (V.key === 'Escape' ? n.close() : null)
        const E = n.getAttribute('x-ref')
        const S = n.parentElement.closest('[x-data]')
        const _ = S.querySelectorAll(`[\\@click^="$refs.${E}"]`)
        const I = S.querySelectorAll(
                        `[x-on\\:click^="$refs.${E}"]`
        );
        (n.style.setProperty('display', 'none'),
        r([..._, ...I][0], n),
        (n._x_isShown = !1),
        (n.trigger = null),
        n._x_doHide ||
                            (n._x_doHide = () => {
                              n.style.setProperty(
                                'display',
                                'none',
                                i.includes('important')
                                  ? 'important'
                                  : void 0
                              )
                            }),
        n._x_doShow ||
                            (n._x_doShow = () => {
                              n.style.setProperty(
                                'display',
                                'block',
                                i.includes('important')
                                  ? 'important'
                                  : void 0
                              )
                            }))
        const $ = () => {
          (n._x_doHide(), (n._x_isShown = !1))
        }
        const A = () => {
          (n._x_doShow(), (n._x_isShown = !0))
        }
        const N = () => setTimeout(A)
        const Y = Ba(
          (V) => (V ? A() : $()),
          (V) => {
            typeof n._x_toggleAndCascadeWithTransitions ===
                            'function'
              ? n._x_toggleAndCascadeWithTransitions(
                n,
                V,
                A,
                $
              )
              : V
                ? N()
                : $()
          }
        )
        let ne
        let J = !0;
        (c(() =>
          a((V) => {
            (!J && V === ne) ||
                                (i.includes('immediate') && (V ? N() : $()),
                                Y(V),
                                (ne = V),
                                (J = !1))
          })
        ),
        (n.open = async function (V) {
          ((n.trigger = V.currentTarget
            ? V.currentTarget
            : V),
          Y(!0),
          n.trigger.setAttribute('aria-expanded', 'true'),
          d.component.trap &&
                                    n.setAttribute('x-trap', 'true'),
          (y = ji(n.trigger, n, () => {
            Bi(n.trigger, n, d.float).then(
              ({
                middlewareData: de,
                placement: X,
                x: Q,
                y: me
              }) => {
                if (de.arrow) {
                  const l = de.arrow?.x
                  const h = de.arrow?.y
                  const v =
                                                    d.float.middleware.filter(
                                                      (j) =>
                                                        j.name == 'arrow'
                                                    )[0].options.element
                  const p = {
                    top: 'bottom',
                    right: 'left',
                    bottom: 'top',
                    left: 'right'
                  }[X.split('-')[0]]
                  Object.assign(v.style, {
                    left:
                                                        l != null
                                                          ? `${l}px`
                                                          : '',
                    top:
                                                        h != null
                                                          ? `${h}px`
                                                          : '',
                    right: '',
                    bottom: '',
                    [p]: '-4px'
                  })
                }
                if (de.hide) {
                  const { referenceHidden: l } =
                                                    de.hide
                  Object.assign(n.style, {
                    visibility: l
                      ? 'hidden'
                      : 'visible'
                  })
                }
                Object.assign(n.style, {
                  left: `${Q}px`,
                  top: `${me}px`
                })
              }
            )
          })),
          window.addEventListener('click', m),
          window.addEventListener('keydown', O, !0))
        }),
        (n.close = function () {
          if (!n._x_isShown) return !1;
          (Y(!1),
          n.trigger.setAttribute(
            'aria-expanded',
            'false'
          ),
          d.component.trap &&
                                    n.setAttribute('x-trap', 'false'),
          y(),
          window.removeEventListener('click', m),
          window.removeEventListener('keydown', O, !1))
        }),
        (n.toggle = function (V) {
          n._x_isShown ? n.close() : n.open(V)
        }))
      }
    ))
  }
  const oo = Ha
  function $a (e) {
    e.store('lazyLoadedAssets', {
      loaded: new Set(),
      check (a) {
        return Array.isArray(a)
          ? a.every((c) => this.loaded.has(c))
          : this.loaded.has(a)
      },
      markLoaded (a) {
        Array.isArray(a)
          ? a.forEach((c) => this.loaded.add(c))
          : this.loaded.add(a)
      }
    })
    const t = (a) =>
      new CustomEvent(a, {
        bubbles: !0,
        composed: !0,
        cancelable: !0
      })
    const r = (a, c = {}, f, d) => {
      const y = document.createElement(a)
      return (
        Object.entries(c).forEach(([m, O]) => (y[m] = O)),
        f && (d ? f.insertBefore(y, d) : f.appendChild(y)),
        y
      )
    }
    const n = (a, c, f = {}, d = null, y = null) => {
      const m = a === 'link' ? `link[href="${c}"]` : `script[src="${c}"]`
      if (
        document.querySelector(m) ||
                e.store('lazyLoadedAssets').check(c)
      ) {
        return Promise.resolve()
      }
      const O = a === 'link' ? { ...f, href: c } : { ...f, src: c }
      const E = r(a, O, d, y)
      return new Promise((S, _) => {
        ((E.onload = () => {
          (e.store('lazyLoadedAssets').markLoaded(c), S())
        }),
        (E.onerror = () => {
          _(new Error(`Failed to load ${a}: ${c}`))
        }))
      })
    }
    const i = async (a, c, f = null, d = null) => {
      const y = { type: 'text/css', rel: 'stylesheet' }
      c && (y.media = c)
      let m = document.head
      let O = null
      if (f && d) {
        const E = document.querySelector(`link[href*="${d}"]`)
        E
          ? ((m = E.parentElement),
            (O = f === 'before' ? E : E.nextSibling))
          : (console.warn(
                          `Target (${d}) not found for ${a}. Appending to head.`
            ),
            (m = document.head),
            (O = null))
      }
      await n('link', a, y, m, O)
    }
    const o = async (a, c, f = null, d = null, y = null) => {
      let m = document.head
      let O = null
      if (f && d) {
        const S = document.querySelector(`script[src*="${d}"]`)
        S
          ? ((m = S.parentElement),
            (O = f === 'before' ? S : S.nextSibling))
          : (console.warn(
                          `Target (${d}) not found for ${a}. Falling back to head or body.`
            ),
            (m = document.head),
            (O = null))
      } else {
        (c.has('body-start') || c.has('body-end')) &&
                    ((m = document.body),
                    c.has('body-start') && (O = document.body.firstChild))
      }
      const E = {};
      (y && (E.type = 'module'), await n('script', a, E, m, O))
    };
    (e.directive('load-css', (a, { expression: c }, { evaluate: f }) => {
      const d = f(c)
      const y = a.media
      const m = a.getAttribute('data-dispatch')
      const O = a.getAttribute('data-css-before')
        ? 'before'
        : a.getAttribute('data-css-after')
          ? 'after'
          : null
      const E =
                a.getAttribute('data-css-before') ||
                a.getAttribute('data-css-after') ||
                null
      Promise.all(d.map((S) => i(S, y, O, E)))
        .then(() => {
          m && window.dispatchEvent(t(`${m}-css`))
        })
        .catch(console.error)
    }),
    e.directive(
      'load-js',
      (a, { expression: c, modifiers: f }, { evaluate: d }) => {
        const y = d(c)
        const m = new Set(f)
        const O = a.getAttribute('data-js-before')
          ? 'before'
          : a.getAttribute('data-js-after')
            ? 'after'
            : null
        const E =
                        a.getAttribute('data-js-before') ||
                        a.getAttribute('data-js-after') ||
                        null
        const S =
                        a.getAttribute('data-js-as-module') ||
                        a.getAttribute('data-as-module') ||
                        !1
        const _ = a.getAttribute('data-dispatch')
        Promise.all(y.map((I) => o(I, m, O, E, S)))
          .then(() => {
            _ && window.dispatchEvent(t(`${_}-js`))
          })
          .catch(console.error)
      }
    ))
  }
  const ao = $a
  function Wa () {
    return !0
  }
  function Ua ({ component: e, argument: t }) {
    return new Promise((r) => {
      if (t) window.addEventListener(t, () => r(), { once: !0 })
      else {
        const n = (i) => {
          i.detail.id === e.id &&
                        (window.removeEventListener('async-alpine:load', n),
                        r())
        }
        window.addEventListener('async-alpine:load', n)
      }
    })
  }
  function Va () {
    return new Promise((e) => {
      'requestIdleCallback' in window
        ? window.requestIdleCallback(e)
        : setTimeout(e, 200)
    })
  }
  function za ({ argument: e }) {
    return new Promise((t) => {
      if (!e) {
        return (
          console.log(
            "Async Alpine: media strategy requires a media query. Treating as 'eager'"
          ),
          t()
        )
      }
      const r = window.matchMedia(`(${e})`)
      r.matches ? t() : r.addEventListener('change', t, { once: !0 })
    })
  }
  function Ya ({ component: e, argument: t }) {
    return new Promise((r) => {
      const n = t || '0px 0px 0px 0px'
      const i = new IntersectionObserver(
        (o) => {
          o[0].isIntersecting && (i.disconnect(), r())
        },
        { rootMargin: n }
      )
      i.observe(e.el)
    })
  }
  const so = { eager: Wa, event: Ua, idle: Va, media: za, visible: Ya }
  async function Xa (e) {
    const t = qa(e.strategy)
    await oi(e, t)
  }
  async function oi (e, t) {
    if (t.type === 'expression') {
      if (t.operator === '&&') {
        return Promise.all(t.parameters.map((r) => oi(e, r)))
      }
      if (t.operator === '||') {
        return Promise.any(t.parameters.map((r) => oi(e, r)))
      }
    }
    return so[t.method]
      ? so[t.method]({ component: e, argument: t.argument })
      : !1
  }
  function qa (e) {
    const t = Ga(e)
    const r = co(t)
    return r.type === 'method'
      ? { type: 'expression', operator: '&&', parameters: [r] }
      : r
  }
  function Ga (e) {
    const t =
            /\s*([()])\s*|\s*(\|\||&&|\|)\s*|\s*((?:[^()&|]+\([^()]+\))|[^()&|]+)\s*/g
    const r = []
    let n
    for (; (n = t.exec(e)) !== null;) {
      const [i, o, a, c] = n
      if (o !== void 0) r.push({ type: 'parenthesis', value: o })
      else if (a !== void 0) {
        r.push({ type: 'operator', value: a === '|' ? '&&' : a })
      } else {
        const f = { type: 'method', method: c.trim() };
        (c.includes('(') &&
                    ((f.method = c.substring(0, c.indexOf('(')).trim()),
                    (f.argument = c.substring(
                      c.indexOf('(') + 1,
                      c.indexOf(')')
                    ))),
        c.method === 'immediate' && (c.method = 'eager'),
        r.push(f))
      }
    }
    return r
  }
  function co (e) {
    let t = lo(e)
    for (
      ;
      e.length > 0 &&
            (e[0].value === '&&' || e[0].value === '|' || e[0].value === '||');

    ) {
      const r = e.shift().value
      const n = lo(e)
      t.type === 'expression' && t.operator === r
        ? t.parameters.push(n)
        : (t = { type: 'expression', operator: r, parameters: [t, n] })
    }
    return t
  }
  function lo (e) {
    if (e[0].value === '(') {
      e.shift()
      const t = co(e)
      return (e[0].value === ')' && e.shift(), t)
    } else return e.shift()
  }
  function fo (e) {
    const t = 'load'
    const r = e.prefixed('load-src')
    const n = e.prefixed('ignore')
    let i = { defaultStrategy: 'eager', keepRelativeURLs: !1 }
    let o = !1
    const a = {}
    let c = 0
    function f () {
      return c++
    }
    ((e.asyncOptions = (A) => {
      i = { ...i, ...A }
    }),
    (e.asyncData = (A, N = !1) => {
      a[A] = { loaded: !1, download: N }
    }),
    (e.asyncUrl = (A, N) => {
      !A ||
                    !N ||
                    a[A] ||
                    (a[A] = { loaded: !1, download: () => import($(N)) })
    }),
    (e.asyncAlias = (A) => {
      o = A
    }))
    const d = (A) => {
      e.skipDuringClone(() => {
        A._x_async ||
                    ((A._x_async = 'init'),
                    (A._x_ignore = !0),
                    A.setAttribute(n, ''))
      })()
    }
    const y = async (A) => {
      e.skipDuringClone(async () => {
        if (A._x_async !== 'init') return
        A._x_async = 'await'
        const { name: N, strategy: Y } = m(A);
        (await Xa({ name: N, strategy: Y, el: A, id: A.id || f() }),
        A.isConnected &&
                        (await O(N),
                        A.isConnected && (S(A), (A._x_async = 'loaded'))))
      })()
    };
    ((y.inline = d), e.directive(t, y).before('ignore'))
    function m (A) {
      const N = I(A.getAttribute(e.prefixed('data')))
      const Y = A.getAttribute(e.prefixed(t)) || i.defaultStrategy
      const ne = A.getAttribute(r)
      return (ne && e.asyncUrl(N, ne), { name: N, strategy: Y })
    }
    async function O (A) {
      if (A.startsWith('_x_async_') || (_(A), !a[A] || a[A].loaded)) {
        return
      }
      const N = await E(A);
      (e.data(A, N), (a[A].loaded = !0))
    }
    async function E (A) {
      if (!a[A]) return
      const N = await a[A].download(A)
      return typeof N === 'function'
        ? N
        : N[A] || N.default || Object.values(N)[0] || !1
    }
    function S (A) {
      (e.destroyTree(A),
      (A._x_ignore = !1),
      A.removeAttribute(n),
      !A.closest(`[${n}]`) && e.initTree(A))
    }
    function _ (A) {
      if (!(!o || a[A])) {
        if (typeof o === 'function') {
          e.asyncData(A, o)
          return
        }
        e.asyncUrl(A, o.replaceAll('[name]', A))
      }
    }
    function I (A) {
      return (A || '').trim().split(/[({]/g)[0] || `_x_async_${f()}`
    }
    function $ (A) {
      return i.keepRelativeURLs ||
                new RegExp('^(?:[a-z+]+:)?//', 'i').test(A)
        ? A
        : new URL(A, document.baseURI).href
    }
  }
  const Xo = ea(ho(), 1)
  function vo (e, t) {
    const r = Object.keys(e)
    if (Object.getOwnPropertySymbols) {
      let n = Object.getOwnPropertySymbols(e);
      (t &&
                (n = n.filter(function (i) {
                  return Object.getOwnPropertyDescriptor(e, i).enumerable
                })),
      r.push.apply(r, n))
    }
    return r
  }
  function Mt (e) {
    for (let t = 1; t < arguments.length; t++) {
      var r = arguments[t] != null ? arguments[t] : {}
      t % 2
        ? vo(Object(r), !0).forEach(function (n) {
          Ka(e, n, r[n])
        })
        : Object.getOwnPropertyDescriptors
          ? Object.defineProperties(
            e,
            Object.getOwnPropertyDescriptors(r)
          )
          : vo(Object(r)).forEach(function (n) {
            Object.defineProperty(
              e,
              n,
              Object.getOwnPropertyDescriptor(r, n)
            )
          })
    }
    return e
  }
  function Sr (e) {
    '@babel/helpers - typeof'
    return (
      typeof Symbol === 'function' && typeof Symbol.iterator === 'symbol'
        ? (Sr = function (t) {
            return typeof t
          })
        : (Sr = function (t) {
            return t &&
                          typeof Symbol === 'function' &&
                          t.constructor === Symbol &&
                          t !== Symbol.prototype
              ? 'symbol'
              : typeof t
          }),
      Sr(e)
    )
  }
  function Ka (e, t, r) {
    return (
      t in e
        ? Object.defineProperty(e, t, {
          value: r,
          enumerable: !0,
          configurable: !0,
          writable: !0
        })
        : (e[t] = r),
      e
    )
  }
  function $t () {
    return (
      ($t =
                Object.assign ||
                function (e) {
                  for (let t = 1; t < arguments.length; t++) {
                    const r = arguments[t]
                    for (const n in r) {
                      Object.prototype.hasOwnProperty.call(r, n) &&
                                (e[n] = r[n])
                    }
                  }
                  return e
                }),
      $t.apply(this, arguments)
    )
  }
  function Ja (e, t) {
    if (e == null) return {}
    const r = {}
    const n = Object.keys(e)
    let i
    let o
    for (o = 0; o < n.length; o++) {
      ((i = n[o]), !(t.indexOf(i) >= 0) && (r[i] = e[i]))
    }
    return r
  }
  function Qa (e, t) {
    if (e == null) return {}
    const r = Ja(e, t)
    let n
    let i
    if (Object.getOwnPropertySymbols) {
      const o = Object.getOwnPropertySymbols(e)
      for (i = 0; i < o.length; i++) {
        ((n = o[i]),
        !(t.indexOf(n) >= 0) &&
                        Object.prototype.propertyIsEnumerable.call(e, n) &&
                        (r[n] = e[n]))
      }
    }
    return r
  }
  const Za = '1.15.6'
  function Ht (e) {
    if (typeof window < 'u' && window.navigator) {
      return !!navigator.userAgent.match(e)
    }
  }
  const Wt = Ht(/(?:Trident.*rv[ :]?11\.|msie|iemobile|Windows Phone)/i)
  const er = Ht(/Edge/i)
  const mo = Ht(/firefox/i)
  const Gn = Ht(/safari/i) && !Ht(/chrome/i) && !Ht(/android/i)
  const yi = Ht(/iP(ad|od|hone)/i)
  const So = Ht(/chrome/i) && Ht(/android/i)
  const Ao = { capture: !1, passive: !1 }
  function Oe (e, t, r) {
    e.addEventListener(t, r, !Wt && Ao)
  }
  function Ee (e, t, r) {
    e.removeEventListener(t, r, !Wt && Ao)
  }
  function Tr (e, t) {
    if (t) {
      if ((t[0] === '>' && (t = t.substring(1)), e)) {
        try {
          if (e.matches) return e.matches(t)
          if (e.msMatchesSelector) return e.msMatchesSelector(t)
          if (e.webkitMatchesSelector) {
            return e.webkitMatchesSelector(t)
          }
        } catch {
          return !1
        }
      }
      return !1
    }
  }
  function Co (e) {
    return e.host && e !== document && e.host.nodeType
      ? e.host
      : e.parentNode
  }
  function St (e, t, r, n) {
    if (e) {
      r = r || document
      do {
        if (
          (t != null &&
                        (t[0] === '>'
                          ? e.parentNode === r && Tr(e, t)
                          : Tr(e, t))) ||
                    (n && e === r)
        ) {
          return e
        }
        if (e === r) break
      } while ((e = Co(e)))
    }
    return null
  }
  const go = /\s+/g
  function ft (e, t, r) {
    if (e && t) {
      if (e.classList) e.classList[r ? 'add' : 'remove'](t)
      else {
        const n = (' ' + e.className + ' ')
          .replace(go, ' ')
          .replace(' ' + t + ' ', ' ')
        e.className = (n + (r ? ' ' + t : '')).replace(go, ' ')
      }
    }
  }
  function ae (e, t, r) {
    const n = e && e.style
    if (n) {
      if (r === void 0) {
        return (
          document.defaultView &&
                    document.defaultView.getComputedStyle
            ? (r = document.defaultView.getComputedStyle(e, ''))
            : e.currentStyle && (r = e.currentStyle),
          t === void 0 ? r : r[t]
        )
      }
      (!(t in n) && t.indexOf('webkit') === -1 && (t = '-webkit-' + t),
      (n[t] = r + (typeof r === 'string' ? '' : 'px')))
    }
  }
  function Fn (e, t) {
    let r = ''
    if (typeof e === 'string') r = e
    else {
      do {
        const n = ae(e, 'transform')
        n && n !== 'none' && (r = n + ' ' + r)
      } while (!t && (e = e.parentNode))
    }
    const i =
            window.DOMMatrix ||
            window.WebKitCSSMatrix ||
            window.CSSMatrix ||
            window.MSCSSMatrix
    return i && new i(r)
  }
  function Do (e, t, r) {
    if (e) {
      const n = e.getElementsByTagName(t)
      let i = 0
      const o = n.length
      if (r) for (; i < o; i++) r(n[i], i)
      return n
    }
    return []
  }
  function Pt () {
    const e = document.scrollingElement
    return e || document.documentElement
  }
  function qe (e, t, r, n, i) {
    if (!(!e.getBoundingClientRect && e !== window)) {
      let o, a, c, f, d, y, m
      if (
        (e !== window && e.parentNode && e !== Pt()
          ? ((o = e.getBoundingClientRect()),
            (a = o.top),
            (c = o.left),
            (f = o.bottom),
            (d = o.right),
            (y = o.height),
            (m = o.width))
          : ((a = 0),
            (c = 0),
            (f = window.innerHeight),
            (d = window.innerWidth),
            (y = window.innerHeight),
            (m = window.innerWidth)),
        (t || r) && e !== window && ((i = i || e.parentNode), !Wt))
      ) {
        do {
          if (
            i &&
                        i.getBoundingClientRect &&
                        (ae(i, 'transform') !== 'none' ||
                            (r && ae(i, 'position') !== 'static'))
          ) {
            const O = i.getBoundingClientRect();
            ((a -= O.top + parseInt(ae(i, 'border-top-width'))),
            (c -=
                                O.left + parseInt(ae(i, 'border-left-width'))),
            (f = a + o.height),
            (d = c + o.width))
            break
          }
        } while ((i = i.parentNode))
      }
      if (n && e !== window) {
        const E = Fn(i || e)
        const S = E && E.a
        const _ = E && E.d
        E &&
                    ((a /= _),
                    (c /= S),
                    (m /= S),
                    (y /= _),
                    (f = a + y),
                    (d = c + m))
      }
      return {
        top: a,
        left: c,
        bottom: f,
        right: d,
        width: m,
        height: y
      }
    }
  }
  function bo (e, t, r) {
    for (let n = sn(e, !0), i = qe(e)[t]; n;) {
      const o = qe(n)[r]
      let a = void 0
      if (
        (r === 'top' || r === 'left' ? (a = i >= o) : (a = i <= o), !a)
      ) {
        return n
      }
      if (n === Pt()) break
      n = sn(n, !1)
    }
    return !1
  }
  function kn (e, t, r, n) {
    for (let i = 0, o = 0, a = e.children; o < a.length;) {
      if (
        a[o].style.display !== 'none' &&
                a[o] !== se.ghost &&
                (n || a[o] !== se.dragged) &&
                St(a[o], r.draggable, e, !1)
      ) {
        if (i === t) return a[o]
        i++
      }
      o++
    }
    return null
  }
  function wi (e, t) {
    for (
      var r = e.lastElementChild;
      r &&
            (r === se.ghost || ae(r, 'display') === 'none' || (t && !Tr(r, t)));

    ) {
      r = r.previousElementSibling
    }
    return r || null
  }
  function vt (e, t) {
    let r = 0
    if (!e || !e.parentNode) return -1
    for (; (e = e.previousElementSibling);) {
      e.nodeName.toUpperCase() !== 'TEMPLATE' &&
                e !== se.clone &&
                (!t || Tr(e, t)) &&
                r++
    }
    return r
  }
  function yo (e) {
    let t = 0
    let r = 0
    const n = Pt()
    if (e) {
      do {
        const i = Fn(e)
        const o = i.a
        const a = i.d;
        ((t += e.scrollLeft * o), (r += e.scrollTop * a))
      } while (e !== n && (e = e.parentNode))
    }
    return [t, r]
  }
  function es (e, t) {
    for (const r in e) {
      if (e.hasOwnProperty(r)) {
        for (const n in t) {
          if (t.hasOwnProperty(n) && t[n] === e[r][n]) {
            return Number(r)
          }
        }
      }
    }
    return -1
  }
  function sn (e, t) {
    if (!e || !e.getBoundingClientRect) return Pt()
    let r = e
    let n = !1
    do {
      if (
        r.clientWidth < r.scrollWidth ||
                r.clientHeight < r.scrollHeight
      ) {
        const i = ae(r)
        if (
          (r.clientWidth < r.scrollWidth &&
                        (i.overflowX == 'auto' || i.overflowX == 'scroll')) ||
                    (r.clientHeight < r.scrollHeight &&
                        (i.overflowY == 'auto' || i.overflowY == 'scroll'))
        ) {
          if (!r.getBoundingClientRect || r === document.body) {
            return Pt()
          }
          if (n || t) return r
          n = !0
        }
      }
    } while ((r = r.parentNode))
    return Pt()
  }
  function ts (e, t) {
    if (e && t) for (const r in t) t.hasOwnProperty(r) && (e[r] = t[r])
    return e
  }
  function ai (e, t) {
    return (
      Math.round(e.top) === Math.round(t.top) &&
            Math.round(e.left) === Math.round(t.left) &&
            Math.round(e.height) === Math.round(t.height) &&
            Math.round(e.width) === Math.round(t.width)
    )
  }
  let Kn
  function _o (e, t) {
    return function () {
      if (!Kn) {
        const r = arguments
        const n = this;
        (r.length === 1 ? e.call(n, r[0]) : e.apply(n, r),
        (Kn = setTimeout(function () {
          Kn = void 0
        }, t)))
      }
    }
  }
  function ns () {
    (clearTimeout(Kn), (Kn = void 0))
  }
  function To (e, t, r) {
    ((e.scrollLeft += t), (e.scrollTop += r))
  }
  function Po (e) {
    const t = window.Polymer
    const r = window.jQuery || window.Zepto
    return t && t.dom
      ? t.dom(e).cloneNode(!0)
      : r
        ? r(e).clone(!0)[0]
        : e.cloneNode(!0)
  }
  function Mo (e, t, r) {
    const n = {}
    return (
      Array.from(e.children).forEach(function (i) {
        let o, a, c, f
        if (!(!St(i, t.draggable, e, !1) || i.animated || i === r)) {
          const d = qe(i);
          ((n.left = Math.min(
            (o = n.left) !== null && o !== void 0 ? o : 1 / 0,
            d.left
          )),
          (n.top = Math.min(
            (a = n.top) !== null && a !== void 0 ? a : 1 / 0,
            d.top
          )),
          (n.right = Math.max(
            (c = n.right) !== null && c !== void 0 ? c : -1 / 0,
            d.right
          )),
          (n.bottom = Math.max(
            (f = n.bottom) !== null && f !== void 0
              ? f
              : -1 / 0,
            d.bottom
          )))
        }
      }),
      (n.width = n.right - n.left),
      (n.height = n.bottom - n.top),
      (n.x = n.left),
      (n.y = n.top),
      n
    )
  }
  const st = 'Sortable' + new Date().getTime()
  function rs () {
    let e = []
    let t
    return {
      captureAnimationState: function () {
        if (((e = []), !!this.options.animation)) {
          const n = [].slice.call(this.el.children)
          n.forEach(function (i) {
            if (!(ae(i, 'display') === 'none' || i === se.ghost)) {
              e.push({ target: i, rect: qe(i) })
              const o = Mt({}, e[e.length - 1].rect)
              if (i.thisAnimationDuration) {
                const a = Fn(i, !0)
                a && ((o.top -= a.f), (o.left -= a.e))
              }
              i.fromRect = o
            }
          })
        }
      },
      addAnimationState: function (n) {
        e.push(n)
      },
      removeAnimationState: function (n) {
        e.splice(es(e, { target: n }), 1)
      },
      animateAll: function (n) {
        const i = this
        if (!this.options.animation) {
          (clearTimeout(t), typeof n === 'function' && n())
          return
        }
        let o = !1
        let a = 0;
        (e.forEach(function (c) {
          let f = 0
          const d = c.target
          const y = d.fromRect
          const m = qe(d)
          const O = d.prevFromRect
          const E = d.prevToRect
          const S = c.rect
          const _ = Fn(d, !0);
          (_ && ((m.top -= _.f), (m.left -= _.e)),
          (d.toRect = m),
          d.thisAnimationDuration &&
                            ai(O, m) &&
                            !ai(y, m) &&
                            (S.top - m.top) / (S.left - m.left) ===
                                (y.top - m.top) / (y.left - m.left) &&
                            (f = os(S, O, E, i.options)),
          ai(m, y) ||
                            ((d.prevFromRect = y),
                            (d.prevToRect = m),
                            f || (f = i.options.animation),
                            i.animate(d, S, m, f)),
          f &&
                            ((o = !0),
                            (a = Math.max(a, f)),
                            clearTimeout(d.animationResetTimer),
                            (d.animationResetTimer = setTimeout(function () {
                              ((d.animationTime = 0),
                              (d.prevFromRect = null),
                              (d.fromRect = null),
                              (d.prevToRect = null),
                              (d.thisAnimationDuration = null))
                            }, f)),
                            (d.thisAnimationDuration = f)))
        }),
        clearTimeout(t),
        o
          ? (t = setTimeout(function () {
              typeof n === 'function' && n()
            }, a))
          : typeof n === 'function' && n(),
        (e = []))
      },
      animate: function (n, i, o, a) {
        if (a) {
          (ae(n, 'transition', ''), ae(n, 'transform', ''))
          const c = Fn(this.el)
          const f = c && c.a
          const d = c && c.d
          const y = (i.left - o.left) / (f || 1)
          const m = (i.top - o.top) / (d || 1);
          ((n.animatingX = !!y),
          (n.animatingY = !!m),
          ae(
            n,
            'transform',
            'translate3d(' + y + 'px,' + m + 'px,0)'
          ),
          (this.forRepaintDummy = is(n)),
          ae(
            n,
            'transition',
            'transform ' +
                                a +
                                'ms' +
                                (this.options.easing
                                  ? ' ' + this.options.easing
                                  : '')
          ),
          ae(n, 'transform', 'translate3d(0,0,0)'),
          typeof n.animated === 'number' &&
                            clearTimeout(n.animated),
          (n.animated = setTimeout(function () {
            (ae(n, 'transition', ''),
            ae(n, 'transform', ''),
            (n.animated = !1),
            (n.animatingX = !1),
            (n.animatingY = !1))
          }, a)))
        }
      }
    }
  }
  function is (e) {
    return e.offsetWidth
  }
  function os (e, t, r, n) {
    return (
      (Math.sqrt(
        Math.pow(t.top - e.top, 2) + Math.pow(t.left - e.left, 2)
      ) /
                Math.sqrt(
                  Math.pow(t.top - r.top, 2) + Math.pow(t.left - r.left, 2)
                )) *
            n.animation
    )
  }
  const Mn = []
  const si = { initializeByDefault: !0 }
  const tr = {
    mount: function (t) {
      for (const r in si) {
        si.hasOwnProperty(r) && !(r in t) && (t[r] = si[r])
      }
      (Mn.forEach(function (n) {
        if (n.pluginName === t.pluginName) {
          throw 'Sortable: Cannot mount plugin '.concat(
            t.pluginName,
            ' more than once'
          )
        }
      }),
      Mn.push(t))
    },
    pluginEvent: function (t, r, n) {
      const i = this;
      ((this.eventCanceled = !1),
      (n.cancel = function () {
        i.eventCanceled = !0
      }))
      const o = t + 'Global'
      Mn.forEach(function (a) {
        r[a.pluginName] &&
                    (r[a.pluginName][o] &&
                        r[a.pluginName][o](Mt({ sortable: r }, n)),
                    r.options[a.pluginName] &&
                        r[a.pluginName][t] &&
                        r[a.pluginName][t](Mt({ sortable: r }, n)))
      })
    },
    initializePlugins: function (t, r, n, i) {
      Mn.forEach(function (c) {
        const f = c.pluginName
        if (!(!t.options[f] && !c.initializeByDefault)) {
          const d = new c(t, r, t.options);
          ((d.sortable = t),
          (d.options = t.options),
          (t[f] = d),
          $t(n, d.defaults))
        }
      })
      for (const o in t.options) {
        if (t.options.hasOwnProperty(o)) {
          const a = this.modifyOption(t, o, t.options[o])
          typeof a < 'u' && (t.options[o] = a)
        }
      }
    },
    getEventProperties: function (t, r) {
      const n = {}
      return (
        Mn.forEach(function (i) {
          typeof i.eventProperties === 'function' &&
                        $t(n, i.eventProperties.call(r[i.pluginName], t))
        }),
        n
      )
    },
    modifyOption: function (t, r, n) {
      let i
      return (
        Mn.forEach(function (o) {
          t[o.pluginName] &&
                        o.optionListeners &&
                        typeof o.optionListeners[r] === 'function' &&
                        (i = o.optionListeners[r].call(t[o.pluginName], n))
        }),
        i
      )
    }
  }
  function as (e) {
    let t = e.sortable
    const r = e.rootEl
    const n = e.name
    const i = e.targetEl
    const o = e.cloneEl
    const a = e.toEl
    const c = e.fromEl
    const f = e.oldIndex
    const d = e.newIndex
    const y = e.oldDraggableIndex
    const m = e.newDraggableIndex
    const O = e.originalEvent
    const E = e.putSortable
    const S = e.extraEventProperties
    if (((t = t || (r && r[st])), !!t)) {
      let _
      const I = t.options
      const $ = 'on' + n.charAt(0).toUpperCase() + n.substr(1);
      (window.CustomEvent && !Wt && !er
        ? (_ = new CustomEvent(n, { bubbles: !0, cancelable: !0 }))
        : ((_ = document.createEvent('Event')), _.initEvent(n, !0, !0)),
      (_.to = a || r),
      (_.from = c || r),
      (_.item = i || r),
      (_.clone = o),
      (_.oldIndex = f),
      (_.newIndex = d),
      (_.oldDraggableIndex = y),
      (_.newDraggableIndex = m),
      (_.originalEvent = O),
      (_.pullMode = E ? E.lastPutMode : void 0))
      const A = Mt(Mt({}, S), tr.getEventProperties(n, t))
      for (const N in A) _[N] = A[N];
      (r && r.dispatchEvent(_), I[$] && I[$].call(t, _))
    }
  }
  const ss = ['evt']
  const at = function (t, r) {
    const n =
            arguments.length > 2 && arguments[2] !== void 0 ? arguments[2] : {}
    const i = n.evt
    const o = Qa(n, ss)
    tr.pluginEvent.bind(se)(
      t,
      r,
      Mt(
        {
          dragEl: k,
          parentEl: Ve,
          ghostEl: ue,
          rootEl: Ne,
          nextEl: bn,
          lastDownEl: Ar,
          cloneEl: We,
          cloneHidden: an,
          dragStarted: Yn,
          putSortable: Qe,
          activeSortable: se.active,
          originalEvent: i,
          oldIndex: Ln,
          oldDraggableIndex: Jn,
          newIndex: ut,
          newDraggableIndex: on,
          hideGhostForTarget: Fo,
          unhideGhostForTarget: ko,
          cloneNowHidden: function () {
            an = !0
          },
          cloneNowShown: function () {
            an = !1
          },
          dispatchSortableEvent: function (c) {
            it({ sortable: r, name: c, originalEvent: i })
          }
        },
        o
      )
    )
  }
  function it (e) {
    as(
      Mt(
        {
          putSortable: Qe,
          cloneEl: We,
          targetEl: k,
          rootEl: Ne,
          oldIndex: Ln,
          oldDraggableIndex: Jn,
          newIndex: ut,
          newDraggableIndex: on
        },
        e
      )
    )
  }
  let k
  let Ve
  let ue
  let Ne
  let bn
  let Ar
  let We
  let an
  let Ln
  let ut
  let Jn
  let on
  let wr
  let Qe
  let In = !1
  let Pr = !1
  const Mr = []
  let mn
  let Ot
  let li
  let ci
  let wo
  let xo
  let Yn
  let Rn
  let Qn
  let Zn = !1
  let xr = !1
  let Cr
  let nt
  let fi = []
  let vi = !1
  const Rr = []
  const Lr = typeof document < 'u'
  const Er = yi
  const Eo = er || Wt ? 'cssFloat' : 'float'
  const ls = Lr && !So && !yi && 'draggable' in document.createElement('div')
  const Ro = (function () {
    if (Lr) {
      if (Wt) return !1
      const e = document.createElement('x')
      return (
        (e.style.cssText = 'pointer-events:auto'),
        e.style.pointerEvents === 'auto'
      )
    }
  })()
  const Io = function (t, r) {
    const n = ae(t)
    const i =
            parseInt(n.width) -
            parseInt(n.paddingLeft) -
            parseInt(n.paddingRight) -
            parseInt(n.borderLeftWidth) -
            parseInt(n.borderRightWidth)
    const o = kn(t, 0, r)
    const a = kn(t, 1, r)
    const c = o && ae(o)
    const f = a && ae(a)
    const d =
            c && parseInt(c.marginLeft) + parseInt(c.marginRight) + qe(o).width
    const y =
            f && parseInt(f.marginLeft) + parseInt(f.marginRight) + qe(a).width
    if (n.display === 'flex') {
      return n.flexDirection === 'column' ||
                n.flexDirection === 'column-reverse'
        ? 'vertical'
        : 'horizontal'
    }
    if (n.display === 'grid') {
      return n.gridTemplateColumns.split(' ').length <= 1
        ? 'vertical'
        : 'horizontal'
    }
    if (o && c.float && c.float !== 'none') {
      const m = c.float === 'left' ? 'left' : 'right'
      return a && (f.clear === 'both' || f.clear === m)
        ? 'vertical'
        : 'horizontal'
    }
    return o &&
            (c.display === 'block' ||
                c.display === 'flex' ||
                c.display === 'table' ||
                c.display === 'grid' ||
                (d >= i && n[Eo] === 'none') ||
                (a && n[Eo] === 'none' && d + y > i))
      ? 'vertical'
      : 'horizontal'
  }
  const cs = function (t, r, n) {
    const i = n ? t.left : t.top
    const o = n ? t.right : t.bottom
    const a = n ? t.width : t.height
    const c = n ? r.left : r.top
    const f = n ? r.right : r.bottom
    const d = n ? r.width : r.height
    return i === c || o === f || i + a / 2 === c + d / 2
  }
  const fs = function (t, r) {
    let n
    return (
      Mr.some(function (i) {
        const o = i[st].options.emptyInsertThreshold
        if (!(!o || wi(i))) {
          const a = qe(i)
          const c = t >= a.left - o && t <= a.right + o
          const f = r >= a.top - o && r <= a.bottom + o
          if (c && f) return (n = i)
        }
      }),
      n
    )
  }
  const Lo = function (t) {
    function r (o, a) {
      return function (c, f, d, y) {
        const m =
                    c.options.group.name &&
                    f.options.group.name &&
                    c.options.group.name === f.options.group.name
        if (o == null && (a || m)) return !0
        if (o == null || o === !1) return !1
        if (a && o === 'clone') return o
        if (typeof o === 'function') {
          return r(o(c, f, d, y), a)(c, f, d, y)
        }
        const O = (a ? c : f).options.group.name
        return (
          o === !0 ||
                    (typeof o === 'string' && o === O) ||
                    (o.join && o.indexOf(O) > -1)
        )
      }
    }
    const n = {}
    let i = t.group;
    ((!i || Sr(i) != 'object') && (i = { name: i }),
    (n.name = i.name),
    (n.checkPull = r(i.pull, !0)),
    (n.checkPut = r(i.put)),
    (n.revertClone = i.revertClone),
    (t.group = n))
  }
  var Fo = function () {
    !Ro && ue && ae(ue, 'display', 'none')
  }
  var ko = function () {
    !Ro && ue && ae(ue, 'display', '')
  }
  Lr &&
        !So &&
        document.addEventListener(
          'click',
          function (e) {
            if (Pr) {
              return (
                e.preventDefault(),
                e.stopPropagation && e.stopPropagation(),
                e.stopImmediatePropagation &&
                            e.stopImmediatePropagation(),
                (Pr = !1),
                !1
              )
            }
          },
          !0
        )
  const gn = function (t) {
    if (k) {
      t = t.touches ? t.touches[0] : t
      const r = fs(t.clientX, t.clientY)
      if (r) {
        const n = {}
        for (const i in t) t.hasOwnProperty(i) && (n[i] = t[i]);
        ((n.target = n.rootEl = r),
        (n.preventDefault = void 0),
        (n.stopPropagation = void 0),
        r[st]._onDragOver(n))
      }
    }
  }
  const us = function (t) {
    k && k.parentNode[st]._isOutsideThisEl(t.target)
  }
  function se (e, t) {
    if (!(e && e.nodeType && e.nodeType === 1)) {
      throw 'Sortable: `el` must be an HTMLElement, not '.concat(
        {}.toString.call(e)
      )
    }
    ((this.el = e), (this.options = t = $t({}, t)), (e[st] = this))
    const r = {
      group: null,
      sort: !0,
      disabled: !1,
      store: null,
      handle: null,
      draggable: /^[uo]l$/i.test(e.nodeName) ? '>li' : '>*',
      swapThreshold: 1,
      invertSwap: !1,
      invertedSwapThreshold: null,
      removeCloneOnHide: !0,
      direction: function () {
        return Io(e, this.options)
      },
      ghostClass: 'sortable-ghost',
      chosenClass: 'sortable-chosen',
      dragClass: 'sortable-drag',
      ignore: 'a, img',
      filter: null,
      preventOnFilter: !0,
      animation: 0,
      easing: null,
      setData: function (a, c) {
        a.setData('Text', c.textContent)
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
                se.supportPointer !== !1 &&
                'PointerEvent' in window &&
                (!Gn || yi),
      emptyInsertThreshold: 5
    }
    tr.initializePlugins(this, e, r)
    for (const n in r) !(n in t) && (t[n] = r[n])
    Lo(t)
    for (const i in this) {
      i.charAt(0) === '_' &&
                typeof this[i] === 'function' &&
                (this[i] = this[i].bind(this))
    }
    ((this.nativeDraggable = t.forceFallback ? !1 : ls),
    this.nativeDraggable && (this.options.touchStartThreshold = 1),
    t.supportPointer
      ? Oe(e, 'pointerdown', this._onTapStart)
      : (Oe(e, 'mousedown', this._onTapStart),
        Oe(e, 'touchstart', this._onTapStart)),
    this.nativeDraggable &&
                (Oe(e, 'dragover', this), Oe(e, 'dragenter', this)),
    Mr.push(this.el),
    t.store && t.store.get && this.sort(t.store.get(this) || []),
    $t(this, rs()))
  }
  se.prototype = {
    constructor: se,
    _isOutsideThisEl: function (t) {
      !this.el.contains(t) && t !== this.el && (Rn = null)
    },
    _getDirection: function (t, r) {
      return typeof this.options.direction === 'function'
        ? this.options.direction.call(this, t, r, k)
        : this.options.direction
    },
    _onTapStart: function (t) {
      if (t.cancelable) {
        const r = this
        const n = this.el
        const i = this.options
        const o = i.preventOnFilter
        const a = t.type
        const c =
                    (t.touches && t.touches[0]) ||
                    (t.pointerType && t.pointerType === 'touch' && t)
        let f = (c || t).target
        const d =
                    (t.target.shadowRoot &&
                        ((t.path && t.path[0]) ||
                            (t.composedPath && t.composedPath()[0]))) ||
                    f
        let y = i.filter
        if (
          (ys(n),
          !k &&
                        !(
                          (/mousedown|pointerdown/.test(a) &&
                                t.button !== 0) ||
                            i.disabled
                        ) &&
                        !d.isContentEditable &&
                        !(
                          !this.nativeDraggable &&
                            Gn &&
                            f &&
                            f.tagName.toUpperCase() === 'SELECT'
                        ) &&
                        ((f = St(f, i.draggable, n, !1)),
                        !(f && f.animated) && Ar !== f))
        ) {
          if (
            ((Ln = vt(f)),
            (Jn = vt(f, i.draggable)),
            typeof y === 'function')
          ) {
            if (y.call(this, t, f, this)) {
              (it({
                sortable: r,
                rootEl: d,
                name: 'filter',
                targetEl: f,
                toEl: n,
                fromEl: n
              }),
              at('filter', r, { evt: t }),
              o && t.preventDefault())
              return
            }
          } else if (
            y &&
                        ((y = y.split(',').some(function (m) {
                          if (((m = St(d, m.trim(), n, !1)), m)) {
                            return (
                              it({
                                sortable: r,
                                rootEl: m,
                                name: 'filter',
                                targetEl: f,
                                fromEl: n,
                                toEl: n
                              }),
                              at('filter', r, { evt: t }),
                              !0
                            )
                          }
                        })),
                        y)
          ) {
            o && t.preventDefault()
            return
          }
          (i.handle && !St(d, i.handle, n, !1)) ||
                        this._prepareDragStart(t, c, f)
        }
      }
    },
    _prepareDragStart: function (t, r, n) {
      const i = this
      const o = i.el
      const a = i.options
      const c = o.ownerDocument
      let f
      if (n && !k && n.parentNode === o) {
        const d = qe(n)
        if (
          ((Ne = o),
          (k = n),
          (Ve = k.parentNode),
          (bn = k.nextSibling),
          (Ar = n),
          (wr = a.group),
          (se.dragged = k),
          (mn = {
            target: k,
            clientX: (r || t).clientX,
            clientY: (r || t).clientY
          }),
          (wo = mn.clientX - d.left),
          (xo = mn.clientY - d.top),
          (this._lastX = (r || t).clientX),
          (this._lastY = (r || t).clientY),
          (k.style['will-change'] = 'all'),
          (f = function () {
            if (
              (at('delayEnded', i, { evt: t }), se.eventCanceled)
            ) {
              i._onDrop()
              return
            }
            (i._disableDelayedDragEvents(),
            !mo && i.nativeDraggable && (k.draggable = !0),
            i._triggerDragStart(t, r),
            it({
              sortable: i,
              name: 'choose',
              originalEvent: t
            }),
            ft(k, a.chosenClass, !0))
          }),
          a.ignore.split(',').forEach(function (y) {
            Do(k, y.trim(), ui)
          }),
          Oe(c, 'dragover', gn),
          Oe(c, 'mousemove', gn),
          Oe(c, 'touchmove', gn),
          a.supportPointer
            ? (Oe(c, 'pointerup', i._onDrop),
              !this.nativeDraggable &&
                              Oe(c, 'pointercancel', i._onDrop))
            : (Oe(c, 'mouseup', i._onDrop),
              Oe(c, 'touchend', i._onDrop),
              Oe(c, 'touchcancel', i._onDrop)),
          mo &&
                        this.nativeDraggable &&
                        ((this.options.touchStartThreshold = 4),
                        (k.draggable = !0)),
          at('delayStart', this, { evt: t }),
          a.delay &&
                        (!a.delayOnTouchOnly || r) &&
                        (!this.nativeDraggable || !(er || Wt)))
        ) {
          if (se.eventCanceled) {
            this._onDrop()
            return
          }
          (a.supportPointer
            ? (Oe(c, 'pointerup', i._disableDelayedDrag),
              Oe(c, 'pointercancel', i._disableDelayedDrag))
            : (Oe(c, 'mouseup', i._disableDelayedDrag),
              Oe(c, 'touchend', i._disableDelayedDrag),
              Oe(c, 'touchcancel', i._disableDelayedDrag)),
          Oe(c, 'mousemove', i._delayedDragTouchMoveHandler),
          Oe(c, 'touchmove', i._delayedDragTouchMoveHandler),
          a.supportPointer &&
                            Oe(
                              c,
                              'pointermove',
                              i._delayedDragTouchMoveHandler
                            ),
          (i._dragStartTimer = setTimeout(f, a.delay)))
        } else f()
      }
    },
    _delayedDragTouchMoveHandler: function (t) {
      const r = t.touches ? t.touches[0] : t
      Math.max(
        Math.abs(r.clientX - this._lastX),
        Math.abs(r.clientY - this._lastY)
      ) >=
                Math.floor(
                  this.options.touchStartThreshold /
                        ((this.nativeDraggable && window.devicePixelRatio) ||
                            1)
                ) && this._disableDelayedDrag()
    },
    _disableDelayedDrag: function () {
      (k && ui(k),
      clearTimeout(this._dragStartTimer),
      this._disableDelayedDragEvents())
    },
    _disableDelayedDragEvents: function () {
      const t = this.el.ownerDocument;
      (Ee(t, 'mouseup', this._disableDelayedDrag),
      Ee(t, 'touchend', this._disableDelayedDrag),
      Ee(t, 'touchcancel', this._disableDelayedDrag),
      Ee(t, 'pointerup', this._disableDelayedDrag),
      Ee(t, 'pointercancel', this._disableDelayedDrag),
      Ee(t, 'mousemove', this._delayedDragTouchMoveHandler),
      Ee(t, 'touchmove', this._delayedDragTouchMoveHandler),
      Ee(t, 'pointermove', this._delayedDragTouchMoveHandler))
    },
    _triggerDragStart: function (t, r) {
      ((r = r || (t.pointerType == 'touch' && t)),
      !this.nativeDraggable || r
        ? this.options.supportPointer
          ? Oe(document, 'pointermove', this._onTouchMove)
          : r
            ? Oe(document, 'touchmove', this._onTouchMove)
            : Oe(document, 'mousemove', this._onTouchMove)
        : (Oe(k, 'dragend', this),
          Oe(Ne, 'dragstart', this._onDragStart)))
      try {
        document.selection
          ? Dr(function () {
            document.selection.empty()
          })
          : window.getSelection().removeAllRanges()
      } catch {}
    },
    _dragStarted: function (t, r) {
      if (((In = !1), Ne && k)) {
        (at('dragStarted', this, { evt: r }),
        this.nativeDraggable && Oe(document, 'dragover', us))
        const n = this.options;
        (!t && ft(k, n.dragClass, !1),
        ft(k, n.ghostClass, !0),
        (se.active = this),
        t && this._appendGhost(),
        it({ sortable: this, name: 'start', originalEvent: r }))
      } else this._nulling()
    },
    _emulateDragOver: function () {
      if (Ot) {
        ((this._lastX = Ot.clientX), (this._lastY = Ot.clientY), Fo())
        for (
          var t = document.elementFromPoint(Ot.clientX, Ot.clientY),
            r = t;
          t &&
                    t.shadowRoot &&
                    ((t = t.shadowRoot.elementFromPoint(
                      Ot.clientX,
                      Ot.clientY
                    )),
                    t !== r);

        ) {
          r = t
        }
        if ((k.parentNode[st]._isOutsideThisEl(t), r)) {
          do {
            if (r[st]) {
              let n = void 0
              if (
                ((n = r[st]._onDragOver({
                  clientX: Ot.clientX,
                  clientY: Ot.clientY,
                  target: t,
                  rootEl: r
                })),
                n && !this.options.dragoverBubble)
              ) {
                break
              }
            }
            t = r
          } while ((r = Co(r)))
        }
        ko()
      }
    },
    _onTouchMove: function (t) {
      if (mn) {
        const r = this.options
        const n = r.fallbackTolerance
        const i = r.fallbackOffset
        const o = t.touches ? t.touches[0] : t
        let a = ue && Fn(ue, !0)
        const c = ue && a && a.a
        const f = ue && a && a.d
        const d = Er && nt && yo(nt)
        const y =
                    (o.clientX - mn.clientX + i.x) / (c || 1) +
                    (d ? d[0] - fi[0] : 0) / (c || 1)
        const m =
                    (o.clientY - mn.clientY + i.y) / (f || 1) +
                    (d ? d[1] - fi[1] : 0) / (f || 1)
        if (!se.active && !In) {
          if (
            n &&
                        Math.max(
                          Math.abs(o.clientX - this._lastX),
                          Math.abs(o.clientY - this._lastY)
                        ) < n
          ) {
            return
          }
          this._onDragStart(t, !0)
        }
        if (ue) {
          a
            ? ((a.e += y - (li || 0)), (a.f += m - (ci || 0)))
            : (a = { a: 1, b: 0, c: 0, d: 1, e: y, f: m })
          const O = 'matrix('
            .concat(a.a, ',')
            .concat(a.b, ',')
            .concat(a.c, ',')
            .concat(a.d, ',')
            .concat(a.e, ',')
            .concat(a.f, ')');
          (ae(ue, 'webkitTransform', O),
          ae(ue, 'mozTransform', O),
          ae(ue, 'msTransform', O),
          ae(ue, 'transform', O),
          (li = y),
          (ci = m),
          (Ot = o))
        }
        t.cancelable && t.preventDefault()
      }
    },
    _appendGhost: function () {
      if (!ue) {
        const t = this.options.fallbackOnBody ? document.body : Ne
        const r = qe(k, !0, Er, !0, t)
        const n = this.options
        if (Er) {
          for (
            nt = t;
            ae(nt, 'position') === 'static' &&
                        ae(nt, 'transform') === 'none' &&
                        nt !== document;

          ) {
            nt = nt.parentNode
          }
          (nt !== document.body && nt !== document.documentElement
            ? (nt === document && (nt = Pt()),
              (r.top += nt.scrollTop),
              (r.left += nt.scrollLeft))
            : (nt = Pt()),
          (fi = yo(nt)))
        }
        ((ue = k.cloneNode(!0)),
        ft(ue, n.ghostClass, !1),
        ft(ue, n.fallbackClass, !0),
        ft(ue, n.dragClass, !0),
        ae(ue, 'transition', ''),
        ae(ue, 'transform', ''),
        ae(ue, 'box-sizing', 'border-box'),
        ae(ue, 'margin', 0),
        ae(ue, 'top', r.top),
        ae(ue, 'left', r.left),
        ae(ue, 'width', r.width),
        ae(ue, 'height', r.height),
        ae(ue, 'opacity', '0.8'),
        ae(ue, 'position', Er ? 'absolute' : 'fixed'),
        ae(ue, 'zIndex', '100000'),
        ae(ue, 'pointerEvents', 'none'),
        (se.ghost = ue),
        t.appendChild(ue),
        ae(
          ue,
          'transform-origin',
          (wo / parseInt(ue.style.width)) * 100 +
                            '% ' +
                            (xo / parseInt(ue.style.height)) * 100 +
                            '%'
        ))
      }
    },
    _onDragStart: function (t, r) {
      const n = this
      const i = t.dataTransfer
      const o = n.options
      if ((at('dragStart', this, { evt: t }), se.eventCanceled)) {
        this._onDrop()
        return
      }
      (at('setupClone', this),
      se.eventCanceled ||
                    ((We = Po(k)),
                    We.removeAttribute('id'),
                    (We.draggable = !1),
                    (We.style['will-change'] = ''),
                    this._hideClone(),
                    ft(We, this.options.chosenClass, !1),
                    (se.clone = We)),
      (n.cloneId = Dr(function () {
        (at('clone', n),
        !se.eventCanceled &&
                            (n.options.removeCloneOnHide ||
                                Ne.insertBefore(We, k),
                            n._hideClone(),
                            it({ sortable: n, name: 'clone' })))
      })),
      !r && ft(k, o.dragClass, !0),
      r
        ? ((Pr = !0),
          (n._loopId = setInterval(n._emulateDragOver, 50)))
        : (Ee(document, 'mouseup', n._onDrop),
          Ee(document, 'touchend', n._onDrop),
          Ee(document, 'touchcancel', n._onDrop),
          i &&
                          ((i.effectAllowed = 'move'),
                          o.setData && o.setData.call(n, i, k)),
          Oe(document, 'drop', n),
          ae(k, 'transform', 'translateZ(0)')),
      (In = !0),
      (n._dragStartId = Dr(n._dragStarted.bind(n, r, t))),
      Oe(document, 'selectstart', n),
      (Yn = !0),
      window.getSelection().removeAllRanges(),
      Gn && ae(document.body, 'user-select', 'none'))
    },
    _onDragOver: function (t) {
      const r = this.el
      let n = t.target
      let i
      let o
      let a
      const c = this.options
      const f = c.group
      const d = se.active
      const y = wr === f
      const m = c.sort
      const O = Qe || d
      let E
      const S = this
      let _ = !1
      if (vi) return
      function I (R, Z) {
        at(
          R,
          S,
          Mt(
            {
              evt: t,
              isOwner: y,
              axis: E ? 'vertical' : 'horizontal',
              revert: a,
              dragRect: i,
              targetRect: o,
              canSort: m,
              fromSortable: O,
              target: n,
              completed: A,
              onMove: function (Rt, Ut) {
                return Or(Ne, r, k, i, Rt, qe(Rt), t, Ut)
              },
              changed: N
            },
            Z
          )
        )
      }
      function $ () {
        (I('dragOverAnimationCapture'),
        S.captureAnimationState(),
        S !== O && O.captureAnimationState())
      }
      function A (R) {
        return (
          I('dragOverCompleted', { insertion: R }),
          R &&
                        (y ? d._hideClone() : d._showClone(S),
                        S !== O &&
                            (ft(
                              k,
                              Qe
                                ? Qe.options.ghostClass
                                : d.options.ghostClass,
                              !1
                            ),
                            ft(k, c.ghostClass, !0)),
                        Qe !== S && S !== se.active
                          ? (Qe = S)
                          : S === se.active && Qe && (Qe = null),
                        O === S && (S._ignoreWhileAnimating = n),
                        S.animateAll(function () {
                          (I('dragOverAnimationComplete'),
                          (S._ignoreWhileAnimating = null))
                        }),
                        S !== O &&
                            (O.animateAll(), (O._ignoreWhileAnimating = null))),
          ((n === k && !k.animated) || (n === r && !n.animated)) &&
                        (Rn = null),
          !c.dragoverBubble &&
                        !t.rootEl &&
                        n !== document &&
                        (k.parentNode[st]._isOutsideThisEl(t.target),
                        !R && gn(t)),
          !c.dragoverBubble &&
                        t.stopPropagation &&
                        t.stopPropagation(),
          (_ = !0)
        )
      }
      function N () {
        ((ut = vt(k)),
        (on = vt(k, c.draggable)),
        it({
          sortable: S,
          name: 'change',
          toEl: r,
          newIndex: ut,
          newDraggableIndex: on,
          originalEvent: t
        }))
      }
      if (
        (t.preventDefault !== void 0 &&
                    t.cancelable &&
                    t.preventDefault(),
        (n = St(n, c.draggable, r, !0)),
        I('dragOver'),
        se.eventCanceled)
      ) {
        return _
      }
      if (
        k.contains(t.target) ||
                (n.animated && n.animatingX && n.animatingY) ||
                S._ignoreWhileAnimating === n
      ) {
        return A(!1)
      }
      if (
        ((Pr = !1),
        d &&
                    !c.disabled &&
                    (y
                      ? m || (a = Ve !== Ne)
                      : Qe === this ||
                          ((this.lastPutMode = wr.checkPull(this, d, k, t)) &&
                              f.checkPut(this, d, k, t))))
      ) {
        if (
          ((E = this._getDirection(t, n) === 'vertical'),
          (i = qe(k)),
          I('dragOverValid'),
          se.eventCanceled)
        ) {
          return _
        }
        if (a) {
          return (
            (Ve = Ne),
            $(),
            this._hideClone(),
            I('revert'),
            se.eventCanceled ||
                            (bn ? Ne.insertBefore(k, bn) : Ne.appendChild(k)),
            A(!0)
          )
        }
        const Y = wi(r, c.draggable)
        if (!Y || (vs(t, E, this) && !Y.animated)) {
          if (Y === k) return A(!1)
          if (
            (Y && r === t.target && (n = Y),
            n && (o = qe(n)),
            Or(Ne, r, k, i, n, o, t, !!n) !== !1)
          ) {
            return (
              $(),
              Y && Y.nextSibling
                ? r.insertBefore(k, Y.nextSibling)
                : r.appendChild(k),
              (Ve = r),
              N(),
              A(!0)
            )
          }
        } else if (Y && hs(t, E, this)) {
          const ne = kn(r, 0, c, !0)
          if (ne === k) return A(!1)
          if (
            ((n = ne),
            (o = qe(n)),
            Or(Ne, r, k, i, n, o, t, !1) !== !1)
          ) {
            return (
              $(),
              r.insertBefore(k, ne),
              (Ve = r),
              N(),
              A(!0)
            )
          }
        } else if (n.parentNode === r) {
          o = qe(n)
          let J = 0
          let V
          const de = k.parentNode !== r
          const X = !cs(
            (k.animated && k.toRect) || i,
            (n.animated && n.toRect) || o,
            E
          )
          const Q = E ? 'top' : 'left'
          const me = bo(n, 'top', 'top') || bo(k, 'top', 'top')
          const l = me ? me.scrollTop : void 0;
          (Rn !== n &&
                        ((V = o[Q]),
                        (Zn = !1),
                        (xr = (!X && c.invertSwap) || de)),
          (J = ms(
            t,
            n,
            o,
            E,
            X ? 1 : c.swapThreshold,
            c.invertedSwapThreshold == null
              ? c.swapThreshold
              : c.invertedSwapThreshold,
            xr,
            Rn === n
          )))
          let h
          if (J !== 0) {
            let v = vt(k)
            do ((v -= J), (h = Ve.children[v]))
            while (h && (ae(h, 'display') === 'none' || h === ue))
          }
          if (J === 0 || h === n) return A(!1);
          ((Rn = n), (Qn = J))
          const p = n.nextElementSibling
          let j = !1
          j = J === 1
          const M = Or(Ne, r, k, i, n, o, t, j)
          if (M !== !1) {
            return (
              (M === 1 || M === -1) && (j = M === 1),
              (vi = !0),
              setTimeout(ps, 30),
              $(),
              j && !p
                ? r.appendChild(k)
                : n.parentNode.insertBefore(k, j ? p : n),
              me && To(me, 0, l - me.scrollTop),
              (Ve = k.parentNode),
              V !== void 0 &&
                                !xr &&
                                (Cr = Math.abs(V - qe(n)[Q])),
              N(),
              A(!0)
            )
          }
        }
        if (r.contains(k)) return A(!1)
      }
      return !1
    },
    _ignoreWhileAnimating: null,
    _offMoveEvents: function () {
      (Ee(document, 'mousemove', this._onTouchMove),
      Ee(document, 'touchmove', this._onTouchMove),
      Ee(document, 'pointermove', this._onTouchMove),
      Ee(document, 'dragover', gn),
      Ee(document, 'mousemove', gn),
      Ee(document, 'touchmove', gn))
    },
    _offUpEvents: function () {
      const t = this.el.ownerDocument;
      (Ee(t, 'mouseup', this._onDrop),
      Ee(t, 'touchend', this._onDrop),
      Ee(t, 'pointerup', this._onDrop),
      Ee(t, 'pointercancel', this._onDrop),
      Ee(t, 'touchcancel', this._onDrop),
      Ee(document, 'selectstart', this))
    },
    _onDrop: function (t) {
      const r = this.el
      const n = this.options
      if (
        ((ut = vt(k)),
        (on = vt(k, n.draggable)),
        at('drop', this, { evt: t }),
        (Ve = k && k.parentNode),
        (ut = vt(k)),
        (on = vt(k, n.draggable)),
        se.eventCanceled)
      ) {
        this._nulling()
        return
      }
      ((In = !1),
      (xr = !1),
      (Zn = !1),
      clearInterval(this._loopId),
      clearTimeout(this._dragStartTimer),
      mi(this.cloneId),
      mi(this._dragStartId),
      this.nativeDraggable &&
                    (Ee(document, 'drop', this),
                    Ee(r, 'dragstart', this._onDragStart)),
      this._offMoveEvents(),
      this._offUpEvents(),
      Gn && ae(document.body, 'user-select', ''),
      ae(k, 'transform', ''),
      t &&
                    (Yn &&
                        (t.cancelable && t.preventDefault(),
                        !n.dropBubble && t.stopPropagation()),
                    ue && ue.parentNode && ue.parentNode.removeChild(ue),
                    (Ne === Ve || (Qe && Qe.lastPutMode !== 'clone')) &&
                        We &&
                        We.parentNode &&
                        We.parentNode.removeChild(We),
                    k &&
                        (this.nativeDraggable && Ee(k, 'dragend', this),
                        ui(k),
                        (k.style['will-change'] = ''),
                        Yn &&
                            !In &&
                            ft(
                              k,
                              Qe
                                ? Qe.options.ghostClass
                                : this.options.ghostClass,
                              !1
                            ),
                        ft(k, this.options.chosenClass, !1),
                        it({
                          sortable: this,
                          name: 'unchoose',
                          toEl: Ve,
                          newIndex: null,
                          newDraggableIndex: null,
                          originalEvent: t
                        }),
                        Ne !== Ve
                          ? (ut >= 0 &&
                                  (it({
                                    rootEl: Ve,
                                    name: 'add',
                                    toEl: Ve,
                                    fromEl: Ne,
                                    originalEvent: t
                                  }),
                                  it({
                                    sortable: this,
                                    name: 'remove',
                                    toEl: Ve,
                                    originalEvent: t
                                  }),
                                  it({
                                    rootEl: Ve,
                                    name: 'sort',
                                    toEl: Ve,
                                    fromEl: Ne,
                                    originalEvent: t
                                  }),
                                  it({
                                    sortable: this,
                                    name: 'sort',
                                    toEl: Ve,
                                    originalEvent: t
                                  })),
                            Qe && Qe.save())
                          : ut !== Ln &&
                              ut >= 0 &&
                              (it({
                                sortable: this,
                                name: 'update',
                                toEl: Ve,
                                originalEvent: t
                              }),
                              it({
                                sortable: this,
                                name: 'sort',
                                toEl: Ve,
                                originalEvent: t
                              })),
                        se.active &&
                            ((ut == null || ut === -1) &&
                                ((ut = Ln), (on = Jn)),
                            it({
                              sortable: this,
                              name: 'end',
                              toEl: Ve,
                              originalEvent: t
                            }),
                            this.save()))),
      this._nulling())
    },
    _nulling: function () {
      (at('nulling', this),
      (Ne =
                    k =
                    Ve =
                    ue =
                    bn =
                    We =
                    Ar =
                    an =
                    mn =
                    Ot =
                    Yn =
                    ut =
                    on =
                    Ln =
                    Jn =
                    Rn =
                    Qn =
                    Qe =
                    wr =
                    se.dragged =
                    se.ghost =
                    se.clone =
                    se.active =
                        null),
      Rr.forEach(function (t) {
        t.checked = !0
      }),
      (Rr.length = li = ci = 0))
    },
    handleEvent: function (t) {
      switch (t.type) {
        case 'drop':
        case 'dragend':
          this._onDrop(t)
          break
        case 'dragenter':
        case 'dragover':
          k && (this._onDragOver(t), ds(t))
          break
        case 'selectstart':
          t.preventDefault()
          break
      }
    },
    toArray: function () {
      for (
        var t = [],
          r,
          n = this.el.children,
          i = 0,
          o = n.length,
          a = this.options;
        i < o;
        i++
      ) {
        ((r = n[i]),
        St(r, a.draggable, this.el, !1) &&
                        t.push(r.getAttribute(a.dataIdAttr) || bs(r)))
      }
      return t
    },
    sort: function (t, r) {
      const n = {}
      const i = this.el;
      (this.toArray().forEach(function (o, a) {
        const c = i.children[a]
        St(c, this.options.draggable, i, !1) && (n[o] = c)
      }, this),
      r && this.captureAnimationState(),
      t.forEach(function (o) {
        n[o] && (i.removeChild(n[o]), i.appendChild(n[o]))
      }),
      r && this.animateAll())
    },
    save: function () {
      const t = this.options.store
      t && t.set && t.set(this)
    },
    closest: function (t, r) {
      return St(t, r || this.options.draggable, this.el, !1)
    },
    option: function (t, r) {
      const n = this.options
      if (r === void 0) return n[t]
      const i = tr.modifyOption(this, t, r);
      (typeof i < 'u' ? (n[t] = i) : (n[t] = r), t === 'group' && Lo(n))
    },
    destroy: function () {
      at('destroy', this)
      let t = this.el;
      ((t[st] = null),
      Ee(t, 'mousedown', this._onTapStart),
      Ee(t, 'touchstart', this._onTapStart),
      Ee(t, 'pointerdown', this._onTapStart),
      this.nativeDraggable &&
                    (Ee(t, 'dragover', this), Ee(t, 'dragenter', this)),
      Array.prototype.forEach.call(
        t.querySelectorAll('[draggable]'),
        function (r) {
          r.removeAttribute('draggable')
        }
      ),
      this._onDrop(),
      this._disableDelayedDragEvents(),
      Mr.splice(Mr.indexOf(this.el), 1),
      (this.el = t = null))
    },
    _hideClone: function () {
      if (!an) {
        if ((at('hideClone', this), se.eventCanceled)) return;
        (ae(We, 'display', 'none'),
        this.options.removeCloneOnHide &&
                        We.parentNode &&
                        We.parentNode.removeChild(We),
        (an = !0))
      }
    },
    _showClone: function (t) {
      if (t.lastPutMode !== 'clone') {
        this._hideClone()
        return
      }
      if (an) {
        if ((at('showClone', this), se.eventCanceled)) return;
        (k.parentNode == Ne && !this.options.group.revertClone
          ? Ne.insertBefore(We, k)
          : bn
            ? Ne.insertBefore(We, bn)
            : Ne.appendChild(We),
        this.options.group.revertClone && this.animate(k, We),
        ae(We, 'display', ''),
        (an = !1))
      }
    }
  }
  function ds (e) {
    (e.dataTransfer && (e.dataTransfer.dropEffect = 'move'),
    e.cancelable && e.preventDefault())
  }
  function Or (e, t, r, n, i, o, a, c) {
    let f
    const d = e[st]
    const y = d.options.onMove
    let m
    return (
      window.CustomEvent && !Wt && !er
        ? (f = new CustomEvent('move', { bubbles: !0, cancelable: !0 }))
        : ((f = document.createEvent('Event')),
          f.initEvent('move', !0, !0)),
      (f.to = t),
      (f.from = e),
      (f.dragged = r),
      (f.draggedRect = n),
      (f.related = i || t),
      (f.relatedRect = o || qe(t)),
      (f.willInsertAfter = c),
      (f.originalEvent = a),
      e.dispatchEvent(f),
      y && (m = y.call(d, f, a)),
      m
    )
  }
  function ui (e) {
    e.draggable = !1
  }
  function ps () {
    vi = !1
  }
  function hs (e, t, r) {
    const n = qe(kn(r.el, 0, r.options, !0))
    const i = Mo(r.el, r.options, ue)
    const o = 10
    return t
      ? e.clientX < i.left - o ||
                  (e.clientY < n.top && e.clientX < n.right)
      : e.clientY < i.top - o ||
                  (e.clientY < n.bottom && e.clientX < n.left)
  }
  function vs (e, t, r) {
    const n = qe(wi(r.el, r.options.draggable))
    const i = Mo(r.el, r.options, ue)
    const o = 10
    return t
      ? e.clientX > i.right + o ||
                  (e.clientY > n.bottom && e.clientX > n.left)
      : e.clientY > i.bottom + o ||
                  (e.clientX > n.right && e.clientY > n.top)
  }
  function ms (e, t, r, n, i, o, a, c) {
    const f = n ? e.clientY : e.clientX
    const d = n ? r.height : r.width
    const y = n ? r.top : r.left
    const m = n ? r.bottom : r.right
    let O = !1
    if (!a) {
      if (c && Cr < d * i) {
        if (
          (!Zn &&
                        (Qn === 1
                          ? f > y + (d * o) / 2
                          : f < m - (d * o) / 2) &&
                        (Zn = !0),
          Zn)
        ) {
          O = !0
        } else if (Qn === 1 ? f < y + Cr : f > m - Cr) return -Qn
      } else if (f > y + (d * (1 - i)) / 2 && f < m - (d * (1 - i)) / 2) {
        return gs(t)
      }
    }
    return (
      (O = O || a),
      O && (f < y + (d * o) / 2 || f > m - (d * o) / 2)
        ? f > y + d / 2
          ? 1
          : -1
        : 0
    )
  }
  function gs (e) {
    return vt(k) < vt(e) ? 1 : -1
  }
  function bs (e) {
    for (
      var t = e.tagName + e.className + e.src + e.href + e.textContent,
        r = t.length,
        n = 0;
      r--;

    ) {
      n += t.charCodeAt(r)
    }
    return n.toString(36)
  }
  function ys (e) {
    Rr.length = 0
    for (let t = e.getElementsByTagName('input'), r = t.length; r--;) {
      const n = t[r]
      n.checked && Rr.push(n)
    }
  }
  function Dr (e) {
    return setTimeout(e, 0)
  }
  function mi (e) {
    return clearTimeout(e)
  }
  Lr &&
        Oe(document, 'touchmove', function (e) {
          (se.active || In) && e.cancelable && e.preventDefault()
        })
  se.utils = {
    on: Oe,
    off: Ee,
    css: ae,
    find: Do,
    is: function (t, r) {
      return !!St(t, r, t, !1)
    },
    extend: ts,
    throttle: _o,
    closest: St,
    toggleClass: ft,
    clone: Po,
    index: vt,
    nextTick: Dr,
    cancelNextTick: mi,
    detectDirection: Io,
    getChild: kn,
    expando: st
  }
  se.get = function (e) {
    return e[st]
  }
  se.mount = function () {
    for (var e = arguments.length, t = new Array(e), r = 0; r < e; r++) {
      t[r] = arguments[r]
    }
    (t[0].constructor === Array && (t = t[0]),
    t.forEach(function (n) {
      if (!n.prototype || !n.prototype.constructor) {
        throw 'Sortable: Mounted plugin must be a constructor function, not '.concat(
          {}.toString.call(n)
        )
      }
      (n.utils && (se.utils = Mt(Mt({}, se.utils), n.utils)),
      tr.mount(n))
    }))
  }
  se.create = function (e, t) {
    return new se(e, t)
  }
  se.version = Za
  let Xe = []
  let Xn
  let gi
  let bi = !1
  let di
  let pi
  let Ir
  let qn
  function ws () {
    function e () {
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
      (e.prototype = {
        dragStarted: function (r) {
          const n = r.originalEvent
          this.sortable.nativeDraggable
            ? Oe(document, 'dragover', this._handleAutoScroll)
            : this.options.supportPointer
              ? Oe(
                document,
                'pointermove',
                this._handleFallbackAutoScroll
              )
              : n.touches
                ? Oe(
                  document,
                  'touchmove',
                  this._handleFallbackAutoScroll
                )
                : Oe(
                  document,
                  'mousemove',
                  this._handleFallbackAutoScroll
                )
        },
        dragOverCompleted: function (r) {
          const n = r.originalEvent
          !this.options.dragOverBubble &&
                        !n.rootEl &&
                        this._handleAutoScroll(n)
        },
        drop: function () {
          (this.sortable.nativeDraggable
            ? Ee(document, 'dragover', this._handleAutoScroll)
            : (Ee(
                document,
                'pointermove',
                this._handleFallbackAutoScroll
              ),
              Ee(
                document,
                'touchmove',
                this._handleFallbackAutoScroll
              ),
              Ee(
                document,
                'mousemove',
                this._handleFallbackAutoScroll
              )),
          Oo(),
          _r(),
          ns())
        },
        nulling: function () {
          ((Ir = gi = Xn = bi = qn = di = pi = null),
          (Xe.length = 0))
        },
        _handleFallbackAutoScroll: function (r) {
          this._handleAutoScroll(r, !0)
        },
        _handleAutoScroll: function (r, n) {
          const i = this
          const o = (r.touches ? r.touches[0] : r).clientX
          const a = (r.touches ? r.touches[0] : r).clientY
          const c = document.elementFromPoint(o, a)
          if (
            ((Ir = r),
            n ||
                            this.options.forceAutoScrollFallback ||
                            er ||
                            Wt ||
                            Gn)
          ) {
            hi(r, this.options, c, n)
            let f = sn(c, !0)
            bi &&
                            (!qn || o !== di || a !== pi) &&
                            (qn && Oo(),
                            (qn = setInterval(function () {
                              const d = sn(
                                document.elementFromPoint(o, a),
                                !0
                              );
                              (d !== f && ((f = d), _r()),
                              hi(r, i.options, d, n))
                            }, 10)),
                            (di = o),
                            (pi = a))
          } else {
            if (!this.options.bubbleScroll || sn(c, !0) === Pt()) {
              _r()
              return
            }
            hi(r, this.options, sn(c, !1), !1)
          }
        }
      }),
      $t(e, { pluginName: 'scroll', initializeByDefault: !0 })
    )
  }
  function _r () {
    (Xe.forEach(function (e) {
      clearInterval(e.pid)
    }),
    (Xe = []))
  }
  function Oo () {
    clearInterval(qn)
  }
  var hi = _o(function (e, t, r, n) {
    if (t.scroll) {
      const i = (e.touches ? e.touches[0] : e).clientX
      const o = (e.touches ? e.touches[0] : e).clientY
      const a = t.scrollSensitivity
      const c = t.scrollSpeed
      const f = Pt()
      let d = !1
      let y
      gi !== r &&
                ((gi = r),
                _r(),
                (Xn = t.scroll),
                (y = t.scrollFn),
                Xn === !0 && (Xn = sn(r, !0)))
      let m = 0
      let O = Xn
      do {
        const E = O
        const S = qe(E)
        const _ = S.top
        const I = S.bottom
        const $ = S.left
        const A = S.right
        const N = S.width
        const Y = S.height
        let ne = void 0
        let J = void 0
        const V = E.scrollWidth
        const de = E.scrollHeight
        const X = ae(E)
        const Q = E.scrollLeft
        const me = E.scrollTop
        E === f
          ? ((ne =
                          N < V &&
                          (X.overflowX === 'auto' ||
                              X.overflowX === 'scroll' ||
                              X.overflowX === 'visible')),
            (J =
                          Y < de &&
                          (X.overflowY === 'auto' ||
                              X.overflowY === 'scroll' ||
                              X.overflowY === 'visible')))
          : ((ne =
                          N < V &&
                          (X.overflowX === 'auto' || X.overflowX === 'scroll')),
            (J =
                          Y < de &&
                          (X.overflowY === 'auto' ||
                              X.overflowY === 'scroll')))
        const l =
                    ne &&
                    (Math.abs(A - i) <= a && Q + N < V) -
                        (Math.abs($ - i) <= a && !!Q)
        const h =
                    J &&
                    (Math.abs(I - o) <= a && me + Y < de) -
                        (Math.abs(_ - o) <= a && !!me)
        if (!Xe[m]) {
          for (let v = 0; v <= m; v++) Xe[v] || (Xe[v] = {})
        }
        ((Xe[m].vx != l || Xe[m].vy != h || Xe[m].el !== E) &&
                    ((Xe[m].el = E),
                    (Xe[m].vx = l),
                    (Xe[m].vy = h),
                    clearInterval(Xe[m].pid),
                    (l != 0 || h != 0) &&
                        ((d = !0),
                        (Xe[m].pid = setInterval(
                          function () {
                            n &&
                                    this.layer === 0 &&
                                    se.active._onTouchMove(Ir)
                            const p = Xe[this.layer].vy
                              ? Xe[this.layer].vy * c
                              : 0
                            const j = Xe[this.layer].vx
                              ? Xe[this.layer].vx * c
                              : 0;
                            (typeof y === 'function' &&
                                    y.call(
                                      se.dragged.parentNode[st],
                                      j,
                                      p,
                                      e,
                                      Ir,
                                      Xe[this.layer].el
                                    ) !== 'continue') ||
                                    To(Xe[this.layer].el, j, p)
                          }.bind({ layer: m }),
                          24
                        )))),
        m++)
      } while (t.bubbleScroll && O !== f && (O = sn(O, !1)))
      bi = d
    }
  }, 30)
  const No = function (t) {
    const r = t.originalEvent
    const n = t.putSortable
    const i = t.dragEl
    const o = t.activeSortable
    const a = t.dispatchSortableEvent
    const c = t.hideGhostForTarget
    const f = t.unhideGhostForTarget
    if (r) {
      const d = n || o
      c()
      const y =
                r.changedTouches && r.changedTouches.length
                  ? r.changedTouches[0]
                  : r
      const m = document.elementFromPoint(y.clientX, y.clientY);
      (f(),
      d &&
                    !d.el.contains(m) &&
                    (a('spill'), this.onSpill({ dragEl: i, putSortable: n })))
    }
  }
  function xi () {}
  xi.prototype = {
    startIndex: null,
    dragStart: function (t) {
      const r = t.oldDraggableIndex
      this.startIndex = r
    },
    onSpill: function (t) {
      const r = t.dragEl
      const n = t.putSortable;
      (this.sortable.captureAnimationState(),
      n && n.captureAnimationState())
      const i = kn(this.sortable.el, this.startIndex, this.options);
      (i
        ? this.sortable.el.insertBefore(r, i)
        : this.sortable.el.appendChild(r),
      this.sortable.animateAll(),
      n && n.animateAll())
    },
    drop: No
  }
  $t(xi, { pluginName: 'revertOnSpill' })
  function Ei () {}
  Ei.prototype = {
    onSpill: function (t) {
      const r = t.dragEl
      const n = t.putSortable
      const i = n || this.sortable;
      (i.captureAnimationState(),
      r.parentNode && r.parentNode.removeChild(r),
      i.animateAll())
    },
    drop: No
  }
  $t(Ei, { pluginName: 'removeOnSpill' })
  se.mount(new ws())
  se.mount(Ei, xi)
  const Oi = se
  window.Sortable = Oi
  const jo = (e) => {
    e.directive('sortable', (t) => {
      let r = parseInt(t.dataset?.sortableAnimationDuration);
      (r !== 0 && !r && (r = 300),
      (t.sortable = Oi.create(t, {
        group: t.getAttribute('x-sortable-group'),
        draggable: '[x-sortable-item]',
        handle: '[x-sortable-handle]',
        dataIdAttr: 'x-sortable-item',
        animation: r,
        ghostClass: 'fi-sortable-ghost',
        onEnd (n) {
          const {
            item: i,
            to: o,
            oldDraggableIndex: a,
            newDraggableIndex: c
          } = n
          if (a === c) return
          const f = this.options.draggable
          const d = o.querySelectorAll(`:scope > ${f}`)[c - 1]
          d && o.insertBefore(i, d.nextSibling)
        }
      })))
    })
  }
  const xs = Object.create
  const Ci = Object.defineProperty
  const Es = Object.getPrototypeOf
  const Os = Object.prototype.hasOwnProperty
  const Ss = Object.getOwnPropertyNames
  const As = Object.getOwnPropertyDescriptor
  const Cs = (e) => Ci(e, '__esModule', { value: !0 })
  const Bo = (e, t) => () => (
    t || ((t = { exports: {} }), e(t.exports, t)),
    t.exports
  )
  const Ds = (e, t, r) => {
    if ((t && typeof t === 'object') || typeof t === 'function') {
      for (const n of Ss(t)) {
        !Os.call(e, n) &&
                    n !== 'default' &&
                    Ci(e, n, {
                      get: () => t[n],
                      enumerable: !(r = As(t, n)) || r.enumerable
                    })
      }
    }
    return e
  }
  const Ho = (e) =>
    Ds(
      Cs(
        Ci(
          e != null ? xs(Es(e)) : {},
          'default',
          e && e.__esModule && 'default' in e
            ? { get: () => e.default, enumerable: !0 }
            : { value: e, enumerable: !0 }
        )
      ),
      e
    )
  const _s = Bo((e) => {
    'use strict'
    Object.defineProperty(e, '__esModule', { value: !0 })
    function t (u) {
      const s = u.getBoundingClientRect()
      return {
        width: s.width,
        height: s.height,
        top: s.top,
        right: s.right,
        bottom: s.bottom,
        left: s.left,
        x: s.left,
        y: s.top
      }
    }
    function r (u) {
      if (u == null) return window
      if (u.toString() !== '[object Window]') {
        const s = u.ownerDocument
        return (s && s.defaultView) || window
      }
      return u
    }
    function n (u) {
      const s = r(u)
      const b = s.pageXOffset
      const T = s.pageYOffset
      return { scrollLeft: b, scrollTop: T }
    }
    function i (u) {
      const s = r(u).Element
      return u instanceof s || u instanceof Element
    }
    function o (u) {
      const s = r(u).HTMLElement
      return u instanceof s || u instanceof HTMLElement
    }
    function a (u) {
      if (typeof ShadowRoot > 'u') return !1
      const s = r(u).ShadowRoot
      return u instanceof s || u instanceof ShadowRoot
    }
    function c (u) {
      return { scrollLeft: u.scrollLeft, scrollTop: u.scrollTop }
    }
    function f (u) {
      return u === r(u) || !o(u) ? n(u) : c(u)
    }
    function d (u) {
      return u ? (u.nodeName || '').toLowerCase() : null
    }
    function y (u) {
      return ((i(u) ? u.ownerDocument : u.document) || window.document)
        .documentElement
    }
    function m (u) {
      return t(y(u)).left + n(u).scrollLeft
    }
    function O (u) {
      return r(u).getComputedStyle(u)
    }
    function E (u) {
      const s = O(u)
      const b = s.overflow
      const T = s.overflowX
      const P = s.overflowY
      return /auto|scroll|overlay|hidden/.test(b + P + T)
    }
    function S (u, s, b) {
      b === void 0 && (b = !1)
      const T = y(s)
      const P = t(u)
      const F = o(s)
      let U = { scrollLeft: 0, scrollTop: 0 }
      let H = { x: 0, y: 0 }
      return (
        (F || (!F && !b)) &&
                    ((d(s) !== 'body' || E(T)) && (U = f(s)),
                    o(s)
                      ? ((H = t(s)),
                        (H.x += s.clientLeft),
                        (H.y += s.clientTop))
                      : T && (H.x = m(T))),
        {
          x: P.left + U.scrollLeft - H.x,
          y: P.top + U.scrollTop - H.y,
          width: P.width,
          height: P.height
        }
      )
    }
    function _ (u) {
      const s = t(u)
      let b = u.offsetWidth
      let T = u.offsetHeight
      return (
        Math.abs(s.width - b) <= 1 && (b = s.width),
        Math.abs(s.height - T) <= 1 && (T = s.height),
        { x: u.offsetLeft, y: u.offsetTop, width: b, height: T }
      )
    }
    function I (u) {
      return d(u) === 'html'
        ? u
        : u.assignedSlot ||
                      u.parentNode ||
                      (a(u) ? u.host : null) ||
                      y(u)
    }
    function $ (u) {
      return ['html', 'body', '#document'].indexOf(d(u)) >= 0
        ? u.ownerDocument.body
        : o(u) && E(u)
          ? u
          : $(I(u))
    }
    function A (u, s) {
      let b
      s === void 0 && (s = [])
      const T = $(u)
      const P = T === ((b = u.ownerDocument) == null ? void 0 : b.body)
      const F = r(T)
      const U = P ? [F].concat(F.visualViewport || [], E(T) ? T : []) : T
      const H = s.concat(U)
      return P ? H : H.concat(A(I(U)))
    }
    function N (u) {
      return ['table', 'td', 'th'].indexOf(d(u)) >= 0
    }
    function Y (u) {
      return !o(u) || O(u).position === 'fixed' ? null : u.offsetParent
    }
    function ne (u) {
      const s =
                navigator.userAgent.toLowerCase().indexOf('firefox') !== -1
      const b = navigator.userAgent.indexOf('Trident') !== -1
      if (b && o(u)) {
        const T = O(u)
        if (T.position === 'fixed') return null
      }
      for (let P = I(u); o(P) && ['html', 'body'].indexOf(d(P)) < 0;) {
        const F = O(P)
        if (
          F.transform !== 'none' ||
                    F.perspective !== 'none' ||
                    F.contain === 'paint' ||
                    ['transform', 'perspective'].indexOf(F.willChange) !== -1 ||
                    (s && F.willChange === 'filter') ||
                    (s && F.filter && F.filter !== 'none')
        ) {
          return P
        }
        P = P.parentNode
      }
      return null
    }
    function J (u) {
      for (
        var s = r(u), b = Y(u);
        b && N(b) && O(b).position === 'static';

      ) {
        b = Y(b)
      }
      return b &&
                (d(b) === 'html' ||
                    (d(b) === 'body' && O(b).position === 'static'))
        ? s
        : b || ne(u) || s
    }
    const V = 'top'
    const de = 'bottom'
    const X = 'right'
    const Q = 'left'
    const me = 'auto'
    const l = [V, de, X, Q]
    const h = 'start'
    const v = 'end'
    const p = 'clippingParents'
    const j = 'viewport'
    const M = 'popper'
    const R = 'reference'
    const Z = l.reduce(function (u, s) {
      return u.concat([s + '-' + h, s + '-' + v])
    }, [])
    const ze = [].concat(l, [me]).reduce(function (u, s) {
      return u.concat([s, s + '-' + h, s + '-' + v])
    }, [])
    const Rt = 'beforeRead'
    const Ut = 'read'
    const Fr = 'afterRead'
    const kr = 'beforeMain'
    const Nr = 'main'
    const Vt = 'afterMain'
    const nr = 'beforeWrite'
    const jr = 'write'
    const rr = 'afterWrite'
    const It = [Rt, Ut, Fr, kr, Nr, Vt, nr, jr, rr]
    function Br (u) {
      const s = new Map()
      const b = new Set()
      const T = []
      u.forEach(function (F) {
        s.set(F.name, F)
      })
      function P (F) {
        b.add(F.name)
        const U = [].concat(F.requires || [], F.requiresIfExists || []);
        (U.forEach(function (H) {
          if (!b.has(H)) {
            const G = s.get(H)
            G && P(G)
          }
        }),
        T.push(F))
      }
      return (
        u.forEach(function (F) {
          b.has(F.name) || P(F)
        }),
        T
      )
    }
    function mt (u) {
      const s = Br(u)
      return It.reduce(function (b, T) {
        return b.concat(
          s.filter(function (P) {
            return P.phase === T
          })
        )
      }, [])
    }
    function zt (u) {
      let s
      return function () {
        return (
          s ||
                        (s = new Promise(function (b) {
                          Promise.resolve().then(function () {
                            ((s = void 0), b(u()))
                          })
                        })),
          s
        )
      }
    }
    function At (u) {
      for (
        var s = arguments.length,
          b = new Array(s > 1 ? s - 1 : 0),
          T = 1;
        T < s;
        T++
      ) {
        b[T - 1] = arguments[T]
      }
      return [].concat(b).reduce(function (P, F) {
        return P.replace(/%s/, F)
      }, u)
    }
    const Ct =
            'Popper: modifier "%s" provided an invalid %s property, expected %s but got %s'
    const Hr =
            'Popper: modifier "%s" requires "%s", but "%s" modifier is not available'
    const Ze = [
      'name',
      'enabled',
      'phase',
      'fn',
      'effect',
      'requires',
      'options'
    ]
    function $r (u) {
      u.forEach(function (s) {
        Object.keys(s).forEach(function (b) {
          switch (b) {
            case 'name':
              typeof s.name !== 'string' &&
                                console.error(
                                  At(
                                    Ct,
                                    String(s.name),
                                    '"name"',
                                    '"string"',
                                    '"' + String(s.name) + '"'
                                  )
                                )
              break
            case 'enabled':
              typeof s.enabled !== 'boolean' &&
                                console.error(
                                  At(
                                    Ct,
                                    s.name,
                                    '"enabled"',
                                    '"boolean"',
                                    '"' + String(s.enabled) + '"'
                                  )
                                )
            case 'phase':
              It.indexOf(s.phase) < 0 &&
                                console.error(
                                  At(
                                    Ct,
                                    s.name,
                                    '"phase"',
                                    'either ' + It.join(', '),
                                    '"' + String(s.phase) + '"'
                                  )
                                )
              break
            case 'fn':
              typeof s.fn !== 'function' &&
                                console.error(
                                  At(
                                    Ct,
                                    s.name,
                                    '"fn"',
                                    '"function"',
                                    '"' + String(s.fn) + '"'
                                  )
                                )
              break
            case 'effect':
              typeof s.effect !== 'function' &&
                                console.error(
                                  At(
                                    Ct,
                                    s.name,
                                    '"effect"',
                                    '"function"',
                                    '"' + String(s.fn) + '"'
                                  )
                                )
              break
            case 'requires':
              Array.isArray(s.requires) ||
                                console.error(
                                  At(
                                    Ct,
                                    s.name,
                                    '"requires"',
                                    '"array"',
                                    '"' + String(s.requires) + '"'
                                  )
                                )
              break
            case 'requiresIfExists':
              Array.isArray(s.requiresIfExists) ||
                                console.error(
                                  At(
                                    Ct,
                                    s.name,
                                    '"requiresIfExists"',
                                    '"array"',
                                    '"' + String(s.requiresIfExists) + '"'
                                  )
                                )
              break
            case 'options':
            case 'data':
              break
            default:
              console.error(
                'PopperJS: an invalid property has been provided to the "' +
                                    s.name +
                                    '" modifier, valid properties are ' +
                                    Ze.map(function (T) {
                                      return '"' + T + '"'
                                    }).join(', ') +
                                    '; but "' +
                                    b +
                                    '" was provided.'
              )
          }
          s.requires &&
                        s.requires.forEach(function (T) {
                          u.find(function (P) {
                            return P.name === T
                          }) == null &&
                                console.error(At(Hr, String(s.name), T, T))
                        })
        })
      })
    }
    function Wr (u, s) {
      const b = new Set()
      return u.filter(function (T) {
        const P = s(T)
        if (!b.has(P)) return (b.add(P), !0)
      })
    }
    function ot (u) {
      return u.split('-')[0]
    }
    function Ur (u) {
      const s = u.reduce(function (b, T) {
        const P = b[T.name]
        return (
          (b[T.name] = P
            ? Object.assign({}, P, T, {
              options: Object.assign({}, P.options, T.options),
              data: Object.assign({}, P.data, T.data)
            })
            : T),
          b
        )
      }, {})
      return Object.keys(s).map(function (b) {
        return s[b]
      })
    }
    function ir (u) {
      const s = r(u)
      const b = y(u)
      const T = s.visualViewport
      let P = b.clientWidth
      let F = b.clientHeight
      let U = 0
      let H = 0
      return (
        T &&
                    ((P = T.width),
                    (F = T.height),
                    /^((?!chrome|android).)*safari/i.test(
                      navigator.userAgent
                    ) || ((U = T.offsetLeft), (H = T.offsetTop))),
        { width: P, height: F, x: U + m(u), y: H }
      )
    }
    const gt = Math.max
    const ln = Math.min
    const Yt = Math.round
    function or (u) {
      let s
      const b = y(u)
      const T = n(u)
      const P = (s = u.ownerDocument) == null ? void 0 : s.body
      const F = gt(
        b.scrollWidth,
        b.clientWidth,
        P ? P.scrollWidth : 0,
        P ? P.clientWidth : 0
      )
      const U = gt(
        b.scrollHeight,
        b.clientHeight,
        P ? P.scrollHeight : 0,
        P ? P.clientHeight : 0
      )
      let H = -T.scrollLeft + m(u)
      const G = -T.scrollTop
      return (
        O(P || b).direction === 'rtl' &&
                    (H += gt(b.clientWidth, P ? P.clientWidth : 0) - F),
        { width: F, height: U, x: H, y: G }
      )
    }
    function Nn (u, s) {
      const b = s.getRootNode && s.getRootNode()
      if (u.contains(s)) return !0
      if (b && a(b)) {
        let T = s
        do {
          if (T && u.isSameNode(T)) return !0
          T = T.parentNode || T.host
        } while (T)
      }
      return !1
    }
    function Xt (u) {
      return Object.assign({}, u, {
        left: u.x,
        top: u.y,
        right: u.x + u.width,
        bottom: u.y + u.height
      })
    }
    function ar (u) {
      const s = t(u)
      return (
        (s.top = s.top + u.clientTop),
        (s.left = s.left + u.clientLeft),
        (s.bottom = s.top + u.clientHeight),
        (s.right = s.left + u.clientWidth),
        (s.width = u.clientWidth),
        (s.height = u.clientHeight),
        (s.x = s.left),
        (s.y = s.top),
        s
      )
    }
    function sr (u, s) {
      return s === j ? Xt(ir(u)) : o(s) ? ar(s) : Xt(or(y(u)))
    }
    function yn (u) {
      const s = A(I(u))
      const b = ['absolute', 'fixed'].indexOf(O(u).position) >= 0
      const T = b && o(u) ? J(u) : u
      return i(T)
        ? s.filter(function (P) {
          return i(P) && Nn(P, T) && d(P) !== 'body'
        })
        : []
    }
    function wn (u, s, b) {
      const T = s === 'clippingParents' ? yn(u) : [].concat(s)
      const P = [].concat(T, [b])
      const F = P[0]
      const U = P.reduce(
        function (H, G) {
          const oe = sr(u, G)
          return (
            (H.top = gt(oe.top, H.top)),
            (H.right = ln(oe.right, H.right)),
            (H.bottom = ln(oe.bottom, H.bottom)),
            (H.left = gt(oe.left, H.left)),
            H
          )
        },
        sr(u, F)
      )
      return (
        (U.width = U.right - U.left),
        (U.height = U.bottom - U.top),
        (U.x = U.left),
        (U.y = U.top),
        U
      )
    }
    function cn (u) {
      return u.split('-')[1]
    }
    function dt (u) {
      return ['top', 'bottom'].indexOf(u) >= 0 ? 'x' : 'y'
    }
    function lr (u) {
      const s = u.reference
      const b = u.element
      const T = u.placement
      const P = T ? ot(T) : null
      const F = T ? cn(T) : null
      const U = s.x + s.width / 2 - b.width / 2
      const H = s.y + s.height / 2 - b.height / 2
      let G
      switch (P) {
        case V:
          G = { x: U, y: s.y - b.height }
          break
        case de:
          G = { x: U, y: s.y + s.height }
          break
        case X:
          G = { x: s.x + s.width, y: H }
          break
        case Q:
          G = { x: s.x - b.width, y: H }
          break
        default:
          G = { x: s.x, y: s.y }
      }
      const oe = P ? dt(P) : null
      if (oe != null) {
        const z = oe === 'y' ? 'height' : 'width'
        switch (F) {
          case h:
            G[oe] = G[oe] - (s[z] / 2 - b[z] / 2)
            break
          case v:
            G[oe] = G[oe] + (s[z] / 2 - b[z] / 2)
            break
        }
      }
      return G
    }
    function cr () {
      return { top: 0, right: 0, bottom: 0, left: 0 }
    }
    function fr (u) {
      return Object.assign({}, cr(), u)
    }
    function ur (u, s) {
      return s.reduce(function (b, T) {
        return ((b[T] = u), b)
      }, {})
    }
    function qt (u, s) {
      s === void 0 && (s = {})
      const b = s
      const T = b.placement
      const P = T === void 0 ? u.placement : T
      const F = b.boundary
      const U = F === void 0 ? p : F
      const H = b.rootBoundary
      const G = H === void 0 ? j : H
      const oe = b.elementContext
      const z = oe === void 0 ? M : oe
      const De = b.altBoundary
      const Fe = De === void 0 ? !1 : De
      const Ce = b.padding
      const xe = Ce === void 0 ? 0 : Ce
      const Me = fr(typeof xe !== 'number' ? xe : ur(xe, l))
      const Se = z === M ? R : M
      const Be = u.elements.reference
      const Re = u.rects.popper
      const He = u.elements[Fe ? Se : z]
      const ce = wn(
        i(He) ? He : He.contextElement || y(u.elements.popper),
        U,
        G
      )
      const Pe = t(Be)
      const _e = lr({
        reference: Pe,
        element: Re,
        strategy: 'absolute',
        placement: P
      })
      const ke = Xt(Object.assign({}, Re, _e))
      const Le = z === M ? ke : Pe
      const Ye = {
        top: ce.top - Le.top + Me.top,
        bottom: Le.bottom - ce.bottom + Me.bottom,
        left: ce.left - Le.left + Me.left,
        right: Le.right - ce.right + Me.right
      }
      const $e = u.modifiersData.offset
      if (z === M && $e) {
        const Ue = $e[P]
        Object.keys(Ye).forEach(function (wt) {
          const et = [X, de].indexOf(wt) >= 0 ? 1 : -1
          const Ft = [V, de].indexOf(wt) >= 0 ? 'y' : 'x'
          Ye[wt] += Ue[Ft] * et
        })
      }
      return Ye
    }
    const dr =
            'Popper: Invalid reference or popper argument provided. They must be either a DOM element or virtual element.'
    const Vr =
            'Popper: An infinite loop in the modifiers cycle has been detected! The cycle has been interrupted to prevent a browser crash.'
    const xn = {
      placement: 'bottom',
      modifiers: [],
      strategy: 'absolute'
    }
    function fn () {
      for (
        var u = arguments.length, s = new Array(u), b = 0;
        b < u;
        b++
      ) {
        s[b] = arguments[b]
      }
      return !s.some(function (T) {
        return !(T && typeof T.getBoundingClientRect === 'function')
      })
    }
    function En (u) {
      u === void 0 && (u = {})
      const s = u
      const b = s.defaultModifiers
      const T = b === void 0 ? [] : b
      const P = s.defaultOptions
      const F = P === void 0 ? xn : P
      return function (H, G, oe) {
        oe === void 0 && (oe = F)
        let z = {
          placement: 'bottom',
          orderedModifiers: [],
          options: Object.assign({}, xn, F),
          modifiersData: {},
          elements: { reference: H, popper: G },
          attributes: {},
          styles: {}
        }
        let De = []
        let Fe = !1
        var Ce = {
          state: z,
          setOptions: function (Be) {
            (Me(),
            (z.options = Object.assign({}, F, z.options, Be)),
            (z.scrollParents = {
              reference: i(H)
                ? A(H)
                : H.contextElement
                  ? A(H.contextElement)
                  : [],
              popper: A(G)
            }))
            const Re = mt(Ur([].concat(T, z.options.modifiers)))
            z.orderedModifiers = Re.filter(function ($e) {
              return $e.enabled
            })
            const He = Wr(
              [].concat(Re, z.options.modifiers),
              function ($e) {
                const Ue = $e.name
                return Ue
              }
            )
            if (($r(He), ot(z.options.placement) === me)) {
              const ce = z.orderedModifiers.find(function ($e) {
                const Ue = $e.name
                return Ue === 'flip'
              })
              ce ||
                                console.error(
                                  [
                                    'Popper: "auto" placements require the "flip" modifier be',
                                    'present and enabled to work.'
                                  ].join(' ')
                                )
            }
            const Pe = O(G)
            const _e = Pe.marginTop
            const ke = Pe.marginRight
            const Le = Pe.marginBottom
            const Ye = Pe.marginLeft
            return (
              [_e, ke, Le, Ye].some(function ($e) {
                return parseFloat($e)
              }) &&
                                console.warn(
                                  [
                                    'Popper: CSS "margin" styles cannot be used to apply padding',
                                    'between the popper and its reference element or boundary.',
                                    'To replicate margin, use the `offset` modifier, as well as',
                                    'the `padding` option in the `preventOverflow` and `flip`',
                                    'modifiers.'
                                  ].join(' ')
                                ),
              xe(),
              Ce.update()
            )
          },
          forceUpdate: function () {
            if (!Fe) {
              const Be = z.elements
              const Re = Be.reference
              const He = Be.popper
              if (!fn(Re, He)) {
                console.error(dr)
                return
              }
              ((z.rects = {
                reference: S(
                  Re,
                  J(He),
                  z.options.strategy === 'fixed'
                ),
                popper: _(He)
              }),
              (z.reset = !1),
              (z.placement = z.options.placement),
              z.orderedModifiers.forEach(function (Ue) {
                return (z.modifiersData[Ue.name] =
                                        Object.assign({}, Ue.data))
              }))
              for (
                let ce = 0, Pe = 0;
                Pe < z.orderedModifiers.length;
                Pe++
              ) {
                if (((ce += 1), ce > 100)) {
                  console.error(Vr)
                  break
                }
                if (z.reset === !0) {
                  ((z.reset = !1), (Pe = -1))
                  continue
                }
                const _e = z.orderedModifiers[Pe]
                const ke = _e.fn
                const Le = _e.options
                const Ye = Le === void 0 ? {} : Le
                const $e = _e.name
                typeof ke === 'function' &&
                                    (z =
                                        ke({
                                          state: z,
                                          options: Ye,
                                          name: $e,
                                          instance: Ce
                                        }) || z)
              }
            }
          },
          update: zt(function () {
            return new Promise(function (Se) {
              (Ce.forceUpdate(), Se(z))
            })
          }),
          destroy: function () {
            (Me(), (Fe = !0))
          }
        }
        if (!fn(H, G)) return (console.error(dr), Ce)
        Ce.setOptions(oe).then(function (Se) {
          !Fe && oe.onFirstUpdate && oe.onFirstUpdate(Se)
        })
        function xe () {
          z.orderedModifiers.forEach(function (Se) {
            const Be = Se.name
            const Re = Se.options
            const He = Re === void 0 ? {} : Re
            const ce = Se.effect
            if (typeof ce === 'function') {
              const Pe = ce({
                state: z,
                name: Be,
                instance: Ce,
                options: He
              })
              const _e = function () {}
              De.push(Pe || _e)
            }
          })
        }
        function Me () {
          (De.forEach(function (Se) {
            return Se()
          }),
          (De = []))
        }
        return Ce
      }
    }
    const On = { passive: !0 }
    function zr (u) {
      const s = u.state
      const b = u.instance
      const T = u.options
      const P = T.scroll
      const F = P === void 0 ? !0 : P
      const U = T.resize
      const H = U === void 0 ? !0 : U
      const G = r(s.elements.popper)
      const oe = [].concat(
        s.scrollParents.reference,
        s.scrollParents.popper
      )
      return (
        F &&
                    oe.forEach(function (z) {
                      z.addEventListener('scroll', b.update, On)
                    }),
        H && G.addEventListener('resize', b.update, On),
        function () {
          (F &&
                        oe.forEach(function (z) {
                          z.removeEventListener('scroll', b.update, On)
                        }),
          H && G.removeEventListener('resize', b.update, On))
        }
      )
    }
    const jn = {
      name: 'eventListeners',
      enabled: !0,
      phase: 'write',
      fn: function () {},
      effect: zr,
      data: {}
    }
    function Yr (u) {
      const s = u.state
      const b = u.name
      s.modifiersData[b] = lr({
        reference: s.rects.reference,
        element: s.rects.popper,
        strategy: 'absolute',
        placement: s.placement
      })
    }
    const Bn = {
      name: 'popperOffsets',
      enabled: !0,
      phase: 'read',
      fn: Yr,
      data: {}
    }
    const Xr = {
      top: 'auto',
      right: 'auto',
      bottom: 'auto',
      left: 'auto'
    }
    function qr (u) {
      const s = u.x
      const b = u.y
      const T = window
      const P = T.devicePixelRatio || 1
      return { x: Yt(Yt(s * P) / P) || 0, y: Yt(Yt(b * P) / P) || 0 }
    }
    function Hn (u) {
      let s
      const b = u.popper
      const T = u.popperRect
      const P = u.placement
      const F = u.offsets
      const U = u.position
      const H = u.gpuAcceleration
      const G = u.adaptive
      const oe = u.roundOffsets
      const z = oe === !0 ? qr(F) : typeof oe === 'function' ? oe(F) : F
      const De = z.x
      let Fe = De === void 0 ? 0 : De
      const Ce = z.y
      let xe = Ce === void 0 ? 0 : Ce
      const Me = F.hasOwnProperty('x')
      const Se = F.hasOwnProperty('y')
      let Be = Q
      let Re = V
      const He = window
      if (G) {
        let ce = J(b)
        let Pe = 'clientHeight'
        let _e = 'clientWidth';
        (ce === r(b) &&
                    ((ce = y(b)),
                    O(ce).position !== 'static' &&
                        ((Pe = 'scrollHeight'), (_e = 'scrollWidth'))),
        (ce = ce),
        P === V &&
                        ((Re = de),
                        (xe -= ce[Pe] - T.height),
                        (xe *= H ? 1 : -1)),
        P === Q &&
                        ((Be = X),
                        (Fe -= ce[_e] - T.width),
                        (Fe *= H ? 1 : -1)))
      }
      const ke = Object.assign({ position: U }, G && Xr)
      if (H) {
        let Le
        return Object.assign(
          {},
          ke,
          ((Le = {}),
          (Le[Re] = Se ? '0' : ''),
          (Le[Be] = Me ? '0' : ''),
          (Le.transform =
                        (He.devicePixelRatio || 1) < 2
                          ? 'translate(' + Fe + 'px, ' + xe + 'px)'
                          : 'translate3d(' + Fe + 'px, ' + xe + 'px, 0)'),
          Le)
        )
      }
      return Object.assign(
        {},
        ke,
        ((s = {}),
        (s[Re] = Se ? xe + 'px' : ''),
        (s[Be] = Me ? Fe + 'px' : ''),
        (s.transform = ''),
        s)
      )
    }
    function g (u) {
      const s = u.state
      const b = u.options
      const T = b.gpuAcceleration
      const P = T === void 0 ? !0 : T
      const F = b.adaptive
      const U = F === void 0 ? !0 : F
      const H = b.roundOffsets
      const G = H === void 0 ? !0 : H
      const oe = O(s.elements.popper).transitionProperty || ''
      U &&
                ['transform', 'top', 'right', 'bottom', 'left'].some(
                  function (De) {
                    return oe.indexOf(De) >= 0
                  }
                ) &&
                console.warn(
                  [
                    'Popper: Detected CSS transitions on at least one of the following',
                    'CSS properties: "transform", "top", "right", "bottom", "left".',
                        `

`,
                        'Disable the "computeStyles" modifier\'s `adaptive` option to allow',
                        'for smooth transitions, or remove these properties from the CSS',
                        'transition declaration on the popper element if only transitioning',
                        'opacity or background-color for example.',
                        `

`,
                        'We recommend using the popper element as a wrapper around an inner',
                        'element that can have any CSS property transitioned for animations.'
                  ].join(' ')
                )
      const z = {
        placement: ot(s.placement),
        popper: s.elements.popper,
        popperRect: s.rects.popper,
        gpuAcceleration: P
      };
      (s.modifiersData.popperOffsets != null &&
                (s.styles.popper = Object.assign(
                  {},
                  s.styles.popper,
                  Hn(
                    Object.assign({}, z, {
                      offsets: s.modifiersData.popperOffsets,
                      position: s.options.strategy,
                      adaptive: U,
                      roundOffsets: G
                    })
                  )
                )),
      s.modifiersData.arrow != null &&
                    (s.styles.arrow = Object.assign(
                      {},
                      s.styles.arrow,
                      Hn(
                        Object.assign({}, z, {
                          offsets: s.modifiersData.arrow,
                          position: 'absolute',
                          adaptive: !1,
                          roundOffsets: G
                        })
                      )
                    )),
      (s.attributes.popper = Object.assign({}, s.attributes.popper, {
        'data-popper-placement': s.placement
      })))
    }
    const w = {
      name: 'computeStyles',
      enabled: !0,
      phase: 'beforeWrite',
      fn: g,
      data: {}
    }
    function C (u) {
      const s = u.state
      Object.keys(s.elements).forEach(function (b) {
        const T = s.styles[b] || {}
        const P = s.attributes[b] || {}
        const F = s.elements[b]
        !o(F) ||
                    !d(F) ||
                    (Object.assign(F.style, T),
                    Object.keys(P).forEach(function (U) {
                      const H = P[U]
                      H === !1
                        ? F.removeAttribute(U)
                        : F.setAttribute(U, H === !0 ? '' : H)
                    }))
      })
    }
    function L (u) {
      const s = u.state
      const b = {
        popper: {
          position: s.options.strategy,
          left: '0',
          top: '0',
          margin: '0'
        },
        arrow: { position: 'absolute' },
        reference: {}
      }
      return (
        Object.assign(s.elements.popper.style, b.popper),
        (s.styles = b),
        s.elements.arrow &&
                    Object.assign(s.elements.arrow.style, b.arrow),
        function () {
          Object.keys(s.elements).forEach(function (T) {
            const P = s.elements[T]
            const F = s.attributes[T] || {}
            const U = Object.keys(
              s.styles.hasOwnProperty(T) ? s.styles[T] : b[T]
            )
            const H = U.reduce(function (G, oe) {
              return ((G[oe] = ''), G)
            }, {})
            !o(P) ||
                            !d(P) ||
                            (Object.assign(P.style, H),
                            Object.keys(F).forEach(function (G) {
                              P.removeAttribute(G)
                            }))
          })
        }
      )
    }
    const q = {
      name: 'applyStyles',
      enabled: !0,
      phase: 'write',
      fn: C,
      effect: L,
      requires: ['computeStyles']
    }
    function W (u, s, b) {
      const T = ot(u)
      const P = [Q, V].indexOf(T) >= 0 ? -1 : 1
      const F =
                typeof b === 'function'
                  ? b(Object.assign({}, s, { placement: u }))
                  : b
      let U = F[0]
      let H = F[1]
      return (
        (U = U || 0),
        (H = (H || 0) * P),
        [Q, X].indexOf(T) >= 0 ? { x: H, y: U } : { x: U, y: H }
      )
    }
    function B (u) {
      const s = u.state
      const b = u.options
      const T = u.name
      const P = b.offset
      const F = P === void 0 ? [0, 0] : P
      const U = ze.reduce(function (z, De) {
        return ((z[De] = W(De, s.rects, F)), z)
      }, {})
      const H = U[s.placement]
      const G = H.x
      const oe = H.y;
      (s.modifiersData.popperOffsets != null &&
                ((s.modifiersData.popperOffsets.x += G),
                (s.modifiersData.popperOffsets.y += oe)),
      (s.modifiersData[T] = U))
    }
    const be = {
      name: 'offset',
      enabled: !0,
      phase: 'main',
      requires: ['popperOffsets'],
      fn: B
    }
    const le = {
      left: 'right',
      right: 'left',
      bottom: 'top',
      top: 'bottom'
    }
    function pe (u) {
      return u.replace(/left|right|bottom|top/g, function (s) {
        return le[s]
      })
    }
    const ye = { start: 'end', end: 'start' }
    function Te (u) {
      return u.replace(/start|end/g, function (s) {
        return ye[s]
      })
    }
    function je (u, s) {
      s === void 0 && (s = {})
      const b = s
      const T = b.placement
      const P = b.boundary
      const F = b.rootBoundary
      const U = b.padding
      const H = b.flipVariations
      const G = b.allowedAutoPlacements
      const oe = G === void 0 ? ze : G
      const z = cn(T)
      const De = z
        ? H
          ? Z
          : Z.filter(function (xe) {
            return cn(xe) === z
          })
        : l
      let Fe = De.filter(function (xe) {
        return oe.indexOf(xe) >= 0
      })
      Fe.length === 0 &&
                ((Fe = De),
                console.error(
                  [
                    'Popper: The `allowedAutoPlacements` option did not allow any',
                    'placements. Ensure the `placement` option matches the variation',
                    'of the allowed placements.',
                    'For example, "auto" cannot be used to allow "bottom-start".',
                    'Use "auto-start" instead.'
                  ].join(' ')
                ))
      const Ce = Fe.reduce(function (xe, Me) {
        return (
          (xe[Me] = qt(u, {
            placement: Me,
            boundary: P,
            rootBoundary: F,
            padding: U
          })[ot(Me)]),
          xe
        )
      }, {})
      return Object.keys(Ce).sort(function (xe, Me) {
        return Ce[xe] - Ce[Me]
      })
    }
    function Ae (u) {
      if (ot(u) === me) return []
      const s = pe(u)
      return [Te(u), s, Te(s)]
    }
    function Ie (u) {
      const s = u.state
      const b = u.options
      const T = u.name
      if (!s.modifiersData[T]._skip) {
        for (
          var P = b.mainAxis,
            F = P === void 0 ? !0 : P,
            U = b.altAxis,
            H = U === void 0 ? !0 : U,
            G = b.fallbackPlacements,
            oe = b.padding,
            z = b.boundary,
            De = b.rootBoundary,
            Fe = b.altBoundary,
            Ce = b.flipVariations,
            xe = Ce === void 0 ? !0 : Ce,
            Me = b.allowedAutoPlacements,
            Se = s.options.placement,
            Be = ot(Se),
            Re = Be === Se,
            He = G || (Re || !xe ? [pe(Se)] : Ae(Se)),
            ce = [Se].concat(He).reduce(function (te, ge) {
              return te.concat(
                ot(ge) === me
                  ? je(s, {
                    placement: ge,
                    boundary: z,
                    rootBoundary: De,
                    padding: oe,
                    flipVariations: xe,
                    allowedAutoPlacements: Me
                  })
                  : ge
              )
            }, []),
            Pe = s.rects.reference,
            _e = s.rects.popper,
            ke = new Map(),
            Le = !0,
            Ye = ce[0],
            $e = 0;
          $e < ce.length;
          $e++
        ) {
          const Ue = ce[$e]
          const wt = ot(Ue)
          const et = cn(Ue) === h
          const Ft = [V, de].indexOf(wt) >= 0
          const dn = Ft ? 'width' : 'height'
          const Qt = qt(s, {
            placement: Ue,
            boundary: z,
            rootBoundary: De,
            altBoundary: Fe,
            padding: oe
          })
          let kt = Ft ? (et ? X : Q) : et ? de : V
          Pe[dn] > _e[dn] && (kt = pe(kt))
          const $n = pe(kt)
          const Zt = []
          if (
            (F && Zt.push(Qt[wt] <= 0),
            H && Zt.push(Qt[kt] <= 0, Qt[$n] <= 0),
            Zt.every(function (te) {
              return te
            }))
          ) {
            ((Ye = Ue), (Le = !1))
            break
          }
          ke.set(Ue, Zt)
        }
        if (Le) {
          for (
            let Sn = xe ? 3 : 1,
              Wn = function (ge) {
                const we = ce.find(function (Ke) {
                  const Je = ke.get(Ke)
                  if (Je) {
                    return Je.slice(0, ge).every(
                      function (Dt) {
                        return Dt
                      }
                    )
                  }
                })
                if (we) return ((Ye = we), 'break')
              },
              D = Sn;
            D > 0;
            D--
          ) {
            const K = Wn(D)
            if (K === 'break') break
          }
        }
        s.placement !== Ye &&
                    ((s.modifiersData[T]._skip = !0),
                    (s.placement = Ye),
                    (s.reset = !0))
      }
    }
    const re = {
      name: 'flip',
      enabled: !0,
      phase: 'main',
      fn: Ie,
      requiresIfExists: ['offset'],
      data: { _skip: !1 }
    }
    function he (u) {
      return u === 'x' ? 'y' : 'x'
    }
    function ve (u, s, b) {
      return gt(u, ln(s, b))
    }
    function ee (u) {
      const s = u.state
      const b = u.options
      const T = u.name
      const P = b.mainAxis
      const F = P === void 0 ? !0 : P
      const U = b.altAxis
      const H = U === void 0 ? !1 : U
      const G = b.boundary
      const oe = b.rootBoundary
      const z = b.altBoundary
      const De = b.padding
      const Fe = b.tether
      const Ce = Fe === void 0 ? !0 : Fe
      const xe = b.tetherOffset
      const Me = xe === void 0 ? 0 : xe
      const Se = qt(s, {
        boundary: G,
        rootBoundary: oe,
        padding: De,
        altBoundary: z
      })
      const Be = ot(s.placement)
      const Re = cn(s.placement)
      const He = !Re
      const ce = dt(Be)
      const Pe = he(ce)
      const _e = s.modifiersData.popperOffsets
      const ke = s.rects.reference
      const Le = s.rects.popper
      const Ye =
                typeof Me === 'function'
                  ? Me(
                    Object.assign({}, s.rects, {
                      placement: s.placement
                    })
                  )
                  : Me
      const $e = { x: 0, y: 0 }
      if (_e) {
        if (F || H) {
          const Ue = ce === 'y' ? V : Q
          const wt = ce === 'y' ? de : X
          const et = ce === 'y' ? 'height' : 'width'
          const Ft = _e[ce]
          const dn = _e[ce] + Se[Ue]
          const Qt = _e[ce] - Se[wt]
          const kt = Ce ? -Le[et] / 2 : 0
          const $n = Re === h ? ke[et] : Le[et]
          const Zt = Re === h ? -Le[et] : -ke[et]
          const Sn = s.elements.arrow
          const Wn = Ce && Sn ? _(Sn) : { width: 0, height: 0 }
          const D = s.modifiersData['arrow#persistent']
            ? s.modifiersData['arrow#persistent'].padding
            : cr()
          const K = D[Ue]
          const te = D[wt]
          const ge = ve(0, ke[et], Wn[et])
          const we = He
            ? ke[et] / 2 - kt - ge - K - Ye
            : $n - ge - K - Ye
          const Ke = He
            ? -ke[et] / 2 + kt + ge + te + Ye
            : Zt + ge + te + Ye
          const Je = s.elements.arrow && J(s.elements.arrow)
          const Dt = Je
            ? ce === 'y'
              ? Je.clientTop || 0
              : Je.clientLeft || 0
            : 0
          const Un = s.modifiersData.offset
            ? s.modifiersData.offset[s.placement][ce]
            : 0
          const _t = _e[ce] + we - Un - Dt
          const An = _e[ce] + Ke - Un
          if (F) {
            const pn = ve(
              Ce ? ln(dn, _t) : dn,
              Ft,
              Ce ? gt(Qt, An) : Qt
            );
            ((_e[ce] = pn), ($e[ce] = pn - Ft))
          }
          if (H) {
            const en = ce === 'x' ? V : Q
            const Gr = ce === 'x' ? de : X
            const tn = _e[Pe]
            const hn = tn + Se[en]
            const Di = tn - Se[Gr]
            const _i = ve(
              Ce ? ln(hn, _t) : hn,
              tn,
              Ce ? gt(Di, An) : Di
            );
            ((_e[Pe] = _i), ($e[Pe] = _i - tn))
          }
        }
        s.modifiersData[T] = $e
      }
    }
    const ie = {
      name: 'preventOverflow',
      enabled: !0,
      phase: 'main',
      fn: ee,
      requiresIfExists: ['offset']
    }
    const x = function (s, b) {
      return (
        (s =
                    typeof s === 'function'
                      ? s(
                        Object.assign({}, b.rects, {
                          placement: b.placement
                        })
                      )
                      : s),
        fr(typeof s !== 'number' ? s : ur(s, l))
      )
    }
    function Ge (u) {
      let s
      const b = u.state
      const T = u.name
      const P = u.options
      const F = b.elements.arrow
      const U = b.modifiersData.popperOffsets
      const H = ot(b.placement)
      const G = dt(H)
      const oe = [Q, X].indexOf(H) >= 0
      const z = oe ? 'height' : 'width'
      if (!(!F || !U)) {
        const De = x(P.padding, b)
        const Fe = _(F)
        const Ce = G === 'y' ? V : Q
        const xe = G === 'y' ? de : X
        const Me =
                    b.rects.reference[z] +
                    b.rects.reference[G] -
                    U[G] -
                    b.rects.popper[z]
        const Se = U[G] - b.rects.reference[G]
        const Be = J(F)
        const Re = Be
          ? G === 'y'
            ? Be.clientHeight || 0
            : Be.clientWidth || 0
          : 0
        const He = Me / 2 - Se / 2
        const ce = De[Ce]
        const Pe = Re - Fe[z] - De[xe]
        const _e = Re / 2 - Fe[z] / 2 + He
        const ke = ve(ce, _e, Pe)
        const Le = G
        b.modifiersData[T] =
                    ((s = {}), (s[Le] = ke), (s.centerOffset = ke - _e), s)
      }
    }
    function fe (u) {
      const s = u.state
      const b = u.options
      const T = b.element
      let P = T === void 0 ? '[data-popper-arrow]' : T
      if (
        P != null &&
                !(
                  typeof P === 'string' &&
                    ((P = s.elements.popper.querySelector(P)), !P)
                )
      ) {
        if (
          (o(P) ||
                        console.error(
                          [
                            'Popper: "arrow" element must be an HTMLElement (not an SVGElement).',
                            'To use an SVG arrow, wrap it in an HTMLElement that will be used as',
                            'the arrow.'
                          ].join(' ')
                        ),
          !Nn(s.elements.popper, P))
        ) {
          console.error(
            [
              'Popper: "arrow" modifier\'s `element` must be a child of the popper',
              'element.'
            ].join(' ')
          )
          return
        }
        s.elements.arrow = P
      }
    }
    const Lt = {
      name: 'arrow',
      enabled: !0,
      phase: 'main',
      fn: Ge,
      effect: fe,
      requires: ['popperOffsets'],
      requiresIfExists: ['preventOverflow']
    }
    function bt (u, s, b) {
      return (
        b === void 0 && (b = { x: 0, y: 0 }),
        {
          top: u.top - s.height - b.y,
          right: u.right - s.width + b.x,
          bottom: u.bottom - s.height + b.y,
          left: u.left - s.width - b.x
        }
      )
    }
    function Gt (u) {
      return [V, X, de, Q].some(function (s) {
        return u[s] >= 0
      })
    }
    function Kt (u) {
      const s = u.state
      const b = u.name
      const T = s.rects.reference
      const P = s.rects.popper
      const F = s.modifiersData.preventOverflow
      const U = qt(s, { elementContext: 'reference' })
      const H = qt(s, { altBoundary: !0 })
      const G = bt(U, T)
      const oe = bt(H, P, F)
      const z = Gt(G)
      const De = Gt(oe);
      ((s.modifiersData[b] = {
        referenceClippingOffsets: G,
        popperEscapeOffsets: oe,
        isReferenceHidden: z,
        hasPopperEscaped: De
      }),
      (s.attributes.popper = Object.assign({}, s.attributes.popper, {
        'data-popper-reference-hidden': z,
        'data-popper-escaped': De
      })))
    }
    const Jt = {
      name: 'hide',
      enabled: !0,
      phase: 'main',
      requiresIfExists: ['preventOverflow'],
      fn: Kt
    }
    const rt = [jn, Bn, w, q]
    const lt = En({ defaultModifiers: rt })
    const yt = [jn, Bn, w, q, be, re, ie, Lt, Jt]
    const un = En({ defaultModifiers: yt });
    ((e.applyStyles = q),
    (e.arrow = Lt),
    (e.computeStyles = w),
    (e.createPopper = un),
    (e.createPopperLite = lt),
    (e.defaultModifiers = yt),
    (e.detectOverflow = qt),
    (e.eventListeners = jn),
    (e.flip = re),
    (e.hide = Jt),
    (e.offset = be),
    (e.popperGenerator = En),
    (e.popperOffsets = Bn),
    (e.preventOverflow = ie))
  })
  const $o = Bo((e) => {
    'use strict'
    Object.defineProperty(e, '__esModule', { value: !0 })
    const t = _s()
    const r =
            '<svg width="16" height="6" xmlns="http://www.w3.org/2000/svg"><path d="M0 6s1.796-.013 4.67-3.615C5.851.9 6.93.006 8 0c1.07-.006 2.148.887 3.343 2.385C14.233 6.005 16 6 16 6H0z"></svg>'
    const n = 'tippy-box'
    const i = 'tippy-content'
    const o = 'tippy-backdrop'
    const a = 'tippy-arrow'
    const c = 'tippy-svg-arrow'
    const f = { passive: !0, capture: !0 }
    function d (g, w) {
      return {}.hasOwnProperty.call(g, w)
    }
    function y (g, w, C) {
      if (Array.isArray(g)) {
        const L = g[w]
        return L ?? (Array.isArray(C) ? C[w] : C)
      }
      return g
    }
    function m (g, w) {
      const C = {}.toString.call(g)
      return C.indexOf('[object') === 0 && C.indexOf(w + ']') > -1
    }
    function O (g, w) {
      return typeof g === 'function' ? g.apply(void 0, w) : g
    }
    function E (g, w) {
      if (w === 0) return g
      let C
      return function (L) {
        (clearTimeout(C),
        (C = setTimeout(function () {
          g(L)
        }, w)))
      }
    }
    function S (g, w) {
      const C = Object.assign({}, g)
      return (
        w.forEach(function (L) {
          delete C[L]
        }),
        C
      )
    }
    function _ (g) {
      return g.split(/\s+/).filter(Boolean)
    }
    function I (g) {
      return [].concat(g)
    }
    function $ (g, w) {
      g.indexOf(w) === -1 && g.push(w)
    }
    function A (g) {
      return g.filter(function (w, C) {
        return g.indexOf(w) === C
      })
    }
    function N (g) {
      return g.split('-')[0]
    }
    function Y (g) {
      return [].slice.call(g)
    }
    function ne (g) {
      return Object.keys(g).reduce(function (w, C) {
        return (g[C] !== void 0 && (w[C] = g[C]), w)
      }, {})
    }
    function J () {
      return document.createElement('div')
    }
    function V (g) {
      return ['Element', 'Fragment'].some(function (w) {
        return m(g, w)
      })
    }
    function de (g) {
      return m(g, 'NodeList')
    }
    function X (g) {
      return m(g, 'MouseEvent')
    }
    function Q (g) {
      return !!(g && g._tippy && g._tippy.reference === g)
    }
    function me (g) {
      return V(g)
        ? [g]
        : de(g)
          ? Y(g)
          : Array.isArray(g)
            ? g
            : Y(document.querySelectorAll(g))
    }
    function l (g, w) {
      g.forEach(function (C) {
        C && (C.style.transitionDuration = w + 'ms')
      })
    }
    function h (g, w) {
      g.forEach(function (C) {
        C && C.setAttribute('data-state', w)
      })
    }
    function v (g) {
      let w
      const C = I(g)
      const L = C[0]
      return !(L == null || (w = L.ownerDocument) == null) && w.body
        ? L.ownerDocument
        : document
    }
    function p (g, w) {
      const C = w.clientX
      const L = w.clientY
      return g.every(function (q) {
        const W = q.popperRect
        const B = q.popperState
        const be = q.props
        const le = be.interactiveBorder
        const pe = N(B.placement)
        const ye = B.modifiersData.offset
        if (!ye) return !0
        const Te = pe === 'bottom' ? ye.top.y : 0
        const je = pe === 'top' ? ye.bottom.y : 0
        const Ae = pe === 'right' ? ye.left.x : 0
        const Ie = pe === 'left' ? ye.right.x : 0
        const re = W.top - L + Te > le
        const he = L - W.bottom - je > le
        const ve = W.left - C + Ae > le
        const ee = C - W.right - Ie > le
        return re || he || ve || ee
      })
    }
    function j (g, w, C) {
      const L = w + 'EventListener';
      ['transitionend', 'webkitTransitionEnd'].forEach(function (q) {
        g[L](q, C)
      })
    }
    const M = { isTouch: !1 }
    let R = 0
    function Z () {
      M.isTouch ||
                ((M.isTouch = !0),
                window.performance &&
                    document.addEventListener('mousemove', ze))
    }
    function ze () {
      const g = performance.now();
      (g - R < 20 &&
                ((M.isTouch = !1),
                document.removeEventListener('mousemove', ze)),
      (R = g))
    }
    function Rt () {
      const g = document.activeElement
      if (Q(g)) {
        const w = g._tippy
        g.blur && !w.state.isVisible && g.blur()
      }
    }
    function Ut () {
      (document.addEventListener('touchstart', Z, f),
      window.addEventListener('blur', Rt))
    }
    const Fr = typeof window < 'u' && typeof document < 'u'
    const kr = Fr ? navigator.userAgent : ''
    const Nr = /MSIE |Trident\//.test(kr)
    function Vt (g) {
      const w = g === 'destroy' ? 'n already-' : ' '
      return [
        g +
                    '() was called on a' +
                    w +
                    'destroyed instance. This is a no-op but',
        'indicates a potential memory leak.'
      ].join(' ')
    }
    function nr (g) {
      const w = /[ \t]{2,}/g
      const C = /^[ \t]*/gm
      return g.replace(w, ' ').replace(C, '').trim()
    }
    function jr (g) {
      return nr(
                `
  %ctippy.js

  %c` +
                    nr(g) +
                    `

  %c\u{1F477}\u200D This is a development-only message. It will be removed in production.
  `
      )
    }
    function rr (g) {
      return [
        jr(g),
        'color: #00C584; font-size: 1.3em; font-weight: bold;',
        'line-height: 1.5',
        'color: #a6a095;'
      ]
    }
    let It
    Br()
    function Br () {
      It = new Set()
    }
    function mt (g, w) {
      if (g && !It.has(w)) {
        let C;
        (It.add(w), (C = console).warn.apply(C, rr(w)))
      }
    }
    function zt (g, w) {
      if (g && !It.has(w)) {
        let C;
        (It.add(w), (C = console).error.apply(C, rr(w)))
      }
    }
    function At (g) {
      const w = !g
      const C =
                Object.prototype.toString.call(g) === '[object Object]' &&
                !g.addEventListener;
      (zt(
        w,
        [
          'tippy() was passed',
          '`' + String(g) + '`',
          'as its targets (first) argument. Valid types are: String, Element,',
          'Element[], or NodeList.'
        ].join(' ')
      ),
      zt(
        C,
        [
          'tippy() was passed a plain object which is not supported as an argument',
          'for virtual positioning. Use props.getReferenceClientRect instead.'
        ].join(' ')
      ))
    }
    const Ct = {
      animateFill: !1,
      followCursor: !1,
      inlinePositioning: !1,
      sticky: !1
    }
    const Hr = {
      allowHTML: !1,
      animation: 'fade',
      arrow: !0,
      content: '',
      inertia: !1,
      maxWidth: 350,
      role: 'tooltip',
      theme: '',
      zIndex: 9999
    }
    const Ze = Object.assign(
      {
        appendTo: function () {
          return document.body
        },
        aria: { content: 'auto', expanded: 'auto' },
        delay: 0,
        duration: [300, 250],
        getReferenceClientRect: null,
        hideOnClick: !0,
        ignoreAttributes: !1,
        interactive: !1,
        interactiveBorder: 2,
        interactiveDebounce: 0,
        moveTransition: '',
        offset: [0, 10],
        onAfterUpdate: function () {},
        onBeforeUpdate: function () {},
        onCreate: function () {},
        onDestroy: function () {},
        onHidden: function () {},
        onHide: function () {},
        onMount: function () {},
        onShow: function () {},
        onShown: function () {},
        onTrigger: function () {},
        onUntrigger: function () {},
        onClickOutside: function () {},
        placement: 'top',
        plugins: [],
        popperOptions: {},
        render: null,
        showOnCreate: !1,
        touch: !0,
        trigger: 'mouseenter focus',
        triggerTarget: null
      },
      Ct,
      {},
      Hr
    )
    const $r = Object.keys(Ze)
    const Wr = function (w) {
      gt(w, [])
      const C = Object.keys(w)
      C.forEach(function (L) {
        Ze[L] = w[L]
      })
    }
    function ot (g) {
      const w = g.plugins || []
      const C = w.reduce(function (L, q) {
        const W = q.name
        const B = q.defaultValue
        return (W && (L[W] = g[W] !== void 0 ? g[W] : B), L)
      }, {})
      return Object.assign({}, g, {}, C)
    }
    function Ur (g, w) {
      const C = w
        ? Object.keys(ot(Object.assign({}, Ze, { plugins: w })))
        : $r
      const L = C.reduce(function (q, W) {
        const B = (g.getAttribute('data-tippy-' + W) || '').trim()
        if (!B) return q
        if (W === 'content') q[W] = B
        else {
          try {
            q[W] = JSON.parse(B)
          } catch {
            q[W] = B
          }
        }
        return q
      }, {})
      return L
    }
    function ir (g, w) {
      const C = Object.assign(
        {},
        w,
        { content: O(w.content, [g]) },
        w.ignoreAttributes ? {} : Ur(g, w.plugins)
      )
      return (
        (C.aria = Object.assign({}, Ze.aria, {}, C.aria)),
        (C.aria = {
          expanded:
                        C.aria.expanded === 'auto'
                          ? w.interactive
                          : C.aria.expanded,
          content:
                        C.aria.content === 'auto'
                          ? w.interactive
                            ? null
                            : 'describedby'
                          : C.aria.content
        }),
        C
      )
    }
    function gt (g, w) {
      (g === void 0 && (g = {}), w === void 0 && (w = []))
      const C = Object.keys(g)
      C.forEach(function (L) {
        const q = S(Ze, Object.keys(Ct))
        let W = !d(q, L);
        (W &&
                    (W =
                        w.filter(function (B) {
                          return B.name === L
                        }).length === 0),
        mt(
          W,
          [
            '`' + L + '`',
            "is not a valid prop. You may have spelled it incorrectly, or if it's",
            'a plugin, forgot to pass it in an array as props.plugins.',
                            `

`,
                            `All props: https://atomiks.github.io/tippyjs/v6/all-props/
`,
                            'Plugins: https://atomiks.github.io/tippyjs/v6/plugins/'
          ].join(' ')
        ))
      })
    }
    const ln = function () {
      return 'innerHTML'
    }
    function Yt (g, w) {
      g[ln()] = w
    }
    function or (g) {
      const w = J()
      return (
        g === !0
          ? (w.className = a)
          : ((w.className = c), V(g) ? w.appendChild(g) : Yt(w, g)),
        w
      )
    }
    function Nn (g, w) {
      V(w.content)
        ? (Yt(g, ''), g.appendChild(w.content))
        : typeof w.content !== 'function' &&
                  (w.allowHTML
                    ? Yt(g, w.content)
                    : (g.textContent = w.content))
    }
    function Xt (g) {
      const w = g.firstElementChild
      const C = Y(w.children)
      return {
        box: w,
        content: C.find(function (L) {
          return L.classList.contains(i)
        }),
        arrow: C.find(function (L) {
          return L.classList.contains(a) || L.classList.contains(c)
        }),
        backdrop: C.find(function (L) {
          return L.classList.contains(o)
        })
      }
    }
    function ar (g) {
      const w = J()
      const C = J();
      ((C.className = n),
      C.setAttribute('data-state', 'hidden'),
      C.setAttribute('tabindex', '-1'))
      const L = J();
      ((L.className = i),
      L.setAttribute('data-state', 'hidden'),
      Nn(L, g.props),
      w.appendChild(C),
      C.appendChild(L),
      q(g.props, g.props))
      function q (W, B) {
        const be = Xt(w)
        const le = be.box
        const pe = be.content
        const ye = be.arrow;
        (B.theme
          ? le.setAttribute('data-theme', B.theme)
          : le.removeAttribute('data-theme'),
        typeof B.animation === 'string'
          ? le.setAttribute('data-animation', B.animation)
          : le.removeAttribute('data-animation'),
        B.inertia
          ? le.setAttribute('data-inertia', '')
          : le.removeAttribute('data-inertia'),
        (le.style.maxWidth =
                        typeof B.maxWidth === 'number'
                          ? B.maxWidth + 'px'
                          : B.maxWidth),
        B.role
          ? le.setAttribute('role', B.role)
          : le.removeAttribute('role'),
        (W.content !== B.content || W.allowHTML !== B.allowHTML) &&
                        Nn(pe, g.props),
        B.arrow
          ? ye
            ? W.arrow !== B.arrow &&
                              (le.removeChild(ye), le.appendChild(or(B.arrow)))
            : le.appendChild(or(B.arrow))
          : ye && le.removeChild(ye))
      }
      return { popper: w, onUpdate: q }
    }
    ar.$$tippy = !0
    let sr = 1
    let yn = []
    let wn = []
    function cn (g, w) {
      const C = ir(g, Object.assign({}, Ze, {}, ot(ne(w))))
      let L
      let q
      let W
      let B = !1
      let be = !1
      let le = !1
      let pe = !1
      let ye
      let Te
      let je
      let Ae = []
      let Ie = E(Re, C.interactiveDebounce)
      let re
      const he = sr++
      const ve = null
      const ee = A(C.plugins)
      const ie = {
        isEnabled: !0,
        isVisible: !1,
        isDestroyed: !1,
        isMounted: !1,
        isShown: !1
      }
      const x = {
        id: he,
        reference: g,
        popper: J(),
        popperInstance: ve,
        props: C,
        state: ie,
        plugins: ee,
        clearDelayTimeouts: Ft,
        setProps: dn,
        setContent: Qt,
        show: kt,
        hide: $n,
        hideWithInteractivity: Zt,
        enable: wt,
        disable: et,
        unmount: Sn,
        destroy: Wn
      }
      if (!C.render) {
        return (zt(!0, 'render() function has not been supplied.'), x)
      }
      const Ge = C.render(x)
      const fe = Ge.popper
      const Lt = Ge.onUpdate;
      (fe.setAttribute('data-tippy-root', ''),
      (fe.id = 'tippy-' + x.id),
      (x.popper = fe),
      (g._tippy = x),
      (fe._tippy = x))
      const bt = ee.map(function (D) {
        return D.fn(x)
      })
      const Gt = g.hasAttribute('aria-expanded')
      return (
        Me(),
        P(),
        s(),
        b('onCreate', [x]),
        C.showOnCreate && $e(),
        fe.addEventListener('mouseenter', function () {
          x.props.interactive &&
                        x.state.isVisible &&
                        x.clearDelayTimeouts()
        }),
        fe.addEventListener('mouseleave', function (D) {
          x.props.interactive &&
                        x.props.trigger.indexOf('mouseenter') >= 0 &&
                        (yt().addEventListener('mousemove', Ie), Ie(D))
        }),
        x
      )
      function Kt () {
        const D = x.props.touch
        return Array.isArray(D) ? D : [D, 0]
      }
      function Jt () {
        return Kt()[0] === 'hold'
      }
      function rt () {
        let D
        return !!((D = x.props.render) != null && D.$$tippy)
      }
      function lt () {
        return re || g
      }
      function yt () {
        const D = lt().parentNode
        return D ? v(D) : document
      }
      function un () {
        return Xt(fe)
      }
      function u (D) {
        return (x.state.isMounted && !x.state.isVisible) ||
                    M.isTouch ||
                    (ye && ye.type === 'focus')
          ? 0
          : y(x.props.delay, D ? 0 : 1, Ze.delay)
      }
      function s () {
        ((fe.style.pointerEvents =
                    x.props.interactive && x.state.isVisible ? '' : 'none'),
        (fe.style.zIndex = '' + x.props.zIndex))
      }
      function b (D, K, te) {
        if (
          (te === void 0 && (te = !0),
          bt.forEach(function (we) {
            we[D] && we[D].apply(void 0, K)
          }),
          te)
        ) {
          let ge;
          (ge = x.props)[D].apply(ge, K)
        }
      }
      function T () {
        const D = x.props.aria
        if (D.content) {
          const K = 'aria-' + D.content
          const te = fe.id
          const ge = I(x.props.triggerTarget || g)
          ge.forEach(function (we) {
            const Ke = we.getAttribute(K)
            if (x.state.isVisible) {
              we.setAttribute(K, Ke ? Ke + ' ' + te : te)
            } else {
              const Je = Ke && Ke.replace(te, '').trim()
              Je ? we.setAttribute(K, Je) : we.removeAttribute(K)
            }
          })
        }
      }
      function P () {
        if (!(Gt || !x.props.aria.expanded)) {
          const D = I(x.props.triggerTarget || g)
          D.forEach(function (K) {
            x.props.interactive
              ? K.setAttribute(
                'aria-expanded',
                x.state.isVisible && K === lt()
                  ? 'true'
                  : 'false'
              )
              : K.removeAttribute('aria-expanded')
          })
        }
      }
      function F () {
        (yt().removeEventListener('mousemove', Ie),
        (yn = yn.filter(function (D) {
          return D !== Ie
        })))
      }
      function U (D) {
        if (
          !(M.isTouch && (le || D.type === 'mousedown')) &&
                    !(x.props.interactive && fe.contains(D.target))
        ) {
          if (lt().contains(D.target)) {
            if (
              M.isTouch ||
                            (x.state.isVisible &&
                                x.props.trigger.indexOf('click') >= 0)
            ) {
              return
            }
          } else b('onClickOutside', [x, D])
          x.props.hideOnClick === !0 &&
                        (x.clearDelayTimeouts(),
                        x.hide(),
                        (be = !0),
                        setTimeout(function () {
                          be = !1
                        }),
                        x.state.isMounted || z())
        }
      }
      function H () {
        le = !0
      }
      function G () {
        le = !1
      }
      function oe () {
        const D = yt();
        (D.addEventListener('mousedown', U, !0),
        D.addEventListener('touchend', U, f),
        D.addEventListener('touchstart', G, f),
        D.addEventListener('touchmove', H, f))
      }
      function z () {
        const D = yt();
        (D.removeEventListener('mousedown', U, !0),
        D.removeEventListener('touchend', U, f),
        D.removeEventListener('touchstart', G, f),
        D.removeEventListener('touchmove', H, f))
      }
      function De (D, K) {
        Ce(D, function () {
          !x.state.isVisible &&
                        fe.parentNode &&
                        fe.parentNode.contains(fe) &&
                        K()
        })
      }
      function Fe (D, K) {
        Ce(D, K)
      }
      function Ce (D, K) {
        const te = un().box
        function ge (we) {
          we.target === te && (j(te, 'remove', ge), K())
        }
        if (D === 0) return K();
        (j(te, 'remove', Te), j(te, 'add', ge), (Te = ge))
      }
      function xe (D, K, te) {
        te === void 0 && (te = !1)
        const ge = I(x.props.triggerTarget || g)
        ge.forEach(function (we) {
          (we.addEventListener(D, K, te),
          Ae.push({
            node: we,
            eventType: D,
            handler: K,
            options: te
          }))
        })
      }
      function Me () {
        (Jt() &&
                    (xe('touchstart', Be, { passive: !0 }),
                    xe('touchend', He, { passive: !0 })),
        _(x.props.trigger).forEach(function (D) {
          if (D !== 'manual') {
            switch ((xe(D, Be), D)) {
              case 'mouseenter':
                xe('mouseleave', He)
                break
              case 'focus':
                xe(Nr ? 'focusout' : 'blur', ce)
                break
              case 'focusin':
                xe('focusout', ce)
                break
            }
          }
        }))
      }
      function Se () {
        (Ae.forEach(function (D) {
          const K = D.node
          const te = D.eventType
          const ge = D.handler
          const we = D.options
          K.removeEventListener(te, ge, we)
        }),
        (Ae = []))
      }
      function Be (D) {
        let K
        let te = !1
        if (!(!x.state.isEnabled || Pe(D) || be)) {
          const ge = ((K = ye) == null ? void 0 : K.type) === 'focus';
          ((ye = D),
          (re = D.currentTarget),
          P(),
          !x.state.isVisible &&
                            X(D) &&
                            yn.forEach(function (we) {
                              return we(D)
                            }),
          D.type === 'click' &&
                        (x.props.trigger.indexOf('mouseenter') < 0 || B) &&
                        x.props.hideOnClick !== !1 &&
                        x.state.isVisible
            ? (te = !0)
            : $e(D),
          D.type === 'click' && (B = !te),
          te && !ge && Ue(D))
        }
      }
      function Re (D) {
        const K = D.target
        const te = lt().contains(K) || fe.contains(K)
        if (!(D.type === 'mousemove' && te)) {
          const ge = Ye()
            .concat(fe)
            .map(function (we) {
              let Ke
              const Je = we._tippy
              const Dt =
                                (Ke = Je.popperInstance) == null
                                  ? void 0
                                  : Ke.state
              return Dt
                ? {
                    popperRect: we.getBoundingClientRect(),
                    popperState: Dt,
                    props: C
                  }
                : null
            })
            .filter(Boolean)
          p(ge, D) && (F(), Ue(D))
        }
      }
      function He (D) {
        const K = Pe(D) || (x.props.trigger.indexOf('click') >= 0 && B)
        if (!K) {
          if (x.props.interactive) {
            x.hideWithInteractivity(D)
            return
          }
          Ue(D)
        }
      }
      function ce (D) {
        (x.props.trigger.indexOf('focusin') < 0 && D.target !== lt()) ||
                    (x.props.interactive &&
                        D.relatedTarget &&
                        fe.contains(D.relatedTarget)) ||
                    Ue(D)
      }
      function Pe (D) {
        return M.isTouch ? Jt() !== D.type.indexOf('touch') >= 0 : !1
      }
      function _e () {
        ke()
        const D = x.props
        const K = D.popperOptions
        const te = D.placement
        const ge = D.offset
        const we = D.getReferenceClientRect
        const Ke = D.moveTransition
        const Je = rt() ? Xt(fe).arrow : null
        const Dt = we
          ? {
              getBoundingClientRect: we,
              contextElement: we.contextElement || lt()
            }
          : g
        const Un = {
          name: '$$tippy',
          enabled: !0,
          phase: 'beforeWrite',
          requires: ['computeStyles'],
          fn: function (pn) {
            const en = pn.state
            if (rt()) {
              const Gr = un()
              const tn = Gr.box;
              ([
                'placement',
                'reference-hidden',
                'escaped'
              ].forEach(function (hn) {
                hn === 'placement'
                  ? tn.setAttribute(
                    'data-placement',
                    en.placement
                  )
                  : en.attributes.popper['data-popper-' + hn]
                    ? tn.setAttribute('data-' + hn, '')
                    : tn.removeAttribute('data-' + hn)
              }),
              (en.attributes.popper = {}))
            }
          }
        }
        const _t = [
          { name: 'offset', options: { offset: ge } },
          {
            name: 'preventOverflow',
            options: {
              padding: {
                top: 2,
                bottom: 2,
                left: 5,
                right: 5
              }
            }
          },
          { name: 'flip', options: { padding: 5 } },
          {
            name: 'computeStyles',
            options: { adaptive: !Ke }
          },
          Un
        ];
        (rt() &&
                    Je &&
                    _t.push({
                      name: 'arrow',
                      options: { element: Je, padding: 3 }
                    }),
        _t.push.apply(_t, K?.modifiers || []),
        (x.popperInstance = t.createPopper(
          Dt,
          fe,
          Object.assign({}, K, {
            placement: te,
            onFirstUpdate: je,
            modifiers: _t
          })
        )))
      }
      function ke () {
        x.popperInstance &&
                    (x.popperInstance.destroy(), (x.popperInstance = null))
      }
      function Le () {
        const D = x.props.appendTo
        let K
        const te = lt();
        ((x.props.interactive && D === Ze.appendTo) || D === 'parent'
          ? (K = te.parentNode)
          : (K = O(D, [te])),
        K.contains(fe) || K.appendChild(fe),
        _e(),
        mt(
          x.props.interactive &&
                            D === Ze.appendTo &&
                            te.nextElementSibling !== fe,
          [
            'Interactive tippy element may not be accessible via keyboard',
            'navigation because it is not directly after the reference element',
            'in the DOM source order.',
                            `

`,
                            'Using a wrapper <div> or <span> tag around the reference element',
                            'solves this by creating a new parentNode context.',
                            `

`,
                            'Specifying `appendTo: document.body` silences this warning, but it',
                            'assumes you are using a focus management solution to handle',
                            'keyboard navigation.',
                            `

`,
                            'See: https://atomiks.github.io/tippyjs/v6/accessibility/#interactivity'
          ].join(' ')
        ))
      }
      function Ye () {
        return Y(fe.querySelectorAll('[data-tippy-root]'))
      }
      function $e (D) {
        (x.clearDelayTimeouts(), D && b('onTrigger', [x, D]), oe())
        let K = u(!0)
        const te = Kt()
        const ge = te[0]
        const we = te[1];
        (M.isTouch && ge === 'hold' && we && (K = we),
        K
          ? (L = setTimeout(function () {
              x.show()
            }, K))
          : x.show())
      }
      function Ue (D) {
        if (
          (x.clearDelayTimeouts(),
          b('onUntrigger', [x, D]),
          !x.state.isVisible)
        ) {
          z()
          return
        }
        if (
          !(
            x.props.trigger.indexOf('mouseenter') >= 0 &&
                        x.props.trigger.indexOf('click') >= 0 &&
                        ['mouseleave', 'mousemove'].indexOf(D.type) >= 0 &&
                        B
          )
        ) {
          const K = u(!1)
          K
            ? (q = setTimeout(function () {
                x.state.isVisible && x.hide()
              }, K))
            : (W = requestAnimationFrame(function () {
                x.hide()
              }))
        }
      }
      function wt () {
        x.state.isEnabled = !0
      }
      function et () {
        (x.hide(), (x.state.isEnabled = !1))
      }
      function Ft () {
        (clearTimeout(L), clearTimeout(q), cancelAnimationFrame(W))
      }
      function dn (D) {
        if (
          (mt(x.state.isDestroyed, Vt('setProps')),
          !x.state.isDestroyed)
        ) {
          (b('onBeforeUpdate', [x, D]), Se())
          const K = x.props
          const te = ir(
            g,
            Object.assign({}, x.props, {}, D, {
              ignoreAttributes: !0
            })
          );
          ((x.props = te),
          Me(),
          K.interactiveDebounce !== te.interactiveDebounce &&
                            (F(), (Ie = E(Re, te.interactiveDebounce))),
          K.triggerTarget && !te.triggerTarget
            ? I(K.triggerTarget).forEach(function (ge) {
              ge.removeAttribute('aria-expanded')
            })
            : te.triggerTarget &&
                              g.removeAttribute('aria-expanded'),
          P(),
          s(),
          Lt && Lt(K, te),
          x.popperInstance &&
                            (_e(),
                            Ye().forEach(function (ge) {
                              requestAnimationFrame(
                                ge._tippy.popperInstance.forceUpdate
                              )
                            })),
          b('onAfterUpdate', [x, D]))
        }
      }
      function Qt (D) {
        x.setProps({ content: D })
      }
      function kt () {
        mt(x.state.isDestroyed, Vt('show'))
        const D = x.state.isVisible
        const K = x.state.isDestroyed
        const te = !x.state.isEnabled
        const ge = M.isTouch && !x.props.touch
        const we = y(x.props.duration, 0, Ze.duration)
        if (
          !(D || K || te || ge) &&
                    !lt().hasAttribute('disabled') &&
                    (b('onShow', [x], !1), x.props.onShow(x) !== !1)
        ) {
          if (
            ((x.state.isVisible = !0),
            rt() && (fe.style.visibility = 'visible'),
            s(),
            oe(),
            x.state.isMounted || (fe.style.transition = 'none'),
            rt())
          ) {
            const Ke = un()
            const Je = Ke.box
            const Dt = Ke.content
            l([Je, Dt], 0)
          }
          ((je = function () {
            let _t
            if (!(!x.state.isVisible || pe)) {
              if (
                ((pe = !0),
                fe.offsetHeight,
                (fe.style.transition = x.props.moveTransition),
                rt() && x.props.animation)
              ) {
                const An = un()
                const pn = An.box
                const en = An.content;
                (l([pn, en], we), h([pn, en], 'visible'))
              }
              (T(),
              P(),
              $(wn, x),
              (_t = x.popperInstance) == null ||
                                    _t.forceUpdate(),
              (x.state.isMounted = !0),
              b('onMount', [x]),
              x.props.animation &&
                                    rt() &&
                                    Fe(we, function () {
                                      ((x.state.isShown = !0),
                                      b('onShown', [x]))
                                    }))
            }
          }),
          Le())
        }
      }
      function $n () {
        mt(x.state.isDestroyed, Vt('hide'))
        const D = !x.state.isVisible
        const K = x.state.isDestroyed
        const te = !x.state.isEnabled
        const ge = y(x.props.duration, 1, Ze.duration)
        if (
          !(D || K || te) &&
                    (b('onHide', [x], !1), x.props.onHide(x) !== !1)
        ) {
          if (
            ((x.state.isVisible = !1),
            (x.state.isShown = !1),
            (pe = !1),
            (B = !1),
            rt() && (fe.style.visibility = 'hidden'),
            F(),
            z(),
            s(),
            rt())
          ) {
            const we = un()
            const Ke = we.box
            const Je = we.content
            x.props.animation &&
                            (l([Ke, Je], ge), h([Ke, Je], 'hidden'))
          }
          (T(),
          P(),
          x.props.animation
            ? rt() && De(ge, x.unmount)
            : x.unmount())
        }
      }
      function Zt (D) {
        (mt(x.state.isDestroyed, Vt('hideWithInteractivity')),
        yt().addEventListener('mousemove', Ie),
        $(yn, Ie),
        Ie(D))
      }
      function Sn () {
        (mt(x.state.isDestroyed, Vt('unmount')),
        x.state.isVisible && x.hide(),
        x.state.isMounted &&
                        (ke(),
                        Ye().forEach(function (D) {
                          D._tippy.unmount()
                        }),
                        fe.parentNode && fe.parentNode.removeChild(fe),
                        (wn = wn.filter(function (D) {
                          return D !== x
                        })),
                        (x.state.isMounted = !1),
                        b('onHidden', [x])))
      }
      function Wn () {
        (mt(x.state.isDestroyed, Vt('destroy')),
        !x.state.isDestroyed &&
                        (x.clearDelayTimeouts(),
                        x.unmount(),
                        Se(),
                        delete g._tippy,
                        (x.state.isDestroyed = !0),
                        b('onDestroy', [x])))
      }
    }
    function dt (g, w) {
      w === void 0 && (w = {})
      const C = Ze.plugins.concat(w.plugins || []);
      (At(g), gt(w, C), Ut())
      const L = Object.assign({}, w, { plugins: C })
      const q = me(g)
      const W = V(L.content)
      const B = q.length > 1
      mt(
        W && B,
        [
          'tippy() was passed an Element as the `content` prop, but more than',
          'one tippy instance was created by this invocation. This means the',
          'content element will only be appended to the last tippy instance.',
                    `

`,
                    'Instead, pass the .innerHTML of the element, or use a function that',
                    'returns a cloned version of the element instead.',
                    `

`,
                    `1) content: element.innerHTML
`,
                    '2) content: () => element.cloneNode(true)'
        ].join(' ')
      )
      const be = q.reduce(function (le, pe) {
        const ye = pe && cn(pe, L)
        return (ye && le.push(ye), le)
      }, [])
      return V(g) ? be[0] : be
    }
    ((dt.defaultProps = Ze),
    (dt.setDefaultProps = Wr),
    (dt.currentInput = M))
    const lr = function (w) {
      const C = w === void 0 ? {} : w
      const L = C.exclude
      const q = C.duration
      wn.forEach(function (W) {
        let B = !1
        if (
          (L &&
                        (B = Q(L) ? W.reference === L : W.popper === L.popper),
          !B)
        ) {
          const be = W.props.duration;
          (W.setProps({ duration: q }),
          W.hide(),
          W.state.isDestroyed || W.setProps({ duration: be }))
        }
      })
    }
    const cr = Object.assign({}, t.applyStyles, {
      effect: function (w) {
        const C = w.state
        const L = {
          popper: {
            position: C.options.strategy,
            left: '0',
            top: '0',
            margin: '0'
          },
          arrow: { position: 'absolute' },
          reference: {}
        };
        (Object.assign(C.elements.popper.style, L.popper),
        (C.styles = L),
        C.elements.arrow &&
                        Object.assign(C.elements.arrow.style, L.arrow))
      }
    })
    const fr = function (w, C) {
      let L;
      (C === void 0 && (C = {}),
      zt(
        !Array.isArray(w),
        [
          'The first argument passed to createSingleton() must be an array of',
          'tippy instances. The passed value was',
          String(w)
        ].join(' ')
      ))
      let q = w
      let W = []
      let B
      let be = C.overrides
      let le = []
      let pe = !1
      function ye () {
        W = q.map(function (ee) {
          return ee.reference
        })
      }
      function Te (ee) {
        q.forEach(function (ie) {
          ee ? ie.enable() : ie.disable()
        })
      }
      function je (ee) {
        return q.map(function (ie) {
          const x = ie.setProps
          return (
            (ie.setProps = function (Ge) {
              (x(Ge), ie.reference === B && ee.setProps(Ge))
            }),
            function () {
              ie.setProps = x
            }
          )
        })
      }
      function Ae (ee, ie) {
        const x = W.indexOf(ie)
        if (ie !== B) {
          B = ie
          const Ge = (be || []).concat('content').reduce(function (
            fe,
            Lt
          ) {
            return ((fe[Lt] = q[x].props[Lt]), fe)
          }, {})
          ee.setProps(
            Object.assign({}, Ge, {
              getReferenceClientRect:
                                typeof Ge.getReferenceClientRect === 'function'
                                  ? Ge.getReferenceClientRect
                                  : function () {
                                    return ie.getBoundingClientRect()
                                  }
            })
          )
        }
      }
      (Te(!1), ye())
      const Ie = {
        fn: function () {
          return {
            onDestroy: function () {
              Te(!0)
            },
            onHidden: function () {
              B = null
            },
            onClickOutside: function (x) {
              x.props.showOnCreate &&
                                !pe &&
                                ((pe = !0), (B = null))
            },
            onShow: function (x) {
              x.props.showOnCreate &&
                                !pe &&
                                ((pe = !0), Ae(x, W[0]))
            },
            onTrigger: function (x, Ge) {
              Ae(x, Ge.currentTarget)
            }
          }
        }
      }
      const re = dt(
        J(),
        Object.assign({}, S(C, ['overrides']), {
          plugins: [Ie].concat(C.plugins || []),
          triggerTarget: W,
          popperOptions: Object.assign({}, C.popperOptions, {
            modifiers: [].concat(
              ((L = C.popperOptions) == null
                ? void 0
                : L.modifiers) || [],
              [cr]
            )
          })
        })
      )
      const he = re.show;
      ((re.show = function (ee) {
        if ((he(), !B && ee == null)) return Ae(re, W[0])
        if (!(B && ee == null)) {
          if (typeof ee === 'number') {
            return W[ee] && Ae(re, W[ee])
          }
          if (q.includes(ee)) {
            const ie = ee.reference
            return Ae(re, ie)
          }
          if (W.includes(ee)) return Ae(re, ee)
        }
      }),
      (re.showNext = function () {
        const ee = W[0]
        if (!B) return re.show(0)
        const ie = W.indexOf(B)
        re.show(W[ie + 1] || ee)
      }),
      (re.showPrevious = function () {
        const ee = W[W.length - 1]
        if (!B) return re.show(ee)
        const ie = W.indexOf(B)
        const x = W[ie - 1] || ee
        re.show(x)
      }))
      const ve = re.setProps
      return (
        (re.setProps = function (ee) {
          ((be = ee.overrides || be), ve(ee))
        }),
        (re.setInstances = function (ee) {
          (Te(!0),
          le.forEach(function (ie) {
            return ie()
          }),
          (q = ee),
          Te(!1),
          ye(),
          je(re),
          re.setProps({ triggerTarget: W }))
        }),
        (le = je(re)),
        re
      )
    }
    const ur = {
      mouseover: 'mouseenter',
      focusin: 'focus',
      click: 'click'
    }
    function qt (g, w) {
      zt(
        !(w && w.target),
        [
          'You must specity a `target` prop indicating a CSS selector string matching',
          'the target elements that should receive a tippy.'
        ].join(' ')
      )
      let C = []
      let L = []
      let q = !1
      const W = w.target
      const B = S(w, ['target'])
      const be = Object.assign({}, B, { trigger: 'manual', touch: !1 })
      const le = Object.assign({}, B, { showOnCreate: !0 })
      const pe = dt(g, be)
      const ye = I(pe)
      function Te (he) {
        if (!(!he.target || q)) {
          const ve = he.target.closest(W)
          if (ve) {
            const ee =
                            ve.getAttribute('data-tippy-trigger') ||
                            w.trigger ||
                            Ze.trigger
            if (
              !ve._tippy &&
                            !(
                              he.type === 'touchstart' &&
                                typeof le.touch === 'boolean'
                            ) &&
                            !(
                              he.type !== 'touchstart' &&
                                ee.indexOf(ur[he.type]) < 0
                            )
            ) {
              const ie = dt(ve, le)
              ie && (L = L.concat(ie))
            }
          }
        }
      }
      function je (he, ve, ee, ie) {
        (ie === void 0 && (ie = !1),
        he.addEventListener(ve, ee, ie),
        C.push({
          node: he,
          eventType: ve,
          handler: ee,
          options: ie
        }))
      }
      function Ae (he) {
        const ve = he.reference;
        (je(ve, 'touchstart', Te, f),
        je(ve, 'mouseover', Te),
        je(ve, 'focusin', Te),
        je(ve, 'click', Te))
      }
      function Ie () {
        (C.forEach(function (he) {
          const ve = he.node
          const ee = he.eventType
          const ie = he.handler
          const x = he.options
          ve.removeEventListener(ee, ie, x)
        }),
        (C = []))
      }
      function re (he) {
        const ve = he.destroy
        const ee = he.enable
        const ie = he.disable;
        ((he.destroy = function (x) {
          (x === void 0 && (x = !0),
          x &&
                            L.forEach(function (Ge) {
                              Ge.destroy()
                            }),
          (L = []),
          Ie(),
          ve())
        }),
        (he.enable = function () {
          (ee(),
          L.forEach(function (x) {
            return x.enable()
          }),
          (q = !1))
        }),
        (he.disable = function () {
          (ie(),
          L.forEach(function (x) {
            return x.disable()
          }),
          (q = !0))
        }),
        Ae(he))
      }
      return (ye.forEach(re), pe)
    }
    const dr = {
      name: 'animateFill',
      defaultValue: !1,
      fn: function (w) {
        let C
        if (!((C = w.props.render) != null && C.$$tippy)) {
          return (
            zt(
              w.props.animateFill,
              'The `animateFill` plugin requires the default render function.'
            ),
            {}
          )
        }
        const L = Xt(w.popper)
        const q = L.box
        const W = L.content
        const B = w.props.animateFill ? Vr() : null
        return {
          onCreate: function () {
            B &&
                            (q.insertBefore(B, q.firstElementChild),
                            q.setAttribute('data-animatefill', ''),
                            (q.style.overflow = 'hidden'),
                            w.setProps({
                              arrow: !1,
                              animation: 'shift-away'
                            }))
          },
          onMount: function () {
            if (B) {
              const le = q.style.transitionDuration
              const pe = Number(le.replace('ms', ''));
              ((W.style.transitionDelay =
                                Math.round(pe / 10) + 'ms'),
              (B.style.transitionDuration = le),
              h([B], 'visible'))
            }
          },
          onShow: function () {
            B && (B.style.transitionDuration = '0ms')
          },
          onHide: function () {
            B && h([B], 'hidden')
          }
        }
      }
    }
    function Vr () {
      const g = J()
      return ((g.className = o), h([g], 'hidden'), g)
    }
    let xn = { clientX: 0, clientY: 0 }
    let fn = []
    function En (g) {
      const w = g.clientX
      const C = g.clientY
      xn = { clientX: w, clientY: C }
    }
    function On (g) {
      g.addEventListener('mousemove', En)
    }
    function zr (g) {
      g.removeEventListener('mousemove', En)
    }
    const jn = {
      name: 'followCursor',
      defaultValue: !1,
      fn: function (w) {
        const C = w.reference
        const L = v(w.props.triggerTarget || C)
        let q = !1
        let W = !1
        let B = !0
        let be = w.props
        function le () {
          return (
            w.props.followCursor === 'initial' && w.state.isVisible
          )
        }
        function pe () {
          L.addEventListener('mousemove', je)
        }
        function ye () {
          L.removeEventListener('mousemove', je)
        }
        function Te () {
          ((q = !0),
          w.setProps({ getReferenceClientRect: null }),
          (q = !1))
        }
        function je (re) {
          const he = re.target ? C.contains(re.target) : !0
          const ve = w.props.followCursor
          const ee = re.clientX
          const ie = re.clientY
          const x = C.getBoundingClientRect()
          const Ge = ee - x.left
          const fe = ie - x.top;
          (he || !w.props.interactive) &&
                        w.setProps({
                          getReferenceClientRect: function () {
                            const bt = C.getBoundingClientRect()
                            let Gt = ee
                            let Kt = ie
                            ve === 'initial' &&
                                    ((Gt = bt.left + Ge), (Kt = bt.top + fe))
                            const Jt = ve === 'horizontal' ? bt.top : Kt
                            const rt = ve === 'vertical' ? bt.right : Gt
                            const lt = ve === 'horizontal' ? bt.bottom : Kt
                            const yt = ve === 'vertical' ? bt.left : Gt
                            return {
                              width: rt - yt,
                              height: lt - Jt,
                              top: Jt,
                              right: rt,
                              bottom: lt,
                              left: yt
                            }
                          }
                        })
        }
        function Ae () {
          w.props.followCursor &&
                        (fn.push({ instance: w, doc: L }), On(L))
        }
        function Ie () {
          ((fn = fn.filter(function (re) {
            return re.instance !== w
          })),
          fn.filter(function (re) {
            return re.doc === L
          }).length === 0 && zr(L))
        }
        return {
          onCreate: Ae,
          onDestroy: Ie,
          onBeforeUpdate: function () {
            be = w.props
          },
          onAfterUpdate: function (he, ve) {
            const ee = ve.followCursor
            q ||
                            (ee !== void 0 &&
                                be.followCursor !== ee &&
                                (Ie(),
                                ee
                                  ? (Ae(),
                                    w.state.isMounted && !W && !le() && pe())
                                  : (ye(), Te())))
          },
          onMount: function () {
            w.props.followCursor &&
                            !W &&
                            (B && (je(xn), (B = !1)), le() || pe())
          },
          onTrigger: function (he, ve) {
            (X(ve) &&
                            (xn = {
                              clientX: ve.clientX,
                              clientY: ve.clientY
                            }),
            (W = ve.type === 'focus'))
          },
          onHidden: function () {
            w.props.followCursor && (Te(), ye(), (B = !0))
          }
        }
      }
    }
    function Yr (g, w) {
      let C
      return {
        popperOptions: Object.assign({}, g.popperOptions, {
          modifiers: [].concat(
            (
              ((C = g.popperOptions) == null
                ? void 0
                : C.modifiers) || []
            ).filter(function (L) {
              const q = L.name
              return q !== w.name
            }),
            [w]
          )
        })
      }
    }
    const Bn = {
      name: 'inlinePositioning',
      defaultValue: !1,
      fn: function (w) {
        const C = w.reference
        function L () {
          return !!w.props.inlinePositioning
        }
        let q
        let W = -1
        let B = !1
        const be = {
          name: 'tippyInlinePositioning',
          enabled: !0,
          phase: 'afterWrite',
          fn: function (je) {
            const Ae = je.state
            L() &&
                            (q !== Ae.placement &&
                                w.setProps({
                                  getReferenceClientRect: function () {
                                    return le(Ae.placement)
                                  }
                                }),
                            (q = Ae.placement))
          }
        }
        function le (Te) {
          return Xr(
            N(Te),
            C.getBoundingClientRect(),
            Y(C.getClientRects()),
            W
          )
        }
        function pe (Te) {
          ((B = !0), w.setProps(Te), (B = !1))
        }
        function ye () {
          B || pe(Yr(w.props, be))
        }
        return {
          onCreate: ye,
          onAfterUpdate: ye,
          onTrigger: function (je, Ae) {
            if (X(Ae)) {
              const Ie = Y(w.reference.getClientRects())
              const re = Ie.find(function (he) {
                return (
                  he.left - 2 <= Ae.clientX &&
                                    he.right + 2 >= Ae.clientX &&
                                    he.top - 2 <= Ae.clientY &&
                                    he.bottom + 2 >= Ae.clientY
                )
              })
              W = Ie.indexOf(re)
            }
          },
          onUntrigger: function () {
            W = -1
          }
        }
      }
    }
    function Xr (g, w, C, L) {
      if (C.length < 2 || g === null) return w
      if (C.length === 2 && L >= 0 && C[0].left > C[1].right) {
        return C[L] || w
      }
      switch (g) {
        case 'top':
        case 'bottom': {
          const q = C[0]
          const W = C[C.length - 1]
          const B = g === 'top'
          const be = q.top
          const le = W.bottom
          const pe = B ? q.left : W.left
          const ye = B ? q.right : W.right
          const Te = ye - pe
          const je = le - be
          return {
            top: be,
            bottom: le,
            left: pe,
            right: ye,
            width: Te,
            height: je
          }
        }
        case 'left':
        case 'right': {
          const Ae = Math.min.apply(
            Math,
            C.map(function (fe) {
              return fe.left
            })
          )
          const Ie = Math.max.apply(
            Math,
            C.map(function (fe) {
              return fe.right
            })
          )
          const re = C.filter(function (fe) {
            return g === 'left' ? fe.left === Ae : fe.right === Ie
          })
          const he = re[0].top
          const ve = re[re.length - 1].bottom
          const ee = Ae
          const ie = Ie
          const x = ie - ee
          const Ge = ve - he
          return {
            top: he,
            bottom: ve,
            left: ee,
            right: ie,
            width: x,
            height: Ge
          }
        }
        default:
          return w
      }
    }
    const qr = {
      name: 'sticky',
      defaultValue: !1,
      fn: function (w) {
        const C = w.reference
        const L = w.popper
        function q () {
          return w.popperInstance
            ? w.popperInstance.state.elements.reference
            : C
        }
        function W (pe) {
          return w.props.sticky === !0 || w.props.sticky === pe
        }
        let B = null
        let be = null
        function le () {
          const pe = W('reference')
            ? q().getBoundingClientRect()
            : null
          const ye = W('popper') ? L.getBoundingClientRect() : null;
          (((pe && Hn(B, pe)) || (ye && Hn(be, ye))) &&
                        w.popperInstance &&
                        w.popperInstance.update(),
          (B = pe),
          (be = ye),
          w.state.isMounted && requestAnimationFrame(le))
        }
        return {
          onMount: function () {
            w.props.sticky && le()
          }
        }
      }
    }
    function Hn (g, w) {
      return g && w
        ? g.top !== w.top ||
                      g.right !== w.right ||
                      g.bottom !== w.bottom ||
                      g.left !== w.left
        : !0
    }
    (dt.setDefaultProps({ render: ar }),
    (e.animateFill = dr),
    (e.createSingleton = fr),
    (e.default = dt),
    (e.delegate = qt),
    (e.followCursor = jn),
    (e.hideAll = lr),
    (e.inlinePositioning = Bn),
    (e.roundArrow = r),
    (e.sticky = qr))
  })
  const Si = Ho($o())
  const Ts = Ho($o())
  const Ps = (e) => {
    const t = { plugins: [] }
    const r = (i) => e[e.indexOf(i) + 1]
    if (
      (e.includes('animation') && (t.animation = r('animation')),
      e.includes('duration') && (t.duration = parseInt(r('duration'))),
      e.includes('delay'))
    ) {
      const i = r('delay')
      t.delay = i.includes('-')
        ? i.split('-').map((o) => parseInt(o))
        : parseInt(i)
    }
    if (e.includes('cursor')) {
      t.plugins.push(Ts.followCursor)
      const i = r('cursor');
      ['x', 'initial'].includes(i)
        ? (t.followCursor = i === 'x' ? 'horizontal' : 'initial')
        : (t.followCursor = !0)
    }
    (e.includes('on') && (t.trigger = r('on')),
    e.includes('arrowless') && (t.arrow = !1),
    e.includes('html') && (t.allowHTML = !0),
    e.includes('interactive') && (t.interactive = !0),
    e.includes('border') &&
                t.interactive &&
                (t.interactiveBorder = parseInt(r('border'))),
    e.includes('debounce') &&
                t.interactive &&
                (t.interactiveDebounce = parseInt(r('debounce'))),
    e.includes('max-width') && (t.maxWidth = parseInt(r('max-width'))),
    e.includes('theme') && (t.theme = r('theme')),
    e.includes('placement') && (t.placement = r('placement')))
    const n = {}
    return (
      e.includes('no-flip') &&
                (n.modifiers || (n.modifiers = []),
                n.modifiers.push({ name: 'flip', enabled: !1 })),
      (t.popperOptions = n),
      t
    )
  }
  function Ai (e) {
    (e.magic('tooltip', (t) => (r, n = {}) => {
      const i = n.timeout
      delete n.timeout
      const o = (0, Si.default)(t, {
        content: r,
        trigger: 'manual',
        ...n
      });
      (o.show(),
      setTimeout(() => {
        (o.hide(),
        setTimeout(() => o.destroy(), n.duration || 300))
      }, i || 2e3))
    }),
    e.directive(
      'tooltip',
      (
        t,
        { modifiers: r, expression: n },
        { evaluateLater: i, effect: o, cleanup: a }
      ) => {
        const c = r.length > 0 ? Ps(r) : {};
        (t.__x_tippy || (t.__x_tippy = (0, Si.default)(t, c)),
        a(() => {
          t.__x_tippy &&
                                (t.__x_tippy.destroy(), delete t.__x_tippy)
        }))
        const f = () => t.__x_tippy.enable()
        const d = () => t.__x_tippy.disable()
        const y = (m) => {
          m ? (f(), t.__x_tippy.setContent(m)) : d()
        }
        if (r.includes('raw')) y(n)
        else {
          const m = i(n)
          o(() => {
            m((O) => {
              typeof O === 'object'
                ? (t.__x_tippy.setProps(O), f())
                : y(O)
            })
          })
        }
      }
    ))
  }
  Ai.defaultProps = (e) => (Si.default.setDefaultProps(e), Ai)
  const Ms = Ai
  const Wo = Ms
  const Uo = () => ({
    toggle (e) {
      this.$refs.panel?.toggle(e)
    },
    open (e) {
      this.$refs.panel?.open(e)
    },
    close (e) {
      this.$refs.panel?.close(e)
    }
  })
  const Vo = () => ({
    form: null,
    isProcessing: !1,
    processingMessage: null,
    init () {
      const e = this.$el.closest('form');
      (e?.addEventListener('form-processing-started', (t) => {
        ((this.isProcessing = !0),
        (this.processingMessage = t.detail.message))
      }),
      e?.addEventListener('form-processing-finished', () => {
        this.isProcessing = !1
      }))
    }
  })
  const zo = ({ id: e }) => ({
    isOpen: !1,
    isWindowVisible: !1,
    livewire: null,
    textSelectionClosePreventionMouseDownHandler: null,
    textSelectionClosePreventionMouseUpHandler: null,
    textSelectionClosePreventionClickHandler: null,
    init () {
      this.$nextTick(() => {
        ((this.isWindowVisible = this.isOpen),
        this.setUpTextSelectionClosePrevention(),
        this.$watch(
          'isOpen',
          () => (this.isWindowVisible = this.isOpen)
        ))
      })
    },
    setUpTextSelectionClosePrevention () {
      const t = '.fi-modal-window'
      const r = '.fi-modal-close-overlay'
      let i = !1
      let o = 0
      this.textSelectionClosePreventionClickHandler = (c) => {
        (c.stopPropagation(),
        c.preventDefault(),
        document.removeEventListener(
          'click',
          this.textSelectionClosePreventionClickHandler,
          !0
        ))
      }
      const a = (c) =>
        !c.target.closest(t) &&
                (c.target.closest(r) || c.target.closest('body'));
      ((this.textSelectionClosePreventionMouseDownHandler = (c) => {
        ((o = Date.now()), (i = !!c.target.closest(t)))
      }),
      (this.textSelectionClosePreventionMouseUpHandler = (c) => {
        const f = Date.now() - o < 75;
        (i && a(c) && !f
          ? document.addEventListener(
            'click',
            this.textSelectionClosePreventionClickHandler,
            !0
          )
          : document.removeEventListener(
            'click',
            this.textSelectionClosePreventionClickHandler,
            !0
          ),
        (i = !1))
      }),
      document.addEventListener(
        'mousedown',
        this.textSelectionClosePreventionMouseDownHandler,
        !0
      ),
      document.addEventListener(
        'mouseup',
        this.textSelectionClosePreventionMouseUpHandler,
        !0
      ))
    },
    close () {
      (this.closeQuietly(), this.$dispatch('modal-closed', { id: e }))
    },
    closeQuietly () {
      this.isOpen = !1
    },
    open () {
      this.$nextTick(() => {
        ((this.isOpen = !0),
        document.dispatchEvent(
          new CustomEvent('x-modal-opened', {
            bubbles: !0,
            composed: !0,
            detail: { id: e }
          })
        ))
      })
    },
    destroy () {
      (this.textSelectionClosePreventionMouseDownHandler &&
                (document.removeEventListener(
                  'mousedown',
                  this.textSelectionClosePreventionMouseDownHandler,
                  !0
                ),
                (this.textSelectionClosePreventionMouseDownHandler = null)),
      this.textSelectionClosePreventionMouseUpHandler &&
                    (document.removeEventListener(
                      'mouseup',
                      this.textSelectionClosePreventionMouseUpHandler,
                      !0
                    ),
                    (this.textSelectionClosePreventionMouseUpHandler = null)),
      this.textSelectionClosePreventionClickHandler &&
                    (document.removeEventListener(
                      'click',
                      this.textSelectionClosePreventionClickHandler,
                      !0
                    ),
                    (this.textSelectionClosePreventionClickHandler = null)))
    }
  })
  document.addEventListener('livewire:init', () => {
    const e = (t) => {
      const r = Alpine.findClosest(t, (n) => n.__livewire)
      if (!r) throw 'Could not find Livewire component in DOM tree.'
      return r.__livewire
    }
    Livewire.hook(
      'commit',
      ({ component: t, commit: r, respond: n, succeed: i, fail: o }) => {
        n(() => {
          queueMicrotask(() => {
            if (!t.effects.html) {
              for (const [f, d] of Object.entries(
                t.effects.partials ?? {}
              )) {
                const y = Array.from(
                  t.el.querySelectorAll(
                                        `[wire\\:partial="${f}"]`
                  )
                ).filter((_) => e(_) === t)
                if (!y.length) continue
                if (y.length > 1) {
                  throw `Multiple elements found for partial [${f}].`
                }
                const m = y[0]
                const O = m.parentElement
                  ? m.parentElement.tagName.toLowerCase()
                  : 'div'
                const E = document.createElement(O);
                ((E.innerHTML = d), (E.__livewire = t))
                const S = E.firstElementChild;
                ((S.__livewire = t),
                window.Alpine.morph(m, S, {
                  updating: (_, I, $, A) => {
                    if (!a(_)) {
                      if (
                        (_.__livewire_replace ===
                                                        !0 &&
                                                        (_.innerHTML =
                                                            I.innerHTML),
                        _.__livewire_replace_self ===
                                                        !0)
                      ) {
                        return (
                          (_.outerHTML =
                                                            I.outerHTML),
                          A()
                        )
                      }
                      if (
                        _.__livewire_ignore ===
                                                        !0 ||
                                                    (_.__livewire_ignore_self ===
                                                        !0 && $(),
                                                    c(_) &&
                                                        _.getAttribute(
                                                          'wire:id'
                                                        ) !== t.id)
                      ) {
                        return A()
                      }
                      c(_) && (I.__livewire = t)
                    }
                  },
                  key: (_) => {
                    if (!a(_)) {
                      return _.hasAttribute(
                        'wire:key'
                      )
                        ? _.getAttribute('wire:key')
                        : _.hasAttribute('wire:id')
                          ? _.getAttribute(
                            'wire:id'
                          )
                          : _.id
                    }
                  },
                  lookahead: !1
                }))
              }
            }
          })
        })
        function a (f) {
          return typeof f.hasAttribute !== 'function'
        }
        function c (f) {
          return f.hasAttribute('wire:id')
        }
      }
    )
  })
  const Yo = (e, t, r) => {
    const n = (y, m) => {
      for (const O of y) {
        const E = i(O, m)
        if (E !== null) return E
      }
    }
    const i = (y, m) => {
      const O = y.match(/^[\{\[]([^\[\]\{\}]*)[\}\]](.*)/s)
      if (O === null || O.length !== 3) return null
      const E = O[1]
      const S = O[2]
      if (E.includes(',')) {
        const [_, I] = E.split(',', 2)
        if (I === '*' && m >= _) return S
        if (_ === '*' && m <= I) return S
        if (m >= _ && m <= I) return S
      }
      return E == m ? S : null
    }
    const o = (y) =>
      y.toString().charAt(0).toUpperCase() + y.toString().slice(1)
    const a = (y, m) => {
      if (m.length === 0) return y
      const O = {}
      for (const [E, S] of Object.entries(m)) {
        ((O[':' + o(E ?? '')] = o(S ?? '')),
        (O[':' + E.toUpperCase()] = S.toString().toUpperCase()),
        (O[':' + E] = S))
      }
      return (
        Object.entries(O).forEach(([E, S]) => {
          y = y.replaceAll(E, S)
        }),
        y
      )
    }
    const c = (y) =>
      y.map((m) => m.replace(/^[\{\[]([^\[\]\{\}]*)[\}\]]/, ''))
    let f = e.split('|')
    const d = n(f, t)
    return d != null
      ? a(d.trim(), r)
      : ((f = c(f)), a(f.length > 1 && t > 1 ? f[1] : f[0], r))
  }
  document.addEventListener('alpine:init', () => {
    (window.Alpine.plugin(oo),
    window.Alpine.plugin(ao),
    window.Alpine.plugin(fo),
    window.Alpine.plugin(jo),
    window.Alpine.plugin(Wo),
    window.Alpine.data('filamentDropdown', Uo),
    window.Alpine.data('filamentFormButton', Vo),
    window.Alpine.data('filamentModal', zo))
  })
  window.jsMd5 = Xo.md5
  window.pluralize = Yo
})()
/*! Bundled license information:

js-md5/src/md5.js:
  (**
   * [js-md5]{@link https://github.com/emn178/js-md5}
   *
   * @namespace md5
   * @version 0.8.3
   * @author Chen, Yi-Cyuan [emn178@gmail.com]
   * @copyright Chen, Yi-Cyuan 2014-2023
   * @license MIT
   *)

sortablejs/modular/sortable.esm.js:
  (**!
   * Sortable 1.15.6
   * @author	RubaXa   <trash@rubaxa.org>
   * @author	owenm    <owen23355@gmail.com>
   * @license MIT
   *)
*/
