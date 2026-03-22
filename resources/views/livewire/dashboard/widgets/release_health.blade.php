<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
            <div>
                <h3 class="h6 mb-1">{{ __('Release health') }}</h3>
                <p class="small text-body-secondary mb-0">{{ __('Release readiness and the milestones carrying current risk.') }}</p>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="border rounded-3 bg-body-tertiary p-3 h-100">
                    <div class="small text-body-secondary">{{ __('Tracked releases') }}</div>
                    <div class="h4 mb-0 mt-1">{{ $data['summary']['release_count'] }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded-3 bg-body-tertiary p-3 h-100">
                    <div class="small text-body-secondary">{{ __('At risk') }}</div>
                    <div class="h4 mb-0 mt-1">{{ $data['summary']['at_risk_count'] }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded-3 bg-body-tertiary p-3 h-100">
                    <div class="small text-body-secondary">{{ __('Next release') }}</div>
                    <div class="fw-semibold mt-1">{{ $data['summary']['upcoming_label'] ?? __('None') }}</div>
                </div>
            </div>
        </div>

        @if(empty($data['releases']))
            <p class="small text-body-secondary mb-0">{{ __('No release milestones are visible yet.') }}</p>
        @else
            <div class="d-flex flex-column gap-3">
                @foreach($data['releases'] as $release)
                    <div class="border rounded-3 p-3">
                        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                            <div>
                                @if($release['url'])
                                    <a href="{{ $release['url'] }}" class="fw-semibold text-decoration-none">
                                        {{ $release['name'] }}
                                    </a>
                                @else
                                    <div class="fw-semibold">{{ $release['name'] }}</div>
                                @endif

                                <div class="small text-body-secondary mt-1">
                                    {{ $release['project_key'] }} · {{ $release['window'] }}
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <span class="badge {{ $release['risk_tone'] === 'danger' ? 'text-bg-danger' : ($release['risk_tone'] === 'warning' ? 'text-bg-warning' : ($release['risk_tone'] === 'success' ? 'text-bg-success' : 'text-bg-light')) }}">
                                    {{ $release['risk_label'] }}
                                </span>
                                <span class="small text-body-secondary">{{ $release['progress_percent'] }}% {{ __('complete') }}</span>
                            </div>
                        </div>

                        <div class="row g-3 mt-2 small">
                            <div class="col-md-3">
                                <div class="text-body-secondary">{{ __('Open issues') }}</div>
                                <div class="fw-semibold">{{ $release['open_issues_count'] }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-body-secondary">{{ __('Done') }}</div>
                                <div class="fw-semibold">{{ $release['done_issues_count'] }}/{{ $release['total_issues_count'] }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-body-secondary">{{ __('Overdue') }}</div>
                                <div class="fw-semibold">{{ $release['overdue_issues_count'] }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-body-secondary">{{ __('Project') }}</div>
                                <div class="fw-semibold">{{ $release['project_name'] }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
