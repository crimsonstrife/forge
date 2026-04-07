@props(['submit'])

<div {{ $attributes->merge(['class' => 'row g-4 mb-4']) }}>
    <div class="col-md-4">
        <x-section-title>
            <x-slot name="title">{{ $title }}</x-slot>
            <x-slot name="description">{{ $description }}</x-slot>
        </x-section-title>
    </div>

    <div class="col-md-8">
        <form wire:submit="{{ $submit }}">
            <div class="card shadow-sm {{ isset($actions) ? 'rounded-bottom-0' : '' }}">
                <div class="card-body">
                    {{ $form }}
                </div>
            </div>

            @if (isset($actions))
                <div class="card border-top-0 rounded-top-0 shadow-sm">
                    <div class="card-footer d-flex justify-content-end gap-2">
                        {{ $actions }}
                    </div>
                </div>
            @endif
        </form>
    </div>
</div>
