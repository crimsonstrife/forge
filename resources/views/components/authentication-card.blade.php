<div class="min-vh-100 d-flex flex-column justify-content-center align-items-center bg-body-tertiary py-4">
    <div class="mb-4">
        {{ $logo }}
    </div>

    <div class="card shadow-sm w-100" style="max-width: 28rem;">
        <div class="card-body px-4 py-4">
            {{ $slot }}
        </div>
    </div>
</div>
