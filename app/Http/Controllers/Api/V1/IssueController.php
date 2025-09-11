<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Issue;
use App\Models\IssueStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreIssueRequest;
use App\Http\Requests\Api\V1\UpdateIssueRequest;
use App\Http\Resources\Api\V1\IssueResource;
use App\Http\Controllers\Api\Concerns\InteractsWithTokenAbilities;

final class IssueController extends Controller
{
    use InteractsWithTokenAbilities;

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->requireAbility($request, 'issues:read');

        $query = Issue::query()
            ->with(['status:id,name,is_done', 'priority:id,name', 'type:id,key,name', 'assignee:id,name', 'project:id,key,name'])
            ->when($request->boolean('assigned_to_me'), fn ($q) => $q->where('assignee_id', $request->user()->getKey()))
            ->when($project = $request->string('project')->toString(), fn ($q) => $q->where('project_id', $project))
            ->when($search = $request->string('q')->toString(), fn ($q) => $q->where('summary', 'like', "%{$search}%"))
            ->when($request->has('status'), fn ($q) => $q->whereIn('status_id', (array) $request->input('status')))
            ->latest('updated_at');

        $issues = $query->paginate(25);

        return IssueResource::collection($issues);
    }

    public function show(Request $request, Issue $issue): IssueResource
    {
        $this->requireAbility($request, 'issues:read');
        $this->authorize('view', $issue);

        $issue->loadMissing(['status', 'priority', 'type', 'assignee', 'project']);

        return IssueResource::make($issue);
    }

    public function store(StoreIssueRequest $request): IssueResource
    {
        $this->requireAbility($request, 'issues:write');
        $this->authorize('create', Issue::class);

        /** @var Issue $issue */
        $issue = Issue::query()->create($request->validated());

        return IssueResource::make($issue->fresh(['status', 'priority', 'type', 'assignee', 'project']));
    }

    public function update(UpdateIssueRequest $request, Issue $issue): IssueResource
    {
        $this->requireAbility($request, 'issues:write');
        $this->authorize('update', $issue);

        $issue->fill($request->validated())->save();

        return IssueResource::make($issue->fresh(['status', 'priority', 'type', 'assignee', 'project']));
    }
}
