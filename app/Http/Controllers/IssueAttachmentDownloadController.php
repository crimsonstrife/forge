<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class IssueAttachmentDownloadController extends Controller
{
    public function __invoke(Request $request, string $project, Issue $issue, Media $media): StreamedResponse
    {
        $this->authorize('view', $issue);

        abort_unless(
            $media->model_type === Issue::class
            && $media->model_id === $issue->getKey()
            && $media->collection_name === 'attachments',
            404
        );

        // Spatie will stream the file from private disk
        return response()->streamDownload(function () use ($media) {
            echo $media->stream();
        }, $media->file_name);
    }
}
