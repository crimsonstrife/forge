<div class="vstack gap-3">
    <div class="card">
        <div class="card-body">
            <h2 class="h5 mb-1">{{ $ticket->key }} — {{ $ticket->subject }}</h2>
            <div class="small text-body-secondary mb-2">
                Opened {{ $ticket->created_at->diffForHumans() }} by {{ $ticket->submitter_name }}
            </div>
            <div class="border rounded p-3 bg-body-tertiary">
                {!! nl2br(e($ticket->redacted_body ?? '')) !!}
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header fw-semibold">Conversation</div>
        <div class="card-body vstack gap-3">
            @forelse($comments as $c)
                <div class="border rounded p-3">
                    <div class="small text-body-secondary mb-1">{{ $c->created_at->format('M j, Y g:ia') }}</div>
                    {!! nl2br(e($c->redacted_body ?? '')) !!}
                </div>
            @empty
                <div class="text-body-secondary">No replies yet.</div>
            @endforelse
        </div>
    </div>

    <form wire:submit.prevent="postReply" class="card">
        <div class="card-header fw-semibold">Add a reply</div>
        <div class="card-body vstack gap-2">
            <div class="alert alert-warning small mb-2">
                {{ app(\App\Settings\SupportSettings::class)->public_warning_text }}
            </div>
            <textarea rows="4" wire:model.defer="reply" class="form-control"></textarea>
            @error('reply') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        <div class="card-footer d-flex justify-content-end">
            <button class="btn btn-primary" type="submit">Send</button>
        </div>
    </form>
</div>
