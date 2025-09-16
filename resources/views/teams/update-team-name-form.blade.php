<div class="card shadow-sm">
    <div class="card-body">
        <div class="mb-2">
            <div class="fw-semibold">{{ __('Team Name') }}</div>
            <div class="text-body-secondary small">
                {{ __('The team\'s name and owner information.') }}
            </div>
        </div>

        <form wire:submit.prevent="updateTeamName" class="row g-3">
            <div class="col-12">
                <label class="form-label">{{ __('Team Owner') }}</label>
                <div class="d-flex align-items-center gap-3 mt-1">
                    <x-avatar :src="$team->owner->profile_photo_url" :name="$team->owner->name" preset="md" />
                    <div class="lh-sm">
                        <div class="fw-medium">{{ $team->owner->name }}</div>
                        <div class="text-body-secondary small">{{ $team->owner->email }}</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-8">
                <label for="team_name" class="form-label">{{ __('Team Name') }}</label>
                <input id="team_name" type="text"
                       class="form-control @error('name') is-invalid @enderror"
                       wire:model="state.name"
                    @disabled(! Gate::check('update', $team))>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            @if (Gate::check('update', $team))
                <div class="col-12 d-flex align-items-center gap-2">
                    <div x-data="{ shown: false, timeout: null }"
                         x-init="window.Livewire.find('{{ $_instance->getId() }}').on('saved', () => { clearTimeout(timeout); shown = true; timeout = setTimeout(() => { shown = false }, 2000) })"
                         x-show.transition.out.opacity.duration.1500ms="shown"
                         style="display:none"
                         class="small text-success">
                        {{ __('Saved.') }}
                    </div>

                    <button type="submit" class="btn btn-primary">
                        {{ __('Save') }}
                    </button>
                </div>
            @endif
        </form>
    </div>
</div>
