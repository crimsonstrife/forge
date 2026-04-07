@php ob_start(); @endphp
<div class="min-vh-100 d-flex flex-column justify-content-center align-items-center bg-body-tertiary py-5">
    <div class="w-100 px-3" style="max-width: 28rem;">
        <a class="navbar-brand d-flex align-items-center justify-content-center gap-2 mb-4" href="{{ auth()->check() ? route('dashboard') : url('/') }}">
            <x-application-logo />
        </a>
        <div class="card shadow-sm">
            <div class="card-body p-4">
            {{ $slot }}
            </div>
        </div>
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
