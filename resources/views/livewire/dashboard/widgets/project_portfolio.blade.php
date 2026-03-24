<div class="card h-100">
    <div class="card-body">
        <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
            <div>
                <h3 class="h6 mb-1">{{ __('Projects') }}</h3>
                <p class="small text-body-secondary mb-0">{{ __('Visible projects and their current load.') }}</p>
            </div>

            <a href="{{ route('projects.create') }}" class="btn btn-outline-secondary btn-sm">
                {{ __('New project') }}
            </a>
        </div>

        @if(empty($data['items']))
            <p class="small text-body-secondary mb-0">{{ __('No visible projects yet.') }}</p>
        @else
            <div class="row g-3">
                @foreach($data['items'] as $project)
                    <div class="col-12">
                        <a href="{{ $project['url'] }}" class="card border text-reset text-decoration-none h-100">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start justify-content-between gap-3">
                                    <div>
                                        <div class="fw-semibold">{{ $project['name'] }}</div>
                                        <div class="small text-body-secondary mt-1">
                                            {{ $project['key'] }}
                                            @if($project['organization_name'])
                                                · {{ $project['organization_name'] }}
                                            @endif
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <div class="fw-semibold">{{ $project['open_issues_count'] }}</div>
                                        <div class="small text-body-secondary">{{ __('open') }}</div>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-3 mt-3 small text-body-secondary">
                                    <span>{{ $project['due_soon_issues_count'] }} {{ __('due soon') }}</span>
                                    <span>{{ $project['active_sprints_count'] }} {{ __('active sprints') }}</span>
                                    @if($project['updated_label'])
                                        <span>{{ __('Updated') }} {{ $project['updated_label'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
