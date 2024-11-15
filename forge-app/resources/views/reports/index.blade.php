<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight">
            {{ __('Reports for ') . $dashboard->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium">Existing Reports</h3>
                    @if($dashboard->reports->isEmpty())
                        <p class="mt-4">No reports available. <a href="{{ route('reports.create', $dashboard->id) }}" class="text-blue-600">Create one</a>.</p>
                    @else
                        <ul class="mt-4">
                            @foreach($dashboard->reports as $report)
                                <li class="py-2">
                                    <a href="{{ route('reports.show', [$dashboard->id, $report->id]) }}" class="text-blue-600">
                                        {{ $report->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
