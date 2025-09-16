<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo/>
        </x-slot>

        <p class="text-body-secondary mb-3 small">
            {{ __('Before continuing, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
        </p>

        @if (session('status') === 'verification-link-sent')
            <div class="alert alert-success mb-3" role="alert">
                {{ __('A new verification link has been sent to the email address you provided in your profile settings.') }}
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mt-2">
            <form method="POST" action="{{ route('verification.send') }}" class="m-0">
                @csrf
                <x-button type="submit">
                    <wa-icon slot="start" name="envelope"></wa-icon>
                    {{ __('Resend Verification Email') }}
                </x-button>
            </form>

            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('profile.show') }}" class="link-secondary">
                    {{ __('Edit Profile') }}
                </a>

                <form method="POST" action="{{ route('logout') }}" class="m-0 d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link link-secondary text-decoration-underline p-0">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </x-authentication-card>
</x-guest-layout>
