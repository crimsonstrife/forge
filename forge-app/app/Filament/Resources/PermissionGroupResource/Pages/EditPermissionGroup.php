<?php

namespace App\Filament\Resources\PermissionGroupResource\Pages;

use App\Filament\Resources\PermissionGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Class EditPermissionGroup
 *
 * This class extends the EditRecord class and is used to handle the editing of permission groups
 * within the Filament resource management system.
 *
 * @package App\Filament\Resources\PermissionGroupResource\Pages
 */
class EditPermissionGroup extends EditRecord
{
    protected static string $resource = PermissionGroupResource::class;

    /**
     * Get the header actions for the EditPermissionGroup page.
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
