<div wire:poll.visible.5s="refreshLink" wire:key="repo-sync-{{ $link?->id ?? 'missing' }}">
    <div class="small text-muted">Sync status</div>
    <div class="fw-semibold">
        {{ $link?->last_sync_status ? strtoupper($link->last_sync_status) : '—' }}
    </div>

    <div class="small mt-1">
        <div>Initial import:
            @if($link?->initial_import_finished_at)
                <span class="text-success">Complete</span>
            @elseif($link?->initial_import_started_at)
                <span class="text-warning">In progress…</span>
            @else
                <span class="text-muted">Not started</span>
            @endif
        </div>

        @if($link?->last_sync_error)
            <div class="text-danger mt-1">
                {{ \Illuminate\Support\Str::limit($link->last_sync_error, 160) }}
            </div>
        @endif

        @if($link?->updated_at)
            <div class="text-muted mt-1">Updated {{ $link->updated_at->diffForHumans() }}</div>
        @endif
    </div>
</div>
