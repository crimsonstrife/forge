@php ob_start(); @endphp
<div class="min-vh-100 row g-0 bg-body">
    <div class="col-lg-6 d-none d-lg-flex flex-column justify-content-between bg-dark text-white p-5">
        <a href="{{ url('/') }}" class="d-flex align-items-center gap-2 text-white text-decoration-none fs-5 fw-medium">
            <x-application-mark style="height: 2.5rem;" />
            {{ config('app.name', 'Forge') }}
        </a>

        @php([$message, $author] = str(Illuminate\Foundation\Inspiring::quotes()->random())->explode('-'))

        <div>
            <blockquote class="mb-0">
                <p class="lead mb-2">&ldquo;{{ trim($message) }}&rdquo;</p>
                <footer class="text-white-50">{{ trim($author) }}</footer>
            </blockquote>
        </div>
    </div>

    <div class="col-12 col-lg-6 d-flex align-items-center justify-content-center p-4">
        <div class="w-100" style="max-width: 24rem;">
            <a href="{{ url('/') }}" class="d-flex flex-column align-items-center gap-2 text-body text-decoration-none fw-medium mb-4 d-lg-none">
                <x-application-mark style="height: 2.25rem;" />
                <span class="visually-hidden">{{ config('app.name', 'Forge') }}</span>
            </a>

            {{ $slot }}
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
