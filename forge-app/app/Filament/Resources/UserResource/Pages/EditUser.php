<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Class EditUser
 *
 * This class represents an edit user page.
 */
class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    /**
     * Retrieve the header actions for the EditUser page.
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
