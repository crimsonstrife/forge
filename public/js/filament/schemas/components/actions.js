const o = () => ({
    isSticky: !1,
    init() {
        this.evaluatePageScrollPosition();
    },
    evaluatePageScrollPosition() {
        const i = this.$el.getBoundingClientRect();
        const t = i.top > window.innerHeight;
        const e = i.top < window.innerHeight && i.bottom > window.innerHeight;
        this.isSticky = t || e;
    },
});
export { o as default };
