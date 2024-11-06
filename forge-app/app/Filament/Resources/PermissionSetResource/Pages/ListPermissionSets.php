<?php

namespace App\Filament\Resources\PermissionSetResource\Pages;

use App\Filament\Resources\PermissionSetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Class ListPermissionSets
 *
 * This class extends the ListRecords class and is used to manage the listing of permission sets
 * within the Filament resource.
 *
 * @package App\Filament\Resources\PermissionSetResource\Pages
 */
class ListPermissionSets extends ListRecords
{
    protected static string $resource = PermissionSetResource::class;

    /**
     * Get the header actions for the PermissionSetResource list page.
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
