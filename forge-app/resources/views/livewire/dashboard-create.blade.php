<div>
    <x-form-section submit="createDashboard">
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
                <x-buk-input id="name" name="name" type="text" class="block w-full mt-1" wire:model.defer="name" />
                <x-buk-error field="name" />

                <!-- Description -->
                <x-buk-label for="description" value="{{ __('Description') }}" class="mt-4" />
                <x-buk-input id="description" name="description" type="text" class="block w-full mt-1" wire:model.defer="description" />
                <x-buk-error field="description" />

                <!-- Is Shared -->
                <x-buk-label for="is_shared" value="{{ __('Share Dashboard') }}" class="mt-4" />
                <x-buk-checkbox id="is_shared" name="is_shared" wire:model.defer="is_shared" />
                <x-buk-error field="is_shared" />
            </div>
        </x-slot>

        <x-slot name="actions">
            <x-button>
                {{ __('Create Dashboard') }}
            </x-button>
        </x-slot>
    </x-form-section>
</div>
