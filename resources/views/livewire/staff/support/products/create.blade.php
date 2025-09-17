<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="fw-semibold">New Service Product</div>
        <a href="{{ route('support.staff.products.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
    </div>

    <form wire:submit.prevent="save" class="card-body vstack gap-3">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Key</label>
                <input type="text" class="form-control" wire:model.defer="key" placeholder="e.g. APP">
                @error('key') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-8">
                <label class="form-label">Name</label>
                <input type="text" class="form-control" wire:model.defer="name" placeholder="e.g. Mobile App">
                @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
            <div class="col-12">
                <label class="form-label">Description</label>
                <textarea rows="2" class="form-control" wire:model.defer="description"></textarea>
                @error('description') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Default Project</label>
                <select class="form-select" wire:model="defaultProjectId">
                    <option value="">— None —</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} @if($p->key) ({{ $p->key }}) @endif</option>
                    @endforeach
                </select>
                @error('defaultProjectId') <div class="text-danger small">{{ $message }}</div> @enderror
                <div class="form-text">Used to auto-assign new tickets to a project.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Associated Projects</label>
                <select class="form-select" wire:model="projectIds" multiple size="6">
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} @if($p->key) ({{ $p->key }}) @endif</option>
                    @endforeach
                </select>
                @error('projectIds') <div class="text-danger small">{{ $message }}</div> @enderror
                <div class="form-text">Context only. Default Project is used for routing.</div>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button class="btn btn-primary">Create Product</button>
        </div>
    </form>
</div>
