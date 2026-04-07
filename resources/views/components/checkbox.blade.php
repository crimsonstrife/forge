@props([
    'label' => null,
    'switch' => false,
    'id' => null,
    'disabled' => false,
])

@php
    $name = $attributes->get('name');
    $model = $attributes->wire('model')->value();
    $id = $id ?? $attributes->get('id') ?? ($name ?: ($model ? str_replace(['.', '_'], '-', (string) $model) : uniqid('checkbox-', false)));
    $inputAttributes = $attributes
        ->except(['label', 'switch'])
        ->merge(['id' => $id, 'class' => 'form-check-input']);
@endphp

@if ($label)
    <div class="form-check {{ $switch ? 'form-switch' : '' }}">
        <input type="checkbox" {{ $disabled ? 'disabled' : '' }} {!! $inputAttributes !!}>
        <label class="form-check-label" for="{{ $id }}">
            {{ $label }}
        </label>
    </div>
@else
    <input type="checkbox" {{ $disabled ? 'disabled' : '' }} {!! $inputAttributes !!}>
@endif
