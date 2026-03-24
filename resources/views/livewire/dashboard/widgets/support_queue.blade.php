<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
            <div>
                <h3 class="h6 mb-1">{{ __('Support queue') }}</h3>
                <p class="small text-body-secondary mb-0">{{ __('Open tickets, SLA pressure, and assignment gaps.') }}</p>
            </div>

            <a href="{{ route('support.staff.index') }}" class="btn btn-outline-secondary btn-sm">
                {{ __('Open triage') }}
            </a>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="border rounded-3 bg-body-tertiary p-3 h-100">
                    <div class="small text-body-secondary">{{ __('Open') }}</div>
                    <div class="h4 mb-0 mt-1">{{ $data['summary']['open_count'] }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded-3 bg-body-tertiary p-3 h-100">
                    <div class="small text-body-secondary">{{ __('Breached') }}</div>
                    <div class="h4 mb-0 mt-1">{{ $data['summary']['breached_count'] }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded-3 bg-body-tertiary p-3 h-100">
                    <div class="small text-body-secondary">{{ __('Unassigned') }}</div>
                    <div class="h4 mb-0 mt-1">{{ $data['summary']['unassigned_count'] }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded-3 bg-body-tertiary p-3 h-100">
                    <div class="small text-body-secondary">{{ __('Mine') }}</div>
                    <div class="h4 mb-0 mt-1">{{ $data['summary']['mine_count'] }}</div>
                </div>
            </div>
        </div>

        @if(empty($data['tickets']))
            <p class="small text-body-secondary mb-0">{{ __('No open tickets in the queue.') }}</p>
        @else
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>{{ __('Ticket') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Assignee') }}</th>
                        <th>{{ __('SLA') }}</th>
                        <th>{{ __('Opened') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data['tickets'] as $ticket)
                        <tr>
                            <td>
                                <a href="{{ $ticket['url'] }}" class="fw-semibold text-decoration-none">
                                    {{ $ticket['key'] }}
                                </a>
                                <div class="small text-body-secondary">
                                    {{ $ticket['subject'] }}
                                    @if($ticket['product_name'])
                                        · {{ $ticket['product_name'] }}
                                    @endif
                                </div>
                            </td>
                            <td>{{ $ticket['status_name'] }}</td>
                            <td>{{ $ticket['assignee_name'] ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $ticket['breached'] ? 'text-bg-danger' : 'text-bg-light' }}">
                                    {{ $ticket['sla_label'] }}
                                </span>
                            </td>
                            <td class="small text-body-secondary">{{ $ticket['opened_label'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
