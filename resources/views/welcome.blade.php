@php($allowReg = app(\App\Settings\AuthSettings::class)->allowRegistration ?? true)

<x-guest-layout>
    <section class="py-5">
        <div class="container py-4">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <x-application-mark style="height: 3rem;" />
                        <span class="badge text-bg-primary-subtle text-primary-emphasis border border-primary-subtle">
                            {{ __('Project and issue management') }}
                        </span>
                    </div>

                    <h1 class="display-5 fw-bold mb-3">
                        {{ __('Build, track, and ship with :app.', ['app' => config('app.name', 'Forge')]) }}
                    </h1>
                    <p class="lead text-body-secondary mb-4">
                        {{ __('Forge brings projects, issues, docs, support, and delivery workflows into one focused Laravel workspace.') }}
                    </p>

                    <div class="d-flex flex-wrap gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">{{ __('Go to Dashboard') }}</a>
                            <a href="{{ route('today.index') }}" class="btn btn-outline-secondary btn-lg">{{ __('Today\'s Work') }}</a>
                            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary btn-lg">{{ __('Browse Projects') }}</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg">{{ __('Sign in') }}</a>
                            @if ($allowReg && Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-lg">{{ __('Create an account') }}</a>
                            @endif
                        @endauth
                    </div>

                    @env(['local', 'development'])
                        @auth
                            <div class="alert alert-secondary mt-4 py-2 px-3 small mb-0">
                                <strong>{{ __('Dev note:') }}</strong>
                                {{ __('Edit this view at') }} <code>resources/views/welcome.blade.php</code>.
                            </div>
                        @endauth
                    @endenv
                </div>

                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h2 class="h5 fw-semibold mb-3">{{ __('What Forge helps with') }}</h2>
                            <div class="vstack gap-3">
                                <div class="d-flex gap-3">
                                    <span class="badge rounded-pill text-bg-primary align-self-start">1</span>
                                    <div>
                                        <div class="fw-semibold">{{ __('Project planning') }}</div>
                                        <p class="text-body-secondary small mb-0">{{ __('Organize work by project, milestone, goal, team, and status.') }}</p>
                                    </div>
                                </div>
                                <div class="d-flex gap-3">
                                    <span class="badge rounded-pill text-bg-success align-self-start">2</span>
                                    <div>
                                        <div class="fw-semibold">{{ __('Issue execution') }}</div>
                                        <p class="text-body-secondary small mb-0">{{ __('Track priorities, estimates, attachments, comments, and linked code work.') }}</p>
                                    </div>
                                </div>
                                <div class="d-flex gap-3">
                                    <span class="badge rounded-pill text-bg-warning align-self-start">3</span>
                                    <div>
                                        <div class="fw-semibold">{{ __('Cross-app context') }}</div>
                                        <p class="text-body-secondary small mb-0">{{ __('Connect repositories and Codex workspaces without leaving the flow.') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-body-tertiary border-top">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h3 class="h5">{{ __('Projects') }}</h3>
                            <p class="text-body-secondary mb-0">{{ __('Shape roadmaps, backlogs, boards, calendars, and milestones around how your team ships.') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h3 class="h5">{{ __('Issues') }}</h3>
                            <p class="text-body-secondary mb-0">{{ __('Move work from idea to done with issue views, timers, notes, links, and transitions.') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h3 class="h5">{{ __('Support') }}</h3>
                            <p class="text-body-secondary mb-0">{{ __('Give teams and customers one place to request help, triage, and close the loop.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-guest-layout>
