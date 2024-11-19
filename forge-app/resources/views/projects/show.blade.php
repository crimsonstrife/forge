<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ $project->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold">{{ __('Project Details') }}</h3>
                    <p>{{ $project->description }}</p>

                    <div class="mt-4">
                        <h4 class="font-bold">{{ __('Type') }}</h4>
                        <p>{{ $project->type->name ?? 'N/A' }}</p>
                    </div>

                    <div class="mt-4">
                        <h4 class="font-bold">{{ __('Status') }}</h4>
                        <p>{{ $project->status->name ?? 'N/A' }}</p>
                    </div>

                    <div class="mt-4">
                        <h4 class="font-bold">{{ __('Views') }}</h4>
                        <p>{{ $project->view_count ?? '0' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
