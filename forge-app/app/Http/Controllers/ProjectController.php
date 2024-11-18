<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Projects\Project;
use App\Models\Projects\ProjectType;
use App\Models\Projects\ProjectStatus;
use App\Models\ProjectView;
use App\Http\Controllers\Controller;
use App\Services\CrucibleService;
use Illuminate\Support\Facades\Auth;
use Xetaio\Mentions\Parser\MentionParser;

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
        // Retrieve all projects for the authenticated user
        $projects = auth()->user()->projects;

        return view('projects.index', compact('projects'));
    }


    /**
     * Display the form for creating a new project.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('projects.create', [
            'projectTypes' => ProjectType::all(),
            'projectStatuses' => ProjectStatus::all(),
        ]);
    }

    /**
     * Store a newly created project.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type_id' => 'required|exists:project_types,id',
            'status_id' => 'required|exists:project_statuses,id',
            'repository_url' => 'nullable|url',
        ]);

        $project = Project::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'type_id' => $validated['type_id'],
            'status_id' => $validated['status_id'],
            'owner_id' => Auth::id(),
        ]);

        // Handle repository syncing if a URL is provided
        if (!empty($validated['repository_url'])) {
            $success = $project->syncRepository($validated['repository_url']);
            if (!$success) {
                return redirect()->route('projects.create')->withErrors('Failed to sync repository. Check the URL or Crucible settings.');
            }
        }

        return redirect()->route('dashboard')->with('success', 'Project created successfully!');
    }

    /**
     * Show the details of a project.
     *
     * @param int $id The ID of the project.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the project details.
     */
    public function show($id)
    {
        $project = Project::findOrFail($id);

        // Ensure the user has access to this project
        if (!$project->users->contains(auth()->user()) && $project->owner_id !== auth()->id()) {
            abort(403, 'You do not have access to this project.');
        }

        // Track the view
        if (Auth::check()) {
            ProjectView::updateOrCreate(
                ['user_id' => Auth::id(), 'project_id' => $project->id],
                ['updated_at' => now()]
            );
        }

        return view('projects.show', compact('project'));
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
