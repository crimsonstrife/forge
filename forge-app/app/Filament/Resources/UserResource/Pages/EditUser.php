<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Class EditUser
 *
 * This class extends the EditRecord class and is used for editing user records
 * within the Filament resource management system.
 *
 * @package App\Filament\Resources\UserResource\Pages
 */
class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    /**
     * Get the header actions for the EditUser page.
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
