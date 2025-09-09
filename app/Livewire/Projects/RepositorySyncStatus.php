<?php

namespace App\Livewire\Projects;

use App\Models\ProjectRepository;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

final class RepositorySyncStatus extends Component
{
    /** Keep only the ID in state for reliable refresh/diffing */
    public string $linkId;

    public function mount(ProjectRepository $link): void
    {
        $this->linkId = (string) $link->id;
    }

    /** Optional manual refresh invoked by poll */
    public function refreshLink(): void
    {
        // no-op: we re-query in render() so calling $refresh is enough
        $this->dispatch('$refresh');
    }

    /** React to “repo-link-refreshed” events from the ManageRepository component */
    #[On('repo-link-refreshed')]
    public function onRepoLinkRefreshed(): void
    {
        $this->dispatch('$refresh');
    }

    public function render(): View
    {
        /** @var ProjectRepository|null $link */
        $link = ProjectRepository::query()
            ->select([
                'id',
                'last_sync_status',
                'last_sync_error',
                'initial_import_started_at',
                'initial_import_finished_at',
                'updated_at',
            ])
            ->find($this->linkId);

        return view('livewire.projects.repository-sync-status', compact('link'));
    }
}
