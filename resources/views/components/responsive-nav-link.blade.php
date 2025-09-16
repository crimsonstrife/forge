@props(['active' => false])

@php
    $classes = 'dropdown-item';
    if ($active) { $classes .= ' active'; }
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
