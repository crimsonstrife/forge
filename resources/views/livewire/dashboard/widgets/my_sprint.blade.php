<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
            <div>
                <h3 class="h6 mb-1">{{ __('My sprint') }}</h3>
                <p class="small text-body-secondary mb-0">{{ __('Assigned sprint work and the commitments currently running.') }}</p>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="border rounded-3 bg-body-tertiary p-3 h-100">
                    <div class="small text-body-secondary">{{ __('Active sprints') }}</div>
                    <div class="h4 mb-0 mt-1">{{ $data['summary']['active_sprint_count'] }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded-3 bg-body-tertiary p-3 h-100">
                    <div class="small text-body-secondary">{{ __('Assigned in sprint') }}</div>
                    <div class="h4 mb-0 mt-1">{{ $data['summary']['assigned_issue_count'] }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded-3 bg-body-tertiary p-3 h-100">
                    <div class="small text-body-secondary">{{ __('Due this week') }}</div>
                    <div class="h4 mb-0 mt-1">{{ $data['summary']['due_this_week_count'] }}</div>
                </div>
            </div>
        </div>

        @if(empty($data['sprints']) && empty($data['issues']))
            <p class="small text-body-secondary mb-0">{{ __('No active sprint assignments right now.') }}</p>
        @else
            @if(!empty($data['sprints']))
                <div class="row g-3 mb-4">
                    @foreach($data['sprints'] as $sprint)
                        <div class="col-lg-6">
                            <div class="border rounded-3 p-3 h-100">
                                <div class="d-flex align-items-start justify-content-between gap-3">
                                    <div>
                                        <div class="fw-semibold">{{ $sprint['name'] }}</div>
                                        <div class="small text-body-secondary mt-1">
                                            {{ $sprint['project_key'] }} · {{ $sprint['window'] }}
                                        </div>
                                    </div>

                                    @if($sprint['project_url'])
                                        <a href="{{ $sprint['project_url'] }}" class="btn btn-outline-secondary btn-sm">
                                            {{ __('Open scrum') }}
                                        </a>
                                    @endif
                                </div>

                                <div class="progress mt-3" role="progressbar" aria-valuenow="{{ $sprint['completion_percent'] }}" aria-valuemin="0" aria-valuemax="100" style="height: .5rem;">
                                    <div class="progress-bar" style="width: {{ $sprint['completion_percent'] }}%;"></div>
                                </div>

                                <div class="d-flex flex-wrap gap-3 mt-3 small text-body-secondary">
                                    <span>{{ $sprint['done_issues_count'] }}/{{ $sprint['total_issues_count'] }} {{ __('done') }}</span>
                                    <span>{{ $sprint['assigned_to_me_count'] }} {{ __('assigned to me') }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if(!empty($data['issues']))
                <div class="d-flex flex-column gap-3">
                    @foreach($data['issues'] as $issue)
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div class="min-w-0">
                                @if($issue['url'])
                                    <a href="{{ $issue['url'] }}" class="fw-medium text-decoration-none text-reset d-block">
                                        {{ $issue['key'] }} — {{ $issue['summary'] }}
                                    </a>
                                @else
                                    <div class="fw-medium">{{ $issue['key'] }} — {{ $issue['summary'] }}</div>
                                @endif

                                <div class="small text-body-secondary mt-1">
                                    {{ $issue['project_key'] }}
                                    @if($issue['sprint_name'])
                                        · {{ $issue['sprint_name'] }}
                                    @endif
                                    @if($issue['due_label'])
                                        · {{ __('Due') }} {{ $issue['due_label'] }}
                                    @endif
                                </div>
                            </div>

                            @if($issue['status_name'])
                                <span class="badge rounded-pill flex-shrink-0"
                                      style="background-color: {{ $issue['status_color'] }}15; color: {{ $issue['status_color'] }};">
                                    {{ $issue['status_name'] }}
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>
</div>
