<?php

namespace App\Http\Controllers;

use App\Contracts\RepositoryProviderInterface;
use App\Http\Requests\Vcs\CreateBranchRequest;
use App\Http\Requests\Vcs\CreatePrRequest;
use App\Http\Requests\Vcs\SearchVcsRequest;
use App\Models\Issue;
use App\Models\IssueVcsLink;
use App\Models\ProjectRepository;
use App\Models\Repository;
use App\Services\Issues\IssueCollaborationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IssueVcsController extends Controller
{
    public function __construct(
        private IssueCollaborationService $collaboration,
    ) {
    }

    /** GET /api/issues/{key}/vcs/branches */
    public function searchBranches(SearchVcsRequest $request, Issue $issue): JsonResponse
    {
        $this->authorize('view', $issue->project);

        [$repo, $provider, $token] = $this->resolveRepoProviderAndCredential($issue, $request->validated('repository_id'));
        $items = $provider->searchBranches($repo, $token, (string) $request->validated('q', ''), 20);

        return response()->json($items);
    }

    /** GET /api/issues/{key}/vcs/pulls */
    public function searchPulls(SearchVcsRequest $request, Issue $issue): JsonResponse
    {
        $this->authorize('view', $issue->project);

        [$repo, $provider, $token] = $this->resolveRepoProviderAndCredential($issue, $request->validated('repository_id'));
        $items = $provider->searchPullRequests($repo, $token, (string) $request->validated('q', ''), 20);

        return response()->json($items);
    }

    /** POST /api/issues/{key}/vcs/link/branch */
    public function linkBranch(Request $request, Issue $issue): JsonResponse
    {
        $this->authorize('update', $issue->project);

        $data = $request->validate([
            'repository_id' => ['required','uuid','exists:repositories,id'],
            'name' => ['required','string','max:200'],
            'url'  => ['nullable','url','max:500'],
            'payload' => ['nullable','array'],
        ]);

        [$repo] = $this->resolveRepoProviderAndCredential($issue, $data['repository_id']); // validates access

        $link = IssueVcsLink::query()->firstOrCreate([
            'issue_id' => $issue->id,
            'repository_id' => $repo->id,
            'type' => 'branch',
            'name' => $data['name'],
        ], [
            'url' => $data['url'] ?? null,
            'payload' => $data['payload'] ?? null,
            'linked_by_user_id' => auth()->id(),
        ]);

        if ($link->wasRecentlyCreated) {
            $this->collaboration->notifyLinkChanged(
                issue: $issue,
                summary: __(':actor linked branch :name.', [
                    'actor' => auth()->user()?->name ?? __('Someone'),
                    'name' => $link->name,
                ]),
                actor: auth()->user(),
            );
        }

        return response()->json($link->toArray(), 201);
    }

    /** POST /api/issues/{key}/vcs/link/pr */
    public function linkPr(Request $request, Issue $issue): JsonResponse
    {
        $this->authorize('update', $issue->project);

        $data = $request->validate([
            'repository_id' => ['required','uuid','exists:repositories,id'],
            'number' => ['required','integer','min:1'],
            'title' => ['nullable','string','max:300'],
            'state' => ['nullable','string','max:50'],
            'url' => ['nullable','url','max:500'],
            'payload' => ['nullable','array'],
        ]);

        [$repo] = $this->resolveRepoProviderAndCredential($issue, $data['repository_id']);

        $link = IssueVcsLink::query()->firstOrCreate([
            'issue_id' => $issue->id,
            'repository_id' => $repo->id,
            'type' => 'pull_request',
            'number' => $data['number'],
        ], [
            'name' => $data['title'] ?? null,
            'state' => $data['state'] ?? null,
            'url' => $data['url'] ?? null,
            'payload' => $data['payload'] ?? null,
            'linked_by_user_id' => auth()->id(),
        ]);

        if ($link->wasRecentlyCreated) {
            $this->collaboration->notifyLinkChanged(
                issue: $issue,
                summary: __(':actor linked pull request #:number.', [
                    'actor' => auth()->user()?->name ?? __('Someone'),
                    'number' => $link->number,
                ]),
                actor: auth()->user(),
            );
        }

        return response()->json($link->toArray(), 201);
    }

    /** POST /api/issues/{key}/vcs/create/branch */
    public function createBranch(CreateBranchRequest $request, Issue $issue): JsonResponse
    {
        $this->authorize('update', $issue->project);

        [$repo, $provider, $token] = $this->resolveRepoProviderAndCredential($issue, $request->validated('repository_id'));

        $from = $request->validated('from_ref') ?: null;
        $name = $request->validated('name');

        $created = $provider->createBranch($repo, $token, $name, $from);

        $link = IssueVcsLink::query()->create([
            'issue_id' => $issue->id,
            'repository_id' => $repo->id,
            'type' => 'branch',
            'name' => $created['name'],
            'url' => $created['url'] ?? null,
            'linked_by_user_id' => auth()->id(),
            'payload' => $created,
        ]);

        $this->collaboration->notifyLinkChanged(
            issue: $issue,
            summary: __(':actor created branch :name.', [
                'actor' => auth()->user()?->name ?? __('Someone'),
                'name' => $link->name,
            ]),
            actor: auth()->user(),
        );

        return response()->json($link->toArray(), 201);
    }

    /** POST /api/issues/{key}/vcs/create/pr */
    public function createPr(CreatePrRequest $request, Issue $issue): JsonResponse
    {
        $this->authorize('update', $issue->project);

        [$repo, $provider, $token] = $this->resolveRepoProviderAndCredential($issue, $request->validated('repository_id'));

        $created = $provider->createPullRequest(
            $repo,
            $token,
            $request->validated('title'),
            $request->validated('head'),
            $request->validated('base'),
            $request->validated('body')
        );

        $link = IssueVcsLink::query()->create([
            'issue_id' => $issue->id,
            'repository_id' => $repo->id,
            'type' => 'pull_request',
            'number' => $created['number'] ?? null,
            'name' => $created['title'] ?? null,
            'state' => $created['state'] ?? null,
            'url' => $created['url'] ?? null,
            'linked_by_user_id' => auth()->id(),
            'payload' => $created,
        ]);

        $this->collaboration->notifyLinkChanged(
            issue: $issue,
            summary: __(':actor created pull request #:number.', [
                'actor' => auth()->user()?->name ?? __('Someone'),
                'number' => $link->number ?? __('new'),
            ]),
            actor: auth()->user(),
        );

        return response()->json($link->toArray(), 201);
    }

    /** @return array{0:Repository,1:RepositoryProviderInterface,2:string} */
    private function resolveRepoProviderAndCredential(Issue $issue, string $repositoryId): array
    {
        $link = ProjectRepository::query()
            ->where('project_id', $issue->project_id)
            ->where('repository_id', $repositoryId)
            ->first();

        abort_unless($link, 403, 'Repository not linked to this project.');

        /** @var Repository $repo */
        $repo = $link->repository;
        $provider = $this->providerFor($repo);

        if (strtolower((string) $repo->provider) === 'crucible') {
            return [$repo, $provider, (string) auth()->id()];
        }

        $token = trim((string) ($link->effectiveToken((string) $repo->provider) ?? $link->token));
        abort_unless($token !== '', 403, 'Missing token for repository.');

        if (strtolower((string) $repo->provider) === 'github' && ! $link->isLikelyGithubToken($token)) {
            abort(403, 'Stored repository token is invalid. Please re-link the repository.');
        }

        return [$repo, $provider, $token];
    }

    /** GET /issues/{key}/vcs/default-branch */
    public function defaultBranch(SearchVcsRequest $request, Issue $issue): JsonResponse
    {
        $this->authorize('view', $issue->project);

        [$repo, $provider, $token] = $this->resolveRepoProviderAndCredential($issue, $request->validated('repository_id'));
        $name = $provider->getDefaultBranch($repo, $token);

        return response()->json(['default' => $name]);
    }

    private function providerFor(Repository $repository): RepositoryProviderInterface
    {
        $factory = app('repo-provider-factory');

        return $factory((string) $repository->provider);
    }
}
