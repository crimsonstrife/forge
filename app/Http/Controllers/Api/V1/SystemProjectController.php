<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProjectResource;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

/**
 * Project listing for machine-to-machine (client credentials) callers.
 *
 * The `for_user` query parameter MUST be provided. It identifies which Forge
 * user is making the request (passed by the calling app), and results are
 * filtered to only that user's project memberships. If `for_user` is absent
 * or refers to a non-existent user, an empty collection is returned.
 *
 * Protected by the `client:projects:read` middleware (Passport client credentials).
 */
final class SystemProjectController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $this->resolveScopedUser($request);

        // Require a user identity — never return all projects blindly.
        if (! $user) {
            return ProjectResource::collection(Project::query()->whereRaw('0=1')->paginate(0));
        }

        $q = (string) $request->string('q');

        $projects = Project::query()
            ->visibleTo($user)
            ->when($q !== '', fn ($qrb) => $qrb->where('name', 'like', "%{$q}%"))
            ->latest('id')
            ->paginate(50);

        return ProjectResource::collection($projects);
    }

    public function show(Request $request, Project $project): ProjectResource
    {
        $user = $this->resolveScopedUser($request);

        abort_if(! $user, Response::HTTP_NOT_FOUND);
        abort_unless(Project::query()->visibleTo($user)->whereKey($project->getKey())->exists(), Response::HTTP_NOT_FOUND);

        return ProjectResource::make($project);
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
