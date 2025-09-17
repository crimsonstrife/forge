<div class="vstack gap-3">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h2 class="h5 mb-1">{{ $ticket->key }} — {{ $ticket->subject }}</h2>
                    <div class="small text-body-secondary">
                        From {{ $ticket->submitter_name }} &lt;{{ $ticket->submitter_email }}&gt; · Opened {{ $ticket->created_at->diffForHumans() }}
                    </div>
                </div>
                <div class="text-end">
                    <button
                        class="btn btn-outline-primary"
                        wire:click="convertToIssue"
                        @disabled(!$projectId)  {{-- Disable until a project is chosen --}}
                        title="{{ $projectId ? 'Convert to Issue' : 'Select a project first' }}"
                    >
                        Convert to Issue
                    </button>
                    @error('projectId') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            <hr>
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="mb-2 fw-semibold">Original description</div>
                    <div class="border rounded p-3 bg-body-tertiary">
                        {!! nl2br(e($ticket->body)) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <form wire:submit.prevent="saveMeta" class="vstack gap-2">

                        {{-- NEW: Project --}}
                        <div>
                            <label class="form-label">Project <span class="text-body-secondary">(required to convert)</span></label>
                            <select class="form-select" wire:model="projectId">
                                <option value="">— Select a project —</option>
                                @foreach($projects as $proj)
                                    <option value="{{ $proj->id }}">
                                        {{ $proj->name }} @if(!empty($proj->key)) ({{ $proj->key }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('projectId') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="form-label">Status</label>
                            <select class="form-select" wire:model="statusId">
                                @foreach($statuses as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Priority</label>
                            <select class="form-select" wire:model="priorityId">
                                @foreach($priorities as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Type</label>
                            <select class="form-select" wire:model="typeId">
                                @foreach($types as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Assignee</label>
                            <select class="form-select" wire:model="assigneeId">
                                <option value="">Unassigned</option>
                                @foreach($assignees as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach
                            </select>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-primary" type="submit">Save</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <form wire:submit.prevent="addInternalNote" class="card">
                <div class="card-header fw-semibold">Add internal note</div>
                <div class="card-body vstack gap-2">
                    <textarea class="form-control" rows="4" wire:model.defer="internalNote"></textarea>
                    @error('internalNote') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <button class="btn btn-secondary">Add note</button>
                </div>
            </form>

            <div class="card mt-3">
                <div class="card-header fw-semibold">Internal notes</div>
                <div class="card-body vstack gap-2">
                    @forelse($internal as $c)
                        <div class="border rounded p-2">
                            <div class="small text-body-secondary mb-1">{{ $c->created_at->format('M j, Y g:ia') }}</div>
                            {!! nl2br(e($c->body)) !!}
                        </div>
                    @empty
                        <div class="text-body-secondary">No internal notes.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <form wire:submit.prevent="addPublicReply" class="card">
                <div class="card-header fw-semibold">Reply to customer</div>
                <div class="card-body vstack gap-2">
                    <textarea class="form-control" rows="4" wire:model.defer="publicReply"></textarea>
                    @error('publicReply') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <button class="btn btn-primary">Send reply</button>
                </div>
            </form>

            <div class="card mt-3">
                <div class="card-header fw-semibold">Public conversation</div>
                <div class="card-body vstack gap-2">
                    @forelse($public as $c)
                        <div class="border rounded p-2">
                            <div class="small text-body-secondary mb-1">{{ $c->created_at->format('M j, Y g:ia') }}</div>
                            {!! nl2br(e($c->body)) !!}
                        </div>
                    @empty
                        <div class="text-body-secondary">No replies yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
