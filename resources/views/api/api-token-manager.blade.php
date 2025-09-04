<div class="d-grid gap-4">
    {{-- Create API Token --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="mb-2">
                <div class="fw-semibold">{{ __('Create API Token') }}</div>
                <div class="text-body-secondary small">
                    {{ __('API tokens allow third-party services to authenticate with our application on your behalf.') }}
                </div>
            </div>

            <form wire:submit.prevent="createApiToken" class="row g-3">
                {{-- Token Name --}}
                <div class="col-12 col-sm-8 col-md-6">
                    <label for="token_name" class="form-label">{{ __('Token Name') }}</label>
                    <input id="token_name" type="text" class="form-control @error('name') is-invalid @enderror"
                           wire:model="createApiTokenForm.name" autofocus>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Token Permissions --}}
                @if (Laravel\Jetstream\Jetstream::hasPermissions())
                    <div class="col-12">
                        <label class="form-label">{{ __('Permissions') }}</label>
                        <div class="row g-2">
                            @foreach (Laravel\Jetstream\Jetstream::$permissions as $permission)
                                <div class="col-12 col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               id="perm_create_{{ Str::slug($permission) }}"
                                               wire:model="createApiTokenForm.permissions"
                                               value="{{ $permission }}">
                                        <label class="form-check-label" for="perm_create_{{ Str::slug($permission) }}">
                                            {{ $permission }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="col-12 d-flex align-items-center gap-2">
                    {{-- Keep the Jetstream action message if your Livewire class emits("created") --}}
                    <x-action-message class="text-success small" on="created">
                        {{ __('Created.') }}
                    </x-action-message>

                    <button type="submit" class="btn btn-primary">
                        {{ __('Create') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Existing Tokens --}}
    @if ($this->user->tokens->isNotEmpty())
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="mb-2">
                    <div class="fw-semibold">{{ __('Manage API Tokens') }}</div>
                    <div class="text-body-secondary small">
                        {{ __('You may delete any of your existing tokens if they are no longer needed.') }}
                    </div>
                </div>

                <div class="d-grid gap-2">
                    @foreach ($this->user->tokens->sortBy('name') as $token)
                        <div class="d-flex align-items-center justify-content-between p-2 border rounded">
                            <div class="text-break fw-medium">{{ $token->name }}</div>

                            <div class="d-flex align-items-center ms-2 gap-3">
                                @if ($token->last_used_at)
                                    <div class="small text-body-secondary">
                                        {{ __('Last used') }} {{ $token->last_used_at->diffForHumans() }}
                                    </div>
                                @endif

                                @if (Laravel\Jetstream\Jetstream::hasPermissions())
                                    <button type="button" class="btn btn-link btn-sm p-0"
                                            wire:click="manageApiTokenPermissions({{ $token->id }})">
                                        {{ __('Permissions') }}
                                    </button>
                                @endif

                                <button type="button" class="btn btn-link btn-sm text-danger p-0"
                                        wire:click="confirmApiTokenDeletion({{ $token->id }})">
                                    {{ __('Delete') }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    @endif

    {{-- Token Value Modal --}}
    @if($displayingToken)
        <div class="modal fade show" tabindex="-1" style="display:block;" aria-modal="true" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-6">{{ __('API Token') }}</h1>
                        <button type="button" class="btn-close" aria-label="Close"
                                wire:click="$set('displayingToken', false)"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2">
                            {{ __('Please copy your new API token. For your security, it won\'t be shown again.') }}
                        </p>
                        <input type="text"
                               readonly
                               value="{{ $plainTextToken }}"
                               class="form-control font-monospace text-muted">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                                wire:click="$set('displayingToken', false)">{{ __('Close') }}</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    {{-- API Token Permissions Modal --}}
    @if($managingApiTokenPermissions)
        <div class="modal fade show" tabindex="-1" style="display:block;" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-6">{{ __('API Token Permissions') }}</h1>
                        <button type="button" class="btn-close" aria-label="Close"
                                wire:click="$set('managingApiTokenPermissions', false)"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-2">
                            @foreach (Laravel\Jetstream\Jetstream::$permissions as $permission)
                                <div class="col-12 col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               id="perm_update_{{ Str::slug($permission) }}"
                                               wire:model="updateApiTokenForm.permissions"
                                               value="{{ $permission }}">
                                        <label class="form-check-label" for="perm_update_{{ Str::slug($permission) }}">
                                            {{ $permission }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-outline-secondary"
                                wire:click="$set('managingApiTokenPermissions', false)"
                                wire:loading.attr="disabled">
                            {{ __('Cancel') }}
                        </button>

                        <button type="button"
                                class="btn btn-primary"
                                wire:click="updateApiToken"
                                wire:loading.attr="disabled">
                            {{ __('Save') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    {{-- Delete Token Confirmation Modal --}}
    @if($confirmingApiTokenDeletion)
        <div class="modal fade show" tabindex="-1" style="display:block;" aria-modal="true" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-6">{{ __('Delete API Token') }}</h1>
                        <button type="button" class="btn-close" aria-label="Close"
                                wire:click="$toggle('confirmingApiTokenDeletion')"></button>
                    </div>
                    <div class="modal-body">
                        {{ __('Are you sure you would like to delete this API token?') }}
                    </div>
                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-outline-secondary"
                                wire:click="$toggle('confirmingApiTokenDeletion')"
                                wire:loading.attr="disabled">
                            {{ __('Cancel') }}
                        </button>
                        <button type="button"
                                class="btn btn-danger"
                                wire:click="deleteApiToken"
                                wire:loading.attr="disabled">
                            {{ __('Delete') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
