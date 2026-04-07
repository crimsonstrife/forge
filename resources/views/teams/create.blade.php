<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <h2 class="h4 mb-0">{{ __('Create Team') }}</h2>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container py-4">
            @livewire('teams.create-team-form')
        </div>
    </div>
</x-app-layout>
