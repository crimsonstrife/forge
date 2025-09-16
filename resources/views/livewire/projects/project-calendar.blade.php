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

                // Render chip + title
                eventContent: function(arg) {
                    const icon = arg.event.extendedProps?.typeIcon || 'filter_none';
                    const title = arg.event.title || '';
                    return {
                        html: `
                <div class="fc-issue">
                    <span class="issue-tier-badge"><i class="material-icons md-14">${icon}</i></span>
                    <span class="fc-issue-title">${title}</span>
                </div>
            `
                    };
                },

                // Color rail via CSS var
                eventDidMount: function(info) {
                    const color = info.event.extendedProps?.typeColor || '#607D8B';
                    info.el.style.setProperty('--tier-color', color);
                    info.el.classList.add('has-tier');
                },

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
@push('styles')
    <style>
        /* Tiny chip (same style as Kanban/Scrum) */
        .issue-tier-badge{
            display:inline-flex;align-items:center;gap:.375rem;
            padding:.125rem .5rem;border-radius:9999px;
            background: color-mix(in oklab, var(--tier-color) 14%, transparent);
            border:1px solid color-mix(in oklab, var(--tier-color) 32%, #0000);
            color: var(--tier-color);
            font-size:.75rem;line-height:1rem;font-weight:600;
        }
        .issue-tier-badge .material-icons.md-14{font-size:14px;line-height:14px}

        .fc-issue{display:flex;align-items:center;gap:.375rem}
        .fc-issue-title{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

        /* Left color rail on events */
        .fc .fc-event.has-tier{
            border-left: 4px solid var(--tier-color) !important;
        }
    </style>
@endpush
