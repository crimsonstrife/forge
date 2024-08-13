<?php

namespace App\Http\Controllers\RoadMap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use App\Models\Project;
use App\Models\Epic;
use App\Models\Issue;
use App\Models\Story;

class DataController extends Controller
{
    /**
     * Get project epics data
     *
     * @param Project $project
     * @return JsonResponse
     */
    public function data(Project $project): JsonResponse
    {
        // Query the project
        $project = Project::where(function ($query) {
            return $query->where('owner_id', auth()->user()->id)
                ->orWhereHas('users', function ($query) {
                    return $query->where('users.id', auth()->user()->id);
                });
        })->where('id', $project->id)->first();
        if (!$project) {
            return response()->json([]);
        }
        $epics = Epic::where('project_id', $project->id)->get();
        return response()->json($this->formatResponse($epics, $project));
    }

    /**
     * Format epics to JSON data
     *
     * @param Collection $epics
     * @return Collection
     */
    private function formatResponse(Collection $epics, Project $project): Collection
    {
        $results = collect();
        foreach ($epics->sortBy('id') as $epic) {
            $results->push(collect($this->epicObj($epic)));
            foreach ($epic->issues as $issue) {
                $results->push(collect($this->issueObj($epic, $issue)));
            }
        }

        // Get all issues
        $issues = Issue::where('project_id', $project->id)->whereNull('epic_id')->orderBy('epic_id')->orderBy('id')->get();

        foreach ($issues as $issue) {
            $results->push(collect($this->issueObj(null, $issue)));
        }
        return $results;
    }

    /**
     * Format Epic object
     *
     * @param Epic $epic
     * @return array
     */
    private function epicObj(Epic $epic)
    {
        return [
            "pID" => $epic->id,
            "pName" => $epic->name,
            "pStart" => $epic->starts_at->format('Y-m-d'),
            "pEnd" => $epic->ends_at->format('Y-m-d') . " 23:59:59",
            "pClass" => "g-custom-issue",
            "pLink" => "",
            "pMile" => 0,
            "pRes" => "",
            "pComp" => "",
            "pGroup" => 1,
            "pParent" => 0,
            "pOpen" => 1,
            "pDepend" => $epic->parent_id ?? "",
            "pCaption" => "",
            "pNotes" => "",
            "pBarText" => "",
            "meta" => [
                "id" => $epic->id,
                "epic" => true,
                "parent" => null,
                "slug" => null
            ]
        ];
    }
}
