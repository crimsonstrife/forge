<div class="vstack gap-4">
    <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

    <x-auth-session-status class="text-center" :status="session('status')" />
    <x-validation-errors class="mb-3" />

    <form method="POST" wire:submit="register" class="vstack gap-3">
        <x-input
            wire:model="name"
            :label="__('Name')"
            type="text"
            required
            autofocus
            autocomplete="name"
            :placeholder="__('Full name')"
        />

        <x-input
            wire:model="email"
            :label="__('Email address')"
            type="email"
            required
            autocomplete="email"
            placeholder="email@example.com"
        />

        <x-input
            wire:model="password"
            :label="__('Password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Password')"
            viewable
        />

        <x-input
            wire:model="password_confirmation"
            :label="__('Confirm password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Confirm password')"
            viewable
        />

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">{{ __('Create account') }}</button>
        </div>
    </form>

    <div class="text-center small text-body-secondary">
        <span>{{ __('Already have an account?') }}</span>
        <a href="{{ route('login') }}" class="text-decoration-none">{{ __('Log in') }}</a>
    </div>
</div>
