<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="apple-mobile-web-app-title" content="Forge" />

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="manifest" href="/favicons/site.webmanifest" />
    <link rel="icon" type="image/png" href="/favicons/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
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
<body x-data="themeSwitcher()" :class="{ 'dark': switchOn }">
<x-banner />

<div class="min-vh-100 bg-body-tertiary">
    @livewire('navigation-menu')

    @if (isset($header))
        <header class="bg-body border-bottom">
            <div class="container py-3">
                {{ $header }}
            </div>
        </header>
    @endif

    <main>
        {{ $slot }}
    </main>
    @cookieconsentview
</div>
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
