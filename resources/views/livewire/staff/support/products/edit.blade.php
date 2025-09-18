<div class="vstack gap-3">
    {{-- Product Details --}}
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

    {{-- Ingest Keys --}}
    <div class="card">
        <div class="card-header fw-semibold d-flex align-items-center justify-content-between">
            <span>API Ingest Keys</span>
        </div>

        <div class="card-body vstack gap-3">
            {{-- One-time token alert --}}
            @if($newKeyToken)
                <div class="alert alert-warning d-flex align-items-start gap-2">
                    <div>
                        <div class="fw-semibold">Copy this token now — it won't be shown again.</div>
                        <code class="user-select-all d-inline-block mt-1" x-data
                              x-on:click="$clipboard('{{ $newKeyToken }}')">{{ $newKeyToken }}</code>
                    </div>
                    <button type="button" class="btn-close ms-auto" aria-label="Close"
                            wire:click="$set('newKeyToken', null)"></button>
                </div>
            @endif

            {{-- Generate new key --}}
            <div class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label class="form-label mb-1">Label (optional)</label>
                    <input type="text" class="form-control" wire:model.defer="newKeyLabel" placeholder="e.g. UE4 build server, QA rig">
                    @error('newKeyLabel') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button type="button" class="btn btn-success ms-auto"
                            wire:click="generateKey">Generate Key</button>
                </div>
            </div>

            {{-- Keys table --}}
            @if($this->ingestKeys->isEmpty())
                <div class="text-body-secondary small">No keys yet.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                        <tr>
                            <th style="width:30%">Label</th>
                            <th style="width:15%">Status</th>
                            <th style="width:20%">Last Used</th>
                            <th style="width:20%">Created</th>
                            <th style="width:15%" class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($this->ingestKeys as $k)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $k->name ?? '—' }}</div>
                                    <div class="small text-body-secondary">ID: {{ $k->id }}</div>
                                </td>
                                <td>
                                    @if($k->revoked_at)
                                        <span class="badge text-bg-secondary">Revoked</span>
                                    @else
                                        <span class="badge text-bg-success">Active</span>
                                    @endif
                                </td>
                                <td class="small">
                                    {{ $k->last_used_at?->diffForHumans() ?? '—' }}
                                </td>
                                <td class="small">
                                    {{ $k->created_at?->format('Y-m-d H:i') }}
                                </td>
                                <td class="text-end">
                                    @if(!$k->revoked_at)
                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                wire:click="revokeKey('{{ $k->id }}')"
                                                wire:confirm="Revoke this key? Clients using it will stop working.">
                                            Revoke
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                wire:click="deleteKey('{{ $k->id }}')"
                                                wire:confirm="Delete this revoked key permanently?">
                                            Delete
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
