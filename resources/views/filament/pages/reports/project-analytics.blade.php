<x-filament-panels::page>
    {{ $this->form }}

    @if (blank($this->projectId) || (! $this->hasReportData()))
        <x-filament::section>
            <x-slot name="heading">No data yet</x-slot>

            <p class="text-sm text-gray-600 dark:text-gray-300">
                We couldn't find any snapshots for this date range. Once your nightly job runs or you backfill,
                charts will appear here.
            </p>

            <div class="mt-3 text-xs">
                Backfill tip:
                <code class="px-1 py-0.5 rounded bg-gray-100 dark:bg-gray-800">
                    php artisan reports:backfill --days=30
                </code>
            </div>
        </x-filament::section>
    @else
        <x-filament::section>
            <x-slot name="heading">Overview</x-slot>
            @livewire(\App\Filament\Widgets\ProjectHealthStats::class, ['projectId' => $this->projectId], key('stats-'.$this->projectId))
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Flow & Throughput</x-slot>
            @livewire(\App\Filament\Widgets\CumulativeFlowChart::class, ['projectId' => $this->projectId], key('cfd-'.$this->projectId))
            @livewire(\App\Filament\Widgets\ThroughputTrend::class, ['projectId' => $this->projectId], key('tp-'.$this->projectId))
        </x-filament::section>

        @livewire(\App\Filament\Widgets\AssigneeWorkloadTable::class, ['projectId' => $this->projectId], key('workload-'.$this->projectId))
    @endif
</x-filament-panels::page>
