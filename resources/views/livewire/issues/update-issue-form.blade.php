<form wire:submit.prevent="save" class="d-grid gap-3">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12">
                    <x-label for="summary" value="Summary"/>
                    <input id="summary" type="text" wire:model.defer="summary" class="form-control">
                    @error('summary') <div class="form-text text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <x-label for="description" value="Description"/>
                    <textarea id="description" rows="6" wire:model.defer="description" class="form-control"></textarea>
                    @error('description') <div class="form-text text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="col-sm-6">
                    <x-label for="issue_type_id" value="Type"/>
                    <select id="issue_type_id" wire:model.defer="issue_type_id" class="form-select">
                        <option value="">{{ __('Select…') }}</option>
                        @foreach($this->typeOptions as $opt)
                            <option value="{{ $opt['id'] }}">{{ $opt['name'] }}</option>
                        @endforeach
                    </select>
                    @error('issue_type_id') <div class="form-text text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="col-sm-6">
                    <x-label for="issue_status_id" value="Status"/>
                    <select id="issue_status_id" wire:model.defer="issue_status_id" class="form-select">
                        <option value="">{{ __('Select…') }}</option>
                        @foreach($this->statusOptions as $opt)
                            <option value="{{ $opt['id'] }}">{{ $opt['name'] }}</option>
                        @endforeach
                    </select>
                    @error('issue_status_id') <div class="form-text text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="col-sm-6">
                    <x-label for="issue_priority_id" value="Priority"/>
                    <select id="issue_priority_id" wire:model.defer="issue_priority_id" class="form-select">
                        <option value="">{{ __('—') }}</option>
                        @foreach($this->priorityOptions as $opt)
                            <option value="{{ $opt['id'] }}">{{ $opt['name'] }}</option>
                        @endforeach
                    </select>
                    @error('issue_priority_id') <div class="form-text text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="col-sm-6">
                    <x-label for="assignee_id" value="Assignee"/>
                    <select id="assignee_id" wire:model.defer="assignee_id" class="form-select">
                        <option value="">{{ __('Unassigned') }}</option>
                        @foreach($this->assigneeOptions as $opt)
                            <option value="{{ $opt['id'] }}">{{ $opt['name'] }}</option>
                        @endforeach
                    </select>
                    @error('assignee_id') <div class="form-text text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="col-sm-6">
                    <x-label for="story_points" value="Story points"/>
                    <input id="story_points" type="number" min="0" wire:model.defer="story_points" class="form-control">
                    @error('story_points') <div class="form-text text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="col-sm-6">
                    <x-label for="estimate_minutes" value="Estimate (minutes)"/>
                    <input id="estimate_minutes" type="number" min="0" wire:model.defer="estimate_minutes" class="form-control">
                    @error('estimate_minutes') <div class="form-text text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <x-label for="starts_at_input" value="Start date & time"/>
                    <div class="d-flex gap-2">
                        <input id="starts_at_input" type="datetime-local" wire:model.defer="starts_at_input" class="form-control">
                        @if($starts_at_input)
                            <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="clearStart">{{ __('Clear') }}</button>
                        @endif
                    </div>
                    @error('starts_at_input') <div class="form-text text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <x-label for="due_at_input" value="Due date & time"/>
                    <div class="d-flex gap-2">
                        <input id="due_at_input" type="datetime-local" wire:model.defer="due_at_input" class="form-control">
                        @if($due_at_input)
                            <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="clearDue">{{ __('Clear') }}</button>
                        @endif
                    </div>
                    @error('due_at_input') <div class="form-text text-danger">{{ $message }}</div> @enderror
                    <div class="form-text">
                        {{ __('Times interpreted in your timezone') }} ({{ auth()->user()->timezone ?? config('app.timezone', 'UTC') }}).
                    </div>
                </div>

                <div class="col-12">
                    <x-label for="tags" value="Tags (comma-separated)"/>
                    <input id="tags" type="text" wire:model.defer="tags" class="form-control">
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('issues.show', ['project' => $this->project, 'issue' => $this->issue]) }}" class="btn btn-outline-secondary">
            {{ __('Cancel') }}
        </a>
        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
</form>
