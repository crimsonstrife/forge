<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Create Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
                <x-form-section submit="store">
                    <x-slot name="title">
                        {{ __('Create a New Dashboard') }}
                    </x-slot>

                    <x-slot name="description">
                        {{ __('Fill in the details below to create a new dashboard.') }}
                    </x-slot>

                    <x-slot name="form">
                        <div class="col-span-6 sm:col-span-4">
                            <!-- Name -->
                            <x-buk-label for="name" value="{{ __('Dashboard Name') }}" />
                            <x-buk-input id="name" name="name" type="text" class="block w-full mt-1" wire:model.defer="name" autocomplete="off" />
                            <x-buk-error for="name" field="name" class="mt-2" />

                            <!-- Description -->
                            <x-buk-label for="description" value="{{ __('Description') }}" class="mt-4" />
                            <x-buk-input id="description" name="name" class="block w-full mt-1" wire:model.defer="description" />
                            <x-buk-error for="description" field="description" class="mt-2" />

                            <!-- Is it a shared field? -->
                            <x-buk-label for="is_shared" value="{{ __('Share Dashboard') }}" class="mt-4" />
                            <x-buk-checkbox id="is_shared" name="is_shared" class="block mt-1" wire:model.defer="is_shared" />
                            <x-buk-error for="is_shared" field="is_shared" class="mt-2" />
                        </div>
                    </x-slot>

                    <x-slot name="actions">
                        <x-buk-form-button>
                            {{ __('Create Dashboard') }}
                        </x-buk-form-button>
                    </x-slot>
                </x-form-section>
            </div>
        </div>
    </div>
</x-app-layout>
