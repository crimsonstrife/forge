<div class="card shadow-sm">
    <div class="card-body">
        <div class="mb-2">
            <div class="fw-semibold text-danger">{{ __('Delete Team') }}</div>
            <div class="text-body-secondary small">
                {{ __('Permanently delete this team.') }}
            </div>
        </div>

        <p class="text-body-secondary small mb-3">
            {{ __('Once a team is deleted, all of its resources and data will be permanently deleted. Please download any data you wish to retain.') }}
        </p>

        <button type="button" class="btn btn-danger" wire:click="confirmTeamDeletion" wire:loading.attr="disabled">
            {{ __('Delete Team') }}
        </button>
    </div>
</div>

@if ($confirmingTeamDeletion ?? false)
    <div class="modal fade show" tabindex="-1" role="dialog" aria-modal="true" style="display:block;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Delete Team') }}</h5>
                    <button type="button" class="btn-close" aria-label="{{ __('Close') }}"
                            wire:click="$set('confirmingTeamDeletion', false)"></button>
                </div>
                <div class="modal-body">
                    {{ __('Are you sure you want to delete this team? This action cannot be undone.') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                            wire:click="$set('confirmingTeamDeletion', false)" wire:loading.attr="disabled">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" class="btn btn-danger"
                            wire:click="deleteTeam" wire:loading.attr="disabled">
                        {{ __('Delete Team') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
@endif
