<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Project listing for machine-to-machine (client credentials) callers.
 *
 * Unlike the user-facing ProjectController, this endpoint is not scoped to a
 * specific user's project membership — it returns all projects so that external
 * systems (e.g. Codex) can display a full project selector without needing to
 * impersonate a particular user.
 *
 * Protected by the `client:projects:read` middleware (Passport client credentials).
 */
final class SystemProjectController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $q = (string) $request->string('q');

        $projects = Project::query()
            ->when($q !== '', fn ($qrb) => $qrb->where('name', 'like', "%{$q}%"))
            ->latest('id')
            ->paginate(50);

        return ProjectResource::collection($projects);
    }

    public function show(Project $project): ProjectResource
    {
        return ProjectResource::make($project);
    }
}
