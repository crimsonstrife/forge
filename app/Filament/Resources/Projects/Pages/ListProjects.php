<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Jobs\ImportExport\ImportProjectJob;
use App\Models\ImportExportRecord;
use App\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('import')
                ->label('Import Into New Project')
                ->schema([
                    Forms\Components\Select::make('lead_id')
                        ->label('Project lead')
                        ->options(fn () => User::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->default((string) auth()->id())
                        ->required(),
                    Forms\Components\FileUpload::make('archive')
                        ->label('.forgepkg file')
                        ->rules([
                            'file',
                            'mimes:forgepkg,zip',
                            'mimetypes:application/zip,application/x-zip-compressed,application/octet-stream',
                            'max:102400', // 100 MB
                        ])
                        ->visibility('private')
                        ->directory('imports')
                        ->preserveFilenames()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    /** @var string $stored */           // e.g. "imports/project-...forgepkg"
                    $stored = $data['archive'];

                    // Keep it RELATIVE in the DB; compute absolute later via the disk.
                    $record = ImportExportRecord::query()->create([
                        'direction'  => 'import',
                        'status'     => 'queued',
                        'file_path'  => $stored,
                        'options'    => [],
                        'initiator_id'  => (string) auth()->id(),
                    ]);

                    ImportProjectJob::dispatch($stored, $record->id, (string) $data['lead_id']);
                    Notification::make()
                        ->title('Import queued')
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
