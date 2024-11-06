<?php

namespace App\Filament\Resources\IssuePriorityResource\Pages;

use App\Filament\Resources\IssuePriorityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Class ListIssuePriorities
 *
 * This class extends the ListRecords class and is used to manage the listing of issue priorities
 * within the Filament resource pages.
 *
 * @package App\Filament\Resources\IssuePriorityResource\Pages
 */
class ListIssuePriorities extends ListRecords
{
    protected static string $resource = IssuePriorityResource::class;

    /**
     * Get the header actions for the issue priorities list page.
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
