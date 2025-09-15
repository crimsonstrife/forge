<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IssueTransitionRequest;
use App\Http\Controllers\Api\Concerns\InteractsWithTokenAbilities;

final class IssueTransitionController extends Controller
{
    use InteractsWithTokenAbilities;

    public function store(IssueTransitionRequest $request, Issue $issue): JsonResponse
    {
        $this->requireAbility($request, 'issues:write');
        $this->authorize('update', $issue);

        $issue->update(['status_id' => $request->to_status_id]);

        return response()->json(['data' => true]);
    }
}
