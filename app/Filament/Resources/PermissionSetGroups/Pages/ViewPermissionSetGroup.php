<?php

namespace App\Filament\Resources\PermissionSetGroups\Pages;

use App\Filament\Resources\PermissionSetGroups\PermissionSetGroupResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPermissionSetGroup extends ViewRecord
{
    protected static string $resource = PermissionSetGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
