<div>
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="btn-group" role="group">
            <button wire:click="$set('groupBy','assignee')" class="btn btn-sm btn-outline-primary {{ $groupBy==='assignee' ? 'active' : '' }}">Group: Assignee</button>
            <button wire:click="$set('groupBy','status')"   class="btn btn-sm btn-outline-primary {{ $groupBy==='status' ? 'active' : '' }}">Group: Status</button>
        </div>
    </div>

    <div id="project-timeline-chart" wire:ignore style="min-height:480px;width:100%"></div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('livewire:navigated', renderTimeline);
        document.addEventListener('DOMContentLoaded', renderTimeline);

        let timelineChart;

        function renderTimeline() {
            const el = document.getElementById('project-timeline-chart');
            if (!el) return;

            const payload = @json($this->getChartData(), JSON_THROW_ON_ERROR);

            const options = {
                chart: { type: 'rangeBar', height: 480 },
                plotOptions: { bar: { horizontal: true, rangeBarGroupRows: true } },
                series: payload.series,
                xaxis: { type: 'datetime' },
                dataLabels: { enabled: false },
                tooltip: {
                    custom: ({seriesIndex, dataPointIndex, w}) => {
                        const d = w.config.series[seriesIndex].data[dataPointIndex];
                        const [start, end] = d.y;
                        return `<div class="p-2">
                        <div class="fw-bold">${d.x}</div>
                        <div>${new Date(start).toLocaleString()} → ${new Date(end).toLocaleString()}</div>
                    </div>`;
                    }
                }
            };

            if (timelineChart) timelineChart.destroy();
            timelineChart = new ApexCharts(el, options);
            timelineChart.render();
        }

        Livewire.hook('morph.updated', renderTimeline);
    </script>
@endpush
