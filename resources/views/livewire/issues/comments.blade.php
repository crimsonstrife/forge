<div class="d-grid gap-3">
    <form wire:submit.prevent="add" class="d-flex gap-2">
        <wa-textarea class="form-control" wire:model.defer="body" rows="3" placeholder="Add a comment..."></wa-textarea>
        <button class="btn btn-primary align-self-end">{{ __('Post') }}</button>
    </form>

    <ul class="list-group">
        @forelse($comments as $c)
            <li class="list-group-item">
                <div class="d-flex gap-2">
                    <x-avatar :src="$c->user->profile_photo_url" :name="$c->user->name" preset="sm" />
                    <div>
                        <div class="small fw-semibold">
                            {{ $c->user->name }}
                            <span class="text-body-secondary fw-normal">· {{ $c->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="small">{{ $c->body }}</div>
                    </div>
                </div>
            </li>
        @empty
            <li class="list-group-item text-body-secondary small py-4">No comments yet.</li>
        @endforelse
    </ul>
</div>
