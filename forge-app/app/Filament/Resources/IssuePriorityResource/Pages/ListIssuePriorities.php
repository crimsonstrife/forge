<?php

namespace App\Filament\Resources\IssuePriorityResource\Pages;

use App\Filament\Resources\IssuePriorityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Class ListIssuePriorities
 *
 * This class represents a list issue priorities page.
 */
class ListIssuePriorities extends ListRecords
{
    protected static string $resource = IssuePriorityResource::class;

    /**
     * Retrieves the header actions for the ListIssuePriorities page.
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
