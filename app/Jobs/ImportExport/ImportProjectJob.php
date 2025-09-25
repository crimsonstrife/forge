<?php

namespace App\Jobs\ImportExport;

use App\Models\ImportExportRecord;
use App\Services\ImportExport\ProjectImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ImportProjectJob implements ShouldQueue
{
    use Queueable;
    use Dispatchable;

    public function __construct(
        public string $relativePath,
        public int $recordId,
        public ?string $leadUserId = null,
    ) {
    }

    /**
     * @throws Throwable
     */
    public function handle(ProjectImportService $service): void
    {
        $record = ImportExportRecord::query()->findOrFail($this->recordId);
        $record->update(['status' => 'running']);

        // Resolve absolute path from the actual disk root:
        $abs = Storage::disk('local')->path($this->relativePath);

        try {
            $projectId = $service->import($abs, $record, $this->leadUserId);
            $record->update(['project_id' => $projectId, 'status' => 'success']);
        } catch (Throwable $e) {
            $record->update(['status' => 'failed', 'report' => ['error' => $e->getMessage(), 'path' => $abs]]);
            throw $e;
        }
    }
}
