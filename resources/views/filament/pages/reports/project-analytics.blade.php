<x-filament-panels::page>
    {{ $this->form }}

    @php
        $widgetKey = ($this->projectId ?? 'none').'-'.($this->dateFrom ?? 'na').'-'.($this->dateTo ?? 'na');
    @endphp

    @if (blank($this->projectId))
        <x-filament::section>
            <x-slot name="heading">Select a project</x-slot>

            <p class="text-sm text-gray-600 dark:text-gray-300">
                Choose a project to load operational reporting.
            </p>
        </x-filament::section>
    @else
        @if (! $this->hasReportData())
            <x-filament::section>
                <x-slot name="heading">Snapshot data is still warming up</x-slot>

                <p class="text-sm text-gray-600 dark:text-gray-300">
                    Current-state widgets can still render, but historical charts will stay sparse until reporting jobs
                    build project and sprint snapshots for this range.
                </p>

                <div class="mt-3 text-xs">
                    Backfill tip:
                    <code class="px-1 py-0.5 rounded bg-gray-100">
                        php artisan reports:backfill --days=30
                    </code>
                    <code class="px-1 py-0.5 rounded bg-gray-100">
                        php artisan reports:compute-metrics
                    </code>
                </div>
            </x-filament::section>
        @endif

        <div class="space-y-6">
            <x-filament::section>
                <x-slot name="heading">Overview</x-slot>

                <div class="grid gap-6 xl:grid-cols-2">
                    @livewire(\App\Filament\Widgets\ProjectHealthStats::class, ['projectId' => $this->projectId, 'dateFrom' => $this->dateFrom, 'dateTo' => $this->dateTo], key('overview-'.$widgetKey))
                    @livewire(\App\Filament\Widgets\LeadTimeStats::class, ['projectId' => $this->projectId, 'dateFrom' => $this->dateFrom, 'dateTo' => $this->dateTo], key('lead-time-'.$widgetKey))
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">Delivery</x-slot>

                <div class="grid gap-6 xl:grid-cols-2">
                    @livewire(\App\Filament\Widgets\ThroughputTrend::class, ['projectId' => $this->projectId, 'dateFrom' => $this->dateFrom, 'dateTo' => $this->dateTo], key('throughput-'.$widgetKey))
                    @livewire(\App\Filament\Widgets\BurnupTrend::class, ['projectId' => $this->projectId, 'dateFrom' => $this->dateFrom, 'dateTo' => $this->dateTo], key('burnup-'.$widgetKey))
                    @livewire(\App\Filament\Widgets\VelocityTrend::class, ['projectId' => $this->projectId, 'dateFrom' => $this->dateFrom, 'dateTo' => $this->dateTo], key('velocity-'.$widgetKey))
                    @livewire(\App\Filament\Widgets\SprintBurndown::class, ['projectId' => $this->projectId], key('burndown-'.$widgetKey))
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">Flow Health</x-slot>

                <div class="grid gap-6 xl:grid-cols-2">
                    @livewire(\App\Filament\Widgets\CumulativeFlowChart::class, ['projectId' => $this->projectId, 'dateFrom' => $this->dateFrom, 'dateTo' => $this->dateTo], key('cfd-'.$widgetKey))
                    @livewire(\App\Filament\Widgets\CycleTimeHistogram::class, ['projectId' => $this->projectId, 'dateFrom' => $this->dateFrom, 'dateTo' => $this->dateTo], key('cycle-'.$widgetKey))
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">Operational Risk</x-slot>

                <div class="grid gap-6 xl:grid-cols-2">
                    @livewire(\App\Filament\Widgets\WipAgingTable::class, ['projectId' => $this->projectId, 'dateTo' => $this->dateTo], key('aging-'.$widgetKey))
                    @livewire(\App\Filament\Widgets\OverdueWorkTable::class, ['projectId' => $this->projectId, 'dateTo' => $this->dateTo], key('overdue-'.$widgetKey))
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">Team Load</x-slot>

                @livewire(\App\Filament\Widgets\AssigneeWorkloadTable::class, ['projectId' => $this->projectId, 'dateTo' => $this->dateTo], key('workload-'.$widgetKey))
            </x-filament::section>
        </div>
    @endif
</x-filament-panels::page>
