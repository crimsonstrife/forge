<section>
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Update password')" :subheading="__('Ensure your account is using a long, random password to stay secure')">
        <form method="POST" wire:submit="updatePassword" class="card shadow-sm">
            <div class="card-body vstack gap-3">
                <x-input
                    wire:model="current_password"
                    :label="__('Current password')"
                    type="password"
                    required
                    autocomplete="current-password"
                />
                <x-input
                    wire:model="password"
                    :label="__('New password')"
                    type="password"
                    required
                    autocomplete="new-password"
                />
                <x-input
                    wire:model="password_confirmation"
                    :label="__('Confirm Password')"
                    type="password"
                    required
                    autocomplete="new-password"
                />
            </div>

            <div class="card-footer d-flex align-items-center gap-3">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

                <x-action-message on="password-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>
    </x-settings.layout>
</section>
