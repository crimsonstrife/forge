@php ob_start(); @endphp
<div class="min-vh-100 d-flex flex-column justify-content-center align-items-center bg-body-tertiary py-5">
    <div class="w-100 px-3" style="max-width: 24rem;">
        <a href="{{ url('/') }}" class="d-flex flex-column align-items-center gap-2 text-body text-decoration-none fw-medium mb-4">
            <x-application-mark style="height: 2.5rem;" />
            <span class="visually-hidden">{{ config('app.name', 'Forge') }}</span>
        </a>

        {{ $slot }}
    </div>
</div>
@php($content = new \Illuminate\Support\HtmlString(ob_get_clean()))

@include('layouts.guest', [
    'slot' => $content,
    'header' => $header ?? null,
    'showNavigation' => false,
    'showFooter' => false,
    'showCookieConsent' => false,
])
