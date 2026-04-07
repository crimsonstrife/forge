@php
    use Illuminate\Support\Facades\Route;
@endphp

<footer class="bg-body border-top mt-auto py-4">
    <div class="container-fluid">
        <div class="d-flex flex-column flex-lg-row align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2">
                <x-application-mark style="height: 1.5rem;" />
                <span class="fw-semibold text-body-secondary small">{{ config('app.name', 'Forge') }}</span>
            </div>

            <nav class="d-flex flex-wrap justify-content-center gap-3 small" aria-label="{{ __('Footer') }}">
                <a class="link-body-emphasis text-decoration-none" href="{{ Route::has('dashboard') ? route('dashboard') : url('/dashboard') }}">{{ __('Dashboard') }}</a>
                <a class="link-body-emphasis text-decoration-none" href="{{ Route::has('projects.index') ? route('projects.index') : url('/projects') }}">{{ __('Projects') }}</a>
                <a class="link-body-emphasis text-decoration-none" href="{{ Route::has('issues.index') ? route('issues.index') : url('/issues') }}">{{ __('Issues') }}</a>
                <a class="link-body-emphasis text-decoration-none" href="{{ Route::has('support.index') ? route('support.index') : url('/support') }}">{{ __('Support') }}</a>
                @if (Route::has('changelog'))
                    <a class="link-body-emphasis text-decoration-none" href="{{ route('changelog') }}">{{ __('Changelog') }}</a>
                @endif
                @if (Route::has('status'))
                    <a class="link-body-emphasis text-decoration-none" href="{{ route('status') }}">{{ __('Status') }}</a>
                @endif
                @if (Route::has('legal.terms.show'))
                    <a class="link-body-emphasis text-decoration-none" href="{{ route('legal.terms.show') }}">{{ __('Terms') }}</a>
                @endif
                @if (Route::has('legal.policy.show'))
                    <a class="link-body-emphasis text-decoration-none" href="{{ route('legal.policy.show') }}">{{ __('Privacy') }}</a>
                @endif
            </nav>

            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-end gap-2">
                @include('layouts.partials.theme-toggle')

                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.scrollTo({top:0,behavior:'smooth'})">
                    {{ __('Back to top') }}
                </button>
            </div>
        </div>

        <div class="d-flex flex-column flex-md-row justify-content-center justify-content-md-between align-items-center gap-2 mt-3 pt-3 border-top">
            <div class="text-body-secondary small">
                &copy; {{ now()->year }} {{ config('app.name', 'Forge') }}. {{ __('All rights reserved.') }}
            </div>

            <div class="text-body-secondary small">
                {{ __('Powered by') }} <a href="https://getforge.live" class="link-body-emphasis">Forge</a>
                @unless (app()->isProduction())
                    <span class="badge text-bg-secondary ms-2">{{ strtoupper(app()->environment()) }}</span>
                    <span class="text-body-tertiary ms-1">{{ config('app.version') }}</span>
                @endunless
            </div>
        </div>
    </div>
</footer>
