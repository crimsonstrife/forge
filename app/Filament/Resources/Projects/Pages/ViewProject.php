<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Jobs\ImportExport\ExportProjectJob;
use App\Models\ImportExportRecord;
use Filament\Actions;
use Filament\Forms;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Export Project')
                ->schema([
                    Forms\Components\Toggle::make('include_attachments')->default(false),
                    Forms\Components\Toggle::make('scrub_pii')->default(false),
                    Forms\Components\Select::make('issue_state')
                        ->options(['all' => 'All', 'open' => 'Open only', 'done' => 'Done only'])
                        ->default('all'),
                ])
                ->action(function (array $data): void {
                    $record = ImportExportRecord::query()->create([
                        'project_id' => $this->record->id,
                        'direction'  => 'export',
                        'status'     => 'queued',
                        'options'    => $data,
                    ]);

                    ExportProjectJob::dispatch($this->record->id, $data, $record->id);
                    Notification::make()
                        ->title('Export queued')
                        ->success()
                        ->send();
                }),
            EditAction::make(),
        ];
    }
}
