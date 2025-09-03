<div class="card shadow-sm">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-sm-6">
                <x-label for="name" value="Name"/>
                <input wire:model.defer="name" id="name" type="text" class="form-control" autofocus>
                <x-input-error for="name" class="mt-1"/>
            </div>

            <div class="col-sm-6">
                <x-label for="key" value="Key (2–10 A–Z/0–9)"/>
                <input wire:model.defer="key" id="key" type="text" class="form-control text-uppercase">
                <x-input-error for="key" class="mt-1"/>
            </div>

            <div class="col-12">
                <x-label for="description" value="Description"/>
                <textarea wire:model.defer="description" id="description" rows="4" class="form-control"></textarea>
                <x-input-error for="description" class="mt-1"/>
            </div>

            <div class="col-sm-6">
                <x-label for="lead_id" value="Project Lead"/>
                <select wire:model.defer="lead_id" id="lead_id" class="form-select">
                    <option value="">(Select)</option>
                    @foreach($teamMembers as $m)
                        <option value="{{ $m['id'] }}">{{ $m['name'] }}</option>
                    @endforeach
                </select>
                <x-input-error for="lead_id" class="mt-1"/>
            </div>

            <div class="col-sm-6">
                <x-label for="stage" value="Stage"/>
                <select wire:model.defer="stage" id="stage" class="form-select">
                    @foreach($stages as $s)
                        <option value="{{ $s->value }}">{{ ucfirst($s->value) }}</option>
                    @endforeach
                </select>
                <x-input-error for="stage" class="mt-1"/>
            </div>

            <div class="col-12">
                <x-label for="teams" value="Teams included in this project"/>
                <select id="teams" multiple wire:model.defer="attach_team_ids" class="form-select">
                    @foreach($teamOptions as $t)
                        <option value="{{ $t['id'] }}">{{ $t['name'] }}</option>
                    @endforeach
                </select>
                <x-input-error for="attach_team_ids" class="mt-1"/>
            </div>

            <div class="col-12">
                <x-label for="copy" value="Copy configuration from…"/>
                <select wire:model.defer="copy_from_project_id" id="copy" class="form-select">
                    <option value="">(Use defaults)</option>
                    @foreach($projectsForCopy as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
                <div class="form-text">{{ __('Types, statuses, priorities, and transitions will be copied.') }}</div>
                <x-input-error for="copy" class="mt-1"/>
            </div>
        </div>
    </div>

    <div class="card-footer d-flex justify-content-end gap-2">
        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
        <wa-button variant="brand" wire:click="save">{{ __('Create Project') }}</wa-button>
    </div>
</div>
