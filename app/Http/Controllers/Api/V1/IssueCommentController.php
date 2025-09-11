<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreIssueCommentRequest;
use App\Http\Controllers\Api\Concerns\InteractsWithTokenAbilities;

final class IssueCommentController extends Controller
{
    use InteractsWithTokenAbilities;

    public function store(StoreIssueCommentRequest $request, Issue $issue): JsonResponse
    {
        $this->requireAbility($request, 'comments:write');
        $this->authorize('comment', $issue);

        $issue->comments()->create([
            'user_id' => $request->user()->getKey(),
            'body'    => $request->validated('body'),
        ]);

        return response()->json(['data' => true], 201);
    }
}
