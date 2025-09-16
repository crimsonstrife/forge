<div class="card shadow-sm">
    <div class="card-body">
        @if (! $project)
            <div class="mb-3">
                <x-label for="__proj" value="Project"/>
                <select id="__proj" wire:model.live="selectedProjectId" class="form-select">
                    <option value="">Select project…</option>
                    @foreach ($projectOptions as $p)
                        <option value="{{ $p['id'] }}">{{ $p['key'] }} — {{ $p['name'] }}</option>
                    @endforeach
                </select>
                @error('selectedProjectId') <div class="form-text text-danger">{{ $message }}</div> @enderror
            </div>
        @endif

        @if($parent)
            <div class="alert alert-secondary small mb-3">
                Creating as sub-issue of
                <a class="link-primary" href="{{ route('issues.show', ['project' => $project, 'issue' => $parent]) }}">
                    {{ $parent->key }} — {{ $parent->summary }}
                </a>
            </div>
        @endif

        <form wire:submit.prevent="save">
            <fieldset @disabled(! $project)>
                <div class="row g-3">
                    <div class="col-12">
                        <x-label for="summary" value="Summary"/>
                        <input id="summary" type="text" wire:model.defer="summary" class="form-control">
                        @error('summary') <div class="form-text text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <x-label for="description" value="Description"/>
                        <textarea id="description" rows="5" wire:model.defer="description" class="form-control"></textarea>
                        @error('description') <div class="form-text text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-sm-4">
                        <x-label for="typeId" value="Type"/>
                        <select id="typeId" wire:model="typeId" class="form-select" @disabled(count($typeOptions)===1)>
                            <option value="">Select type…</option>
                            @foreach ($typeOptions as $opt)
                                <option value="{{ $opt['id'] }}">{{ $opt['name'] }}</option>
                            @endforeach
                        </select>
                        @if (count($typeOptions) === 1)
                            <input type="hidden" wire:model="typeId">
                            <div class="form-text">{{ __('Type is fixed for this parent.') }}</div>
                        @endif
                        @error('typeId') <div class="form-text text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-sm-4">
                        <x-label for="priorityId" value="Priority"/>
                        <select id="priorityId" wire:model="priorityId" class="form-select">
                            <option value="">Select priority…</option>
                            @foreach($priorityOptions as $opt)
                                <option value="{{ $opt['id'] }}">{{ $opt['name'] }}</option>
                            @endforeach
                        </select>
                        @error('priorityId') <div class="form-text text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-sm-4">
                        <x-label for="assigneeId" value="Assignee"/>
                        <select id="assigneeId" wire:model="assigneeId" class="form-select">
                            <option value="">{{ __('Unassigned') }}</option>
                            @foreach($assigneeOptions as $u)
                                <option value="{{ $u['id'] }}">{{ $u['name'] }}</option>
                            @endforeach
                        </select>
                        @error('assigneeId') <div class="form-text text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-sm-6">
                        <x-label for="storyPoints" value="Story points"/>
                        <input id="storyPoints" type="number" wire:model.defer="storyPoints" class="form-control">
                    </div>
                    <div class="col-sm-6">
                        <x-label for="estimateMinutes" value="Estimate (minutes)"/>
                        <input id="estimateMinutes" type="number" wire:model.defer="estimateMinutes" class="form-control">
                    </div>
                </div>
            </fieldset>

            <div class="d-flex justify-content-end gap-2 mt-3">
                @if($project)
                    <a href="{{ route('projects.show', ['project' => $project]) }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                @else
                    <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                @endif
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('Save') }}</span>
                    <span wire:loading>{{ __('Saving…') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
