@php ob_start(); @endphp
<x-authentication-card>
    <x-slot name="logo">
        @auth
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
                <x-application-mark style="height: 2.5rem;" />
                <span class="fw-semibold">{{ config('app.name', 'Forge') }}</span>
            </a>
        @else
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
                <x-application-mark style="height: 2.5rem;" />
                <span class="fw-semibold">{{ config('app.name', 'Forge') }}</span>
            </a>
        @endauth
    </x-slot>

    {{ $slot }}
</x-authentication-card>
@php($content = new \Illuminate\Support\HtmlString(ob_get_clean()))

@include('layouts.guest', [
    'slot' => $content,
    'header' => $header ?? null,
    'showNavigation' => false,
    'showFooter' => false,
    'showCookieConsent' => false,
])
