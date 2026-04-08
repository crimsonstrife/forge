@props(['team', 'component' => 'dropdown-link'])

@if ($team)
    <form method="POST" action="{{ route('current-team.update') }}" x-data>
        @method('PUT')
        @csrf

        <input type="hidden" name="team_id" value="{{ $team->id }}">

        <x-dynamic-component :component="$component" href="#" x-on:click.prevent="$root.submit();">
            <div class="d-flex align-items-center gap-2">
                @if (Auth::user()?->isCurrentTeam($team))
                    <span class="text-success" aria-hidden="true">✓</span>
                @endif
                <span class="text-truncate">{{ $team->name }}</span>
            </div>
        </x-dynamic-component>
    </form>
@endif
