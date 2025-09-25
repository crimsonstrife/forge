<?php

namespace App\Filament\Resources\ImportExportRecords\Pages;

use App\Filament\Resources\ImportExportRecords\ImportExportRecordResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListImportExportRecords extends ListRecords
{
    protected static string $resource = ImportExportRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
