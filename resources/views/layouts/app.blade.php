<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head', [
        'title' => config('app.name', 'Forge'),
        'viteEntries' => [
            'resources/css/app.css',
            'resources/js/app.js',
            'resources/js/editor/tinymce-init.js', // app-only
        ],
        'includeMaterialIcons' => true,
    ])
</head>
<body x-data="themeSwitcher()" :class="{ 'dark': switchOn }">
<x-banner />

<div class="min-vh-100 bg-body-tertiary">
    @livewire('navigation-menu')

    {{-- Header partial (uses the $header slot if present) --}}
    @include('layouts.partials.header', ['header' => $header ?? null])

    @auth
        <livewire:onboarding.prompt />
    @endauth

    <main>
        {{ $slot }}
    </main>
    {{-- Footer partial --}}
    @include('layouts.partials.footer', [
    'footerLogo' => view('components.application-footer-logo', ['class' => 'h-24'])
    ])
    @cookieconsentview
</div>

@stack('modals')
@fluxScripts
@livewireScripts
@cookieconsentscripts
@stack('scripts')
</body>
</html>
