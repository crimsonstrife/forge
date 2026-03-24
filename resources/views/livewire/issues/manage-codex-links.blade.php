@php
    /** @var \App\Models\Issue $issue */
    /** @var \Illuminate\Support\Collection $links */
@endphp

<div class="d-flex flex-column gap-3" style="width: 100%;">

    {{-- Search panel --}}
    @can('update', $issue)
    <div class="border rounded p-3"
         x-data="{
             open: false,
             activeIndex: -1,
             results: @entangle('results'),
             q: @entangle('search').live,
             openMenu() { this.open = true },
             closeMenu() { this.open = false; this.activeIndex = -1 },
             move(delta) {
                 const n = this.results?.length ?? 0;
                 if (!n) return;
                 this.open = true;
                 this.activeIndex = ((this.activeIndex + delta) % n + n) % n;
                 this.$nextTick(() => {
                     const el = this.$refs.menu?.querySelector(`[data-idx='${this.activeIndex}']`);
                     el?.scrollIntoView({ block: 'nearest' });
                 });
             },
             chooseActive() {
                 const r = this.results?.[this.activeIndex];
                 if (r) {
                     $wire.link(r.id, r.title, r.url, r.workspace_id, r.workspace_slug);
                     this.closeMenu();
                 }
             }
         }"
         x-on:click.outside="closeMenu()"
         x-effect="if ((q?.length ?? 0) >= 2 && (results?.length ?? 0) >= 0) open = true">

        <label class="form-label small fw-medium mb-1">
            <i class="fas fa-book me-1 text-primary"></i>Link a Codex page
        </label>

        @if($error)
            <div class="alert alert-warning py-2 small mb-2">{{ $error }}</div>
        @endif

        <div class="position-relative">
            <input type="text"
                   class="form-control form-control-sm"
                   placeholder="Search Codex pages…"
                   wire:model.live.debounce.400ms="search"
                   wire:input.debounce.400ms="searchPages"
                   x-on:focus="openMenu()"
                   x-on:keydown.down.prevent="move(1)"
                   x-on:keydown.up.prevent="move(-1)"
                   x-on:keydown.enter.prevent="chooseActive()"
                   x-on:keydown.escape.prevent="closeMenu()"
                   aria-haspopup="listbox"
                   :aria-expanded="open ? 'true' : 'false'">

            {{-- Spinner --}}
            <div wire:loading wire:target="searchPages"
                 class="position-absolute top-50 end-0 translate-middle-y me-2 text-body-secondary">
                <i class="fas fa-spinner fa-spin" style="font-size:0.75rem;"></i>
            </div>

            {{-- Results dropdown --}}
            <ul x-show="open && results.length > 0"
                x-ref="menu"
                x-transition
                class="position-absolute start-0 end-0 bg-body border rounded-2 shadow-sm list-unstyled mb-0 py-1 overflow-auto"
                style="top:calc(100% + 4px); max-height:14rem; z-index:1050;"
                role="listbox">
                <template x-for="(result, idx) in results" :key="result.id">
                    <li :data-idx="idx"
                        :class="{ 'bg-primary text-white': activeIndex === idx }"
                        class="px-3 py-2 cursor-pointer d-flex flex-column"
                        role="option"
                        style="font-size:0.83rem;"
                        x-on:click="$wire.link(result.id, result.title, result.url, result.workspace_id, result.workspace_slug); closeMenu();"
                        x-on:mouseenter="activeIndex = idx">
                        <span x-text="result.title" class="fw-medium text-truncate"></span>
                        <span x-text="result.workspace_name ?? result.workspace_slug"
                              :class="activeIndex === idx ? 'text-white-50' : 'text-body-secondary'"
                              style="font-size:0.72rem;"></span>
                    </li>
                </template>
            </ul>

            {{-- No results --}}
            <div x-show="open && search.length >= 2 && results.length === 0 && !$wire.searching"
                 class="position-absolute start-0 end-0 bg-body border rounded-2 shadow-sm px-3 py-2 small text-body-secondary"
                 style="top:calc(100% + 4px); z-index:1050;">
                No Codex pages found.
            </div>
        </div>
    </div>
    @endcan

    {{-- Linked pages list --}}
    @if($links->isNotEmpty())
        <div class="d-flex flex-column gap-1">
            @foreach($links as $link)
                <div class="d-flex align-items-center justify-content-between border rounded px-3 py-2">
                    <div class="d-flex align-items-start gap-2 overflow-hidden">
                        <i class="fas fa-book text-primary mt-1 flex-shrink-0" style="font-size:0.8rem;"></i>
                        <div class="overflow-hidden">
                            <a href="{{ $link->codex_page_url }}"
                               target="_blank"
                               rel="noopener"
                               class="fw-medium text-decoration-none text-truncate d-block"
                               style="font-size:0.85rem;">
                                {{ $link->codex_page_title }}
                                <i class="fas fa-external-link-alt ms-1" style="font-size:0.65rem; opacity:0.5;"></i>
                            </a>
                            <span class="text-body-secondary" style="font-size:0.72rem;">
                                Codex / {{ $link->codex_workspace_slug }}
                            </span>
                        </div>
                    </div>
                    @can('update', $issue)
                        <button type="button"
                                class="btn btn-link btn-sm text-danger p-0 ms-2 flex-shrink-0"
                                wire:click="unlink('{{ $link->id }}')"
                                wire:confirm="Remove this Codex page link?"
                                title="Remove link">
                            <i class="fas fa-times" style="font-size:0.75rem;"></i>
                        </button>
                    @endcan
                </div>
            @endforeach
        </div>
    @elseif(!config('codex.enabled'))
        <p class="text-body-secondary small mb-0">Codex integration is not enabled.</p>
    @endif
</div>
