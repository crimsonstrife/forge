<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head', [
        'title' => config('app.name', 'Forge'),
        'viteEntries' => [
            'resources/css/app.css',
            'resources/js/app.js',
        ],
        'includeMaterialIcons' => true,
    ])
</head>
<body>
<x-banner />

@php
    $showNavigation = $showNavigation ?? true;
    $showFooter = $showFooter ?? true;
    $showCookieConsent = $showCookieConsent ?? true;
@endphp

<div class="min-vh-100 d-flex flex-column bg-body">
    @if ($showNavigation)
        @livewire('navigation-menu')
    @endif

    {{-- Header partial (uses the $header slot if present) --}}
    @include('layouts.partials.header', ['header' => $header ?? null])

    <main class="flex-grow-1">
        {{ $slot }}
    </main>

    @if ($showFooter)
        {{-- Footer partial --}}
        @include('layouts.partials.footer')
    @endif

    @if ($showCookieConsent)
        @cookieconsentview
    @endif
</div>
@stack('modals')
@livewireScripts
@cookieconsentscripts
@stack('scripts')
</body>
</html>
