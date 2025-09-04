<div
    x-data="focusTimer({
        isRunning: @js($isRunning),
        initialElapsed: Math.max(0, Number(@js($elapsedSeconds ?? 0))),
    })"
    x-init="init()"
    class="card"
>
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between gap-3">
            <div class="fs-2 fw-semibold" x-text="displayTime"></div>

            <div class="d-flex align-items-center gap-2">
                <template x-if="!isRunning">
                    <button type="button" class="btn btn-primary" @click="$wire.start()">Start</button>
                </template>
                <template x-if="isRunning">
                    <button type="button" class="btn btn-danger" @click="$wire.stop()">Stop</button>
                </template>
                <button type="button" class="btn btn-outline-secondary" @click="openPopout()" title="Pop out timer">Pop out</button>
                <a href="{{ $publicUrl }}" class="btn btn-outline-secondary" target="_blank" rel="noopener">Public URL</a>
            </div>
        </div>

        <div class="mt-3">
            <label class="form-label">Notes</label>
            <wa-textarea class="form-control" wire:model.debounce.750ms="runningNotes" rows="3" placeholder="What are you focusing on?" x-bind:disabled="!isRunning"></wa-textarea>
            <div class="form-text">Notes save automatically while the timer runs.</div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('focusTimer', (opts) => ({
            isRunning: !!opts.isRunning,
            seconds: Math.max(0, Number(opts.initialElapsed || 0)),
            intervalId: null,
            displayTime: '00:00:00',

            init() {
                this.displayTime = this.format(this.seconds);
                if (this.isRunning) { this.beginTicking(); }

                window.addEventListener('timer-started', () => { this.isRunning = true; this.seconds = 0; this.beginTicking(); });
                window.addEventListener('timer-stopped', () => { this.isRunning = false; this.stopTicking(); });

                window.addEventListener('timer-elapsed', (e) => {
                    const serverSeconds = Math.max(0, Number(e.detail?.seconds || 0));
                    if (serverSeconds > this.seconds) {
                        this.seconds = serverSeconds;
                        this.displayTime = this.format(this.seconds);
                    }
                });

                setInterval(() => { Livewire.dispatch('timer-tick'); }, 5000);
            },

            beginTicking() {
                this.stopTicking();
                this.intervalId = setInterval(() => {
                    this.seconds += 1;
                    if (this.seconds < 0) this.seconds = 0;
                    this.displayTime = this.format(this.seconds);
                }, 1000);
            },

            stopTicking() {
                if (this.intervalId) { clearInterval(this.intervalId); this.intervalId = null; }
            },

            format(total) {
                total = Math.max(0, Math.floor(Number(total) || 0));
                const h = Math.floor(total / 3600).toString().padStart(2, '0');
                const m = Math.floor((total % 3600) / 60).toString().padStart(2, '0');
                const s = Math.floor(total % 60).toString().padStart(2, '0');
                return `${h}:${m}:${s}`;
            },

            openPopout() {
                const url = @js($focusUrl);
                window.open(url, 'focusTimer', 'width=420,height=220,menubar=no,toolbar=no,resizable=yes,scrollbars=no');
            },
        }));
    });
</script>
