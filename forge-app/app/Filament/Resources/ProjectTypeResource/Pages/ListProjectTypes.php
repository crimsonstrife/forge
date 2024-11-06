<?php

namespace App\Filament\Resources\ProjectTypeResource\Pages;

use App\Filament\Resources\ProjectTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Class ListProjectTypes
 *
 * This class extends the ListRecords class to provide functionality for listing project types.
 * It is part of the Filament resource pages for managing project types.
 *
 * @package App\Filament\Resources\ProjectTypeResource\Pages
 */
class ListProjectTypes extends ListRecords
{
    protected static string $resource = ProjectTypeResource::class;

    /**
     * Get the header actions for the ProjectTypeResource.
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
