<?php

namespace App\Filament\Resources\ActivityResource\Pages;

use App\Filament\Resources\ActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Class ListActivities
 *
 * This class represents a list activities page.
 */
class ListActivities extends ListRecords
{
    protected static string $resource = ActivityResource::class;

    /**
     * Retrieve the header actions for the ListActivities page.
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
