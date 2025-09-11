<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Concerns\InteractsWithTokenAbilities;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreIssueRequest;
use App\Http\Requests\Api\V1\UpdateIssueRequest;
use App\Http\Resources\Api\V1\IssueResource;
use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class IssueController extends Controller
{
    use InteractsWithTokenAbilities;

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->requireAbility($request, 'issues:read');

        $issues = Issue::query()
            ->with([
                'status:id,name,is_done',
                'priority:id,name',
                'type:id,key,name',
                'assignee:id,name',
                'project:id,key,name',
            ])
            ->when($request->boolean('assigned_to_me'), fn ($q) => $q->where('assignee_id', $request->user()->getKey()))
            ->when($project = $request->string('project')->toString(), fn ($q) => $q->where('project_id', $project))
            ->when($search = $request->string('q')->toString(), fn ($q) => $q->where('summary', 'like', "%{$search}%"))
            ->when($request->has('status'), fn ($q) => $q->whereIn('issue_status_id', (array) $request->input('status')))
            ->latest('updated_at')
            ->paginate(25);

        return IssueResource::collection($issues);
    }

    public function show(Request $request, Issue $issue): IssueResource
    {
        $this->requireAbility($request, 'issues:read');
        $this->authorize('view', $issue);

        return IssueResource::make($issue->loadMissing(['status', 'priority', 'type', 'assignee', 'project']));
    }

    public function store(StoreIssueRequest $request): IssueResource
    {
        $this->requireAbility($request, 'issues:write');
        $this->authorize('create', Issue::class);

        $data = $this->normalizeIssueKeys($request->validated());

        /** @var Issue $issue */
        $issue = Issue::query()->create($data);

        return IssueResource::make($issue->fresh(['status', 'priority', 'type', 'assignee', 'project']));
    }

    public function update(UpdateIssueRequest $request, Issue $issue): IssueResource
    {
        $this->requireAbility($request, 'issues:write');
        $this->authorize('update', $issue);

        $data = $this->normalizeIssueKeys($request->validated());

        $issue->fill($data)->save();

        return IssueResource::make($issue->fresh(['status', 'priority', 'type', 'assignee', 'project']));
    }

    /**
     * Map friendly API keys to DB columns.
     *
     * @param  array<string,mixed>  $data
     * @return array<string,mixed>
     */
    private function normalizeIssueKeys(array $data): array
    {
        $map = [
            'type_id'     => 'issue_type_id',
            'status_id'   => 'issue_status_id',
            'priority_id' => 'issue_priority_id',
        ];

        foreach ($map as $friendly => $dbKey) {
            if (array_key_exists($friendly, $data) && ! array_key_exists($dbKey, $data)) {
                $data[$dbKey] = $data[$friendly];
                unset($data[$friendly]);
            }
        }

        return $data;
    }
}
