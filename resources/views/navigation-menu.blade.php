@php
    use App\Models\{Project, Organization, Issue, Goal, Ticket};
    use Illuminate\Support\Facades\Route;

    /** @var \App\Models\User|null $user */
    $user = auth()->user();
    $allowReg = app(\App\Settings\AuthSettings::class)->allowRegistration ?? true;
    $canUseOnboarding = $user?->hasVerifiedEmail() ?? false;
    $codex = app(\App\Support\Codex\CodexConnection::class);
@endphp

<nav class="navbar navbar-expand-lg bg-body border-bottom sticky-top" style="z-index: 1025;" x-data>
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
            <x-application-mark style="height: 2rem;" />
            <span class="fw-semibold">{{ config('app.name', 'Forge') }}</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#appNavbar"
                data-tour="nav-toggle"
                aria-controls="appNavbar" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="appNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-lg-center">
                @auth
                    <li class="nav-item">
                        <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('projects.*') ? 'active' : '' }}"
                           href="#" id="projectsDropdown" role="button" data-bs-toggle="dropdown"
                           aria-expanded="false" data-tour="projects-nav">
                            {{ __('Projects') }}
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="projectsDropdown">
                            <li>
                                <x-dropdown-link href="{{ Route::has('projects.index') ? route('projects.index') : url('/projects') }}">
                                    {{ __('Browse projects') }}
                                </x-dropdown-link>
                            </li>
                            <li>
                                <x-dropdown-link href="{{ Route::has('projects.mine') ? route('projects.mine') : url('/projects?filter=mine') }}">
                                    {{ __('My projects') }}
                                </x-dropdown-link>
                            </li>
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
                        <x-nav-link href="{{ Route::has('issues.explorer') ? route('issues.explorer') : url('/issues') }}"
                                    :active="request()->routeIs('issues.*')"
                                    data-tour="issues-nav">
                            {{ __('Issues') }}
                        </x-nav-link>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('organizations.*') ? 'active' : '' }}"
                           href="#" id="orgDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
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

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('goals.*') ? 'active' : '' }}"
                           href="#" id="goalDropdown" role="button" data-bs-toggle="dropdown"
                           aria-expanded="false" data-tour="goals-nav">
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
                @endauth

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('support.*') ? 'active' : '' }}"
                       href="#" id="supportDropdown" role="button" data-bs-toggle="dropdown"
                       aria-expanded="false" data-tour="support-nav">
                        {{ __('Support') }}
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="supportDropdown">
                        <li>
                            <x-dropdown-link href="{{ Route::has('support.index') ? route('support.index') : url('/support') }}">
                                {{ __('Support Portal') }}
                            </x-dropdown-link>
                        </li>
                        @auth
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
                        @else
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
                        @endauth
                    </ul>
                </li>
            </ul>

            @auth
                <form action="{{ Route::has('search') ? route('search') : url('/search') }}" method="GET"
                      data-tour="global-search"
                      class="d-none d-lg-flex align-items-center me-3">
                    <label for="global-search" class="visually-hidden">{{ __('Search') }}</label>
                    <input id="global-search" name="q" type="search"
                           class="form-control form-control-sm"
                           style="width: 18rem;"
                           placeholder="{{ __('Search projects, issues, people...') }}"
                           value="{{ request('q') }}" />
                </form>
            @endauth

            <div class="d-flex align-items-center gap-2 flex-wrap">
                @auth
                    @if($codex->enabled() && $codex->baseUrl() !== '')
                        @php
                            $forgeProject = request()->route('project');
                            $codexSlug = $forgeProject?->codex_workspace_slug;
                            $codexBase = $codex->baseUrl();
                        @endphp
                        <a href="{{ $codexBase }}{{ $codexSlug ? '/workspaces/' . $codexSlug : '' }}"
                           class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                           target="_blank"
                           rel="noopener"
                           title="{{ $codexSlug ? __('Open linked Codex workspace') : __('Open Codex') }}">
                            <wa-icon family="solid" name="book" style="font-size: 0.8rem;"></wa-icon>
                            {{ __('Docs') }}
                        </a>
                    @endif

                    <div class="dropdown">
                        <button id="recordCreate"
                                class="btn btn-sm btn-primary dropdown-toggle"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                                data-tour="create-menu">
                            {{ __('Create') }}
                        </button>
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

                    @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                        @php($currentTeam = $user?->currentTeam)
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                    id="teamsDropdown" data-bs-toggle="dropdown" aria-expanded="false" data-tour="teams-menu">
                                {{ $currentTeam?->name ?? __('No team selected') }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="teamsDropdown">
                                <li><span class="dropdown-header">{{ __('Manage Team') }}</span></li>

                                @if ($currentTeam)
                                    <li>
                                        <x-dropdown-link href="{{ route('teams.dashboard', ['team' => $currentTeam]) }}">{{ __('Team Dashboard') }}</x-dropdown-link>
                                    </li>
                                    <li>
                                        <x-dropdown-link href="{{ route('teams.show', $currentTeam->id) }}">{{ __('Team Settings') }}</x-dropdown-link>
                                    </li>
                                @else
                                    <li><span class="dropdown-item-text text-body-secondary small">{{ __('You are not in a team yet.') }}</span></li>
                                @endif

                                @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                    <li><x-dropdown-link href="{{ route('teams.create') }}">{{ __('Create New Team') }}</x-dropdown-link></li>
                                @endcan

                                @if ($user && ($user->allTeams()->count() > 1 || ($currentTeam === null && $user->allTeams()->count() >= 1)))
                                    <li><hr class="dropdown-divider"></li>
                                    <li><span class="dropdown-header">{{ __('Switch Teams') }}</span></li>
                                    @foreach ($user->allTeams() as $team)
                                        <li><x-switchable-team :team="$team" /></li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    @endif

                    <livewire:notifications.menu />

                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center gap-2"
                                type="button" id="settingsDropdown" data-bs-toggle="dropdown"
                                aria-expanded="false" data-tour="account-menu">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <x-avatar :src="$user?->profile_photo_url" :name="$user?->name" preset="sm" />
                            @else
                                <span>{{ $user?->name }}</span>
                            @endif
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsDropdown" style="min-width: 14rem;">
                            <li><span class="dropdown-header">{{ __('Manage Account') }}</span></li>
                            <li><x-dropdown-link href="{{ route('profile.show') }}">{{ __('Profile') }}</x-dropdown-link></li>
                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <li><x-dropdown-link href="{{ route('api-tokens.index') }}">{{ __('API Tokens') }}</x-dropdown-link></li>
                            @endif

                            <li><hr class="dropdown-divider"></li>
                            <li><span class="dropdown-header">{{ __('Support') }}</span></li>
                            <li><x-dropdown-link href="{{ route('support.index') }}">{{ __('Support Portal') }}</x-dropdown-link></li>
                            <li><x-dropdown-link href="{{ route('support.my') }}">{{ __('My Tickets') }}</x-dropdown-link></li>
                            <li><x-dropdown-link href="{{ route('support.new') }}">{{ __('Submit Ticket') }}</x-dropdown-link></li>
                            @can('viewAny', Ticket::class)
                                <li><x-dropdown-link href="{{ route('support.staff.index') }}">{{ __('Support Triage') }}</x-dropdown-link></li>
                            @endcan

                            @if ($canUseOnboarding)
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <button type="button"
                                            class="dropdown-item"
                                            data-start-tour="main-app"
                                            data-start-tour-route="{{ route('onboarding.tours.start', ['tour' => 'main-app']) }}">
                                        {{ __('Take the tour') }}
                                    </button>
                                </li>
                                <li>
                                    <button type="button"
                                            class="dropdown-item"
                                            data-start-tour="project-detail"
                                            data-start-tour-route="{{ route('onboarding.tours.start', ['tour' => 'project-detail']) }}">
                                        {{ __('Project walkthrough') }}
                                    </button>
                                </li>
                                <li>
                                    <button type="button"
                                            class="dropdown-item"
                                            data-start-tour="issue-detail"
                                            data-start-tour-route="{{ route('onboarding.tours.start', ['tour' => 'issue-detail']) }}">
                                        {{ __('Issue walkthrough') }}
                                    </button>
                                </li>
                                <li><x-dropdown-link href="{{ route('getting-started') }}">{{ __('Getting Started') }}</x-dropdown-link></li>
                            @endif

                            <li><hr class="dropdown-divider"></li>
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
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('login') }}">{{ __('Log in') }}</a>
                    @endif
                    @if ($allowReg && Route::has('register'))
                        <a class="btn btn-sm btn-primary" href="{{ route('register') }}">{{ __('Register') }}</a>
                    @endif
                @endauth
            </div>

            @auth
                <form action="{{ Route::has('search') ? route('search') : url('/search') }}" method="GET" class="d-lg-none mt-3 w-100">
                    <label for="global-search-mobile" class="visually-hidden">{{ __('Search') }}</label>
                    <input id="global-search-mobile" name="q" type="search"
                           class="form-control form-control-sm"
                           placeholder="{{ __('Search...') }}"
                           value="{{ request('q') }}" />
                </form>
            @endauth
        </div>
    </div>
</nav>

@once
    <script>
        document.addEventListener('keydown', (event) => {
            if (['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
                return;
            }

            if (event.key === '/') {
                const search = document.getElementById('global-search') ?? document.getElementById('global-search-mobile');

                if (search) {
                    event.preventDefault();
                    search.focus();
                }
            }
        });
    </script>
@endonce
