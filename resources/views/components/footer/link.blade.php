@props([
    'route' => null,        // e.g. 'docs.index'
    'href' => null,         // e.g. '/docs' (fallback if route missing)
    'params' => [],         // route params (if any)
    'newTab' => false,      // force open in new tab
])

@php
    $routeExists = $route && \Illuminate\Support\Facades\Route::has($route);
    $url = $routeExists ? route($route, $params) : ($href ?? '#');

    // If not forced, open absolute external URLs in new tab.
    $isExternal = \Illuminate\Support\Str::startsWith($url, ['http://', 'https://']) &&
                  !\Illuminate\Support\Str::startsWith($url, config('app.url', ''));

    $target = ($newTab || $isExternal) ? '_blank' : null;
    $rel = ($newTab || $isExternal) ? 'noopener' : null;
@endphp

<a href="{{ $url }}"
   @if($target) target="{{ $target }}" rel="{{ $rel }}" @endif
    {{ $attributes->merge(['class' => 'link-body-emphasis text-decoration-none']) }}>
    {{ $slot }}
</a>
