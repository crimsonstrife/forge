<div class="rounded-xl border border-gray-200/60 dark:border-gray-700/60 bg-white dark:bg-gray-800 p-6">
    @if($parent)
        <div class="mb-4 rounded-lg border border-gray-200/60 dark:border-gray-700/60 p-3 text-sm bg-gray-50 dark:bg-gray-900/30">
            Creating as sub-issue of:
            <a class="underline" href="{{ route('issues.show', ['project' => $project, 'issue' => $parent]) }}">
                {{ $parent->key }} — {{ $parent->summary }}
            </a>
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-6">
        <div>
            <label class="block text-sm font-medium mb-1">Summary</label>
            <input type="text" wire:model.defer="summary" class="w-full rounded border px-3 py-2 bg-white dark:bg-gray-900">
            @error('summary') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea wire:model.defer="description" rows="5" class="w-full rounded border px-3 py-2 bg-white dark:bg-gray-900"></textarea>
            @error('description') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Type</label>
                <select wire:model="typeId"
                        class="w-full rounded border px-3 py-2 bg-white dark:bg-gray-900"
                        @if($parent) disabled @endif>
                    <option value="">Select type…</option>
                    @foreach($typeOptions as $opt)
                        <option value="{{ $opt['id'] }}">{{ $opt['name'] }}</option>
                    @endforeach
                </select>
                {{-- Ensure Livewire keeps the value when the field is disabled --}}
                @if($parent)
                    <input type="hidden" wire:model="typeId">
                @endif
                @error('typeId') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Priority</label>
                <select wire:model="priorityId" class="w-full rounded border px-3 py-2 bg-white dark:bg-gray-900">
                    <option value="">Select priority…</option>
                    @foreach($priorityOptions as $opt)
                        <option value="{{ $opt['id'] }}">{{ $opt['name'] }}</option>
                    @endforeach
                </select>
                @error('priorityId') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Assignee</label>
                <select wire:model="assigneeId" class="w-full rounded border px-3 py-2 bg-white dark:bg-gray-900">
                    <option value="">Unassigned</option>
                    @foreach($assigneeOptions as $u)
                        <option value="{{ $u['id'] }}">{{ $u['name'] }}</option>
                    @endforeach
                </select>
                @error('assigneeId') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>


        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Story points</label>
                <input type="number" wire:model.defer="storyPoints" class="w-full rounded border px-3 py-2 bg-white dark:bg-gray-900">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Estimate (minutes)</label>
                <input type="number" wire:model.defer="estimateMinutes" class="w-full rounded border px-3 py-2 bg-white dark:bg-gray-900">
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                    class="rounded-lg px-4 py-2 border-gray-300 dark:border-gray-700 hover:bg-primary-700"
                    wire:loading.attr="disabled">
                <span wire:loading.remove>Save</span>
                <span wire:loading>Saving…</span>
            </button>

            <a href="{{ route('projects.show', ['project' => $project]) }}"
               class="rounded-lg px-4 py-2 border border-gray-300 dark:border-gray-700">
                Cancel
            </a>
        </div>
    </form>
</div>
