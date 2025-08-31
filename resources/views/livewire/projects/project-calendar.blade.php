<div>
    <div class="d-flex gap-2 mb-3"></div>

    <div wire:ignore id="project-calendar"></div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.js"></script>
    <script>
        document.addEventListener('livewire:navigated', initCalendar);
        document.addEventListener('DOMContentLoaded', initCalendar);

        function initCalendar() {
            const el = document.getElementById('project-calendar');
            if (!el || el.dataset.initialized) return;
            el.dataset.initialized = '1';

            const calendar = new FullCalendar.Calendar(el, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                height: 'auto',
                editable: true,
                droppable: false,
                events: @json($this->getEvents(), JSON_THROW_ON_ERROR),
                eventClick(info) {
                    if (info.event.url) {
                        info.jsEvent.preventDefault();
                        window.location.href = info.event.url;
                    }
                },
                eventDrop(info) {
                    @this.
                    call('updateIssueDates',
                        parseInt(info.event.id.replace('issue-', '')),
                        info.event.start?.toISOString() ?? null,
                        info.event.end?.toISOString() ?? null
                    );
                },
                eventResize(info) {
                    @this.
                    call('updateIssueDates',
                        parseInt(info.event.id.replace('issue-', '')),
                        info.event.start?.toISOString() ?? null,
                        info.event.end?.toISOString() ?? null
                    );
                },
            });

            calendar.render();

            // Refresh events when Livewire morphs (e.g. filters)
            Livewire.hook('morph.updated', () => {
                calendar.removeAllEvents();
                calendar.addEventSource(@json($this->getEvents(), JSON_THROW_ON_ERROR));
            });
        }
    </script>
@endpush
