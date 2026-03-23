<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProjectResource;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
        $forUserId = $request->string('for_user')->toString();

        // Require a user identity — never return all projects blindly.
        if ($forUserId === '') {
            return ProjectResource::collection(Project::query()->whereRaw('0=1')->paginate(0));
        }

        $user = User::find($forUserId);

        // Unknown user → return nothing.
        if (! $user) {
            return ProjectResource::collection(Project::query()->whereRaw('0=1')->paginate(0));
        }

        $q = (string) $request->string('q');

        $projects = Project::query()
            ->when($q !== '', fn ($qrb) => $qrb->where('name', 'like', "%{$q}%"))
            // Only projects this specific user is a direct member of or belongs to via a team.
            ->where(function ($query) use ($user) {
                $query->whereHas('users', fn ($u) => $u->whereKey($user->getKey()))
                      ->orWhereHas('teams', fn ($t) => $t->whereHas('users', fn ($u) => $u->whereKey($user->getKey())));
            })
            ->latest('id')
            ->paginate(50);

        return ProjectResource::collection($projects);
    }

    public function show(Project $project): ProjectResource
    {
        return ProjectResource::make($project);
    }
}
