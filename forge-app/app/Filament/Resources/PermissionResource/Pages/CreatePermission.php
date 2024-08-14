<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Resources\PermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * Class CreatePermission
 *
 * This class represents a create permission page.
 */
class CreatePermission extends CreateRecord
{
    protected static string $resource = PermissionResource::class;
}
