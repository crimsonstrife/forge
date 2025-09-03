@props(['id' => null, 'maxWidth' => null])

<x-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <div class="modal-header">
        <h5 class="modal-title">{{ $title }}</h5>
        <button type="button" class="btn-close" aria-label="Close" @click="$dispatch('close')"></button>
    </div>

    <div class="modal-body">
        {{ $content }}
    </div>

    <div class="modal-footer">
        {{ $footer }}
    </div>
</x-modal>
