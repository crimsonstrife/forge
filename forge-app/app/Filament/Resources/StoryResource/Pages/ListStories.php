<?php

namespace App\Filament\Resources\StoryResource\Pages;

use App\Filament\Resources\StoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Class ListStories
 *
 * This class represents a list stories page.
 */
class ListStories extends ListRecords
{
    protected static string $resource = StoryResource::class;

    /**
     * Retrieve the header actions for the ListStories page.
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
