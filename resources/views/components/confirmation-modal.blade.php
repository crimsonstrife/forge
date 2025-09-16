@props(['id' => null, 'maxWidth' => null])

<x-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <div class="modal-header">
        <h5 class="modal-title">{{ $title }}</h5>
        <button type="button" class="btn-close" aria-label="Close" @click="$dispatch('close')"></button>
    </div>

    <div class="modal-body">
        <div class="d-sm-flex align-items-start gap-3">
            <div class="d-flex align-items-center justify-content-center rounded-circle bg-danger-subtle text-danger flex-shrink-0" style="width: 40px; height: 40px;">
                <wa-icon name="triangle-exclamation"></wa-icon>
            </div>
            <div class="mt-3 mt-sm-0">
                <div class="text-body">{{ $title }}</div>
                <div class="text-body-secondary small mt-2">
                    {{ $content }}
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        {{ $footer }}
    </div>
</x-modal>
