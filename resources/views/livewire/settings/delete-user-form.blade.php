<section class="card border-danger-subtle shadow-sm" x-data="{ confirmingDeletion: @js($errors->isNotEmpty()) }">
    <div class="card-body">
        <h3 class="h5 mb-1">{{ __('Delete account') }}</h3>
        <p class="text-body-secondary mb-3">{{ __('Delete your account and all of its resources') }}</p>

        <button type="button" class="btn btn-outline-danger" x-on:click="confirmingDeletion = true">
            {{ __('Delete account') }}
        </button>
    </div>

    <div x-show="confirmingDeletion"
         class="modal fade"
         :class="{ 'show d-block': confirmingDeletion }"
         style="display: none;"
         tabindex="-1"
         role="dialog"
         aria-modal="true">
        <div class="modal-dialog modal-dialog-centered" x-trap.inert.noscroll="confirmingDeletion">
            <form method="POST" wire:submit="deleteUser" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Are you sure you want to delete your account?') }}</h5>
                    <button type="button" class="btn-close" x-on:click="confirmingDeletion = false" aria-label="{{ __('Close') }}"></button>
                </div>

                <div class="modal-body vstack gap-3">
                    <p class="text-body-secondary mb-0">
                        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                    </p>

                    <x-input wire:model="password" :label="__('Password')" type="password" />
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" x-on:click="confirmingDeletion = false">
                        {{ __('Cancel') }}
                    </button>

                    <button type="submit" class="btn btn-danger">{{ __('Delete account') }}</button>
                </div>
            </form>
        </div>

        <div x-show="confirmingDeletion" class="modal-backdrop fade show" x-on:click="confirmingDeletion = false"></div>
    </div>
</section>
