<div class="d-grid gap-3">
    {{-- Top-level composer --}}
    <form wire:submit.prevent="add" class="d-grid gap-2">
        <x-editor.tiny
            :id="'issue-comment-'.$issue->id.'-'.$composerNonce"
            name="body"
            wireModel="body"
            :value="$body"
            :height="180"
            toolbar="undo redo | bold italic link | bullist numlist | mentionUser mentionIssue"
            plugins="link lists"
        />
        @error('body') <div class="form-text text-danger">{{ $message }}</div> @enderror
        <div class="d-flex align-items-center justify-content-between gap-3">
            <div class="small text-body-secondary">{{ __('Use @ to mention people and # to link issues.') }}</div>
            <button class="btn btn-primary">{{ __('Post') }}</button>
        </div>
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
