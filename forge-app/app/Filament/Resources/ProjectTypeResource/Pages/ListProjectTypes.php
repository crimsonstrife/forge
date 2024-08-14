<?php

namespace App\Filament\Resources\ProjectTypeResource\Pages;

use App\Filament\Resources\ProjectTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Class ListProjectTypes
 *
 * This class represents a list project types page.
 */
class ListProjectTypes extends ListRecords
{
    protected static string $resource = ProjectTypeResource::class;

    /**
     * Retrieves the header actions for the ListProjectTypes page.
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
