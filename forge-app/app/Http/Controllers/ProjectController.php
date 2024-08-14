<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Http\Controllers\Controller;

/**
 * Class ProjectController
 *
 * The ProjectController class is responsible for handling HTTP requests related to projects.
 */
class ProjectController extends Controller
{

    /**
     * Retrieve all projects.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(Project::all());
    }

    /**
     * Store a newly created project.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:projects',
            'description' => 'nullable|longText',
            'owner_id' => 'required|exists:users,id',
            'status_id' => 'required|exists:project_statuses,id',
            'type_id' => 'required|exists:project_types,id',
        ]);

        $project = Project::create($request->only(['name', 'description', 'owner_id', 'status_id', 'type_id']));

        return response()->json($project, 201);
    }

    /**
     * Show the details of a project.
     *
     * @param int $id The ID of the project.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the project details.
     */
    public function show($id)
    {
        //get the project
        $project = Project::findOrFail($id);

        //get the details of the project
        $project->load('owner', 'status', 'type', 'repositories');

        return response()->json($project);
    }

    /**
     * Update a project.
     *
     * @param \Illuminate\Http\Request $request The request object.
     * @param int $id The ID of the project to update.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the updated project.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'string|max:255|unique:projects',
            'description' => 'nullable|longText',
            'owner_id' => 'required|exists:users,id',
            'status_id' => 'required|exists:project_statuses,id',
            'type_id' => 'required|exists:project_types,id',
        ]);

        $project = Project::findOrFail($id);
        $project->update($request->only(['name', 'description', 'owner_id', 'status_id', 'type_id']));

        return response()->json($project);
    }

    /**
     * Delete a project.
     *
     * @param int $id The ID of the project to be deleted.
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the success of the deletion.
     */
    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return response()->json(['message' => 'Project deleted successfully']);
    }
}
