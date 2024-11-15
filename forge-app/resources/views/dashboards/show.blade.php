<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ $dashboard->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <p>{{ $dashboard->description }}</p>
                </div>
                <div class="p-6 bg-gray-100 border-t">
                    <h3 class="mb-4 text-lg font-semibold text-gray-800">Reports</h3>
                    @forelse ($dashboard->reports as $report)
                        <div class="mb-4">
                            <h4 class="text-lg font-medium">{{ $report->title }}</h4>
                            <p class="text-sm text-gray-500">{{ $report->description }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">{{ __('No reports available for this dashboard.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>