<?php

namespace App\Filament\Resources\PermissionSetResource\Pages;

use App\Filament\Resources\PermissionSetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Class EditPermissionSet
 *
 * This class extends the EditRecord functionality to provide
 * specific editing capabilities for Permission Sets within the
 * Filament resource management system.
 *
 * @package App\Filament\Resources\PermissionSetResource\Pages
 */
class EditPermissionSet extends EditRecord
{
    protected static string $resource = PermissionSetResource::class;

    /**
     * Get the header actions for the permission set resource.
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
