<?php

namespace App\Http\Controllers;

use App\Models\Projects\Project;
use Illuminate\Http\Request;

/**
 * Class FavoriteProjectController
 *
 * This controller handles the functionality related to favorite projects.
 * It extends the base Controller class provided by the framework.
 *
 * @package App\Http\Controllers
 */
class FavoriteProjectController extends Controller
{
    /**
     * Toggle the favorite status of a project for the authenticated user.
     *
     * @param \Illuminate\Http\Request $request The incoming request instance.
     * @param \App\Models\Project $project The project instance to toggle favorite status.
     * @return \Illuminate\Http\JsonResponse The response indicating the new favorite status.
     */
    public function toggleFavorite(Request $request, Project $project)
    {
        $user = $request->user();

        // Check if the user has already favorited the project.
        if ($user->favoriteProjects()->where('project_id', $project->id)->exists()) {
            $user->favoriteProjects()->detach($project->id); // Un-favorite the project.
            return response()->json(['message' => 'Project un-favorited']);
        } else {
            $user->favoriteProjects()->attach($project->id); // Favorite the project.
            return response()->json(['message' => 'Project favorited']);
        }
    }
}
