<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImportExportRecord;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportsController extends Controller
{
    public function download(Request $request, ImportExportRecord $record): BinaryFileResponse|StreamedResponse|Response
    {
        // Guard: only downloadable when finished and file path present
        abort_unless(
            $record->direction === 'export'
            && $record->status === 'success'
            && filled($record->file_path),
            404
        );

        // Authorize
        if ($record->project) {
            $this->authorize('view', $record->project);
        } else {
            $this->authorize('viewAny', ImportExportRecord::class);
        }

        $disk = Storage::disk('local');

        // Normalize relative path (“exports/...”), tolerate slashes
        $relative = ltrim(str_replace('\\', '/', (string) $record->file_path), '/');
        $downloadName = basename($relative);

        // 1) Try as relative on the "local" disk (storage/app)
        if ($disk->exists($relative)) {
            return $disk->download($relative, $downloadName, [
                'Content-Type' => 'application/zip',
            ]);
        }

        // 2) Try absolute path under storage/app (in case something wrote absolute to DB or mismatched separators)
        $abs = storage_path('app/'.$relative);
        if (is_file($abs)) {
            return response()->download($abs, $downloadName, [
                'Content-Type' => 'application/zip',
            ]);
        }

        // 3) Final fallback: if DB contains an absolute path already, stream it
        if (is_file($record->file_path)) {
            return response()->download($record->file_path, $downloadName, [
                'Content-Type' => 'application/zip',
            ]);
        }

        abort(404);
    }
}
