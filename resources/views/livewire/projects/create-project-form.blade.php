<div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-6 space-y-6">
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <x-label for="name" value="Name"/>
            <x-input wire:model.defer="name" id="name" type="text" class="mt-1 block w-full" autofocus/>
            <x-input-error for="name" class="mt-2"/>
        </div>
        <div>
            <x-label for="key" value="Key (2–10 A–Z/0–9)"/>
            <x-input wire:model.defer="key" id="key" type="text" class="mt-1 block w-full uppercase"/>
            <x-input-error for="key" class="mt-2"/>
        </div>
        <div class="sm:col-span-2">
            <x-label for="description" value="Description"/>
            <x-textarea wire:model.defer="description" id="description" type="text" rows="4" class="mt-1 block w-full"/>
            <x-input-error for="description" class="mt-2"/>
        </div>
        <div>
            <x-label for="lead_id" value="Project Lead"/>
            <select wire:model.defer="lead_id" id="lead_id"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700">
                <option value="">(Select)</option>
                @foreach($teamMembers as $m)
                    <option value="{{ $m['id'] }}">{{ $m['name'] }}</option>
                @endforeach
            </select>
            <x-input-error for="lead_id" class="mt-2"/>
        </div>
        <div>
            <x-label for="stage" value="Stage"/>
            <select wire:model.defer="stage" id="stage"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700">
                @foreach($stages as $s)
                    <option value="{{ $s->value }}">{{ ucfirst($s->value) }}</option>
                @endforeach
            </select>
            <x-input-error for="stage" class="mt-2"/>
        </div>

        <div class="sm:col-span-2">
            <x-label for="teams" value="Teams included in this project"/>
            <select id="teams" multiple wire:model.defer="attach_team_ids"
                    class="mt-1 block w-full rounded-md border">
                @foreach($teamOptions as $t)
                    <option value="{{ $t['id'] }}">{{ $t['name'] }}</option>
                @endforeach
            </select>
            <x-input-error for="attach_team_ids" class="mt-2"/>
        </div>

        <div class="sm:col-span-2">
            <x-label for="copy" value="Copy configuration from…"/>
            <select wire:model.defer="copy_from_project_id" id="copy"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700">
                <option value="">(Use defaults)</option>
                @foreach($projectsForCopy as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500">Types, statuses, priorities, and transitions will be copied.</p>
            <x-input-error for="copy" class="mt-2"/>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('projects.index') }}" class="px-3 py-2 rounded-lg border">Cancel</a>
        <x-button wire:click="save">Create Project</x-button>
    </div>
</div>
