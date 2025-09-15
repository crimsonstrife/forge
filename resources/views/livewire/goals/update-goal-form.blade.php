<form wire:submit.prevent="save" class="card">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label">Name</label>
                <input type="text" wire:model.defer="name" class="form-control">
                @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Type</label>
                <select wire:model="goal_type" class="form-select">
                    @foreach($typeOptions as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('goal_type') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-12">
                <label class="form-label">Description</label>
                <textarea wire:model.defer="description" rows="3" class="form-control"></textarea>
            </div>

            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select wire:model="status" class="form-select">
                    @foreach($statusOptions as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('status') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Confidence</label>
                <input type="number" min="0" max="100" class="form-control" wire:model="confidence">
                @error('confidence') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Health</label>
                <select class="form-select" wire:model="health">
                    <option value="on_track">On track</option>
                    <option value="at_risk">At risk</option>
                    <option value="off_track">Off track</option>
                </select>
                @error('health') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Start Date</label>
                <input type="date" wire:model="start_date" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">Due Date</label>
                <input type="date" wire:model="due_date" class="form-control">
                @error('due_date') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Check-in cadence</label>
                <select class="form-select" wire:model="cadence">
                    <option value="weekly">Weekly</option>
                    <option value="biweekly">Bi-weekly</option>
                    <option value="monthly">Monthly</option>
                </select>
                @error('cadence') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Owner Type</label>
                <select
                    wire:key="owner-type-{{ $owner_type }}"
                    wire:model.live="owner_type"
                    class="form-select">
                    @foreach($ownerTypes as $class => $label)
                        <option value="{{ $class }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('owner_type') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-8">
                <label class="form-label">Owner</label>
                <select wire:model="owner_id" class="form-select">
                    <option value="">Select…</option>
                    @foreach($ownerOptions as $opt)
                        <option value="{{ $opt['id'] }}">{{ $opt['name'] }}</option>
                    @endforeach
                </select>
                @error('owner_id') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
        </div>

        <hr class="my-4">

        <div class="d-flex align-items-center mb-2">
            <h5 class="mb-0">Key Results</h5>
            <button type="button" class="btn btn-sm btn-outline-primary ms-auto" wire:click="addKeyResult">
                + Add KR
            </button>
        </div>

        @forelse($keyResults as $i => $kr)
            @php $deleted = $kr['_delete'] ?? false; @endphp
            <div class="border rounded p-3 mb-3 {{ $deleted ? 'opacity-50' : '' }}">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" wire:model.defer="keyResults.{{ $i }}.name" @disabled($deleted)>
                                @error("keyResults.$i.name") <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Unit</label>
                                <select class="form-select" wire:model="keyResults.{{ $i }}.unit" @disabled($deleted)>
                                    <option value="number">Number</option>
                                    <option value="percent">Percent</option>
                                    <option value="currency">Currency</option>
                                    <option value="duration">Duration</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Direction</label>
                                <select class="form-select" wire:model="keyResults.{{ $i }}.direction" @disabled($deleted)>
                                    <option value="increase_to">Increase to</option>
                                    <option value="decrease_to">Decrease to</option>
                                    <option value="maintain_between">Maintain between</option>
                                    <option value="hit_exact">Hit exact</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Initial</label>
                                <input type="number" step="any" class="form-control" wire:model.defer="keyResults.{{ $i }}.initial_value" @disabled($deleted)>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Current</label>
                                <input type="number" step="any" class="form-control" wire:model.defer="keyResults.{{ $i }}.current_value" @disabled($deleted)>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Target</label>
                                <input type="number" step="any" class="form-control" wire:model.defer="keyResults.{{ $i }}.target_value" @disabled($deleted)>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Weight</label>
                                <input type="number" min="1" class="form-control" wire:model.defer="keyResults.{{ $i }}.weight" @disabled($deleted)>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Automation</label>
                                <select class="form-select" wire:model="keyResults.{{ $i }}.automation" @disabled($deleted)>
                                    <option value="manual">Manual</option>
                                    <option value="issues_done_percent">% Issues Done</option>
                                    <option value="story_points_done_percent">% Story Points Done</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Min (for maintain)</label>
                                <input type="number" step="any" class="form-control" wire:model.defer="keyResults.{{ $i }}.target_min" @disabled($deleted)>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Max (for maintain)</label>
                                <input type="number" step="any" class="form-control" wire:model.defer="keyResults.{{ $i }}.target_max" @disabled($deleted)>
                            </div>
                        </div>
                    </div>
                    <div class="ms-3 d-flex flex-column gap-2">
                        @if(!$deleted)
                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="markDelete({{ $i }})">Delete</button>
                        @else
                            <span class="badge text-bg-danger">Will delete</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-muted">No KRs. Add one to start.</div>
        @endforelse
    </div>

    <div class="card-footer d-flex justify-content-end gap-2">
        <a href="{{ route('goals.show', ['goal' => $goal]) }}" class="btn btn-secondary">Cancel</a>
        <button class="btn btn-primary" type="submit">Save Changes</button>
    </div>
</form>
