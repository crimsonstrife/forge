<x-guest-layout>
    <x-slot name="header">
        <h1 class="h3 mb-0">{{ __('Cookie Policy') }}</h1>
    </x-slot>

    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                {!! $cookies !!}
            </div>
        </div>
    </div>
</x-guest-layout>
