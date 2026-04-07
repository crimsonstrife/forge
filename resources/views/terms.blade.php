<x-guest-layout>
    <x-slot name="header">
        <h1 class="h3 mb-0">{{ __('Terms of Service') }}</h1>
    </x-slot>

    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                {!! $terms !!}
            </div>
        </div>
    </div>
</x-guest-layout>
