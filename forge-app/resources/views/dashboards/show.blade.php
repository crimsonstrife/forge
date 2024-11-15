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
                    <div class="mt-6">
                        <h2 class="text-lg font-semibold leading-6 text-gray-900">Reports</h2>

                        @if($dashboard->reports->isEmpty())
                            <p class="text-gray-600">No reports found. <a href="{{ route('reports.create', $dashboard->id) }}" class="text-blue-600">Create a report</a>.</p>
                        @else
                            <ul>
                                @foreach($dashboard->reports as $report)
                                    <li class="py-2">
                                        <a href="{{ route('reports.show', [$dashboard->id, $report->id]) }}" class="text-blue-600">
                                            {{ $report->title }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ route('reports.create', $dashboard->id) }}" class="inline-block mt-4 text-blue-600">Create another report</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
