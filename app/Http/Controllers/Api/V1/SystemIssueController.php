<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\IssueResource;
use App\Models\Issue;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

/**
 * Issue listing for machine-to-machine (client credentials) callers.
 *
 * Returns issues for a given project, scoped to the Forge user identified
 * by `for_forge_user_id`. Protected by `client` middleware (Passport).
 */
final class SystemIssueController extends Controller
{
    public function index(Request $request, Project $project): AnonymousResourceCollection
    {
        $user = $this->resolveScopedUser($request);

        abort_if(! $user, Response::HTTP_FORBIDDEN, 'Missing or invalid for_forge_user_id.');
        abort_unless(
            Project::query()->visibleTo($user)->whereKey($project->getKey())->exists(),
            Response::HTTP_NOT_FOUND
        );

        $q = (string) $request->string('q');
        $status = (string) $request->string('status');
        $perPage = min((int) $request->integer('per_page', 25), 100);

        $query = $project->issues()
            ->with(['status', 'priority', 'type', 'assignee'])
            ->when($q !== '', fn ($qb) => $qb->where('summary', 'like', "%{$q}%"))
            ->when($status === 'open', fn ($qb) => $qb->whereHas('status', fn ($sq) => $sq->where('is_done', false)))
            ->when($status === 'closed', fn ($qb) => $qb->whereHas('status', fn ($sq) => $sq->where('is_done', true)))
            ->orderByDesc('created_at');

        return IssueResource::collection($query->paginate($perPage));
    }

    private function resolveScopedUser(Request $request): ?User
    {
        $forUserId = $request->string('for_forge_user_id')->toString();

        if ($forUserId === '') {
            $forUserId = $request->string('for_user')->toString();
        }

        if ($forUserId === '') {
            return null;
        }

        return User::find($forUserId);
    }
}
