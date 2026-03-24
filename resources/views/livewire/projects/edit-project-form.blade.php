<div class="card shadow-sm">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-sm-6">
                <x-label for="name" value="Name"/>
                <input id="name" type="text" class="form-control" wire:model.defer="name" autofocus>
                <x-input-error for="name" class="mt-1"/>
            </div>

            <div class="col-sm-6">
                <x-label for="key" value="Key"/>
                <input id="key" type="text" class="form-control text-uppercase" wire:model.defer="key">
                <x-input-error for="key" class="mt-1"/>
            </div>

            <div class="col-12">
                <x-label for="description" value="Description"/>
                <textarea id="description" rows="5" class="form-control" wire:model.defer="description"></textarea>
                <x-input-error for="description" class="mt-1"/>
            </div>

            <div class="col-sm-6">
                <x-label for="organization_id" value="Organization"/>
                <select id="organization_id" class="form-select" wire:model.defer="organization_id">
                    <option value="">— {{ __('None') }} —</option>
                    @foreach($organizationOptions as $organization)
                        <option value="{{ $organization['id'] }}">{{ $organization['name'] }}</option>
                    @endforeach
                </select>
                <div class="form-text">{{ __('Organizations and teams can both scope the same project.') }}</div>
                <x-input-error for="organization_id" class="mt-1"/>
            </div>

            <div class="col-sm-6">
                <x-label for="stage" value="Stage"/>
                <select id="stage" class="form-select" wire:model.defer="stage">
                    @foreach($stages as $s)
                        <option value="{{ $s->value }}">{{ ucfirst($s->value) }}</option>
                    @endforeach
                </select>
                <x-input-error for="stage" class="mt-1"/>
            </div>

            <div class="col-sm-6">
                <x-label for="lead_id" value="Project lead (optional)"/>
                <select id="lead_id" class="form-select" wire:model.defer="lead_id">
                    <option value="">— {{ __('None') }} —</option>
                    @foreach($leadOptions as $user)
                        <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                    @endforeach
                </select>
                <x-input-error for="lead_id" class="mt-1"/>
            </div>

            <div class="col-12">
                <x-label for="teams" value="Teams included in this project"/>
                <select id="teams" multiple class="form-select" wire:model.defer="attach_team_ids">
                    @foreach($teamOptions as $team)
                        <option value="{{ $team['id'] }}">{{ $team['name'] }}</option>
                    @endforeach
                </select>
                <div class="form-text">{{ __('Selected teams will see this project as shared team work.') }}</div>
                <x-input-error for="attach_team_ids" class="mt-1"/>
            </div>

            <div class="col-sm-4">
                <x-label for="started_at" value="Start date"/>
                <input id="started_at" type="date" class="form-control" wire:model.defer="started_at">
                <x-input-error for="started_at" class="mt-1"/>
            </div>
            <div class="col-sm-4">
                <x-label for="due_at" value="Deadline"/>
                <input id="due_at" type="date" class="form-control" wire:model.defer="due_at">
                <x-input-error for="due_at" class="mt-1"/>
            </div>
            <div class="col-sm-4">
                <x-label for="ended_at" value="Ended"/>
                <input id="ended_at" type="date" class="form-control" wire:model.defer="ended_at">
                <x-input-error for="ended_at" class="mt-1"/>
            </div>
        </div>
    </div>

    <div class="card-footer d-flex justify-content-end gap-2">
        <a href="{{ route('projects.show', ['project'=>$project]) }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
        <wa-button variant="brand" wire:click="save">{{ __('Save') }}</wa-button>
    </div>
</div>
