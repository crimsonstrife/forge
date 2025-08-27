<?php

namespace App\Filament\Resources\PermissionSets\Pages;

use App\Filament\Resources\PermissionSets\PermissionSetResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPermissionSet extends ViewRecord
{
    protected static string $resource = PermissionSetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
