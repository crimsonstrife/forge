<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Class EditRole
 *
 * This class extends the EditRecord class and is used to handle the editing of roles
 * within the Filament resource management system.
 *
 * @package App\Filament\Resources\RoleResource\Pages
 */
class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    /**
     * Get the header actions for the EditRole page.
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
