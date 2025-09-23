@php ob_start(); @endphp
<div class="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
    <div class="flex w-full max-w-sm flex-col gap-2">
        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ $user ? route('dashboard') : url('/') }}">
            <x-application-logo />
        </a>
        <div class="flex flex-col gap-6">
            {{ $slot }}
        </div>
    </div>
</div>
@php($content = new \Illuminate\Support\HtmlString(ob_get_clean()))

@include('layouts.guest', [
    'slot' => $content,
    'header' => $header ?? null,
])
