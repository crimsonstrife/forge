<?php

namespace App\Filament\Resources\ProjectStatusResource\Pages;

use App\Filament\Resources\ProjectStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Class ListProjectStatuses
 *
 * This class represents a list project statuses page.
 */
class ListProjectStatuses extends ListRecords
{
    protected static string $resource = ProjectStatusResource::class;

    /**
     * Retrieves the header actions for the ListProjectStatuses page.
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
