<div>
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="btn-group" role="group">
            <button wire:click="$set('groupBy','assignee')" class="btn btn-sm btn-outline-primary {{ $groupBy==='assignee' ? 'active' : '' }}">Group: Assignee</button>
            <button wire:click="$set('groupBy','status')"   class="btn btn-sm btn-outline-primary {{ $groupBy==='status' ? 'active' : '' }}">Group: Status</button>
        </div>
    </div>
    <div class="d-flex align-items-center gap-3 small text-muted mt-2">
        <span><span class="legend-dot" style="--c:#7e57c2"></span> Epic</span>
        <span><span class="legend-dot" style="--c:#1e88e5"></span> Story</span>
        <span><span class="legend-dot" style="--c:#9e9e9e"></span> Task</span>
        <span><span class="legend-dot" style="--c:#78909C"></span> Sub-task</span>
    </div>
    <style>
        .legend-dot{display:inline-block;width:.65rem;height:.65rem;border-radius:50%;background:var(--c);margin-right:.25rem}
    </style>
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
                chart: {
                    type: 'rangeBar',
                    height: 480,
                    events: {
                        dataPointSelection: function (event, ctx, config) {
                            const d = config.w.config.series[config.seriesIndex].data[config.dataPointIndex];
                            if (d && d.url) { window.location = d.url; }
                        }
                    }
                },
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
