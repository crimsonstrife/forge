<div class="card">
    <div class="card-header">Dependencies</div>
    <div class="card-body">
        <div class="mb-2">
            <input type="search" class="form-control" placeholder="Find goal to add as blocker…" wire:model.live.debounce.300ms="q">
        </div>

        @if(!empty($results))
            <div class="list-group mb-3">
                @foreach($results as $r)
                    <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between"
                            wire:click="addBlocker('{{ $r['id'] }}')">
                        <span>{{ $r['label'] }}</span>
                        <span class="badge bg-outline-secondary">Add</span>
                    </button>
                @endforeach
            </div>
        @endif

        <h6 class="mb-2">Blockers</h6>
        @if($goal->blockers->isEmpty())
            <div class="text-muted">No blockers.</div>
        @else
            <ul class="list-group">
                @foreach($goal->blockers as $g)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <a class="link-primary" href="{{ route('goals.show', $g) }}">{{ $g->name }}</a>
                        <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeBlocker('{{ $g->id }}')">Remove</button>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
