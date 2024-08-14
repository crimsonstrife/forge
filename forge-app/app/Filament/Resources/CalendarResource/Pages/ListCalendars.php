<?php

namespace App\Filament\Resources\CalendarResource\Pages;

use App\Filament\Resources\CalendarResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Class ListCalendars
 *
 * This class represents a list calendars page.
 */
class ListCalendars extends ListRecords
{
    protected static string $resource = CalendarResource::class;

    /**
     * Retrieves the header actions for the ListCalendars page.
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
