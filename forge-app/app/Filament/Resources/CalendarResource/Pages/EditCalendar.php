<?php

namespace App\Filament\Resources\CalendarResource\Pages;

use App\Filament\Resources\CalendarResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Class EditCalendar
 *
 * This class represents an edit calendar page.
 */
class EditCalendar extends EditRecord
{
    protected static string $resource = CalendarResource::class;

    /**
     * Returns an array of header actions for the EditCalendar page.
     *
     * @return array The array of header actions.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
