<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Projects\ProjectRepository;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class RepositoryController
 *
 * This class is responsible for handling repository-related operations.
 */
class RepositoryController extends Controller
{
    /**
     * Retrieve all project repositories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(ProjectRepository::all());
    }

    /**
     * Validates the request data and creates a new repository.
     *
     * @param Illuminate\Http\Request $request The request object.
     * @return Illuminate\Http\JsonResponse The JSON response containing the created repository or an error message.
     */
    public function store(Request $request)
    {
        $request->validate([
            'remote_id' => 'required|bigInteger|unique:repository',
            'name' => 'required|string|max:255|unique:repository',
            'http_url' => 'required|url',
            'slug' => 'required|string|unique:repository',
            'ssh_url' => 'nullable|url',
            'main_branch' => 'nullable|string',
            'scm_type' => 'required|string|in:git,svn,p4',
            'description' => 'nullable|longText',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $repository = ProjectRepository::create($request->all());

        if ($repository->verifyAndFetchMetadata()) {
            // Log the result

            // Return the response
            return response()->json($repository, 201);
        } else {
            $repository->delete();
            return response()->json(['error' => 'Repository verification failed or Crucible connection is disabled'], 400);
        }
    }

    /**
     * Show the specified repository.
     *
     * @param int $id The ID of the repository.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the repository data.
     */
    public function show($id)
    {
        $repository = ProjectRepository::findOrFail($id);
        return response()->json($repository);
    }

    /**
     * Update a repository.
     *
     * @param \Illuminate\Http\Request $request The request object.
     * @param int $id The ID of the repository.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the updated repository.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'remote_id' => 'sometimes|required|bigInteger|unique:repository',
            'name' => 'sometimes|required|string|max:255|unique:repository',
            'http_url' => 'sometimes|required|url',
            'slug' => 'sometimes|required|string|unique:repository',
            'ssh_url' => 'sometimes|nullable|url',
            'main_branch' => 'sometimes|nullable|string',
            'scm_type' => 'sometimes|required|string|in:git,svn,p4',
            'description' => 'nullable|longText',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $repository = ProjectRepository::findOrFail($id);
        $repository->update($request->all());

        return response()->json($repository);
    }

    /**
     * Delete a repository.
     *
     * @param int $id The ID of the repository to be deleted.
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the success of the deletion.
     */
    public function destroy($id)
    {
        $repository = ProjectRepository::findOrFail($id);
        $repository->delete();

        return response()->json(['message' => 'Repository removed successfully, your data still exists on the remote server']);
    }

    /**
     * Update the metadata of a repository.
     *
     * @param int $id The ID of the repository.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the updated repository.
     */
    public function updateMetadata($id)
    {
        $repository = ProjectRepository::findOrFail($id);
        $repository->updateMetadata();

        return response()->json($repository);
    }

    /**
     * Process metadata updates when triggered by a webhook from Crucible.
     * @param Request $request
     * @return
     */
    public function handleCrucibleWebhook(Request $request)
    {
        $event = $request->header('X-Crucible-Event');
        $payload = $request->all();

        if ($event == 'repository.updated') {
            $repository = ProjectRepository::where('url', $payload['url'])->first();

            if ($repository) {
                $repository->updateMetadata();
                return response()->json(['message' => 'Repository metadata updated successfully']);
            }
        }

        return response()->json(['message' => 'Event not handled'], 400);
    }
}
