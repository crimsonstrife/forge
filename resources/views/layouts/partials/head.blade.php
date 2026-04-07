@php
    /** Defaults (override when including) */
    $pageTitle = $title ?? config('app.name', 'Forge');
    /** @var array<int, string> $viteEntries */
    $viteEntries = $viteEntries ?? ['resources/css/app.css', 'resources/js/app.js'];
    $includeMaterialIcons = $includeMaterialIcons ?? true;
@endphp

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="apple-mobile-web-app-title" content="{{ config('app.name', 'Forge') }}" />

<title>{{ $pageTitle }}</title>

<link rel="manifest" href="/favicons/site.webmanifest" />
<link rel="icon" type="image/png" href="/favicons/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/svg+xml" href="/favicon.svg" />
<link rel="shortcut icon" href="/favicon.ico" />
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

@if ($includeMaterialIcons)
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
@endif
{{-- THEME: initial paint without flash --}}
<script>
    (() => {
        try {
            const ls = localStorage;

            // Migrate legacy boolean key if you had one (isDark → theme)
            const legacy = ls.getItem('isDark');
            if (legacy !== null && !ls.getItem('theme')) {
                ls.setItem('theme', JSON.parse(legacy) ? 'dark' : 'light');
            }

            const getPref = () => ls.getItem('theme') || 'auto';
            const prefersDark = () => matchMedia('(prefers-color-scheme: dark)').matches;
            const resolve = (pref = getPref()) => pref === 'auto' ? (prefersDark() ? 'dark' : 'light') : pref;

            const apply = (theme) => {
                const root = document.documentElement;
                root.setAttribute('data-bs-theme', theme);
                root.classList.toggle('dark', theme === 'dark'); // for any .dark-based utilities

                // Set browser UI color for PWA / mobile chrome
                let meta = document.querySelector('meta[name="theme-color"]');
                if (!meta) { meta = document.createElement('meta'); meta.name = 'theme-color'; document.head.appendChild(meta); }
                meta.content = theme === 'dark' ? '#0b0f13' : '#ffffff';

                window.__isDarkTheme = () => document.documentElement.getAttribute('data-bs-theme') === 'dark';

                // Let Alpine/Livewire listeners react if needed
                window.dispatchEvent(new CustomEvent('theme:changed', { detail: { theme } }));

                window.dispatchEvent(new Event('tiny-reinit'));
            };

            // Public setter for UI controls
            window.__setTheme = (pref) => { ls.setItem('theme', pref); apply(resolve(pref)); };

            // Apply now
            apply(resolve());

            // Follow system changes when in "auto"
            matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                if (getPref() === 'auto') apply(resolve('auto'));
            });
        } catch (e) { /* no-op */ }
    })();
</script>
@vite($viteEntries)

@livewireStyles

@stack('meta')
@stack('styles')
