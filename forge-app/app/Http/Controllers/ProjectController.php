<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

//use App\Models\ProjectRepository as Repository;

class ProjectController extends Controller
{
    // Get list of all projects
    public function index()
    {
        return response()->json(Project::all());
    }

    // Create a new project
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

    // Get details of a specific project
    public function show($id)
    {
        //get the project
        $project = Project::findOrFail($id);

        //get the details of the project
        $project->load('owner', 'status', 'type', 'repositories');

        return response()->json($project);
    }

    // Update a specific project
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

    // Delete a specific project
    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return response()->json(['message' => 'Project deleted successfully']);
    }
}
