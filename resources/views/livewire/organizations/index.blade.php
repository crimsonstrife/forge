<div class="space-y-6">
    <div class="flex items-center justify-between gap-3">
        <h1 class="text-xl font-semibold">Organizations</h1>

        <div class="flex items-center gap-3">
            <input
                type="search"
                wire:model.debounce.300ms="q"
                placeholder="Search…"
                class="input input-bordered w-56"
            />
            <a href="{{ route('organizations.create') }}" class="btn btn-primary">New Organization</a>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card">
        <div class="divide-y">
            @forelse($organizations as $org)
                <div class="flex items-center justify-between p-4">
                    <div>
                        <a href="{{ route('organizations.show', ['organization' => $org]) }}"
                           class="font-medium hover:underline">
                            {{ $org->name }}
                        </a>
                        <div class="text-xs opacity-70">slug: {{ $org->slug }}</div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('organizations.edit', ['organization' => $org]) }}" class="btn btn-sm">
                            Edit
                        </a>
                        <button wire:click="delete('{{ $org->id }}')"
                                onclick="return confirm('Move this organization to trash?')"
                                class="btn btn-sm btn-error">
                            Delete
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center opacity-70">No organizations found.</div>
            @endforelse
        </div>
    </div>

    <div>
        {{ $organizations->links() }}
    </div>
</div>
