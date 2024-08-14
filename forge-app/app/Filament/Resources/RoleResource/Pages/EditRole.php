<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Class EditRole
 *
 * This class represents an edit role page.
 */
class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    /**
     * Retrieves the header actions for the EditRole page.
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
