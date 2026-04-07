@props(['variant' => 'primary', 'appearance' => null, 'type' => 'submit'])

@php
    $bootstrapVariant = match ($variant) {
        'brand', 'primary' => 'primary',
        'neutral', 'secondary' => 'secondary',
        'danger' => 'danger',
        default => $variant,
    };

    $isOutline = in_array($appearance, ['outline', 'outlined'], true) || $variant === 'neutral';
    $classes = 'btn btn-' . ($isOutline ? 'outline-' : '') . $bootstrapVariant;
@endphp

<button type="{{ $type }}" {{ $attributes->except(['variant', 'appearance', 'type'])->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
