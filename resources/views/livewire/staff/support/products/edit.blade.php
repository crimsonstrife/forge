<div class="vstack gap-3">
    <div class="card">
        <div class="card-header fw-semibold">Product Details</div>
        <form wire:submit.prevent="save" class="card-body vstack gap-3">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Key</label>
                    <input type="text" class="form-control" wire:model.defer="key">
                    @error('key') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-8">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" wire:model.defer="name">
                    @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea rows="2" class="form-control" wire:model.defer="description"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Default Project</label>
                    <select class="form-select" wire:model="defaultProjectId">
                        <option value="">— None —</option>
                        @foreach($allProjects as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} @if($p->key) ({{ $p->key }}) @endif</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Associated Projects</label>
                    <select class="form-select" wire:model="projectIds" multiple size="6">
                        @foreach($allProjects as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} @if($p->key) ({{ $p->key }}) @endif</option>
                        @endforeach
                    </select>
                    <div class="form-text">Used for context; default project is used when routing new tickets.</div>
                </div>
            </div>

            <hr>

            <div class="row g-3">
                <div class="col-md-4">
                    <div class="fw-semibold mb-2">Map Types</div>
                    @foreach($ticketTypes as $t)
                        <div class="mb-2">
                            <label class="form-label small mb-1">{{ $t->name }}</label>
                            <select class="form-select form-select-sm" wire:model="typeMap.{{ $t->id }}">
                                <option value="">— Unmapped (project default) —</option>
                                @foreach($issueTypes as $it)
                                    <option value="{{ $it->id }}">{{ $it->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>

                <div class="col-md-4">
                    <div class="fw-semibold mb-2">Map Statuses</div>
                    @foreach($ticketStatuses as $s)
                        <div class="mb-2">
                            <label class="form-label small mb-1">{{ $s->name }}</label>
                            <select class="form-select form-select-sm" wire:model="statusMap.{{ $s->id }}">
                                <option value="">— Unmapped (project initial) —</option>
                                @foreach($issueStatuses as $is)
                                    <option value="{{ $is->id }}">{{ $is->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>

                <div class="col-md-4">
                    <div class="fw-semibold mb-2">Map Priorities</div>
                    @foreach($ticketPriorities as $p)
                        <div class="mb-2">
                            <label class="form-label small mb-1">{{ $p->name }}</label>
                            <select class="form-select form-select-sm" wire:model="priorityMap.{{ $p->id }}">
                                <option value="">— Unmapped (project default) —</option>
                                @foreach($issuePriorities as $ip)
                                    <option value="{{ $ip->id }}">{{ $ip->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
