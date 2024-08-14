<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Resources\PermissionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;


/**
 * Class ViewPermission
 *
 * This class represents a view permission page for a specific record.
 */
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
