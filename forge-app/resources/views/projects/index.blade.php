<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('All Projects') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg">
                <table class="w-full border-collapse table-auto">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 border">{{ __('Name') }}</th>
                            <th class="px-4 py-2 border">{{ __('Type') }}</th>
                            <th class="px-4 py-2 border">{{ __('Status') }}</th>
                            <th class="px-4 py-2 border">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($projects as $project)
                            <tr>
                                <td class="px-4 py-2 border">{{ $project->name }}</td>
                                <td class="px-4 py-2 border">{{ $project->type->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2 border">{{ $project->status->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2 border">
                                    <a href="{{ route('projects.view', $project->id) }}"
                                        class="text-blue-500 hover:underline">
                                        {{ __('View') }}
                                    </a>
                                </td>
                                <td class="px-4 py-2 border">
                                    <button class="favorite-button" data-project-id="{{ $project->id }}"
                                        onclick="toggleFavorite({{ $project->id }})">
                                        {{ auth()->user()->favoriteProjects->contains($project->id)? 'Unfavorite': 'Favorite' }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-2 text-center border">
                                    {{ __('No projects found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        async function toggleFavorite(projectId) {
            try {
                const response = await fetch(`/project/${projectId}/favorite`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                });

                const result = await response.json();
                alert(result.message);
                location.reload(); // Optional: reload to reflect changes
            } catch (error) {
                console.error('Error toggling favorite:', error);
            }
        }
    </script>
</x-app-layout>
