<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-3" />

        @session('status')
        <div class="alert alert-success mb-3" role="alert">
            {{ $value }}
        </div>
        @endsession

        <form method="POST" action="{{ route('login') }}" class="row g-3">
            @csrf

            <div class="col-12">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="col-12">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="col-12">
                <div class="form-check">
                    <x-checkbox id="remember_me" name="remember" />
                    <label for="remember_me" class="form-check-label">
                        {{ __('Remember me') }}
                    </label>
                </div>
            </div>

            <div class="col-12 d-flex justify-content-between align-items-center">
                @if (Route::has('password.request'))
                    <a class="link-secondary" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-button variant="brand">
                    <wa-icon slot="start" name="door-open"></wa-icon>
                    {{ __('Log in') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
