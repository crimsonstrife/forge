<div class="card shadow-sm">
    <div class="card-body">
        <div class="mb-2">
            <div class="fw-semibold text-danger">{{ __('Delete Account') }}</div>
            <div class="text-body-secondary small">
                {{ __('Permanently delete your account.') }}
            </div>
        </div>

        <p class="text-body-secondary small mb-3">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please download any data you wish to retain.') }}
        </p>

        <button type="button" class="btn btn-danger" wire:click="confirmUserDeletion" wire:loading.attr="disabled">
            {{ __('Delete Account') }}
        </button>
    </div>

    {{-- Confirmation modal --}}
    @if($confirmingUserDeletion)
        <div class="modal fade show" tabindex="-1" style="display:block;" aria-modal="true" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-6">{{ __('Delete Account') }}</h1>
                        <button type="button" class="btn-close" aria-label="Close"
                                wire:click="$toggle('confirmingUserDeletion')"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">
                            {{ __('Are you sure you want to delete your account? This action cannot be undone. Please enter your password to confirm.') }}
                        </p>

                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="{{ __('Password') }}"
                               x-ref="password"
                               autocomplete="current-password"
                               wire:model="password"
                               wire:keydown.enter="deleteUser">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                                wire:click="$toggle('confirmingUserDeletion')" wire:loading.attr="disabled">
                            {{ __('Cancel') }}
                        </button>

                        <button type="button" class="btn btn-danger"
                                wire:click="deleteUser" wire:loading.attr="disabled">
                            {{ __('Delete Account') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
