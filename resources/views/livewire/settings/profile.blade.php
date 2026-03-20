<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&! auth()->user()->hasVerifiedEmail())
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <div class="rounded-xl border border-zinc-200/70 p-4">
                <div class="font-medium">{{ __('Issue collaboration notifications') }}</div>
                <div class="mt-1 text-sm text-zinc-600">{{ __('Choose which issue events should notify you immediately and whether Forge should send a daily digest.') }}</div>

                <div class="mt-4 space-y-3 text-sm">
                    <label class="flex items-start gap-3">
                        <input type="checkbox" wire:model="notify_on_assignment" class="mt-1 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-500">
                        <span>{{ __('Notify me when I am assigned to an issue') }}</span>
                    </label>
                    <label class="flex items-start gap-3">
                        <input type="checkbox" wire:model="notify_on_comment" class="mt-1 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-500">
                        <span>{{ __('Notify me about new comments on followed issues') }}</span>
                    </label>
                    <label class="flex items-start gap-3">
                        <input type="checkbox" wire:model="notify_on_status_change" class="mt-1 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-500">
                        <span>{{ __('Notify me when followed issues change status') }}</span>
                    </label>
                    <label class="flex items-start gap-3">
                        <input type="checkbox" wire:model="notify_on_link_change" class="mt-1 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-500">
                        <span>{{ __('Notify me when links or code references change on followed issues') }}</span>
                    </label>
                    <label class="flex items-start gap-3">
                        <input type="checkbox" wire:model="notify_on_mention" class="mt-1 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-500">
                        <span>{{ __('Notify me when someone mentions me in an issue comment') }}</span>
                    </label>
                    <label class="flex items-start gap-3">
                        <input type="checkbox" wire:model="daily_digest_enabled" class="mt-1 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-500">
                        <span>{{ __('Send a daily digest with issue activity I have not seen yet') }}</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>
