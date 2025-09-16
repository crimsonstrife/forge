<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <p class="text-body-secondary mb-3 small">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </p>

        @session('status')
        <div class="alert alert-success mb-3" role="alert">
            {{ $value }}
        </div>
        @endsession

        <x-validation-errors class="mb-3" />

        <form method="POST" action="{{ route('password.email') }}" class="row g-3">
            @csrf

            <div class="col-12">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input
                    id="email"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autofocus
                    autocomplete="username"
                />
            </div>

            <div class="col-12 d-flex justify-content-end">
                <x-button>
                    <wa-icon slot="start" name="envelope"></wa-icon>
                    {{ __('Email Password Reset Link') }}
                </x-button>
            </div>
            <div class="col-12 text-end">
                <a class="link-secondary small" href="{{ route('login') }}">{{ __('Back to login') }}</a>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
