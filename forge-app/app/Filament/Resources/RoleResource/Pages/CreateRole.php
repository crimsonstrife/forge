<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * Class CreateRole
 *
 * This class represents a create role page.
 */
class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;
}
