<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

final class IssueAttachmentController extends Controller
{
    /**
     * Stream a private attachment download.
     */
    public function download(Request $request, Project|string $project, Issue $issue, Media $media): StreamedResponse
    {
        $this->authorize('view', $issue);

        abort_unless(
            $media->model_type === $issue->getMorphClass()
            && (int) $media->model_id === (int) $issue->getKey()
            && $media->collection_name === 'attachments',
            404
        );

        return response()->streamDownload(function () use ($media) {
            echo $media->stream();
        }, $media->file_name);
    }

    /**
     * Delete an attachment and return the refreshed list HTML + count.
     * @throws Throwable
     */
    public function destroy(Request $request, Project|string $project, Issue $issue, Media $media): JsonResponse
    {
        $this->authorize('update', $issue);

        abort_unless(
            $media->model_type === $issue->getMorphClass()
            && (int) $media->model_id === (int) $issue->getKey()
            && $media->collection_name === 'attachments',
            404
        );

        $media->delete();

        $attachments = $issue->media()
            ->where('collection_name', 'attachments')
            ->latest()
            ->get();

        $html = view('partials.issues.attachments_list', [
            'attachments' => $attachments,
            'issue'       => $issue,
            'project'     => $project instanceof Project ? $project : $issue->project,
        ])->render();

        return response()->json([
            'html'  => $html,
            'count' => $attachments->count(),
        ]);
    }
}
