<div class="card shadow-sm">
    <div class="card-body">
        <h4 class="h6 mb-3">Notes</h4>

        <div class="mb-3">
            <input type="text" class="form-control mb-2" placeholder="Title (optional)" wire:model.defer="newTitle">
            <textarea class="form-control" rows="3" placeholder="Write a quick note…" wire:model.defer="newBody"></textarea>
            <div class="mt-2">
                <button type="button" class="btn btn-primary btn-sm" wire:click="add">Add Note</button>
            </div>
        </div>

        <ul class="list-unstyled mb-0">
            @forelse($notes as $n)
                <li class="border rounded p-2 mb-2">
                    @if($editingId === $n->id)
                        <input type="text" class="form-control mb-2" wire:model.defer="editingTitle">
                        <textarea class="form-control mb-2" rows="3" wire:model.defer="editingBody"></textarea>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary btn-sm" wire:click="save">Save</button>
                            <button class="btn btn-outline-secondary btn-sm" wire:click="cancel">Cancel</button>
                        </div>
                    @else
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="me-2">
                                <div class="fw-semibold">{{ $n->title ?? 'Untitled' }}</div>
                                @if($n->body)
                                    <div class="small text-body">{{ $n->body }}</div>
                                @endif
                                <div class="small text-body-secondary mt-1">{{ $n->created_at?->diffForHumans() }}</div>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-secondary btn-sm" wire:click="edit('{{ $n->id }}')">Edit</button>
                                <button class="btn btn-outline-primary btn-sm" wire:click="convertToIssue('{{ $n->id }}')">Convert</button>
                                <button class="btn btn-outline-danger btn-sm" wire:click="delete('{{ $n->id }}')">Delete</button>
                            </div>
                        </div>
                    @endif
                </li>
            @empty
                <li class="text-body-secondary small">No notes yet.</li>
            @endforelse
        </ul>
    </div>
</div>
