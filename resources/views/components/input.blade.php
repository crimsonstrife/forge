@props([
    'disabled' => false,
    'label' => null,
    'viewable' => false,
])

@php
    $field = $attributes->wire('model')->value() ?: $attributes->get('name');
    $id = $attributes->get('id') ?: ($field ? str_replace(['.', '_'], '-', (string) $field) : uniqid('input-', false));
    $type = $attributes->get('type', 'text');
    $except = ['label', 'viewable'];
    if ($viewable) {
        $except[] = 'type';
    }
    $inputAttributes = $attributes
        ->except($except)
        ->merge(['id' => $id, 'class' => 'form-control']);
@endphp

@if ($label)
    <div>
        <label class="form-label fw-medium" for="{{ $id }}">{{ $label }}</label>
        @if ($viewable)
            <div class="input-group" x-data="{ show: false }">
                <input :type="show ? 'text' : @js($type)" {{ $disabled ? 'disabled' : '' }} {!! $inputAttributes !!}>
                <button type="button"
                        class="btn btn-outline-secondary"
                        x-on:click="show = ! show"
                        x-bind:aria-label="show ? @js(__('Hide password')) : @js(__('Show password'))">
                    <wa-icon x-show="!show" family="regular" name="eye"></wa-icon>
                    <wa-icon x-cloak x-show="show" family="regular" name="eye-slash"></wa-icon>
                </button>
            </div>
        @else
            <input {{ $disabled ? 'disabled' : '' }} {!! $inputAttributes !!}>
        @endif

        @if ($field)
            <x-input-error :for="$field" class="mt-1" />
        @endif
    </div>
@else
    @if ($viewable)
        <div class="input-group" x-data="{ show: false }">
            <input :type="show ? 'text' : @js($type)" {{ $disabled ? 'disabled' : '' }} {!! $inputAttributes !!}>
            <button type="button"
                    class="btn btn-outline-secondary"
                    x-on:click="show = ! show"
                    x-bind:aria-label="show ? @js(__('Hide password')) : @js(__('Show password'))">
                <wa-icon x-show="!show" family="regular" name="eye"></wa-icon>
                <wa-icon x-cloak x-show="show" family="regular" name="eye-slash"></wa-icon>
            </button>
        </div>
    @else
        <input {{ $disabled ? 'disabled' : '' }} {!! $inputAttributes !!}>
    @endif
@endif
