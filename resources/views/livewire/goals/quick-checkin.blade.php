@php
    $isPercent = $kr->unit->value === 'percent';
@endphp

<div class="mt-2 border rounded p-2">
    <div class="d-flex align-items-center gap-3">
        @if($isPercent)
            <input type="range"
                   min="0" max="100" step="1"
                   class="form-range"
                   wire:model.live="value"
                   aria-label="Percent value">
            <span class="fw-semibold" style="min-width: 3.5rem; text-align:right;">
                {{ (int) $value }}%
            </span>
        @else
            <div class="input-group" style="max-width: 240px;">
                <span class="input-group-text">Value</span>
                <input type="number" step="any" class="form-control" wire:model.live="value">
            </div>
        @endif
    </div>

    <div class="mt-2">
        <input type="text" class="form-control" placeholder="Optional note…" wire:model.defer="note">
    </div>

    <div class="d-flex justify-content-end mt-2">
        <button type="button" class="btn btn-sm btn-primary" wire:click="save">Save Check-in</button>
    </div>
</div>
