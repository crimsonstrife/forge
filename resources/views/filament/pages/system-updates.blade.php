<x-filament::page>
    <div class="fi-section rounded-xl border p-6">
        <h2 class="text-lg font-semibold">Current Version</h2>
        <p class="text-2xl font-bold mt-1">{{ $currentVersion }}</p>
        @if ($updateAvailable)
            <p class="mt-2 text-amber-600">A new update is available.</p>
        @else
            <p class="mt-2 text-emerald-600">You are up to date.</p>
        @endif
    </div>
</x-filament::page>
