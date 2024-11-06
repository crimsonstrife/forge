<?php

namespace App\Filament\Resources\PrioritySetResource\Pages;

use App\Filament\Resources\PrioritySetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Class EditPrioritySet
 *
 * This class extends the EditRecord class and is used for editing priority sets
 * within the Filament resource management system.
 *
 * @package App\Filament\Resources\PrioritySetResource\Pages
 */
class EditPrioritySet extends EditRecord
{
    protected static string $resource = PrioritySetResource::class;

    /**
     * Retrieves the header actions for the EditPrioritySet page.
     *
     * @return array An array of header actions.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
