<div {{ $attributes->class('wa-avatar-group') }}>
    {{ $slot }}
</div>
@push('styles')
    <style>
        .wa-avatar-group wa-avatar:not(:first-of-type) {
            margin-left: calc(-1 * var(--wa-space-m));
        }
        .wa-avatar-group wa-avatar {
            border: 2px solid var(--wa-color-surface-default);
        }
    </style>
@endpush
