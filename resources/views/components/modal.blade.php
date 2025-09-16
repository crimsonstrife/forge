@props(['id', 'maxWidth'])

@php
    $id = $id ?? md5($attributes->wire('model'));

    // Map Jetstream sizes to Bootstrap modal sizes
    $sizeClass = match ($maxWidth ?? '2xl') {
        'sm' => 'modal-sm',
        'md' => '',          // default
        'lg' => 'modal-lg',
        'xl', '2xl' => 'modal-xl',
        default => 'modal-xl',
    };
@endphp

<div
    x-data="{ show: @entangle($attributes->wire('model')) }"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-trap.inert.noscroll="show"
    x-show="show"
    id="{{ $id }}"
    class="modal d-block"
    tabindex="-1"
    role="dialog"
    aria-modal="true"
    style="display: none;"
    x-transition.opacity
>
    <!-- Backdrop -->
    <div class="modal-backdrop fade show"></div>

    <!-- Dialog -->
    <div class="modal-dialog modal-dialog-centered {{ $sizeClass }}">
        <div class="modal-content">
            {{ $slot }}
        </div>
    </div>
</div>
