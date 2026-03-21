@php
    $mode = $mode ?? 'detail';
    $showEmpty = $showEmpty ?? false;
    $emptyText = $emptyText ?? 'No active timer';
    $emptyClass = $emptyClass ?? 'text-body-secondary';
    $windows = collect($windows ?? []);
    $visibleWindows = $mode === 'compact'
        ? $windows->filter(fn (array $window) => $window['open'])
        : $windows->filter(fn (array $window) => $window['due_at'] !== null);
    $formatSlaTime = static function (array $window, string $mode): string {
        if ($mode === 'compact') {
            if ($window['due_at'] === null) {
                return 'No timer set';
            }

            return ($window['breached'] ? 'Overdue ' : 'Due ') . $window['due_at']->diffForHumans();
        }

        if ($window['completed_at'] !== null) {
            return 'Met ' . $window['completed_at']->diffForHumans();
        }

        if ($window['due_at'] === null) {
            return 'No timer set';
        }

        return ($window['breached'] ? 'Breached ' : 'Due ') . $window['due_at']->diffForHumans();
    };
@endphp

@if($visibleWindows->isEmpty())
    @if($showEmpty)
        <span class="{{ $emptyClass }}">{{ $emptyText }}</span>
    @endif
@elseif($mode === 'compact')
    @foreach($visibleWindows as $window)
        <div class="text-nowrap">
            <span class="badge {{ $window['breached'] ? 'text-bg-danger' : 'text-bg-warning' }}">{{ $window['label'] }}</span>
            <span class="{{ $window['breached'] ? 'text-danger' : 'text-body-secondary' }}">
                {{ $formatSlaTime($window, $mode) }}
            </span>
        </div>
    @endforeach
@else
    <div class="border rounded p-3 bg-body-tertiary">
        <div class="small text-uppercase text-body-secondary fw-semibold mb-2">SLA targets</div>
        <div class="vstack gap-2">
            @foreach($visibleWindows as $window)
                <div class="border rounded p-2 {{ $window['breached'] ? 'border-danger bg-danger-subtle' : '' }}">
                    <div class="d-flex justify-content-between align-items-center gap-2">
                        <span class="fw-semibold small">{{ $window['label'] }}</span>
                        <span class="badge {{ $window['breached'] ? 'text-bg-danger' : ($window['open'] ? 'text-bg-warning' : 'text-bg-success') }}">
                            {{ $window['breached'] ? 'Breached' : ($window['open'] ? 'Open' : 'Met') }}
                        </span>
                    </div>
                    <div class="small text-body-secondary mt-1">
                        {{ $formatSlaTime($window, $mode) }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
