<div class="d-grid gap-3">
    <div class="d-flex align-items-center justify-content-between gap-3">
        <h1 class="h5 mb-0">Organizations</h1>

        <div class="d-flex align-items-center gap-2">
            <input
                type="search"
                wire:model.debounce.300ms="q"
                placeholder="Search…"
                class="form-control"
                style="width: 18rem"
            >
            <a href="{{ route('organizations.create') }}" class="btn btn-primary">New Organization</a>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="list-group list-group-flush">
            @forelse($organizations as $org)
                <div class="list-group-item d-flex align-items-center justify-content-between">
                    <div>
                        <a href="{{ route('organizations.show', ['organization' => $org->slug]) }}"
                           class="fw-semibold link-primary text-decoration-none">{{ $org->name }}</a>
                        <div class="text-body-secondary small">slug: {{ $org->slug }}</div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('organizations.edit', ['organization' => $org->slug]) }}"
                           class="btn btn-sm btn-outline-secondary">Edit</a>
                        <button wire:click="delete('{{ $org->id }}')"
                                onclick="return confirm('Move this organization to trash?')"
                                class="btn btn-sm btn-danger">
                            Delete
                        </button>
                    </div>
                </div>
            @empty
                <div class="list-group-item text-center text-body-secondary py-4">
                    No organizations found.
                </div>
            @endforelse
        </div>
    </div>

    <div>
        {{ $organizations->links() }}
    </div>
</div>
