<div class="card h-100" data-tour="issues-overview">
    <div class="card-body">
        <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
            <div>
                <h3 class="h6 mb-1">{{ __('My open issues') }}</h3>
                <p class="small text-body-secondary mb-0">{{ __('Assigned work that is still in motion.') }}</p>
            </div>

            <a href="{{ route('issues.create.global') }}" class="btn btn-outline-secondary btn-sm">
                {{ __('New issue') }}
            </a>
        </div>

        @if(!empty($data['status_summary']))
            <div class="d-flex flex-wrap gap-2 mb-3">
                @foreach($data['status_summary'] as $status)
                    <span class="badge rounded-pill border"
                          style="background-color: {{ $status['color'] }}15; color: {{ $status['color'] }};">
                        {{ $status['label'] }} · {{ $status['total'] }}
                    </span>
                @endforeach
            </div>
        @endif

        @if(empty($data['items']))
            <p class="small text-body-secondary mb-0">{{ __('Nothing assigned right now.') }}</p>
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
    </div>
</div>
