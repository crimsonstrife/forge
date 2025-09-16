@props([
  'type' => 'button',
  'variant' => 'neutral',     // WA variants: brand, neutral, danger, etc.
  'appearance' => 'outlined', // solid | outlined | text
])

<wa-button
    type="{{ $type }}"
    variant="{{ $variant }}"
    appearance="{{ $appearance }}"
    {{ $attributes }}
>
    {{ $slot }}
</wa-button>
