<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Class ListUsers
 *
 * This class represents a list users page.
 */
class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    /**
     * Get the header actions for the ListUsers page.
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
