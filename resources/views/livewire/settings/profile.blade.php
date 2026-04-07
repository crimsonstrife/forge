<section>
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="vstack gap-4">
            <div class="card shadow-sm">
                <div class="card-body vstack gap-3">
                    <x-input
                        wire:model="name"
                        :label="__('Name')"
                        type="text"
                        required
                        autofocus
                        autocomplete="name"
                    />

                    <div>
                        <x-input
                            wire:model="email"
                            :label="__('Email')"
                            type="email"
                            required
                            autocomplete="email"
                        />

                        @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                            <div class="alert alert-warning mt-3 mb-0" role="alert">
                                <div class="fw-semibold">{{ __('Your email address is unverified.') }}</div>
                                <button type="button" class="btn btn-link p-0 align-baseline" wire:click.prevent="resendVerificationNotification">
                                    {{ __('Click here to re-send the verification email.') }}
                                </button>

                                @if (session('status') === 'verification-link-sent')
                                    <div class="text-success small mt-2">
                                        {{ __('A new verification link has been sent to your email address.') }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="h6 mb-1">{{ __('Issue collaboration notifications') }}</h3>
                    <p class="text-body-secondary small mb-4">
                        {{ __('Choose which issue events should notify you immediately and whether Forge should send a daily digest.') }}
                    </p>

                    <div class="vstack gap-3">
                        <x-checkbox wire:model="notify_on_assignment" :label="__('Notify me when I am assigned to an issue')" />
                        <x-checkbox wire:model="notify_on_comment" :label="__('Notify me about new comments on followed issues')" />
                        <x-checkbox wire:model="notify_on_status_change" :label="__('Notify me when followed issues change status')" />
                        <x-checkbox wire:model="notify_on_link_change" :label="__('Notify me when links or code references change on followed issues')" />
                        <x-checkbox wire:model="notify_on_mention" :label="__('Notify me when someone mentions me in an issue comment')" />
                        <x-checkbox wire:model="daily_digest_enabled" :label="__('Send a daily digest with issue activity I have not seen yet')" />
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

                <x-action-message on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>
