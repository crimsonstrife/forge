<div class="vstack gap-4">
    <x-auth-header :title="__('Reset password')" :description="__('Please enter your new password below')" />

    <x-auth-session-status class="text-center" :status="session('status')" />
    <x-validation-errors class="mb-3" />

    <form method="POST" wire:submit="resetPassword" class="vstack gap-3">
        <x-input
            wire:model="email"
            :label="__('Email')"
            type="email"
            required
            autocomplete="email"
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
            <button type="submit" class="btn btn-primary">{{ __('Reset password') }}</button>
        </div>
    </form>
</div>
