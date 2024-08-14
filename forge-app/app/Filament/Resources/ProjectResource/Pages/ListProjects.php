<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Class ListProjects
 *
 * This class represents a list projects page.
 */
class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    /**
     * Returns an array of header actions for the ListProjects page.
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
