<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Resources\PermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Class ListPermissions
 *
 * This class extends the ListRecords class to provide functionality
 * for listing permissions within the application.
 *
 * @package App\Filament\Resources\PermissionResource\Pages
 */
class ListPermissions extends ListRecords
{
    protected static string $resource = PermissionResource::class;

    /**
     * Get the header actions for the permissions list page.
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
