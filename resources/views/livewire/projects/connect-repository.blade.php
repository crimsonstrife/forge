@php
    /** @var \App\Models\Project $project */
    /** @var array<int, array{id:string,name:string,is_done:bool}> $statuses */
@endphp

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Connect Repository</h5>
                    <span class="text-muted small">Project: {{ $project->key ?? $project->name ?? '—' }}</span>
                </div>

                <form wire:submit.prevent="save">
                    <div class="card-body">
                        {{-- Provider / Host --}}
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Provider</label>
                                <select class="form-select" wire:model="provider">
                                    <option value="github">GitHub</option>
                                    <option value="gitlab" disabled>GitLab (soon)</option>
                                    <option value="gitea" disabled>Gitea (soon)</option>
                                </select>
                                @error('provider') <div class="text-danger small">{{ $message }}</div> @enderror
                                <div class="form-text">
                                    We’ll start with GitHub, but the interface supports adding others later.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Host</label>
                                <input type="text" class="form-control" wire:model.defer="host" placeholder="github.com">
                                <div class="form-text">Use a custom domain for GitHub Enterprise / self-hosted Gitea later.</div>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Repository Coordinates --}}
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Owner / Organization</label>
                                <input type="text" class="form-control" wire:model.defer="owner" placeholder="helicalgames">
                                @error('owner') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Repository Name</label>
                                <input type="text" class="form-control" wire:model.defer="name" placeholder="umbrella-forge">
                                @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="alert alert-secondary mb-0">
                                <div class="d-flex align-items-start">
                                    <div class="me-2">
                                        <i class="bi bi-info-circle"></i>
                                    </div>
                                    <div>
                                        <strong>Tip:</strong> Coordinates are in <code>owner/name</code> format (e.g. <code>helicalgames/forge</code>).
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Authorization --}}
                        <div class="mb-3">
                            <label class="form-label">Authorization</label>
                            <div class="d-flex flex-column gap-2">
                                <div>
                                    <a href="{{ route('auth.github.redirect') }}"
                                       class="btn btn-outline-dark btn-sm me-2">
                                        <i class="bi bi-github me-1"></i>
                                        Connect with GitHub
                                    </a>
                                    <span class="text-muted small">— or paste a Personal Access Token:</span>
                                </div>
                                <input type="password" class="form-control" wire:model.defer="token"
                                       placeholder="ghp_xxx… (scopes: repo, read:org)">
                                <div class="form-text">
                                    Minimum scopes: <code>repo</code> (for issues) and <code>read:org</code> if the repo is in an org.
                                    Your token is stored encrypted and used only for sync.
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Status Mapping --}}
                        <h6 class="mb-3">Status Mapping</h6>
                        <p class="text-muted small">
                            Map external states to your project’s custom statuses. You can refine this later.
                        </p>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">External: <span class="fw-semibold">open</span></label>
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
                                <label class="form-label">External: <span class="fw-semibold">closed</span></label>
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

                        <div class="mt-3">
                            <div class="alert alert-light border">
                                <div class="mb-1 fw-semibold">People Mapping</div>
                                <div class="text-muted small">
                                    Assignees/reporters will only sync if the matching user has connected their GitHub account.
                                    Otherwise, we’ll leave those fields unset.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a href="{{ route('projects.show', ['project' => $project]) }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>

                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>Connect & Start Import</span>
                            <span wire:loading>Connecting…</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Livewire flash/notify hook (optional) --}}
            <div class="mt-3">
                <div wire:loading.delay>
                    <div class="text-muted small">Working…</div>
                </div>
            </div>
        </div>
    </div>
</div>
