<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
            <div>
                <h3 class="h6 mb-1">{{ __('Executive summary') }}</h3>
                <p class="small text-body-secondary mb-0">{{ __('A compact health scan for delivery, release risk, and support pressure.') }}</p>
            </div>
        </div>

        <div class="row g-3 mb-4">
            @foreach($data['stats'] as $stat)
                <div class="col-md-6 col-xl-3">
                    <div class="border rounded-3 bg-body-tertiary p-3 h-100">
                        <div class="small text-body-secondary">{{ $stat['label'] }}</div>
                        <div class="h4 mb-0 mt-1">{{ $stat['value'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex flex-column gap-3">
            @foreach($data['highlights'] as $highlight)
                <div class="border rounded-3 p-3">
                    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                        <div>
                            <div class="small text-uppercase fw-semibold text-body-secondary">{{ $highlight['label'] }}</div>
                            <div class="mt-1">{{ $highlight['text'] }}</div>
                        </div>

                        <div class="d-flex gap-2 align-items-center">
                            <span class="badge {{ $highlight['tone'] === 'danger' ? 'text-bg-danger' : ($highlight['tone'] === 'warning' ? 'text-bg-warning' : ($highlight['tone'] === 'success' ? 'text-bg-success' : 'text-bg-light')) }}">
                                {{ ucfirst($highlight['tone']) }}
                            </span>

                            @if($highlight['url'])
                                <a href="{{ $highlight['url'] }}" class="btn btn-outline-secondary btn-sm">
                                    {{ __('Open') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
