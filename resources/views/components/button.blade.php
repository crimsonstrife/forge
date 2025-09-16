@props(['variant' => 'brand', 'appearance' => null, 'type' => 'submit'])

<wa-button type="{{ $type }}" variant="{{ $variant }}" @if($appearance) appearance="{{ $appearance }}" @endif{{ $attributes->except(['class', 'variant', 'appearance', 'type']) }} class="{{ $attributes->get('class') }}">
    {{ $slot }}
</wa-button>
