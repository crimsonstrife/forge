<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\ProjectView;
use App\Models\Projects\Project;

class TrackProjectView
{
    public function handle(Request $request, Closure $next)
    {
        $projectId = $request->route('id'); // Assuming the project ID is passed as a route parameter

        if ($projectId && Auth::check()) {
            $project = Project::find($projectId);

            if ($project) {
                // Track the view
                ProjectView::updateOrCreate(
                    ['user_id' => Auth::id(), 'project_id' => $projectId],
                    ['updated_at' => now()]
                );

                // Increment global view count
                $project->increment('view_count');
            }
        }

        return $next($request);
    }
}
