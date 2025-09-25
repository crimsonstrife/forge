<x-guest-layout>
{{-- MAIN --}}
<x-slot class="flex-fill">
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
</x-slot>
</x-guest-layout>
