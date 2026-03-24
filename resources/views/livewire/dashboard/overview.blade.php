<div class="d-flex flex-column gap-4">
    <section class="card border-0 shadow-sm bg-body-tertiary" data-tour="dashboard-overview">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-xl-row align-items-start justify-content-between gap-3">
                <div class="pe-xl-4">
                    <div class="text-uppercase small fw-semibold text-body-secondary">{{ __('Dashboard workspace') }}</div>
                    <h3 class="h3 mt-2 mb-2">{{ $activeWorkspaceDefinition['label'] }}</h3>
                    <p class="text-body-secondary mb-0">{{ $activeWorkspaceDefinition['description'] }}</p>
                </div>

                @if($heroStats !== [])
                    <div class="d-flex flex-wrap gap-2 justify-content-xl-end">
                        @foreach($heroStats as $stat)
                            <div class="rounded-3 border  px-3 py-2" style="min-width: 9rem;">
                                <div class="small text-body-secondary">{{ $stat['label'] }}</div>
                                <div class="fw-semibold mt-1">{{ $stat['value'] }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="mt-4" data-tour="dashboard-workspaces">
                <div class="d-flex flex-wrap gap-2">
                    @foreach($workspaces as $key => $workspace)
                        <button
                            type="button"
                            wire:click="activateWorkspace('{{ $key }}')"
                            class="btn {{ $activeWorkspace === $key ? 'btn-dark' : 'btn-outline-secondary' }} text-start"
                        >
                            <span>{{ $workspace['label'] }}</span>
                            @if($landingWorkspace === $key)
                                <span class="badge rounded-pill text-bg-light ms-2">{{ __('Landing') }}</span>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="mt-4 d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3" data-tour="dashboard-layout-controls">
                <p class="small text-body-secondary mb-0">
                    {{ __('Each workspace keeps its own widget visibility and order, so one dashboard route can behave like multiple landing pages.') }}
                </p>

                <div class="d-flex flex-wrap gap-2">
                    <button type="button" wire:click="$toggle('showCustomizer')" class="btn btn-outline-secondary btn-sm">
                        {{ $showCustomizer ? __('Hide layout controls') : __('Customize layout') }}
                    </button>

                    @if($landingWorkspace !== $activeWorkspace)
                        <button type="button" wire:click="makeWorkspaceDefault('{{ $activeWorkspace }}')" class="btn btn-dark btn-sm">
                            {{ __('Make landing page') }}
                        </button>
                    @endif
                </div>
            </div>

            @if($showCustomizer)
                <div class="mt-4 pt-4 border-top">
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-2">
                            <div>
                                <h4 class="h6 mb-1">{{ __('Widget controls') }}</h4>
                                <p class="small text-body-secondary mb-0">{{ __('Show, hide, and reorder widgets for this workspace.') }}</p>
                            </div>

                            <button type="button" wire:click="resetWorkspaceLayout" class="btn btn-outline-secondary btn-sm">
                                {{ __('Reset layout') }}
                            </button>
                        </div>

                        <div class="row g-3">
                            @foreach($customizerWidgets as $widget)
                                <div class="col-md-6 col-xl-4">
                                    <div class="border rounded-3  h-100 p-3">
                                        <div class="d-flex align-items-start justify-content-between gap-3">
                                            <div>
                                                <div class="fw-semibold">{{ $widget['label'] }}</div>
                                                <div class="small text-body-secondary mt-1">{{ $widget['description'] }}</div>
                                            </div>

                                            <span class="badge {{ $widget['enabled'] ? 'text-bg-dark' : 'text-bg-light' }}">
                                                {{ $widget['enabled'] ? __('Visible') : __('Hidden') }}
                                            </span>
                                        </div>

                                        <div class="d-flex flex-wrap gap-2 mt-3">
                                            <button type="button" wire:click="moveWidgetUp('{{ $widget['id'] }}')" class="btn btn-outline-secondary btn-sm">
                                                {{ __('Up') }}
                                            </button>
                                            <button type="button" wire:click="moveWidgetDown('{{ $widget['id'] }}')" class="btn btn-outline-secondary btn-sm">
                                                {{ __('Down') }}
                                            </button>
                                            <button type="button" wire:click="toggleWidget('{{ $widget['id'] }}')" class="btn btn-outline-secondary btn-sm">
                                                {{ $widget['enabled'] ? __('Hide') : __('Show') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <div class="row g-4">
        @foreach($visibleWidgets as $widget)
            <section class="{{ $widget['span'] }}" wire:key="dashboard-widget-{{ $widget['id'] }}">
                @include('livewire.dashboard.widgets.'.$widget['view'], ['data' => $widgetData[$widget['id']] ?? []])
            </section>
        @endforeach
    </div>
</div>
