<div class="card shadow-sm">
    <div class="card-body">
        <div class="mb-2">
            <div class="fw-semibold">{{ __('Two Factor Authentication') }}</div>
            <div class="text-body-secondary small">{{ __('Add additional security to your account using two factor authentication.') }}</div>
        </div>

        <h3 class="h6">
            @if ($this->enabled)
                @if ($showingConfirmation)
                    {{ __('Finish enabling two factor authentication.') }}
                @else
                    {{ __('You have enabled two factor authentication.') }}
                @endif
            @else
                {{ __('You have not enabled two factor authentication.') }}
            @endif
        </h3>

        <p class="text-body-secondary small mb-3">
            {{ __('When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s authenticator application.') }}
        </p>

        @if ($this->enabled)
            @if ($showingQrCode)
                <p class="text-body-secondary small fw-semibold mb-2">
                    @if ($showingConfirmation)
                        {{ __('To finish enabling two factor authentication, scan the following QR code or enter the setup key and provide the generated OTP code.') }}
                    @else
                        {{ __('Two factor authentication is now enabled. Scan the following QR code or enter the setup key.') }}
                    @endif
                </p>

                <div class="p-2 bg-white d-inline-block rounded border">
                    {!! $this->user->twoFactorQrCodeSvg() !!}
                </div>

                <p class="text-body-secondary small mt-3 mb-0">
                    <span class="fw-semibold">{{ __('Setup Key') }}:</span>
                    {{ decrypt($this->user->two_factor_secret) }}
                </p>

                @if ($showingConfirmation)
                    <div class="mt-3" style="max-width: 320px">
                        <label for="code" class="form-label">{{ __('Code') }}</label>
                        <input id="code" type="text" inputmode="numeric" autocomplete="one-time-code"
                               class="form-control @error('code') is-invalid @enderror"
                               wire:model="code" wire:keydown.enter="confirmTwoFactorAuthentication">
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                @endif
            @endif

            @if ($showingRecoveryCodes)
                <p class="text-body-secondary small fw-semibold mt-3 mb-2">
                    {{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor device is lost.') }}
                </p>

                <div class="bg-light rounded p-3 font-monospace small" style="max-width: 36rem">
                    @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                        <div>{{ $code }}</div>
                    @endforeach
                </div>
            @endif
        @endif

        @php
            // Static IDs are fine; they just need to match across click + confirm event.
            $idEnable      = 'tfa-enable';
            $idRegenerate  = 'tfa-regenerate';
            $idConfirm     = 'tfa-confirm';
            $idShowCodes   = 'tfa-show-codes';
            $idDisable     = 'tfa-disable';
        @endphp

        <div class="mt-3 d-flex flex-wrap gap-2">
            @if (! $this->enabled)
                {{-- Enable --}}
                <span wire:then="enableTwoFactorAuthentication"
                      x-data
                      x-ref="enableBtn"
                      x-on:click="$wire.startConfirmingPassword('{{ $idEnable }}')"
                      x-on:password-confirmed.window="
                          setTimeout(() => {
                            if ($event.detail.id === '{{ $idEnable }}') $refs.enableBtn.dispatchEvent(new CustomEvent('then', { bubbles: false }))
                          }, 250);
                      ">
                    <button type="button" class="btn btn-primary" wire:loading.attr="disabled">
                        {{ __('Enable') }}
                    </button>
                </span>
            @else
                @if ($showingRecoveryCodes)
                    {{-- Regenerate Recovery Codes --}}
                    <span wire:then="regenerateRecoveryCodes"
                          x-data
                          x-ref="regenBtn"
                          x-on:click="$wire.startConfirmingPassword('{{ $idRegenerate }}')"
                          x-on:password-confirmed.window="
                              setTimeout(() => {
                                if ($event.detail.id === '{{ $idRegenerate }}') $refs.regenBtn.dispatchEvent(new CustomEvent('then', { bubbles: false }))
                              }, 250);
                          ">
                        <button type="button" class="btn btn-outline-secondary">
                            {{ __('Regenerate Recovery Codes') }}
                        </button>
                    </span>
                @elseif ($showingConfirmation)
                    {{-- Confirm (finalize enabling 2FA) --}}
                    <span wire:then="confirmTwoFactorAuthentication"
                          x-data
                          x-ref="confirmBtn"
                          x-on:click="$wire.startConfirmingPassword('{{ $idConfirm }}')"
                          x-on:password-confirmed.window="
                              setTimeout(() => {
                                if ($event.detail.id === '{{ $idConfirm }}') $refs.confirmBtn.dispatchEvent(new CustomEvent('then', { bubbles: false }))
                              }, 250);
                          ">
                        <button type="button" class="btn btn-primary" wire:loading.attr="disabled">
                            {{ __('Confirm') }}
                        </button>
                    </span>
                @else
                    {{-- Show Recovery Codes --}}
                    <span wire:then="showRecoveryCodes"
                          x-data
                          x-ref="showBtn"
                          x-on:click="$wire.startConfirmingPassword('{{ $idShowCodes }}')"
                          x-on:password-confirmed.window="
                              setTimeout(() => {
                                if ($event.detail.id === '{{ $idShowCodes }}') $refs.showBtn.dispatchEvent(new CustomEvent('then', { bubbles: false }))
                              }, 250);
                          ">
                        <button type="button" class="btn btn-outline-secondary">
                            {{ __('Show Recovery Codes') }}
                        </button>
                    </span>
                @endif

                @if ($showingConfirmation)
                    {{-- Cancel (disable 2FA during setup) --}}
                    <span wire:then="disableTwoFactorAuthentication"
                          x-data
                          x-ref="cancelBtn"
                          x-on:click="$wire.startConfirmingPassword('{{ $idDisable }}')"
                          x-on:password-confirmed.window="
                              setTimeout(() => {
                                if ($event.detail.id === '{{ $idDisable }}') $refs.cancelBtn.dispatchEvent(new CustomEvent('then', { bubbles: false }))
                              }, 250);
                          ">
                        <button type="button" class="btn btn-outline-secondary" wire:loading.attr="disabled">
                            {{ __('Cancel') }}
                        </button>
                    </span>
                @else
                    {{-- Disable --}}
                    <span wire:then="disableTwoFactorAuthentication"
                          x-data
                          x-ref="disableBtn"
                          x-on:click="$wire.startConfirmingPassword('{{ $idDisable }}')"
                          x-on:password-confirmed.window="
                              setTimeout(() => {
                                if ($event.detail.id === '{{ $idDisable }}') $refs.disableBtn.dispatchEvent(new CustomEvent('then', { bubbles: false }))
                              }, 250);
                          ">
                        <button type="button" class="btn btn-danger" wire:loading.attr="disabled">
                            {{ __('Disable') }}
                        </button>
                    </span>
                @endif
            @endif
        </div>
    </div>
</div>

{{-- Confirm Password Modal (rendered only when needed; no d-block) --}}
@if ($confirmingPassword)
    <div class="modal fade show" tabindex="-1" role="dialog" aria-modal="true" style="display:block;">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Confirm Password') }}</h5>
                    <button type="button" class="btn-close" aria-label="{{ __('Close') }}"
                            wire:click="stopConfirmingPassword"></button>
                </div>

                <div class="modal-body">
                    {{ __('For your security, please confirm your password to continue.') }}

                    <div class="mt-3" x-data
                         x-on:confirming-password.window="setTimeout(() => $refs.confirmable_password.focus(), 250)">
                        <wa-input type="password"
                                  placeholder="{{ __('Password') }}"
                                  autocomplete="current-password"
                                  x-ref="confirmable_password"
                                  wire:ignore
                                  x-on:input="$wire.set('confirmablePassword', $event.target.value)"
                                  x-on:keydown.enter="$wire.confirmPassword()">
                        </wa-input>
                        @error('confirmablePassword')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <wa-button type="button" variant="neutral" appearance="outlined"
                               wire:click="stopConfirmingPassword" wire:loading.attr="disabled">
                        {{ __('Cancel') }}
                    </wa-button>
                    <wa-button type="submit" variant="brand" appearance="accent" class="ms-2"
                               dusk="confirm-password-button"
                               wire:click="confirmPassword" wire:loading.attr="disabled">
                        {{ __('Confirm') }}
                    </wa-button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
@endif
