<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Resources\PermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Class ListPermissions
 *
 * This class represents a list permissions page.
 */
class ListPermissions extends ListRecords
{
    protected static string $resource = PermissionResource::class;

    /**
     * Returns an array of header actions for the ListPermissions page.
     *
     * @return array The array of header actions.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
