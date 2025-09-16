@php ob_start(); @endphp
<flux:main>
    {{ $slot }}
</flux:main>
@php($content = new \Illuminate\Support\HtmlString(ob_get_clean()))

@include('layouts.app', [
    'slot' => $content,
    'header' => $header ?? null,
])
