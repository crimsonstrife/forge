<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
            <div>
                <h3 class="h6 mb-1">{{ __('Team delivery') }}</h3>
                <p class="small text-body-secondary mb-0">
                    @if($data['summary']['scope_label'])
                        {{ __('Delivery signals for :team and its visible project work.', ['team' => $data['summary']['scope_label']]) }}
                    @else
                        {{ __('Delivery signals across the projects you can see.') }}
                    @endif
                </p>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="border rounded-3 bg-body-tertiary p-3 h-100">
                    <div class="small text-body-secondary">{{ __('Projects') }}</div>
                    <div class="h4 mb-0 mt-1">{{ $data['summary']['project_count'] }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded-3 bg-body-tertiary p-3 h-100">
                    <div class="small text-body-secondary">{{ __('Open issues') }}</div>
                    <div class="h4 mb-0 mt-1">{{ $data['summary']['open_issue_count'] }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded-3 bg-body-tertiary p-3 h-100">
                    <div class="small text-body-secondary">{{ __('Due soon') }}</div>
                    <div class="h4 mb-0 mt-1">{{ $data['summary']['due_soon_count'] }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded-3 bg-body-tertiary p-3 h-100">
                    <div class="small text-body-secondary">{{ __('Overdue') }}</div>
                    <div class="h4 mb-0 mt-1">{{ $data['summary']['overdue_count'] }}</div>
                </div>
            </div>
        </div>

        @if(empty($data['projects']))
            <p class="small text-body-secondary mb-0">{{ __('No visible project delivery data yet.') }}</p>
        @else
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>{{ __('Project') }}</th>
                        <th>{{ __('Open') }}</th>
                        <th>{{ __('Due soon') }}</th>
                        <th>{{ __('Overdue') }}</th>
                        <th>{{ __('Sprints') }}</th>
                        <th>{{ __('Updated') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data['projects'] as $project)
                        <tr>
                            <td>
                                <a href="{{ $project['url'] }}" class="fw-semibold text-decoration-none">
                                    {{ $project['name'] }}
                                </a>
                                <div class="small text-body-secondary">
                                    {{ $project['key'] }}
                                    @if($project['organization_name'])
                                        · {{ $project['organization_name'] }}
                                    @endif
                                </div>
                            </td>
                            <td>{{ $project['open_issues_count'] }}</td>
                            <td>{{ $project['due_soon_issues_count'] }}</td>
                            <td>
                                <span class="badge {{ $project['overdue_issues_count'] > 0 ? 'text-bg-danger' : 'text-bg-light' }}">
                                    {{ $project['overdue_issues_count'] }}
                                </span>
                            </td>
                            <td>{{ $project['active_sprints_count'] }}</td>
                            <td class="small text-body-secondary">{{ $project['updated_label'] ?? '—' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
