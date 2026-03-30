@php
    /** @var \App\Models\Project $project */
    $codex = app(\App\Support\Codex\CodexConnection::class);
@endphp

<div>
    <div class="card shadow-sm">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-book text-body-secondary"></i>
                <h6 class="mb-0">Codex Workspace</h6>
            </div>
            @if($project->codex_workspace_id)
                <span class="badge bg-success-subtle text-success border border-success-subtle">
                    <i class="fas fa-link me-1"></i>Linked
                </span>
            @endif
        </div>

        <div class="card-body">
            @if($project->codex_workspace_id)
                {{-- Currently linked --}}
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="mb-1 fw-semibold">
                            <i class="fas fa-book me-1 text-primary"></i>
                            {{ $project->codex_workspace_slug ?? $project->codex_workspace_id }}
                        </p>
                        <p class="mb-0 text-body-secondary small">
                            <a href="{{ $codex->baseUrl() }}/workspaces/{{ $project->codex_workspace_slug }}"
                               target="_blank" rel="noopener">
                                Open workspace <i class="fas fa-external-link-alt ms-1" style="font-size:0.7rem;"></i>
                            </a>
                        </p>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger"
                            wire:click="unlink"
                            wire:confirm="Unlink this Codex workspace from the project?">
                        <i class="fas fa-unlink me-1"></i>Unlink
                    </button>
                </div>
            @else
                {{-- Not linked yet --}}
                <p class="text-body-secondary small mb-3">
                    Connect a Codex workspace to this project so team members can navigate directly between issues and documentation.
                </p>

                @if($error)
                    <div class="alert alert-danger py-2 small">{{ $error }}</div>
                @endif

                <div class="d-flex gap-2 mb-3">
                    <input type="text"
                           class="form-control form-control-sm"
                           placeholder="Search workspaces…"
                           wire:model.live.debounce.400ms="search" />
                    <button type="button" class="btn btn-sm btn-outline-secondary flex-shrink-0"
                            wire:click="loadWorkspaces"
                            wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="loadWorkspaces">
                            <i class="fas fa-search me-1"></i>Search
                        </span>
                        <span wire:loading wire:target="loadWorkspaces">
                            <i class="fas fa-spinner fa-spin me-1"></i>Loading…
                        </span>
                    </button>
                </div>

                @if($loading)
                    <div class="text-center py-3 text-body-secondary small">
                        <i class="fas fa-spinner fa-spin me-1"></i> Loading workspaces…
                    </div>
                @elseif(count($workspaces) > 0)
                    <div class="list-group list-group-flush border rounded">
                        @foreach($workspaces as $workspace)
                            <div class="list-group-item d-flex align-items-center justify-content-between py-2">
                                <div>
                                    <span class="fw-medium">{{ $workspace['name'] }}</span>
                                    <span class="text-body-secondary small ms-2">{{ $workspace['slug'] }}</span>
                                </div>
                                <button type="button"
                                        class="btn btn-sm btn-primary"
                                        wire:click="link('{{ $workspace['id'] }}', '{{ $workspace['slug'] }}')"
                                        wire:loading.attr="disabled">
                                    Link
                                </button>
                            </div>
                        @endforeach
                    </div>
                @elseif(!$loading && !$error)
                    <p class="text-body-secondary small mb-0">
                        Click <strong>Search</strong> to load available Codex workspaces.
                    </p>
                @endif
            @endif
        </div>
    </div>
</div>
