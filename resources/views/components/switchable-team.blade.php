@props(['team', 'component' => 'dropdown-link'])

<form method="POST" action="{{ route('current-team.update') }}" x-data>
    @method('PUT')
    @csrf

    <input type="hidden" name="team_id" value="{{ $team->id }}">

    <x-dynamic-component :component="$component" href="#" x-on:click.prevent="$root.submit();">
        <div class="d-flex align-items-center">
            @if (Auth::user()->isCurrentTeam($team))
                <wa-icon class="me-2" name="check-circle"></wa-icon>
            @endif
            <div class="text-truncate">{{ $team->name }}</div>
        </div>
    </x-dynamic-component>
</form>
