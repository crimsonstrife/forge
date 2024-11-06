<?php

namespace App\Filament\Resources\PermissionGroupResource\Pages;

use App\Filament\Resources\PermissionGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * Class CreatePermissionGroup
 *
 * This class handles the creation of permission groups within the application.
 * It extends the CreateRecord class provided by the Filament framework.
 *
 * @package App\Filament\Resources\PermissionGroupResource\Pages
 */
class CreatePermissionGroup extends CreateRecord
{
    protected static string $resource = PermissionGroupResource::class;
}
