<div class="rounded-xl border border-gray-200/60 dark:border-gray-700/60 bg-white dark:bg-gray-800 p-6 space-y-6">
    <div>
        <x-label for="summary" value="Summary"/>
        <x-input id="summary" type="text" class="mt-1 block w-full" wire:model.defer="summary" autofocus/>
        <x-input-error for="summary" class="mt-2"/>
    </div>

    <div>
        <x-label for="description" value="Description"/>
        <x-textarea id="description" rows="6" class="mt-1 block w-full" wire:model.defer="description"/>
        <x-input-error for="description" class="mt-2"/>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <x-label for="type" value="Type"/>
            <select id="type" class="mt-1 block w-full rounded-md border" wire:model.defer="type_id">
                @foreach($typeOptions as $t)
                    <option value="{{ $t['id'] }}">{{ $t['name'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <x-label for="priority" value="Priority"/>
            <select id="priority" class="mt-1 block w-full rounded-md border" wire:model.defer="priority_id">
                @foreach($priorityOptions as $p)
                    <option value="{{ $p['id'] }}">{{ $p['name'] }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('issues.index', ['project'=>$project]) }}" class="px-3 py-2 rounded-lg border">Cancel</a>
        <x-button wire:click="save">Create</x-button>
    </div>
</div>
