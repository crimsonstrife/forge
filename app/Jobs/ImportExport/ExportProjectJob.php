<?php

namespace App\Jobs\ImportExport;

use App\Models\ImportExportRecord;
use App\Models\Project;
use App\Services\ImportExport\ProjectExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use JsonException;

final class ExportProjectJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public string $projectId,
        /** @var array<string,mixed> */
        public array $options,
        public int $recordId,
    ) {}

    /**
     * @throws JsonException
     */
    public function handle(ProjectExportService $service): void
    {
        $record  = ImportExportRecord::query()->findOrFail($this->recordId);
        $record->update(['status' => 'running']);

        $project = Project::query()->findOrFail($this->projectId);
        $path = $service->export($project, $this->options);

        $record->update([
            'status' => 'success',
            'file_path' => str_replace(storage_path('app/'), '', $path),
        ]);
    }
}
