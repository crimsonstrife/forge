<div class="min-vh-100 d-flex flex-column justify-content-center align-items-center bg-body-tertiary py-5">
    <div class="mb-3">
        {{ $logo }}
    </div>

    <div class="card shadow-sm" style="max-width: 480px; width: 100%;">
        <div class="card-body p-4">
            {{ $slot }}
        </div>
    </div>
</div>
