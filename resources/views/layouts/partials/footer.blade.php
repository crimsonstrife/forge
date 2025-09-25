@php use Illuminate\Support\Facades\Route; @endphp

<footer class="bg-body border-top mt-auto">
    <div class="container py-5">
        <div class="row g-4">
            {{-- Brand / blurb --}}
            <div class="col-12 col-lg-4">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <x-application-footer-logo class="me-2" style="height: 2rem" />
                    </div>
                </div>
                <p class="text-body-secondary mb-3">
                    Streamlined project & issue management built with Laravel.
                </p>

                {{-- Socials (Web Awesome) --}}
                <div class="d-flex align-items-center gap-3">
                    <a href="https://github.com/crimsonstrife/forge" class="link-body-emphasis" aria-label="GitHub">
                        <wa-icon family="brands" name="github" style="font-size:1.25rem;"></wa-icon>
                    </a>
                    @if (Route::has('docs.index'))
                        <a href="{{ route('docs.index') }}" class="link-body-emphasis" aria-label="Documentation">
                            <wa-icon family="solid" name="book" style="font-size:1.25rem;"></wa-icon>
                        </a>
                    @endif
                    <a href="{{ url('/scalar') }}" class="link-body-emphasis" aria-label="API Reference">
                        <wa-icon family="solid" name="code" style="font-size:1.25rem;"></wa-icon>
                    </a>
                </div>
            </div>

            {{-- Link columns --}}
            <div class="col-6 col-lg-2">
                <h6 class="text-uppercase text-body-secondary fw-semibold mb-3">Product</h6>
                <ul class="list-unstyled mb-0">
                    <li><a class="link-body-emphasis text-decoration-none" href="{{ Route::has('dashboard') ? route('dashboard') : url('/dashboard') }}">Dashboard</a></li>
                    <li><a class="link-body-emphasis text-decoration-none" href="{{ url('/projects') }}">Projects</a></li>
                    <li><a class="link-body-emphasis text-decoration-none" href="{{ url('/issues') }}">Issues</a></li>
                    <li><a class="link-body-emphasis text-decoration-none" href="{{ url('/support') }}">Support</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-2">
                <h6 class="text-uppercase text-body-secondary fw-semibold mb-3">Resources</h6>
                <ul class="list-unstyled mb-0">
                    <li>
                        @if (Route::has('docs.index'))
                            <a class="link-body-emphasis text-decoration-none" href="{{ route('docs.index') }}">Docs</a>
                        @else
                            <a class="link-body-emphasis text-decoration-none" href="{{ url('/docs') }}">Docs</a>
                        @endif
                    </li>
                    <li><a class="link-body-emphasis text-decoration-none" href="{{ url('/scalar') }}">API Reference</a></li>
                    <li><a class="link-body-emphasis text-decoration-none" href="{{ url('/changelog') }}">Changelog</a></li>
                    <li><a class="link-body-emphasis text-decoration-none" href="{{ url('/status') }}">Status</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-2">
                <h6 class="text-uppercase text-body-secondary fw-semibold mb-3">Company</h6>
                <ul class="list-unstyled mb-0">
                    <li><a class="link-body-emphasis text-decoration-none" href="{{ url('/about') }}">About</a></li>
                    <li><a class="link-body-emphasis text-decoration-none" href="{{ url('/blog') }}">Blog</a></li>
                    <li><a class="link-body-emphasis text-decoration-none" href="{{ url('/contact') }}">Contact</a></li>
                    <li><a class="link-body-emphasis text-decoration-none" href="{{ url('/careers') }}">Careers</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-2">
                <h6 class="text-uppercase text-body-secondary fw-semibold mb-3">Legal</h6>
                <ul class="list-unstyled mb-0">
                    <li><a class="link-body-emphasis text-decoration-none" href="{{ route('legal.terms.show') }}">Terms</a></li>
                    <li><a class="link-body-emphasis text-decoration-none" href="{{ route('legal.policy.show') }}">Privacy</a></li>
                    <li><a class="link-body-emphasis text-decoration-none" href="{{ route('legal.cookies.show') }}">Cookies</a></li>
                    <li><a class="link-body-emphasis text-decoration-none" href="{{ url('/security') }}">Security</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="border-top">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center py-3 gap-2">
            <div class="text-body-secondary small">
                © {{ now()->year }} {{ config('app.name', 'Forge') }}. All rights reserved.
            </div>
            <a href="#" class="btn btn-sm btn-outline-secondary" onclick="window.scrollTo({top:0,behavior:'smooth'})">
                <wa-icon name="solid/arrow-up"></wa-icon> Back to top
            </a>
        </div>
    </div>
</footer>
