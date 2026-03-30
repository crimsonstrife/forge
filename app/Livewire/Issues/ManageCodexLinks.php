<?php

namespace App\Livewire\Issues;

use App\Models\Issue;
use App\Models\IssueCodexLink;
use App\Support\Codex\CodexConnection;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;

final class ManageCodexLinks extends Component
{
    use AuthorizesRequests;

    public Issue $issue;

    public string $search = '';

    /** @var array<int, array{id:string, title:string, url:string, workspace_id:string, workspace_slug:string, workspace_name:string}> */
    public array $results = [];

    public bool $searching = false;

    public ?string $error = null;

    public function mount(Issue $issue): void
    {
        $this->authorize('view', $issue);
        $this->issue = $issue;
    }

    public function updatedSearch(string $value): void
    {
        $this->searchPages();
    }

    public function searchPages(): void
    {
        $q = trim($this->search);
        if ($q === '') {
            $this->results = [];

            return;
        }

        $codex = app(CodexConnection::class);

        if (! $codex->configured()) {
            $this->error = 'Codex integration is not configured. Configure Codex in settings or set CODEX_ENABLED, CODEX_URL, and CODEX_APP_TOKEN.';

            return;
        }

        $this->searching = true;
        $this->error = null;

        // Filter by the linked workspace if this project is connected to one
        $workspaceId = $this->issue->project?->codex_workspace_id;

        try {
            $response = Http::withToken($codex->token())
                ->acceptJson()
                ->timeout(10)
                ->withoutVerifying()
                ->get($codex->baseUrl().'/api/v1/pages/search', array_filter([
                    'q' => $q,
                    'workspace_id' => $workspaceId,
                ]));

            if ($response->successful()) {
                $this->results = $response->json('data') ?? [];
            } else {
                Log::warning('Codex page search failed', [
                    'status' => $response->status(),
                    'base_url' => $codex->baseUrl(),
                    'path' => '/api/v1/pages/search',
                    'issue_id' => (string) $this->issue->getKey(),
                    'project_id' => (string) $this->issue->project_id,
                    'workspace_id' => (string) ($workspaceId ?? ''),
                    'forge_user_id' => (string) auth()->id(),
                    'response_body' => Str::limit($response->body(), 1000),
                ]);
                $this->error = 'Codex search failed (HTTP '.$response->status().').';
                $this->results = [];
            }
        } catch (\Throwable $e) {
            report($e);
            $this->error = 'Could not reach Codex: '.$e->getMessage();
            $this->results = [];
        }

        $this->searching = false;
    }

    /**
     * Link a Codex page to this issue.
     */
    public function link(
        string $pageId,
        string $pageTitle,
        string $pageUrl,
        string $workspaceId,
        string $workspaceSlug,
    ): void {
        $this->authorize('update', $this->issue);

        // Guard against duplicates (DB unique also protects this)
        $exists = IssueCodexLink::query()
            ->where('issue_id', $this->issue->getKey())
            ->where('codex_page_id', $pageId)
            ->exists();

        if ($exists) {
            $this->dispatch('notify', title: 'Already linked', body: 'This Codex page is already linked.');

            return;
        }

        IssueCodexLink::query()->create([
            'issue_id' => $this->issue->getKey(),
            'codex_page_id' => $pageId,
            'codex_page_title' => $pageTitle,
            'codex_page_url' => $pageUrl,
            'codex_workspace_id' => $workspaceId,
            'codex_workspace_slug' => $workspaceSlug,
            'added_by_id' => auth()->id(),
        ]);

        $this->reset('search', 'results');
        $this->dispatch('notify', title: 'Page linked', body: 'Codex page linked to this issue.');
    }

    /**
     * Remove a Codex page link from this issue.
     */
    public function unlink(string $linkId): void
    {
        $link = IssueCodexLink::query()
            ->where('issue_id', $this->issue->getKey())
            ->findOrFail($linkId);

        $this->authorize('update', $this->issue);

        $link->delete();

        $this->dispatch('notify', title: 'Unlinked', body: 'Codex page link removed.');
    }

    public function render(): View
    {
        $links = $this->issue->codexLinks()->orderBy('created_at')->get();

        return view('livewire.issues.manage-codex-links', compact('links'));
    }
}
