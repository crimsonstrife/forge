@php
    /** @var \App\Models\Project $project */
    $link = $project->repositoryLink; // HasOne ProjectRepository
    $issuesCount = $project->issues()->count();
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
                $repo = $link->repository()->first();
            @endphp

            <div class="row g-3 align-items-center">
                <div class="col-md-8">
                    {{-- existing provider/repo markup --}}
                    <div class="mb-1">
                        <strong>Provider:</strong> {{ strtoupper($repo->provider) }}
                    </div>
                    <div class="mb-1">
                        <strong>Repository:</strong>
                        {{ $repo->owner }}/{{ $repo->name }}
                        @if($repo->host)
                            <span class="text-muted">({{ $repo->host }})</span>
                        @endif
                    </div>
                    @if($repo->provider === 'github')
                        <div class="mb-2">
                            <a class="link-primary" target="_blank"
                               href="https://{{ $repo->host ?? 'github.com' }}/{{ $repo->owner }}/{{ $repo->name }}">
                                View on GitHub
                            </a>
                        </div>
                    @endif

                    <livewire:projects.manage-repository :project="$project" :link="$link" />
                </div>

                <div class="col-md-4">
                    <div class="border rounded p-3 bg-body-tertiary">
                        <livewire:projects.repository-sync-status :link="$link" />
                    </div>
                </div>
            </div>

        @else
            @can('connect', [\App\Models\ProjectRepository::class, $project])
                @if($issuesCount > 0)
                    <div class="alert alert-warning mb-0">
                        <strong>Repository connection disabled:</strong>
                        this project already has issues. To keep the initial import one-way and avoid conflicts,
                        connect a repository only on projects without issues.
                    </div>
                @else
                    <livewire:projects.connect-repository :project="$project" />
                @endif
            @else
                <div class="alert alert-secondary mb-0">
                    You don’t have permission to connect a repository for this project.
                </div>
            @endcan
        @endif
    </div>
</div>
