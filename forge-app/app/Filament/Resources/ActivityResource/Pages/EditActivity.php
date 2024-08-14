<?php

namespace App\Filament\Resources\ActivityResource\Pages;

use App\Filament\Resources\ActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Class EditActivity
 *
 * This class represents an edit activity page.
 */
class EditActivity extends EditRecord
{
    protected static string $resource = ActivityResource::class;

    /**
     * Returns an array of header actions for the EditActivity page.
     *
     * @return array The array of header actions.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
