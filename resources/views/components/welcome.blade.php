<div class="p-4 p-lg-5">
    <div class="d-flex align-items-center gap-3 mb-4">
        <x-application-mark style="height: 2.5rem;" />
        <div>
            <h1 class="h3 mb-1">{{ __('Welcome to :app', ['app' => config('app.name', 'Forge')]) }}</h1>
            <p class="text-body-secondary mb-0">
                {{ __('A Bootstrap-aligned workspace for projects, issues, goals, support, and delivery workflows.') }}
            </p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h2 class="h5">{{ __('Projects') }}</h2>
                    <p class="text-body-secondary mb-0">{{ __('Plan work, track milestones, and keep delivery context close to the team.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h2 class="h5">{{ __('Issues') }}</h2>
                    <p class="text-body-secondary mb-0">{{ __('Manage priorities, status, code references, attachments, comments, and time.') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
