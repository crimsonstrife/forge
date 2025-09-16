<div class="card">
    @php $ownerIsProject = $goal->owner_type === \App\Models\Project::class; @endphp
    <div class="card-header d-flex align-items-center">
        <span>Linked Work</span>
        <div class="ms-auto d-flex gap-2">
            @unless($ownerIsProject)
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="t-project" value="project" wire:model.live="types">
                    <label class="form-check-label" for="t-project">Projects</label>
                </div>
            @endunless

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" id="t-issue" value="issue" wire:model.live="types">
                <label class="form-check-label" for="t-issue">Issues</label>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <input type="search"
                   class="form-control"
                   placeholder="Search projects by name, or issues by key/summary…"
                   wire:model.live.debounce.300ms="q">
        </div>

        @if(!empty($results))
            <div class="list-group mb-3">
                @foreach($results as $row)
                    <div class="list-group-item d-flex align-items-center justify-content-between">
                        <div>
                            <div class="fw-semibold">{{ $row['label'] }}</div>
                            <small class="text-muted">{{ ucfirst($row['type']) }}</small>
                        </div>
                        <button type="button"
                                class="btn btn-sm btn-outline-primary"
                                wire:click="add('{{ $row['type'] }}','{{ $row['id'] }}')">
                            Link
                        </button>
                    </div>
                @endforeach
            </div>
        @endif

        <h6 class="mb-2">Current Links</h6>
        @if($goal->links->isEmpty())
            <div class="text-muted">No links yet.</div>
        @else
            <div class="list-group">
                @foreach($goal->links->sortBy('weight') as $link)
                    @php
                        $type = class_basename($link->linkable_type);
                        $linkId = (string) $link->getKey();
                        $m = $link->linkable;
                        $label = match ($type) {
                            'Project' => $m?->name ?? "#{$link->linkable_id}",
                            'Issue' => ($m?->key ? "[{$m->key}] " : '') . ($m?->summary ?? "#{$link->linkable_id}"),
                            default => "#{$link->linkable_id}",
                        };

                        $route = match ($type) {
                            'Project' => $m ? route('projects.show', $m) : null,
                            'Issue' => $m ? route('issues.show', ['project' => $m->project_id, 'issue' => $m]) : null,
                            default => null,
                        };
                    @endphp
                    <div class="list-group-item" wire:key="goal-link-{{ $linkId }}">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="me-3">
                                <div class="fw-semibold">{{ $type }}</div>
                                <small class="text-muted">{{ $label }}</small>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <div class="input-group input-group-sm" style="width: 120px;">
                                    <span class="input-group-text">Weight</span>
                                    <input type="number"
                                           class="form-control"
                                           wire:model.defer="weights.{{ $linkId }}">
                                </div>

                                @if($route)
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ $route }}">View</a>
                                @endif

                                <button type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        wire:click="remove('{{ $linkId }}')">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-end mt-3">
                <button type="button" class="btn btn-primary btn-sm" wire:click="saveWeights">Save Weights</button>
            </div>
        @endif
    </div>
</div>
