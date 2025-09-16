<div class="card shadow-sm">
    <div class="card-body">
        <div class="mb-2">
            <div class="fw-semibold">{{ __('Team Details') }}</div>
            <div class="text-body-secondary small">
                {{ __('Create a new team to collaborate with others on projects.') }}
            </div>
        </div>

        <form wire:submit.prevent="createTeam" class="row g-3">
            <div class="col-12">
                <label class="form-label">{{ __('Team Owner') }}</label>
                <div class="d-flex align-items-center gap-3 mt-1">
                    <x-avatar :src="$this->user->profile_photo_url" :name="$this->user->name" preset="md" />
                    <div class="lh-sm">
                        <div class="fw-medium">{{ $this->user->name }}</div>
                        <div class="text-body-secondary small">{{ $this->user->email }}</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-8">
                <label for="name" class="form-label">{{ __('Team Name') }}</label>
                <input id="name" type="text"
                       class="form-control @error('name') is-invalid @enderror"
                       wire:model="state.name" autofocus>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    {{ __('Create') }}
                </button>
            </div>
        </form>
    </div>
</div>
