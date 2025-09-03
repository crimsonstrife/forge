@php use App\Models\Sprint; @endphp
<div class="d-flex flex-column gap-4" x-data>
    {{-- Sprint header --}}
    <div class="rounded border bg-body-tertiary">
        <div class="px-3 py-2 d-flex align-items-center justify-content-between">
            <div>
                @if ($currentSprintId)
                    @php $activeSprint = $project->sprints()->whereKey($currentSprintId)->first(); @endphp
                    <div class="fw-semibold">{{ __('Active Sprint') }}{{ $activeSprint?->name ? ': '.$activeSprint->name : '' }}</div>
                    @if ($activeSprint && $activeSprint->start_date && $activeSprint->end_date)
                        <div class="small text-body-secondary">
                            {{ $activeSprint->start_date->toFormattedDateString() }} &rarr; {{ $activeSprint->end_date->toFormattedDateString() }}
                        </div>
                    @endif
                @else
                    <div class="fw-semibold">{{ __('No active sprint') }}</div>
                    <div class="small text-body-secondary">{{ __('Move to sprint is disabled until a sprint is activated.') }}</div>
                @endif
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="openSprints">{{ __('View sprints') }}</button>

                @if ($currentSprintId)
                    <button type="button" class="btn btn-outline-danger btn-sm" wire:click="openEndSprint">{{ __('End sprint') }}</button>
                @else
                    <button type="button" class="btn btn-outline-success btn-sm" wire:click="openCreateSprint">{{ __('New sprint') }}</button>
                @endif
            </div>
        </div>
    </div>

    {{-- Backlog --}}
    <div class="rounded border bg-body-tertiary">
        <div class="px-3 py-2 border-bottom d-flex align-items-center justify-content-between">
            <div class="fw-semibold">{{ __('Backlog') }}</div>
            <div class="small text-body-secondary">{{ count($backlog) }} {{ __('items') }}</div>
        </div>
        <div class="p-3">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-2">
                @foreach ($backlog as $item)
                    <div class="col">
                        <div class="card h-100" data-backlog-card data-issue-id="{{ $item['id'] }}" wire:key="backlog-{{ $item['id'] }}" style="--tier-color: {{ $item['type_color'] ?? '#607D8B' }};">
                            <div class="card-body p-3 d-flex flex-column">
                                <div class="small text-body-secondary d-flex align-items-center gap-2">
                                    <span class="fw-medium">{{ $item['key'] }}</span>
                                    <x-issues.tier-badge :color="$item['type_color'] ?? '#607D8B'" :icon="$item['type_icon'] ?? 'filter_none'"/>
                                </div>
                                <div class="mt-1 small">{{ $item['summary'] }}</div>

                                @if(($item['progress'] ?? null) !== null)
                                    <div class="mt-2">
                                        <div class="progress" style="height:4px;">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $item['progress'] }}%; background: {{ $item['type_color'] }};"></div>
                                        </div>
                                        <div class="mt-1 text-muted" style="font-size:10px;">
                                            {{ $item['children_done'] }} / {{ $item['children_total'] }} ({{ $item['progress'] }}%)
                                        </div>
                                    </div>
                                @endif

                                <div class="mt-2 d-flex align-items-center justify-content-end">
                                    @if ($currentSprintId)
                                        <button type="button" class="btn btn-outline-secondary btn-sm"
                                                x-on:click="window.Livewire.dispatch('scrum:move-to-sprint', {issueId: '{{ $item['id'] }}'})">
                                            {{ __('Add to sprint') }}
                                        </button>
                                    @else
                                        <span class="small text-body-secondary">{{ __('No active sprint') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Sprint board (columns by status) --}}
    <div class="row g-3">
        @foreach ($sprintColumns as $col)
            <div class="col-12 col-sm-6 col-lg-4" wire:key="col-{{ $col['id'] }}">
                <div class="rounded border bg-body-tertiary d-flex flex-column h-100">
                    <div class="px-3 py-2 border-bottom d-flex align-items-center justify-content-between">
                        <span class="small fw-semibold">{{ $col['name'] }}</span>
                        <span class="badge bg-body-secondary text-body">{{ count($sprintLists[$col['id']] ?? []) }}</span>
                    </div>

                    <div class="p-2 d-flex flex-column gap-2" data-sprint-col data-status-id="{{ $col['id'] }}">
                        @foreach (($sprintLists[$col['id']] ?? []) as $card)
                            <div class="card" data-sprint-card data-issue-id="{{ $card['id'] }}" wire:key="card-{{ $card['id'] }}" style="--tier-color: {{ $card['type_color'] ?? '#607D8B' }};">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center justify-content-between small text-body-secondary">
                                        <span class="fw-medium">{{ $card['key'] }}</span>
                                        <button type="button" class="btn btn-outline-secondary btn-sm"
                                                x-on:click="window.Livewire.dispatch('scrum:move-to-backlog', {issueId: '{{ $card['id'] }}'})">← {{ __('Backlog') }}</button>
                                    </div>

                                    <div class="mt-1 d-flex align-items-center justify-content-between gap-2 small">
                                        <span>{{ $card['summary'] }}</span>
                                        <x-issues.tier-badge :color="$card['type_color'] ?? '#607D8B'" :icon="$card['type_icon'] ?? 'filter_none'"/>
                                    </div>

                                    @if(($card['progress'] ?? null) !== null)
                                        <div class="mt-2">
                                            <div class="progress" style="height:4px;">
                                                <div class="progress-bar" role="progressbar" style="width: {{ $card['progress'] }}%; background: {{ $card['type_color'] }};"></div>
                                            </div>
                                            <div class="mt-1 text-muted" style="font-size:10px;">
                                                {{ $card['children_done'] }} / {{ $card['children_total'] }} ({{ $card['progress'] }}%)
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Create Sprint Modal --}}
    @if ($showCreateSprint)
        <div class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center z-3">
            <div class="position-absolute top-0 start-0 w-100 h-100 bg-black opacity-50" wire:click="$set('showCreateSprint', false)"></div>
            <div class="position-relative bg-body rounded border p-3" style="width:100%;max-width:640px;">
                <div class="fw-semibold mb-2">{{ __('Create sprint') }}</div>

                <div class="row g-2">
                    <div class="col-12">
                        <label class="form-label small">{{ __('Name') }}</label>
                        <input type="text" class="form-control" wire:model.defer="newSprint.name">
                        @error('newSprint.name') <div class="invalid-feedback d-block small">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label small">{{ __('Goal (optional)') }}</label>
                        <textarea rows="3" class="form-control" wire:model.defer="newSprint.goal"></textarea>
                        @error('newSprint.goal') <div class="invalid-feedback d-block small">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-6">
                        <label class="form-label small">{{ __('Start date') }}</label>
                        <input type="date" class="form-control" wire:model.defer="newSprint.start_date">
                        @error('newSprint.start_date') <div class="invalid-feedback d-block small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-6">
                        <label class="form-label small">{{ __('End date') }}</label>
                        <input type="date" class="form-control" wire:model.defer="newSprint.end_date">
                        @error('newSprint.end_date') <div class="invalid-feedback d-block small">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 form-check">
                        <input type="checkbox" id="start_now" class="form-check-input" wire:model.defer="newSprint.start_now">
                        <label for="start_now" class="form-check-label small">{{ __('Start sprint immediately') }}</label>
                    </div>
                </div>

                <div class="mt-3 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="$set('showCreateSprint', false)">{{ __('Cancel') }}</button>
                    <wa-button variant="brand" size="sm" wire:click="createSprint">{{ __('Create') }}</wa-button>
                </div>
            </div>
        </div>
    @endif

    {{-- End Sprint Modal --}}
    @if ($showEndSprint)
        <div class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center z-3">
            <div class="position-absolute top-0 start-0 w-100 h-100 bg-black opacity-50" wire:click="$set('showEndSprint', false)"></div>
            <div class="position-relative bg-body rounded border p-3" style="width:100%;max-width:640px;">
                <div class="fw-semibold mb-2">{{ __('End sprint') }}</div>
                <p class="small text-body-secondary mb-3">{{ __('Do you want to move incomplete issues back to the backlog?') }}</p>

                <div class="form-check">
                    <input type="checkbox" id="rollover" class="form-check-input" wire:model="rolloverIncomplete">
                    <label for="rollover" class="form-check-label small">{{ __('Move incomplete issues to backlog') }}</label>
                </div>

                <div class="mt-3 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="$set('showEndSprint', false)">{{ __('Cancel') }}</button>
                    <wa-button variant="danger" size="sm" wire:click="endCurrentSprint">{{ __('End sprint') }}</wa-button>
                </div>
            </div>
        </div>
    @endif

    {{-- View Sprints Modal --}}
    @if ($showSprintsModal)
        <div class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center z-3">
            <div class="position-absolute top-0 start-0 w-100 h-100 bg-black opacity-50" wire:click="$set('showSprintsModal', false)"></div>
            <div class="position-relative bg-body rounded border p-3" style="width:100%;max-width:880px;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="fw-semibold small">{{ __('Sprints') }}</div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="$set('showSprintsModal', false)">{{ __('Close') }}</button>
                        <button type="button" class="btn btn-outline-success btn-sm" wire:click="openCreateSprint">{{ __('New sprint') }}</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('State') }}</th>
                            <th>{{ __('Start') }}</th>
                            <th>{{ __('End') }}</th>
                            <th class="text-end"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($sprints as $s)
                            <tr>
                                <td>{{ $s['name'] }}</td>
                                <td><span class="badge bg-body-tertiary text-body text-uppercase">{{ $s['state'] }}</span></td>
                                <td>{{ $s['start_date'] ?? '—' }}</td>
                                <td>{{ $s['end_date'] ?? '—' }}</td>
                                <td class="text-end">
                                    @if ($s['state'] === \App\Enums\SprintState::Planned)
                                        <button type="button" class="btn btn-outline-primary btn-sm" wire:click="startSprint('{{ $s['id'] }}')">{{ __('Start') }}</button>
                                    @elseif ($s['state'] === \App\Enums\SprintState::Active)
                                        <button type="button" class="btn btn-outline-danger btn-sm" wire:click="openEndSprint">{{ __('End') }}</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center small text-body-secondary py-3">{{ __('No sprints yet.') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
    const initSprintSortable = () => {
        const groupName = 'sprint-board-{{ (string) $project->id }}';
        document.querySelectorAll('[data-sprint-col]').forEach(col => {
            if (col._sortable) { try { col._sortable.destroy(); } catch(e){} }
            col._sortable = new Sortable(col, {
                group: groupName,
                animation: 150,
                onAdd: (evt) => handleDrop(evt),
                onUpdate: (evt) => handleDrop(evt),
            });
        });
    };
    function handleDrop(evt){
        const issueId = evt.item?.dataset?.issueId;
        const toStatusId = parseInt(evt.to?.dataset?.statusId ?? 0, 10);
        if (!issueId || !toStatusId) return;
        window.Livewire.dispatch('scrum:status-changed', {issueId, toStatusId});
    }
    document.addEventListener('livewire:init', () => {
        initSprintSortable();
        if (window.Livewire?.hook) {
            Livewire.hook('message.processed', () => initSprintSortable());
        } else {
            document.addEventListener('livewire:update', () => initSprintSortable());
        }
    });
</script>
