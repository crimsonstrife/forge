<?php

namespace App\Filament\Resources\PermissionSetGroups\Pages;

use App\Filament\Resources\PermissionSetGroups\PermissionSetGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPermissionSetGroups extends ListRecords
{
    protected static string $resource = PermissionSetGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
