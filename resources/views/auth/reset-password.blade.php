<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-3" />

        <form method="POST" action="{{ route('password.update') }}" class="row g-3">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="col-12">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input
                    id="email"
                    type="email"
                    name="email"
                    :value="old('email', $request->email)"
                    required
                    autofocus
                    autocomplete="username"
                />
            </div>

            <div class="col-12">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                />
            </div>

            <div class="col-12">
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                />
            </div>

            <div class="col-12 d-flex justify-content-end">
                <x-button>
                    <wa-icon slot="start" name="key"></wa-icon>
                    {{ __('Reset Password') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
