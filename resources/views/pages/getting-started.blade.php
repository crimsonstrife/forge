<?php

use function Laravel\Folio\{middleware, name};

name('getting-started');
middleware(['auth', 'verified']);
?>

<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h1 class="h3 mb-1">{{ __('Getting Started') }}</h1>
                <p class="text-body-secondary mb-0">
                    {{ __('Learn the main Forge surfaces, then launch the guided tour whenever you want.') }}
                </p>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                data-start-tour="main-app"
                data-start-tour-route="{{ route('onboarding.tours.start', ['tour' => 'main-app']) }}"
            >
                {{ __('Start interactive tour') }}
            </button>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container mx-auto py-4 d-flex flex-column gap-4">
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column gap-3">
                            <div>
                                <p class="text-uppercase small text-body-secondary fw-semibold mb-2">{{ __('What Forge covers') }}</p>
                                <h2 class="h4 mb-0">{{ __('Projects, issues, goals, support, and account tools in one place') }}</h2>
                            </div>

                            <p class="text-body-secondary mb-0">
                                {{ __('Forge combines day-to-day execution with planning and intake. Projects hold delivery work, issues track the details, goals keep outcomes visible, and the service desk helps staff turn incoming requests into actionable work.') }}
                            </p>

                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">{{ __('Browse projects') }}</a>
                                <a href="{{ route('issues.create.global') }}" class="btn btn-outline-secondary">{{ __('Create an issue') }}</a>
                                <a href="{{ route('goals.index') }}" class="btn btn-outline-secondary">{{ __('Open goals') }}</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card h-100">
                        <div class="card-body">
                            <p class="text-uppercase small text-body-secondary fw-semibold mb-2">{{ __('First 5 things') }}</p>
                            <ol class="list-group list-group-numbered">
                                <li class="list-group-item">{{ __('Open your dashboard to see assigned work and recent activity.') }}</li>
                                <li class="list-group-item">{{ __('Browse or create a project so work has a home.') }}</li>
                                <li class="list-group-item">{{ __('Capture the next unit of work as an issue.') }}</li>
                                <li class="list-group-item">{{ __('Add a goal if you need a visible outcome across multiple issues or projects.') }}</li>
                                <li class="list-group-item">{{ __('Use search and the Create menu to move faster once the basics are in place.') }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body d-flex flex-column gap-3">
                    <div>
                        <p class="text-uppercase small text-body-secondary fw-semibold mb-2">{{ __('Hands-on walkthroughs') }}</p>
                        <h2 class="h4 mb-1">{{ __('Launch tours against a private sample project when you need more context') }}</h2>
                        <p class="text-body-secondary mb-0">
                            {{ __('Project and issue walkthroughs create a private Forge Sandbox the first time you launch them, so new users can explore deeper screens like backlog planning without needing a real project on day one.') }}
                        </p>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button
                            type="button"
                            class="btn btn-outline-primary"
                            data-start-tour="project-detail"
                            data-start-tour-route="{{ route('onboarding.tours.start', ['tour' => 'project-detail']) }}"
                        >
                            {{ __('Project walkthrough') }}
                        </button>
                        <button
                            type="button"
                            class="btn btn-outline-primary"
                            data-start-tour="issue-detail"
                            data-start-tour-route="{{ route('onboarding.tours.start', ['tour' => 'issue-detail']) }}"
                        >
                            {{ __('Issue walkthrough') }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h2 class="h5">{{ __('Projects and issues') }}</h2>
                            <p class="text-body-secondary mb-0">
                                {{ __('Projects organize a stream of work. Issues are the actual tasks, bugs, requests, and follow-ups that move a project forward.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h2 class="h5">{{ __('Goals') }}</h2>
                            <p class="text-body-secondary mb-0">
                                {{ __('Goals help you track progress above the issue level, especially when work spans teams or multiple projects.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h2 class="h5">{{ __('Service desk') }}</h2>
                            <p class="text-body-secondary mb-0">
                                {{ __('Support is where staff triage incoming tickets and connect customer-facing work back to the product backlog.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h2 class="h5">{{ __('Search and Create') }}</h2>
                            <p class="text-body-secondary mb-0">
                                {{ __('Search gets you back to work quickly, while Create lets you add new records without breaking your flow.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h2 class="h5">{{ __('Teams and account') }}</h2>
                            <p class="text-body-secondary mb-0">
                                {{ __('Your account menu is where you manage profile details, API tokens, team membership, and relaunch onboarding later.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h2 class="h5">{{ __('Admin panel') }}</h2>
                            <p class="text-body-secondary mb-0">
                                {{ __("Forge's Filament admin panel is a separate surface. This tour focuses on the main app so admin onboarding can evolve independently later.") }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <h2 class="h5 mb-1">{{ __('Prefer to explore manually?') }}</h2>
                        <p class="text-body-secondary mb-0">
                            {{ __('Jump straight into the areas most new users need first.') }}
                        </p>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">{{ __('Dashboard') }}</a>
                        <a href="{{ route('search') }}" class="btn btn-outline-secondary">{{ __('Search') }}</a>
                        <a href="{{ route('support.staff.index') }}" class="btn btn-outline-secondary">{{ __('Support') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
