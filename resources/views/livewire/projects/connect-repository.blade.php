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
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Provider</label>
                                <select class="form-select" wire:model.live="provider">
                                    <option value="github">GitHub</option>
                                    <option value="crucible">Crucible Repos</option>
                                </select>
                                @error('provider') <div class="text-danger small">{{ $message }}</div> @enderror
                                <div class="form-text">
                                    GitHub imports issues. Crucible links a repo already connected back to this Forge project.
                                </div>
                            </div>

                            @if($provider === 'github')
                                <div class="col-md-6">
                                    <label class="form-label">Host</label>
                                    <input type="text" class="form-control" wire:model.defer="host" placeholder="github.com">
                                    <div class="form-text">Use a custom domain for GitHub Enterprise later.</div>
                                </div>
                            @endif
                        </div>

                        <hr class="my-4">

                        @if($provider === 'github')
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Owner / Organization</label>
                                    <input type="text" class="form-control" wire:model.defer="owner" placeholder="helicalgames">
                                    @error('owner') <div class="text-danger small">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Repository Name</label>
                                    <input type="text" class="form-control" wire:model.defer="name" placeholder="forge">
                                    @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mt-3">
                                <div class="alert alert-secondary mb-0">
                                    <strong>Tip:</strong> Coordinates are in <code>owner/name</code> format (for example <code>helicalgames/forge</code>).
                                </div>
                            </div>

                            @if($projectHasIssues)
                                <div class="alert alert-warning mt-3 mb-0">
                                    GitHub issue import is disabled for projects that already have issues. Use a Crucible repository link instead, or connect GitHub on a new project before import.
                                </div>
                            @endif

                            <hr class="my-4">

                            <div class="mb-3">
                                <label class="form-label">Authorization</label>
                                <div class="d-flex flex-column gap-2">
                                    <div>
                                        <a href="{{ route('auth.github.redirect') }}"
                                           class="btn btn-outline-dark btn-sm me-2">
                                            <i class="bi bi-github me-1"></i>
                                            Connect with GitHub
                                        </a>
                                        <span class="text-muted small">or paste a Personal Access Token.</span>
                                    </div>
                                    <input type="password" class="form-control" wire:model.defer="token"
                                           placeholder="ghp_xxx… (scopes: repo, read:org)">
                                    @error('token') <div class="text-danger small">{{ $message }}</div> @enderror
                                    <div class="form-text">
                                        Minimum scopes: <code>repo</code> and <code>read:org</code> when the repository is in an org.
                                        Your token is stored encrypted and only used for sync.
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

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
                                        Assignees/reporters only sync when the matching user has connected their GitHub account.
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                Link a Crucible repository that is already connected back to this Forge project from Crucible. Forge uses that reverse link as the source of truth.
                            </div>

                            @if($crucibleError)
                                <div class="alert alert-danger py-2 small">{{ $crucibleError }}</div>
                            @endif

                            @if($selectedCrucibleRepository)
                                <div class="border rounded p-3 mb-3 bg-body-tertiary">
                                    <div class="d-flex align-items-start justify-content-between gap-3">
                                        <div>
                                            <div class="fw-semibold">
                                                {{ $selectedCrucibleRepository['organization_name'] ?? $selectedCrucibleRepository['organization_slug'] }}/{{ $selectedCrucibleRepository['name'] ?? $selectedCrucibleRepository['slug'] }}
                                            </div>
                                            <div class="small text-body-secondary">
                                                Slug: {{ $selectedCrucibleRepository['organization_slug'] }}/{{ $selectedCrucibleRepository['slug'] }}
                                            </div>
                                            @if(!empty($selectedCrucibleRepository['description']))
                                                <div class="small text-body-secondary mt-1">{{ $selectedCrucibleRepository['description'] }}</div>
                                            @endif
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="clearCrucibleSelection">
                                            Change
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <div class="d-flex gap-2 mb-3">
                                <input type="text"
                                       class="form-control"
                                       placeholder="Search Crucible repositories…"
                                       wire:model.live.debounce.400ms="crucibleSearch">
                                <button type="button"
                                        class="btn btn-outline-secondary flex-shrink-0"
                                        wire:click="loadCrucibleRepositories"
                                        wire:loading.attr="disabled"
                                        wire:target="loadCrucibleRepositories">
                                    <span wire:loading.remove wire:target="loadCrucibleRepositories">Search</span>
                                    <span wire:loading wire:target="loadCrucibleRepositories">Loading…</span>
                                </button>
                            </div>

                            <input type="hidden" wire:model="owner">
                            <input type="hidden" wire:model="name">

                            @error('owner') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
                            @error('name') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

                            @if($loadingCrucibleRepositories)
                                <div class="text-muted small">Loading repositories…</div>
                            @elseif(count($crucibleRepositories) > 0)
                                <div class="list-group border rounded">
                                    @foreach($crucibleRepositories as $repo)
                                        <div class="list-group-item d-flex align-items-start justify-content-between gap-3">
                                            <div>
                                                <div class="fw-medium">
                                                    {{ $repo['organization_name'] ?? $repo['organization_slug'] }}/{{ $repo['name'] ?? $repo['slug'] }}
                                                </div>
                                                <div class="small text-body-secondary">
                                                    {{ $repo['organization_slug'] }}/{{ $repo['slug'] }}
                                                    @if(!empty($repo['visibility']))
                                                        <span class="mx-1">•</span>{{ strtoupper((string) $repo['visibility']) }}
                                                    @endif
                                                </div>
                                                @if(!empty($repo['description']))
                                                    <div class="small text-body-secondary mt-1">{{ $repo['description'] }}</div>
                                                @endif
                                            </div>
                                            <button type="button"
                                                    class="btn btn-sm btn-primary flex-shrink-0"
                                                    wire:click="selectCrucibleRepository('{{ $repo['organization_slug'] }}', '{{ $repo['slug'] }}')">
                                                Select
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-body-secondary small mb-0">
                                    Search by organization or repository name to load available Crucible repositories.
                                </p>
                            @endif

                            <div class="alert alert-light border mt-3 mb-0 small">
                                Crucible links do not import issues into Forge. They provide a repo destination for the project’s Code experience and keep the relationship aligned with Crucible’s existing Forge project integration.
                            </div>
                        @endif
                    </div>

                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a href="{{ route('projects.code', ['project' => $project]) }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>

                        <button type="submit"
                                class="btn btn-primary"
                                wire:loading.attr="disabled"
                                @disabled($provider === 'github' && $projectHasIssues)>
                            <span wire:loading.remove>
                                {{ $provider === 'crucible' ? 'Link Repository' : 'Connect & Start Import' }}
                            </span>
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
