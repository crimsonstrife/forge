@php
    /** @var \App\Models\Project $project */
    $link = $project->repositoryLink; // HasOne ProjectRepository
@endphp

<div class="card shadow-sm mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">Repository</h5>

        @if($link)
            <span class="badge text-bg-success">Connected</span>
        @else
            <span class="badge text-bg-secondary">Not connected</span>
        @endif
    </div>

    <div class="card-body">
        @if($link)
            @php
                $repo = $link->repository;
                $repoUrl = $repo?->externalUrl();
                $supportsIssueSync = $repo?->supportsIssueSync() ?? false;
            @endphp

            <div class="row g-3 align-items-center">
                <div class="col-md-8">
                    <div class="mb-1">
                        <strong>Provider:</strong> {{ ucfirst($repo->provider) }}
                    </div>
                    <div class="mb-1">
                        <strong>Repository:</strong>
                        {{ $repo->displayPath() }}
                        @if($repo->host)
                            <span class="text-muted">({{ $repo->host }})</span>
                        @endif
                    </div>
                    @if($repo->provider === 'crucible' && $repo->slugPath() !== $repo->displayPath())
                        <div class="mb-2">
                            <span class="text-muted small">Repo path: {{ $repo->slugPath() }}</span>
                        </div>
                    @endif
                    @if($repoUrl)
                        <div class="mb-2">
                            <a class="link-primary" target="_blank" rel="noopener noreferrer" href="{{ $repoUrl }}">
                                {{ $repo->provider === 'crucible' ? 'Open in Crucible' : 'View on GitHub' }}
                            </a>
                        </div>
                    @endif
                    @if($repo?->default_branch)
                        <div class="mb-2 text-muted small">
                            Default branch: <code>{{ $repo->default_branch }}</code>
                        </div>
                    @endif
                    @if($repo->provider === 'crucible')
                        <div class="alert alert-light border small mb-3">
                            This project is linked to a Crucible repository. Forge uses the reverse Crucible → Forge project link as the source of truth for this connection.
                        </div>
                    @endif

                    <livewire:projects.manage-repository :project="$project" :link="$link" />
                </div>

                <div class="col-md-4">
                    <div class="border rounded p-3 bg-body-tertiary">
                        @if($supportsIssueSync)
                            <livewire:projects.repository-sync-status :link="$link" />
                        @else
                            <div class="small text-muted">Link status</div>
                            <div class="fw-semibold">VERIFIED</div>
                            <div class="small mt-1 text-muted">
                                Managed through Crucible. Issue import and sync are not used for this provider.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        @else
            @can('update', $project)
                <livewire:projects.connect-repository :project="$project" />
            @else
                <div class="alert alert-secondary mb-0">
                    You don’t have permission to connect a repository for this project.
                </div>
            @endcan
        @endif
    </div>
</div>
