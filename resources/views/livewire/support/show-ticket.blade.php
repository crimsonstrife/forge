<div class="vstack gap-3">
    <div class="card">
        <div class="card-body">
            <h2 class="h5 mb-1 d-flex align-items-center gap-2">
                {{ $ticket->key }} — {{ $ticket->subject }}
                <span class="badge text-bg-secondary">{{ $ticket->status->name ?? '—' }}</span>
            </h2>

            <div class="small text-body-secondary mb-2">
                Product: {{ $ticket->product->name ?? '—' }} · Opened {{ $ticket->created_at->diffForHumans() }} by {{ $ticket->submitter_name }}
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
                @php
                    $name = $c->user?->name ?? $ticket->submitter_name;
                    $email = $c->user?->email ?? $ticket->submitter_email;
                    $initials = collect(preg_split('/\s+/', trim($name)))->filter()->map(fn($p) => mb_substr($p, 0, 1))->take(2)->implode('');
                    $avatar = $c->user?->profile_photo_url ?? ( ($email && $email !== '')
                        ? 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($email))) . '?s=64&d=404'
                        : null
                    );
                @endphp

                <div class="border rounded p-3 d-flex gap-3 align-items-start">
                    <div style="width:40px;height:40px;">
                        @if($avatar)
                            <img src="{{ $avatar }}" alt="" width="40" height="40" class="rounded-circle"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        @else
                            <div class="rounded-circle bg-secondary-subtle border d-flex align-items-center justify-content-center"
                                 style="width:40px;height:40px; {{ $avatar ? 'display:none;' : '' }}">
                                <span class="fw-semibold">{{ $initials }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <div class="fw-semibold">{{ $name }}</div>
                            <div class="small text-body-secondary">{{ $c->created_at->format('M j, Y g:ia') }}</div>
                        </div>
                        <div class="mt-1">{!! nl2br(e($c->redacted_body ?? '')) !!}</div>
                    </div>
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
