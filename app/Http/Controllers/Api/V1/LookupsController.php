<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\{IssueStatus, IssueType, IssuePriority};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class LookupsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        // require read ability
        abort_if(! $request->user()->tokenCan('issues:read'), 403, 'Token missing ability: issues:read');

        return response()->json([
            'statuses'  => IssueStatus::query()->get(['id','name','is_done']),
            'types'     => IssueType::query()->get(['id','key','name']),
            'priorities' => IssuePriority::query()->get(['id','name']),
        ]);
    }
}
