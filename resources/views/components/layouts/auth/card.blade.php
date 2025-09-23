@php ob_start(); @endphp
<x-authentication-card>
    <div class="bg-muted flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
        <div class="flex w-full max-w-md flex-col gap-6">
            <x-slot name="logo">
                <!-- Brand -->
                @auth
                    <a class="flex flex-col items-center gap-2 text-black dark:text-white" href="{{ route('dashboard') }}">
                        <x-application-logo />
                    </a>
                @else
                    <a class="flex flex-col items-center gap-2 text-black dark:text-white" href="{{ url('/') }}">
                        <x-application-logo />
                    </a>
                @endauth
            </x-slot>

            <x-validation-errors class="mb-3" />

            @session('status')
            <div class="alert alert-success mb-3" role="alert">
                {{ $value }}
            </div>
            @endsession

            <div class="rounded-xl border bg-white dark:bg-stone-950 dark:border-stone-800 text-stone-800 shadow-xs">
                <div class="px-10 py-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</x-authentication-card>
@php($content = new \Illuminate\Support\HtmlString(ob_get_clean()))

@include('layouts.guest', [
    'slot' => $content,
    'header' => $header ?? null,
])
