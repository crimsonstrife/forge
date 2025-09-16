<?php

namespace App\Filament\Resources\PermissionSets\Pages;

use App\Filament\Resources\PermissionSets\PermissionSetResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePermissionSet extends CreateRecord
{
    protected static string $resource = PermissionSetResource::class;
}
