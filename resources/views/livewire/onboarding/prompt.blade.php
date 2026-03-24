<div data-onboarding-root>
    @if(($config['enabled'] ?? false) === true)
        <script type="application/json" data-forge-onboarding-config>@json($config)</script>

        @if(($config['shouldPrompt'] ?? false) === true)
            <div
                class="modal fade"
                id="forgeOnboardingPrompt"
                tabindex="-1"
                aria-labelledby="forgeOnboardingPromptLabel"
                aria-hidden="true"
                data-bs-backdrop="static"
                data-bs-keyboard="false"
                data-forge-onboarding-prompt
            >
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header border-0 pb-0">
                            <div>
                                <p class="text-uppercase small text-body-secondary fw-semibold mb-1">{{ __('Welcome to Forge') }}</p>
                                <h2 class="modal-title h4 mb-0" id="forgeOnboardingPromptLabel">{{ __('Start with a quick walkthrough?') }}</h2>
                            </div>
                        </div>

                        <div class="modal-body pt-3">
                            <p class="text-body-secondary mb-3">
                                {{ __('Take a guided tour through dashboard workspaces, the Issue Explorer, projects, goals, support, search, and your account tools.') }}
                            </p>

                            <div class="rounded-3 bg-body-tertiary p-3 small text-body-secondary">
                                {{ __('You can relaunch the tour any time from your account menu or the Getting Started page.') }}
                            </div>
                        </div>

                        <div class="modal-footer border-0 justify-content-between flex-wrap gap-2">
                            <a href="{{ route('getting-started') }}" class="btn btn-link text-decoration-none px-0">
                                {{ __('Open Getting Started') }}
                            </a>

                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-secondary" data-onboarding-prompt-action="snooze">
                                    {{ __('Maybe later') }}
                                </button>
                                <button type="button" class="btn btn-link text-body-secondary text-decoration-none" data-onboarding-prompt-action="dismiss">
                                    {{ __("Don't show automatically again") }}
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-primary"
                                    data-start-tour="main-app"
                                    data-start-tour-route="{{ route('onboarding.tours.start', ['tour' => 'main-app']) }}"
                                >
                                    {{ __('Start tour') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
