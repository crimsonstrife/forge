<?php

namespace App\Jobs\ImportExport;

use App\Models\ImportExportRecord;
use App\Models\Project;
use App\Services\ImportExport\ProjectExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Str;
use JsonException;

final class ExportProjectJob implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    public function __construct(
        public string $projectId,
        /** @var array<string,mixed> */
        public array $options,
        public int $recordId,
    ) {
    }

    /**
     * @throws JsonException
     */
    public function handle(ProjectExportService $service): void
    {
        $record  = ImportExportRecord::query()->findOrFail($this->recordId);
        $record->update(['status' => 'running']);

        $project = Project::query()->findOrFail($this->projectId);
        $path = $service->export($project, $this->options); // absolute path

        $relative = Str::of($path)
            ->after(storage_path('app'))   // drop the "…/storage/app"
            ->replace('\\', '/')           // normalize slashes
            ->ltrim('/');                  // remove leading /

        $record->update([
            'status'    => 'success',
            'file_path' => (string) $relative,   // e.g. "exports/project-...forgepkg"
        ]);
    }
}
