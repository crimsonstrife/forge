<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <h2 class="h4 mb-0">{{ __('Team Settings') }}</h2>
            <a class="btn btn-sm btn-outline-primary" href="{{ route('teams.dashboard', ['team' => $team]) }}">
                {{ __('Team Dashboard') }}
            </a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container mx-auto py-4">
            <div class="d-grid gap-4">
                @livewire('teams.update-team-name-form', ['team' => $team])

                @livewire('teams.team-member-manager', ['team' => $team])

                @if (Gate::check('delete', $team) && ! $team->personal_team)
                    @livewire('teams.delete-team-form', ['team' => $team])
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
