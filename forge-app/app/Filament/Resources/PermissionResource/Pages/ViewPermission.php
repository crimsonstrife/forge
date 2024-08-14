<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Resources\PermissionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPermission extends ViewRecord
{
    protected static string $resource = PermissionResource::class;

    protected function getActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
