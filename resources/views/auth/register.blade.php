<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-3" />

        <form method="POST" action="{{ route('register') }}" class="row g-3">
            @csrf

            <div class="col-12">
                <x-label for="name" value="{{ __('Name') }}" />
                <x-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="col-12">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" />
            </div>

            <div class="col-12">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="col-12">
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="col-12">
                    <div class="form-check">
                        <x-checkbox name="terms" id="terms" required />
                        <label for="terms" class="form-check-label">
                            {!! __('I agree to the :terms_of_service and :privacy_policy', [
                              'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="link-secondary">'.__('Terms of Service').'</a>',
                              'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="link-secondary">'.__('Privacy Policy').'</a>',
                            ]) !!}
                        </label>
                    </div>
                </div>
            @endif

            <div class="col-12 d-flex justify-content-between align-items-center">
                <a class="link-secondary" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-button variant="brand">
                    <wa-icon slot="start" name="user-plus"></wa-icon>
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
