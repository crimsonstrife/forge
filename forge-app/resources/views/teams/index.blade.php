<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Teams') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="mb-4 text-lg font-semibold">{{ __('Your Teams') }}</h3>
                    <ul>
                        @foreach ($teams as $team)
                            <li class="mb-2">
                                <a href="{{ route('teams.show', $team->id) }}" class="text-blue-500 hover:underline">
                                    {{ $team->name }}
                                </a>
                                <span class="text-gray-500">({{ $team->members->count() }} members)</span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-6">
                        <a href="{{ route('teams.create') }}" class="px-4 py-2 text-white bg-blue-500 rounded-md">
                            {{ __('Create New Team') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
