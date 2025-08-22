function u({
    activeTab: a,
    isTabPersistedInQueryString: e,
    livewireId: h,
    tab: o,
    tabQueryStringKey: s,
}) {
    return {
        tab: o,
        init() {
            const t = this.getTabs();
            const i = new URLSearchParams(window.location.search);
            (e && i.has(s) && t.includes(i.get(s)) && (this.tab = i.get(s)),
                this.$watch("tab", () => this.updateQueryString()),
                (!this.tab || !t.includes(this.tab)) && (this.tab = t[a - 1]),
                Livewire.hook(
                    "commit",
                    ({
                        component: r,
                        commit: f,
                        succeed: c,
                        fail: l,
                        respond: b,
                    }) => {
                        c(({ snapshot: d, effect: m }) => {
                            this.$nextTick(() => {
                                if (r.id !== h) return;
                                const n = this.getTabs();
                                n.includes(this.tab) ||
                                    (this.tab = n[a - 1] ?? this.tab);
                            });
                        });
                    },
                ));
        },
        getTabs() {
            return this.$refs.tabsData
                ? JSON.parse(this.$refs.tabsData.value)
                : [];
        },
        updateQueryString() {
            if (!e) return;
            const t = new URL(window.location.href);
            (t.searchParams.set(s, this.tab),
                history.replaceState(null, document.title, t.toString()));
        },
    };
}
export { u as default };
