<?php

namespace App\Filament\Resources\ImportExportRecords\Pages;

use App\Filament\Resources\ImportExportRecords\ImportExportRecordResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewImportExportRecord extends ViewRecord
{
    protected static string $resource = ImportExportRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
