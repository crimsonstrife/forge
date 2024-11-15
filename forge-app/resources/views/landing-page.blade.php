<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            {{ __('Welcome to the App') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg">
                <div class="p-6">
                    {{ __('You don’t have any dashboards assigned yet. Please contact your admin or create your first dashboard.') }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
