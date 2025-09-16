<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container">
            <livewire:dashboard.overview />
        </div>
    </div>
</x-app-layout>
