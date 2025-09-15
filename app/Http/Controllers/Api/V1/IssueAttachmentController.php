<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Issue;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Api\Concerns\InteractsWithTokenAbilities;

final class IssueAttachmentController extends Controller
{
    use InteractsWithTokenAbilities;

    public function store(Request $request, Issue $issue): JsonResponse
    {
        $this->requireAbility($request, 'attachments:write');
        $this->authorize('update', $issue);

        $request->validate([
            'file' => ['required', 'file', 'max:10240'],
        ]);

        $media = $issue->addMediaFromRequest('file')->toMediaCollection('attachments');

        return response()->json([
            'data' => [
                'id' => (string) $media->id,
                'file_name' => $media->file_name,
                'size' => $media->size,
                'mime_type' => $media->mime_type,
            ],
        ], Response::HTTP_CREATED);
    }
}
