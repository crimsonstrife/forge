<?php

namespace App\Filament\Resources\ImportExportRecords\Pages;

use App\Filament\Resources\ImportExportRecords\ImportExportRecordResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditImportExportRecord extends EditRecord
{
    protected static string $resource = ImportExportRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
