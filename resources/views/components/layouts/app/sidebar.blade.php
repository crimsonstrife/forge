@php ob_start(); @endphp
<div class="container py-4">
    {{ $slot }}
</div>
@php($content = new \Illuminate\Support\HtmlString(ob_get_clean()))

@include('layouts.app', [
    'slot' => $content,
    'header' => $header ?? null,
])
