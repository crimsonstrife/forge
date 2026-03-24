<div class="d-grid gap-3">
    <div class="card border-0 shadow-sm">
        <div class="card-body d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
            <div>
                <h3 class="h6 mb-1">{{ __('Solo mode today') }}</h3>
                <p class="small text-body-secondary mb-0">{{ __('A focused execution surface with current work, next items, and timers.') }}</p>
            </div>

            <div class="d-flex flex-wrap gap-3 small text-body-secondary">
                <span>{{ $data['open_issue_count'] }} {{ __('open issues') }}</span>
                <span>{{ $data['due_soon_count'] }} {{ __('due soon') }}</span>
                <a href="{{ route('today.index') }}" class="text-decoration-underline">{{ __('Open full today view') }}</a>
            </div>
        </div>
    </div>

    <livewire:today.today-panel />
</div>
