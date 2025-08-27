<div class="rounded-xl border border-gray-200/60 dark:border-gray-700/60 bg-white dark:bg-gray-800 p-6 space-y-6">
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <x-label for="name" value="Name"/>
            <x-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="name" autofocus/>
            <x-input-error for="name" class="mt-2"/>
        </div>
        <div>
            <x-label for="key" value="Key"/>
            <x-input id="key" type="text" class="mt-1 block w-full uppercase" wire:model.defer="key"/>
            <x-input-error for="key" class="mt-2"/>
        </div>
        <div class="sm:col-span-2">
            <x-label for="description" value="Description"/>
            <x-textarea id="description" rows="5" class="mt-1 block w-full" wire:model.defer="description"/>
            <x-input-error for="description" class="mt-2"/>
        </div>
        <div>
            <x-label for="stage" value="Stage"/>
            <select id="stage" class="mt-1 block w-full rounded-md border" wire:model.defer="stage">
                @foreach($stages as $s)
                    <option value="{{ $s->value }}">{{ ucfirst($s->value) }}</option>
                @endforeach
            </select>
            <x-input-error for="stage" class="mt-2"/>
        </div>
        <div>
            <x-label for="lead_id" value="Lead (optional)"/>
            <x-input id="lead_id" type="text" placeholder="UUID" class="mt-1 block w-full" wire:model.defer="lead_id"/>
            <x-input-error for="lead_id" class="mt-2"/>
        </div>
        <div>
            <x-label for="started_at" value="Start date"/>
            <x-input id="started_at" type="date" class="mt-1 block w-full" wire:model.defer="started_at"/>
            <x-input-error for="started_at" class="mt-2"/>
        </div>
        <div>
            <x-label for="due_at" value="Deadline"/>
            <x-input id="due_at" type="date" class="mt-1 block w-full" wire:model.defer="due_at"/>
            <x-input-error for="due_at" class="mt-2"/>
        </div>
        <div>
            <x-label for="ended_at" value="Ended"/>
            <x-input id="ended_at" type="date" class="mt-1 block w-full" wire:model.defer="ended_at"/>
            <x-input-error for="ended_at" class="mt-2"/>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('projects.show', ['project'=>$project]) }}" class="px-3 py-2 rounded-lg border">Cancel</a>
        <x-button wire:click="save">Save</x-button>
    </div>
</div>
