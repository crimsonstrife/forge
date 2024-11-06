<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Resources\PermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Class EditPermission
 *
 * This class extends the EditRecord class and is used for editing permissions
 * within the Filament resource.
 *
 * @package App\Filament\Resources\PermissionResource\Pages
 */
class EditPermission extends EditRecord
{
    protected static string $resource = PermissionResource::class;

    /**
     * Get the header actions for the EditPermission page.
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
