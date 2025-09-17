<div>
    @if($tickets->isEmpty())
        <p class="text-body-secondary">No tickets found for this access link.</p>
    @else
        <div class="list-group">
            @foreach($tickets as $t)
                <a href="{{ route('support.ticket.public', ['key' => $t->key]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold">{{ $t->key }} — {{ $t->subject }}</div>
                        <div class="small text-body-secondary">{{ $t->created_at->format('M j, Y g:ia') }}</div>
                    </div>
                    <span class="badge text-bg-secondary">{{ $t->status->name ?? '—' }}</span>
                </a>
            @endforeach
        </div>
    @endif
</div>
