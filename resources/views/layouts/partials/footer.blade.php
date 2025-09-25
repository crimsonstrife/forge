@php
    use Illuminate\Support\Facades\Route;
    $start = 2025;
    $year = now()->year;
    $range = $year > $start ? "{$start}–{$year}" : "{$start}";
@endphp
<footer class="bg-body border-top mt-auto">
    <div class="container py-5">
        <div class="row g-4">
            {{-- Brand / blurb --}}
            <div class="col-12 col-lg-4">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <x-application-footer-logo class="me-2" style="height: 2rem" />
                </div>
                <p class="text-body-secondary mb-3">
                    {{ __('Streamlined project & issue management built with Laravel.') }}
                </p>

                <nav aria-label="{{ __('Social links') }}">
                    <div class="d-flex align-items-center gap-3">
                        <a href="https://github.com/crimsonstrife/forge" class="link-body-emphasis"
                           aria-label="{{ __('GitHub') }}" target="_blank" rel="noopener">
                            <wa-icon family="brands" name="github" style="font-size:1.25rem;"></wa-icon>
                        </a>
                        @if (Route::has('docs.index'))
                            <a href="{{ route('docs.index') }}" class="link-body-emphasis"
                               aria-label="{{ __('Documentation') }}">
                                <wa-icon family="solid" name="book" style="font-size:1.25rem;"></wa-icon>
                            </a>
                        @endif
                        <a href="{{ url('/scalar') }}" class="link-body-emphasis" aria-label="{{ __('API Reference') }}">
                            <wa-icon family="solid" name="code" style="font-size:1.25rem;"></wa-icon>
                        </a>
                    </div>
                </nav>
            </div>

            {{-- Link columns --}}
            <div class="col-6 col-lg-2">
                <h6 id="footer-product" class="text-uppercase text-body-secondary fw-semibold mb-3">{{ __('Product') }}</h6>
                <nav aria-labelledby="footer-product">
                    <ul class="list-unstyled mb-0">
                        <li><a class="link-body-emphasis text-decoration-none" href="{{ Route::has('dashboard') ? route('dashboard') : url('/dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li><a class="link-body-emphasis text-decoration-none" href="{{ Route::has('projects.index') ? route('projects.index') : url('/projects') }}">{{ __('Projects') }}</a></li>
                        <li><a class="link-body-emphasis text-decoration-none" href="{{ Route::has('issues.index') ? route('issues.index') : url('/issues') }}">{{ __('Issues') }}</a></li>
                        <li><a class="link-body-emphasis text-decoration-none" href="{{ Route::has('support.index') ? route('support.index') :  url('/support') }}">{{ __('Support') }}</a></li>
                    </ul>
                </nav>
            </div>

            <div class="col-6 col-lg-2">
                <h6 id="footer-resources" class="text-uppercase text-body-secondary fw-semibold mb-3">{{ __('Resources') }}</h6>
                <nav aria-labelledby="footer-resources">
                    <ul class="list-unstyled mb-0">
                        <li>
                            @if (Route::has('docs.index'))
                                <a class="link-body-emphasis text-decoration-none" href="{{ route('docs.index') }}">{{ __('Docs') }}</a>
                            @else
                                <a class="link-body-emphasis text-decoration-none" href="{{ url('/docs') }}">{{ __('Docs') }}</a>
                            @endif
                        </li>
                        <li><a class="link-body-emphasis text-decoration-none" href="{{ url('/scalar') }}">{{ __('API Reference') }}</a></li>
                        <li><a class="link-body-emphasis text-decoration-none" href="{{ route('changelog') }}">{{ __('Changelog') }}</a></li>
                        <li><a class="link-body-emphasis text-decoration-none" href="{{ route('status') }}">{{ __('Status') }}</a></li>
                    </ul>
                </nav>
            </div>

            @php($showCompany = (bool) config('footer.show_company'))
            @if ($showCompany)
                @php($c = config('footer.company'))
                <div class="col-6 col-lg-2">
                    <h6 id="footer-company" class="text-uppercase text-body-secondary fw-semibold mb-3">{{ __($c['title']) }}</h6>
                    <nav aria-labelledby="footer-company">
                        <ul class="list-unstyled mb-0">
                            @foreach ($c['links'] as $l)
                                <li>
                                    <x-footer.link :href="$l['href']">{{ __($l['label']) }}</x-footer.link>
                                </li>
                            @endforeach
                        </ul>
                    </nav>
                </div>
            @endif

            <div class="col-6 col-lg-2">
                <h6 id="footer-legal" class="text-uppercase text-body-secondary fw-semibold mb-3">{{ __('Legal') }}</h6>
                <nav aria-labelledby="footer-legal">
                    <ul class="list-unstyled mb-0">
                        <li><a class="link-body-emphasis text-decoration-none" href="{{ route('legal.terms.show') }}">{{ __('Terms') }}</a></li>
                        <li><a class="link-body-emphasis text-decoration-none" href="{{ route('legal.policy.show') }}">{{ __('Privacy') }}</a></li>
                        <li><a class="link-body-emphasis text-decoration-none" href="{{ route('legal.cookies.show') }}">{{ __('Cookies') }}</a></li>
                        <li><a class="link-body-emphasis text-decoration-none" href="{{ route('security') }}">{{ __('Security') }}</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <div class="border-top">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center py-3 gap-2">
            <div class="text-body-secondary small">
                © {{ now()->year }} {{ config('app.name', 'Forge') }}. {{ __('All rights reserved.') }}
            </div>
            <div class="text-body-secondary small">
               Powered By: <a href="https://getforge.live">Forge</a> @unless (app()->isProduction())
                    <span class="badge text-bg-secondary ms-2">{{ strtoupper(app()->environment()) }}</span>
                    <span class="text-body-tertiary ms-1">{{ config('app.version') }}</span>
                @endunless ©{{ now()->year === 2025 ? '2025' : '2025 - ' . now()->year }} <a href="https://crimsonstrife.live">CrimsonStrife</a>. All rights reserved.
            </div>
            <a href="#" class="btn btn-sm btn-outline-secondary" onclick="window.scrollTo({top:0,behavior:'smooth'})">
                <wa-icon family="solid" name="arrow-up"></wa-icon> Back to top
            </a>
        </div>
    </div>
</footer>
