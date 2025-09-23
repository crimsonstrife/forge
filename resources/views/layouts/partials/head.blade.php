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

@vite($viteEntries)

@fluxAppearance
@livewireStyles

@stack('meta')
@stack('styles')
