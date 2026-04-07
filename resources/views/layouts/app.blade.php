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
<body>
<x-banner />

<div class="min-vh-100 d-flex flex-column bg-body">
    @livewire('navigation-menu')

    {{-- Header partial (uses the $header slot if present) --}}
    @include('layouts.partials.header', ['header' => $header ?? null])

    @auth
        <livewire:onboarding.prompt />
    @endauth

    <main class="flex-grow-1">
        {{ $slot }}
    </main>

    {{-- Footer partial --}}
    @include('layouts.partials.footer')

    @cookieconsentview
</div>

@stack('modals')
@livewireScripts
@cookieconsentscripts
@stack('scripts')
</body>
</html>
