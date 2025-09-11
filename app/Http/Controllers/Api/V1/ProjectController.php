<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProjectResource;

final class ProjectController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Project::class);

        $q = (string) $request->string('q');
        $projects = Project::query()
            ->when($q !== '', fn ($qrb) => $qrb->where('name', 'like', "%{$q}%"))
            ->whereHas('users', fn ($qrb) => $qrb->whereKey($request->user()->getKey()))
            ->latest('id')
            ->paginate(20);

        return ProjectResource::collection($projects);
    }

    public function show(Project $project): ProjectResource
    {
        $this->authorize('view', $project);

        return ProjectResource::make($project);
    }
}
