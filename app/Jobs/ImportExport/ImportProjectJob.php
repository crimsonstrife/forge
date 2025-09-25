<?php

namespace App\Jobs\ImportExport;

use App\Models\ImportExportRecord;
use App\Services\ImportExport\ProjectImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Throwable;

final class ImportProjectJob implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    public function __construct(
        public string $absolutePath,
        public int $recordId,
    ) {
    }

    /**
     * @throws Throwable
     */
    public function handle(ProjectImportService $service): void
    {
        $record = ImportExportRecord::query()->findOrFail($this->recordId);
        $record->update(['status' => 'running']);

        try {
            $projectId = $service->import($this->absolutePath, $record);

            $record->update([
                'project_id' => $projectId,
                'status'     => 'success',
            ]);
        } catch (Throwable $e) {
            $record->update([
                'status' => 'failed',
                'report' => ['error' => $e->getMessage()],
            ]);
            throw $e;
        }
    }
}
