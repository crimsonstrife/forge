<div class="vstack gap-4">
    <x-auth-header
        :title="__('Confirm password')"
        :description="__('This is a secure area of the application. Please confirm your password before continuing.')"
    />

    <x-auth-session-status class="text-center" :status="session('status')" />
    <x-validation-errors class="mb-3" />

    <form method="POST" wire:submit="confirmPassword" class="vstack gap-3">
        <x-input
            wire:model="password"
            :label="__('Password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Password')"
            viewable
        />

        <button type="submit" class="btn btn-primary">{{ __('Confirm') }}</button>
    </form>
</div>
