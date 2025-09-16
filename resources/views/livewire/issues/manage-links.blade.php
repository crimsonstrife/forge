<div class="d-flex flex-column gap-3">
    @can('link', $issue)
        <div class="border rounded p-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Link type</label>
                    <select class="form-select" wire:model="typeId">
                        <option value="">Select type…</option>
                        @foreach($this->types as $t)
                            <option value="{{ $t->id }}">
                                {{ $t->is_symmetric ? $t->name : ($t->name . ' / ' . $t->inverse_name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Search issue</label>
                    <input type="text"
                           class="form-control"
                           placeholder="Key or summary…"
                           wire:model.live.debounce.300ms="q"
                           wire:input.debounce.300ms="search">
                @if(!empty($results))
                    <div class="list-group mt-2">
                            @foreach($results as $r)
                                <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between"
                                        wire:click="link('{{ $r['id'] }}')">
                                    <span>
                                        <span class="badge text-bg-light me-2">{{ $r['project_key'] }} — {{ $r['key'] }}</span>
                                        {{ $r['summary'] }}
                                    </span>
                                    <small class="text-body-secondary">Link</small>
                                </button>
                            @endforeach
                        </div>
                    @elseif(strlen($q) >= 2)
                        <div class="text-body-secondary small mt-2">No matches found.</div>
                    @endif
                </div>
            </div>
        </div>
    @endcan

    <div class="row g-3">
        <div class="col-md-6">
            <h6 class="mb-2">Outgoing</h6>
            @if($outgoing->isEmpty())
                <div class="text-body-secondary small">No outgoing links.</div>
            @else
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                    @foreach($outgoing as $l)
                        <li class="d-flex justify-content-between align-items-start gap-2">
                            <div>
                                <span class="badge text-bg-light">{{ $l['label'] }}</span>
                                <a class="link-primary ms-2" href="{{ $l['url'] }}">
                                    {{ $l['issue_key'] }} — {{ $l['summary'] }}
                                </a>
                            </div>
                            @can('link', $issue)
                                <button class="btn btn-sm btn-outline-danger" wire:click="unlink('{{ $l['id'] }}')">Remove</button>
                            @endcan
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="col-md-6">
            <h6 class="mb-2">Incoming</h6>
            @if($incoming->isEmpty())
                <div class="text-body-secondary small">No incoming links.</div>
            @else
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                    @foreach($incoming as $l)
                        <li class="d-flex justify-content-between align-items-start gap-2">
                            <div>
                                <span class="badge text-bg-light">{{ $l['label'] }}</span>
                                <a class="link-primary ms-2" href="{{ $l['url'] }}">
                                    {{ $l['issue_key'] }} — {{ $l['summary'] }}
                                </a>
                            </div>
                            @can('link', $issue)
                                <button class="btn btn-sm btn-outline-danger" wire:click="unlink('{{ $l['id'] }}')">Remove</button>
                            @endcan
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
