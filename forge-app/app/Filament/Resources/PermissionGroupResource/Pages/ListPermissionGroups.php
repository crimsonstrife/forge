<?php

namespace App\Filament\Resources\PermissionGroupResource\Pages;

use App\Filament\Resources\PermissionGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Class ListPermissionGroups
 *
 * This class extends the ListRecords class and is used to manage the listing of permission groups
 * within the Filament resource.
 *
 * @package App\Filament\Resources\PermissionGroupResource\Pages
 */
class ListPermissionGroups extends ListRecords
{
    protected static string $resource = PermissionGroupResource::class;

    /**
     * Retrieves the header actions for the permission groups list page.
     *
     * @return array An array of header actions.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
