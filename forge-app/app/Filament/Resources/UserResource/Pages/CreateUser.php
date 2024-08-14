<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * Class CreateUser
 *
 * This class represents a create user page.
 */
class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
