@props(['project'])

<div class="card shadow-sm">
    <div class="card-header d-flex align-items-center justify-content-between">
        {{-- Tabs (left) --}}
        <x-projects.nav-tabs :project="$project" />

        {{-- Actions (right, optional) --}}
        @isset($actions)
            <div class="d-flex align-items-center gap-2">
                {{ $actions }}
            </div>
        @endisset
    </div>

    <div class="card-body">
        {{ $slot }}
    </div>
</div>
