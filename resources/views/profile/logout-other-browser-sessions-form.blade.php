<div class="card shadow-sm">
    <div class="card-body">
        <div class="mb-2">
            <div class="fw-semibold">{{ __('Browser Sessions') }}</div>
            <div class="text-body-secondary small">
                {{ __('Manage and log out your active sessions on other browsers and devices.') }}
            </div>
        </div>

        <p class="text-body-secondary small mb-3">
            {{ __('If necessary, you may log out of all other browser sessions across your devices. Some recent sessions are listed below.') }}
        </p>

        @if (count($this->sessions) > 0)
            <div class="d-grid gap-3 mb-3">
                @foreach ($this->sessions as $session)
                    <div class="d-flex align-items-center gap-3 border rounded p-2">
                        <div class="text-body-secondary">
                            @if ($session->agent->isDesktop())
                                {{-- desktop icon --}}
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28"><path fill="currentColor" d="M4 5h16a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-6v2h3v2H7v-2h3v-2H4a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2z"/></svg>
                            @else
                                {{-- phone icon --}}
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28"><path fill="currentColor" d="M7 2h10a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/></svg>
                            @endif
                        </div>

                        <div class="flex-grow-1">
                            <div class="small">
                                {{ $session->agent->platform() ?: __('Unknown') }} — {{ $session->agent->browser() ?: __('Unknown') }}
                            </div>
                            <div class="text-body-secondary small">
                                {{ $session->ip_address }},
                                @if ($session->is_current_device)
                                    <span class="text-success fw-semibold">{{ __('This device') }}</span>
                                @else
                                    {{ __('Last active') }} {{ $session->last_active }}
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-primary" wire:click="confirmLogout" wire:loading.attr="disabled">
                {{ __('Log Out Other Browser Sessions') }}
            </button>

            <x-action-message class="text-success small" on="loggedOut">
                {{ __('Done.') }}
            </x-action-message>
        </div>
    </div>

    {{-- Confirm modal --}}
    @if($confirmingLogout)
        <div class="modal fade show" tabindex="-1" style="display:block;" aria-modal="true" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-6">{{ __('Log Out Other Browser Sessions') }}</h1>
                        <button type="button" class="btn-close" aria-label="Close"
                                wire:click="$toggle('confirmingLogout')"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">
                            {{ __('Please enter your password to confirm you would like to log out of your other browser sessions across all devices.') }}
                        </p>

                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                               placeholder="{{ __('Password') }}"
                               x-ref="password"
                               wire:model="password"
                               wire:keydown.enter="logoutOtherBrowserSessions">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                                wire:click="$toggle('confirmingLogout')" wire:loading.attr="disabled">
                            {{ __('Cancel') }}
                        </button>

                        <button type="button" class="btn btn-primary"
                                wire:click="logoutOtherBrowserSessions" wire:loading.attr="disabled">
                            {{ __('Log Out Other Browser Sessions') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
