<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * Class CreateRole
 *
 * This class extends the CreateRecord class and is used to handle the creation of roles within the application.
 * It is part of the Filament resource pages for managing roles.
 */
class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;
}
