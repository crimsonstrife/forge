@props([
    'label' => null,
    'switch' => false,  // set to true for a switch style
    // auto id if not provided
    'id' => $attributes->get('id') ?? uniqid('wa_chk_', true),
])

@php
    $name = $attributes->get('name'); // optional; Livewire works without it
@endphp

<div {{ $attributes->class([
        'form-check',
        'form-switch' => (bool) $switch,
    ])->except(['id','class','label','switch','name']) }}
>
    <input
        id="{{ $id }}"
        type="checkbox"
        @if($name) name="{{ $name }}" @endif
        {{ $attributes->merge(['class' => 'form-check-input']) }}
    >

    @if ($label)
        <label class="form-check-label" for="{{ $id }}">
            {{ $label }}
        </label>
    @endif
</div>
