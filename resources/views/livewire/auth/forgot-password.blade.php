<div class="vstack gap-4">
    <x-auth-header :title="__('Forgot password')" :description="__('Enter your email to receive a password reset link')" />

    <x-auth-session-status class="text-center" :status="session('status')" />
    <x-validation-errors class="mb-3" />

    <form method="POST" wire:submit="sendPasswordResetLink" class="vstack gap-3">
        <x-input
            wire:model="email"
            :label="__('Email Address')"
            type="email"
            required
            autofocus
            placeholder="email@example.com"
        />

        <button type="submit" class="btn btn-primary">{{ __('Email password reset link') }}</button>
    </form>

    <div class="text-center small text-body-secondary">
        <span>{{ __('Or, return to') }}</span>
        <a href="{{ route('login') }}" class="text-decoration-none">{{ __('log in') }}</a>
    </div>
</div>
