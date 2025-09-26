<div class="card shadow-sm">
    <div class="card-body">
        <div class="mb-2">
            <div class="fw-semibold">{{ __('Two Factor Authentication') }}</div>
            <div class="text-body-secondary small">
                {{ __('Add additional security to your account using two factor authentication.') }}
            </div>
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
                        @error('code')
                        <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                @endif
            @endif

            @if ($showingRecoveryCodes)
                <p class="text-body-secondary small fw-semibold mt-3 mb-2">
                    {{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor device is lost.') }}
                </p>

                <div class="bg-light rounded p-3 font-monospace small" style="max-width: 36rem">
                    @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true, 512, JSON_THROW_ON_ERROR) as $code)
                        <div>{{ $code }}</div>
                    @endforeach
                </div>
            @endif
        @endif

        <div class="mt-3 d-flex flex-wrap gap-2">
            @if (! $this->enabled)
                {{-- Enable --}}
                <x-confirms-password wire:then="enableTwoFactorAuthentication">
                    <button type="button" class="btn btn-primary" wire:loading.attr="disabled">
                        {{ __('Enable') }}
                    </button>
                </x-confirms-password>
            @else
                @if ($showingRecoveryCodes)
                    {{-- Regenerate Recovery Codes --}}
                    <x-confirms-password wire:then="regenerateRecoveryCodes">
                        <button type="button" class="btn btn-outline-secondary">
                            {{ __('Regenerate Recovery Codes') }}
                        </button>
                    </x-confirms-password>
                @elseif ($showingConfirmation)
                    {{-- Confirm (finalize enabling 2FA) --}}
                    <x-confirms-password wire:then="confirmTwoFactorAuthentication">
                        <button type="button" class="btn btn-primary" wire:loading.attr="disabled">
                            {{ __('Confirm') }}
                        </button>
                    </x-confirms-password>
                @else
                    {{-- Show Recovery Codes --}}
                    <x-confirms-password wire:then="showRecoveryCodes">
                        <button type="button" class="btn btn-outline-secondary">
                            {{ __('Show Recovery Codes') }}
                        </button>
                    </x-confirms-password>
                @endif

                @if ($showingConfirmation)
                    {{-- Cancel (disable 2FA during setup) --}}
                    <x-confirms-password wire:then="disableTwoFactorAuthentication">
                        <button type="button" class="btn btn-outline-secondary" wire:loading.attr="disabled">
                            {{ __('Cancel') }}
                        </button>
                    </x-confirms-password>
                @else
                    {{-- Disable --}}
                    <x-confirms-password wire:then="disableTwoFactorAuthentication">
                        <button type="button" class="btn btn-danger" wire:loading.attr="disabled">
                            {{ __('Disable') }}
                        </button>
                    </x-confirms-password>
                @endif
            @endif
        </div>
    </div>
</div>
