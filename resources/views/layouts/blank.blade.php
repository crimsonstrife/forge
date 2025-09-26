<div class="container py-3" x-data x-init="window.parent?.postMessage({type:'forge-embed-height', height: document.body.scrollHeight}, '*')">
    <div class="d-flex align-items-center gap-3 mb-3">
        <h2 class="h4 mb-0">{{ $project->name }}</h2>
        <div class="ms-auto d-flex align-items-center gap-3">
            <div class="progress" style="width: 220px;" title="Progress (done / total)">
                <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                    {{ $progress }}%
                </div>
            </div>
            <small class="text-body-secondary">{{ $doneCount }} / {{ $totalCount }}</small>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Title or key…">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Assignee</label>
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="assignee" placeholder="User ID (optional)">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Compact</label><br>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" wire:model.live="compact" id="compactSwitch">
                        <label class="form-check-label" for="compactSwitch">Enable compact cards</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Kanban --}}
    <div class="row g-3">
        @foreach ($columns as $col)
            <div class="col-12 col-md-6 col-lg-3">
                <div class="border rounded">
                    <div class="px-3 py-2 d-flex justify-content-between align-items-center" @class([
                        'bg-light' => ! $col['status']['is_done'],
                        'bg-success-subtle' => $col['status']['is_done'],
                    ])>
                        <strong>{{ $col['status']['name'] }}</strong>
                        <span class="badge text-bg-secondary">{{ count($col['items']) }}</span>
                    </div>
                    <div class="p-2" style="min-height: 200px;">
                        @forelse ($col['items'] as $it)
                            <div class="card mb-2 @if($compact) small @endif">
                                <div class="card-body py-2">
                                    <div class="d-flex justify-content-between">
                                        <div class="fw-medium text-truncate" title="{{ $it['title'] }}">{{ $it['title'] }}</div>
                                        <div class="ms-2 text-body-secondary">{{ $it['id'] }}</div>
                                    </div>
                                    @if(!$compact)
                                        <div class="mt-1 d-flex flex-wrap gap-1">
                                            @foreach(($it['tags'] ?? []) as $tag)
                                                <span class="badge text-bg-light">{{ $tag }}</span>
                                            @endforeach
                                        </div>
                                        @if($it['assignee'])
                                            <div class="mt-1 text-body-secondary">Assignee: {{ $it['assignee']['name'] }}</div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-body-secondary py-4">No issues</div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        // Auto-resize support for embed iframes
        const resize = () => window.parent?.postMessage({type:'forge-embed-height', height: document.body.scrollHeight}, '*');
        new ResizeObserver(resize).observe(document.body);
        window.addEventListener('load', resize);
    </script>
</div>
