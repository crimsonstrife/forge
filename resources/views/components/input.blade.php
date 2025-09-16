@props(['disabled' => false])

<wa-input {{ $attributes->except('class') }} {{ $disabled ? 'disabled' : '' }}>
</wa-input>
