<div class="card shadow-sm">
    <div class="card-body">
        <div class="mb-2">
            <div class="fw-semibold">{{ __('Update Password') }}</div>
            <div class="text-body-secondary small">{{ __('Ensure your account is using a long, random password to stay secure.') }}</div>
        </div>

        <form wire:submit.prevent="updatePassword" class="row g-3">
            <div class="col-12 col-sm-6">
                <label for="current_password" class="form-label">{{ __('Current Password') }}</label>
                <input id="current_password" type="password" class="form-control @error('state.current_password') is-invalid @enderror"
                       wire:model="state.current_password" autocomplete="current-password">
                @error('state.current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-6">
                <label for="password" class="form-label">{{ __('New Password') }}</label>
                <input id="password" type="password" class="form-control @error('state.password') is-invalid @enderror"
                       wire:model="state.password" autocomplete="new-password">
                @error('state.password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-6">
                <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                <input id="password_confirmation" type="password" class="form-control @error('state.password_confirmation') is-invalid @enderror"
                       wire:model="state.password_confirmation" autocomplete="new-password">
                @error('state.password_confirmation') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 d-flex align-items-center gap-2">
                <x-action-message class="text-success small" on="saved">
                    {{ __('Saved.') }}
                </x-action-message>

                <button type="submit" class="btn btn-primary">
                    {{ __('Save') }}
                </button>
            </div>
        </form>
    </div>
</div>
