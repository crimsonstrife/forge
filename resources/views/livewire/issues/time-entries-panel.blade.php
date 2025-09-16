<div class="card">
    <div class="card-body d-flex align-items-center justify-content-between">
        <div>
            <h3 class="h6 mb-1">Time Tracked</h3>
            <div class="fs-4 fw-bold">{{ gmdate('H:i:s', max(0, $totalSeconds)) }}</div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="text-body-secondary small">Scope:</span>
            <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="toggleScope">
                {{ $showAll ? 'All Users' : 'Only Me' }}
            </button>
        </div>
    </div>

    <div class="table-responsive border-top">
        <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
            <tr>
                <th>Started</th>
                <th>Ended</th>
                <th>Duration</th>
                <th>Source</th>
                @if ($showAll)
                    <th>User</th>
                @endif
                <th>Notes</th>
                <th class="text-end"></th>
            </tr>
            </thead>
            <tbody>
            @forelse ($entries as $row)
                <tr class="{{ $row['is_running'] ? 'table-warning' : '' }}">
                    <td class="text-nowrap">{{ $row['started_at'] }}</td>
                    <td class="text-nowrap">{{ $row['ended_at'] ?? '—' }}</td>
                    <td class="fw-semibold">{{ gmdate('H:i:s', max(0, $row['duration_seconds'])) }}
                        @if($row['is_running'])
                            <span class="badge text-bg-secondary ms-2">Running</span>
                        @endif
                    </td>
                    <td>{{ ucfirst($row['source']) }}</td>
                    @if ($showAll)
                        <td>{{ $row['user'] }}</td>
                    @endif
                    <td style="max-width: 28rem;">
                        @if ($editingId === $row['id'])
                            <textarea wire:model.defer="editingNotes" rows="2" class="form-control"></textarea>
                            <div class="mt-1 d-flex gap-2">
                                <button type="button" class="btn btn-primary btn-sm" wire:click="saveNote">Save</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="cancelEdit">Cancel</button>
                            </div>
                        @else
                            <div class="text-truncate" title="{{ $row['notes'] ?? '' }}">{{ $row['notes'] ?? '—' }}</div>
                        @endif
                    </td>
                    <td class="text-end">
                        @if ($row['is_running'])
                            <button type="button" class="btn btn-danger btn-sm" wire:click="stopEntry('{{ $row['id'] }}')">Stop</button>
                        @else
                            <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="edit('{{ $row['id'] }}')">Edit</button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="text-center text-body-secondary" colspan="{{ $showAll ? 7 : 6 }}">
                        No time tracked yet.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
