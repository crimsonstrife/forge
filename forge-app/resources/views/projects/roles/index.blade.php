<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Manage Roles for Project: ') . $project->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900">Roles</h3>
                    <a href="{{ route('projects.roles.create', $project) }}"
                        class="inline-flex items-center px-4 py-2 mt-2 text-white bg-blue-500 rounded-md">
                        {{ __('Create Role') }}
                    </a>

                    <table class="min-w-full mt-4 divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase">
                                    {{ __('Name') }}
                                </th>
                                <th class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase">
                                    {{ __('Permissions') }}
                                </th>
                                <th class="px-6 py-3 text-xs font-medium text-right text-gray-500 uppercase">
                                    {{ __('Actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $role)
                                <tr>
                                    <td class="px-6 py-4">{{ $role->name }}</td>
                                    <td class="px-6 py-4">
                                        {{ $role->permissions->pluck('name')->join(', ') }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('projects.roles.edit', [$project, $role]) }}"
                                            class="text-indigo-600 hover:text-indigo-900">
                                            {{ __('Edit') }}
                                        </a>
                                        <form action="{{ route('projects.roles.destroy', [$project, $role]) }}"
                                            method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                {{ __('Delete') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
