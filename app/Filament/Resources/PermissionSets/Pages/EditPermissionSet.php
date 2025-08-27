<?php

namespace App\Filament\Resources\PermissionSets\Pages;

use App\Filament\Resources\PermissionSets\PermissionSetResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPermissionSet extends EditRecord
{
    protected static string $resource = PermissionSetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
