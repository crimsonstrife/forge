<div class="vstack gap-3">
    <div class="card">
        <div class="card-body">
            <form class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" wire:model.live.debounce.400ms="search" placeholder="Key, subject, email">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select class="form-select" wire:model.live="statusId">
                        <option value="">All</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Priority</label>
                    <select class="form-select" wire:model.live="priorityId">
                        <option value="">All</option>
                        @foreach($priorities as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Type</label>
                    <select class="form-select" wire:model.live="typeId">
                        <option value="">All</option>
                        @foreach($types as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>Key</th>
                    <th>Subject</th>
                    <th>Product</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Assignee</th>
                    <th>Opened</th>
                </tr>
                </thead>
                <tbody>
                @forelse($rows as $t)
                    <tr>
                        <td class="fw-semibold"><a href="{{ route('support.staff.show', ['key' => $t->key]) }}">{{ $t->key }}</a></td>
                        <td>{{ Str::limit($t->subject, 80) }}</td>
                        <td>{{ $t->product->name ?? '—' }}</td>
                        <td>{{ $t->status->name ?? '—' }}</td>
                        <td>{{ $t->priority->name ?? '—' }}</td>
                        <td>{{ $t->assignee->name ?? '—' }}</td>
                        <td class="text-nowrap">{{ $t->created_at->format('M j, Y g:ia') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-body-secondary">No tickets found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $rows->onEachSide(1)->links() }}
        </div>
    </div>
</div>
