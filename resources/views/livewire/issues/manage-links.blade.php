<div class="d-flex flex-column gap-3" style="width: 100%;">
    @can('link', $issue)
        <div class="border rounded p-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Link type</label>
                    <select class="form-select" wire:model="typeId">
                        <option value="">Select type…</option>
                        @foreach($this->types as $t)
                            <option value="{{ $t->id }}">
                                {{ $t->is_symmetric ? $t->name : ($t->name . ' / ' . $t->inverse_name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8 position-relative"
                     x-data="{
        open:false,
        activeIndex:-1,
        // Livewire ↔ Alpine state
        results: @entangle('results'),
        q: @entangle('q').live,

        openMenu(){ this.open = true },
        closeMenu(){ this.open = false; this.activeIndex = -1 },
        move(delta){
            const n = this.results?.length ?? 0;
            if (!n) return;
            this.open = true;
            this.activeIndex = ((this.activeIndex + delta) % n + n) % n;
            // allow scroll when using keyboard
            this.$nextTick(() => {
                const el = this.$refs.menu?.querySelector(`[data-idx='${this.activeIndex}']`);
                el?.scrollIntoView({ block: 'nearest' });
            });
        },
        chooseActive(){
            const r = this.results?.[this.activeIndex];
            if (r) { $wire.link(r.id); this.closeMenu(); }
        }
     }"
                     x-on:click.outside="closeMenu()"
                     x-effect="if ((q?.length ?? 0) >= 2 && (results?.length ?? 0) >= 0) open = true"
                >
                    <label class="form-label">Search issue</label>

                    <input type="text"
                           class="form-control"
                           placeholder="Key or summary…"
                           wire:model.live.debounce.300ms="q"
                           wire:input.debounce.300ms="search"
                           x-on:focus="openMenu()"
                           x-on:keydown.down.prevent="move(1)"
                           x-on:keydown.up.prevent="move(-1)"
                           x-on:keydown.enter.prevent="chooseActive()"
                           x-on:keydown.escape.prevent="closeMenu()"
                           aria-haspopup="listbox"
                           :aria-expanded="open ? 'true' : 'false'">

                    <!-- Results dropdown -->
                    <div x-cloak
                         x-show="open && (results?.length ?? 0) > 0"
                         x-ref="menu"
                         class="dropdown-menu show shadow position-absolute top-100 start-0 mt-1"
                         style="
        min-width: 100%;
        max-width: min(36rem, calc(100vw - 2rem));
        max-height: 18rem;
        overflow: auto;
        z-index: 1055;
     "
                         role="listbox"
                         aria-label="Link search results">
                        <template x-for="(r, idx) in results" :key="r.id">
                            <button type="button"
                                    role="option"
                                    class="dropdown-item px-3 py-2"
                                    :class="{ 'active': activeIndex === idx }"
                                    :data-idx="idx"
                                    x-on:mouseenter="activeIndex = idx"
                                    x-on:click="$wire.link(r.id); closeMenu();">
                                <!-- 2-col grid: content wraps on the left, action on the right -->
                                <div class="d-grid" style="grid-template-columns: 1fr auto; align-items: start; gap: .5rem;">
                                    <div class="text-wrap text-break" style="white-space: normal; overflow-wrap: anywhere; word-break: break-word;">
                                        <span class="badge text-bg-light me-2" x-text="`${r.project_key} — ${r.key}`"></span>
                                        <span x-text="r.summary"></span>
                                    </div>
                                    <small class="text-body-secondary">Link</small>
                                </div>
                            </button>
                        </template>
                    </div>

                    <!-- Empty state -->
                    <div x-cloak
                         x-show="open && (q?.length ?? 0) >= 2 && (results?.length ?? 0) === 0"
                         class="dropdown-menu show position-absolute top-100 start-0 mt-1 p-2 text-body-secondary small"
                         style="min-width:100%; max-width:min(36rem, calc(100vw - 2rem)); z-index:1055;">
                        No matches found.
                    </div>

                    <!-- Optional: loading hint -->
                    <div wire:loading.delay
                         class="dropdown-menu show position-absolute top-100 start-0 mt-1 p-2 small"
                         style="min-width:100%; max-width:min(36rem, calc(100vw - 2rem)); z-index:1055;">
                        Searching…
                    </div>

                </div>
            </div>
        </div>
    @endcan

    <div class="row g-3">
        <div class="col-md-6">
            <h6 class="mb-2">Outgoing</h6>
            @if($outgoing->isEmpty())
                <div class="text-body-secondary small">No outgoing links.</div>
            @else
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                    @foreach($outgoing as $l)
                        <li class="d-flex justify-content-between align-items-start gap-2">
                            <div>
                                <span class="badge text-bg-light">{{ $l['label'] }}</span>
                                <a class="link-primary ms-2" href="{{ $l['url'] }}">
                                    {{ $l['issue_key'] }} — {{ $l['summary'] }}
                                </a>
                            </div>
                            @can('link', $issue)
                                <button class="btn btn-sm btn-outline-danger" wire:click="unlink('{{ $l['id'] }}')">Remove</button>
                            @endcan
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="col-md-6">
            <h6 class="mb-2">Incoming</h6>
            @if($incoming->isEmpty())
                <div class="text-body-secondary small">No incoming links.</div>
            @else
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                    @foreach($incoming as $l)
                        <li class="d-flex justify-content-between align-items-start gap-2">
                            <div>
                                <span class="badge text-bg-light">{{ $l['label'] }}</span>
                                <a class="link-primary ms-2" href="{{ $l['url'] }}">
                                    {{ $l['issue_key'] }} — {{ $l['summary'] }}
                                </a>
                            </div>
                            @can('link', $issue)
                                <button class="btn btn-sm btn-outline-danger" wire:click="unlink('{{ $l['id'] }}')">Remove</button>
                            @endcan
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
