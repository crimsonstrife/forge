<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
