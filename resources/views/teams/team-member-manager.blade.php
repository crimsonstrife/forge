<div class="d-grid gap-4">

    @if (Gate::check('addTeamMember', $team))
        {{-- Add Member --}}
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="mb-2">
                    <div class="fw-semibold">{{ __('Add Team Member') }}</div>
                    <div class="text-body-secondary small">
                        {{ __('Add a new team member to your team, allowing them to collaborate with you.') }}
                    </div>
                </div>

                <form wire:submit.prevent="addTeamMember" class="row g-3">
                    <div class="col-12 col-sm-6">
                        <label for="email" class="form-label">{{ __('Email') }}</label>
                        <input id="email" type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               wire:model="addTeamMemberForm.email">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    @if (count($this->roles) > 0)
                        <div class="col-12">
                            <label class="form-label">{{ __('Role') }}</label>
                            @error('role') <div class="text-danger small mb-1">{{ $message }}</div> @enderror>

                            <div class="list-group">
                                @foreach ($this->roles as $role)
                                    <label class="list-group-item list-group-item-action d-flex align-items-start gap-2 {{ (isset($addTeamMemberForm['role']) && $addTeamMemberForm['role'] === $role->key) ? 'active' : '' }}"
                                           wire:click="$set('addTeamMemberForm.role', '{{ $role->key }}')"
                                           style="cursor:pointer;">
                                        <input type="radio" class="form-check-input mt-1"
                                               name="role"
                                               value="{{ $role->key }}"
                                            @checked(isset($addTeamMemberForm['role']) && $addTeamMemberForm['role'] === $role->key)>
                                        <div>
                                            <div class="fw-semibold">{{ $role->name }}</div>
                                            <div class="text-body-secondary small">{{ $role->description }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="col-12 d-flex align-items-center gap-2">
                        <div x-data="{ shown: false, timeout: null }"
                             x-init="window.Livewire.find('{{ $_instance->getId() }}').on('saved', () => { clearTimeout(timeout); shown = true; timeout = setTimeout(() => { shown = false }, 2000) })"
                             x-show.transition.out.opacity.duration.1500ms="shown"
                             style="display:none"
                             class="small text-success">
                            {{ __('Added.') }}
                        </div>

                        <button type="submit" class="btn btn-primary">
                            {{ __('Add') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Pending Invitations --}}
        @if ($team->teamInvitations->isNotEmpty())
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="mb-2">
                        <div class="fw-semibold">{{ __('Pending Team Invitations') }}</div>
                        <div class="text-body-secondary small">
                            {{ __('These people have been invited and may join by accepting their email invitation.') }}
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        @foreach ($team->teamInvitations as $invitation)
                            <div class="d-flex align-items-center justify-content-between border rounded px-3 py-2">
                                <div class="text-body">{{ $invitation->email }}</div>

                                @if (Gate::check('removeTeamMember', $team))
                                    <button type="button" class="btn btn-link link-danger p-0"
                                            wire:click="cancelTeamInvitation({{ $invitation->id }})">
                                        {{ __('Cancel') }}
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endif

    @if ($team->users->isNotEmpty())
        {{-- Members list --}}
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="mb-2">
                    <div class="fw-semibold">{{ __('Team Members') }}</div>
                    <div class="text-body-secondary small">
                        {{ __('All of the people that are part of this team.') }}
                    </div>
                </div>

                <div class="d-grid gap-2">
                    @foreach ($team->users->sortBy('name') as $user)
                        <div class="d-flex align-items-center justify-content-between border rounded px-3 py-2">
                            <div class="d-flex align-items-center gap-2">
                                <x-avatar :src="$user->profile_photo_url" :name="$user->name" preset="sm" />
                                <div class="fw-medium">{{ $user->name }}</div>
                            </div>

                            <div class="d-flex align-items-center gap-3">
                                @if (Gate::check('updateTeamMember', $team) && Laravel\Jetstream\Jetstream::hasRoles())
                                    <button type="button" class="btn btn-link p-0"
                                            wire:click="manageRole('{{ $user->id }}')">
                                        {{ Laravel\Jetstream\Jetstream::findRole($user->membership->role)->name }}
                                    </button>
                                @elseif (Laravel\Jetstream\Jetstream::hasRoles())
                                    <div class="text-body-secondary small">
                                        {{ Laravel\Jetstream\Jetstream::findRole($user->membership->role)->name }}
                                    </div>
                                @endif

                                @if ($this->user->id === $user->id)
                                    <button type="button" class="btn btn-link link-danger p-0"
                                            wire:click="$toggle('confirmingLeavingTeam')">
                                        {{ __('Leave') }}
                                    </button>
                                @elseif (Gate::check('removeTeamMember', $team))
                                    <button type="button" class="btn btn-link link-danger p-0"
                                            wire:click="confirmTeamMemberRemoval('{{ $user->id }}')">
                                        {{ __('Remove') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Manage Role Modal --}}
    @if ($currentlyManagingRole)
        <div class="modal fade show" tabindex="-1" role="dialog" aria-modal="true" style="display:block;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Manage Role') }}</h5>
                        <button type="button" class="btn-close" aria-label="{{ __('Close') }}"
                                wire:click="stopManagingRole"></button>
                    </div>
                    <div class="modal-body">
                        <div class="list-group">
                            @foreach ($this->roles as $role)
                                <label class="list-group-item list-group-item-action d-flex align-items-start gap-2 {{ $currentRole === $role->key ? 'active' : '' }}"
                                       wire:click="$set('currentRole','{{ $role->key }}')"
                                       style="cursor:pointer;">
                                    <input type="radio" class="form-check-input mt-1"
                                           name="currentRole" value="{{ $role->key }}"
                                        @checked($currentRole === $role->key)>
                                    <div>
                                        <div class="fw-semibold">{{ $role->name }}</div>
                                        <div class="text-body-secondary small">{{ $role->description }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                                wire:click="stopManagingRole" wire:loading.attr="disabled">
                            {{ __('Cancel') }}
                        </button>
                        <button type="button" class="btn btn-primary"
                                wire:click="updateRole" wire:loading.attr="disabled">
                            {{ __('Save') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    {{-- Leave Team Modal --}}
    @if ($confirmingLeavingTeam)
        <div class="modal fade show" tabindex="-1" role="dialog" aria-modal="true" style="display:block;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Leave Team') }}</h5>
                        <button type="button" class="btn-close" aria-label="{{ __('Close') }}"
                                wire:click="$set('confirmingLeavingTeam', false)"></button>
                    </div>
                    <div class="modal-body">
                        {{ __('Are you sure you would like to leave this team?') }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                                wire:click="$set('confirmingLeavingTeam', false)" wire:loading.attr="disabled">
                            {{ __('Cancel') }}
                        </button>
                        <button type="button" class="btn btn-danger"
                                wire:click="leaveTeam" wire:loading.attr="disabled">
                            {{ __('Leave') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    {{-- Remove Member Modal --}}
    @if ($confirmingTeamMemberRemoval)
        <div class="modal fade show" tabindex="-1" role="dialog" aria-modal="true" style="display:block;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Remove Team Member') }}</h5>
                        <button type="button" class="btn-close" aria-label="{{ __('Close') }}"
                                wire:click="$set('confirmingTeamMemberRemoval', false)"></button>
                    </div>
                    <div class="modal-body">
                        {{ __('Are you sure you would like to remove this person from the team?') }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                                wire:click="$set('confirmingTeamMemberRemoval', false)" wire:loading.attr="disabled">
                            {{ __('Cancel') }}
                        </button>
                        <button type="button" class="btn btn-danger"
                                wire:click="removeTeamMember" wire:loading.attr="disabled">
                            {{ __('Remove') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
