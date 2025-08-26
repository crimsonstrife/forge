<div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-6 space-y-6">
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <x-input-label for="name" value="Name" />
            <x-text-input wire:model.defer="name" id="name" type="text" class="mt-1 block w-full" autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="key" value="Key (2–10 A–Z/0–9)" />
            <x-text-input wire:model.defer="key" id="key" type="text" class="mt-1 block w-full uppercase" />
            <x-input-error :messages="$errors->get('key')" class="mt-2" />
        </div>
        <div class="sm:col-span-2">
            <x-input-label for="description" value="Description" />
            <x-textarea wire:model.defer="description" id="description" rows="4" class="mt-1 block w-full" />
            <x-input-error :messages="$errors->get('description')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="lead_id" value="Project Lead" />
            <select wire:model.defer="lead_id" id="lead_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700">
                <option value="">(Select)</option>
                @foreach($teamMembers as $m)
                    <option value="{{ $m['id'] }}">{{ $m['name'] }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('lead_id')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="stage" value="Stage" />
            <select wire:model.defer="stage" id="stage" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700">
                @foreach($stages as $s)
                    <option value="{{ $s->value }}">{{ ucfirst($s->value) }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('stage')" class="mt-2" />
        </div>

        <div class="sm:col-span-2">
            <x-input-label for="teams" value="Teams included in this project" />
            <select id="teams" multiple class="mt-1 block w-full rounded-md border">
                @foreach($teamOptions as $t)
                    <option value="{{ $t['id'] }}" wire:key="t-{{ $t['id'] }}"
                        @selected(in_array($t['id'], $attach_team_ids))>
                        {{ $t['name'] }}
                    </option>
                @endforeach
            </select>
            @error('attach_team_ids') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2">
            <x-input-label for="copy" value="Copy configuration from…" />
            <select wire:model.defer="copy_from_project_id" id="copy" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700">
                <option value="">(Use defaults)</option>
                @foreach($projectsForCopy as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500">Types, statuses, priorities, and transitions will be copied.</p>
            <x-input-error :messages="$errors->get('copy_from_project_id')" class="mt-2" />
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('projects.index') }}" class="px-3 py-2 rounded-lg border">Cancel</a>
        <x-primary-button wire:click="save">Create Project</x-primary-button>
    </div>
</div>
