<?php

namespace App\Filament\Resources\CalendarResource\Pages;

use App\Filament\Resources\CalendarResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

/**
 * Class ViewCalendar
 *
 * This class represents a page for viewing calendars.
 * It extends the ViewRecord class and is part of the CalendarResource.
 */
class ViewCalendar extends ViewRecord
{
    protected static string $resource = CalendarResource::class;

    /**
     * Retrieves the actions available for the ViewCalendars page.
     *
     * @return array The actions available for the ViewCalendars page.
     */
    protected function getActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
