<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <!-- Fonts (keep if you like this face) -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- (Optional) Material Icons — safe to remove if you only use <wa-icon> -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
    @livewireStyles
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100 bg-light" x-data="themeSwitcher()" :class="{ 'dark': switchOn }">

{{-- NAVBAR --}}
<nav class="navbar navbar-expand-md bg-white border-bottom">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
            <img src="/favicon.svg" alt="{{ config('app.name') }} logo" width="28" height="28">
            <span class="fw-semibold">{{ config('app.name', 'Laravel') }}</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topnav" aria-controls="topnav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        @if (Route::has('login'))
            <div class="collapse navbar-collapse" id="topnav">
                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    <div x-data="window.themeSwitcher()" x-init="switchTheme()" class="d-none d-md-flex align-items-center gap-2 me-2">
                        <span class="small text-muted">{{ __('Dark Mode') }}</span>
                        <wa-switch :checked="switchOn" @click="switchOn = !switchOn; switchTheme()"></wa-switch>
                    </div>
                    @auth
                        <li class="nav-item">
                            <a class="btn btn-dark" href="{{ route('dashboard') }}">
                                <i class="fa-solid fa-gauge-high me-1"></i> Dashboard
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="btn btn-outline-secondary" href="{{ route('login') }}">
                                <i class="fa-solid fa-right-to-bracket me-1"></i> Log in
                            </a>
                        </li>

                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="btn btn-outline-dark" href="{{ route('register') }}">
                                    <i class="fa-regular fa-id-card me-1"></i> Register
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>
            </div>
        @endif
    </div>
</nav>

{{-- MAIN --}}
<main class="flex-fill">
    <div class="container py-5">
        <div class="row g-4 align-items-center">
            {{-- Left: Hero / CTAs --}}
            <div class="col-lg-6">
                <h1 class="display-5 fw-semibold mb-2">
                    Welcome to {{ config('app.name', 'Forge') }}
                </h1>
                <p class="text-secondary mb-4">
                    Your all-in-one workspace for projects, issues, docs, and more.
                </p>

                @auth
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-dark">
                            <i class="fa-solid fa-house me-1"></i> Go to Dashboard
                        </a>
                        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-diagram-project me-1"></i> Browse Projects
                        </a>
                    </div>
                @else
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('login') }}" class="btn btn-dark">
                            <i class="fa-solid fa-right-to-bracket me-1"></i> Sign in
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-outline-secondary">
                                <i class="fa-regular fa-id-card me-1"></i> Create an account
                            </a>
                        @endif
                    </div>
                @endauth

                @env(['local','development'])
                    @auth
                        <div class="alert alert-secondary mt-4 py-2 px-3 small mb-0">
                            <strong>Dev note:</strong> Edit this view at
                            <code>resources/views/welcome.blade.php</code>.
                        </div>
                    @endauth
                @endenv
            </div>

            {{-- Right: Quick links / Info --}}
            <div class="col-lg-6">
                @auth
                    <div class="row g-3">
                        <div class="col-12">
                            <a href="#" class="text-decoration-none">
                                <div class="card shadow-sm border-0 h-100">
                                    <div class="card-body d-flex gap-3 align-items-start">
                                        <div class="btn btn-outline-secondary rounded-circle p-3 disabled">
                                            <i class="fa-solid fa-circle-dot"></i>
                                        </div>
                                        <div>
                                            <h2 class="h6 fw-semibold mb-1 text-dark">Issues</h2>
                                            <p class="mb-0 text-secondary small">
                                                Track work, link PRs, upload attachments, and discuss.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-12">
                            <a href="{{ route('projects.index') }}" class="text-decoration-none">
                                <div class="card shadow-sm border-0 h-100">
                                    <div class="card-body d-flex gap-3 align-items-start">
                                        <div class="btn btn-outline-secondary rounded-circle p-3 disabled">
                                            <i class="fa-solid fa-diagram-project"></i>
                                        </div>
                                        <div>
                                            <h2 class="h6 fw-semibold mb-1 text-dark">Projects</h2>
                                            <p class="mb-0 text-secondary small">
                                                Organize work by status, type, and priority.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endauth

                @guest
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h2 class="h6 fw-semibold mb-1">Welcome</h2>
                            <p class="mb-0 text-secondary small">
                                Sign in to access your dashboard, projects, and docs.
                            </p>
                        </div>
                    </div>
                @endguest
            </div>
        </div>
    </div>
</main>
@cookieconsentview
{{-- FOOTER --}}
<footer class="border-top py-3 small text-secondary bg-white">
    <div class="container d-flex justify-content-between">
        <span>&copy; {{ now()->year }} {{ config('app.name', 'Laravel') }}</span>
        <span>v{{ app()->version() }}</span>
    </div>
</footer>
@stack('modals')
@fluxScripts
@livewireScripts
@cookieconsentscripts
@stack('scripts')
<script>
    // Bootstrap-friendly + WA-friendly theme toggle
    window.themeSwitcher = function () {
        return {
            switchOn: JSON.parse(localStorage.getItem('isDark')) || false,
            switchTheme() {
                const isDark = this.switchOn;
                document.documentElement.classList.toggle('dark', isDark);
                document.documentElement.setAttribute('data-bs-theme', isDark ? 'dark' : 'light');
                localStorage.setItem('isDark', isDark);
            }
        }
    }
    // Initialize data-bs-theme on load
    document.addEventListener('alpine:init', () => {
        const isDark = JSON.parse(localStorage.getItem('isDark')) || false;
        document.documentElement.setAttribute('data-bs-theme', isDark ? 'dark' : 'light');
    });
</script>
</body>
</html>
