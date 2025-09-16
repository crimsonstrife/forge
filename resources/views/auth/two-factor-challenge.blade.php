<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div x-data="{ recovery: false }">
            <p class="text-body-secondary mb-3 small" x-show="!recovery">
                {{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
            </p>

            <p class="text-body-secondary mb-3 small" x-cloak x-show="recovery">
                {{ __('Please confirm access to your account by entering one of your emergency recovery codes.') }}
            </p>

            <x-validation-errors class="mb-3" />

            <form method="POST" action="{{ route('two-factor.login') }}" class="row g-3">
                @csrf

                <div class="col-12" x-show="!recovery">
                    <x-label for="code" value="{{ __('Code') }}" />
                    <x-input
                        id="code"
                        type="text"
                        name="code"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        x-ref="code"
                        autofocus
                    />
                </div>

                <div class="col-12" x-cloak x-show="recovery">
                    <x-label for="recovery_code" value="{{ __('Recovery Code') }}" />
                    <x-input
                        id="recovery_code"
                        type="text"
                        name="recovery_code"
                        autocomplete="one-time-code"
                        x-ref="recovery_code"
                    />
                </div>

                <div class="col-12 d-flex justify-content-between align-items-center">
                    <button
                        type="button"
                        class="btn btn-link btn-sm text-decoration-underline link-secondary px-0"
                        x-show="!recovery"
                        x-on:click="
              recovery = true;
              $nextTick(() => { $refs.recovery_code.focus() })
            "
                    >
                        {{ __('Use a recovery code') }}
                    </button>

                    <button
                        type="button"
                        class="btn btn-link btn-sm text-decoration-underline link-secondary px-0"
                        x-cloak
                        x-show="recovery"
                        x-on:click="
              recovery = false;
              $nextTick(() => { $refs.code.focus() })
            "
                    >
                        {{ __('Use an authentication code') }}
                    </button>

                    <x-button class="ms-auto">
                        <wa-icon slot="start" name="shield-check"></wa-icon>
                        {{ __('Log in') }}
                    </x-button>
                </div>
            </form>
        </div>
    </x-authentication-card>
</x-guest-layout>
