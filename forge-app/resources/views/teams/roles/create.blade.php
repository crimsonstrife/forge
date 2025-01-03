<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Create Role for Team: ') . $team->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('teams.roles.store', $team) }}">
                        @csrf
                        <div class="mb-4">
                            <label for="name"
                                class="block text-sm font-medium text-gray-700">{{ __('Role Name') }}</label>
                            <input type="text" name="name" id="name"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                        </div>

                        <div class="mb-4">
                            <label for="permissions"
                                class="block text-sm font-medium text-gray-700">{{ __('Permissions') }}</label>
                            <select name="permissions[]" id="permissions" multiple
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                @foreach ($permissions as $permission)
                                    <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="px-4 py-2 text-white bg-blue-500 rounded-md">
                                {{ __('Create Role') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
