<div class="max-w-xl space-y-6">
    <div>
        <label class="label">Name</label>
        <input type="text" class="input input-bordered w-full" wire:model.defer="name" autofocus />
        @error('name') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="flex items-center gap-3">
        <button wire:click="save" class="btn btn-primary">
            {{ $isEditing ? 'Save Changes' : 'Create Organization' }}
        </button>

        @if ($isEditing && $organization?->exists)
            <a href="{{ route('organizations.show', ['organization' => $organization]) }}" class="btn btn-ghost">
                Cancel
            </a>
        @else
            <a href="{{ route('organizations.index') }}" class="btn btn-ghost">
                Cancel
            </a>
        @endif
    </div>
</div>
