<?php

namespace App\Filament\Resources\PermissionSetGroups\Pages;

use App\Filament\Resources\PermissionSetGroups\PermissionSetGroupResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPermissionSetGroup extends EditRecord
{
    protected static string $resource = PermissionSetGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
