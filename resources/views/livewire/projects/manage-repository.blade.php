<div>
    {{-- Action buttons (rendered when a link already exists) --}}
    <div class="d-flex flex-wrap gap-2">
        <button class="btn btn-primary btn-sm" wire:click="syncNow(false)" wire:loading.attr="disabled">
            <span wire:loading.remove>Sync issues now</span>
            <span wire:loading>Syncing…</span>
        </button>
        <button class="btn btn-outline-secondary btn-sm" wire:click="openEditor">
            Edit connection
        </button>
        <button class="btn btn-outline-secondary btn-sm" wire:click="syncNow(true)">
            Queue sync
        </button>
    </div>

    {{-- Modal --}}
    @if($showEditor)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.35)">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit repository connection</h5>
                        <button type="button" class="btn-close" wire:click="closeEditor"></button>
                    </div>

                    <form wire:submit.prevent="save">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">New token (optional)</label>
                                <input type="password" class="form-control" wire:model.defer="token" placeholder="Paste new OAuth/PAT token">
                                <div class="form-text">Leave blank to keep the current token.</div>
                                @error('token') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Token type</label>
                                <select class="form-select" wire:model="token_type">
                                    <option value="oauth">OAuth</option>
                                    <option value="pat">Personal Access Token</option>
                                </select>
                            </div>

                            <hr>

                            <h6 class="mb-2">Status mapping</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">External: open</label>
                                    <select class="form-select" wire:model.defer="statusMapping.open">
                                        <option value="">— Select status —</option>
                                        @foreach($statuses as $s)
                                            @if(!$s['is_done'])
                                                <option value="{{ $s['id'] }}">{{ $s['name'] }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">External: closed</label>
                                    <select class="form-select" wire:model.defer="statusMapping.closed">
                                        <option value="">— Select status —</option>
                                        @foreach($statuses as $s)
                                            @if($s['is_done'])
                                                <option value="{{ $s['id'] }}">{{ $s['name'] }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="alert alert-light border mt-3 small mb-0">
                                People mapping is automatic: assignees/reporters sync only when a matching user has their
                                {{ strtoupper($link->repository->provider) }} account connected.
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" wire:click="closeEditor">Cancel</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove>Save changes</span>
                                <span wire:loading>Saving…</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Prevent body scroll while modal shown --}}
        <script>document.body.classList.add('modal-open');</script>
    @else
        <script>document.body.classList.remove('modal-open');</script>
    @endif
</div>
