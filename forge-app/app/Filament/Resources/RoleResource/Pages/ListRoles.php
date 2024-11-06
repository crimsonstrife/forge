<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Class ListRoles
 *
 * This class extends the ListRecords class and is used to list roles in the application.
 * It is part of the Filament resource pages for managing roles.
 *
 * @package App\Filament\Resources\RoleResource\Pages
 */
class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    /**
     * Get the header actions for the list roles page.
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
