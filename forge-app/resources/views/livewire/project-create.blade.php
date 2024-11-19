<div>
    <form wire:submit.prevent="save" class="space-y-6">
        <!-- Project Name -->
        <x-form-section submit="saveProject">
            <x-slot name="title">{{ __('Project Details') }}</x-slot>
            <x-slot name="description">
                {{ __('Enter the details of the new project.') }}
            </x-slot>
            <x-slot name="form">
                <div class="col-span-6 sm:col-span-4">
                    <x-input-label for="name" value="{{ __('Project Name') }}" />
                    <x-text-input id="name" wire:model.defer="name" type="text" class="block w-full mt-1"
                        required />
                    <x-input-error for="name" class="mt-2" />
                </div>

                <div class="col-span-6 sm:col-span-4">
                    <x-input-label for="description" value="{{ __('Description') }}" />
                    <x-textarea id="description" wire:model.defer="description" class="block w-full mt-1" />
                    <x-input-error for="description" class="mt-2" />
                </div>

                <div class="col-span-6 sm:col-span-4">
                    <x-input-label for="type_id" value="{{ __('Project Type') }}" />
                    <x-select id="type_id" wire:model.defer="type_id" class="block w-full mt-1">
                        <option value="">{{ __('Select a Type') }}</option>
                        @foreach ($projectTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </x-select>
                    <x-input-error for="type_id" class="mt-2" />
                </div>

                <div class="col-span-6 sm:col-span-4">
                    <x-input-label for="status_id" value="{{ __('Project Status') }}" />
                    <x-select id="status_id" wire:model.defer="status_id" class="block w-full mt-1">
                        <option value="">{{ __('Select a Status') }}</option>
                        @foreach ($projectStatuses as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </x-select>
                    <x-input-error for="status_id" class="mt-2" />
                </div>

                <!-- Other fields -->
                <div class="col-span-6 sm:col-span-4" >
                    <livewire:tags-manager :existing-tags="$project->tags->pluck('id')->toArray()" />
                </div>

                <div class="col-span-6 sm:col-span-4">
                    <x-input-label for="repository_url" value="{{ __('Repository URL') }}" />
                    <x-text-input id="repository_url" wire:model.defer="repository_url" type="url"
                        class="block w-full mt-1" />
                    <x-input-error for="repository_url" class="mt-2" />
                </div>
            </x-slot>
        </x-form-section>

        <!-- Submit Button -->
        <div class="flex items-center justify-end">
            <x-button>{{ __('Create Project') }}</x-button>
        </div>
    </form>
</div>
