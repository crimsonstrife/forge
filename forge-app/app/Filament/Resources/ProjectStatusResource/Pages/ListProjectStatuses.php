<?php

namespace App\Filament\Resources\ProjectStatusResource\Pages;

use App\Filament\Resources\ProjectStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Class ListProjectStatuses
 *
 * This class extends the ListRecords class and is used to list project statuses.
 * It is part of the Filament resource pages for the ProjectStatusResource.
 *
 * @package App\Filament\Resources\ProjectStatusResource\Pages
 */
class ListProjectStatuses extends ListRecords
{
    protected static string $resource = ProjectStatusResource::class;

    /**
     * Get the header actions for the project statuses list page.
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
