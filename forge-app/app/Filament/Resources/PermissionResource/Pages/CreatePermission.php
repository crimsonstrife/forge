<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Resources\PermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * Class CreatePermission
 *
 * This class extends the CreateRecord class and is responsible for handling the creation of permissions
 * within the Filament resource management system.
 *
 * @package App\Filament\Resources\PermissionResource\Pages
 */
class CreatePermission extends CreateRecord
{
    protected static string $resource = PermissionResource::class;
}
