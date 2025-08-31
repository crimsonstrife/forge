<form wire:submit.prevent="save" class="space-y-4">
    <div class="rounded-xl border border-gray-200/60 dark:border-gray-700/60 bg-white dark:bg-gray-800 p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium mb-1">Summary</label>
            <input type="text" wire:model.defer="summary"
                   class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
            @error('summary') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea rows="6" wire:model.defer="description"
                      class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800"></textarea>
            @error('description') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Type</label>
                <select wire:model.defer="issue_type_id"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                    <option value="">Select…</option>
                    @foreach($this->typeOptions as $opt)
                        <option value="{{ $opt['id'] }}">{{ $opt['name'] }}</option>
                    @endforeach
                </select>
                @error('issue_type_id') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Status</label>
                <select wire:model.defer="issue_status_id"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                    <option value="">Select…</option>
                    @foreach($this->statusOptions as $opt)
                        <option value="{{ $opt['id'] }}">{{ $opt['name'] }}</option>
                    @endforeach
                </select>
                @error('issue_status_id') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Priority</label>
                <select wire:model.defer="issue_priority_id"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                    <option value="">—</option>
                    @foreach($this->priorityOptions as $opt)
                        <option value="{{ $opt['id'] }}">{{ $opt['name'] }}</option>
                    @endforeach
                </select>
                @error('issue_priority_id') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Assignee</label>
                <select wire:model.defer="assignee_id"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                    <option value="">Unassigned</option>
                    @foreach($this->assigneeOptions as $opt)
                        <option value="{{ $opt['id'] }}">{{ $opt['name'] }}</option>
                    @endforeach
                </select>
                @error('assignee_id') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Story points</label>
                <input type="number" wire:model.defer="story_points" min="0" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                @error('story_points') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Estimate (minutes)</label>
                <input type="number" wire:model.defer="estimate_minutes" min="0" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                @error('estimate_minutes') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Start date & time</label>
            <div class="flex items-center gap-2">
                <input
                    type="datetime-local"
                    wire:model.defer="starts_at_input"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800"
                />
                @if($starts_at_input)
                    <button type="button"
                            wire:click="clearStart"
                            class="px-2 py-1 text-xs rounded border border-gray-300 dark:border-gray-700">
                        Clear
                    </button>
                @endif
            </div>
            @error('starts_at_input') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Due date & time</label>
            <div class="flex items-center gap-2">
                <input
                    type="datetime-local"
                    wire:model.defer="due_at_input"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800"
                />
                @if($due_at_input)
                    <button type="button"
                            wire:click="clearDue"
                            class="px-2 py-1 text-xs rounded border border-gray-300 dark:border-gray-700">
                        Clear
                    </button>
                @endif
            </div>
            @error('due_at_input') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
            <p class="text-xs text-gray-500 mt-1">
                Times interpreted in your timezone ({{ auth()->user()->timezone ?? config('app.timezone', 'UTC') }}).
            </p>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Tags (comma-separated)</label>
            <input type="text" wire:model.defer="tags"
                   class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
        </div>
    </div>

    <div class="flex justify-end gap-2">
        <a href="{{ route('issues.show', ['project' => $this->project, 'issue' => $this->issue]) }}"
           class="rounded-lg px-3 py-2 border border-gray-300 dark:border-gray-700">Cancel</a>
        <button type="submit" class="rounded-lg px-3 py-2 bg-primary-600 text-white hover:bg-primary-700">
            Save
        </button>
    </div>
</form>
