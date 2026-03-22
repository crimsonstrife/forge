<div class="card h-100">
    <div class="card-body">
        <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
            <div>
                <h3 class="h6 mb-1">{{ __('Due soon') }}</h3>
                <p class="small text-body-secondary mb-0">{{ __('Work due in the next 14 days.') }}</p>
            </div>
        </div>

        @if(empty($data['items']))
            <p class="small text-body-secondary mb-0">{{ __('No upcoming deadlines.') }}</p>
        @else
            <div class="d-flex flex-column gap-3">
                @foreach($data['items'] as $issue)
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
                                {{ $issue['project_key'] ?? __('Project') }}
                                @if($issue['due_date'])
                                    · {{ $issue['due_date'] }}
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
    </div>
</div>
