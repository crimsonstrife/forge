@props(['id' => null, 'maxWidth' => null])

<x-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <div class="modal-header">
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10 text-danger fw-bold"
                 style="width: 2.5rem; height: 2.5rem;">
                !
            </div>
            <h5 class="modal-title">{{ $title }}</h5>
        </div>
        <button type="button" class="btn-close" x-on:click="show = false" aria-label="{{ __('Close') }}"></button>
    </div>

    <div class="modal-body">
        {{ $content }}
    </div>

    <div class="modal-footer">
        {{ $footer }}
    </div>
</x-modal>
