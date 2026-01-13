function n ({ initialHeight: e, shouldAutosize: i, state: h }) {
  return {
    state: h,
    wrapperEl: null,
    init () {
      ((this.wrapperEl = this.$el.parentNode),
      this.setInitialHeight(),
      i
        ? this.$watch('state', () => {
          this.resize()
        })
        : this.setUpResizeObserver())
    },
    setInitialHeight () {
      this.$el.scrollHeight <= 0 ||
                (this.wrapperEl.style.height = e + 'rem')
    },
    resize () {
      if (this.$el.scrollHeight <= 0) return
      const t = this.$el.style.height
      this.$el.style.height = '0px'
      const r = this.$el.scrollHeight
      this.$el.style.height = t
      const l =
                parseFloat(e) *
                parseFloat(getComputedStyle(document.documentElement).fontSize)
      const s = Math.max(r, l) + 'px'
      this.wrapperEl.style.height !== s &&
                (this.wrapperEl.style.height = s)
    },
    setUpResizeObserver () {
      new ResizeObserver(() => {
        this.wrapperEl.style.height = this.$el.style.height
      }).observe(this.$el)
    }
  }
}
export { n as default }
