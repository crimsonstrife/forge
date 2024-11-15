<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Create Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('dashboards.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <x-label for="name" :value="__('Dashboard Name')" />
                            <x-input id="name" name="name" type="text" class="block w-full mt-1" required />
                        </div>

                        <x-button>{{ __('Create Dashboard') }}</x-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
