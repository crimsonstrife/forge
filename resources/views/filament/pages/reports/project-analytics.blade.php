<x-filament-panels::page>
    {{ $this->form }}
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
</x-filament-panels::page>
