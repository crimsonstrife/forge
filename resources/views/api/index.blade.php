<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">{{ __('API Tokens') }}</h2>
    </x-slot>

    <div class="py-4">
        <div class="container" style="max-width: 900px">
            @livewire('api.api-token-manager')
        </div>
    </div>
</x-app-layout>
