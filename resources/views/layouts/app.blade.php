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
