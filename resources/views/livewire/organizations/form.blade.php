<div class="card shadow-sm">
    <div class="card-body">
        <div class="mb-3">
            <label for="org_name" class="form-label">Name</label>
            <input id="org_name" type="text" class="form-control @error('name') is-invalid @enderror"
                   wire:model.defer="name" autofocus>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="card-footer d-flex justify-content-end gap-2">
        <button wire:click="save" class="btn btn-primary">
            {{ $isEditing ? 'Save Changes' : 'Create Organization' }}
        </button>

        @if ($isEditing && $organization?->exists && filled($organization->slug))
            <a href="{{ route('organizations.show', ['organization' => $organization->slug]) }}" class="btn btn-outline-secondary">
                Cancel
            </a>
        @else
            <a href="{{ route('organizations.index') }}" class="btn btn-outline-secondary">
                Cancel
            </a>
        @endif
    </div>
</div>
