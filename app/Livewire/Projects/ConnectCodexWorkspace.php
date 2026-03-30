<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use App\Support\Codex\CodexConnection;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;

final class ConnectCodexWorkspace extends Component
{
    use AuthorizesRequests;

    public Project $project;

    /** Search query for filtering workspaces */
    public string $search = '';

    /** @var array<int, array{id:string, name:string, slug:string}> */
    public array $workspaces = [];

    public bool $loading = false;

    public ?string $error = null;

    public function mount(Project $project): void
    {
        $this->authorize('update', $project);
        $this->project = $project;
    }

    /**
     * Fetch available workspaces from the Codex API.
     * Called when the user opens the search panel.
     */
    public function loadWorkspaces(): void
    {
        $this->loading = true;
        $this->error = null;
        $this->workspaces = [];

        $codex = app(CodexConnection::class);

        if (! $codex->configured()) {
            $this->error = 'Codex integration is not configured. Configure Codex in settings or set CODEX_ENABLED, CODEX_URL, and CODEX_APP_TOKEN.';
            $this->loading = false;

            return;
        }

        try {
            $response = Http::withToken($codex->token())
                ->acceptJson()
                ->timeout(10)
                ->withoutVerifying()
                ->get($codex->baseUrl().'/api/v1/workspaces', [
                    'search' => $this->search,
                    // Let Codex scope results to workspaces this user can access.
                    // Codex resolves the Forge user ID to the matching Codex account.
                    'for_forge_user_id' => auth()->id(),
                ]);

            if ($response->successful()) {
                $this->workspaces = $response->json('data') ?? [];
            } else {
                Log::warning('Codex workspace lookup failed', [
                    'status' => $response->status(),
                    'base_url' => $codex->baseUrl(),
                    'path' => '/api/v1/workspaces',
                    'project_id' => (string) $this->project->getKey(),
                    'forge_user_id' => (string) auth()->id(),
                    'has_search' => trim($this->search) !== '',
                    'response_body' => Str::limit($response->body(), 1000),
                ]);
                $this->error = 'Failed to load workspaces from Codex (HTTP '.$response->status().').';
            }
        } catch (\Throwable $e) {
            report($e);
            $this->error = 'Could not connect to Codex: '.$e->getMessage();
        }

        $this->loading = false;
    }

    /**
     * Link the given Codex workspace to this project.
     */
    public function link(string $workspaceId, string $workspaceSlug): void
    {
        $this->authorize('update', $this->project);

        $this->project->codex_workspace_id = $workspaceId;
        $this->project->codex_workspace_slug = $workspaceSlug;
        $this->project->save();

        $this->reset('search', 'workspaces', 'error');
        $this->dispatch('notify', title: 'Linked', body: 'Codex workspace linked to this project.');
    }

    /**
     * Unlink the Codex workspace from this project.
     */
    public function unlink(): void
    {
        $this->authorize('update', $this->project);

        $this->project->codex_workspace_id = null;
        $this->project->codex_workspace_slug = null;
        $this->project->save();

        $this->dispatch('notify', title: 'Unlinked', body: 'Codex workspace disconnected from this project.');
    }

    public function render(): View
    {
        return view('livewire.projects.connect-codex-workspace');
    }
}
