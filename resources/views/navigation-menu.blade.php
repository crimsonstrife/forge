@php
    use App\Models\{Project, Organization, Issue, Goal, Ticket};
    /** @var \App\Models\User|null $user */
    $user = auth()->user();
    $allowReg = app(\App\Settings\AuthSettings::class)->allowRegistration ?? true;
    $canUseOnboarding = $user?->hasVerifiedEmail() ?? false;
@endphp
<nav class="navbar navbar-expand-md bg-body border-bottom forge-site-navbar" x-data>
    <div class="container mx-auto py-3 forge-site-navbar__inner">
        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center gap-2 forge-site-navbar__brand" href="{{ url('/') }}">
            <x-application-logo />
        </a>
        <!-- Toggler -->
        <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#appNavbar"
                data-tour="nav-toggle"
                aria-controls="appNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Nav contents -->
        <div class="collapse navbar-collapse forge-site-navbar__collapse" id="appNavbar">
            <div class="forge-site-navbar__links-row">
                <ul class="navbar-nav mb-0 align-items-md-center forge-site-navbar__links">
                    @auth
                        <li class="nav-item">
                            <x-nav-link href="{{ $user ? route('dashboard') : url('/') }}" :active="request()->routeIs('dashboard')">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                        </li>

                        <!-- Projects -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="projectsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-tour="projects-nav">
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

                        <li class="nav-item">
                            <x-nav-link href="{{ Route::has('issues.explorer') ? route('issues.explorer') : url('/issues') }}" :active="request()->routeIs('issues.explorer')" data-tour="issues-nav">
                                {{ __('Issues') }}
                            </x-nav-link>
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
                            <a class="nav-link dropdown-toggle" href="#" id="goalDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-tour="goals-nav">
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

                        <!-- Support -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('support.*') ? 'active' : '' }}"
                               href="#"
                               id="supportDropdownAuth"
                               role="button"
                               data-bs-toggle="dropdown"
                               aria-expanded="false"
                               data-tour="support-nav">
                                {{ __('Support') }}
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="supportDropdownAuth">
                                <li>
                                    <x-dropdown-link href="{{ Route::has('support.index') ? route('support.index') : url('/support') }}">
                                        {{ __('Support Portal') }}
                                    </x-dropdown-link>
                                </li>
                                <li>
                                    <x-dropdown-link href="{{ Route::has('support.my') ? route('support.my') : url('/support/my') }}">
                                        {{ __('My Tickets') }}
                                    </x-dropdown-link>
                                </li>
                                <li>
                                    <x-dropdown-link href="{{ Route::has('support.new') ? route('support.new') : url('/support/new') }}">
                                        {{ __('Submit Ticket') }}
                                    </x-dropdown-link>
                                </li>
                                @can('viewAny', Ticket::class)
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <x-dropdown-link href="{{ Route::has('support.staff.index') ? route('support.staff.index') : url('/support/staff') }}">
                                            {{ __('Support Triage') }}
                                        </x-dropdown-link>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @elseguest
                        <!-- Support -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('support.*') ? 'active' : '' }}" href="#" id="supportDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ __('Support') }}
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="supportDropdown">
                                <li>
                                    <x-dropdown-link href="{{ Route::has('support.index') ? route('support.index') : url('/support') }}">
                                        {{ __('Support Portal') }}
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
            </div>

            <div class="forge-site-navbar__utility-row">
                @auth
                    <form action="{{ Route::has('search') ? route('search') : url('/search') }}" method="GET"
                          data-tour="global-search"
                          class="d-none d-md-flex align-items-center forge-site-navbar__search">
                        <label for="global-search" class="visually-hidden">{{ __('Search') }}</label>
                        <input id="global-search" name="q" type="search"
                               class="form-control form-control-sm"
                               placeholder="{{ __('Search projects, issues, people…') }}" />
                    </form>
                @endauth

                <!-- Right: actions -->
                <div class="d-flex align-items-center gap-2 forge-site-navbar__actions">
                    @auth
                        <!-- Codex cross-app link (shown when Codex integration is configured) -->
                        @if(config('codex.enabled') && config('codex.url'))
                            @php
                                $forgeProject    = request()->route('project');
                                $codexSlug       = $forgeProject?->codex_workspace_slug;
                                $codexBase       = rtrim(config('codex.url'), '/');
                            @endphp
                            <a href="{{ $codexBase }}{{ $codexSlug ? '/workspaces/' . $codexSlug : '' }}"
                               class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                               target="_blank"
                               rel="noopener"
                               title="{{ $codexSlug ? __('Open linked Codex workspace') : __('Open Codex') }}">
                                <i class="fas fa-book" style="font-size:0.8rem;"></i>
                                {{ __('Docs') }}
                            </a>
                        @endif

                        <!-- Create -->
                        <div class="dropdown">
                            <wa-button id="recordCreate" class="dropdown-toggle" variant="brand" data-bs-toggle="dropdown" aria-expanded="false" data-tour="create-menu">
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
                                <button class="btn dropdown-toggle" type="button" id="teamsDropdown"
                                        data-bs-toggle="dropdown" aria-expanded="false" data-tour="teams-menu">
                                    {{ $currentTeam?->name ?? __('No team selected') }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="teamsDropdown">
                                    <li class="px-3 py-2 text-muted small">{{ __('Manage Team') }}</li>

                                    @if ($currentTeam)
                                        <li>
                                            <x-dropdown-link href="{{ route('teams.dashboard', ['team' => $currentTeam]) }}">{{ __('Team Dashboard') }}</x-dropdown-link>
                                        </li>
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

                        @auth
                            <livewire:notifications.menu />
                        @endauth

                        <!-- Settings / Profile -->
                        <div class="dropdown">
                            <button class="btn dropdown-toggle d-flex align-items-center gap-2" type="button"
                                    id="settingsDropdown" data-bs-toggle="dropdown" aria-expanded="false" data-tour="account-menu">
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
                                <li class="px-3 py-2 text-muted small">{{ __('Support') }}</li>
                                <li><x-dropdown-link href="{{ route('support.index') }}">{{ __('Support Portal') }}</x-dropdown-link></li>
                                <li><x-dropdown-link href="{{ route('support.my') }}">{{ __('My Tickets') }}</x-dropdown-link></li>
                                <li><x-dropdown-link href="{{ route('support.new') }}">{{ __('Submit Ticket') }}</x-dropdown-link></li>
                                @can('viewAny', Ticket::class)
                                    <li><x-dropdown-link href="{{ route('support.staff.index') }}">{{ __('Support Triage') }}</x-dropdown-link></li>
                                @endcan

                                @if ($canUseOnboarding)
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <button
                                            type="button"
                                            class="dropdown-item"
                                            data-start-tour="main-app"
                                            data-start-tour-route="{{ route('onboarding.tours.start', ['tour' => 'main-app']) }}"
                                        >
                                            {{ __('Take the tour') }}
                                        </button>
                                    </li>
                                    <li>
                                        <button
                                            type="button"
                                            class="dropdown-item"
                                            data-start-tour="project-detail"
                                            data-start-tour-route="{{ route('onboarding.tours.start', ['tour' => 'project-detail']) }}"
                                        >
                                            {{ __('Project walkthrough') }}
                                        </button>
                                    </li>
                                    <li>
                                        <button
                                            type="button"
                                            class="dropdown-item"
                                            data-start-tour="issue-detail"
                                            data-start-tour-route="{{ route('onboarding.tours.start', ['tour' => 'issue-detail']) }}"
                                        >
                                            {{ __('Issue walkthrough') }}
                                        </button>
                                    </li>
                                    <li><x-dropdown-link href="{{ route('getting-started') }}">{{ __('Getting Started') }}</x-dropdown-link></li>
                                @endif

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
                        @if (Route::has('login'))
                            <a class="btn btn-outline-secondary" href="{{ route('login') }}">{{ __('Log in') }}</a>
                        @endif
                            @if ($allowReg)
                                @if (Route::has('register'))
                                    <a class="btn btn-primary" href="{{ route('register') }}">{{ __('Register') }}</a>
                                @endif
                            @endif
                    @endauth
                </div>
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
