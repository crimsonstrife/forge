<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImportExportRecord;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportsController
{
    public function download(int $recordId): StreamedResponse
    {
        $record = ImportExportRecord::query()->findOrFail($recordId);
        Gate::authorize('view', $record->project ?? $record);

        abort_unless($record->direction === 'export' && $record->status === 'success', 404);

        return Storage::disk('local')->download($record->file_path);
    }
}
