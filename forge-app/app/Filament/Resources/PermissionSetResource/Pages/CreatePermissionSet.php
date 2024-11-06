<?php

namespace App\Filament\Resources\PermissionSetResource\Pages;

use App\Filament\Resources\PermissionSetResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * Class CreatePermissionSet
 *
 * This class extends the CreateRecord class and is used to handle the creation of permission sets
 * within the Filament resource management system.
 *
 * @package App\Filament\Resources\PermissionSetResource\Pages
 */
class CreatePermissionSet extends CreateRecord
{
    protected static string $resource = PermissionSetResource::class;
}
