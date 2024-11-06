<?php

namespace App\Filament\Resources\PrioritySetResource\Pages;

use App\Filament\Resources\PrioritySetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;


/**
 * Class ListPrioritySets
 *
 * This class extends the ListRecords class to provide functionality for listing priority sets.
 * It is part of the Filament resource pages for managing priority sets in the application.
 *
 * @package App\Filament\Resources\PrioritySetResource\Pages
 */
class ListPrioritySets extends ListRecords
{
    protected static string $resource = PrioritySetResource::class;

    /**
     * Retrieves the header actions for the PrioritySetResource.
     *
     * @return array An array of header actions.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
