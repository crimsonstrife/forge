<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight">
            {{ __('Create Report for ') . $dashboard->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg">
                <form action="{{ route('reports.store', $dashboard->id) }}" method="POST" class="p-6">
                    @csrf
                    <div>
                        <x-label for="title" value="{{ __('Report Title') }}" />
                        <x-input id="title" name="title" type="text" class="block w-full mt-1" required />
                    </div>

                    <div class="mt-4">
                        <x-label for="description" value="{{ __('Description') }}" />
                        <textarea id="description" name="description" class="block w-full mt-1"></textarea>
                    </div>

                    <div class="mt-4">
                        <x-label for="settings" value="{{ __('Settings (Optional JSON)') }}" />
                        <textarea id="settings" name="settings" class="block w-full mt-1"></textarea>
                    </div>

                    <div class="mt-6">
                        <x-button>{{ __('Create Report') }}</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
