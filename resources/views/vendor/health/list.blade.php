@php
    $stale = $lastRanAt && $lastRanAt->diffInMinutes() > 5;
@endphp

<div class="container px-0">
    <div class="row justify-content-center text-center mb-3">
        <div class="col-12">
            <h4 class="fw-bold text-body-emphasis mb-2">
                {{ __('health::notifications.laravel_health') }}
            </h4>
        </div>

        <div class="col-12 mb-2">
            {{-- Keep the pulse logo; Bootstrap color via text-danger --}}
            <div class="d-inline-flex align-items-center justify-content-center">
                <x-health-logo class="text-danger" />
            </div>
        </div>

        @if ($lastRanAt)
            <div class="col-12">
                <div class="small fw-medium {{ $stale ? 'text-danger' : 'text-secondary' }}">
                    {{ __('health::notifications.check_results_from') }} {{ $lastRanAt->diffForHumans() }}
                </div>
            </div>
        @endif
    </div>

    @if (count($checkResults?->storedCheckResults ?? []))
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3 g-md-4">
            @foreach ($checkResults->storedCheckResults as $result)
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex gap-3 align-items-start">
                            <x-health-status-indicator :result="$result" />

                            <div class="text-start">
                                <dd class="fw-bold text-body-emphasis mb-1">
                                    {{ $result->label }}
                                </dd>

                                <dt class="small text-body-secondary mb-0">
                                    @if (!empty($result->notificationMessage))
                                        {{ $result->notificationMessage }}
                                    @else
                                        {{ $result->shortSummary }}
                                    @endif
                                </dt>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
