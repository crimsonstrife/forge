@php
    use App\Models\{Project, Organization, Issue, Goal};
    /** @var \App\Models\User|null $user */
    $user = auth()->user();
@endphp

<nav class="navbar navbar-expand-md bg-body border-bottom" x-data>
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ $user ? route('dashboard') : url('/') }}">
            <x-application-mark />
        </a>

        <!-- Toggler -->
        <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#appNavbar"
                aria-controls="appNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Nav contents -->
        <div class="collapse navbar-collapse d-md-flex" id="appNavbar">
            <!-- Left: main nav -->
            <ul class="navbar-nav me-auto mb-2 mb-md-0 align-items-md-center">
                @auth
                    <li class="nav-item">
                        <x-nav-link href="{{ $user ? route('dashboard') : url('/') }}" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    </li>

                    <!-- Projects -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="projectsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ __('Projects') }}
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="projectsDropdown">
                            <li>
                                <x-dropdown-link href="{{ Route::has('projects.index') ? route('projects.index') : url('/projects') }}">
                                    {{ __('Browse projects') }}
                                </x-dropdown-link>
                            </li>
                            @auth
                                <li>
                                    <x-dropdown-link href="{{ Route::has('projects.mine') ? route('projects.mine') : url('/projects?filter=mine') }}">
                                        {{ __('My projects') }}
                                    </x-dropdown-link>
                                </li>
                            @endauth
                            @can('create', Project::class)
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <x-dropdown-link href="{{ Route::has('projects.create') ? route('projects.create') : url('/projects/create') }}">
                                        {{ __('New project') }}
                                    </x-dropdown-link>
                                </li>
                            @endcan
                        </ul>
                    </li>

                    <!-- Organizations -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="orgDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ __('Organizations') }}
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="orgDropdown">
                            <li>
                                <x-dropdown-link href="{{ Route::has('organizations.index') ? route('organizations.index') : url('/organizations') }}">
                                    {{ __('Browse organizations') }}
                                </x-dropdown-link>
                            </li>
                            @can('create', Organization::class)
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <x-dropdown-link href="{{ Route::has('organizations.create') ? route('organizations.create') : url('/organizations/create') }}">
                                        {{ __('New organization') }}
                                    </x-dropdown-link>
                                </li>
                            @endcan
                        </ul>
                    </li>

                    <!-- Goals -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="goalDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ __('Goals') }}
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="goalDropdown">
                            <li>
                                <x-dropdown-link href="{{ Route::has('goals.index') ? route('goals.index') : url('/goals') }}">
                                    {{ __('Browse goals') }}
                                </x-dropdown-link>
                            </li>
                            @can('create', Goal::class)
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <x-dropdown-link href="{{ Route::has('goals.create') ? route('goals.create') : url('/goals/create') }}">
                                        {{ __('New goal') }}
                                    </x-dropdown-link>
                                </li>
                            @endcan
                        </ul>
                    </li>

                    <!-- Support/Service Desk -->
                    <li class="nav-item">
                        <x-nav-link href="{{ $user ? route('support.staff.index') : url('/support/staff') }}" :active="request()->routeIs('support.staff.index')">
                            {{ __('Support') }}
                        </x-nav-link>
                    </li>
                @elseguest
                    <!-- Support/Service Desk -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="supportDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ __('Support') }}
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="supportDropdown">
                            <li>
                                <x-dropdown-link href="{{ Route::has('support.index') ? route('support.index') : url('/support') }}">
                                    {{ __('Support Center') }}
                                </x-dropdown-link>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <x-dropdown-link href="{{ Route::has('support.my') ? route('support.my') : url('/support/my') }}">
                                    {{ __('My Tickets') }}
                                </x-dropdown-link>
                            </li>
                            <li>
                                <x-dropdown-link href="{{ Route::has('support.new') ? route('support.new') : url('/support/new') }}">
                                    {{ __('New Ticket') }}
                                </x-dropdown-link>
                            </li>
                        </ul>
                    </li>
                @endauth
            </ul>

            @auth
            <!-- Middle: global search -->
            <form action="{{ Route::has('search') ? route('search') : url('/search') }}" method="GET"
                  class="d-none d-md-flex align-items-center me-3">
                <label for="global-search" class="visually-hidden">{{ __('Search') }}</label>
                <input id="global-search" name="q" type="search"
                       class="form-control form-control-sm" style="width: 18rem;"
                       placeholder="{{ __('Search projects, issues, people…') }}" />
            </form>
            @endauth

            <!-- Right: actions -->
            <div class="d-flex align-items-center gap-2">
                @auth
                    <!-- Create -->
                    <div class="dropdown">
                        <wa-button class="dropdown-toggle" variant="brand" data-bs-toggle="dropdown" aria-expanded="false">
                            <wa-icon slot="start" name="plus"></wa-icon>
                            {{ __('Create') }}
                        </wa-button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @can('create', Issue::class)
                                <li><x-dropdown-link href="{{ Route::has('issues.create') ? route('issues.create') : url('/issues/create') }}">{{ __('New issue') }}</x-dropdown-link></li>
                            @endcan
                            @can('create', Project::class)
                                <li><x-dropdown-link href="{{ Route::has('projects.create') ? route('projects.create') : url('/projects/create') }}">{{ __('New project') }}</x-dropdown-link></li>
                            @endcan
                            @can('create', Organization::class)
                                <li><x-dropdown-link href="{{ Route::has('organizations.create') ? route('organizations.create') : url('/organizations/create') }}">{{ __('New organization') }}</x-dropdown-link></li>
                            @endcan
                            @can('create', Goal::class)
                                <li><x-dropdown-link href="{{ Route::has('goals.create') ? route('goals.create') : url('/goals/create') }}">{{ __('New goal') }}</x-dropdown-link></li>
                            @endcan
                        </ul>
                    </div>

                    <!-- Teams (auth-only) -->
                    @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                        @php($currentTeam = $user?->currentTeam)
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="teamsDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                {{ $currentTeam?->name ?? __('No team selected') }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="teamsDropdown">
                                <li class="px-3 py-2 text-muted small">{{ __('Manage Team') }}</li>

                                @if ($currentTeam)
                                    <li>
                                        <x-dropdown-link href="{{ route('teams.show', $currentTeam->id) }}">{{ __('Team Settings') }}</x-dropdown-link>
                                    </li>
                                @else
                                    <li class="px-3 py-2 small text-muted">{{ __('You are not in a team yet.') }}</li>
                                @endif

                                @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                    <li><x-dropdown-link href="{{ route('teams.create') }}">{{ __('Create New Team') }}</x-dropdown-link></li>
                                @endcan

                                @if ($user && ($user->allTeams()->count() > 1 || ($currentTeam === null && $user->allTeams()->count() >= 1)))
                                    <li><hr class="dropdown-divider"></li>
                                    <li class="px-3 py-2 text-muted small">{{ __('Switch Teams') }}</li>
                                    @foreach ($user->allTeams() as $team)
                                        <li><x-switchable-team :team="$team" /></li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    @endif

                    <!-- Settings / Profile -->
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2" type="button"
                                id="settingsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <x-avatar :src="$user?->profile_photo_url" :name="$user?->name" preset="md" />
                            @else
                                <span>{{ $user?->name }}</span>
                            @endif
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsDropdown" style="min-width: 14rem;">
                            <li class="px-3 py-2 text-muted small">{{ __('Manage Account') }}</li>
                            <li><x-dropdown-link href="{{ route('profile.show') }}">{{ __('Profile') }}</x-dropdown-link></li>
                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <li><x-dropdown-link href="{{ route('api-tokens.index') }}">{{ __('API Tokens') }}</x-dropdown-link></li>
                            @endif

                            <li><hr class="dropdown-divider"></li>

                            <!-- Dark mode toggle -->
                            <li class="px-3 py-2">
                                <div x-data="window.themeSwitcher()" x-init="switchTheme()" class="d-flex align-items-center justify-content-between">
                                    <span class="small text-muted">{{ __('Dark Mode') }}</span>
                                    <wa-switch :checked="switchOn" @click="switchOn = !switchOn; switchTheme()"></wa-switch>
                                </div>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            <!-- Logout -->
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item">{{ __('Log Out') }}</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <!-- Guest actions -->
                    <div x-data="window.themeSwitcher()" x-init="switchTheme()" class="d-none d-md-flex align-items-center gap-2 me-2">
                        <span class="small text-muted">{{ __('Dark Mode') }}</span>
                        <wa-switch :checked="switchOn" @click="switchOn = !switchOn; switchTheme()"></wa-switch>
                    </div>

                    @if (Route::has('login'))
                        <a class="btn btn-outline-secondary" href="{{ route('login') }}">{{ __('Log in') }}</a>
                    @endif
                    @if (Route::has('register'))
                        <a class="btn btn-primary" href="{{ route('register') }}">{{ __('Register') }}</a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</nav>

<script>
    // Quick focus for global search with '/'
    document.addEventListener('keydown', (e) => {
        if (['INPUT','TEXTAREA'].includes(document.activeElement.tagName)) return;
        if (e.key === '/') {
            e.preventDefault();
            const el = document.getElementById('global-search');
            if (el) el.focus();
        }
    });
</script>
