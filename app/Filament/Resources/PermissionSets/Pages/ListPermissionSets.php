<?php

namespace App\Filament\Resources\PermissionSets\Pages;

use App\Filament\Resources\PermissionSets\PermissionSetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPermissionSets extends ListRecords
{
    protected static string $resource = PermissionSetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
