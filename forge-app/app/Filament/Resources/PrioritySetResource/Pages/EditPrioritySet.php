<?php

namespace App\Filament\Resources\PrioritySetResource\Pages;

use App\Filament\Resources\PrioritySetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Class EditPrioritySet extends the EditRecord class to handle operations
 * specific to editing priority sets.
 *
 * This class utilizes the PrioritySetResource for interacting with
 * priority set data and provides methods for managing header actions.
 */
class EditPrioritySet extends EditRecord
{
    protected static string $resource = PrioritySetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
