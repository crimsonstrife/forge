<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Resources\PermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Class EditPermission
 *
 * This class represents an edit permission page.
 */
class EditPermission extends EditRecord
{
    protected static string $resource = PermissionResource::class;

    /**
     * Retrieve the header actions for the EditPermission page.
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
