<div class="vstack gap-4">
    <x-auth-session-status class="text-center" :status="session('status')" />

    <x-validation-errors class="mb-3" />

    <form wire:submit.prevent="login" class="vstack gap-3">
        <x-input
            wire:model="email"
            :label="__('Email address')"
            type="email"
            required
            autofocus
            autocomplete="email"
            placeholder="email@example.com"
        />

        <div>
            <x-input
                wire:model="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="__('Password')"
                viewable
            />
        </div>

        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
            <x-checkbox wire:model="remember" :label="__('Remember me')" />

            @if (Route::has('password.request'))
                <a class="small text-body-secondary text-decoration-none" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">{{ __('Log in') }}</button>
        </div>
    </form>

    @if (Route::has('register'))
        <div class="text-center small text-body-secondary">
            <span>{{ __('Don\'t have an account?') }}</span>
            <a href="{{ route('register') }}" class="text-decoration-none">{{ __('Sign up') }}</a>
        </div>
    @endif
</div>
