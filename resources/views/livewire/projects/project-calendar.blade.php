<div>
    {{-- Filters area (optional, Bootstrap) --}}
    <div class="d-flex gap-2 mb-3">
        {{-- Add selects for status/assignee if desired --}}
    </div>

    <div wire:ignore id="project-calendar"></div>

    {{-- FullCalendar CDN --}}
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.js'></script>

    <script>
        document.addEventListener('livewire:navigated', initCalendar);
        document.addEventListener('DOMContentLoaded', initCalendar);

        function initCalendar() {
            const el = document.getElementById('project-calendar');
            if (!el || el.dataset.initialized) {
                return;
            }
            el.dataset.initialized = '1';

            const calendar = new FullCalendar.Calendar(el, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                editable: true,
                droppable: false,
                eventDrop(info) {
                    @this.
                    dispatch('calendar:move-issue', {
                        issueId: parseInt(info.event.id.replace('issue-', '')),
                        startIso: info.event.start?.toISOString() ?? null,
                        endIso: info.event.end?.toISOString() ?? null,
                    });
                },
                eventResize(info) {
                    @this.
                    dispatch('calendar:move-issue', {
                        issueId: parseInt(info.event.id.replace('issue-', '')),
                        startIso: info.event.start?.toISOString() ?? null,
                        endIso: info.event.end?.toISOString() ?? null,
                    });
                },
                events: @json($this->getEvents(), JSON_THROW_ON_ERROR),
                eventClick(info) {
                    if (info.event.url) {
                        window.location.href = info.event.url;
                        info.jsEvent.preventDefault();
                    }
                },
                height: 'auto'
            });

            calendar.render();

            // Optional: refresh events when Livewire refreshes
            Livewire.hook('morph.updated', () => {
                calendar.removeAllEvents();
                calendar.addEventSource(@json($this->getEvents()));
            });
        }
    </script>
</div>
