<div
    x-data="focusTimer({
        isRunning: @js($isRunning),
        initialElapsed: Math.max(0, Number(@js($elapsedSeconds ?? 0))),
    })"
    x-init="init()"
    class="rounded-lg border p-4 bg-white dark:bg-gray-800"
>
    <div class="flex items-center justify-between gap-3">
        <div class="text-3xl font-semibold tabular-nums" x-text="displayTime"></div>

        <div class="flex items-center gap-2">
            <template x-if="!isRunning">
                <button type="button" class="px-3 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700" @click="$wire.start()">Start</button>
            </template>

            <template x-if="isRunning">
                <button type="button" class="px-3 py-2 rounded bg-rose-600 text-white hover:bg-rose-700" @click="$wire.stop()">Stop</button>
            </template>

            <button type="button" class="px-3 py-2 rounded border" @click="openPopout()" title="Pop out timer">Pop out</button>

            <a
                href="{{ $publicUrl }}"
                class="px-3 py-2 rounded border"
                title="View-only URL (signed)"
                target="_blank" rel="noopener"
            >Public URL</a>
        </div>
    </div>

    <div class="mt-3">
        <label class="block text-sm font-medium mb-1">Notes</label>
        <textarea
            wire:model.debounce.750ms="runningNotes"
            class="w-full rounded border p-2 bg-white dark:bg-gray-900"
            rows="2"
            placeholder="What are you focusing on?"
            x-bind:disabled="!isRunning"></textarea>

        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Notes save automatically while the timer runs.</p>
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
