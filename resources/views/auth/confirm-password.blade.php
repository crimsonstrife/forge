<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <p class="text-body-secondary mb-3 small">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </p>

        <x-validation-errors class="mb-3" />

        <form method="POST" action="{{ route('password.confirm') }}" class="row g-3">
            @csrf

            <div class="col-12">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autofocus
                    autocomplete="current-password"
                />
            </div>

            <div class="col-12 d-flex justify-content-end">
                <x-button>
                    <wa-icon slot="start" name="lock"></wa-icon>
                    {{ __('Confirm') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
