<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Manage Dashboards') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold">{{ __('Your Dashboards') }}</h3>

                    <ul class="mt-4 list-disc list-inside">
                        @forelse ($dashboards as $dashboard)
                            <li class="flex items-center justify-between">
                                <span>{{ $dashboard->name }}</span>
                                <a href="{{ route('dashboards.show', $dashboard->id) }}" class="text-blue-500 hover:underline">
                                    {{ __('View') }}
                                </a>
                            </li>
                        @empty
                            <p>{{ __('You have no dashboards yet. Create one below!') }}</p>
                        @endforelse
                    </ul>

                    <div class="mt-6">
                        <a href="{{ route('dashboards.create') }}" class="px-4 py-2 text-sm text-white bg-blue-500 rounded hover:bg-blue-700">
                            {{ __('Create New Dashboard') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
