<div class="d-grid gap-3">
    {{-- Top-level composer --}}
    <form wire:submit.prevent="add" class="d-flex gap-2">
        <wa-textarea class="form-control" wire:model.defer="body" rows="3" placeholder="{{ __('Add a comment...') }}"></wa-textarea>
        <button class="btn btn-primary align-self-end">{{ __('Post') }}</button>
    </form>

    {{-- Threaded list --}}
    @if ($tree->isEmpty())
        <div class="text-body-secondary small py-4 text-center">No comments yet.</div>
    @else
        <ul class="list-group">
            @foreach ($tree as $c)
                @include('livewire.issues.partials.comment', ['c' => $c, 'level' => 0])
            @endforeach
        </ul>
    @endif
</div>
