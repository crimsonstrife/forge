<div class="d-grid gap-3">

    {{-- Running timer --}}
    <div class="card shadow-sm">
        <div class="card-body d-flex align-items-center justify-content-between">
            <div class="me-3">
                <div class="text-body-secondary small mb-1">Now working on</div>
                @if($running)
                    <div class="fw-semibold">
                        <a class="text-decoration-none"
                           href="{{ route('issues.show', ['project' => $running->issue->project_id, 'issue' => $running->issue]) }}">
                            {{ $running->issue->summary }}
                        </a>
                        <span class="badge ms-2"
                              style="background-color: {{ $running->issue->status?->color ?? 'transparent' }}20;">
                            {{ $running->issue->status?->name }}
                        </span>
                    </div>
                    <div class="small text-body-secondary mt-1">
                        Started {{ $running->started_at?->diffForHumans() }}
                    </div>
                @else
                    <div class="text-body-secondary">No active timer.</div>
                @endif
            </div>
            @if($running)
                <a class="btn btn-outline-primary btn-sm"
                   href="{{ route('issues.focus', ['project' => $running->issue->project_id, 'issue' => $running->issue]) }}">
                    Open focus
                </a>
            @endif
        </div>
    </div>

    {{-- Next (pinned) --}}
    @if($pinned->isNotEmpty())
        <div class="card shadow-sm">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h3 class="h6 mb-0">Next</h3>
                <small class="text-body-secondary">{{ $pinned->count() }} pinned</small>
            </div>

            <div class="list-group list-group-flush">
                @foreach ($pinned as $i)
                    <div class="list-group-item d-flex justify-content-between align-items-center gap-3">
                        <div class="flex-grow-1">
                            <a class="text-decoration-none fw-semibold"
                               href="{{ route('issues.show', ['project' => $i->project_id, 'issue' => $i->id]) }}">
                                {{ $i->summary }}
                            </a>
                            <span class="badge ms-2"
                                  style="background-color: {{ $i->status?->color ?? 'transparent' }}20;">
                                {{ $i->status?->name }}
                            </span>
                            <div class="small text-body-secondary">Updated {{ $i->updated_at?->diffForHumans() }}</div>
                        </div>
                        <div class="d-inline-flex gap-2 flex-shrink-0">
                            {{-- Start/Stop timer --}}
                            <livewire:issues.issue-quick-timer :issue-id="$i->id" :wire:key="'qt-next-'.$i->id" />
                            {{-- Unpin/Pin --}}
                            <livewire:issues.next-toggle :issue-id="$i->id" :is-next="$i->is_next" :wire:key="'next-next-'.$i->id" />
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- On Deck (not pinned) --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h3 class="h6 mb-0">On Deck</h3>
            <small class="text-body-secondary">{{ $onDeck->count() }} items</small>
        </div>

        <div class="list-group list-group-flush">
            @forelse ($onDeck as $i)
                <div class="list-group-item d-flex justify-content-between align-items-center gap-3">
                    <div class="flex-grow-1">
                        <a class="text-decoration-none fw-semibold"
                           href="{{ route('issues.show', ['project' => $i->project_id, 'issue' => $i->id]) }}">
                            {{ $i->summary }}
                        </a>
                        <span class="badge ms-2"
                              style="background-color: {{ $i->status?->color ?? 'transparent' }}20;">
                            {{ $i->status?->name }}
                        </span>
                        <div class="small text-body-secondary">Updated {{ $i->updated_at?->diffForHumans() }}</div>
                    </div>
                    <div class="d-inline-flex gap-2 flex-shrink-0">
                        <livewire:issues.issue-quick-timer :issue-id="$i->id" :wire:key="'qt-deck-'.$i->id" />
                        <livewire:issues.next-toggle :issue-id="$i->id" :is-next="$i->is_next" :wire:key="'next-deck-'.$i->id" />
                    </div>
                </div>
            @empty
                <div class="list-group-item text-body-secondary small">Nothing assigned yet.</div>
            @endforelse
        </div>
    </div>
</div>
